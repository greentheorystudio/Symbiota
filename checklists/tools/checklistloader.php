<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ChecklistLoaderManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']: '';
$pid = array_key_exists('pid',$_REQUEST)?(int)$_REQUEST['pid']: '';
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']): '';

$clLoaderManager = new ChecklistLoaderManager();
$clLoaderManager->setClid($clid);
$clMeta = $clLoaderManager->getChecklistMetadata();

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
	$isEditor = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Species Checklist Loader</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
		function validateUploadForm(){
            let testStr = document.getElementById("uploadfile").value;
            if(testStr === ""){
				alert("Please select a file to upload");
				return false;
			}
			testStr = testStr.toLowerCase();
			if(testStr.indexOf(".csv") == -1 && testStr.indexOf(".CSV") == -1){
				alert("Document "+document.getElementById("uploadfile").value+" must be a CSV file (with a .csv extension)");
				return false;
			}
			return true;
		}

		function displayErrors(clickObj){
			clickObj.style.display='none';
			document.getElementById('errordiv').style.display = 'block';
		}
	</script>
</head>
<body>
	<?php
    include_once(__DIR__ . '/../../header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<?php
		if($pid) {
            echo '<a href="' . $GLOBALS['CLIENT_ROOT'] . '/projects/index.php?pid=' . $pid . '">';
        }
		echo '<a href="../checklist.php?cl='.$clid.'&pid='.$pid.'">Return to Checklist</a> &gt;&gt; ';
		?>
		<a href="checklistloader.php?clid=<?php echo $clid.'&pid='.$pid; ?>"><b>Checklists Loader</b></a>
	</div>
	<div id="innertext">
		<h2>
			<a href="<?php echo $GLOBALS['CLIENT_ROOT']. '/checklists/checklist.php?cl=' .$clid.'&pid='.$pid; ?>">
				<?php echo $clMeta['name']; ?>
			</a>
		</h2>
		<div style="margin:10px;">
			<b>Authors:</b> <?php echo $clMeta['authors']; ?>
		</div>
		<?php
			if($isEditor){
				if($action === 'Upload Checklist'){
					?>
					<div style='margin:10px;'>
						<ul>
							<li>Loading checklist...</li>
							<?php
							$cnt = $clLoaderManager->uploadCsvList();
							$statusStr = $clLoaderManager->getErrorStr();
							if(!$cnt && $statusStr){
								echo '<div style="margin:20px;font-weight:bold;">';
								echo '<div style="color:red;">'.$statusStr.'</div>';
								echo '<div><a href="checklistloader.php?clid='.$clid.'&pid='.$pid.'">Return to Loader</a> and make sure the input file matches requirements within instructions</div>';
								echo '</div>';
								exit;
							}
							$probCnt = count($clLoaderManager->getProblemTaxa());
							$errorArr = $clLoaderManager->getErrorArr();
							?>
							<li>Upload status...</li>
							<li style="margin-left:10px;">Taxa successfully loaded: <?php echo $cnt; ?></li>
							<li style="margin-left:10px;">Problematic Taxa: <?php echo $probCnt.($probCnt?' (see below)':''); ?></li>
							<li style="margin-left:10px;">General errors: <?php echo count($errorArr); ?></li>
							<li style="margin-left:10px;">Upload Complete! <a href="../checklist.php?cl=<?php echo $clid.'&pid='.$pid; ?>">Proceed to Checklists</a></li>
						</ul>
						<?php
						if($probCnt){
							echo '<fieldset>';
							echo '<legend><b>Problematic Taxa Resolution</b></legend>';
							$clLoaderManager->resolveProblemTaxa();
							echo '</fieldset>';
						}
						if($errorArr){
							?>
							<fieldset style="padding:20px;">
								<legend><b>General Errors</b></legend>
								<a href="#" onclick="displayErrors(this);return false;"><b>Display <?php echo count($errorArr); ?> general errors</b></a>
								<div id="errordiv" style="display:none">
									<ol style="margin-left:15px;">
										<?php
										foreach($errorArr as $errStr){
											echo '<li>'.$errStr.'</li>';
										}
										?>
									</ol>
								</div>
							</fieldset>
							<?php
						}
						?>
					</div>
					<?php
				}
				else{
					?>
					<form enctype="multipart/form-data" action="checklistloader.php" method="post" onsubmit="return validateUploadForm();">
						<fieldset style="padding:15px;width:800px;">
							<legend><b>Checklist Upload Form</b></legend>
							<div style="font-weight:bold;">
								Checklist File:
								<input id="uploadfile" name="uploadfile" type="file" size="45" />
							</div>
							<div style="margin-top:10px;">
								<div>Must be a CSV text file with the first row containing the following columns. Note that Excel spreadsheets can be saved as a CSV file.</div>
								<ul>
									<li>sciname (required)</li>
									<li>family (optional)</li>
									<li>habitat (optional)</li>
									<li>abundance (optional)</li>
									<li>notes (optional)</li>
									<li>internalnotes (optional) - displayed only to editors</li>
									<li>source (optional)</li>

								</ul>
							</div>
							<div style="margin:25px;">
								<input id="clloadsubmit" name="action" type="submit" value="Upload Checklist" />
								<input type="hidden" name="clid" value="<?php echo $clid; ?>" />
							</div>
						</fieldset>
					</form>
				<?php
				}
			}
			else{
				echo '<h2>You appear not to have rights to edit this checklist. If you think this is in error, contact an administrator</h2>';
			}
		?>
	</div>
	<?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
