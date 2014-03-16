<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Parameters.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataTables.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataFixtures.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataTeams.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataSeasons.php';

// All basic API requests must have a valid method and action parameter.
$strMethod = Parameters::getParam('method', $_GET);
$strAction = Parameters::getParam('action', $_GET);

$strResource = sprintf('%s/%s', $strMethod, $strAction);

try {
    switch ($strResource)
    {
        case 'get/currentSeason':
            $objRequest = new DataSeasons();
            $strJson = $objRequest->getCurrentSeason($_GET);
            break;
                        
        case 'get/tablesByCompetition':    
            $objRequest = new DataTables();
            $strJson = $objRequest->getTablesByCompetition($_GET);
            break;
            
        case 'get/fixturesByCompetitionAndSeason':
            $objRequest = new DataFixtures();
            $strJson = $objRequest->getFixturesByCompetitionAndSeason($_GET);        
            break;

        case 'get/fixturesBySeason':
            $objRequest = new DataFixtures();
            $strJson = $objRequest->getFixturesBySeason($_GET);        
            break;
            
        case 'get/teamsByCompetition':
            $objRequest = new DataTeams();            
            $strJson = $objRequest->getTeamsInCompetition($_GET);        
            break;
            
        case 'create/newSeason':
            //Get existing data from the current season with this season_type_id to decide promotions/relegations
            $objOldSeason = new DataSeasons();
            $objOldSeason->closeSeason($_GET);
        
            //List all comps with this season_type_id
            /*
            $objData = new DataSeasons();
            $objData->getCompsBySeasonType();
                       
            //Generate a fixture list for each comp            
            $objRequest = new DataFixtures();
            $arrFixtures = $objRequest->createLeagueFixturesByCompetition($_GET, 'array');
            break;
            */
            
        case 'reset/season':
            $objData = new DataSeasons();
            $strJson = $objData->resetSeason($_GET);
            break;            
        
        // Register a game as having been played and add the result. This triggers league updates or cup draws as appropriate. 
        case 'update/result':
            $objData = new DataFixtures();
            $strJson = $objData->updateResult($_GET);
            break;
                    
        default:
            throw new ApiException("Unrecognised API request: $strResource");
            die;
    }
} catch (ApiException $objException) {
    $objException->gracefulError($objException->getCode());
    die;
}

header("Content-type: text/json");
die($strJson);
?>