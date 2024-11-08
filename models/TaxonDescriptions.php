<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonDescriptions{

	private $conn;

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function addTaxonDescriptionStatement($statement): int
    {
        $retVal = 0;
        if($statement){
            $sql = 'INSERT INTO taxadescrstmts (tdbid, heading, `statement`, displayheader, notes, sortsequence) '.
                'VALUES ('.
                (isset($statement['tdbid']) ? (int)$statement['tdbid'] :'NULL').','.
                (isset($statement['heading']) ? '"'.SanitizerService::cleanInStr($this->conn,$statement['heading']).'"' :'NULL').','.
                (isset($statement['statement']) ? '"'.SanitizerService::cleanInStr($this->conn,strip_tags($statement['statement'], '<i><b><em><div>')).'"' :'NULL').','.
                (isset($statement['displayheader']) ? (int)$statement['displayheader'] :'1').','.
                (isset($statement['notes']) ? '"'.SanitizerService::cleanInStr($this->conn,$statement['notes']).'"' :'NULL').','.
                (isset($statement['sortsequence']) ? (int)$statement['sortsequence'] :'1').')';
            //echo $sql; exit;
            if($this->conn->query($sql)){
                $retVal = $this->conn->insert_id;
            }
        }
        return $retVal;
    }

    public function addTaxonDescriptionTab($description): int
    {
        $retVal = 0;
        if($description){
            $sql = 'INSERT INTO taxadescrblock (tid, caption, `source`, sourceurl, `language`, langid, displaylevel, uid, notes) '.
                'VALUES ('.
                (isset($description['tid']) ? (int)$description['tid'] :'NULL').','.
                (isset($description['caption']) ? '"'.SanitizerService::cleanInStr($this->conn,$description['caption']).'"' :'NULL').','.
                (isset($description['source']) ? '"'.SanitizerService::cleanInStr($this->conn,$description['source']).'"' :'NULL').','.
                (isset($description['sourceurl']) ? '"'.SanitizerService::cleanInStr($this->conn,$description['sourceurl']).'"' :'NULL').','.
                (isset($description['language']) ? '"'.SanitizerService::cleanInStr($this->conn,$description['language']).'"' :'NULL').','.
                (isset($description['langid']) ? (int)$description['langid'] :'NULL').','.
                (isset($description['displaylevel']) ? (int)$description['displaylevel'] :'1').','.
                '"'.$GLOBALS['USERNAME'].'",'.
                (isset($description['notes']) ? '"'.SanitizerService::cleanInStr($this->conn,$description['notes']).'"' :'NULL').')';
            //echo $sql; exit;
            if($this->conn->query($sql)){
                $retVal = $this->conn->insert_id;
            }
        }
        return $retVal;
    }

    public function getTaxonDescriptions($tid): array
    {
        $retArr = array();
        $sql = 'SELECT tdbid, caption, source, sourceurl, language, displaylevel, notes '.
            'FROM taxadescrblock WHERE tid = ' . (int)$tid . ' '.
            'ORDER BY displaylevel ';
        //echo $sql;
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $descrArr = array();
                $descrArr['tdbid'] = $row['tdbid'];
                $descrArr['caption'] = $row['caption'];
                $descrArr['source'] = $row['source'];
                $descrArr['sourceurl'] = $row['sourceurl'];
                $descrArr['language'] = $row['language'];
                $descrArr['displaylevel'] = $row['displaylevel'];
                $descrArr['notes'] = $row['notes'];
                $descrArr['stmts'] = $this->getTaxonDescriptionStatements($row['tdbid']);
                $retArr[] = $descrArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxonDescriptionStatements($tdbid): array
    {
        $retArr = array();
        $sql = 'SELECT tdsid, heading, statement, displayheader, notes, sortsequence '.
            'FROM taxadescrstmts WHERE tdbid = ' . (int)$tdbid . ' '.
            'ORDER BY sortsequence';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $statementArr = array();
                $statementArr['tdsid'] = $row['tdsid'];
                $statementArr['heading'] = $row['heading'];
                $statementArr['statement'] = $row['statement'];
                $statementArr['displayheader'] = $row['displayheader'];
                $statementArr['notes'] = $row['notes'];
                $statementArr['sortsequence'] = $row['sortsequence'];
                $retArr[] = $statementArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }
}
