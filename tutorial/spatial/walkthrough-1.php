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
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20220823" rel="stylesheet" id="theme" />
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
                <section id="grid-vectorize-tool" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div class="tutorial-frame tutorial-display-toggle-slide">
                        <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                        <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                    </div>
                    <div class="topic-title-slide">
                        <div class="tutorial-frame topic-title-slide-inner">
                            <div class="slide-title">Walkthrough #1</div>
                            <h3>Basic Occurrence Search</h3>
                            <div class="topic-nav-links">
                                <div><a href="grid-vectorize-tool.php">Previous Topic</a></div>
                                <div><a href="index.php#/index">Index of Topics</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>In this walkthrough we will use the the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a> in
                            the <a href="side-panel.php#/side-panel/0">Side Panel</a> to enter search criteria for occurrence records
                            and <a href="loading-occurrence-records.php#/loading-occurrence-records/0"> load them on the map</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 1</h3>
                        <p>Click on the <a href="main-map-window.php#/main-map-window/1">Side Panel Toggle</a> to open
                            the <a href="side-panel.php#/side-panel/0">Side Panel</a>, click on Search Criteria to
                            expand the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a> (if it
                            isn't already expanded), and then click on the Collections Tab.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 2</h3>
                        <p>In the Collections Tab, uncheck the Select/Deselect All checkbox to deselect all collections. Then
                            check one or two individual collections of your choice.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 3</h3>
                        <p>Click the Load Records button to run the search. If a message pops up stating that there were no
                            records matching the query, check different collections, and do this step over until occurrence records are loaded.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Step 4</h3>
                        <p>Click the Display Target Box Button in the Grid-Based Vectorize Tool to display the red target box on the map: <button data-role="none">Display Target Box</button></p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3><a href="working-with-points-layer.php">Go To Next Topic</a></h3>
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
