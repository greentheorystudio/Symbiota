<?php
/** @var string $inputWindowMode */
?>
<div id="rastertoolstab" style="width:379px;padding:0;">
    <div style="padding:10px;">
        <div style="margin-top:10px;">
            <b>Target Raster Layer</b> <select data-role="none" id="targetrasterselect" style="width:275px;">
                <option value="">None</option>
            </select>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button data-role="none" onclick="vectorizeRasterByData();" >Data-Based Vectorize</button> Creates vector features for regions within the selected
            Target Raster Layer with a value equal to or between <input data-role="none" type="text" id="vectorizeRasterByDataValueLow" style="margin-top:3px;width:50px;" /> and <input data-role="none" type="text" id="vectorizeRasterByDataValueHigh" style="margin-top:3px;width:50px;" /> based on a
            data analysis of the selected Target Raster Layer with a resolution relative to that layer.
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button data-role="none" onclick="vectorizeRasterByGrid();" >Grid-Based Vectorize</button> Creates vector features within the bounds of the Target for regions within the selected
            Target Raster Layer with a value equal to or between <input data-role="none" type="text" id="vectorizeRasterByGridValueLow" style="width:50px;" /> and <input data-role="none" type="text" id="vectorizeRasterByGridValueHigh" style="width:50px;" /> based on a
            grid analysis with a resolution of <select data-role="none" id="vectorizeRasterByGridResolution" onchange="processVectorizeRasterByGridResolutionChange();" style="margin-top:3px;">
                <option value="0.025">25</option>
                <option value="0.05">50</option>
                <option value="0.1">100</option>
                <option value="0.25">250</option>
                <option value="0.5">500</option>
            </select> meters.
            <div style="margin-top:5px;">
                <button data-role="none" id="vectorizeRasterByGridTargetPolyDisplayButton" onclick="displayVectorizeRasterByGridTargetPolygon();" >Show Target</button>
                <button data-role="none" id="vectorizeRasterByGridTargetPolyHideButton" onclick="hideVectorizeRasterByGridTargetPolygon();" style="display:none;" >Hide Target</button>
            </div>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
    </div>
</div>
