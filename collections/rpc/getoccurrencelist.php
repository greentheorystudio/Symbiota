<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceListManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');

$queryId = array_key_exists('queryId',$_REQUEST)?$_REQUEST['queryId']:0;
$stArrJson = $_REQUEST['starr'] ?? '';
$targetTid = $_REQUEST['targettid'];
$pageNumber = $_REQUEST['page'];
$cntPerPage = 100;

$stArr= json_decode($stArrJson, true);
$copyURL = '';

$collManager = null;
$occurArr = array();

if(strlen($stArrJson) <= 1800){
    $urlPrefix = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https://':'http://').$_SERVER['HTTP_HOST'].$CLIENT_ROOT.'/collections/list.php';
    $urlArgs = '?starr='.$stArrJson.'&page='.$pageNumber;
    $copyURL = $urlPrefix.$urlArgs;
}

if(isset($SOLR_MODE) && $SOLR_MODE){
    $collManager = new SOLRManager();
    $collManager->setSearchTermsArr($stArr);
    $solrArr = $collManager->getRecordArr($pageNumber,$cntPerPage);
    $occurArr = $collManager->translateSOLRRecList($solrArr);
}
else{
    $collManager = new OccurrenceListManager();
    $collManager->setSearchTermsArr($stArr);
    $occurArr = $collManager->getRecordArr($pageNumber,$cntPerPage);
}

$htmlStr = '<div style="float:right;">';
$targetClid = $collManager->getSearchTerm('targetclid');
if($targetTid && $collManager->getClName()){
    $htmlStr .= '<div style="cursor:pointer;margin:8px 8px 0px 0px;" onclick="addAllVouchersToCl('.$targetTid.')" title="Link All Vouchers on Page">';
    $htmlStr .= '<img src="../images/voucheradd.png" style="border:solid 1px gray;height:13px;margin-right:5px;" /></div>';
}
$htmlStr .= '</div><div style="margin:5px;">';
$htmlStr .= '<div><b>Dataset:</b> '.$collManager->getDatasetSearchStr().'</div>';
if($taxaSearchStr = $collManager->getTaxaSearchStr()){
    $htmlStr .= '<div><b>Taxa:</b> '.wordwrap($taxaSearchStr, 115, "<br />\n", true).'</div>';
}
if($localSearchStr = $collManager->getLocalSearchStr()){
    $htmlStr .= '<div><b>Search Criteria:</b> '.$localSearchStr.'</div>';
}
$htmlStr .= '<textarea id="urlFullBox" style="position:absolute;left:-9999px;top:-9999px">'.$copyURL.'</textarea>';
$htmlStr .= '</div>';
$htmlStr .= '<div style="clear:both;"></div>';
$htmlStr .= '<div style="height:20px;width:100%;display:flex;justify-content:space-between;align-items:center;">';
$htmlStr .= '<div style="height:20px;width:275px;display:flex;justify-content:flex-start;align-items:center;">';
$htmlStr .= '<div>';
$htmlStr .= '<select data-role="none" id="querydownloadselect">';
$htmlStr .= '<option>Download Type</option>';
$htmlStr .= '<option value="csv">CSV</option>';
$htmlStr .= '<option value="kml">KML</option>';
$htmlStr .= '<option value="geojson">GeoJSON</option>';
$htmlStr .= '<option value="gpx">GPX</option>';
$htmlStr .= '</select>';
$htmlStr .= '</div>';
$htmlStr .= '<div>';
$htmlStr .= '<button class="icon-button" title="Download" onclick="processDownloadRequest(false,'.$collManager->getRecordCnt().');"><img src="../images/download.svg" style="width:15px; height:15px" /></button>';
$htmlStr .= '</div>';
$htmlStr .= '</div>';
$htmlStr .= '<div style="height:20px;width:400px;display:flex;justify-content:flex-end;align-items:center;">';
if($SYMB_UID){
    $htmlStr .= '<div><button class="icon-button" title="Dataset Management" onclick="displayDatasetTools();"><img src="../images/dataset.png" style="width:15px;" /></button></div>';
}
$htmlStr .= '<div><a href="listtabledisplay.php?queryId='.$queryId.'"><button class="icon-button" title="Table Display"><img src="../images/table.png" style="width:15px; height:15px" /></button></a></div>';
$htmlStr .= '<div><a href="../spatial/index.php?queryId='.$queryId.'"><button class="icon-button" title="Spatial Module"><img src="../images/globe.svg" style="width:15px; height:15px" /></button></a></div>';
if(strlen($stArrJson) <= 1800){
    $htmlStr .= '<div><button class="icon-button" title="Copy URL to Clipboard" onclick="copySearchUrl();"><img src="../images/link.svg" style="width:15px; height:15px" /></button></div>';
}
$htmlStr .= '</div>';
$htmlStr .= '</div>';

