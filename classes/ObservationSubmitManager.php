<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/ImageShared.php');

class ObservationSubmitManager {

	private $conn;
	private $collId;
	private $collMap = array();

	private $errArr = array();

	public function __construct(){
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}
	
	public function __destruct(){
		if(!($this->conn === null)) {
			$this->conn->close();
		}
	}

	public function addObservation($postArr){
		global $SYMB_UID;
		$newOccId = '';
		if($postArr && $this->collId){
			$eventYear = 'NULL'; $eventMonth = 'NULL'; $eventDay = 'NULL'; $startDay = 'NULL';
			if($dateObj = strtotime($postArr['eventdate'])){
				$eventYear = date('Y',$dateObj);
				$eventMonth = date('m',$dateObj);
				$eventDay = date('d',$dateObj);
				$startDay = date('z',$dateObj)+1;
			}
			$tid = 0;
			$localitySecurity = (array_key_exists('localitysecurity',$postArr)?1:0);
			if($postArr['sciname']){
				$result = $this->conn->query('SELECT tid, securitystatus FROM taxa WHERE (sciname = "'.$postArr['sciname'].'")');
				if($row = $result->fetch_object()){
					$tid = $row->tid;
					if($row->securitystatus > 0) {
						$localitySecurity = $row->securitystatus;
					}
					if(!$localitySecurity){
						$sql = 'SELECT cl.tid '.
							'FROM fmchecklists c INNER JOIN fmchklsttaxalink cl ON c.clid = cl.clid '. 
							'WHERE c.type = "rarespp" AND c.locality = "'.$postArr['stateprovince'].'" AND cl.tid = '.$tid;
						$rs = $this->conn->query($sql);
						if($rs->num_rows){
							$localitySecurity = 1;
						}
					}
				}
				else{
					$this->errArr[] = 'ERROR: scientific name failed, contact admin to add name to thesaurus';
					return false;
				}
			}

			$sql = 'INSERT INTO omoccurrences(collid, basisofrecord, family, sciname, scientificname, '.
				'scientificNameAuthorship, tidinterpreted, taxonRemarks, identifiedBy, dateIdentified, '.
				'identificationReferences, recordedBy, recordNumber, '.
				'associatedCollectors, eventDate, `year`, `month`, `day`, startDayOfYear, habitat, substrate, occurrenceRemarks, associatedTaxa, '.
				'verbatimattributes, reproductiveCondition, cultivationStatus, establishmentMeans, country, '.
				'stateProvince, county, locality, localitySecurity, decimalLatitude, decimalLongitude, '.
				'geodeticDatum, coordinateUncertaintyInMeters, georeferenceRemarks, minimumElevationInMeters, observeruid, dateEntered) '.

			'VALUES ('.$this->collId.',"HumanObservation",'.($postArr['family']?'"'.$this->cleanInStr($postArr['family']).'"':'NULL').','.
			'"'.$this->cleanInStr($postArr['sciname']).'","'.
			$this->cleanInStr($postArr['sciname'].' '.$postArr['scientificnameauthorship']).'",'.
			($postArr['scientificnameauthorship']?'"'.$this->cleanInStr($postArr['scientificnameauthorship']).'"':'NULL').','.
			($tid?:'NULL').','.($postArr['taxonremarks']?'"'.$this->cleanInStr($postArr['taxonremarks']).'"':'NULL').','.
			($postArr['identifiedby']?'"'.$this->cleanInStr($postArr['identifiedby']).'"':'NULL').','.
			($postArr['dateidentified']?'"'.$this->cleanInStr($postArr['dateidentified']).'"':'NULL').','.
			($postArr['identificationreferences']?'"'.$this->cleanInStr($postArr['identificationreferences']).'"':'NULL').','.
			'"'.$this->cleanInStr($postArr['recordedby']).'",'.
			($postArr['recordnumber']?'"'.$this->cleanInStr($postArr['recordnumber']).'"':'NULL').','.
			($postArr['associatedcollectors']?'"'.$this->cleanInStr($postArr['associatedcollectors']).'"':'NULL').','.
			'"'.$postArr['eventdate'].'",'.$eventYear.','.$eventMonth.','.$eventDay.','.$startDay.','.
			($postArr['habitat']?'"'.$this->cleanInStr($postArr['habitat']).'"':'NULL').','.
			($postArr['substrate']?'"'.$this->cleanInStr($postArr['substrate']).'"':'NULL').','.
			($postArr['occurrenceremarks']?'"'.$this->cleanInStr($postArr['occurrenceremarks']).'"':'NULL').','.
			($postArr['associatedtaxa']?'"'.$this->cleanInStr($postArr['associatedtaxa']).'"':'NULL').','.
			($postArr['verbatimattributes']?'"'.$this->cleanInStr($postArr['verbatimattributes']).'"':'NULL').','.
			($postArr['reproductivecondition']?'"'.$this->cleanInStr($postArr['reproductivecondition']).'"':'NULL').','.
			(array_key_exists('cultivationstatus',$postArr)?'1':'0').','.
			($postArr['establishmentmeans']?'"'.$this->cleanInStr($postArr['establishmentmeans']).'"':'NULL').','.
			'"'.$this->cleanInStr($postArr['country']).'",'.
			($postArr['stateprovince']?'"'.$this->cleanInStr($postArr['stateprovince']).'"':'NULL').','.
			($postArr['county']?'"'.$this->cleanInStr($postArr['county']).'"':'NULL').','.
			'"'.$this->cleanInStr($postArr['locality']).'",'.$localitySecurity.','.
			$postArr['decimallatitude'].','.$postArr['decimallongitude'].','.
			($postArr['geodeticdatum']?'"'.$this->cleanInStr($postArr['geodeticdatum']).'"':'NULL').','.
			($postArr['coordinateuncertaintyinmeters']?'"'.$postArr['coordinateuncertaintyinmeters'].'"':'NULL').','.
			($postArr['georeferenceremarks']?'"'.$this->cleanInStr($postArr['georeferenceremarks']).'"':'NULL').','.
			($postArr['minimumelevationinmeters']?:'NULL').','.
				$SYMB_UID.',"'.date('Y-m-d H:i:s').'") ';
			//echo $sql;
			if($this->conn->query($sql)){
				$newOccId = $this->conn->insert_id;
				if(isset($postArr['clid'])){
					$clid = $postArr['clid'];
					$finalTid = 0;
					if($tid){
						$sql = 'SELECT cltl.tid '.
							'FROM fmchklsttaxalink cltl INNER JOIN taxstatus ts1 ON cltl.tid = ts1.tid '.
							'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
							'WHERE ts1.taxauthid = 1 AND ts2.taxauthid = 1 AND cltl.clid = '.$clid.' AND ts2.tid = '.$tid;
						$rs = $this->conn->query($sql);
						while($r = $rs->fetch_object()){
							$finalTid = $r->tid;
							if($finalTid === $tid) {
								break;
							}
						}
						$rs->free();
						if(!$finalTid){
							$sql = 'INSERT INTO fmchklsttaxalink(tid,clid) '.
								'VALUES('.$tid.','.$clid.')';
							$this->conn->query($sql);
							$finalTid = $tid;
						}
					}
					$sql = 'INSERT INTO fmvouchers(tid,clid,occid) '.
						'VALUES('.($finalTid?:'NULL').','.$clid.','.$newOccId.') ';
					$this->conn->query($sql);
				}
				if(!$this->addImages($postArr,$newOccId,$tid)){
					$this->errArr[] = 'Observation added successfully, but images did not upload successful';
				}
				if(is_numeric($postArr['confidenceranking'])){
					$sqlVer = 'INSERT INTO omoccurverification(occid,category,ranking,uid) '.
							'VALUES('.$newOccId.',"identification",'.$postArr['confidenceranking'].','.$SYMB_UID.')';
					$this->conn->query($sqlVer);
				}
			}
			else{
				$this->errArr[] = 'ERROR: Failed to load observation record.<br/> Err Descr: '.$this->conn->error;
			}
		}
		return $newOccId;
	}

