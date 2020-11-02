<div id="mapsettings" data-role="popup" class="well" style="width:600px;height:90%;font-size:14px;">
    <a class="boxclose mapsettings_close" id="boxclose"></a>
    <h2>Map Settings</h2>
    <div style="margin-top:5px;">
        <b>Cluster Points</b> <input data-role="none" type='checkbox' name='clusterswitch' id='clusterswitch' onchange="changeClusterSetting();" value='1' checked>
    </div>
    <div style="margin-top:5px;">
        <b>Cluster Distance</b> <input data-role="none" type="text" id="setclusterdistance" style="width:50px;" name="setclusterdistance" value="50" onchange="changeClusterDistance();" />
    </div>
    <!-- <div style="margin-top:5px;">
        <b>Display Date Slider</b> <input data-role="none" type='checkbox' name='datesliderswitch' id='datesliderswitch' onchange="toggleDateSlider();" value='1' >
        <input data-role="none" type="radio" name="dateslidertype" id="dssingletype" value="single" onchange="checkDateSliderType();" checked /> Single
        <input data-role="none" type="radio" name="dateslidertype" id="dsdualtype" value="dual" onchange="checkDateSliderType();" /> Dual
    </div> -->
    <div style="margin-top:5px;">
        <b>Display Heat Map</b> <input data-role="none" type='checkbox' name='heatmapswitch' id='heatmapswitch' onchange="toggleHeatMap();" value='1' >
    </div>
    <div style="margin-top:5px;">
        <b>Heat Map Radius</b> <input data-role="none" type="text" id="heatmapradius" style="width:50px;" value="5" onchange="changeHeatMapRadius();" />
    </div>
    <div style="margin-top:5px;">
        <b>Heat Map Blur</b> <input data-role="none" type="text" id="heatmapblur" style="width:50px;" value="15" onchange="changeHeatMapBlur();" />
    </div>
</div>
