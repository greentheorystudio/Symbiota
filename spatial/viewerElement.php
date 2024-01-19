<?php
include_once(__DIR__ . '/../config/symbbase.php');
?>
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
            <option value="ngstopo" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='ngstopo'?'selected':''); ?>>National Geographic Topo</option>
            <option value="natgeoworld" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='natgeoworld'?'selected':''); ?>>National Geographic World</option>
            <option value="openstreet" <?php echo (isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER'])&&$GLOBALS['SPATIAL_INITIAL_BASE_LAYER']==='openstreet'?'selected':''); ?>>OpenStreetMap</option>
        </select>
        <div id="mapcoords"></div>
    </div>
</div>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/spatial.viewer.js?ver=20240115" type="text/javascript"></script>
