<?php
/** @var int $collid */
/** @var int $isEditor */
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../classes/ImageProcessor.php');

$spprid = array_key_exists('spprid',$_REQUEST)?(int)$_REQUEST['spprid']:0;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$specManager = new SpecProcessorManager();
$specManager->setCollId($collid);

$fileName = '';

if($isEditor){
    if($action === 'Analyze Image Data File'){
        if($_POST['projecttype'] === 'file'){
            $imgProcessor = new ImageProcessor();
            $fileName = $imgProcessor->loadImageFile();
        }
    }
    elseif($action === 'Save Image Profile'){
        if($_POST['spprid']){
            $specManager->editProject($_POST);
        }
        else{
            $specManager->addProject($_POST);
        }
    }
    elseif($action === 'Delete Image Profile'){
        $specManager->deleteProject($_POST['sppriddel']);
    }
}

if($spprid) {
    $specManager->setProjVariables($spprid);
}

$globalImageRootPath = $GLOBALS['IMAGE_ROOT_PATH'] ?? '';
$globalImageRootUrl = $GLOBALS['IMAGE_ROOT_URL'] ?? '';
$globalImageWebWidth = $GLOBALS['IMG_WEB_WIDTH'] ?? 1400;
$globalImageTnWidth = $GLOBALS['IMG_TN_WIDTH'] ?? 200;
$globalImageLgWidth = $GLOBALS['IMG_LG_WIDTH'] ?? 3200;
?>
<script>
    $(function() {
        const dialogArr = ["speckeypattern", "sourcepath"];
        let dialogStr = "";
        for(let i=0;i<dialogArr.length;i++){
            dialogStr = dialogArr[i]+"info";
            $( "#"+dialogStr+"dialog" ).dialog({
                autoOpen: false,
                modal: true,
                position: { my: "left top", at: "right bottom", of: "#"+dialogStr }
            });

            $( "#"+dialogStr ).click(function() {
                $( "#"+this.id+"dialog" ).dialog( "open" );
            });
        }

    });

    function uploadTypeChanged(){
        const uploadType = document.getElementById('projecttype').value;
        if(uploadType === 'local'){
            document.getElementById("titleDiv").style.display = "flex";
            document.getElementById("specKeyPatternDiv").style.display = "flex";
            document.getElementById("sourcePathDiv").style.display = "flex";
            document.getElementById("thumbnailDiv").style.display = "flex";
            document.getElementById("largeImageDiv").style.display = "flex";
            document.getElementById("sourcePathInfoOther").style.display = "block";
            $("#chooseFileDiv").hide();
            if($("[name='sourcepath']").val() === "-- Use Default Path --") {
                $("[name='sourcepath']").val("");
            }
            $("#profileEditSubmit").val("Save Image Profile");
            $("#submitDiv").show();
        }
        else if(uploadType === 'file'){
            document.getElementById("titleDiv").style.display = "none";
            document.getElementById("specKeyPatternDiv").style.display = "none";
            document.getElementById("sourcePathDiv").style.display = "none";
            document.getElementById("thumbnailDiv").style.display = "none";
            document.getElementById("largeImageDiv").style.display = "none";
            document.getElementById("sourcePathInfoOther").style.display = "none";
            $("#chooseFileDiv").show();
            $("#profileEditSubmit").val("Analyze Image Data File");
            $("#submitDiv").show();
        }
        else{
            document.getElementById("titleDiv").style.display = "none";
            document.getElementById("specKeyPatternDiv").style.display = "none";
            document.getElementById("sourcePathDiv").style.display = "none";
            document.getElementById("thumbnailDiv").style.display = "none";
            document.getElementById("largeImageDiv").style.display = "none";
            document.getElementById("sourcePathInfoOther").style.display = "none";
            document.getElementById("chooseFileDiv").style.display = "none";
            document.getElementById("submitDiv").style.display = "none";
        }
    }

    function validateProjectForm(f){
        if(f.projecttype.value === ""){
            alert("Image Mapping/Import type must be selected");
            return false;
        }
        if(f.projecttype.value !== 'file'){
            if(f.speckeypattern.value === ""){
                alert("Pattern matching term must have a value");
                return false;
            }
            if(f.speckeypattern.value.indexOf("(") < 0 || f.speckeypattern.value.indexOf(")") < 0){
                alert("Catalog portion of pattern matching term must be enclosed in parenthesis");
                return false;
            }
        }
        if(f.projecttype.value === 'file' && f.uploadfile.value === ""){
            alert("Select a CSV file to upload");
            return false;
        }
        if(f.projecttype.value === 'local'){
            if(f.title.value === ""){
                alert("Title cannot be empty");
                return false;
            }
            if(f.sourcepath.value === ""){
                alert("Image source path must have a value");
                return false;
            }
        }
        if(f.sourcepath.value === "-- Use Default Path --") {
            f.sourcepath.value = "";
        }
        return true;
    }

    function validateProcForm(f){
        if($("[name='matchcatalognumber']").prop("checked") === false && $("[name='matchothercatalognumbers']").prop("checked") === false){
            alert("At least one of the Match Term checkboxes need to be checked");
            return false;
        }
        return true;
    }

    function validateFileUploadForm(f){
        const sfArr = [];
        const tfArr = [];
        for(let i=0; i<f.length; i++){
            const obj = f.elements[i];
            if(obj.value !== ""){
                if(obj.name.indexOf("tf[") === 0){
                    if(tfArr.indexOf(obj.value) > -1){
                        alert("ERROR: Target field names must be unique (duplicate field: "+obj.value+")");
                        return false;
                    }
                    tfArr[tfArr.length] = obj.value;
                }
                if(obj.name.indexOf("sf[") === 0){
                    if(sfArr.indexOf(obj.value) > -1){
                        alert("ERROR: Source field names must be unique (duplicate field: "+obj.value+")");
                        return false;
                    }
                    sfArr[sfArr.length] = obj.value;
                }
            }
        }
        if(tfArr.indexOf("catalognumber") < 0 || tfArr.indexOf("originalurl") < 0){
            alert("Catalog Number and Large Image URL must both be mapped to an incoming field");
            return false;
        }
        return true;
    }
