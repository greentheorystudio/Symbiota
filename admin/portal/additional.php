<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/ConfigurationManager.php');

if(!$GLOBALS['IS_ADMIN']) {
    header('Location: ../../index.php');
}

$confManager = new ConfigurationManager();

$fullConfArr = $confManager->getConfigurationsArr();
$additionalConfArr = $fullConfArr['additional'];
?>
<div id="additionalconfig">
    <div style="display:flex;justify-content:right;margin:10px;cursor:pointer;" title="Add Configuration" onclick="toggle('addconfdiv')">
        <i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
    </div>
    <div id="addconfdiv" style="display:none">
        <fieldset>
            <legend><b>Add Configuration</b></legend>
            <div class="field-block">
                <span class="field-label">New Configuration Name:</span>
                <span class="field-elem">
                            <input type="text" id="newConfName" value="" style="width:600px;" onchange="processNewConfNameChange();" />
                        </span>
            </div>
            <div class="field-block">
                <span class="field-label">New Configuration Value:</span>
                <span class="field-elem">
                            <input type="text" id="newConfValue" value="" style="width:600px;" />
                        </span>
            </div>
            <div style="margin-top:12px;width:98%;display:flex;justify-content:right;">
                <button type="button" onclick="processAddConfiguration();">Add Configuration</button>
            </div>
        </fieldset>
    </div>
    <fieldset style="margin: 10px 0;">
        <legend><b>Additional Configurations</b></legend>
        <?php
        if($additionalConfArr){
            foreach($additionalConfArr as $confName => $confValue){
                ?>
                <div class="field-block">
                    <span class="field-label"><?php echo $confName; ?>:  <button type="button" onclick="sendAPIRequest('delete','<?php echo $confName; ?>','');">Delete</button></span>
                    <span class="field-elem">
                        <input type="text" id="<?php echo $confName; ?>" value="<?php echo $confValue; ?>" style="width:600px;" onchange="processTextConfigurationChange('<?php echo $confName; ?>','',false);" />
                    </span>
                </div>
                <?php
            }
        }
        else{
            ?>
            <div class="field-block">
                <span class="field-label">No additional configurations set</span>
                <span class="field-elem"></span>
            </div>
            <?php
        }
        ?>
    </fieldset>
</div>
