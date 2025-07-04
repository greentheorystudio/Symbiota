<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/GlossaryManager.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$glossId = array_key_exists('glossid',$_REQUEST)?(int)$_REQUEST['glossid']:0;
$glimgId = array_key_exists('glimgid',$_REQUEST)?(int)$_REQUEST['glimgid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$tidStr = '';
$hasImages = false;

$glosManager = new GlossaryManager();
$glosManager->setGlossId($glossId);

$closeWindow = false;
$statusStr = '';
if($formSubmit){
	if($formSubmit === 'Edit Term'){
		if(!$glosManager->editTerm($_POST)){
			$statusStr = $glosManager->getErrorStr();
		}
	}
	elseif($formSubmit === 'Submit New Image'){
		$statusStr = $glosManager->addImage();
	}
	elseif($formSubmit === 'Save Image Edits'){
		$statusStr = $glosManager->editImageData($_POST);
	}
	elseif($formSubmit === 'Delete Image'){
		$statusStr = $glosManager->deleteImage($glimgId);
	}
	elseif($formSubmit === 'Link Translation'){
		if(!$glosManager->linkTranslation($_POST['relglossid'])){
			$statusStr = $glosManager->getErrorStr();
		}
		$glosManager->setGlossId($glossId);
	}
	elseif($formSubmit === 'Link Related Term'){
		if(!$glosManager->linkRelation($_POST['relglossid'],$_POST['relationship'])){
			$statusStr = $glosManager->getErrorStr();
		}
		$glosManager->setGlossId($glossId);
	}
	elseif($formSubmit === 'Remove Translation'){
		if(!$glosManager->removeRelation($_POST['gltlinkid'],$_POST['relglossid'])){
			$statusStr = $glosManager->getErrorStr();
		}
		$glosManager->setGlossId($glossId);
	}
	elseif($formSubmit === 'Remove Synonym'){
		if(!$glosManager->removeRelation($_POST['gltlinkid'],$_POST['relglossid'])){
			$statusStr = $glosManager->getErrorStr();
		}
		$glosManager->setGlossId($glossId);
	}
	elseif($formSubmit === 'Unlink Related Term'){
		if(!$glosManager->removeRelation($_POST['gltlinkid'])){
			$statusStr = $glosManager->getErrorStr();
		}
	}
	elseif($formSubmit === 'Add Taxa Group'){
		if(!$glosManager->addGroupTaxaLink($_POST['tid'])){
			$statusStr = $glosManager->getErrorStr();
		}
	}
	elseif($formSubmit === 'Delete Taxa Group'){
		if(!$glosManager->deleteGroupTaxaLink($_POST['tid'])){
			$statusStr = $glosManager->getErrorStr();
		}
	}
	elseif($formSubmit === 'Delete Term'){
		if($glosManager->deleteTerm()){
			$glossId = 0;
			$closeWindow = true;
		}
		else{
			$statusStr = $glosManager->getErrorStr();
		}
	}
}

if($glossId){
	$termArr = $glosManager->getTermArr();
	$taxaArr = $glosManager->getTermTaxaArr();
	$termImgArr = $glosManager->getImgArr();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Glossary Term Editor</title>
    <meta name="description" content="Glossary term editor for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
	<style>
		#tabs a{
			outline-color: transparent;
			font-weight: normal;
		}
	</style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/glossary.index.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
	<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
			<?php 
			if($closeWindow){
				echo 'window.opener.searchform.submit();';
				echo 'self.close();';
			}
			?>
		});

		function verifyTermEditForm(f){
			if(!f.term.value || !f.language.value){
				alert("Term and language must have a value");
				return false;
			}
	
			if(f.definition.value.length > 1998){
				if(!confirm("Warning, your definition is close to maximum size limit and may be cut off. Are you sure the definition is completely entered?")) return false;
			}
			return true;
		}
	
		function verifyRelLinkForm(f){
			if(!f.relglossid.value){
				alert("Please select a related term");
				return false;
			}
			return true;
		}
	
		function verifyTransLinkForm(f){
			if(!f.relglossid.value){
				alert("Please select a translation term");
				return false;
			}
			return true;
		}
	
		function verifyNewImageForm(){
			if(!document.getElementById("imgfile").files[0] && document.getElementById("imgurl").value === ""){
				alert("Please either upload an image or enter the url of an existing image.");
				return false;
			}
			return true;
		}
	
		function verifyImageEditForm(){
			if(document.getElementById("editurl").value === ""){
				document.getElementById("editurl").value = document.getElementById("oldurl").value;
				alert("Please enter a url for the image to save.");
				return false;
			}
			return true;
		}
	</script>
