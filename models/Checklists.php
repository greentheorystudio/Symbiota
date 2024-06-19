<?php
include_once(__DIR__ . '/../services/DbService.php');

class Checklists{

	private $conn;

    private $fields = array(
        "clid" => array("dataType" => "number", "length" => 10),
        "name" => array("dataType" => "string", "length" => 100),
        "title" => array("dataType" => "string", "length" => 150),
        "locality" => array("dataType" => "string", "length" => 500),
        "publication" => array("dataType" => "string", "length" => 500),
        "abstract" => array("dataType" => "text", "length" => 0),
        "authors" => array("dataType" => "string", "length" => 250),
        "type" => array("dataType" => "string", "length" => 50),
        "politicaldivision" => array("dataType" => "string", "length" => 45),
        "dynamicsql" => array("dataType" => "string", "length" => 500),
        "parent" => array("dataType" => "string", "length" => 50),
        "parentclid" => array("dataType" => "number", "length" => 10),
        "notes" => array("dataType" => "string", "length" => 500),
        "latcentroid" => array("dataType" => "number", "length" => 9),
        "longcentroid" => array("dataType" => "number", "length" => 9),
        "pointradiusmeters" => array("dataType" => "number", "length" => 10),
        "footprintwkt" => array("dataType" => "text", "length" => 0),
        "percenteffort" => array("dataType" => "number", "length" => 11),
        "access" => array("dataType" => "string", "length" => 45),
        "defaultsettings" => array("dataType" => "string", "length" => 250),
        "iconurl" => array("dataType" => "string", "length" => 150),
        "headerurl" => array("dataType" => "string", "length" => 150),
        "uid" => array("dataType" => "number", "length" => 10),
        "sortsequence" => array("dataType" => "number", "length" => 10),
        "expiration" => array("dataType" => "number", "length" => 10),
        "datelastmodified" => array("dataType" => "date", "length" => 0),
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

    public function getChecklistListByUserRights(): array
    {
        $retArr = array();
        $cArr = array();
        if(array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])) {
            $cArr = $GLOBALS['USER_RIGHTS']['ClAdmin'];
        }
        if($cArr){
            $sql = 'SELECT clid, name FROM fmchecklists WHERE clid IN('.implode(',', $cArr).') ORDER BY name ';
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    $nodeArr['clid'] = $r->clid;
                    $nodeArr['name'] = $r->name;
                    $retArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $retArr;
    }
}
