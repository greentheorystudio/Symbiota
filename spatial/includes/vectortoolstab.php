<?php
/** @var string $inputWindowMode */
?>
<div id="polycalculatortab" style="width:379px;padding:0;">
    <div style="padding:10px;">
        <div style="height:45px;">
            <div style="float:<?php echo (!$inputWindowMode?'right':'left'); ?>;">
                Total area of selected features (sq/km)
            </div>
            <div style="float:<?php echo (!$inputWindowMode?'right':'left'); ?>;margin-top:5px;">
                <input data-role="none" type="text" id="polyarea" style="width:250px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="0" disabled />
            </div>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="width:100%;display:flex;justify-content:space-between;align-items:center;">
            <div style="display:flex;justify-content:flex-start;align-items:center;">
                <select data-role="none" id="shapesdownloadselect">
                    <option>Download Type</option>
                    <option value="kml">KML</option>
                    <option value="geojson">GeoJSON</option>
                </select>
                <button data-role="none" class="icon-button" title="Download" onclick="downloadShapesLayer();"><i style="height:15px;width:15px;" class="fas fa-download"></i></button>
            </div>
            <div style="width:250px;display:flex;justify-content:flex-end;align-items:center;">
                <button data-role="none" type="button" onclick='deleteSelections();' >Delete Selected Features</button>
            </div>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button id="bufferPolyButton" data-role="none" onclick="createBuffers();" disabled>Buffer</button> Creates a buffer polygon of <input data-role="none" type="text" id="bufferSize" style="width:50px;" /> km around selected features.
            <span id="bufferPolyWarning" class="tool-warning">At least one feature in the Shapes layer needs to be selected to use this tool.</span>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button id="differencePolyButton" data-role="none" onclick="createPolyDifference();" disabled>Difference</button> Returns a new polygon with the area of the polygon, box, or circle selected first, excluding the area of the polygon, box, or circle selected second.
            <span id="differencePolyWarning" class="tool-warning">Two features in the Shapes layer need to be selected to use this tool.</span>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button id="intersectPolyButton" data-role="none" onclick="createPolyIntersect();" disabled>Intersect</button> Returns a new polygon with the overlapping area of two selected polygons, boxes, or circles.
            <span id="intersectPolyWarning" class="tool-warning">Two features in the Shapes layer need to be selected to use this tool.</span>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button id="unionPolyButton" data-role="none" onclick="createPolyUnion();" disabled>Union</button> Returns a new polygon with the combined area of two or more selected polygons, boxes, or circles. *Note the new polygon will replace all selected shapes.
            <span id="unionPolyWarning" class="tool-warning">At least two features in the Shapes layer need to be selected to use this tool.</span>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
    </div>
</div>
