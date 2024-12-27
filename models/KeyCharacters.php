<?php
include_once(__DIR__ . '/../services/DbService.php');

class KeyCharacters{

	private $conn;

    private $fields = array(
        "cid" => array("dataType" => "number", "length" => 10),
        "chid" => array("dataType" => "number", "length" => 10),
        "charactername" => array("dataType" => "string", "length" => 150),
        "description" => array("dataType" => "string", "length" => 255),
        "infourl" => array("dataType" => "string", "length" => 500),
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

    public function createKeyCharacterRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'cid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO keycharacters(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteKeyCharacterRecord($cid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM keycharacters WHERE cid = ' . (int)$cid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getKeyCharacterData($cid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM keycharacters WHERE chid = ' . (int)$cid . ' ';
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

    public function updateKeyCharacterRecord($cid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($cid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'cid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE keycharacters SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE cid = ' . (int)$cid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
