<?php
include_once(__DIR__ . '/../services/DbService.php');

class KeyCharacterHeadings{

	private $conn;

    private $fields = array(
        "chid" => array("dataType" => "number", "length" => 10),
        "headingname" => array("dataType" => "string", "length" => 255),
        "language" => array("dataType" => "string", "length" => 45),
        "langid" => array("dataType" => "number", "length" => 11),
        "sortsequence" => array("dataType" => "number", "length" => 11),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createKeyCharacterHeadingRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'chid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO keycharacterheadings(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteKeyCharacterHeadingRecord($chid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM keycharacterheadings WHERE chid = ' . (int)$chid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getKeyCharacterHeadingData($chid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM keycharacterheadings WHERE chid = ' . (int)$chid . ' ';
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
        }
        return $retArr;
    }

    public function updateKeyCharacterHeadingRecord($chid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($chid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'chid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE keycharacterheadings SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE chid = ' . (int)$chid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
