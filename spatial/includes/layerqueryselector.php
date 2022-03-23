<div id="layerqueryselector" data-role="popup" class="well" style="width:50%;height:225px;font-size:14px;">
    <a class="boxclose layerqueryselector_close" id="boxclose"></a>
    <h2>Query Selector</h2>
    <div style="display:flex;justify-content:center;gap:10px;">
        <select data-role="none" id="spatialQueryFieldSelector" style="width:250px;"></select>
        <select data-role="none" id="spatialQueryOperatorSelector" onchange="processSpatialQueryOperatorSelectorChange(this.value);">
            <option value="equals">EQUALS</option>
            <option value="contains">CONTAINS</option>
            <option value="greaterThan">GREATER THAN</option>
            <option value="lessThan">LESS THAN</option>
            <option value="between">BETWEEN</option>
        </select>
        <div id="spatialQuerySingleValueDiv" style="display:block;">
            <input data-role="none" type="text" id="spatialQuerySingleValueInput" style="width:125px;" />
        </div>
        <div id="spatialQueryBetweenValueDiv" style="display:none;justify-content:space-evenly;gap:8px;align-items:center;align-content:center;">
            <input data-role="none" type="text" id="spatialQueryDoubleValueInput1" style="width:125px;" /> AND
            <input data-role="none" type="text" id="spatialQueryDoubleValueInput2" style="width:125px;" />
        </div>
    </div>
    <div style="width:100%;display:flex;justify-content:flex-end;margin-top:15px;">
        <input data-role="none" type="hidden" id="spatialQuerySelectorLayerId" value="" />
        <button data-role="none" type="button" onclick="processQuerySelectorQuery();">Run Query</button>
    </div>
</div>
