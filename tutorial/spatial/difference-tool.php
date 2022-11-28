<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Tutorial</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" type="text/css" rel="stylesheet" />
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
                <section id="difference-tool" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                    <div class="tutorial-frame tutorial-display-toggle-slide">
                        <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                        <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                    </div>
                    <div class="topic-title-slide">
                        <div class="tutorial-frame topic-title-slide-inner">
                            <div class="slide-title">Difference Tool</div>
                            <div class="topic-nav-links">
                                <div><a href="buffer-tool.php">Previous Topic</a></div>
                                <div><a href="index.php#/index">Index of Topics</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>The Difference Tool is accessable in the <a href="shapes-tab.php#/shapes-tab/0">Shapes Tab</a> in
                            the <a href="vector-tools-panel.php#/vector-tools-panel/0">Vector Tools Panel</a> in the <a href="side-panel.php#/side-panel/0">Side Panel</a>. It
                            can be used to create a polygon with the area representing the difference of one selected
                            polygon, box, or circle from another selected polygon, box, or circle in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>Before using the Difference Tool, there must already be the two features in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> on
                            which to base the difference polygon. Features can be <a href="using-draw-tool.php#/using-draw-tool/0">created using the Draw Tool</a>,
                            or <a href="exploring-map-layer-data.php#/exploring-map-layer-data/3">added from other vector layers loaded onto the map</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>To use the Difference Tool, follow these steps:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 1</h3>
                        <p>Make sure that Shapes is selected in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                            the <a href="main-map-window.php#/main-map-window/2">Control Panel</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 2</h3>
                        <p>Select the feature from which the area of the second selected feature will be subtracted in
                            the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 3</h3>
                        <p>Select the second feature to subtract from the first feature in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 4</h3>
                        <p>Click the Difference button in the Difference Tool: <button data-role="none">Difference</button></p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>The created difference feature is added to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3><a href="intersect-tool.php">Go To Next Topic</a></h3>
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
