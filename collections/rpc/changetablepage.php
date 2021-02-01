<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceListManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');

$queryId = array_key_exists('queryId',$_REQUEST)?$_REQUEST['queryId']:0;
$stArrJson = $_REQUEST['starr'];
$targetTid = $_REQUEST['targettid'];
$occIndex = $_REQUEST['occindex'];
$sortField1 = $_REQUEST['sortfield1'];
$sortField2 = $_REQUEST['sortfield2'];
$sortOrder = $_REQUEST['sortorder'];

$stArr = json_decode($stArrJson, true);
$copyURL = '';

if($SOLR_MODE){
    $collManager = new SOLRManager();
    $collManager->setSearchTermsArr($stArr);
    $collManager->setSorting($sortField1,$sortField2,$sortOrder);
    $solrArr = $collManager->getRecordArr($occIndex,1000);
    $recArr = $collManager->translateSOLRRecList($solrArr);
}
else{
    $collManager = new OccurrenceListManager(false);
    $collManager->setSearchTermsArr($stArr);
    $collManager->setSorting($sortField1,$sortField2,$sortOrder);
    $recArr = $collManager->getRecordArr($occIndex,1000);
}

$targetClid = $collManager->getSearchTerm('targetclid');

if(strlen($stArrJson) <= 1800){
    $urlPrefix = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https://':'http://').$_SERVER['HTTP_HOST'].$CLIENT_ROOT.'/collections/listtabledisplay.php';
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
    $recordListHtml .= '<div style="width:790px;clear:both;">';
    $recordListHtml .= '<div style="height:25px;width:100%;display:flex;justify-content:space-between;align-items:center;">';
    $recordListHtml .= '<div style="width:200px;display:flex;justify-content:space-between;align-items:center;">';
    $recordListHtml .= '<div>';
    $recordListHtml .= '<select data-role="none" id="querydownloadselect">';
    $recordListHtml .= '<option>Download Type</option>';
    $recordListHtml .= '<option value="csv">CSV</option>';
    $recordListHtml .= '<option value="kml">KML</option>';
    $recordListHtml .= '<option value="geojson">GeoJSON</option>';
    $recordListHtml .= '<option value="gpx">GPX</option>';
    $recordListHtml .= '</select>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<div>';
    $recordListHtml .= '<button data-role="none" type="button" onclick="processDownloadRequest(false,'.$qryCnt.');" >Download</button>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<div style="width:400px;display:flex;justify-content:space-between;align-items:center;">';
    $recordListHtml .= '<div><a href="list.php?queryId='.$queryId.'" style="cursor:pointer;font-weight:bold;">List View</a></div>';
    $recordListHtml .= '<div><a href="../spatial/index.php?queryId='.$queryId.'" style="cursor:pointer;font-weight:bold;">Spatial Module</a></div>';
    if(strlen($stArrJson) <= 1800){
        $recordListHtml .= '<div><a href="#" style="cursor:pointer;font-weight:bold;" onclick="copySearchUrl();">Copy URL</a></div>';
    }
    if($qryCnt > 1){
        $recordListHtml .= '<div>';
        $recordListHtml .= $navStr;
        $recordListHtml .= '</div>';
    }
    $recordListHtml .= '</div>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '</div>';
    $recordListHtml .= '<div style="clear:both;"></div>';
    $recordListHtml .= '<table class="styledtable" style="font-family:Arial,serif;font-size:12px;"><tr>';
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
    if($QUICK_HOST_ENTRY_IS_ACTIVE) {
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
        if($SYMB_UID && ($IS_ADMIN
                || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($occArr['collid'], $USER_RIGHTS['CollAdmin'], true))
                || (array_key_exists('CollEditor',$USER_RIGHTS) && in_array($occArr['collid'], $USER_RIGHTS['CollEditor'], true)))){
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
        $recordListHtml .= '<td>';
        $recordListHtml .= '<a href="#" onclick="return openIndPU('.$id.','.($targetClid?: '0'). ')">' .$id.'</a> ';
        if($isEditor || ($SYMB_UID && $SYMB_UID === $fieldArr['observeruid'])){
            $recordListHtml .= '<a href="editor/occurrenceeditor.php?occid='.$id.'" target="_blank">';
            $recordListHtml .= '<img src="../images/edit.png" style="height:13px;" title="Edit Record" />';
            $recordListHtml .= '</a>';
        }
        if(isset($occArr['img'])){
            $recordListHtml .= '<img src="../images/image.png" style="height:13px;margin-left:5px;" title="Has Image" />';
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
        if($QUICK_HOST_ENTRY_IS_ACTIVE){
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
