<?php
/** @var int $collid */
/** @var int $isEditor */
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../classes/SpecUpload.php');
include_once(__DIR__ . '/../../classes/SpecUploadDirect.php');
include_once(__DIR__ . '/../../classes/SpecUploadFile.php');
include_once(__DIR__ . '/../../classes/SpecUploadDwca.php');

$uspid = array_key_exists('uspid',$_REQUEST)?(int)$_REQUEST['uspid']:0;
$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']): '';
$uploadType = array_key_exists('uploadtype',$_REQUEST)?htmlspecialchars($_REQUEST['uploadtype']):'';
$autoMap = array_key_exists('automap',$_POST);
$ulPath = array_key_exists('ulpath',$_REQUEST)?htmlspecialchars($_REQUEST['ulpath']): '';
$importIdent = array_key_exists('importident',$_REQUEST);
$importImage = array_key_exists('importimage',$_REQUEST);
$matchCatNum = array_key_exists('matchcatnum',$_REQUEST);
$matchOtherCatNum = array_key_exists('matchothercatnum',$_REQUEST);
$verifyImages = (array_key_exists('verifyimages', $_REQUEST) && $_REQUEST['verifyimages']);
$processingStatus = array_key_exists('processingstatus',$_REQUEST)?htmlspecialchars($_REQUEST['processingstatus']):'';
$finalTransfer = array_key_exists('finaltransfer',$_REQUEST)?(int)$_REQUEST['finaltransfer']:0;
$dbpk = array_key_exists('dbpk',$_REQUEST)?htmlspecialchars($_REQUEST['dbpk']):'';
$recStart = array_key_exists('recstart',$_REQUEST)?(int)$_REQUEST['recstart']:0;
$recLimit = array_key_exists('reclimit',$_REQUEST)?(int)$_REQUEST['reclimit']:1000;

if(!preg_match('/^[a-zA-Z\d\s_-]+$/',$processingStatus)) {
    $processingStatus = '';
}
if(!$recLimit) {
    $recLimit = 1000;
}

if(strpos($uploadType,'-')){
    $tok = explode('-',$uploadType);
    if($tok){
        $uspid = (int)$tok[0];
        $uploadType = (int)$tok[1];
    }
}
else{
    $uploadType = (int)$uploadType;
}

$DIRECTUPLOAD = 1;
$FILEUPLOAD = 3;
$STOREDPROCEDURE = 4;
$SCRIPTUPLOAD = 5;
$DWCAUPLOAD = 6;
$SKELETAL = 7;
$IPTUPLOAD = 8;
$NFNUPLOAD = 9;
$SYMBIOTA = 10;

$specUploadManager = new SpecUpload();

if($isEditor){
    if($action === 'Automap Fields'){
        $autoMap = true;
    }
    elseif($action === 'Save Edits'){
        if($specUploadManager->editUploadProfile($_POST)){
            $statusStr = 'SUCCESS: Edits to import profile have been applied';
        }
        else{
            $statusStr = $specUploadManager->getErrorStr();
        }
        $action = '';
        $uploadType = 0;
    }
    elseif($action === 'Create Profile'){
        if($specUploadManager->createUploadProfile($_POST)){
            $statusStr = 'SUCCESS: New upload profile added';
        }
        else{
            $statusStr = $specUploadManager->getErrorStr();
        }
        $action = '';
        $uploadType = 0;
    }
    elseif($action === 'Delete Profile'){
        if($specUploadManager->deleteUploadProfile($uspid)){
            $statusStr = 'SUCCESS: Upload Profile Deleted';
        }
        else{
            $statusStr = $specUploadManager->getErrorStr();
        }
        $action = '';
        $uploadType = 0;
    }
}

$specManager = new SpecProcessorManager();
$specUploadManager = new SpecUpload();
$duManager = new SpecUploadBase();
if($uploadType === $DIRECTUPLOAD){
    $duManager = new SpecUploadDirect();
}
elseif($uploadType === $FILEUPLOAD || $uploadType === $NFNUPLOAD){
    $duManager = new SpecUploadFile();
    $duManager->setUploadFileName($ulPath);
}
elseif($uploadType === $SKELETAL){
    $duManager = new SpecUploadFile();
    $duManager->setUploadFileName($ulPath);
    $matchCatNum = true;
}
elseif($uploadType === $DWCAUPLOAD || $uploadType === $IPTUPLOAD || $uploadType === $SYMBIOTA){
    $duManager = new SpecUploadDwca();
    $duManager->setBaseFolderName($ulPath);
    $duManager->setIncludeIdentificationHistory($importIdent);
    $duManager->setIncludeImages($importImage);
}

$duManager->setCollId($collid);
$duManager->setUspid($uspid);
$duManager->setUploadType($uploadType);
$duManager->setMatchCatalogNumber($matchCatNum);
$duManager->setMatchOtherCatalogNumbers($matchOtherCatNum);
$duManager->setVerifyImageUrls($verifyImages);
$duManager->setProcessingStatus($processingStatus);
$specUploadManager->setCollId($collid);
$specUploadManager->setUspid($uspid);

