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
		if($this->conn) {
            $this->conn->close();
        }
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
            'FROM taxadescrblock WHERE tid = '.$tid.' '.
            'ORDER BY displaylevel ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $descrArr = array();
                $descrArr['tdbid'] = $r->tdbid;
                $descrArr['caption'] = $r->caption;
                $descrArr['source'] = $r->source;
                $descrArr['sourceurl'] = $r->sourceurl;
                $descrArr['language'] = $r->language;
                $descrArr['displaylevel'] = $r->displaylevel;
                $descrArr['notes'] = $r->notes;
                $descrArr['stmts'] = $this->getTaxonDescriptionStatements($r->tdbid);
                $retArr[] = $descrArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonDescriptionStatements($tdbid): array
    {
        $retArr = array();
        $sql = 'SELECT tdsid, heading, statement, displayheader, notes, sortsequence '.
            'FROM taxadescrstmts WHERE tdbid = '.$tdbid.' '.
            'ORDER BY sortsequence';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $statementArr = array();
                $statementArr['tdsid'] = $r->tdsid;
                $statementArr['heading'] = $r->heading;
                $statementArr['statement'] = $r->statement;
                $statementArr['displayheader'] = $r->displayheader;
                $statementArr['notes'] = $r->notes;
                $statementArr['sortsequence'] = $r->sortsequence;
                $retArr[] = $statementArr;
            }
            $rs->free();
        }
        return $retArr;
    }
}
