<?php
include_once(__DIR__ . '/../services/DbService.php');

class Media{

	private $conn;

    private $fields = array(
        "mediaid" => array("dataType" => "number", "length" => 10),
        "tid" => array("dataType" => "number", "length" => 10),
        "occid" => array("dataType" => "number", "length" => 10),
        "accessuri" => array("dataType" => "string", "length" => 2048),
        "sourceurl" => array("dataType" => "string", "length" => 255),
        "title" => array("dataType" => "string", "length" => 255),
        "creatoruid" => array("dataType" => "number", "length" => 10),
        "creator" => array("dataType" => "string", "length" => 45),
        "type" => array("dataType" => "string", "length" => 45),
        "format" => array("dataType" => "string", "length" => 45),
        "owner" => array("dataType" => "string", "length" => 250),
        "furtherinformationurl" => array("dataType" => "string", "length" => 2048),
        "language" => array("dataType" => "string", "length" => 45),
        "usageterms" => array("dataType" => "string", "length" => 255),
        "rights" => array("dataType" => "string", "length" => 255),
        "bibliographiccitation" => array("dataType" => "string", "length" => 255),
        "publisher" => array("dataType" => "string", "length" => 255),
        "contributor" => array("dataType" => "string", "length" => 255),
        "locationcreated" => array("dataType" => "string", "length" => 1000),
        "description" => array("dataType" => "string", "length" => 1000),
        "sortsequence" => array("dataType" => "number", "length" => 10),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
		if($this->conn) {
            $this->conn->close();
        }
	}

    public function createMediaRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'mediaid' && array_key_exists($field, $data)){
                if($field === 'language' || $field === 'owner'){
                    $fieldNameArr[] = '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = $field;
                }
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT IGNORE INTO media(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteMediaRecord($mediaid): int
    {
        $retVal = 1;
        $data = $this->getMediaData($mediaid);
        if($data['accessuri'] && strpos($data['accessuri'], '/') === 0){
            $urlServerPath = FileSystemService::getServerPathFromUrlPath($data['accessuri']);
            FileSystemService::deleteFile($urlServerPath, true);
        }
        $sql = 'DELETE FROM media WHERE mediaid = ' . (int)$mediaid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getMediaArrByProperty($property, $value, $limitFormat = null): array
    {
        $returnArr = array();
        if($property === 'occid' || $property === 'tid'){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM media WHERE ' . $property . ' = ' . (int)$value . ' ';
            if($limitFormat){
                if($limitFormat === 'audio'){
                    $sql .= 'AND format LIKE "audio/%" ';
                }
                elseif($limitFormat === 'video'){
                    $sql .= 'AND format LIKE "video/%" ';
                }
            }
            $sql .= 'ORDER BY sortsequence ';
            //echo '<div>'.$sql.'</div>';
            if($rs = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($rs);
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        $nodeArr[$name] = $r->$name;
                    }
                    $returnArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $returnArr;
    }

    public function getMediaData($mediaid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM media WHERE mediaid = ' . (int)$mediaid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function updateTidFromOccurrenceRecord($occid, $tid): void
    {
        if((int)$occid > 0){
            $sql = 'UPDATE media SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
