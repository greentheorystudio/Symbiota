<?php
include_once(__DIR__ . '/config/symbini.php');
include_once(__DIR__ . '/classes/IRLManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$IRLManager = new IRLManager();

$totalTaxa = number_format($IRLManager->getTotalTaxa());
$totalTaxaWithDesc = number_format($IRLManager->getTotalTaxaWithDesc());
$totalOccurrenceRecords = number_format($IRLManager->getTotalOccurrenceRecords());
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo $GLOBALS['GOOGLE_TAG_MANAGER_ID'] ?? ''; ?>');
    </script>
    <!-- End Google Tag Manager -->
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/jquery-ui.css" type="text/css" rel="Stylesheet" />
    <style>
        .hero-container {
            background-image: url("content/imglib/static/20Donahue1.jpg");
        }
    </style>
    <script src="js/all.min.js" type="text/javascript"></script>
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery-ui.js" type="text/javascript"></script>
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
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $GLOBALS['GOOGLE_TAG_MANAGER_ID']; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="hero-container">
    <div class="top-shade-container"></div>
    <div class="logo-container">
        <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/janky_mangrove_logo_med.png" />
    </div>
    <div class="title-container">
        <h1>Indian River Lagoon<br />
            Species Inventory</h1>
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
</div>
<div id="innertext">
    <div class="totals-row">
        <div class="totals-box">
            <i style="height:60px;width:60px;" class="fas fa-leaf"></i>
            <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalTaxaWithDesc; ?></h2>
            <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;">Species Reports</h5>
        </div>
        <div class="totals-box">
            <i style="height:60px;width:60px;background-image: url('images/layout/fat_tree.svg');background-size:cover;filter: drop-shadow(10px 10px 4px lightgrey);"></i>
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
?>
</body>
</html>
