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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.nestedSortable.js?ver=20220624" type="text/javascript"></script>
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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/ol/ol.js?ver=20220615" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/spatial.module.js?ver=20220622" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $( "#addLayerDateAquired" ).datepicker({ dateFormat: 'yy-mm-dd' });
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
    <div id="statusStr" style="margin:20px;font-weight:bold;color:red;"></div>
    <div id="tabs" style="width:95%;">
        <ul>
            <li><a href='#mapwindow'>Map Window</a></li>
            <li><a href="#symbology">Symbology</a></li>
            <li><a href="#layers" onclick="setLayersList();">Layers</a></li>
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
            <div style="width:98%;margin: 10px auto;">
                <div style="display:flex;justify-content:right;gap:15px;">
                    <button type="button" onclick="processSetDefaultSettings();">Set Default Settings</button>
                    <button type="button" onclick="processSaveSymbologySettings();">Save Settings</button>
                </div>
            </div>
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
                            <button type="button" onclick="saveLayerConfigChanges();">Save Changes</button>
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
                                    <input id='addLayerFile' type='file' onchange="validateFileUpload();" />
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
                                <div><input type="text" id="addLayerSourceURL" style="width:550px;" value="" /></div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
                                <div style="font-weight:bold;margin-right:10px;font-size:14px;">Date Aquired:</div>
                                <div style="width:550px;display:flex;justify-content:flex-start;">
                                    <input type="text" id="addLayerDateAquired" style="width:100px;" onchange="" />
                                </div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: flex-end;gap:10px;">
                                <button type="button" onclick="hideAddLayer();">Cancel</button>
                                <button type="button" onclick="">Add</button>
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
<?php
include(__DIR__ . '/../footer.php');
?>
<script type="text/javascript">
    const maxUploadSizeMB = <?php echo $GLOBALS['MAX_UPLOAD_FILESIZE']; ?>;
    let serverayerArrObject;
    let layerArr;
    let layerData = {};

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
        http.onreadystatechange = function () {
            if (http.readyState === 4 && http.status === 200) {
                document.getElementById("statusStr").innerHTML = 'Settings saved!';
                setTimeout(function () {
                    document.getElementById("statusStr").innerHTML = '';
                }, 5000);
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
        const dragDropRasterColorScaleValue = document.getElementById('dragDropRasterColorScale').value;
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
        data['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] = '';
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
                data['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] = dragDropRasterColorScaleValue;
                const addJsonData = JSON.stringify(data);
                let params = 'action=add&data=' + addJsonData;
                //console.log(url+'?'+params);
                http.open("POST", url, true);
                http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                http.onreadystatechange = function () {
                    if (http.readyState === 4 && http.status === 200) {
                        document.getElementById("statusStr").innerHTML = 'Settings saved!';
                        setTimeout(function () {
                            document.getElementById("statusStr").innerHTML = '';
                        }, 5000);
                    }
                };
                http.send(params);
            }
        };
        http.send(params);
    }

    function processSetDefaultSettings() {
        document.getElementById('pointsBorderColor').value = '000000';
        document.getElementById('pointsFillColor').value = 'E69E67';
        $('#pointsBorderWidth').spinner("value", 1);
        $('#pointsPointRadius').spinner("value", 7);
        document.getElementById('pointsSelectionsBorderColor').value = '10D8E6';
        $('#pointsSelectionsBorderWidth').spinner("value", 2);
        document.getElementById('shapesBorderColor').value = '3399CC';
        document.getElementById('shapesFillColor').value = 'FFFFFF';
        $('#shapesBorderWidth').spinner("value", 2);
        $('#shapesPointRadius').spinner("value", 5);
        $('#shapesOpacity').spinner("value", 0.4);
        document.getElementById('shapesSelectionsBorderColor').value = '0099FF';
        document.getElementById('shapesSelectionsFillColor').value = 'FFFFFF';
        $('#shapesSelectionsBorderWidth').spinner("value", 5);
        $('#shapesSelectionsOpacity').spinner("value", 0.5);
        document.getElementById('dragDropBorderColor').value = '000000';
        document.getElementById('dragDropFillColor').value = 'AAAAAA';
        $('#dragDropBorderWidth').spinner("value", 2);
        $('#dragDropPointRadius').spinner("value", 5);
        $('#dragDropOpacity').spinner("value", 0.3);
        document.getElementById('dragDropRasterColorScale').value = 'earth';
        processSaveSymbologySettings();
    }

    function setLayersList() {
        const http = new XMLHttpRequest();
        const url = "../spatial/rpc/getlayersconfig.php";
        //console.log(url);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function () {
            if (http.readyState == 4 && http.status == 200) {
                if (http.responseText) {
                    serverayerArrObject = JSON.parse(http.responseText);
                    if (serverayerArrObject.hasOwnProperty('layerConfig')) {
                        layerArr = serverayerArrObject['layerConfig'];
                        for (let i in layerArr) {
                            if(layerArr.hasOwnProperty(i)){
                                const layerId = layerArr[i]['id'];
                                const layerType = layerArr[i]['type'];
                                layerData[layerId] = {};
                                layerData[layerId]['type'] = layerType;
                                if(layerType === 'layer'){
                                    processLayerDataFromLayerArr(layerArr[i],layerId);
                                    processAddLayerListElement(layerArr[i],document.getElementById("layerList"));
                                }
                                else if(layerType === 'layerGroup'){
                                    layerData[layerId]['name'] = layerArr[i]['name'];
                                    processAddLayerListGroup(layerArr[i],document.getElementById("layerList"));
                                }
                            }
                        }
                    }
                }
            }
        };
        http.send();
    }

    function processLayerDataFromLayerArr(lArr,id) {
        layerData[id]['file'] = lArr['file'];
        layerData[id]['fileType'] = lArr['fileType'];
        layerData[id]['layerName'] = lArr['layerName'];
        layerData[id]['layerDescription'] = lArr.hasOwnProperty('layerDescription') ? lArr['layerDescription'] : '';
        layerData[id]['providedBy'] = lArr.hasOwnProperty('providedBy') ? lArr['providedBy'] : '';
        layerData[id]['sourceURL'] = lArr.hasOwnProperty('sourceURL') ? lArr['sourceURL'] : '';
        layerData[id]['dateAquired'] = lArr.hasOwnProperty('dateAquired') ? lArr['dateAquired'] : '';
        layerData[id]['dateUploaded'] = lArr.hasOwnProperty('dateUploaded') ? lArr['dateUploaded'] : '';
        layerData[id]['opacity'] = (lArr.hasOwnProperty('opacity') && lArr['opacity']) ? lArr['opacity'] : dragDropOpacity;
        if(lArr['fileType'] === 'tif'){
            layerData[id]['colorScale'] = (lArr.hasOwnProperty('colorScale') && lArr['colorScale']) ? lArr['colorScale'] : dragDropRasterColorScale;
        }
        else{
            layerData[id]['fillColor'] = (lArr.hasOwnProperty('fillColor') && lArr['fillColor']) ? lArr['fillColor'] : dragDropFillColor;
            layerData[id]['borderColor'] = (lArr.hasOwnProperty('borderColor') && lArr['borderColor']) ? lArr['borderColor'] : dragDropBorderColor;
            layerData[id]['borderWidth'] = (lArr.hasOwnProperty('borderWidth') && lArr['borderWidth']) ? lArr['borderWidth'] : dragDropBorderWidth;
            layerData[id]['pointRadius'] = (lArr.hasOwnProperty('pointRadius') && lArr['pointRadius']) ? lArr['pointRadius'] : dragDropPointRadius;
        }
    }

    function processAddLayerListElement(lArr, parentElement) {
        const layerLiId = 'layer-' + lArr['id'];
        if (!document.getElementById(layerLiId)) {
            const layerLi = buildLayerListElement(lArr);
            parentElement.appendChild(layerLi);
        }
    }

    function processAddLayerListGroup(lArr, parentElement) {
        const layerGroupdLiId = 'layerGroup-' + lArr['id'];
        if (!document.getElementById(layerGroupdLiId)) {
            const layersArr = lArr['layers'];
            const layerGroupContainerId = 'layerGroupList-' + lArr['id'];
            const layerGroupLi = document.createElement('li');
            layerGroupLi.setAttribute("id", layerGroupdLiId);
            layerGroupLi.setAttribute("class", "group");
            const layerGroupHeaderDiv = document.createElement('div');
            layerGroupHeaderDiv.setAttribute("class","layer-group-header");
            const layerGroupTitleDiv = document.createElement('div');
            layerGroupTitleDiv.setAttribute("style","display:flex;gap:15px;justify-content:flex-start;align-items:center;");
            const layerGroupTitleB = document.createElement('b');
            layerGroupTitleB.innerHTML = lArr['name'];
            layerGroupTitleDiv.appendChild(layerGroupTitleB);
            const layerGroupShowIconI = document.createElement('i');
            const layerGroupShowIconIId = 'showLayerGroupButton-' + lArr['id'];
            const layerGroupShowIconIOnclickVal = "showLayerGroup('" + lArr['id'] + "');";
            layerGroupShowIconI.setAttribute("id",layerGroupShowIconIId);
            layerGroupShowIconI.setAttribute("style","display:none;width:15px;height:15px;cursor:pointer;");
            layerGroupShowIconI.setAttribute("title","Show layers");
            layerGroupShowIconI.setAttribute("class","fas fa-plus");
            layerGroupShowIconI.setAttribute("onclick",layerGroupShowIconIOnclickVal);
            layerGroupTitleDiv.appendChild(layerGroupShowIconI);
            const layerGroupHideIconI = document.createElement('i');
            const layerGroupHideIconIId = 'hideLayerGroupButton-' + lArr['id'];
            const layerGroupHideIconIOnclickVal = "hideLayerGroup('" + lArr['id'] + "');";
            layerGroupHideIconI.setAttribute("id",layerGroupHideIconIId);
            layerGroupHideIconI.setAttribute("style","width:15px;height:15px;cursor:pointer;");
            layerGroupHideIconI.setAttribute("title","Hide layers");
            layerGroupHideIconI.setAttribute("class","fas fa-minus");
            layerGroupHideIconI.setAttribute("onclick",layerGroupHideIconIOnclickVal);
            layerGroupTitleDiv.appendChild(layerGroupHideIconI);
            layerGroupHeaderDiv.appendChild(layerGroupTitleDiv);
            const layerGroupEditIconDiv = document.createElement('div');
            const layerGroupEditIconI = document.createElement('i');
            const layerGroupEditIconIOnclickVal = "openLayerGroupEditWindow('" + lArr['id'] + "');";
            layerGroupEditIconI.setAttribute("style","width:20px;height:20px;cursor:pointer;margin-right:10px");
            layerGroupEditIconI.setAttribute("title","Edit layer group");
            layerGroupEditIconI.setAttribute("class","fas fa-edit");
            layerGroupEditIconI.setAttribute("onclick",layerGroupEditIconIOnclickVal);
            layerGroupEditIconDiv.appendChild(layerGroupEditIconI);
            layerGroupHeaderDiv.appendChild(layerGroupEditIconDiv);
            layerGroupLi.appendChild(layerGroupHeaderDiv);
            const layerGroupContainerOl = document.createElement('ol');
            layerGroupContainerOl.setAttribute("id", layerGroupContainerId);
            layerGroupContainerOl.setAttribute("class", "layer-group-container");
            layerGroupLi.appendChild(layerGroupContainerOl);
            parentElement.appendChild(layerGroupLi);
            for (let i in layersArr) {
                if (layersArr.hasOwnProperty(i)) {
                    const layerId = layersArr[i]['id'];
                    const layerType = layersArr[i]['type'];
                    layerData[layerId] = {};
                    layerData[layerId]['type'] = layerType;
                    processLayerDataFromLayerArr(layersArr[i],layerId);
                    processAddLayerListElement(layersArr[i], layerGroupContainerOl)
                }
            }
        }
    }

    function buildLayerListElement(lArr){
        const layerLiId = 'layer-' + lArr['id'];
        const layerLi = document.createElement('li');
        layerLi.setAttribute("id",layerLiId);
        layerLi.setAttribute("class","layer");
        const layerContentDiv = document.createElement('div');
        layerContentDiv.setAttribute("class","layerContent");
        const layerHeaderDiv = document.createElement('div');
        layerHeaderDiv.setAttribute("class","layer-header");
        const layerTitleDiv = document.createElement('div');
        const layerTitleB = document.createElement('b');
        layerTitleB.innerHTML = lArr['layerName'];
        layerTitleDiv.appendChild(layerTitleB);
        layerHeaderDiv.appendChild(layerTitleDiv);
        const layerEditIconDiv = document.createElement('div');
        const layerEditIconA = document.createElement('a');
        const layerEditIconAOnclickVal = "openLayerEditWindow('" + lArr['id'] + "');";
        layerEditIconA.setAttribute("href","#");
        layerEditIconA.setAttribute("onclick",layerEditIconAOnclickVal);
        const layerEditIconI = document.createElement('i');
        layerEditIconI.setAttribute("style","width:20px;height:20px;");
        layerEditIconI.setAttribute("title","Edit layer");
        layerEditIconI.setAttribute("class","fas fa-edit");
        layerEditIconA.appendChild(layerEditIconI);
        layerEditIconDiv.appendChild(layerEditIconA);
        layerHeaderDiv.appendChild(layerEditIconDiv);
        layerContentDiv.appendChild(layerHeaderDiv);
        if(lArr.hasOwnProperty('layerDescription') && lArr['layerDescription']){
            const layerDescDiv = document.createElement('div');
            layerDescDiv.innerHTML = lArr['layerDescription'];
            layerContentDiv.appendChild(layerDescDiv);
        }
        if(lArr.hasOwnProperty('providedBy') || lArr.hasOwnProperty('sourceURL')){
            layerContentDiv.appendChild(buildLayerControllerLayerProvidedByElement(lArr));
        }
        if(lArr.hasOwnProperty('dateAquired') || lArr.hasOwnProperty('dateUploaded')){
            layerContentDiv.appendChild(buildLayerControllerLayerDateElement(lArr));
        }
        const layerFileDiv = document.createElement('div');
        layerFileDiv.innerHTML = '<b>File:</b> ' + lArr['file'];
        layerContentDiv.appendChild(layerFileDiv);
        layerLi.appendChild(layerContentDiv);
        return layerLi;
    }

    function hideLayerGroup(layerId) {
        const groupId = 'layerGroupList-' + layerId;
        const hideButtonId = 'hideLayerGroupButton-' + layerId;
        const showButtonId = 'showLayerGroupButton-' + layerId;
        document.getElementById(groupId).style.display = "none";
        document.getElementById(hideButtonId).style.display = "none";
        document.getElementById(showButtonId).style.display = "block";
    }

    function showLayerGroup(layerId) {
        const groupId = 'layerGroupList-' + layerId;
        const hideButtonId = 'hideLayerGroupButton-' + layerId;
        const showButtonId = 'showLayerGroupButton-' + layerId;
        document.getElementById(groupId).style.display = "block";
        document.getElementById(hideButtonId).style.display = "block";
        document.getElementById(showButtonId).style.display = "none";
    }

    function showAddLayer() {
        document.getElementById('addLayerDiv').style.display = "block";
        document.getElementById('addLayerGroupDiv').style.display = "none";
    }

    function hideAddLayer() {
        document.getElementById('addLayerDiv').style.display = "none";
    }

    function showAddLayerGroup() {
        document.getElementById('addLayerGroupDiv').style.display = "block";
        document.getElementById('addLayerDiv').style.display = "none";
    }

    function hideAddLayerGroup() {
        document.getElementById('addLayerGroupDiv').style.display = "none";
    }

    function validateFileUpload(){
        const file = document.getElementById('addLayerFile').files[0];
        const fileType = file.name.split('.').pop().toLowerCase();
        if(fileType !== 'geojson' && fileType !== 'kml' && fileType !== 'zip' && fileType !== 'tif' && fileType !== 'tiff'){
            alert("The file you are trying to upload is a type that is not supported. Only GeoJSON, KML, shapefile, and TIF file formats are supported.");
            document.getElementById('addLayerFile').value = '';
        }
        else if(Number(file.size) > (maxUploadSizeMB * 1000 * 1000)){
            alert("The file you are trying to upload is larger than the maximum upload size of " + maxUploadSizeMB + "MB");
            document.getElementById('addLayerFile').value = '';
        }
    }

    function buildNewLayerBlockObjFromData(id,dataArr){
        const blockObj = {};
        blockObj['id'] = id;
        blockObj['type'] = 'layer';
        blockObj['file'] = dataArr['file'];
        blockObj['fileType'] = dataArr['fileType'];
        blockObj['layerName'] = dataArr['layerName'];
        if(dataArr['layerDescription'] !== ''){
            blockObj['layerDescription'] = dataArr['layerDescription'];
        }
        if(dataArr['providedBy'] !== ''){
            blockObj['providedBy'] = dataArr['providedBy'];
        }
        if(dataArr['sourceURL'] !== ''){
            blockObj['sourceURL'] = dataArr['sourceURL'];
        }
        if(dataArr['dateAquired'] !== ''){
            blockObj['dateAquired'] = dataArr['dateAquired'];
        }
        if(dataArr['dateUploaded'] !== ''){
            blockObj['dateUploaded'] = dataArr['dateUploaded'];
        }
        blockObj['opacity'] = dataArr['opacity'];
        if(dataArr['fileType'] === 'tif'){
            blockObj['colorScale'] = dataArr['colorScale'];
        }
        else{
            blockObj['fillColor'] = dataArr['fillColor'];
            blockObj['borderColor'] = dataArr['borderColor'];
            blockObj['borderWidth'] = dataArr['borderWidth'];
            blockObj['pointRadius'] = dataArr['pointRadius'];
        }
        return blockObj;
    }

    function setNewLayerConfigArr(){
        const newLayerConfigArr = [];
        const layerBlocks = document.getElementById('layerList').querySelectorAll('li');
        layerBlocks.forEach((block) => {
            const blockObj = {};
            const blockIdArr = block.id.split("-");
            const type = blockIdArr[0];
            const id = Number(blockIdArr[1]);
            const dataArr = layerData[id];
            if(type === 'layer'){
                newLayerConfigArr.push(buildNewLayerBlockObjFromData(id,dataArr));
            }
            else if(type === 'layerGroup'){
                const newLayerGroupArr = [];
                const layerGroupContainerId = 'layerGroupList-' + id;
                const layerGroupBlocks = document.getElementById(layerGroupContainerId).querySelectorAll('li');
                blockObj['id'] = id;
                blockObj['type'] = type;
                blockObj['name'] = dataArr['name'];
                layerGroupBlocks.forEach((groupBlock) => {
                    const blockObj = {};
                    const blockIdArr = groupBlock.id.split("-");
                    const type = blockIdArr[0];
                    const id = Number(blockIdArr[1]);
                    const dataArr = layerData[id];
                    newLayerGroupArr.push(buildNewLayerBlockObjFromData(id,dataArr));
                });
                blockObj['layers'] = newLayerGroupArr;
                newLayerConfigArr.push(blockObj);
            }
        });
        return newLayerConfigArr;
    }

    function saveLayerConfigChanges(){
        const newLayerConfigArr = setNewLayerConfigArr();
        if(newLayerConfigArr.length > 0){
            const newLayerConfig = {};
            newLayerConfig['layerConfig'] = newLayerConfigArr;
            const http = new XMLHttpRequest();
            const url = "rpc/mapServerConfigurationController.php";
            const jsonData = JSON.stringify(newLayerConfig).replaceAll('&','%<amp>%');
            const params = 'action=saveMapServerConfig&data='+jsonData;
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function() {
                if(http.readyState === 4 && http.status === 200) {
                    if(Number(http.responseText) !== 1){
                        document.getElementById("statusStr").innerHTML = 'Error saving changes';
                        setTimeout(function () {
                            document.getElementById("statusStr").innerHTML = '';
                        }, 5000);
                    }
                    else{
                        document.getElementById("statusStr").innerHTML = 'Configuration saved';
                        setTimeout(function () {
                            document.getElementById("statusStr").innerHTML = '';
                        }, 5000);
                    }
                }
            };
            http.send(params);
        }
    }

    function createLayerGroup(){
        const groupName = document.getElementById("addLayerGroupName").value;
        if(groupName !== ''){
            const newGroupId = Date.now();
            layerData[newGroupId] = {};
            layerData[newGroupId]['id'] = newGroupId;
            layerData[newGroupId]['type'] = 'layerGroup';
            layerData[newGroupId]['name'] = groupName;
            const layerGroupdLiId = 'layerGroup-' + newGroupId;
            const layerGroupContainerId = 'layerGroupList-' + newGroupId;
            const layerGroupLi = document.createElement('li');
            layerGroupLi.setAttribute("id", layerGroupdLiId);
            layerGroupLi.setAttribute("class", "group");
            const layerGroupHeaderDiv = document.createElement('div');
            layerGroupHeaderDiv.setAttribute("class","layer-group-header");
            const layerGroupTitleDiv = document.createElement('div');
            layerGroupTitleDiv.setAttribute("style","display:flex;gap:15px;justify-content:flex-start;align-items:center;");
            const layerGroupTitleB = document.createElement('b');
            layerGroupTitleB.innerHTML = groupName;
            layerGroupTitleDiv.appendChild(layerGroupTitleB);
            const layerGroupShowIconI = document.createElement('i');
            const layerGroupShowIconIId = 'showLayerGroupButton-' + newGroupId;
            const layerGroupShowIconIOnclickVal = "showLayerGroup('" + newGroupId + "');";
            layerGroupShowIconI.setAttribute("id",layerGroupShowIconIId);
            layerGroupShowIconI.setAttribute("style","display:none;width:15px;height:15px;cursor:pointer;");
            layerGroupShowIconI.setAttribute("title","Show layers");
            layerGroupShowIconI.setAttribute("class","fas fa-plus");
            layerGroupShowIconI.setAttribute("onclick",layerGroupShowIconIOnclickVal);
            layerGroupTitleDiv.appendChild(layerGroupShowIconI);
            const layerGroupHideIconI = document.createElement('i');
            const layerGroupHideIconIId = 'hideLayerGroupButton-' + newGroupId;
            const layerGroupHideIconIOnclickVal = "hideLayerGroup('" + newGroupId + "');";
            layerGroupHideIconI.setAttribute("id",layerGroupHideIconIId);
            layerGroupHideIconI.setAttribute("style","width:15px;height:15px;cursor:pointer;");
            layerGroupHideIconI.setAttribute("title","Hide layers");
            layerGroupHideIconI.setAttribute("class","fas fa-minus");
            layerGroupHideIconI.setAttribute("onclick",layerGroupHideIconIOnclickVal);
            layerGroupTitleDiv.appendChild(layerGroupHideIconI);
            layerGroupHeaderDiv.appendChild(layerGroupTitleDiv);
            const layerGroupEditIconDiv = document.createElement('div');
            const layerGroupEditIconI = document.createElement('i');
            const layerGroupEditIconIOnclickVal = "openLayerGroupEditWindow('" + newGroupId + "');";
            layerGroupEditIconI.setAttribute("style","width:20px;height:20px;cursor:pointer;margin-right:10px");
            layerGroupEditIconI.setAttribute("title","Edit layer group");
            layerGroupEditIconI.setAttribute("class","fas fa-edit");
            layerGroupEditIconI.setAttribute("onclick",layerGroupEditIconIOnclickVal);
            layerGroupEditIconDiv.appendChild(layerGroupEditIconI);
            layerGroupHeaderDiv.appendChild(layerGroupEditIconDiv);
            layerGroupLi.appendChild(layerGroupHeaderDiv);
            const layerGroupContainerOl = document.createElement('ol');
            layerGroupContainerOl.setAttribute("id", layerGroupContainerId);
            layerGroupContainerOl.setAttribute("class", "layer-group-container");
            layerGroupLi.appendChild(layerGroupContainerOl);
            document.getElementById("layerList").insertBefore(layerGroupLi, document.getElementById("layerList").firstChild);
            hideAddBoxes();
            clearAddForms();
            saveLayerConfigChanges();
        }
        else{
            alert("You need to enter a Group Name before adding a layer group.");
        }
    }

    function hideAddBoxes() {
        document.getElementById('addLayerDiv').style.display = "none";
        document.getElementById('addLayerGroupDiv').style.display = "none";
    }

    function clearAddForms() {
        document.getElementById('addLayerGroupName').value = '';
        document.getElementById('addLayerFile').value = '';
        document.getElementById('addLayerName').value = '';
        document.getElementById('addLayerDescription').value = '';
        document.getElementById('addLayerProvidedBy').value = '';
        document.getElementById('addLayerSourceURL').value = '';
        document.getElementById('addLayerDateAquired').value = '';
    }
</script>
</body>
</html>
