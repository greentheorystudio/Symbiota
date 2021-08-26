<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/KeyCharAdmin.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=../ident/admin/headingadmin.php?' . str_replace('&amp;', '&',htmlspecialchars($_SERVER['QUERY_STRING'], ENT_NOQUOTES)));
}

$hid = array_key_exists('hid',$_POST)?(int)$_POST['hid']:0;
$langId = array_key_exists('langid',$_REQUEST)?$_REQUEST['langid']:'';
$action = array_key_exists('action',$_POST)?$_POST['action']:'';

$charManager = new KeyCharAdmin();
$charManager->setLangId($langId);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$statusStr = '';
if($isEditor && $action){
	if($action === 'Create'){
		$statusStr = $charManager->addHeading($_POST['headingname'],$_POST['notes'],$_POST['sortsequence']);
	}
	elseif($action === 'Save'){
		$statusStr = $charManager->editHeading($hid,$_POST['headingname'],$_POST['notes'],$_POST['sortsequence']);
	}
	elseif($action === 'Delete'){
		$statusStr = $charManager->deleteHeading($hid);
	}
}
$headingArr = $charManager->getHeadingArr();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title>Heading Administration</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <script src="../../js/all.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../js/symb/shared.js?ver=20210621"></script>
	<script type="text/javascript">
		function validateHeadingForm(f){
			if(f.headingname.value === ""){
				alert("Heading must have a title");
				return false;
			}
			return true;
		}
	</script>
</head>
<body>
	<div style="width:700px">
		<?php 
		if($statusStr){
			?>
			<hr/>
			<div style="margin:15px;color:<?php echo (strncmp($statusStr, 'SUCCESS', 7) ===0?'green':'red'); ?>;">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php 
		}
		if($isEditor){
			?>
			<div id="addheadingdiv">
				<form name="newheadingform" action="headingadmin.php" method="post" onsubmit="return validateHeadingForm(this)">
					<fieldset>
						<legend><b>New Heading</b></legend>
						<div>
							Heading Name<br />
							<input type="text" name="headingname" maxlength="255" style="width:400px;" autocomplete="off" />
						</div>
						<div style="padding-top:6px;">
							<b>Notes</b><br />
							<input name="notes" type="text" style="width:500px;" autocomplete="off" />
						</div>
						<div style="padding-top:6px;">
							<b>Sort Sequence</b><br />
							<input type="text" name="sortsequence" autocomplete="off" />
						</div>
						<div style="width:100%;padding-top:6px;">
							<button name="action" type="submit" value="Create">Create Heading</button>
						</div>
					</fieldset>
				</form>
			</div>
			<div>
				<?php 
				if($headingArr){
					?>
					<fieldset>
						<legend><b>Existing Headings</b></legend>
						<ul>
							<?php 
							foreach($headingArr as $headingId => $headArr){
								echo '<li><a href="#" onclick="toggle(\'headingedit-'.$headingId.'\');">'.$headArr['name'].' <i style="height:15px;width:15px;" class="far fa-edit"></i></a></li>';
								?>
								<div id="headingedit-<?php echo $headingId; ?>" style="display:none;margin:20px;">
									<fieldset style="padding:15px;">
										<legend><b>Heading Editor</b></legend>
										<form name="headingeditform" action="headingadmin.php" method="post" onsubmit="return validateHeadingForm(this)">
											<div style="margin:2px;">
												<b>Heading Name</b><br/>
												<input name="headingname" type="text" value="<?php echo $headArr['name']; ?>" style="width:400px;" autocomplete="off" />
											</div>
											<div style="margin:2px;">
												<b>Notes</b><br/>
												<input name="notes" type="text" value="<?php echo $headArr['notes']; ?>" style="width:500px;" autocomplete="off" />
											</div>
											<div style="margin:2px;">
												<b>Sort Sequence</b><br/>
												<input name="sortsequence" type="text" value="<?php echo $headArr['sortsequence']; ?>" autocomplete="off" />
											</div>
											<div>
												<input name="hid" type="hidden" value="<?php echo $headingId; ?>" />
												<button name="action" type="submit" value="Save">Save Edits</button>
											</div>
										</form>
									</fieldset>
									<fieldset style="padding:15px;">
										<legend><b>Delete Heading</b></legend>
										<form name="headingdeleteform" action="headingadmin.php" method="post">
											<input name="hid" type="hidden" value="<?php echo $headingId; ?>" />
											<button name="action" type="submit" value="Delete">Delete Heading</button>
										</form>
									</fieldset>
								</div>
								<?php 
							}
							?>
						</ul>
					</fieldset>
					<?php 
				}
				else{
					echo '<div style="font-weight:bold;font-size:120%;">There are no existing character headings</div>';
				}
				?>
			</div>
			<?php 
		}
		else{
			echo '<h2>You are not authorized to add characters</h2>';
		}
		?>
	</div>
</body>
</html>
