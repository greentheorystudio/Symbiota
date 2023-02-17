<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/IRLManager.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$tId = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:null;

if($action){
    $irlManager = new IRLManager();
    if($action === 'getNativeStatus' && $tId){
        echo $irlManager->getNativeStatus($tId);
    }
}
