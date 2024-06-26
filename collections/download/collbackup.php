<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']):'';
$cSet = array_key_exists('cset',$_REQUEST)?htmlspecialchars($_REQUEST['cset']):'';

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title>Occurrences download</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <script>
    	function submitBuForm(f){
			f.action.disabled = true;
			document.getElementById("workingdiv").style.display = "block";
			return true;
    	}
    </script>
</head>
<body>
	<div id="innertext">
		<?php 
		if($isEditor){
			?>
			<form name="buform" action="downloadhandler.php" method="post" onsubmit="return submitBuForm(this);">
				<fieldset style="padding:15px;width:350px">
					<legend>Backup Downloader</legend>
					<div style="float:left;">
						Data Set: 
					</div>
					<div style="float:left;height:50px">
						<input type="radio" name="cset" value="iso-8859-1" <?php echo (!$cSet || $cSet === 'iso88591'?'checked':''); ?> /> ISO-8859-1 (western)<br/>
						<input type="radio" name="cset" value="utf-8" <?php echo ($cSet === 'utf8'?'checked':''); ?> /> UTF-8 (unicode)
					</div>
					<div style="clear:both;">
						<div style="float:left">
							<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
							<input type="hidden" name="schema" value="backup" />
							<input type="submit" name="action" value="Download Backup" />
                            <input type="hidden" name="tabindex" value="1" />
						</div>
						<div id="workingdiv" style="float:left;margin-left:15px;display:<?php echo ($action === 'Download Backup'?'block':'none'); ?>;">
							<b>Downloading backup file...</b> 
						</div>
					</div>
				</fieldset>
			</form>
			<?php 
		}
		?>
	</div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
</body>
</html>
