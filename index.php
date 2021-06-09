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
        .totals-row {
            display: flex;
            width: 100%;
            justify-content: space-evenly;
            margin-top: 20px;
        }
        .totals-box {
            width: 22%;
            border: solid 4px #cccccc;
            padding: 30px;
            color: gray;
            display: flex;
            flex-direction:column;
            align-items:center;
        }

        .hero-container {
            background-image: url("images/layout/home-header-image.jpeg");
            width: 100%;
            height: 1000px;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
        }

        .title-container {
            position: absolute;
            top: 30px;
            left: 30px;
            color: white;
            padding-left: 20px;
        }

        .login-container {
            position: absolute;
            top: 15px;
            right: 0;
            background-color: rgba(0, 0, 0, 0.5);
            width: 500px;
            height: 35px;
            padding-left: 10px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .login-container a {
            color: white;
        }

        .login-link {
            font-family: 'Fira Sans';
            font-size: 9pt;
            color: white;
            margin: 10px;
        }

        .nav-bar-container {
            position: absolute;
            top: 150px;
            left: 0;
        }

        .quicksearch-container {
            position: absolute;
            top: 525px;
            left: 0;
            width: 100%;
            display: flex;
            align-content: center;
            color: black;
        }

        .heading-container {
            position: absolute;
            top: 750px;
            left: 0;
            width: 100%;
            color: white;
        }

        .heading-inner {
            padding: 15px;
            width: 75%;
            background-color: rgba(0, 0, 0, 0.5);
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .fa-leaf, .fa-dna, .fa-map-marked-alt{
            color: #1599AB;
            filter: drop-shadow(10px 10px 4px lightgrey);
            height: 60px;
            width: 60px;
        }

        #innertext{
            min-height:200px;
        }

        h3{
            font-family: 'Fira Sans';
            font-size: 14pt;
            font-weight: normal;
        }
    </style>
    <script src="js/all.min.js" type="text/javascript"></script>
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $GLOBALS['GOOGLE_TAG_MANAGER_ID']; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<?php
include(__DIR__ . '/home-header.php');
?>
<div id="innertext">
    <div class="totals-row">
        <div class="totals-box">
            <i style="height:60px;width:60px;" class="fas fa-leaf"></i>
            <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalTaxaWithDesc; ?></h2>
            <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;">Species Reports</h5>
        </div>
        <div class="totals-box">
            <i style="height:60px;width:60px;" class="fas fa-dna"></i>
            <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalTaxa; ?></h2>
            <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;"> Total Taxa</h5>
        </div>
        <div class="totals-box">
            <i style="height:60px;width:60px;" class="fas fa-map-marked-alt"></i>
            <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalOccurrenceRecords; ?></h2>
            <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;">Occurrence Records</h5>
        </div>
    </div>
    <p style="margin-top: 25px;width: 100%;text-align: center;font-size: 16px;">
        As you explore the portal and its resources, please reach out with comments, questions and concerns to <a href="mailto:irlwebmaster@si.edu">irlwebmaster@si.edu</a>
    </p>
</div>

<?php
include(__DIR__ . '/footer.php');
?>
</body>
</html>
