<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

include_once(__DIR__ . '/classes/RandomSearchManager.php');
$taxon = array_key_exists("taxon",$_REQUEST)?trim($_REQUEST["taxon"]):"";
$imgLibManagerRand = new RandomSearchManager();

$target = array_key_exists("target",$_REQUEST)?trim($_REQUEST["target"]):"";
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
    <meta name='keywords' content='' />
    <script type="text/javascript">
        <?php include_once('config/googleanalytics.php'); ?>
    </script>
</head>
<body>
<?php
include(__DIR__ . '/header.php');
?>
<main>

    <!-- hero image -->
    <div class="uw-row-full">
        <div id="home-hero" class="uw-hero">
        </div>
    </div>



    <!-- Searches  -->
    <!-- featured content cards -->
    <section id="home-searches">
        <div class="uw-full-row uw-pad-tb-xl uw-light-grer-bg">
            <h2 class="uw-text-center uw-mini-bar uw-mini-bar-center">
                Searches</h2>
            <div class="uw-row">
                <!-- Quick Search -->
                <div class="uw-card">
                    <div class="uw-card-content" style="width: 100%">
                        <div class="uw-text-center">
                            <a href="1"><i class="fi-magnifying-glass"></i></a>
                        </div>
                        <div class="uw-card-copy">
                            <h2 class="uw-mini-bar"><a href="1">Quick Search</a></h2>
                            <p>Search by taxon or object category:</p>
                            <!-- Original Symbiota Quick Search -->
                            <div>
                                <!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
                                <form name="quicksearch" id="quicksearch" action="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
                                    <input id="taxa" type="text" name="taxon" />
                                    <button name="formsubmit" class="uw-button" id="quicksearchbutton" type="submit" value="Search Terms">Search</button>
                                </form>
                            </div>
                            <!-- Random Taxon -->
                            <div id="randnum">Random Taxon:
                                <?php
                                $randomTaxon = $imgLibManagerRand->getRandomTID();
                                if($randomTaxon){
                                    foreach ($randomTaxon as $key => $value) {
                                        echo "<div style='font-style:italic;'>";
                                        echo "<a href='../taxa/index.php?taxon=".$key."' target='_blank'>".$value."</a>";
                                        echo "</div>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Advanced Search -->
                <div class="uw-card">
                    <div class="uw-card-content">
                        <div class="uw-text-center">
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php"><i class="fi-zoom-in"></i></a>
                        </div>
                        <div class="uw-card-copy">
                            <h2 class="uw-mini-bar"><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/index.php">Advanced Search</a></h2>
                            <p>Search Collections for specimens, taxa or object categories using advanced criteria.</p>
                        </div>
                    </div>
                </div>
                <!-- Map Search -->
                <div class="uw-card">
                    <div class="uw-card-content">
                        <div class="uw-text-center">
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial-module-info.php"><i class="fi-map"></i></a>
                        </div>
                        <div class="uw-card-copy">
                            <h2 class="uw-mini-bar"><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php">Spatial Module</a></h2>
                            <p>Live search specimens in map.</p>
                            <p>Disclaimer: only specimens with full geographical coordinates are searchable.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Browse  -->
    <!-- featured content cards -->
    <section id="home-browse">
        <div class="uw-full-row uw-pad-tb-xl">
            <h2 class="uw-text-center uw-mini-bar uw-mini-bar-center">
                Browse</h2>
            <div class="uw-row">

                <div class="uw-card">
                    <div class="uw-card-content">
                        <div class="uw-text-center">
                            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php"><i class="fi-photo"></i></a>
                        </div>
                        <div class="uw-card-copy">
                            <h2 class="uw-mini-bar"><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/index.php">Images</a></h2>
                            <p>Browse images by taxa or object category.</p>
                        </div>
                    </div>
                </div>

                <div class="uw-card">
                    <div class="uw-card-content">
                        <div class="uw-text-center">
                            <a href=""><i class="fi-folder"></i></a>
                        </div>
                        <div class="uw-card-copy">
                            <h2 class="uw-mini-bar"><a href="">Taxa and Objects</a></h2>
                            <p>Check the taxa and objects with specimens in our database using the hierarchy explorer.</p>
                        </div>
                    </div>
                </div>

                <div class="uw-card">
                    <div class="uw-card-content">
                        <div class="uw-text-center">
                            <a href=""><i class="fi-results"></i></a>
                        </div>
                        <div class="uw-card-copy">
                            <h2 class="uw-mini-bar"><a href="collections/misc/collprofiles.php">Collections</a></h2>
                            <p>Browse our Museums and Collections.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>

    <!-- Featured Specimens  -->
    <section id="home-specimens" class="uw-row-full uw-pad-tb-l uw-light-grer-bg" style="display: none;">
        <div class="uw-row">
            <div class="uw-col uw-body">
                <h2 class="uw-text-center uw-mini-bar uw-mini-bar-center">
                    Featured Specimens
                </h2>
                <div class="row">
                    <div class="eight columns slide">
                        <img src="https://placeholdit.imgix.net/~text?txtsize=84&amp;bg=dbdbdb&amp;txt=Feature Item&amp;w=620&amp;h=310" alt="Placeholder image for deomnstration">
                    </div>
                    <div class="four columns slide-description">
                        <h2>Curator's Favorites</h2>
                        <p>A collection of specimens picked out by the curators.</p>
                        <button><a href="" class="uw-button">View</a></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About  -->
    <section id="home-about" class="uw-row-full uw-pad-tb-l">
        <div class="uw-row">
            <div class="uw-col uw-body">
                <h2 class="uw-text-center uw-mini-bar uw-mini-bar-center">
                    About the Portal
                </h2>
                <p>At present >11 million natural history museum specimens, used for research, teaching, and outreach, are housed in the collections of the UW-Madison departments of anthropology, botany, entomology, geoscience, and zoology.</p>
                <p>This portal is an effort in providing access to those specimens.</p>
                <button><a href="#" class="uw-button">Learn more</a></button>
            </div>
        </div>
    </section>

</main>
<?php
include(__DIR__ . '/footer.php');
?>
</body>
</html>
