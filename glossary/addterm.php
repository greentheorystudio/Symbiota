<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/GlossaryManager.php');
include_once(__DIR__ . '/../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$relatedGlossId = array_key_exists('relglossid',$_REQUEST)?(int)$_REQUEST['relglossid']:0;
$taxaTid  = array_key_exists('taxatid',$_REQUEST)?(int)$_REQUEST['taxatid']:0;
$taxaName  = array_key_exists('taxaname',$_REQUEST)?$_REQUEST['taxaname']:'';
$relationship = array_key_exists('relationship',$_REQUEST)?$_REQUEST['relationship']:'';
$relatedLanguage = array_key_exists('rellanguage',$_REQUEST)?$_REQUEST['rellanguage']:'';
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

if(!$relatedLanguage){
	$relatedLanguage = $GLOBALS['DEFAULT_LANG'];
}
if($relatedLanguage === 'en') {
    $relatedLanguage = 'English';
}
if($relatedLanguage === 'es') {
    $relatedLanguage = 'Spanish';
}

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$glosManager = new GlossaryManager();

$closeWindow = false;
$statusStr = '';
if($isEditor && $formSubmit === 'Create Term') {
    if($glosManager->createTerm($_POST)){
        if(isset($_POST['tid']) && $_POST['tid']){
            header('Location: termdetails.php?glossid='.$glosManager->getGlossId());
        }
        elseif($relatedGlossId && isset($_POST['relation'])){
            if($_POST['relation'] === 'translation'){
                header('Location: termdetails.php?glossid='.$relatedGlossId.'#termtransdiv');
            }
            else{
                header('Location: termdetails.php?glossid='.$relatedGlossId.'#termrelateddiv');
            }
        }
        else{
            $closeWindow = true;
        }
    }
    else{
        $statusStr = $glosManager->getErrorStr();
    }
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Glossary - Add New Term</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <script src="../js/external/all.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/external/jquery.js"></script>
	<script type="text/javascript" src="../js/external/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/shared.js?ver=20221126"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php 
			if($closeWindow){
				echo 'window.opener.searchform.submit();';
				echo 'self.close();';
			}
			?>
		});

		function verifyNewTermForm(f){
			if(!f.term.value){
				alert("Please enter a value in the term field");
				return false;
			}

			if(f.definition.value.length > 1998){
				if(!confirm("Warning, your definition is close to maximum size limit and may be cut off. Are you sure the definition is completely entered?")) return false;
			}
			
			if(!f.language.value){
				alert("Please select a language");
				return false;
			}

            let tidValue = '';
            if(f.tid) tidValue = f.tid.value;
			if(!f.relglossid.value && !tidValue){
				alert("Please enter a taxonomic group or a related term to which new term will be linked");
				return false;
			}

			$.ajax({
				type: "POST",
				url: "../api/glossary/checkterm.php",
				data: { term: f.term.value, language: f.language.value, tid: tidValue, relglossid: f.relglossid.value }
			}).success(function( data ) {
				if(data === "1"){
					alert("Term already exists in database in that language and taxonomic group.");
				}
				else{
					f.submit();
				}
			});
			
			return false;
		}

	</script>
	<script src="../js/glossary.index.js?ver=20221115" type="text/javascript"></script>
