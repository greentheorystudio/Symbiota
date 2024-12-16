<?php
include_once(__DIR__ . '/../services/DbService.php');

class UploadMediaTemp{

	private $conn;

    private $fields = array(
        "upmid" => array("dataType" => "number", "length" => 50),
        "tid" => array("dataType" => "number", "length" => 10),
        "url" => array("dataType" => "string", "length" => 255),
        "thumbnailurl" => array("dataType" => "string", "length" => 255),
        "originalurl" => array("dataType" => "string", "length" => 255),
        "accessuri" => array("dataType" => "string", "length" => 2048),
        "photographer" => array("dataType" => "string", "length" => 100),
        "title" => array("dataType" => "string", "length" => 255),
        "imagetype" => array("dataType" => "string", "length" => 50),
        "format" => array("dataType" => "string", "length" => 45),
        "caption" => array("dataType" => "string", "length" => 100),
        "description" => array("dataType" => "string", "length" => 1000),
        "creator" => array("dataType" => "string", "length" => 45),
        "owner" => array("dataType" => "string", "length" => 100),
        "type" => array("dataType" => "string", "length" => 45),
        "sourceurl" => array("dataType" => "string", "length" => 255),
        "furtherinformationurl" => array("dataType" => "string", "length" => 2048),
        "referenceurl" => array("dataType" => "string", "length" => 255),
        "language" => array("dataType" => "string", "length" => 45),
        "copyright" => array("dataType" => "string", "length" => 255),
        "accessrights" => array("dataType" => "string", "length" => 255),
        "usageterms" => array("dataType" => "string", "length" => 255),
        "rights" => array("dataType" => "string", "length" => 255),
        "locality" => array("dataType" => "string", "length" => 250),
        "locationcreated" => array("dataType" => "string", "length" => 1000),
        "bibliographiccitation" => array("dataType" => "string", "length" => 255),
        "occid" => array("dataType" => "number", "length" => 10),
        "collid" => array("dataType" => "number", "length" => 10),
        "dbpk" => array("dataType" => "string", "length" => 150),
        "publisher" => array("dataType" => "string", "length" => 255),
        "contributor" => array("dataType" => "string", "length" => 255),
        "sourceidentifier" => array("dataType" => "string", "length" => 150),
        "notes" => array("dataType" => "string", "length" => 350),
        "anatomy" => array("dataType" => "string", "length" => 100),
        "username" => array("dataType" => "string", "length" => 45),
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
                if(($fieldMapping && $fieldMapping[$key] !== 'unmapped') || !$fieldMapping){
                    $field = $fieldMapping ? $fieldMapping[$key] : $key;
                    if($field === 'language' || $field === 'owner'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                    $sourceKeyArr[] = $key;
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
            $sql = 'INSERT INTO uploadmediatemp(' . implode(',', $fieldNameArr) . ') '.
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
            $sql = 'DELETE FROM uploadmediatemp WHERE collid = ' . (int)$collid . ' ';
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
