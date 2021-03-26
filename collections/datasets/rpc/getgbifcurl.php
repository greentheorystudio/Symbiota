<?php
include_once(__DIR__ . '/../../../config/symbini.php');

$type = array_key_exists('type',$_REQUEST)?$_REQUEST['type']:'';
$url = array_key_exists('url',$_REQUEST)?$_REQUEST['url']:'';
$data = array_key_exists('data',$_REQUEST)?$_REQUEST['data']:'';

$result = '';
$loginStr = $GLOBALS['GBIF_USERNAME'].':'.$GLOBALS['GBIF_PASSWORD'];

if($type && $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    if($data){
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $loginStr);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json')
    );

    $result = curl_exec($ch);
}

echo str_replace('"','',$result);
