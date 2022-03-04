<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TPEditorManager.php');
include_once(__DIR__ . '/../../classes/TPImageEditorManager.php');

$tImageEditor = new TPImageEditorManager();
$tEditor = new TPEditorManager();

$editable = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $editable = true;
}

if($editable && isset($_FILES)){
    $_FILES['imgfile'] = $_FILES['files[]'];
    if($tImageEditor->loadImage($_POST)){
        $returnArr = array();
        $returnArr['files'] = array();
        $returnArr['files'][0]['name'] = 'jpeg_1617133394_web.jpg';
        $returnArr['files'][0]['error'] = $_REQUEST['title'];
        //$returnArr['files'][0]['thumbnailUrl'] = 'https://storage.idigbio.org/ny/mycology/01926/NY-F-01926523.jpg';
        $returnArr['files'][0]['jpeg_1617133394_web.jpg'] = true;
        echo json_encode($returnArr);
    }
    if($tEditor->getErrorStr()){
        $returnArr = array();
        $returnArr['files'] = array();
        $returnArr['files'][0]['name'] = 'jpeg_1617133394_web.jpg';
        $returnArr['files'][0]['error'] = $_REQUEST['title'];
        //$returnArr['files'][0]['thumbnailUrl'] = 'https://storage.idigbio.org/ny/mycology/01926/NY-F-01926523.jpg';
        $returnArr['files'][0]['jpeg_1617133394_web.jpg'] = true;
        echo json_encode($returnArr);
    }
}
