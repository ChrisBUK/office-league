<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/UrlParameters.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/AbstractData.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataTables.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/DataFixtures.php';

// All basic API requests must have a valid action and resource parameter.
$strMethod = UrlParameters::getParam('method');
$strAction = UrlParameters::getParam('action');

$strResource = sprintf('%s/%s', $strMethod, $strAction);

switch ($strResource)
{
    case 'get/tablesByCompetition':
        $arrRequired = array('competitionId', 'seasonId');        
        $bolHasRequired = AbstractData::hasRequiredParameters($arrRequired);
        
        if (!$bolHasRequired)
        {
            throw New Exception("$strResource requires ".join(',',$arrRequired));
        }
        
        $arrPassedVars = UrlParameters::getFullParamList($arrRequired);
        
        list($strStatement,$arrQueryParams) = DataTables::getTablesByCompetition($arrPassedVars);    
        break;
        
    case 'get/fixturesByCompetitionAndSeason':
        $arrRequired = array('competitionId', 'seasonId');        
        $bolHasRequired = AbstractData::hasRequiredParameters($arrRequired);

        if (!$bolHasRequired)
        {
            throw New Exception("$strResource requires ".join(',',$arrRequired));
        }

        $arrPassedVars = UrlParameters::getFullParamList($arrRequired);
        
        list($strStatement,$arrQueryParams) = DataFixtures::getFixturesByCompetitionAndSeason($arrPassedVars);        
        break;
        
    default:
        throw new Exception("Unrecognised API request: $strResource");
        die;
}

try {
    $objDb = new PDO('mysql:host=localhost;dbname=office_league;charset=utf8', 'root', '');
    $objQuery = $objDb->prepare($strStatement);
    $objQuery->execute($arrQueryParams);
    echo json_encode($objQuery->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $objException) {
    die ("PDO died badly: ".$objException->message);
}
?>