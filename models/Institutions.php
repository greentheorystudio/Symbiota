<?php
include_once(__DIR__ . '/../services/DbService.php');

class Institutions{

    private $conn;

    private $fields = array(
        'iid' => array('dataType' => 'number', 'length' => 0),
        'institutioncode' => array('dataType' => 'string', 'length' => 45),
        'institutionname' => array('dataType' => 'string', 'length' => 150),
        'institutionname2' => array('dataType' => 'string', 'length' => 150),
        'address1' => array('dataType' => 'string', 'length' => 150),
        'address2' => array('dataType' => 'string', 'length' => 150),
        'city' => array('dataType' => 'string', 'length' => 45),
        'stateprovince' => array('dataType' => 'string', 'length' => 45),
        'postalcode' => array('dataType' => 'string', 'length' => 45),
        'country' => array('dataType' => 'string', 'length' => 45),
        'phone' => array('dataType' => 'string', 'length' => 45),
        'contact' => array('dataType' => 'string', 'length' => 65),
        'email' => array('dataType' => 'string', 'length' => 45),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 150)
    );

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function createInstitutionRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'iid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO institutions(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteInstitutionRecord($iid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM institutions WHERE iid = ' . (int)$iid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }
    public function getInstitutionsArr(): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM institutions ORDER BY institutionname';
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
    public function getInstitutionData($iid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM institutions WHERE iid = ' . (int)$iid . ' ';
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

    public function updateInstitutionRecord($iid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($iid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'iid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE institutions SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE iid = ' . (int)$iid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
