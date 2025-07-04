<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistVoucherAdmin.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']): '';
$startPos = (array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0);
$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;
$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']): '';
$displayMode = (array_key_exists('displaymode',$_REQUEST)?(int)$_REQUEST['displaymode']:0);

$clManager = new ChecklistVoucherAdmin();
$clManager->setClid($clid);
$clManager->setCollectionVariables();

$statusStr = '';
$isEditor = 0;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
	$isEditor = 1;
	if($action === 'SaveSearch'){
		$clManager->saveQueryVariables($_POST);
	}
	elseif($action === 'DeleteVariables'){
		$statusStr = $clManager->deleteQueryVariables();
	}
	elseif($action === 'Add Vouchers'){
		$clManager->linkVouchers($_POST['occids']);
	}
	elseif($action === 'Add Taxa and Vouchers'){
		$clManager->linkTaxaVouchers($_POST['occids'],(array_key_exists('usecurrent',$_POST)?$_POST['usecurrent']:0));
	}
	elseif($action === 'resolveconflicts'){
		$clManager->batchAdjustChecklist($_POST);
	}
    elseif($action === 'Add All Taxa to Checklist'){
        $clManager->batchAddAllUnlinkedTaxa();
    }
}
$clManager->setCollectionVariables();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklist Voucher Administration</title>
    <meta name="description" content="Manage checklist voucher data">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        let clid = <?php echo $clid; ?>;
        let tabIndex = <?php echo $tabIndex; ?>;

        function openSpatialInputWindow(type) {
            let mapWindow = open("<?php echo $GLOBALS['CLIENT_ROOT']; ?>/spatial/index.php?windowtype=" + type,"input","resizable=0,width=900,height=700,left=100,top=20");
            if (mapWindow.opener == null) {
                mapWindow.opener = self;
            }
            mapWindow.addEventListener('blur', function(){
                mapWindow.close();
                mapWindow = null;
            });
        }

        function setPopup(sciname,clid){
            if(!Number(sciname)){
                sciname = sciname.replaceAll("'",'%squot;');
            }
            const starrObj = {
                usethes: true,
                taxa: sciname,
                clid: clid
            };
            const url = '<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/list.php?starr=' + JSON.stringify(starrObj);
            openPopup(url);
        }
    </script>
	<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/checklists.voucheradmin.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
	<style>
		li{margin:5px;}
	</style>
</head>

<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="breadcrumbs">
	<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
	<a href="checklist.php?clid=<?php echo $clid.'&pid='.$pid; ?>">Return to Checklist</a> &gt;&gt;
	<b>Checklist Administration</b>
</div>

