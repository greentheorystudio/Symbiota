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
            <section id="map-layers" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Map Layers</div>
                        <div class="topic-nav-links">
                            <div><a href="exploring-map.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The map initially loads without any additional layers included. As layers get added to the map, they
                        are added in a stacking fashion, much like sheets being stacked on a pile of paper.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Additional layers of two data types can be optionally loaded onto the map: <b>vector</b> layers can
                        be added, which include data based on discrete features (points, lines, or polygons), that can
                        have any number of data attributes associated with them; and <b>raster</b> layers can be added, which
                        include data arranged in a grid with each grid cell (or pixel) associated with a single data value.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>While there are tools for analyzing both raster and vector data, only vector data can be used as
                        search criteria for occurrence records. Raster data can be used however to create vector features
                        based on a value, or value range.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Each additional layer loaded onto the map can be interacted with individually. For most layers interaction
                        is limited to exploring the data they contain, whether it be viewing the data attributes of specific
                        features within a vector layer, or viewing the data value at a specific point in a raster layer.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>There are two additional layers that are added automatically to the map, and allow for a greater
                        amount of interaction. These two layers are relied upon for creating, editing, and using vector data
                        to search for occurrence records, and to further analyze points assocated with occurrence records
                        returned from a search. Both layers will be introduced here, but will be discussed in greater depth
                        in further topics in this tutorial:</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Shapes Layer</h3>
                    <p>The Shapes Layer is a vector layer that is automatically added to the map when new features are created
                        using the <a href="using-draw-tool.php#/using-draw-tool/0">Draw Tool</a> (to be discussed in a later topic), or vector features are selected from an
                        additional layer added to the map. Features included in the Shapes Layer can be edited, selected, deleted,
                        processed further using the Vector Tools, or used to search for occurrence records. The contents of
                        the Shapes Layer can also be downloaded in multiple geospatial formats for further use.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Points Layer</h3>
                    <p>The Points Layer is a vector layer that is automatically added to the map when occurrence records
                        are loaded from a search. The point features included in this layer can be rendered as individual
                        points, clustered points, or as a heat map. Additionally they allow for a greater degree of symbology
                        options based on the collections and taxonomy of the associated occurrence data. Point features in
                        this layer can be individually selected, and downloads can be prepared of either the entire dataset,
                        or a selected subset, in a variety of export formats.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="control-panel.php">Go To Next Topic</a></h3>
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
