<?php
include_once('DbConnection.php');
include_once('OccurrenceUtilities.php');
include_once('ChecklistVoucherAdmin.php');

class OccurrenceManager{

	protected $conn;
	protected $taxaArr = array();
	private $taxaSearchType;
	protected $searchTermsArr = array();
	protected $localSearchArr = array();
	protected $reset = 0;
	private $clName;
	private $collArrIndex = 0;

 	public function __construct($readVariables = true){
        $connection = new DbConnection();
 	    $this->conn = $connection->getConnection();
		if(array_key_exists('reset',$_REQUEST) && $_REQUEST['reset']) {
            $this->reset();
        }
		if($readVariables) {
            $this->readRequestVariables();
        }
 	}

	public function __destruct(){
 		if(!($this->conn === false)){
 			$this->conn->close();
 			$this->conn = null;
 		}
	}

	public function reset(): void
    {
		$this->reset = 1;
		if(isset($this->searchTermsArr['db']) || isset($this->searchTermsArr['oic'])){
            $dbsTemp = $this->searchTermsArr['db'] ?? '';
            $clidTemp = $this->searchTermsArr['clid'] ?? '';
            unset($this->searchTermsArr);
			if($dbsTemp) {
                $this->searchTermsArr['db'] = $dbsTemp;
            }
			if($clidTemp) {
                $this->searchTermsArr['clid'] = $clidTemp;
            }
		}
	}

	public function getSearchTerms(): array
    {
		return $this->searchTermsArr;
	}

	public function getSearchTerm($k){
		if(array_key_exists($k,$this->searchTermsArr)){
			return $this->searchTermsArr[$k];
		}

        return '';
    }

