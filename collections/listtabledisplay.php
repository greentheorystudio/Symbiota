<?php
include_once(__DIR__ . '/../config/symbini.php'); 
include_once(__DIR__ . '/../classes/OccurrenceListManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$targetTid = array_key_exists('targettid',$_REQUEST)?$_REQUEST['targettid']:0;
$stArrCollJson = array_key_exists('jsoncollstarr',$_REQUEST)?$_REQUEST['jsoncollstarr']:'';
$stArrSearchJson = array_key_exists('starr',$_REQUEST)?$_REQUEST['starr']:'';
$occIndex = array_key_exists('occindex',$_REQUEST)?$_REQUEST['occindex']:1;
$sortField1 = array_key_exists('sortfield1',$_REQUEST)?$_REQUEST['sortfield1']:'collection';
$sortField2 = array_key_exists('sortfield2',$_REQUEST)?$_REQUEST['sortfield2']:'';
$sortOrder = array_key_exists('sortorder',$_REQUEST)?$_REQUEST['sortorder']:'';

if(!is_numeric($occIndex)) {
    $occIndex = 0;
}

$collManager = new OccurrenceListManager();
$stArr = array();
$collArr = array();
$resetOccIndex = false;
$navStr = '';

$sortFields = array('Catalog Number','Collection','Collector','Country','County','Elevation','Event Date',
    'Family','Individual Count','Life Stage','Number','Scientific Name','Sex','State/Province');

if($stArrCollJson || $stArrSearchJson){
    $stArrSearchJson = str_replace('%apos;',"'",$stArrSearchJson);
    $collStArr = json_decode($stArrCollJson, true);
    $searchStArr = json_decode($stArrSearchJson, true);
    if($collStArr && $searchStArr) {
        $stArr = array_merge($searchStArr, $collStArr);
    }
    if(!$collStArr && $searchStArr) {
        $stArr = $searchStArr;
    }
    if($collStArr && !$searchStArr) {
        $stArr = $collStArr;
    }
}
else{
    if(isset($_REQUEST['taxa']) || isset($_REQUEST['country']) || isset($_REQUEST['state']) || isset($_REQUEST['county']) || isset($_REQUEST['local']) || isset($_REQUEST['elevlow']) || isset($_REQUEST['elevhigh']) || isset($_REQUEST['upperlat']) || isset($_REQUEST['pointlat']) || isset($_REQUEST['collector']) || isset($_REQUEST['collnum']) || isset($_REQUEST['eventdate1']) || isset($_REQUEST['eventdate2']) || isset($_REQUEST['catnum']) || isset($_REQUEST['typestatus']) || isset($_REQUEST['hasimages'])){
        $stArr = $collManager->getSearchTerms();
        $stArrSearchJson = json_encode($stArr);
        $resetOccIndex = true;
    }
    if(isset($_REQUEST['db'])){
        $collArr['db'] = $_REQUEST['db'];
        $stArrCollJson = json_encode($collArr);
        $resetOccIndex = true;
    }
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Collections Search Results Table</title>
    <style type="text/css">
		table.styledtable td {
		    white-space: nowrap;
		}
    </style>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<script src="../js/jquery.js" type="text/javascript"></script>
	<script src="../js/jquery-ui.js" type="text/javascript"></script>
    <script src="../js/symb/collections.search.js?ver=1" type="text/javascript"></script>
	<script type="text/javascript">
		<?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
	</script>
    <script type="text/javascript">
        let starrJson = '';
        let collJson = '';
        let sortfield1 = '';
        let sortfield2 = '';
        let sortorder = '';
        let tableIndex = <?php echo $occIndex; ?>;

        $(document).ready(function() {
            <?php
            if($stArrSearchJson){
                ?>
                starrJson = '<?php echo $stArrSearchJson; ?>';
                sessionStorage.jsonstarr = starrJson;
                <?php
            }
            else{
                ?>
                if(sessionStorage.jsonstarr){
                    starrJson = sessionStorage.jsonstarr;
                }
                <?php
            }
            ?>

            <?php
            if($stArrCollJson){
                ?>
                collJson = '<?php echo $stArrCollJson; ?>';
                sessionStorage.jsoncollstarr = collJson;
                <?php
            }
            else{
                ?>
                if(sessionStorage.jsoncollstarr){
                    collJson = sessionStorage.jsoncollstarr;
                }
                <?php
            }
            ?>

            <?php
            if(!$resetOccIndex){
                ?>
                if(sessionStorage.collSearchTableIndex){
                    tableIndex = sessionStorage.collSearchTableIndex;
                }
                else{
                    sessionStorage.collSearchTableIndex = tableIndex;
                }
                <?php
            }
            else{
                echo 'sessionStorage.collSearchTableIndex = tableIndex;';
            }
            ?>

            document.getElementById("dllink").href = 'download/index.php?dltype=specimen&starr='+starrJson+'&jsoncollstarr='+collJson;
            sessionStorage.collsearchtableview = true;

            changeTablePage(tableIndex);
        });

        function changeTablePage(index){
            sortfield1 = document.sortform.sortfield1.value;
            sortfield2 = document.sortform.sortfield2.value;
            sortorder = document.sortform.sortorder.value;
            sessionStorage.collSearchTableIndex = index;

            document.getElementById("tablediv").innerHTML = "<p>Loading... <img src='../images/workingcircle.gif' style='width:15px;' /></p>";

            $.ajax({
                type: "POST",
                url: "rpc/changetablepage.php",
                data: {
                    starr: starrJson,
                    jsoncollstarr: collJson,
                    occindex: index,
                    sortfield1: sortfield1,
                    sortfield2: sortfield2,
                    sortorder: sortorder,
                    targettid: <?php echo $targetTid; ?>
                }
            }).done(function(msg) {
                if(msg){
                    document.getElementById("tablediv").innerHTML = msg;
                }
                else{
                    document.getElementById("tablediv").innerHTML = "<p>An error occurred retrieving records.</p>";
                }
            });
        }

        function copySearchUrl(){
            const urlPrefix = document.getElementById('urlPrefixBox').value;
            const urlFixed = urlPrefix + '&occindex=' + sessionStorage.collSearchTableIndex + '&sortfield1=' + sortfield1 + '&sortfield2=' + sortfield2 + '&sortorder=' + sortorder;
            const copyBox = document.getElementById('urlFullBox');
            copyBox.value = urlFixed;
            copyBox.focus();
            copyBox.setSelectionRange(0,copyBox.value.length);
            document.execCommand("copy");
            copyBox.value = '';
        }
    </script>
</head>
<body style="margin-left: 0;margin-right: 0;background-color:white;">
	<div id="">
		<div style="width:725px;clear:both;margin-bottom:5px;">
			<div style="float:right;">
				<div class='button' style='margin:15px 15px 0 0;width:13px;height:13px;' title='Download occurrence data'>
					<a id='dllink' href=''>
						<img src='../images/dl.png'/>
					</a>
				</div>
			</div>
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
            echo '<a href="index.php">Collections</a> &gt;&gt; ';
            echo '<a href="harvestparams.php">Search Criteria</a> &gt;&gt; ';
            echo '<b>Specimen Records Table</b>';
            echo '</span>';
			?>
		</div>
        <div id="tablediv"></div>
	</div>
</body>
</html>
