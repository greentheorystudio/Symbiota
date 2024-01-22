<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 180);

$windowType = array_key_exists('windowtype',$_REQUEST)?$_REQUEST['windowtype']:'analysis';
$clusterPoints = !(array_key_exists('clusterpoints', $_REQUEST) && $_REQUEST['clusterpoints'] === 'false');

$inputWindowMode = false;
$inputWindowModeTools = array();
$inputWindowSubmitText = '';
$displayWindowMode = false;

if(strncmp($windowType, 'input', 5) === 0){
    $inputWindowMode = true;
    if(strpos($windowType, '-') !== false){
        $windowTypeArr = explode('-',$windowType);
        if($windowTypeArr){
            $windowToolsArr = explode(',',$windowTypeArr[1]);
            foreach($windowToolsArr as $tool){
                $inputWindowModeTools[] = $tool;
            }
            $inputWindowSubmitText = 'Coordinates';
        }
    }
    else{
        $inputWindowSubmitText = 'Criteria';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>" style="background-color:white;">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Spatial Module</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION_LOCAL']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/bootstrap.min.css?ver=20221225" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery.mobile-1.4.0.min.css?ver=20221204" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui_accordian.css?ver=20221204" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver20240115" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/spatialbase.css?ver=20230929" rel="stylesheet" type="text/css" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.popupoverlay.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol-ext.min.js?ver=20240115" type="text/javascript"></script>
    <script src="https://npmcdn.com/@turf/turf/turf.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/shp.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jszip.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jscolor/jscolor.js?ver=13" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/stream.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/FileSaver.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/html2canvas.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/geotiff.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/plotty.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/spatial.module.core.js?ver=20240115" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/search.term.manager.js?ver=20230901" type="text/javascript"></script>
    <script type="text/javascript">
        const WINDOWMODE = '<?php echo $windowType; ?>';
        const INPUTWINDOWMODE = '<?php echo ($inputWindowMode?1:false); ?>';
        const INPUTTOOLSARR = JSON.parse('<?php echo json_encode($inputWindowModeTools); ?>');
        let clusterPoints = <?php echo ($GLOBALS['SPATIAL_POINT_CLUSTER']?'true':'false'); ?>;
        let clusterDistance = <?php echo $GLOBALS['SPATIAL_POINT_CLUSTER_DISTANCE']; ?>;
        let showHeatMap = <?php echo ($GLOBALS['SPATIAL_POINT_DISPLAY_HEAT_MAP']?'true':'false'); ?>;
        let heatMapRadius = <?php echo $GLOBALS['SPATIAL_POINT_HEAT_MAP_RADIUS']; ?>;
        let heatMapBlur = <?php echo $GLOBALS['SPATIAL_POINT_HEAT_MAP_BLUR']; ?>;
    </script>
    <?php include_once(__DIR__ . '/includes/spatialinitialize.php'); ?>
</head>
<body class="mapbody">
<div data-role="page" id="panelcontainer">
    <div role="main" class="ui-content">
        <a href="#defaultpanel" id="panelopenbutton" data-role="button" data-inline="true" data-icon="bars">Open</a>
    </div>
    <div data-role="panel" data-dismissible=false class="overflow:hidden;" id="defaultpanel" data-swipe-close=false data-position="left" data-display="overlay" >
        <div class="panel-content">
            <?php include_once(__DIR__ . '/includes/sidepanel.php'); ?>
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

    <?php include_once(__DIR__ . '/includes/controlpanel.php'); ?>
</div>

<script type="text/javascript">
    const popupcontainer = document.getElementById('popup');
    const popupcontent = document.getElementById('popup-content');
    const popupcloser = document.getElementById('popup-closer');
    const finderpopupcontainer = document.getElementById('finderpopup');
    const finderpopupcontent = document.getElementById('finderpopup-content');
    const finderpopupcloser = document.getElementById('finderpopup-closer');
    let finderpopuptimeout;
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

    layersObj['dragdrop1'].on('postrender', function(evt) {
        if(!loadPointsEvent){
            hideWorking();
        }
    });

    layersObj['dragdrop2'].on('postrender', function(evt) {
        if(!loadPointsEvent){
            hideWorking();
        }
    });

    layersObj['dragdrop3'].on('postrender', function(evt) {
        if(!loadPointsEvent){
            hideWorking();
        }
    });

    layersObj['select'].on('postrender', function(evt) {
        if(!loadPointsEvent){
            hideWorking();
        }
    });

    layersObj['pointv'].on('prerender', function(evt) {
        if(loadPointsEvent){
            showWorking();
        }
    });

    layersObj['heat'].on('prerender', function(evt) {
        if(loadPointsEvent){
            showWorking();
        }
    });

    layersObj['pointv'].on('postrender', function(evt) {
        if(loadPointsEvent && pointvectorsource.getFeatures().length === Number(queryRecCnt)){
            loadPointsPostrender();
        }
    });

    layersObj['heat'].on('postrender', function(evt) {
        if(loadPointsEvent && pointvectorsource.getFeatures().length === Number(queryRecCnt)){
            loadPointsPostrender();
        }
    });

    const selectInteraction = new ol.interaction.Select({
        layers: [layersObj['select']],
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

    const rasterAnalysisInteraction = new ol.interaction.Select({
        layers: [layersObj['rasteranalysis']],
        style: new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(255,0,0,0.3)'
            }),
            stroke: new ol.style.Stroke({
                color: 'rgba(255,0,0,1)',
                width: 5
            })
        })
    });

    const rasterAnalysisTranslate = new ol.interaction.Translate({
        features: rasterAnalysisInteraction.getFeatures(),
    });

    const pointInteraction = new ol.interaction.Select({
        layers: [layersObj['pointv'], layersObj['spider']],
        condition: function (evt) {
            if (evt.type === 'click' && activeLayer === 'pointv') {
                if (!evt.originalEvent.altKey) {
                    if (spiderCluster) {
                        const spiderclick = map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                            spiderFeature = feature;
                            if (feature && layer === layersObj['spider']) {
                                return feature;
                            }
                        });
                        if (!spiderclick) {
                            const blankSource = new ol.source.Vector({
                                features: new ol.Collection(),
                                useSpatialIndex: true
                            });
                            layersObj['spider'].setSource(blankSource);
                            for (const i in hiddenClusters) {
                                if(hiddenClusters.hasOwnProperty(i)){
                                    showFeature(hiddenClusters[i]);
                                }
                            }
                            hiddenClusters = [];
                            spiderCluster = false;
                            spiderFeature = '';
                            layersObj['pointv'].getSource().changed();
                        }
                    }
                    return true;
                } else if (evt.originalEvent.altKey) {
                    map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                        if (feature) {
                            if (spiderCluster && layer === layersObj['spider']) {
                                clickedFeatures.push(feature);
                                return feature;
                            } else if (layer === layersObj['pointv']) {
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

    const transformInteraction = new ol.interaction.Transform ({
        enableRotatedTransform: false,
        condition: function(evt) {
            return (activeLayer === 'select' && evt.originalEvent.shiftKey);
        },
        addCondition: ol.events.condition.shiftKeyOnly,
        layers: [layersObj['select']],
        hitTolerance: 2,
        translateFeature: false,
        scale: true,
        rotate: <?php echo (($inputWindowMode && in_array('box', $inputWindowModeTools, true))?'false':'true'); ?>,
        keepAspectRatio: false,
        translate: true,
        stretch: true
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

    dragAndDropInteraction.on('addfeatures', function(event) {
        let filename = event.file.name.split('.');
        const fileType = filename.pop();
        filename = filename.join("");
        if(fileType === 'geojson' || fileType === 'kml' || fileType === 'zip' || fileType === 'tif' || fileType === 'tiff'){
            if(fileType === 'geojson' || fileType === 'kml'){
                if(setDragDropTarget()){
                    const infoArr = [];
                    infoArr['id'] = dragDropTarget;
                    infoArr['type'] = 'userLayer';
                    infoArr['fileType'] = fileType;
                    infoArr['layerName'] = filename;
                    infoArr['layerDescription'] = "This layer is from a file that was added to the map.",
                        infoArr['fillColor'] = SPATIAL_DRAGDROP_FILL_COLOR;
                    infoArr['borderColor'] = SPATIAL_DRAGDROP_BORDER_COLOR;
                    infoArr['borderWidth'] = SPATIAL_DRAGDROP_BORDER_WIDTH;
                    infoArr['pointRadius'] = SPATIAL_DRAGDROP_POINT_RADIUS;
                    infoArr['opacity'] = SPATIAL_DRAGDROP_OPACITY;
                    infoArr['removable'] = true;
                    infoArr['sortable'] = true;
                    infoArr['symbology'] = true;
                    infoArr['query'] = true;
                    const sourceIndex = dragDropTarget + 'Source';
                    let features = event.features;
                    if(fileType === 'kml'){
                        const geoJSONFormat = new ol.format.GeoJSON();
                        features = geoJSONFormat.readFeatures(geoJSONFormat.writeFeatures(features));
                    }
                    layersObj[sourceIndex] = new ol.source.Vector({
                        features: features
                    });
                    layersObj[dragDropTarget].setSource(layersObj[sourceIndex]);
                    processAddLayerControllerElement(infoArr,document.getElementById("dragDropLayers"),true);
                    map.getView().fit(layersObj[sourceIndex].getExtent());
                    toggleLayerDisplayMessage();
                }
            }
            else if(fileType === 'zip'){
                if(setDragDropTarget()){
                    getArrayBuffer(event.file).then((data) => {
                        shp(data).then((geojson) => {
                            const infoArr = [];
                            infoArr['id'] = dragDropTarget;
                            infoArr['type'] = 'userLayer';
                            infoArr['fileType'] = 'zip';
                            infoArr['layerName'] = filename;
                            infoArr['layerDescription'] = "This layer is from a file that was added to the map.",
                                infoArr['fillColor'] = SPATIAL_DRAGDROP_FILL_COLOR;
                            infoArr['borderColor'] = SPATIAL_DRAGDROP_BORDER_COLOR;
                            infoArr['borderWidth'] = SPATIAL_DRAGDROP_BORDER_WIDTH;
                            infoArr['pointRadius'] = SPATIAL_DRAGDROP_POINT_RADIUS;
                            infoArr['opacity'] = SPATIAL_DRAGDROP_OPACITY;
                            infoArr['removable'] = true;
                            infoArr['sortable'] = true;
                            infoArr['symbology'] = true;
                            infoArr['query'] = true;
                            const sourceIndex = dragDropTarget + 'Source';
                            const format = new ol.format.GeoJSON();
                            const features = format.readFeatures(geojson, {
                                featureProjection: 'EPSG:3857'
                            });
                            layersObj[sourceIndex] = new ol.source.Vector({
                                features: features
                            });
                            layersObj[dragDropTarget].setSource(layersObj[sourceIndex]);
                            processAddLayerControllerElement(infoArr,document.getElementById("dragDropLayers"),true);
                            map.getView().fit(layersObj[sourceIndex].getExtent());
                            hideWorking();
                            toggleLayerDisplayMessage();
                        });
                    });
                }
            }
            else if(fileType === 'tif' || fileType === 'tiff'){
                if(setRasterDragDropTarget()){
                    event.file.arrayBuffer().then((data) => {
                        const extent = ol.extent.createEmpty();
                        const infoArr = [];
                        infoArr['id'] = dragDropTarget;
                        infoArr['type'] = 'userLayer';
                        infoArr['fileType'] = 'tif';
                        infoArr['layerName'] = filename;
                        infoArr['layerDescription'] = "This layer is from a file that was added to the map.",
                            infoArr['removable'] = true;
                        infoArr['sortable'] = true;
                        infoArr['symbology'] = true;
                        infoArr['query'] = false;
                        const sourceIndex = dragDropTarget + 'Source';
                        const dataIndex = dragDropTarget + 'Data';
                        const tiff = GeoTIFF.parse(data);
                        const image = tiff.getImage();
                        const rawBox = image.getBoundingBox();
                        const box = [rawBox[0],rawBox[1] - (rawBox[3] - rawBox[1]), rawBox[2], rawBox[1]];
                        const bands = image.readRasters();
                        const meta = image.getFileDirectory();
                        const x_min = meta.ModelTiepoint[3];
                        const x_max = x_min + meta.ModelPixelScale[0] * meta.ImageWidth;
                        const y_min = meta.ModelTiepoint[4];
                        const y_max = y_min - meta.ModelPixelScale[1] * meta.ImageLength;
                        const imageWidth = image.getWidth();
                        const imageHeight = image.getHeight();
                        let minValue = 0;
                        let maxValue = 0;
                        bands[0].forEach(function(item, index) {
                            if(item < minValue && ((minValue - item) < 5000)){
                                minValue = item;
                            }
                            if(item > maxValue){
                                maxValue = item;
                            }
                        });
                        layersObj[dataIndex] = {};
                        layersObj[dataIndex]['data'] = bands[0];
                        layersObj[dataIndex]['bbox'] = image.getBoundingBox();
                        layersObj[dataIndex]['resolution'] = (Number(meta.ModelPixelScale[0]) * 100) * 1.6;
                        layersObj[dataIndex]['x_min'] = x_min;
                        layersObj[dataIndex]['x_max'] = x_max;
                        layersObj[dataIndex]['y_min'] = y_min;
                        layersObj[dataIndex]['y_max'] = y_max;
                        layersObj[dataIndex]['imageWidth'] = imageWidth;
                        layersObj[dataIndex]['imageHeight'] = imageHeight;
                        layersObj[dataIndex]['minValue'] = minValue;
                        layersObj[dataIndex]['maxValue'] = maxValue;
                        const canvasElement = document.createElement('canvas');
                        const plot = new plotty.plot({
                            canvas: canvasElement,
                            data: bands[0],
                            width: imageWidth,
                            height: imageHeight,
                            domain: [minValue, maxValue],
                            colorScale: SPATIAL_DRAGDROP_RASTER_COLOR_SCALE
                        });
                        plot.render();
                        layersObj[sourceIndex] = new ol.source.ImageStatic({
                            url: canvasElement.toDataURL("image/png"),
                            imageExtent: box,
                            projection: 'EPSG:4326'
                        });
                        layersObj[dragDropTarget].setSource(layersObj[sourceIndex]);
                        map.addLayer(layersObj[dragDropTarget]);
                        processAddLayerControllerElement(infoArr,document.getElementById("dragDropLayers"),true);
                        addRasterLayerToTargetList(dragDropTarget,filename);
                        toggleLayerDisplayMessage();
                        const topRight = new ol.geom.Point(ol.proj.fromLonLat([box[2], box[3]]));
                        const topLeft = new ol.geom.Point(ol.proj.fromLonLat([box[0], box[3]]));
                        const bottomLeft = new ol.geom.Point(ol.proj.fromLonLat([box[0], box[1]]));
                        const bottomRight = new ol.geom.Point(ol.proj.fromLonLat([box[2], box[1]]));
                        ol.extent.extend(extent, topRight.getExtent());
                        ol.extent.extend(extent, topLeft.getExtent());
                        ol.extent.extend(extent, bottomLeft.getExtent());
                        ol.extent.extend(extent, bottomRight.getExtent());
                        map.getView().fit(extent, map.getSize());
                        hideWorking();
                    });
                }
            }
        }
        else if(fileType === 'shp' || fileType === 'dbf'){
            hideWorking();
            alert('In order to load a shapefile, the entire shapefile zip file must be dragged and dropped onto the map.');
        }
        else{
            hideWorking();
            alert('The drag and drop file loading only supports GeoJSON, kml, tif, and shapefile zip archives.');
        }
    });

    const mapView = new ol.View({
        zoom: SPATIAL_INITIAL_ZOOM,
        projection: 'EPSG:3857',
        minZoom: 2.5,
        maxZoom: 19,
        center: ol.proj.transform(SPATIAL_INITIAL_CENTER, 'EPSG:4326', 'EPSG:3857'),
    });

    const map = new ol.Map({
        view: mapView,
        target: 'map',
        layers: layersArr,
        overlays: [popupoverlay,finderpopupoverlay]
    });
    map.addInteraction(rasterAnalysisInteraction);
    map.addInteraction(rasterAnalysisTranslate);
    map.addControl(new ol.control.FullScreen());

    changeBaseMap();

    const scaleLineControl_us = new ol.control.ScaleLine({
        target: document.getElementById('mapscale_us'),
        units: 'us'
    });

    const scaleLineControl_metric = new ol.control.ScaleLine({
        target: document.getElementById('mapscale_metric'),
        units: 'metric'
    });

    const mousePositionControl = new ol.control.MousePosition({
        coordinateFormat: coordFormat(),
        projection: 'EPSG:4326',
        className: 'custom-mouse-position',
        target: document.getElementById('mapcoords'),
        undefinedHTML: '&nbsp;'
    });

    const zoomslider = new ol.control.ZoomSlider();

    map.addControl(zoomslider);
    map.addControl(scaleLineControl_us);
    map.addControl(scaleLineControl_metric);
    map.addControl(mousePositionControl);
    map.addInteraction(selectInteraction);
    map.addInteraction(pointInteraction);
    map.addInteraction(dragAndDropInteraction);
    map.addInteraction(transformInteraction);

    const selectedFeatures = selectInteraction.getFeatures();
    const selectedPointFeatures = pointInteraction.getFeatures();

    selectedPointFeatures.on('add', function() {
        processVectorInteraction();
    });

    selectedPointFeatures.on('remove', function() {
        processVectorInteraction();
    });

    map.getView().on('change:resolution', function() {
        if(spiderCluster){
            const source = layersObj['spider'].getSource();
            source.clear();
            const blankSource = new ol.source.Vector({
                features: new ol.Collection(),
                useSpatialIndex: true
            });
            layersObj['spider'].setSource(blankSource);
            for(const i in hiddenClusters){
                if(hiddenClusters.hasOwnProperty(i)){
                    showFeature(hiddenClusters[i]);
                }
            }
            hiddenClusters = [];
            spiderCluster = '';
            layersObj['pointv'].getSource().changed();
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
        processVectorInteraction();
    });

    selectedFeatures.on('remove', function() {
        processVectorInteraction();
    });

    selectsource.on('change', function() {
        if(!draw){
            const featureCnt = selectsource.getFeatures().length;
            if(featureCnt > 0){
                if(!shapeActive){
                    const infoArr = [];
                    infoArr['id'] = 'select';
                    infoArr['type'] = 'userLayer';
                    infoArr['fileType'] = 'vector';
                    infoArr['layerName'] = 'Shapes';
                    infoArr['layerDescription'] = "This layer contains all of the features created through using the Draw Tool, and those that have been selected from other layers added to the map.",
                        infoArr['fillColor'] = SPATIAL_SHAPES_FILL_COLOR;
                    infoArr['borderColor'] = SPATIAL_SHAPES_BORDER_COLOR;
                    infoArr['borderWidth'] = SPATIAL_SHAPES_BORDER_WIDTH;
                    infoArr['pointRadius'] = SPATIAL_SHAPES_POINT_RADIUS;
                    infoArr['opacity'] = SPATIAL_SHAPES_OPACITY;
                    infoArr['removable'] = true;
                    infoArr['sortable'] = false;
                    infoArr['symbology'] = false;
                    infoArr['query'] = true;
                    processAddLayerControllerElement(infoArr,document.getElementById("coreLayers"),false);
                    shapeActive = true;
                }
            }
            else{
                if(shapeActive){
                    removeLayerFromSelList('select');
                    shapeActive = false;
                }
            }
        }
    });

    map.getViewport().addEventListener('drop', function(event) {
        showWorking();
    });

    map.on('singleclick', function(evt) {
        let infoHTML;
        if(evt.originalEvent.altKey){
            if(activeLayer === 'pointv'){
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
                    popupcontent.innerHTML = getPointFeatureInfoHtml(iFeature);
                    popupoverlay.setPosition(evt.coordinate);
                }
                else{
                    alert('You clicked on multiple points. The info window can only display data for a single point.');
                }
                clickedFeatures = [];
            }
            else if(activeLayer === 'dragdrop4' || activeLayer === 'dragdrop5' || activeLayer === 'dragdrop6' || layersObj[activeLayer] instanceof ol.layer.Image){
                infoHTML = '';
                const coords = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                const dataIndex = activeLayer + 'Data';
                const x = Math.floor(layersObj[dataIndex]['imageWidth']*(coords[0] - layersObj[dataIndex]['x_min'])/(layersObj[dataIndex]['x_max'] - layersObj[dataIndex]['x_min']));
                const y = layersObj[dataIndex]['imageHeight']-Math.ceil(layersObj[dataIndex]['imageHeight']*(coords[1] - layersObj[dataIndex]['y_max'])/(layersObj[dataIndex]['y_min'] - layersObj[dataIndex]['y_max']));
                const rasterDataIndex = (Number(layersObj[dataIndex]['imageWidth']) * y) + x;
                infoHTML += '<b>Value:</b> '+layersObj[dataIndex]['data'][rasterDataIndex]+'<br />';
                popupcontent.innerHTML = infoHTML;
                popupoverlay.setPosition(evt.coordinate);
            }
            else if(layersObj[activeLayer] instanceof ol.layer.Vector){
                infoHTML = '';
                const feature = map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                    if (layer === layersObj[activeLayer]) {
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
        }
        else{
            if(activeLayer !== 'none' && activeLayer !== 'select' && activeLayer !== 'pointv'){
                if(layersObj[activeLayer] instanceof ol.layer.Vector){
                    map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                        if(layer === layersObj[activeLayer]){
                            if(!selectsource.hasFeature(feature)){
                                const featureClone = feature.clone();
                                selectsource.addFeature(featureClone);
                            }
                        }
                    });
                }
            }
        }
    });

    map.on('pointermove', function (evt) {
        if(activeLayer === 'none'){
            const selectobject = document.getElementById("selectlayerselect");
            let infoHTML = '';
            let idArr = [];
            map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                if(layer === layersObj['pointv']){
                    let iFeature = '';
                    if(feature){
                        if(clusterPoints && feature.get('features').length === 1){
                            iFeature = feature.get('features')[0];
                        }
                        else if(!clusterPoints){
                            iFeature = feature;
                        }
                    }
                    if(iFeature){
                        infoHTML = getPointFeatureInfoHtml(iFeature);
                    }
                }
                else{
                    for(let i = 0; i < selectobject.length; i++){
                        if(layer === layersObj[selectobject.options[i].value] && !idArr.includes(selectobject.options[i].value)){
                            idArr.push(selectobject.options[i].value);
                            if(infoHTML){
                                infoHTML += '<br />';
                            }
                            infoHTML += selectobject.options[i].innerHTML;
                        }
                    }
                }
            });
            if(infoHTML){
                popupcontent.innerHTML = infoHTML;
                popupoverlay.setPosition(evt.coordinate);
            }
            else{
                popupoverlay.setPosition(undefined);
                popupcloser.blur();
            }
        }
    });

    transformInteraction.on (['select'], function(evt) {
        if(transformFirstPoint && evt.features && evt.features.getLength()){
            transformInteraction.setCenter(evt.features.getArray()[0].getGeometry().getFirstCoordinate());
        }
    });

    transformInteraction.on (['rotatestart','translatestart'], function(evt){
        transformStartAngle = evt.feature.get('angle') || 0;
        transformD = [0,0];
    });

    transformInteraction.on('rotating', function (evt){
        evt.feature.set('angle', transformStartAngle - evt.angle);
    });

    transformInteraction.on('translating', function (evt){
        transformD[0] += evt.delta[0];
        transformD[1] += evt.delta[1];
        if(transformFirstPoint){
            transformInteraction.setCenter(evt.features.getArray()[0].getGeometry().getFirstCoordinate());
        }
    });

    transformInteraction.on('scaling', function (evt){
        if(transformFirstPoint){
            transformInteraction.setCenter(evt.features.getArray()[0].getGeometry().getFirstCoordinate());
        }
    });

    typeSelect.onchange = function() {
        map.removeInteraction(draw);
        changeDraw();
    };

    changeDraw();
    setTransformHandleStyle();
</script>

<?php include_once(__DIR__ . '/includes/mapsettings.php'); ?>

<?php include_once(__DIR__ . '/includes/layerqueryselector.php'); ?>

<?php include_once(__DIR__ . '/includes/infowindow.php'); ?>

<?php include_once(__DIR__ . '/includes/layercontroller.php'); ?>

<?php include_once(__DIR__ . '/includes/spatialfooter.php'); ?>

<div class="loadingModal">
    <div class="vine-native-spinner" style="width:200px;height:200px;"></div>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
?>
</body>
</html>
