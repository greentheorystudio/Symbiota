<?php
/** @var array $dupArr */
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCleaner.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$obsUid = array_key_exists('obsuid',$_REQUEST)?(int)$_REQUEST['obsuid']:0;
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']):'';

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
    $action = '';
}

$cleanManager = new OccurrenceCleaner();
if($collid) {
    $cleanManager->setCollId($collid);
}
$collMap = $cleanManager->getCollMap();

$statusStr = '';
$isEditor = 0; 
if($GLOBALS['IS_ADMIN'] || ($collMap['colltype'] === 'HumanObservation')
	|| (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}

if($collMap['colltype'] === 'HumanObservation' && $obsUid !== 0){
	$obsUid = $GLOBALS['SYMB_UID'];
	$cleanManager->setObsUid($obsUid);
}

?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Field Standardization</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
	
	</script>
</head>
<body>
	<?php 	
	if(!$dupArr) {
        include(__DIR__ . '/../../header.php');
    }
	?>
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
		<b>Batch Field Cleaning Tools</b>
	</div>

	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:20px;color:<?php echo (strncmp($statusStr, 'ERROR', 5) === 0 ?'red':'green');?>">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php 
		}
        echo '<h2>'.$collMap['collectionname'].($collMap['code']?' ('.$collMap['code'].')':'').'</h2>';
		if($isEditor){
			?>
			<div>
				Description...
			</div>
			<fieldset style="padding:20px;">
				<legend><b>Country</b></legend>
				<div style="margin:5px">
					<select name="country_old">
						<option value="">Select Target Field</option>
						<option value="">--------------------------------</option>
					</select>
				</div>
				<div style="margin:5px">
					<b>Replacement Value:</b> 
					<input name="country_new" type="text" value="" /> 
				</div>
			</fieldset>
			<?php 
		}
		else{
			echo '<h2>You are not authorized to access this page</h2>';
		}
		?>
	</div>
<?php 	
if(!$dupArr){
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
}
?>
</body>
</html>
