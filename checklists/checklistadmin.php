<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistAdmin.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']): '';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;
$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']): '';

$clManager = new ChecklistAdmin();
if(!$clid && isset($_POST['delclid'])) {
    $clid = (int)$_POST['delclid'];
}
$clManager->setClid($clid);

if($action === 'SubmitAdd' && $GLOBALS['VALID_USER']){
	$newClid = $clManager->createChecklist($_POST);
	header('Location: checklist.php?clid=' .$newClid. '&emode=1');
}

$statusStr = '';
$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
	$isEditor = 1;

	if($action === 'SubmitEdit'){
		$clManager->editMetaData($_POST);
		if(array_key_exists('footprintwkt',$_POST) && $_POST['footprintwkt'] !== ''){
            $clManager->savePolygon($_POST['footprintwkt']);
        }
		header('Location: checklist.php?clid='.$clid.'&pid='.$pid);
	}
	elseif($action === 'DeleteCheck'){
		$statusStr = $clManager->deleteChecklist($_POST['delclid']);
		if($statusStr === true) {
            header('Location: ../index.php');
        }
	}
	elseif($action === 'Addeditor'){
		$statusStr = $clManager->addEditor($_POST['editoruid']);
	}
	elseif(array_key_exists('deleteuid',$_REQUEST)){
		$statusStr = $clManager->deleteEditor($_REQUEST['deleteuid']);
	}
	elseif($action && array_key_exists('clidadd',$_POST)){
		$statusStr = $clManager->addChildChecklist($_POST['clidadd']);
	}
	elseif($action && array_key_exists('cliddel',$_GET)){
		$statusStr = $clManager->deleteChildChecklist($_GET['cliddel']);
	}
}
$clArray = $clManager->getMetaData();
$defaultArr = array();
if($clArray['defaultsettings']){
	$defaultArr = json_decode($clArray['defaultsettings'], true);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklist Administration</title>
    <meta name="description" content="Manage checklist content and configurations">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/tiny_mce/tiny_mce.js" type="text/javascript"></script>
	<script type="text/javascript">
        let clid = <?php echo $clid; ?>;
        let tabIndex = <?php echo $tabIndex; ?>;
    </script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/checklists.checklistadmin.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>

<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="breadcrumbs">
	<a href="../index.php">Home</a> &gt;&gt;
	<a href="checklist.php?clid=<?php echo $clid.'&pid='.$pid; ?>">Return to Checklist</a> &gt;&gt;
	<b> Checklist Administration</b>
</div>

<div id="mainContainer" style="padding: 10px 15px 15px;">
<div style="color:#990000;font-weight:bold;margin:0 10px 10px 0;">
	<a href="checklist.php?clid=<?php echo $clid.'&pid='.$pid; ?>">
		<?php echo $clManager->getClName(); ?>
	</a>
</div>
<?php
if($statusStr){
	?>
	<hr />
	<div style="margin:20px;font-weight:bold;color:red;">
		<?php echo $statusStr; ?>
	</div>
	<hr />
<?php
}

if($clid && $isEditor){
	?>
	<div id="tabs" style="margin:10px;">
        <ul>
            <li><a href="#admintab"><span>Admin</span></a></li>
            <li><a href="checklistadminmeta.php?clid=<?php echo $clid.'&pid='.$pid; ?>"><span>Description</span></a></li>
            <li><a href="checklistadminchildren.php?clid=<?php echo $clid.'&pid='.$pid; ?>"><span>Related Checklists</span></a></li>
        </ul>
        <div id="admintab">
            <div style="margin:20px;">
                <div style="font-weight:bold;">Current Editors</div>
                <?php
                $editorArr = $clManager->getEditors();
                if($editorArr){
                    ?>
                    <ul>
                        <?php
                        foreach($editorArr as $uid => $uName){
                            ?>
                            <li>
                                <?php echo $uName; ?>
                                <a href="checklistadmin.php?clid=<?php echo $clid.'&deleteuid='.$uid.'&pid='.$pid.'&tabindex='.$tabIndex; ?>" onclick="return confirm('Are you sure you want to remove editing rights for this user?');" title="Delete this user">
                                    <i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                <?php
                }
                else{
                    echo "<div>No one has been explicitly assigned as an editor</div>\n";
                }
                ?>
                <fieldset style="margin:40px 5px;padding:15px;">
                    <legend><b>Add New User</b></legend>
                    <form name="adduser" action="checklistadmin.php" method="post">
                        <div>
                            <select name="editoruid">
                                <option value="">Select User</option>
                                <option value="">--------------------</option>
                                <?php
                                $userArr = $clManager->getUserList();
                                foreach($userArr as $uid => $uName){
                                    echo '<option value="'.$uid.'">'.$uName.'</option>';
                                }
                                ?>
                            </select>
                            <input name="submit" type="submit" value="Add Editor" />
                            <input type="hidden" name="submitaction" value="Addeditor" />
                            <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                            <input type="hidden" name="clid" value="<?php echo $clid; ?>" />
                        </div>
                    </form>
                </fieldset>
            </div>
            <hr/>
            <div style="margin:20px;">
                <div style="font-weight:bold;">Inventory Project Assignments</div>
                <ul>
                    <?php
                    $projArr = $clManager->getInventoryProjects();
                    if($projArr){
                        foreach($projArr as $pid => $pName){
                            echo '<li>';
                            echo '<a href="../projects/index.php?pid='.$pid.'">'.$pName.'</a>';
                            echo '</li>';
                        }
                    }
                    else{
                        echo '<li>Checklist has not been assigned to any inventory projects</li>';
                    }
                    ?>
                </ul>
            </div>
            <hr/>
            <div style="margin:20px;">
                <div style="font-weight:bold;">Permanently Remove Checklist</div>
                <div style="margin:10px;">
                    Before a checklist can be deleted, all editors (except yourself) and inventory project assignments must be removed.
                    Inventory project assignments can only be removed by active managers of the project or a system administrator. <br/>
                    <b>WARNING: Action cannot be undone.</b>
                </div>
                <div style="margin:15px;">
                    <form action="checklistadmin.php" method="post" name="deleteclform" onsubmit="return window.confirm('Are you sure you want to permanently remove checklist? This action cannot be undone!')">
                        <input name="delclid" type="hidden" value="<?php echo $clid; ?>" />
                        <input name="submit" type="submit" value="Delete Checklist" <?php echo (($projArr || count($editorArr) > 1)?'DISABLED':''); ?> />
                        <input type="hidden" name="submitaction" value="DeleteCheck" />
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}
elseif($clid) {
    echo '<div><span style="font-weight:bold;">Error:</span> You do not have administrative permission for this checklist</div>';
}
else {
    echo '<div><span style="font-weight:bold;">Error:</span> Checklist identifier not set</div>';
}
?>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>

</body>
</html>
