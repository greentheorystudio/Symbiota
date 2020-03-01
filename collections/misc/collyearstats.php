<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
header('Content-Type: text/html; charset=' .$CHARSET);
ini_set('max_execution_time', 1200);

$catId = array_key_exists('catid',$_REQUEST)?$_REQUEST['catid']:0;
if(!$catId && isset($DEFAULTCATID) && $DEFAULTCATID) {
    $catId = $DEFAULTCATID;
}
$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$years = array_key_exists('years',$_REQUEST)?$_REQUEST['years']:1;

$days = 365 * $years;
$months = 12 * $years;

$collManager = new OccurrenceCollectionProfile();

if($collId){
	$dateArr = $collManager->getYearStatsHeaderArr($months);
	$statArr = $collManager->getYearStatsDataArr($collId,$days);
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<meta name="keywords" content="Natural history collections yearly statistics" />
		<title><?php echo $DEFAULT_TITLE; ?> Year Statistics</title>
		<link rel="stylesheet" href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" />
		<link rel="stylesheet" href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" />
		<link href="../../css/jquery-ui.css" type="text/css" rel="Stylesheet" />
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquery-ui.js"></script>
		<script type="text/javascript" src="../../js/symb/collections.index.js"></script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../../header.php');
		?>
		<div id="innertext">
			<fieldset id="yearstatsbox" style="clear:both;margin-top:15px;width:97%;">
				<legend><b>Month Totals</b></legend>
				<table class="styledtable" style="font-family:Arial,serif;font-size:12px;width:98%;">
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
