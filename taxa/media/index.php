<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Media Batch Uploader</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
	<script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
	<script>
        document.addEventListener("DOMContentLoaded", function() {
            $('#tabs').tabs({
                active: <?php echo $tabIndex; ?>,
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
<div class="navpath">
    <a href="../../index.php">Home</a> &gt;&gt;
    <b>Taxa Media Batch Uploader</b>
</div>
<div id="innertext">
	<h1>Taxa Media Batch Uploader</h1>
	<?php
	if($isEditor){
		?>
        <div id="tabs">
            <ul>
                <li><a href="#batch">Batch Image Loader</a></li>
                <li><a href="#eol">EOL Loader</a></li>
            </ul>

            <div id="batch">
                <?php include_once(__DIR__ . '/batchimageloader.php'); ?>
            </div>

            <div id="eol">
                <?php include_once(__DIR__ . '/eolmapper.php'); ?>
            </div>
        </div>
        <?php
	}
	else{
        echo '<div style="font-weight:bold;">You do not have permissions to access this tool</div>';
    }
	?>
</div>
<?php
include(__DIR__ . '/../../footer.php');
include_once(__DIR__ . '/../../config/footer-includes.php');
?>
</body>
</html>
