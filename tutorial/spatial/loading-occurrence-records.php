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
            <section id="loading-occurrence-records" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Loading Occurrence Records</div>
                        <div class="topic-nav-links">
                            <div><a href="search-criteria-panel.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>A search for occurrence records can be run when at least one polygon, box, or circle feature
                        is <a href="working-with-shapes-layer.php#/working-with-shapes-layer/2">selected in the Shapes Layer</a>, or search
                        criteria has been entered in either the Criteria or Collections tabs in
                        the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a> in
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>, or both.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>If polygon, box, or circle features are <a href="working-with-shapes-layer.php#/working-with-shapes-layer/2">selected in the Shapes Layer</a> the
                        search will find occurrence records occurring within all selected features.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>To run an occurrence search, click the Load Records button in either the Criteria or Collections tabs
                        in the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a> of
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Occurrence records loaded onto the map are automatically added to the <a href="map-layers.php#/map-layers/6">Points Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>When occurrence records are loaded, the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> (to
                        be discussed in a later topic) becomes available in the <a href="side-panel.php#/side-panel/0">Side Panel</a> with
                        tabs showing the collections and taxa represented in the search return, and the occurrence records themselves.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Occurrence records are initially loaded symbolized based on the collection they represent and all
                        collections being symbolized with the same color. The symbology for occurrence records can be
                        changed in either the <a href="collections-tab.php#/collections-tab/0">Collections Tab</a> or <a href="#/taxa-tab/0">Taxa Tab</a> in
                        the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> in the <a href="side-panel.php#/side-panel/0">Side Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="working-with-points-layer.php">Go To Next Topic</a></h3>
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
