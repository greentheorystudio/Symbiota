<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ChecklistAdmin.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=../checklists/checklistadmin.php?' . $_SERVER['QUERY_STRING']);
}

$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']: '';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']: '';

$clManager = new ChecklistAdmin();
if(!$clid && isset($_POST['delclid'])) {
    $clid = $_POST['delclid'];
}
$clManager->setClid($clid);

if($action === 'SubmitAdd'){
	$newClid = $clManager->createChecklist($_POST);
	header('Location: checklist.php?cl=' .$newClid. '&emode=1');
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
		header('Location: checklist.php?cl='.$clid.'&pid='.$pid);
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
	elseif($action === 'Add Point'){
		$statusStr = $clManager->addPoint($_POST['pointtid'],$_POST['pointlat'],$_POST['pointlng'],$_POST['notes']);
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

$voucherProjects = $clManager->getVoucherProjects();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklist Administration</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link type="text/css" href="../css/jquery-ui.css" rel="stylesheet" />
    <script src="../js/all.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
        let clid = <?php echo $clid; ?>;
        let tabIndex = <?php echo $tabIndex; ?>;
    </script>
	<script type="text/javascript" src="../js/symb/shared.js?ver=20210621"></script>
	<script type="text/javascript" src="../js/symb/checklists.checklistadmin.js?ver=20210218"></script>
</head>

<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div class="navpath">
	<a href="../index.php">Home</a> &gt;&gt;
	<a href="checklist.php?cl=<?php echo $clid.'&pid='.$pid; ?>">Return to Checklist</a> &gt;&gt;
	<b> Checklist Administration</b>
</div>

<div id='innertext'>
<div style="color:#990000;font-size:20px;font-weight:bold;margin:0 10px 10px 0;">
	<a href="checklist.php?cl=<?php echo $clid.'&pid='.$pid; ?>">
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
		<?php
		if($voucherProjects){
			?>
			<li><a href="#imgvouchertab">Add Image Voucher</a></li>
		<?php
		}
		?>
	</ul>
	<div id="admintab">
		<div style="margin:20px;">
			<div style="font-weight:bold;font-size:120%;">Current Editors</div>
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
			<div style="font-weight:bold;font-size:120%;">Inventory Project Assignments</div>
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
			<div style="font-weight:bold;font-size:120%;">Permanently Remove Checklist</div>
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
	<?php
	if($voucherProjects){
		?>
		<div id="imgvouchertab">
			<form name="addimagevoucher" action="../collections/editor/observationsubmit.php" method="get" target="_blank">
				<fieldset style="margin:15px;padding:25px;">
					<legend><b>Add Image Voucher and Link to Checklist</b></legend>
                    This form will allow you to add an image voucher linked to this checklist.<br/>
                    If not already present, Scientific name will be added to checklist.<br><br>
                    Select the voucher project to which you wish to add the voucher.
                    <div style="margin:5px;">
						<select name="collid">
							<?php
							foreach($voucherProjects as $k => $v){
								echo '<option value="'.$k.'">'.$v.'</option>';
							}
							?>
						</select><br/>
						<input type="hidden" name="clid" value="<?php echo $clid; ?>" />
					</div>
					<div style="margin:5px;">
						<input type="submit" name="submitvoucher" value="Add Image Voucher and Link to Checklist" /><br/>
					</div>
				</fieldset>
			</form>
		</div>
	<?php
	}
	?>
	</div>
<?php
}
else if(!$clid){
    echo '<div><span style="font-weight:bold;font-size:110%;">Error:</span> Checklist identifier not set</div>';
}
else{
    echo '<div><span style="font-weight:bold;font-size:110%;">Error:</span> You do not have administrative permission for this checklist</div>';
}
?>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>

</body>
</html>