	private function addImages($postArr,$newOccId,$tid): bool
	{
		global $SYMB_UID;
		$status = true;
		$imgManager = new ImageShared();
		$subTargetPath = $this->collMap['institutioncode'];
		if($this->collMap['collectioncode']) {
			$subTargetPath .= '_' . $this->collMap['collectioncode'];
		}
		
		for($i=1;$i<=5;$i++){
			$imgManager->setTargetPath($subTargetPath.'/'.date('Ym').'/');
			$imgManager->setMapLargeImg(false);
			$imgManager->setPhotographerUid($SYMB_UID);
			$imgManager->setSortSeq(40);
			$imgManager->setOccid($newOccId);
			$imgManager->setTid($tid);
				
			$imgFileName = 'imgfile'.$i;
			if(!array_key_exists($imgFileName,$_FILES) || !$_FILES[$imgFileName]['name']) {
				break;
			}
		
			$capLabel = 'caption'.$i;
			if(isset($postArr[$capLabel])) {
				$imgManager->setCaption($postArr['caption' . $i]);
			}
			$noteLabel = 'notes'.$i;
			if(isset($postArr[$noteLabel])) {
				$imgManager->setNotes($postArr['notes' . $i]);
			}
		
			if($imgManager->uploadImage($imgFileName)){
				$status = $imgManager->processImage();
			}
			else{
				$status = false;
			}
			if(!$status && $errArr = $imgManager->getErrArr()) {
				foreach($errArr as $errStr){
					$this->errArr[] = $errStr;
				}
			}
			$imgManager->reset();
		}
		return $status;
	}

