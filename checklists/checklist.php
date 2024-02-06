<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistManager.php');
include_once(__DIR__ . '/../classes/ChecklistAdmin.php');
include_once(__DIR__ . '/../classes/ChecklistFGExportManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']): '';
$clValue = array_key_exists('cl',$_REQUEST)?(int)$_REQUEST['cl']:0;
$dynClid = array_key_exists('dynclid',$_REQUEST)?(int)$_REQUEST['dynclid']:0;
$pageNumber = array_key_exists('pagenumber',$_REQUEST)?(int)$_REQUEST['pagenumber']:1;
$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']): '';
$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?htmlspecialchars($_REQUEST['taxonfilter']): '';
$thesFilter = array_key_exists('thesfilter',$_REQUEST)?(int)$_REQUEST['thesfilter']:0;
$showSynonyms = array_key_exists('showsynonyms',$_REQUEST)?(int)$_REQUEST['showsynonyms']:0;
$showAuthors = array_key_exists('showauthors',$_REQUEST)?(int)$_REQUEST['showauthors']:0;
$showCommon = array_key_exists('showcommon',$_REQUEST)?(int)$_REQUEST['showcommon']:0;
$showImages = array_key_exists('showimages',$_REQUEST)?(int)$_REQUEST['showimages']:0;
$showVouchers = array_key_exists('showvouchers',$_REQUEST)?(int)$_REQUEST['showvouchers']:0;
$showAlphaTaxa = array_key_exists('showalphataxa',$_REQUEST)?(int)$_REQUEST['showalphataxa']:0;
$searchCommon = array_key_exists('searchcommon',$_REQUEST)?(int)$_REQUEST['searchcommon']:0;
$searchSynonyms = array_key_exists('searchsynonyms',$_REQUEST)?(int)$_REQUEST['searchsynonyms']:0;
$defaultOverride = array_key_exists('defaultoverride',$_REQUEST)?(int)$_REQUEST['defaultoverride']:0;
$editMode = array_key_exists('emode',$_REQUEST)?(int)$_REQUEST['emode']:0;
$printMode = array_key_exists('printmode',$_REQUEST)?(int)$_REQUEST['printmode']:0;

$statusStr='';
$locStr = '';
$isEditor = false;
$taxaArray = array();

if($action !== 'Rebuild List' && $action !== 'Download List') {
    $searchSynonyms = 1;
}
if($action === 'Rebuild List') {
    $defaultOverride = 1;
}

