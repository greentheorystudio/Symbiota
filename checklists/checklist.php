<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid',$_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$pid = array_key_exists('pid',$_REQUEST) ? (int)$_REQUEST['pid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> <?php echo $clManager->getClName(); ?> checklist</title>
        <meta name="description" content="Information for the <?php echo $clManager->getClName(); ?> checklist">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol-ext.min.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/turf.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/shp.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jszip.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/stream.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/FileSaver.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/html2canvas.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/geotiff.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/plotty.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const CLID = <?php echo $clid; ?>;
            const QUERYID = <?php echo $queryId; ?>;
            const PID = <?php echo $pid; ?>;
        </script>
    </head>
    <body>
        <div id="mainContainer">
            <?php
            if($clValue || $dynClid){
                echo '<div class="checklist-header-row">';
                echo '<div class="checklist-header-element">';
                ?>
                <div style="color:#990000;">
                    <a href="checklist.php?cl=<?php echo $clValue. '&proj=' .$pid. '&dynclid=' .$dynClid; ?>">
                        <h1><?php echo $clManager->getClName(); ?></h1>
                    </a>
                </div>
                <?php
                if($activateKey && !$printMode){
                    ?>
                    <div>
                        <a href="../ident/key.php?clid=<?php echo $clValue. '&pid=' .$pid;?>">
                            <i style='width:15px; height:15px;' class="fas fa-key"></i>
                        </a>
                    </div>
                    <?php
                }
                if(!$printMode && $taxaArray){
                    ?>
                    <div>
                        <?php
                        $varStr = '?clid=' .$clid. '&dynclid=' .$dynClid. '&listname=' .$clManager->getClName(). '&taxonfilter=' .$taxonFilter. '&showcommon=' .$showCommon. '&thesfilter=' .$thesFilter. '&showsynonyms=' .$showSynonyms;
                        ?>
                        <a href="../games/flashcards.php<?php echo $varStr; ?>"><i class="fas fa-gamepad"></i></a>
                    </div>
                    <?php
                }
                echo '</div>';
                if($clValue && $isEditor && !$printMode){
                    ?>
                    <div class="checklist-header-element">
                        <span>
                            <a href="checklistadmin.php?clid=<?php echo $clid.'&pid='.$pid; ?>" title="Checklist Administration">
                                <i style='width:20px;height:20px;' class="fas fa-cog"></i>
                            </a>
                        </span>
                        <span>
                            <a href="voucheradmin.php?clid=<?php echo $clid.'&pid='.$pid; ?>" title="Manage Linked Voucher">
                                <i style='width:20px;height:20px;' class="fas fa-link"></i>
                            </a>
                        </span>
                        <span onclick="toggle('editspp');">
                            <i style='width:20px;height:20px;cursor:pointer;' class="fas fa-clipboard-list" title="Edit Species List"></i>
                        </span>
                    </div>
                    <?php
                }
                echo '</div>';
                if($clValue){
                    if($clArray['type'] === 'rarespp'){
                        echo '<div style="clear:both;">';
                        echo '<b>Sensitive species checklist for:</b> '.$clArray['locality'];
                        echo '</div>';
                    }
                    if($clArray['authors']){
                        echo '<div style="clear:both;">';
                        echo '<h3>Authors:</h3>';
                        echo $clArray['authors'];
                        echo '</div>';
                    }
                    if($clArray['publication']){
                        $pubStr = $clArray['publication'];
                        if($pubStr && strncmp($pubStr, 'http', 4) === 0 && !strpos($pubStr,' ')) {
                            $pubStr = '<a href="' . $pubStr . '" target="_blank">' . $pubStr . '</a>';
                        }
                        echo "<div><span style='font-weight:bold;'>Citation:</span> ".$pubStr. '</div>';
                    }
                    if(($locStr || $clArray['latcentroid'] || $clArray['abstract'] || $clArray['notes'])){
                        ?>
                        <div class="moredetails" style="<?php echo (($showDetails || $printMode)?'display:none;':''); ?>color:blue;cursor:pointer;" onclick="toggle('moredetails')">More Details</div>
                        <div class="moredetails" style="display:<?php echo (($showDetails && !$printMode)?'block':'none'); ?>;color:blue;cursor:pointer;" onclick="toggle('moredetails')">Less Details</div>
                        <div class="moredetails" style="display:<?php echo (($showDetails || $printMode)?'block':'none'); ?>;">
                            <?php
                            if($locStr){
                                echo "<div><span style='font-weight:bold;'>Locality: </span>".$locStr. '</div>';
                            }
                            if($clValue && $clArray['abstract']){
                                echo "<div><span style='font-weight:bold;'>Abstract: </span>".$clArray['abstract']. '</div>';
                            }
                            echo ($clValue && $clArray['notes']) ? "<div><span style='font-weight:bold;'>Notes: </span>".$clArray['notes']. '</div>' : '';
                            ?>
                        </div>
                        <?php
                    }
                }
                if($statusStr){
                    ?>
                    <hr />
                    <div style="margin:20px;font-weight:bold;color:red;">
                        <?php echo $statusStr; ?>
                    </div>
                    <hr />
                    <?php
                }
                ?>
                <div>
                    <hr/>
                </div>
                <div>
                    <?php
                    if(!$printMode){
                        ?>
                        <div id="cloptiondiv">
                            <form name="optionform" action="checklist.php" method="post">
                                <fieldset style="background-color:white;padding-bottom:10px;">
                                    <legend><b>Options</b></legend>
                                    <div id="taxonfilterdiv" title="Filter species list by family or genus">
                                        <div>
                                            <b>Search:</b>
                                            <input type="text" id="taxonfilter" name="taxonfilter" value="<?php echo $taxonFilter;?>" size="20" />
                                        </div>
                                        <div>
                                            <div style="margin-left:10px;">
                                                <input type='checkbox' name='searchcommon' value='1' <?php echo ($searchCommon? 'checked' : '');?> /> Common Names<br/>
                                                <input type="checkbox" name="searchsynonyms" value="1" <?php echo ($searchSynonyms? 'checked' : '');?> /> Synonyms
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <input id='thesfilter' name='thesfilter' type='checkbox' value='1' <?php echo ($thesFilter ? 'checked' : '');?> /> Filter Through Thesaurus
                                    </div>
                                    <div>
                                        <input id='showsynonyms' name='showsynonyms' type='checkbox' value='1' <?php echo ($showSynonyms ? 'checked' : '');?> /> Display Synonyms
                                    </div>
                                    <div>
                                        <input id='showcommon' name='showcommon' type='checkbox' value='1' <?php echo ($showCommon ? 'checked' : '');?> /> Common Names
                                    </div>
                                    <div>
                                        <input id='showimages' name='showimages' type='checkbox' value='1' <?php echo ($showImages? 'checked' : ''); ?> onclick="showImagesChecked(this.form);" />
                                        Display as Images
                                    </div>
                                    <?php
                                    if($clValue){
                                        ?>
                                        <div style='display:<?php echo ($showImages? 'none' : 'block');?>' id="showvouchersdiv">
                                            <input name='showvouchers' type='checkbox' value='1' <?php echo ($showVouchers? 'checked' : ''); ?> />
                                            Notes &amp; Vouchers
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div style='display:<?php echo ($showImages? 'none' : 'block');?>' id="showauthorsdiv">
                                        <input name='showauthors' type='checkbox' value='1' <?php echo ($showAuthors? 'checked' : ''); ?> />
                                        Taxon Authors
                                    </div>
                                    <div style='' id="showalphataxadiv">
                                        <input name='showalphataxa' type='checkbox' value='1' <?php echo ($showAlphaTaxa? 'checked' : ''); ?> />
                                        Show Taxa Alphabetically
                                    </div>
                                    <div style="margin:5px 0 0 5px;">
                                        <input type='hidden' name='cl' value='<?php echo $clid; ?>' />
                                        <input type='hidden' name='dynclid' value='<?php echo $dynClid; ?>' />
                                        <input type="hidden" name="proj" value="<?php echo $pid; ?>" />
                                        <input type='hidden' name='defaultoverride' value='1' />
                                        <?php
                                        if(!$taxonFilter) {
                                            echo "<input type='hidden' name='pagenumber' value='" . $pageNumber . "' />";
                                        }
                                        ?>
                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                            <input type="submit" name="submitaction" value="Rebuild List" onclick="changeOptionFormAction('checklist.php?cl=<?php echo $clValue. '&proj=' .$pid. '&dynclid=' .$dynClid; ?>','_self');" />
                                            <div style="width:100px;display:flex;justify-content:flex-end;align-items:center;">
                                                <div id="wordicondiv" style="margin-right:5px;">
                                                    <button class="icon-button" type="submit" name="submitaction" value="Export to DOCX" style="margin:0;padding:2px;" title="Export to DOCX" onclick="changeOptionFormAction('defaultchecklistexport.php','_self');">
                                                        <i style="height:15px;width:15px;" class="far fa-file-word"></i>
                                                    </button>
                                                </div>
                                                <div style="margin-right:5px;">
                                                    <button class="icon-button" type="submit" name="submitaction" value="Print List" style="margin:0;padding:2px;" title="Print in Browser" onclick="changeOptionFormAction('checklist.php','_blank');">
                                                        <i style="height:15px;width:15px;" class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                                <div style="margin-right:5px;">
                                                    <button class="icon-button" type="submit" name="submitaction" value="Download List" style="margin:0;padding:2px;" title="Download List" onclick="changeOptionFormAction('checklist.php?cl=<?php echo $clValue. '&proj=' .$pid. '&dynclid=' .$dynClid; ?>','_self');">
                                                        <i style="height:15px;width:15px;" class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                            <?php
                            if($clValue && $isEditor){
                                ?>
                                <div class="editspp" style="display:<?php echo ($editMode?'block':'none'); ?>;width:250px;margin-top:10px;">
                                    <form id='addspeciesform' action='checklist.php' method='post' name='addspeciesform' onsubmit="return validateAddSpecies(this);">
                                        <fieldset style='margin:5px 0 5px 5px;background-color:#FFFFCC;'>
                                            <legend><b>Add New Species to Checklist</b></legend>
                                            <div>
                                                <b>Taxon:</b><br/>
                                                <input type="text" id="speciestoadd" name="speciestoadd" style="width:174px;" />
                                                <input type="hidden" id="tidtoadd" name="tidtoadd" value="" />
                                            </div>
                                            <div>
                                                <b>Family Override:</b><br/>
                                                <input type="text" name="familyoverride" style="width:122px;" title="Only enter if you want to override current family" />
                                            </div>
                                            <div>
                                                <b>Habitat:</b><br/>
                                                <input type="text" name="habitat" style="width:170px;" />
                                            </div>
                                            <div>
                                                <b>Abundance:</b><br/>
                                                <input type="text" name="abundance" style="width:145px;" />
                                            </div>
                                            <div>
                                                <b>Notes:</b><br/>
                                                <input type="text" name="notes" style="width:175px;" />
                                            </div>
                                            <div style="padding:2px;">
                                                <b>Internal Notes:</b><br/>
                                                <input type="text" name="internalnotes" style="width:126px;" title="Displayed to administrators only" />
                                            </div>
                                            <div>
                                                <b>Source:</b><br/>
                                                <input type="text" name="source" style="width:167px;" />
                                            </div>
                                            <div>
                                                <input type="hidden" name="cl" value="<?php echo $clid; ?>" />
                                                <input type="hidden" name="cltype" value="<?php echo $clArray['type']; ?>" />
                                                <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
                                                <input type='hidden' name='thesfilter' value='<?php echo $thesFilter; ?>' />
                                                <input type='hidden' name='showsynonyms' value='<?php echo $showSynonyms; ?>' />
                                                <input type='hidden' name='showcommon' value='<?php echo $showCommon; ?>' />
                                                <input type='hidden' name='showvouchers' value='<?php echo $showVouchers; ?>' />
                                                <input type='hidden' name='showauthors' value='<?php echo $showAuthors; ?>' />
                                                <input type='hidden' name='taxonfilter' value='<?php echo $taxonFilter; ?>' />
                                                <input type='hidden' name='searchcommon' value='<?php echo $searchCommon; ?>' />
                                                <input type="hidden" name="emode" value="1" />
                                                <input type="submit" name="submitadd" value="Add Species to List"/>
                                                <hr />
                                            </div>
                                            <div style="text-align:center;">
                                                <a href="tools/checklistloader.php?clid=<?php echo $clid.'&pid='.$pid;?>">Batch Upload Spreadsheet</a>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div style="min-height: 450px;">
                        <div style="margin-bottom:15px;">
                            <div style="margin:3px;">
                                <h3>Families: <?php echo $clManager->getFamilyCount(); ?></h3>
                            </div>
                            <div style="margin:3px;">
                                <h3>Genera: <?php echo $clManager->getGenusCount(); ?></h3>
                            </div>
                            <div style="margin:3px;">
                                <h3>Species: <?php echo $clManager->getSpeciesCount(); ?></h3>
                            </div>
                            <div style="margin:3px;">
                                <h3>Total Taxa: <?php echo $clManager->getTaxaCount(); ?> (including subsp. and var.)</h3>
                            </div>
                        </div>
                        <?php
                        $taxaLimit = ($showImages?$clManager->getImageLimit():$clManager->getTaxaLimit());
                        $pageCount = ceil($clManager->getTaxaCount()/$taxaLimit);
                        $argStr = '';
                        if($pageCount > 1 && !$printMode){
                            if(($pageNumber)>$pageCount) {
                                $pageNumber = 1;
                            }
                            $argStr .= '&cl=' .$clValue. '&dynclid=' .$dynClid.($showCommon? '&showcommon=' .$showCommon: '').($showVouchers? '&showvouchers=' .$showVouchers: '');
                            $argStr .= ($thesFilter? '&thesfilter=' .$thesFilter: '');
                            $argStr .= ($showSynonyms? '&showsynonyms=' .$showSynonyms: '');
                            $argStr .= ($showAuthors? '&showauthors=' .$showAuthors: '');
                            $argStr .= ($pid? '&pid=' .$pid: '').($showImages? '&showimages=' .$showImages: '').($taxonFilter? '&taxonfilter=' .$taxonFilter: '');
                            $argStr .= ($searchCommon? '&searchcommon=' .$searchCommon: '').($searchSynonyms? '&searchsynonyms=' .$searchSynonyms: '');
                            $argStr .= ($showAlphaTaxa? '&showalphataxa=' .$showAlphaTaxa: '');
                            $argStr .= ($defaultOverride? '&defaultoverride=' .$defaultOverride: '');
                            echo '<hr /><div>Page<b>' .($pageNumber)."</b> of <b>$pageCount</b>: ";
                            for($x=1; $x <= $pageCount; $x++){
                                if($x>1) {
                                    echo ' | ';
                                }
                                if($pageNumber === $x){
                                    echo '<b>';
                                }
                                else{
                                    echo "<a href='checklist.php?pagenumber=".$x.$argStr."'>";
                                }
                                echo ($x);
                                if($pageNumber === $x){
                                    echo '</b>';
                                }
                                else{
                                    echo '</a>';
                                }
                            }
                            echo '</div><hr />';
                        }
                        $prevfam = '';
                        if($showImages){
                            echo '<div style="clear:both;display:flex;flex-direction:row;flex-wrap:wrap;gap:20px;">';
                            foreach($taxaArray as $tid => $sppArr){
                                $tu = (array_key_exists('tnurl',$sppArr)?$sppArr['tnurl']:'');
                                $u = (array_key_exists('url',$sppArr)?$sppArr['url']:'');
                                $imgSrc = ($tu?:$u);
                                ?>
                                <div class="tndiv">
                                    <div class="tnimg" style="<?php echo ($imgSrc? '' : 'border:1px solid black;'); ?>">
                                        <?php
                                        $spUrl = "../taxa/index.php?taxon=$tid&cl=".$clid;
                                        if($imgSrc){
                                            if(!$printMode) {
                                                echo "<a href='" . $spUrl . "' target='_blank'>";
                                            }
                                            echo "<img src='".$imgSrc."' />";
                                            if(!$printMode) {
                                                echo '</a>';
                                            }
                                        }
                                        else{
                                            ?>
                                            <div style="margin-top:50px;">
                                                <b>Image<br/>not yet<br/>available</b>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div>
                                        <?php
                                        if(!$printMode) {
                                            echo '<a href="' . $spUrl . '" target="_blank">';
                                        }
                                        echo '<b>'.$sppArr['sciname'].'</b>';
                                        if(!$printMode) {
                                            echo '</a>';
                                        }
                                        if(array_key_exists('vern',$sppArr)){
                                            echo "<div style='font-weight:bold;'>".$sppArr['vern']. '</div>';
                                        }
                                        if($isEditor){
                                            ?>
                                            <span class="editspp" style="display:<?php echo ($editMode?'inline':'none'); ?>;">
                                                <i style='width:13px;height:13px;cursor:pointer;' title='edit details' class="fas fa-edit" onclick="openPopup('clsppeditor.php?tid=<?php echo $tid. '&clid=' .$clid; ?>');"></i>
                                            </span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            echo '</div>';
                        }
                        else{
                            $voucherArr = $clManager->getVoucherArr();
                            foreach($taxaArray as $tid => $sppArr){
                                if(!$showAlphaTaxa){
                                    $family = $sppArr['family'];
                                    if($family !== $prevfam){
                                        $famUrl = "../taxa/index.php?taxon=$family&cl=".$clid;
                                        ?>
                                        <div class="familydiv" id="<?php echo $family;?>" style="margin:15px 0 5px 0;font-weight:bold;">
                                            <a href="<?php echo $famUrl; ?>" target="_blank" style="color:black;"><?php echo $family;?></a>
                                        </div>
                                        <?php
                                        $prevfam = $family;
                                    }
                                }
                                $spUrl = "../taxa/index.php?taxon=$tid&cl=".$clid;
                                echo "<div id='tid-$tid' style='margin:0 0 3px 10px;'>";
                                echo '<div style="clear:left">';
                                if(!$printMode && !preg_match('/\ssp\d/',$sppArr['sciname'])) {
                                    echo "<a href='" . $spUrl . "' target='_blank'>";
                                }
                                echo '<b><i>' .$sppArr['sciname']. '</b></i> ';
                                if(array_key_exists('author',$sppArr)) {
                                    echo $sppArr['author'];
                                }
                                if(!$printMode && !preg_match('/\ssp\d/',$sppArr['sciname'])) {
                                    echo '</a>';
                                }
                                if(array_key_exists('vern',$sppArr)){
                                    echo " - <span style='font-weight:bold;'>".$sppArr['vern']. '</span>';
                                }
                                if($isEditor){
                                    ?>
                                    <span class="editspp" style="display:<?php echo ($editMode?'inline':'none'); ?>;">
                                        <i style='width:13px;height:13px;cursor:pointer;' title='edit details' class="fas fa-edit" onclick="openPopup('clsppeditor.php?tid=<?php echo $tid. '&clid=' .$clid; ?>');"></i>
                                    </span>
                                    <?php
                                    if($showVouchers && array_key_exists('searchterms',$clArray) && $clArray['searchterms']){
                                        ?>
                                        <span class="editspp" style="display:none;">
                                            <i style='width:13px;height:13px;cursor:pointer;' title='Link Voucher Occurrences' class="fas fa-link" onclick="setPopup(<?php echo $tid . ',' . $clid;?>);"></i>
                                        </span>
                                        <?php
                                    }
                                }
                                echo "</div>\n";
                                if($showSynonyms && isset($sppArr['syn'])){
                                    echo '<div class="syn-div">['.$sppArr['syn'].']</div>';
                                }
                                if($showVouchers){
                                    $voucStr = '';
                                    if(array_key_exists($tid,$voucherArr)){
                                        $voucCnt = 0;
                                        foreach($voucherArr[$tid] as $occid => $collName){
                                            $voucStr .= ', ';
                                            if($voucCnt === 4 && !$printMode){
                                                $voucStr .= '<a href="#" id="morevouch-'.$tid.'" onclick="return toggleVoucherDiv('.$tid. ')">more...</a>' .
                                                    '<span id="voucdiv-'.$tid.'" style="display:none;">';
                                            }
                                            if(!$printMode) {
                                                $openPopupArgs = "'../collections/individual/index.php'," . $occid;
                                                $voucStr .= '<a href="#" onclick="return openIndividualPopup(' . $openPopupArgs . ')">';
                                            }
                                            $voucStr .= $collName;
                                            if(!$printMode) {
                                                $voucStr .= "</a>\n";
                                            }
                                            $voucCnt++;
                                        }
                                        if($voucCnt > 4 && !$printMode) {
                                            $voucStr .= '</span><a href="#" id="lessvouch-' . $tid . '" style="display:none;" onclick="return toggleVoucherDiv(' . $tid . ')">...less</a>';
                                        }
                                        $voucStr = substr($voucStr,2);
                                    }
                                    $noteStr = '';
                                    if(array_key_exists('notes',$sppArr)){
                                        $noteStr = $sppArr['notes'];
                                    }
                                    if($noteStr || $voucStr){
                                        echo "<div style='margin-left:15px;'>".$noteStr.($noteStr && $voucStr?'; ':'').$voucStr. '</div>';
                                    }
                                }
                                echo "</div>\n";
                            }
                        }
                        $taxaLimit = ($showImages?$clManager->getImageLimit():$clManager->getTaxaLimit());
                        if(!$printMode && $clManager->getTaxaCount() > (($pageNumber)*$taxaLimit)){
                            echo '<div style="margin:20px;clear:both;">';
                            echo '<a href="checklist.php?pagenumber='.($pageNumber+1).$argStr.'"> Display next '.$taxaLimit.' taxa...</a></div>';
                        }
                        if(!$taxaArray) {
                            echo "<h2 style='margin:40px;'>No Taxa Found</h2>";
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            else{
                ?>
                <div style="color:red;">
                    ERROR: Checklist identification is null!
                </div>
                <?php
            }
            ?>
        </div>
        <div style="clear:both;"></div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/determinationRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/geneticLinkRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/colorPicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/copyURLButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/collectionCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/listDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/spatialDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDownloadOptionsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDataDownloader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/tableDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/keyDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/checklistDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/imageDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/advancedQueryBuilder.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCollectionsBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaPopupTabControls.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRecordsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSelectionsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSymbologyTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanelLeftShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanelTopShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSidePanelShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSideButtonTray.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialRasterColorScaleSelect.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialVectorToolsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialPointVectorToolsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRecordsSymbologyExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialVectorToolsExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRasterToolsExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSidePanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialDrawToolSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialBaseLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialActiveLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialMapSettingsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerLayerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerLayerGroupElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerQuerySelectorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRow.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRowGroup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoTabModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script>
            const keyIdentificationModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'search-criteria-popup': searchCriteriaPopup,
                    'selector-input-element': selectorInputElement,
                    'spatial-analysis-popup': spatialAnalysisPopup
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const checklistStore = useChecklistStore();
                    const searchStore = useSearchStore();

                    const activeChidArr = Vue.computed(() => {
                        const valArr = [];
                        keyDataArr.value.forEach(heading => {
                            if(!heading['characterArr'].every((character) => !activeCidArr.value.includes(Number(character['cid'])))){
                                valArr.push(Number(heading['chid']));
                            }
                        });
                        return valArr;
                    });
                    const activeCidArr = Vue.ref([]);
                    const activeFamilyArr = Vue.ref([]);
                    const activeTidArr = Vue.ref([]);
                    const characterDependencyDataArr = Vue.ref([]);
                    const checklistData = Vue.ref({});
                    const checklistName = Vue.computed(() => {
                        return checklistData.value.hasOwnProperty('name') ? checklistData.value['name'] : '';
                    });
                    const clId = Vue.ref(CLID);
                    const clidArr = Vue.ref([]);
                    const clientRoot = baseStore.getClientRoot;
                    const commonNameData = Vue.ref({});
                    const csidArr = Vue.ref([]);
                    const displayCommonNamesVal = Vue.ref(false);
                    const displayImagesVal = Vue.ref(false);
                    const displayQueryPopup = Vue.ref(false);
                    const imageData = Vue.ref({});
                    const keyDataArr = Vue.ref([]);
                    const languageArr = [];
                    const pId = Vue.ref(PID);
                    const popupWindowType = Vue.ref(null);
                    const projectData = Vue.ref({});
                    const projectName = Vue.computed(() => {
                        return projectData.value.hasOwnProperty('projname') ? projectData.value['projname'] : '';
                    });
                    const queryId = QUERYID;
                    const selectedCidArr = Vue.computed(() => {
                        const valueArr = selectedStateArr.value.length > 0 ? selectedStateArr.value.map(state => Number(state['cid'])) : [];
                        return valueArr.length > 0 ? valueArr.filter((value, index, array) => array.indexOf(value) === index) : [];
                    });
                    const selectedCsidArr = Vue.computed(() => {
                        const valueArr = selectedStateArr.value.length > 0 ? selectedStateArr.value.map(state => Number(state['csid'])) : [];
                        return valueArr.length > 0 ? valueArr.filter((value, index, array) => array.indexOf(value) === index) : [];
                    });
                    const selectedSortByOption = Vue.ref('family');
                    const selectedStateArr = Vue.ref([]);
                    const showSpatialPopup = Vue.ref(false);
                    const sortByOptions = Vue.ref([
                        {value: 'family', label: 'Family/Scientific Name'},
                        {value: 'sciname', label: 'Scientific Name'}
                    ]);
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const taxaCount = Vue.computed(() => {
                        return activeTidArr.value.length;
                    });
                    const taxaDataArr = Vue.ref([]);
                    const taxaDisplayDataArr = Vue.ref([]);
                    const tidArr = Vue.ref([]);

                    function buildChecklist(){
                        if(searchStore.getSearchTermsValid){
                            showWorking('Loading...');
                            const options = {
                                schema: 'occurrence',
                                spatial: 0
                            };
                            searchStore.getSearchTidArr(options, (tidArr) => {
                                if(tidArr.length > 0){
                                    checklistStore.createTemporaryChecklistFromTidArr(tidArr, (res) => {
                                        hideWorking();
                                        if(Number(res) > 0){
                                            setQueryPopupDisplay(false);
                                            clId.value = Number(res);
                                            setChecklistData();
                                        }
                                        else{
                                            showNotification('negative', 'An error occurred while creating the checklist.');
                                        }
                                    });
                                }
                                else{
                                    hideWorking();
                                    showNotification('negative', 'There were no taxa matching your criteria.');
                                }
                            });
                        }
                        else{
                            showNotification('negative', 'Please enter search criteria.');
                        }
                    }

                    function closeSpatialPopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        searchStore.clearSpatialInputValues();
                    }

                    function openSpatialPopup(type) {
                        searchStore.setSpatialInputValues();
                        popupWindowType.value = type;
                        showSpatialPopup.value = true;
                    }

                    function processCharacterStateSelectionChange(state, value) {
                        if(Number(value) === 1){
                            selectedStateArr.value.push(state);
                        }
                        else{
                            const index = selectedStateArr.value.indexOf(state);
                            selectedStateArr.value.splice(index, 1);
                        }
                        setActiveCidArr();
                        setActiveTaxa();
                    }

                    function processDisplayCommonNameChange(value) {
                        displayCommonNamesVal.value = Number(value) === 1;
                    }

                    function processDisplayImagesChange(value) {
                        displayImagesVal.value = Number(value) === 1;
                    }

                    function processKeyData(keyData) {
                        keyData['character-headings'].forEach(heading => {
                            const headingCharacterArr = [];
                            const characterArr = keyData['characters'].filter((character) => Number(character['chid']) === Number(heading['chid']));
                            characterArr.forEach(character => {
                                const characterStateArr = [];
                                const stateArr = keyData['character-states'].filter((state) => Number(state['cid']) === Number(character['cid']));
                                stateArr.forEach(state => {
                                    characterStateArr.push(state);
                                });
                                characterStateArr.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                                character['stateArr'] = characterStateArr.slice();
                                characterDependencyDataArr.value.push({
                                    cid: character['cid'],
                                    dependencies: character['dependencies'].slice()
                                });
                                headingCharacterArr.push(character);
                            });
                            headingCharacterArr.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                            heading['characterArr'] = headingCharacterArr.slice();
                            keyDataArr.value.push(heading);
                        });
                        keyDataArr.value.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                        setActiveCidArr();
                    }

                    function processSortByChange(value) {
                        selectedSortByOption.value = value;
                        setTaxaDisplayData();
                    }

                    function processSpatialData(data) {
                        searchStore.processSpatialPopupData(popupWindowType.value, data);
                    }

                    function processTaxaData() {
                        taxaDataArr.value.forEach(taxon => {
                            if(!tidArr.value.includes(taxon['tid'])){
                                tidArr.value.push(taxon['tid']);
                                activeTidArr.value.push(taxon['tid']);
                                if(!activeFamilyArr.value.includes(taxon['family'])){
                                    activeFamilyArr.value.push(taxon['family']);
                                }
                            }
                            if(taxon['keyData'].length > 0){
                                taxon['keyData'].forEach(keyData => {
                                    if(!csidArr.value.includes(keyData['csid'])){
                                        csidArr.value.push(keyData['csid']);
                                    }
                                });
                            }
                        });
                        setTaxaDisplayData();
                        setKeyData();
                    }

                    function setActiveCidArr() {
                        characterDependencyDataArr.value.forEach(character => {
                            if(character['dependencies'].length > 0){
                                let active = false;
                                character['dependencies'].forEach(dep => {
                                    if(!active){
                                        if(Number(dep['csid']) === 0){
                                            if(selectedCidArr.value.includes(Number(dep['cid']))){
                                                active = true;
                                            }
                                        }
                                        else{
                                            if(selectedCsidArr.value.includes(Number(dep['csid']))){
                                                active = true;
                                            }
                                        }
                                    }
                                });
                                if(active && !activeCidArr.value.includes(Number(character['cid']))){
                                    activeCidArr.value.push(Number(character['cid']));
                                }
                                else if(!active){
                                    if(activeCidArr.value.includes(Number(character['cid']))){
                                        const index = activeCidArr.value.indexOf(Number(character['cid']));
                                        activeCidArr.value.splice(index, 1);
                                    }
                                    if(selectedCidArr.value.includes(Number(character['cid']))){
                                        const targetStateArr = selectedStateArr.value.filter((state) => Number(state['cid']) === Number(character['cid']));
                                        targetStateArr.forEach(state => {
                                            const index = selectedStateArr.value.indexOf(state);
                                            selectedStateArr.value.splice(index, 1);
                                        });
                                    }
                                }
                            }
                            else if(!activeCidArr.value.includes(Number(character['cid']))){
                                activeCidArr.value.push(Number(character['cid']));
                            }
                        });
                    }

                    function setActiveTaxa() {
                        const newActiveFamilyArr = [];
                        const newActiveTidArr = [];
                        taxaDataArr.value.forEach(taxon => {
                            const cidArr = [];
                            let includeTaxon = true;
                            taxon['keyData'].forEach(char => {
                                if(includeTaxon && selectedCidArr.value.includes(Number(char['cid'])) && !selectedCsidArr.value.includes(Number(char['csid']))){
                                    includeTaxon = false;
                                }
                                else if(!cidArr.includes(Number(char['cid']))){
                                    cidArr.push(Number(char['cid']));
                                }
                            });
                            selectedCidArr.value.forEach(cid => {
                                if(!cidArr.includes(Number(cid))){
                                    includeTaxon = false;
                                }
                            });
                            if(includeTaxon){
                                newActiveTidArr.push(taxon['tid']);
                                if(!newActiveFamilyArr.includes(taxon['family'])){
                                    newActiveFamilyArr.push(taxon['family']);
                                }
                            }
                        });
                        activeFamilyArr.value = newActiveFamilyArr.slice();
                        activeTidArr.value = newActiveTidArr.slice();
                    }

                    function setChecklistData() {
                        const formData = new FormData();
                        formData.append('clid', clId.value.toString());
                        formData.append('action', 'getChecklistData');
                        fetch(checklistApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((data) => {
                                checklistData.value = Object.assign({}, data);
                                clidArr.value = checklistData.value['clidArr'].slice();
                                setTaxaData();
                            });
                    }

                    function setKeyData() {
                        const formData = new FormData();
                        formData.append('csidArr', JSON.stringify(csidArr.value));
                        formData.append('includeFullKeyData', '1');
                        formData.append('action', 'getKeyCharacterStatesArr');
                        fetch(keyCharacterStateApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((data) => {
                                processKeyData(data);
                            });
                    }

                    function setProjectData() {
                        const formData = new FormData();
                        formData.append('pid', pId.value.toString());
                        formData.append('action', 'getProjectData');
                        fetch(projectApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((data) => {
                                projectData.value = Object.assign({}, data);
                                clidArr.value = Object.values(projectData.value['clidArr']).slice();
                                setTaxaData();
                            });
                    }

                    function setQueryPopupDisplay(val) {
                        displayQueryPopup.value = val;
                    }

                    function setTaxaData() {
                        const formData = new FormData();
                        formData.append('clidArr', JSON.stringify(clidArr.value));
                        formData.append('includeKeyData', '1');
                        formData.append('action', 'getChecklistTaxa');
                        fetch(checklistTaxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((data) => {
                                taxaDataArr.value = data;
                                processTaxaData();
                            });
                    }

                    function setTaxaDisplayData() {
                        const newDataArr = [];
                        taxaDataArr.value.forEach(taxon => {
                            if(selectedSortByOption.value === 'family'){
                                const familyObj = newDataArr.find(family => family['familyName'] === taxon['family']);
                                if(familyObj){
                                    familyObj['taxa'].push(taxon);
                                }
                                else{
                                    const taxaArr = [taxon];
                                    newDataArr.push({
                                        familyName: taxon['family'],
                                        taxa: taxaArr
                                    });
                                }
                            }
                            else{
                                newDataArr.push(taxon);
                            }
                        });
                        if(selectedSortByOption.value === 'family'){
                            newDataArr.sort((a, b) => {
                                return a['familyName'].localeCompare(b['familyName']);
                            });
                            newDataArr.forEach(family => {
                                family['taxa'].sort((a, b) => {
                                    return a['sciname'].localeCompare(b['sciname']);
                                });
                            });
                        }
                        else{
                            newDataArr.sort((a, b) => {
                                return a['sciname'].localeCompare(b['sciname']);
                            });
                        }
                        taxaDisplayDataArr.value = newDataArr.slice();
                    }

                    Vue.onMounted(() => {
                        if(Number(clId.value) > 0){
                            setChecklistData();
                        }
                        else if(Number(pId.value) > 0){
                            setProjectData();
                        }
                        else{
                            if(Number(queryId) === 0){
                                displayQueryPopup.value = true;
                            }
                            searchStore.initializeSearchStorage(queryId);
                        }
                    });

                    return {
                        activeChidArr,
                        activeCidArr,
                        activeFamilyArr,
                        activeTidArr,
                        checklistData,
                        checklistName,
                        clId,
                        clientRoot,
                        displayCommonNamesVal,
                        displayImagesVal,
                        displayQueryPopup,
                        keyDataArr,
                        languageArr,
                        pId,
                        popupWindowType,
                        projectData,
                        projectName,
                        selectedCsidArr,
                        selectedSortByOption,
                        selectedStateArr,
                        showSpatialPopup,
                        sortByOptions,
                        spatialInputValues,
                        taxaCount,
                        taxaDisplayDataArr,
                        buildChecklist,
                        closeSpatialPopup,
                        openSpatialPopup,
                        processCharacterStateSelectionChange,
                        processDisplayCommonNameChange,
                        processDisplayImagesChange,
                        processSortByChange,
                        processSpatialData,
                        setQueryPopupDisplay
                    }
                }
            });
            keyIdentificationModule.use(Quasar, { config: {} });
            keyIdentificationModule.use(Pinia.createPinia());
            keyIdentificationModule.mount('#mainContainer');
        </script>
    </body>
</html>