$statusStr = '';
$isLiveData = false;
if($uploadType){
    $duManager->readUploadParameters();

    if($duManager->getCollInfo('managementtype') === 'Live Data') {
        $isLiveData = true;
    }

    if(array_key_exists('sf',$_POST)){
        if($action === 'Reset Field Mapping'){
            $statusStr = $duManager->deleteFieldMap();
        }
        else{
            $targetFields = $_POST['tf'];
            $sourceFields = $_POST['sf'];
            $fieldMap = array();
            for($x = 0, $xMax = count($targetFields); $x< $xMax; $x++){
                if($targetFields[$x]){
                    $tField = $targetFields[$x];
                    if($tField === 'unmapped') {
                        $tField .= '-' . $x;
                    }
                    $fieldMap[$tField]['field'] = $sourceFields[$x];
                }
            }
            if($dbpk) {
                $fieldMap['dbpk']['field'] = $dbpk;
            }
            $duManager->setFieldMap($fieldMap);

            if(array_key_exists('ID-sf',$_POST)){
                $targetIdFields = $_POST['ID-tf'];
                $sourceIdFields = $_POST['ID-sf'];
                $fieldIdMap = array();
                for($x = 0, $xMax = count($targetIdFields); $x< $xMax; $x++){
                    if($targetIdFields[$x]){
                        $tIdField = $targetIdFields[$x];
                        if($tIdField === 'unmapped') {
                            $tIdField .= '-' . $x;
                        }
                        $fieldIdMap[$tIdField]['field'] = $sourceIdFields[$x];
                    }
                }
                $duManager->setIdentFieldMap($fieldIdMap);
            }
            if(array_key_exists('IM-sf',$_POST)){
                $targetImFields = $_POST['IM-tf'];
                $sourceImFields = $_POST['IM-sf'];
                $fieldImMap = array();
                for($x = 0, $xMax = count($targetImFields); $x< $xMax; $x++){
                    if($targetImFields[$x]){
                        $tImField = $targetImFields[$x];
                        if($tImField === 'unmapped') {
                            $tImField .= '-' . $x;
                        }
                        $fieldImMap[$tImField]['field'] = $sourceImFields[$x];
                    }
                }
                $duManager->setImageFieldMap($fieldImMap);
            }
        }
        if($action === 'Save Mapping'){
            $statusStr = $duManager->saveFieldMap(array_key_exists('profiletitle',$_POST)?$_POST['profiletitle']:'');
            if(!$uspid) {
                $uspid = $duManager->getUspid();
            }
        }
    }
    $duManager->loadFieldMap();
}
$specUploadManager->readUploadParameters();
?>
<script>
    $(document).ready(function() {
        <?php echo (($uspid && $action)?'adjustParameterForm()':''); ?>
    });

    function checkUploadListForm(f){
        if(f.uspid.length == null){
            if(f.uspid.checked) {
                return true;
            }
        }
        else{
            const radioCnt = f.uspid.length;
            for(let counter = 0; counter < radioCnt; counter++){
                if (f.uspid[counter].checked) {
                    return true;
                }
            }
        }
        alert("Please select an Upload Option");
        return false;
    }

    function checkParameterForm(f){
        if(f.title.value === ""){
            alert("Profile title is required");
            return false;
        }
        else if(f.uploadtype.value === ""){
            alert("Select Upload Type");
            return false;
        }
        return true;
    }

    function adjustParameterForm(){
        document.getElementById("platformDiv").style.display='none';
        document.getElementById("serverDiv").style.display='none';
        document.getElementById("portDiv").style.display='none';
        document.getElementById("codeDiv").style.display='none';
        document.getElementById("pathDiv").style.display='none';
        document.getElementById("pkfieldDiv").style.display='none';
        document.getElementById("usernameDiv").style.display='none';
        document.getElementById("passwordDiv").style.display='none';
        document.getElementById("schemanameDiv").style.display='none';
        document.getElementById("cleanupspDiv").style.display='none';
        document.getElementById("querystrDiv").style.display='none';
        const selValue = Number(document.parameterform.uploadtype.value);
        if(selValue === 1){
            document.getElementById("platformDiv").style.display='block';
            document.getElementById("serverDiv").style.display='block';
            document.getElementById("portDiv").style.display='block';
            document.getElementById("usernameDiv").style.display='block';
            document.getElementById("passwordDiv").style.display='block';
            document.getElementById("schemanameDiv").style.display='block';
            document.getElementById("cleanupspDiv").style.display='block';
            document.getElementById("querystrDiv").style.display='block';
        }
        else if(selValue === 2){
            document.getElementById("serverDiv").style.display='block';
            document.getElementById("portDiv").style.display='block';
            document.getElementById("codeDiv").style.display='block';
            document.getElementById("pathDiv").style.display='block';
            document.getElementById("pkfieldDiv").style.display='block';
            document.getElementById("schemanameDiv").style.display='block';
            document.getElementById("cleanupspDiv").style.display='block';
            document.getElementById("querystrDiv").style.display='block';
        }
        else if(selValue === 3){
            document.getElementById("cleanupspDiv").style.display='block';
        }
        else if(selValue === 4){
            document.getElementById("cleanupspDiv").style.display='block';
            document.getElementById("querystrDiv").style.display='block';
        }
        else if(selValue === 5){
            document.getElementById("cleanupspDiv").style.display='block';
            document.getElementById("querystrDiv").style.display='block';
        }
        else if(selValue === 6){
            document.getElementById("cleanupspDiv").style.display='block';
        }
        else if(selValue === 7){
            document.getElementById("cleanupspDiv").style.display='block';
        }
        else if(selValue === 8 || selValue === 10){
            document.getElementById("pathDiv").style.display='block';
            document.getElementById("cleanupspDiv").style.display='block';
        }
    }

    function verifyFileUploadForm(f){
        let fileName = "";
        if(f.uploadfile){
            if(f.uploadfile && f.uploadfile.value){
                fileName = f.uploadfile.value;
            }
            if(fileName === ""){
                alert("File path is empty. Please select the file that is to be loaded.");
                return false;
            }
            else{
                const ext = fileName.split('.').pop();
                if(ext === 'csv' || ext === 'CSV') {
                    return true;
                }
                else if(ext === 'zip' || ext === 'ZIP') {
                    return true;
                }
                else if(ext === 'txt' || ext === 'TXT') {
                    return true;
                }
                else if(ext === 'tab' || ext === 'tab') {
                    return true;
                }
                else if(fileName.substring(0,4) === 'http') {
                    return true;
                }
                else{
                    alert("File must be comma separated (.csv), tab delimited (.txt or .tab), ZIP file (.zip), or a URL to an IPT Resource");
                    return false;
                }
            }
        }
        return true;
    }

    function verifyImageSize(inputObj){
        const file = inputObj.files[0];
        if(file.size > <?php echo ($GLOBALS['MAX_UPLOAD_FILESIZE'] * 1000 * 1000); ?>){
            let msg = "Import file " + file.name + " (" + Math.round(file.size / 100000) / 10 + "MB) is larger than is allowed (current limit: <?php echo $GLOBALS['MAX_UPLOAD_FILESIZE']; ?>MB).";
            if(file.name.slice(-3) !== "zip") {
                msg = msg + " Note that import file size can be reduced by compressing within a zip file. ";
            }
            alert(msg);
        }
    }

    function verifyMappingForm(f){
        const sfArr = [];
        const idSfArr = [];
        const imSfArr = [];
        const tfArr = [];
        const idTfArr = [];
        const imTfArr = [];
        let lacksCatalogNumber = true;
        let possibleMappingErr = false;
        for(let i=0; i<f.length; i++){
            const obj = f.elements[i];
            if(obj.name === "sf[]"){
                if(sfArr.indexOf(obj.value) > -1){
                    alert("ERROR: Source field names must be unique (duplicate field: "+obj.value+")");
                    return false;
                }
                sfArr[sfArr.length] = obj.value;
                if(!possibleMappingErr){
                    if(isNumeric(obj.value)){
                        possibleMappingErr = true;
                    }
                    if(obj.value.length > 7){
                        if(isNumeric(obj.value.substring(5))){
                            possibleMappingErr = true;
                        }
                        else if(obj.value.slice(-5) === "aceae" || obj.value.slice(-4) === "idae"){
                            possibleMappingErr = true;
                        }
                    }
                }
            }
            else if(obj.name === "ID-sf[]"){
                if(Number(f.importident.value) === 1){
                    if(idSfArr.indexOf(obj.value) > -1){
                        alert("ERROR: Source field names must be unique (Identification: "+obj.value+")");
                        return false;
                    }
                    idSfArr[idSfArr.length] = obj.value;
                }
            }
            else if(obj.name === "IM-sf[]"){
                if(Number(f.importimage.value) === 1){
                    if(imSfArr.indexOf(obj.value) > -1){
                        alert("ERROR: Source field names must be unique (Image: "+obj.value+")");
                        return false;
                    }
                    imSfArr[imSfArr.length] = obj.value;
                }
            }
            else if(obj.value !== "" && obj.value !== "unmapped"){
                if(obj.name === "tf[]"){
                    if(tfArr.indexOf(obj.value) > -1){
                        alert("ERROR: Can't map to the same target field more than once ("+obj.value+")");
                        return false;
                    }
                    tfArr[tfArr.length] = obj.value;
                }
                else if(obj.name === "ID-tf[]"){
                    if(Number(f.importident.value) === 1){
                        if(idTfArr.indexOf(obj.value) > -1){
                            alert("ERROR: Can't map to the same target field more than once (Identification: "+obj.value+")");
                            return false;
                        }
                        idTfArr[idTfArr.length] = obj.value;
                    }
                }
                else if(obj.name === "IM-tf[]"){
                    if(Number(f.importimage.value) === 1){
                        if(imTfArr.indexOf(obj.value) > -1){
                            alert("ERROR: Can't map to the same target field more than once (Images: "+obj.value+")");
                            return false;
                        }
                        imTfArr[imTfArr.length] = obj.value;
                    }
                }
            }
            if(lacksCatalogNumber && obj.name === "tf[]"){
                if(obj.value === "catalognumber"){
                    lacksCatalogNumber = false;
                }
            }
        }
        if(lacksCatalogNumber && Number(f.uploadtype.value) === 7){
            alert("ERROR: Catalog Number is required for Skeletal File Uploads");
            return false;
        }
        if(possibleMappingErr){
            return confirm("Does the first row of the input file contain the column names? It appears that you may be mapping directly to the first row of active data rather than a header row. If so, the first row of data will be lost and some columns might be skipped. Select OK to proceed, or cancel to abort");
        }
        return true;
    }

    function verifySaveMapping(f){
        if(f.uspid.value === "" && f.profiletitle.value === ""){
            $("#newProfileNameDiv").show();
            alert("Enter a profile name and click the Save Map button to create a new Upload Profile");
            return false;
        }
        return true;
    }

    function pkChanged(selObj){
        if(selObj.value){
            $("#mdiv").show();
        }
        else{
            $("#mdiv").hide();
        }
    }
