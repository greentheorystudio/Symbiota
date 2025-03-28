<?php
include_once(__DIR__ . '/KeyCharacters.php');
include_once(__DIR__ . '/../services/DbService.php');

class KeyCharacterStates{

	private $conn;

    private $fields = array(
        "csid" => array("dataType" => "number", "length" => 10),
        "cid" => array("dataType" => "number", "length" => 10),
        "characterstatename" => array("dataType" => "string", "length" => 255),
        "description" => array("dataType" => "string", "length" => 255),
        "infourl" => array("dataType" => "string", "length" => 255),
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

    public function createKeyCharacterStateRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'csid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO keycharacterstates(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteKeyCharacterStateRecord($csid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM keycharacterstates WHERE csid = ' . (int)$csid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getKeyCharacterStateData($csid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM keycharacterstates WHERE csid = ' . (int)$csid . ' ';
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

    public function getTaxaKeyCharacterStates($tidArr): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'cs');
        $sql = 'SELECT tl.tid, ' . implode(',', $fieldNameArr) . ' '.
            'FROM keycharacterstatetaxalink AS tl LEFT JOIN keycharacterstates AS cs ON tl.csid = cs.csid '.
            'WHERE tl.tid IN(' . implode(',', $tidArr) . ') ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(!array_key_exists($row['tid'], $retArr)){
                    $retArr[$row['tid']] = array();
                }
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[$row['tid']][] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getKeyCharacterStatesArr($csidArr, $includeFullKeyData = false): array
    {
        $retArr = array();
        $cidArr = array();
        if(count($csidArr) > 0){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT DISTINCT ' . implode(',', $fieldNameArr) . ' '.
                'FROM keycharacterstates WHERE csid IN(' . implode(',', $csidArr) . ') ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $retArr['character-states'] = array();
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if($includeFullKeyData && !in_array((int)$row['cid'], $cidArr, true)){
                        $cidArr[] = (int)$row['cid'];
                    }
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        $nodeArr[$name] = $row[$name];
                    }
                    $retArr['character-states'][] = $nodeArr;
                    unset($rows[$index]);
                }
                if($includeFullKeyData){
                    $keyDataArr = (new KeyCharacters)->getKeyCharactersArr($cidArr, $includeFullKeyData);
                    $retArr['characters'] = $keyDataArr['characters'];
                    $retArr['character-headings'] = $keyDataArr['character-headings'];
                }
            }
        }
        return $retArr;
    }

    public function updateKeyCharacterStateRecord($csid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($csid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'csid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE keycharacterstates SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE csid = ' . (int)$csid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
