<?php
/** @var string $inputWindowMode */
?>
<div id="rastertoolstab" style="width:379px;padding:0;">
    <div style="padding:10px;">
        <div style="margin-top:10px;">
            <b>Target Raster Layer</b> <select data-role="none" id="targetrasterselect">
                <option value="">None</option>
            </select>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button data-role="none" onclick="vectorizeRaster();" >Vectorize</button> Creates vector features within the bounds of a selected polygon for regions within the selected
            target raster with a value equal to or between <input data-role="none" type="text" id="vectorizeRasterValueLow" style="width:50px;" /> and <input data-role="none" type="text" id="vectorizeRasterValueHigh" style="width:50px;" />.
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
    </div>
</div>
