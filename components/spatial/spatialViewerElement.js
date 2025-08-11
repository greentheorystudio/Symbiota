const spatialViewerElement = {
    props: {
        coordinateSet: {
            type: Array,
            default: []
        },
        footprintWkt: {
            type: String,
            default: null
        }
    },
    template: `
        <div id="viewer-map" class="fit">
            <div id="viewer-popup" class="ol-popup">
                <template v-if="popupCloser">
                    <a class="ol-popup-closer cursor-pointer" @click="closePopup();"></a>
                </template>
                <div id="popup-content" v-html="popupContent"></div>
            </div>
        
            <div id="viewer-mapinfo">
                <div id="viewer-mapscale_us"></div>
                <div id="viewer-mapscale_metric"></div>
            </div>
        
            <div id="viewer-maptoolcontainer" class="row justify-between q-pa-sm">
                <div class="col-4">
                    <spatial-base-layer-selector @change-base-layer="processChangeBaseLayer"></spatial-base-layer-selector>
                </div>
                <div id="viewer-mapcoords" class="row justify-end self-center col-4 text-subtitle2 text-bold"></div>
            </div>
        </div>
    `,
    components: {
        'spatial-base-layer-selector': spatialBaseLayerSelector
    },
    setup(props) {
        const { getArrayBuffer, getCorrectedPolygonCoordArr, getRgbaStrFromHexOpacity, hideWorking, showNotification, showWorking, validatePolygonCoordArr } = useCore();
        const spatialStore = useSpatialStore();

        const dragAndDropInteraction = new ol.interaction.DragAndDrop({
            formatConstructors: [
                ol.format.GPX,
                ol.format.GeoJSON,
                ol.format.IGC,
                ol.format.KML,
                ol.format.TopoJSON
            ]
        });
        const layersArr = Vue.shallowReactive([]);
        const layersObj = Vue.shallowReactive({});
        let map = null;
        const mapSettings = Vue.shallowReactive(Object.assign({}, spatialStore.getMapSettings));
        let mapView = null;
        let popupCloser = Vue.ref(false);
        let popupContent = Vue.ref('');
        let popupOverlay = null;
        let popupTimeout = null;
        const propsRefs = Vue.toRefs(props);

        Vue.watch(propsRefs.coordinateSet, () => {
            processCoordinateSet();
        });

        Vue.watch(propsRefs.footprintWkt, () => {
            processFootprintWkt();
        });

        updateMapSettings('blankDragDropSource', new ol.source.Vector({
            wrapX: true
        }));
        updateMapSettings('radiusCircleSource', new ol.source.Vector({
            wrapX: true
        }));
        updateMapSettings('vectorSource', new ol.source.Vector({
            wrapX: true
        }));

        function addMapControlsInteractions() {
            map.addControl(new ol.control.ScaleLine({
                target: document.getElementById('viewer-mapscale_us'),
                units: 'us'
            }));
            map.addControl(new ol.control.ScaleLine({
                target: document.getElementById('viewer-mapscale_metric'),
                units: 'metric'
            }));
            map.addControl(new ol.control.MousePosition({
                coordinateFormat: coordFormat(),
                projection: 'EPSG:4326',
                className: 'custom-mouse-position',
                target: document.getElementById('viewer-mapcoords'),
                undefinedHTML: '&nbsp;'
            }));
            map.addInteraction(dragAndDropInteraction);
        }

        function changeBaseMap(){
            let blsource;
            const baseLayer = map.getLayers().getArray()[0];
            if(mapSettings.selectedBaseLayer === 'googleroadmap'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'googlealteredroadmap'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=r&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'googleterrain'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=p&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'googlehybrid'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=y&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'googlesatellite'){
                blsource = new ol.source.XYZ({
                    url: 'https://mt0.google.com/vt/lyrs=s&hl=en&x={x}&y={y}&z={z}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'worldtopo'){
                blsource = new ol.source.XYZ({
                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'openstreet'){
                blsource = new ol.source.OSM();
            }
            else if(mapSettings.selectedBaseLayer === 'stamentoner'){
                blsource = new ol.source.StadiaMaps({ layer: 'stamen_toner' });
            }
            else if(mapSettings.selectedBaseLayer === 'stamentonerlite'){
                blsource = new ol.source.StadiaMaps({ layer: 'stamen_toner_lite' });
            }
            else if(mapSettings.selectedBaseLayer === 'stamenterrain'){
                blsource = new ol.source.StadiaMaps({ layer: 'stamen_terrain' });
            }
            else if(mapSettings.selectedBaseLayer === 'stamenalidade'){
                blsource = new ol.source.StadiaMaps({ layer: 'alidade_smooth' });
            }
            else if(mapSettings.selectedBaseLayer === 'stamenoutdoors'){
                blsource = new ol.source.StadiaMaps({ layer: 'outdoors' });
            }
            else if(mapSettings.selectedBaseLayer === 'worldimagery'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'ocean'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/Ocean_Basemap/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'ngstopo'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/USA_Topo_Maps/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'natgeoworld'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'esristreet'){
                blsource = new ol.source.XYZ({
                    url: 'https://services.arcgisonline.com/arcgis/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
                    crossOrigin: 'anonymous'
                });
            }
            else if(mapSettings.selectedBaseLayer === 'opentopo'){
                blsource = new ol.source.XYZ({
                    url: 'https://tile.opentopomap.org/{z}/{x}/{y}.png',
                    crossOrigin: 'anonymous'
                });
            }
            baseLayer.setSource(blsource);
        }

        function closePopup() {
            popupOverlay.setPosition(undefined);
            popupCloser.value = false;
            clearTimeout(popupTimeout);
            popupTimeout = null;
        }

        function coordFormat() {
            return((coords) => {
                if(coords[0] < -180){
                    coords[0] += 360;
                }
                if(coords[0] > 180){
                    coords[0] -= 360;
                }
                const template = 'Lat: {y} Lon: {x}';
                return ol.coordinate.format(coords,template,5);
            });
        }

        function getValidatedFootprintWkt() {
            const wktFormat = new ol.format.WKT();
            const wgs84Projection = new ol.proj.Projection({
                code: 'EPSG:4326',
                units: 'degrees'
            });
            const mapProjection = new ol.proj.Projection({
                code: 'EPSG:3857'
            });
            const footprintpoly = wktFormat.readFeature(props.footprintWkt, mapProjection);
            if(footprintpoly){
                const polyClone = footprintpoly.clone();
                polyClone.getGeometry().transform(wgs84Projection, mapProjection);
                if(validatePolygonCoordArr(polyClone.getGeometry().getCoordinates())){
                    return polyClone;
                }
                else{
                    const geoJSONFormat = new ol.format.GeoJSON();
                    const geojsonStr = geoJSONFormat.writeGeometry(footprintpoly.getGeometry());
                    const coordArr = JSON.parse(geojsonStr).coordinates;
                    const fixedCoords = getCorrectedPolygonCoordArr(coordArr);
                    const turfSimple = turf.polygon(fixedCoords);
                    const polySimple = geoJSONFormat.readFeature(turfSimple, wgs84Projection);
                    polySimple.getGeometry().transform(wgs84Projection, mapProjection);
                    return polySimple;
                }
            }
        }

        function getVectorLayerStyle(fillColor, borderColor, borderWidth, pointRadius, opacity) {
            if(Number(borderWidth) !== 0){
                return new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: getRgbaStrFromHexOpacity(fillColor, opacity)
                    }),
                    stroke: new ol.style.Stroke({
                        color: borderColor,
                        width: borderWidth
                    }),
                    image: new ol.style.Circle({
                        radius: pointRadius,
                        fill: new ol.style.Fill({
                            color: getRgbaStrFromHexOpacity(fillColor, opacity)
                        }),
                        stroke: new ol.style.Stroke({
                            color: borderColor,
                            width: borderWidth
                        })
                    })
                })
            }
            else{
                return new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: getRgbaStrFromHexOpacity(fillColor, opacity)
                    }),
                    image: new ol.style.Circle({
                        radius: pointRadius,
                        fill: new ol.style.Fill({
                            color: getRgbaStrFromHexOpacity(fillColor, opacity)
                        })
                    })
                })
            }
        }

        function processChangeBaseLayer(value) {
            updateMapSettings('selectedBaseLayer', value);
            changeBaseMap();
        }

        function processCoordinateSet() {
            if(props.coordinateSet && props.coordinateSet.length > 0){
                props.coordinateSet.forEach((coords) => {
                    const pointGeom = new ol.geom.Point(ol.proj.fromLonLat([
                        Number(coords[0]), Number(coords[1])
                    ]));
                    mapSettings.vectorSource.addFeature(new ol.Feature(pointGeom));
                    const vectorextent = mapSettings.vectorSource.getExtent();
                    map.getView().fit(vectorextent,map.getSize());
                    let fittedZoom = map.getView().getZoom();
                    map.getView().setZoom(fittedZoom);
                });
            }
        }

        function processFootprintWkt() {
            if(props.footprintWkt){
                const footprintpoly = getValidatedFootprintWkt();
                if(footprintpoly){
                    mapSettings.vectorSource.addFeature(footprintpoly);
                    const vectorextent = mapSettings.vectorSource.getExtent();
                    map.getView().fit(vectorextent, map.getSize());
                    let fittedZoom = map.getView().getZoom();
                    map.getView().setZoom(fittedZoom);
                }
            }
        }

        function setDragDropTarget() {
            updateMapSettings('dragDropTarget', '');
            if(!mapSettings.dragDrop1){
                updateMapSettings('dragDrop1', true);
                updateMapSettings('dragDropTarget', 'dragDrop1');
                return true;
            }
            else if(!mapSettings.dragDrop2){
                updateMapSettings('dragDrop2', true);
                updateMapSettings('dragDropTarget', 'dragDrop2');
                return true;
            }
            else if(!mapSettings.dragDrop3){
                updateMapSettings('dragDrop3', true);
                updateMapSettings('dragDropTarget', 'dragDrop3');
                return true;
            }
            else{
                showNotification('negative','You may only have 3 uploaded vector layers at a time. Please remove one of the currently uploaded layers to upload more.');
                return false;
            }
        }

        function setMap() {
            mapView = new ol.View({
                zoom: mapSettings.initialMapZoom,
                projection: 'EPSG:3857',
                minZoom: 2.5,
                maxZoom: 19,
                center: ol.proj.transform(mapSettings.initialMapCenter, 'EPSG:4326', 'EPSG:3857'),
            });
            map = new ol.Map({
                view: mapView,
                target: 'viewer-map',
                layers: layersArr,
                overlays: [popupOverlay]
            });
            map.addControl(new ol.control.FullScreen());
            map.getViewport().addEventListener('drop', () => {
                showWorking('Loading...');
            });
            map.on('singleclick', (evt) => {
                let infoHTML;
                if(evt.originalEvent.altKey){
                    infoHTML = '';
                    const feature = map.forEachFeatureAtPixel(evt.pixel, (feature, layer) => {
                        if(layer === layersObj[mapSettings.activeLayer]){
                            return feature;
                        }
                    });
                    if(feature){
                        const properties = feature.getKeys();
                        properties.forEach((prop) => {
                            if(String(prop) !== 'geometry'){
                                infoHTML += '<b>' + prop + ':</b> ' + feature.get(prop) + '<br />';
                            }
                        });
                        if(infoHTML){
                            showPopup(infoHTML, evt.coordinate, true);
                        }
                    }
                }
            });
        }

        function setMapLayersInteractions() {
            layersObj['base'] = new ol.layer.Tile({
                zIndex: 0
            });
            layersArr.push(layersObj['base']);
            layersObj['dragDrop1'] = new ol.layer.Vector({
                zIndex: 1,
                source: mapSettings.blankDragDropSource,
                style: getVectorLayerStyle(mapSettings.dragDropFillColor, mapSettings.dragDropBorderColor, mapSettings.dragDropBorderWidth, mapSettings.dragDropPointRadius, mapSettings.dragDropOpacity)
            });
            layersArr.push(layersObj['dragDrop1']);
            layersObj['dragDrop2'] = new ol.layer.Vector({
                zIndex: 2,
                source: mapSettings.blankDragDropSource,
                style: getVectorLayerStyle(mapSettings.dragDropFillColor, mapSettings.dragDropBorderColor, mapSettings.dragDropBorderWidth, mapSettings.dragDropPointRadius, mapSettings.dragDropOpacity)
            });
            layersArr.push(layersObj['dragDrop2']);
            layersObj['dragDrop3'] = new ol.layer.Vector({
                zIndex: 3,
                source: mapSettings.blankDragDropSource,
                style: getVectorLayerStyle(mapSettings.dragDropFillColor, mapSettings.dragDropBorderColor, mapSettings.dragDropBorderWidth, mapSettings.dragDropPointRadius, mapSettings.dragDropOpacity)
            });
            layersArr.push(layersObj['dragDrop3']);
            layersObj['radius'] = new ol.layer.Vector({
                zIndex: 4,
                source: mapSettings.radiusCircleSource,
                style: new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'rgba(255, 0, 0, 0.3)'
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
                            color: 'rgb(255,0,0)'
                        })
                    })
                })
            });
            layersArr.push(layersObj['radius']);
            layersObj['vector'] = new ol.layer.Vector({
                zIndex: 5,
                source: mapSettings.vectorSource,
                style: new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'rgba(255, 0, 0, 0.3)'
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
                            color: 'rgba(255, 0, 0)'
                        })
                    })
                })
            });
            layersArr.push(layersObj['vector']);
            dragAndDropInteraction.on('addfeatures', (event) => {
                let filename = event.file.name.split('.');
                const fileType = filename.pop();
                if(fileType === 'geojson' || fileType === 'kml' || fileType === 'zip' || fileType === 'tif' || fileType === 'tiff'){
                    if(fileType === 'geojson' || fileType === 'kml'){
                        if(setDragDropTarget()){
                            const sourceIndex = mapSettings.dragDropTarget + 'Source';
                            let features = event.features;
                            if(fileType === 'kml'){
                                const geoJSONFormat = new ol.format.GeoJSON();
                                features = geoJSONFormat.readFeatures(geoJSONFormat.writeFeatures(features));
                            }
                            layersObj[sourceIndex] = new ol.source.Vector({
                                features: features
                            });
                            layersObj[mapSettings.dragDropTarget].setSource(layersObj[sourceIndex]);
                            map.getView().fit(layersObj[sourceIndex].getExtent());
                        }
                    }
                    else if(fileType === 'zip'){
                        if(setDragDropTarget()){
                            getArrayBuffer(event.file).then((data) => {
                                shp(data).then((geojson) => {
                                    const sourceIndex = mapSettings.dragDropTarget + 'Source';
                                    const format = new ol.format.GeoJSON();
                                    const features = format.readFeatures(geojson, {
                                        featureProjection: 'EPSG:3857'
                                    });
                                    layersObj[sourceIndex] = new ol.source.Vector({
                                        features: features
                                    });
                                    layersObj[mapSettings.dragDropTarget].setSource(layersObj[sourceIndex]);
                                    map.getView().fit(layersObj[sourceIndex].getExtent());
                                    hideWorking();
                                });
                            });
                        }
                    }
                    else if(fileType === 'tif' || fileType === 'tiff'){
                        if(setRasterDragDropTarget()){
                            event.file.arrayBuffer().then((data) => {
                                const tiff = GeoTIFF.parse(data);
                                const image = tiff.getImage();
                                try {
                                    if(image.getWidth()){
                                        const bands = image.readRasters();
                                        const extent = ol.extent.createEmpty();
                                        const sourceIndex = mapSettings.dragDropTarget + 'Source';
                                        const dataIndex = mapSettings.dragDropTarget + 'Data';
                                        const rawBox = image.getBoundingBox();
                                        const box = [rawBox[0],rawBox[1] - (rawBox[3] - rawBox[1]), rawBox[2], rawBox[1]];
                                        const meta = image.getFileDirectory();
                                        const x_min = meta.ModelTiepoint[3];
                                        const x_max = x_min + meta.ModelPixelScale[0] * meta.ImageWidth;
                                        const y_min = meta.ModelTiepoint[4];
                                        const y_max = y_min - meta.ModelPixelScale[1] * meta.ImageLength;
                                        const imageWidth = image.getWidth();
                                        const imageHeight = image.getHeight();
                                        let minValue = 0;
                                        let maxValue = 0;
                                        bands[0].forEach(function(item) {
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
                                            colorScale: mapSettings.dragDropRasterColorScale
                                        });
                                        plot.render();
                                        layersObj[sourceIndex] = new ol.source.ImageStatic({
                                            url: canvasElement.toDataURL("image/png"),
                                            imageExtent: box,
                                            projection: 'EPSG:4326'
                                        });
                                        layersObj[mapSettings.dragDropTarget].setSource(layersObj[sourceIndex]);
                                        map.addLayer(layersObj[mapSettings.dragDropTarget]);
                                        const topRight = new ol.geom.Point(ol.proj.fromLonLat([box[2], box[3]]));
                                        const topLeft = new ol.geom.Point(ol.proj.fromLonLat([box[0], box[3]]));
                                        const bottomLeft = new ol.geom.Point(ol.proj.fromLonLat([box[0], box[1]]));
                                        const bottomRight = new ol.geom.Point(ol.proj.fromLonLat([box[2], box[1]]));
                                        ol.extent.extend(extent, topRight.getExtent());
                                        ol.extent.extend(extent, topLeft.getExtent());
                                        ol.extent.extend(extent, bottomLeft.getExtent());
                                        ol.extent.extend(extent, bottomRight.getExtent());
                                        map.getView().fit(extent, map.getSize());
                                    }
                                }
                                catch(err) {
                                    showNotification('negative','The GeoTiff file cannot be read correctly.');
                                }
                                hideWorking();
                            });
                        }
                    }
                }
                else if(fileType === 'shp' || fileType === 'dbf'){
                    hideWorking();
                    showNotification('negative','In order to load a shapefile, the entire shapefile zip file must be dragged and dropped onto the map.');
                }
                else{
                    hideWorking();
                    showNotification('negative','The drag and drop file loading only supports GeoJSON, kml, tif, and shapefile zip archives.');
                }
            });
        }

        function setMapOverlays() {
            popupOverlay = new ol.Overlay({
                element: document.getElementById('viewer-popup'),
                autoPan: true,
                autoPanAnimation: {
                    duration: 250
                }
            });
        }

        function setRasterDragDropTarget() {
            updateMapSettings('dragDropTarget', '');
            if(!mapSettings.dragDrop4){
                updateMapSettings('dragDrop4', true);
                updateMapSettings('dragDropTarget', 'dragDrop4');
                return true;
            }
            else if(!mapSettings.dragDrop5){
                updateMapSettings('dragDrop5', true);
                updateMapSettings('dragDropTarget', 'dragDrop5');
                return true;
            }
            else if(!mapSettings.dragDrop6){
                updateMapSettings('dragDrop6', true);
                updateMapSettings('dragDropTarget', 'dragDrop6');
                return true;
            }
            else{
                showNotification('negative','You may only have 3 uploaded raster layers at a time. Please remove one of the currently uploaded layers to upload more.');
                return false;
            }
        }

        function showPopup(content, position, closer, centerMap = false) {
            clearTimeout(popupTimeout);
            popupContent.value = content;
            popupOverlay.setPosition(position);
            if(closer){
                popupCloser.value = true;
            }
            else{
                popupTimeout = setTimeout(() => {
                    closePopup();
                }, 2000);
            }
            if(centerMap){
                map.getView().setCenter(position);
            }
        }

        function updateMapSettings(prop, value) {
            mapSettings[prop] = value;
        }

        Vue.provide('mapSettings', mapSettings);

        Vue.onMounted(() => {
            setMapLayersInteractions();
            setMapOverlays();
            setMap();
            addMapControlsInteractions();
            changeBaseMap();
            processFootprintWkt();
            processCoordinateSet();
        });

        return {
            mapSettings,
            popupCloser,
            popupContent,
            closePopup,
            processChangeBaseLayer
        }
    }
};
