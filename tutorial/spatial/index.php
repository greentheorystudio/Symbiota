<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Tutorial</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20220813" rel="stylesheet" id="theme" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <style>
            .reveal .controls {
                margin-bottom: 75px;
            }
        </style>
    </head>
    <body>
        <div class="reveal">
            <div class="slides">
                <section id="intro" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10; width:40%;">
                        <div class="tutorial-frame" style="position:relative;left: -50%;">
                            <h2>Mapping Tutorial</h2>
                            <p>Welcome to the mapping tutorial! This tutorial will explain the different components included
                                within this module, how to load and work with different types of map layers, and how to use the
                                vector and raster analysis tools. This tutorial is meant to be interactive, so be sure to test each
                                component and tool explained as you progress through the topics. Use the red arrows located in
                                the bottom-right corner of this screen to progress forwards and backwards. The left and right arrow
                                keys on your keyboard can also be used for progression, however if anything is clicked outide of
                                the tutorial windows on any slide, the red arrows will need to be used for the next progression.</p>
                        </div>
                    </div>
                </section>
                <section id="index" data-background-iframe="../../spatial/index.php" data-preload>
                    <div class="tutorial-frame" style="left: 3%; top: 20%; width:75%; height: 70%;">
                        <h2>Index of Topics</h2>
                        <ul style="display:flex;flex-flow: column wrap;height:90%;">
                            <li><a href="index.php#/main-map-window">Main Map Window</a></li>
                            <li><a href="index.php#/exploring-map">Exploring the Map</a></li>
                            <li><a href="index.php#/map-layers">Map Layers</a></li>
                            <li><a href="index.php#/control-panel">Control Panel</a></li>
                            <li><a href="index.php#/settings-panel">Settings Panel</a></li>
                            <li><a href="index.php#/using-draw-tool">Using the Draw Tool</a></li>
                            <li><a href="index.php#/loading-map-files">Loading Map Data Files Onto the Map</a></li>
                            <li><a href="index.php#/exploring-map-layer-data">Exploring Map Layer Data</a></li>
                            <li><a href="index.php#/layers-panel">Layers Panel</a></li>
                            <li><a href="index.php#/using-query-selector">Using the Query Selector</a></li>
                        </ul>
                    </div>
                </section>
                <section id="main-map-window" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Main Map Window</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>The main map window is the primary location to view map data, access tools and controls, and load
                        new data onto the map. Let's go through the components of the map window that are available to use
                        for these purposes.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Side Panel Toggle</h3>
                        <p>Located in the top-left corner of this window. Click on this to open the Side Panel, and then you
                        can click on the x button in the top-right corner of the Side Panel to close it.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Control Panel</h3>
                        <p>Located along the center top edge of this window. This panel contains controls and
                        access to settings that will be covered in much further detail later in this tutorial.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Full Screen Toggle</h3>
                        <p>Located in the top-right corner of this window, above the Zoom Slider. Click this button to toggle
                            the full screen display of the map. Once the full screen display has been toggled, the esc key
                             can be pressed to exit back to normal display.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Zoom Slider</h3>
                        <p>Located along the top-right edge of this window, just below the Full Screen Toggle. Click on either
                            the plus or minus buttons at the top and bottom of the slider, or click and drag the handle in the middle,
                            to adjust the zoom level of the map.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Location and Distance Panel</h3>
                        <p>Located in the bottom-right corner of this window. This panel displays the coordinates of the current
                            mouse position on the map (if the mouse cursor is on the map) and scale bars displaying distance in
                            both miles and kilometers according to the current zoom level of the map.</p>
                    </div>
                </section>
                <section id="exploring-map" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Exploring the Map</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Zoom</h3>
                        <p>The zoom level of the map can be adjusted by either using the <a href="index.php#/main-map-window/4">Zoom Slider</a>,
                            or scrolling on your mouse or touchpad while hovering over the map.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Pan</h3>
                        <p>To pan the map in any direction, simply click and drag on any area of the map to move it in the
                            direction you wish.</p>
                    </div>
                </section>
                <section id="map-layers" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Map Layers</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>The map initially loads without any additional layers included. As layers get added to the map, they
                            are added in a stacking fashion, much like sheets being stacked on a pile of paper.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Additional layers of two data types can be optionally loaded onto the map: <b>vector</b> layers can
                            be added, which include data based on discrete features (points, lines, or polygons), that can
                            have any number of data attributes associated with them; and <b>raster</b> layers can be added, which
                            include data arranged in a grid with each grid cell (or pixel) associated with a single data value.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>While there are tools for analyzing both raster and vector data, only vector data can be used as
                            search criteria for occurrence records. Raster data can be used however to create vector features
                            based on a value, or value range.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Each additional layer loaded onto the map can be interacted with individually. For most layers interaction
                            is limited to exploring the data they contain, whether it be viewing the data attributes of specific
                            features within a vector layer, or viewing the data value at a specific point in a raster layer.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>There are two additional layers that are added automatically to the map, and allow for a greater
                            amount of interaction. These two layers are relied upon for creating, editing, and using vector data
                            to search for occurrence records, and to further analyze points assocated with occurrence records
                            returned from a search. Both layers will be introduced here, but will be discussed in greater depth
                            in further topics in this tutorial:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Shapes Layer</h3>
                        <p>The Shapes Layer is a vector layer that is automatically added to the map when new features are created
                            using the <a href="index.php#/using-draw-tool">Draw Tool</a> (to be discussed in a later topic), or vector features are selected from an
                            additional layer added to the map. Features included in the Shapes Layer can be edited, selected, deleted,
                            processed further using the Vector Tools, or used to search for occurrence records. The contents of
                            the Shapes Layer can also be downloaded in multiple geospatial formats for further use.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Points Layer</h3>
                        <p>The Points Layer is a vector layer that is automatically added to the map when occurrence records
                            are loaded from a search. The point features included in this layer can be rendered as individual
                            points, clustered points, or as a heat map. Additionally they allow for a greater degree of symbology
                            options based on the collections and taxonomy of the associated occurrence data. Point features in
                            this layer can be individually selected, and downloads can be prepared of either the entire dataset,
                            or a selected subset, in a variety of export formats.</p>
                    </div>
                </section>
                <section id="control-panel" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Control Panel</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Located along the center top edge of this window. This panel has three rows of features:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Draw Selector</h3>
                        <p>Located on the left side of the top row of the Control Panel. This selector can be used to activate the draw
                            tool for adding new polygon, box, circle, line, and point features on the map. To use this
                            selector, simply click on the drop-down and select the type of feature that you would like to draw on
                            the map. This will activate the draw tool. To deactivate the draw tool, simply click on the drop-down
                            and select None. Using the draw tool to create new map features will be discussed in greater depth in
                            a later topic in this tutorial.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Base Layer Selector</h3>
                        <p>Located on the right side of the top row of the Control Panel. This selector can be used to change the base layer of
                            the map to any of the publically available base layers that are included. These layers are provided
                            for reference purposes only and cannot be interacted with or used with any of the analysis tools. To use this
                            selector, simply click on the drop-down and select the base layer of your choice. The map will
                            immediately load the new base layer when you make a selection. It is important to note that these
                            layers have different minimum zoom threshholds and therefore some may not display properly when the
                            map is set to a high zoom level.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Active Layer Selector</h3>
                        <p>Located in the middle row of the Control Panel. When additional layers are loaded onto the map, this
                            selector is used to activate any loaded layer in order to inspect data within that layer,
                            select features within that layer, or edit selected features. While this selector is empty when no
                            additional layers are loaded n the map, once they are, it can be used by simply clicking on the drop-down
                            and selecting the layer you would like to activate. The use of this selector is explained more in
                            later topics in this tutorial.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Settings Toggle</h3>
                        <p>Located on the left side of the bottom row of the Control Panel. This toggle can be clicked to open
                            the Settings Panel. The Settings Panel will be discussed further in the next topic in this tutorial.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Layers Toggle</h3>
                        <p>Located in the left middle of the bottom row of the Control Panel. This toggle can be clicked to open
                            the Layers Panel. The Layers Panel will be discussed further in a later topic in this tutorial.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Download Map Image Button</h3>
                        <p>Located in the right middle of the bottom row of the Control Panel. This button can be clicked to download
                            a png image of the map as it currently appears, including all layers and data that are currently
                            loaded onto it and with the current symbology settings for each layer.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Tutorial Toggle</h3>
                        <p>Located on the right side of the bottom row of the Control Panel. This toggle can be clicked to open
                            the Mapping Tutorial.</p>
                    </div>
                </section>
                <section id="settings-panel" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Settings Panel</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>The Settings Panel can be accessed by clicking on the <a href="index.php#/control-panel/4">Settings Toggle</a>
                            in the <a href="index.php#/main-map-window/2">Control Panel</a>. This panel includes settings for how loaded occurrence data will be displayed
                            on the map. The panel can be closed by clicking the close icon in the top-right corner. The settings
                            included in the Settings Panel are:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Cluster Points</h3>
                        <p>When checked (default) occrrence points loaded onto the map are clustered, based on the cluster
                            distance setting, into clusters of like records. When not checked, all occurrence records are loaded
                            as individual points on the map, regardless of proximity.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Cluster Distance</h3>
                        <p>This setting controls the minimum distance threshold (in pixels) between points or clusters which
                            determine whether they are clustered when Cluster Points is checked.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Display Heat Map</h3>
                        <p>When checked occrrence points loaded onto the map are displayed as a heat map, based on the Heat Map
                            Radius and Heat Map Blur settings. When not checked (default) occurrence points are displayed on
                            the map as individual points, or clusters of like points.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Heat Map Radius</h3>
                        <p>This setting controls the radius (in pixels) of occurrence points in the heat map display when Display
                            Heat Map is checked.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Heat Map Blur</h3>
                        <p>This setting controls the blur size (in pixels) of points in the heat map display when Display
                            Heat Map is checked.</p>
                    </div>
                </section>
                <section id="using-draw-tool" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Using the Draw Tool</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>The Draw Tool is activated through the <a href="index.php#/control-panel/1">Draw Selector</a>
                            in the <a href="index.php#/main-map-window/2">Control Panel</a>. To activate this tool simply click on the drop-down and select the type of
                            feature that you would like to draw on the map. To deactivate the draw tool, simply click on the drop-down
                            and select None. The Draw Tool can be used to create new polygon, box, circle, line, and point features
                            on the map. Once the Draw Tool has been activated, follow these steps to create each feature type:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Polygon</h3>
                        <p>Click once on the map to start drawing. Move the mouse cursor and click additionally to create additional
                            vertices in the polygon. Click on the original vertex to complete the feature.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Box</h3>
                        <p>Click once on the map to start drawing. Move the mouse cursor to expand the size of the box. Click
                            again to complete the feature.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Circle</h3>
                        <p>Click once on the map to start drawing. Move the mouse cursor to expand the size of the circle. Click
                            again to complete the feature.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Line</h3>
                        <p>Click once on the map to start drawing. Move the mouse cursor and click additionally to create
                            additional vertices in the line. Click twice on the last vertex to complete the feature.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Point</h3>
                        <p>Click once on the map to create features.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>All features created with the Draw Tool are automatically added to the <a href="index.php#/map-layers/5">Shapes Layer</a> on the map, which
                            will be discussed further in a later topic in this tutorial.</p>
                    </div>
                </section>
                <section id="loading-map-files" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Loading Map Data Files Onto the Map</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Map data files can be added to the map from the following file formats: KML (.kml), GeoJSON (.geojson),
                            Shapefile (.zip), and GeoTIFF (.tif or .tiff).</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>All map data loaded onto the map must be in either WGS84 or NAD83 projections.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>To load a map data file onto the map, simply drag and drop the file anywhere over the map window.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Each layer file added to the map is added to both the <a href="index.php#/control-panel/3">Active Layer Selector</a> and
                            <a href="index.php#/control-panel/1">Layers Panel</a> under the filename of the original map data file.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>No more than 3 vector files (KML, GeoJSON, or Shapefile) and 3 raster files (GeoTIFF) can be loaded
                            onto the map at any one time.</p>
                    </div>
                </section>
                <section id="exploring-map-layer-data" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Exploring Map Layer Data</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>In order to explore the data or select features from any layer loaded onto the map, the layer must
                            first be activated through the <a href="index.php#/control-panel/3">Active Layer Selector</a> in
                            the <a href="index.php#/main-map-window/2">Control Panel</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>When a vector layer is activated on the map, holding the alt key (option key on Mac) and left-clicking
                            on any feature in that layer will open an info window displaying all of the metadata of that feature.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>When a raster layer is activated on the map, holding the alt key (option key on Mac) and left-clicking
                            anywhere that layer will open an info window displaying the raster value for the point clicked.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>When a vector layer is activated on the map, left-clicking on any feature in that layer will add
                            it to the <a href="index.php#/map-layers/5">Shapes Layer</a> where it can be used for further processing or searching, which will be
                            discussed further in a later topic in this tutorial.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>When there are no layers activated on the map, and the <a href="index.php#/control-panel/3">Active Layer Selector</a> is
                            set to None, hovering over any feature or raster data on the map will open an
                            info window displaying the source layer for that feature or data.</p>
                    </div>
                </section>
                <section id="layers-panel" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Layers Panel</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>The Layers Panel can be accessed by clicking on the <a href="index.php#/control-panel/5">Layers Toggle</a>
                            in the <a href="index.php#/main-map-window/2">Control Panel</a>. This panel includes controls for any
                            layer that has been loaded onto the map as well as layers that have been preconfigured. The types
                            of controls available for any layer can vary depending on whether the layer is currently loaded on the map,
                            whether the layer has raster or vector type data, and whether if it is a preconfigured layer
                            or if it is either the <a href="index.php#/map-layers/5">Shapes</a> or <a href="index.php#/map-layers/6">Points</a> Layers (to be discussed further in a later topic).</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Preconfigured layers may be arranged individually or in groups. If layer groups have been configured, there
                            will be an expansion bar in the Layers Panel with the layer group name, which can be clicked to view
                            all of the layers included in the group.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Layers are arranged in the Layers Panel in the following order: the <a href="index.php#/map-layers/5">Shapes</a> and <a href="index.php#/map-layers/6">Points</a> Layers (if loaded
                            on the map) appear at the top, any layers that you have loaded onto the map appear next, and then
                            all preconfigured layers and layer groups appear last. If no layers have been loaded on the map,
                            and no layers have been preconfigured, there will be no layers in the Layers Panel.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Each layer is depicted in a box within the Layers Panel. In the top-left of the box is the layer name,
                            which corresponds with the layer name in the <a href="index.php#/control-panel/3">Active Layer Selector</a> if
                            the layer is visible. Any metadata that has been configured with layer, including description, who
                            provided the layer, source link, date acquired, and date uploaded, will appear below the layer name. In
                            the bottom-left corner of the box will be an icon to indicate whether the layer contains vector or raster
                            data, with <i style="height:20px;width:20px;" class="fas fa-vector-square"></i> for vector data
                            and <i style="height:20px;width:20px;" class="fas fa-border-all"></i> for raster data.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Each layer's information and controls are enclosed within individual frames within the Layers Panel. In
                            the top-left of each frame is the layer name, which corresponds with the layer name in
                            the <a href="index.php#/control-panel/3">Active Layer Selector</a>, if the layer is visible. Any metadata
                            that has been configured with the layer, including description, who provided the layer, source link, date
                            acquired, and date uploaded, will appear below the layer name. In the bottom-left corner of each
                            frame will be an icon to indicate whether the layer contains vector or raster data,
                            with <i style="height:20px;width:20px;" class="fas fa-vector-square"></i> for vector data and <i style="height:20px;width:20px;" class="fas fa-border-all"></i> for
                            raster data.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>The layer controls are found in the bottom-right of each layer frame. If the layer is currently not
                            visible, the controls may be limited to one or two, but more will be available once the layer's
                            visibility is changed. We will go through each control that is available, what layers it is available
                            for, and how to use each. We will start with the control in the far bottom-right corner of each layer
                            frame and address each control to the left:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Visibility Toggle</h3>
                        <p><b>Available for every layer, both when visible and hidden.</b> In the far bottom-right corner of each layer frame is the Visibility Toggle checkbox,
                            which toggles whether the layer is visible on the map. Checking this checkbox will show the layer on the
                            map, and unchecking it will hide the layer. It is important to note that this checkbox simply
                            toggles the visibility of a layer and does not affect the layer data.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Delete Layer Button</h3>
                        <p><b>Available for the <a href="index.php#/map-layers/5">Shapes</a> and <a href="index.php#/map-layers/6">Points</a> Layers and any layers that you have loaded onto the map, both
                            when visible and hidden.</b> Clicking this button will completely remove the layer, and all of its data,
                            from the map. If this layer is the <a href="index.php#/map-layers/5">Shapes Layer</a>, this means that all features included in that layer
                            will be deleted. If this layer is the <a href="index.php#/map-layers/6">Points Layer</a>, this means that all search results will be
                            removed from the map and the Records Panel. Clicing this button cannot be undone.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Query Selector Toggle Button</h3>
                        <p><b>Available for vector layers when visible.</b> Clicking this will close the Layers Panel and open
                            the Query Selector for the layer. The Query Selector allows you to select features within a given
                            layer based on their attribute values and will be discussed further in a later topic.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Symbology Toggle Button</h3>
                        <p><b>Available for all layers, except the <a href="index.php#/map-layers/5">Shapes</a> and <a href="index.php#/map-layers/6">Points</a> Layers, when visible.</b> Clicking this
                            button will open and close the layer's symbology settings, where you can adjust how the layer is
                            symbolized on the map. For vector layers, these settings include: border color, fill color, border
                            width, point radius (for point features), and opacity. To adjust border color and fill color, simply
                            click on the color indicator box next to each, and then select the color you would like from the
                            color picker that opens. For raster layers, you can select one of the several predefined color scales
                            for symbolizing raster data. All symbology adjustments made for any layer are immediately reflected
                            on the map.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Order Adjuster</h3>
                        <p><b>Available for all layers, except the <a href="index.php#/map-layers/5">Shapes</a> and <a href="index.php#/map-layers/6">Points</a> Layers, when visible.</b> This will indicate,
                            and allow you to adjust, the order in which the layer is located within the stack of layers currently
                            visible on the map. As layers are added to the map, they are added over previously added layers and
                            have incrementally higher order numbers in the stack of layers. Layers with lower order numbers apeear
                            underneath layers of higher order numbers on the map. You can use this adjuster to either move a layer
                            higher or lower within the layer stack, and therefore make it appear above or below other layers
                            on the map.</p>
                    </div>
                </section>
                <section id="using-query-selector" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Using the Query Selector</div>
                            <div class="index-link"><a href="index.php#/index">Back to index</a></div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>The Query Selector for any layer is accessed by clicking on the <a href="index.php#/layers-panel/8">Query Selector Toggle Button</a>
                            in the layer's frame in the <a href="index.php#/layers-panel">Layers Panel</a>. This tool allows you to select features within a given
                            layer based on their attribute values. Once the Query Selector has been opened for a layer, here are the steps for use:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>The Select attribute drop-down contains all of the attributes applied to features within that layer. Select
                            the attribute you would like to use to base your selection query.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>In the next drop-down select the comparator you would like to use in your query, out of EQUALS, CONTAINS,
                            GREATER THAN, LESS THAN, and BETWEEN. Both the EQUALS and CONTAINS comparators can compare text or
                            numeric values, but the remaining comparators can only compare numeric values.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>If the BETWEEN comparator is selected, enter the low and high values in the approprite boxes for the
                            comparison query. If any other comparator is selected, enter the single value in the remaining box for the
                            comparison query.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <p>Finally click the Run Query button to actually run the selection query. All features within the target
                            layer that match the criteria you have specified will automatically be added to
                            the <a href="index.php#/map-layers/5">Shapes Layer</a>.</p>
                    </div>
                </section>
            </div>
        </div>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/reveal.js"></script>
        <script>
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
