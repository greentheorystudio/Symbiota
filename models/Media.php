<?php
include_once(__DIR__ . '/../services/DbService.php');

class Media{

	private $conn;

    private $fields = array(
        "mediaid" => array("dataType" => "number", "length" => 10),
        "tid" => array("dataType" => "number", "length" => 10),
        "occid" => array("dataType" => "number", "length" => 10),
        "accessuri" => array("dataType" => "string", "length" => 2048),
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

    public function updateTidFromOccurrenceRecord($occid, $tid): void
    {
        if((int)$occid > 0){
            $sql = 'UPDATE media SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
