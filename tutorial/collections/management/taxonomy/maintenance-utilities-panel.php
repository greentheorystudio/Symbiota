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
                        <div class="slide-title">Maintenance Utilities Panel</div>
                        <div class="topic-nav-links">
                            <div><a href="overview.php?collid=<?php echo $collid; ?>">Previous Topic</a></div>
                            <div><a href="index.php?collid=<?php echo $collid; ?>#/index">Index of Topics</a></div>
                        </div>
                    </div>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>The Maintenance Utilities Panel is accessed by clicking on Maintenance Utilities in
                        the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/5">Process Control Window</a> to
                        expand the panel (if it isn't already expanded).</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <p>There are four processes that can be run from this panel. Each will be discussed in the following
                        slides.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>General Cleaning</h3>
                    <p>The General Cleaning process performs multiple cleaning actions on the scientific names of occurrence
                        records that are not linked to the taxonomic thesaurus, including: removing question marks, removing
                        unecessary endings (such as sp. or spp.), removing identification qualifiers (such as cf. or aff.)
                        and moving them to the identification qualifier field, normalizing infraspecific rank abbreviations
                        into universally accepted values, and removing double, leading, and trailing spaces. Any occurrence
                        record that is edited during these actions, outside of removing spaces, will have the original
                        scientific name saved in the verbatim scientific name field before any editing takes place.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>General Cleaning</h3>
                    <h4>Instructions</h4>
                    <p>As this process does not iterate through a list of scientific names, there is no need to set
                        the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/3">Processing Start Index</a>
                        or <a href="overview.php?collid=<?php echo $collid; ?>#/overview/4">Processing Batch Limit</a> before
                        running it. To run this process simply click on the Start button. Once the process has been started,
                        the Cancel button can be clicked to stop it before it has completed.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>General Cleaning</h3>
                    <h4>Results</h4>
                    <p>An indication of each cleaning action as it is being run, and the amount of occurrence records that
                        were edited from that action, will be displayed in the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/6">Process Display Window</a>.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Scientific Name Authorship Cleaning</h3>
                    <p>The Scientific Name Authorship Cleaning process finds and removes the taxonomic author names from
                        the scientific names of occurrence records that are not linked to the taxonomic thesaurus. All occurrence
                        records that are edited during this process will have their original scientific names saved in
                        the verbatim scientific name field before editing takes place.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Scientific Name Authorship Cleaning</h3>
                    <h4>Instructions</h4>
                    <p>As this process iterates through the list of scientific names, the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/3">Processing Start Index</a>
                        and the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/4">Processing Batch Limit</a> can
                        be set to adjust what scientific name the process starts with, and/or how many scientific names are processed before it completes.
                        To run this process click on the Start button. Once the process has been started, the Cancel
                        button can be clicked to stop it before it has completed.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Scientific Name Authorship Cleaning</h3>
                    <h4>Results</h4>
                    <p>As this process iterates through the list of unique scientific names, the current name being processed
                        will be displayed in the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/6">Process Display Window</a>.
                        If a taxonomic author name is detected within the scietific name, the parsed author, and the cleaned
                        scieitific name without the author are displayed below the current name. Occurrence records containing
                        the original scientific name will then be updated with the cleaned scientific name and the amount of
                        occurrence records updated will be displayed below the cleaned scietifc name. Once the process has
                        completed, through either using the Cancel button or letting it run to completion, an Undo button
                        will activate under each name edited that can be clicked to undo the edits related
                        that specific name, and revert the occurrence records back to their original scientific name.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Set Taxonomic Thesaurus Linkages</h3>
                    <p>The Set Taxonomic Thesaurus Linkages process attempts to match the scientific names of occurrence
                        records that are not currently linked to the taxonomic thesaurus, to those included within the
                        selected Target Kingdom in the Taxonomic Thesaurus. If a match is found, a linkage is established
                        in the associated occurrence records with the Taxonomic Thesaurus. This process can also be set to
                        include the scientific names of occurrence record determinations as well.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Set Taxonomic Thesaurus Linkages</h3>
                    <h4>Instructions</h4>
                    <p>It is necessary to select a Target Kingdom from the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/2">Target Kingdom Selector</a>
                        before running this process. Optionally, the checkbox can be checked to include determination records
                        in the processing. As this process does not iterate through a list of scientific names, there is no need to
                        set the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/3">Processing Start Index</a>
                        or <a href="overview.php?collid=<?php echo $collid; ?>#/overview/4">Processing Batch Limit</a> before
                        running it. To run the process click on the Start button. Once the process has been started, the
                        Cancel button can be clicked to stop it before it has completed.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Set Taxonomic Thesaurus Linkages</h3>
                    <h4>Results</h4>
                    <p>An indication the process has started will be displayed in the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/6">Process Display Window</a>
                        followed by the amount of occurrence records that had linakges established as a result. If determinations
                        were included, a similar indicator and result will be displayed next, for that process.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Update Locality Security Settings</h3>
                    <p>The Update Locality Security Settings process updates the locality security settings, to obscure locality
                        information, for all occurrence records that contain scientific names matching those included the
                        the portal's Protected Species list.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Update Locality Security Settings</h3>
                    <h4>Instructions</h4>
                    <p>As this process does not iterate through a list of scientific names, there is no need to set
                        the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/3">Processing Start Index</a>
                        or <a href="overview.php?collid=<?php echo $collid; ?>#/overview/4">Processing Batch Limit</a> before
                        running it. To run this process click on the Start button. Once the process has been started,
                        the Cancel button can be clicked to stop it before it has completed.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3>Update Locality Security Settings</h3>
                    <h4>Results</h4>
                    <p>An indication the process has started will be displayed in the <a href="overview.php?collid=<?php echo $collid; ?>#/overview/6">Process Display Window</a>
                        followed by the amount of occurrence records that had locality security settings edited as a result.</p>
                </div>
                <div class="tutorial-frame fragment fade-in-then-out topic-content-slide">
                    <h3><a href="search-utilities-panel.php?collid=<?php echo $collid; ?>">Go To Next Topic</a></h3>
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
