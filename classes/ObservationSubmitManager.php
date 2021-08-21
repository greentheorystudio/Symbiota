<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/ImageShared.php');
include_once(__DIR__ . '/Sanitizer.php');

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

			$sql = 'INSERT INTO omoccurrences(collid, basisofrecord, family, sciname, scientificname, '.
				'scientificNameAuthorship, tidinterpreted, taxonRemarks, identifiedBy, dateIdentified, '.
				'identificationReferences, recordedBy, recordNumber, '.
				'associatedCollectors, eventDate, `year`, `month`, `day`, startDayOfYear, habitat, substrate, occurrenceRemarks, associatedTaxa, '.
				'verbatimattributes, reproductiveCondition, cultivationStatus, establishmentMeans, country, '.
				'stateProvince, county, locality, localitySecurity, decimalLatitude, decimalLongitude, '.
				'geodeticDatum, coordinateUncertaintyInMeters, georeferenceRemarks, minimumElevationInMeters, observeruid, dateEntered) '.

			'VALUES ('.$this->collId.',"HumanObservation",'.($postArr['family']?'"'.Sanitizer::cleanInStr($postArr['family']).'"':'NULL').','.
			'"'.Sanitizer::cleanInStr($postArr['sciname']).'","'.
			Sanitizer::cleanInStr($postArr['sciname'].' '.$postArr['scientificnameauthorship']).'",'.
			($postArr['scientificnameauthorship']?'"'.Sanitizer::cleanInStr($postArr['scientificnameauthorship']).'"':'NULL').','.
			($tid?:'NULL').','.($postArr['taxonremarks']?'"'.Sanitizer::cleanInStr($postArr['taxonremarks']).'"':'NULL').','.
			($postArr['identifiedby']?'"'.Sanitizer::cleanInStr($postArr['identifiedby']).'"':'NULL').','.
			($postArr['dateidentified']?'"'.Sanitizer::cleanInStr($postArr['dateidentified']).'"':'NULL').','.
			($postArr['identificationreferences']?'"'.Sanitizer::cleanInStr($postArr['identificationreferences']).'"':'NULL').','.
			'"'.Sanitizer::cleanInStr($postArr['recordedby']).'",'.
			($postArr['recordnumber']?'"'.Sanitizer::cleanInStr($postArr['recordnumber']).'"':'NULL').','.
			($postArr['associatedcollectors']?'"'.Sanitizer::cleanInStr($postArr['associatedcollectors']).'"':'NULL').','.
			'"'.$postArr['eventdate'].'",'.$eventYear.','.$eventMonth.','.$eventDay.','.$startDay.','.
			($postArr['habitat']?'"'.Sanitizer::cleanInStr($postArr['habitat']).'"':'NULL').','.
			($postArr['substrate']?'"'.Sanitizer::cleanInStr($postArr['substrate']).'"':'NULL').','.
			($postArr['occurrenceremarks']?'"'.Sanitizer::cleanInStr($postArr['occurrenceremarks']).'"':'NULL').','.
			($postArr['associatedtaxa']?'"'.Sanitizer::cleanInStr($postArr['associatedtaxa']).'"':'NULL').','.
			($postArr['verbatimattributes']?'"'.Sanitizer::cleanInStr($postArr['verbatimattributes']).'"':'NULL').','.
			($postArr['reproductivecondition']?'"'.Sanitizer::cleanInStr($postArr['reproductivecondition']).'"':'NULL').','.
			(array_key_exists('cultivationstatus',$postArr)?'1':'0').','.
			($postArr['establishmentmeans']?'"'.Sanitizer::cleanInStr($postArr['establishmentmeans']).'"':'NULL').','.
			'"'.Sanitizer::cleanInStr($postArr['country']).'",'.
			($postArr['stateprovince']?'"'.Sanitizer::cleanInStr($postArr['stateprovince']).'"':'NULL').','.
			($postArr['county']?'"'.Sanitizer::cleanInStr($postArr['county']).'"':'NULL').','.
			'"'.Sanitizer::cleanInStr($postArr['locality']).'",'.$localitySecurity.','.
			$postArr['decimallatitude'].','.$postArr['decimallongitude'].','.
			($postArr['geodeticdatum']?'"'.Sanitizer::cleanInStr($postArr['geodeticdatum']).'"':'NULL').','.
			($postArr['coordinateuncertaintyinmeters']?'"'.$postArr['coordinateuncertaintyinmeters'].'"':'NULL').','.
			($postArr['georeferenceremarks']?'"'.Sanitizer::cleanInStr($postArr['georeferenceremarks']).'"':'NULL').','.
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
							'VALUES('.$newOccId.',"identification",'.$postArr['confidenceranking'].','.$GLOBALS['SYMB_UID'].')';
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
		$status = true;
		$imgManager = new ImageShared();
		$subTargetPath = $this->collMap['institutioncode'];
		if($this->collMap['collectioncode']) {
			$subTargetPath .= '_' . $this->collMap['collectioncode'];
		}
		
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
			$sql .= 'WHERE (colltype = "General Observations")';
		}
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$this->collMap['collid'] = $r->collid;
			$this->collMap['institutioncode'] = $r->institutioncode;
			$this->collMap['collectioncode'] = $r->collectioncode;
			$this->collMap['collectionname'] = Sanitizer::cleanOutStr($r->collectionname);
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
