<?php
ob_start();
$url = '';
$mAnchor = '';
if($displayLocality){
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
    if($taxonManager->getImageCount() > 100){
        $taxonManager->echoImages(0, 100);
    }
    else{
        $taxonManager->echoImages(0);
    }
    ?>
</div>
<?php
$imgDiv = ob_get_clean();

ob_start();
?>
<div id="img-tab-div" style="clear:both;display:<?php echo ($taxonManager->getImageCount() > 0?'block':'none');?>;">
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
                        if(isset($GLOBALS['IMAGE_DOMAIN'])){
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
                    echo '<div class="spptext"><a href="profile/tpeditor.php?category=imageadd&tid='.$subArr['tid'].'">Add an Image</a></div>';
                }
                else{
                    echo '<div class="spptext">Images<br/>not available</div>';
                }
                echo "</div>\n";

                echo '<div class="sppmap">';
                if(array_key_exists('map',$subArr) && $subArr['map']){
                    $mapUrl = $subArr['map'];
                    if(strncmp($mapUrl, '/', 1) === 0) {
                        if(isset($GLOBALS['IMAGE_DOMAIN'])){
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
