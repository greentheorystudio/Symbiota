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
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20220829" rel="stylesheet" id="theme" />
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
                <section id="selections-tab" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div class="tutorial-frame tutorial-display-toggle-slide">
                        <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                        <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                    </div>
                    <div class="topic-title-slide">
                        <div class="tutorial-frame topic-title-slide-inner">
                            <div class="slide-title">Selections Tab</div>
                            <div class="topic-nav-links">
                                <div><a href="taxa-tab.php">Previous Topic</a></div>
                                <div><a href="index.php#/index">Index of Topics</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>The Selections Tab is available whenever occurrence records are selected. It is accessed in
                            the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> in the <a href="side-panel.php#/side-panel/0">Side Panel</a>. It
                            contains download options for the selected occurrence records, a list of the records themselves, the ability
                            to clear the current record selections, and the ability adjust the map to show the selections.
                            To go through each part of this tab:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Download Type Selector and Button</h3>
                        <p>In the top-left corner of the Selections Tab is the Download Type drop-down selector followed by the Download
                            button <button class="icon-button" title="Download"><i style="height:15px;width:15px;" class="fas fa-download"></i></button>. These
                            can be used to download the selected occurrence records in a CSV/ZIP, KML, GeoJSON, or GPX file format.
                            <a href="downloading-occurrence-data.php#/downloading-occurrence-data/0">Downloading selected occurrence records</a> will be discussed further in a later topic.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Clear Selections Button</h3>
                        <p>Below the Download Type Selector and button is the Clear Selections button <button class="icon-button" title="List Display"><i style="height:15px;width:15px;" class="fas fa-list"></i></button>. This
                            can be clicked to deselect all of the currently selected occurrence records, removing all records from
                            this tab and the tab itself, deselecting all occurrence records on the map, and deselecting all
                            records in the <a href="records-tab.php#/records-tab/0">Records Tab</a>.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Zoom to Selections Button</h3>
                        <p>To the right of the Clear Selections button is the Zoom to Selections button. This can be clicked
                            to automatically zoom and pan the map so that all of the selected records are visible on the map.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Show Only Selected Points Checkbox</h3>
                        <p>This checkbox can be used to limit the occurrence records displayed on the map to only those selected. When checked,
                            only the selected occurrence records will be displayed on the map.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Selected Occurrence Record Table</h3>
                        <p>Below the Show Only Selected Points Checkbox is the Selected Occurrence Record Table displaying a table
                            of all of the currently selected occurrence records. There are columns for Catalog Number, Collector,
                            Date, and Scientific Name for each record. In the far left column there is a checkbox to deselect
                            individual records in the table, which removes them from this table, deselects them on the map, and
                            deselects them in the <a href="records-tab.php#/records-tab/0">Records Tab</a>. The text in the Collector column
                            for each record can be clicked to open a popup window displaying the complete data for that record.
                            Additionally, the <i style="height:20px;width:20px;" class="fas fa-search-location"></i> icon in the Collector column
                            can be clicked to pan the map to that record's location on the map and add a temporary marker.
                            If the Scientific Name is included in the Taxonomic Thesaurus, the text in this column can be
                            clicked to open the Taxon Profile page for the taxon in a separate browser tab.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3><a href="downloading-occurrence-data.php">Go To Next Topic</a></h3>
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
