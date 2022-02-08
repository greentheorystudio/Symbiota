<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/GlossaryUpload.php');
include_once(__DIR__ . '/../classes/GlossaryManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' . $GLOBALS['CLIENT_ROOT'] . '/glossary/glossaryloader.php');
}

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
$ulFileName = array_key_exists('ulfilename',$_REQUEST)?$_REQUEST['ulfilename']: '';
$ulOverride = array_key_exists('uloverride',$_REQUEST)?$_REQUEST['uloverride']: '';
$batchTaxaStr = array_key_exists('batchtid',$_REQUEST)?$_REQUEST['batchtid']: '';
$batchSource = array_key_exists('batchsources',$_REQUEST)?str_replace("'", '&#39;',$_REQUEST['batchsources']): '';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$loaderManager = new GlossaryUpload();
$glosManager = new GlossaryManager();

$status = '';
$fieldMap = array();
$languageArr = array();
$tidStr = '';
$ulSource = '';

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
		$languageArr = json_decode($_REQUEST['ullanguages'], true);
		$tidStr = $_REQUEST['ultids'];
		$ulSource = (array_key_exists('ulsources',$_REQUEST)? json_decode($_REQUEST['ulsources'], true) :'');
	}
	if($action === 'downloadcsv'){
		$loaderManager->exportUploadTerms();
		exit;
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Glossary Term Loader</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
	<script src="../js/jquery.manifest.js" type="text/javascript"></script>
	<script src="../js/jquery.marcopolo.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/symb/glossary.index.js?ver=20220113"></script>
	<script type="text/javascript">
        const taxArr = [];

        $(document).ready(function() {
			$('#batchtaxagroup').manifest({
				marcoPolo: {
					url: 'rpc/taxalist.php',
					data: {
						t: 'batch'
					},
					formatItem: function (data) {
						return data.name;
					}
				},
				required: true
			});
			
			$('#batchtaxagroup').on('marcopoloselect', function (event, data) {
				taxArr.push({name:data.name,id:data.id});
			});
			
			$('#batchtaxagroup').on('manifestremove',function (event, data){
				for (let i = 0; i < taxArr.length; i++) {
					if(taxArr[i].name === data){
						taxArr.splice(i,1);
					}
				}
			});
		});
		
		function verifyUploadForm(f){
            const inputValue = f.uploadfile.value;
            const taxavals = $('#batchtaxagroup').manifest('values');
            if(inputValue.indexOf(".csv") === -1 && inputValue.indexOf(".CSV") === -1 && inputValue.indexOf(".zip") === -1){
				alert("Upload file must be a .csv or .zip file.");
				return false;
			}
			if(taxavals.length < 1){
				alert("Please enter at least one taxonomic group.");
				return false;
			}
			if(taxArr.length > 0){
                const tids = [];
                for(let i = 0; i < taxArr.length; i++){
					tids.push(taxArr[i].id);
				}
                document.getElementById('batchtid').value = tids.join();
			}
			return true;
		}
	</script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div class="navpath">
    <a href="../index.php">Home</a> &gt;&gt;
    <a href="index.php"><b>Glossary Management</b></a> &gt;&gt;
    <b>Glossary Batch Loader</b>
</div>
<?php

if($isEditor){
	?>
	<div id="innertext">
		<h1>Glossary Term Batch Loader</h1>
		<div style="margin:30px;">
			<div style="margin-bottom:30px;">
				This page allows a Taxonomic Administrator to batch upload glossary data files. 
			</div> 
			<?php 
			if($action === 'Map Input File' || $action === 'Verify Mapping'){
				?>
				<form name="mapform" action="glossaryloader.php" method="post">
					<fieldset style="width:90%;">
						<legend style="font-weight:bold;font-size:120%;">Term Upload Form</legend>
						<div style="margin:10px;">
						</div>
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
							$fArr = $loaderManager->getFieldArr();
							$sArr = $fArr['source'];
							$tArr = $fArr['target'];
							asort($tArr);
							foreach($sArr as $sField){
								?>
								<tr>
									<td style='padding:2px;'>
										<?php echo $sField; ?>
										<input type="hidden" name="sf[]" value="<?php echo $sField; ?>" />
									</td>
									<td>
										<select name="tf[]" style="background:yellow">
											<option value="">Field Unmapped</option>
											<option value="">-------------------------</option>
											<?php 
											$selStr = '';
											echo "<option value='unmapped' ".$selStr. '>Leave Field Unmapped</option>';
											if($selStr){
												$selStr = 0;
											}
											foreach($tArr as $k => $tField){
												if($selStr !== 0 && $tField === $sField){
													$selStr = 'SELECTED';
												}
												elseif($selStr !== 0 && $tField === $sField.'_term'){
													$selStr = 'SELECTED';
												}
												echo '<option value="'.$tField.'" '.($selStr?:'').'>'.$tField."</option>\n";
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
						<div style="margin:10px;">
							<input type="submit" name="action" value="Upload Terms" />
							<input type="hidden" name="ultids" value='<?php echo $batchTaxaStr;?>' />
							<input type="hidden" name="ulsources" value='<?php echo json_encode($batchSource);?>' />
							<input type="hidden" name="ullanguages" value='<?php echo $fArr['languages'];?>' />
							<input type="hidden" name="ulfilename" value="<?php echo $loaderManager->getFileName();?>" />
						</div>
					</fieldset>
				</form>
				<?php 
			}
			elseif($action === 'Upload Terms'){
				echo '<ul>';
                $loaderManager->loadFile($fieldMap,$languageArr,$tidStr,$ulSource);
                $loaderManager->cleanUpload($tidStr);
				$loaderManager->analysisUpload();
				echo '</ul>';
				?>
				<form name="transferform" action="glossaryloader.php" method="post">
					<fieldset style="width:450px;">
						<legend style="font-weight:bold;font-size:120%;">Transfer Terms To Central Table</legend>
						<div style="margin:10px;">
							Review upload statistics below before activating. Use the download option to review and/or adjust for reload if necessary.  
						</div>
						<div style="margin:10px;">
							<?php 
							$statArr = $loaderManager->getStatArr();
							if($statArr){
								if(isset($statArr['upload'])) {
                                    echo '<u>Terms uploaded</u>: <b>' . $statArr['upload'] . '</b><br/>';
                                }
								echo '<u>Total terms</u>: <b>'.$statArr['total'].'</b><br/>';
								echo '<u>Terms already in database</u>: <b>'.($statArr['exist'] ?? 0).'</b><br/>';
								echo '<u>New terms</u>: <b>'.($statArr['new'] ?? 0).'</b><br/>';
							}
							else{
								echo 'Upload statistics are unavailable';
							}
							?>
						</div>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Activate Terms" />
						</div>
						<div style="float:right;margin:10px;">
							<a href="glossaryloader.php?action=downloadcsv" >Download CSV Terms File</a>
						</div>
					</fieldset>
				</form>
				<?php 
			}
			elseif($action === 'Activate Terms'){
				echo '<ul>';
				$loaderManager->transferUpload();
				echo '<li>Terms upload appears to have been successful.</li>';
				echo "<li>Go to <a href='index.php'>Glossary Search</a> page to search for a loaded name.</li>";
				echo '</ul>';
			}
			else{
				?>
				<div>
					<form name="uploadform" action="glossaryloader.php" method="post" enctype="multipart/form-data" onsubmit="return verifyUploadForm(this)">
						<fieldset style="width:90%;">
							<legend style="font-weight:bold;font-size:120%;">Term Upload Form</legend>
							<div style="margin:10px;">
								Flat structured, CSV (comma delimited) text files can be uploaded here. 
								Please specify the taxonomic groups for which the terms are related. 
								For each language in the CSV file, name the column with the terms as the language the terms are in,
								and then name all columns related to that term as the language underscore and then the column name
								(ex. English, English_definition, Spanish, Spanish_Definition, etc.). Columns can be added for the definition,
								author, translator, source, notes, and an online resource url.
								Synonyms can be added by naming the column the language underscore synonym (ex. English_synonym).
								A source can be added for all of the terms by filling in the Enter Sources box below. 
								Please do not use spaces in the column names or file names.
								If the file upload step fails without displaying an error message, it is possible that the 
								file size exceeds the file upload limits set within your PHP installation (see your php configuration file).
							</div>
							<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />
							<div>
								<div class="overrideopt">
									<b>Enter Taxonomic Groups:</b>
									<div style="margin:10px;">
										<input type="text" name="batchtaxagroup" id="batchtaxagroup" style="width:550px;" value="" onchange="" autocomplete="off" />
										<input name="batchtid" id="batchtid" type="hidden" value="" />
									</div>
								</div>
							</div>
							<div>
								<div class="overrideopt">
									<b>Enter Sources:</b>
									<div style="margin:10px;">
										<textarea name="batchsources" id="batchsources" maxlength="1000" rows="10" style="width:450px;height:40px;resize:vertical;" ></textarea>
									</div>
								</div>
							</div>
							<div>
								<div class="overrideopt">
									<b>Upload File:</b>
									<div style="margin:10px;">
										<input id="genuploadfile" name="uploadfile" type="file" size="40" />
									</div>
								</div>
								<div style="margin:10px;">
									<input type="submit" name="action" value="Map Input File" />
								</div>
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
		You do not have permissions to batch upload glossary data
	</div>
	<?php 
}

include(__DIR__ . '/../footer.php');
?>
</body>
</html>
