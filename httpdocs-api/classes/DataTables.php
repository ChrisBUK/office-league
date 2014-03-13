<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';

    class DataTables extends AbstractData
    {
        /** 
        * Get tables by competition, optionally filter on season/team.
        * 
        * @param mixed $arrParams
        */
        public function getTablesByCompetition($strFormat='json')
        {
            $arrRequired = array('competitionId', 'seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }       

            $arrParams = UrlParameters::getFullParamList($arrRequired, $arrOptional);

            $strSql = "SELECT * FROM competition_table WHERE ctb_competition = ? AND ctb_season = ?";
            $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            return self::formatData($objQuery->fetchAll(PDO::FETCH_ASSOC), $strFormat);
        }

    }
?>
