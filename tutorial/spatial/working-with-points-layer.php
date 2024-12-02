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
            <section id="working-with-points-layer" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Working With the Points Layer</div>
                        <div class="topic-nav-links">
                            <div><a href="loading-occurrence-records.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Occurrence records loaded on the map are automatically added into the Points Layer. In order to work
                        with occurrence records in the Points Layer, be sure that Points is selected in
                        the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> first.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Clusters</h3>
                    <p>When <a href="settings-panel.php#/settings-panel/1">Cluster Points</a> is activated in the <a href="settings-panel.php#/settings-panel/0">Settings Panel</a>,
                        occurrence clusters will appear as wider circles on the map with a number indicating the
                        amount of records included in that cluster. Click on any cluster to zoom further in to see the
                        individual occurrence records included.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Selecting Records</h3>
                    <p>Selecting or deselecting any occurrence record in the Points Layer can be done by simply clicking on the
                        individual occurrence record. Selecting occurrence records on the map both adds them to the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a>,
                        and selects them in the <a href="records-tab.php#/records-tab/0">Records Tab</a>, in the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> in
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>. Deselecting occurrence records on the map both removes them from the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a>, and
                        deselects them in the <a href="records-tab.php#/records-tab/0">Records Tab</a>, in the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> in
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Viewing Record Data</h3>
                    <p>Holding the alt key (option key on Mac) and clicking on any individual occurrence record will open
                        an info window displaying all of the data for that record.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="records-taxa-panel.php">Go To Next Topic</a></h3>
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
