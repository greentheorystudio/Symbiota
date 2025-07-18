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
            <section id="walkthrough-3" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Walkthrough #3</div>
                        <div class="topic-nav-links">
                            <div><a href="walkthrough-2.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>In this walkthrough we will use <a href="https://www.census.gov/geographies/mapping-files/time-series/geo/tiger-line-file.html" target="_blank">TIGER/Line data from the United States Census Bureau</a>
                        to load US county data onto the map. We will then find all occurrence records that occur within
                        a selected county.</p>
                    <p><b>This walkthrough uses external county data for the United States, and so requires occurrence
                            data to exist occurring within the United States as well. Variations of this walkthrough can
                            be can be completed using equivalent data for other countries.</b></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 1</h3>
                    <p>Download the US county shapefile from the <a href="https://www.census.gov/geographies/mapping-files/time-series/geo/tiger-line-file.html" target="_blank">TIGER/Line data from the United States Census Bureau</a>.
                        This can be found in the COUNTY directory of their <a href="https://www2.census.gov/geo/tiger/TIGER2021/" target="_blank">FTP Archive</a>.
                        You can also <a href="https://www2.census.gov/geo/tiger/TIGER2021/COUNTY/tl_2021_us_county.zip">click this link to download it directly</a>.
                        <b>Be sure to note the location the file is downloaded on your computer.</b></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 2</h3>
                    <p>Drag and drop the entire shapefile zip file downloaded in step 1 over the map to load the US county data onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 3</h3>
                    <p>Once the US county data has been loaded, <a href="exploring-map.php#/exploring-map/1">pan</a>
                        and <a href="exploring-map.php#/exploring-map/0">adjust the zoom level</a> of the map to view the
                        county data. Hold the alt key (option key on Mac) and click on different county features to open
                        an info popup showing all of the data for that county. You can click the x icon in the top-right
                        corner of any info popup to close it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 4</h3>
                    <p>Identify a county on the map in which you know of occurrence records occurring. If you are unsure
                        of how to do this, identify the county in which the location you identified in <a href="walkthrough-2.php#/walkthrough-2/1">step 1</a>
                        of <a href="walkthrough-2.php#/walkthrough-2/0">Walkthrough #2</a> is located.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 5</h3>
                    <p>Click once on the county feature you identified in the previous step to add it to
                        the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>. The newly added feature in
                        the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> should display over the original feature
                        on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 6</h3>
                    <p>Select Shapes in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 7</h3>
                    <p>Click once on the county feature you added to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> in
                        step 5 to select it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 8</h3>
                    <p>Click on the <a href="main-map-window.php#/main-map-window/1">Side Panel Toggle</a> to open
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>, click on Search Criteria to
                        expand the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a> (if it
                        isn't already expanded), and then click the Load Records button, in either the Criteria or Collections Tab (whichever is selected)
                        to load the occurrence records occurring in the selected county. If you receive a message stating
                        that there were no records matching the query, or if you wish to try search for occurrence records
                        in a different county, proceed with the next steps.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 9</h3>
                    <p>Click once on the county feature again to deselect it in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 10</h3>
                    <p>Select the US county data layer in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a>
                        (this should be tl_2021_us_county if you downloaded the file specified in step 1) in the <a href="main-map-window.php#/main-map-window/2">Control Panel</a>
                        to activate it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 11</h3>
                    <p>Repeat steps 5, 6, 7, and 8, but clicking on a different county feature in step 5.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="walkthrough-4.php">Go To Next Topic</a></h3>
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
