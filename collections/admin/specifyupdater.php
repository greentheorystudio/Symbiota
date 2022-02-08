<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpecifyUpdateManager.php');
include_once(__DIR__ . '/../../classes/SpecifyDwcArchiverOccurrence.php');
include_once(__DIR__ . '/../../classes/SpecUploadBase.php');
include_once(__DIR__ . '/../../classes/SpecifySpecUploadDwca.php');

header("Content-Type: text/html; charset=".$GLOBALS['CHARSET']);
ini_set('max_execution_time', 3600);

$collId = array_key_exists("collid",$_REQUEST)?$_REQUEST["collid"]:0;
$uploadType = array_key_exists("uploadtype",$_REQUEST)?$_REQUEST["uploadtype"]:6;
$filename = array_key_exists("filename",$_REQUEST)?$_REQUEST["filename"]:6;
$uspid = array_key_exists("uspid",$_REQUEST)?$_REQUEST["uspid"]:0;
$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$autoMap = array_key_exists("automap",$_POST)?true:false;
$ulPath = array_key_exists("ulpath",$_REQUEST)?$_REQUEST["ulpath"]:"";
$importIdent = array_key_exists("importident",$_REQUEST)?true:false;
$importImage = array_key_exists("importimage",$_REQUEST)?true:false;
$finalTransfer = array_key_exists("finaltransfer",$_REQUEST)?$_REQUEST["finaltransfer"]:0;
$dbpk = array_key_exists("dbpk",$_REQUEST)?$_REQUEST["dbpk"]:'';

if(!is_numeric($collId)) $collId = 0;
if(!is_numeric($uploadType)) $uploadType = 0;
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if($autoMap !== true) $autoMap = false;
if($importIdent !== true) $importIdent = false;
if($autoMap !== true) $autoMap = false;
if(!is_numeric($finalTransfer)) $finalTransfer = 0;
if($dbpk) $dbpk = htmlspecialchars($dbpk);

$spdwcaManager = new SpecifyDwcArchiverOccurrence();
$spuManager = new SpecifyUpdateManager();
$duManager = new SpecUploadBase();
$duManager = new SpecifySpecUploadDwca();
$duManager->setBaseFolderName($ulPath);
$duManager->setIncludeIdentificationHistory($importIdent);
$duManager->setIncludeImages($importImage);

$collArr = array();
$DWCAUPLOAD = 6;

if(strpos($uspid,'-')){
	$tok = explode('-',$uspid);
	$uspid = $tok[0];
	$uploadType = $tok[1];
}

$duManager->setBaseFolderName($ulPath);
$duManager->setIncludeIdentificationHistory($importIdent);
$duManager->setIncludeImages($importImage);
$spdwcaManager->setIncludeDets($importIdent);
$spdwcaManager->setIncludeImgs($importImage);
$uspid = $spuManager->getUspid($collId);
$duManager->setCollId($collId);
$duManager->setUspid($uspid);
$duManager->setUploadType($uploadType);
$duManager->readUploadParameters();

if($action == 'Add Profile'){
	$spuManager->addUploadProfile($collId);
}

