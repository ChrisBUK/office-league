<?php
  
  class DataFixtures extends AbstractData
  {
      /** 
      * Get tables by competition, optionally filter on season/team.
      * 
      * @param mixed $arrParams
      */
      public function getFixturesByCompetitionAndSeason($arrParams)
      {
          $strSql = "SELECT * FROM competition_fixture WHERE fix_competition = ? AND fix_season = ? AND fix_played = 0";
          $arrQueryParams = array($arrParams['competitionId'], $arrParams['seasonId']);
                    
          return array($strSql, $arrQueryParams);
      }
      
  }
?>
