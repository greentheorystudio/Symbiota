<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Tutorial</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20230530" rel="stylesheet" id="theme" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/tutorial.js" type="text/javascript"></script>
    <style>
        .reveal .controls {
            margin-bottom: 75px;
        }
        .topic-list-container {
            position: absolute;
            left: 50%;
            bottom:15%;
            min-width: 725px;
            min-height: 625px;
            width:55%;
            height: 70%;
        }
    </style>
</head>
<body>
    <div class="reveal">
        <div class="slides">
            <section id="intro" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div style="position:absolute;left: 50%; bottom:20%; width:40%;">
                    <div class="tutorial-frame" style="position:relative;left: -50%;">
                        <h2>Mapping Tutorial</h2>
                        <p>Welcome to the mapping tutorial! This tutorial will explain the different components included
                            within this module, how to load and work with different types of map layers, and how to use the
                            vector and raster analysis tools. This tutorial is meant to be interactive, so be sure to test each
                            component and tool explained as you progress through the topics.</p>
                        <p>Use the red arrows located in
                            the bottom-right corner of this screen to progress forwards and backwards. The left and right arrow
                            keys on your keyboard can also be used for progression, however if anything is clicked outside
                            the tutorial windows on any slide, the red arrows will need to be used for the next progression.</p>
                        <p>On any topic slide there will be a Hide Tutorial link in the bottom-left corner of the screen,
                            which can be clicked to hide the tutorial content. Once clicked, a Show Tutorial link in the
                            same location can be clicked to show the tutorial content again.</p>
                    </div>
                </div>
            </section>
            <section id="index" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-preload>
                <div class="topic-list-container">
                    <div class="tutorial-frame" style="position:relative;left: -50%;">
                        <h2>Index of Topics</h2>
                        <ul class="topic-list">
                            <li><a href="main-map-window.php">Main Map Window</a></li>
                            <li><a href="exploring-map.php">Exploring the Map</a></li>
                            <li><a href="map-layers.php">Map Layers</a></li>
                            <li><a href="control-panel.php">Control Panel</a></li>
                            <li><a href="settings-panel.php">Settings Panel</a></li>
                            <li><a href="using-draw-tool.php">Using the Draw Tool</a></li>
                            <li><a href="loading-map-files.php">Loading Map Data Files Onto the Map</a></li>
                            <li><a href="exploring-map-layer-data.php">Exploring Map Layer Data</a></li>
                            <li><a href="layers-panel.php">Layers Panel</a></li>
                            <li><a href="using-query-selector.php">Using the Query Selector</a></li>
                            <li><a href="working-with-shapes-layer.php">Working With the Shapes Layer</a></li>
                            <li><a href="side-panel.php">Side Panel</a></li>
                            <li><a href="search-criteria-panel.php">Search Criteria Panel</a></li>
                            <li><a href="loading-occurrence-records.php">Loading Occurrence Records</a></li>
                            <li><a href="working-with-points-layer.php">Working With the Points Layer</a></li>
                            <li><a href="records-taxa-panel.php">Records and Taxa Panel</a></li>
                            <li><a href="records-tab.php">Records Tab</a></li>
                            <li><a href="collections-tab.php">Collections Tab</a></li>
                            <li><a href="taxa-tab.php">Taxa Tab</a></li>
                            <li><a href="selections-tab.php">Selections Tab</a></li>
                            <li><a href="using-heat-map-display.php">Using the Heat Map Display</a></li>
                            <li><a href="downloading-map-image.php">Downloading a Map Image</a></li>
                            <li><a href="downloading-occurrence-data.php">Downloading Occurrence Data</a></li>
                            <li><a href="vector-tools-panel.php">Vector Tools Panel</a></li>
                            <li><a href="shapes-tab.php">Shapes Tab</a></li>
                            <li><a href="downloading-features-from-shapes-layer.php">Downloading Features From the Shapes Layer</a></li>
                            <li><a href="buffer-tool.php">Buffer Tool</a></li>
                            <li><a href="difference-tool.php">Difference Tool</a></li>
                            <li><a href="intersect-tool.php">Intersect Tool</a></li>
                            <li><a href="union-tool.php">Union Tool</a></li>
                            <li><a href="points-tab.php">Points Tab</a></li>
                            <li><a href="concave-hull-polygon-tool.php">Concave Hull Polygon Tool</a></li>
                            <li><a href="convex-hull-polygon-tool.php">Convex Hull Polygon Tool</a></li>
                            <li><a href="raster-tools-panel.php">Raster Tools Panel</a></li>
                            <li><a href="data-vectorize-tool.php">Data-Based Vectorize Tool</a></li>
                            <li><a href="grid-vectorize-tool.php">Grid-Based Vectorize Tool</a></li>
                            <li><a href="walkthrough-1.php">Walkthrough #1</a></li>
                            <li><a href="walkthrough-2.php">Walkthrough #2</a></li>
                            <li><a href="walkthrough-3.php">Walkthrough #3</a></li>
                            <li><a href="walkthrough-4.php">Walkthrough #4</a></li>
                            <li><a href="walkthrough-5.php">Walkthrough #5</a></li>
                        </ul>
                    </div>
                </div>
            </section>
            <section data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div style="position:absolute;left: 50%; bottom:20%; width:40%;">
                    <div class="tutorial-frame" style="position:relative;left: -50%;">
                        <h3><a href="main-map-window.php">Start Tutorial</a></h3>
                    </div>
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
