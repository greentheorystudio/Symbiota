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
    style: getVectorLayerStyle(dragDropFillColor, dragDropBorderColor, dragDropBorderWidth, dragDropPointRadius, dragDropOpacity)
});
layersArr.push(layersObj['dragdrop1']);
layersObj['dragdrop2'] = new ol.layer.Vector({
    zIndex: 2,
    source: blankdragdropsource,
    style: getVectorLayerStyle(dragDropFillColor, dragDropBorderColor, dragDropBorderWidth, dragDropPointRadius, dragDropOpacity)
});
layersArr.push(layersObj['dragdrop2']);
layersObj['dragdrop3'] = new ol.layer.Vector({
    zIndex: 3,
    source: blankdragdropsource,
    style: getVectorLayerStyle(dragDropFillColor, dragDropBorderColor, dragDropBorderWidth, dragDropPointRadius, dragDropOpacity)
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
    zoom: initialMapZoom,
    projection: 'EPSG:3857',
    minZoom: 2.5,
    maxZoom: 19,
    center: ol.proj.transform(initialMapCenter, 'EPSG:4326', 'EPSG:3857'),
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
