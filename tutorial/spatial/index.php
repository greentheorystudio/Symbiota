<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Tutorial</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20220811" rel="stylesheet" id="theme" />
        <style>
            .reveal .controls {
                margin-bottom: 75px;
            }
        </style>
    </head>
    <body>
        <div class="reveal">
            <div class="slides">
                <section id="intro" data-background-iframe="../../spatial/index.php" data-background-interactive>
                    <div style="position:absolute;left: 50%; bottom:10; width:40%;">
                        <div class="tutorial-frame" style="position:relative;left: -50%;">
                            <h2>Mapping tutorial</h2>
                            <p>Welcome to the mapping tutorial. This tutorial will explain the different components included
                                within this module, how to load and work with different types of map layers, and how to use the vector and raster
                                analysis tools. You can progress through this tutorial by using the red arrows located in the bottom-right
                                corner of this screen, using the right and left arrow buttons on your keyboard, or pressing the o button on
                                your keyboard to see an overview of all of the slides included here.</p>
                        </div>
                    </div>
                </section>
                <section id="main-map-window" data-background-iframe="../../spatial/index.php" data-background-interactive>
                    <div style="position:absolute;left: 50%; bottom:10;">
                        <div class="tutorial-frame" style="position:relative; left: -50%;">
                            <h2>Main Map Window</h2>
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
                        <p>Located along the center top edge of this window. This panel contains several controls and
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
            </div>
        </div>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/reveal.js"></script>
        <script>
            Reveal.initialize({
                controls: true,
                controlsTutorial: true,
                progress: true,
                center: true,
                hash: true,
                disableLayout: true
            });
        </script>
    </body>
</html>
