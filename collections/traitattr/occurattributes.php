<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceAttributes.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ' . $GLOBALS['CLIENT_ROOT'] . '/profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = (int)$_REQUEST['collid'];
$submitForm = array_key_exists('submitform',$_POST)?$_POST['submitform']:'';
$mode = array_key_exists('mode',$_REQUEST)?$_REQUEST['mode']:1;
$traitID = array_key_exists('traitid',$_REQUEST)?(int)$_REQUEST['traitid']:0;
$taxonFilter = array_key_exists('taxonfilter',$_POST)?$_POST['taxonfilter']:'';
$tidFilter = array_key_exists('tidfilter',$_POST)?(int)$_POST['tidfilter']:0;
$paneX = array_key_exists('panex',$_POST)?(int)$_POST['panex']:575;
$paneY = array_key_exists('paney',$_POST)?(int)$_POST['paney']:550;
$imgRes = array_key_exists('imgres',$_POST)?$_POST['imgres']:'med';
$reviewUid = array_key_exists('reviewuid',$_POST)?(int)$_POST['reviewuid']:0;
$reviewDate = array_key_exists('reviewdate',$_POST)?$_POST['reviewdate']:'';
$reviewStatus = array_key_exists('reviewstatus',$_POST)?(int)$_POST['reviewstatus']:0;
$start = array_key_exists('start',$_POST)?(int)$_POST['start']:0;

$isEditor = 0; 
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN']){
		$isEditor = 2;
	}
	elseif($collid){
		if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
			$isEditor = 2;
		}
		elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
			$isEditor = 1;
		}
	}
}

$attrManager = new OccurrenceAttributes();
if($tidFilter) {
    $attrManager->setTidFilter($tidFilter);
}
$attrManager->setCollid($collid);

