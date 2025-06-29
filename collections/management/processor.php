<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../classes/ImageLocalProcessor.php');
include_once(__DIR__ . '/../../classes/ImageProcessor.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 3600);

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
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
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Processor Control Panel</title>
    <meta name="description" content="Processor control panel for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    echo '<div id="breadcrumbs">';
    echo '<a href="../../index.php">Home</a> &gt;&gt; ';
    echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
    echo '<a href="../upload/index.php?collid='.$collid.'&tabindex='.$tabIndex.'"><b>Occurrence Data Upload Module</b></a> &gt;&gt; ';
    echo '<b>Image Processor</b>';
    echo '</div>';
    ?>
    <div id="mainContainer" style="padding: 10px 15px 15px;">
        <h2><?php echo $specManager->getCollectionName(); ?></h2>
        <?php
        if($isEditor){
            $specManager->setProjVariables($spprid);
            if($action === 'Process Images'){
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
                $imageProcessor->setCollArr(array($collid => array('pmterm' => $specManager->getSpecKeyPattern())));
                $imageProcessor->setMatchCatalogNumber((array_key_exists('matchcatalognumber', $_POST)?1:0));
                $imageProcessor->setMatchOtherCatalogNumbers((array_key_exists('matchothercatalognumbers', $_POST)?1:0));
                $imageProcessor->setSourcePathBase($specManager->getSourcePath());
                $imageProcessor->setTnImg((int)$_POST['createtnimg']);
                $imageProcessor->setLgImg((int)$_POST['createlgimg']);
                $imageProcessor->setCreateNewRec($_POST['createnewrec']);
                $imageProcessor->setImgExists($_POST['imgexists']);
                $imageProcessor->setKeepOrig(0);
                $imageProcessor->setSkeletalFileProcessing($_POST['skeletalFileProcessing']);
                $imageProcessor->batchLoadImages();
                echo '</div>'."\n";
            }
            elseif($action === 'Load Image Data'){
                $imageProcessor = new ImageProcessor($specManager->getConn());
                echo '<ul>';
                $imageProcessor->setLogMode(3);
                $imageProcessor->setCollid($collid);
                $imageProcessor->loadFileData($_POST);
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
        <div style="font-weight:bold;"><a href="../upload/index.php?collid=<?php echo $collid.'&tabindex='.$tabIndex; ?>"><b>Return to Occurrence Data Upload Module</b></a></div>
    </div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
    ?>
</body>
</html>
