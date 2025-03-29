<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyAPIManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$queryString = $_REQUEST['term'];
$limit = array_key_exists('limit',$_REQUEST)?(int)$_REQUEST['limit']:0;

if(SanitizerService::validateInternalRequest()){
    $qHandler = new TaxonomyAPIManager();
    $listArr = array();

    if($queryString){
        $qHandler->setLimit($limit);

        $listArr = $qHandler->generateVernacularList($queryString);
        echo json_encode($listArr);
    }
}
