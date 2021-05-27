<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/OccurrenceEditorManager.php');

class OccurrenceDuplicate {

    private $conn;
    private $obsUid;
    private $relevantFields = array();

    private $errorStr;

    public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if(!($this->conn === null)) {
            $this->conn->close();
        }
    }

    public function getClusterArr($occid){
        $retArr = array();
        $sql1 = 'SELECT DISTINCT d.duplicateid, d.title, d.description, d.notes '.
            'FROM omoccurduplicates d INNER JOIN omoccurduplicatelink l ON d.duplicateid = l.duplicateid '.
            'WHERE (l.occid = '.$occid.')';
        if($rs1 = $this->conn->query($sql1)){
            while($r1 = $rs1->fetch_object()){
                $retArr[$r1->duplicateid]['title'] = $r1->title;
                $retArr[$r1->duplicateid]['description'] = $r1->description;
                $retArr[$r1->duplicateid]['notes'] = $r1->notes;
            }
            $rs1->close();
        }
        else{
            $this->errorStr = 'ERROR getting list of duplicate records [1]: '.$this->conn->error;
            $retArr = false;
        }

        if($retArr){
            $sql = 'SELECT d.duplicateid, d.occid, c.institutioncode, c.collectioncode, c.collectionname, o.catalognumber, '.
                'o.occurrenceid, o.sciname, o.identifiedby, o.dateidentified, '.
                'o.recordedby, o.recordnumber, o.eventdate, d.notes, i.url, i.thumbnailurl '.
                'FROM omoccurduplicatelink d INNER JOIN omoccurrences o ON d.occid = o.occid '.
                'INNER JOIN omcollections c ON o.collid = c.collid '.
                'LEFT JOIN images i ON o.occid = i.occid '.
                'WHERE (d.duplicateid IN('.implode(',',array_keys($retArr)).'))';
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $retArr[$r->duplicateid]['o'][$r->occid] = array('instcode' => $r->institutioncode, 'collcode' => $r->collectioncode,
                        'collname' => $r->collectionname, 'catnum' => $r->catalognumber, 'occurrenceid' => $r->occurrenceid, 'sciname' => $r->sciname,
                        'identifiedby' => $r->identifiedby, 'dateidentified' => $r->dateidentified, 'recordedby' => $r->recordedby,
                        'recordnumber' => $r->recordnumber, 'eventdate' => $r->eventdate, 'notes' => $r->notes, 'tnurl' => $r->thumbnailurl,
                        'url' => $r->url);
                }
                $rs->free();
            }
            else{
                $this->errorStr = 'ERROR getting list of duplicate records [2]: '.$this->conn->error;
                $retArr = false;
            }
        }
        return $retArr;
    }

    public function linkDuplicates($occid1,$occidStr,$dupTitle=''): bool
    {
        $status = true;
        if($occid1 && $occidStr){
            $dupArr = array();
            $sql = 'SELECT occid, duplicateid FROM omoccurduplicatelink WHERE occid IN('.$occid1.','.$occidStr.')';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $dupArr[$r->duplicateid] = $r->occid;
            }
            $rs->free();
            if(count($dupArr) === 1){
                $targetDupID = key($dupArr);
            }
            elseif(count($dupArr) > 1){
                $targetDupID = $this->mergeClusters(array_keys($dupArr));
            }
            else{
                $targetDupID = $this->createCluster($occid1,$dupTitle);
            }
            if($targetDupID){
                $sql2 = 'INSERT IGNORE INTO omoccurduplicatelink(duplicateid,occid) '.
                    'VALUES('.$targetDupID.','.$occid1.'),('.$targetDupID.','.$occidStr.')';
                if(!$this->conn->query($sql2)){
                    $status = false;
                    $this->errorStr = 'ERROR linking occurrences to duplicate cluster: '.$this->conn->error;
                }
            }
            else{
                $status = false;
            }
        }
        return $status;
    }

    private function createCluster($occid,$title=''){
        $retId = 0;
        if(!$title){
            $sqlTitle = 'SELECT recordedby, recordnumber, eventdate '.
                'FROM omoccurrences '.
                'WHERE occid = '.$occid;
            $rsTitle = $this->conn->query($sqlTitle);
            while($r = $rsTitle->fetch_object()){
                $title = $this->parseLastName($r->recordedby).' '.$r->recordnumber.' '.$r->eventdate;
            }
            $rsTitle->free();
            if(!$title) {
                $title = 'Undefined Collector';
            }
        }
        $sql1 = 'INSERT INTO omoccurduplicates(title,dupetype) VALUES("'.$this->cleanInStr($title).'",1)';
        if($this->conn->query($sql1)){
            $retId = $this->conn->insert_id;
        }
        else{
            $this->errorStr = 'ERROR creating new dupliate cluster: '.$this->conn->error;
        }
        return $retId;
    }

    private function mergeClusters($dupArr){
        $targetId = 0;
        if(count($dupArr)>1){
            $targetId = min($dupArr);
            unset($dupArr[array_search($targetId, $dupArr, true)]);
            $sql = 'UPDATE omoccurduplicatelink SET duplicateid = '.$targetId.' WHERE duplicateid IN('.$dupArr.')';
            if($this->conn->query($sql)){
                if(!$this->conn->query('DELETE FROM omoccurduplicates WHERE duplicateid IN('.$dupArr.')')){
                    $this->errorStr = 'ERROR merging duplicate clusters: '.$this->conn->error;
                }
            }
            else{
                $this->errorStr = 'ERROR removing extract duplicate cluster: '.$this->conn->error;
            }
        }
        return $targetId;
    }

    public function editCluster($dupId, $title, $description, $notes): bool
    {
        $status = true;
        $sql = 'UPDATE omoccurduplicates SET title = '.($title?'"'.$this->cleanInStr($title).'"':'NULL').', '.
            'description = '.($description?'"'.$this->cleanInStr($description).'"':'NULL').', '.
            'notes = '.($notes?'"'.$this->cleanInStr($notes).'"':'NULL').' '.
            'WHERE (duplicateid = '.$dupId.')';
        //echo $sql;
        if(!$this->conn->query($sql)){
            $this->errorStr = 'ERROR editing duplicate cluster: '.$this->conn->error;
            $status = false;
        }
        return $status;
    }

    public function deleteOccurFromCluster($dupId, $occid): bool
    {
        $status = true;
        $rs = $this->conn->query('SELECT duplicateid FROM omoccurduplicatelink WHERE duplicateid = '.$dupId);
        if($rs->num_rows === 2){
            $sql = 'DELETE FROM omoccurduplicates WHERE (duplicateid = '.$dupId.')';
            if(!$this->conn->query($sql)){
                $this->errorStr = 'ERROR deleting duplicate cluster: '.$this->conn->error;
                $status = false;
            }
        }
        else{
            $sql = 'DELETE FROM omoccurduplicatelink WHERE (duplicateid = '.$dupId.') AND (occid = '.$occid.')';
            if(!$this->conn->query($sql)){
                $this->errorStr = 'ERROR deleting occurrence from duplicate cluster: '.$this->conn->error;
                $status = false;
            }
        }
        return $status;
    }

    public function deleteCluster($dupId): bool
    {
        $status = true;
        $sql = 'DELETE FROM omoccurduplicates WHERE duplicateid = '.$dupId;
        if(!$this->conn->query($sql)){
            $this->errorStr = 'ERROR deleting duplicate cluster: '.$this->conn->error;
            $status = false;
        }
        return $status;
    }

    public function getDupes($collName, $collNum, $collDate, $ometid, $exsNumber, $currentOccid): string
    {
        $retStr = '';
        $collName = $this->cleanInStr($collName);
        $collNum = $this->cleanInStr($collNum);
        $collDate = $this->cleanInStr($collDate);
        $exsNumber = $this->cleanInStr($exsNumber);
        if(!is_numeric($currentOccid)) {
            $currentOccid = 0;
        }
        if(is_numeric($ometid) && $exsNumber){
            $occArr = $this->getDupesExsiccati($ometid, $exsNumber, $currentOccid);
            if($occArr){
                $retStr = 'exsic:'.implode(',',$occArr);
            }
        }

        if(!$retStr){
            $occArr = $this->getDupesCollector($collName, $collNum, $currentOccid);
            if($occArr){
                $retStr = 'exact:'.implode(',',$occArr);
            }
        }

        if(!$retStr){
            $occArr = $this->getDupesCollectorEvent($collName, $collNum, $collDate, $currentOccid);
            if($occArr){
                $retStr = 'event:'.implode(',',$occArr);
            }
        }
        return $retStr;
    }

    private function getDupesExsiccati($ometid, $exsNumber, $currentOccid): array
    {
        $retArr = array();
        $sql = 'SELECT el.occid '.
            'FROM omexsiccatiocclink el INNER JOIN omexsiccatinumbers en ON el.omenid = en.omenid '.
            'WHERE (en.ometid = '.$ometid.') AND (en.exsnumber = "'.$exsNumber.'") AND (occid != '.$currentOccid.') ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr[$r->occid] = $r->occid;
        }
        $rs->free();
        return $retArr;
    }

    private function getDupesCollector($collName, $collNum, $skipOccid): array
    {
        $retArr = array();
        $lastName = $this->parseLastName($collName);
        if($lastName && $collNum){
            $sql = 'SELECT o.occid FROM omoccurrences o ';
            if(strlen($lastName) < 4 || strtolower($lastName) === 'best'){
                $sql .= 'WHERE (o.recordedby LIKE "%'.$lastName.'%") ';
            }
            else{
                $sql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid '.
                    'WHERE (MATCH(f.recordedby) AGAINST("'.$lastName.'")) ';
            }
            $sql .= 'AND (o.recordnumber = "'.$collNum.'") AND (o.occid != '.$skipOccid.') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->occid] = $r->occid;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getDupesCatalogNumber($catNum, $collid, $skipOccid): array
    {
        $retArr = array();
        if(is_numeric($collid) && is_numeric($skipOccid) && $catNum){
            $catNumber = $this->cleanInStr($catNum);
            $sql = 'SELECT occid FROM omoccurrences '.
                'WHERE (catalognumber = "'.$catNumber.'") AND (collid = '.$collid.') AND (occid != '.$skipOccid.') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->occid] = $r->occid;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getDupesOtherCatalogNumbers($otherCatNum, $collid, $skipOccid): array
    {
        $retArr = array();
        if(is_numeric($collid) && is_numeric($skipOccid) && $otherCatNum){
            $sql = 'SELECT occid FROM omoccurrences '.
                'WHERE (othercatalognumbers = "'.$this->cleanInStr($otherCatNum).'") AND (collid = '.$collid.') AND (occid != '.$skipOccid.') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->occid] = $r->occid;
            }
            $rs->free();
        }
        return $retArr;
    }

    private function getDupesCollectorEvent($collName, $collNum, $collDate, $skipOccid): array
    {
        $retArr = array();
        $lastName = $this->parseLastName($collName);
        if($lastName){
            $sql = 'SELECT o.occid FROM omoccurrences o ';
            $sql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid '.
                'WHERE f.recordedby LIKE "%'.$lastName.'%" ';
            $sql .= 'AND (o.processingstatus IS NULL OR o.processingstatus != "unprocessed" OR o.locality IS NOT NULL) AND (o.occid != '.$skipOccid.') ';

            $runQry = true;
            if($collNum){
                if(is_numeric($collNum)){
                    $nStart = $collNum - 4;
                    if($nStart < 1) {
                        $nStart = 1;
                    }
                    $nEnd = $collNum + 4;
                    $sql .= 'AND (o.recordnumber BETWEEN '.$nStart.' AND '.$nEnd.') ';
                }
                elseif(preg_match('/^(\d+)-?[a-zA-Z]{1,2}$/',$collNum,$m)){
                    $cNum = $m[1];
                    $nStart = $cNum - 4;
                    if($nStart < 1) {
                        $nStart = 1;
                    }
                    $nEnd = $cNum + 4;
                    $sql .= 'AND (CAST(o.recordnumber AS SIGNED) BETWEEN '.$nStart.' AND '.$nEnd.') ';
                }
                elseif(preg_match('/^(\D+-?)(\d+)-?[a-zA-Z]{0,2}$/',$collNum,$m)){
                    $prefix = $m[1];
                    $num = $m[2];
                    $nStart = $num - 5;
                    if($nStart < 1) {
                        $nStart = 1;
                    }
                    $rangeArr = array();
                    for($x=1;$x<11;$x++){
                        $rangeArr[] = $prefix.($nStart+$x);
                    }
                    $sql .= 'AND o.recordnumber IN("'.implode('","',$rangeArr).'") ';
                }
                elseif(preg_match('/^(\d{2,4}-)(\d+)-?[a-zA-Z]{0,2}$/',$collNum,$m)){
                    $prefix = $m[1];
                    $num = $m[2];
                    $nStart = $num - 5;
                    if($nStart < 1) {
                        $nStart = 1;
                    }
                    $rangeArr = array();
                    for($x=1;$x<11;$x++){
                        $rangeArr[] = $prefix.($nStart+$x);
                    }
                    $sql .= 'AND o.recordnumber IN("'.implode('","',$rangeArr).'") ';
                }
                else{
                    $runQry = false;
                }
                if($collDate) {
                    $sql .= 'AND (o.eventdate = "' . $collDate . '") ';
                }
            }
            elseif($collDate){
                $sql .= 'AND (o.eventdate = "'.$collDate.'") LIMIT 10';
            }
            else{
                $runQry = false;
            }
            if($runQry){
                //echo $sql;
                $result = $this->conn->query($sql);
                while ($r = $result->fetch_object()) {
                    $retArr[$r->occid] = $r->occid;
                }
                $result->free();
            }
        }
        return $retArr;
    }

    public function getDupesOccid($occidQuery): array
    {
        $retArr = array();
        if($occidQuery){
            $targetFields = array('o.family', 'o.sciname', 'o.scientificNameAuthorship',
                'o.identifiedBy', 'o.dateIdentified', 'o.identificationReferences', 'o.identificationRemarks', 'o.taxonRemarks', 'o.identificationQualifier',
                'o.recordedBy', 'o.recordNumber', 'o.associatedCollectors', 'o.eventDate', 'o.verbatimEventDate',
                'o.country', 'o.stateProvince', 'o.county', 'o.locality', 'o.decimalLatitude', 'o.decimalLongitude', 'o.geodeticDatum',
                'o.coordinateUncertaintyInMeters', 'o.verbatimCoordinates', 'o.georeferencedBy', 'o.georeferenceProtocol',
                'o.georeferenceSources', 'o.georeferenceVerificationStatus', 'o.georeferenceRemarks', 'o.locationRemarks',
                'o.minimumElevationInMeters', 'o.maximumElevationInMeters', 'o.verbatimElevation', 'o.fieldnumber', 'o.locationID',
                'o.habitat', 'o.substrate', 'o.occurrenceRemarks', 'o.associatedTaxa', 'o.dynamicProperties',
                'o.verbatimAttributes','o.reproductiveCondition', 'o.cultivationStatus', 'o.establishmentMeans', 'o.typeStatus');
            $relArr = array();
            $sql = 'SELECT c.collectionName, c.institutionCode, c.collectionCode, o.occid, o.collid, o.tidinterpreted, '.
                'o.catalogNumber, o.otherCatalogNumbers, '.implode(',',$targetFields).
                ' FROM omcollections c INNER JOIN omoccurrences o ON c.collid = o.collid '.
                'WHERE (o.occid IN('.$occidQuery.')) '.
                'ORDER BY recordnumber';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_assoc()) {
                foreach($row as $k => $v){
                    $vStr = trim($v);
                    $retArr[$row['occid']][$k] = $vStr;
                    if($vStr) {
                        $relArr[$k] = '';
                    }
                }
            }
            $result->free();
            foreach($targetFields as $tfVal){
                if(array_key_exists($tfVal,$relArr)) {
                    $this->relevantFields[] = $tfVal;
                }
            }
        }
        return $retArr;
    }

    public function getDupeList($recordedBy, $recordNumber, $eventDate, $catNum, $occid, $currentOccid): array
    {
        $retArr = array();
        if(!is_numeric($currentOccid)) {
            return $retArr;
        }

        $queryTerms = array();
        $recordedBy = $this->cleanInStr($recordedBy);
        if($recordedBy){
            if(strlen($recordedBy) < 4 || strtolower($recordedBy) === 'best'){
                $queryTerms[] = '(o.recordedby LIKE "%'.$recordedBy.'%")';
            }
            else{
                $queryTerms[] = 'MATCH(f.recordedby) AGAINST("'.$recordedBy.'")';
            }
        }
        if($recordNumber) {
            $queryTerms[] = 'o.recordnumber = "' . $this->cleanInStr($recordNumber) . '"';
        }
        if($eventDate) {
            $queryTerms[] = 'o.eventdate = "' . $this->cleanInStr($eventDate) . '"';
        }
        if($catNum) {
            $queryTerms[] = 'o.catalognumber = "' . $this->cleanInStr($catNum) . '"';
        }
        if(is_numeric($occid)) {
            $queryTerms[] = 'o.occid = ' . $occid;
        }
        $sql = 'SELECT c.institutioncode, c.collectioncode, c.collectionname, o.occid, o.catalognumber, '.
            'o.recordedby, o.recordnumber, o.eventdate, o.verbatimeventdate, o.country, o.stateprovince, o.county, o.locality '.
            'FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid ';
        if($recordedBy) {
            $sql .= 'LEFT JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
        }
        $sql .= 'WHERE o.occid != '.$currentOccid;
        if($queryTerms){
            $sql .= ' AND ('.implode(') AND (', $queryTerms).') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->occid]['collname'] = $r->collectionname.' ('.$r->institutioncode.($r->collectioncode?'-'.$r->collectioncode:'').')';
                $retArr[$r->occid]['catalognumber'] = $r->catalognumber;
                $retArr[$r->occid]['recordedby'] = $r->recordedby;
                $retArr[$r->occid]['recordnumber'] = $r->recordnumber;
                $retArr[$r->occid]['eventdate'] = $r->eventdate;
                $retArr[$r->occid]['verbatimeventdate'] = $r->verbatimeventdate;
                $retArr[$r->occid]['country'] = $r->country;
                $retArr[$r->occid]['stateprovince'] = $r->stateprovince;
                $retArr[$r->occid]['county'] = $r->county;
                $retArr[$r->occid]['locality'] = $r->locality;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getDupeLocality($recordedBy, $collDate, $localFrag): array
    {
        $retArr = array();
        if($recordedBy && $collDate && $localFrag){
            $locArr = Array('associatedcollectors','verbatimeventdate','country','stateprovince','county','municipality','locality',
                'decimallatitude','decimallongitude','verbatimcoordinates','coordinateuncertaintyinmeters','geodeticdatum','minimumelevationinmeters',
                'maximumelevationinmeters','verbatimelevation','verbatimcoordinates','georeferencedby','georeferenceprotocol','georeferencesources',
                'georeferenceverificationstatus','georeferenceremarks','habitat','substrate','associatedtaxa');
            $collStr = $this->cleanInStr($recordedBy);
            $sql = 'SELECT DISTINCT o.'.implode(',o.',$locArr).' FROM omoccurrences o ';
            if(strlen($collStr) < 4 || strtolower($collStr) === 'best'){
                $sql .= 'WHERE (o.recordedby LIKE "%'.$collStr.'%") ';
            }
            else{
                $sql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid WHERE (MATCH(f.recordedby) AGAINST("'.$collStr.'")) ';
            }
            $sql .= 'AND (o.eventdate = "'.$this->cleanInStr($collDate).'") AND (o.locality LIKE "'.$this->cleanInStr($localFrag).'%") ';

            //echo $sql;
            $rs = $this->conn->query($sql);
            $cnt = 0;
            while($r = $rs->fetch_assoc()){
                foreach($locArr as $field){
                    if($r[$field]) {
                        $retArr[$cnt][$field] = $r[$field];
                    }
                }
                $loc = $r['locality'];
                if($r['decimallatitude']) {
                    $loc .= '; ' . $r['decimallatitude'] . ' ' . $r['decimallongitude'];
                }
                $retArr[$cnt]['value'] = $loc;
                $cnt++;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function mergeRecords($targetOccid,$sourceOccid): bool
    {
        $status = true;
        $editorManager = new OccurrenceEditorManager($this->conn);
        if($editorManager->mergeRecords($targetOccid,$sourceOccid)){
            if(!$editorManager->deleteOccurrence($sourceOccid)){
                $this->errorStr = $editorManager->getErrorStr();
            }
        }
        else{
            $this->errorStr = $editorManager->getErrorStr();
            $status = false;
        }
        return $status;
    }

    public function getDuplicateClusterList($collid, $dupeDepth, $start, $limit): array
    {
        $retArr = array();
        if($collid){
            $sqlPrefix = 'SELECT DISTINCT d.duplicateid, d.title, d.description, d.notes ';
            if($dupeDepth === 1){
                $sqlSuffix = 'FROM omoccurduplicates d INNER JOIN omoccurduplicatelink dl1 ON d.duplicateid = dl1.duplicateid '.
                    'INNER JOIN omoccurrences o ON dl1.occid = o.occid '.
                    'INNER JOIN omoccurduplicatelink dl2 ON d.duplicateid = dl2.duplicateid '.
                    'INNER JOIN omoccurrences o2 ON dl2.occid = o2.occid '.
                    'WHERE o.collid = '.$collid.($this->obsUid?' AND o.observeruid = '.$this->obsUid:'').' AND o.tidinterpreted <> o2.tidinterpreted ';
            }
            elseif($dupeDepth === 2){
                $sqlSuffix = 'FROM omoccurduplicates d INNER JOIN omoccurduplicatelink dl1 ON d.duplicateid = dl1.duplicateid '.
                    'INNER JOIN omoccurrences o ON dl1.occid = o.occid '.
                    'INNER JOIN omoccurduplicatelink dl2 ON d.duplicateid = dl2.duplicateid '.
                    'INNER JOIN omoccurrences o2 ON dl2.occid = o2.occid '.
                    'WHERE o.collid = '.$collid.($this->obsUid?' AND o.observeruid = '.$this->obsUid:'').' AND o.tidinterpreted <> o2.tidinterpreted '.
                    'AND (o2.dateidentified IS NOT NULL OR o2.identifiedBy IS NOT NULL) ';
            }
            elseif($dupeDepth === 3){
                $sqlSuffix = 'FROM omoccurduplicates d INNER JOIN omoccurduplicatelink dl1 ON d.duplicateid = dl1.duplicateid '.
                    'INNER JOIN omoccurrences o ON dl1.occid = o.occid '.
                    'INNER JOIN omoccurduplicatelink dl2 ON d.duplicateid = dl2.duplicateid '.
                    'INNER JOIN omoccurrences o2 ON dl2.occid = o2.occid '.
                    'INNER JOIN omoccurdeterminations i ON o2.occid = i.occid '.
                    'WHERE o.collid = '.$collid.($this->obsUid?' AND o.observeruid = '.$this->obsUid:'').' AND o.tidinterpreted <> o2.tidinterpreted '.
                    'AND (o2.dateidentified IS NOT NULL OR o2.identifiedBy IS NOT NULL) ';
            }
            else{
                $sqlSuffix = 'FROM omoccurduplicates d INNER JOIN omoccurduplicatelink dl ON d.duplicateid = dl.duplicateid '.
                    'INNER JOIN omoccurrences o ON dl.occid = o.occid '.
                    'WHERE o.collid = '.$collid.($this->obsUid?' AND o.observeruid = '.$this->obsUid:'');
            }
            $totalCnt = 0;
            $sql = 'SELECT count(DISTINCT d.duplicateid) as cnt '.$sqlSuffix;
            //echo $sql;
            $rsCnt = $this->conn->query($sql);
            if($rCnt = $rsCnt->fetch_object()){
                $totalCnt = $rCnt->cnt;
            }
            $rsCnt->free();

            $sql = $sqlPrefix.$sqlSuffix.' ORDER BY o.recordedby,o.recordnumber LIMIT '.$start.','.$limit;
            //echo 'sql: '.$sql; exit;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->duplicateid]['title'] = $r->title;
                $retArr[$r->duplicateid]['desc'] = $r->description;
                $retArr[$r->duplicateid]['notes'] = $r->notes;
            }
            $rs->free();
            if($retArr){
                $sql = 'SELECT dl.duplicateid, o.occid, IFNULL(IFNULL(o.catalognumber,othercatalognumbers),"Undefined Identifier") AS identifier, '.
                    'o.sciname, o.tidinterpreted, o.recordedby, o.recordnumber, CONCAT_WS(":",c.institutioncode ,c.collectioncode) as code, '.
                    'o.identifiedby, o.dateidentified '.
                    'FROM omoccurduplicatelink dl INNER JOIN omoccurrences o ON dl.occid = o.occid '.
                    'INNER JOIN omcollections c ON o.collid = c.collid '.
                    'WHERE dl.duplicateid IN ('.implode(',',array_keys($retArr)).')';
                //echo $sql;
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    $idStr = $r->identifier;
                    if(is_numeric($idStr) || $idStr === 'Undefined Identifier') {
                        $idStr = $r->code . ':' . $idStr;
                    }
                    if(!$idStr) {
                        $idStr = $r->code . ':' . 'undefined';
                    }
                    $retArr[$r->duplicateid][$r->occid]['id'] = $idStr;
                    $retArr[$r->duplicateid][$r->occid]['sciname'] = $r->sciname;
                    $retArr[$r->duplicateid][$r->occid]['tid'] = $r->tidinterpreted;
                    $retArr[$r->duplicateid][$r->occid]['idby'] = $r->identifiedby;
                    $retArr[$r->duplicateid][$r->occid]['dateid'] = $r->dateidentified;
                    $retArr[$r->duplicateid][$r->occid]['recby'] = $r->recordedby.' '.$r->recordnumber;
                }
                $rs->free();
            }
            $retArr['cnt'] = $totalCnt;
        }
        return $retArr;
    }

    public function batchLinkDuplicates($collid = 0, $verbose = true): void
    {
        ini_set('max_execution_time', 1800);
        $startDate = '1700-00-00';
        if($verbose) {
            echo '<li>Starting to search for duplicates ' . date('Y-m-d H:i:s') . '</li>';
        }
        flush();
        do{
            $sql = 'SELECT DISTINCT o.eventdate '.
                'FROM omoccurrences o LEFT JOIN omoccurduplicatelink d ON o.occid = d.occid '.
                'WHERE o.eventdate > "'.$startDate.'" AND d.occid IS NULL ';
            if($collid) {
                $sql .= 'AND o.collid = ' . $collid . ' ';
            }
            if($this->obsUid) {
                $sql .= 'AND o.observeruid = ' . $this->obsUid . ' ';
            }
            $sql .= 'ORDER BY o.eventdate LIMIT 500';
            $rs = $this->conn->query($sql);
            $recCnt = $rs->num_rows;
            if($verbose) {
                echo '<li>Start date ' . $startDate . ' with ' . $recCnt . ' dates to be evaluated</li>';
            }
            flush();
            while($r = $rs->fetch_object()){
                $startDate = $r->eventdate;
                $sql2 = 'SELECT o.recordedby, o.recordnumber, o.occid, IFNULL(d.duplicateid,0) as dupid, o.collid, o.observeruid '.
                    'FROM omoccurrences o LEFT JOIN omoccurduplicatelink d ON o.occid = d.occid '.
                    'WHERE o.eventdate = "'.$r->eventdate.'" AND o.recordedby IS NOT NULL AND o.recordnumber IS NOT NULL ';
                $rs2 = $this->conn->query($sql2);
                $rArr = array();
                $keepArr = array();
                while($r2 = $rs2->fetch_object()){
                    $recNum = str_replace(array(' ','-',':'),'',$r2->recordnumber);
                    if(preg_match('#\d#',$recNum)){
                        $lastName = $this->parseLastName($r2->recordedby);
                        if(strpos($lastName,'.')) {
                            $lastName = $r2->recordedby;
                        }
                        if(isset($lastName) && $lastName && !preg_match('#\d#',$lastName) && (is_string($recNum) || is_numeric($recNum))){
                            $rArr[$recNum][$lastName][$r2->dupid][] = $r2->occid;
                            if($r2->collid === $collid && ($this->obsUid || $r2->observeruid === $this->obsUid)) {
                                $keepArr[$recNum][$lastName] = 1;
                            }
                        }
                    }
                }
                $recArr = array();
                if($collid){
                    foreach($keepArr as $n => $lArr){
                        foreach($lArr as $l => $v){
                            $recArr[$n][$l] = $rArr[$n][$l];
                        }
                    }
                }
                else{
                    $recArr = $rArr;
                }
                foreach($recArr as $numStr => $collArr){
                    foreach($collArr as $lastnameStr => $mArr){
                        $unlinkedArr = $mArr[0] ?? null;
                        unset($mArr[0]);
                        if(($unlinkedArr && $mArr) || count($unlinkedArr) > 1){
                            $dupIdStr = $lastnameStr.' '.$numStr.' '.$r->eventdate;
                            if($verbose) {
                                echo '<li>Duplicates located: ' . $dupIdStr . '</li>';
                            }
                            flush();
                            $dupId = 0;
                            if($mArr) {
                                $dupId = key($mArr);
                            }
                            if(!$dupId){
                                $sqlI1 = 'INSERT INTO omoccurduplicates(title,dupetype) VALUES("'.$this->cleanInStr($dupIdStr).'",1)';
                                if($this->conn->query($sqlI1)){
                                    $dupId = $this->conn->insert_id;
                                    if($verbose) {
                                        echo '<li style="margin-left:10px;">New duplicate project created: #' . $dupId . '</li>';
                                    }
                                }
                                else{
                                    if($verbose) {
                                        echo '<li style="margin-left:10px;">ERROR creating dupe project: ' . $this->conn->error . '</li>';
                                    }
                                    if($verbose) {
                                        echo '<li style="margin-left:10px;">sql: ' . $sqlI1 . '</li>';
                                    }
                                }
                                flush();
                            }
                            if($dupId){
                                $outLink = '';
                                $sqlI2 = 'INSERT INTO omoccurduplicatelink(duplicateid,occid) VALUES ';
                                foreach($unlinkedArr as $v){
                                    $sqlI2 .= '('.$dupId.','.$v.'),';
                                    $outLink .= ' <a href="../individual/index.php?occid='.$v.'" target="_blank">'.$v.'</a>,';
                                }
                                if($this->conn->query(trim($sqlI2,','))){
                                    if($verbose) {
                                        echo '<li style="margin-left:10px;">' . count($unlinkedArr) . ' duplicates linked (' . trim($outLink, ' ,') . ')</li>';
                                    }
                                }
                                else if($verbose) {
                                    echo '<li style="margin-left:10px;">ERROR linking dupes: ' . $this->conn->error . '</li>';
                                }
                                flush();
                            }
                        }
                        if(count($mArr) > 1){
                            if($verbose) {
                                echo '<li style="margin-left:10px;">Two matching duplicate projects located</li>';
                            }
                            flush();

                        }
                    }
                }
            }
            $rs->close();
        }
        while($recCnt);
        if($verbose) {
            echo '<li>Finished linking duplicates ' . date('Y-m-d H:i:s') . '</li>';
        }
    }

    public function parseLastName($collName){
        $lastNameArr = array();
        $collName = trim($collName);
        if(!$collName) {
            return '';
        }
        $primaryArr = explode(';',$collName);
        if($primaryArr){
            $primaryArr = explode('&',$primaryArr[0]);
        }
        if($primaryArr){
            $primaryArr = explode(' and ',$primaryArr[0]);
        }
        if($primaryArr){
            $lastNameArr = explode(',',$primaryArr[0]);
        }
        if($lastNameArr){
            if(count($lastNameArr) > 1){
                $lastName = array_shift($lastNameArr);
            }
            else{
                $tempArr = isset($lastNameArr[0])?explode(' ',$lastNameArr[0]):array();
                $lastName = array_pop($tempArr);
                while($tempArr && (strpos($lastName,'.') || $lastName === 'III' || strlen($lastName)<3)){
                    $lastName = array_pop($tempArr);
                }
            }
        }
        return $lastName;
    }

    public function getRelevantFields(): array
    {
        return $this->relevantFields;
    }

    public function getErrorStr(){
        return $this->errorStr;
    }

    public function getCollMap($collid): array
    {
        $returnArr = array();
        if($collid){
            $sql = 'SELECT c.institutioncode, c.collectioncode, c.collectionname, '.
                'c.icon, c.colltype, c.managementtype '.
                'FROM omcollections c '.
                'WHERE (c.collid = '.$collid.') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($row = $rs->fetch_object()){
                $returnArr['institutioncode'] = $row->institutioncode;
                $returnArr['collectioncode'] = $row->collectioncode;
                $returnArr['collectionname'] = $row->collectionname;
                $returnArr['icon'] = $row->icon;
                $returnArr['colltype'] = $row->colltype;
                $returnArr['managementtype'] = $row->managementtype;
            }
            $rs->close();
        }
        return $returnArr;
    }

    private function cleanInStr($str){
        $newStr = trim($str);
        $newStr = preg_replace('/\s\s+/', ' ',$newStr);
        $newStr = $this->conn->real_escape_string($newStr);
        return $newStr;
    }
}
