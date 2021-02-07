<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/InventoryProjectManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']: '';
$editMode = array_key_exists('emode',$_REQUEST)?$_REQUEST['emode']:0;
$newProj = array_key_exists('newproj',$_REQUEST)?1:0;
$projSubmit = array_key_exists('projsubmit',$_REQUEST)?$_REQUEST['projsubmit']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;
$statusStr = '';

if(!$pid && array_key_exists('proj',$_GET) && is_numeric($_GET['proj'])) {
    $pid = $_GET['proj'];
}

$projManager = new InventoryProjectManager();
if($pid) {
    $projManager->setPid($pid);
}

$isEditor = 0;
if($IS_ADMIN || (array_key_exists('ProjAdmin',$USER_RIGHTS) && in_array($pid, $USER_RIGHTS['ProjAdmin'], true))){
	$isEditor = 1;
}

if($isEditor && $projSubmit){
	if($projSubmit === 'addnewproj'){
		$pid = $projManager->addNewProject($_POST);
		if(!$pid) {
            $statusStr = $projManager->getErrorStr();
        }
	}
	elseif($projSubmit === 'subedit'){
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

$projArr = $projManager->getProjectData();
$researchList = $projManager->getResearchChecklists();
$managerArr = $projManager->getManagers();
if(!$researchList && !$editMode){
	$editMode = 1;
	$tabIndex = 2;
	if(!$managerArr) {
        $tabIndex = 1;
    }
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Inventory Projects</title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link type="text/css" href="../css/jquery-ui.css" rel="stylesheet" />
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
	<script type="text/javascript">
		let tabIndex = <?php echo $tabIndex; ?>;

		$(document).ready(function() {
			$('#tabs').tabs(
				{ active: tabIndex }
			);
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

		function toggleResearchInfoBox(anchorObj){
            const obj = document.getElementById("researchlistpopup");
            const pos = findPos(anchorObj);
            let posLeft = pos[0];
            if(posLeft > 550){
				posLeft = 550;
			}
			obj.style.left = String(posLeft - 40);
			obj.style.top = String(pos[1] + 25);
			if(obj.style.display === "block"){
				obj.style.display="none";
			}
			else {
				obj.style.display="block";
			}
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
			else if(!isNumeric(f.sortsequence.value)){
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
		
		function isNumeric(sText){
            const validChars = "0123456789-.";
            let ch;

            for(let i = 0; i < sText.length; i++){
				ch = sText.charAt(i);
				if(validChars.indexOf(ch) === -1) {
				    return false;
				}
		   	}
			return true;
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
	echo '<b><a href="index.php?pid='.$pid.'">'.($projArr?$projArr['projname']:'Inventory Project List').'</a></b>';
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
					<a href="#" onclick="toggleById('tabs');return false;"><img style="border:0;" src="../images/edit.png"/></a>
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
			if($isEditor){ 
				?>
				<div id="tabs" style="height:550px;margin:10px;display:<?php echo ($newProj||$editMode?'block':'none'); ?>;">
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
						?>
						<div style="font-weight:bold;font-size:130%;">
                            Research Checklists
							<span onclick="toggleResearchInfoBox(this);" title="What is a Research Species List?" style="cursor:pointer;">
								<img src="../images/qmark_big.png" style="height:15px;"/>
							</span> 
							<a href="../checklists/clgmap.php?proj=<?php echo $pid;?>" title="Map Checklists">
								<img src='../images/world.png' style='width:14px;border:0' />
							</a>
						</div>
						<div id="researchlistpopup" class="genericpopup" style="display:none;">
							<img src="../images/uptriangle.png" style="position: relative; top: -22px; left: 30px;" />
                            Research checklists are pre-compiled by biologists.
                            This is a very controlled method for building a species list, which allows for
                            specific specimens to be linked to the species names within the checklist and thus serve as vouchers.
                            Specimen vouchers are proof that the species actually occurs in the given area. If there is any doubt, one
                            can inspect these specimens for verification or annotate the identification when necessary.
						</div>
						<?php 
						if($KEY_MOD_IS_ACTIVE){
							?>
							<div style="margin-left:15px;font-size:90%">
                                The <img src="../images/key.png" style="width: 12px;" alt="Golden Key Symbol" />
                                symbol opens the species list as an interactive key.
							</div>
							<?php
						}
						$gMapUrl = $projManager->getGoogleStaticMap();
						if($gMapUrl){
							?>
							<div style="float:right;text-align:center;">
								<a href="../checklists/clgmap.php?proj=<?php echo $pid;?>" title="Map Checklists">
									<img src="<?php echo $gMapUrl; ?>" title="Map representation of checklists" alt="Map representation of checklists" />
									<br/>
                                    Click to Open Map
								</a>
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
										if($KEY_MOD_IS_ACTIVE){
											?>
											<a href='../ident/key.php?cl=<?php echo $key; ?>&proj=<?php echo $pid; ?>&taxon=All+Species'>
												<img style='width:12px;border:0;' src='../images/key.png'/>
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
			echo '<h1>'.$DEFAULT_TITLE.' Projects</h1>'; 
			$projectArr = $projManager->getProjectList();
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
		?>
	</div>
	<?php
	include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