<div id="mainContainer" style="padding: 10px 15px 15px;">
<div style="color:#990000;font-weight:bold;margin:0 10px 10px 0;">
	<a href="checklist.php?clid=<?php echo $clid.'&pid='.$pid; ?>">
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
			<span style="margin-left:10px;"><a href="#" onclick="toggle('sqlbuilderdiv');return false;" title="Edit Search Statement"><i style='width:15px;height:15px;' class="far fa-edit"></i></a></span>
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
								<input type="text" name="country" value="<?php echo $termArr['country'] ?? ''; ?>" />
							</div>
							<div style="margin:2px;">
								<b>State:</b>
								<input type="text" name="state" value="<?php echo $termArr['state'] ?? ''; ?>" />
							</div>
							<div style="margin:2px;">
								<b>County:</b>
								<input type="text" name="county" value="<?php echo $termArr['county'] ?? ''; ?>" />
							</div>
							<div style="margin:2px;">
								<b>Locality:</b>
								<input type="text" name="locality" value="<?php echo $termArr['locality'] ?? ''; ?>" />
							</div>
							<div style="margin:2px;" title="Genus, family, or higher rank">
								<b>Taxon:</b>
								<input type="text" name="taxon" value="<?php echo $termArr['taxon'] ?? ''; ?>" />
							</div>
							<div>
								<b>Collection:</b>
								<select name="collid" style="width:275px;">
									<option value="">Search All Collections</option>
									<option value="">-------------------------------------</option>
									<?php
									if($termArr){
                                        $selCollid = $termArr['collid'] ? (int)$termArr['collid'] : 0;
                                        foreach($collList as $id => $name){
                                            echo '<option value="'.$id.'" '.($selCollid === (int)$id?'SELECTED':'').'>'.$name.'</option>';
                                        }
                                    }
									?>
								</select>
							</div>
							<div>
								<b>Collector:</b>
								<input name="recordedby" type="text" value="<?php echo $termArr['recordedby'] ?? ''; ?>" style="width:250px" title="Enter multiple collectors separated by semicolons" />
							</div>
						</td>
						<td style="padding-left:20px;">
							<div style="float:left;">
								<div>
									<b>Lat North:</b>
									<input id="upperlat" type="text" name="latnorth" style="width:70px;" value="<?php echo $termArr['latnorth'] ?? ''; ?>" title="Latitude North" />
									<a href="#" onclick="openSpatialInputWindow('input-box');"><i style='width:15px;height:15px;' title="Find Coordinate" class="fas fa-globe"></i></a>
								</div>
								<div>
									<b>Lat South:</b>
									<input id="bottomlat" type="text" name="latsouth" style="width:70px;" value="<?php echo $termArr['latsouth'] ?? ''; ?>" title="Latitude South" />
								</div>
								<div>
									<b>Long East:</b>
									<input id="rightlong" type="text" name="lngeast" style="width:70px;" value="<?php echo $termArr['lngeast'] ?? ''; ?>" title="Longitude East" />
								</div>
								<div>
									<b>Long West:</b>
									<input id="leftlong" type="text" name="lngwest" style="width:70px;" value="<?php echo $termArr['lngwest'] ?? ''; ?>" title="Longitude West" />
								</div>
								<div>
									<input type="checkbox" name="latlngor" value="1" <?php echo (isset($termArr['latlngor'])?'CHECKED':''); ?> />
                                    Include Lat/Long and locality as an "OR" condition
								</div>
								<div>
									<input name="onlycoord" value="1" type="checkbox" <?php echo (isset($termArr['onlycoord'])?'CHECKED':''); ?> />
                                    Only include occurrences with coordinates
								</div>
								<div>
									<input name="excludecult" value="1" type="checkbox" <?php echo (isset($termArr['excludecult'])?'CHECKED':''); ?> />
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
				<li><a href="vamissingtaxa.php?clid=<?php echo $clid.'&pid='.$pid.'&start='.$startPos.'&displaymode='.($tabIndex === 1?$displayMode:0); ?>"><span>Missing Taxa</span></a></li>
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
								<option value="1" <?php echo ($displayMode === 1?'SELECTED':''); ?>>Occurrences for non-vouchered taxa</option>
								<option value="2" <?php echo ($displayMode === 2?'SELECTED':''); ?>>New occurrences for all taxa</option>
							</select>
							<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
							<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
							<input name="tabindex" type="hidden" value="0" />
						</form>
					</div>
					<?php
					if(!$displayMode || $displayMode === 1 || $displayMode === 2){
						?>
						<div style='float:left;margin-top:3px;height:30px;'>
							<b>Taxa without Vouchers: <?php echo $nonVoucherCnt; ?></b>
							<?php
							if($clManager->getChildClidArr()){
								echo ' (excludes taxa from children checklists)';
							}
							?>
                            <a href="voucheradmin.php?clid=<?php echo $clid.'&pid='.$pid; ?>"><i style='width:15px;height:15px;' title="Refresh List" class="fas fa-redo-alt"></i></a>
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
									<form name="batchnonvoucherform" method="post" action="voucheradmin.php" onsubmit="return validateBatchNonVoucherForm()">
										<table class="styledtable" style="font-family:Arial,serif;">
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
													if($oArr['eventdate']) {
                                                        echo $oArr['eventdate'] . '<br/>';
                                                    }
													echo '<a href="../collections/individual/index.php?occid='.$occid.'" target="_blank">';
                                                    echo $oArr['collcode'] ?: 'Full Record Details';
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
									echo '<div style="font-weight:bold;">No vouchers located</div>';
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
										echo '<div style="margin:10px;font-style:italic;">';
										foreach($tArr as $tid => $sciname){
											?>
											<div>
												<a href="#" onclick="openPopup('../taxa/index.php?taxon=<?php echo $tid.'&cl='.$clid; ?>');return false;"><?php echo $sciname; ?></a>
												<a href="#" onclick="setPopup(<?php echo $tid . ',' . $clid;?>);">
													<i style='width:15px;height:15px;' title="Link Voucher Occurrences" class="fas fa-link"></i>
												</a>
											</div>
										<?php
										}
										echo '</div>';
									}
									$arrCnt = $nonVoucherArr;
									if($startPos || $nonVoucherCnt > 100){
										echo '<div style="font-weight:bold;">';
										if($startPos > 0) {
                                            echo '<a href="voucheradmin.php?clid=' . $clid . '&pid=' . $pid . '&start=' . ($startPos - 100) . '">';
                                        }
										echo '&lt;&lt; Previous';
										if($startPos > 0) {
                                            echo '</a>';
                                        }
										echo ' || <b>'.$startPos.'-'.($startPos+($arrCnt<100?$arrCnt:100)).' Records</b> || ';
										if(($startPos + 100) <= $nonVoucherCnt) {
                                            echo '<a href="voucheradmin.php?clid=' . $clid . '&pid=' . $pid . '&start=' . ($startPos + 100) . '">';
                                        }
										echo 'Next &gt;&gt;';
										if(($startPos + 100) <= $nonVoucherCnt) {
                                            echo '</a>';
                                        }
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
						<li><a href="checklist.php?printmode=1&showvouchers=0&defaultoverride=1&clid=<?php echo $clid; ?>" target="_blank">Full species list (Print Friendly)</a></li>
						<li><a href="reports/voucherreporthandler.php?rtype=fullvoucherscsv&clid=<?php echo $clid; ?>">Full species list with vouchers (CSV)</a></li>
						<li><a href="checklist.php?printmode=1&showvouchers=1&defaultoverride=1&clid=<?php echo $clid; ?>" target="_blank">Full species list with vouchers (Print Friendly)</a></li>
						<li><a href="reports/voucherreporthandler.php?rtype=pensoftxlsx&clid=<?php echo $clid; ?>" target="_blank">Pensoft Excel Export</a></li>
						<li><a href="#" onclick="openPopup('reports/download.php?clid=<?php echo $clid; ?>');return false;">Occurrence vouchers only (DwC-A, CSV, Tab-delimited)</a></li>
						<li>Possible species additions based on occurrence vouchers</li>
					</ul>
					<ul style="margin:-10px 0 0 25px;list-style-type:circle">
						<li><a href="reports/voucherreporthandler.php?rtype=missingoccurcsv&clid=<?php echo $clid; ?>">Occurrences of taxa missing from checklist (CSV)</a></li>
						<li><a href="reports/voucherreporthandler.php?rtype=problemtaxacsv&clid=<?php echo $clid; ?>">Occurrences with misspelled, illegal, and problematic scientific names (CSV)</a></li>
					</ul>
				</div>
			</div>
		</div>
	<?php
	}
}
elseif($clid) {
    echo '<div><span style="font-weight:bold;">Error:</span>You do not have administrative permission for this checklist</div>';
}
else {
    echo '<div><span style="font-weight:bold;">Error:</span>Checklist identifier not set</div>';
}
?>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
