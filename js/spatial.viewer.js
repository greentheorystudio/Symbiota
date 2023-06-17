const popupcontainer = document.getElementById('popup');
const popupcontent = document.getElementById('popup-content');
const popupcloser = document.getElementById('popup-closer');

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

const blankdragdropsource = new ol.source.Vector({
    wrapX: true
});
layersObj['dragdrop1'] = new ol.layer.Vector({
    zIndex: 1,
    source: blankdragdropsource,
    style: getVectorLayerStyle(SPATIAL_DRAGDROP_FILL_COLOR, SPATIAL_DRAGDROP_BORDER_COLOR, SPATIAL_DRAGDROP_BORDER_WIDTH, SPATIAL_DRAGDROP_POINT_RADIUS, SPATIAL_DRAGDROP_OPACITY)
});
layersArr.push(layersObj['dragdrop1']);
layersObj['dragdrop2'] = new ol.layer.Vector({
    zIndex: 2,
    source: blankdragdropsource,
    style: getVectorLayerStyle(SPATIAL_DRAGDROP_FILL_COLOR, SPATIAL_DRAGDROP_BORDER_COLOR, SPATIAL_DRAGDROP_BORDER_WIDTH, SPATIAL_DRAGDROP_POINT_RADIUS, SPATIAL_DRAGDROP_OPACITY)
});
layersArr.push(layersObj['dragdrop2']);
layersObj['dragdrop3'] = new ol.layer.Vector({
    zIndex: 3,
    source: blankdragdropsource,
    style: getVectorLayerStyle(SPATIAL_DRAGDROP_FILL_COLOR, SPATIAL_DRAGDROP_BORDER_COLOR, SPATIAL_DRAGDROP_BORDER_WIDTH, SPATIAL_DRAGDROP_POINT_RADIUS, SPATIAL_DRAGDROP_OPACITY)
});
layersArr.push(layersObj['dragdrop3']);

let radiuscirclesource = new ol.source.Vector({
    wrapX: true
});
layersObj['radius'] = new ol.layer.Vector({
    zIndex: 4,
    source: radiuscirclesource,
    style: new ol.style.Style({
        fill: new ol.style.Fill({
            color: 'rgba(255,0,0,0.3)'
        }),
        stroke: new ol.style.Stroke({
            color: '#000000',
            width: 1
        }),
        image: new ol.style.Circle({
            radius: 7,
            stroke: new ol.style.Stroke({
                color: '#000000',
                width: 1
            }),
            fill: new ol.style.Fill({
                color: 'rgba(255,0,0)'
            })
        })
    })
});
layersArr.push(layersObj['radius']);

let vectorsource = new ol.source.Vector({
    wrapX: true
});
layersObj['vector'] = new ol.layer.Vector({
    zIndex: 5,
    source: vectorsource,
    style: new ol.style.Style({
        fill: new ol.style.Fill({
            color: 'rgba(255,0,0,0.3)'
        }),
        stroke: new ol.style.Stroke({
            color: '#000000',
            width: 1
        }),
        image: new ol.style.Circle({
            radius: 7,
            stroke: new ol.style.Stroke({
                color: '#000000',
                width: 1
            }),
            fill: new ol.style.Fill({
                color: 'rgba(255,0,0)'
            })
        })
    })
});
layersArr.push(layersObj['vector']);

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
    controls: ol.control.defaults().extend([
        new ol.control.FullScreen()
    ]),
    layers: layersArr,
    overlays: [popupoverlay],
    renderer: 'canvas'
});

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
    if(fileType === 'geojson' || fileType === 'kml' || fileType === 'zip'){
        if(fileType === 'geojson' || fileType === 'kml'){
            if(setDragDropTarget()){
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
                map.getView().fit(layersObj[sourceIndex].getExtent());
            }
        }
        else if(fileType === 'zip'){
            if(setDragDropTarget()){
                getArrayBuffer(event.file).then((data) => {
                    shp(data).then((geojson) => {
                        const sourceIndex = dragDropTarget + 'Source';
                        const format = new ol.format.GeoJSON();
                        const features = format.readFeatures(geojson, {
                            featureProjection: 'EPSG:3857'
                        });
                        layersObj[sourceIndex] = new ol.source.Vector({
                            features: features
                        });
                        layersObj[dragDropTarget].setSource(layersObj[sourceIndex]);
                        map.getView().fit(layersObj[sourceIndex].getExtent());
                    });
                });
            }
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
    else if(fileType === 'shp' || fileType === 'dbf'){
        hideWorking();
        alert('In order to load a shapefile, the entire shapefile zip file must be dragged and dropped onto the map.');
    }
    else{
        alert('The drag and drop file loading only supports GeoJSON, kml, and shapefile zip archives.');
    }
});

map.addControl(scaleLineControl_us);
map.addControl(scaleLineControl_metric);
map.addControl(mousePositionControl);
map.addInteraction(dragAndDropInteraction);

map.on('singleclick', function(evt) {
    let infoHTML;
    if(evt.originalEvent.altKey){
        infoHTML = '';
        const feature = map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
            return feature;
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
});
