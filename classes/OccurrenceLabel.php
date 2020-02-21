<?php
include_once($SERVER_ROOT.'/classes/DbConnection.php');

class OccurrenceLabel{

	private $conn;
	private $collid;
	private $collArr = array();
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

	public function queryOccurrences($postArr): array
	{
		global $USER_RIGHTS, $IS_ADMIN, $SYMB_UID;
		$canReadRareSpp = false;
		if($IS_ADMIN || array_key_exists('CollAdmin', $USER_RIGHTS) || array_key_exists('RareSppAdmin', $USER_RIGHTS) || array_key_exists('RareSppReadAll', $USER_RIGHTS)){
			$canReadRareSpp = true;
		}
		elseif((array_key_exists('CollEditor', $USER_RIGHTS) && in_array($this->collid, $USER_RIGHTS['CollEditor'], true)) || (array_key_exists('RareSppReader', $USER_RIGHTS) && in_array($this->collid, $USER_RIGHTS['RareSppReader'], true))){
			$canReadRareSpp = true;
		}
		$retArr = array();
		if($this->collid){
			$sqlWhere = '';
			$sqlOrderBy = '';
			if($postArr['taxa']){
				$sqlWhere .= 'AND (o.sciname = "'.$this->cleanInStr($postArr['taxa']).'") ';
			}
			if($postArr['labelproject']){
				$sqlWhere .= 'AND (o.labelproject = "'.$this->cleanInStr($postArr['labelproject']).'") ';
			}
			if($postArr['recordenteredby']){
				$sqlWhere .= 'AND (o.recordenteredby = "'.$this->cleanInStr($postArr['recordenteredby']).'") ';
			}
			$date1 = $this->cleanInStr($postArr['date1']);
			$date2 = $this->cleanInStr($postArr['date2']);
			if(!$date1 && $date2){
				$date1 = $date2;
				$date2 = '';
			}
			$dateTarget = $this->cleanInStr($postArr['datetarget']);
			if($date1){
				if($date2){
					$sqlWhere .= 'AND (DATE('.$dateTarget.') BETWEEN "'.$date1.'" AND "'.$date2.'") ';
				}
				else{
					$sqlWhere .= 'AND (DATE('.$dateTarget.') = "'.$date1.'") ';
				}
			}
			if($postArr['recordnumber']){
				$rnArr = explode(',',$this->cleanInStr($postArr['recordnumber']));
				$rnBetweenFrag = array();
				$rnInFrag = array();
				foreach($rnArr as $v){
					$v = trim($v);
					if($p = strpos($v,' - ')){
						$term1 = trim(substr($v,0,$p));
						$term2 = trim(substr($v,$p+3));
						if(is_numeric($term1) && is_numeric($term2)){
							$rnBetweenFrag[] = '(o.recordnumber BETWEEN '.$term1.' AND '.$term2.')';
						}
						else{
							$catTerm = 'o.recordnumber BETWEEN "'.$term1.'" AND "'.$term2.'"';
							if(strlen($term1) === strlen($term2)) {
								$catTerm .= ' AND length(o.recordnumber) = ' . strlen($term2);
							}
							$rnBetweenFrag[] = '('.$catTerm.')';
						}
					}
					else{
						$rnInFrag[] = $v;
					}
				}
				$rnWhere = '';
				if($rnBetweenFrag){
					$rnWhere .= 'OR '.implode(' OR ',$rnBetweenFrag);
				}
				if($rnInFrag){
					$rnWhere .= 'OR (o.recordnumber IN("'.implode('","',$rnInFrag).'")) ';
				}
				$sqlWhere .= 'AND ('.substr($rnWhere,3).') ';
			}
			if($postArr['recordedby']){
				$recordedBy = $this->cleanInStr($postArr['recordedby']);
				if(strlen($recordedBy) < 4 || strtolower($recordedBy) === 'best'){
					$sqlWhere .= 'AND (o.recordedby LIKE "%'.$recordedBy.'%") ';
				}
				else{
					$sqlWhere .= 'AND (MATCH(f.recordedby) AGAINST("'.$recordedBy.'")) ';
				}
			}
			if($postArr['identifier']){
				$iArr = explode(',',$this->cleanInStr($postArr['identifier']));
				$iBetweenFrag = array();
				$iInFrag = array();
				foreach($iArr as $v){
					$v = trim($v);
					if($p = strpos($v,' - ')){
						$term1 = trim(substr($v,0,$p));
						$term2 = trim(substr($v,$p+3));
						if(is_numeric($term1) && is_numeric($term2)){
							$iBetweenFrag[] = '(o.catalogNumber BETWEEN '.$term1.' AND '.$term2.')';
						}
						else{
							$catTerm = 'o.catalogNumber BETWEEN "'.$term1.'" AND "'.$term2.'"';
							if(strlen($term1) === strlen($term2)) {
								$catTerm .= ' AND length(o.catalogNumber) = ' . strlen($term2);
							}
							$iBetweenFrag[] = '('.$catTerm.')';
						}
					}
					else{
						$iInFrag[] = $v;
					}
				}
				$iWhere = '';
				if($iBetweenFrag){
					$iWhere .= 'OR '.implode(' OR ',$iBetweenFrag);
				}
				if($iInFrag){
					$iWhere .= 'OR (o.catalogNumber IN("'.implode('","',$iInFrag).'")) ';
				}
				$sqlWhere .= 'AND ('.substr($iWhere,3).') ';
			}
			if($this->collArr['colltype'] === 'General Observations'){
				$sqlWhere .= 'AND (o.collid = '.$this->collid.') ';
				if(!array_key_exists('extendedsearch', $postArr)) {
					$sqlWhere .= ' AND (o.observeruid = ' . $SYMB_UID . ') ';
				}
			}
			elseif(!array_key_exists('extendedsearch', $postArr)){
				$sqlWhere .= 'AND (o.collid = '.$this->collid.') ';
			}
			$sql = 'SELECT o.occid, o.collid, IFNULL(o.duplicatequantity,1) AS q, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS collector, o.observeruid, '.
				'o.family, o.sciname, CONCAT_WS("; ",o.country, o.stateProvince, o.county, o.locality) AS locality, IFNULL(o.localitySecurity,0) AS localitySecurity '.
				'FROM omoccurrences o ';
			if(strpos($sqlWhere,'MATCH(f.recordedby)') || strpos($sqlWhere,'MATCH(f.locality)')){
				$sql.= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
			}
			if($sqlWhere) {
				$sql .= 'WHERE ' . substr($sqlWhere, 4);
			}
			$sql .= ' LIMIT 400';
			//echo '<div>'.$sql.'</div>'; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$localitySecurity = $r->localitySecurity;
				if(!$localitySecurity || $canReadRareSpp || ($r->observeruid === $SYMB_UID)){
					$occId = $r->occid;
					$retArr[$occId]['collid'] = $r->collid;
					$retArr[$occId]['q'] = $r->q;
					$retArr[$occId]['c'] = $r->collector;
					$retArr[$occId]['s'] = $r->sciname;
					$retArr[$occId]['l'] = $r->locality;
					$retArr[$occId]['uid'] = $r->observeruid;
				}
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getLabelArray($occidArr, $speciesAuthors): array
	{
		global $SYMB_UID;
		$retArr = array();
		if($occidArr){
			$authorArr = array();
			$sqlWhere = 'WHERE (o.occid IN('.implode(',',$occidArr).')) ';
			if($this->collArr['colltype'] === 'General Observations') {
				$sqlWhere .= 'AND (o.observeruid = ' . $SYMB_UID . ') ';
			}
			$sql1 = 'SELECT o.occid, t2.author '.
				'FROM taxa t INNER JOIN omoccurrences o ON t.tid = o.tidinterpreted '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'INNER JOIN taxa t2 ON ts.parenttid = t2.tid '.
				$sqlWhere.' AND t.rankid > 220 AND ts.taxauthid = 1 ';
			if(!$speciesAuthors){
				$sql1 .= 'AND t.unitname2 = t.unitname3 ';
			}
			//echo $sql1; exit;
			if($rs1 = $this->conn->query($sql1)){
				while($row1 = $rs1->fetch_object()){
					$authorArr[$row1->occid] = $row1->author;
				}
				$rs1->free();
			}
				
			$sql2 = 'SELECT o.occid, o.collid, o.catalognumber, o.othercatalognumbers, '.
				'o.family, o.sciname AS scientificname, o.genus, o.specificepithet, o.taxonrank, o.infraspecificepithet, '.
				'o.scientificnameauthorship, "" AS parentauthor, o.identifiedby, o.dateidentified, o.identificationreferences, '.
				'o.identificationremarks, o.taxonremarks, o.identificationqualifier, o.typestatus, o.recordedby, o.recordnumber, o.associatedcollectors, '.
				'DATE_FORMAT(o.eventdate,"%e %M %Y") AS eventdate, o.year, o.month, o.day, DATE_FORMAT(o.eventdate,"%M") AS monthname, '.
				'o.verbatimeventdate, o.habitat, o.substrate, o.occurrenceremarks, o.associatedtaxa, o.verbatimattributes, '.
				'o.reproductivecondition, o.cultivationstatus, o.establishmentmeans, o.country, '.
				'o.stateprovince, o.county, o.municipality, o.locality, o.decimallatitude, o.decimallongitude, '.
				'o.geodeticdatum, o.coordinateuncertaintyinmeters, o.verbatimcoordinates, '.
				'o.minimumelevationinmeters, o.maximumelevationinmeters, '.
				'o.verbatimelevation, o.disposition, o.duplicatequantity, o.datelastmodified '.
				'FROM omoccurrences o '.$sqlWhere;
			//echo 'SQL: '.$sql2;
			if($rs2 = $this->conn->query($sql2)){
				while($row2 = $rs2->fetch_assoc()){
					$row2 = array_change_key_case($row2);
					if(array_key_exists($row2['occid'],$authorArr)){
						$row2['parentauthor'] = $authorArr[$row2['occid']];
					}
					$retArr[$row2['occid']] = $row2;
				}
				$rs2->free();
			}
		}
		return $retArr;
	}
	
	public function getAnnoArray($detidArr, $speciesAuthors): array
	{
		$retArr = array();
		if($detidArr){
			$authorArr = array();
			$sqlWhere = 'WHERE (d.detid IN('.implode(',',$detidArr).')) ';
			$sql1 = 'SELECT d.detid, t2.author '.
				'FROM (taxa t INNER JOIN omoccurrences o ON t.tid = o.tidinterpreted) '.
				'INNER JOIN omoccurdeterminations d ON o.occid = d.occid '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'INNER JOIN taxa t2 ON ts.parenttid = t2.tid '.
				$sqlWhere.' AND t.rankid > 220 AND ts.taxauthid = 1 ';
			if(!$speciesAuthors){
				$sql1 .= 'AND t.unitname2 = t.unitname3 ';
			}
			//echo $sql1; exit;
			if($rs1 = $this->conn->query($sql1)){
				while($row1 = $rs1->fetch_object()){
					$authorArr[$row1->detid] = $row1->author;
				}
				$rs1->free();
			}
				
			$sql2 = 'SELECT d.detid, d.identifiedBy, d.dateIdentified, d.sciname, d.scientificNameAuthorship, '.
				'd.identificationQualifier, d.identificationReferences, d.identificationRemarks '.
				'FROM omoccurdeterminations d '.$sqlWhere;
			//echo 'SQL: '.$sql2;
			if($rs2 = $this->conn->query($sql2)){
				while($row2 = $rs2->fetch_assoc()){
					$row2 = array_change_key_case($row2);
					if(array_key_exists($row2['detid'],$authorArr)){
						$row2['parentauthor'] = $authorArr[$row2['detid']];
					}
					$retArr[$row2['detid']] = $row2;
				}
				$rs2->free();
			}
		}
		return $retArr;
	}
	
	public function clearAnnoQueue($detidArr): string
	{
		$statusStr = '';
		if($detidArr){
			$sql = 'UPDATE omoccurdeterminations '.
				'SET printqueue = NULL '.
				'WHERE (detid IN('.implode(',',$detidArr).')) ';
			//echo $sql; exit;
			if($this->conn->query($sql)){
				$statusStr = 'Success!';
			}
		}
		return $statusStr;
	}

	public function getLabelProjects(): array
	{
		global $SYMB_UID;
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT DISTINCT labelproject, observeruid '.
				'FROM omoccurrences '.
				'WHERE labelproject IS NOT NULL AND collid = '.$this->collid.' ';
			if($this->collArr['colltype'] === 'General Observations') {
				$sql .= 'AND (observeruid = ' . $SYMB_UID . ') ';
			}
			$sql .= 'ORDER BY labelproject';
			$rs = $this->conn->query($sql);
			$altArr = array();
			while($r = $rs->fetch_object()){
				if($SYMB_UID === $r->observeruid){
					$retArr[] = $r->labelproject;
				}
				else{
					$altArr[] = $r->labelproject;
				}
			}
			$rs->free();
			if($altArr){
				if($retArr) {
					$retArr[] = '------------------';
				}
				$retArr = array_merge($retArr,$altArr);
			}
		}
		return $retArr;
	}

	public function getAnnoQueue(): array
	{
		global $SYMB_UID;
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT o.occid, d.detid, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS collector, '.
				'CONCAT_WS(" ",d.identificationQualifier,d.sciname) AS sciname, '.
				'CONCAT_WS(", ",d.identifiedBy,d.dateIdentified,d.identificationRemarks,d.identificationReferences) AS determination '.
				'FROM omoccurrences o INNER JOIN omoccurdeterminations d ON o.occid = d.occid '.
				'WHERE (o.collid = '.$this->collid.') AND (d.printqueue = 1) ';
			if($this->collArr['colltype'] === 'General Observations'){
				$sql .= ' AND (o.observeruid = '.$SYMB_UID.') ';
			}
			$sql .= 'LIMIT 400 ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->detid]['occid'] = $r->occid;
				$retArr[$r->detid]['detid'] = $r->detid;
				$retArr[$r->detid]['collector'] = $r->collector;
				$retArr[$r->detid]['sciname'] = $r->sciname;
				$retArr[$r->detid]['determination'] = $r->determination;
			}
			$rs->free();
		}
		return $retArr;
	}
	
	public function exportCsvFile($postArr, $speciesAuthors): void
	{
		global $CHARSET;
		$occidArr = $postArr['occid'];
		if($occidArr){
			$labelArr = $this->getLabelArray($occidArr, $speciesAuthors);
			if($labelArr){
				$fileName = 'labeloutput_'.time(). '.csv';
				header('Content-Description: Symbiota Label Output File');
				header ('Content-Type: text/csv');
				header ('Content-Disposition: attachment; filename="'.$fileName.'"'); 
				header('Content-Transfer-Encoding: '.strtoupper($CHARSET));
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				
				$fh = fopen('php://output', 'wb');
				$headerArr = array('occid', 'catalogNumber', 'otherCatalogNumbers', 'family', 'scientificName', 'genus', 'specificEpithet',
					'taxonRank', 'infraSpecificEpithet', 'scientificNameAuthorship', 'parentAuthor', 'identifiedBy',
					'dateIdentified', 'identificationReferences', 'identificationRemarks', 'taxonRemarks', 'identificationQualifier',
					'typeStatus', 'recordedBy', 'recordNumber', 'associatedCollectors', 'eventDate', 'year', 'month', 'day', 'monthName',
					'verbatimEventDate', 'habitat', 'substrate', 'occurrenceRemarks', 'associatedTaxa', 'verbatimAttributes',
					'reproductiveCondition', 'establishmentMeans', 'country',
					'stateProvince', 'county', 'municipality', 'locality', 'decimalLatitude', 'decimalLongitude',
					'geodeticDatum', 'coordinateUncertaintyInMeters', 'verbatimCoordinates',
					'minimumElevationInMeters', 'maximumElevationInMeters', 'verbatimElevation', 'disposition');

				fputcsv($fh,$headerArr);
				$headerLcArr = array();
				foreach($headerArr as $k => $v){
					$headerLcArr[strtolower($v)] = $k;
				}
				foreach($labelArr as $occid => $occArr){
					$dupCnt = $postArr['q-'.$occid];
					for($i = 0;$i < $dupCnt;$i++){
						fputcsv($fh,array_intersect_key($occArr,$headerLcArr));
					}
				}
				fclose($fh);
			}
			else{
				echo "Recordset is empty.\n";
			}
		}
	}

	public function setCollid($collid): void
	{
		if(is_numeric($collid)){
			$this->collid = $collid;
			$this->setCollMetadata();
		}
	}
	
	public function getCollName(): string
	{
		return $this->collArr['collname'].' ('.$this->collArr['instcode'].($this->collArr['collcode']?':'.$this->collArr['collcode']:'').')';
	}
	
	public function getAnnoCollName(): string
	{
		return $this->collArr['collname'].' ('.$this->collArr['instcode'].')';
	}

	public function getMetaDataTerm($key){
		if($this->collArr && array_key_exists($key,$this->collArr)){
			return $this->collArr[$key];
		}
		return false;
	}

	private function setCollMetadata(): void
	{
		if($this->collid){
			$sql = 'SELECT institutioncode, collectioncode, collectionname, colltype '.
				'FROM omcollections WHERE collid = '.$this->collid;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$this->collArr['instcode'] = $r->institutioncode;
					$this->collArr['collcode'] = $r->collectioncode;
					$this->collArr['collname'] = $r->collectionname;
					$this->collArr['colltype'] = $r->colltype;
				}
				$rs->free();
			}
		}
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
