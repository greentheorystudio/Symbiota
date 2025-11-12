<?php
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');

class TaxonHierarchy{

	private $conn;

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function deleteTidFromHierarchyTable($tid): int
    {
        $returnVal = 0;
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
                    $returnVal = 1;
                }
            }
        }
        return $returnVal;
    }

    public function getChildTidArr($tid): array
    {
        $returnArr = array();
        $sql = 'SELECT tid FROM taxaenumtree WHERE parenttid = ' . (int)$tid . ' ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $returnArr[] = $row['parenttid'];
                unset($rows[$index]);
            }
        }
        return $returnArr;
    }

    public function getParentTidArr($tid): array
    {
        $returnArr = array();
        $sql = 'SELECT parenttid FROM taxaenumtree WHERE tid = ' . (int)$tid . ' ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $returnArr[] = $row['parenttid'];
                unset($rows[$index]);
            }
        }
        return $returnArr;
    }

    public function getSubtaxaTidArrFromTid($tid): array
    {
        $retArr = array();
        $sql = 'SELECT t.tid FROM taxa AS t LEFT JOIN taxaenumtree AS te ON t.tid = te.tid '.
            'WHERE te.parenttid = ' . (int)$tid . ' AND t.tid = t.tidaccepted ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[] = $row['tid'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxonomicTreeChildNodes($tId, $limitToAccepted, $includeImage): array
    {
        $retArr = array();
        $tidArr = array();
        $tempArr = array();
        if(!$limitToAccepted){
            $sql = 'SELECT t.TID, t.SciName, t.Author, tu.rankname '.
                'FROM taxa AS t LEFT JOIN taxonunits AS tu ON t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid  '.
                'WHERE t.tidaccepted = ' . (int)$tId . ' AND TID <> tidaccepted '.
                'ORDER BY tu.rankid, t.SciName ';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    $nodeArr['tid'] = $row['TID'];
                    $nodeArr['sciname'] = $row['SciName'];
                    $nodeArr['author'] = $row['Author'];
                    $nodeArr['rankname'] = $row['rankname'];
                    $nodeArr['nodetype'] = 'synonym';
                    $nodeArr['expandable'] = false;
                    $nodeArr['lazy'] = false;
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }

        $sql = 'SELECT t.TID, t.SciName, t.Author, tu.rankname '.
            'FROM taxa AS t LEFT JOIN taxonunits AS tu ON t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid  '.
            'WHERE t.parenttid = ' . $tId . ' AND TID = tidaccepted '.
            'ORDER BY tu.rankid, t.SciName ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $expandable = (new Taxa)->taxonHasChildren($row['TID']);
                $nodeArr = array();
                $nodeArr['tid'] = $row['TID'];
                $nodeArr['sciname'] = $row['SciName'];
                $nodeArr['author'] = $row['Author'];
                $nodeArr['rankname'] = $row['rankname'];
                $nodeArr['nodetype'] = 'child';
                $nodeArr['expandable'] = $expandable;
                $nodeArr['lazy'] = $expandable;
                if($includeImage){
                    $nodeArr['image'] = null;
                    $tidArr[] = $row['TID'];
                    $tempArr[] = $nodeArr;
                }
                else{
                    $retArr[] = $nodeArr;
                }
                unset($rows[$index]);
            }
            if($includeImage){
                $imageArr = (new Images)->getTaxonArrDisplayImageData($tidArr, false, true, false, 1, 50);
                foreach($tempArr as $data){
                    if(array_key_exists($data['tid'], $imageArr)){
                        $data['image'] = $imageArr[$data['tid']][0]['url'];
                    }
                    $retArr[] = $data;
                }
            }
        }
        return $retArr;
    }

    public function getTaxonomicTreeKingdomNodes(): array
    {
        $retArr = array();
        $sql = 'SELECT TID, SciName, Author FROM taxa '.
            'WHERE RankId = 10 AND TID = tidaccepted '.
            'ORDER BY SciName ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $expandable = (new Taxa)->taxonHasChildren($row['TID']);
                $nodeArr = array();
                $nodeArr['tid'] = $row['TID'];
                $nodeArr['sciname'] = $row['SciName'];
                $nodeArr['author'] = $row['Author'];
                $nodeArr['rankname'] = 'Kingdom';
                $nodeArr['nodetype'] = 'child';
                $nodeArr['expandable'] = $expandable;
                $nodeArr['lazy'] = $expandable;
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxonomicTreeTaxonPath($tId): array
    {
        $retArr = array();
        $sql = 'SELECT t2.TID, t2.SciName '.
            'FROM taxa AS t LEFT JOIN taxaenumtree AS te ON t.tidaccepted = te.tid  '.
            'LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID  '.
            'WHERE t.TID = ' . (int)$tId . ' AND t2.RankId >= 10 '.
            'ORDER BY t2.RankId ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                $nodeArr['tid'] = $row['TID'];
                $nodeArr['sciname'] = $row['SciName'];
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT t.TID, t2.TID AS accTID, t2.SciName AS accSciName '.
            'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID  '.
            'WHERE t.TID = ' . (int)$tId . ' ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if($row['TID'] !== $row['accTID']){
                    $nodeArr = array();
                    $nodeArr['tid'] = $row['accTID'];
                    $nodeArr['sciname'] = $row['accSciName'];
                    $retArr[] = $nodeArr;
                }
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getUpperTaxonomyData(): array
    {
        $returnArr = array();
        $sql = 'SELECT t.sciname AS family, t2.sciname AS taxonorder '.
            'FROM taxa AS t LEFT JOIN taxaenumtree AS e ON t.tid = e.tid LEFT JOIN taxa AS t2 ON e.parenttid = t2.tid '.
            'WHERE t.rankid = 140 AND t2.rankid = 100';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $returnArr[strtolower($row['family'])]['o'] = $row['taxonorder'];
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT t.sciname AS orderName, t2.sciname AS taxonclass '.
            'FROM taxa AS t LEFT JOIN taxaenumtree AS e ON t.tid = e.tid LEFT JOIN taxa AS t2 ON e.parenttid = t2.tid '.
            'WHERE t.rankid = 100 AND t2.rankid = 60';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $returnArr[strtolower($row['orderName'])]['c'] = $row['taxonclass'];
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT t.sciname AS className, t2.sciname AS taxonphylum '.
            'FROM taxa AS t LEFT JOIN taxaenumtree AS e ON t.tid = e.tid LEFT JOIN taxa AS t2 ON e.parenttid = t2.tid '.
            'WHERE t.rankid = 60 AND t2.rankid = 30';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $returnArr[strtolower($row['className'])]['p'] = $row['taxonphylum'];
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT t.sciname AS phylum, t2.sciname AS kingdom '.
            'FROM taxa AS t LEFT JOIN taxaenumtree AS e ON t.tid = e.tid LEFT JOIN taxa AS t2 ON e.parenttid = t2.tid '.
            'WHERE t.rankid = 30 AND t2.rankid = 10';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $returnArr[strtolower($row['phylum'])]['k'] = $row['kingdom'];
                unset($rows[$index]);
            }
        }
        return $returnArr;
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

    public function populateTaxonHierarchyData($tid): int
    {
        $returnVal = 0;
        if($this->primeHierarchyTable($tid)){
            do {
                $hierarchyAdded = $this->populateHierarchyTable();
                if($hierarchyAdded > 0 && !$returnVal){
                    $returnVal = 1;
                }
            } while($hierarchyAdded > 0);
        }
        return $returnVal;
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
            'WHERE ' . ($tidStr ? 'tid IN(' . $tidStr . ') AND ' : '') . 'tid NOT IN(SELECT tid FROM taxaenumtree) AND parenttid IS NOT NULL ';
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
            $sql = 'DELETE FROM taxaenumtree WHERE parenttid = ' . (int)$tid . ' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }

            $sql = 'UPDATE taxa SET parenttid = ' . (int)$parenttid . ' WHERE parenttid = ' . (int)$tid . ' ';
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
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $searchData[$row['sciname']] = $row['tid'];
                unset($rows[$index]);
            }
        }
        return $searchData;
    }

    public function updateHierarchyTable($tid): int
    {
        $returnVal = 1;
        if(is_array($tid) || is_numeric($tid)){
            $tidArr = $this->getChildTidArr($tid);
            $tidArr[] = $tid;
            $this->deleteTidFromHierarchyTable($tidArr);
            $this->primeHierarchyTable($tidArr);
            do {
                $hierarchyAdded = $this->populateHierarchyTable();
            } while($hierarchyAdded > 0);
        }
        return $returnVal;
    }
}
