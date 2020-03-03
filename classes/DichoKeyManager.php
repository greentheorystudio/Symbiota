<?php
include_once('DbConnection.php');

class DichoKeyManager{

    private function getConnection(): mysqli
    {
        $connection = new DbConnection();
        return $connection->getConnection();
    }

    public function buildKey($clid, $taxonFilter): void
    {
        $con = $this->getConnection();
        $stmtTaxaMap = array();
        $sql = 'SELECT t.tid, t.sciname, ts.family, ts.hierarchystr, ts.parenttid ' .
            'FROM (fmchklsttaxalink cl INNER JOIN taxstatus ts ON cl.tid = ts.tid) ' .
            'INNER JOIN taxa t ON ts.tid = t.tid ' .
            "WHERE ts.taxauthid = 1 AND cl.clid = $clid ";
        if($taxonFilter){
            $sql .= "AND (ts.family = '".$taxonFilter."' OR t.sciname LIKE '".$taxonFilter."%')";
        }
        //echo $sql."<br/>";
        $result = $con->query($sql);
        $parentArr = array();
        $taxaArr = array();
        $tempTaxa = array();
        while($row = $result->fetch_object()){
            $childTid = $row->tid;
            if($row->hierarchystr){
                $hierArr = array_reverse(explode(',',$row->hierarchystr));
                foreach($hierArr as $tid){
                    if(array_key_exists($childTid,$parentArr)) {
                        break;
                    }
                    $parentArr[$childTid] = $tid;
                    $childTid = $tid;
                }
            }
            else{
                $parentArr[$row->tid] = $row->parenttid;
            }
            $taxaArr[$row->family][$row->tid] = $row->sciname;
            $tempTaxa[] = $row->tid;
        }
        $result->close();

        $sql = 'SELECT dk.stmtid, dk.statement, dk.hierarchystr, dk.tid ' .
            'FROM dichotomouskey dk INNER JOIN taxa t ON dk.tid = t.tid ' .
            'WHERE dk.tid IN(' .implode(',',array_keys($parentArr)). ') ORDER BY t.rankid DESC ';
        $result = $con->query($sql);
        //echo $sql;
        $stmtArr = array();
        while($row = $result->fetch_object()){
            $hArr = explode(',',$row->hierarchystr);
            $pStmt = 0;
            if($row->hierarchystr){
                foreach($hArr as $v){
                    if(!array_key_exists($pStmt,$stmtArr) || !array_key_exists($v,$stmtArr[$pStmt])) {
                        $stmtArr[$pStmt][$v] = 0;
                    }
                    $pStmt = $v;
                }
            }
            $stmtArr[$pStmt][$row->stmtid] = ($row->tid?:0);
            $children = array();
            $child = $row->tid;
            do{
                $keys = array_keys($parentArr,$child);
                if($keys){
                    foreach($keys as $key){
                        $children[] = $key;
                    }
                }
                if(in_array($child, $tempTaxa, true)){
                    $stmtTaxaMap[$row->stmtid][] = $child;
                }
            }
            while($child = array_shift($children));
        }
        $result->close();
        unset($tempTaxa);

        ksort($stmtArr);
        foreach($stmtArr as $p => $sArr){
            while(is_array($sArr) && count($sArr) === 1){
                $taxon = $sArr;
                $key = key($sArr);
                if(array_key_exists($key,$stmtArr)){
                    $sArr = $stmtArr[$key];
                    unset($stmtArr[$key]);
                    $stmtArr[$p] = $sArr;
                }
                else{
                    foreach($stmtArr as $k => $vArr){
                        if(array_key_exists($p,$vArr) && $vArr[$p] === 0){
                            $stmtArr[$k][$p] = $taxon;
                            unset($stmtArr[$p]);
                            break;
                        }
                    }
                    break;
                }
            }
        }

        $tempArr = array();
        foreach($stmtArr as $k => $innerArr){
            $keys = array_keys($innerArr);
            foreach($keys as $key){
                $tempArr[] = $key;
            }
        }
        $sql = 'SELECT dk.stmtid, dk.statement FROM dichotomouskey dk WHERE dk.stmtid IN ('.implode(',',$tempArr).')';
        $rs = $con->query($sql);
        while($row = $rs->fetch_object()){
            $tempArr[$row->stmtid] = $row->statement;
        }
        $rs->close();
        foreach($stmtArr as $pid => $cArr){
            foreach($cArr as $sid => $v){
                $stmtArr[$pid][$sid] = $tempArr[$sid];
            }
        }

        $displayStmt = true;
        foreach($stmtArr as $sId => $stStrArr){
            echo "<div id='".$sId."' style='display:".($displayStmt? 'block' : 'none').";'>\n";
            foreach($stStrArr as $k => $str){
                echo "<div style onclick=''>".$str."</div>\n";
            }
            echo "</div>\n";
            $displayStmt = false;
        }
        //print_r($stmtArr);
        echo '<br/><br/>';

        //print_r($taxaArr);
        echo '<br/><br/>';

        //print_r($stmtTaxaMap);

        $con->close();
    }
}
