<?php
include_once(__DIR__ . '/../models/Images.php');
include_once(__DIR__ . '/../models/Media.php');
include_once(__DIR__ . '/../models/Occurrences.php');
include_once(__DIR__ . '/../models/OccurrenceDeterminations.php');
include_once(__DIR__ . '/../models/UploadDeterminationTemp.php');
include_once(__DIR__ . '/../models/UploadMediaTemp.php');
include_once(__DIR__ . '/../models/UploadMofTemp.php');
include_once(__DIR__ . '/../models/UploadOccurrenceTemp.php');
include_once(__DIR__ . '/DataDownloadService.php');
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

    public function cleanUploadCoordinates($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadOccurrenceTemp)->cleanUploadCoordinates($collid);
        }
        return $retVal;
    }

    public function cleanUploadCountryStateNames($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadOccurrenceTemp)->cleanUploadCountryStateNames($collid);
        }
        return $retVal;
    }

    public function cleanUploadEventDates($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadOccurrenceTemp)->cleanUploadEventDates($collid);
        }
        return $retVal;
    }

    public function cleanUploadTaxonomy($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadOccurrenceTemp)->cleanUploadTaxonomy($collid);
        }
        return $retVal;
    }

    public function clearOccurrenceUploadTables($collid): int
    {
        $retVal = 1;
        if($collid){
            if(!(new UploadDeterminationTemp)->clearCollectionData($collid)){
                $retVal = 0;
            }
            if(!(new UploadMediaTemp)->clearCollectionData($collid)){
                $retVal = 0;
            }
            if(!(new UploadMofTemp)->clearCollectionData($collid)){
                $retVal = 0;
            }
            if(!(new UploadOccurrenceTemp)->clearCollectionData($collid)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function executeCleaningScriptArr($collid, $cleaningScriptArr): int
    {
        $retVal = 1;
        if($collid && count($cleaningScriptArr) > 0){
            foreach($cleaningScriptArr as $scriptData){
                if($retVal === 1){
                    $retVal = (new UploadOccurrenceTemp)->processCleaningScriptData($collid, $scriptData);
                }
            }
            (new UploadOccurrenceTemp)->removeOrphanedPoints($collid);
        }
        return $retVal;
    }

    public function finalTransferAddNewDeterminations($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new OccurrenceDeterminations)->createOccurrenceDeterminationRecordsFromUploadData($collid);
        }
        return $retVal;
    }

    public function finalTransferAddNewOccurrences($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Occurrences)->createOccurrenceRecordsFromUploadData($collid);
        }
        return $retVal;
    }

    public function finalTransferClearPreviousDeterminations($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new OccurrenceDeterminations)->deleteOccurrenceDeterminationRecords('collid', $collid);
        }
        return $retVal;
    }

    public function finalTransferRemoveExistingDeterminationsFromUpload($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadDeterminationTemp)->removeExistingDeterminationDataFromUpload($collid);
        }
        return $retVal;
    }

    public function finalTransferRemoveUnmatchedOccurrences($collid): int
    {
        $retVal = 1;
        if($collid){
            $occidArr = (new Occurrences)->getOccidArrNotIncludedInUpload($collid);
            (new Images)->deleteOccurrenceImageFiles('occidArr', $occidArr);
            (new Media)->deleteOccurrenceMediaFiles('occidArr', $occidArr);
            $retVal = (new Occurrences)->deleteOccurrenceRecord('occidArr', $occidArr);
        }
        return $retVal;
    }

    public function finalTransferSetNewOccurrenceIds($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Occurrences)->createOccurrenceRecordsFromUploadData($collid);
        }
        return $retVal;
    }

    public function finalTransferUpdateExistingOccurrences($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Occurrences)->updateOccurrenceRecordsFromUploadData($collid);
        }
        return $retVal;
    }

    public function getUploadData($collid, $dataType, $index = null, $limit = null): array
    {
        $retArr = array();
        if($collid){
            if($dataType === 'exist'){
                $retArr = (new Occurrences)->getOccurrenceRecordsNotIncludedInUpload($collid, $index, $limit);
            }
            else{
                $retArr = (new UploadOccurrenceTemp)->getUploadData($collid, $dataType, $index, $limit);
            }
        }
        return $retArr;
    }

    public function getUploadedMofDataFields($collid): array
    {
        $retArr = array();
        if($collid){
            $retArr = (new UploadMofTemp)->getUploadedMofDataFields($collid);
        }
        return $retArr;
    }

    public function getUploadSummary($collid): array
    {
        $retArr = array();
        if($collid){
            $retArr = (new UploadOccurrenceTemp)->getUploadSummary($collid);
            $retArr['exist'] = (new Occurrences)->getOccurrenceCountNotIncludedInUpload($collid);
            $retArr['ident'] = (new UploadDeterminationTemp)->getUploadCount($collid);
            $retArr['media'] = (new UploadMediaTemp)->getUploadCount($collid);
            $retArr['mof'] = (new UploadMofTemp)->getUploadCount($collid);
        }
        return $retArr;
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

    public function linkExistingOccurrencesToUpload($collid, $updateAssociatedData, $matchByCatalogNumber, $linkField): int
    {
        $retVal = 0;
        if($collid){
            if($matchByCatalogNumber){
                $retVal = (new UploadOccurrenceTemp)->linkUploadToExistingOccurrenceDataByCatalogNumber($collid, $linkField);
            }
            else{
                $retVal = (new UploadOccurrenceTemp)->linkUploadToExistingOccurrenceData($collid);
            }
            if($retVal && $updateAssociatedData){
                (new UploadDeterminationTemp)->populateOccidFromUploadOccurrenceData($collid);
                (new UploadMediaTemp)->populateOccidFromUploadOccurrenceData($collid);
            }
        }
        return $retVal;
    }

    public function processDwcaFileDataUpload($collid, $configArr): int
    {
        $recordsCreated = 0;
        $recordIndex = 0;
        $dataUploadArr = array();
        $fh = fopen(($configArr['serverPath'] . '/' . $configArr['uploadFile']), 'rb');
        while($dataArr = fgetcsv($fh,0, ',', '"', '')){
            if($recordIndex === 5000){
                if($configArr['dataType'] === 'occurrence'){
                    $recordsCreated += (new UploadOccurrenceTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['processingStatus'], $configArr['fieldMap']);
                }
                elseif($configArr['dataType'] === 'determination'){
                    $recordsCreated += (new UploadDeterminationTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['fieldMap']);
                }
                elseif($configArr['dataType'] === 'multimedia'){
                    $recordsCreated += (new UploadMediaTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['fieldMap']);
                }
                elseif($configArr['dataType'] === 'mof'){
                    $recordsCreated += (new UploadMofTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['fieldMap'], $configArr['eventMofFields'], $configArr['occurrenceMofFields']);
                }
                $recordIndex = 0;
                $dataUploadArr = array();
            }
            $dataUploadArr[] = $dataArr;
            $recordIndex++;
        }
        fclose($fh);
        if(count($dataUploadArr) > 0){
            if($configArr['dataType'] === 'occurrence'){
                $recordsCreated += (new UploadOccurrenceTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['processingStatus'], $configArr['fieldMap']);
            }
            elseif($configArr['dataType'] === 'determination'){
                $recordsCreated += (new UploadDeterminationTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['fieldMap']);
            }
            elseif($configArr['dataType'] === 'multimedia'){
                $recordsCreated += (new UploadMediaTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['fieldMap']);
            }
            elseif($configArr['dataType'] === 'mof'){
                $recordsCreated += (new UploadMofTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['fieldMap'], $configArr['eventMofFields'], $configArr['occurrenceMofFields']);
            }
        }
        FileSystemService::deleteFile($configArr['serverPath'] . '/' . $configArr['uploadFile']);
        return $recordsCreated;
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
        $targetPath = FileSystemService::getTempDownloadUploadPath();
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
                $returnArr['targetPath'] = $targetPath;
                $returnArr['archivePath'] = $fullTargetPath;
            }
        }
        return $returnArr;
    }

    public function processExternalDwcaUnpack($targetPath, $archivePath): array
    {
        $returnArr = array();
        if($targetPath && $archivePath){
            FileSystemService::unpackZipArchive($targetPath, $archivePath);
            FileSystemService::deleteFile($archivePath);
            $returnArr['baseFolderPath'] = $targetPath;
            $returnArr['files'] = FileSystemService::getDirectoryFilenameArr($targetPath);
        }
        return $returnArr;
    }

    public function processFlatFileDataUpload($collid, $configArr, $data): int
    {
        $recordsCreated = 0;
        if($configArr['dataType'] === 'occurrence'){
            $recordsCreated += (new UploadOccurrenceTemp)->batchCreateRecords($collid, $data, $configArr['processingStatus']);
        }
        elseif($configArr['dataType'] === 'mof'){
            $recordsCreated += (new UploadMofTemp)->batchCreateRecords($collid, $data);
        }
        FileSystemService::deleteFile($configArr['serverPath'] . '/' . $configArr['uploadFile']);
        return $recordsCreated;
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
        $fileIndex = 1;
        $recordIndex = 0;
        $currentFilename = $prefix . '_' . $fileIndex . '.csv';
        $fh = fopen(($serverPath . '/' . $fileInfo['filename']), 'rb');
        $wh = fopen(($serverPath . '/' . $currentFilename), 'wb');
        if((int)$fileInfo['ignoreHeaderLines'] === 1) {
            fgetcsv($fh,0, $fileInfo['fieldsTerminatedBy'], $fileInfo['fieldsEnclosedBy'], '');
        }
        while($dataArr = fgetcsv($fh,0, $fileInfo['fieldsTerminatedBy'], $fileInfo['fieldsEnclosedBy'], '')){
            if($recordIndex === 10000){
                fclose($wh);
                $returnArr[] = $currentFilename;
                $fileIndex++;
                $currentFilename = $prefix . '_' . $fileIndex . '.csv';
                $wh = fopen(($serverPath . '/' . $currentFilename), 'wb');
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

    public function processUploadDataDownload($collid, $filename, $dataType): void
    {
        if($collid && $filename && $dataType){
            if($dataType === 'exist'){
                $sql = (new Occurrences)->getOccurrenceRecordsNotIncludedInUploadSql($collid);
            }
            else{
                $sql = (new UploadOccurrenceTemp)->getUploadDataSql($collid, $dataType);
            }
            (new DataDownloadService)->processCsvDownloadFromSql($sql, $filename);
        }
    }

    public function removeExistingOccurrencesFromUpload($collid): int
    {
        $retVal = 0;
        if($collid){
            $retVal = (new UploadDeterminationTemp)->removeExistingOccurrenceDataFromUpload($collid);
            if($retVal){
                $retVal = (new UploadMediaTemp)->removeExistingOccurrenceDataFromUpload($collid);
            }
            if($retVal){
                $retVal = (new UploadMofTemp)->removeExistingOccurrenceDataFromUpload($collid);
            }
            if($retVal){
                $retVal = (new UploadOccurrenceTemp)->removeExistingOccurrenceDataFromUpload($collid);
            }
        }
        return $retVal;
    }

    public function uploadDwcaFile($collid, $dwcaFile): array
    {
        $returnArr = array();
        $targetPath = FileSystemService::getTempDownloadUploadPath();
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
