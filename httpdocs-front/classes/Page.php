<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractRenderer.php';

class Page extends AbstractRenderer
{
    protected $objSession;
    
    public function setSession(session $objSession)
    {
        $this->objSession = $objSession;
    }
    
    /**
    * Query the session to find out if we're logged in
    */
    public function isLoggedIn()
    {
        return $this->objSession->isLoggedIn();
    }
    
    public function login()
    {
        
        $this->objSession->login();        
    }
    
    public function logout()
    {
        $this->objSession->logout();
    }
    
    public function createCsrfToken()
    {
        $strTokenValue = chr(rand(65,90)) . rand(1000000,9999999);
        $this->objSession->setPageVar('csrfToken', $strTokenValue);
        return $strTokenValue;
    }
    
    public function checkCsrfToken($strProvidedToken)
    {
        $strExpectedToken = $this->objSession->getPageVar('csrfToken');
        return ($strProvidedToken === $strExpectedToken) ? true : false;        
    }
    
}
?>