$statusStr = '';
if($isEditor){
	if($submitForm === 'Save and Next'){
		$attrManager->setTargetOccid($_POST['targetoccid']);
		if(!$attrManager->saveAttributes($_POST,$_POST['notes'],$GLOBALS['SYMB_UID'])){
			$statusStr = $attrManager->getErrorMessage();
		}
	}
	elseif($submitForm === 'Set Status and Save'){
		$attrManager->setTargetOccid($_POST['targetoccid']);
		$attrManager->saveReviewStatus($traitID,$_POST);
	}
}
$imgArr = array();
$occid = 0;
$catNum = '';
if($traitID){
	$imgRetArr = array();
	if($mode === 1){
		$imgRetArr = $attrManager->getImageUrls();
        $imgArr = current($imgRetArr);
	}
	elseif($mode === 2){
		$imgRetArr = $attrManager->getReviewUrls($traitID, $reviewUid, $reviewDate, $reviewStatus, $start);
		if($imgRetArr) {
            $imgArr = current($imgRetArr);
        }
		
	}
	if($imgRetArr){
		$catNum = $imgArr['catnum'];
		unset($imgArr['catnum']);
		$occid = key($imgRetArr);
		if($occid) {
            $attrManager->setTargetOccid($occid);
        }
	}
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title>Occurrence Attribute batch Editor</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
    <script src="../../js/external/jquery.js" type="text/javascript"></script>
    <script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="../../js/external/jquery.imagetool-1.7.js?ver=160102" type="text/javascript"></script>
    <script type="text/javascript">
        let activeImgIndex = 1;
        const imgArr = [];
        const imgLgArr = [];

        <?php
        $imgDomain = 'http://';
        if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
            $imgDomain = 'https://';
        }
        $imgDomain .= $_SERVER['HTTP_HOST'];
        foreach($imgArr as $cnt => $iArr){
            $url = $iArr['web'];
            if(strncmp($url, '/', 1) === 0) {
                $url = $imgDomain . $url;
            }
            echo 'imgArr['.$cnt.'] = "'.$url.'";'."\n";
            $lgUrl = $iArr['lg'];
            if($lgUrl){
                if(strncmp($lgUrl, '/', 1) === 0) {
                    $lgUrl = $imgDomain . $lgUrl;
                }
                echo 'imgLgArr['.$cnt.'] = "'.$lgUrl.'";'."\n";
            }
        }
        ?>

        document.addEventListener("DOMContentLoaded", function() {
            setImgRes();
            $("#specimg").imagetool({
                maxWidth: 6000
                ,viewportWidth: <?php echo $paneX; ?>
                ,viewportHeight: <?php echo $paneY; ?>
            });
        });

        function setImgRes(){
            if(imgLgArr[activeImgIndex] != null){
                if($("#imgres1").val() === 'lg') {
                    changeImgRes('lg');
                }
            }
            else{
                if(imgArr[activeImgIndex] != null){
                    $("#specimg").attr("src",imgArr[activeImgIndex]);
                    document.getElementById("imgresmed").checked = true;
                    const imgResLgRadio = document.getElementById("imgreslg");
                    imgResLgRadio.disabled = true;
                    imgResLgRadio.title = "Large resolution image not available";
                }
            }
            else{
                if(imgLgArr[activeImgIndex] != null){
                    $("#specimg").attr("src",imgLgArr[activeImgIndex]);
                    document.getElementById("imgreslg").checked = true;
                    const imgResMedRadio = document.getElementById("imgresmed");
                    imgResMedRadio.disabled = true;
                    imgResMedRadio.title = "Medium resolution image not available";
                }
            }
        }

        function changeImgRes(resType){
            if(resType === 'lg'){
                $("#imgres1").val("lg");
                $("#imgres2").val("lg");
                if(imgLgArr[activeImgIndex]){
                    $("#specimg").attr("src",imgLgArr[activeImgIndex]);
                    $( "#imgreslg" ).prop( "checked", true );
                }
            }
            else{
                $("#imgres1").val("med");
                $("#imgres2").val("med");
                if(imgArr[activeImgIndex]){
                    $("#specimg").attr("src",imgArr[activeImgIndex]);
                    $( "#imgresmed" ).prop( "checked", true );
                }
            }
        }

        function setPortXY(portWidth,portHeight){
            $("#panex1").val(portWidth);
            $("#paney1").val(portHeight);
            $("#panex2").val(portWidth);
            $("#paney2").val(portHeight);
        }

        function nextImage(){
            activeImgIndex = activeImgIndex + 1;
            if(activeImgIndex >= imgArr.length) {
                activeImgIndex = 1;
            }
            $("#specimg").attr("src",imgArr[activeImgIndex]);
            $("#specimg").imagetool({
                maxWidth: 6000
                ,viewportWidth: $("#panex1").val()
                ,viewportHeight: $("#paney1").val()
            });
            $("#labelcnt").html(activeImgIndex);
            return false;
        }

        function skipSpecimen(){
            $("#filterform").submit();
        }

        function verifyFilterForm(f){
            if(f.traitid.value === ""){
                alert("An occurrence trait must be selected");
                return false;
            }
            if(f.taxonfilter.value !== "" && f.tidfilter.value === ""){
                alert("Taxon filter not syncronized with thesaurus");
                return false;
            }
            return true;
        }

        function verifyReviewForm(f){
            if(f.traitid.value === ""){
                alert("An occurrence trait must be selected");
                return false;
            }
            return true;
        }

        function nextReviewRecord(startValue){
            const f = document.getElementById("reviewform");
            f.start.value = startValue;
            f.submit();
        }
    </script>
    <script src="../../js/collections.traitattr.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    if($isEditor === 2){
        echo '<div style="float:right;margin:0 3px;">';
        if($mode === 1){
            echo '<a href="occurattributes.php?collid='.$collid.'&mode=2&traitid='.$traitID.'"><i style="height:15px;width:15px;" class="far fa-edit"></i></a>';
        }
        else{
            echo '<a href="occurattributes.php?collid='.$collid.'&mode=1&traitid='.$traitID.'"><i style="height:15px;width:15px;" class="far fa-edit"></i></a>';
        }
        echo '</div>';
    }
    ?>
    <div class="navpath">
        <a href="../../index.php">Home</a> &gt;&gt;
        <a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
        <?php
        if($mode === 2){
            echo '<b>Attribute Reviewer</b>';
        }
        else{
            echo '<b>Attribute Editor</b>';
        }
        ?>
    </div>
    <?php
    if($statusStr){
        echo '<div style="color:red">';
        echo $statusStr;
        echo '</div>';
    }
    ?>
    <div id="innertext" style="position:relative;">
    <?php
    if($collid){
        ?>
        <div style="float:right;width:290px;">
            <?php
            $attrNameArr = $attrManager->getTraitNames();
            if($mode === 1){
                ?>
                <fieldset style="margin-top:25px">
                    <legend><b>Filter</b></legend>
                    <form id="filterform" name="filterform" method="post" action="occurattributes.php" onsubmit="return verifyFilterForm(this)" >
                        <div>
                            <b>Taxon: </b>
                            <input id="taxonfilter" name="taxonfilter" type="text" value="<?php echo $taxonFilter; ?>" />
                            <input id="tidfilter" name="tidfilter" type="hidden" value="<?php echo $tidFilter; ?>" />
                        </div>
                        <div>
                            <select name="traitid">
                                <option value="">Select Trait</option>
                                <option value="">------------------------------------</option>
                                <?php
                                if($attrNameArr){
                                    foreach($attrNameArr as $ID => $aName){
                                        echo '<option value="'.$ID.'" '.($traitID === $ID?'SELECTED':'').'>'.$aName.'</option>';
                                    }
                                }
                                else{
                                    echo '<option value="0">No attributes are available</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                            <input id="panex1" name="panex" type="hidden" value="<?php echo $paneX; ?>" />
                            <input id="paney1" name="paney" type="hidden" value="<?php echo $paneY; ?>" />
                            <input id="imgres1"  name="imgres" type="hidden" value="<?php echo $imgRes; ?>" />
                            <input id="filtersubmit" name="submitform" type="submit" value="Load Images" />
                            <span id="verify-span" style="display:none;font-weight:bold;color:green;">verifying taxonomy...</span>
                            <span id="notvalid-span" style="display:none;font-weight:bold;color:red;">taxon not valid...</span>
                        </div>
                        <div style="margin:10px">
                            <?php
                            if($traitID) {
                                echo '<b>Target Occurrences:</b> ' . $attrManager->getSpecimenCount();
                            }
                            ?>
                        </div>
                    </form>
                </fieldset>
            <?php
            }
            elseif($mode === 2){
                ?>
                <fieldset style="margin-top:25px">
                    <legend><b>Reviewer</b></legend>
                    <form id="reviewform" name="reviewform" method="post" action="occurattributes.php" onsubmit="return verifyReviewForm(this)" >
                        <div style="margin:3px">
                            <select name="traitid">
                                <option value="">Select Trait</option>
                                <option value="">------------------------------------</option>
                                <?php
                                if($attrNameArr){
                                    foreach($attrNameArr as $ID => $aName){
                                        echo '<option value="'.$ID.'" '.($traitID === $ID?'SELECTED':'').'>'.$aName.'</option>';
                                    }
                                }
                                else{
                                    echo '<option value="0">No attributes are available</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin:3px">
                            <select name="reviewuid">
                                <option value="">All Editors</option>
                                <option value="">-----------------------</option>
                                <?php
                                $editorArr = $attrManager->getEditorArr();
                                foreach($editorArr as $uid => $name){
                                    echo '<option value="'.$uid.'" '.($uid === $reviewUid?'SELECTED':'').'>'.$name.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin:3px">
                            <select name="reviewdate">
                                <option value="">All Dates</option>
                                <option value="">-----------------------</option>
                                <?php
                                $dateArr = $attrManager->getEditDates();
                                foreach($dateArr as $date){
                                    echo '<option '.($date === $reviewDate?'SELECTED':'').'>'.$date.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin:3px">
                            <select name="reviewstatus">
                                <option value="0">Not reviewed</option>
                                <option value="5" <?php echo  ($reviewStatus === 5?'SELECTED':''); ?>>Expert Needed</option>
                                <option value="10" <?php echo  ($reviewStatus === 10?'SELECTED':''); ?>>Reviewed</option>
                            </select>
                        </div>
                        <div style="margin:10px;">
                            <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                            <input id="panex1" name="panex" type="hidden" value="<?php echo $paneX; ?>" />
                            <input id="paney1" name="paney" type="hidden" value="<?php echo $paneY; ?>" />
                            <input id="imgres1" name="imgres" type="hidden" value="<?php echo $imgRes; ?>" />
                            <input name="mode" type="hidden" value="2" />
                            <input name="start" type="hidden" value="" />
                            <input name="submitform" type="submit" value="Get Images" />
                        </div>
                        <div>
                            <?php
                            if($traitID){
                                $rCnt = $attrManager->getReviewCount($traitID, $reviewUid, $reviewDate, $reviewStatus);
                                echo '<b>'.($rCnt?$start+1:0).' of '.$rCnt.' records</b>';
                                if($rCnt > 1){
                                    $next = ($start+1);
                                    if($next >= $rCnt) {
                                        $next = 0;
                                    }
                                    echo ' (<a href="#" onclick="nextReviewRecord('.($next).')">Next record &gt;&gt;</a>)';
                                }
                            }
                            ?>
                        </div>
                    </form>
                </fieldset>
                <?php
            }
            if($imgArr){
                $traitArr = $attrManager->getTraitArr($traitID,($mode === 2));
                ?>
                <fieldset style="margin-top:20px">
                    <legend><b>Action Panel - <?php echo $traitArr[$traitID]['name']; ?></b></legend>
                    <form name="submitform" method="post" action="occurattributes.php">
                        <div>
                            <?php
                            $attrManager->echoFormTraits($traitID);
                            ?>
                        </div>
                        <div style="margin-left:5px;">
                            Status:
                            <select name="setstatus">
                                <?php
                                if($mode === 2){
                                    ?>
                                    <option value="0">Not reviewed</option>
                                    <option value="5">Expert Needed</option>
                                    <option value="10" selected>Reviewed</option>
                                    <?php
                                }
                                else{
                                    ?>
                                    <option value="0">---------------</option>
                                    <option value="5">Expert Needed</option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin:20px">
                            <input name="taxonfilter" type="hidden" value="<?php echo $taxonFilter; ?>" />
                            <input name="tidfilter" type="hidden" value="<?php echo $tidFilter; ?>" />
                            <input name="traitid" type="hidden" value="<?php echo $traitID; ?>" />
                            <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                            <input id="panex2" name="panex" type="hidden" value="<?php echo $paneX; ?>" />
                            <input id="paney2" name="paney" type="hidden" value="<?php echo $paneY; ?>" />
                            <input id="imgres2" name="imgres" type="hidden" value="<?php echo $imgRes; ?>" />
                            <input name="targetoccid" type="hidden" value="<?php echo $occid; ?>" />
                            <input name="mode" type="hidden" value="<?php echo $mode; ?>" />
                            <input name="reviewuid" type="hidden" value="<?php echo $reviewUid; ?>" />
                            <input name="reviewdate" type="hidden" value="<?php echo $reviewDate; ?>" />
                            <input name="reviewstatus" type="hidden" value="<?php echo $reviewStatus; ?>" />
                            <?php
                            if($mode === 2){
                                echo '<input name="submitform" type="submit" value="Set Status and Save" />';
                            }
                            else{
                                echo '<input name="submitform" type="submit" value="Save and Next" disabled />';
                            }
                            ?>
                        </div>
                    </form>
                </fieldset>
                <?php
            }
            ?>
        </div>
        <div style="height:600px">
            <?php
            if($imgArr){
                ?>
                <div>
                    <span><input id="imgresmed" name="resradio"  type="radio" checked onchange="changeImgRes('med')" />Med Res.</span>
                    <span style="margin-left:6px;"><input id="imgreslg" name="resradio" type="radio" onchange="changeImgRes('lg')" />High Res.</span>
                    <?php
                    if($occid){
                        if(!$catNum) {
                            $catNum = 'Occurrence Details';
                        }
                        echo '<span style="margin-left:50px;">';
                        echo '<a href="../individual/index.php?occid='.$occid.'" target="_blank" title="Occurrence Details">'.$catNum.'</a>';
                        echo '</span>';
                    }
                    $imgTotal = count($imgArr);
                    if($imgTotal > 1) {
                        echo '<span id="labelcnt" style="margin-left:60px;">1</span> of ' . $imgTotal . ' images ' . ($imgTotal > 1 ? '<a href="#" onclick="nextImage()">&gt;&gt; next</a>' : '');
                    }
                    if($occid && $mode !== 2) {
                        echo '<span style="margin-left:80px" title="Skip Occurrence"><a href="#" onclick="skipSpecimen()">SKIP &gt;&gt;</a></span>';
                    }
                    ?>
                </div>
                <div>
                    <?php
                    $url = $imgArr[1]['web'];
                    if(strncmp($url, '/', 1) === 0) {
                        $url = $imgDomain . $url;
                    }
                    echo '<img id="specimg" src="'.$url.'" />';
                    ?>
                </div>
                <?php
            }
            else if($submitForm) {
                echo '<div style="margin:50px;color:red;font-weight:bold;">No images available matching taxon search criteria</div>';
            }
            else {
                echo '<div style="margin:50px;font-weight:bold;">Select a trait and submit filter in the form to the right to display images that have not yet been scored</div>';
            }
            ?>
        </div>
        <?php
    }
    else{
        echo '<div><b>ERROR: collection identifier is not set</b></div>';
    }
    ?>
    </div>
    <?php
    include(__DIR__ . '/../../footer.php');
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
</body>
</html>
