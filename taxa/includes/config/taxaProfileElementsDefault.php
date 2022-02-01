<?php
/** @var TaxonProfileManager $taxonManager */
/** @var int $taxonRank */
/** @var string $styleClass */
/** @var string $spDisplay */
/** @var string $projValue */
/** @var string $ambiguous */
/** @var array $synonymArr */
/** @var string $acceptedName */
/** @var array $links */
/** @var string $clValue */
/** @var string $lang */
/** @var string $vernStr */
/** @var string $synStr */
/** @var boolean $isEditor */
/** @var int $displayLocality */
/** @var string $showAllImages */
/** @var string $taxonValue */
ob_start();
$isTaxonEditor = false;
if($GLOBALS['SYMB_UID']){
    if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
        $isTaxonEditor = true;
    }
}
if($isTaxonEditor){
    ?>
    <div id="editbutton">
        <a href="admin/tpeditor.php?tid=<?php echo $taxonManager->getTid(); ?>" <?php echo 'title="Edit Taxon Data"'; ?>>
            <i style="height:20px;width:20px;" class="far fa-edit"></i>
        </a>
    </div>
    <?php
}
$editButtonDiv = ob_get_clean();

ob_start();
if($taxonRank > 180){
    ?>
    <div id="scinameheader" class="<?php echo $styleClass; ?>">
        <span id="sciname" class="<?php echo $styleClass; ?>">
            <i><?php echo $spDisplay; ?></i>
        </span>
        <?php echo $taxonManager->getAuthor(); ?>
        <?php
        $parentLink = 'index.php?taxon=' .$taxonManager->getParentTid(). '&cl=' .$taxonManager->getClid(). '&proj=' .$projValue;
        echo "<a href='".$parentLink."'><i id='parenttaxonicon' style='height:15px;width:15px;' title='Go to Parent' class='fas fa-level-up-alt'></i></a>";
        if($taxonManager->getTid() !== $taxonManager->getSubmittedTid()){
            echo '<span id="redirectedfrom"> (redirected from: <i>'.$taxonManager->getSubmittedSciName().'</i>)</span>';
        }
        ?>
    </div>
    <?php
}
else{
    ?>
    <div id="scinameheader" class="<?php echo $styleClass; ?>">
        <?php
        $displayName = $spDisplay;
        if($taxonRank === 180) {
            $displayName = '<i>' . $displayName . '</i>';
        }
        $displayName .= ' ' . $taxonManager->getAuthor().' ';
        if($taxonRank > 140){
            $parentLink = 'index.php?taxon=' .$taxonManager->getParentTid(). '&cl=' .$taxonManager->getClid(). '&proj=' .$projValue;
            $displayName .= ' <a href="'.$parentLink.'">';
            $displayName .= '<i id="parenttaxonicon" style="height:15px;width:15px;" title="Go to Parent" class="fas fa-level-up-alt"></i>';
            $displayName .= '</a>';
        }
        echo "<div id='sciname' class='".$styleClass."' >".$displayName. '</div> ';
        ?>
    </div>
    <?php
}
$scinameHeaderDiv = ob_get_clean();

ob_start();
if($ambiguous){
    $synLinkStr = '';
    $explanationStr = '';
    foreach($synonymArr as $synTid => $sName){
        $synLinkStr .= '<a href="index.php?taxon='.$synTid.'&cl='.$clValue.'&proj='.$projValue.'&lang='.$lang.'">'.$sName.'</a>, ';
    }
    $synLinkStr = substr($synLinkStr,0,-2);
    if($acceptedName){
        $explanationStr = 'This name is accepted, but is also synonymized with the following taxa: ';
    }
    else{
        $explanationStr = 'This name is not accepted, but is synonymized with the following taxa: ';
    }
    echo "<div id='ambiguous'>";
    echo $explanationStr.$synLinkStr;
    echo '</div>';
}
$ambiguousDiv = ob_get_clean();

