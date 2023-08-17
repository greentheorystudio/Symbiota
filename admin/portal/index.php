<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ConfigurationManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['IS_ADMIN']) {
    header('Location: ../../index.php');
}

$confManager = new ConfigurationManager();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Portal Configuration Manager</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link type="text/css" href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" />
    <link type="text/css" href="../../css/admin.portal.css?ver=20221103" rel="stylesheet" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../js/external/jquery.js?ver=20130917"></script>
    <script type="text/javascript" src="../../js/external/jquery-ui.js?ver=20130917"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/admin.portal.js?ver=20230314" type="text/javascript"></script>
    <script type="text/javascript">
        const maxPostSize = <?php echo $confManager->getServerMaxPostSize(); ?>;
        const maxUploadSize = <?php echo $confManager->getServerMaxUploadFilesize(); ?>;

        document.addEventListener("DOMContentLoaded", function() {
            $('#tabs').tabs({
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
        });
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div id="innertext">
    <div id="tabs" style="width:95%;">
        <ul>
            <li><a href='core.php'>Core Configurations</a></li>
            <li><a href='taxonomy.php'>Taxonomy Configurations</a></li>
            <li><a href="additional.php">Additional Configurations</a></li>
        </ul>
    </div>
</div>
<?php
include(__DIR__ . '/../../footer.php');
include_once(__DIR__ . '/../../config/footer-includes.php');
?>
</body>
</html>
