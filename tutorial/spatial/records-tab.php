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
                <section id="records-tab" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div class="tutorial-frame tutorial-display-toggle-slide">
                        <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                        <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                    </div>
                    <div class="topic-title-slide">
                        <div class="tutorial-frame topic-title-slide-inner">
                            <div class="slide-title">Records Tab</div>
                            <div class="topic-nav-links">
                                <div><a href="records-taxa-panel.php">Previous Topic</a></div>
                                <div><a href="index.php#/index">Index of Topics</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>The Records Tab is accessed in the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> in
                            the <a href="side-panel.php#/side-panel/0">Side Panel</a>. It contains download options for the occurrence records
                            returned in a search, links to other options to view the records, and a paginated list of the records
                            themselves, with the ability for selecting records of interest. To go through each part of this tab:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Download Type Selector and Button</h3>
                        <p>In the top-left corner of the Records Tab is the Download Type drop-down selector followed by the Download
                            button <button class="icon-button" title="Download"><i style="height:15px;width:15px;" class="fas fa-download"></i></button>. These
                            can be used to download the occurrence records in a CSV/ZIP, KML, GeoJSON, or GPX file format.
                            <a href="downloading-occurrence-data.php#/downloading-occurrence-data/0">Downloading occurrence records</a> will be discussed further in a later topic.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>List Display Button</h3>
                        <p>To the right of the Download Type Selector and button is the List Display button <button class="icon-button" title="List Display"><i style="height:15px;width:15px;" class="fas fa-list"></i></button>. This
                            can be clicked to go to the list display for the occurrence records search.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Table Display Button</h3>
                        <p>To the right of the List Display button is the Table Display button <button class="icon-button" title="Table Display"><i style="height:15px;width:15px;" class="fas fa-table"></i></button>. This
                            can be clicked to go to the table display for the occurrence records search.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Image Display Button</h3>
                        <p>To the right of the Table Display button is the Image Display button <button class="icon-button" title="Image Display"><i style="height:15px;width:15px;" class="fas fa-camera"></i></button>. This
                            can be clicked to go to the image display for the occurrence records search.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Copy Search URL Button</h3>
                        <p>When the search does not include a complex shape, to the right of the Image Display button is the Copy Search URL button <button class="icon-button" title="Copy Search URL"><i style="height:15px;width:15px;" class="fas fa-link"></i></button>. This
                            can be clicked to copy a url to your computer's clipboard that will load the same search.
                            This url can be used at any time to quickly reload the same search at a different time.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Pagination and Record Count Bar</h3>
                        <p>Below the options and display links is the Pagination and Record Count Bar. If more than 100 occurrence
                            records are returned in a search, the records will be divided into pages of 100 and this bar will
                            have links to each record page in the top row, and indicate the current page and record range
                            in the bottom row. If 100 occurrence records or less are returned in a search, this bar will
                            simply indicate the amount of records returned. This same bar is also displayed at the bottom of
                            the Records Tab.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Select/Deselect All Checkbox</h3>
                        <p>Below the Pagination and Record Count Bar is the Select/Deselect All Checkbox. This checkbox can
                            be used to select and deselect all of the occurrence records in the current table. When selected,
                            records will be both added to the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a> as well as
                            selected on the map. Deselecting records will both remove them from the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a> and delselect
                            them on the map.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Occurrence Record Table</h3>
                        <p>Below the Select/Deselect All Checkbox is the Occurrence Record Table displaying a paginated table
                            of the occurrence records returned from the search. There are columns for Catalog Number, Collector,
                            Date, and Scientific Name for each record. In the far left column there is a checkbox to select
                            and deselect individual records in the table. This will also select or deselect the record on
                            the map, and add it to, or remove it from, the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a>. The
                            text in the Collector column for each record can be clicked to open a popup window displaying
                            the complete data for that record. Additionally, the <i style="height:20px;width:20px;" class="fas fa-search-location"></i> icon in the Collector column
                            can be clicked to pan the map to that record's location on the map and add a temporary marker.
                            If the Scientific Name is included in the Taxonomic Thesaurus, the text in this column can be
                            clicked to open the Taxon Profile page for the taxon in a separate browser tab.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3><a href="collections-tab.php">Go To Next Topic</a></h3>
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
