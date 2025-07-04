<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/KeyCharAdmin.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$langId = array_key_exists('langid',$_REQUEST)?$_REQUEST['langid']:'';

$charManager = new KeyCharAdmin();
$charManager->setLangId($langId);

$charArr = $charManager->getCharacterArr();
$headingArr = $charManager->getHeadingArr();

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

$headingAdminUrl = 'headingadmin.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Key Administration</title>
    <meta name="description" content="Identification key administration for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">
		function validateNewCharForm(f){
			if(f.charname.value === ""){
				alert("Character name must have a value");
				return false;
			}
			if(f.chartype.value === ""){
				alert("A character type must be selected");
				return false;
			} 
			if(f.sortsequence.value && isNaN(f.sortsequence.value)){
				alert("Sort Sequence must be a numeric value only");
				return false;
			}
			return true;
		}

		function openHeadingAdmin(){
            const newWindow = window.open("<?php echo $headingAdminUrl; ?>", "headingWin", "scrollbars=1,toolbar=1,resizable=1,width=800,height=600,left=50,top=50");
            if (newWindow.opener == null) {
                newWindow.opener = self;
            }
		}
	</script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div id="breadcrumbs">
		<a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php'>Home</a> &gt;&gt;
		<b>Character Management</b>
	</div>
	<div id="mainContainer" style="padding: 10px 15px 15px;">
		<?php 
		if($isEditor){
			?>
			<div id="addeditchar">
				<div style="float:right;margin:10px;">
					<a href="#" onclick="toggle('addchardiv');">
						<i style="height:20px;width:20px;color:green;" title="Create New Character" class="fas fa-plus"></i>
					</a>
				</div>
				<div id="addchardiv" style="display:none;margin-bottom:8px;">
					<form name="newcharform" action="chardetails.php" method="post" onsubmit="return validateNewCharForm(this)">
						<fieldset style="padding:10px;">
							<legend><b>New Character</b></legend>
							<div>
								Character Name:<br />
								<input type="text" name="charname" maxlength="255" style="width:400px;" autocomplete="off" />
							</div>
							<div style="padding-top:6px;">
								<div style="float:left;">
									Type:<br />
									<select name="chartype" style="width:180px;">
										<option value="UM">Unordered Multi-state</option>
									</select>
								</div>
								<div style="margin-left:30px;float:left;">
									Difficulty:<br />
									<select name="difficultyrank" style="width:100px;">
										<option value="">---------------</option>
										<option value="1">Easy</option>
										<option value="2">Intermediate</option>
										<option value="3">Advanced</option>
										<option value="4">Hidden</option>
									</select>
								</div>
								<div style="margin-left:30px;float:left;">
									Heading:<br />
									<select name="hid" style="width:125px;">
										<option value="">No Heading</option>
										<option value="">---------------------</option>
										<?php
										$hArr = $headingArr;
										asort($hArr);
										foreach($hArr as $k => $v){
											echo '<option value="'.$k.'">'.$v['name'].'</option>';
										}
										?>
									</select> 
									<a href="#" onclick="openHeadingAdmin(); return false;"><i style="height:20px;width:20px;" class="far fa-edit"></i></a>
								</div>
							</div>
							<div style="padding-top:6px;clear:both;">
								<b>Sort Sequence</b><br />
								<input type="text" name="sortsequence" autocomplete="off" />
							</div>
							<div style="width:100%;padding-top:6px;">
								<button name="formsubmit" type="submit" value="Create">Create</button>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="charlist" style="padding-left:10px;">
					<?php 
					if($charArr){
						?>
						<h3>Characters by Heading</h3>
						<ul>
							<?php 
							foreach($headingArr as $hid => $hArr){
								if(array_key_exists($hid, $charArr)){
									?>
									<li>
										<a href="#" onclick="toggle('char-<?php echo $hid; ?>');return false;"><b><?php echo $hArr['name']; ?></b></a>
										<div id="char-<?php echo $hid; ?>" style="display:block;">
											<ul>
												<?php 
												$charList = $charArr[$hid];
												foreach($charList as $cid => $charName){
													echo '<li>';
													echo '<a href="chardetails.php?cid='.$cid.'">'.$charName.'</a>';
													echo '</li>';
												}
												?>
											</ul>
										</div>
									</li>
									<?php
								}
							}
							if(array_key_exists(0, $charArr)){
								$noHeaderArr = $charArr[0];
								?>
								<li>
									<a href="#" onclick="toggle('char-0');return false;"><b>No Assigned Header</b></a>
									<div id="char-0" style="display:block;">
										<ul>
											<?php 
											foreach($noHeaderArr as $cid => $charName){
												echo '<li>';
												echo '<a href="chardetails.php?cid='.$cid.'">'.$charName.'</a>';
												echo '</li>';
											}
											?>
										</ul>
									</div>
								</li>
								<?php
							}
							?>
						</ul>
					<?php 
					}
					else{
						echo '<div style="font-weight:bold;">There are no existing characters</div>';
					}
					?>
				</div>
			</div>
			<?php 
		}
		else{
			echo '<h2>You are not authorized to add characters</h2>';
		}
		?>
	</div>
	<?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
