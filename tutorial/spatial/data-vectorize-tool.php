<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Tutorial</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20220908" rel="stylesheet" id="theme" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/tutorial.js" type="text/javascript"></script>
        <style>
            .reveal .controls {
                margin-bottom: 75px;
            }
        </style>
    </head>
    <body>
        <div class="reveal">
            <div class="slides">
                <section id="data-vectorize-tool" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                    <div class="tutorial-frame tutorial-display-toggle-slide">
                        <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                        <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                    </div>
                    <div class="topic-title-slide">
                        <div class="tutorial-frame topic-title-slide-inner">
                            <div class="slide-title">Data-Based Vectorize Tool</div>
                            <div class="topic-nav-links">
                                <div><a href="raster-tools-panel.php">Previous Topic</a></div>
                                <div><a href="index.php#/index">Index of Topics</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>The Data-Based Vectorize Tool is accessable in the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a> in
                            the <a href="side-panel.php#/side-panel/0">Side Panel</a>. It can be used to create vector polygon features for
                            all areas within a selected polygon, box, or circle in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>, that
                            have raster data values within a defined value range, based on the data values within the raster layer itself.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>The Data-Based Vectorize Tool compares to the Grid-Based Vectorize Tool in that it can analyze larger
                            areas, and is faster. The Grid-Based Vectorize Tool however, can have a much greater level of
                            resolution in the vector features it produces, and is more accurate.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>Before using the Data-Based Vectorize Tool, at least one raster layer needs to be loaded on the
                            map and there must already be at least one polygon, box, or circle feature in
                            the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> to serve as the bounds for the vectorization
                            process.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>To use the Data-Based Vectorize Tool, follow these steps:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 1</h3>
                        <p>Make sure that Shapes is selected in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                            the <a href="main-map-window.php#/main-map-window/2">Control Panel</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 2</h3>
                        <p>Select the one polygon, box, or circle feature to serve as the bounds for the vectorization process
                            in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>. *Note that this feature must overlap with the
                            raster layer to be vectorized.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 3</h3>
                        <p>Using the Target Raster Layer Selector, at the top of the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>,
                            select the raster layer to be vectorized.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 4</h3>
                        <p>Enter the low and high values for the value range in the appropriate boxes in the Data-Based
                            Vectorize Tool: <input data-role="none" type="text" style="margin-top:3px;width:50px;" /> and <input data-role="none" type="text" style="margin-top:3px;width:50px;" /></p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 5</h3>
                        <p>Click the Data-Based Vectorize button in the Data-Based Vectorize Tool: <button data-role="none">Data-Based Vectorize</button></p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>All polygon features that are created are added to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3><a href="grid-vectorize-tool.php">Go To Next Topic</a></h3>
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
