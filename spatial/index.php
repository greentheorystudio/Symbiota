<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../config/includes/searchVarDefault.php');
include_once(__DIR__ . '/../classes/SpatialModuleManager.php');
header('Content-Type: text/html; charset=' .$CHARSET);
ini_set('max_execution_time', 180);

$windowType = array_key_exists('windowtype',$_REQUEST)?$_REQUEST['windowtype']:'analysis';

if(file_exists(__DIR__ . '/../config/includes/searchVarCustom.php')){
    include(__DIR__ . '/../config/includes/searchVarCustom.php');
}

$mapCenter = '[-110.90713, 32.21976]';
if(isset($SPATIAL_INITIAL_CENTER) && $SPATIAL_INITIAL_CENTER) {
    $mapCenter = $SPATIAL_INITIAL_CENTER;
}
$mapZoom = 7;
if(isset($SPATIAL_INITIAL_ZOOM) && $SPATIAL_INITIAL_ZOOM) {
    $mapZoom = $SPATIAL_INITIAL_ZOOM;
}

$catId = array_key_exists('catid',$_REQUEST)?$_REQUEST['catid']:0;
if(!$catId && isset($DEFAULTCATID) && $DEFAULTCATID) {
    $catId = $DEFAULTCATID;
}

$spatialManager = new SpatialModuleManager();

$collList = $spatialManager->getFullCollectionList($catId);
$specArr = ($collList['spec'] ?? null);
$obsArr = ($collList['obs'] ?? null);

