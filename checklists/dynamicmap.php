<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/DynamicChecklistManager.php');
header('Content-Type: text/html; charset=UTF-8' );

$tid = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:0;
$taxa = array_key_exists('taxa',$_REQUEST)?htmlspecialchars($_REQUEST['taxa']):'';
$interface = array_key_exists('interface',$_REQUEST)&&$_REQUEST['interface']?htmlspecialchars($_REQUEST['interface']):'checklist';

$dynClManager = new DynamicChecklistManager();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Dynamic Checklist Generator</title>
    <meta name="description" content="Dynamically create a checklist in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20230105" rel="stylesheet" type="text/css" />
    <style>
        .map {
            width:95%;
            height:650px;
            margin-left: auto;
            margin-right: auto;
        }

        #mapinfo, #mapscale_us, #mapscale_metric {
            display: none;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/turf.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/spatial.module.core.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            $( "#taxa" ).autocomplete({
                source: function( request, response ) {
                    $.getJSON( "<?php echo $GLOBALS['CLIENT_ROOT']; ?>/api/taxa/speciessuggest.php", { term: request.term, level: 'high' }, response );
                },
                minLength: 2,
                autoFocus: true,
                select: function( event, ui ) {
                    if(ui.item){
                        $( "#tid" ).val(ui.item.id);
                    }
                }
            });

        });

        function checkForm(){
            if(document.getElementById("latbox").value && document.getElementById("lngbox").value){
                return true;
            }
            alert("You must first click on map to capture coordinate points");
            return false;
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="breadcrumbs">
    <a href='../index.php'>Home</a> &gt;
    <b>Dynamic Map</b>
</div>
<div id="mainContainer" style="padding: 10px 15px 15px;">
    <form name="mapForm" action="dynamicchecklist.php" method="post" onsubmit="return checkForm();">
        <div style="width:95%;margin-left:auto;margin-right:auto;">
            Click and drag anywhere on the map to pan the map in any direction. Use the + and - buttons in the top-right corner
            of the map to adjust the zoom level. Adjust the radius value to any value you wish. Click once on the map to create
            a circle, based on the radius value, which will be used to contruct the taxa list. Click the Build Taxa List button
            to create the taxa list based on the circle you have created.
        </div>
        <div style="width:95%;margin-left:auto;margin-right:auto;margin-top:5px;">
            <div style="float:left;width:300px;">
                <div>
                    <input type="submit" name="buildchecklistbutton" value="Build Taxa List" disabled/>
                    <input type="hidden" name="interface" value="<?php echo $interface; ?>" />
                    <input type="hidden" id="latbox" name="lat" value="" />
                    <input type="hidden" id="lngbox" name="lng" value="" />
                    <input type="hidden" id="groundradiusbox" name="groundradius" value="" />
                </div>
                <div style="margin-top:5px;">
                    <b>Click on the map to set a point</b>
                </div>
            </div>
            <div style="float:left;">
                <div style="margin-right:35px;">
                    <b>Taxon Filter:</b> <input id="taxa" name="taxa" type="text" value="<?php echo $taxa; ?>" />
                    <input id="tid" name="tid" type="hidden" value="<?php echo $tid; ?>" />
                </div>
                <div style="margin-top:5px;">
                    <b>Radius:</b>
                    <input id="radius" name="radius" value="<?php echo $GLOBALS['DYN_CHECKLIST_RADIUS']; ?>" type="text" style="width:140px;" onchange="setRadiusCircle();" />
                    <select id="radiusunits" name="radiusunits" onchange="setRadiusCircle();">
                        <option value="km">Kilometers</option>
                        <option value="mi">Miles</option>
                    </select>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>
    </form>
    <?php include_once(__DIR__ . '/../spatial/viewerElement.php'); ?>
    <div style="clear:both;width:100%;height:40px;"></div>
</div>
<?php
include_once(__DIR__ . '/../footer.php');
include_once(__DIR__ . '/../config/footer-includes.php');
?>
<script type="text/javascript">
    const selectInteraction = new ol.interaction.Select({
        layers: layersArr,
        condition: function (evt) {
            return (evt.type === 'click' && activeLayer === 'select' && !evt.originalEvent.altKey && !evt.originalEvent.shiftKey);
        },
        style: new ol.style.Style({
            fill: new ol.style.Fill({
                color: getRgbaStrFromHexOpacity((SPATIAL_SHAPES_SELECTIONS_FILL_COLOR),SPATIAL_SHAPES_SELECTIONS_OPACITY)
            }),
            stroke: new ol.style.Stroke({
                color: getRgbaStrFromHexOpacity((SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR),1),
                width: SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH
            }),
            image: new ol.style.Circle({
                radius: SPATIAL_SHAPES_POINT_RADIUS,
                stroke: new ol.style.Stroke({
                    color: getRgbaStrFromHexOpacity((SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR),1),
                    width: (SPATIAL_SHAPES_BORDER_WIDTH + 2)
                }),
                fill: new ol.style.Fill({
                    color: getRgbaStrFromHexOpacity((SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR),1)
                })
            })
        }),
        toggleCondition: ol.events.condition.click
    });
    map.addInteraction(selectInteraction);
    const selectedFeatures = selectInteraction.getFeatures();

    draw = new ol.interaction.Draw({
        source: vectorsource,
        type: 'Point'
    });
    draw.on('drawend', function(evt){
        const featureClone = evt.feature.clone();
        const geoJSONFormat = new ol.format.GeoJSON();
        vectorsource.clear();
        selectedFeatures.clear();
        const selectiongeometry = featureClone.getGeometry();
        const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
        const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
        let pointCoords = JSON.parse(geojsonStr).coordinates;
        document.getElementById("latbox").value = pointCoords[1];
        document.getElementById("lngbox").value = pointCoords[0];
        selectedFeatures.push(evt.feature);
        if(document.getElementById("radius").value && !isNaN(document.getElementById("radius").value)){
            setRadiusCircle();
        }
    });
    map.addInteraction(draw);

    function setRadiusCircle(){
        radiuscirclesource.clear();
        const latVal = document.getElementById("latbox").value;
        const longVal = document.getElementById("lngbox").value;
        const radiusunits = document.getElementById("radiusunits").value;
        let radius = document.getElementById("radius").value;
        if(latVal && longVal && radius){
            if(radiusunits === 'mi'){
                radius = radius * 1609.34;
            }
            else{
                radius = radius * 1000;
            }
            const centerCoords = ol.proj.fromLonLat([longVal, latVal]);
            const circle = new ol.geom.Circle(centerCoords);
            circle.setRadius(Number(radius));
            const edgeCoordinate = [centerCoords[0] + radius, centerCoords[1]];
            const fixedcenter = ol.proj.transform(centerCoords, 'EPSG:3857', 'EPSG:4326');
            const fixededgeCoordinate = ol.proj.transform(edgeCoordinate, 'EPSG:3857', 'EPSG:4326');
            const groundRadius = turf.distance([fixedcenter[0], fixedcenter[1]], [fixededgeCoordinate[0], fixededgeCoordinate[1]]);
            document.getElementById("groundradiusbox").value = groundRadius;
            const circleFeature = new ol.Feature(circle);
            radiuscirclesource.addFeature(circleFeature);
            document.mapForm.buildchecklistbutton.disabled = false;
        }
        else{
            document.getElementById("groundradiusbox").value = '';
            document.mapForm.buildchecklistbutton.disabled = true;
        }
    }
</script>
</body>
</html>