</head>
<body>
	<div id="breadcrumbs">
        <a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php'>Home</a> &gt;&gt;
        <a href='index.php?language=<?php echo $glosManager->getTermLanguage(); ?>'> <b>Glossary Management</b></a>
    </div>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
		<?php 
		if($glossId && $isEditor){
			if($statusStr){
				?>
				<div style="margin:15px;color:<?php echo (strpos($statusStr, 'SUCCESS') !== false?'green':'red'); ?>;">
					<?php echo $statusStr; ?>
				</div>
				<?php 
			}
			?>
			<div id="tabs" style="margin:0;">
				<ul>
					<li><a href="#termdetaildiv">Details</a></li>
					<li><a href="#termrelateddiv">Related Terms</a></li>
					<li><a href="#termtransdiv">Translations</a></li>
					<li><a href="#termimagediv">Images</a></li>
					<li><a href="#termadmindiv">Admin</a></li>
				</ul>
				
				<div id="termdetaildiv" style="">
					<div id="termdetails" style="overflow:auto;">
						<form name="termeditform" id="termeditform" action="termdetails.php" method="post" onsubmit="return verifyTermEditForm(this);">
							<div style="clear:both;padding-top:4px;float:left;">
								<div style="float:left;">
									<b>Term: </b>
								</div>
								<div style="float:left;margin-left:10px;">
									<input type="text" name="term" id="term" maxlength="150" style="width:400px;" value="<?php echo $termArr['term']; ?>" onchange="" title="" />
								</div>
							</div>
							<div style="clear:both;padding-top:4px;float:left;width:100%;">
								<div style="float:left;">
									<b>Definition: </b>
								</div>
								<div style="float:left;margin-left:10px;width:95%;">
									<textarea name="definition" id="definition" rows="10" maxlength="2000" style="width:100%;height:200px;" ><?php echo $termArr['definition']; ?></textarea>
								</div>
							</div>
							<div style="clear:both;padding-top:4px;float:left;">
								<div style="float:left;">
									<b>Language: </b>
								</div>
								<div style="float:left;margin-left:10px;">
									<select id="langSelect" name="language">
										<?php 
										$langArr = $glosManager->getLanguageArr('all');
										foreach($langArr as $langStr ){
											echo '<option '.($glosManager->getTermLanguage() === $langStr?'SELECTED':'').'>'.$langStr.'</option>';
										}
										?>
									</select> 
									<a href="#" onclick="toggle('addLangDiv');return false;"><i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i></a>&nbsp;&nbsp;
								</div>
								<div id="addLangDiv" style="float:left;display:none">
									<input name="newlang" type="text" maxlength="45" style="width:200px;" /> 
									<button onclick="addNewLang(this.form);return false;">Add language</button>
								</div>
							</div>
							<div style="clear:both;padding-top:4px;float:left;">
								<div style="float:left;">
									<b>Author: </b>
								</div>
								<div style="float:left;margin-left:10px;">
									<input name="author" type="text" maxlength="250" style="width:500px;" value="<?php echo $termArr['author']; ?>" onchange="" title="" />
								</div>
							</div>
							<div style="clear:both;padding-top:4px;float:left;">
								<div style="float:left;">
									<b>Translator: </b>
								</div>
								<div style="float:left;margin-left:10px;">
									<input name="translator" type="text" maxlength="250" style="width:500px;" value="<?php echo $termArr['translator']; ?>" onchange="" title="" />
								</div>
							</div>
							<div style="clear:both;padding-top:4px;float:left;">
								<div style="float:left;">
									<b>Source: </b>
								</div>
								<div style="float:left;margin-left:10px;">
									<input name="source" type="text" maxlength="1000" style="width:500px;" value="<?php echo $termArr['source']; ?>" />
								</div>
							</div>
							<div style="clear:both;padding-top:4px;float:left;">
								<div style="float:left;">
									<b>Notes: </b>
								</div>
								<div style="float:left;margin-left:10px;">
									<input name="notes" type="text" maxlength="250" style="width:380px;" value="<?php echo $termArr['notes']; ?>" />
								</div>
							</div>
							<div style="clear:both;padding-top:4px;float:left;">
								<div style="float:left;">
									<b>Resource URL: </b>
								</div>
								<div style="float:left;margin-left:10px;">
									<input name="resourceurl" type="text" maxlength="600" style="width:600px;" value="<?php echo $termArr['resourceurl']; ?>" />
								</div>
							</div>
							<div style="clear:both;padding:20px;">
								<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
								<input id="origterm" type="hidden" value="<?php echo $termArr['term']; ?>" />
								<input id="origlang" type="hidden" value="<?php echo $glosManager->getTermLanguage(); ?>" />
								<button name="formsubmit" type="submit" value="Edit Term">Save Edits</button>
							</div>
						</form>
						<div style="clear:both;height:15px;"></div>
						<fieldset style='clear:both;padding:8px;margin-bottom:10px;'>
							<legend><b>Taxonomic Groups</b></legend>
							<div style="clear:both;" onclick="" title="Taxa Groups">
								<ul>
									<?php
									foreach($taxaArr as $taxId => $sciname){
										echo '<li><form name="taxadelform" id="'.$sciname.'" action="termdetails.php" style="margin-top:0;margin-bottom:0;" method="post">';
										echo $sciname;
										echo '<button style="margin:0;padding:2px;" title="Delete Taxa Group" type="submit"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></button>';
										echo '<input name="glossid" type="hidden" value="'.$glossId.'" />';
										echo '<input name="tid" type="hidden" value="'.$taxId.'" />';
										echo '<input name="formsubmit" type="hidden" value="Delete Taxa Group" />';
										echo '</form></li>';
									}
									?>
								</ul>
							</div>
							<div style="clear:both;margin:10px">
								<form name="taxaaddform" id="taxaaddform" action="termdetails.php" method="post" onsubmit="">
									<div style="float:left;">
										<b>Add Taxonomic Group: </b>
									</div>
									<div style="float:left;margin-left:10px;">
										<input type="text" name="taxagroup" id="taxagroup" maxlength="45" style="width:250px;" value="" onchange="" title="" />
										<input name="tid" id="tid" type="hidden" value="" />
									</div>
									<div style="float:left;margin-left:10px;">
										<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
										<button name="formsubmit" type="submit" value="Add Taxa Group">Add Group</button>
									</div>
								</form>
							</div>
						</fieldset>
					</div>
				</div>
				<div id="termrelateddiv">
					<?php 
					$synonymArr = $glosManager->getSynonyms();
					$otherRelationshipsArr = $glosManager->getOtherRelatedTerms();
					?>
					<div style="margin:10px;float:right;cursor:pointer;<?php echo (!$synonymArr||$otherRelationshipsArr?'display:none;':''); ?>" onclick="toggle('addsyndiv');" title="Add a New Synonym">
                        <i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
					</div>
					<div id="addsyndiv" style="margin-bottom:10px;<?php echo ($synonymArr||$otherRelationshipsArr?'display:none;':''); ?>;">
						<form name="relnewform" action="termdetails.php#termrelateddiv" method="post" onsubmit="return verifyRelLinkForm(this);">
							<fieldset style="padding:25px">
								<legend><b>Link A Related Term</b></legend>
								<div style="clear:both;padding-top:4px;">
									<div style="">
										<b>This term</b> 
										<select name="relationship">
											<option value="synonym">is Synonym Of</option>
											<option value="subClassOf">is Subclass of... (Child of...)</option>
											<option value="superClassOf">is Superclass of... (Parent of...)</option>
											<option value="hasPart">has Part...</option>
											<option value="partOf">is Part of...</option>
										</select> 
										<select name="relglossid">
											<option value=''>Select Related Term</option>
											<option value=''>------------------------</option>
											<?php 
											$relList = $glosManager->getTermList('related',$glosManager->getTermLanguage());
											unset($relList[$glossId]);
											$relList = array_diff_key($relList, $synonymArr, $otherRelationshipsArr);
											foreach($relList as $relId => $relName){
												echo '<option value="'.$relId.'">'.$relName.'</option>';
											}
											?>
										</select> 
										<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
										<button name="formsubmit" type="submit" value="Link Related Term">Link Related Term</button>
									</div>
								</div>
								<div style="clear:both;"></div>
								<div style="clear:both;margin:30px 10px;">
									<div style="margin:3px">Or add a <a href="addterm.php?relationship=synonym&relglossid=<?php echo $glossId.'&rellanguage='.$glosManager->getTermLanguage(); ?>">New Synonym</a> that is not yet in the system</div>
								</div>
							</fieldset>
						</form>
					</div>
					<?php
					if($synonymArr){
						?>
						<fieldset style='clear:both;padding:15px;margin-bottom:10px;'>
							<legend><b>Synonyms</b></legend>
							<?php 
							foreach($synonymArr as $synGlossId => $synArr){
								?>
								<div style="margin:15px;padding:10px;border:1px solid gray">
									<?php
									$disableRemoveSyn = false;
									$removeSynTitle = 'Remove Synonym';
									if($synGlossId === $glosManager->getGlossGroupId()){
										$removeSynTitle = 'Root term cannot be removed! Instead, go to root term and then remove other relations.';
										$disableRemoveSyn = true;
									}
									?>
									<div style="float:right;margin:5px;" title="<?php echo $removeSynTitle; ?>">
										<form name="syndelform" action="termdetails.php#termrelateddiv" method="post" onsubmit="<?php echo ($disableRemoveSyn?'return false':''); ?>">
											<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
											<input name="gltlinkid" type="hidden" value="<?php echo $synArr['gltlinkid']; ?>" />
											<input name="relglossid" type="hidden" value="<?php echo $synGlossId; ?>" />
                                            <input name="formsubmit" type="hidden" value="Remove Synonym" />
											<button style="margin:0;padding:2px;" type="submit" <?php echo ($disableRemoveSyn?'disabled':''); ?>>
                                                <i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
                                            </button>
										</form>
									</div>
									<div style="float:right;margin:5px;cursor:pointer;" title="Edit Term">
										<a href="termdetails.php?glossid=<?php echo $synGlossId; ?>">
                                            <i style="height:15px;width:15px;" class="far fa-edit"></i>
										</a>
									</div>
									<div style='' >
										<b>Term:</b> 
										<?php echo $synArr['term']; ?>
									</div>
									<div style='margin-top:8px;' >
										<b>Definition:</b> 
										<?php echo $synArr['definition']; ?>
									</div>
									<div style='margin-top:8px;' >
										<b>Source:</b> 
										<?php echo $synArr['source']; ?>
									</div>
								</div>
								<?php
							}
							?>
						</fieldset>	
						<?php
					}
					if($otherRelationshipsArr){
						?>
						<fieldset style='clear:both;padding:15px;margin-bottom:10px;'>
							<legend><b>Other Relationships</b></legend>
							<?php 
							foreach($otherRelationshipsArr as $relType => $relTypeArr){
								$relStr = 'is related to';
								if($relType === 'partOf') {
                                    $relStr = 'is part of';
                                }
								elseif($relType === 'hasPart') {
                                    $relStr = 'has part';
                                }
								elseif($relType === 'subClassOf') {
                                    $relStr = 'is subclass of (child of)';
                                }
								elseif($relType === 'superClassOf') {
                                    $relStr = 'is superclass of (parent of)';
                                }
								foreach($relTypeArr as $relGlossId => $relArr){
									$disableRemoveRel = false;
									$removeRelTitle = 'Unlink Related Term';
									if($relGlossId === $glosManager->getGlossGroupId()){
										$removeRelTitle = 'Root term cannot be removed! Instead, go to root term and then remove other relations.';
										$disableRemoveRel = true;
									}
									?>
									<div style="margin:15px;padding:10px;border:1px solid gray">
										<div style="float:right;margin:5px;" title="<?php echo $removeRelTitle; ?>">
											<form name="reldelform" action="termdetails.php#termrelateddiv" method="post" onsubmit="<?php echo ($disableRemoveRel?'return false':''); ?>">
												<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
												<input name="gltlinkid" type="hidden" value="<?php echo $relArr['gltlinkid']; ?>" />
												<input name="relglossid" type="hidden" value="<?php echo $relGlossId; ?>" />
                                                <input name="formsubmit" type="hidden" value="Unlink Related Term" />
												<button style="margin:0;padding:2px;" type="submit" <?php echo ($disableRemoveRel?'disabled':''); ?>>
                                                    <i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
                                                </button>
											</form>
										</div>
										<div style="float:right;margin:5px;" title="Edit Term">
											<a href="termdetails.php?glossid=<?php echo $relGlossId; ?>">
                                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
											</a>
										</div>
										<div>
											Current term <?php echo $relStr.': <b>'.$relArr['term'].'</b>'; ?>
										</div>
										<div style='clear:both;margin-top:3px;' >
											<b>Definition:</b> 
											<?php echo $relArr['definition']; ?>
										</div>
									</div>
									<?php
								}
							}
							?>
						</fieldset>	
						<?php 
					}
					?>
				</div>
				<div id="termtransdiv" style="">
					<?php 
					$translationArr = $glosManager->getTranslations();
					?>
					<div style="margin:10px;float:right;cursor:pointer;<?php echo (!$translationArr?'display:none;':''); ?>" onclick="toggle('addtransdiv');" title="Add a New Translation">
                        <i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
					</div>
					<div id="addtransdiv" style="margin-bottom:10px;<?php echo ($translationArr?'display:none;':''); ?>;">
						<form name="translinkform" action="termdetails.php#termtransdiv" method="post" onsubmit="return verifyTransLinkForm(this);">
							<fieldset style="padding:25px">
								<legend><b>Link a Translation</b></legend>
								<div style="clear:both;padding-top:4px;float:left;">
									<div style="float:left;">
										<b>Link an existing term: </b>
									</div>
									<div style="float:left;margin-left:10px;">
										<select name="relglossid">
											<option value=''>Select Translation Term</option>
											<option value=''>------------------------</option>
											<?php 
											$transList = $glosManager->getTermList('translation',$glosManager->getTermLanguage());
											$transList = array_diff_key($transList, $translationArr);
											foreach($transList as $transId => $transName){
												echo '<option value="'.$transId.'">'.$transName.'</option>';
											}
											?>
										</select>
									</div>
									<div style="float:left;margin-left:30px;">
										<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
										<button name="formsubmit" type="submit" value="Link Translation">Link Translation</button>
									</div>
								</div>
								<div style="clear:both;"></div>
								<div style="clear:both;margin: 30px 10px;">
									Or add a <a href="addterm.php?relationship=translation&relglossid=<?php echo $glossId.'&rellanguage='.$glosManager->getTermLanguage(); ?>">New Translation</a> to this term
								</div>
							</fieldset>
						</form>
					</div>
					<div style="padding-top:15px">
						<?php
						if($translationArr){
							foreach($translationArr as $transGlossId => $transArr){
								?>
								<div style="width:95%;margin:15px;padding:10px;border:1px solid gray">
									<?php
									if($transArr['gltlinkid'] && $transGlossId !== $glosManager->getGlossGroupId()){
										?>
										<div style="float:right;margin:5px;" title="Remove Translation">
											<form name="transdelform" action="termdetails.php#termtransdiv" method="post">
												<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
												<input name="gltlinkid" type="hidden" value="<?php echo $transArr['gltlinkid']; ?>" />
												<input name="relglossid" type="hidden" value="<?php echo $transGlossId; ?>" />
                                                <input name="formsubmit" type="hidden" value="Remove Translation" />
												<button style="margin:0;padding:2px;" type="submit">
                                                    <i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
                                                </button>
											</form>
										</div>
										<?php 
									}
									?>
									<div style="float:right;margin:5px;" title="Edit Term Data">
										<a href="termdetails.php?glossid=<?php echo $transGlossId; ?>">
                                            <i style="height:15px;width:15px;" class="far fa-edit"></i>
										</a>
									</div>
									<div>
										<b>Term:</b> 
										<?php echo $transArr['term']; ?>
									</div>
									<div style='margin-top:8px;' >
										<b>Definition:</b> 
										<?php echo $transArr['definition']; ?>
									</div>
									<div style='margin-top:8px;' >
										<b>Language:</b> 
										<?php echo $transArr['language']; ?>
									</div>
									<div style='margin-top:8px;' >
										<b>Source:</b> 
										<?php echo $transArr['source']; ?>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
				<div id="termimagediv" style="min-height:300px;">
					<div id="imagediv" style="">
						<div style="margin:10px;float:right;cursor:pointer;<?php echo (!$termImgArr?'display:none;':''); ?>" onclick="toggle('addimgdiv');" title="Add a New Image">
                            <i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
						</div>
						<div id="addimgdiv" style="<?php echo ($termImgArr?'display:none;':''); ?>;">
							<form name="imgnewform" action="termdetails.php#termimagediv" method="post" enctype="multipart/form-data" onsubmit="return verifyNewImageForm();">
								<fieldset style="padding:15px">
									<legend><b>Add a New Image</b></legend>
									<div style='padding:15px;border:1px solid yellow;background-color:#FFFF99;'>
										<div class="targetdiv" style="display:block;">
											<div style="font-weight:bold;margin-bottom:5px;">
												Select an image file located on your computer that you want to upload:
											</div>
											<div style="height:10px;float:right;text-decoration:underline;font-weight:bold;">
												<a href="#" onclick="toggle('targetdiv');return false;">Enter URL</a>
											</div>
											<div>
												<input name='imgfile' id='imgfile' type='file' size='70'/>
											</div>
										</div>
										<div class="targetdiv" style="display:none;">
											<div style="float:right;text-decoration:underline;font-weight:bold;">
												<a href="#" onclick="toggle('targetdiv');return false;">
													Upload Local Image
												</a>
											</div>
											<div style="margin-bottom:10px;">
												Enter a URL to an image already located on a web server. 
											</div>
											<div>
												<b>Image URL:</b><br/> 
												<input type='text' name='imgurl' id='imgurl' size='70'/>
											</div>
										</div>
									</div>
									<div style="clear:both;padding-top:4px;float:left;">
										<div style="float:left;">
											<b>Created By:</b>
										</div>
										<div style="float:left;margin-left:10px;">
											<textarea name="createdBy" id="createdBy" rows="10" style="width:380px;height:50px;resize:vertical;" ></textarea>
										</div>
									</div>
									<div style="clear:both;padding-top:4px;float:left;">
										<div style="float:left;">
											<b>Structures:</b>
										</div>
										<div style="float:left;margin-left:10px;">
											<textarea name="structures" id="structures" rows="10" style="width:380px;height:50px;resize:vertical;" ></textarea>
										</div>
									</div>
									<div style="clear:both;padding-top:4px;float:left;">
										<div style="float:left;">
											<b>Notes:</b> 
										</div>
										<div style="float:left;margin-left:10px;">
											<textarea name="notes" id="notes" rows="10" style="width:380px;height:70px;resize:vertical;" ></textarea>
										</div>
									</div>
									<div style="clear:both;padding-top:8px;float:right;">
										<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
										<input type="submit" name="formsubmit" value="Submit New Image" />
									</div>
								</fieldset>
							</form>
						</div>
						<div style="clear:both;">
							<?php
							if($termImgArr){
								foreach($termImgArr as $imgId => $imgArr){
									$termImage = false;
									if($imgArr['glossid'] === $glossId){
										$termImage = true;
										$hasImages = true;
									}
									?>
									<fieldset style="margin-top:10px;">
										<div style="float:right;cursor:pointer;" onclick="toggle('img<?php echo $imgId; ?>editdiv');" title="Edit Image MetaData">
                                            <i style="height:15px;width:15px;" class="far fa-edit"></i>
										</div>
										<div style="float:left;">
											<?php
											$imgUrl = $imgArr['url'];
											$displayUrl = $imgUrl;
											?>
											<a href="<?php echo $imgUrl;?>" target="_blank">
												<img src="<?php echo $displayUrl;?>" style="width:250px;" title="<?php echo $imgArr['structures']; ?>" />
											</a>
										</div>
										<div style="float:left;margin-left:10px;padding:10px;width:350px;">
											<div style="">
												<?php
												if($imgArr['createdBy']){
													?>
													<div style="overflow:hidden;">
														<b>Created By:</b> 
														<?php echo wordwrap($imgArr['createdBy'], 150, "<br />\n"); ?>
													</div>
													<?php
												}
												if($imgArr['structures']){
													?>
													<div style="overflow:hidden;">
														<b>Structures:</b> 
														<?php echo wordwrap($imgArr['structures'], 150, "<br />\n"); ?>
													</div>
													<?php
												}
												if($imgArr['notes']){
													?>
													<div style="overflow:hidden;margin-top:8px;">
														<b>Notes:</b> 
														<?php echo wordwrap($imgArr['notes'], 150, "<br />\n"); ?>
													</div>
													<?php
												}
												?>
											</div>
										</div>
										<div id="img<?php echo $imgId; ?>editdiv" style="display:none;clear:both;">
											<form name="img<?php echo $imgId; ?>editform" action="termdetails.php" method="post" onsubmit="return verifyImageEditForm();">
												<fieldset style="">
													<legend><b>Edit Image Data</b></legend>
													<div style="clear:both;">
														<div style="float:left;">
															<b>Created By:</b>
														</div>
														<div style="float:left;margin-left:10px;">
															<textarea name="createdBy" id="createdBy" rows="10" style="width:380px;height:50px;resize:vertical;" ><?php echo $imgArr['createdBy']; ?></textarea>
														</div>
													</div>
													<div style="clear:both;">
														<div style="float:left;">
															<b>Structures:</b>
														</div>
														<div style="float:left;margin-left:10px;">
															<textarea name="structures" id="structures" rows="10" style="width:380px;height:50px;resize:vertical;" ><?php echo $imgArr['structures']; ?></textarea>
														</div>
													</div>
													<div style="clear:both;padding-top:10px;">
														<div style="float:left;">
															<b>Notes:</b> 
														</div>
														<div style="float:left;margin-left:10px;">
															<textarea name="notes" id="notes" rows="10" style="width:380px;height:70px;resize:vertical;" ><?php echo $imgArr['notes']; ?></textarea>
														</div>
													</div>
													<div style="clear:both;">
														<?php
														if($termImage){
															?>
															<div style="padding-top:8px;float:left;">
																<input type="submit" name="formsubmit" onclick="return confirm('Are you sure you want to permanently delete this image?');" value="Delete Image" />
															</div>
															<?php
														}
														?>
														<div style="padding-top:8px;float:right;">
															<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
															<input type="hidden" name="glimgid" value="<?php echo $imgId; ?>" />
															<input type="submit" name="formsubmit" value="Save Image Edits" />
														</div>
													</div>
												</fieldset>
											</form>
										</div>
									</fieldset>
									<?php 
								}
							}
							?>
						</div>
					</div>
				</div>
				
				<div id="termadmindiv" style="">
					<form name="deltermform" action="termdetails.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this term?')">
						<fieldset style="width:350px;margin:20px;padding:20px;">
							<legend><b>Delete Term</b></legend>
							<?php
							if($hasImages){
								echo '<div style="font-weight:bold;margin-bottom:15px;">';
								echo 'Term cannot be deleted until all linked images are deleted.';
								echo '</div>';
							}
							?>
							<input name="formsubmit" type="submit" value="Delete Term" <?php echo ($hasImages?'DISABLED':''); ?> />
							<input name="glossid" type="hidden" value="<?php echo $glossId; ?>" />
						</fieldset>
					</form>
				</div>
			</div>
			<?php 
		}
		else{
			echo '<h2>Permissions or data error, please contact system administrator</h2>';
		}
		?>
	</div>
    <?php
    include_once(__DIR__ . '/../config/footer-includes.php');
    ?>
</body>
</html>