	public function getChecklists(): array
	{
		global $USER_RIGHTS;
		$retArr = array();
		if(isset($USER_RIGHTS['ClAdmin'])){
			$sql = 'SELECT clid, name, access '.
				'FROM fmchecklists '.
				'WHERE clid IN('.implode(',',$USER_RIGHTS['ClAdmin']).') '.
				'ORDER BY name';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				$retArr[$row->clid] = $row->name.($row->access === 'private'?' (private)':'');
			}
		}
		return $retArr;
	}
 	
	public function getCollMap(): array
	{
		return $this->collMap;
	}
	
	public function getErrorArr(): array
	{
		return $this->errArr;
	}

	public function setCollid($id): void
	{
		if(is_numeric($id)) {
			$this->collId = $id;
		}
		$this->setMetadata();
	}

	private function setMetadata(): void
	{
		$sql = 'SELECT collid, institutioncode, collectioncode, collectionname, colltype FROM omcollections ';
		if($this->collId){
			$sql .= 'WHERE (collid = '.$this->collId.')';
		}
		else{
			$sql .= 'WHERE (colltype = "General Observations")';
		}
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$this->collMap['collid'] = $r->collid;
			$this->collMap['institutioncode'] = $r->institutioncode;
			$this->collMap['collectioncode'] = $r->collectioncode;
			$this->collMap['collectionname'] = $this->cleanOutStr($r->collectionname);
			$this->collMap['colltype'] = $r->colltype;
			if(!$this->collId){
				$this->collId = $r->collid;
			}
		}
		$rs->free();
	}

	public function getUserName(): string
	{
		global $SYMB_UID;
		$retStr = '';
		if(is_numeric($SYMB_UID)){
			$sql = 'SELECT CONCAT_WS(", ",lastname,firstname) AS username FROM users WHERE uid = '.$SYMB_UID;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retStr = $r->username;
			}
			$rs->free();
		}
		return $retStr;
	}

	private function cleanOutStr($str){
		return str_replace(array('"', "'"), array('&quot;', '&apos;'), $str);
	}
	
	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
