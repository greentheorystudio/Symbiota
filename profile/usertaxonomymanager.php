<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/UserTaxonomy.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

$action = array_key_exists('action',$_POST)?$_POST['action']: '';

$utManager = new UserTaxonomy();

$isEditor = 0;		 
if($GLOBALS['SYMB_UID']){
	if( $GLOBALS['IS_ADMIN'] ){
		$isEditor = 1;
	}
}
else{
	header('Location: ../profile/index.php?refurl=../profile/usertaxonomymanager.php');
}

$statusStr = '';
if($isEditor){
	if($action === 'Add Taxonomic Relationship'){
		$uid = $_POST['uid'];
		$taxon = $_POST['taxon'];
		$editorStatus = $_POST['editorstatus'];
		$geographicScope = $_POST['geographicscope'];
		$notes = $_POST['notes'];
		$statusStr = $utManager->addUser($uid, $taxon, $editorStatus, $geographicScope, $notes);
	}
	elseif(array_key_exists('delutid',$_GET)){
		$delUid = array_key_exists('deluid',$_GET)?$_GET['deluid']:0;
		$editorStatus = array_key_exists('es',$_GET)?$_GET['es']:'';
		$statusStr = $utManager->deleteUser($_GET['delutid'],$delUid,$editorStatus);
	}
}
$editorArr = $utManager->getTaxonomyEditors();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title>Taxonomic Interest User permissions</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link type="text/css" href="../css/external/jquery-ui.css?ver=20220720" rel="stylesheet" />
    <script src="../js/external/all.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/external/jquery.js"></script>
	<script type="text/javascript" src="../js/external/jquery-ui.js"></script>
	<script>
		$(document).ready(function() {
			$( "#taxoninput" ).autocomplete({
				source: "rpc/taxasuggest.php",
				minLength: 2,
				autoFocus: true
			});
		});

		function verifyUserAddForm(f){
			if(f.uid.value === ""){
				alert("Select a User");
				return false;
			}
			if(f.editorstatus.value === ""){
				alert("Select the Scope of Relationship");
				return false;
			}
			if(f.taxoninput.value === ""){
				alert("Select the Taxonomic Name");
				return false;
			}
			return true;
		}
	</script>
	<script type="text/javascript" src="../js/shared.js?ver=20220718"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/../header.php');
    ?>
    <div class='navpath'>
        <a href='../index.php'>Home</a> &gt;&gt;
        <b>Taxonomic Interest User permissions</b>
    </div>
    <?php

	if($statusStr){
		?>
		<hr/>
		<div style="color:<?php echo (strpos($statusStr,'SUCCESS') !== false?'green':'red'); ?>;margin:15px;">
			<?php echo $statusStr; ?>
		</div>
		<hr/>
		<?php 
	}
	if($isEditor){
		?>
		<div id="innertext">
			<h2>Taxonomic Interest User Permissions</h2>
			<div style="float:right;" title="Add a new taxonomic relationship">
				<a href="#" onclick="toggle('addUserDiv')">
                    <i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
				</a>
			</div>
			<div id="addUserDiv" style="display:none;">
				<fieldset style="padding:20px;">
					<legend><b>New Taxonomic Relationship</b></legend>
					<form name="adduserform" action="usertaxonomymanager.php" method="post" onsubmit="return verifyUserAddForm(this)">
						<div style="margin:3px;">
							<b>User</b><br/>
							<select name="uid">
								<option value="">-------------------------------</option>
								<?php 
								$userArr = $utManager->getUserArr();
								foreach($userArr as $uid => $displayName){
									echo '<option value="'.$uid.'">'.$displayName.'</option>';
								}
								?>
							</select>
						</div>
						<div style="margin:3px;">
							<b>Taxon</b><br/>
							<input id="taxoninput" name="taxon" type="text" value="" style="width:90%;" />
						</div>
						<div style="margin:3px;">
							<b>Scope of Relationship</b><br/>
							<select name="editorstatus">
								<option value="">----------------------------</option>
								<option value="OccurrenceEditor">Occurrence Identification Editor</option>
								<option value="RegionOfInterest">Region Of Interest</option>
								<option value="TaxonomicThesaurusEditor">Taxonomic Thesaurus Editor</option>
							</select>
						
						</div>
						<div style="margin:3px;">
							<b>Geographic Scope Limits</b><br/>
							<input name="geographicscope" type="text" value="" style="width:90%;"/>
						
						</div>
						<div style="margin:3px;">
							<b>Notes</b><br/>
							<input name="notes" type="text" value="" style="width:90%;" />
						
						</div>
						<div style="margin:3px;">
							<input name="action" type="submit" value="Add Taxonomic Relationship" />
						</div>
					</form>
				</fieldset>
			</div>
			<div>
				<?php 
				foreach($editorArr as $editorStatus => $userArr){
					$cat = 'Undefined';
					if($editorStatus === 'RegionOfInterest') {
                        $cat = 'Region Of Interest';
                    }
					elseif($editorStatus === 'OccurrenceEditor') {
                        $cat = 'Occurrence Identification Editor';
                    }
					elseif($editorStatus === 'TaxonomicThesaurusEditor') {
                        $cat = 'Taxonomic Thesaurus Editor';
                    }
					?>
					<div><b><u><?php echo $cat; ?></u></b></div>
					<ul style="margin:10px;">
					<?php 
					foreach($userArr as $uid => $uArr){
						$username = $uArr['username'];
						unset($uArr['username']);
						?>
						<li>
							<?php
							echo '<b>'.$username.'</b>';
							?>
							<a href="usertaxonomymanager.php?delutid=all&deluid=<?php echo $uid.'&es='.$editorStatus; ?>" onclick="return confirm('Are you sure you want to remove all taxonomy links for this user?');" title="Delete all taxonomic relationships for this user">
								<i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
							</a>
							<?php
							foreach($uArr as $utid => $utArr){
								echo '<li style="margin-left:15px;">'.$utArr['sciname'];
								if($utArr['geoscope']) {
                                    echo ' (' . $utArr['geoscope'] . ')';
                                }
								if($utArr['notes']) {
                                    echo ': ' . $utArr['notes'];
                                }
								?>
								<a href="usertaxonomymanager.php?delutid=<?php echo $utid; ?>" onclick="return confirm('Are you sure you want to remove this taxonomy links for this user?');" title="Delete this user taxonomic relationship">
									<i style="height:15px;width:15px;" class="far fa-trash-alt"></i>
								</a>
								<?php
								echo '</li>';
							}
							?>
						</li>
						<?php  
					}
					?>
					</ul>
					<?php 
				}
				?>
			</div>
		</div>
		<?php
	}
	else{
		echo '<div style="color:red;">You are not authorized to access this page</div>';
	}
	include(__DIR__ . '/../footer.php');
	?>
</body>
