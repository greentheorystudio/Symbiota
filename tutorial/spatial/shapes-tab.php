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
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css" />
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
            <section id="shapes-tab" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Shapes Tab</div>
                        <div class="topic-nav-links">
                            <div><a href="vector-tools-panel.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Shapes Tab is accessed in the <a href="vector-tools-panel.php#/vector-tools-panel/0">Vector Tools Panel</a> in
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>. It contains download options, means for deleting,
                        and several analysis tools for features in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>. The
                        top portion of the Shapes Tab will be covered here, while the analysis tools available in the
                        lower portion of the tab will be covered individually in following topics.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Selected Area Display</h3>
                    <p>At the top of the Shapes Tab is the Selected Area Display. This display shows the total area, in
                        sq/km, of all of the features currently selected in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Download Type Selector and Button</h3>
                    <p>Below the Selected Area Display on the left side is the Download Type drop-down selector followed by the Download
                        button <button class="icon-button" title="Download"><i style="height:15px;width:15px;" class="fas fa-download"></i></button>. These
                        can be used to download all of the features currently in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> in
                        a KML or GeoJSON file format.<a href="downloading-occurrence-data.php#/downloading-occurrence-data/0">Downloading features from the Shapes Layer</a> will
                        be discussed further in the next topic.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Delete Selected Features Button</h3>
                    <p>To the right of the Download Type Selector and button is the Delete Selected Features button. This
                        can be clicked to go to delete all features that are currently selected in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="downloading-features-from-shapes-layer.php">Go To Next Topic</a></h3>
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
