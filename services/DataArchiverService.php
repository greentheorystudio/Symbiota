<?php
include_once(__DIR__ . '/../models/Collections.php');
include_once(__DIR__ . '/../models/Configurations.php');
include_once(__DIR__ . '/../models/Occurrences.php');
include_once(__DIR__ . '/../models/Permissions.php');
include_once(__DIR__ . '/../models/TaxonHierarchy.php');
include_once(__DIR__ . '/DarwinCoreFieldDefinitionService.php');
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/SanitizerService.php');
include_once(__DIR__ . '/SearchService.php');
include_once(__DIR__ . '/UuidService.php');

class DataArchiverService {

    private ?mysqli $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function cleanMediaFileRow($row, $urlPathPrefix, $options): array
    {
        if(strncmp($row['identifier'], '/', 1) === 0) {
            $row['identifier'] = $urlPathPrefix . $row['identifier'];
        }
        if(strncmp($row['accessuri'], '/', 1) === 0) {
            $row['accessuri'] = $urlPathPrefix . $row['accessuri'];
        }
        if($row['thumbnailaccessuri'] && strncmp($row['thumbnailaccessuri'], '/', 1) === 0) {
            $row['thumbnailaccessuri'] = $urlPathPrefix . $row['thumbnailaccessuri'];
        }
        if($row['goodqualityaccessuri'] && strncmp($row['goodqualityaccessuri'], '/', 1) === 0) {
            $row['goodqualityaccessuri'] = $urlPathPrefix . $row['goodqualityaccessuri'];
        }
        if($options['schema'] !== 'backup'){
            if($row['rights'] && strncasecmp($row['rights'], 'http://creativecommons.org', 26) === 0){
                $row['webstatement'] = $row['rights'];
                $row['rights'] = '';
                if(!$row['usageterms']){
                    if($row['webstatement'] === 'http://creativecommons.org/publicdomain/zero/1.0/'){
                        $row['usageterms'] = 'CC0 1.0 (Public-domain)';
                    }
                    elseif($row['webstatement'] === 'http://creativecommons.org/licenses/by/3.0/'){
                        $row['usageterms'] = 'CC BY (Attribution)';
                    }
                    elseif($row['webstatement'] === 'http://creativecommons.org/licenses/by-sa/3.0/'){
                        $row['usageterms'] = 'CC BY-SA (Attribution-ShareAlike)';
                    }
                    elseif($row['webstatement'] === 'http://creativecommons.org/licenses/by-nc/3.0/'){
                        $row['usageterms'] = 'CC BY-NC (Attribution-Non-Commercial)';
                    }
                    elseif($row['webstatement'] === 'http://creativecommons.org/licenses/by-nc-sa/3.0/'){
                        $row['usageterms'] = 'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)';
                    }
                }
            }
            if(!$row['usageterms']) {
                $row['usageterms'] = 'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)';
            }
        }
        $row['associatedspecimenreference'] = ((array_key_exists('occid', $row) && $row['occid']) ? ($urlPathPrefix . '/collections/individual/index.php?occid=' . $row['occid']) : '');
        if(!$row['format']){
            if(stripos($row['accessuri'], '.jpg') !== false || stripos($row['accessuri'], '.jpeg') !== false){
                $row['format'] = 'image/jpeg';
            }
            elseif(stripos($row['accessuri'], '.png') !== false){
                $row['format'] = 'image/png';
            }
            elseif(stripos($row['accessuri'], '.zc') !== false){
                $row['format'] = 'application/zc';
            }
            elseif(stripos($row['accessuri'], '.mp4') !== false){
                $row['format'] = 'video/mp4';
            }
            elseif(stripos($row['accessuri'], '.webm') !== false){
                $row['format'] = 'video/webm';
            }
            elseif(stripos($row['accessuri'], '.ogg') !== false){
                $row['format'] = 'video/ogg';
            }
            elseif(stripos($row['accessuri'], '.wav') !== false){
                $row['format'] = 'audio/wav';
            }
            elseif(stripos($row['accessuri'], '.mp3') !== false){
                $row['format'] = 'audio/mpeg';
            }
            else{
                $row['format'] = '';
            }
        }
        if(!$row['type']){
            if($row['format'] === 'image/jpeg' || $row['format'] === 'image/png'){
                $row['type'] = 'StillImage';
            }
            elseif($row['format'] === 'application/zc'){
                $row['type'] = 'Zipkey';
            }
            elseif($row['format'] === 'video/mp4' || $row['format'] === 'video/webm' || $row['format'] === 'video/ogg'){
                $row['type'] = 'MovingImage';
            }
            elseif($row['format'] === 'audio/wav' || $row['format'] === 'audio/mpeg'){
                $row['type'] = 'Sound';
            }
            else{
                $row['type'] = '';
            }
        }
        $row['metadatalanguage'] = 'en';
        return $row;
    }

