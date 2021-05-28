<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once($GLOBALS['SERVER_ROOT'].'/classes/DynamicChecklistManager.php');
header('Content-Type: text/html; charset='.$GLOBALS['CHARSET']);

$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:0;
$taxa = array_key_exists('taxa',$_REQUEST)?$_REQUEST['taxa']:'';
$interface = array_key_exists('interface',$_REQUEST)&&$_REQUEST['interface']?htmlspecialchars($_REQUEST['interface']):'checklist';

$dynClManager = new DynamicChecklistManager();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> - Dynamic Checklist Generator</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/ol.css?ver=2" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialviewerbase.css?ver=20210415" type="text/css" rel="stylesheet" />
    <style type="text/css">
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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/ol.js?ver=4" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/spatial.module.js?ver=20210527" type="text/javascript"></script>
    <script type="text/javascript">
        var submitCoord = false;

        $(document).ready(function() {
            $( "#taxa" ).autocomplete({
                source: function( request, response ) {
                    $.getJSON( "rpc/speciessuggest.php", { term: request.term, level: 'high' }, response );
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
include($GLOBALS['SERVER_ROOT'].'/header.php');
?>
<div class='navpath'>
    <a href='../index.php'>Home</a> &gt;
    <b>Dynamic Map</b>
</div>
<div id='innertext'>
    <form name="mapForm" action="dynamicchecklist.php" method="post" onsubmit="return checkForm();">
        <div style="width:95%;margin-left:auto;margin-right:auto;">
            Pan, zoom and click on map to capture coordinates, then submit coordinates to build a species list.
            <span id="moredetails" style="cursor:pointer;color:blue;font-size:80%;" onclick="this.style.display='none';document.getElementById('moreinfo').style.display='inline';document.getElementById('lessdetails').style.display='inline';">
                        More Details
                    </span>
            <span id="moreinfo" style="display:none;">
                        If a radius is defined, species lists are generated using occurrence data collected within the defined area.
                        If a radius is not supplied, the area is sampled in concentric rings until the sample size is determined to
                        best represent the local species diversity. In other words, poorly collected areas will have a larger radius sampled.
                        Setting the taxon filter will limit the return to species found within that taxonomic group.
                    </span>
            <span id="lessdetails" style="cursor:pointer;color:blue;font-size:80%;display:none;" onclick="this.style.display='none';document.getElementById('moreinfo').style.display='none';document.getElementById('moredetails').style.display='inline';">
                        Less Details
                    </span>
        </div>
        <div style="width:95%;margin-left:auto;margin-right:auto;margin-top:5px;">
            <div style="float:left;width:300px;">
                <div>
                    <input type="submit" name="buildchecklistbutton" value="Build Checklist" disabled />
                    <input type="hidden" name="interface" value="<?php echo $interface; ?>" />
                    <input type="hidden" id="latbox" name="lat" value="" />
                    <input type="hidden" id="lngbox" name="lng" value="" />
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
                    <b>Radius (optional):</b>
                    <input id="radius" name="radius" value="" type="text" style="width:140px;" onchange="setRadiusCircle();" />
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
include_once($GLOBALS['SERVER_ROOT'].'/footer.php');
?>
<script type="text/javascript">
    const selectInteraction = new ol.interaction.Select({
        layers: [vectorlayer],
        condition: function (evt) {
            return (evt.type === 'click' && activeLayer === 'select' && !evt.originalEvent.altKey && !evt.originalEvent.shiftKey);
        },
        style: new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(255,255,255,0.5)'
            }),
            stroke: new ol.style.Stroke({
                color: 'rgba(0,153,255,1)',
                width: 5
            }),
            image: new ol.style.Circle({
                radius: 7,
                stroke: new ol.style.Stroke({
                    color: 'rgba(0,153,255,1)',
                    width: 2
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(0,153,255,1)'
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
        document.mapForm.buildchecklistbutton.disabled = false;
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
            const circleFeature = new ol.Feature(circle);
            radiuscirclesource.addFeature(circleFeature);
        }
    }
</script>
</body>
</html>
