<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';

    class DataTeams extends AbstractData
    {
        
        /**
        * List the teams in a competition
        * 
        */
        function getTeamsInCompetition($strFormat='json')
        {
            $arrRequired = array('competitionId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }    
            
            $arrParams = UrlParameters::getFullParamList($arrRequired, $arrOptional);

            $strSql = "SELECT ctm_team AS teamId, team_name AS teamName FROM competition_team JOIN team ON ctm_team = team_id WHERE ctm_comp = ?";
            $arrQueryParams = array($arrParams['competitionId']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            
            return self::formatData($objQuery->fetchAll(PDO::FETCH_CLASS), $strFormat);
        }
        
    }  
?>
