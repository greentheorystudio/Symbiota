<?php
include_once(__DIR__ . '/../../../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Management Module Tutorial</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20230516" rel="stylesheet" id="theme" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/tutorial.js" type="text/javascript"></script>
</head>
<body>
    <div class="reveal">
        <div class="slides">
            <section id="overview" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/taxonomycleaner.php?collid=<?php echo $collid; ?>" data-background-interactive data-preload>
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
                    <p>The Taxonomy Management Module can be used to clean and normalize occurrence record scientific names,
                        update the locality security setting for occurrence records based on the portal's Protected Species list,
                        and linking occurrence records to the Taxonomic Thesaurus. It also has the capability to search the
                        Catalogue of Life, the Integrated Taxonomic Information System, and the World Register of Marine Species
                        to find and add new taxa to the Taxonomic Thesaurus, so that occurrence records for taxa not currently
                        in the thesaurus can be linked.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>In the top portion of the module, just below the collection name, there are three settings that can
                        be used for controlling some of the processes available within the module on the left side, and both
                        the total amount of occurrence records not linked to the Taxonomic Thesaurus, and the total unique
                        scientific names those occurrence records recpresent, on the right side.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Target Kingdom Selector</h3>
                    <p>The Target Kingdom Selector is located in the top portion of the module on the left side. It is
                        used to select the taxonomic Kingdom from which taxa will be searched in some of the processes
                        available in the module. This setting is necessary when running the Set Taxonomic Thesaurus Linkages,
                        Search Taxonomic Data Sources, and Taxonomic Thesaurus Fuzzy Search processes (all of which be
                        discussed further in the following topics).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Processing Start Index</h3>
                    <p>The Processing Start Index setting is located in the top portion of the module on the left side.
                        When some of the processes available within this module run, they iterate through an alphabetical
                        list of the scientific names found in occurrence records that are not linked to the Taxonomic
                        Thesaurus. This setting can be used to set the alphabetic start point from which to start the
                        iteration through the list of names, if a later start point is desired than the natural alphabetic
                        start. If nothing is entered in this setting, iteration through the list of names will begin at
                        the alphabetic first name.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Processing Batch Limit</h3>
                    <p>The Processing Batch Limit setting is located in the top portion of the module on the left side.
                        When some of the processes available within this module run, they iterate through an alphabetical
                        list of the scientific names found in occurrence records that are not linked to the Taxonomic Thesaurus.
                        This setting can be used to set the amount of names a process will iterate through, if a number
                        less than the total amount of names is desired. If no numeric value is entered in this setting,
                        processes will iterate through the entire list of scientific names.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Process Control Window</h3>
                    <p>The Process Control Window is located below the top portion of the module, on the left side. It
                        contains two expansion panels, Maintenance Utilities and Search Utilities, each of which include
                        the controls for starting and cancelling the processes available in this module. Each of these panels,
                        and the processes they include, will be discussed further in the following topics.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Process Display Window</h3>
                    <p>The Process Display Window is located below the top portion of the module, on the right side. Initially
                        it is a blank window, but it will display feedback, results, and additional options, as each process
                        included in the module is run. Any content displayed in this window while a process is running
                        will remain in the window until another process is started, at which point the window will be cleared.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="maintenance-utilities-panel.php?collid=<?php echo $collid; ?>">Go To Next Topic</a></h3>
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
