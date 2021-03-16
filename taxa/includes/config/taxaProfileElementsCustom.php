<?php
ob_start();
if($taxonRank > 180){
    ?>
    <div id="scinameheader" class="<?php echo $styleClass; ?>">
        <span id="sciname" class="<?php echo $styleClass; ?>">
            <i><?php echo $spDisplay; ?></i>
        </span>
        <?php
        echo $taxonManager->getAuthor();
        $parentLink = 'index.php?taxon=' .$taxonManager->getParentTid(). '&cl=' .$taxonManager->getClid(). '&proj=' .$projValue. '&taxauthid=' .$taxAuthId;
        echo ' <a href="'.$parentLink.'">';
        echo '<img id="parenttaxonicon" src="../images/toparent.png" title="Go to Parent" />';
        echo '</a>';
        if($taxAuthId && ($taxonManager->getTid() !== $taxonManager->getSubmittedTid())){
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
            $displayName = '<i>' . $displayName . '</i> ';
        }
        echo "<span id='sciname' class='".$styleClass."' >".$displayName."</span>";
        echo $taxonManager->getAuthor();
        if($taxonRank > 140){
            $parentLink = 'index.php?taxon=' .$taxonManager->getParentTid(). '&cl=' .$taxonManager->getClid(). '&proj=' .$projValue. '&taxauthid=' .$taxAuthId;
            echo ' <a href="'.$parentLink.'">';
            echo '<img id="parenttaxonicon" src="../images/toparent.png" title="Go to Parent" />';
            echo '</a>';
        }
        ?>
    </div>
    <?php
}
$penaScinameHeaderDiv = ob_get_clean();

ob_start();
if($vernArr){
    echo '<div id="vernaculars"><span class="verns">'.implode(', ',$vernArr).'</span></div>';
}
$penaVernacularsDiv = ob_get_clean();

ob_start();
?>
    <div id="img-tab-div" style="display:<?php echo $taxonManager->getImageCount()> 6?'block':'none';?>;">
        <a href="#" onclick="expandExtraImages();return false;">
            <div id="img-tab-expand">
                <?php echo 'Click to View All Images'; ?>
            </div>
        </a>
    </div>
<?php
$penaImgTabDiv = ob_get_clean();


ob_start();
echo '<div id="penaButtonDiv">';
echo '<div id="penaLeftButtonDiv">';
if($taxonRank > 180 && $taxonManager->getNumChildren() > 0 && !$displayingChildren) {
    echo '<a href="index.php?taxon=' . $taxonManager->getTid() . '&displaychildren=1"><button id="taxinfrabutton" type="button">Infraspecific Taxa</button></a>';
}
echo '</div>';
echo '</div>';
$penaButtonsDiv = ob_get_clean();

ob_start();
$fieldImageArr = $taxonManager->getFilteredImageArr('field',2);
$specimenImageArr = $taxonManager->getFilteredImageArr('specimen',1);
echo '<div id="penacentralimage">';

echo '<div id="penacentralimageleftcolumn">';
foreach($fieldImageArr as $imgId => $imgObj){
    echo "<div class='penafieldimage'>";

    $imgUrl = $imgObj['url'];
    $imgAnchor = '../imagelib/imgdetails.php?imgid='.$imgId;
    $imgThumbnail = $imgObj['thumbnailurl'];
    if($IMAGE_DOMAIN){
        if(strpos($imgUrl, '/') === 0) {
            $imgUrl = $IMAGE_DOMAIN . $imgUrl;
        }
        if(strpos($imgThumbnail, '/') === 0) {
            $imgThumbnail = $IMAGE_DOMAIN . $imgThumbnail;
        }
    }
    if($imgObj['occid']){
        $imgAnchor = '../collections/individual/index.php?occid='.$imgObj['occid'];
    }
    if($imgObj['thumbnailurl']) {
        $imgUrl = $imgThumbnail;
    }
    echo '<div class="tptnimg"><a href="'.$imgAnchor.'">';
    $titleStr = $imgObj['caption'];
    if($imgObj['sciname'] !== $taxonManager->getSciName()) {
        $titleStr .= ' (linked from ' . $imgObj['sciname'] . ')';
    }
    echo '<img src="'.$imgUrl.'" title="'.$titleStr.'" alt="'.$spDisplay.' image" />';
    echo '</a></div>';
    echo '<div class="photographer">';
    if($imgObj['photographer']){
        echo $imgObj['photographer'].'&nbsp;&nbsp;';
    }
    elseif($imgObj['owner']){
        echo $imgObj['owner'].'&nbsp;&nbsp;';
    }
    echo '</div>';

    echo '</div>';
}
echo '</div>';

