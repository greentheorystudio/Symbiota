<?php
include_once(__DIR__ . '/../config/symbini.php');
$mapCenter = '[-110.90713, 32.21976]';
if(isset($SPATIAL_INITIAL_CENTER) && $SPATIAL_INITIAL_CENTER) {
    $mapCenter = $SPATIAL_INITIAL_CENTER;
}
$mapZoom = 7;
if(isset($SPATIAL_INITIAL_ZOOM) && $SPATIAL_INITIAL_ZOOM) {
    $mapZoom = $SPATIAL_INITIAL_ZOOM;
}
?>
<script type="text/javascript">
    const initialMapZoom = <?php echo $mapZoom; ?>;
    const initialMapCenter = <?php echo $mapCenter; ?>;
</script>
<div id="map" class="map">
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
<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/spatial.viewer.js?ver=3" type="text/javascript"></script>
