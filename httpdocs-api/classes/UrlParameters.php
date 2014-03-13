<?php
/**
* Simple class to sanitize what's being passed in and make sure that we like it.
* 
* @author Chris Booker
*/

class UrlParameters {

    /**
    * Ensure all parameters adhere to a common format of being trimmed/lowercased.
    * 
    * @param string 
    */
    protected static function cleanParam($strValue)
    {
        return trim(strtolower($strValue));
    }
    
    /**
    * Check a parameter against an allowed list and return a valid value, or a default value, or bool false.
    * 
    * @param mixed $strParamName
    * @param mixed $arrAllowedList
    * @param mixed $strDefault
    */
    protected static function getAllowedParam($strParamName, array $arrAllowedList, $strDefault=null)
    {        
        if (empty($_GET[$strParamName]) || !in_array($_GET[$strParamName], $arrAllowedList))
        {            
            return ($strDefault === null) ? false : $strDefault;
        }
        
        return $_GET[$strParamName];        
    }
    
    /**
    * Build a list of parameters based on what is allowed, and what is given, so we can ensure 
    * that other crap doesn't get through.
    * 
    * @param mixed $arrRequired
    * @param mixed $arrOptional
    */
    public static function getFullParamList(array $arrRequired, array $arrOptional = null)
    {
        $arrParamList = array();
        
        foreach ($arrRequired as $strParam) 
        {
            $arrParamList[$strParam] = self::getParam($strParam);
        }
        if ($arrOptional != null)
        {
            foreach ($arrOptional as $strParam)
            {
                $arrParamList[$strParam] = self::getParam($strParam);
            }
        }
        return $arrParamList;
    }
    
    /**
    * Get a recognised parameter, sanitize it, and return it.
    * 
    * @param string 
    */
    public static function getParam($strParamName) 
    {        
               
        switch ($strParamName) 
        {
            case 'method':
                $arrAllowList = array('get','create');
                $strParamValue = self::getAllowedParam('method', $arrAllowList, 'get');
                break;
            
            case 'action':
                $strParamValue = !empty($_GET['action']) ? $_GET['action'] : null;
                break;
                
            case 'competitionId':                
                $strParamValue = !empty($_GET['competitionId']) ? intval($_GET['competitionId']) : 0;
                break;
            
            case 'seasonId':
                $strParamValue = !empty($_GET['seasonId']) ? intval($_GET['seasonId']) : 0;
                break;
            
            case 'teamId':
                $strParamValue = !empty($_GET['teamId']) ? intval($_GET['teamId']) : 0;
                break;            
                
            default:
                return null;
                break;
        }
        
        return $strParamValue;
    }
  
}
?>
