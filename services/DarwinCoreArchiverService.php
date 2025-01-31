<?php
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
        $archiveFilePath = '';
        $archiveFilename = $options['filename'] . '.zip';
        $sqlWhereCriteria = (new SearchService)->prepareOccurrenceWhereSql($searchTermsArr);
        $sqlWhere = (new SearchService)->setWhereSql($sqlWhereCriteria, $options['schema'], $options['spatial']);
        $status = $this->createOccurrenceFile($targetPath, $options, true);
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

    public function createOccurrenceFile($targetPath, $options, $archiveFile){
        $outputPath = '';
        $outputFilename = '';
        $dataIncluded = false;
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
                $this->applyConditions();
                $sql = DwcArchiverOccurrence::getSqlOccurrences($occurrenceFieldData['fields'],$this->conditionSql,$this->getTableJoins(),true);
                if(!$this->collArr){
                    $sql1 = 'SELECT DISTINCT o.collid FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.tid '.
                        'LEFT JOIN omcollections AS c ON o.collid = c.collid ';
                    if($this->conditionSql){
                        $sql1 .= $this->getTableJoins().$this->conditionSql;
                    }
                    $rs1 = $this->conn->query($sql1);
                    $collidStr = '';
                    while($r1 = $rs1->fetch_object()){
                        $collidStr .= ','.$r1->collid;
                    }
                    $rs1->free();
                    if($collidStr) {
                        $this->setCollArr(trim($collidStr, ','));
                    }
                }

                $this->setUpperTaxonomy();

                //echo $sql; exit;

                FileSystemService::writeRowToCsv($fileHandler, $this->getOccurrenceFileHeaders($occurrenceFieldData, $options['schema']));
                $sql = 'SELECT DISTINCT ' . $this->getOccurrenceFileSqlSelect($occurrenceFieldData['fields']);
                if($rs = $this->conn->query($sql,MYSQLI_USE_RESULT)){
                    $this->setServerDomain();
                    $urlPathPrefix = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'?'':'/');
                    $typeArr = null;
                    while($r = $rs->fetch_assoc()){
                        $dataIncluded = true;
                        if(!$r['occurrenceID']){
                            $guidTarget = $this->collArr[$r['collid']]['guidtarget'];
                            if($guidTarget === 'catalogNumber'){
                                $r['occurrenceID'] = $r['catalogNumber'];
                            }
                            elseif($guidTarget === 'symbiotaUUID'){
                                $r['occurrenceID'] = $r['recordId'];
                            }
                        }
                        if($this->limitToGuids && (!$r['occurrenceID'] || !$r['basisOfRecord'])){
                            continue;
                        }
                        if($this->redactLocalities && (int)$r['localitySecurity'] === 1 && !in_array($r['collid'], $this->rareReaderArr, true)){
                            $protectedFields = array();
                            foreach($this->securityArr as $v){
                                if(array_key_exists($v,$r) && $r[$v]){
                                    $r[$v] = '';
                                    $protectedFields[] = $v;
                                }
                            }
                            if($protectedFields){
                                $r['informationWithheld'] = trim($r['informationWithheld'].'; field values redacted: '.implode(', ',$protectedFields),' ;');
                            }
                        }

                        if($urlPathPrefix) {
                            $r['t_references'] = $urlPathPrefix . 'collections/individual/index.php?occid=' . $r['occid'];
                        }
                        $r['recordId'] = 'urn:uuid:'.$r['recordId'];
                        $managementType = $this->collArr[$r['collid']]['managementtype'];
                        if($managementType === 'Live Data' && array_key_exists('collectionID', $r) && !$r['collectionID']) {
                            $guid = $this->collArr[$r['collid']]['collectionguid'];
                            if(strlen($guid) === 36) {
                                $guid = 'urn:uuid:' . $guid;
                            }
                            $r['collectionID'] = $guid;
                        }
                        if($this->schemaType === 'dwc'){
                            unset($r['localitySecurity'], $r['collid']);
                        }
                        elseif($this->schemaType === 'backup'){
                            unset($r['collid']);
                        }
                        if($this->upperTaxonomy){
                            $lcSciName = $r['scientificName']?strtolower($r['scientificName']):'';
                            $famStr = (isset($r['family'])?strtolower($r['family']):'');
                            $ordStr = (isset($this->upperTaxonomy[$famStr]['o'])?strtolower($this->upperTaxonomy[$famStr]['o']):'');
                            if(!$ordStr){
                                $ordStr = $lcSciName;
                            }
                            $claStr = (isset($this->upperTaxonomy[$ordStr]['c'])?strtolower($this->upperTaxonomy[$ordStr]['c']):'');
                            if(!$claStr){
                                $claStr = (isset($this->upperTaxonomy[$lcSciName]['c'])?strtolower($this->upperTaxonomy[$lcSciName]['c']):'');
                            }
                            $phyStr = (isset($this->upperTaxonomy[$claStr]['p'])?strtolower($this->upperTaxonomy[$claStr]['p']):'');
                            if(!$phyStr){
                                $phyStr = (isset($this->upperTaxonomy[$lcSciName]['p'])?strtolower($this->upperTaxonomy[$lcSciName]['p']):'');
                            }
                            if($famStr && isset($this->upperTaxonomy[$famStr]['o'])){
                                $r['t_order'] = $this->upperTaxonomy[$famStr]['o'];
                            }
                            elseif($ordStr && $claStr){
                                $r['t_order'] = $r['scientificName'];
                            }
                            if($ordStr && isset($this->upperTaxonomy[$ordStr]['c'])){
                                $r['t_class'] = $this->upperTaxonomy[$ordStr]['c'];
                            }
                            if($claStr && isset($this->upperTaxonomy[$claStr]['p'])){
                                $r['t_phylum'] = $this->upperTaxonomy[$claStr]['p'];
                            }
                            if($phyStr && isset($this->upperTaxonomy[$phyStr]['k'])){
                                $r['t_kingdom'] = $this->upperTaxonomy[$phyStr]['k'];
                            }
                        }
                        $this->addcslashesArr($r);
                        fputcsv($fileHandler, $r);
                    }
                    $rs->free();
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
