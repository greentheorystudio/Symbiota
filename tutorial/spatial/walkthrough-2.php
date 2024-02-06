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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/tutorial.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <style>
        .reveal .controls {
            margin-bottom: 75px;
        }
    </style>
</head>
<body>
    <div class="reveal">
        <div class="slides">
            <section id="walkthrough-2" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Walkthrough #2</div>
                        <div class="topic-nav-links">
                            <div><a href="walkthrough-1.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>In this walkthrough we will use the the <a href="using-draw-tool.php#/using-draw-tool/0">Draw Tool</a>
                        and <a href="buffer-tool.php#/buffer-tool/0">Buffer Tool</a> to create a buffer polygon around a
                        point on the map, and then find all occurrence records that occur within it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 1</h3>
                    <p>Identify a location on the map (ideally a city, town, or village) in an area which you know of
                        occurrence records occurring. If you are unsure of how to do this, follow steps <a href="walkthrough-1.php#/walkthrough-1/1">1</a>,
                        <a href="walkthrough-1.php#/walkthrough-1/2">2</a>, and <a href="walkthrough-1.php#/walkthrough-1/3">3</a>,
                        of <a href="walkthrough-1.php#/walkthrough-1/0">Walkthrough #1</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 2</h3>
                    <p>With the location identified, <a href="exploring-map.php#/exploring-map/1">pan</a>
                        and <a href="exploring-map.php#/exploring-map/0">adjust the zoom level</a> of the map so that
                        the location appears as a point on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 3</h3>
                    <p>In the <a href="control-panel.php#/control-panel/1">Draw Selector</a> in
                        the <a href="control-panel.php#/control-panel/0">Control Panel</a>, select Point to activate the
                        Point Draw Tool.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 4</h3>
                    <p>Poisition the mouse cursor over the location point you identified in Step 1 and click once to create
                        a new point feature in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 5</h3>
                    <p>Click once on the new point feature you created in the previous step to select it in
                        the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 6</h3>
                    <p>Click on the <a href="main-map-window.php#/main-map-window/1">Side Panel Toggle</a> to open
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>, click on Vector Tools to
                        expand the <a href="vector-tools-panel.php#/vector-tools-panel/0">Vector Tools Panel</a>, and
                        then click on the Shapes Tab to select it (if it isn't already).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 7</h3>
                    <p>In the <a href="buffer-tool.php#/buffer-tool/0">Buffer Tool</a> in the <a href="shapes-tab.php#/shapes-tab/0">Shapes Tab</a>, enter the width
                        the buffer should be (in km) in the box and click the Buffer button to create the buffer polygon.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 8</h3>
                    <p>Click once on the new buffer polygon feature created in the previous step to select it in
                        the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 9</h3>
                    <p>Click on Search Criteria in the <a href="side-panel.php#/side-panel/0">Side Panel</a> to expand
                        the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a>, and
                        then click the Load Records button, in either the Criteria or Collections Tab (whichever is selected)
                        to load the occurrence records occurring in the buffer polygon.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 10</h3>
                    <p>If you receive a message after step 9 stating that there were no records matching the query, try
                        repeatinging steps 7, 8, and 9, but using a higher value for buffer width in step 7.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="walkthrough-3.php">Go To Next Topic</a></h3>
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
