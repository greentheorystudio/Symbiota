<?php
include_once(__DIR__ . '/../models/Collections.php');
include_once(__DIR__ . '/../models/Occurrences.php');
include_once(__DIR__ . '/../models/Permissions.php');
include_once(__DIR__ . '/../models/TaxonHierarchy.php');
include_once(__DIR__ . '/DarwinCoreFieldDefinitionService.php');
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/SanitizerService.php');
include_once(__DIR__ . '/SearchService.php');

class DarwinCoreArchiverService {

    private $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function createDwcArchive($targetPath, $searchTermsArr, $options): string
    {
        $rareSpCollidAccessArr = (new Permissions)->getUserRareSpCollidAccessArr();
        $archiveFilePath = '';
        $archiveFilename = $options['filename'] . '.zip';
        $sqlWhereCriteria = (new SearchService)->prepareOccurrenceWhereSql($searchTermsArr);
        $sqlWhere = (new SearchService)->setWhereSql($sqlWhereCriteria, $options['schema'], $options['spatial']);
        $sqlFrom = (new SearchService)->setFromSql($options['schema']);
        $sqlFrom .= ' ' . (new SearchService)->setTableJoinsSql($searchTermsArr);
        $status = $this->createOccurrenceFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options, true);
        if($status){
            $archiveFilePath = $targetPath . '/' . $archiveFilename;
            if(file_exists($archiveFilePath)) {
                unlink($archiveFilePath);
            }
            $zipArchive = new ZipArchive;
            $status = $zipArchive->open($archiveFilePath, ZipArchive::CREATE);
            if($status !== true){
                exit('FATAL ERROR: unable to create archive file: '.$status);
            }

            $zipArchive->addFile($targetPath.time().'-occur'.$this->fileExt);
            $zipArchive->renameName($targetPath.time().'-occur'.$this->fileExt,'occurrences'.$this->fileExt);
            if($this->includeDets) {
                $this->writeDeterminationFile();
                $zipArchive->addFile($targetPath.time().'-det'.$this->fileExt);
                $zipArchive->renameName($targetPath.time().'-det'.$this->fileExt,'identifications'.$this->fileExt);
            }
            if($this->includeImgs){
                $this->writeImageFile();
                $zipArchive->addFile($targetPath.time().'-images'.$this->fileExt);
                $zipArchive->renameName($targetPath.time().'-images'.$this->fileExt,'images'.$this->fileExt);
            }
            if($this->includeAttributes){
                $this->writeAttributeFile();
                $zipArchive->addFile($targetPath.time().'-attr'.$this->fileExt);
                $zipArchive->renameName($targetPath.time().'-attr'.$this->fileExt,'measurementOrFact'.$this->fileExt);
            }
            $this->writeMetaFile();
            $zipArchive->addFile($targetPath.time().'-meta.xml');
            $zipArchive->renameName($targetPath.time().'-meta.xml','meta.xml');
            $this->writeEmlFile();
            $zipArchive->addFile($targetPath.time().'-eml.xml');
            $zipArchive->renameName($targetPath.time().'-eml.xml','eml.xml');

            $zipArchive->close();
            unlink($targetPath.time().'-occur'.$this->fileExt);
            if($this->includeDets) {
                unlink($targetPath . time() . '-det' . $this->fileExt);
            }
            if($this->includeImgs) {
                unlink($targetPath . time() . '-images' . $this->fileExt);
            }
            if($this->includeAttributes) {
                unlink($targetPath . time() . '-attr' . $this->fileExt);
            }
            unlink($targetPath.time().'-meta.xml');
            if($this->schemaType === 'dwc'){
                rename($targetPath.time().'-eml.xml',$targetPath.str_replace('.zip','.eml',$archiveFilename));
            }
            else{
                unlink($targetPath.time().'-eml.xml');
            }
        }
        return $archiveFilePath;
    }

    public function createOccurrenceFile($rareSpCollidAccessArr, $sqlWhere, $sqlFrom, $targetPath, $options, $archiveFile): string
    {
        $outputPath = '';
        $outputFilename = '';
        $dataIncluded = false;
        $collectionData = array();
        if($archiveFile){
            $outputFilename = 'occurrences.csv';
        }
        elseif(array_key_exists('filename', $options) && $options['filename']){
            $outputFilename = $options['filename'] . '.csv';
        }
        if($outputFilename){
            $outputPath = $targetPath . '/' . $outputFilename;
            $fileHandler = FileSystemService::openFileHandler($outputPath);
            if($fileHandler){
                $occurrenceFieldData = DarwinCoreFieldDefinitionService::getOccurrenceArr($options['schema']);
                $upperTaxonomyData = (new TaxonHierarchy)->getUpperTaxonomyData();
                $urlPathPrefix = SanitizerService::getFullUrlPathPrefix();
                FileSystemService::writeRowToCsv($fileHandler, $this->getOccurrenceFileHeaders($occurrenceFieldData, $options['schema']));
                $sql = 'SELECT DISTINCT ' . $this->getOccurrenceFileSqlSelect($occurrenceFieldData['fields']) . ' ';
                $sql .= $sqlFrom . 'LEFT JOIN guidoccurrences AS g ON o.occid = g.occid ' . $sqlWhere . ' ORDER BY c.collid ';
                if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
                    while($row = $result->fetch_assoc()){
                        $rareSpReader = false;
                        $dataIncluded = true;
                        $localitySecurity = (int)$row['localitySecurity'] === 1;
                        if($localitySecurity){
                            $rareSpReader = in_array((int)$row['collid'], $rareSpCollidAccessArr, true);
                        }
                        if(($localitySecurity && $rareSpReader) || !$localitySecurity || !$options['spatial']){
                            if(!array_key_exists($row['collid'], $collectionData)){
                                $collectionData[$row['collid']] = (new Collections)->getCollectionInfoArr($row['collid']);
                            }
                            if(!$localitySecurity && !$rareSpReader){
                                $row = (new Occurrences)->clearSensitiveOccurrenceData($row);
                            }
                            if(!$row['occurrenceID']){
                                $guidTarget = $collectionData[$row['collid']]['guidtarget'];
                                if($guidTarget === 'catalogNumber'){
                                    $row['occurrenceID'] = $row['catalogNumber'];
                                }
                                elseif($guidTarget === 'symbiotaUUID'){
                                    $row['occurrenceID'] = $row['recordId'];
                                }
                            }
                            if($urlPathPrefix) {
                                $row['t_references'] = $urlPathPrefix . '/collections/individual/index.php?occid=' . $row['occid'];
                            }
                            $row['recordId'] = 'urn:uuid:' . $row['recordId'];
                            $managementType = $collectionData[$row['collid']]['managementtype'];
                            if($managementType === 'Live Data' && array_key_exists('collectionID', $row) && !$row['collectionID']) {
                                $guid = $collectionData[$row['collid']]['collectionguid'];
                                if(strlen($guid) === 36) {
                                    $guid = 'urn:uuid:' . $guid;
                                }
                                $row['collectionID'] = $guid;
                            }
                            if($options['schema'] === 'dwc'){
                                unset($row['localitySecurity'], $row['collid']);
                            }
                            elseif($options['schema'] === 'backup'){
                                unset($row['collid']);
                            }
                            if($upperTaxonomyData){
                                $lcSciName = $row['scientificName'] ? strtolower($row['scientificName']) : '';
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
                                    $row['t_order'] = $row['scientificName'];
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
        }
        return $outputPath;
    }

    public function getOccurrenceFileHeaders($occurrenceFieldData, $schemaType): array
    {
        $returnArr = array();
        if(is_array($occurrenceFieldData) && array_key_exists('fields', $occurrenceFieldData)){
            $fieldArr = $occurrenceFieldData['fields'];
            if($schemaType === 'dwc'){
                unset($fieldArr['localitySecurity']);
            }
            if($schemaType === 'dwc' || $schemaType === 'backup'){
                unset($fieldArr['collId']);
            }
            $returnArr = array_keys($fieldArr);
        }
        return $returnArr;
    }

    public function getOccurrenceFileSqlSelect($fieldArr): string
    {
        $returnStrArr = array();
        foreach($fieldArr as $fieldName => $sql){
            if($sql){
                $returnStrArr[] = $sql;
            }
            else{
                $returnStrArr[] = '"" AS t_' . $fieldName;
            }
        }
        return implode(',', $returnStrArr);
    }
}
