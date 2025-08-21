<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/FileSystemService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['IS_ADMIN']) {
    header('Location: ../../index.php');
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Portal Configuration Manager</title>
    <meta name="description" content="Manage configurations for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/admin.portal.css?ver=20221103" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js?ver=20130917" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js?ver=20130917" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/admin.portal.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script type="text/javascript">
        const maxPostSize = <?php echo FileSystemService::getServerMaxPostSize(); ?>;
        const maxUploadSize = <?php echo FileSystemService::getServerMaxUploadFilesize(); ?>;

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
<div id="mainContainer" style="padding: 10px 15px 15px;">
    <div id="tabs" style="width:95%;">
        <ul>
            <li><a href='core.php'>Core Configurations</a></li>
            <li><a href='taxonomy.php'>Taxonomy Configurations</a></li>
            <li><a href="additional.php">Additional Configurations</a></li>
        </ul>
    </div>
</div>
<?php
include_once(__DIR__ . '/../../config/footer-includes.php');
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
