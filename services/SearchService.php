<?php
include_once(__DIR__ . '/../models/Checklists.php');
include_once(__DIR__ . '/../models/Collections.php');
include_once(__DIR__ . '/../models/Occurrences.php');
include_once(__DIR__ . '/../models/Taxa.php');
include_once(__DIR__ . '/../models/TaxonVernaculars.php');
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/DataUtilitiesService.php');
include_once(__DIR__ . '/SanitizerService.php');

class SearchService {

    private $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function prepareImageUploadDateWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $dateArr = array();
        if(strpos($searchTermsArr['uploaddate1'],' to ')){
            $dateArr = explode(' to ', $searchTermsArr['uploaddate1']);
        }
        elseif(strpos($searchTermsArr['uploaddate1'],' - ')){
            $dateArr = explode(' - ', $searchTermsArr['uploaddate1']);
        }
        else{
            $dateArr[] = $searchTermsArr['uploaddate1'];
            if(isset($searchTermsArr['uploaddate2'])){
                $dateArr[] = $searchTermsArr['uploaddate2'];
            }
        }
        if($dateArr && $eDate1 = DataUtilitiesService::formatDate($dateArr[0])){
            $eDate2 = count($dateArr) > 1 ? DataUtilitiesService::formatDate($dateArr[1]) : '';
            if($eDate2){
                $tempArr[] = '(i.initialtimestamp BETWEEN "' . SanitizerService::cleanInStr($this->conn, $eDate1) . '" AND "' . SanitizerService::cleanInStr($this->conn, $eDate2) . '")';
            }
            else if(substr($eDate1,-5) === '00-00'){
                $tempArr[] = '(i.initialtimestamp LIKE "' . SanitizerService::cleanInStr($this->conn, substr($eDate1,0,5)) . '%")';
            }
            elseif(substr($eDate1,-2) === '00'){
                $tempArr[] = '(i.initialtimestamp LIKE "' . SanitizerService::cleanInStr($this->conn, substr($eDate1,0,8)) . '%") ';
            }
            else{
                $tempArr[] = '(i.initialtimestamp LIKE "' . SanitizerService::cleanInStr($this->conn, $eDate1) . '%") ';
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceCatalogNumberWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $catStr = $searchTermsArr['catnum'];
        $includeOtherCatNum = array_key_exists('othercatnum', $searchTermsArr);
        $catArr = explode(',', str_replace(';',',', $catStr));
        $betweenFrag = array();
        $inFrag = array();
        foreach($catArr as $v){
            if($p = strpos($v,' - ')){
                $term1 = trim(substr($v,0, $p));
                $term2 = trim(substr($v,$p+3));
                if(is_numeric($term1) && is_numeric($term2)){
                    $betweenFrag[] = '(o.catalognumber BETWEEN ' . SanitizerService::cleanInStr($this->conn, $term1) . ' AND ' . SanitizerService::cleanInStr($this->conn, $term2) . ')';
                    if($includeOtherCatNum){
                        $betweenFrag[] = '(o.othercatalognumbers BETWEEN ' . SanitizerService::cleanInStr($this->conn, $term1) . ' AND ' . SanitizerService::cleanInStr($this->conn, $term2) . ')';
                    }
                }
                else{
                    $catTerm = 'o.catalognumber BETWEEN "' . SanitizerService::cleanInStr($this->conn, $term1) . '" AND "' . SanitizerService::cleanInStr($this->conn, $term2) . '"';
                    if(strlen($term1) === strlen($term2)) {
                        $catTerm .= ' AND length(o.catalognumber) = ' . SanitizerService::cleanInStr($this->conn, strlen($term2));
                    }
                    $betweenFrag[] = '('.$catTerm.')';
                    if($includeOtherCatNum){
                        $betweenFrag[] = '(o.othercatalognumbers BETWEEN "' . SanitizerService::cleanInStr($this->conn, $term1) . '" AND "' . SanitizerService::cleanInStr($this->conn, $term2) . '")';
                    }
                }
            }
            else{
                $vStr = trim($v);
                $inFrag[] = SanitizerService::cleanInStr($this->conn, $vStr);
                if(is_numeric($vStr) && strncmp($vStr, '0', 1) === 0){
                    $inFrag[] = ltrim($vStr,0);
                }
            }
        }
        if($betweenFrag){
            $tempArr[] = '(' . implode(' OR ', $betweenFrag) . ')';
        }
        if($inFrag){
            $tempArr[] = '(o.catalognumber IN("' . implode('","', $inFrag) . '"))';
            if($includeOtherCatNum){
                $tempArr[] = '(o.othercatalognumbers IN("' . implode('","', $inFrag) . '"))';
                if(strlen($inFrag[0]) === 36){
                    $guidOccid = (new Occurrences)->getOccidByGUIDArr($inFrag);
                    if($guidOccid){
                        $tempArr[] = '(o.occid IN(' . implode(',', $guidOccid) . '))';
                        $tempArr[] = '(o.occurrenceid IN("' . implode('","', $inFrag) . '"))';
                    }
                }
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceChecklistVoucherWhereSql($voucherSearchTermsArr): string
    {
        $tempArr = array();
        $llStrArr = array();
        if(isset($voucherSearchTermsArr['country']) && $voucherSearchTermsArr['country']){
            $countryStr = str_replace(';',',', SanitizerService::cleanInStr($this->conn, $voucherSearchTermsArr['country']));
            $tempArr[] = '(o.country IN("' . $countryStr . '"))';
        }
        if(isset($voucherSearchTermsArr['state']) && $voucherSearchTermsArr['state']){
            $stateStr = str_replace(';',',', SanitizerService::cleanInStr($this->conn, $voucherSearchTermsArr['state']));
            $tempArr[] = '(o.stateprovince = "' . $stateStr . '")';
        }
        if(isset($voucherSearchTermsArr['county']) && $voucherSearchTermsArr['county']){
            $countyStr = str_replace(';',',', $voucherSearchTermsArr['county']);
            $cArr = explode(',', $countyStr);
            $cStArr = array();
            foreach($cArr as $str){
                $cStArr[] = '(o.county LIKE "' . SanitizerService::cleanInStr($this->conn, $str) . '%")';
            }
            $tempArr[] = '(' . implode(' OR ', $cStArr) . ')';
        }
        if(isset($voucherSearchTermsArr['locality']) && $voucherSearchTermsArr['locality']){
            $localityStr = str_replace(';',',', $voucherSearchTermsArr['locality']);
            $locArr = explode(',', $localityStr);
            $locStArr = array();
            foreach($locArr as $str){
                $str = SanitizerService::cleanInStr($this->conn, $str);
                $locStArr[] = '(o.locality LIKE "%' . $str . '%")';
            }
            $tempArr[] = '(' . implode(' OR ', $locStArr) . ')';
        }
        if(isset($voucherSearchTermsArr['taxon']) && $voucherSearchTermsArr['taxon']){
            $tStr = SanitizerService::cleanInStr($this->conn, $voucherSearchTermsArr['taxon']);
            $tidPar = (new Taxa)->getTid($tStr);
            if($tidPar){
                $tempArr[] = '(o.tid IN (SELECT tid FROM taxaenumtree WHERE parenttid = ' . $tidPar . '))';
            }
        }
        if(isset($voucherSearchTermsArr['latnorth'], $voucherSearchTermsArr['latsouth']) && is_numeric($voucherSearchTermsArr['latnorth']) && is_numeric($voucherSearchTermsArr['latsouth'])){
            $llStrArr[] = '(o.decimallatitude BETWEEN ' . $voucherSearchTermsArr['latsouth'] . ' AND ' . $voucherSearchTermsArr['latnorth'] . ')';
        }
        if(isset($voucherSearchTermsArr['lngwest'], $voucherSearchTermsArr['lngeast']) && is_numeric($voucherSearchTermsArr['lngwest']) && is_numeric($voucherSearchTermsArr['lngeast'])){
            $llStrArr[] = '(o.decimallongitude BETWEEN ' . $voucherSearchTermsArr['lngwest'] . ' AND ' . $voucherSearchTermsArr['lngeast'] . ')';
        }
        if(count($llStrArr) > 0){
            $llStr = '(' . implode(' AND ', $llStrArr) . ')';
            if(array_key_exists('latlngor', $voucherSearchTermsArr)) {
                $llStr = 'OR (' . $llStr . ') ';
            }
            $tempArr[] = $llStr;
        }
        elseif(isset($voucherSearchTermsArr['onlycoord']) && $voucherSearchTermsArr['onlycoord']){
            $tempArr[] = '(o.decimallatitude IS NOT NULL)';
        }
        if(isset($voucherSearchTermsArr['excludecult']) && $voucherSearchTermsArr['excludecult']){
            $tempArr[] = '(o.cultivationstatus = 0 OR ISNULL(o.cultivationstatus))';
        }
        if(isset($voucherSearchTermsArr['collid']) && is_numeric($voucherSearchTermsArr['collid'])){
            $tempArr[] = '(o.collid = ' . (int)$voucherSearchTermsArr['collid'] . ')';
        }
        if(isset($voucherSearchTermsArr['recordedby']) && $voucherSearchTermsArr['recordedby']){
            $collStr = str_replace(',', ';', $voucherSearchTermsArr['recordedby']);
            $collArr = explode(';', $collStr);
            $collectorStrArr = array();
            foreach($collArr as $str => $postArr){
                $collectorStrArr[] = '(o.recordedby LIKE "%' . SanitizerService::cleanInStr($this->conn, $voucherSearchTermsArr['recordedby']) . '%")';
            }
            $tempArr[] = '(' . implode(' OR ', $collectorStrArr) . ')';
        }
        return count($tempArr) > 0 ? '(' . implode(' AND ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceCollectionWhereSql($searchTermsArr): string
    {
        $collSqlWhereStr = '';
        if(array_key_exists('db', $searchTermsArr) && $searchTermsArr['db'] && $searchTermsArr['db'] !== 'all') {
            if(!$GLOBALS['IS_ADMIN']){
                $searchCollections = array();
                $publicCollections = (new Collections)->getPublicCollections();
                $selectedCollections = explode(',', SanitizerService::cleanInStr($this->conn, $searchTermsArr['db']));
                foreach($selectedCollections as $id){
                    if(in_array((int)$id, $publicCollections, true) || in_array((int)$id, $GLOBALS['PERMITTED_COLLECTIONS'], true)){
                        $searchCollections[] = (int)$id;
                    }
                }
                $collIdStr = implode(',', $searchCollections);
            }
            else{
                $collIdStr = SanitizerService::cleanInStr($this->conn, $searchTermsArr['db']);
            }
            $collSqlWhereStr .= '(o.collid IN(' . $collIdStr . '))';
        }
        elseif(!$GLOBALS['IS_ADMIN']){
            $collSqlWhereStr .= '(ISNULL(c.collid) OR c.isPublic = 1';
            if($GLOBALS['PERMITTED_COLLECTIONS']){
                $collSqlWhereStr .= ' OR o.collid IN(' . implode(',', $GLOBALS['PERMITTED_COLLECTIONS']) . ')';
            }
            $collSqlWhereStr .= ')';
        }
        return $collSqlWhereStr;
    }

    public function prepareOccurrenceCollectionNumberWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $collNumArr = explode(';', $searchTermsArr['collnum']);
        foreach($collNumArr as $v){
            $v = trim($v);
            if($p = strpos($v,' - ')){
                $term1 = trim(substr($v,0, $p));
                $term2 = trim(substr($v,$p+3));
                if(is_numeric($term1) && is_numeric($term2)){
                    $tempArr[] = '(o.recordnumber BETWEEN ' . $term1 . ' AND ' . $term2 . ')';
                }
                else{
                    if(strlen($term2) > strlen($term1)) {
                        $term1 = str_pad($term1, strlen($term2), '0', STR_PAD_LEFT);
                    }
                    $catTerm = '(o.recordnumber BETWEEN "' . SanitizerService::cleanInStr($this->conn, $term1) . '" AND "' . SanitizerService::cleanInStr($this->conn, $term2) . '")';
                    $catTerm .= ' AND (length(o.recordnumber) <= ' . strlen($term2) . ')';
                    $tempArr[] = '(' . $catTerm . ')';
                }
            }
            else{
                $tempArr[] = '(o.recordnumber = "' . SanitizerService::cleanInStr($this->conn, $v) . '")';
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceCollectorWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $searchStr = str_replace('%apos;',"'", $searchTermsArr['collector']);
        $collectorArr = explode(';', $searchStr);
        if($collectorArr && count($collectorArr) === 1){
            if($collectorArr[0] === 'NULL'){
                $tempArr[] = '(ISNULL(o.recordedby))';
            }
            else{
                $tempInnerArr = array();
                $collValueArr = explode(' ', trim($collectorArr[0]));
                foreach($collValueArr as $collV){
                    if(strlen($collV) < 4 || strtolower($collV) === 'best'){
                        $tempInnerArr[] = '(o.recordedBy LIKE "%' . SanitizerService::cleanInStr($this->conn, $collV) . '%")';
                    }
                    else{
                        $tempInnerArr[] = '(MATCH(f.recordedby) AGAINST("' . SanitizerService::cleanInStr($this->conn, $collV) . '"))';
                    }
                }
                $tempArr[] = implode(' AND ', $tempInnerArr);
            }
        }
        elseif(count($collectorArr) > 1){
            $collStr = current($collectorArr);
            if(strlen($collStr) < 4 || strtolower($collStr) === 'best'){
                $tempArr[] = '(o.recordedby LIKE "%' . SanitizerService::cleanInStr($this->conn, $collStr) . '%")';
            }
            else{
                $tempArr[] = '(MATCH(f.recordedby) AGAINST("' . SanitizerService::cleanInStr($this->conn, $collStr) . '"))';
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceCountryWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $searchStr = str_replace('%apos;', "'", $searchTermsArr['country']);
        $countryArr = explode(';', $searchStr);
        if($countryArr){
            foreach($countryArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ISNULL(o.country))';
                }
                else{
                    $tempArr[] = '(o.country = "' . SanitizerService::cleanInStr($this->conn, $value) . '")';
                }
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceCountyWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $searchStr = str_replace('%apos;',"'", $searchTermsArr['county']);
        $countyArr = explode(';', $searchStr);
        if($countyArr){
            foreach($countyArr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ISNULL(o.county))';
                }
                else{
                    $value = trim(str_ireplace(' county','', $value));
                    $tempArr[] = '(o.county LIKE "' . SanitizerService::cleanInStr($this->conn, $value) . '%")';
                }
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceElevationWhereSql($searchTermsArr): string
    {
        $elevlow = 0;
        $elevhigh = 30000;
        if(array_key_exists('elevlow',$searchTermsArr)){
            $elevlow = (int)$searchTermsArr['elevlow'];
        }
        if(array_key_exists('elevhigh',$searchTermsArr)){
            $elevhigh = (int)$searchTermsArr['elevhigh'];
        }
        return '(' . '(minimumelevationinmeters >= ' . $elevlow . ' AND maximumelevationinmeters <= ' . $elevhigh . ') OR ' .
            '(ISNULL(maximumelevationinmeters) AND minimumelevationinmeters >= ' . $elevlow . ' AND minimumelevationinmeters <= ' . $elevhigh . ')' . ')';
    }

    public function prepareOccurrenceEventDateWhereSql($searchTermsArr): string
    {
        $returnStr = '';
        $dateArr = array();
        if(strpos($searchTermsArr['eventdate1'],' to ')){
            $dateArr = explode(' to ', $searchTermsArr['eventdate1']);
        }
        elseif(strpos($searchTermsArr['eventdate1'],' - ')){
            $dateArr = explode(' - ', $searchTermsArr['eventdate1']);
        }
        else{
            $dateArr[] = $searchTermsArr['eventdate1'];
            if(isset($searchTermsArr['eventdate2'])){
                $dateArr[] = $searchTermsArr['eventdate2'];
            }
        }
        if($dateArr){
            if($dateArr[0] === 'NULL'){
                $returnStr = '(ISNULL(o.eventdate))';
            }
            elseif($eDate1 = DataUtilitiesService::formatDate($dateArr[0])){
                $eDate2 = count($dateArr) > 1 ? DataUtilitiesService::formatDate($dateArr[1]) : '';
                if($eDate2){
                    $returnStr = '(o.eventdate BETWEEN "' . SanitizerService::cleanInStr($this->conn, $eDate1) . '" AND "' . SanitizerService::cleanInStr($this->conn, $eDate2) . '")';
                }
                else if(substr($eDate1,-5) === '00-00'){
                    $returnStr = '(o.eventdate LIKE "' . SanitizerService::cleanInStr($this->conn, substr($eDate1,0,5)) . '%")';
                }
                elseif(substr($eDate1,-2) === '00'){
                    $returnStr = '(o.eventdate LIKE "' . SanitizerService::cleanInStr($this->conn, substr($eDate1,0,8)) . '%")';
                }
                else{
                    $returnStr = '(o.eventdate = "' . SanitizerService::cleanInStr($this->conn, $eDate1) . '")';
                }
            }
        }
        return $returnStr;
    }

    public function prepareOccurrenceLocalityWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $searchStr = str_replace('%apos;',"'", $searchTermsArr['local']);
        $localArr = explode(';', $searchStr);
        if($localArr){
            foreach($localArr as $k => $value){
                $value = trim($value);
                if($value === 'NULL'){
                    $tempArr[] = '(ISNULL(o.locality))';
                }
                else{
                    $tempArr[] = '(o.municipality LIKE "' . SanitizerService::cleanInStr($this->conn, $value) . '%" OR o.locality LIKE "%' . SanitizerService::cleanInStr($this->conn, $value) . '%")';
                }
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceOccurrenceRemarksWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $searchStr = str_replace('%apos;',"'", $searchTermsArr['occurrenceRemarks']);
        $remarksArr = explode(';', $searchStr);
        if($remarksArr){
            foreach($remarksArr as $k => $value){
                $value = trim($value);
                if($value === 'NULL'){
                    $tempArr[] = '(ISNULL(o.occurrenceremarks))';
                }
                else{
                    $tempArr[] = '(o.occurrenceremarks LIKE "%' . SanitizerService::cleanInStr($this->conn, $value) . '%")';
                }
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceSpatialWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        if(array_key_exists('upperlat', $searchTermsArr) && $searchTermsArr['upperlat']){
            $tempArr[] = '(o.decimallatitude BETWEEN ' . SanitizerService::cleanInStr($this->conn, $searchTermsArr['bottomlat']) . ' AND ' . SanitizerService::cleanInStr($this->conn, $searchTermsArr['upperlat']) . ' AND ' .
                'o.decimallongitude BETWEEN ' . SanitizerService::cleanInStr($this->conn, $searchTermsArr['leftlong']) . ' AND ' . SanitizerService::cleanInStr($this->conn, $searchTermsArr['rightlong']) . ')';
        }
        if(array_key_exists('pointlat', $searchTermsArr) && $searchTermsArr['pointlat']){
            $radius = $searchTermsArr['groundradius'] * 0.621371192;
            $tempArr[] = '((3959 * ACOS(COS(RADIANS(o.decimallatitude)) * COS(RADIANS(' . $searchTermsArr['pointlat'] . ')) * COS(RADIANS(' . $searchTermsArr['pointlong'] . ') - RADIANS(o.decimallongitude)) + SIN(RADIANS(o.decimallatitude)) * SIN(RADIANS(' . $searchTermsArr['pointlat'] . ')))) <= ' . $radius . ')';
        }
        if(array_key_exists('circleArr', $searchTermsArr) && $searchTermsArr['circleArr']){
            $sqlFragArr = array();
            $objArr = $searchTermsArr['circleArr'];
            if(!is_array($objArr)){
                $objArr = json_decode($objArr, true);
            }
            if($objArr){
                foreach($objArr as $obj => $oArr){
                    $radius = $oArr['groundradius'] * 0.621371192;
                    $sqlFragArr[] = '((3959 * ACOS(COS(RADIANS(o.decimallatitude)) * COS(RADIANS(' . $oArr['pointlat'] . ')) * COS(RADIANS(' . $oArr['pointlong'] . ') - RADIANS(o.decimallongitude)) + SIN(RADIANS(o.decimallatitude)) * SIN(RADIANS(' . $oArr['pointlat'] . ')))) <= ' . $radius . ')';
                }
                $tempArr[] = '('.implode(' OR ', $sqlFragArr).')';
            }
        }
        if(array_key_exists('polyArr',$searchTermsArr) && $searchTermsArr['polyArr']){
            $sqlFragArr = array();
            $geomArr = $searchTermsArr['polyArr'];
            if(!is_array($geomArr)){
                $geomArr = json_decode($geomArr, true);
            }
            if($geomArr){
                foreach($geomArr as $geom){
                    $sqlFragArr[] = "(ST_Within(p.point, ST_GeomFromText('" . $geom . " ')))";
                }
                $tempArr[] = '(' . implode(' OR ', $sqlFragArr) . ')';
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceStateProvinceWhereSql($searchTermsArr): string
    {
        $tempArr = array();
        $searchStr = str_replace('%apos;',"'", $searchTermsArr['state']);
        $stateAr = explode(';', $searchStr);
        if($stateAr){
            foreach($stateAr as $k => $value){
                if($value === 'NULL'){
                    $tempArr[] = '(ISNULL(o.stateprovince))';
                }
                else{
                    $tempArr[] = '(o.stateprovince = "' . SanitizerService::cleanInStr($this->conn, $value) . '")';
                }
            }
        }
        return count($tempArr) > 0 ? '(' . implode(' OR ', $tempArr) . ')' : '';
    }

    public function prepareOccurrenceTaxaWhereSql($searchTermsArr, $image): string
    {
        $sqlTaxaWherePartsArr = array();
        $taxaDataArr = array();
        $vernacularArr = array();
        $searchTidArr = array();
        $useThes = array_key_exists('usethes', $searchTermsArr) ? $searchTermsArr['usethes'] : 0;
        $taxaSearchType = (isset($searchTermsArr['taxontype']) && (int)$searchTermsArr['taxontype'] > 0) ? (int)$searchTermsArr['taxontype'] : 1;
        $taxaArr = explode(';', trim($searchTermsArr['taxa']));
        foreach($taxaArr as $sName){
            $trimmedName = trim($sName);
            if(is_numeric($trimmedName)) {
                $searchTidArr[] = $trimmedName;
            }
            else if($taxaSearchType !== 5){
                $taxaDataArr[$trimmedName] = 0;
            }
            else{
                $vernacularArr[] = $trimmedName;
            }
        }
        if($taxaSearchType === 5){
            $taxaDataArr = (new TaxonVernaculars)->setSciNameSearchDataByVernaculars($taxaDataArr, $vernacularArr);
        }
        elseif($useThes){
            $taxaDataArr = (new Taxa)->setSynonymSearchData($taxaDataArr);
        }
        else{
            $taxaDataArr = (new Taxa)->setTaxaSearchDataTids($taxaDataArr);
        }
        foreach($taxaDataArr as $name => $tid){
            if($tid){
                $searchTidArr[] = $tid;
            }
            if($taxaSearchType === 4 || $taxaSearchType === 5){
                if($image){
                    $sqlTaxaWherePartsArr[] = '(te.parenttid = ' . (int)$tid . ' OR te.tid = ' . (int)$tid . ')';
                }
                else{
                    $sqlTaxaWherePartsArr[] = '(te.parenttid = ' . (int)$tid . ' OR te.tid = ' . (int)$tid . ') OR (ISNULL(o.tid) AND o.sciname = "' . SanitizerService::cleanInStr($this->conn, $name) . '")';
                }
            }
            elseif($taxaSearchType === 2 || ($taxaSearchType === 1 && (strtolower(substr($name,-5)) === 'aceae' || strtolower(substr($name,-4)) === 'idae'))){
                if($image){
                    $sqlTaxaWherePartsArr[] = '(t.family = "' . SanitizerService::cleanInStr($this->conn, $name) . '")';
                }
                else{
                    $sqlTaxaWherePartsArr[] = '(t.family = "' . SanitizerService::cleanInStr($this->conn, $name) . '") OR (ISNULL(o.tid) AND (o.family = "' . SanitizerService::cleanInStr($this->conn, $name) . '" OR o.sciname = "' . SanitizerService::cleanInStr($this->conn, $name) . '"))';
                }
            }
            elseif(!$image){
                $sqlTaxaWherePartsArr[] = '(o.sciname LIKE "' . SanitizerService::cleanInStr($this->conn, $name) . '%")';
            }
        }
        if($searchTidArr){
            if($image){
                $sqlTaxaWherePartsArr[] = '(i.tid IN(' . implode(',', $searchTidArr) . '))';
            }
            else{
                $sqlTaxaWherePartsArr[] = '(o.tid IN(' . implode(',', $searchTidArr) . '))';
            }
        }
        return count($sqlTaxaWherePartsArr) > 0 ? ('(' . implode(' OR ', $sqlTaxaWherePartsArr) . ')') : '';
    }

    public function prepareOccurrenceWhereSql($searchTermsArr, $image = null): string
    {
        $sqlWherePartsArr = array();
        if(array_key_exists('clid', $searchTermsArr) && $searchTermsArr['clid']){
            $sqlWherePartsArr[] = '(v.clid IN(' . $searchTermsArr['clid'] . '))';
        }
        if(array_key_exists('dsid', $searchTermsArr) && $searchTermsArr['dsid']){
            $sqlWherePartsArr[] = '(o.occid IN(SELECT occid FROM omoccurdatasetlink WHERE datasetid = ' . (int)$searchTermsArr['dsid'] . '))';
        }
        $collStr = $this->prepareOccurrenceCollectionWhereSql($searchTermsArr);
        if($collStr){
            $sqlWherePartsArr[] = $collStr;
        }
        if(array_key_exists('taxa', $searchTermsArr) && $searchTermsArr['taxa']){
            $taxaStr = $this->prepareOccurrenceTaxaWhereSql($searchTermsArr, $image);
            if($taxaStr){
                $sqlWherePartsArr[] = $taxaStr;
            }
        }
        if(array_key_exists('country',$searchTermsArr) && $searchTermsArr['country']){
            $countryStr = $this->prepareOccurrenceCountryWhereSql($searchTermsArr);
            if($countryStr){
                $sqlWherePartsArr[] = $countryStr;
            }
        }
        if(array_key_exists('state',$searchTermsArr) && $searchTermsArr['state']){
            $stateStr = $this->prepareOccurrenceStateProvinceWhereSql($searchTermsArr);
            if($stateStr){
                $sqlWherePartsArr[] = $stateStr;
            }
        }
        if(array_key_exists('county',$searchTermsArr) && $searchTermsArr['county']){
            $countyStr = $this->prepareOccurrenceCountyWhereSql($searchTermsArr);
            if($countyStr){
                $sqlWherePartsArr[] = $countyStr;
            }
        }
        if(array_key_exists('local',$searchTermsArr) && $searchTermsArr['local']){
            $localityStr = $this->prepareOccurrenceLocalityWhereSql($searchTermsArr);
            if($localityStr){
                $sqlWherePartsArr[] = $localityStr;
            }
        }
        if((array_key_exists('elevlow',$searchTermsArr) && is_numeric($searchTermsArr['elevlow'])) || (array_key_exists('elevhigh',$searchTermsArr) && is_numeric($searchTermsArr['elevhigh']))){
            $elevationStr = $this->prepareOccurrenceElevationWhereSql($searchTermsArr);
            if($elevationStr){
                $sqlWherePartsArr[] = $elevationStr;
            }
        }
        if((array_key_exists('upperlat',$searchTermsArr) && $searchTermsArr['upperlat']) || (array_key_exists('pointlat',$searchTermsArr) && $searchTermsArr['pointlat']) || (array_key_exists('circleArr',$searchTermsArr) && $searchTermsArr['circleArr']) || (array_key_exists('polyArr',$searchTermsArr) && $searchTermsArr['polyArr'])){
            $spatialStr = $this->prepareOccurrenceSpatialWhereSql($searchTermsArr);
            if($spatialStr){
                $sqlWherePartsArr[] = $spatialStr;
            }
        }
        if(array_key_exists('collector',$searchTermsArr) && $searchTermsArr['collector']){
            $collectorStr = $this->prepareOccurrenceCollectorWhereSql($searchTermsArr);
            if($collectorStr){
                $sqlWherePartsArr[] = $collectorStr;
            }
        }
        if(array_key_exists('collnum',$searchTermsArr) && $searchTermsArr['collnum']){
            $collNumStr = $this->prepareOccurrenceCollectionNumberWhereSql($searchTermsArr);
            if($collNumStr){
                $sqlWherePartsArr[] = $collNumStr;
            }
        }
        if(array_key_exists('eventdate1',$searchTermsArr) && $searchTermsArr['eventdate1']){
            $eventDateStr = $this->prepareOccurrenceEventDateWhereSql($searchTermsArr);
            if($eventDateStr){
                $sqlWherePartsArr[] = $eventDateStr;
            }
        }
        if(array_key_exists('occurrenceRemarks',$searchTermsArr) && $searchTermsArr['occurrenceRemarks']){
            $occurrenceRemarksStr = $this->prepareOccurrenceOccurrenceRemarksWhereSql($searchTermsArr);
            if($occurrenceRemarksStr){
                $sqlWherePartsArr[] = $occurrenceRemarksStr;
            }
        }
        if(array_key_exists('catnum',$searchTermsArr) && $searchTermsArr['catnum']){
            $catNumStr = $this->prepareOccurrenceCatalogNumberWhereSql($searchTermsArr);
            if($catNumStr){
                $sqlWherePartsArr[] = $catNumStr;
            }
        }
        if(array_key_exists('typestatus',$searchTermsArr) && $searchTermsArr['typestatus']){
            $sqlWherePartsArr[] = '(o.typestatus IS NOT NULL)';
        }
        if(array_key_exists('hasaudio',$searchTermsArr) && $searchTermsArr['hasaudio']){
            $sqlWherePartsArr[] = '(o.occid IN(SELECT occid FROM media WHERE format LIKE "audio/%"))';
        }
        if(array_key_exists('hasimages',$searchTermsArr) && $searchTermsArr['hasimages']){
            $sqlWherePartsArr[] = '(o.occid IN(SELECT occid FROM images))';
        }
        if(array_key_exists('hasvideo',$searchTermsArr) && $searchTermsArr['hasvideo']){
            $sqlWherePartsArr[] = '(o.occid IN(SELECT occid FROM media WHERE format LIKE "video/%"))';
        }
        if(array_key_exists('hasmedia',$searchTermsArr) && $searchTermsArr['hasmedia']){
            $sqlWherePartsArr[] = '(o.occid IN(SELECT occid FROM images) OR o.occid IN(SELECT occid FROM media))';
        }
        if(array_key_exists('hasgenetic',$searchTermsArr) && $searchTermsArr['hasgenetic']){
            $sqlWherePartsArr[] = '(o.occid IN(SELECT occid FROM omoccurgenetic))';
        }
        if(array_key_exists('targetclid',$searchTermsArr) && $searchTermsArr['targetclid'] && is_numeric($searchTermsArr['targetclid'])){
            $checklist = (new Checklists)->getChecklistFromClid($searchTermsArr['targetclid']);
            if($checklist && $checklist['dynamicsql']){
                $checklistVoucherStr = $this->prepareOccurrenceChecklistVoucherWhereSql(json_decode($checklist['dynamicsql'], true));
                if($checklistVoucherStr){
                    $sqlWherePartsArr[] = '(' . $checklistVoucherStr . ') AND (o.occid NOT IN(SELECT occid FROM fmvouchers WHERE clid = ' . (int)$searchTermsArr['targetclid'] . '))';
                }
            }
        }
        if(array_key_exists('phuid',$searchTermsArr) && $searchTermsArr['phuid']){
            $sqlWherePartsArr[] = '(i.photographeruid IN(' . SanitizerService::cleanInStr($this->conn, $searchTermsArr['phuid']) . '))';
        }
        if(array_key_exists('imagetag',$searchTermsArr) && $searchTermsArr['imagetag']){
            $sqlWherePartsArr[] = '(it.keyvalue = "' . SanitizerService::cleanInStr($this->conn, $searchTermsArr['imagetag']) . '")';
        }
        if(array_key_exists('uploaddate1',$searchTermsArr) && $searchTermsArr['uploaddate1']){
            $uploadDateStr = $this->prepareImageUploadDateWhereSql($searchTermsArr);
            if($uploadDateStr){
                $sqlWherePartsArr[] = $uploadDateStr;
            }
        }
        if(array_key_exists('imagetype',$searchTermsArr) && $searchTermsArr['imagetype']){
            if($searchTermsArr['imagetype'] === 'specimenonly'){
                $sqlWherePartsArr[] = '(i.occid IS NOT NULL) AND (o.basisofrecord LIKE "%specimen%")';
            }
            elseif($searchTermsArr['imagetype'] === 'observationonly'){
                $sqlWherePartsArr[] = '(i.occid IS NOT NULL) AND (o.basisofrecord LIKE "%observation%")';
            }
            elseif($searchTermsArr['imagetype'] === 'fieldonly'){
                $sqlWherePartsArr[] = '(i.imgid IS NOT NULL AND (ISNULL(i.occid) OR o.basisofrecord LIKE "%observation%"))';
            }
        }
        return count($sqlWherePartsArr) > 0 ? implode(' AND ', $sqlWherePartsArr) : '';
    }
}
