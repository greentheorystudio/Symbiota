<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/InventoryProjectManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$pid = array_key_exists('pid',$_REQUEST)?(int)$_REQUEST['pid']:0;
$editMode = array_key_exists('emode',$_REQUEST)?(int)$_REQUEST['emode']:0;
$newProj = array_key_exists('newproj',$_REQUEST)?1:0;
$projSubmit = array_key_exists('projsubmit',$_REQUEST)?$_REQUEST['projsubmit']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;
$statusStr = '';

if(!$pid && array_key_exists('proj',$_GET) && is_numeric($_GET['proj'])) {
    $pid = $_GET['proj'];
}

$projManager = new InventoryProjectManager();
if($pid) {
    $projManager->setPid($pid);
}

$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ProjAdmin',$GLOBALS['USER_RIGHTS']) && in_array($pid, $GLOBALS['USER_RIGHTS']['ProjAdmin'], true))){
    $isEditor = 1;
}

if($projSubmit){
    if($projSubmit === 'addnewproj'){
        $pid = $projManager->addNewProject($_POST);
        if(!$pid) {
            $statusStr = $projManager->getErrorStr();
        }
        if($GLOBALS['IS_ADMIN'] || (array_key_exists('ProjAdmin',$GLOBALS['USER_RIGHTS']) && in_array($pid, $GLOBALS['USER_RIGHTS']['ProjAdmin'], true))){
            $isEditor = 1;
        }
    }
    if($isEditor){
        if($projSubmit === 'subedit'){
            $projManager->submitProjEdits($_POST);
        }
        elseif($projSubmit === 'subdelete'){
            if($projManager->deleteProject($_POST['pid'])){
                $pid = 0;
            }
            else{
                $statusStr = $projManager->getErrorStr();
            }
        }
        elseif($projSubmit === 'deluid'){
            if(!$projManager->deleteManager($_GET['uid'])){
                $statusStr = $projManager->getErrorStr();
            }
        }
        elseif($projSubmit === 'Add to Manager List'){
            if(!$projManager->addManager($_POST['uid'])){
                $statusStr = $projManager->getErrorStr();
            }
        }
        elseif($projSubmit === 'Add Checklist'){
            $projManager->addChecklist($_POST['clid']);
        }
        elseif($projSubmit === 'Delete Checklist'){
            $projManager->deleteChecklist($_POST['clid']);
        }
    }
}

