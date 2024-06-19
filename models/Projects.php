<?php
include_once(__DIR__ . '/../services/DbService.php');

class Projects{

	private $conn;

    private $fields = array(
        "pid" => array("dataType" => "number", "length" => 10),
        "projname" => array("dataType" => "string", "length" => 45),
        "displayname" => array("dataType" => "string", "length" => 150),
        "managers" => array("dataType" => "string", "length" => 150),
        "briefdescription" => array("dataType" => "string", "length" => 300),
        "fulldescription" => array("dataType" => "string", "length" => 5000),
        "notes" => array("dataType" => "string", "length" => 250),
        "iconurl" => array("dataType" => "string", "length" => 150),
        "headerurl" => array("dataType" => "string", "length" => 150),
        "occurrencesearch" => array("dataType" => "number", "length" => 10),
        "ispublic" => array("dataType" => "number", "length" => 10),
        "dynamicproperties" => array("dataType" => "text", "length" => 0),
        "parentpid" => array("dataType" => "number", "length" => 10),
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

    public function getProjectListByUserRights(): array
    {
        $retArr = array();
        $cArr = array();
        if(array_key_exists('ProjAdmin',$GLOBALS['USER_RIGHTS'])) {
            $cArr = $GLOBALS['USER_RIGHTS']['ProjAdmin'];
        }
        if($cArr){
            $sql = 'SELECT pid, projname FROM fmprojects WHERE pid IN('.implode(',', $cArr).') ORDER BY projname ';
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    $nodeArr['pid'] = $r->pid;
                    $nodeArr['projname'] = $r->projname;
                    $retArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $retArr;
    }
}
