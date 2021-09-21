<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceLabel{

	private $conn;
	private $collid;
	private $collArr = array();
	private $labelFieldArr = array();
    private $globalLabelFormatArr = array();
	private $errorArr = array();

	public function __construct(){
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->conn) {
			$this->conn->close();
		}
	}

	public function queryOccurrences($pArr): array
	{
		$canReadRareSpp = false;
		if($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll', $GLOBALS['USER_RIGHTS'])){
			$canReadRareSpp = true;
		}
		elseif((array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array($this->collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)) || (array_key_exists('RareSppReader', $GLOBALS['USER_RIGHTS']) && in_array($this->collid, $GLOBALS['USER_RIGHTS']['RareSppReader'], true))){
			$canReadRareSpp = true;
		}
		$retArr = array();
		if($this->collid){
			$sqlWhere = '';
			$sqlOrderBy = '';
			if($pArr['taxa']){
				$sqlWhere .= 'AND (o.sciname = "'.Sanitizer::cleanInStr($pArr['taxa']).'") ';
			}
			if($pArr['labelproject']){
				$sqlWhere .= 'AND (o.labelproject = "'.Sanitizer::cleanInStr($pArr['labelproject']).'") ';
			}
			if($pArr['recordenteredby']){
				$sqlWhere .= 'AND (o.recordenteredby = "'.Sanitizer::cleanInStr($pArr['recordenteredby']).'") ';
			}
			$date1 = Sanitizer::cleanInStr($pArr['date1']);
			$date2 = Sanitizer::cleanInStr($pArr['date2']);
			if(!$date1 && $date2){
				$date1 = $date2;
				$date2 = '';
			}
			$dateTarget = Sanitizer::cleanInStr($pArr['datetarget']);
			if($date1){
				if($date2){
					$sqlWhere .= 'AND (DATE('.$dateTarget.') BETWEEN "'.$date1.'" AND "'.$date2.'") ';
				}
				else{
					$sqlWhere .= 'AND (DATE('.$dateTarget.') = "'.$date1.'") ';
				}
			}
			if($pArr['recordnumber']){
				$rnArr = explode(',',Sanitizer::cleanInStr($pArr['recordnumber']));
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
			if($pArr['recordedby']){
				$recordedBy = Sanitizer::cleanInStr($pArr['recordedby']);
                if(strlen($recordedBy) < 4 || in_array(strtolower($recordedBy),array('best','little'))){
					$sqlWhere .= 'AND (o.recordedby LIKE "%'.$recordedBy.'%") ';
				}
				else{
					$sqlWhere .= 'AND (MATCH(f.recordedby) AGAINST("'.$recordedBy.'")) ';
				}
			}
			if($pArr['identifier']){
				$iArr = explode(',',Sanitizer::cleanInStr($pArr['identifier']));
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
				$sqlOrderBy .= ',o.catalogNumber';
			}
			if($this->collArr['colltype'] === 'General Observations'){
				$sqlWhere .= 'AND (o.collid = '.$this->collid.') ';
                $sqlWhere .= (!array_key_exists('extendedsearch', $pArr)) ? ' AND (o.observeruid = ' . $GLOBALS['SYMB_UID'] . ') ' : '';
			}
			elseif(!array_key_exists('extendedsearch', $pArr)){
				$sqlWhere .= 'AND (o.collid = '.$this->collid.') ';
			}
			$sql = 'SELECT o.occid, o.collid, IFNULL(o.duplicatequantity,1) AS q, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS collector, o.observeruid, '.
				'o.family, o.sciname, CONCAT_WS("; ",o.country, o.stateProvince, o.county, o.locality) AS locality, IFNULL(o.localitySecurity,0) AS localitySecurity '.
				'FROM omoccurrences AS o ';
			if(strpos($sqlWhere,'MATCH(f.recordedby)') || strpos($sqlWhere,'MATCH(f.locality)')){
				$sql.= 'INNER JOIN omoccurrencesfulltext AS f ON o.occid = f.occid ';
			}
			if($sqlWhere) {
				$sql .= 'WHERE ' . substr($sqlWhere, 4);
			}
            if($sqlOrderBy) {
                $sql .= ' ORDER BY ' . substr($sqlOrderBy, 1);
            }
            else {
                $sql .= ' ORDER BY (o.recordnumber+1)';
            }
			$sql .= ' LIMIT 400';
			//echo '<div>'.$sql.'</div>'; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$localitySecurity = $r->localitySecurity;
				if(!$localitySecurity || $canReadRareSpp || ((int)$r->observeruid === (int)$GLOBALS['SYMB_UID'])){
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
		$retArr = array();
		if($occidArr){
			$authorArr = array();
			$occidStr = implode(',',$occidArr);
			if(preg_match('/^[,\d]+$/', $occidStr)) {
                $sqlWhere = 'WHERE (o.occid IN('.$occidStr.')) ';
                $sql1 = 'SELECT o.occid, t2.author '.
                    'FROM taxa AS t INNER JOIN omoccurrences AS o ON t.tid = o.tidinterpreted '.
                    'INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
                    'INNER JOIN taxa AS t2 ON ts.parenttid = t2.tid '.
                    $sqlWhere.' AND t.rankid > 220 AND ts.taxauthid = 1 ';
                if(!$speciesAuthors) {
                    $sql1 .= 'AND t.unitname2 = t.unitname3 ';
                }
                //echo $sql1; exit;
                if($rs1 = $this->conn->query($sql1)){
                    while($row1 = $rs1->fetch_object()){
                        $authorArr[$row1->occid] = $row1->author;
                    }
                    $rs1->free();
                }
                $this->setLabelFieldArr();
                $sql2 = 'SELECT '.implode(',',$this->labelFieldArr).' FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tidinterpreted = t.tid '.$sqlWhere;
                //echo 'SQL: '.$sql2;
                if($rs2 = $this->conn->query($sql2)){
                    while($row2 = $rs2->fetch_assoc()){
                        $row2 = array_change_key_case($row2);
                        if(array_key_exists($row2['occid'],$authorArr)) {
                            $row2['parentauthor'] = $authorArr[$row2['occid']];
                        }
                        $retArr[$row2['occid']] = $row2;
                    }
                    $rs2->free();
                }
                if($retArr){
                    $sql = 'SELECT occid, identifiername, identifiervalue FROM omoccuridentifiers WHERE occid IN('.implode(',',array_keys($retArr)).')';
                    if($rs = $this->conn->query($sql)){
                        $otherCatArr = array();
                        $cnt = 0;
                        while($r = $rs->fetch_object()){
                            $otherCatArr[$r->occid][$cnt]['v'] = $r->identifiervalue;
                            $otherCatArr[$r->occid][$cnt]['n'] = $r->identifiername;
                            $cnt++;
                        }
                        $rs->free();
                        foreach($otherCatArr as $occid => $ocnArr){
                            $verbIdStr = $retArr[$occid]['othercatalognumbers'];
                            $ocnStr = '';
                            foreach($ocnArr as $idArr){
                                $ocnStr .= '; '.($idArr['n']?$idArr['n'].': ':'').$idArr['v'];
                                $verbIdStr = str_ireplace($idArr['n'],'',$verbIdStr);
                                $verbIdStr = str_ireplace($idArr['v'],'',$verbIdStr);
                            }
                            $ocnStr = trim($ocnStr,';,: ');
                            $verbIdStr = trim($verbIdStr,';,: ');
                            if($verbIdStr) {
                                $ocnStr .= '; ' . $verbIdStr;
                            }
                            $retArr[$occid]['othercatalognumbers'] = $ocnStr;
                        }
                    }
                }
            }
		}
		return $retArr;
	}

	public function exportLabelCsvFile($pArr): void
    {
		global $CHARSET;
		$occidArr = $pArr['occid'];
		if($occidArr){
			$speciesAuthors = 0;
			if(array_key_exists('speciesauthors',$pArr) && $pArr['speciesauthors']) {
                $speciesAuthors = 1;
            }
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
				$this->setLabelFieldArr();
				$headerArr = array_diff(array_keys($this->labelFieldArr), array('collid','duplicateQuantity','dateLastModified'));
				fputcsv($fh,$headerArr);
				$headerLcArr = array();
				foreach($headerArr as $k => $v){
					$headerLcArr[strtolower($v)] = $k;
				}
				foreach($labelArr as $occid => $occArr){
					$dupCnt = $pArr['q-'.$occid];
					if(isset($occArr['parentauthor']) && $occArr['parentauthor']){
						$occArr['scientificname_with_author'] = trim($occArr['speciesname'].' '.trim($occArr['parentauthor'].' '.$occArr['taxonrank']).' '.$occArr['infraspecificepithet'].' '.$occArr['scientificnameauthorship']);
					}
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

	private function setLabelFieldArr(): void
    {
		if(!$this->labelFieldArr){
			$this->labelFieldArr = array('occid'=>'o.occid', 'collid'=>'o.collid', 'catalogNumber'=>'o.catalognumber', 'otherCatalogNumbers'=>'o.othercatalognumbers', 'family'=>'o.family',
				'scientificName'=>'o.sciname AS scientificname', 'scientificName_with_author'=>'CONCAT_WS(" ",o.sciname,o.scientificnameauthorship) AS scientificname_with_author',
				'speciesName'=>'TRIM(CONCAT_WS(" ",t.unitind1,t.unitname1,t.unitind2,t.unitname2)) AS speciesname', 'taxonRank'=>'t.unitind3 AS taxonrank',
				'infraSpecificEpithet'=>'t.unitname3 AS infraspecificepithet', 'scientificNameAuthorship'=>'o.scientificnameauthorship', 'parentAuthor'=>'"" AS parentauthor','identifiedBy'=>'o.identifiedby',
				'dateIdentified'=>'o.dateidentified', 'identificationReferences'=>'o.identificationreferences', 'identificationRemarks'=>'o.identificationremarks', 'taxonRemarks'=>'o.taxonremarks',
				'identificationQualifier'=>'o.identificationqualifier', 'typeStatus'=>'o.typestatus', 'recordedBy'=>'o.recordedby', 'recordNumber'=>'o.recordnumber', 'associatedCollectors'=>'o.associatedcollectors',
				'eventDate'=>'DATE_FORMAT(o.eventdate,"%e %M %Y") AS eventdate', 'year'=>'o.year', 'month'=>'o.month', 'day'=>'o.day', 'monthName'=>'DATE_FORMAT(o.eventdate,"%M") AS monthname',
				'verbatimEventDate'=>'o.verbatimeventdate', 'habitat'=>'o.habitat', 'substrate'=>'o.substrate', 'occurrenceRemarks'=>'o.occurrenceremarks', 'associatedTaxa'=>'o.associatedtaxa',
				'dynamicProperties'=>'o.dynamicproperties','verbatimAttributes'=>'o.verbatimattributes', 'behavior'=>'behavior', 'reproductiveCondition'=>'o.reproductivecondition', 'cultivationStatus'=>'o.cultivationstatus',
				'establishmentMeans'=>'o.establishmentmeans','lifeStage'=>'lifestage','sex'=>'sex','individualCount'=>'individualcount','samplingProtocol'=>'samplingprotocol','preparations'=>'preparations',
				'country'=>'o.country', 'stateProvince'=>'o.stateprovince', 'county'=>'o.county', 'municipality'=>'o.municipality', 'locality'=>'o.locality', 'decimalLatitude'=>'o.decimallatitude',
				'decimalLongitude'=>'o.decimallongitude', 'geodeticDatum'=>'o.geodeticdatum', 'coordinateUncertaintyInMeters'=>'o.coordinateuncertaintyinmeters', 'verbatimCoordinates'=>'o.verbatimcoordinates',
				'minimumElevationInMeters'=>'o.minimumelevationinmeters', 'maximumElevationInMeters'=>'o.maximumelevationinmeters',
				'elevationInMeters'=>'CONCAT_WS(" - ",o.minimumElevationInMeters,o.maximumElevationInMeters) AS elevationinmeters', 'verbatimElevation'=>'o.verbatimelevation',
				'minimumDepthInMeters'=>'minimumdepthinmeters', 'maximumDepthInMeters'=>'maximumdepthinmeters', 'verbatimDepth'=>'verbatimdepth',
				'disposition'=>'o.disposition', 'storageLocation'=>'storagelocation', 'duplicateQuantity'=>'o.duplicatequantity', 'dateLastModified'=>'o.datelastmodified');
		}
	}

	public function getLabelBlock($blockArr,$occArr): string
    {
		$outStr = '';
		foreach($blockArr as $bArr){
			if(array_key_exists('divBlock', $bArr)){
				$outStr .= $this->getDivBlock($bArr['divBlock'],$occArr);
			}
			elseif(array_key_exists('fieldBlock', $bArr)){
				$delimiter = ($bArr['delimiter'] ?? '');
				$cnt = 0;
				$fieldDivStr = '';
				foreach($bArr['fieldBlock'] as $fieldArr){
					$fieldName = strtolower($fieldArr['field']);
					$fieldValue = trim($occArr[$fieldName]);
					if($fieldValue){
						if($delimiter && $cnt) {
                            $fieldDivStr .= $delimiter;
                        }
						$fieldDivStr .= '<span class="'.$fieldName.(isset($fieldArr['className'])?' '.$fieldArr['className']:'').'" '.(isset($fieldArr['style'])?'style="'.$fieldArr['style'].'"':'').'>';
						if(isset($fieldArr['prefix']) && $fieldArr['prefix']){
							$fieldDivStr .= '<span class="'.$fieldName.'Prefix" '.(isset($fieldArr['prefixStyle'])?'style="'.$fieldArr['prefixStyle']:'').'">'.$fieldArr['prefix'].'</span>';
						}
						$fieldDivStr .= $fieldValue;
						if(isset($fieldArr['suffix']) && $fieldArr['suffix']){
							$fieldDivStr .= '<span class="'.$fieldName.'Suffix" '.(isset($fieldArr['suffixStyle'])?'style="'.$fieldArr['suffixStyle']:'').'">'.$fieldArr['suffix'].'</span>';
						}
						$fieldDivStr .= '</span>';
						$cnt++;
					}
				}
				if($fieldDivStr) {
                    $outStr .= '<div class="field-block' . (isset($bArr['className']) ? ' ' . $bArr['className'] : '') . '" ' . (isset($bArr['style']) ? ' style="' . $bArr['style'] : '') . '">' . $fieldDivStr . '</div>';
                }
			}
		}
		return $outStr;
	}

	private function getDivBlock($divArr,$occArr): string
    {
		$contentStr = '';
		if(array_key_exists('blocks', $divArr)) {
            $contentStr = $this->getLabelBlock($divArr['blocks'], $occArr);
        }
		elseif(array_key_exists('content', $divArr)) {
            $contentStr = $divArr['content'];
        }
		if($contentStr){
			$attrStr = '';
			if(isset($divArr['className'])) {
                $attrStr .= 'class="' . $divArr['className'] . '"';
            }
			if(isset($divArr['style']) && $divArr['style']) {
                $attrStr .= 'style="' . $divArr['style'] . '"';
            }
			return '<div '.trim($attrStr).'>'.$contentStr.'</div>'."\n";
		}
		return '';
	}

	public function getLabelFormatByID($scope, $labelIndex){
		$returnArr = array();
        if(is_numeric($labelIndex)){
			$scopeArr = $this->getCurrentScopeLabelFormatArr($scope);
            if(array_key_exists((int)$labelIndex, $scopeArr['labelFormats'])){
                $returnArr = $scopeArr['labelFormats'][(int)$labelIndex];
            }
        }
		return $returnArr;
	}

	public function getLabelFormatArr($annotated = null): array
    {
		$retArr = array();
		if($GLOBALS['SYMB_UID']){
			$this->globalLabelFormatArr = $this->getCurrentScopeLabelFormatArr('g');
            if($this->globalLabelFormatArr){
                if($annotated){
                    if(isset($this->globalLabelFormatArr['labelFormats'])){
                        foreach($this->globalLabelFormatArr['labelFormats'] as $k => $labelObj){
                            unset($labelObj['labelFormats']);
                            $retArr['g'][$k] = $labelObj;
                        }
                    }
                }
                else {
                    $retArr['g'] = $this->globalLabelFormatArr['labelFormats'];
                }
            }
			if($this->collid){
				$collFormatArr = $this->getCurrentScopeLabelFormatArr('c');
				if(isset($collFormatArr['labelFormats'])){
                    if($annotated){
                        foreach($collFormatArr['labelFormats'] as $k => $labelObj){
                            unset($labelObj['labelBlocks']);
                            $retArr['c'][$k] = $labelObj;
                        }
                    }
                    else{
                        $retArr['c'] = $collFormatArr['labelFormats'];
                    }
                }
				else {
                    $retArr['c'] = array();
                }
			}
            $dynPropArr = $this->getCurrentScopeLabelFormatArr('u');
            if(isset($dynPropArr['labelFormats'])){
                if($annotated){
                    foreach($dynPropArr['labelFormats'] as $k => $labelObj){
                        unset($labelObj['labelBlocks']);
                        $retArr['u'][$k] = $labelObj;
                    }
                }
                else{
                    $retArr['u'] = $dynPropArr['labelFormats'];
                }
            }
            else {
                $retArr['u'] = array();
            }
		}
		return $retArr;
	}

    public function getCurrentScopeLabelFormatArr($scope): array
    {
        $retArr = array();
        if($scope === 'g'){
            if(file_exists($GLOBALS['SERVER_ROOT'].'/content/json/globallabeljson.json')){
                $retArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'].'/content/json/globallabeljson.json'), true);
            }
            else{
                $retArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'].'/config/labeljson.json'), true);
            }
        }
        elseif($scope === 'c'){
            if($this->collid && file_exists($GLOBALS['SERVER_ROOT'] . '/content/json/collection' . $this->collid . 'labeljson.json')) {
                $retArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'].'/content/json/collection'.$this->collid.'labeljson.json'), true);
            }
        }
        elseif($scope === 'u'){
            if($GLOBALS['SYMB_UID'] && file_exists($GLOBALS['SERVER_ROOT'] . '/content/json/user' . $GLOBALS['SYMB_UID'] . 'labeljson.json')) {
                $retArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'].'/content/json/user'.$GLOBALS['SYMB_UID'].'labeljson.json'), true);
            }
        }
        return $retArr;
    }

    public function saveNewLabelFormatJson($pArr): bool
    {
        $status = true;
        $scope = $pArr['scope'];
        $newFormatArr = json_decode($pArr['json'], true);
        if($scope && $newFormatArr){
            $scopeArr = $this->getCurrentScopeLabelFormatArr($scope);
            $scopeArr['labelFormats'][] = $newFormatArr;
            if($scope === 'g'){
                $status = $this->saveGlobalLabelJson($scopeArr);
            }
            elseif($scope === 'c'){
                $status = $this->saveCollectionLabelJson($scopeArr);
            }
            elseif($scope === 'u'){
                $status = $this->saveUserLabelJson($scopeArr);
            }
        }
        return $status;
    }

	public function saveLabelFormatJson($pArr): bool
    {
		$status = true;
		$scope = $pArr['scope'];
        $labelIndex = $pArr['index'] ?? '';
        $newFormatArr = json_decode($pArr['json'], true);
        if(is_numeric($labelIndex) && $newFormatArr){
			$scopeArr = $this->getCurrentScopeLabelFormatArr($scope);
            $scopeArr['labelFormats'][(int)$labelIndex] = $newFormatArr;
            if($scope === 'g'){
                $status = $this->saveGlobalLabelJson($scopeArr);
			}
			elseif($scope === 'c'){
                $status = $this->saveCollectionLabelJson($scopeArr);
			}
			elseif($scope === 'u'){
                $status = $this->saveUserLabelJson($scopeArr);
			}
		}
		return $status;
	}

	public function deleteLabelFormat($scope, $labelIndex): bool
    {
		$status = true;
        if(is_numeric($labelIndex)){
			$scopeArr = $this->getCurrentScopeLabelFormatArr($scope);
            unset($scopeArr['labelFormats'][(int)$labelIndex]);
            if($scope === 'g'){
                $status = $this->saveGlobalLabelJson($scopeArr);
            }
            elseif($scope === 'c'){
                $status = $this->saveCollectionLabelJson($scopeArr);
            }
            elseif($scope === 'u'){
                $status = $this->saveUserLabelJson($scopeArr);
            }
		}
		return $status;
	}

	private function saveGlobalLabelJson($formatArr): bool
    {
		$status = false;
		$jsonStr = json_encode($formatArr);
        if($fh = fopen($GLOBALS['SERVER_ROOT'].'/content/json/globallabeljson.json', 'wb')){
			if(!fwrite($fh,$jsonStr)){
				$this->errorArr[] = 'ERROR saving label format to global file ';
				$status = false;
			}
			fclose($fh);
		}
		else{
			$this->errorArr[] = 'ERROR saving label format: unable opening/creating json file for writing';
			$status = false;
		}
		return $status;
	}

	private function saveCollectionLabelJson($formatArr): bool
    {
		$status = false;
        $jsonStr = json_encode($formatArr);
        if($fh = fopen($GLOBALS['SERVER_ROOT'].'/content/json/collection'.$this->collid.'labeljson.json', 'wb')){
            if(!fwrite($fh,$jsonStr)){
                $this->errorArr[] = 'ERROR saving label format to collection file ';
                $status = false;
            }
            fclose($fh);
        }
        else{
            $this->errorArr[] = 'ERROR saving label format: unable opening/creating json file for writing';
            $status = false;
        }
        return $status;
	}

	private function saveUserLabelJson($formatArr): bool
    {
		$status = false;
        $jsonStr = json_encode($formatArr);
        if($fh = fopen($GLOBALS['SERVER_ROOT'].'/content/json/user'.$GLOBALS['SYMB_UID'].'labeljson.json', 'wb')){
            if(!fwrite($fh,$jsonStr)){
                $this->errorArr[] = 'ERROR saving label format to collection file ';
                $status = false;
            }
            fclose($fh);
        }
        else{
            $this->errorArr[] = 'ERROR saving label format: unable opening/creating json file for writing';
            $status = false;
        }
        return $status;
	}

	public function getAnnoArray($detidArr, $speciesAuthors): array
    {
		$retArr = array();
		if($detidArr){
			$authorArr = array();
			$sqlWhere = 'WHERE (d.detid IN('.implode(',',$detidArr).')) ';
			$sql1 = 'SELECT d.detid, t2.author '.
				'FROM (taxa AS t INNER JOIN omoccurrences AS o ON t.tid = o.tidinterpreted) '.
				'INNER JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
				'INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
				'INNER JOIN taxa AS t2 ON ts.parenttid = t2.tid '.
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

            $sql2 = 'SELECT d.detid, d.identifiedBy, d.dateIdentified, d.sciname, d.scientificNameAuthorship, d.identificationQualifier, '.
                'd.identificationReferences, d.identificationRemarks, IFNULL(o.catalogNumber,o.otherCatalogNumbers) AS catalogNumber '.
                'FROM omoccurdeterminations AS d LEFT JOIN omoccurrences AS o ON d.occid = o.occid '.$sqlWhere;
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

    public function getBarcodePng($text, $size, $type)
    {
        $bcStr = '';
        if($type === 'code128' || $type === 'code128b'){
            $chksum = 104;
            $bcArr = array(' ' => '212222', '!' => '222122', '\''=> '222221', '#' => '121223', '$' => '121322', '%' => '131222', '&' => '122213',"'"=> '122312', '(' => '132212', ')' => '221213', '*' => '221312', '+' => '231212', ',' => '112232', '-' => '122132', '.' => '122231', '/' => '113222', '0' => '123122', '1' => '123221', '2' => '223211', '3' => '221132', '4' => '221231', '5' => '213212', '6' => '223112', '7' => '312131', '8' => '311222', '9' => '321122', ':' => '321221', ';' => '312212', '<' => '322112', '=' => '322211', '>' => '212123', '?' => '212321', '@' => '232121', 'A' => '111323', 'B' => '131123', 'C' => '131321', 'D' => '112313', 'E' => '132113', 'F' => '132311', 'G' => '211313', 'H' => '231113', 'I' => '231311', 'J' => '112133', 'K' => '112331', 'L' => '132131', 'M' => '113123', 'N' => '113321', 'O' => '133121', 'P' => '313121', 'Q' => '211331', 'R' => '231131', 'S' => '213113', 'T' => '213311', 'U' => '213131', 'V' => '311123', 'W' => '311321', 'X' => '331121', 'Y' => '312113', 'Z' => '312311', '[' => '332111',"\\"=> '314111', ']' => '221411', '^' => '431111', '_' => '111224',"\`"=> '111422', 'a' => '121124', 'b' => '121421', 'c' => '141122', 'd' => '141221', 'e' => '112214', 'f' => '112412', 'g' => '122114', 'h' => '122411', 'i' => '142112', 'j' => '142211', 'k' => '241211', 'l' => '221114', 'm' => '413111', 'n' => '241112', 'o' => '134111', 'p' => '111242', 'q' => '121142', 'r' => '121241', 's' => '114212', 't' => '124112', 'u' => '124211', 'v' => '411212', 'w' => '421112', 'x' => '421211', 'y' => '212141', 'z' => '214121', '{' => '412121', '|' => '111143', '}' => '111341', '~' => '131141', 'DEL' => '114113', 'FNC 3' => '114311', 'FNC 2' => '411113', 'SHIFT' => '411311', 'CODE C' => '113141', 'FNC 4' => '114131', 'CODE A' => '311141', 'FNC 1' => '411131', 'Start A' => '211412', 'Start B' => '211214', 'Start C' => '211232', 'Stop' => '2331112');
            $bcKeys = array_keys($bcArr);
            $bcVals = array_flip($bcKeys);
            for($x = 1, $xMax = strlen($text); $x <= $xMax; $x++ ){
                $key = $text[($x - 1)];
                $bcStr .= $bcArr[$key];
                $chksum += ($bcVals[$key] * $x);
            }
            $index = $chksum - ((int)($chksum / 103) * 103);
            $bcStr .= $bcArr[$bcKeys[(int)$index]];
            $bcStr = '211214' . $bcStr . '2331112';
        }
        elseif($type === 'code128a'){
            $chksum = 103;
            $bcArr = array(' ' => '212222', '!' => '222122','\''=> '222221', '#' => '121223', '$' => '121322', '%' => '131222', '&' => '122213',"'"=> '122312', '(' => '132212', ')' => '221213', '*' => '221312', '+' => '231212', ',' => '112232', '-' => '122132', '.' => '122231', '/' => '113222', '0' => '123122', '1' => '123221', '2' => '223211', '3' => '221132', '4' => '221231', '5' => '213212', '6' => '223112', '7' => '312131', '8' => '311222', '9' => '321122', ':' => '321221', ';' => '312212', '<' => '322112', '=' => '322211', '>' => '212123', '?' => '212321', '@' => '232121', 'A' => '111323', 'B' => '131123', 'C' => '131321', 'D' => '112313', 'E' => '132113', 'F' => '132311', 'G' => '211313', 'H' => '231113', 'I' => '231311', 'J' => '112133', 'K' => '112331', 'L' => '132131', 'M' => '113123', 'N' => '113321', 'O' => '133121', 'P' => '313121', 'Q' => '211331', 'R' => '231131', 'S' => '213113', 'T' => '213311', 'U' => '213131', 'V' => '311123', 'W' => '311321', 'X' => '331121', 'Y' => '312113', 'Z' => '312311', '[' => '332111',"\\"=> '314111', ']' => '221411', '^' => '431111', '_' => '111224', 'NUL' => '111422', 'SOH' => '121124', 'STX' => '121421', 'ETX' => '141122', 'EOT' => '141221', 'ENQ' => '112214', 'ACK' => '112412', 'BEL' => '122114', 'BS' => '122411', 'HT' => '142112', 'LF' => '142211', 'VT' => '241211', 'FF' => '221114', 'CR' => '413111', 'SO' => '241112', 'SI' => '134111', 'DLE' => '111242', 'DC1' => '121142', 'DC2' => '121241', 'DC3' => '114212', 'DC4' => '124112', 'NAK' => '124211', 'SYN' => '411212', 'ETB' => '421112', 'CAN' => '421211', 'EM' => '212141', 'SUB' => '214121', 'ESC' => '412121', 'FS' => '111143', 'GS' => '111341', 'RS' => '131141', 'US' => '114113', 'FNC 3' => '114311', 'FNC 2' => '411113', 'SHIFT' => '411311', 'CODE C' => '113141', 'CODE B' => '114131', 'FNC 4' => '311141', 'FNC 1' => '411131', 'Start A' => '211412', 'Start B' => '211214', 'Start C' => '211232', 'Stop' => '2331112');
            $bcKeys = array_keys($bcArr);
            $bcVals = array_flip($bcKeys);
            for($x = 1, $xMax = strlen($text); $x <= $xMax; $x++ ){
                $key = $text[($x - 1)];
                $bcStr .= $bcArr[$key];
                $chksum += ($bcVals[$key] * $x);
            }
            $index = $chksum - ((int)($chksum / 103) * 103);
            $bcStr .= $bcArr[$bcKeys[(int)$index]];
            $bcStr = '211412' . $bcStr . '2331112';
        }
        elseif($type === 'code39') {
            $bcArr = array('0' => '111221211', '1' => '211211112', '2' => '112211112', '3' => '212211111', '4' => '111221112', '5' => '211221111', '6' => '112221111', '7' => '111211212', '8' => '211211211', '9' => '112211211', 'A' => '211112112', 'B' => '112112112', 'C' => '212112111', 'D' => '111122112', 'E' => '211122111', 'F' => '112122111', 'G' => '111112212', 'H' => '211112211', 'I' => '112112211', 'J' => '111122211', 'K' => '211111122', 'L' => '112111122', 'M' => '212111121', 'N' => '111121122', 'O' => '211121121', 'P' => '112121121', 'Q' => '111111222', 'R' => '211111221', 'S' => '112111221', 'T' => '111121221', 'U' => '221111112', 'V' => '122111112', 'W' => '222111111', 'X' => '121121112', 'Y' => '221121111', 'Z' => '122121111', '-' => '121111212', '.' => '221111211', ' ' => '122111211', '$' => '121212111', '/' => '121211121', '+' => '121112121', '%' => '111212121', '*' => '121121211');
            $text = strtoupper($text);
            for($x = 1, $xMax = strlen($text); $x<= $xMax; $x++ ){
                $index = $text[($x - 1)];
                if((string)$index === '0'){
                    $bcStr .= '111221211' . '1';
                }
                else{
                    $bcStr .= $bcArr[$index] . '1';
                }
            }
            $bcStr = '1211212111' . $bcStr . '121121211';
        }
        elseif($type === 'code25'){
            $bcArr1 = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
            $bcArr2 = array('3-1-1-1-3', '1-3-1-1-3', '3-3-1-1-1', '1-1-3-1-3', '3-1-3-1-1', '1-3-3-1-1', '1-1-1-3-3', '3-1-1-3-1', '1-3-1-3-1', '1-1-3-3-1');
            for($x = 1, $xMax = strlen($text); $x <= $xMax; $x++ ){
                for($y = 0, $yMax = count($bcArr1); $y < $yMax; $y++ ){
                    if($text[($x - 1)] === $bcArr1[$y]){
                        $temp[$x] = $bcArr2[$y];
                    }
                }
            }
            for($x=1, $xMax = strlen($text); $x<= $xMax; $x+=2 ){
                if(isset($temp[$x], $temp[($x + 1)])){
                    $temp1 = explode( '-', $temp[$x] );
                    $temp2 = explode( '-', $temp[($x + 1)] );
                    for($y = 0, $yMax = count($temp1); $y < $yMax; $y++ ){
                        if($temp1 && array_key_exists($y, $temp1) && $temp2 && array_key_exists($y, $temp2)){
                            $bcStr .= $temp1[$y] . $temp2[$y];
                        }
                    }
                }
            }
            $bcStr = '1111' . $bcStr . '311';
        }
        elseif($type === 'codabar'){
            $bcArr1 = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '$', ':', '/', '.', '+', 'A', 'B', 'C', 'D');
            $bcArr2 = array('1111221', '1112112', '2211111', '1121121', '2111121', '1211112', '1211211', '1221111', '2112111', '1111122', '1112211', '1122111', '2111212', '2121112', '2121211', '1121212', '1122121', '1212112', '1112122', '1112221');
            $text = strtoupper($text);
            for($x = 1, $xMax = strlen($text); $x<= $xMax; $x++ ){
                for($y = 0, $yMax = count($bcArr1); $y< $yMax; $y++ ){
                    if((string)$text[($x - 1)] === '0'){
                        $bcStr .= '1111122' . '1';
                    }
                    elseif($text[($x - 1)] === $bcArr1[$y]){
                        $bcStr .= $bcArr2[$y] . '1';
                    }
                }
            }
            $bcStr = '11221211' . $bcStr . '1122121';
        }
        $bcLength = 20;
        for($i=1, $iMax = strlen($bcStr); $i <= $iMax; $i++ ){
            $bcLength += (int)($bcStr[($i - 1)]);
        }
        $img_width = $bcLength;
        $img_height = $size;
        $image = imagecreate($img_width, $img_height);
        $black = imagecolorallocate ($image, 0, 0, 0);
        $white = imagecolorallocate ($image, 255, 255, 255);
        imagefill( $image, 0, 0, $white );
        $location = 10;
        for($position = 1, $positionMax = strlen($bcStr); $position <= $positionMax; $position++ ){
            $cur_size = $location + (int)$bcStr[($position - 1)];
            imagefilledrectangle($image, $location, 0, $cur_size, $img_height, ($position % 2 === 0 ? $white : $black));
            $location = $cur_size;
        }
        return $image;
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

	public function getAnnoQueue(): array
    {
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT o.occid, d.detid, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS collector, '.
				'CONCAT_WS(" ",d.identificationQualifier,d.sciname) AS sciname, '.
				'CONCAT_WS(", ",d.identifiedBy,d.dateIdentified,d.identificationRemarks,d.identificationReferences) AS determination '.
				'FROM omoccurrences AS o INNER JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
				'WHERE (o.collid = '.$this->collid.') AND (d.printqueue = 1) ';
			if($this->collArr['colltype'] === 'General Observations'){
				$sql .= ' AND (o.observeruid = '.$GLOBALS['SYMB_UID'].') ';
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

	public function getLabelProjects(): array
    {
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT DISTINCT labelproject FROM omoccurrences WHERE labelproject IS NOT NULL AND collid = '.$this->collid.' ';
			if($this->collArr['colltype'] === 'General Observations' && !array_key_exists('extendedsearch', $GLOBALS['_POST'])) {
                $sql .= 'AND (observeruid = ' . $GLOBALS['SYMB_UID'] . ') ';
            }
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = $r->labelproject;
			}
			sort($retArr);
			$rs->free();
		}
		return $retArr;
	}

	public function getDatasetProjects(): array
    {
		$retArr = array();
		if($this->collid){
			$sql = 'SELECT DISTINCT ds.datasetid, ds.name '.
				'FROM omoccurdatasets AS ds INNER JOIN userroles AS r ON ds.datasetid = r.tablepk '.
				'INNER JOIN omoccurdatasetlink AS dl ON ds.datasetid = dl.datasetid '.
				'INNER JOIN omoccurrences AS o ON dl.occid = o.occid '.
				'WHERE (r.tablename = "omoccurdatasets") AND (o.collid = '.$this->collid.') ';
			if($this->collArr['colltype'] === 'General Observations' && !array_key_exists('extendedsearch', $GLOBALS['_POST'])) {
                $sql .= 'AND (o.observeruid = ' . $GLOBALS['SYMB_UID'] . ') ';
            }
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->datasetid] = $r->name;
			}
			$rs->free();
		}
		return $retArr;
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
			$sql = 'SELECT institutioncode, collectioncode, collectionname, colltype, dynamicProperties FROM omcollections WHERE collid = '.$this->collid;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$this->collArr['instcode'] = $r->institutioncode;
					$this->collArr['collcode'] = $r->collectioncode;
					$this->collArr['collname'] = $r->collectionname;
					$this->collArr['colltype'] = $r->colltype;
					$this->collArr['dynprops'] = $r->dynamicProperties;
				}
				$rs->free();
			}
		}
	}

	public function getErrorArr(): array
	{
		return $this->errorArr;
	}
}
