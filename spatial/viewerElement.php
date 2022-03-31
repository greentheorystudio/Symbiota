<?php
include_once(__DIR__ . '/../config/symbbase.php');
?>
<script type="text/javascript">
    const initialMapZoom = <?php echo $GLOBALS['SPATIAL_INITIAL_ZOOM']; ?>;
    const initialMapCenter = <?php echo $GLOBALS['SPATIAL_INITIAL_CENTER']; ?>;
    const pointLayerFillColor = '<?php echo $GLOBALS['SPATIAL_POINT_FILL_COLOR']; ?>';
    const pointLayerBorderColor = '<?php echo $GLOBALS['SPATIAL_POINT_BORDER_COLOR']; ?>';
    const pointLayerBorderWidth = <?php echo $GLOBALS['SPATIAL_POINT_BORDER_WIDTH']; ?>;
    const pointLayerPointRadius = <?php echo $GLOBALS['SPATIAL_POINT_POINT_RADIUS']; ?>;
    const pointLayerSelectionsBorderColor = '<?php echo $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_COLOR']; ?>';
    const pointLayerSelectionsBorderWidth = <?php echo $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH']; ?>;
    const shapesFillColor = '<?php echo $GLOBALS['SPATIAL_SHAPES_FILL_COLOR']; ?>';
    const shapesBorderColor = '<?php echo $GLOBALS['SPATIAL_SHAPES_BORDER_COLOR']; ?>';
    const shapesBorderWidth = <?php echo $GLOBALS['SPATIAL_SHAPES_BORDER_WIDTH']; ?>;
    const shapesPointRadius = <?php echo $GLOBALS['SPATIAL_SHAPES_POINT_RADIUS']; ?>;
    const shapesOpacity = '<?php echo $GLOBALS['SPATIAL_SHAPES_OPACITY']; ?>';
    const shapesSelectionsBorderColor = '<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR']; ?>';
    const shapesSelectionsFillColor = '<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR']; ?>';
    const shapesSelectionsBorderWidth = <?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH']; ?>;
    const shapesSelectionsOpacity = '<?php echo $GLOBALS['SPATIAL_SHAPES_SELECTIONS_OPACITY']; ?>';
    const dragDropFillColor = '<?php echo $GLOBALS['SPATIAL_DRAGDROP_FILL_COLOR']; ?>';
    const dragDropBorderColor = '<?php echo $GLOBALS['SPATIAL_DRAGDROP_BORDER_COLOR']; ?>';
    const dragDropBorderWidth = <?php echo $GLOBALS['SPATIAL_DRAGDROP_BORDER_WIDTH']; ?>;
    const dragDropPointRadius = <?php echo $GLOBALS['SPATIAL_DRAGDROP_POINT_RADIUS']; ?>;
    const dragDropOpacity = '<?php echo $GLOBALS['SPATIAL_DRAGDROP_OPACITY']; ?>';
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
