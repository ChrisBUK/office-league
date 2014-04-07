<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractSession.php';

class Session extends AbstractSession
{
    
    
    public function isLoggedIn() {
        return $this->getVisitVar('loggedIn');
    }
        

}
  
?>
