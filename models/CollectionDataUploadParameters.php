<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class CollectionDataUploadParameters {

    private $conn;

    private $fields = array(
        "uspid" => array("dataType" => "number", "length" => 10),
        "collid" => array("dataType" => "number", "length" => 10),
        "uploadtype" => array("dataType" => "number", "length" => 10),
        "title" => array("dataType" => "string", "length" => 45),
        "dwcpath" => array("dataType" => "text", "length" => 0),
        "queryparamjson" => array("dataType" => "json", "length" => 0),
        "cleansql" => array("dataType" => "json", "length" => 0),
        "configjson" => array("dataType" => "json", "length" => 0),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

	public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function createCollectionDataUploadParameterRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $collId = array_key_exists('collid', $data) ? (int)$data['collid'] : 0;
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    if($field === 'queryparamjson' || $field === 'cleansql' || $field === 'configjson'){
                        $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, json_encode($data[$field]), $fieldArr['dataType']);
                    }
                    else{
                        $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                    }
                }
            }
            $sql = 'INSERT INTO omcolldatauploadparameters(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
            }
        }
        return $newID;
    }

    public function deleteCollectionDataUploadParameterRecord($uspid): int
    {
        $retVal = 1;
        if($uspid){
            $this->deleteFieldMappingData($uspid);
            $sql = 'DELETE FROM omcolldatauploadparameters WHERE uspid = ' . (int)$uspid . ' ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function deleteFieldMappingData($uspid): void
    {
        if($uspid){
            $sql = 'DELETE FROM uploadspecmap WHERE uspid = ' . (int)$uspid . ' ';
            $this->conn->query($sql);
        }
    }

    public function getCollectionDataUploadParametersByCollection($collid): array
    {
        $retArr = array();
        if($collid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM omcolldatauploadparameters WHERE collid = ' . (int)$collid . ' ORDER BY title ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        if($row[$name] && ($name === 'queryparamjson' || $name === 'cleansql' || $name === 'configjson')){
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

    public function getUploadParametersFieldMapping($uspid): array
    {
        $retArr = array();
        if($uspid){
            $sql = 'SELECT symbspecfield, sourcefield '.
                'FROM uploadspecmap WHERE uspid = ' . (int)$uspid . ' ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    $nodeArr['symbspecfield'] = $row['symbspecfield'];
                    $nodeArr['sourcefield'] = $row['sourcefield'];
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function saveFieldMapping($uspid, $fieldMappingData): int
    {
        $retVal = 0;
        $sqlValueArr = array();
        if($uspid && $fieldMappingData){
            $this->deleteFieldMappingData($uspid);
            foreach($fieldMappingData['occurrence'] as $source => $target){
                $sqlValueArr[] = '(' . $uspid . ',"' . $source . '","' . $target . '")';
            }
            foreach($fieldMappingData['secondary'] as $source => $target){
                $sqlValueArr[] = '(' . $uspid . ',"' . $source . '","' . $target . '")';
            }
            foreach($fieldMappingData['determination'] as $source => $target){
                $sqlValueArr[] = '(' . $uspid . ',"' . $source . '","' . $target . '")';
            }
            foreach($fieldMappingData['multimedia'] as $source => $target){
                $sqlValueArr[] = '(' . $uspid . ',"' . $source . '","' . $target . '")';
            }
            foreach($fieldMappingData['mof'] as $source => $target){
                $sqlValueArr[] = '(' . $uspid . ',"' . $source . '","' . $target . '")';
            }
            if(count($sqlValueArr) > 0){
                $sql = 'INSERT INTO uploadspecmap(uspid, sourcefield, symbspecfield) VALUES ' . implode(',', $sqlValueArr) . ' ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $retVal = 1;
                }
            }
        }
        return $retVal;
    }

    public function updateCollectionDataUploadParameterRecord($uspid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($uspid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    if($field === 'queryparamjson' || $field === 'cleansql' || $field === 'configjson'){
                        $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, json_encode($editData[$field]), $fieldArr['dataType']);
                    }
                    else{
                        $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                    }
                }
            }
            if(count($sqlPartArr) > 0){
                $sql = 'UPDATE omcolldatauploadparameters SET ' . implode(', ', $sqlPartArr) . ' '.
                    'WHERE uspid = ' . (int)$uspid . ' ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $retVal = 1;
                }
            }
        }
        return $retVal;
    }
}
