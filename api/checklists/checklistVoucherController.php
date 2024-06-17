<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../models/ChecklistVouchers.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if($action && SanitizerService::validateInternalRequest()){
    $checklistVouchers = new ChecklistVouchers();

}
