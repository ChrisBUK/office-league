<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Page.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Session.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/OfficeLeagueApi.php';    

    //Page
    $objPage = new Page();
    $objPage->setSession(new Session(Config::SESSION_NAME));
    
    //What season are we in?
    $objApi = new OfficeLeagueApi();

    if (!empty($_GET['seasonId'])) {
        $intCurrentSeason = (int) $_GET['seasonId'];
    } else {
        $objData = json_decode($objApi->getJson('get/currentSeason', array('seasonTypeId'=>1)));
        $intCurrentSeason = $objData->seas_current_id;
    }

    //Get all tables for the season - TODO: write an api that gets all for a season.
    $objL1 = json_decode($objApi->getJson('get/tablesByCompetition', array('competitionId'=>1, 'seasonId'=>$intCurrentSeason)));
    $objL2 = json_decode($objApi->getJson('get/tablesByCompetition', array('competitionId'=>2, 'seasonId'=>$intCurrentSeason)));
    $objL3 = json_decode($objApi->getJson('get/tablesByCompetition', array('competitionId'=>3, 'seasonId'=>$intCurrentSeason)));
    $objL4 = json_decode($objApi->getJson('get/tablesByCompetition', array('competitionId'=>5, 'seasonId'=>$intCurrentSeason)));    
    
    //Get all fixtures for the season
    $objFix = json_decode($objApi->getJson('get/fixturesBySeason', array('seasonId'=>$intCurrentSeason)));

    //Render    
    echo $objPage->render(null,'siteHeader');
    echo $objPage->render(null, 'siteNav');
    
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class='t-page-h1'>SSDM Fifa League</h1>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class='visible-xs'>
                <h2 class='t-page-h2'>League Tables</h2>
            </div>
            <?php echo !empty($objL1->table) ? $objPage->render($objL1, 'smallTable') : ''?>
            <?php echo !empty($objL2->table) ? $objPage->render($objL2, 'smallTable') : ''?>
            <?php echo !empty($objL3->table) ? $objPage->render($objL3, 'smallTable') : ''?>
            <?php echo !empty($objL4->table) ? $objPage->render($objL4, 'smallTable') : ''?>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class='visible-xs'>
                <h2 class='t-page-h2'>Fixtures &amp; Results</h2>
            </div>
            <?php echo $objPage->render($objFix, 'smallFixture'); ?>
        </div>
    </div>
</div>        
<?php 
    echo $objPage->render(null,'siteFooter'); 
?>
