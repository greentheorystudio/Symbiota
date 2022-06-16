<?php
/** @var string $inputWindowMode */
?>
<div id="rastertoolstab" style="width:379px;padding:0;">
    <div style="padding:10px;">
        <div style="margin-top:10px;">
            <b>Target Raster Layer</b> <select data-role="none" id="targetrasterselect">
                <option value="none">None</option>
            </select>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button data-role="none" onclick="createBuffers();" >Vectorize</button> Creates vector features within the bounds of a selected polygon for regions within the selected
            target raster with a value of <input data-role="none" type="text" id="bufferSize" style="width:50px;" /> or equal to or between <input data-role="none" type="text" id="bufferSize" style="width:50px;" /> and <input data-role="none" type="text" id="bufferSize" style="width:50px;" />
            at a resolution of <input data-role="none" type="text" id="bufferSize" style="width:50px;" /> kilometers.
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
    </div>
</div>
