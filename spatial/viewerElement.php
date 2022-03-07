<?php
include_once(__DIR__ . '/../config/symbbase.php');
$mapCenter = ((isset($GLOBALS['SPATIAL_INITIAL_CENTER']) && $GLOBALS['SPATIAL_INITIAL_CENTER'])?$GLOBALS['SPATIAL_INITIAL_CENTER']:'[-110.90713, 32.21976]');
$mapZoom = ((isset($GLOBALS['SPATIAL_INITIAL_ZOOM']) && $GLOBALS['SPATIAL_INITIAL_ZOOM'])?$GLOBALS['SPATIAL_INITIAL_ZOOM']:7);
$initialPointColor = ((isset($GLOBALS['SPATIAL_INITIAL_POINT_COLOR']) && $GLOBALS['SPATIAL_INITIAL_POINT_COLOR'])?$GLOBALS['SPATIAL_INITIAL_POINT_COLOR']:'E69E67');
$shapesFillColor = ((isset($GLOBALS['SPATIAL_INITIAL_SHAPES_FILL_COLOR']) && $GLOBALS['SPATIAL_INITIAL_SHAPES_FILL_COLOR'])?$GLOBALS['SPATIAL_INITIAL_SHAPES_FILL_COLOR']:'ffffff');
$shapesBorderColor = ((isset($GLOBALS['SPATIAL_INITIAL_SHAPES_BORDER_COLOR']) && $GLOBALS['SPATIAL_INITIAL_SHAPES_BORDER_COLOR'])?$GLOBALS['SPATIAL_INITIAL_SHAPES_BORDER_COLOR']:'3399CC');
$shapesBorderWidth = ((isset($GLOBALS['SPATIAL_INITIAL_SHAPES_BORDER_WIDTH']) && $GLOBALS['SPATIAL_INITIAL_SHAPES_BORDER_WIDTH'])?$GLOBALS['SPATIAL_INITIAL_SHAPES_BORDER_WIDTH']:2);
$shapesPointRadius = ((isset($GLOBALS['SPATIAL_INITIAL_SHAPES_POINT_RADIUS']) && $GLOBALS['SPATIAL_INITIAL_SHAPES_POINT_RADIUS'])?$GLOBALS['SPATIAL_INITIAL_SHAPES_POINT_RADIUS']:5);
$shapesOpacity = ((isset($GLOBALS['SPATIAL_INITIAL_SHAPES_OPACITY']) && $GLOBALS['SPATIAL_INITIAL_SHAPES_OPACITY'])?$GLOBALS['SPATIAL_INITIAL_SHAPES_OPACITY']:'0.4');
$dragDropFillColor = ((isset($GLOBALS['SPATIAL_INITIAL_DRAGDROP_FILL_COLOR']) && $GLOBALS['SPATIAL_INITIAL_DRAGDROP_FILL_COLOR'])?$GLOBALS['SPATIAL_INITIAL_DRAGDROP_FILL_COLOR']:'aaaaaa');
$dragDropBorderColor = ((isset($GLOBALS['SPATIAL_INITIAL_DRAGDROP_BORDER_COLOR']) && $GLOBALS['SPATIAL_INITIAL_DRAGDROP_BORDER_COLOR'])?$GLOBALS['SPATIAL_INITIAL_DRAGDROP_BORDER_COLOR']:'000000');
$dragDropBorderWidth = ((isset($GLOBALS['SPATIAL_INITIAL_DRAGDROP_BORDER_WIDTH']) && $GLOBALS['SPATIAL_INITIAL_DRAGDROP_BORDER_WIDTH'])?$GLOBALS['SPATIAL_INITIAL_DRAGDROP_BORDER_WIDTH']:2);
$dragDropPointRadius = ((isset($GLOBALS['SPATIAL_INITIAL_DRAGDROP_POINT_RADIUS']) && $GLOBALS['SPATIAL_INITIAL_DRAGDROP_POINT_RADIUS'])?$GLOBALS['SPATIAL_INITIAL_DRAGDROP_POINT_RADIUS']:5);
$dragDropOpacity = ((isset($GLOBALS['SPATIAL_INITIAL_DRAGDROP_OPACITY']) && $GLOBALS['SPATIAL_INITIAL_DRAGDROP_OPACITY'])?$GLOBALS['SPATIAL_INITIAL_DRAGDROP_OPACITY']:'0.3');
?>
<script type="text/javascript">
    const initialMapZoom = <?php echo $mapZoom; ?>;
    const initialMapCenter = <?php echo $mapCenter; ?>;
    const initialPointColor = '<?php echo $initialPointColor; ?>';
    const shapesFillColor = '<?php echo $shapesFillColor; ?>';
    const shapesBorderColor = '<?php echo $shapesBorderColor; ?>';
    const shapesBorderWidth = <?php echo $shapesBorderWidth; ?>;
    const shapesPointRadius = <?php echo $shapesPointRadius; ?>;
    const shapesOpacity = '<?php echo $shapesOpacity; ?>';
    const dragDropFillColor = '<?php echo $dragDropFillColor; ?>';
    const dragDropBorderColor = '<?php echo $dragDropBorderColor; ?>';
    const dragDropBorderWidth = <?php echo $dragDropBorderWidth; ?>;
    const dragDropPointRadius = <?php echo $dragDropPointRadius; ?>;
    const dragDropOpacity = '<?php echo $dragDropOpacity; ?>';
</script>
<div id="map" class="map">
    <div id="popup" class="ol-popup">
        <a href="#" id="popup-closer" class="ol-popup-closer"></a>
        <div id="popup-content"></div>
    </div>

    <div id="mapinfo">
        <div id="mapscale_us"></div>
        <div id="mapscale_metric"></div>
    </div>

    <div id="maptoolcontainer">
        <span class="maptext">Base Layer</span>
        <select id="base-map" onchange="changeBaseMap();">
            <option value="googleterrain" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='googleterrain'?'selected':''); ?>>Google Terrain</option>
            <option value="googleroadmap" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='googleroadmap'?'selected':''); ?>>Google Terrain-Roadmap</option>
            <option value="googlealteredroadmap" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='googlealteredroadmap'?'selected':''); ?>>Google Roadmap</option>
            <option value="googlehybrid" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='googlehybrid'?'selected':''); ?>>Google Satellite-Roadmap</option>
            <option value="googlesatellite" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='googlesatellite'?'selected':''); ?>>Google Satellite</option>
            <option value="worldtopo" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='worldtopo'?'selected':''); ?>>ESRI World Topo</option>
            <option value="worldimagery" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='worldimagery'?'selected':''); ?>>ESRI World Imagery</option>
            <option value="esristreet" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='esristreet'?'selected':''); ?>>ESRI StreetMap</option>
            <option value="ocean" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='ocean'?'selected':''); ?>>ESRI Ocean</option>
            <option value="ngstopo" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='ngstopo'?'selected':''); ?>>National Geographic Topo</option>
            <option value="natgeoworld" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='natgeoworld'?'selected':''); ?>>National Geographic World</option>
            <option value="openstreet" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='openstreet'?'selected':''); ?>>OpenStreetMap</option>
            <option value="blackwhite" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='blackwhite'?'selected':''); ?>>Stamen Design Black &amp; White</option>
        </select>
        <div id="mapcoords"></div>
    </div>
</div>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/spatial.viewer.js?ver=20220306" type="text/javascript"></script>
