<?php
include_once(__DIR__ . '/../../config/symbini.php');
@include('Image/Barcode/Code39.php');
header('Content-type: image/png');

$bcText = array_key_exists('bctext',$_REQUEST)?$_REQUEST['bctext']:'';
$bcCode = array_key_exists('bccode',$_REQUEST)?$_REQUEST['bccode']:'Code39';
$imgType = array_key_exists('imgtype',$_REQUEST)?$_REQUEST['imgtype']:'png';
$bcHeight = array_key_exists('bcheight',$_REQUEST)?$_REQUEST['bcheight']:50;

if($bcText && class_exists('Image_Barcode')){
	$bcText = strtoupper($bcText);
	$bcObj = new Image_Barcode_Code39();
	$bc = $bcObj->draw($bcText, 'png', false, $bcHeight);
	imagepng($bc);
	imagedestroy($bc);
}
