<?php
class AbstractSession {
    
    private $sessionStorage = "sessionVars";
    
    /** 
    * Initialise a session with a unique name and start it.
    * 
    * @param mixed $sessionName
    * @return session
    */
    function __construct($sessionName) {
        session_name($sessionName);
        session_start();
        
        //handle expiration and setting of page variables (only for one page load)
        $_SESSION[$this->sessionStorage]['PREV_PAGE_VARS'] = array();;
        
        if (isset($_SESSION[$this->sessionStorage]['PAGE_VARS'])) {
            $_SESSION[$this->sessionStorage]['PREV_PAGE_VARS'] = $_SESSION[$this->sessionStorage]['PAGE_VARS'];
        }                           
        $_SESSION[$this->sessionStorage]['PAGE_VARS'] = array();
        
        //handle setting of new user variables into cookies
        if (isset($_SESSION[$this->sessionStorage]['NEW_USER_VARS'])) {
            foreach ($_SESSION[$this->sessionStorage]['NEW_USER_VARS'] as $key=>$cookiePair) {
                list ($value,$expires) = $cookiePair;
                setCookie($key,$value,$expires,"/");                     
            }    
            
            unset($_SESSION[$this->sessionStorage]['NEW_USER_VARS']);
        }
        
        //handle unsetting of user variables from cookies into nothing
        if (isset($_SESSION[$this->sessionStorage]['CLEAR_USER_VARS'])) {
            foreach ($_SESSION[$this->sessionStorage]['CLEAR_USER_VARS'] as $key=>$value) {
                setCookie($key,"",time()-3600,"/");                
            }
            unset($_SESSION[$this->sessionStorage]['CLEAR_USER_VARS']);
        }
        
        $_SESSION[$this->sessionStorage]['USER_VARS'] = array();
        
        //handle getting of existing user variables (persistent across multiple session)
        if (isset($_COOKIE)) {
            foreach ($_COOKIE as $key=>$value) {
                if ($value != "") {
                    $_SESSION[$this->sessionStorage]['USER_VARS'][$key] = $value;    
                }                
            }
        }
        
        //Set variables for internal stuff thats quite useful to know, and ensure we've generated the session ID on a new session.
        if (!$this->getVisitVar("startedTime")) {
            session_regenerate_id(true);
            $this->setVisitVar("startedTime",date("U"));
            $this->setVisitVar("sessionAgeSecs",0);
            $this->setVisitVar("userAgent", isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Unknown");
            $this->setVisitVar("userIP", isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "Unknown");
            $this->setVisitVar("userAddr", (function_exists("getHostByAddr") && isset($_SERVER['REMOTE_ADDR'])) ? gethostbyaddr($_SERVER['REMOTE_ADDR']) : "Unknown");
        } else {
            $this->setVisitVar("sessionAgeSecs",date("U")-$this->getVisitVar("startedTime"));
        }        
        
    }
    
    /**
    * Kill session variables, cookie and everything.
    * 
    */
    public function destroy() {        
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }   
        session_destroy();          
        return true;                
    }            
    
    /**
    * Set a page variable.  These only persist to the next page and then are cleared.
    * 
    * @param mixed $key
    * @param mixed $value
    */
    public function setPageVar($key,$value) {
        $_SESSION[$this->sessionStorage]['PAGE_VARS'][$key] = $value;
        return true;
    }

    /**     
    * Set a visit variable.  These persist for the duration of the current visit and then are cleared.
    * 
    * @param mixed $key
    * @param mixed $value
    */
    public function setVisitVar($key,$value) { 
        $_SESSION[$this->sessionStorage]['VISIT_VARS'][$key] = $value;
        return true;        
    }    
    
    /** 
    * Set a user variable.  These persist between sessions by use of cookies.
    * 
    * @param mixed $key
    * @param mixed $value
    */
    public function setUserVar($key,$value,$durationDays="30") { 
        $expires = (intval($durationDays) > 0) ? mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $durationDays, date("Y")) : 0;        
        $_SESSION[$this->sessionStorage]['NEW_USER_VARS'][$key] = array($value, $expires); //this is used to set the cookie at the next opportunity.
        $_SESSION[$this->sessionStorage]['USER_VARS'][$key] = $value; //this is the actual user var
        return true;        
    }
    
    /**
    * Unset a user variable and clear associated cookies.
    * 
    * @param mixed $key
    */
    public function unsetUserVar($key) {
        if (isset($_SESSION[$this->sessionStorage]['USER_VARS'][$key])) {
            unset($_SESSION[$this->sessionStorage]['USER_VARS'][$key]);
        }
        $_SESSION[$this->sessionStorage]['CLEAR_USER_VARS'][$key] = $key;    
    }
    
    /** 
    * Retrieve page varaible (only available on page it was set, and following page)
    * 
    * @param mixed $key
    */
    public function getPageVar($key) {
        $tempPageVars = array_merge($_SESSION[$this->sessionStorage]['PREV_PAGE_VARS'], $_SESSION[$this->sessionStorage]['PAGE_VARS']);
        if (isset($tempPageVars[$key])) {
            $var = $tempPageVars[$key];
            unset($tempPageVars);
            return $var;
        } else {
            return false;
        }
    }
    
    /** 
    * Unset a page variable if it exists.
    * 
    * @param mixed $key
    */
    public function unsetPageVar($key) {
        if (isset($_SESSION[$this->sessionStorage]['PREV_PAGE_VARS'][$key])) {
            unset($_SESSION[$this->sessionStorage]['PREV_PAGE_VARS'][$key]);
        }
        if (isset($_SESSION[$this->sessionStorage]['PAGE_VARS'][$key])) {
            unset($_SESSION[$this->sessionStorage]['PAGE_VARS'][$key]);
        }
        return true;
    }
        
    /**
    * Retrieve visit variable (available through existing session)
    * 
    * @param mixed $key
    */
    public function getVisitVar($key) {
        if (isset($_SESSION[$this->sessionStorage]['VISIT_VARS'][$key])) {
            return $_SESSION[$this->sessionStorage]['VISIT_VARS'][$key];
        } else {
            return false;
        }        
    }
    
    /**
    * Unset visit variable completely.
    * 
    * @param mixed $key
    */
    public function unsetVisitVar($key) {
        if (isset($_SESSION[$this->sessionStorage]['VISIT_VARS'][$key])) {
            unset($_SESSION[$this->sessionStorage]['VISIT_VARS'][$key]);
            return true;
        } else {
            return false;
        }           
    }
    
    /**
    * Retrieve user variable (available across sessions, in theory)
    * 
    * @param mixed $key
    */
    public function getUserVar($key) {
        if (isset($_SESSION[$this->sessionStorage]['USER_VARS'][$key])) {
            return $_SESSION[$this->sessionStorage]['USER_VARS'][$key];
        } else {
            return false;
        }               
    }
                
    /**
    * Regenerate 
    * 
    */
    public function regenerate() {
        session_regenerate_id(true);
        return true;
    }
    
    
    /**
    * Extend the class with your own login/logout functionality.
    * 
    */
    public function login() {
        
        $this->regenerate();
    }
    
    public function logout() {
        
        $this->destroy();
        $this->regenerate();
    }
    
    public function isLoggedIn() {
        
    }
        
    
        
}  
?>
