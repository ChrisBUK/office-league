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

            $objResult = new stdClass();
            
            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);

            $strSql = "SELECT comp_type_id id,
                              comp_name name,
                              comp_rank rank,
                              comp_format format,
                              comp_total_places totalPlaces,
                              comp_promo_places promotionPlaces,
                              comp_releg_places relegationPlaces,
                              comp_promo_into promotionIntoCompetition,
                              comp_releg_into relegationIntoCompetition,
                              comp_points_win pointsForWin,
                              comp_points_draw pointsForDraw,
                              comp_points_lose pointsForLose,
                              comp_rounds numberOfRounds,
                              comp_rules rules            
                        FROM competition_type
                        WHERE comp_type_id = ?                        
                        ";
            
            $arrQueryParams = array($arrParams['competitionId']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            
            $objResult->competition = $objQuery->fetch(PDO::FETCH_OBJ);
            
            $strSql = "SELECT ctm_season_instance seasonId,
                              ctm_competition competitionId,
                              ctm_team teamId,
                              team_name teamName,
                              ctm_played gamesPlayed,
                              ctm_won gamesWon,
                              ctm_drawn gamesDrawn,
                              ctm_lost gamesLost,
                              ctm_points pointsTotal,
                              ctm_score_for scoreFor,
                              ctm_score_against scoreAgainst,
                              ctm_score_diff scoreDifference,
                              ctm_current_pos currentPosition,
                              ctm_previous_pos previousPosition,
                              ctm_promoted isPromoted,
                              ctm_relegated isRelegated,
                              ctm_winners isWinner,
                              ctm_runners_up isRunnerUp,
                              ctm_knocked_out_round roundKnockedOut
                        FROM competition_team 
                        JOIN team ON ctm_team = team_id 
                        WHERE ctm_competition = ? 
                        AND ctm_season_instance = ?
                        ORDER BY ctm_current_pos
                        ";
                        
            $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            
            $objResult->table = $objQuery->fetchAll(PDO::FETCH_OBJ);
                        
            return self::formatData($objResult, $strFormat);
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
                        
            // Get all comp details
            $strSql = "SELECT * FROM competition_type WHERE comp_type_id = ?";
            $arrQueryParams = array($arrParams['competitionId']);
            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            $arrComp = $objQuery->fetch(PDO::FETCH_ASSOC);
                        
            if ($arrComp['comp_format'] != 'LEAGUE')
            {
                throw new ApiException("This competition does not use a league table.", 405);
            }
                        
            // Create a blank table if one isn't already there.            
            self::createTablesByCompetition($arrParams);
            
            // Update table from fixtures
            $arrParams['played'] = 1;
            $objFixtures = new DataFixtures();        
            $arrFixtures = $objFixtures->getFixturesByCompetitionAndSeason($arrParams, 'array');
                        
            $arrTeams = array();
            foreach ($arrFixtures as $arrResult)
            {                
                $intHomeTeamId = $arrResult['fix_home_team'];
                $intAwayTeamId = $arrResult['fix_away_team'];
                
                // Played
                $arrTeams[$intHomeTeamId]['played'] = empty($arrTeams[$intHomeTeamId]['played']) ? 1 : $arrTeams[$intHomeTeamId]['played'] + 1;
                $arrTeams[$intAwayTeamId]['played'] = empty($arrTeams[$intAwayTeamId]['played']) ? 1 : $arrTeams[$intAwayTeamId]['played'] + 1;
             
                // W/D/L
                switch (true)
                {
                    case ($arrResult['fix_home_score'] > $arrResult['fix_away_score']): //home win
                        $arrTeams[$intHomeTeamId]['won'] = empty($arrTeams[$intHomeTeamId]['won']) ? 1 : $arrTeams[$intHomeTeamId]['won'] + 1;
                        $arrTeams[$intHomeTeamId]['lost'] = empty($arrTeams[$intHomeTeamId]['lost']) ? 0 : $arrTeams[$intHomeTeamId]['lost'];
                        $arrTeams[$intHomeTeamId]['drawn'] = empty($arrTeams[$intHomeTeamId]['drawn']) ? 0 : $arrTeams[$intHomeTeamId]['drawn'];
                        
                        $arrTeams[$intAwayTeamId]['lost'] = empty($arrTeams[$intAwayTeamId]['lost']) ? 1 : $arrTeams[$intAwayTeamId]['lost'] + 1;
                        $arrTeams[$intAwayTeamId]['won'] = empty($arrTeams[$intAwayTeamId]['won']) ? 0 : $arrTeams[$intAwayTeamId]['won'];
                        $arrTeams[$intAwayTeamId]['drawn'] = empty($arrTeams[$intAwayTeamId]['drawn']) ? 0 : $arrTeams[$intAwayTeamId]['drawn'];
                        break;
                        
                    case ($arrResult['fix_away_score'] > $arrResult['fix_home_score']): //away win
                        $arrTeams[$intHomeTeamId]['lost'] = empty($arrTeams[$intHomeTeamId]['lost']) ? 1 : $arrTeams[$intHomeTeamId]['lost'] + 1;
                        $arrTeams[$intHomeTeamId]['won'] = empty($arrTeams[$intHomeTeamId]['won']) ? 0 : $arrTeams[$intHomeTeamId]['won'];
                        $arrTeams[$intHomeTeamId]['drawn'] = empty($arrTeams[$intHomeTeamId]['drawn']) ? 0 : $arrTeams[$intHomeTeamId]['drawn'];

                        $arrTeams[$intAwayTeamId]['won'] = empty($arrTeams[$intAwayTeamId]['won']) ? 1 : $arrTeams[$intAwayTeamId]['won'] + 1;
                        $arrTeams[$intAwayTeamId]['lost'] = empty($arrTeams[$intAwayTeamId]['lost']) ? 0 : $arrTeams[$intAwayTeamId]['lost'];
                        $arrTeams[$intAwayTeamId]['drawn'] = empty($arrTeams[$intAwayTeamId]['drawn']) ? 0 : $arrTeams[$intAwayTeamId]['drawn'];
                        break;
                        
                    default: //draw 
                        $arrTeams[$intHomeTeamId]['drawn'] = empty($arrTeams[$intHomeTeamId]['drawn']) ? 1 : $arrTeams[$intHomeTeamId]['drawn'] + 1;
                        $arrTeams[$intHomeTeamId]['won']   = empty($arrTeams[$intHomeTeamId]['won']) ? 0 : $arrTeams[$intHomeTeamId]['won'];
                        $arrTeams[$intHomeTeamId]['lost']  = empty($arrTeams[$intHomeTeamId]['lost']) ? 0 : $arrTeams[$intHomeTeamId]['lost'];

                        $arrTeams[$intAwayTeamId]['drawn'] = empty($arrTeams[$intAwayTeamId]['drawn']) ? 1 : $arrTeams[$intAwayTeamId]['drawn'] + 1;
                        $arrTeams[$intAwayTeamId]['won']   = empty($arrTeams[$intAwayTeamId]['won']) ? 0 : $arrTeams[$intAwayTeamId]['won'];
                        $arrTeams[$intAwayTeamId]['lost']  = empty($arrTeams[$intAwayTeamId]['lost']) ? 0 : $arrTeams[$intAwayTeamId]['lost'];
                        break;                
                }             
                
                //points
                $arrTeams[$intHomeTeamId]['points']  = $arrTeams[$intHomeTeamId]['won'] * $arrComp['comp_points_win'];
                $arrTeams[$intHomeTeamId]['points'] += $arrTeams[$intHomeTeamId]['drawn'] * $arrComp['comp_points_draw'];
                $arrTeams[$intHomeTeamId]['points'] += $arrTeams[$intHomeTeamId]['lost'] * $arrComp['comp_points_lose'];

                $arrTeams[$intAwayTeamId]['points']  = $arrTeams[$intAwayTeamId]['won'] * $arrComp['comp_points_win'];
                $arrTeams[$intAwayTeamId]['points'] += $arrTeams[$intAwayTeamId]['drawn'] * $arrComp['comp_points_draw'];
                $arrTeams[$intAwayTeamId]['points'] += $arrTeams[$intAwayTeamId]['lost'] * $arrComp['comp_points_lose'];
                
                //difference
                $arrTeams[$intHomeTeamId]['score_for'] = empty($arrTeams[$intHomeTeamId]['score_for']) ? $arrResult['fix_home_score'] : $arrTeams[$intHomeTeamId]['score_for'] + $arrResult['fix_home_score'];
                $arrTeams[$intHomeTeamId]['score_against'] = empty($arrTeams[$intHomeTeamId]['score_against']) ? $arrResult['fix_away_score'] : $arrTeams[$intHomeTeamId]['score_against'] + $arrResult['fix_away_score'];
                $arrTeams[$intHomeTeamId]['score_diff'] = $arrTeams[$intHomeTeamId]['score_for'] - $arrTeams[$intHomeTeamId]['score_against'];
                
                $arrTeams[$intAwayTeamId]['score_for'] = empty($arrTeams[$intAwayTeamId]['score_for']) ? $arrResult['fix_away_score'] : $arrTeams[$intAwayTeamId]['score_for'] + $arrResult['fix_away_score'];
                $arrTeams[$intAwayTeamId]['score_against'] = empty($arrTeams[$intAwayTeamId]['score_against']) ? $arrResult['fix_home_score'] : $arrTeams[$intAwayTeamId]['score_against'] + $arrResult['fix_home_score'];
                $arrTeams[$intAwayTeamId]['score_diff'] = $arrTeams[$intAwayTeamId]['score_for'] - $arrTeams[$intAwayTeamId]['score_against'];                
            }
            
            $this->objDb->beginTransaction();
            foreach ($arrTeams as $intTeamId=>$arrTeamInfo)
            {
                $strSql = "UPDATE competition_team 
                            SET ctm_played = ?, 
                                ctm_won = ?,
                                ctm_drawn = ?,
                                ctm_lost = ?,
                                ctm_points = ?,
                                ctm_score_for = ?,
                                ctm_score_against = ?,
                                ctm_score_diff = ?
                            WHERE ctm_competition = ? 
                            AND ctm_season_instance = ?
                            AND ctm_team = ?                                
                            ";
                            
                $arrQueryParams = array($arrTeamInfo['played'], 
                                        $arrTeamInfo['won'], 
                                        $arrTeamInfo['drawn'], 
                                        $arrTeamInfo['lost'], 
                                        $arrTeamInfo['points'], 
                                        $arrTeamInfo['score_for'], 
                                        $arrTeamInfo['score_against'], 
                                        $arrTeamInfo['score_diff'],
                                        $arrParams['competitionId'],
                                        $arrParams['seasonId'],
                                        $intTeamId
                                        );
                                        
                $objQuery = $this->objDb->prepare($strSql);
                $objQuery->execute($arrQueryParams);                
            }

            $this->objDb->commit();
            
            self::sortTableForCompetition($arrParams);
                        
            return self::getTablesByCompetition($arrParams);
                                                     
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
            
            /*
            if (!$this->objDb->inTransaction())            
            {
                $this->objDb->beginTransaction();
                $bolCommit = true;
            }
            */
            
            foreach($arrTeams as $objTeam)
            {
                $strSql = "INSERT INTO competition_table (ctb_competition, ctb_season_instance, ctb_team) VALUES (?, ?, ?)";
                $objQuery = $this->objDb->prepare($strSql);
                $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId'], $objTeam->teamId);
                $objQuery->execute($arrQueryParams);
            }            
            
            /*
            if (!empty($bolCommit))
            {
                $this->objDb->commit();                
            }
            */
            
            return true;            
        }
        
        
        /**
        * Sort a season/competition and update the rankings into the table.
        * 
        * @param mixed $arrParams
        * @param mixed $strFormat
        */
        private function sortTableForCompetition(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('competitionId', 'seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);
            
            $strSql = "SELECT ctm_team 
                        FROM competition_team
                        WHERE ctm_competition = ?
                        AND ctm_season_instance = ? 
                        ORDER BY ctm_knocked_out_round ASC, ctm_points DESC, ctm_score_diff DESC, ctm_score_for DESC, ctm_score_against ASC, ctm_won DESC, ctm_drawn DESC, ctm_lost ASC, ctm_previous_pos ASC
                        ";

            $objQuery = $this->objDb->prepare($strSql);
            $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);
            $objQuery->execute($arrQueryParams);
            $arrComp = $objQuery->fetchAll(PDO::FETCH_COLUMN);
                        
            foreach ($arrComp as $intRank=>$intTeam)
            {
                $intRank += 1;
                
                $strSql = "UPDATE competition_team 
                            SET ctm_previous_pos = ctm_current_pos, 
                                ctm_current_pos = ? 
                            WHERE ctm_season_instance = ? 
                                AND ctm_competition = ? 
                                AND ctm_team = ?
                          ";
                          
                $objQuery = $this->objDb->prepare($strSql);
                $arrQueryParams = array($intRank, $arrParams['seasonId'], $arrParams['competitionId'], $intTeam);
                $objQuery->execute($arrQueryParams);                
            }
                                    
            return true;            
        }

    }
?>
