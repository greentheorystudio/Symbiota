<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../classes/ImageProcessor.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
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
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Import/Update Data</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
	<script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/shared.js?ver=20220809" type="text/javascript"></script>
	<script>
        $(document).ready(function() {
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
    <a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management Panel</a> &gt;&gt;
    <b>Import/Update Occurrence Records and Images</b>
</div>
<div id="innertext">
	<h1>Data Upload Module</h1>
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
        echo '<div style="font-weight:bold;font-size:120%;">ERROR: you are not authorized to upload to this collection</div>';
    }
	?>
</div>
<?php
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>