<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/ExsiccatiManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

if(!$GLOBALS['SYMB_UID']){
	header('Location: ../../profile/index.php?refurl=../collections/exsiccati/batchimport.php?'.$_SERVER['QUERY_STRING']);
}

$ometid = array_key_exists('ometid',$_REQUEST)?$_REQUEST['ometid']:0;
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$source1 = array_key_exists('source1',$_POST)?$_POST['source1']:0;
$source2 = array_key_exists('source2',$_POST)?$_POST['source2']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

$statusStr = '';
$isEditor = 0;
if($GLOBALS['IS_ADMIN']){
	$isEditor = 1;
}
elseif(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
	$isEditor = 1;
}
elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
	$isEditor = 1;
}

$exsManager = new ExsiccatiManager();
if($isEditor && $formSubmit){
	if($formSubmit === 'Import Selected Records'){
		$statusStr = $exsManager->batchImport($collid,$_POST);
	}
	elseif($formSubmit === 'Export Selected Records'){
		$exsManager->exportAsCsv($_POST);
		exit;
	}
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Exsiccati Batch Transfer</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
		function verifyExsTableForm(f){
            let formVerified = false;
            for(let h = 0; h<f.length; h++){
				if(f.elements[h].name === "occid[]" && f.elements[h].checked){
					formVerified = true;
					break;
				}
			}
			if(!formVerified){
				alert("Select at least one record");
				return false;
			}
			return true;
		}

		function verifyFirstForm(f){
			if(f.ometid.value === ""){
				alert("Exsiccati title must be selected");
				return false;
			}
			return true;
		}

		function checkRecord(textObj,occid){
            const cbObj = document.getElementById(occid);
            cbObj.checked = textObj.value !== "";
		}

		function selectAll(selectObj){
            let boxesChecked = true;
            if(!selectObj.checked){
				boxesChecked = false;
			}
            const f = selectObj.form;
            for(let i=0; i<f.length; i++){
				if(f.elements[i].name === "occid[]") {
				    f.elements[i].checked = boxesChecked;
				}
			}
		}

		function openIndPU(occId){
            let wWidth = 900;
            if(document.getElementById('innertext').offsetWidth){
				wWidth = document.getElementById('innertext').offsetWidth*1.05;
			}
			else if(document.body.offsetWidth){
				wWidth = document.body.offsetWidth*0.9;
			}
            const newWindow = window.open('../individual/index.php?occid=' + occId, 'indspec', 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
            if(newWindow.opener == null) {
                newWindow.opener = self;
            }
			return false;
		}

		function openExsPU(omenid){
            let wWidth = 900;
            if(document.getElementById('innertext').offsetWidth){
				wWidth = document.getElementById('innertext').offsetWidth*1.05;
			}
			else if(document.body.offsetWidth){
				wWidth = document.body.offsetWidth*0.9;
			}
            const newWindow = window.open('index.php?omenid=' + omenid, 'exsnum', 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
            if(newWindow.opener == null) {
                newWindow.opener = self;
            }
			return false;
		}
	</script>
</head>
<body>
	<?php 
	include(__DIR__ . '/../../header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt; 
		<a href="index.php">Exsiccati Index</a> &gt;&gt; 
		<a href="batchimport.php">Batch Import Module</a>
	</div>
	<div id="innertext">
		<?php
		if($statusStr){
			echo '<hr/>';
			echo '<div style="margin:10px;color:'.(strpos($statusStr,'SUCCESS') === false?'red':'green').';">'.$statusStr.'</div>';
			echo '<hr/>';
		}
		if(!$ometid){
			if($exsArr = $exsManager->getTitleArr('', 1)){
				?>
				<form name="firstform" action="batchimport.php" method="post" onsubmit="return verifyFirstForm(this)">
					<fieldset>
						<legend><b>Batch Import Module</b></legend>
						<div style="margin:30px">
							<select name="ometid" style="width:500px;" onchange="this.form.submit()">
								<option value="">Choose Exsiccati Series</option>
								<option value="">------------------------------------</option>
								<?php 
								foreach($exsArr as $exid => $exTitle){
									echo '<option value="'.$exid.'">'.$exTitle.'</option>';
								}
								?>
							</select>
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						</div>
					</fieldset>
				</form>
				<?php
			}
			else{
				echo '<div style="margin:20px;font-size:120%;"><b>The system does not yet have occurrence linked to exsiccati that can be transferred</b></div>';
			}				
		}
		elseif($formSubmit === 'Show Exsiccati Table'){
			$occurArr = $exsManager->getExsOccArr($ometid, 'ometid');
			if($occurArr){
				$exsMetadata = $exsManager->getTitleObj($ometid);
				$exstitle = $exsMetadata['title'].' ['.$exsMetadata['editor'].']';
				echo '<div style="font-size:120%;"><b>'.$exstitle.'</b></div>';
				?>
				<form name="exstableform" method="post" action="batchimport.php" onsubmit="return verifyExsTableForm(this)">
					<div style="margin:10px 0;">
						Enter your catalog numbers in field associated with record and then transfer into your collection or download as a spreadsheet (CSV) 
						for import into a local database application.   
					</div>
					<table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
						<tr><th><input name="selectAllCB" type="checkbox" onchange="selectAll(this)" /></th><th>Catalog Number</th><th>Exsiccati #</th><th>Details</th></tr>
						<?php 
						foreach($occurArr as $omenid => $occArr){
							$prefOcc = array();
							if($source1 || $source2){
								foreach($occArr as $id => $oArr){
									if($oArr['collid'] === $source1){
										array_unshift($prefOcc,$id);
									}
									if($oArr['collid'] === $source2){
										$prefOcc[] = $id;
									}
								}
							}
							$cnt = 0;
							foreach($prefOcc as $oid){
								echo $exsManager->getExsTableRow($oid,$occArr[$oid],$omenid,$collid);
								unset($occArr[$oid]);
								$cnt++;
							}
							foreach($occArr as $occid => $oArr){
								if($cnt < 3 || $oArr['collid'] === $collid){
									echo $exsManager->getExsTableRow($occid,$oArr,$omenid,$collid);
									$cnt++;
								}
							}
						}
						?>
					</table>
					<?php
					if($targetCollArr = $exsManager->getTargetCollArr()){
						?>
						<div style="margin:10px">
							<select name="collid">
								<option value="">Choose Target Collection</option>
								<option value="">----------------------------------</option>
								<?php
								foreach($targetCollArr as $id => $collName){
									echo '<option value="'.$id.'" '.($id === $collid?'SELECTED':'').'>'.$collName.'</option>';
								}
								?>
							</select>
							<input name="formsubmit" type="submit" value="Import Selected Records" />
						</div>
						<?php 
					}
					?>
					<div style="margin:15px">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="ometid" type="hidden" value="<?php echo $ometid; ?>" />
						<input name="formsubmit" type="submit" value="Export Selected Records" />
					</div>
				</form>
				<?php 
			}
			else{
				echo '<div style="font-weight:bold;">There are no specimen records linked to this exsiccati title</div>';
			}
		}
		else{
			?>
			<form name="queryform" action="batchimport.php" method="post" onsubmit="return verifyQueryForm()">
				<fieldset>
					<legend><b>Batch Import Module</b></legend>
					<?php 
					$exsTitleArr = $exsManager->getTitleArr();
					echo '<h2>'.$exsTitleArr[$ometid].'</h2>';
					if($sourceCollArr = $exsManager->getCollArr($ometid)){
						?>
						<div style="margin:10px">
							<div>
								<b>Select up to two collections that are the preferred sources for occurrence records</b>
							</div>
							<div style="margin:5px 0;">
								<select name="source1">
									<option value="">Source Collection 1</option>
									<option value="">------------------------------------</option>
									<?php 
									foreach($sourceCollArr as $id => $cTitle){
										echo '<option value="'.$id.'" '.($source1 === $id?'SELECTED':'').'>'.$cTitle.'</option>';
									}
									?>
								</select>
							</div>
							<?php 
							if(count($sourceCollArr) > 1){
								?>
								<div style="margin:5px 0;">
									<select name="source2">
										<option value="">Source Collection 2</option>
										<option value="">------------------------------------</option>
										<?php 
										foreach($sourceCollArr as $id => $cTitle){
											echo '<option value="'.$id.'" '.($source2 === $id?'SELECTED':'').'>'.$cTitle.'</option>';
										}
										?>
									</select>
								</div>
								<?php 
							}
							?>
						</div>
						<?php 
					}
					?>
					<div style="margin:20px">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="ometid" type="hidden" value="<?php echo $ometid; ?>" />
						<input name="formsubmit" type="submit" value="Show Exsiccati Table" />
					</div>
				</fieldset>
			</form>
			<?php
		}
		?>
	</div>
	<?php
	include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