ob_start();
if($links && (int)$links[0]['sortseq'] === 1){
    $uStr = str_replace('--SCINAME--',rawurlencode($taxonManager->getSciName()),$links[0]['url']);
    ?>
    <div id="weblinks">
        Go to <a href="<?php echo $uStr; ?>" target="_blank"><?php echo $links[0]['title']; ?></a>...
    </div>
    <?php
}
$webLinksDiv = ob_get_clean();

ob_start();
if($taxonRank > 140){
    $family = $taxonManager->getFamily();
    if($family){
        ?>
        <div id="family" class="<?php echo $styleClass; ?>">
            <?php echo '<b>Family:</b> '.$taxonManager->getFamily(); ?>
        </div>
        <?php
    }
}
$familyDiv = ob_get_clean();

ob_start();
$notes = $taxonManager->getTaxonNotes();
if($notes){
    ?>
    <div id="taxonnotes" class="<?php echo $styleClass; ?>">
        <?php echo '<b>Notes:</b> '.$notes; ?>
    </div>
    <?php
}
$taxonNotesDiv = ob_get_clean();

ob_start();
$sources = $taxonManager->getTaxonSources();
if($sources){
    ?>
    <div id="taxonsource" class="<?php echo $styleClass; ?>">
        <?php echo '<b>Source:</b> '.$sources; ?>
    </div>
    <?php
}
$taxonSourcesDiv = ob_get_clean();

ob_start();
if($projValue){
    ?>
    <div id='project'><b>Project:</b> <?php echo $taxonManager->getProjName(); ?></div>
    <?php
}
$projectDiv = ob_get_clean();

ob_start();
if($vernStr){
    ?>
    <div id="vernaculars"><?php echo $vernStr; ?></div>
    <?php
}
$vernacularsDiv = ob_get_clean();

ob_start();
if($synStr){
    ?>
    <div id="synonyms" title="Synonyms">[<?php echo $synStr; ?>]</div>
    <?php
}
$synonymsDiv = ob_get_clean();

ob_start();
if(!$taxonManager->echoImages(0,1,0)){
    echo '<div id="nocentralimage">';
    if($isEditor){
        echo '<a href="admin/tpeditor.php?category=imageadd&tid='.$taxonManager->getTid().'"><b>Add an Image</b></a>';
    }
    else{
        echo 'Images<br/>not available';
    }
    echo '</div>';
}
$centralImageDiv = ob_get_clean();

