<?php
ob_start();
if($descArr = $taxonManager->getDescriptions()){
    $glossaryArr = $taxonManager->getGlossary();
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
            if($glossaryArr){
                echo '<li><a href="#tabglossary" class="taxon-desc-tab-text">Glossary</a></li>';
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
                            echo $stmt;
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            if($glossaryArr){
                ?>
                <div id="tabglossary" class="<?php echo ($styleClass === 'species'?'sptab':'spptab'); ?>">
                    <div style="clear:both;">
                        <?php
                        foreach($glossaryArr as $termId => $termArr){
                            echo '<b>'.$termArr['term'].'</b>: ' .$termArr['definition']. '<br /><br />';
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

ob_start();
if($vernStr){
    ?>
    <div style="display:block;width:325px;margin-left:10px;margin-top:0.5em;"><b>Common names:</b> <?php echo $vernStr; ?></div>
    <?php
}
$IRLVernacularsDiv = ob_get_clean();

ob_start();
if($synStr){
    ?>
    <div style="display:block;width:325px;margin-left:10px;margin-top:0.5em;" title="Synonyms"><b>Synonyms:</b> <?php echo $synStr; ?></div>
    <?php
}
$IRLSynonymsDiv = ob_get_clean();
