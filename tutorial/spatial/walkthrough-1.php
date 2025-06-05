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
            <section id="walkthrough-1" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Walkthrough #1</div>
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
                        isn't already expanded), and then click on the Collections Tab to select it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 2</h3>
                    <p>In the Collections Tab, uncheck the Select/Deselect All checkbox to deselect all collections. Then
                        check one or two individual collections of your choice.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 3</h3>
                    <p>Click the Load Records button to run the search. If a message pops up stating that there were no
                        records matching the query, repeat step 2, but check different collections, and do this step over
                        until occurrence records are loaded.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 4</h3>
                    <p>When occurrence records are loaded on the map, the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> should
                        automatically expand with the <a href="records-tab.php#/records-tab/0">Records Tab</a> selected. Scroll
                        up and down over the records to view them. Click the <i style="height:20px;width:20px;" class="fas fa-search-location"></i> icon
                        in the Collector column of records to pan the map to that record's location on the map and add
                        a temporary marker. Check the checkbox in the far left column of the <a href="records-tab.php#/records-tab/8">Occurrence Record Table</a> for
                        a few records to select them.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 5</h3>
                    <p>Click the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a> to select it, and then check
                        the <a href="selections-tab.php#/selections-tab/4">Show Only Selected Points Checkbox</a> so that
                        only the selected records are displayed on the map. If the selected records aren't readily visible,
                        click the <a href="selections-tab.php#/selections-tab/3">Zoom to Selections button</a> to pan and
                        zoom the map to the records. Uncheck the <a href="selections-tab.php#/selections-tab/4">Show Only Selected Points Checkbox</a> so
                        that all records are visible on the map again.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 6</h3>
                    <p>Click the <a href="records-tab.php#/records-tab/0">Records Tab</a> to select it again and make note
                        of one of the scientific names in the Scientific Name column of the <a href="records-tab.php#/records-tab/8">Occurrence Record Table</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 7</h3>
                    <p>Click on Search Criteria to expand the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a>
                        and click on the Collections Tab to select it. In the Collections Tab, check the Select/Deselect All
                        checkbox to select all collections.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 8</h3>
                    <p>Click the Criteria Tab to select it, and then enter the scientific name noted in Step 6 into the
                        Taxa box, leaving the Taxa Type Selector above the Taxa box set to Family or Scientific Name.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 9</h3>
                    <p>Click the Load Records button to load all occurrence records identified to the scientific name you entered.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 10</h3>
                    <p>Click on Search Criteria to expand the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a>
                        and click on the Criteria Tab to select it. Try entering other criteria into the other options
                        in this tab and loading occurrence records.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="walkthrough-2.php">Go To Next Topic</a></h3>
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
