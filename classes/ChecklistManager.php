<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class ChecklistManager {

	private $conn;
	private $clid;
	private $dynClid;
	private $clName;
	private $childClidArr = array();
	private $voucherArr = array();
	private $pid = '';
	private $projName = '';
	private $taxaList = array();
	private $thesFilter;
    private $showSynonyms;
	private $taxonFilter;
	private $showAuthors;
	private $showCommon;
	private $showImages;
	private $showVouchers;
	private $showAlphaTaxa;
	private $searchCommon;
	private $searchSynonyms;
	private $filterArr = array();
	private $imageLimit = 100;
	private $taxaLimit = 500;
	private $speciesCount = 0;
	private $taxaCount = 0;
	private $familyCount = 0;
	private $genusCount = 0;
	private $basicSql;

	public function __construct() {
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}

	public function setClValue($clValue): string
	{
		$retStr = '';
		$clValue = $this->conn->real_escape_string($clValue);
		if(is_numeric($clValue)){
			$this->clid = $clValue;
		}
		else{
			$sql = 'SELECT c.clid FROM fmchecklists c WHERE (c.Name = "'.$clValue.'")';
			$rs = $this->conn->query($sql);
			if($rs){
				if($row = $rs->fetch_object()){
					$this->clid = $row->clid;
				}
				else{
					$retStr = '<h2>ERROR: invalid checklist identifier supplied ('.$clValue.')</h2>';
				}
				$rs->free();
			}
		}
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
		return $retStr;
	}

	public function setDynClid($did): void
	{
		if(is_numeric($did)){
			$this->dynClid = $did;
		}
	}

	public function getClMetaData(): array
	{
		$retArr = array();
		$sql = '';
		if($this->clid){
			$sql = 'SELECT c.clid, c.name, c.locality, c.publication, ' .
				'c.abstract, c.authors, c.parentclid, c.notes, ' .
				'c.latcentroid, c.longcentroid, c.pointradiusmeters, c.footprintwkt, c.access, c.defaultSettings, ' .
				'c.dynamicsql, c.datelastmodified, c.uid, c.type, c.initialtimestamp ' .
				'FROM fmchecklists c WHERE (c.clid = ' .$this->clid.')';
		}
		elseif($this->dynClid){
			$sql = 'SELECT c.dynclid AS clid, c.name, c.details AS locality, c.notes, c.uid, c.type, c.initialtimestamp ' .
				'FROM fmdynamicchecklists c WHERE (c.dynclid = ' .$this->dynClid.')';
		}
		if($sql){
		 	$result = $this->conn->query($sql);
			if($result){
		 		if($row = $result->fetch_object()){
					$this->clName = $row->name;
					$retArr['locality'] = $row->locality;
					$retArr['notes'] = $row->notes;
					$retArr['type'] = $row->type;
					if($this->clid){
						$retArr['publication'] = $row->publication;
						$retArr['abstract'] = $row->abstract;
						$retArr['authors'] = $row->authors;
						$retArr['parentclid'] = $row->parentclid;
						$retArr['uid'] = $row->uid;
						$retArr['latcentroid'] = $row->latcentroid;
						$retArr['longcentroid'] = $row->longcentroid;
						$retArr['pointradiusmeters'] = $row->pointradiusmeters;
						$retArr['footprintwkt'] = $row->footprintwkt;
						$retArr['access'] = $row->access;
						$retArr['defaultSettings'] = $row->defaultSettings;
						$retArr['dynamicsql'] = $row->dynamicsql;
						$retArr['datelastmodified'] = $row->datelastmodified;
					}
		    	}
		    	$result->free();
			}
		}
		return $retArr;
	}

	public function getTaxaList($pageNumber, $retLimit){
		if($this->clid || $this->dynClid) {
            $speciesPrev = '';
            $taxonPrev = '';
            $tidReturn = array();
            $genusCntArr = array();
            $familyCntArr = array();
            if($this->showImages && $retLimit) {
                $retLimit = $this->imageLimit;
            }
            if(!$this->basicSql) {
                $this->setClSql();
            }
            $result = $this->conn->query($this->basicSql);
            while($row = $result->fetch_object()){
                $family = strtoupper($row->family);
                if(!$family) {
                    $family = 'Family Incertae Sedis';
                }
                $this->filterArr[$family] = '';
                $tid = $row->tid;
                $sciName = Sanitizer::cleanOutStr($row->sciname);
                $taxonTokens = explode(' ',$sciName);
                if($taxonTokens){
                    if(in_array('x', $taxonTokens, true) || in_array('X', $taxonTokens, true)){
                        $index = in_array('x', $taxonTokens, true) ? array_search('x', $taxonTokens, true) : array_search('X', $taxonTokens, true);
                        if(is_string($index) || is_int($index)){
                            unset($taxonTokens[$index]);
                        }
                        $newArr = array();
                        foreach($taxonTokens as $v){
                            $newArr[] = $v;
                        }
                        $taxonTokens = $newArr;
                    }
                    if(!$retLimit || ($this->taxaCount >= (($pageNumber-1)*$retLimit) && $this->taxaCount <= ($pageNumber)*$retLimit)){
                        if(count($taxonTokens) === 1) {
                            $sciName .= ' sp.';
                        }
                        if($this->showVouchers){
                            $clStr = '';
                            if($row->habitat) {
                                $clStr = ', ' . $row->habitat;
                            }
                            if($row->abundance) {
                                $clStr .= ', ' . $row->abundance;
                            }
                            if($row->notes) {
                                $clStr .= ', ' . $row->notes;
                            }
                            if($row->source) {
                                $clStr .= ', <u>source</u>: ' . $row->source;
                            }
                            if($clStr) {
                                $this->taxaList[$tid]['notes'] = substr($clStr, 2);
                            }
                        }
                        $this->taxaList[$tid]['sciname'] = $sciName;
                        $this->taxaList[$tid]['family'] = $family;
                        $tidReturn[] = $tid;
                        if($this->showAuthors){
                            $this->taxaList[$tid]['author'] = Sanitizer::cleanOutStr($row->author);
                        }
                    }
                    if(!in_array($family, $familyCntArr, true)){
                        $familyCntArr[] = $family;
                    }
                    if(!in_array($taxonTokens[0], $genusCntArr, true)){
                        $genusCntArr[] = $taxonTokens[0];
                    }
                    $this->filterArr[$taxonTokens[0]] = '';
                    if(count($taxonTokens) > 1 && $taxonTokens[0]. ' ' .$taxonTokens[1] !== $speciesPrev){
                        $this->speciesCount++;
                        $speciesPrev = $taxonTokens[0]. ' ' .$taxonTokens[1];
                    }
                    if(!$taxonPrev || $sciName !== $taxonPrev){
                        $this->taxaCount++;
                    }
                    $taxonPrev = implode(' ',$taxonTokens);
                }
            }
            $this->familyCount = count($familyCntArr);
            $this->genusCount = count($genusCntArr);
            $this->filterArr = array_keys($this->filterArr);
            sort($this->filterArr);
            $result->free();
            if($this->taxaCount < (($pageNumber-1)*$retLimit)){
                $this->taxaCount = 0; $this->genusCount = 0; $this->familyCount = 0;
                unset($this->filterArr);
                return $this->getTaxaList(1,$retLimit);
            }
            if($this->taxaList){
                if($this->showVouchers){
                    $clidStr = $this->clid;
                    if($this->childClidArr){
                        $clidStr .= ','.implode(',',$this->childClidArr);
                    }
                    $vSql = 'SELECT DISTINCT v.tid, v.occid, c.institutioncode, v.notes, '.
                        'o.catalognumber, o.recordedby, o.recordnumber, o.eventdate '.
                        'FROM fmvouchers v INNER JOIN omoccurrences o ON v.occid = o.occid '.
                        'INNER JOIN omcollections c ON o.collid = c.collid '.
                        'WHERE (v.clid IN ('.$clidStr.')) AND v.tid IN('.implode(',',array_keys($this->taxaList)).')';
                    //echo $vSql; exit;
                    $vResult = $this->conn->query($vSql);
                    while ($row = $vResult->fetch_object()){
                        $collector = ($row->recordedby?:$row->catalognumber);
                        if(strlen($collector) > 25){
                            $strPos = strpos($collector,';');
                            if(!$strPos) {
                                $strPos = strpos($collector, ',');
                            }
                            if(!$strPos) {
                                $strPos = strpos($collector, ' ', 10);
                            }
                            if($strPos) {
                                $collector = substr($collector, 0, $strPos) . '...';
                            }
                        }
                        if($row->recordnumber) {
                            $collector .= ' ' . $row->recordnumber;
                        }
                        else {
                            $collector .= ' ' . $row->eventdate;
                        }
                        if($row->institutioncode){
                            $collector .= ' ['.$row->institutioncode.']';
                        }
                        $this->voucherArr[$row->tid][$row->occid] = $collector;
                    }
                    $vResult->close();
                }
                if($this->showImages) {
                    $this->setImages($tidReturn);
                }
                if($this->showCommon) {
                    $this->setVernaculars($tidReturn);
                }
                if($this->showSynonyms) {
                    $this->setSynonyms();
                }
            }
            return $this->taxaList;
		}
    }

	private function setImages($tidReturn): void
	{
		if($tidReturn){
			$sql = 'SELECT i2.tid, i.url, i.thumbnailurl FROM images AS i INNER JOIN '.
				'(SELECT t.tidaccepted AS tid, SUBSTR(MIN(CONCAT(LPAD(i.sortsequence,6,"0"),i.imgid)),7) AS imgid '.
				'FROM taxa AS t INNER JOIN images AS i ON t.tid = i.tid '.
				'WHERE i.sortsequence < 500 '.
				'AND t.tidaccepted IN('.implode(',',$tidReturn).') '.
				'GROUP BY t.tidaccepted) AS i2 ON i.imgid = i2.imgid';
			//echo $sql;
			$rs = $this->conn->query($sql);
			$matchedArr = array();
			while($row = $rs->fetch_object()){
				$this->taxaList[$row->tid]['url'] = $row->url;
				$this->taxaList[$row->tid]['tnurl'] = $row->thumbnailurl;
				$matchedArr[] = $row->tid;
			}
			$rs->free();
			$missingArr = array_diff(array_keys($this->taxaList),$matchedArr);
			if($missingArr){
				$sql2 = 'SELECT i2.tid, i.url, i.thumbnailurl FROM images AS i INNER JOIN '.
					'(SELECT t.parenttid AS tid, SUBSTR(MIN(CONCAT(LPAD(i.sortsequence,6,"0"),i.imgid)),7) AS imgid '.
					'FROM taxa AS t INNER JOIN images AS i ON t.tid = i.tid '.
					'WHERE i.sortsequence < 500 '.
					'AND t.parenttid IN('.implode(',',$missingArr).') '.
					'GROUP BY t.tid) AS i2 ON i.imgid = i2.imgid';
				//echo $sql;
				$rs2 = $this->conn->query($sql2);
				while($row2 = $rs2->fetch_object()){
					$this->taxaList[$row2->tid]['url'] = $row2->url;
					$this->taxaList[$row2->tid]['tnurl'] = $row2->thumbnailurl;
				}
				$rs2->free();
			}
		}
	}

	private function setVernaculars($tidReturn): void
	{
		if($tidReturn){
			$sql = 'SELECT t.tid, v.vernacularname '.
				'FROM taxa AS t INNER JOIN taxavernaculars AS v ON t.tidaccepted = v.tid '.
				'WHERE t.tid IN('.implode(',',$tidReturn).') ';
			$sql .= 'ORDER BY v.sortsequence DESC ';
			//echo $sql; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if($r->vernacularname) {
					$this->taxaList[$r->tid]['vern'] = Sanitizer::cleanOutStr($r->vernacularname);
				}
			}
			$rs->free();
		}
	}

    private function setSynonyms(): void
    {
        if($this->taxaList){
            $tempArr = array();
            $sql = 'SELECT t.tid, t2.sciname, t2.author '.
                'FROM taxa AS t INNER JOIN taxa AS t2 ON t.tidaccepted = t2.tid '.
                'WHERE t.tid IN('.implode(',',array_keys($this->taxaList)).') AND t.tid <> t2.tid '.
                'ORDER BY t2.sciname';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $tempArr[$r->tid][] = '<i>'.$r->sciname.'</i>'.($this->showAuthors && $r->author?' '.$r->author:'');
            }
            $rs->free();
            foreach($tempArr as $k => $vArr){
                $this->taxaList[$k]['syn'] = implode(', ',$vArr);
            }
        }
    }

	public function downloadChecklistCsv(): void
	{
    	if(!$this->basicSql) {
			$this->setClSql();
		}
		$fileName = $this->clName. '_' .time(). '.csv';
    	header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header ('Content-Type: text/csv');
		header ("Content-Disposition: attachment; filename=\"$fileName\"");
		$this->showAuthors = 1;
		if($taxaArr = $this->getTaxaList(1,0)){
			$fh = fopen('php://output', 'wb');
			$headerArr = array('Family','ScientificName','ScientificNameAuthorship');
			if($this->showCommon) {
				$headerArr[] = 'CommonName';
			}
			$headerArr[] = 'Notes';
			$headerArr[] = 'TaxonId';
			fputcsv($fh,$headerArr);
			foreach($taxaArr as $tid => $tArr){
				unset($outArr);
				$outArr = array($tArr['family'],$tArr['sciname'],$tArr['author']);
				if($this->showCommon) {
					$outArr[] = (array_key_exists('vern', $tArr) ? $tArr['vern'] : '');
				}
				$outArr[] = (array_key_exists('notes',$tArr)?strip_tags($tArr['notes']):'');
				$outArr[] = $tid;
				fputcsv($fh,$outArr);
			}
			fclose($fh);
		}
		else{
			echo "Recordset is empty.\n";
		}
    }

	private function setClSql(): void
	{
		if($this->clid){
			$clidStr = $this->clid;
			if($this->childClidArr){
				$clidStr .= ','.implode(',',$this->childClidArr);
			}
            if($this->thesFilter){
                $this->basicSql = 'SELECT DISTINCT t2.tid, IFNULL(ctl.familyoverride,t2.family) AS family, '.
                    't2.sciname, t2.author, ctl.habitat, ctl.abundance, ctl.notes, ctl.source '.
                    'FROM fmchklsttaxalink AS ctl INNER JOIN taxa AS t ON ctl.tid = t.tid '.
                    'INNER JOIN taxa AS t2 ON t.tidaccepted = t2.tid '.
                    'WHERE ctl.clid IN('.$clidStr.') ';
            }
            else{
                $this->basicSql = 'SELECT DISTINCT t.tid, IFNULL(ctl.familyoverride,t.family) AS family, '.
                    't.sciname, t.author, ctl.habitat, ctl.abundance, ctl.notes, ctl.source '.
                    'FROM taxa AS t INNER JOIN fmchklsttaxalink AS ctl ON t.tid = ctl.tid '.
                    'WHERE ctl.clid IN('.$clidStr.') ';
            }
		}
		else{
			$this->basicSql = 'SELECT DISTINCT t.tid, t.family, t.sciname, t.author '.
				'FROM taxa AS t INNER JOIN fmdyncltaxalink AS ctl ON t.tid = ctl.tid '.
    	  		'WHERE ctl.dynclid = '.$this->dynClid.' ';
		}
		if($this->taxonFilter){
			if($this->searchCommon){
				$this->basicSql .= 'AND t.tidaccepted IN(SELECT t.tidaccepted FROM taxavernaculars AS v INNER JOIN taxa AS t ON v.tid = t.tid WHERE v.vernacularname LIKE "%'.$this->taxonFilter.'%") ';
			}
			else{
				$sqlWhere = 'OR (t.SciName Like "'.$this->taxonFilter.'%") ';
				if($this->clid && (substr($this->taxonFilter,-5) === 'aceae' || substr($this->taxonFilter,-4) === 'idae')){
					$sqlWhere .= "OR (ctl.familyoverride = '".$this->taxonFilter."') ";
				}
				if($this->searchSynonyms){
					$sqlWhere .= 'OR (t.tidaccepted IN(SELECT tidaccepted FROM taxa ' .
						"WHERE sciname LIKE '".$this->taxonFilter."%')) ";
				}
				$sqlWhere .= 'OR (t.tid IN(SELECT e.tid '.
					'FROM taxa AS t3 INNER JOIN taxaenumtree AS e ON t3.tid = e.parenttid '.
					'WHERE (t3.sciname = "'.$this->taxonFilter.'")))';
				if($sqlWhere) {
					$this->basicSql .= 'AND (' . substr($sqlWhere, 2) . ') ';
				}
			}
		}
		if($this->showAlphaTaxa){
			$this->basicSql .= ' ORDER BY sciname';
		}
		else{
			$this->basicSql .= ' ORDER BY family, sciname';
		}
		//echo $this->basicSql; exit;
	}

	public function getChecklists(): array
	{
		$retArr = array();
		$sql = 'SELECT p.pid, p.projname, p.ispublic, c.clid, c.name, c.access, c.LatCentroid, c.LongCentroid '.
			'FROM fmchecklists AS c LEFT JOIN fmchklstprojlink AS cpl ON c.clid = cpl.clid '.
			'LEFT JOIN fmprojects AS p ON cpl.pid = p.pid '.
			'WHERE ((c.access LIKE "public%") ';
		if(isset($GLOBALS['USER_RIGHTS']['ClAdmin']) && $GLOBALS['USER_RIGHTS']['ClAdmin']) {
			$sql .= 'OR (c.clid IN(' . implode(',', $GLOBALS['USER_RIGHTS']['ClAdmin']) . '))';
		}
		$sql .= ') AND ((p.pid IS NULL) OR (p.ispublic = 1) ';
		if(isset($GLOBALS['USER_RIGHTS']['ProjAdmin']) && $GLOBALS['USER_RIGHTS']['ProjAdmin']) {
			$sql .= 'OR (p.pid IN(' . implode(',', $GLOBALS['USER_RIGHTS']['ProjAdmin']) . '))';
		}
		$sql .= ') ';
		if($this->pid) {
			$sql .= 'AND (p.pid = ' . $this->pid . ') ';
		}
		$sql .= 'ORDER BY p.projname, c.Name';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
            $coordArr = array();
            $projCoordArr = array();
            $projName = '';
            $pid = 0;
		    if($row->pid){
				$pid = $row->pid;
				$projName = $row->projname.(!$row->ispublic?' (Private)':'');
			}
			if(array_key_exists($pid,$retArr) && array_key_exists('coords',$retArr[$pid])){
                $projCoordArr = json_decode($retArr[$pid]['coords'], true);
            }
            if($row->LatCentroid && $row->LongCentroid){
                $coordArr[] = (float)$row->LatCentroid;
                $coordArr[] = (float)$row->LongCentroid;
                $projCoordArr[] = $coordArr;
                $retArr[$pid]['coords'] = json_encode($projCoordArr);
            }
			if($projName){
                $retArr[$pid]['name'] = Sanitizer::cleanOutStr($projName);
            }
			$retArr[$pid]['clid'][$row->clid] = Sanitizer::cleanOutStr($row->name).($row->access === 'private'?' (Private)':'');
		}
		$rs->free();
		if(isset($retArr[0])){
			$tempArr = $retArr[0];
			unset($retArr[0]);
			$retArr[0] = $tempArr;
		}
		return $retArr;
	}

	public function setTaxonFilter($tFilter): void
	{
		$this->taxonFilter = Sanitizer::cleanInStr($this->conn,strtolower($tFilter));
	}

    public function setThesFilter(): void
    {
        $this->thesFilter = 1;
    }

    public function setShowSynonyms(): void
    {
        $this->showSynonyms = 1;
    }

	public function setShowAuthors(): void
	{
		$this->showAuthors = 1;
	}

	public function setShowCommon(): void
	{
		$this->showCommon = 1;
	}

	public function setShowImages(): void
	{
		$this->showImages = 1;
	}

	public function setShowVouchers(): void
	{
		$this->showVouchers = 1;
	}

	public function setShowAlphaTaxa(): void
	{
		$this->showAlphaTaxa = 1;
	}

	public function setSearchCommon(): void
	{
		$this->searchCommon = 1;
	}

	public function setSearchSynonyms(): void
	{
		$this->searchSynonyms = 1;
	}

	public function getClid(){
		return $this->clid;
	}

	public function getVoucherArr(): array
	{
		return $this->voucherArr;
	}

	public function getClName(){
		return $this->clName;
	}

	public function setProj($pValue): string
	{
		$sql = 'SELECT pid, projname FROM fmprojects ';
		if(is_numeric($pValue)){
			$sql .= 'WHERE (pid = '.$pValue.')';
		}
		else{
			$sql .= 'WHERE (projname = "'.Sanitizer::cleanInStr($this->conn,$pValue).'")';
		}
		$rs = $this->conn->query($sql);
		if($rs){
			if($r = $rs->fetch_object()){
				$this->pid = $r->pid;
				$this->projName = Sanitizer::cleanOutStr($r->projname);
			}
			$rs->free();
		}
		return $this->pid;
	}

	public function getProjName(): string
	{
		return $this->projName;
	}

	public function getPid(): string
	{
		return $this->pid;
	}

	public function getImageLimit(): int
	{
		return $this->imageLimit;
	}

	public function getTaxaLimit(): int
	{
		return $this->taxaLimit;
	}

	public function getTaxaCount(): int
	{
		return $this->taxaCount;
	}

	public function getFamilyCount(): int
	{
		return $this->familyCount;
	}

	public function getGenusCount(): int
	{
		return $this->genusCount;
	}

	public function getSpeciesCount(): int
	{
		return $this->speciesCount;
	}
}
