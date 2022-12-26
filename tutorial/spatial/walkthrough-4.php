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
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20220908" rel="stylesheet" id="theme" />
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
            <section id="walkthrough-4" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Walkthrough #4</div>
                        <div class="topic-nav-links">
                            <div><a href="walkthrough-3.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>In this walkthrough we will use <a href="https://www.census.gov/geographies/mapping-files/time-series/geo/tiger-line-file.html" target="_blank">TIGER/Line data from the United States Census Bureau</a>
                        and <a href="https://www.sciencebase.gov/catalog/item/54244abde4b037b608f9e23d" target="_blank">ecoregion data from the USGS</a> to
                        load US state and ecoregion data onto the map. Using the <a href="intersect-tool.php#/intersect-tool/0">Intersect Tool</a> we will create a feature
                        representing a selected ecoregion in a selected state. We will then find all occurrence records that occur within
                        the created feature.</p>
                    <p><b>This walkthrough uses external state and ecoregion data for the United States, and so requires occurrence
                            data to exist occurring within the United States as well. Variations of this walkthrough can
                            be can be completed using equivalent data for other countries.</b></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 1</h3>
                    <p>Download the US state shapefile from the <a href="https://www.census.gov/geographies/mapping-files/time-series/geo/tiger-line-file.html" target="_blank">TIGER/Line data from the United States Census Bureau</a>.
                        This can be found in the STATE directory of their <a href="https://www2.census.gov/geo/tiger/TIGER2021/" target="_blank">FTP Archive</a>.
                        You can also <a href="https://www2.census.gov/geo/tiger/TIGER2021/STATE/tl_2021_us_state.zip">click this link to download it directly</a>.
                        <b>Be sure to note the location the file is downloaded on your computer.</b></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 2</h3>
                    <p>Download the <a href="https://www.sciencebase.gov/catalog/item/54244abde4b037b608f9e23d" target="_blank">Bailey's Ecoregions of the Conterminous United States shapefile from the USGS</a>.
                        You can <a href="https://www.sciencebase.gov/catalog/file/get/54244abde4b037b608f9e23d?facet=Baileys_ecoregions_sgca">click this link to download it directly</a>.
                        <b>Be sure to note the location the file is downloaded on your computer.</b></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 3</h3>
                    <p>Drag and drop the entire shapefile zip file downloaded in step 1 over the map to load the US state data onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 4</h3>
                    <p>Once the US state data has been loaded, <a href="exploring-map.php#/exploring-map/1">pan</a>
                        and <a href="exploring-map.php#/exploring-map/0">adjust the zoom level</a> of the map to view the
                        state data better.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 5</h3>
                    <p>Identify a state on the map in which you know of occurrence records occurring. If you are unsure
                        of how to do this, identify the state in which the county you identified in <a href="walkthrough-3.php#/walkthrough-3/4">step 4</a>
                        of <a href="walkthrough-3.php#/walkthrough-3/0">Walkthrough #3</a> is located.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 6</h3>
                    <p>Click once on the state feature you identified in the previous step to add it to
                        the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>. The newly added feature in
                        the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> should display over the original feature
                        on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 7</h3>
                    <p>Open the <a href="control-panel.php#/control-panel/1">Layers Panel</a> by clicking on the <a href="control-panel.php#/control-panel/5">Layers Toggle</a>
                        in the <a href="main-map-window.php#/main-map-window/2">Control Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 8</h3>
                    <p>In the <a href="control-panel.php#/control-panel/1">Layers Panel</a> in the US state data layer
                        added in step 3 (this should be titled tl_2021_us_state if you downloaded the file specified in
                        step 1), click the <a href="layers-panel.php#/layers-panel/7">Delete Layer button</a> to remove
                        that layer from the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 9</h3>
                    <p>Close the <a href="control-panel.php#/control-panel/1">Layers Panel</a> by clicking close icon
                        in the top-right corner of the panel.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 10</h3>
                    <p>Drag and drop the entire shapefile zip file downloaded in step 2 over the map to load the Bailey's Ecoregion data onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 11</h3>
                    <p>Identify an ecoregion on the map that overlaps the state you identified in step 5.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 12</h3>
                    <p>Click once on the ecoregion feature you identified in the previous step to add it to
                        the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 13</h3>
                    <p>Open the <a href="control-panel.php#/control-panel/1">Layers Panel</a> by clicking on the <a href="control-panel.php#/control-panel/5">Layers Toggle</a>
                        in the <a href="main-map-window.php#/main-map-window/2">Control Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 14</h3>
                    <p>In the <a href="control-panel.php#/control-panel/1">Layers Panel</a> in the Bailey's Ecoregion data layer
                        added in step 10 (this should be titled Baileys_ecoregions_sgca if you downloaded the file specified in
                        step 2), click the <a href="layers-panel.php#/layers-panel/7">Delete Layer button</a> to remove
                        that layer from the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 15</h3>
                    <p>Close the <a href="control-panel.php#/control-panel/1">Layers Panel</a> by clicking close icon
                        in the top-right corner of the panel.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 16</h3>
                    <p>Select Shapes in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 17</h3>
                    <p>Click once on the state and ecoregion features you added to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> in
                        steps 6 and 12 to select them.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 18</h3>
                    <p>Click on the <a href="main-map-window.php#/main-map-window/1">Side Panel Toggle</a> to open
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>, click on Vector Tools to
                        expand the <a href="vector-tools-panel.php#/vector-tools-panel/0">Vector Tools Panel</a>, and
                        then click on the Shapes Tab to select it (if it isn't already).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 19</h3>
                    <p>In the <a href="intersect-tool.php#/intersect-tool/0">Intersect Tool</a> in
                        the <a href="shapes-tab.php#/shapes-tab/0">Shapes Tab</a>, click the Intersect button to create
                        an intersect feature of the two selected features.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 20</h3>
                    <p>Click the <a href="shapes-tab.php#/shapes-tab/3">Delete Selected Features button</a> in
                        the <a href="shapes-tab.php#/shapes-tab/0">Shapes Tab</a> to remove the state and ecoregion features,
                        leaving only the new intersect features created in the previous step.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 21</h3>
                    <p>Click once on the intersect feature to select it in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 22</h3>
                    <p>Click on Search Criteria in the <a href="side-panel.php#/side-panel/0">Side Panel</a> to expand
                        the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a>, and
                        then click the Load Records button, in either the Criteria or Collections Tab (whichever is selected)
                        to load the occurrence records occurring within the intersect polygon.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="walkthrough-5.php">Go To Next Topic</a></h3>
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
