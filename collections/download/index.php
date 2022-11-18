<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
ini_set('max_execution_time', 3600);
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = (int)$_REQUEST['collid'];
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']): '';
$cSet = array_key_exists('cset',$_REQUEST)?htmlspecialchars($_REQUEST['cset']):'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

if($action && !preg_match('/^[a-zA-Z\d\s_]+$/',$action)) {
    $action = '';
}

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Data Exporter and Backup</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
	<script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/shared.js?ver=20221117" type="text/javascript"></script>
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
    <a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
    <b>Data Exporter and Backup</b>
</div>
<div id="innertext">
	<h1>Data Exporter and Backup Module</h1>
	<?php
	if($isEditor && $collid){
		?>
        <div id="tabs">
            <ul>
                <li><a href="collbackup.php?collid=<?php echo $collid.'&cset='.$cSet.'&action='.$action; ?>">Backup</a></li>
                <li><a href="exporter.php?collid=<?php echo $collid; ?>">Exporter</a></li>
            </ul>
        </div>
        <?php
	}
	elseif($isEditor){
        echo '<div>ERROR: collection identifier not defined. Contact administrator</div>';
    }
    else{
        echo '<div style="font-weight:bold;font-size:120%;">ERROR: you are not authorized to access this page</div>';
    }
	?>
</div>
<?php
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
