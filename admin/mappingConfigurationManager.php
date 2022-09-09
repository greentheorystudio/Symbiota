<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['IS_ADMIN']) {
    header('Location: ../index.php');
}

$confManager = new ConfigurationManager();

$fullConfArr = $confManager->getConfigurationsArr();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Configuration Manager</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/bootstrap.min.css?ver=20220225" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20220209" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20210415" type="text/css" rel="stylesheet" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.nestedSortable.js?ver=20220624" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jscolor/jscolor.js?ver=13" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.popupoverlay.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/shared.js?ver=20220809" type="text/javascript"></script>
    <style>
        .map {
            width:95%;
            height:650px;
            margin-left: auto;
            margin-right: auto;
        }
        #mapinfo, #mapscale_us, #mapscale_metric {
            display: none;
        }
        .placeholder {
            outline: 1px dashed #4183C4;
        }
        .layer-group-header {
            margin-top: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-content: center;
            align-items: center;
        }
        .layer-header {
            display: flex;
            justify-content: space-between;
            align-content: center;
            align-items: center;
        }
        .layer-group-container, .layerContent{
            background: #FFF;
        }
        .layer-group-container{
            padding: 10px;
        }
        .layerContent{
            padding: 5px;
        }
        ol.sortable{
            padding: 0 10px;
        }
        ol.sortable, ol.sortable ol {
            list-style-type: none;
        }

        ol.sortable li, ol.sortable ol li {
            display: list-item;
        }
        ol.sortable li, ol.sortable li ol li {
            border: 1px solid #d4d4d4;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            cursor: move;
            border-color: #D4D4D4 #D4D4D4 #BCBCBC;
            background: #EBEBEB;
            margin: 10px 0;
        }
        li.group {
            padding: 0 5px 5px 5px;
        }
        li.layer {
            padding: 5px 5px 5px 5px;
        }
        ol.sortable li ol {
            padding: 10px;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol/ol.js?ver=20220615" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/spatial.module.core.js?ver=20220907" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/admin.spatial.js?ver=20220909" type="text/javascript"></script>
    <script type="text/javascript">
        const maxUploadSizeMB = <?php echo $GLOBALS['MAX_UPLOAD_FILESIZE']; ?>;
        let serverayerArrObject;
        let layerArr;
        let layerData = {};

        $(document).ready(function() {
            $( "#addLayerDateAquired" ).datepicker({ dateFormat: 'yy-mm-dd' });
            $( "#editLayerDateAquired" ).datepicker({ dateFormat: 'yy-mm-dd' });
            $('#tabs').tabs({
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
            $('#pointsClusterDistance').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#pointsHeatMapRadius').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
            });
            $('#pointsHeatMapBlur').spinner({
                step: 1,
                min: 0,
                numberFormat: "n"
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

            $('ol.sortable').nestedSortable({
                doNotClear: true,
                forcePlaceholderSize: true,
                handle: 'div',
                helper: 'clone',
                items: 'li',
                opacity: .6,
                placeholder: 'placeholder',
                revert: 250,
                tabSize: 0,
                tolerance: 'pointer',
                toleranceElement: '> div',
                maxLevels: 2,
                expandOnHover: 700,
                startCollapsed: false,
                isAllowed: function (placeholder, placeholderParent, currentItem) {
                    if(!placeholderParent || !currentItem[0].id.startsWith("layerGroup-")){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
            });
            $('#layergroupeditwindow').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
            $('#layereditwindow').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
        });
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <div style="padding: 0 20px 10px;display:flex;justify-content: space-between;">
        <div id="statusStr" style="font-weight:bold;color:red;"></div>
        <div onclick="openTutorialWindow('../tutorial/admin/mappingConfigurationManager/index.php');" title="Open Tutorial Window">
            <i style="height:20px;width:20px;cursor:pointer;" class="far fa-question-circle"></i>
        </div>
    </div>
    <div id="tabs" style="width:95%;">
        <ul>
            <li><a href='#mapwindow'>Map Window</a></li>
            <li><a href="#symbology">Symbology</a></li>
            <li><a href="#layers" onclick="setLayersList();">Layers</a></li>
        </ul>

        <div id="mapwindow">
            <fieldset style="margin: 10px 0;padding:15px;">
                <div style="width:95%;display:flex;justify-content:space-between;align-items:center;margin:auto;">
                    <div style="width:80%;">
                        Adjust the Base Layer and zoom level, and move the map below to how you would like map windows to open within the portal.
                        Then click the Save Settings button to save the settings.
                    </div>
                    <div>
                        <button type="button" onclick="processSaveDisplaySettings();">Save Settings</button>
                    </div>
                </div>
            </fieldset>
            <?php include_once(__DIR__ . '/../spatial/viewerElement.php'); ?>
            <div style="clear:both;width:100%;height:40px;"></div>
        </div>

        <div id="symbology">
            <fieldset style="margin: 10px 0;padding:15px;">
                <div style="width:95%;display:flex;justify-content:space-between;margin:auto;">
                    <div>
                        <button type="button" onclick="processSetDefaultSettings();">Set Default Settings</button>
                    </div>
                    <div>
                        <button type="button" onclick="processSaveSymbologySettings();">Save Settings</button>
                    </div>
                </div>
            </fieldset>
            <fieldset style="margin: 10px 0;">
                <legend><b>Points Layer</b></legend>
                <div style="padding:5px;margin-top:5px;display:flex;flex-direction:column;width:90%;margin-left:auto;margin-right:auto;">
                    <div style="display:flex;justify-content:space-evenly;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Cluster Points: </span>
                            <input type='checkbox' id='pointsCluster' <?php echo ($GLOBALS['SPATIAL_POINT_CLUSTER']?'checked':''); ?>>
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Cluster Distance (px): </span>
                            <input id="pointsClusterDistance" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_POINT_CLUSTER_DISTANCE']; ?>" />
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Display Heat Map: </span>
                            <input type='checkbox' id='pointsDisplayHeatMap' <?php echo ($GLOBALS['SPATIAL_POINT_DISPLAY_HEAT_MAP']?'checked':''); ?>>
                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Heat Map Radius (px): </span>
                            <input id="pointsHeatMapRadius" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_POINT_HEAT_MAP_RADIUS']; ?>" />

                        </div>
                        <div style="display:flex;align-items:center;">
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Heat Map Blur (px): </span>
                            <input id="pointsHeatMapBlur" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_POINT_HEAT_MAP_BLUR']; ?>" />
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
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
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Fill Opacity: </span>
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
                <legend><b>Drag and Dropped Layers</b></legend>
                <div style="padding:5px;margin-top:5px;display:flex;flex-direction:column;width:90%;margin-left:auto;margin-right:auto;">
                    <div style="display:flex;justify-content:space-evenly;">
                        <div style="display:flex;justify-content:space-evenly;margin-top:15px;">
                            <div style="display:flex;align-items:center;">
                                <span style="font-weight:bold;margin-right:10px;font-size:12px;">Raster color scale: </span>
                                <select id="dragDropRasterColorScale">
                                    <option value="autumn" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'autumn'?'selected':''); ?>>Autumn</option>
                                    <option value="blackbody" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'blackbody'?'selected':''); ?>>Blackbody</option>
                                    <option value="bluered" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'bluered'?'selected':''); ?>>Bluered</option>
                                    <option value="bone" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'bone'?'selected':''); ?>>Bone</option>
                                    <option value="cool" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'cool'?'selected':''); ?>>Cool</option>
                                    <option value="copper" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'copper'?'selected':''); ?>>Copper</option>
                                    <option value="earth" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'earth'?'selected':''); ?>>Earth</option>
                                    <option value="electric" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'electric'?'selected':''); ?>>Electric</option>
                                    <option value="greens" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'greens'?'selected':''); ?>>Greens</option>
                                    <option value="greys" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'greys'?'selected':''); ?>>Greys</option>
                                    <option value="hot" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'hot'?'selected':''); ?>>Hot</option>
                                    <option value="hsv" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'hsv'?'selected':''); ?>>Hsv</option>
                                    <option value="inferno" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'inferno'?'selected':''); ?>>Inferno</option>
                                    <option value="jet" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'jet'?'selected':''); ?>>Jet</option>
                                    <option value="magma" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'magma'?'selected':''); ?>>Magma</option>
                                    <option value="picnic" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'picnic'?'selected':''); ?>>Picnic</option>
                                    <option value="plasma" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'plasma'?'selected':''); ?>>Plasma</option>
                                    <option value="portland" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'portland'?'selected':''); ?>>Portland</option>
                                    <option value="rainbow" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'rainbow'?'selected':''); ?>>Rainbow</option>
                                    <option value="rdbu" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'rdbu'?'selected':''); ?>>Rdbu</option>
                                    <option value="spring" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'spring'?'selected':''); ?>>Spring</option>
                                    <option value="summer" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'summer'?'selected':''); ?>>Summer</option>
                                    <option value="turbo" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'turbo'?'selected':''); ?>>Turbo</option>
                                    <option value="viridis" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'viridis'?'selected':''); ?>>Viridis</option>
                                    <option value="winter" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'winter'?'selected':''); ?>>Winter</option>
                                    <option value="ylgnbu" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'ylgnbu'?'selected':''); ?>>Ylgnbu</option>
                                    <option value="ylorrd" <?php echo ($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === 'ylorrd'?'selected':''); ?>>Ylorrd</option>
                                </select>
                            </div>
                        </div>
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
                            <span style="font-weight:bold;margin-right:10px;font-size:12px;">Fill Opacity: </span>
                            <input id="dragDropOpacity" style="width:25px;" value="<?php echo $GLOBALS['SPATIAL_DRAGDROP_OPACITY']; ?>" />
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>

        <div id="layers">
            <fieldset style="margin: 10px 0;padding:15px;">
                <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;">
                    <div style="width:95%;display:flex;justify-content:space-between;margin:auto;">
                        <div style="display:flex;justify-content:flex-start;gap:10px;">
                            <div>
                                <button type="button" onclick="showAddLayer();">Add Layer</button>
                            </div>
                            <div>
                                <button type="button" onclick="showAddLayerGroup();">Add Layer Group</button>
                            </div>
                        </div>
                        <div>
                            <button type="button" onclick="saveLayerConfigChanges();">Save Settings</button>
                        </div>
                    </div>
                    <div id="addLayerGroupDiv" style="width:95%;display:none;">
                        <fieldset style="margin: 10px 0;padding:10px;">
                            <legend><b>Add Layer Group</b></legend>
                            <div style="display:flex;justify-content: space-between;">
                                <div>
                                    <span style="font-weight:bold;margin-right:10px;font-size:14px;">Group Name: </span>
                                    <input type="text" id="addLayerGroupName" style="width:400px;" value="" />
                                </div>
                                <div style="display:flex;justify-content: flex-end;gap:10px;">
                                    <button type="button" onclick="hideAddLayerGroup();">Cancel</button>
                                    <button type="button" onclick="createLayerGroup();">Add</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div id="addLayerDiv" style="width:95%;display:none;">
                        <fieldset style="margin: 10px 0;padding:10px;">
                            <legend><b>Add Layer</b></legend>
                            <div style="display:flex;justify-content: space-between;align-content: center;align-items: center;">
                                <div style="font-weight:bold;margin-right:10px;font-size:14px;">File:</div>
                                <div style="width:550px;display:flex;justify-content:flex-start;">
                                    <input id='addLayerFile' type='file' onchange="validateFileUpload('add');" />
                                </div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
                                <div style="font-weight:bold;margin-right:10px;font-size:14px;">Layer Name:</div>
                                <div><input type="text" id="addLayerName" style="width:550px;" value="" /></div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
                                <div style="font-weight:bold;margin-right:10px;font-size:14px;">Description:</div>
                                <div>
                                    <textarea id="addLayerDescription" style="width:550px;height:60px;resize:vertical;"></textarea>
                                </div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
                                <div style="font-weight:bold;margin-right:10px;font-size:14px;">Provided By:</div>
                                <div><input type="text" id="addLayerProvidedBy" style="width:550px;" value="" /></div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
                                <div style="font-weight:bold;margin-right:10px;font-size:14px;">Source URL:</div>
                                <div><input type="text" id="addLayerSourceURL" style="width:550px;" value="" onchange="validateSourceURL('add');" /></div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
                                <div style="font-weight:bold;margin-right:10px;font-size:14px;">Date Aquired:</div>
                                <div style="width:550px;display:flex;justify-content:flex-start;">
                                    <input type="text" id="addLayerDateAquired" style="width:100px;" />
                                </div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: flex-end;gap:10px;">
                                <button type="button" onclick="hideAddLayer();">Cancel</button>
                                <button type="button" onclick="uploadLayerFile();">Add</button>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <div>
                    <ol id="layerList" class="sortable"></ol>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div id="layergroupeditwindow" data-role="popup" class="well" style="width:60%;min-width:425px;height:150px;font-size:14px;">
    <fieldset style="padding:15px;">
        <div>
            <span style="font-weight:bold;margin-right:10px;font-size:14px;">Group Name: </span>
            <input type="text" id="editLayerGroupName" style="width:400px;" value="" />
            <input type="hidden" id="editLayerGroupId" value="" />
        </div>
    </fieldset>
    <div style="margin-top:15px;width:95%;margin: 15px auto;padding: 0 10px;display:flex;justify-content: space-between;">
        <div>
            <button onclick="closePopup('layergroupeditwindow');">Cancel</button>
        </div>
        <div style="display:flex;gap:15px;">
            <div>
                <button onclick="deleteLayerGroup();">Delete Layer Group</button>
            </div>
            <div>
                <button onclick="saveLayerGroupEdits();">Save</button>
            </div>
        </div>
    </div>
