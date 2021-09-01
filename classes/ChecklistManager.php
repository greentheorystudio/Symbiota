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
	private $thesFilter = 0;
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
					$retStr = '<h1>ERROR: invalid checklist identifier supplied ('.$clValue.')</h1>';
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

	public function getTaxonAuthorityList(): array
	{
    	$taxonAuthList = array();
		$sql = 'SELECT ta.taxauthid, ta.name FROM taxauthority ta WHERE (ta.isactive <> 0)';
 		$rs = $this->conn->query($sql);
		while ($row = $rs->fetch_object()){
			$taxonAuthList[$row->taxauthid] = $row->name;
		}
		$rs->free();
		return $taxonAuthList;
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
                        if(in_array('x',$taxonTokens, true)) {
                            $index = array_search('x', $taxonTokens, true);
                            if(is_string($index) || is_int($index)){
                                unset($taxonTokens[$index]);
                            }
                        }
                        if(in_array('X',$taxonTokens, true)) {
                            $index = array_search('X', $taxonTokens, true);
                            if(is_string($index) || is_int($index)){
                                unset($taxonTokens[$index]);
                            }
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
                        $collector .= ' ['.$row->institutioncode.']';
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
            }
            return $this->taxaList;
		}
    }

	private function setImages($tidReturn): void
	{
		if($tidReturn){
			$sql = 'SELECT i2.tid, i.url, i.thumbnailurl FROM images i INNER JOIN '.
				'(SELECT ts1.tid, SUBSTR(MIN(CONCAT(LPAD(i.sortsequence,6,"0"),i.imgid)),7) AS imgid '.
				'FROM taxstatus ts1 INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
				'INNER JOIN images i ON ts2.tid = i.tid '.
				'WHERE i.sortsequence < 500 AND ts1.taxauthid = 1 AND ts2.taxauthid = 1 '.
				'AND (ts1.tid IN('.implode(',',$tidReturn).')) '.
				'GROUP BY ts1.tid) i2 ON i.imgid = i2.imgid';
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
				$sql2 = 'SELECT i2.tid, i.url, i.thumbnailurl FROM images i INNER JOIN '.
					'(SELECT ts1.parenttid AS tid, SUBSTR(MIN(CONCAT(LPAD(i.sortsequence,6,"0"),i.imgid)),7) AS imgid '.
					'FROM taxstatus ts1 INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
					'INNER JOIN images i ON ts2.tid = i.tid '.
					'WHERE i.sortsequence < 500 AND ts1.taxauthid = 1 AND ts2.taxauthid = 1 '.
					'AND (ts1.parenttid IN('.implode(',',$missingArr).')) '.
					'GROUP BY ts1.tid) i2 ON i.imgid = i2.imgid';
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
			$sql = 'SELECT ts1.tid, v.vernacularname '.
				'FROM taxstatus ts1 INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.tidaccepted '.
				'INNER JOIN taxavernaculars v ON ts2.tid = v.tid '.
				'WHERE ts1.taxauthid = 1 AND ts2.taxauthid = 1 AND (ts1.tid IN('.implode(',',$tidReturn).')) ';
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

	public function getCoordinates($tid = null, $abbreviated = null): array
	{
		$retArr = array();
		if(!$this->basicSql) {
			$this->setClSql();
		}
		if($this->clid){
			$clidStr = $this->clid;
			if($this->childClidArr){
				$clidStr .= ','.implode(',',$this->childClidArr);
			}

			$retCnt = 0;
            if($tid){
                $sql1 = 'SELECT DISTINCT cc.tid, t.sciname, cc.decimallatitude, cc.decimallongitude, cc.notes '.
                    'FROM fmchklstcoordinates cc INNER JOIN taxa t ON cc.tid = t.tid '.
                    'WHERE cc.tid = '.$tid.' AND cc.clid IN ('.$clidStr.') AND cc.decimallatitude IS NOT NULL AND cc.decimallongitude IS NOT NULL ';
            }
            else{
                $sql1 = 'SELECT DISTINCT cc.tid, t.sciname, cc.decimallatitude, cc.decimallongitude, cc.notes '.
                    'FROM fmchklstcoordinates cc INNER JOIN ('.$this->basicSql.') t ON cc.tid = t.tid '.
                    'WHERE cc.clid IN ('.$clidStr.') AND cc.decimallatitude IS NOT NULL AND cc.decimallongitude IS NOT NULL ';
            }
            if($abbreviated){
                $sql1 .= 'ORDER BY RAND() LIMIT 50';
            }
			try{
				//echo $sql1;
				$rs1 = $this->conn->query($sql1);
				if($rs1){
					while($r1 = $rs1->fetch_object()){
						if($abbreviated){
							$retArr[] = $r1->decimallatitude.','.$r1->decimallongitude;
						}
						else{
							$retArr[$r1->tid][] = array('ll'=>$r1->decimallatitude.','.$r1->decimallongitude,'sciname'=>Sanitizer::cleanOutStr($r1->sciname),'notes'=>Sanitizer::cleanOutStr($r1->notes));
						}
						$retCnt++;
					}
					$rs1->free();
				}
			}
			catch(Exception $e){
				echo 'Caught exception getting general coordinates: ',  $e->getMessage(), "\n";
			}

			if(!$abbreviated || $retCnt < 50){
                if($tid){
                    $sql2 = 'SELECT DISTINCT v.tid, o.occid, o.decimallatitude, o.decimallongitude, '.
                        'CONCAT(o.recordedby," (",IFNULL(o.recordnumber,o.eventdate),")") as notes '.
                        'FROM omoccurrences o INNER JOIN fmvouchers v ON o.occid = v.occid '.
                        'WHERE v.tid = '.$tid.' AND v.clid IN ('.$clidStr.') AND o.decimallatitude IS NOT NULL AND o.decimallongitude IS NOT NULL '.
                        'AND (o.localitysecurity = 0 OR o.localitysecurity IS NULL) ';
                }
                else{
                    $sql2 = 'SELECT DISTINCT v.tid, o.occid, o.decimallatitude, o.decimallongitude, '.
                        'CONCAT(o.recordedby," (",IFNULL(o.recordnumber,o.eventdate),")") as notes '.
                        'FROM omoccurrences o INNER JOIN fmvouchers v ON o.occid = v.occid '.
                        'INNER JOIN ('.$this->basicSql.') t ON v.tid = t.tid '.
                        'WHERE v.clid IN ('.$clidStr.') AND o.decimallatitude IS NOT NULL AND o.decimallongitude IS NOT NULL '.
                        'AND (o.localitysecurity = 0 OR o.localitysecurity IS NULL) ';
                }
                if($abbreviated){
                    $sql2 .= 'ORDER BY RAND() LIMIT 50';
                }
			    try{
					//echo $sql2;
					$rs2 = $this->conn->query($sql2);
					if($rs2){
						while($r2 = $rs2->fetch_object()){
							if($abbreviated){
								$retArr[] = $r2->decimallatitude.','.$r2->decimallongitude;
							}
							else{
								$retArr[$r2->tid][] = array('ll'=>$r2->decimallatitude.','.$r2->decimallongitude,'notes'=>Sanitizer::cleanOutStr($r2->notes),'occid'=>$r2->occid);
							}
						}
						$rs2->free();
					}
				}
				catch(Exception $e){
                    echo 'Caught exception getting general coordinates: ',  $e->getMessage(), "\n";
                }
			}
		}
		return $retArr;
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
				$this->basicSql = 'SELECT DISTINCT t.tid, IFNULL(ctl.familyoverride,ts.family) AS family, '.
					't.sciname, t.author, ctl.habitat, ctl.abundance, ctl.notes, ctl.source '.
					'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tidaccepted '.
					'INNER JOIN fmchklsttaxalink ctl ON ts.tid = ctl.tid '.
			  		'WHERE (ts.taxauthid = '.$this->thesFilter.') AND (ctl.clid IN ('.$clidStr.')) ';
			}
			else{
				$this->basicSql = 'SELECT DISTINCT t.tid, IFNULL(ctl.familyoverride,ts.family) AS family, '.
					't.sciname, t.author, ctl.habitat, ctl.abundance, ctl.notes, ctl.source '.
					'FROM taxa t INNER JOIN fmchklsttaxalink ctl ON t.tid = ctl.tid '.
					'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			  		'WHERE (ts.taxauthid = 1) AND (ctl.clid IN ('.$clidStr.')) ';
			}
		}
		else{
			$this->basicSql = 'SELECT DISTINCT t.tid, ts.family, t.sciname, t.author '.
				'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'INNER JOIN fmdyncltaxalink ctl ON t.tid = ctl.tid '.
    	  		'WHERE (ts.taxauthid = '.($this->thesFilter?:'1').') AND (ctl.dynclid = '.$this->dynClid.') ';
		}
		if($this->taxonFilter){
			if($this->searchCommon){
				$this->basicSql .= 'AND ts.tidaccepted IN(SELECT ts2.tidaccepted FROM taxavernaculars v INNER JOIN taxstatus ts2 ON v.tid = ts2.tid WHERE (v.vernacularname LIKE "%'.$this->taxonFilter.'%")) ';
			}
			else{
				$sqlWhere = 'OR (t.SciName Like "'.$this->taxonFilter.'%") ';
				if($this->clid && (substr($this->taxonFilter,-5) === 'aceae' || substr($this->taxonFilter,-4) === 'idae')){
					$sqlWhere .= "OR (ctl.familyoverride = '".$this->taxonFilter."') ";
				}
				if($this->searchSynonyms){
					$sqlWhere .= 'OR (ts.tidaccepted IN(SELECT ts2.tidaccepted FROM taxa t2 INNER JOIN taxstatus ts2 ON t2.tid = ts2.tid ' .
						"WHERE (t2.sciname Like '".$this->taxonFilter."%'))) ";
				}
				$sqlWhere .= 'OR (t.tid IN(SELECT e.tid '.
					'FROM taxa t3 INNER JOIN taxaenumtree e ON t3.tid = e.parenttid '.
					'WHERE (e.taxauthid = '.($this->thesFilter?:'1').') AND (t3.sciname = "'.$this->taxonFilter.'")))';
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
		    if($row->pid){
				$pid = $row->pid;
				$projName = $row->projname.(!$row->ispublic?' (Private)':'');
			}
			else{
				$pid = 0;
				$projName = 'Undefinded Inventory Project';
			}
            if(array_key_exists($pid,$retArr) && array_key_exists('coords',$retArr[$pid])){
                $projCoordArr = json_decode($retArr[$pid]['coords'], true, 512, JSON_THROW_ON_ERROR);
            }
            if($row->LatCentroid && $row->LongCentroid){
                $coordArr[] = (float)$row->LatCentroid;
                $coordArr[] = (float)$row->LongCentroid;
                $projCoordArr[] = $coordArr;
                $retArr[$pid]['coords'] = json_encode($projCoordArr, JSON_THROW_ON_ERROR);
            }
			$retArr[$pid]['name'] = Sanitizer::cleanOutStr($projName);
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

	public function setThesFilter($filt): void
	{
		$this->thesFilter = (int)$filt;
	}

	public function getThesFilter(): int
	{
		return $this->thesFilter;
	}

	public function setTaxonFilter($tFilter): void
	{
		$this->taxonFilter = Sanitizer::cleanInStr(strtolower($tFilter));
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
			$sql .= 'WHERE (projname = "'.Sanitizer::cleanInStr($pValue).'")';
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
