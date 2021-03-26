<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/ImageImport.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$action = array_key_exists('action',$_POST)?$_POST['action']: '';
$ulFileName = array_key_exists('ulfilename',$_POST)?$_POST['ulfilename']: '';

$isEditor = false;
if($GLOBALS['IS_ADMIN']){
	$isEditor = true;
}

$importManager = new ImageImport();

$fieldMap = array();
if($isEditor){
	if($action){
		$importManager->setUploadFile($ulFileName);
	}
	if(array_key_exists('sf',$_POST)){
		$targetFields = $_POST['tf'];
 		$sourceFields = $_POST['sf'];
		for($x = 0, $xMax = count($targetFields); $x< $xMax; $x++){
			if($targetFields[$x] !== '' && $sourceFields[$x]) {
                $fieldMap[$sourceFields[$x]] = $targetFields[$x];
            }
		}
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Image Loader</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');

?>
<div class="navpath">
	<b><a href="../../index.php">Homepage</a></b> &gt;&gt; 
	<b>Image Importer</b>
</div>

<h1>Image Importer</h1>
<div  id="innertext">
	<div style="margin-bottom:30px;">
		
	</div> 
	<div>
		<form name="uploadform" action="imageloader.php" method="post" enctype="multipart/form-data">
			<fieldset style="width:90%;">
				<legend style="font-weight:bold;font-size:120%;">Image Upload Form</legend>
				<div style="margin:10px;">
					This tool is designed to aid collection managers in batch importing image files 
					that are defined within a comma delimited text file (CSV). The only two required fields are 
					the image url. If scientific name is null, script will attempt to extract taxon name from image file name. 
					The image urls must represent the full path to the image, or consist of the file names with base path 
					defined within the ingestion form.  
					Other optional fields include: photographer, caption, locality, sourceUrl, anatomy, 
					notes, collection identifier, owner, copyright, sortSequence.   
					Internal fields can include photographerUid, occid, or tid. 
				</div>
				<input type="hidden" name="ulfilename" value="<?php echo $importManager->getUploadFileName();?>" />
				<?php 
				if(!$importManager->getUploadFileName()){ 
					?>
					<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />
					<div>
						<div>
							<b>Upload File:</b>
							<div style="margin:10px;">
								<input name="uploadfile" type="file" size="40" />
							</div>
						</div>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Analyze Input File" />
						</div>
					</div>
					<?php 
				}
				else{ 
					?>
					<div id="mdiv" style="margin:15px;">
						<table style="border:1px solid black;border-spacing: 2px;">
							<tr>
								<th>
									Source Field
								</th>
								<th>
									Target Field
								</th>
							</tr>
							<?php
							$sArr = $importManager->getSourceArr();
							$tArr = $importManager->getTargetArr();
							foreach($sArr as $sKey => $sField){
								?>
								<tr>
									<td style='padding:2px;'>
										<?php echo $sField; ?>
										<input type="hidden" name="sf[]" value="<?php echo $sField; ?>" />
									</td>
									<td>
										<select name="tf[]" style="background:<?php echo (array_key_exists(strtolower($sField),$fieldMap)?'':'yellow');?>">
											<option value="">Select Target</option>
											<?php 
											$sField = strtolower($sField);
											$symbIndex = '';
											if(array_key_exists($sField,$fieldMap)){
												$symbIndex = $fieldMap[$sField];
											}
											if($symbIndex === ''){
												$transStr = $importManager->getTranslation($sField);
												if($transStr) {
                                                    $sField = $transStr;
                                                }
											}
											$selStr = '';
											echo "<option value='unmapped' ".($symbIndex === 'unmapped' ?'SELECTED':''). '>Leave Field Unmapped</option>';
											echo '<option value="">-------------------------</option>';
											foreach($tArr as $tKey => $tField){
												if($selStr !== 0){
													if($symbIndex === '' && $sField === strtolower($tField)){
														$selStr = 'SELECTED';
													}
													elseif(is_numeric($symbIndex) && $symbIndex === $tKey){
														$selStr = 'SELECTED';
													}
												}
												echo '<option value="'.$tKey.'" '.($selStr?:'').'>'.$tField."</option>\n";
												if($selStr) {
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
							* Fields in yellow are not yet mapped or verified
						</div>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Verify Mapping" /><br/>
							<fieldset>
								<legend>Large Image</legend>
								<input name="lgimg" type="radio" value="0" /> Leave blank<br/>
								<input name="lgimg" type="radio" value="1" /> Map to remote images<br/>
								<input name="lgimg" type="radio" value="2" /> Import to local storage
							</fieldset>
							Base Path: <input name="basepath" type="text" value="" /><br/>
							<input name="action" type="submit" value="Upload Images" />
						</div>
					</div>
					<?php 
				} 
				?>
			</fieldset>
		</form>
	</div>
</div>
<?php  
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
