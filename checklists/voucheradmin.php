<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistVoucherAdmin.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl=../checklists/voucheradmin.php?'.$_SERVER['QUERY_STRING']);

$clid = array_key_exists("clid",$_REQUEST)?$_REQUEST["clid"]:0;
$pid = array_key_exists("pid",$_REQUEST)?$_REQUEST["pid"]:"";
$startPos = (array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0);
$tabIndex = array_key_exists("tabindex",$_REQUEST)?$_REQUEST["tabindex"]:0;
$action = array_key_exists("submitaction",$_REQUEST)?$_REQUEST["submitaction"]:"";

$displayMode = (array_key_exists('displaymode',$_REQUEST)?$_REQUEST['displaymode']:0);

$clManager = new ChecklistVoucherAdmin();
$clManager->setClid($clid);

$statusStr = "";
$isEditor = 0;
if($IS_ADMIN || (array_key_exists("ClAdmin",$USER_RIGHTS) && in_array($clid,$USER_RIGHTS["ClAdmin"]))){
	$isEditor = 1;
	if($action == "SaveSearch"){
		$statusStr = $clManager->saveQueryVariables($_POST);
	}
	elseif($action == 'DeleteVariables'){
		$statusStr = $clManager->deleteQueryVariables();
	}
	elseif($action == 'Add Vouchers'){
		$clManager->linkVouchers($_POST['occids']);
	}
	elseif($action == 'Add Taxa and Vouchers'){
		$clManager->linkTaxaVouchers($_POST['occids'],(array_key_exists('usecurrent',$_POST)?$_POST['usecurrent']:0));
	}
	elseif($action == 'resolveconflicts'){
		$clManager->batchAdjustChecklist($_POST);
	}
}
$clManager->setCollectionVariables();
?>

<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<title><?php echo $DEFAULT_TITLE; ?> Checklist Administration</title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link type="text/css" href="../css/jquery-ui.css" rel="Stylesheet" />
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
	<script type="text/javascript">
		var clid = <?php echo $clid; ?>;
		var tabIndex = <?php echo $tabIndex; ?>;
	</script>
	<script type="text/javascript" src="../js/symb/checklists.voucheradmin.js?ver=130330"></script>
	<style type="text/css">
		li{margin:5px;}
	</style>
</head>

<body>
<?php
include($SERVER_ROOT.'/header.php');
?>
<div class="navpath">
	<a href="../index.php">Home</a> &gt;&gt;
	<a href="checklist.php?cl=<?php echo $clid.'&pid='.$pid; ?>">Return to Checklist</a> &gt;&gt;
	<b>Checklist Administration</b>
</div>

<!-- This is inner text! -->
<div id='innertext'>
<div style="color:#990000;font-size:20px;font-weight:bold;margin:0px 10px 10px 0px;">
	<a href="checklist.php?cl=<?php echo $clid.'&pid='.$pid; ?>">
		<?php echo $clManager->getClName(); ?>
	</a>
</div>
<?php
if($statusStr){
	?>
	<hr />
	<div style="margin:20px;font-weight:bold;color:red;">
		<?php echo $statusStr; ?>
	</div>
	<hr />
<?php
}

