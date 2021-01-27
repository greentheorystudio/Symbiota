<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/DwcArchiverOccurrence.php');
include_once(__DIR__ . '/DwcArchiverDetermination.php');
include_once(__DIR__ . '/DwcArchiverImage.php');
include_once(__DIR__ . '/DwcArchiverAttribute.php');
include_once(__DIR__ . '/UuidFactory.php');
include_once(__DIR__ . '/OccurrenceAccessStats.php');

class DwcArchiverCore extends Manager{

    private $ts;

    protected $collArr;
    private $collID;
    private $customWhereSql;
    private $conditionSql;
    private $conditionArr = array();
    private $upperTaxonomy = array();

    private $targetPath;
    protected $serverDomain;

    private $schemaType = 'dwc';
    private $limitToGuids = false;
    private $extended = 0;
    private $delimiter = ',';
    private $fileExt = '.csv';
    private $occurrenceFieldArr = array();
    private $determinationFieldArr = array();
    private $imageFieldArr = array();
    private $attributeFieldArr = array();
    private $isPublicDownload = false;

    private $securityArr;
    private $includeDets = 1;
    private $includeImgs = 1;
    private $includeAttributes = 0;
    private $redactLocalities = 1;
    private $rareReaderArr = array();
    private $charSetSource;
    protected $charSetOut = '';

    private $geolocateVariables = array();

    public function __construct(){
        global $LOG_PATH, $CHARSET;
        parent::__construct(null);
        if(!class_exists('DOMDocument')){
            exit('FATAL ERROR: PHP DOMDocument class is not installed, please contact your server admin');
        }
        $this->ts = time();
        if($this->verboseMode){
            $this->setLogFH($LOG_PATH);
        }

        $this->charSetSource = strtoupper($CHARSET);
        $this->charSetOut = $this->charSetSource;

        $this->securityArr = array('recordNumber','locality','locationRemarks','minimumElevationInMeters','maximumElevationInMeters','verbatimElevation',
            'decimalLatitude','decimalLongitude','geodeticDatum','coordinateUncertaintyInMeters','footprintWKT',
            'verbatimCoordinates','georeferenceRemarks','georeferencedBy','georeferenceProtocol','georeferenceSources',
            'georeferenceVerificationStatus','habitat','informationWithheld');

        set_time_limit(500);
    }

