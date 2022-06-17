<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');

class TaxonomyDynamicListManager{

    private $conn;
    private $tid = 0;
    private $descLimit = 0;
    private $sortField = '';
    private $index = 0;
    private $collid = 0;
    private $taxaCnt = 0;
    private $tidArr = array();
    private $targetTidArr = array();
    private $sciname = '';

    public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
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
            $this->sciname = $sciname;
        }
        return $tid;
    }

    public function setTid($tid): void
    {
        if(is_numeric($tid)) {
            $this->tid = $tid;
            $this->targetTidArr[] = $tid;
            if($this->collid){
                $tidSql = 'SELECT DISTINCT tidinterpreted AS tid FROM omoccurrences WHERE collid = '.$this->collid.' ';
            }
            else{
                $tidSql = 'SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = '.$this->tid.' ';
            }
            //echo "<div>TID sql: ".$tidSql."</div>";
            $rs = $this->conn->query($tidSql);
            while($r = $rs->fetch_object()){
                if($r->tid){
                    $this->targetTidArr[] = $r->tid;
                }
            }
            $rs->free();
            if(!$this->sciname){
                $this->setSciName();
            }
            $this->setTaxaCnt();
        }
    }

    public function setTaxaCnt(): void
    {
        $taxCnt = 0;
        $sql = 'SELECT COUNT(DISTINCT t2.SciName) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxa AS t2 ON te.tid = t2.TID '.
            'LEFT JOIN taxstatus AS ts ON t.TID = ts.tid '.
            'WHERE (te.tid IN('.implode(',',$this->targetTidArr).') AND t.RankId >= 180 AND ts.tid = ts.tidaccepted) '.
            'AND (t.SciName LIKE "% %" OR t.TID NOT IN(SELECT parenttid FROM taxstatus)) ';
        if($this->descLimit > 0){
            $sql .= 'AND t.TID IN(SELECT tid FROM taxadescrblock) ';
        }
        //echo '<div>Count sql: ' .$sql. '</div>';
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $this->taxaCnt = $row->cnt;
        }
        $result->free();
    }

    public function getTableArr(): array
    {
        $parentTaxonArr = array();
        $returnArr = array();
        $parentTaxonSql = 'SELECT DISTINCT te.tid, t.TID AS parentTid, t.RankId, t.SciName '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
            'LEFT JOIN taxstatus AS ts ON te.tid = ts.tid '.
            'WHERE (te.tid IN('.implode(',',$this->targetTidArr).') AND ts.tid = ts.tidaccepted AND t.RankId IN(10,30,60,100,140)) ';
        //echo '<div>Parent sql: ' .$parentTaxonSql. '</div>';
        $rs = $this->conn->query($parentTaxonSql);
        while($r = $rs->fetch_object()){
            $parentTaxonArr[$r->tid][(int)$r->RankId]['id'] = $r->parentTid;
            $parentTaxonArr[$r->tid][(int)$r->RankId]['sciname'] = $r->SciName;
        }
        $rs->free();

        $sql = 'SELECT DISTINCT t.TID, t.SciName '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxstatus AS ts ON t.TID = ts.tid '.
            'WHERE (te.tid IN('.implode(',',$this->targetTidArr).') AND t.RankId >= 180 AND ts.tid = ts.tidaccepted) '.
            'AND (t.SciName LIKE "% %" OR t.TID NOT IN(SELECT parenttid FROM taxstatus)) ';
        if($this->descLimit){
            $sql .= 'AND t.TID IN(SELECT tid FROM taxadescrblock) ';
        }
        //echo '<div>Table sql: ' .$sql. '</div>';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $tid = $r->TID;
            if($tid){
                $recordArr = array();
                $parentArr = (array_key_exists($tid,$parentTaxonArr)?$parentTaxonArr[$tid]:array());
                $this->tidArr[] = $tid;
                $recordArr['tid'] = $tid;
                $recordArr['SciName'] = $r->SciName;
                $recordArr['kingdomTid'] = (array_key_exists(10,$parentArr)?$parentArr[10]['id']:0);
                $recordArr['kingdomName'] = (array_key_exists(10,$parentArr)?$parentArr[10]['sciname']:'');
                $recordArr['phylumTid'] = (array_key_exists(30,$parentArr)?$parentArr[30]['id']:0);
                $recordArr['phylumName'] = (array_key_exists(30,$parentArr)?$parentArr[30]['sciname']:'');
                $recordArr['classTid'] = (array_key_exists(60,$parentArr)?$parentArr[60]['id']:0);
                $recordArr['className'] = (array_key_exists(60,$parentArr)?$parentArr[60]['sciname']:'');
                $recordArr['orderTid'] = (array_key_exists(100,$parentArr)?$parentArr[100]['id']:0);
                $recordArr['orderName'] = (array_key_exists(100,$parentArr)?$parentArr[100]['sciname']:'');
                $recordArr['familyTid'] = (array_key_exists(140,$parentArr)?$parentArr[140]['id']:0);
                $recordArr['familyName'] = (array_key_exists(140,$parentArr)?$parentArr[140]['sciname']:'');
                $returnArr[] = $recordArr;
            }
        }
        $rs->free();

        if($this->sortField === 'kingdom'){
            $kingdomName  = array_column($returnArr, 'kingdomName');
            $phylumName = array_column($returnArr, 'phylumName');
            $className = array_column($returnArr, 'className');
            $orderName = array_column($returnArr, 'orderName');
            $familyName = array_column($returnArr, 'familyName');
            $SciName = array_column($returnArr, 'SciName');
            array_multisort($kingdomName, SORT_ASC, $phylumName, SORT_ASC, $className, SORT_ASC, $orderName, SORT_ASC, $familyName, SORT_ASC, $SciName, SORT_ASC, $returnArr);
        }
        elseif($this->sortField === 'phylum'){
            $phylumName = array_column($returnArr, 'phylumName');
            $className = array_column($returnArr, 'className');
            $orderName = array_column($returnArr, 'orderName');
            $familyName = array_column($returnArr, 'familyName');
            $SciName = array_column($returnArr, 'SciName');
            array_multisort($phylumName, SORT_ASC, $className, SORT_ASC, $orderName, SORT_ASC, $familyName, SORT_ASC, $SciName, SORT_ASC, $returnArr);
        }
        elseif($this->sortField === 'class'){
            $className = array_column($returnArr, 'className');
            $orderName = array_column($returnArr, 'orderName');
            $familyName = array_column($returnArr, 'familyName');
            $SciName = array_column($returnArr, 'SciName');
            array_multisort($className, SORT_ASC, $orderName, SORT_ASC, $familyName, SORT_ASC, $SciName, SORT_ASC, $returnArr);
        }
        elseif($this->sortField === 'order'){
            $orderName = array_column($returnArr, 'orderName');
            $familyName = array_column($returnArr, 'familyName');
            $SciName = array_column($returnArr, 'SciName');
            array_multisort($orderName, SORT_ASC, $familyName, SORT_ASC, $SciName, SORT_ASC, $returnArr);
        }
        elseif($this->sortField === 'family'){
            $familyName = array_column($returnArr, 'familyName');
            $SciName = array_column($returnArr, 'SciName');
            array_multisort($familyName, SORT_ASC, $SciName, SORT_ASC, $returnArr);
        }
        elseif($this->sortField === 'sciname'){
            $SciName = array_column($returnArr, 'SciName');
            array_multisort($SciName, SORT_ASC, $returnArr);
        }

        if($this->index){
            return array_slice($returnArr, ($this->index > 0?$this->index * 100:0), 100);
        }

        return $returnArr;
    }

    public function getVernacularArr(): array
    {
        $returnArr = array();
        if($this->tidArr){
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
        }
        return $returnArr;
    }

    public function getSpAmtByParent($tid): int
    {
        $cnt = 0;
        $sql = 'SELECT COUNT(DISTINCT t.TID) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'WHERE t.RankId >= 220 AND te.parenttid = '.$tid.' ';
        //echo "<div>Sql: ".$sql."</div>";
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $cnt = $r->cnt;
        }
        $rs->free();
        return $cnt;
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

    public function setCollId($value): void
    {
        $this->collid = $value;
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
