<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/ReferenceManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

$refId = array_key_exists('refid',$_REQUEST)?(int)$_REQUEST['refid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

$refManager = new ReferenceManager();
$refArr = '';
$refExist = false;

$statusStr = '';
if($formSubmit){
	if($formSubmit === 'Delete Reference'){
		$statusStr = $refManager->deleteReference($refId);
	}
	if($formSubmit === 'Search References'){
		$refArr = $refManager->getRefList($_POST['searchtitlekeyword'],$_POST['searchauthor']);
		foreach($refArr as $refName => $valueArr){
			if($valueArr['title']){
				$refExist = true;
			}
		}
	}
}
if($formSubmit !== 'Search References'){
	$refArr = $refManager->getRefList('','');
	foreach($refArr as $refName => $valueArr){
		if($valueArr['title']){
			$refExist = true;
		}
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Reference Management</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <script src="../js/all.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/symb/references.index.js?ver=20220113"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/../header.php');
    ?>
    <div class='navpath'>
        <a href='../index.php'>Home</a> &gt;&gt;
        <a href='index.php'> <b>Reference Management</b></a>
    </div>
	<div id="innertext">
		<?php 
		if($GLOBALS['SYMB_UID']){
			if($statusStr){
				?>
				<hr/>
				<div style="margin:15px;color:red;">
					<?php echo $statusStr; ?>
				</div>
				<?php 
			}
			?>
			<div id="" style="float:right;width:240px;">
				<form name="filterrefform" action="index.php" method="post">
					<fieldset style="background-color:#FFD700;">
					    <legend><b>Filter List</b></legend>
				    	<div>
							<div>
								<b>Title Keyword:</b> 
								<input type="text" autocomplete="off" name="searchtitlekeyword" id="searchtitlekeyword" size="25" value="<?php echo ($formSubmit === 'Search References'?$_POST['searchtitlekeyword']:''); ?>" />
							</div>
							<div>
								<b>Author's Last Name:</b> 
								<input type="text" name="searchauthor" id="searchauthor" size="25" value="<?php echo ($formSubmit === 'Search References'?$_POST['searchauthor']:''); ?>" />
							</div>
							<div style="padding-top:8px;float:right;">
								<button name="formsubmit" type="submit" value="Search References">Filter List</button>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<div id="reflistdiv" style="min-height:200px;">
				<div style="float:right;margin:10px;">
					<a href="#" onclick="toggle('newreferencediv');">
						<i style="height:20px;width:20px;color:green;" title="Create New Reference" class="fas fa-plus"></i>
					</a>
				</div>
				<div id="newreferencediv" style="display:none;">
					<form name="newreferenceform" action="refdetails.php" method="post" onsubmit="return verifyNewRefForm();">
						<fieldset>
							<legend><b>Add New Reference</b></legend>
							<div style="clear:both;padding-top:4px;float:left;">
								<div style="">
									<b>Title: </b>
								</div>
								<div style="margin-left:35px;margin-top:-14px;">
									<textarea name="newreftitle" id="newreftitle" rows="10" style="width:380px;height:40px;resize:vertical;" ></textarea>
								</div>
							</div>
							<div style="clear:both;padding-top:6px;float:left;">
								<span>
									<b>Reference Type: </b><select name="newreftype" id="newreftype" style="width:400px;">
										<option value="">Select Reference Type</option>
										<option value="">------------------------------------------</option>
										<?php 
										$typeArr = $refManager->getRefTypeArr();
										foreach($typeArr as $k => $v){
											echo '<option value="'.$k.'">'.$v.'</option>';
										}
										?>
									</select>
								</span>
							</div>
							<div style="clear:both;padding-top:8px;float:right;">
								<input name="ispublished" type="hidden" value="1" />
								<button name="formsubmit" type="submit" value="Create Reference">Create Reference</button>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
				if($refExist){
					echo '<div style="font-weight:bold;font-size:120%;">References</div>';
					echo '<div><ul>';
					foreach($refArr as $refId => $recArr){
						echo '<li>';
						echo '<a href="refdetails.php?refid='.$refId.'"><b>'.$recArr['title'].'</b></a>';
						if($recArr['ReferenceTypeId'] === 27){
							echo ' series.';
						}
						if($recArr['tertiarytitle'] !== $recArr['title']){
							echo ($recArr['tertiarytitle']?', '.$recArr['tertiarytitle']:'');
						}
						echo ($recArr['volume']?' Vol. '.$recArr['volume'].'.':'');
						echo ($recArr['number']?' No. '.$recArr['number'].'.':'');
						if(($recArr['tertiarytitle'] !== $recArr['secondarytitle']) && ($recArr['title'] !== $recArr['secondarytitle'])){
							echo ($recArr['secondarytitle']?', '.$recArr['secondarytitle'].'.':'.');
						}
						echo ($recArr['edition']?' '.$recArr['edition'].' Ed.':'');
						echo ($recArr['pubdate']?' '.$recArr['pubdate'].'.':'');
						echo ($recArr['authline']?' '.$recArr['authline']:'');
						echo '</li>';
					}
					echo '</ul></div>';
				}
				elseif(($formSubmit === 'Search References') && !$refExist){
					echo '<div style="margin-top:10px;"><div style="font-weight:bold;font-size:120%;">There were no references matching your criteria.</div></div>';
				}
				else{
					echo '<div style="margin-top:10px;"><div style="font-weight:bold;font-size:120%;">There are currently no references in the database.</div></div>';
				}
				?>
			</div>
			<?php 
		}
		else if($GLOBALS['SYMB_UID']) {
            echo '<h2>ERROR: unknown error, please contact system administrator</h2>';
        }
        else {
            echo 'Please <a href="../profile/index.php?refurl=../references/index.php">login</a>';
        }
		?>
	</div>
	<?php
	include(__DIR__ . '/../footer.php');
	?>
</body>
</html>
