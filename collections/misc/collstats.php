<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 1200);

$catId = array_key_exists('catid',$_REQUEST)?(int)$_REQUEST['catid']:0;
if(!$catId && isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID']) {
    $catId = (int)$GLOBALS['DEFAULTCATID'];
}
$collId = array_key_exists('collid',$_REQUEST)?htmlspecialchars($_REQUEST['collid']):'';
$cPartentTaxon = array_key_exists('taxon',$_REQUEST)?htmlspecialchars($_REQUEST['taxon']):'';
$cCountry = array_key_exists('country',$_REQUEST)?htmlspecialchars($_REQUEST['country']):'';
$days = array_key_exists('days',$_REQUEST)?(int)$_REQUEST['days']:365;
$months = array_key_exists('months',$_REQUEST)?(int)$_REQUEST['months']:12;
$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']):'';

$collManager = new OccurrenceCollectionProfile();

$collList = $collManager->getStatCollectionList($catId);
$specArr = ($collList['spec'] ?? null);
$obsArr = ($collList['obs'] ?? null);

$collIdArr = array();
$collectionArr = array();
$familyArr = array();
$countryArr = array();
$results = array();
$collStr = '';
$resultsTemp = array();

if($collId){
	if(is_numeric($collId)){
        $collIdArr[] = (int)$collId;
    }
	elseif(strpos($collId, ',') !== false){
        $collIdArr = explode(',',$collId);
    }
    if($collIdArr){
        if($action === 'Run Statistics'){
            if(!$cPartentTaxon && !$cCountry){
                $resultsTemp = $collManager->runStatistics($collId);
                $results['FamilyCount'] = $resultsTemp['familycnt'];
                $results['GeneraCount'] = $resultsTemp['genuscnt'];
                $results['SpeciesCount'] = $resultsTemp['speciescnt'];
                $results['TotalTaxaCount'] = $resultsTemp['TotalTaxaCount'];
                $results['TotalImageCount'] = $resultsTemp['TotalImageCount'];
                unset($resultsTemp['familycnt'], $resultsTemp['genuscnt'], $resultsTemp['speciescnt'], $resultsTemp['TotalTaxaCount'], $resultsTemp['TotalImageCount']);
                ksort($resultsTemp, SORT_STRING | SORT_FLAG_CASE);
                $c = 0;
                foreach($resultsTemp as $i => $collArr){
                    $dynPropTempArr = array();
                    $familyTempArr = array();
                    $countryTempArr = array();
                    if($c>0) {
                        $collStr .= ', ';
                    }
                    $collStr .= $collArr['CollectionName'];
                    if(array_key_exists('SpecimenCount',$results)){
                        $results['SpecimenCount'] += $collArr['recordcnt'];
                    }
                    else{
                        $results['SpecimenCount'] = $collArr['recordcnt'];
                    }

                    if(array_key_exists('GeorefCount',$results)){
                        $results['GeorefCount'] += $collArr['georefcnt'];
                    }
                    else{
                        $results['GeorefCount'] = $collArr['georefcnt'];
                    }

                    if($collArr['dynamicProperties']){
                        $dynPropTempArr = json_decode($collArr['dynamicProperties'], true);
                        if(is_array($dynPropTempArr)){
                            $resultsTemp[$i]['speciesID'] = $dynPropTempArr['SpecimensCountID'];
                            $resultsTemp[$i]['types'] = $dynPropTempArr['TypeCount'];

                            if(array_key_exists('SpecimensCountID',$results)){
                                $results['SpecimensCountID'] += $dynPropTempArr['SpecimensCountID'];
                            }
                            else{
                                $results['SpecimensCountID'] = $dynPropTempArr['SpecimensCountID'];
                            }

                            if(array_key_exists('TypeCount',$results)){
                                $results['TypeCount'] += $dynPropTempArr['TypeCount'];
                            }
                            else{
                                $results['TypeCount'] = $dynPropTempArr['TypeCount'];
                            }

                            if(array_key_exists('families',$dynPropTempArr)){
                                $familyTempArr = $dynPropTempArr['families'];
                                foreach($familyTempArr as $k => $famArr){
                                    if(array_key_exists($k,$familyArr)){
                                        $familyArr[$k]['SpecimensPerFamily'] += $famArr['SpecimensPerFamily'];
                                        $familyArr[$k]['GeorefSpecimensPerFamily'] += $famArr['GeorefSpecimensPerFamily'];
                                        $familyArr[$k]['IDSpecimensPerFamily'] += $famArr['IDSpecimensPerFamily'];
                                        $familyArr[$k]['IDGeorefSpecimensPerFamily'] += $famArr['IDGeorefSpecimensPerFamily'];
                                    }
                                    else{
                                        $familyArr[$k]['SpecimensPerFamily'] = $famArr['SpecimensPerFamily'];
                                        $familyArr[$k]['GeorefSpecimensPerFamily'] = $famArr['GeorefSpecimensPerFamily'];
                                        $familyArr[$k]['IDSpecimensPerFamily'] = $famArr['IDSpecimensPerFamily'];
                                        $familyArr[$k]['IDGeorefSpecimensPerFamily'] = $famArr['IDGeorefSpecimensPerFamily'];
                                    }
                                }
                                ksort($familyArr, SORT_STRING | SORT_FLAG_CASE);
                            }

                            if(array_key_exists('countries',$dynPropTempArr)){
                                $countryTempArr = $dynPropTempArr['countries'];
                                foreach($countryTempArr as $k => $countArr){
                                    if(array_key_exists($k,$countryArr)){
                                        $countryArr[$k]['CountryCount'] += $countArr['CountryCount'];
                                        $countryArr[$k]['GeorefSpecimensPerCountry'] += $countArr['GeorefSpecimensPerCountry'];
                                        $countryArr[$k]['IDSpecimensPerCountry'] += $countArr['IDSpecimensPerCountry'];
                                        $countryArr[$k]['IDGeorefSpecimensPerCountry'] += $countArr['IDGeorefSpecimensPerCountry'];
                                    }
                                    else{
                                        $countryArr[$k]['CountryCount'] = $countArr['CountryCount'];
                                        $countryArr[$k]['GeorefSpecimensPerCountry'] = $countArr['GeorefSpecimensPerCountry'];
                                        $countryArr[$k]['IDSpecimensPerCountry'] = $countArr['IDSpecimensPerCountry'];
                                        $countryArr[$k]['IDGeorefSpecimensPerCountry'] = $countArr['IDGeorefSpecimensPerCountry'];
                                    }
                                }
                                ksort($countryArr, SORT_STRING | SORT_FLAG_CASE);
                            }
                        }
                    }
                    $c++;
                }
                $results['SpecimensNullLatitude'] = $results['SpecimenCount'] - $results['GeorefCount'];
            }
            elseif($cPartentTaxon || $cCountry){
                $resultsTemp = $collManager->runStatisticsQuery($collId,$cPartentTaxon,$cCountry);
                $familyArr = $resultsTemp['families'];
                ksort($familyArr, SORT_STRING | SORT_FLAG_CASE);
                $countryArr = $resultsTemp['countries'];
                ksort($countryArr, SORT_STRING | SORT_FLAG_CASE);
                unset($resultsTemp['families'], $resultsTemp['countries']);
                ksort($resultsTemp, SORT_STRING | SORT_FLAG_CASE);
                $c = 0;
                foreach($resultsTemp as $k => $collArr){
                    if($c>0) {
                        $collStr .= ', ';
                    }
                    $collStr .= $collArr['CollectionName'];
                    if(array_key_exists('SpecimenCount',$results)){
                        $results['SpecimenCount'] += $collArr['recordcnt'];
                    }
                    else{
                        $results['SpecimenCount'] = $collArr['recordcnt'];
                    }

                    if(array_key_exists('GeorefCount',$results)){
                        $results['GeorefCount'] += $collArr['georefcnt'];
                    }
                    else{
                        $results['GeorefCount'] = $collArr['georefcnt'];
                    }

                    if(array_key_exists('FamilyCount',$results)){
                        $results['FamilyCount'] += $collArr['familycnt'];
                    }
                    else{
                        $results['FamilyCount'] = $collArr['familycnt'];
                    }

                    if(array_key_exists('GeneraCount',$results)){
                        $results['GeneraCount'] += $collArr['genuscnt'];
                    }
                    else{
                        $results['GeneraCount'] = $collArr['genuscnt'];
                    }

                    if(array_key_exists('SpeciesCount',$results)){
                        $results['SpeciesCount'] += $collArr['speciescnt'];
                    }
                    else{
                        $results['SpeciesCount'] = $collArr['speciescnt'];
                    }

                    if(array_key_exists('TotalTaxaCount',$results)){
                        $results['TotalTaxaCount'] += $collArr['TotalTaxaCount'];
                    }
                    else{
                        $results['TotalTaxaCount'] = $collArr['TotalTaxaCount'];
                    }

                    if(array_key_exists('TotalImageCount',$results)){
                        $results['TotalImageCount'] += $collArr['OccurrenceImageCount'];
                    }
                    else{
                        $results['TotalImageCount'] = $collArr['OccurrenceImageCount'];
                    }

                    if(array_key_exists('SpecimensCountID',$results)){
                        $results['SpecimensCountID'] += $collArr['speciesID'];
                    }
                    else{
                        $results['SpecimensCountID'] = $collArr['speciesID'];
                    }

                    if(array_key_exists('TypeCount',$results)){
                        $results['TypeCount'] += $collArr['types'];
                    }
                    else{
                        $results['TypeCount'] = $collArr['types'];
                    }
                    $c++;
                }
                $results['SpecimensNullLatitude'] = $results['SpecimenCount'] - $results['GeorefCount'];
            }
        }
        if($action === 'Update Statistics'){
            $collManager->batchUpdateStatistics($collId);
            echo '<script type="text/javascript">window.location="collstats.php?collid='.$collId.'"</script>';
        }
        $_SESSION['statsFamilyArr'] = $familyArr;
        $_SESSION['statsCountryArr'] = $countryArr;
    }
    else{
        $collId = '';
    }
}
if($action !== 'Update Statistics'){
	?>
	<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Collection Statistics</title>
        <meta name="description" content="Statistics for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/search.term.manager.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                $("#tabs").tabs({<?php echo ($action === 'Run Statistics' ?'active: 1':''); ?>});

                $( "#taxon" )
                    .bind( "keydown", function( event ) {
                        if ( event.keyCode === $.ui.keyCode.TAB &&
                            $( this ).data( "autocomplete" ).menu.active ) {
                            event.preventDefault();
                        }
                    })
                    .autocomplete({
                        source: function( request, response ) {
                            $.getJSON( "../../api/taxa/speciessuggest.php", {
                                term: request.term.split( /,\s*/ ).pop()
                            }, response );
                        },
                        search: function() {
                            const term = this.value.split( /,\s*/ ).pop();
                            if ( term.length < 4 ) {
                                return false;
                            }
                        },
                        focus: function() {
                            return false;
                        },
                        select: function( event, ui ) {
                            const terms = this.value.split( /,\s*/ );
                            terms.pop();
                            terms.push( ui.item.value );
                            this.value = terms.join( ", " );
                            return false;
                        }
                    },{});
            });

            function toggleStatsPerColl(){
                toggleById("statspercollbox");
                toggleById("showstatspercoll");
                toggleById("hidestatspercoll");

                document.getElementById("geodistbox").style.display="none";
                document.getElementById("showgeodist").style.display="block";
                document.getElementById("hidegeodist").style.display="none";
                document.getElementById("famdistbox").style.display="none";
                document.getElementById("showfamdist").style.display="block";
                document.getElementById("hidefamdist").style.display="none";
                return false;
            }

            function toggleFamilyDist(){
                toggleById("famdistbox");
                toggleById("showfamdist");
                toggleById("hidefamdist");

                document.getElementById("geodistbox").style.display="none";
                document.getElementById("showgeodist").style.display="block";
                document.getElementById("hidegeodist").style.display="none";
                document.getElementById("statspercollbox").style.display="none";
                document.getElementById("showstatspercoll").style.display="block";
                document.getElementById("hidestatspercoll").style.display="none";
                return false;
            }

            function toggleGeoDist(){
                toggleById("geodistbox");
                toggleById("showgeodist");
                toggleById("hidegeodist");

                document.getElementById("famdistbox").style.display="none";
                document.getElementById("showfamdist").style.display="block";
                document.getElementById("hidefamdist").style.display="none";
                document.getElementById("statspercollbox").style.display="none";
                document.getElementById("showstatspercoll").style.display="block";
                document.getElementById("hidestatspercoll").style.display="none";
                return false;
            }

            function toggleById(target){
                if(target != null){
                    const obj = document.getElementById(target);
                    if(obj.style.display === "none" || obj.style.display === ""){
                        obj.style.display="block";
                    }
                    else {
                        obj.style.display="none";
                    }
                }
                return false;
            }

            function changeCollForm(f){
                const dbElements = document.getElementsByName("db[]");
                let c = false;
                let collid = "";
                for(let i = 0; i < dbElements.length; i++){
                    const dbElement = dbElements[i];
                    if(dbElement.checked && !isNaN(dbElement.value)){
                        if(c === true) {
                            collid = collid+",";
                        }
                        collid = collid + dbElement.value;
                        c = true;
                    }
                }
                if(c === true){
                    const collobj = document.getElementById("colltxt");
                    collobj.value = collid;
                    document.getElementById("collform").submit();
                }
                else{
                    alert("Please choose at least one collection!");
                    return false;
                }
            }
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="breadcrumbs">
            <a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php'>Home</a> &gt;&gt;
            <a href='collprofiles.php'>Collections</a> &gt;&gt;
            <b>Collection Statistics</b>
        </div>
        <div id="mainContainer" style="padding: 10px 15px 15px;">
            <h3>Select Collections to be Analyzed</h3>
            <div id="tabs" style="margin:0;">
                <ul>
                    <li><a href="#specobsdiv">Collections</a></li>
                    <?php
                    if($action === 'Run Statistics'){
                        echo '<li><a href="#statsdiv">Statistics</a></li>';
                    }
                    ?>
                </ul>

                <div id="specobsdiv">
                    <?php
                    if($specArr || $obsArr){
                        ?>
                        <form name="collections" id="collform" action="collstats.php" method="post" onsubmit="return changeCollForm(this);">
                            <?php
                            if($GLOBALS['SYMB_UID'] && ($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']))){
                                ?>
                                <fieldset style="padding: 10px 10px 10px 25px;">
                                    <legend><b>Record Criteria</b></legend>
                                    <div style="margin:10px;float:left;">
                                        Parent Taxon: <input type="text" id="taxon" size="43" name="taxon" value="<?php echo $cPartentTaxon; ?>" />
                                    </div>
                                    <div style="margin:10px;float:left;">
                                        Country: <input type="text" id="country" size="43" name="country" value="<?php echo $cCountry; ?>" />
                                    </div>
                                </fieldset>
                                <?php
                            }
                            ?>
                            <div style="margin:20px 0 10px 20px;">
                                <input id="dballcb" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" />
                                Select/Deselect all <a href="collprofiles.php">Collections</a>
                            </div>
                            <?php
                            $collArrIndex = 0;
                            if($specArr){
                                $collCnt = 0;
                                if(isset($specArr['cat'])){
                                    $categoryArr = $specArr['cat'];
                                    ?>
                                    <div style="float:right;margin-top:20px;margin-bottom:10px;">
                                        <div>
                                            <input type="submit" name="submitaction" value="Run Statistics" />
                                        </div>
                                        <?php
                                        if($GLOBALS['SYMB_UID'] && $GLOBALS['IS_ADMIN']){
                                            ?>
                                            <div style="clear:both;margin-top:8px;">
                                                <input type="submit" name="submitaction" value="Update Statistics" />
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <table style="float:left;width:80%;">
                                        <?php
                                        $cnt = 0;
                                        foreach($categoryArr as $catid => $catArr){
                                            $name = $catArr['name'];
                                            if($catArr['acronym']) {
                                                $name .= ' (' . $catArr['acronym'] . ')';
                                            }
                                            $catIcon = $catArr['icon'];
                                            unset($catArr['name'], $catArr['acronym'], $catArr['icon']);
                                            $idStr = $collArrIndex.'-'.$catid;
                                            ?>
                                            <tr>
                                                <td style="padding:6px;width:25px;">
                                                    <input id="cat-<?php echo $idStr; ?>-Input" name="cat[]" value="<?php echo $catid; ?>" type="checkbox" onclick="selectAllCat(this,'cat-<?php echo $idStr; ?>')" <?php echo ($collIdArr&&($collIdArr === array_keys($catArr))?'checked':''); ?> />
                                                </td>
                                                <td style="padding:9px 5px;width:10px;">
                                                    <a href="#" onclick="toggleCat('<?php echo $idStr; ?>');return false;">
                                                        <img id="plus-<?php echo $idStr; ?>" src="../../images/plus_sm.png" style="<?php echo ((isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID'] !== $catid)?'':'display:none;') ?>" /><img id="minus-<?php echo $idStr; ?>" src="../../images/minus_sm.png" style="<?php echo (($GLOBALS['DEFAULTCATID'] && $GLOBALS['DEFAULTCATID'] !== $catid)?'display:none;':'') ?>" />
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
                                                <td colspan="3">
                                                    <div id="cat-<?php echo $idStr; ?>" style="<?php echo ((isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID'] !== $catid)?'display:none;':'') ?>margin:10px;padding:10px 20px;border:inset">
                                                        <table>
                                                            <?php
                                                            foreach($catArr as $collid => $collName2){
                                                                ?>
                                                                <tr>
                                                                    <td style="padding:6px;width:25px;">
                                                                        <input name="db[]" value="<?php echo $collid; ?>" type="checkbox" class="cat-<?php echo $idStr; ?>" onclick="unselectCat('cat-<?php echo $idStr; ?>-Input')" <?php echo ($collIdArr && in_array($collid, $collIdArr, true) ?'checked':''); ?> />
                                                                    </td>
                                                                    <td style="padding:6px">
                                                                        <div class="collectiontitle">
                                                                            <a href='collprofiles.php?collid=<?php echo $collid; ?>'>
                                                                                <?php
                                                                                $codeStr = '';
                                                                                if($collName2['instcode']){
                                                                                    $codeStr .= $collName2['instcode'];
                                                                                }
                                                                                if($collName2['collcode']) {
                                                                                    $codeStr .= ($codeStr?'-':'') . $collName2['collcode'];
                                                                                }
                                                                                echo $collName2['collname'].($codeStr?' ('.$codeStr.')':'');
                                                                                ?>
                                                                            </a>
                                                                            <a href='collprofiles.php?collid=<?php echo $collid; ?>'>
                                                                                more info
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                $collCnt++;
                                                            }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $cnt++;
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                if(isset($specArr['coll'])){
                                    $collArr = $specArr['coll'];
                                    ?>
                                    <table style="float:left;width:80%;">
                                        <?php
                                        foreach($collArr as $collid => $cArr){
                                            ?>
                                            <tr>
                                                <td style="padding:6px;width:25px;">
                                                    <input name="db[]" value="<?php echo $collid; ?>" type="checkbox" onclick="uncheckAll();" <?php echo ($collIdArr && in_array($collid, $collIdArr, true) ?'checked':''); ?> />
                                                </td>
                                                <td style="padding:6px">
                                                    <div class="collectiontitle">
                                                        <a href='collprofiles.php?collid=<?php echo $collid; ?>'>
                                                            <?php
                                                            $codeStr = '';
                                                            if($cArr['instcode']){
                                                                $codeStr .= $cArr['instcode'];
                                                            }
                                                            if($cArr['collcode']) {
                                                                $codeStr .= ($codeStr?'-':'') . $cArr['collcode'];
                                                            }
                                                            echo $cArr['collname'].($codeStr?' ('.$codeStr.')':'');
                                                            ?>
                                                        </a>
                                                        <a href='collprofiles.php?collid=<?php echo $collid; ?>'>
                                                            more info
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $collCnt++;
                                        }
                                        ?>
                                    </table>
                                    <div style="float:right;margin-top:20px;margin-bottom:10px;">
                                        <div>
                                            <input type="submit" name="submitaction" value="Run Statistics" />
                                        </div>
                                        <?php
                                        if($GLOBALS['SYMB_UID'] && $GLOBALS['IS_ADMIN']){
                                            ?>
                                            <div style="clear:both;margin-top:8px;">
                                                <input type="submit" name="submitaction" value="Update Statistics" />
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                $collArrIndex++;
                            }
                            if($specArr && $obsArr) {
                                echo '<hr style="clear:both;margin:20px 0;"/>';
                            }
                            if($obsArr){
                                $collCnt = 0;
                                if(isset($obsArr['cat'])){
                                    $categoryArr = $obsArr['cat'];
                                    ?>
                                    <div style="float:right;margin-top:20px;margin-bottom:10px;">
                                        <div>
                                            <input type="submit" name="submitaction" value="Run Statistics" />
                                        </div>
                                        <?php
                                        if($GLOBALS['SYMB_UID'] && $GLOBALS['IS_ADMIN']){
                                            ?>
                                            <div style="clear:both;margin-top:8px;">
                                                <input type="submit" name="submitaction" value="Update Statistics" />
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <table style="float:left;width:80%;">
                                        <?php
                                        $cnt = 0;
                                        foreach($categoryArr as $catid => $catArr){
                                            $name = $catArr['name'];
                                            if($catArr['acronym']) {
                                                $name .= ' (' . $catArr['acronym'] . ')';
                                            }
                                            $catIcon = $catArr['icon'];
                                            unset($catArr['name'], $catArr['acronym'], $catArr['icon']);
                                            $idStr = $collArrIndex.'-'.$catid;
                                            ?>
                                            <tr>
                                                <td style="padding:6px;width:25px;">
                                                    <input id="cat-<?php echo $idStr; ?>-Input" name="cat[]" value="<?php echo $catid; ?>" type="checkbox" onclick="selectAllCat(this,'cat-<?php echo $idStr; ?>')" <?php echo ($collIdArr&&($collIdArr === array_keys($catArr))?'checked':''); ?> />
                                                </td>
                                                <td style="padding:9px 5px;width:10px;">
                                                    <a href="#" onclick="toggleCat('<?php echo $idStr; ?>');return false;">
                                                        <img id="plus-<?php echo $idStr; ?>" src="../../images/plus_sm.png" style="<?php echo ((isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID'] !== $catid)?'':'display:none;') ?>" /><img id="minus-<?php echo $idStr; ?>" src="../../images/minus_sm.png" style="<?php echo (($GLOBALS['DEFAULTCATID'] && $GLOBALS['DEFAULTCATID'] !== $catid)?'display:none;':'') ?>" />
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
                                                <td colspan="3">
                                                    <div id="cat-<?php echo $idStr; ?>" style="<?php echo ((isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID'] !== $catid)?'display:none;':'') ?>margin:10px;padding:10px 20px;border:inset">
                                                        <table>
                                                            <?php
                                                            foreach($catArr as $collid => $collName2){
                                                                ?>
                                                                <tr>
                                                                    <td style="padding:6px;width:25px;">
                                                                        <input name="db[]" value="<?php echo $collid; ?>" type="checkbox" class="cat-<?php echo $idStr; ?>" onclick="unselectCat('cat-<?php echo $idStr; ?>-Input')" <?php echo ($collIdArr&& in_array($collid, $collIdArr, true) ?'checked':''); ?> />
                                                                    </td>
                                                                    <td style="padding:6px">
                                                                        <div class="collectiontitle">
                                                                            <a href = 'collprofiles.php?collid=<?php echo $collid; ?>'>
                                                                                <?php
                                                                                $codeStr = '';
                                                                                if($collName2['instcode']){
                                                                                    $codeStr .= $collName2['instcode'];
                                                                                }
                                                                                if($collName2['collcode']) {
                                                                                    $codeStr .= ($codeStr?'-':'') . $collName2['collcode'];
                                                                                }
                                                                                echo $collName2['collname'].($codeStr?' ('.$codeStr.')':'');
                                                                                ?>
                                                                            </a>
                                                                            <a href = 'collprofiles.php?collid=<?php echo $collid; ?>'>
                                                                                more info
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                $collCnt++;
                                                            }
                                                            ?>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $cnt++;
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                if(isset($obsArr['coll'])){
                                    $collArr = $obsArr['coll'];
                                    ?>
                                    <table style="float:left;width:80%;">
                                        <?php
                                        foreach($collArr as $collid => $cArr){
                                            ?>
                                            <tr>
                                                <td style="padding:6px;width:25px;">
                                                    <input name="db[]" value="<?php echo $collid; ?>" type="checkbox" onclick="uncheckAll();" <?php echo ($collIdArr && in_array($collid, $collIdArr, true) ?'checked':''); ?> />
                                                </td>
                                                <td style="padding:6px">
                                                    <div class="collectiontitle">
                                                        <a href = 'collprofiles.php?collid=<?php echo $collid; ?>'>
                                                            <?php
                                                            $codeStr = '';
                                                            if($cArr['instcode']){
                                                                $codeStr .= $cArr['instcode'];
                                                            }
                                                            if($cArr['collcode']) {
                                                                $codeStr .= ($codeStr?'-':'') . $cArr['collcode'];
                                                            }
                                                            echo $cArr['collname'].($codeStr?' ('.$codeStr.')':'');
                                                            ?>
                                                        </a>
                                                        <a href = 'collprofiles.php?collid=<?php echo $collid; ?>'>
                                                            more info
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $collCnt++;
                                        }
                                        ?>
                                    </table>
                                    <div style="float:right;margin-top:20px;margin-bottom:10px;">
                                        <div>
                                            <input type="submit" name="submitaction" value="Run Statistics" />
                                        </div>
                                        <?php
                                        if($GLOBALS['SYMB_UID'] && $GLOBALS['IS_ADMIN']){
                                            ?>
                                            <div style="clear:both;margin-top:8px;">
                                                <input type="submit" name="submitaction" value="Update Statistics" />
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                $collArrIndex++;
                            }
                            ?>
                            <div style="clear:both;">&nbsp;</div>
                            <input type="hidden" name="collid" id="colltxt" value="" />
                            <input type="hidden" name="days" value="<?php echo $days; ?>" />
                            <input type="hidden" name="months" value="<?php echo $months; ?>" />
                        </form>
                        <?php
                    }
                    else{
                        echo '<div style="margin-top:10px;"><div style="font-weight:bold;">There are currently no collections to analyze.</div></div>';
                    }
                    ?>
                </div>

                <?php
                if($action === 'Run Statistics'){
                    ?>
                    <div id="statsdiv">
                        <div style="min-height:300px;">
                            <div style="height:100%;">
                                <h1>Selected Collection Statistics</h1>
                                <div style="font-weight:bold;margin:10px;">
                                    <div id="colllistlabel"><a href="#" onclick="toggle('colllist');toggle('colllistlabel');">Display List of Collections Analyzed</a></div>
                                    <div id="colllist" style="display:none">
                                        <?php echo $collStr; ?>
                                    </div>
                                </div>
                                <fieldset style="float:left;width:400px;margin-bottom:15px;margin-right:15px;">
                                    <ul style="margin:0;padding-left:10px;">
                                        <?php
                                        echo '<li>';
                                        echo ($results['SpecimenCount']?number_format($results['SpecimenCount']):0). ' occurrence records';
                                        echo '</li>';
                                        echo '<li>';
                                        $percGeo = '';
                                        if($results['SpecimenCount'] && $results['GeorefCount']){
                                            $percGeo = (100* ($results['GeorefCount'] / $results['SpecimenCount']));
                                        }
                                        echo ($results['GeorefCount']?number_format($results['GeorefCount']):0).($percGeo? ' (' .($percGeo>1?round($percGeo):round($percGeo,2)). '%)' :''). ' georeferenced';
                                        echo '</li>';
                                        echo '<li>';
                                        $percImg = '';
                                        if($results['SpecimenCount'] && $results['TotalImageCount']){
                                            $percImg = (100* ($results['TotalImageCount'] / $results['SpecimenCount']));
                                        }
                                        echo ($results['TotalImageCount']?number_format($results['TotalImageCount']):0).($percImg? ' (' .($percImg>1?round($percImg):round($percImg,2)). '%)' :''). ' imaged';
                                        echo '</li>';
                                        echo '<li>';
                                        $percId = '';
                                        if($results['SpecimenCount'] && $results['SpecimensCountID']){
                                            $percId = (100* ($results['SpecimensCountID'] / $results['SpecimenCount']));
                                        }
                                        echo ($results['SpecimensCountID']?number_format($results['SpecimensCountID']):0).($percId? ' (' .($percId>1?round($percId):round($percId,2)). '%)' :''). ' identified to species';
                                        echo '</li>';
                                        echo '<li>';
                                        echo ($results['FamilyCount']?number_format($results['FamilyCount']):0). ' families';
                                        echo '</li>';
                                        echo '<li>';
                                        echo ($results['GeneraCount']?number_format($results['GeneraCount']):0). ' genera';
                                        echo '</li>';
                                        echo '<li>';
                                        echo ($results['SpeciesCount']?number_format($results['SpeciesCount']):0). ' species';
                                        echo '</li>';
                                        echo '<li>';
                                        echo ($results['TotalTaxaCount']?number_format($results['TotalTaxaCount']):0). ' total taxa (including subsp. and var.)';
                                        echo '</li>';
                                        ?>
                                    </ul>
                                    <form name="statscsv" id="statscsv" action="collstatscsv.php" method="post" onsubmit="">
                                        <div style="margin-top:8px;">
                                            <div id="showstatspercoll" style="float:left;display:block;" >
                                                <a href="#" onclick="return toggleStatsPerColl()">Show Statistics per Collection</a>
                                            </div>
                                            <div id="hidestatspercoll" style="float:left;display:none;" >
                                                <a href="#" onclick="return toggleStatsPerColl()">Hide Statistics per Collection</a>
                                            </div>
                                            <div style='float:left;margin-left:6px;width:16px;height:16px;padding:2px;' title="Save CSV">
                                                <input type="hidden" name="collids" id="collids" value='<?php echo $collId; ?>' />
                                                <input type="hidden" name="taxon" value='<?php echo $cPartentTaxon; ?>' />
                                                <input type="hidden" name="country" value='<?php echo $cCountry; ?>' />
                                                <input type="hidden" name="action" id="action" value='Download Stats per Coll' />
                                                <button type="submit">
                                                    <i style="height:15px;width:15px;" class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </fieldset>
                                <div style="">
                                    <fieldset style="width:275px;margin:20px 0 10px 20px;background-color:#FFFFCC;">
                                        <form name="famstatscsv" id="famstatscsv" action="collstatscsv.php" method="post" onsubmit="">
                                            <div class='legend'><b>Extra Statistics</b></div>
                                            <div style="margin-top:8px;">
                                                <div id="showfamdist" style="float:left;display:block;" >
                                                    <a href="#" onclick="return toggleFamilyDist()">Show Family Distribution</a>
                                                </div>
                                                <div id="hidefamdist" style="float:left;display:none;" >
                                                    <a href="#" onclick="return toggleFamilyDist()">Hide Family Distribution</a>
                                                </div>
                                                <div style='float:left;margin-left:6px;width:16px;height:16px;padding:2px;' title="Save CSV">
                                                    <input type="hidden" name="action" value='Download Family Dist' />
                                                    <button type="submit">
                                                        <i style="height:15px;width:15px;" class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <form name="geostatscsv" id="geostatscsv" action="collstatscsv.php" method="post" onsubmit="">
                                            <div style="clear:both;">
                                                <div id="showgeodist" style="float:left;display:block;" >
                                                    <a href="#" onclick="return toggleGeoDist()">Show Geographic Distribution</a>
                                                </div>
                                                <div id="hidegeodist" style="float:left;display:none;" >
                                                    <a href="#" onclick="return toggleGeoDist();">Hide Geographic Distribution</a>
                                                </div>
                                                <div style='float:left;margin-left:6px;width:16px;height:16px;padding:2px;' title="Save CSV">
                                                    <input type="hidden" name="action" value='Download Geo Dist' />
                                                    <button type="submit">
                                                        <i style="height:15px;width:15px;" class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <?php
                                        if(!$cPartentTaxon && !$cCountry){
                                            ?>
                                            <div style="margin-top:25px;">
                                                <form name="orderstats" style="margin-bottom:0;"
                                                      action="collorderstats.php" method="post" target="_blank"
                                                      onsubmit="">
                                                    <input type="hidden" name="collid" id="collid" value='<?php echo $collId; ?>'/>
                                                    <input type="hidden" name="totalcnt" id="totalcnt" value='<?php echo $results['SpecimenCount']; ?>'/>
                                                    <input type="submit" name="action" value="Load Order Distribution"/>
                                                </form>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </fieldset>
                                    <?php
                                    if(!$cPartentTaxon && !$cCountry && $GLOBALS['SYMB_UID'] && ($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']))) {
                                        ?>
                                        <fieldset id="yearstatsbox" style="width:275px;">
                                            <legend><b>Year Stats</b></legend>
                                            <form name="yearstats" style="margin-bottom:0;" action="collyearstats.php" method="post" target="_blank">
                                                <input type="hidden" name="collid" id="collid" value='<?php echo $collId; ?>'/>
                                                <input type="hidden" name="days" value="<?php echo $days; ?>"/>
                                                <input type="hidden" name="months" value="<?php echo $months; ?>"/>
                                                <div style="float:left;">
                                                    Years: <input type="text" id="years" size="5" name="years" value="1" />
                                                </div>
                                                <div style="margin-left:10px;float:left;">
                                                    <input type="submit" name="action" value="Load Stats"/>
                                                </div>
                                            </form>
                                        </fieldset>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div style="clear:both;"> </div>
                            </div>

                            <fieldset id="statspercollbox" style="clear:both;margin-top:15px;width:90%;display:none;">
                                <legend><b>Statistics per Collection</b></legend>
                                <table class="styledtable" style="font-family:Arial,serif;">
                                    <tr>
                                        <th style="text-align:center;">Collection</th>
                                        <th style="text-align:center;">Specimens</th>
                                        <th style="text-align:center;">Georeferenced</th>
                                        <th style="text-align:center;">Imaged</th>
                                        <th style="text-align:center;">Species ID</th>
                                        <th style="text-align:center;">Families</th>
                                        <th style="text-align:center;">Genera</th>
                                        <th style="text-align:center;">Species</th>
                                        <th style="text-align:center;">Total Taxa</th>
                                    </tr>
                                    <?php
                                    foreach($resultsTemp as $name => $data){
                                        echo '<tr>';
                                        echo '<td>'.wordwrap($name,40,"<br />\n",true).'</td>';
                                        echo '<td>'.(array_key_exists('recordcnt',$data)?$data['recordcnt']:0).'</td>';
                                        echo '<td>'.(array_key_exists('georefcnt',$data)?$data['georefcnt']:0).'</td>';
                                        echo '<td>'.(array_key_exists('OccurrenceImageCount',$data)?$data['OccurrenceImageCount']:0).'</td>';
                                        echo '<td>'.(array_key_exists('speciesID',$data)?$data['speciesID']:0).'</td>';
                                        echo '<td>'.(array_key_exists('familycnt',$data)?$data['familycnt']:0).'</td>';
                                        echo '<td>'.(array_key_exists('genuscnt',$data)?$data['genuscnt']:0).'</td>';
                                        echo '<td>'.(array_key_exists('speciescnt',$data)?$data['speciescnt']:0).'</td>';
                                        echo '<td>'.(array_key_exists('TotalTaxaCount',$data)?$data['TotalTaxaCount']:0).'</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </table>
                            </fieldset>
                            <fieldset id="famdistbox" style="clear:both;margin-top:15px;width:800px;display:none;">
                                <legend><b>Family Distribution</b></legend>
                                <table class="styledtable" style="font-family:Arial,serif;width:780px;">
                                    <tr>
                                        <th style="text-align:center;">Family</th>
                                        <th style="text-align:center;">Specimens</th>
                                        <th style="text-align:center;">Georeferenced</th>
                                        <th style="text-align:center;">Species ID</th>
                                        <th style="text-align:center;">Georeferenced<br />and<br />Species ID</th>
                                    </tr>
                                    <?php
                                    $total = 0;
                                    foreach($familyArr as $name => $data){
                                        echo '<tr>';
                                        echo '<td>'.wordwrap($name,52,"<br />\n",true).'</td>';
                                        echo '<td>';
                                        if(count($resultsTemp) === 1){
                                            $starrStr = '{"db":"'.$collid.'","usethes":true,"taxa":"'.$name.'"}';
                                            echo "<a href='../list.php?starr=".$starrStr."' target='_blank'>";
                                        }
                                        echo number_format($data['SpecimensPerFamily']);
                                        if(count($resultsTemp) === 1){
                                            echo '</a>';
                                        }
                                        echo '</td>';
                                        echo '<td>'.($data['GeorefSpecimensPerFamily']?round(100*($data['GeorefSpecimensPerFamily']/$data['SpecimensPerFamily'])):0).'%</td>';
                                        echo '<td>'.($data['IDSpecimensPerFamily']?round(100*($data['IDSpecimensPerFamily']/$data['SpecimensPerFamily'])):0).'%</td>';
                                        echo '<td>'.($data['IDGeorefSpecimensPerFamily']?round(100*($data['IDGeorefSpecimensPerFamily']/$data['SpecimensPerFamily'])):0).'%</td>';
                                        echo '</tr>';
                                        $total += $data['SpecimensPerFamily'];
                                    }
                                    ?>
                                </table>
                                <div style="margin-top:10px;">
                                    <b>Total Occurrences with Family:</b> <?php echo number_format($total); ?><br />
                                    Specimens without Family: <?php echo number_format($results['SpecimenCount']-$total); ?><br />
                                </div>
                            </fieldset>
                            <fieldset id="geodistbox" style="margin-top:15px;width:800px;display:none;">
                                <legend><b>Geographic Distribution</b></legend>
                                <table class="styledtable" style="font-family:Arial,serif;width:780px;">
                                    <tr>
                                        <th style="text-align:center;">Country</th>
                                        <th style="text-align:center;">Specimens</th>
                                        <th style="text-align:center;">Georeferenced</th>
                                        <th style="text-align:center;">Species ID</th>
                                        <th style="text-align:center;">Georeferenced<br />and<br />Species ID</th>
                                    </tr>
                                    <?php
                                    $total = 0;
                                    foreach($countryArr as $name => $data){
                                        echo '<tr>';
                                        echo '<td>'.wordwrap($name,52,"<br />\n",true).'</td>';
                                        echo '<td>';
                                        if(count($resultsTemp) === 1){
                                            $starrStr = '{"db":"'.$collid.'","country":"'.$name.'"}';
                                            echo "<a href='../list.php?starr=".$starrStr."' target='_blank'>";
                                        }
                                        echo number_format($data['CountryCount']);
                                        if(count($resultsTemp) === 1){
                                            echo '</a>';
                                        }
                                        echo '</td>';
                                        echo '<td>'.($data['GeorefSpecimensPerCountry']?round(100*($data['GeorefSpecimensPerCountry']/$data['CountryCount'])):0).'%</td>';
                                        echo '<td>'.($data['IDSpecimensPerCountry']?round(100*($data['IDSpecimensPerCountry']/$data['CountryCount'])):0).'%</td>';
                                        echo '<td>'.($data['IDGeorefSpecimensPerCountry']?round(100*($data['IDGeorefSpecimensPerCountry']/$data['CountryCount'])):0).'%</td>';
                                        echo '</tr>';
                                        $total += $data['CountryCount'];
                                    }
                                    ?>
                                </table>
                                <div style="margin-top:10px;">
                                    <b>Total Occurrences with Country:</b> <?php echo number_format($total); ?><br />
                                    Specimens without Country or Georeferencing: <?php echo number_format(($results['SpecimenCount']-$total)+$results['SpecimensNullLatitude']); ?><br />
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
    </body>
	</html>
	<?php
}
?>
