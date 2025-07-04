<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/OccurrenceCrowdSource.php');
include_once(__DIR__ . '/../../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$uid = array_key_exists('uid',$_REQUEST)?(int)$_REQUEST['uid']:0;
$rStatus = array_key_exists('rstatus',$_REQUEST)?$_REQUEST['rstatus']:'5,10';
$start = array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?(int)$_REQUEST['limit']:500;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$csManager = new OccurrenceCrowdSource();
$csManager->setCollid($collid);

$isEditor = 0;
if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
	$isEditor = 1;
}

$statusStr = '';
if(array_key_exists('occid',$_POST)){
	$statusStr = $csManager->submitReviews($_POST);
}

$projArr = $csManager->getProjectDetails();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Crowdsourcing Edit Reviewer</title>
    <meta name="description" content="Crowdsourcing edit reviewer for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">
		function selectAll(cbObj){
            const cbStatus = cbObj.checked;
            const f = cbObj.form;
            for(let i = 0; i < f.length; i++) {
				if(f.elements[i].name === "occid[]") {
				    f.elements[i].checked = cbStatus;
				}
			}
		}

		function selectCheckbox(occid){
			document.getElementById("o-"+occid).checked = true;
		}

		function expandNotes(textObj){
			textObj.style.width = "300px";
		}

		function collapseNotes(textObj){
			textObj.style.width = "60px";
		}

		function validateReviewForm(f){
			for(let i = 0; i < f.length; i++) {
				if(f.elements[i].name === "occid[]" && f.elements[i].checked) {
				    return true;
				}
			}
			alert("No records have been selected");
			return false;
		}
	</script>
