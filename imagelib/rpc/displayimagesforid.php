<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageExplorer.php');

    $imageExplorer = new ImageExplorer();
    $imgArr = $imageExplorer->getImages($_POST);

                echo '<div style="clear:both;">';
                echo '<input type="hidden" id="imgCnt" value="'.$imgArr['cnt'].'" />';

                unset($imgArr['cnt']);

                foreach($imgArr as $imgArr){
                    $imgId = $imgArr['imgid'];
                    $imgUrl = $imgArr['url'];
                    $imgTn = $imgArr['thumbnailurl'];
                    if($imgTn){
                        $imgUrl = $imgTn;
                        if($IMAGE_DOMAIN && substr($imgTn,0,1)=='/'){
                            $imgUrl = $IMAGE_DOMAIN.$imgTn;
                        }
                    }
                    elseif($IMAGE_DOMAIN && substr($imgUrl,0,1)=='/'){
                        $imgUrl = $IMAGE_DOMAIN.$imgUrl;
                    }
    ?>

                    <div class="tndiv" style="margin-top: 15px; margin-bottom: 15px">
                        <div class="tnimg">
                            <a href="imgdetails.php?imgid=<?php echo $imgId; ?>">
                                <img src="<?php echo $imgUrl; ?>" />
                            </a>
                        </div>
                        <div>
                            <?php echo "<a href=\"../collections/editor/occurrenceeditor.php?q_customtypeone=EQUALS&occid=". $imgArr['occid']. "\">". $imgArr['instcode'] . " - " . $imgArr['catalognumber'].  "<br />" . $imgArr['sciname'] . "<br />" . $imgArr['stateprovince']; ?>
                            </a>
                        </div>
                    </div>
                <?php
                }
                echo "</div>";
?>
