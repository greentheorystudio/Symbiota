<?php
include_once(__DIR__ . '/../config/symbini.php');
$mapCenter = '[-110.90713, 32.21976]';
if(isset($GLOBALS['SPATIAL_INITIAL_CENTER']) && $GLOBALS['SPATIAL_INITIAL_CENTER']) {
    $mapCenter = $GLOBALS['SPATIAL_INITIAL_CENTER'];
}
$mapZoom = 7;
if(isset($GLOBALS['SPATIAL_INITIAL_ZOOM']) && $GLOBALS['SPATIAL_INITIAL_ZOOM']) {
    $mapZoom = $GLOBALS['SPATIAL_INITIAL_ZOOM'];
}
?>
<script type="text/javascript">
    const initialMapZoom = <?php echo $mapZoom; ?>;
    const initialMapCenter = <?php echo $mapCenter; ?>;
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
            <option value="googleterrain">Google Terrain</option>
            <option value="googleroadmap">Google Terrain-Roadmap</option>
            <option value="googlealteredroadmap">Google Roadmap</option>
            <option value="googlehybrid">Google Satellite-Roadmap</option>
            <option value="googlesatellite">Google Satellite</option>
            <option value="worldtopo">ESRI World Topo</option>
            <option value="worldimagery">ESRI World Imagery</option>
            <option value="esristreet">ESRI StreetMap</option>
            <option value="ocean">ESRI Ocean</option>
            <option value="ngstopo">National Geographic Topo</option>
            <option value="natgeoworld">National Geographic World</option>
            <option value="openstreet">OpenStreetMap</option>
            <option value="blackwhite">Stamen Design Black &amp; White</option>
        </select>
        <div id="mapcoords"></div>
    </div>
</div>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/spatial.viewer.js?ver=20210416" type="text/javascript"></script>
