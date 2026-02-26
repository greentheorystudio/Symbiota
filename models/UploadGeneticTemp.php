<?php
include_once(__DIR__ . '/../services/DbService.php');

class UploadGeneticTemp{

	private $conn;

    private $fields = array(
        'upgid' => array('dataType' => 'number', 'length' => 11),
        'sourceidentifier' => array('dataType' => 'string', 'length' => 150),
        'sourcename' => array('dataType' => 'string', 'length' => 150),
        'description' => array('dataType' => 'string', 'length' => 500),
        'targetgene' => array('dataType' => 'string', 'length' => 500),
        'targetsubfragment' => array('dataType' => 'string', 'length' => 500),
        'dnasequence' => array('dataType' => 'text', 'length' => 0),
        'url' => array('dataType' => 'text', 'length' => 0),
        'notes' => array('dataType' => 'string', 'length' => 1000),
        'authors' => array('dataType' => 'string', 'length' => 500),
        'authorinstitution' => array('dataType' => 'string', 'length' => 500),
        'reference' => array('dataType' => 'string', 'length' => 750),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'collid' => array('dataType' => 'number', 'length' => 10),
        'dbpk' => array('dataType' => 'string', 'length' => 150),
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
        $skipFields = array('upgid', 'occid', 'collid', 'initialtimestamp');
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
                $sql = 'INSERT INTO uploadgenetictemp(' . implode(',', $fieldNameArr) . ') '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
    }

    public function clearCollectionData($collid): bool
    {
        if($collid){
            $sql = 'DELETE FROM uploadgenetictemp WHERE collid = ' . (int)$collid . ' ';
            if($this->conn->query($sql)){
                return true;
            }
        }
        return false;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getUploadCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(upgid) AS cnt FROM uploadgenetictemp WHERE collid  = ' . (int)$collid . ' ';
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
            $sql = 'UPDATE uploadgenetictemp AS u LEFT JOIN uploadspectemp AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                'SET u.occid = o.occid '.
                'WHERE u.collid  = ' . (int)$collid . ' AND u.dbpk IS NOT NULL AND o.occid IS NOT NULL ';
            $this->conn->query($sql);
        }
    }

    public function removeExistingGeneticDataFromUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE u.* FROM uploadgenetictemp AS u LEFT JOIN omoccurgenetic AS g ON u.occid = g.occid '.
                'WHERE u.collid  = ' . $collid . ' AND u.sourceidentifier = g.sourceidentifier ';
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
            $sql = 'DELETE u.* FROM uploadgenetictemp AS u LEFT JOIN omoccurrences AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                'WHERE u.collid  = ' . $collid . ' AND u.dbpk IS NOT NULL AND o.occid IS NOT NULL ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }
}
