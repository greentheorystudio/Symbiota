<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/OccurrenceListManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$queryId = array_key_exists('queryId',$_REQUEST)?$_REQUEST['queryId']:0;
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';
$targetTid = array_key_exists('targettid',$_REQUEST)?$_REQUEST['targettid']:0;
$occIndex = array_key_exists('occindex',$_REQUEST)?$_REQUEST['occindex']:1;
$sortField1 = array_key_exists('sortfield1',$_REQUEST)?$_REQUEST['sortfield1']:'collection';
$sortField2 = array_key_exists('sortfield2',$_REQUEST)?$_REQUEST['sortfield2']:'';
$sortOrder = array_key_exists('sortorder',$_REQUEST)?$_REQUEST['sortorder']:'';

if(!is_numeric($occIndex)) {
    $occIndex = 0;
}

$collManager = new OccurrenceListManager();
$resetOccIndex = false;
$navStr = '';

$sortFields = array('Catalog Number','Collection','Collector','Country','County','Elevation','Event Date',
    'Family','Individual Count','Life Stage','Number','Scientific Name','Sex','State/Province');
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Collections Search Results Table</title>
    <style type="text/css">
        table.styledtable td {
            white-space: nowrap;
        }
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
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet" />
    <script src="../js/jquery.js" type="text/javascript"></script>
    <script src="../js/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/jquery.popupoverlay.js"></script>
    <script src="../js/symb/collections.search.js?ver=3" type="text/javascript"></script>
    <script type="text/javascript" src="../js/symb/search.term.manager.js?ver=20210313"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
    <script type="text/javascript">
        let stArr = {};
        let collJson = '';
        let sortfield1 = '';
        let sortfield2 = '';
        let sortorder = '';
        let tableIndex = <?php echo $occIndex; ?>;

        $(document).ready(function() {
            $('#csvoptions').popup({
                transition: 'all 0.3s',
                scrolllock: true
            });
            <?php
            if($stArrJson){
            ?>
            initializeSearchStorage(<?php echo $queryId; ?>);
            loadSearchTermsArrFromJson('<?php echo $stArrJson; ?>');
            <?php
            }
            ?>
            stArr = getSearchTermsArr();
            changeTablePage(tableIndex);
        });

        function changeTablePage(index){
            sortfield1 = document.sortform.sortfield1.value;
            sortfield2 = document.sortform.sortfield2.value;
            sortorder = document.sortform.sortorder.value;
            document.getElementById("tablediv").innerHTML = "<p>Loading... <img src='../images/workingcircle.gif' style='width:15px;' /></p>";
            const http = new XMLHttpRequest();
            const url = "rpc/changetablepage.php";
            const queryid = document.getElementById('queryId').value;
            const params = 'starr='+JSON.stringify(stArr)+'&targettid=<?php echo $targetTid; ?>&queryId='+queryid+'&occindex='+index+'&sortfield1='+sortfield1+'&sortfield2='+sortfield2+'&sortorder='+sortorder;
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

        function setOccurrenceList(listPage){
            sessionStorage.collSearchPage = listPage;
            document.getElementById("queryrecords").innerHTML = "<p>Loading... <img src='../images/workingcircle.gif' style='width:15px;' /></p>";
            const http = new XMLHttpRequest();
            const url = "rpc/getoccurrencelist.php";
            const queryid = document.getElementById('queryId').value;
            const params = 'starr='+JSON.stringify(stArr)+'&targettid=<?php echo $targetTid; ?>&queryId='+queryid+'&page='+listPage;
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
        echo '<span class="navpath">';
        echo '<a href="../index.php">Home</a> &gt;&gt; ';
        echo '<a style="cursor:pointer;font-weight:bold;" onclick="redirectWithQueryId(\'index.php\');">Collections</a> &gt;&gt; ';
        echo '<a style="cursor:pointer;font-weight:bold;" onclick="redirectWithQueryId(\'harvestparams.php\');">Search Criteria</a> &gt;&gt; ';
        echo '<b>Specimen Records Table</b>';
        echo '</span>';
        ?>
    </div>
    <div id="tablediv"></div>
</div>
<input type="hidden" id="queryId" name="queryId" value='<?php echo $queryId; ?>' />
<!-- Data Download Form -->
<?php include_once('csvoptions.php'); ?>
<div style="display:none;">
    <form name="datadownloadform" id="datadownloadform" action="rpc/datadownloader.php" method="post">
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
</body>
</html>
