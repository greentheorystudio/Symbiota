<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceGeneticLinks{

	private $conn;

    private $fields = array(
        "idoccurgenetic" => array("dataType" => "number", "length" => 11),
        "occid" => array("dataType" => "number", "length" => 10),
        "identifier" => array("dataType" => "string", "length" => 150),
        "resourcename" => array("dataType" => "string", "length" => 150),
        "title" => array("dataType" => "string", "length" => 150),
        "locus" => array("dataType" => "string", "length" => 500),
        "resourceurl" => array("dataType" => "string", "length" => 500),
        "notes" => array("dataType" => "string", "length" => 250),
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

    public function getOccurrenceGeneticLinkData($occid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurgenetic '.
            'WHERE occid = ' . (int)$occid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }
}
