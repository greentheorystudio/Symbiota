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
            <section id="symbology-tab" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/admin/mapping/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Symbology Tab</div>
                        <div class="topic-nav-links">
                            <div><a href="map-window-tab.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Symbology tab can be accessed by clicking on the Symbology tab. It contains several symbology
                        settings for the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php#/intro">Mapping module</a> arranged in three groups, Points Layer, Shapes Layer, and Drag
                        and Dropped Layers, which can be set and saved as the initial setting. We will go through each
                        of the settings in each of these groups here:</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Cluster Points</h3>
                    <p>When checked, occurrence points loaded onto the map are clustered, based on the cluster
                        distance setting, into clusters of like records. When not checked, all occurrence records are loaded
                        as individual points on the map, regardless of proximity.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Cluster Distance</h3>
                    <p>This setting controls the minimum distance threshold (in pixels) between points or clusters which
                        determine whether they are clustered when Cluster Points is checked.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Display Heat Map</h3>
                    <p>When checked, occurrence records loaded onto the map are displayed as a heat map, based on the Heat Map
                        Radius and Heat Map Blur settings. When not checked occurrence records are displayed on
                        the map as individual points, or clusters of like points.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Heat Map Radius</h3>
                    <p>This setting controls the radius (in pixels) of occurrence record points in the heat map display when Display
                        Heat Map is checked.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Heat Map Blur</h3>
                    <p>This setting controls the blur size (in pixels) of occurrence record points in the heat map display when Display
                        Heat Map is checked.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Border color</h3>
                    <p>This sets the border color for occurrence record points loaded on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Fill color</h3>
                    <p>This sets the initial fill color for occurrence record points loaded on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Border width</h3>
                    <p>This sets the border width, in pixels, for occurrence record points loaded on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Point radius</h3>
                    <p>This sets the point radius, in pixels, for occurrence record points loaded on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Selections Border color</h3>
                    <p>This sets the border color for selected occurrence record points.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Points Layer</h4>
                    <h3>Selections Border width</h3>
                    <p>This sets the border width, in pixels, for selected occurrence record points.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Border color</h3>
                    <p>This sets the border color for all features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Fill color</h3>
                    <p>This sets the fill color for all features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Border width</h3>
                    <p>This sets the border width, in pixels, for all features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Point radius</h3>
                    <p>This sets the point radius, in pixels, for all point features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Fill Opacity</h3>
                    <p>This sets the fill color opacity for all features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Selections Border color</h3>
                    <p>This sets the border color for all selected features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Selections Fill color</h3>
                    <p>This sets the fill color for all selected features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Selections Border width</h3>
                    <p>This sets the border width, in pixels, for all selected features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Shapes Layer</h4>
                    <h3>Selections Opacity</h3>
                    <p>This sets the fill color opacity for all selected features in the <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/map-layers.php#/map-layers/5">Shapes Layer</a> on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Drag and Dropped Layers</h4>
                    <h3>Raster color scale</h3>
                    <p>This sets the initial color scale that is used to symbolize raster layers that are added to the
                        map by dragging and dropping raster files onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Drag and Dropped Layers</h4>
                    <h3>Border color</h3>
                    <p>This sets the initial border color that is used to symbolize features in vector layers that are
                        added to the map by dragging and dropping vector files onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Drag and Dropped Layers</h4>
                    <h3>Fill color</h3>
                    <p>This sets the initial fill color that is used to symbolize features in vector layers that are
                        added to the map by dragging and dropping vector files onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Drag and Dropped Layers</h4>
                    <h3>Border width</h3>
                    <p>This sets the initial border width, in pixels, that is used to symbolize features in vector layers that are
                        added to the map by dragging and dropping vector files onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Drag and Dropped Layers</h4>
                    <h3>Point radius</h3>
                    <p>This sets the initial point radius, in pixels, that is used to symbolize point features in vector layers that are
                        added to the map by dragging and dropping vector files onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h4>Drag and Dropped Layers</h4>
                    <h3>Fill Opacity</h3>
                    <p>This sets the initial fill color opacity that is used to symbolize features in vector layers that are
                        added to the map by dragging and dropping vector files onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Click the Save Settings button to save changes to any of the settings in this tab.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Click the Set Default Settings button to reset and save all of the settings in this tab back to
                        their defaults.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="layers-tab.php">Go To Next Topic</a></h3>
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
