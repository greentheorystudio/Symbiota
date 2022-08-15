<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Tutorial</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20220813" rel="stylesheet" id="theme" />
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
                            <li><a href="index.php#/controlling-map">Controlling the Map</a></li>
                            <li><a href="index.php#/control-panel">Control Panel</a></li>
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
                <section id="controlling-map" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <div class="slide-title">Controlling the Map</div>
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
                            the map. This will activate the draw tool. To deactivate any draw tool, simply click on the drop-down
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
                            selector is used to activate any loaded layer in order to inspect features or data within that layer,
                            select features within that layer, or edit selected features. While this selector is empty when no
                            additional layers are loaded n the map, once they are, it can be used by simply clicking on the drop-down
                            and selecting the layer you would like to activate. The use of this selector is explained more in
                            later topics in this tutorial.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out" style="width: 25%; right: 5%; top: 25%;">
                        <h3>Settings Toggle</h3>
                        <p>Located on the left side of the bottom row of the Control Panel. This toggle can be clicked to open
                            the Settings Panel. The Settings Panel will be discussed further in a later topic in this tutorial.</p>
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
                overview: false,
                disableLayout: true
            });
        </script>
    </body>
</html>