</head>
<body>
	<div id="innertext">
		<?php 
		if($statusStr){
			?>
			<div style="margin:15px;color:red;">
				<?php echo $statusStr; ?>
			</div>
			<?php 
		}
		if($isEditor){
			?>
			<div id="newtermdiv" style="margin-bottom:10px;">
				<form name="newtermform" action="addterm.php" method="post" onsubmit="return verifyNewTermForm(this)">
					<fieldset>
						<legend><b>Add New Term</b></legend>
						<div style="clear:both;padding-top:4px;">
							<div style="float:left;">
								<b>Term: </b>
							</div>
							<div style="float:left;margin-left:10px;">
								<input type="text" name="term" id="term" maxlength="150" style="width:350px;" value="" onchange="" title="" />
							</div>
						</div>
						<div style="clear:both;padding-top:4px;width:100%;">
							<div style="float:left;">
								<b>Definition: </b>
							</div>
							<div style="float:left;margin-left:10px;width:95%;">
								<textarea name="definition" id="definition" rows="10" maxlength="2000" style="width:100%;height:200px;" ></textarea>
							</div>
						</div>
						<div style="clear:both;padding-top:4px;">
							<div style="float:left;">
								<b>Language: </b>
							</div>
							<div style="float:left;margin-left:10px;">
								<select id="langSelect" name="language">
									<option value="">Select a Language</option>
									<option value="">---------------------------</option>
									<?php 
									$langArr = $glosManager->getLanguageArr('all');
									foreach($langArr as $langKey => $langValue ){
										if($relationship !== 'translation' || $relatedLanguage !== $langValue){
											echo '<option '.($relatedLanguage === $langValue || $relatedLanguage === $langKey?'SELECTED':'').'>'.$langValue.'</option>';
										}
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
						<div style="clear:both;padding-top:4px;">
							<div style="float:left;">
								<b>Author: </b>
							</div>
							<div style="float:left;margin-left:10px;">
								<input type="text" name="author" maxlength="250" style="width:400px;" />
							</div>
						</div>
						<div style="clear:both;padding-top:4px;">
							<div style="float:left;">
								<b>Translator: </b>
							</div>
							<div style="float:left;margin-left:10px;">
								<input type="text" name="translator" maxlength="250" style="width:400px;" />
							</div>
						</div>
						<div style="clear:both;padding-top:4px;">
							<div style="float:left;">
								<b>Source: </b>
							</div>
							<div style="float:left;margin-left:10px;">
								<input type="text" name="source" maxlength="1000" style="width:700px;" />
							</div>
						</div>
						<div style="clear:both;padding-top:4px;">
							<div style="float:left;">
								<b>Notes: </b>
							</div>
							<div style="float:left;margin-left:10px;">
								<input type="text" name="notes" maxlength="250" style="width:700px;" />
							</div>
						</div>
						<div style="clear:both;padding-top:4px;">
							<div style="float:left;">
								<b>Resource URL: </b>
							</div>
							<div style="float:left;margin-left:10px;">
								<input type="text" name="resourceurl" maxlength="600" style="width:700px;" />
							</div>
						</div>
						<div style="clear:both;"></div>
						<div style="clear:both;">
							<fieldset style="padding:10px;margin-top:12px;">
								<?php 
								if(!$relatedGlossId){
									?>
									<div style="">
										Please enter the taxonomic group (higher than family) to which this term applies <b>OR</b> link new term to a related existing term 
									</div>
									<div style="padding:4px;">
										<div style="">
											<b>Taxonomic Group: </b>
										</div>
										<div style="">
											<input id="taxagroup" name="taxagroup" type="text" maxlength="45" style="width:250px;" value="<?php echo $taxaName; ?>" />
											<input id="tid" name="tid" type="hidden" value="<?php echo $taxaTid; ?>" />
										</div>
									</div>
									<div style="padding:10px;">
										<b>-- OR --</b>
									</div>
									<?php 
								}
								?>
								<div style="padding:4px;font-weight:bold;text-decoration:underline;">Link to related term</div>
								<div style="margin:10px">
									<div style="margin:3px">
										<b>Relationship:</b> 
										<select name="relation" <?php echo ($relationship?'readonly':''); ?>>
											<option value="">Select Relationship</option>
											<option value="">----------------------------</option>
											<option value="synonym" <?php echo ($relationship === 'synonym'?'selected':''); ?>>Synonym</option>
											<option value="translation" <?php echo ($relationship === 'translation'?'selected':''); ?>>Translation</option>
										</select>
									</div>
									<div style="margin:3px">
										<b>Related Term:</b> 
										<select name="relglossid" <?php echo ($relatedGlossId?'readonly':''); ?>>
											<option value="">Select Term to be Link</option>
											<option value="">----------------------------</option>
											<?php 
											$termList = $glosManager->getTermList($relatedGlossId,$relatedLanguage);
											foreach($termList as $id => $termName){
												echo '<option value="'.$id.'" '.($relatedGlossId === $id?'selected':'').'>'.$termName.'</option>';
											}
											?>
										</select>
									</div>
								</div>
							</fieldset>
						</div>
						<div style="clear:both;padding:25px;">
							<input name="formsubmit" type="hidden" value="Create Term" />
							<button name="submitbutton" type="submit" value="Create Term">Create Term</button>
						</div>
					</fieldset>
				</form>
			</div>
			<?php
		}
		else{
			echo '<div style="font-weight:bold;margin:20px">You do not have editing permissions for glossary</div>';
		}
		?>
	</div>
</body>
</html>