$dbArr = array();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title><?php echo $DEFAULT_TITLE; ?> Spatial Module</title>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?<?php echo $CSS_VERSION_LOCAL; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $CLIENT_ROOT; ?>/css/bootstrap.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $CLIENT_ROOT; ?>/css/jquery.mobile-1.4.0.min.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $CLIENT_ROOT; ?>/css/jquery.symbiota.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui_accordian.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $CLIENT_ROOT; ?>/css/ol.css?ver=2" type="text/css" rel="stylesheet" />
    <link href="<?php echo $CLIENT_ROOT; ?>/css/spatialbase.css?ver=17" type="text/css" rel="stylesheet" />
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.popupoverlay.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/ol.js?ver=4" type="text/javascript"></script>
    <script src="https://npmcdn.com/@turf/turf/turf.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/shpjs@latest/dist/shp.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jszip.min.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jscolor/jscolor.js?ver=13" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/stream.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/FileSaver.min.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/html2canvas.min.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/symb/spatial.module.js?ver=276" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            let winHeight = $(window).height();
            winHeight = winHeight + "px";
            document.getElementById('spatialpanel').style.height = winHeight;

            $("#accordion").accordion({
                icons: null,
                collapsible: true,
                heightStyle: "fill"
            });
        });

        $(document).ready(function() {
            $('#criteriatab').tabs({
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
            $('#recordstab').tabs({
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
            $('#vectortoolstab').tabs({
                beforeLoad: function( event, ui ) {
                    $(ui.panel).html("<p>Loading...</p>");
                }
            });
            $('#addLayers').popup({
                transition: 'all 0.3s',
                scrolllock: true
            });
            $('#csvoptions').popup({
                transition: 'all 0.3s',
                scrolllock: true
            });
            $('#mapsettings').popup({
                transition: 'all 0.3s',
                scrolllock: true
            });
            $('#reclassifytool').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
            $('#rastercalctool').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
            $('#vectorizeoverlaytool').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
            $('#loadingOverlay').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                opacity:0.6,
                color:'white',
                blur: false
            });
        });
    </script>
</head>
<body class="mapbody">
<div data-role="page" id="page1">
    <div role="main" class="ui-content">
        <a href="#defaultpanel" id="panelopenbutton" data-role="button" data-inline="true" data-icon="bars">Open</a>
    </div>
    <div data-role="panel" data-dismissible=false class="overflow:hidden;" id="defaultpanel" data-swipe-close=false data-position="left" data-display="overlay" >
        <div class="panel-content">
            <?php include_once('includes/sidepanel.php'); ?>
            <a href="#" id="panelclosebutton" data-rel="close" data-role="button" data-theme="a" data-icon="delete" data-inline="true"></a>
        </div>
    </div>
</div>

<div id="map" class="map">
    <div id="popup" class="ol-popup">
        <a href="#" id="popup-closer" class="ol-popup-closer"></a>
        <div id="popup-content"></div>
    </div>

    <div id="finderpopup" class="ol-popup ol-popup-finder" style="padding:5px;">
        <a href="#" id="finderpopup-closer" style="display:none;"></a>
        <div id="finderpopup-content"></div>
    </div>

    <div id="mapinfo">
        <div id="mapcoords"></div>
        <div id="mapscale_us"></div>
        <div id="mapscale_metric"></div>
    </div>

    <?php include_once('includes/controlpanel.php'); ?>
</div>

<script type="text/javascript">
    const SOLRMODE = '<?php echo $SOLR_MODE; ?>';
    let collectionParams = false;
    let geogParams = false;
    let textParams = false;
    let taxaParams = false;
    let tempOccArr = [];
    let geoPolyArr = [];
    let geoCircleArr = [];
    let searchTermsArr = {};
    let layersArr = [];
    let mouseCoords = [];
    let solrqArr = [];
    let solrgeoqArr = [];
    let selections = [];
    let collSymbology = [];
    let taxaSymbology = [];
    let collKeyArr = [];
    let taxaKeyArr = [];
    let solrqString = '';
    let newsolrqString = '';
    let solroccqString = '';
    let geoCallOut = false;
    let queryRecCnt = 0;
    let draw;
    let clustersource;
    let taxaArr = [];
    let taxontype = '';
    let thes = false;
    let loadPointsEvent = false;
    let taxaCnt = 0;
    let lazyLoadCnt = 20000;
    let clusterDistance = 50;
    let clusterPoints = true;
    let showHeatMap = false;
    let heatMapRadius = 5;
    let heatMapBlur = 15;
    let mapSymbology = 'coll';
    let clusterKey = 'CollectionName';
    let maxFeatureCount;
    let currentResolution;
    let activeLayer = 'none';
    let shapeActive = false;
    let pointActive = false;
    let spiderCluster;
    let spiderFeature;
    let hiddenClusters = [];
    let clickedFeatures = [];
    let dragDrop1 = false;
    let dragDrop2 = false;
    let dragDrop3 = false;
    let dragDropTarget = '';
    let dsOldestDate = '';
    let dsNewestDate = '';
    let tsOldestDate = '';
    let tsNewestDate = '';
    let dateSliderActive = false;
    let sliderdiv = '';
    let rasterLayers = [];
    let overlayLayers = [];
    let vectorizeLayers = [];
    let loadingTimer = 0;
    let loadingComplete = true;
    let returnClusters = false;
    let dsAnimDuration = '';
    let dsAnimTime = '';
    let dsAnimImageSave = false;
    let dsAnimReverse = false;
    let dsAnimDual = false;
    let dsAnimLow = '';
    let dsAnimHigh = '';
    let dsAnimStop = true;
    let dsAnimation = '';
    let zipFile = '';
    let zipFolder = '';
    const SOLRFields = 'occid,collid,catalogNumber,otherCatalogNumbers,family,sciname,tidinterpreted,scientificNameAuthorship,identifiedBy,' +
        'dateIdentified,typeStatus,recordedBy,recordNumber,eventDate,displayDate,coll_year,coll_month,coll_day,habitat,associatedTaxa,' +
        'cultivationStatus,country,StateProvince,county,municipality,locality,localitySecurity,localitySecurityReason,geo,minimumElevationInMeters,' +
        'maximumElevationInMeters,labelProject,InstitutionCode,CollectionCode,CollectionName,CollType,thumbnailurl,accFamily';
    const dragDropStyle = {
        'Point': new ol.style.Style({
            image: new ol.style.Circle({
                fill: new ol.style.Fill({
                    color: 'rgba(255,255,0,0.5)'
                }),
                radius: 5,
                stroke: new ol.style.Stroke({
                    color: '#ff0',
                    width: 1
                })
            })
        }),
        'LineString': new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: '#f00',
                width: 3
            })
        }),
        'Polygon': new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(170,170,170,0.3)'
            }),
            stroke: new ol.style.Stroke({
                color: '#000000',
                width: 1
            })
        }),
        'MultiPoint': new ol.style.Style({
            image: new ol.style.Circle({
                fill: new ol.style.Fill({
                    color: 'rgba(255,0,255,0.5)'
                }),
                radius: 5,
                stroke: new ol.style.Stroke({
                    color: '#f0f',
                    width: 1
                })
            })
        }),
        'MultiLineString': new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: '#0f0',
                width: 3
            })
        }),
        'MultiPolygon': new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(170,170,170,0.3)'
            }),
            stroke: new ol.style.Stroke({
                color: '#000000',
                width: 1
            })
        })
    };

    const popupcontainer = document.getElementById('popup');
    const popupcontent = document.getElementById('popup-content');
    const popupcloser = document.getElementById('popup-closer');
    const finderpopupcontainer = document.getElementById('finderpopup');
    const finderpopupcontent = document.getElementById('finderpopup-content');
    const finderpopupcloser = document.getElementById('finderpopup-closer');
    const typeSelect = document.getElementById('drawselect');

    const popupoverlay = new ol.Overlay({
        element: popupcontainer,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    });

    popupcloser.onclick = function() {
        popupoverlay.setPosition(undefined);
        popupcloser.blur();
        return false;
    };

    const finderpopupoverlay = new ol.Overlay({
        element: finderpopupcontainer,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    });

    finderpopupcloser.onclick = function(){
        finderpopupoverlay.setPosition(undefined);
        finderpopupcloser.blur();
        return false;
    };

    const mapProjection = new ol.proj.Projection({
        code: 'EPSG:3857'
    });

    const wgs84Projection = new ol.proj.Projection({
        code: 'EPSG:4326',
        units: 'degrees'
    });

    const projection = ol.proj.get('EPSG:4326');
    const projectionExtent = projection.getExtent();
    const tileSize = 512;
    const maxResolution = ol.extent.getWidth(projectionExtent) / (tileSize * 2);
    const resolutions = new Array(16);
    for (let z = 0; z < 16; ++z) {
        resolutions[z] = maxResolution / Math.pow(2, z);
    }

    const baselayer = new ol.layer.Tile({
        source: new ol.source.XYZ({
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
            crossOrigin: 'anonymous'
        })
    });

    const selectsource = new ol.source.Vector({
        wrapX: true
    });
    const selectlayer = new ol.layer.Vector({
        source: selectsource,
        style: new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(255,255,255,0.4)'
            }),
            stroke: new ol.style.Stroke({
                color: '#3399CC',
                width: 2
            }),
            image: new ol.style.Circle({
                radius: 7,
                stroke: new ol.style.Stroke({
                    color: '#3399CC',
                    width: 2
                }),
                fill: new ol.style.Fill({
                    color: 'rgba(255,255,255,0.4)'
                })
            })
        })
    });

    let pointvectorsource = new ol.source.Vector({
        wrapX: true
    });
    const pointvectorlayer = new ol.layer.Vector({
        source: pointvectorsource
    });

    const heatmaplayer = new ol.layer.Heatmap({
        source: pointvectorsource,
        weight: function (feature) {
            let showPoint = true;
            if (dateSliderActive) {
                showPoint = validateFeatureDate(feature);
            }
            if (showPoint) {
                return 1;
            } else {
                return 0;
            }
        },
        gradient: ['#00f', '#0ff', '#0f0', '#ff0', '#f00'],
        blur: parseInt(heatMapBlur.toString(), 10),
        radius: parseInt(heatMapRadius.toString(), 10),
        visible: false
    });

    const blankdragdropsource = new ol.source.Vector({
        wrapX: true
    });
    const dragdroplayer1 = new ol.layer.Vector({
        source: blankdragdropsource
    });
    const dragdroplayer2 = new ol.layer.Vector({
        source: blankdragdropsource
    });
    const dragdroplayer3 = new ol.layer.Vector({
        source: blankdragdropsource
    });

    const spiderLayer = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: new ol.Collection(),
            useSpatialIndex: true
        })
    });

    layersArr['base'] = baselayer;
    layersArr['dragdrop1'] = dragdroplayer1;
    layersArr['dragdrop2'] = dragdroplayer2;
    layersArr['dragdrop3'] = dragdroplayer3;
    layersArr['select'] = selectlayer;
    layersArr['pointv'] = pointvectorlayer;
    layersArr['heat'] = heatmaplayer;
    layersArr['spider'] = spiderLayer;

    const zoomslider = new ol.control.ZoomSlider();
    const scaleLineControl_us = new ol.control.ScaleLine({target: document.getElementById('mapscale_us'), units: 'us'});
    const scaleLineControl_metric = new ol.control.ScaleLine({
        target: document.getElementById('mapscale_metric'),
        units: 'metric'
    });
    const dragAndDropInteraction = new ol.interaction.DragAndDrop({
        formatConstructors: [
            ol.format.GPX,
            ol.format.GeoJSON,
            ol.format.IGC,
            ol.format.KML,
            ol.format.TopoJSON
        ]
    });

    const selectInteraction = new ol.interaction.Select({
        layers: [layersArr['select']],
        condition: function (evt) {
            return (evt.type === 'click' && activeLayer === 'select' && !evt.originalEvent.altKey);
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

    const pointInteraction = new ol.interaction.Select({
        layers: [layersArr['pointv'], layersArr['spider']],
        condition: function (evt) {
            if (evt.type === 'click' && activeLayer === 'pointv') {
                if (!evt.originalEvent.altKey) {
                    if (spiderCluster) {
                        const spiderclick = map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                            spiderFeature = feature;
                            if (feature && layer === layersArr['spider']) {
                                return feature;
                            }
                        });
                        if (!spiderclick) {
                            const blankSource = new ol.source.Vector({
                                features: new ol.Collection(),
                                useSpatialIndex: true
                            });
                            layersArr['spider'].setSource(blankSource);
                            for (const i in hiddenClusters) {
                                if(hiddenClusters.hasOwnProperty(i)){
                                    showFeature(hiddenClusters[i]);
                                }
                            }
                            hiddenClusters = [];
                            spiderCluster = false;
                            spiderFeature = '';
                            layersArr['pointv'].getSource().changed();
                        }
                    }
                    return true;
                } else if (evt.originalEvent.altKey) {
                    map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                        if (feature) {
                            if (spiderCluster && layer === layersArr['spider']) {
                                clickedFeatures.push(feature);
                                return feature;
                            } else if (layer === layersArr['pointv']) {
                                clickedFeatures.push(feature);
                                return feature;
                            }
                        }
                    });
                    return false;
                }
            } else {
                return false;
            }
        },
        toggleCondition: ol.events.condition.click,
        multi: true,
        hitTolerance: 2,
        style: getPointStyle
    });

    function editVectorLayers(c,title){
        const layer = c.value;
        if(c.checked === true){
            const layerName = '<?php echo ($GEOSERVER_LAYER_WORKSPACE ?? ''); ?>:'+layer;
            const layerSourceName = layer + 'Source';
            layersArr[layerSourceName] = new ol.source.ImageWMS({
                url: 'rpc/GeoServerConnector.php',
                params: {'LAYERS':layerName, 'datatype':'vector'},
                serverType: 'geoserver',
                crossOrigin: 'anonymous',
                imageLoadFunction: function(image, src) {
                    imagePostFunction(image, src);
                }
            });
            layersArr[layer] = new ol.layer.Image({
                source: layersArr[layerSourceName]
            });
            layersArr[layer].setOpacity(0.3);
            map.addLayer(layersArr[layer]);
            refreshLayerOrder();
            addLayerToSelList(layer,title);
        }
        else{
            map.removeLayer(layersArr[layer]);
            removeLayerToSelList(layer);
        }
    }

    function vectorizeRaster(){
        showWorking();
        const overlay = document.getElementById("vectorizesourcelayer").value;
        const overlaySource = overlayLayers[overlay]['source'];
        const features = selectInteraction.getFeatures().getArray();
        const boundsFeature = features[0].clone();
        const geoJSONFormat = new ol.format.GeoJSON();
        const geometry = boundsFeature.getGeometry();
        const fixedgeometry = geometry.transform(mapProjection, wgs84Projection);
        const geojsonStr = geoJSONFormat.writeGeometry(fixedgeometry);
        const xmlContent = generateWPSPolyExtractXML(overlayLayers[overlay]['values'], overlaySource, geojsonStr);
        const http = new XMLHttpRequest();
        const url = "rpc/GeoServerConnector.php";
        const params = 'REQUEST=wps&xmlrequest=' + xmlContent;
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                //console.log(http.responseText);
                const features = geoJSONFormat.readFeatures(http.responseText, {
                    featureProjection: 'EPSG:3857'
                });
                selectsource.addFeatures(features);
                document.getElementById("selectlayerselect").value = 'select';
                setActiveLayer();
            }
            hideWorking();
        };
        http.send(params);
    }

    function checkRasterCalcForm(){
        const outputName = document.getElementById("rastercalcOutputName").value;
        const layer1 = document.getElementById("rastcalcoverlay1").value;
        const operator = document.getElementById("rastcalcoperator").value;
        const layer2 = document.getElementById("rastcalcoverlay2").value;
        const colorVal = document.getElementById("rastcalccolor").value;
        if(layer1 === "" || layer2 === "") {
            alert("Please select overlay layers to calculate.");
        }
        else if(outputName === "") {
            alert("Please enter a name for the output overlay.");
        }
        else if(layersArr[outputName]) {
            alert("The name for the output you entered is already being used by another layer. Please enter a different name.");
        }
        else if(operator === "") {
            alert("Please select operator for calculation.");
        }
        else if(colorVal === "FFFFFF") {
            alert("Please select a color other than white for this overlay.");
        }
        else{
            $("#rastercalctool").popup("hide");
            calculateRasters();
        }
    }

    function calculateRasters(){
        const layer1 = document.getElementById("rastcalcoverlay1").value;
        const layer2 = document.getElementById("rastcalcoverlay2").value;
        const operator = document.getElementById("rastcalcoperator").value;
        const hexColor = document.getElementById("rastcalccolor").value;
        const rgbColorArr = hexToRgb('#' + hexColor);
        let outputName = document.getElementById("rastercalcOutputName").value;
        outputName = outputName.replace(" ","_");
        overlayLayers[outputName] = [];
        overlayLayers[outputName]['id'] = outputName;
        overlayLayers[outputName]['title'] = outputName;
        overlayLayers[outputName]['source'] = '';

        const layerRasterSourceName = outputName + 'RasterSource';
        layersArr[layerRasterSourceName] = new ol.source.Raster({
            sources: [layersArr[layer1].getSource(), layersArr[layer2].getSource()],
            operationType: 'pixel',
            operation: function (pixels, data) {
                let result;
                const operator = data.operator;
                const value1 = pixels[0][4];
                const value2 = pixels[1][4];
                if(operator === '+') {
                    result = value1 + value2;
                }
                else if(operator === '-') {
                    result = value1 - value2;
                }
                else if(operator === '*') {
                    result = value1 * value2;
                }
                else if(operator === '/') {
                    result = value1 / value2;
                }
                if(result > 0){
                    let inputPixel = [];
                    inputPixel[0] = 123; //rgbarr['r'];
                    inputPixel[1] = 203; //rgbarr['g'];
                    inputPixel[2] = 122; //rgbarr['b'];
                    inputPixel[3] = 255;
                    inputPixel[4] = result;
                    return inputPixel;
                }
                return [0, 0, 0, 0, 0];
            },
            beforeoperations: function(event) {
                event.data['operator'] = operator;
                event.data['rgbarr'] = rgbColorArr;
            }
        });
        layersArr[outputName] = new ol.layer.Image({
            source: layersArr[layerRasterSourceName]
        });

        layersArr[outputName].setOpacity(0.4);
        map.addLayer(layersArr[outputName]);
        refreshLayerOrder();
        const infoArr = [];
        infoArr['Name'] = outputName;
        infoArr['layerType'] = 'raster';
        infoArr['Title'] = outputName;
        infoArr['Abstract'] = '';
        infoArr['DefaultCRS'] = '';
        buildLayerTableRow(infoArr,true);
    }

    function clearRasterCalcForm() {
        document.getElementById("rastcalcoverlay1").selectedIndex = 0;
        document.getElementById("rastcalcoperator").selectedIndex = 0;
        document.getElementById("rastcalcoverlay2").selectedIndex = 0;
        document.getElementById("rastcalccolor").value = "FFFFFF";
    }

    function reclassifyRaster(){
        const rasterLayer = document.getElementById("reclassifysourcelayer").value;
        let outputName = document.getElementById("reclassifyOutputName").value;
        outputName = outputName.replace(" ","_");
        overlayLayers[outputName] = [];
        overlayLayers[outputName]['id'] = outputName;
        overlayLayers[outputName]['title'] = outputName;
        overlayLayers[outputName]['source'] = rasterLayer;
        overlayLayers[outputName]['values'] = [];
        overlayLayers[outputName]['values']['rasmin'] = document.getElementById('reclassifyRasterMin').value;
        overlayLayers[outputName]['values']['rasmax'] = document.getElementById('reclassifyRasterMax').value;
        overlayLayers[outputName]['values']['color'] = document.getElementById('reclassifyColorVal').value;

        const layerName = '<?php echo ($GEOSERVER_LAYER_WORKSPACE ?? ''); ?>:'+rasterLayer;
        const layerTileSourceName = outputName + 'Source';
        const layerRasterSourceName = outputName + 'RasterSource';
        const sldContent = generateReclassifySLD(overlayLayers[outputName]['values'], layerName);
        layersArr[layerTileSourceName] = new ol.source.TileWMS({
            url: 'rpc/GeoServerConnector.php',
            params: {'LAYERS':layerName, 'STYLES':'reclassify_style', 'SLD_BODY':sldContent, 'datatype':'raster'},
            serverType: 'geoserver',
            crossOrigin: 'anonymous',
            imageLoadFunction: function(image, src) {
                imagePostFunction(image, src);
            }
        });
        layersArr[layerRasterSourceName] = new ol.source.Raster({
            sources: [layersArr[layerTileSourceName]],
            operationType: 'pixel',
            operation: function (pixels) {
                const inputPixel = pixels[0];
                if((inputPixel[0] && inputPixel[1] && inputPixel[2])){
                    const pixr = inputPixel[0];
                    const pixg = inputPixel[1];
                    const pixb = inputPixel[2];
                    if(pixr === 255 && pixg === 255 && pixb === 255){
                        return [0, 0, 0, 0];
                    }
                    else if(pixr === 0 && pixg === 0 && pixb === 0){
                        return [0, 0, 0, 0];
                    }
                    else{
                        return inputPixel;
                    }
                }
                return [0, 0, 0, 0];
            }
        });
        layersArr[outputName] = new ol.layer.Image({
            source: layersArr[layerRasterSourceName]
        });

        layersArr[outputName].setOpacity(0.4);
        map.addLayer(layersArr[outputName]);
        refreshLayerOrder();
        const infoArr = [];
        infoArr['Name'] = outputName;
        infoArr['raster'] = 'vector';
        infoArr['Title'] = outputName;
        infoArr['Abstract'] = '';
        infoArr['DefaultCRS'] = '';
        buildLayerTableRow(infoArr,true);
        vectorizeLayers[outputName] = outputName;
    }

    function editRasterLayers(c,title){
        const layer = c.value;
        if(c.checked === true){
            const layerName = '<?php echo ($GEOSERVER_LAYER_WORKSPACE ?? ''); ?>:'+layer;
            const layerTileSourceName = layer + 'Source';
            const layerRasterSourceName = layer + 'RasterSource';
            layersArr[layerTileSourceName] = new ol.source.TileWMS({
                url: 'rpc/GeoServerConnector.php',
                params: {'LAYERS':layerName, 'datatype':'raster'},
                serverType: 'geoserver',
                crossOrigin: 'anonymous',
                imageLoadFunction: function(image, src) {
                    imagePostFunction(image, src);
                }
            });
            layersArr[layerRasterSourceName] = new ol.source.Raster({
                sources: [layersArr[layerTileSourceName]],
                operationType: 'pixel',
                operation: function (pixels) {
                    return pixels[0];
                }
            });
            layersArr[layer] = new ol.layer.Image({
                source: layersArr[layerRasterSourceName]
            });

            layersArr[layer].setOpacity(0.4);
            map.addLayer(layersArr[layer]);
            refreshLayerOrder();
            addLayerToSelList(layer,title);
        }
        else{
            map.removeLayer(layersArr[layer]);
            removeLayerToSelList(layer);
        }
    }

    const mapView = new ol.View({
        zoom: <?php echo $mapZoom; ?>,
        projection: 'EPSG:3857',
        minZoom: 2.5,
        maxZoom: 19,
        center: ol.proj.transform(<?php echo $mapCenter; ?>, 'EPSG:4326', 'EPSG:3857'),
    });

    const map = new ol.Map({
        view: mapView,
        target: 'map',
        controls: ol.control.defaults().extend([
            new ol.control.FullScreen()
        ]),
        layers: [
            layersArr['base'],
            layersArr['dragdrop1'],
            layersArr['dragdrop2'],
            layersArr['dragdrop3'],
            layersArr['select'],
            layersArr['pointv'],
            layersArr['heat'],
            layersArr['spider']
        ],
        overlays: [popupoverlay,finderpopupoverlay],
        renderer: 'canvas'
    });

    const mousePositionControl = new ol.control.MousePosition({
        coordinateFormat: coordFormat(),
        projection: 'EPSG:4326',
        className: 'custom-mouse-position',
        target: document.getElementById('mapcoords'),
        undefinedHTML: '&nbsp;'
    });

    map.addControl(zoomslider);
    map.addControl(scaleLineControl_us);
    map.addControl(scaleLineControl_metric);
    map.addControl(mousePositionControl);
    map.addInteraction(selectInteraction);
    map.addInteraction(pointInteraction);
    map.addInteraction(dragAndDropInteraction);

    const selectedFeatures = selectInteraction.getFeatures();
    const selectedPointFeatures = pointInteraction.getFeatures();

    selectedPointFeatures.on('add', function() {
        setSpatialParamBox();
        buildQueryStrings();
    });

    selectedPointFeatures.on('remove', function() {
        setSpatialParamBox();
        buildQueryStrings();
    });

    map.getView().on('change:resolution', function() {
        if(spiderCluster){
            const source = layersArr['spider'].getSource();
            source.clear();
            const blankSource = new ol.source.Vector({
                features: new ol.Collection(),
                useSpatialIndex: true
            });
            layersArr['spider'].setSource(blankSource);
            for(const i in hiddenClusters){
                if(hiddenClusters.hasOwnProperty(i)){
                    showFeature(hiddenClusters[i]);
                }
            }
            hiddenClusters = [];
            spiderCluster = '';
            layersArr['pointv'].getSource().changed();
        }
    });

    function getArrayBuffer(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.readAsArrayBuffer(file);
            reader.onload = () => {
                const arrayBuffer = reader.result;
                const bytes = new Uint8Array(arrayBuffer);
                resolve(bytes);
            };
        });
    }

    dragAndDropInteraction.on('addfeatures', function(event) {
        let filename = event.file.name.split('.');
        const fileType = filename.pop();
        filename = filename.join("");
        if(fileType === 'geojson' || fileType === 'kml' || fileType === 'zip'){
            if(fileType === 'geojson' || fileType === 'kml'){
                if(setDragDropTarget()){
                    const infoArr = [];
                    infoArr['Name'] = dragDropTarget;
                    infoArr['layerType'] = 'vector';
                    infoArr['Title'] = filename;
                    infoArr['Abstract'] = '';
                    infoArr['DefaultCRS'] = '';
                    const sourceIndex = dragDropTarget + 'Source';
                    let features = event.features;
                    if(fileType === 'kml'){
                        const geoJSONFormat = new ol.format.GeoJSON();
                        features = geoJSONFormat.readFeatures(geoJSONFormat.writeFeatures(features));
                    }
                    layersArr[sourceIndex] = new ol.source.Vector({
                        features: features
                    });
                    layersArr[dragDropTarget].setStyle(getDragDropStyle);
                    layersArr[dragDropTarget].setSource(layersArr[sourceIndex]);
                    buildLayerTableRow(infoArr,true);
                    map.getView().fit(layersArr[sourceIndex].getExtent());
                    toggleLayerTable();
                }
            }
            else if(fileType === 'zip'){
                if(setDragDropTarget()){
                    getArrayBuffer(event.file).then((data) => {
                        shp(data).then((geojson) => {
                            const infoArr = [];
                            infoArr['Name'] = dragDropTarget;
                            infoArr['layerType'] = 'vector';
                            infoArr['Title'] = filename;
                            infoArr['Abstract'] = '';
                            infoArr['DefaultCRS'] = '';
                            const sourceIndex = dragDropTarget + 'Source';
                            const format = new ol.format.GeoJSON();
                            const features = format.readFeatures(geojson, {
                                featureProjection: 'EPSG:3857'
                            });
                            layersArr[sourceIndex] = new ol.source.Vector({
                                features: features
                            });
                            layersArr[dragDropTarget].setStyle(getDragDropStyle);
                            layersArr[dragDropTarget].setSource(layersArr[sourceIndex]);
                            buildLayerTableRow(infoArr,true);
                            map.getView().fit(layersArr[sourceIndex].getExtent());
                            toggleLayerTable();
                        });
                    });
                }
            }
        }
        else{
            alert('The drag and drop file loading only supports GeoJSON, kml, and shapefile zip archives.');
        }
    });

    pointInteraction.on('select', function(event) {
        let clusterCnt;
        let newfeature;
        let cFeatures;
        const newfeatures = event.selected;
        const zoomLevel = map.getView().getZoom();
        if (newfeatures.length > 0) {
            if (zoomLevel < 17) {
                const extent = ol.extent.createEmpty();
                if (newfeatures.length > 1) {
                    for (const n in newfeatures) {
                        if(newfeatures.hasOwnProperty(n)){
                            const nfeature = newfeatures[n];
                            pointInteraction.getFeatures().remove(nfeature);
                            if(nfeature.get('features')){
                                cFeatures = nfeature.get('features');
                                for (let f in cFeatures) {
                                    if(cFeatures.hasOwnProperty(f)){
                                        ol.extent.extend(extent, cFeatures[f].getGeometry().getExtent());
                                    }
                                }
                            }
                            else{
                                ol.extent.extend(extent, nfeature.getGeometry().getExtent());
                            }
                        }
                    }
                    map.getView().fit(extent, map.getSize());
                }
                else {
                    newfeature = newfeatures[0];
                    pointInteraction.getFeatures().remove(newfeature);
                    if (newfeature.get('features')) {
                        clusterCnt = newfeature.get('features').length;
                        if (clusterCnt > 1) {
                            cFeatures = newfeature.get('features');
                            for (let f in cFeatures) {
                                if(cFeatures.hasOwnProperty(f)){
                                    ol.extent.extend(extent, cFeatures[f].getGeometry().getExtent());
                                }
                            }
                            map.getView().fit(extent, map.getSize());
                        }
                        else {
                            processPointSelection(newfeature);
                        }
                    }
                    else {
                        processPointSelection(newfeature);
                    }
                }
            }
            else {
                if (newfeatures.length > 1 && !spiderFeature) {
                    pointInteraction.getFeatures().clear();
                    if(!spiderCluster){
                        spiderifyPoints(newfeatures);
                    }
                }
                else {
                    if(spiderFeature){
                        newfeature = spiderFeature;
                        spiderFeature = '';
                    }
                    else{
                        newfeature = newfeatures[0];
                    }
                    pointInteraction.getFeatures().clear();
                    if (newfeature.get('features')) {
                        clusterCnt = newfeatures[0].get('features').length;
                        if (clusterCnt > 1 && !spiderCluster) {
                            spiderifyPoints(newfeatures);
                        }
                        else {
                            processPointSelection(newfeature);
                        }
                    }
                    else {
                        processPointSelection(newfeature);
                    }
                }
            }
        }
    });

    selectedFeatures.on('add', function() {
        setSpatialParamBox();
        buildQueryStrings();
    });

    selectedFeatures.on('remove', function() {
        setSpatialParamBox();
        buildQueryStrings();
    });

    selectsource.on('change', function() {
        if(!draw){
            const featureCnt = selectsource.getFeatures().length;
            if(featureCnt > 0){
                if(!shapeActive){
                    const infoArr = [];
                    infoArr['Name'] = 'select';
                    infoArr['layerType'] = 'vector';
                    infoArr['Title'] = 'Shapes';
                    infoArr['Abstract'] = '';
                    infoArr['DefaultCRS'] = '';
                    buildLayerTableRow(infoArr,true);
                    shapeActive = true;
                }
            }
            else{
                if(shapeActive){
                    removeLayerToSelList('select');
                    shapeActive = false;
                }
            }
        }
    });

    function loadPointWFSLayer(index){
        pointvectorsource = new ol.source.Vector({
            loader: function(extent, resolution, projection) {
                let processed = 0;
                do{
                    lazyLoadPoints(index,function(res){
                        const format = new ol.format.GeoJSON();
                        const features = format.readFeatures(res, {
                            featureProjection: 'EPSG:3857'
                        });
                        primeSymbologyData(features);
                        pointvectorsource.addFeatures(features);
                        if(loadPointsEvent){
                            const pointextent = pointvectorsource.getExtent();
                            map.getView().fit(pointextent,map.getSize());
                        }
                    });
                    processed = processed + lazyLoadCnt;
                    index++;
                }
                while(processed < queryRecCnt);
            }
        });

        clustersource = new ol.source.PropertyCluster({
            distance: clusterDistance,
            source: pointvectorsource,
            clusterkey: clusterKey,
            indexkey: 'occid',
            geometryFunction: function(feature){
                if(dateSliderActive){
                    if(validateFeatureDate(feature)){
                        return feature.getGeometry();
                    }
                    else{
                        return null;
                    }
                }
                else{
                    return feature.getGeometry();
                }
            }
        });

        layersArr['pointv'].setStyle(getPointStyle);
        if(clusterPoints){
            layersArr['pointv'].setSource(clustersource);
        }
        else{
            layersArr['pointv'].setSource(pointvectorsource);
        }
        layersArr['heat'].setSource(pointvectorsource);
        if(showHeatMap){
            layersArr['heat'].setVisible(true);
        }
    }

    map.on('singleclick', function(evt) {
        let infoHTML;
        let url;
        let viewResolution;
        let layerIndex;
        if(evt.originalEvent.altKey){
            layerIndex = activeLayer + "Source";
            viewResolution = (mapView.getResolution());
            if(activeLayer !== 'none' && activeLayer !== 'select' && activeLayer !== 'pointv' && activeLayer !== 'dragdrop1' && activeLayer !== 'dragdrop2' && activeLayer !== 'dragdrop3'){
                url = layersArr[layerIndex].getGetFeatureInfoUrl(evt.coordinate, viewResolution, 'EPSG:3857', {'INFO_FORMAT': 'application/json'});
                if (url) {
                    $.ajax({
                        type: "GET",
                        url: url,
                        async: true
                    }).done(function(msg) {
                        if(msg){
                            let infoHTML = '';
                            const infoArr = JSON.parse(msg);
                            const propArr = infoArr['features'][0]['properties'];
                            if(overlayLayers[activeLayer]){
                                const sourceVal = propArr['GRAY_INDEX'];
                                const lowCalVal = overlayLayers[activeLayer]['values']['rasmin'];
                                const highCalVal = overlayLayers[activeLayer]['values']['rasmax'];
                                const calcVal = overlayLayers[activeLayer]['values']['newval'];
                                if(sourceVal >= lowCalVal && sourceVal <= highCalVal){
                                    infoHTML += '<b>Value:</b> '+calcVal+'<br />';
                                }
                                else{
                                    infoHTML += '<b>Value:</b> 0<br />';
                                }
                            }
                            else{
                                //infoHTML += '<b>id:</b> '+infoArr['id']+'<br />';
                                //infoHTML += '<b>geometry:</b> '+infoArr['geometry']+'<br />';
                                for(const key in propArr){
                                    if(propArr.hasOwnProperty(key)){
                                        let valTag = '';
                                        if(key === 'GRAY_INDEX') {
                                            valTag = 'Value';
                                        }
                                        else {
                                            valTag = key;
                                        }
                                        infoHTML += '<b>'+valTag+':</b> '+propArr[key]+'<br />';
                                    }
                                }
                            }
                            popupcontent.innerHTML = infoHTML;
                            popupoverlay.setPosition(evt.coordinate);
                        }
                    });
                }
            }
            else if(activeLayer === 'dragdrop1' || activeLayer === 'dragdrop2' || activeLayer === 'dragdrop3' || activeLayer === 'select'){
                infoHTML = '';
                const feature = map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                    if (layer === layersArr[activeLayer]) {
                        return feature;
                    }
                });
                if(feature){
                    const properties = feature.getKeys();
                    for(let i in properties){
                        if(properties.hasOwnProperty(i) && String(properties[i]) !== 'geometry'){
                            infoHTML += '<b>'+properties[i]+':</b> '+feature.get(properties[i])+'<br />';
                        }
                    }
                    if(infoHTML){
                        popupcontent.innerHTML = infoHTML;
                        popupoverlay.setPosition(evt.coordinate);
                    }
                }
            }
            else if(activeLayer === 'pointv'){
                infoHTML = '';
                let targetFeature = '';
                let iFeature = '';
                if(clickedFeatures.length === 1){
                    targetFeature = clickedFeatures[0];
                }
                if(targetFeature){
                    if(clusterPoints && targetFeature.get('features').length === 1){
                        iFeature = targetFeature.get('features')[0];
                    }
                    else if(!clusterPoints){
                        iFeature = targetFeature;
                    }
                }
                else{
                    return;
                }
                if(iFeature){
                    infoHTML += '<b>occid:</b> '+iFeature.get('occid')+'<br />';
                    infoHTML += '<b>CollectionName:</b> '+(iFeature.get('CollectionName')?iFeature.get('CollectionName'):'')+'<br />';
                    infoHTML += '<b>catalogNumber:</b> '+(iFeature.get('catalogNumber')?iFeature.get('catalogNumber'):'')+'<br />';
                    infoHTML += '<b>otherCatalogNumbers:</b> '+(iFeature.get('otherCatalogNumbers')?iFeature.get('otherCatalogNumbers'):'')+'<br />';
                    infoHTML += '<b>family:</b> '+(iFeature.get('family')?iFeature.get('family'):'')+'<br />';
                    infoHTML += '<b>sciname:</b> '+(iFeature.get('sciname')?iFeature.get('sciname'):'')+'<br />';
                    infoHTML += '<b>recordedBy:</b> '+(iFeature.get('recordedBy')?iFeature.get('recordedBy'):'')+'<br />';
                    infoHTML += '<b>recordNumber:</b> '+(iFeature.get('recordNumber')?iFeature.get('recordNumber'):'')+'<br />';
                    infoHTML += '<b>eventDate:</b> '+(iFeature.get('displayDate')?iFeature.get('displayDate'):'')+'<br />';
                    infoHTML += '<b>habitat:</b> '+(iFeature.get('habitat')?iFeature.get('habitat'):'')+'<br />';
                    infoHTML += '<b>associatedTaxa:</b> '+(iFeature.get('associatedTaxa')?iFeature.get('associatedTaxa'):'')+'<br />';
                    infoHTML += '<b>country:</b> '+(iFeature.get('country')?iFeature.get('country'):'')+'<br />';
                    infoHTML += '<b>StateProvince:</b> '+(iFeature.get('StateProvince')?iFeature.get('StateProvince'):'')+'<br />';
                    infoHTML += '<b>county:</b> '+(iFeature.get('county')?iFeature.get('county'):'')+'<br />';
                    infoHTML += '<b>locality:</b> '+(iFeature.get('locality')?iFeature.get('locality'):'')+'<br />';
                    if(iFeature.get('thumbnailurl')){
                        const thumburl = iFeature.get('thumbnailurl');
                        infoHTML += '<img src="'+thumburl+'" style="height:150px" />';
                    }
                    popupcontent.innerHTML = infoHTML;
                    popupoverlay.setPosition(evt.coordinate);
                }
                else{
                    alert('You clicked on multiple points. The info window can only display data for a single point.');
                }
                clickedFeatures = [];
            }
        }
        else{
            layerIndex = activeLayer + "Source";
            if(activeLayer !== 'none' && activeLayer !== 'select' && activeLayer !== 'pointv'){
                if(activeLayer === 'dragdrop1' || activeLayer === 'dragdrop2' || activeLayer === 'dragdrop3'){
                    map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                        if(layer === layersArr[activeLayer]){
                            try{
                                selectsource.addFeature(feature);
                                document.getElementById("selectlayerselect").value = 'select';
                                setActiveLayer();
                            }
                            catch(e){
                                alert('Feature has already been added to Shapes layer.');
                            }
                        }
                    });
                }
                else{
                    viewResolution = (mapView.getResolution());
                    url = layersArr[layerIndex].getGetFeatureInfoUrl(evt.coordinate, viewResolution, 'EPSG:3857', {'INFO_FORMAT': 'application/json'});
                    selectObjectFromID(url, activeLayer);
                }
            }
        }
    });

    function selectObjectFromID(url,selectLayer){
        $.ajax({
            type: "GET",
            url: url,
            async: true
        }).done(function(msg) {
            if(msg){
                const infoArr = JSON.parse(msg);
                const objID = infoArr['features'][0]['id'];
                const url = 'rpc/GeoServerConnector.php?SERVICE=WFS&VERSION=1.1.0&REQUEST=GetFeature&typename=<?php echo ($GEOSERVER_LAYER_WORKSPACE ?? ''); ?>:'+selectLayer+'&featureid='+objID+'&outputFormat=application/json&srsname=EPSG:3857';
                $.get(url, function(data){
                    const features = new ol.format.GeoJSON().readFeatures(data);
                    if(features){
                        selectsource.addFeatures(features);
                        document.getElementById("selectlayerselect").value = 'select';
                        setActiveLayer();
                    }
                });
            }
        });
    }

    typeSelect.onchange = function() {
        map.removeInteraction(draw);
        changeDraw();
    };

    changeDraw();
