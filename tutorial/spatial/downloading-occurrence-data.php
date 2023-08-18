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
            <section id="downloading-occurrence-data" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Downloading Occurrence Data</div>
                        <div class="topic-nav-links">
                            <div><a href="downloading-map-image.php">Previous Topic</a></div>
                            <div><a href="index.php#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>Occurrence data can be downloaded from both the <a href="records-tab.php#/records-tab/0">Records Tab</a> and
                        the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a> in the <a href="records-taxa-panel.php#/records-taxa-panel/0">Records and Taxa Panel</a> in
                        the <a href="side-panel.php#/side-panel/0">Side Panel</a>. Downloading occurrence data from the <a href="records-tab.php#/records-tab/0">Records Tab</a> will
                        download all of the records returned in a search. Downloading occurrence data from the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a> will
                        download only the selected records from a search.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>In either the <a href="records-tab.php#/records-tab/0">Records Tab</a> or the <a href="selections-tab.php#/selections-tab/0">Selections Tab</a>,
                        the Download Type Selector can be used to select the download format for the occurrence data.
                        The options include: CSV/ZIP, KML, GeoJSON, and GPX. Once a download type is selected, click
                        the Download button <button class="icon-button" title="Download"><i style="height:15px;width:15px;" class="fas fa-download"></i></button> to
                        initiate the download.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>If the download type CSV/ZIP is selected, an additional window will open allowing for further
                        options as to the structure and formatting of the download. These options include whether the
                        structure the data in the Native or Darwin Core format, whether to include determination or
                        image extensions, whether the file be an archive, and other file formatting. Once these further
                        options have been set, or if the default settings are preferred, click the Download Data button
                        to proceed with the download.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="vector-tools-panel.php">Go To Next Topic</a></h3>
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
