<?php
include_once(__DIR__ . '/GPoint.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');

class OccurrenceUtilities {

    public static $monthRoman = array('I'=>'01','II'=>'02','III'=>'03','IV'=>'04','V'=>'05','VI'=>'06','VII'=>'07','VIII'=>'08','IX'=>'09','X'=>'10','XI'=>'11','XII'=>'12');
    public static $monthNames = array('jan'=>'01','ene'=>'01','feb'=>'02','mar'=>'03','abr'=>'04','apr'=>'04','may'=>'05','jun'=>'06','jul'=>'07','ago'=>'08',
        'aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12','dic'=>'12');

    public function __construct(){
    }

    public function __destruct(){
    }

    public static function formatDate($inStr){
        $retDate = '';
        $dateStr = trim($inStr);
        if(!$dateStr) {
            return true;
        }
        $t = '';
        $y = '';
        $m = '00';
        $d = '00';
        if(preg_match('/\d{2}:\d{2}:\d{2}/',$dateStr,$match)){
            $t = $match[0];
        }
        if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/',$dateStr,$match)){
            $y = $match[1];
            $m = $match[2];
            $d = $match[3];
        }
        elseif(preg_match('/^(\d{4})-(\d{1,2})/',$dateStr,$match)){
            $y = $match[1];
            $m = $match[2];
        }
        elseif(preg_match('/^([\d-]{1,5})\.([IVX]{1,4})\.(\d{2,4})/i',$dateStr,$match)){
            $d = $match[1];
            $mStr = strtoupper($match[2]);
            $y = $match[3];
            if(array_key_exists($mStr,self::$monthRoman)){
                $m = self::$monthRoman[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})[\s\/-](\D{3,})\.*[\s\/-](\d{2,4})/',$dateStr,$match)){
            $d = $match[1];
            $mStr = $match[2];
            $y = $match[3];
            $mStr = strtolower(substr($mStr,0,3));
            if(array_key_exists($mStr,self::$monthNames)){
                $m = self::$monthNames[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})/',$dateStr,$match)){
            $m = $match[1];
            $d = $match[2];
            $y = $match[3];
        }
        elseif(preg_match('/^(\D{3,})\.*\s?(\d{1,2})[,\s]+([1,2][0,5-9]\d{2})$/',$dateStr,$match)){
            $mStr = $match[1];
            $d = $match[2];
            $y = $match[3];
            $mStr = strtolower(substr($mStr,0,3));
            if(array_key_exists($mStr,self::$monthNames)) {
                $m = self::$monthNames[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})-(\d{1,2})-(\d{2,4})/',$dateStr,$match)){
            $m = $match[1];
            $d = $match[2];
            $y = $match[3];
        }
        elseif(preg_match('/^(\D{3,})\.*\s+([1,2][0,5-9]\d{2})/',$dateStr,$match)){
            $mStr = strtolower(substr($match[1],0,3));
            if(array_key_exists($mStr,self::$monthNames)){
                $m = self::$monthNames[$mStr];
            }
            else{
                $m = '00';
            }
            $y = $match[2];
        }
        elseif(preg_match('/([1,2][0,5-9]\d{2})/',$dateStr,$match)){
            $y = $match[1];
        }
        if($y){
            if(strlen($m) == 1) {
                $m = '0' . $m;
            }
            if(strlen($d) === 1) {
                $d = '0' . $d;
            }
            if($m > 12){
                $m = '00';
                $d = '00';
            }
            if($d > 31){
                $d = '00';
            }
            elseif($d === 30 && $m === 2){
                $d = '00';
            }
            elseif($d === 31 && ($m === 4 || $m === 6 || $m === 9 || $m === 11)){
                $d = '00';
            }
            if(strlen($y) === 2){
                if($y < 20) {
                    $y = '20' . $y;
                }
                else {
                    $y = '19' . $y;
                }
            }
            $retDate = $y.'-'.$m.'-'.$d;
        }
        elseif(($timestamp = strtotime($retDate)) !== false){
            $retDate = date('Y-m-d', $timestamp);
        }
        if($t){
            $retDate .= ' '.$t;
        }
        return $retDate;
    }

    public static function parseScientificName($inStr, $rankId = 0): array
    {
        $taxonArr = (new TaxonomyUtilities)->parseScientificName($inStr, $rankId);
        if(array_key_exists('unitind1',$taxonArr)){
            $taxonArr['unitname1'] = $taxonArr['unitind1'].' '.$taxonArr['unitname1'];
            unset($taxonArr['unitind1']);
        }
        if(array_key_exists('unitind2',$taxonArr)){
            $taxonArr['unitname2'] = $taxonArr['unitind2'].' '.$taxonArr['unitname2'];
            unset($taxonArr['unitind2']);
        }
        return $taxonArr;
    }

    public static function parseVerbatimElevation($inStr): array
    {
        $retArr = array();
        $search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
        $replace = array("'","'",'"','"','*','-','-');
        $inStr= str_replace($search, $replace, $inStr);
        if(preg_match('/([.\d]+)\s*-\s*([.\d]+)\s*meter/i',$inStr,$m)){
            $retArr['minelev'] = $m[1];
            $retArr['maxelev'] = $m[2];
        }
        elseif(preg_match('/([.\d]+)\s*-\s*([.\d]+)\s*m./i',$inStr,$m)){
            $retArr['minelev'] = $m[1];
            $retArr['maxelev'] = $m[2];
        }
        elseif(preg_match('/([.\d]+)\s*-\s*([.\d]+)\s*m$/i',$inStr,$m)){
            $retArr['minelev'] = $m[1];
            $retArr['maxelev'] = $m[2];
        }
        elseif(preg_match('/([.\d]+)\s*meter/i',$inStr,$m)){
            $retArr['minelev'] = $m[1];
        }
        elseif(preg_match('/([.\d]+)\s*m./i',$inStr,$m)){
            $retArr['minelev'] = $m[1];
        }
        elseif(preg_match('/([.\d]+)\s*m$/i',$inStr,$m)){
            $retArr['minelev'] = $m[1];
        }
        elseif(preg_match('/([.\d]+)[fet\']{,4}\s*-\s*([.\d]+)\s?[f\']/i',$inStr,$m)){
            $retArr['minelev'] = (round($m[1]*.3048));
            $retArr['maxelev'] = (round($m[2]*.3048));
        }
        elseif(preg_match('/([.\d]+)\s*[f\']/i',$inStr,$m)){
            $retArr['minelev'] = (round($m[1]*.3048));
        }
        if($retArr){
            if(array_key_exists('minelev',$retArr) && ($retArr['minelev'] > 8000 || $retArr['minelev'] < 0)) {
                unset($retArr['minelev']);
            }
            if(array_key_exists('maxelev',$retArr) && ($retArr['maxelev'] > 8000 || $retArr['maxelev'] < 0)) {
                unset($retArr['maxelev']);
            }
        }
        return $retArr;
    }

    public static function parseVerbatimCoordinates($inStr,$target=''){
        $retArr = array();
        if(strpos($inStr,' to ')) {
            return $retArr;
        }
        if(strpos($inStr,' betw ')) {
            return $retArr;
        }
        $search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
        $replace = array("'","'",'"','"','*','-','-');
        $inStr= str_replace($search, $replace, $inStr);

        $latSec = 0;
        $latNS = 'N';
        $lngSec = 0;
        $lngEW = 'W';
        if(!$target || $target === 'LL'){
            if(preg_match('/([\sNSns]?)(-?\d{1,2}\.\d+)\D?\s?([NSns]?)\D?([\sEWew])(-?\d{1,4}\.\d+)\D?\s?([EWew]?)\D*/',$inStr,$m)){
                $retArr['lat'] = $m[2];
                $retArr['lng'] = $m[5];
                $latDir = $m[3];
                if(!$latDir && $m[1]) {
                    $latDir = trim($m[1]);
                }
                if($retArr['lat'] > 0 && $latDir && ($latDir === 'S' || $latDir === 's')) {
                    $retArr['lat'] = -1 * $retArr['lat'];
                }
                $lngDir = $m[6];
                if(!$lngDir && $m[4]) {
                    $lngDir = trim($m[4]);
                }
                if($retArr['lng'] > 0 && $latDir && ($lngDir === 'W' || $lngDir === 'w')) {
                    $retArr['lng'] = -1 * $retArr['lng'];
                }
            }
            elseif(preg_match('/(\d{1,2})\D{1,3}\s{0,2}(\d{1,2}\.?\d*)[\'](.*)/',$inStr,$m)){
                $latDeg = $m[1];
                $latMin = $m[2];
                $leftOver = str_replace("''",'"',trim($m[3]));
                if(stripos($inStr,'N') === false && strpos($inStr,'S') !== false){
                    $latNS = 'S';
                }
                if(stripos($inStr,'W') === false && stripos($inStr,'E') !== false){
                    $lngEW = 'E';
                }
                if(preg_match('/^(\d{1,2}\.?\d*)["](.*)/',$leftOver,$m)){
                    $latSec = $m[1];
                    if(count($m)>2){
                        $leftOver = trim($m[2]);
                    }
                }
                if(preg_match('/(\d{1,3})\D{1,3}\s{0,2}(\d{1,2}\.?\d*)[\'](.*)/',$leftOver,$m)){
                    $lngDeg = $m[1];
                    $lngMin = $m[2];
                    $leftOver = trim($m[3]);
                    if(preg_match('/^(\d{1,2}\.?\d*)["](.*)/',$leftOver,$m)){
                        $lngSec = $m[1];
                    }
                    if(is_numeric($latDeg) && is_numeric($latMin) && is_numeric($lngDeg) && is_numeric($lngMin) && $latDeg < 90 && $latMin < 60 && $lngDeg < 180 && $lngMin < 60) {
                        $latDec = $latDeg + ($latMin/60) + ($latSec/3600);
                        $lngDec = $lngDeg + ($lngMin/60) + ($lngSec/3600);
                        if($latNS === 'S'){
                            $latDec = -$latDec;
                        }
                        if($lngEW === 'W'){
                            $lngDec = -$lngDec;
                        }
                        $retArr['lat'] = round($latDec,6);
                        $retArr['lng'] = round($lngDec,6);
                    }
                }
            }
        }
        if((!$target && !$retArr) || $target === 'UTM'){
            $d = '';
            if(preg_match('/NAD\s*27/i',$inStr)) {
                $d = 'NAD27';
            }
            if(preg_match('/\D*(\d{1,2}\D?)\s+(\d{6,7})m?E\s+(\d{7})m?N/i',$inStr,$m)){
                $z = $m[1];
                $e = $m[2];
                $n = $m[3];
                if($n && $e && $z){
                    $llArr = self::convertUtmToLL($e,$n,$z,$d);
                    if(isset($llArr['lat'])) {
                        $retArr['lat'] = $llArr['lat'];
                    }
                    if(isset($llArr['lng'])) {
                        $retArr['lng'] = $llArr['lng'];
                    }
                }

            }
            elseif(false !== strpos($inStr, 'UTM') || preg_match('/\d{1,2}[\D\s]+\d{6,7}[\D\s]+\d{6,7}/',$inStr)){
                $z = ''; $e = ''; $n = '';
                if(preg_match('/^(\d{1,2}\D?)[\s\D]+/',$inStr,$m)) {
                    $z = $m[1];
                }
                if(!$z && preg_match('/[\s\D]+(\d{1,2}\D?)$/',$inStr,$m)) {
                    $z = $m[1];
                }
                if(!$z && preg_match('/[\s\D]+(\d{1,2}\D?)[\s\D]+/',$inStr,$m)) {
                    $z = $m[1];
                }
                if($z){
                    if(preg_match('/(\d{6,7})m?E[\D\s]+(\d{7})m?N/i',$inStr,$m)){
                        $e = $m[1];
                        $n = $m[2];
                    }
                    elseif(preg_match('/m?E(\d{6,7})[\D\s]+m?N(\d{7})/i',$inStr,$m)){
                        $e = $m[1];
                        $n = $m[2];
                    }
                    elseif(preg_match('/(\d{7})m?N[\D\s]+(\d{6,7})m?E/i',$inStr,$m)){
                        $e = $m[2];
                        $n = $m[1];
                    }
                    elseif(preg_match('/m?N(\d{7})[\D\s]+m?E(\d{6,7})/i',$inStr,$m)){
                        $e = $m[2];
                        $n = $m[1];
                    }
                    elseif(preg_match('/(\d{6})[\D\s]+(\d{7})/',$inStr,$m)){
                        $e = $m[1];
                        $n = $m[2];
                    }
                    elseif(preg_match('/(\d{7})[\D\s]+(\d{6})/',$inStr,$m)){
                        $e = $m[2];
                        $n = $m[1];
                    }
                    if($e && $n){
                        $llArr = self::convertUtmToLL($e,$n,$z,$d);
                        if(isset($llArr['lat'])) {
                            $retArr['lat'] = $llArr['lat'];
                        }
                        if(isset($llArr['lng'])) {
                            $retArr['lng'] = $llArr['lng'];
                        }
                    }
                }
            }
        }
        if($retArr){
            if($retArr['lat'] < -90 || $retArr['lat'] > 90) {
                return false;
            }
            if($retArr['lng'] < -180 || $retArr['lng'] > 180) {
                return false;
            }
        }
        return $retArr;
    }

    public static function convertUtmToLL($e, $n, $z, $d): array
    {
        $retArr = array();
        if($e && $n && $z){
            $gPoint = new GPoint($d);
            $gPoint->setUTM($e,$n,$z);
            $gPoint->convertTMtoLL();
            $lat = $gPoint->Lat();
            $lng = $gPoint->Long();
            if($lat && $lng){
                $retArr['lat'] = round($lat,6);
                $retArr['lng'] = round($lng,6);
            }
        }
        return $retArr;
    }

    public static function occurrenceArrayCleaning($recMap){
        foreach($recMap as $k => $v){
            $recMap[$k] = trim($v);
        }
        if(isset($recMap['eventdate']) && $recMap['eventdate']){
            if(is_numeric($recMap['eventdate'])){
                if($recMap['eventdate'] > 2100 && $recMap['eventdate'] < 45000){
                    $recMap['eventdate'] = date('Y-m-d', mktime(0,0,0,1,$recMap['eventdate']-1,1900));
                }
                elseif($recMap['eventdate'] > 2200000 && $recMap['eventdate'] < 2500000){
                    $dArr = explode('/',jdtogregorian($recMap['eventdate']));
                    if($dArr){
                        $recMap['eventdate'] = $dArr[2].'-'.$dArr[0].'-'.$dArr[1];
                    }
                }
                elseif($recMap['eventdate'] > 19000000){
                    $recMap['eventdate'] = substr($recMap['eventdate'],0,4).'-'.substr($recMap['eventdate'],4,2).'-'.substr($recMap['eventdate'],6,2);
                }
            }
            else{
                $dateStr = self::formatDate($recMap['eventdate']);
                if($dateStr){
                    if($recMap['eventdate'] !== $dateStr && (!array_key_exists('verbatimeventdate',$recMap) || !$recMap['verbatimeventdate'])){
                        $recMap['verbatimeventdate'] = $recMap['eventdate'];
                    }
                    $recMap['eventdate'] = $dateStr;
                }
                else{
                    if(!array_key_exists('verbatimeventdate',$recMap) || !$recMap['verbatimeventdate']){
                        $recMap['verbatimeventdate'] = $recMap['eventdate'];
                    }
                    unset($recMap['eventdate']);
                }
            }
        }
        if(array_key_exists('latestdatecollected',$recMap) && $recMap['latestdatecollected'] && is_numeric($recMap['latestdatecollected'])){
            if($recMap['latestdatecollected'] > 2100 && $recMap['latestdatecollected'] < 45000){
                $recMap['latestdatecollected'] = date('Y-m-d', mktime(0,0,0,1,$recMap['latestdatecollected']-1,1900));
            }
            elseif($recMap['latestdatecollected'] > 2200000 && $recMap['latestdatecollected'] < 2500000){
                $dArr = explode('/',jdtogregorian($recMap['latestdatecollected']));
                if($dArr){
                    $recMap['latestdatecollected'] = $dArr[2].'-'.$dArr[0].'-'.$dArr[1];
                }
            }
            elseif($recMap['latestdatecollected'] > 19000000){
                $recMap['latestdatecollected'] = substr($recMap['latestdatecollected'],0,4).'-'.substr($recMap['latestdatecollected'],4,2).'-'.substr($recMap['latestdatecollected'],6,2);
            }
        }
        if(array_key_exists('verbatimeventdate',$recMap) && $recMap['verbatimeventdate'] && is_numeric($recMap['verbatimeventdate'])
            && $recMap['verbatimeventdate'] > 2100 && $recMap['verbatimeventdate'] < 45000){
            $recMap['verbatimeventdate'] = date('Y-m-d', mktime(0,0,0,1,$recMap['verbatimeventdate']-1,1900));
        }
        if(array_key_exists('dateidentified',$recMap) && $recMap['dateidentified'] && is_numeric($recMap['dateidentified'])
            && $recMap['dateidentified'] > 2100 && $recMap['dateidentified'] < 45000){
            $recMap['dateidentified'] = date('Y-m-d', mktime(0,0,0,1,$recMap['dateidentified']-1,1900));
        }
        if(array_key_exists('year',$recMap) || array_key_exists('month',$recMap) || array_key_exists('day',$recMap)){
            $y = (array_key_exists('year',$recMap)?$recMap['year']:'');
            $m = (array_key_exists('month',$recMap)?$recMap['month']:'');
            $d = (array_key_exists('day',$recMap)?$recMap['day']:'');
            $vDate = trim($y.'-'.$m.'-'.$d,'- ');
            if(isset($recMap['day']) && $recMap['day'] && !is_numeric($recMap['day'])){
                unset($recMap['day']);
                $d = '00';
            }
            if(isset($recMap['year']) && !is_numeric($recMap['year'])){
                unset($recMap['year']);
            }
            if(isset($recMap['month']) && $recMap['month'] && !is_numeric($recMap['month']) && !is_numeric($recMap['month'])) {
                $monAbbr = strtolower(substr($recMap['month'],0,3));
                if(preg_match('/^[IVX]{1-4}$/',$recMap['month'])){
                    $vDate = $d.'-'.$recMap['month'].'-'.$y;
                    $recMap['month'] = self::$monthRoman[$recMap['month']];
                    $recMap['eventdate'] = self::formatDate($y.'-'.$recMap['month'].'-'.($d?:'00'));
                }
                elseif(array_key_exists($monAbbr,self::$monthNames) && preg_match('/^\D{3,}$/',$recMap['month'])){
                    $vDate = $d.' '.$recMap['month'].' '.$y;
                    $recMap['month'] = self::$monthNames[$monAbbr];
                    $recMap['eventdate'] = self::formatDate($y.'-'.$recMap['month'].'-'.($d?:'00'));
                }
                elseif(preg_match('/^(\d{1,2})\s?-\s?(\D{3,10})$/',$recMap['month'],$m)){
                    $recMap['month'] = $m[1];
                    $recMap['eventdate'] = self::formatDate(trim($y.'-'.$recMap['month'].'-'.($d?:'00'),'- '));
                    $vDate = $d.' '.$m[2].' '.$y;
                }
                else{
                    unset($recMap['month']);
                }
            }
            if(!array_key_exists('verbatimeventdate',$recMap) || !$recMap['verbatimeventdate']){
                $recMap['verbatimeventdate'] = $vDate;
            }
            if($vDate && (!array_key_exists('eventdate',$recMap) || !$recMap['eventdate'])){
                $recMap['eventdate'] = self::formatDate($vDate);
            }
        }
        if((!array_key_exists('eventdate',$recMap) || !$recMap['eventdate']) && array_key_exists('verbatimeventdate',$recMap) && $recMap['verbatimeventdate'] && (!array_key_exists('year',$recMap) || !$recMap['year'])){
            $dateStr = self::formatDate($recMap['verbatimeventdate']);
            if($dateStr) {
                $recMap['eventdate'] = $dateStr;
            }
        }
        if((isset($recMap['recordnumberprefix']) && $recMap['recordnumberprefix']) || (isset($recMap['recordnumbersuffix']) && $recMap['recordnumbersuffix'])){
            $recNumber = $recMap['recordnumber'];
            if(isset($recMap['recordnumberprefix']) && $recMap['recordnumberprefix']) {
                $recNumber = $recMap['recordnumberprefix'] . '-' . $recNumber;
            }
            if(isset($recMap['recordnumbersuffix']) && $recMap['recordnumbersuffix']){
                if(is_numeric($recMap['recordnumbersuffix']) && $recMap['recordnumber']) {
                    $recNumber .= '-';
                }
                $recNumber .= $recMap['recordnumbersuffix'];
            }
            $recMap['recordnumber'] = $recNumber;
        }
        if(array_key_exists('decimallatitude',$recMap) || array_key_exists('decimallongitude',$recMap)){
            $latValue = (array_key_exists('decimallatitude',$recMap)?$recMap['decimallatitude']:'');
            $lngValue = (array_key_exists('decimallongitude',$recMap)?$recMap['decimallongitude']:'');
            if(($latValue && !is_numeric($latValue)) || ($lngValue && !is_numeric($lngValue))){
                $llArr = self::parseVerbatimCoordinates(trim($latValue.' '.$lngValue),'LL');
                if($llArr && array_key_exists('lat',$llArr) && array_key_exists('lng',$llArr)){
                    $recMap['decimallatitude'] = $llArr['lat'];
                    $recMap['decimallongitude'] = $llArr['lng'];
                }
                else{
                    unset($recMap['decimallatitude'], $recMap['decimallongitude']);
                }
                $vcStr = '';
                if(array_key_exists('verbatimcoordinates',$recMap) && $recMap['verbatimcoordinates']){
                    $vcStr .= $recMap['verbatimcoordinates'].'; ';
                }
                $vcStr .= $latValue.' '.$lngValue;
                if(trim($vcStr)) {
                    $recMap['verbatimcoordinates'] = trim($vcStr);
                }
            }
        }
        if(isset($recMap['verbatimlatitude']) || isset($recMap['verbatimlongitude'])){
            if(isset($recMap['verbatimlatitude'], $recMap['verbatimlongitude']) && !isset($recMap['decimallatitude'], $recMap['decimallongitude'])) {
                if((is_numeric($recMap['verbatimlatitude']) && is_numeric($recMap['verbatimlongitude']))){
                    if($recMap['verbatimlatitude'] > -90 && $recMap['verbatimlatitude'] < 90
                        && $recMap['verbatimlongitude'] > -180 && $recMap['verbatimlongitude'] < 180){
                        $recMap['decimallatitude'] = $recMap['verbatimlatitude'];
                        $recMap['decimallongitude'] = $recMap['verbatimlongitude'];
                    }
                }
                else{
                    $coordArr = self::parseVerbatimCoordinates($recMap['verbatimlatitude'].' '.$recMap['verbatimlongitude'],'LL');
                    if($coordArr){
                        if(array_key_exists('lat',$coordArr)) {
                            $recMap['decimallatitude'] = $coordArr['lat'];
                        }
                        if(array_key_exists('lng',$coordArr)) {
                            $recMap['decimallongitude'] = $coordArr['lng'];
                        }
                    }
                }
            }
            $vCoord = ($recMap['verbatimcoordinates'] ?? '');
            if($vCoord) {
                $vCoord .= '; ';
            }
            if(stripos($vCoord,$recMap['verbatimlatitude']) === false && stripos($vCoord,$recMap['verbatimlongitude']) === false){
                $recMap['verbatimcoordinates'] = trim($vCoord.$recMap['verbatimlatitude'].', '.$recMap['verbatimlongitude'],' ,;');
            }
        }
        if(isset($recMap['latdeg'], $recMap['lngdeg']) && $recMap['latdeg'] && $recMap['lngdeg']){
            if(is_numeric($recMap['latdeg']) && is_numeric($recMap['lngdeg']) && (!isset($recMap['decimallatitude'], $recMap['decimallongitude']))){
                $latDec = $recMap['latdeg'];
                if(isset($recMap['latmin']) && $recMap['latmin'] && is_numeric($recMap['latmin'])) {
                    $latDec += $recMap['latmin'] / 60;
                }
                if(isset($recMap['latsec']) && $recMap['latsec'] && is_numeric($recMap['latsec'])) {
                    $latDec += $recMap['latsec'] / 3600;
                }
                if($latDec > 0 && stripos($recMap['latns'],'s') === 0) {
                    $latDec *= -1;
                }
                $lngDec = $recMap['lngdeg'];
                if(isset($recMap['lngmin']) && $recMap['lngmin'] && is_numeric($recMap['lngmin'])) {
                    $lngDec += $recMap['lngmin'] / 60;
                }
                if(isset($recMap['lngsec']) && $recMap['lngsec'] && is_numeric($recMap['lngsec'])) {
                    $lngDec += $recMap['lngsec'] / 3600;
                }
                if($lngDec > 0 && stripos($recMap['lngew'],'w') === 0) {
                    $lngDec *= -1;
                }
                if(($lngDec > 0) && in_array(strtolower($recMap['country']), array('usa', 'united states', 'canada', 'mexico', 'panama'))) {
                    $lngDec *= -1;
                }
                $recMap['decimallatitude'] = round($latDec,6);
                $recMap['decimallongitude'] = round($lngDec,6);
            }
            $vCoord = ($recMap['verbatimcoordinates'] ?? '');
            if($vCoord) {
                $vCoord .= '; ';
            }
            $vCoord .= $recMap['latdeg'].chr(167).' ';
            if(isset($recMap['latmin']) && $recMap['latmin']) {
                $vCoord .= $recMap['latmin'] . 'm ';
            }
            if(isset($recMap['latsec']) && $recMap['latsec']) {
                $vCoord .= $recMap['latsec'] . 's ';
            }
            if(isset($recMap['latns'])) {
                $vCoord .= $recMap['latns'] . '; ';
            }
            $vCoord .= $recMap['lngdeg'].chr(167).' ';
            if(isset($recMap['lngmin']) && $recMap['lngmin']) {
                $vCoord .= $recMap['lngmin'] . 'm ';
            }
            if(isset($recMap['lngsec']) && $recMap['lngsec']) {
                $vCoord .= $recMap['lngsec'] . 's ';
            }
            if(isset($recMap['lngew'])) {
                $vCoord .= $recMap['lngew'];
            }
            $recMap['verbatimcoordinates'] = $vCoord;
        }
        if((array_key_exists('utmnorthing',$recMap) && $recMap['utmnorthing']) || (array_key_exists('utmeasting',$recMap) && $recMap['utmeasting'])){
            $no = (array_key_exists('utmnorthing',$recMap)?$recMap['utmnorthing']:'');
            $ea = (array_key_exists('utmeasting',$recMap)?$recMap['utmeasting']:'');
            $zo = (array_key_exists('utmzoning',$recMap)?$recMap['utmzoning']:'');
            $da = (array_key_exists('geodeticdatum',$recMap)?$recMap['geodeticdatum']:'');
            if(!isset($recMap['decimallatitude'], $recMap['decimallongitude'])){
                if($no && $ea && $zo){
                    $llArr = self::convertUtmToLL($ea,$no,$zo,$da);
                    if(isset($llArr['lat'])) {
                        $recMap['decimallatitude'] = $llArr['lat'];
                    }
                    if(isset($llArr['lng'])) {
                        $recMap['decimallongitude'] = $llArr['lng'];
                    }
                }
                else{
                    $coordArr = self::parseVerbatimCoordinates(trim($zo.' '.$ea.' '.$no),'UTM');
                    if($coordArr){
                        if(array_key_exists('lat',$coordArr)) {
                            $recMap['decimallatitude'] = $coordArr['lat'];
                        }
                        if(array_key_exists('lng',$coordArr)) {
                            $recMap['decimallongitude'] = $coordArr['lng'];
                        }
                    }
                }
            }
            $vCoord = ($recMap['verbatimcoordinates'] ?? '');
            if(!($no && strpos($vCoord,$no))) {
                $recMap['verbatimcoordinates'] = ($vCoord ? $vCoord . '; ' : '') . $zo . ' ' . $ea . 'E ' . $no . 'N';
            }
        }
        if(isset($recMap['trstownship'], $recMap['trsrange']) && $recMap['trstownship'] && $recMap['trsrange']){
            $vCoord = ($recMap['verbatimcoordinates'] ?? '');
            if($vCoord) {
                $vCoord .= '; ';
            }
            $vCoord .= (stripos($recMap['trstownship'],'t') === false?'T':'').$recMap['trstownship'].' ';
            $vCoord .= (stripos($recMap['trsrange'],'r') === false?'R':'').$recMap['trsrange'].' ';
            if(isset($recMap['trssection'])) {
                $vCoord .= (stripos($recMap['trssection'], 's') === false ? 'sec' : '') . $recMap['trssection'] . ' ';
            }
            if(isset($recMap['trssectiondetails'])) {
                $vCoord .= $recMap['trssectiondetails'];
            }
            $recMap['verbatimcoordinates'] = trim($vCoord);
        }

        if((isset($recMap['minimumelevationinmeters']) && $recMap['minimumelevationinmeters'] && !is_numeric($recMap['minimumelevationinmeters']))
            || (isset($recMap['maximumelevationinmeters']) && $recMap['maximumelevationinmeters'] && !is_numeric($recMap['maximumelevationinmeters']))){
            $vStr = ($recMap['verbatimelevation'] ?? '');
            if(isset($recMap['minimumelevationinmeters']) && $recMap['minimumelevationinmeters']) {
                $vStr .= ($vStr ? '; ' : '') . $recMap['minimumelevationinmeters'];
            }
            if(isset($recMap['maximumelevationinmeters']) && $recMap['maximumelevationinmeters']) {
                $vStr .= '-' . $recMap['maximumelevationinmeters'];
            }
            $recMap['verbatimelevation'] = $vStr;
            $recMap['minimumelevationinmeters'] = '';
            $recMap['maximumelevationinmeters'] = '';
        }
        if(array_key_exists('verbatimelevation',$recMap) && $recMap['verbatimelevation'] && (!array_key_exists('minimumelevationinmeters',$recMap) || !$recMap['minimumelevationinmeters'])){
            $eArr = self::parseVerbatimElevation($recMap['verbatimelevation']);
            if($eArr && array_key_exists('minelev', $eArr)) {
                $recMap['minimumelevationinmeters'] = $eArr['minelev'];
                if(array_key_exists('maxelev',$eArr)) {
                    $recMap['maximumelevationinmeters'] = $eArr['maxelev'];
                }
            }
        }
        if(isset($recMap['elevationnumber']) && $recMap['elevationnumber']){
            $elevStr = $recMap['elevationnumber'].$recMap['elevationunits'];
            $eArr = self::parseVerbatimElevation($elevStr);
            if($eArr && array_key_exists('minelev', $eArr)) {
                $recMap['minimumelevationinmeters'] = $eArr['minelev'];
                if(array_key_exists('maxelev',$eArr)) {
                    $recMap['maximumelevationinmeters'] = $eArr['maxelev'];
                }
            }
            if(!$eArr || !stripos($elevStr,'m')){
                $vElev = ($recMap['verbatimelevation'] ?? '');
                if($vElev) {
                    $vElev .= '; ';
                }
                $recMap['verbatimelevation'] = $vElev.$elevStr;
            }
        }
        if(isset($recMap['collectorfamilyname']) && $recMap['collectorfamilyname'] && (!isset($recMap['recordedby']) || !$recMap['recordedby'])){
            $recordedBy = $recMap['collectorfamilyname'];
            if(isset($recMap['collectorinitials']) && $recMap['collectorinitials']) {
                $recordedBy .= ', ' . $recMap['collectorinitials'];
            }
            $recMap['recordedby'] = $recordedBy;
        }

        if(array_key_exists('specificepithet',$recMap)){
            if($recMap['specificepithet'] === 'sp.' || $recMap['specificepithet'] === 'sp') {
                $recMap['specificepithet'] = '';
            }
        }
        if(array_key_exists('taxonrank',$recMap)){
            $tr = strtolower($recMap['taxonrank']);
            if($tr === 'species' || !array_key_exists('specificepithet',$recMap)) {
                $recMap['taxonrank'] = '';
            }
            if($tr === 'subspecies') {
                $recMap['taxonrank'] = 'subsp.';
            }
            if($tr === 'variety') {
                $recMap['taxonrank'] = 'var.';
            }
            if($tr === 'forma') {
                $recMap['taxonrank'] = 'f.';
            }
        }

        if(array_key_exists('sciname',$recMap) && $recMap['sciname']){
            if(substr($recMap['sciname'],-4) === ' sp.') {
                $recMap['sciname'] = substr($recMap['sciname'], 0, -4);
            }
            if(substr($recMap['sciname'],-3) === ' sp') {
                $recMap['sciname'] = substr($recMap['sciname'], 0, -3);
            }

            $recMap['sciname'] = str_replace(array(' ssp. ',' ssp '),' subsp. ',$recMap['sciname']);
            $recMap['sciname'] = str_replace(' var ',' var. ',$recMap['sciname']);

            $pattern = '/\b(cf\.|cf|aff\.|aff)\s{1}/';
            if(preg_match($pattern,$recMap['sciname'],$m)){
                $recMap['identificationqualifier'] = $m[1];
                $recMap['sciname'] = preg_replace($pattern,'',$recMap['sciname']);
            }
        }
        else if(array_key_exists('genus',$recMap)){
            $sciName = $recMap['genus'];
            if(array_key_exists('specificepithet',$recMap)) {
                $sciName .= ' ' . $recMap['specificepithet'];
            }
            if(array_key_exists('infraspecificepithet',$recMap)) {
                if(array_key_exists('taxonrank',$recMap)) {
                    $sciName .= ' ' . $recMap['taxonrank'];
                }
                $sciName .= ' ' . $recMap['infraspecificepithet'];
            }
            $recMap['sciname'] = trim($sciName);
        }
        elseif(array_key_exists('scientificname',$recMap)){
            $parsedArr = (new TaxonomyUtilities)->parseScientificName($recMap['scientificname']);
            $scinameStr = '';
            if(array_key_exists('unitname1',$parsedArr)){
                $scinameStr = $parsedArr['unitname1'];
                if(!array_key_exists('genus',$recMap) || $recMap['genus']){
                    $recMap['genus'] = $parsedArr['unitname1'];
                }
            }
            if(array_key_exists('unitname2',$parsedArr)){
                $scinameStr .= ' '.$parsedArr['unitname2'];
                if(!array_key_exists('specificepithet',$recMap) || !$recMap['specificepithet']){
                    $recMap['specificepithet'] = $parsedArr['unitname2'];
                }
            }
            if(array_key_exists('unitind3',$parsedArr)){
                $scinameStr .= ' '.$parsedArr['unitind3'];
                if((!array_key_exists('taxonrank',$recMap) || !$recMap['taxonrank'])){
                    $recMap['taxonrank'] = $parsedArr['unitind3'];
                }
            }
            if(array_key_exists('unitname3',$parsedArr)){
                $scinameStr .= ' '.$parsedArr['unitname3'];
                if(!array_key_exists('infraspecificepithet',$recMap) || !$recMap['infraspecificepithet']){
                    $recMap['infraspecificepithet'] = $parsedArr['unitname3'];
                }
            }
            if(array_key_exists('author',$parsedArr)){
                if(!array_key_exists('scientificnameauthorship',$recMap) || !$recMap['scientificnameauthorship']){
                    $recMap['scientificnameauthorship'] = $parsedArr['author'];
                }
            }
            $recMap['sciname'] = trim($scinameStr);
        }
        return $recMap;
    }
}
