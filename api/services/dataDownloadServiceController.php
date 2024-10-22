<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DwcArchiverCore.php');
include_once(__DIR__ . '/../../services/DataDownloadService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
include_once(__DIR__ . '/../../services/SearchService.php');

$filename = array_key_exists('filename', $_REQUEST) ? $_REQUEST['filename'] : null;
$contentType = array_key_exists('contentType', $_REQUEST) ? $_REQUEST['contentType'] : null;
$dataSchema = array_key_exists('dataSchema', $_REQUEST) ? $_REQUEST['dataSchema'] : null;
$includeDeterminations = array_key_exists('includeDeterminations', $_REQUEST) && (int)$_REQUEST['includeDeterminations'] === 1;
$includeImages = array_key_exists('includeImages', $_REQUEST) && (int)$_REQUEST['includeImages'] === 1;

$options = array_key_exists('options', $_REQUEST) ? json_decode($_POST['options'], true) : null;
$stArr = array_key_exists('starr', $_REQUEST) ? json_decode($_POST['starr'], true) : null;

if($options && $stArr && SanitizerService::validateInternalRequest()){
    $downloadService = new DataDownloadService();
    $searchService = new SearchService();

    $contentType = $downloadService->getContentTypeFromFileType($options['type']);
    if($contentType){
        if($options['type'] === 'geojson' || $options['type'] === 'gpx' || $options['type'] === 'kml'){
            $fileContent = '';
            $fileData = $searchService->processSearch($stArr, $options);
            if($options['type'] === 'geojson'){
                $fileContent = json_encode($fileData);
            }
            elseif($options['type'] === 'gpx'){
                $fileContent = $downloadService->writeGPXFromOccurrenceArr($fileData);
            }
            elseif($options['type'] === 'kml'){
                $fileContent = $downloadService->writeKMLFromOccurrenceArr($fileData);
            }
            header('Content-Type: ' . $contentType);
            header('Content-Disposition: attachment; filename=' . $options['filename']);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . strlen($fileContent));
            echo $fileContent;
        }
        else{
            $outputFile = null;
            $dwcaHandler = new DwcArchiverCore();
            $sqlWhereCriteria = $searchService->prepareOccurrenceWhereSql($stArr);
            $sqlWhere = $searchService->setWhereSql($sqlWhereCriteria, $options['schema'], $options['spatial']);
            $dwcaHandler->setCharSetOut('UTF-8');
            $dwcaHandler->setSchemaType($options['schema']);
            $dwcaHandler->setVerboseMode(0);
            $dwcaHandler->setRedactLocalities(0);
            $dwcaHandler->setCustomWhereSql($sqlWhere);
            $dwcaHandler->setIsPublicDownload();
            if($options['type'] === 'zip'){
                $dwcaHandler->setIncludeDets($options['identifications']);
                $dwcaHandler->setIncludeImgs($options['images']);
                $outputFile = $dwcaHandler->createDwcArchive('webreq');
            }
            else{
                $outputFile = $dwcaHandler->getOccurrenceFile();
            }
            header('Content-Type: ' . $contentType);
            header('Content-Disposition: attachment; filename=' . basename($outputFile));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($outputFile));
            flush();
            readfile($outputFile);
            unlink($outputFile);
        }
    }
}
