<?php
include_once(__DIR__ . '/../services/DbService.php');

class UploadDeterminationTemp{

	private $conn;

    private $fields = array(
        "updid" => array("dataType" => "number", "length" => 50),
        "occid" => array("dataType" => "number", "length" => 10),
        "collid" => array("dataType" => "number", "length" => 10),
        "dbpk" => array("dataType" => "string", "length" => 150),
        "identifiedby" => array("dataType" => "string", "length" => 60),
        "dateidentified" => array("dataType" => "string", "length" => 45),
        "dateidentifiedinterpreted" => array("dataType" => "date", "length" => 0),
        "sciname" => array("dataType" => "string", "length" => 100),
        "tid" => array("dataType" => "number", "length" => 10),
        "scientificnameauthorship" => array("dataType" => "string", "length" => 100),
        "identificationqualifier" => array("dataType" => "string", "length" => 45),
        "iscurrent" => array("dataType" => "number", "length" => 2),
        "dettype" => array("dataType" => "string", "length" => 45),
        "identificationreferences" => array("dataType" => "string", "length" => 255),
        "identificationremarks" => array("dataType" => "string", "length" => 255),
        "sourceidentifier" => array("dataType" => "string", "length" => 45),
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

    public function batchCreateRecords($collid, $data, $fieldMapping =  null): int
    {
        $recordsCreated = 0;
        $fieldNameArr = array();
        $sourceKeyArr = array();
        $valueArr = array();
        if($collid){
            $sourceDataKeys = array_keys($data[0]);
            $fieldNameArr[] = 'collid';
            foreach($sourceDataKeys as $key){
                if($key || (string)$key === '0'){
                    if(($fieldMapping && array_key_exists($key, $fieldMapping) && $fieldMapping[$key] !== 'unmapped') || !$fieldMapping){
                        $field = $fieldMapping ? $fieldMapping[$key] : $key;
                        $fieldNameArr[] = $field;
                        $sourceKeyArr[] = $key;
                    }
                }
            }
            foreach($data as $dataArr){
                $dataValueArr = array();
                $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $collid, $this->fields['collid']);
                foreach($sourceKeyArr as $key){
                    $targetField = $fieldMapping ? $fieldMapping[$key] : $key;
                    $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $dataArr[$key], $this->fields[$targetField]);
                }
                $valueArr[] = '(' . implode(',', $dataValueArr) . ')';
            }
            $sql = 'INSERT INTO uploaddetermtemp(' . implode(',', $fieldNameArr) . ') '.
                'VALUES ' . implode(',', $valueArr) . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $recordsCreated = $this->conn->affected_rows;
            }
        }
        return $recordsCreated;
    }

    public function clearCollectionData($collid): bool
    {
        if($collid){
            $sql = 'DELETE FROM uploaddetermtemp WHERE collid = ' . (int)$collid . ' ';
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
}
