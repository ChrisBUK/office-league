<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataTeams.php';

    class DataTables extends AbstractData
    {
        /** 
        * Get tables by competition, optionally filter on season/team.
        * 
        * @param mixed $arrParams
        */
        public function getTablesByCompetition(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('competitionId', 'seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);

            $strSql = "SELECT * FROM competition_table WHERE ctb_competition = ? AND ctb_season = ?";
            $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            return self::formatData($objQuery->fetchAll(PDO::FETCH_ASSOC), $strFormat);
        }
        
        /**
        * Update or create tables for a competition.
        * 
        * @param mixed $arrParams
        * @param mixed $strFormat
        */
        public function updateTablesByCompetition(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('competitionId', 'seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);
                        
            // Check if this competition uses a table (comp_format)
            $strSql = "SELECT comp_format FROM competition_type WHERE comp_type_id = ?";
            $arrQueryParams = array($arrParams['competitionId']);
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
                        
            if ($objQuery->fetch(PDO::FETCH_COLUMN) != 'LEAGUE')
            {
                throw new ApiException("This competition does not use a league table.", 405);
            }
            
            // Create a blank table if one isn't already there.            
            self::createTablesByCompetition($arrParams);
            
            // Update table from fixtures
                                                     
        }
        
        /**
        * Create a blank table for a competition and season if one doesn't exist already.
        * 
        * @param mixed $arrParams
        * @param mixed $strFormat
        */
        private function createTablesByCompetition(array $arrParams, $strFormat='json')
        {
            $strSql = "SELECT COUNT(ctb_id) FROM competition_table WHERE ctb_season_instance = ?";
            $objQuery = $this->objDb->prepare($strSql);
            $arrQueryParams = array($arrParams['seasonId']);
            $objQuery->execute($arrQueryParams);
            
            if ($objQuery->fetch(PDO::FETCH_COLUMN) > 0)
            {
                return false;
            }            
            
            $objTeams = new DataTeams();
            $arrTeams = $objTeams->getTeamsInCompetition($arrParams, 'array');
            
            if (!$this->objDb->inTransaction())            
            {
                $this->objDb->beginTransaction();
                $bolCommit = true;
            }
            
            foreach($arrTeams as $objTeam)
            {
                $strSql = "INSERT INTO competition_table (ctb_competition, ctb_season_instance, ctb_team) VALUES (?, ?, ?)";
                $objQuery = $this->objDb->prepare($strSql);
                $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId'], $objTeam->teamId);
                $objQuery->execute($arrQueryParams);
            }            
            
            if (!empty($bolCommit))
            {
                $this->objDb->commit();                
            }
            
            return true;            
        }

    }
?>
