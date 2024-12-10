<?php
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/SanitizerService.php');

class DataUploadService {

    private $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function getUploadTableFieldData($tableArr): array
    {
        $retArr = array();
        foreach($tableArr as $table){
            if(strpos($table, 'upload') === 0){
                $retArr[$table] = array();
                $sql = 'SHOW COLUMNS FROM ' . $table . ' ';
                //echo '<div>'.$sql.'</div>';
                if($result = $this->conn->query($sql)){
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                    $result->free();
                    foreach($rows as $rIndex => $row){
                        $retArr[$table][] = strtolower($row['Field']);
                        unset($rows[$rIndex]);
                    }
                }
            }
        }
        return $retArr;
    }

    public function processDwcaMetaFile($metaPath): array
    {
        $returnArr = array();
        $coreId = '';
        $coreEventId = '';
        if(file_exists($metaPath)){
            $metaDoc = new DOMDocument();
            $metaDoc->load($metaPath);
            if($coreElements = $metaDoc->getElementsByTagName('core')){
                foreach($coreElements as $coreElement){
                    $rowType = $coreElement->getAttribute('rowType');
                    if(stripos($rowType,'occurrence')){
                        if($idElement = $coreElement->getElementsByTagName('id')){
                            $coreId = $idElement->item(0)->getAttribute('index');
                            $returnArr['occurrence']['coreid'] = $coreId;
                        }
                        if($locElements = $coreElement->getElementsByTagName('location')){
                            $returnArr['occurrence']['filename'] = $locElements->item(0)->nodeValue;
                        }
                        $returnArr['occurrence']['encoding'] = $coreElement->getAttribute('encoding');
                        $returnArr['occurrence']['fieldsTerminatedBy'] = $coreElement->getAttribute('fieldsTerminatedBy');
                        $returnArr['occurrence']['linesTerminatedBy'] = $coreElement->getAttribute('linesTerminatedBy');
                        $returnArr['occurrence']['fieldsEnclosedBy'] = $coreElement->getAttribute('fieldsEnclosedBy');
                        $returnArr['occurrence']['ignoreHeaderLines'] = $coreElement->getAttribute('ignoreHeaderLines');
                        $returnArr['occurrence']['rowType'] = $rowType;
                        if($fieldElements = $coreElement->getElementsByTagName('field')){
                            foreach($fieldElements as $fieldElement){
                                $term = $fieldElement->getAttribute('term');
                                if(strpos($term,'/')) {
                                    $term = substr($term, strrpos($term, '/') + 1);
                                }
                                $returnArr['occurrence']['fields'][$fieldElement->getAttribute('index')] = $term;
                                if(strtolower($term) === 'eventid'){
                                    $coreEventId = $fieldElement->getAttribute('index');
                                    $returnArr['occurrence']['coreeventid'] = $coreEventId;
                                }
                            }
                        }
                        if($coreId !== ''){
                            $returnArr['occurrence']['fields'][$coreId] = 'coreid';
                        }
                        if($coreEventId){
                            $returnArr['occurrence']['fields'][$coreEventId] = 'coreeventid';
                        }
                    }
                }
            }
            if($returnArr){
                $extensionElements = $metaDoc->getElementsByTagName('extension');
                foreach($extensionElements as $extensionElement){
                    $extCoreId = '';
                    $extEventId = '';
                    $tagName = '';
                    $rowType = $extensionElement->getAttribute('rowType');
                    if(stripos($rowType,'identification')){
                        $tagName = 'identification';
                    }
                    elseif(stripos($rowType,'image') || stripos($rowType,'audubon_core') || stripos($rowType,'Multimedia')){
                        $tagName = 'multimedia';
                    }
                    elseif(stripos($rowType,'extendedmeasurementorfact')){
                        $tagName = 'measurementorfact';
                    }
                    if($coreidElement = $extensionElement->getElementsByTagName('coreid')){
                        $extCoreId = $coreidElement->item(0)->getAttribute('index');
                        $returnArr[$tagName]['coreid'] = $extCoreId;
                    }
                    if($coreId === '' || $coreEventId || $coreId === $extCoreId){
                        if($tagName){
                            if($locElements = $extensionElement->getElementsByTagName('location')){
                                $returnArr[$tagName]['filename'] = $locElements->item(0)->nodeValue;
                            }
                            $returnArr[$tagName]['encoding'] = $extensionElement->getAttribute('encoding');
                            $returnArr[$tagName]['fieldsTerminatedBy'] = $extensionElement->getAttribute('fieldsTerminatedBy');
                            $returnArr[$tagName]['linesTerminatedBy'] = $extensionElement->getAttribute('linesTerminatedBy');
                            $returnArr[$tagName]['fieldsEnclosedBy'] = $extensionElement->getAttribute('fieldsEnclosedBy');
                            $returnArr[$tagName]['ignoreHeaderLines'] = $extensionElement->getAttribute('ignoreHeaderLines');
                            $returnArr[$tagName]['rowType'] = $rowType;
                            if($fieldElements = $extensionElement->getElementsByTagName('field')){
                                foreach($fieldElements as $fieldElement){
                                    $term = $fieldElement->getAttribute('term');
                                    if(strpos($term,'/')) {
                                        $term = substr($term, strrpos($term, '/') + 1);
                                    }
                                    $index = $fieldElement->getAttribute('index');
                                    if(is_numeric($index)){
                                        $returnArr[$tagName]['fields'][$index] = $term;
                                    }
                                    if($tagName === 'measurementorfact'){
                                        if(!$extCoreId && strtolower($term) === 'occurrenceid'){
                                            $extCoreId = $index;
                                            $returnArr[$tagName]['coreid'] = $extCoreId;
                                        }
                                        elseif(strtolower($term) === 'eventid'){
                                            $extEventId = $index;
                                            $returnArr[$tagName]['coreeventid'] = $extEventId;
                                        }
                                    }
                                }
                            }
                            if($extCoreId !== ''){
                                $returnArr[$tagName]['fields'][$extCoreId] = 'coreid';
                            }
                            if($extEventId){
                                $returnArr[$tagName]['fields'][$extEventId] = 'coreeventid';
                            }
                        }
                    }
                }
            }
        }
        return $returnArr;
    }

