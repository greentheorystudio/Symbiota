<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceIndividualManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$occid = $_GET['occid'];
$tid = $_GET['tid'];
$collId = $_GET['collid'];
$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;

if(!is_numeric($occid)) {
    $occid = 0;
}
if(!is_numeric($tid)) {
    $tid = 0;
}
if(!is_numeric($collId)) {
    $collId = 0;
}
if(!is_numeric($clid)) {
    $clid = 0;
}

$indManager = new OccurrenceIndividualManager();
$indManager->setOccid($occid);
?>
<div id='innertext' style='width:95%;min-height:400px;clear:both;background-color:white;'>
    <fieldset style="padding:20px;margin:15px;">
        <legend><b>Species Checklist Relationships</b></legend>
        <?php
        $vClArr = $indManager->getVoucherChecklists();
        if($vClArr){
            echo '<div style="font-weight:bold"><u>Specimen voucher of the following checklists</u></div>';
            echo '<ul style="margin:15px 0 25px 0;">';
            foreach($vClArr as $id => $clName){
                echo '<li>';
                echo '<a href="../../checklists/checklist.php?showvouchers=1&cl='.$id.'" target="_blank">'.$clName.'</a>&nbsp;&nbsp;';
                if(isset($GLOBALS['USER_RIGHTS']['ClAdmin']) && in_array($id, $GLOBALS['USER_RIGHTS']['ClAdmin'], true)){
                    $confirmLine = "'Are you sure you want to remove this voucher link ? '";
                    echo '<a href="index.php?delvouch='.$id.'&occid='.$occid.'" title="Delete voucher link" onclick="return confirm('.$confirmLine.')"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></a>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        else{
            echo '<h3>This occurrence has not been designated as a voucher for a species checklist</h3>';
        }
        if($GLOBALS['IS_ADMIN'] || array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])){
            ?>
            <div style='margin-top:15px;'>
                <?php
                if($clArr = $indManager->getChecklists(array_keys($vClArr))){
                    ?>
                    <fieldset style='margin-top:20px;padding:15px;'>
                        <legend><b>New Voucher Assignment</b></legend>
                        <?php
                        if($tid){
                            ?>
                            <div style='margin:10px;'>
                                <form action="../../checklists/clsppeditor.php" method="post" onsubmit="return verifyVoucherForm(this);">
                                    <div>
                                        Add as voucher to checklist:
                                        <input name='voccid' type='hidden' value='<?php echo $occid; ?>'>
                                        <input name='tid' type='hidden' value='<?php echo $tid; ?>'>
                                        <select id='clid' name='clid'>
                                            <option value='0'>Select a Checklist</option>
                                            <option value='0'>--------------------------</option>
                                            <?php
                                            foreach($clArr as $clKey => $clValue){
                                                echo "<option value='".$clKey."' ".($clid === $clKey? 'SELECTED' : '').">$clValue</option>\n";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div style='margin:5px 0 0 10px;'>
                                        Notes:
                                        <input name='vnotes' type='text' size='50' title='Viewable to public'>
                                    </div>
                                    <div style='margin:5px 0 0 10px;'>
                                        Editor Notes:
                                        <input name='veditnotes' type='text' size='50' title='Viewable only to checklist editors'>
                                    </div>
                                    <div>
                                        <input type='submit' name='action' value='Add Voucher'>
                                    </div>
                                </form>
                            </div>
                            <?php
                        }
                        else{
                            ?>
                            <div style='margin:20px;'>
                                Unable to use this occurrence record as a voucher because
                                scientific name could not be verified in the taxonomic thesaurus
                            </div>
                            <?php
                        }
                        ?>
                    </fieldset>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </fieldset>
    <?php
    $datasetArr = $indManager->getDatasetArr();
    if($datasetArr){
        echo '<fieldset style="padding:20px;margin:15px;">';
        echo '<legend>Dataset Linkages</legend>';
        if($GLOBALS['SYMB_UID']) {
            echo '<div style="float:right"><a href="#" onclick="toggle(\'dataset-block\');return false"><i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i></a></div>';
        }
        $dsDisplayStr = '';
        foreach($datasetArr as $dsid => $dsArr){
            if(isset($dsArr['linked']) && $dsArr['linked']){
                $dsDisplayStr .= '<li>';
                $dsDisplayStr .= '<a href="../datasets/datasetmanager.php?datasetid='.$dsid.'" target="_blank">'.$dsArr['name'].'</a>';
                if(isset($dsArr['role']) && $dsArr['role']) {
                    $dsDisplayStr .= ' (role: ' . $dsArr['role'] . ')';
                }
                if(isset($dsArr['notes']) && $dsArr['notes']) {
                    $dsDisplayStr .= ' - ' . $dsArr['notes'];
                }
                $dsDisplayStr .= '</li>';
            }
        }
        if($dsDisplayStr){
            echo '<div class="section-title">Member of the following datasets</div>';
            echo '<ul>'.$dsDisplayStr.'</ul>';
        }
        else {
            echo '<div style="margin:15px 0">Occurrence is not linked to any datasets</div>';
        }
        if($GLOBALS['SYMB_UID']){
            ?>
            <fieldset id="dataset-block" style="display:none;">
                <legend>Create New Dataset Relationship</legend>
                <form action="../datasets/datasetHandler.php" method="post" onsubmit="return verifyDatasetForm(this);">
                    <div style="margin:3px">
                        <select name="targetdatasetid">
                            <option value="">Select an Existing Dataset</option>
                            <option value="">----------------------------------</option>
                            <?php
                            foreach($datasetArr as $dsid => $dsArr){
                                if(!array_key_exists('linked',$dsArr)){
                                    echo '<option value="'.$dsid.'">'.$dsArr['name'].'</option>';
                                }
                            }
                            ?>
                            <option value="--newDataset">Create New Dataset</option>
                        </select>
                    </div>
                    <div style="margin:5px">
                        <b>Notes:</b><br/>
                        <input name="notes" type="text" value="" maxlength="250" style="width:90%;" />
                    </div>
                    <div style="margin:15px">
                        <input name="occid" type="hidden" value="<?php echo $occid; ?>" />
                        <input name="sourcepage" type="hidden" value="individual" />
                        <button name="action" type="submit" value="addSelectedToDataset" >Link to Dataset</button>
                    </div>
                </form>
            </fieldset>
            <?php
        }
        echo '</fieldset>';
    }
    ?>
</div>
