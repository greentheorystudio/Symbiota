<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 1200);

$catId = array_key_exists('catid',$_REQUEST)?(int)$_REQUEST['catid']:0;
if(!$catId && isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID']) {
    $catId = (int)$GLOBALS['DEFAULTCATID'];
}
$collId = array_key_exists('collid',$_REQUEST)?htmlspecialchars($_REQUEST['collid']):'';
$years = array_key_exists('years',$_REQUEST)?(int)$_REQUEST['years']:1;

$days = 365 * $years;
$months = 12 * $years;

$collManager = new OccurrenceCollectionProfile();

$dateArr = array();
$statArr = array();
$collIdArr = array();

if(is_numeric($collId)){
    $collIdArr[] = (int)$collId;
}
elseif(strpos($collId, ',') !== false){
    $collIdArr = explode(',',$collId);
}

if($collIdArr){
	$dateArr = $collManager->getYearStatsHeaderArr($months);
	$statArr = $collManager->getYearStatsDataArr(implode(',',$collIdArr),$days);
}
else{
    $collId = '';
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<meta name="keywords" content="Natural history collections yearly statistics" />
		<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Year Statistics</title>
		<link rel="stylesheet" href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" />
		<link rel="stylesheet" href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" />
		<link href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../../js/external/jquery.js"></script>
		<script type="text/javascript" src="../../js/external/jquery-ui.js"></script>
		<script type="text/javascript" src="../../js/search.term.manager.js?ver=20221110"></script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../../header.php');
		?>
		<div id="innertext">
			<fieldset id="yearstatsbox" style="clear:both;margin-top:15px;width:97%;">
				<legend><b>Month Totals</b></legend>
				<table class="styledtable" style="font-family:Arial,serif;width:98%;">
					<tr>
						<th style="text-align:center;">Institution</th>
						<th style="text-align:center;">Object</th>
						<?php
						foreach($dateArr as $i => $month){
							echo '<th style="text-align:center;">'.$month.'</th>';
						}
						?>
						<th style="text-align:center;">Total</th>
					</tr>
					<?php
					$recCnt = 0;
					foreach($statArr as $code => $data){
						echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
						echo '<td>'.wordwrap($data['collectionname'],32,"<br />\n",true).'</td>';
						echo '<td>Specimens</td>';
						$total = 0;
						foreach($dateArr as $i => $month){
							if(array_key_exists($month, $data['stats']) && array_key_exists('speccnt', $data['stats'][$month])) {
                                $total += $data['stats'][$month]['speccnt'];
                                echo '<td>'.$data['stats'][$month]['speccnt'].'</td>';
                            }
							else{
								echo '<td>0</td>';
							}
						}
						echo '<td>'.$total.'</td>';
						echo '</tr>';
						echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
						echo '<td></td>';
						echo '<td>Unprocessed</td>';
						$total = 0;
						foreach($dateArr as $i => $month){
							if(array_key_exists($month, $data['stats']) && array_key_exists('unprocessedCount', $data['stats'][$month])) {
                                $total += $data['stats'][$month]['unprocessedCount'];
                                echo '<td>'.$data['stats'][$month]['unprocessedCount'].'</td>';
                            }
							else{
								echo '<td>0</td>';
							}
						}
						echo '<td>'.$total.'</td>';
						echo '</tr>';
						echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
						echo '<td></td>';
						echo '<td>Stage 1</td>';
						$total = 0;
						foreach($dateArr as $i => $month){
							if(array_key_exists($month, $data['stats']) && array_key_exists('stage1Count', $data['stats'][$month])) {
                                $total += $data['stats'][$month]['stage1Count'];
                                echo '<td>'.$data['stats'][$month]['stage1Count'].'</td>';
                            }
							else{
								echo '<td>0</td>';
							}
						}
						echo '<td>'.$total.'</td>';
						echo '</tr>';
						echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
						echo '<td></td>';
						echo '<td>Stage 2</td>';
						$total = 0;
						foreach($dateArr as $i => $month){
							if(array_key_exists($month, $data['stats']) && array_key_exists('stage2Count', $data['stats'][$month])) {
                                $total += $data['stats'][$month]['stage2Count'];
                                echo '<td>'.$data['stats'][$month]['stage2Count'].'</td>';
                            }
							else{
								echo '<td>0</td>';
							}
						}
						echo '<td>'.$total.'</td>';
						echo '</tr>';
						echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
						echo '<td></td>';
						echo '<td>Stage 3</td>';
						$total = 0;
						foreach($dateArr as $i => $month){
							if(array_key_exists($month, $data['stats']) && array_key_exists('stage3Count', $data['stats'][$month])) {
                                $total += $data['stats'][$month]['stage3Count'];
                                echo '<td>'.$data['stats'][$month]['stage3Count'].'</td>';
                            }
							else{
								echo '<td>0</td>';
							}
						}
						echo '<td>'.$total.'</td>';
						echo '</tr>';
						echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
						echo '<td></td>';
						echo '<td>Images</td>';
						$total = 0;
						foreach($dateArr as $i => $month){
							if(array_key_exists($month, $data['stats']) && array_key_exists('imgcnt', $data['stats'][$month])) {
                                $total += $data['stats'][$month]['imgcnt'];
                                echo '<td>'.$data['stats'][$month]['imgcnt'].'</td>';
                            }
							else{
								echo '<td>0</td>';
							}
						}
						echo '<td>'.$total.'</td>';
						echo '</tr>';
						echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
						echo '<td></td>';
						echo '<td>Georeferenced</td>';
						$total = 0;
						foreach($dateArr as $i => $month){
							if(array_key_exists($month, $data['stats']) && array_key_exists('georcnt', $data['stats'][$month])) {
                                $total += $data['stats'][$month]['georcnt'];
                                echo '<td>'.$data['stats'][$month]['georcnt'].'</td>';
                            }
							else{
								echo '<td>0</td>';
							}
						}
						echo '<td>'.$total.'</td>';
						echo '</tr>';
						$recCnt++;
					}
					?>
				</table>
				<div style='float:right;margin:15px;' title="Save CSV">
					<form name="yearstatscsv" id="yearstatscsv" style="margin-bottom:0;" action="collstatscsv.php" method="post" onsubmit="">
						<input type="hidden" name="collids" id="collids" value='<?php echo $collId; ?>' />
						<input type="hidden" name="days" value="<?php echo $days; ?>" />
						<input type="hidden" name="months" value="<?php echo $months; ?>" />
                        <input type="hidden" name="years" value="<?php echo $years; ?>" />
						<input type="submit" name="action" value="Download CSV" />
					</form>
				</div>
			</fieldset>
		</div>
		<?php
			include(__DIR__ . '/../../footer.php');
		?>
	</body>
</html>
