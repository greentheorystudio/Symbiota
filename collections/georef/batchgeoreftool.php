<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceGeorefTools.php');
include_once(__DIR__ . '/../../services/SOLRService.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$submitAction = array_key_exists('submitaction',$_POST)?htmlspecialchars($_POST['submitaction']):'';

$qCountry = array_key_exists('qcountry',$_POST)?$_POST['qcountry']:'';
$qState = array_key_exists('qstate',$_POST)?$_POST['qstate']:'';
$qCounty = array_key_exists('qcounty',$_POST)?$_POST['qcounty']:'';
$qMunicipality = array_key_exists('qmunicipality',$_POST)?$_POST['qmunicipality']:'';
$qLocality = array_key_exists('qlocality',$_POST)?$_POST['qlocality']:'';
$qDisplayAll = array_key_exists('qdisplayall',$_POST)?(int)$_POST['qdisplayall']:0;
$qVStatus = array_key_exists('qvstatus',$_POST)?$_POST['qvstatus']:'';
$qSciname = array_key_exists('qsciname',$_POST)?$_POST['qsciname']:'';
$qProcessingStatus = array_key_exists('qprocessingstatus',$_POST)?$_POST['qprocessingstatus']:'';

$latDeg = array_key_exists('latdeg',$_POST)?$_POST['latdeg']:'';
$latMin = array_key_exists('latmin',$_POST)?$_POST['latmin']:'';
$latSec = array_key_exists('latsec',$_POST)?$_POST['latsec']:'';
$decimalLatitude = array_key_exists('decimallatitude',$_POST)?$_POST['decimallatitude']:'';
$latNS = array_key_exists('latns',$_POST)?$_POST['latns']:'';

$lngDeg = array_key_exists('lngdeg',$_POST)?$_POST['lngdeg']:'';
$lngMin = array_key_exists('lngmin',$_POST)?$_POST['lngmin']:'';
$lngSec = array_key_exists('lngsec',$_POST)?$_POST['lngsec']:'';
$decimalLongitude = array_key_exists('decimallongitude',$_POST)?$_POST['decimallongitude']:'';
$lngEW = array_key_exists('lngew',$_POST)?$_POST['lngew']:'';

$coordinateUncertaintyInMeters = array_key_exists('coordinateuncertaintyinmeters',$_POST)?$_POST['coordinateuncertaintyinmeters']:'';
$geodeticDatum = array_key_exists('geodeticdatum',$_POST)?$_POST['geodeticdatum']:'';
$georeferenceSources = array_key_exists('georeferencesources',$_POST)?$_POST['georeferencesources']:'';
$georeferenceRemarks = array_key_exists('georeferenceremarks',$_POST)?$_POST['georeferenceremarks']:'';
$footprintWKT = array_key_exists('footprintwkt',$_POST)?$_POST['footprintwkt']:'';
$georeferenceVerificationStatus = array_key_exists('georeferenceverificationstatus',$_POST)?$_POST['georeferenceverificationstatus']:'';
$minimumElevationInMeters = array_key_exists('minimumelevationinmeters',$_POST)?$_POST['minimumelevationinmeters']:'';
$maximumElevationInMeters = array_key_exists('maximumelevationinmeters',$_POST)?$_POST['maximumelevationinmeters']:'';
$minimumElevationInFeet = array_key_exists('minimumelevationinfeet',$_POST)?$_POST['minimumelevationinfeet']:'';
$maximumElevationInFeet = array_key_exists('maximumelevationinfeet',$_POST)?$_POST['maximumelevationinfeet']:'';

if(!$georeferenceSources) {
    $georeferenceSources = 'georef batch tool ' . date('Y-m-d');
}
if(!$georeferenceVerificationStatus) {
    $georeferenceVerificationStatus = 'reviewed - high confidence';
}

$geoManager = new OccurrenceGeorefTools();
$solrManager = new SOLRService();
$geoManager->setCollId($collId);

$editor = false;
if($GLOBALS['IS_ADMIN']
	|| (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))
	|| (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
 	$editor = true;
}

$statusStr = '';
if($editor && $submitAction){
	if($qCountry) {
        $geoManager->setQueryVariables('qcountry', $qCountry);
    }
	if($qState) {
        $geoManager->setQueryVariables('qstate', $qState);
    }
	if($qCounty) {
        $geoManager->setQueryVariables('qcounty', $qCounty);
    }
	if($qMunicipality) {
        $geoManager->setQueryVariables('qmunicipality', $qMunicipality);
    }
	if($qSciname) {
        $geoManager->setQueryVariables('qsciname', $qSciname);
    }
	if($qDisplayAll) {
        $geoManager->setQueryVariables('qdisplayall', $qDisplayAll);
    }
	if($qVStatus) {
        $geoManager->setQueryVariables('qvstatus', $qVStatus);
    }
	if($qLocality) {
        $geoManager->setQueryVariables('qlocality', $qLocality);
    }
	if($qProcessingStatus) {
        $geoManager->setQueryVariables('qprocessingstatus', $qProcessingStatus);
    }
	if($submitAction === 'Update Coordinates'){
		$geoManager->updateCoordinates($_POST);
        if($GLOBALS['SOLR_MODE']) {
            $solrManager->updateSOLR();
        }
	}
	$localArr = $geoManager->getLocalityArr();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Batch Georeference Occurrences</title>
    <meta name="description" content="Batch georeference occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/collections.georef.batchgeoreftool.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script type="text/javascript">
        function openSpatialInputWindow(type) {
            let mapWindow = open("../../spatial/index.php?windowtype=" + type,"input","resizable=0,width=900,height=700,left=100,top=20");
            if (mapWindow.opener == null) {
                mapWindow.opener = self;
            }
            mapWindow.addEventListener('blur', function(){
                mapWindow.close();
                mapWindow = null;
            });
        }
    </script>
</head>
<body>
    <div id="mainContainer" style="padding: 10px 15px 15px;">
        <div style="float:left;">
            <div style="font-weight:bold;margin-top:6px;">
                <?php echo $geoManager->getCollName(); ?>
            </div>
            <div id="breadcrumbs" style="margin:10px;">
                <a href='../../index.php'>Home</a> &gt;&gt;
                <a href='../misc/collprofiles.php?emode=1&collid=<?php echo $collId; ?>'>Collection Control Panel</a> &gt;&gt;
                <b>Batch Georeference Occurrences</b>
            </div>
            <?php
            if($statusStr){
                ?>
                <div style='margin:20px;font-weight:bold;color:red;'>
                    <?php echo $statusStr; ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
        if($collId){
            if($editor){
                ?>
                <div style="float:right;">
                    <form name="queryform" method="post" action="batchgeoreftool.php" onsubmit="return verifyQueryForm()">
                        <fieldset style="padding:5px;width:600px;background-color:lightyellow;">
                            <legend><b>Query Form</b></legend>
                            <div style="height:20px;">
                                <div style="clear:both;">
                                    <div style="float:left;margin-right:10px;">
                                        <select name="qcountry" style="width:150px;">
                                            <option value=''>All Countries</option>
                                            <option value=''>--------------------</option>
                                            <?php
                                            $cArr = $geoManager->getCountryArr();
                                            foreach($cArr as $c){
                                                echo '<option '.($qCountry === $c?'SELECTED':'').'>'.$c.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div style="float:left;margin-right:10px;">
                                        <select name="qstate" style="width:150px;">
                                            <option value=''>All States</option>
                                            <option value=''>--------------------</option>
                                            <?php
                                            $sArr = $geoManager->getStateArr();
                                            foreach($sArr as $s){
                                                echo '<option '.($qState === $s?'SELECTED':'').'>'.$s.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div style="float:left;margin-right:10px;">
                                        <select name="qcounty" style="width:180px;">
                                            <option value=''>All Counties</option>
                                            <option value=''>--------------------</option>
                                            <?php
                                            $coArr = $geoManager->getCountyArr($qState);
                                            foreach($coArr as $c){
                                                echo '<option '.($qCounty === $c?'SELECTED':'').'>'.$c.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div style="clear:both;margin-top:5px;">
                                    <div style="float:left;margin-right:10px;">
                                        <select name="qmunicipality" style="width:180px;">
                                            <option value=''>All Municipalities</option>
                                            <option value=''>--------------------</option>
                                            <?php
                                            $muArr = $geoManager->getMunicipalityArr($qState);
                                            foreach($muArr as $m){
                                                echo '<option '.($qMunicipality === $m?'SELECTED':'').'>'.$m.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div style="float:left;margin-right:10px;">
                                        <select name="qprocessingstatus">
                                            <option value="">All Processing Status</option>
                                            <option value="">-----------------------</option>
                                            <?php
                                            $processingStatus = $geoManager->getProcessingStatus();
                                            foreach($processingStatus as $pStatus){
                                                echo '<option '.($qProcessingStatus === $pStatus?'SELECTED':'').'>'.$pStatus.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div style="float:left;">
                                        <i style="height:15px;width:15px;color:green;" onclick="toggle('advfilterdiv')" title="Advanced Options" class="fas fa-plus"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="advfilterdiv" style="clear:both;margin-top:5px;display:<?php echo ($qSciname || $qVStatus || $qDisplayAll?'block':'none'); ?>;">
                                <div style="float:left;margin-right:15px;">
                                    <b>Verification status:</b>
                                    <input id="qvstatus" name="qvstatus" type="text" value="<?php echo $qVStatus; ?>" style="width:175px;" />
                                </div>
                                <div style="float:left;">
                                    <b>Family/Genus:</b>
                                    <input name="qsciname" type="text" value="<?php echo $qSciname; ?>" style="width:150px;" />
                                </div>
                                <div style="clear:both;margin-top:5px;">
                                    <input name="qdisplayall" type="checkbox" value="1" <?php echo ($qDisplayAll?'checked':''); ?> />
                                    Including previously georeferenced records
                                </div>
                            </div>
                            <div style="margin-top:5px;clear:both;">
                                <div style="float:right;">
                                    <input name="collid" type="hidden" value="<?php echo $collId; ?>" />
                                    <input name="submitaction" type="submit" value="Generate List" />
                                    <span id="qworkingspan" style="display:none;">
                                        <span class="sm-native-spinner" style="width:12px;height:12px;"></span>
                                    </span>
                                </div>
                                <div style="float:left">
                                    <b>Locality Term:</b>
                                    <input name="qlocality" type="text" value="<?php echo $qLocality; ?>" style="width:250px;" />
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div style="clear:both;">
                    <form name="georefform" method="post" action="batchgeoreftool.php" onsubmit="return verifyGeorefForm(this)">
                        <div style="float:right;">
                            <span style="margin-left:10px;">
                                <a href="#" onclick="geoLocateLocality();"><img src="../../images/geolocate.png" title="GeoLocate locality" style="width:15px;" /></a>
                            </span>
                            <span style="margin-left:10px;">
                                <a href="#" onclick="analyseLocalityStr();"><i style="height:15px;width:15px;" title="Analyse Locality string for embedded Lat/Long or UTM" class="fas fa-search"></i></a>
                            </span>
                            <span style="margin-left:10px;">
                                <a href="#" onclick="openFirstRecSet();"><i style="height:15px;width:15px;" title="Edit first set of records" class="far fa-edit"></i></a>
                            </span>
                        </div>
                        <div style="font-weight:bold;">
                            <?php
                            $localCnt = '---';
                            if(isset($localArr)){
                                $localCnt = count($localArr);
                            }
                            if($localCnt === 1000){
                                $localCnt = '1000 or more';
                            }
                            echo 'Return Count: '.$localCnt;
                            ?>
                        </div>
                        <div style="clear:both;">
                            <select id="locallist" name="locallist[]" size="15" multiple="multiple" style="width:100%">
                                <?php
                                if(isset($localArr)){
                                    if($localArr){
                                        foreach($localArr as $k => $v){
                                            $locStr = '';
                                            if(!$qCountry && $v['country']) {
                                                $locStr = $v['country'] . '; ';
                                            }
                                            if(!$qState && $v['stateprovince']) {
                                                $locStr .= $v['stateprovince'] . '; ';
                                            }
                                            if(!$qCounty && $v['county']) {
                                                $locStr .= $v['county'] . '; ';
                                            }
                                            if(!$qMunicipality && $v['municipality']) {
                                                $locStr .= $v['municipality'] . '; ';
                                            }
                                            if($v['locality']) {
                                                $locStr .= str_replace(';', ',', $v['locality']);
                                            }
                                            if($v['verbatimcoordinates']) {
                                                $locStr .= ', ' . $v['verbatimcoordinates'];
                                            }
                                            if(array_key_exists('decimallatitude',$v) && $v['decimallatitude']){
                                                $locStr .= ' ('.$v['decimallatitude'].', '.$v['decimallongitude'].') ';
                                            }
                                            echo '<option value="'.$v['occid'].'">'.trim($locStr,' ,').' ['.$v['cnt'].']</option>'."\n";
                                        }
                                    }
                                    else{
                                        echo '<option value="">No localities returned matching search term</option>';
                                    }
                                }
                                else{
                                    echo '<option value="">Use query form above to build locality list</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div style="float:right;">
                            <fieldset>
                                <legend><b>Statistics</b></legend>
                                <div style="">
                                    Records to be Georeferenced
                                </div>
                                <div style="margin:5px;">
                                    <?php
                                    $statArr = $geoManager->getCoordStatistics();
                                    echo '<div>Total: '.$statArr['total'].'</div>';
                                    echo '<div>Percentage: '.$statArr['percent'].'%</div>';
                                    ?>
                                </div>
                            </fieldset>
                        </div>
                        <div style="margin:15px;">
                            <table>
                                <tr>
                                    <td></td>
                                    <td><b>Deg.</b></td>
                                    <td style="width:55px;"><b>Min.</b></td>
                                    <td style="width:55px;"><b>Sec.</b></td>
                                    <td style="width:20px;">&nbsp;</td>
                                    <td style="width:15px;">&nbsp;</td>
                                    <td><b>Decimal</b></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:middle"><b>Latitude:</b> </td>
                                    <td><input name="latdeg" type="text" value="" onchange="updateLatDec(this.form)" style="width:30px;" /></td>
                                    <td><input name="latmin" type="text" value="" onchange="updateLatDec(this.form)" style="width:50px;" /></td>
                                    <td><input name="latsec" type="text" value="" onchange="updateLatDec(this.form)" style="width:50px;" /></td>
                                    <td>
                                        <select name="latns" onchange="updateLatDec(this.form)">
                                            <option>N</option>
                                            <option >S</option>
                                        </select>
                                    </td>
                                    <td> = </td>
                                    <td>
                                        <input id="decimallatitude" name="decimallatitude" type="text" style="width:80px;" />
                                        <span style="cursor:pointer;padding:3px;" onclick="openSpatialInputWindow('input-point');">
                                            <i style="height:15px;width:15px;" class="fas fa-globe"></i>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:middle"><b>Longitude:</b> </td>
                                    <td><input name="lngdeg" type="text" value="" onchange="updateLngDec(this.form)" style="width:30px;" /></td>
                                    <td><input name="lngmin" type="text" value="" onchange="updateLngDec(this.form)" style="width:50px;" /></td>
                                    <td><input name="lngsec" type="text" value="" onchange="updateLngDec(this.form)" style="width:50px;" /></td>
                                    <td style="width:20px;">
                                        <select name="lngew" onchange="updateLngDec(this.form)">
                                            <option>E</option>
                                            <option SELECTED>W</option>
                                        </select>
                                    </td>
                                    <td> = </td>
                                    <td><input id="decimallongitude" name="decimallongitude" type="text" value="" style="width:80px;" /></td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align:middle">
                                        <b>Error (in meters):</b>
                                    </td>
                                    <td colspan="2" style="vertical-align:middle">
                                        <input id="coordinateuncertaintyinmeters" name="coordinateuncertaintyinmeters" type="text" value="" style="width:50px;" onchange="verifyCoordUncertainty(this)" />
                                    </td>
                                    <td colspan="2" style="vertical-align:middle">
                                        <span style="margin-left:20px;font-weight:bold;">Datum:</span>
                                        <input id="geodeticdatum" name="geodeticdatum" type="text" value="" style="width:75px;" />
                                        <span style="cursor:pointer;margin-left:3px;" onclick="toggle('utmdiv');">
                                            <i style="height:15px;width:15px;" class="far fa-plus-square"></i>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align:middle">
                                        <b>Footprint WKT:</b>
                                    </td>
                                    <td colspan="4" style="vertical-align:middle">
                                        <input id="footprintwkt" name="footprintwkt" type="text" value="" style="width:500px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <div id="utmdiv" style="display:none;padding:15px 10px;background-color:lightyellow;border:1px solid yellow;width:400px;height:75px;margin-bottom:10px;">
                                            <div>
                                                <div style="margin:3px;float:left;">
                                                    East: <input name="utmeast" type="text" style="width:100px;" />
                                                </div>
                                                <div style="margin:3px;float:left;">
                                                    North: <input name="utmnorth" type="text" style="width:100px;" />
                                                </div>
                                                <div style="margin:3px;float:left;">
                                                    Zone: <input name="utmzone" style="width:40px;" />
                                                </div>
                                            </div>
                                            <div style="clear:both;margin:3px;">
                                                <div style="float:left;">
                                                    Hemisphere:
                                                    <select name="hemisphere" title="Use hemisphere designator (e.g. 12N) rather than grid zone ">
                                                        <option value="Northern">North</option>
                                                        <option value="Southern">South</option>
                                                    </select>
                                                </div>
                                                <div style="margin:5px 0 0 15px;float:left;">
                                                    <input type="button" value="Convert UTM values to lat/long " onclick="insertUtm(this.form)" />
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align:middle">
                                        <b>Sources:</b>
                                    </td>
                                    <td colspan="4">
                                        <input id="georeferencesources" name="georeferencesources" type="text" value="<?php echo $georeferenceSources; ?>" style="width:500px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align:middle">
                                        <b>Remarks:</b>
                                    </td>
                                    <td colspan="4">
                                        <input name="georeferenceremarks" type="text" value="" style="width:500px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align:middle">
                                        <b>Verification Status:</b>
                                    </td>
                                    <td colspan="4">
                                        <input id="georeferenceverificationstatus" name="georeferenceverificationstatus" type="text" value="<?php echo $georeferenceVerificationStatus; ?>" style="width:400px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align:middle">
                                        <b>Elevation:</b>
                                    </td>
                                    <td colspan="4">
                                        <input name="minimumelevationinmeters" type="text" value="" style="width:50px;" /> to
                                        <input name="maximumelevationinmeters" type="text" value="" style="width:50px;" /> meters
                                        <span style="margin-left:80px;">
                                            <input type="text" value="" style="width:50px;" onchange="updateMinElev(this.value)" /> to
                                            <input type="text" value="" style="width:50px;" onchange="updateMaxElev(this.value)" /> feet
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <b>Processing status: </b>
                                    </td>
                                    <td colspan="4">
                                        <select name="processingstatus">
                                            <option value="">Leave as is</option>
                                            <option value="unprocessed">Unprocessed</option>
                                            <option value="unprocessed/NLP">unprocessed/NLP</option>
                                            <option value="stage 1">Stage 1</option>
                                            <option value="stage 2">Stage 2</option>
                                            <option value="stage 3">Stage 3</option>
                                            <option value="pending review-nfn">Pending Review-NfN</option>
                                            <option value="pending review">Pending Review</option>
                                            <option value="expert required">Expert Required</option>
                                            <option value="reviewed">Reviewed</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                        <span style="margin-left:20px;">
                                            Georefer by:
                                            <input name="georeferencedby" type="text" value="<?php echo $GLOBALS['PARAMS_ARR']['un']; ?>" style="width:75px" readonly />
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <input name="submitaction" type="submit" value="Update Coordinates" />
                                        <span id="workingspan" style="display:none;">
                                            <span class="sm-native-spinner" style="width:12px;height:12px;"></span>
                                        </span>
                                        <input name="qcountry" type="hidden" value="<?php echo $qCountry; ?>" />
                                        <input name="qstate" type="hidden" value="<?php echo $qState; ?>" />
                                        <input name="qcounty" type="hidden" value="<?php echo $qCounty; ?>" />
                                        <input name="qmunicipality" type="hidden" value="<?php echo $qMunicipality; ?>" />
                                        <input name="qlocality" type="hidden" value="<?php echo $qLocality; ?>" />
                                        <input name="qsciname" type="hidden" value="<?php echo $qSciname; ?>" />
                                        <input name="qvstatus" type="hidden" value="<?php echo $qVStatus; ?>" />
                                        <input name="qprocessingstatus" type="hidden" value="<?php echo $qProcessingStatus; ?>" />
                                        <input name="qdisplayall" type="hidden" value="<?php echo $qDisplayAll; ?>" />
                                        <input name="collid" type="hidden" value="<?php echo $collId; ?>" />
                                    </td>
                                    <td colspan="4">
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top:15px">Note: Existing data within following georeference fields will be replaced with incoming data.
                            However, elevation data will only be added when the target fields are null.
                            No incoming data will replace existing elevational data.
                            Georeference fields that will be replaced: decimalLatitude, decimalLongitude, coordinateUncertaintyInMeters, geodeticdatum,
                            footprintwkt, georeferencedby, georeferenceRemarks, georeferenceSources, georeferenceVerificationStatus </div>
                        </div>
                    </form>
                </div>
                <?php
            }
            else{
                ?>
                <div style='font-weight:bold;'>
                    ERROR: You do not have permission to edit this collection
                </div>
                <?php
            }
        }
        else{
            ?>
            <div style='font-weight:bold;'>
                ERROR: Collection identifier is null
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
</body>
</html>
