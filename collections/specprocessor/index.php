<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../classes/OccurrenceCrowdSource.php');
include_once(__DIR__ . '/../../classes/SpecProcessorOcr.php');
include_once(__DIR__ . '/../../classes/ImageProcessor.php');
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

$fileName = '';
$statusStr = '';
if($isEditor){
	if($action === 'Analyze Image Data File'){
		if($_POST['projecttype'] === 'file'){
			$imgProcessor = new ImageProcessor();
			$fileName = $imgProcessor->loadImageFile();
		}
	}
	elseif($action === 'Save Profile'){
		if($_POST['spprid']){
			$specManager->editProject($_POST);
		}
		else{
			$specManager->addProject($_POST);
		}
	}
	elseif($action === 'Delete Profile'){
		$specManager->deleteProject($_POST['sppriddel']);
	}
	elseif($action === 'Add to Queue'){
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
		<title>Specimen Processor Control Panel</title>
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
        <script src="../../js/all.min.js" type="text/javascript"></script>
		<script src="../../js/jquery.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../js/symb/shared.js?ver=20211227" type="text/javascript"></script>
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
        echo '<b>Specimen Processor Control Panel</b>';
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
				        <li><a href="#introdiv">Introduction</a></li>
				        <li><a href="imageprocessor.php?collid=<?php echo $collid.'&spprid='.$spprId.'&submitaction='.$action.'&filename='.$fileName; ?>">Image Loading</a></li>
				        <li><a href="crowdsource/controlpanel.php?collid=<?php echo $collid; ?>">Crowdsourcing</a></li>
				        <li><a href="ocrprocessor.php?collid=<?php echo $collid.'&procstatus='.$procStatus.'&spprid='.$spprId; ?>">OCR</a></li>
				        <li><a href="reports.php?<?php echo str_replace('&amp;', '&',htmlspecialchars($_SERVER['QUERY_STRING'])); ?>">Reports</a></li>
				        <li><a href="exporter.php?collid=<?php echo $collid.'&displaymode='.$displayMode; ?>">Exporter</a></li>
				        <?php 
				        if($GLOBALS['ACTIVATE_GEOLOCATE_TOOLKIT']){
					        ?>
					        <li><a href="geolocate.php?collid=<?php echo $collid; ?>">GeoLocate CoGe</a></li>
					        <?php 	
				        }
				        ?>
				    </ul>
					<div id="introdiv">
						<h1>Specimen Processor Control Panel</h1>
						<div style="margin:10px">
							This management module is designed to aid in establishing advanced processing workflows 
							for unprocessed specimens using images of the specimen label. The central functions addressed in this page are:
							Batch loading images, Optical Character Resolution (OCR), Natural Language Processing (NLP), 
							and crowdsourcing data entry. 
							Use tabs above for access tools.     
						</div>
						<div style="margin:10px;min-height:400px;">
							<h2>Image Loading</h2>
							<div style="margin:15px">
								The batch image loading module is designed to batch process specimen images that are deposited in a 
								drop folder. This module will produce web-ready images for a group of specimen images and 
								map the new image derivative to specimen records. Images can be linked to already existing 
								specimen records, or linked to a newly created skeletal specimen record for further digitization within the portal.
								Field data from skeletal data files (.csv, .tab, .dat) placed in the image folders will  
								augment new records by adding content to empty fields only. 
								The column names of skeletal files must match Symbiota field names (e.g. Darwin Core) with catalogNumber as a 
								required field. For more information, see the
								<b><a href="http://symbiota.org/docs/batch-loading-specimen-images-2/">Batch Image Loading</a></b> section 
								on the <b><a href="http://symbiota.org">Symbiota</a> website</b>.   
							</div>

							<h2>Crowdsourcing Module</h2>
							<div style="margin:15px">
								The crowdsourcing module can be used to make unprocessed records accessible for data entry by 
								general users who typically do not have explicit editing writes for a particular collection. 
								For more information, see the
								<b><a href="http://symbiota.org/docs/crowdsourcing-within-symbiota-2/">Crowdsource</a></b> section 
								on the <b><a href="http://symbiota.org">Symbiota</a> website</b>.   
							</div>

							<h2>Optical Character Resolution (OCR)</h2>
							<div style="margin:15px;">
								The OCR module gives collection managers the ability to batch OCR specimen images using the Tesseract OCR 
								engine or process and upload text files containing OCR obtained from other OCR software.   
							</div>
                        </div>
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
