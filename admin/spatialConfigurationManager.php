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
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/bootstrap.min.css?ver=20220225" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/ol.css?ver=20220209" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20210415" type="text/css" rel="stylesheet" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.nestedSortable.js?ver=20220624" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jscolor/jscolor.js?ver=13" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.popupoverlay.js" type="text/javascript"></script>
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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/admin.spatial.js?ver=20220623" type="text/javascript"></script>
    <script type="text/javascript">
        const maxUploadSizeMB = <?php echo $GLOBALS['MAX_UPLOAD_FILESIZE']; ?>;
        let serverayerArrObject;
        let layerArr;
        let layerData = {};

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
            $('#layergroupeditwindow').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
        });

        function openLayerGroupEditWindow(id) {
            const groupName = layerData[id]['name'];
            document.getElementById('editLayerGroupName').value = groupName;
            document.getElementById('editLayerGroupId').value = id;
            $('#layergroupeditwindow').popup('show');
        }

        function clearEditWindows() {
            document.getElementById('editLayerGroupName').value = '';
            document.getElementById('editLayerGroupId').value = '';
        }

        function deleteLayerGroup() {
            const groupId = Number(document.getElementById('editLayerGroupId').value);
            const layerGroupContainerId = 'layerGroupList-' + groupId;
            const layerGroupBlocks = document.getElementById(layerGroupContainerId).querySelectorAll('li');
            if(layerGroupBlocks.length > 0){
                alert('Please move all layers out of the layer group before deleting the group.');
            }
            else if(confirm("Are you sure you want to delete this layer group? This cannot be undone.")){
                const layerGroupElementId = 'layerGroup-' + groupId;
                document.getElementById(layerGroupElementId).remove();
                $('#layergroupeditwindow').popup('hide');
                clearEditWindows();
                saveLayerConfigChanges();
            }
        }

        function saveLayerGroupEdits() {
            const groupId = Number(document.getElementById('editLayerGroupId').value);
            const newGroupName = document.getElementById('editLayerGroupName').value;
            if(newGroupName !== ''){
                layerData[groupId]['name'] = newGroupName;
                $('#layergroupeditwindow').popup('hide');
                clearEditWindows();
                saveLayerConfigChanges();
            }
            else{
                alert('Please enter a Group Name to save.');
            }
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
                                <div><input type="text" id="addLayerSourceURL" style="width:550px;" value="" onchange="validateSourceURL();" /></div>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content: space-between;align-content: center;align-items: center;">
                                <div style="font-weight:bold;margin-right:10px;font-size:14px;">Date Aquired:</div>
                                <div style="width:550px;display:flex;justify-content:flex-start;">
                                    <input type="text" id="addLayerDateAquired" style="width:100px;" onchange="" />
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
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
