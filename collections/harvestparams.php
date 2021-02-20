<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../config/includes/searchVarDefault.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$queryId = array_key_exists('queryId',$_REQUEST)?$_REQUEST['queryId']:0;

if(file_exists($SERVER_ROOT.'/config/includes/searchVarCustom.php')){
    include(__DIR__ . '/../config/includes/searchVarCustom.php');
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title><?php echo $DEFAULT_TITLE.' '.$SEARCHTEXT['PAGE_TITLE']; ?></title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/jquery-ui.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/symb/shared.js?ver=1"></script>
    <script type="text/javascript" src="../js/symb/collections.harvestparams.js?ver=20"></script>
    <script type="text/javascript" src="../js/symb/search.term.manager.js?ver=12"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/ol.js?ver=4" type="text/javascript"></script>
    <script src="https://npmcdn.com/@turf/turf/turf.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        const SOLRMODE = '<?php echo $SOLR_MODE; ?>';

        $(document).ready(function() {
            initializeSearchStorage(<?php echo $queryId; ?>);
            setHarvestParamsForm();
        });

        function processHarvestparamsForm(frm){
            setSpatialSearchTerms();
            const searchTermsArr = getSearchTermsArr();
            if(frm.upperlat.value !== '' || frm.bottomlat.value !== '' || frm.leftlong.value !== '' || frm.rightlong.value !== ''){
                if(frm.upperlat.value === '' || frm.bottomlat.value === '' || frm.leftlong.value === '' || frm.rightlong.value === ''){
                    alert("Error: Please make all Lat/Long bounding box values contain a value or all are empty");
                    return false;
                }
                if(Math.abs(frm.upperlat.value) > 90 || Math.abs(frm.bottomlat.value) > 90 || Math.abs(frm.pointlat.value) > 90){
                    alert("Latitude values can not be greater than 90 or less than -90.");
                    return false;
                }
                if(Math.abs(frm.leftlong.value) > 180 || Math.abs(frm.rightlong.value) > 180 || Math.abs(frm.pointlong.value) > 180){
                    alert("Longitude values can not be greater than 180 or less than -180.");
                    return false;
                }
                if(parseFloat(frm.upperlat.value) < parseFloat(frm.bottomlat.value)){
                    alert("Your northern latitude value is less then your southern latitude value. Please correct this.");
                    return false;
                }
                if(parseFloat(frm.leftlong.value) > parseFloat(frm.rightlong.value)){
                    alert("Your western longitude value is greater then your eastern longitude value. Please correct this. Note that western hemisphere longitudes in the decimal format are negitive.");
                    return false;
                }
            }
            if(frm.pointlat.value !== '' || frm.pointlong.value !== '' || frm.radius.value !== ''){
                if(frm.pointlat.value === '' || frm.pointlong.value === '' || frm.radius.value === ''){
                    alert("Error: Please make all Lat/Long point-radius values contain a value or all are empty");
                    return false;
                }
            }
            if(frm.elevlow.value || frm.elevhigh.value){
                if(isNaN(frm.elevlow.value) || isNaN(frm.elevhigh.value)){
                    alert("Error: Please enter only numbers for elevation values");
                    return false;
                }
            }
            if(!validateSearchTermsArr(searchTermsArr)){
                alert('Please enter search criteria.');
                return false;
            }
            return true;
        }

        function updateRadius(){
            const pointRadiusLat = Number(document.getElementById("pointlat").value);
            const pointRadiusLong = Number(document.getElementById("pointlong").value);
            let enteredRadius = Number(document.getElementById("radiustemp").value);
            if(pointRadiusLat && pointRadiusLong && enteredRadius){
                const radiusUnits = document.getElementById("radiusunits").value;
                const radius = (radiusUnits === "km" ? (enteredRadius * 1000) : ((enteredRadius * 0.621371192) * 1000));
                const centerCoords = ol.proj.fromLonLat([pointRadiusLong, pointRadiusLat]);
                const edgeCoordinate = [centerCoords[0] + radius, centerCoords[1]];
                const fixedcenter = ol.proj.transform(centerCoords, 'EPSG:3857', 'EPSG:4326');
                const fixededgeCoordinate = ol.proj.transform(edgeCoordinate, 'EPSG:3857', 'EPSG:4326');
                const groundRadius = turf.distance([fixedcenter[0], fixedcenter[1]], [fixededgeCoordinate[0], fixededgeCoordinate[1]]);
                document.getElementById("radius").value = radius;
                document.getElementById("groundradius").value = groundRadius;
            }
        }

        function setSpatialSearchTerms() {
            let polyArrVal = document.getElementById('polyArr').value;
            let circleArrVal = document.getElementById('circleArr').value;
            let upperlatVal = document.getElementById('upperlat').value;
            let bottomlatVal = document.getElementById('bottomlat').value;
            let leftlongVal = document.getElementById('leftlong').value;
            let rightlongVal = document.getElementById('rightlong').value;
            let pointlatVal = document.getElementById('pointlat').value;
            let pointlongVal = document.getElementById('pointlong').value;
            let radiustempVal = document.getElementById('radiustemp').value;
            let radiusVal = document.getElementById('radius').value;
            let groundRadiusVal = document.getElementById('groundradius').value;
            let radiusunitsVal = document.getElementById('radiusunits').value;
            if(polyArrVal){
                setSearchTermsArrKeyValue('polyArr',polyArrVal);
            }
            else{
                clearSearchTermsArrKey('polyArr');
            }
            if(circleArrVal){
                setSearchTermsArrKeyValue('circleArr',circleArrVal);
            }
            else{
                clearSearchTermsArrKey('circleArr');
            }
            if(upperlatVal){
                setSearchTermsArrKeyValue('upperlat',upperlatVal);
                setSearchTermsArrKeyValue('bottomlat',bottomlatVal);
                setSearchTermsArrKeyValue('leftlong',leftlongVal);
                setSearchTermsArrKeyValue('rightlong',rightlongVal);
            }
            else{
                clearSearchTermsArrKey('upperlat');
                clearSearchTermsArrKey('bottomlat');
                clearSearchTermsArrKey('leftlong');
                clearSearchTermsArrKey('rightlong');
            }
            if(pointlatVal){
                setSearchTermsArrKeyValue('pointlat',pointlatVal);
                setSearchTermsArrKeyValue('pointlong',pointlongVal);
                setSearchTermsArrKeyValue('radiustemp',radiustempVal);
                setSearchTermsArrKeyValue('radius',radiusVal);
                setSearchTermsArrKeyValue('groundradius',groundRadiusVal);
                setSearchTermsArrKeyValue('radiusunits',radiusunitsVal);
            }
            else{
                clearSearchTermsArrKey('pointlat');
                clearSearchTermsArrKey('pointlong');
                clearSearchTermsArrKey('radiustemp');
                clearSearchTermsArrKey('radius');
                clearSearchTermsArrKey('groundradius');
                clearSearchTermsArrKey('radiusunits');
            }
        }

        function openSpatialInputWindow(type) {
            let mapWindow = open("../spatial/index.php?windowtype=" + type,"input","resizable=0,width=800,height=700,left=100,top=20");
            if (mapWindow.opener == null) {
                mapWindow.opener = self;
            }
            mapWindow.addEventListener('blur', function(){
                mapWindow.close();
                mapWindow = null;
            });
        }
    </script>
</head>
<body>

<?php
	include(__DIR__ . '/../header.php');
?>
<div class='navpath'>
    <a href="../index.php">Home</a> &gt;&gt;
    <a href="index.php?queryId=<?php echo $queryId; ?>">Collections</a> &gt;&gt;
    <b>Search Criteria</b>
</div>

	<div id="innertext">
        <h1><?php echo $SEARCHTEXT['PAGE_HEADER']; ?></h1>
		<?php echo $SEARCHTEXT['GENERAL_TEXT_1']; ?>
        <div style="margin:5px;">
			<input type='checkbox' name='showtable' id='showtable' value='1' onchange="changeTableDisplay();" /> Show results in table view
		</div>
		<form name="harvestparams" id="harvestparams" action="list.php" method="post" onsubmit="return processHarvestparamsForm(this);">
			<div style="margin:10px 0 10px 0;"><hr></div>
			<div style='float:right;margin:5px 10px;'>
				<div style="margin-bottom:10px"><input type="submit" class="nextbtn" value="Next" /></div>
				<div><button type="button" class="resetbtn" onclick='resetHarvestParamsForm(this.form);'>Reset Form</button></div>
			</div>
			<div>
				<h1><?php echo $SEARCHTEXT['TAXON_HEADER']; ?></h1>
				<span style="margin-left:5px;"><input type='checkbox' name='thes' id='thes' onchange="processTaxaParamChange();" value='1' checked /><?php echo $SEARCHTEXT['GENERAL_TEXT_2']; ?></span>
			</div>
			<div id="taxonSearch0">
				<div>
					<select id="taxontype" onchange="processTaxaParamChange();" name="type">
						<option value='1'><?php echo $SEARCHTEXT['SELECT_1-1']; ?></option>
						<option value='2'><?php echo $SEARCHTEXT['SELECT_1-2']; ?></option>
						<option value='3'><?php echo $SEARCHTEXT['SELECT_1-3']; ?></option>
						<option value='4'><?php echo $SEARCHTEXT['SELECT_1-4']; ?></option>
						<option value='5'><?php echo $SEARCHTEXT['SELECT_1-5']; ?></option>
					</select>:
					<input id="taxa" type="text" size="60" name="taxa" onchange="processTaxaParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
				</div>
			</div>
			<div style="margin:10px 0 10px 0;"><hr></div>
			<div>
				<h1><?php echo $SEARCHTEXT['LOCALITY_HEADER']; ?></h1>
			</div>
			<div>
				<?php echo $SEARCHTEXT['COUNTRY_INPUT']; ?> <input type="text" id="country" size="43" name="country" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
			</div>
			<div>
				<?php echo $SEARCHTEXT['STATE_INPUT']; ?> <input type="text" id="state" size="37" name="state" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
			</div>
			<div>
				<?php echo $SEARCHTEXT['COUNTY_INPUT']; ?> <input type="text" id="county" size="37"  name="county" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
			</div>
			<div>
				<?php echo $SEARCHTEXT['LOCALITY_INPUT']; ?> <input type="text" id="locality" size="43" name="local" onchange="processTextParamChange();" />
			</div>
			<div>
				<?php echo $SEARCHTEXT['ELEV_INPUT_1']; ?> <input type="text" id="elevlow" size="10" name="elevlow" onchange="processTextParamChange();" /> <?php echo $SEARCHTEXT['ELEV_INPUT_2']; ?>
				<input type="text" id="elevhigh" size="10" name="elevhigh" onchange="processTextParamChange();" />
			</div>
            <?php
            if($QUICK_HOST_ENTRY_IS_ACTIVE) {
                ?>
                <div>
                    <?php echo $SEARCHTEXT['ASSOC_HOST_INPUT']; ?> <input type="text" id="assochost" size="43" name="assochost" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
                </div>
                <?php
            }
            ?>
			<div style="margin:10px 0 10px 0;">
				<hr>
				<h1><?php echo $SEARCHTEXT['LAT_LNG_HEADER']; ?></h1>
			</div>
			<div style="clear:both;width:600px;float:left;border:2px solid brown;padding:10px;margin-bottom:10px;">
                <div style="font-weight:bold;margin-bottom:10px;">
                    <?php echo $SEARCHTEXT['LL_BOUND_TEXT']; ?>
                </div>
                <div style="margin-bottom:8px;">
                    <div style="float:left;">
                        <div style="width:145px;display:inline-block;"><?php echo $SEARCHTEXT['LL_BOUND_NLAT']; ?></div> <input type="text" id="upperlat" name="upperlat" size="7" style="width:100px;">
                    </div>
                    <div style="float:left;margin-left:10px;">
                        <div style="width:145px;display:inline-block;"><?php echo $SEARCHTEXT['LL_BOUND_SLAT']; ?></div> <input type="text" id="bottomlat" name="bottomlat" size="7" style="width:100px;">
                    </div>
                </div>
                <div style="clear:both;margin-bottom:8px;">
                    <div style="float:left;">
                        <div style="width:145px;display:inline-block;"><?php echo $SEARCHTEXT['LL_BOUND_WLNG']; ?></div> <input type="text" id="leftlong" name="leftlong" size="7" style="width:100px;">
                    </div>
                    <div style="float:left;margin-left:10px;">
                        <div style="width:145px;display:inline-block;"><?php echo $SEARCHTEXT['LL_BOUND_ELNG']; ?></div> <input type="text" id="rightlong" name="rightlong" size="7" style="width:100px;">
                    </div>
                </div>
                <div style="float:right;cursor:pointer;" onclick="openSpatialInputWindow('input-box');">
                    <img src="../images/world.png" style="width:15px;" title="Open Spatial Window" />
                </div>
            </div>
            <div style="clear:both;width:600px;float:left;border:2px solid brown;padding:10px;margin-bottom:10px;">
                <div style="font-weight:bold;margin-bottom:10px;">
                    <?php echo $SEARCHTEXT['LL_P-RADIUS_TEXT']; ?>
                </div>
                <div style="margin-bottom:8px;">
                    <div style="float:left;">
                        <div style="width:80px;display:inline-block;"><?php echo $SEARCHTEXT['LL_P-RADIUS_LAT']; ?></div> <input type="text" id="pointlat" name="pointlat" size="7" style="width:100px;" onchange="updateRadius();">
                    </div>
                    <div style="float:left;margin-left:10px;">
                        <div style="width:80px;display:inline-block;"><?php echo $SEARCHTEXT['LL_P-RADIUS_LNG']; ?></div> <input type="text" id="pointlong" name="pointlong" size="7" style="width:100px;" onchange="updateRadius();">
                    </div>
                </div>
                <div style="clear:both;margin-bottom:8px;">
                    <div style="float:left;">
                        <div style="width:80px;display:inline-block;"><?php echo $SEARCHTEXT['LL_P-RADIUS_RADIUS']; ?></div> <input type="text" id="radiustemp" name="radiustemp" size="7" style="width:100px;" onchange="updateRadius();">
                        <select id="radiusunits" name="radiusunits" onchange="updateRadius();">
                            <option value="km"><?php echo $SEARCHTEXT['LL_P-RADIUS_KM']; ?></option>
                            <option value="mi"><?php echo $SEARCHTEXT['LL_P-RADIUS_MI']; ?></option>
                        </select>
                        <input type="hidden" id="radius" name="radius" value="" />
                        <input type="hidden" id="groundradius" name="groundradius" value="" />
                    </div>
                </div>
                <div style="float:right;cursor:pointer;" onclick="openSpatialInputWindow('input-circle');">
                    <img src="../images/world.png" style="width:15px;" title="Open Spatial Window" />
                </div>
            </div>
            <div style="clear:both;width:600px;float:left;border:2px solid brown;padding:10px;margin-bottom:10px;">
                <div id="spatialParamasNoCriteria" style="font-weight:bold;margin-bottom:8px;display:block;">
                    <?php echo $SEARCHTEXT['SPATIAL_NO_CRITERIA_TEXT']; ?>
                </div>
                <div id="spatialParamasCriteria" style="font-weight:bold;margin-bottom:8px;display:none;">
                    <?php echo $SEARCHTEXT['SPATIAL_CRITERIA_TEXT']; ?>
                </div>
                <div style="clear:both;margin-bottom:8px;">
                    <div id="openspatialwindowdiv" style="width:240px;float:left;">
                        <button type="button" style="width:200px;" onclick="openSpatialInputWindow('input');">
                            Open Spatial Window
                            <span style="float:right;cursor:pointer;">
                                <img src="../images/world.png" style="width:15px;" title="Open Spatial Window" />
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div style=";clear:both;"><hr/></div>
			<div>
				<h1><?php echo $SEARCHTEXT['COLLECTOR_HEADER']; ?></h1>
			</div>
			<div>
				<?php echo $SEARCHTEXT['COLLECTOR_LASTNAME']; ?>
				<input type="text" id="collector" size="32" name="collector" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
			</div>
			<div>
				<?php echo $SEARCHTEXT['COLLECTOR_NUMBER']; ?>
				<input type="text" id="collnum" size="31" name="collnum" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_2']; ?>" />
			</div>
			<div>
				<?php echo $SEARCHTEXT['COLLECTOR_DATE']; ?>
				<input type="text" id="eventdate1" size="32" name="eventdate1" style="width:100px;" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_3']; ?>" /> -
				<input type="text" id="eventdate2" size="32" name="eventdate2" style="width:100px;" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_4']; ?>" />
			</div>
			<div style="float:right;">
				<input type="submit" class="nextbtn" value="Next" />
			</div>
			<div>
				<h1><?php echo $SEARCHTEXT['SPECIMEN_HEADER']; ?></h1>
			</div>
            <div>
                <?php echo $SEARCHTEXT['OCCURRENCE_REMARKS']; ?> <input type="text" id="occurrenceRemarks" size="50" name="occurrenceRemarks" onchange="processTextParamChange();" />
            </div>
			<div>
                <?php echo $SEARCHTEXT['CATALOG_NUMBER']; ?>
                <input type="text" id="catnum" size="32" name="catnum" onchange="processTextParamChange();" title="<?php echo $SEARCHTEXT['TITLE_TEXT_1']; ?>" />
            </div>
            <div>
                <input name="othercatnum" id="othercatnum"  type="checkbox" onchange="processTextParamChange();" value="1" checked /> <?php echo $SEARCHTEXT['INCLUDE_OTHER_CATNUM']; ?>
            </div>
            <div>
				<input type='checkbox' name='typestatus' id='typestatus' onchange="processTextParamChange();" value='1' /> <?php echo $SEARCHTEXT['TYPE']; ?>
			</div>
			<div>
				<input type='checkbox' name='hasimages' id='hasimages' onchange="processTextParamChange();" value='1' /> <?php echo $SEARCHTEXT['HAS_IMAGE']; ?>
			</div>
            <div id="searchGeneticCheckbox">
                <input type='checkbox' name='hasgenetic' id='hasgenetic' onchange="processTextParamChange();" value='1' /> <?php echo $SEARCHTEXT['HAS_GENETIC']; ?>
            </div>
			<input type="hidden" id="polyArr" name="polyArr" value="" />
            <input type="hidden" id="circleArr" name="circleArr" value="" />
            <input type="hidden" id="queryId" name="queryId" value='<?php echo $queryId; ?>' />
		</form>
    </div>
	<?php
	include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
