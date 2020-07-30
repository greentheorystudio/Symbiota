<?php
include_once('DbConnection.php');
include_once('TaxonomyUtilities.php');

class TaxonomyDynamicListManager{

	private $conn;
    private $tid = 0;
    private $descLimit = false;
    private $sortField = '';
    private $index = 0;
    private $taxaCnt = 0;
    private $tidArr = array();
    private $sciname = '';

	public function __construct(){
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
		if(!($this->conn === null)) {
			$this->conn->close();
		}
	}

    public function getHigherRankArr(): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.RankId, t.SciName, v.VernacularName '.
            'FROM taxa AS t LEFT JOIN taxavernaculars AS v ON t.TID = v.TID '.
            'WHERE t.RankId IN(10,30,60) '.
            'ORDER BY t.RankId, t.SciName ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $display = '';
            $retArr[(int)$r->RankId][$r->TID]['sciname'] = $r->SciName;
            if($r->VernacularName){
                $retArr[(int)$r->RankId][$r->TID]['vernacularArr'][] = $r->VernacularName;
                $vernacularStr = implode(',', $retArr[$r->RankId][$r->TID]['vernacularArr']);
                $display = $r->SciName . ' - ' . $vernacularStr;
            }
            $retArr[(int)$r->RankId][$r->TID]['display'] = ($display?:$r->SciName);
        }
        $rs->free();
        return $retArr;
    }

    public function setTidFromSciname($sciname): int
    {
        $tid = 0;
        $sql = 'SELECT TID FROM taxa WHERE SciName = "'.$sciname.'" ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $tid = $r->TID;
        }
        $rs->free();
        if($tid){
            $this->tid = $tid;
            $this->sciname = $sciname;
        }
        return $tid;
    }

    public function setTaxaCnt(): void
    {
        $taxCnt = 0;
        $sql = 'SELECT COUNT(CONCAT(t1.TID,t2.TID,t3.TID,t4.TID,t5.TID,t6.TID)) AS cnt '.
            'FROM taxaenumtree AS te1 LEFT JOIN taxa AS t1 ON te1.tid = t1.TID '.
            'LEFT JOIN taxstatus AS ts ON t1.TID = ts.tid '.
            'LEFT JOIN taxaenumtree AS te2 ON t1.TID = te2.tid '.
            'LEFT JOIN taxa AS t2 ON te2.parenttid = t2.TID '.
            'LEFT JOIN taxaenumtree AS te3 ON t1.TID = te3.tid '.
            'LEFT JOIN taxa AS t3 ON te3.parenttid = t3.TID '.
            'LEFT JOIN taxaenumtree AS te4 ON t1.TID = te4.tid '.
            'LEFT JOIN taxa AS t4 ON te4.parenttid = t4.TID '.
            'LEFT JOIN taxaenumtree AS te5 ON t1.TID = te5.tid '.
            'LEFT JOIN taxa AS t5 ON te5.parenttid = t5.TID '.
            'LEFT JOIN taxaenumtree AS te6 ON t1.TID = te6.tid '.
            'LEFT JOIN taxa AS t6 ON te6.parenttid = t6.TID '.
            'WHERE ((te1.parenttid = '.$this->tid.') AND t1.RankId >= 180 AND ts.tid = ts.tidaccepted) '.
            'AND (t2.RankId = 10 OR ISNULL(t2.RankId)) '.
            'AND (t3.RankId = 30 OR ISNULL(t3.RankId)) '.
            'AND (t4.RankId = 60 OR ISNULL(t4.RankId)) '.
            'AND (t5.RankId = 100 OR ISNULL(t5.RankId)) '.
            'AND (t6.RankId = 140 OR ISNULL(t6.RankId)) ';
        if($this->descLimit){
            $sql .= 'AND t1.TID IN(SELECT tid FROM taxadescrblock) ';
        }
        //echo "<div>Count sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $taxCnt += $row->cnt;
        }
        $sql = 'SELECT COUNT(t1.TID) AS cnt '.
            'FROM taxa AS t1 LEFT JOIN taxstatus AS ts ON t1.TID = ts.tid '.
            'WHERE ((t1.TID = '.$this->tid.') AND t1.RankId >= 180 AND ts.tid = ts.tidaccepted) ';
        if($this->descLimit){
            $sql .= 'AND t1.TID IN(SELECT tid FROM taxadescrblock) ';
        }
        //echo "<div>Count sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $taxCnt += $row->cnt;
        }
        $result->free();
        $this->taxaCnt = $taxCnt;
    }

    public function getTableArr(): array
    {
        $returnArr = array();
        $sql = 'SELECT DISTINCT t1.TID, t1.SciName, t2.TID AS kingdomTid, t2.SciName AS kingdomName, t3.TID AS phylumTid, '.
            't3.SciName AS phylumName, t4.TID AS classTid, t4.SciName AS className, t5.TID AS orderTid, t5.SciName AS orderName, '.
            't6.TID AS familyTid, t6.SciName AS familyName '.
            'FROM taxaenumtree AS te1 LEFT JOIN taxa AS t1 ON te1.tid = t1.TID '.
            'LEFT JOIN taxstatus AS ts ON t1.TID = ts.tid '.
            'LEFT JOIN taxaenumtree AS te2 ON t1.TID = te2.tid '.
            'LEFT JOIN taxa AS t2 ON te2.parenttid = t2.TID '.
            'LEFT JOIN taxaenumtree AS te3 ON t1.TID = te3.tid '.
            'LEFT JOIN taxa AS t3 ON te3.parenttid = t3.TID '.
            'LEFT JOIN taxaenumtree AS te4 ON t1.TID = te4.tid '.
            'LEFT JOIN taxa AS t4 ON te4.parenttid = t4.TID '.
            'LEFT JOIN taxaenumtree AS te5 ON t1.TID = te5.tid '.
            'LEFT JOIN taxa AS t5 ON te5.parenttid = t5.TID '.
            'LEFT JOIN taxaenumtree AS te6 ON t1.TID = te6.tid '.
            'LEFT JOIN taxa AS t6 ON te6.parenttid = t6.TID '.
            'WHERE ((te1.parenttid = '.$this->tid.' OR t1.TID = '.$this->tid.') AND t1.RankId >= 180 AND ts.tid = ts.tidaccepted) '.
            'AND (t2.RankId = 10 OR ISNULL(t2.RankId)) '.
            'AND (t3.RankId = 30 OR ISNULL(t3.RankId)) '.
            'AND (t4.RankId = 60 OR ISNULL(t4.RankId)) '.
            'AND (t5.RankId = 100 OR ISNULL(t5.RankId)) '.
            'AND (t6.RankId = 140 OR ISNULL(t6.RankId)) ';
        if($this->descLimit){
            $sql .= 'AND t1.TID IN(SELECT tid FROM taxadescrblock) ';
        }
        if($this->sortField === 'kingdom'){
            $sql .= 'ORDER BY kingdomName, phylumName, className, orderName, familyName, SciName ';
        }
        elseif($this->sortField === 'phylum'){
            $sql .= 'ORDER BY phylumName, className, orderName, familyName, SciName ';
        }
        elseif($this->sortField === 'class'){
            $sql .= 'ORDER BY className, orderName, familyName, SciName ';
        }
        elseif($this->sortField === 'order'){
            $sql .= 'ORDER BY orderName, familyName, SciName ';
        }
        elseif($this->sortField === 'family'){
            $sql .= 'ORDER BY familyName, SciName ';
        }
        elseif($this->sortField === 'sciname'){
            $sql .= 'ORDER BY SciName ';
        }
        $sql .= 'LIMIT '.($this->index > 0?$this->index * 100:0).',100';
        //echo "<div>Table sql: ".$sql."</div>";
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $tid = $r->TID;
            $indexId = $r->kingdomTid . $r->phylumTid . $r->classTid . $r->orderTid . $r->familyTid . $r->TID;
            $this->tidArr[] = $tid;
            $returnArr[$indexId]['tid'] = $tid;
            $returnArr[$indexId]['SciName'] = $r->SciName;
            $returnArr[$indexId]['kingdomTid'] = $r->kingdomTid;
            $returnArr[$indexId]['kingdomName'] = $r->kingdomName;
            $returnArr[$indexId]['phylumTid'] = $r->phylumTid;
            $returnArr[$indexId]['phylumName'] = $r->phylumName;
            $returnArr[$indexId]['classTid'] = $r->classTid;
            $returnArr[$indexId]['className'] = $r->className;
            $returnArr[$indexId]['orderTid'] = $r->orderTid;
            $returnArr[$indexId]['orderName'] = $r->orderName;
            $returnArr[$indexId]['familyTid'] = $r->familyTid;
            $returnArr[$indexId]['familyName'] = $r->familyName;
        }
        $rs->free();
        return $returnArr;
    }

    public function getVernacularArr(): array
    {
        $returnArr = array();
        $sql = 'SELECT TID, VernacularName '.
            'FROM taxavernaculars '.
            'WHERE TID IN('.implode(',', $this->tidArr).') '.
            'ORDER BY TID, VernacularName ';
        //echo "<div>Vernacular sql: ".$sql."</div>";
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $returnArr[$r->TID][] = $r->VernacularName;
        }
        $rs->free();
        return $returnArr;
    }

    public function setSciName(): void
    {
        $sql = 'SELECT SciName FROM taxa WHERE TID = '.$this->tid.' ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $this->sciname = $r->SciName;
        }
        $rs->free();
    }

    public function setTid($id): void
	{
		if(is_numeric($id)) {
			$this->tid = $id;
			$this->setSciName();
		}
	}

    public function setDescLimit($value): void
    {
        $this->descLimit = $value;
    }

    public function setSortField($value): void
    {
        $this->sortField = $value;
    }

    public function setPageIndex($value): void
    {
        $this->index = $value;
    }

    public function getSciName(): string
    {
        return $this->sciname;
    }

    public function getTaxaCnt(): string
    {
        return $this->taxaCnt;
    }
}
