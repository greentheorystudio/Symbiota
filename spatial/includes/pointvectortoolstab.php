<?php
/** @var string $inputWindowMode */
?>
<div id="pointscalculatortab">
    <div style="padding:10px;">
        <div>
            <button id="concavePolyButton" data-role="none" onclick="createConcavePoly();" disabled>Concave Hull Polygon</button> Creates a concave hull polygon or multipolygon for
            <select data-role="none" id="concavepolysource" style="margin-top:3px;" onchange="checkPointToolSource('concavepolysource');">
                <option value="all">all</option>
                <option value="selected">selected</option>
            </select> points with a maximum edge length of <input data-role="none" type="text" id="concaveMaxEdgeSize" style="width:75px;margin-top:3px;" /> kilometers.
            <span id="concavePolyNoPoints" class="tool-warning">Occurrence points need to be loaded on the map to use this tool.</span>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button id="convexPolyButton" data-role="none" onclick="createConvexPoly();" disabled>Convex Hull Polygon</button> Creates a convex hull polygon for
            <select data-role="none" id="convexpolysource" style="margin-top:3px;" onchange="checkPointToolSource('convexpolysource');">
                <option value="all">all</option>
                <option value="selected">selected</option>
            </select> points.
            <span id="convexPolyNoPoints" class="tool-warning">Occurrence points need to be loaded on the map to use this tool.</span>
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
    </div>
</div>
