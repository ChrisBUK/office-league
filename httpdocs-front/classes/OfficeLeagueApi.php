<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractApi.php';

    class OfficeLeagueApi extends AbstractApi
    {
                
        public function getJson($strResource, array $arrParams)
        {
            return AbstractApi::getJson(Config::API_HOST . '/' .$strResource, $arrParams);
        }
        
    }
?>
