<?php
/*
 * * ****  Accepts  ********************************************
 *
 * POST or GET requests
 *
 * ****  Input Variables  ********************************************
 *
 * term: User inputted string for which to auto-complete.
 * hideauth (optional): Hide authority from label of scientific name reutrn.
 * hideprotected (optional): Hide taxa flagged as sensitive from scientific name return.
 * taid (optional): Taxonomic thesaurus ID from which to narrow scientific name return.
 * rlimit (optional): Rank ID for which to limit scientific name return.
 * rlow (optional): Rank ID for which to be lowest limit for scientific name return. Taxa will be higher or equal to this rank.
 * rhigh (optional): Rank ID for which to be highest limit for scientific name return. Taxa will be lower or equal to this rank.
 * limit (optional): Sets number of scientific names returned.
 *
 * * ****  Output  ********************************************
 *
 * JSON array of scientific names.
 *
 * Each scientific name contains a subarray with:
 *  id: Symbiota portal TID for that taxon.
 *  value: Suggested label for taxon (currently set to be same as index).
 *  author: Authority for taxon.
 *
 */

include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/TaxonomyAPIManager.php');

$queryString = $_REQUEST['term'];
$hideAuth = array_key_exists('hideauth',$_REQUEST)?$_REQUEST['hideauth']:false;
$hideProtected = array_key_exists('hideprotected',$_REQUEST)?$_REQUEST['hideprotected']:false;
$taxAuthId = array_key_exists('taid',$_REQUEST)?(int)$_REQUEST['taid']:0;
$rankLimit = array_key_exists('rlimit',$_REQUEST)?(int)$_REQUEST['rlimit']:0;
$rankLow = array_key_exists('rlow',$_REQUEST)?(int)$_REQUEST['rlow']:0;
$rankHigh = array_key_exists('rhigh',$_REQUEST)?(int)$_REQUEST['rhigh']:0;
$limit = array_key_exists('limit',$_REQUEST)?(int)$_REQUEST['limit']:0;

$qHandler = new TaxonomyAPIManager();
$listArr = array();

if($queryString){
    $qHandler->setHideAuth($hideAuth);
    $qHandler->setHideProtected($hideProtected);
    $qHandler->setTaxAuthId($taxAuthId);
    $qHandler->setRankLimit($rankLimit);
    $qHandler->setRankLow($rankLow);
    $qHandler->setRankHigh($rankHigh);
    $qHandler->setLimit($limit);

    $listArr = $qHandler->generateSciNameList($queryString);
    echo json_encode($listArr, JSON_THROW_ON_ERROR);
}
