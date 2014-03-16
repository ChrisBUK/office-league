<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataFixtures.php';

    class DataSeasons extends AbstractData
    {
        /**
        * Get current season based on type. 
        * 
        * @param mixed $arrParams
        * @param mixed $strFormat
        */
        public function getCurrentSeason(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('seasonTypeId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);
            
            $strSql = "SELECT seas_current_id 
                        FROM season_type
                        WHERE seas_type_id = ?
                        ";
                        
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute(array($arrParams['seasonTypeId']));
            
            return self::formatData($objQuery->fetch(PDO::FETCH_ASSOC), $strFormat);            
        }
        
        /**
        * Close a season instance and begin a new one.
        * 
        * @param mixed $arrParams
        * @param mixed $strFormat
        */
        public function closeSeason(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);

            /*
            // PHP 5.3.3+            
            if ($this->objDb->inTransaction())
            {
                throw new ApiException("The database can't be updated right now, a transaction is in progress.", 405);
            }
            */
                            
            // Run through this seasons competitions and re-rank, assign promotion/relegations
            $strSql = "SELECT * 
                        FROM competition_team 
                        JOIN competition_type ON comp_type_id = ctm_competition    
                        WHERE ctm_season_instance = ? 
                        ORDER BY comp_rank, ctm_competition, ctm_knocked_out_round ASC, ctm_points DESC, ctm_score_diff DESC, ctm_score_for DESC, ctm_score_against ASC, ctm_won DESC, ctm_drawn DESC, ctm_lost ASC, ctm_previous_pos ASC";
                        
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute(array($arrParams['seasonId']));
            
            $arrLastSeason = $objQuery->fetchAll(PDO::FETCH_ASSOC);
            $arrNewSeason = array();
            $arrComps = array();
            
            $intCompId = 0;
            
            $this->objDb->beginTransaction();        
            
            foreach ($arrLastSeason as $arrStanding)
            {
                if ($arrStanding['ctm_competition'] <> $intCompId)
                {
                    $intPos = 0;
                    $intCompId = $arrStanding['ctm_competition'];
                    $arrComps[] = array('format'=>$arrStanding['comp_format'], 'id'=>$arrStanding['ctm_competition']);
                }
                
                $intPos++;
                
                switch ($arrStanding['comp_format'])
                {
                    case 'LEAGUE':
                        $intWinner = $intPos == 1 ? 1 : 0;
                        $intRunnerUp = $intPos == 2 ? 1 : 0;
                        $intPromoted = $intPos <= $arrStanding['comp_promo_places'] ? 1 : 0;
                        $intRelegated = $intPos > $arrStanding['comp_total_places'] - $arrStanding['comp_releg_places'] ? 1 : 0;
                        break;
                    
                    case 'KO':
                        $intWinner = empty($arrStanding['ctm_knocked_out_round']) ? 1 : 0; 
                        $intRunnerUp = $arrStanding['ctm_knocked_out_round'] == $arrStanding['comp_rounds'] ? 1 : 0;
                        $intPromoted = 0;
                        $intRelegated = 0;
                        break;
                        
                    default:
                        $intWinner = 0;
                        $intRunnerUp = 0;
                        $intPromoted = 0;
                        $intRelegated = 0;
                        break;
                }
                                
                $strSql = "UPDATE competition_team 
                            SET ctm_promoted = ?,
                                ctm_relegated = ?,
                                ctm_winners = ?,
                                ctm_runners_up = ?,
                                ctm_previous_pos = ctm_current_pos, 
                                ctm_current_pos = ? 
                            WHERE ctm_id = ?";
                
                $arrQueryParams = array($intPromoted, $intRelegated, $intWinner, $intRunnerUp, $intPos, $arrStanding['ctm_id']);
                
                $objQuery = $this->objDb->prepare($strSql);
                $objQuery->execute($arrQueryParams); 
                
                switch (true)
                {
                    case ($arrStanding['comp_format'] == 'LEAGUE' && $intPromoted):
                        $intNewCompId = $arrStanding['comp_promo_into'];
                        $intStartPos = 99;
                        break;
                    
                    case ($arrStanding['comp_format'] == 'LEAGUE' && $intRelegated):
                        $intNewCompId = $arrStanding['comp_releg_into'];                        
                        $intStartPos = 0;
                        break;
                        
                    case ($arrStanding['comp_format'] == 'KO'):
                        $intNewCompId = $arrStanding['ctm_competition'];
                        $intStartPos = 0;
                        break;

                    default:
                        $intNewCompId = $arrStanding['ctm_competition'];
                        $intStartPos = $intPos;
                        break;
                        
                }
                
                $intKey = $intNewCompId."_".$intStartPos."_".$arrStanding['ctm_team'];
                $arrNewSeason[$intKey] = array('teamId'=>$arrStanding['ctm_team'],
                                      'compId'=>$intNewCompId,
                                      'startPos'=>$intStartPos
                );
            }
            
            ksort($arrNewSeason);
            $this->objDb->commit();                    
            
            $this->objDb->beginTransaction();      
                          
            // We need to know the season type to roll over to the next season
            $strSql = "SELECT sin_season_type, sin_ended FROM season_instance WHERE sin_id = ?";
            $arrQueryParams = array($arrParams['seasonId']);
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            $arrOldSeason = $objQuery->fetch(PDO::FETCH_ASSOC);

            if (empty($arrOldSeason))
            {
                throw new ApiException("This season does not exist in the database", 405);
            }
            
            if (!empty($arrOldSeason['sin_ended']))
            {
                throw new ApiException("This season has already been closed", 405);
            }        
            
            // We need to close the existing season
            $strSql = "UPDATE season_instance SET sin_ended = NOW() WHERE sin_id = ?";
            $arrQueryParams = array($arrParams['seasonId']);
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
                        
            // Create a new season instance and know its ID for later.
            $strSql = "INSERT INTO season_instance (sin_season_type, sin_began) VALUES (?, NOW())";
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute(array($arrOldSeason['sin_season_type']));
            $intNewSeasonInstance = $this->objDb->lastInsertId();
            
            // Update the new season instance into the season type table.
            $strSql = "UPDATE season_type SET seas_current_id = ? WHERE seas_type_id = ?";
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute(array($intNewSeasonInstance, $arrOldSeason['sin_season_type']));            

            $this->objDb->commit();                    
            
            // Run through the new seasons teams and set up the new divisions.
            $this->objDb->beginTransaction();        
            foreach ($arrNewSeason as $arrStanding)
            {
                $strSql = "INSERT INTO competition_team (ctm_season_instance, ctm_competition, ctm_team, ctm_current_pos, ctm_previous_pos) VALUES (?, ?, ?, ?, ?)";
                $objQuery = $this->objDb->prepare($strSql);
                $objQuery->execute(array($intNewSeasonInstance, $arrStanding['compId'], $arrStanding['teamId'], $arrStanding['startPos'], $arrStanding['startPos']));                            
            }
            $this->objDb->commit(); 
            
            // Set up new fixtures 
            foreach ($arrComps as $arrComp)
            {
                switch ($arrComp['format'])
                {
                    case 'LEAGUE':
                        $objFixtures = new DataFixtures();
                        $arrFixtures = $objFixtures->createLeagueFixturesByCompetition(array('competitionId'=>$arrComp['id'], 'seasonId'=>$intNewSeasonInstance), 'array');
                        break;
                    
                    case 'KO':        
                        $ojbFixtures = new DataFixtures();
                        $arrFixtures = $objFixtures->drawSsdmCup(array('competitionId'=>$arrComp['id'], 'seasonId'=>$intNewSeasonInstance, 'roundId'=>1));                
                        break;
                }
            }
                                    
            return true;
        }
        
        public function resetSeason(array $arrParams, $strFormat='json') 
        {
            $arrRequired = array('seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);

                                              
            // We need to know the season type to roll over to the next season
            $strSql = "SELECT sin_season_type, sin_ended FROM season_instance WHERE sin_id = ?";
            $arrQueryParams = array($arrParams['seasonId']);
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            $arrOldSeason = $objQuery->fetch(PDO::FETCH_ASSOC);

            if (empty($arrOldSeason))
            {
                throw new ApiException("This season does not exist in the database", 405);
            }
            
            if (!empty($arrOldSeason['sin_ended']))
            {
                throw new ApiException("This season has already been closed", 405);
            }        
            
            // Get season type
            $strSql = "SELECT comp_format format, comp_type_id id
                        FROM season_type_competition JOIN competition_type ON comp_type_id = stc_competition WHERE stc_season_type = ?";
            $arrQueryParams = array($arrOldSeason['sin_season_type']);
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            $arrComps = $objQuery->fetchAll(PDO::FETCH_ASSOC);
                                        
            // Reset season instance team stats
            $strSql = "UPDATE competition_team SET ctm_played = 0, ctm_won = 0, ctm_drawn = 0, ctm_lost = 0, ctm_points = 0, ctm_score_for = 0, ctm_score_against = 0, ctm_score_diff = 0, ctm_previous_pos = 99, ctm_current_pos = 99, ctm_promoted = 0, ctm_relegated = 0, ctm_winners = 0, ctm_runners_up = 0, ctm_knocked_out_round = NULL WHERE ctm_season_instance = ?";
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute(array($arrParams['seasonId']));
            
            // Delete season instance fixtures - we'll need to redraw them because of cups.
            $strSql = "DELETE FROM competition_fixture WHERE fix_season = ?";
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute(array($arrParams['seasonId']));
            
            // Delete entrants into cup competitions - we'll redraw them
            $strSql = "DELETE FROM competition_team
                        WHERE (SELECT comp_format FROM competition_type WHERE comp_type_id = ctm_competition) = 'KO'
                        AND ctm_season_instance = ?
                        ";                        
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute(array($arrParams['seasonId']));
                                   
            // Set up new fixtures 
            foreach ($arrComps as $arrComp)
            {
                switch ($arrComp['format'])
                {
                    case 'LEAGUE':
                        $objFixtures = new DataFixtures();
                        $arrFixtures = $objFixtures->createLeagueFixturesByCompetition(array('competitionId'=>$arrComp['id'], 'seasonId'=>$arrParams['seasonId']), 'array');
                        break;
                    
                    case 'KO':        
                        $ojbFixtures = new DataFixtures();
                        $arrFixtures = $objFixtures->drawSsdmCupRound(array('competitionId'=>$arrComp['id'], 'seasonId'=>$arrParams['seasonId'], 'roundId'=>1));                
                        break;
                }
            }
                                    
            return true;            
        }      
        
    }
?>