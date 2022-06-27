<?php
/** @var string $inputWindowMode */
?>
<div id="pointscalculatortab" style="width:379px;padding:0;">
    <div id="pointToolsNoneDiv" style="padding:10px;margin-top:10px;display:block;">
        There are no points loaded on the map.
    </div>
    <div id="pointToolsDiv" style="padding:10px;display:none;">
        <div>
            <button data-role="none" onclick="createConcavePoly();" >Concave Hull Polygon</button> Creates a concave hull polygon or multipolygon for
            <select data-role="none" id="concavepolysource" style="margin-top:3px;" onchange="checkPointToolSource('concavepolysource');">
                <option value="all">all</option>
                <option value="selected">selected</option>
            </select> points with a maximum edge length of <input data-role="none" type="text" id="concaveMaxEdgeSize" style="width:75px;margin-top:3px;" /> kilometers.
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
        <div style="margin-top:10px;">
            <button data-role="none" onclick="createConvexPoly();" >Convex Hull Polygon</button> Creates a convex hull polygon for
            <select data-role="none" id="convexpolysource" style="margin-top:3px;" onchange="checkPointToolSource('convexpolysource');">
                <option value="all">all</option>
                <option value="selected">selected</option>
            </select> points.
        </div>
        <div style="margin:5px 0 5px 0;"><hr /></div>
    </div>
</div>