//Grab field mapping, if mapping form was submitted
if(array_key_exists("sf",$_POST)){
	if($action == "Delete Field Mapping" || $action == "Reset Field Mapping"){
		$statusStr = $duManager->deleteFieldMap();
	}
	else{
		//Set field map for occurrences using mapping form
 		$targetFields = $_POST["tf"];
 		$sourceFields = $_POST["sf"];
 		$fieldMap = Array();
		for($x = 0;$x<count($targetFields);$x++){
			if($targetFields[$x]){
				$tField = $targetFields[$x];
				if($tField == 'unmapped') $tField .= '-'.$x;
				$fieldMap[$tField]["field"] = $sourceFields[$x];
			}
		}
		//Set Source PK
		if($dbpk) $fieldMap["dbpk"]["field"] = $dbpk;
 		$duManager->setFieldMap($fieldMap);
		
 		//Set field map for identification history
		if(array_key_exists("ID-sf",$_POST)){
	 		$targetIdFields = $_POST["ID-tf"];
	 		$sourceIdFields = $_POST["ID-sf"];
	 		$fieldIdMap = Array();
			for($x = 0;$x<count($targetIdFields);$x++){
				if($targetIdFields[$x]){
					$tIdField = $targetIdFields[$x];
					if($tIdField == 'unmapped') $tIdField .= '-'.$x;
					$fieldIdMap[$tIdField]["field"] = $sourceIdFields[$x];
				}
			}
 			$duManager->setIdentFieldMap($fieldIdMap);
		}
 		//Set field map for image history
		if(array_key_exists("IM-sf",$_POST)){
	 		$targetImFields = $_POST["IM-tf"];
	 		$sourceImFields = $_POST["IM-sf"];
	 		$fieldImMap = Array();
			for($x = 0;$x<count($targetImFields);$x++){
				if($targetImFields[$x]){
					$tImField = $targetImFields[$x];
					if($tImField == 'unmapped') $tImField .= '-'.$x;
					$fieldImMap[$tImField]["field"] = $sourceImFields[$x];
				}
			}
 			$duManager->setImageFieldMap($fieldImMap);
		}
	}
	if($action == "Save Mapping"){
		$statusStr = $duManager->saveFieldMap();
	}
}
$duManager->loadFieldMap();
$collArr = $spuManager->getSpecifyCollectionList();
$fullCollArr = $spuManager->getCollectionList();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $GLOBALS['CHARSET']; ?>">
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Specify Collection Updater</title>
		<link href="../../css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/main.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<script src="../../js/symb/shared.js" type="text/javascript"></script>
		<script type="text/javascript">
			function verifyMappingForm(f){
				var sfArr = [];
				var idSfArr = [];
				var imSfArr = [];
				var tfArr = [];
				var idTfArr = [];
				var imTfArr = [];
				var lacksCatalogNumber = true;
				var possibleMappingErr = false; 
				for(var i=0;i<f.length;i++){
					var obj = f.elements[i];
					if(obj.name == "sf[]"){
						if(sfArr.indexOf(obj.value) > -1){
							alert("ERROR: Source field names must be unique (duplicate field: "+obj.value+")");
							return false;
						}
						sfArr[sfArr.length] = obj.value;
						//Test value to make sure source file isn't missing the header and making directly to file record
						if(!possibleMappingErr){
							if(isNumeric(obj.value)){
								possibleMappingErr = true;
							} 
							if(obj.value.length > 7){
								if(isNumeric(obj.value.substring(5))){ 
									possibleMappingErr = true;
								}
								else if(obj.value.slice(-5) == "aceae" || obj.value.slice(-4) == "idae"){
									possibleMappingErr = true;
								}
							}
						}
					}
					else if(obj.name == "ID-sf[]"){
						if(f.importident.value == "1"){
							if(idSfArr.indexOf(obj.value) > -1){
								alert("ERROR: Source field names must be unique (Identification: "+obj.value+")");
								return false;
							}
							idSfArr[idSfArr.length] = obj.value;
						}
					}
					else if(obj.name == "IM-sf[]"){
						if(f.importimage.value == "1"){
							if(imSfArr.indexOf(obj.value) > -1){
								alert("ERROR: Source field names must be unique (Image: "+obj.value+")");
								return false;
							}
							imSfArr[imSfArr.length] = obj.value;
						}
					}
					else if(obj.value != "" && obj.value != "unmapped"){
						if(obj.name == "tf[]"){
							if(tfArr.indexOf(obj.value) > -1){
								alert("ERROR: Can't map to the same target field more than once ("+obj.value+")");
								return false;
							}
							tfArr[tfArr.length] = obj.value;
						}
						else if(obj.name == "ID-tf[]"){
							if(f.importident.value == "1"){
								if(idTfArr.indexOf(obj.value) > -1){
									alert("ERROR: Can't map to the same target field more than once (Identification: "+obj.value+")");
									return false;
								}
								idTfArr[idTfArr.length] = obj.value;
							}
						}
						else if(obj.name == "IM-tf[]"){
							if(f.importimage.value == "1"){
								if(imTfArr.indexOf(obj.value) > -1){
									alert("ERROR: Can't map to the same target field more than once (Images: "+obj.value+")");
									return false;
								}
								imTfArr[imTfArr.length] = obj.value;
							}
						}
					}
					if(lacksCatalogNumber && obj.name == "tf[]"){
						//Is skeletal file upload
						if(obj.value == "catalognumber"){
							lacksCatalogNumber = false;
						}
					}
				}
				if(lacksCatalogNumber && f.uploadtype.value == 7){
					//Skeletal records require catalog number to be mapped
					alert("ERROR: Catalog Number is required for Skeletal File Uploads");
					return false;
				}
				if(possibleMappingErr){
					return confirm("Does the first row of the input file contain the column names? It appears that you may be mapping directly to the first row of active data rather than a header row. If so, the first row of data will be lost and some columns might be skipped. Select OK to proceed, or cancel to abort");
				}
				return true;
			}

			function pkChanged(selObj){
				document.getElementById('pkdiv').style.display='block';
				document.getElementById('mdiv').style.display='none';
				document.getElementById('uldiv').style.display='none';
			}
		</script>
	</head>
	<body>
		<?php
		//$displayLeftMenu = (isset($collections_misc_specifyupdateMenu)?$collections_misc_specifyupdateMenu:false);
		include(__DIR__ . '/../../header.php');
		?>
		<div id="innertext">
			<?php
			if($GLOBALS['IS_ADMIN']){
				echo "<h1>Specify Data Upload Module</h1>";
				if($action == 'Initialize Upload'){
					$spdwcaManager->setTargetPath($GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1)=='/'?'':'/').'collections/datasets/dwc/');
					$spdwcaManager->setVerbose(1);
					$spdwcaManager->setLimitToGuids(true);
					$spdwcaManager->setSpecifyWhere($collId);
					echo "<div style='font-weight:bold;font-size:120%'>Initializing Specify Data:</div>";
					echo "<ul style='margin:10px;font-weight:bold;'>";
					$archive = $spdwcaManager->batchCreateDwca($collId);
					$ulPath = $duManager->uploadFile();
					echo "</ul>";
					//Data has been uploaded and it's a DWCA upload type
					if($duManager->analyzeUpload()){
						$metaArr = $duManager->getMetaArr();
						if(isset($metaArr['occur'])){
							?>
							<form name="dwcauploadform" action="specifyupdater.php" method="post" onsubmit="return verifyMappingForm(this)">
								<fieldset style="width:95%;">
									<legend style="font-weight:bold;font-size:120%;"><?php echo $duManager->getTitle();?></legend>
									<div style="margin:10px;">
										<b>Source Unique Identifier / Primary Key (required): </b>
										<?php
										$dbpk = $duManager->getDbpk();
										?>
										<select name="dbpk" onchange="pkChanged(this);">
											<option value="id">core id</option>
											<option value="catalognumber" <?php if($dbpk == 'catalognumber') echo 'SELECTED'; ?>>catalogNumber</option>
											<option value="occurrenceid" <?php if($dbpk == 'occurrenceid') echo 'SELECTED'; ?>>occurrenceId</option>
										</select>
										<div style="margin-left:10px;">
											*Change ONLY if you are sure that a field other than the Core Id will better serve as the primary specimen identifier
										</div> 
										<div id="pkdiv" style="margin:5px 0px 0px 20px;display:none";>
											<input type="submit" name="action" value="Save Primary Key" />
										</div>
										<div style="margin:10px;">
											<div>
												<input name="importspec" value="1" type="checkbox" checked /> 
												Import Occurrence Records (<a href="#" onclick="toggle('dwcaOccurDiv');return false;">view mapping</a>)
											</div>
											<div id="dwcaOccurDiv" style="display:none;margin:20px;">
												<?php $duManager->echoFieldMapTable(true,'occur'); ?>
												<div>
													* Mappings that are not yet saved are displayed in Yellow
												</div>
												<div style="margin:10px;">
													<input type="submit" name="action" value="Reset Field Mapping" />
													<input type="submit" name="action" value="Save Mapping" />
												</div>
											</div>
											<div>
												<input name="importident" value="1" type="checkbox" <?php echo (isset($metaArr['ident'])?'checked':'disabled') ?> /> 
												Import Identification History 
												<?php 
												if(isset($metaArr['ident'])){
													echo '(<a href="#" onclick="toggle(\'dwcaIdentDiv\');return false;">view mapping</a>)';
													?>
													<div id="dwcaIdentDiv" style="display:none;margin:20px;">
														<?php $duManager->echoFieldMapTable(true,'ident'); ?>
														<div>
															* Mappings that are not yet saved are displayed in Yellow
														</div>
														<div style="margin:10px;">
															<input type="submit" name="action" value="Save Mapping" />
														</div>
													</div>
													<?php 
												}
												else{
													echo '(not present in DwC-Archive)';
												}
												?>
												
											</div>
											<div>
												<input name="importimage" value="1" type="checkbox" <?php echo (isset($metaArr['image'])?'checked':'disabled') ?> /> 
												Import Images 
												<?php 
												if(isset($metaArr['image'])){
													echo '(<a href="#" onclick="toggle(\'dwcaImgDiv\');return false;">view mapping</a>)';
													?>
													<div id="dwcaImgDiv" style="display:none;margin:20px;">
														<?php $duManager->echoFieldMapTable(true,'image'); ?>
														<div>
															* Mappings that are not yet saved are displayed in Yellow
														</div>
														<div style="margin:10px;">
															<input type="submit" name="action" value="Save Mapping" />
														</div>
														
													</div>
													<?php 
												}
												else{
													echo '(not present in DwC-Archive)';
												}
												?>
											</div>
											<div>
												<div style="margin:10px;">
													<input type="submit" name="action" value="Start Upload" />
													<input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
													<input type="hidden" name="collid" value="<?php echo $collId;?>" />
													<input type="hidden" name="uploadtype" value="<?php echo $uploadType;?>" />
													<input type="hidden" name="ulpath" value="<?php echo $ulPath;?>" />
													<input type="hidden" name="filename" value="<?php echo $archive;?>" />
												</div>
											</div>
										</div>
									</div>
								</fieldset>
							</form>
							<?php
						}
					}
					else{
						if($duManager->getErrorStr()){
							echo '<div style="font-weight:bold;">'.$duManager->getErrorStr().'</div>';
						}
						else{
							echo '<div style="font-weight:bold;">Unknown error analyzing upload</div>';
						}
					}
				}
				elseif($action == 'Start Upload'){
					$tempArchPath = $GLOBALS['SERVER_ROOT'].'collections/datasets/dwc/'.$filename;
					echo "<div style='font-weight:bold;font-size:120%'>Upload Status:</div>";
					echo "<ul style='margin:10px;font-weight:bold;'>";
					$duManager->uploadData($finalTransfer);
					//$spdwcaManager->deleteTempArchive($tempArchPath);
					echo "</ul>";
					if($duManager->getTransferCount() && !$finalTransfer){
						?>
						<fieldset style="margin:15px;">
							<legend><b>Final transfer</b></legend>
							<div style="margin:5px;">
								<?php 
								$reportArr = $duManager->getTransferReport();
								echo '<div>Occurrences pending transfer: '.$reportArr['occur'];
								if($reportArr['occur']){
									echo ' <a href="uploadviewer.php?collid='.$collId.'" target="_blank"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadcsv.php?collid='.$collId.'" target="_self"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
								echo '<div style="margin-left:15px;">';
								echo '<div>Records to be updated: ';
								echo $reportArr['update'];
								if($reportArr['update']){
									echo ' <a href="uploadviewer.php?collid='.$collId.'&searchvar=occid:ISNOTNULL" target="_blank"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadcsv.php?collid='.$collId.'&searchvar=occid:ISNOTNULL" target="_self"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
								echo '<div>New records: ';
								echo $reportArr['new'];
								if($reportArr['new']){ 
									echo ' <a href="uploadviewer.php?collid='.$collId.'&searchvar=occid:ISNULL" target="_blank"><img src="../../images/list.png" style="width:12px;" /></a>';
									echo ' <a href="uploadcsv.php?collid='.$collId.'&searchvar=occid:ISNULL" target="_self"><img src="../../images/dl.png" style="width:12px;" /></a>';
								}
								echo '</div>';
								if(isset($reportArr['matchappend']) && $reportArr['matchappend']){
									echo '<div>Records matching on catalog number that will be appended : ';
									echo $reportArr['matchappend'];
									if($reportArr['matchappend']){ 
										echo ' <a href="uploadviewer.php?collid='.$collId.'&searchvar=matchappend" target="_blank"><img src="../../images/list.png" style="width:12px;" /></a>';
										echo ' <a href="uploadcsv.php?collid='.$collId.'&searchvar=matchappend" target="_self"><img src="../../images/dl.png" style="width:12px;" /></a>';
									}
									echo '</div>';
									echo '<div style="margin-left:15px;"><span style="color:orange;">WARNING:</span> This will result in records with duplicate catalog numbers</div>';
								}
								if(isset($reportArr['sync']) && $reportArr['sync']){
									echo '<div>Records that will be syncronized with central database: ';
									echo $reportArr['sync'];
									if($reportArr['sync']){  
										echo ' <a href="uploadviewer.php?collid='.$collId.'&searchvar=sync" target="_blank"><img src="../../images/list.png" style="width:12px;" /></a>';
										echo ' <a href="uploadcsv.php?collid='.$collId.'&searchvar=sync" target="_self"><img src="../../images/dl.png" style="width:12px;" /></a>';
									}
									echo '</div>';
									echo '<div style="margin-left:15px;">These are typically records that have been originally processed within the portal, exported and integrated into a local management database, and then reimported and synchronized with the portal records by matching on catalog number.</div>';
									echo '<div style="margin-left:15px;"><span style="color:orange;">WARNING:</span> Incoming records will replace portal records by matching on catalog numbers. Make sure incoming records are the most up to date!</div>';
								}
								if(isset($reportArr['exist']) && $reportArr['exist']){
									echo '<div>Previous loaded records not matching incoming records: ';
									echo $reportArr['exist'];
									if($reportArr['exist']){  
										echo ' <a href="uploadviewer.php?collid='.$collId.'&searchvar=exist" target="_blank"><img src="../../images/list.png" style="width:12px;" /></a>';
										echo ' <a href="uploadcsv.php?collid='.$collId.'&searchvar=exist" target="_self"><img src="../../images/dl.png" style="width:12px;" /></a>';
									}
									echo '</div>';
									echo '<div style="margin-left:15px;">';
									echo 'Note: If you are doing a partial upload, this is expected. ';
									echo 'If you are doing a full data refresh, these may be records that were deleted within your local database but not within the portal.';
									echo '</div>';
								}
								if(isset($reportArr['nulldbpk']) && $reportArr['nulldbpk']){
									echo '<div style="color:red;">Records that will be removed due to NULL Primary Identifier: ';
									echo $reportArr['nulldbpk'];
									if($reportArr['nulldbpk']){ 
										echo ' <a href="uploadviewer.php?collid='.$collId.'&searchvar=dbpk:ISNULL" target="_blank"><img src="../../images/list.png" style="width:12px;" /></a>';
										echo ' <a href="uploadcsv.php?collid='.$collId.'&searchvar=dbpk:ISNULL" target="_self"><img src="../../images/dl.png" style="width:12px;" /></a>';
									}
									echo '</div>';
								}
								if(isset($reportArr['dupdbpk']) && $reportArr['dupdbpk']){
									echo '<div style="color:red;">Records that will be removed due to DUPLICATE Primary Identifier: ';
									echo $reportArr['dupdbpk'];
									if($reportArr['dupdbpk']){  
										echo ' <a href="uploadviewer.php?collid='.$collId.'&searchvar=dupdbpk" target="_blank"><img src="../../images/list.png" style="width:12px;" /></a>';
										echo ' <a href="uploadcsv.php?collid='.$collId.'&searchvar=dupdbpk" target="_self"><img src="../../images/dl.png" style="width:12px;" /></a>';
									}
									echo '</div>';
								}
								echo '</div>';
								//Extensions
								if(isset($reportArr['ident'])){
									echo '<div>Identification histories pending transfer: '.$reportArr['ident'].'</div>';
								}
								if(isset($reportArr['image'])){
									echo '<div>Images pending transfer: '.$reportArr['image'].'</div>';
								}
								
								?>
							</div>
							<form name="finaltransferform" action="specifyupdater.php" method="post" style="margin-top:10px;" onsubmit="return confirm('Are you sure you want to transfer records from temporary table to central specimen table?');">
								<input type="hidden" name="collid" value="<?php echo $collId;?>" /> 
								<input type="hidden" name="uploadtype" value="<?php echo $uploadType;?>" />
								<input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
								<div style="margin:5px;"> 
									<input type="submit" name="action" value="Transfer Records to Central Specimen Table" />
								</div>
							</form>
						</fieldset>			
						<?php
					}
				}
				elseif($action == 'Transfer Records to Central Specimen Table' || $finalTransfer){
					echo '<ul>';
					$duManager->finalTransfer();
					echo '</ul>';
				}
				else{
					if($collArr){
						?>
						<form name="collselectform" action="specifyupdater.php" method="post" onsubmit="">
							<fieldset style="width:450px;">
								<legend style="font-weight:bold;font-size:120%;">Select Collection to Update</legend>
								<div style= "margin-top:10px;">
									<select name="collid" >
										<?php 
										foreach($collArr as $k => $v){
											echo '<option value="'.$k.'">'.$v.'</option>';
										}
										?>
									</select>
								</div>
								<!-- <input type="checkbox" name="importident" value="1" <?php echo ($importIdent?'CHECKED':''); ?> /> Include Determination History<br/> -->
								<input type="checkbox" name="importimage" value="1" <?php echo ($importImage?'CHECKED':''); ?> /> Include Image URLs<br/>
								<div style="float:right;" onclick="toggle('colladdform');return false;">
									<a href="#">Add Collection</a>
								</div>
								<div style="margin:10px;">
									<input type="submit" name="action" value="Initialize Upload" />
								</div>
								<?php
								?>
							</fieldset>
						</form>
						<?php
					}
					if($fullCollArr){
						?>
						<form name="colladdform" id="colladdform" style="display:<?php echo ($collArr?'none':'block'); ?>;" action="specifyupdater.php" method="post">
							<fieldset style="width:450px;">
								<legend style="font-weight:bold;font-size:120%;">Select Specify Collection to Add</legend>
								<div style= "margin-top:10px;">
									<select name="collid" >
										<?php 
										foreach($fullCollArr as $i => $c){
											echo '<option value="'.$i.'">'.$c.'</option>';
										}
										?>
									</select>
								</div>
								<div style="margin:10px;">
									<input type="submit" name="action" value="Add Profile" />
								</div>
								<?php
								?>
							</fieldset>
						</form>
						<?php
					}
				}
			}
			else{
				if(!$GLOBALS['SYMB_UID']){
					header("Location: ../../profile/index.php?refurl=../collections/admin/specifyupdater.php");
				}
				else{
					echo '<h2>You do not have permissions to update collections.</h2>';
				}
			}
			?>
		</div>
		<?php 
		include(__DIR__ . '/../../footer.php');
		?>
	</body>
</html>
