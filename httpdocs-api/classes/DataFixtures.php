<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';

    class DataFixtures extends AbstractData
    {
        /** 
        * Get tables by competition, optionally filter on season/team.
        * 
        * @param mixed $arrParams
        */
        public function getFixturesByCompetitionAndSeason($strFormat='json')
        {
            $arrRequired = array('competitionId', 'seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = UrlParameters::getFullParamList($arrRequired, $arrOptional);            
            
            $strSql = "SELECT * FROM competition_fixture WHERE fix_competition = ? AND fix_season = ? AND fix_played = 0";
            $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            return self::formatData($objQuery->fetchAll(PDO::FETCH_ASSOC), $strFormat);
        }
        
        /**
        * Generate all fixtures for a competition
        */
        public function createFixturesByCompetition()
        {
            $arrRequired = array('competitionId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = UrlParameters::getFullParamList($arrRequired, $arrOptional);            
            
            // TODO: Find the last season (if exists) and see if its been completed, if so, create a new season ID, if not, fail.
            //throw new ApiException("Season already underway", 400);
            
            // Roll fixture list & Build up a round of matches for each player.
            $arrTeams = DataTeams::getTeamsInCompetition('array');
            $intRoundSize = count($arrTeams) - 1;

            $arrFixtures = array();
            
            foreach($arrTeams as $intRound => $objHomeTeam)
            {
                $arrOpponents = $arrTeams;
                unset($arrOpponents[$intRound]); //don't want to play yourself.
                    
                foreach($arrOpponents as $intOpponentRound => $objAwayTeam) {
                    $intHomeTeamId = $objHomeTeam->teamId;
                    $arrFixtures[] = array('homeTeam'=>$objHomeTeam, 'awayTeam'=>$objAwayTeam);
                }                 
                shuffle($arrFixtures);                            
            }
            
            $arrFixtureRounds = array_fill(1,$intRoundSize,array());
            $arrTeamsInRound = array_fill(1,$intRoundSize,array());
                        
            while (!empty($arrFixtures)) {
                
                $intRound = 1;
                $bolAddedFixture = false;
                $arrFixture = array_shift($arrFixtures);
                
                while (!$bolAddedFixture) 
                {         
                    if (empty($arrTeamsInRound[$intRound]) || (!in_array($arrFixture['homeTeam']->teamId, $arrTeamsInRound[$intRound]) && !in_array($arrFixture['awayTeam']->teamId, $arrTeamsInRound[$intRound])))
                    {                    
                        $arrFixtureRounds[$intRound][] = $arrFixture;                   
                        $arrTeamsInRound[$intRound][] = $arrFixture['homeTeam']->teamId;
                        $arrTeamsInRound[$intRound][] = $arrFixture['awayTeam']->teamId;
                        $bolAddedFixture = true;
                    } else {
                        $intRound ++;
                    }                    
                }                
            }
                                     
            // TODO: Insert each fixture in a single db transaction.
            $this->objDb->beginTransaction();
            foreach ($arrFixtureRounds as $intRound=>$arrRoundFixtures)
            {
                foreach ($arrRoundFixtures as $arrFixture)
                {
                    $strSql = "INSERT INTO competition_fixture (fix_season, fix_competition, fix_round, fix_home_team, fix_away_team) VALUES (?, ?, ?, ?, ?)";    
                    $arrQueryParams = array(1, $arrParams['competitionId'], $intRound, $arrFixture['homeTeam']->teamId, $arrFixture['awayTeam']->teamId);
                    $objQuery = $this->objDb->prepare($strSql);
                    $objQuery->execute($arrQueryParams);
                }
            }
            $this->objDb->commit();
                                    
            // TODO: Return the season ID
            
        }

    }
?>