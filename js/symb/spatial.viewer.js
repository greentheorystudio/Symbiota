let vectorsource = new ol.source.Vector({
    wrapX: true
});
const vectorlayer = new ol.layer.Vector({
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

let radiuscirclesource = new ol.source.Vector({
    wrapX: true
});
const radiuscirclelayer = new ol.layer.Vector({
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

layersArr['base'] = baselayer;
layersArr['dragdrop1'] = dragdroplayer1;
layersArr['dragdrop2'] = dragdroplayer2;
layersArr['dragdrop3'] = dragdroplayer3;
layersArr['radius'] = radiuscirclelayer;
layersArr['vector'] = vectorlayer;

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
    layers: [
        layersArr['base'],
        layersArr['dragdrop1'],
        layersArr['dragdrop2'],
        layersArr['dragdrop3'],
        layersArr['radius'],
        layersArr['vector']
    ],
    renderer: 'canvas'
});

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
                layersArr[sourceIndex] = new ol.source.Vector({
                    features: features
                });
                layersArr[dragDropTarget].setStyle(getDragDropStyle);
                layersArr[dragDropTarget].setSource(layersArr[sourceIndex]);
                map.getView().fit(layersArr[sourceIndex].getExtent());
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
                        layersArr[sourceIndex] = new ol.source.Vector({
                            features: features
                        });
                        layersArr[dragDropTarget].setStyle(getDragDropStyle);
                        layersArr[dragDropTarget].setSource(layersArr[sourceIndex]);
                        map.getView().fit(layersArr[sourceIndex].getExtent());
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
