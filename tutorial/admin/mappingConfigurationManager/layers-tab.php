<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Configuration Manager Tutorial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20230530" rel="stylesheet" id="theme" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/tutorial.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
    <div class="reveal">
        <div class="slides">
            <section id="layers-tab" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/admin/mapping.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Layers Tab</div>
                        <div class="topic-nav-links">
                            <div><a href="symbology-tab.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Layers tab can be accessed by clicking on the Layers tab. In the Layers tab you can upload
                        map data layers and create layer groups, which will be available in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/layers-panel.php#/layers-panel/0">Layers Panel</a>
                        of the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php#/intro">Mapping module</a>. You can also edit metadata
                        for, set initial symbology, arrange the order of display in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/layers-panel.php#/layers-panel/0">Layers Panel</a>
                        of, and delete layers and layer groups.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Adding Layers</h3>
                    <p>Click the Add Layer button to open the Add Layer box, where you can choose the layer data file to be
                        used and enter a layer name, description, provided by, source URL, and date acquired for the layer.
                        This information will be displayed with the layer in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/layers-panel.php#/layers-panel/0">Layers Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Adding Layers</h3>
                    <p><b>Layer data files must be of the following file formats: KML (.kml), GeoJSON (.geojson),
                            Shapefile (.zip), and GeoTIFF (.tif or .tiff).</b></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Adding Layers</h3>
                    <p><b>All layer data must be in either WGS84 or NAD83 projections.</b></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Adding Layers</h3>
                    <p>Once the layer data file has been chosen and other desired information entered, click the Add
                        button to upload the data file and save the new layer. New layers will appear in the box in the
                        lower portion of the Layers tab.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Adding Layers</h3>
                    <p>Click the Cancel button to close the Add Layer box.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Adding Layer Groups</h3>
                    <p>Click the Add Layer Group button to open the Add Layer Group box, where you can enter a name for
                        the layer group. The name entered will be used as the label for the layer group in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/layers-panel.php#/layers-panel/0">Layers Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Adding Layer Groups</h3>
                    <p>Once the name has been entered, click the Add button to save the new layer group. New layer groups
                        will appear in the box in the lower portion of the Layers tab.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Adding Layer Groups</h3>
                    <p>Click the Cancel button to close the Add Layer Group box.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Arranging Layers and Layer Groups</h3>
                    <p>All layers and layer groups appear in the box in the lower portion of the Layers tab where they
                        can be arranged and organized.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Arranging Layers and Layer Groups</h3>
                    <p>All layer groups have a grey header bar, whcih includes the group name, expand/minimize control,
                        and edit icon. Click and drag the header bar to move the layer group upwards or downwards in
                        the list of other layers and layer groups.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Arranging Layers and Layer Groups</h3>
                    <p>Layer groups that are currently expanded will have a - icon just to the right of the layer name
                        in the header bar. Click this con to minimize the layer group and hide all of the layers that it contains.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Arranging Layers and Layer Groups</h3>
                    <p>Layer groups that are currently minimized will have a + icon just to the right of the layer name
                        in the header bar. Click this con to expand the layer group and show all of the layers that it contains.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Arranging Layers and Layer Groups</h3>
                    <p>All layers are displayed within a grey outline. Click and drag within the outline of any layer
                        to move it upwards or downwards in the list of other layers and layer groups, or within the list
                        of layers in any layer group. Additionally, layers can be dragged into, or out of, any layer group
                        to either include it in, or remove it from, that group.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Arranging Layers and Layer Groups</h3>
                    <p>When dragging any layer or layer group, a grey placeholder box will appear within the list to show
                        its placement once the mouse click is released.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Arranging Layers and Layer Groups</h3>
                    <p>Once any changes have been made to the arrangement or organization of layers or leyer groups, click
                        the Save Settings button in the top-right corner of the Layer tab to save the changes.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Editing Layer Groups</h3>
                    <p>Click the edit icon <i style="height:20px;width:20px;" class="fas fa-edit"></i> in the grey header
                        bar of any layer group to open the edit panel.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Editing Layer Groups</h3>
                    <p>Within the layer group edit panel: You can edit the group name. Click the Save button to save any
                        changes to the group name. Click the Delete Layer Group button to delete the layer group (note
                        layer groups containing layers cannot be deleted until all layers have been removed). Click the
                        Cancel button to close the edit panel.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Editing Layers</h3>
                    <p>Click the edit icon <i style="height:20px;width:20px;" class="fas fa-edit"></i> in the top-right
                        corner of any layer to open the edit panel.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Editing Layers</h3>
                    <p>Within the layer edit panel: You can edit the layer name, description, provided by, source URL,
                        and date acquired information. Additionally, you can also edit the intial symbology for the layer,
                        including border color, fill color, border width, point radius, and fill opacity for vector layers,
                        and color scale for raster layers. Click the Save button to save any changes to the layer information
                        or initial symbology. Click the Update Layer File button to open the update box (explained further
                        in the next slide). Click the Delete Layer button to delete the layer, which also deletes the layer
                        data file from the server. Click the Cancel button to close the edit panel.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Updating Layers</h3>
                    <p>Layer data files can be updated to reflect changes in data. Click the Update Layer File button
                        in the layer edit panel to open the update box. In the box that opens, choose the update file
                        you would like to use to upadte the layer. Once the update file has been chosen, click the Update
                        button to complete the process. Update files completely replace the previously existing data file
                        for the layer, while maintaining all information and symbology settings for the layer.</p>
                </div>
            </section>
        </div>
    </div>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/reveal.js"></script>
    <script type="text/javascript">
        Reveal.initialize({
            controls: true,
            controlsTutorial: true,
            controlsBackArrows: 'visible',
            progress: true,
            center: true,
            hash: true,
            history: true,
            overview: false,
            disableLayout: true
        });
    </script>
</body>
</html>