</script>
<div>
    <?php
    if($spprid){
        ?>
        <div style="display:flex;justify-content: flex-end;" title="Show all saved profiles or add a new one...">
            <a href="index.php?tabindex=1&collid=<?php echo $collid; ?>"><i style="height:20px;width:20px;color:green;cursor:pointer;" class="fas fa-plus"></i></a>
        </div>
        <?php
    }
    if($GLOBALS['SYMB_UID']){
        if($collid){
            if($fileName){
                ?>
                <form name="filemappingform" action="../management/processor.php" method="post" onsubmit="return validateFileUploadForm(this)">
                    <fieldset>
                        <legend><b>Image File Upload Mapping</b></legend>
                        <div style="margin:15px;">
                            <table class="styledtable" style="width:700px;font-family:Arial,serif;">
                                <tr><th>Source Field</th><th>Target Field</th></tr>
                                <?php
                                $imgProcessor = new ImageProcessor();
                                $imgProcessor->echoFileMapping($fileName);
                                ?>
                            </table>
                        </div>
                        <div style="margin:15px;">
                            <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                            <input name="tabindex" type="hidden" value="1" />
                            <input name="filename" type="hidden" value="<?php echo $fileName; ?>" />
                            <input name="submitaction" type="submit" value="Load Image Data" />
                        </div>
                    </fieldset>
                </form>
                <?php
            }
            else{
                if(!$spprid){
                    $specProjects = $specManager->getProjects();
                    if($specProjects){
                        ?>
                        <form name="sppridform" action="index.php" method="post">
                            <fieldset>
                                <legend><b>Saved Image Processing Profiles</b></legend>
                                <div style="margin:15px;">
                                    <?php
                                    foreach($specProjects as $id => $projTitle){
                                        echo '<input type="radio" name="spprid" value="'.$id.'" onchange="this.form.submit()" /> '.$projTitle.'<br/>';
                                    }
                                    ?>
                                </div>
                                <div style="margin:15px;">
                                    <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                                    <input name="tabindex" type="hidden" value="1" />
                                </div>
                            </fieldset>
                        </form>
                        <?php
                    }
                }
                $projectType = $specManager->getProjectType();
                ?>
                <div id="editdiv" style="display:<?php echo ($spprid?'none':'block'); ?>;position:relative;">
                    <form name="editproj" action="index.php" enctype="multipart/form-data" method="post" onsubmit="return validateProjectForm(this);">
                        <fieldset style="padding:15px;">
                            <legend><b><?php echo ($spprid?'Edit':'New'); ?> Profile</b></legend>
                            <?php
                            if($spprid){
                                ?>
                                <div style="display:flex;justify-content:flex-end;" onclick="toggle('editdiv');toggle('imgprocessdiv')" title="Close Editor">
                                    <i style="height:20px;width:20px;cursor:pointer;" class="far fa-edit"></i>
                                </div>
                                <input name="projecttype" type="hidden" value="<?php echo $projectType; ?>" />
                                <?php
                            }
                            else{
                                ?>
                                <div style="clear:both;width:700px;display:flex;justify-content:space-between;">
                                    <div style="width:180px;">
                                        <b>Process Type:</b>
                                    </div>
                                    <div style="margin-right:19px;">
                                        <select name="projecttype" id="projecttype" style="width:300px;" onchange="uploadTypeChanged()" <?php echo ($spprid?'DISABLED':'');?>>
                                            <option value="">----------------------</option>
                                            <option value="local">Local Image Mapping</option>
                                            <option value="file">Image Data File</option>
                                        </select>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div id="titleDiv" style="display:<?php echo ($projectType === 'local'?'flex':'none'); ?>;clear:both;width:700px;justify-content:space-between;">
                                <div style="width:180px;">
                                    <b>Title:</b>
                                </div>
                                <div style="margin-right:19px;">
                                    <input name="title" type="text" style="width:300px;" value="<?php echo $specManager->getTitle(); ?>" />
                                </div>
                            </div>
                            <div id="specKeyPatternDiv" style="display:<?php echo ($projectType?'flex':'none'); ?>;clear:both;width:700px;justify-content:space-between;">
                                <div style="width:180px;">
                                    <b>Pattern match term:</b>
                                </div>
                                <div>
                                    <input name="speckeypattern" type="text" style="width:300px;" value="<?php echo $specManager->getSpecKeyPattern(); ?>" />
                                    <a id="speckeypatterninfo" href="#" onclick="return false" title="More Information">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
                                    <div id="speckeypatterninfodialog">
                                        Regular expression needed to extract the unique identifier from source text.
                                        For example, regular expression /^(WIS-L-\d{7})\D*/ will extract catalog number WIS-L-0001234
                                        from image file named WIS-L-0001234_a.jpg. For more information on creating regular expressions,
                                        Google &quot;Regular Expression PHP Tutorial&quot;
                                    </div>
                                </div>
                            </div>
                            <div id="sourcePathDiv" style="display:<?php echo ($projectType === 'local'?'flex':'none'); ?>;clear:both;width:700px;justify-content:space-between;">
                                <div style="width:180px;">
                                    <b>Image source path:</b>
                                </div>
                                <div>
                                    <input name="sourcepath" type="text" style="width:400px;" value="<?php echo $specManager->getSourcePath(); ?>" />
                                    <a id="sourcepathinfo" href="#" onclick="return false" title="More Information">
                                        <i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i>
                                    </a>
                                    <div id="sourcepathinfodialog">
                                        <div id="sourcePathInfoOther" style="display:block;">
                                            Server path or URL to source image location. Server paths should be absolute and writable to web server (e.g. apache).
                                            If a URL (e.g. http://) is supplied, the web server needs to be configured to publically list
                                            all files within the directory, or the html output can simply list all images within anchor tags.
                                            In all cases, scripts will attempt to crawl through all child directories.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="thumbnailDiv" style="display:<?php echo ($projectType === 'local'?'flex':'none'); ?>;clear:both;width:700px;justify-content:flex-start;">
                                <div>
                                    <b>Thumbnail:</b>
                                    <div style="margin:5px 15px;">
                                        <input name="createtnimg" type="radio" value="1" <?php echo ($specManager->getCreateTnImg() === 1?'CHECKED':''); ?> /> Create new thumbnail from source image<br/>
                                        <input name="createtnimg" type="radio" value="0" <?php echo (!$specManager->getCreateTnImg()?'CHECKED':''); ?> /> Exclude thumbnail <br/>
                                    </div>
                                </div>
                            </div>
                            <div id="largeImageDiv" style="display:<?php echo ($projectType === 'local'?'flex':'none'); ?>;clear:both;width:700px;justify-content:flex-start;">
                                <div>
                                    <b>Large Image:</b>
                                    <div style="margin:5px 15px;">
                                        <input name="createlgimg" type="radio" value="1" <?php echo ($specManager->getCreateLgImg() === 1?'CHECKED':''); ?> /> Import source image as large version<br/>
                                        <input name="createlgimg" type="radio" value="0" <?php echo (!$specManager->getCreateLgImg()?'CHECKED':''); ?> /> Exclude large version<br/>
                                    </div>
                                </div>
                            </div>
                            <div id="chooseFileDiv" style="clear:both;padding:15px 0;display:none">
                                <b>Select image data file:</b>
                                <div style="margin:5px 15px;">
                                    <input name='uploadfile' type='file' size='70' value="Choose File" />
                                </div>
                            </div>
                            <div id="submitDiv" style="clear:both;padding:25px 15px;display:<?php echo ($projectType?'block':'none'); ?>">
                                <input name="spprid" type="hidden" value="<?php echo $spprid; ?>" />
                                <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                                <input name="tabindex" type="hidden" value="1" />
                                <input id="profileEditSubmit" name="submitaction" type="hidden" value="Save Image Profile" />
                                <button type="submit">Save Profile</button>
                            </div>
                        </fieldset>
                    </form>
                    <?php
                    if($spprid){
                        ?>
                        <form id="delform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to delete this image processing profile?')" >
                            <fieldset style="padding:25px">
                                <legend><b>Delete Project</b></legend>
                                <div>
                                    <input name="sppriddel" type="hidden" value="<?php echo $spprid; ?>" />
                                    <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                                    <input name="tabindex" type="hidden" value="1" />
                                    <input name="submitaction" type="submit" value="Delete Image Profile" />
                                </div>
                            </fieldset>
                        </form>
                        <?php
                    }
                    ?>
                </div>
                <?php
                if($spprid){
                    ?>
                    <div id="imgprocessdiv" style="position:relative;">
                        <form name="imgprocessform" action="../management/processor.php" method="post" enctype="multipart/form-data" onsubmit="return validateProcForm(this);">
                            <fieldset style="padding:15px;">
                                <legend><b><?php echo $specManager->getTitle(); ?></b></legend>
                                <div style="display:flex;justify-content:flex-end;" title="Open Editor">
                                    <a href="#" onclick="toggle('editdiv');toggle('imgprocessdiv');return false;"><i style="height:20px;width:20px;cursor:pointer;" class="far fa-edit"></i></a>
                                </div>
                                <div style="margin-top:10px;clear:both;">
                                    <div style="width:200px;float:left;">
                                        <b>Pattern match term:</b>
                                    </div>
                                    <div style="float:left;">
                                        <?php echo $specManager->getSpecKeyPattern(); ?>
                                        <input type='hidden' name='speckeypattern' value='<?php echo $specManager->getSpecKeyPattern();?>' />
                                    </div>
                                </div>
                                <div style="clear:both;">
                                    <div style="width:200px;float:left;">
                                        <b>Match term on:</b>
                                    </div>
                                    <div style="float:left;">
                                        <input name="matchcatalognumber" type="checkbox" value="1" checked /> Catalog Number
                                        <input name="matchothercatalognumbers" type="checkbox" value="1" style="margin-left:30px;" /> Other Catalog Numbers
                                    </div>
                                </div>
                                <div style="clear:both;">
                                    <div>
                                        <b>Thumbnail:</b>
                                        <div style="margin:5px 15px">
                                            <input name="createtnimg" type="radio" value="1" <?php echo ($specManager->getCreateTnImg()?'CHECKED':'') ?> /> Create new thumbnail from source image<br/>
                                            <input name="createtnimg" type="radio" value="0" <?php echo (!$specManager->getCreateTnImg()?'CHECKED':'') ?> /> Exclude thumbnail <br/>
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both;">
                                    <div>
                                        <b>Large Image:</b>
                                        <div style="margin:5px 15px">
                                            <input name="createlgimg" type="radio" value="1" <?php echo ($specManager->getCreateLgImg()?'CHECKED':'') ?> /> Import source image as large version<br/>
                                            <input name="createlgimg" type="radio" value="0" <?php echo (!$specManager->getCreateLgImg()?'CHECKED':'') ?> /> Exclude large version<br/>
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both;">
                                    <div title="Unable to match primary identifer with an existing database record">
                                        <b>Missing record:</b>
                                        <div style="margin:5px 15px">
                                            <input type="radio" name="createnewrec" value="0" />
                                            Skip image import and go to next<br/>
                                            <input type="radio" name="createnewrec" value="1" CHECKED />
                                            Create empty record and link image
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both;">
                                    <div title="Image with exact same name already exists">
                                        <b>Image already exists:</b>
                                        <div style="margin:5px 15px">
                                            <input type="radio" name="imgexists" value="0" CHECKED />
                                            Skip import<br/>
                                            <input type="radio" name="imgexists" value="1" />
                                            Rename image and save both<br/>
                                            <input type="radio" name="imgexists" value="2" />
                                            Replace existing image
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both;">
                                    <div>
                                        <b>Look for and process skeletal files (must be csv or txt):</b>
                                        <div style="margin:5px 15px">
                                            <input type="radio" name="skeletalFileProcessing" value="0" CHECKED />
                                            Skip skeletal files<br/>
                                            <input type="radio" name="skeletalFileProcessing" value="1" />
                                            Process skeletal files<br/>
                                        </div>
                                    </div>
                                </div>
                                <div style="clear:both;padding:20px;">
                                    <input name="spprid" type="hidden" value="<?php echo $spprid; ?>" />
                                    <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                                    <input name="projtype" type="hidden" value="<?php echo $projectType; ?>" />
                                    <input name="tabindex" type="hidden" value="1" />
                                    <input name="submitaction" type="submit" value="Process Images" />
                                </div>
                                <div style="margin:20px;">
                                    <fieldset style="padding:15px;">
                                        <legend><b>Log Files</b></legend>
                                        <?php
                                        $logArr = $specManager->getLogListing();
                                        $GLOBALS['LOG_PATH'] = '../../content/logs/'.($projectType === 'local'?'imgProccessing':$projectType).'/';
                                        if($logArr){
                                            foreach($logArr as $logFile){
                                                echo '<div><a href="'.$GLOBALS['LOG_PATH'].$logFile.'" target="_blank">'.$logFile.'</a></div>';
                                            }
                                        }
                                        else{
                                            echo '<div>No logs exist for this collection</div>';
                                        }
                                        ?>
                                    </fieldset>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <?php
                }
            }
        }
        else{
            echo '<div>ERROR: collection identifier not defined. Contact administrator</div>';
        }
    }
    ?>
</div>
