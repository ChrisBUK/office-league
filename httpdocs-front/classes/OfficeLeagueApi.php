<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractApi.php';

    class OfficeLeagueApi extends AbstractApi
    {
        
        const API_HOST = 'officeleague-api.dev.codecraft.co.uk';
        
        public function getJson($strResource, array $arrParams)
        {
            return AbstractApi::getJson(self::API_HOST . '/' .$strResource, $arrParams);
        }
        
    }
?>
