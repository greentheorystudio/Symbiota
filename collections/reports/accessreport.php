<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceAccessStats.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$display = array_key_exists('display',$_REQUEST)?$_REQUEST['display']:'summary';
$duration = array_key_exists('duration',$_REQUEST)?$_REQUEST['duration']:'day';
$startDate = array_key_exists('startdate',$_REQUEST)?$_REQUEST['startdate']:'';
$endDate = array_key_exists('enddate',$_REQUEST)?$_REQUEST['enddate']:'';
$ip = array_key_exists('ip',$_REQUEST)?$_REQUEST['ip']:'';
$accessType = array_key_exists('accesstype',$_REQUEST)?$_REQUEST['accesstype']:'';
$occid = array_key_exists('occid',$_REQUEST)?(int)$_REQUEST['occid']:0;
$pageNum = array_key_exists('pagenum',$_REQUEST)?(int)$_REQUEST['pagenum']:0;
$limitCnt = array_key_exists('limitcnt',$_REQUEST)?(int)$_REQUEST['limitcnt']:1000;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$statManager = new OccurrenceAccessStats();
$collName = 'All Collections';
if($collid) {
    $collName = $statManager->setCollid($collid);
}

$statManager->setDuration($duration);
$statManager->setStartDate($startDate);
$statManager->setEndDate($endDate);
$statManager->setIpAddress($ip);
$statManager->setAccessType($accessType);
$statManager->setOccidStr($occid);
$statManager->setPageNum($pageNum);
$statManager->setLimit($limitCnt);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || ($collid && array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
 	$isEditor = true;
}

if($action === 'export'){
	$statManager->exportCsvFile($display);
	exit;
}

