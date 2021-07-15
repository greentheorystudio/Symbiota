<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/FieldGuideManager.php');
include_once(__DIR__ . '/../../classes/OccurrenceCleaner.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
ini_set('max_execution_time', 180);

$action = array_key_exists('action',$_POST)?$_POST['action']: '';
$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$resultId = array_key_exists('resid',$_REQUEST)?$_REQUEST['resid']:0;
$viewMode = array_key_exists('viewmode',$_REQUEST)?$_REQUEST['viewmode']:'full';
$start = array_key_exists('start',$_REQUEST)?$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?$_REQUEST['limit']:100;

$apiManager = new FieldGuideManager();
$cleanManager = new OccurrenceCleaner();
$resultArr = array();
$imageCntArr = array();
$resultTot = 0;
$statusStr = '';

if($collId) {
    $cleanManager->setCollId($collId);
}
$collMap = $cleanManager->getCollMap();

$isEditor = 0;
if($GLOBALS['SYMB_UID']){
    if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
        $isEditor = 1;
    }
}

if($isEditor){
    $apiManager->setCollID($collId);
    if($action === 'Add Determinations'){
        $apiManager->processDeterminations($_POST);
        $statusStr = 'Determinations added';
    }
    if($resultId){
        $apiManager->setJobID($resultId);
        $apiManager->setViewMode($viewMode);
        $apiManager->setRecLimit($limit);
        $apiManager->setRecStart($start);
        $apiManager->primeFGResults();
        $apiManager->processFGResults();
        $resultArr = $apiManager->getResults();
        $imageCntArr = $apiManager->getImageCnts();
        $tidArr = $apiManager->getTids();
        $resultTot = $apiManager->getResultTot();
    }
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $collMap['collectionname']; ?> Fieldguide Results Viewer</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css" />
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../../js/symb/shared.js?ver=20210621"></script>
    <script type="text/javascript">
        function validateForm(){
            const dbElements = document.getElementsByName("occid[]");
            for(let i = 0; i < dbElements.length; i++){
                const dbElement = dbElements[i];
                if(dbElement.checked) {
                    return true;
                }
            }
            alert("Please select occurrences to be determined!");
            return false;
        }

        function selectAll(f){
            let boxesChecked = true;
            if(!f.selectall.checked){
                boxesChecked = false;
            }
            const dbElements = document.getElementsByName("occid[]");
            for(let i = 0; i < dbElements.length; i++){
                dbElements[i].checked = boxesChecked;
            }

        }

        function submitViewForm(f){
            f.submit();
        }
    </script>
</head>
<body style="background-color:white;margin-left:0;margin-right:0;">
    <div class='navpath'>
        <a href='../../index.php'>Home</a> &gt;&gt;
        <a href='collprofiles.php?emode=1&collid=<?php echo $collId; ?>'>Collection Management</a> &gt;&gt;
        <b>Fieldguide Results Viewer</b>
    </div>

    <div id="innertext" style="background-color:white;">
        <?php
        echo '<h2 style="margin-top:0;margin-bottom:0;">'.$collMap['collectionname'].' ('.$collMap['code'].')</h2>';
        if($statusStr){
            ?>
            <hr/>
            <div style="margin:15px;color:red;">
                <?php echo $statusStr; ?>
            </div>
            <hr/>
            <?php
        }
        if($isEditor){
            if($resultArr){
                ?>
                <div style="">
                    <div style='float:left;'>
                        <form name="viewform" action="fgresults.php" method="post" onsubmit="">
                            <div style="width:250px;height:10px;">
                                <div style="float:left;">
                                    <input name="viewmode" type="radio" value="full" <?php echo ($viewMode === 'full'?'checked':''); ?> onchange="submitViewForm(this.form);" /> Full Results
                                </div>
                                <div style="float:right;">
                                    <input name="viewmode" type="radio" value="filtered" <?php echo ($viewMode === 'filtered'?'checked':''); ?> onchange="submitViewForm(this.form);" /> Filtered Results
                                </div>
                            </div>
                            <input name="collid" type="hidden" value="<?php echo $collId; ?>" />
                            <input name="resid" type="hidden" value="<?php echo $resultId; ?>" />
                            <input name="start" type="hidden" value="<?php echo $start; ?>" />
                        </form>
                    </div>

                    <div style='float:right;'>
                        <form name="downloadcsv" id="downloadcsv" style="margin-bottom:0;" action="fgcsv.php" method="post" onsubmit="">
                            <input type="hidden" name="collid" value='<?php echo $collId; ?>' />
                            <input type="hidden" name="resid" value="<?php echo $resultId; ?>" />
                            <input type="hidden" name="viewmode" value="<?php echo $viewMode; ?>" />
                            <input type="submit" name="action" value="Download CSV" />
                        </form>
                    </div>
                </div>

                <div style="clear:both;">
                    <b>Use the checkboxes to select the records you would like to add determinations, and the radio buttons to select which determination to add.</b>
                </div>
                <form name="fgbatchidform" action="fgresults.php" method="post" onsubmit="return validateForm();">
                    <?php
                    $recCnt = count($resultArr);
                    if($resultTot > $limit){
                        echo '<div style="width:300px;float:right;">';
                        if($start > 0){
                            $href = 'fgresults.php?collid='.$collId.'&resid='.$resultId.'&viewmode='.$viewMode.'&start='.($start-$limit);
                            echo '<div style="float:left;"><a href="'.$href.'"><b>&lt;&lt; LAST '.$limit.' RESULTS</b></a></div>';
                        }
                        if(($start+$limit) < $resultTot){
                            $href = 'fgresults.php?collid='.$collId.'&resid='.$resultId.'&viewmode='.$viewMode.'&start='.($start+$limit);
                            echo '<div style="float:right;"><a href="'.$href.'"><b>NEXT '.$limit.' RESULTS &gt;&gt;</b></a></div>';
                        }
                        echo '</div>';
                    }
                    echo '<div><b>'.($start+1).' to '.($start+$recCnt).' of '.$resultTot.' Results </b></div>';
                    ?>
                    <table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
                        <tr>
                            <th style="width:40px;">Record ID</th>
                            <th style="width:40px;">Inst. Code</th>
                            <th style="width:40px;">Coll. Code</th>
                            <th style="width:20px;"><input name="selectall" type="checkbox" title="Select/Deselect All" onclick="selectAll(this.form)" /></th>
                            <th>Current Identification</th>
                            <th>Family</th>
                            <th></th>
                            <th></th>
                            <th>Fieldguide Identification</th>
                            <th>Fieldguide Training Images</th>
                        </tr>
                        <?php
                        $setCnt = 0;
                        $prevOccId = 0;
                        $prevImgId = 0;
                        $currID = '';
                        foreach($resultArr as $occId => $occArr){
                            if($prevOccId !== $occId){
                                $prevOccId = $occId;
                                $setCnt++;
                                $firstOcc = true;
                                $firstRadio = true;
                                $recResults = false;
                                $instCode = $occArr['InstitutionCode'];
                                $collCode = $occArr['CollectionCode'];
                                $currID = $occArr['sciname'];
                                $family = $occArr['family'];
                                unset($occArr['InstitutionCode'], $occArr['CollectionCode'], $occArr['sciname'], $occArr['family']);
                                foreach($occArr as $imgId => $imgArr){
                                    if($imgArr['results']) {
                                        $recResults = true;
                                    }
                                }
                            }
                            foreach($occArr as $imgId => $imgArr){
                                if($prevImgId !== $imgId){
                                    $prevImgId = $imgId;
                                    $imgurl = $imgArr['url'];
                                    $fgStatus = $imgArr['status'];
                                    $fgidarr = $imgArr['results'];
                                    $firstImg = true;
                                }
                                if($fgidarr){
                                    foreach($fgidarr as $name){
                                        $valid = false;
                                        $displayName = $name;
                                        $note = '';
                                        $tId = 0;
                                        if(array_key_exists($name,$tidArr) && $tidArr[$name]){
                                            if($currID == $name){
                                                $note = 'Current determination';
                                            }
                                            else if(count($tidArr[$name]) == 1){
                                                $valid = true;
                                                $tId = $tidArr[$name][0];
                                            }
                                            else{
                                                $note = 'Name ambiguous';
                                            }
                                        }
                                        else{
                                            $note = 'Not valid in thesaurus';
                                        }
                                        if($note) {
                                            $displayName = $name . ' <span style="color:red;">' . $note . '</span>';
                                        }
                                        echo '<tr '.(($setCnt % 2) == 1?'class="alt"':'').'>';
                                        echo '<td>'."\n";
                                        if($firstOcc) {
                                            echo '<a href="../editor/occurrenceeditor.php?occid=' . $occId . '" target="_blank">' . $occId . '</a>' . "\n";
                                        }
                                        echo '</td>'."\n";
                                        echo '<td>'.($firstOcc?$instCode:'').'</td>'."\n";
                                        echo '<td>'.($firstOcc?$collCode:'').'</td>'."\n";
                                        echo '<td>'."\n";
                                        if($firstOcc && $recResults) {
                                            echo '<input name="occid[]" type="checkbox" value="' . $occId . '" />' . "\n";
                                        }
                                        echo '</td>'."\n";
                                        echo '<td>'."\n";
                                        if($firstOcc) {
                                            echo '<a href="' . $GLOBALS['CLIENT_ROOT'] . '/taxa/index.php?taxon=' . $currID . '" target="_blank">' . $currID . '</a>' . "\n";
                                        }
                                        echo '</td>'."\n";
                                        echo '<td>'."\n";
                                        if($firstOcc) {
                                            echo $family . "\n";
                                        }
                                        echo '</td>'."\n";
                                        echo '<td>'."\n";
                                        if($firstImg) {
                                            echo '<a href="' . $imgurl . '" target="_blank">View Image</a>' . "\n";
                                        }
                                        echo '</td>'."\n";
                                        echo '<td>'."\n";
                                        if($valid) {
                                            echo '<input name="id' . $occId . '" type="radio" value="' . $tId . '" ' . ($firstRadio ? 'checked' : '') . '/>' . "\n";
                                        }
                                        echo '</td>'."\n";
                                        if($note === 'Current determination' || $valid){
                                            echo '<td><a href="'.$GLOBALS['CLIENT_ROOT'].'/taxa/index.php?taxon='.$name.'" target="_blank">'.$displayName.'</a></td>'."\n";
                                        }
                                        else{
                                            echo '<td>'.$displayName.'</td>'."\n";
                                        }
                                        echo '<td>'.(($name && isset($imageCntArr[$name]))?$imageCntArr[$name]:'').'</td>'."\n";
                                        $firstOcc = false;
                                        $firstImg = false;
                                        if($valid) {
                                            $firstRadio = false;
                                        }
                                    }
                                }
                                elseif($viewMode === 'full'){
                                    $note = '';
                                    if($fgStatus === 'OK' && !$fgidarr){
                                        $note = '<span style="color:red;">No results provided.</span>';
                                    }
                                    echo '<tr '.(($setCnt % 2) == 1?'class="alt"':'').'>';
                                    echo '<td>'."\n";
                                    if($firstOcc) {
                                        echo '<a href="../editor/occurrenceeditor.php?occid=' . $occId . '" target="_blank">' . $occId . '</a>' . "\n";
                                    }
                                    echo '</td>'."\n";
                                    echo '<td>'.($firstOcc?$instCode:'').'</td>'."\n";
                                    echo '<td>'.($firstOcc?$collCode:'').'</td>'."\n";
                                    echo '<td>'."\n";
                                    if($firstOcc && $recResults) {
                                        echo '<input name="occid[]" type="checkbox" value="' . $occId . '" />' . "\n";
                                    }
                                    echo '</td>'."\n";
                                    echo '<td>'."\n";
                                    if($firstOcc) {
                                        echo '<a href="' . $GLOBALS['CLIENT_ROOT'] . '/taxa/index.php?taxon=' . $currID . '" target="_blank">' . $currID . '</a>' . "\n";
                                    }
                                    echo '</td>'."\n";
                                    echo '<td>'."\n";
                                    if($firstOcc) {
                                        echo $family . "\n";
                                    }
                                    echo '</td>'."\n";
                                    echo '<td>'."\n";
                                    if($firstImg) {
                                        echo '<a href="' . $imgurl . '" target="_blank">View Image</a>' . "\n";
                                    }
                                    echo '</td>'."\n";
                                    echo '<td>'."\n";
                                    echo '</td>'."\n";
                                    echo '<td>'.$note.'</td>'."\n";
                                    echo '<td></td>'."\n";
                                    $firstOcc = false;
                                    $firstImg = false;
                                }
                            }
                        }
                        ?>
                    </table>
                    <div style="margin:15px;">
                        <input name="collid" type="hidden" value="<?php echo $collId; ?>" />
                        <input name="resid" type="hidden" value="<?php echo $resultId; ?>" />
                        <input name="start" type="hidden" value="<?php echo $start; ?>" />
                        <input name="viewmode" type="hidden" value="<?php echo $viewMode; ?>" />
                        <input name="action" type="submit" value="Add Determinations" />
                    </div>
                </form>
                <?php
            }
            else{
                echo '<p><b>No results to display</b></p>';
            }
            ?>
            <div>
                <a href="fgbatch.php?collid=<?php echo $collId; ?>">Return to Fieldguide Batch Processing</a>
            </div>
            <?php
        }
        else{
            echo '<h2>You are not authorized to access this page</h2>';
        }
        ?>
    </div>
</body>
</html>
