<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ApiException.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/UrlParameters.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataTables.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataFixtures.php';

// All basic API requests must have a valid method and action parameter.
$strMethod = UrlParameters::getParam('method');
$strAction = UrlParameters::getParam('action');

$strResource = sprintf('%s/%s', $strMethod, $strAction);

try {
    switch ($strResource)
    {
        case 'get/tablesByCompetition':    
            list($strStatement,$arrQueryParams) = DataTables::getTablesByCompetition();    
            break;
            
        case 'get/fixturesByCompetitionAndSeason':
            list($strStatement,$arrQueryParams) = DataFixtures::getFixturesByCompetitionAndSeason();        
            break;
            
        default:
            throw new ApiException("Unrecognised API request: $strResource");
            die;
    }
} catch (ApiException $objException) {
    $objException->gracefulError($objException->getCode());
    die;
}



try {
    $objDb = new PDO('mysql:host=localhost;dbname=office_league;charset=utf8', 'root', '', array(PDO::ERRMODE_EXCEPTION));
    $objQuery = $objDb->prepare($strStatement);
    $objQuery->execute($arrQueryParams);
    echo json_encode($objQuery->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $objException) {
    die ("PDO died badly: ".$objException->getMessage());
}
?>