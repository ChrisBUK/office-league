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
        public function createFixturesByCompetition($strFormat='json')
        {
            $arrRequired = array('competitionId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = UrlParameters::getFullParamList($arrRequired, $arrOptional);            
                        
            // Roll fixture list & Build up a round of matches for each player.
            $arrTeams = DataTeams::getTeamsInCompetition('array');
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

    }
?>