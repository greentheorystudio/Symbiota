<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class Images{

	private $conn;

    private $fields = array(
        "imgid" => array("dataType" => "number", "length" => 10),
        "tid" => array("dataType" => "number", "length" => 10),
        "url" => array("dataType" => "string", "length" => 255),
        "thumbnailurl" => array("dataType" => "string", "length" => 255),
        "originalurl" => array("dataType" => "string", "length" => 255),
        "archiveurl" => array("dataType" => "string", "length" => 255),
        "photographer" => array("dataType" => "string", "length" => 100),
        "photographeruid" => array("dataType" => "number", "length" => 10),
        "imagetype" => array("dataType" => "string", "length" => 50),
        "format" => array("dataType" => "string", "length" => 45),
        "caption" => array("dataType" => "string", "length" => 750),
        "owner" => array("dataType" => "string", "length" => 250),
        "sourceurl" => array("dataType" => "string", "length" => 255),
        "referenceurl" => array("dataType" => "string", "length" => 255),
        "copyright" => array("dataType" => "string", "length" => 255),
        "rights" => array("dataType" => "string", "length" => 255),
        "accessrights" => array("dataType" => "string", "length" => 255),
        "locality" => array("dataType" => "string", "length" => 250),
        "occid" => array("dataType" => "number", "length" => 10),
        "notes" => array("dataType" => "string", "length" => 350),
        "anatomy" => array("dataType" => "string", "length" => 100),
        "username" => array("dataType" => "string", "length" => 45),
        "sourceidentifier" => array("dataType" => "string", "length" => 150),
        "mediamd5" => array("dataType" => "string", "length" => 45),
        "dynamicproperties" => array("dataType" => "text", "length" => 0),
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

    public function getImageArrByProperty($property, $value, $includeOccurrence = false): array
    {
        $returnArr = array();
        if($property === 'occid' || $property === 'tid'){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM images WHERE ' . $property . ' = ' . (int)$value . ' ';
            if($property === 'tid' && !$includeOccurrence){
                $sql .= 'AND ISNULL(occid) ';
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
            $sql = 'UPDATE images SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
