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
                <section id="collections-tab" data-background-iframe="../../spatial/index.php" data-background-interactive data-preload>
                    <div class="tutorial-frame tutorial-display-toggle-slide">
                        <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                        <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                    </div>
                    <div class="topic-title-slide">
                        <div class="tutorial-frame topic-title-slide-inner">
                            <div class="slide-title">Collections Tab</div>
                            <div class="topic-nav-links">
                                <div><a href="records-tab.php">Previous Topic</a></div>
                                <div><a href="index.php#/index">Index of Topics</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>The Collections Tab is accessed in the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> in
                            the <a href="side-panel.php#/side-panel/0">Side Panel</a>. It contains a list of the collections represented in
                            the occurrence records returned in the search and options for symbolizing the associated occurrence
                            points on the map according to the collection it represents. To go through each part of this tab:</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Occurrence Symbol Key</h3>
                        <p>In the top-left corner of the Collections Tab is the Occurrence Symbol Key, which simply shows the
                            symbols used to distinguish individual records representing specimens, from those representing
                            observations.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Reset Symbology Button</h3>
                        <p>In the top-right corner of the Collections Tab is the Reset Symbology Button. Clicking this button
                            will reset the symbology for all of the occurrence records on the map back to the original, default
                            state of being symbolized based on the collection they represent and all collections being symbolized
                            with the same color.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Auto Color Button</h3>
                        <p>Below the Reset Symbology Button is the Auto Color Button. Clicking this button
                            will set the symbology for all of the occurrence records on the map to be symbolized based on
                            the collection they represent, if they are not already, and will assign each collection a random,
                            unique color for its symbology.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Save Symbology Button</h3>
                        <p>Below the Auto Color Button is the Save Symbology Button. Clicking this button will generate a png
                            image file of the collection list in this tab and all of the symbology colors assigned to each
                            collection.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3>Collection List</h3>
                        <p>Below the symbol key and symbology buttons is the Collection List. This list includes all of the
                            collections represented in the occurrence records returned in the search. If the occurrence records
                            on the map are being symbolized based on the collection they represent, the colored box to the
                            left of each collection in this list indicates the color that is being used to represent that
                            collection's records on the map. Each box can be clicked to open a color picker to select
                            a different color to represent that collection. Changing the color for any collection immediately sets
                            the symbology for all of the occurrence records on the map to be symbolized based on
                            the collection they represent, if they are not already, and updates the symbology to include the
                            newly selected color.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3><a href="taxa-tab.php">Go To Next Topic</a></h3>
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
