<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class ImageExplorer{
    private $debug = FALSE;
	private $conn;
	private $imgCnt = 0;

	public function __construct(){
		$connection = new DbService();
		$this->conn = $connection->getConnection();
	}
 
	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function getImages($searchCriteria): array
	{
		$retArr = array();
        if (array_key_exists('taxon',$searchCriteria)) { 
           $searchCriteria['taxa'] = $searchCriteria['taxon'];
           unset($searchCriteria['taxon']);
        }
		$sql = $this->getSql($searchCriteria);
        if ($this->debug) {
        	echo "ImageExplorer.getImages sql=[$sql]";
        }
		$rs = $this->conn->query($sql);
		if($rs){
			while($r = $rs->fetch_assoc()){
				$retArr[$r['imgid']] = $r;
			}
			$rs->free();
			
			if($retArr){
				$sql2 = 'SELECT i.imgid, t.tid, t.sciname FROM images i INNER JOIN taxa t ON i.tid = t.tid '.
					'WHERE i.imgid IN('.implode(',',array_keys($retArr)).')';
				$rs2 = $this->conn->query($sql2);
				if($rs2){
					while($r2 = $rs2->fetch_object()){
						$retArr[$r2->imgid]['tid'] = $r2->tid;
						$retArr[$r2->imgid]['sciname'] = $r2->sciname;
					}
					$rs2->free();
				}
				else{
					echo 'ERROR populating assigned tid and sciname for image.';
				}
			
				$cntSql = 'SELECT count(DISTINCT i.imgid) AS cnt '.substr($sql,strpos($sql,' FROM '));
				$cntSql = substr($cntSql,0,strpos($cntSql,' LIMIT '));
				//echo '<br/>'.$cntSql.'<br/>';
				$cntRs = $this->conn->query($cntSql);
				if($cntR = $cntRs->fetch_object()){
					$this->imgCnt = $cntR->cnt;
					$retArr['cnt'] = $cntR->cnt;
				}
				$cntRs->free();
			}
            else{
                $retArr['cnt'] = 0;
            }
		}
		else{
			echo 'ERROR returning image recordset.';
		}
		return $retArr;
	}
	
	private function getSql($searchCriteria): string
	{
		$sqlWhere = '';

		if(isset($searchCriteria['taxa']) && $searchCriteria['taxa']){
			$accArr = array_unique($this->getAcceptedTid($searchCriteria['taxa']));
			if(count($accArr) === 1){
				$targetTid = array_shift($accArr);
				$sqlFrag = $this->getChildTids($targetTid);
				$sqlWhere .= 'AND (i.tid IN('.$sqlFrag.')) ';
			}
			elseif(count($accArr) > 1){
				$tidArr = array_merge($this->getTaxaChildren($accArr),$accArr);
				$tidArr = $this->getTaxaSynonyms($tidArr);
				$sqlWhere .= 'AND (i.tid IN('.implode(',',SanitizerService::cleanInArray($this->conn,$tidArr)).')) ';
			}
		}
		
		if (isset($searchCriteria['text']) && $searchCriteria['text']) {
			$sqlWhere .= 'AND o.sciname like "%'.SanitizerService::cleanInStr($this->conn,$searchCriteria['text'][0]).'%" ';
		}

		if(isset($searchCriteria['country']) && $searchCriteria['country']){
			$countryArr = SanitizerService::cleanInArray($this->conn,$searchCriteria['country']);
			$usaArr = array('usa','united states','united states of america','u.s.a','us');
			foreach($countryArr as $countryStr){
				if(in_array(strtolower($countryStr), $usaArr, true)){
					$countryArr = array_unique(array_merge($countryArr,$usaArr));
					break;
				}
			}
			$sqlWhere .= 'AND o.country IN("'.implode('","',$countryArr).'") ';
		}

		if(isset($searchCriteria['state']) && $searchCriteria['state']){
			$stateArr = SanitizerService::cleanInArray($this->conn,$searchCriteria['state']);
			$sqlWhere .= 'AND o.stateProvince IN("'.implode('","',$stateArr).'") ';
		}

		if(isset($searchCriteria['tags']) && $searchCriteria['tags']){
			$sqlWhere .= 'AND it.keyvalue IN("'.implode('","',SanitizerService::cleanInArray($this->conn,$searchCriteria['tags'])).'") ';
		}
		else{
			$sqlWhere .= 'AND i.sortsequence < 500 ';
		}
		
		if(isset($searchCriteria['collection']) && $searchCriteria['collection']){
			$sqlWhere .= 'AND o.collid IN('.implode(',',SanitizerService::cleanInArray($this->conn,$searchCriteria['collection'])).') ';
		}

		if(isset($searchCriteria['photographer']) && $searchCriteria['photographer']){
			$sqlWhere .= 'AND i.photographerUid IN('.implode(',',SanitizerService::cleanInArray($this->conn,$searchCriteria['photographer'])).') ';
		}

        $sqlStr = 'SELECT DISTINCT i.imgid, t.tidaccepted, i.url, i.thumbnailurl, i.originalurl, '.
			'u.uid, CONCAT_WS(", ",u.lastname,u.firstname) AS photographer, i.caption, '.
			'o.occid, o.stateprovince, o.catalognumber, i.initialtimestamp '.
			'FROM images AS i LEFT JOIN taxa AS t ON i.tid = t.tid '.
			'LEFT JOIN users AS u ON i.photographeruid = u.uid '.
			'LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
			'LEFT JOIN omcollections AS c ON o.collid = c.collid ';
		if(isset($searchCriteria['tags']) && $searchCriteria['tags']){
			$sqlStr .= 'LEFT JOIN imagetag AS it ON i.imgid = it.imgid ';
		}
		if($sqlWhere) {
			$sqlStr .= 'WHERE ' . substr($sqlWhere, 3);
		}
		
		if(isset($searchCriteria['countPerCategory'])){
			if($searchCriteria['countPerCategory'] === 'taxon'){
				$sqlStr .= 'GROUP BY t.tidaccepted ';
			}
			elseif($searchCriteria['countPerCategory'] === 'specimen'){
				$sqlStr .= 'GROUP BY o.occid ';
			}
		}
		
		$start = ($searchCriteria['start'] ?? 0);
		$limit = ($searchCriteria['limit'] ?? 100);
		$sqlStr .= 'LIMIT '.$start.','.$limit;

        return $sqlStr;
	}

	private function getAcceptedTid($inTidArr): array
	{
		$retArr = array();
		$sql = 'SELECT tidaccepted, tid FROM taxa WHERE tid IN('. ltrim(implode(',', $inTidArr), ',') .') ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tid] = $r->tidaccepted;
		}
		$rs->free();
		return $retArr;
	}

	private function getChildTids($inTid): string
	{
        $result = $inTid;
        $sqlInner = 'SELECT DISTINCT t.tid '.
			'FROM taxa AS t INNER JOIN taxaenumtree AS e ON t.tid = e.tid '.
			'WHERE t.tid = t.tidaccepted '.
			'AND (e.parenttid = '.$inTid.' OR t.parenttid = '.$inTid.' ) ';
		$sql = 'SELECT DISTINCT tid FROM taxa '.
			'WHERE (tidaccepted = '.$inTid.' OR tidaccepted IN('.$sqlInner.'))';
        $rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$tid = $r->tid;
			$result .= ','.$tid;
		}
		$rs->free();
        return $result;
    }

	private function getTaxaChildren($inTidArr): array
	{
		$childArr = array();
		foreach($inTidArr as $tid){
			$sql = 'SELECT DISTINCT t.tid '.
				'FROM taxa AS t INNER JOIN taxaenumtree AS e ON t.tid = e.tid '.
				'WHERE t.tid = t.tidaccepted '.
				'AND (e.parenttid = '.$tid.' OR t.parenttid = '.$tid.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$childArr[] = $r->tid;
			}
			$rs->free();
		}
		return array_unique($childArr);
	} 

	private function getTaxaSynonyms($inTidArr): array
	{
		$synArr = array();
		$searchStr = implode(',',$inTidArr);
		$sql = 'SELECT tid, tidaccepted '.
			'FROM taxa '.
			'WHERE tidaccepted IN('.$searchStr.') ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$synArr[] = $r->tid;
			$synArr[] = $r->tidaccepted;
		}
		$rs->free();
		return array_unique($synArr);
	}
}
