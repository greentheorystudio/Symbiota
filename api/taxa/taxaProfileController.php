<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonProfileManager.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if($action){
    $tpManager = new TaxonProfileManager();
    if($action === 'setTaxon' && array_key_exists('taxonStr',$_POST) && array_key_exists('clid',$_POST)){
        echo json_encode($tpManager->setTaxon(htmlspecialchars($_POST['taxonStr']),htmlspecialchars($_POST['clid'])));
    }
}