    public function createDeterminationFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options): ?bool
    {
        $outputFilename = 'identifications.csv';
        $dataIncluded = false;
        $outputPath = $targetPath . '/' . $outputFilename;
        $fileHandler = FileSystemService::openFileHandler($outputPath);
        if($fileHandler){
            $determinationFieldData = DarwinCoreFieldDefinitionService::getDeterminationArr();
            FileSystemService::writeRowToCsv($fileHandler, $this->getDataFileHeaders($determinationFieldData));
            $sql = 'SELECT DISTINCT o.collid,' . $this->getDataFileSqlSelect($determinationFieldData) . ',o.localitysecurity ';
            $sql .= $sqlFrom . 'LEFT JOIN omoccurdeterminations AS d ON o.occid = d.occid ' . $sqlWhere . ' AND d.occid IS NOT NULL ORDER BY c.collid ';
            if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
                while($row = $result->fetch_assoc()){
                    $rareSpReader = false;
                    $localitySecurity = (int)$row['localitysecurity'] === 1;
                    if($localitySecurity){
                        $rareSpReader = in_array((int)$row['collid'], $rareSpCollidAccessArr, true);
                    }
                    if(($localitySecurity && $rareSpReader) || !$localitySecurity || !$options['spatial']){
                        unset($row['localitysecurity'], $row['collid']);
                        FileSystemService::writeRowToCsv($fileHandler, $row);
                        $dataIncluded = true;
                    }
                }
                $result->free();
            }
            FileSystemService::closeFileHandler($fileHandler);
        }
        if(!$dataIncluded){
            FileSystemService::deleteFile($outputPath, true);
            $outputPath = '';
        }
        return $outputPath;
    }

    public function createDataArchive($collidArr, $searchTermsArr, $options): string
    {
        $archiveFilePath = '';
        $determinationFilePath = '';
        $mediaFilePath = '';
        $mofFilePath = '';
        $metaFilePath = '';
        $emlFilePath = '';
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        $rareSpCollidAccessArr = (new Permissions)->getUserRareSpCollidAccessArr();
        $archiveFilename = $options['filename'] . '.zip';
        $sqlWhereCriteria = (new SearchService)->prepareOccurrenceWhereSql($searchTermsArr);
        $sqlWhere = (new SearchService)->setWhereSql($sqlWhereCriteria, $options['schema']);
        $sqlFrom = (new SearchService)->setFromSql($options['schema']);
        $occurrenceFileData = $this->createOccurrenceFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options, true);
        $occurrenceFilePath = $occurrenceFileData['outputPath'];
        if($occurrenceFilePath){
            $collectionData = $occurrenceFileData['collectionData'];
            $archiveFilePath = $targetPath . '/' . $archiveFilename;
            $zipArchive = FileSystemService::initializeNewZipArchive();
            if(FileSystemService::createNewZipArchive($zipArchive, $archiveFilePath)){
                FileSystemService::addFileToZipArchive($zipArchive, $occurrenceFilePath);
                if($options['identifications']) {
                    $determinationFilePath = $this->createDeterminationFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options);
                    if($determinationFilePath){
                        FileSystemService::addFileToZipArchive($zipArchive, $determinationFilePath);
                    }
                }
                if($options['media']){
                    $mediaFilePath = $this->createMediaFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options, true);
                    if($mediaFilePath){
                        FileSystemService::addFileToZipArchive($zipArchive, $mediaFilePath);
                    }
                }
                if($options['mof']){
                    $mofFilePath = $this->createMofFile($collectionData, $rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options);
                    if($mofFilePath){
                        FileSystemService::addFileToZipArchive($zipArchive, $mofFilePath);
                    }
                }
                if($options['schema'] === 'dwc'){
                    $metaFilePath = $this->createMetaFile($targetPath, $options);
                    if($metaFilePath){
                        FileSystemService::addFileToZipArchive($zipArchive, $metaFilePath);
                    }
                    $emlFilePath = $this->createMetadataFile($targetPath, $collectionData);
                    if($emlFilePath){
                        FileSystemService::addFileToZipArchive($zipArchive, $emlFilePath);
                    }
                }
                FileSystemService::closeZipArchive($zipArchive);
                FileSystemService::deleteFile($occurrenceFilePath);
                if($determinationFilePath) {
                    FileSystemService::deleteFile($determinationFilePath);
                }
                if($mediaFilePath) {
                    FileSystemService::deleteFile($mediaFilePath);
                }
                if($mofFilePath) {
                    FileSystemService::deleteFile($mofFilePath);
                }
                if($metaFilePath) {
                    FileSystemService::deleteFile($metaFilePath);
                }
                if($emlFilePath) {
                    FileSystemService::deleteFile($emlFilePath);
                }
            }
        }
        return $archiveFilePath;
    }

    public function createMediaFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options, $archiveFile): string
    {
        $outputPath = '';
        $outputFilename = '';
        $dataIncluded = false;
        if($archiveFile){
            $outputFilename = 'multimedia.csv';
        }
        elseif(array_key_exists('filename', $options) && $options['filename']){
            $outputFilename = $options['filename'] . '.csv';
        }
        if($outputFilename){
            $outputPath = $targetPath . '/' . $outputFilename;
            $fileHandler = FileSystemService::openFileHandler($outputPath);
            if($fileHandler){
                $imageFieldData = DarwinCoreFieldDefinitionService::getImageArr();
                if(!$archiveFile){
                    $imageFieldData['coreid']['sql'] = 'i.imgid AS coreid';
                }
                $urlPathPrefix = SanitizerService::getFullUrlPathPrefix();
                FileSystemService::writeRowToCsv($fileHandler, $this->getDataFileHeaders($imageFieldData));
                $imageSql = 'SELECT DISTINCT o.collid,' . $this->getDataFileSqlSelect($imageFieldData) . ',o.localitysecurity ';
                if($archiveFile){
                    $imageSql .= $sqlFrom . 'LEFT JOIN images AS i ON o.occid = i.occid ' . $sqlWhere . ' AND i.occid IS NOT NULL ORDER BY c.collid ';
                }
                else{
                    $imageSql .= $sqlFrom . $sqlWhere . ' ';
                }
                if($result = $this->conn->query($imageSql,MYSQLI_USE_RESULT)){
                    while($row = $result->fetch_assoc()){
                        $rareSpReader = false;
                        $localitySecurity = (int)$row['localitysecurity'] === 1;
                        if($localitySecurity){
                            $rareSpReader = in_array((int)$row['collid'], $rareSpCollidAccessArr, true);
                        }
                        if(($localitySecurity && $rareSpReader) || !$localitySecurity){
                            $row = $this->cleanMediaFileRow($row, $urlPathPrefix, $options);
                            unset($row['localitysecurity'], $row['collid']);
                            FileSystemService::writeRowToCsv($fileHandler, $row);
                            $dataIncluded = true;
                        }
                    }
                    $result->free();
                }
                if($options['schema'] !== 'image'){
                    $mediaFieldData = DarwinCoreFieldDefinitionService::getMediaArr();
                    $mediaSql = 'SELECT DISTINCT o.collid,' . $this->getDataFileSqlSelect($mediaFieldData) . ',o.localitysecurity ';
                    $mediaSql .= $sqlFrom . 'LEFT JOIN media AS m ON o.occid = m.occid ' . $sqlWhere . ' AND m.occid IS NOT NULL ORDER BY c.collid ';
                    if($result = $this->conn->query($mediaSql,MYSQLI_USE_RESULT)){
                        while($row = $result->fetch_assoc()){
                            $rareSpReader = false;
                            $localitySecurity = (int)$row['localitysecurity'] === 1;
                            if($localitySecurity){
                                $rareSpReader = in_array((int)$row['collid'], $rareSpCollidAccessArr, true);
                            }
                            if(($localitySecurity && $rareSpReader) || !$localitySecurity){
                                $row = $this->cleanMediaFileRow($row, $urlPathPrefix, $options);
                                unset($row['localitysecurity'], $row['collid']);
                                FileSystemService::writeRowToCsv($fileHandler, $row);
                                $dataIncluded = true;
                            }
                        }
                        $result->free();
                    }
                }
                FileSystemService::closeFileHandler($fileHandler);
            }
            if(!$dataIncluded){
                FileSystemService::deleteFile($outputPath, true);
                $outputPath = '';
            }
        }
        return $outputPath;
    }

    public function createMetadataFile($targetPath, $collectionData): string
    {
        $outputFilename = 'metadata.xml';
        $outputPath = $targetPath . '/' . $outputFilename;
        $newDoc = $this->getMetadataDomDoc($collectionData);
        $newDoc->formatOutput = true;
        FileSystemService::saveDomDocument($newDoc, $outputPath);
        return $outputPath;
    }

    public function createMetaFile($targetPath, $options): string
    {
        $outputFilename = 'meta.xml';
        $outputPath = $targetPath . '/' . $outputFilename;
        $occurrenceFieldData = DarwinCoreFieldDefinitionService::getOccurrenceArr($options['schema']);
        $determinationFieldData = DarwinCoreFieldDefinitionService::getDeterminationArr();
        $imageFieldData = DarwinCoreFieldDefinitionService::getImageArr();
        $mofFieldData = DarwinCoreFieldDefinitionService::getMeasurementOrFactArr('occurrence');
        $newDoc = FileSystemService::initializeNewDomDocument();
        $newDoc->formatOutput = true;
        $rootElem = $newDoc->createElement('archive');
        $rootElem->setAttribute('metadata', 'metadata.xml');
        $rootElem->setAttribute('xmlns', 'http://rs.tdwg.org/dwc/text/');
        $rootElem->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $rootElem->setAttribute('xsi:schemaLocation', 'http://rs.tdwg.org/dwc/text/   http://rs.tdwg.org/dwc/text/tdwg_dwc_text.xsd');
        $newDoc->appendChild($rootElem);
        $coreElem = $newDoc->createElement('core');
        $coreElem->setAttribute('dateFormat', 'YYYY-MM-DD');
        $coreElem->setAttribute('encoding', 'UTF-8');
        $coreElem->setAttribute('fieldsTerminatedBy', ',');
        $coreElem->setAttribute('linesTerminatedBy', '\n');
        $coreElem->setAttribute('fieldsEnclosedBy', '"');
        $coreElem->setAttribute('ignoreHeaderLines', '1');
        $coreElem->setAttribute('rowType', 'http://rs.tdwg.org/dwc/terms/Occurrence');
        $filesElem = $newDoc->createElement('files');
        $filesElem->appendChild($newDoc->createElement('location', 'occurrences.csv'));
        $coreElem->appendChild($filesElem);
        $idElem = $newDoc->createElement('id');
        $idElem->setAttribute('index', '0');
        $coreElem->appendChild($idElem);
        $occCnt = 1;
        foreach($occurrenceFieldData as $data) {
            $fieldElem = $newDoc->createElement('field');
            $fieldElem->setAttribute('index', $occCnt);
            $fieldElem->setAttribute('term', $data['definition']);
            $coreElem->appendChild($fieldElem);
            $occCnt++;
        }
        $rootElem->appendChild($coreElem);
        if($options['identifications']) {
            $extElem1 = $newDoc->createElement('extension');
            $extElem1->setAttribute('encoding', 'UTF-8');
            $extElem1->setAttribute('fieldsTerminatedBy', ',');
            $extElem1->setAttribute('linesTerminatedBy', '\n');
            $extElem1->setAttribute('fieldsEnclosedBy', '"');
            $extElem1->setAttribute('ignoreHeaderLines', '1');
            $extElem1->setAttribute('rowType', 'http://rs.tdwg.org/dwc/terms/Identification');
            $filesElem1 = $newDoc->createElement('files');
            $filesElem1->appendChild($newDoc->createElement('location', 'identifications.csv'));
            $extElem1->appendChild($filesElem1);
            $coreIdElem1 = $newDoc->createElement('coreid');
            $coreIdElem1->setAttribute('index', '0');
            $extElem1->appendChild($coreIdElem1);
            $detCnt = 1;
            foreach($determinationFieldData as $data) {
                $fieldElem = $newDoc->createElement('field');
                $fieldElem->setAttribute('index', $detCnt);
                $fieldElem->setAttribute('term', $data['definition']);
                $extElem1->appendChild($fieldElem);
                $detCnt++;
            }
            $rootElem->appendChild($extElem1);
        }
        if($options['media']) {
            $extElem2 = $newDoc->createElement('extension');
            $extElem2->setAttribute('encoding', 'UTF-8');
            $extElem2->setAttribute('fieldsTerminatedBy', ',');
            $extElem2->setAttribute('linesTerminatedBy', '\n');
            $extElem2->setAttribute('fieldsEnclosedBy', '"');
            $extElem2->setAttribute('ignoreHeaderLines', '1');
            $extElem2->setAttribute('rowType', 'http://rs.tdwg.org/ac/terms/Multimedia');
            $filesElem2 = $newDoc->createElement('files');
            $filesElem2->appendChild($newDoc->createElement('location', 'multimedia.csv'));
            $extElem2->appendChild($filesElem2);
            $coreIdElem2 = $newDoc->createElement('coreid');
            $coreIdElem2->setAttribute('index', '0');
            $extElem2->appendChild($coreIdElem2);
            $imgCnt = 1;
            foreach($imageFieldData as $data) {
                $fieldElem = $newDoc->createElement('field');
                $fieldElem->setAttribute('index', $imgCnt);
                $fieldElem->setAttribute('term', $data['definition']);
                $extElem2->appendChild($fieldElem);
                $imgCnt++;
            }
            $rootElem->appendChild($extElem2);
        }
        if($options['mof']) {
            $extElem3 = $newDoc->createElement('extension');
            $extElem3->setAttribute('encoding', 'UTF-8');
            $extElem3->setAttribute('fieldsTerminatedBy', ',');
            $extElem3->setAttribute('linesTerminatedBy', '\n');
            $extElem3->setAttribute('fieldsEnclosedBy', '"');
            $extElem3->setAttribute('ignoreHeaderLines', '1');
            $extElem3->setAttribute('rowType', 'http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact');
            $filesElem3 = $newDoc->createElement('files');
            $filesElem3->appendChild($newDoc->createElement('location', 'measurementOrFact.csv'));
            $extElem3->appendChild($filesElem3);
            $coreIdElem3 = $newDoc->createElement('coreid');
            $coreIdElem3->setAttribute('index', '0');
            $extElem3->appendChild($coreIdElem3);
            $mofCnt = 1;
            foreach($mofFieldData as $data) {
                $fieldElem = $newDoc->createElement('field');
                $fieldElem->setAttribute('index', $mofCnt);
                $fieldElem->setAttribute('term', $data['definition']);
                $extElem3->appendChild($fieldElem);
                $mofCnt++;
            }
            $rootElem->appendChild($extElem3);
        }
        FileSystemService::saveDomDocument($newDoc, $outputPath);
        return $outputPath;
    }

    public function createMofFile($collectionData, $rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options): string
    {
        $outputFilename = 'measurementOrFact.csv';
        $dataIncluded = false;
        $outputPath = $targetPath . '/' . $outputFilename;
        $fileHandler = FileSystemService::openFileHandler($outputPath);
        if($fileHandler){
            $mofFieldDefData = DarwinCoreFieldDefinitionService::getMeasurementOrFactArr('occurrence');
            FileSystemService::writeRowToCsv($fileHandler, $this->getDataFileHeaders($mofFieldDefData));
            $sql = 'SELECT DISTINCT o.collid,' . $this->getDataFileSqlSelect($mofFieldDefData) . ',o.localitysecurity ';
            $occSql = $sql . $sqlFrom . 'LEFT JOIN ommofextension AS m ON o.occid = m.occid ' . $sqlWhere . ' AND m.occid IS NOT NULL ORDER BY c.collid ';
            if($result = $this->conn->query($occSql,MYSQLI_USE_RESULT)){
                while($row = $result->fetch_assoc()){
                    $rareSpReader = false;
                    $localitySecurity = (int)$row['localitysecurity'] === 1;
                    if($localitySecurity){
                        $rareSpReader = in_array((int)$row['collid'], $rareSpCollidAccessArr, true);
                    }
                    if(($localitySecurity && $rareSpReader) || !$localitySecurity){
                        if(array_key_exists($row['measurementtype'], $collectionData[$row['collid']]['configuredData']['occurrenceMofExtension']['dataFields'])){
                            $mofFieldData = $collectionData[$row['collid']]['configuredData']['occurrenceMofExtension']['dataFields'][$row['measurementtype']];
                            $row['measurementunit'] = (array_key_exists('measurementUnit', $mofFieldData) && $mofFieldData['measurementUnit'] !== '') ? $mofFieldData['measurementUnit'] : '';
                            $row['measurementaccuracy'] = (array_key_exists('measurementAccuracy', $mofFieldData) && $mofFieldData['measurementAccuracy'] !== '') ? $mofFieldData['measurementAccuracy'] : '';
                            $row['measurementmethod'] = (array_key_exists('measurementMethod', $mofFieldData) && $mofFieldData['measurementMethod'] !== '') ? $mofFieldData['measurementMethod'] : '';
                            $row['measurementremarks'] = (array_key_exists('label', $mofFieldData) && $mofFieldData['label'] !== '') ? $mofFieldData['label'] : '';
                        }
                        unset($row['localitysecurity'], $row['collid']);
                        FileSystemService::writeRowToCsv($fileHandler, $row);
                        $dataIncluded = true;
                    }
                }
                $result->free();
            }
            $eventSql = $sql . $sqlFrom . 'LEFT JOIN ommofextension AS m ON o.eventid = m.eventid ' . $sqlWhere . ' AND m.eventid IS NOT NULL ORDER BY c.collid ';
            if($result = $this->conn->query($eventSql,MYSQLI_USE_RESULT)){
                while($row = $result->fetch_assoc()){
                    $rareSpReader = false;
                    $dataIncluded = true;
                    $localitySecurity = (int)$row['localitysecurity'] === 1;
                    if($localitySecurity){
                        $rareSpReader = in_array((int)$row['collid'], $rareSpCollidAccessArr, true);
                    }
                    if(($localitySecurity && $rareSpReader) || !$localitySecurity){
                        if(!array_key_exists($row['collid'], $collectionData)){
                            $collectionData[$row['collid']] = (new Collections)->getCollectionInfoArr($row['collid']);
                        }
                        if(array_key_exists($row['measurementtype'], $collectionData[$row['collid']]['configuredData']['eventMofExtension']['dataFields'])){
                            $mofFieldData = $collectionData[$row['collid']]['configuredData']['eventMofExtension']['dataFields'][$row['measurementtype']];
                            $row['measurementunit'] = (array_key_exists('measurementUnit', $mofFieldData) && $mofFieldData['measurementUnit'] !== '') ? $mofFieldData['measurementUnit'] : '';
                            $row['measurementaccuracy'] = (array_key_exists('measurementAccuracy', $mofFieldData) && $mofFieldData['measurementAccuracy'] !== '') ? $mofFieldData['measurementAccuracy'] : '';
                            $row['measurementmethod'] = (array_key_exists('measurementMethod', $mofFieldData) && $mofFieldData['measurementMethod'] !== '') ? $mofFieldData['measurementMethod'] : '';
                            $row['measurementremarks'] = (array_key_exists('label', $mofFieldData) && $mofFieldData['label'] !== '') ? $mofFieldData['label'] : '';
                        }
                        unset($row['localitysecurity'], $row['collid']);
                        FileSystemService::writeRowToCsv($fileHandler, $row);
                    }
                }
                $result->free();
            }
            FileSystemService::closeFileHandler($fileHandler);
        }
        if(!$dataIncluded){
            FileSystemService::deleteFile($outputPath, true);
            $outputPath = '';
        }
        return $outputPath;
    }

    public function createOccurrenceFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options, $archiveFile): array
    {
        $returnArr = array();
        $returnArr['outputPath'] = '';
        $outputFilename = '';
        $dataIncluded = false;
        $returnArr['collectionData'] = array();
        if($archiveFile){
            $outputFilename = 'occurrences.csv';
        }
        elseif(array_key_exists('filename', $options) && $options['filename']){
            $outputFilename = $options['filename'] . '.csv';
        }
        if($outputFilename){
            $returnArr['outputPath'] = $targetPath . '/' . $outputFilename;
            $fileHandler = FileSystemService::openFileHandler($returnArr['outputPath']);
            if($fileHandler){
                $occurrenceFieldData = DarwinCoreFieldDefinitionService::getOccurrenceArr();
                $upperTaxonomyData = (new TaxonHierarchy)->getUpperTaxonomyData();
                $urlPathPrefix = SanitizerService::getFullUrlPathPrefix();
                FileSystemService::writeRowToCsv($fileHandler, $this->getDataFileHeaders($occurrenceFieldData));
                $sql = 'SELECT DISTINCT o.occid,o.collid,' . $this->getDataFileSqlSelect($occurrenceFieldData) . ',o.localitysecurity ' . $sqlFrom . ' ' . $sqlWhere . ' ORDER BY c.collid ';
                if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
                    while($row = $result->fetch_assoc()){
                        $rareSpReader = false;
                        $localitySecurity = (int)$row['localitysecurity'] === 1;
                        if($localitySecurity){
                            $rareSpReader = in_array((int)$row['collid'], $rareSpCollidAccessArr, true);
                        }
                        if(($localitySecurity && $rareSpReader) || !$localitySecurity || !$options['spatial']){
                            if(!array_key_exists($row['collid'], $returnArr['collectionData'])){
                                $returnArr['collectionData'][$row['collid']] = (new Collections)->getCollectionInfoArr($row['collid']);
                            }
                            if($localitySecurity && !$rareSpReader){
                                $row = (new Occurrences)->clearSensitiveOccurrenceData($row);
                            }
                            if($urlPathPrefix) {
                                $row['t_references'] = $urlPathPrefix . '/collections/individual/index.php?occid=' . $row['occid'];
                            }
                            $managementType = $returnArr['collectionData'][$row['collid']]['managementtype'];
                            if($managementType === 'Live Data' && array_key_exists('collectionid', $row) && !$row['collectionid']) {
                                $row['collectionid'] = $returnArr['collectionData'][$row['collid']]['collectionguid'];
                            }
                            if($upperTaxonomyData){
                                $lcSciName = $row['scientificname'] ? strtolower($row['scientificname']) : '';
                                $famStr = isset($row['family']) ? strtolower($row['family']) : '';
                                $ordStr = isset($upperTaxonomyData[$famStr]['o']) ? strtolower($upperTaxonomyData[$famStr]['o']) : '';
                                if(!$ordStr){
                                    $ordStr = $lcSciName;
                                }
                                $claStr = isset($upperTaxonomyData[$ordStr]['c']) ? strtolower($upperTaxonomyData[$ordStr]['c']) : '';
                                if(!$claStr){
                                    $claStr = isset($upperTaxonomyData[$lcSciName]['c']) ? strtolower($upperTaxonomyData[$lcSciName]['c']) : '';
                                }
                                $phyStr = isset($upperTaxonomyData[$claStr]['p']) ? strtolower($upperTaxonomyData[$claStr]['p']) : '';
                                if(!$phyStr){
                                    $phyStr = isset($upperTaxonomyData[$lcSciName]['p']) ? strtolower($upperTaxonomyData[$lcSciName]['p']) : '';
                                }
                                if($famStr && isset($upperTaxonomyData[$famStr]['o'])){
                                    $row['t_order'] = $upperTaxonomyData[$famStr]['o'];
                                }
                                elseif($ordStr && $claStr){
                                    $row['t_order'] = $row['scientificname'];
                                }
                                if($ordStr && isset($upperTaxonomyData[$ordStr]['c'])){
                                    $row['t_class'] = $upperTaxonomyData[$ordStr]['c'];
                                }
                                if($claStr && isset($upperTaxonomyData[$claStr]['p'])){
                                    $row['t_phylum'] = $upperTaxonomyData[$claStr]['p'];
                                }
                                if($phyStr && isset($upperTaxonomyData[$phyStr]['k'])){
                                    $row['t_kingdom'] = $upperTaxonomyData[$phyStr]['k'];
                                }
                            }
                            unset($row['occid'], $row['collid'], $row['localitysecurity']);
                            FileSystemService::writeRowToCsv($fileHandler, $row);
                            $dataIncluded = true;
                        }
                    }
                    $result->free();
                }
                FileSystemService::closeFileHandler($fileHandler);
            }
            if(!$dataIncluded){
                FileSystemService::deleteFile($returnArr['outputPath'], true);
                $returnArr['outputPath'] = '';
            }
        }
        return $returnArr;
    }

    public function getDataFileHeaders($fieldData): array
    {
        $returnArr = array();
        if(is_array($fieldData) && count($fieldData) > 0){
            foreach($fieldData as $data){
                $returnArr[] = $data['label'];
            }
        }
        return $returnArr;
    }

    public function getDataFileSqlSelect($fieldArr): string
    {
        $returnStrArr = array();
        foreach($fieldArr as $data){
            $returnStrArr[] = $data['sql'];
        }
        return implode(',', $returnStrArr);
    }

    public function getMetadataArr($collectionData): array
    {
        $emlArr = array();
        $urlPathPrefix = SanitizerService::getFullUrlPathPrefix();
        if(count($collectionData) === 1){
            $collId = key($collectionData);
            $cArr = $collectionData[$collId];
            $emlArr['alternateIdentifier'][] = $urlPathPrefix . '/collections/misc/collprofiles.php?collid=' . $collId;
            $emlArr['title'] = $cArr['collectionname'];
            $emlArr['description'] = $cArr['fulldescription'];
            $emlArr['contact']['individualName'] = $cArr['contact'];
            $emlArr['contact']['organizationName'] = $cArr['collectionname'];
            $emlArr['contact']['electronicMailAddress'] = $cArr['email'];
            $emlArr['contact']['onlineUrl'] = $cArr['homepage'];
            $emlArr['contact']['addr']['deliveryPoint'] = $cArr['address1'] . ($cArr['address2'] ? ', ' . $cArr['address2'] : '');
            $emlArr['contact']['addr']['city'] = $cArr['city'];
            $emlArr['contact']['addr']['administrativeArea'] = $cArr['stateprovince'];
            $emlArr['contact']['addr']['postalCode'] = $cArr['postalcode'];
            $emlArr['contact']['addr']['country'] = $cArr['country'];
            $emlArr['intellectualRights'] = $cArr['rights'];
        }
        else{
            $emlArr['title'] = $GLOBALS['DEFAULT_TITLE'] . ' general data extract';
        }
        if(isset($GLOBALS['USER_DISPLAY_NAME'])){
            $emlArr['associatedParty'][0]['individualName'] = $GLOBALS['USER_DISPLAY_NAME'];
            $emlArr['associatedParty'][0]['role'] = 'CONTENT_PROVIDER';
        }
        if($GLOBALS['PORTAL_GUID']){
            $emlArr['creator'][0]['attr']['id'] = $GLOBALS['PORTAL_GUID'];
        }
        $emlArr['creator'][0]['organizationName'] = $GLOBALS['DEFAULT_TITLE'];
        $emlArr['creator'][0]['electronicMailAddress'] = $GLOBALS['ADMIN_EMAIL'];
        $emlArr['creator'][0]['onlineUrl'] = $urlPathPrefix . '/index.php';
        $emlArr['metadataProvider'][0]['organizationName'] = $GLOBALS['DEFAULT_TITLE'];
        $emlArr['metadataProvider'][0]['electronicMailAddress'] = $GLOBALS['ADMIN_EMAIL'];
        $emlArr['metadataProvider'][0]['onlineUrl'] = $urlPathPrefix . '/index.php';
        $emlArr['pubDate'] = date('Y-m-d');
        $cnt = 1;
        foreach($collectionData as $id => $collArr){
            $emlArr['associatedParty'][$cnt]['organizationName'] = $collArr['collectionname'];
            $emlArr['associatedParty'][$cnt]['individualName'] = $collArr['contact'];
            $emlArr['associatedParty'][$cnt]['positionName'] = 'Collection Manager';
            $emlArr['associatedParty'][$cnt]['role'] = 'CONTENT_PROVIDER';
            $emlArr['associatedParty'][$cnt]['electronicMailAddress'] = $collArr['email'];
            if($collArr['stateprovince']){
                $emlArr['associatedParty'][$cnt]['address']['deliveryPoint'] = $collArr['address1'];
                if($collArr['address2']) {
                    $emlArr['associatedParty'][$cnt]['address']['deliveryPoint'] = $collArr['address2'];
                }
                $emlArr['associatedParty'][$cnt]['address']['city'] = $collArr['city'];
                $emlArr['associatedParty'][$cnt]['address']['administrativeArea'] = $collArr['stateprovince'];
                $emlArr['associatedParty'][$cnt]['address']['postalCode'] = $collArr['postalcode'];
                $emlArr['associatedParty'][$cnt]['address']['country'] = $collArr['country'];
            }
            $emlArr['collMetadata'][$cnt]['attr']['identifier'] = $collArr['collectionguid'];
            $emlArr['collMetadata'][$cnt]['attr']['id'] = $id;
            $emlArr['collMetadata'][$cnt]['alternateIdentifier'] = $urlPathPrefix . '/collections/misc/collprofiles.php?collid=' . $id;
            $emlArr['collMetadata'][$cnt]['parentCollectionIdentifier'] = $collArr['institutioncode'];
            $emlArr['collMetadata'][$cnt]['collectionIdentifier'] = $collArr['collectioncode'];
            $emlArr['collMetadata'][$cnt]['collectionName'] = $collArr['collectionname'];
            if($collArr['icon']){
                if(strncmp($collArr['icon'], '/', 1) === 0){
                    $collArr['icon'] = $urlPathPrefix . $collArr['icon'];
                }
                $emlArr['collMetadata'][$cnt]['resourceLogoUrl'] = $collArr['icon'];
            }
            $emlArr['collMetadata'][$cnt]['onlineUrl'] = $collArr['homepage'];
            $emlArr['collMetadata'][$cnt]['intellectualRights'] = $collArr['rights'];
            if($collArr['rightsholder']) {
                $emlArr['collMetadata'][$cnt]['additionalInfo'] = $collArr['rightsholder'];
            }
            if($collArr['usageterm']) {
                $emlArr['collMetadata'][$cnt]['additionalInfo'] = $collArr['usageterm'];
            }
            $emlArr['collMetadata'][$cnt]['abstract'] = $collArr['fulldescription'];
            $cnt++;
        }
        return $emlArr;
    }

    public function getMetadataDomDoc($collectionData): DOMDocument
    {
        $emlArr = $this->getMetadataArr($collectionData);
        $usageTermArr = Configurations::getRightsTermData($emlArr['collMetadata'][1]['intellectualRights']);
        $newDoc = FileSystemService::initializeNewDomDocument();
        $rootElem = $newDoc->createElement('eml:eml');
        $rootElem->setAttribute('xmlns:eml','https://eml.ecoinformatics.org/eml-2.2.0');
        $rootElem->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $rootElem->setAttribute('xsi:schemaLocation','https://eml.ecoinformatics.org/eml-2.2.0');
        $rootElem->setAttribute('packageId', UuidService::getUuidV4());
        $rootElem->setAttribute('system', SanitizerService::getFullUrlPathPrefix());
        $rootElem->setAttribute('scope','system');
        $rootElem->setAttribute('xml:lang','en');
        $newDoc->appendChild($rootElem);
        $datasetElem = $newDoc->createElement('dataset');
        $rootElem->appendChild($datasetElem);
        if(array_key_exists('alternateIdentifier',$emlArr)){
            foreach($emlArr['alternateIdentifier'] as $v){
                $altIdElem = $newDoc->createElement('alternateIdentifier');
                $altIdElem->appendChild($newDoc->createTextNode($v));
                $datasetElem->appendChild($altIdElem);
            }
        }
        if(array_key_exists('title',$emlArr)){
            $titleElem = $newDoc->createElement('title');
            $titleElem->setAttribute('xml:lang','eng');
            $titleElem->appendChild($newDoc->createTextNode($emlArr['title']));
            $datasetElem->appendChild($titleElem);
        }
        if(array_key_exists('creator',$emlArr)){
            $createArr = $emlArr['creator'];
            foreach($createArr as $childArr){
                $creatorElem = $newDoc->createElement('creator');
                if(isset($childArr['attr'])){
                    $attrArr = $childArr['attr'];
                    unset($childArr['attr']);
                    foreach($attrArr as $atKey => $atValue){
                        $creatorElem->setAttribute($atKey, ($atValue ?: ''));
                    }
                }
                foreach($childArr as $k => $v){
                    $newChildElem = $newDoc->createElement($k);
                    $newChildElem->appendChild($newDoc->createTextNode($v));
                    $creatorElem->appendChild($newChildElem);
                }
                $datasetElem->appendChild($creatorElem);
            }
        }
        if(array_key_exists('metadataProvider',$emlArr)){
            $mdArr = $emlArr['metadataProvider'];
            foreach($mdArr as $childArr){
                $mdElem = $newDoc->createElement('metadataProvider');
                foreach($childArr as $k => $v){
                    $newChildElem = $newDoc->createElement($k);
                    $newChildElem->appendChild($newDoc->createTextNode($v));
                    $mdElem->appendChild($newChildElem);
                }
                $datasetElem->appendChild($mdElem);
            }
        }
        if(array_key_exists('pubDate',$emlArr) && $emlArr['pubDate']){
            $pubElem = $newDoc->createElement('pubDate');
            $pubElem->appendChild($newDoc->createTextNode($emlArr['pubDate']));
            $datasetElem->appendChild($pubElem);
        }
        $langStr = 'eng';
        if(array_key_exists('language',$emlArr) && $emlArr) {
            $langStr = $emlArr['language'];
        }
        $langElem = $newDoc->createElement('language');
        $langElem->appendChild($newDoc->createTextNode($langStr));
        $datasetElem->appendChild($langElem);
        if(array_key_exists('description',$emlArr) && $emlArr['description']){
            $abstractElem = $newDoc->createElement('abstract');
            $paraElem = $newDoc->createElement('para');
            $paraElem->appendChild($newDoc->createTextNode($emlArr['description']));
            $abstractElem->appendChild($paraElem);
            $datasetElem->appendChild($abstractElem);
        }
        if(array_key_exists('contact',$emlArr)){
            $contactArr = $emlArr['contact'];
            $contactElem = $newDoc->createElement('contact');
            $addrArr = array();
            if(isset($contactArr['addr'])){
                $addrArr = $contactArr['addr'];
                unset($contactArr['addr']);
            }
            foreach($contactArr as $contactKey => $contactValue){
                $conElem = $newDoc->createElement($contactKey);
                $conElem->appendChild($newDoc->createTextNode(($contactValue ?: '')));
                $contactElem->appendChild($conElem);
            }
            if(isset($contactArr['addr'])){
                $addressElem = $newDoc->createElement('address');
                foreach($addrArr as $aKey => $aVal){
                    $childAddrElem = $newDoc->createElement($aKey);
                    $childAddrElem->appendChild($newDoc->createTextNode($aVal));
                    $addressElem->appendChild($childAddrElem);
                }
                $contactElem->appendChild($addressElem);
            }
            $datasetElem->appendChild($contactElem);
        }
        if(array_key_exists('associatedParty',$emlArr)){
            $associatedPartyArr = $emlArr['associatedParty'];
            foreach($associatedPartyArr as $assocArr){
                $assocElem = $newDoc->createElement('associatedParty');
                $addrArr = array();
                if(isset($assocArr['address'])){
                    $addrArr = $assocArr['address'];
                    unset($assocArr['address']);
                }
                foreach($assocArr as $aKey => $aArr){
                    $childAssocElem = $newDoc->createElement($aKey);
                    $childAssocElem->appendChild($newDoc->createTextNode(($aArr ?: '')));
                    $assocElem->appendChild($childAssocElem);
                }
                if($addrArr){
                    $addrElem = $newDoc->createElement('address');
                    foreach($addrArr as $addrKey => $addrValue){
                        $childAddrElem = $newDoc->createElement($addrKey);
                        $childAddrElem->appendChild($newDoc->createTextNode($addrValue));
                        $addrElem->appendChild($childAddrElem);
                    }
                    $assocElem->appendChild($addrElem);
                }
                $datasetElem->appendChild($assocElem);
            }
        }
        if(array_key_exists('intellectualRights',$emlArr)){
            $rightsElem = $newDoc->createElement('intellectualRights');
            $paraElem = $newDoc->createElement('para');
            $paraElem->appendChild($newDoc->createTextNode('To the extent possible under law, the publisher has waived all rights to these data and has dedicated them to the '));
            $ulinkElem = $newDoc->createElement('ulink');
            $citetitleElem = $newDoc->createElement('citetitle');
            $citetitleElem->appendChild($newDoc->createTextNode(array_key_exists('title', $usageTermArr) ? $usageTermArr['title'] : ''));
            $ulinkElem->appendChild($citetitleElem);
            $ulinkElem->setAttribute('url', (array_key_exists('url', $usageTermArr) ? $usageTermArr['url'] : $emlArr['intellectualRights']));
            $paraElem->appendChild($ulinkElem);
            $paraElem->appendChild($newDoc->createTextNode(array_key_exists('def',$usageTermArr) ? $usageTermArr['def'] : ''));
            $rightsElem->appendChild($paraElem);
            $datasetElem->appendChild($rightsElem);
        }
        $symbElem = $newDoc->createElement('biosurv');
        $dateElem = $newDoc->createElement('dateStamp');
        $dateElem->appendChild($newDoc->createTextNode(date('c')));
        $symbElem->appendChild($dateElem);
        $id = UuidService::getUuidV4();
        $citeElem = $newDoc->createElement('citation');
        $citeElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'] . ' - ' . $id));
        $citeElem->setAttribute('identifier', $id);
        $symbElem->appendChild($citeElem);
        $physicalElem = $newDoc->createElement('physical');
        $physicalElem->appendChild($newDoc->createElement('characterEncoding', 'UTF-8'));
        $dfElem = $newDoc->createElement('dataFormat');
        $edfElem = $newDoc->createElement('externallyDefinedFormat');
        $dfElem->appendChild($edfElem);
        $edfElem->appendChild($newDoc->createElement('formatName','Darwin Core Archive'));
        $physicalElem->appendChild($dfElem);
        $symbElem->appendChild($physicalElem);
        if(array_key_exists('collMetadata', $emlArr)){
            foreach($emlArr['collMetadata'] as $collArr){
                $collElem = $newDoc->createElement('collection');
                if(isset($collArr['attr']) && $collArr['attr']){
                    $attrArr = $collArr['attr'];
                    unset($collArr['attr']);
                    foreach($attrArr as $attrKey => $attrValue){
                        $collElem->setAttribute($attrKey, ($attrValue ?: ''));
                    }
                }
                $abstractStr = '';
                if(isset($collArr['abstract']) && $collArr['abstract']){
                    $abstractStr = $collArr['abstract'];
                    unset($collArr['abstract']);
                }
                foreach($collArr as $collKey => $collValue){
                    $collElem2 = $newDoc->createElement($collKey);
                    if($collValue){
                        $collElem2->appendChild($newDoc->createTextNode($collValue));
                    }
                    $collElem->appendChild($collElem2);
                }
                if($abstractStr){
                    $abstractElem = $newDoc->createElement('abstract');
                    $abstractElem2 = $newDoc->createElement('para');
                    $abstractElem2->appendChild($newDoc->createTextNode($abstractStr));
                    $abstractElem->appendChild($abstractElem2);
                    $collElem->appendChild($abstractElem);
                }
                $symbElem->appendChild($collElem);
            }
        }
        $metaElem = $newDoc->createElement('metadata');
        $metaElem->appendChild($symbElem);
        $addMetaElem = $newDoc->createElement('additionalMetadata');
        $addMetaElem->appendChild($metaElem);
        $rootElem->appendChild($addMetaElem);
        return $newDoc;
    }

    public function getMetadataDomDocContent($collid): bool|string
    {
        $collectionData = array();
        $collectionData[$collid] = (new Collections)->getCollectionInfoArr($collid);
        return $this->getMetadataDomDoc($collectionData)->saveXML();
    }
}
