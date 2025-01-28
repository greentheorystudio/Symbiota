<?php
include_once(__DIR__ . '/Occurrences.php');
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class OccurrenceDeterminations{

	private $conn;

    private $fields = array(
        "detid" => array("dataType" => "number", "length" => 10),
        "occid" => array("dataType" => "number", "length" => 10),
        "identifiedby" => array("dataType" => "string", "length" => 60),
        "dateidentified" => array("dataType" => "string", "length" => 45),
        "sciname" => array("dataType" => "string", "length" => 100),
        "verbatimscientificname" => array("dataType" => "string", "length" => 255),
        "tid" => array("dataType" => "number", "length" => 10),
        "scientificnameauthorship" => array("dataType" => "string", "length" => 100),
        "identificationqualifier" => array("dataType" => "string", "length" => 45),
        "iscurrent" => array("dataType" => "number", "length" => 11),
        "printqueue" => array("dataType" => "number", "length" => 11),
        "identificationreferences" => array("dataType" => "string", "length" => 255),
        "identificationremarks" => array("dataType" => "string", "length" => 500),
        "sortsequence" => array("dataType" => "number", "length" => 10),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
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
        $returnVal = 1;
        $valueArr = array();
        $insertPrefix = 'INSERT INTO guidoccurdeterminations(guid, detid) VALUES ';
        $sql = 'SELECT d.detid FROM omoccurdeterminations AS d LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.
            'WHERE o.collid = ' . (int)$collid . ' AND d.detid NOT IN(SELECT detid FROM guidoccurdeterminations) ';
        if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $row){
                if(count($valueArr) === 5000){
                    $sql2 = $insertPrefix . implode(',', $valueArr);
                    if(!$this->conn->query($sql2)){
                        $returnVal = 0;
                    }
                    $valueArr = array();
                }
                if($row['detid']){
                    $guid = UuidService::getUuidV4();
                    $valueArr[] = '("' . $guid . '",' . $row['detid'] . ')';
                }
            }
            if($returnVal && count($valueArr) > 0){
                $sql2 = $insertPrefix . implode(',', $valueArr);
                $this->conn->query($sql2);
            }
        }
        return $returnVal;
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
                if($field !== 'detid' && array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $fieldNameArr[] = 'initialtimestamp';
            $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
            $sql = 'INSERT INTO omoccurdeterminations(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                $guid = UuidService::getUuidV4();
                $this->conn->query('INSERT INTO guidoccurdeterminations(guid, detid) VALUES("' . $guid . '",' . $newID . ')');
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
        return $newID;
    }

    public function createOccurrenceDeterminationRecordFromOccurrence($occid): void
    {
        $sql = 'INSERT IGNORE INTO omoccurdeterminations(occid, tid, identifiedby, dateidentified, sciname, verbatimscientificname, '.
            'scientificnameauthorship, identificationqualifier, identificationreferences, identificationremarks, sortsequence) '.
            'SELECT occid, tid, IFNULL(identifiedby, "unknown"), IFNULL(dateidentified, "unknown"), sciname, verbatimScientificName, '.
            'scientificnameauthorship, identificationqualifier, identificationreferences, identificationremarks, 10 '.
            'FROM omoccurrences WHERE occid = ' . (int)$occid . ' ';
        //echo "<div>".$sqlInsert."</div>";
        if($this->conn->query($sql)){
            $guid = UuidService::getUuidV4();
            $detId = $this->conn->insert_id;
            $this->conn->query('INSERT IGNORE INTO guidoccurdeterminations(guid, detid) VALUES("' . $guid . '" ,' . $detId . ')');
        }
    }

    public function createOccurrenceDeterminationRecordsFromUploadData($collId): int
    {
        $skipFields = array('detid', 'verbatimscientificname', 'printqueue', 'initialtimestamp');
        $retVal = 0;
        $fieldNameArr = array();
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    $fieldNameArr[] = $field;
                }
            }
            if(count($fieldNameArr) > 0){
                $sql = 'INSERT INTO omoccurdeterminations(' . implode(',', $fieldNameArr) . ') '.
                    'SELECT ' . implode(',', $fieldNameArr) . ' FROM uploaddetermtemp '.
                    'WHERE collid = ' . (int)$collId . ' AND occid IS NOT NULL ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $retVal = 1;
                }
            }
        }
        return $retVal;
    }

    public function deleteDeterminationRecord($detId): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM guidoccurdeterminations WHERE detid = ' . (int)$detId . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
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
            $whereStr = 'd.occid = ' . (int)$id;
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'd.occid IN(' . implode(',', $id) . ')';
        }
        elseif($idType === 'collid'){
            $whereStr = 'd.occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ')';
        }
        if($whereStr){
            $sql = 'DELETE g.* FROM guidoccurdeterminations AS g LEFT JOIN omoccurdeterminations AS d ON g.detid = d.detid WHERE ' . $whereStr . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
            if($retVal){
                $sql = 'DELETE d.* FROM omoccurdeterminations AS d WHERE ' . $whereStr . ' ';
                if($this->conn->query($sql)){
                    $retVal = 1;
                }
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
        //echo '<div>'.$sql.'</div>';
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
        //echo '<div>'.$sql.'</div>';
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
            //echo "<div>".$sql."</div>";
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
}
