<?php

class ApiException extends Exception
{
    
    public function gracefulError($intHttpStatus, $strDetail='')
    {
        switch ($intHttpStatus) 
        {
            case 303: //See other
                header("HTTP/1.1 See Other", 303);
                $arrJson = array('error'=>'true', 'message'=>'Erm..');
                break;
            
            default:            
            case 400: //Bad request
                header("HTTP/1.1 Bad Request", 400);
                $arrJson = array('error'=>'true', 'message'=>self::getMessage());
                break;

            case 401: //Not authorized
                header("HTTP/1.1 Unauthorized", 401);
                $arrJson = array('error'=>'true', 'message'=>'This request requires a valid API key.');
                break;
            
            case 403: //Forbidden
                header("HTTP/1.1 Forbidden", 403);
                $arrJson = array('error'=>'true', 'message'=>'You are not permitted to make this request, ever.');
                break;
            
            case 405: //Method not allowed
                header("HTTP/1.1 Method Not Allowed", 405);
                $arrJson = array('error'=>'true', 'message'=>'This is not a valid resource.');
                break;
                
            case 406: //Not acceptable
                header("HTTP/1.1 Not Acceptable", 406);
                $arrJson = array('error'=>'true', 'message'=>'The resource does not comply with the accept-headers given.');
                break;
                
            case 410: //This method is no longer available
                header("HTTP/1.1 Gone", 410);
                $arrJson = array('error'=>'true', 'message'=>'This resource has gone away and will not be coming back.');
                break;
            
            case 413: //Too big
                header("HTTP/1.1 Request Entity Too Large", 413);
                $arrJson = array('error'=>'true', 'message'=>'This request would result in too large of a dataset. Please apply filters.');
                break;
            
            case 429: //Rate limit exceeded
                header("HTTP/1.1 Rate Limit Exceeded", 429);
                $arrJson = array('error'=>'true', 'message'=>'You have exceeded your rate limit, make fewer requests.');
                break;
            
            case 500: //Internal server error
                header("HTTP/1.1 Internal Server Error", 500);
                $arrJson = array('error'=>'true', 'message'=>'Something terrible happened.');
                break;
                
            case 503: //Service unavailable
                header("HTTP/1.1 Service Unavailable", 503);
                $arrJson = array('error'=>'true', 'message'=>'The service is presently unavailable, try later.');
                break;
        }
        
        echo json_encode($arrJson);
        die;
    }
    
}
?>
