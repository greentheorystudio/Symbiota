<?php
include_once(__DIR__ . '/../models/Collections.php');
include_once(__DIR__ . '/../models/OccurrenceMeasurementsOrFacts.php');
include_once(__DIR__ . '/../models/Occurrences.php');
include_once(__DIR__ . '/../models/Permissions.php');
include_once(__DIR__ . '/../models/TaxonHierarchy.php');
include_once(__DIR__ . '/DarwinCoreArchiverService.php');
include_once(__DIR__ . '/DarwinCoreFieldDefinitionService.php');
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/SearchService.php');

class DataDownloadService {

    private ?mysqli $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function getContentTypeFromFileType($fileType): string
    {
        $returnVal = '';
        if($fileType === 'csv'){
            $returnVal = 'text/csv; charset=UTF-8';
        }
        elseif($fileType === 'zip'){
            $returnVal = 'application/zip';
        }
        elseif($fileType === 'geojson'){
            $returnVal = 'application/vnd.geo+json';
        }
        elseif($fileType === 'kml'){
            $returnVal = 'application/vnd.google-earth.kml+xml';
        }
        elseif($fileType === 'gpx'){
            $returnVal = 'application/gpx+xml';
        }
        elseif($fileType === 'docx'){
            $returnVal = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        }
        elseif($fileType === 'fasta'){
            $returnVal = 'text/x-fasta';
        }
        return $returnVal;
    }

    public function getMofFieldArrFromCollectionData($collectionData): array
    {
        $eventFieldArr = array();
        $locationFieldArr = array();
        $occurrenceFieldArr = array();
        foreach($collectionData as $data) {
            if($data['configjson']){
                if(array_key_exists('eventMofExtension', $data['configjson']) && array_key_exists('dataFields', $data['configjson']['eventMofExtension']) && count($data['configjson']['eventMofExtension']['dataFields']) > 0){
                    foreach(array_keys($data['configjson']['eventMofExtension']['dataFields']) as $field) {
                        if(!in_array($field, $eventFieldArr, true) && !in_array($field, $locationFieldArr, true) && !in_array($field, $occurrenceFieldArr, true)){
                            $eventFieldArr[] = $field;
                        }
                    }
                }
                if(array_key_exists('locationMofExtension', $data['configjson']) && array_key_exists('dataFields', $data['configjson']['locationMofExtension']) && count($data['configjson']['locationMofExtension']['dataFields']) > 0){
                    foreach(array_keys($data['configjson']['locationMofExtension']['dataFields']) as $field) {
                        if(!in_array($field, $eventFieldArr, true) && !in_array($field, $locationFieldArr, true) && !in_array($field, $occurrenceFieldArr, true)){
                            $locationFieldArr[] = $field;
                        }
                    }
                }
                if(array_key_exists('occurrenceMofExtension', $data['configjson']) && array_key_exists('dataFields', $data['configjson']['occurrenceMofExtension']) && count($data['configjson']['occurrenceMofExtension']['dataFields']) > 0){
                    foreach(array_keys($data['configjson']['occurrenceMofExtension']['dataFields']) as $field) {
                        if(!in_array($field, $eventFieldArr, true) && !in_array($field, $locationFieldArr, true) && !in_array($field, $occurrenceFieldArr, true)){
                            $occurrenceFieldArr[] = $field;
                        }
                    }
                }
            }
        }
        return [...$occurrenceFieldArr, ...$eventFieldArr, ...$locationFieldArr];
    }

    public function getOccurrenceDownloadFileHeaders($fieldData): array
    {
        $returnArr = array();
        foreach($fieldData as $data){
            $returnArr[] = $data['label'];
        }
        return $returnArr;
    }