</div>
<div id="layereditwindow" data-role="popup" class="well" style="width:60%;min-width:800px;min-height:300px;font-size:14px;">
    <fieldset style="padding:15px;">
        <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
            <div style="font-weight:bold;margin-right:10px;font-size:14px;">Layer Name:</div>
            <div>
                <input type="text" id="editLayerName" style="width:550px;" value="" />
                <input type="hidden" id="editLayerId" value="" />
            </div>
        </div>
        <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
            <div style="font-weight:bold;margin-right:10px;font-size:14px;">Description:</div>
            <div>
                <textarea id="editLayerDescription" style="width:550px;height:60px;resize:vertical;"></textarea>
            </div>
        </div>
        <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
            <div style="font-weight:bold;margin-right:10px;font-size:14px;">Provided By:</div>
            <div><input type="text" id="editLayerProvidedBy" style="width:550px;" value="" /></div>
        </div>
        <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
            <div style="font-weight:bold;margin-right:10px;font-size:14px;">Source URL:</div>
            <div><input type="text" id="editLayerSourceURL" style="width:550px;" value="" onchange="validateSourceURL('edit');" /></div>
        </div>
        <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
            <div style="font-weight:bold;margin-right:10px;font-size:14px;">Date Aquired:</div>
            <div style="width:550px;display:flex;justify-content:flex-start;">
                <input type="text" id="editLayerDateAquired" style="width:100px;" />
            </div>
        </div>
        <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
            <div style="font-weight:bold;margin-right:10px;font-size:14px;">Date Uploaded:</div>
            <div id="editLayerDateUploaded" style="width:550px;display:flex;justify-content:flex-start;"></div>
        </div>
        <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
            <div style="font-weight:bold;margin-right:10px;font-size:14px;">File:</div>
            <div id="editLayerFile" style="width:550px;display:flex;justify-content:flex-start;"></div>
        </div>
    </fieldset>
    <fieldset id="editVectorSymbology" style="display:none;margin-top:10px;padding:15px;flex-direction:column;">
        <legend><b>Initial Symbology</b></legend>
        <div style="display:flex;justify-content:space-evenly;">
            <div style="display:flex;align-items:center;">
                <span style="font-weight:bold;margin-right:10px;font-size:12px;">Border color: </span>
                <input id="editLayerBorderColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" />
            </div>
            <div style="display:flex;align-items:center;">
                <span style="font-weight:bold;margin-right:10px;font-size:12px;">Fill color: </span>
                <input id="editLayerFillColor" class="color" style="cursor:pointer;border:1px black solid;height:15px;width:15px;margin-bottom:-2px;font-size:0;" />
            </div>
        </div>
        <div style="display:flex;justify-content:space-evenly;margin-top:10px;">
            <div style="display:flex;align-items:center;">
                <span style="font-weight:bold;margin-right:10px;font-size:12px;">Border width (px): </span>
                <input id="editLayerBorderWidth" style="width:25px;" />
            </div>
            <div style="display:flex;align-items:center;">
                <span style="font-weight:bold;margin-right:10px;font-size:12px;">Point radius (px): </span>
                <input id="editLayerPointRadius" style="width:25px;" />
            </div>
            <div style="display:flex;align-items:center;">
                <span style="font-weight:bold;margin-right:10px;font-size:12px;">Fill Opacity: </span>
                <input id="editLayerOpacity" style="width:25px;" />
            </div>
        </div>
    </fieldset>
    <fieldset id="editRasterSymbology" style="display:none;margin-top:10px;padding:15px;flex-direction:column;">
        <legend><b>Initial Symbology</b></legend>
        <div style="display:flex;justify-content:center;align-items:center;">
            <div>
                <span style="font-weight:bold;margin-right:10px;font-size:12px;">Raster color scale: </span>
                <select id="editLayerColorScale">
                    <option value="autumn">Autumn</option>
                    <option value="blackbody">Blackbody</option>
                    <option value="bluered">Bluered</option>
                    <option value="bone">Bone</option>
                    <option value="cool">Cool</option>
                    <option value="copper">Copper</option>
                    <option value="earth">Earth</option>
                    <option value="electric">Electric</option>
                    <option value="greens">Greens</option>
                    <option value="greys">Greys</option>
                    <option value="hot">Hot</option>
                    <option value="hsv">Hsv</option>
                    <option value="inferno">Inferno</option>
                    <option value="jet">Jet</option>
                    <option value="magma">Magma</option>
                    <option value="picnic">Picnic</option>
                    <option value="plasma">Plasma</option>
                    <option value="portland">Portland</option>
                    <option value="rainbow">Rainbow</option>
                    <option value="rdbu">Rdbu</option>
                    <option value="spring">Spring</option>
                    <option value="summer">Summer</option>
                    <option value="turbo">Turbo</option>
                    <option value="viridis">Viridis</option>
                    <option value="winter">Winter</option>
                    <option value="ylgnbu">Ylgnbu</option>
                    <option value="ylorrd">Ylorrd</option>
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset id="updateLayerFileBox" style="display:none;margin-top:10px;padding:15px;flex-direction:column;">
        <div style="display:flex;justify-content:flex-start;align-content:center;align-items:center;gap:15px;">
            <div style="font-weight:bold;margin-right:10px;font-size:14px;">Update File:</div>
            <div>
                <input id='layerFileUpdate' type='file' onchange="validateFileUpload('edit');" />
            </div>
        </div>
        <div style="margin-top:10px;display:flex;justify-content:flex-end;align-content:center;align-items:center;">
            <div>
                <button onclick="uploadLayerUpdateFile();">Update</button>
            </div>
        </div>
    </fieldset>
    <div style="margin-top:15px;width:95%;margin: 15px auto;padding: 0 10px;display:flex;justify-content: space-between;">
        <div>
            <button onclick="closePopup('layereditwindow');">Cancel</button>
        </div>
        <div style="display:flex;gap:15px;">
            <div>
                <button onclick="deleteLayer();">Delete Layer</button>
            </div>
            <div>
                <button onclick="openUpdateFileUpload();">Update Layer File</button>
            </div>
            <div>
                <button onclick="saveLayerEdits();">Save</button>
            </div>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
<div class="loadingModal">
    <div id="loaderAnimation"></div>
</div>
</body>
</html>
