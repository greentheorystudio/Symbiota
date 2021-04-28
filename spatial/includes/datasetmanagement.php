<?php
/** @var array $datasetArr */
?>
<div id="datasetmanagement" data-role="popup" class="well" style="width:500px;height:175px;font-size:14px;">
    <a class="boxclose datasetmanagement_close" id="boxclose"></a>
    <h2>Dataset Management</h2>
    <div style="padding:5px;float:left;">Dataset target: </div>
    <div style="padding:5px;float:left;">
        <select data-role="none" id="targetdatasetid">
            <option value="">------------------------</option>
            <?php
            if($datasetArr){
                foreach($datasetArr as $datasetID => $datasetName){
                    echo '<option value="'.$datasetID.'">'.$datasetName.'</option>';
                }
            }
            else echo '<option value="">no existing datasets available</option>';
            ?>
            <option value="">----------------------------------</option>
            <option value="--newDataset">Create New Dataset</option>
        </select>
    </div>
    <div style="clear:both;">
        <div id="datasetselecteddiv" style="padding:5px 0;float:left;display:none;"><button style="background-color: white;" data-role="none" name="action" type="submit" value="addSelectedToDataset" onclick="addSelectionsToDataset();">Add Selected Records to Dataset</button></div>
        <div style="padding:5px;float:left;"><button style="background-color: white;" data-role="none" name="action" type="submit" value="addAllToDataset" onclick="addQueryToDataset();">Add Complete Query to Dataset</button></div>
    </div>
</div>