</script>

<?php include_once('includes/mapsettings.php'); ?>

<?php include_once('includes/layercontroller.php'); ?>

<?php include_once('includes/csvoptions.php'); ?>

<?php include_once('includes/reclassifytool.php'); ?>

<?php include_once('includes/rastercalculator.php'); ?>

<?php include_once('includes/vectorizeoverlay.php'); ?>

<!-- Data Download Form -->
<div style="display:none;">
    <form name="datadownloadform" id="datadownloadform" action="rpc/datadownloader.php" method="post">
        <input id="starrjson" name="starrjson"  type="hidden" />
        <input id="dh-q" name="dh-q"  type="hidden" />
        <input id="dh-fq" name="dh-fq" type="hidden" />
        <input id="dh-fl" name="dh-fl" type="hidden" />
        <input id="dh-rows" name="dh-rows" type="hidden" />
        <input id="dh-type" name="dh-type" type="hidden" />
        <input id="dh-filename" name="dh-filename" type="hidden" />
        <input id="dh-contentType" name="dh-contentType" type="hidden" />
        <input id="dh-selections" name="dh-selections" type="hidden" />
        <input id="schemacsv" name="schemacsv" type="hidden" />
        <input id="identificationscsv" name="identificationscsv" type="hidden" />
        <input id="imagescsv" name="imagescsv" type="hidden" />
        <input id="formatcsv" name="formatcsv" type="hidden" />
        <input id="zipcsv" name="zipcsv" type="hidden" />
        <input id="csetcsv" name="csetcsv" type="hidden" />
    </form>
</div>

<div id="loadingOverlay" data-role="popup" style="width:100%;position:relative;">
    <div id="loader"></div>
</div>
</body>
</html>
