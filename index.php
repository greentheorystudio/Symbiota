<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/classes/IRLManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$IRLManager = new IRLManager();

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
        $(document).ready(function() {
            const quicksearchDiv = document.getElementById('quicksearchinputcontainer');
            const linkDiv = document.createElement('div');
            linkDiv.setAttribute("class","as");
            const linkElement = document.createElement('a');
            linkElement.setAttribute("href","<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/dynamictaxalist.php");
            linkElement.innerHTML = "Advanced Search";
            linkDiv.appendChild(linkElement);
            quicksearchDiv.appendChild(linkDiv);
         });
    </script>
    <?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
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

        $(document).ready(function() {
            const imgIndex = Math.floor(Math.random() * 20);
            document.getElementById('hero-container').style.backgroundImage = imgArray[imgIndex];
            document.getElementById('photographerName').innerHTML = photographerArray[imgIndex];
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
    <div class="top-shade-container"></div>
    <div class="logo-container">
        <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/janky_mangrove_logo_med.png" />
    </div>
    <div class="title-container">
        <span class="titlefont">Indian River Lagoon<br />
            Species Inventory</span>
    </div>
    <div class="login-container">
        <?php
        include(__DIR__ . '/header-login.php');
        ?>
    </div>
    <div class="nav-bar-container">
        <?php
        include(__DIR__ . '/header-navigation.php');
        ?>
    </div>
    <div class="quicksearch-container">
        <div class="searcharea">
            <div class="searchtop">
                <?php
                $searchText = '';
                $buttonText = 'Search the Inventory';
                $placeholderText = '';
                include_once(__DIR__ . '/classes/PluginsManager.php');
                $pluginManager = new PluginsManager();
                $pluginManager->setQuickSearchShowSelector(true);
                $pluginManager->setQuickSearchDefaultSetting('common');
                $quicksearch = $pluginManager->createQuickSearch($buttonText,$searchText);
                echo $quicksearch;
                ?>
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
<div id="innertext">
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
include(__DIR__ . '/footer.php');
include_once(__DIR__ . '/config/footer-includes.php');
?>
</body>
</html>
