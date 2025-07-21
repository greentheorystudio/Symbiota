<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/PermissionsManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']): '';
$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;

$permManager = new PermissionsManager();

$isEditor = 0;		 
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
		$isEditor = 1;
	}
}

if($isEditor){
	if(array_key_exists('deladmin',$_GET)){
		$permManager->deletePermission($_GET['deladmin'],'CollAdmin',$collId);
	}
	elseif(array_key_exists('deleditor',$_GET)){
		$permManager->deletePermission($_GET['deleditor'],'CollEditor',$collId);
	}
	elseif(array_key_exists('delrare',$_GET)){
		$permManager->deletePermission($_GET['delrare'],'RareSppReader',$collId);
	}
	elseif(array_key_exists('delidenteditor',$_GET)){
		$permManager->deletePermission($_GET['delidenteditor'],'CollTaxon',$collId,$_GET['utid']);
		if(is_numeric($_GET['utid'])){
			$permManager->deletePermission($_GET['delidenteditor'],'CollTaxon',$collId,'all');
		}
	}
	elseif($action === 'Add Permissions for User'){
		$rightType = $_POST['righttype'];
		if($rightType === 'admin'){
			$permManager->addPermission($_POST['uid'], 'CollAdmin',$collId);
		}
		elseif($rightType === 'editor'){
			$permManager->addPermission($_POST['uid'], 'CollEditor',$collId);
		}
		elseif($rightType === 'rare'){
			$permManager->addPermission($_POST['uid'], 'RareSppReader',$collId);
		}
	}
	elseif($action === 'Add Identification Editor'){
		$identEditor = $_POST['identeditor'];
		$pTokens = explode(':',$identEditor);
		if($pTokens){
            $permManager->addPermission($pTokens[0],'CollTaxon',$collId,$pTokens[1]);
        }
	}
}
$collMetadataFull = $permManager->getCollectionMetadata($collId);
$collMetadata = $collMetadataFull[$collId];
$isGenObs = 0;
if($collMetadata['colltype'] === 'HumanObservation') {
    $isGenObs = 1;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $collMetadata['collectionname']; ?> Manage Permissions</title>
    <meta name="description" content="Manage permissions for collections in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<script>
		function verifyAddRights(f){
			if(f.uid.value === ""){
				alert("Please select a user from list");
				return false;
			}
			else if(f.righttype.value === ""){
				alert("Please select the permissions you wish to assign this user");
				return false;
			}
			return true;
		}
	</script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
    ?>
    <div id="mainContainer" style="padding: 10px 15px 15px;">
        <div id="breadcrumbs">
            <a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php'>Home</a> &gt;&gt;
            <a href='collprofiles.php?emode=1&collid=<?php echo $collId; ?>'>Collection Control Panel</a> &gt;&gt;
            <b>Manage Permissions</b>
        </div>
        <?php
		if($isEditor){
			$collPerms = $permManager->getCollectionEditors($collId);
			if(!$isGenObs){
				?>
				<fieldset style="margin:15px;padding:15px;">
					<legend><b>Administrators</b></legend>
					<?php 
					if(array_key_exists('admin',$collPerms)){
						?>
						<ul>
						<?php 
						$adminArr = $collPerms['admin'];
						foreach($adminArr as $uid => $uName){
							?>
							<li>
								<?php echo $uName; ?> 
								<a href="collpermissions.php?collid=<?php echo $collId.'&deladmin='.$uid; ?>" onclick="return confirm('Are you sure you want to remove administrative rights for this user?');" title="Delete permissions for this user">
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
						echo '<div style="font-weight:bold;">';
						echo 'There are no administrative permissions (excluding Super Admins)';
						echo '</div>';
					}
					?>
				</fieldset>
				<?php
			}
			?>
			<fieldset style="margin:15px;padding:15px;">
				<legend><b>Editors</b></legend>
				<?php 
				if(array_key_exists('editor',$collPerms)){
					?>
					<ul>
					<?php 
					$editorArr = $collPerms['editor'];
					foreach($editorArr as $uid => $uName){
						?>
						<li>
							<?php echo $uName; ?> 
							<a href="collpermissions.php?collid=<?php echo $collId.'&deleditor='.$uid; ?>" onclick="return confirm('Are you sure you want to remove editing rights for this user?');" title="Delete permissions for this user">
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
					echo '<div style="font-weight:bold;">';
					echo 'There are no general Editor permissions';
					echo '</div>';
				}
				?>
				<div style="margin:10px">
					*Administrators automatically inherit editing rights
				</div>
			</fieldset>
			<?php 
			if(!$isGenObs){
				?>
				<fieldset style="margin:15px;padding:15px;">
					<legend><b>Rare Species Readers</b></legend>
					<?php 
					if(array_key_exists('rarespp',$collPerms)){
						?>
						<ul>
						<?php 
						$rareArr = $collPerms['rarespp'];
						foreach($rareArr as $uid => $uName){
							?>
							<li>
								<?php echo $uName; ?> 
								<a href="collpermissions.php?collid=<?php echo $collId.'&delrare='.$uid; ?>" onclick="return confirm('Are you sure you want to remove user rights to view locality details for rare species?');" title="Delete permissions for this user">
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
						echo '<div style="font-weight:bold;">';
						echo 'There are no Sensitive Species Reader permissions';
						echo '</div>';
					}
					?>
					<div style="margin:10px">
						*Administrators and editors automatically inherit protected species viewing rights
					</div>
				</fieldset>
				<?php
			} 
			?>
			<fieldset style="margin:15px;padding:15px;">
				<legend><b>Add a User</b></legend>
				<form name="addrights" action="collpermissions.php" method="post" onsubmit="return verifyAddRights(this)">
					<div>
						<select name="uid">
							<option value="">Select User</option>
							<option value="">-----------------------------------</option>
							<?php 
							$userArr = $permManager->getUsers('confirmed');
							foreach($userArr as $uid => $uName){
								echo '<option value="'.$uid.'">'.$uName.'</option>';
							}
							?>
						</select> 
					</div>
					<div style="margin:5px 0 5px 0;">
					<?php 
					if($isGenObs){
						?>
						<input name="righttype" type="hidden" value="editor" />
						<?php 
					}
					else{
						?>
						<input name="righttype" type="radio" value="admin" /> Administrator <br/> 
						<input name="righttype" type="radio" value="editor" /> Editor <br/>
						<input name="righttype" type="radio" value="rare" /> Rare Species Reader<br/>
						<?php 
					}
					?>
					</div>
					<div style="margin:15px;">
						<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
						<input name="action" type="submit" value="Add Permissions for User" />
					</div> 
				</form>
			</fieldset>
			<?php 
		}
		else{
			echo '<div style="font-weight:bold;">';
			echo 'Unauthorized to view this page. You must have administrative right for this collection.';
			echo '</div>';
		} 
		?>
	</div>
	<?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
