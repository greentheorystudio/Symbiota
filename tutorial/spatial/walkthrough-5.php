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
            <section id="walkthrough-5" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Walkthrough #5</div>
                        <div class="topic-nav-links">
                            <div><a href="walkthrough-4.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>In this walkthrough we will use <a href="https://www.census.gov/geographies/mapping-files/time-series/geo/tiger-line-file.html" target="_blank">TIGER/Line data from the United States Census Bureau</a>
                        and <a href="https://worldclim.org/data/worldclim21.html" target="_blank">raster elevation data from WorldClim</a> to
                        load US state and elevation data onto the map. Using the <a href="data-vectorize-tool.php#/data-vectorize-tool/0">Data-Based Vectorize</a>
                        and <a href="grid-vectorize-tool.php#/grid-vectorize-tool/0">Grid-Based Vectorize</a> Tools, we
                        will create vector features representing an elevation range in a selected state. We will then
                        find all occurrence records that occur within each created vector feature.</p>
                    <p><b>This walkthrough uses external state data for the United States, and so requires occurrence
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
                    <p>Download the <a href="https://worldclim.org/data/worldclim21.html" target="_blank">5 minutes Elevation raster data from WorldClim</a>.
                        You can <a href="https://biogeo.ucdavis.edu/data/worldclim/v2.1/base/wc2.1_5m_elev.zip">click this link to download it directly</a>.
                        This file will download as a zip archive. Once downloaded, unzip the archive to access the tif file it contains.
                        <b>Be sure to note the location of the unzipped tif file on your computer.</b></p>
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
                    <p>If you went through <a href="walkthrough-4.php#/walkthrough-4/0">Walkthrough #4</a> and wish to use
                        the same state identified in <a href="walkthrough-4.php#/walkthrough-4/5">step 5</a> of that walkthrough,
                        proceed to the next step. If not, identify a state on the map in which you know of occurrence records
                        occurring. If you are unsure of how to do this, identify the state in which the county you identified
                        in <a href="walkthrough-3.php#/walkthrough-3/4">step 4</a>of <a href="walkthrough-3.php#/walkthrough-3/0">Walkthrough #3</a>
                        is located.</p>
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
                    <p>Click on the <a href="main-map-window.php#/main-map-window/1">Side Panel Toggle</a> to open
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>, click on Vector Tools to
                        expand the <a href="vector-tools-panel.php#/vector-tools-panel/0">Vector Tools Panel</a>, select
                        either KML or GeoJSON in the <a href="vector-tools-panel.php#/vector-tools-panel/0">Vector Tools Panel</a>,
                        and click the Download button to download the selected state feature to a file. <b>Be sure to
                        note the location the file is downloaded on your computer ecause we will be using it later in this
                        walkthrough.</b></p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 11</h3>
                    <p>Drag and drop the file downloaded in step 2 over the map to load the elevation data onto the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 12</h3>
                    <p>Once the elevation data has been loaded, <a href="exploring-map.php#/exploring-map/1">pan</a>
                        and <a href="exploring-map.php#/exploring-map/0">adjust the zoom level</a> of the map so that
                        you can easily see the state feature you added to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>
                        in step 6, which should display over the elevation data.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 13</h3>
                    <p>While holding the alt key (option key on Mac), click on different areas within the state feature to open
                        an info popup showing the elevation value (elevation values are in meters). You can click the x
                        icon in the top-right corner of any info popup to close it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 14</h3>
                    <p>Identifiy low and high elevation values that you would like to use in the vectorization process.
                        This process will create vector features representing all areas that are within the low and high
                        elevation range that you identify.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 15</h3>
                    <p>Select Shapes in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 16</h3>
                    <p>Click once on the state feature you added to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a> in
                        step 6 to select it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 17</h3>
                    <p>Click on Raster Tools in the <a href="side-panel.php#/side-panel/0">Side Panel</a> to expand
                        the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 18</h3>
                    <p>Select the elevation data layer in the Target Raster Layer Selector in the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>
                        (this should be wc21_5m_elev if you downloaded the file specified in step 2).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 19</h3>
                    <p>In the <a href="data-vectorize-tool.php#/data-vectorize-tool/0">Data-Based Vectorize Tool</a> in
                        the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>, enter the
                        low and high elevation values you identified in step 14 into the approprite boxes.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 20</h3>
                    <p>In the <a href="data-vectorize-tool.php#/data-vectorize-tool/0">Data-Based Vectorize Tool</a>,
                        click the Data-Based Vectorize button to create vector features of all areas within the state
                        feature you selected that are within the low and high elevation values you entered.
                        <b>Ignore any browser popup warnings about the page being unresponsive, the process takes a while,
                        but will complete on its own.</b></p>
                    <p>The Data-Based Vectorize Tool can vectorize raster data quickly, but can be less accuarte in its
                        processing of larger rasters, such as this one. We will vectorize first using this tool, and then
                        try the <a href="grid-vectorize-tool.php#/grid-vectorize-tool/0">Grid-Based Vectorize Tool</a> to
                        see how the two compare.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 21</h3>
                    <p>Once the vectorization process is complete, click on Vector Tools in the <a href="side-panel.php#/side-panel/0">Side Panel</a> to expand
                        the <a href="vector-tools-panel.php#/vector-tools-panel/0">Vector Tools Panel</a>, and
                        then click the <a href="shapes-tab.php#/shapes-tab/3">Delete Selected Features button</a> in
                        the <a href="shapes-tab.php#/shapes-tab/0">Shapes Tab</a> to remove the state feature,
                        leaving only the new feature created in the vectorization process.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 22</h3>
                    <p>Select the elevation data layer in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate it (this should be wc21_5m_elev
                        if you downloaded the file specified in step 2).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 23</h3>
                    <p>While holding the alt key (option key on Mac), click on different areas within the new feature to open
                        an info popup showing the elevation value (elevation values are in meters). You can click the x
                        icon in the top-right corner of any info popup to close it. Note how well (or not) the vectorization
                        process was at vectorizing the value range you had specified.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 24</h3>
                    <p>Open the <a href="control-panel.php#/control-panel/1">Layers Panel</a> by clicking on the <a href="control-panel.php#/control-panel/5">Layers Toggle</a>
                        in the <a href="main-map-window.php#/main-map-window/2">Control Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 25</h3>
                    <p>In the <a href="control-panel.php#/control-panel/1">Layers Panel</a> in the elevation data layer
                        (this should be titled wc21_5m_elev if you downloaded the file specified in
                        step 2), click the <a href="layers-panel.php#/layers-panel/7">Delete Layer button</a> to remove
                        that layer from the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 26</h3>
                    <p>Close the <a href="control-panel.php#/control-panel/1">Layers Panel</a> by clicking close icon
                        in the top-right corner of the panel.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 27</h3>
                    <p>Select Shapes in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 28</h3>
                    <p>Click once on the new feature to select it.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 29</h3>
                    <p>Click on Search Criteria in the <a href="side-panel.php#/side-panel/0">Side Panel</a> to expand
                        the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a>, and
                        then click the Load Records button, in either the Criteria or Collections Tab (whichever is selected)
                        to load the occurrence records occurring within the new feature.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 30</h3>
                    <p>Click on Search Criteria in the <a href="side-panel.php#/side-panel/0">Side Panel</a> to expand
                        the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a> again, and
                        then click the Reset button, in either the Criteria or Collections Tab (whichever is selected)
                        to reset the map back to its default state.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 31</h3>
                    <p>Drag and drop the file downloaded in step 2 over the map to load the elevation data onto the map again.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 32</h3>
                    <p>Drag and drop the file downloaded in step 10 over the map to load the state feature onto the map again.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 33</h3>
                    <p>Click on the <a href="main-map-window.php#/main-map-window/1">Side Panel Toggle</a> to open
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>, click on Raster Tools in the <a href="side-panel.php#/side-panel/0">Side Panel</a>
                        to expand the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 34</h3>
                    <p>Select the elevation data layer in the Target Raster Layer Selector in the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>
                        (this should be wc21_5m_elev if you downloaded the file specified in step 2).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 35</h3>
                    <p>In the <a href="grid-vectorize-tool.php#/grid-vectorize-tool/0">Grid-Based Vectorize Tool</a> in
                        the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>, enter the
                        low and high elevation values you identified in step 14 into the approprite boxes and select 250 for
                        the resolution in meters.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 36</h3>
                    <p>In the <a href="grid-vectorize-tool.php#/grid-vectorize-tool/0">Grid-Based Vectorize Tool</a> in
                        the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>, click the
                        Display Target Box button to display the target polygon on the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 37</h3>
                    <p>Click and drag the target polygon on the map so that it covers as much of the state feature as
                        possible, any vectorization that happens outside of the state feature will be removed at a later
                        step.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 38</h3>
                    <p>Once the target polygon is positioned, in the <a href="grid-vectorize-tool.php#/grid-vectorize-tool/0">Grid-Based Vectorize Tool</a>,
                        click the Grid-Based Vectorize button to create vector features of all areas within the target polygon
                        that are within the low and high elevation values you entered. <b>Ignore any browser popup warnings
                        about the page being unresponsive, the process takes a while, but will complete on its own.</b></p>
                    <p>The Grid-Based Vectorize Tool is slower, depending on the resolution selected, at vectorizing,
                        but offers a much higher level of accuracy.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 39</h3>
                    <p>Once the vectorization process is complete, select the elevation data layer in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate it (this should be wc21_5m_elev
                        if you downloaded the file specified in step 2).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 40</h3>
                    <p>While holding the alt key (option key on Mac), click on different areas within the new feature to open
                        an info popup showing the elevation value (elevation values are in meters). You can click the x
                        icon in the top-right corner of any info popup to close it. Note how well (or not) the vectorization
                        process was at vectorizing the value range you had specified.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 41</h3>
                    <p>Repeat steps 37 and 38, repositioning the target polygon over different areas of the state feature
                        until the entire area of the state feature (and any additional area) has been vectorized.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 42</h3>
                    <p>Once the entire area of the state feature has been vectorized, in the <a href="grid-vectorize-tool.php#/grid-vectorize-tool/0">Grid-Based Vectorize Tool</a>
                        in the <a href="raster-tools-panel.php#/raster-tools-panel/0">Raster Tools Panel</a>, click the
                        Hide Target Box button to remove the target polygon from the map.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 43</h3>
                    <p>Select Shapes in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 44</h3>
                    <p>Click once on each the new features created from the vectorization process to select all of them.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 45</h3>
                    <p>Click on Vector Tools in the <a href="side-panel.php#/side-panel/0">Side Panel</a> to
                        expand the <a href="vector-tools-panel.php#/vector-tools-panel/0">Vector Tools Panel</a>, click
                        on the Shapes Tab to select it (if it isn't already), and then click the Union button in
                        the <a href="union-tool.php#/union-tool/0">Union Tool</a> to combine all of the selected
                        features into one.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 46</h3>
                    <p>Select state feature layer that you added to the map in step 32 in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate it. It will
                        be named the same as the filename in the Active Layer Selector.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 47</h3>
                    <p>Click once on the state feature to add it to the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 48</h3>
                    <p>Select Shapes in the <a href="control-panel.php#/control-panel/3">Active Layer Selector</a> in
                        the <a href="main-map-window.php#/main-map-window/2">Control Panel</a> to activate the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 49</h3>
                    <p>Click once on the state feature and the combined vectorization feature to select them both in
                        the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 50</h3>
                    <p>In the <a href="intersect-tool.php#/intersect-tool/0">Intersect Tool</a> in
                        the <a href="shapes-tab.php#/shapes-tab/0">Shapes Tab</a>, click the Intersect button to create
                        an intersect feature of the state and combined vectorization features.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 51</h3>
                    <p>Click the <a href="shapes-tab.php#/shapes-tab/3">Delete Selected Features button</a> in
                        the <a href="shapes-tab.php#/shapes-tab/0">Shapes Tab</a> to remove the state and combined vectorization features,
                        leaving only the new intersect features created in the previous step.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 52</h3>
                    <p>Click once on the intersect feature to select it in the <a href="map-layers.php#/map-layers/5">Shapes Layer</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Step 53</h3>
                    <p>Click on Search Criteria in the <a href="side-panel.php#/side-panel/0">Side Panel</a> to expand
                        the <a href="search-criteria-panel.php#/search-criteria-panel/0">Search Criteria Panel</a>, and
                        then click the Load Records button, in either the Criteria or Collections Tab (whichever is selected)
                        to load the occurrence records occurring within the intersect polygon.</p>
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
