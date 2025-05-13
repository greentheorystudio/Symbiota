<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/TaxonomyDynamicListManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
header('Cache-Control: no-cache, must-revalidate, max-age=0');

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$index = array_key_exists('index',$_REQUEST)?(int)$_REQUEST['index']:0;
$descLimit = array_key_exists('desclimit',$_REQUEST)?(int)$_REQUEST['desclimit']:0;
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
$listManager->setDescLimit($descLimit);
if($higherRankArr){
    $kingdomArr = $higherRankArr[10];
    $phylumArr = $higherRankArr[30];
    $classArr = $higherRankArr[60];
}

if($targetTid){
    $listManager->setTid($targetTid);
    $listManager->setSortField($sortSelect);
    $listManager->setPageIndex($index);
    $tableArr = $listManager->getTableArr();
    $vernacularArr = $listManager->getVernacularArr();
    $qryCnt = (int)$listManager->getTaxaCnt();
    $urlVars = 'desclimit='.$descLimit.'&orderinput='.$orderInput.'&familyinput='.$familyInput.'&scinameinput='.$scinameInput.'&commoninput='.$commonInput.'&sortSelect='.$sortSelect.'&targettid='.$targetTid.'&desclimit='.$descLimit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Dynamic Species List</title>
    <meta name="description" content="Dynamic species list for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            $("#orderinput").autocomplete({
                source: function( request, response ) {
                    $.getJSON( "<?php echo $GLOBALS['CLIENT_ROOT']; ?>/api/taxa/autofillsciname.php", {
                        term: request.term,
                        limit: 10,
                        rlimit: 100,
                        hideauth: false
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
                    $.getJSON( "<?php echo $GLOBALS['CLIENT_ROOT']; ?>/api/taxa/autofillsciname.php", {
                        term: request.term,
                        limit: 10,
                        rlimit: 140,
                        hideauth: false
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
                    $.getJSON( "<?php echo $GLOBALS['CLIENT_ROOT']; ?>/api/taxa/autofillsciname.php", {
                        term: request.term,
                        limit: 10,
                        hideauth: false
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
                    $.getJSON( "<?php echo $GLOBALS['CLIENT_ROOT']; ?>/api/taxa/autofillvernacular.php", {
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
            /*if(id !== 'kingdomSelect'){
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
            }*/
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
    <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
    <a href="dynamictaxalist.php"><b>Dynamic Species List</b></a>
</div>
<div id="main-container">
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
                <div style="float:left;">
                    <div style="display:flex;flex-direction:column;">
                        <div>
                            <span style="display:inline-block;width:120px;">Kingdom:</span>
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
                        <div style="margin-top:8px;">
                            <span style="display:inline-block;width:120px;">Phylum:</span>
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
                        <div style="margin-top:8px;">
                            <span style="display:inline-block;width:120px;">Class:</span>
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
                        <div style="margin-top:8px;">
                            <span style="display:inline-block;width:120px;">Order:</span>
                            <input name="orderinput" id="orderinput" type="text" style="width:200px;" value="<?php echo $orderInput; ?>" />
                        </div>
                        <div style="margin-top:8px;">
                            <span style="display:inline-block;width:120px;">Family:</span>
                            <input name="familyinput" id="familyinput" type="text" style="width:200px;" value="<?php echo $familyInput; ?>" />
                        </div>
                        <div style="margin-top:8px;">
                            <span style="display:inline-block;width:120px;">Scientific Name:</span>
                            <input name="scinameinput" id="scinameinput" type="text" style="width:250px;" value="<?php echo $scinameInput; ?>" />
                        </div>
                    </div>
                </div>
                <div style="float:right;">
                    <div style="display:flex;flex-direction:column;">
                        <div style="">
                            <span style="display:inline-block;width:120px;">Common Name:</span>
                            <input name="commoninput" id="commoninput" type="text" style="width:350px;" value="<?php echo $commonInput; ?>" />
                        </div>
                        <div style="margin-top:8px;">
                            <span style="display:inline-block;width:120px;">Sort By:</span>
                            <select name="sortSelect">
                                <option value="kingdom" <?php echo ($sortSelect === 'kingdom'?'SELECTED':''); ?>>Kingdom</option>
                                <option value="phylum" <?php echo ($sortSelect === 'phylum'?'SELECTED':''); ?>>Phylum</option>
                                <option value="class" <?php echo ($sortSelect === 'class'?'SELECTED':''); ?>>Class</option>
                                <option value="order" <?php echo ($sortSelect === 'order'?'SELECTED':''); ?>>Order</option>
                                <option value="family" <?php echo ($sortSelect === 'family'?'SELECTED':''); ?>>Family</option>
                                <option value="sciname" <?php echo ($sortSelect === 'sciname'?'SELECTED':''); ?>>Scientific Name</option>
                            </select>
                        </div>
                        <div style="margin-top:90px;">
                            <div style="float:left;">
                                <input type="checkbox" name="desclimit" value="1" <?php echo ($descLimit ? 'CHECKED' : ''); ?> /> Limit to species with information
                            </div>
                            <div style="float:right;">
                                <input name="targettid" id="targettid" type="hidden" value="<?php echo $targetTid; ?>" />
                                <input name="action" type="submit" value="Build Species List"/>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div>
        <?php
        if($tableArr){
            $urlPrefix = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https://':'http://').$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/taxa/';
            $navUrl = $urlPrefix . 'dynamictaxalist.php?' . $urlVars . '&index=';
            $downloadUrl = $urlPrefix . 'dynamictaxalistdownload.php?' . $urlVars;
            $navStr = '<div style="clear:both;display:flex;justify-content:center;">';
            $lastPage = ($qryCnt / 100) + 1;
            $startPage = ($index > 4?$index - 4:1);
            $endPage = ($lastPage > $startPage + 9?$startPage + 9:$lastPage);
            if($qryCnt > 100){
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
            }
            $beginNum = ($index)*100 + 1;
            $endNum = $beginNum + 100 - 1;
            if($endNum > $qryCnt) {
                $endNum = $qryCnt;
            }
            $navStr .= '</div>';
            $navStr .= '<div style="clear:both;display:flex;justify-content:center;align-items:center;align-content:center;gap:10px;">';
            $navStr .= '<div>';
            $navStr .= '<a href="'.$downloadUrl.'"><button type="button">Download Results</button></a>';
            $navStr .= '</div>';
            $navStr .= '<div>';
            $navStr .= 'Page '.($index + 1).', records '.$beginNum.'-'.$endNum.' of '.$qryCnt;
            $navStr .= '</div>';
            $navStr .= '<div></div>';
            $navStr .= '</div>';

            echo '<div style="width:100%;clear:both;margin:5px;">';
            if($qryCnt > 1){
                echo $navStr;
            }
            echo '</div>';
            echo '<div style="clear:both;height:5px;"></div>';
            echo '<table class="styledtable" style="font-family:Arial,serif;"><tr>';
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
            echo '<div style="font-weight:bold;">There are no taxa matching your criteria.</div>';
        }
        ?>
    </div>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
