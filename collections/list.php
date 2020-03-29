<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/OccurrenceListManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:1;
$taxonFilter = array_key_exists('taxonfilter',$_REQUEST)?$_REQUEST['taxonfilter']:0;
$targetTid = array_key_exists('targettid',$_REQUEST)?$_REQUEST['targettid']:0;
$cntPerPage = array_key_exists('cntperpage',$_REQUEST)?$_REQUEST['cntperpage']:100;
$pageNumber = array_key_exists('page',$_REQUEST)?$_REQUEST['page']:1;

if(!is_numeric($taxonFilter)) {
    $taxonFilter = 1;
}
if(!is_numeric($cntPerPage)) {
    $cntPerPage = 100;
}

$collManager = new OccurrenceListManager();
$stArr = array();
$collArr = array();
$stArrSearchJson = '';
$stArrCollJson = '';
$resetPageNum = false;

if(isset($_REQUEST['taxa']) || isset($_REQUEST['country']) || isset($_REQUEST['state']) || isset($_REQUEST['county']) || isset($_REQUEST['local']) || isset($_REQUEST['elevlow']) || isset($_REQUEST['elevhigh']) || isset($_REQUEST['upperlat']) || isset($_REQUEST['pointlat']) || isset($_REQUEST['collector']) || isset($_REQUEST['collnum']) || isset($_REQUEST['eventdate1']) || isset($_REQUEST['eventdate2']) || isset($_REQUEST['catnum']) || isset($_REQUEST['typestatus']) || isset($_REQUEST['hasimages']) || isset($_REQUEST['hasgenetic'])){
    $stArr = $collManager->getSearchTerms();
    $stArrSearchJson = json_encode($stArr);
    if(!isset($_REQUEST['page']) || !$_REQUEST['page']) {
        $resetPageNum = true;
    }
}

