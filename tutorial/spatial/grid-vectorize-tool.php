<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
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
    </style>
</head>
<body>
    <div class="reveal">
        <div class="slides">
            <section id="grid-vectorize-tool" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Grid-Based Vectorize Tool</div>
                        <div class="topic-nav-links">
                            <div><a href="data-vectorize-tool.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Grid-Based Vectorize Tool is accessable in the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a> in
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>. It can be used to create vector polygon features for
                        all areas within a target box positioned on the map, that have raster data values within a defined
                        value range, based on a grid analysis performed at either a 25, 50, 100, 250, or 500 meter resolution.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Grid-Based Vectorize Tool compares to the Data-Based Vectorize Tool in that its raster analysis
                        can be more accurate and it can produce vector features that have a much greater level of resolution.
                        The Data-Based Vectorize Tool however, can be run on much larger areas and can be a significantly
                        faster to run.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Before using the Grid-Based Vectorize Tool, at least one raster layer needs to be loaded on the
                        map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>To use the Grid-Based Vectorize Tool, follow these steps:</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 1</h3>
                    <p>Using the Target Raster Layer Selector, at the top of the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>,
                        select the raster layer to be vectorized.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 2</h3>
                    <p>Enter the low and high values for the value range in the appropriate boxes in the Grid-Based
                        Vectorize Tool: <input data-role="none" type="text" style="margin-top:3px;width:50px;" /> and <input data-role="none" type="text" style="margin-top:3px;width:50px;" /></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 3</h3>
                    <p>Select the resolution of the grid analysis, or leave it at the default value of 25, in the Grid-Based
                        Vectorize Tool: resolution of <select data-role="none" style="margin-top:3px;"><option value="0.025">25</option></select> meters</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 4</h3>
                    <p>Click the Display Target Box button in the Grid-Based Vectorize Tool to display the red target box on the map: <button data-role="none">Display Target Box</button></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 5</h3>
                    <p>Click and drag the red target box on the map to position it on the area to be vectorized. Note that
                        the target box is different sizes, based on the resolution selected in Step 3, and it may be
                        necessary to zoom in on the map in order to position it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 6</h3>
                    <p>Click the Grid-Based Vectorize button in the Grid-Based Vectorize Tool: <button data-role="none">Grid-Based Vectorize</button></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 7</h3>
                    <p>Click the Hide Target Box button in the Grid-Based Vectorize Tool to remove the red target box from the map: <button data-role="none">Hide Target Box</button></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>All polygon features that are created are added to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="walkthrough-1.php">Go To Next Topic</a></h3>
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
