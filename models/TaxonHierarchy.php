<?php
include_once(__DIR__ . '/../services/DbService.php');

class TaxonHierarchy{

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

    public function deleteTidFromHierarchyTable($tid): bool
    {
        $status = false;
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $sql = 'DELETE FROM taxaenumtree '.
                    'WHERE tid IN(' . $tidStr . ') OR parenttid IN(' . $tidStr . ') ';
                //echo $sql;
                if($this->conn->query($sql)){
                    $status = true;
                }
            }
        }
        return $status;
    }

    public function getTaxonomicTreeTaxonPath($tId): array
    {
        $retArr = array();
        $sql = 'SELECT t2.TID, t2.SciName '.
            'FROM taxa AS t LEFT JOIN taxaenumtree AS te ON t.tidaccepted = te.tid  '.
            'LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID  '.
            'WHERE t.TID = '.$tId.' AND t2.RankId >= 10 '.
            'ORDER BY t2.RankId ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                $nodeArr['tid'] = $r->TID;
                $nodeArr['sciname'] = $r->SciName;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }

        $sql = 'SELECT t.TID, t2.TID AS accTID, t2.SciName AS accSciName '.
            'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID  '.
            'WHERE t.TID = '.$tId.' ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                if($r->TID !== $r->accTID){
                    $nodeArr = array();
                    $nodeArr['tid'] = $r->accTID;
                    $nodeArr['sciname'] = $r->accSciName;
                    $retArr[] = $nodeArr;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function populateHierarchyTable(): int
    {
        $retCnt = 0;
        $sql = 'INSERT IGNORE INTO taxaenumtree(tid,parenttid) '.
            'SELECT DISTINCT e.tid, t.parenttid '.
            'FROM taxaenumtree AS e LEFT JOIN taxa AS t ON e.parenttid = t.tid ';
        //echo $sql . '<br />';
        if($this->conn->query($sql)){
            $retCnt = $this->conn->affected_rows;
        }
        return $retCnt;
    }

    public function primeHierarchyTable($tid = null): int
    {
        $retCnt = 0;
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
        }
        $sql = 'INSERT IGNORE INTO taxaenumtree(tid,parenttid) '.
            'SELECT DISTINCT tid, parenttid FROM taxa '.
            'WHERE '.($tidStr ? 'tid IN('.$tidStr.') AND ' : '').'tid NOT IN(SELECT tid FROM taxaenumtree) AND parenttid IS NOT NULL ';
        //echo $sql . '<br />';;
        if($this->conn->query($sql)){
            $retCnt = $this->conn->affected_rows;
        }
        return $retCnt;
    }

    public function removeTaxonFromTaxonomicHierarchy($tid, $parenttid): int
    {
        $retVal = 1;
        if($tid){
            $sql = 'DELETE FROM taxaenumtree WHERE parenttid = '.$tid.' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }

            $sql = 'UPDATE taxa SET parenttid = '.$parenttid.' WHERE parenttid = '.$tid.' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function setParentSearchDataByTidArr($searchData, $tidArr): array
    {
        $sql = 'SELECT DISTINCT t.tid, t.sciname ' .
            'FROM taxa AS t LEFT JOIN taxaenumtree AS te ON t.tid = te.tid ' .
            'WHERE te.parenttid IN(' . implode('', $tidArr) . ') AND t.tidaccepted = t.tid ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()) {
            $searchData[$r->sciname] = $r->tid;
        }
        $rs->free();
        return $searchData;
    }

    public function updateHierarchyTable($tid = null): void
    {
        if(is_array($tid) || is_numeric($tid)){
            $this->deleteTidFromHierarchyTable($tid);
            $this->primeHierarchyTable($tid);
            do {
                $hierarchyAdded = $this->populateHierarchyTable();
            } while($hierarchyAdded > 0);
        }
    }
}