$htmlStr .= '<div style="clear:both;"></div>';

$paginationStr = '<div><div style="clear:both;"><hr/></div><div style="float:left;margin:5px;">';
$lastPage = (int)($collManager->getRecordCnt() / $cntPerPage) + 1;
$startPage = ($pageNumber > 4?$pageNumber - 4:1);
$endPage = ($lastPage > $startPage + 9?$startPage + 9:$lastPage);
$pageBar = '';
if($startPage > 1){
    $pageBar .= "<span class='pagination' style='margin-right:5px;'><a href='' onclick='setOccurrenceList(1);return false;'>First</a></span>";
    $pageBar .= "<span class='pagination' style='margin-right:5px;'><a href='' onclick='setOccurrenceList(".(($pageNumber - 10) < 1?1:$pageNumber - 10).");return false;'>&lt;&lt;</a></span>";
}
for($x = $startPage; $x <= $endPage; $x++){
    if($pageNumber !== $x){
        $pageBar .= "<span class='pagination' style='margin-right:3px;'><a href='' onclick='setOccurrenceList(" .$x.");return false;'>".$x. '</a></span>';
    }
    else{
        $pageBar .= "<span class='pagination' style='margin-right:3px;font-weight:bold;'>" .$x. '</span>';
    }
}
if(($lastPage - $startPage) >= 10){
    $pageBar .= "<span class='pagination' style='margin-left:5px;'><a href='' onclick='setOccurrenceList(".(($pageNumber + 10) > $lastPage?$lastPage:($pageNumber + 10)).");return false;'>&gt;&gt;</a></span>";
    $pageBar .= "<span class='pagination' style='margin-left:5px;'><a href='' onclick='setOccurrenceList(".$lastPage.");return false;'>Last</a></span>";
}
$pageBar .= '</div><div style="float:right;margin:5px;">';
$beginNum = ($pageNumber - 1)*$cntPerPage + 1;
$endNum = $beginNum + $cntPerPage - 1;
if($endNum > $collManager->getRecordCnt()) {
    $endNum = $collManager->getRecordCnt();
}
$pageBar .= 'Page '.$pageNumber.', records '.$beginNum.'-'.$endNum.' of '.$collManager->getRecordCnt();
$paginationStr .= $pageBar;
$paginationStr .= '</div><div style="clear:both;"><hr/></div></div>';
$htmlStr .= $paginationStr;