    public function getOccurrenceCnt(){
        $retStr = 0;
        $this->applyConditions();
        if(!$this->occurrenceFieldArr) {
            $this->occurrenceFieldArr = DwcArchiverOccurrence::getOccurrenceArr($this->schemaType, $this->extended);
        }
        $sql = DwcArchiverOccurrence::getSqlOccurrences($this->occurrenceFieldArr['fields'],$this->conditionSql,$this->getTableJoins(),false);
        if($this->schemaType !== 'backup') {
            $sql .= ' LIMIT 1000000';
        }
        if($sql){
            $sql = 'SELECT COUNT(o.occid) as cnt '.$sql;
            //echo $sql; exit;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retStr = $r->cnt;
            }
            $rs->free();
        }
        return $retStr;
    }

    public function setTargetPath($tp = ''): void
    {
        global $SERVER_ROOT, $TEMP_DIR_ROOT;
        if($tp){
            $this->targetPath = $tp;
        }
        else{
            $tPath = $TEMP_DIR_ROOT;
            $tPathSub = substr($tPath,-1);
            if(!$tPath){
                $tPath = ini_get('upload_tmp_dir');
            }
            if(!$tPath){
                $tPath = $SERVER_ROOT;
                if($tPathSub !== '/' && $tPathSub !== '\\'){
                    $tPath .= '/';
                }
                $tPath .= 'temp/';
            }
            if($tPathSub !== '/' && $tPathSub !== '\\'){
                $tPath .= '/';
            }
            if(file_exists($tPath. 'downloads')){
                $tPath .= 'downloads/';
            }
            $this->targetPath = $tPath;
        }
    }

    public function setCollArr($collTarget, $collTypeStr = ''): void
    {
        $collTarget = $this->cleanInStr($collTarget);
        $collType = $this->cleanInStr($collTypeStr);
        $sqlWhere = '';
        if($collType === 'specimens'){
            $sqlWhere = '(c.colltype = "Preserved Specimens") ';
        }
        elseif($collType === 'observations'){
            $sqlWhere = '(c.colltype = "Observations" OR c.colltype = "General Observations") ';
        }
        if($collTarget){
            $this->addCondition('collid', 'EQUALS', $collTarget);
            if($collTarget !== 'all') {
                $sqlWhere .= ($sqlWhere ? 'AND ' : '') . '(c.collid IN(' . $collTarget . ')) ';
            }
        }

        if($sqlWhere){
            $sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.fulldescription, c.collectionguid, '.
                'IFNULL(c.homepage,i.url) AS url, IFNULL(c.contact,i.contact) AS contact, IFNULL(c.email,i.email) AS email, c.guidtarget, c.dwcaurl, '.
                'c.latitudedecimal, c.longitudedecimal, c.icon, c.managementtype, c.colltype, c.rights, c.rightsholder, c.usageterm, '.
                'i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country, i.phone '.
                'FROM omcollections c LEFT JOIN institutions i ON c.iid = i.iid WHERE '.$sqlWhere;
            //echo 'SQL: '.$sql.'<br/>';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $this->collArr[$r->collid]['instcode'] = $r->institutioncode;
                $this->collArr[$r->collid]['collcode'] = $r->collectioncode;
                $this->collArr[$r->collid]['collname'] = $r->collectionname;
                $this->collArr[$r->collid]['description'] = $r->fulldescription;
                $this->collArr[$r->collid]['collectionguid'] = $r->collectionguid;
                $this->collArr[$r->collid]['url'] = $r->url;
                $this->collArr[$r->collid]['contact'] = $r->contact;
                $this->collArr[$r->collid]['email'] = $r->email;
                $this->collArr[$r->collid]['guidtarget'] = $r->guidtarget;
                $this->collArr[$r->collid]['dwcaurl'] = $r->dwcaurl;
                $this->collArr[$r->collid]['lat'] = $r->latitudedecimal;
                $this->collArr[$r->collid]['lng'] = $r->longitudedecimal;
                $this->collArr[$r->collid]['icon'] = $r->icon;
                $this->collArr[$r->collid]['colltype'] = $r->colltype;
                $this->collArr[$r->collid]['managementtype'] = $r->managementtype;
                $this->collArr[$r->collid]['rights'] = $r->rights;
                $this->collArr[$r->collid]['rightsholder'] = $r->rightsholder;
                $this->collArr[$r->collid]['usageterm'] = $r->usageterm;
                $this->collArr[$r->collid]['address1'] = $r->address1;
                $this->collArr[$r->collid]['address2'] = $r->address2;
                $this->collArr[$r->collid]['city'] = $r->city;
                $this->collArr[$r->collid]['state'] = $r->stateprovince;
                $this->collArr[$r->collid]['postalcode'] = $r->postalcode;
                $this->collArr[$r->collid]['country'] = $r->country;
                $this->collArr[$r->collid]['phone'] = $r->phone;
            }
            $rs->free();
        }
    }

    public function getCollArr($id = 0){
        if($id && isset($this->collArr[$id])) {
            return $this->collArr[$id];
        }
        return $this->collArr;
    }

    public function setCustomWhereSql($sql): void
    {
        $this->customWhereSql = $sql;
    }

    public function addCondition($field, $cond, $value = null): ?bool
    {
        $cond = strtoupper(trim($cond));
        if(!preg_match('/^[A-Za-z]+$/',$field)) {
            return false;
        }
        if(!preg_match('/^[A-Z]+$/',$cond)) {
            return false;
        }
        if($field){
            if(!$cond) {
                $cond = 'EQUALS';
            }
            if($value || ($cond === 'NULL' || $cond === 'NOTNULL')){
                if(is_array($value)){
                    $this->conditionArr[$field][$cond] = $this->cleanInArray($value);
                }
                else{
                    $this->conditionArr[$field][$cond][] = $this->cleanInStr($value);
                }
            }
        }
        return true;
    }

    private function applyConditions(): void
    {
        $this->conditionSql = '';
        if($this->customWhereSql){
            $this->conditionSql = $this->customWhereSql.' ';
        }
        if(array_key_exists('collid', $this->conditionArr) && $this->conditionArr['collid']){
            if($this->conditionArr['collid']['EQUALS'][0] !== 'all'){
                $this->conditionSql .= 'AND (o.collid IN('.$this->conditionArr['collid']['EQUALS'][0].')) ';
            }
            unset($this->conditionArr['collid']);
        }
        else if($this->collArr && (!$this->conditionSql || !stripos($this->conditionSql,'collid in('))){
            $this->conditionSql .= 'AND (o.collid IN('.implode(',',array_keys($this->collArr)).')) ';
        }
        $sqlFrag = '';
        if($this->conditionArr){
            foreach($this->conditionArr as $field => $condArr){
                if($field === 'stateid'){
                    $sqlFrag .= 'AND (a.stateid IN('.implode(',',$condArr['EQUALS']).')) ';
                }
                elseif($field === 'traitid'){
                    $sqlFrag .= 'AND (s.traitid IN('.implode(',',$condArr['EQUALS']).')) ';
                }
                elseif($field === 'clid'){
                    $sqlFrag .= 'AND (v.clid IN('.implode(',',$condArr['EQUALS']).')) ';
                }
                else{
                    $sqlFrag2 = '';
                    foreach($condArr as $cond => $valueArr){
                        if($cond === 'NULL'){
                            $sqlFrag2 .= 'OR o.'.$field.' IS NULL ';
                        }
                        elseif($cond === 'NOTNULL'){
                            $sqlFrag2 .= 'OR o.'.$field.' IS NOT NULL ';
                        }
                        elseif($cond === 'EQUALS'){
                            $sqlFrag2 .= 'OR o.'.$field.' IN("'.implode('","',$valueArr).'") ';
                        }
                        elseif($cond === 'NOTEQUALS'){
                            $sqlFrag2 .= 'OR o.'.$field.' NOT IN("'.implode('","',$valueArr).'") ';
                        }
                        else{
                            foreach($valueArr as $value){
                                if($cond === 'STARTS'){
                                    $sqlFrag2 .= 'OR o.'.$field.' LIKE "'.$value.'%" ';
                                }
                                elseif($cond === 'LIKE'){
                                    $sqlFrag2 .= 'OR o.'.$field.' LIKE "%'.$value.'%" ';
                                }
                                elseif($cond === 'NOTLIKE'){
                                    $sqlFrag2 .= 'OR o.'.$field.' NOT LIKE "%'.$value.'%" ';
                                }
                                elseif($cond === 'LESSTHAN'){
                                    $sqlFrag2 .= 'OR o.'.$field.' < "'.$value.'" ';
                                }
                                elseif($cond === 'GREATERTHAN'){
                                    $sqlFrag2 .= 'OR o.'.$field.' > "'.$value.'" ';
                                }
                            }
                        }
                    }
                    if($sqlFrag2) {
                        $sqlFrag .= 'AND (' . substr($sqlFrag2, 3) . ') ';
                    }
                }
            }
        }
        if($sqlFrag){
            $this->conditionSql .= $sqlFrag;
        }
        if($this->conditionSql){
            if(strpos($this->conditionSql, 'AND ') === 0){
                $this->conditionSql = 'WHERE'.substr($this->conditionSql,3);
            }
            elseif(strpos($this->conditionSql, 'WHERE ') !== 0){
                $this->conditionSql = 'WHERE '.$this->conditionSql;
            }
        }
    }

    private function getTableJoins(): string
    {
        global $QUICK_HOST_ENTRY_IS_ACTIVE;
        $sql = '';
        if($this->conditionSql){
            if(stripos($this->conditionSql,'v.clid')){
                $sql = 'LEFT JOIN fmvouchers v ON o.occid = v.occid ';
            }
            if(stripos($this->conditionSql,'p.point')){
                $sql .= 'LEFT JOIN omoccurpoints p ON o.occid = p.occid ';
            }
            if($QUICK_HOST_ENTRY_IS_ACTIVE){
                $sql .= 'LEFT JOIN omoccurassociations oas ON o.occid = oas.occid ';
            }
            if(strpos($this->conditionSql,'MATCH(f.recordedby)') || strpos($this->conditionSql,'MATCH(f.locality)')){
                $sql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
            }
            if(stripos($this->conditionSql,'a.stateid')){
                $sql .= 'INNER JOIN tmattributes a ON o.occid = a.occid ';
            }
            elseif(stripos($this->conditionSql,'s.traitid')){
                $sql .= 'INNER JOIN tmattributes a ON o.occid = a.occid '.
                    'INNER JOIN tmstates s ON a.stateid = s.stateid ';
            }
        }
        return $sql;
    }

    public function getDwcArray() {
        global $CLIENT_ROOT;
        $result = array();
        if(!$this->occurrenceFieldArr){
            $this->occurrenceFieldArr = DwcArchiverOccurrence::getOccurrenceArr($this->schemaType, $this->extended);
        }

        $this->applyConditions();
        $sql = DwcArchiverOccurrence::getSqlOccurrences($this->occurrenceFieldArr['fields'],$this->conditionSql,$this->getTableJoins());
        if(!$sql) {
            return false;
        }
        $sql .= ' LIMIT 1000000';
        $fieldArr = $this->occurrenceFieldArr['fields'];
        if($this->schemaType === 'dwc' || $this->schemaType === 'pensoft'){
            unset($fieldArr['localitySecurity']);
        }
        if($this->schemaType === 'dwc' || $this->schemaType === 'pensoft' || $this->schemaType === 'backup'){
            unset($fieldArr['collId']);
        }
        if(!$this->collArr){
            $sql1 = 'SELECT DISTINCT o.collid FROM omoccurrences o ';
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
        if($rs = $this->conn->query($sql,MYSQLI_USE_RESULT)){
            $typeArr = null;
            if($this->schemaType === 'pensoft'){
                $typeArr = array('Other material', 'Holotype', 'Paratype', 'Isotype', 'Isoparatype', 'Isolectotype', 'Isoneotype', 'Isosyntype');
            }
            $this->setServerDomain();
            $urlPathPrefix = $this->serverDomain.$CLIENT_ROOT.(substr($CLIENT_ROOT,-1) === '/'?'':'/');
            $cnt = 0;
            while($r = $rs->fetch_assoc()){
                if($this->redactLocalities
                    && (int)$r['localitySecurity'] === 1
                    && !in_array($r['collid'], $this->rareReaderArr, true)
                ){
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
                if(!$r['occurrenceID']){
                    $guidTarget = $this->collArr[$r['collid']]['guidtarget'];
                    if($guidTarget === 'catalogNumber'){
                        $r['occurrenceID'] = $r['catalogNumber'];
                    }
                    elseif($guidTarget === 'symbiotaUUID'){
                        $r['occurrenceID'] = $r['recordId'];
                    }
                }

                $r['recordId'] = 'urn:uuid:'.$r['recordId'];
                $managementType = $this->collArr[$r['collid']]['managementtype'];
                if($managementType && $managementType === 'Live Data' && array_key_exists('collectionID', $r) && !$r['collectionID']) {
                    $guid = $this->collArr[$r['collid']]['collectionguid'];
                    if(strlen($guid) === 36) {
                        $guid = 'urn:uuid:' . $guid;
                    }
                    $r['collectionID'] = $guid;
                }
                if($this->schemaType === 'dwc'){
                    unset($r['localitySecurity']);
                }
                if($this->schemaType === 'dwc' || $this->schemaType === 'backup'){
                    unset($r['collid']);
                }
                if($this->schemaType === 'pensoft'){
                    unset($r['localitySecurity'], $r['collid']);
                    if($r['typeStatus']){
                        $typeValue = strtolower($r['typeStatus']);
                        $typeInvalid = true;
                        $invalidText = '';
                        foreach($typeArr as $testStr){
                            if ($typeValue === strtolower($testStr)) {
                                $typeInvalid = false;
                                break;
                            }

                            if(stripos($typeValue, $testStr)) {
                                $invalidText = $r['typeStatus'];
                                $r['typeStatus'] = $testStr;
                                $typeInvalid = false;
                                break;
                            }
                        }
                        if($typeInvalid){
                            $invalidText = $r['typeStatus'];
                            $r['typeStatus'] = 'Other material';
                        }
                        if($invalidText){
                            if($r['occurrenceRemarks']) {
                                $invalidText = $r['occurrenceRemarks'] . '; ' . $invalidText;
                            }
                            $r['occurrenceRemarks'] = $invalidText;
                        }
                    }
                    else{
                        $r['typeStatus'] = 'Other material';
                    }
                }
                if($this->upperTaxonomy){
                    $lcSciName = strtolower($r['scientificName']);
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
                if($urlPathPrefix) {
                    $r['t_references'] = $urlPathPrefix . 'collections/individual/index.php?occid=' . $r['occid'];
                }

                foreach($r as $rKey => $rValue){
                    if(strpos($rKey, 't_') === 0) {
                        $rKey = substr($rKey, 2);
                    }
                    $result[$cnt][$rKey] = $rValue;
                }
                $cnt++;
            }
            $rs->free();
        }
        else{
            $this->logOrEcho('ERROR creating occurrence file: ' .$this->conn->error."\n");
            $this->logOrEcho("\tSQL: ".$sql."\n");
        }
        return $result;
    }

    public function createDwcArchive($fileNameSeed = ''): string
    {
        $collid = ($this->collArr?key($this->collArr):0);
        if(!$fileNameSeed){
            if(count($this->collArr) === 1){
                $firstColl = $this->collArr[$collid];
                if($firstColl){
                    $fileNameSeed = $firstColl['instcode'];
                    if($firstColl['collcode']) {
                        $fileNameSeed .= '-' . $firstColl['collcode'];
                    }
                }
                if($this->schemaType === 'backup'){
                    $fileNameSeed .= '_backup_'.$this->ts;
                }
            }
            else{
                $fileNameSeed = 'SymbiotaOutput_'.$this->ts;
            }
        }
        $fileName = str_replace(array(' ','"',"'"),'',$fileNameSeed).'_DwC-A.zip';

        if(!$this->targetPath) {
            $this->setTargetPath();
        }
        $archiveFile = '';
        $this->logOrEcho('Creating DwC-A file: '.$fileName."\n");

        if(!class_exists('ZipArchive')){
            $this->logOrEcho("FATAL ERROR: PHP ZipArchive class is not installed, please contact your server admin\n");
            exit('FATAL ERROR: PHP ZipArchive class is not installed, please contact your server admin');
        }
        $status = $this->writeOccurrenceFile();
        if($status){
            $archiveFile = $this->targetPath.$fileName;
            if(file_exists($archiveFile)) {
                unlink($archiveFile);
            }
            $zipArchive = new ZipArchive;
            $status = $zipArchive->open($archiveFile, ZipArchive::CREATE);
            if($status !== true){
                exit('FATAL ERROR: unable to create archive file: '.$status);
            }

            $zipArchive->addFile($this->targetPath.$this->ts.'-occur'.$this->fileExt);
            $zipArchive->renameName($this->targetPath.$this->ts.'-occur'.$this->fileExt,'occurrences'.$this->fileExt);
            if($this->includeDets) {
                $this->writeDeterminationFile();
                $zipArchive->addFile($this->targetPath.$this->ts.'-det'.$this->fileExt);
                $zipArchive->renameName($this->targetPath.$this->ts.'-det'.$this->fileExt,'identifications'.$this->fileExt);
            }
            if($this->includeImgs){
                $this->writeImageFile();
                $zipArchive->addFile($this->targetPath.$this->ts.'-images'.$this->fileExt);
                $zipArchive->renameName($this->targetPath.$this->ts.'-images'.$this->fileExt,'images'.$this->fileExt);
            }
            if($this->includeAttributes){
                $this->writeAttributeFile();
                $zipArchive->addFile($this->targetPath.$this->ts.'-attr'.$this->fileExt);
                $zipArchive->renameName($this->targetPath.$this->ts.'-attr'.$this->fileExt,'measurementOrFact'.$this->fileExt);
            }
            $this->writeMetaFile();
            $zipArchive->addFile($this->targetPath.$this->ts.'-meta.xml');
            $zipArchive->renameName($this->targetPath.$this->ts.'-meta.xml','meta.xml');
            $this->writeEmlFile();
            $zipArchive->addFile($this->targetPath.$this->ts.'-eml.xml');
            $zipArchive->renameName($this->targetPath.$this->ts.'-eml.xml','eml.xml');

            $zipArchive->close();
            unlink($this->targetPath.$this->ts.'-occur'.$this->fileExt);
            if($this->includeDets) {
                unlink($this->targetPath . $this->ts . '-det' . $this->fileExt);
            }
            if($this->includeImgs) {
                unlink($this->targetPath . $this->ts . '-images' . $this->fileExt);
            }
            if($this->includeAttributes) {
                unlink($this->targetPath . $this->ts . '-attr' . $this->fileExt);
            }
            unlink($this->targetPath.$this->ts.'-meta.xml');
            if($this->schemaType === 'dwc'){
                rename($this->targetPath.$this->ts.'-eml.xml',$this->targetPath.str_replace('.zip','.eml',$fileName));
            }
            else{
                unlink($this->targetPath.$this->ts.'-eml.xml');
            }
        }
        else{
            $errStr = "<span style='color:red;'>FAILED to create archive file due to failure to return occurrence records. ".
                'Note that OccurrenceID GUID assignments are required for Darwin Core Archive publishing. ' .
                'Symbiota GUID (recordID) assignments are also required, which can be verified by the portal manager through running the GUID mapping utilitiy available in sitemap</span>';
            $this->logOrEcho($errStr);
            if($collid) {
                $this->deleteArchive($collid);
            }
            unset($this->collArr[$collid]);
        }
        $this->logOrEcho("\n-----------------------------------------------------\n");
        return $archiveFile;
    }

    private function writeMetaFile(): void
    {
        $this->logOrEcho('Creating meta.xml (' .date('h:i:s A'). ')... ');

        $newDoc = new DOMDocument('1.0',$this->charSetOut);

        $rootElem = $newDoc->createElement('archive');
        $rootElem->setAttribute('metadata','eml.xml');
        $rootElem->setAttribute('xmlns','http://rs.tdwg.org/dwc/text/');
        $rootElem->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $rootElem->setAttribute('xsi:schemaLocation','http://rs.tdwg.org/dwc/text/   http://rs.tdwg.org/dwc/text/tdwg_dwc_text.xsd');
        $newDoc->appendChild($rootElem);

        $coreElem = $newDoc->createElement('core');
        $coreElem->setAttribute('dateFormat','YYYY-MM-DD');
        $coreElem->setAttribute('encoding',$this->charSetOut);
        $coreElem->setAttribute('fieldsTerminatedBy',$this->delimiter);
        $coreElem->setAttribute('linesTerminatedBy','\n');
        $coreElem->setAttribute('fieldsEnclosedBy','"');
        $coreElem->setAttribute('ignoreHeaderLines','1');
        $coreElem->setAttribute('rowType','http://rs.tdwg.org/dwc/terms/Occurrence');

        $filesElem = $newDoc->createElement('files');
        $filesElem->appendChild($newDoc->createElement('location','occurrences'.$this->fileExt));
        $coreElem->appendChild($filesElem);

        $idElem = $newDoc->createElement('id');
        $idElem->setAttribute('index','0');
        $coreElem->appendChild($idElem);

        $occCnt = 1;
        $termArr = $this->occurrenceFieldArr['terms'];
        if($this->schemaType === 'dwc'){
            unset($termArr['localitySecurity']);
        }
        if($this->schemaType === 'dwc' || $this->schemaType === 'backup'){
            unset($termArr['collId']);
        }
        foreach($termArr as $k => $v){
            $fieldElem = $newDoc->createElement('field');
            $fieldElem->setAttribute('index',$occCnt);
            $fieldElem->setAttribute('term',$v);
            $coreElem->appendChild($fieldElem);
            $occCnt++;
        }
        $rootElem->appendChild($coreElem);

        if($this->includeDets){
            $extElem1 = $newDoc->createElement('extension');
            $extElem1->setAttribute('encoding',$this->charSetOut);
            $extElem1->setAttribute('fieldsTerminatedBy',$this->delimiter);
            $extElem1->setAttribute('linesTerminatedBy','\n');
            $extElem1->setAttribute('fieldsEnclosedBy','"');
            $extElem1->setAttribute('ignoreHeaderLines','1');
            $extElem1->setAttribute('rowType','http://rs.tdwg.org/dwc/terms/Identification');

            $filesElem1 = $newDoc->createElement('files');
            $filesElem1->appendChild($newDoc->createElement('location','identifications'.$this->fileExt));
            $extElem1->appendChild($filesElem1);

            $coreIdElem1 = $newDoc->createElement('coreid');
            $coreIdElem1->setAttribute('index','0');
            $extElem1->appendChild($coreIdElem1);

            $detCnt = 1;
            $termArr = $this->determinationFieldArr['terms'];
            foreach($termArr as $k => $v){
                $fieldElem = $newDoc->createElement('field');
                $fieldElem->setAttribute('index',$detCnt);
                $fieldElem->setAttribute('term',$v);
                $extElem1->appendChild($fieldElem);
                $detCnt++;
            }
            $rootElem->appendChild($extElem1);
        }

        if($this->includeImgs){
            $extElem2 = $newDoc->createElement('extension');
            $extElem2->setAttribute('encoding',$this->charSetOut);
            $extElem2->setAttribute('fieldsTerminatedBy',$this->delimiter);
            $extElem2->setAttribute('linesTerminatedBy','\n');
            $extElem2->setAttribute('fieldsEnclosedBy','"');
            $extElem2->setAttribute('ignoreHeaderLines','1');
            $extElem2->setAttribute('rowType','http://rs.gbif.org/terms/1.0/Image');

            $filesElem2 = $newDoc->createElement('files');
            $filesElem2->appendChild($newDoc->createElement('location','images'.$this->fileExt));
            $extElem2->appendChild($filesElem2);

            $coreIdElem2 = $newDoc->createElement('coreid');
            $coreIdElem2->setAttribute('index','0');
            $extElem2->appendChild($coreIdElem2);

            $imgCnt = 1;
            $termArr = $this->imageFieldArr['terms'];
            foreach($termArr as $k => $v){
                $fieldElem = $newDoc->createElement('field');
                $fieldElem->setAttribute('index',$imgCnt);
                $fieldElem->setAttribute('term',$v);
                $extElem2->appendChild($fieldElem);
                $imgCnt++;
            }
            $rootElem->appendChild($extElem2);
        }

        if($this->includeAttributes){
            $extElem3 = $newDoc->createElement('extension');
            $extElem3->setAttribute('encoding',$this->charSetOut);
            $extElem3->setAttribute('fieldsTerminatedBy',$this->delimiter);
            $extElem3->setAttribute('linesTerminatedBy','\n');
            $extElem3->setAttribute('fieldsEnclosedBy','"');
            $extElem3->setAttribute('ignoreHeaderLines','1');
            $extElem3->setAttribute('rowType','http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact');

            $filesElem3 = $newDoc->createElement('files');
            $filesElem3->appendChild($newDoc->createElement('location','measurementOrFact'.$this->fileExt));
            $extElem3->appendChild($filesElem3);

            $coreIdElem3 = $newDoc->createElement('coreid');
            $coreIdElem3->setAttribute('index','0');
            $extElem3->appendChild($coreIdElem3);

            $mofCnt = 1;
            $termArr = $this->attributeFieldArr['terms'];
            foreach($termArr as $k => $v){
                $fieldElem = $newDoc->createElement('field');
                $fieldElem->setAttribute('index',$mofCnt);
                $fieldElem->setAttribute('term',$v);
                $extElem3->appendChild($fieldElem);
                $mofCnt++;
            }
            $rootElem->appendChild($extElem3);
        }

        $newDoc->save($this->targetPath.$this->ts.'-meta.xml');

        $this->logOrEcho('Done!! (' .date('h:i:s A').")\n");
    }

    private function getEmlArr(): array
    {
        global $CLIENT_ROOT, $DEFAULT_TITLE, $USER_DISPLAY_NAME, $PORTAL_GUID, $ADMIN_EMAIL;
        $this->setServerDomain();
        $urlPathPrefix = $this->serverDomain.$CLIENT_ROOT.(substr($CLIENT_ROOT,-1) === '/'?'':'/');
        $localDomain = $this->serverDomain;

        $emlArr = array();
        if(count($this->collArr) === 1){
            $collId = key($this->collArr);
            $cArr = $this->collArr[$collId];

            $emlArr['alternateIdentifier'][] = $urlPathPrefix.'collections/misc/collprofiles.php?collid='.$collId;
            $emlArr['title'] = $cArr['collname'];
            $emlArr['description'] = $cArr['description'];

            $emlArr['contact']['individualName'] = $cArr['contact'];
            $emlArr['contact']['organizationName'] = $cArr['collname'];
            $emlArr['contact']['phone'] = $cArr['phone'];
            $emlArr['contact']['electronicMailAddress'] = $cArr['email'];
            $emlArr['contact']['onlineUrl'] = $cArr['url'];

            $emlArr['contact']['addr']['deliveryPoint'] = $cArr['address1'].($cArr['address2']?', '.$cArr['address2']:'');
            $emlArr['contact']['addr']['city'] = $cArr['city'];
            $emlArr['contact']['addr']['administrativeArea'] = $cArr['state'];
            $emlArr['contact']['addr']['postalCode'] = $cArr['postalcode'];
            $emlArr['contact']['addr']['country'] = $cArr['country'];


            $emlArr['intellectualRights'] = $cArr['rights'];
        }
        else{
            $emlArr['title'] = $DEFAULT_TITLE.' general data extract';
        }
        if(isset($USER_DISPLAY_NAME)){
            $emlArr['associatedParty'][0]['individualName'] = $USER_DISPLAY_NAME;
            $emlArr['associatedParty'][0]['role'] = 'CONTENT_PROVIDER';
        }

        if($PORTAL_GUID){
            $emlArr['creator'][0]['attr']['id'] = $PORTAL_GUID;
        }
        $emlArr['creator'][0]['organizationName'] = $DEFAULT_TITLE;
        $emlArr['creator'][0]['electronicMailAddress'] = $ADMIN_EMAIL;
        $emlArr['creator'][0]['onlineUrl'] = $urlPathPrefix.'index.php';

        $emlArr['metadataProvider'][0]['organizationName'] = $DEFAULT_TITLE;
        $emlArr['metadataProvider'][0]['electronicMailAddress'] = $ADMIN_EMAIL;
        $emlArr['metadataProvider'][0]['onlineUrl'] = $urlPathPrefix.'index.php';

        $emlArr['pubDate'] = date('Y-m-d');

        $cnt = 1;
        foreach($this->collArr as $id => $collArr){
            $emlArr['associatedParty'][$cnt]['organizationName'] = $collArr['collname'];
            $emlArr['associatedParty'][$cnt]['individualName'] = $collArr['contact'];
            $emlArr['associatedParty'][$cnt]['positionName'] = 'Collection Manager';
            $emlArr['associatedParty'][$cnt]['role'] = 'CONTENT_PROVIDER';
            $emlArr['associatedParty'][$cnt]['electronicMailAddress'] = $collArr['email'];
            $emlArr['associatedParty'][$cnt]['phone'] = $collArr['phone'];

            if($collArr['state']){
                $emlArr['associatedParty'][$cnt]['address']['deliveryPoint'] = $collArr['address1'];
                if($collArr['address2']) {
                    $emlArr['associatedParty'][$cnt]['address']['deliveryPoint'] = $collArr['address2'];
                }
                $emlArr['associatedParty'][$cnt]['address']['city'] = $collArr['city'];
                $emlArr['associatedParty'][$cnt]['address']['administrativeArea'] = $collArr['state'];
                $emlArr['associatedParty'][$cnt]['address']['postalCode'] = $collArr['postalcode'];
                $emlArr['associatedParty'][$cnt]['address']['country'] = $collArr['country'];
            }

            $emlArr['collMetadata'][$cnt]['attr']['identifier'] = $collArr['collectionguid'];
            $emlArr['collMetadata'][$cnt]['attr']['id'] = $id;
            $emlArr['collMetadata'][$cnt]['alternateIdentifier'] = $urlPathPrefix.'collections/misc/collprofiles.php?collid='.$id;
            $emlArr['collMetadata'][$cnt]['parentCollectionIdentifier'] = $collArr['instcode'];
            $emlArr['collMetadata'][$cnt]['collectionIdentifier'] = $collArr['collcode'];
            $emlArr['collMetadata'][$cnt]['collectionName'] = $collArr['collname'];
            if($collArr['icon']){
                if(strpos($collArr['icon'], 'images/collicons/') === 0){
                    $imgLink = $urlPathPrefix.$collArr['icon'];
                }
                elseif(strpos($collArr['icon'], '/') === 0){
                    $imgLink = $localDomain.$collArr['icon'];
                }
                else{
                    $imgLink = $collArr['icon'];
                }
                $emlArr['collMetadata'][$cnt]['resourceLogoUrl'] = $imgLink;
            }
            $emlArr['collMetadata'][$cnt]['onlineUrl'] = $collArr['url'];
            $emlArr['collMetadata'][$cnt]['intellectualRights'] = $collArr['rights'];
            if($collArr['rightsholder']) {
                $emlArr['collMetadata'][$cnt]['additionalInfo'] = $collArr['rightsholder'];
            }
            if($collArr['usageterm']) {
                $emlArr['collMetadata'][$cnt]['additionalInfo'] = $collArr['usageterm'];
            }
            $emlArr['collMetadata'][$cnt]['abstract'] = $collArr['description'];

            $cnt++;
        }
        $emlArr = $this->utf8EncodeArr($emlArr);
        return $emlArr;
    }

    private function writeEmlFile(): void
    {
        $this->logOrEcho('Creating eml.xml (' .date('h:i:s A'). ')... ');

        $emlDoc = $this->getEmlDom();

        $emlDoc->save($this->targetPath.$this->ts.'-eml.xml');

        $this->logOrEcho('Done!! (' .date('h:i:s A').")\n");
    }

    public function getEmlDom($emlArr = null): DOMDocument
    {
        global $DEFAULT_TITLE, $CLIENT_ROOT, $RIGHTS_TERMS_DEFS, $EML_PROJECT_ADDITIONS;
        $usageTermArr = array();

        if(!$emlArr) {
            $emlArr = $this->getEmlArr();
        }
        foreach($RIGHTS_TERMS_DEFS as $k => $v){
            if($k === $emlArr['collMetadata'][1]['intellectualRights']){
                $usageTermArr = $v;
            }
        }

        $newDoc = new DOMDocument('1.0',$this->charSetOut);

        $rootElem = $newDoc->createElement('eml:eml');
        $rootElem->setAttribute('xmlns:eml','eml://ecoinformatics.org/eml-2.1.1');
        $rootElem->setAttribute('xmlns:dc','http://purl.org/dc/terms/');
        $rootElem->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $rootElem->setAttribute('xsi:schemaLocation','eml://ecoinformatics.org/eml-2.1.1 http://rs.gbif.org/schema/eml-gbif-profile/1.0.1/eml.xsd');
        $rootElem->setAttribute('packageId',UuidFactory::getUuidV4());
        $rootElem->setAttribute('system','http://symbiota.org');
        $rootElem->setAttribute('scope','system');
        $rootElem->setAttribute('xml:lang','eng');

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
                        $creatorElem->setAttribute($atKey,$atValue);
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
                $conElem->appendChild($newDoc->createTextNode($contactValue));
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
            foreach($associatedPartyArr as $assocKey => $assocArr){
                $assocElem = $newDoc->createElement('associatedParty');
                $addrArr = array();
                if(isset($assocArr['address'])){
                    $addrArr = $assocArr['address'];
                    unset($assocArr['address']);
                }
                foreach($assocArr as $aKey => $aArr){
                    $childAssocElem = $newDoc->createElement($aKey);
                    $childAssocElem->appendChild($newDoc->createTextNode($aArr));
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
            $citetitleElem->appendChild($newDoc->createTextNode((array_key_exists('title',$usageTermArr)?$usageTermArr['title']:'')));
            $ulinkElem->appendChild($citetitleElem);
            $ulinkElem->setAttribute('url',(array_key_exists('url',$usageTermArr)?$usageTermArr['url']:$emlArr['intellectualRights']));
            $paraElem->appendChild($ulinkElem);
            $paraElem->appendChild($newDoc->createTextNode((array_key_exists('def',$usageTermArr)?$usageTermArr['def']:'')));
            $rightsElem->appendChild($paraElem);
            $datasetElem->appendChild($rightsElem);
        }

        if($EML_PROJECT_ADDITIONS){
            foreach($EML_PROJECT_ADDITIONS as $k => $v){
                if(is_array($v['collid']) && in_array($this->collID, $v['collid'], true)){
                    $projID = $v['id'];
                    $projTitle = $v['title'];
                    $projectElem = $newDoc->createElement('project');
                    $projectElem->setAttribute('id',$projID);
                    $titleElem = $newDoc->createElement('title');
                    $titleElem->appendChild($newDoc->createTextNode($projTitle));
                    $projectElem->appendChild($titleElem);
                    $datasetElem->appendChild($projectElem);
                }
            }
        }

        $symbElem = $newDoc->createElement('symbiota');
        $dateElem = $newDoc->createElement('dateStamp');
        $dateElem->appendChild($newDoc->createTextNode(date('c')));
        $symbElem->appendChild($dateElem);
        $id = UuidFactory::getUuidV4();
        $citeElem = $newDoc->createElement('citation');
        $citeElem->appendChild($newDoc->createTextNode($DEFAULT_TITLE.' - '.$id));
        $citeElem->setAttribute('identifier',$id);
        $symbElem->appendChild($citeElem);
        $physicalElem = $newDoc->createElement('physical');
        $physicalElem->appendChild($newDoc->createElement('characterEncoding',$this->charSetOut));
        $dfElem = $newDoc->createElement('dataFormat');
        $edfElem = $newDoc->createElement('externallyDefinedFormat');
        $dfElem->appendChild($edfElem);
        $edfElem->appendChild($newDoc->createElement('formatName','Darwin Core Archive'));
        $physicalElem->appendChild($dfElem);
        $symbElem->appendChild($physicalElem);
        if(array_key_exists('collMetadata',$emlArr)){
            foreach($emlArr['collMetadata'] as $k => $collArr){
                $collArr = $this->utf8EncodeArr($collArr);
                $collElem = $newDoc->createElement('collection');
                if(isset($collArr['attr']) && $collArr['attr']){
                    $attrArr = $collArr['attr'];
                    unset($collArr['attr']);
                    foreach($attrArr as $attrKey => $attrValue){
                        $collElem->setAttribute($attrKey,$attrValue);
                    }
                }
                $abstractStr = '';
                if(isset($collArr['abstract']) && $collArr['abstract']){
                    $abstractStr = $collArr['abstract'];
                    unset($collArr['abstract']);
                }
                foreach($collArr as $collKey => $collValue){
                    $collElem2 = $newDoc->createElement($collKey);
                    $collElem2->appendChild($newDoc->createTextNode($collValue));
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
        if($this->schemaType === 'coge' && $this->geolocateVariables){
            $this->setServerDomain();
            if($this->serverDomain){
                $urlPathPrefix = $this->serverDomain.$CLIENT_ROOT.(substr($CLIENT_ROOT,-1) === '/'?'':'/');
                $urlPathPrefix .= 'collections/individual/index.php';
                $glElem = $newDoc->createElement('geoLocate');
                $glElem->appendChild($newDoc->createElement('dataSourcePrimaryName',$this->geolocateVariables['cogename']));
                $glElem->appendChild($newDoc->createElement('dataSourceSecondaryName',$this->geolocateVariables['cogedescr']));
                $glElem->appendChild($newDoc->createElement('targetCommunityName',$this->geolocateVariables['cogecomm']));
                $glElem->appendChild($newDoc->createElement('specimenHyperlinkBase',$urlPathPrefix));
                $glElem->appendChild($newDoc->createElement('specimenHyperlinkParameter','occid'));
                $glElem->appendChild($newDoc->createElement('specimenHyperlinkValueField','Id'));
                $metaElem->appendChild($glElem);
            }
        }
        $addMetaElem = $newDoc->createElement('additionalMetadata');
        $addMetaElem->appendChild($metaElem);
        $rootElem->appendChild($addMetaElem);

        return $newDoc;
    }

    public function getFullRss(): string
    {
        global $DEFAULT_TITLE, $CLIENT_ROOT;
        $newDoc = new DOMDocument('1.0',$this->charSetOut);

        $rootElem = $newDoc->createElement('rss');
        $rootAttr = $newDoc->createAttribute('version');
        $rootAttr->value = '2.0';
        $rootElem->appendChild($rootAttr);
        $newDoc->appendChild($rootElem);

        $channelElem = $newDoc->createElement('channel');
        $rootElem->appendChild($channelElem);

        $titleElem = $newDoc->createElement('title');
        $titleElem->appendChild($newDoc->createTextNode($DEFAULT_TITLE.' Biological Occurrences RSS feed'));
        $channelElem->appendChild($titleElem);

        $this->setServerDomain();
        $urlPathPrefix = $this->serverDomain.$CLIENT_ROOT.(substr($CLIENT_ROOT,-1) === '/'?'':'/');

        $localDomain = $this->serverDomain;

        $linkElem = $newDoc->createElement('link');
        $linkElem->appendChild($newDoc->createTextNode($urlPathPrefix));
        $channelElem->appendChild($linkElem);
        $descriptionElem = $newDoc->createElement('description');
        $descriptionElem->appendChild($newDoc->createTextNode($DEFAULT_TITLE.' Natural History Collections and Observation Project feed'));
        $channelElem->appendChild($descriptionElem);
        $languageElem = $newDoc->createElement('language','en-us');
        $channelElem->appendChild($languageElem);

        $sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.collectionguid, c.dwcaurl, c.managementtype, s.uploaddate '.
            'FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid '.
            'WHERE s.recordcnt > 0 '.
            'ORDER BY c.SortSeq, c.CollectionName';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_assoc()){
            $cArr = $this->utf8EncodeArr($r);
            $itemElem = $newDoc->createElement('item');
            $itemAttr = $newDoc->createAttribute('collid');
            $itemAttr->value = $cArr['collid'];
            $itemElem->appendChild($itemAttr);
            $instCode = $cArr['institutioncode'];
            if($cArr['collectioncode']) {
                $instCode .= '-' . $cArr['collectioncode'];
            }
            $title = $instCode;
            $itemTitleElem = $newDoc->createElement('title');
            $itemTitleElem->appendChild($newDoc->createTextNode($title));
            $itemElem->appendChild($itemTitleElem);
            if(strpos($cArr['icon'], 'images/collicons/') === 0){
                $imgLink = $urlPathPrefix.$cArr['icon'];
            }
            elseif(strpos($cArr['icon'], '/') === 0){
                $imgLink = $localDomain.$cArr['icon'];
            }
            else{
                $imgLink = $cArr['icon'];
            }
            $iconElem = $newDoc->createElement('image');
            $iconElem->appendChild($newDoc->createTextNode($imgLink));
            $itemElem->appendChild($iconElem);

            $descTitleElem = $newDoc->createElement('description');
            $descTitleElem->appendChild($newDoc->createTextNode($cArr['collectionname']));
            $itemElem->appendChild($descTitleElem);
            $guidElem = $newDoc->createElement('guid');
            $guidElem->appendChild($newDoc->createTextNode($cArr['collectionguid']));
            $itemElem->appendChild($guidElem);

            $emlElem = $newDoc->createElement('emllink');
            $emlElem->appendChild($newDoc->createTextNode($urlPathPrefix.'collections/datasets/emlhandler.php?collid='.$cArr['collid']));
            $itemElem->appendChild($emlElem);

            $link = $cArr['dwcaurl'];
            if(!$link){
                $link = $urlPathPrefix.'collections/misc/collprofiles.php?collid='.$cArr['collid'];
            }
            $typeTitleElem = $newDoc->createElement('type','DWCA');
            $itemElem->appendChild($typeTitleElem);

            $linkTitleElem = $newDoc->createElement('link');
            $linkTitleElem->appendChild($newDoc->createTextNode($link));
            $itemElem->appendChild($linkTitleElem);
            $dateStr = '';
            if($cArr['managementtype'] === 'Live Data'){
                $dateStr = date('D, d M Y H:i:s');
            }
            elseif($cArr['uploaddate']){
                $dateStr = date('D, d M Y H:i:s',strtotime($cArr['uploaddate']));
            }
            $pubDateTitleElem = $newDoc->createElement('pubDate');
            $pubDateTitleElem->appendChild($newDoc->createTextNode($dateStr));
            $itemElem->appendChild($pubDateTitleElem);
            $itemArr[$title] = $itemElem;
            $channelElem->appendChild($itemElem);
        }
        return $newDoc->saveXML();
    }

    private function writeOccurrenceFile(){
        global $CLIENT_ROOT;
        $this->logOrEcho('Creating occurrence file (' .date('h:i:s A'). ')... ');
        $filePath = $this->targetPath.$this->ts.'-occur'.$this->fileExt;
        $fh = fopen($filePath, 'wb');
        if(!$fh){
            $this->logOrEcho('ERROR establishing output file ('.$filePath.'), perhaps target folder is not readable by web server.');
            return false;
        }
        $hasRecords = false;

        if(!$this->occurrenceFieldArr){
            $this->occurrenceFieldArr = DwcArchiverOccurrence::getOccurrenceArr($this->schemaType, $this->extended);
        }
        $this->applyConditions();
        $sql = DwcArchiverOccurrence::getSqlOccurrences($this->occurrenceFieldArr['fields'],$this->conditionSql,$this->getTableJoins());
        if(!$sql) {
            return false;
        }
        if($this->schemaType !== 'backup') {
            $sql .= ' LIMIT 1000000';
        }

        $fieldArr = $this->occurrenceFieldArr['fields'];
        if($this->schemaType === 'dwc' || $this->schemaType === 'pensoft'){
            unset($fieldArr['localitySecurity']);
        }
        if($this->schemaType === 'dwc' || $this->schemaType === 'pensoft' || $this->schemaType === 'backup'){
            unset($fieldArr['collId']);
        }
        $fieldOutArr = array();
        if($this->schemaType === 'coge'){
            $glFields = array('specificEpithet'=>'Species','scientificNameAuthorship'=>'ScientificNameAuthor','recordedBy'=>'Collector','recordNumber'=>'CollectorNumber',
                'year'=>'YearCollected','month'=>'MonthCollected','day'=>'DayCollected','decimalLatitude'=>'Latitude','decimalLongitude'=>'Longitude',
                'minimumElevationInMeters'=>'MinimumElevation','maximumElevationInMeters'=>'MaximumElevation','maximumDepthInMeters'=>'MaximumDepth','minimumDepthInMeters'=>'MinimumDepth',
                'occurrenceRemarks'=>'Notes','dateEntered','dateLastModified','collId','recordId','references');
            foreach($fieldArr as $k => $v){
                if(array_key_exists($k,$glFields)){
                    $fieldOutArr[] = $glFields[$k];
                }
                else{
                    $fieldOutArr[] = strtoupper($k[0]).substr($k,1);
                }
            }
        }
        else{
            $fieldOutArr = array_keys($fieldArr);
        }
        $this->writeOutRecord($fh,$fieldOutArr);
        if(!$this->collArr){
            $sql1 = 'SELECT DISTINCT o.collid FROM omoccurrences o ';
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
        if($rs = $this->conn->query($sql,MYSQLI_USE_RESULT)){
            $this->setServerDomain();
            $urlPathPrefix = $this->serverDomain.$CLIENT_ROOT.(substr($CLIENT_ROOT,-1) === '/'?'':'/');
            $typeArr = null;
            if($this->schemaType === 'pensoft'){
                $typeArr = array('Other material', 'Holotype', 'Paratype', 'Isotype', 'Isoparatype', 'Isolectotype', 'Isoneotype', 'Isosyntype');
            }
            $statsManager = new OccurrenceAccessStats();
            while($r = $rs->fetch_assoc()){
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
                $hasRecords = true;
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
                if($managementType && $managementType === 'Live Data' && array_key_exists('collectionID', $r) && !$r['collectionID']) {
                    $guid = $this->collArr[$r['collid']]['collectionguid'];
                    if(strlen($guid) === 36) {
                        $guid = 'urn:uuid:' . $guid;
                    }
                    $r['collectionID'] = $guid;
                }
                if($this->schemaType === 'dwc'){
                    unset($r['localitySecurity'], $r['collid']);
                }
                elseif($this->schemaType === 'pensoft'){
                    unset($r['localitySecurity'], $r['collid']);
                    if($r['typeStatus']){
                        $typeValue = strtolower($r['typeStatus']);
                        $typeInvalid = true;
                        $invalidText = '';
                        foreach($typeArr as $testStr){
                            if ($typeValue === strtolower($testStr)) {
                                $typeInvalid = false;
                                break;
                            }

                            if(stripos($typeValue, $testStr)) {
                                $invalidText = $r['typeStatus'];
                                $r['typeStatus'] = $testStr;
                                $typeInvalid = false;
                                break;
                            }
                        }
                        if($typeInvalid){
                            $invalidText = $r['typeStatus'];
                            $r['typeStatus'] = 'Other material';
                        }
                        if($invalidText){
                            if($r['occurrenceRemarks']) {
                                $invalidText = $r['occurrenceRemarks'] . '; ' . $invalidText;
                            }
                            $r['occurrenceRemarks'] = $invalidText;
                        }
                    }
                    else{
                        $r['typeStatus'] = 'Other material';
                    }
                }
                elseif($this->schemaType === 'backup'){
                    unset($r['collid']);
                }
                if($this->upperTaxonomy){
                    $lcSciName = strtolower($r['scientificName']);
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
                $this->encodeArr($r);
                $this->addcslashesArr($r);
                $this->writeOutRecord($fh,$r);
            }
            $rs->free();
        }
        else{
            $this->logOrEcho('ERROR creating occurrence file: ' .$this->conn->error."\n");
            $this->logOrEcho("\tSQL: ".$sql."\n");
        }

        fclose($fh);
        if(!$hasRecords){
            $filePath = false;
            $this->logOrEcho("No records returned. Modify query variables to be more inclusive. \n");
        }
        $this->logOrEcho('Done!! (' .date('h:i:s A').")\n");
        return $filePath;
    }

    public function getOccurrenceFile(){
        if(!$this->targetPath) {
            $this->setTargetPath();
        }
        return $this->writeOccurrenceFile();
    }

    private function writeDeterminationFile(): ?bool
    {
        $this->logOrEcho('Creating identification file (' .date('h:i:s A'). ')... ');
        $filePath = $this->targetPath.$this->ts.'-det'.$this->fileExt;
        $fh = fopen($filePath, 'wb');
        if(!$fh){
            $this->logOrEcho('ERROR establishing output file ('.$filePath.'), perhaps target folder is not readable by web server.');
            return false;
        }

        if(!$this->determinationFieldArr){
            $this->determinationFieldArr = DwcArchiverDetermination::getDeterminationArr($this->schemaType,$this->extended);
        }
        $this->writeOutRecord($fh,array_keys($this->determinationFieldArr['fields']));

        $sql = DwcArchiverDetermination::getSqlDeterminations($this->determinationFieldArr['fields'],$this->conditionSql);
        if($rs = $this->conn->query($sql,MYSQLI_USE_RESULT)){
            while($r = $rs->fetch_assoc()){
                $r['recordId'] = 'urn:uuid:'.$r['recordId'];
                $this->encodeArr($r);
                $this->addcslashesArr($r);
                $this->writeOutRecord($fh,$r);
            }
            $rs->free();
        }
        else{
            $this->logOrEcho('ERROR creating identification file: ' .$this->conn->error."\n");
            $this->logOrEcho("\tSQL: ".$sql."\n");
        }

        fclose($fh);
        $this->logOrEcho('Done!! (' .date('h:i:s A').")\n");
        return true;
    }

    private function writeImageFile(): ?bool
    {
        global $CLIENT_ROOT, $IMAGE_DOMAIN;
        $this->logOrEcho('Creating image file (' .date('h:i:s A'). ')... ');
        $filePath = $this->targetPath.$this->ts.'-images'.$this->fileExt;
        $fh = fopen($filePath, 'wb');
        if(!$fh){
            $this->logOrEcho('ERROR establishing output file ('.$filePath.'), perhaps target folder is not readable by web server.');
            return false;
        }

        if(!$this->imageFieldArr) {
            $this->imageFieldArr = DwcArchiverImage::getImageArr($this->schemaType);
        }

        $this->writeOutRecord($fh,array_keys($this->imageFieldArr['fields']));

        $sql = DwcArchiverImage::getSqlImages($this->imageFieldArr['fields'], $this->conditionSql, $this->redactLocalities, $this->rareReaderArr);
        if($rs = $this->conn->query($sql,MYSQLI_USE_RESULT)){

            $this->setServerDomain();
            $urlPathPrefix = $this->serverDomain.$CLIENT_ROOT.(substr($CLIENT_ROOT,-1) === '/'?'':'/');

            if($IMAGE_DOMAIN){
                $localDomain = $IMAGE_DOMAIN;
            }
            else{
                $localDomain = $this->serverDomain;
            }

            while($r = $rs->fetch_assoc()){
                if(strpos($r['identifier'], '/') === 0) {
                    $r['identifier'] = $localDomain . $r['identifier'];
                }
                if(strpos($r['accessURI'], '/') === 0) {
                    $r['accessURI'] = $localDomain . $r['accessURI'];
                }
                if(strpos($r['thumbnailAccessURI'], '/') === 0) {
                    $r['thumbnailAccessURI'] = $localDomain . $r['thumbnailAccessURI'];
                }
                if(strpos($r['goodQualityAccessURI'], '/') === 0) {
                    $r['goodQualityAccessURI'] = $localDomain . $r['goodQualityAccessURI'];
                }

                if($this->schemaType !== 'backup'){
                    if(stripos($r['rights'],'http://creativecommons.org') === 0){
                        $r['webstatement'] = $r['rights'];
                        $r['rights'] = '';
                        if(!$r['usageterms']){
                            if($r['webstatement'] === 'http://creativecommons.org/publicdomain/zero/1.0/'){
                                $r['usageterms'] = 'CC0 1.0 (Public-domain)';
                            }
                            elseif($r['webstatement'] === 'http://creativecommons.org/licenses/by/3.0/'){
                                $r['usageterms'] = 'CC BY (Attribution)';
                            }
                            elseif($r['webstatement'] === 'http://creativecommons.org/licenses/by-sa/3.0/'){
                                $r['usageterms'] = 'CC BY-SA (Attribution-ShareAlike)';
                            }
                            elseif($r['webstatement'] === 'http://creativecommons.org/licenses/by-nc/3.0/'){
                                $r['usageterms'] = 'CC BY-NC (Attribution-Non-Commercial)';
                            }
                            elseif($r['webstatement'] === 'http://creativecommons.org/licenses/by-nc-sa/3.0/'){
                                $r['usageterms'] = 'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)';
                            }
                        }
                    }
                    if(!$r['usageterms']) {
                        $r['usageterms'] = 'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)';
                    }
                }
                $r['providermanagedid'] = 'urn:uuid:'.$r['providermanagedid'];
                $r['associatedSpecimenReference'] = $urlPathPrefix.'collections/individual/index.php?occid='.$r['occid'];
                $r['type'] = 'StillImage';
                $r['subtype'] = 'Photograph';
                $extStr = strtolower(substr($r['accessURI'],strrpos($r['accessURI'],'.')+1));
                if($r['format'] === ''){
                    if($extStr === 'jpg' || $extStr === 'jpeg'){
                        $r['format'] = 'image/jpeg';
                    }
                    elseif($extStr === 'gif'){
                        $r['format'] = 'image/gif';
                    }
                    elseif($extStr === 'png'){
                        $r['format'] = 'image/png';
                    }
                    elseif($extStr === 'tiff' || $extStr === 'tif'){
                        $r['format'] = 'image/tiff';
                    }
                    else{
                        $r['format'] = '';
                    }
                }
                $r['metadataLanguage'] = 'en';
                $this->writeOutRecord($fh,$r);
            }
            $rs->free();
        }
        else{
            $this->logOrEcho('ERROR creating image file: ' .$this->conn->error."\n");
            $this->logOrEcho("\tSQL: ".$sql."\n");
        }

        fclose($fh);

        $this->logOrEcho('Done!! (' .date('h:i:s A').")\n");
        return true;
    }

    private function writeAttributeFile(): ?bool
    {
        $this->logOrEcho('Creating occurrence Attributes file as MeasurementsOrFact extension (' .date('h:i:s A'). ')... ');
        $filePath = $this->targetPath.$this->ts.'-attr'.$this->fileExt;
        $fh = fopen($filePath, 'wb');
        if(!$fh){
            $this->logOrEcho('ERROR establishing output file ('.$filePath.'), perhaps target folder is not readable by web server.');
            return false;
        }

        if(!$this->attributeFieldArr) {
            $this->attributeFieldArr = DwcArchiverAttribute::getFieldArr();
        }

        $this->writeOutRecord($fh,array_keys($this->attributeFieldArr['fields']));

        $sql = DwcArchiverAttribute::getSql($this->attributeFieldArr['fields'],$this->conditionSql);
        //echo $sql; exit;
        if($rs = $this->conn->query($sql,MYSQLI_USE_RESULT)){
            while($r = $rs->fetch_assoc()){
                $this->encodeArr($r);
                $this->addcslashesArr($r);
                $this->writeOutRecord($fh,$r);
            }
            $rs->free();
        }
        else{
            $this->logOrEcho('ERROR creating attribute (MeasurementOrFact file: ' .$this->conn->error."\n");
            $this->logOrEcho("\tSQL: ".$sql."\n");
        }

        fclose($fh);
        $this->logOrEcho('Done!! (' .date('h:i:s A').")\n");
        return true;
    }

    private function writeOutRecord($fh,$outputArr): void
    {
        if($this->delimiter === ','){
            fputcsv($fh, $outputArr);
        }
        else{
            foreach($outputArr as $k => $v){
                $outputArr[$k] = str_replace($this->delimiter,'',$v);
            }
            fwrite($fh, implode($this->delimiter,$outputArr)."\n");
        }
    }

    public function deleteArchive($collID): bool
    {
        global $SERVER_ROOT;
        $rssFile = $SERVER_ROOT.(substr($SERVER_ROOT,-1) === '/'?'':'/').'webservices/dwc/rss.xml';
        if(!file_exists($rssFile)) {
            return false;
        }
        $doc = new DOMDocument();
        $doc->load($rssFile);
        $cElem = $doc->getElementsByTagName('channel')->item(0);
        if($cElem){
            $items = $cElem->getElementsByTagName('item');
            foreach($items as $i){
                if($i->getAttribute('collid') === $collID){
                    $link = $i->getElementsByTagName('link');
                    $nodeValue = $link->item(0)->nodeValue;
                    $filePath = $SERVER_ROOT.(substr($SERVER_ROOT,-1) === '/'?'':'/');
                    $filePath1 = $filePath.'content/dwca'.substr($nodeValue,strrpos($nodeValue,'/'));
                    if(file_exists($filePath1)) {
                        unlink($filePath1);
                    }
                    $emlPath1 = str_replace('.zip','.eml',$filePath1);
                    if(file_exists($emlPath1)) {
                        unlink($emlPath1);
                    }
                    $filePath2 = $filePath.'collections/datasets/dwc'.substr($nodeValue,strrpos($nodeValue,'/'));
                    if(file_exists($filePath2)) {
                        unlink($filePath2);
                    }
                    $emlPath2 = str_replace('.zip','.eml',$filePath2);
                    if(file_exists($emlPath2)) {
                        unlink($emlPath2);
                    }
                    $cElem->removeChild($i);
                }
            }
            $doc->save($rssFile);
            $sql = 'UPDATE omcollections SET dwcaUrl = NULL WHERE collid = '.$collID;
            if(!$this->conn->query($sql)){
                $this->logOrEcho('ERROR nullifying dwcaUrl while removing DWCA instance: '.$this->conn->error);
                return false;
            }
            return true;
        }
        return false;
    }

    private function setUpperTaxonomy(): void
    {
        if(!$this->upperTaxonomy){
            $sqlOrder = 'SELECT t.sciname AS family, t2.sciname AS taxonorder '.
                'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
                'INNER JOIN taxa t2 ON e.parenttid = t2.tid '.
                'WHERE t.rankid = 140 AND t2.rankid = 100';
            $rsOrder = $this->conn->query($sqlOrder);
            while($rowOrder = $rsOrder->fetch_object()){
                $this->upperTaxonomy[strtolower($rowOrder->family)]['o'] = $rowOrder->taxonorder;
            }
            $rsOrder->free();

            $sqlClass = 'SELECT t.sciname AS orderName, t2.sciname AS taxonclass '.
                'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
                'INNER JOIN taxa t2 ON e.parenttid = t2.tid '.
                'WHERE t.rankid = 100 AND t2.rankid = 60';
            $rsClass = $this->conn->query($sqlClass);
            while($rowClass = $rsClass->fetch_object()){
                $this->upperTaxonomy[strtolower($rowClass->orderName)]['c'] = $rowClass->taxonclass;
            }
            $rsClass->free();

            $sqlPhylum = 'SELECT t.sciname AS className, t2.sciname AS taxonphylum '.
                'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
                'INNER JOIN taxa t2 ON e.parenttid = t2.tid '.
                'WHERE t.rankid = 60 AND t2.rankid = 30';
            $rsPhylum = $this->conn->query($sqlPhylum);
            while($rowPhylum = $rsPhylum->fetch_object()){
                $this->upperTaxonomy[strtolower($rowPhylum->className)]['p'] = $rowPhylum->taxonphylum;
            }
            $rsPhylum->free();

            $sqlKing = 'SELECT t.sciname AS phylum, t2.sciname AS kingdom '.
                'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
                'INNER JOIN taxa t2 ON e.parenttid = t2.tid '.
                'WHERE t.rankid = 30 AND t2.rankid = 10';
            $rsKing = $this->conn->query($sqlKing);
            while($rowKing = $rsKing->fetch_object()){
                $this->upperTaxonomy[strtolower($rowKing->phylum)]['k'] = $rowKing->kingdom;
            }
            $rsKing->free();
        }
    }

    public function setSchemaType($type): void
    {
        if(in_array($type, array('dwc','backup','coge','pensoft'))){
            $this->schemaType = $type;
        }
        else{
            $this->schemaType = 'symbiota';
        }
    }

    public function setLimitToGuids($testValue): void
    {
        if($testValue) {
            $this->limitToGuids = true;
        }
    }

    public function setExtended($e): void
    {
        $this->extended = $e;
    }

    public function setDelimiter($d): void
    {
        if($d === 'tab' || $d === "\t"){
            $this->delimiter = "\t";
            $this->fileExt = '.tab';
        }
        elseif($d === 'csv' || $d === 'comma' || $d === ','){
            $this->delimiter = ',';
            $this->fileExt = '.csv';
        }
        else{
            $this->delimiter = $d;
            $this->fileExt = '.txt';
        }
    }

    public function setIncludeDets($includeDets): void
    {
        $this->includeDets = $includeDets;
    }

    public function setIncludeImgs($includeImgs): void
    {
        $this->includeImgs = $includeImgs;
    }

    public function setIncludeAttributes($include): void
    {
        $this->includeAttributes = $include;
    }

    public function setRedactLocalities($redact): void
    {
        $this->redactLocalities = $redact;
    }

    public function setRareReaderArr($approvedCollid): void
    {
        if(is_array($approvedCollid)){
            $this->rareReaderArr = $approvedCollid;
        }
        elseif(is_string($approvedCollid)){
            $this->rareReaderArr = explode(',',$approvedCollid);
        }
    }

    public function setIsPublicDownload(): void
    {
        $this->isPublicDownload = true;
    }

    public function setCharSetOut($cs): void
    {
        $cs = strtoupper($cs);
        if($cs === 'ISO-8859-1' || $cs === 'UTF-8'){
            $this->charSetOut = $cs;
        }
    }

    public function setGeolocateVariables($geolocateArr): void
    {
        $this->geolocateVariables = $geolocateArr;
    }

    public function setCollID($id): void
    {
        $this->collID = $id;
    }

    public function setServerDomain($domain = ''): void
    {
        if($domain){
            $this->serverDomain = $domain;
        }
        elseif(!$this->serverDomain){
            $this->serverDomain = 'http://';
            if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
                $this->serverDomain = 'https://';
            }
            $this->serverDomain .= $_SERVER['HTTP_HOST'];
            if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
                $this->serverDomain .= ':' . $_SERVER['SERVER_PORT'];
            }
        }
    }

    public function getServerDomain(){
        $this->setServerDomain();
        return $this->serverDomain;
    }

    protected function utf8EncodeArr($inArr){
        $retArr = $inArr;
        if($this->charSetSource === 'ISO-8859-1'){
            foreach($retArr as $k => $v){
                if(is_array($v)){
                    $retArr[$k] = $this->utf8EncodeArr($v);
                }
                elseif(is_string($v)){
                    if(mb_detect_encoding($v,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
                        $retArr[$k] = utf8_encode($v);
                    }
                }
                else{
                    $retArr[$k] = $v;
                }
            }
        }
        return $retArr;
    }

    private function encodeArr(&$inArr): void
    {
        if($this->charSetSource && $this->charSetOut !== $this->charSetSource){
            foreach($inArr as $k => $v){
                $inArr[$k] = $this->encodeStr($v);
            }
        }
    }

    private function encodeStr($inStr){
        $retStr = $inStr;
        if($inStr && $this->charSetSource){
            if($this->charSetOut === 'UTF-8' && $this->charSetSource === 'ISO-8859-1'){
                if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
                    $retStr = utf8_encode($inStr);
                }
            }
            elseif($this->charSetOut === 'ISO-8859-1' && $this->charSetSource === 'UTF-8'){
                if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
                    $retStr = utf8_decode($inStr);
                }
            }
        }
        return $retStr;
    }

    private function addcslashesArr(&$arr): void
    {
        foreach($arr as $k => $v){
            if($v) {
                $arr[$k] = addcslashes($v, "\n\r\\");
            }
        }
    }
}
