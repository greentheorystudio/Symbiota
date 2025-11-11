<?php
include_once(__DIR__ . '/TaxonRanks.php');
include_once(__DIR__ . '/../services/DbService.php');

class TaxonKingdoms{

	private $conn;

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createTaxonKingdomRecord($name): int
    {
        $retVal = 0;
        $sql = 'INSERT INTO taxonkingdoms(`kingdom_name`) VALUES("' . SanitizerService::cleanInStr($this->conn, $name) . '")';
        if($this->conn->query($sql)){
            $retVal = $this->conn->insert_id;
            if((int)$retVal > 0){
                (new TaxonRanks)->setNewKingdomRanks($retVal, $name);
            }
        }
        return $retVal;
    }

    public function getKingdomArr(): array
    {
        $retArr = array();
        $sql = 'SELECT k.kingdom_id, t.tid, t.sciname '.
            'FROM taxonkingdoms AS k LEFT JOIN taxa AS t ON k.kingdom_name = t.SciName '.
            'WHERE t.tid IS NOT NULL '.
            'ORDER BY t.SciName ';
        //echo $sql;
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $tArr = array();
                $tArr['id'] = $row['kingdom_id'];
                $tArr['tid'] = $row['tid'];
                $tArr['name'] = $row['sciname'];
                $retArr[] = $tArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function updateKingdomAcceptance($tid, $tidNew): void
    {
        if(is_numeric($tid) && is_numeric($tidNew)){
            $oldKingdomId = 0;
            $newKingdomId = 0;
            $sql = 'SELECT k.kingdom_id FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.SciName = k.kingdom_name WHERE t.TID = ' . (int)$tid . ' ';
            $result = $this->conn->query($sql);
            if($row = $result->fetch_array(MYSQLI_ASSOC)){
                $oldKingdomId = $row['kingdom_id'];
            }
            $result->free();
            $sql = 'SELECT k.kingdom_id FROM taxa AS t LEFT JOIN taxonkingdoms AS k ON t.SciName = k.kingdom_name WHERE t.TID = ' . (int)$tidNew . ' ';
            $result = $this->conn->query($sql);
            if($row = $result->fetch_array(MYSQLI_ASSOC)){
                $newKingdomId = $row['kingdom_id'];
            }
            $result->free();
            if($oldKingdomId && $newKingdomId){
                $sql = 'UPDATE taxa SET kingdomId = '.$newKingdomId.' WHERE kingdomId = ' . (int)$oldKingdomId . ' ';
                $this->conn->query($sql);
                $sql = 'DELETE FROM taxonkingdoms WHERE kingdom_id = ' . (int)$oldKingdomId . ' ';
                $this->conn->query($sql);
                $sql = 'DELETE FROM taxonunits WHERE kingdomid = ' . (int)$oldKingdomId . ' ';
                $this->conn->query($sql);
            }
        }
    }
}
