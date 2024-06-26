<?php
include_once(__DIR__ . '/TPEditorManager.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TPDescEditorManager extends TPEditorManager{

    public function __construct(){
        parent::__construct();
    }

    public function getDescriptions($editor = null): array
	{
		$descrArr = array();
		$sql = 'SELECT t.tid, t.sciname, tdb.tdbid, tdb.caption, tdb.source, tdb.sourceurl, tdb.displaylevel, tdb.notes, tdb.language '.
			'FROM taxa AS t INNER JOIN taxadescrblock AS tdb ON t.tid = tdb.tid '.
			'WHERE t.tidaccepted = '.$this->tid.' ';
		$sql .= 'ORDER BY tdb.DisplayLevel ';
		//echo $sql;
		if($rs = $this->taxonCon->query($sql)){
			while($r = $rs->fetch_object()){
				$descrArr[$r->tdbid]['caption'] = $r->caption;
				$descrArr[$r->tdbid]['source'] = $r->source;
				$descrArr[$r->tdbid]['sourceurl'] = $r->sourceurl;
				$descrArr[$r->tdbid]['displaylevel'] = $r->displaylevel;
				$descrArr[$r->tdbid]['notes'] = $r->notes;
				$descrArr[$r->tdbid]['language'] = $r->language;
				$descrArr[$r->tdbid]['tid'] = $r->tid;
				$descrArr[$r->tdbid]['sciname'] = $r->sciname;
			}
			$rs->free();
		}
		if($descrArr){
			$sql2 = 'SELECT tdbid, tdsid, heading, statement, notes, displayheader, sortsequence '.
				'FROM taxadescrstmts '.
				'WHERE (tdbid IN('.implode(',',array_keys($descrArr)).')) '.
				'ORDER BY sortsequence'; 
			if($rs2 = $this->taxonCon->query($sql2)){
				while($r2 = $rs2->fetch_object()){
					$descrArr[$r2->tdbid]['stmts'][$r2->tdsid]['heading'] = $r2->heading;
					$descrArr[$r2->tdbid]['stmts'][$r2->tdsid]['statement'] = $r2->statement;
					$descrArr[$r2->tdbid]['stmts'][$r2->tdsid]['notes'] = $r2->notes;
					$descrArr[$r2->tdbid]['stmts'][$r2->tdsid]['displayheader'] = $r2->displayheader;
					$descrArr[$r2->tdbid]['stmts'][$r2->tdsid]['sortsequence'] = $r2->sortsequence;
				}
				$rs2->free();
			}
		}
		return $descrArr;
	}

	public function editDescriptionBlock(): string
	{
		$sql = 'UPDATE taxadescrblock ' .
			'SET language = ' .($_REQUEST['language']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['language']).'"': 'NULL').
			',displaylevel = ' .SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['displaylevel']).
			',notes = ' .($_REQUEST['notes']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['notes']).'"': 'NULL').
			',caption = ' .($_REQUEST['caption']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['caption']).'"': 'NULL').
			',source = ' .($_REQUEST['source']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['source']).'"': 'NULL').
			',sourceurl = ' .($_REQUEST['sourceurl']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['sourceurl']).'"': 'NULL').
			' WHERE (tdbid = ' .$this->taxonCon->real_escape_string($_REQUEST['tdbid']).')';
		//echo $sql;
		$status = '';
		if(!$this->taxonCon->query($sql)){
			$status = 'ERROR editing description block.';
			//$status .= "\nSQL: ".$sql;
		}
		return $status;
	}

	public function deleteDescriptionBlock(): string
	{
		$sql = 'DELETE FROM taxadescrblock WHERE (tdbid = '.$this->taxonCon->real_escape_string($_REQUEST['tdbid']).')';
		//echo $sql;
		$status = '';
		if(!$this->taxonCon->query($sql)){
			$status = 'ERROR deleting description block.';
			//$status .= "\nSQL: ".$sql;
		}
		return $status;
	}

	public function addDescriptionBlock(): string
	{
		$status = '';
		if(is_numeric($_REQUEST['tid'])){
			$sql = 'INSERT INTO taxadescrblock(tid,uid,'.($_REQUEST['language']? 'language,' : '').($_REQUEST['displaylevel']? 'displaylevel,' : '').
				'notes,caption,source,sourceurl) '.
				'VALUES('.$_REQUEST['tid'].','.$GLOBALS['SYMB_UID'].
				','.($_REQUEST['language']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['language']).'",': '').
				($_REQUEST['displaylevel']?$this->taxonCon->real_escape_string($_REQUEST['displaylevel']). ',' : '').
				($_REQUEST['notes']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['notes']).'",': 'NULL,').
				($_REQUEST['caption']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['caption']).'",': 'NULL,').
				($_REQUEST['source']?'"'.SanitizerService::cleanInStr($this->taxonCon,$_REQUEST['source']).'",': 'NULL,').
				($_REQUEST['sourceurl']?'"'.$_REQUEST['sourceurl'].'"': 'NULL').')';
			//echo $sql;
			if(!$this->taxonCon->query($sql)){
				$status = 'ERROR adding description block.';
			}
		}
		return $status;
	}
	
	public function remapDescriptionBlock($tdbid): string
	{
		$statusStr = '';
		$displayLevel = 1;
		$sql = 'SELECT max(displaylevel) as maxdl FROM taxadescrblock WHERE tid = '.$this->tid; 
		if($rs = $this->taxonCon->query($sql)){
			if($r = $rs->fetch_object()){
				$displayLevel = $r->maxdl + 1;
			}
			$rs->free();
		}
		
		$sql = 'UPDATE taxadescrblock SET tid = '.$this->tid.',displaylevel = '.$displayLevel.' WHERE tdbid = '.$tdbid;
		//echo $sql;
		if(!$this->taxonCon->query($sql)){
			$statusStr = 'ERROR remapping description block.';
		}
		return $statusStr;
	}

	public function addStatement($stArr): string
	{
		$status = '';
		$stmtStr = SanitizerService::cleanInStr($this->taxonCon,$stArr['statement']);
		if(strncmp($stmtStr, '<p>', 3) === 0 && substr($stmtStr,-4) === '</p>'){
			$stmtStr = trim(substr($stmtStr,3, -4));
		}
		if($stmtStr && $stArr['tdbid'] && is_numeric($stArr['tdbid'])){
			$sql = 'INSERT INTO taxadescrstmts(tdbid,heading,statement,displayheader'.($stArr['sortsequence']?',sortsequence':'').') '.
				'VALUES('.$stArr['tdbid'].',"'.SanitizerService::cleanInStr($this->taxonCon,$stArr['heading']).
				'","'.$stmtStr.'",'.(array_key_exists('displayheader',$stArr)?'1':'0').
				($stArr['sortsequence']?','.SanitizerService::cleanInStr($this->taxonCon,$stArr['sortsequence']):'').')';
			//echo $sql;
			if(!$this->taxonCon->query($sql)){
				$status = 'ERROR adding description statement.';
			}
		}
		return $status;
	}
	
	public function editStatement($stArr): string
	{
		$status = '';
		$stmtStr = SanitizerService::cleanInStr($this->taxonCon,$stArr['statement']);
		if(strncmp($stmtStr, '<p>', 3) === 0 && substr($stmtStr,-4) === '</p>'){
			$stmtStr = trim(substr($stmtStr,3, -4));
		}
		if($stmtStr && $stArr['tdsid'] && is_numeric($stArr['tdsid'])){
			$sql = 'UPDATE taxadescrstmts '.
				'SET heading = "'.SanitizerService::cleanInStr($this->taxonCon,$stArr['heading']).'",'.
				'statement = "'.$stmtStr.'"'.
				(array_key_exists('displayheader',$stArr)?',displayheader = 1':',displayheader = 0').
				($stArr['sortsequence']?',sortsequence = '.SanitizerService::cleanInStr($this->taxonCon,$stArr['sortsequence']):'').
				' WHERE (tdsid = '.$stArr['tdsid'].')';
			//echo $sql;
			if(!$this->taxonCon->query($sql)){
				$status = 'ERROR editing description statement.';
			}
		}
		return $status;
	}

	public function deleteStatement($tdsid): string
	{
		$status = '';
		if(is_numeric($tdsid)){
			$sql = 'DELETE FROM taxadescrstmts WHERE (tdsid = '.$tdsid.')';
			//echo $sql;
			if(!$this->taxonCon->query($sql)){
				$status = 'ERROR deleting description statement.';
			}
		}
		return $status;
	}
}