echo '<div id="penacentralimagerightcolumn">';
$imgObj = current($specimenImageArr);
if(is_array($imgObj)){
    echo "<div class='penaspecimenimage'>";

    $imgId = key($specimenImageArr);
    $imgUrl = $imgObj['url'];
    $imgAnchor = '../imagelib/imgdetails.php?imgid='.$imgId;
    $imgThumbnail = $imgObj['thumbnailurl'];
    if(strpos($imgUrl, '/') === 0) {
        if($IMAGE_DOMAIN){
            $imgUrl = $IMAGE_DOMAIN . $imgUrl;
        }
        else{
            $imgUrl = $CLIENT_ROOT . $imgUrl;
        }
    }
    if(strpos($imgThumbnail, '/') === 0) {
        if($IMAGE_DOMAIN){
            $imgThumbnail = $IMAGE_DOMAIN . $imgThumbnail;
        }
        else{
            $imgThumbnail = $CLIENT_ROOT . $imgThumbnail;
        }
    }
    if($imgObj['occid']){
        $imgAnchor = '../collections/individual/index.php?occid='.$imgObj['occid'];
    }
    if($imgObj['thumbnailurl']) {
        $imgUrl = $imgThumbnail;
    }
    echo '<div class="tptnimg"><a href="'.$imgAnchor.'">';
    $titleStr = $imgObj['caption'];
    if($imgObj['sciname'] !== $taxonManager->getSciName()) {
        $titleStr .= ' (linked from ' . $imgObj['sciname'] . ')';
    }
    echo '<img src="'.$imgUrl.'" title="'.$titleStr.'" alt="'.$spDisplay.' image" />';
    echo '</a></div>';
    echo '<div class="photographer">';
    if($imgObj['photographer']){
        echo $imgObj['photographer'].'&nbsp;&nbsp;';
    }
    elseif($imgObj['owner']){
        echo $imgObj['owner'].'&nbsp;&nbsp;';
    }
    echo '</div>';

    echo '</div>';
}
$url = '';
$mAnchor = '';
if($OCCURRENCE_MOD_IS_ACTIVE && $displayLocality){
    $mAnchor = "openMapPopup('".$taxonManager->getSciName()."',".($taxonManager->getClid()?:'0'). ')';
    if($mapSrc = $taxonManager->getMapArr()){
        $url = array_shift($mapSrc);
    }
    echo '<div class="mapthumb">';
    if($url){
        if(strpos($url, '/') === 0) {
            if($IMAGE_DOMAIN){
                $url = $IMAGE_DOMAIN . $url;
            }
            else{
                $url = $CLIENT_ROOT . $url;
            }
        }
        echo '<a href="#" onclick="'.$mAnchor.';return false">';
        echo '<a href="'.$url.'">';
        echo '<img src="'.$url.'" title="'.$spDisplay.'" alt="'.$spDisplay.'" />';
        echo '</a>';
    }
    echo '<br /><a href="#" onclick="' . $mAnchor . ';return false">Open Interactive Map</a>';
    echo '</div>';
}
echo '</div>';
echo '</div>';
$penaCentralImageDiv = ob_get_clean();

ob_start();
?>
    <div id="img-tab-div" style="display:<?php echo (!$showAllImages?'block':'none');?>;">
        <div style="display:flex;justify-content:space-around;align-items: center;">
            <?php
            echo '<div style="penaLeftButtonDiv">';
            $starrValue = '{"usethes":true,"taxontype":"1","taxa":"' . $taxonManager->getSciName() . '"}';
            echo '<div>';
            echo '<form style="display:none;" name="occurrences" id="occurrences" action="../collections/list.php" method="post">';
            echo "<input type='hidden' name='starr' value='" . $starrValue . "' />";
            echo '<input type="hidden" name="page" value="1" />';
            echo '</form>';
            echo '<button id="taxscpecrecordbutton" type="button" onclick="document.getElementById(\'occurrences\').submit();">Specimen Records</button>';
            echo '</div>';
            echo '</div>';
            if($taxonManager->getImageCount() > 100){
                ?>
                <div id="img-tab-expand">
                    <a href="#" onclick="expandExtraImages();return false;">
                        <?php echo 'Click to Display<br/>100 Initial Images'; ?>
                    </a><br/>
                    - - - - -<br/>
                    <a href='<?php echo $GLOBALS['CLIENT_ROOT']; ?>/imagelib/search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"taxontype":"2","taxa":"<?php echo $taxonManager->getSciName(); ?>"}' target="_blank">
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
    </div>
<?php
$penaImgTabDiv = ob_get_clean();

ob_start();
if($descArr = $taxonManager->getDescriptions(true)){
    if(isset($PORTAL_TAXA_DESC)){
        $tempArr = array();
        $descIndex = 0;
        foreach($descArr as $dArr){
            foreach($dArr as $id => $vArr){
                if($vArr['caption'] === $PORTAL_TAXA_DESC){
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
                    </div style="clear:both;">
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
$penaDescTabsDiv = ob_get_clean();
