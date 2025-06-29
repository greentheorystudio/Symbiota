<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/ChecklistVoucherAdmin.php');

$action = array_key_exists('submitaction',$_REQUEST)?htmlspecialchars($_REQUEST['submitaction']): '';
$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?htmlspecialchars($_REQUEST['pid']): '';
$displayMode = (array_key_exists('displaymode',$_REQUEST)?(int)$_REQUEST['displaymode']:0);
$startIndex = array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0;

$vManager = new ChecklistVoucherAdmin();
$vManager->setClid($clid);
$vManager->setCollectionVariables();

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
	$isEditor = true;
}

$missingArr = array();
if($displayMode === 1){
	$missingArr = $vManager->getMissingTaxaSpecimens($startIndex);
}
elseif($displayMode === 2){
	$missingArr = $vManager->getMissingProblemTaxa();
}
else{
	$missingArr = $vManager->getMissingTaxa();
}
?>

<div id="mainContainer" style="padding: 10px 15px 15px;background-color:white;">
	<div style="display:flex;justify-content: space-between;align-content: center;align-items: center;">
        <div style='font-weight:bold;margin-left:5px'>
            <?php
            if($displayMode === 2){
                echo 'Problem Taxa: ';
            }
            else{
                echo 'Possible Missing Taxa: ';
            }
            echo $vManager->getMissingTaxaCount();
            ?>
            <a href="voucheradmin.php?clid=<?php echo $clid.'&pid='.$pid.'&displaymode='.$displayMode; ?>&tabindex=1"><i style='width:15px;height:15px;' title="Refresh List" class="fas fa-redo-alt"></i></a>
            <a href="reports/voucherreporthandler.php?rtype=<?php echo ($displayMode === 2?'problemtaxacsv':'missingoccurcsv').'&clid='.$clid; ?>" target="_blank" title="Download Occurrence Records">
                <i style='width:15px;height:15px;' class="fas fa-download"></i>
            </a>
        </div>
        <div>
            <form name="displaymodeform" method="post" action="voucheradmin.php">
                <b>Display Mode:</b>
                <select name="displaymode" onchange="this.form.submit()">
                    <option value="0">Species List</option>
                    <option value="1" <?php echo ($displayMode === 1?'SELECTED':''); ?>>Batch Linking</option>
                    <option value="2" <?php echo ($displayMode === 2?'SELECTED':''); ?>>Problem Taxa</option>
                </select>
                <input name="clid" id="clvalue" type="hidden" value="<?php echo $clid; ?>" />
                <input name="pid" type="hidden" value="<?php echo $pid; ?>" />
                <input name="tabindex" type="hidden" value="1" />
            </form>
        </div>
    </div>
	<div>
		<?php
		$recCnt = 0;
		if($displayMode === 1){
			if($missingArr){
				?>
				<div style="clear:both;margin:10px;">
					Listed below are occurrences identified to a species not found in the checklist. Use the form to add the
					names and link the vouchers as a batch action.
				</div>
				<form name="batchmissingform" method="post" action="voucheradmin.php" onsubmit="return validateBatchMissingForm();">
					<table class="styledtable" style="font-family:Arial,serif;">
						<tr>
							<th>
								<span title="Select All">
									<input name="selectallbatch" type="checkbox" onclick="selectAll(this);" value="0-0" />
								</span>
							</th>
							<th>Specimen ID</th>
							<th>Collector</th>
							<th>Locality</th>
						</tr>
						<?php
						ksort($missingArr);
						foreach($missingArr as $sciname => $sArr){
							foreach($sArr as $occid => $oArr){
								echo '<tr>';
								echo '<td><input name="occids[]" type="checkbox" value="'.$occid.'-'.$oArr['tid'].'" /></td>';
								echo '<td><a href="../taxa/index.php?taxon='.$oArr['tid'].'" target="_blank">'.$sciname.'</a></td>';
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
								$recCnt++;
							}
						}
						?>
					</table>
					<input name="tabindex" value="1" type="hidden" />
					<input name="clid" value="<?php echo $clid; ?>" type="hidden" />
					<input name="pid" value="<?php echo $pid; ?>" type="hidden" />
					<input name="displaymode" value="1" type="hidden" />
					<input name="usecurrent" style="margin-top:8px;" value="1" type="checkbox" checked /> Add name using current taxonomy<br/>
					<input name="submitaction" style="margin-top:8px;" value="Add Taxa and Vouchers" type="submit" />
					<input name="start" type="hidden" value="<?php echo $startIndex; ?>" />
				</form>
				<?php
				echo 'Specimen count: '.$recCnt;
				$queryStr = 'tabindex=1&displaymode=1&clid='.$clid.'&pid='.$pid.'&start='.(++$startIndex);
				if($recCnt > 399) {
                    echo '<a style="margin-left:10px;" href="voucheradmin.php?' . $queryStr . '">View Next 400</a>';
                }
			}
		}
		elseif($displayMode === 2){
			if($missingArr){
				?>
				<div style="clear:both;margin:10px;">
					Listed below are species name obtained from occurrences matching the above search term but
					are not found within the taxonomic thesaurus. To add as a voucher,
					type the correct name from the checklist, and then click the Link Voucher button.
					The correct name must already be added to the checklist before voucher can be linked.
				</div>
				<table class="styledtable" style="font-family:Arial,serif;">
					<tr>
						<th>Specimen ID</th>
						<th>Link to</th>
						<th>Collector</th>
						<th>Locality</th>
					</tr>
					<?php
					ksort($missingArr);
					foreach($missingArr as $sciname => $sArr){
						foreach($sArr as $occid => $oArr){
							?>
							<tr>
								<td><?php echo $sciname; ?></td>
								<td>
									<input id="tid-<?php echo $occid; ?>" name="sciname" type="text" value="" onfocus="initAutoComplete('tid-<?php echo $occid; ?>')" />
									<input name="formsubmit" type="button" value="Link Voucher" onclick="linkVoucher(<?php echo $occid.','.$clid; ?>)" title="Link Voucher" />
								</td>
								<?php
								echo '<td>';
								echo $oArr['recordedby'].' '.$oArr['recordnumber'].'<br/>';
								if($oArr['eventdate']) {
                                    echo $oArr['eventdate'] . '<br/>';
                                }
								echo '<a href="../collections/individual/index.php?occid='.$occid.'" target="_blank">';
                                echo $oArr['collcode'] ?: 'Full Record Details';
								echo '</a>';
								echo '</td>';
								?>
								<td><?php echo $oArr['locality']; ?></td>
							</tr>
							<?php
							$recCnt++;
						}
					}
					?>
				</table>
				<?php
			}
		}
		else if($missingArr){
            ?>
            <div style="margin:20px;clear:both;">
                <div style="clear:both;margin:20px;">
                    <form method="post" action="voucheradmin.php">
                        <input name="tabindex" value="1" type="hidden" />
                        <input name="clid" value="<?php echo $clid; ?>" type="hidden" />
                        <input name="pid" value="<?php echo $pid; ?>" type="hidden" />
                        <div style="display:flex;justify-content: flex-start;gap:15px;">
                            <input name="submitaction" value="Add All Taxa to Checklist" type="submit" />
                        </div>
                    </form>
                </div>
                <div style="clear:both;margin:10px;">
                    Listed below are each species, subspecies, and variety name not found in the checklist but are represented by one or more occurrence
                    records that have a locality matching the above search term.
                </div>
                <?php
                foreach($missingArr as $tid => $sn){
                    ?>
                    <div>
                        <a href="#" onclick="openPopup('../taxa/index.php?taxon=<?php echo $tid.'&cl='.$clid; ?>','taxawindow');return false;"><?php echo $sn; ?></a>
                        <a href="#" onclick="setPopup(<?php echo $tid . ',' . $clid;?>);return false;">
                            <i style='width:15px;height:15px;' title="Link Voucher Occurrences" class="fas fa-link"></i>
                        </a>
                    </div>
                    <?php
                    $recCnt++;
                }
                ?>
            </div>
            <?php
        }
		?>
	</div>
</div>