    public function getOccurrenceDownloadSqlSelectFields($fieldData, $schema): string
    {
        $returnStrArr = array();
        if($schema === 'dwc'){
            $returnStrArr[] = 'o.occid';
            $returnStrArr[] = 'o.collid';
            $returnStrArr[] = 'o.localitysecurity';
            foreach($fieldData as $data){
                $returnStrArr[] = $data['sql'];
            }
        }
        else{
            $returnStrArr = (new DbService)->getSqlFieldNameArrFromFieldData($fieldData, 'o');
        }
        return count($returnStrArr) > 0 ? implode(', ', $returnStrArr) : '';
    }

    public function initializeCSVDownloadFile($fileHandler, $fieldData, $mofFieldArr): void
    {
        $fileHeaderArr = $this->getOccurrenceDownloadFileHeaders($fieldData);
        if(count($mofFieldArr) > 0){
            $fileHeaderArr = [...$fileHeaderArr, ...$mofFieldArr];
        }
        FileSystemService::writeRowToCsv($fileHandler, $fileHeaderArr);
    }

    public function initializeGeoJSONDownloadFile($fileHandler): void
    {
        FileSystemService::writeTextToFile($fileHandler, ('{"type":"FeatureCollection","features":['));
    }

    public function initializeGPXDownloadFile($fileHandler): void
    {
        FileSystemService::writeTextToFile($fileHandler, '<gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ');
        FileSystemService::writeTextToFile($fileHandler, 'xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" version="1.1" creator="' . SanitizerService::getFullUrlPathPrefix() . '">');
    }

    public function initializeKMLDownloadFile($fileHandler): void
    {
        FileSystemService::writeTextToFile($fileHandler, '<kml xmlns="http://www.opengis.net/kml/2.2">');
        FileSystemService::writeTextToFile($fileHandler, '<Document>');
    }

