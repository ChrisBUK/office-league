<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/UrlParameters.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataTables.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataFixtures.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataTeams.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataSeasons.php';

// All basic API requests must have a valid method and action parameter.
$strMethod = UrlParameters::getParam('method');
$strAction = UrlParameters::getParam('action');

$strResource = sprintf('%s/%s', $strMethod, $strAction);

try {
    switch ($strResource)
    {
        case 'get/tablesByCompetition':    
            $objRequest = new DataTables();
            $strJson = $objRequest->getTablesByCompetition();
            break;
            
        case 'get/fixturesByCompetitionAndSeason':
            $objRequest = new DataFixtures();
            $strJson = $objRequest->getFixturesByCompetitionAndSeason();        
            break;
            
        case 'get/teamsByCompetition':
            $objRequest = new DataTeams();
            $strJson = $objRequest->getTeamsInCompetition();        
            break;
            
        case 'create/newSeason':
            $objRequest = new DataFixtures();
            $strJson = $objRequest->createFixturesByCompetition();
            break;
        
            
        default:
            throw new ApiException("Unrecognised API request: $strResource");
            die;
    }
} catch (ApiException $objException) {
    $objException->gracefulError($objException->getCode());
    die;
}

die($strJson);
?>