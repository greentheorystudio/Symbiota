<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/OccurrenceListManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:1;
$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?(int)$_REQUEST['taxonfilter']:0;
$targetTid = array_key_exists('targettid',$_REQUEST)?(int)$_REQUEST['targettid']:0;
$cntPerPage = array_key_exists('cntperpage',$_REQUEST)?(int)$_REQUEST['cntperpage']:100;
$pageNumber = array_key_exists('page',$_REQUEST)?(int)$_REQUEST['page']:1;

$collManager = new OccurrenceListManager();
$resetPageNum = false;
$stArr = array();
$validStArr = false;
if($stArrJson){
    $stArr = json_decode(str_replace('%squot;', "'",$stArrJson), true);
    if($collManager->validateSearchTermsArr($stArr)){
        $validStArr = true;
    }
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Collections Search Results</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/external/bootstrap.min.css?ver=20220225" rel="stylesheet" type="text/css" />
    <link type="text/css" href="../css/external/jquery-ui.css?ver=20220720" rel="stylesheet" />
    <style>
        .ui-tabs .ui-tabs-nav li { width:32%; }
        .ui-tabs .ui-tabs-nav li a { margin-left:10px;}
        a.boxclose{
            float:right;
            width:36px;
            height:36px;
            background:transparent url(../images/spatial_close_icon.png) repeat top left;
            margin-top:-35px;
            margin-right:-35px;
            cursor:pointer;
        }
    </style>
    <script src="../js/external/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/external/jquery.js?ver=20130917"></script>
    <script type="text/javascript" src="../js/external/jquery-ui.js?ver=20130917"></script>
    <script type="text/javascript" src="../js/external/jquery.popupoverlay.js"></script>
    <script type="text/javascript" src="../js/shared.js?ver=20221126"></script>
    <script type="text/javascript" src="../js/collections.search.js?ver=20221025"></script>
    <script type="text/javascript" src="../js/search.term.manager.js?ver=20221110"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
    <script type="text/javascript">
        let stArr = {};
        let listPage = <?php echo $pageNumber; ?>;

        $(document).ready(function() {
            $('#csvoptions').popup({
                transition: 'all 0.3s',
                scrolllock: true
            });
            $('#tabs').tabs({
                active: <?php echo $tabIndex; ?>,
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
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
                setOccurrenceList(listPage);
            }
        });

        function setOccurrenceList(listPage){
            document.getElementById("queryrecords").innerHTML = '<div>Loading...<span style="margin-left:15px;">' + getSmallWorkingSpinnerHtml(12) + '</span></div>';
            const http = new XMLHttpRequest();
            const url = "../api/search/getoccurrencelist.php";
            const queryid = document.getElementById('queryId').value;
            const params = 'starr='+encodeURIComponent(JSON.stringify(stArr))+'&targettid=<?php echo $targetTid; ?>&queryId='+queryid+'&page='+listPage;
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    if(!http.responseText) {
                        http.responseText = "<p>An error occurred retrieving records.</p>";
                    }
                    document.getElementById("queryrecords").innerHTML = http.responseText;
                }
            };
            http.send(params);
        }

        function addAllVouchersToCl(clidIn){
            const occJson = document.getElementById("specoccjson").value;
            const http = new XMLHttpRequest();
            const url = "../api/checklists/addallvouchers.php";
            const params = 'clid='+clidIn+'&jsonOccArr='+encodeURIComponent(occJson)+'&tid=<?php echo ($targetTid?:'0'); ?>';
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    if(http.responseText === "1"){
                        alert("Success! All vouchers added to checklist.");
                    }
                    else{
                        alert(http.responseText);
                    }
                }
            };
            http.send(params);
        }

        function getTaxaList(){
            document.getElementById("taxalist").innerHTML = "<p>Loading...</p>";
            const http = new XMLHttpRequest();
            const url = "../api/search/getchecklist.php";
            const jsonStarr = encodeURIComponent(JSON.stringify(stArr));
            const params = 'starr='+jsonStarr;
            //console.log(url+'?'+params);
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    document.getElementById("taxalist").innerHTML = http.responseText;
                }
            };
            http.send(params);
        }

        function submitInteractiveKeyFormTaxaList(){
            document.getElementById('interactiveKeyForm').submit();
        }

        function submitChecklistExplorerFormTaxaList(){
            document.getElementById('checklistExplorerForm').submit();
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
echo '<div class="navpath">';
echo '<a href="../index.php">Home</a> &gt;&gt; ';
echo '<a style="cursor:pointer;font-weight:bold;" onclick="redirectWithQueryId(\'index.php\');">Collections</a> &gt;&gt; ';
echo '<a style="cursor:pointer;font-weight:bold;" onclick="redirectWithQueryId(\'harvestparams.php\');">Search Criteria</a> &gt;&gt; ';
echo '<b>Specimen Records</b>';
echo '</div>';
?>
<div id="innertext">
    <div id="tabs" style="width:95%;">
        <ul>
            <li><a href='#taxalistdiv' onclick='getTaxaList();'>Species List</a></li>
            <li><a href="#speclist">Occurrence Records</a></li>
        </ul>
        <div id="speclist">
            <div id="queryrecords"></div>
        </div>
        <div id="taxalistdiv">
            <div id="taxalist"></div>
        </div>
    </div>
    <input type="hidden" id="queryId" name="queryId" value='<?php echo $queryId; ?>' />
</div>
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
        <input id="csetcsv" name="csetcsv" type="hidden" />
    </form>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
