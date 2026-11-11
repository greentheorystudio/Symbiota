<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Datasets{

	private ?mysqli $conn;

    private array $fields = array(
        'datasetid' => array('dataType' => 'number', 'length' => 10),
        'name' => array('dataType' => 'string', 'length' => 100),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'description' => array('dataType' => 'string', 'length' => 2000),
        'contact' => array('dataType' => 'string', 'length' => 250),
        'rights' => array('dataType' => 'string', 'length' => 250),
        'sortsequence' => array('dataType' => 'number', 'length' => 10),
        'uid' => array('dataType' => 'number', 'length' => 10),
        'collid' => array('dataType' => 'number', 'length' => 10),
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

    public function createDatasetRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'datasetid' && $field !== 'uid' && $field !== 'guid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                if($field === 'name'){
                    $fieldNameArr[] = '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = $field;
                }
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'uid';
        $fieldValueArr[] = $GLOBALS['SYMB_UID'] ?: 'NULL';
        $fieldNameArr[] = 'guid';
        $fieldValueArr[] = '"' . UuidService::getUuidV4() . '"';
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO omoccurdatasets(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteDatasetRecord($datasetid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM omoccurdatasetlink WHERE datasetid = ' . (int)$datasetid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurdatasets WHERE datasetid = ' . (int)$datasetid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function deleteOccurrenceDatasetRecords($idType, $id): int
    {
        $retVal = 0;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'occid = ' . (int)$id;
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'occid IN(' . implode(',', $id) . ')';
        }
        elseif($idType === 'collid'){
            $whereStr = 'occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ')';
        }
        if($whereStr){
            $sql = 'DELETE FROM omoccurdatasetlink WHERE ' . $whereStr . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function updateDatasetRecord($datasetid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($datasetid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'datasetid' && $field !== 'uid' && $field !== 'guid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    if($field === 'name'){
                        $fieldName = '`' . $field . '`';
                    }
                    else{
                        $fieldName = $field;
                    }
                    $sqlPartArr[] = $fieldName . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE omoccurdatasets SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE datasetid = ' . (int)$datasetid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
