<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/ImageShared.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class ObservationSubmitManager {

	private $conn;
	private $collId;
	private $collMap = array();

	private $errArr = array();

	public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}
	
	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function addObservation($postArr){
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

			$sql = 'INSERT INTO omoccurrences(collid, basisofrecord, family, sciname, verbatimScientificName, '.
				'scientificNameAuthorship, tid, taxonRemarks, identifiedBy, dateIdentified, '.
				'identificationReferences, recordedBy, recordNumber, '.
				'associatedCollectors, eventDate, `year`, `month`, `day`, startDayOfYear, habitat, substrate, occurrenceRemarks, associatedTaxa, '.
				'verbatimattributes, reproductiveCondition, cultivationStatus, establishmentMeans, country, '.
				'stateProvince, county, locality, localitySecurity, decimalLatitude, decimalLongitude, '.
				'geodeticDatum, coordinateUncertaintyInMeters, georeferenceRemarks, minimumElevationInMeters, observeruid, dateEntered) '.

			'VALUES ('.$this->collId.',"HumanObservation",'.($postArr['family']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['family']).'"':'NULL').','.
			'"'.SanitizerService::cleanInStr($this->conn,$postArr['sciname']).'","'.
			SanitizerService::cleanInStr($this->conn,$postArr['sciname'].' '.$postArr['scientificnameauthorship']).'",'.
			($postArr['scientificnameauthorship']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['scientificnameauthorship']).'"':'NULL').','.
			($tid?:'NULL').','.($postArr['taxonremarks']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['taxonremarks']).'"':'NULL').','.
			($postArr['identifiedby']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['identifiedby']).'"':'NULL').','.
			($postArr['dateidentified']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['dateidentified']).'"':'NULL').','.
			($postArr['identificationreferences']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['identificationreferences']).'"':'NULL').','.
			'"'.SanitizerService::cleanInStr($this->conn,$postArr['recordedby']).'",'.
			($postArr['recordnumber']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['recordnumber']).'"':'NULL').','.
			($postArr['associatedcollectors']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['associatedcollectors']).'"':'NULL').','.
			'"'.$postArr['eventdate'].'",'.$eventYear.','.$eventMonth.','.$eventDay.','.$startDay.','.
			($postArr['habitat']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['habitat']).'"':'NULL').','.
			($postArr['substrate']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['substrate']).'"':'NULL').','.
			($postArr['occurrenceremarks']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['occurrenceremarks']).'"':'NULL').','.
			($postArr['associatedtaxa']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['associatedtaxa']).'"':'NULL').','.
			($postArr['verbatimattributes']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['verbatimattributes']).'"':'NULL').','.
			($postArr['reproductivecondition']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['reproductivecondition']).'"':'NULL').','.
			(array_key_exists('cultivationstatus',$postArr)?'1':'0').','.
			($postArr['establishmentmeans']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['establishmentmeans']).'"':'NULL').','.
			'"'.SanitizerService::cleanInStr($this->conn,$postArr['country']).'",'.
			($postArr['stateprovince']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['stateprovince']).'"':'NULL').','.
			($postArr['county']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['county']).'"':'NULL').','.
			'"'.SanitizerService::cleanInStr($this->conn,$postArr['locality']).'",'.$localitySecurity.','.
			$postArr['decimallatitude'].','.$postArr['decimallongitude'].','.
			($postArr['geodeticdatum']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['geodeticdatum']).'"':'NULL').','.
			($postArr['coordinateuncertaintyinmeters']?'"'.$postArr['coordinateuncertaintyinmeters'].'"':'NULL').','.
			($postArr['georeferenceremarks']?'"'.SanitizerService::cleanInStr($this->conn,$postArr['georeferenceremarks']).'"':'NULL').','.
			($postArr['minimumelevationinmeters']?:'NULL').','.
				$GLOBALS['SYMB_UID'].',"'.date('Y-m-d H:i:s').'") ';
			//echo $sql;
			if($this->conn->query($sql)){
				$newOccId = $this->conn->insert_id;
				if(isset($postArr['clid'])){
					$clid = $postArr['clid'];
					$finalTid = 0;
					if($tid){
						$sql = 'SELECT cltl.tid '.
							'FROM fmchklsttaxalink AS cltl INNER JOIN taxa AS t ON cltl.tid = t.tid '.
							'WHERE cltl.clid = '.$clid.' AND t.tidaccepted = '.$tid;
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
			}
			else{
				$this->errArr[] = 'ERROR: Failed to load observation record.';
			}
		}
		return $newOccId;
	}

	private function addImages($postArr,$newOccId,$tid): bool
	{
		$status = true;
		$imgManager = new ImageShared();
        $subTargetPath = $this->collId;
		
		for($i=1;$i<=5;$i++){
			$imgManager->setTargetPath($subTargetPath.'/'.date('Ym').'/');
			$imgManager->setMapLargeImg(false);
			$imgManager->setPhotographerUid($GLOBALS['SYMB_UID']);
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
		$retArr = array();
		if(isset($GLOBALS['USER_RIGHTS']['ClAdmin'])){
			$sql = 'SELECT clid, name, access '.
				'FROM fmchecklists '.
				'WHERE clid IN('.implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']).') '.
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
			$sql .= 'WHERE (colltype = "HumanObservation")';
		}
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$this->collMap['collid'] = $r->collid;
			$this->collMap['institutioncode'] = $r->institutioncode;
			$this->collMap['collectioncode'] = $r->collectioncode;
			$this->collMap['collectionname'] = SanitizerService::cleanOutStr($r->collectionname);
			$this->collMap['colltype'] = $r->colltype;
			if(!$this->collId){
				$this->collId = $r->collid;
			}
		}
		$rs->free();
	}

	public function getUserName(): string
	{
		$retStr = '';
		if(is_numeric($GLOBALS['SYMB_UID'])){
			$sql = 'SELECT CONCAT_WS(", ",lastname,firstname) AS username FROM users WHERE uid = '.$GLOBALS['SYMB_UID'];
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retStr = $r->username;
			}
			$rs->free();
		}
		return $retStr;
	}
}
