<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/services/DarwinCoreArchiverService.php');
header('Content-Type: text/xml; charset=utf-8');

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;

if($collid > 0){
    echo (new DarwinCoreArchiverService)->getEmlDomDocContent($collid);
}
