<?php


    abstract class AbstractApi 
    {
        
        public function getJson($strResource, array $arrParams)
        {
            $resCurl = curl_init($strResource);
            
            curl_setopt($resCurl, CURLOPT_URL, $strResource . '?' . http_build_query($arrParams));
            curl_setopt($resCurl, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($resCurl, CURLOPT_TIMEOUT, 3);
            curl_setopt($resCurl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($resCurl, CURLOPT_HEADER, false);
            curl_setopt($resCurl, CURLOPT_HTTPHEADER, array('Accept: text/json'));
            //curl_setopt($resCurl, CURLOPT_HTTPGET, $arrParams);

            $objResp = curl_exec($resCurl);
            curl_close($resCurl);
            
            return $objResp;
            
        }
        
    }
?>
