<?php
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
