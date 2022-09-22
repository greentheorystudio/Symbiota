<?php
include_once(__DIR__ . '/../../classes/EOLManager.php');

$submitAction = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';
$statusStr = array_key_exists('status',$_REQUEST)?$_REQUEST['status']:'';

$eolManager = new EOLManager();
?>
<div id="innertext">
    <?php
    if($statusStr){
        ?>
        <hr/>
        <div style="color:red;margin:15px;">
            <?php echo $statusStr; ?>
        </div>
        <hr/>
        <?php
    }
    if($submitAction){
        ?>
        <hr/>
        <div style="margin:15px;">
            <?php
            if($submitAction === 'Map Taxa'){
                $makePrimary = 0;
                $restart = 0;
                if(array_key_exists('makeprimary',$_POST) && $_POST['makeprimary']){
                    $makePrimary = 1;
                }
                if(array_key_exists('restart',$_POST) && $_POST['restart']){
                    $restart = 1;
                }
                $eolManager->mapTaxa($makePrimary,$_POST['tidstart'],$restart);
            }
            elseif($submitAction === 'Map Images'){
                $restart = 0;
                if(array_key_exists('restart',$_POST) && $_POST['restart']){
                    $restart = 1;
                }
                $eolManager->mapImagesForTaxa($_POST['startindex'],$restart);
            }
            ?>
        </div>
        <hr/>
        <?php
    }
    ?>
    <div style="color:red;margin:15px;">
        Note: these processes may take a great deal of time to complete
    </div>
    <div style="margin:15px;">
        <fieldset style="padding:15px;">
            <legend><b>Taxa Mapping</b></legend>
            <div>
                This module will query EOL for all accepted taxa that do not currently have an EOL link nor identifier assignment.
                If an EOL taxon object is found, a link to EOL will be created for that taxon.
            </div>
            <div style="margin:10px;">
                Number of taxa not mapped to EOL:
                <b><?php echo $eolManager->getEmptyIdentifierCount(); ?></b>
                <div style="margin:10px;">
                    <form name="taxamappingform" action="index.php" method="post">
                        <input type="submit" name="submitaction" value="Map Taxa" />
                        <div style="margin:15px;">
                            TID Start Index: <input type="text" name="tidstart" value="" /><br />
                            <input type="checkbox" name="restart" value="1" CHECKED /> Restart where left off within the last week<br />
                            <input type="checkbox" name="makeprimary" value="1" CHECKED /> Make EOL primary link (sort order = 1)
                        </div>
                        <input type="hidden" name="tabindex" value="1" />
                    </form>
                </div>
            </div>
        </fieldset>
        <fieldset style="margin-top:15px;padding:15px;">
            <legend><b>Image Mapping</b></legend>
            <div>
                This module will query the EOL image library for all accepted taxa currently linked to EOL
                that do not have any field images.
                Up to 5 images will be automatically linked in the mapping procedure.
            </div>
            <div style="margin:10px;">
                Number of accpeted taxa without images:
                <b><?php echo $eolManager->getImageDeficiencyCount(); ?></b>
                <div style="margin:10px;">
                    <form name="imagemappingform" action="index.php" method="post">
                        TID Start Index: <input type="text" name="startindex" value="" /><br/>
                        <input type="checkbox" name="restart" value="1" CHECKED /> Restart where left off within the last week<br />
                        <input type="hidden" name="tabindex" value="1" />
                        <input type="submit" name="submitaction" value="Map Images" />
                    </form>
                </div>
            </div>
        </fieldset>
    </div>
</div>
