<?php
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/TaxonomyService.php');

class DataUtilitiesService {

    public static $ellipsoid = array(
        'Airy' => array(6377563, 0.00667054),
        'Australian National' => array(6378160, 0.006694542),
        'Bessel 1841' => array(6377397, 0.006674372),
        'Bessel 1841 Nambia' => array(6377484, 0.006674372),
        'Clarke 1866' => array(6378206, 0.006768658),
        'Clarke 1880' => array(6378249, 0.006803511),
        'Everest' => array(6377276, 0.006637847),
        'Fischer 1960 Mercury' => array(6378166, 0.006693422),
        'Fischer 1968' => array(6378150, 0.006693422),
        'GRS 1967' => array(6378160, 0.006694605),
        'GRS 1980' => array(6378137, 0.00669438),
        'Helmert 1906' => array(6378200, 0.006693422),
        'Hough' => array(6378270, 0.00672267),
        'International' => array(6378388, 0.00672267),
        'Krassovsky' => array(6378245, 0.006693422),
        'Modified Airy' => array(6377340, 0.00667054),
        'Modified Everest' => array(6377304, 0.006637847),
        'Modified Fischer 1960' => array(6378155, 0.006693422),
        'South American 1969' => array(6378160, 0.006694542),
        'WGS 60' => array(6378165, 0.006693422),
        'WGS 66' => array(6378145, 0.006694542),
        'WGS 72' => array(6378135, 0.006694318),
        'WGS 84' => array(6378137, 0.00669438)
    );
    public static $monthRoman = array('I'=>'01','II'=>'02','III'=>'03','IV'=>'04','V'=>'05','VI'=>'06','VII'=>'07','VIII'=>'08','IX'=>'09','X'=>'10','XI'=>'11','XII'=>'12');
    public static $monthNames = array('jan'=>'01','ene'=>'01','feb'=>'02','mar'=>'03','abr'=>'04','apr'=>'04','may'=>'05','jun'=>'06','jul'=>'07','ago'=>'08',
        'aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12','dic'=>'12');

