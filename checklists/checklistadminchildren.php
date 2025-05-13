<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistAdmin.php');

$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']): '';

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);
?>
<div id="mainContainer" style="padding: 10px 15px 15px;background-color:white;">
	<div style="float:right;">
		<a href="#" onclick="toggle('addchilddiv')"><i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i></a>
	</div>
	<div style="margin:15px;font-weight:bold;">
		<u>Children Checklists</u>
	</div>
	<div style="margin:25px;clear:both;">
		Checklists will inherit scientific names, vouchers, notes, etc from all children checklists. 
		Adding a new taxon or voucher to a child checklist will automatically add it to all parent checklists. 
		The parent child relationship can transcend multiple levels (e.g. country &lt;- state &lt;- county).
		Note that only direct child can be removed. 
	</div>
	<div id="addchilddiv" style="margin:15px;display:none;">
		<fieldset style="padding:15px;">
			<legend><b>Link New Checklist</b></legend>
			<form name="addchildform" target="checklistadmin.php" method="post">
				<div style="margin:10px;">
					<select name="clidadd">
						<option value="">Select Child Checklist</option>
						<option value="">-------------------------------</option>
						<?php 
						$clArr = $clManager->getChildSelectArr();
						foreach($clArr as $k => $name){
							echo '<option value="'.$k.'">'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div style="margin:10px;">
					<input name="submitaction" type="submit" value="Add Child Checklist" />
					<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
					<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
					<input name="tabindex" type="hidden" value="2" />
				</div>
			</form>
		</fieldset>
	</div>
	<div style="margin:15px;">
		<ul>
			<?php
			if($childArr = $clManager->getChildrenChecklist()){
				foreach($childArr as $k => $cArr){
					?>
					<li>
						<a href="checklist.php?cl=<?php echo $k; ?>"><?php echo $cArr['name']; ?></a>
						<?php 
						if((int)$cArr['pclid'] === $clid){
							echo '<a href="checklistadmin.php?submitaction=delchild&tabindex=2&cliddel='.$k.'&clid='.$clid.'&pid='.$pid.'" onclick="return confirm(\'Are you sure you want to remove'.$cArr['name'].' as a child checklist?\')"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></a>';
						}
						?>
					</li>
					<?php
				}
			}
			else{
				echo '<div>There are no Children Checklists</div>';
			}
			?>
		</ul>
	</div>
	<div style="margin:30px 15px;font-weight:bold;">
		<u>Parent Checklists</u>
	</div>
	<div style="margin:15px;">
		<ul>
			<?php
			if($parentArr = $clManager->getParentChecklists()){
				foreach($parentArr as $k => $name){
					?>
					<li>
						<a href="checklist.php?cl=<?php echo $k; ?>"><?php echo $name; ?></a>
					</li>
					<?php
				}
			}
			else{
				echo '<div>There are no Parent Checklists</div>';
			}
			?>
		</ul>
	</div>
</div>
