<?php
include_once('DbConnection.php');

class IRLManager {

	private $conn;
    private $tidArr = array();

 	public function __construct(){
		$connection = new DbConnection();
 		$this->conn = $connection->getConnection();
 	}

 	public function __destruct(){
		if(!($this->conn === null)) {
			$this->conn->close();
		}
	}

    public function getNativeStatus($tid): array
    {
        $returnArr = array();
        if($tid){
            $sql = 'SELECT TID, CLID ' .
                'FROM fmchklsttaxalink  ' .
                'WHERE TID = ' .$tid. ' AND CLID IN(13,14) ';
            //echo $sql;
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                if((int)$row->CLID === 13) {
                    $returnArr[] = 'NON-NATIVE';
                }
                if((int)$row->CLID === 14) {
                    $returnArr[] = 'CRYPTOGENIC';
                }
            }
            $result->free();
        }
        return $returnArr;
    }

    public function getChecklistTaxa($clid): array
    {
        $returnArr = array();
        $sql = 'SELECT c.TID, t.SciName, c.Habitat, c.Notes ' .
            'FROM fmchklsttaxalink AS c LEFT JOIN taxa AS t ON c.TID = t.TID  ' .
            'WHERE c.CLID = ' .$clid. ' '.
            'ORDER BY t.SciName ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $sciname = $row->SciName;
            if(strpos($sciname, ' ') === false){
                $sciname .= ' spp.';
            }
            $returnArr[$row->TID]['sciname'] = $sciname;
            $returnArr[$row->TID]['habitat'] = $row->Habitat;
            $returnArr[$row->TID]['notes'] = $row->Notes;
            $this->tidArr[] = $row->TID;
        }
        $result->free();
        return $returnArr;
    }

    public function getChecklistVernaculars(): array
    {
        $returnArr = array();
        $sql = 'SELECT TID, VernacularName ' .
            'FROM taxavernaculars  ' .
            'WHERE TID IN(' .implode(',', $this->tidArr). ') '.
            'ORDER BY TID, VernacularName ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArr[$row->TID][] = $row->VernacularName;
        }
        $result->free();
        return $returnArr;
    }

    public function getTotalTaxa(): int
    {
        $total = 0;
        $sql = 'SELECT COUNT(DISTINCT TID) AS cnt FROM taxa WHERE (RankId > 180) OR (RankId = 180 AND TID NOT IN(SELECT parenttid FROM taxaenumtree)) ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $total = (int)$row->cnt;
        }
        $result->free();
        return $total;
    }

    public function getTotalTaxaWithDesc(): int
    {
        $total = 0;
        $sql = 'SELECT COUNT(TID) AS cnt FROM taxa WHERE TID IN(SELECT tid FROM taxadescrblock) ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $total = (int)$row->cnt;
        }
        $result->free();
        return $total;
    }

    public function getTotalOccurrenceRecords(): int
    {
        $total = 0;
        $sql = 'SELECT COUNT(occid) AS cnt FROM omoccurrences ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $total = (int)$row->cnt;
        }
        $result->free();
        return $total;
    }
}