</script>
<div>
    <?php
    if($statusStr){
        echo '<hr />';
        echo '<div>' . $statusStr . '</div>';
        echo '<hr />';
    }
    $recReplaceMsg = '<span style="color:orange"><b>Caution:</b></span> Matching records will be replaced with incoming records';
    if($GLOBALS['SYMB_UID']){
        if($isEditor && $collid){
            echo '<div style="font-weight:bold;font-size:130%;">'.$duManager->getCollInfo('name').'</div>';
            echo '<div style="margin:0 0 15px 15px;"><b>Last Upload Date:</b> '.($duManager->getCollInfo('uploaddate')?:'not recorded').'</div>';
            if($action === 'addprofile' || $action === 'editprofile') {
                ?>
                <div style="clear:both;">
                    <fieldset>
                        <legend><b>Upload Parameters</b></legend>
                        <div style="float:right;">
                            <?php
                            echo '<a href="dataupload.php?collid='.$collid.'">View All</a> ';
                            ?>
                        </div>
                        <form name="parameterform" action="index.php" method="post" onsubmit="return checkParameterForm(this)">
                            <div id="updatetypeDiv">
                                <b>Upload Type:</b>
                                <select name="uploadtype" onchange="adjustParameterForm()" <?php echo ($uspid?'DISABLED':''); ?>>
                                    <option value="">Select an Upload Type</option>
                                    <option value="">----------------------------------</option>
                                    <?php
                                    $uploadType = (int)$specUploadManager->getUploadType();
                                    echo '<option value="'.$DWCAUPLOAD.'" '.($uploadType === $DWCAUPLOAD?'SELECTED':'').'>Darwin Core Archive Manual Upload</option>';
                                    echo '<option value="'.$IPTUPLOAD.'" '.($uploadType === $IPTUPLOAD?'SELECTED':'').'>IPT Resource / Darwin Core Archive Provider</option>';
                                    echo '<option value="'.$FILEUPLOAD.'" '.($uploadType === $FILEUPLOAD?'SELECTED':'').'>File Upload</option>';
                                    echo '<option value="'.$SKELETAL.'" '.($uploadType === $SKELETAL?'SELECTED':'').'>Skeletal File Upload</option>';
                                    echo '<option value="'.$SYMBIOTA.'" '.($uploadType === $SYMBIOTA?'SELECTED':'').'>Symbiota Portal</option>';
                                    echo '<option value="'.$NFNUPLOAD.'" '.($uploadType === $NFNUPLOAD?'SELECTED':'').'>Notes for Nature File Upload</option>';
                                    echo '<option value="">......................................</option>';
                                    echo '<option value="'.$DIRECTUPLOAD.'" '.($uploadType === $DIRECTUPLOAD?'SELECTED':'').'>Direct Database Mapping</option>';
                                    echo '<option value="'.$STOREDPROCEDURE.'" '.($uploadType === $STOREDPROCEDURE?'SELECTED':'').'>Stored Procedure</option>';
                                    echo '<option value="'.$SCRIPTUPLOAD.'" '.($uploadType === $SCRIPTUPLOAD?'SELECTED':'').'>Script Upload</option>';
                                    ?>
                                </select>
                            </div>
                            <div id="titleDiv">
                                <b>Title:</b>
                                <input name="title" type="text" value="<?php echo $specUploadManager->getTitle(); ?>" style="width:400px;" maxlength="45" />
                            </div>
                            <div id="platformDiv" style="display:none">
                                <b>Database Platform:</b>
                                <select name="platform">
                                    <option value="">None Selected</option>
                                    <option value="">--------------------------------------------</option>
                                    <option value="mysql" <?php echo ($specUploadManager->getPlatform() === 'mysql'?'SELECTED':''); ?>>MySQL Database</option>
                                </select>
                            </div>
                            <div id="serverDiv" style="display:none">
                                <b>Server:</b>
                                <input name="server" type="text" size="50" value="<?php echo $specUploadManager->getServer(); ?>" style="width:400px;" />
                            </div>
                            <div id="portDiv" style="display:none">
                                <b>Port:</b>
                                <input name="port" type="text" value="<?php echo $specUploadManager->getPort(); ?>" />
                            </div>
                            <div id="pathDiv" style="display:none">
                                <b>Path:</b>
                                <input name="path" type="text" size="50" value="<?php echo $specUploadManager->getPath(); ?>" style="width:400px;" />
                            </div>
                            <div id="codeDiv" style="display:none">
                                <b>Code:</b>
                                <input name="code" type="text" value="<?php echo $specUploadManager->getCode(); ?>" />
                            </div>
                            <div id="pkfieldDiv" style="display:none">
                                <b>Primary Key Field:</b>
                                <input name="pkfield" type="text" value="<?php echo $specUploadManager->getPKField(); ?>" />
                            </div>
                            <div id="usernameDiv" style="display:none">
                                <b>Username:</b>
                                <input name="username" type="text" value="<?php echo $specUploadManager->getUsername(); ?>" />
                            </div>
                            <div id="passwordDiv" style="display:none">
                                <b>Password:</b>
                                <input name="password" type="text" value="<?php echo $specUploadManager->getPassword(); ?>" />
                            </div>
                            <div id="schemanameDiv" style="display:none">
                                <b>Schema Name:</b>
                                <input name="schemaname" type="text" size="65" value="<?php echo $specUploadManager->getSchemaName(); ?>" />
                            </div>
                            <div id="cleanupspDiv" style="display:none">
                                <b>Stored Procedure:</b>
                                <input name="cleanupsp" type="text" size="40" value="<?php echo $specUploadManager->getStoredProcedure(); ?>" style="width:400px;" />
                            </div>
                            <div id="querystrDiv" style="display:none">
                                <b>Query/Command String: </b><br/>
                                <textarea name="querystr" cols="75" rows="6" ><?php echo $specUploadManager->getQueryStr(); ?></textarea>
                            </div>
                            <div id="existingrecordsDiv" style="display:block;margin-top:2px;">
                                <b>Existing Records:</b>
                                <select name="existingrecords">
                                    <?php
                                    $existingManagement = $specUploadManager->getExistingRecordManagement();
                                    echo '<option value="update" '.($existingManagement === 'update'?'SELECTED':'').'>Update existing records (Replaces records with incoming records)</option>';
                                    echo '<option value="skip" '.($existingManagement === 'skip'?'SELECTED':'').'>Skip existing records (Do not update)</option>';
                                    ?>
                                </select>
                            </div>
                            <div style="margin:15px">
                                <input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
                                <input type="hidden" name="collid" value="<?php echo $collid;?>" />
                                <?php
                                if($uspid){
                                    ?>
                                    <input type="submit" name="action" value="Save Edits" />
                                    <?php
                                }
                                else{
                                    ?>
                                    <input type="submit" name="action" value="Create Profile" />
                                    <?php
                                }
                                ?>
                            </div>
                        </form>
                    </fieldset>
                </div>
                <?php
                if($uspid){
                    ?>
                    <form action="index.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this profile?')">
                        <fieldset>
                            <legend><b>Delete this Profile</b></legend>
                            <div>
                                <input type="hidden" name="uspid" value="<?php echo $uspid; ?>" />
                                <input type="hidden" name="collid" value="<?php echo $collid; ?>" />
                                <input type="submit" name="action" value="Delete Profile" />
                            </div>
                        </fieldset>
                    </form>
                    <?php
                }
            }
            elseif(!$uploadType){
                $profileList = $specUploadManager->getUploadList();
                ?>
                <form name="uploadlistform" action="index.php" method="post" onsubmit="return checkUploadListForm(this);">
                    <fieldset>
                        <legend style="font-weight:bold;font-size:120%;">Upload Options</legend>
                        <div style="float:right;">
                            <?php
                            echo '<a href="index.php?collid='.$collid.'&action=addprofile"><i style="height:20px;width:20px;" title="Add a New Upload Profile" class="fas fa-plus"></i></a>';
                            ?>
                        </div>
                        <?php
                        if($profileList){
                            foreach($profileList as $id => $v){
                                ?>
                                <div style="margin:10px;">
                                    <input type="radio" name="uploadtype" value="<?php echo $id.'-'.$v['uploadtype'];?>" />
                                    <?php echo $v['title']; ?>
                                    <a href="index.php?action=editprofile&collid=<?php echo $collid.'&uspid='.$id; ?>" title="View/Edit Parameters"><i style="height:20px;width:20px;" class="far fa-edit"></i></a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="6" /> Darwin Core Archive Manual Upload</div>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="8" /> IPT Resource / Darwin Core Archive Provider</div>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="3" /> File Upload</div>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="7" /> Skeletal File Upload</div>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="10" /> Symbiota Portal</div>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="9" /> Notes for Nature File Upload</div>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="1" /> Direct Database Mapping</div>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="4" /> Stored Procedure</div>
                        <div style="margin:10px;"><input type="radio" name="uploadtype" value="5" /> Script Upload</div>
                        <input type="hidden" name="collid" value="<?php echo $collid;?>" />
                        <div style="margin:10px;">
                            <input type="submit" name="action" value="Initialize Upload" />
                        </div>
                    </fieldset>
                </form>
                <?php
            }
            if($uploadType && $action !== 'addprofile' && $action !== 'editprofile'){
                if(($action === 'Start Upload') || (!$action && ($uploadType === $STOREDPROCEDURE || $uploadType === $SCRIPTUPLOAD))){
                    echo "<div style='font-weight:bold;font-size:120%'>Upload Status:</div>";
                    echo "<ul style='margin:10px;font-weight:bold;'>";
                    $duManager->uploadData($finalTransfer);
                    echo '</ul>';
                    if(!$finalTransfer){
                        ?>
                        <fieldset style="margin:15px;">
                            <legend style="<?php echo (($uploadType === $SKELETAL)?'background-color:lightgreen':''); ?>"><b>Final transfer</b></legend>
                            <div style="margin:5px;">
                                <?php
                                $reportArr = $duManager->getTransferReport();
                                echo '<div>Occurrences pending transfer: '.$reportArr['occur'];
                                if($reportArr['occur']){
                                    echo ' <a href="uploadviewer.php?collid='.$collid.'" target="_blank" title="Preview 1st 1000 Records"><i style="height:15px;width:15px;" class="fas fa-list"></i></a>';
                                    echo ' <a href="uploadcsv.php?collid='.$collid.'" target="_self" title="Download Records"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>';
                                }
                                echo '</div>';
                                echo '<div style="margin-left:15px;">';
                                echo '<div>Records to be updated: ';
                                echo $reportArr['update'];
                                if($reportArr['update']){
                                    echo ' <a href="uploadviewer.php?collid='.$collid.'&searchvar=occid:ISNOTNULL" target="_blank" title="Preview 1st 1000 Records"><i style="height:15px;width:15px;" class="fas fa-list"></i></a>';
                                    echo ' <a href="uploadcsv.php?collid='.$collid.'&searchvar=occid:ISNOTNULL" target="_self" title="Download Records"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>';
                                    if($uploadType !== $SKELETAL && $uploadType !== $NFNUPLOAD) {
                                        echo '&nbsp;&nbsp;&nbsp;<span style="color:orange"><b>Caution:</b></span> incoming records will replace existing records';
                                    }
                                }
                                echo '</div>';
                                if($uploadType !== $NFNUPLOAD || $reportArr['new']){
                                    if($uploadType === $NFNUPLOAD) {
                                        echo '<div>Mismatched records: ';
                                    }
                                    else {
                                        echo '<div>New records: ';
                                    }
                                    echo $reportArr['new'];
                                    if($reportArr['new']){
                                        echo ' <a href="uploadviewer.php?collid='.$collid.'&searchvar=occid:ISNULL" target="_blank" title="Preview 1st 1000 Records"><i style="height:15px;width:15px;" class="fas fa-list"></i></a>';
                                        echo ' <a href="uploadcsv.php?collid='.$collid.'&searchvar=occid:ISNULL" target="_self" title="Download Records"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>';
                                        if($uploadType === $NFNUPLOAD) {
                                            echo '<span style="margin-left:15px;color:orange">&gt;&gt; Records failed to link to records within this collection and will not be imported</span>';
                                        }
                                    }
                                    echo '</div>';
                                }
                                if(isset($reportArr['matchappend']) && $reportArr['matchappend']){
                                    echo '<div>Records matching on catalog number that will be appended : ';
                                    echo $reportArr['matchappend'];
                                    if($reportArr['matchappend']){
                                        echo ' <a href="uploadviewer.php?collid='.$collid.'&searchvar=matchappend" target="_blank" title="Preview 1st 1000 Records"><i style="height:15px;width:15px;" class="fas fa-list"></i></a>';
                                        echo ' <a href="uploadcsv.php?collid='.$collid.'&searchvar=matchappend" target="_self" title="Download Records"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>';
                                    }
                                    echo '</div>';
                                    echo '<div style="margin-left:15px;"><span style="color:orange;">WARNING:</span> This will result in records with duplicate catalog numbers</div>';
                                }
                                if($uploadType !== $NFNUPLOAD && $uploadType !== $SKELETAL){
                                    if(isset($reportArr['sync']) && $reportArr['sync']){
                                        echo '<div>Records that will be syncronized with central database: ';
                                        echo $reportArr['sync'];
                                        if($reportArr['sync']){
                                            echo ' <a href="uploadviewer.php?collid='.$collid.'&searchvar=sync" target="_blank" title="Preview 1st 1000 Records"><i style="height:15px;width:15px;" class="fas fa-list"></i></a>';
                                            echo ' <a href="uploadcsv.php?collid='.$collid.'&searchvar=sync" target="_self" title="Download Records"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>';
                                        }
                                        echo '</div>';
                                        echo '<div style="margin-left:15px;">These are typically records that have been originally processed within the portal, exported and integrated into a local management database, and then reimported and synchronized with the portal records by matching on catalog number.</div>';
                                        echo '<div style="margin-left:15px;"><span style="color:orange;">WARNING:</span> Incoming records will replace portal records by matching on catalog numbers. Make sure incoming records are the most up to date!</div>';
                                    }
                                    if(isset($reportArr['exist']) && $reportArr['exist']){
                                        echo '<div>Previous loaded records not matching incoming records: ';
                                        echo $reportArr['exist'];
                                        if($reportArr['exist']){
                                            echo ' <a href="uploadviewer.php?collid='.$collid.'&searchvar=exist" target="_blank" title="Preview 1st 1000 Records"><i style="height:15px;width:15px;" class="fas fa-list"></i></a>';
                                            echo ' <a href="uploadcsv.php?collid='.$collid.'&searchvar=exist" target="_self" title="Download Records"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>';
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
                                            echo ' <a href="uploadviewer.php?collid='.$collid.'&searchvar=dbpk:ISNULL" target="_blank" title="Preview 1st 1000 Records"><i style="height:15px;width:15px;" class="fas fa-list"></i></a>';
                                            echo ' <a href="uploadcsv.php?collid='.$collid.'&searchvar=dbpk:ISNULL" target="_self" title="Download Records"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>';
                                        }
                                        echo '</div>';
                                    }
                                    if(isset($reportArr['dupdbpk']) && $reportArr['dupdbpk']){
                                        echo '<div style="color:red;">Records that will be removed due to DUPLICATE Primary Identifier: ';
                                        echo $reportArr['dupdbpk'];
                                        if($reportArr['dupdbpk']){
                                            echo ' <a href="uploadviewer.php?collid='.$collid.'&searchvar=dupdbpk" target="_blank" title="Preview 1st 1000 Records"><i style="height:15px;width:15px;" class="fas fa-list"></i></a>';
                                            echo ' <a href="uploadcsv.php?collid='.$collid.'&searchvar=dupdbpk" target="_self" title="Download Records"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>';
                                        }
                                        echo '</div>';
                                    }
                                }
                                echo '</div>';
                                if(isset($reportArr['ident'])){
                                    echo '<div>Identification histories pending transfer: '.$reportArr['ident'].'</div>';
                                }
                                if(isset($reportArr['image'])){
                                    echo '<div>Records with images: '.$reportArr['image'].'</div>';
                                }

                                ?>
                            </div>
                            <form name="finaltransferform" action="index.php" method="post" style="margin-top:10px;" onsubmit="return confirm('Are you sure you want to transfer records from temporary table to central occurrence table?');">
                                <input type="hidden" name="collid" value="<?php echo $collid;?>" />
                                <input type="hidden" name="uploadtype" value="<?php echo $uploadType; ?>" />
                                <input type="hidden" name="verifyimages" value="<?php echo ($verifyImages?'1':'0'); ?>" />
                                <input type="hidden" name="processingstatus" value="<?php echo $processingStatus;?>" />
                                <input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
                                <div style="margin:5px;">
                                    <input type="submit" name="action" value="Transfer Records to Central Occurrence Table" />
                                </div>
                            </form>
                        </fieldset>
                        <?php
                    }
                }
                elseif($action === 'Transfer Records to Central Occurrence Table' || $finalTransfer){
                    echo '<ul>';
                    $duManager->finalTransfer();
                    echo '</ul>';
                }
                else{
                    $uploadTitle = $duManager->getTitle();
                    if(!$uploadTitle){
                        if($uploadType === $DWCAUPLOAD) {
                            $uploadTitle = 'Manual DwC-Archive Import';
                        }
                        elseif($uploadType === $IPTUPLOAD) {
                            $uploadTitle = 'IPT/DwC-A Provider Import';
                        }
                        elseif($uploadType === $SYMBIOTA) {
                            $uploadTitle = 'Symbiota Portal';
                        }
                        elseif($uploadType === $SKELETAL) {
                            $uploadTitle = 'Skeletal File Import';
                        }
                        elseif($uploadType === $FILEUPLOAD) {
                            $uploadTitle = 'Delimited Text File Import';
                        }
                        elseif($uploadType === $NFNUPLOAD) {
                            $uploadTitle = 'Notes from Natural Import';
                        }
                    }
                    if(!$ulPath && ($uploadType === $FILEUPLOAD || $uploadType === $SKELETAL || $uploadType === $NFNUPLOAD || $uploadType === $DWCAUPLOAD || $uploadType === $IPTUPLOAD || $uploadType === $SYMBIOTA)){
                        $ulPath = $duManager->uploadFile();
                        if(!$ulPath && $uploadType !== $IPTUPLOAD && $uploadType !== $SYMBIOTA){
                            ?>
                            <form name="fileuploadform" action="index.php" method="post" enctype="multipart/form-data" onsubmit="return verifyFileUploadForm(this)">
                                <fieldset style="width:95%;">
                                    <legend style="font-weight:bold;font-size:120%;<?php echo (($uploadType === $SKELETAL)?'background-color:lightgreen':''); ?>"><?php echo $uploadTitle;?>: Identify Data Source</legend>
                                    <div>
                                        <div style="margin:10px">
                                            <div>
                                                <input name="uploadfile" type="file" size="50" onchange="verifyImageSize(this);" />
                                            </div>
                                        </div>
                                        <div style="margin:10px;">
                                            <?php
                                            if(!$uspid && $uploadType !== $NFNUPLOAD) {
                                                echo '<input name="automap" type="checkbox" value="1" CHECKED /> <b>Automap fields</b><br/>';
                                            }
                                            ?>
                                        </div>
                                        <div style="margin:10px;">
                                            <input name="action" type="submit" value="Analyze File" />
                                            <input name="uspid" type="hidden" value="<?php echo $uspid;?>" />
                                            <input name="collid" type="hidden" value="<?php echo $collid;?>" />
                                            <input name="uploadtype" type="hidden" value="<?php echo $uploadType;?>" />
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                            <?php
                        }
                        else{
                            echo '<div style="font-weight:bold;color:red;">There was a problem loading the resource at the path entered. Please verify that the path for this resource is correct.</div>';
                        }
                    }
                    $processingList = array('unprocessed' => 'Unprocessed', 'stage 1' => 'Stage 1', 'stage 2' => 'Stage 2', 'stage 3' => 'Stage 3',
                        'pending review' => 'Pending Review', 'expert required' => 'Expert Required', 'pending review-nfn' => 'Pending Review-NfN',
                        'reviewed' => 'Reviewed', 'closed' => 'Closed');
                    if($ulPath && ($uploadType === $DWCAUPLOAD || $uploadType === $IPTUPLOAD || $uploadType === $SYMBIOTA)){
                        if($duManager->analyzeUpload()){
                            $metaArr = $duManager->getMetaArr();
                            if(isset($metaArr['occur'])){
                                ?>
                                <form name="dwcauploadform" action="index.php" method="post" onsubmit="return verifyMappingForm(this)">
                                    <fieldset style="width:95%;">
                                        <legend style="font-weight:bold;font-size:120%;"><?php echo $uploadTitle.': Field Mapping';?></legend>
                                        <div style="margin:10px;">
                                            <b>Source Unique Identifier / Primary Key (<span style="color:red">required</span>): </b>
                                            <?php
                                            $dbpk = $duManager->getDbpk();
                                            $dbpkTitle = 'Core ID';
                                            if($dbpk === 'catalognumber') {
                                                $dbpkTitle = 'Catalog Number';
                                            }
                                            elseif($dbpk === 'occurrenceid') {
                                                $dbpkTitle = 'Occurrence ID';
                                            }
                                            echo $dbpkTitle;
                                            ?>
                                            <div style="margin:10px;">
                                                <div>
                                                    <input name="importspec" value="1" type="checkbox" checked />
                                                    Import Occurrence Records (<a href="#" onclick="toggle('dwcaOccurDiv');return false;">view mapping</a>)
                                                </div>
                                                <div id="dwcaOccurDiv" style="display:none;margin:20px;">
                                                    <?php $duManager->echoFieldMapTable(true,'occur'); ?>
                                                    <div>
                                                        * Unverified mappings are displayed in yellow
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
                                                                * Unverified mappings are displayed in yellow
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
                                                                * Unverified mappings are displayed in yellow
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    else{
                                                        echo '(not present in DwC-Archive)';
                                                    }
                                                    ?>
                                                </div>
                                                <div style="margin:10px 0;">
                                                    <?php
                                                    if($uspid) {
                                                        echo '<input type="submit" name="action" value="Reset Field Mapping" />';
                                                    }
                                                    echo '<input type="submit" name="action" value="Save Mapping" onclick="return verifySaveMapping(this.form)" style="margin-left:5px" />';
                                                    if(!$uspid) {
                                                        echo ' <span id="newProfileNameDiv" style="margin-left:15px;color:orange;display:none">New profile title: <input type="text" name="profiletitle" style="width:300px" /></span>';
                                                    }
                                                    ?>

                                                </div>
                                                <div style="margin-top:30px;">
                                                    <?php
                                                    if($isLiveData){
                                                        ?>
                                                        <div>
                                                            <input name="matchcatnum" type="checkbox" value="1" checked />
                                                            Match on Catalog Number
                                                        </div>
                                                        <div>
                                                            <input name="matchothercatnum" type="checkbox" value="1" />
                                                            Match on Other Catalog Numbers
                                                        </div>
                                                        <ul style="margin-top:2px">
                                                            <li><?php echo $recReplaceMsg; ?></li>
                                                            <li>If both checkboxes are selected, matches will first be made on catalog numbers and secondarily on other catalog numbers</li>
                                                        </ul>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div style="margin:10px 0;">
                                                        <input name="verifyimages" type="checkbox" value="1" />
                                                        Verify image links
                                                    </div>
                                                    <div style="margin:10px 0;">
                                                        Processing Status:
                                                        <select name="processingstatus">
                                                            <option value="">Leave as is / No Explicit Setting</option>
                                                            <option value="">--------------------------</option>
                                                            <?php
                                                            foreach($processingList as $ps){
                                                                echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div style="margin:10px 0;">
                                                        Existing Records:
                                                        <select name="existingrecords">
                                                            <?php
                                                            $existingManagement = $duManager->getExistingRecordManagement();
                                                            echo '<option value="update" '.($existingManagement === 'update'?'SELECTED':'').'>Update existing records (Replaces records with incoming records)</option>';
                                                            echo '<option value="skip" '.($existingManagement === 'skip'?'SELECTED':'').'>Skip existing records (Do not update)</option>';
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div style="margin:10px;">
                                                        <input type="submit" name="action" value="Start Upload" />
                                                        <input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
                                                        <input type="hidden" name="collid" value="<?php echo $collid;?>" />
                                                        <input type="hidden" name="uploadtype" value="<?php echo $uploadType;?>" />
                                                        <input type="hidden" name="ulpath" value="<?php echo $ulPath;?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                                <?php
                            }
                        }
                        else if($duManager->getErrorStr()){
                            echo '<div style="font-weight:bold;">'.$duManager->getErrorStr().'</div>';
                        }
                        else{
                            echo '<div style="font-weight:bold;">Unknown error analyzing upload</div>';
                        }
                    }
                    elseif($uploadType === $NFNUPLOAD && $ulPath){
                        $duManager->analyzeUpload();
                        ?>
                        <form name="filemappingform" action="index.php" method="post" onsubmit="return verifyMappingForm(this)">
                            <fieldset style="width:95%;padding:15px">
                                <legend style="font-weight:bold;font-size:120%;">Notes from Nature File Import</legend>
                                <?php
                                $duManager->echoFieldMapTable(true, 'spec')
                                ?>
                                <div style="margin:10px 0;">
                                    Processing Status:
                                    <select name="processingstatus">
                                        <option value="">Leave as is / No Explicit Setting</option>
                                        <option value="">--------------------------</option>
                                        <?php
                                        foreach($processingList as $ps){
                                            echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div style="margin:20px;">
                                    <input type="submit" name="action" value="Start Upload" />
                                </div>
                            </fieldset>
                            <input name="matchcatnum" type="hidden" value="0" />
                            <input name="matchothercatnum" type="hidden" value="0" />
                            <input name="uspid" type="hidden" value="<?php echo $uspid;?>" />
                            <input name="collid" type="hidden" value="<?php echo $collid;?>" />
                            <input name="uploadtype" type="hidden" value="<?php echo $uploadType;?>" />
                            <input name="ulpath" type="hidden" value="<?php echo $ulPath;?>" />
                        </form>
                        <?php
                    }
                    elseif($uploadType === $DIRECTUPLOAD || (($uploadType === $FILEUPLOAD || $uploadType === $SKELETAL) && $ulPath)){
                        $duManager->analyzeUpload();
                        ?>
                        <form name="filemappingform" action="index.php" method="post" onsubmit="return verifyMappingForm(this)">
                            <fieldset style="width:95%;">
                                <legend style="font-weight:bold;font-size:120%;<?php echo (($uploadType === $SKELETAL)?'background-color:lightgreen':''); ?>"><?php echo $uploadTitle; ?></legend>
                                <?php
                                if(!$isLiveData && $uploadType !== $SKELETAL){
                                    ?>
                                    <div style="margin:20px;">
                                        <b>Source Unique Identifier / Primary Key (<span style="color:red">required</span>): </b>
                                        <?php
                                        $dbpk = $duManager->getDbpk();
                                        $dbpkOptions = $duManager->getDbpkOptions();
                                        ?>
                                        <select name="dbpk" onchange="pkChanged(this);">
                                            <option value="">Select Source Primary Key</option>
                                            <option value="">----------------------------------</option>
                                            <?php
                                            foreach($dbpkOptions as $f){
                                                echo '<option '.($dbpk === $f?'SELECTED':'').'>'.$f.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                }
                                $displayStr = 'block';
                                if(!$isLiveData) {
                                    $displayStr = 'none';
                                }
                                if($uploadType === $SKELETAL) {
                                    $displayStr = 'block';
                                }
                                if($dbpk) {
                                    $displayStr = 'block';
                                }
                                ?>
                                <div id="mdiv" style="display:<?php echo $displayStr; ?>">
                                    <?php $duManager->echoFieldMapTable($autoMap,'spec'); ?>
                                    <div>
                                        * Unverified mappings are displayed in yellow<br/>
                                        * To learn more about mapping to Darwin Core:
                                        <div style="margin-left:15px;">
                                            <a href="https://dwc.tdwg.org/terms/" target="_blank">Darwin Core quick reference guide</a><br/>
                                        </div>
                                    </div>
                                    <div style="margin:10px;">
                                        <?php
                                        if($uspid){
                                            ?>
                                            <input type="submit" name="action" value="Reset Field Mapping" />
                                            <?php
                                        }
                                        ?>
                                        <input type="submit" name="action" value="Automap Fields" />
                                        <input type="submit" name="action" value="Verify Mapping" />
                                        <input type="submit" name="action" value="Save Mapping" onclick="return verifySaveMapping(this.form)" />
                                        <span id="newProfileNameDiv" style="margin-left:15px;color:red;display:none">
                                    New profile title: <input type="text" name="profiletitle" style="width:300px" />
                                </span>
                                    </div>
                                    <hr />
                                    <div id="uldiv" style="margin-top:30px;">
                                        <?php
                                        if($isLiveData || $uploadType === $SKELETAL){
                                            ?>
                                            <div>
                                                <input name="matchcatnum" type="checkbox" value="1" checked <?php echo ($uploadType === $SKELETAL?'DISABLED':''); ?> />
                                                Match on Catalog Number
                                            </div>
                                            <div>
                                                <input name="matchothercatnum" type="checkbox" value="1" />
                                                Match on Other Catalog Numbers
                                            </div>
                                            <ul style="margin-top:2px">
                                                <?php
                                                if($uploadType === $SKELETAL){
                                                    echo '<li>Incoming skeletal data will be appended only if targeted field is empty</li>';
                                                }
                                                else{
                                                    echo '<li>'.$recReplaceMsg.'</li>';
                                                }
                                                ?>
                                                <li>If both checkboxes are selected, matches will first be made on catalog numbers and secondarily on other catalog numbers</li>
                                            </ul>
                                            <?php
                                        }
                                        ?>
                                        <div style="margin:10px 0;">
                                            <input name="verifyimages" type="checkbox" value="1" />
                                            Verify image links from associatedMedia field
                                        </div>
                                        <div style="margin:10px 0;">
                                            Processing Status:
                                            <select name="processingstatus">
                                                <option value="">Leave as is / No Explicit Setting</option>
                                                <option value="">--------------------------</option>
                                                <?php
                                                foreach($processingList as $ps){
                                                    echo '<option value="'.$ps.'">'.ucwords($ps).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div style="margin:20px;">
                                            <input type="submit" name="action" value="Start Upload" />
                                        </div>
                                    </div>
                                    <?php
                                    if($uploadType === $SKELETAL){
                                        ?>
                                        <div style="margin:15px;background-color:lightgreen;">
                                            Skeletal Files consist of stub data that is easy to capture in bulk during the imaging process.
                                            This data is used to seed new records to which images are linked.
                                            Skeletal fields typically collected include filed by or current scientific name, country, state/province, and sometimes county, though any supported field can be included.
                                            Skeletal file uploads are similar to regular uploads though differ in several ways.
                                            <ul>
                                                <li>General file uploads typically consist of full records, while skeletal uploads will almost always be an annotated record with data for only a few selected fields</li>
                                                <li>The catalog number field is required for skeletal file uploads since this field is used to find matches on images or existing records</li>
                                                <li>In cases where a record already exists, a general file upload will completely replace the existing record with the data in the new record.
                                                    On the other hand, a skeletal upload will augment the existing record only with new field data.
                                                    Fields are only added if data does not already exist within the target field.</li>
                                                <li>If a record DOES NOT already exist, a new record will be created in both cases, but only the skeletal record will be tagged as unprocessed</li>
                                            </ul>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </fieldset>
                            <input type="hidden" name="uspid" value="<?php echo $uspid;?>" />
                            <input type="hidden" name="collid" value="<?php echo $collid;?>" />
                            <input type="hidden" name="uploadtype" value="<?php echo $uploadType;?>" />
                            <input type="hidden" name="ulpath" value="<?php echo $ulPath;?>" />
                        </form>
                        <?php
                    }
                }
            }
        }
        elseif($isEditor){
            echo '<div>ERROR: collection identifier not defined. Contact administrator</div>';
        }
        else{
            echo '<div style="font-weight:bold;font-size:120%;">ERROR: you are not authorized to upload to this collection</div>';
        }
    }
    ?>
</div>
