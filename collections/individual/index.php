<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceIndividualManager.php');
include_once(__DIR__ . '/../../classes/DwcArchiverCore.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$occid = array_key_exists('occid',$_REQUEST)?(int)$_REQUEST['occid']:0;
$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$pk = array_key_exists('pk',$_REQUEST)?htmlspecialchars($_REQUEST['pk']): '';
$guid = array_key_exists('guid',$_REQUEST)?htmlspecialchars($_REQUEST['guid']): '';
$submit = array_key_exists('formsubmit',$_REQUEST)?htmlspecialchars($_REQUEST['formsubmit']):'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;
$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$fullWindow = (array_key_exists('fullwindow', $_REQUEST) && $_REQUEST['fullwindow']);
$format = array_key_exists('format',$_REQUEST)?htmlspecialchars($_GET['format']):'';

if($guid && !preg_match('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $guid)) {
    $guid = '';
}
if($pk && !preg_match('/^[a-zA-Z0-9\s_]+$/',$pk)) {
    $pk = '';
}
if($submit && !preg_match('/^[a-zA-Z0-9\s_]+$/',$submit)) {
    $submit = '';
}

$indManager = new OccurrenceIndividualManager();
if($occid){
    $indManager->setOccid($occid);
}
elseif($guid){
    $occid = $indManager->setGuid($guid);
}
elseif($collid && $pk){
    $indManager->setCollid($collid);
    $indManager->setDbpk($pk);
}

$indManager->setDisplayFormat($format);
$occArr = $indManager->getOccData();
if(!$occid) {
    $occid = $indManager->getOccid();
}
$collMetadata = $indManager->getMetadata();
if(!$collid) {
    $collid = $occArr['collid'];
}

$genticArr = $indManager->getGeneticArr();

$statusStr = '';
$displayLocality = false;
$isEditor = false;

if($GLOBALS['SYMB_UID']){
    if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
        $isEditor = true;
    }
    elseif((array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
        $isEditor = true;
    }
    elseif($occArr['observeruid'] === $GLOBALS['SYMB_UID']){
        $isEditor = true;
    }
    elseif($indManager->isTaxonomicEditor()){
        $isEditor = true;
    }

    if($isEditor || array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll',$GLOBALS['USER_RIGHTS'])){
        $displayLocality = true;
    }
    elseif(array_key_exists('RareSppReader',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['RareSppReader'], true)){
        $displayLocality = true;
    }
    elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) || array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])){
        $displayLocality = true;
    }

    if(array_key_exists('delvouch', $_GET) && $occid && !$indManager->deleteVoucher($occid, $_GET['delvouch'])) {
        $statusStr = $indManager->getErrorMessage();
    }
    if(array_key_exists('commentstr',$_POST)){
        if(!$indManager->addComment($_POST['commentstr'])){
            $statusStr = $indManager->getErrorMessage();
        }
    }
    elseif($submit === 'Delete Comment'){
        if(!$indManager->deleteComment($_POST['comid'])){
            $statusStr = $indManager->getErrorMessage();
        }
    }
    elseif(array_key_exists('repcomid',$_GET)){
        if($indManager->reportComment($_GET['repcomid'])){
            $statusStr = 'Comment reported as inappropriate. Comment will remain unavailable to public until reviewed by an administrator.';
        }
        else{
            $statusStr = $indManager->getErrorMessage();
        }
    }
    elseif(array_key_exists('publiccomid',$_GET)){
        if(!$indManager->makeCommentPublic($_GET['publiccomid'])){
            $statusStr = $indManager->getErrorMessage();
        }
    }
    elseif($submit === 'Add Voucher'){
        if(!$indManager->linkVoucher($_POST)){
            $statusStr = $indManager->getErrorMessage();
        }
    }
    elseif($submit === 'Link to Dataset'){
        $dsid = ($_POST['dsid'] ?? 0);
        if(!$indManager->linkToDataset($dsid,$_POST['dsname'],$_POST['notes'])){
            $statusStr = $indManager->getErrorMessage();
        }
    }
}
if(!$occArr['localitysecurity']) {
    $displayLocality = true;
}

$displayMap = false;
if($displayLocality && ((is_numeric($occArr['decimallatitude']) && is_numeric($occArr['decimallongitude'])) || $occArr['footprintwkt'])) {
    $displayMap = true;
}
$dupClusterArr = $indManager->getDuplicateArr();
$commentArr = $indManager->getCommentArr($isEditor);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Detailed Collection Record Information</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta name="description" content="<?php echo 'Occurrence author: '.$occArr['recordedby'].','.$occArr['recordnumber']; ?>" />
    <meta name="keywords" content="<?php echo $occArr['guid']; ?>">
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet">
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet">
    <link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
    <style>
        .map {
            width: 100%;
            height: 600px;
        }
    </style>
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
    <script src="../../js/external/jquery.js" type="text/javascript"></script>
    <script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
    <?php
    if($displayMap){
        ?>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20220209" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20210415" type="text/css" rel="stylesheet" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol/ol.js?ver=20220926" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/shared.js?ver=20221114" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/spatial.module.core.js?ver=20221025" type="text/javascript"></script>
        <?php
    }
    ?>
    <script type="text/javascript">
        let tabIndex = <?php echo $tabIndex; ?>;
        const decimalLatitude = <?php echo $occArr['decimallatitude'] ?: 'null'; ?>;
        const decimalLongitude = <?php echo $occArr['decimallongitude'] ?: 'null'; ?>;
        const coordUncertainty = <?php echo $occArr['coordinateuncertaintyinmeters'] ?: 0; ?>;
        const footprintWKT = '<?php echo $occArr['footprintwkt']; ?>';

        $(document).ready(function() {
            $('#tabs').tabs({
                <?php
                if($displayMap){
                ?>
                beforeActivate: function(event, ui) {
                    if(ui.newTab.index() === 1){
                        if(decimalLatitude && decimalLongitude){
                            if(coordUncertainty > 0){
                                const centerCoords = ol.proj.fromLonLat([decimalLongitude, decimalLatitude]);
                                const circle = new ol.geom.Circle(centerCoords);
                                circle.setRadius(Number(coordUncertainty));
                                const circleFeature = new ol.Feature(circle);
                                vectorsource.addFeature(circleFeature);
                            }
                            const pointGeom = new ol.geom.Point(ol.proj.fromLonLat([
                                decimalLongitude, decimalLatitude
                            ]));
                            vectorsource.addFeature(new ol.Feature(pointGeom));
                        }
                        if(footprintWKT !== '' && (footprintWKT.startsWith("POLYGON") || footprintWKT.startsWith("MULTIPOLYGON"))){
                            let wktFormat = new ol.format.WKT();
                            const footprintpoly = wktFormat.readFeature(footprintWKT, mapProjection);
                            if(footprintpoly){
                                footprintpoly.getGeometry().transform(wgs84Projection,mapProjection);
                                vectorsource.addFeature(footprintpoly);
                            }
                        }
                        const vectorextent = vectorsource.getExtent();
                        map.getView().fit(vectorextent,map.getSize());
                        let fittedZoom = map.getView().getZoom();
                        if(fittedZoom > 10){
                            map.getView().setZoom(fittedZoom - 8);
                        }
                    }
                    return true;
                },
                <?php
                }
                ?>
                active: tabIndex
            });

            $("#tabs").tabs().css({
                'min-height': '400px',
                'overflow': 'auto'
            });
        });

        function toggle(target){
            const objDiv = document.getElementById(target);
            if(objDiv){
                if(objDiv.style.display === "none"){
                    objDiv.style.display = "block";
                }
                else{
                    objDiv.style.display = "none";
                }
            }
            else{
                const divObjs = document.getElementsByTagName("div");
                for (let i = 0; i < divObjs.length; i++) {
                    const obj = divObjs[i];
                    if(obj.getAttribute("class") === target || obj.getAttribute("className") === target){
                        if(obj.style.display === "none"){
                            obj.style.display="inline";
                        }
                        else {
                            obj.style.display="none";
                        }
                    }
                }
            }
        }

        function verifyVoucherForm(f){
            const clTarget = f.elements["clid"].value;
            if(clTarget === "0"){
                window.alert("Please select a checklist");
                return false;
            }
            return true;
        }

        function verifyCommentForm(f){
            if(f.commentstr.value.replaceAll(/^\s+|\s+$/g,"")){
                return true;
            }
            alert("Please enter a comment");
            return false;
        }

        function openIndividual(target) {
            let occWindow = open("index.php?occid="+target,"occdisplay","resizable=1,scrollbars=1,toolbar=1,width=900,height=600,left=20,top=20");
            if (occWindow.opener == null) {
                occWindow.opener = self;
            }
        }
    </script>
