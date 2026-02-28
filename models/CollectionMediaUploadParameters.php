<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class CollectionMediaUploadParameters {

    private $conn;

    private $fields = array(
        'spprid' => array('dataType' => 'number', 'length' => 10),
        'collid' => array('dataType' => 'number', 'length' => 10),
        'title' => array('dataType' => 'string', 'length' => 100),
        'filenamepatternmatch' => array('dataType' => 'string', 'length' => 500),
        'patternmatchfield' => array('dataType' => 'string', 'length' => 255),
        'configjson' => array('dataType' => 'json', 'length' => 0),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

	public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function createCollectionMediaUploadParameterRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $collId = array_key_exists('collid', $data) ? (int)$data['collid'] : 0;
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    if($field === 'configjson'){
                        $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, json_encode($data[$field]), $fieldArr['dataType']);
                    }
                    else{
                        $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                    }
                }
            }
            $sql = 'INSERT INTO omcollmediauploadparameters(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
            }
        }
        return $newID;
    }

    public function deleteCollectionMediaUploadParameterRecord($spprid): int
    {
        $retVal = 0;
        if($spprid){
            $sql = 'DELETE FROM omcollmediauploadparameters WHERE spprid = ' . (int)$spprid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function getCollectionMediaUploadParametersByCollection($collid): array
    {
        $retArr = array();
        if($collid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM omcollmediauploadparameters WHERE collid = ' . (int)$collid . ' ORDER BY title ';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        if($row[$name] && $name === 'configjson'){
                            $nodeArr[$name] = json_decode($row[$name], true);
                        }
                        else{
                            $nodeArr[$name] = $row[$name];
                        }
                    }
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function updateCollectionMediaUploadParameterRecord($spprid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($spprid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    if($field === 'configjson'){
                        $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, json_encode($editData[$field]), $fieldArr['dataType']);
                    }
                    else{
                        $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                    }
                }
            }
            if(count($sqlPartArr) > 0){
                $sql = 'UPDATE omcollmediauploadparameters SET ' . implode(', ', $sqlPartArr) . ' '.
                    'WHERE spprid = ' . (int)$spprid . ' ';
                if($this->conn->query($sql)){
                    $retVal = 1;
                }
            }
        }
        return $retVal;
    }
}
