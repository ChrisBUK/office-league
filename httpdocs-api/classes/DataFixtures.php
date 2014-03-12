<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';

    class DataFixtures extends AbstractData
    {
        /** 
        * Get tables by competition, optionally filter on season/team.
        * 
        * @param mixed $arrParams
        */
        public function getFixturesByCompetitionAndSeason()
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

            return array($strSql, $arrQueryParams);
        }

    }
?>
