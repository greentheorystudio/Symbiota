<?php
ob_start();
if($descArr = $taxonManager->getDescriptions()){
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
                    $descArr = $vArr['desc'];
                    ?>
                    <div style="clear:both;">
                        <?php
                        foreach($descArr as $tdsId => $stmt){
                            echo $stmt. '<br /><br />';
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
$IRLDescTabsDiv = ob_get_clean();
