<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/OfficeLeagueApi.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractRenderer.php';

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
            
    //Get all fixtures for the season
    $objFix = json_decode($objApi->getJson('get/fixturesBySeason', array('seasonId'=>$intCurrentSeason)));
    //echo "<pre>";print_R($objFix);die;
    
    //Render    
    
    
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Office League</title>
        <link rel='stylesheet' type='text/css' href='/css/reset.css' />
        <link rel='stylesheet' type='text/css' href='/css/home.css' />
    </head>
    <body>
        <div class="wrap">
            <div class="pageTitle">
                <h1>SSDM Technical FIFA 14 League</h1>
            </div>
            <div class="col1">
                <div class="fixtureTableOuter">
                    <?php echo AbstractRenderer::render($objFix, 'smallFixture'); ?>
                </div>
            </div>
            <div class="col2">
                <?php echo AbstractRenderer::render($objL1, 'smallTable');?>
                <?php echo AbstractRenderer::render($objL2, 'smallTable');?>
                <?php echo AbstractRenderer::render($objL3, 'smallTable');?>
            </div>
        </div>
    </body>
</html>