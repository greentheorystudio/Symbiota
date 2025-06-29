<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 1200);

$catId = array_key_exists('catid',$_REQUEST)?(int)$_REQUEST['catid']:0;
if(!$catId && isset($GLOBALS['DEFAULTCATID']) && $GLOBALS['DEFAULTCATID']) {
    $catId = (int)$GLOBALS['DEFAULTCATID'];
}
$collId = array_key_exists('collid',$_REQUEST)?htmlspecialchars($_REQUEST['collid']):'';
$totalCnt = array_key_exists('totalcnt',$_REQUEST)?(int)$_REQUEST['totalcnt']:0;

$collManager = new OccurrenceCollectionProfile();
$orderArr = array();
$collIdArr = array();

if(is_numeric($collId)){
    $collIdArr[] = (int)$collId;
}
elseif(strpos($collId, ',') !== false){
    $collIdArr = explode(',',$collId);
}

if($collIdArr){
	$orderArr = $collManager->getOrderStatsDataArr(implode(',',$collIdArr));
	ksort($orderArr, SORT_STRING | SORT_FLAG_CASE);
}
else{
    $collId = '';
}
$_SESSION['statsOrderArr'] = $orderArr;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Order Distribution</title>
    <meta name="description" content="Order distribution for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/search.term.manager.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    ?>
    <div id="mainContainer" style="padding: 10px 15px 15px;">
        <fieldset id="orderdistbox" style="clear:both;margin-top:15px;width:800px;">
            <legend><b>Order Distribution</b></legend>
            <table class="styledtable" style="font-family:Arial,serif;width:780px;">
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
                        $starrStr = '{"db":"'.$collId.'","usethes":true,"taxa":"'.$name.'"}';
                        echo "<a href='../list.php?starr=".$starrStr."' target='_blank'>";
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
                <b>Total Occurrences with Order:</b> <?php echo number_format($total); ?><br />
                Specimens without Order: <?php echo number_format($totalCnt-$total); ?><br />
            </div>
            <div style='float:left;margin-left:25px;margin-top:10px;width:16px;height:16px;padding:2px;' title="Save CSV">
                <form name="orderstatscsv" id="orderstatscsv" action="collstatscsv.php" method="post" onsubmit="">
                    <input type="hidden" name="action" value='Download Order Dist' />
                    <button class="icon-button" type="submit">
                        <i style="height:15px;width:15px;" class="fas fa-download"></i>
                    </button>
                </form>
            </div>
        </fieldset>
    </div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
    ?>
</body>
</html>
