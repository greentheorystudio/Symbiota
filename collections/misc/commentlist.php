<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceSupport.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ' . $GLOBALS['CLIENT_ROOT'] . '/profile/index.php?refurl=../collections/misc/commentlist.php?' . $_SERVER['QUERY_STRING']);
}

$collid = (int)$_REQUEST['collid'];
$start = array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?(int)$_REQUEST['limit']:100;
$tsStart = array_key_exists('tsstart',$_POST)?$_POST['tsstart']:'';
$tsEnd = array_key_exists('tsend',$_POST)?$_POST['tsend']:'';
$uid = array_key_exists('uid',$_POST)?(int)$_POST['uid']:0;
$rs = array_key_exists('rs',$_POST)?(int)$_POST['rs']:1;

$commentManager = new OccurrenceSupport();

$isEditor = 0; 
if($GLOBALS['SYMB_UID']){
	if($GLOBALS['IS_ADMIN']){
		$isEditor = 1;
	}
	elseif($collid){
		if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
			$isEditor = 1;
		}
	}
}

$statusStr = '';
$commentArr = null;
if($isEditor){
	$formSubmit = array_key_exists('formsubmit',$_REQUEST)?$_REQUEST['formsubmit']:'';
	if($formSubmit){
		if($formSubmit === 'Delete Comment'){
			if(!$commentManager->deleteComment($_POST['comid'])){
				$statusStr = $commentManager->getErrorStr();
			}
		}
		elseif($formSubmit === 'Make comment public'){
			if(!$commentManager->setReviewStatus($_POST['comid'],1)){
				$statusStr = $commentManager->getErrorStr();
			}
		}
		elseif($formSubmit === 'Hide Comment from Public'){
			if(!$commentManager->setReviewStatus($_POST['comid'],2)){
				$statusStr = $commentManager->getErrorStr();
			}
		}
		elseif($formSubmit === 'Mark as reviewed'){
			if(!$commentManager->setReviewStatus($_POST['comid'],3)){
				$statusStr = $commentManager->getErrorStr();
			}
		}
	}
	$commentArr = $commentManager->getComments($collid, $start, $limit, $tsStart, $tsEnd, $uid, $rs);
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title>Comments Listing</title>
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<script>
			function dateChanged(dateInput){
				if(dateInput.value !== ""){
                    const dateArr = parseDate(dateInput.value);
                    if(dateArr['y'] === 0){
						alert("Unable to interpret Date. Please use the following formats: 2016-05-21, 05/21/2016, 21 May 2016, May 2016, or simply 2016");
						return false;
					}
					else{
						if(dateArr['m'] > 12){
							alert("Month cannot be greater than 12. Note that the format should be YYYY-MM-DD");
							return false;
						}
			
						if(dateArr['d'] > 28){
							if(dateArr['d'] > 31 
								|| (dateArr['d'] === 30 && dateArr['m'] === 2)
								|| (dateArr['d'] === 31 && (dateArr['m'] === 4 || dateArr['m'] === 6 || dateArr['m'] === 9 || dateArr['m'] === 11))){
								alert("The Day (" + dateArr['d'] + ") is invalid for that month");
								return false;
							}
						}

                        let mStr = dateArr['m'];
                        if(mStr.length === 1){
							mStr = "0" + mStr;
						}
                        let dStr = dateArr['d'];
                        if(dStr.length === 1){
							dStr = "0" + dStr;
						}
						dateInput.value = dateArr['y'] + "-" + mStr + "-" + dStr;
					}
				}
				dateInput.form.submit();
			}

			function parseDate(dateStr){
                const dateObj = new Date(dateStr);
                let dateTokens;
                let y = 0;
                let m = 0;
                let d = 0;
                let mText;
                try {
                    const mNames = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
                    const validformat1 = /^\d{4}-\d{1,2}-\d{1,2}$/;
                    const validformat2 = /^\d{1,2}\/\d{1,2}\/\d{2,4}$/;
                    const validformat3 = /^\d{1,2} \D+ \d{2,4}$/;
                    const validformat4 = /^\D+ \d{2,4}$/;
                    const validformat5 = /^\d{2,4}$/;
                    if (validformat1.test(dateStr)) {
                        dateTokens = dateStr.split("-");
                        y = dateTokens[0];
                        m = dateTokens[1];
                        d = dateTokens[2];
                    } else if (validformat2.test(dateStr)) {
                        dateTokens = dateStr.split("/");
                        m = dateTokens[0];
                        d = dateTokens[1];
                        y = dateTokens[2];
                        if (y.length === 2) {
                            if (y < 20) {
                                y = "20" + y;
                            } else {
                                y = "19" + y;
                            }
                        }
                    } else if (validformat3.test(dateStr)) {
                        dateTokens = dateStr.split(" ");
                        d = dateTokens[0];
                        mText = dateTokens[1];
                        y = dateTokens[2];
                        if (y.length === 2) {
                            if (y < 15) {
                                y = "20" + y;
                            } else {
                                y = "19" + y;
                            }
                        }
                        mText = mText.substring(0, 3);
                        mText = mText.toLowerCase();
                        m = mNames.indexOf(mText) + 1;
                    } else if (validformat4.test(dateStr)) {
                        dateTokens = dateStr.split(" ");
                        y = dateTokens[1];
                        mText = dateTokens[0];
                        mText = mText.substring(0, 3);
                        mText = mText.toLowerCase();
                        m = mNames.indexOf(mText) + 1;
                        d = "00";
                    } else if (validformat5.test(dateStr)) {
                        y = dateStr;
                        m = "00";
                        d = "00";
                    } else if (dateObj instanceof Date) {
                        y = dateObj.getFullYear();
                        m = dateObj.getMonth() + 1;
                        d = dateObj.getDate();
                    }
                } catch (ex) {}
                const retArr = [];
                retArr["y"] = y.toString();
				retArr["m"] = m.toString();
				retArr["d"] = d.toString();
				return retArr;
			}
		</script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../../header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt; 
			<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
			<b>Occurrence Comment Listing</b>
		</div>
		<?php 
		if($statusStr){
			echo '<div style="margin:20px;color:red;">';
			echo $statusStr;
			echo '</div>';
		}
		?>
		<!-- This is inner text! -->
		<div id="innertext">
			<?php
			if($collid){
				$pageBar = '';
				if($commentArr){
					$recCnt = 0;
					if(isset($commentArr['cnt'])){
						$recCnt = $commentArr['cnt'];
						unset($commentArr['cnt']);
					}
					$urlVars = 'collid='.$collid.'&limit='.$limit.'&tsstart='.$tsStart.'&tsend='.$tsEnd.'&uid='.$uid.'&rs='.$rs;
					$currentPage = ($start/$limit)+1;
					$lastPage = ceil($recCnt / $limit);
					$startPage = $currentPage > 4?$currentPage - 4:1;
					$endPage = ($lastPage > $startPage + 9?$startPage + 9:$lastPage);
					$hrefPrefix = 'commentlist.php?'.$urlVars. '&start=';
					$pageBar .= "<span style='margin:5px;'>\n";
					if($endPage > 1){
					    $pageBar .= "<span style='margin-right:5px;'><a href='".$hrefPrefix."0'>First Page</a> &lt;&lt;</span>";
						for($x = $startPage; $x <= $endPage; $x++){
						    if($currentPage !== $x){
						        $pageBar .= "<span style='margin-right:3px;'><a href='" .$hrefPrefix.(($x-1)*$limit)."'>".$x. '</a></span>';
						    }
						    else{
						        $pageBar .= "<span style='margin-right:3px;font-weight:bold;'>" .$x. '</span>';
						    }
						}
					}
					if($lastPage > $endPage){
					    $pageBar .= "<span style='margin-left:5px;'>&gt;&gt; <a href='".$hrefPrefix.(($lastPage-1)*$limit)."'>Last Page</a></span>";
					}
					$pageBar .= '</span>';
					$endNum = $start + $limit;
					if($endNum > $recCnt) {
                        $endNum = $recCnt;
                    }
					$cntBar = ($start+1). '-' .$endNum. ' of ' .$recCnt.' comments';
					echo "<div><hr/></div>\n";
					echo '<div style="float:right;"><b>'.$pageBar.'</b></div>';
					echo '<div><b>'.$cntBar.'</b></div>';
					echo "<div style='clear:both;'><hr/></div>";
				}
				?>
				<fieldset style="float:right;width:250px;margin:10px;">
					<legend><b>Options</b></legend>
					<form name="optionform" action="commentlist.php" method="post">
						<div>
							<select name="uid" onchange="this.form.submit()">
								<option value="0">All Users</option> 
								<option value="0">------------------------</option> 
								<?php 
									$userArr = $commentManager->getCommentUsers($collid);
									foreach($userArr as $userid => $userStr){
										echo '<option value="'.$userid.'" '.($uid === $userid?'selected':'').'>'.$userStr.'</option>';
									}
								?>
							</select>
						</div>
						<div>
							Beginning Date: 
							<input name="tsstart" type="text" value="<?php echo $tsStart; ?>" style="width:100px;" onchange="dateChanged(this)" /><br/>
							End Date:  
							<input name="tsend" type="text" value="<?php echo $tsEnd; ?>" style="width:100px;" onchange="dateChanged(this)" />
						</div>
						<div>
							<input name="rs" type="radio" value="1" <?php echo ($rs === 1?'checked':''); ?> onchange="this.form.submit()" /> Public <br/>
							<input name="rs" type="radio" value="2" <?php echo ($rs === 2?'checked':''); ?> onchange="this.form.submit()" /> Non-public <br/>
							<input name="rs" type="radio" value="3" <?php echo ($rs === 3?'checked':''); ?> onchange="this.form.submit()" /> Reviewed <br/>
							<input name="rs" type="radio" value="0" <?php echo (!$rs?'checked':''); ?> onchange="this.form.submit()" /> All
						</div>
						<div>
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						</div>
					</form>
				</fieldset>
				<?php 
				if($commentArr){
					foreach($commentArr as $comid => $cArr){
						echo '<div style="margin:15px;">';
						echo '<div style="margin-bottom:10px;"><a href="../individual/index.php?occid='.$cArr['occid'].'" target="_blank">'.$cArr['occurstr'].'</a></div>';
						echo '<div>';
						echo '<b>'.$userArr[$cArr['uid']].'</b> <span style="color:gray;">posted on '.$cArr['ts'].'</span>';
						if($cArr['rs'] === 2 || $cArr['rs'] === '0'){
							echo '<span style="margin-left:20px;"><b>Status:</b> </span><span style="color:red;" title="viewable by administrators only)">Not Public</span>';
						}
						elseif($cArr['rs'] === 3){
							echo '<span style="margin-left:20px;"><b>Status:</b> </span><span style="color:orange;">Reviewed</span>';
						}
						echo '</div>';
						echo '<div style="margin:10px;">'.$cArr['str'].'</div>';
						?>
						<div style="margin:20px;">
							<form name="commentactionform" action="commentlist.php" method="post">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="start" type="hidden" value="<?php echo $start; ?>" />
								<input name="limit" type="hidden" value="<?php echo $limit; ?>" />
								<input name="tsstart" type="hidden" value="<?php echo $tsStart; ?>" />
								<input name="tsend" type="hidden" value="<?php echo $tsEnd; ?>" />
								<input name="uid" type="hidden" value="<?php echo $uid; ?>" />
								<input name="rs" type="hidden" value="<?php echo $rs; ?>" />
								<?php 
								if($cArr['rs'] === 2){
									echo '<input name="formsubmit" type="submit" value="Make comment public" />';
								}
								else{
									echo '<input name="formsubmit" type="submit" value="Hide Comment from Public" />';
								}
								?>
								<span style="margin-left:20px;">
									<input name="formsubmit" type="submit" value="Mark as reviewed" />
								</span>
								<span style="margin-left:20px;">
									<input name="formsubmit" type="submit" value="Delete Comment"  onclick="return confirm('Are you sure you want to delete this comment?')" />
								</span>
								<input name="comid" type="hidden" value="<?php echo $comid; ?>" />
							</form>
						</div>
						<?php 
						echo '</div>';
						echo '<hr style="color:gray;"/>';
					}
					echo '<div style="float:right;">'.$pageBar.'</div>';
					echo "<div style='clear:both;'><hr/></div></div>";
				}
				else{
					echo '<div style="font-weight:bold;font-size:120%;margin:20px;">No comments have been submitted</div>';
				}
			}
			else{
				echo '<div>ERROR: collid is null</div>';
			}
			?>
		</div>
		<?php
			include(__DIR__ . '/../../footer.php');
		?>
	</body>
</html>
