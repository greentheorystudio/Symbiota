<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistVoucherAdmin.php');

$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']): '';
$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']): '';
$startPos = (array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0);

$vManager = new ChecklistVoucherAdmin();
$vManager->setClid($clid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
	$isEditor = true;
}

?>
<script>
	function selectAll(cbox){
        let boxesChecked = true;
        if(!cbox.checked) boxesChecked = false;
        const f = cbox.form;
        for(let i=0; i<f.length; i++){
			if(f.elements[i].name === "occid[]") f.elements[i].checked = boxesChecked;
		}
	}
	
	function validateBatchConflictForm(f){
        let formVerified = false;
        for(let h=0; h<f.length; h++){
			if(f.elements[h].name === "occid[]" && f.elements[h].checked){
				formVerified = true;
				break;
			}
		}
		if(!formVerified){
			alert("At least one voucher record needs to be selected");
			return false;
		}
		f.submit();
	}
</script>
<div id="main-container" style="background-color:white;">
	<h2>Possible Voucher Conflicts</h2>
	<div style="margin-bottom:10px;">
		List of vouchers where the current identifications conflict with the checklist.
		Voucher conflicts are typically due to identification changes for records.
		Click on Checklist ID to open the editing pane for that record. 
	</div>
	<?php 
	if($conflictArr = $vManager->getConflictVouchers()){
		echo '<div style="font-weight:bold;">Conflict Count: '.count($conflictArr).'</div>';
		?>
		<form name="batchConflictForm" method="post" action="voucheradmin.php">
			<table class="styledtable" style="font-family:Arial,serif;">
				<tr>
					<th><input type="checkbox" onclick="selectAll(this)" /></th>
					<th><b>Checklist ID</b></th>
					<th><b>Voucher Occurrence</b></th>
					<th><b>Corrected Occurrence ID</b></th>
					<th><b>Identified By</b></th>
				</tr>
				<?php
				foreach($conflictArr as $id => $vArr){
					?>
					<tr>
						<td>
							<input name="occid[]" type="checkbox" value="<?php echo $vArr['occid']; ?>" /> 
						</td>
						<td>
							<a href="#" onclick="return openPopup('clsppeditor.php?tid=<?php echo $vArr['tid']. '&clid=' .$vArr['clid']; ?>','editorwindow');">
								<?php 
								echo $vArr['listid'];
								?>
							</a>
							<?php 
							if($vArr['clid'] !== $clid) {
                                echo '<br/>(from child checklists)';
                            }
							?>
						</td>
						<td>
							<a href="#" onclick="return openPopup('../collections/individual/index.php?occid=<?php echo $vArr['occid']; ?>','occwindow');">
								<?php echo $vArr['recordnumber']; ?>
							</a>
						</td>
						<td>
							<?php echo $vArr['specid'] ?>
						</td>
						<td>
							<?php echo $vArr['identifiedby'] ?>
						</td>
					</tr>
					<?php 
				}
				?>
			</table>
			<div>
				<input name="removeOldIn" type="checkbox" value="1" checked /> Remove old ID if all vouchers have been transferred
			</div>
			<div style="margin: 10px 0;">
				<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
				<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
				<input name="tabindex" type="hidden" value="2" />
				<input name="submitaction" type="hidden" value="resolveconflicts" />
				<b>Batch Action:</b> <input name="submitbutton" type="button" value="Transfer Vouchers to Corrected Taxon" onclick="return validateBatchConflictForm(this.form)" />
				<div>* Corrected taxon will be added to checklist if not yet present</div>
			</div>
		</form>
		<?php 
	}
	else{
		echo '<h3>No conflicts exist</h3>';
	}
	?>
</div>
