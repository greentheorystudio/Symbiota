<?php
/** @var string $inputWindowMode */
?>
<div id="rastertoolstab" style="width:379px;padding:0;">
    <div style="padding:10px;">
        <div style="margin-bottom:10px;">
            <b>Target Raster Layer</b> <select data-role="none" id="targetrasterselect" style="width:275px;">
                <option value="">None</option>
            </select>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button id="dataRasterVectorizeButton" data-role="none" onclick="vectorizeRasterByData();" disabled>Data-Based Vectorize</button> Creates vector features for regions within the selected
            Target Raster Layer with a value equal to or between <input data-role="none" type="text" id="vectorizeRasterByDataValueLow" style="margin-top:3px;width:50px;" /> and <input data-role="none" type="text" id="vectorizeRasterByDataValueHigh" style="margin-top:3px;width:50px;" /> based on a
            data analysis of the selected Target Raster Layer with a resolution relative to that layer.
            <span id="dataRasterVectorizeWarning" class="tool-warning">At least one raster layer needs to be loaded on the map and one feature in the Shapes layer needs to be selected to use this tool.</span>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button id="gridRasterVectorizeButton" data-role="none" onclick="vectorizeRasterByGrid();" disabled>Grid-Based Vectorize</button> Creates vector features within the bounds of the Target Polygon for regions within the selected
            Target Raster Layer with a value equal to or between <input data-role="none" type="text" id="vectorizeRasterByGridValueLow" style="width:50px;" /> and <input data-role="none" type="text" id="vectorizeRasterByGridValueHigh" style="width:50px;" /> based on a
            grid analysis with a resolution of <select data-role="none" id="vectorizeRasterByGridResolution" onchange="processVectorizeRasterByGridResolutionChange();" style="margin-top:3px;">
                <option value="0.025">25</option>
                <option value="0.05">50</option>
                <option value="0.1">100</option>
                <option value="0.25">250</option>
                <option value="0.5">500</option>
            </select> meters.
            <div style="margin-top:5px;">
                <button data-role="none" id="vectorizeRasterByGridTargetPolyDisplayButton" onclick="displayVectorizeRasterByGridTargetPolygon();" disabled>Display Target Polygon</button>
                <button data-role="none" id="vectorizeRasterByGridTargetPolyHideButton" onclick="hideVectorizeRasterByGridTargetPolygon();" style="display:none;">Hide Target Polygon</button>
            </div>
            <span id="gridRasterVectorizeWarning" class="tool-warning">At least one raster layer needs to be loaded on the map and the Target Polygon needs to be displayed to use this tool.</span>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
    </div>
</div>
