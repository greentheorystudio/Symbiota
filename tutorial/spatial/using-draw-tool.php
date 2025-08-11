<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Tutorial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20230530" rel="stylesheet" id="theme" />
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
            <section id="using-draw-tool" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Using the Draw Tool</div>
                        <div class="topic-nav-links">
                            <div><a href="settings-panel.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Draw Tool is activated through the <a href="control-panel.php#/control-panel/1">Draw Selector</a>
                        in the <a href="main-map-window.php#/main-map-window/2">Control Panel</a>. To activate this tool simply click on the drop-down and select the type of
                        feature that you would like to draw on the map. To deactivate the draw tool, simply click on the drop-down
                        and select None. The Draw Tool can be used to create new polygon, box, circle, line, and point features
                        on the map. Once the Draw Tool has been activated, follow these steps to create each feature type:</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Polygon</h3>
                    <p>Click once on the map to start drawing. Move the mouse cursor and click additionally to create additional
                        vertices in the polygon. Click on the original vertex to complete the feature.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Box</h3>
                    <p>Click once on the map to start drawing. Move the mouse cursor to expand the size of the box. Click
                        again to complete the feature.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Circle</h3>
                    <p>Click once on the map to start drawing. Move the mouse cursor to expand the size of the circle. Click
                        again to complete the feature.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Line</h3>
                    <p>Click once on the map to start drawing. Move the mouse cursor and click additionally to create
                        additional vertices in the line. Click twice on the last vertex to complete the feature.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Point</h3>
                    <p>Click once on the map to create features.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>All features created with the Draw Tool are automatically added to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> on the map, which
                        will be discussed further in a later topic in this tutorial.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="loading-map-files.php">Go To Next Topic</a></h3>
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