    public static function cleanOccurrenceData($occData){
        foreach($occData as $k => $v){
            $occData[$k] = $v ? trim($v) : null;
        }
        if(isset($occData['eventdate']) && $occData['eventdate']){
            if(is_numeric($occData['eventdate'])){
                if($occData['eventdate'] > 2100 && $occData['eventdate'] < 45000){
                    $occData['eventdate'] = date('Y-m-d', mktime(0,0,0,1,$occData['eventdate'] - 1,1900));
                }
                elseif($occData['eventdate'] > 2200000 && $occData['eventdate'] < 2500000){
                    $dArr = explode('/', jdtogregorian($occData['eventdate']));
                    if($dArr){
                        $occData['eventdate'] = $dArr[2] . '-' . $dArr[0] . '-' . $dArr[1];
                    }
                }
                elseif($occData['eventdate'] > 19000000){
                    $occData['eventdate'] = substr($occData['eventdate'],0,4) . '-' . substr($occData['eventdate'],4,2) . '-' . substr($occData['eventdate'],6,2);
                }
            }
            else{
                $dateStr = self::formatDate($occData['eventdate']);
                if($dateStr){
                    if($occData['eventdate'] !== $dateStr && (!array_key_exists('verbatimeventdate', $occData) || !$occData['verbatimeventdate'])){
                        $occData['verbatimeventdate'] = $occData['eventdate'];
                    }
                    $occData['eventdate'] = $dateStr;
                }
                else{
                    if(!array_key_exists('verbatimeventdate', $occData) || !$occData['verbatimeventdate']){
                        $occData['verbatimeventdate'] = $occData['eventdate'];
                    }
                    unset($occData['eventdate']);
                }
            }
        }
        if(array_key_exists('latestdatecollected', $occData) && $occData['latestdatecollected'] && is_numeric($occData['latestdatecollected'])){
            if($occData['latestdatecollected'] > 2100 && $occData['latestdatecollected'] < 45000){
                $occData['latestdatecollected'] = date('Y-m-d', mktime(0,0,0,1,$occData['latestdatecollected'] - 1,1900));
            }
            elseif($occData['latestdatecollected'] > 2200000 && $occData['latestdatecollected'] < 2500000){
                $dArr = explode('/', jdtogregorian($occData['latestdatecollected']));
                if($dArr){
                    $occData['latestdatecollected'] = $dArr[2] . '-' . $dArr[0] . '-' . $dArr[1];
                }
            }
            elseif($occData['latestdatecollected'] > 19000000){
                $occData['latestdatecollected'] = substr($occData['latestdatecollected'],0,4) . '-' . substr($occData['latestdatecollected'],4,2) . '-' . substr($occData['latestdatecollected'],6,2);
            }
        }
        if(array_key_exists('verbatimeventdate', $occData) && $occData['verbatimeventdate'] && is_numeric($occData['verbatimeventdate'])
            && $occData['verbatimeventdate'] > 2100 && $occData['verbatimeventdate'] < 45000){
            $occData['verbatimeventdate'] = date('Y-m-d', mktime(0,0,0,1,$occData['verbatimeventdate'] - 1,1900));
        }
        if(array_key_exists('dateidentified', $occData) && $occData['dateidentified'] && is_numeric($occData['dateidentified'])
            && $occData['dateidentified'] > 2100 && $occData['dateidentified'] < 45000){
            $occData['dateidentified'] = date('Y-m-d', mktime(0,0,0,1,$occData['dateidentified'] - 1,1900));
        }
        if(array_key_exists('year', $occData) || array_key_exists('month', $occData) || array_key_exists('day', $occData)){
            $y = (array_key_exists('year', $occData) && is_numeric($occData['year'])) ? $occData['year'] : '';
            $m = (array_key_exists('month', $occData) && is_numeric($occData['month'])) ? $occData['month'] : '';
            $d = (array_key_exists('day', $occData) && is_numeric($occData['day'])) ? $occData['day'] : '';
            $vDate = trim($y . '-' . $m . '-' . $d,'- ');
            if(isset($occData['day']) && $occData['day'] && !is_numeric($occData['day'])){
                unset($occData['day']);
                $d = '00';
            }
            if(isset($occData['year']) && !is_numeric($occData['year'])){
                unset($occData['year']);
            }
            if(isset($occData['month']) && $occData['month'] && !is_numeric($occData['month'])) {
                $monAbbr = strtolower(substr($occData['month'],0,3));
                if(preg_match('/^[IVX]{1-4}$/', $occData['month'])){
                    $vDate = $d . '-' . $occData['month'] . '-' . $y;
                    $occData['month'] = self::$monthRoman[$occData['month']];
                    $occData['eventdate'] = self::formatDate($y . '-' . $occData['month'] . '-' . ($d ?: '00'));
                }
                elseif(array_key_exists($monAbbr, self::$monthNames) && preg_match('/^\D{3,}$/', $occData['month'])){
                    $vDate = $d . ' ' . $occData['month'] . ' ' . $y;
                    $occData['month'] = self::$monthNames[$monAbbr];
                    $occData['eventdate'] = self::formatDate($y . '-' . $occData['month'] . '-' . ($d ?: '00'));
                }
                elseif(preg_match('/^(\d{1,2})\s?-\s?(\D{3,10})$/', $occData['month'],$m)){
                    $occData['month'] = $m[1];
                    $occData['eventdate'] = self::formatDate(trim($y . '-' . $occData['month'] . '-' . ($d ?: '00'),'- '));
                    $vDate = $d . ' ' . $m[2] . ' ' . $y;
                }
                else{
                    unset($occData['month']);
                }
            }
            if(!array_key_exists('verbatimeventdate', $occData) || !$occData['verbatimeventdate']){
                $occData['verbatimeventdate'] = $vDate;
            }
            if($vDate && (!array_key_exists('eventdate', $occData) || !$occData['eventdate'])){
                $occData['eventdate'] = self::formatDate($vDate);
            }
        }
        if((!array_key_exists('eventdate', $occData) || !$occData['eventdate']) && array_key_exists('verbatimeventdate', $occData) && $occData['verbatimeventdate'] && (!array_key_exists('year', $occData) || !$occData['year'])){
            $dateStr = self::formatDate($occData['verbatimeventdate']);
            if($dateStr) {
                $occData['eventdate'] = $dateStr;
            }
        }
        if(array_key_exists('decimallatitude', $occData) || array_key_exists('decimallongitude', $occData)){
            $latValue = array_key_exists('decimallatitude', $occData) ? $occData['decimallatitude'] : '';
            $lngValue = array_key_exists('decimallongitude', $occData) ? $occData['decimallongitude'] : '';
            if(($latValue && !is_numeric($latValue)) || ($lngValue && !is_numeric($lngValue))){
                $llArr = self::parseVerbatimCoordinates(trim($latValue . ' ' . $lngValue), 'LL');
                if($llArr && array_key_exists('lat', $llArr) && array_key_exists('lng', $llArr)){
                    $occData['decimallatitude'] = $llArr['lat'];
                    $occData['decimallongitude'] = $llArr['lng'];
                }
                else{
                    unset($occData['decimallatitude'], $occData['decimallongitude']);
                }
                $vcStr = '';
                if(array_key_exists('verbatimcoordinates', $occData) && $occData['verbatimcoordinates']){
                    $vcStr .= $occData['verbatimcoordinates'] . '; ';
                }
                $vcStr .= $latValue . ' ' . $lngValue;
                if(trim($vcStr)) {
                    $occData['verbatimcoordinates'] = trim($vcStr);
                }
            }
        }
        if(isset($occData['verbatimlatitude']) || isset($occData['verbatimlongitude'])){
            if(isset($occData['verbatimlatitude'], $occData['verbatimlongitude']) && !isset($occData['decimallatitude'], $occData['decimallongitude'])) {
                if((is_numeric($occData['verbatimlatitude']) && is_numeric($occData['verbatimlongitude']))){
                    if($occData['verbatimlatitude'] > -90 && $occData['verbatimlatitude'] < 90 && $occData['verbatimlongitude'] > -180 && $occData['verbatimlongitude'] < 180){
                        $occData['decimallatitude'] = $occData['verbatimlatitude'];
                        $occData['decimallongitude'] = $occData['verbatimlongitude'];
                    }
                }
                else{
                    $coordArr = self::parseVerbatimCoordinates($occData['verbatimlatitude'] . ' ' . $occData['verbatimlongitude'],'LL');
                    if($coordArr){
                        if(array_key_exists('lat', $coordArr)) {
                            $occData['decimallatitude'] = $coordArr['lat'];
                        }
                        if(array_key_exists('lng', $coordArr)) {
                            $occData['decimallongitude'] = $coordArr['lng'];
                        }
                    }
                }
            }
            $vCoord = ($occData['verbatimcoordinates'] ?? '');
            if($vCoord) {
                $vCoord .= '; ';
            }
            if(stripos($vCoord, $occData['verbatimlatitude']) === false && stripos($vCoord, $occData['verbatimlongitude']) === false){
                $occData['verbatimcoordinates'] = trim($vCoord . $occData['verbatimlatitude'] . ', ' . $occData['verbatimlongitude'],' ,;');
            }
        }
        if(isset($occData['latdeg'], $occData['lngdeg']) && $occData['latdeg'] && $occData['lngdeg']){
            if(is_numeric($occData['latdeg']) && is_numeric($occData['lngdeg']) && (!isset($occData['decimallatitude'], $occData['decimallongitude']))){
                $latDec = $occData['latdeg'];
                if(isset($occData['latmin']) && $occData['latmin'] && is_numeric($occData['latmin'])) {
                    $latDec += $occData['latmin'] / 60;
                }
                if(isset($occData['latsec']) && $occData['latsec'] && is_numeric($occData['latsec'])) {
                    $latDec += $occData['latsec'] / 3600;
                }
                if($latDec > 0 && strncasecmp($occData['latns'], 's', 1) === 0) {
                    $latDec *= -1;
                }
                $lngDec = $occData['lngdeg'];
                if(isset($occData['lngmin']) && $occData['lngmin'] && is_numeric($occData['lngmin'])) {
                    $lngDec += $occData['lngmin'] / 60;
                }
                if(isset($occData['lngsec']) && $occData['lngsec'] && is_numeric($occData['lngsec'])) {
                    $lngDec += $occData['lngsec'] / 3600;
                }
                if($lngDec > 0 && strncasecmp($occData['lngew'], 'w', 1) === 0) {
                    $lngDec *= -1;
                }
                if(($lngDec > 0) && in_array(strtolower($occData['country']), array('usa', 'united states', 'canada', 'mexico', 'panama'))) {
                    $lngDec *= -1;
                }
                $occData['decimallatitude'] = round($latDec,6);
                $occData['decimallongitude'] = round($lngDec,6);
            }
            $vCoord = $occData['verbatimcoordinates'] ?? '';
            if($vCoord) {
                $vCoord .= '; ';
            }
            $vCoord .= $occData['latdeg'] . chr(167) . ' ';
            if(isset($occData['latmin']) && $occData['latmin']) {
                $vCoord .= $occData['latmin'] . 'm ';
            }
            if(isset($occData['latsec']) && $occData['latsec']) {
                $vCoord .= $occData['latsec'] . 's ';
            }
            if(isset($occData['latns'])) {
                $vCoord .= $occData['latns'] . '; ';
            }
            $vCoord .= $occData['lngdeg'] . chr(167) . ' ';
            if(isset($occData['lngmin']) && $occData['lngmin']) {
                $vCoord .= $occData['lngmin'] . 'm ';
            }
            if(isset($occData['lngsec']) && $occData['lngsec']) {
                $vCoord .= $occData['lngsec'] . 's ';
            }
            if(isset($occData['lngew'])) {
                $vCoord .= $occData['lngew'];
            }
            $occData['verbatimcoordinates'] = $vCoord;
        }
        if((array_key_exists('utmnorthing', $occData) && is_numeric($occData['utmnorthing'])) || (array_key_exists('utmeasting', $occData) && is_numeric($occData['utmeasting']))){
            $no = array_key_exists('utmnorthing',$occData) ? $occData['utmnorthing'] : '';
            $ea = array_key_exists('utmeasting',$occData) ? $occData['utmeasting'] : '';
            $zo = array_key_exists('utmzoning',$occData) ? $occData['utmzoning'] : '';
            $da = array_key_exists('geodeticdatum',$occData) ? $occData['geodeticdatum'] : '';
            if(!isset($occData['decimallatitude'], $occData['decimallongitude'])){
                if($no && $ea && $zo){
                    $llArr = self::convertUtmToLL($ea, $no, $zo, $da);
                    if(isset($llArr['lat'])) {
                        $occData['decimallatitude'] = $llArr['lat'];
                    }
                    if(isset($llArr['lng'])) {
                        $occData['decimallongitude'] = $llArr['lng'];
                    }
                }
                else{
                    $coordArr = self::parseVerbatimCoordinates(trim($zo . ' ' . $ea . ' ' . $no),'UTM');
                    if($coordArr){
                        if(array_key_exists('lat', $coordArr)) {
                            $occData['decimallatitude'] = $coordArr['lat'];
                        }
                        if(array_key_exists('lng', $coordArr)) {
                            $occData['decimallongitude'] = $coordArr['lng'];
                        }
                    }
                }
            }
            $vCoord = $occData['verbatimcoordinates'] ?? '';
            if(!($no && strpos($vCoord,$no))) {
                $occData['verbatimcoordinates'] = ($vCoord ? $vCoord . '; ' : '') . $zo . ' ' . $ea . 'E ' . $no . 'N';
            }
        }
        if(isset($occData['trstownship'], $occData['trsrange']) && $occData['trstownship'] && $occData['trsrange']){
            $vCoord = $occData['verbatimcoordinates'] ?? '';
            if($vCoord) {
                $vCoord .= '; ';
            }
            $vCoord .= (stripos($occData['trstownship'],'t') === false ? 'T' : '') . $occData['trstownship'] . ' ';
            $vCoord .= (stripos($occData['trsrange'],'r') === false ? 'R' : '') . $occData['trsrange'] . ' ';
            if(isset($occData['trssection'])) {
                $vCoord .= (stripos($occData['trssection'], 's') === false ? 'sec' : '') . $occData['trssection'] . ' ';
            }
            if(isset($occData['trssectiondetails'])) {
                $vCoord .= $occData['trssectiondetails'];
            }
            $occData['verbatimcoordinates'] = trim($vCoord);
        }
        if((isset($occData['minimumelevationinmeters']) && $occData['minimumelevationinmeters'] && !is_numeric($occData['minimumelevationinmeters'])) || (isset($occData['maximumelevationinmeters']) && $occData['maximumelevationinmeters'] && !is_numeric($occData['maximumelevationinmeters']))){
            $vStr = ($occData['verbatimelevation'] ?? '');
            if(isset($occData['minimumelevationinmeters']) && $occData['minimumelevationinmeters']) {
                $vStr .= ($vStr ? '; ' : '') . $occData['minimumelevationinmeters'];
            }
            if(isset($occData['maximumelevationinmeters']) && $occData['maximumelevationinmeters']) {
                $vStr .= '-' . $occData['maximumelevationinmeters'];
            }
            $occData['verbatimelevation'] = $vStr;
            $occData['minimumelevationinmeters'] = '';
            $occData['maximumelevationinmeters'] = '';
        }
        if(array_key_exists('verbatimelevation', $occData) && $occData['verbatimelevation'] && (!array_key_exists('minimumelevationinmeters', $occData) || !$occData['minimumelevationinmeters'])){
            $eArr = self::parseVerbatimElevation($occData['verbatimelevation']);
            if($eArr && array_key_exists('minelev', $eArr)) {
                $occData['minimumelevationinmeters'] = $eArr['minelev'];
                if(array_key_exists('maxelev', $eArr)) {
                    $occData['maximumelevationinmeters'] = $eArr['maxelev'];
                }
            }
        }
        if(isset($occData['elevationnumber']) && $occData['elevationnumber']){
            $elevStr = $occData['elevationnumber'] . ((isset($occData['elevationunits']) && $occData['elevationunits']) ? $occData['elevationunits'] : '');
            $eArr = self::parseVerbatimElevation($elevStr);
            if($eArr && array_key_exists('minelev', $eArr)){
                $occData['minimumelevationinmeters'] = $eArr['minelev'];
                if(array_key_exists('maxelev', $eArr)) {
                    $occData['maximumelevationinmeters'] = $eArr['maxelev'];
                }
            }
            if(!$eArr || !stripos($elevStr,'m')){
                $vElev = ($occData['verbatimelevation'] ?? '');
                if($vElev) {
                    $vElev .= '; ';
                }
                $occData['verbatimelevation'] = $vElev.$elevStr;
            }
        }
        if(array_key_exists('specificepithet',$occData)){
            if($occData['specificepithet'] === 'sp.' || $occData['specificepithet'] === 'sp'){
                $occData['specificepithet'] = '';
            }
        }
        if(array_key_exists('taxonrank',$occData)){
            $tr = $occData['taxonrank'] ? strtolower($occData['taxonrank']) : '';
            if($tr === 'species' || !array_key_exists('specificepithet',$occData)){
                $occData['taxonrank'] = '';
            }
            elseif($tr === 'subspecies'){
                $occData['taxonrank'] = 'subsp.';
            }
            elseif($tr === 'variety'){
                $occData['taxonrank'] = 'var.';
            }
            elseif($tr === 'forma'){
                $occData['taxonrank'] = 'f.';
            }
        }
        if(array_key_exists('sciname', $occData) && $occData['sciname']){
            if(substr($occData['sciname'],-4) === ' sp.') {
                $occData['sciname'] = substr($occData['sciname'], 0, -4);
            }
            if(substr($occData['sciname'],-3) === ' sp') {
                $occData['sciname'] = substr($occData['sciname'], 0, -3);
            }
            $occData['sciname'] = str_replace(array(' ssp. ',' ssp '),' subsp. ',$occData['sciname']);
            $occData['sciname'] = str_replace(' var ',' var. ',$occData['sciname']);
            $pattern = '/\b(cf\.|cf|aff\.|aff)\s/';
            if(preg_match($pattern,$occData['sciname'],$m)){
                $occData['identificationqualifier'] = $m[1];
                $occData['sciname'] = preg_replace($pattern,'', $occData['sciname']);
            }
        }
        else if(array_key_exists('genus', $occData)){
            $sciName = $occData['genus'];
            if(array_key_exists('specificepithet', $occData)) {
                $sciName .= ' ' . $occData['specificepithet'];
            }
            if(array_key_exists('infraspecificepithet', $occData)) {
                if(array_key_exists('taxonrank', $occData)) {
                    $sciName .= ' ' . $occData['taxonrank'];
                }
                $sciName .= ' ' . $occData['infraspecificepithet'];
            }
            $occData['sciname'] = trim($sciName);
        }
        elseif(array_key_exists('scientificname', $occData)){
            $parsedArr = (new TaxonomyService)->parseScientificName($occData['scientificname']);
            $scinameStr = '';
            if(array_key_exists('unitname1', $parsedArr)){
                $scinameStr = $parsedArr['unitname1'];
                if(!array_key_exists('genus', $occData) || $occData['genus']){
                    $occData['genus'] = $parsedArr['unitname1'];
                }
            }
            if(array_key_exists('unitname2', $parsedArr)){
                $scinameStr .= ' ' . $parsedArr['unitname2'];
                if(!array_key_exists('specificepithet', $occData) || !$occData['specificepithet']){
                    $occData['specificepithet'] = $parsedArr['unitname2'];
                }
            }
            if(array_key_exists('unitind3', $parsedArr)){
                $scinameStr .= ' ' . $parsedArr['unitind3'];
                if((!array_key_exists('taxonrank', $occData) || !$occData['taxonrank'])){
                    $occData['taxonrank'] = $parsedArr['unitind3'];
                }
            }
            if(array_key_exists('unitname3', $parsedArr)){
                $scinameStr .= ' ' . $parsedArr['unitname3'];
                if(!array_key_exists('infraspecificepithet', $occData) || !$occData['infraspecificepithet']){
                    $occData['infraspecificepithet'] = $parsedArr['unitname3'];
                }
            }
            if(array_key_exists('author', $parsedArr)){
                if(!array_key_exists('scientificnameauthorship', $occData) || !$occData['scientificnameauthorship']){
                    $occData['scientificnameauthorship'] = $parsedArr['author'];
                }
            }
            $occData['sciname'] = trim($scinameStr);
        }
        return $occData;
    }

