<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/TaxonomyDynamicListManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$index = array_key_exists('index',$_REQUEST)?(int)$_REQUEST['index']:0;
$descLimit = array_key_exists('desclimit',$_REQUEST);
$orderInput = array_key_exists('orderinput',$_REQUEST)?$_REQUEST['orderinput']:'';
$familyInput = array_key_exists('familyinput',$_REQUEST)?$_REQUEST['familyinput']:'';
$scinameInput = array_key_exists('scinameinput',$_REQUEST)?$_REQUEST['scinameinput']:'';
$commonInput = array_key_exists('commoninput',$_REQUEST)?$_REQUEST['commoninput']:'';
$sortSelect = array_key_exists('sortSelect',$_REQUEST)?$_REQUEST['sortSelect']:'kingdom';
$targetTaxon = array_key_exists('targetname',$_REQUEST)?$_REQUEST['targetname']:'';
$targetTid = array_key_exists('targettid',$_REQUEST)?(int)$_REQUEST['targettid']:0;
$statusStr = array_key_exists('statusstr',$_REQUEST)?$_REQUEST['statusstr']:'';

$listManager = new TaxonomyDynamicListManager();
$kingdomArr = array();
$phylumArr = array();
$classArr = array();
$tableArr = array();
$vernacularArr = array();
$qryCnt = 0;
$urlVars = '';

if(!$targetTid && $targetTaxon){
    $targetTid = $listManager->setTidFromSciname($targetTaxon);
    if($targetTid){
        $scinameInput = $targetTaxon;
    }
}

$higherRankArr = $listManager->getHigherRankArr();
if($higherRankArr){
    $kingdomArr = $higherRankArr[10];
    $phylumArr = $higherRankArr[30];
    $classArr = $higherRankArr[60];
}

if($targetTid){
    if(!$targetTaxon){
        $listManager->setTid($targetTid);
    }
    $listManager->setDescLimit($descLimit);
    $listManager->setSortField($sortSelect);
    $listManager->setPageIndex($index);
    $listManager->setTaxaCnt();
    $tableArr = $listManager->getTableArr();
    $vernacularArr = $listManager->getVernacularArr();
    $qryCnt = (int)$listManager->getTaxaCnt();
    $urlVars = ($descLimit?'desclimit=1':'').'&orderinput='.$orderInput.'&familyinput='.$familyInput.'&scinameinput='.$scinameInput.'&commoninput='.$commonInput.'&sortSelect='.$sortSelect.'&targettid='.$targetTid;
}
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
	<title><?php echo $DEFAULT_TITLE . ($targetTid?' Dynamic Species List: ' . $listManager->getSciName():''); ?></title>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link type="text/css" href="../css/jquery-ui.css" rel="Stylesheet" />
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#orderinput").autocomplete({
				source: function( request, response ) {
					$.getJSON( "../webservices/autofillsciname.php", {
					    term: request.term,
                        limit: 10,
                        rlimit: 100,
                        hideauth: true,
                        taid: 1
                    }, response );
				},
                select: function( event, ui ) {
                    processSelection('orderinput',ui.item.id);
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        document.getElementById('orderinput').value = '';
                    }
                }
			},{ minLength: 3 });

            $("#familyinput").autocomplete({
                source: function( request, response ) {
                    $.getJSON( "../webservices/autofillsciname.php", {
                        term: request.term,
                        limit: 10,
                        rlimit: 140,
                        hideauth: true,
                        taid: 1
                    }, response );
                },
                select: function( event, ui ) {
                    processSelection('familyinput',ui.item.id);
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        document.getElementById('familyinput').value = '';
                    }
                }
            },{ minLength: 3 });

            $("#scinameinput").autocomplete({
                source: function( request, response ) {
                    $.getJSON( "../webservices/autofillsciname.php", {
                        term: request.term,
                        limit: 10,
                        hideauth: true,
                        taid: 1
                    }, response );
                },
                select: function( event, ui ) {
                    processSelection('scinameinput',ui.item.id);
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        document.getElementById('scinameinput').value = '';
                    }
                }
            },{ minLength: 3 });

            $("#commoninput").autocomplete({
                source: function( request, response ) {
                    $.getJSON( "../webservices/autofillvernacular.php", {
                        term: request.term,
                        limit: 10
                    }, response );
                },
                select: function( event, ui ) {
                    processSelection('commoninput',ui.item.id);
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        document.getElementById('commoninput').value = '';
                    }
                }
            },{ minLength: 3 });
        });

        function processSelection(id,value){
            document.getElementById('targettid').value = value;
            if(id !== 'kingdomSelect'){
                document.getElementById('kingdomSelect').value = '';
            }
            if(id !== 'phylumSelect'){
                document.getElementById('phylumSelect').value = '';
            }
            if(id !== 'classSelect'){
                document.getElementById('classSelect').value = '';
            }
            if(id !== 'orderinput'){
                document.getElementById('orderinput').value = '';
            }
            if(id !== 'familyinput'){
                document.getElementById('familyinput').value = '';
            }
            if(id !== 'scinameinput'){
                document.getElementById('scinameinput').value = '';
            }
            if(id !== 'commoninput'){
                document.getElementById('commoninput').value = '';
            }
        }

        function verifySubmit(){
            if(!document.getElementById('targettid').value || document.getElementById('targettid').value === ''){
                alert('Please enter or select a taxon.');
                return false;
            }
            return true;
        }
	</script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div class="navpath">
    <a href="../index.php">Home</a> &gt;&gt;
    <a href="dynamictaxalist.php"><b>Dynamic Species List</b></a>
