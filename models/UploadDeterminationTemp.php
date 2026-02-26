<?php
include_once(__DIR__ . '/../services/DbService.php');

class UploadDeterminationTemp{

	private $conn;

    private $fields = array(
        'updid' => array('dataType' => 'number', 'length' => 50),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'collid' => array('dataType' => 'number', 'length' => 10),
        'dbpk' => array('dataType' => 'string', 'length' => 150),
        'identifiedby' => array('dataType' => 'string', 'length' => 60),
        'dateidentified' => array('dataType' => 'string', 'length' => 45),
        'dateidentifiedinterpreted' => array('dataType' => 'date', 'length' => 0),
        'sciname' => array('dataType' => 'string', 'length' => 100),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'scientificnameauthorship' => array('dataType' => 'string', 'length' => 100),
        'identificationqualifier' => array('dataType' => 'string', 'length' => 45),
        'iscurrent' => array('dataType' => 'number', 'length' => 2),
        'dettype' => array('dataType' => 'string', 'length' => 45),
        'identificationreferences' => array('dataType' => 'string', 'length' => 255),
        'identificationremarks' => array('dataType' => 'string', 'length' => 255),
        'sourceidentifier' => array('dataType' => 'string', 'length' => 45),
        'sortsequence' => array('dataType' => 'number', 'length' => 10),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function batchCreateRecords($collid, $data, $fieldMapping =  null): int
    {
        $recordsCreated = 0;
        $fieldNameArr = array();
        $valueArr = array();
        $skipFields = array('updid', 'occid', 'collid', 'tid', 'initialtimestamp');
        $mappedFields = array();
        if($collid){
            $fieldNameArr[] = 'collid';
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    $fieldNameArr[] = $field;
                    if($fieldMapping){
                        $mappedFieldVal = null;
                        $mappedKey = $fieldMapping[$field] ?? null;
                        if(($mappedKey && (string)$mappedKey !== 'unmapped') || (string)$mappedKey === '0'){
                            $mappedFieldVal = (string)$mappedKey;
                        }
                        $mappedFields[$field] = $mappedFieldVal;
                    }
                    elseif(array_key_exists($field, $data[0])){
                        $mappedFields[$field] = $field;
                    }
                }
            }
            foreach($data as $dataArr){
                $dataValueArr = array();
                $detData = array();
                $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $collid, $this->fields['collid']);
                foreach($mappedFields as $field => $key){
                    $detData[$field] = ($key || (string)$key === '0') ? $dataArr[$key] : null;
                }
                foreach($this->fields as $field => $fieldArr){
                    if(!in_array($field, $skipFields)){
                        $dataValue = $detData[$field] ?? null;
                        $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, SanitizerService::cleanInStr($this->conn, $dataValue), $fieldArr);
                    }
                }
                $valueArr[] = '(' . implode(',', $dataValueArr) . ')';
            }
            if(count($valueArr) > 0){
                $sql = 'INSERT INTO uploaddetermtemp(' . implode(',', $fieldNameArr) . ') '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
    }

    public function clearCollectionData($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE FROM uploaddetermtemp WHERE collid = ' . (int)$collid . ' LIMIT 50000 ';
            if($this->conn->query($sql)){
                $returnVal = $this->conn->affected_rows;
            }
        }
        return $returnVal;
    }

    public function clearOrphanedRecords($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE FROM uploaddetermtemp WHERE dbpk NOT IN(SELECT DISTINCT dbpk FROM uploadspectemp '.
                'WHERE collid = ' . (int)$collid . ' AND dbpk IS NOT NULL) LIMIT 10000 ';
            if($this->conn->query($sql)){
                $returnVal = $this->conn->affected_rows;
            }
        }
        return $returnVal;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getUploadCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(updid) AS cnt FROM uploaddetermtemp WHERE collid  = ' . (int)$collid . ' ';
            if($result = $this->conn->query($sql)){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    $returnVal = (int)$row['cnt'];
                }
            }
        }
        return $returnVal;
    }

    public function populateOccidFromUploadOccurrenceData($collid): void
    {
        if($collid){
            $sql = 'UPDATE uploaddetermtemp AS u LEFT JOIN uploadspectemp AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                'SET u.occid = o.occid '.
                'WHERE u.collid  = ' . (int)$collid . ' AND u.dbpk IS NOT NULL AND o.occid IS NOT NULL ';
            $this->conn->query($sql);
        }
    }

    public function removeExistingDeterminationDataFromUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE u.* FROM uploaddetermtemp AS u LEFT JOIN omoccurdeterminations AS d ON u.occid = d.occid '.
                'WHERE u.collid  = ' . $collid . ' AND u.sciname = d.sciname AND u.identifiedby = d.identifiedby AND u.dateidentified = d.dateidentified ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function removeExistingOccurrenceDataFromUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE FROM uploaddetermtemp AS u WHERE u.collid  = ' . $collid . ' AND u.dbpk IS NOT NULL '.
            'AND u.dbpk IN(SELECT dbpk FROM omoccurrences WHERE collid = ' . $collid . ')  LIMIT 50000 ';
            if($this->conn->query($sql)){
                $returnVal = $this->conn->affected_rows;
            }
        }
        return $returnVal;
    }
}
