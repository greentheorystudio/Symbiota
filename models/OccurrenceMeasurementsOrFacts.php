<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceMeasurementsOrFacts{

	private $conn;

    private $fields = array(
        "mofid" => array("dataType" => "number", "length" => 10),
        "eventid" => array("dataType" => "number", "length" => 10),
        "occid" => array("dataType" => "number", "length" => 10),
        "field" => array("dataType" => "string", "length" => 250),
        "datavalue" => array("dataType" => "string", "length" => 1000),
        "enteredby" => array("dataType" => "string", "length" => 250),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function deleteOccurrenceMofRecords($idType, $id): int
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
            $sql = 'DELETE FROM ommofextension WHERE ' . $whereStr . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function getMofDataByTypeAndId($type, $id): array
    {
        $retArr = array();
        if($type === 'event'){
            $field = 'eventid';
        }
        else{
            $field = 'occid';
        }
        $sql = 'SELECT mofid, field, datavalue, initialtimestamp '.
            'FROM ommofextension WHERE ' . $field . ' = ' . (int)$id . ' ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[$row['field']] = $row['datavalue'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function processMofEdits($type, $id, $editData): int
    {
        $returnVal = 0;
        if($type === 'event'){
            $idField = 'eventid';
        }
        else{
            $idField = 'occid';
        }
        $sql = '';
        if(array_key_exists('add', $editData) && count($editData['add']) > 0){
            foreach($editData['add'] as $addArr){
                $key = SanitizerService::cleanInStr($this->conn, $addArr['field']);
                $value = SanitizerService::cleanInStr($this->conn, $addArr['value']);
                $sql .= 'INSERT INTO ommofextension(' . $idField . ', field, datavalue) '.
                    'VALUES (' . (int)$id . ', "' . $key . '", "' . $value . '");';
            }
        }
        if(array_key_exists('delete', $editData) && count($editData['delete']) > 0){
            foreach($editData['delete'] as $field){
                $key = SanitizerService::cleanInStr($this->conn, $field);
                $sql .= 'DELETE FROM ommofextension WHERE ' . $idField . ' = ' . (int)$id . ' AND field = "' . $key . '";';
            }
        }
        if(array_key_exists('update', $editData) && count($editData['update']) > 0){
            foreach($editData['update'] as $updateArr){
                $key = SanitizerService::cleanInStr($this->conn, $updateArr['field']);
                $value = SanitizerService::cleanInStr($this->conn, $updateArr['value']);
                $sql .= 'UPDATE ommofextension SET datavalue = "' . $value . '" '.
                    'WHERE ' . $idField . ' = ' . (int)$id . ' AND field = "' . $key . '";';
            }
        }
        if($this->conn->multi_query($sql)){
            $returnVal = 1;
        }
        return $returnVal;
    }
}
