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
            <section id="control-panel" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Control Panel</div>
                        <div class="topic-nav-links">
                            <div><a href="map-layers.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Located along the center top edge of this window. This panel has three rows of features:</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Draw Selector</h3>
                    <p>Located on the left side of the top row of the Control Panel. This selector can be used to activate the draw
                        tool for adding new polygon, box, circle, line, and point features on the map. To use this
                        selector, simply click on the drop-down and select the type of feature that you would like to draw on
                        the map. This will activate the draw tool. To deactivate the draw tool, simply click on the drop-down
                        and select None. Using the draw tool to create new map features will be discussed in greater depth in
                        a later topic in this tutorial.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Base Layer Selector</h3>
                    <p>Located on the right side of the top row of the Control Panel. This selector can be used to change the base layer of
                        the map to any of the publically available base layers that are included. These layers are provided
                        for reference purposes only and cannot be interacted with or used with any of the analysis tools. To use this
                        selector, simply click on the drop-down and select the base layer of your choice. The map will
                        immediately load the new base layer when you make a selection. It is important to note that these
                        layers have different minimum zoom threshholds and therefore some may not display properly when the
                        map is set to a high zoom level.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Active Layer Selector</h3>
                    <p>Located in the middle row of the Control Panel. When additional layers are loaded onto the map, this
                        selector is used to activate any loaded layer in order to inspect data within that layer,
                        select features within that layer, or edit selected features. While this selector is empty when no
                        additional layers are loaded n the map, once they are, it can be used by simply clicking on the drop-down
                        and selecting the layer you would like to activate. The use of this selector is explained more in
                        later topics in this tutorial.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Settings Toggle</h3>
                    <p>Located on the left side of the bottom row of the Control Panel. This toggle can be clicked to open
                        the Settings Panel. The Settings Panel will be discussed further in the next topic in this tutorial.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Layers Toggle</h3>
                    <p>Located in the left middle of the bottom row of the Control Panel. This toggle can be clicked to open
                        the Layers Panel. The Layers Panel will be discussed further in a later topic in this tutorial.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Download Map Image Button</h3>
                    <p>Located in the right middle of the bottom row of the Control Panel. This button can be clicked to download
                        a png image of the map as it currently appears, including all layers and data that are currently
                        loaded onto it and with the current symbology settings for each layer.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Tutorial Toggle</h3>
                    <p>Located on the right side of the bottom row of the Control Panel. This toggle can be clicked to open
                        the Mapping Tutorial.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="settings-panel.php">Go To Next Topic</a></h3>
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
