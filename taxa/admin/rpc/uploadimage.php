<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/TPEditorManager.php');
include_once(__DIR__ . '/../../../classes/TPImageEditorManager.php');

$tImageEditor = new TPImageEditorManager();
$tEditor = new TPEditorManager();

$editable = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $editable = true;
}

if($_FILES && $editable){
    $tImageEditor->loadImage($_POST);
    $errorMessage = $tEditor->getErrorStr();
    $returnArr = array();
    $returnArr['files'] = array();
    if($errorMessage){
        $returnArr['files'][0]['error'] = $errorMessage;
    }
    echo json_encode($returnArr);
}
