<?php
/**
* Abstract data - class for common database queries
*
* @Author Chris Booker
*/

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/UrlParameters.php';

abstract class AbstractData
{

    /**
    * Check a parameter list against what we've got and return true if satisfied, false if not.
    * 
    * @param mixed $arrRequired
    */
    public function hasRequiredParameters(array $arrRequired)
    {
        foreach ($arrRequired as $strParam)
        {
            if (UrlParameters::getParam($strParam) == null)
            {
                return false;
            }
        }
        return true;
    }
    
}
?>
