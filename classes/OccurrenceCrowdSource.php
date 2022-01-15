<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceCrowdSource {

	private $conn;
	private $collid;
	private $omcsid;
	private $headArr = array('catalogNumber','family','sciname','identifiedBy','dateIdentified','recordedBy','recordNumber',
        'associatedCollectors','eventDate','verbatimEventDate','country','stateProvince','county','locality',
        'decimalLatitude','decimalLongitude','coordinateUncertaintyInMeters','verbatimCoordinates','minimumElevationInMeters',
        'maximumElevationInMeters','verbatimElevation','habitat','reproductiveCondition','substrate','occurrenceRemarks',
        'processingstatus','dateLastModified');

	public function __construct() {
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function getProjectDetails(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT CONCAT_WS(":",c.institutioncode,c.collectioncode) AS collcode, c.collectionname, '.
				'csc.omcsid, csc.instructions, csc.trainingurl '.
				'FROM omcollections c LEFT JOIN omcrowdsourcecentral csc ON c.collid = csc.collid '.
				'WHERE c.collid = '.$this->collid;
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr['name'] = $r->collectionname.' ('.$r->collcode.')';
				$retArr['instr'] = $r->instructions;
				$retArr['url'] = $r->trainingurl;
				$retArr['omcsid'] = $r->omcsid;
				$this->omcsid = $r->omcsid;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function editProject($omcsid,$instr,$url): string
	{
		$statusStr = '';
		if(is_numeric($omcsid)){
			$sql = 'UPDATE omcrowdsourcecentral '.
				'SET instructions = '.($instr?'"'.Sanitizer::cleanInStr($instr).'"':'NULL').',trainingurl = '.($url?'"'.Sanitizer::cleanInStr($url).'"':'NULL').
				' WHERE omcsid = '.$omcsid;
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR editing project.';
			}
            $this->conn->close();
		}
		return $statusStr;
	}

	private function createNewProject(): void
	{
		if($this->collid){
			$sql = 'INSERT INTO omcrowdsourcecentral(collid,instructions,trainingurl) '.
				'VALUES('.$this->collid.',NULL,NULL)';
			//echo $sql;
			if($this->conn->query($sql)){
				$this->omcsid = $this->conn->insert_id;
			}
            $this->conn->close();
		}
	}

	public function getProjectStats(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT q.reviewstatus, count(q.occid) as cnt '.
				'FROM omcrowdsourcequeue q INNER JOIN omcrowdsourcecentral c ON q.omcsid = c.omcsid '.
				'WHERE c.collid = '.$this->collid.' '.
				'GROUP BY q.reviewstatus';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->reviewstatus] = $r->cnt;
			}
			$rs->free();

			$sql = 'SELECT count(o.occid) as cnt '.
				'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
				'LEFT JOIN omcrowdsourcequeue q ON o.occid = q.occid '.
				'WHERE o.collid = '.$this->collid.' AND (o.processingstatus = "unprocessed") AND q.occid IS NULL ';
			$toAddCnt = 0;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$toAddCnt = $r->cnt;
			}
			$rs->free();
			$retArr['toadd'] = $toAddCnt;
		}
		return $retArr;
	}

	public function getProcessingStats(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT CONCAT_WS(", ", u.lastname, u.firstname) as username, u.uid, q.isvolunteer, sum(IFNULL(q.points,0)) as usersum '.
				'FROM omcrowdsourcequeue q INNER JOIN omcrowdsourcecentral c ON q.omcsid = c.omcsid '.
				'INNER JOIN users u ON q.uidprocessor = u.uid '.
				'WHERE c.collid = '.$this->collid.' '.
				'GROUP BY username, u.uid, q.isvolunteer ORDER BY usersum DESC ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$tag = ($r->isvolunteer === 1?'v':'e');
				$retArr[$tag][$r->uid]['score'] = $r->usersum;
				$retArr[$tag][$r->uid]['name'] = $r->username;
			}
			$rs->free();

			$sql = 'SELECT q.uidprocessor, q.reviewstatus, q.isvolunteer, count(q.occid) as cnt '.
				'FROM omcrowdsourcequeue q INNER JOIN omcrowdsourcecentral c ON q.omcsid = c.omcsid '.
				'WHERE c.collid = '.$this->collid.' AND q.uidprocessor IS NOT NULL '.
				'GROUP BY q.uidprocessor, q.reviewstatus, q.isvolunteer';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$tag = ($r->isvolunteer === 1?'v':'e');
				$retArr[$tag][$r->uidprocessor][$r->reviewstatus] = $r->cnt;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getTopScores($catid): array
	{
		$retArr = array();
		$sql = 'SELECT u.uid, CONCAT_WS(", ",u.lastname,u.firstname) as user, sum(q.points) AS toppoints '.
			'FROM omcrowdsourcequeue q INNER JOIN users u ON q.uidprocessor = u.uid ';
		if($catid){
			$sql .= 'INNER JOIN omcrowdsourcecentral c ON q.omcsid = c.omcsid INNER JOIN omcollcatlink cat ON c.collid = cat.collid ';
		}
		$sql .= 'WHERE q.reviewstatus = 10 AND q.points is not null AND q.isvolunteer = 1 ';
		if($catid){
			$sql .= 'AND (cat.ccpk = '.$catid.') ';
		}
		$sql .= 'GROUP BY u.firstname, u.lastname ORDER BY sum(q.points) DESC ';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$topPoints = $r->toppoints;
			if(!$topPoints) {
				$topPoints = 0;
			}
			$retArr[$topPoints] = $r->user;
			$cnt++;
			if($cnt > 10) {
				break;
			}
		}
		$rs->free();
		return $retArr;
	}

	public function getUserStats($catid): array
	{
		$retArr = array();
		$sql = 'SELECT c.collid, CONCAT_WS(":",c.institutioncode,c.collectioncode) as collcode, c.collectionname, '.
			'q.reviewstatus, q.isvolunteer, COUNT(q.occid) AS cnt, SUM(IFNULL(q.points,2)) AS points '.
			'FROM omcrowdsourcequeue q INNER JOIN omcrowdsourcecentral csc ON q.omcsid = csc.omcsid '.
			'INNER JOIN omcollections c ON csc.collid = c.collid ';
		if($catid){
			$sql .= 'INNER JOIN omcollcatlink cat ON c.collid = cat.collid WHERE (cat.ccpk = '.$catid.') ';
		}
		$sql .= 'GROUP BY c.collid,q.reviewstatus,q.uidprocessor,q.isvolunteer '.
			'HAVING (q.uidprocessor = '.$GLOBALS['SYMB_UID'].' OR q.uidprocessor IS NULL) '.
			'ORDER BY c.institutioncode,c.collectioncode,q.reviewstatus';
		//echo $sql;
		$rs = $this->conn->query($sql);
		$pPoints = 0;
		$aPoints = 0;
		$nonVolunteerCnt = 0;
		$totalCnt = 0;
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['name'] = $r->collectionname.' ('.$r->collcode.')';
			$retArr[$r->collid]['cnt'][$r->reviewstatus] = $r->cnt;
			if($r->isvolunteer) {
				$retArr[$r->collid]['points'][$r->reviewstatus] = $r->points;
			}
			if($r->reviewstatus >= 10){
				if($r->isvolunteer){
					$aPoints += $r->points;
				}
			}
			elseif($r->reviewstatus === 5){
				if($r->isvolunteer){
					$pPoints += $r->points;
				}
			}
			if($r->reviewstatus > 0){
				if($r->isvolunteer){
					$totalCnt += $r->cnt;
				}
				else{
					$nonVolunteerCnt += $r->cnt;
				}
			}
		}
		$retArr['ppoints'] = $pPoints;
		$retArr['apoints'] = $aPoints;
		$retArr['totalcnt'] = $totalCnt;
		$retArr['nonvolcnt'] = $nonVolunteerCnt;
		$rs->free();
		return $retArr;
	}

	public function addToQueue($omcsid,$family,$taxon,$country,$stateProvince,$limit): string
	{
		$statusStr = 'SUCCESS: occurrences added to queue';
		if($this->omcsid) {
            if($this->collid) {
                $sqlFrag = 'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
                    'LEFT JOIN omcrowdsourcequeue q ON o.occid = q.occid '.
                    'WHERE o.collid = '.$this->collid.' AND q.occid IS NULL AND (o.processingstatus = "unprocessed") ';
                if($family){
                    $sqlFrag .= 'AND (o.family = "'.Sanitizer::cleanInStr($family).'") ';
                }
                if($taxon){
                    $sqlFrag .= 'AND (o.sciname LIKE "'.Sanitizer::cleanInStr($taxon).'%") ';
                }
                if($country){
                    $sqlFrag .= 'AND (o.country = "'.Sanitizer::cleanInStr($country).'") ';
                }
                if($stateProvince){
                    $sqlFrag .= 'AND (o.stateprovince = "'.Sanitizer::cleanInStr($stateProvince).'") ';
                }
                $sqlCnt = 'SELECT COUNT(DISTINCT o.occid) AS cnt '.$sqlFrag;
                $rs = $this->conn->query($sqlCnt);
                if($r = $rs->fetch_object()){
                    $statusStr = $r->cnt;
                    if($statusStr > $limit) {
                        $statusStr = $limit;
                    }
                }
                $rs->free();
                if($limit){
                    $sqlFrag .= 'LIMIT '.$limit;
                }
                $sql = 'INSERT INTO omcrowdsourcequeue(occid, omcsid) '.
                    'SELECT DISTINCT o.occid, '.$omcsid.' AS csid '.$sqlFrag;
                if(!$this->conn->query($sql)){
                    $statusStr = 'ERROR adding to queue.';
                    $statusStr .= '; SQL: '.$sql;
                }
                $this->conn->close();
            }
            else {
                $statusStr = 'ERROR adding to queue, collid is null';
            }
        }
		else {
            $statusStr = 'ERROR adding to queue, omcsid is null';
		}
		return $statusStr;
	}

	public function deleteQueue(): string
	{
		$statusStr = 'SUCCESS: all occurrences removed from queue';
		if($this->omcsid) {
            if($this->collid) {
                $sql = 'DELETE FROM omcrowdsourcequeue '.
                    'WHERE omcsid = '.$this->omcsid.' AND uidprocessor IS NULL and reviewstatus = 0 ';
                if(!$this->conn->query($sql)){
                    $statusStr = 'ERROR removing occurrences from queue.';
                }
                $this->conn->close();
            }
            else {
                $statusStr = 'ERROR adding to queue, collid is null';
            }
        }
		else {
            $statusStr = 'ERROR adding to queue, omcsid is null';
		}
		return $statusStr;
	}

	public function getQueueLimitCriteria(): array
	{
		$country = array();
		$state = array();
		$sql = 'SELECT DISTINCT o.country, o.stateprovince '.
			'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
			'LEFT JOIN omcrowdsourcequeue q ON o.occid = q.occid '.
			'WHERE o.collid = '.$this->collid.' AND (o.processingstatus = "unprocessed") AND q.occid IS NULL ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->country) {
				$country[$r->country] = '';
			}
			if($r->stateprovince) {
				$state[$r->stateprovince] = '';
			}
		}
		$rs->free();
		$retArr = array();
		$retArr['country'] = array_keys($country);
		$retArr['state'] = array_keys($state);
		$family = array();
		$sciname = array();
		$sql = 'SELECT DISTINCT o.family, o.sciname, t.unitname1 '.
			'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
			'LEFT JOIN omcrowdsourcequeue q ON o.occid = q.occid '.
			'LEFT JOIN taxa t ON o.tidinterpreted = t.tid '.
			'WHERE o.collid = '.$this->collid.' AND (o.processingstatus = "unprocessed") AND q.occid IS NULL ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->family) {
				$family[$r->family] = '';
			}
			if($r->sciname){
				$sciname[$r->sciname] = '';
				if($r->unitname1 && !array_key_exists($r->unitname1,$sciname)) {
					$sciname[$r->unitname1] = '';
				}
			}
		}
		$rs->free();
		$retArr['family'] = array_keys($family);
		$retArr['taxa'] = array_keys($sciname);
		return $retArr;
	}

	public function getReviewArr($startIndex,$limit,$uid,$rStatus): array
	{
		$retArr = array();
		if($this->collid || $uid){
			$sql = 'FROM omcrowdsourcequeue q INNER JOIN omcrowdsourcecentral csc ON q.omcsid = csc.omcsid '.
				'INNER JOIN omoccurrences o ON q.occid = o.occid '.
				'WHERE q.reviewstatus IN('.$rStatus.') ';
			if($this->collid){
				$sql .= 'AND csc.collid = '.$this->collid.' ';
			}
			if($uid){
				$sql .= 'AND (q.uidprocessor = '.$uid.') ';
			}
			$sqlRec = 'SELECT o.occid, '.implode(', ',$this->headArr).', q.uidprocessor, q.reviewstatus, q.points, q.notes '.
				$sql.'ORDER BY o.datelastmodified DESC LIMIT '.$startIndex.','.$limit;
			//echo $sqlRec;
			$rs = $this->conn->query($sqlRec);
			$headerArr = array();
			while($r = $rs->fetch_assoc()){
				$retArr[$r['occid']] = $r;
				foreach($r as $field => $value){
					if($value && !in_array($field, $headerArr, true)) {
						$headerArr[] = $field;
					}
				}
			}
			$rs->free();
			$this->headArr = array_intersect($this->headArr,$headerArr);

			//Get count
			$sqlCnt = 'SELECT COUNT(o.occid) AS cnt '.$sql;
			//echo $sqlCnt;
			$rs = $this->conn->query($sqlCnt);
			if($row = $rs->fetch_object()){
				$retArr['totalcnt'] = $row->cnt;
			}
		}
		else{
			echo 'ERROR: both collid and user id are null';
		}
		return $retArr;
	}

	public function submitReviews($postArr): string
	{
		$statusStr = '';
		$occidArr = $postArr['occid'];
		if($occidArr){
			$successArr = array();
			foreach($occidArr as $occid){
				$points = $postArr['p-'.$occid];
				$comments = Sanitizer::cleanInStr($postArr['c-'.$occid]);
				$sql = 'UPDATE omcrowdsourcequeue '.
					'SET points = '.$points.',notes = '.($comments?'"'.$comments.'"':'NULL').',reviewstatus = 10 '.
					'WHERE occid = '.$occid;
				if($this->conn->query($sql)){
					$successArr[] = $occid;
				}
				else{
					$statusStr = 'ERROR submitting reviews.';
				}
			}
			if($successArr && isset($postArr['updateProcessingStatus']) && $postArr['updateProcessingStatus']){
				$sql2 = 'UPDATE omoccurrences SET processingstatus = "reviewed" WHERE occid IN('.implode(',',$successArr).')';
                $this->conn->query($sql2);
			}
            $this->conn->close();
		}
		return $statusStr;
	}

	public function setCollid($id): void
	{
		if($id && is_numeric($id)){
			$this->collid = $id;
			if(!$this->omcsid){
				$sql = 'SELECT omcsid FROM omcrowdsourcecentral WHERE collid = '.$this->collid;
				//echo $sql;
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$this->omcsid = $r->omcsid;
				}
				$rs->free();
			}
			if(!$this->omcsid){
				$this->createNewProject();
			}
		}
	}

	public function getOmcsid(){
		return $this->omcsid;
	}

	public function getEditorList(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT DISTINCT u.uid, CONCAT_WS(", ", u.lastname, u.firstname) as user '.
				'FROM omcrowdsourcequeue q INNER JOIN users u ON q.uidprocessor = u.uid '.
				'INNER JOIN omcrowdsourcecentral c ON q.omcsid = c.omcsid '.
				'WHERE c.collid = '.$this->collid.' '.
				'ORDER BY u.lastname, u.firstname';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->uid] = $r->user;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getHeaderArr(): array
	{
    	return $this->headArr;
    }
}
