<?php
include_once(__DIR__ . '/../models/Collections.php');
include_once(__DIR__ . '/../models/Images.php');
include_once(__DIR__ . '/../models/Media.php');
include_once(__DIR__ . '/../models/Occurrences.php');
include_once(__DIR__ . '/../models/OccurrenceDeterminations.php');
include_once(__DIR__ . '/../models/OccurrenceMeasurementsOrFacts.php');
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

    public function clearOccurrenceUploadTables($collid, $optimizeTables): int
    {
        $retVal = 0;
        if($collid){
            $retVal = (new UploadDeterminationTemp)->clearCollectionData($collid);
            if($retVal === 0){
                $retVal = (new UploadMediaTemp)->clearCollectionData($collid);
            }
            if($retVal === 0){
                $retVal = (new UploadMofTemp)->clearCollectionData($collid);
            }
            if($retVal === 0){
                $retVal = (new UploadOccurrenceTemp)->clearCollectionData($collid, $optimizeTables);
            }
        }
        return $retVal;
    }

    public function executeCleaningAssociatedData($collid): int
    {
        $retVal = 0;
        if($collid){
            $retVal = (new UploadDeterminationTemp)->clearOrphanedRecords($collid);
            if($retVal === 0){
                $retVal = (new UploadMediaTemp)->clearOrphanedRecords($collid);
            }
            if($retVal === 0){
                $retVal = (new UploadMofTemp)->clearOrphanedRecords($collid);
            }
        }
        return $retVal;
    }

    public function executeCleaningScriptArr($collid, $cleaningScriptArr): int
    {
        $retVal = 0;
        if($collid && count($cleaningScriptArr) > 0){
            foreach($cleaningScriptArr as $scriptData){
                if($retVal === 0 && $scriptData){
                    $retVal = (new UploadOccurrenceTemp)->processCleaningScriptData($collid, $scriptData);
                }
            }
            if($retVal === 0){
                $retVal = (new UploadOccurrenceTemp)->removeOrphanedPoints($collid);
            }
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

    public function finalTransferAddNewMedia($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Images)->createImageRecordsFromUploadData($collid);
            if($retVal){
                $retVal = (new Media)->createMediaRecordsFromUploadData($collid);
            }
        }
        return $retVal;
    }

    public function finalTransferAddNewMof($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new OccurrenceMeasurementsOrFacts)->createOccurrenceMofRecordsFromUploadData($collid);
        }
        return $retVal;
    }

    public function finalTransferAddNewOccurrences($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Occurrences)->createOccurrenceRecordsFromUploadData($collid);
            if($retVal){
                (new Collections)->updateUploadDate($collid);
            }
        }
        return $retVal;
    }

    public function finalTransferCleanMediaRecords($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadMediaTemp)->cleanMediaRecords($collid);
        }
        return $retVal;
    }

    public function finalTransferClearExistingMediaNotInUpload($collid, $clearDerivatives): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Images)->clearExistingImagesNotInUpload($collid, $clearDerivatives);
            if($retVal){
                $retVal = (new Media)->clearExistingMediaNotInUpload($collid);
            }
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

    public function finalTransferClearPreviousMediaRecords($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Images)->deleteAssociatedImageRecords('collid', $collid);
            if($retVal){
                $retVal = (new Media)->deleteAssociatedMediaRecords('collid', $collid);
            }
        }
        return $retVal;
    }

    public function finalTransferClearPreviousMofRecords($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new OccurrenceMeasurementsOrFacts)->deleteOccurrenceMofRecords('collid', $collid);
        }
        return $retVal;
    }

    public function finalTransferClearPreviousMofRecordsForUpload($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new OccurrenceMeasurementsOrFacts)->deleteOccurrenceMofRecordsForUpload($collid);
        }
        return $retVal;
    }

    public function finalTransferPopulateMofIdentifiers($collid, $eventMofDataFields, $occurrenceMofDataFields): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadMofTemp)->populateMofIdentifiers($collid, $eventMofDataFields, $occurrenceMofDataFields);
        }
        return $retVal;
    }

    public function finalTransferRemoveDuplicateDbpkRecordsFromUpload($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadOccurrenceTemp)->removeDuplicateDbpkRecordsFromUpload($collid);
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

    public function finalTransferRemoveExistingMediaRecordsFromUpload($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadMediaTemp)->removeExistingMediaDataFromUpload($collid);
        }
        return $retVal;
    }

    public function finalTransferRemoveExistingMofRecordsFromUpload($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadMofTemp)->removeExistingMofDataFromUpload($collid);
        }
        return $retVal;
    }

    public function finalTransferRemoveUnmatchedOccurrences($collid): int
    {
        $retVal = 1;
        if($collid){
            $occidArr = (new Occurrences)->getOccidArrNotIncludedInUpload($collid);
            if($occidArr){
                (new Images)->deleteAssociatedImageRecords('occidArr', $occidArr);
                (new Media)->deleteAssociatedMediaRecords('occidArr', $occidArr);
                $retVal = (new Occurrences)->deleteOccurrenceRecord('occidArr', $occidArr);
            }
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

    public function finalTransferUpdateExistingOccurrences($collid, $mappedFields): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Occurrences)->updateOccurrenceRecordsFromUploadData($collid, $mappedFields);
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
            if(strncmp($table, 'upload', 6) === 0){
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

    public function linkExistingOccurrencesToUpload($collid, $updateAssociatedData, $matchByRecordId, $matchByCatalogNumber, $linkField): int
    {
        $retVal = 0;
        if($collid){
            if($matchByCatalogNumber){
                $retVal = (new UploadOccurrenceTemp)->linkUploadToExistingOccurrenceDataByCatalogNumber($collid, $linkField);
            }
            elseif($matchByRecordId){
                $retVal = (new UploadOccurrenceTemp)->linkUploadToExistingOccurrenceDataByRecordId($collid);
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
                    $recordsCreated += (new UploadOccurrenceTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['processingStatus'], $configArr['fieldMap'], $configArr['secondaryFieldMap']);
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
                $recordsCreated += (new UploadOccurrenceTemp)->batchCreateRecords($collid, $dataUploadArr, $configArr['processingStatus'], $configArr['fieldMap'], $configArr['secondaryFieldMap']);
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
        if(FileSystemService::fileExists($metaPath)){
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
                        $returnArr['occurrence']['dataFiles'] = array();
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
                    elseif(stripos($rowType,'image') || stripos($rowType,'audubon_core') || stripos($rowType,'multimedia')){
                        $tagName = 'multimedia';
                    }
                    elseif(stripos($rowType,'extendedmeasurementorfact')){
                        $tagName = 'extendedmeasurementorfact';
                    }
                    elseif(stripos($rowType,'measurementorfact')){
                        $tagName = 'measurementorfact';
                    }
                    elseif(stripos($rowType,'dnaderiveddata')){
                        $tagName = 'genetic';
                    }
                    if($tagName){
                        if($coreidElement = $extensionElement->getElementsByTagName('coreid')){
                            $extCoreId = $coreidElement->item(0)->getAttribute('index');
                            $returnArr[$tagName]['coreid'] = $extCoreId;
                        }
                        if($coreId === '' || $coreEventId || $coreId === $extCoreId){
                            if($locElements = $extensionElement->getElementsByTagName('location')){
                                $returnArr[$tagName]['filename'] = $locElements->item(0)->nodeValue;
                            }
                            $returnArr[$tagName]['encoding'] = $extensionElement->getAttribute('encoding');
                            $returnArr[$tagName]['fieldsTerminatedBy'] = $extensionElement->getAttribute('fieldsTerminatedBy');
                            $returnArr[$tagName]['linesTerminatedBy'] = $extensionElement->getAttribute('linesTerminatedBy');
                            $returnArr[$tagName]['fieldsEnclosedBy'] = $extensionElement->getAttribute('fieldsEnclosedBy');
                            $returnArr[$tagName]['ignoreHeaderLines'] = $extensionElement->getAttribute('ignoreHeaderLines');
                            $returnArr[$tagName]['rowType'] = $rowType;
                            $returnArr[$tagName]['dataFiles'] = array();
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

    public function processExternalDwcaTransfer($uploadType, $dwcaPath): array
    {
        $returnArr = array();
        $transferSuccess = false;
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($targetPath && $dwcaPath){
            $fileName = 'dwca.zip';
            $fullTargetPath = $targetPath . '/' . $fileName;
            if((int)$uploadType === 8 || (int)$uploadType === 11){
                $transferSuccess = FileSystemService::transferDwcaToLocalTarget($fullTargetPath, $dwcaPath);
            }
            elseif((int)$uploadType === 10){
                $transferSuccess = FileSystemService::transferSymbiotaDwcaToLocalTarget($fullTargetPath, $dwcaPath);
            }
            if($transferSuccess){
                $returnArr['targetPath'] = $targetPath;
                $returnArr['archivePath'] = $fullTargetPath;
            }
            else{
                FileSystemService::deleteDirectory($targetPath);
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
        if(!$this->validateDwcaFilenameArr($returnArr['files'], $targetPath)){
            FileSystemService::deleteDirectory($targetPath);
            $returnArr['files'] = array();
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
        return $recordsCreated;
    }

    public function processSourceDataMetaXmlFile($serverPath, $metaFile): array
    {
        $returnArr = array();
        if($metaFile && $serverPath && strpos($serverPath, $GLOBALS['SERVER_ROOT']) === 0){
            $metaPath = $serverPath . '/' . $metaFile;
            $returnArr = $this->processDwcaMetaFile($metaPath);
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
            fgetcsv($fh,0, ($fileInfo['fieldsTerminatedBy'] === '\t' ? "\t" : $fileInfo['fieldsTerminatedBy']), ($fileInfo['fieldsEnclosedBy'] === '' ? chr(0) : $fileInfo['fieldsEnclosedBy']), '');
        }
        while($dataArr = fgetcsv($fh,0, ($fileInfo['fieldsTerminatedBy'] === '\t' ? "\t" : $fileInfo['fieldsTerminatedBy']), ($fileInfo['fieldsEnclosedBy'] === '' ? chr(0) : $fileInfo['fieldsEnclosedBy']), '')){
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
        FileSystemService::deleteFile($serverPath . '/' . $fileInfo['filename'], true);
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
            if($retVal === 0){
                $retVal = (new UploadMediaTemp)->removeExistingOccurrenceDataFromUpload($collid);
            }
            if($retVal === 0){
                $retVal = (new UploadMofTemp)->removeExistingOccurrenceDataFromUpload($collid);
            }
            if($retVal === 0){
                $retVal = (new UploadOccurrenceTemp)->removeExistingOccurrenceDataFromUpload($collid);
            }
        }
        return $retVal;
    }

    public function removePrimaryIdentifiersFromUploadedOccurrences($collid): int
    {
        $retVal = 0;
        if($collid){
            $retVal = (new Occurrences)->removePrimaryIdentifiersFromUploadedOccurrences($collid);
        }
        return $retVal;
    }

    public function removeUploadFiles($serverPath): int
    {
        $returnVal = 0;
        if($serverPath && FileSystemService::deleteDirectory($serverPath)) {
            $returnVal = 1;
        }
        return $returnVal;
    }

    public function requestGbifDataDownload($predicateData): string
    {
        $requestData = json_encode(array(
            'creator' => $GLOBALS['GBIF_USERNAME'],
            'notificationAddresses' => array(
                $GLOBALS['ADMIN_EMAIL']
            ),
            'sendNotification' => false,
            'format' => 'DWCA',
            'predicate' => $predicateData,
            'verbatimExtensions' => array(
                'http://rs.gbif.org/terms/1.0/DNADerivedData',
                'http://rs.tdwg.org/dwc/terms/MeasurementOrFact',
                'http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact'
            )
        ));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.gbif.org/v1/occurrence/download/request');
        curl_setopt($curl, CURLOPT_USERPWD, ($GLOBALS['GBIF_USERNAME'] . ':' . $GLOBALS['GBIF_PASSWORD']));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'curl/8.7.1');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($requestData)
        ));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public function setUploadLocalitySecurity($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new UploadOccurrenceTemp)->setUploadLocalitySecurity($collid);
        }
        return $retVal;
    }

    public function uploadDwcaFile($dwcaFile): array
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
        if(!$this->validateDwcaFilenameArr($returnArr['files'], $targetPath)){
            FileSystemService::deleteDirectory($targetPath);
            $returnArr['files'] = array();
        }
        return $returnArr;
    }

    public function validateDwcaFilenameArr($arr, $dirPath, $subDir = null): bool
    {
        $returnVal = false;
        foreach($arr as $filename){
            if(!$subDir && strtolower($filename) === 'meta.xml'){
                $returnVal = true;
            }
            elseif(FileSystemService::isDirectory($dirPath . '/' . $filename) && (strtolower($filename) === 'dataset' || strtolower($filename) === 'verbatim')){
                $fileArr = FileSystemService::getDirectoryFilenameArr(($dirPath . '/' . $filename));
                $this->validateDwcaFilenameArr($fileArr, ($dirPath . '/' . $filename), true);
            }
            elseif(strtolower(substr($filename, -4)) !== '.csv' && strtolower(substr($filename, -4)) !== '.txt' && strtolower(substr($filename, -4)) !== '.xml'){
                if(FileSystemService::isDirectory($dirPath . '/' . $filename)){
                    FileSystemService::deleteDirectory($dirPath . '/' . $filename);
                }
                else{
                    FileSystemService::deleteFile($dirPath . '/' . $filename);
                }
            }
        }
        return $returnVal;
    }
}
