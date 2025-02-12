<?php
include_once(__DIR__ . '/../../../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Management Module Tutorial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20230530" rel="stylesheet" id="theme" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/tutorial.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
            <section id="intro" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/taxonomycleaner.php?collid=<?php echo $collid; ?>" data-background-interactive data-preload>
                <div style="position:absolute;left: 50%; bottom:20%; width:40%;">
                    <div class="tutorial-frame" style="position:relative;left: -50%;">
                        <h2>Taxonomy Management Module Tutorial</h2>
                        <p>Welcome to the taxonomy management module tutorial! This tutorial will explain the different
                            processes that can be run within this module and how to use them to improve the accuracy
                            and consistency of scientific names within occurrence records and increase the amount of occurrence
                            records that are linked to the taxonomic thesaurus.</p>
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
            <section id="index" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/taxonomycleaner.php?collid=<?php echo $collid; ?>" data-preload>
                <div class="topic-list-container">
                    <div class="tutorial-frame" style="position:relative;left: -50%;">
                        <h2>Index of Topics</h2>
                        <ul class="topic-list">
                            <li><a href="overview.php?collid=<?php echo $collid; ?>">Overview</a></li>
                            <li><a href="maintenance-utilities-panel.php?collid=<?php echo $collid; ?>">Maintenance Utilities Panel</a></li>
                            <li><a href="search-utilities-panel.php?collid=<?php echo $collid; ?>">Search Utilities Panel</a></li>
                        </ul>
                    </div>
                </div>
            </section>
            <section data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/taxonomycleaner.php?collid=<?php echo $collid; ?>" data-background-interactive data-preload>
                <div style="position:absolute;left: 50%; bottom:20%; width:40%;">
                    <div class="tutorial-frame" style="position:relative;left: -50%;">
                        <h3><a href="overview.php?collid=<?php echo $collid; ?>">Start Tutorial</a></h3>
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
