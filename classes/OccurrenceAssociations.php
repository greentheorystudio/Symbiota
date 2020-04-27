<?php
include_once('Manager.php');
include_once('GPoint.php');

class OccurrenceAssociations extends Manager {

 	public function __construct(){
		parent::__construct(null);
 	}

	public function parseAssociatedTaxa($collid = 0): void
	{
		if(!is_numeric($collid)){
			echo '<div><b>FAIL ERROR: abort process</b></div>';
			return;
		} 
		set_time_limit(900);
		echo '<ul>';
		echo '<li>Starting to parse associated species text blocks </li>';
		flush();
		$sql = 'SELECT o.occid, o.associatedtaxa '.
			'FROM omoccurrences o LEFT JOIN omoccurassociations a ON o.occid = a.occid '.
			'WHERE (o.associatedtaxa IS NOT NULL) AND (o.associatedtaxa <> "") AND (a.occid IS NULL) ';
		if($collid && is_numeric($collid)){
			$sql .= 'AND (o.collid = '.$collid.') ';
		}
		$rs = $this->conn->query($sql);
		echo '<li>Parsing new associated species text blocks (target count: '.$rs->num_rows.')... </li>';
		flush();
		$cnt = 1;
		while($r = $rs->fetch_object()){
			if($cnt%5000 === 0) {
				echo '<li style="margin-left:10px">' . $cnt . ' occurrences parsed</li>';
			}
			$cnt++;
		}
		$rs->free();
		
		echo '<li>Populate tid field using taxa table... </li>';
		flush();
		if($collid){
			$sql2 = 'UPDATE omoccurassociations a INNER JOIN taxa t ON a.verbatimsciname = t.sciname '.
				'INNER JOIN omoccurrences o ON a.occid = o.occid '.
				'SET a.tid = t.tid '.
				'WHERE a.tid IS NULL AND (o.collid = '.$collid.') ';
		}
		else{
			$sql2 = 'UPDATE omoccurassociations a INNER JOIN taxa t ON a.verbatimsciname = t.sciname '.
				'SET a.tid = t.tid '.
				'WHERE a.tid IS NULL';
		}
		if(!$this->conn->query($sql2)){
			echo '<li style="margin-left:20px;">Unable to populate tid field using taxa table: '.$this->conn->error.'</li>';
		}

		echo '<li>Populate tid field using taxavernaculars table... </li>';
		flush();
		if($collid){
			$sql3 = 'UPDATE omoccurassociations a INNER JOIN taxavernaculars v ON a.verbatimsciname = v.vernacularname '.
				'INNER JOIN omoccurrences o ON a.occid = o.occid '.
				'SET a.tid = v.tid '.
				'WHERE (a.tid IS NULL) AND (o.collid = '.$collid.') ';
		}
		else{
			$sql3 = 'UPDATE omoccurassociations a INNER JOIN taxavernaculars v ON a.verbatimsciname = v.vernacularname '.
				'SET a.tid = v.tid '.
				'WHERE a.tid IS NULL ';
		}
		if(!$this->conn->query($sql3)){
			echo '<li style="margin-left:20px;">Unable to populate tid field using taxavernaculars table: '.$this->conn->error.'</li>';
		}
		
		echo '<li>Populate tid field by linking back to omoccurassociations table... </li>';
		flush();
		if($collid){
			$sql4 = 'UPDATE omoccurassociations a INNER JOIN omoccurassociations a2 ON a.verbatimsciname = a2.verbatimsciname '.
				'INNER JOIN omoccurrences o ON a.occid = o.occid '.
				'SET a.tid = a2.tid '.
				'WHERE (a.tid IS NULL) AND (a2.tid IS NOT NULL) AND (o.collid = '.$collid.') ';
		}
		else{
			$sql4 = 'UPDATE omoccurassociations a INNER JOIN omoccurassociations a2 ON a.verbatimsciname = a2.verbatimsciname '.
				'SET a.tid = a2.tid '.
				'WHERE a.tid IS NULL AND a2.tid IS NOT NULL ';
		}
		if(!$this->conn->query($sql4)){
			echo '<li style="margin-left:20px;">Unable to populate tid field relinking back to omoccurassociations table: '.$this->conn->error.'</li>';
		}
		
		echo '<li>Mining database for the more difficult matches... </li>';
		flush();
		if($collid){
			$sql5 = 'SELECT DISTINCT a.verbatimsciname '.
				'FROM omoccurassociations a INNER JOIN omoccurrences o ON a.occid = o.occid '.
				'WHERE (a.tid IS NULL) AND (o.collid = '.$collid.') ';
		}
		else{
			$sql5 = 'SELECT DISTINCT verbatimsciname '.
				'FROM omoccurassociations '.
				'WHERE tid IS NULL ';
		}
		$rs5 = $this->conn->query($sql5);
		while($r5 = $rs5->fetch_object()){
			$verbStr = $r5->verbatimsciname;
			$tid = $this->mineAssocSpeciesMatch($verbStr);
			if($tid){
				$sql5b = 'UPDATE omoccurassociations '.
					'SET tid = '.$tid.' '.
					'WHERE tid IS NULL AND verbatimsciname = "'.$verbStr.'"';
				if(!$this->conn->query($sql5b)){
					echo '<li style="margin-left:20px;">Unable to populate NULL tid field: '.$this->conn->error.'</li>';
				}
			}
		}
		$rs5->free();
		
		echo '<li>DONE!</li>';
		echo '</ul>';
		flush();
	}

