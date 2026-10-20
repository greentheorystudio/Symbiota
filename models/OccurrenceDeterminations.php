<?php
include_once(__DIR__ . '/Occurrences.php');
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class OccurrenceDeterminations{

	private ?mysqli $conn;

    private array $fields = array(
        'detid' => array('dataType' => 'number', 'length' => 10),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'identifiedby' => array('dataType' => 'string', 'length' => 60),
        'dateidentified' => array('dataType' => 'string', 'length' => 45),
        'sciname' => array('dataType' => 'string', 'length' => 100),
        'verbatimscientificname' => array('dataType' => 'string', 'length' => 255),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'scientificnameauthorship' => array('dataType' => 'string', 'length' => 100),
        'identificationqualifier' => array('dataType' => 'string', 'length' => 45),
        'iscurrent' => array('dataType' => 'number', 'length' => 11),
        'printqueue' => array('dataType' => 'number', 'length' => 11),
        'identificationreferences' => array('dataType' => 'string', 'length' => 255),
        'identificationremarks' => array('dataType' => 'string', 'length' => 500),
        'sortsequence' => array('dataType' => 'number', 'length' => 10),
        'guid' => array('dataType' => 'string', 'length' => 45),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function batchCreateOccurrenceDeterminationRecordGUIDs($collid): int
    {
        $returnVal = 0;
        $sql = 'SELECT d.detid FROM omoccurdeterminations AS d LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(d.guid) LIMIT 50000 ';
        if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $row){
                $uSql = 'UPDATE omoccurdeterminations SET guid = "' . UuidService::getUuidV4() . '" WHERE detid = ' . (int)$row['detid'] . ' ';
                if($this->conn->query($uSql)){
                    $returnVal++;
                }
            }
        }
        return $returnVal;
    }

    public function clearOccurrenceDeterminationsByArr($detIdArr): int
    {
        $retVal = 0;
        if(count($detIdArr) > 0){
            $sql = 'DELETE FROM omoccurdeterminations WHERE detid IN(' . implode(',', $detIdArr) . ') ';
            if($this->conn->query($sql)){
                $retVal = $this->conn->affected_rows;
            }
        }
        return $retVal;
    }

    public function createOccurrenceDeterminationRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $occId = array_key_exists('occid', $data) ? (int)$data['occid'] : 0;
        $sciname = array_key_exists('sciname', $data) ? SanitizerService::cleanInStr($this->conn, $data['sciname']) : '';
        if($occId && $sciname){
            $isCurrent = (array_key_exists('iscurrent', $data) && (int)$data['iscurrent'] === 1);
            if($isCurrent){
                $sqlSetCur1 = 'UPDATE omoccurdeterminations SET iscurrent = 0 WHERE occid = ' . $occId;
                $this->conn->query($sqlSetCur1);
            }
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'detid' && $field !== 'guid' && array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $fieldNameArr[] = 'guid';
            $fieldValueArr[] = '"' . UuidService::getUuidV4() . '"';
            $fieldNameArr[] = 'initialtimestamp';
            $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
            $sql = 'INSERT IGNORE INTO omoccurdeterminations(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                if((int)$newID > 0){
                    if($isCurrent){
                        $this->createOccurrenceDeterminationRecordFromOccurrence($occId);
                        if(array_key_exists('tid', $data) && (int)$data['tid'] > 0){
                            $taxonData = (new Taxa)->getTaxonFromTid($data['tid']);
                            $data['family'] = $taxonData['family'];
                            $data['localitysecurity'] = $taxonData['securitystatus'];
                        }
                        (new Occurrences)->updateOccurrenceRecord($occId, $data, true);
                    }
                }
            }
        }
        return $newID;
    }

    public function createOccurrenceDeterminationRecordFromOccurrence($occid): void
    {
        $sql = 'INSERT IGNORE INTO omoccurdeterminations(occid, tid, identifiedby, dateidentified, sciname, verbatimscientificname, '.
            'scientificnameauthorship, identificationqualifier, identificationreferences, identificationremarks, sortsequence, guid) '.
            'SELECT occid, tid, IFNULL(identifiedby, "unknown"), IFNULL(dateidentified, "unknown"), sciname, verbatimScientificName, '.
            'scientificnameauthorship, identificationqualifier, identificationreferences, identificationremarks, 10, "' . UuidService::getUuidV4() . '" '.
            'FROM omoccurrences WHERE occid = ' . (int)$occid . ' ';
        $this->conn->query($sql);
    }

    public function createOccurrenceDeterminationRecordsFromUploadData($collId): int
    {
        $skipFields = array('detid', 'guid', 'verbatimscientificname', 'printqueue', 'initialtimestamp');
        $retVal = 0;
        $fieldNameArr = array();
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    $fieldNameArr[] = $field;
                }
            }
            if(count($fieldNameArr) > 0){
                $idArr = array();
                $sql = 'SELECT DISTINCT u.updid FROM uploaddetermtemp AS u LEFT JOIN omoccurdeterminations AS d ON u.occid = d.occid '.
                    'WHERE u.collid  = ' . (int)$collId . ' AND u.occid IS NOT NULL AND (ISNULL(d.occid) OR (u.sciname <> d.sciname AND u.identifiedby <> d.identifiedby AND u.dateidentified <> d.dateidentified)) LIMIT 50000 ';
                if($result = $this->conn->query($sql)){
                    while($row = $result->fetch_assoc()){
                        $idArr[] = $row['updid'];
                    }
                    $result->free();
                    if(count($idArr) > 0){
                        $sql = 'INSERT IGNORE INTO omoccurdeterminations(' . implode(',', $fieldNameArr) . ') '.
                            'SELECT ' . implode(',', $fieldNameArr) . ' FROM uploaddetermtemp '.
                            'WHERE updid IN(' . implode(',', $idArr) . ') ';
                        if($this->conn->query($sql)){
                            $retVal = $this->conn->affected_rows;
                        }
                    }
                }
            }
        }
        return $retVal;
    }

    public function deleteDeterminationRecord($detId): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM omoccurdeterminations WHERE detid = ' . (int)$detId . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function deleteOccurrenceDeterminationRecords($idType, $id): int
    {
        $retVal = 0;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'occid = ' . (int)$id . ' ';
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'occid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'collid'){
            $whereStr = 'occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ') ';
        }
        if($whereStr){
            $detIdArr = array();
            $sql = 'SELECT detid FROM omoccurdeterminations WHERE ' . $whereStr . ' ';
            if($idType === 'collid'){
                $sql .= 'LIMIT 50000 ';
            }
            if($result = $this->conn->query($sql)){
                while($row = $result->fetch_assoc()){
                    $detIdArr[] = $row['detid'];
                }
                $result->free();
                $retVal = $this->clearOccurrenceDeterminationsByArr($detIdArr);
            }
        }
        return $retVal;
    }

    public function getCollectionNullDeterminationGuidCount($collid): int
    {
        $retVal = 0;
        $sql = 'SELECT COUNT(d.detid) AS cnt FROM omoccurdeterminations AS d LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(d.guid) ';
        if($result = $this->conn->query($sql)){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                $retVal = $row['cnt'];
            }
        }
        return $retVal;
    }

    public function getDeterminationFields(): array
    {
        return $this->fields;
    }

    public function getDeterminationDataById($detid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurdeterminations '.
            'WHERE detid = ' . (int)$detid . ' ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $row[$name];
                }
            }
            if($retArr && $retArr['tid'] && (int)$retArr['tid'] > 0){
                $retArr['taxonData'] = (new Taxa)->getTaxonFromTid($retArr['tid']);
            }
        }
        return $retArr;
    }

    public function getOccurrenceDeterminationData($occid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurdeterminations '.
            'WHERE occid = ' . (int)$occid . ' ORDER BY iscurrent DESC, sortsequence ';
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
                if($nodeArr['tid'] && (int)$nodeArr['tid'] > 0){
                    $nodeArr['taxonData'] = (new Taxa)->getTaxonFromTid($nodeArr['tid']);
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxonDeterminationCount($tid): int
    {
        $retVal = 0;
        if($tid){
            $sql ='SELECT COUNT(detid) AS cnt FROM omoccurdeterminations WHERE tid = ' . (int)$tid;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $retVal = $row->cnt;
            }
            $result->free();
        }
        return $retVal;
    }

    public function makeDeterminationCurrent($detId): int
    {
        $retVal = 0;
        $determinationData = $this->getDeterminationDataById($detId);
        $this->createOccurrenceDeterminationRecordFromOccurrence($determinationData['occid']);
        $sqlSetCur1 = 'UPDATE omoccurdeterminations SET iscurrent = 0 WHERE occid = ' . (int)$determinationData['occid'];
        if($this->conn->query($sqlSetCur1)){
            if(array_key_exists('taxonData', $determinationData)){
                $determinationData['family'] = $determinationData['taxonData']['family'];
                $determinationData['localitysecurity'] = $determinationData['taxonData']['securitystatus'];
            }
            if((new Occurrences)->updateOccurrenceRecord($determinationData['occid'], $determinationData, true)){
                $sqlSetCur2 = 'UPDATE omoccurdeterminations SET iscurrent = 1 WHERE detid = ' . (int)$detId;
                if($this->conn->query($sqlSetCur2)){
                    $retVal = 1;
                }
            }
        }
        return $retVal;
    }

    public function remapTaxonDeterminations($tid, $targetTid): int
    {
        $retVal = 0;
        if($tid && $targetTid){
            $sql = 'UPDATE omoccurdeterminations SET tid = ' . (int)$targetTid . ' WHERE tid = ' . (int)$tid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function removeTaxonFromDeterminationRecords($tid): int
    {
        $retVal = 1;
        $sql = 'UPDATE omoccurdeterminations SET tid = NULL WHERE tid = ' . (int)$tid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function updateDeterminationRecord($detId, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($detId && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'detid' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE omoccurdeterminations SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE detid = ' . (int)$detId . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
                $determinationData = $this->getDeterminationDataById($detId);
                if((int)$determinationData['iscurrent'] === 1){
                    (new Occurrences)->updateOccurrenceRecord($determinationData['occid'], $editData, true);
                }
            }
        }
        return $retVal;
    }

    public function updateDetTaxonomicThesaurusLinkages($collid, $kingdomId): int
    {
        $retCnt = 0;
        $rankIdArr = array();
        if((int)$collid && $kingdomId){
            $sql = 'SELECT DISTINCT rankid FROM taxonunits WHERE kingdomid = ' . (int)$kingdomId . ' AND rankid < 180 AND rankid > 20 ORDER BY rankid DESC ';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $rankIdArr[] = $r->rankid;
            }
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
                'LEFT JOIN taxa AS t ON d.sciname = t.SciName '.
                'SET d.tid = t.tid '.
                'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(d.tid) AND t.kingdomId = ' . (int)$kingdomId . ' AND t.rankid >= 180 ';
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }
            foreach($rankIdArr as $id){
                $sql = 'UPDATE omoccurrences AS o LEFT JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
                    'LEFT JOIN taxa AS t ON d.sciname = t.SciName '.
                    'SET d.tid = t.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(d.tid) AND t.kingdomId = ' . (int)$kingdomId . ' AND t.rankid = ' . $id . ' ';
                if($this->conn->query($sql)){
                    $retCnt += $this->conn->affected_rows;
                }
            }
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
                'LEFT JOIN taxa AS t ON d.sciname = t.SciName '.
                'SET d.tid = t.tid '.
                'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(d.tid) AND t.kingdomId = ' . (int)$kingdomId . ' AND t.rankid <= 20 ';
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }
}
