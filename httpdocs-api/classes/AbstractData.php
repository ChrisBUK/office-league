<?php
/**
* Abstract data - class for common database queries
*
* @Author Chris Booker
*/

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Parameters.php';

abstract class AbstractData
{
    
    const PDO_DRIVER  = 'mysql';
    const PDO_HOST    = 'localhost';
    const PDO_DB      = 'office_league';
    const PDO_CHARSET = 'utf8';
    const PDO_USER    = 'root';
    const PDO_PASS    = '';

    function __construct()
    {
        $this->objDb = self::buildPDO();
    }
    
    public function buildPDO()
    {
        $strDsn = sprintf('%s:host=%s;dbname=%s;charset=%s', self::PDO_DRIVER, self::PDO_HOST, self::PDO_DB, self::PDO_CHARSET);
        return new PDO($strDsn, self::PDO_USER, self::PDO_PASS, array(PDO::ERRMODE_EXCEPTION));
    }
    
    /**
    * Check a parameter list against what we've got and return true if satisfied, false if not.
    * 
    * @param mixed $arrRequired
    */
    public function hasRequiredParameters(array $arrRequired, array $arrParamSource)
    {
        foreach ($arrRequired as $strParam)
        {
            if (Parameters::getParam($strParam, $arrParamSource) == null)
            {
                return false;
            }
        }
        return true;
    }
    
    /**
    * Format the data.
    * JSON is for output via the API, other options are more for internal use.
    * 
    * @param mixed $objData
    * @param mixed $strFormat
    */
    protected function formatData($objData, $strFormat)
    {
        switch (strtolower($strFormat))
        {
            case 'json':
                return json_encode($objData);
                break;
            
            case 'array':
                return $objData;
                break;
        }
    }
    
}
?>
