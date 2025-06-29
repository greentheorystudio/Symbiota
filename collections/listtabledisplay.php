<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/OccurrenceListManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';
$targetTid = array_key_exists('targettid',$_REQUEST)?(int)$_REQUEST['targettid']:0;
$occIndex = array_key_exists('occindex',$_REQUEST)?(int)$_REQUEST['occindex']:1;
$sortField1 = array_key_exists('sortfield1',$_REQUEST)?$_REQUEST['sortfield1']:'collection';
$sortField2 = array_key_exists('sortfield2',$_REQUEST)?$_REQUEST['sortfield2']:'';
$sortOrder = array_key_exists('sortorder',$_REQUEST)?$_REQUEST['sortorder']:'';

$collManager = new OccurrenceListManager();
$resetOccIndex = false;
$navStr = '';
$sortFields = array('Catalog Number','Collection','Collector','Country','County','Elevation','Event Date',
    'Family','Individual Count','Life Stage','Number','Scientific Name','Sex','State/Province');
$stArr = array();
$validStArr = false;
if($stArrJson){
    $stArr = json_decode(str_replace('%squot;', "'",$stArrJson), true);
    if($collManager->validateSearchTermsArr($stArr)){
        $validStArr = true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Collection Search Table Display</title>
    <meta name="description" content="Collection search table display for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        table.styledtable td {
            white-space: nowrap;
        }
        a.boxclose{
            float:right;
            width:36px;
            height:36px;
            background:transparent url('<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/spatial_close_icon.png') repeat top left;
            margin-top:-35px;
            margin-right:-35px;
            cursor:pointer;
        }
    </style>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/bootstrap.min.css?ver=20221225" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.popupoverlay.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/collections.search.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/search.term.manager.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script type="text/javascript">
        let stArr = {};
        let collJson = '';
        let sortfield1 = '';
        let sortfield2 = '';
        let sortorder = '';
        let tableIndex = <?php echo $occIndex; ?>;

        document.addEventListener("DOMContentLoaded", function() {
            $('#csvoptions').popup({
                transition: 'all 0.3s',
                scrolllock: true
            });
            <?php
            if($validStArr){
                ?>
                initializeSearchStorage(<?php echo $queryId; ?>);
                loadSearchTermsArrFromJson('<?php echo $stArrJson; ?>');
                <?php
            }
            ?>
            stArr = getSearchTermsArr();
            if(validateSearchTermsArr(stArr)){
                changeTablePage(tableIndex);
            }
        });

        function changeTablePage(index){
            sortfield1 = document.sortform.sortfield1.value;
            sortfield2 = document.sortform.sortfield2.value;
            sortorder = document.sortform.sortorder.value;
            document.getElementById("tablediv").innerHTML = '<div>Loading...<span style="margin-left:15px;">' + getSmallWorkingSpinnerHtml(12) + '</span></div>';
            const http = new XMLHttpRequest();
            const url = "../api/search/changetablepage.php";
            const queryid = document.getElementById('queryId').value;
            const params = 'starr='+encodeURIComponent(JSON.stringify(stArr))+'&targettid=<?php echo $targetTid; ?>&queryId='+queryid+'&occindex='+index+'&sortfield1='+sortfield1+'&sortfield2='+sortfield2+'&sortorder='+sortorder;
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    if(http.responseText) {
                        document.getElementById("tablediv").innerHTML = http.responseText;
                    }
                    else{
                        document.getElementById("tablediv").innerHTML = "<p>An error occurred retrieving records.</p>";
                    }
                }
            };
            http.send(params);
        }
    </script>
</head>
<body style="margin-left: 0;margin-right: 0;background-color:white;border:0;">
<div id="">
    <div style="width:725px;clear:both;margin-bottom:5px;">
        <fieldset style="padding:5px;width:650px;">
            <legend><b>Sort Results</b></legend>
            <form name="sortform" action="listtabledisplay.php" method="post">
                <div style="float:left;">
                    <b>Sort By:</b>
                    <select name="sortfield1">
                        <?php
                        foreach($sortFields as $k){
                            echo '<option value="'.$k.'" '.($k === $sortField1?'SELECTED':'').'>'.$k.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div style="float:left;margin-left:10px;">
                    <b>Then By:</b>
                    <select name="sortfield2">
                        <option value="">Select Field Name</option>
                        <?php
                        foreach($sortFields as $k){
                            echo '<option value="'.$k.'" '.($k === $sortField2?'SELECTED':'').'>'.$k.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div style="float:left;margin-left:10px;">
                    <b>Order:</b>
                    <select name="sortorder">
                        <option value="asc" <?php echo ($sortOrder === 'asc' ?'SELECTED':''); ?>>Ascending</option>
                        <option value="desc" <?php echo ($sortOrder === 'desc' ?'SELECTED':''); ?>>Descending</option>
                    </select>
                </div>
                <div style="float:right;margin-right:10px;">
                    <button name="formsubmit" type="button" value="sortresults" onclick="changeTablePage(1);">Sort</button>
                </div>
            </form>
        </fieldset>
    </div>
    <div style="width:790px;clear:both;">
        <?php
        echo '<span id="breadcrumbs">';
        echo '<a href="../index.php">Home</a> &gt;&gt; ';
        echo '<a style="cursor:pointer;font-weight:bold;" onclick="redirectWithQueryId(\'list.php\');">Search Criteria</a> &gt;&gt; ';
        echo '<b>Specimen Records Table</b>';
        echo '</span>';
        ?>
    </div>
    <div id="tablediv"></div>
</div>
<input type="hidden" id="queryId" name="queryId" value='<?php echo $queryId; ?>' />
<!-- Data Download Form -->
<?php include_once(__DIR__ . '/csvoptions.php'); ?>
<div style="display:none;">
    <form name="datadownloadform" id="datadownloadform" action="../api/search/datadownloader.php" method="post">
        <input id="starrjson" name="starrjson" type="hidden" />
        <input id="dh-q" name="dh-q" type="hidden" />
        <input id="dh-fq" name="dh-fq" type="hidden" />
        <input id="dh-fl" name="dh-fl" type="hidden" />
        <input id="dh-rows" name="dh-rows" type="hidden" />
        <input id="dh-type" name="dh-type" type="hidden" />
        <input id="dh-filename" name="dh-filename" type="hidden" />
        <input id="dh-contentType" name="dh-contentType" type="hidden" />
        <input id="dh-selections" name="dh-selections" type="hidden" />
        <input id="schemacsv" name="schemacsv" type="hidden" />
        <input id="identificationscsv" name="identificationscsv" type="hidden" />
        <input id="imagescsv" name="imagescsv" type="hidden" />
        <input id="formatcsv" name="formatcsv" type="hidden" />
        <input id="zipcsv" name="zipcsv" type="hidden" />
    </form>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
?>
</body>
</html>
