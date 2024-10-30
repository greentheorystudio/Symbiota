<?php
include_once(__DIR__ . '/../../../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Management Module Tutorial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reset.css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/reveal.css?ver=20220813" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/tutorial-theme.css?ver=20230530" rel="stylesheet" id="theme" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/tutorial.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
    <div class="reveal">
        <div class="slides">
            <section id="symbology-tab" data-background-iframe="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/management/taxonomycleaner.php?collid=<?php echo $collid; ?>" data-background-interactive data-preload>
                <div class="tutorial-frame tutorial-display-toggle-slide">
                    <div id="hideToggle" class="index-link tutorial-display-toggle"><a style="cursor:pointer;" onclick="hideTutorial();">Hide Tutorial</a></div>
                    <div id="showToggle" class="index-link tutorial-display-toggle" style="display:none;"><a style="cursor:pointer;" onclick="showTutorial();">Show Tutorial</a></div>
                </div>
                <div class="topic-title-slide">
                    <div class="tutorial-frame topic-title-slide-inner">
                        <div class="slide-title">Search Utilities Panel</div>
                        <div class="topic-nav-links">
                            <div><a href="maintenance-utilities-panel.php?collid=<?php echo $collid; ?>">Previous Topic</a></div>
                            <div><a href="index.php?collid=<?php echo $collid; ?>#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Search Utilities Panel is accessed by clicking on Search Utilities in
                        the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/5">Process Control Window</a> to
                        expand the panel (if it isn't already expanded).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>There are two processes that can be run from this panel. Both will be discussed in the following
                        slides.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Search Taxonomic Data Sources</h3>
                    <p>The Search Taxonomic Data Sources process iterates through all of the scientific names of occurrence
                        records that are not linked to the taxonomic thesaurus, and searches for scientific names matching from within
                        a selected Target Kingdom in the Catalogue of Life, the Integrated Taxonomic Information System,
                        or the World Register of Marine Species. If a match is found, the matching taxon, and all parent
                        and accepted taxa, that are not currently in the Taxonomic Thesaurus, are added to it. Then all occurrence
                        records for that taxon are linked to the Taxonomic Thesaurus.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Search Taxonomic Data Sources</h3>
                    <h4>Instructions</h4>
                    <p>To run this process, select a Target Kingdom from the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/2">Target Kingdom Selector</a>. Then select the Taxonomic Data Source to be searched during the process.
                        As this process iterates through the list of scientific names, the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/3">Processing Start Index</a>
                        and the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/4">Processing Batch Limit</a> can
                        be set to adjust what scientific name the process starts with, and/or how many scientific names are processed before it completes.
                        To run this process click on the Start button. Once the process has been started, the Cancel
                        button can be clicked to stop it before it has completed.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Search Taxonomic Data Sources</h3>
                    <h4>Results</h4>
                    <p>As this process iterates through the list of scientific names, the current name being processed
                        will be displayed in the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/6">Process Display Window</a>.
                        If a match is found in the selected Taxonomic Data Source, notifications will follow for every
                        parent or accepted taxon that is added to the Taxonomic Thesaurus, followed by a notification
                        when the matched taxon is added to the taxonomic thesaurus. A final notification will follow indicating
                        how many occurrence records were linked to the newly added taxon in the Taxonomic Thesaurus.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Taxonomic Thesaurus Fuzzy Search</h3>
                    <p>The Taxonomic Thesaurus Fuzzy Search process attempts to match the scientific names of occurrence
                        records that are not currently linked to the taxonomic thesaurus, to those included within the
                        selected Target Kingdom in the Taxonomic Thesaurus that have a difference in spelling within the
                        entered character difference tolerance. For each match that is found, the option to update occurrence
                        records containing the original name with the selected matched name and create linkages for the
                        matched name within those records is provided. This tool is intended to find correctly spelled
                        scientific names for occurrence records that have incorrectly spelled names. All occurrence records
                        that are edited during this process will have their original scientific names saved in the verbatim
                        scientific name field before editing takes place.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Taxonomic Thesaurus Fuzzy Search</h3>
                    <h4>Instructions</h4>
                    <p>It is necessary to select a Target Kingdom from the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/2">Target Kingdom Selector</a>
                        before running this process. Then, if a different value than the default is desired, enter a numeric value for the Character difference tolerance.
                        As this process iterates through the list of scientific names, the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/3">Processing Start Index</a>
                        and the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/4">Processing Batch Limit</a> can
                        be set to adjust what scientific name the process starts with, and/or how many scientific names are processed before it completes.
                        To run this process click on the Start button. Once the process has been started, the Cancel
                        button can be clicked to stop it before it has completed.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide" style="width:28%;">
                    <h3>Taxonomic Thesaurus Fuzzy Search</h3>
                    <h4>Results</h4>
                    <p>As this process iterates through the list of scientific names, the current name being processed
                        will be displayed in the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/6">Process Display Window</a>.
                        If matches are found, each match will be listed in rows following, with a Select button next to
                        each. All of the matches will be followed by a Skip Taxon button. Click on the Select button next
                        to any match to update the occurrence records with that taxon. A notification will follow indicating
                        how many occurrence records were updated. If none of the matches are correct, click on the Skip
                        Taxon button to move to the next name in the list and continue the processing. Once the process has
                        completed, through either using the Cancel button or letting it run to completion, an Undo button
                        will activate under each selected matched taxon that can
                        be clicked to undo the edits related that specific taxon, and revert the occurrence records back
                        to their original scientific name.</p>
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
