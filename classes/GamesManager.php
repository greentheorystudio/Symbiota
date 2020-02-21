<?php
include_once('DbConnection.php');

class GamesManager {

	private $conn;
	private $clid;
	private $clidStr;
	private $dynClid;
	private $taxonFilter;
	private $showCommon = 0;
	private $lang;

	public function __construct(){
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if(!($this->conn === null)) {
			$this->conn->close();
		}
	}
	
	public function getChecklistArr($projId = 0): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT c.clid, c.name '.
			'FROM fmchecklists c INNER JOIN fmchklstprojlink plink ON c.clid = plink.clid ';
		if($projId){
			$sql .= 'WHERE c.type = "static" AND (plink.pid = '.$projId.') ';
		}
		else{
			$sql .= 'INNER JOIN fmprojects p ON plink.pid = p.pid WHERE c.type = "static" AND p.ispublic = 1 ';
		}
		$sql .= 'ORDER BY c.name';
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->clid] = $row->name;
		}
		$rs->free();
		return $retArr;
	}

	public function setOOTD($oodID,$clid){
		global $SERVER_ROOT, $IMAGE_DOMAIN;
		$infoArr = array();
		if(!preg_match('/^[\d,]+$/',$clid)) {
			return '';
		}
		if(is_numeric($oodID)){
			$currentDate = date('Y-m-d');
			$replace = 0;
			if(file_exists($SERVER_ROOT.'/temp/ootd/'.$oodID.'_info.json')){
				$oldArr = json_decode(file_get_contents($SERVER_ROOT.'/temp/ootd/'.$oodID.'_info.json'), true);
				$lastDate = $oldArr['lastDate'];
				$lastCLID = $oldArr['clid'];
				if(($currentDate > $lastDate) || ($clid !== $lastCLID)){
					$replace = 1;
				}
			}
			else{
				$replace = 1;
			}
			
			if($replace === 1){
				$previous = array();
				if(file_exists($SERVER_ROOT.'/temp/ootd/'.$oodID.'_previous.json')){
					$previous = json_decode(file_get_contents($SERVER_ROOT.'/temp/ootd/'.$oodID.'_previous.json'), true);
					unlink($SERVER_ROOT.'/temp/ootd/'.$oodID.'_previous.json');
				}
				if(file_exists($SERVER_ROOT.'/temp/ootd/'.$oodID.'_info.json')){
					unlink($SERVER_ROOT.'/temp/ootd/'.$oodID.'_info.json');
				}
				if(file_exists($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_1.jpg')){
					unlink($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_1.jpg');
				}
				if(file_exists($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_2.jpg')){
					unlink($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_2.jpg');
				}
				if(file_exists($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_3.jpg')){
					unlink($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_3.jpg');
				}
				if(file_exists($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_4.jpg')){
					unlink($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_4.jpg');
				}
				if(file_exists($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_5.jpg')){
					unlink($SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_5.jpg');
				}
				
				$ootdInfo = array();
				$ootdInfo['lastDate'] = $currentDate;
				
				$tidArr = array();
				$sql = 'SELECT l.TID, COUNT(i.imgid) AS cnt '. 
					'FROM fmchklsttaxalink l INNER JOIN images i ON l.TID = i.tid '.
					'LEFT JOIN omoccurrences o ON i.occid = o.occid '.
					'LEFT JOIN omcollections c ON o.collid = c.collid '.
					'WHERE (l.CLID IN('.$clid.')) AND (i.occid IS NULL OR c.CollType LIKE "%Observations") '.
					'GROUP BY l.TID';
				//echo '<div>'.$sql.'</div>';
				$rs = $this->conn->query($sql);
				while($row = $rs->fetch_object()){
					if(($row->cnt > 2) && (!in_array($row->TID, $previous, true))){
						$tidArr[] = $row->TID;
					}
				}
				$rs->free();
				$k = array_rand($tidArr);
				$randTaxa = $tidArr[$k];
				if($randTaxa){
					$previous[] = $randTaxa;
					$ootdInfo['clid'] = $clid;
					
					$sql2 = 'SELECT t.TID, t.SciName, t.UnitName1, s.family '.
						'FROM taxa AS t INNER JOIN taxstatus AS s ON t.TID = s.tid '.
						'WHERE s.taxauthid = 1 AND t.TID = '.$randTaxa.' ';
					//echo '<div>'.$sql2.'</div>';
					$rs = $this->conn->query($sql2);
					while($row = $rs->fetch_object()){
						$ootdInfo['tid'] = $row->TID;
						$ootdInfo['sciname'] = $row->SciName;
						$ootdInfo['genus'] = $row->UnitName1;
						$ootdInfo['family'] = $row->family;
					}
					$rs->free();
					
					$files = array();
					$sql3 = 'SELECT i.url '.
						'FROM images i '.
						'WHERE (i.tid = '.$randTaxa.') '.
						'ORDER BY i.sortsequence ';
					//echo '<div>'.$sql.'</div>';
					$cnt = 1;
					$rs = $this->conn->query($sql3);
					while(($row = $rs->fetch_object()) && ($cnt < 6)){
						if(strpos($row->url, '/') === 0){
							if(isset($IMAGE_DOMAIN) && $IMAGE_DOMAIN){
								$file = $IMAGE_DOMAIN.$row->url;
							}
							else{
								$domain = 'http://';
								if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
									$domain = 'https://';
								}
								$domain .= $_SERVER['HTTP_HOST'];
								if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
									$domain .= ':' . $_SERVER['SERVER_PORT'];
								}
								$file = $domain.$row->url;
							}
						}
						else{
							$file = $row->url;
						}
						$newfile = $SERVER_ROOT.'/temp/ootd/'.$oodID.'_organism300_'.$cnt.'.jpg';
						if(fopen($file, 'rb')){
							copy($file, $newfile);
							$files[] = '../../temp/ootd/'.$oodID.'_organism300_'.$cnt.'.jpg';
							$cnt++;
						}
					}
					$rs->free();
					$ootdInfo['images'] = $files;
					
					if(array_diff($tidArr,$previous)){
						$fp = fopen($SERVER_ROOT.'/temp/ootd/'.$oodID.'_previous.json', 'wb');
						fwrite($fp, json_encode($previous));
						fclose($fp);
					}
					$fp = fopen($SERVER_ROOT.'/temp/ootd/'.$oodID.'_info.json', 'wb');
					fwrite($fp, json_encode($ootdInfo));
					fclose($fp);
				}
			}
			
			$infoArr = json_decode(file_get_contents($SERVER_ROOT.'/temp/ootd/'.$oodID.'_info.json'), true);
		}
		return $infoArr;
	}
	
	public function getFlashcardImages(): array
	{
		global $IMAGE_DOMAIN;
		$retArr = array();
		if($this->clid){
			if(!$this->clidStr) {
				$this->setClidStr();
			}
			$sql = 'SELECT DISTINCT t.tid, t.sciname, ts.tidaccepted '.
				'FROM taxa t INNER JOIN fmchklsttaxalink ctl ON t.tid = ctl.tid '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'WHERE (ctl.clid IN('.$this->clidStr.')) AND (ts.taxauthid = 1) ';
		}
		else{
			$sql = 'SELECT DISTINCT t.tid, t.sciname, ts.tidaccepted '.
				'FROM taxa t INNER JOIN fmdyncltaxalink ctl ON t.tid = ctl.tid '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'WHERE (ctl.dynclid = '.$this->dynClid.') AND (ts.taxauthid = 1) ';
		}
		if($this->taxonFilter) {
			$sql .= 'AND (ts.Family = "' . $this->taxonFilter . '" OR t.sciname Like "' . $this->taxonFilter . '%") ';
		}
		$sql .= 'ORDER BY RAND() LIMIT 1000 ';
		//echo 'sql: '.$sql; exit;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->tidaccepted]['tid'] = $r->tid;
				$retArr[$r->tidaccepted]['sciname'] = $r->sciname;
			}
			$rs->free();
		}

		if($retArr){
			$tidStr = implode(',',array_keys($retArr));
			$tidComplete = array();
	
			if($this->showCommon){
				$sqlV = 'SELECT ts.tidaccepted, v.vernacularname '.
					'FROM taxavernaculars v INNER JOIN taxstatus ts ON v.tid = ts.tid '.
					'WHERE v.Language = "'.$this->lang.'" AND ts.tidaccepted IN('.$tidStr.') '.
					'ORDER BY v.SortSequence';
				if($rsV = $this->conn->query($sqlV)){
					while($rV = $rsV->fetch_object()){
						if(!array_key_exists('vern',$retArr[$rV->tidaccepted])) {
							$retArr[$rV->tidaccepted]['vern'] = $rV->vernacularname;
						}
					}
					$rsV->free();
				}
			}
			
			$sqlImg = 'SELECT DISTINCT i.url, ts.tidaccepted FROM images i INNER JOIN taxstatus ts ON i.tid = ts.tid '.
				'WHERE ts.tidaccepted IN('.$tidStr.') AND i.occid IS NULL '.
				'ORDER BY i.sortsequence';
			//echo $sql;
			$rsImg = $this->conn->query($sqlImg);
			while($rImg = $rsImg->fetch_object()){
				$iCnt = 0;
				if(array_key_exists('url',$retArr[$rImg->tidaccepted])) {
					$iCnt = count($retArr[$rImg->tidaccepted]['url']);
				}
				if($iCnt < 5){
					$url = $rImg->url;
					if($IMAGE_DOMAIN && strpos($url, '/') === 0){
						$url = $IMAGE_DOMAIN.$url;
					}
					$retArr[$rImg->tidaccepted]['url'][] = $url;
				}
				else{
					$tidComplete[$rImg->tidaccepted] = $rImg->tidaccepted; 
				}
			}
			$rsImg->free();
			
			if(count($tidComplete) < count($retArr)){
				$newTidStr = implode(',',array_keys(array_diff_key($retArr,$tidComplete)));
				$sqlImg2 = 'SELECT DISTINCT i.url, ts.parenttid FROM images i INNER JOIN taxstatus ts ON i.tid = ts.tid '.
					'WHERE ts.parenttid IN('.$newTidStr.') AND i.occid IS NULL '.
					'ORDER BY i.sortsequence';
				$rsImg2 = $this->conn->query($sqlImg2);
				while($rImg2 = $rsImg2->fetch_object()){
					$iCnt = 0;
					if(array_key_exists('url',$retArr[$rImg2->parenttid])) {
						$iCnt = count($retArr[$rImg2->parenttid]['url']);
					}
					if($iCnt < 5){
						$url = $rImg2->url;
						if($IMAGE_DOMAIN && strpos($url, '/') === 0){
							$url = $IMAGE_DOMAIN.$url;
						}
						$retArr[$rImg2->parenttid]['url'][] = $url;
					}
				}
				$rsImg2->free();
			}		
		}
		
		return $retArr;
	}
	
	public function echoFlashcardTaxonFilterList(): void
	{
		$returnArr = array();
		if($this->clid || $this->dynClid){
			if($this->clid){
				if(!$this->clidStr) {
					$this->setClidStr();
				}
				$sqlFamily = 'SELECT DISTINCT IFNULL(ctl.familyoverride,ts.Family) AS family '.
					'FROM taxa t INNER JOIN taxstatus ts ON t.TID = ts.TID '.
					'INNER JOIN fmchklsttaxalink ctl ON t.TID = ctl.TID '.
					'WHERE (ts.taxauthid = 1) AND (ctl.clid IN('.$this->clidStr.')) ';
			}
			else{
				$sqlFamily = 'SELECT DISTINCT ts.Family AS family '.
					'FROM taxa t INNER JOIN taxstatus ts ON t.TID = ts.TID '.
					'INNER JOIN fmdyncltaxalink ctl ON t.TID = ctl.TID '.
					'WHERE (ts.taxauthid = 1) AND (ctl.dynclid = '.$this->dynClid.') ';
			}
			//echo $sqlFamily."<br>";
			$rsFamily = $this->conn->query($sqlFamily);
			while ($row = $rsFamily->fetch_object()){
				$returnArr[] = $row->family;
			}
			$rsFamily->free();
			
			if($this->clid){
				$sqlGenus = 'SELECT DISTINCT t.unitname1 '.
					'FROM taxa t INNER JOIN fmchklsttaxalink ctl ON t.tid = ctl.tid '.
					'WHERE (ctl.clid IN('.$this->clidStr.')) ';
			}
			else{
				$sqlGenus = 'SELECT DISTINCT t.unitname1 '.
					'FROM taxa t INNER JOIN fmdyncltaxalink ctl ON t.tid = ctl.tid '.
					'WHERE (ctl.clid = '.$this->dynClid.') ';
			}
			//echo $sqlGenus."<br>";
	 		$rsGenus = $this->conn->query($sqlGenus);
			while ($row = $rsGenus->fetch_object()){
				$returnArr[] = $row->unitname1;
			}
			$rsGenus->free();
			natcasesort($returnArr);
			$returnArr['-----------------------------------------------'] = '';
			foreach($returnArr as $value){
				echo '<option ';
				if($this->taxonFilter && $this->taxonFilter === $value){
					echo ' SELECTED';
				}
				echo '>' .$value."</option>\n";
			}
		}
	}

	public function getNameGameWordList(): array
	{
		$retArr = array();
		$sql = '';
		if($this->clid){
			$this->setClidStr();
			$sql = 'SELECT DISTINCT IFNULL(cl.familyoverride,ts.family) AS family, CONCAT_WS(" ",t.unitind1,t.unitname1,t.unitind2,t.unitname2) AS sciname '.
				'FROM fmchklsttaxalink cl INNER JOIN taxa t ON cl.tid = t.tid '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'WHERE cl.clid IN('.$this->clidStr.') AND ts.taxauthid = 1 ORDER BY RAND() LIMIT 25';
		}
		elseif($this->dynClid){
			$sql = 'SELECT DISTINCT ts.family, CONCAT_WS(" ",t.unitind1,t.unitname1,t.unitind2,t.unitname2) AS sciname '.
				'FROM fmdyncltaxalink cl INNER JOIN taxa t ON cl.tid = t.tid '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'WHERE cl.dynclid = '.$this->dynClid.' AND ts.taxauthid = 1 ORDER BY RAND() LIMIT 25';
		}
		//echo $sql.'<br/><br/>';
		if($sql){
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = array($r->sciname,$r->family);
			}
			$rs->free();
		}
		return $retArr;
	}

	private function setClidStr(): void
	{
		$clidArr = array($this->clid);
		$sqlBase = 'SELECT clidchild FROM fmchklstchildren WHERE clid IN(';
		$sql = $sqlBase.$this->clid.')';
		do{
			$childStr = '';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$clidArr[] = $r->clidchild;
				$childStr .= ','.$r->clidchild;
			}
			$sql = $sqlBase.substr($childStr,1).')';
		}
		while($childStr);
		$this->clidStr = implode(',',$clidArr);
	}
	
	public function getClid(){
		return $this->clid;
	}

	public function setClid($id): void
	{
		if(is_numeric($id)){
			$this->clid = $id;
		}
	}

	public function setDynClid($id): void
	{
		if(is_numeric($id)){
			$this->dynClid = $id;
		}
	}

	public function setTaxonFilter($tValue): void
	{
		if(preg_match('/^[\D\s]+$/',$tValue)){
			$this->taxonFilter = $tValue;
		}
	}

	public function setShowCommon($sc): void
	{
		$this->showCommon = $sc;
	}

	public function setLang($l): void
	{
		$lang = strtolower($l);
		if(strlen($lang) === 2){
			if($lang === 'en') {
				$lang = 'english';
			}
			if($lang === 'es') {
				$lang = 'spanish';
			}
			if($lang === 'fr') {
				$lang = 'french';
			}
		}
		$this->lang = $lang;
	}
	
	public function getClName(): string
	{
		$retStr = '';
		if($this->clid || $this->dynClid){
			$sql = 'SELECT name ';
			if($this->clid){
				$sql .= 'FROM fmchecklists WHERE (clid = '.$this->clid.')';
			}
			else{
				$sql .= 'FROM fmdynamicchecklists WHERE (dynclid = '.$this->dynClid.')';
			}
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$retStr = $row->name;
			}
			$rs->free();
		}
		return $retStr;
	}	
}
