<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceListManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;
$stArrJson = $_REQUEST['starr'];
$targetTid = (int)$_REQUEST['targettid'];
$occIndex = (int)$_REQUEST['occindex'];
$sortField1 = $_REQUEST['sortfield1'];
$sortField2 = $_REQUEST['sortfield2'];
$sortOrder = $_REQUEST['sortorder'];

$stArr = json_decode($stArrJson, true);
$copyURL = '';

if($GLOBALS['SOLR_MODE']){
    $collManager = new SOLRManager();
    if($collManager->validateSearchTermsArr($stArr)){
        $collManager->setSearchTermsArr($stArr);
        $collManager->setSorting($sortField1,$sortField2,$sortOrder);
        $solrArr = $collManager->getRecordArr($occIndex,1000);
        $recArr = $collManager->translateSOLRRecList($solrArr);
    }
}
else{
    $collManager = new OccurrenceListManager();
    if($collManager->validateSearchTermsArr($stArr)){
        $collManager->setSearchTermsArr($stArr);
        $collManager->setSorting($sortField1,$sortField2,$sortOrder);
        $recArr = $collManager->getRecordArr($occIndex,1000);
    }
}

$targetClid = $collManager->getSearchTerm('targetclid');

if($collManager->validateSearchTermsArr($stArr) && strlen($stArrJson) <= 1800){
    $urlPrefix = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https://':'http://').$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/collections/listtabledisplay.php';
    $urlArgs = '?starr='.$stArrJson.'&occindex='.$occIndex.'&sortfield1='.$sortField1.'&sortfield2='.$sortField2.'&sortorder='.$sortOrder;
    $copyURL = $urlPrefix.$urlArgs;
}

$recordListHtml = '';

$qryCnt = $collManager->getRecordCnt();
$navStr = '<div style="float:right;">';
if(($occIndex*1000) > 1000){
    $navStr .= "<a href='' title='Previous 1000 records' onclick='changeTablePage(".($occIndex-1). ");return false;'>&lt;&lt;</a>";
}
$navStr .= ' | ';
$navStr .= ($occIndex <= 1?1:(($occIndex-1)*1000)+1).'-'.($qryCnt<1000+$occIndex?$qryCnt:(($occIndex-1)*1000)+1000).' of '.$qryCnt.' records';
$navStr .= ' | ';
if($qryCnt > (1000+$occIndex)){
    $navStr .= "<a href='' title='Next 1000 records' onclick='changeTablePage(".($occIndex+1). ");return false;'>&gt;&gt;</a>";
}
$navStr .= '</div>';

