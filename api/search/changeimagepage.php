<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ImageLibraryManager.php');
include_once(__DIR__ . '/../../classes/OccurrenceManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$queryId = array_key_exists('queryId',$_REQUEST)?(int)$_REQUEST['queryId']:0;
$stArrJson = $_REQUEST['starr'] ?? '';
$taxon = $_REQUEST['taxon'] ?? '';
$view = $_REQUEST['view'] ?? 'thumbnail';
$cntPerPage = array_key_exists('cntperpage',$_REQUEST)?(int)$_REQUEST['cntperpage']:100;
$pageNumber = array_key_exists('page',$_REQUEST)?(int)$_REQUEST['page']:1;

$copyURL = '';
$imageArr = array();
$taxaList = array();
$paginationStr = '';
$recordListHtml = '';

$stArr = json_decode(str_replace('%squot;', "'",$stArrJson), true);

$imgLibManager = new ImageLibraryManager();
$collManager = new OccurrenceManager();

if($collManager->validateSearchTermsArr($stArr)){
    if(strlen($stArrJson) <= 1800){
        $urlPrefix = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443)?'https://':'http://').$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/imagelib/search.php';
        $urlArgs = '?starr='.str_replace("'", '%squot;',$stArrJson).'&page='.$pageNumber.'&imagedisplay='.$view.'&taxon='.$taxon;
        $copyURL = $urlPrefix.$urlArgs;
    }

    $collManager->setSearchTermsArr($stArr);
    $imgLibManager->setSearchTermsArr($stArr);
    $recordListHtml .= '<textarea id="urlFullBox" style="position:absolute;left:-9999px;top:-9999px">'.$copyURL.'</textarea>';
    $recordListHtml .= '<div style="height:20px;width:100%;display:flex;justify-content:flex-end;align-items:center;">';
    $recordListHtml .= '<div><a href="../collections/list.php?queryId='.$queryId.'"><button class="icon-button" title="List Display"><i style="height:15px;width:15px;" class="fas fa-list"></i></button></a></div>';
    $recordListHtml .= '<div><a href="../collections/listtabledisplay.php?queryId='.$queryId.'"><button class="icon-button" title="Table Display"><i style="width:15px;height:15px;" class="fas fa-table"></i></button></a></div>';
    $recordListHtml .= '<div><a href="../spatial/index.php?queryId='.$queryId.'"><button class="icon-button" title="Spatial Module"><i style="width:15px;height:15px;" class="fas fa-globe"></i></button></a></div>';
    if(strlen($stArrJson) <= 1800){
        $recordListHtml .= '<div><button class="icon-button" title="Copy Search URL" onclick="copySearchUrl();"><i style="width:15px;height:15px;" class="fas fa-link"></i></button></div>';
    }
    $recordListHtml .= '</div>';
    $recordListHtml .= '<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>';
    if($view === 'thumbnail'){
        $collManager->setTaxon($taxon);
        $sqlWhere = $collManager->getSqlWhere(true);
        $imgLibManager->setSqlWhere($sqlWhere);
        $imageArr = $imgLibManager->getImageArr($pageNumber,$cntPerPage);
        $recordCnt = $imgLibManager->getRecordCnt();

        $lastPage = (int) ($recordCnt / $cntPerPage) + 1;
        $startPage = ($pageNumber > 4?$pageNumber - 4:1);
        if($lastPage > $startPage){
            $endPage = (min($lastPage, $startPage + 9));
            $onclick = 'changeImagePage("","thumbnail",';
            $hrefPrefix = "<a href='#' onclick='".$onclick;
            $paginationStr = '<div style="display:flex;justify-content:space-between;margin-top:4px;margin-bottom:8px;"><div>';
            if($startPage > 1){
                $paginationStr .= "<span class='pagination' style='margin-right:5px;'>".$hrefPrefix."1); return false;'>First</a></span>";
                $paginationStr .= "<span class='pagination' style='margin-right:5px;'>".$hrefPrefix.(($pageNumber - 10) < 1 ?1:$pageNumber - 10)."); return false;'>&lt;&lt;</a></span>";
            }
            for($x = $startPage; $x <= $endPage; $x++){
                if($pageNumber !== $x){
                    $paginationStr .= "<span class='pagination' style='margin-right:3px;'>".$hrefPrefix.$x."); return false;'>".$x. '</a></span>';
                }
                else{
                    $paginationStr .= "<span class='pagination' style='margin-right:3px;font-weight:bold;'>".$x. '</span>';
                }
            }
            if(($lastPage - $startPage) >= 10){
                $paginationStr .= "<span class='pagination' style='margin-left:5px;'>".$hrefPrefix.(($pageNumber + 10) > $lastPage?$lastPage:($pageNumber + 10))."); return false;'>&gt;&gt;</a></span>";
                $paginationStr .= "<span class='pagination' style='margin-left:5px;'>".$hrefPrefix.$lastPage."); return false;'>Last</a></span>";
            }
            $paginationStr .= "</div><div>";
            $beginNum = ($pageNumber - 1)*$cntPerPage + 1;
            $endNum = $beginNum + $cntPerPage - 1;
            if($endNum > $recordCnt) {
                $endNum = $recordCnt;
            }
            $paginationStr .= 'Page ' .$pageNumber. ', records ' .$beginNum. '-' .$endNum. ' of ' .$recordCnt. '</div></div>';
        }
        elseif($recordCnt > 0){
            $paginationStr = '<div style="display:flex;justify-content:flex-end;margin-top:4px;margin-bottom:8px;"><div>';
            $paginationStr .= 'Records 1-' .$recordCnt. ' of ' .$recordCnt. '</div></div>';
        }
        $recordListHtml .= '<div style="width:100%;">';
        $recordListHtml .= $paginationStr;
        $recordListHtml .= '</div>';
        $recordListHtml .= '<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>';
        $recordListHtml .= '<div style="width:98%;margin-left:auto;margin-right:auto;display:flex;flex-direction:row;flex-wrap:wrap;gap:15px;">';
        if($imageArr){
            foreach($imageArr as $imgArr){
                $imgId = $imgArr['imgid'];
                $imgUrl = $imgArr['url'];
                $imgTn = $imgArr['thumbnailurl'];
                if($imgTn){
                    $imgUrl = (isset($GLOBALS['IMAGE_DOMAIN']) && strncmp($imgUrl, '/', 1) === 0) ? $GLOBALS['IMAGE_DOMAIN'].$imgTn : $imgTn;
                }
                elseif(isset($GLOBALS['IMAGE_DOMAIN']) && strncmp($imgUrl, '/', 1) === 0){
                    $imgUrl = $GLOBALS['IMAGE_DOMAIN'].$imgUrl;
                }
                $recordListHtml .= '<div class="tndiv">';
                $recordListHtml .= '<div class="tnimg">';
                if($imgArr['occid']){
                    $recordListHtml .= '<a href="#" onclick="openIndPU('.$imgArr['occid'].');return false;">';
                }
                else{
                    $recordListHtml .= '<a href="#" onclick="openImagePopup('.$imgId.');return false;">';
                }
                $recordListHtml .= '<img src="'.$imgUrl.'" />';
                $recordListHtml .= '</a>';
                $recordListHtml .= '</div>';
                $recordListHtml .= '<div>';
                $sciname = $imgArr['sciname'];
                if($sciname){
                    if(strpos($imgArr['sciname'],' ')) {
                        $sciname = '<i>' . $sciname . '</i>';
                    }
                    if($imgArr['tid']) {
                        $recordListHtml .= '<a href="#" onclick="openTaxonPopup(' . $imgArr['tid'] . ');return false;" >';
                    }
                    $recordListHtml .= $sciname;
                    if($imgArr['tid']) {
                        $recordListHtml .= '</a>';
                    }
                    $recordListHtml .= '<br />';
                }
                if($imgArr['catalognumber']){
                    $label = '';
                    if($imgArr['instcode']){
                        $label .= $imgArr['instcode'];
                    }
                    if($imgArr['catalognumber']){
                        $label .= ($label?':':'') . $imgArr['catalognumber'];
                    }
                    if(!$label){
                        $label = 'Full Record Details';
                    }
                    $recordListHtml .= '<a href="#" onclick="openIndPU('.$imgArr['occid'].');return false;">';
                    $recordListHtml .= $label;
                    $recordListHtml .= '</a>';
                }
                elseif($imgArr['lastname']){
                    $pName = $imgArr['firstname'].' '.$imgArr['lastname'];
                    if(strlen($pName) < 20) {
                        $recordListHtml .= $pName . '<br />';
                    }
                    else {
                        $recordListHtml .= $imgArr['lastname'] . '<br />';
                    }
                }
                $recordListHtml .= '</div>';
                $recordListHtml .= '</div>';
            }
            $recordListHtml .= '</div>';
            $recordListHtml .= '<div style="clear:both;margin:5px 0 5px 0;"><hr /></div>';
            $recordListHtml .= '<div style="width:100%;">'.$paginationStr.'</div>';
            $recordListHtml .= '<div style="clear:both;"></div>';
        }
        else{
            $recordListHtml .= '<div style="font-weight:bold;">';
            $recordListHtml .= 'There were no images matching your search critera';
            $recordListHtml .= '</div>';
        }
    }
    elseif($view === 'famlist'){
        $sqlWhere = $collManager->getSqlWhere(true);
        $imgLibManager->setSqlWhere($sqlWhere);
        $taxaList = $imgLibManager->getFamilyList();

        $recordListHtml .= "<div style='margin-left:20px;margin-bottom:20px;font-weight:bold;'>Select a family to see genera list.</div>";
        foreach($taxaList as $value){
            $onChange = '"'.$value.'","genlist",1';
            $famChange = '"'.$value.'"';
            $recordListHtml .= "<div style='margin-left:30px;'><a href='#' onclick='changeFamily(".$famChange. ');changeImagePage(' .$onChange."); return false;'>".strtoupper($value). '</a></div>';
        }
    }
    elseif($view === 'genlist'){
        $sqlWhere = $collManager->getSqlWhere(true);
        $imgLibManager->setSqlWhere($sqlWhere);
        $taxaList = $imgLibManager->getGenusList($taxon);

        $topOnChange = '"","famlist",1';
        $recordListHtml .= "<div style='margin-left:20px;margin-bottom:10px;font-weight:bold;'><a href='#' onclick='changeImagePage(".$topOnChange."); return false;'>Return to family list</a></div>";
        $recordListHtml .= "<div style='margin-left:20px;margin-bottom:20px;font-weight:bold;'>Select a genus to see species list.</div>";
        foreach($taxaList as $value){
            $onChange = '"'.$value.'","splist",1';
            $recordListHtml .= "<div style='margin-left:30px;'><a href='#' onclick='changeImagePage(".$onChange."); return false;'>".$value. '</a></div>';
        }
    }
    elseif($view === 'splist'){
        $sqlWhere = $collManager->getSqlWhere(true);
        $imgLibManager->setSqlWhere($sqlWhere);
        $taxaList = $imgLibManager->getSpeciesList($taxon);

        $topOnChange = 'selectedFamily,"genlist",1';
        $recordListHtml .= "<div style='margin-left:20px;margin-bottom:10px;font-weight:bold;'><a href='#' onclick='changeImagePage(".$topOnChange."); return false;'>Return to genera list</a></div>";
        $recordListHtml .= "<div style='margin-left:20px;margin-bottom:20px;font-weight:bold;'>Select a species to see images.</div>";
        foreach($taxaList as $key => $value){
            $onChange = '"'.$value.'","thumbnail",1';
            $recordListHtml .= "<div style='margin-left:30px;'><a href='#' onclick='changeImagePage(".$onChange."); return false;'>".$value. '</a></div>';
        }
    }
}

echo $recordListHtml;
