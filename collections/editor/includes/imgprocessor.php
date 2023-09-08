<?php
/** @var string $occId */
/** @var string $collId */
/** @var string $occIndex */
/** @var string $crowdSourceMode */
/** @var array $occArr */
?>
<script>
$(function() {
	$( "#zoomInfoDialog" ).dialog({
		autoOpen: false,
		position: { my: "left top", at: "right bottom", of: "#zoomInfoDiv" }
	});
	
	$( "#zoomInfoDiv" ).click(function() {
		$( "#zoomInfoDialog" ).dialog( "open" );
	});
});
</script>
<div style="width:100%;height:1050px;">
    <fieldset style="height:95%;background-color:white;">
        <legend><b>Label Processing</b></legend>
        <div style="margin-top:-10px;height:15px;position:relative">
            <div style="float:left;padding-top:3px"><a id="zoomInfoDiv" href="#">Zoom?</a></div>
            <div id="zoomInfoDialog">
                Hold down control button and click on the image to quick zoom into specific location
                or hold down the shift button and hold a left-click while moving the mouse up or down for a more controlled zoom action.
                Click and drag bottom right corner of image to resize display panel.
            </div>
            <?php
            reset($imgArr);
            ?>
        </div>
        <div id="labelprocessingdiv" style="clear:both;">
            <?php
            $imgCnt = 1;
            foreach($imgArr as $imgCnt => $iArr){
                $iUrl = $iArr['web'];
                $imgId = $iArr['imgid'];
                ?>
                <div id="labeldiv-<?php echo $imgCnt; ?>" style="display:<?php echo ($imgCnt === 1?'block':'none'); ?>;">
                    <div>
                        <img id="activeimg-<?php echo $imgCnt; ?>" src="<?php echo $iUrl; ?>" style="width:400px;height:400px" />
                    </div>
                    <?php
                    if(array_key_exists('error', $iArr)){
                        echo '<div style="font-weight:bold;color:red">'.$iArr['error'].'</div>';
                    }
                    ?>
                    <div style="width:100%;clear:both;">
                        <div style="float:right;margin-right:20px;font-weight:bold;">
                            Image <?php echo $imgCnt; ?> of
                            <?php
                            echo count($imgArr);
                            if(count($imgArr)>1){
                                echo '<a href="#" onclick="return nextLabelProcessingImage('.($imgCnt+1). ')">=&gt;&gt;</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $imgCnt++;
            }
            ?>
        </div>
    </fieldset>
</div>
