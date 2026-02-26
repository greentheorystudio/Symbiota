<?php
include_once(__DIR__ . '/ChecklistTaxa.php');
include_once(__DIR__ . '/Images.php');
include_once(__DIR__ . '/KeyCharacterStates.php');
include_once(__DIR__ . '/Media.php');
include_once(__DIR__ . '/OccurrenceDeterminations.php');
include_once(__DIR__ . '/Occurrences.php');
include_once(__DIR__ . '/TaxonDescriptionBlocks.php');
include_once(__DIR__ . '/TaxonHierarchy.php');
include_once(__DIR__ . '/TaxonKingdoms.php');
include_once(__DIR__ . '/TaxonMaps.php');
include_once(__DIR__ . '/TaxonVernaculars.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/TaxonomyService.php');

class Taxa{

	private $conn;

    private $fields = array(
        'tid' => array('dataType' => 'number', 'length' => 10),
        'kingdomid' => array('dataType' => 'number', 'length' => 11),
        'rankid' => array('dataType' => 'number', 'length' => 5),
        'sciname' => array('dataType' => 'string', 'length' => 250),
        'unitind1' => array('dataType' => 'string', 'length' => 1),
        'unitname1' => array('dataType' => 'string', 'length' => 50),
        'unitind2' => array('dataType' => 'string', 'length' => 1),
        'unitname2' => array('dataType' => 'string', 'length' => 50),
        'unitind3' => array('dataType' => 'string', 'length' => 15),
        'unitname3' => array('dataType' => 'string', 'length' => 35),
        'author' => array('dataType' => 'string', 'length' => 100),
        'tidaccepted' => array('dataType' => 'number', 'length' => 10),
        'parenttid' => array('dataType' => 'number', 'length' => 10),
        'family' => array('dataType' => 'string', 'length' => 50),
        'source' => array('dataType' => 'string', 'length' => 250),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'hybrid' => array('dataType' => 'string', 'length' => 50),
        'securitystatus' => array('dataType' => 'number', 'length' => 10),
        'modifieduid' => array('dataType' => 'number', 'length' => 10),
        'modifiedtimestamp' => array('dataType' => 'date', 'length' => 0),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function addTaxonIdentifier($tid, $idName, $id): int
    {
        $returnVal = 0;
        if($tid && $idName && $id){
            $identifierName = SanitizerService::cleanInStr($this->conn, $idName);
            $identifier = SanitizerService::cleanInStr($this->conn, $id);
            $sql = 'INSERT IGNORE INTO taxaidentifiers(tid, `name`, identifier) VALUES('.
                (int)$tid . ',"' . $identifierName . '", "' . $identifier . '")';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
            else{
                $sql = 'UPDATE taxaidentifiers SET identifier = "' . $identifier . '" WHERE tid = ' . (int)$tid . ' AND `name` = "' . $identifierName . '" ';
                if($this->conn->query($sql)){
                    $returnVal = 1;
                }
            }
        }
        return $returnVal;
    }

    public function changeTaxonParent($tid, $parentTid): int
    {
        $status = 0;
        if(is_numeric($tid)){
            $sql = 'SELECT family, kingdomId FROM taxa WHERE TID = ' . (int)$parentTid . ' ';
            //echo $sql."<br>";
            if($result = $this->conn->query($sql)){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    $kingdomId = (int)$row['kingdomId'];
                    $family = $row['family'];
                    $sql2 = 'UPDATE taxa SET parenttid = ' . $parentTid . ', family = ' . ($family ? ('"' . $family . '"') : 'NULL') . ', kingdomId = ' . $kingdomId . ' WHERE tid = ' . (int)$tid . ' ';
                    //echo $sql2;
                    if($this->conn->query($sql2)) {
                        $status = 1;
                        $sqlSyns = 'UPDATE taxa SET parenttid = ' . $parentTid . ', family = ' . ($family ? ('"' . $family . '"') : 'NULL') . ', kingdomId = ' . $kingdomId . ' WHERE tidaccepted = ' . (int)$tid . ' ';
                        if(!$this->conn->query($sqlSyns)){
                            $status = 0;
                        }
                        if($status){
                            $sqlParent = 'UPDATE taxa SET family = ' . ($family ? ('"' . $family . '"') : 'NULL') . ', kingdomId = ' . $kingdomId . ' WHERE parenttid = ' . (int)$tid . ' ';
                            if(!$this->conn->query($sqlParent)){
                                $status = 0;
                            }
                        }
                        if($status){
                            $status = (new TaxonHierarchy)->updateHierarchyTable($tid);
                        }
                    }
                }
            }
        }
        return $status;
    }

