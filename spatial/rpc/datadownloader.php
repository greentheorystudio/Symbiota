<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpatialModuleManager.php');
include_once(__DIR__ . '/../../classes/OccurrenceManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');
include_once(__DIR__ . '/../../classes/DwcArchiverCore.php');

$spatialManager = new SpatialModuleManager();
$dwcaHandler = new DwcArchiverCore();
$occManager = new OccurrenceManager();

$fileType = array_key_exists('dh-type',$_REQUEST)?$_REQUEST['dh-type']:'';
$fileName = array_key_exists('dh-filename',$_REQUEST)?$_REQUEST['dh-filename']:'';
$contentType = array_key_exists('dh-contentType',$_REQUEST)?$_REQUEST['dh-contentType']:'';
$selections = array_key_exists('dh-selections',$_REQUEST)?$_REQUEST['dh-selections']:'';
$schema = array_key_exists('schemacsv',$_REQUEST)?$_REQUEST['schemacsv']:'';
$identifications = array_key_exists('identificationscsv',$_REQUEST)?$_REQUEST['identificationscsv']:0;
$images = array_key_exists('imagescsv',$_REQUEST)?$_REQUEST['imagescsv']:0;
$rows = array_key_exists('dh-rows',$_REQUEST)?$_REQUEST['dh-rows']:0;
$format = array_key_exists('formatcsv',$_REQUEST)?$_REQUEST['formatcsv']:'';
$zip = array_key_exists('zipcsv',$_REQUEST)?$_REQUEST['zipcsv']:'';
$cset = array_key_exists('csetcsv',$_REQUEST)?$_REQUEST['csetcsv']:'';
$stArrJson = array_key_exists('starrjson',$_REQUEST)?$_REQUEST['starrjson']:'';

$jsonContent = '';

if($SOLR_MODE){
    $solrManager = new SOLRManager();
    $pArr = array();
    if(isset($_REQUEST['dh-q'])) {
        $pArr['q'] = $_REQUEST['dh-q'];
    }
    if(isset($_REQUEST['dh-fq'])) {
        $pArr['fq'] = $_REQUEST['dh-fq'];
    }
    if(isset($_REQUEST['dh-fl'])) {
        $pArr['fl'] = $_REQUEST['dh-fl'];
    }
    if($rows) {
        $pArr['rows'] = $rows;
    }
    $pArr['start'] = '0';
    $pArr['wt'] = 'geojson';
    $pArr['geojson.field'] = 'geo';
    $pArr['omitHeader'] = 'true';
    if($fileType !== 'zip' && $fileType !== 'csv' && $selections){
        $pArr['q'] = '(occid:('.$selections.'))';
        unset($pArr['fq']);
    }

    $pArr['q'] = $solrManager->checkQuerySecurity($pArr['q']);
    if(($fileType !== 'zip' && $fileType !== 'csv') || !$selections){
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Content-Length: '.strlen(http_build_query($pArr))
        );

        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $SOLR_URL.'/select',
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_POSTFIELDS => http_build_query($pArr),
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $jsonContent = curl_exec($ch);
        curl_close($ch);
    }
}
else if($stArrJson){
    $stArr = json_decode($stArrJson, true);
    $occManager->setSearchTermsArr($stArr);
    $spatialManager->setSearchTermsArr($stArr);
    if($selections){
        $mapWhere = 'o.occid IN('.$selections.') ';
    }
    else{
        $mapWhere = $occManager->getSqlWhere();
    }
    $spatialManager->setSqlWhere($mapWhere);
    if($fileType !== 'zip' && $fileType !== 'csv'){
        $jsonContent = $spatialManager->getOccPointDownloadGeoJson(0,$rows);
    }
}

if($fileType !== 'zip' && $fileType !== 'csv'){
    $fileContent = '';
    $fileName .= '.' . $fileType;
    if($fileType === 'geojson') {
        $fileContent = $jsonContent;
    }
    elseif($fileType === 'kml'){
        $fileContent = $spatialManager->writeKMLFromGeoJSON($jsonContent);
    }
    elseif($fileType === 'gpx'){
        $fileContent = $spatialManager->writeGPXFromGeoJSON($jsonContent);
    }

    header('Content-Type: '.$contentType);
    header('Content-Disposition: attachment; filename='.$fileName);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.strlen($fileContent));
    echo $fileContent;
}
else{
    $occStr = '';
    if($SOLR_MODE){
        if($selections){
            $occStr = $selections;
        }
        else{
            $occStr = $spatialManager->getOccStrFromGeoJSON($jsonContent);
        }
        $mapWhere = 'WHERE o.occid IN('.$occStr.') ';
    }
    $dwcaHandler->setCharSetOut($cset);
    $dwcaHandler->setSchemaType($schema);
    $dwcaHandler->setDelimiter($format);
    $dwcaHandler->setVerboseMode(0);
    $dwcaHandler->setRedactLocalities(0);
    $dwcaHandler->setCustomWhereSql($mapWhere);

    $outputFile = null;
    if($fileType === 'zip'){
        $dwcaHandler->setIncludeDets($identifications);
        $dwcaHandler->setIncludeImgs($images);
        $outputFile = $dwcaHandler->createDwcArchive('webreq');
    }
    else{
        $outputFile = $dwcaHandler->getOccurrenceFile();
    }

    $contentDesc = '';
    if($schema === 'dwc'){
        $contentDesc = 'Darwin Core ';
    }
    else{
        $contentDesc = 'Symbiota ';
    }
    $contentDesc .= 'Occurrence ';
    if($zip){
        $contentDesc .= 'Archive ';
    }
    $contentDesc .= 'File';
    header('Content-Description: '.$contentDesc);
    header('Content-Type: '.$contentType);
    header('Content-Disposition: attachment; filename='.basename($outputFile));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($outputFile));
    ob_clean();
    flush();
    readfile($outputFile);
    unlink($outputFile);
}
