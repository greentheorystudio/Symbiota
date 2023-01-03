<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../classes/ImageProcessor.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 3600);
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = (int)$_REQUEST['collid'];
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Data Upload Module</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
	<script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/shared.js?ver=20221207" type="text/javascript"></script>
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
    <a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
    <b>Occurrence Data Upload Module</b>
</div>
<div id="innertext">
	<h2>Occurrence Data Upload Module</h2>
	<?php
	if($isEditor && $collid){
		?>
        <div id="tabs">
            <ul>
                <li><a href="#occurrences">Occurrence Records</a></li>
                <li><a href="#images">Images</a></li>
            </ul>

            <div id="occurrences">
                <?php include_once(__DIR__ . '/occurrenceloader.php'); ?>
            </div>

            <div id="images">
                <?php include_once(__DIR__ . '/imageloader.php'); ?>
            </div>
        </div>
        <?php
	}
	elseif($isEditor){
        echo '<div>ERROR: collection identifier not defined. Contact administrator</div>';
    }
    else{
        echo '<div style="font-weight:bold;">ERROR: you are not authorized to upload to this collection</div>';
    }
	?>
</div>
<?php
include(__DIR__ . '/../../footer.php');
include_once(__DIR__ . '/../../config/footer-includes.php');
?>
</body>
</html>
