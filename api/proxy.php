<?php
include_once(__DIR__ . '/../config/symbbase.php');

$url = str_replace(' ','%20',$_REQUEST['url']);
$action = $_REQUEST['action'];
$sessionId = $_REQUEST['sessionid'];
$params = array_key_exists('params',$_REQUEST)?$_REQUEST['params']:'';

$pArr = array();

if(session_id() === $sessionId){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 90);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    if($action === 'post'){
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Content-Length: '.strlen(http_build_query($pArr))
        );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($pArr));
    }
    $result = curl_exec($curl);
    curl_close($curl);

    echo $result;
}