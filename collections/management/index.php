<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../classes/OccurrenceCrowdSource.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';
$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$spprId = array_key_exists('spprid',$_REQUEST)?(int)$_REQUEST['spprid']:0;
$spNlpId = array_key_exists('spnlpid',$_REQUEST)?(int)$_REQUEST['spnlpid']:0;
$procStatus = array_key_exists('procstatus',$_REQUEST)?$_REQUEST['procstatus']:'unprocessed';
$displayMode = array_key_exists('displaymode',$_REQUEST)?(int)$_REQUEST['displaymode']:0;
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
    $action = '';
}
if($procStatus && !preg_match('/^[a-zA-Z]+$/',$procStatus)) {
    $procStatus = '';
}

$specManager = new SpecProcessorManager();
$csManager = new OccurrenceCrowdSource();
$specManager->setCollId($collid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
 	$isEditor = true;
}

$statusStr = '';
if($isEditor){
	if($action === 'Add to Queue'){
		$csManager->setCollid($collid);
		$statusStr = $csManager->addToQueue($_POST['omcsid'],$_POST['family'],$_POST['taxon'],$_POST['country'],$_POST['stateprovince'],$_POST['limit']);
		if(is_numeric($statusStr)){
			$statusStr .= ' records added to queue';
		}
		$action = '';
	}
	elseif($action === 'delqueue'){
		$csManager->setCollid($collid);
		$statusStr = $csManager->deleteQueue();
	}
	elseif($action === 'Edit Crowdsource Project'){
		$omcsid = $_POST['omcsid'];
		$csManager->setCollid($collid);
		$statusStr = $csManager->editProject($omcsid,$_POST['instr'],$_POST['url']);
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title>Data Management Toolbox</title>
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
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
        echo '<div class="navpath">';
        echo '<a href="../../index.php">Home</a> &gt;&gt; ';
        echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
        echo '<b>Data Management Toolbox</b>';
        echo '</div>';
		?>
		<div id="innertext">
			<h2><?php echo $specManager->getCollectionName(); ?></h2>
			<?php
			if($statusStr){ 
				?>
				<div style='margin:20px 0 20px 0;'>
					<hr/>
					<div style="margin:15px;color:<?php echo (stripos($statusStr,'error') !== false?'red':'green'); ?>">
						<?php echo $statusStr; ?>
					</div>
					<hr/>
				</div>
				<?php 
			}
			if($collid){
				?>
				<div id="tabs" class="taxondisplaydiv">
				    <ul>
				        <li><a href="crowdsource/controlpanel.php?collid=<?php echo $collid; ?>">Crowdsourcing</a></li>
				        <li><a href="#dupmanager">Duplicate Clustering</a></li>
                        <?php
				        if(isset($GLOBALS['ACTIVATE_GEOLOCATE_TOOLKIT']) && $GLOBALS['ACTIVATE_GEOLOCATE_TOOLKIT']){
					        ?>
					        <li><a href="geolocate.php?collid=<?php echo $collid; ?>">GeoLocate CoGe</a></li>
					        <?php 	
				        }
				        ?>
                        <li><a href="reports.php?<?php echo str_replace('&amp;', '&',htmlspecialchars($_SERVER['QUERY_STRING'])); ?>">Reports</a></li>
				    </ul>

                    <div id="dupmanager">
                        <?php include_once(__DIR__ . '/duplicatemanager.php'); ?>
                    </div>
                </div>
				<?php 
			}
			else{
				?>
				<div style='font-weight:bold;'>
					Collection project has not been identified
				</div>
				<?php
			}
			?>
		</div>
		<?php
			include(__DIR__ . '/../../footer.php');
		?>
	</body>
</html>