$clManager = new ChecklistManager();
$fgManager = new ChecklistFGExportManager();
if($clValue){
    $statusStr = $clManager->setClValue($clValue);
}
elseif($dynClid){
    $clManager->setDynClid($dynClid);
}
$clArray = array();
if($clValue || $dynClid){
    $clArray = $clManager->getClMetaData();
}
$activateKey = (array_key_exists('KEY_MOD_IS_ACTIVE',$GLOBALS) && $GLOBALS['KEY_MOD_IS_ACTIVE']);
$showDetails = 0;
$clid = 0;
if($clArray){
    if(array_key_exists('defaultSettings',$clArray) && $clArray['defaultSettings']){
        $defaultArr = json_decode($clArray['defaultSettings'], true);
        $showDetails = $defaultArr['ddetails'];
        if(!$defaultOverride){
            if(array_key_exists('thesfilter',$defaultArr)){
                $thesFilter = $defaultArr['thesfilter'];
            }
            if(array_key_exists('showsynonyms',$defaultArr)){
                $showSynonyms = $defaultArr['showsynonyms'];
            }
            if(array_key_exists('dcommon',$defaultArr)){
                $showCommon = $defaultArr['dcommon'];
            }
            if(array_key_exists('dimages',$defaultArr)){
                $showImages = $defaultArr['dimages'];
            }
            if(array_key_exists('dvouchers',$defaultArr)){
                $showVouchers = $defaultArr['dvouchers'];
            }
            if(array_key_exists('dauthors',$defaultArr)){
                $showAuthors = $defaultArr['dauthors'];
            }
            if(array_key_exists('dalpha',$defaultArr)){
                $showAlphaTaxa = $defaultArr['dalpha'];
            }
        }
        if(isset($defaultArr['activatekey'])) {
            $activateKey = $defaultArr['activatekey'];
        }
    }
    if($pid) {
        $clManager->setProj($pid);
    }
    elseif(array_key_exists('proj',$_REQUEST)) {
        $pid = $clManager->setProj($_REQUEST['proj']);
    }
    if($taxonFilter) {
        $clManager->setTaxonFilter($taxonFilter);
    }
    if($searchCommon){
        $showCommon = 1;
        $clManager->setSearchCommon();
    }
    if($searchSynonyms) {
        $clManager->setSearchSynonyms();
    }
    if($thesFilter) {
        $clManager->setThesFilter();
    }
    if($showSynonyms) {
        $clManager->setShowSynonyms();
    }
    if($showAuthors) {
        $clManager->setShowAuthors();
    }
    if($showCommon) {
        $clManager->setShowCommon();
    }
    if($showImages) {
        $clManager->setShowImages();
    }
    if($showVouchers) {
        $clManager->setShowVouchers();
    }
    if($showAlphaTaxa) {
        $clManager->setShowAlphaTaxa();
    }
    $clid = $clManager->getClid();
    $pid = $clManager->getPid();

    if($action === 'Download List') {
        $clManager->downloadChecklistCsv();
        exit();
    }

    if($action === 'Print List') {
        $printMode = 1;
    }

    if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
        $isEditor = true;

        if(array_key_exists('tidtoadd',$_POST)){
            $dataArr = array();
            $dataArr['tid'] = $_POST['tidtoadd'];
            if($_POST['familyoverride']) {
                $dataArr['familyoverride'] = $_POST['familyoverride'];
            }
            if($_POST['habitat']) {
                $dataArr['habitat'] = $_POST['habitat'];
            }
            if($_POST['abundance']) {
                $dataArr['abundance'] = $_POST['abundance'];
            }
            if($_POST['notes']) {
                $dataArr['notes'] = $_POST['notes'];
            }
            if($_POST['source']) {
                $dataArr['source'] = $_POST['source'];
            }
            if($_POST['internalnotes']) {
                $dataArr['internalnotes'] = $_POST['internalnotes'];
            }
            $setRareSpp = false;
            if($_POST['cltype'] === 'rarespp') {
                $setRareSpp = true;
            }
            $clAdmin = new ChecklistAdmin();
            $clAdmin->setClid($clid);
            $statusStr = $clAdmin->addNewSpecies($dataArr,$setRareSpp);
        }
    }
    if($clValue || $dynClid){
        $taxaArray = $clManager->getTaxaList($pageNumber,($printMode?0:500));
        if($GLOBALS['CHECKLIST_FG_EXPORT']){
            if($clValue){
                $fgManager->setClValue($clValue);
            }
            elseif($dynClid){
                $fgManager->setDynClid($dynClid);
            }
            $fgManager->setSqlVars();
            $fgManager->primeDataArr();
        }
    }
    if($clArray['locality']){
        $locStr = $clArray['locality'];
        if($clValue && $clArray['latcentroid']) {
            $locStr .= ' (' . $clArray['latcentroid'] . ', ' . $clArray['longcentroid'] . ')';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Research Checklist: <?php echo $clManager->getClName(); ?></title>
    <link type="text/css" href="../css/external/bootstrap.min.css?ver=20221225" rel="stylesheet" />
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.popupoverlay.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
    <script type="text/javascript">
        <?php
        if($clid) {
            echo 'var clid = ' . $clid . ';';
        }
        ?>

        const checklistName = "<?php echo $clManager->getClName(); ?>";
        const checklistAuthors = "<?php echo (array_key_exists('authors',$clArray)?$clArray['authors']:''); ?>";
        const checklistCitation = "<?php echo (array_key_exists('publication',$clArray)?$clArray['publication']:''); ?>";
        const checklistLocality = "<?php echo $locStr; ?>";
        const checklistAbstract = "<?php echo (array_key_exists('abstract',$clArray)?$clArray['abstract']:''); ?>";
        const checklistNotes = "<?php echo (array_key_exists('notes',$clArray)?$clArray['notes']:''); ?>";
        const fieldguideDisclaimer = "This field guide was produced through the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal. This field guide is intended for educational use only, no commercial uses are allowed. It is created under Fair Use copyright provisions supporting educational uses of information. All rights are reserved to authors and photographers unless otherwise specified.";

        function lazyLoadData(index,callback){
            let startindex = 0;
            if(index > 0) startindex = (index*lazyLoadCnt) + 1;
            const http = new XMLHttpRequest();
            const url = "../api/checklists/fieldguideexporter.php";
            const params = 'rows=' + lazyLoadCnt + '&photogArr=' + encodeURIComponent(JSON.stringify(photog)) + '&photoNum=' + photoNum + '&start=' + startindex + '&cl=<?php echo $clValue . '&pid=' . $pid . '&dynclid=' . $dynClid; ?>';
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    callback(http.responseText);
                }
            };
            http.send(params);
        }

        function setPopup(tid,clid){
            const starrObj = {
                usethes: true,
                taxa: tid,
                targetclid: clid
            };
            const url = '../collections/list.php?starr=' + JSON.stringify(starrObj) + '&targettid=' + clid;
            openPopup(url);
        }
    </script>
    <script type="text/javascript" src="../js/checklists.checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>"></script>
    <?php
    if($GLOBALS['CHECKLIST_FG_EXPORT']){
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/pdfmake.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/vfs_fonts.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jszip.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/FileSaver.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/checklists.fieldguideexport.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <?php
    }
    ?>
    <style>
        a.boxclose{
            float:right;
            width:36px;
            height:36px;
            background:transparent url(../images/spatial_close_icon.png) repeat top left;
            margin-top:-35px;
            margin-right:-35px;
            cursor:pointer;
        }

        #loaderMessage {
            position: absolute;
            top: 65%;
            z-index: 1;
            font-weight: bold;
            text-align: center;
            width: 100%;
            color: #f3f3f3;
        }

        .checklist-header-row {
            display: flex;
            justify-content: space-between;
        }

        .checklist-header-element {
            display: flex;
            flex-direction: row;
            gap: 10px;
            text-decoration: none;
            align-items: center;
            align-content: center;
        }

        h1 {
            margin: 15px 0 20px;
        }
        h3 {
            margin: 0;
        }
    </style>
