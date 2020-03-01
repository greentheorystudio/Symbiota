<?php
include_once('DbConnection.php');

class DichoManager{

    private function getConnection(): mysqli
    {
        $connection = new DbConnection();
        return $connection->getConnection();
    }

    public function echoNodeById($nodeId): array
    {
        $sql = 'SELECT DISTINCT d.nodeid, d.stmtid, d.statement, d.parentstmtid, d.tid, t.sciname, d.notes, dc.nodeid AS childid ' .
            'FROM dichotomouskey d LEFT JOIN taxa t ON d.tid = t.tid ' .
            'LEFT JOIN dichotomouskey dc ON d.stmtid = dc.parentstmtid ' .
            'WHERE d.nodeid = ' .$nodeId. ' ' .
            'ORDER BY d.stmtid';
        //echo $sql;
        return $this->echoNode($sql);
    }

    public function echoNodeByStmtId($stmtId): array
    {
        $sql = 'SELECT DISTINCT d.nodeid, d.stmtid, d.statement, d.parentstmtid, d.tid, t.sciname, d.notes, dc.nodeid AS childid ' .
            'FROM dichotomouskey d LEFT JOIN taxa t ON d.tid = t.tid ' .
            'LEFT JOIN dichotomouskey dc ON d.stmtid = dc.parentstmtid ' .
            "WHERE d.nodeid = (SELECT d2.nodeid FROM dichotomouskey d2 WHERE d2.stmtid = $stmtId) ".
            'ORDER BY d.stmtid';
        return $this->echoNode($sql);
    }

    private function echoNode($sql): array
    {
        $con = $this->getConnection();
        $result = $con->query($sql);
        $returnArr = array();
        $stmtCnt = 0;
        while($row = $result->fetch_object()){
            $returnArr[$stmtCnt]['nodeid'] = $row->nodeid;
            $returnArr[$stmtCnt]['stmtid'] = $row->stmtid;
            $returnArr[$stmtCnt]['statement'] = $row->statement;
            $returnArr[$stmtCnt]['parentstmtid'] = $row->parentstmtid;
            $returnArr[$stmtCnt]['childid'] = $row->childid;
            $returnArr[$stmtCnt]['sciname'] = $row->sciname;
            $returnArr[$stmtCnt]['tid'] = $row->tid;
            $returnArr[$stmtCnt]['notes'] = $row->notes;
            $stmtCnt++;
        }
        $result->close();
        $con->close();
        return $returnArr;
    }

    public function editStatement($dataArr): void
    {
        $con = $this->getConnection();
        $sql = 'UPDATE dichotomouskey ' .
            "SET statement = '".$this->cleanString($dataArr['statement'])."',tid=".($dataArr['tid']?:"\N").
            ",notes='".$this->cleanString($dataArr['notes'])."' ".
            'WHERE stmtid = ' .$dataArr['stmtid'];
        //echo $sql;
        if(!$con->query($sql)){
            echo '<div>ERROR Updating Statement: ' .$con->error. '</div>';
            echo '<div>SQL: ' .$sql. '</div>';
        }
        $con->close();
    }

    public function addCuplet($dataArr){
        $parentStart=0;
        $childStart=0;
        $childEnd=0;
        $con = $this->getConnection();
        $sql = 'SELECT di.startindex, di.endindex FROM dichotomousindex di WHERE di.nodeid = '.$dataArr['nodeid'];
        //echo $sql;
        $result = $con->query($sql);
        if($row = $result->fetch_object()){
            $parentStart = $row->startindex;
        }
        $result->close();

        $sql = 'SELECT di.startindex, di.endindex ' .
            'FROM dichotomouskey dk INNER JOIN dichotomousindex di ON dk.nodeid = di.nodeid ' .
            'WHERE dk.parentstmtid = ' .$dataArr['parentstmtid'];
        //echo $sql;
        $result = $con->query($sql);
        if($row = $result->fetch_object()){
            $childStart = $row->startindex;
        }
        $result->close();

        if($childStart){
            $con->query('UPDATE dichotomousindex SET endindex = endindex + 2 WHERE startindex < '.$childStart.' AND endindex > '.$childEnd);
            $con->query('UPDATE dichotomousindex SET startindex = startindex + 1,endindex = endindex + 1 WHERE startindex >= '.$childStart.' AND endindex <= '.$childEnd);
            $con->query('INSERT INTO dichotomousindex (startindex, endindex) VALUES("'.($childStart).'","'.($childEnd+2).'")"');
        }
        else{
            $con->query('UPDATE dichotomousindex SET endindex = endindex +2 WHERE endindex > '.$parentStart);
            $con->query('UPDATE dichotomousindex SET startindex = startindex + 2 WHERE startindex > '.$parentStart);
            $con->query('INSERT INTO dichotomousindex (startindex, endindex) VALUES('.($parentStart+1).','.($parentStart+2).')');
        }

        $newNodeId = $con->insert_id;

        $sql = 'INSERT INTO dichotomouskey (nodeid,statement,parentstmtid,tid,notes) '.
            'VALUES('.$newNodeId.',"'.$this->cleanString($dataArr['statement']).'",'.$dataArr['parentstmtid'].','.($dataArr['tid']?:"\N").',"'.$this->cleanString($dataArr['notes']).'") "';
        if(!$con->query($sql)){
            echo '<div>ERROR Loading Statement1: ' .$con->error. '</div>';
            echo '<div>SQL: ' .$sql. '</div>';
        }
        $sql = 'INSERT INTO dichotomouskey (nodeid,statement,parentstmtid,tid,notes) '.
            'VALUES('.$newNodeId.',"'.$this->cleanString($dataArr['statement2']).'",'.$dataArr['parentstmtid'].','.($dataArr['tid2']?:"\N").',"'.$this->cleanString($dataArr['notes2']).'") "';
        if(!$con->query($sql)){
            echo '<div>ERROR Loading Statement1: ' .$con->error. '</div>';
            echo '<div>SQL: ' .$sql. '</div>';
        }

        $con->close();
        return $newNodeId;
    }

    private function cleanString($str){
        $str = str_replace(array('"', chr(10), chr(11), chr(12), chr(13)), array('-', '', '', '', ''), $str);
        return $str;
    }
}
