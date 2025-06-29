<?php 
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/InstitutionManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$iid = array_key_exists('iid',$_REQUEST)?(int)$_REQUEST['iid']:0;
$targetCollid = array_key_exists('targetcollid',$_REQUEST)?(int)$_REQUEST['targetcollid']:0;
$eMode = array_key_exists('emode',$_REQUEST)?(int)$_REQUEST['emode']:0;
$instCodeDefault = array_key_exists('instcode',$_REQUEST)?htmlspecialchars($_REQUEST['instcode']):'';
$formSubmit = array_key_exists('formsubmit',$_POST)?htmlspecialchars($_POST['formsubmit']): '';
$addCollId = array_key_exists('addcollid',$_POST)?(int)$_POST['addcollid']:0;
$removeCollId = array_key_exists('removecollid',$_REQUEST)?(int)$_REQUEST['removecollid']:0;

$instManager = new InstitutionManager();
$fullCollList = $instManager->getCollectionList();
if($iid){
	$instManager->setInstitutionId($iid);
}
$collList = array();
foreach($fullCollList as $k => $v){
	if($v['iid'] === $iid) {
        $collList[$k] = $v['name'];
    }
}

$editorCode = 0;
$statusStr = '';
if($GLOBALS['IS_ADMIN']){
	$editorCode = 3;
}
elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])){
	$editorCode = 1;
	if($collList && array_intersect($GLOBALS['USER_RIGHTS']['CollAdmin'],array_keys($collList))){
		$editorCode = 2;
	}
}
if($editorCode){
	if($formSubmit === 'Add Institution'){
		$iid = $instManager->submitInstitutionAdd($_POST);
		if($iid){
			if($targetCollid) {
                header('Location: ../misc/collprofiles.php?collid=' . $targetCollid);
            }
		}
		else{
			$statusStr = $instManager->getErrorStr();
		}
	}
	else if($editorCode > 1){
        if($formSubmit === 'Update Institution Address'){
            if($instManager->submitInstitutionEdits($_POST)){
                if($targetCollid) {
                    header('Location: ../misc/collprofiles.php?collid=' . $targetCollid);
                }
            }
            else{
                $statusStr = $instManager->getErrorStr();
            }
        }
        elseif(isset($_POST['deliid'])){
            $delIid = $_POST['deliid'];
            if($instManager->deleteInstitution($delIid)){
                $statusStr = 'SUCCESS! Institution deleted.';
                $iid = 0;
            }
            else{
                $statusStr = $instManager->getErrorStr();
            }
        }
        elseif($formSubmit === 'Add Collection'){
            if($instManager->addCollection($addCollId,$iid)){
                $collList[$addCollId] = $fullCollList[$addCollId]['name'];
            }
            else{
                $statusStr = $instManager->getErrorStr();
            }
        }
        elseif(isset($_GET['removecollid'])){
            if($instManager->removeCollection($_GET['removecollid'])){
                $statusStr = 'SUCCESS! Institution removed';
                unset($collList[$removeCollId]);
            }
            else{
                $statusStr = $instManager->getErrorStr();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Institution Editor</title>
    <meta name="description" content="Collection institution editor">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <script>
		function validateAddCollectionForm(f){
			if(f.addcollid.value === ""){
				alert("Select a collection to be added");
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
<div id="breadcrumbs">
	<a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php'>Home</a> &gt;&gt;
	<?php 
	if(!$targetCollid && count($collList) === 1){
		$targetCollid = key($collList);
	}
	if($targetCollid){
		echo '<a href="../misc/collprofiles.php?collid='.$targetCollid.'&emode=1">'.$collList[$targetCollid].' Management</a> &gt;&gt;';
	}
	else{
		echo '<a href="institutioneditor.php">Full Address List</a> &gt;&gt;';
	}
	?>
	<b>Institution Editor</b> 
</div>
<div id="mainContainer" style="padding: 10px 15px 15px;">
	<?php
	if($statusStr){
		?>
		<hr />
		<div style="margin:20px;color:<?php echo (strncmp($statusStr, 'ERROR', 5) === 0 ?'red':'green'); ?>;">
			<?php echo $statusStr; ?>
		</div>
		<hr />
		<?php 
	}
	if($iid){
		if($instArr = $instManager->getInstitutionData()){
			?>
			<div style="float:right;">
				<a href="institutioneditor.php">
					<i style="height:15px;width:15px;" title="Return to Institution List" class="fas fa-level-up-alt"></i>
				</a>
				<?php 
				if($editorCode > 1){
					?>
					<a href="#" onclick="toggle('editdiv');">
						<i style="height:20px;width:20px;" title="Edit Institution" class="far fa-edit"></i>
					</a>
					<?php 
				}
				?>
			</div>
			<div style="clear:both;">
				<form name="insteditform" action="institutioneditor.php" method="post">
					<fieldset style="padding:20px;">
						<legend><b>Address Details</b></legend>
						<div style="position:relative;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Code:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['institutioncode']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="institutioncode" type="text" value="<?php echo $instArr['institutioncode']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Name:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['institutionname']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="institutionname" type="text" value="<?php echo $instArr['institutionname']; ?>" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Institution Name2:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['institutionname2']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="institutionname2" type="text" value="<?php echo $instArr['institutionname2']; ?>" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Address:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['address1']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="address1" type="text" value="<?php echo $instArr['address1']; ?>" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Address 2:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['address2']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="address2" type="text" value="<?php echo $instArr['address2']; ?>" style="width:400px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								City:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['city']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="city" type="text" value="<?php echo $instArr['city']; ?>" style="width:100px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								State/Province:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['stateprovince']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="stateprovince" type="text" value="<?php echo $instArr['stateprovince']; ?>" style="width:100px;" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Postal Code:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['postalcode']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="postalcode" type="text" value="<?php echo $instArr['postalcode']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Country:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['country']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="country" type="text" value="<?php echo $instArr['country']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Phone:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['phone']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="phone" type="text" value="<?php echo $instArr['phone']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Contact:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['contact']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="contact" type="text" value="<?php echo $instArr['contact']; ?>" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Email:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['email']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="email" type="text" value="<?php echo $instArr['email']; ?>" style="width:150px" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								URL:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<a href="<?php echo $instArr['url']; ?>" target="_blank">
									<?php echo $instArr['url']; ?>
								</a>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="url" type="text" value="<?php echo $instArr['url']; ?>" style="width:400px" />
							</div>
						</div>
						<div style="position:relative;clear:both;">
							<div style="float:left;width:155px;font-weight:bold;">
								Notes:
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'none':'block'; ?>;">
								<?php echo $instArr['notes']; ?>
							</div>
							<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
								<input name="notes" type="text" value="<?php echo $instArr['notes']; ?>" style="width:400px" />
							</div>
						</div>
						<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;clear:both;margin:30px 0 0 20px;">
							<input name="formsubmit" type="submit" value="Update Institution Address" />
							<input name="iid" type="hidden" value="<?php echo $iid; ?>" />
							<input name="targetcollid" type="hidden" value="<?php echo $targetCollid; ?>" />
						</div>
					</fieldset>
				</form>
				<div style="clear:both;">
					<fieldset style="padding:20px;">
						<legend><b>Collecitons Linked to Institution Address</b></legend>
						<div>
							<?php 
							if($collList){
								foreach($collList as $id => $collName){
									echo '<div style="margin:5px;font-weight:bold;clear:both;height:15px;">';
									echo '<div style="float:left;"><a href="../misc/collprofiles.php?collid='.$id.'">'.$collName.'</a></div> ';
									if($editorCode === 3 || in_array($id, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)) {
                                        echo ' <div class="editdiv" style="margin-left:10px;display:' . ($eMode ? '' : 'none') . '"><a href="institutioneditor.php?iid=' . $iid . '&removecollid=' . $id . '"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></a></div>';
                                    }
									echo '</div>';
								}
							}
							else{
								echo '<div style="margin:25px;"><b>Institution is not linked to a collection</b></div>';
							}
							?>
						</div>
						<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
							<div style="margin:15px;clear:both;">* Click on red X to unlink collection</div>
							<?php 
							$addList = array();
							foreach($fullCollList as $collid => $collArr){
								if($collArr['iid'] !== $iid){
									if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
										$addList[$collid] = $collArr;
									}
								}
							}
							if($addList){
								?>
								<hr />
								<form name="addcollectionform" method="post" action="institutioneditor.php" onsubmit="return validateAddCollectionForm(this)">
									<select name="addcollid" style="width:400px;">
										<option value="">Select collection to add</option>
										<option value="">------------------------------------</option>
										<?php 
										foreach($addList as $collid => $collArr){
											echo '<option value="'.$collid.'">'.$collArr['name'].'</option>';
										}
										?>
									</select>
									<input name="iid" type="hidden" value="<?php echo $iid; ?>" />
									<input name="formsubmit" type="submit" value="Add Collection" />
								</form>
								<?php 
							}
							?>
						</div>
					</fieldset>
					<div class="editdiv" style="display:<?php echo $eMode?'block':'none'; ?>;">
						<fieldset style="padding:20px;">
							<legend><b>Delete Institution</b></legend>
							<form name="instdelform" action="institutioneditor.php" method="post" onsubmit="return confirm('Are you sure you want to delete this institution?')">
								<div style="position:relative;clear:both;">
									<input name="formsubmit" type="submit" value="Delete Institution" <?php
                                    if($collList) {
                                        echo 'disabled';
                                    } ?> />
									<input name="deliid" type="hidden" value="<?php echo $iid; ?>" />
									<?php 
									if($collList) {
                                        echo '<div style="margin:15px;color:red;">Deletion of addresses that have linked collections is not allowed</div>';
                                    }
									?>
								</div>
							</form>
						</fieldset>
					</div>
				</div>
			</div>		
			<?php
		}
	}
	else if($editorCode){
        ?>
        <div style="float:right;">
            <a href="#" onclick="toggle('instadddiv');">
                <i style="height:20px;width:20px;color:green;" title="Add a New Institution" class="fas fa-plus"></i>
            </a>
        </div>
        <div id="instadddiv" style="display:<?php echo ($eMode?'block':'none'); ?>;margin-bottom:8px;">
            <form name="instaddform" action="institutioneditor.php" method="post">
                <fieldset style="padding:20px;">
                    <legend><b>Add New Institution</b></legend>
                    <div style="position:relative;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Institution Code:
                        </div>
                        <div>
                            <input name="institutioncode" type="text" value="<?php echo $instCodeDefault; ?>" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Institution Name:
                        </div>
                        <div>
                            <input name="institutionname" type="text" value="" style="width:400px;" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Institution Name2:
                        </div>
                        <div>
                            <input name="institutionname2" type="text" value="" style="width:400px;" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Address:
                        </div>
                        <div>
                            <input name="address1" type="text" value="" style="width:400px;" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Address 2:
                        </div>
                        <div>
                            <input name="address2" type="text" value="" style="width:400px;" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            City:
                        </div>
                        <div>
                            <input name="city" type="text" value="" style="width:100px;" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            State/Province:
                        </div>
                        <div>
                            <input name="stateprovince" type="text" value="" style="width:100px;" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Postal Code:
                        </div>
                        <div>
                            <input name="postalcode" type="text" value="" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Country:
                        </div>
                        <div>
                            <input name="country" type="text" value="" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Phone:
                        </div>
                        <div>
                            <input name="phone" type="text" value="" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Contact:
                        </div>
                        <div>
                            <input name="contact" type="text" value="" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Email:
                        </div>
                        <div>
                            <input name="email" type="text" value="" style="width:150px" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            URL:
                        </div>
                        <div>
                            <input name="url" type="text" value="" style="width:400px" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Notes:
                        </div>
                        <div>
                            <input name="notes" type="text" value="" style="width:400px" />
                        </div>
                    </div>
                    <div style="position:relative;clear:both;">
                        <div style="float:left;width:155px;font-weight:bold;">
                            Link to:
                        </div>
                        <div>
                            <select name="targetcollid" style="width:400px;">
                                <option value="">Leave Orphaned</option>
                                <option value="">--------------------------------------</option>
                                <?php
                                foreach($fullCollList as $collid => $collArr){
                                    if($collArr['iid'] && ($GLOBALS['IS_ADMIN'] || ($GLOBALS['USER_RIGHTS']['CollAdmin'] && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)))){
                                        echo '<option value="'.$collid.'" '.($collid === $targetCollid?'SELECTED':'').'>'.$collArr['name'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div style="margin:20px;clear:both;">
                        <input name="formsubmit" type="submit" value="Add Institution" />
                    </div>
                </fieldset>
            </form>
        </div>
        <?php
        if(!$eMode){
            ?>
            <div style="padding-left:10px;">
                <h2>Select an Institution from the list</h2>
                <ul>
                    <?php
                    $instList = $instManager->getInstitutionList();
                    if($instList){
                        foreach($instList as $iid => $iArr){
                            echo '<li><a href="institutioneditor.php?iid='.$iid.'">';
                            echo $iArr['institutionname'].($iArr['institutioncode']?' ('.$iArr['institutioncode'].')':'');
                            if($editorCode === 3 || array_intersect(explode(',',$iArr['collid']),$GLOBALS['USER_RIGHTS']['CollAdmin'])){
                                echo ' <a href="institutioneditor.php?emode=1&iid='.$iid.'"><i style="height:15px;width:15px;" title="Edit Institution" class="far fa-edit"></i></a>';
                            }
                            echo '</a></li>';
                        }
                    }
                    else{
                        echo '<div>There are no institutions you have right to edit</div>';
                    }
                    ?>
                </ul>
            </div>
            <?php
        }
    }
    else{
        echo '<div>You need to have administrative user rights for a collection to add an institution</div>';
    }
	?>
</div>
<?php
include_once(__DIR__ . '/../../config/footer-includes.php');
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
