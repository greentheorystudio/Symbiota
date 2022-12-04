<div id="mapsettings" data-role="popup" class="well" style="width:600px;height:90%;">
    <a class="boxclose mapsettings_close" id="boxclose"></a>
    <h2>Settings</h2>
    <div style="margin-top:5px;">
        <b>Cluster Points</b> <input data-role="none" type='checkbox' id='clusterswitch' onchange="changeClusterSetting();" <?php echo ($GLOBALS['SPATIAL_POINT_CLUSTER']?'checked':''); ?>>
    </div>
    <div style="margin-top:5px;">
        <b>Cluster Distance (px)</b> <input data-role="none" type="text" id="setclusterdistance" style="width:50px;" value="<?php echo $GLOBALS['SPATIAL_POINT_CLUSTER_DISTANCE']; ?>" />
    </div>
    <div style="margin-top:5px;">
        <b>Display Heat Map</b> <input data-role="none" type='checkbox' id='heatmapswitch' onchange="toggleHeatMap();" <?php echo ($GLOBALS['SPATIAL_POINT_DISPLAY_HEAT_MAP']?'checked':''); ?>>
    </div>
    <div style="margin-top:5px;">
        <b>Heat Map Radius (px)</b> <input data-role="none" type="text" id="heatmapradius" style="width:50px;" value="<?php echo $GLOBALS['SPATIAL_POINT_HEAT_MAP_RADIUS']; ?>" />
    </div>
    <div style="margin-top:5px;">
        <b>Heat Map Blur (px)</b> <input data-role="none" type="text" id="heatmapblur" style="width:50px;" value="<?php echo $GLOBALS['SPATIAL_POINT_HEAT_MAP_BLUR']; ?>" />
    </div>
</div>
