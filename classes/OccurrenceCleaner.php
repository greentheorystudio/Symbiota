<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/OccurrenceEditorManager.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceCleaner extends Manager{

	private $collid;
	private $obsUid;
	private $featureCount = 0;
	
	public function __construct(){
		parent::__construct();
	}

	public function getDuplicateCatalogNumber($type, $start, $limit): array
	{
		$dupArr = array();
		$catArr = array();
		$cnt = 0;
		if($type === 'cat'){
			$sql1 = 'SELECT catalognumber '.
				'FROM omoccurrences '.
				'WHERE catalognumber IS NOT NULL AND collid = '.$this->collid;
		}
		else{
			$sql1 = 'SELECT otherCatalogNumbers '.
				'FROM omoccurrences '.
				'WHERE otherCatalogNumbers IS NOT NULL AND collid = '.$this->collid;
		}
		//echo $sql1;
		$rs = $this->conn->query($sql1);
		while($r = $rs->fetch_object()){
			$cn = ($type === 'cat'?$r->catalognumber:$r->otherCatalogNumbers);
			if(array_key_exists($cn,$catArr)){
				$cnt++;
				if($start < $cnt && !array_key_exists($cn,$dupArr)){
					$dupArr[$cn] = '';
					if(count($dupArr) > $limit) {
						break;
					}
				}
			}
			else{
				$catArr[$cn] = '';
			}
		}
		$rs->free();

		if($type === 'cat'){
			$sql = 'SELECT o.catalognumber AS dupid, o.occid, o.catalognumber, o.dbpk, o.othercatalognumbers, o.family, o.sciname, '.
				'o.recordedby, o.recordnumber, o.associatedcollectors, o.eventdate, o.verbatimeventdate, '.
				'o.country, o.stateprovince, o.county, o.municipality, o.locality, o.datelastmodified '.
				'FROM omoccurrences AS o '.
				'WHERE o.collid = '.$this->collid.' AND o.catalognumber IN("'.implode('","',array_keys($dupArr)).'") '.
				'ORDER BY o.catalognumber';
		}
		else{
			$sql = 'SELECT o.otherCatalogNumbers AS dupid, o.occid, o.catalognumber, o.dbpk, o.othercatalognumbers, o.family, o.sciname, '.
				'o.recordedby, o.recordnumber, o.associatedcollectors, o.eventdate, o.verbatimeventdate, '.
				'o.country, o.stateprovince, o.county, o.municipality, o.locality, o.datelastmodified '.
				'FROM omoccurrences AS o '.
				'WHERE o.collid = '.$this->collid.' AND o.otherCatalogNumbers IN("'.implode('","',array_keys($dupArr)).'") '.
				'ORDER BY o.otherCatalogNumbers';
		}
		//echo $sql;

		return $this->getDuplicates($sql);
	}

	public function getDuplicateCollectorNumber($start): array
	{
		$retArr = array();
		if($this->obsUid){
			$sql = 'SELECT o.occid, o.eventdate, recordedby, o.recordnumber '.
				'FROM omoccurrences AS o INNER JOIN '.
				'(SELECT eventdate, recordnumber FROM omoccurrences GROUP BY eventdate, recordnumber, collid, observeruid '.
				'HAVING Count(*)>1 AND collid = '.$this->collid.' AND observeruid = '.$this->obsUid.
				' AND eventdate IS NOT NULL AND recordnumber IS NOT NULL '.
				'AND recordnumber NOT IN("sn","s.n.","Not Provided","unknown")) AS intab '.
				'ON o.eventdate = intab.eventdate AND o.recordnumber = intab.recordnumber '.
				'WHERE collid = '.$this->collid.' AND observeruid = '.$this->obsUid.' ';
		}
		else{
			$sql = 'SELECT o.occid, o.eventdate, recordedby, o.recordnumber '.
				'FROM omoccurrences AS o INNER JOIN '.
				'(SELECT eventdate, recordnumber FROM omoccurrences GROUP BY eventdate, recordnumber, collid '.
				'HAVING Count(*)>1 AND collid = '.$this->collid.
				' AND eventdate IS NOT NULL AND recordnumber IS NOT NULL '.
				'AND recordnumber NOT IN("sn","s.n.","Not Provided","unknown")) AS intab '.
				'ON o.eventdate = intab.eventdate AND o.recordnumber = intab.recordnumber '.
				'WHERE collid = '.$this->collid.' ';
		}
		//echo $sql;
		$rs = $this->conn->query($sql);
		$collArr = array();
		while($r = $rs->fetch_object()){
			$collArr[$r->eventdate][$r->recordnumber][$r->recordedby][] = $r->occid;
		}
		$rs->free();

		$cnt = 0;
		foreach($collArr as $ed => $arr1){
			foreach($arr1 as $rn => $arr2){
				foreach($arr2 as $ln => $dupArr){
					if(count($dupArr) > 1){
						if($cnt >= $start){
							$sql = 'SELECT '.$cnt.' AS dupid, o.occid, o.dbpk, o.catalognumber, o.othercatalognumbers, o.othercatalognumbers, o.family, o.sciname, o.recordedby, o.recordnumber, '.
								'o.associatedcollectors, o.eventdate, o.verbatimeventdate, o.country, o.stateprovince, o.county, o.municipality, o.locality, datelastmodified '.
								'FROM omoccurrences AS o '.
								'WHERE occid IN('.implode(',',$dupArr).') ';
							//echo $sql;
							$dupArr = $this->getDuplicates($sql);
							foreach($dupArr as $dup){
								$retArr[] = $dup;
							}
						}
						if($cnt > ($start+200)) {
							break 3;
						}
						$cnt++;
					}
				}
			}
		}
		return $retArr;
	}

	private function getDuplicates($sql): array
	{
		$retArr = array();
		$cnt = 0;
		$dupid = '';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_assoc()){
			if($dupid !== $row['dupid']) {
				$cnt++;
			}
			$retArr[$cnt][$row['occid']] = array_change_key_case($row);
			$dupid = $row['dupid'];
		}
		$rs->free();
		return $retArr;
	}

	public function mergeDupeArr($occidArr): bool
	{
		$status = true;
		$this->verboseMode = 2;
		$editorManager = new OccurrenceEditorManager($this->conn);
		foreach($occidArr as $target => $occArr){
			$mergeArr = array($target);
			foreach($occArr as $source){
				if($source !== $target){
					if($editorManager->mergeRecords($target,$source)){
						$mergeArr[] = $source;
					}
					else{
						$this->logOrEcho($editorManager->getErrorStr(),1);
						$status = false;
					}
				}
			}
			if(count($mergeArr) > 1){
				$this->logOrEcho('Merged records: '.implode(', ',$mergeArr),1);
			}
		}
		return $status;
	}

	public function countryCleanFirstStep(): void
	{
		echo '<div style="margin-left:15px;">Preparing countries index...</div>';
		flush();
		$occArr = array();
		$sql = 'SELECT occid FROM omoccurrences WHERE ((country LIKE " %") OR (country LIKE "% ")) AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$occArr[] = $r->occid;
		}
		$rs->free();
		if($occArr){
			$sqlTrim = 'UPDATE omoccurrences SET country = trim(country) WHERE (occid IN('.implode(',',$occArr).'))';
			$this->conn->query($sqlTrim);
		}

		$sqlEmpty = 'UPDATE omoccurrences SET country = NULL WHERE (country = "")';
		$this->conn->query($sqlEmpty);

		echo '<div style="margin-left:15px;">Preparing state index...</div>';
		flush();
		unset($occArr);
		$occArr = array();
		$sql = 'SELECT occid FROM omoccurrences WHERE ((stateprovince LIKE " %") OR (stateprovince LIKE "% ")) AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$occArr[] = $r->occid;
		}
		$rs->free();
		if($occArr){
			$sqlTrim = 'UPDATE omoccurrences SET stateprovince = trim(stateprovince) WHERE (occid IN('.implode(',',$occArr).'))';
			$this->conn->query($sqlTrim);
		}

		$sqlEmpty = 'UPDATE omoccurrences SET stateprovince = NULL WHERE (stateprovince = "")';
		$this->conn->query($sqlEmpty);

		echo '<div style="margin-left:15px;">Preparing county index...</div>';
		flush();
		unset($occArr);
		$occArr = array();
		$sql = 'SELECT occid FROM omoccurrences WHERE ((county LIKE " %") OR (county LIKE "% ")) AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$occArr[] = $r->occid;
		}
		$rs->free();
		if($occArr){
			$sqlTrim = 'UPDATE omoccurrences SET county = trim(county) WHERE (occid IN('.implode(',',$occArr).'))';
			$this->conn->query($sqlTrim);
		}

		$sqlEmpty = 'UPDATE omoccurrences SET county = NULL WHERE (county = "")';
		$this->conn->query($sqlEmpty);
	}

	public function getBadCountryCount(): int
    {
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT o.country) AS cnt '.
			'FROM omoccurrences AS o LEFT JOIN lkupcountry AS l ON o.country = l.countryname '.
			'WHERE o.country IS NOT NULL AND o.collid = '.$this->collid.' AND ISNULL(l.countryid) ';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getBadCountryArr(): array
	{
		$retArr = array();
		$sql = 'SELECT country, count(o.occid) as cnt '.
			'FROM omoccurrences AS o LEFT JOIN lkupcountry AS l ON o.country = l.countryname '.
			'WHERE o.country IS NOT NULL AND o.collid = '.$this->collid.' AND ISNULL(l.countryid) '.
			'GROUP BY o.country ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->country] = $r->cnt;
		}
		$rs->free();
		$this->featureCount = count($retArr);
		ksort($retArr);
		return $retArr;
	}

	public function getGoodCountryArr($includeStates = null): array
	{
		$retArr = array();
		if($includeStates){
			$sql = 'SELECT c.countryname, s.statename FROM lkupcountry AS c LEFT JOIN lkupstateprovince AS s ON c.countryid = s.countryid ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->countryname][] = $r->statename;
			}
			$rs->free();
			ksort($retArr);
		}
		else{
			$sql = 'SELECT countryname FROM lkupcountry';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = $r->countryname;
			}
			$rs->free();
			sort($retArr);
			$retArr[] = 'unknown';
		}
		return $retArr;
	}

	public function getNullCountryNotStateCount(): int
    {
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT stateprovince) AS cnt '.
			'FROM omoccurrences '.
			'WHERE collid = '.$this->collid.' AND ISNULL(country) AND stateprovince IS NOT NULL ';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getNullCountryNotStateArr(): array
	{
		$retArr = array();
		$sql = 'SELECT stateprovince, COUNT(occid) AS cnt '.
			'FROM omoccurrences '.
			'WHERE collid = '.$this->collid.' AND ISNULL(country IS NULL) AND stateprovince IS NOT NULL '.
			'GROUP BY stateprovince';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[ucwords(strtolower($r->stateprovince))] = $r->cnt;
		}
		$rs->free();
		$this->featureCount = count($retArr);
		ksort($retArr);
		return $retArr;
	}

	public function getBadStateCount($country = null): int
    {
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT o.stateprovince) AS cnt '.$this->getBadStateSqlBase();
		if($country) {
			$sql .= 'AND o.country = "' . Sanitizer::cleanInStr($this->conn,$country) . '" ';
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = (int)$r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getBadStateArr(): array
	{
		$retArr = array();
		$sqlFrag = $this->getBadStateSqlBase();
		if($sqlFrag){
			$sql = 'SELECT o.country, o.stateprovince, COUNT(DISTINCT o.occid) AS cnt '.
				$this->getBadStateSqlBase().
				'GROUP BY o.stateprovince ';
			$rs = $this->conn->query($sql);
			$cnt = 0;
			while($r = $rs->fetch_object()){
				$retArr[$r->country][ucwords(strtolower($r->stateprovince))] = $r->cnt;
				$cnt++;
			}
			$rs->free();
			$this->featureCount = $cnt;
			ksort($retArr);
		}
		else{
			$this->errorMessage = '';
		}
		return $retArr;
	}

	private function getBadStateSqlBase(): string
	{
		$retStr = '';
		$countryArr = array();
		$sql = 'SELECT DISTINCT c.countryname FROM lkupcountry AS c INNER JOIN lkupstateprovince AS s ON c.countryid = s.countryid ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$countryArr[] = $r->countryname;
		}
		$rs->free();

		if($countryArr){
			$retStr = 'FROM omoccurrences AS o LEFT JOIN lkupstateprovince AS l ON o.stateprovince = l.statename '.
				'WHERE (o.country IN("'.implode('","', $countryArr).'")) AND (o.stateprovince IS NOT NULL) AND (o.collid = '.$this->collid.') AND ISNULL(l.stateid) ';
		}

		return $retStr;
	}

	public function getGoodStateArr($includeCounties = null): array
	{
		$retArr = array();
		if($includeCounties){
			$sql = 'SELECT c.countryname, s.statename, co.countyname '.
				'FROM lkupstateprovince AS s INNER JOIN lkupcountry AS c ON s.countryid = c.countryid '.
				'LEFT JOIN lkupcounty AS co ON s.stateid = co.stateid ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[strtoupper($r->countryname)][ucwords(strtolower($r->statename))][] = str_replace(array(' county',' co.',' co'),'',strtolower($r->countyname));
			}
			$rs->free();
		}
		else{
			$sql = 'SELECT c.countryname, s.statename '.
				'FROM lkupstateprovince AS s INNER JOIN lkupcountry AS c ON s.countryid = c.countryid ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->countryname][] = $r->statename;
			}
			$rs->free();
		}
		ksort($retArr);
		$retArr[] = 'unknown';
		return $retArr;
	}

	public function getNullStateNotCountyCount(): int
    {
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT county) AS cnt '.$this->getNullStateNotCountySqlFrag();
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getNullStateNotCountyArr(): array
	{
		$retArr = array();
		$sql = 'SELECT country, county, COUNT(occid) AS cnt '.$this->getNullStateNotCountySqlFrag().'GROUP BY county';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$retArr[strtoupper($r->country)][$r->county] = $r->cnt;
			$cnt++;
		}
		$rs->free();
		$this->featureCount = $cnt;
		ksort($retArr);
		return $retArr;
	}

	private function getNullStateNotCountySqlFrag(): string
	{
		return 'FROM omoccurrences '.
			'WHERE (collid = '.$this->collid.') AND ISNULL(stateprovince) AND (county IS NOT NULL) AND (country IS NOT NULL) ';
	}

	public function getBadCountyCount($state = null): int
    {
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT o.county) AS cnt '.$this->getBadCountySqlFrag();
		if($state) {
			$sql .= 'AND o.stateprovince = "' . Sanitizer::cleanInStr($this->conn,$state) . '" ';
		}
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = (int)$r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getBadCountyArr(): array
	{
		$retArr = array();
		$sql = 'SELECT o.country, o.stateprovince, o.county, COUNT(o.occid) AS cnt '.$this->getBadCountySqlFrag().'GROUP BY o.country, o.stateprovince, o.county ';
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$retArr[strtoupper($r->country)][ucwords(strtolower($r->stateprovince))][$r->county] = $r->cnt;
			$cnt++;
		}
		$rs->free();
		$this->featureCount = $cnt;
		return $retArr;
	}

	private function getBadCountySqlFrag(): string
	{
		$retStr = '';
		$stateyArr = array();
		$sql = 'SELECT DISTINCT s.statename '.
			'FROM lkupstateprovince AS s INNER JOIN lkupcounty AS co ON s.stateid = co.stateid ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$stateyArr[] = $r->statename;
		}
		$rs->free();
		if($stateyArr){
			$retStr = 'FROM omoccurrences AS o LEFT JOIN lkupcounty AS l ON o.county = l.countyname '.
			'WHERE (o.county IS NOT NULL) AND (o.country = "USA") AND (o.stateprovince IN("'.implode('","', $stateyArr).'")) '.
			'AND (o.collid = '.$this->collid.') AND ISNULL(l.countyid) ';
		}
		return $retStr;
	}

	public function getGoodCountyArr(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT statename, REPLACE(countyname," County","") AS countyname '.
			'FROM lkupcounty AS c INNER JOIN lkupstateprovince AS s ON c.stateid = s.stateid '.
			'ORDER BY c.countyname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[strtolower($r->statename)][] = $r->countyname;
		}
		$rs->free();
		$retArr[] = 'unknown';
		return $retArr;
	}

	public function getNullCountyNotLocalityCount(): int
    {
		$retCnt = 0;
		$sql = 'SELECT COUNT(DISTINCT locality) AS cnt '.$this->getNullCountyNotLocalitySqlFrag();
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getNullCountyNotLocalityArr(): array
	{
		$retArr = array();
		$sql = 'SELECT country, stateprovince, locality, COUNT(occid) AS cnt '.
			$this->getNullCountyNotLocalitySqlFrag().
			'GROUP BY country, stateprovince, locality';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$locStr = $r->locality;
			$retArr[$r->country][ucwords(strtolower($r->stateprovince))][$locStr] = $r->cnt;
			$cnt++;
		}
		$rs->free();
		$this->featureCount = $cnt;
		ksort($retArr);
		return $retArr;
	}

	private function getNullCountyNotLocalitySqlFrag(): string
	{
		return 'FROM omoccurrences '.
			'WHERE (collid = '.$this->collid.') AND ISNULL(county) AND (locality IS NOT NULL) '.
			'AND country IN("USA","United States") AND (stateprovince IS NOT NULL) AND (stateprovince NOT IN("District Of Columbia","DC")) ';
	}

	public function getCoordStats(): array
	{
		$retArr = array();
        $coord = 0;
        $noCoord = 0;
        $noCoordVerbatim = 0;
        $noCoordNoVerbatim = 0;
		$sql = 'SELECT decimallatitude, decimallongitude, verbatimcoordinates '.
			'FROM omoccurrences '.
			'WHERE collid = '.$this->collid.' ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$coords = ($r->decimallatitude && $r->decimallongitude);
            if($coords){
                $coord++;
            }
            else{
                $noCoord++;
                if($r->verbatimcoordinates){
                    $noCoordVerbatim++;
                }
                else{
                    $noCoordNoVerbatim++;
                }
            }
		}
		$rs->free();

        $retArr['coord'] = $coord;
        $retArr['noCoord'] = $noCoord;
        $retArr['noCoord_verbatim'] = $noCoordVerbatim;
        $retArr['noCoord_noVerbatim'] = $noCoordNoVerbatim;

		return $retArr;
	}

	public function getUnverifiedByCountry(): array
	{
		$retArr = array();
		$sql = 'SELECT country, COUNT(occid) AS cnt '.
			'FROM omoccurrences '.
			'WHERE (collid = '.$this->collid.') AND (decimallatitude IS NOT NULL) AND (decimallongitude IS NOT NULL) AND country IS NOT NULL '.
			'AND (occid NOT IN(SELECT occid FROM omoccurverification WHERE category = "coordinate")) '.
			'GROUP BY country';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->country] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function verifyCoordAgainstPolitical($queryCountry): void
	{
		echo '<ul>';
		echo '<li>Starting coordinate crawl...</li>';
		$sql = 'SELECT occid, country, stateprovince, county, decimallatitude, decimallongitude '.
			'FROM omoccurrences '.
			'WHERE (collid = '.$this->collid.') AND (decimallatitude IS NOT NULL) AND (decimallongitude IS NOT NULL) AND (country = "'.Sanitizer::cleanInStr($this->conn,$queryCountry).'") '.
			'AND (occid NOT IN(SELECT occid FROM omoccurverification WHERE category = "coordinate")) '.
			'LIMIT 500';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			echo '<li>Checking occurrence <a href="../editor/occurrenceeditor.php?occid='.$r->occid.'" target="_blank">'.$r->occid.'</a>...</li>';
			$geocodedUnits = $this->callOSMApi($r->decimallatitude, $r->decimallongitude);
			$ranking = 0;
			$protocolStr = '';
			if(isset($geocodedUnits['country'])){
				if($this->countryUnitsEqual($geocodedUnits['country'],$r->country)){
					$ranking = 2;
					$protocolStr = 'OpenStreetMapApiMatch:countryEqual';
					if(isset($geocodedUnits['state'])){
						if($this->unitsEqual($geocodedUnits['state'], $r->stateprovince)){
							$ranking = 5;
							$protocolStr = 'OpenStreetMapApiMatch:stateEqual';
							if(isset($geocodedUnits['county'])){
								if($this->countyUnitsEqual($geocodedUnits['county'], $r->county)){
									$ranking = 7;
									$protocolStr = 'OpenStreetMapApiMatch:countyEqual';
								}
								else{
									echo '<li style="margin-left:15px;">County not equal (source: '.$r->county.'; OpenStreetMap value: '.$geocodedUnits['county'].')</li>';
								}
							}
							else{
								echo '<li style="margin-left:15px;">County not provided by OpenStreetMap</li>';
							}
						}
						else{
							echo '<li style="margin-left:15px;">State/Province not equal (source: '.$r->stateprovince.'; OpenStreetMap value: '.$geocodedUnits['state'].')</li>';
						}
					}
					else{
						echo '<li style="margin-left:15px;">State/Province not provided by OpenStreetMap</li>';
					}
				}
				else{
					echo '<li style="margin-left:15px;">Country not equal (source: '.$r->country.'; OpenStreetMap value: '.$geocodedUnits['country'].')</li>';
				}
			}
			else{
				echo '<li style="margin-left:15px;">Country not provided by OpenStreetMap</li>';
			}
			if($ranking){
				$this->setVerification($r->occid, 'coordinate', $ranking, $protocolStr);
				echo '<li style="margin-left:15px;">Verification status set (rank: '.$ranking.', '.$protocolStr.')</li>';
			}
			else{
				echo '<li style="margin-left:15px;">Unable to set verification status</li>';
			}
			flush();
		}
		$rs->free();
	}

	private function callOSMApi($lat, $lng): array
	{
		$retArr = array();
		$url = 'https://nominatim.openstreetmap.org/reverse?lat='.$lat.'&lon='.$lng.'&format=json';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $url);
        $data = curl_exec($curl);
		curl_close($curl);
        $dataObj = json_decode($data, true);
		if(array_key_exists('address',$dataObj)){
			$addressArr = $dataObj['address'];
			$retArr['country'] = $addressArr['country'];
            $retArr['state'] = $addressArr['state'];
            $retArr['county'] = $addressArr['county'];
		}
		elseif(array_key_exists('error',$dataObj)){
			echo '<li style="margin-left:15px;">Error in getting return from OpenStreetMap API (error: '.$dataObj['error'].')</li>';
		}
		else{
            echo '<li style="margin-left:15px;">Unable to get results from OpenStreetMap API</li>';
        }
		return $retArr;
	}

	private function unitsEqual($osmTerm, $dbTerm): bool
	{
        $osmTerm = strtolower(trim($osmTerm));
		$dbTerm = strtolower(trim($dbTerm));

		return $osmTerm === $dbTerm;
	}

	private function countryUnitsEqual($countryOSM,$countryDb): bool
	{
        $status = false;
		if(!$this->unitsEqual($countryOSM,$countryDb)) {
            $countryOSM = strtolower(trim($countryOSM));
            $countryDb = strtolower(trim($countryDb));

            $synonymArr = array();
            $synonymArr[] = array('united states','usa','united states of america','u.s.a.');

            foreach($synonymArr as $synArr){
                if(in_array($countryOSM, $synArr, true) && in_array($countryDb, $synArr, true)) {
                    $status = true;
                }
            }
		}
        return $status;
	}

	private function countyUnitsEqual($countyOSM,$countyDb): bool
	{
        $countyOSM = strtolower(trim($countyOSM));
		$countyDb = strtolower(trim($countyDb));

        $countyOSM = trim(str_replace(array('county','parish'), '', $countyOSM));
		return strpos($countyDb, $countyOSM) !== false;
	}

	private function setVerification($occid, $category, $ranking, $protocol = null, $source = null, $notes = null): void
	{
		$sql = 'INSERT INTO omoccurverification(occid, category, ranking, protocol, source, notes, uid) '.
			'VALUES('.$occid.',"'.$category.'",'.$ranking.','.
			($protocol?'"'.$protocol.'"':'NULL').','.
			($source?'"'.$source.'"':'NULL').','.
			($notes?'"'.$notes.'"':'NULL').','.
			$GLOBALS['SYMB_UID'].')';
		if(!$this->conn->query($sql)){
			$this->errorMessage = 'ERROR thrown setting occurrence verification';
			echo '<li style="margin-left:15px;">'.$this->errorMessage.'</li>';
		}
	}

	public function getOccurrenceRankingArr($category, $ranking): array
	{
		$retArr = array();
		if(is_numeric($ranking)){
			$sql = 'SELECT DISTINCT v.occid, l.username, v.initialtimestamp '.
				'FROM omoccurverification AS v INNER JOIN omoccurrences AS o ON v.occid = o.occid '.
				'INNER JOIN users AS l ON v.uid = l.uid '.
				'WHERE (o.collid = '.$this->collid.') AND (v.category = "'.Sanitizer::cleanInStr($this->conn,$category).'") AND (ranking = '.$ranking.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->occid]['username'] = $r->username;
				$retArr[$r->occid]['ts'] = $r->initialtimestamp;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getRankList(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT v.ranking FROM omoccurverification AS v INNER JOIN omoccurrences AS o ON v.occid = o.occid WHERE (o.collid = '.$this->collid.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[] = $r->ranking;
		}
		$rs->free();
		sort($retArr);
		return $retArr;
	}

	public function updateField($fieldName, $oldValue, $newValue, $conditionArr = null): bool
	{
		if(is_numeric($this->collid) && $fieldName && $newValue){
			$editorManager = new OccurrenceEditorManager($this->conn);
			$qryArr = array('cf1'=>'collid','ct1'=>'EQUALS','cv1'=>$this->collid);
			if($conditionArr){
				$cnt = 2;
				foreach($conditionArr as $k => $v){
					$qryArr['cf'.$cnt] = $k;
					if($v === '--ISNULL--'){
						$qryArr['ct'.$cnt] = 'NULL';
						$qryArr['cv'.$cnt] = '';
					}
					else{
						$qryArr['ct'.$cnt] = 'EQUALS';
						$qryArr['cv'.$cnt] = $v;
					}
					$cnt++;
					if($cnt > 4) {
						break;
					}
				}
			}
			$editorManager->setQueryVariables($qryArr);
			$editorManager->setSqlWhere();
			$editorManager->batchUpdateField($fieldName,$oldValue,$newValue,false);
		}
		return true;
	}

	public function setCollId($collid): void
	{
		if(is_numeric($collid)){
			$this->collid = $collid;
		}
	}

    public function setObsuid($obsUid): void
    {
        if(is_numeric($obsUid)){
            $this->obsUid = $obsUid;
        }
    }

	public function getFeatureCount(): int
	{
		return $this->featureCount;
	}

	public function getCollMap(): array
	{
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT CONCAT_WS("-",c.institutioncode, c.collectioncode) AS code, c.collectionname, '.
				'c.icon, c.colltype, c.managementtype '.
				'FROM omcollections AS c '.
				'WHERE (c.collid = '.$this->collid.') ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($row = $rs->fetch_object()){
				$retArr['code'] = $row->code;
				$retArr['collectionname'] = $row->collectionname;
				$retArr['icon'] = ($GLOBALS['CLIENT_ROOT'] && strncmp($row->icon, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->icon) : $row->icon;
				$retArr['colltype'] = $row->colltype;
				$retArr['managementtype'] = $row->managementtype;
			}
			$rs->free();
		}
		return $retArr;
	}
}
