<?php
include_once(__DIR__ . '/../services/DbService.php');

class GlossarySources{

	private $conn;

    private $fields = array(
        'tid' => array('dataType' => 'number', 'length' => 10),
        'contributorterm' => array('dataType' => 'string', 'length' => 1000),
        'contributorimage' => array('dataType' => 'string', 'length' => 1000),
        'translator' => array('dataType' => 'string', 'length' => 1000),
        'additionalsources' => array('dataType' => 'string', 'length' => 1000),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createGlossarySourceRecord($data): int
    {
        $newID = 0;
        if(array_key_exists('tid', $data) && (int)$data['tid'] > 0){
            $fieldNameArr = array();
            $fieldValueArr = array();
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'initialtimestamp' && array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $fieldNameArr[] = 'initialtimestamp';
            $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
            $sql = 'INSERT INTO glossarysources(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            if($this->conn->query($sql)){
                $newID = (int)$data['tid'];
            }
        }
        return $newID;
    }

    public function deleteGlossarySourceRecord($tid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM glossarysources WHERE tid = ' . (int)$tid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getGlossarySourceData($tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM glossarysources WHERE tid = ' . (int)$tid . ' ';
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

    public function updateGlossarySourceRecord($tid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($tid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE glossarysources SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE tid = ' . (int)$tid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