    public static function convertTMtoLL($easting, $northing, $zone, $a, $e2): array
    {
        $k0 = 0.9996;
        $e1 = (1 - sqrt(1 - $e2)) / (1 + sqrt(1 - $e2));
        sscanf($zone, '%d%s',$zoneNumber, $zoneLetter);
        $isSouthern = false;
        if($zoneLetter){
            if(strtoupper($zoneLetter) < 'N'){
                $isSouthern = true;
            }
            if(strtoupper($zoneLetter) === 'S'){
                if(($zoneNumber > 18 && $zoneNumber < 23) || $northing < 3540000 || $northing > 4420000){
                    $isSouthern = true;
                }
            }
        }
        if($isSouthern){
            $northing -= 10000000.0;
        }
        $longOrigin = ($zoneNumber - 1) * 6 - 180 + 3;
        $falseEasting = 500000.0;
        $x = $easting - $falseEasting;
        $eccPrimeSquared = $e2 / (1 - $e2);
        $m = $northing / $k0;
        $mu = $m / ($a * (1 - $e2 / 4 - 3 * $e2 * $e2 / 64 - 5 * $e2 * $e2 * $e2 / 256));
        $phi1Rad = $mu + (3 * $e1 / 2 - 27 * $e1 * $e1 * $e1 / 32) * sin(2 * $mu) + (21 * $e1 * $e1 / 16 - 55 * $e1 * $e1 * $e1 * $e1 / 32) * sin(4 * $mu) + (151 * $e1 * $e1 * $e1 / 96) * sin(6 * $mu);
        $n1 = $a / sqrt(1 - $e2 * sin($phi1Rad) * sin($phi1Rad));
        $t1 = tan($phi1Rad) * tan($phi1Rad);
        $c1 = $eccPrimeSquared * cos($phi1Rad) * cos($phi1Rad);
        $r1 = $a * (1 - $e2) / ((1 - $e2 * sin($phi1Rad) * sin($phi1Rad)) ** 1.5);
        $d = $x / ($n1 * $k0);
        $tlat = $phi1Rad - ($n1 * tan($phi1Rad) / $r1) * ($d * $d / 2 - (5 + 3 * $t1 + 10 * $c1 - 4 * $c1 * $c1 - 9 * $eccPrimeSquared) * $d * $d * $d * $d / 24 + (61 + 90 * $t1 + 298 * $c1 + 45 * $t1 * $t1 - 252 * $eccPrimeSquared - 3 * $c1 * $c1) * $d * $d * $d * $d * $d * $d / 720);
        $tlong = ($d - (1 + 2 * $t1 + $c1) * $d * $d * $d / 6 + (5 - 2 * $c1 + 28 * $t1 - 3 * $c1 * $c1 + 8 * $eccPrimeSquared + 24 * $t1 * $t1) * $d * $d * $d * $d * $d / 120) / cos($phi1Rad);
        $returnLat = rad2deg($tlat);
        $returnLong = $longOrigin + rad2deg($tlong);
        return array($returnLat, $returnLong);
    }