if($recArr){
    $recordListHtml .= '<div style="width:790px;clear:both;margin-top:5px;margin-bottom:5px;">';
    $recordListHtml .= '<div style="height:25px;width:100%;display:flex;justify-content:space-between;align-items:center;">';
    $recordListHtml .= '<div style="width:200px;display:flex;justify-content:flex-start;align-items:center;">';
    $recordListHtml .= '<div>';
    $recordListHtml .= '<select data-role="none" id="querydownloadselect">';
    $recordListHtml .= '<option>Download Type</option>';
    $recordListHtml .= '<option value="csv">CSV/ZIP</option>';
    $recordListHtml .= '<option value="kml">KML</option>';
    $recordListHtml .= '<option value="geojson">GeoJSON</option>';
    $recordListHtml .= '<option value="gpx">GPX</option>';
    $recordListHtml .= '</select>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<div>';
    $recordListHtml .= '<button class="icon-button" title="Download" onclick="processDownloadRequest(false,'.$qryCnt.');"><i style="height:15px;width:15px;" class="fas fa-download"></i></button>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<div style="width:400px;display:flex;justify-content:flex-end;align-items:center;">';
    if($GLOBALS['SYMB_UID']){
        $recordListHtml .= '<div><button class="icon-button" title="Dataset Management" onclick="displayDatasetTools();"><i style="height:15px;width:15px;" class="fas fa-layer-group"></i></button></div>';
    }
    $recordListHtml .= '<div><a href="list.php?queryId='.$queryId.'"><button class="icon-button" title="List Display"><i style="height:15px;width:15px;" class="fas fa-list"></i></button></a></div>';
    $recordListHtml .= '<div><a href="../spatial/index.php?queryId='.$queryId.'"><button class="icon-button" title="Spatial Module"><i style="height:15px;width:15px;" class="fas fa-globe"></i></button></a></div>';
    if(strlen($stArrJson) <= 1800){
        $recordListHtml .= '<div><button class="icon-button" title="Copy URL to Clipboard" onclick="copySearchUrl();"><i style="height:15px;width:15px;" class="fas fa-link"></i></button></div>';
    }
    if($qryCnt > 1){
        $recordListHtml .= '<div>';
        $recordListHtml .= $navStr;
        $recordListHtml .= '</div>';
    }
    $recordListHtml .= '</div>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<form name="occurListForm" method="post" action="datasets/datasetHandler.php" onsubmit="return validateOccurListForm(this)" target="_blank">';
    $recordListHtml .= '<div id="dataset-tools" class="dataset-div" style="clear:both;display:none">';
    $recordListHtml .= '<fieldset>';
    $recordListHtml .= '<legend>Dataset Management</legend>';
    $datasetArr = $collManager->getDatasetArr();
    $recordListHtml .= '<div style="padding:5px;float:left;">Dataset target: </div>';
    $recordListHtml .= '<div style="padding:5px;float:left;">';
    $recordListHtml .= '<select name="targetdatasetid">';
    $recordListHtml .= '<option value="">------------------------</option>';
    if($datasetArr){
        foreach($datasetArr as $datasetID => $datasetName){
            $recordListHtml .= '<option value="'.$datasetID.'">'.$datasetName.'</option>';
        }
    }
    else {
        $recordListHtml .= '<option value="">No existing datasets available</option>';
    }
    $recordListHtml .= '<option value="">----------------------------------</option>';
    $recordListHtml .= '<option value="--newDataset">Create New Dataset</option>';
    $recordListHtml .= '</select>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<div style="clear:both;margin:5px 0;">';
    $recordListHtml .= '<span class="checkbox-elem"><input name="selectall" type="checkbox" onclick="selectAllDataset(this)" /></span>';
    $recordListHtml .= '<span style="padding:10px;">Select all records on page</span>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<div style="clear:both;">';
    $recordListHtml .= '<input name="dsstarrjson" id="dsstarrjson" type="hidden" value="" />';
    $recordListHtml .= '<div style="padding:5px 0;float:left;"><button name="action" type="submit" value="addSelectedToDataset" onclick="return hasSelectedOccid(this.form)">Add Selected Records to Dataset</button></div>';
    $recordListHtml .= '<div style="padding:5px;float:left;"><button name="action" type="submit" value="addAllToDataset">Add Complete Query to Dataset</button></div>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '</fieldset>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<table class="styledtable" style="font-family:Arial,serif;font-size:12px;"><tr>';
    $recordListHtml .= '<th class="dataset-div checkbox-elem" style="display:none;"></th>';
    $recordListHtml .= '<th>Symbiota ID</th>';
    $recordListHtml .= '<th>Collection</th>';
    $recordListHtml .= '<th>Catalog Number</th>';
    $recordListHtml .= '<th>Family</th>';
    $recordListHtml .= '<th>Scientific Name</th>';
    $recordListHtml .= '<th>Collector</th>';
    $recordListHtml .= '<th>Number</th>';
    $recordListHtml .= '<th>Event Date</th>';
    $recordListHtml .= '<th>Country</th>';
    $recordListHtml .= '<th>State/Province</th>';
    $recordListHtml .= '<th>County</th>';
    $recordListHtml .= '<th>Locality</th>';
    $recordListHtml .= '<th>Decimal Latitude</th>';
    $recordListHtml .= '<th>Decimal Longitude</th>';
    $recordListHtml .= '<th>Habitat</th>';
    if($GLOBALS['QUICK_HOST_ENTRY_IS_ACTIVE']) {
        $recordListHtml .= '<th>Host</th>';
    }
    $recordListHtml .= '<th>Substrate</th>';
    $recordListHtml .= '<th>Elevation</th>';
    $recordListHtml .= '<th>Associated Taxa</th>';
    $recordListHtml .= '<th>Individual Count</th>';
    $recordListHtml .= '<th>Life Stage</th>';
    $recordListHtml .= '<th>Sex</th>';
    $recordListHtml .= '</tr>';
    $recCnt = 0;
    foreach($recArr as $id => $occArr){
        $isEditor = false;
        if($GLOBALS['SYMB_UID'] && ($GLOBALS['IS_ADMIN']
                || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array((int)$occArr['collid'], $GLOBALS['USER_RIGHTS']['CollAdmin'], true))
                || (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array((int)$occArr['collid'], $GLOBALS['USER_RIGHTS']['CollEditor'], true)))){
            $isEditor = true;
        }
        $collection = $occArr['institutioncode'];
        if($occArr['collectioncode']) {
            $collection .= ':' . $occArr['collectioncode'];
        }
        if($occArr['sciname']) {
            $occArr['sciname'] = '<i>' . $occArr['sciname'] . '</i> ';
        }
        $recordListHtml .= '<tr ' .(($recCnt%2)?'class="alt"':'').">\n";
        $recordListHtml .= '<td class="dataset-div checkbox-elem" style="display:none;"><input name="occid[]" type="checkbox" value="'.$id.'" /></td>'."\n";
        $recordListHtml .= '<td>';
        $recordListHtml .= '<a href="#" onclick="return openIndPU('.$id.','.($targetClid?: '0'). ')">' .$id.'</a> ';
        if($isEditor || ($GLOBALS['SYMB_UID'] && $GLOBALS['SYMB_UID'] === $occArr['observeruid'])){
            $recordListHtml .= '<a href="editor/occurrenceeditor.php?occid='.$id.'" target="_blank">';
            $recordListHtml .= '<i style="height:15px;width:15px;" title="Edit Record" class="far fa-edit"></i>';
            $recordListHtml .= '</a>';
        }
        if(isset($occArr['img'])){
            $recordListHtml .= '<i style="height:15px;width:15px;margin-left:5px;" title="Has Image" class="fas fa-camera"></i>';
        }
        $recordListHtml .= '</td>'."\n";
        $recordListHtml .= '<td>'.$collection.'</td>'."\n";
        $recordListHtml .= '<td>'.$occArr['accession'].'</td>'."\n";
        $recordListHtml .= '<td>'.$occArr['family'].'</td>'."\n";
        $recordListHtml .= '<td>'.$occArr['sciname'].($occArr['author']? ' ' .$occArr['author']: '').'</td>'."\n";
        $recordListHtml .= '<td>'.$occArr['collector'].'</td>'."\n";
        $recordListHtml .= '<td>'.(array_key_exists('collnumber',$occArr)?$occArr['collnumber']: '').'</td>'."\n";
        $recordListHtml .= '<td>'.(array_key_exists('date',$occArr)?$occArr['date']: '').'</td>'."\n";
        $recordListHtml .= '<td>'.$occArr['country'].'</td>'."\n";
        $recordListHtml .= '<td>'.$occArr['state'].'</td>'."\n";
        $recordListHtml .= '<td>'.$occArr['county'].'</td>'."\n";
        $recordListHtml .= '<td>'.((strlen($occArr['locality'])>80)?substr($occArr['locality'],0,80).'...':$occArr['locality']).'</td>'."\n";
        $recordListHtml .= '<td>'.(array_key_exists('decimallatitude',$occArr)?$occArr['decimallatitude']: '').'</td>'."\n";
        $recordListHtml .= '<td>'.(array_key_exists('decimallongitude',$occArr)?$occArr['decimallongitude']: '').'</td>'."\n";
        if(array_key_exists('habitat',$occArr)){
            $recordListHtml .= '<td>'.((strlen($occArr['habitat'])>80)?substr($occArr['habitat'],0,80).'...':$occArr['habitat']).'</td>'."\n";
        }
        else{
            $recordListHtml .= '<td></td>'."\n";
        }
        if($GLOBALS['QUICK_HOST_ENTRY_IS_ACTIVE']){
            if(array_key_exists('assochost',$occArr)){
                $recordListHtml .= '<td>'.((strlen($occArr['assochost'])>80)?substr($occArr['assochost'],0,80).'...':$occArr['assochost']).'</td>'."\n";
            }
            else{
                $recordListHtml .= '<td></td>'."\n";
            }
        }
        if(array_key_exists('substrate',$occArr)){
            $recordListHtml .= '<td>'.((strlen($occArr['substrate'])>80)?substr($occArr['substrate'],0,80).'...':$occArr['substrate']).'</td>'."\n";
        }
        else{
            $recordListHtml .= '<td></td>'."\n";
        }
        $recordListHtml .= '<td>'.(array_key_exists('elev',$occArr)?$occArr['elev']: '').'</td>'."\n";
        if(array_key_exists('associatedtaxa',$occArr)){
            $recordListHtml .= '<td>'.((strlen($occArr['associatedtaxa'])>80)?substr($occArr['associatedtaxa'],0,80).'...':$occArr['associatedtaxa']).'</td>'."\n";
        }
        else{
            $recordListHtml .= '<td></td>'."\n";
        }
        $recordListHtml .= '<td>'.(array_key_exists('individualCount',$occArr)?$occArr['individualCount']: '').'</td>'."\n";
        $recordListHtml .= '<td>'.(array_key_exists('lifeStage',$occArr)?$occArr['lifeStage']: '').'</td>'."\n";
        $recordListHtml .= '<td>'.(array_key_exists('sex',$occArr)?$occArr['sex']: '').'</td>'."\n";
        $recordListHtml .= "</tr>\n";
        $recCnt++;
    }
    $recordListHtml .= '</table>';
    $recordListHtml .= '<div style="clear:both;height:5px;"></div>';
    $recordListHtml .= '<textarea id="urlFullBox" style="position:absolute;left:-9999px;top:-9999px">'.$copyURL.'</textarea>';
    if($qryCnt > 1){
        $recordListHtml .= '<div style="width:790px;">'.$navStr.'</div>';
    }
    $recordListHtml .= '*Click on the Symbiota identifier in the first column to see Full Record Details.';
}
else{
    $recordListHtml .= '<div style="font-weight:bold;font-size:120%;">No records found matching the query</div>';
}
echo $recordListHtml;