</head>
<body style="margin-left: 0; margin-right: 0;background-color:white;">
	<div id="breadcrumbs">
		<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
		<a href="index.php">Source Board</a> &gt;&gt;
		<?php
		if($collid) {
            echo '<a href="../index.php?tabindex=2&collid=' . $collid . '">Control Panel</a> &gt;&gt;';
        }
		?>
		<b>Crowdsourcing Review</b>
	</div>
	<div style="margin:10px;">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:20px;color:<?php echo (strncmp($statusStr, 'ERROR', 5) === 0 ?'red':'green');?>">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($recArr = $csManager->getReviewArr($start,$limit,$uid,$rStatus)){
			$totalCnt = $recArr['totalcnt'];
			unset($recArr['totalcnt']);
			$pageCnt = count($recArr);
			$end = ($start + $pageCnt);
			$urlPrefix = 'review.php?collid='.$collid.'&uid='.$uid.'&rstatus='.$rStatus;
			$navStr = '<b>';
			if($start > 0) {
                $navStr .= '<a href="' . $urlPrefix . '&start=0&limit=' . $limit . '">';
            }
			$navStr .= '|&lt; ';
			if($start > 0) {
                $navStr .= '</a>';
            }
			$navStr .= '&nbsp;&nbsp;&nbsp;';
			if($start > 0) {
                $navStr .= '<a href="' . $urlPrefix . '&start=' . ($start - $limit) . '&limit=' . $limit . '">';
            }
			$navStr .= '&lt;&lt;';
			if($start > 0) {
                $navStr .= '</a>';
            }
			$navStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;'.($start + 1).' - '.($end).' of '.number_format($totalCnt).'&nbsp;&nbsp;|&nbsp;&nbsp;';
			if($totalCnt > ($start+$limit)) {
                $navStr .= '<a href="' . $urlPrefix . '&start=' . ($start + $limit) . '&limit=' . $limit . '">';
            }
			$navStr .= '&gt;&gt;';
			if($totalCnt > ($start+$limit)) {
                $navStr .= '</a>';
            }
			$navStr .= '&nbsp;&nbsp;&nbsp;';
			if(($start+$pageCnt) < $totalCnt) {
                $navStr .= '<a href="' . $urlPrefix . '&start=' . (floor($totalCnt / $limit) * $limit) . '&limit=' . $limit . '">';
            }
			$navStr .= '&gt;|';
			if(($start+$pageCnt) < $totalCnt) {
                $navStr .= '</a> ';
            }
			$navStr .= '</b>';
			?>
			<div style="width:850px;">
				<div style="float:right;">
					<form name="filter" action="review.php" method="get">
						<fieldset style="width:300px;text-align:left;">
							<legend><b>Filter</b></legend>
							<div style="margin:3px;">
								<b>Review Status:</b>
								<select name="rstatus">
									<option value="5,10">All Records</option>
									<option value="5,10">----------------------</option>
									<option value="5" <?php echo ($rStatus === '5'?'SELECTED':''); ?>>Pending Review</option>
									<option value="10" <?php echo ($rStatus === '10'?'SELECTED':''); ?>>Closed (Approved)</option>
								</select>
							</div>
							<?php
							if($collid){
								?>
								<div style="margin:3px;">
									<b>Editor:</b>
									<select name="uid">
										<option value="">All Editors</option>
										<option value="">----------------------</option>
										<?php
										$editorArr = $csManager->getEditorList();
										foreach($editorArr as $eUid => $eName){
											echo '<option value="'.$eUid.'" '.($eUid === $uid?'SELECTED':'').'>'.$eName.'</option>'."\n";
										}
										?>
									</select>
								</div>
								<?php
							}
							else{
								echo '<input name="uid" type="hidden" value="'.$uid.'" />';
							}
							?>
							<div style="margin:3px;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="action" type="submit" value="Filter Records" />
							</div>
						</fieldset>
					</form>
				</div>
				<div style="font-weight:bold;">
					<?php echo ($collid?$projArr['name']:$GLOBALS['USER_DISPLAY_NAME']); ?>
				</div>
				<div style="clear:both;">
					<?php echo $navStr; ?>
				</div>
			</div>
			<div style="clear:both;">
				<?php
				if($totalCnt){
					?>
					<div style="clear:both;">
						<form name="reviewform" method="post" action="review.php" onsubmit="return validateReviewForm(this)">
							<?php
							if($collid){
								echo '<input name="collid" type="hidden" value="'.$collid.'" />';
								echo '<input name="rstatus" type="hidden" value="'.$rStatus.'" />';
								echo '<input name="uid" type="hidden" value="'.$uid.'" />';
							}
							?>
							<table class="styledtable" style="font-family:Arial,serif;">
								<tr>
									<?php
									if($collid) {
                                        echo '<th><span title="Select All"><input name="selectall" type="checkbox" onclick="selectAll(this)" /></span></th>';
                                    }
									?>
									<th>Points</th>
									<th>Comments</th>
									<th>Edit</th>
									<?php
									$header = $csManager->getHeaderArr();
									foreach($header as $v){
										echo '<th>'.$v.'</th>';
									}
									?>
								</tr>
								<?php
								$cnt = 0;
								foreach($recArr as $occid => $rArr){
									?>
									<tr <?php echo (($cnt%2)?'class="alt"':'') ?>>
										<?php
                                        $notes = $rArr['notes'] ?? '';
                                        $points = $rArr['points'] ?? 2;
                                        if($collid){
											echo '<td><input id="o-'.$occid.'" name="occid[]" type="checkbox" value="'.$occid.'" /></td>';
											echo '<td><select name="p-'.$occid.'" style="width:45px;" onchange="selectCheckbox('.$occid.')">';
											echo '<option value="0" '.($points === '0'?'SELECTED':'').'>0</option>';
											echo '<option value="1" '.($points === '1'?'SELECTED':'').'>1</option>';
											echo '<option value="2" '.($points === '2'?'SELECTED':'').'>2</option>';
											echo '<option value="3" '.($points === '3'?'SELECTED':'').'>3</option>';
											echo '</select></td>';
											echo '<td><input name="c-'.$occid.'" type="text" value="'.$notes.'" style="width:60px;" onfocus="expandNotes(this)" onblur="collapseNotes(this)" onchange="selectCheckbox('.$occid.')" /></td>';
										}
										else{
											echo '<td><input name="p-'.$occid.'" type="text" value="'.$points.'" style="width:15px;" DISABLED /></td>';
											echo '<td>'.$notes.'</td>';
										}
										?>
										<td>
											<?php
											if($isEditor || $rArr['reviewstatus'] === 5){
												echo '<a href="../../editor/occurrenceeditor.php?csmode=1&occid='.$occid.'" target="_blank">';
												echo '<i style="height:15px;width:15px;" class="far fa-edit"></i>';
												echo '</a>';
											}
											else{
												echo '<i style="height:15px;width:15px;" class="far fa-times-circle"></i>';
											}
											?>
										</td>
										<?php
										foreach($header as $v){
											$displayStr = $rArr[$v];
											if(strlen($displayStr) > 40){
												$displayStr = substr($displayStr,0,40).'...';
											}
											echo '<td>'.$displayStr.'</td>'."\n";
										}
										?>
									</tr>
									<?php
									$cnt++;
								}
								?>
							</table>
							<div style="width:850px;">
								<div>
									<?php echo $navStr; ?>
								</div>
								<div style="clear:both;">
									<?php
									if($collid){
										echo '<div style="float:left"><input name="action" type="submit" value="Submit Reviews" /></div>';
										echo '<div style="float:left; margin-left:15px"><input name="updateProcessingStatus" type="checkbox" value="1" checked /> Set Processing Status to reviewed (unchecking will leave Processing Status as set by user for each record)</div>';
									}
									?>
								</div>
							</div>
						</form>
					</div>
					<?php
				}
				else if($collid && $rStatus === 5){
                    ?>
                    <div style="clear:both;margin:30px 15px;font-weight:bold;">
                        <div style="">
                            There are no more records to review for this user
                        </div>
                        <div style="margin:15px;">
                            Return to <a href="../index.php?tabindex=2&collid=<?php echo $collid; ?>">Control Panel</a>
                        </div>
                        <div style="margin:15px;">
                            Return to <a href="index.php">Source Board</a>
                        </div>
                    </div>
                    <?php
                }
                else{
                    ?>
                    <div style="clear:both;padding-top:30px;font-weight:bold;">
                        There are no records matching search criteria
                    </div>
                    <?php
                }
				?>
			</div>
			<?php
		}
		?>
	</div>
    <?php
    include_once(__DIR__ . '/../../../config/footer-includes.php');
    ?>
</body>
</html>