    public function processExternalDwcaTransfer($collid, $uploadType, $dwcaPath): array
    {
        $returnArr = array();
        $transferSuccess = false;
        $targetPath = FileSystemService::getTempDwcaUploadPath($collid);
        if($targetPath && $dwcaPath){
            $fileName = 'dwca.zip';
            $fullTargetPath = $targetPath . '/' . $fileName;
            if((int)$uploadType === 8){
                $transferSuccess = FileSystemService::transferDwcaToLocalTarget($fullTargetPath, $dwcaPath);
            }
            elseif((int)$uploadType === 10){
                $transferSuccess = FileSystemService::transferSymbiotaDwcaToLocalTarget($fullTargetPath, $dwcaPath);
            }
            if($transferSuccess){
                FileSystemService::unpackZipArchive($targetPath, $fullTargetPath);
                FileSystemService::deleteFile($fullTargetPath);
                $returnArr['baseFolderPath'] = $targetPath;
                $returnArr['files'] = FileSystemService::getDirectoryFilenameArr($targetPath);
            }
        }
        return $returnArr;
    }

    public function processTransferredDwca($serverPath, $metaFile): array
    {
        $returnArr = array();
        if($metaFile && $serverPath && strpos($serverPath, $GLOBALS['SERVER_ROOT']) === 0){
            $metaPath = $serverPath . '/' . $metaFile;
            $returnArr = $this->processDwcaMetaFile($metaPath);
            if(array_key_exists('occurrence', $returnArr) && array_key_exists('filename', $returnArr['occurrence']) && $returnArr['occurrence']['filename']){
                $returnArr['occurrence']['dataFiles'] = $this->processTransferredDwcaFile($serverPath, 'occurrence', $returnArr['occurrence']);
                if(array_key_exists('identification', $returnArr) && array_key_exists('filename', $returnArr['identification']) && $returnArr['identification']['filename']){
                    $returnArr['identification']['dataFiles'] = $this->processTransferredDwcaFile($serverPath, 'identification', $returnArr['identification']);
                }
                if(array_key_exists('multimedia', $returnArr) && array_key_exists('filename', $returnArr['multimedia']) && $returnArr['multimedia']['filename']){
                    $returnArr['multimedia']['dataFiles'] = $this->processTransferredDwcaFile($serverPath, 'multimedia', $returnArr['multimedia']);
                }
                if(array_key_exists('measurementorfact', $returnArr) && array_key_exists('filename', $returnArr['measurementorfact']) && $returnArr['measurementorfact']['filename']){
                    $returnArr['measurementorfact']['dataFiles'] = $this->processTransferredDwcaFile($serverPath, 'measurementorfact', $returnArr['measurementorfact']);
                }
            }
        }
        return $returnArr;
    }

    public function processTransferredDwcaFile($serverPath, $prefix, $fileInfo): array
    {
        $returnArr = array();
        $headerArr = array();
        $fileIndex = 1;
        $recordIndex = 0;
        $currentFilename = $prefix . '_' . $fileIndex . '.csv';
        $fh = fopen(($serverPath . '/' . $fileInfo['filename']), 'rb');
        $wh = fopen(($serverPath . '/' . $currentFilename), 'wb');
        if((int)$fileInfo['ignoreHeaderLines'] === 1) {
            $headerArr = fgetcsv($fh,0, $fileInfo['fieldsTerminatedBy'], $fileInfo['fieldsEnclosedBy'], '');
            fputcsv($wh, $headerArr, ',', '"', '');
        }
        while($dataArr = fgetcsv($fh,0, $fileInfo['fieldsTerminatedBy'], $fileInfo['fieldsEnclosedBy'], '')){
            if($recordIndex === 100000){
                fclose($wh);
                $returnArr[] = $currentFilename;
                $fileIndex++;
                $currentFilename = $prefix . '_' . $fileIndex . '.csv';
                $wh = fopen(($serverPath . '/' . $currentFilename), 'wb');
                if($headerArr){
                    fputcsv($wh, $headerArr, ',', '"', '');
                }
                $recordIndex = 0;
            }
            fputcsv($wh, $dataArr, ',', '"', '');
            $recordIndex++;
        }
        fclose($wh);
        $returnArr[] = $currentFilename;
        fclose($fh);
        FileSystemService::deleteFile($serverPath . '/' . $fileInfo['filename']);
        return $returnArr;
    }

    public function uploadDwcaFile($collid, $dwcaFile): array
    {
        $returnArr = array();
        $targetPath = FileSystemService::getTempDwcaUploadPath($collid);
        if($targetPath && $dwcaFile['name'] && FileSystemService::moveUploadedFileToServer($dwcaFile, $targetPath, $dwcaFile['name'])) {
            $fullTargetPath = $targetPath . '/' . $dwcaFile['name'];
            FileSystemService::unpackZipArchive($targetPath, $fullTargetPath);
            FileSystemService::deleteFile($fullTargetPath);
            $returnArr['baseFolderPath'] = $targetPath;
            $returnArr['files'] = FileSystemService::getDirectoryFilenameArr($targetPath);
        }
        return $returnArr;
    }
}