$statArr = array();
$recCnt = 0;
$headerStr = '';
if($display === 'full'){
	$statArr = $statManager->getFullReport();
	$recCnt = $statManager->getFullReportCount();
	$headerStr = '<th>Date</th><th>Access Type</th><th>Record #</th><th>Record Count</th>';
}
else{
	$statArr = $statManager->getSummaryReport();
	$recCnt = $statManager->getSummaryReportCount();
	$periodArr = array('day'=>'Date','week'=>'Year-Week','month'=>'Year-Month','year'=>'Year');
	$headerStr = '<th>'.$periodArr[$duration].'</th><th>Access Type</th><th>Record Count</th>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Access Statistics</title>
    <meta name="description" content="Access statistics for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script>
        function validateFilterForm(f){
            if(f.startdate.value !== "" && f.enddate.value !== "" && f.startdate.value > f.enddate.value){
                alert("Start date cannot be after end date");
                return false;
            }
            return true
        }

        function printFriendlyMode(status){
            if(status){
                $("#breadcrumbs").hide();
                $(".header").hide();
                $(".navbarDiv").hide();
                $(".returnDiv").show();
                $("#filterDiv").hide();
                $(".footer").hide();
            }
            else{
                $("#breadcrumbs").show();
                $(".header").show();
                $(".navbarDiv").show();
                $(".returnDiv").hide();
                $("#filterDiv").show();
                $(".footer").show();
            }
        }

        function openIndPU(occid){
            const newWindow = window.open('../individual/index.php?occid=' + occid, 'indspec' + occid, 'scrollbars=1,toolbar=0,resizable=1,width=1000,height=700,left=20,top=20');
            if (newWindow.opener == null) {
                newWindow.opener = self;
            }
        }
    </script>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    echo '<div id="breadcrumbs">';
    echo '<a href="../../index.php">Home</a> &gt;&gt; ';
    echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
    echo '<b>View Access Statistics</b>';
    echo '</div>';
    ?>
    <div id="mainContainer" style="padding: 10px 15px 15px;min-width:1100px;">
        <div>
            <div style="float:left;"><b><u>User Access Statistics</u></b></div>
            <div id="desc_details" style="clear:both;display:none;width:500px;">Displays general user access statistics for all occurrences within collection.
                Download = any occurrence download excluding data backups and custom downloads made by collection administrators (e.g. via Data Management Menu),
                Full View = viewing full record via occurrence details page,
                List View = viewing basic field data through a list view (e.g. default occurrence listing tab within the general search interface),
                Map View = occurrence represented as a dot within any of the map-based search interfaces
            </div>
            <div id="desc_info" style="float:left;margin-left:5px;"><a href="#" onclick="toggle('desc_details');toggle('desc_info');"><i style="height:15px;width:15px;color:green;" class="fas fa-info-circle"></i></a></div>
        </div>
        <?php
        if($isEditor){
            $subsetCnt = $limitCnt*($pageNum + 1);
            if($subsetCnt > $recCnt) {
                $subsetCnt = $recCnt;
            }
            $navPageBase = 'accessreport.php?collid='.$collid.'&display='.$display.'&duration='.$duration.'&startdate='.$startDate.'&enddate='.$endDate.'&ip='.$ip.'&accesstype='.$accessType;
            $navStr = '<div class="navbarDiv" style="float:right;">';
            if($pageNum){
                $navStr .= '<a href="'.$navPageBase.'&pagenum='.($pageNum-1).'&limitcnt='.$limitCnt.'" title="Previous '.$limitCnt.' records">&lt;&lt;</a>';
            }
            else{
                $navStr .= '&lt;&lt;';
            }
            $navStr .= ' | ';
            $navStr .= ($pageNum*$limitCnt).'-'.$subsetCnt.' of '.$recCnt.' records';
            $navStr .= ' | ';
            if($subsetCnt < $recCnt){
                $navStr .= '<a href="'.$navPageBase.'&pagenum='.($pageNum+1).'&limitcnt='.$limitCnt.'" title="Next '.$limitCnt.' records">&gt;&gt;</a>';
            }
            else{
                $navStr .= '&gt;&gt;';
            }
            $navStr .= '</div>';
            $retToMenuStr = '<div class="returnDiv" style="clear:both;display:none"><b><a href="#" onclick="printFriendlyMode(false)">Exit Print Mode</a></b></div>';
            echo $retToMenuStr;
            $accessTypeArr = array('download'=>'Download','view'=>'Full View','list'=>'List View','map'=>'Map View','downloadJSON'=>'API JSON Download');
            ?>
            <div id="filterDiv" style="clear:both;padding-top:5px;">
                <form name="filter" action="accessreport.php" method="post" onsubmit="return validateFilterForm(this)">
                    <fieldset style="width:375px;text-align:left;">
                        <legend><b>Filter</b></legend>
                        <div style="margin:3px;">
                            Display:
                            <select name="display">
                                <option value="summary">Summary Count</option>
                                <option value="full" <?php echo ($display === 'full'?'SELECTED':''); ?>>Full Records</option>
                            </select>
                        </div>
                        <div style="margin:3px;">
                            Duration:
                            <select name="duration">
                                <option value="day">Daily</option>
                                <option value="week" <?php echo ($duration === 'week'?'SELECTED':''); ?>>Weekly</option>
                                <option value="month" <?php echo ($duration === 'month'?'SELECTED':''); ?>>Monthly</option>
                                <option value="year" <?php echo ($duration === 'year'?'SELECTED':''); ?>>Yearly</option>
                            </select>
                        </div>
                        <div style="margin:3px;">
                            Access Type:
                            <select name="accesstype">
                                <option value="">All Access Types</option>
                                <option value="">---------------------</option>
                                <?php
                                foreach($accessTypeArr as $k => $v){
                                    echo '<option value="'.$k.'" '.($accessType === $k?'SELECTED':'').'>'.$v.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin:3px;">
                            Date:
                            <input name="startdate" type="date" value="<?php echo $startDate; ?>" /> to
                            <input name="enddate" type="date" value="<?php echo $endDate; ?>" />
                        </div>
                        <div style="margin:10px;">
                            <button name="submitbutton" type="submit" value="submitfilter">Submit Filter</button>
                            <input name="collid" type="hidden" value="<?php echo $collid; ?>" />
                        </div>
                    </fieldset>
                </form>
            </div>
            <div style="font-weight:bold;">
                <?php echo $collName; ?>
                <a href="<?php echo $navPageBase.'&action=export'; ?>" title="Download Results"><i style="height:15px;width:15px;" class="fas fa-download"></i></a>
            </div>
            <div style="width:400px">
                <div style="clear:both"><?php echo $navStr; ?></div>
                <table class="styledtable">
                    <tr>
                        <?php
                        echo $headerStr;
                        ?>
                    </tr>
                    <?php
                    if($statArr){
                        if($display === 'full'){
                            foreach($statArr as $date => $arr1){
                                foreach($arr1 as $aType => $arr2){
                                    foreach($arr2 as $recid => $cnt){
                                        echo '<tr><td>'.$date.'</td><td>'.($accessTypeArr[$aType] ?? '').'</td><td><a href="#" onclick="openIndPU('.$recid. ');return false;">' .$recid.'</a></td><td>'.$cnt.'</td></tr>';
                                    }
                                }
                            }
                        }
                        else{
                            foreach($statArr as $date => $arr1){
                                foreach($arr1 as $aType => $cnt){
                                    echo '<tr><td>'.$date.'</td><td>'.($accessTypeArr[$aType] ?? '').'</td><td>'.$cnt.'</td></tr>';
                                }
                            }
                        }
                    }
                    else{
                        ?>
                        <tr>
                            <td colspan="10">
                                <div style="font-weight:bold;margin:20px;">There are no access statistic matching search criteria.</div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <div style="clear:both"><?php echo $navStr; ?></div>
            </div>
            <?php
            echo $retToMenuStr;
        }
        else{
            echo '<div>Error!</div>';
        }
        ?>
    </div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
    ?>
</body>
</html>
