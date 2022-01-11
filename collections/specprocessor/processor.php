<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../classes/ImageLocalProcessor.php');
include_once(__DIR__ . '/../../classes/ImageProcessor.php');
include_once(__DIR__ . '/../../classes/SpecProcessorOcr.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';
$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$spprid = array_key_exists('spprid',$_REQUEST)?(int)$_REQUEST['spprid']:0;
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;
$spNlpId = array_key_exists('spnlpid',$_REQUEST)?(int)$_REQUEST['spnlpid']:0;
$procStatus = array_key_exists('procstatus',$_REQUEST)?$_REQUEST['procstatus']:'unprocessed';

$specManager = new SpecProcessorManager();
$specManager->setCollId($collid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = true;
}

if(in_array($action, array('dlnoimg','unprocnoimg','noskel','unprocwithdata'))){
	$specManager->downloadReportData($action);
	exit;
}

$statusStr = '';
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title>Occurrence Processor Control Panel</title>
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<?php
		include(__DIR__ . '/../../header.php');
		echo '<div class="navpath">';
		echo '<a href="../../index.php">Home</a> &gt;&gt; ';
		echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
		echo '<a href="index.php?collid='.$collid.'&tabindex='.$tabIndex.'"><b>Specimen Processor</b></a> &gt;&gt; ';
		echo '<b>Processing Handler</b>';
		echo '</div>';
		?>
		<div id="innertext">
			<h2><?php echo $specManager->getCollectionName(); ?></h2>
			<?php
			if($isEditor){
				$specManager->setProjVariables($spprid);
				if($action === 'Process Images'){
					if($specManager->getProjectType() === 'iplant'){
						$imageProcessor = new ImageProcessor($specManager->getConn());
						echo '<ul>';
						$imageProcessor->setLogMode(3);
						$imageProcessor->setCollid($collid);
						$imageProcessor->setSpprid($spprid);
						$imageProcessor->processIPlantImages($specManager->getSpecKeyPattern(), $_POST);
						echo '</ul>';
					}
					else{
						echo '<div style="padding:15px;">'."\n";
						$imageProcessor = new ImageLocalProcessor();

						$imageProcessor->setLogMode(3);
						$GLOBALS['LOG_PATH'] = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) === '/'?'':'/').'content/logs/imgProccessing';
						if(!file_exists($GLOBALS['LOG_PATH']) && !mkdir($GLOBALS['LOG_PATH']) && !is_dir($GLOBALS['LOG_PATH'])) {
                            throw new RuntimeException(sprintf('Directory "%s" was not created', $GLOBALS['LOG_PATH']));
                        }
						$imageProcessor->setLogPath($GLOBALS['LOG_PATH']);
						$logFile = $collid.'_'.$specManager->getInstitutionCode();
						if($specManager->getCollectionCode()) {
                            $logFile .= '-' . $specManager->getCollectionCode();
                        }
						$imageProcessor->initProcessor($logFile);
						$imageProcessor->setCollArr(array($collid => array('pmterm' => $specManager->getSpecKeyPattern(),'prpatt' => $specManager->getPatternReplace(),'prrepl' => $specManager->getReplaceStr())));
						$imageProcessor->setMatchCatalogNumber((array_key_exists('matchcatalognumber', $_POST)?1:0));
						$imageProcessor->setMatchOtherCatalogNumbers((array_key_exists('matchothercatalognumbers', $_POST)?1:0));
						$imageProcessor->setDbMetadata(1);
						$imageProcessor->setSourcePathBase($specManager->getSourcePath());
						$imageProcessor->setTargetPathBase($specManager->getTargetPath());
						$imageProcessor->setImgUrlBase($specManager->getImgUrlBase());
						$imageProcessor->setServerRoot($GLOBALS['SERVER_ROOT']);
						if($specManager->getWebPixWidth()) {
                            $imageProcessor->setWebPixWidth($specManager->getWebPixWidth());
                        }
						if($specManager->getTnPixWidth()) {
                            $imageProcessor->setTnPixWidth($specManager->getTnPixWidth());
                        }
						if($specManager->getLgPixWidth()) {
                            $imageProcessor->setLgPixWidth($specManager->getLgPixWidth());
                        }
						if($specManager->getWebMaxFileSize()) {
                            $imageProcessor->setWebFileSizeLimit($specManager->getWebMaxFileSize());
                        }
						if($specManager->getLgMaxFileSize()) {
                            $imageProcessor->setLgFileSizeLimit($specManager->getLgMaxFileSize());
                        }
						if($specManager->getJpgQuality()) {
                            $imageProcessor->setJpgQuality($specManager->getJpgQuality());
                        }
						$imageProcessor->setUseImageMagick($specManager->getUseImageMagick());
						$imageProcessor->setWebImg($_POST['webimg']);
						$imageProcessor->setTnImg($_POST['createtnimg']);
						$imageProcessor->setLgImg($_POST['createlgimg']);
						$imageProcessor->setCreateNewRec($_POST['createnewrec']);
						$imageProcessor->setImgExists($_POST['imgexists']);
						$imageProcessor->setKeepOrig(0);
						$imageProcessor->setSkeletalFileProcessing($_POST['skeletalFileProcessing']);

						$imageProcessor->batchLoadImages();
						echo '</div>'."\n";
					}
				}
				elseif($action === 'Process Output File'){
					$imageProcessor = new ImageProcessor($specManager->getConn());
					echo '<ul>';
					$imageProcessor->setLogMode(3);
					$imageProcessor->setSpprid($spprid);
					$imageProcessor->setCollid($collid);
					$imageProcessor->processiDigBioOutput($specManager->getSpecKeyPattern(),$_POST);
					echo '</ul>';

				}
				elseif($action === 'Load Image Data'){
					$imageProcessor = new ImageProcessor($specManager->getConn());
					echo '<ul>';
					$imageProcessor->setLogMode(3);
					$imageProcessor->setCollid($collid);
					$imageProcessor->loadFileData($_POST);
					echo '</ul>';
				}
				elseif($action === 'Run Batch OCR'){
					$ocrManager = new SpecProcessorOcr();
					$ocrManager->setVerbose(2);
					$batchLimit = 100;
					if(array_key_exists('batchlimit',$_POST)) {
                        $batchLimit = $_POST['batchlimit'];
                    }
					echo '<ul>';
					$ocrManager->batchOcrUnprocessed($collid,$procStatus,$batchLimit,0);
					echo '</ul>';
				}
				elseif($action === 'Load OCR Files'){
					$specManager->addProject($_POST);
					$ocrManager = new SpecProcessorOcr();
					$ocrManager->setVerbose(2);
					echo '<ul>';
					$ocrManager->harvestOcrText($_POST);
					echo '</ul>';
				}
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
			}
			?>
			<div style="font-weight:bold;font-size:120%;"><a href="index.php?collid=<?php echo $collid.'&tabindex='.$tabIndex; ?>"><b>Return to Specimen Processor</b></a></div>
		</div>
		<?php
			include(__DIR__ . '/../../footer.php');
		?>
	</body>
</html>
