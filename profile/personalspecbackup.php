<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ProfileManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$collId = (int)$_REQUEST['collid'];
$action = array_key_exists('formsubmit',$_REQUEST)?$_REQUEST['formsubmit']:'';
$cSet = array_key_exists('cset',$_REQUEST)?$_REQUEST['cset']:'utf8';
$zipFile = array_key_exists('zipfile',$_REQUEST)?(int)$_REQUEST['zipfile']:0;

$dlManager = new ProfileManager();
$dlManager->setUid($GLOBALS['SYMB_UID']);

$editable = 0;
if($GLOBALS['IS_ADMIN']
	|| (array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))
	|| (array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
		$editable = 1;
}
?>

<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title>Personal Specimen Backup</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
</head>
<body>
<div id="innertext">
	<?php 
	if($editable){
		if($action === 'Perform Backup'){
			echo '<ul>';
			$dlFile = $dlManager->dlSpecBackup($collId,$cSet,$zipFile);
			if($dlFile){
				echo '<li style="font-weight:bold;">Backup Complete!</li>';
				echo '<li style="font-weight:bold;">Click on file to download: <a href="'.$dlFile.'">'.$dlFile.'</a></li>';
				echo '</ul>';
			}
			echo '</ul>';
		}
		else{
			?>
			<form name="buform" action="personalspecbackup.php" method="post">
				<fieldset style="padding:15px;">
					<legend>Download Module</legend>
					<div style="float:left;">
						Data Set: 
					</div>
					<div style="float:left;">
						<?php 
						$cSet = str_replace('-','',strtolower($GLOBALS['CHARSET']));
						?>
						<input type="radio" name="cset" value="latin1" <?php echo ($cSet === 'iso88591'?'checked':''); ?> /> ISO-8859-1 (western)<br/>
						<input type="radio" name="cset" value="utf8" <?php echo ($cSet === 'utf8'?'checked':''); ?> /> UTF-8 (unicode)
					</div>
					<div style="clear:both;">
						<input name="zipfile" type="checkbox" value="1" CHECKED /> 
						Compress data into a zip file
					</div>
					<div style="clear:both;">
						<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
						<input type="submit" name="formsubmit" value="Perform Backup" />
					</div>
				</fieldset>
			</form>
			<?php 
		}
	}
	?>
</div>
</body>
</html>
