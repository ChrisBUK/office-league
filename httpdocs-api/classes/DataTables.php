<?php
  
  class DataTables extends AbstractData
  {
      /** 
      * Get tables by competition, optionally filter on season/team.
      * 
      * @param mixed $arrParams
      */
      public function getTablesByCompetition($arrParams)
      {
          $strSql = "SELECT * FROM competition_table WHERE ctb_competition = ? AND ctb_season = ?";
          $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);
                    
          return array($strSql, $arrQueryParams);
      }
      
  }
?>