if(isset($_REQUEST['db'])){
    $reqDBStrStr = str_replace(array('(', ')'), '', $_REQUEST['db']);
    if($reqDBStrStr === 'all' || preg_match('/^[0-9,;]+$/', $reqDBStrStr)){
        $collArr['db'] = $reqDBStrStr;
        $stArrCollJson = json_encode($collArr);
        if(!isset($_REQUEST['page']) || !$_REQUEST['page']) {
            $resetPageNum = true;
        }
    }
    if(!isset($_REQUEST['page']) || !$_REQUEST['page']) {
        $resetPageNum = true;
    }
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Collections Search Results</title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link type="text/css" href="../css/jquery-ui.css" rel="Stylesheet" />
	<style type="text/css">
		.ui-tabs .ui-tabs-nav li { width:32%; }
		.ui-tabs .ui-tabs-nav li a { margin-left:10px;}
	</style>
	<script type="text/javascript" src="../js/jquery.js?ver=20130917"></script>
	<script type="text/javascript" src="../js/jquery-ui.js?ver=20130917"></script>
    <script type="text/javascript" src="../js/symb/collections.search.js"></script>
    <script type="text/javascript">
		<?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
	</script>
	<script type="text/javascript">
        let starrJson = '';
        let collJson = '';
        let listPage = <?php echo $pageNumber; ?>;

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
            if(!$resetPageNum){
                ?>
                if(sessionStorage.collSearchPage){
                    listPage = sessionStorage.collSearchPage;
                }
                else{
                    sessionStorage.collSearchPage = listPage;
                }
                <?php
            }
            else{
                echo "sessionStorage.collSearchPage = listPage;\n";
            }
            ?>

            document.getElementById("taxatablink").href = 'checklist.php?starr='+starrJson+'&jsoncollstarr='+collJson+'&taxonfilter=<?php echo $taxonFilter; ?>';
            document.getElementById("mapdllink").href = 'download/index.php?starr='+starrJson+'&jsoncollstarr='+collJson+'&dltype=georef';
            document.getElementById("kmldlcolljson").value = collJson;
            document.getElementById("kmldlstjson").value = starrJson;

            setOccurrenceList(listPage);
            $('#tabs').tabs({
                active: <?php echo $tabIndex; ?>,
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
        });

        function setOccurrenceList(listPage){
            sessionStorage.collSearchPage = listPage;
            document.getElementById("queryrecords").innerHTML = "<p>Loading... <img src='../images/workingcircle.gif' style='width:15px;' /></p>";
            $.ajax({
                type: "POST",
                url: "rpc/getoccurrencelist.php",
                data: {
                    starr: starrJson,
                    jsoncollstarr: collJson,
                    targettid: <?php echo $targetTid; ?>,
                    page: listPage
                },
                dataType: "html"
            }).done(function(msg) {
                if(!msg) {
                    msg = "<p>An error occurred retrieving records.</p>";
                }
                document.getElementById("queryrecords").innerHTML = msg;
            });
        }

		function addAllVouchersToCl(clidIn){
            const occJson = document.getElementById("specoccjson").value;

            $.ajax({
				type: "POST",
				url: "rpc/addallvouchers.php",
				data: { clid: clidIn, jsonOccArr: occJson, tid: <?php echo ($targetTid?:'0'); ?> }
			}).done(function( msg ) {
				if(msg === "1"){
					alert("Success! All vouchers added to checklist.");
				}
				else{
					alert(msg);
				}
			});
		}

        function copySearchUrl(){
            const urlPrefix = document.getElementById('urlPrefixBox').value;
            const urlFixed = urlPrefix + '&page=' + sessionStorage.collSearchPage;
            const copyBox = document.getElementById('urlFullBox');
            copyBox.value = urlFixed;
            copyBox.focus();
            copyBox.setSelectionRange(0,copyBox.value.length);
            document.execCommand("copy");
            copyBox.value = '';
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
echo '<div class="navpath">';
echo '<a href="../index.php">Home</a> &gt;&gt; ';
echo '<a href="index.php">Collections</a> &gt;&gt; ';
echo '<a href="harvestparams.php">Search Criteria</a> &gt;&gt; ';
echo '<b>Specimen Records</b>';
echo '</div>';
?>
<div id="innertext">
	<div id="tabs" style="width:95%;">
		<ul>
			<li>
				<a id='taxatablink' href=''>
					<span>Species List</span>
				</a>
			</li>
			<li>
				<a href="#speclist">
					<span>Occurrence Records</span>
				</a>
			</li>
			<li>
				<a href="#maps">
					<span>Map</span>
				</a>
			</li>
		</ul>
		<div id="speclist">
            <div id="queryrecords"></div>
		</div>
		<div id="maps" style="min-height:400px;margin-bottom:10px;">
			<div class="button" style="margin-top:20px;float:right;width:13px;height:13px;" title="Download Coordinate Data">
				<a id='mapdllink' href=''><img src="../images/dl.png"/></a>
			</div>
			<div style='margin-top:10px;'>
				<h2>Google Map</h2>
			</div>
			<div style='margin:10px 0 0 20px;'>
				<a href="#" onclick="openMapPU();" >
                    Display coordinates in Google Map
				</a>
			</div>
			<div style='margin:10px 0 0 20px;'>
                Google Maps is a web mapping service provided by Google that features a map that users can pan (by
                dragging the mouse) and zoom (by using the mouse wheel). Collection points are displayed as colored markers
                that when clicked on, displays the full information for that collection. When multiple species are queried
                (separated by semi-colons), different colored markers denote each individual species.
			</div>

			<div style='margin-top:10px;'>
				<h2>Google Earth (KML)</h2>
			</div>
			<form name="kmlform" action="../map/googlekml.php" method="post" onsubmit="">
				<div style='margin:10px 0 0 20px;'>
                    This creates an KML file that can be opened in the Google Earth mapping application. Note that you
                    must have <a href="http://earth.google.com/" target="_blank"> Google Earth</a> installed on your computer
                    to make use of this option.
				</div>
				<div style="margin:20px;">
					<input name="jsoncollstarr" id="kmldlcolljson" type="hidden" value='' />
					<input name="starr" id="kmldlstjson" type="hidden" value='' />
					<button name="formsubmit" type="submit" value="Create KML">Create KML</button>
				</div>
				<div style='margin:10px 0 0 20px;'>
					<a href="#" onclick="toggleFieldBox('fieldBox');">
                        Add Extra Fields
					</a>
				</div>
				<div id="fieldBox" style="display:none;">
					<fieldset>
						<div style="width:600px;">
							<?php
							$occFieldArr = Array('occurrenceid','family', 'scientificname', 'sciname',
								'tidinterpreted', 'scientificnameauthorship', 'identifiedby', 'dateidentified', 'identificationreferences',
								'identificationremarks', 'taxonremarks', 'identificationqualifier', 'typestatus', 'recordedby', 'recordnumber',
								'associatedcollectors', 'eventdate', 'year', 'month', 'day', 'startdayofyear', 'enddayofyear',
								'verbatimeventdate', 'habitat', 'substrate', 'fieldnumber','occurrenceremarks', 'associatedtaxa', 'verbatimattributes',
								'dynamicproperties', 'reproductivecondition', 'cultivationstatus', 'establishmentmeans',
								'lifestage', 'sex', 'individualcount', 'samplingprotocol', 'preparations',
								'country', 'stateprovince', 'county', 'municipality', 'locality',
								'decimallatitude', 'decimallongitude','geodeticdatum', 'coordinateuncertaintyinmeters',
								'locationremarks', 'verbatimcoordinates', 'georeferencedby', 'georeferenceprotocol', 'georeferencesources',
								'georeferenceverificationstatus', 'georeferenceremarks', 'minimumelevationinmeters', 'maximumelevationinmeters',
								'verbatimelevation','language',
								'labelproject','basisofrecord');
							foreach($occFieldArr as $k => $v){
								echo '<div style="float:left;margin-right:5px;">';
								echo '<input type="checkbox" name="kmlFields[]" value="'.$v.'" />'.$v.'</div>';
							}
							?>
						</div>
					</fieldset>
				</div>
			</form>
        </div>
	</div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
