<?php
include_once(__DIR__ . '/../services/DbService.php');

class TaxonKingdoms{

	private $conn;

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
		if($this->conn) {
            $this->conn->close();
        }
	}

    public function createTaxonKingdomRecord($name): int
    {
        $retVal = 0;
        $sql = 'INSERT INTO taxonkingdoms(`kingdom_name`) VALUES("'.SanitizerService::cleanInStr($this->conn,$name).'")';
        if($this->conn->query($sql)){
            $retVal = $this->conn->insert_id;
            $sql = 'INSERT INTO taxonunits(kingdomid,rankid,rankname,dirparentrankid,reqparentrankid) '.
                'SELECT '.$retVal.',rankid,rankname,dirparentrankid,reqparentrankid '.
                'FROM taxonunits WHERE kingdomid = 100 ';
            $this->conn->query($sql);
        }
        return $retVal;
    }

    public function getKingdomArr(): array
    {
        $retArr = array();
        $sql = 'SELECT k.kingdom_id, t.sciname '.
            'FROM taxonkingdoms AS k LEFT JOIN taxa AS t ON k.kingdom_name = t.SciName '.
            'WHERE t.tid IS NOT NULL '.
            'ORDER BY t.SciName ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $tArr = array();
            $tArr['id'] = $r->kingdom_id;
            $tArr['name'] = $r->sciname;
            $retArr[] = $tArr;
        }
        $rs->free();
        return $retArr;
    }

    public function updateKingdomAcceptance($tid, $tidNew): void
    {
        if(is_numeric($tid) && is_numeric($tidNew)){
            $oldKingdomId = 0;
            $newKingdomId = 0;
            $sql = 'SELECT k.kingdom_id FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.SciName = k.kingdom_name WHERE t.TID = '.$tid.' ';
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $oldKingdomId = $r->kingdom_id;
            }
            $sql = 'SELECT k.kingdom_id FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.SciName = k.kingdom_name WHERE t.TID = '.$tidNew.' ';
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $newKingdomId = $r->kingdom_id;
            }
            if($oldKingdomId && $newKingdomId){
                $sql = 'UPDATE taxa SET kingdomId = '.$newKingdomId.' WHERE kingdomId = '.$oldKingdomId.' ';
                $this->conn->query($sql);
                $sql = 'DELETE FROM taxonkingdoms WHERE kingdom_id = '.$oldKingdomId.' ';
                $this->conn->query($sql);
                $sql = 'DELETE FROM taxonunits WHERE kingdomid = '.$oldKingdomId.' ';
                $this->conn->query($sql);
            }
        }
    }
}
