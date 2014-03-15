<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';

    class DataTeams extends AbstractData
    {
        
        /**
        * List the teams in a competition
        * 
        */
        function getTeamsInCompetition(array $arrParams, $strFormat='json')
        {
            $arrRequired = array('competitionId','seasonId'); 
            $arrOptional = array();

            if (!self::hasRequiredParameters($arrRequired, $arrParams))
            {
                throw new ApiException("The following parameters are required: ".join(',',$arrRequired), 400);
            }    
            
            $arrParams = Parameters::getFullParamList($arrRequired, $arrOptional, $arrParams);
            
            $strSql = "SELECT ctm_team AS teamId, 
                              team_name AS teamName, 
                              ctm_promoted AS isPromoted,
                              ctm_relegated AS isRelegated,
                              ctm_winners AS isWinner,
                              IF (ctm_knocked_out_round > 0, 1, 0) AS isKnockedOut,
                              ctm_knocked_out_round AS knockedOutRound
                       FROM competition_team 
                       JOIN team ON ctm_team = team_id 
                       WHERE ctm_competition = ? 
                       AND ctm_season_instance = ?
                       ORDER BY ctm_winners DESC, ctm_promoted DESC, ctm_relegated ASC, ctm_knocked_out_round ASC                       
                       ";            
            
            $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);

            $objQuery = $this->objDb->prepare($strSql);
            $objQuery->execute($arrQueryParams);
            
            return self::formatData($objQuery->fetchAll(PDO::FETCH_CLASS), $strFormat);
        }
        
    }  
?>
