<?php
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=' .$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
    <head>
        <title><?php echo $DEFAULT_TITLE; ?> About This Portal</title>
        <link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
        <link href="css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
        <script type="text/javascript">
            <?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
        </script>
    </head>
    <body>
        <?php include(__DIR__ . '/header.php'); ?>

        <main id="innertext">
            <h1><?php echo $DEFAULT_TITLE; ?></h1>
            <p>At present >11 million natural history museum specimens, used for research, teaching, and outreach, are housed in the collections of the UW-Madison departments of anthropology, botany, entomology, geoscience, and zoology.</p>
            <p>This portal is a new shared research tool made publically available to constituents across the campus, throughout the state, and beyond. It takes advantage of more than two centuries worth of collective knowledge and professional expertise across different divisions and departments at UW-Madison.</p>

            <div class="quote">
                <p>Efforts should be made at once to begin the formation of a cabinet of natural history. — University of Wisconsin’s Board of Regents, 1848</p>
            </div>

            <h2>Background</h2>
            <p>At their first meeting in 1848, the newly established University of Wisconsin’s Board of Regents proclaimed that “efforts should be made at once to begin the formation of a cabinet of natural history.” Without haste they commissioned H.A. Tenney to oversee the collection of geological, mineralogical, zoological, & botanical samples from around Wisconsin for the Museum Cabinet. The young state’s most famous naturalist, Increase Lapham of Milwaukee, donated a modest collection of 1,500 plant specimens to establish the cabinet’s herbarium; others followed. These seminal collections, intended to encourage interest in the natural resources of Wisconsin, grew quickly and would ultimately serve as the cradle of origin for most of the physical and natural sciences that have made UW-Madison famous.</p>
            <p>When North Hall’s construction was completed in 1851 the collections were housed there, but quickly overfilled that space and were moved to South Hall four years later. Upon completion of Bascom Hall in 1859, the collections were moved once again, and had grown so large that by 1875, Edward A. Birge needed to be hired as an instructor of natural history (later Professor, Dean, and President) and “Assistant Curator of the Cabinet”. Science Hall was completed a year later, and the Museum, central to natural history research and instruction, was relocated to its fourth floor. The original Science Hall burned in the fall of 1884, and with it were lost many of the museum’s original collections.</p>
            <p>Shortly after this tragedy, the centralized Museum lost most of its original cohesive identity as the collections ultimately were started from scratch and re-born in different departments, but they grew once again at a rapid pace, separately, and continue to do so today.</p>

        </main>

        <?php
        include(__DIR__ . '/footer.php');
        ?>

    </body>
</html>
