<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/OccurrenceUtilities.php');
include_once(__DIR__ . '/ChecklistVoucherAdmin.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceManager{

    protected $conn;
    protected $taxaArr = array();
    protected $searchTidArr = array();
    protected $searchSynArr = array();
    protected $taxaSearchType;
    protected $searchTermsArr = array();
    protected $localSearchArr = array();
    private $clName;
    private $collArrIndex = 0;

    public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn){
            $this->conn->close();
            $this->conn = null;
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

    public function getDatasetArr(): array
    {
        $retArr = array();
        if($GLOBALS['SYMB_UID']){
            $sql = 'SELECT DISTINCT datasetid, name FROM omoccurdatasets WHERE uid = '.$GLOBALS['SYMB_UID'].' OR datasetid IN(SELECT tablepk FROM userroles WHERE uid = '.$GLOBALS['SYMB_UID'].' AND role IN("DatasetAdmin","DatasetEditor"))';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->datasetid] = $r->name;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function addOccurrencesToDataset($datasetID,$mapWhere): bool
    {
        if(is_numeric($datasetID)) {
            $sql = 'INSERT IGNORE INTO omoccurdatasetlink(occid,datasetid) SELECT DISTINCT o.occid, '.$datasetID.' ';
            $sql .= 'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.$this->setTableJoins($mapWhere).$mapWhere;
            if(!$this->conn->query($sql)){
                return false;
            }
        }
        return true;
    }

    public function getSqlWhere($image = null): string
    {
        $sqlWhere = '';
        $retStr = '';
        if(array_key_exists('clid',$this->searchTermsArr) && $this->searchTermsArr['clid']){
            $sqlWhere .= 'AND (v.clid IN(' .$this->searchTermsArr['clid']. ')) ';
        }
        if(array_key_exists('dsid',$this->searchTermsArr) && $this->searchTermsArr['dsid']){
            $sqlWhere .= 'AND (o.occid IN(SELECT occid FROM omoccurdatasetlink WHERE datasetid = ' .$this->searchTermsArr['dsid']. ')) ';
        }
        if(array_key_exists('db', $this->searchTermsArr) && $this->searchTermsArr['db'] && $this->searchTermsArr['db'] !== 'all') {
            $sqlWhere .= 'AND (o.collid IN(' .Sanitizer::cleanInStr($this->searchTermsArr['db']). ')) ';
        }
        if(array_key_exists('taxa',$this->searchTermsArr) && $this->searchTermsArr['taxa']){
            $sqlWhereTaxa = '';
            $useThes = (array_key_exists('usethes',$this->searchTermsArr)?$this->searchTermsArr['usethes']:0);
            $this->taxaSearchType = (int)$this->searchTermsArr['taxontype'];
            $taxaArr = explode(';',trim($this->searchTermsArr['taxa']));
            foreach($taxaArr as $sName){
                $this->taxaArr[trim($sName)] = array();
            }
            if($this->taxaSearchType === 5){
                $this->setSciNamesByVerns();
            }
            elseif($useThes){
                $this->setSynonyms();
            }
            $this->setSearchTids();
            foreach($this->taxaArr as $key => $valueArray){
                if($this->taxaSearchType === 4){
                    if($image){
                        $sqlWhereTaxa = 'OR (t.sciname = "'.Sanitizer::cleanInStr($key).'") ';
                    }
                    else{
                        $sqlWhereTaxa = 'OR (o.sciname = "'.Sanitizer::cleanInStr($key).'") ';
                    }
                }
                elseif($this->taxaSearchType === 5){
                    $famArr = array();
                    if(array_key_exists('families',$valueArray)){
                        $famArr = $valueArray['families'];
                    }
                    if(array_key_exists('tid',$valueArray)){
                        $tidArr = $valueArray['tid'];
                        $sql = 'SELECT DISTINCT t.sciname '.
                            'FROM taxa AS t INNER JOIN taxaenumtree AS e ON t.tid = e.tid '.
                            'WHERE t.rankid = 140 AND e.parenttid IN('.implode(',',$tidArr).')';
                        $rs = $this->conn->query($sql);
                        while($r = $rs->fetch_object()){
                            $famArr[] = $r->family;
                        }
                    }
                    if($famArr){
                        $famArr = array_unique($famArr);
                        $sqlWhereTaxa .= 'OR (o.family IN("'.Sanitizer::cleanInStr(implode('","',$famArr)).'")) ';
                        $sqlWhereTaxa .= 'OR (ts.family IN("'.Sanitizer::cleanInStr(implode('","',$famArr)).'")) ';
                    }
                    if(array_key_exists('scinames',$valueArray)){
                        foreach($valueArray['scinames'] as $sciName){
                            $sqlWhereTaxa .= "OR (o.sciname LIKE '".Sanitizer::cleanInStr($sciName)."%') ";
                        }
                    }
                }
                else{
                    if($this->taxaSearchType === 2 || ($this->taxaSearchType === 1 && (strtolower(substr($key,-5)) === 'aceae' || strtolower(substr($key,-4)) === 'idae'))){
                        $sqlWhereTaxa .= "OR (o.family = '".Sanitizer::cleanInStr($key)."') ";
                        $sqlWhereTaxa .= "OR (ts.family = '".Sanitizer::cleanInStr($key)."') ";
                        $sqlWhereTaxa .= "OR (o.sciname = '".Sanitizer::cleanInStr($key)."') ";
                        $sqlWhereTaxa .= "OR (t.sciname = '".Sanitizer::cleanInStr($key)."') ";
                    }
                    if($this->taxaSearchType === 3 || ($this->taxaSearchType === 1 && strtolower(substr($key,-5)) !== 'aceae' && strtolower(substr($key,-4)) !== 'idae')){
                        $sqlWhereTaxa .= "OR (o.sciname LIKE '".Sanitizer::cleanInStr($key)."%') ";
                    }
                }
            }
            if($this->searchSynArr){
                if($this->taxaSearchType === 1 || $this->taxaSearchType === 2 || $this->taxaSearchType === 5){
                    foreach($this->searchSynArr as $synTid => $sciName){
                        if(strpos($sciName,'aceae') || strpos($sciName,'idae')){
                            $sqlWhereTaxa .= "OR (o.family = '".Sanitizer::cleanInStr($sciName)."') ";
                            $sqlWhereTaxa .= "OR (ts.family = '".Sanitizer::cleanInStr($sciName)."') ";
                        }
                    }
                }
            }
            if($this->searchTidArr){
                if($image){
                    $sqlWhereTaxa .= 'OR (i.tid IN('.implode(',',$this->searchTidArr).')) ';
                }
                else{
                    $sqlWhereTaxa .= 'OR (o.tidinterpreted IN('.implode(',',$this->searchTidArr).')) ';
                }
            }
            $sqlWhere .= 'AND (' .substr($sqlWhereTaxa,3). ') ';
        }
        if(array_key_exists('country',$this->searchTermsArr) && $this->searchTermsArr['country']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['country']);
            $countryArr = explode(';',$searchStr);
            if($countryArr){
                $tempArr = array();
                foreach($countryArr as $k => $value){
                    if($value === 'NULL'){
                        $countryArr[$k] = 'Country IS NULL';
                        $tempArr[] = '(ISNULL(o.Country))';
                    }
                    else{
                        $tempArr[] = '(o.Country = "'.Sanitizer::cleanInStr($value).'")';
                    }
                }
                $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
                $this->localSearchArr[] = implode(' OR ',$countryArr);
            }
        }
        if(array_key_exists('state',$this->searchTermsArr) && $this->searchTermsArr['state']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['state']);
            $stateAr = explode(';',$searchStr);
            if($stateAr){
                $tempArr = array();
                foreach($stateAr as $k => $value){
                    if($value === 'NULL'){
                        $tempArr[] = '(ISNULL(o.StateProvince))';
                        $stateAr[$k] = 'State IS NULL';
                    }
                    else{
                        $tempArr[] = '(o.StateProvince = "'.Sanitizer::cleanInStr($value).'")';
                    }
                }
                $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
                $this->localSearchArr[] = implode(' OR ',$stateAr);
            }
        }
        if(array_key_exists('county',$this->searchTermsArr) && $this->searchTermsArr['county']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['county']);
            $countyArr = explode(';',$searchStr);
            if($countyArr){
                $tempArr = array();
                foreach($countyArr as $k => $value){
                    if($value === 'NULL'){
                        $tempArr[] = '(ISNULL(o.county))';
                        $countyArr[$k] = 'County IS NULL';
                    }
                    else{
                        $value = trim(str_ireplace(' county',' ',$value));
                        $tempArr[] = '(o.county LIKE "'.Sanitizer::cleanInStr($value).'%")';
                    }
                }
                $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
                $this->localSearchArr[] = implode(' OR ',$countyArr);
            }
        }
        if(array_key_exists('local',$this->searchTermsArr) && $this->searchTermsArr['local']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['local']);
            $localArr = explode(';',$searchStr);
            if($localArr){
                $tempArr = array();
                foreach($localArr as $k => $value){
                    $value = trim($value);
                    if($value === 'NULL'){
                        $tempArr[] = '(ISNULL(o.locality))';
                        $localArr[$k] = 'Locality IS NULL';
                    }
                    else{
                        $tempArr[] = '(o.municipality LIKE "'.Sanitizer::cleanInStr($value).'%" OR o.Locality LIKE "%'.Sanitizer::cleanInStr($value).'%")';
                    }
                }
                $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
                $this->localSearchArr[] = implode(' OR ',$localArr);
            }
        }
        if((array_key_exists('elevlow',$this->searchTermsArr) && is_numeric($this->searchTermsArr['elevlow'])) || (array_key_exists('elevhigh',$this->searchTermsArr) && is_numeric($this->searchTermsArr['elevhigh']))){
            $elevlow = 0;
            $elevhigh = 30000;
            if (array_key_exists('elevlow',$this->searchTermsArr))  { $elevlow = $this->searchTermsArr['elevlow']; }
            if (array_key_exists('elevhigh',$this->searchTermsArr))  { $elevhigh = $this->searchTermsArr['elevhigh']; }
            $sqlWhere .= 'AND ( ' .
                '	  ( minimumElevationInMeters >= ' .$elevlow. ' AND maximumElevationInMeters <= ' .$elevhigh. ' ) OR ' .
                '	  ( ISNULL(maximumElevationInMeters) AND minimumElevationInMeters >= ' .$elevlow. ' AND minimumElevationInMeters <= ' .$elevhigh. ' ) ' .
                '	) ';
        }
        if(array_key_exists('assochost',$this->searchTermsArr) && $this->searchTermsArr['assochost']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['assochost']);
            $hostAr = explode(';',$searchStr);
            if($hostAr){
                $tempArr = array();
                foreach($hostAr as $k => $value){
                    if($value !== 'NULL'){
                        $tempArr[] = '(oas.relationship = "host" AND oas.verbatimsciname LIKE "%'.Sanitizer::cleanInStr($value).'%")';
                    }
                }
                $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
                $this->localSearchArr[] = implode(' OR ',$hostAr);
            }
        }
        if((array_key_exists('upperlat',$this->searchTermsArr) && $this->searchTermsArr['upperlat']) || (array_key_exists('pointlat',$this->searchTermsArr) && $this->searchTermsArr['pointlat']) || (array_key_exists('circleArr',$this->searchTermsArr) && $this->searchTermsArr['circleArr']) || (array_key_exists('polyArr',$this->searchTermsArr) && $this->searchTermsArr['polyArr'])){
            $geoSqlStrArr = array();
            if(array_key_exists('upperlat',$this->searchTermsArr) && $this->searchTermsArr['upperlat']){
                $geoSqlStrArr[] = '(o.DecimalLatitude BETWEEN ' .Sanitizer::cleanInStr($this->searchTermsArr['bottomlat']). ' AND ' .Sanitizer::cleanInStr($this->searchTermsArr['upperlat']). ' AND ' .
                    'o.DecimalLongitude BETWEEN ' .Sanitizer::cleanInStr($this->searchTermsArr['leftlong']). ' AND ' .Sanitizer::cleanInStr($this->searchTermsArr['rightlong']). ') ';
                $this->localSearchArr[] = 'Lat: >' .$this->searchTermsArr['bottomlat']. ', <' .$this->searchTermsArr['upperlat']. '; Long: >' .$this->searchTermsArr['leftlong']. ', <' .$this->searchTermsArr['rightlong'];
            }
            if(array_key_exists('pointlat',$this->searchTermsArr) && $this->searchTermsArr['pointlat']){
                $radius = $this->searchTermsArr['groundradius'] * 0.621371192;
                $geoSqlStrArr[] = '((3959 * ACOS(COS(RADIANS(o.DecimalLatitude)) * COS(RADIANS('.$this->searchTermsArr['pointlat'].')) * COS(RADIANS('.$this->searchTermsArr['pointlong'].') - RADIANS(o.DecimalLongitude)) + SIN(RADIANS(o.DecimalLatitude)) * SIN(RADIANS('.$this->searchTermsArr['pointlat'].')))) <= '.$radius.') ';
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
                        $radius = $oArr['groundradius'] * 0.621371192;
                        $sqlFragArr[] = '((3959 * ACOS(COS(RADIANS(o.DecimalLatitude)) * COS(RADIANS('.$oArr['pointlat'].')) * COS(RADIANS('.$oArr['pointlong'].') - RADIANS(o.DecimalLongitude)) + SIN(RADIANS(o.DecimalLatitude)) * SIN(RADIANS('.$oArr['pointlat'].')))) <= '.$radius.') ';
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
                        $sqlFragArr[] = "(ST_Within(p.point,ST_GeomFromText('".$geom." '))) ";
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
            if($collectorArr && count($collectorArr) === 1){
                if($collectorArr[0] === 'NULL'){
                    $tempArr[] = '(ISNULL(o.recordedBy))';
                    $collectorArr[] = 'Collector IS NULL';
                }
                else{
                    $tempInnerArr = array();
                    $collValueArr = explode(' ',trim($collectorArr[0]));
                    foreach($collValueArr as $collV){
                        if(strlen($collV) < 4 || strtolower($collV) === 'best'){
                            $tempInnerArr[] = '(o.recordedBy LIKE "%'.Sanitizer::cleanInStr($collV).'%")';
                        }
                        else{
                            $tempInnerArr[] = '(MATCH(f.recordedby) AGAINST("'.Sanitizer::cleanInStr($collV).'")) ';
                        }
                    }
                    $tempArr[] = implode(' AND ', $tempInnerArr);
                }
            }
            elseif(count($collectorArr) > 1){
                $collStr = current($collectorArr);
                if(strlen($collStr) < 4 || strtolower($collStr) === 'best'){
                    $tempInnerArr[] = '(o.recordedBy LIKE "%'.Sanitizer::cleanInStr($collStr).'%")';
                }
                else{
                    $tempArr[] = '(MATCH(f.recordedby) AGAINST("'.Sanitizer::cleanInStr($collStr).'")) ';
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
                        $catTerm = '(o.recordnumber BETWEEN "'.Sanitizer::cleanInStr($term1).'" AND "'.Sanitizer::cleanInStr($term2).'")';
                        $catTerm .= ' AND (length(o.recordnumber) <= '.strlen($term2).')';
                        $rnWhere .= 'OR ('.$catTerm.')';
                    }
                }
                else{
                    $rnWhere .= 'OR (o.recordNumber = "'.Sanitizer::cleanInStr($v).'") ';
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
            if($dateArr){
                if($dateArr[0] === 'NULL'){
                    $sqlWhere .= 'AND (ISNULL(o.eventdate)) ';
                    $this->localSearchArr[] = 'Date IS NULL';
                }
                elseif($eDate1 = $this->formatDate($dateArr[0])){
                    $eDate2 = (count($dateArr)>1?$this->formatDate($dateArr[1]):'');
                    if($eDate2){
                        $sqlWhere .= 'AND (o.eventdate BETWEEN "'.Sanitizer::cleanInStr($eDate1).'" AND "'.Sanitizer::cleanInStr($eDate2).'") ';
                    }
                    else if(substr($eDate1,-5) === '00-00'){
                        $sqlWhere .= 'AND (o.eventdate LIKE "'.Sanitizer::cleanInStr(substr($eDate1,0,5)).'%") ';
                    }
                    elseif(substr($eDate1,-2) === '00'){
                        $sqlWhere .= 'AND (o.eventdate LIKE "'.Sanitizer::cleanInStr(substr($eDate1,0,8)).'%") ';
                    }
                    else{
                        $sqlWhere .= 'AND (o.eventdate = "'.Sanitizer::cleanInStr($eDate1).'") ';
                    }
                    $this->localSearchArr[] = $this->searchTermsArr['eventdate1'].(isset($this->searchTermsArr['eventdate2'])?' to '.$this->searchTermsArr['eventdate2']:'');
                }
            }
        }
        if(array_key_exists('occurrenceRemarks',$this->searchTermsArr) && $this->searchTermsArr['occurrenceRemarks']){
            $searchStr = str_replace('%apos;',"'",$this->searchTermsArr['occurrenceRemarks']);
            $remarksArr = explode(';',$searchStr);
            if($remarksArr){
                $tempArr = array();
                foreach($remarksArr as $k => $value){
                    $value = trim($value);
                    if($value === 'NULL'){
                        $tempArr[] = '(o.occurrenceRemarks IS NULL)';
                        $remarksArr[$k] = 'Occurrence Remarks IS NULL';
                    }
                    else{
                        $tempArr[] = '(o.occurrenceRemarks LIKE "%'.Sanitizer::cleanInStr($value).'%")';
                    }
                }
                $sqlWhere .= 'AND ('.implode(' OR ',$tempArr).') ';
                $this->localSearchArr[] = implode(' OR ',$remarksArr);
            }
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
                        $betweenFrag[] = '(o.catalogNumber BETWEEN '.Sanitizer::cleanInStr($term1).' AND '.Sanitizer::cleanInStr($term2).')';
                        if($includeOtherCatNum){
                            $betweenFrag[] = '(o.othercatalognumbers BETWEEN '.Sanitizer::cleanInStr($term1).' AND '.Sanitizer::cleanInStr($term2).')';
                        }
                    }
                    else{
                        $catTerm = 'o.catalogNumber BETWEEN "'.Sanitizer::cleanInStr($term1).'" AND "'.Sanitizer::cleanInStr($term2).'"';
                        if(strlen($term1) === strlen($term2)) {
                            $catTerm .= ' AND length(o.catalogNumber) = ' . Sanitizer::cleanInStr(strlen($term2));
                        }
                        $betweenFrag[] = '('.$catTerm.')';
                        if($includeOtherCatNum){
                            $betweenFrag[] = '(o.othercatalognumbers BETWEEN "'.Sanitizer::cleanInStr($term1).'" AND "'.Sanitizer::cleanInStr($term2).'")';
                        }
                    }
                }
                else{
                    $vStr = trim($v);
                    $inFrag[] = Sanitizer::cleanInStr($vStr);
                    if(is_numeric($vStr) && strncmp($vStr, '0', 1) === 0){
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
        if(array_key_exists('hasaudio',$this->searchTermsArr) && $this->searchTermsArr['hasaudio']){
            $sqlWhere .= 'AND (o.occid IN(SELECT occid FROM media WHERE format LIKE "audio/%")) ';
            $this->localSearchArr[] = 'has audio';
        }
        if(array_key_exists('hasimages',$this->searchTermsArr) && $this->searchTermsArr['hasimages']){
            $sqlWhere .= 'AND (o.occid IN(SELECT occid FROM images)) ';
            $this->localSearchArr[] = 'has images';
        }
        if(array_key_exists('hasvideo',$this->searchTermsArr) && $this->searchTermsArr['hasvideo']){
            $sqlWhere .= 'AND (o.occid IN(SELECT occid FROM media WHERE format LIKE "video/%")) ';
            $this->localSearchArr[] = 'has video';
        }
        if(array_key_exists('hasmedia',$this->searchTermsArr) && $this->searchTermsArr['hasmedia']){
            $sqlWhere .= 'AND (o.occid IN(SELECT occid FROM images) OR o.occid IN(SELECT occid FROM media)) ';
            $this->localSearchArr[] = 'has images, audio, or video';
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
        if(array_key_exists('phuid',$this->searchTermsArr) && $this->searchTermsArr['phuid']){
            $sqlWhere .= 'AND (i.photographeruid IN(' .Sanitizer::cleanInStr($this->searchTermsArr['phuid']). ')) ';
        }
        if(array_key_exists('imagetag',$this->searchTermsArr) && $this->searchTermsArr['imagetag']){
            $sqlWhere .= 'AND (it.keyvalue = "'.Sanitizer::cleanInStr($this->searchTermsArr['imagetag']).'") ';
        }
        if(array_key_exists('imagekeyword',$this->searchTermsArr) && $this->searchTermsArr['imagekeyword']){
            $keywordArr = explode(';',$this->searchTermsArr['imagekeyword']);
            $tempArr = array();
            foreach($keywordArr as $value){
                $tempArr[] = "(ik.keyword LIKE '%".trim(Sanitizer::cleanInStr($value))."%')";
            }
            $sqlWhere .= 'AND (' .implode(' OR ',$tempArr). ') ';
        }
        if(array_key_exists('uploaddate1',$this->searchTermsArr) && $this->searchTermsArr['uploaddate1']){
            $dateArr = array();
            if(strpos($this->searchTermsArr['uploaddate1'],' to ')){
                $dateArr = explode(' to ',$this->searchTermsArr['uploaddate1']);
            }
            elseif(strpos($this->searchTermsArr['uploaddate1'],' - ')){
                $dateArr = explode(' - ',$this->searchTermsArr['uploaddate1']);
            }
            else{
                $dateArr[] = $this->searchTermsArr['uploaddate1'];
                if(isset($this->searchTermsArr['uploaddate2'])){
                    $dateArr[] = $this->searchTermsArr['uploaddate2'];
                }
            }
            if($dateArr && $eDate1 = $this->formatDate($dateArr[0])){
                $eDate2 = (count($dateArr)>1?$this->formatDate($dateArr[1]):'');
                if($eDate2){
                    $sqlWhere .= 'AND (i.InitialTimeStamp BETWEEN "'.Sanitizer::cleanInStr($eDate1).'" AND "'.Sanitizer::cleanInStr($eDate2).'") ';
                }
                else if(substr($eDate1,-5) === '00-00'){
                    $sqlWhere .= 'AND (i.InitialTimeStamp LIKE "'.Sanitizer::cleanInStr(substr($eDate1,0,5)).'%") ';
                }
                elseif(substr($eDate1,-2) === '00'){
                    $sqlWhere .= 'AND (i.InitialTimeStamp LIKE "'.Sanitizer::cleanInStr(substr($eDate1,0,8)).'%") ';
                }
                else{
                    $sqlWhere .= 'AND (i.InitialTimeStamp LIKE "'.Sanitizer::cleanInStr($eDate1).'%") ';
                }
            }
        }
        if(array_key_exists('imagetype',$this->searchTermsArr) && $this->searchTermsArr['imagetype']){
            if($this->searchTermsArr['imagetype'] === 'specimenonly'){
                $sqlWhere .= 'AND (i.occid IS NOT NULL) AND (c.colltype = "Preserved Specimens") ';
            }
            elseif($this->searchTermsArr['imagetype'] === 'observationonly'){
                $sqlWhere .= 'AND (i.occid IS NOT NULL) AND (c.colltype != "Preserved Specimens") ';
            }
            elseif($this->searchTermsArr['imagetype'] === 'fieldonly'){
                $sqlWhere .= 'AND (ISNULL(i.occid)) ';
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
            $sqlJoin .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
        }
        if(array_key_exists('assochost',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN omoccurassociations AS oas ON o.occid = oas.occid ';
        }
        if(array_key_exists('polyArr',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ';
        }
        if(strpos($sqlWhere,'MATCH(f.recordedby)') || strpos($sqlWhere,'MATCH(f.locality)')){
            $sqlJoin .= 'LEFT JOIN omoccurrencesfulltext AS f ON o.occid = f.occid ';
        }
        if(array_key_exists('phuid',$this->searchTermsArr) || array_key_exists('imagetag',$this->searchTermsArr) || array_key_exists('imagekeyword',$this->searchTermsArr) || array_key_exists('uploaddate1',$this->searchTermsArr) || array_key_exists('imagetype',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN images AS i ON o.occid = i.occid ';
            $sqlJoin .= array_key_exists('phuid',$this->searchTermsArr) ? 'LEFT JOIN users AS u ON i.photographeruid = u.uid ' :'';
            $sqlJoin .= array_key_exists('imagetag',$this->searchTermsArr) ? 'LEFT JOIN imagetag AS it ON i.imgid = it.imgid ' :'';
            $sqlJoin .= array_key_exists('imagekeyword',$this->searchTermsArr) ? 'LEFT JOIN imagekeywords AS ik ON i.imgid = ik.imgid ' :'';
        }
        return $sqlJoin;
    }

    protected function setSciNamesByVerns(): void
    {
        $sql = 'SELECT DISTINCT v.VernacularName, t.tid, t.sciname, ts.family, t.rankid ' .
            'FROM taxstatus AS ts LEFT JOIN taxavernaculars AS v ON ts.TID = v.TID ' .
            'LEFT JOIN taxa AS t ON ts.tidaccepted = t.TID ';
        $whereStr = '';
        foreach($this->taxaArr as $key => $value){
            $whereStr .= "OR v.VernacularName = '".Sanitizer::cleanInStr($key)."' ";
        }
        $sql .= 'WHERE (' .substr($whereStr,3). ') ORDER BY t.rankid LIMIT 20';
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

    protected function setSearchTids(): void
    {
        foreach($this->taxaArr as $key => $valueArray){
            if($this->taxaSearchType === 4){
                $sql = 'SELECT DISTINCT te.tid '.
                    'FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.TID = ts.tid '.
                    'LEFT JOIN taxaenumtree AS te ON ts.tidaccepted = te.parenttid '.
                    "WHERE (t.sciname = '".Sanitizer::cleanInStr($key)."')";
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    $this->searchTidArr[] = $r->family;
                }
            }
            elseif($this->taxaSearchType === 5 && array_key_exists('scinames',$valueArray)){
                foreach($valueArray['scinames'] as $sciName){
                    $sql = 'SELECT DISTINCT TID FROM taxa '.
                        "WHERE SciName LIKE '".Sanitizer::cleanInStr($sciName)."%'";
                    $rs = $this->conn->query($sql);
                    while($r = $rs->fetch_object()){
                        $this->searchTidArr[] = $r->TID;
                    }
                }
            }
            elseif($this->taxaSearchType === 3 || ($this->taxaSearchType === 1 && strtolower(substr($key,-5)) !== 'aceae' && strtolower(substr($key,-4)) !== 'idae')){
                $sql = 'SELECT DISTINCT TID FROM taxa '.
                    "WHERE SciName LIKE '".Sanitizer::cleanInStr($key)."%'";
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    $this->searchTidArr[] = $r->TID;
                }
            }
        }
    }

    protected function setSynonyms(): void
    {
        foreach($this->taxaArr as $key => $value){
            if(array_key_exists('scinames',$value)){
                if(!in_array('no records', $value['scinames'], true)){
                    $this->getSynonyms($value['scinames']);
                }
            }
            else{
                $this->getSynonyms($key);
            }
        }
        if($this->searchSynArr) {
            $this->searchTidArr = array_keys($this->searchSynArr);
        }
    }

    public function getFullCollectionList($catId = null): array
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

    public function outputFullCollArr($occArr, $expanded): void{
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
                                    $catIcon = (strncmp($catIcon, 'images', 6) === 0 ?'../':'').$catIcon;
                                    echo '<img src="'.$catIcon.'" style="border:0px;width:30px;height:30px;" />';
                                }
                                ?>
                            </td>
                            <td style="padding:6px;width:25px;">
                                <input id="cat-<?php echo $idStr; ?>-Input" name="cat[]" value="<?php echo $catid; ?>" type="checkbox" onchange="processCollectionParamChange(this.form);" onclick="selectAllCat(this,'cat-<?php echo $idStr; ?>')" checked />
                            </td>
                            <td style="padding:9px 5px;width:10px;">
                                <a href="#" onclick="toggleCat('<?php echo $idStr; ?>');return false;">
                                    <img id="plus-<?php echo $idStr; ?>" src="../images/plus_sm.png" style="<?php echo ($GLOBALS['DEFAULTCATID'] !== $catid?'':'display:none;') ?>" /><img id="minus-<?php echo $idStr; ?>" src="../images/minus_sm.png" style="<?php echo ($GLOBALS['DEFAULTCATID'] !== $catid?'display:none;':'') ?>" />
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
                                <div id="cat-<?php echo $idStr; ?>" style="<?php echo ($GLOBALS['DEFAULTCATID'] && $GLOBALS['DEFAULTCATID'] !== $catid?'display:none;':'') ?>margin:10px;padding:10px 20px;border:inset">
                                    <table>
                                        <?php
                                        foreach($catArr as $collid => $collName2){
                                            ?>
                                            <tr>
                                                <td style="width:40px;">
                                                    <?php
                                                    if($collName2['icon']){
                                                        $cIcon = (strncmp($collName2['icon'], 'images', 6) === 0 ?'../':'').$collName2['icon'];
                                                        ?>
                                                        <a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>'><img src="<?php echo $cIcon; ?>" style="border:0;width:30px;height:30px;" /></a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td style="padding:6px;width:25px;">
                                                    <input name="db[]" value="<?php echo $collid; ?>" type="checkbox" class="cat-<?php echo $idStr; ?>" onchange="processCollectionParamChange(this.form);" onclick="processCatCheckboxes('<?php echo $idStr; ?>')" checked />
                                                </td>
                                                <td style="padding:6px">
                                                    <div class="collectiontitle">
                                                        <a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>'>
                                                            <?php
                                                            $codeStr = ' ('.$collName2['instcode'];
                                                            if($collName2['collcode']) {
                                                                $codeStr .= '-' . $collName2['collcode'];
                                                            }
                                                            $codeStr .= ')';
                                                            echo $collName2['collname'].$codeStr;
                                                            ?>
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
                                    <img id="plus-<?php echo $idStr; ?>" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/plus_sm.png" style="<?php echo ($GLOBALS['DEFAULTCATID'] === $catid?'display:none;':'') ?>" /><img id="minus-<?php echo $idStr; ?>" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/minus_sm.png" style="<?php echo ($GLOBALS['DEFAULTCATID'] === $catid?'':'display:none;') ?>" />
                                </a>
                            </td>
                            <td>
                                <input id="cat-<?php echo $idStr; ?>-Input" data-role="none" name="cat[]" value="<?php echo $catid; ?>" type="checkbox" onchange="processCollectionParamChange(this.form);" onclick="selectAllCat(this,'cat-<?php echo $idStr; ?>')" checked />
                            </td>
                            <td>
			    		<span style='text-decoration:none;color:black;font-size:14px;font-weight:bold;'>
				    		<a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?catid=<?php echo $catid; ?>' target="_blank" ><?php echo $name; ?></a>
				    	</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div id="cat-<?php echo $idStr; ?>" style="<?php echo ($GLOBALS['DEFAULTCATID']===$catid?'':'display:none;') ?>margin:10px 0;">
                                    <table style="margin-left:15px;">
                                        <?php
                                        foreach($catArr as $collid => $collName2){
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    if($collName2['icon']){
                                                        $cIcon = (strncmp($collName2['icon'], 'images', 6) === 0 ?$GLOBALS['CLIENT_ROOT'].'/':'').$collName2['icon'];
                                                        ?>
                                                        <a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' target="_blank" >
                                                            <img src="<?php echo $cIcon; ?>" style="border:0;width:30px;height:30px;" />
                                                        </a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td style="padding:6px;">
                                                    <input name="db[]" value="<?php echo $collid; ?>" data-role="none" type="checkbox" class="cat-<?php echo $idStr; ?>" onchange="processCollectionParamChange(this.form);" onclick="processCatCheckboxes('<?php echo $idStr; ?>')" checked />
                                                </td>
                                                <td style="padding:6px;">
                                                    <a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' style='text-decoration:none;color:black;font-size:14px;' target="_blank" >
                                                        <?php echo $collName2['collname']. ' (' .$collName2['instcode']. ')'; ?>
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
                            <td style="<?php echo ($cArr['icon']?'width:35px':''); ?>">
                                <?php
                                if($cArr['icon']){
                                    $cIcon = (strncmp($cArr['icon'], 'images', 6) === 0 ?'../':'').$cArr['icon'];
                                    ?>
                                    <a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>'><img src="<?php echo $cIcon; ?>" style="border:0;width:30px;height:30px;" /></a>
                                    <?php
                                }
                                ?>
                            </td>
                            <td style="padding:6px;width:25px;">
                                <input name="db[]" value="<?php echo $collid; ?>" type="checkbox" onchange="processCollectionParamChange(this.form);" onclick="processCheckAllCheckboxes();" checked />
                            </td>
                            <td style="padding:6px">
                                <div class="collectiontitle">
                                    <a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>'>
                                        <?php
                                        $codeStr = ' ('.$cArr['instcode'];
                                        if($cArr['collcode']) {
                                            $codeStr .= '-' . $cArr['collcode'];
                                        }
                                        $codeStr .= ')';
                                        echo $cArr['collname'].$codeStr;
                                        ?>
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
                                    $cIcon = (strncmp($cArr['icon'], 'images', 6) === 0 ?$GLOBALS['CLIENT_ROOT'].'/':'').$cArr['icon'];
                                    ?>
                                    <a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' target="_blank" >
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
                                <a href = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>' style='text-decoration:none;color:black;font-size:14px;' target="_blank" >
                                    <?php echo $cArr['collname']. ' (' .$cArr['instcode']. ')'; ?>
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
            $retStr = 'All Occurrence Collections';
        }
        elseif($this->searchTermsArr['db'] === 'allobs'){
            $retStr = 'All Observation Projects';
        }
        else{
            $cArr = explode(';',$this->searchTermsArr['db']);
            if($cArr){
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

    public function getSynonyms($searchTarget): void
    {
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
            $parentTidArr = array();
            $rankId = 0;
            $sql2 = 'SELECT DISTINCT t.tid, t.sciname, t.rankid '.
                'FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.TID = ts.tidaccepted '.
                'WHERE ts.tid IN('.implode(',',$targetTidArr).') OR ts.tidaccepted IN('.implode(',',$targetTidArr).') ';
            $rs2 = $this->conn->query($sql2);
            while($r2 = $rs2->fetch_object()){
                $this->searchSynArr[$r2->tid] = $r2->sciname;
                if((int)$r2->rankid === 220){
                    $parentTidArr[] = $r2->tid;
                }
            }
            $rs2->free();

            if($parentTidArr) {
                $sql4 = 'SELECT DISTINCT t.tid, t.sciname ' .
                    'FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.tid = ts.tid ' .
                    'LEFT JOIN taxaenumtree AS te ON t.tid = te.tid ' .
                    'WHERE (te.parenttid IN(' . implode('', $parentTidArr) . ')) AND (ts.tidaccepted = ts.tid)';
                $rs4 = $this->conn->query($sql4);
                while ($r4 = $rs4->fetch_object()) {
                    $this->searchSynArr[$r4->tid] = $r4->sciname;
                }
                $rs4->free();
            }
        }
    }

    public function getClName(){
        return $this->clName;
    }

    public function setTaxon($taxon): void
    {
        if($taxon){
            $this->searchTermsArr['taxontype'] = 2;
            $this->searchTermsArr['usethes'] = 1;
            $this->searchTermsArr['taxa'] = $taxon;
        }
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

    public function getSearchSynArr(): array
    {
        return $this->searchSynArr;
    }

    public function getTaxaArr(): array
    {
        return $this->taxaArr;
    }

    protected function cleanOutStr($str): string
    {
        return htmlspecialchars($str);
    }

    protected function cleanInputStr($str): string
    {
        $newStr = str_replace(array('"', "'"), array('', '%apos;'), $str);
        $newStr = strip_tags($newStr);
        return $newStr;
    }

    public function validateSearchTermsArr($stArr): bool
    {
        $valid = false;
        if(
            array_key_exists('db',$stArr) ||
            array_key_exists('clid',$stArr) ||
            array_key_exists('taxa',$stArr) ||
            array_key_exists('country',$stArr) ||
            array_key_exists('state',$stArr) ||
            array_key_exists('county',$stArr) ||
            array_key_exists('local',$stArr) ||
            array_key_exists('elevlow',$stArr) ||
            array_key_exists('elevhigh',$stArr) ||
            array_key_exists('collector',$stArr) ||
            array_key_exists('collnum',$stArr) ||
            array_key_exists('eventdate1',$stArr) ||
            array_key_exists('eventdate2',$stArr) ||
            array_key_exists('occurrenceRemarks',$stArr) ||
            array_key_exists('catnum',$stArr) ||
            array_key_exists('othercatnum',$stArr) ||
            array_key_exists('typestatus',$stArr) ||
            array_key_exists('hasaudio',$stArr) ||
            array_key_exists('hasimages',$stArr) ||
            array_key_exists('hasvideo',$stArr) ||
            array_key_exists('hasmedia',$stArr) ||
            array_key_exists('hasgenetic',$stArr) ||
            array_key_exists('upperlat',$stArr) ||
            array_key_exists('pointlat',$stArr) ||
            array_key_exists('circleArr',$stArr) ||
            array_key_exists('phuid',$stArr) ||
            array_key_exists('imagetag',$stArr) ||
            array_key_exists('imagekeyword',$stArr) ||
            array_key_exists('uploaddate1',$stArr) ||
            array_key_exists('uploaddate2',$stArr) ||
            array_key_exists('polyArr',$stArr)
        ){
            $valid = true;
        }
        return $valid;
    }
}
?>
