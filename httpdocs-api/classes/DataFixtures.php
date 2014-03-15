<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataTeams.php';

    class DataFixtures extends AbstractData
    {
        /** 
        * Get Fixtures by competition, optionally filter on season/team.
        * 
        * @param mixed $arrParams
        */
        public function getFixturesByCompetitionAndSeason(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('competitionId', 'seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);            
            
            $strSql = "SELECT * FROM competition_fixture WHERE fix_competition = ? AND fix_season = ? AND fix_played = 0";
            $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            return self::formatData($objQuery->fetchAll(PDO::FETCH_ASSOC), $strFormat);
        }
        
        /**
        * Generate all fixtures for a competition
        */
        public function createLeagueFixturesByCompetition(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('competitionId', 'seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);            
                        
            // Roll fixture list & Build up a round of matches for each player.
            $arrTeams = DataTeams::getTeamsInCompetition($arrParams, 'array');
            shuffle($arrTeams); //Randomly ordering the teams prevents fixtures always being in the same order.
            
            $intNumTeams = count($arrTeams);
            
            //Even up the teams so that fixtures can always be plotted, in unevenly number leagues, each team will have a round where they don't play.
            if ($intNumTeams % 2 == 1) {
                $intNumTeams++;
            }
            
            $intTotalRounds = $intNumTeams - 1;
            $intMatchesPerRound = $intNumTeams / 2;
            
            //Some of this logic borrowed from http://bluebones.net/2005/05/generating-fixture-lists/
            for ($intRound = 0; $intRound < $intTotalRounds; $intRound++)
            {
                for ($intMatch = 0; $intMatch < $intMatchesPerRound; $intMatch++)
                {
                    $intHome = ($intRound + $intMatch) % ($intNumTeams - 1);
                    $intAway = ($intNumTeams - 1 - $intMatch + $intRound) % ($intNumTeams - 1);
                    
                    if ($intMatch == 0)
                    {
                        $intAway = $intNumTeams - 1;
                    }
                                        
                    if (!empty($arrTeams[$intHome]) && !empty($arrTeams[$intAway]))
                    {
                        $arrFixtureRounds[$intRound][$intMatch] = array('home'=>$arrTeams[$intHome], 'away'=>$arrTeams[$intAway]);    
                    }                                                        
                }
            }            
                                            
            $this->objDb->beginTransaction();
            foreach ($arrFixtureRounds as $intRoundId=>$arrRound)
            {
                foreach($arrRound as $intMatchId=>$arrMatch)
                {
                    $strSql = "INSERT INTO competition_fixture (fix_season, fix_competition, fix_round, fix_home_team, fix_away_team) VALUES (?,?,?,?,?)";
                    $objQuery = $this->objDb->prepare($strSql);
                    $objQuery->execute(array($arrParams['seasonId'], $arrParams['competitionId'], $intRoundId + 1, $arrMatch['home']->teamId, $arrMatch['away']->teamId));                                                
                }
            }
                        
            $this->objDb->commit();
                                                        
                                                            
            return self::formatData($arrFixtureRounds, $strFormat);            
        }
        
        /**
        * Generate a set of knockout fixtures for a competition.
        * 
        * @param mixed $strFormat
        */
        protected function createKnockoutFixtures(array $arrParams, array $arrTeamIds, $strFormat='json')
        {        
            
            $arrRequired = array('competitionId', 'seasonId', 'roundId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);            
            
            shuffle($arrTeamIds); // To keep the draw fresh.
            
            $arrFixtures = array();
            
            while (!empty($arrTeamIds))
            {
                $intTeam1 = array_shift($arrTeamIds);
                $intTeam2 = array_shift($arrTeamIds);
                
                $arrFixtures[] = array('home'=>$intTeam1, 'away'=>$intTeam2);
            }
            
            //Insert into DB
            $this->objDb->beginTransaction();
            
            // Get list of teams already known in comp so we don't add them again
            $strSql = "SELECT ctm_team FROM competition_team WHERE ctm_season_instance = ? AND ctm_competition = ? AND ctm_team IN (?,?)";
            $objQuery = $this->objDb->prepare($strSql);
            $arrQueryParams = array($arrParams['seasonId'], $arrParams['competitionId'], $arrFixture['home'], $arrFixture['away']);
            $objQuery->execute($arrQueryParams);                         
            $arrTeams = $objQuery->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($arrFixtures as $arrFixture)
            {
                // Add fixture to season
                $strSql = "INSERT INTO competition_fixture (fix_season, fix_competition, fix_round, fix_home_team, fix_away_team) VALUES (?,?,?,?,?)";
                $objQuery = $this->objDb->prepare($strSql);
                $arrQueryParams = array($arrParams['seasonId'], $arrParams['competitionId'], $arrParams['roundId'], $arrFixture['home'], $arrFixture['away']);
                $objQuery->execute($arrQueryParams);   
                                                  
                // Add newly drawn teams to competition table
                if (!in_array($arrFixture['home'], $arrTeams))
                {
                    $strSql = "INSERT INTO competition_team (ctm_season_instance, ctm_competition, ctm_team) VALUES (?,?,?)";                                           
                    $objQuery = $this->objDb->prepare($strSql);
                    $arrQueryParams = array($arrParams['seasonId'], $arrParams['competitionId'], $arrFixture['home']);
                    $objQuery->execute($arrQueryParams);                         
                }

                if (!in_array($arrFixture['away'], $arrTeams))
                {
                    $strSql = "INSERT INTO competition_team (ctm_season_instance, ctm_competition, ctm_team) VALUES (?,?,?)";                                           
                    $objQuery = $this->objDb->prepare($strSql);
                    $arrQueryParams = array($arrParams['seasonId'], $arrParams['competitionId'], $arrFixture['away']);
                    $objQuery->execute($arrQueryParams);                         
                }                
            }           
            $this->objDb->commit();
            
            return $arrFixtures;               
        }
        
        /**
        * This method specifically deals with the SSDM cup and the logic has to change
        * based on the number of teams in it.  Someone must create a way of getting the teams
        * to the right number in the fairest way and then code it in.
        * 
        * @param mixed $arrTeamIds
        * @param mixed $intRound
        * @param mixed $strFormat
        */
        public function drawSsdmCupRound($arrParams, $strFormat='json')
        {
            // With 13 teams across 3 divisions of 5, 4, 4 the logic is thus:
            // --
            // Round 1    - Third Division Teams only (4 teams -> 2 teams)
            // Round 2    - Second Division join Winners of R1 (4 + 2 teams = 6 -> 3 teams)
            // Round 3 QF - Premier Division join Winners of R2 (5 + 3 teams = 8 -> 4 teams)
            // Round 4 SF - Semi Finals (4 -> 2 teams)
            // Round 5 F  - Finals (2 -> 1 team)                                    
                           
            $arrRequired = array('competitionId', 'seasonId', 'roundId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);            
            
            $arrRoundParams = $arrParams; //A copy of the params so we can change a few values.
            $arrParams['notKnockedOut'] = 1;
                                                
            switch ($arrParams['roundId']) 
            {
                case 1:
                    $arrRoundParams['competitionId'] = 3;
                    
                    $objTeams = new DataTeams();
                    $arrTeams = $objTeams->getTeamsInCompetition($arrRoundParams, 'array');
                    break;
                    
                case 2:
                    $arrRoundParams['competitionId'] = 2;
                    
                    $objTeams = new DataTeams();
                    $arrTeamsInComp = $objTeams->getTeamsInCompetition($arrParams, 'array');                
                    $arrTeamsAdded  = $objTeams->getTeamsInCompetition($arrRoundParams, 'array');                                    
                    $arrTeams = array_merge($arrTeamsInComp, $arrTeamsAdded);
                    break;
                    
                case 3:
                    $arrRoundParams['competitionId'] = 1;
                    
                    $objTeams = new DataTeams();
                    $arrTeamsInComp = $objTeams->getTeamsInCompetition($arrParams, 'array');                
                    $arrTeamsAdded  = $objTeams->getTeamsInCompetition($arrRoundParams, 'array');                
                    $arrTeams = array_merge($arrTeamsInComp, $arrTeamsAdded);
                    break;
                    
                case 4: 
                case 5:
                    $objTeams = new DataTeams();
                    $arrTeams = $objTeams->getTeamsInCompetition($arrParams, 'array');                
                    break;
            }

            //Just need the IDs                   
            $arrTeamIds = array();
            foreach ($arrTeams as $objTeam)
            {
                $arrTeamIds[] = $objTeam->teamId;
            }
            
            return self::createKnockoutFixtures($arrParams, $arrTeamIds);            
        }

    }
?>