<?php
include_once(__DIR__ . '/../services/DbService.php');

class ChecklistVouchers{

	private $conn;

    private $fields = array(
        "vid" => array("dataType" => "number", "length" => 10),
        "tid" => array("dataType" => "number", "length" => 10),
        "clid" => array("dataType" => "number", "length" => 10),
        "occid" => array("dataType" => "number", "length" => 10),
        "editornotes" => array("dataType" => "string", "length" => 50),
        "preferredimage" => array("dataType" => "number", "length" => 11),
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

    public function getChecklistListByOccurrenceVoucher($occid): array
    {
        $retArr = array();
        if($occid){
            $sql = 'SELECT c.clid, c.name '.
                'FROM fmchecklists AS c LEFT JOIN fmvouchers AS v ON c.clid = v.clid '.
                'WHERE v.occid = ' . (int)$occid . ' ORDER BY c.name ';
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

    public function updateTidFromOccurrenceRecord($occid, $tid): void
    {
        if((int)$occid > 0){
            $sql = 'UPDATE fmvouchers SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