</head>

<body <?php echo ($printMode?'style="background-color:#ffffff;"':''); ?>>
<?php
if(!$printMode){
    include(__DIR__ . '/../header.php');
    echo '<div class="navpath">';
    echo '<a href="../index.php">Home</a> &gt;&gt; ';
    if($pid){
        echo '<a href="'.$GLOBALS['CLIENT_ROOT'].'/projects/index.php?pid='.$pid.'">';
        echo $clManager->getProjName();
        echo '</a> &gt;&gt; ';
    }
    else{
        echo '<a href="index.php">Checklists</a> &gt;&gt; ';
    }
    echo '<b>'.$clManager->getClName().'</b>';
    echo '</div>';
}
?>
<div id='innertext' style="<?php echo ($printMode?'background-color:#ffffff;':''); ?>">
    <?php
    if($clValue || $dynClid){
        echo '<div class="checklist-header-row">';
        echo '<div class="checklist-header-element">';
        ?>
        <div style="color:#990000;">
            <a href="checklist.php?cl=<?php echo $clValue. '&proj=' .$pid. '&dynclid=' .$dynClid; ?>">
                <h1><?php echo $clManager->getClName(); ?></h1>
            </a>
        </div>
        <?php
        if($activateKey && !$printMode){
            ?>
            <div>
                <a href="../ident/key.php?cl=<?php echo $clValue. '&proj=' .$pid. '&dynclid=' .$dynClid;?>&taxon=All+Species">
                    <i style='width:15px; height:15px;' class="fas fa-key"></i>
                </a>
            </div>
            <?php
        }
        if(!$printMode && $taxaArray){
            ?>
            <div>
                <?php
                $varStr = '?clid=' .$clid. '&dynclid=' .$dynClid. '&listname=' .$clManager->getClName(). '&taxonfilter=' .$taxonFilter. '&showcommon=' .$showCommon. '&thesfilter=' .$thesFilter. '&showsynonyms=' .$showSynonyms;
                ?>
                <a href="../games/flashcards.php<?php echo $varStr; ?>"><i class="fas fa-gamepad"></i></a>
            </div>
            <?php
        }
        echo '</div>';
        if($clValue && $isEditor && !$printMode){
            ?>
            <div class="checklist-header-element">
                <span>
                    <a href="checklistadmin.php?clid=<?php echo $clid.'&pid='.$pid; ?>" title="Checklist Administration">
                        <i style='width:20px;height:20px;' class="fas fa-cog"></i>
                    </a>
                </span>
                <span>
                    <a href="voucheradmin.php?clid=<?php echo $clid.'&pid='.$pid; ?>" title="Manage Linked Voucher">
                        <i style='width:20px;height:20px;' class="fas fa-link"></i>
                    </a>
                </span>
                <span onclick="toggle('editspp');">
                    <i style='width:20px;height:20px;cursor:pointer;' class="fas fa-clipboard-list" title="Edit Species List"></i>
                </span>
            </div>
            <?php
        }
        echo '</div>';
        if($clValue){
            if($clArray['type'] === 'rarespp'){
                echo '<div style="clear:both;">';
                echo '<b>Sensitive species checklist for:</b> '.$clArray['locality'];
                echo '</div>';
            }
            if($clArray['authors']){
                echo '<div style="clear:both;">';
                echo '<h3>Authors:</h3>';
                echo $clArray['authors'];
                echo '</div>';
            }
            if($clArray['publication']){
                $pubStr = $clArray['publication'];
                if($pubStr && strncmp($pubStr, 'http', 4) === 0 && !strpos($pubStr,' ')) {
                    $pubStr = '<a href="' . $pubStr . '" target="_blank">' . $pubStr . '</a>';
                }
                echo "<div><span style='font-weight:bold;'>Citation:</span> ".$pubStr. '</div>';
            }
            if(($locStr || $clArray['latcentroid'] || $clArray['abstract'] || $clArray['notes'])){
                ?>
                <div class="moredetails" style="<?php echo (($showDetails || $printMode)?'display:none;':''); ?>color:blue;cursor:pointer;" onclick="toggle('moredetails')">More Details</div>
                <div class="moredetails" style="display:<?php echo (($showDetails && !$printMode)?'block':'none'); ?>;color:blue;cursor:pointer;" onclick="toggle('moredetails')">Less Details</div>
                <div class="moredetails" style="display:<?php echo (($showDetails || $printMode)?'block':'none'); ?>;">
                    <?php
                    if($locStr){
                        echo "<div><span style='font-weight:bold;'>Locality: </span>".$locStr. '</div>';
                    }
                    if($clValue && $clArray['abstract']){
                        echo "<div><span style='font-weight:bold;'>Abstract: </span>".$clArray['abstract']. '</div>';
                    }
                    echo ($clValue && $clArray['notes']) ? "<div><span style='font-weight:bold;'>Notes: </span>".$clArray['notes']. '</div>' : '';
                    ?>
                </div>
                <?php
            }
        }
        if($statusStr){
            ?>
            <hr />
            <div style="margin:20px;font-weight:bold;color:red;">
                <?php echo $statusStr; ?>
            </div>
            <hr />
            <?php
        }
        ?>
        <div>
            <hr/>
        </div>
        <div>
            <?php
            if(!$printMode){
                ?>
                <div id="cloptiondiv">
                    <form name="optionform" action="checklist.php" method="post">
                        <fieldset style="background-color:white;padding-bottom:10px;">
                            <legend><b>Options</b></legend>
                            <div id="taxonfilterdiv" title="Filter species list by family or genus">
                                <div>
                                    <b>Search:</b>
                                    <input type="text" id="taxonfilter" name="taxonfilter" value="<?php echo $taxonFilter;?>" size="20" />
                                </div>
                                <div>
                                    <div style="margin-left:10px;">
                                        <input type='checkbox' name='searchcommon' value='1' <?php echo ($searchCommon? 'checked' : '');?> /> Common Names<br/>
                                        <input type="checkbox" name="searchsynonyms" value="1" <?php echo ($searchSynonyms? 'checked' : '');?> /> Synonyms
                                    </div>
                                </div>
                            </div>
                            <div>
                                <input id='thesfilter' name='thesfilter' type='checkbox' value='1' <?php echo ($thesFilter ? 'checked' : '');?> /> Filter Through Thesaurus
                            </div>
                            <div>
                                <input id='showsynonyms' name='showsynonyms' type='checkbox' value='1' <?php echo ($showSynonyms ? 'checked' : '');?> /> Display Synonyms
                            </div>
                            <div>
                                <input id='showcommon' name='showcommon' type='checkbox' value='1' <?php echo ($showCommon ? 'checked' : '');?> /> Common Names
                            </div>
                            <div>
                                <input id='showimages' name='showimages' type='checkbox' value='1' <?php echo ($showImages? 'checked' : ''); ?> onclick="showImagesChecked(this.form);" />
                                Display as Images
                            </div>
                            <?php
                            if($clValue){
                                ?>
                                <div style='display:<?php echo ($showImages? 'none' : 'block');?>' id="showvouchersdiv">
                                    <input name='showvouchers' type='checkbox' value='1' <?php echo ($showVouchers? 'checked' : ''); ?> />
                                    Notes &amp; Vouchers
                                </div>
                                <?php
                            }
                            ?>
                            <div style='display:<?php echo ($showImages? 'none' : 'block');?>' id="showauthorsdiv">
                                <input name='showauthors' type='checkbox' value='1' <?php echo ($showAuthors? 'checked' : ''); ?> />
                                Taxon Authors
                            </div>
                            <div style='' id="showalphataxadiv">
                                <input name='showalphataxa' type='checkbox' value='1' <?php echo ($showAlphaTaxa? 'checked' : ''); ?> />
                                Show Taxa Alphabetically
                            </div>
                            <div style="margin:5px 0 0 5px;">
                                <input type='hidden' name='cl' value='<?php echo $clid; ?>' />
                                <input type='hidden' name='dynclid' value='<?php echo $dynClid; ?>' />
                                <input type="hidden" name="proj" value="<?php echo $pid; ?>" />
                                <input type='hidden' name='defaultoverride' value='1' />
                                <?php
                                if(!$taxonFilter) {
                                    echo "<input type='hidden' name='pagenumber' value='" . $pageNumber . "' />";
                                }
                                ?>
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <input type="submit" name="submitaction" value="Rebuild List" onclick="changeOptionFormAction('checklist.php?cl=<?php echo $clValue. '&proj=' .$pid. '&dynclid=' .$dynClid; ?>','_self');" />
                                    <div style="width:100px;display:flex;justify-content:flex-end;align-items:center;">
                                        <div id="wordicondiv" style="margin-right:5px;">
                                            <button class="icon-button" type="submit" name="submitaction" value="Export to DOCX" style="margin:0;padding:2px;" title="Export to DOCX" onclick="changeOptionFormAction('defaultchecklistexport.php','_self');">
                                                <i style="height:15px;width:15px;" class="far fa-file-word"></i>
                                            </button>
                                        </div>
                                        <div style="margin-right:5px;">
                                            <button class="icon-button" type="submit" name="submitaction" value="Print List" style="margin:0;padding:2px;" title="Print in Browser" onclick="changeOptionFormAction('checklist.php','_blank');">
                                                <i style="height:15px;width:15px;" class="fas fa-print"></i>
                                            </button>
                                        </div>
                                        <div style="margin-right:5px;">
                                            <button class="icon-button" type="submit" name="submitaction" value="Download List" style="margin:0;padding:2px;" title="Download List" onclick="changeOptionFormAction('checklist.php?cl=<?php echo $clValue. '&proj=' .$pid. '&dynclid=' .$dynClid; ?>','_self');">
                                                <i style="height:15px;width:15px;" class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if($GLOBALS['CHECKLIST_FG_EXPORT']){
                                ?>
                                <div style="margin:5px 0 0 5px;clear:both;">
                                    <a class="" href="#" onclick="openFieldGuideExporter();"><b>Open Export Panel</b></a>
                                </div>
                                <?php
                            }
                            ?>
                        </fieldset>
                    </form>
                    <?php
                    if($clValue && $isEditor){
                        ?>
                        <div class="editspp" style="display:<?php echo ($editMode?'block':'none'); ?>;width:250px;margin-top:10px;">
                            <form id='addspeciesform' action='checklist.php' method='post' name='addspeciesform' onsubmit="return validateAddSpecies(this);">
                                <fieldset style='margin:5px 0 5px 5px;background-color:#FFFFCC;'>
                                    <legend><b>Add New Species to Checklist</b></legend>
                                    <div>
                                        <b>Taxon:</b><br/>
                                        <input type="text" id="speciestoadd" name="speciestoadd" style="width:174px;" />
                                        <input type="hidden" id="tidtoadd" name="tidtoadd" value="" />
                                    </div>
                                    <div>
                                        <b>Family Override:</b><br/>
                                        <input type="text" name="familyoverride" style="width:122px;" title="Only enter if you want to override current family" />
                                    </div>
                                    <div>
                                        <b>Habitat:</b><br/>
                                        <input type="text" name="habitat" style="width:170px;" />
                                    </div>
                                    <div>
                                        <b>Abundance:</b><br/>
                                        <input type="text" name="abundance" style="width:145px;" />
                                    </div>
                                    <div>
                                        <b>Notes:</b><br/>
                                        <input type="text" name="notes" style="width:175px;" />
                                    </div>
                                    <div style="padding:2px;">
                                        <b>Internal Notes:</b><br/>
                                        <input type="text" name="internalnotes" style="width:126px;" title="Displayed to administrators only" />
                                    </div>
                                    <div>
                                        <b>Source:</b><br/>
                                        <input type="text" name="source" style="width:167px;" />
                                    </div>
                                    <div>
                                        <input type="hidden" name="cl" value="<?php echo $clid; ?>" />
                                        <input type="hidden" name="cltype" value="<?php echo $clArray['type']; ?>" />
                                        <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                                        <input type='hidden' name='thesfilter' value='<?php echo $thesFilter; ?>' />
                                        <input type='hidden' name='showsynonyms' value='<?php echo $showSynonyms; ?>' />
                                        <input type='hidden' name='showcommon' value='<?php echo $showCommon; ?>' />
                                        <input type='hidden' name='showvouchers' value='<?php echo $showVouchers; ?>' />
                                        <input type='hidden' name='showauthors' value='<?php echo $showAuthors; ?>' />
                                        <input type='hidden' name='taxonfilter' value='<?php echo $taxonFilter; ?>' />
                                        <input type='hidden' name='searchcommon' value='<?php echo $searchCommon; ?>' />
                                        <input type="hidden" name="emode" value="1" />
                                        <input type="submit" name="submitadd" value="Add Species to List"/>
                                        <hr />
                                    </div>
                                    <div style="text-align:center;">
                                        <a href="tools/checklistloader.php?clid=<?php echo $clid.'&pid='.$pid;?>">Batch Upload Spreadsheet</a>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>
            <div style="min-height: 450px;">
                <div style="margin-bottom:15px;">
                    <div style="margin:3px;">
                        <h3>Families: <?php echo $clManager->getFamilyCount(); ?></h3>
                    </div>
                    <div style="margin:3px;">
                        <h3>Genera: <?php echo $clManager->getGenusCount(); ?></h3>
                    </div>
                    <div style="margin:3px;">
                        <h3>Species: <?php echo $clManager->getSpeciesCount(); ?></h3>
                    </div>
                    <div style="margin:3px;">
                        <h3>Total Taxa: <?php echo $clManager->getTaxaCount(); ?> (including subsp. and var.)</h3>
                    </div>
                </div>
                <?php
                $taxaLimit = ($showImages?$clManager->getImageLimit():$clManager->getTaxaLimit());
                $pageCount = ceil($clManager->getTaxaCount()/$taxaLimit);
                $argStr = '';
                if($pageCount > 1 && !$printMode){
                    if(($pageNumber)>$pageCount) {
                        $pageNumber = 1;
                    }
                    $argStr .= '&cl=' .$clValue. '&dynclid=' .$dynClid.($showCommon? '&showcommon=' .$showCommon: '').($showVouchers? '&showvouchers=' .$showVouchers: '');
                    $argStr .= ($thesFilter? '&thesfilter=' .$thesFilter: '');
                    $argStr .= ($showSynonyms? '&showsynonyms=' .$showSynonyms: '');
                    $argStr .= ($showAuthors? '&showauthors=' .$showAuthors: '');
                    $argStr .= ($pid? '&pid=' .$pid: '').($showImages? '&showimages=' .$showImages: '').($taxonFilter? '&taxonfilter=' .$taxonFilter: '');
                    $argStr .= ($searchCommon? '&searchcommon=' .$searchCommon: '').($searchSynonyms? '&searchsynonyms=' .$searchSynonyms: '');
                    $argStr .= ($showAlphaTaxa? '&showalphataxa=' .$showAlphaTaxa: '');
                    $argStr .= ($defaultOverride? '&defaultoverride=' .$defaultOverride: '');
                    echo '<hr /><div>Page<b>' .($pageNumber)."</b> of <b>$pageCount</b>: ";
                    for($x=1; $x <= $pageCount; $x++){
                        if($x>1) {
                            echo ' | ';
                        }
                        if($pageNumber === $x){
                            echo '<b>';
                        }
                        else{
                            echo "<a href='checklist.php?pagenumber=".$x.$argStr."'>";
                        }
                        echo ($x);
                        if($pageNumber === $x){
                            echo '</b>';
                        }
                        else{
                            echo '</a>';
                        }
                    }
                    echo '</div><hr />';
                }
                $prevfam = '';
                if($showImages){
                    echo '<div style="clear:both;display:flex;flex-direction:row;flex-wrap:wrap;gap:20px;">';
                    foreach($taxaArray as $tid => $sppArr){
                        $tu = (array_key_exists('tnurl',$sppArr)?$sppArr['tnurl']:'');
                        $u = (array_key_exists('url',$sppArr)?$sppArr['url']:'');
                        $imgSrc = ($tu?:$u);
                        ?>
                        <div class="tndiv">
                            <div class="tnimg" style="<?php echo ($imgSrc? '' : 'border:1px solid black;'); ?>">
                                <?php
                                $spUrl = "../taxa/index.php?taxon=$tid&cl=".$clid;
                                if($imgSrc){
                                    if(!$printMode) {
                                        echo "<a href='" . $spUrl . "' target='_blank'>";
                                    }
                                    echo "<img src='".$imgSrc."' />";
                                    if(!$printMode) {
                                        echo '</a>';
                                    }
                                }
                                else{
                                    ?>
                                    <div style="margin-top:50px;">
                                        <b>Image<br/>not yet<br/>available</b>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div>
                                <?php
                                if(!$printMode) {
                                    echo '<a href="' . $spUrl . '" target="_blank">';
                                }
                                echo '<b>'.$sppArr['sciname'].'</b>';
                                if(!$printMode) {
                                    echo '</a>';
                                }
                                if(array_key_exists('vern',$sppArr)){
                                    echo "<div style='font-weight:bold;'>".$sppArr['vern']. '</div>';
                                }
                                if($isEditor){
                                    ?>
                                    <span class="editspp" style="display:<?php echo ($editMode?'inline':'none'); ?>;">
                                        <i style='width:13px;height:13px;cursor:pointer;' title='edit details' class="fas fa-edit" onclick="openPopup('clsppeditor.php?tid=<?php echo $tid. '&clid=' .$clid; ?>');"></i>
                                    </span>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    echo '</div>';
                }
                else{
                    $voucherArr = $clManager->getVoucherArr();
                    foreach($taxaArray as $tid => $sppArr){
                        if(!$showAlphaTaxa){
                            $family = $sppArr['family'];
                            if($family !== $prevfam){
                                $famUrl = "../taxa/index.php?taxon=$family&cl=".$clid;
                                ?>
                                <div class="familydiv" id="<?php echo $family;?>" style="margin:15px 0 5px 0;font-weight:bold;">
                                    <a href="<?php echo $famUrl; ?>" target="_blank" style="color:black;"><?php echo $family;?></a>
                                </div>
                                <?php
                                $prevfam = $family;
                            }
                        }
                        $spUrl = "../taxa/index.php?taxon=$tid&cl=".$clid;
                        echo "<div id='tid-$tid' style='margin:0 0 3px 10px;'>";
                        echo '<div style="clear:left">';
                        if(!$printMode && !preg_match('/\ssp\d/',$sppArr['sciname'])) {
                            echo "<a href='" . $spUrl . "' target='_blank'>";
                        }
                        echo '<b><i>' .$sppArr['sciname']. '</b></i> ';
                        if(array_key_exists('author',$sppArr)) {
                            echo $sppArr['author'];
                        }
                        if(!$printMode && !preg_match('/\ssp\d/',$sppArr['sciname'])) {
                            echo '</a>';
                        }
                        if(array_key_exists('vern',$sppArr)){
                            echo " - <span style='font-weight:bold;'>".$sppArr['vern']. '</span>';
                        }
                        if($isEditor){
                            ?>
                            <span class="editspp" style="display:<?php echo ($editMode?'inline':'none'); ?>;">
                                <i style='width:13px;height:13px;cursor:pointer;' title='edit details' class="fas fa-edit" onclick="openPopup('clsppeditor.php?tid=<?php echo $tid. '&clid=' .$clid; ?>');"></i>
                            </span>
                            <?php
                            if($showVouchers && array_key_exists('dynamicsql',$clArray) && $clArray['dynamicsql']){
                                ?>
                                <span class="editspp" style="display:none;">
                                    <i style='width:13px;height:13px;cursor:pointer;' title='Link Voucher Occurrences' class="fas fa-link" onclick="setPopup(<?php echo $tid . ',' . $clid;?>);"></i>
                                </span>
                                <?php
                            }
                        }
                        echo "</div>\n";
                        if($showSynonyms && isset($sppArr['syn'])){
                            echo '<div class="syn-div">['.$sppArr['syn'].']</div>';
                        }
                        if($showVouchers){
                            $voucStr = '';
                            if(array_key_exists($tid,$voucherArr)){
                                $voucCnt = 0;
                                foreach($voucherArr[$tid] as $occid => $collName){
                                    $voucStr .= ', ';
                                    if($voucCnt === 4 && !$printMode){
                                        $voucStr .= '<a href="#" id="morevouch-'.$tid.'" onclick="return toggleVoucherDiv('.$tid. ')">more...</a>' .
                                            '<span id="voucdiv-'.$tid.'" style="display:none;">';
                                    }
                                    if(!$printMode) {
                                        $openPopupArgs = "'../collections/individual/index.php'," . $occid;
                                        $voucStr .= '<a href="#" onclick="return openIndividualPopup(' . $openPopupArgs . ')">';
                                    }
                                    $voucStr .= $collName;
                                    if(!$printMode) {
                                        $voucStr .= "</a>\n";
                                    }
                                    $voucCnt++;
                                }
                                if($voucCnt > 4 && !$printMode) {
                                    $voucStr .= '</span><a href="#" id="lessvouch-' . $tid . '" style="display:none;" onclick="return toggleVoucherDiv(' . $tid . ')">...less</a>';
                                }
                                $voucStr = substr($voucStr,2);
                            }
                            $noteStr = '';
                            if(array_key_exists('notes',$sppArr)){
                                $noteStr = $sppArr['notes'];
                            }
                            if($noteStr || $voucStr){
                                echo "<div style='margin-left:15px;'>".$noteStr.($noteStr && $voucStr?'; ':'').$voucStr. '</div>';
                            }
                        }
                        echo "</div>\n";
                    }
                }
                $taxaLimit = ($showImages?$clManager->getImageLimit():$clManager->getTaxaLimit());
                if(!$printMode && $clManager->getTaxaCount() > (($pageNumber)*$taxaLimit)){
                    echo '<div style="margin:20px;clear:both;">';
                    echo '<a href="checklist.php?pagenumber='.($pageNumber+1).$argStr.'"> Display next '.$taxaLimit.' taxa...</a></div>';
                }
                if(!$taxaArray) {
                    echo "<h2 style='margin:40px;'>No Taxa Found</h2>";
                }
                ?>
            </div>
        </div>
        <?php
    }
    else{
        ?>
        <div style="color:red;">
            ERROR: Checklist identification is null!
        </div>
        <?php
    }
    ?>
</div>
<div style="clear:both;"></div>
<?php
if(!$printMode) {
    include(__DIR__ . '/../footer.php');
}

if($GLOBALS['CHECKLIST_FG_EXPORT']){
    ?>
    <div id="fieldguideexport" data-role="popup" class="well" style="width:600px;min-height:250px;">
        <a class="boxclose fieldguideexport_close" id="boxclose"></a>
        <h2>Fieldguide Export Settings</h2>

        <div style="margin-top:5px;">
            <b>Primary Description Source:</b>
            <select data-role='none' name='fgPriDescSource' id='fgPriDescSource'>
                <?php
                $descSourceList = $fgManager->getDescSourceList();
                foreach($descSourceList as $source){
                    echo "<option value='".$source."'>".$source."</option>\n";
                }
                ?>
            </select>
        </div>
        <div style="margin-top:5px;">
            <b>Secondary Description Source:</b>
            <select data-role='none' name='fgSecDescSource' id='fgSecDescSource'>
                <?php
                foreach($descSourceList as $source){
                    echo "<option value='".$source."'>".$source."</option>\n";
                }
                ?>
            </select>
        </div>
        <div style="margin-top:5px;">
            <b>Use Other Description Sources:</b>
            <input data-role='none' name='fgUseAltDesc' id='fgUseAltDesc' type='checkbox' value='1' checked />
        </div>
        <div style="margin-top:5px;">
            <b>Photographers:</b>
            <input data-role='none' name='fgUseAllPhotog' id='fgUseAllPhotog' type='checkbox' value='1' onclick="selectAllPhotog();" checked /> Use All
            <a href="#" id='fgShowPhotog' title="Show Photographers List" style="margin-left:8px;" onclick="toggle('fgPhotogBox');toggle('fgShowPhotog');toggle('fgHidePhotog');return false;">Show Photographers</a>
            <a href="#" id='fgHidePhotog' title="Hide Photographers List" style="display:none;margin-left:8px;" onclick="toggle('fgPhotogBox');toggle('fgShowPhotog');toggle('fgHidePhotog');return false;">Hide Photographers</a>
            <div id='fgPhotogBox' style="display:none;width:570px;margin-top:10px;margin-bottom:10px;">
                <table style="font-family:Arial,serif;">
                    <?php
                    $photogList = array();
                    $i = 1;
                    $innerHtml = '';
                    $innerHtml .= '<tr>';
                    $photogList = $fgManager->getPhotogList();
                    ksort($photogList, SORT_STRING | SORT_FLAG_CASE);
                    foreach($photogList as $name => $id){
                        if($name){
                            $value = $id.'---'.$name;
                            if((($i % 3) === 1)) {
                                $innerHtml .= '</tr><tr>';
                            }
                            $innerHtml .= '<td style="width:190px;">';
                            $innerHtml .= "<input data-role='none' name='photog[]' type='checkbox' value='".$value."' onclick='checkPhotogSelections();' checked /> ".$name;
                            $innerHtml .= '</td>';
                            $i++;
                        }
                    }
                    $innerHtml .= '</tr>';
                    echo $innerHtml;
                    ?>
                </table>
            </div>
        </div>
        <div style="margin-top:5px;">
            <b>Max Images Per Taxon:</b>
            <input data-role="none" name="fgMaxImages" type="radio" value="0" checked /> 0
            <input data-role="none" name="fgMaxImages" type="radio" value="1"/> 1
            <input data-role="none" name="fgMaxImages" type="radio" value="2"/> 2
            <input data-role="none" name="fgMaxImages" type="radio" value="3"/> 3
        </div>
        <?php
        if($clManager->getTaxaCount() > 300){
            $highIndex = ceil(($clManager->getTaxaCount()/300));
            ?>
            <div style="margin-top:5px;">
                <b>File set:</b>
                <select data-role='none' id='zipindex'>
                    <?php
                    $optIndex = 1;
                    while($optIndex <= $highIndex) {
                        echo "<option value='".$optIndex."'>".$optIndex."</option>\n";
                        $optIndex++;
                    }
                    ?>
                </select>
            </div>
            <?php
        }
        else{
            ?>
            <input type="hidden" id="zipindex" value="1" />
            <?php
        }
        ?>
        <div style="margin-top:10px;float:right;">
            <button data-role="none" type="button" onclick='prepareFieldGuideExport(<?php echo $clManager->getTaxaCount(); ?>);' >Export Field Guide</button>
        </div>
    </div>

    <div class="loadingModal">
        <div class="vine-native-spinner" style="width:200px;height:200px;"></div>
        <div id="loaderMessage">This may take several minutes...</div>
    </div>
    <?php
}
include_once(__DIR__ . '/../config/footer-includes.php');
?>
</body>
</html>
