<?php
include_once(__DIR__ . '/../../config/symbbase.php');
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
    if(($action === 'createProfile') && !$labelManager->saveNewLabelFormatJson($_POST)) {
        $statusStr = implode('; ', $labelManager->getErrorArr());
    }
    $applyEdits = true;
    $scope = ($_POST['scope'] ?? '');
    if($scope === 'g' && $isEditor < 3) {
        $applyEdits = false;
    }
    if($scope === 'c' && $isEditor < 2) {
        $applyEdits = false;
    }
    if($applyEdits){
        if($action === 'saveProfile'){
            if(!$labelManager->saveLabelFormatJson($_POST)){
                $statusStr = implode('; ', $labelManager->getErrorArr());
            }
        }
        elseif($action === 'deleteProfile'){
            if(!$labelManager->deleteLabelFormat($_POST['scope'],$_POST['index'])){
                $statusStr = implode('; ', $labelManager->getErrorArr());
            }
        }
    }
}
$isGeneralObservation = ($labelManager->getMetaDataTerm('colltype') === 'General Observations');
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Label Format Manager</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <style>
        fieldset{ width:850px; padding:15px; }
        fieldset legend{ font-weight:bold; }
        textarea{ width: 800px; height: 300px }
        input[type=text]{ width:500px }
        hr{ margin:15px 0px; }
        fieldset{ border:1px solid black; }
        .fieldset-block{ width:800px }
        .field-block{ margin:3px 0px }
        .labelFormat{ font-weight: bold; }
    </style>
    <script src="../../js/all.min.js" type="text/javascript"></script>
    <script src="../../js/jquery.js" type="text/javascript"></script>
    <script src="../../js/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        function toggleEditDiv(classTag){
            $('#display-'+classTag).toggle();
            $('#edit-'+classTag).toggle();
        }

        function toggleNew(scope){
            const divId = 'new-' + scope;
            if(document.getElementById(divId).style.display === 'none'){
                const formId = 'labelprofilenew-' + scope;
                const newForm = document.getElementById(formId);
                document.getElementById(divId).style.display = 'block';
                setEmptyJsonVal(newForm);
            }
            else{
                document.getElementById(divId).style.display = 'none';
            }
        }

        function setEmptyJsonVal(form){
            const newArr = {};
            newArr['title'] = '';
            newArr['labelBlocks'] = [];
            form.json.value = JSON.stringify(newArr, null, 4);
        }

        function toggleJsonDiv(id){
            const divId = 'jsonDisplay' + id;
            if(document.getElementById(divId).style.display === 'none'){
                document.getElementById(divId).style.display = 'block';
            }
            else{
                document.getElementById(divId).style.display = 'none';
            }
        }

        function processLabelFormChange(formId){
            const f = document.getElementById(formId);
            const currArr = JSON.parse(f.json.value);
            const newArr = {};
            let header = false;
            let footer = false;
            newArr['title'] = f.title.value;
            if(f.headerPrefix.value){
                newArr['headerPrefix'] = f.headerPrefix.value;
                header = true;
            }
            if(f.headerMidText.value){
                newArr['headerMidText'] = f.headerMidText.value;
                header = true;
            }
            if(f.headerSuffix.value){
                newArr['headerSuffix'] = f.headerSuffix.value;
                header = true;
            }
            if(header){
                if(f.headerBold.checked === true){
                    newArr['headerBold'] = true;
                }
                if(f.headerItalic.checked === true){
                    newArr['headerItalic'] = true;
                }
                if(f.headerUnderline.checked === true){
                    newArr['headerUnderline'] = true;
                }
                if(f.headerUppercase.checked === true){
                    newArr['headerUppercase'] = true;
                }
                if(f.headerTextAlign.value){
                    newArr['headerTextAlign'] = f.headerTextAlign.value;
                }
                if(f.headerBottomMargin.value){
                    newArr['headerBottomMargin'] = f.headerBottomMargin.value;
                }
                if(f.headerFont.value){
                    newArr['headerFont'] = f.headerFont.value;
                }
                if(f.headerFontSize.value){
                    newArr['headerFontSize'] = f.headerFontSize.value;
                }
            }
            if(f.footerText.value){
                newArr['footerText'] = f.footerText.value;
                footer = true;
            }
            if(footer){
                if(f.footerBold.checked === true){
                    newArr['footerBold'] = true;
                }
                if(f.footerItalic.checked === true){
                    newArr['footerItalic'] = true;
                }
                if(f.footerUnderline.checked === true){
                    newArr['footerUnderline'] = true;
                }
                if(f.footerUppercase.checked === true){
                    newArr['footerUppercase'] = true;
                }
                if(f.footerTextAlign.value){
                    newArr['footerTextAlign'] = f.footerTextAlign.value;
                }
                if(f.footerTopMargin.value){
                    newArr['footerTopMargin'] = f.footerTopMargin.value;
                }
                if(f.footerFont.value){
                    newArr['footerFont'] = f.footerFont.value;
                }
                if(f.footerFontSize.value){
                    newArr['footerFontSize'] = f.footerFontSize.value;
                }
            }
            if(f.defaultFont.value){
                newArr['defaultFont'] = f.defaultFont.value;
            }
            if(f.defaultFontSize.value){
                newArr['defaultFontSize'] = f.defaultFontSize.value;
            }
            if(f.pageLayout.value){
                newArr['pageLayout'] = f.pageLayout.value;
            }
            newArr['labelBlocks'] = currArr['labelBlocks'];
            f.json.value = JSON.stringify(newArr, null, 4);
        }

        function processLabelFormJsonChange(formId){
            const f = document.getElementById(formId);
            try{
                const labelArr = JSON.parse(f.json.value);
                setFormFromJson(formId,labelArr);
                f.json.value = JSON.stringify(labelArr, null, 4);
            }catch(error){
                alert('There was a problem parsing the JSON.');
                setEmptyJsonVal(f);
            }
        }

        function setFormFromJson(formId,labelArr){
            const f = document.getElementById(formId);
            if(labelArr.hasOwnProperty('title')){
                f.title.value = labelArr['title'];
            }
            else{
                f.title.value = '';
            }
            if(labelArr.hasOwnProperty('headerPrefix')){
                f.headerPrefix.value = labelArr['headerPrefix'];
            }
            else{
                f.headerPrefix.value = '';
            }
            if(labelArr.hasOwnProperty('headerMidText')){
                f.headerMidText.value = labelArr['headerMidText'];
            }
            else{
                f.headerMidText[0].checked = true;
            }
            if(labelArr.hasOwnProperty('headerSuffix')){
                f.headerSuffix.value = labelArr['headerSuffix'];
            }
            else{
                f.headerSuffix.value = '';
            }
            if(labelArr.hasOwnProperty('headerBold')){
                f.headerBold.checked = true;
            }
            else{
                f.headerBold.checked = false;
            }
            if(labelArr.hasOwnProperty('headerItalic')){
                f.headerItalic.checked = true;
            }
            else{
                f.headerItalic.checked = false;
            }
            if(labelArr.hasOwnProperty('headerUnderline')){
                f.headerUnderline.checked = true;
            }
            else{
                f.headerUnderline.checked = false;
            }
            if(labelArr.hasOwnProperty('headerUppercase')){
                f.headerUppercase.checked = true;
            }
            else{
                f.headerUppercase.checked = false;
            }
            if(labelArr.hasOwnProperty('headerTextAlign')){
                f.headerTextAlign.value = labelArr['headerTextAlign'];
            }
            else{
                f.headerTextAlign.value = 'left';
            }
            if(labelArr.hasOwnProperty('headerBottomMargin')){
                f.headerBottomMargin.value = labelArr['headerBottomMargin'];
            }
            else{
                f.headerBottomMargin.value = '';
            }
            if(labelArr.hasOwnProperty('headerFont')){
                f.headerFont.value = labelArr['headerFont'];
            }
            else{
                f.headerFont.value = '';
            }
            if(labelArr.hasOwnProperty('headerFontSize')){
                f.headerFontSize.value = labelArr['headerFontSize'];
            }
            else{
                f.headerFontSize.value = '';
            }
            if(labelArr.hasOwnProperty('footerText')){
                f.footerText.value = labelArr['footerText'];
            }
            else{
                f.footerText.value = '';
            }
            if(labelArr.hasOwnProperty('footerBold')){
                f.footerBold.checked = true;
            }
            else{
                f.footerBold.checked = false;
            }
            if(labelArr.hasOwnProperty('footerItalic')){
                f.footerItalic.checked = true;
            }
            else{
                f.footerItalic.checked = false;
            }
            if(labelArr.hasOwnProperty('footerUnderline')){
                f.footerUnderline.checked = true;
            }
            else{
                f.footerUnderline.checked = false;
            }
            if(labelArr.hasOwnProperty('footerUppercase')){
                f.footerUppercase.checked = true;
            }
            else{
                f.footerUppercase.checked = false;
            }
            if(labelArr.hasOwnProperty('footerTextAlign')){
                f.footerTextAlign.value = labelArr['footerTextAlign'];
            }
            else{
                f.footerTextAlign.value = 'left';
            }
            if(labelArr.hasOwnProperty('footerTopMargin')){
                f.footerTopMargin.value = labelArr['footerTopMargin'];
            }
            else{
                f.footerTopMargin.value = '';
            }
            if(labelArr.hasOwnProperty('footerFont')){
                f.footerFont.value = labelArr['footerFont'];
            }
            else{
                f.footerFont.value = '';
            }
            if(labelArr.hasOwnProperty('footerFontSize')){
                f.footerFontSize.value = labelArr['footerFontSize'];
            }
            else{
                f.footerFontSize.value = '';
            }
            if(labelArr.hasOwnProperty('defaultFont')){
                f.defaultFont.value = labelArr['defaultFont'];
            }
            else{
                f.defaultFont.value = 'Arial';
            }
            if(labelArr.hasOwnProperty('defaultFontSize')){
                f.defaultFontSize.value = labelArr['defaultFontSize'];
            }
            else{
                f.defaultFontSize.value = '';
            }
            if(labelArr.hasOwnProperty('pageLayout')){
                f.pageLayout.value = labelArr['pageLayout'];
            }
            else{
                f.pageLayout.value = '2';
            }
        }

        function validateLabelFormatForm(formId){
            const f = document.getElementById(formId);
            if(f.title.value !== ''){
                return true;
            }
            alert('Please enter a title for the label format profile.');
            return false;
        }

        function openJsonEditorPopup(formId){
            const f = document.getElementById(formId);
            let editorWindow = window.open('labeljsongui.php');
            if(editorWindow.opener == null){
                editorWindow.opener = self;
            }
            editorWindow.focus();
            editorWindow.onload = function(){
                editorWindow.document.getElementById("formid").value = formId;
                editorWindow.document.getElementById("guijson").value = f.json.value;
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
    <a href="labelmanager.php?collid=<?php echo $collid; ?>&emode=1">Print Labels/Annotations</a> &gt;&gt;
    <b>Label Format Manager</b>
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
    foreach($labelFormatArr as $scope => $scopeArr){
        $fieldsetTitlePrefix = '';
        if($scope === 'g') {
            $fieldsetTitlePrefix = 'Portal Label';
        }
        elseif($scope === 'c') {
            $fieldsetTitlePrefix = $labelManager->getCollName() . ' Label';
        }
        elseif($scope === 'u') {
            $fieldsetTitlePrefix = 'User Label';
        }
        $fieldsetTitle = $fieldsetTitlePrefix . ' Profiles ('.count($scopeArr).' formats)';
        ?>
        <fieldset style="margin-top:5px;">
            <legend><?php echo $fieldsetTitle; ?></legend>
            <?php
            if($isEditor === 3 || $scope === 'u' || ($scope === 'c' && $isEditor > 1)) {
                echo '<div style="float:right;" title="Create a new label profile"><i style="height:15px;width:15px;color:green;" class="fas fa-plus" onclick="toggleNew(\'' . $scope . '\');"></i></div>';
            }
            if($scopeArr){
                foreach($scopeArr as $index => $formatArr){
                    $midText = '';
                    $pageLayout = 2;
                    if($formatArr){
                        if($index) {
                            echo '<hr/>';
                        }
                        ?>
                        <div id="display-<?php echo $scope.'-'.$index; ?>">
                            <div class="field-block">
                                <span class="labelFormat">Title:</span>
                                <span class="field-value"><?php echo htmlspecialchars($formatArr['title']); ?></span>
                                <?php
                                if($isEditor === 3 || $scope === 'u' || ($scope === 'c' && $isEditor > 1)) {
                                    echo '<span title="Edit label profile"> <a href="#" onclick="toggleEditDiv(\'' . $scope . '-' . $index . '\');return false;"><i style="width:15px;height:15px;" class="far fa-edit"></i></a></span>';
                                }
                                ?>
                            </div>
                            <?php
                            if(isset($formatArr['headerMidText'])) {
                                $midText = $formatArr['headerMidText'];
                            }
                            $headerStr = isset($formatArr['headerPrefix']) ? $formatArr['headerPrefix'].' ' : '';
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
                            $headerStr = isset($formatArr['headerSuffix']) ? ' '.$formatArr['headerSuffix'] : '';
                            if(trim($headerStr)){
                                ?>
                                <div class="field-block">
                                    <span class="labelFormat">Header: </span>
                                    <span class="field-value"><?php echo htmlspecialchars(trim($headerStr)); ?></span>
                                </div>
                                <?php
                            }
                            if(isset($formatArr['footerText'])){
                                ?>
                                <div class="field-block">
                                    <span class="labelFormat">Footer: </span>
                                    <span class="field-value"><?php echo htmlspecialchars($formatArr['footerText']); ?></span>
                                </div>
                                <?php
                            }
                            if($formatArr['pageLayout']){
                                $pageLayout = $formatArr['pageLayout'];
                                ?>
                                <div class="field-block">
                                    <span class="labelFormat">Type: </span>
                                    <span class="field-value"><?php echo $pageLayout.(is_numeric($pageLayout)?' column per page':''); ?></span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    $formId = 'edit-' . $scope.(is_numeric($index)?'-'.$index:'');
                    ?>
                    <form id="<?php echo $formId; ?>" action="labelprofile.php" method="post" style="display:none;" onsubmit="return validateLabelFormatForm('<?php echo $formId; ?>');">
                        <div class="field-block">
                            <span class="labelFormat">Title:</span>
                            <span class="field-elem"><input name="title" type="text" value="<?php echo ($formatArr?$formatArr['title']:''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" /> </span>
                            <?php
                            if($formatArr) {
                                echo '<span title="Edit label profile"><i style="width:15px;height:15px;" class="far fa-edit" onclick="toggleEditDiv(\'' . $scope . '-' . $index . '\')"></i></span>';
                            }
                            ?>
                        </div>
                        <fieldset class="fieldset-block">
                            <legend>Label Header</legend>
                            <div class="field-block">
                                <span class="labelFormat">Prefix:</span>
                                <span class="field-elem">
										<input name="headerPrefix" type="text" value="<?php echo ($formatArr['headerPrefix'] ?? ''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" />
									</span>
                            </div>
                            <div class="field-block">
                                <div class="field-elem">
										<span class="field-inline">
											<input name="headerMidText" type="radio" value="" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (!$midText?'checked':''); ?> />
											<span class="labelFormat">Blank</span>
										</span>
                                    <span class="field-inline">
											<input name="headerMidText" type="radio" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo ((int)$midText === 1?'checked':''); ?> />
											<span class="labelFormat">Country</span>
										</span>
                                    <span class="field-inline">
											<input name="headerMidText" type="radio" value="2" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo ((int)$midText === 2?'checked':''); ?> />
											<span class="labelFormat">State</span>
										</span>
                                    <span class="field-inline">
											<input name="headerMidText" type="radio" value="3" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo ((int)$midText === 3?'checked':''); ?> />
											<span class="labelFormat">County</span>
										</span>
                                    <span class="field-inline">
											<input name="headerMidText" type="radio" value="4" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo ((int)$midText === 4?'checked':''); ?> />
											<span class="labelFormat">Family</span>
										</span>
                                </div>
                            </div>
                            <div class="field-block">
                                <span class="labelFormat">Suffix:</span>
                                <span class="field-elem"><input name="headerSuffix" type="text" value="<?php echo ($formatArr['headerSuffix'] ?? ''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" /></span>
                            </div>
                            <div class="field-block">
                                <div class="field-elem">
										<span class="field-inline">
											<input name="headerBold" type="checkbox" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (isset($formatArr['headerBold']) && $formatArr['headerBold']?'checked':''); ?> />
									        <span class="labelFormat">Bold</span>
										</span>
                                    <span class="field-inline">
											<input name="headerItalic" type="checkbox" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (isset($formatArr['headerItalic']) && $formatArr['headerItalic']?'checked':''); ?> />
									        <span class="labelFormat">Italic</span>
										</span>
                                    <span class="field-inline">
											<input name="headerUnderline" type="checkbox" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (isset($formatArr['headerUnderline']) && $formatArr['headerUnderline']?'checked':''); ?> />
									        <span class="labelFormat">Underline</span>
										</span>
                                    <span class="field-inline">
											<input name="headerUppercase" type="checkbox" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (isset($formatArr['headerUppercase']) && $formatArr['headerUppercase']?'checked':''); ?> />
									        <span class="labelFormat">Uppercase</span>
										</span>
                                </div>
                            </div>
                            <div class="field-block">
                                <div class="field-elem">
										<span class="field-inline">
											<span class="labelFormat">Text Alignment:</span>
                                            <select name="headerTextAlign" onchange="processLabelFormChange('<?php echo $formId; ?>')">
                                                <option value="left" <?php echo (isset($formatArr['headerTextAlign']) && $formatArr['headerTextAlign'] === 'left'?'selected':''); ?>>Left</option>
                                                <option value="center" <?php echo (isset($formatArr['headerTextAlign']) && $formatArr['headerTextAlign'] === 'center'?'selected':''); ?>>Center</option>
                                                <option value="right" <?php echo (isset($formatArr['headerTextAlign']) && $formatArr['headerTextAlign'] === 'right'?'selected':''); ?>>Right</option>
                                            </select>
										</span>
                                    <span class="field-inline" style="margin-left:5px;">
											<span class="labelFormat">Margin Below (px):</span>
									        <span class="field-elem"><input name="headerBottomMargin" type="text" style="width:40px;" value="<?php echo ($formatArr['headerBottomMargin'] ?? ''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" /></span>
										</span>
                                    <span class="field-inline" style="margin-left:5px;">
											<span class="labelFormat">Font:</span>
                                            <select name="headerFont" onchange="processLabelFormChange('<?php echo $formId; ?>')">
                                                <option value="">Select a Font</option>
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
											<span class="labelFormat">Font Size (px):</span>
									        <span class="field-elem"><input name="headerFontSize" type="text" style="width:40px;" value="<?php echo ($formatArr['headerFontSize'] ?? ''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" /></span>
										</span>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="fieldset-block">
                            <legend>Label Footer</legend>
                            <div class="field-block">
                                <span class="labelFormat">Footer text:</span>
                                <input name="footerText" type="text" value="<?php echo ($formatArr['footerText'] ?? ''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" />
                            </div>
                            <div class="field-block">
                                <div class="field-elem">
										<span class="field-inline">
											<input name="footerBold" type="checkbox" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (isset($formatArr['footerBold'])&&$formatArr['footerBold']?'checked':''); ?> />
									        <span class="labelFormat">Bold</span>
										</span>
                                    <span class="field-inline">
											<input name="footerItalic" type="checkbox" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (isset($formatArr['footerItalic'])&&$formatArr['footerItalic']?'checked':''); ?> />
									        <span class="labelFormat">Italic</span>
										</span>
                                    <span class="field-inline">
											<input name="footerUnderline" type="checkbox" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (isset($formatArr['footerUnderline'])&&$formatArr['footerUnderline']?'checked':''); ?> />
									        <span class="labelFormat">Underline</span>
										</span>
                                    <span class="field-inline">
											<input name="footerUppercase" type="checkbox" value="1" onchange="processLabelFormChange('<?php echo $formId; ?>')" <?php echo (isset($formatArr['footerUppercase'])&&$formatArr['footerUppercase']?'checked':''); ?> />
									        <span class="labelFormat">Uppercase</span>
										</span>
                                </div>
                            </div>
                            <div class="field-block">
                                <div class="field-elem">
										<span class="field-inline">
											<span class="labelFormat">Text Alignment:</span>
                                            <select name="footerTextAlign" onchange="processLabelFormChange('<?php echo $formId; ?>')">
                                                <option value="left" <?php echo (isset($formatArr['footerTextAlign']) && $formatArr['footerTextAlign'] === 'left'?'selected':''); ?>>Left</option>
                                                <option value="center" <?php echo (isset($formatArr['footerTextAlign']) && $formatArr['footerTextAlign'] === 'center'?'selected':''); ?>>Center</option>
                                                <option value="right" <?php echo (isset($formatArr['footerTextAlign']) && $formatArr['footerTextAlign'] === 'right'?'selected':''); ?>>Right</option>
                                            </select>
										</span>
                                    <span class="field-inline" style="margin-left:5px;">
											<span class="labelFormat">Margin Above (px):</span>
									        <span class="field-elem"><input name="footerTopMargin" type="text" style="width:40px;" value="<?php echo ($formatArr['footerTopMargin'] ?? ''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" /></span>
										</span>
                                    <span class="field-inline" style="margin-left:5px;">
											<span class="labelFormat">Font:</span>
                                            <select name="footerFont" onchange="processLabelFormChange('<?php echo $formId; ?>')">
                                                <option value="">Select a Font</option>
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
											<span class="labelFormat">Font Size (px):</span>
									        <span class="field-elem"><input name="footerFontSize" type="text" style="width:40px;" value="<?php echo ($formatArr['footerFontSize'] ?? ''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" /></span>
										</span>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="fieldset-block">
                            <legend>Label Settings</legend>
                            <div class="field-block">
                                <span class="labelFormat">Default Font:</span>
                                <select name="defaultFont" onchange="processLabelFormChange('<?php echo $formId; ?>')">
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
                                <span class="labelFormat">Default Font Size (px):</span>
                                <span class="field-elem"><input name="defaultFontSize" type="text" style="width:40px;" value="<?php echo ($formatArr['defaultFontSize'] ?? ''); ?>" onchange="processLabelFormChange('<?php echo $formId; ?>')" /></span>
                            </div>
                            <div class="field-block">
                                <span class="labelFormat">Page Layout:</span>
                                <select name="pageLayout" onchange="processLabelFormChange('<?php echo $formId; ?>')">
                                    <option value="1" <?php echo ((int)$pageLayout === 1?'selected':''); ?>>1 column per page</option>
                                    <option value="2" <?php echo ((int)$pageLayout === 2?'selected':''); ?>>2 columns per page</option>
                                    <option value="3" <?php echo ((int)$pageLayout === 3?'selected':''); ?>>3 columns per page</option>
                                    <option value="4" <?php echo ((int)$pageLayout === 4?'selected':''); ?>>4 columns per page</option>
                                </select>
                            </div>
                        </fieldset>
                        <div id="jsonDisplay<?php echo $scope.(is_numeric($index)?'-'.$index:''); ?>" class="field-block" style="display:none;">
                            <div class="label">JSON:</div>
                            <div class="field-block">
                                <textarea id="json-<?php echo $scope.(is_numeric($index)?'-'.$index:''); ?>" name="json" onchange="processLabelFormJsonChange('<?php echo $formId; ?>')" ><?php echo ($formatArr?json_encode($formatArr,JSON_PRETTY_PRINT):''); ?></textarea>
                            </div>
                        </div>
                        <div>
                            <input type="hidden" name="collid" value="<?php echo $collid; ?>" />
                            <input type="hidden" name="scope" value="<?php echo $scope; ?>" />
                            <input type="hidden" name="index" value="<?php echo $index; ?>" />
                            <span><a href="#" onclick="toggleJsonDiv('<?php echo $scope.(is_numeric($index)?'-'.$index:''); ?>');return false"><button class="icon-button" title="Edit raw JSON"><i style="width:15px;height:15px;" class="fas fa-code"></i></button></a></span>
                            <span style="margin-left:5px;"><a href="#" onclick="openJsonEditorPopup('<?php echo $formId; ?>');return false"><button class="icon-button" title="Open JSON builder"><i style="width:15px;height:15px;" class="fas fa-tools"></i></button></a></span>
                            <?php
                            if($isEditor === 3 || $scope === 'u' || ($scope === 'c' && $isEditor > 1)) {
                                echo '<span style="margin-left:25px"><button name="submitaction" type="submit" value="saveProfile">' . (is_numeric($index) ? 'Save Label Profile' : 'Create New Label Profile') . '</button></span>';
                            }
                            if(is_numeric($index)){
                                if($isEditor === 3 || $scope === 'u' || ($scope === 'c' && $isEditor > 1)) {
                                    echo '<span style="margin-left:5px"><button name="submitaction" type="submit" value="deleteProfile" onclick="return confirm(\'Are you sure you want to delete this profile?\')">Delete Profile</button></span>';
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
                    </form>
                    <?php
                }
            }
            else{
                echo '<div>There are no label profiles saved.';
                if($isEditor === 3 || $scope === 'u' || ($scope === 'c' && $isEditor > 1)) {
                    echo 'Click the green plus sign to the right to create a new label profile';
                }
                echo '</div>';
            }
            ?>
            <fieldset id="new-<?php echo $scope; ?>" style="display:none;margin-top:10px;">
                <legend>Create New <?php echo $fieldsetTitlePrefix; ?> Profile</legend>
                <form id="labelprofilenew-<?php echo $scope; ?>" action="labelprofile.php" method="post" onsubmit="return validateLabelFormatForm('labelprofilenew-<?php echo $scope; ?>');">
                    <div class="field-block">
                        <span class="labelFormat">Title:</span>
                        <span class="field-elem"><input name="title" type="text" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" /> </span>
                    </div>
                    <fieldset class="fieldset-block">
                        <legend>Label Header</legend>
                        <div class="field-block">
                            <span class="labelFormat">Prefix:</span>
                            <span class="field-elem">
                                    <input name="headerPrefix" type="text" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                </span>
                        </div>
                        <div class="field-block">
                            <div class="field-elem">
                                    <span class="field-inline">
                                        <input name="headerMidText" type="radio" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Blank</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="headerMidText" type="radio" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Country</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="headerMidText" type="radio" value="2" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">State</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="headerMidText" type="radio" value="3" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">County</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="headerMidText" type="radio" value="4" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Family</span>
                                    </span>
                            </div>
                        </div>
                        <div class="field-block">
                            <span class="labelFormat">Suffix:</span>
                            <span class="field-elem"><input name="headerSuffix" type="text" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" /></span>
                        </div>
                        <div class="field-block">
                            <div class="field-elem">
                                    <span class="field-inline">
                                        <input name="headerBold" type="checkbox" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Bold</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="headerItalic" type="checkbox" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Italic</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="headerUnderline" type="checkbox" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Underline</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="headerUppercase" type="checkbox" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Uppercase</span>
                                    </span>
                            </div>
                        </div>
                        <div class="field-block">
                            <div class="field-elem">
                                    <span class="field-inline">
                                        <span class="labelFormat">Text Alignment:</span>
                                        <select name="headerTextAlign" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')">
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </span>
                                <span class="field-inline" style="margin-left:5px;">
                                        <span class="labelFormat">Margin Below (px):</span>
                                        <span class="field-elem"><input name="headerBottomMargin" type="text" style="width:40px;" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" /></span>
                                    </span>
                                <span class="field-inline" style="margin-left:5px;">
                                        <span class="labelFormat">Font:</span>
                                        <select name="headerFont" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')">
                                            <option value="Arial">Arial (sans-serif)</option>
                                            <option value="Brush Script MT">Brush Script MT (cursive)</option>
                                            <option value="Courier New">Courier New (monospace)</option>
                                            <option value="Garamond">Garamond (serif)</option>
                                            <option value="Georgia">Georgia (serif)</option>
                                            <option value="Helvetica">Helvetica (sans-serif)</option>
                                            <option value="Tahoma">Tahoma (sans-serif)</option>
                                            <option value="Times New Roman">Times New Roman (serif)</option>
                                            <option value="Trebuchet">Trebuchet (sans-serif)</option>
                                            <option value="Verdana">Verdana (sans-serif)</option>
                                        </select>
                                    </span>
                                <span class="field-inline" style="margin-left:5px;">
                                        <span class="labelFormat">Font Size (px):</span>
                                        <span class="field-elem"><input name="headerFontSize" type="text" style="width:40px;" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" /></span>
                                    </span>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="fieldset-block">
                        <legend>Label Footer</legend>
                        <div class="field-block">
                            <span class="labelFormat">Footer text:</span>
                            <input name="footerText" type="text" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                        </div>
                        <div class="field-block">
                            <div class="field-elem">
                                    <span class="field-inline">
                                        <input name="footerBold" type="checkbox" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Bold</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="footerItalic" type="checkbox" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Italic</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="footerUnderline" type="checkbox" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Underline</span>
                                    </span>
                                <span class="field-inline">
                                        <input name="footerUppercase" type="checkbox" value="1" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" />
                                        <span class="labelFormat">Uppercase</span>
                                    </span>
                            </div>
                        </div>
                        <div class="field-block">
                            <div class="field-elem">
                                    <span class="field-inline">
                                        <span class="labelFormat">Text Alignment:</span>
                                        <select name="footerTextAlign" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')">
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </span>
                                <span class="field-inline" style="margin-left:5px;">
                                        <span class="labelFormat">Margin Above (px):</span>
                                        <span class="field-elem"><input name="footerTopMargin" type="text" style="width:40px;" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" /></span>
                                    </span>
                                <span class="field-inline" style="margin-left:5px;">
                                        <span class="labelFormat">Font:</span>
                                        <select name="footerFont" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')">
                                            <option value="Arial">Arial (sans-serif)</option>
                                            <option value="Brush Script MT">Brush Script MT (cursive)</option>
                                            <option value="Courier New">Courier New (monospace)</option>
                                            <option value="Garamond">Garamond (serif)</option>
                                            <option value="Georgia">Georgia (serif)</option>
                                            <option value="Helvetica">Helvetica (sans-serif)</option>
                                            <option value="Tahoma">Tahoma (sans-serif)</option>
                                            <option value="Times New Roman">Times New Roman (serif)</option>
                                            <option value="Trebuchet">Trebuchet (sans-serif)</option>
                                            <option value="Verdana">Verdana (sans-serif)</option>
                                        </select>
                                    </span>
                                <span class="field-inline" style="margin-left:5px;">
                                        <span class="labelFormat">Font Size (px):</span>
                                        <span class="field-elem"><input name="footerFontSize" type="text" style="width:40px;" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" /></span>
                                    </span>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="fieldset-block">
                        <legend>Label Settings</legend>
                        <div class="field-block">
                            <span class="labelFormat">Default Font:</span>
                            <select name="defaultFont" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')">
                                <option value="Arial">Arial (sans-serif)</option>
                                <option value="Brush Script MT">Brush Script MT (cursive)</option>
                                <option value="Courier New">Courier New (monospace)</option>
                                <option value="Garamond">Garamond (serif)</option>
                                <option value="Georgia">Georgia (serif)</option>
                                <option value="Helvetica">Helvetica (sans-serif)</option>
                                <option value="Tahoma">Tahoma (sans-serif)</option>
                                <option value="Times New Roman">Times New Roman (serif)</option>
                                <option value="Trebuchet">Trebuchet (sans-serif)</option>
                                <option value="Verdana">Verdana (sans-serif)</option>
                            </select>
                        </div>
                        <div class="field-block">
                            <span class="labelFormat">Default Font Size (px):</span>
                            <span class="field-elem"><input name="defaultFontSize" type="text" style="width:40px;" value="" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')" /></span>
                        </div>
                        <div class="field-block">
                            <span class="labelFormat">Page Layout:</span>
                            <select name="pageLayout" onchange="processLabelFormChange('labelprofilenew-<?php echo $scope; ?>')">
                                <option value="1">1 column per page</option>
                                <option value="2" selected>2 columns per page</option>
                                <option value="3">3 columns per page</option>
                                <option value="4">4 columns per page</option>
                            </select>
                        </div>
                    </fieldset>
                    <div id="jsonDisplayNew<?php echo $scope; ?>" class="field-block" style="display:none;">
                        <div class="label">JSON:</div>
                        <div class="field-block">
                            <textarea id="json-New<?php echo $scope; ?>" name="json" onchange="processLabelFormJsonChange('labelprofilenew-<?php echo $scope; ?>')"></textarea>
                        </div>
                    </div>
                    <div>
                        <input type="hidden" name="collid" value="<?php echo $collid; ?>" />
                        <input type="hidden" name="scope" value="<?php echo $scope; ?>" />
                        <span><a href="#" onclick="toggleJsonDiv('New<?php echo $scope; ?>');return false"><button class="icon-button" title="Edit raw JSON"><i style="width:15px;height:15px;" class="fas fa-code"></i></button></a></span>
                        <span style="margin-left:5px;"><a href="#" onclick="openJsonEditorPopup('labelprofilenew-<?php echo $scope; ?>');return false"><button class="icon-button" title="Open JSON builder"><i style="width:15px;height:15px;" class="fas fa-tools"></i></button></a></span>
                        <?php
                        if($isEditor === 3 || $scope === 'u' || ($scope === 'c' && $isEditor > 1)) {
                            echo '<span style="margin-left:25px"><button name="submitaction" type="submit" value="createProfile">Create New Label Profile</button></span>';
                        }
                        ?>
                    </div>
                </form>
            </fieldset>
            <hr />
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