    public static function convertUtmToLL($easting, $northing, $zone, $datum): array
    {
        $retArr = array();
        if($easting && $northing && $zone){
            if(preg_match('/nad\s*83/i', $datum)){
                $datumVal = 'GRS 1980';
            }
            elseif(preg_match('/nad\s*27/i', $datum)){
                $datumVal = 'Clarke 1866';
            }
            else{
                $datumVal = 'WGS 84';
            }
            $a = self::$ellipsoid[$datumVal][1];
            $e2 = self::$ellipsoid[$datumVal][1];
            $latLongArr = self::convertTMtoLL($easting, $northing, $zone, $a, $e2);
            $lat = count($latLongArr) === 2 ? $latLongArr[0] : null;
            $lng = count($latLongArr) === 2 ? $latLongArr[1] : null;
            if($lat && $lng){
                $retArr['lat'] = round($lat,6);
                $retArr['lng'] = round($lng,6);
            }
        }
        return $retArr;
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
        if(preg_match('/\d{2}:\d{2}:\d{2}/', $dateStr,$match)){
            $t = $match[0];
        }
        if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $dateStr,$match)){
            $y = $match[1];
            $m = $match[2];
            $d = $match[3];
        }
        elseif(preg_match('/^(\d{4})-(\d{1,2})/', $dateStr,$match)){
            $y = $match[1];
            $m = $match[2];
        }
        elseif(preg_match('/^([\d-]{1,5})\.([IVX]{1,4})\.(\d{2,4})/i', $dateStr,$match)){
            $d = $match[1];
            $mStr = strtoupper($match[2]);
            $y = $match[3];
            if(array_key_exists($mStr, self::$monthRoman)){
                $m = self::$monthRoman[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})[\s\/-](\D{3,})\.*[\s\/-](\d{2,4})/', $dateStr,$match)){
            $d = $match[1];
            $mStr = $match[2];
            $y = $match[3];
            $mStr = strtolower(substr($mStr,0,3));
            if(array_key_exists($mStr, self::$monthNames)){
                $m = self::$monthNames[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})/', $dateStr,$match)){
            $m = $match[1];
            $d = $match[2];
            $y = $match[3];
        }
        elseif(preg_match('/^(\D{3,})\.*\s?(\d{1,2})[,\s]+([1,2][0,5-9]\d{2})$/', $dateStr,$match)){
            $mStr = $match[1];
            $d = $match[2];
            $y = $match[3];
            $mStr = strtolower(substr($mStr,0,3));
            if(array_key_exists($mStr, self::$monthNames)) {
                $m = self::$monthNames[$mStr];
            }
        }
        elseif(preg_match('/^(\d{1,2})-(\d{1,2})-(\d{2,4})/', $dateStr,$match)){
            $m = $match[1];
            $d = $match[2];
            $y = $match[3];
        }
        elseif(preg_match('/^(\D{3,})\.*\s+([1,2][0,5-9]\d{2})/', $dateStr,$match)){
            $mStr = strtolower(substr($match[1],0,3));
            if(array_key_exists($mStr, self::$monthNames)){
                $m = self::$monthNames[$mStr];
            }
            $y = $match[2];
        }
        elseif(preg_match('/([1,2][0,5-9]\d{2})/', $dateStr,$match)){
            $y = $match[1];
        }
        if($y && strlen($y) === 4){
            if(strlen($m) === 1) {
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
            elseif((int)$d === 30 && (int)$m === 2){
                $d = '00';
            }
            elseif((int)$d === 31 && ((int)$m === 4 || (int)$m === 6 || (int)$m === 9 || (int)$m === 11)){
                $d = '00';
            }
            $retDate = $y.'-'.$m.'-'.$d;
        }
        elseif($timestamp = strtotime($retDate)){
            $retDate = date('Y-m-d', $timestamp);
        }
        if($t){
            $retDate .= ' ' . $t;
        }
        return $retDate;
    }

    public static function parseVerbatimCoordinates($inStr, $target = null){
        $retArr = array();
        if(!strpos($inStr,' to ') && !strpos($inStr,' betw ')){
            $search = array(chr(145), chr(146), chr(147), chr(148), chr(149), chr(150), chr(151));
            $replace = array("'", "'", '"', '"', '*', '-', '-');
            $inStr= str_replace($search, $replace, $inStr);
            $latSec = 0;
            $latNS = 'N';
            $lngSec = 0;
            $lngEW = 'W';
            if(!$target || $target === 'LL'){
                if(preg_match('/([\sNSns]?)(-?\d{1,2}\.\d+)\D?\s?([NSns]?)\D?([\sEWew])(-?\d{1,4}\.\d+)\D?\s?([EWew]?)\D*/', $inStr,$m)){
                    $retArr['lat'] = $m[2];
                    $retArr['lng'] = $m[5];
                    $latDir = $m[3];
                    if(!$latDir && $m[1]) {
                        $latDir = trim($m[1]);
                    }
                    if($retArr['lat'] > 0 && ($latDir === 'S' || $latDir === 's')) {
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
                elseif(preg_match('/(\d{1,2})\D{1,3}\s{0,2}(\d{1,2}\.?\d*)\'(.*)/', $inStr,$m)){
                    $latDeg = $m[1];
                    $latMin = $m[2];
                    $leftOver = str_replace("''",'"', trim($m[3]));
                    if(stripos($inStr,'N') === false && strpos($inStr,'S') !== false){
                        $latNS = 'S';
                    }
                    if(stripos($inStr,'W') === false && stripos($inStr,'E') !== false){
                        $lngEW = 'E';
                    }
                    if(preg_match('/^(\d{1,2}\.?\d*)"(.*)/',$leftOver,$m)){
                        $latSec = $m[1];
                        if(count($m)>2){
                            $leftOver = trim($m[2]);
                        }
                    }
                    if(preg_match('/(\d{1,3})\D{1,3}\s{0,2}(\d{1,2}\.?\d*)\'(.*)/',$leftOver,$m)){
                        $lngDeg = $m[1];
                        $lngMin = $m[2];
                        $leftOver = trim($m[3]);
                        if(preg_match('/^(\d{1,2}\.?\d*)"(.*)/',$leftOver,$m)){
                            $lngSec = $m[1];
                        }
                        if(is_numeric($latDeg) && is_numeric($latMin) && is_numeric($lngDeg) && is_numeric($lngMin) && $latDeg < 90 && $latMin < 60 && $lngDeg < 180 && $lngMin < 60) {
                            $latDec = $latDeg + ($latMin / 60) + ($latSec / 3600);
                            $lngDec = $lngDeg + ($lngMin / 60) + ($lngSec / 3600);
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
                if(preg_match('/NAD\s*27/i', $inStr)) {
                    $d = 'NAD27';
                }
                if(preg_match('/\D*(\d{1,2}\D?)\s+(\d{6,7})m?E\s+(\d{7})m?N/i', $inStr,$m)){
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
                elseif(false !== strpos($inStr, 'UTM') || preg_match('/\d{1,2}[\D\s]+\d{6,7}[\D\s]+\d{6,7}/', $inStr)){
                    $z = '';
                    $e = '';
                    $n = '';
                    if(preg_match('/^(\d{1,2}\D?)[\s\D]+/', $inStr,$m)) {
                        $z = $m[1];
                    }
                    if(!$z && preg_match('/[\s\D]+(\d{1,2}\D?)$/', $inStr,$m)) {
                        $z = $m[1];
                    }
                    if(!$z && preg_match('/[\s\D]+(\d{1,2}\D?)[\s\D]+/', $inStr,$m)) {
                        $z = $m[1];
                    }
                    if($z){
                        if(preg_match('/(\d{6,7})m?E[\D\s]+(\d{7})m?N/i', $inStr,$m)){
                            $e = $m[1];
                            $n = $m[2];
                        }
                        elseif(preg_match('/m?E(\d{6,7})[\D\s]+m?N(\d{7})/i', $inStr,$m)){
                            $e = $m[1];
                            $n = $m[2];
                        }
                        elseif(preg_match('/(\d{7})m?N[\D\s]+(\d{6,7})m?E/i', $inStr,$m)){
                            $e = $m[2];
                            $n = $m[1];
                        }
                        elseif(preg_match('/m?N(\d{7})[\D\s]+m?E(\d{6,7})/i', $inStr,$m)){
                            $e = $m[2];
                            $n = $m[1];
                        }
                        elseif(preg_match('/(\d{6})[\D\s]+(\d{7})/', $inStr,$m)){
                            $e = $m[1];
                            $n = $m[2];
                        }
                        elseif(preg_match('/(\d{7})[\D\s]+(\d{6})/', $inStr,$m)){
                            $e = $m[2];
                            $n = $m[1];
                        }
                        if($e && $n){
                            $llArr = self::convertUtmToLL($e, $n, $z, $d);
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
                if($retArr['lat'] < -90 || $retArr['lat'] > 90 || $retArr['lng'] < -180 || $retArr['lng'] > 180) {
                    $retArr = array();
                }
            }
        }
        return $retArr;
    }

    public static function parseVerbatimElevation($inStr): array
    {
        $retArr = array();
        $search = array(chr(145), chr(146), chr(147), chr(148), chr(149), chr(150), chr(151));
        $replace = array("'", "'", '"', '"', '*', '-', '-');
        $inStr= str_replace($search, $replace, $inStr);
        if(preg_match('/([.\d]+)\s*-\s*([.\d]+)\s*meter/i', $inStr,$m)){
            $retArr['minelev'] = $m[1];
            $retArr['maxelev'] = $m[2];
        }
        elseif(preg_match('/([.\d]+)\s*-\s*([.\d]+)\s*m./i', $inStr,$m)){
            $retArr['minelev'] = $m[1];
            $retArr['maxelev'] = $m[2];
        }
        elseif(preg_match('/([.\d]+)\s*-\s*([.\d]+)\s*m$/i', $inStr,$m)){
            $retArr['minelev'] = $m[1];
            $retArr['maxelev'] = $m[2];
        }
        elseif(preg_match('/([.\d]+)\s*meter/i', $inStr,$m)){
            $retArr['minelev'] = $m[1];
        }
        elseif(preg_match('/([.\d]+)\s*m./i', $inStr,$m)){
            $retArr['minelev'] = $m[1];
        }
        elseif(preg_match('/([.\d]+)\s*m$/i', $inStr,$m)){
            $retArr['minelev'] = $m[1];
        }
        elseif(preg_match('/([.\d]+)[fet\']{,4}\s*-\s*([.\d]+)\s?[f\']/i', $inStr,$m)){
            $retArr['minelev'] = round($m[1] * .3048);
            $retArr['maxelev'] = round($m[2] * .3048);
        }
        elseif(preg_match('/([.\d]+)\s*[f\']/i', $inStr,$m)){
            $retArr['minelev'] = round($m[1] * .3048);
        }
        if($retArr){
            if(array_key_exists('minelev', $retArr) && ($retArr['minelev'] > 8000 || $retArr['minelev'] < 0)) {
                unset($retArr['minelev']);
            }
            if(array_key_exists('maxelev', $retArr) && ($retArr['maxelev'] > 8000 || $retArr['maxelev'] < 0)) {
                unset($retArr['maxelev']);
            }
        }
        return $retArr;
    }
}