if($clid && $isEditor){
	$termArr = $clManager->getQueryVariablesArr();
	$collList = $clManager->getCollectionList();
	if($termArr){
		?>
		<div style="margin:10px;">
			<?php
			echo $clManager->getQueryVariableStr();
			?>
			<span style="margin-left:10px;"><a href="#" onclick="toggle('sqlbuilderdiv');return false;" title="Edit Search Statement"><img src="../images/edit.png" style="width:15px;border:0px;"/></a></span>
		</div>
	<?php
	}
	?>
	<div id="sqlbuilderdiv" style="display:<?php echo ($termArr?'none':'block'); ?>;margin-top:15px;">
		<fieldset>
			<legend><b>Edit Search Statement</b></legend>
			<form name="sqlbuilderform" action="voucheradmin.php" method="post" onsubmit="return validateSqlFragForm(this);">
				<div style="margin:10px;">
                    To use the voucher administration functions, it is first necessary to define a search terms that will be used to limit occurrence records to those collected within the vicinity of the research area.
                </div>
				<table style="margin:15px;">
					<tr>
						<td>
							<div style="margin:2px;">
								<b>Country:</b>
								<input type="text" name="country" value="<?php echo isset($termArr['country'])?$termArr['country']:''; ?>" />
							</div>
							<div style="margin:2px;">
								<b>State:</b>
								<input type="text" name="state" value="<?php echo isset($termArr['state'])?$termArr['state']:''; ?>" />
							</div>
							<div style="margin:2px;">
								<b>County:</b>
								<input type="text" name="county" value="<?php echo isset($termArr['county'])?$termArr['county']:''; ?>" />
							</div>
							<div style="margin:2px;">
								<b>Locality:</b>
								<input type="text" name="locality" value="<?php echo isset($termArr['locality'])?$termArr['locality']:''; ?>" />
							</div>
							<div style="margin:2px;" title="Genus, family, or higher rank">
								<b>Taxon:</b>
								<input type="text" name="taxon" value="<?php echo isset($termArr['taxon'])?$termArr['taxon']:''; ?>" />
							</div>
							<div>
								<b>Collection:</b>
								<select name="collid" style="width:275px;">
									<option value="">Search All Collections</option>
									<option value="">-------------------------------------</option>
									<?php
									$selCollid = isset($termArr['collid'])?$termArr['collid']:'';
									foreach($collList as $id => $name){
										echo '<option value="'.$id.'" '.($selCollid==$id?'SELECTED':'').'>'.$name.'</option>';
									}
									?>
								</select>
							</div>
							<div>
								<b>Collector:</b>
								<input name="recordedby" type="text" value="<?php echo isset($termArr['recordedby'])?$termArr['recordedby']:''; ?>" style="width:250px" title="Enter multiple collectors separated by semicolons" />
							</div>
						</td>
						<td style="padding-left:20px;">
							<div style="float:left;">
								<div>
									<b>Lat North:</b>
									<input id="upperlat" type="text" name="latnorth" style="width:70px;" value="<?php echo isset($termArr['latnorth'])?$termArr['latnorth']:''; ?>" title="Latitude North" />
									<a href="#" onclick="openPopup('../collections/mapboundingbox.php','boundingbox')"><img src="../images/world.png" width="15px" title="Find Coordinate" /></a>
								</div>
								<div>
									<b>Lat South:</b>
									<input id="bottomlat" type="text" name="latsouth" style="width:70px;" value="<?php echo isset($termArr['latsouth'])?$termArr['latsouth']:''; ?>" title="Latitude South" />
								</div>
								<div>
									<b>Long East:</b>
									<input id="rightlong" type="text" name="lngeast" style="width:70px;" value="<?php echo isset($termArr['lngeast'])?$termArr['lngeast']:''; ?>" title="Longitude East" />
								</div>
								<div>
									<b>Long West:</b>
									<input id="leftlong" type="text" name="lngwest" style="width:70px;" value="<?php echo isset($termArr['lngwest'])?$termArr['lngwest']:''; ?>" title="Longitude West" />
								</div>
								<div>
									<input type="checkbox" name="latlngor" value="1" <?php if(isset($termArr['latlngor'])) echo 'CHECKED'; ?> />
                                    Include Lat/Long and locality as an "OR" condition
								</div>
								<div>
									<input name="onlycoord" value="1" type="checkbox" <?php if(isset($termArr['onlycoord'])) echo 'CHECKED'; ?> />
                                    Only include occurrences with coordinates
								</div>
								<div>
									<input name="excludecult" value="1" type="checkbox" <?php if(isset($termArr['excludecult'])) echo 'CHECKED'; ?> />
                                    Exclude cultivated species
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div style="margin:10px;">
								<input type="submit" name="submit" value="Save Search Terms" />
								<input type="hidden" name="submitaction" value="SaveSearch" />
								<input type='hidden' name='clid' value='<?php echo $clid; ?>' />
								<input type='hidden' name='pid' value='<?php echo $pid; ?>' />
							</div>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
		<?php
		if($termArr){
			?>
			<fieldset>
				<legend><b>Remove Search Statement</b></legend>
				<form name="sqldeleteform" action="voucheradmin.php" method="post" onsubmit="return confirm('Are you sure you want to delete query variables?');">
					<div style="margin:20px">
						<input type="submit" name="submit" value="Delete Query Variables" />
						<input type="hidden" name="submitaction" value="DeleteVariables" />
					</div>
					<input type="hidden" name="clid" value="<?php echo $clid; ?>" />
					<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
				</form>
			</fieldset>
			<?php
		}
		?>
	</div>
	<?php
	if($termArr){
		?>
		<div id="tabs" style="margin-top:25px;">
			<ul>
				<li><a href="#nonVoucheredDiv"><span>New Vouchers</span></a></li>
				<li><a href="vamissingtaxa.php?clid=<?php echo $clid.'&pid='.$pid.'&start='.$startPos.'&displaymode='.($tabIndex==1?$displayMode:0); ?>"><span>Missing Taxa</span></a></li>
				<li><a href="vaconflicts.php?clid=<?php echo $clid.'&pid='.$pid.'&start='.$startPos; ?>"><span>Voucher Conflicts</span></a></li>
				<li><a href="#reportDiv"><span>Reports</span></a></li>
			</ul>
			<div id="nonVoucheredDiv">
				<div style="margin:10px;">
					<?php
					$nonVoucherCnt = $clManager->getNonVoucheredCnt();
					?>
					<div style="float:right;">
						<form name="displaymodeform" method="post" action="voucheradmin.php">
							<b>Display Mode:</b>
							<select name="displaymode" onchange="this.form.submit()">
								<option value="0">Non-vouchered taxa list</option>
								<option value="1" <?php echo ($displayMode==1?'SELECTED':''); ?>>Occurrences for non-vouchered taxa</option>
								<option value="2" <?php echo ($displayMode==2?'SELECTED':''); ?>>New occurrences for all taxa</option>
								<!-- <option value="3" <?php //echo ($displayMode==3?'SELECTED':''); ?>>Non-species level or poorly identified vouchers</option> -->
							</select>
							<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
							<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
							<input name="tabindex" type="hidden" value="0" />
						</form>
					</div>
					<?php
					if(!$displayMode || $displayMode==1 || $displayMode==2){
						?>
						<div style='float:left;margin-top:3px;height:30px;'>
							<b>Taxa without Vouchers: <?php echo $nonVoucherCnt; ?></b>
							<?php
							if($clManager->getChildClidArr()){
								echo ' (excludes taxa from children checklists)';
							}
							?>
						</div>
						<div style='float:left;'>
							<a href="voucheradmin.php?clid=<?php echo $clid.'&pid='.$pid; ?>"><img src="../images/refresh.png" style="border:0px;" title="Refresh List" /></a>
						</div>
					<?php
					}
					if($displayMode){
						?>
						<div style="clear:both;">
							<div style="margin:10px;">
                                Listed below are occurrences that can be batch linked to species within the checklist.
							</div>
							<div>
								<?php
								if($specArr = $clManager->getNewVouchers($startPos,$displayMode)){
									?>
									<form name="batchnonvoucherform" method="post" action="voucheradmin.php" onsubmit="return validateBatchNonVoucherForm(this)">
										<table class="styledtable" style="font-family:Arial;font-size:12px;">
											<tr>
												<th>
													<span title="Select All">
														<input name="occids[]" type="checkbox" onclick="selectAll(this);" value="0-0" />
													</span>
												</th>
												<th>Checklist ID</th>
												<th>Collector</th>
												<th>Locality</th>
											</tr>
											<?php
											foreach($specArr as $cltid => $occArr){
												foreach($occArr as $occid => $oArr){
													echo '<tr>';
													echo '<td><input name="occids[]" type="checkbox" value="'.$occid.'-'.$cltid.'" /></td>';
													echo '<td><a href="../taxa/index.php?taxon='.$oArr['tid'].'" target="_blank">'.$oArr['sciname'].'</a></td>';
													echo '<td>';
													echo $oArr['recordedby'].' '.$oArr['recordnumber'].'<br/>';
													if($oArr['eventdate']) echo $oArr['eventdate'].'<br/>';
													echo '<a href="../collections/individual/index.php?occid='.$occid.'" target="_blank">';
													echo $oArr['collcode'];
													echo '</a>';
													echo '</td>';
													echo '<td>'.$oArr['locality'].'</td>';
													echo '</tr>';
												}
											}
											?>
										</table>
										<input name="tabindex" value="0" type="hidden" />
										<input name="clid" value="<?php echo $clid; ?>" type="hidden" />
										<input name="pid" value="<?php echo $pid; ?>" type="hidden" />
										<input name="displaymode" value="1" type="hidden" />
										<input name="usecurrent" value="1" type="checkbox" checked /> Add name using current taxonomy<br/>
										<input name="submitaction" value="Add Vouchers" type="submit" />
									</form>
								<?php
								}
								else{
									echo '<div style="font-weight:bold;font-size:120%;">No vouchers located</div>';
								}
								?>
							</div>
						</div>

					<?php
					}
					else{
						?>
						<div style="clear:both;">
							<div style="margin:10px;">
                                Listed below are species from the checklist that do not have linked occurrence vouchers. Click on name to use the search statement above to dynamically query the occurrence dataset for
                                possible voucher occurrences. Use the pulldown to the right to display the occurrences in a table format.
							</div>
							<div style="margin:20px;">
								<?php
								if($nonVoucherArr = $clManager->getNonVoucheredTaxa($startPos)){
									foreach($nonVoucherArr as $family => $tArr){
										echo '<div style="font-weight:bold;">'.strtoupper($family).'</div>';
										echo '<div style="margin:10px;text-decoration:italic;">';
										foreach($tArr as $tid => $sciname){
											?>
											<div>
												<a href="#" onclick="openPopup('../taxa/index.php?taxauthid=1&taxon=<?php echo $tid.'&cl='.$clid; ?>','taxawindow');return false;"><?php echo $sciname; ?></a>
												<a href="#" onclick="openPopup('../collections/list.php?db=all&thes=1&reset=1&taxa=<?php echo $sciname.'&targetclid='.$clid.'&targettid='.$tid;?>','editorwindow');return false;">
													<img src="../images/link.png" style="width:13px;" title="Link Voucher Specimens" />
												</a>
											</div>
										<?php
										}
										echo '</div>';
									}
									$arrCnt = $nonVoucherArr;
									if($startPos || $nonVoucherCnt > 100){
										echo '<div style="text-weight:bold;">';
										if($startPos > 0) echo '<a href="voucheradmin.php?clid='.$clid.'&pid='.$pid.'&start='.($startPos-100).'">';
										echo '&lt;&lt; Previous';
										if($startPos > 0) echo '</a>';
										echo ' || <b>'.$startPos.'-'.($startPos+($arrCnt<100?$arrCnt:100)).' Records</b> || ';
										if(($startPos + 100) <= $nonVoucherCnt) echo '<a href="voucheradmin.php?clid='.$clid.'&pid='.$pid.'&start='.($startPos+100).'">';
										echo 'Next &gt;&gt;';
										if(($startPos + 100) <= $nonVoucherCnt) echo '</a>';
										echo '</div>';
									}
								}
								else{
									echo '<h2>All taxa contain voucher links</h2>';
								}
								?>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</div>
			<div id="reportDiv">
				<div style="margin:25px;height:400px;">
					<ul>
						<li><a href="reports/voucherreporthandler.php?rtype=fullcsv&clid=<?php echo $clid; ?>">Full species list (CSV)</a></li>
						<li><a href="checklist.php?printmode=1&showvouchers=0&defaultoverride=1&cl=<?php echo $clid; ?>" target="_blank">Full species list (Print Friendly)</a></li>
						<li><a href="reports/voucherreporthandler.php?rtype=fullvoucherscsv&clid=<?php echo $clid; ?>">Full species list with vouchers (CSV)</a></li>
						<li><a href="checklist.php?printmode=1&showvouchers=1&defaultoverride=1&cl=<?php echo $clid; ?>" target="_blank">Full species list with vouchers (Print Friendly)</a></li>
						<li><a href="reports/voucherreporthandler.php?rtype=pensoftxlsx&clid=<?php echo $clid; ?>" target="_blank">Pensoft Excel Export</a></li>
						<li><a href="#" onclick="openPopup('reports/download.php?clid=<?php echo $clid; ?>','repvouchers');return false;">Occurrence vouchers only (DwC-A, CSV, Tab-delimited)</a></li>
						<li>Possible species additions based on occurrence vouchers</li>
					</ul>
					<ul style="margin:-10px 0 0 25px;list-style-type:circle">
						<li><a href="reports/voucherreporthandler.php?rtype=missingoccurcsv&clid=<?php echo $clid; ?>">Specimens of taxa missing from checklist (CSV)</a></li>
						<li><a href="reports/voucherreporthandler.php?rtype=problemtaxacsv&clid=<?php echo $clid; ?>">Specimens with misspelled, illegal, and problematic scientific names (CSV)</a></li>
					</ul>
				</div>
			</div>
		</div>
	<?php
	}
}
else{
	if(!$clid){
		echo '<div><span style="font-weight:bold;font-size:110%;">Error:</span>Checklist identifier not set</div>';
	}
	else{
		echo '<div><span style="font-weight:bold;font-size:110%;">Error:</span>You do not have administrative permission for this checklist</div>';
	}
}
?>
</div>
<?php
include($SERVER_ROOT.'/footer.php');
?>
</body>
</html>
