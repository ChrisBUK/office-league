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
                        $arrFixtureRounds[$intRound][$intMatch] = array($arrTeams[$intHome], $arrTeams[$intAway]);    
                    }                                                        
                }
            }            
                                                            
            return self::formatData($arrFixtureRounds, $strFormat);            
        }
        
        /**
        * Generate a set of knockout fixtures for a competition.
        * 
        * @param mixed $strFormat
        */
        protected function createKnockoutFixtures($arrTeamIds, $strFormat='json')
        {        
            shuffle($arrTeamIds);
               
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
        protected function drawSsdmCup($intRound, $strFormat='json')
        {
            // With 13 teams across 3 divisions of 5, 4, 4 the logic is thus:
            // --
            // Round 1    - Third Division Teams only (4 teams -> 2 teams)
            // Round 2    - Second Division join Winners of R1 (4 + 2 teams = 6 -> 3 teams)
            // Round 3 QF - Premier Division join Winners of R2 (5 + 3 teams = 8 -> 4 teams)
            // Round 4 SF - Semi Finals (4 -> 2 teams)
            // Round 5 F  - Finals (2 -> 1 team)                                    
            
            switch ($intRound) 
            {
                case 1:
                    
                    break;
                case 2:
                    break;
                case 3:
                    break;
                case 4: 
                    break;
                case 5:
                    break;                
            }
            
        }

    }
?>