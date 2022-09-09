<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
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
        <style>
            .topic-list-container {
                position: absolute;
                left: 50%;
                bottom:20%;
                min-width: 550px;
                min-height: 500px;
                width:20%;
                height: 30%;
            }
        </style>
    </head>
    <body>
        <div class="reveal">
            <div class="slides">
                <section id="intro" data-background-iframe="../../../admin/mappingConfigurationManager.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:20%; width:40%;">
                        <div class="tutorial-frame" style="position:relative;left: -50%;">
                            <h2>Mapping Configurations Tutorial</h2>
                            <p>Welcome to the mapping configurations manager tutorial! This tutorial will explain the different
                                settings that can be configured within this module. It will also explain how to upload and
                                configure map data layers, and configure map layer groups in the Layers Tab.</p>
                            <p>Use the red arrows located in
                                the bottom-right corner of this screen to progress forwards and backwards. The left and right arrow
                                keys on your keyboard can also be used for progression, however if anything is clicked outside
                                the tutorial windows on any slide, the red arrows will need to be used for the next progression.</p>
                            <p>On any topic slide there will be a Hide Tutorial link in the bottom-left corner of the screen,
                                which can be clicked to hide the tutorial content. Once clicked, a Show Tutorial link in the
                                same location can be clicked to show the tutorial content again.</p>
                        </div>
                    </div>
                </section>
                <section id="index" data-background-iframe="../../../admin/mappingConfigurationManager.php" data-preload>
                    <div class="topic-list-container">
                        <div class="tutorial-frame" style="position:relative;left: -50%;">
                            <h2>Index of Topics</h2>
                            <ul class="topic-list">
                                <li><a href="overview.php">Overview</a></li>
                                <li><a href="map-window-tab.php">Map Window Tab</a></li>
                                <li><a href="symbology-tab.php">Symbology Tab</a></li>
                                <li><a href="layers-tab.php">Layers Tab</a></li>
                            </ul>
                        </div>
                    </div>
                </section>
                <section data-background-iframe="../../../admin/mappingConfigurationManager.php" data-background-interactive data-preload>
                    <div style="position:absolute;left: 50%; bottom:20%; width:40%;">
                        <div class="tutorial-frame" style="position:relative;left: -50%;">
                            <h3><a href="overview.php">Start Tutorial</a></h3>
                        </div>
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