</div>
<div id="innertext">
    <?php
    if($statusStr){
        ?>
        <div style="color:red;margin:15px;">
            <?php echo $statusStr; ?>
        </div>
        <?php
    }
    ?>
    <div>
        <form id="tdform" name="tdform" action="dynamictaxalist.php" method='POST' onsubmit="return verifySubmit();">
            <fieldset style="width:90%;padding:8px;margin: 0 auto;">
                <legend><b>Set Criteria</b></legend>
                <div style="width:100%;display:flex;justify-content:space-between;">
                    <div>
                        Kingdom:
                        <select id="kingdomSelect" style="width:200px;" onchange="processSelection('kingdomSelect',this.value);">
                            <option value="">Select Kingdom</option>
                            <option value="">-----------------------</option>
                            <?php
                            foreach ($kingdomArr as $tid => $kArr) {
                                echo '<option value="' . $tid . '" ' . ((int)$tid === $targetTid ? 'SELECTED' : '') . '>' . $kArr['display'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        Phylum:
                        <select id="phylumSelect" style="width:200px;" onchange="processSelection('phylumSelect',this.value);">
                            <option value="">Select Phylum</option>
                            <option value="">-----------------------</option>
                            <?php
                            foreach ($phylumArr as $tid => $pArr) {
                                echo '<option value="' . $tid . '" ' . ((int)$tid === $targetTid ? 'SELECTED' : '') . '>' . $pArr['display'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        Class:
                        <select id="classSelect" style="width:200px;" onchange="processSelection('classSelect',this.value);">
                            <option value="">Select Class</option>
                            <option value="">-----------------------</option>
                            <?php
                            foreach ($classArr as $tid => $cArr) {
                                echo '<option value="' . $tid . '" ' . ((int)$tid === $targetTid ? 'SELECTED' : '') . '>' . $cArr['display'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="width:100%;margin-top:5px;display:flex;justify-content:space-between;">
                    <div>
                        Order:
                        <input name="orderinput" id="orderinput" type="text" style="width:200px;" value="<?php echo $orderInput; ?>" />
                    </div>
                    <div>
                        Family:
                        <input name="familyinput" id="familyinput" type="text" style="width:200px;" value="<?php echo $familyInput; ?>" />
                    </div>
                </div>
                <div style="width:100%;margin-top:5px;display:flex;justify-content:space-between;">
                    <div>
                        Scientific Name:
                        <input name="scinameinput" id="scinameinput" type="text" style="width:250px;" value="<?php echo $scinameInput; ?>" />
                    </div>
                    <div>
                        Common Name:
                        <input name="commoninput" id="commoninput" type="text" style="width:350px;" value="<?php echo $commonInput; ?>" />
                    </div>
                </div>
                <div style="float:left;margin:10px;">
                    <div style="float:left;">
                        Sort By:
                        <select name="sortSelect">
                            <option value="kingdom" <?php echo ($sortSelect === 'kingdom'?'SELECTED':''); ?>>Kingdom</option>
                            <option value="phylum" <?php echo ($sortSelect === 'phylum'?'SELECTED':''); ?>>Phylum</option>
                            <option value="class" <?php echo ($sortSelect === 'class'?'SELECTED':''); ?>>Class</option>
                            <option value="order" <?php echo ($sortSelect === 'order'?'SELECTED':''); ?>>Order</option>
                            <option value="family" <?php echo ($sortSelect === 'family'?'SELECTED':''); ?>>Family</option>
                            <option value="sciname" <?php echo ($sortSelect === 'sciname'?'SELECTED':''); ?>>Scientific Name</option>
                        </select>
                    </div>
                    <div style="float:left;margin-left:10px;">
                        <input type="checkbox" name="desclimit" value="1" <?php echo (((!$_POST && !$_GET) || $descLimit)?'CHECKED':''); ?> /> Limit to taxa with information
                    </div>
                </div>
                <div style="float:right;margin:10px;">
                    <input name="targettid" id="targettid" type="hidden" value="<?php echo $targetTid; ?>" />
                    <input name="action" type="submit" value="Build Species List"/>
                </div>
            </fieldset>
        </form>
    </div>
    <div>
        <?php
        if($tableArr){
            $urlPrefix = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https://':'http://').$_SERVER['HTTP_HOST'].$CLIENT_ROOT.'/taxa/';
            $navUrl = $urlPrefix . 'dynamictaxalist.php?' . $urlVars . '&index=';
            $navStr = '<div style="clear:both;display:flex;justify-content:center;">';
            $lastPage = ($qryCnt / 100) + 1;
            $startPage = ($index > 4?$index - 4:1);
            $endPage = ($lastPage > $startPage + 9?$startPage + 9:$lastPage);
            if($startPage > 1){
                $navStr .= '<span class="pagination" style="margin-right:5px;"><a href="'.$navUrl.'0">First</a></span>';
                $navStr .= '<span class="pagination" style="margin-right:5px;"><a href="'.$navUrl.(($index - 10) < 1?0:$index - 10).'">&lt;&lt;</a></span>';
            }
            for($x = $startPage; $x <= $endPage; $x++){
                if(($index + 1) !== $x){
                    $navStr .= '<span class="pagination" style="margin-right:3px;"><a href="'.$navUrl.($x-1).'">'.$x. '</a></span>';
                }
                else{
                    $navStr .= '<span class="pagination" style="margin-right:3px;font-weight:bold;">' .$x. '</span>';
                }
            }
            if(($lastPage - $startPage) >= 10){
                $navStr .= '<span class="pagination" style="margin-left:5px;"><a href="'.$navUrl.(($index + 10) > $lastPage?$lastPage:($index + 10)).'">&gt;&gt;</a></span>';
                $navStr .= '<span class="pagination" style="margin-left:5px;"><a href="'.$navUrl.$lastPage.'">Last</a></span>';
            }
            $beginNum = ($index)*100 + 1;
            $endNum = $beginNum + 100 - 1;
            if($endNum > $qryCnt) {
                $endNum = $qryCnt;
            }
            $navStr .= '</div>';
            $navStr .= '<div style="clear:both;display:flex;justify-content:center;">';
            $navStr .= 'Page '.($index + 1).', records '.$beginNum.'-'.$endNum.' of '.$qryCnt;
            $navStr .= '</div>';

            echo '<div style="width:100%;clear:both;margin:5px;">';
            if($qryCnt > 1){
                echo $navStr;
            }
            echo '</div>';
            echo '<div style="clear:both;height:5px;"></div>';
            echo '<table class="styledtable" style="font-family:Arial,serif;font-size:12px;"><tr>';
            echo '<th>Kingdom</th>';
            echo '<th>Phylum</th>';
            echo '<th>Class</th>';
            echo '<th>Order</th>';
            echo '<th>Family</th>';
            echo '<th>Scientific Name</th>';
            echo '<th>Common Name</th>';
            echo '</tr>';
            $recCnt = 0;
            foreach($tableArr as $id => $taxArr){
                echo '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
                echo '<td>'.($taxArr['kingdomTid']?'<a href="'.$urlPrefix.'index.php?taxon='.$taxArr['kingdomTid'].'" target="_blank">':'').$taxArr['kingdomName'].($taxArr['kingdomTid']?'</a>':'').'</td>'."\n";
                echo '<td>'.($taxArr['phylumTid']?'<a href="'.$urlPrefix.'index.php?taxon='.$taxArr['phylumTid'].'" target="_blank">':'').$taxArr['phylumName'].($taxArr['phylumTid']?'</a>':'').'</td>'."\n";
                echo '<td>'.($taxArr['classTid']?'<a href="'.$urlPrefix.'index.php?taxon='.$taxArr['classTid'].'" target="_blank">':'').$taxArr['className'].($taxArr['classTid']?'</a>':'').'</td>'."\n";
                echo '<td>'.($taxArr['orderTid']?'<a href="'.$urlPrefix.'index.php?taxon='.$taxArr['orderTid'].'" target="_blank">':'').$taxArr['orderName'].($taxArr['orderTid']?'</a>':'').'</td>'."\n";
                echo '<td>'.($taxArr['familyTid']?'<a href="'.$urlPrefix.'index.php?taxon='.$taxArr['familyTid'].'" target="_blank">':'').$taxArr['familyName'].($taxArr['familyTid']?'</a>':'').'</td>'."\n";
                echo '<td>'.($taxArr['tid']?'<a href="'.$urlPrefix.'index.php?taxon='.$taxArr['tid'].'" target="_blank">':'').$taxArr['SciName'].($taxArr['tid']?'</a>':'').'</td>'."\n";
                if(array_key_exists($taxArr['tid'],$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$taxArr['tid']]);
                    echo '<td>'.wordwrap($vernacularStr,100,"<br />\n",true).'</td>'."\n";
                }
                else{
                    echo '<td></td>'."\n";
                }
                echo '</tr>'."\n";
                $recCnt++;
            }
            echo '</table>';
            echo '<div style="clear:both;height:5px;"></div>';
            if($qryCnt > 1){
                echo '<div style="width:100%;">'.$navStr.'</div>';
            }
        }
        elseif($targetTid || $targetTaxon){
            echo '<div style="font-weight:bold;font-size:120%;">The taxon you searched for does not exist in the database.</div>';
        }
        ?>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>