	private function mineAssocSpeciesMatch($verbStr){
		$retTid = 0;
		if(preg_match('/^([A-Z])\.?\s([a-z]*)$/',$verbStr,$m)){
			$sql = 'SELECT tid, sciname '.
				'FROM taxa '. 
				'WHERE unitname1 LIKE "'.$m[1].'%" AND unitname2 = "'.$m[2].'" AND rankid = 220';
			//echo $sql.'; '.$verbStr;
			$rs = $this->conn->query($sql);
			if(($rs->num_rows === 1) && $r = $rs->fetch_object()) {
				$retTid = $r->tid;
			}
			$rs->free();
		}
		return $retTid;
	}

	public function getParsingStats($collid): array
	{
		$retArr = array();
		$sqlZ = 'SELECT COUNT(DISTINCT o.occid) as cnt '.
			'FROM omoccurrences o INNER JOIN omoccurassociations a ON o.occid = a.occid '.
			'WHERE (a.relationship = "associatedSpecies") ';
		if($collid){
			$sqlZ .= 'AND (o.collid = '.$collid.') ';
		}
		$rsZ = $this->conn->query($sqlZ);
		while($rZ = $rsZ->fetch_object()){
			$retArr['parsed'] = $rZ->cnt;
		}
		$rsZ->free();

		$sqlA = 'SELECT count(o.occid) as cnt '.
			'FROM omoccurrences o LEFT JOIN omoccurassociations a ON o.occid = a.occid '.
			'WHERE (o.associatedtaxa IS NOT NULL) AND (o.associatedtaxa <> "") AND (a.occid IS NULL) ';
		if($collid){
			$sqlA .= 'AND (o.collid = '.$collid.') ';
		}
		$rsA = $this->conn->query($sqlA);
		while($rA = $rsA->fetch_object()){
			$retArr['unparsed'] = $rA->cnt;
		}
		$rsA->free();

		$sqlB = 'SELECT count(a.occid) as cnt '.
			'FROM omoccurrences o INNER JOIN omoccurassociations a ON o.occid = a.occid '.
			'WHERE (a.verbatimsciname IS NOT NULL) AND (a.tid IS NULL) ';
		if($collid){
			$sqlB .= 'AND (o.collid = '.$collid.') ';
		}
		$rsB = $this->conn->query($sqlB);
		while($rB = $rsB->fetch_object()){
			$retArr['failed'] = $rB->cnt;
		}
		$rsB->free();

		$sqlC = 'SELECT count(DISTINCT o.occid) as cnt '.
			'FROM omoccurrences o INNER JOIN omoccurassociations a ON o.occid = a.occid '.
			'WHERE (a.verbatimsciname IS NOT NULL) AND (a.tid IS NULL) ';
		if($collid){
			$sqlC .= 'AND (o.collid = '.$collid.') ';
		}
		$rsC = $this->conn->query($sqlC);
		while($rC = $rsC->fetch_object()){
			$retArr['failedOccur'] = $rC->cnt;
		}
		$rsC->free();
		return $retArr;
	}

	public function getCollectionMetadata($collid): array
	{
		$retArr = array();
		if(is_numeric($collid)){
			$sql = 'SELECT institutioncode, collectioncode, collectionname, colltype, managementtype '.
				'FROM omcollections '.
				'WHERE collid = '.$collid;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['instcode'] = $r->institutioncode;
				$retArr['collcode'] = $r->collectioncode;
				$retArr['collname'] = $r->collectionname;
				$retArr['colltype'] = $r->colltype;
				$retArr['mantype'] = $r->managementtype;
			}
			$rs->free();
		}
		return $retArr;
	}
}
