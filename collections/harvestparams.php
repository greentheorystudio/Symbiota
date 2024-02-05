<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../config/includes/searchVarDefault.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;

if(file_exists($GLOBALS['SERVER_ROOT'].'/config/includes/searchVarCustom.php')){
    include(__DIR__ . '/../config/includes/searchVarCustom.php');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE'].' '.$GLOBALS['SEARCHTEXT']['PAGE_TITLE']; ?></title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
        <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20220717" rel="stylesheet" type="text/css" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js?ver=1.9.1" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js?ver=1.10.3" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/collections.harvestparams.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/search.term.manager.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol-ext.min.js?ver=20240115" type="text/javascript"></script>
        <script src="https://npmcdn.com/@turf/turf/turf.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/shp.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jszip.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/stream.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/FileSaver.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/html2canvas.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/geotiff.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/plotty.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
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
            <h1><?php echo $GLOBALS['SEARCHTEXT']['PAGE_HEADER']; ?></h1>
            <?php echo $GLOBALS['SEARCHTEXT']['GENERAL_TEXT_1']; ?>
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
                    <h1><?php echo $GLOBALS['SEARCHTEXT']['TAXON_HEADER']; ?></h1>
                    <span style="margin-left:5px;"><input type='checkbox' name='thes' id='thes' onchange="processTaxaParamChange();" value='1' checked /><?php echo $GLOBALS['SEARCHTEXT']['GENERAL_TEXT_2']; ?></span>
                </div>
                <div id="taxonSearch0">
                    <div>
                        <select id="taxontype" onchange="processTaxaParamChange();" name="type">
                            <option value='1'><?php echo $GLOBALS['SEARCHTEXT']['SELECT_1-1']; ?></option>
                            <option value='2'><?php echo $GLOBALS['SEARCHTEXT']['SELECT_1-2']; ?></option>
                            <option value='3'><?php echo $GLOBALS['SEARCHTEXT']['SELECT_1-3']; ?></option>
                            <option value='4'><?php echo $GLOBALS['SEARCHTEXT']['SELECT_1-4']; ?></option>
                            <option value='5'><?php echo $GLOBALS['SEARCHTEXT']['SELECT_1-5']; ?></option>
                        </select>:
                        <input id="taxa" type="text" size="60" name="taxa" onchange="processTaxaParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_1']; ?>" />
                    </div>
                </div>
                <div style="margin:10px 0 10px 0;"><hr></div>
                <div>
                    <h1><?php echo $GLOBALS['SEARCHTEXT']['LOCALITY_HEADER']; ?></h1>
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['COUNTRY_INPUT']; ?> <input type="text" id="country" size="43" name="country" onchange="processTextParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_1']; ?>" />
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['STATE_INPUT']; ?> <input type="text" id="state" size="37" name="state" onchange="processTextParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_1']; ?>" />
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['COUNTY_INPUT']; ?> <input type="text" id="county" size="37"  name="county" onchange="processTextParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_1']; ?>" />
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['LOCALITY_INPUT']; ?> <input type="text" id="locality" size="43" name="local" onchange="processTextParamChange();" />
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['ELEV_INPUT_1']; ?> <input type="text" id="elevlow" size="10" name="elevlow" onchange="processTextParamChange();" /> <?php echo $GLOBALS['SEARCHTEXT']['ELEV_INPUT_2']; ?>
                    <input type="text" id="elevhigh" size="10" name="elevhigh" onchange="processTextParamChange();" />
                </div>
                <div style="margin:10px 0 10px 0;">
                    <hr>
                    <h1><?php echo $GLOBALS['SEARCHTEXT']['LAT_LNG_HEADER']; ?></h1>
                </div>
                <div style="clear:both;width:600px;float:left;border:2px solid brown;padding:10px;margin-bottom:10px;">
                    <div style="font-weight:bold;margin-bottom:10px;">
                        <?php echo $GLOBALS['SEARCHTEXT']['LL_BOUND_TEXT']; ?>
                    </div>
                    <div style="margin-bottom:8px;">
                        <div style="float:left;">
                            <div style="width:145px;display:inline-block;"><?php echo $GLOBALS['SEARCHTEXT']['LL_BOUND_NLAT']; ?></div> <input type="text" id="upperlat" name="upperlat" size="7" style="width:100px;">
                        </div>
                        <div style="float:left;margin-left:10px;">
                            <div style="width:145px;display:inline-block;"><?php echo $GLOBALS['SEARCHTEXT']['LL_BOUND_SLAT']; ?></div> <input type="text" id="bottomlat" name="bottomlat" size="7" style="width:100px;">
                        </div>
                    </div>
                    <div style="clear:both;margin-bottom:8px;">
                        <div style="float:left;">
                            <div style="width:145px;display:inline-block;"><?php echo $GLOBALS['SEARCHTEXT']['LL_BOUND_WLNG']; ?></div> <input type="text" id="leftlong" name="leftlong" size="7" style="width:100px;">
                        </div>
                        <div style="float:left;margin-left:10px;">
                            <div style="width:145px;display:inline-block;"><?php echo $GLOBALS['SEARCHTEXT']['LL_BOUND_ELNG']; ?></div> <input type="text" id="rightlong" name="rightlong" size="7" style="width:100px;">
                        </div>
                    </div>
                    <div style="float:right;cursor:pointer;" @click="openPopup('input-box');">
                        <i style="float:right;height:15px;width:15px;" title="Open Spatial Window" class="fas fa-globe"></i>
                    </div>
                </div>
                <div style="clear:both;width:600px;float:left;border:2px solid brown;padding:10px;margin-bottom:10px;">
                    <div style="font-weight:bold;margin-bottom:10px;">
                        <?php echo $GLOBALS['SEARCHTEXT']['LL_P-RADIUS_TEXT']; ?>
                    </div>
                    <div style="margin-bottom:8px;">
                        <div style="float:left;">
                            <div style="width:80px;display:inline-block;"><?php echo $GLOBALS['SEARCHTEXT']['LL_P-RADIUS_LAT']; ?></div> <input type="text" id="pointlat" name="pointlat" size="7" style="width:100px;" onchange="updateRadius();">
                        </div>
                        <div style="float:left;margin-left:10px;">
                            <div style="width:80px;display:inline-block;"><?php echo $GLOBALS['SEARCHTEXT']['LL_P-RADIUS_LNG']; ?></div> <input type="text" id="pointlong" name="pointlong" size="7" style="width:100px;" onchange="updateRadius();">
                        </div>
                    </div>
                    <div style="clear:both;margin-bottom:8px;">
                        <div style="float:left;">
                            <div style="width:80px;display:inline-block;"><?php echo $GLOBALS['SEARCHTEXT']['LL_P-RADIUS_RADIUS']; ?></div> <input type="text" id="radiustemp" name="radiustemp" size="7" style="width:100px;" onchange="updateRadius();">
                            <select id="radiusunits" name="radiusunits" onchange="updateRadius();">
                                <option value="km"><?php echo $GLOBALS['SEARCHTEXT']['LL_P-RADIUS_KM']; ?></option>
                                <option value="mi"><?php echo $GLOBALS['SEARCHTEXT']['LL_P-RADIUS_MI']; ?></option>
                            </select>
                            <input type="hidden" id="radius" name="radius" value="" />
                            <input type="hidden" id="groundradius" name="groundradius" value="" />
                        </div>
                    </div>
                    <div style="float:right;cursor:pointer;" @click="openPopup('input-circle');">
                        <i style="float:right;height:15px;width:15px;" title="Open Spatial Window" class="fas fa-globe"></i>
                    </div>
                </div>
                <div style="clear:both;width:600px;float:left;border:2px solid brown;padding:10px;margin-bottom:10px;">
                    <div id="spatialParamasNoCriteria" style="font-weight:bold;margin-bottom:8px;display:block;">
                        <?php echo $GLOBALS['SEARCHTEXT']['SPATIAL_NO_CRITERIA_TEXT']; ?>
                    </div>
                    <div id="spatialParamasCriteria" style="font-weight:bold;margin-bottom:8px;display:none;">
                        <?php echo $GLOBALS['SEARCHTEXT']['SPATIAL_CRITERIA_TEXT']; ?>
                    </div>
                    <div style="clear:both;margin-bottom:8px;">
                        <div id="openspatialwindowdiv" style="width:240px;float:left;">
                            <button type="button" style="width:200px;" @click="openPopup('input');">
                                Open Spatial Window
                                <span style="float:right;cursor:pointer;">
                                    <i style="float:right;height:15px;width:15px;" title="Open Spatial Window" class="fas fa-globe"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div style=";clear:both;"><hr/></div>
                <div>
                    <h1><?php echo $GLOBALS['SEARCHTEXT']['COLLECTOR_HEADER']; ?></h1>
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['COLLECTOR_LASTNAME']; ?>
                    <input type="text" id="collector" size="32" name="collector" onchange="processTextParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_1']; ?>" />
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['COLLECTOR_NUMBER']; ?>
                    <input type="text" id="collnum" size="31" name="collnum" onchange="processTextParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_2']; ?>" />
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['COLLECTOR_DATE']; ?>
                    <input type="text" id="eventdate1" size="32" name="eventdate1" style="width:100px;" onchange="processTextParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_3']; ?>" /> -
                    <input type="text" id="eventdate2" size="32" name="eventdate2" style="width:100px;" onchange="processTextParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_4']; ?>" />
                </div>
                <div style="float:right;">
                    <input type="submit" class="nextbtn" value="Next" />
                </div>
                <div>
                    <h1><?php echo $GLOBALS['SEARCHTEXT']['SPECIMEN_HEADER']; ?></h1>
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['OCCURRENCE_REMARKS']; ?> <input type="text" id="occurrenceRemarks" size="50" name="occurrenceRemarks" onchange="processTextParamChange();" />
                </div>
                <div>
                    <?php echo $GLOBALS['SEARCHTEXT']['CATALOG_NUMBER']; ?>
                    <input type="text" id="catnum" size="32" name="catnum" onchange="processTextParamChange();" title="<?php echo $GLOBALS['SEARCHTEXT']['TITLE_TEXT_1']; ?>" />
                </div>
                <div>
                    <input name="othercatnum" id="othercatnum"  type="checkbox" onchange="processTextParamChange();" value="1" checked /> <?php echo $GLOBALS['SEARCHTEXT']['INCLUDE_OTHER_CATNUM']; ?>
                </div>
                <div>
                    <input type='checkbox' name='typestatus' id='typestatus' onchange="processTextParamChange();" value='1' /> <?php echo $GLOBALS['SEARCHTEXT']['TYPE']; ?>
                </div>
                <div>
                    <input type='checkbox' name='hasaudio' id='hasaudio' onchange="processTextParamChange();" value='1' /> <?php echo $GLOBALS['SEARCHTEXT']['HAS_AUDIO']; ?>
                </div>
                <div>
                    <input type='checkbox' name='hasimages' id='hasimages' onchange="processTextParamChange();" value='1' /> <?php echo $GLOBALS['SEARCHTEXT']['HAS_IMAGE']; ?>
                </div>
                <div>
                    <input type='checkbox' name='hasvideo' id='hasvideo' onchange="processTextParamChange();" value='1' /> <?php echo $GLOBALS['SEARCHTEXT']['HAS_VIDEO']; ?>
                </div>
                <div>
                    <input type='checkbox' name='hasmedia' id='hasmedia' onchange="processTextParamChange();" value='1' /> <?php echo $GLOBALS['SEARCHTEXT']['HAS_MEDIA']; ?>
                </div>
                <div id="searchGeneticCheckbox">
                    <input type='checkbox' name='hasgenetic' id='hasgenetic' onchange="processTextParamChange();" value='1' /> <?php echo $GLOBALS['SEARCHTEXT']['HAS_GENETIC']; ?>
                </div>
                <input type="hidden" id="polyArr" name="polyArr" value="" />
                <input type="hidden" id="circleArr" name="circleArr" value="" />
                <input type="hidden" id="queryId" name="queryId" value='<?php echo $queryId; ?>' />
            </form>
            <template v-if="showSpatialPopup">
                <spatial-analysis-popup
                        :bottom-lat="bottomLatValue"
                        :circle-arr="circleArrValue"
                        :left-long="leftLongValue"
                        :point-lat="pointLatValue"
                        :point-long="pointLongValue"
                        :poly-arr="polyArrValue"
                        :radius="radiusValue"
                        :right-long="rightLongValue"
                        :show-popup="showSpatialPopup"
                        :upper-lat="upperLatValue"
                        :window-type="popupWindowType"
                        @update:spatial-data="processSpatialData"
                        @close:popup="closePopup();"
                ></spatial-analysis-popup>
            </template>
        </div>
        <?php
        include(__DIR__ . '/../footer.php');
        include_once(__DIR__ . '/../config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/misc/colorPicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/copyURLButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/datePicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/listDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDataDownloader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/tableDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRecordsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSelectionsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSymbologyTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanelLeftShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanelTopShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSidePanelShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSideButtonTray.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRasterColorScaleSelect.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialVectorToolsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialPointVectorToolsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSearchCriteriaTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSearchCollectionsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSearchCriteriaExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRecordsSymbologyExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialVectorToolsExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRasterToolsExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSidePanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialDrawToolSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialBaseLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialActiveLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialMapSettingsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerLayerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerLayerGroupElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerQuerySelectorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/recordInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script>
            const textSearchModule = Vue.createApp({
                components: {
                    'spatial-analysis-popup': spatialAnalysisPopup
                },
                setup() {
                    const bottomLatValue = Vue.ref(null);
                    const circleArrValue = Vue.ref(null);
                    const leftLongValue = Vue.ref(null);
                    const pointLatValue = Vue.ref(null);
                    const pointLongValue = Vue.ref(null);
                    const polyArrValue = Vue.ref(null);
                    const queryId = Vue.computed(() => document.getElementById('queryId').value);
                    const radiusValue = Vue.ref(null);
                    const rightLongValue = Vue.ref(null);
                    const showSpatialPopup = Vue.ref(false);
                    const popupWindowType = Vue.ref(null);
                    const upperLatValue = Vue.ref(null);

                    function clearInputValues() {
                        bottomLatValue.value = null;
                        circleArrValue.value = null;
                        leftLongValue.value = null;
                        pointLatValue.value = null;
                        pointLongValue.value = null;
                        polyArrValue.value = null;
                        radiusValue.value = null;
                        rightLongValue.value = null;
                        upperLatValue.value = null;
                    }

                    function closePopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        clearInputValues();
                    }

                    function openPopup(type) {
                        setInputValues();
                        popupWindowType.value = type;
                        showSpatialPopup.value = true;
                    }

                    function processSpatialData(data) {
                        if(popupWindowType.value.includes('box') && data.hasOwnProperty('boundingBoxArr')){
                            document.getElementById('upperlat').value = data.boundingBoxArr.upperlat;
                            document.getElementById('bottomlat').value = data.boundingBoxArr.bottomlat;
                            document.getElementById('leftlong').value = data.boundingBoxArr.leftlong;
                            document.getElementById('rightlong').value = data.boundingBoxArr.rightlong;
                        }
                        else if(popupWindowType.value.includes('circle') && data.hasOwnProperty('circleArr')){
                            document.getElementById('pointlat').value = data.circleArr[0].pointlat;
                            document.getElementById('pointlong').value = data.circleArr[0].pointlong;
                            document.getElementById('radiusunits').value = 'km';
                            document.getElementById('radiustemp').value = (data.circleArr[0].radius / 1000);
                            document.getElementById('radius').value = data.circleArr[0].radius;
                            document.getElementById('groundradius').value = data.circleArr[0].groundradius;
                        }
                        else if(popupWindowType.value === 'input'){
                            if(data.hasOwnProperty('circleArr') || data.hasOwnProperty('polyArr')){
                                document.getElementById("spatialParamasNoCriteria").style.display = "none";
                                document.getElementById("spatialParamasCriteria").style.display = "block";
                                if(data.hasOwnProperty('circleArr')){
                                    document.getElementById('circleArr').value = JSON.stringify(data.circleArr);
                                }
                                else if(data.hasOwnProperty('polyArr')){
                                    document.getElementById('polyArr').value = JSON.stringify(data.polyArr);
                                }
                            }
                            else{
                                document.getElementById("spatialParamasNoCriteria").style.display = "block";
                                document.getElementById("spatialParamasCriteria").style.display = "none";
                            }
                        }
                    }

                    function setInputValues() {
                        bottomLatValue.value = document.getElementById('bottomlat').value;
                        circleArrValue.value = document.getElementById('circleArr').value !== '' ? JSON.parse(document.getElementById('circleArr').value) : null;
                        leftLongValue.value = document.getElementById('leftlong').value;
                        pointLatValue.value = document.getElementById('pointlat').value;
                        pointLongValue.value = document.getElementById('pointlong').value;
                        polyArrValue.value = document.getElementById('polyArr').value !== '' ? JSON.parse(document.getElementById('polyArr').value) : null;
                        radiusValue.value = document.getElementById('radius').value;
                        rightLongValue.value = document.getElementById('rightlong').value;
                        upperLatValue.value = document.getElementById('upperlat').value;
                    }

                    return {
                        bottomLatValue,
                        circleArrValue,
                        leftLongValue,
                        pointLatValue,
                        pointLongValue,
                        polyArrValue,
                        popupWindowType,
                        queryId,
                        radiusValue,
                        rightLongValue,
                        showSpatialPopup,
                        upperLatValue,
                        closePopup,
                        openPopup,
                        processSpatialData
                    }
                }
            });
            textSearchModule.use(Quasar, { config: {} });
            textSearchModule.use(Pinia.createPinia());
            textSearchModule.mount('#innertext');
        </script>
    </body>
</html>
