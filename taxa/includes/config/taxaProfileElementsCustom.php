<?php
include_once(__DIR__ . '/../../../classes/IRLManager.php');

$IRLManager = new IRLManager();

$nativeStatusArr = $IRLManager->getNativeStatus($taxonManager->getTid());

ob_start();
if($nativeStatusArr){
    ?>
    <div style="display:block;width:325px;margin-left:10px;margin-top:0.5em;color:red;font-weight:bold;"><?php echo implode(',', $nativeStatusArr); ?></div>
    <?php
}
$IRLNativeStatus = ob_get_clean();
