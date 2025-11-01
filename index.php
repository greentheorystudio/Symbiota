<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/services/IRLDataService.php');
header('Content-Type: text/html; charset=UTF-8' );

$IRLManager = new IRLDataService();

$totalTaxa = number_format($IRLManager->getTotalTaxa());
$totalTaxaWithDesc = number_format($IRLManager->getTotalTaxaWithDesc());
$totalOccurrenceRecords = number_format($IRLManager->getTotalOccurrenceRecords());
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/external/jquery-ui.css" type="text/css" rel="Stylesheet" />
    <script src="js/external/all.min.js" type="text/javascript"></script>
    <script src="js/external/jquery.js" type="text/javascript"></script>
    <script src="js/external/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        var imgArray = [
            "url(content/imglib/static/09ThomanR1.jpg)",
            "url(content/imglib/static/20Donahue1.jpg)",
            "url(content/imglib/static/21ShirahD1.jpg)",
            "url(content/imglib/static/21VanMeterS2.jpg)",
            "url(content/imglib/static/04BergerJ3.jpg)",
            "url(content/imglib/static/11SmithA1.jpg)",
            "url(content/imglib/static/17AdamsN1.jpg)",
            "url(content/imglib/static/18FischerD1.JPG)",
            "url(content/imglib/static/19CunninghamD1.jpg)",
            "url(content/imglib/static/19GilbertD1.jpg)",
            "url(content/imglib/static/19PalmerC2.jpg)",
            "url(content/imglib/static/21SmithA1.jpg)",
            "url(content/imglib/static/06KemptonR1.jpg)",
            "url(content/imglib/static/13ClarkeG1.jpg)",
            "url(content/imglib/static/13CorapiP1.jpg)",
            "url(content/imglib/static/17Spratt2.jpg)",
            "url(content/imglib/static/18CoteM1.JPG)",
            "url(content/imglib/static/18PhippsL2.jpg)",
            "url(content/imglib/static/18SimonsT1.jpg)",
            "url(content/imglib/static/20MandevilleJ2.jpg)"];
        var photographerArray = [
            "R. Thoman",
            "M. Donahue",
            "D. Shirah",
            "S. Van Meter",
            "J. Berger",
            "A. Smith",
            "N. Adams",
            "D. Fischer",
            "D. Cunningham",
            "D. Gilbert",
            "C. Palmer",
            "A. Smith",
            "R. Kempton",
            "G. Clarke",
            "P. Corapi",
            "R. Spratt",
            "M. Cote",
            "L. Phipps",
            "T. Simons",
            "J. Mandeville"];
        var altTextArray = [
            "Red boat hull reflected in calm marina water with wooden posts and dock elements.",
            "",
            "Sunset over calm water with clouds and sky reflected in the surface.",
            "Blue scrub jay flying over green trees under a cloudy sky.",
            "Dragonfly on a multicolored leaf with transparent wings and a long body.",
            "Cracked, dry riverbed with a muddy path leading to green vegetation under an orange sunrise or sunset.",
            "Lightning strikes over a large body of water under dramatic purple storm clouds, viewed from a rocky shoreline.",
            "Wooden hut on stilts over water, with a pier, palm trees, and a rainbow in the background.",
            "A large flock of ibises flying over green trees and foliage.",
            "Dolphin catching a mullet mid-air with splashing water.",
            "Person kayaking on a calm river, wearing an orange life jacket and red cap, with tree reflections on the water.",
            "Four stingrays swimming in dark green water.",
            "Under a lit bridge at night with glowing columns and water reflections.",
            "Sunset over calm water with clouds, a tree silhouette, and shoreline reflections.",
            "Close-up of a blue and brown land crab on sand.",
            "Sea turtle swimming in clear turquoise water with sunlight reflecting off the surface and rocks below.",
            "Dolphins swimming near shore with land in the background.",
            "Calm water reflecting a partly cloudy sky with scattered small mangrove trees in the foreground and buildings on the horizon.",
            "Starry night sky with Milky Way over water and illuminated trees.",
            "Storm clouds over water with a docked boat and distant red lighthouse."];

        $(document).ready(function() {
            const imgIndex = Math.floor(Math.random() * 20);
            document.getElementById('hero-container').style.backgroundImage = imgArray[imgIndex];
            document.getElementById('photographerName').innerHTML = photographerArray[imgIndex];
            document.getElementById('hero-container-alt').setAttribute('aria-label', altTextArray[imgIndex]);
        });
    </script>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $GLOBALS['GOOGLE_TAG_MANAGER_ID']; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div class="hero-container" id="hero-container">
        <span id="hero-container-alt" class="screen-reader-only" role="img" aria-label=""> </span>
        <div class="top-shade-container"></div>
        <div class="logo-container">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" aria-label="Go to homepage" tabindex="0">
                <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/janky_mangrove_logo_med.png" alt="Mangrove logo" />
            </a>
        </div>
        <div class="title-container">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" aria-label="Go to homepage" tabindex="0">
                <span class="titlefont">Indian River Lagoon<br />
                Species Inventory</span>
            </a>
        </div>
        <div class="login-bar">
            <?php
            include(__DIR__ . '/header-login.php');
            ?>
        </div>
        <div class="nav-bar-container">
            <?php
            include(__DIR__ . '/header-navigation.php');
            ?>
        </div>
        <div id="quicksearch-container" class="quicksearch-container">
            <div class="full-width row justify-center">
                <div class="col-6">
                    <taxa-quick-search></taxa-quick-search>
                </div>
            </div>
        </div>
        <div class="heading-container">
            <div class="heading-inner">
                <h3>The <b>Indian River Lagoon Species Inventory</b> is a dynamic and growing research resource and ecological encyclopedia that documents the biodiversity of the 156-mile-long estuary system along Florida’s Atlantic coast.</h3>
            </div>
        </div>
        <div class="photo-credit-container">
            Photo credit: <span id="photographerName"></span>
        </div>
    </div>
    <div id="mainContainer" style="padding: 10px 15px 15px;">
        <div class="totals-row">
            <div class="totals-box">
                <i style="height:60px;width:60px;" class="fas fa-leaf"></i>
                <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalTaxaWithDesc; ?></h2>
                <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;">Species Reports</h5>
            </div>
            <div class="totals-box">
                <i style="height:60px;width:60px;background-image: url('/content/imglib/layout/fat_tree.svg');background-size:cover;filter: drop-shadow(10px 10px 4px lightgrey);"></i>
                <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalTaxa; ?></h2>
                <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;"> Total Taxa</h5>
            </div>
            <div class="totals-box">
                <i style="height:60px;width:60px;" class="fas fa-map-marked-alt"></i>
                <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalOccurrenceRecords; ?></h2>
                <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;">Occurrence Records</h5>
            </div>
        </div>
    </div>
    <?php
    include_once(__DIR__ . '/config/footer-includes.php');
    include(__DIR__ . '/footer.php');
    ?>
    <script>
        const quickSearchElement = Vue.createApp({
            components: {
                'taxa-quick-search': taxaQuickSearch
            }
        });
        quickSearchElement.use(Quasar, { config: {} });
        quickSearchElement.use(Pinia.createPinia());
        quickSearchElement.mount('#quicksearch-container');
    </script>
</body>
</html>
