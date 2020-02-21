<?php
include_once('DbConnection.php');

class ChecklistVoucherAdmin {

	protected $conn;
	protected $clid;
	protected $childClidArr = array();
	private $clName;
	private $queryVariablesArr = array();
	private $missingTaxaCount = 0;
	private $closeConnOnDestroy = true;

	public function __construct($con = null) {
		if($con) {
			$this->conn = $con;
			$this->closeConnOnDestroy = false;
		}
		else{
			$connection = new DbConnection();
			$this->conn = $connection->getConnection();
		}
	}

	public function __destruct(){
 		if($this->closeConnOnDestroy && !($this->conn === false)) {
			$this->conn->close();
		}
	}

	public function setClid($clid): void
	{
		if(is_numeric($clid)) {
			$this->clid = $clid;
		}
	}

	public function setCollectionVariables(): void
	{
		if($this->clid){
			$sql = 'SELECT name, dynamicsql FROM fmchecklists WHERE (clid = '.$this->clid.')';
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$this->clName = $this->cleanOutStr($row->name);
				$sqlFrag = $row->dynamicsql;
				$varArr = json_decode($sqlFrag,true);
				if(json_last_error() !== JSON_ERROR_NONE){
					$varArr = $this->parseSqlFrag($sqlFrag);
					$this->saveQueryVariables($varArr);
				}
				$this->queryVariablesArr = $varArr;
			}
			else{
				$this->clName = 'Unknown';
			}
			$result->free();
			$sqlChildBase = 'SELECT clidchild FROM fmchklstchildren WHERE clid IN(';
			$sqlChild = $sqlChildBase.$this->clid.')';
			do{
				$childStr = '';
				$rsChild = $this->conn->query($sqlChild);
				while($rChild = $rsChild->fetch_object()){
					$this->childClidArr[] = $rChild->clidchild;
					$childStr .= ','.$rChild->clidchild;
				}
				$sqlChild = $sqlChildBase.substr($childStr,1).')';
			}
			while($childStr);
		}
	}

	public function getClName(){
		return $this->clName;
	}

	public function saveQueryVariables($postArr): void
	{
		$fieldArr = array('country','state','county','locality','taxon','collid','recordedby',
			'latnorth','latsouth','lngeast','lngwest','latlngor','excludecult','onlycoord');
		$jsonArr = array();
		foreach($fieldArr as $fieldName){
			if(isset($postArr[$fieldName]) && $postArr[$fieldName]) {
				$jsonArr[$fieldName] = $postArr[$fieldName];
			}
		}
		$sql = 'UPDATE fmchecklists c SET c.dynamicsql = '.($jsonArr?'"'.$this->cleanInStr(json_encode($jsonArr)).'"':'NULL').' WHERE (c.clid = '.$this->clid.')';
		//echo $sql; exit;
		$this->conn->query($sql);
	}

	private function parseSqlFrag($sqlFrag): array
	{
		$retArr = array();
		if($sqlFrag){
			if(preg_match('/country = "([^"]+)"/',$sqlFrag,$m)){
				$retArr['country'] = $m[1];
			}
			if(preg_match('/stateprovince = "([^"]+)"/',$sqlFrag,$m)){
				$retArr['state'] = $m[1];
			}
			if(preg_match('/county LIKE "([^%"]+)%"/',$sqlFrag,$m)){
				$retArr['county'] = trim($m[1],' %');
			}
			if(preg_match('/locality LIKE "%([^%"]+)%"/',$sqlFrag,$m)){
				$retArr['locality'] = trim($m[1],' %');
			}
			if(preg_match('/parenttid = (\d+)\)/',$sqlFrag,$m)){
				$retArr['taxon'] = $this->getSciname($m[1]);
			}
			if(preg_match_all('/AGAINST\("([^()"]+)"\)/',$sqlFrag,$m)){
				$retArr['recordedby'] = implode(',',$m[1]);
			}
			if(preg_match('/decimallatitude BETWEEN ([-.\d]+) AND ([-.\d]+)\D+/',$sqlFrag,$m)){
				$retArr['latsouth'] = $m[1];
				$retArr['latnorth'] = $m[2];
			}
			if(preg_match('/decimallongitude BETWEEN ([-.\d]+) AND ([-.\d]+)\D+/',$sqlFrag,$m)){
				$retArr['lngwest'] = $m[1];
				$retArr['lngeast'] = $m[2];
			}
			if(preg_match('/collid = (\d+)\D/',$sqlFrag,$m)){
				$retArr['collid'] = $m[1];
			}
			if(preg_match('/ OR \(\(o.decimallatitude/',$sqlFrag) || preg_match('/ OR \(\(o.decimallongitude/',$sqlFrag)){
				$retArr['latlngor'] = 1;
			}
			if(false !== strpos($sqlFrag, 'cultivationStatus')){
				$retArr['excludecult'] = 1;
			}
			if(false !== strpos($sqlFrag, 'decimallatitude')){
				$retArr['onlycoord'] = 1;
			}
		}
		return $retArr;
	}

	public function getSqlFrag(): string
	{
		$sqlFrag = '';
		if(isset($this->queryVariablesArr['country']) && $this->queryVariablesArr['country']){
			$countryStr = str_replace(';',',',$this->cleanInStr($this->queryVariablesArr['country']));
			$sqlFrag = 'AND (o.country IN("'.$countryStr.'")) ';
		}
		if(isset($this->queryVariablesArr['state']) && $this->queryVariablesArr['state']){
			$stateStr = str_replace(';',',',$this->cleanInStr($this->queryVariablesArr['state']));
			$sqlFrag .= 'AND (o.stateprovince = "'.$stateStr.'") ';
		}
		if(isset($this->queryVariablesArr['county']) && $this->queryVariablesArr['county']){
			$countyStr = str_replace(';',',',$this->queryVariablesArr['county']);
			$cArr = explode(',', $countyStr);
			$cStr = '';
			foreach($cArr as $str){
				$cStr .= 'OR (o.county LIKE "'.$this->cleanInStr($str).'%") ';
			}
			$sqlFrag .= 'AND ('.substr($cStr, 2).') ';
		}
		if(isset($this->queryVariablesArr['locality']) && $this->queryVariablesArr['locality']){
			$localityStr = str_replace(';',',',$this->queryVariablesArr['locality']);
			$locArr = explode(',', $localityStr);
			$locStr = '';
			foreach($locArr as $str){
				$str = $this->cleanInStr($str);
				if(strlen($str) > 4){
					$locStr .= 'OR (MATCH(f.locality) AGAINST("'.$str.'")) ';
				}
				else{
					$locStr .= 'OR (o.locality LIKE "%'.$str.'%") ';
				}
			}
			$sqlFrag .= 'AND ('.substr($locStr, 2).') ';
		}
		if(isset($this->queryVariablesArr['taxon']) && $this->queryVariablesArr['taxon']){
			$tStr = $this->cleanInStr($this->queryVariablesArr['taxon']);
			$tidPar = $this->getTid($tStr);
			if($tidPar){
				$sqlFrag .= 'AND (o.tidinterpreted IN (SELECT tid FROM taxaenumtree WHERE taxauthid = 1 AND parenttid = '.$tidPar.')) ';
			}
		}
		$llStr = '';
		if(isset($this->queryVariablesArr['latnorth'], $this->queryVariablesArr['latsouth']) && is_numeric($this->queryVariablesArr['latnorth']) && is_numeric($this->queryVariablesArr['latsouth'])){
			$llStr .= 'AND (o.decimallatitude BETWEEN '.$this->queryVariablesArr['latsouth'].' AND '.$this->queryVariablesArr['latnorth'].') ';
		}
		if(isset($this->queryVariablesArr['lngwest'], $this->queryVariablesArr['lngeast']) && is_numeric($this->queryVariablesArr['lngwest']) && is_numeric($this->queryVariablesArr['lngeast'])){
			$llStr .= 'AND (o.decimallongitude BETWEEN '.$this->queryVariablesArr['lngwest'].' AND '.$this->queryVariablesArr['lngeast'].') ';
		}
		if($llStr){
			if(array_key_exists('latlngor',$this->queryVariablesArr)) {
				$llStr = 'OR (' . trim(substr($llStr, 3)) . ') ';
			}
			$sqlFrag .= $llStr;
		}
		if(!$llStr && isset($this->queryVariablesArr['onlycoord']) && $this->queryVariablesArr['onlycoord']){
			$sqlFrag .= 'AND (o.decimallatitude IS NOT NULL) ';
		}
		if(isset($this->queryVariablesArr['excludecult']) && $this->queryVariablesArr['excludecult']){
			$sqlFrag .= 'AND (o.cultivationStatus = 0 OR o.cultivationStatus IS NULL) ';
		}
		if(isset($this->queryVariablesArr['collid']) && is_numeric($this->queryVariablesArr['collid'])){
			$sqlFrag .= 'AND (o.collid = '.$this->queryVariablesArr['collid'].') ';
		}

		if(isset($this->queryVariablesArr['recordedby']) && $this->queryVariablesArr['recordedby']){
			$collStr = str_replace(',', ';', $this->queryVariablesArr['recordedby']);
			$collArr = explode(';',$collStr);
			$tempArr = array();
			foreach($collArr as $str => $postArr){
				if(strlen($str) < 4 || strtolower($str) === 'best'){
					$tempArr[] = '(o.recordedby LIKE "%'.$this->cleanInStr($postArr['recordedby']).'%")';
				}
				else{
					$tempArr[] = '(MATCH(f.recordedby) AGAINST("'.$this->cleanInStr($str).'"))';
				}
			}
			$sqlFrag .= 'AND ('.implode(' OR ', $tempArr).') ';
		}

		if($sqlFrag) {
			$sqlFrag = trim(substr($sqlFrag, 3));
		}
		return $sqlFrag;
	}

	public function deleteQueryVariables(): string
	{
		$statusStr = '';
		if($this->conn->query('UPDATE fmchecklists c SET c.dynamicsql = NULL WHERE (c.clid = '.$this->clid.')')){
			unset($this->queryVariablesArr);
			$this->queryVariablesArr = array();
		}
		else{
			$statusStr = 'ERROR: '.$this->conn->error;
		}
		return $statusStr;
	}

	public function getNonVoucheredCnt(): int
	{
		$uvCnt = 0;
		$sql = 'SELECT count(t.tid) AS uvcnt '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'INNER JOIN fmchklsttaxalink ctl ON t.tid = ctl.tid '.
			'LEFT JOIN fmvouchers v ON ctl.clid = v.clid AND ctl.tid = v.tid '.
			'WHERE v.clid IS NULL AND (ctl.clid = '.$this->clid.') AND ts.taxauthid = 1 ';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$uvCnt = $row->uvcnt;
		}
		$rs->free();
		return $uvCnt;
	}

	public function getNonVoucheredTaxa($startLimit,$limit = 100): array
	{
		$retArr = array();
		$sql = 'SELECT t.tid, ts.family, t.sciname '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'INNER JOIN fmchklsttaxalink ctl ON t.tid = ctl.tid '.
			'LEFT JOIN fmvouchers v ON ctl.clid = v.clid AND ctl.tid = v.tid '.
			'WHERE v.clid IS NULL AND (ctl.clid = '.$this->clid.') AND ts.taxauthid = 1 '.
			'ORDER BY ts.family, t.sciname '.
			'LIMIT '.($startLimit?$startLimit.',':'').$limit;
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->family][$row->tid] = $this->cleanOutStr($row->sciname);
		}
		$rs->free();
		return $retArr;
	}

	public function getNewVouchers($startLimit = 500,$includeAll = 1): array
	{
		$retArr = array();
		if($sqlFrag = $this->getSqlFrag()){
			$clidStr = $this->clid;
			if($this->childClidArr){
				$clidStr .= ','.implode(',',$this->childClidArr);
			}
			if($includeAll === 1 || $includeAll === 2){
				$sql = 'SELECT DISTINCT cl.tid AS cltid, t.sciname AS clsciname, o.occid, '.
					'IFNULL(CONCAT(c.institutioncode,"-",c.collectioncode,"-",o.catalognumber),"[no catalog number]") AS collcode, '.
					'o.tidinterpreted, o.sciname, o.recordedby, o.recordnumber, o.eventdate, '.
					'CONCAT_WS("; ",o.country, o.stateprovince, o.county, o.locality) as locality '.
					'FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid '.
					'INNER JOIN taxstatus ts ON o.tidinterpreted = ts.tid '.
					'INNER JOIN fmchklsttaxalink cl ON ts.tidaccepted = cl.tid '.
					'INNER JOIN taxa t ON cl.tid = t.tid ';
				if(strpos($sqlFrag,'MATCH(f.recordedby)') || strpos($sqlFrag,'MATCH(f.locality)')) {
					$sql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
				}
				$sql .= 'WHERE ('.$sqlFrag.') AND (cl.clid = '.$this->clid.') AND (ts.taxauthid = 1) ';
				if($includeAll === 1){
					$sql .= 'AND cl.tid NOT IN(SELECT tid FROM fmvouchers WHERE clid IN('.$clidStr.')) ';
				}
				elseif($includeAll === 2){
					$sql .= 'AND o.occid NOT IN(SELECT occid FROM fmvouchers WHERE clid IN('.$clidStr.')) ';
				}
				$sql .= 'ORDER BY ts.family, o.sciname LIMIT '.$startLimit.', 500';
				//echo '<div>'.$sql.'</div>';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retArr[$r->cltid][$r->occid]['tid'] = $r->tidinterpreted;
					$sciName = $r->clsciname;
					if($r->clsciname !== $r->sciname) {
						$sciName .= '<br/>spec id: ' . $r->sciname;
					}
					$retArr[$r->cltid][$r->occid]['sciname'] = $sciName;
					$retArr[$r->cltid][$r->occid]['collcode'] = $r->collcode;
					$retArr[$r->cltid][$r->occid]['recordedby'] = $r->recordedby;
					$retArr[$r->cltid][$r->occid]['recordnumber'] = $r->recordnumber;
					$retArr[$r->cltid][$r->occid]['eventdate'] = $r->eventdate;
					$retArr[$r->cltid][$r->occid]['locality'] = $r->locality;
				}
			}
			elseif($includeAll === 3){
				$sql = 'SELECT DISTINCT t.tid AS cltid, t.sciname AS clsciname, o.occid, '.
					'IFNULL(CONCAT(c.institutioncode,"-",c.collectioncode,"-",o.catalognumber),"[no catalog number]") AS collcode, '.
					'o.tidinterpreted, o.sciname, o.recordedby, o.recordnumber, o.eventdate, '.
					'CONCAT_WS("; ",o.country, o.stateprovince, o.county, o.locality) as locality '.
					'FROM omcollections AS c INNER JOIN omoccurrences AS o ON c.collid = o.collid '.
					'LEFT JOIN taxa AS t ON o.tidinterpreted = t.TID '.
					'LEFT JOIN taxstatus AS ts ON t.TID = ts.tid ';
				if(strpos($sqlFrag,'MATCH(f.recordedby)') || strpos($sqlFrag,'MATCH(f.locality)')) {
					$sql .= 'LEFT JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
				}
				$sql .= 'WHERE ('.$sqlFrag.') AND ((t.RankId < 220)) '.
					'AND (o.occid NOT IN(SELECT occid FROM fmvouchers WHERE CLID IN('.$clidStr.'))) ';
				$sql .= 'ORDER BY o.family, o.sciname LIMIT '.$startLimit.', 500';
				//echo '<div>'.$sql.'</div>';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retArr[$r->cltid][$r->occid]['tid'] = $r->tidinterpreted;
					$sciName = $r->clsciname;
					if($r->clsciname !== $r->sciname) {
						$sciName .= '<br/>spec id: ' . $r->sciname;
					}
					$retArr[$r->cltid][$r->occid]['sciname'] = $sciName;
					$retArr[$r->cltid][$r->occid]['collcode'] = $r->collcode;
					$retArr[$r->cltid][$r->occid]['recordedby'] = $r->recordedby;
					$retArr[$r->cltid][$r->occid]['recordnumber'] = $r->recordnumber;
					$retArr[$r->cltid][$r->occid]['eventdate'] = $r->eventdate;
					$retArr[$r->cltid][$r->occid]['locality'] = $r->locality;
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function getMissingTaxa(): array
	{
		$retArr = array();
		if($sqlFrag = $this->getSqlFrag()){
			$sql = 'SELECT DISTINCT t.tid, t.sciname '.$this->getMissingTaxaBaseSql($sqlFrag);
			//echo '<div>'.$sql.'</div>';
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				$retArr[$row->tid] = $this->cleanOutStr($row->sciname);
			}
			asort($retArr);
			$rs->free();
		}
		$this->missingTaxaCount = count($retArr);
		return $retArr;
	}

	public function getMissingTaxaSpecimens($limitIndex): array
	{
		$retArr = array();
		if($sqlFrag = $this->getSqlFrag()){
			$sqlBase = $this->getMissingTaxaBaseSql($sqlFrag);
			$sql = 'SELECT DISTINCT o.occid, IFNULL(CONCAT(c.institutioncode,"-",c.collectioncode,"-",o.catalognumber),"[no catalog number]") AS collcode, '.
				'o.tidinterpreted, o.sciname, o.recordedby, o.recordnumber, o.eventdate, '.
				'CONCAT_WS("; ",o.country, o.stateprovince, o.county, o.locality) as locality '.
				$sqlBase.' LIMIT '.($limitIndex?($limitIndex*400).',':'').'400';
			//echo '<div>'.$sql.'</div>';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->sciname][$r->occid]['tid'] = $r->tidinterpreted;
				$retArr[$r->sciname][$r->occid]['collcode'] = $r->collcode;
				$retArr[$r->sciname][$r->occid]['recordedby'] = $r->recordedby;
				$retArr[$r->sciname][$r->occid]['recordnumber'] = $r->recordnumber;
				$retArr[$r->sciname][$r->occid]['eventdate'] = $r->eventdate;
				$retArr[$r->sciname][$r->occid]['locality'] = $r->locality;
			}
			$rs->free();

			$sqlB = 'SELECT COUNT(DISTINCT ts.tidaccepted) as cnt '.
				$sqlBase;
			//echo '<div>'.$sql.'</div>';
			$rsB = $this->conn->query($sqlB);
			if($r = $rsB->fetch_object()){
				$this->missingTaxaCount = $r->cnt;
			}
			$rsB->free();
		}
		return $retArr;
	}

	public function getConflictVouchers(): array
	{
		$retArr = array();
		$clidStr = $this->clid;
		if($this->childClidArr){
			$clidStr .= ','.implode(',',$this->childClidArr);
		}
		$sql = 'SELECT DISTINCT t.tid, v.clid, t.sciname AS listid, o.recordedby, o.recordnumber, o.sciname, o.identifiedby, o.dateidentified, o.occid '.
				'FROM taxstatus ts1 INNER JOIN omoccurrences o ON ts1.tid = o.tidinterpreted '.
				'INNER JOIN fmvouchers v ON o.occid = v.occid '.
				'INNER JOIN taxstatus ts2 ON v.tid = ts2.tid '.
				'INNER JOIN taxa t ON v.tid = t.tid '.
				'INNER JOIN taxstatus ts3 ON ts1.tidaccepted = ts3.tid '.
				'WHERE (v.clid IN('.$clidStr.')) AND ts1.taxauthid = 1 AND ts2.taxauthid = 1 AND ts1.tidaccepted <> ts2.tidaccepted '.
				'AND ts1.parenttid <> ts2.tidaccepted AND v.tid <> o.tidinterpreted AND ts3.parenttid <> v.tid '.
				'ORDER BY t.sciname ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($row = $rs->fetch_object()){
			$clSciname = $row->listid;
			$voucherSciname = $row->sciname;
			$retArr[$cnt]['tid'] = $row->tid;
			$retArr[$cnt]['clid'] = $row->clid;
			$retArr[$cnt]['occid'] = $row->occid;
			$retArr[$cnt]['listid'] = $clSciname;
			$collStr = $row->recordedby;
			if($row->recordnumber) {
				$collStr .= ' (' . $row->recordnumber . ')';
			}
			$retArr[$cnt]['recordnumber'] = $this->cleanOutStr($collStr);
			$retArr[$cnt]['specid'] = $this->cleanOutStr($voucherSciname);
			$idBy = $row->identifiedby;
			if($row->dateidentified) {
				$idBy .= ' (' . $this->cleanOutStr($row->dateidentified) . ')';
			}
			$retArr[$cnt]['identifiedby'] = $this->cleanOutStr($idBy);
			$cnt++;
		}
		$rs->free();
		return $retArr;
	}

	public function batchAdjustChecklist($postArr): void
	{
		$occidArr = $postArr['occid'];
		foreach($occidArr as $occid){
			$tidChecklist = 0;
			$sql = 'SELECT tid FROM fmvouchers WHERE (clid = '.$this->clid.') AND (occid = '.$occid.')';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$tidChecklist = $r->tid;
			}
			$rs->free();
			$tidVoucher = 0;
			$sql1 = 'SELECT tidinterpreted FROM omoccurrences WHERE (occid = '.$occid.')';
			$rs1 = $this->conn->query($sql1);
			if($r1 = $rs1->fetch_object()){
				$tidVoucher = $r1->tidinterpreted;
			}
			$rs1->free();
			$sql2 = 'INSERT IGNORE INTO fmchklsttaxalink(tid, clid, morphospecies, familyoverride, habitat, abundance, notes, explicitExclude, source, internalnotes, dynamicProperties) '.
				'SELECT '.$tidVoucher.' as tid, c.clid, c.morphospecies, c.familyoverride, c.habitat, c.abundance, c.notes, c.explicitExclude, c.source, c.internalnotes, c.dynamicProperties '.
				'FROM fmchklsttaxalink c INNER JOIN fmvouchers v ON c.tid = v.tid AND c.clid = v.clid '.
				'WHERE (c.clid = '.$this->clid.') AND (v.occid = '.$occid.')';
			$this->conn->query($sql2);
			$sql3 = 'UPDATE fmvouchers SET tid = '.$tidVoucher.' WHERE (clid = '.$this->clid.') AND (occid = '.$occid.')';
			$this->conn->query($sql3);
			if(array_key_exists('removeOldIn',$postArr)){
				$sql4 = 'DELETE c.* FROM fmchklsttaxalink c LEFT JOIN fmvouchers v ON c.clid = v.clid AND c.tid = v.tid '.
					'WHERE (c.clid = '.$this->clid.') AND (c.tid = '.$tidChecklist.') AND ISNULL(v.clid)';
				$this->conn->query($sql4);
			}
		}
	}

	public function exportMissingOccurCsv(): void
	{
		if($sqlFrag = $this->getSqlFrag()){
			$fileName = 'Missing_'.$this->getExportFileName().'.csv';

			$fieldArr = $this->getOccurrenceFieldArr();
			$localitySecurityFields = $this->getLocalitySecurityArr();

			$exportSql = 'SELECT '.implode(',',$fieldArr).', o.localitysecurity, o.collid '.
				$this->getMissingTaxaBaseSql($sqlFrag);
			//echo $exportSql;
			$this->exportCsv($fileName,$exportSql,$localitySecurityFields);
		}
	}

	private function getMissingTaxaBaseSql($sqlFrag): string
	{
		$clidStr = $this->clid;
		if($this->childClidArr) {
			$clidStr .= ',' . implode(',', $this->childClidArr);
		}
		$retSql = 'FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.collid '.
			'INNER JOIN taxstatus ts ON o.tidinterpreted = ts.tid '.
			'INNER JOIN taxa t ON ts.tidaccepted = t.tid '.
			'LEFT JOIN guidoccurrences g ON o.occid = g.occid ';
		if(strpos($sqlFrag,'MATCH(f.recordedby)') || strpos($sqlFrag,'MATCH(f.locality)')) {
			$retSql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
		}
		$retSql .= 'WHERE ('.$sqlFrag.') '.
			'AND (t.rankid IN(220,230,240,260,230)) AND (ts.taxauthid = 1) '.
			'AND (o.occid NOT IN(SELECT occid FROM fmvouchers WHERE clid IN('.$clidStr.'))) '.
			'AND (ts.tidaccepted NOT IN(SELECT ts.tidaccepted FROM fmchklsttaxalink cl INNER JOIN taxstatus ts ON cl.tid = ts.tid WHERE ts.taxauthid = 1 AND cl.clid IN('.$clidStr.'))) ';
		return $retSql;
	}

	public function getMissingProblemTaxa(): array
	{
		$retArr = array();
		if($sqlFrag = $this->getSqlFrag()){
			$sql = 'SELECT DISTINCT o.occid, IFNULL(CONCAT(c.institutioncode,"-",c.collectioncode,"-",o.catalognumber),"[no catalog number]") AS collcode, '.
				'o.sciname, o.recordedby, o.recordnumber, o.eventdate, '.
				'CONCAT_WS("; ",o.country, o.stateprovince, o.county, o.locality) as locality '.
				$this->getProblemTaxaSql($sqlFrag);
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$sciname = $r->sciname;
				if($sciname){
					$retArr[$sciname][$r->occid]['collcode'] = $r->collcode;
					$retArr[$sciname][$r->occid]['recordedby'] = $r->recordedby;
					$retArr[$sciname][$r->occid]['recordnumber'] = $r->recordnumber;
					$retArr[$sciname][$r->occid]['eventdate'] = $r->eventdate;
					$retArr[$sciname][$r->occid]['locality'] = $r->locality;
				}
			}
			$rs->free();
		}
		$this->missingTaxaCount = count($retArr);
		return $retArr;
	}

	public function exportProblemTaxaCsv(): void
	{
		$fileName = 'ProblemTaxa_'.$this->getExportFileName().'.csv';

		if($sqlFrag = $this->getSqlFrag()){
			$fieldArr = $this->getOccurrenceFieldArr();
			$localitySecurityFields = $this->getLocalitySecurityArr();
			$sql = 'SELECT DISTINCT '.implode(',',$fieldArr).', o.localitysecurity, o.collid '.
				$this->getProblemTaxaSql($sqlFrag);
			$this->exportCsv($fileName,$sql,$localitySecurityFields);
		}
	}

	private function getProblemTaxaSql($sqlFrag): string
	{
		$clidStr = $this->clid;
		if($this->childClidArr) {
			$clidStr .= ',' . implode(',', $this->childClidArr);
		}
		$retSql = 'FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.CollID '.
			'LEFT JOIN guidoccurrences g ON o.occid = g.occid ';
		if(strpos($sqlFrag,'MATCH(f.recordedby)') || strpos($sqlFrag,'MATCH(f.locality)')) {
			$retSql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
		}
		$retSql .= 'WHERE ('.$sqlFrag.') AND (o.tidinterpreted IS NULL) AND (o.sciname IS NOT NULL) '.
			'AND (o.occid NOT IN(SELECT occid FROM fmvouchers WHERE clid IN('.$clidStr.'))) ';
		return $retSql;
	}

	public function downloadChecklistCsv(): void
	{
		if($this->clid){
			$fieldArr = array('tid'=>'t.tid AS Taxon_Local_ID');
			$fieldArr['clhabitat'] = 'ctl.habitat AS habitat';
			$fieldArr['clabundance'] = 'ctl.abundance';
			$fieldArr['clNotes'] = 'ctl.notes';
			$fieldArr['clSource'] = 'ctl.source';
			$fieldArr['editorNotes'] = 'ctl.internalnotes';
			$fieldArr['family'] = 'IFNULL(ctl.familyoverride,ts.family) AS family';
			$fieldArr['scientificName'] = 't.sciName AS scientificName';
			$fieldArr['author'] = 't.author AS scientificNameAuthorship';

			$clidStr = $this->clid;
			if($this->childClidArr){
				$clidStr .= ','.implode(',',$this->childClidArr);
			}

			$fileName = $this->getExportFileName().'.csv';
			$sql = 'SELECT DISTINCT '.implode(',',$fieldArr).' '.
					'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
					'INNER JOIN fmchklsttaxalink ctl ON ctl.tid = t.tid '.
					'WHERE (ts.taxauthid = 1) AND (ctl.clid IN('.$clidStr.')) ';
			$this->exportCsv($fileName,$sql);
		}
	}

	public function downloadVoucherCsv(): void
	{
		if($this->clid){
			$fileName = $this->getExportFileName().'.csv';

			$fieldArr = array('tid'=>'t.tid AS taxonID', 'family'=>'IFNULL(ctl.familyoverride,ts.family) AS family', 'scientificName'=>'t.sciname', 'author'=>'t.author AS scientificNameAuthorship');
			$fieldArr['clhabitat'] = 'ctl.habitat AS cl_habitat';
			$fieldArr['clabundance'] = 'ctl.abundance';
			$fieldArr['clNotes'] = 'ctl.notes';
			$fieldArr['clSource'] = 'ctl.source';
			$fieldArr['editorNotes'] = 'ctl.internalnotes';
			$fieldArr = array_merge($fieldArr,$this->getOccurrenceFieldArr());
			$fieldArr['family'] = 'ts.family';
			$fieldArr['scientificName'] = 't.sciName AS scientificName';

			$localitySecurityFields = $this->getLocalitySecurityArr();

			$clidStr = $this->clid;
			if($this->childClidArr){
				$clidStr .= ','.implode(',',$this->childClidArr);
			}

			$sql = 'SELECT DISTINCT '.implode(',',$fieldArr).', o.localitysecurity, o.collid '.
				'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'INNER JOIN fmchklsttaxalink ctl ON ctl.tid = t.tid '.
				'LEFT JOIN fmvouchers v ON ctl.clid = v.clid AND ctl.tid = v.tid '.
				'LEFT JOIN omoccurrences o ON v.occid = o.occid '.
				'LEFT JOIN omcollections c ON o.collid = c.collid '.
				'LEFT JOIN guidoccurrences g ON o.occid = g.occid '.
				'WHERE (ts.taxauthid = 1) AND (ctl.clid IN('.$clidStr.')) ';
			$this->exportCsv($fileName,$sql,$localitySecurityFields);
		}
	}

	private function exportCsv($fileName,$sql,$localitySecurityFields = null): void
	{
		global $USER_RIGHTS;
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$headerArr = array();
			$fields = mysqli_fetch_fields($rs);
			foreach ($fields as $val) {
				$headerArr[] = $val->name;
			}
			$rareSpeciesReader = $this->isRareSpeciesReader();
			$out = fopen('php://output', 'w');
			fputcsv($out, $headerArr);
			while($row = $rs->fetch_assoc()){
				if($localitySecurityFields){
					$localSecurity = ($row['localitysecurity']?:0);
					if(!$rareSpeciesReader && $localSecurity !== 1 && (!array_key_exists('RareSppReader', $USER_RIGHTS) || !in_array($row['collid'], $USER_RIGHTS['RareSppReader'], true))){
						$redactStr = '';
						foreach($localitySecurityFields as $fieldName){
							if($row[$fieldName]) {
								$redactStr .= ',' . $fieldName;
							}
						}
						if($redactStr) {
							$row['informationWithheld'] = 'Fields with redacted values (e.g. rare species localities):' . trim($redactStr, ', ');
						}
					}
				}
				$this->encodeArr($row);
				fputcsv($out, $row);
			}
			$rs->free();
			fclose($out);
		}
		else{
			echo "Recordset is empty.\n";
		}
	}

	protected function getExportFileName(){
		$fileName = $this->clName;
		if($fileName){
			if(strlen($fileName) > 20){
				$nameArr = explode(' ',$fileName);
				foreach($nameArr as $k => $w){
					if(strlen($w) > 3) {
						$nameArr[$k] = substr($w, 0, 4);
					}
				}
				$fileName = implode('',$nameArr);
			}
		}
		else{
			$fileName = 'symbiota';
		}
		$fileName = str_replace(Array('.',' ',':'),'',$fileName);
		$fileName .= '_'.time();
		return $fileName;
	}

	private function getOccurrenceFieldArr(): array
	{
		global $CLIENT_ROOT;
		$retArr = array('o.family AS family_occurrence', 'o.sciName AS scientificName_occurrence', 'IFNULL(o.institutionCode,c.institutionCode) AS institutionCode','IFNULL(o.collectionCode,c.collectionCode) AS collectionCode',
			'CASE guidTarget WHEN "symbiotaUUID" THEN IFNULL(o.occurrenceID,g.guid) WHEN "occurrenceId" THEN o.occurrenceID WHEN "catalogNumber" THEN o.catalogNumber ELSE "" END AS occurrenceID',
			'o.catalogNumber', 'o.otherCatalogNumbers', 'o.identifiedBy', 'o.dateIdentified',
 			'o.recordedBy', 'o.recordNumber', 'o.eventDate', 'o.country', 'o.stateProvince', 'o.county', 'o.municipality', 'o.locality',
 			'o.decimalLatitude', 'o.decimalLongitude', 'o.coordinateUncertaintyInMeters', 'o.minimumElevationInMeters', 'o.maximumelevationinmeters',
			'o.verbatimelevation', 'o.habitat', 'o.occurrenceRemarks', 'o.associatedTaxa', 'o.reproductivecondition', 'o.informationWithheld', 'o.occid');
		$retArr[] = 'g.guid AS recordID';
		$serverDomain = 'http://';
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
			$serverDomain = 'https://';
		}
		$serverDomain .= $_SERVER['HTTP_HOST'];
		if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
			$serverDomain .= ':' . $_SERVER['SERVER_PORT'];
		}
		$retArr[] = 'CONCAT("'.$serverDomain.$CLIENT_ROOT.'/collections/individual/index.php?occid=",o.occid) as `references`';
		return $retArr;
	}

	private function getLocalitySecurityArr(): array
	{
		return array('recordNumber','eventDate','locality','decimalLatitude','decimalLongitude','minimumElevationInMeters',
			'minimumElevationInMeters','habitat','occurrenceRemarks');
	}

	public function linkVouchers($occidArr): string
	{
		$retStatus = '';
		$sqlFrag = '';
		foreach($occidArr as $v){
			$vArr = explode('-',$v);
			if(count($vArr) === 2 && $vArr[0] && $vArr[1]) {
				$sqlFrag .= ',(' . $this->clid . ',' . $vArr[0] . ',' . $vArr[1] . ')';
			}
		}
		$sql = 'INSERT INTO fmvouchers(clid,occid,tid) VALUES '.substr($sqlFrag,1);
		//echo $sql;
		if(!$this->conn->query($sql)){
			trigger_error('Unable to link voucher; '.$this->conn->error,E_USER_WARNING);
		}
		return $retStatus;
	}

	public function linkVoucher($taxa,$occid){
		if(!is_numeric($taxa)){
			$rs = $this->conn->query('SELECT tid FROM taxa WHERE (sciname = "'.$this->conn->real_escape_string($taxa).'")');
			if($r = $rs->fetch_object()){
				$taxa = $r->tid;
			}
		}
		$sql = 'INSERT INTO fmvouchers(clid,tid,occid) '.
			'VALUES ('.$this->clid.','.$taxa.','.$occid.')';
		if($this->conn->query($sql)){
			return 1;
		}

		if($this->conn->errno === 1062){
			return 'Specimen already a voucher for checklist';
		}

		$sql2 = 'INSERT INTO fmchklsttaxalink(tid,clid) VALUES('.$taxa.','.$this->clid.')';
		if($this->conn->query($sql2)){
			if($this->conn->query($sql)){
				return 1;
			}

			return 'Name added to checklist, though unable to link voucher: '.$this->conn->error;
		}

		return 'Unable to link voucher: '.$this->conn->error;
	}

	public function linkTaxaVouchers($occidArr,$useCurrentTaxon = 1): void
	{
		$tidsUsed = array();
		foreach($occidArr as $v){
			$vArr = explode('-',$v);
			$tid = $vArr[1];
			$occid = $vArr[0];
			if(is_numeric($occid) && is_numeric($tid) && count($vArr) === 2){
				if($useCurrentTaxon){
					$sql = 'SELECT tidaccepted FROM taxstatus WHERE taxauthid = 1 AND tid = '.$tid;
					$rs = $this->conn->query($sql);
					if($r = $rs->fetch_object()){
						$tid = $r->tidaccepted;
					}
					$rs->free();
				}
				if(!in_array($tid, $tidsUsed, true)){
					$sql = 'INSERT INTO fmchklsttaxalink(clid,tid) VALUES('.$this->clid.','.$tid.')';
					$tidsUsed[] = $tid;
					//echo $sql;
					if(!$this->conn->query($sql)){
						trigger_error('Unable to add taxon; '.$this->conn->error,E_USER_WARNING);
					}
				}
				$sql2 = 'INSERT INTO fmvouchers(clid,occid,tid) VALUES ('.$this->clid.','.$occid.','.$tid.')';
				if(!$this->conn->query($sql2)){
					trigger_error('Unable to link taxon voucher; '.$this->conn->error,E_USER_WARNING);
				}
			}
		}
	}

	public function getQueryVariablesArr(): array
	{
		return $this->queryVariablesArr;
	}

	public function getQueryVariableStr(): string
	{
		$retStr = '';
		if(isset($this->queryVariablesArr['collid'])){
			$collArr = $this->getCollectionList($this->queryVariablesArr['collid']);
			$retStr .= current($collArr).'; ';
		}
		if(isset($this->queryVariablesArr['country'])) {
			$retStr .= $this->queryVariablesArr['country'] . '; ';
		}
		if(isset($this->queryVariablesArr['state'])) {
			$retStr .= $this->queryVariablesArr['state'] . '; ';
		}
		if(isset($this->queryVariablesArr['county'])) {
			$retStr .= $this->queryVariablesArr['county'] . '; ';
		}
		if(isset($this->queryVariablesArr['locality'])) {
			$retStr .= $this->queryVariablesArr['locality'] . '; ';
		}
		if(isset($this->queryVariablesArr['taxon'])) {
			$retStr .= $this->queryVariablesArr['taxon'] . '; ';
		}
		if(isset($this->queryVariablesArr['recordedby'])) {
			$retStr .= $this->queryVariablesArr['recordedby'] . '; ';
		}
		if(isset($this->queryVariablesArr['latsouth'], $this->queryVariablesArr['latnorth'])) {
			$retStr .= 'Lat between ' . $this->queryVariablesArr['latsouth'] . ' and ' . $this->queryVariablesArr['latnorth'] . '; ';
		}
		if(isset($this->queryVariablesArr['lngwest'], $this->queryVariablesArr['lngeast'])) {
			$retStr .= 'Long between ' . $this->queryVariablesArr['lngwest'] . ' and ' . $this->queryVariablesArr['lngeast'] . '; ';
		}
		if(isset($this->queryVariablesArr['latlngor'])) {
			$retStr .= 'Include Lat/Long and locality as an "OR" condition; ';
		}
		if(isset($this->queryVariablesArr['excludecult'])) {
			$retStr .= 'Exclude cultivated species; ';
		}
		if(isset($this->queryVariablesArr['onlycoord'])) {
			$retStr .= 'Only include occurrences with coordinates; ';
		}
		return trim($retStr,' ;');
	}

	public function getMissingTaxaCount(): int
	{
		return $this->missingTaxaCount;
	}

	private function isRareSpeciesReader(): bool
	{
		global $IS_ADMIN, $USER_RIGHTS;
		$canReadRareSpp = false;
		if($IS_ADMIN
			|| array_key_exists('CollAdmin', $USER_RIGHTS)
			|| array_key_exists('RareSppAdmin', $USER_RIGHTS) || array_key_exists('RareSppReadAll', $USER_RIGHTS)){
			$canReadRareSpp = true;
		}
		return $canReadRareSpp;
	}

	public function getCollectionList($collId = 0): array
	{
		$retArr = array();
		$sql = 'SELECT collid, collectionname FROM omcollections ';
		if($collId) $sql .= 'WHERE collid = '.$collId;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid] = $r->collectionname;
		}
		$rs->free();
		asort($retArr);
		return $retArr;
	}

	private function getSciname($tid): string
	{
		$retStr = '';
		if(is_numeric($tid)){
			$sql = 'SELECT sciname FROM taxa WHERE tid = '.$tid;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retStr = $r->sciname;
			}
			$rs->free();
		}
		return $retStr;
	}

	private function getTid($sciname): int
	{
		$tidRet = 0;
		$sql = 'SELECT tid FROM taxa WHERE sciname = ("'.$sciname.'")';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$tidRet = $r->tid;
		}
		$rs->free();
		return $tidRet;
	}

	public function getChildClidArr(): array
	{
		return $this->childClidArr;
	}

	private function encodeArr(&$inArr): void
	{
		global $CHARSET;
		$charSetOut = 'ISO-8859-1';
		$charSetSource = strtoupper($CHARSET);
		if($charSetSource && $charSetOut !== $charSetSource){
			foreach($inArr as $k => $v){
				$inArr[$k] = $this->encodeStr($v);
			}
		}
	}

	protected function encodeStr($inStr): string
	{
		global $CHARSET;
		$charSetSource = strtoupper($CHARSET);
		$charSetOut = 'ISO-8859-1';
		$retStr = $inStr;
		if($inStr && $charSetSource){
			if($charSetOut === 'UTF-8' && $charSetSource === 'ISO-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
				}
			}
			elseif($charSetOut === 'ISO-8859-1' && $charSetSource === 'UTF-8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
					$retStr = utf8_decode($inStr);
				}
			}
		}
		return $retStr;
	}

	private function cleanOutStr($str){
		$str = str_replace(array('"', "'"), array('&quot;', '&apos;'), $str);
		return $str;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
