<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/TaxonomyUpload.php');
include_once(__DIR__ . '/../../classes/TaxonomyUtilities.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' . $GLOBALS['CLIENT_ROOT'] . '/taxa/admin/batchloader.php');
}
ini_set('max_execution_time', 7200);

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
$ulFileName = array_key_exists('ulfilename',$_REQUEST)?$_REQUEST['ulfilename']: '';
$ulOverride = array_key_exists('uloverride',$_REQUEST)?$_REQUEST['uloverride']: '';
$taxAuthId = (array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$loaderManager = new TaxonomyUpload();
$taxaUtilities = new TaxonomyUtilities();
$loaderManager->setTaxaAuthId($taxAuthId);

$status = '';
$fieldMap = array();
if($isEditor){
	if($ulFileName){
		$loaderManager->setFileName($ulFileName);
	}
	else{
		$loaderManager->setUploadFile($ulOverride);
	}

	if(array_key_exists('sf',$_REQUEST)){
		$targetFields = $_REQUEST['tf'];
 		$sourceFields = $_REQUEST['sf'];
		for($x = 0, $xMax = count($targetFields); $x< $xMax; $x++){
			if($targetFields[$x] && $sourceFields[$x]) {
                $fieldMap[$sourceFields[$x]] = $targetFields[$x];
            }
		}
	}

	if($action === 'downloadcsv'){
		$loaderManager->exportUploadTaxa();
		exit;
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Loader</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <script src="../../js/symb/shared.js?ver=20210621" type="text/javascript"></script>
	<script type="text/javascript">
		function verifyUploadForm(f){
			let inputValue = f.uploadfile.value;
			if(inputValue === "") inputValue = f.uloverride.value;
			if(inputValue === ""){
				alert("Please enter a path value of the file you wish to upload");
				return false;
			}
			else{
				if(inputValue.indexOf(".csv") === -1 && inputValue.indexOf(".CSV") === -1 && inputValue.indexOf(".zip") === -1){
					alert("Upload file must be a CSV or ZIP file");
					return false;
				}
			}
			return true;
		}
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div class="navpath">
    <a href="../../index.php">Home</a> &gt;&gt;
    <a href="taxonomydisplay.php"><b>Taxonomic Tree Viewer</b></a> &gt;&gt;
    <a href="batchloader.php"><b>Taxa Batch Loader</b></a>
</div>
<?php

if($isEditor){
	?>
	<div id="innertext">
		<h1>Taxonomic Name Batch Loader</h1>
		<div style="margin:30px;">
			<div style="margin-bottom:30px;">
				This page allows a Taxonomic Administrator to batch upload taxonomic data files.
				See <a href="http://symbiota.org/docs/loading-taxonomic-data/">Symbiota Documentation</a>
				pages for more details on the Taxonomic Thesaurus layout.
			</div>
			<?php
			if($action === 'Map Input File' || $action === 'Verify Mapping'){
				?>
				<form name="mapform" action="batchloader.php" method="post">
					<fieldset style="width:90%;">
						<legend style="font-weight:bold;font-size:120%;">Taxa Upload Form</legend>
						<div style="margin:10px;">
						</div>
						<table style="border:1px solid black">
							<tr>
								<th>
									Source Field
								</th>
								<th>
									Target Field
								</th>
							</tr>
							<?php
							$sArr = $loaderManager->getSourceArr();
							$tArr = $loaderManager->getTargetArr();
							asort($tArr);
							foreach($sArr as $sField){
								?>
								<tr>
									<td style='padding:2px;'>
										<?php echo $sField; ?>
										<input type="hidden" name="sf[]" value="<?php echo $sField; ?>" />
									</td>
									<td>
										<select name="tf[]" style="background:<?php echo (array_key_exists($sField,$fieldMap)? '' : 'yellow');?>">
											<option value="">Field Unmapped</option>
											<option value="">-------------------------</option>
											<?php
											$mappedTarget = (array_key_exists($sField,$fieldMap)?$fieldMap[$sField]: '');
											$selStr = '';
											if($mappedTarget === 'unmapped') {
                                                $selStr = 'SELECTED';
                                            }
											echo "<option value='unmapped' ".$selStr. '>Leave Field Unmapped</option>';
											if($selStr){
												$selStr = 0;
											}
											foreach($tArr as $k => $tField){
												if($selStr !== 0 && (
												        ($mappedTarget && $mappedTarget == $tField) ||
                                                        ($tField == $sField && $tField !== 'sciname') ||
                                                        ($tField === 'scinameinput' && (strtolower($sField) === 'sciname' || strtolower($sField) === 'scientific name'))
                                                    )){
													$selStr = 'SELECTED';
												}
												echo '<option value="'.$k.'" '.($selStr?:'').'>'.$tField."</option>\n";
												if($selStr){
													$selStr = 0;
												}
											}
											?>
										</select>
									</td>
								</tr>
								<?php
							}
							?>
						</table>
						<div>
							* Fields in yellow have not yet been verified
						</div>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Verify Mapping" />
							<input type="submit" name="action" value="Upload Taxa" />
							<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>" />
							<input type="hidden" name="ulfilename" value="<?php echo $loaderManager->getFileName();?>" />
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action === 'Analyze Taxa' || strpos($action, 'Upload') === 0){
				echo '<ul>';
				if($action === 'Upload Taxa'){
					$loaderManager->loadFile($fieldMap);
					$loaderManager->cleanUpload();
				}
				elseif($action === 'Analyze Taxa'){
					$loaderManager->cleanUpload();
				}
				$reportArr = $loaderManager->analysisUpload();
				echo '</ul>';
				?>
				<form name="transferform" action="batchloader.php" method="post">
					<fieldset style="width:450px;">
						<legend style="font-weight:bold;font-size:120%;">Transfer Taxa To Central Table</legend>
						<div style="margin:10px;">
							Review upload statistics below before activating. Use the download option to review and/or adjust for reload if necessary.
						</div>
						<div style="margin:10px;">
							<?php
							$statArr = $loaderManager->getStatArr();
							if($statArr){
								if(isset($statArr['upload'])) {
                                    echo '<u>Taxa uploaded</u>: <b>' . $statArr['upload'] . '</b><br/>';
                                }
								echo '<u>Total taxa</u>: <b>'.$statArr['total'].'</b> (includes new parent taxa)<br/>';
								echo '<u>Taxa already in thesaurus</u>: <b>'.($statArr['exist'] ?? 0).'</b><br/>';
								echo '<u>New taxa</u>: <b>'.($statArr['new'] ?? 0).'</b><br/>';
								echo '<u>Accepted taxa</u>: <b>'.($statArr['accepted'] ?? 0).'</b><br/>';
								echo '<u>Non-accepted taxa</u>: <b>'.($statArr['nonaccepted'] ?? 0).'</b><br/>';
								if(isset($statArr['bad'])){
									?>
									<fieldset style="margin:15px;padding:15px;">
										<legend><b>Problematic taxa</b></legend>
										<div style="margin-bottom:10px">
											These taxa are marked as FAILED within the notes field and will not load until problems have been resolved.
											You may want to download the data (link below), fix the bad relationships, and then reload.
										</div>
										<?php
										foreach($statArr['bad'] as $msg => $cnt){
											echo '<div style="margin-left:10px"><u>'.$msg.'</u>: <b>'.$cnt.'</b></div>';
										}
										?>
									</fieldset>
									<?php
								}
							}
							else{
								echo 'Upload statistics are unavailable';
							}
							?>
						</div>
						<div style="margin:10px;">
							<input type="hidden" name="taxauthid" value="<?php echo $taxAuthId;?>" />
							<input type="submit" name="action" value="Activate Taxa" />
						</div>
						<div style="float:right;margin:10px;">
							<a href="batchloader.php?action=downloadcsv" target="_blank">Download CSV Taxa File</a>
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action === 'Activate Taxa'){
				echo '<ul>';
				$loaderManager->transferUpload();
                $taxaUtilities->buildHierarchyEnumTree($taxAuthId);
				echo '<li>Taxa upload appears to have been successful.</li>';
				echo "<li>Go to the <a href='taxonomydynamicdisplay.php'>Taxonomy Explorer</a> to search for a loaded name.</li>";
				echo '</ul>';
			}
			else{
				?>
				<div>
					<form name="uploadform" action="batchloader.php" method="post" enctype="multipart/form-data" onsubmit="return verifyUploadForm(this);">
						<fieldset style="width:90%;">
							<legend style="font-weight:bold;font-size:120%;">Taxa Upload Form</legend>
							<div style="margin:10px;">
								Flat structured, CSV (comma delimited) text files can be uploaded here.
								Scientific name is the only required field below genus rank.
								However, family, author, and rankid (as defined in taxonunits table) are always advised.
								For upper level taxa, parents and rankids need to be included in order to build the taxonomic hierarchy.
								Large data files can be compressed as a ZIP file before import.
								If the file upload step fails without displaying an error message, it is possible that the
								file size exceeds the file upload limits set within your PHP installation (see your php configuration file).
							</div>
							<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />
							<div>
								<div class="overrideopt">
									<b>Upload File:</b>
									<div style="margin:10px;">
										<input id="genuploadfile" name="uploadfile" type="file" size="40" />
									</div>
								</div>
								<div class="overrideopt" style="display:none;">
									<b>Full File Path:</b>
									<div style="margin:10px;">
										<input name="uloverride" type="text" size="50" /><br/>
										* This option is for manual upload of a data file.
										Enter full path to data file located on working server.
									</div>
								</div>
								<div style="margin:10px;">
									Target Thesaurus:
									<select name="taxauthid">
										<?php
										$taxonAuthArr = $loaderManager->getTaxAuthorityArr();
										foreach($taxonAuthArr as $k => $v){
											echo '<option value="'.$k.'" '.($k == $taxAuthId?'SELECTED':'').'>'.$v.'</option>'."\n";
										}
										?>
									</select>
								</div>
								<div style="margin:10px;">
									<input type="submit" name="action" value="Map Input File" />
								</div>
								<div style="float:right;" >
									<a href="#" onclick="toggle('overrideopt');return false;">Toggle Manual Upload Option</a>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<div>
					<form name="analyzeform" action="batchloader.php" method="post">
						<fieldset style="width:90%;">
							<legend style="font-weight:bold;font-size:120%;">Clean and Analyze</legend>
							<div style="margin:10px;">
								If taxa information was loaded into the UploadTaxa table using other means,
								one can use this form to clean and analyze taxa names in preparation to loading into the taxonomic tables (taxa, taxstatus).
							</div>
							<div style="margin:10px;">
								Target Thesaurus:
								<select name="taxauthid">
									<?php
									$taxonAuthArr = $loaderManager->getTaxAuthorityArr();
									foreach($taxonAuthArr as $k => $v){
										echo '<option value="'.$k.'" '.($k == $taxAuthId?'SELECTED':'').'>'.$v.'</option>'."\n";
									}
									?>
								</select>
							</div>
							<div style="margin:10px;">
								<input type="submit" name="action" value="Analyze Taxa" />
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
else{
	?>
	<div style='font-weight:bold;margin:30px;'>
		You do not have permissions to batch upload taxonomic data
	</div>
	<?php
}
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
