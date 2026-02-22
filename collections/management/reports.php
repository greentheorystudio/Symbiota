<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCleaner.php');
include_once(__DIR__ . '/../../classes/SpecProcessorManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$menu = array_key_exists('menu',$_REQUEST)&&$_REQUEST['menu']?(int)$_REQUEST['menu']:0;
$formAction = array_key_exists('formaction',$_REQUEST)?$_REQUEST['formaction']:'';

$cleanManager = new OccurrenceCleaner();
$procManager = new SpecProcessorManager();
$procManager->setCollId($collid);
$cleanManager->setCollId($collid);
$tabIndex = 1;

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
 	$isEditor = true;
}
?>
<div id="mainContainer" style="padding: 10px 15px 15px;background-color:white;">
	<?php
	if($isEditor){
		$reportTypes = array(0 => 'General Stats', 1 => 'User Stats', 2 => 'Georeference Stats');
		?>
		<form name="filterForm" action="index.php" method="get">
			<b>Report Type:</b>
			<select name="menu" onchange="this.form.submit()">
				<?php
				foreach($reportTypes as $k => $v){
					echo '<option value="'.$k.'" '.($menu === $k?'SELECTED':'').'>'.$v.'</option>';
				}
				?>
			</select>
			<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
			<input name="tabindex" type="hidden" value="<?php echo $tabIndex; ?>" />
		</form>

		<fieldset style="padding:15px">
			<legend><b><?php echo $reportTypes[$menu]; ?></b></legend>
			<?php
			if(!$menu){
				$statsArr = $procManager->getProcessingStats();
				?>
				<div style="margin:10px;">
					<div style="margin:5px;">
						<b>Total Occurrences:</b>
						<?php
						echo $statsArr['total'];
						if($statsArr['total']){
							$fullEdUrl = '../editor/occurrenceeditor.php?collid='.$collid.'&starr={"collid":'.$collid.'}';
                            echo "<span style='margin-left:10px;'><a href='".$fullEdUrl."' target='_blank' title='Edit Records'><i style='height:15px;width:15px;' class='far fa-edit'></i></a></span>";
							echo '<span style="margin-left:10px;"><a href="../table.php?collid='.$collid.'" target="_blank" title="Editor in Table View"><i style="height:15px;width:15px;" class="fas fa-list"></i></a></span>';
							echo '<span style="margin-left:10px;"><a href="../download/index.php?collid='.$collid.'&tabindex=1" target="_blank" title="Download Full Data"><i style="height:15px;width:15px;" class="fas fa-download"></i></a></span>';
						}
						?>
					</div>
					<div style="margin:5px;">
						<b>Occurrences without linked images:</b>
						<?php
						echo $statsArr['noimg'];
						if($statsArr['noimg']){
							$eUrl1 = '../editor/occurrenceeditor.php?collid='.$collid.'&starr={"collid":'.$collid.',"withoutimages":1}';
							$beUrl1 = '../table.php?starr={"collid":'.$collid.',"withoutimages":1}';
							echo "<span style='margin-left:10px;'><a href='".$eUrl1."' target='_blank' title='Edit Records'><i style='height:15px;width:15px;' class='far fa-edit'></i></a></span>";
							echo "<span style='margin-left:10px;'><a href='".$beUrl1."' target='_blank' title='Batch Edit Records'><i style='height:15px;width:15px;' class='fas fa-list'></i></a></span>";
							echo '<span style="margin-left:10px;"><a href="processor.php?submitaction=dlnoimg&tabindex='.$tabIndex.'&collid='.$collid.'" target="_blank" title="Download Report File"><i style="height:15px;width:15px;" class="fas fa-download"></i></a></span>';
						}
						?>
					</div>
					<?php
					if($statsArr['noskel']){
						?>
						<div style="margin:5px;">
							<b>Unprocessed records without Skeletal Data:</b>
							<?php
							echo $statsArr['noskel'];
							if($statsArr['noskel']){
								$eUrl3 = '../editor/occurrenceeditor.php?collid='.$collid.'&starr={"collid":'.$collid.',"advanced":[{"concatenator":null,"openParens":null,"field":"stateprovince","dataType":null,"operator":"IS NULL","value":null,"closeParens":null},{"concatenator":"AND","openParens":null,"field":"sciname","dataType":null,"operator":"IS NULL","value":null,"closeParens":null}],"processingstatus":"Unprocessed"}';
                                $beUrl3 = '../table.php?starr={"collid":'.$collid.',"advanced":[{"concatenator":null,"openParens":null,"field":"stateprovince","dataType":null,"operator":"IS NULL","value":null,"closeParens":null},{"concatenator":"AND","openParens":null,"field":"sciname","dataType":null,"operator":"IS NULL","value":null,"closeParens":null}],"processingstatus":"Unprocessed"}';
								echo "<span style='margin-left:10px;'><a href='".$eUrl3."' target='_blank' title='Edit Records'><i style='height:15px;width:15px;' class='far fa-edit'></i></a></span>";
								echo "<span style='margin-left:10px;'><a href='".$beUrl3."' target='_blank' title='Batch Edit Records'><i style='height:15px;width:15px;' class='fas fa-list'></i></a></span>'";
								echo '<span style="margin-left:10px;"><a href="processor.php?submitaction=noskel&tabindex='.$tabIndex.'&collid='.$collid.'" target="_blank" title="Download Report File"><i style="height:15px;width:15px;" class="fas fa-download"></i></a></span>';
							}
							?>
						</div>
						<?php
					}
					if($statsArr['unprocnoimg']){
						?>
						<div style="margin:5px;">
							<b>Unprocessed records without Images (<span style="color:orange">possible issue</span>):</b>
							<?php
							echo $statsArr['unprocnoimg'];
							if($statsArr['unprocnoimg']){
								$eUrl2 = '../editor/occurrenceeditor.php?collid='.$collid.'&starr={"collid":'.$collid.',"withoutimages":1,"processingstatus":"Unprocessed"}';
                                $beUrl2 = '../table.php?starr={"collid":'.$collid.',"withoutimages":1,"processingstatus":"Unprocessed"}';
								echo "<span style='margin-left:10px;'><a href='".$eUrl2."' target='_blank' title='Edit Records'><i style='height:15px;width:15px;' class='far fa-edit'></i></a></span>";
								echo "<span style='margin-left:10px;'><a href='".$beUrl2."' target='_blank' title='Batch Edit Records'><i style='height:15px;width:15px;' class='fas fa-list'></i></a></span>";
								echo '<span style="margin-left:10px;"><a href="processor.php?submitaction=unprocnoimg&tabindex='.$tabIndex.'&collid='.$collid.'" target="_blank" title="Download Report File"><i style="height:15px;width:15px;" class="fas fa-download"></i></a></span>';
							}
							?>
						</div>
						<?php
					}
					if($statsArr['unprocwithdata']){
						?>
						<div style="margin:5px;">
							<b>Unprocessed records with Locality details (<span style="color:orange">possible issue</span>):</b>
							<?php
							echo $statsArr['unprocwithdata'];
							if($statsArr['unprocwithdata']){
								$eUrl3b = '../editor/occurrenceeditor.php?collid='.$collid.'&starr={"collid":'.$collid.',"advanced":[{"concatenator":null,"openParens":null,"field":"locality","dataType":null,"operator":"IS NOT NULL","value":null,"closeParens":null},{"concatenator":"AND","openParens":null,"field":"stateprovince","dataType":null,"operator":"IS NOT NULL","value":null,"closeParens":null}],"processingstatus":"Unprocessed"}';
                                $beUrl3b = '../table.php?starr={"collid":'.$collid.',"advanced":[{"concatenator":null,"openParens":null,"field":"locality","dataType":null,"operator":"IS NOT NULL","value":null,"closeParens":null},{"concatenator":"AND","openParens":null,"field":"stateprovince","dataType":null,"operator":"IS NOT NULL","value":null,"closeParens":null}],"processingstatus":"Unprocessed"}';
								echo "<span style='margin-left:10px;'><a href='".$eUrl3b."' target='_blank' title='Edit Records'><i style='height:15px;width:15px;' class='far fa-edit'></i></a></span>";
								echo "<span style='margin-left:10px;'><a href='".$beUrl3b."' target='_blank' title='Batch Edit Records'><i style='height:15px;width:15px;' class='fas fa-list'></i></a></span>";
								echo '<span style="margin-left:10px;"><a href="processor.php?submitaction=unprocwithdata&tabindex='.$tabIndex.'&collid='.$collid.'" target="_blank" title="Download Report File"><i style="height:15px;width:15px;" class="fas fa-download"></i></a></span>';
							}
							?>
						</div>
						<?php
					}
					?>
					<div style="margin:20px 5px;">
						<table class="styledtable" style="width:400px;">
							<tr><th>Processing Status</th><th>Count</th></tr>
							<?php
							foreach($statsArr['ps'] as $processingStatus => $cnt){
								if($processingStatus && $processingStatus !== 'no-status'){
                                    $eUrl4 = '../editor/occurrenceeditor.php?collid='.$collid.'&starr={"collid":'.$collid.',"processingstatus":"'.$processingStatus.'"}';
                                    $beUrl4 = '../table.php?starr={"collid":'.$collid.',"processingstatus":"'.$processingStatus.'"}';
                                }
                                else{
                                    $eUrl4 = '../editor/occurrenceeditor.php?collid='.$collid.'&starr={"collid":'.$collid.',"advanced":[{"concatenator":null,"openParens":null,"field":"processingstatus","dataType":null,"operator":"IS NULL","value":null,"closeParens":null}]}';
                                    $beUrl4 = '../table.php?starr={"collid":'.$collid.',"advanced":[{"concatenator":null,"openParens":null,"field":"processingstatus","dataType":null,"operator":"IS NULL","value":null,"closeParens":null}]}';
                                    $processingStatus = 'No Status Set';
                                }
								echo '<tr>';
								echo '<td>'.$processingStatus.'</td>';
								echo '<td>';
								echo $cnt;
								if($cnt){
									echo "<span style='margin-left:10px;'><a href='".$eUrl4."' target='_blank' title='Edit Records'><i style='height:15px;width:15px;' class='far fa-edit'></i></a></span>";
									echo "<span style='margin-left:10px;'><a href='".$beUrl4."' target='_blank' title='Batch Edit Records'><i style='height:15px;width:15px;' class='fas fa-list'></i></a></span>";
								}
								echo '</td>';
								echo '</tr>';
							}
							?>
						</table>
					</div>
				</div>
				<?php
			}
			elseif($menu === 1){
				$uid = ($_GET['uid'] ?? '');
				$interval= ($_GET['interval'] ?? 'day');
				$startDate = ($_GET['startdate'] ?? '');
				$endDate = ($_GET['enddate'] ?? '');
				$processingStatus = ($_GET['processingstatus'] ?? 0);
				$excludeBatch = ($_GET['excludebatch'] ?? '');
				?>
				<fieldset style="padding:15px;width:400px;">
					<legend><b>Filter</b></legend>
					<form name="userStatsFilterForm" method="get" action="index.php">
						<div style="margin:2px">
							Editors:
							<select name="uid">
								<option value="0">Show all users</option>
								<option value="0">-----------------------</option>
								<?php
								$userArr = $procManager->getUserList();
								foreach($userArr as $id => $uname){
									echo '<option value="'.$id.'" '.($uid === $id?'SELECTED':'').'>'.$uname.'</option>';
								}
								?>
							</select>
						</div>
						<div style="margin:2px">
							Interval:
							<select name="interval">
								<option value="hour" <?php echo ($interval === 'hour'?'SELECTED':''); ?>>Hour</option>
								<option value="day" <?php echo ($interval === 'day'?'SELECTED':''); ?>>Day</option>
								<option value="week" <?php echo ($interval === 'week'?'SELECTED':''); ?>>Week</option>
								<option value="month" <?php echo ($interval === 'month'?'SELECTED':''); ?>>Month</option>
							</select>
						</div>
						<div style="margin:2px">
							Date: <input name="startdate" type="date" value="<?php echo $startDate; ?>" />
							to <input name="enddate" type="date" value="<?php echo ($_GET['enddate'] ?? ''); ?>" />
						</div>
						<div style="margin:2px">
							Processing Status set to:
							<select name="processingstatus">
								<option value="0">Ignore Processing Status</option>
								<option value="all" <?php echo ($processingStatus === 'all' ?'SELECTED':''); ?>>Any Processing Status</option>
								<option value="0">-----------------------</option>
								<?php
								$psArr = array('Unprocessed','Stage 1','Stage 2','Stage 3','Pending Duplicate','Pending Review-NfN','Pending Review','Expert Required','Reviewed','Closed');
								foreach($psArr as $psValue){
									$psValue = strtolower($psValue);
									echo '<option value="'.$psValue.'" '.($processingStatus && $processingStatus === $psValue?'SELECTED':'').'>'.$psValue.'</option>';
								}
								?>
							</select>
						</div>
						<div style="float:right;margin-top:25px;">
							<?php
							$editReviewUrl = '../editor/editreviewer.php?collid='.$collid.'&editor='.$uid.'&startdate='.$startDate.'&enddate='.$endDate;
							echo '<a href="'.$editReviewUrl.'" target="_blank">Visit Edit Reviewer</a>';
							?>
						</div>
						<div style="margin-top:15px">
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="menu" type="hidden" value="1" />
							<input name="tabindex" type="hidden" value="<?php echo $tabIndex; ?>" />
							<button name="formaction" type="submit" value="displayReport">Display Report</button>
						</div>
					</form>
				</fieldset>
				<?php
				if($formAction === 'displayReport'){
					echo '<table class="styledtable" style="width:500px;">';
					echo '<tr><th>Time Period</th>';
					echo '<th>User</th>';
					if($processingStatus) {
                        echo '<th>Previous Status</th><th>Saved Status</th><th>Current Status</th>';
                    }
					echo '<th>All Edits</th>';
					if($procManager->hasEditType()) {
                        echo '<th>Excluding Batch Edits</th>';
                    }
					echo '</tr>';
					$repArr = $procManager->getFullStatReport($_GET);
					if($repArr){
						foreach($repArr as $t => $arr2){
							foreach($arr2 as $u => $arr3){
								echo '<tr><td>'.$t.'</td>';
								echo '<td>'.$u.'</td>';
								if(array_key_exists('cs', $arr3)){
									echo '<td>'.$arr3['os'].'</td>';
									echo '<td>'.$arr3['ns'].'</td>';
									echo '<td>'.$arr3['cs'].'</td>';
								}
								echo '<td>'.$arr3['cnt'].'</td>';
								if(array_key_exists('cntexcbatch', $arr3)) {
                                    echo '<td>' . $arr3['cntexcbatch'] . '</td>';
                                }
								echo '</tr>';
							}
						}
					}
					else{
						echo '<div style="font-weight:bold">No Records Returned</div>';
					}
					echo '</table>';
				}
			}
            elseif($menu === 2){
                ?>
                <ul>
                    <?php
                    $statsArr = $cleanManager->getCoordStats();
                    ?>
                    <li>Georeferenced: <?php echo $statsArr['coord']; ?>
                        <?php
                        if($statsArr['coord']){
                            ?>
                            <a href='../table.php?starr={"collid":<?php echo $collid; ?>,"advanced":[{"concatenator":null,"openParens":null,"field":"decimallatitude","dataType":null,"operator":"IS NOT NULL","value":null,"closeParens":null}]}' style="margin-left:5px;' title="Open Editor" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </a>
                            <?php
                        }
                        ?>
                    </li>
                    <li>Lacking coordinates: <?php echo $statsArr['noCoord']; ?>
                        <?php
                        if($statsArr['noCoord']){
                            ?>
                            <a href='../table.php?starr={"collid":<?php echo $collid; ?>,"advanced":[{"concatenator":null,"openParens":null,"field":"decimallatitude","dataType":null,"operator":"IS NULL","value":null,"closeParens":null}]}' style="margin-left:5px;" title="Open Editor" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </a>
                            <a href="../georef/batchgeoreftool.php?collid=<?php echo $collid; ?>" style="margin-left:5px;" title="Open Batch Georeference Tool" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i><span style="margin-left:-3px;">b-geo</span>
                            </a>
                            <?php
                        }
                        ?>
                    </li>
                    <li style="margin-left:15px">Lacking coordinates with verbatim coordinates: <?php echo $statsArr['noCoord_verbatim']; ?>
                        <?php
                        if($statsArr['noCoord_verbatim']){
                            ?>
                            <a href='../table.php?starr={"collid":<?php echo $collid; ?>,"advanced":[{"concatenator":null,"openParens":null,"field":"decimallatitude","dataType":null,"operator":"IS NULL","value":null,"closeParens":null},{"concatenator":"AND","openParens":null,"field":"verbatimcoordinates","dataType":null,"operator":"IS NOT NULL","value":null,"closeParens":null}]}' style="margin-left:5px;" title="Open Editor" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </a>
                            <?php
                        }
                        ?>
                    </li>
                    <li style="margin-left:15px">Lacking coordinates without verbatim coordinates: <?php echo $statsArr['noCoord_noVerbatim']; ?>
                        <?php
                        if($statsArr['noCoord_noVerbatim']){
                            ?>
                            <a href='../table.php?starr={"collid":<?php echo $collid; ?>,"advanced":[{"concatenator":null,"openParens":null,"field":"decimallatitude","dataType":null,"operator":"IS NULL","value":null,"closeParens":null},{"concatenator":"AND","openParens":null,"field":"verbatimcoordinates","dataType":null,"operator":"IS NULL","value":null,"closeParens":null}]}' style="margin-left:5px;" title="Open Editor" target="_blank">
                                <i style="height:15px;width:15px;" class="far fa-edit"></i>
                            </a>
                            <?php
                        }
                        ?>
                    </li>
                </ul>
                <?php
            }
			?>
		</fieldset>
		<?php
	}
	?>
</div>
