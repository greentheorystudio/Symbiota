<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/GUIDManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 3600);

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$action = array_key_exists('formsubmit',$_POST)?htmlspecialchars($_POST['formsubmit']):'';

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = 1;
}

$uuidManager = new GUIDManager();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> GUID/UUID Generator</title>
    <meta name="description" content="GUID/UUID generator for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
</head>
<body>
<?php 
include(__DIR__ . '/../../header.php');
?>
<div id="mainContainer" style="padding: 10px 15px 15px;">
    <div id="breadcrumbs">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
        <?php
        if($collId){
            echo '<a href="../../collections/misc/collprofiles.php?collid='.$collId.'&emode=1">Collection Control Panel</a> &gt;&gt;';
        }
        ?>
        <b>GUID/UUID Generator</b>
    </div>
    <?php
	if($isEditor){
		if($action === 'Generate Collection GUIDs/UUIDs'){
			echo '<ul>';
			$uuidManager->populateGuids($collId);
			echo '</ul>';
		}
		elseif($action === 'Generate GUIDs/UUIDs'){
			echo '<ul>';
			$uuidManager->populateGuids();
			echo '</ul>';
		}
		
		$occCnt = $uuidManager->getOccurrenceCount($collId);
		$detCnt = $uuidManager->getDeterminationCount($collId);
		$imgCnt = $uuidManager->getImageCount($collId);
		if($collId) {
            echo '<h3>' . $uuidManager->getCollectionName($collId) . '</h3>';
        }
		?>
		<div style="font-weight:bold;">Records without GUIDs/UUIDs</div>
		<div style="margin:10px;">
			<div><b>Occurrences: </b><?php echo $occCnt; ?></div>
			<div><b>Determinations: </b><?php echo $detCnt; ?></div>
			<div><b>Images: </b><?php echo $imgCnt; ?></div>
		</div>
		<?php 
		if($collId){
			?>
			<form name="guidform" action="guidmapper.php" method="post">
				<fieldset style="padding:15px;">
					<legend><b>Generator</b></legend>
					<div style="clear:both;">
						<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
						<input type="submit" name="formsubmit" value="Generate Collection GUIDs/UUIDs" />
					</div>
				</fieldset>
			</form>
			<?php
		}
		elseif($GLOBALS['IS_ADMIN']){
			?>
			<div id="guidadmindiv">
				<form name="dwcaguidform" action="guidmapper.php" method="post">
					<fieldset style="padding:15px;">
						<legend><b>GUID (UUID) Mapper</b></legend>
						<div style="clear:both;margin:10px;">
							<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
							<input type="submit" name="formsubmit" value="Generate GUIDs/UUIDs" />
						</div>
					</fieldset>
				</form>
			</div>
			<?php
		}
	}
	else{
		echo '<h2>You are not authorized to access this page</h2>';
	}
	?>
</div>
<?php
include_once(__DIR__ . '/../../config/footer-includes.php');
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
