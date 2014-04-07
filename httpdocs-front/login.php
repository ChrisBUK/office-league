<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Page.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Session.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/OfficeLeagueApi.php';    

            
    //Page
    $objPage = new Page();
    $objPage->setSession(new Session(Config::SESSION_NAME));
  
    //Logout if requested
    if (!empty($_GET['logout']))
    {
        $objPage->logout();
        header("location: /login");
        die;    
    }
  
    //Redirect if already logged in
    if ($objPage->isLoggedIn())
    {
        header("location: /season");
        die;
    }  
  
    //Check login if provided
    if (!empty($_POST['token']) && $objPage->checkCsrfToken($_POST['token']) && !empty($_POST['username']) && !empty($_POST['password']))
    {
        $arrAuthResult = json_decode($objApi->getJson('get/authToken', array('user'=>$_POST['username'], 'password'=>$_POST['password'])));           
        
        if (!empty($arrAuthResult->token)) {
            $objPage->login($arrAuthResult->userId, $arrAuthResult->apiToken);
        }
    }
  
    //Create form token for if needed
    $strToken = $objPage->createCsrfToken();
  
    //Render    
    echo $objPage->render(null,'siteHeader');
    echo $objPage->render(null, 'siteNav');
    
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-4" style='min-height: 643px; margin-top: 200px;'>
            <form role="form" method="post" action="">                            
                <input type="hidden" name="token" value="<?php echo $strToken;?>" />
                <h1><span class="glyphicon glyphicon-user"></span>&nbsp;Login</h1>
                <fieldset>
                    <div class="form-group">
                        <label for="username">Username or Email Address:</label>
                        <input type="text" class="form-control" id="username" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" placeholder="Password">
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>       
</div>        
<?php 
    echo $objPage->render(null,'siteFooter'); 
?>
