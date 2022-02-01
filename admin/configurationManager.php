<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!isset($GLOBALS['IS_ADMIN']) || !$GLOBALS['IS_ADMIN']) {
    header('Location: ../index.php');
}

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;
$stArrJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:1;
$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?(int)$_REQUEST['taxonfilter']:0;
$targetTid = array_key_exists('targettid',$_REQUEST)?(int)$_REQUEST['targettid']:0;
$cntPerPage = array_key_exists('cntperpage',$_REQUEST)?(int)$_REQUEST['cntperpage']:100;
$pageNumber = array_key_exists('page',$_REQUEST)?(int)$_REQUEST['page']:1;

$confManager = new ConfigurationManager();

$confArray = $confManager->getConfigurationsArr();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Portal Configuration Manager</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/bootstrap.css" type="text/css" rel="stylesheet" />
    <link type="text/css" href="../css/jquery-ui.css" rel="stylesheet" />
    <style type="text/css">
        .ui-tabs .ui-tabs-nav li { width:32%; }
        .ui-tabs .ui-tabs-nav li a { margin-left:10px;}
    </style>
    <script src="../js/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../js/jquery.js?ver=20130917"></script>
    <script type="text/javascript" src="../js/jquery-ui.js?ver=20130917"></script>
    <script type="text/javascript">
        function setOccurrenceList(listPage){
            document.getElementById("queryrecords").innerHTML = "<p>Loading... <img src='../images/workingcircle.gif' style='width:15px;' /></p>";
            const http = new XMLHttpRequest();
            const url = "rpc/getoccurrencelist.php";
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
            const url = "rpc/addallvouchers.php";
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
            const url = "rpc/getchecklist.php";
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
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
