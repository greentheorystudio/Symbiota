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
$totalCnt = array_key_exists('totalcnt',$_REQUEST)?$_REQUEST['totalcnt']:0;

$collManager = new OccurrenceCollectionProfile();
$orderArr = array();

if($collId){
	$orderArr = $collManager->getOrderStatsDataArr($collId);
	ksort($orderArr, SORT_STRING | SORT_FLAG_CASE);
}
$_SESSION['statsOrderArr'] = $orderArr;
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<meta name="keywords" content="Natural history collections yearly statistics" />
		<title><?php echo $DEFAULT_TITLE; ?> Order Distribution</title>
		<link rel="stylesheet" href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" />
		<link rel="stylesheet" href="../../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" />
		<link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquery-ui.js"></script>
		<script type="text/javascript" src="../../js/symb/collections.index.js"></script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../../header.php');
		?>
		<div id="innertext">
			<fieldset id="orderdistbox" style="clear:both;margin-top:15px;width:800px;">
				<legend><b>Order Distribution</b></legend>
				<table class="styledtable" style="font-family:Arial,serif;font-size:12px;width:780px;">
					<tr>
						<th style="text-align:center;">Order</th>
						<th style="text-align:center;">Specimens</th>
						<th style="text-align:center;">Georeferenced</th>
						<th style="text-align:center;">Species ID</th>
						<th style="text-align:center;">Georeferenced<br />and<br />Species ID</th>
					</tr>
					<?php
					$total = 0;
					foreach($orderArr as $name => $data){
						echo '<tr>';
						echo '<td>'.wordwrap($name,52,"<br />\n",true).'</td>';
						echo '<td>';
						if($data['SpecimensPerOrder'] === 1){
							echo '<a href="../list.php?db[]='.$collId.'&reset=1&taxa='.$name.'" target="_blank">';
						}
						echo number_format($data['SpecimensPerOrder']);
						if($data['SpecimensPerOrder'] === 1){
							echo '</a>';
						}
						echo '</td>';
						echo '<td>'.($data['GeorefSpecimensPerOrder']?round(100*($data['GeorefSpecimensPerOrder']/$data['SpecimensPerOrder'])):0).'%</td>';
						echo '<td>'.($data['IDSpecimensPerOrder']?round(100*($data['IDSpecimensPerOrder']/$data['SpecimensPerOrder'])):0).'%</td>';
						echo '<td>'.($data['IDGeorefSpecimensPerOrder']?round(100*($data['IDGeorefSpecimensPerOrder']/$data['SpecimensPerOrder'])):0).'%</td>';
						echo '</tr>';
						$total += $data['SpecimensPerOrder'];
					}
					?>
				</table>
				<div style="margin-top:10px;float:left;">
					<b>Total Specimens with Order:</b> <?php echo number_format($total); ?><br />
					Specimens without Order: <?php echo number_format($totalCnt-$total); ?><br />
				</div>
				<div style='float:left;margin-left:25px;margin-top:10px;width:16px;height:16px;padding:2px;' title="Save CSV">
					<form name="orderstatscsv" id="orderstatscsv" action="collstatscsv.php" method="post" onsubmit="">
						<input type="hidden" name="action" value='Download Order Dist' />
						<input type="image" name="action" src="../../images/dl.png" onclick="" />
					</form>
				</div>
			</fieldset>
		</div>
		<?php
			include(__DIR__ . '/../../footer.php');
		?>
	</body>
</html>
