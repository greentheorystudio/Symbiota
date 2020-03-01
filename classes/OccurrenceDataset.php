<?php
include_once('DbConnection.php');
include_once('DwcArchiverCore.php');

class OccurrenceDataset {

	private $conn;
	private $newDatasetId = 0;

	private $errorArr = array();

	public function __construct(){
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if(!($this->conn === null)) {
			$this->conn->close();
		}
	}

	public function getDatasetMetadata($dsid): array
	{
		global $SYMB_UID;
		$retArr = array();
		if($SYMB_UID && $dsid){
			$sql = 'SELECT datasetid, name, notes, uid, sortsequence, initialtimestamp '.
				'FROM omoccurdatasets '.
				'WHERE (datasetid = '.$dsid.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['name'] = $r->name;
				$retArr['notes'] = $r->notes;
				$retArr['uid'] = $r->uid;
				$retArr['sort'] = $r->sortsequence;
				$retArr['ts'] = $r->initialtimestamp;
			}
			$rs->free();
			$sql1 = 'SELECT role '.
				'FROM userroles '.
				'WHERE (tablename = "omoccurdatasets") AND (tablepk = '.$dsid.') AND (uid = '.$SYMB_UID.') ';
			$rs1 = $this->conn->query($sql1);
			while($r1 = $rs1->fetch_object()){
				$retArr['roles'][] = $r1->role;
			}
			$rs1->free();
		}
		return $retArr;
	}

	public function getDatasetArr(): array
	{
		global $SYMB_UID;
		$retArr = array();
		if($SYMB_UID){
			$sql = 'SELECT datasetid, name, notes, sortsequence, initialtimestamp '.
				'FROM omoccurdatasets '.
				'WHERE (uid = '.$SYMB_UID.') '.
				'ORDER BY sortsequence,name';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['owner'][$r->datasetid]['name'] = $r->name;
				$retArr['owner'][$r->datasetid]['notes'] = $r->notes;
				$retArr['owner'][$r->datasetid]['sort'] = $r->sortsequence;
				$retArr['owner'][$r->datasetid]['ts'] = $r->initialtimestamp;
			}
			$rs->free();

			$sql1 = 'SELECT d.datasetid, d.name, d.notes, d.sortsequence, d.initialtimestamp, r.role '.
				'FROM omoccurdatasets d INNER JOIN userroles r ON d.datasetid = r.tablepk '.
				'WHERE (r.uid = '.$SYMB_UID.') AND (r.role IN("DatasetAdmin","DatasetEditor","DatasetReader")) '.
				'ORDER BY sortsequence,name';
			//echo $sql1;
			$rs1 = $this->conn->query($sql1);
			while($r1 = $rs1->fetch_object()){
				$retArr['other'][$r1->datasetid]['name'] = $r1->name;
				$retArr['other'][$r1->datasetid]['role'] = $r1->role;
				$retArr['other'][$r1->datasetid]['notes'] = $r1->notes;
				$retArr['other'][$r1->datasetid]['sort'] = $r1->sortsequence;
				$retArr['other'][$r1->datasetid]['ts'] = $r1->initialtimestamp;
			}
			$rs1->free();
		}
		return $retArr;
	}

	public function editDataset($dsid,$name,$notes): bool
	{
		$sql = 'UPDATE omoccurdatasets '.
			'SET name = "'.$this->cleanInStr($name).'", notes = "'.$this->cleanInStr($notes).'" '.
			'WHERE datasetid = '.$dsid;
		if(!$this->conn->query($sql)){
			$this->errorArr[] = 'ERROR saving dataset edits: '.$this->conn->error;
			return false;
		}
		return true;
	}

	public function createDataset($name,$notes,$uid): bool
	{
		$sql = 'INSERT INTO omoccurdatasets (name,notes,uid) '.
			'VALUES("'.$this->cleanInStr($name).'","'.$this->cleanInStr($notes).'",'.$uid.') ';
		if(!$this->conn->query($sql)){
			$this->errorArr[] = 'ERROR creating new dataset: '.$this->conn->error;
			return false;
		}

		$this->newDatasetId = $this->conn->insert_id;
		return true;
	}

	public function mergeDatasets($targetArr): bool
	{
		$targetDsid = array_shift($targetArr);
		$newName = '';
		$sql2 = 'UPDATE omoccurdatasets SET name = "'.$newName.'" WHERE datasetid = '.$targetDsid;
		if($this->conn->query($sql2)){
			$sql3 = 'UPDATE IGNORE omoccurdatasetlink SET datasetid = '.$targetDsid.' WHERE datasetid IN('.implode(',',$targetArr).')';
			if($this->conn->query($sql3)){
				$sql4 = 'DELETE FROM omoccurdatasets WHERE datasetid IN('.implode(',',$targetArr).')';
				if(!$this->conn->query($sql4)){
					$this->errorArr[] = 'WARNING: Unable to remove extra datasets: '.$this->conn->error;
					return false;
				}
			}
			else{
				$this->errorArr[] = 'FATAL ERROR: Unable to transfer occurrence records into target dataset: '.$this->conn->error;
				return false;
			}
		}
		else{
			$this->errorArr[] = 'FATAL ERROR: Unable to rename target dataset in prep for merge: '.$this->conn->error;
			return false;
		}
		return true;
	}

	public function cloneDatasets($targetArr): bool
	{
        global $SYMB_UID;
	    $status = true;
		$sql = 'SELECT datasetid, name, notes, sortsequence FROM omoccurdatasets '.
			'WHERE datasetid IN('.implode(',',$targetArr).')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$newName = $r->name.' - Copy';
			$newNameTemp = $newName;
			$cnt = 1;
			do{
				$sql1 = 'SELECT datasetid FROM omoccurdatasets WHERE name = "'.$newNameTemp.'" AND uid = '.$SYMB_UID;
				$nameExists = false;
				$rs1 = $this->conn->query($sql1);
				while($rs1->fetch_object()){
					$newNameTemp = $newName.' '.$cnt;
					$nameExists = true;
					$cnt++;
				}
				$rs1->free();
			}while($nameExists);
			$newName = $newNameTemp;
			$sql2 = 'INSERT INTO omoccurdatasets(name, notes, sortsequence, uid) '.
				'VALUES("'.$newName.'","'.$r->notes.'",'.($r->sortsequence?:'""').','.$SYMB_UID.')';
			if($this->conn->query($sql2)){
				$this->newDatasetId = $this->conn->insert_id;
				$sql3 = 'INSERT INTO omoccurdatasetlink(occid, datasetid, notes) '.
					'SELECT occid, '.$this->newDatasetId.', notes FROM omoccurdatasetlink WHERE datasetid = '.$r->datasetid;
				if(!$this->conn->query($sql3)){
					$this->errorArr[] = 'ERROR: Unable to clone dataset links into new datasets: '.$this->conn->error;
					$status = false;
				}
			}
			else{
				$this->errorArr[] = 'ERROR: Unable to create new dataset within clone method: '.$this->conn->error;
				$status = false;
			}
			
			$dsArr[$r->datasetid] = $r->name;
		}
		$rs->free();
		return $status;
	}

	public function deleteDataset($dsid): bool
	{
		$sql1 = 'DELETE FROM userroles '.
			'WHERE (role IN("DatasetAdmin","DatasetEditor","DatasetReader")) AND (tablename = "omoccurdatasets") AND (tablepk = '.$dsid.') ';
		//echo $sql;
		if(!$this->conn->query($sql1)){
			$this->errorArr[] = 'ERROR deleting user: '.$this->conn->error;
			return false;
		}
		
		$sql2 = 'DELETE FROM omoccurdatasets WHERE datasetid = '.$dsid;
		if(!$this->conn->query($sql2)){
			$this->errorArr[] = 'ERROR: Unable to delete target datasets: '.$this->conn->error;
			return false;
		}
		return true;
		
		$sql3 = 'DELETE FROM omoccurdatasetlink WHERE datasetid = '.$dsid;
		if(!$this->conn->query($sql3)){
			$this->errorArr[] = 'ERROR: Unable to delete target datasets: '.$this->conn->error;
			return false;
		}
		return true;
	}

	public function getUsers($datasetId): array
	{
		$retArr = array();
		$sql = 'SELECT u.uid, r.role, CONCAT_WS(", ",u.lastname,u.firstname) as username '.
				'FROM userroles r INNER JOIN users u ON r.uid = u.uid '.
				'WHERE r.role IN("DatasetAdmin","DatasetEditor","DatasetReader") '.
				'AND (r.tablename = "omoccurdatasets") AND (r.tablepk = '.$datasetId.')';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->role][$r->uid] = $r->username;
		}
		$rs->free();
		return $retArr;
	}

	public function addUser($dsid,$userStr,$role): bool
	{
		$status = true;
		$uid = 0;
		if(preg_match('/\D\[#(.+)]$/',$userStr,$m)){
			$uid = $m[1];
		}
		if(!$uid || !is_numeric($uid)){
			$sql = 'SELECT uid FROM userlogin WHERE username = "'.$userStr.'"';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$uid = $r->uid;
			}
			else{
				$this->errorArr[] = 'ERROR adding new user; unable to locate user name: '.$userStr;
				return false;
			}
			$rs->free();
		}
		if($uid && is_numeric($uid)){
			$sql1 = 'INSERT INTO userroles(uid,role,tablename,tablepk) '.
				'VALUES('.$uid.',"'.$role.'","omoccurdatasets",'.$dsid.')';
			if(!$this->conn->query($sql1)){
				$this->errorArr[] = 'ERROR adding new user: '.$this->conn->error;
				return false;
			}
		}
		else{
			$this->errorArr[] = 'ERROR adding new user; unable to locate user name(2): '.$userStr;
			return false;
		}
		return $status;
	}
	
	public function deleteUser($dsid,$uid,$role): bool
	{
		$status = true;
		$sql = 'DELETE FROM userroles '.
			'WHERE (uid = '.$uid.') AND (role = "'.$role.'") AND (tablename = "omoccurdatasets") AND (tablepk = '.$dsid.') ';
		//echo $sql;
		if(!$this->conn->query($sql)){
			$this->errorArr[] = 'ERROR deleting user: '.$this->conn->error;
			return false;
		}
		return $status;
	}
	
	public function getOccurrences($datasetId): array
	{
		$retArr = array();
		if($datasetId){
			$sql = 'SELECT o.occid, o.catalognumber, o.occurrenceid ,o.othercatalognumbers, '.
				'o.sciname, o.family, o.recordedby, o.recordnumber, o.eventdate, '.
				'o.country, o.stateprovince, o.county, o.locality, o.decimallatitude, o.decimallongitude, dl.notes '.
				'FROM omoccurrences o INNER JOIN omoccurdatasetlink dl ON o.occid = dl.occid '.
				'WHERE dl.datasetid = '.$datasetId;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if($r->catalognumber) {
					$retArr[$r->occid]['catnum'] = $r->catalognumber;
				}
				elseif($r->occurrenceid) {
					$retArr[$r->occid]['catnum'] = $r->occurrenceid;
				}
				elseif($r->othercatalognumbers) {
					$retArr[$r->occid]['catnum'] = $r->othercatalognumbers;
				}
				else {
					$retArr[$r->occid]['catnum'] = '';
				}
				$sciname = $r->sciname;
				if($r->family) {
					$sciname .= ' (' . $r->family . ')';
				}
				$retArr[$r->occid]['sciname'] = $sciname;
				$collStr = $r->recordedby.' '.$r->recordnumber;
				if($r->eventdate) {
					$collStr .= ' [' . $r->eventdate . ']';
				}
				$retArr[$r->occid]['coll'] = $collStr;
				$retArr[$r->occid]['loc'] = trim($r->country.', '.$r->stateprovince.', '.$r->county.', '.$r->locality,', ');
			}
			$rs->free();
		}
		return $retArr; 
	}

	public function removeSelectedOccurrences($datasetId, $occArr): bool
	{
		$status = true;
		if($datasetId && $occArr){
			$sql = 'DELETE FROM omoccurdatasetlink '.
				'WHERE (datasetid = '.$datasetId.') AND (occid IN('.implode(',',$occArr).'))';
			if(!$this->conn->query($sql)){
				$this->errorArr[] = 'ERROR deleting selected occurrences: '.$this->conn->error;
				return false;
			}
		}
		return $status;
	}

	public function exportDataset(): void
	{
		global $IS_ADMIN, $USER_RIGHTS, $CHARSET;
		$zip = (array_key_exists('zip',$_POST)?$_POST['zip']:0);
		$format = $_POST['format'];
		$extended = (array_key_exists('extended',$_POST)?$_POST['extended']:0);
		$schema = array_key_exists('schema',$_POST)?$_POST['schema']: 'symbiota';
	
		$redactLocalities = 1;
		$rareReaderArr = array();
		if($IS_ADMIN || array_key_exists('CollAdmin', $USER_RIGHTS)){
			$redactLocalities = 0;
		}
		elseif(array_key_exists('RareSppAdmin', $USER_RIGHTS) || array_key_exists('RareSppReadAll', $USER_RIGHTS)){
			$redactLocalities = 0;
		}
		else{
			if(array_key_exists('CollEditor', $USER_RIGHTS)){
				$rareReaderArr = $USER_RIGHTS['CollEditor'];
			}
			if(array_key_exists('RareSppReader', $USER_RIGHTS)){
				$rareReaderArr = array_unique(array_merge($rareReaderArr,$USER_RIGHTS['RareSppReader']));
			}
		}
		$dwcaHandler = new DwcArchiverCore();
		$dwcaHandler->setCharSetOut($CHARSET);
		$dwcaHandler->setSchemaType($schema);
		$dwcaHandler->setExtended($extended);
		$dwcaHandler->setDelimiter($format);
		$dwcaHandler->setVerboseMode(0);
		$dwcaHandler->setRedactLocalities($redactLocalities);
		if($rareReaderArr) {
			$dwcaHandler->setRareReaderArr($rareReaderArr);
		}

		$occurManager = new OccurrenceManager();
		$dwcaHandler->setCustomWhereSql($occurManager->getSqlWhere());

		$outputFile = null;
		if($zip){
			$includeIdent = (array_key_exists('identifications',$_POST)?1:0);
			$dwcaHandler->setIncludeDets($includeIdent);
			$includeImages = (array_key_exists('images',$_POST)?1:0);
			$dwcaHandler->setIncludeImgs($includeImages);
			$includeAttributes = (array_key_exists('attributes',$_POST)?1:0);
			$dwcaHandler->setIncludeAttributes($includeAttributes);
				
			$outputFile = $dwcaHandler->createDwcArchive('webreq');
			
		}
		else{
			$outputFile = $dwcaHandler->getOccurrenceFile();
		}
		if($schema === 'dwc'){
			$contentDesc = 'Darwin Core ';
		}
		else{
			$contentDesc = 'Symbiota ';
		}
		$contentDesc .= 'Occurrence ';
		if($zip){
			$contentDesc .= 'Archive ';
		}
		$contentDesc .= 'File';
		header('Content-Description: '.$contentDesc);
		
		if($zip){
			header('Content-Type: application/zip');
		}
		elseif($format === 'csv'){
			header('Content-Type: text/csv; charset='.$CHARSET);
		}
		else{
			header('Content-Type: text/html; charset='.$CHARSET);
		}
		
		header('Content-Disposition: attachment; filename='.basename($outputFile));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($outputFile));
		ob_clean();
		flush();
		readfile($outputFile);
		unlink($outputFile);
	}

    public function getErrorArr(): array
	{
		return $this->errorArr;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
