<?php
    /**
    * This class provides a way to interact with Paul's ranking service API
    */

    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataFixtures.php';

    class RankingService extends AbstractData
    {  
        const SERVICE_HOST = Config::RANKINGS_SERVICE_HOST;

        private function makeRequest($strEndpoint, $strMethod, array $arrRequestData = null)
        {            
            $arrAllowedMethods = array('GET', 'POST', 'PUT', 'DELETE');
            
            if (!is_null($arrRequestData))            
            {
                if (self::validateRequest($arrRequestData))
                {
                    $strRequestJson = json_encode($arrRequestData);    
                } else {
                    throw new ApiException('Invalid ranking service request - malformed data', 400);
                }                
            }
            
            if (!in_array($strMethod, $arrAllowedMethods))
            {
                throw new ApiException('Invalid ranking service request - bad method', 400);                
            }
            
            $resCurl = curl_init(self::SERVICE_HOST . $strEndpoint);
            curl_setopt($resCurl, CURLOPT_CUSTOMREQUEST, $strMethod);
            curl_setopt($resCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($resCurl, CURLOPT_TIMEOUT, 2);
            
            if (!empty($strRequestJson))
            {
                curl_setopt($resCurl, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Content-length: '.strlen($strRequestJson)));
                curl_setopt($resCurl, CURLOPT_POSTFIELDS, $strRequestJson);
            }

            return curl_exec($resCurl);
        }

        private function validateRequest($arrRequestData)
        {
            //Switch allows us to write these simple checks in a more compact way.
            switch (true)
            {
                case (empty($arrRequestData['h']['player'])): //No home player name
                case (!isset($arrRequestData['h']['score'])): //No home player score
                case (!is_int($arrRequestData['h']['score'])): //Home player score not a number
                case (($arrRequestData['h']['score']) < 0):   //Home player score < 0
                case (empty($arrRequestData['a']['player'])): //No away player name
                case (!isset($arrRequestData['a']['score'])): //No away player score
                case (!is_int($arrRequestData['a']['score'])): //Away player score not a number
                case (($arrRequestData['a']['score']) < 0):   //Away player score < 0
                case (empty($arrRequestData['group'])):       //No group                                
                case (empty($arrRequestData['ref'])):         //No match ref.
                case (empty($arrRequestData['playeddate'])):  //No played date (optional in service but required for our use)
                    return false;   
                    break;

                default:
                    return true;
                    break;
            }
        }        

        public function getGamesByGroup($strGroupId)
        {
            $strEndpoint = sprintf("/games/group/%s", $strGroupId);
            $strMethod = 'GET';
            
            return self::makeRequest($strEndpoint, $strMethod);
        }

        public function deleteGamesByGroup($intGroupId)
        {
            $strEndpoint = sprintf("/games/group/%s", $intGroupId);
            $strMethod = 'DELETE';
            
            return self::makeRequest($strEndpoint, $strMethod);
        }

        public function createGame($arrData)
        {
            $strEndpoint = '/games';
            $strMethod = 'POST';            
            
            return self::makeRequest($strEndpoint, $strMethod, $arrData);
        }

        public function updateGame($arrData)
        {
            $strEndpoint = '/games';
            $strMethod = 'PUT';
            
            return self::makeRequest($strEndpoint, $strMethod, $arrData);
        }

        public function getGameByRefAndGroup($strGroupId, $intRefId)
        {
            $strEndpoint = sprintf("/games/group/%s/ref/%s", $strGroupId, $intRefId);
            $strMethod = 'GET';
            
            return self::makeRequest($strEndpoint, $strMethod);
        }

        public function deleteGameByRefAndGroup($strGroupId, $intRefId)
        {
            $strEndpoint = sprintf("/games/group/%s/ref/%s", $strGroupId, $intRefId);
            $strMethod = 'GET';
            
            return self::makeRequest($strEndpoint, $strMethod);
        }

        public function getLatestRatings($strGroupId)
        {
            $strEndpoint = sprintf("/games/group/%s/last", $strGroupId, $intRefId);
            $strMethod = 'GET';
            
            return self::makeRequest($strEndpoint, $strMethod);
        }


    }
?>