	public function getSqlWhere(): string
    {
		$sqlWhere = '';
        $retStr = '';
		if(array_key_exists('clid',$this->searchTermsArr) && $this->searchTermsArr['clid']){
			$sqlWhere .= 'AND (v.clid IN(' .$this->searchTermsArr['clid']. ')) ';
		}
		if(array_key_exists('db',$this->searchTermsArr) && $this->searchTermsArr['db']){
			if($this->searchTermsArr['db'] !== 'all'){
                $sqlWhere .= 'AND (o.collid IN(' .$this->cleanInStr($this->searchTermsArr['db']). ')) ';
			}
		}
        if(array_key_exists('taxa',$this->searchTermsArr) && $this->searchTermsArr['taxa']){
			$sqlWhereTaxa = '';
			$useThes = (array_key_exists('usethes',$this->searchTermsArr)?$this->searchTermsArr['usethes']:0);
			$this->taxaSearchType = (int)$this->searchTermsArr['taxontype'];
			$taxaArr = explode(';',trim($this->searchTermsArr['taxa']));
			$this->taxaArr = array();
			foreach($taxaArr as $sName){
				$this->taxaArr[trim($sName)] = array();
			}
			if($this->taxaSearchType === 5){
				$this->setSciNamesByVerns();
			}
			else if($useThes){
                $this->setSynonyms();
            }

			foreach($this->taxaArr as $key => $valueArray){
				if($this->taxaSearchType === 4){
					$rs1 = $this->conn->query("SELECT ts.tidaccepted FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.TID = ts.tid WHERE (t.sciname = '".$this->cleanInStr($key)."')");
					if($r1 = $rs1->fetch_object()){
						$sqlWhereTaxa = 'OR ((o.sciname = "'.$this->cleanInStr($key).'") OR (o.tidinterpreted IN(SELECT DISTINCT tid FROM taxaenumtree WHERE taxauthid = 1 AND parenttid IN('.$r1->tidaccepted.')))) ';
					}
				}
				else{
					if($this->taxaSearchType === 5){
						$famArr = array();
						if(array_key_exists('families',$valueArray)){
							$famArr = $valueArray['families'];
						}
						if(array_key_exists('tid',$valueArray)){
							$tidArr = $valueArray['tid'];
							$sql = 'SELECT DISTINCT t.sciname '.
								'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
								'WHERE t.rankid = 140 AND e.taxauthid = 1 AND e.parenttid IN('.implode(',',$tidArr).')';
							$rs = $this->conn->query($sql);
							while($r = $rs->fetch_object()){
								$famArr[] = $r->family;
							}
						}
						if($famArr){
							$famArr = array_unique($famArr);
							$sqlWhereTaxa .= 'OR (o.family IN("'.$this->cleanInStr(implode('","',$famArr)).'")) ';
						}
						if(array_key_exists('scinames',$valueArray)){
							foreach($valueArray['scinames'] as $sciName){
								$sqlWhereTaxa .= "OR (o.sciname Like '".$this->cleanInStr($sciName)."%') ";
							}
						}
					}
					else{
						if($this->taxaSearchType === 2 || ($this->taxaSearchType === 1 && (strtolower(substr($key,-5)) === 'aceae' || strtolower(substr($key,-4)) === 'idae'))){
							$sqlWhereTaxa .= "OR (o.family = '".$this->cleanInStr($key)."') ";
						}
						if($this->taxaSearchType === 3 || ($this->taxaSearchType === 1 && strtolower(substr($key,-5)) !== 'aceae' && strtolower(substr($key,-4)) !== 'idae')){
							$sqlWhereTaxa .= "OR (o.sciname LIKE '".$this->cleanInStr($key)."%') ";
						}
					}
					if(array_key_exists('synonyms',$valueArray)){
						$synArr = $valueArray['synonyms'];
						if($synArr){
							if($this->taxaSearchType === 1 || $this->taxaSearchType === 2 || $this->taxaSearchType === 5){
								foreach($synArr as $synTid => $sciName){
									if(strpos($sciName,'aceae') || strpos($sciName,'idae')){
										$sqlWhereTaxa .= "OR (o.family = '".$this->cleanInStr($sciName)."') ";
									}
								}
							}
							$sqlWhereTaxa .= 'OR (o.tidinterpreted IN('.implode(',',array_keys($synArr)).')) ';
						}
					}
				}
			}
			$sqlWhere .= 'AND (' .substr($sqlWhereTaxa,3). ') ';
		}
        if(array_key_exists('country',$this->searchTermsArr) && $this->searchTermsArr['country']){
			$searchStr = str_replace('%apos;',"'",$this->searchTermsArr['country']);
			$countryArr = explode(';',$searchStr);
			$tempArr = array();
			foreach($countryArr as $k => $value){
				if($value === 'NULL'){
					$countryArr[$k] = 'Country IS NULL';
					$tempArr[] = '(o.Country IS NULL)';
				}
				else{
					$tempArr[] = '(o.Country = "'.$this->cleanInStr($value).'")';
				}
			}
			$sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
			$this->localSearchArr[] = implode(' OR ',$countryArr);
		}
		if(array_key_exists('state',$this->searchTermsArr) && $this->searchTermsArr['state']){
			$searchStr = str_replace('%apos;',"'",$this->searchTermsArr['state']);
			$stateAr = explode(';',$searchStr);
			$tempArr = array();
			foreach($stateAr as $k => $value){
				if($value === 'NULL'){
					$tempArr[] = '(o.StateProvince IS NULL)';
					$stateAr[$k] = 'State IS NULL';
				}
				else{
					$tempArr[] = '(o.StateProvince = "'.$this->cleanInStr($value).'")';
				}
			}
			$sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
			$this->localSearchArr[] = implode(' OR ',$stateAr);
		}
		if(array_key_exists('county',$this->searchTermsArr) && $this->searchTermsArr['county']){
			$searchStr = str_replace('%apos;',"'",$this->searchTermsArr['county']);
			$countyArr = explode(';',$searchStr);
			$tempArr = array();
			foreach($countyArr as $k => $value){
				if($value === 'NULL'){
					$tempArr[] = '(o.county IS NULL)';
					$countyArr[$k] = 'County IS NULL';
				}
				else{
					$value = trim(str_ireplace(' county',' ',$value));
					$tempArr[] = '(o.county LIKE "'.$this->cleanInStr($value).'%")';
				}
			}
			$sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
			$this->localSearchArr[] = implode(' OR ',$countyArr);
		}
		if(array_key_exists('local',$this->searchTermsArr) && $this->searchTermsArr['local']){
			$searchStr = str_replace('%apos;',"'",$this->searchTermsArr['local']);
			$localArr = explode(';',$searchStr);
			$tempArr = array();
			foreach($localArr as $k => $value){
				$value = trim($value);
				if($value === 'NULL'){
					$tempArr[] = '(o.locality IS NULL)';
					$localArr[$k] = 'Locality IS NULL';
				}
				else{
                    $tempArr[] = '(o.municipality LIKE "'.$this->cleanInStr($value).'%" OR o.Locality LIKE "%'.$this->cleanInStr($value).'%")';
                }
			}
			$sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
			$this->localSearchArr[] = implode(' OR ',$localArr);
		}
		if((array_key_exists('elevlow',$this->searchTermsArr) && is_numeric($this->searchTermsArr['elevlow'])) || (array_key_exists('elevhigh',$this->searchTermsArr) && is_numeric($this->searchTermsArr['elevhigh']))){
			$elevlow = 0;
			$elevhigh = 30000;
			if (array_key_exists('elevlow',$this->searchTermsArr))  { $elevlow = $this->searchTermsArr['elevlow']; }
			if (array_key_exists('elevhigh',$this->searchTermsArr))  { $elevhigh = $this->searchTermsArr['elevhigh']; }
			$sqlWhere .= 'AND ( ' .
                '	  ( minimumElevationInMeters >= ' .$elevlow. ' AND maximumElevationInMeters <= ' .$elevhigh. ' ) OR ' .
                '	  ( maximumElevationInMeters is null AND minimumElevationInMeters >= ' .$elevlow. ' AND minimumElevationInMeters <= ' .$elevhigh. ' ) ' .
                '	) ';
		}
        if(array_key_exists('assochost',$this->searchTermsArr) && $this->searchTermsArr['assochost']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['assochost']);
            $hostAr = explode(';',$searchStr);
            $tempArr = array();
            foreach($hostAr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(o.StateProvince IS NULL)';
                    $hostAr[$k] = 'Host IS NULL';
                }
                else{
                    $tempArr[] = '(oas.relationship = "host" AND oas.verbatimsciname LIKE "%'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$hostAr);
        }
		if((array_key_exists('upperlat',$this->searchTermsArr) && $this->searchTermsArr['upperlat']) || (array_key_exists('pointlat',$this->searchTermsArr) && $this->searchTermsArr['pointlat']) || (array_key_exists('circleArr',$this->searchTermsArr) && $this->searchTermsArr['circleArr']) || (array_key_exists('polyArr',$this->searchTermsArr) && $this->searchTermsArr['polyArr'])){
            $geoSqlStrArr = array();
            if(array_key_exists('upperlat',$this->searchTermsArr) && $this->searchTermsArr['upperlat']){
                $geoSqlStrArr[] = '(o.DecimalLatitude BETWEEN ' .$this->cleanInStr($this->searchTermsArr['bottomlat']). ' AND ' .$this->cleanInStr($this->searchTermsArr['upperlat']). ' AND ' .
                    'o.DecimalLongitude BETWEEN ' .$this->cleanInStr($this->searchTermsArr['leftlong']). ' AND ' .$this->cleanInStr($this->searchTermsArr['rightlong']). ') ';
                $this->localSearchArr[] = 'Lat: >' .$this->searchTermsArr['bottomlat']. ', <' .$this->searchTermsArr['upperlat']. '; Long: >' .$this->searchTermsArr['leftlong']. ', <' .$this->searchTermsArr['rightlong'];
            }
            if(array_key_exists('pointlat',$this->searchTermsArr) && $this->searchTermsArr['pointlat']){
                $geoSqlStrArr[] = '(( 3959 * acos( cos( radians(' .$this->searchTermsArr['pointlat']. ') ) * cos( radians( o.DecimalLatitude ) ) * cos( radians( o.DecimalLongitude ) - radians(' .$this->searchTermsArr['pointlong']. ') ) + sin( radians(' .$this->searchTermsArr['pointlat']. ') ) * sin(radians(o.DecimalLatitude)) ) ) < ' .$this->searchTermsArr['radius']. ') ';
                $this->localSearchArr[] = 'Point radius: ' .$this->searchTermsArr['pointlat']. ', ' .$this->searchTermsArr['pointlong']. ', within ' .$this->searchTermsArr['radiustemp']. ' '.$this->searchTermsArr['radiusunits'];
            }
            if(array_key_exists('circleArr',$this->searchTermsArr) && $this->searchTermsArr['circleArr']){
                $sqlFragArr = array();
                $objArr = $this->searchTermsArr['circleArr'];
                if(!is_array($objArr)){
                    $objArr = json_decode($objArr, true);
                }
                if($objArr){
                    foreach($objArr as $obj => $oArr){
                        $radius = $oArr['radius'] * 0.621371;
                        $sqlFragArr[] = '(( 3959 * acos( cos( radians(' .$oArr['pointlat']. ') ) * cos( radians( o.DecimalLatitude ) ) * cos( radians( o.DecimalLongitude ) - radians(' .$oArr['pointlong']. ') ) + sin( radians(' .$oArr['pointlat']. ') ) * sin(radians(o.DecimalLatitude)) ) ) < ' .$radius. ') ';
                        $this->localSearchArr[] = 'Point radius: ' .$oArr['pointlat']. ', ' .$oArr['pointlong']. ', within ' .$radius. ' miles';
                    }
                    $geoSqlStrArr[] = '('.implode(' OR ', $sqlFragArr).') ';
                }
            }
            if(array_key_exists('polyArr',$this->searchTermsArr) && $this->searchTermsArr['polyArr']){
                //$polyStr = str_replace("\\", '',$this->searchTermsArr['polyArr']);
                $sqlFragArr = array();
                $geomArr = $this->searchTermsArr['polyArr'];
                if(!is_array($geomArr)){
                    $geomArr = json_decode($geomArr, true);
                }
                if($geomArr){
                    foreach($geomArr as $geom){
                        $sqlFragArr[] = "(ST_Within(p.point,GeomFromText('".$geom." '))) ";
                    }
                    $geoSqlStrArr[] = '('.implode(' OR ', $sqlFragArr).') ';
                }
            }
            if($geoSqlStrArr){
                $sqlWhere .= 'AND ('.implode(' OR ', $geoSqlStrArr).') ';
            }
        }
		if(array_key_exists('collector',$this->searchTermsArr) && $this->searchTermsArr['collector']){
			$searchStr = str_replace('%apos;',"'",$this->searchTermsArr['collector']);
			$collectorArr = explode(';',$searchStr);
			$tempArr = array();
			if(count($collectorArr) === 1){
				if($collectorArr[0] === 'NULL'){
					$tempArr[] = '(o.recordedBy IS NULL)';
					$collectorArr[] = 'Collector IS NULL';
				}
				else{
					$tempInnerArr = array();
					$collValueArr = explode(' ',trim($collectorArr[0]));
					foreach($collValueArr as $collV){
						if(strlen($collV) < 4 || strtolower($collV) === 'best'){
							$tempInnerArr[] = '(o.recordedBy LIKE "%'.$this->cleanInStr($collV).'%")';
						}
						else{
							$tempInnerArr[] = '(MATCH(f.recordedby) AGAINST("'.$this->cleanInStr($collV).'")) ';
						}
					}
					$tempArr[] = implode(' AND ', $tempInnerArr);
				}
			}
			elseif(count($collectorArr) > 1){
				$collStr = current($collectorArr);
				if(strlen($collStr) < 4 || strtolower($collStr) === 'best'){
					$tempInnerArr[] = '(o.recordedBy LIKE "%'.$this->cleanInStr($collStr).'%")';
				}
				else{
					$tempArr[] = '(MATCH(f.recordedby) AGAINST("'.$this->cleanInStr($collStr).'")) ';
				}
			}
			$sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
			$this->localSearchArr[] = implode(', ',$collectorArr);
		}
		if(array_key_exists('collnum',$this->searchTermsArr) && $this->searchTermsArr['collnum']){
			$collNumArr = explode(';',$this->searchTermsArr['collnum']);
			$rnWhere = '';
			foreach($collNumArr as $v){
				$v = trim($v);
				if($p = strpos($v,' - ')){
					$term1 = trim(substr($v,0,$p));
					$term2 = trim(substr($v,$p+3));
					if(is_numeric($term1) && is_numeric($term2)){
                        $rnWhere .= 'OR (o.recordnumber BETWEEN '.$term1.' AND '.$term2.')';
					}
					else{
						if(strlen($term2) > strlen($term1)) {
                            $term1 = str_pad($term1, strlen($term2), '0', STR_PAD_LEFT);
                        }
						$catTerm = '(o.recordnumber BETWEEN "'.$this->cleanInStr($term1).'" AND "'.$this->cleanInStr($term2).'")';
						$catTerm .= ' AND (length(o.recordnumber) <= '.strlen($term2).')';
						$rnWhere .= 'OR ('.$catTerm.')';
					}
				}
				else{
					$rnWhere .= 'OR (o.recordNumber = "'.$this->cleanInStr($v).'") ';
				}
			}
			if($rnWhere){
				$sqlWhere .= 'AND (' .substr($rnWhere,3). ') ';
				$this->localSearchArr[] = implode(', ',$collNumArr);
			}
		}
		if(array_key_exists('eventdate1',$this->searchTermsArr) && $this->searchTermsArr['eventdate1']){
			$dateArr = array();
			if(strpos($this->searchTermsArr['eventdate1'],' to ')){
				$dateArr = explode(' to ',$this->searchTermsArr['eventdate1']);
			}
			elseif(strpos($this->searchTermsArr['eventdate1'],' - ')){
				$dateArr = explode(' - ',$this->searchTermsArr['eventdate1']);
			}
			else{
				$dateArr[] = $this->searchTermsArr['eventdate1'];
				if(isset($this->searchTermsArr['eventdate2'])){
					$dateArr[] = $this->searchTermsArr['eventdate2'];
				}
			}
			if($dateArr[0] === 'NULL'){
				$sqlWhere .= 'AND (o.eventdate IS NULL) ';
				$this->localSearchArr[] = 'Date IS NULL';
			}
			elseif($eDate1 = $this->formatDate($dateArr[0])){
				$eDate2 = (count($dateArr)>1?$this->formatDate($dateArr[1]):'');
				if($eDate2){
					$sqlWhere .= 'AND (o.eventdate BETWEEN "'.$this->cleanInStr($eDate1).'" AND "'.$this->cleanInStr($eDate2).'") ';
				}
				else if(substr($eDate1,-5) === '00-00'){
                    $sqlWhere .= 'AND (o.eventdate LIKE "'.$this->cleanInStr(substr($eDate1,0,5)).'%") ';
                }
                elseif(substr($eDate1,-2) === '00'){
                    $sqlWhere .= 'AND (o.eventdate LIKE "'.$this->cleanInStr(substr($eDate1,0,8)).'%") ';
                }
                else{
                    $sqlWhere .= 'AND (o.eventdate = "'.$this->cleanInStr($eDate1).'") ';
                }
				$this->localSearchArr[] = $this->searchTermsArr['eventdate1'].(isset($this->searchTermsArr['eventdate2'])?' to '.$this->searchTermsArr['eventdate2']:'');
			}
		}
        if(array_key_exists('occurrenceRemarks',$this->searchTermsArr) && $this->searchTermsArr['occurrenceRemarks']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['occurrenceRemarks']);
            $remarksArr = explode(';',$searchStr);
            $tempArr = array();
            foreach($remarksArr as $k => $value){
                $value = trim($value);
                if($value === 'NULL'){
                    $tempArr[] = '(o.occurrenceRemarks IS NULL)';
                    $remarksArr[$k] = 'Occurrence Remarks IS NULL';
                }
                else{
                    $tempArr[] = '(o.occurrenceRemarks LIKE "%'.$this->cleanInStr($value).'%")';
                }
            }
            $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
            $this->localSearchArr[] = implode(' OR ',$remarksArr);
        }
		if(array_key_exists('catnum',$this->searchTermsArr) && $this->searchTermsArr['catnum']){
			$catStr = $this->searchTermsArr['catnum'];
			$includeOtherCatNum = array_key_exists('othercatnum',$this->searchTermsArr)?true:false;

			$catArr = explode(',',str_replace(';',',',$catStr));
			$betweenFrag = array();
			$inFrag = array();
			foreach($catArr as $v){
				if($p = strpos($v,' - ')){
					$term1 = trim(substr($v,0,$p));
					$term2 = trim(substr($v,$p+3));
					if(is_numeric($term1) && is_numeric($term2)){
						$betweenFrag[] = '(o.catalogNumber BETWEEN '.$this->cleanInStr($term1).' AND '.$this->cleanInStr($term2).')';
						if($includeOtherCatNum){
							$betweenFrag[] = '(o.othercatalognumbers BETWEEN '.$this->cleanInStr($term1).' AND '.$this->cleanInStr($term2).')';
						}
					}
					else{
						$catTerm = 'o.catalogNumber BETWEEN "'.$this->cleanInStr($term1).'" AND "'.$this->cleanInStr($term2).'"';
						if(strlen($term1) === strlen($term2)) {
                            $catTerm .= ' AND length(o.catalogNumber) = ' . $this->cleanInStr(strlen($term2));
                        }
						$betweenFrag[] = '('.$catTerm.')';
						if($includeOtherCatNum){
							$betweenFrag[] = '(o.othercatalognumbers BETWEEN "'.$this->cleanInStr($term1).'" AND "'.$this->cleanInStr($term2).'")';
						}
					}
				}
				else{
					$vStr = trim($v);
					$inFrag[] = $this->cleanInStr($vStr);
					if(is_numeric($vStr) && strpos($vStr, '0') === 0){
						$inFrag[] = ltrim($vStr,0);
					}
				}
			}
			$catWhere = '';
			if($betweenFrag){
				$catWhere .= 'OR '.implode(' OR ',$betweenFrag);
			}
			if($inFrag){
				$catWhere .= 'OR (o.catalogNumber IN("'.implode('","',$inFrag).'")) ';
				if($includeOtherCatNum){
					$catWhere .= 'OR (o.othercatalognumbers IN("'.implode('","',$inFrag).'")) ';
					if(strlen($inFrag[0]) === 36){
						$guidOccid = $this->queryRecordID($inFrag);
						if($guidOccid){
							$catWhere .= 'OR (o.occid IN('.implode(',',$guidOccid).')) ';
							$catWhere .= 'OR (o.occurrenceID IN("'.implode('","',$inFrag).'")) ';
						}
					}
				}
			}
			$sqlWhere .= 'AND ('.substr($catWhere,3).') ';
			$this->localSearchArr[] = $this->searchTermsArr['catnum'];
		}
		if(array_key_exists('typestatus',$this->searchTermsArr) && $this->searchTermsArr['typestatus']){
			$sqlWhere .= 'AND (o.typestatus IS NOT NULL) ';
			$this->localSearchArr[] = 'is type';
		}
		if(array_key_exists('hasimages',$this->searchTermsArr) && $this->searchTermsArr['hasimages']){
			$sqlWhere .= 'AND (o.occid IN(SELECT occid FROM images)) ';
			$this->localSearchArr[] = 'has images';
		}
        if(array_key_exists('hasgenetic',$this->searchTermsArr) && $this->searchTermsArr['hasgenetic']){
            $sqlWhere .= 'AND (o.occid IN(SELECT occid FROM omoccurgenetic)) ';
            $this->localSearchArr[] = 'has genetic data';
        }
		if(array_key_exists('targetclid',$this->searchTermsArr) && $this->searchTermsArr['targetclid']){
			$clid = $this->searchTermsArr['targetclid'];
			if(is_numeric($clid)){
				$voucherManager = new ChecklistVoucherAdmin($this->conn);
				$voucherManager->setClid($clid);
				$voucherManager->setCollectionVariables();
				$this->clName = $voucherManager->getClName();
				$sqlWhere .= 'AND ('.$voucherManager->getSqlFrag().') '.
					'AND (o.occid NOT IN(SELECT occid FROM fmvouchers WHERE clid = '.$clid.')) ';
				$this->localSearchArr[] = $voucherManager->getQueryVariableStr();
			}
		}
		if($sqlWhere){
			$retStr = 'WHERE '.substr($sqlWhere,4);
		}
		//echo $retStr; exit;
		return $retStr;
	}

	private function queryRecordID($idArr): array
    {
		$retArr = array();
		if($idArr){
			$sql = 'SELECT occid FROM guidoccurrences WHERE guid IN("'.implode('","', $idArr).'")';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = $r->occid;
			}
			$rs->free();
		}
		return $retArr;
	}

    protected function formatDate($inDate){
        return OccurrenceUtilities::formatDate($inDate);
	}

	protected function setTableJoins($sqlWhere): string
    {
		$sqlJoin = '';
		if(array_key_exists('clid',$this->searchTermsArr)) {
            $sqlJoin .= 'INNER JOIN fmvouchers v ON o.occid = v.occid ';
        }
        if(array_key_exists('assochost',$this->searchTermsArr)) {
            $sqlJoin .= 'INNER JOIN omoccurassociations AS oas ON o.occid = oas.occid ';
        }
        if(array_key_exists('polyArr',$this->searchTermsArr) || array_key_exists('circleArr',$this->searchTermsArr) || array_key_exists('pointlat',$this->searchTermsArr) || array_key_exists('upperlat',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ';
        }
		if(strpos($sqlWhere,'MATCH(f.recordedby)') || strpos($sqlWhere,'MATCH(f.locality)')){
			$sqlJoin .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
		}
		return $sqlJoin;
	}

	protected function setSciNamesByVerns(): void
    {
		$sql = 'SELECT DISTINCT v.VernacularName, t.tid, t.sciname, ts.family, t.rankid ' .
            'FROM (taxstatus ts INNER JOIN taxavernaculars v ON ts.TID = v.TID) ' .
            'INNER JOIN taxa t ON t.TID = ts.tidaccepted ';
		$whereStr = '';
		foreach($this->taxaArr as $key => $value){
			$whereStr .= "OR v.VernacularName = '".$this->cleanInStr($key)."' ";
		}
		$sql .= 'WHERE (ts.taxauthid = 1) AND (' .substr($whereStr,3). ') ORDER BY t.rankid LIMIT 20';
		//echo "<div>sql: ".$sql."</div>";
		$result = $this->conn->query($sql);
		if($result->num_rows){
			while($row = $result->fetch_object()){
				$vernName = strtolower($row->VernacularName);
				if($row->rankid < 140){
					$this->taxaArr[$vernName]['tid'][] = $row->tid;
				}
				elseif($row->rankid === 140){
					$this->taxaArr[$vernName]['families'][] = $row->sciname;
				}
				else{
					$this->taxaArr[$vernName]['scinames'][] = $row->sciname;
				}
			}
		}
		else{
			$this->taxaArr['no records']['scinames'][] = 'no records';
		}
		$result->free();
	}

	protected function setSynonyms(): void
    {
		foreach($this->taxaArr as $key => $value){
			if(array_key_exists('scinames',$value)){
				if(!in_array('no records', $value['scinames'], true)){
					$synArr = $this->getSynonyms($value['scinames']);
					if($synArr) {
                        $this->taxaArr[$key]['synonyms'] = $synArr;
                    }
				}
			}
			else{
				$synArr = $this->getSynonyms($key);
				if($synArr) {
                    $this->taxaArr[$key]['synonyms'] = $synArr;
                }
			}
		}
	}

	public function getFullCollectionList($catId = ''): array
    {
        if($catId && !is_numeric($catId)) {
            $catId = '';
        }
        $sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.colltype, ccl.ccpk, '.
            'cat.category, cat.icon AS caticon, cat.acronym '.
            'FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid '.
            'LEFT JOIN omcollcatlink ccl ON c.collid = ccl.collid '.
            'LEFT JOIN omcollcategories cat ON ccl.ccpk = cat.ccpk '.
            'WHERE (cat.inclusive IS NULL OR cat.inclusive = 1 OR cat.ccpk = 1) '.
            'ORDER BY ccl.sortsequence, cat.category, c.sortseq, c.CollectionName ';
        //echo "<div>SQL: ".$sql."</div>";
        $result = $this->conn->query($sql);
        $collArr = array();
        while($r = $result->fetch_object()){
            $collType = '';
            if(stripos($r->colltype, 'observation') !== false) {
                $collType = 'obs';
            }
            if(stripos($r->colltype, 'specimen')) {
                $collType = 'spec';
            }
            if($collType){
                if($r->ccpk){
                    if(!isset($collArr[$collType]['cat'][$r->ccpk]['name'])){
                        $collArr[$collType]['cat'][$r->ccpk]['name'] = $r->category;
                        $collArr[$collType]['cat'][$r->ccpk]['icon'] = $r->caticon;
                        $collArr[$collType]['cat'][$r->ccpk]['acronym'] = $r->acronym;
                    }
                    $collArr[$collType]['cat'][$r->ccpk][$r->collid]['instcode'] = $r->institutioncode;
                    $collArr[$collType]['cat'][$r->ccpk][$r->collid]['collcode'] = $r->collectioncode;
                    $collArr[$collType]['cat'][$r->ccpk][$r->collid]['collname'] = $r->collectionname;
                    $collArr[$collType]['cat'][$r->ccpk][$r->collid]['icon'] = $r->icon;
                }
                else{
                    $collArr[$collType]['coll'][$r->collid]['instcode'] = $r->institutioncode;
                    $collArr[$collType]['coll'][$r->collid]['collcode'] = $r->collectioncode;
                    $collArr[$collType]['coll'][$r->collid]['collname'] = $r->collectionname;
                    $collArr[$collType]['coll'][$r->collid]['icon'] = $r->icon;
                }
            }
        }
        $result->free();

        $retArr = array();
        if(isset($collArr['spec']['cat'][$catId])){
            $retArr['spec']['cat'][$catId] = $collArr['spec']['cat'][$catId];
            unset($collArr['spec']['cat'][$catId]);
        }
        elseif(isset($collArr['obs']['cat'][$catId])){
            $retArr['obs']['cat'][$catId] = $collArr['obs']['cat'][$catId];
            unset($collArr['obs']['cat'][$catId]);
        }
        foreach($collArr as $t => $tArr){
            foreach($tArr as $g => $gArr){
                foreach($gArr as $id => $idArr){
                    $retArr[$t][$g][$id] = $idArr;
                }
            }
        }
        return $retArr;
	}

    public function outputFullCollArr($occArr,$expanded = true): void{
        global $DEFAULTCATID, $CLIENT_ROOT;
        if(isset($occArr['cat'])){
            $categoryArr = $occArr['cat'];
            if($expanded){
                ?>
                <div style="float:right;margin-top:20px;">
                    <input type="submit" class="nextbtn searchcollnextbtn" value="Next >"  />
                </div>
                <?php
            }
            ?>
            <table<?php echo ($expanded?' style="float:left;width:80%;"':''); ?>>
                <?php
                foreach($categoryArr as $catid => $catArr){
                    $name = $catArr['name'];
                    if($catArr['acronym'] && $expanded) {
                        $name .= ' (' . $catArr['acronym'] . ')';
                    }
                    $catIcon = $catArr['icon'];
                    unset($catArr['name'], $catArr['acronym'], $catArr['icon']);
                    $idStr = $this->collArrIndex.'-'.$catid;
                    if($expanded){
                        ?>
                        <tr>
                            <td style="<?php echo ($catIcon?'width:40px':''); ?>">
                                <?php
                                if($catIcon){
                                    $catIcon = (strpos($catIcon, 'images') === 0 ?'../':'').$catIcon;
                                    echo '<img src="'.$catIcon.'" style="border:0px;width:30px;height:30px;" />';
                                }
                                ?>
                            </td>
                            <td style="padding:6px;width:25px;">
                                <input id="cat-<?php echo $idStr; ?>-Input" name="cat[]" value="<?php echo $catid; ?>" type="checkbox" onchange="processCollectionParamChange(this.form);" onclick="selectAllCat(this,'cat-<?php echo $idStr; ?>')" checked />
                            </td>
                            <td style="padding:9px 5px;width:10px;">
                                <a href="#" onclick="toggleCat('<?php echo $idStr; ?>');return false;">
                                    <img id="plus-<?php echo $idStr; ?>" src="../images/plus_sm.png" style="<?php echo ($DEFAULTCATID !== $catid?'':'display:none;') ?>" /><img id="minus-<?php echo $idStr; ?>" src="../images/minus_sm.png" style="<?php echo ($DEFAULTCATID !== $catid?'display:none;':'') ?>" />
                                </a>
                            </td>
                            <td style="padding-top:8px;">
                                <div class="categorytitle">
                                    <a href="#" onclick="toggleCat('<?php echo $idStr; ?>');return false;">
                                        <?php echo $name; ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div id="cat-<?php echo $idStr; ?>" style="<?php echo ($DEFAULTCATID && $DEFAULTCATID !== $catid?'display:none;':'') ?>margin:10px;padding:10px 20px;border:inset">
                                    <table>
                                        <?php
                                        foreach($catArr as $collid => $collName2){
                                            ?>
                                            <tr>
                                                <td style="width:40px;">
                                                    <?php
                                                    if($collName2['icon']){
                                                        $cIcon = (strpos($collName2['icon'], 'images') === 0 ?'../':'').$collName2['icon'];
                                                        ?>
                                                        <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>'><img src="<?php echo $cIcon; ?>" style="border:0;width:30px;height:30px;" /></a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td style="padding:6px;width:25px;">
                                                    <input name="db[]" value="<?php echo $collid; ?>" type="checkbox" class="cat-<?php echo $idStr; ?>" onchange="processCollectionParamChange(this.form);" onclick="processCatCheckboxes('<?php echo $idStr; ?>')" checked />
                                                </td>
                                                <td style="padding:6px">
                                                    <div class="collectiontitle">
                                                        <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>'>
                                                            <?php
                                                            $codeStr = ' ('.$collName2['instcode'];
                                                            if($collName2['collcode']) {
                                                                $codeStr .= '-' . $collName2['collcode'];
                                                            }
                                                            $codeStr .= ')';
                                                            echo $collName2['collname'].$codeStr;
                                                            ?>
                                                        </a>
                                                        <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' style='font-size:75%;'>
                                                            more info
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    else{
                        ?>
                        <tr>
                            <td>
                                <a href="#" onclick="toggleCat('<?php echo $idStr; ?>');return false;">
                                    <img id="plus-<?php echo $idStr; ?>" src="<?php echo $CLIENT_ROOT; ?>/images/plus_sm.png" style="<?php echo ($DEFAULTCATID === $catid?'display:none;':'') ?>" /><img id="minus-<?php echo $idStr; ?>" src="<?php echo $CLIENT_ROOT; ?>/images/minus_sm.png" style="<?php echo ($DEFAULTCATID === $catid?'':'display:none;') ?>" />
                                </a>
                            </td>
                            <td>
                                <input id="cat-<?php echo $idStr; ?>-Input" data-role="none" name="cat[]" value="<?php echo $catid; ?>" type="checkbox" onchange="processCollectionParamChange(this.form);" onclick="selectAllCat(this,'cat-<?php echo $idStr; ?>')" checked />
                            </td>
                            <td>
			    		<span style='text-decoration:none;color:black;font-size:14px;font-weight:bold;'>
				    		<a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?catid=<?php echo $catid; ?>' target="_blank" ><?php echo $name; ?></a>
				    	</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div id="cat-<?php echo $idStr; ?>" style="<?php echo ($DEFAULTCATID===$catid?'':'display:none;') ?>margin:10px 0;">
                                    <table style="margin-left:15px;">
                                        <?php
                                        foreach($catArr as $collid => $collName2){
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    if($collName2['icon']){
                                                        $cIcon = (strpos($collName2['icon'], 'images') === 0 ?$CLIENT_ROOT.'/':'').$collName2['icon'];
                                                        ?>
                                                        <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' target="_blank" >
                                                            <img src="<?php echo $cIcon; ?>" style="border:0;width:30px;height:30px;" />
                                                        </a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td style="padding:6px">
                                                    <input name="db[]" value="<?php echo $collid; ?>" data-role="none" type="checkbox" class="cat-<?php echo $idStr; ?>" onchange="processCollectionParamChange(this.form);" onclick="processCatCheckboxes('<?php echo $idStr; ?>')" checked />
                                                </td>
                                                <td style="padding:6px">
                                                    <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' style='text-decoration:none;color:black;font-size:14px;' target="_blank" >
                                                        <?php echo $collName2['collname']. ' (' .$collName2['instcode']. ')'; ?>
                                                    </a>
                                                    <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' style='font-size:75%;' target="_blank" >
                                                        more info
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
            <?php
        }
        if(isset($occArr['coll'])){
            $collArr = $occArr['coll'];
            ?>
            <table<?php echo ($expanded?' style="float:left;width:80%;"':''); ?>>
                <?php
                foreach($collArr as $collid => $cArr){
                    if($expanded){
                        ?>
                        <tr>
                            <td style="<?php ($cArr['icon']?'width:35px':''); ?>">
                                <?php
                                if($cArr['icon']){
                                    $cIcon = (strpos($cArr['icon'], 'images') === 0 ?'../':'').$cArr['icon'];
                                    ?>
                                    <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>'><img src="<?php echo $cIcon; ?>" style="border:0;width:30px;height:30px;" /></a>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="padding:6px;width:25px;">
                                <input name="db[]" value="<?php echo $collid; ?>" type="checkbox" onchange="processCollectionParamChange(this.form);" onclick="processCheckAllCheckboxes();" checked />
                            </td>
                            <td style="padding:6px">
                                <div class="collectiontitle">
                                    <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>'>
                                        <?php
                                        $codeStr = ' ('.$cArr['instcode'];
                                        if($cArr['collcode']) {
                                            $codeStr .= '-' . $cArr['collcode'];
                                        }
                                        $codeStr .= ')';
                                        echo $cArr['collname'].$codeStr;
                                        ?>
                                    </a>
                                    <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' style='font-size:75%;'>
                                        more info
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    else{
                        ?>
                        <tr>
                            <td>
                                <?php
                                if($cArr['icon']){
                                    $cIcon = (strpos($cArr['icon'], 'images') === 0 ?$CLIENT_ROOT.'/':'').$cArr['icon'];
                                    ?>
                                    <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' target="_blank" >
                                        <img src="<?php echo $cIcon; ?>" style="border:0;width:30px;height:30px;" />
                                    </a>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="padding:6px;">
                                <input name="db[]" value="<?php echo $collid; ?>" data-role="none" type="checkbox" onchange="processCollectionParamChange(this.form);" onclick="processCheckAllCheckboxes()" checked />
                            </td>
                            <td style="padding:6px">
                                <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' style='text-decoration:none;color:black;font-size:14px;' target="_blank" >
                                    <?php echo $cArr['collname']. ' (' .$cArr['instcode']. ')'; ?>
                                </a>
                                <a href = '<?php echo $CLIENT_ROOT; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' style='font-size:75%;' target="_blank" >
                                    more info
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
            <?php
            if($expanded){
                ?>
                <div style="float:right;margin-top:20px;">
                    <input type="submit" class="nextbtn searchcollnextbtn" value="Next >" />
                </div>
                <?php
            }
        }
        $this->collArrIndex++;
    }

    public function getCollectionList($collIdArr): array
    {
		$retArr = array();
		$sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, cat.category '.
			'FROM omcollections c LEFT JOIN omcollcatlink l ON c.collid = l.collid '.
			'LEFT JOIN omcollcategories cat ON l.ccpk = cat.ccpk '.
			'WHERE c.collid IN('.implode(',',$collIdArr).') ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['instcode'] = $r->institutioncode;
			$retArr[$r->collid]['collcode'] = $r->collectioncode;
			$retArr[$r->collid]['name'] = $r->collectionname;
			$retArr[$r->collid]['icon'] = $r->icon;
			$retArr[$r->collid]['category'] = $r->category;
		}
		$rs->free();
		return $retArr;
	}

	public function getOccurVoucherProjects(): array
    {
		$retArr = array();
		$titleArr = array();
		$sql = 'SELECT p2.pid AS parentpid, p2.projname as catname, p1.pid, p1.projname, '.
			'c.clid, c.name as clname '.
			'FROM fmprojects p1 INNER JOIN fmprojects p2 ON p1.parentpid = p2.pid '.
			'INNER JOIN fmchklstprojlink cl ON p1.pid = cl.pid '.
			'INNER JOIN fmchecklists c ON cl.clid = c.clid '.
			'WHERE p2.occurrencesearch = 1 AND p1.ispublic = 1 ';
		//echo "<div>$sql</div>";
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if(!isset($titleArr['cat'][$r->parentpid])) {
                $titleArr['cat'][$r->parentpid] = $r->catname;
            }
			if(!isset($titleArr['proj'][$r->pid])) {
                $titleArr[$r->parentpid]['proj'][$r->pid] = $r->projname;
            }
			$retArr[$r->pid][$r->clid] = $r->clname;
		}
		$rs->free();
		if($titleArr) {
            $retArr['titles'] = $titleArr;
        }
		return $retArr;
	}

	public function getDatasetSearchStr(){
		$retStr = '';
		if(!array_key_exists('db',$this->searchTermsArr) || $this->searchTermsArr['db'] === 'all'){
			$retStr = 'All Collections';
		}
		elseif($this->searchTermsArr['db'] === 'allspec'){
			$retStr = 'All Specimen Collections';
		}
		elseif($this->searchTermsArr['db'] === 'allobs'){
			$retStr = 'All Observation Projects';
		}
		else{
			$cArr = explode(';',$this->searchTermsArr['db']);
			if($cArr[0]){
				$sql = 'SELECT collid, CONCAT_WS("-",institutioncode,collectioncode) as instcode '.
					'FROM omcollections WHERE collid IN('.$cArr[0].') ORDER BY institutioncode,collectioncode';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retStr .= '; '.$r->instcode;
				}
				$rs->free();
			}
			$retStr = substr($retStr,2);
		}
		return $retStr;
	}

	public function getTaxaSearchStr(): string
    {
		$returnArr = array();
		foreach($this->taxaArr as $taxonName => $taxonArr){
			$str = $taxonName;
			if(array_key_exists('sciname',$taxonArr)){
				$str .= ' => ' .implode(', ',$taxonArr['sciname']);
			}
			if(array_key_exists('synonyms',$taxonArr)){
				$str .= ' (' .implode(', ',$taxonArr['synonyms']). ')';
			}
			$returnArr[] = $str;
		}
		return implode('; ', $returnArr);
	}

	public function getLocalSearchStr(): string
    {
		return implode('; ', $this->localSearchArr);
	}

    public function getTaxonAuthorityList(): array
    {
		$taxonAuthorityList = array();
		$sql = 'SELECT ta.taxauthid, ta.name FROM taxauthority ta WHERE (ta.isactive <> 0)';
		$result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$taxonAuthorityList[$row->taxauthid] = $row->name;
		}
		return $taxonAuthorityList;
	}

	private function readRequestVariables(): void
    {
		if(array_key_exists('clid',$_REQUEST)){
			$clidIn = $_REQUEST['clid'];
			if(is_numeric($clidIn)){
                $clidStr = $this->cleanInputStr($clidIn);
            }
			else{
				$clidStr = $this->cleanInputStr(implode(',',array_unique($clidIn)));
			}
			$this->searchTermsArr['clid'] = $clidStr;
		}
		if(array_key_exists('db',$_REQUEST)){
			$dbs = $_REQUEST['db'];
            if(is_numeric($dbs) || $dbs === 'all'){
                $dbStr = $this->cleanInputStr($dbs);
            }
			elseif(is_array($dbs)){
				$dbStr = $this->cleanInputStr(implode(',',array_unique($dbs)));
			}
            else{
                $dbStr = $this->cleanInputStr($dbs);
            }
            if(!(preg_match('/^[0-9,;]+$/', $dbStr)) || (strpos($dbStr,'all') !== false)) {
                $dbStr = 'all';
            }
			if($dbStr){
				$this->searchTermsArr['db'] = $dbStr;
			}
		}
		if(array_key_exists('taxa',$_REQUEST)){
			$taxa = $this->cleanInputStr($_REQUEST['taxa']);
			$searchType = ((array_key_exists('type',$_REQUEST) && is_numeric($_REQUEST['type']))?$_REQUEST['type']:1);
			if($taxa){
				$taxaStr = '';
				if(is_numeric($taxa)){
					$sql = 'SELECT t.sciname ' .
                        'FROM taxa t ' .
                        'WHERE (t.tid = ' .$taxa.')';
					$rs = $this->conn->query($sql);
					while($row = $rs->fetch_object()){
						$taxaStr = $row->sciname;
					}
					$rs->free();
				}
				else{
					$taxaStr = str_replace(',', ';',$taxa);
					$taxaArr = explode(';',$taxaStr);
					foreach($taxaArr as $key => $sciName){
						$snStr = trim($sciName);
						if($searchType !== 5) {
                            $snStr = ucfirst($snStr);
                        }
						$taxaArr[$key] = $snStr;
					}
					$taxaStr = implode(';',$taxaArr);
				}
				$this->searchTermsArr['taxa'] = $taxaStr;
				$useThes = ((array_key_exists('thes',$_REQUEST) && is_numeric($_REQUEST['thes']))?$_REQUEST['thes']:0);
				if($useThes){
					$this->searchTermsArr['usethes'] = true;
				}
				else{
					$this->searchTermsArr['usethes'] = false;
				}
				if($searchType){
					$this->searchTermsArr['taxontype'] = $searchType;
				}
			}
			else{
				unset($this->searchTermsArr['taxa']);
			}
		}
		if(array_key_exists('country',$_REQUEST)){
			$country = $this->cleanInputStr($_REQUEST['country']);
			if($country){
				$str = str_replace(',', ';',$country);
				if(stripos($str, 'USA') !== false || stripos($str, 'United States') !== false || stripos($str, 'U.S.A.') !== false || stripos($str, 'United States of America') !== false){
					if(stripos($str, 'USA') === false){
						$str .= ';USA';
					}
					if(stripos($str, 'United States') === false){
						$str .= ';United States';
					}
					if(stripos($str, 'U.S.A.') === false){
						$str .= ';U.S.A.';
					}
					if(stripos($str, 'United States of America') === false){
						$str .= ';United States of America';
					}
				}
				$this->searchTermsArr['country'] = $str;
			}
			else{
				unset($this->searchTermsArr['country']);
			}
		}
		if(array_key_exists('state',$_REQUEST)){
			$state = $this->cleanInputStr($_REQUEST['state']);
			if($state){
				if(strlen($state) === 2 && (!isset($this->searchTermsArr['country']) || stripos($this->searchTermsArr['country'],'USA') !== false)){
					$sql = 'SELECT s.statename, c.countryname '.
						'FROM lkupstateprovince s INNER JOIN lkupcountry c ON s.countryid = c.countryid '.
						'WHERE c.countryname IN("USA","United States") AND (s.abbrev = "'.$state.'")';
					$rs = $this->conn->query($sql);
					if($r = $rs->fetch_object()){
						$state = $r->statename;
					}
					$rs->free();
				}
				$str = str_replace(',', ';',$state);
				$this->searchTermsArr['state'] = $str;
			}
			else{
				unset($this->searchTermsArr['state']);
			}
		}
		if(array_key_exists('county',$_REQUEST)){
			$county = $this->cleanInputStr($_REQUEST['county']);
			$county = str_ireplace(' Co.', '',$county);
			$county = str_ireplace(' County', '',$county);
			if($county){
				$str = str_replace(',', ';',$county);
				$this->searchTermsArr['county'] = $str;
			}
			else{
				unset($this->searchTermsArr['county']);
			}
		}
		if(array_key_exists('local',$_REQUEST)){
			$local = $this->cleanInputStr($_REQUEST['local']);
			if($local){
				$str = str_replace(',', ';',$local);
				$this->searchTermsArr['local'] = $str;
			}
			else{
				unset($this->searchTermsArr['local']);
			}
		}
		if(array_key_exists('elevlow', $_REQUEST) && is_numeric($_REQUEST['elevlow'])) {
            $elevlow = $_REQUEST['elevlow'];
            if($elevlow){
                $str = str_replace(',', ';',$elevlow);
                $this->searchTermsArr['elevlow'] = $str;
            }
            else{
                unset($this->searchTermsArr['elevlow']);
            }
        }
		if(array_key_exists('elevhigh', $_REQUEST) && is_numeric($_REQUEST['elevhigh'])) {
            $elevhigh = $_REQUEST['elevhigh'];
            if($elevhigh){
                $str = str_replace(',', ';',$elevhigh);
                $this->searchTermsArr['elevhigh'] = $str;
            }
            else{
                unset($this->searchTermsArr['elevhigh']);
            }
        }
        if(array_key_exists('assochost',$_REQUEST)){
            $assocHost = $this->cleanInputStr($_REQUEST['assochost']);
            if($assocHost){
                $str = str_replace(',', ';',$assocHost);
                $this->searchTermsArr['assochost'] = $str;
            }
            else{
                unset($this->searchTermsArr['assochost']);
            }
        }
		if(array_key_exists('collector',$_REQUEST)){
			$collector = $this->cleanInputStr($_REQUEST['collector']);
			if($collector){
				$str = str_replace(',', ';',$collector);
				$this->searchTermsArr['collector'] = $str;
			}
			else{
				unset($this->searchTermsArr['collector']);
			}
		}
		if(array_key_exists('collnum',$_REQUEST)){
			$collNum = $this->cleanInputStr($_REQUEST['collnum']);
			if($collNum){
				$str = str_replace(',', ';',$collNum);
				$this->searchTermsArr['collnum'] = $str;
			}
			else{
				unset($this->searchTermsArr['collnum']);
			}
		}
		if(array_key_exists('eventdate1',$_REQUEST)){
			if($eventDate = $this->cleanInputStr($_REQUEST['eventdate1'])){
				$this->searchTermsArr['eventdate1'] = $eventDate;
				if(array_key_exists('eventdate2',$_REQUEST)){
					if($eventDate2 = $this->cleanInputStr($_REQUEST['eventdate2'])){
						if($eventDate2 !== $eventDate){
							$this->searchTermsArr['eventdate2'] = $eventDate2;
						}
					}
					else{
						unset($this->searchTermsArr['eventdate2']);
					}
				}
			}
			else{
				unset($this->searchTermsArr['eventdate1']);
			}
		}
        if(array_key_exists('occurrenceRemarks',$_REQUEST)){
            $remarks = $this->cleanInputStr($_REQUEST['occurrenceRemarks']);
            if($remarks){
                $str = str_replace(',', ';',$remarks);
                $this->searchTermsArr['occurrenceRemarks'] = $str;
            }
            else{
                unset($this->searchTermsArr['occurrenceRemarks']);
            }
        }
		if(array_key_exists('catnum',$_REQUEST)){
			$catNum = $this->cleanInputStr($_REQUEST['catnum']);
			if($catNum){
				$str = str_replace(',', ';',$catNum);
				$this->searchTermsArr['catnum'] = $str;
				if(array_key_exists('includeothercatnum',$_REQUEST)){
					$this->searchTermsArr['othercatnum'] = '1';
				}
			}
			else{
				unset($this->searchTermsArr['catnum']);
			}
		}
		if(array_key_exists('typestatus',$_REQUEST)){
			$typestatus = $_REQUEST['typestatus'];
			if($typestatus){
				$this->searchTermsArr['typestatus'] = true;
			}
			else{
				unset($this->searchTermsArr['typestatus']);
			}
		}
		if(array_key_exists('hasimages',$_REQUEST)){
			$hasimages = $_REQUEST['hasimages'];
			if($hasimages){
				$this->searchTermsArr['hasimages'] = true;
			}
			else{
				unset($this->searchTermsArr['hasimages']);
			}
		}
        if(array_key_exists('hasgenetic',$_REQUEST)){
            $hasgenetic = $_REQUEST['hasgenetic'];
            if($hasgenetic){
                $this->searchTermsArr['hasgenetic'] = true;
            }
            else{
                unset($this->searchTermsArr['hasgenetic']);
            }
        }
		if(array_key_exists('targetclid',$_REQUEST) && is_numeric($_REQUEST['targetclid'])){
			$this->searchTermsArr['targetclid'] = $_REQUEST['targetclid'];
		}
        if(array_key_exists('upperlat', $_REQUEST) && is_numeric($_REQUEST['upperlat']) && is_numeric($_REQUEST['bottomlat']) && is_numeric($_REQUEST['leftlong']) && is_numeric($_REQUEST['rightlong'])) {
            if($_REQUEST['upperlat'] || $_REQUEST['upperlat'] === '0') {
                $this->searchTermsArr['upperlat'] = $_REQUEST['upperlat'];
            }
            if($_REQUEST['bottomlat'] || $_REQUEST['bottomlat'] === '0') {
                $this->searchTermsArr['bottomlat'] = $_REQUEST['bottomlat'];
            }
            if($_REQUEST['leftlong'] || $_REQUEST['leftlong'] === '0') {
                $this->searchTermsArr['leftlong'] = $_REQUEST['leftlong'];
            }
            if($_REQUEST['rightlong'] || $_REQUEST['rightlong'] === '0') {
                $this->searchTermsArr['rightlong'] = $_REQUEST['rightlong'];
            }
            if(!$this->searchTermsArr['upperlat'] || !$this->searchTermsArr['bottomlat'] || !$this->searchTermsArr['leftlong'] || !$this->searchTermsArr['rightlong']){
                unset($this->searchTermsArr['upperlat'], $this->searchTermsArr['bottomlat'], $this->searchTermsArr['leftlong'], $this->searchTermsArr['rightlong']);
            }
        }
		if(array_key_exists('pointlat', $_REQUEST) && is_numeric($_REQUEST['pointlat']) && is_numeric($_REQUEST['pointlong']) && is_numeric($_REQUEST['radius'])) {
            if($_REQUEST['pointlat'] || $_REQUEST['pointlat'] === '0') {
                $this->searchTermsArr['pointlat'] = $_REQUEST['pointlat'];
            }
            if($_REQUEST['pointlong'] || $_REQUEST['pointlong'] === '0') {
                $this->searchTermsArr['pointlong'] = $_REQUEST['pointlong'];
            }
            if($_REQUEST['radius']) {
                $this->searchTermsArr['radius'] = $_REQUEST['radius'];
            }
            if($_REQUEST['radiustemp'] && is_numeric($_REQUEST['radiustemp'])) {
                $this->searchTermsArr['radiustemp'] = $_REQUEST['radiustemp'];
            }
            if($_REQUEST['radiusunits'] && $this->cleanInStr($_REQUEST['radiusunits'])) {
                $this->searchTermsArr['radiusunits'] = $this->cleanInStr($_REQUEST['radiusunits']);
            }
            if(!$this->searchTermsArr['pointlat'] || !$this->searchTermsArr['pointlong'] || !$this->searchTermsArr['radius']){
                unset($this->searchTermsArr['pointlat'], $this->searchTermsArr['pointlong'], $this->searchTermsArr['radius'], $this->searchTermsArr['radiustemp'], $this->searchTermsArr['radiusunits']);
            }
        }
		if(array_key_exists('polyArr',$_REQUEST)){
            $this->searchTermsArr['polyArr'] = $this->cleanInStr($_REQUEST['polyArr']);
        }
		else{
            unset($this->searchTermsArr['polyArr']);
        }
        if(array_key_exists('circleArr',$_REQUEST)){
            $this->searchTermsArr['circleArr'] = $this->cleanInStr($_REQUEST['circleArr']);
        }
        else{
            unset($this->searchTermsArr['circleArr']);
        }
	}

	private function getSynonyms($searchTarget,$taxAuthId = 1): array
    {
		$synArr = array();
		$targetTidArr = array();
		$searchStr = '';
		if(is_array($searchTarget)){
            $searchStr = implode('","',$searchTarget);
		}
		elseif(is_numeric($searchTarget)){
            $targetTidArr[] = $searchTarget;
        }
        else{
            $searchStr = $searchTarget;
        }
		if($searchStr){
			$sql1 = 'SELECT tid FROM taxa WHERE sciname IN("'.$searchStr.'")';
			$rs1 = $this->conn->query($sql1);
			while($r1 = $rs1->fetch_object()){
				$targetTidArr[] = $r1->tid;
			}
			$rs1->free();
		}

		if($targetTidArr){
			$accArr = array();
			$rankId = 0;
			$sql2 = 'SELECT DISTINCT t.tid, t.sciname, t.rankid '.
				'FROM taxa t INNER JOIN taxstatus ts ON t.Tid = ts.TidAccepted '.
				'WHERE (ts.taxauthid = '.$taxAuthId.') AND (ts.tid IN('.implode(',',$targetTidArr).')) ';
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$accArr[] = $r2->tid;
				$rankId = $r2->rankid;
				$synArr[$r2->tid] = $r2->sciname;
			}
			$rs2->free();

			if($accArr){
                $sql3 = 'SELECT DISTINCT t.tid, t.sciname ' .
                    'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid ' .
                    'WHERE (ts.taxauthid = ' . $taxAuthId . ') AND (ts.tidaccepted IN(' . implode('', $accArr) . ')) ';
                $rs3 = $this->conn->query($sql3);
                while ($r3 = $rs3->fetch_object()) {
                    $synArr[$r3->tid] = $r3->sciname;
                }
                $rs3->free();

                if ($rankId === 220) {
                    $sql4 = 'SELECT DISTINCT t.tid, t.sciname ' .
                        'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid ' .
                        'WHERE (ts.parenttid IN(' . implode('', $accArr) . ')) AND (ts.taxauthid = ' . $taxAuthId . ') ' .
                        'AND (ts.TidAccepted = ts.tid)';
                    $rs4 = $this->conn->query($sql4);
                    while ($r4 = $rs4->fetch_object()) {
                        $synArr[$r4->tid] = $r4->sciname;
                    }
                    $rs4->free();
                }
            }
		}
		return $synArr;
	}

	public function getClName(){
		return $this->clName;
	}

	public function setSearchTermsArr($stArr): void
    {
		if($stArr) {
            $this->searchTermsArr = $stArr;
        }
	}

	public function getSearchTermsArr(): array
    {
		return $this->searchTermsArr;
	}

    public function getTaxaArr(): array
    {
		return $this->taxaArr;
	}

	protected function cleanOutStr($str): string
    {
		return htmlspecialchars($str);
	}

	protected function cleanInputStr($str){
        $newStr = str_replace(array('"', "'"), array('', '%apos;'), $str);
		$newStr = strip_tags($newStr);
		return $newStr;
	}

	protected function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>
