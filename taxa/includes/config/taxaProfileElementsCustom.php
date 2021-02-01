<?php
ob_start();
if($taxonRank > 180){
    ?>
    <div id="scinameheader" class="<?php echo $styleClass; ?>">
        <span id="sciname" class="<?php echo $styleClass; ?>">
            <i><?php echo $spDisplay; ?></i>
        </span>
        <?php echo $taxonManager->getAuthor(); ?>
        <?php
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
        if($taxonRank == 180) {
            $displayName = '<i>' . $displayName . '</i> ';
        }
        echo "<span id='sciname' class='".$styleClass."' >".$displayName."</span>";
        echo $taxonManager->getAuthor();
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
$starrValue = '{"usethes":true,"taxontype":"1","taxa":"' . $taxonManager->getSciName() . '"}';
echo '<div>';
echo '<form style="display:none;" name="occurrences" id="occurrences" action="../collections/list.php" method="post">';
echo "<input type='hidden' name='starr' value='" . $starrValue . "' />";
echo '<input type="hidden" name="page" value="1" />';
echo '</form>';
echo '<button id="taxscpecrecordbutton" type="button" onclick="document.getElementById(\'occurrences\').submit();">Specimen Records</button>';
echo '</div>';
echo '</div>';
echo '<div id="penaRightButtonDiv">';
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
    elseif($imgObj['sciname']){
        echo '<i>'.$imgObj['sciname'].'</i>&nbsp;&nbsp;';
    }
    echo '</div>';
    echo '</div>';
}
echo '</div>';
echo '<div id="penacentralimagerightcolumn">';
echo "<div class='penaspecimenimage'>";
$imgObj = current($specimenImageArr);
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
elseif($imgObj['sciname']){
    echo '<i>'.$imgObj['sciname'].'</i>&nbsp;&nbsp;';
}
echo '</div>';
echo '</div>';
$url = '';
$aUrl = '';
$gAnchor = '';
if($OCCURRENCE_MOD_IS_ACTIVE && $displayLocality){
    $gAnchor = "openMapPopup('".$taxonManager->getTid()."',".($taxonManager->getClid()?:0). ')';
}
if($mapSrc = $taxonManager->getMapArr()){
    $url = array_shift($mapSrc);
    $aUrl = $url;
}
elseif($gAnchor){
    $url = $taxonManager->getGoogleStaticMap();
}
if($url){
    echo '<div class="mapthumb">';
    if($gAnchor){
        echo '<a href="#" onclick="'.$gAnchor.';return false">';
    }
    elseif($aUrl){
        echo '<a href="'.$aUrl.'">';
    }
    echo '<img src="'.$url.'" title="'.$spDisplay.'" alt="'.$spDisplay.'" />';
    if($aUrl || $gAnchor) {
        echo '</a>';
    }
    if($gAnchor) {
        echo '<br /><a href="#" onclick="' . $gAnchor . ';return false">Open Interactive Map</a>';
    }
    echo '</div>';
}
echo '</div>';
echo '</div>';
$penaCentralImageDiv = ob_get_clean();
