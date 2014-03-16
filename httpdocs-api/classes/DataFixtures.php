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
            $arrOptional = array('played');

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);            
            
            $strSql = "SELECT * FROM competition_fixture WHERE fix_competition = ? AND fix_season = ? AND fix_played = ?";
            $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId'], $arrParams['played']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            return self::formatData($objQuery->fetchAll(PDO::FETCH_ASSOC), $strFormat);
        }
        
        public function getFixturesBySeason(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('seasonId'); 
            $arrOptional = array('played');

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);            
            
            if (is_null($arrParams['played']))
            {
                $strSql  = "SELECT 
                                fix_round roundId,
                                fix_competition competitionId,
                                (SELECT comp_name FROM competition_type WHERE comp_type_id = fix_competition) competitionName,
                                fix_home_team homeTeamId,
                                (SELECT team_name FROM team WHERE team_id = fix_home_team) AS homeTeamName,
                                (SELECT team_name FROM team WHERE team_id = fix_away_team) AS awayTeamName,
                                fix_away_team awayTeamId,
                                fix_played isPlayed,
                                fix_home_score homeTeamScore,
                                fix_away_score awayTeamScore,
                                fix_id fixtureId                
                            FROM competition_fixture                            
                            WHERE fix_season = ?";
                            
                $arrQueryParams = array($arrParams['seasonId']);
            }
            else {
                
                $strSql  = "SELECT
                                fix_round roundId,
                                fix_competition competitionId,
                                (SELECT comp_name FROM competition_type WHERE comp_type_id = fix_competition) competitionName,
                                fix_home_team homeTeamId,
                                (SELECT team_name FROM team WHERE team_id = fix_home_team) AS homeTeamName,
                                (SELECT team_name FROM team WHERE team_id = fix_away_team) AS awayTeamName,
                                fix_away_team awayTeamId,
                                fix_played isPlayed,
                                fix_home_score homeTeamScore,
                                fix_away_score awayTeamScore                
                             FROM competition_fixture 
                             WHERE fix_season = ? 
                             AND fix_played = ?";
                             
                $arrQueryParams = array($arrParams['seasonId'], $arrParams['played']);                
            }

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            return self::formatData($objQuery->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC), $strFormat);
            
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
            $strSql = "SELECT ctm_team FROM competition_team WHERE ctm_season_instance = ? AND ctm_competition = ?";
            $objQuery = $this->objDb->prepare($strSql);
            $arrQueryParams = array($arrParams['seasonId'], $arrParams['competitionId']);
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
        * based on the number of teams in it. A way of getting the teams
        * to the right number in the fairest way must be hardcoded in.
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
            
            // With 14 teams (5,5,4):
            // --
            // Round 1    - Qualifier, Third Division Teams Only (4 teams -> 2 teams)
            // Round 2    - Qualifier 2 (2 teams -> 1 team)
            // Round 3    - Add Second Division (5 + 1 teams = 6 -> 3 teams)
            // Round 4 QF - Add Premier Division (5 + 3 teams = 8 -> 4 teams)
            // Round 5 SF - Semi Finals (4 teams -> 2 teams)
            // Round 6 F  - Finals (2 -> 1 team)
            
            // With 15 teams (5,5,5):
            // --
            // Round 1    - All Teams, current holder gets a bye (15 + 1 = 16 -> 8 teams)
            // Round 2 QF - 8->4 teams
            // Round 3 SF - 4->2 teams
            // Round 4 F  - 2->1 team
                                           
                           
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
                
                default:
                    throw new ApiException("Cup draw is complete, no more rounds.");                    
            }

            //Just need the IDs                   
            $arrTeamIds = array();
            foreach ($arrTeams as $objTeam)
            {
                $arrTeamIds[] = $objTeam->teamId;
            }
            
            return self::createKnockoutFixtures($arrParams, $arrTeamIds);            
        }
        
        
        /**
        * Resolve a fixture and update appropriate tables/KO's etc
        * 
        * @param mixed $arrParams
        * @param mixed $strFormat
        */
        public function updateResult($arrParams, $strFormat='json')
        {
            
            $arrRequired = array('fixtureId', 'homeScore', 'awayScore'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);            
            
            $strSql = "SELECT fix_id, fix_season, fix_competition, fix_round, fix_home_team, fix_away_team, (SELECT comp_format FROM competition_type WHERE fix_competition = comp_type_id) format FROM competition_fixture WHERE fix_id = ?";            
            $objQuery = $this->objDb->prepare($strSql);
            $arrQueryParams = array($arrParams['fixtureId']);
            $objQuery->execute($arrQueryParams);                   
                        
            if ($objQuery->rowCount() == 0)
            {
                throw new ApiException("Fixture does not exist.", 400);
            }            
                        
            $arrFixture = $objQuery->fetch(PDO::FETCH_ASSOC);
            
            if ($arrFixture['format'] == 'KO' && $arrParams['awayScore'] == $arrParams['homeScore'])
            {
                throw new ApiException("Knockout fixture cannot end in a draw. Play extra time and penalties!", 400);
            }
            
            $strSql = "UPDATE competition_fixture SET fix_played = 1, fix_home_score = ?, fix_away_score = ? WHERE fix_id = ?";                                           
            $objQuery = $this->objDb->prepare($strSql);
            $arrQueryParams = array($arrParams['homeScore'], $arrParams['awayScore'], $arrParams['fixtureId']);
            $objQuery->execute($arrQueryParams);                   
            
            if ($objQuery->rowCount() == 0)
            {
                throw new ApiException("Fixture not updated - The result has not changed.", 400);
            }

            if ($arrFixture['format'] == 'LEAGUE')
            {
                $objTables = new DataTables();
                return $objTables->updateTablesByCompetition(array('competitionId'=>$arrFixture['fix_competition'], 'seasonId'=>$arrFixture['fix_season']), $strFormat);
            } else if ($arrFixture['format'] == 'KO') {
                
                //Knock losers out of cup
                $strSql = "UPDATE competition_team SET ctm_knocked_out_round = ? WHERE ctm_season_instance = ? AND ctm_competition = ? AND ctm_team = ?";
                $objQuery = $this->objDb->prepare($strSql);
            
                $arrQueryParams = array($arrFixture['fix_round'], $arrFixture['fix_season'], $arrFixture['fix_competition']);
                
                if ($arrParams['homeScore'] > $arrParams['awayScore'])
                {
                    $arrQueryParams[] = $arrFixture['fix_away_team'];
                }
                else
                {
                    $arrQueryParams[] = $arrFixture['fix_home_team'];
                }                
                
                $objQuery->execute($arrQueryParams);                   
                                
                //Draw new cup round?
                $strSql = "SELECT COUNT(fix_id) AS unplayedFixtures FROM competition_fixture WHERE fix_season = ? AND fix_competition = ? AND fix_round = ? AND fix_played = 0";
                $objQuery = $this->objDb->prepare($strSql);
                $arrQueryParams = array($arrFixture['fix_season'], $arrFixture['fix_competition'], $arrFixture['fix_round']);
                $objQuery->execute($arrQueryParams);                   
                $intUnplayedFixtures = $objQuery->fetch(PDO::FETCH_COLUMN);
                
                if ($intUnplayedFixtures == 0)
                {
                    $intNextRound = $arrFixture['fix_round'] += 1;
                    $objCup = new DataFixtures();
                    $arrParams = array('seasonId'=>$arrFixture['fix_season'], 'competitionId'=>$arrFixture['fix_competition'], 'roundId'=>$intNextRound);
                    return $objCup->drawSsdmCupRound($arrParams, 'json');
                }
            }
                                                            
        }

    }
?>