</head>
<body <?php echo ($fullWindow ? '' : 'style="border:0;width:950px;"'); ?>>
<div id="fb-root"></div>
<script>
    (function(d, s, id) {
        let js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<?php
if($fullWindow){
    include(__DIR__ . '/../../header.php');
}
?>
<div>
    <?php
    if($statusStr){
        ?>
        <hr />
        <div style="padding:15px;">
            <span style="color:red;"><?php echo $statusStr; ?></span>
        </div>
        <hr />
        <?php
    }
    if($occArr){
        ?>
        <div id="tabs" style="margin:10px;clear:both;">
            <ul>
                <li><a href="#occurtab"><span>Details</span></a></li>
                <?php
                if($displayMap){
                    ?>
                    <li><a href="#maptab"><span>Map</span></a></li>
                    <?php
                }
                if($genticArr) {
                    echo '<li><a href="#genetictab"><span>Genetic Data</span></a></li>';
                }
                if($dupClusterArr){
                    ?>
                    <li><a href="#dupestab"><span>Duplicates</span></a></li>
                    <?php
                }
                ?>
                <li id="indCommentsTab"><a href="#commenttab"><span><?php echo ($commentArr?count($commentArr).' ':''); ?>Comments</span></a></li>
                <li id="indLinkedResourcesTab"><a href="linkedresources.php?occid=<?php echo $occid.'&tid='.$occArr['tid'].'&clid='.$clid.'&collid='.$collid; ?>"><span>Linked Resources</span></a></li>
                <?php
                if($isEditor){
                    ?>
                    <li><a href="#edittab"><span>Edit History</span></a></li>
                    <?php
                }
                ?>
            </ul>
            <div id="occurtab">
                <div style="float:right;">
                    <div style="float:right;">
                        <a class="twitter-share-button" href="https://twitter.com/share" data-url="<?php echo $_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid='.$occid.'&clid='.$clid; ?>">Tweet</a>
                        <script>
                            window.twttr=(function(d,s,id){
                                let js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};
                                if(d.getElementById(id))return;js=d.createElement(s);
                                js.id=id;js.src="https://platform.twitter.com/widgets.js";
                                fjs.parentNode.insertBefore(js,fjs);t._e=[];
                                t.ready=function(f){t._e.push(f);};
                                return t;
                            }(document,"script","twitter-wjs"));
                        </script>
                    </div>
                    <div style="float:right;margin-right:10px;">
                        <div class="fb-share-button" data-href="" data-layout="button_count"></div>
                    </div>
                </div>
                <div style="float:left;margin:15px 0;text-align:center;font-weight:bold;width:120px;">
                    <?php
                    if($collMetadata['icon']){
                        ?>
                        <img style='height:50px;width:50px;border:1px solid black;' src='<?php echo (strncmp($collMetadata['icon'], 'images', 6) === 0 ?'../../':'').$collMetadata['icon']; ?>'/><br/>
                        <?php
                    }
                    echo $collMetadata['institutioncode'];
                    if(isset($collMetadata['collectioncode'])){
                        echo (strlen($collMetadata['institutioncode'])<7?' : ':'<br/>').$collMetadata['collectioncode'];
                    }
                    elseif(!isset($occArr['secondaryinstcode']) && isset($occArr['secondarycollcode'])){
                        echo (strlen($collMetadata['institutioncode'])<7?' : ':'<br/>').$occArr['secondarycollcode'];
                    }
                    if($occArr['secondaryinstcode']){
                        echo '<div>';
                        echo $occArr['secondaryinstcode'];
                        if(isset($occArr['secondarycollcode'])){
                            echo (strlen($occArr['secondaryinstcode'])<7?' : ':'<br/>');
                            echo $occArr['secondarycollcode'];
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                <div style="float:left;padding:25px;">
                    <span style="font-size:18px;font-weight:bold;vertical-align:60%;">
                        <?php echo $collMetadata['collectionname']; ?>
                    </span>
                </div>
                <div style="clear:both;margin-left:60px;">
                    <div>
                        <?php
                        if(array_key_exists('loan',$occArr)){
                            ?>
                            <div style="float:right;color:red;font-weight:bold;" title="<?php echo 'Loan #'.$occArr['loan']['identifier']; ?>">
                                On Loan to
                                <?php echo $occArr['loan']['code']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['catalognumber']){
                            ?>
                            <div>
                                <b>Catalog #:</b>
                                <?php echo $occArr['catalognumber']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['othercatalognumbers']){
                            ?>
                            <div title="Other Catalog Numbers">
                                <b>Secondary Catalog #:</b>
                                <?php echo $occArr['othercatalognumbers']; ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div>
                        <b>Taxon:</b>
                        <?php
                        echo ($occArr['identificationqualifier']?$occArr['identificationqualifier']. ' ' : '');
                        ?>
                        <i><?php echo $occArr['sciname']; ?></i> <?php echo $occArr['scientificnameauthorship']; ?>
                        <br/>
                        <b>Family:</b> <?php echo $occArr['family']; ?>
                    </div>
                    <div>
                        <?php
                        if($occArr['identifiedby']){
                            ?>
                            <div>
                                <b>Determiner:</b> <?php echo $occArr['identifiedby']; ?>
                                <?php
                                if($occArr['dateidentified']) {
                                    echo ' (' . $occArr['dateidentified'] . ')';
                                }
                                ?>
                            </div>
                            <?php
                        }
                        if($occArr['taxonremarks']){
                            ?>
                            <div style="margin-left:10px;">
                                <b>Taxon Remarks:</b>
                                <?php echo $occArr['taxonremarks']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['identificationremarks']){
                            ?>
                            <div style="margin-left:10px;">
                                <b>ID Remarks:</b>
                                <?php echo $occArr['identificationremarks']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['identificationreferences']){ ?>
                            <div style="margin-left:10px;">
                                <b>ID References:</b>
                                <?php echo $occArr['identificationreferences']; ?>
                            </div>
                            <?php
                        }
                        if(array_key_exists('dets',$occArr)){
                            ?>
                            <div class="detdiv" style="margin-left:10px;cursor:pointer;" onclick="toggle('detdiv');">
                                <img src="../../images/plus_sm.png" style="border:0;" />
                                Show Determination History
                            </div>
                            <div class="detdiv" style="display:none;">
                                <div style="margin-left:10px;cursor:pointer;" onclick="toggle('detdiv');">
                                    <img src="../../images/minus_sm.png" style="border:0;" />
                                    Hide Determination History
                                </div>
                                <fieldset style="width:350px;margin:5px 0 10px 10px;border:1px solid grey;">
                                    <legend><b>Determination History</b></legend>
                                    <?php
                                    $firstIsOut = false;
                                    $dArr = $occArr['dets'];
                                    foreach($dArr as $detId => $detArr){
                                        if($firstIsOut) {
                                            echo '<hr />';
                                        }
                                        $firstIsOut = true;
                                        ?>
                                        <div style="margin:10px;">
                                            <?php
                                            if($detArr['qualifier']) {
                                                echo $detArr['qualifier'];
                                            }
                                            echo ' <b><i>'.$detArr['sciname'].'</i></b> ';
                                            echo $detArr['author']."\n";
                                            ?>
                                            <div style="">
                                                <b>Determiner: </b>
                                                <?php echo $detArr['identifiedby']; ?>
                                            </div>
                                            <div style="">
                                                <b>Date: </b>
                                                <?php echo $detArr['date']; ?>
                                            </div>
                                            <?php
                                            if($detArr['ref']){ ?>
                                                <div style="">
                                                    <b>ID References: </b>
                                                    <?php echo $detArr['ref']; ?>
                                                </div>
                                                <?php
                                            }
                                            if($detArr['notes']){
                                                ?>
                                                <div style="">
                                                    <b>ID Remarks: </b>
                                                    <?php echo $detArr['notes']; ?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </fieldset>
                            </div>
                            <?php
                        }
                        if($occArr['typestatus']){ ?>
                            <div>
                                <b>Type Status:</b>
                                <?php echo $occArr['typestatus']; ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div style="clear:both;">
                        <b>Collector:</b>
                        <?php
                        echo $occArr['recordedby'].'&nbsp;&nbsp;&nbsp;';
                        if($displayLocality) {
                            echo $occArr['recordnumber'] . '&nbsp;&nbsp;&nbsp;';
                        }
                        ?>
                    </div>
                    <?php
                    if($displayLocality){
                        if($occArr['eventdate']){
                            echo '<div><b>Date: </b>';
                            echo $occArr['eventdate'];
                            if($occArr['eventdateend']){
                                echo ' - '.$occArr['eventdateend'];
                            }
                            echo '</div>';
                        }
                        if($occArr['verbatimeventdate']){
                            echo '<div><b>Verbatim Date: </b>'.$occArr['verbatimeventdate'].'</div>';
                        }
                    }
                    ?>
                    <div>
                        <?php
                        if($occArr['associatedcollectors']){
                            ?>
                            <div>
                                <b>Additional Collectors:</b>
                                <?php echo $occArr['associatedcollectors']; ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    $localityStr1 = '';
                    if($occArr['country']) {
                        $localityStr1 .= $occArr['country'] . ', ';
                    }
                    if($occArr['stateprovince']) {
                        $localityStr1 .= $occArr['stateprovince'] . ', ';
                    }
                    if($occArr['county']) {
                        $localityStr1 .= $occArr['county'] . ', ';
                    }
                    ?>
                    <div>
                        <b>Locality:</b>
                        <?php
                        if($displayLocality){
                            if($occArr['municipality']) {
                                $localityStr1 .= $occArr['municipality'] . ', ';
                            }
                            $localityStr1 .= $occArr['locality'];
                        }
                        else{
                            $localityStr1 .= '<span style="color:red;">Detailed locality information protected.';
                            if($occArr['localitysecurityreason']){
                                $localityStr1 .= $occArr['localitysecurityreason'];
                            }
                            else{
                                $localityStr1 .= 'This is typically done to protect rare or threatened species localities.';
                            }
                            $localityStr1 .= '</span>';
                        }
                        echo trim($localityStr1,',; ');
                        ?>
                    </div>
                    <?php
                    if($displayLocality){
                        if($occArr['decimallatitude']){
                            ?>
                            <div style="margin-left:10px;">
                                <?php
                                echo $occArr['decimallatitude'].'&nbsp;&nbsp;'.$occArr['decimallongitude'];
                                if($occArr['coordinateuncertaintyinmeters']) {
                                    echo ' +-' . $occArr['coordinateuncertaintyinmeters'] . 'm.';
                                }
                                if($occArr['geodeticdatum']) {
                                    echo '&nbsp;&nbsp;' . $occArr['geodeticdatum'];
                                }
                                ?>
                            </div>
                            <?php
                        }
                        if($occArr['verbatimcoordinates']){
                            ?>
                            <div style="margin-left:10px;">
                                <b>Verbatim Coordinates: </b>
                                <?php echo $occArr['verbatimcoordinates']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['locationremarks']){
                            ?>
                            <div style="margin-left:10px;">
                                <b>Location Remarks: </b>
                                <?php echo $occArr['locationremarks']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['georeferenceremarks']){
                            ?>
                            <div style="margin-left:10px;clear:both;">
                                <b>Georeference Remarks: </b>
                                <?php echo $occArr['georeferenceremarks']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['minimumelevationinmeters']){
                            ?>
                            <div style="margin-left:10px;">
                                <b>Elevation:</b>
                                <?php
                                echo $occArr['minimumelevationinmeters'];
                                if($occArr['maximumelevationinmeters']){
                                    echo '-'.$occArr['maximumelevationinmeters'];
                                }
                                ?>
                                meters
                                <?php
                                echo (!$occArr['verbatimelevation'])?'('.round($occArr['minimumelevationinmeters']*3.28).($occArr['maximumelevationinmeters']?'-'.round($occArr['maximumelevationinmeters']*3.28):'').'ft)':'';
                                ?>
                            </div>
                            <?php
                        }
                        if($occArr['verbatimelevation']){
                            ?>
                            <div>
                                <b>Verbatim Elevation: </b>
                                <?php echo $occArr['verbatimelevation']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['habitat']){
                            ?>
                            <div style="clear:both;">
                                <b>Habitat:</b>
                                <?php echo $occArr['habitat']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['substrate']){
                            ?>
                            <div style="clear:both;">
                                <b>Substrate:</b>
                                <?php echo $occArr['substrate']; ?>
                            </div>
                            <?php
                        }
                        if($occArr['associatedtaxa']){
                            ?>
                            <div style="clear:both;">
                                <b>Associated Species:</b>
                                <?php echo $occArr['associatedtaxa']; ?>
                            </div>
                            <?php
                        }
                    }
                    if($occArr['verbatimattributes']){
                        ?>
                        <div style="clear:both;">
                            <b>Verbatim Attributes:</b>
                            <?php echo $occArr['verbatimattributes']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['reproductivecondition']){
                        ?>
                        <div style="clear:both;">
                            <b>Reproductive Condition:</b>
                            <?php echo $occArr['reproductivecondition']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['lifestage']){
                        ?>
                        <div style="clear:both;">
                            <b>Life Stage:</b>
                            <?php echo $occArr['lifestage']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['sex']){
                        ?>
                        <div style="clear:both;">
                            <b>Sex:</b>
                            <?php echo $occArr['sex']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['individualcount']){
                        ?>
                        <div style="clear:both;">
                            <b>Individual Count:</b>
                            <?php echo $occArr['individualcount']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['samplingprotocol']){
                        ?>
                        <div style="clear:both;">
                            <b>Sampling Protocol:</b>
                            <?php echo $occArr['samplingprotocol']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['preparations']){
                        ?>
                        <div style="clear:both;">
                            <b>Preparations:</b>
                            <?php echo $occArr['preparations']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['occurrenceremarks']){
                        ?>
                        <div style="clear:both;">
                            <b>Occurrence Remarks: </b>
                            <?php echo $occArr['occurrenceremarks']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['fieldnotes']){
                        ?>
                        <div style="clear:both;">
                            <b>Field Notes: </b>
                            <?php echo $occArr['fieldnotes']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['cultivationstatus']){
                        ?>
                        <div style="clear:both;">
                            <b>Cultivation Status:</b> Cultivated
                        </div>
                        <?php
                    }
                    if($occArr['establishmentmeans']){
                        ?>
                        <div style="clear:both;">
                            <b>Establishment Means: </b>
                            <?php echo $occArr['establishmentmeans']; ?>
                        </div>
                        <?php
                    }
                    if($occArr['disposition']){
                        ?>
                        <div style="clear:both;">
                            <b>Disposition: </b>
                            <?php echo $occArr['disposition']; ?>
                        </div>
                        <?php
                    }
                    if(isset($occArr['exs'])){
                        ?>
                        <div style="clear:both;">
                            <b>Exsiccati series:</b>
                            <?php
                            echo '<a href="../exsiccati/index.php?omenid='.$occArr['exs']['omenid'].'">';
                            echo $occArr['exs']['title'].'&nbsp;#'.$occArr['exs']['exsnumber'];
                            echo '</a>';
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div style="clear:both;padding:10px;">
                        <?php
                        if($displayLocality){
                            if(array_key_exists('imgs',$occArr)){
                                $iArr = $occArr['imgs'];
                                ?>
                                <fieldset style="padding:10px;width:80%;margin-left:auto;margin-right:auto;">
                                    <legend><b>Images</b></legend>
                                    <div style="display:flex;flex-direction:column;padding:5px;">
                                        <?php
                                        foreach($iArr as $imgId => $imgArr){
                                            ?>
                                            <div style="display:flex;justify-content:space-evenly;padding:5px;">
                                                <div style="max-width:250px;text-align:center;">
                                                    <a href='<?php echo $imgArr['url']; ?>' target="_blank">
                                                        <img src="<?php echo ($imgArr['tnurl']?:$imgArr['url']); ?>" title="<?php echo $imgArr['caption']; ?>" style="border:1px solid black;<?php echo (!$imgArr['tnurl']?'width:250px;':'');?>" />
                                                    </a>
                                                    <?php
                                                    if($imgArr['url'] !== $imgArr['lgurl']) {
                                                        echo '<div><a href="' . $imgArr['url'] . '" target="_blank">Open Medium Image</a></div>';
                                                    }
                                                    if($imgArr['lgurl']) {
                                                        echo '<div><a href="' . $imgArr['lgurl'] . '" target="_blank">Open Large Image</a></div>';
                                                    }
                                                    ?>
                                                </div>
                                                <div style="margin-left:25px;">
                                                    <?php
                                                    $photographer = '';
                                                    if($imgArr['photographer']){
                                                        $photographer = $imgArr['photographer'];
                                                    }
                                                    elseif($imgArr['photographeruid']){
                                                        $pArr = $indManager->getPhotographerArr();
                                                        $photographer = $pArr[$imgArr['photographeruid']];
                                                    }
                                                    if($photographer){
                                                        echo '<div><b>Photographer:</b> '.wordwrap($photographer, 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['caption']){
                                                        echo '<div><b>Caption:</b> '.wordwrap($imgArr['caption'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['locality']){
                                                        echo '<div><b>Locality:</b> '.wordwrap($imgArr['locality'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['notes']){
                                                        echo '<div><b>Notes:</b> '.wordwrap($imgArr['notes'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['anatomy']){
                                                        echo '<div><b>Anatomy:</b> '.wordwrap($imgArr['anatomy'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['dynamicProperties']){
                                                        echo '<div><b>Dynamic Properties:</b> '.wordwrap($imgArr['dynamicProperties'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['owner']){
                                                        echo '<div><b>Owner:</b> '.wordwrap($imgArr['owner'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['sourceurl']){
                                                        $urlDisplay = $imgArr['sourceurl'];
                                                        if(strlen($urlDisplay) > 57) {
                                                            $urlDisplay = '...' . substr($urlDisplay, -57);
                                                        }
                                                        echo '<div><b>Source URL:</b> <a href="'.$imgArr['sourceurl'].'" target="_blank">'.$urlDisplay.'</a></div>';
                                                    }
                                                    if($imgArr['referenceUrl']){
                                                        $urlDisplay = $imgArr['referenceUrl'];
                                                        if(strlen($urlDisplay) > 57) {
                                                            $urlDisplay = '...' . substr($urlDisplay, -57);
                                                        }
                                                        echo '<div><b>Reference URL:</b> <a href="'.$imgArr['referenceUrl'].'" target="_blank">'.$urlDisplay.'</a></div>';
                                                    }
                                                    if($imgArr['copyright']){
                                                        echo '<div><b>Copyright:</b> '.wordwrap($imgArr['copyright'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['rights']){
                                                        echo '<div><b>Rights:</b> '.wordwrap($imgArr['rights'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($imgArr['accessrights']){
                                                        echo '<div><b>Access Rights:</b> '.wordwrap($imgArr['accessrights'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </fieldset>
                                <?php
                            }
                            if(array_key_exists('media',$occArr)){
                                $mArr = $occArr['media'];
                                ?>
                                <fieldset style="padding:10px;width:80%;margin-left:auto;margin-right:auto;margin-top:15px;">
                                    <legend><b>Media</b></legend>
                                    <div style="display:flex;flex-direction:column;padding:5px;">
                                        <?php
                                        foreach($mArr as $medId => $medArr){
                                            ?>
                                            <div style="display:flex;justify-content:space-evenly;padding:5px;">
                                                <div style="max-width:250px;text-align:center;">
                                                    <?php
                                                    $medUrl = $medArr['accessuri'];
                                                    $medFormat = $medArr['format'];
                                                    if(isset($GLOBALS['IMAGE_DOMAIN']) && strncmp($medUrl, '/', 1) === 0) {
                                                        $medUrl = $GLOBALS['IMAGE_DOMAIN'].$medUrl;
                                                    }

                                                    if(strncmp($medFormat, 'video/', 6) === 0){
                                                        echo '<video width="250" controls>';
                                                        echo '<source src="'.$medUrl.'" type="'.$medFormat.'">';
                                                        echo '</video>';
                                                    }
                                                    elseif(strncmp($medFormat, 'audio/', 6) === 0){
                                                        echo '<audio style="width:250px;" controls>';
                                                        echo '<source src="'.$medUrl.'" type="'.$medFormat.'">';
                                                        echo '</audio>';
                                                    }
                                                    elseif(substr($medUrl, -3) === '.zc'){
                                                        echo '<a href="'.$medUrl.'">Download File</a>';
                                                    }
                                                    ?>
                                                </div>
                                                <div style="margin-left:25px;">
                                                    <?php
                                                    $creator = '';
                                                    if($medArr['creator']){
                                                        $creator = $medArr['creator'];
                                                    }
                                                    elseif($medArr['creatoruid']){
                                                        $pArr = $indManager->getPhotographerArr();
                                                        $creator = $pArr[$medArr['creatoruid']];
                                                    }
                                                    if($creator){
                                                        echo '<div><b>Creator:</b> '.wordwrap($creator, 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['title']){
                                                        echo '<div><b>Title:</b> '.wordwrap($medArr['title'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['description']){
                                                        echo '<div><b>Description:</b> '.wordwrap($medArr['description'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['locationcreated']){
                                                        echo '<div><b>Locality:</b> '.wordwrap($medArr['locationcreated'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['language']){
                                                        echo '<div><b>Language:</b> '.wordwrap($medArr['language'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['furtherinformationurl']){
                                                        $urlDisplay = $medArr['furtherinformationurl'];
                                                        if(strlen($urlDisplay) > 57) {
                                                            $urlDisplay = '...' . substr($urlDisplay, -57);
                                                        }
                                                        echo '<div><b>Further Information URL:</b> <a href="'.$medArr['furtherinformationurl'].'" target="_blank">'.$urlDisplay.'</a></div>';
                                                    }
                                                    if($medArr['owner']){
                                                        echo '<div><b>Owner:</b> '.wordwrap($medArr['owner'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['publisher']){
                                                        echo '<div><b>Publisher:</b> '.wordwrap($medArr['publisher'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['contributor']){
                                                        echo '<div><b>Contributor:</b> '.wordwrap($medArr['contributor'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['usageterms']){
                                                        echo '<div><b>Usage Terms:</b> '.wordwrap($medArr['usageterms'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['rights']){
                                                        echo '<div><b>Rights:</b> '.wordwrap($medArr['rights'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    if($medArr['bibliographiccitation']){
                                                        echo '<div><b>Bibliographic Citation:</b> '.wordwrap($medArr['bibliographiccitation'], 50, '<br />\n', true).'</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </fieldset>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php
                    if($collMetadata['individualurl']){
                        $indUrl = '';
                        if($occArr['dbpk'] && strpos($collMetadata['individualurl'],'--DBPK--') !== false){
                            $indUrl = str_replace('--DBPK--',$occArr['dbpk'],$collMetadata['individualurl']);
                        }
                        elseif($occArr['catalognumber'] && strpos($collMetadata['individualurl'],'--CATALOGNUMBER--') !== false){
                            $indUrl = str_replace('--CATALOGNUMBER--',$occArr['catalognumber'],$collMetadata['individualurl']);
                        }
                        elseif($occArr['occurrenceid'] && strpos($collMetadata['individualurl'],'--OCCURRENCEID--') !== false){
                            $indUrl = str_replace('--OCCURRENCEID--',$occArr['occurrenceid'],$collMetadata['individualurl']);
                        }
                        if($indUrl){
                            echo '<div style="margin-top:10px;clear:both;">';
                            echo '<b>Link to Source:</b> <a href="'.$indUrl.'" target="_blank">';
                            echo $collMetadata['institutioncode'].' #'.($occArr['catalognumber']?:$occArr['dbpk']);
                            echo '</a></div>';
                        }
                    }
                    $rightsStr = $collMetadata['rights'];
                    if($collMetadata['rights']){
                        $rightsHeading = '';
                        if(isset($GLOBALS['RIGHTS_TERMS'])) {
                            $rightsHeading = array_search($rightsStr, $GLOBALS['RIGHTS_TERMS'], true);
                        }
                        if(strncmp($collMetadata['rights'], 'http', 4) === 0){
                            $rightsStr = '<a href="'.$rightsStr.'" target="_blank">'.($rightsHeading?:$rightsStr).'</a>';
                        }
                        $rightsStr = '<div style="margin-top:2px;"><b>Usage Rights:</b> '.$rightsStr.'</div>';
                    }
                    if($collMetadata['rightsholder']){
                        $rightsStr .= '<div style="margin-top:2px;"><b>Rights Holder:</b> '.$collMetadata['rightsholder'].'</div>';
                    }
                    if($collMetadata['accessrights']){
                        $rightsStr .= '<div style="margin-top:2px;"><b>Access Rights:</b> '.$collMetadata['accessrights'].'</div>';
                    }
                    ?>
                    <div style="margin:5px 0 5px 0;">
                        <?php
                        if($rightsStr){
                            echo $rightsStr;
                        }
                        else{
                            echo '<a href="../../misc/usagepolicy.php">General Data Usage Policy</a>';
                        }
                        ?>
                    </div>
                    <div style="margin:3px 0;"><b>Record Id:</b> <?php echo $occArr['guid']; ?></div>
                    <?php
                    if($occArr['occurrenceid']){
                        ?>
                        <div>
                            <b>Occurrence ID (GUID):</b>
                            <?php
                            $resolvableGuid = false;
                            if(strncmp($occArr['occurrenceid'], 'http', 4) === 0) {
                                $resolvableGuid = true;
                            }
                            if($resolvableGuid) {
                                echo '<a href="' . $occArr['occurrenceid'] . '" target="_blank">';
                            }
                            echo $occArr['occurrenceid'];
                            if($resolvableGuid) {
                                echo '</a>';
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div style="margin-top:10px;clear:both;">
                        For additional information on this occurrence, please contact:
                        <?php
                        $emailSubject = $GLOBALS['DEFAULT_TITLE'].' occurrence: '.$occArr['catalognumber'].' ('.$occArr['othercatalognumbers'].')';
                        $emailBody = 'Occurrence being referenced: http://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid='.$occArr['occid'];
                        $emailRef = 'subject='.$emailSubject.'&cc='.$GLOBALS['ADMIN_EMAIL'].'&body='.$emailBody;
                        ?>
                        <a href="mailto:<?php echo $collMetadata['email'].'?'.$emailRef; ?>">
                            <?php echo $collMetadata['contact'].' ('.$collMetadata['email'].')'; ?>
                        </a>
                    </div>
                    <?php
                    if($isEditor || ($displayLocality && $collMetadata['publicedits'])){
                        ?>
                        <div style="margin-bottom:10px;">
                            <?php
                            if($GLOBALS['SYMB_UID']){
                                ?>
                                Do you see an error? If so, errors can be fixed using the
                                <a href="../editor/occurrenceeditor.php?occid=<?php echo $occArr['occid'];?>">
                                    Occurrence Editor.
                                </a>
                                <?php
                            }
                            else{
                                ?>
                                See an error? <a href="../../profile/index.php?refurl=../collections/individual/index.php?occid=<?php echo $occid; ?>">Login</a> to edit data
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            if($displayMap){
                ?>
                <div id="maptab" style="height: 600px;">
                    <?php include_once(__DIR__ . '/../../spatial/viewerElement.php'); ?>
                </div>
                <?php
            }
            if($genticArr){
                ?>
                <div id="genetictab">
                    <?php
                    foreach($genticArr as $genId => $gArr){
                        ?>
                        <div style="margin:15px;">
                            <div style="font-weight:bold;margin-bottom:5px;"><?php echo $gArr['name']; ?></div>
                            <div style="margin-left:15px;"><b>Identifier:</b> <?php echo $gArr['id']; ?></div>
                            <div style="margin-left:15px;"><b>Locus:</b> <?php echo $gArr['locus']; ?></div>
                            <div style="margin-left:15px;">
                                <b>URL:</b>
                                <a href="<?php echo $gArr['resourceurl']; ?>" target="_blank"><?php echo $gArr['resourceurl']; ?></a>
                            </div>
                            <div style="margin-left:15px;"><b>Notes:</b> <?php echo $gArr['notes']; ?></div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            if($dupClusterArr){
                ?>
                <div id="dupestab">
                    <div style="margin:20px;">
                        <div style="font-weight:bold;font-size:120%;margin-bottom:10px;"><u>Current Record</u></div>
                        <?php
                        echo '<div style="font-weight:bold;font-size:120%;">'.$collMetadata['collectionname'].' ('.$collMetadata['institutioncode'].($collMetadata['collectioncode']?':'.$collMetadata['collectioncode']:'').')</div>';
                        echo '<div style="margin:5px 15px">';
                        if($occArr['recordedby']) {
                            echo '<div>' . $occArr['recordedby'] . ' ' . $occArr['recordnumber'] . '<span style="margin-left:40px;">' . $occArr['eventdate'] . '</span></div>';
                        }
                        if($occArr['catalognumber']) {
                            echo '<div><b>Catalog Number:</b> ' . $occArr['catalognumber'] . '</div>';
                        }
                        if($occArr['occurrenceid']) {
                            echo '<div><b>GUID:</b> ' . $occArr['occurrenceid'] . '</div>';
                        }
                        if($occArr['sciname']) {
                            echo '<div><b>Latest Identification:</b> ' . $occArr['sciname'] . '</div>';
                        }
                        if($occArr['identifiedby']) {
                            echo '<div><b>Identified by:</b> ' . $occArr['identifiedby'] . '<span style="margin-left:30px;">' . $occArr['dateidentified'] . '</span></div>';
                        }
                        echo '</div>';
                        echo '<div style="margin:20px 0;clear:both"><hr/><hr/></div>';
                        foreach($dupClusterArr as $dupid => $dArr){
                            $innerDupArr = $dArr['o'];
                            foreach($innerDupArr as $dupOccid => $dupArr){
                                if($dupOccid !== $occid){
                                    echo '<div style="clear:both;margin:15px;">';
                                    echo '<div style="font-weight:bold;font-size:120%;">'.$dupArr['collname'].' ('.$dupArr['instcode'].($dupArr['collcode']?':'.$dupArr['collcode']:'').')</div>';
                                    echo '<div style="float:left;margin:5px 15px">';
                                    if($dupArr['recordedby']) {
                                        echo '<div>' . $dupArr['recordedby'] . ' ' . $dupArr['recordnumber'] . '<span style="margin-left:40px;">' . $dupArr['eventdate'] . '</span></div>';
                                    }
                                    if($dupArr['catnum']) {
                                        echo '<div><b>Catalog Number:</b> ' . $dupArr['catnum'] . '</div>';
                                    }
                                    if($dupArr['occurrenceid']) {
                                        echo '<div><b>GUID:</b> ' . $dupArr['occurrenceid'] . '</div>';
                                    }
                                    if($dupArr['sciname']) {
                                        echo '<div><b>Latest Identification:</b> ' . $dupArr['sciname'] . '</div>';
                                    }
                                    if($dupArr['identifiedby']) {
                                        echo '<div><b>Identified by:</b> ' . $dupArr['identifiedby'] . '<span style="margin-left:30px;">' . $dupArr['dateidentified'] . '</span></div>';
                                    }
                                    if($dupArr['notes']) {
                                        echo '<div>' . $dupArr['notes'] . '</div>';
                                    }
                                    echo '<div><a href="#" onclick="openIndividual('.$dupOccid. ')">Show Full Details</a></div>';
                                    echo '</div>';
                                    if($dupArr['url']){
                                        $url = $dupArr['url'];
                                        $tnUrl = $dupArr['tnurl'];
                                        if(!$tnUrl) {
                                            $tnUrl = $url;
                                        }
                                        if(isset($GLOBALS['IMAGE_DOMAIN'])){
                                            if(strncmp($url, '/', 1) === 0) {
                                                $url = $GLOBALS['IMAGE_DOMAIN'] . $url;
                                            }
                                            if(strncmp($tnUrl, '/', 1) === 0) {
                                                $tnUrl = $GLOBALS['IMAGE_DOMAIN'] . $tnUrl;
                                            }
                                        }
                                        echo '<div style="float:left;margin:10px;">';
                                        echo '<a href="'.$url.'">';
                                        echo '<img src="'.$tnUrl.'" style="width:100px;border:1px solid grey" />';
                                        echo '</a>';
                                        echo '</div>';
                                    }
                                    echo '<div style="margin:10px 0;clear:both"><hr/></div>';
                                    echo '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <div id="commenttab">
                <?php
                if($commentArr){
                    echo '<div><b>'.count($commentArr).' Comments</b></div>';
                    echo '<hr style="color:gray;"/>';
                    foreach($commentArr as $comId => $comArr){
                        ?>
                        <div style="margin:15px;">
                            <?php
                            echo '<div>';
                            echo '<b>'.$comArr['username'].'</b> <span style="color:gray;">posted '.$comArr['initialtimestamp'].'</span>';
                            echo '</div>';
                            if($comArr['reviewstatus'] === 0 || $comArr['reviewstatus'] === 2) {
                                echo '<div style="color:red;">Comment not public due to pending abuse report (viewable to administrators only)</div>';
                            }
                            echo '<div style="margin:10px;">'.$comArr['comment'].'</div>';
                            if($comArr['reviewstatus']){
                                if($GLOBALS['SYMB_UID']){
                                    ?>
                                    <div><a href="index.php?repcomid=<?php echo $comId.'&occid='.$occid.'&tabindex='.($displayMap?2:1); ?>">Report as inappropriate or abusive</a></div>
                                    <?php
                                }
                            }
                            else{
                                ?>
                                <div><a href="index.php?publiccomid=<?php echo $comId.'&occid='.$occid.'&tabindex='.($displayMap?2:1); ?>">Make comment public</a></div>
                                <?php
                            }
                            if($isEditor || ($GLOBALS['SYMB_UID'] && $comArr['username'] === $GLOBALS['PARAMS_ARR']['un'])){
                                ?>
                                <div style="margin:20px;">
                                    <form name="delcommentform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to delete comment?')">
                                        <input name="occid" type="hidden" value="<?php echo $occid; ?>" />
                                        <input name="comid" type="hidden" value="<?php echo $comId; ?>" />
                                        <input name="tabindex" type="hidden" value="<?php echo ($displayMap?2:1); ?>" />
                                        <input name="formsubmit" type="submit" value="Delete Comment" />
                                    </form>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <hr style="color:gray;"/>
                        <?php
                    }
                }
                else{
                    echo '<div style="font-weight:bold;font-size:120%;margin:20px;">No comments have been submitted</div>';
                }
                ?>
                <fieldset style="padding:20px;">
                    <legend><b>New Comment</b></legend>
                    <?php
                    if($GLOBALS['VALID_USER']){
                        ?>
                        <form name="commentform" action="index.php" method="post" onsubmit="return verifyCommentForm(this);">
                            <textarea name="commentstr" rows="8" style="width:98%;"></textarea>
                            <div style="margin:15px;">
                                <input name="occid" type="hidden" value="<?php echo $occid; ?>" />
                                <input name="tabindex" type="hidden" value="<?php echo ($displayMap?2:1); ?>" />
                                <input type="submit" name="formsubmit" value="Submit Comment" />
                            </div>
                            <div>
                                Messages over 500 words long may be automatically truncated. All comments are moderated.
                            </div>
                        </form>
                        <?php
                    }
                    else{
                        ?>
                        <div style="margin:10px;">
                            <a href="../../profile/index.php?refurl=../collections/individual/index.php?tabindex=2&occid=<?php echo $occid; ?>">Login</a> to leave a comment.
                        </div>
                        <?php
                    }
                    ?>
                </fieldset>

            </div>
            <?php
            if($isEditor){
                ?>
                <div id="edittab">
                    <div style="padding:15px;">
                        <?php
                        if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
                            ?>
                            <div style="float:right;" title="Manage Edits">
                                <a href="../editor/editreviewer.php?collid=<?php echo $collid.'&occid='.$occid; ?>"><i style="height:15px;width:15px;" class="far fa-edit"></i></a>
                            </div>
                            <?php
                        }
                        echo '<div style="margin:20px 0 30px 0;">';
                        echo '<b>Entered By:</b> '.($occArr['recordenteredby']?:'not recorded').'<br/>';
                        echo '<b>Date entered:</b> '.($occArr['dateentered']?:'not recorded').'<br/>';
                        echo '<b>Date modified:</b> '.($occArr['datelastmodified']?:'not recorded').'<br/>';
                        if($occArr['modified'] && $occArr['modified'] !== $occArr['datelastmodified']) {
                            echo '<b>Source date modified:</b> ' . $occArr['modified'];
                        }
                        echo '</div>';
                        $editArr = $indManager->getEditArr();
                        $externalEdits = $indManager->getExternalEditArr();
                        if($editArr || $externalEdits){
                            if($editArr){
                                ?>
                                <fieldset style="padding:20px;">
                                    <legend><b>Internal Edits</b></legend>
                                    <?php
                                    foreach($editArr as $k => $eArr){
                                        $reviewStr = 'OPEN';
                                        if($eArr['reviewstatus'] === 2) {
                                            $reviewStr = 'PENDING';
                                        }
                                        elseif($eArr['reviewstatus'] === 3) {
                                            $reviewStr = 'CLOSED';
                                        }
                                        ?>
                                        <div>
                                            <b>Editor:</b> <?php echo $eArr['editor']; ?>
                                            <span style="margin-left:30px;"><b>Date:</b> <?php echo $eArr['ts']; ?></span>
                                        </div>
                                        <div>
                                            <span><b>Applied Status:</b> <?php echo ($eArr['appliedstatus']?'applied':'not applied'); ?></span>
                                            <span style="margin-left:30px;"><b>Review Status:</b> <?php echo $reviewStr; ?></span>
                                        </div>
                                        <?php
                                        $edArr = $eArr['edits'];
                                        foreach($edArr as $vArr){
                                            echo '<div style="margin:15px;">';
                                            echo '<b>Field:</b> '.$vArr['fieldname'].'<br/>';
                                            echo '<b>Old Value:</b> '.$vArr['old'].'<br/>';
                                            echo '<b>New Value:</b> '.$vArr['new'].'<br/>';
                                            echo '</div>';
                                        }
                                        echo '<div style="margin:15px 0;"><hr/></div>';
                                    }
                                    ?>
                                </fieldset>
                                <?php
                            }
                            if($externalEdits){
                                ?>
                                <fieldset style="margin-top:20px;padding:20px;">
                                    <legend><b>External Edits</b></legend>
                                    <?php
                                    foreach($externalEdits as $orid => $eArr){
                                        foreach($eArr as $appliedStatus => $eArr2){
                                            $reviewStr = 'OPEN';
                                            if($eArr2['reviewstatus'] === 2) {
                                                $reviewStr = 'PENDING';
                                            }
                                            elseif($eArr2['reviewstatus'] === 3) {
                                                $reviewStr = 'CLOSED';
                                            }
                                            ?>
                                            <div>
                                                <b>Editor:</b> <?php echo $eArr2['editor']; ?>
                                                <span style="margin-left:30px;"><b>Date:</b> <?php echo $eArr2['ts']; ?></span>
                                                <span style="margin-left:30px;"><b>Source:</b> <?php echo $eArr2['source']; ?></span>
                                            </div>
                                            <div>
                                                <span><b>Applied Status:</b> <?php echo ($appliedStatus?'applied':'not applied'); ?></span>
                                                <span style="margin-left:30px;"><b>Review Status:</b> <?php echo $reviewStr; ?></span>
                                            </div>
                                            <?php
                                            $edArr = $eArr2['edits'];
                                            foreach($edArr as $fieldName => $vArr){
                                                echo '<div style="margin:15px;">';
                                                echo '<b>Field:</b> '.$fieldName.'<br/>';
                                                echo '<b>Old Value:</b> '.$vArr['old'].'<br/>';
                                                echo '<b>New Value:</b> '.$vArr['new'].'<br/>';
                                                echo '</div>';
                                            }
                                            echo '<div style="margin:15px 0;"><hr/></div>';
                                        }
                                    }
                                    ?>
                                </fieldset>
                                <?php
                            }
                        }
                        else{
                            echo '<div style="margin:25px 0;"><b>Record has not been edited since being entered</b></div>';
                        }
                        echo '<div style="margin:15px">Note: Edits are only viewable by collection administrators and editors</div>';
                        //Display Access Stats
                        $accessStats = $indManager->getAccessStats();
                        if($accessStats){
                            echo '<div style="margin-top:30px"><b>Access Stats</b></div>';
                            echo '<table class="styledtable" style="font-size:100%;width:300px;">';
                            echo '<tr><th>Year</th><th>Access Type</th><th>Count</th></tr>';
                            foreach($accessStats as $accessDate => $arr1){
                                foreach($arr1 as $accessType => $accessCnt){
                                    echo '<tr><td>'.$accessDate.'</td><td>'.$accessType.'</td><td>'.$accessCnt.'</td></tr>';
                                }
                            }
                            echo '</table>';
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
    else{
        ?>
        <h2>Unable to locate occurrence record</h2>
        <div style="margin:20px">
            <div>Checking archive...</div>
            <div style="margin:10px">
                <?php
                flush();
                $rawArchArr = $indManager->checkArchive();
                if($rawArchArr && $rawArchArr['obj']){
                    $archArr = $rawArchArr['obj'];
                    if(isset($archArr['dateDeleted'])) {
                        echo '<div><b>Record deleted:</b> ' . $archArr['dateDeleted'] . '</div>';
                    }
                    if($rawArchArr['notes']) {
                        echo '<div style="margin-left:15px"><b>Notes: </b>' . $rawArchArr['notes'] . '</div>';
                    }
                    $dets = array();
                    $imgs = array();
                    if(isset($archArr['dets'])){
                        $dets = $archArr['dets'];
                        unset($archArr['dets']);
                    }
                    if(isset($archArr['imgs'])){
                        $imgs = $archArr['imgs'];
                        unset($archArr['imgs']);
                    }
                    echo '<table class="styledtable" style="font-family:Arial,serif;font-size:12px;"><tr><th>Field</th><th>Value</th></tr>';
                    foreach($archArr as $f => $v){
                        echo '<tr><td style="width:175px;"><b>'.$f.'</b></td><td>';
                        if(is_array($v)){
                            echo implode(', ',$v);
                        }
                        else{
                            echo $v;
                        }
                        echo '</td></tr>';
                    }
                    if($dets){
                        foreach($dets as $id => $dArr){
                            echo '<tr><td><b>Determination #'.$id.'</b></td><td>';
                            foreach($dArr as $f => $v){
                                echo '<b>'.$f.'</b>: '.$v.'<br/>';
                            }
                            echo '</td></tr>';
                        }
                    }
                    if($imgs){
                        foreach($imgs as $id => $iArr){
                            echo '<tr><td><b>Image #'.$id.'</b></td><td>';
                            foreach($iArr as $f => $v){
                                echo '<b>'.$f.'</b>: '.$v.'<br/>';
                            }
                            echo '</td></tr>';
                        }
                    }
                    echo '</table>';
                }
                else{
                    echo 'Unable to locate record within archive';
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<?php
if($fullWindow){
    include(__DIR__ . '/../../footer.php');
}
?>
</body>
</html>