$projArr = $projManager->getProjectData();
$researchList = $projManager->getResearchChecklists();
$managerArr = $projManager->getManagers();
if(!$researchList && !$editMode){
    $editMode = 1;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Biotic Inventory Projects Index</title>
    <meta name="description" content="Biotic inventory projects index for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/bootstrap.min.css?ver=20221225" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <style>
        a.boxclose{
            float:right;
            width:36px;
            height:36px;
            background:transparent url('<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/spatial_close_icon.png') repeat top left;
            margin-top:-35px;
            margin-right:-35px;
            cursor:pointer;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.popupoverlay.js" type="text/javascript"></script>
    <script type="text/javascript">
        let tabIndex = <?php echo $tabIndex; ?>;

        document.addEventListener("DOMContentLoaded", function() {
            $('#tabs').tabs(
                { active: tabIndex }
            );
            $('#infobox').popup({
                transition: 'all 0.3s',
                scrolllock: true
            });
        });

        function toggleById(target){
            const obj = document.getElementById(target);
            if(obj.style.display === "none"){
                obj.style.display="block";
            }
            else {
                obj.style.display="none";
            }
        }

        function toggleResearchInfoBox(){
            $('#infobox').popup('show');
        }

        function findPos(obj){
            let curleft = 0;
            let curtop = 0;
            if(obj.offsetParent) {
                do{
                    curleft += obj.offsetLeft;
                    curtop += obj.offsetTop;
                }
                while(obj === obj.offsetParent);
            }
            return [curleft,curtop];
        }

        function validateProjectForm(f){
            if(f.projname.value === ""){
                alert("Project name field cannot be empty.");
                return false;
            }
            else if(isNaN(f.sortsequence.value)){
                alert("Sort sequence can only be a numeric value.");
                return false;
            }
            else if(f.fulldescription.value.length > 2000){
                alert("Description can only have a maximum of 2000 characters. The description is currently " + f.fulldescription.value.length + " characters long.");
                return false;
            }
            return true;
        }

        function validateChecklistForm(f){
            if(f.clid.value === ""){
                alert("Choose a checklist from the pull-down");
                return false;
            }
            return true;
        }

        function validateManagerAddForm(f){
            if(f.uid.value === ""){
                alert("Choose a user from the pull-down");
                return false;
            }
            return true;
        }

        function openSpatialViewerWindow(coordArrJson) {
            let mapWindow = open("<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/viewerWindow.php?coordJson=" + coordArrJson,"Spatial Viewer","resizable=0,width=800,height=700,left=100,top=20");
            if (mapWindow.opener == null) {
                mapWindow.opener = self;
            }
            mapWindow.addEventListener('blur', function(){
                mapWindow.close();
                mapWindow = null;
            });
        }
    </script>
    <style>
        fieldset.form-color{
            background-color:#FFF380;
            margin:15px;
            padding:20px;
        }
    </style>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
echo "<div class='navpath'>";
echo "<a href='../index.php'>Home</a> &gt;&gt; ";
if($projArr){
    echo "<a href='index.php'>Biotic Inventory Projects</a> &gt;&gt; ";
    echo '<b>'.$projArr['projname'].'</b>';
}
else{
    echo '<b>Biotic Inventory Projects</b>';
}
echo '</div>';
?>

<div id="innertext">
    <?php
    if($statusStr){
        ?>
        <hr/>
        <div style="margin:20px;font-weight:bold;color:<?php echo (stripos($statusStr,'error')!==false?'red':'green');?>;">
            <?php echo $statusStr; ?>
        </div>
        <hr/>
        <?php
    }
    if($pid || $newProj){
        if($isEditor && !$newProj){
            ?>
            <div style="float:right;" title="Toggle Editing Functions">
                <a href="#" onclick="toggleById('tabs');return false;"><i style="width:20px;height:20px;" class="fas fa-cog"></i></a>
            </div>
            <?php
        }
        if($projArr){
            ?>
            <h1><?php echo $projArr['projname']; ?></h1>
            <div style='margin: 10px;'>
                <div>
                    <b>Project Managers:</b>
                    <?php echo $projArr['managers'];?>
                </div>
                <div style='margin-top:10px;'>
                    <?php echo $projArr['fulldescription'];?>
                </div>
                <div style='margin-top:10px;'>
                    <?php echo $projArr['notes']; ?>
                </div>
            </div>
            <?php
        }
        if(($pid && $isEditor) || $newProj){
            ?>
            <div id="tabs" style="min-height:550px;margin:10px;display:<?php echo ($newProj||$editMode?'block':'none'); ?>;">
                <ul>
                    <li><a href="#mdtab"><span>Metadata</span></a></li>
                    <?php
                    if($pid){
                        ?>
                        <li><a href="managertab.php?pid=<?php echo $pid; ?>"><span>Inventory Managers</span></a></li>
                        <li><a href="checklisttab.php?pid=<?php echo $pid; ?>"><span>Checklist Management</span></a></li>
                        <?php
                    }
                    ?>
                </ul>
                <div id="mdtab">
                    <fieldset class="form-color">
                        <legend><b><?php echo ($newProj?'Add New':'Edit'); ?> Project</b></legend>
                        <form name='projeditorform' action='index.php' method='post' onsubmit="return validateProjectForm(this)">
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        Project Name:
                                    </td>
                                    <td>
                                        <input type="text" name="projname" value="<?php echo ($projArr?htmlentities($projArr['projname']):''); ?>" style="width:95%;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Managers:
                                    </td>
                                    <td>
                                        <input type="text" name="managers" value="<?php echo ($projArr?htmlentities($projArr['managers']):''); ?>" style="width:95%;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Description:
                                    </td>
                                    <td>
                                        <textarea rows="8" cols="45" name="fulldescription" maxlength="2000" style="width:95%"><?php echo ($projArr?htmlentities($projArr['fulldescription']):'');?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Notes:
                                    </td>
                                    <td>
                                        <input type="text" name="notes" value="<?php echo ($projArr?htmlentities($projArr['notes']):'');?>" style="width:95%;"/>
                                    </td>
                                </tr>
                                <?php
                                if($GLOBALS['PUBLIC_CHECKLIST']){
                                    ?>
                                    <tr>
                                        <td>
                                            Access:
                                        </td>
                                        <td>
                                            <select name="ispublic">
                                                <option value="0">Private</option>
                                                <option value="1" <?php echo ($projArr && $projArr['ispublic']?'SELECTED':''); ?>>Public</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="2">
                                        <div style="margin:15px;">
                                            <?php
                                            if($newProj){
                                                ?>
                                                <input type="submit" name="submit" value="Add New Project" />
                                                <input type="hidden" name="projsubmit" value="addnewproj" />
                                                <?php
                                            }
                                            else{
                                                ?>
                                                <input type="hidden" name="pid" value="<?php echo $pid;?>">
                                                <input type="hidden" name="projsubmit" value="subedit" />
                                                <input type="submit" name="submit" value="Submit Edits" />
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                    <?php
                    if($pid){
                        ?>
                        <fieldset class="form-color">
                            <legend><b>Delete Project</b></legend>
                            <form action="index.php" method="post" onsubmit="return confirm('Warning: Action cannot be undone! Are you sure you want to delete this inventory Project?')">
                                <input type="hidden" name="pid" value="<?php echo $pid;?>">
                                <input type="hidden" name="projsubmit" value="subdelete" />
                                <?php
                                echo '<input type="submit" name="submit" value="Delete Project" '.((count($managerArr)>1 || $researchList)?'disabled':'').' />';
                                echo '<div style="margin:10px;color:orange">';
                                if(count($managerArr) > 1){
                                    echo 'Inventory project cannot be deleted until all other managers are removed as project managers';
                                }
                                elseif($researchList){
                                    echo 'Inventory project cannot be deleted until all checklists are removed from the project';
                                }
                                echo '</div>';
                                ?>
                            </form>
                        </fieldset>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        if($pid){
            ?>
            <div style="margin:20px;">
                <?php
                if($researchList){
                    $coordJson = $projManager->getResearchCoords();
                    ?>
                    <div style="font-weight:bold;">
                        Research Checklists
                        <span onclick="toggleResearchInfoBox();" title="What is a Research Species List?" style="cursor:pointer;">
								<i style="height:15px;width:15px;" class="far fa-question-circle"></i>
							</span>
                        <?php
                        if($coordJson){
                            ?>
                            <a href="#" onclick="openSpatialViewerWindow('<?php echo $coordJson; ?>');" title="Map Checklists">
                                <i style='height:15px;width:15px;' class="fas fa-globe"></i>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    if($GLOBALS['KEY_MOD_IS_ACTIVE']){
                        ?>
                        <div style="margin-left:15px;">
                            The <i style="width: 12px;" class="fas fa-key"></i>
                            symbol opens the species list as an interactive key.
                        </div>
                        <?php
                    }
                    ?>
                    <div>
                        <ul>
                            <?php
                            foreach($researchList as $key=>$value){
                                ?>
                                <li>
                                    <a href='../checklists/checklist.php?cl=<?php echo $key. '&pid=' .$pid; ?>'>
                                        <?php echo $value; ?>
                                    </a>
                                    <?php
                                    if($GLOBALS['KEY_MOD_IS_ACTIVE']){
                                        ?>
                                        <a href='../ident/key.php?clid=<?php echo $key; ?>&pid=<?php echo $pid; ?>'>
                                            <i style='width:12px;border:0;' class="fas fa-key"></i>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
    }
    else{
        $projectArr = $projManager->getProjectList();
        if($GLOBALS['VALID_USER']){
            echo '<div><b><a href="index.php?newproj=1">Click here to create a new Biotic Inventory Project</a></b></div>';
        }
        if($projectArr){
            echo '<h1>'.$GLOBALS['DEFAULT_TITLE'].' Biotic Inventory Projects</h1>';
            foreach($projectArr as $pid => $projList){
                ?>
                <h2><a href="index.php?pid=<?php echo $pid; ?>"><?php echo $projList['projname']; ?></a></h2>
                <div style="margin:0 0 30px 15px;">
                    <div><b>Managers:</b> <?php echo ($projList['managers']?:'Not defined'); ?></div>
                    <div style='margin-top:10px;'><?php echo $projList['descr']; ?></div>
                </div>
                <?php
            }
        }
        else{
            echo '<div><b>There are no biotic inventory projects available at this time.</b></div>';
        }
    }
    ?>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>

<div id="infobox" data-role="popup" class="well" style="width:400px;height:300px;">
    <a class="boxclose infobox_close" id="boxclose"></a>
    <h2>What is a research checklist?</h2>
    <div style="margin:15px;">
        Research checklists are pre-compiled by biologists.
        This is a very controlled method for building a species list, which allows for
        specific occurrences to be linked to the species names within the checklist and thus serve as vouchers.
        Occurrence vouchers are proof that the species actually occurs in the given area. If there is any doubt, one
        can inspect these occurrences for verification or annotate the identification when necessary.
    </div>
</div>
</body>
</html>
