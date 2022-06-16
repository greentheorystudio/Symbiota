<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['IS_ADMIN']) {
    header('Location: ../index.php');
}

$confManager = new ConfigurationManager();

$fullConfArr = $confManager->getConfigurationsArr();
$coreConfArr = $fullConfArr['core'];
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Spatial Configuration Manager</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/ol.css?ver=20220209" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20210415" type="text/css" rel="stylesheet" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jscolor/jscolor.js?ver=13" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/shared.js?ver=20220310" type="text/javascript"></script>
    <style type="text/css">
        .map {
            width:95%;
            height:650px;
            margin-left: auto;
            margin-right: auto;
        }

        #mapinfo, #mapscale_us, #mapscale_metric {
            display: none;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/ol/ol.js?ver=20220615" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/spatial.module.js?ver=20220615" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tabs').tabs({
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
            $('#pointsBorderWidth').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#pointsPointRadius').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#pointsSelectionsBorderWidth').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#shapesBorderWidth').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#shapesPointRadius').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#shapesOpacity').spinner({
                step: 0.1,
                min: 0,
                max: 1,
                numberFormat: "n"
            });
            $('#shapesSelectionsBorderWidth').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#shapesSelectionsOpacity').spinner({
                step: 0.1,
                min: 0,
                max: 1,
                numberFormat: "n"
            });
            $('#dragDropBorderWidth').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#dragDropPointRadius').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#dragDropOpacity').spinner({
                step: 0.1,
                min: 0,
                max: 1,
                numberFormat: "n"
            });
            jscolor.init();
        });

        function formatPath(path){
            if(path.charAt(path.length - 1) === '/'){
                path = path.substring(0, path.length - 1);
            }
            if(path.charAt(0) !== '/'){
                path = '/' + path;
            }
            return path;
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
            <li><a href='#mapwindow'>Map Window</a></li>
            <li><a href="#symbology">Symbology</a></li>
        </ul>

        <div id="mapwindow">
            <div style="width:95%;margin: 20px auto;">
                Adjust the Base Layer and zoom level, and move the map below to how you would like maps to open by default within the portal.
                Then click the Save Spatial Defaults button to save the settings.
                <div style="display:flex;justify-content: right;">
                    <button type="button" onclick="processSaveDisplaySettings();">Save Spatial Defaults</button>
                </div>
            </div>
            <?php include_once(__DIR__ . '/../spatial/viewerElement.php'); ?>
            <div style="clear:both;width:100%;height:40px;"></div>
        </div>

        <div id="symbology">
            <fieldset style="margin: 10px 0;">
                <legend><b>Points Layer</b></legend>
                <div style="padding:5px;margin-top:5px;display:flex;flex-direction:column;width:90%;margin-left:auto;margin-right:auto;">
                    <div style="display:flex;justify-content:space-evenly;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Border color: </span>
                            <input id="pointsBorderColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_POINT_BORDER_COLOR']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Fill color: </span>
                            <input id="pointsFillColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_POINT_FILL_COLOR']; ?>" />
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Border width (px): </span>
                            <input id="pointsBorderWidth" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_POINT_BORDER_WIDTH']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Point radius (px): </span>
                            <input id="pointsPointRadius" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_POINT_POINT_RADIUS']; ?>" />
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Selections Border color: </span>
                            <input id="pointsSelectionsBorderColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_COLOR']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Selections Border width (px): </span>
                            <input id="pointsSelectionsBorderWidth" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH']; ?>" />
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>Shapes Layer</b></legend>
                <div style="padding:5px;margin-top:5px;display:flex;flex-direction:column;width:90%;margin-left:auto;margin-right:auto;">
                    <div style="display:flex;justify-content:space-evenly;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Border color: </span>
                            <input id="shapesBorderColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_BORDER_COLOR']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Fill color: </span>
                            <input id="shapesFillColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_FILL_COLOR']; ?>" />
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Border width (px): </span>
                            <input id="shapesBorderWidth" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_BORDER_WIDTH']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Point radius (px): </span>
                            <input id="shapesPointRadius" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_POINT_RADIUS']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Opacity: </span>
                            <input id="shapesOpacity" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_OPACITY']; ?>" />
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Selections Border color: </span>
                            <input id="shapesSelectionsBorderColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Selections Fill color: </span>
                            <input id="shapesSelectionsFillColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR']; ?>" />
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Selections Border width (px): </span>
                            <input id="shapesSelectionsBorderWidth" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Selections Opacity: </span>
                            <input id="shapesSelectionsOpacity" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_OPACITY']; ?>" />
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>Drag and Dropped Vector Layers</b></legend>
                <div style="padding:5px;margin-top:5px;display:flex;flex-direction:column;width:90%;margin-left:auto;margin-right:auto;">
                    <div style="display:flex;justify-content:space-evenly;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Border color: </span>
                            <input id="dragDropBorderColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_DRAGDROP_BORDER_COLOR']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Fill color: </span>
                            <input id="dragDropFillColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" value="<?php echo $GLOBALS['SPATIAL_DRAGDROP_FILL_COLOR']; ?>" />
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Border width (px): </span>
                            <input id="dragDropBorderWidth" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_DRAGDROP_BORDER_WIDTH']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Point radius (px): </span>
                            <input id="dragDropPointRadius" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_DRAGDROP_POINT_RADIUS']; ?>" />
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Opacity: </span>
                            <input id="dragDropOpacity" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_DRAGDROP_OPACITY']; ?>" />
                        </div>
                    </div>
                </div>
            </fieldset>
            <div style="width:98%;margin: 10px auto;">
                <div style="display:flex;justify-content:right;gap:15px;">
                    <button type="button" onclick="processSetDefaultSettings();">Set Default Settings</button>
                    <button type="button" onclick="processSaveSymbologySettings();">Save Settings</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
<script type="text/javascript">
    function processSaveDisplaySettings(){
        const data = {};
        const baseLayerValue = document.getElementById('base-map').value;
        const zoomValue = map.getView().getZoom();
        const centerPoint = map.getView().getCenter();
        const centerPointFixed = ol.proj.transform(centerPoint, 'EPSG:3857', 'EPSG:4326');
        const centerPointValue = '[' + centerPointFixed.toString() + ']';
        data['SPATIAL_INITIAL_BASE_LAYER'] = baseLayerValue;
        data['SPATIAL_INITIAL_ZOOM'] = zoomValue;
        data['SPATIAL_INITIAL_CENTER'] = centerPointValue;
        const jsonData = JSON.stringify(data);
        const http = new XMLHttpRequest();
        const url = "rpc/configurationModelController.php";
        let params = 'action=update&data='+jsonData;
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                location.reload();
            }
        };
        http.send(params);
    }

    function processSaveSymbologySettings(){
        const data = {};
        const pointsBorderColorValue = document.getElementById('pointsBorderColor').value;
        const pointsFillColorValue = document.getElementById('pointsFillColor').value;
        const pointsBorderWidthValue = $('#pointsBorderWidth').spinner( "value" );
        const pointsPointRadiusValue = $('#pointsPointRadius').spinner( "value" );
        const pointsSelectionsBorderColorValue = document.getElementById('pointsSelectionsBorderColor').value;
        const pointsSelectionsBorderWidthValue = $('#pointsSelectionsBorderWidth').spinner( "value" );
        const shapesBorderColorValue = document.getElementById('shapesBorderColor').value;
        const shapesFillColorValue = document.getElementById('shapesFillColor').value;
        const shapesBorderWidthValue = $('#shapesBorderWidth').spinner( "value" );
        const shapesPointRadiusValue = $('#shapesPointRadius').spinner( "value" );
        const shapesOpacityValue = $('#shapesOpacity').spinner( "value" );
        const shapesSelectionsBorderColorValue = document.getElementById('shapesSelectionsBorderColor').value;
        const shapesSelectionsFillColorValue = document.getElementById('shapesSelectionsFillColor').value;
        const shapesSelectionsBorderWidthValue = $('#shapesSelectionsBorderWidth').spinner( "value" );
        const shapesSelectionsOpacityValue = $('#shapesSelectionsOpacity').spinner( "value" );
        const dragDropBorderColorValue = document.getElementById('dragDropBorderColor').value;
        const dragDropFillColorValue = document.getElementById('dragDropFillColor').value;
        const dragDropBorderWidthValue = $('#dragDropBorderWidth').spinner( "value" );
        const dragDropPointRadiusValue = $('#dragDropPointRadius').spinner( "value" );
        const dragDropOpacityValue = $('#dragDropOpacity').spinner( "value" );
        data['SPATIAL_POINT_FILL_COLOR'] = '';
        data['SPATIAL_POINT_BORDER_COLOR'] = '';
        data['SPATIAL_POINT_BORDER_WIDTH'] = '';
        data['SPATIAL_POINT_POINT_RADIUS'] = '';
        data['SPATIAL_POINT_SELECTIONS_BORDER_COLOR'] = '';
        data['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH'] = '';
        data['SPATIAL_SHAPES_BORDER_COLOR'] = '';
        data['SPATIAL_SHAPES_FILL_COLOR'] = '';
        data['SPATIAL_SHAPES_BORDER_WIDTH'] = '';
        data['SPATIAL_SHAPES_POINT_RADIUS'] = '';
        data['SPATIAL_SHAPES_OPACITY'] = '';
        data['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR'] = '';
        data['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR'] = '';
        data['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH'] = '';
        data['SPATIAL_SHAPES_SELECTIONS_OPACITY'] = '';
        data['SPATIAL_DRAGDROP_BORDER_COLOR'] = '';
        data['SPATIAL_DRAGDROP_FILL_COLOR'] = '';
        data['SPATIAL_DRAGDROP_BORDER_WIDTH'] = '';
        data['SPATIAL_DRAGDROP_POINT_RADIUS'] = '';
        data['SPATIAL_DRAGDROP_OPACITY'] = '';
        const deleteJsonData = JSON.stringify(data);
        const http = new XMLHttpRequest();
        const url = "rpc/configurationModelController.php";
        let params = 'action=delete&data='+deleteJsonData;
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                data['SPATIAL_POINT_FILL_COLOR'] = pointsFillColorValue;
                data['SPATIAL_POINT_BORDER_COLOR'] = pointsBorderColorValue;
                data['SPATIAL_POINT_BORDER_WIDTH'] = pointsBorderWidthValue;
                data['SPATIAL_POINT_POINT_RADIUS'] = pointsPointRadiusValue;
                data['SPATIAL_POINT_SELECTIONS_BORDER_COLOR'] = pointsSelectionsBorderColorValue;
                data['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH'] = pointsSelectionsBorderWidthValue;
                data['SPATIAL_SHAPES_BORDER_COLOR'] = shapesBorderColorValue;
                data['SPATIAL_SHAPES_FILL_COLOR'] = shapesFillColorValue;
                data['SPATIAL_SHAPES_BORDER_WIDTH'] = shapesBorderWidthValue;
                data['SPATIAL_SHAPES_POINT_RADIUS'] = shapesPointRadiusValue;
                data['SPATIAL_SHAPES_OPACITY'] = shapesOpacityValue;
                data['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR'] = shapesSelectionsBorderColorValue;
                data['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR'] = shapesSelectionsFillColorValue;
                data['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH'] = shapesSelectionsBorderWidthValue;
                data['SPATIAL_SHAPES_SELECTIONS_OPACITY'] = shapesSelectionsOpacityValue;
                data['SPATIAL_DRAGDROP_BORDER_COLOR'] = dragDropBorderColorValue;
                data['SPATIAL_DRAGDROP_FILL_COLOR'] = dragDropFillColorValue;
                data['SPATIAL_DRAGDROP_BORDER_WIDTH'] = dragDropBorderWidthValue;
                data['SPATIAL_DRAGDROP_POINT_RADIUS'] = dragDropPointRadiusValue;
                data['SPATIAL_DRAGDROP_OPACITY'] = dragDropOpacityValue;
                const addJsonData = JSON.stringify(data);
                let params = 'action=add&data='+addJsonData;
                //console.log(url+'?'+params);
                http.open("POST", url, true);
                http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                http.onreadystatechange = function() {
                    if(http.readyState === 4 && http.status === 200) {
                        location.reload();
                    }
                };
                http.send(params);
            }
        };
        http.send(params);
    }

    function processSetDefaultSettings(){
        document.getElementById('pointsBorderColor').value = '000000';
        document.getElementById('pointsFillColor').value = 'E69E67';
        $('#pointsBorderWidth').spinner( "value", 1 );
        $('#pointsPointRadius').spinner( "value", 7 );
        document.getElementById('pointsSelectionsBorderColor').value = '10D8E6';
        $('#pointsSelectionsBorderWidth').spinner( "value", 2 );
        document.getElementById('shapesBorderColor').value = '3399CC';
        document.getElementById('shapesFillColor').value = 'FFFFFF';
        $('#shapesBorderWidth').spinner( "value", 2 );
        $('#shapesPointRadius').spinner( "value", 5 );
        $('#shapesOpacity').spinner( "value", 0.4 );
        document.getElementById('shapesSelectionsBorderColor').value = '0099FF';
        document.getElementById('shapesSelectionsFillColor').value = 'FFFFFF';
        $('#shapesSelectionsBorderWidth').spinner( "value", 5 );
        $('#shapesSelectionsOpacity').spinner( "value", 0.5 );
        document.getElementById('dragDropBorderColor').value = '000000';
        document.getElementById('dragDropFillColor').value = 'AAAAAA';
        $('#dragDropBorderWidth').spinner( "value", 2 );
        $('#dragDropPointRadius').spinner( "value", 5 );
        $('#dragDropOpacity').spinner( "value", 0.3 );
        processSaveSymbologySettings();
    }
</script>
</body>
</html>
