<?php
include_once(__DIR__ . '/../../../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Configuration Manager Tutorial</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20220908" rel="stylesheet" id="theme" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/tutorial.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="reveal">
            <div class="slides">
                <section id="overview" data-background-iframe="../../../../collections/management/taxonomycleaner.php?collid=<?php echo $collid; ?>" data-background-interactive data-preload>
                    <div class="tutorial-frame tutorial-display-toggle-slide">
                        <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                        <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                    </div>
                    <div class="topic-title-slide">
                        <div class="tutorial-frame topic-title-slide-inner">
                            <div class="slide-title">Overview</div>
                            <div class="topic-nav-links">
                                <div><a href="index.php?collid=<?php echo $collid; ?>">Previous Topic</a></div>
                                <div><a href="index.php?collid=<?php echo $collid; ?>#/index">Index of Topics</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <p>The Mapping Configurations Manager can be used to make global configuration settings for the various
                            map windows throughout the portal. The module contains three tabs, <a href="map-window-tab.php#/map-window-tab/0">Map Window</a>,
                            <a href="symbology-tab.php#/symbology-tab/0">Symbology</a>, and <a href="layers-tab.php#/layers-tab/0">Layers</a>,
                            which contain configuration controls. Each of these tabs will be discussed further in the following
                            topics.</p>
                    </div>
                    <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                        <h3><a href="map-window-tab.php">Go To Next Topic</a></h3>
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
