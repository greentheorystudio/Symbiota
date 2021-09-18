<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$isEditor = 0;
if($GLOBALS['SYMB_UID']) {
    $isEditor = 1;
}
if($collid && array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) {
    $isEditor = 2;
}
if($GLOBALS['IS_ADMIN']) {
    $isEditor = 3;
}
$statusStr = '';
if($isEditor && $action){
	if($action === 'cloneProfile'){
		if(isset($_POST['cloneTarget']) && $_POST['cloneTarget']){
			if(!$labelManager->cloneLabelJson($_POST)){
				$statusStr = implode('; ', $labelManager->getErrorArr());
			}
		}
		else {
            $statusStr = 'ERROR: you must select a clone target!';
        }
	}
	$applyEdits = true;
	$group = ($_POST['group'] ?? '');
	if($group === 'g' && $isEditor < 3) {
        $applyEdits = false;
    }
	if($group === 'c' && $isEditor < 2) {
        $applyEdits = false;
    }
	if($applyEdits){
		if($action === 'saveProfile'){
			if(!$labelManager->saveLabelJson($_POST)){
				$statusStr = implode('; ', $labelManager->getErrorArr());
			}
		}
		elseif($action === 'deleteProfile'){
			if(!$labelManager->deleteLabelFormat($_POST['group'],$_POST['index'])){
				$statusStr = implode('; ', $labelManager->getErrorArr());
			}
		}
	}
}
$isGeneralObservation = ($labelManager->getMetaDataTerm('colltype') === 'General Observations');
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Specimen Label Manager</title>
        <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
        <style>
            fieldset{ width:800px; padding:15px; }
            fieldset legend{ font-weight:bold; }
            textarea{ width: 800px; height: 150px }
            input[type=text]{ width:500px }
            hr{ margin:15px 0px; }
            .fieldset-block{ width:700px }
            .field-block{ margin:3px 0px }
            .label{ font-weight: bold; }
            .label-inline{ font-weight: bold; }
            .field-value{  }
            .field-inline{  }
            .edit-icon{ width:13px; }
            #preview-label{ border: 1px solid gray; min-height: 100px; padding: 0.5em; }
            #preview-label.field-block{ line-height: 1.1rem; }
            #preview-label>.field-block>div{ display: inline; }
        </style>
        <script src="../../js/all.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
            let activeProfileCode = "";

            function toggleEditDiv(classTag){
                $('#display-'+classTag).toggle();
                $('#edit-'+classTag).toggle();
            }

            function makeJsonEditable(classTag){
                alert("You should now be able to edit the JSON label definition. Feel free to modify, but note that editing the raw JSON requires knowledge of the JSON format. A simple error may cause label generation to completely fail. Within the next couple weeks, there should be a editor interface made available that will assist. Until then, you may need to ask your portal manager for assistance if you run into problems. Thank you for your patience.");
                $('#json-'+classTag).prop('readonly', false);
                activeProfileCode = classTag;
            }

            function setJson(json){
                $('#json-'+activeProfileCode).val(json);
            }

            function verifyClone(f){
                if(f.cloneTarget.value == ""){
                    alert("Select a clone target!");
                    return false;
                }
                return true;
            }

            function openJsonEditorPopup(classTag){
                activeProfileCode = classTag;
                let editorWindow = window.open('labeljsongui.php','scrollbars=1,toolbar=0,resizable=1,width=1000,height=700,left=20,top=20');
                if(editorWindow.opener == null){
                    editorWindow.opener = self;
                }
                let formatId = "#json-"+classTag;
                let currJson = $("#json-"+classTag).val();
                editorWindow.focus();
                editorWindow.onload = function(){
                    let dummy = editorWindow.document.getElementById("dummy");
                    dummy.value = currJson;
                    dummy.dataset.formatId = formatId;
                    editorWindow.loadJson();
                }
            }
		</script>
	</head>
    <body>
    <?php
    include(__DIR__ . '/../../header.php');
    ?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<?php
		if($isGeneralObservation) {
            echo '<a href="../../profile/viewprofile.php?tabindex=1">Personal Management Menu</a> &gt;&gt; ';
        }
		elseif($collid){
			echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Panel</a> &gt;&gt; ';
		}
		?>
		<a href="labelmanager.php?collid=<?php echo $collid; ?>&emode=1">Label Manager</a> &gt;&gt;
		<b>Label Profile Editor</b>
	</div>
	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:15px;color:red;">
				<?php echo $statusStr; ?>
			</div>
			<?php
		}
		echo '<h2>Specimen Label Profiles</h2>';
		$labelFormatArr = $labelManager->getLabelFormatArr();
		foreach($labelFormatArr as $group => $groupArr){
			$fieldsetTitle = '';
			if($group === 'g') {
                $fieldsetTitle = 'Portal Profiles ';
            }
			elseif($group === 'c') {
                $fieldsetTitle = $labelManager->getCollName() . ' Label Profiles ';
            }
			elseif($group === 'u') {
                $fieldsetTitle = 'User Profiles ';
            }
			$fieldsetTitle .= '('.count($groupArr).' formats)';
			?>
			<fieldset>
				<legend><?php echo $fieldsetTitle; ?></legend>
				<?php
				if($isEditor === 3 || $group === 'u' || ($group === 'c' && $isEditor > 1)) {
                    echo '<div style="float:right;" title="Create a new label profile"><i style="height:15px;width:15px;color:green;" class="fas fa-plus" onclick="$(\'#edit-' . $group . '\').toggle()"></i></div>';
                }
				$index = null;
				$formatArr = array();
				do{
					$midText = '';
					$labelType = 2;
					if($formatArr){
						if($index) {
                            echo '<hr/>';
                        }
						?>
						<div id="display-<?php echo $group.'-'.$index; ?>">
							<div class="field-block">
								<span class="label">Title:</span>
								<span class="field-value"><?php echo htmlspecialchars($formatArr['title']); ?></span>
								<?php
								if($isEditor === 3 || $group === 'u' || ($group === 'c' && $isEditor > 1)) {
                                    echo '<span title="Edit label profile"> <a href="#" onclick="toggleEditDiv(\'' . $group . '-' . $index . '\');return false;"><i style="width:15px;height:15px;" class="far fa-edit"></i></a></span>';
                                }
								?>
							</div>
							<?php
							if(isset($formatArr['headerMidText'])) {
                                $midText = $formatArr['headerMidText'];
                            }
							$headerStr = $formatArr['headerPrefix'].' ';
							if((int)$midText === 1) {
                                $headerStr .= '[COUNTRY];';
                            }
							elseif((int)$midText === 2) {
                                $headerStr .= '[STATE]';
                            }
							elseif((int)$midText === 3) {
                                $headerStr .= '[COUNTY]';
                            }
							elseif((int)$midText === 4) {
                                $headerStr .= '[FAMILY]';
                            }
							$headerStr .= ' '.$formatArr['headerSuffix'];
							if(trim($headerStr)){
								?>
								<div class="field-block">
									<span class="label">Header: </span>
									<span class="field-value"><?php echo htmlspecialchars(trim($headerStr)); ?></span>
								</div>
								<?php
							}
							if($formatArr['footerText']){
								?>
								<div class="field-block">
									<span class="label">Footer: </span>
									<span class="field-value"><?php echo htmlspecialchars($formatArr['footerText']); ?></span>
								</div>
								<?php
							}
							if($formatArr['labelType']){
								$labelType = $formatArr['labelType'];
								?>
								<div class="field-block">
									<span class="label">Type: </span>
									<span class="field-value"><?php echo $labelType.(is_numeric($labelType)?' column per page':''); ?></span>
								</div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
					<form name="labelprofileeditor-<?php echo $group.(is_numeric($index)?'-'.$index:''); ?>" action="labelprofile.php" method="post" onsubmit="return validateJsonForm(this)">
						<div id="edit-<?php echo $group.(is_numeric($index)?'-'.$index:''); ?>" style="display:none">
							<div class="field-block">
								<span class="label">Title:</span>
								<span class="field-elem"><input id="title" type="text" value="<?php echo ($formatArr?$formatArr['title']:''); ?>" required /> </span>
								<?php
								if($formatArr) {
                                    echo '<span title="Edit label profile"><i style="width:15px;height:15px;" class="far fa-edit"  onclick="toggleEditDiv(\'' . $group . '-' . $index . '\')"></i></span>';
                                }
								?>
							</div>
                            <fieldset class="fieldset-block">
								<legend>Label Header</legend>
								<div class="field-block">
									<span class="label">Prefix:</span>
									<span class="field-elem">
										<input id="headerPrefix" type="text" value="<?php echo ($formatArr['headerPrefix'] ?? ''); ?>" />
									</span>
								</div>
								<div class="field-block">
									<div class="field-elem">
										<span class="field-inline">
											<input name="headerMidText" type="radio" value="1" <?php echo ((int)$midText === 1?'checked':''); ?> />
											<span class="label-inline">Country</span>
										</span>
										<span class="field-inline">
											<input name="headerMidText" type="radio" value="2" <?php echo ((int)$midText === 2?'checked':''); ?> />
											<span class="label-inline">State</span>
										</span>
										<span class="field-inline">
											<input name="headerMidText" type="radio" value="3" <?php echo ((int)$midText === 3?'checked':''); ?> />
											<span class="label-inline">County</span>
										</span>
										<span class="field-inline">
											<input name="headerMidText" type="radio" value="4" <?php echo ((int)$midText === 4?'checked':''); ?> />
											<span class="label-inline">Family</span>
										</span>
										<span class="field-inline">
											<input name="headerMidText" type="radio" value="0" <?php echo (!$midText?'checked':''); ?> />
											<span class="label-inline">Blank</span>
										</span>
									</div>
								</div>
								<div class="field-block">
									<span class="label">Suffix:</span>
									<span class="field-elem"><input id="headerSuffix" type="text" value="<?php echo ($formatArr['headerSuffix'] ?? ''); ?>" /></span>
								</div>
                                <div class="field-block">
                                    <div class="field-elem">
										<span class="field-inline">
											<input id="headerBold" type="checkbox" value="1" <?php echo (isset($formatArr['headerBold']) && $formatArr['headerBold']?'checked':''); ?> />
									        <span class="label-inline">Bold</span>
										</span>
                                        <span class="field-inline">
											<input id="headerItalic" type="checkbox" value="1" <?php echo (isset($formatArr['headerItalic']) && $formatArr['headerItalic']?'checked':''); ?> />
									        <span class="label-inline">Italic</span>
										</span>
                                        <span class="field-inline">
											<input id="headerUnderline" type="checkbox" value="1" <?php echo (isset($formatArr['headerUnderline']) && $formatArr['headerUnderline']?'checked':''); ?> />
									        <span class="label-inline">Underline</span>
										</span>
                                        <span class="field-inline">
											<input id="headerUppercase" type="checkbox" value="1" <?php echo (isset($formatArr['headerUppercase']) && $formatArr['headerUppercase']?'checked':''); ?> />
									        <span class="label-inline">Uppercase</span>
										</span>
                                    </div>
                                </div>
                                <div class="field-block">
                                    <div class="field-elem">
										<span class="field-inline">
											<span class="label-inline">Text Alignment:</span>
                                            <select id="headerTextAlign">
                                                <option value="left" <?php echo (isset($formatArr['headerTextAlign']) && $formatArr['headerTextAlign'] === 'left'?'selected':''); ?>>Left</option>
                                                <option value="center" <?php echo (isset($formatArr['headerTextAlign']) && $formatArr['headerTextAlign'] === 'center'?'selected':''); ?>>Center</option>
                                                <option value="right" <?php echo (isset($formatArr['headerTextAlign']) && $formatArr['headerTextAlign'] === 'right'?'selected':''); ?>>Right</option>
                                            </select>
										</span>
                                        <span class="field-inline" style="margin-left:5px;">
											<span class="label">Margin Below (px):</span>
									        <span class="field-elem"><input id="headerBottomMargin" type="text" style="width:40px;" value="<?php echo ($formatArr['headerBottomMargin'] ?? ''); ?>" /></span>
										</span>
                                        <span class="field-inline" style="margin-left:5px;">
											<span class="label-inline">Font:</span>
                                            <select id="headerFont">
                                                <option value="Arial" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Arial'?'selected':''); ?>>Arial (sans-serif)</option>
                                                <option value="Brush Script MT" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Brush Script MT'?'selected':''); ?>>Brush Script MT (cursive)</option>
                                                <option value="Courier New" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Courier New'?'selected':''); ?>>Courier New (monospace)</option>
                                                <option value="Garamond" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Garamond'?'selected':''); ?>>Garamond (serif)</option>
                                                <option value="Georgia" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Georgia'?'selected':''); ?>>Georgia (serif)</option>
                                                <option value="Helvetica" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Helvetica'?'selected':''); ?>>Helvetica (sans-serif)</option>
                                                <option value="Tahoma" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Tahoma'?'selected':''); ?>>Tahoma (sans-serif)</option>
                                                <option value="Times New Roman" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Times New Roman'?'selected':''); ?>>Times New Roman (serif)</option>
                                                <option value="Trebuchet" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Trebuchet'?'selected':''); ?>>Trebuchet (sans-serif)</option>
                                                <option value="Verdana" <?php echo (isset($formatArr['headerFont']) && $formatArr['headerFont'] === 'Verdana'?'selected':''); ?>>Verdana (sans-serif)</option>
                                            </select>
										</span>
                                        <span class="field-inline" style="margin-left:5px;">
											<span class="label">Font Size (px):</span>
									        <span class="field-elem"><input id="headerFontSize" type="text" style="width:40px;" value="<?php echo ($formatArr['headerFontSize'] ?? ''); ?>" /></span>
										</span>
                                    </div>
                                </div>
							</fieldset>
							<fieldset class="fieldset-block">
								<legend>Label Footer</legend>
								<div class="field-block">
									<span class="label-inline">Footer text:</span>
									<input id="footerText" type="text" value="<?php echo ($formatArr['footerText'] ?? ''); ?>" />
								</div>
                                <div class="field-block">
                                    <div class="field-elem">
										<span class="field-inline">
											<input id="footerBold" type="checkbox" value="1" <?php echo (isset($formatArr['footerBold'])&&$formatArr['footerBold']?'checked':''); ?> />
									        <span class="label-inline">Bold</span>
										</span>
                                        <span class="field-inline">
											<input id="footerItalic" type="checkbox" value="1" <?php echo (isset($formatArr['footerItalic'])&&$formatArr['footerItalic']?'checked':''); ?> />
									        <span class="label-inline">Italic</span>
										</span>
                                        <span class="field-inline">
											<input id="footerUnderline" type="checkbox" value="1" <?php echo (isset($formatArr['footerUnderline'])&&$formatArr['footerUnderline']?'checked':''); ?> />
									        <span class="label-inline">Underline</span>
										</span>
                                        <span class="field-inline">
											<input id="footerUppercase" type="checkbox" value="1" <?php echo (isset($formatArr['footerUppercase'])&&$formatArr['footerUppercase']?'checked':''); ?> />
									        <span class="label-inline">Uppercase</span>
										</span>
                                    </div>
                                </div>
                                <div class="field-block">
                                    <div class="field-elem">
										<span class="field-inline">
											<span class="label-inline">Text Alignment:</span>
                                            <select id="footerTextAlign">
                                                <option value="left" <?php echo (isset($formatArr['footerTextAlign']) && $formatArr['footerTextAlign'] === 'left'?'selected':''); ?>>Left</option>
                                                <option value="center" <?php echo (isset($formatArr['footerTextAlign']) && $formatArr['footerTextAlign'] === 'center'?'selected':''); ?>>Center</option>
                                                <option value="right" <?php echo (isset($formatArr['footerTextAlign']) && $formatArr['footerTextAlign'] === 'right'?'selected':''); ?>>Right</option>
                                            </select>
										</span>
                                        <span class="field-inline" style="margin-left:5px;">
											<span class="label">Margin Above (px):</span>
									        <span class="field-elem"><input id="footerTopMargin" type="text" style="width:40px;" value="<?php echo ($formatArr['footerTopMargin'] ?? ''); ?>" /></span>
										</span>
                                        <span class="field-inline" style="margin-left:5px;">
											<span class="label-inline">Font:</span>
                                            <select id="footerFont">
                                                <option value="Arial" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Arial'?'selected':''); ?>>Arial (sans-serif)</option>
                                                <option value="Brush Script MT" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Brush Script MT'?'selected':''); ?>>Brush Script MT (cursive)</option>
                                                <option value="Courier New" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Courier New'?'selected':''); ?>>Courier New (monospace)</option>
                                                <option value="Garamond" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Garamond'?'selected':''); ?>>Garamond (serif)</option>
                                                <option value="Georgia" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Georgia'?'selected':''); ?>>Georgia (serif)</option>
                                                <option value="Helvetica" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Helvetica'?'selected':''); ?>>Helvetica (sans-serif)</option>
                                                <option value="Tahoma" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Tahoma'?'selected':''); ?>>Tahoma (sans-serif)</option>
                                                <option value="Times New Roman" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Times New Roman'?'selected':''); ?>>Times New Roman (serif)</option>
                                                <option value="Trebuchet" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Trebuchet'?'selected':''); ?>>Trebuchet (sans-serif)</option>
                                                <option value="Verdana" <?php echo (isset($formatArr['footerFont']) && $formatArr['footerFont'] === 'Verdana'?'selected':''); ?>>Verdana (sans-serif)</option>
                                            </select>
										</span>
                                        <span class="field-inline" style="margin-left:5px;">
											<span class="label">Font Size (px):</span>
									        <span class="field-elem"><input id="footerFontSize" type="text" style="width:40px;" value="<?php echo ($formatArr['footerFontSize'] ?? ''); ?>" /></span>
										</span>
                                    </div>
                                </div>
							</fieldset>
							<fieldset class="fieldset-block">
								<legend>Label Settings</legend>
                                <div class="field-block">
                                    <span class="label-inline">Default Font:</span>
                                    <select id="defaultFont">
                                        <option value="Arial" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Arial'?'selected':''); ?>>Arial (sans-serif)</option>
                                        <option value="Brush Script MT" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Brush Script MT'?'selected':''); ?>>Brush Script MT (cursive)</option>
                                        <option value="Courier New" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Courier New'?'selected':''); ?>>Courier New (monospace)</option>
                                        <option value="Garamond" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Garamond'?'selected':''); ?>>Garamond (serif)</option>
                                        <option value="Georgia" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Georgia'?'selected':''); ?>>Georgia (serif)</option>
                                        <option value="Helvetica" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Helvetica'?'selected':''); ?>>Helvetica (sans-serif)</option>
                                        <option value="Tahoma" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Tahoma'?'selected':''); ?>>Tahoma (sans-serif)</option>
                                        <option value="Times New Roman" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Times New Roman'?'selected':''); ?>>Times New Roman (serif)</option>
                                        <option value="Trebuchet" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Trebuchet'?'selected':''); ?>>Trebuchet (sans-serif)</option>
                                        <option value="Verdana" <?php echo (isset($formatArr['defaultFont']) && $formatArr['defaultFont'] === 'Verdana'?'selected':''); ?>>Verdana (sans-serif)</option>
                                    </select>
                                </div>
                                <div class="field-block">
                                    <span class="label">Default Font Size (px):</span>
                                    <span class="field-elem"><input id="defaultFontSize" type="text" style="width:40px;" value="<?php echo ($formatArr['defaultFontSize'] ?? ''); ?>" /></span>
                                </div>
                                <div class="field-block">
									<span class="label-inline">Label type:</span>
									<select id="labelType">
										<option value="1" <?php echo ((int)$labelType === 1?'selected':''); ?>>1 columns per page</option>
										<option value="2" <?php echo ((int)$labelType === 2?'selected':''); ?>>2 columns per page</option>
										<option value="3" <?php echo ((int)$labelType === 3?'selected':''); ?>>3 columns per page</option>
										<option value="4" <?php echo ((int)$labelType === 4?'selected':''); ?>>4 columns per page</option>
										<option value="5" <?php echo ((int)$labelType === 5?'selected':''); ?>>5 columns per page</option>
										<option value="6" <?php echo ((int)$labelType === 6?'selected':''); ?>>6 columns per page</option>
										<option value="7" <?php echo ((int)$labelType === 7?'selected':''); ?>>7 columns per page</option>
										<option value="packet" <?php echo ($labelType === 'packet'?'selected':''); ?>>Packet labels</option>
									</select>
								</div>
								<div class="field-block">
									<input id="displaySpeciesAuthor" type="checkbox" value="1" <?php echo (isset($formatArr['displaySpeciesAuthor']) && $formatArr['displaySpeciesAuthor']?'checked':''); ?> />
									<span class="label-inline">Display species for infraspecific taxa</span>
								</div>
								<div class="field-block">
									<input id="displayBarcode" type="checkbox" value=1" <?php echo (isset($formatArr['displayBarcode']) && $formatArr['displayBarcode']?'checked':''); ?> />
									<span class="label-inline">Display barcode</span>
								</div>
							</fieldset>
							<div class="field-block">
								<div class="label">JSON: <span title="Edit JSON label definition"><a href="#" onclick="makeJsonEditable('<?php echo $group.(is_numeric($index)?'-'.$index:''); ?>');return false"><i style="width:15px;height:15px;" class="far fa-edit"></i></a></span><span title="Edit JSON label definition (Visual Interface)"><a href="#" onclick="openJsonEditorPopup('<?php echo $group.(is_numeric($index)?'-'.$index:''); ?>');return false"><i style="width:15px;height:15px;" class="far fa-edit"></i>(visual interface)</a></span>
								</div>
								<div class="field-block">
									<textarea id="json-<?php echo $group.(is_numeric($index)?'-'.$index:''); ?>" name="json" readonly><?php echo (isset($formatArr['labelBlocks'])?json_encode($formatArr['labelBlocks'],JSON_PRETTY_PRINT):''); ?></textarea>
								</div>
							</div>
							<div style="margin-left:20px;">
								<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
								<input type="hidden" name="group" value="<?php echo $group; ?>" />
								<input type="hidden" name="index" value="<?php echo $index; ?>" />
								<?php
								if($isEditor === 3 || $group === 'u' || ($group === 'c' && $isEditor > 1)) {
                                    echo '<span><button name="submitaction" type="submit" value="saveProfile">' . (is_numeric($index) ? 'Save Label Profile' : 'Create New Label Profile') . '</button></span>';
                                }
								if(is_numeric($index)){
									if($isEditor === 3 || $group === 'u' || ($group === 'c' && $isEditor > 1)) {
                                        echo '<span style="margin-left:15px"><button name="submitaction" type="submit" value="deleteProfile" onclick="return confirm(\'Are you sure you want to delete this profile?\')">Delete Profile</button></span>';
                                    }
									?>
									<?php
								}
								?>
							</div>
							<?php
                            if(!is_numeric($index)) {
                                echo '<hr/>';
                            }
                            ?>
						</div>
						<?php
						if(is_numeric($index) && $group !== 'g'){
							?>
							<div style="margin:5px;">
								<span style="margin-left:15px"><button name="submitaction" type="submit" value="cloneProfile" onclick="return verifyClone(this.form)">Clone Profile</button></span> to
								<select name="cloneTarget">
									<option value="">Select Target</option>
									<option value="">----------------</option>
									<?php
									if($isEditor === 3) {
                                        echo '<option value="g">Portal Global Profile</option>';
                                    }
									if($isEditor > 1) {
                                        echo '<option value="c">Collection Profile</option>';
                                    }
									?>
									<option value="u">User Profile</option>
								</select>
							</div>
							<?php
						}
						?>
					</form>
					<?php
					if($groupArr){
						$index = key($groupArr);
						if(is_numeric($index)){
							$formatArr = $groupArr[$index];
							next($groupArr);
						}
					}
				} while(is_numeric($index));
				if(!$formatArr){
					echo '<div>No label profile yet defined. ';
					if($isEditor === 3 || $group === 'u' || ($group === 'c' && $isEditor > 1)) {
                        echo 'Click green plus sign to right to create a new profile';
                    }
					echo '</div>';
				}
				?>
			</fieldset>
			<?php
		}
		if(!$labelFormatArr) {
            echo '<div>You are not authorized to manage any label profiles. Contact portal administrator for more details.</div>';
        }
		?>
	</div>
    <?php
    include(__DIR__ . '/../../footer.php');
    ?>
	</body>
</html>
