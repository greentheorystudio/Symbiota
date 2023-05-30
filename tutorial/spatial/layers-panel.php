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
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20230516" rel="stylesheet" id="theme" />
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
            <section id="layers-panel" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Layers Panel</div>
                        <div class="topic-nav-links">
                            <div><a href="exploring-map-layer-data.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Layers Panel is accessed by clicking on the <a href="control-panel.php#/control-panel/5">Layers Toggle</a>
                        in the <a href="main-map-window.php#/main-map-window/2">Control Panel</a>. This panel includes controls for any
                        layer that has been loaded onto the map as well as layers that have been preconfigured. The types
                        of controls available for any layer can vary depending on whether the layer is currently loaded on the map,
                        whether the layer has raster or vector type data, and whether if it is a preconfigured layer
                        or if it is either the <a href="map-layers.php#/map-layers/5">Shapes</a> or <a href="map-layers.php#/map-layers/6">Points</a> Layers (to be discussed further in a later topic).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Preconfigured layers may be arranged individually or in groups. If layer groups have been configured, there
                        will be an expansion bar in the Layers Panel with the layer group name, which can be clicked to view
                        all of the layers included in the group.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Layers are arranged in the Layers Panel in the following order: the <a href="map-layers.php#/map-layers/5">Shapes</a> and <a href="map-layers.php#/map-layers/6">Points</a> Layers (if loaded
                        on the map) appear at the top, any layers that you have loaded onto the map appear next, and then
                        all preconfigured layers and layer groups appear last. If no layers have been loaded on the map,
                        and no layers have been preconfigured, there will be no layers in the Layers Panel.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Each layer is depicted in a box within the Layers Panel. In the top-left of the box is the layer name,
                        which corresponds with the layer name in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> if
                        the layer is visible. Any metadata that has been configured with layer, including description, who
                        provided the layer, source link, date acquired, and date uploaded, will appear below the layer name. In
                        the bottom-left corner of the box will be an icon to indicate whether the layer contains vector or raster
                        data, with <i style="height:20px;width:20px;" class="fas fa-vector-square"></i> for vector data
                        and <i style="height:20px;width:20px;" class="fas fa-border-all"></i> for raster data.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Each layer's information and controls are enclosed within individual frames within the Layers Panel. In
                        the top-left of each frame is the layer name, which corresponds with the layer name in
                        the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a>, if the layer is visible. Any metadata
                        that has been configured with the layer, including description, who provided the layer, source link, date
                        acquired, and date uploaded, will appear below the layer name. In the bottom-left corner of each
                        frame will be an icon to indicate whether the layer contains vector or raster data,
                        with <i style="height:20px;width:20px;" class="fas fa-vector-square"></i> for vector data and <i style="height:20px;width:20px;" class="fas fa-border-all"></i> for
                        raster data.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The layer controls are found in the bottom-right of each layer frame. If the layer is currently not
                        visible, the controls may be limited to one or two, but more will be available once the layer's
                        visibility is changed. We will go through each control that is available, what layers it is available
                        for, and how to use each. We will start with the control in the far bottom-right corner of each layer
                        frame and address each control to the left:</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Visibility Toggle</h3>
                    <p><b>Available for every layer, both when visible and hidden.</b> In the far bottom-right corner of each layer frame is the Visibility Toggle checkbox,
                        which toggles whether the layer is visible on the map. Checking this checkbox will show the layer on the
                        map, and unchecking it will hide the layer. It is important to note that this checkbox simply
                        toggles the visibility of a layer and does not affect the layer data.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Delete Layer Button</h3>
                    <p><b>Available for the <a href="map-layers.php#/map-layers/5">Shapes</a> and <a href="map-layers.php#/map-layers/6">Points</a> Layers and any layers that you have loaded onto the map, both
                            when visible and hidden.</b> Clicking this button will completely remove the layer, and all of its data,
                        from the map. If this layer is the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>, this means that all features included in that layer
                        will be deleted. If this layer is the <a href="map-layers.php#/map-layers/6">Points Layer</a>, this means that all search results will be
                        removed from the map and the Records Panel. Clicing this button cannot be undone.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Query Selector Toggle Button</h3>
                    <p><b>Available for vector layers when visible.</b> Clicking this will close the Layers Panel and open
                        the Query Selector for the layer. The Query Selector allows you to select features within a given
                        layer based on their attribute values and will be discussed further in a later topic.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Symbology Toggle Button</h3>
                    <p><b>Available for all layers, except the <a href="map-layers.php#/map-layers/5">Shapes</a> and <a href="map-layers.php#/map-layers/6">Points</a> Layers, when visible.</b> Clicking this
                        button will open and close the layer's symbology settings, where you can adjust how the layer is
                        symbolized on the map. For vector layers, these settings include: border color, fill color, border
                        width, point radius (for point features), and opacity. To adjust border color and fill color, simply
                        click on the color indicator box next to each, and then select the color you would like from the
                        color picker that opens. For raster layers, you can select one of the several predefined color scales
                        for symbolizing raster data. All symbology adjustments made for any layer are immediately reflected
                        on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Order Adjuster</h3>
                    <p><b>Available for all layers, except the <a href="map-layers.php#/map-layers/5">Shapes</a> and <a href="map-layers.php#/map-layers/6">Points</a> Layers, when visible.</b> This will indicate,
                        and allow you to adjust, the order in which the layer is located within the stack of layers currently
                        visible on the map. As layers are added to the map, they are added over previously added layers and
                        have incrementally higher order numbers in the stack of layers. Layers with lower order numbers apeear
                        underneath layers of higher order numbers on the map. You can use this adjuster to either move a layer
                        higher or lower within the layer stack, and therefore make it appear above or below other layers
                        on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="using-query-selector.php">Go To Next Topic</a></h3>
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
