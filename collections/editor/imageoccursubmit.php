<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceEditorImages.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid  = (int)$_REQUEST['collid'];
$action = array_key_exists('action',$_POST)?htmlspecialchars($_POST['action']): '';

$occurManager = new OccurrenceEditorImages();
$solrManager = new SOLRManager();
$occurManager->setCollid($collid);
$collMap = $occurManager->getCollMap();

$statusStr = '';
$isEditor = 0;
if($collid){
	if($GLOBALS['IS_ADMIN']){
		$isEditor = 1;
	}
	elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
		$isEditor = 1;
	}
	elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
		$isEditor = 1;
	}
}
if($isEditor && $action === 'Submit Occurrence') {
    if($occurManager->addImageOccurrence($_POST)){
        $occid = $occurManager->getOccid();
        if($GLOBALS['SOLR_MODE']) {
            $solrManager->updateSOLR();
        }
        if($occid) {
            $statusStr = 'New record has been created: <a href="occurrenceeditor.php?occid=' . $occid . '" target="_blank">' . $occid . '</a>';
        }
    }
    else{
        $statusStr = $occurManager->getErrorStr();
    }
}
if($collid && file_exists('../../config/occurVarColl'.$collid.'.php')){
	include(__DIR__ . '/../../config/occurVarColl'.$collid.'.php');
}
elseif(file_exists('../../config/occurVarDefault.php')){
	include(__DIR__ . '/../../config/occurVarDefault.php');
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Create New Record From Image</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/external/jquery-ui.css?ver=20220720" type="text/css" rel="stylesheet" />
	<script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/collections.imageoccursubmit.js?ver=20221025" type="text/javascript"></script>
	<script src="../../js/shared.js?ver=20220809" type="text/javascript"></script>
	<script type="text/javascript">
	function validateImgOccurForm(f){
		if(f.imgfile.value === "" && f.imgurl.value === ""){
			alert("Please select an image file to upload or enter a remote URL to link");
			return false;
		}
		else{
			if(f.imgfile.value !== ""){
                const fName = f.imgfile.value.toLowerCase();
                if(fName.indexOf(".jpg") === -1 && fName.indexOf(".jpeg") === -1 && fName.indexOf(".gif") === -1 && fName.indexOf(".png") === -1){
					alert("Image file must be a JPG, GIF, or PNG");
					return false;
				}
			} 
			else if(f.imgurl.value !== ""){
                const fileName = f.imgurl.value;
                if(fileName.substring(0,4).toLowerCase() !== 'http'){
					alert("Image path must be a URL ("+fileName.substring(0,4).toLowerCase()+")");
					return false
				}
				$.ajax({
					type: "POST",
					url: "../../api/images/getImageMime.php",
					async: false,
					data: { url: fileName }
				}).success(function( retStr ) {
					if(retStr === "image/jpeg" || retStr === "image/gif" || retStr === "image/png"){
						return true;
					}
					else{
						alert("Image file must be a JPG, GIF, or PNG (type = "+retStr+")");
						return false;
					}
				});
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
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
		<b>Create New Record From Image</b>
	</div>
	<div id="innertext">
		<h1><?php echo $collMap['collectionname']; ?></h1>
		<?php 
		if($statusStr){
			echo '<div style="margin:15px;color:'.(stripos($statusStr,'error') !== false?'red':'green').';">'.$statusStr.'</div>';
		}
		if($isEditor){
			?>
			<form id='imgoccurform' name='imgoccurform' action='imageoccursubmit.php' method='post' enctype='multipart/form-data' onsubmit="return validateImgOccurForm(this)">
				<fieldset style="padding:15px;">
					<legend><b>Manual Occurrence Image Upload</b></legend>
					<div class="targetdiv">
						<div>
							<input name='imgfile' type='file' size='70' />
						</div>
						<div id="newimagediv"></div>
						<div style="margin:10px 0;">
							* Uploading web-ready images recommended. Upload image size can not be greater than 10MB
						</div>
					</div>
					<div class="targetdiv" style="display:none;">
						<div style="margin-bottom:10px;">
							Enter a URL to an image already located on a web server. 
							If the image is larger than a typical web image, the url will be saved as the large version 
							and a basic web derivative will be created. 
						</div>
						<div>
							<b>Image URL:</b><br/> 
							<input type='text' name='imgurl' size='70' />
						</div>
						<div>
							<input type="checkbox" name="copytoserver" value="1" <?php echo (isset($_POST['copytoserver'])&&$_POST['copytoserver']?'checked':''); ?> /> 
							Copy large image to server (if left unchecked, source URL will serve as large version)
						</div>
					</div>
					<div style="float:right;text-decoration:underline;font-weight:bold;">
						<div class="targetdiv">
							<a href="#" onclick="toggle('targetdiv');return false;">Enter URL</a>
						</div>
						<div class="targetdiv" style="display:none;">
							<a href="#" onclick="toggle('targetdiv');return false;">Upload Local Image</a>
						</div>
					</div>
					<div>
						<input type="checkbox" name="nolgimage" value="1" <?php echo (isset($_POST['nolgimage'])&&$_POST['nolgimage']?'checked':''); ?> /> 
						Do not map large version of image (when applicable) 
					</div>
				</fieldset>
				<fieldset style="padding:15px;">
					<legend><b>Skeletal Data</b></legend>
					<div style="margin:3px;">
						<b>Catalog Number:</b> 
						<input name="catalognumber" type="text" onchange="<?php echo ((!defined('CATNUMDUPECHECK') || CATNUMDUPECHECK)?'searchDupesCatalogNumber(this.form,true);':''); ?>" />
					</div>
					<div style="margin:3px;">
						<b>Scientific Name:</b> 
						<input id="sciname" name="sciname" type="text" value="<?php echo ($_POST['sciname'] ?? ''); ?>" style="width:300px"/>
						<input name="scientificnameauthorship" type="text" value="<?php echo ($_POST['scientificnameauthorship'] ?? ''); ?>" /><br/>
						<input type="hidden" id="tidinterpreted" name="tidinterpreted" value="<?php echo ($_POST['tidinterpreted'] ?? ''); ?>" />
						<b>Family:</b> <input name="family" type="text" value="<?php echo ($_POST['family'] ?? ''); ?>" />
					</div>
					<div> 
						<div style="float:left;margin:3px;">
							<b>Country:</b><br/> 
							<input id="country" name="country" type="text" value="<?php echo ($_POST['country'] ?? ''); ?>" />
						</div> 
						<div style="float:left;margin:3px;">
							<b>State/Province:</b><br/>
							<input id="state" name="stateprovince" type="text" value="<?php echo ($_POST['stateprovince'] ?? ''); ?>" />
						</div> 
						<div style="float:left;margin:3px;">
							<b>County:</b><br/>
							<input id="county" name="county" type="text" value="<?php echo ($_POST['county'] ?? ''); ?>" />
						</div> 
					</div>
				</fieldset>
				<div style="margin:10px;clear:both;">
					<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
					<input type="submit" name="action" value="Submit Occurrence" />
					<input type="reset" name="reset" value="Reset Form" />
				</div>
			</form>
			<?php 
		}
		else{
			echo 'You are not authorized to submit to an observation. ';
			echo '<br/><b>Please contact an administrator to obtain the necessary permissions.</b> ';
		}
		?>
	</div>
<?php 	
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