    public function processCsvDownloadFromSql($sql, $filename): void
    {
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($sql && $filename && $targetPath){
            $fullPath = $targetPath . '/' . $filename;
            $fileHandler = FileSystemService::openFileHandler($fullPath);
            if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
                $headerRow = array();
                $fields = mysqli_fetch_fields($result);
                foreach($fields as $val){
                    $headerRow[] = $val->name;
                }
                FileSystemService::writeRowToCsv($fileHandler, $headerRow);
                while($row = $result->fetch_assoc()){
                    FileSystemService::writeRowToCsv($fileHandler, $row);
                }
                $result->free();
                FileSystemService::closeFileHandler($fileHandler);
                $outputType = $this->getContentTypeFromFileType('csv');
                $this->setDownloadHeaders($outputType, basename($fullPath), $fullPath);
                flush();
                readfile($fullPath);
                FileSystemService::deleteFile($fullPath, true);
            }
        }
    }

    public function processSearchDownload($searchTermsArr, $options): void
    {
        if($searchTermsArr && $options){
            $contentType = $this->getContentTypeFromFileType($options['type']);
            if($contentType){
                if($options['schema'] === 'image'){
                    $this->processSearchMediaDownload($searchTermsArr, $options, $contentType);
                }
                else{
                    $this->processSearchOccurrenceDownload($searchTermsArr, $options, $contentType);
                }
            }
        }
    }

    public function processSearchFastaDownload($occidArr, $options, $contentType): void
    {
        $fileData = (new SearchService)->processSearch(['occidArr' => $occidArr], $options);
        $fileName = $options['filename'] . '.' . $options['type'];
        $outputFile = $this->writeFASTAFromDataArr($fileName, $fileData);
        if($outputFile){
            $this->streamDownload($contentType, $outputFile);
        }
    }

    public function processSearchMediaDownload($searchTermsArr, $options, $contentType): void
    {
        $imgidArr = array();
        $loadingCount = 250000;
        $loadingIndex = 0;
        $options['numRows'] = $loadingCount;
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        do {
            $options['index'] = $loadingIndex;
            $newImgidArr = (new SearchService)->getSearchImgidArr($searchTermsArr, $options);
            $imgidArr = [...$imgidArr, ...$newImgidArr];
            $imgidLoadingComplete = count($newImgidArr) < $loadingCount;
            $loadingIndex++;
        }
        while(!$imgidLoadingComplete);
        if(count($imgidArr) > 0){
            $rareSpCollidAccessArr = (new Permissions)->getUserRareSpCollidAccessArr();
            $sqlWhereCriteria = (new SearchService)->prepareOccurrenceWhereSql(['imgidArr' => $imgidArr]);
            unset($imgidArr);
            $sqlWhere = (new SearchService)->setWhereSql($sqlWhereCriteria, $options['schema']);
            $sqlFrom = (new SearchService)->setFromSql($options['schema']);
            $outputFile = (new DarwinCoreArchiverService)->createMediaFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options, false);
            if($outputFile){
                $this->streamDownload($contentType, $outputFile);
            }
        }
    }

    public function processSearchOccurrenceDownload($searchTermsArr, $options, $contentType): void
    {
        $occidArr = array();
        $collidArr = array();
        $loadingCount = 250000;
        $loadingIndex = 0;
        $options['numRows'] = $loadingCount;
        do {
            $options['index'] = $loadingIndex;
            $newArr = (new SearchService)->getSearchOccidArr($searchTermsArr, $options, true);
            $occidArr = [...$occidArr, ...$newArr['occid']];
            $collidArr = [...$collidArr, ...$newArr['collid']];
            $occidLoadingComplete = count($newArr['occid']) < $loadingCount;
            $loadingIndex++;
        }
        while(!$occidLoadingComplete);
        if(count($occidArr) > 0){
            if($options['type'] === 'fasta'){
                $this->processSearchFastaDownload($occidArr, $options, $contentType);
            }
            else{
                $collidArr = array_unique($collidArr);
                if($options['type'] === 'zip'){
                    $outputFile = (new DarwinCoreArchiverService)->createDwcArchive($collidArr, ['occidArr' => $occidArr], $options);
                    if($outputFile){
                        $this->streamDownload($contentType, $outputFile);
                    }
                }
                else{
                    $this->processSearchOccurrenceFlatFileDownload($collidArr, $occidArr, $options, $contentType);
                }
            }
        }
    }

    public function processSearchOccurrenceFlatFileDownload($collidArr, $occidArr, $options, $contentType): void
    {
        $outputFile = '';
        $dataIncluded = false;
        $urlPathPrefix = '';
        $collectionData = array();
        $mofFieldArr = array();
        $upperTaxonomyData = array();
        $recIndex = 0;
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($targetPath && count($occidArr) > 0){
            $recCount = count($occidArr);
            if($options['schema'] === 'dwc' || $options['mof']){
                $collectionData = (new Collections)->getCollectionDataFromCollidArr($collidArr);
            }
            if($options['mof']){
                $mofFieldArr = $this->getMofFieldArrFromCollectionData($collectionData);
            }
            $rareSpCollidAccessArr = (new Permissions)->getUserRareSpCollidAccessArr();
            $fieldData = $options['schema'] === 'dwc' ? DarwinCoreFieldDefinitionService::getOccurrenceArr() : (new Occurrences)->getOccurrenceFields();
            $sqlFrom = (new SearchService)->setFromSql($options['schema']);
            $sqlSelectFields = $this->getOccurrenceDownloadSqlSelectFields($fieldData, $options['schema']);
            $fileName = $options['filename'] . '.' . $options['type'];
            $outputFile = $targetPath . '/' . $fileName;
            $fileHandler = FileSystemService::openFileHandler($outputFile);
            if($options['schema'] === 'dwc'){
                $upperTaxonomyData = (new TaxonHierarchy)->getUpperTaxonomyData();
                $urlPathPrefix = SanitizerService::getFullUrlPathPrefix();
            }
            if($options['type'] === 'geojson'){
                $this->initializeGeoJSONDownloadFile($fileHandler);
            }
            elseif($options['type'] === 'gpx'){
                $this->initializeGPXDownloadFile($fileHandler);
            }
            elseif($options['type'] === 'kml'){
                $this->initializeKMLDownloadFile($fileHandler);
            }
            else{
                $this->initializeCSVDownloadFile($fileHandler, $fieldData, $mofFieldArr);
            }
            do {
                $mofData = array();
                $batchOccidArr = array_splice($occidArr, 0, 50000);
                if(count($batchOccidArr) > 0){
                    $sqlWhereCriteria = (new SearchService)->prepareOccurrenceWhereSql(['occidArr' => $batchOccidArr]);
                    $sqlWhere = (new SearchService)->setWhereSql($sqlWhereCriteria, $options['schema']);
                    if($options['mof']){
                        $mofData = (new OccurrenceMeasurementsOrFacts)->getMofDataFromOccidArr($batchOccidArr);
                    }
                    $sql = 'SELECT DISTINCT ' . $sqlSelectFields . ' ' . $sqlFrom . ' ' . $sqlWhere . ' ORDER BY c.collid ';
                    if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
                        $fields = mysqli_fetch_fields($result);
                        while($row = $result->fetch_assoc()){
                            $recIndex++;
                            $rareSpReader = false;
                            $localitySecurity = (int)$row['localitysecurity'] === 1;
                            if($localitySecurity){
                                $rareSpReader = in_array((int)$row['collid'], $rareSpCollidAccessArr, true);
                            }
                            if(($localitySecurity && $rareSpReader) || !$localitySecurity || !$options['spatial']){
                                $dataIncluded = true;
                                if($localitySecurity && !$rareSpReader){
                                    $row = (new Occurrences)->clearSensitiveOccurrenceData($row);
                                }
                                if($options['type'] === 'gpx'){
                                    $this->writeGPXFromOccurrenceArr($fileHandler, $row);
                                }
                                else{
                                    if($options['schema'] === 'dwc'){
                                        $row = $this->setOccurrenceDwcData($row, $collectionData, $upperTaxonomyData, $urlPathPrefix);
                                        unset($row['occid'], $row['collid'], $row['localitysecurity']);
                                    }
                                    $row = $this->setFlatFileOutputData($fields, $row, $options['schema'], $fieldData, $mofFieldArr, $mofData);
                                    if($options['type'] === 'geojson'){
                                        $this->writeGeoJSONFromOccurrenceData($fileHandler, $row, $options['schema'], $recCount === $recIndex);
                                    }
                                    elseif($options['type'] === 'kml'){
                                        $this->writeKMLFromOccurrenceArr($fileHandler, $row, $options['schema']);
                                    }
                                    else{
                                        FileSystemService::writeRowToCsv($fileHandler, $row);
                                    }
                                }
                            }
                        }
                        $result->free();
                    }
                }
            }
            while(count($occidArr) > 0);
            if($dataIncluded){
                if($options['type'] === 'geojson'){
                    FileSystemService::writeTextToFile($fileHandler, ']}');
                }
                elseif($options['type'] === 'gpx'){
                    FileSystemService::writeTextToFile($fileHandler, '</gpx>');
                }
                elseif($options['type'] === 'kml'){
                    FileSystemService::writeTextToFile($fileHandler, '</Document></kml>');
                }
                FileSystemService::closeFileHandler($fileHandler);
            }
            else{
                FileSystemService::closeFileHandler($fileHandler);
                FileSystemService::deleteFile($outputFile);
                $outputFile = '';
            }
        }
        if($outputFile){
            $this->streamDownload($contentType, $outputFile);
        }
    }

    public function setDownloadHeaders($outputType, $filename, $content): void
    {
        header('Content-Type: ' . $outputType);
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($content));
    }

    public function setFlatFileOutputData($fields, $row, $schema, $fieldData, $mofFieldArr, $mofData): array
    {
        $returnArr = array();
        foreach($fields as $val){
            $name = $val->name;
            if($schema === 'dwc'){
                if($name !== 'occid' && $name !== 'collid' && $name !== 'localitysecurity'){
                    $fieldName = $name;
                    if(str_contains($fieldName, 't_')){
                        $fieldName = str_replace('t_', '', $fieldName);
                    }
                    $returnArr[$fieldData[$fieldName]['label']] = $row[$name];
                }
            }
            else{
                $returnArr[$fieldData[$name]['label']] = $row[$name];
            }
        }
        if(count($mofFieldArr) > 0){
            $occid = $schema === 'dwc' ? $row['providermanagedid'] : $row['occid'];
            foreach($mofFieldArr as $field){
                $value = '';
                if(array_key_exists($occid, $mofData['occurrence']) && array_key_exists($field, $mofData['occurrence'][$occid])){
                    $value = $mofData['occurrence'][$occid][$field];
                }
                elseif((int)$row['eventid'] > 0 && array_key_exists($row['eventid'], $mofData['event']) && array_key_exists($field, $mofData['event'][$row['eventid']])){
                    $value = $mofData['event'][$row['eventid']][$field];
                }
                elseif((int)$row['locationid'] > 0 && array_key_exists($row['locationid'], $mofData['location']) && array_key_exists($field, $mofData['location'][$row['locationid']])){
                    $value = $mofData['location'][$row['locationid']][$field];
                }
                $returnArr[$field] = $value;
            }
        }
        return $returnArr;
    }

    public function setOccurrenceDwcData($occurrenceData, $collectionData, $upperTaxonomyData, $urlPathPrefix): array
    {
        if($urlPathPrefix) {
            $occurrenceData['t_references'] = $urlPathPrefix . '/collections/individual/index.php?occid=' . $occurrenceData['occid'];
        }
        $managementType = $collectionData[$occurrenceData['collid']]['managementtype'];
        if($managementType === 'Live Data' && array_key_exists('collectionid', $occurrenceData) && !$occurrenceData['collectionid']) {
            $occurrenceData['collectionid'] = $collectionData[$occurrenceData['collid']]['collectionguid'];
        }
        if($upperTaxonomyData){
            $lcSciName = $occurrenceData['scientificname'] ? strtolower($occurrenceData['scientificname']) : '';
            $famStr = isset($occurrenceData['family']) ? strtolower($occurrenceData['family']) : '';
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
                $occurrenceData['t_order'] = $upperTaxonomyData[$famStr]['o'];
            }
            elseif($ordStr && $claStr){
                $occurrenceData['t_order'] = $occurrenceData['scientificname'];
            }
            if($ordStr && isset($upperTaxonomyData[$ordStr]['c'])){
                $occurrenceData['t_class'] = $upperTaxonomyData[$ordStr]['c'];
            }
            if($claStr && isset($upperTaxonomyData[$claStr]['p'])){
                $occurrenceData['t_phylum'] = $upperTaxonomyData[$claStr]['p'];
            }
            if($phyStr && isset($upperTaxonomyData[$phyStr]['k'])){
                $occurrenceData['t_kingdom'] = $upperTaxonomyData[$phyStr]['k'];
            }
        }
        return $occurrenceData;
    }

    public function streamChecklistDataArchive($archivePath): void
    {
        if(ob_get_level()){
            ob_end_clean();
        }
        header('Content-Type: application/zip');
        header('Content-Disposition: inline');
        header('Content-Length: ' . filesize($archivePath));
        readfile($archivePath);
        flush();
    }

    public function streamDownload($contentType, $outputFilePath, $keepFile = null): void
    {
        if(ob_get_level()){
            ob_end_clean();
        }
        $this->setDownloadHeaders($contentType, basename($outputFilePath), $outputFilePath);
        readfile($outputFilePath);
        flush();
        if(!$keepFile){
            FileSystemService::deleteFile($outputFilePath, true);
        }
    }

    public function writeFASTAFromDataArr($fileName, $dataArr): string
    {
        $fullPath = '';
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($fileName && $targetPath){
            $fullPath = $targetPath . '/' . $fileName;
            $fileHandler = FileSystemService::openFileHandler($fullPath);
            foreach($dataArr as $data){
                if($data['sourceidentifier'] && $data['description'] && $data['dnasequence']){
                    $seqStrArr = str_split($data['dnasequence'], 70);
                    FileSystemService::writeTextToFile($fileHandler, ('>' . $data['sourceidentifier'] . ' ' . $data['description'] . "\n"));
                    foreach($seqStrArr as $dnaStr){
                        FileSystemService::writeTextToFile($fileHandler, ($dnaStr . "\n"));
                    }
                    FileSystemService::writeTextToFile($fileHandler, "\n");
                }
            }
            FileSystemService::closeFileHandler($fileHandler);
        }
        return $fullPath;
    }

    public function writeGeoJSONFromOccurrenceData($fileHandler, $row, $schema, $final): void
    {
        if($row['decimalLongitude'] && $row['decimalLatitude']){
            $feature = array();
            $feature['type'] = 'Feature';
            $feature['geometry']['type'] = 'Point';
            $feature['geometry']['coordinates'] = [(float)$row['decimalLongitude'], (float)$row['decimalLatitude']];
            $feature['properties'] = array();
            $feature['properties']['id'] = $schema === 'dwc' ? $row['id'] : $row['occid'];
            foreach(array_keys($row) as $field){
                if($field !== 'footprintwkt' && $row[$field]){
                    $feature['properties'][$field] = $row[$field];
                }
            }
            FileSystemService::writeTextToFile($fileHandler, json_encode($feature));
            if(!$final){
                FileSystemService::writeTextToFile($fileHandler, ',');
            }
        }
    }

    public function writeGPXFromOccurrenceArr($fileHandler, $data): void
    {
        if($data['decimallongitude'] && $data['decimallatitude']){
            FileSystemService::writeTextToFile($fileHandler, ('<wpt lat="' . $data['decimallatitude'] . '" lon="' . $data['decimallongitude'] . '">'));
            FileSystemService::writeTextToFile($fileHandler, ('<name>' . ($data['recordedby'] ? htmlspecialchars($data['recordedby']) : '') . ' ' . ($data['recordnumber'] ? htmlspecialchars($data['recordnumber']) : '') . '</name>'));
            FileSystemService::writeTextToFile($fileHandler, ('<desc>' . ($data['sciname'] ? htmlspecialchars($data['sciname']) : '') . '</desc>'));
            FileSystemService::writeTextToFile($fileHandler, '</wpt>');
        }
    }

    public function writeKMLFromOccurrenceArr($fileHandler, $data, $schema): void
    {
        if($data['decimalLongitude'] && $data['decimalLatitude']){
            FileSystemService::writeTextToFile($fileHandler, '<Placemark>');
            FileSystemService::writeTextToFile($fileHandler, ('<name>' . ($data['recordedBy'] ? htmlspecialchars($data['recordedBy']) : '') . ' ' . ($data['recordNumber'] ? htmlspecialchars($data['recordNumber']) : '') . '</name>'));
            FileSystemService::writeTextToFile($fileHandler, '<ExtendedData>');
            foreach(array_keys($data) as $field) {
                FileSystemService::writeTextToFile($fileHandler, ('<Data name="' . $field . '">'));
                FileSystemService::writeTextToFile($fileHandler, ('<value>' . ($data[$field] ? htmlspecialchars($data[$field]) : '') . '</value>'));
                FileSystemService::writeTextToFile($fileHandler, '</Data>');
            }
            FileSystemService::writeTextToFile($fileHandler, '</ExtendedData>');
            FileSystemService::writeTextToFile($fileHandler, ('<Point><coordinates>' . (float)$data['decimalLongitude'] . ',' . (float)$data['decimalLatitude'] . '</coordinates></Point>'));
            FileSystemService::writeTextToFile($fileHandler, '</Placemark>');
        }
    }
}