if($occurArr){
    $htmlStr .= '<form name="occurListForm" method="post" action="datasets/datasetHandler.php" onsubmit="return validateOccurListForm(this)" target="_blank">';
    $htmlStr .= '<div id="dataset-tools" class="dataset-div" style="clear:both;display:none">';
    $htmlStr .= '<fieldset>';
    $htmlStr .= '<legend>Dataset Management</legend>';
    $datasetArr = $collManager->getDatasetArr();
    $htmlStr .= '<div style="padding:5px;float:left;">Dataset target: </div>';
    $htmlStr .= '<div style="padding:5px;float:left;">';
    $htmlStr .= '<select name="targetdatasetid">';
    $htmlStr .= '<option value="">------------------------</option>';
    if($datasetArr){
        foreach($datasetArr as $datasetID => $datasetName){
            $htmlStr .= '<option value="'.$datasetID.'">'.$datasetName.'</option>';
        }
    }
    else {
        $htmlStr .= '<option value="">No existing datasets available</option>';
    }
    $htmlStr .= '<option value="">----------------------------------</option>';
    $htmlStr .= '<option value="--newDataset">Create New Dataset</option>';
    $htmlStr .= '</select>';
    $htmlStr .= '</div>';
    $htmlStr .= '<div style="clear:both;margin:5px 0;">';
    $htmlStr .= '<span class="checkbox-elem"><input name="selectall" type="checkbox" onclick="selectAllDataset(this)" /></span>';
    $htmlStr .= '<span style="padding:10px;">Select all records on page</span>';
    $htmlStr .= '</div>';
    $htmlStr .= '<div style="clear:both;">';
    $htmlStr .= '<input name="dsstarrjson" id="dsstarrjson" type="hidden" value="" />';
    $htmlStr .= '<div style="padding:5px 0;float:left;"><button name="action" type="submit" value="addSelectedToDataset" onclick="return hasSelectedOccid(this.form)">Add Selected Records to Dataset</button></div>';
    $htmlStr .= '<div style="padding:5px;float:left;"><button name="action" type="submit" value="addAllToDataset">Add Complete Query to Dataset</button></div>';
    $htmlStr .= '</div>';
    $htmlStr .= '</fieldset>';
    $htmlStr .= '</div>';
    $htmlStr .= '<table id="omlisttable">';
    $prevCollid = 0;
    $specOccArr = array();
    foreach($occurArr as $occid => $fieldArr){
        $collId = $fieldArr['collid'];
        $specOccArr[] = $occid;
        if($collId !== $prevCollid){
            $prevCollid = $collId;
            $isEditor = false;
            if($SYMB_UID && ($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollAdmin'], true)) || (array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollEditor'], true)))){
                $isEditor = true;
            }
            $instCode = $fieldArr['institutioncode'];
            if($fieldArr['collectioncode']) {
                $instCode .= ':' . $fieldArr['collectioncode'];
            }
            $htmlStr .= '<tr><td colspan="2"><h2>';
            $htmlStr .= '<a href="misc/collprofiles.php?collid='.$collId.'">'.$fieldArr['collectionname'].'</a>';
            $htmlStr .= '</h2><hr /></td></tr>';
        }
        $htmlStr .= '<tr><td style="width:60px;vertical-align:top;text-align:center;">';
        $htmlStr .= '<a href="misc/collprofiles.php?collid='.$collId.'&acronym='.$fieldArr['institutioncode'].'">';
        if($fieldArr['collicon']){
            $icon = (strpos($fieldArr['collicon'], 'images') === 0 ?'../':'').$fieldArr['collicon'];
            $htmlStr .= '<img align="bottom" src="'.$icon.'" style="width:35px;border:0px;" />';
        }
        $htmlStr .= '</a>';
        $htmlStr .= '<div style="font-weight:bold;font-size:75%;">';
        $htmlStr .= $instCode;
        $htmlStr .= '</div>';
        $htmlStr .= '<div style="margin-top:10px"><span class="dataset-div checkbox-elem" style="display:none;"><input name="occid[]" type="checkbox" value="'.$occid.'" /></span></div>';
        $htmlStr .= '</td><td>';
        if($isEditor || ($SYMB_UID && $SYMB_UID === $fieldArr['observeruid'])){
            $htmlStr .= '<div style="float:right;" title="Edit Occurrence Record">';
            $htmlStr .= '<a href="editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">';
            $htmlStr .= '<img src="../images/edit.svg" style="width:15px;height:15px;border:0;" /></a></div>';
        }
        if($targetTid && $collManager->getClName()){
            $htmlStr .= '<div style="float:right;" >';
            $htmlStr .= '<a href="#" onclick="addVoucherToCl('.$occid.','.$targetClid.','.$targetTid.')" title="Link occurrence voucher to '.$collManager->getClName().';return false;">';
            $htmlStr .= '<img src="../images/voucheradd.png" style="border:solid 1px gray;height:13px;margin-right:5px;" /></a></div>';
        }
        if(isset($fieldArr['img'])){
            $htmlStr .= '<div style="float:right;margin:5px 25px;">';
            $htmlStr .= '<a href="#" onclick="return openIndPU('.$occid.','.($targetClid?: '0').');">';
            $htmlStr .= '<img src="'.$fieldArr['img'].'" style="height:70px" /></a></div>';
        }
        $htmlStr .= '<div style="margin:4px;">';
        $htmlStr .= '<a target="_blank" href="../taxa/index.php?taxon='.$fieldArr['sciname'].'">';
        $htmlStr .= '<span style="font-style:italic;">'.$fieldArr['sciname'].'</span></a> '.$fieldArr['author'].'</div>';
        $htmlStr .= '<div style="margin:4px">';
        $htmlStr .= '<span style="width:150px;">'.$fieldArr['accession'].'</span>';
        $htmlStr .= '<span style="width:200px;margin-left:30px;">'.$fieldArr['collector'].'&nbsp;&nbsp;&nbsp;'.($fieldArr['collnumber'] ?? '').'</span>';
        if(isset($fieldArr['date'])) {
            $htmlStr .= '<span style="margin-left:30px;">' . $fieldArr['date'] . '</span>';
        }
        $htmlStr .= '</div><div style="margin:4px">';
        $localStr = '';
        if($fieldArr['country']) {
            $localStr .= $fieldArr['country'] . ', ';
        }
        if($fieldArr['state']) {
            $localStr .= $fieldArr['state'] . ', ';
        }
        if($fieldArr['county']) {
            $localStr .= $fieldArr['county'] . ', ';
        }
        if($fieldArr['locality']) {
            $localStr .= $fieldArr['locality'] . ', ';
        }
        if(isset($fieldArr['assochost']) && $fieldArr['assochost']) {
            $localStr .= $fieldArr['assochost'] . ', ';
        }
        if(isset($fieldArr['elev']) && $fieldArr['elev']) {
            $localStr .= $fieldArr['elev'] . 'm';
        }
        if(strlen($localStr) > 2) {
            $localStr = trim($localStr, ' ,');
        }
        $htmlStr .= $localStr;
        $htmlStr .= '</div><div style="margin:4px">';
        $htmlStr .= '<b><a href="#" onclick="return openIndPU('.$occid.','.($targetClid?: '0').');">Full Record Details</a></b>';
        $htmlStr .= '</div></td></tr><tr><td colspan="2"><hr/></td></tr>';
    }
    $specOccJson = json_encode($specOccArr);
    $htmlStr .= "<input id='specoccjson' type='hidden' value='".$specOccJson."' />";
    $htmlStr .= '</table>';
    $htmlStr .= '</form>';
    $htmlStr .= $paginationStr.'<hr/>';
}
else{
    $htmlStr .= '<div><h3>Your query did not return any results. Please modify your query parameters</h3>';
    $tn = $collManager->getTaxaSearchStr();
    if($p = strpos($tn,';')){
        $tn = substr($tn,0,$p);
    }
    if($p = strpos($tn,'=>')){
        $tn = substr($tn,$p+2);
    }
    if($p = strpos($tn,'(')){
        $tn = substr($tn,0,$p);
    }
    if($closeArr = $collManager->getCloseTaxaMatch(trim($tn))){
        $htmlStr .= '<div style="margin: 40px 0 200px 20px;font-weight:bold;">';
        $htmlStr .= 'Perhaps you were looking for: ';
        foreach($closeArr as $v){
            $htmlStr .= '<a href="harvestparams.php?taxa='.$v.'">'.$v.'</a>, ';
        }
        $htmlStr = substr($htmlStr,0,-2);
        $htmlStr .= '</div>';
    }
    $htmlStr .= '</div>';
}

echo $htmlStr;