ob_start();
if($descArr = $taxonManager->getDescriptions()){
    if(isset($GLOBALS['PORTAL_TAXA_DESC'])){
        $tempArr = array();
        $descIndex = 0;
        foreach($descArr as $dArr){
            foreach($dArr as $id => $vArr){
                if($vArr['caption'] === $GLOBALS['PORTAL_TAXA_DESC']){
                    if($descArr[$descIndex]){
                        $tempArr = $descArr[$descIndex][$id];
                        unset($descArr[$descIndex][$id]);
                        array_unshift($descArr[$descIndex],$tempArr);
                    }
                    $descIndex++;
                }
            }
        }
    }
    ?>
    <div id="desctabs" class="ui-tabs <?php echo $styleClass; ?>">
        <ul class="ui-tabs-nav">
            <?php
            $capCnt = 1;
            foreach($descArr as $dArr){
                foreach($dArr as $id => $vArr){
                    $cap = $vArr['caption'];
                    if(!$cap){
                        $cap = 'Description #'.$capCnt;
                        $capCnt++;
                    }
                    echo '<li><a href="#tab'.$id.'" class="taxon-desc-tab-text">'.$cap.'</a></li>';
                }
            }
            ?>
        </ul>
        <?php
        foreach($descArr as $dArr){
            foreach($dArr as $id => $vArr){
                ?>
                <div id="tab<?php echo $id; ?>" class="<?php echo ($styleClass === 'species'?'sptab':'spptab'); ?>">
                    <?php
                    if($vArr['source']){
                        echo '<div id="descsource">';
                        if($vArr['url']){
                            echo '<a href="'.$vArr['url'].'" target="_blank">';
                        }
                        echo $vArr['source'];
                        if($vArr['url']){
                            echo '</a>';
                        }
                        echo '</div>';
                    }
                    $sdescArr = $vArr['desc'];
                    ?>
                    <div style="clear:both;">
                        <?php
                        foreach($sdescArr as $tdsId => $stmt){
                            echo $stmt. ' ';
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php
}
else{
    echo '<div id="nodesc">Description Not Yet Available</div>';
}
$descTabsDiv = ob_get_clean();

ob_start();
$url = '';
$mAnchor = '';
if($GLOBALS['OCCURRENCE_MOD_IS_ACTIVE'] && $displayLocality){
    $mAnchor = "openMapPopup('".$taxonManager->getSciName()."',true)";
    if($mapSrc = $taxonManager->getMapArr()){
        $url = array_shift($mapSrc);
    }
    echo '<div class="mapthumb">';
    if($url){
        echo '<a href="#" onclick="'.$mAnchor.';return false">';
        echo '<a href="'.$url.'">';
        echo '<img src="'.$url.'" title="'.$spDisplay.'" alt="'.$spDisplay.'" />';
        echo '</a>';
    }
    echo '<br /><a href="#" onclick="' . $mAnchor . ';return false">Open Interactive Map</a>';
    echo '</div>';
}
$mapThumbDiv = ob_get_clean();

ob_start();
?>
<div id="img-div">
    <?php
    if(!$showAllImages && ($taxonManager->getImageCount() > 100)){
        $taxonManager->echoImages(1, 100);
    }
    else{
        $taxonManager->echoImages(1);
    }
    ?>
</div>
<?php
$imgDiv = ob_get_clean();

ob_start();
?>
<div id="img-tab-div" style="display:<?php echo ((($taxonManager->getImageCount() > 6) && !$showAllImages)?'block':'none');?>;">
    <?php
    if($taxonManager->getImageCount() > 100){
        if($taxonRank < 140){
            $taxonType = 4;
        }
        else{
            $taxonType = 2;
        }
        ?>
        <div id="img-tab-expand">
            <a href="#" onclick="expandExtraImages();return false;">
                <?php echo 'Click to Display<br/>100 Initial Images'; ?>
            </a><br/>
            - - - - -<br/>
            <a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"taxontype":"<?php echo $taxonType; ?>","taxa":"<?php echo $taxonManager->getSciName(); ?>"}' target="_blank">
                <?php echo 'View All '.$taxonManager->getImageCount().' Images'; ?>
            </a>
        </div>
        <?php
    }
    else{
        ?>
        <div id="img-tab-expand">
            <a href="#" onclick="expandExtraImages();return false;">
                <?php echo 'View All '.$taxonManager->getImageCount().' Images'; ?>
            </a>
        </div>
        <?php
    }
    ?>
</div>
<?php
$imgTabDiv = ob_get_clean();

ob_start();
?>
<div id="imagebox">
    <?php
    if($clValue){
        echo '<legend>';
        echo 'Species within <b>'.$taxonManager->getClName().'</b>&nbsp;&nbsp;';
        if($taxonManager->getParentClid()){
            echo '<a href="index.php?taxon='.$taxonValue.'&cl='.$taxonManager->getParentClid().'" title="Go to '.$taxonManager->getParentName().' checklist"><i id="parenttaxonicon" style="height:15px;width:15px;" title="Go to Parent" class="fas fa-level-up-alt"></i></a>';
        }
        echo '</legend>';
    }
    ?>
    <div>
        <?php
        if($sppArr = $taxonManager->getSppArray()){
            echo "<div class='flexwrapbox'>";
            $cnt = 0;
            ksort($sppArr);
            foreach($sppArr as $sciNameKey => $subArr){
                echo "<div class='spptaxon'>";
                echo "<div class='spptaxonbox'>";
                echo "<a href='index.php?taxon=".$subArr['tid'].($clValue? '&cl=' .$clValue: '')."'>";
                echo '<i>' .$sciNameKey. '</i>';
                echo "</a></div>\n";
                echo "<div class='sppimg'>";

                if(array_key_exists('url',$subArr)){
                    $imgUrl = $subArr['url'];
                    echo "<a href='index.php?taxon=".$subArr['tid'].($clValue? '&cl=' .$clValue: '')."'>";
                    if($subArr['thumbnailurl']){
                        $imgUrl = $subArr['thumbnailurl'];
                    }
                    if(strncmp($imgUrl, '/', 1) === 0) {
                        if($GLOBALS['IMAGE_DOMAIN']){
                            $imgUrl = $GLOBALS['IMAGE_DOMAIN'] . $imgUrl;
                        }
                        else{
                            $imgUrl = $GLOBALS['CLIENT_ROOT'] . $imgUrl;
                        }
                    }
                    echo '<img class="taxonimage" src="'.$imgUrl.'" title="'.$subArr['caption'].'" alt="Image of '.$sciNameKey.'" />';
                    echo '</a>';
                    echo '<div id="imgphotographer" title="Photographer: '.$subArr['photographer'].'">';
                    echo '</div>';
                }
                elseif($isEditor){
                    echo '<div class="spptext"><a href="admin/tpeditor.php?category=imageadd&tid='.$subArr['tid'].'">Add an Image</a></div>';
                }
                else{
                    echo '<div class="spptext">Images<br/>not available</div>';
                }
                echo "</div>\n";

                echo '<div class="sppmap">';
                if(array_key_exists('map',$subArr) && $subArr['map']){
                    $mapUrl = $subArr['map'];
                    if(strncmp($mapUrl, '/', 1) === 0) {
                        if($GLOBALS['IMAGE_DOMAIN']){
                            $mapUrl = $GLOBALS['IMAGE_DOMAIN'] . $mapUrl;
                        }
                        else{
                            $mapUrl = $GLOBALS['CLIENT_ROOT'] . $mapUrl;
                        }
                    }
                    echo '<img src="'.$mapUrl.'" title="'.$spDisplay.'" alt="'.$spDisplay.'" />';
                }
                elseif($taxonManager->getRankId()>140){
                    echo '<div class="spptext">Map not<br />Available</div>';
                }
                echo '</div>';

                echo '</div>';
                $cnt++;
            }
            echo '</div>';
        }
        ?>
        <div class="clear"><hr></div>
    </div>
</div>
<?php
$imgBoxDiv = ob_get_clean();

ob_start();
echo '<div id="footerlinkstoggle">';
if($taxonRank > 180 && $links) {
    echo '<a href="#" onclick="toggleLinks(\'links\');return false;">Web Links</a>';
}

if($taxonRank > 140){
    $parentLink = 'index.php?taxon=' .$taxonManager->getParentTid();
    if($clValue) {
        $parentLink .= '&cl=' . $taxonManager->getClid();
    }
    if($projValue) {
        $parentLink .= '&proj=' . $projValue;
    }
    echo '<a href="'.$parentLink.'" class="parentlink">View Parent Taxon</a>';
}
echo '</div>';

if($taxonRank > 180 && $links){
    echo '<div id="links" style="display:none;"><h1 id="linksbanner">Web Links</h1><ul id="linkslist">';
    foreach($links as $l){
        $urlStr = str_replace('--SCINAME--',rawurlencode($taxonManager->getSciName()),$l['url']);
        echo '<li><a href="'.$urlStr.'" target="_blank">'.$l['title'].'</a></li>';
        if($l['notes']) {
            echo ' ' . $l['notes'];
        }
    }
    echo "</ul>\n</div>";
}
$footerLinksDiv = ob_get_clean();

ob_start();
?>
<div id="notfoundbox">
    <?php
    if(is_numeric($taxonValue)){
        echo '<h1>Illegal identifier submitted: '.$taxonValue.'</h1>';
    }
    else{
        ?>
        <h1><?php echo '<i>'.$taxonValue.'</i> not found'; ?></h1>
        <?php
        if($matchArr = $taxonManager->getCloseTaxaMatches($taxonValue)){
            ?>
            <div id="suggestionbox">
                Did you mean?
                <div id="suggestionlist">
                    <?php
                    foreach($matchArr as $t => $n){
                        echo '<a href="index.php?taxon='.$t.'">'.$n.'</a><br/>';
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>
<?php
$notFoundDiv = ob_get_clean();
?>