    public function changeTaxonToNotAccepted($tid, $tidAccepted, $kingdom): int
    {
        $status = 0;
        if(is_numeric($tid)){
            $sql = 'SELECT parenttid, family, kingdomId FROM taxa WHERE TID = ' . (int)$tidAccepted . ' ';
            //echo $sql."<br>";
            if($result = $this->conn->query($sql)){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    $parentTid = (int)$row['parenttid'];
                    $kingdomId = (int)$row['kingdomId'];
                    $family = $row['family'];
                    $sql2 = 'UPDATE taxa SET tidaccepted = ' . (int)$tidAccepted . ', parenttid = ' . $parentTid . ', family = ' . ($family ? ('"' . $family . '"') : 'NULL') . ', kingdomId = ' . $kingdomId . ' WHERE tid = ' . (int)$tid . ' ';
                    //echo $sql2;
                    if($this->conn->query($sql2)) {
                        $status = 1;
                        $sqlSyns = 'UPDATE taxa SET tidaccepted = ' . (int)$tidAccepted . ', parenttid = ' . $parentTid . ', family = ' . ($family ? ('"' . $family . '"') : 'NULL') . ', kingdomId = ' . $kingdomId . ' WHERE tidaccepted = ' . (int)$tid . ' ';
                        if(!$this->conn->query($sqlSyns)){
                            $status = 0;
                        }
                        if($status){
                            $sqlParent = 'UPDATE taxa SET parenttid = ' . (int)$tidAccepted . ', family = ' . ($family ? ('"' . $family . '"') : 'NULL') . ', kingdomId = ' . $kingdomId . ' WHERE parenttid = ' . (int)$tid . ' ';
                            if(!$this->conn->query($sqlParent)){
                                $status = 0;
                            }
                        }
                        if($status){
                            $status = (new TaxonHierarchy)->updateTaxonParentTid($tid, $tidAccepted);
                        }
                        if($status && (int)$tid !== (int)$tidAccepted){
                            $status = (new TaxonHierarchy)->deleteTidFromHierarchyTable($tid);
                        }
                        if($kingdom){
                            (new TaxonKingdoms)->updateKingdomAcceptance($tid, $tidAccepted);
                        }
                    }
                }
            }
        }
        return $status;
    }

    public function createTaxaRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $data = $this->validateNewTaxaData($data);
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'tid' && array_key_exists($field, $data)){
                if($field === 'source'){
                    $fieldNameArr[] = '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = $field;
                }
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'modifieduid';
        $fieldValueArr[] = $GLOBALS['SYMB_UID'];
        $fieldNameArr[] = 'modifiedtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT IGNORE INTO taxa(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
            if((int)$data['tidaccepted'] === 0){
                $sqlNewTaxUpdate = 'UPDATE taxa SET tidaccepted = ' . $newID . ' WHERE tid = ' . $newID . ' ';
                $this->conn->query($sqlNewTaxUpdate);
            }
            if((int)$data['rankid'] === 10){
                $kingdomId = (new TaxonKingdoms)->createTaxonKingdomRecord($data['sciname']);
                $sqlNewTaxUpdate = 'UPDATE taxa SET kingdomid = ' . $kingdomId . ' WHERE tid = ' . $newID . ' ';
                $this->conn->query($sqlNewTaxUpdate);
            }
            if(array_key_exists('source-name', $data) && array_key_exists('source-id', $data) && $data['source-name'] && $data['source-id']){
                $sqlId = 'INSERT IGNORE INTO taxaidentifiers(tid, `name`, identifier) VALUES('.
                    $newID . ', "' . SanitizerService::cleanInStr($this->conn, $data['source-name']) . '", '.
                    '"' . SanitizerService::cleanInStr($this->conn, $data['source-id']) . '") ';
                //echo $sqlId; exit;
                $this->conn->query($sqlId);
            }
        }
        return $newID;
    }

    public function deleteTaxon($tid): int
    {
        $retVal = 1;
        if($tid){
            $taxonData = $this->getTaxonFromTid($tid, false, true);
            (new Images)->deleteAssociatedImageRecords('tid', $tid);
            (new Images)->deleteTaxonImageTags($tid);
            (new Media)->deleteAssociatedMediaRecords('tid', $tid);
            (new TaxonMaps)->deleteTaxonMapRecord('tid', $tid);
            (new TaxonHierarchy)->deleteTidFromHierarchyTable($tid);
            (new TaxonVernaculars)->deleteTaxonVernacularRecords($tid);
            (new ChecklistTaxa)->deleteChecklistTaxonRecords($tid);
            (new KeyCharacterStates)->deleteTaxonCharacterStateRecords($tid);
            (new Occurrences)->removeTaxonFromOccurrenceRecords($tid);
            (new OccurrenceDeterminations)->removeTaxonFromDeterminationRecords($tid);
            (new TaxonDescriptionBlocks)->deleteTaxonDescriptionBlockRecords($tid);
            if((int)$taxonData['rankid'] === 10){
                (new TaxonKingdoms)->deleteTaxonKingdom($taxonData['sciname']);
            }
            $sql = 'DELETE FROM glossarytaxalink WHERE tid = ' . (int)$tid . ' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
            $sql = 'DELETE FROM taxa WHERE tid = ' . (int)$tid . ' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function evaluateTaxonForDeletion($tid): int
    {
        $retVal = 0;
        if($tid){
            $sql = 'SELECT DISTINCT TID FROM taxa '.
                'WHERE TID IN(SELECT tid FROM taxa WHERE parenttid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM taxa WHERE TID <> tidaccepted AND tidaccepted = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM fmchklsttaxalink WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM fmdyncltaxalink WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM fmvouchers WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM glossarysources WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM glossarytaxalink WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM images WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM keycharacterstatetaxalink WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM media WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM omoccurassociations WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM omoccurdeterminations WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM omoccurrences WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM taxadescrblock WHERE tid = ' . (int)$tid . ') '.
                'OR TID IN(SELECT tid FROM taxamaps WHERE tid = ' . (int)$tid . ') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            $retVal = $rs->num_rows;
            $rs->free();
        }
        return $retVal;
    }

    public function getAcceptedChildTaxaByParentTid($parentTid): array
    {
        $retArr = array();
        if($parentTid){
            $sql = 'SELECT DISTINCT tid, sciname, rankid, parenttid FROM taxa WHERE tid = tidaccepted AND parenttid = ' . (int)$parentTid . ' ';
            $sql .= 'ORDER BY sciname ';
            //echo $sql;
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $rIndex => $row){
                    $nodeArr = array();
                    $nodeArr['tid'] = $row['tid'];
                    $nodeArr['sciname'] = $row['sciname'];
                    $nodeArr['rankid'] = $row['rankid'];
                    $nodeArr['parenttid'] = $row['parenttid'];
                    $retArr[] = $nodeArr;
                    unset($rows[$rIndex]);
                }
            }
        }
        return $retArr;
    }

    public function getAcceptedTaxaByTaxonomicGroup($parentTid, $index, $rankId = null): array
    {
        $retArr = array();
        if($parentTid){
            $sql = 'SELECT DISTINCT tid, sciname, rankid, parenttid FROM taxa '.
                'WHERE tid = tidaccepted AND (TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') '.
                'OR parenttid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ')) ';
            if($rankId){
                $sql .= 'AND rankid = ' . (int)$rankId . ' ';
            }
            $sql .= 'ORDER BY sciname '.
                'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
            //echo $sql;
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $rIndex => $row){
                    $nodeArr = array();
                    $nodeArr['tid'] = $row['tid'];
                    $nodeArr['sciname'] = $row['sciname'];
                    $nodeArr['rankid'] = $row['rankid'];
                    $nodeArr['parenttid'] = $row['parenttid'];
                    $retArr[] = $nodeArr;
                    unset($rows[$rIndex]);
                }
            }
        }
        return $retArr;
    }

    public function getAudioCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.tid, t.sciname, t.rankid, COUNT(m.mediaid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN media AS m ON t.TID = m.tid '.
            'WHERE (te.parenttid = ' . (int)$tid . ' OR t.TID = ' . (int)$tid . ') AND t.TID = t.tidaccepted AND (m.format LIKE "audio/%" OR ISNULL(m.format)) ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(m.occid) ';
        }
        $sql .= 'GROUP BY t.tid '.
            'ORDER BY t.rankid, t.sciname '.
            'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $rIndex => $row){
                $resultArr = array();
                $resultArr['tid'] = $row['tid'];
                $resultArr['sciname'] = $row['sciname'];
                $resultArr['rankid'] = $row['rankid'];
                $resultArr['cnt'] = $row['cnt'];
                $retArr[] = $resultArr;
                unset($rows[$rIndex]);
            }
        }
        return $retArr;
    }

    public function getAutocompleteSciNameList($opts): array
    {
        $retArr = array();
        $term = array_key_exists('term', $opts) ? SanitizerService::cleanInStr($this->conn, $opts['term']) : null;
        if($term){
            $acceptedOnly = array_key_exists('acceptedonly', $opts) && (($opts['acceptedonly'] === 'true' || (int)$opts['acceptedonly'] === 1));
            $hideAuth = array_key_exists('hideauth', $opts) && (($opts['hideauth'] === 'true' || (int)$opts['hideauth'] === 1));
            $hideProtected = array_key_exists('hideprotected', $opts) && (($opts['hideprotected'] === 'true' || (int)$opts['hideprotected'] === 1));
            $kingdomId = (array_key_exists('kingdomid', $opts) && (int)$opts['kingdomid'] > 0) ? (int)$opts['kingdomid'] : null;
            $limit = array_key_exists('limit', $opts) ? (int)$opts['limit'] : null;
            $parentTid = (array_key_exists('parenttid', $opts) && (int)$opts['parenttid'] > 0) ? (int)$opts['parenttid'] : null;
            $rankHigh = array_key_exists('rhigh', $opts) ? (int)$opts['rhigh'] : null;
            $rankLimit = array_key_exists('rlimit', $opts) ? (int)$opts['rlimit'] : null;
            $rankLow = array_key_exists('rlow', $opts) ? (int)$opts['rlow'] : null;
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT DISTINCT ' . implode(',', $fieldNameArr) . '  '.
                'FROM taxa WHERE sciname LIKE "' . $term . '%" ';
            if($rankLimit){
                $sql .= 'AND rankid = ' . $rankLimit . ' ';
            }
            else{
                if($rankLow){
                    $sql .= 'AND rankid >= ' . $rankLow . ' ';
                }
                if($rankHigh){
                    $sql .= 'AND rankid <= ' . $rankHigh . ' ';
                }
            }
            if($parentTid){
                $sql .= 'AND tid IN(SELECT tid FROM taxaenumtree WHERE parenttid = ' . $parentTid . ') ';
            }
            if($hideProtected){
                $sql .= 'AND securitystatus <> 1 ';
            }
            if($acceptedOnly){
                $sql .= 'AND tid = tidaccepted ';
            }
            if($kingdomId){
                $sql .= 'AND kingdomid = ' . $kingdomId . ' ';
            }
            $sql .= 'ORDER BY sciname ';
            if($limit){
                $sql .= 'LIMIT ' . $limit . ' ';
            }
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $scinameArr = array();
                    $label = $row['sciname'] . ($hideAuth ? '' : (' ' . $row['author']));
                    $scinameArr['label'] = $label;
                    foreach($fields as $val){
                        $name = $val->name;
                        $scinameArr[$name] = $row[$name];
                    }
                    $retArr[] = $scinameArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getChildTaxaFromTid($tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxa WHERE parenttid = ' . (int)$tid . ' AND tid = tidaccepted ORDER BY sciname ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getCloseTaxaMatches($name, $levDistance, $kingdomId = null): array
    {
        $retArr = array();
        $sql = 'SELECT tid, sciname FROM taxa ';
        if($kingdomId){
            $sql .= 'WHERE kingdomId = ' . (int)$kingdomId . ' ';
        }
        $sql .= 'ORDER BY sciname ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if($name !== $row['sciname'] && levenshtein($name, $row['sciname']) <= $levDistance){
                    $valArr = array();
                    $valArr['tid'] = $row['tid'];
                    $valArr['sciname'] = $row['sciname'];
                    $retArr[] = $valArr;
                }
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getDescriptionCountsForTaxonomicGroup($tid, $index): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(tdb.tdbid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxadescrblock AS tdb ON t.TID = tdb.tid '.
            'WHERE (te.parenttid = ' . (int)$tid . ' OR t.TID = ' . (int)$tid . ') AND t.TID = t.tidaccepted '.
            'GROUP BY t.TID '.
            'ORDER BY t.RankId, t.SciName '.
            'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $rIndex => $row){
                $resultArr = array();
                $resultArr['tid'] = $row['TID'];
                $resultArr['sciname'] = $row['SciName'];
                $resultArr['rankid'] = $row['RankId'];
                $resultArr['cnt'] = $row['cnt'];
                $retArr[] = $resultArr;
                unset($rows[$rIndex]);
            }
        }
        return $retArr;
    }

    public function getDynamicTaxaListDataArr($parentIdentifier, $parentIdType, $limitToDescriptions, $index = null, $recCnt = null): array
    {
        $returnArr = array();
        $tempArr = array();
        $parentTaxonArr = array();
        $targetTidArr = array();
        $tidArr = array();
        if($parentIdType === 'parenttid'){
            $targetTidArr = (new TaxonHierarchy)->getSubtaxaTidArrFromTid($parentIdentifier);
            $targetTidArr[] = $parentIdentifier;
        }
        elseif($parentIdType === 'vernacular'){
            $targetTidArr = (new TaxonVernaculars)->getTidArrFromVernacular($parentIdentifier);
        }
        if(count($targetTidArr) > 0){
            $parentTaxonSql = 'SELECT DISTINCT te.tid, t.tid AS parenttid, t.rankid, t.sciname '.
                'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.parenttid = t.tid '.
                'WHERE te.tid IN(' . implode(',', $targetTidArr) . ') AND t.tid = t.tidaccepted AND t.rankid IN(10,30,60,100,140) ';
            if($result = $this->conn->query($parentTaxonSql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $rIndex => $row){
                    $parentTaxonArr[$row['tid']][$row['rankid']]['id'] = $row['parenttid'];
                    $parentTaxonArr[$row['tid']][$row['rankid']]['sciname'] = $row['sciname'];
                    unset($rows[$rIndex]);
                }
            }
            $sql = 'SELECT DISTINCT t.tid, t.sciname FROM taxa AS t '.
                'WHERE t.tid IN(' . implode(',', $targetTidArr) . ') AND t.rankid > 10 AND t.tid = t.tidaccepted '.
                'AND (t.sciname LIKE "% %" OR t.tid NOT IN(SELECT DISTINCT parenttid FROM taxa WHERE parenttid IS NOT NULL)) ';
            if($limitToDescriptions){
                $sql .= 'AND t.TID IN(SELECT tid FROM taxadescrblock) ';
            }
            if((int)$recCnt > 0){
                $startIndex = (int)$index * (int)$recCnt;
                $sql .= 'LIMIT ' . $startIndex . ', ' . (int)$recCnt . ' ';
            }
            //error_log($sql);
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $rIndex => $row){
                    if($row['tid']){
                        if(!in_array($row['tid'], $tidArr, true)){
                            $tidArr[] = $row['tid'];
                        }
                        $recordArr = array();
                        $parentArr = (array_key_exists($row['tid'], $parentTaxonArr) ? $parentTaxonArr[$row['tid']] : array());
                        $recordArr['tid'] = $row['tid'];
                        $recordArr['sciname'] = $row['sciname'];
                        $recordArr['kingdomtid'] = (array_key_exists('10', $parentArr) ? $parentArr['10']['id'] : '0');
                        $recordArr['kingdomname'] = (array_key_exists('10', $parentArr) ? $parentArr['10']['sciname'] : '');
                        $recordArr['phylumtid'] = (array_key_exists('30', $parentArr) ? $parentArr['30']['id'] : '0');
                        $recordArr['phylumname'] = (array_key_exists('30', $parentArr) ? $parentArr['30']['sciname'] : '');
                        $recordArr['classtid'] = (array_key_exists('60', $parentArr) ? $parentArr['60']['id'] : '0');
                        $recordArr['classname'] = (array_key_exists('60', $parentArr) ? $parentArr['60']['sciname'] : '');
                        $recordArr['ordertid'] = (array_key_exists('100', $parentArr) ? $parentArr['100']['id'] : '0');
                        $recordArr['ordername'] = (array_key_exists('100', $parentArr) ? $parentArr['100']['sciname'] : '');
                        $recordArr['familytid'] = (array_key_exists('140', $parentArr) ? $parentArr['140']['id'] : '0');
                        $recordArr['familyname'] = (array_key_exists('140', $parentArr) ? $parentArr['140']['sciname'] : '');
                        $recordArr['vernacularData'] = array();
                        $recordArr['identifierData'] = array();
                        $tempArr[] = $recordArr;
                    }
                    unset($rows[$rIndex]);
                }
                if(count($tidArr) > 0){
                    $vernacularDataArr = (new TaxonVernaculars)->getVernacularArrFromTidArr($tidArr);
                    $identifierDataArr = $this->getIdentifiersFromTidArr($tidArr);
                    if($identifierDataArr || $vernacularDataArr){
                        foreach($tempArr as $taxonArr){
                            if(array_key_exists($taxonArr['tid'], $identifierDataArr)){
                                $taxonArr['identifierData'] = $identifierDataArr[$taxonArr['tid']];
                            }
                            if(array_key_exists($taxonArr['tid'], $vernacularDataArr)){
                                $taxonArr['vernacularData'] = $vernacularDataArr[$taxonArr['tid']];
                            }
                            $returnArr[] = $taxonArr;
                        }
                    }
                    else{
                        $returnArr[] = $tempArr;
                    }
                }
            }
        }
        return $returnArr;
    }

    public function getIdentifiersForTaxonomicGroup($tid, $index, $source): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, ti.identifier '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxaidentifiers AS ti ON t.TID = ti.tid '.
            'WHERE (te.parenttid = ' . (int)$tid . ' OR t.TID = ' . (int)$tid . ') AND ti.name = "' . SanitizerService::cleanInStr($this->conn, $source) . '" '.
            'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $rIndex => $row){
                $resultArr = array();
                $resultArr['tid'] = $row['TID'];
                $resultArr['identifier'] = $row['identifier'];
                $retArr[] = $resultArr;
                unset($rows[$rIndex]);
            }
        }
        return $retArr;
    }

    public function getIdentifiersFromTidArr($tidArr): array
    {
        $retArr = array();
        $sql = 'SELECT tid, name, identifier FROM taxaidentifiers WHERE tid IN(' . implode(',', $tidArr) . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(!array_key_exists($row['tid'], $retArr)){
                    $retArr[$row['tid']] = array();
                }
                $resultArr = array();
                $resultArr['name'] = $row['name'];
                $resultArr['identifier'] = $row['identifier'];
                $retArr[$row['tid']][] = $resultArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getImageCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(i.imgid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN images AS i ON t.TID = i.tid '.
            'WHERE (te.parenttid = ' . (int)$tid . ' OR t.TID = ' . (int)$tid . ') AND t.TID = t.tidaccepted ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(i.occid) ';
        }
        $sql .= 'GROUP BY t.TID '.
            'ORDER BY t.RankId, t.SciName '.
            'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $rIndex => $row){
                $resultArr = array();
                $resultArr['tid'] = $row['TID'];
                $resultArr['sciname'] = $row['SciName'];
                $resultArr['rankid'] = $row['RankId'];
                $resultArr['cnt'] = $row['cnt'];
                $retArr[] = $resultArr;
                unset($rows[$rIndex]);
            }
        }
        return $retArr;
    }

    public function getParentTaxaFromTid($tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 't');
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.parenttid = t.tid '.
            'WHERE te.tid = ' . (int)$tid . ' ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getProtectedTaxaArr(): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 't');
        $fieldNameArr[] = 'k.kingdom_name AS kingdom';
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.kingdomId = k.kingdom_id '.
            'WHERE t.securitystatus = 1 ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getRankArrForTaxonomicGroup($parentTid): array
    {
        $retArr = array();
        if($parentTid){
            $sql = 'SELECT DISTINCT t.RankId, tu.rankname FROM taxa AS t LEFT JOIN taxonunits AS tu ON t.RankId = tu.rankid AND t.kingdomId = tu.kingdomid '.
                'WHERE t.TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') '.
                'OR t.parenttid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') '.
                'ORDER BY t.RankId ';
            //echo $sql;
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    $nodeArr['rankid'] = $row['RankId'];
                    $nodeArr['rankname'] = $row['rankname'];
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getTaxaArrByRankIdArr($rankIdArr, $includeVernacular, $includeParentTids): array
    {
        $retArr = array();
        $tempArr = array();
        $tidArr = array();
        $parentTidDataArr = array();
        $vernacularDataArr = array();
        if($rankIdArr && count($rankIdArr) > 0){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' FROM taxa '.
                'WHERE tid = tidaccepted AND rankid IN(' . implode(',', $rankIdArr) . ') ORDER BY rankid, sciname ';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    if(!in_array($row['tidaccepted'], $tidArr, true)){
                        $tidArr[] = $row['tidaccepted'];
                    }
                    foreach($fields as $val){
                        $name = $val->name;
                        $nodeArr[$name] = $row[$name];
                        $nodeArr['parentTidArr'] = array();
                        $nodeArr['vernacularData'] = array();
                    }
                    if($includeVernacular || $includeParentTids){
                        $tempArr[] = $nodeArr;
                    }
                    else{
                        $retArr[] = $nodeArr;
                    }
                    unset($rows[$index]);
                }
                if(($includeVernacular || $includeParentTids) && count($tidArr) > 0){
                    if($includeParentTids){
                        $parentTidDataArr = (new TaxonHierarchy)->getParentTidDataFromTidArr($tidArr);
                    }
                    if($includeVernacular){
                        $vernacularDataArr = (new TaxonVernaculars)->getVernacularArrFromTidArr($tidArr);
                    }
                    if($parentTidDataArr || $vernacularDataArr){
                        foreach($tempArr as $taxonArr){
                            if($includeParentTids){
                                $taxonArr['parentTidArr'] = $parentTidDataArr[$taxonArr['tidaccepted']] ?? null;
                            }
                            if($includeVernacular){
                                $taxonArr['vernacularData'] = $vernacularDataArr[$taxonArr['tidaccepted']] ?? null;
                            }
                            $retArr[] = $taxonArr;
                        }
                    }
                    else{
                        $retArr[] = $tempArr;
                    }
                }
            }
        }
        return $retArr;
    }

    public function getTaxaSynonymArrFromTidArr($tidArr): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT tid, tidaccepted, sciname FROM taxa WHERE tidaccepted IN(' . implode(',', $tidArr) . ') AND tid <> tidaccepted ORDER BY tidaccepted, sciname ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(!array_key_exists($row['tidaccepted'], $retArr)){
                    $retArr[$row['tidaccepted']] = array();
                }
                $nodeArr = array();
                $nodeArr['tid'] = $row['tid'];
                $nodeArr['sciname'] = $row['sciname'];
                $retArr[$row['tidaccepted']][] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxaIdDataFromNameArr($nameArr, $kingdomId = null): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT tid, sciname FROM taxa  '.
            'WHERE sciname IN("' . implode('","', $nameArr) . '") ';
        if($kingdomId){
            $sql .= 'AND kingdomid = ' . (int)$kingdomId . ' ';
        }
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[strtolower($row['sciname'])]['tid'] = $row['tid'];
                $retArr[strtolower($row['sciname'])]['sciname'] = $row['sciname'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxaUseData($tid): array
    {
        $retArr = array();
        $retArr['children'] = $this->getChildTaxaFromTid($tid);
        $retArr['checklists'] = (new ChecklistTaxa)->getTaxonChecklistArr($tid);
        $retArr['images'] = (new Images)->getTaxonImageCount($tid);
        $retArr['media'] = (new Media)->getTaxonMediaCount($tid);
        $retArr['vernacular'] = (new TaxonVernaculars)->getTaxonVernacularCount($tid);
        $retArr['description'] = (new TaxonDescriptionBlocks)->getTaxonDescriptionCount($tid);
        $retArr['occurrences'] = (new Occurrences)->getTaxonOccurrenceCount($tid);
        $retArr['determinations'] = (new OccurrenceDeterminations)->getTaxonDeterminationCount($tid);
        return $retArr;
    }

    public function getTaxonFromSciname($sciname, $kingdomId = null, $showActual = null): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 't');
        $fieldNameArr[] = 'k.kingdom_name AS kingdom';
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.kingdomId = k.kingdom_id '.
            'WHERE t.sciname = "' . SanitizerService::cleanInStr($this->conn, $sciname) . '" ';
        if($kingdomId){
            $sql .= 'AND t.kingdomid = ' . (int)$kingdomId . ' ';
        }
        if(($result = $this->conn->query($sql)) && $result->num_rows === 1) {
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $row[$name];
                }
                $retArr['acceptedTaxon'] = (int)$row['tid'] !== (int)$row['tidaccepted'] ? $this->getTaxonFromTid($row['tidaccepted'], false) : null;
                $acceptedTid = (int)$row['tidaccepted'];
                $parentTid = (int)$row['tid'] === (int)$row['tidaccepted'] ? (int)$row['parenttid'] : (int)$retArr['acceptedTaxon']['parenttid'];
                $retArr['parentTaxon'] = $parentTid > 0 ? $this->getTaxonFromTid($parentTid, false) : null;
                $retArr['identifiers'] = $this->getTaxonIdentifiersFromTid($showActual ? $row['tid'] : $acceptedTid);
                $retArr['synonyms'] = $this->getTaxonSynonymsFromTid($showActual ? $row['tid'] : $acceptedTid);
                $retArr['children'] = $this->getChildTaxaFromTid($showActual ? $row['tid'] : $acceptedTid);
            }
        }
        return $retArr;
    }

    public function getTaxonFromTid($tid, $fullData = true, $showActual = null): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 't');
        $fieldNameArr[] = 'k.kingdom_name AS kingdom';
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.kingdomId = k.kingdom_id '.
            'WHERE t.tid = ' . (int)$tid . ' ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $row[$name];
                }
                $retArr['acceptedTaxon'] = (int)$row['tid'] !== (int)$row['tidaccepted'] ? $this->getTaxonFromTid($row['tidaccepted'], false) : null;
                $acceptedTid = (int)$row['tidaccepted'];
                $parentTid = (int)$row['tid'] === (int)$row['tidaccepted'] ? (int)$row['parenttid'] : (int)$retArr['acceptedTaxon']['parenttid'];
                if($fullData){
                    $retArr['parentTaxon'] = $parentTid > 0 ? $this->getTaxonFromTid($parentTid, false) : null;
                    $retArr['identifiers'] = $this->getTaxonIdentifiersFromTid($showActual ? $row['tid'] : $acceptedTid);
                    $retArr['synonyms'] = $this->getTaxonSynonymsFromTid($showActual ? $row['tid'] : $acceptedTid);
                    $retArr['children'] = $this->getChildTaxaFromTid($showActual ? $row['tid'] : $acceptedTid);
                }
            }
        }
        return $retArr;
    }

    public function getTaxonIdentifiersFromTid($tid): array
    {
        $retArr = array();
        $sql = 'SELECT `name`, identifier FROM taxaidentifiers WHERE tid = ' . (int)$tid . ' ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                $nodeArr['name'] = $row['name'];
                $nodeArr['identifier'] = $row['identifier'];
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxonSynonymsFromTid($tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' FROM taxa WHERE tidaccepted = ' . (int)$tid . ' AND tid <> tidaccepted '.
            'ORDER BY sciname ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTid($sciName, $kingdomid = null, $rankid = null, $author = null): int
    {
        $retTid = 0;
        if($sciName){
            $sql = 'SELECT tid FROM taxa WHERE sciname = "' . SanitizerService::cleanInStr($this->conn, $sciName) . '" ';
            if($kingdomid){
                $sql .= 'AND kingdomId = ' . (int)$kingdomid . ' ';
            }
            if($rankid){
                $sql .= 'AND rankid = ' . (int)$rankid . ' ';
            }
            if($author){
                $sql .= 'AND author = "' . SanitizerService::cleanInStr($this->conn, $author) . '" ';
            }
            $result = $this->conn->query($sql);
            if($row = $result->fetch_array(MYSQLI_ASSOC)){
                $retTid = (int)$row['tid'];
            }
            $result->free();
        }
        return $retTid;
    }

    public function getUnacceptedTaxaByTaxonomicGroup($parentTid, $index, $rankId = null): array
    {
        $retArr = array();
        if($parentTid){
            $sql = 'SELECT DISTINCT TID, SciName FROM taxa '.
                'WHERE TID <> tidaccepted AND (TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') '.
                'OR parenttid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ')) ';
            if($rankId){
                $sql .= 'AND RankId = ' . (int)$rankId . ' ';
            }
            $sql .= 'ORDER BY SciName '.
                'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
            //echo $sql;
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $rIndex => $row){
                    $nodeArr = array();
                    $nodeArr['tid'] = $row['TID'];
                    $nodeArr['sciname'] = $row['SciName'];
                    $retArr[] = $nodeArr;
                    unset($rows[$rIndex]);
                }
            }
        }
        return $retArr;
    }

    public function getVideoCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(m.mediaid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN media AS m ON t.TID = m.tid '.
            'WHERE (te.parenttid = ' . (int)$tid . ' OR t.TID = ' . (int)$tid . ') AND t.TID = t.tidaccepted AND (m.format LIKE "video/%" OR ISNULL(m.format)) ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(m.occid) ';
        }
        $sql .= 'GROUP BY t.TID '.
            'ORDER BY t.RankId, t.SciName '.
            'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $rIndex => $row){
                $resultArr = array();
                $resultArr['tid'] = $row['TID'];
                $resultArr['sciname'] = $row['SciName'];
                $resultArr['rankid'] = $row['RankId'];
                $resultArr['cnt'] = $row['cnt'];
                $retArr[] = $resultArr;
                unset($rows[$rIndex]);
            }
        }
        return $retArr;
    }

    public function remapChildTaxa($tid, $targetTid): int
    {
        $retVal = 0;
        if($tid && $targetTid){
            $sql = 'UPDATE taxa SET parenttid = ' . (int)$targetTid . ' WHERE parenttid = ' . (int)$tid . ' ';
            //echo $sql2;
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function remapTaxaSynonyms($tid, $targetTid): int
    {
        $retVal = 0;
        if($tid && $targetTid){
            $sql = 'UPDATE taxa SET parenttid = ' . (int)$targetTid . ' WHERE parenttid = ' . (int)$tid . ' ';
            //echo $sql2;
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function remapTaxonResources($tid, $targetTid): int
    {
        $returnVal = $this->remapChildTaxa($tid, $targetTid);
        if($returnVal){
            $returnVal = $this->remapTaxaSynonyms($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new ChecklistTaxa)->remapChecklistTaxon($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new Images)->remapTaxonImages($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new Images)->remapTaxonImageTags($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new Media)->remapTaxonMedia($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new Media)->remapTaxonMedia($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new TaxonVernaculars)->remapTaxonVernaculars($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new TaxonDescriptionBlocks)->remapTaxonDescriptions($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new TaxonDescriptionBlocks)->remapTaxonDescriptions($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new Occurrences)->remapTaxonOccurrences($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new OccurrenceDeterminations)->remapTaxonDeterminations($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new TaxonMaps)->remapTaxonMap($tid, $targetTid);
        }
        if($returnVal){
            $returnVal = (new KeyCharacterStates)->remapTaxonCharacterStates($tid, $targetTid);
        }
        return $returnVal;
    }

    public function removeSecurityForTaxon($tid): int
    {
        $retVal = 0;
        if($tid){
            $sql = 'UPDATE taxa SET securitystatus = 0 '.
                'WHERE tid = ' . (int)$tid . ' OR tidaccepted = ' . (int)$tid . ' ';
            //echo $sql2;
            if($this->conn->query($sql)){
                $retVal = 1;
                (new Occurrences)->protectGlobalSpecies(0);
            }
        }
        return $retVal;
    }

    public function setSecurityForTaxonOrTaxonomicGroup($tid, $includeSubtaxa): int
    {
        $retVal = 0;
        $tidArr = array();
        if($tid){
            if($includeSubtaxa){
                $tidArr = (new TaxonHierarchy)->getSubtaxaTidArrFromTid($tid);
            }
            $tidArr[] = $tid;
            $sql = 'UPDATE taxa SET securitystatus = 1 '.
                'WHERE tid IN(' . implode(',', $tidArr) . ') OR tidaccepted IN(' . implode(',', $tidArr) . ') ';
            //echo $sql2;
            if($this->conn->query($sql)){
                $retVal = 1;
                (new Occurrences)->protectGlobalSpecies(0);
            }
        }
        return $retVal;
    }

    public function setSynonymSearchData($searchData): array
    {
        foreach($searchData as $key => $tid){
            $targetTidArr = array();
            if($key){
                $sql = 'SELECT tid, tidaccepted FROM taxa WHERE sciname IN("' . $key . '") ';
                if($result = $this->conn->query($sql)){
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                    $result->free();
                    foreach($rows as $index => $row){
                        if($row['tid'] && !in_array($row['tid'], $targetTidArr, true)){
                            $targetTidArr[] = $row['tid'];
                        }
                        if($row['tidaccepted'] && !in_array($row['tidaccepted'], $targetTidArr, true)){
                            $targetTidArr[] = $row['tidaccepted'];
                        }
                        unset($rows[$index]);
                    }
                }
            }

            if($targetTidArr){
                $parentTidArr = array();
                $sql = 'SELECT DISTINCT tid, sciname, rankid FROM taxa '.
                    'WHERE tid IN(' . implode(',', $targetTidArr) . ') OR tidaccepted IN(' . implode(',', $targetTidArr) . ') ';
                if($result = $this->conn->query($sql)){
                    $rows = $result->fetch_all(MYSQLI_ASSOC);
                    $result->free();
                    foreach($rows as $index => $row){
                        $searchData[$row['sciname']] = $row['tid'];
                        if((int)$row['rankid'] === 220){
                            $parentTidArr[] = $row['tid'];
                        }
                        unset($rows[$index]);
                    }
                }

                if($parentTidArr) {
                    $searchData = (new TaxonHierarchy)->setParentSearchDataByTidArr($searchData, $parentTidArr);
                }
            }
        }
        return $searchData;
    }

    public function setTaxaSearchDataTids($searchData): array
    {
        foreach($searchData as $name => $tid){
            $cleanName = SanitizerService::cleanInStr($this->conn, $name);
            $sql = 'SELECT DISTINCT TID, SciName FROM taxa '.
                "WHERE SciName = '" . $cleanName . "' OR SciName LIKE '" . $cleanName . " %' ";
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $searchData[$row['SciName']] = $row['TID'];
                    unset($rows[$index]);
                }
            }
        }
        return $searchData;
    }

    public function setUpdateFamiliesAccepted($parentTid): int
    {
        $retCnt = 0;
        if($parentTid){
            $sql1 = 'UPDATE taxa '.
                'SET family = SciName '.
                'WHERE RankId = 140 AND TID = tidaccepted AND (TID IN(SELECT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') OR TID = ' . (int)$parentTid . ') ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE taxa AS t LEFT JOIN taxaenumtree AS te ON t.TID = te.tid '.
                'LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID '.
                'SET t.family = t2.SciName '.
                'WHERE t.RankId >= 140 AND t.TID = t.tidaccepted AND (t.TID IN(SELECT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') OR t.TID = ' . (int)$parentTid . ') AND (t2.RankId = 140 OR ISNULL(t2.RankId)) ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function setUpdateFamiliesUnaccepted($parentTid): int
    {
        $retCnt = 0;
        if($parentTid){
            $sql2 = 'UPDATE taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
                'SET t.family = t2.family '.
                'WHERE t.RankId = 140 AND t.TID <> t.tidaccepted AND (t.TID IN(SELECT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') OR t.TID = ' . (int)$parentTid . ') ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
                'LEFT JOIN taxaenumtree AS te ON t2.TID = te.tid '.
                'LEFT JOIN taxa AS t3 ON te.parenttid = t3.TID '.
                'SET t.family = t3.SciName '.
                'WHERE t.RankId >= 140 AND t.TID <> t.tidaccepted AND (t.TID IN(SELECT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') OR t.TID = ' . (int)$parentTid . ') AND t3.RankId = 140 ';
            //echo $sql4;
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function taxonHasChildren($tid): bool
    {
        $retVal = false;
        $sql = 'SELECT TID FROM taxa WHERE parenttid = ' . (int)$tid . ' LIMIT 1 ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($result->num_rows){
            $retVal = true;
        }
        $result->free();
        return $retVal;
    }

    public function updateTaxaRecord($tid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($tid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'tid' && array_key_exists($field, $editData)){
                    if($field === 'source'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = $fieldStr . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            if((!array_key_exists('sciname', $editData) || !$editData['sciname']) && array_key_exists('unitname1', $editData) && $editData['unitname1']){
                $sciNameConcat = ($editData['unitind1'] ? ($editData['unitind1'] . ' ') : '').
                    $editData['unitname1'] . ($editData['unitind2'] ? (' ' . $editData['unitind2']) : '').
                    ($editData['unitname2'] ? (' ' . $editData['unitname2']) : '').
                    ($editData['unitind3'] ? (' ' . $editData['unitind3']) : '').
                    ($editData['unitname3'] ? (' ' . $editData['unitname3']) : '');
                $sqlPartArr[] = 'sciname = ' . SanitizerService::getSqlValueString($this->conn, $sciNameConcat, 'string');
            }
            $sqlPartArr[] = 'modifieduid = ' . $GLOBALS['SYMB_UID'];
            $sqlPartArr[] = 'modifiedtimestamp = "' . date('Y-m-d H:i:s') . '"';
            $sql = 'UPDATE taxa SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE tid = ' . (int)$tid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function updateTaxonChildrenKingdomFamily($tid, $kingdomid, $family): int
    {
        $retVal = 0;
        if($tid && $kingdomid){
            $sql = 'UPDATE taxa SET kingdomid = ' . (int)$kingdomid . ' AND family = ' . ($family ? ('"' . SanitizerService::cleanInStr($this->conn, $family) . '"') : 'NULL') . ' '.
                'WHERE parenttid = ' . (int)$tid . ' ';
            //echo $sql2;
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function updateTaxonIdentifier($tid, $idName, $id): int
    {
        $returnVal = 0;
        if($tid && $idName && $id){
            $identifierName = SanitizerService::cleanInStr($this->conn, $idName);
            $identifier = SanitizerService::cleanInStr($this->conn, $id);
            $sql = 'UPDATE taxaidentifiers SET identifier = "' . $identifier . '" WHERE tid = ' . (int)$tid . ' AND `name` = "' . $identifierName . '" ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function validateNewTaxaData($dataArr): array
    {
        $dataArr['kingdomid'] = 0;
        $dataArr['family'] = '';
        if(array_key_exists('rankid',$dataArr) && (int)$dataArr['rankid'] === 10 && SanitizerService::cleanInStr($this->conn, $dataArr['sciname'])){
            $dataArr['kingdomid'] = (new TaxonKingdoms)->createTaxonKingdomRecord($dataArr['sciname']);
        }
        elseif((array_key_exists('parenttid',$dataArr) && $dataArr['parenttid']) && (!array_key_exists('kingdomid',$dataArr) || !$dataArr['kingdomid'] || !array_key_exists('family',$dataArr) || !$dataArr['family'])){
            $sqlKg = 'SELECT kingdomId, family FROM taxa WHERE tid = ' . (int)$dataArr['parenttid'] . ' ';
            //echo $sqlKg; exit;
            if($result = $this->conn->query($sqlKg)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $dataArr['kingdomid'] = $row['kingdomId'];
                    $dataArr['family'] = $row['family'];
                    unset($rows[$index]);
                }
            }
            if(!$dataArr['family'] && (int)$dataArr['rankid'] === 140){
                $dataArr['family'] = $dataArr['sciname'];
            }
        }
        if(!array_key_exists('unitname1',$dataArr) || !$dataArr['unitname1']){
            $sciNameArr = (new TaxonomyService)->parseScientificName($dataArr['sciname'], $dataArr['rankid']);
            $dataArr['unitind1'] = array_key_exists('unitind1', $sciNameArr) ? $sciNameArr['unitind1'] : '';
            $dataArr['unitname1'] = array_key_exists('unitname1', $sciNameArr) ? $sciNameArr['unitname1'] : '';
            $dataArr['unitind2'] = array_key_exists('unitind2', $sciNameArr) ? $sciNameArr['unitind2'] : '';
            $dataArr['unitname2'] = array_key_exists('unitname2', $sciNameArr) ? $sciNameArr['unitname2'] : '';
            $dataArr['unitind3'] = array_key_exists('unitind3', $sciNameArr) ? $sciNameArr['unitind3'] : '';
            $dataArr['unitname3'] = array_key_exists('unitname3', $sciNameArr) ? $sciNameArr['unitname3'] : '';
        }
        if(!array_key_exists('source',$dataArr)){
            $dataArr['source'] = '';
        }
        if(!array_key_exists('notes',$dataArr)){
            $dataArr['notes'] = '';
        }
        if(!array_key_exists('securitystatus',$dataArr)){
            $dataArr['securitystatus'] = 0;
        }
        return $dataArr;
    }
}
