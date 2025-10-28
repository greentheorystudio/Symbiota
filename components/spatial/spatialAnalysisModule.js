const spatialAnalysisModule = {
    props: {
        clusterPoints: {
            type: Boolean,
            default: true
        },
        inputWindowMode: {
            type: Boolean,
            default: false
        },
        inputWindowToolsArr: {
            type: Array,
            default: []
        },
        queryId: {
            type: Number,
            default: 0
        },
        stArrJson: {
            type: String,
            default: null
        }
    },
    template: `
        <spatial-layer-controller-popup :layers-info-obj="layersInfoObj"></spatial-layer-controller-popup>
        <spatial-layer-query-selector-popup :layer-id="mapSettings.layerQuerySelectorId"></spatial-layer-query-selector-popup>
        <template v-if="mapSettings.recordInfoWindowId">
            <occurrence-info-window-popup :occurrence-id="mapSettings.recordInfoWindowId" :show-popup="mapSettings.showRecordInfoWindow" @close:popup="closeRecordInfoWindow"></occurrence-info-window-popup>
        </template>
        <search-criteria-popup :show-popup="displayQueryPopup" :show-spatial="false" @reset:search-criteria="clearSelectedFeatures" @process:search-load-records="loadRecords" @reset:search-criteria="processResetCriteria" @close:popup="setQueryPopupDisplay(false)"></search-criteria-popup>

        <div id="map" :class="inputWindowMode ? 'input-window analysis' : 'analysis'">
            <spatial-side-panel :show-panel="mapSettings.showSidePanel" :expanded-element="mapSettings.sidePanelExpandedElement"></spatial-side-panel>
            <spatial-control-panel ref="controlPanelRef"></spatial-control-panel>
            <spatial-side-button-tray></spatial-side-button-tray>
            <div id="popup" class="ol-popup">
                <template v-if="popupCloser">
                    <a class="ol-popup-closer cursor-pointer" @click="closePopup();"></a>
                </template>
                <div id="popup-content" v-html="popupContent"></div>
            </div>
            <template v-if="inputWindowMode">
                <q-btn dense class="z-max map-popup-close-button" size="md" color="red" text-color="white" icon="fas fa-times" @click="emitClosePopup();"></q-btn>
            </template>

            <div id="mapinfo">
                <div id="mapcoords"></div>
                <div id="mapscale_us"></div>
                <div id="mapscale_metric"></div>
            </div>
        </div>
    `,
    components: {
        'occurrence-info-window-popup': occurrenceInfoWindowPopup,
        'search-criteria-popup': searchCriteriaPopup,
        'spatial-side-panel': spatialSidePanel,
        'spatial-control-panel': spatialControlPanel,
        'spatial-layer-controller-popup': spatialLayerControllerPopup,
        'spatial-layer-query-selector-popup': spatialLayerQuerySelectorPopup,
        'spatial-side-button-tray': spatialSideButtonTray
    },
    setup(props, context) {
        const { convertMysqlWKT, csvToArray, generateRandHexColor, getArrayBuffer, getCorrectedPolygonCoordArr, getPlatformProperty, getRgbaStrFromHexOpacity, hexToRgb, hideWorking, parseFile, showNotification, showWorking, validatePolygonCoordArr, writeMySQLWktString } = useCore();
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();
        const spatialStore = useSpatialStore();

        const activeLayerSelectorOptions = Vue.shallowReactive([
            {value: 'none', label: 'None'}
        ]);
        const clickedFeatures = Vue.shallowReactive([]);
        const controlPanelRef = Vue.ref(null);
        const coreLayers = spatialStore.getCoreLayers;
        const displayQueryPopup = Vue.ref(false);
        const dragAndDropInteraction = new ol.interaction.DragAndDrop({
            formatConstructors: [
                ol.format.GPX,
                ol.format.GeoJSON,
                ol.format.IGC,
                ol.format.KML,
                ol.format.TopoJSON
            ]
        });
        const geoBoundingBoxArr = Vue.ref({});
        const geoCentroidArr = Vue.ref([]);
        const geoCircleArr = Vue.ref([]);
        const geoPointArr = Vue.ref([]);
        const geoPolyArr = Vue.ref([]);
        const hiddenClusters = Vue.ref([]);
        const inputResponseData = Vue.ref({});
        const layerOrderArr = Vue.shallowReactive([]);
        const layersArr = Vue.shallowReactive([]);
        const layersConfigArr = Vue.reactive([]);
        const layersInfoObj = Vue.reactive({});
        const layersObj = Vue.shallowReactive({});
        const lazyLoadCnt = 20000;
        let map = null;
        const mapProjection = new ol.proj.Projection({
            code: 'EPSG:3857'
        });
        const mapSettings = Vue.shallowReactive(Object.assign({}, spatialStore.getMapSettings));
        let mapView = null;
        const occurrenceEditorModeActive = Vue.computed(() => searchStore.getOccurrenceEditorModeActive);
        const pointInteraction = Vue.computed(() => setPointInteraction());
        let popupCloser = Vue.ref(false);
        let popupContent = Vue.ref('');
        let popupOverlay = null;
        let popupTimeout = null;
        const propsRefs = Vue.toRefs(props);
        const rasterAnalysisInteraction = Vue.computed(() => setRasterAnalysisInteraction());
        const rasterAnalysisTranslate = Vue.computed(() => setRasterAnalysisTranslate());
        const rasterLayersArr = Vue.ref([
            {value: 'none', label: 'None'}
        ]);
        const searchRecordCnt = Vue.computed(() => searchStore.getSearchRecordCount);
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const selectedPolyError = Vue.ref(false);
        const selectInteraction = Vue.computed(() => setSelectInteraction());
        const spatialModuleInitialising = Vue.ref(false);
        let spiderCluster = false;
        const spiderFeature = Vue.shallowReactive([]);
        const symbologyArr = Vue.reactive({});
        const transformInteraction = Vue.computed(() => setTransformInteraction());
        const transformInteractionD = Vue.ref([0, 0]);
        const transformInteractionStartAngle = Vue.ref(0);
        const transformInteractionStartRadius = Vue.ref(10);
        const wgs84Projection = new ol.proj.Projection({
            code: 'EPSG:4326',
            units: 'degrees'
        });
        const windowWidth = Vue.ref(0);

        updateMapSettings('blankDragDropSource', new ol.source.Vector({
            wrapX: true
        }));
        updateMapSettings('pointVectorSource', new ol.source.Vector({
            wrapX: true
        }));
        updateMapSettings('rasterAnalysisSource', new ol.source.Vector({
            wrapX: true
        }));
        updateMapSettings('selectSource', new ol.source.Vector({
            wrapX: true
        }));
        updateMapSettings('uncertaintyCircleSource', new ol.source.Vector({
            wrapX: true
        }));

        function addLayerToActiveLayerOptions(val, text, active) {
            activeLayerSelectorOptions.push({value: val, label: text});
            if(active){
                updateMapSettings('activeLayer', val);
            }
        }

        function addLayerToLayersObj(id, layerObj) {
            layersObj[id] = layerObj;
        }

        function addLayerToRasterLayersArr(val, text) {
            activeLayerSelectorOptions.push({value: val, label: text});
        }

        function addMapControlsInteractions() {
            map.addControl(new ol.control.ZoomSlider());
            map.addControl(new ol.control.ScaleLine({
                target: document.getElementById('mapscale_us'),
                units: 'us'
            }));
            map.addControl(new ol.control.ScaleLine({
                target: document.getElementById('mapscale_metric'),
                units: 'metric'
            }));
            map.addControl(new ol.control.MousePosition({
                coordinateFormat: coordFormat(),
                projection: 'EPSG:4326',
                className: 'custom-mouse-position',
                target: document.getElementById('mapcoords'),
                undefinedHTML: '&nbsp;'
            }));
            map.addInteraction(selectInteraction.value);
            map.addInteraction(pointInteraction.value);
            map.addInteraction(dragAndDropInteraction);
            map.addInteraction(transformInteraction.value);
        }

        function autoColorSymbologyKeys() {
            const usedColors = [];
            symbologyArr[mapSettings.mapSymbology].forEach((key) => {
                let randColor = generateRandHexColor();
                while(usedColors.indexOf(randColor) > -1) {
                    randColor = generateRandHexColor();
                }
                usedColors.push(randColor);
                processSymbologyKeyColorChange(randColor, key.value);
            });
        }

        function changeDraw() {
            if(mapSettings.selectedDrawTool !== 'None'){
                if(mapSettings.selectedDrawTool === 'Box'){
                    updateMapSettings('draw', new ol.interaction.Draw({
                        source: mapSettings.selectSource,
                        type: 'Circle',
                        freehand: mapSettings.drawToolFreehandMode,
                        geometryFunction: ol.interaction.Draw.createBox()
                    }));
                }
                else{
                    updateMapSettings('draw', new ol.interaction.Draw({
                        source: mapSettings.selectSource,
                        type: mapSettings.selectedDrawTool,
                        freehand: mapSettings.drawToolFreehandMode
                    }));
                }
                mapSettings.draw.on('drawend', (evt) => {
                    if(props.inputWindowMode && (props.inputWindowToolsArr.includes('point') || props.inputWindowToolsArr.includes('circle') || props.inputWindowToolsArr.includes('box'))){
                        mapSettings.selectSource.clear();
                        mapSettings.selectedFeatures.clear();
                        mapSettings.uncertaintyCircleSource.clear();
                        const featureClone = evt.feature.clone();
                        const geoType = featureClone.getGeometry().getType();
                        const geoJSONFormat = new ol.format.GeoJSON();
                        if(geoType === 'Point'){
                            const selectiongeometry = featureClone.getGeometry();
                            const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                            const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                            let pointCoords = JSON.parse(geojsonStr).coordinates;
                            const pointObj = {
                                decimalLatitude: pointCoords[1],
                                decimalLongitude: pointCoords[0]
                            };
                            geoPointArr.value.push(pointObj);
                            mapSettings.selectedFeatures.push(evt.feature);
                            processInputSelections();
                            if(props.inputWindowToolsArr.includes('uncertainty') || props.inputWindowToolsArr.includes('radius')){
                                if(mapSettings.uncertaintyRadiusValue > 0){
                                    const pointRadius = {};
                                    pointRadius.pointlat = pointCoords[1];
                                    pointRadius.pointlong = pointCoords[0];
                                    pointRadius.radius = mapSettings.uncertaintyRadiusValue;
                                    createUncertaintyCircleFromPointRadius(pointRadius);
                                }
                            }
                        }
                        else if(geoType === 'Circle'){
                            let radius;
                            const radiusM = featureClone.getGeometry().getRadius();
                            if(mapSettings.radiusUnits === 'km'){
                                radius = (Number(radiusM) / 1000);
                            }
                            else if(mapSettings.radiusUnits === 'mi'){
                                radius = ((Number(radiusM) * 1.609344) / 1000);
                            }
                            else{
                                radius = Number(radiusM);
                            }
                            updateMapSettings('uncertaintyRadiusValue', radius);
                        }
                        else if(props.inputWindowToolsArr.includes('box') && geoType === 'Polygon'){
                            evt.feature.set('geoType', 'Box');
                        }
                    }
                    else{
                        evt.feature.set('geoType', mapSettings.selectedDrawTool);
                    }
                    updateMapSettings('selectedDrawTool', 'None');
                    map.removeInteraction(mapSettings.draw);
                    if(!mapSettings.shapeActive){
                        const infoArr = [];
                        infoArr['id'] = 'select';
                        infoArr['type'] = 'userLayer';
                        infoArr['fileType'] = 'vector';
                        infoArr['layerName'] = 'Shapes';
                        infoArr['layerDescription'] = 'This layer contains all of the features created through using the Draw Tool, and those that have been selected from other layers added to the map.';
                        infoArr['fillColor'] = mapSettings.shapesFillColor;
                        infoArr['borderColor'] = mapSettings.shapesBorderColor;
                        infoArr['borderWidth'] = mapSettings.shapesBorderWidth;
                        infoArr['pointRadius'] = mapSettings.shapesPointRadius;
                        infoArr['opacity'] = mapSettings.shapesOpacity;
                        processAddedLayer(infoArr,true);
                        updateMapSettings('shapeActive', true);
                        updateMapSettings('activeLayer', 'select');
                    }
                    else{
                        updateMapSettings('activeLayer', 'select');
                    }
                    updateMapSettings('draw', null);
                });
                map.addInteraction(mapSettings.draw);
            }
            else{
                updateMapSettings('draw', null);
            }
        }

        function changeMapSymbology() {
            if(spiderCluster){
                const source = layersObj['spider'].getSource();
                source.clear();
                const blankSource = new ol.source.Vector({
                    features: new ol.Collection(),
                    useSpatialIndex: true
                });
                layersObj['spider'].setSource(blankSource);
                hiddenClusters.value.forEach((cluster) => {
                    showFeature(cluster);
                });
                hiddenClusters.value = [];
                spiderCluster = false;
                layersObj['pointv'].getSource().changed();
            }
            if(mapSettings.clusterPoints){
                loadPointsLayer();
            }
            layersObj['pointv'].getSource().changed();
        }

        function clearSelectedFeatures() {
            selectInteraction.value.getFeatures().clear();
        }

        function clearSelections(resetToggle) {
            const selections = searchStore.getSelectionsIds;
            selections.forEach((id) => {
                let point;
                if(mapSettings.clusterPoints){
                    const cluster = findRecordCluster(Number(id));
                    if(cluster){
                        point = findRecordPointInCluster(cluster,Number(id));
                    }
                }
                else{
                    point = findRecordPoint(Number(id));
                }
                if(point){
                    const style = setSymbol(point);
                    point.setStyle(style);
                }
            });
            searchStore.clearSelections();
            updateMapSettings('toggleSelectedPoints', false);
            if(resetToggle){
                processToggleSelectedChange();
            }
        }

        function closePopup() {
            popupOverlay.setPosition(undefined);
            popupCloser.value = false;
            clearTimeout(popupTimeout);
            popupTimeout = null;
        }

        function closeRecordInfoWindow(){
            updateMapSettings('recordInfoWindowId', null);
            updateMapSettings('showRecordInfoWindow', false);
        }

        function coordFormat() {
            return((coords) => {
                if(coords[0] < -180){
                    coords[0] = coords[0] + 360;
                }
                if(coords[0] > 180){
                    coords[0] = coords[0] - 360;
                }
                const template = 'Lat: {y} Lon: {x}';
                return ol.coordinate.format(coords,template,5);
            });
        }

        function createCircleFromPointRadius(prad, selected) {
            let radius;
            if(prad.radiusunits === 'km'){
                radius = (Number(prad.radius) * 1000);
            }
            else if(prad.radiusunits === 'mi'){
                radius = ((Number(prad.radius) * 1.609344) * 1000);
            }
            else{
                radius = Number(prad.radius);
            }
            const centerCoords = ol.proj.fromLonLat([prad.pointlong, prad.pointlat]);
            const circle = new ol.geom.Circle(centerCoords);
            circle.setRadius(radius);
            const circleFeature = new ol.Feature(circle);
            mapSettings.selectSource.addFeature(circleFeature);
            updateMapSettings('activeLayer', 'select');
            if(selected){
                mapSettings.selectedFeatures.push(circleFeature);
            }
        }

        function createCirclesFromCircleArr(circleArr, selected) {
            circleArr.forEach((feature) => {
                const centerCoords = ol.proj.fromLonLat([feature.pointlong, feature.pointlat]);
                const circle = new ol.geom.Circle(centerCoords);
                circle.setRadius(Number(feature.radius));
                const circleFeature = new ol.Feature(circle);
                mapSettings.selectSource.addFeature(circleFeature);
                if(selected){
                    mapSettings.selectedFeatures.push(circleFeature);
                }
            });
            updateMapSettings('activeLayer', 'select');
        }

        function createPointFromPointParams(lat, long) {
            const pointGeom = new ol.geom.Point(ol.proj.fromLonLat([
                long, lat
            ]));
            const pointFeature = new ol.Feature(pointGeom);
            mapSettings.selectSource.addFeature(pointFeature);
            mapSettings.selectedFeatures.push(pointFeature);
            processInputSelections();
            let selectextent;
            if(mapSettings.uncertaintyCircleSource.getFeatures().length > 0){
                selectextent = mapSettings.uncertaintyCircleSource.getExtent();
            }
            else{
                selectextent = mapSettings.selectSource.getExtent();
            }
            map.getView().fit(selectextent, map.getSize());
            let fittedZoom = map.getView().getZoom();
            map.getView().setZoom(fittedZoom - 2);
        }

        function createPolygonFromBoundingBox(bbox, selected) {
            const coordArr = [];
            const ringArr = [];
            const geoJSONFormat = new ol.format.GeoJSON();
            coordArr.push([bbox.leftlong, bbox.bottomlat]);
            coordArr.push([bbox.rightlong, bbox.bottomlat]);
            coordArr.push([bbox.rightlong, bbox.upperlat]);
            coordArr.push([bbox.leftlong, bbox.upperlat]);
            coordArr.push([bbox.leftlong, bbox.bottomlat]);
            ringArr.push(coordArr);
            const newTurfPolygon = turf.polygon(ringArr);
            const newpoly = geoJSONFormat.readFeature(newTurfPolygon);
            newpoly.getGeometry().transform(wgs84Projection, mapProjection);
            newpoly.set('geoType','Box');
            mapSettings.selectSource.addFeature(newpoly);
            updateMapSettings('activeLayer', 'select');
            if(selected){
                mapSettings.selectedFeatures.push(newpoly);
            }
        }

        function createPolysFromFootprintWKT(footprintWKT) {
            const footprintpoly = getValidatedFootprintWkt(footprintWKT);
            if(footprintpoly){
                mapSettings.selectSource.addFeature(footprintpoly);
                mapSettings.selectedFeatures.push(footprintpoly);
                processInputSelections();
            }
            const selectextent = mapSettings.selectSource.getExtent();
            map.getView().fit(selectextent, map.getSize());
            let fittedZoom = map.getView().getZoom();
            map.getView().setZoom(fittedZoom - 2);
        }

        function createPolysFromPolyArr(polyArr, selected) {
            const wktFormat = new ol.format.WKT();
            polyArr.forEach((feature) => {
                let wktStr;
                wktStr = convertMysqlWKT(feature);
                const newpoly = wktFormat.readFeature(wktStr, mapProjection);
                newpoly.getGeometry().transform(wgs84Projection, mapProjection);
                mapSettings.selectSource.addFeature(newpoly);
                if(selected){
                    mapSettings.selectedFeatures.push(newpoly);
                }
            });
            updateMapSettings('activeLayer', 'select');
        }

        function createShapesFromSearchTermsArr() {
            if(searchTerms.value.hasOwnProperty('upperlat')){
                const boundingBox = {};
                boundingBox.upperlat = searchTerms.value['upperlat'];
                boundingBox.bottomlat = searchTerms.value['bottomlat'];
                boundingBox.leftlong = searchTerms.value['leftlong'];
                boundingBox.rightlong = searchTerms.value['rightlong'];
                if(boundingBox.upperlat && boundingBox.bottomlat && boundingBox.leftlong && boundingBox.rightlong){
                    createPolygonFromBoundingBox(boundingBox, true);
                }
            }
            if(searchTerms.value.hasOwnProperty('pointlat')){
                const pointRadius = {};
                pointRadius.pointlat = searchTerms.value['pointlat'];
                pointRadius.pointlong = searchTerms.value['pointlong'];
                pointRadius.radius = searchTerms.value['radius'];
                if(pointRadius.pointlat && pointRadius.pointlong && pointRadius.radius){
                    createCircleFromPointRadius(pointRadius, true);
                }
            }
            if(searchTerms.value.hasOwnProperty('circleArr')){
                let circleArr;
                if(JSON.parse(searchTerms.value['circleArr'])){
                    circleArr = JSON.parse(searchTerms.value['circleArr']);
                }
                else{
                    circleArr = searchTerms.value['circleArr'];
                }
                if(Array.isArray(circleArr)){
                    createCirclesFromCircleArr(circleArr, true);
                }
            }
            if(searchTerms.value.hasOwnProperty('polyArr')){
                let polyArr;
                if(JSON.parse(searchTerms.value['polyArr'])){
                    polyArr = JSON.parse(searchTerms.value['polyArr']);
                }
                else{
                    polyArr = searchTerms.value['polyArr'];
                }
                if(Array.isArray(polyArr)){
                    createPolysFromPolyArr(polyArr, true);
                }
            }
        }

        function createUncertaintyCircleFromPointRadius(prad) {
            const centerCoords = ol.proj.fromLonLat([prad.pointlong, prad.pointlat]);
            const circle = new ol.geom.Circle(centerCoords);
            circle.setRadius(Number(prad.radius));
            const circleFeature = new ol.Feature(circle);
            mapSettings.uncertaintyCircleSource.addFeature(circleFeature);
        }

        function emitClosePopup() {
            context.emit('close:spatial-popup', inputResponseData.value);
        }

        function findRecordCluster(id) {
            let targetCluster;
            const clusters = layersObj['pointv'].getSource().getFeatures();
            clusters.forEach((cluster) => {
                const clusterindex = cluster.get('identifiers');
                if(clusterindex.indexOf(Number(id)) !== -1){
                    targetCluster = cluster;
                }
            });
            return targetCluster;
        }

        function findRecordClusterPosition(id) {
            let position;
            if(spiderCluster){
                const spiderPoints = layersObj['spider'].getSource().getFeatures();
                spiderPoints.forEach((feature) => {
                    if(Number(feature.get('features')[0].get('id')) === id){
                        position = feature.getGeometry().getCoordinates();
                    }
                });
            }
            else if(mapSettings.clusterPoints){
                const clusters = layersObj['pointv'].getSource().getFeatures();
                clusters.forEach((cluster) => {
                    const clusterindex = cluster.get('identifiers');
                    if(clusterindex.indexOf(Number(id)) !== -1){
                        position = cluster.getGeometry().getCoordinates();
                    }
                });
            }
            else{
                const features = layersObj['pointv'].getSource().getFeatures();
                features.forEach((feature) => {
                    if(Number(feature.get('id')) === id){
                        position = feature.getGeometry().getCoordinates();
                    }
                });
            }
            return position;
        }

        function findRecordPoint(id) {
            let targetFeature;
            const features = layersObj['pointv'].getSource().getFeatures();
            features.forEach((feature) => {
                if(Number(feature.get('id')) === id){
                    targetFeature = feature;
                }
            });
            return targetFeature;
        }

        function findRecordPointInCluster(cluster, id) {
            let targetFeature;
            const cFeatures = cluster.get('features');
            cFeatures.forEach((feature) => {
                if(Number(feature.get('id')) === id){
                    targetFeature = feature;
                }
            });
            return targetFeature;
        }

        function getCoords() {
            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition((position) => {
                    if(position.coords.latitude){
                        updateMapSettings('distFromMeLat', position.coords.latitude);
                        updateMapSettings('distFromMeLong', position.coords.longitude);
                    }
                });
            }
        }

        function getGeographyParams() {
            geoPolyArr.value = [];
            geoCircleArr.value = [];
            let totalArea = 0;
            let polyArrVal = null;
            let circleArrVal = null;
            selectInteraction.value.getFeatures().forEach((feature) => {
                let turfSimple, options, area_km, area, areaFeat;
                if(feature){
                    const selectedClone = feature.clone();
                    const geoType = selectedClone.getGeometry().getType();
                    const geoJSONFormat = new ol.format.GeoJSON();
                    if(geoType === 'MultiPolygon' || geoType === 'Polygon') {
                        const selectiongeometry = selectedClone.getGeometry();
                        const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                        const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                        let polyCoords = JSON.parse(geojsonStr).coordinates;
                        if(geoType === 'MultiPolygon') {
                            areaFeat = turf.multiPolygon(polyCoords);
                            area = turf.area(areaFeat);
                            area_km = area / 1000 / 1000;
                            totalArea += area_km;
                            polyCoords.forEach((poly, index) => {
                                let singlePoly = turf.polygon(poly);
                                //console.log('start multipolygon length: '+singlePoly.geometry.coordinates.length);
                                if(singlePoly.geometry.coordinates.length > 10){
                                    options = {tolerance: 0.001, highQuality: true};
                                    singlePoly = turf.simplify(singlePoly,options);
                                }
                                //console.log('end multipolygon length: '+singlePoly.geometry.coordinates.length);
                                polyCoords[index] = singlePoly.geometry.coordinates;
                            });
                            turfSimple = turf.multiPolygon(polyCoords);
                        }
                        if(geoType === 'Polygon') {
                            areaFeat = turf.polygon(polyCoords);
                            area = turf.area(areaFeat);
                            area_km = area / 1000 / 1000;
                            totalArea += area_km;
                            //console.log('start multipolygon length: '+areaFeat.geometry.coordinates.length);
                            if(areaFeat.geometry.coordinates.length > 10){
                                options = {tolerance: 0.001, highQuality: true};
                                areaFeat = turf.simplify(areaFeat,options);
                            }
                            //console.log('end multipolygon length: '+areaFeat.geometry.coordinates.length);
                            polyCoords = areaFeat.geometry.coordinates;
                            turfSimple = turf.polygon(polyCoords);
                        }
                        const polySimple = geoJSONFormat.readFeature(turfSimple, {featureProjection: 'EPSG:3857'});
                        const simplegeometry = polySimple.getGeometry();
                        const fixedgeometry = simplegeometry.transform(mapProjection, wgs84Projection);
                        const geocoords = fixedgeometry.getCoordinates();
                        const mysqlWktString = writeMySQLWktString(geoType, geocoords);
                        geoPolyArr.value.push(mysqlWktString);
                    }
                    if(geoType === 'Circle'){
                        const center = selectedClone.getGeometry().getCenter();
                        const radius = selectedClone.getGeometry().getRadius();
                        const edgeCoordinate = [center[0] + radius, center[1]];
                        const fixedcenter = ol.proj.transform(center, 'EPSG:3857', 'EPSG:4326');
                        const fixededgeCoordinate = ol.proj.transform(edgeCoordinate, 'EPSG:3857', 'EPSG:4326');
                        const groundRadius = turf.distance([fixedcenter[0], fixedcenter[1]], [fixededgeCoordinate[0], fixededgeCoordinate[1]]);
                        const circleArea = Math.PI * groundRadius * groundRadius;
                        totalArea += circleArea;
                        const circleObj = {
                            pointlat: fixedcenter[1],
                            pointlong: fixedcenter[0],
                            radius: radius,
                            groundradius: groundRadius
                        };
                        geoCircleArr.value.push(circleObj);
                    }
                }
            });
            updateMapSettings('polyArea', totalArea === 0 ? totalArea : totalArea.toFixed(2));
            if(geoPolyArr.value.length > 0){
                const jsonPolyArr = JSON.stringify(geoPolyArr.value);
                if(jsonPolyArr.length < 5000000){
                    polyArrVal = jsonPolyArr;
                    selectedPolyError.value = false;
                }
                else{
                    selectedPolyError.value = true;
                }
            }
            searchStore.updateSearchTerms('polyArr', polyArrVal);
            if(geoCircleArr.value.length > 0){
                circleArrVal = JSON.stringify(geoCircleArr.value);
            }
            searchStore.updateSearchTerms('circleArr', circleArrVal);
        }

        function getPointFeatureInfoHtml(iFeature) {
            let infoHTML = '<div style="width: 225px;">';
            infoHTML += '<b>occid:</b> ' + iFeature.get('id') + '<br />';
            infoHTML += '<b>Collection Name:</b> ' + (iFeature.get('collectionname') ? iFeature.get('collectionname') : '') + '<br />';
            infoHTML += '<b>Catalog Number:</b> ' + (iFeature.get('catalognumber') ? iFeature.get('catalognumber') : '') + '<br />';
            infoHTML += '<b>Other Catalog Numbers:</b> ' + (iFeature.get('othercatalognumbers') ? iFeature.get('othercatalognumbers') : '') + '<br />';
            infoHTML += '<b>Family:</b> ' + (iFeature.get('family') ? iFeature.get('family') : '') + '<br />';
            infoHTML += '<b>Scientific Name:</b> ' + (iFeature.get('sciname') ? iFeature.get('sciname') : '') + '<br />';
            infoHTML += '<b>Recorded By:</b> ' + (iFeature.get('recordedby') ? iFeature.get('recordedby') : '') + '<br />';
            infoHTML += '<b>Record Number:</b> ' + (iFeature.get('recordnumber') ? iFeature.get('recordnumber') : '') + '<br />';
            infoHTML += '<b>Event Date:</b> ' + (iFeature.get('eventdate') ? iFeature.get('eventdate') : '') + '<br />';
            infoHTML += '<b>Habitat:</b> ' + (iFeature.get('habitat') ? iFeature.get('habitat') : '') + '<br />';
            infoHTML += '<b>Associated Taxa:</b> ' + (iFeature.get('associatedtaxa') ? iFeature.get('associatedtaxa') : '') + '<br />';
            infoHTML += '<b>Country:</b> ' + (iFeature.get('country') ? iFeature.get('country') : '') + '<br />';
            infoHTML += '<b>State/Province:</b> ' + (iFeature.get('stateprovince') ? iFeature.get('stateprovince') : '') + '<br />';
            infoHTML += '<b>County:</b> ' + (iFeature.get('county') ? iFeature.get('county') : '') + '<br />';
            infoHTML += '<b>Locality:</b> ' + (iFeature.get('locality') ? iFeature.get('locality') : '') + '<br />';
            if(iFeature.get('thumbnailurl')){
                const thumburl = iFeature.get('thumbnailurl');
                infoHTML += '<img src="' + thumburl + '"style="height:150px" />';
            }
            infoHTML += '</div>';
            return infoHTML;
        }

        function getPointInfoArr(cluster) {
            const feature = (cluster.get('features') ? cluster.get('features')[0] : cluster);
            const infoArr = [];
            infoArr['occid'] = Number(feature.get('id'));
            infoArr['institutioncode'] = (feature.get('institutioncode') ? feature.get('institutioncode') : '');
            infoArr['catalognumber'] = (feature.get('catalognumber') ? feature.get('catalognumber') : '');
            const recordedby = (feature.get('recordedby') ? feature.get('recordedby') : '');
            const recordnumber = (feature.get('recordnumber') ? feature.get('recordnumber') : '');
            infoArr['collector'] = (recordedby ? recordedby : '') + (recordedby && recordnumber ? ' ' : '') + (recordnumber ? recordnumber : '');
            infoArr['eventdate'] = (feature.get('eventdate') ? feature.get('eventdate') : '');
            infoArr['sciname'] = (feature.get('sciname') ? feature.get('sciname') : '');
            const featureClone = feature.clone();
            const featureGeometry = featureClone.getGeometry();
            const fixedFeatureGeometry = featureGeometry.transform(mapProjection, wgs84Projection);
            let pointCoords = fixedFeatureGeometry.getCoordinates();
            infoArr['lat'] = pointCoords[1];
            infoArr['lon'] = pointCoords[0];
            return infoArr;
        }

        function getPointStyle(feature) {
            let style;
            if(mapSettings.clusterPoints){
                style = setClusterSymbol(feature);
            }
            else{
                style = setSymbol(feature);
            }
            return style;
        }

        function getValidatedFootprintWkt(footprintWKT) {
            const wktFormat = new ol.format.WKT();
            const wgs84Projection = new ol.proj.Projection({
                code: 'EPSG:4326',
                units: 'degrees'
            });
            const mapProjection = new ol.proj.Projection({
                code: 'EPSG:3857'
            });
            const footprintpoly = wktFormat.readFeature(footprintWKT, mapProjection);
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

        function handleWindowResize() {
            windowWidth.value = window.innerWidth;
            updateMapSettings('showControlPanelTop', windowWidth.value >= 875);
        }

        function hideFeature(feature) {
            const invisibleStyle = new ol.style.Style({
                image: new ol.style.Circle({
                    radius: feature.get('radius'),
                    fill: new ol.style.Fill({
                        color: 'rgba(255, 255, 255, 0.01)'
                    })
                })
            });
            feature.setStyle(invisibleStyle);
        }

        function loadPointsLayer() {
            updateMapSettings('loadPointsEvent', true);
            updateMapSettings('loadPointsError', false);
            mapSettings.pointVectorSource.clear(true);
            let processed = 0;
            let index = 0;
            do {
                const options = {
                    schema: 'map',
                    spatial: 1,
                    numRows: lazyLoadCnt,
                    index: index,
                    output: 'geojson'
                };
                searchStore.processSearch(options, (res, index) => {
                    if(res){
                        const finalIndex = searchStore.getSearchRecordCount > lazyLoadCnt ? (Math.ceil(searchStore.getSearchRecordCount / lazyLoadCnt) - 1) : 0;
                        const format = new ol.format.GeoJSON();
                        let features = format.readFeatures(res, {
                            featureProjection: 'EPSG:3857'
                        });
                        if(mapSettings.toggleSelectedPoints){
                            const selections = searchStore.getSelectionsIds;
                            features = features.filter((feature) => {
                                const id = Number(feature.get('id'));
                                return (selections.indexOf(id) !== -1);
                            });
                        }
                        primeSymbologyData(features);
                        mapSettings.pointVectorSource.addFeatures(features);
                        if(index === finalIndex){
                            const pointextent = mapSettings.pointVectorSource.getExtent();
                            map.getView().fit(pointextent,map.getSize());
                            loadPointsPostrender();
                        }
                    }
                    else{
                        updateMapSettings('loadPointsError', true);
                        showNotification('negative','An error occurred while loading records.');
                        loadPointsPostrender();
                    }
                });
                processed += lazyLoadCnt;
                index++;
            }
            while(processed < searchStore.getSearchRecordCount && !mapSettings.loadPointsError);
            updateMapSettings('clusterSource', new ol.source.PropertyCluster({
                distance: mapSettings.clusterDistance,
                source: mapSettings.pointVectorSource,
                clusterkey: mapSettings.mapSymbology,
                indexkey: 'id',
                geometryFunction: (feature) => {
                    return feature.getGeometry();
                }
            }));

            layersObj['pointv'].setStyle(getPointStyle);
            if(mapSettings.clusterPoints){
                layersObj['pointv'].setSource(mapSettings.clusterSource);
            }
            else{
                layersObj['pointv'].setSource(mapSettings.pointVectorSource);
            }
            layersObj['heat'].setSource(mapSettings.pointVectorSource);
            if(mapSettings.showHeatMap){
                layersObj['heat'].setVisible(true);
            }
        }

        function loadPointsPostrender(){
            updateMapSettings('sidePanelExpandedElement', 'records');
            updateMapSettings('showSidePanel', true);
            if(!mapSettings.pointActive){
                const infoArr = [];
                infoArr['id'] = 'pointv';
                infoArr['type'] = 'userLayer';
                infoArr['fileType'] = 'vector';
                infoArr['layerName'] = 'Points';
                infoArr['layerDescription'] = 'This layer contains all of the spring points that have been loaded onto the map.';
                processAddedLayer(infoArr,true);
                updateMapSettings('pointActive', true);
            }
            updateMapSettings('loadPointsEvent', false);
            hideWorking();
        }

        function loadRecords(){
            if(!selectedPolyError.value){
                clearSelections(false);
                if(searchStore.getSearchTermsValid || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                    for(const key in symbologyArr){
                        delete symbologyArr[key];
                    }
                    symbologyArr['sciname'] = [];
                    symbologyArr['taxonomy'] = [];
                    searchStore.clearSelections();
                    searchStore.clearQueryOccidArr();
                    showWorking('Loading...');
                    const options = {
                        schema: 'map',
                        spatial: 1
                    };
                    searchStore.setSearchOccidArr(options, () => {
                        if(Number(searchStore.getSearchRecordCount) > 0){
                            displayQueryPopup.value = false;
                            updateMapSettings('showControlPanelLeft', false);
                            loadPointsLayer();
                        }
                        else{
                            if(mapSettings.pointActive){
                                removeLayerFromActiveLayerOptions('pointv');
                                updateMapSettings('pointActive', false);
                            }
                            hideWorking();
                            showNotification('negative','There were no records matching your query.');
                        }
                    });
                }
                else{
                    showNotification('negative','Please enter search criteria.');
                }
            }
            else{
                showNotification('negative','You have too many complex polygons selected. Please deselect one or more polygons in order to Load Records.');
            }
        }

        function mapPostLoadInitialize() {
            spatialModuleInitialising.value = false;
            if(!propsRefs.inputWindowMode.value && (props.queryId || props.stArrJson)){
                showWorking('Loading...');
            }
            if(!propsRefs.inputWindowMode.value && (props.queryId || props.stArrJson)){
                if(props.stArrJson){
                    searchStore.loadSearchTermsArrFromJson(props.stArrJson.replaceAll('%squot;', "'"));
                }
                if(searchStore.getSearchTermsValid || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                    updateMapSettings('loadPointsEvent', true);
                    createShapesFromSearchTermsArr();
                    loadRecords();
                }
            }
            window.addEventListener('resize', handleWindowResize);
            handleWindowResize();
        }

        function openRecordInfoWindow(id){
            updateMapSettings('recordInfoWindowId', id);
            updateMapSettings('showRecordInfoWindow', true);
        }

        function primeSymbologyData(features) {
            const symbologyOptions = spatialStore.getSymbologyOptions;
            features.forEach((feature) => {
                const family = feature.get('family');
                const sciname = feature.get('sciname');
                symbologyOptions.forEach((option) => {
                    if(option['field'] !== 'sciname'){
                        let field = option['field'];
                        let featureValue = feature.get(field);
                        if(!symbologyArr.hasOwnProperty(field)){
                            symbologyArr[field] = [];
                        }
                        if(!symbologyArr[field].find(key => key['value'] === featureValue)){
                            const keyObject = {};
                            keyObject['value'] = featureValue;
                            keyObject['color'] = mapSettings.pointLayerFillColor;
                            symbologyArr[field].push(keyObject);
                        }
                    }
                });
                if(!symbologyArr['sciname'].find(key => key['value'] === sciname)){
                    if(family){
                        if(!symbologyArr['taxonomy'].find(key => key['value'] === family)){
                            const familyObject = {};
                            familyObject['value'] = family;
                            familyObject['taxa'] = [];
                            symbologyArr['taxonomy'].push(familyObject);
                        }
                        const familySymbology = symbologyArr['taxonomy'].find(key => key['value'] === family);
                        if(!familySymbology['taxa'].find(key => key['value'] === sciname)){
                            const taxonObject = {};
                            taxonObject['value'] = sciname;
                            familySymbology['taxa'].push(Object.assign({}, taxonObject));
                            taxonObject['color'] = mapSettings.pointLayerFillColor;
                            symbologyArr['sciname'].push(taxonObject);
                        }
                    }
                    else{
                        if(!symbologyArr['taxonomy'].find(key => key['value'] === sciname)){
                            const taxonObject = {};
                            taxonObject['value'] = sciname;
                            taxonObject['taxa'] = [];
                            symbologyArr['taxonomy'].push(Object.assign({}, taxonObject));
                            taxonObject['color'] = mapSettings.pointLayerFillColor;
                            symbologyArr['sciname'].push(taxonObject);
                        }
                    }
                }
            });
            sortSymbologyArr();
        }

        function processAddedLayer(layerData,active) {
            if(!layersInfoObj.hasOwnProperty(layerData['id'].toString())){
                layerData['active'] = (active || layerData['id'] === 'select');
                layerData['layerOrder'] = 0;
                layersInfoObj[layerData['id'].toString()] = Object.assign({}, layerData);
                if(layerData['id'] !== 'pointv' && layerData['id'] !== 'select' && active){
                    layerOrderArr.push(layerData['id'].toString());
                    setLayersOrder();
                }
                if(active || layerData['id'] === 'select'){
                    addLayerToActiveLayerOptions(layerData['id'], layerData['layerName'], active);
                }
            }
            else if(active){
                updateMapSettings('activeLayer', layerData['id'].toString());
            }
        }

        function processInputPointUncertaintyChange() {
            mapSettings.uncertaintyCircleSource.clear();
            if(mapSettings.uncertaintyRadiusValue && Number(mapSettings.uncertaintyRadiusValue) > 0){
                selectInteraction.value.getFeatures().forEach((feature) => {
                    if(feature){
                        const featureClone = feature.clone();
                        const geoType = featureClone.getGeometry().getType();
                        const geoJSONFormat = new ol.format.GeoJSON();
                        if(geoType === 'Point'){
                            const selectiongeometry = featureClone.getGeometry();
                            const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                            const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                            let pointCoords = JSON.parse(geojsonStr).coordinates;
                            const pointRadius = {};
                            pointRadius.pointlat = pointCoords[1];
                            pointRadius.pointlong = pointCoords[0];
                            pointRadius.radius = mapSettings.uncertaintyRadiusValue;
                            createUncertaintyCircleFromPointRadius(pointRadius);
                        }
                    }
                });
            }
        }

        function processInputSelections() {
            inputResponseData.value = Object.assign({}, {});
            geoPolyArr.value = [];
            geoCircleArr.value = [];
            geoBoundingBoxArr.value = Object.assign({}, {});
            geoPointArr.value = [];
            geoCentroidArr.value = [];
            let totalArea = 0;
            let submitReady = false;
            selectInteraction.value.getFeatures().forEach((feature) => {
                let turfSimple;
                let options;
                let area_km;
                let area;
                let areaFeat;
                if(feature){
                    const featureProps = feature.getProperties();
                    const selectedClone = feature.clone();
                    const geoType = selectedClone.getGeometry().getType();
                    const wktFormat = new ol.format.WKT();
                    const geoJSONFormat = new ol.format.GeoJSON();
                    if(geoType === 'Point'){
                        const selectiongeometry = selectedClone.getGeometry();
                        const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                        const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                        let pointCoords = JSON.parse(geojsonStr).coordinates;
                        const pointObj = {
                            decimalLatitude: pointCoords[1],
                            decimalLongitude: pointCoords[0]
                        };
                        geoPointArr.value.push(pointObj);
                    }
                    if(geoType === 'MultiPolygon' || geoType === 'Polygon') {
                        const boxType = (featureProps.hasOwnProperty('geoType') && featureProps['geoType'] === 'Box');
                        const selectiongeometry = selectedClone.getGeometry();
                        const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                        const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                        let polyCoords = JSON.parse(geojsonStr).coordinates;
                        if(props.inputWindowMode && props.inputWindowToolsArr.includes('box') && boxType){
                            areaFeat = turf.polygon(polyCoords);
                            area = turf.area(areaFeat);
                            area_km = area / 1000 / 1000;
                            totalArea += area_km;
                            geoBoundingBoxArr.value = {
                                upperlat: polyCoords[0][2][1],
                                bottomlat: polyCoords[0][0][1],
                                leftlong: polyCoords[0][0][0],
                                rightlong: polyCoords[0][1][0]
                            };
                        }
                        else{
                            if (geoType === 'MultiPolygon') {
                                areaFeat = turf.multiPolygon(polyCoords);
                                area = turf.area(areaFeat);
                                area_km = area / 1000 / 1000;
                                totalArea += area_km;
                                polyCoords.forEach((coords, index) => {
                                    let singlePoly = turf.polygon(coords);
                                    //console.log('start multipolygon length: '+singlePoly.geometry.coordinates.length);
                                    if(singlePoly.geometry.coordinates.length > 10){
                                        options = {tolerance: 0.001, highQuality: true};
                                        singlePoly = turf.simplify(singlePoly,options);
                                    }
                                    //console.log('end multipolygon length: '+singlePoly.geometry.coordinates.length);
                                    polyCoords[index] = singlePoly.geometry.coordinates;
                                });
                                turfSimple = turf.multiPolygon(polyCoords);
                            }
                            if (geoType === 'Polygon') {
                                areaFeat = turf.polygon(polyCoords);
                                area = turf.area(areaFeat);
                                area_km = area / 1000 / 1000;
                                totalArea += area_km;
                                //console.log('start multipolygon length: '+areaFeat.geometry.coordinates.length);
                                if(areaFeat.geometry.coordinates.length > 10){
                                    options = {tolerance: 0.001, highQuality: true};
                                    areaFeat = turf.simplify(areaFeat,options);
                                }
                                //console.log('end multipolygon length: '+areaFeat.geometry.coordinates.length);
                                polyCoords = areaFeat.geometry.coordinates;
                                turfSimple = turf.polygon(polyCoords);
                            }
                            const polySimple = geoJSONFormat.readFeature(turfSimple, {featureProjection: 'EPSG:3857'});
                            const simplegeometry = polySimple.getGeometry();
                            const fixedgeometry = simplegeometry.transform(mapProjection, wgs84Projection);
                            if(props.inputWindowToolsArr.includes('wkt')) {
                                const wmswktString = wktFormat.writeGeometry(fixedgeometry);
                                geoPolyArr.value.push(wmswktString);
                                const centroid = turf.centroid(turfSimple);
                                if(centroid && centroid.hasOwnProperty('geometry')){
                                    const pointObj = {
                                        decimalLatitude: centroid['geometry']['coordinates'][1],
                                        decimalLongitude: centroid['geometry']['coordinates'][0]
                                    };
                                    geoCentroidArr.value.push(pointObj);
                                }
                            }
                            else{
                                const geocoords = fixedgeometry.getCoordinates();
                                const mysqlWktString = writeMySQLWktString(geoType, geocoords);
                                geoPolyArr.value.push(mysqlWktString);
                            }
                        }
                    }
                    if(geoType === 'Circle'){
                        const center = selectedClone.getGeometry().getCenter();
                        const radius = selectedClone.getGeometry().getRadius();
                        const edgeCoordinate = [center[0] + radius, center[1]];
                        const fixedcenter = ol.proj.transform(center, 'EPSG:3857', 'EPSG:4326');
                        const fixededgeCoordinate = ol.proj.transform(edgeCoordinate, 'EPSG:3857', 'EPSG:4326');
                        const groundRadius = turf.distance([fixedcenter[0], fixedcenter[1]], [fixededgeCoordinate[0], fixededgeCoordinate[1]]);
                        const circleArea = Math.PI * groundRadius * groundRadius;
                        totalArea += circleArea;
                        const circleObj = {
                            pointlat: fixedcenter[1],
                            pointlong: fixedcenter[0],
                            radius: radius,
                            radiusval: mapSettings.uncertaintyRadiusValue,
                            groundradius: groundRadius,
                            radiusunits: mapSettings.radiusUnits
                        };
                        geoCircleArr.value.push(circleObj);
                    }
                }
            });
            updateMapSettings('polyArea', totalArea === 0 ? totalArea : totalArea.toFixed(2));
            if(props.inputWindowMode && ((props.inputWindowToolsArr.length === 0) || (props.inputWindowToolsArr.length > 0 && selectInteraction.value.getFeatures().getLength() === 1))){
                if(geoPolyArr.value.length > 0){
                    submitReady = true;
                    if(props.inputWindowToolsArr.includes('wkt')){
                        inputResponseData.value['footprintWKT'] = geoPolyArr.value[0];
                        inputResponseData.value['centroid'] = geoCentroidArr.value[0];
                    }
                    else{
                        inputResponseData.value['polyArr'] = geoPolyArr.value;
                    }
                }
                if(geoCircleArr.value.length > 0){
                    submitReady = true;
                    inputResponseData.value['circleArr'] = geoCircleArr.value;
                }
                if(geoBoundingBoxArr.value.hasOwnProperty('upperlat')){
                    submitReady = true;
                    inputResponseData.value['boundingBoxArr'] = geoBoundingBoxArr.value;
                }
                if(geoPointArr.value.length > 0){
                    submitReady = true;
                    inputResponseData.value['pointArr'] = geoPointArr.value;
                    if(geoPointArr.value.length === 1){
                        inputResponseData.value['decimalLatitude'] = geoPointArr.value[0]['decimalLatitude'];
                        inputResponseData.value['decimalLongitude'] = geoPointArr.value[0]['decimalLongitude'];
                    }
                }
                if(mapSettings.uncertaintyRadiusValue){
                    inputResponseData.value['coordinateUncertaintyInMeters'] = mapSettings.uncertaintyRadiusValue;
                }
            }
            updateMapSettings('submitButtonDisabled', !submitReady);
        }

        function processInputSubmit() {
            context.emit('update:spatial-data', inputResponseData.value);
        }

        function processPointSelection(sFeature) {
            const feature = (sFeature.get('features') ? sFeature.get('features')[0] : sFeature);
            const id = Number(feature.get('id'));
            const selections = searchStore.getSelectionsIds;
            if(selections.indexOf(id) < 0){
                searchStore.addRecordToSelections(getPointInfoArr(sFeature));
            }
            else{
                searchStore.removeRecordFromSelections(id);
                if(searchStore.getSelectionsIds.length === 0){
                    updateMapSettings('selectedRecordsSelectionsSymbologyTab', 'records');
                }
            }
            updatePointStyle(id);
        }

        function processResetCriteria() {
            if(Number(searchRecordCnt.value) === 0){
                removeUserLayer('pointv');
            }
            if(occurrenceEditorModeActive.value){
                loadRecords();
            }
        }

        function processSymbologyKeyColorChange(color, keyValue) {
            const symbologyData = symbologyArr[mapSettings.mapSymbology].find(key => key['value'] === keyValue);
            if(symbologyData){
                symbologyData.color = color;
            }
            layersObj['pointv'].getSource().changed();
            if(spiderCluster){
                const spiderFeatures = layersObj['spider'].getSource().getFeatures();
                spiderFeatures.forEach(feature => {
                    const style = feature.get('features') ? setClusterSymbol(feature) : setSymbol(feature);
                    feature.setStyle(style);
                });
            }
        }

        function processToggleSelectedChange() {
            if(mapSettings.clusterPoints){
                loadPointsLayer();
            }
            else{
                layersObj['pointv'].setSource(mapSettings.pointVectorSource);
            }
        }

        function processVectorInteraction() {
            if(!spatialModuleInitialising.value){
                let featureCount = 0;
                let polyCount = 0;
                selectInteraction.value.getFeatures().forEach((feature) => {
                    const selectedClone = feature.clone();
                    const geoType = selectedClone.getGeometry().getType();
                    if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
                        polyCount++;
                    }
                    featureCount++;
                });
                updateMapSettings('featureCount', featureCount);
                updateMapSettings('polyCount', polyCount);
                if(!props.inputWindowMode){
                    getGeographyParams();
                }
                else{
                    processInputSelections();
                }
            }
        }

        function removeLayerFromActiveLayerOptions(id) {
            const copyArr = activeLayerSelectorOptions.slice();
            activeLayerSelectorOptions.length = 0;
            copyArr.forEach(option => {
                if(option.value !== id){
                    activeLayerSelectorOptions.push(option);
                }
            });
        }

        function removeLayerFromLayersObj(id) {
            delete layersObj[id];
        }

        function removeLayerFromRasterLayersArr(id) {
            const copyArr = rasterLayersArr.value.slice();
            rasterLayersArr.value.length = 0;
            copyArr.forEach(option => {
                if(option.value !== id){
                    rasterLayersArr.value.push(option);
                }
            });
        }

        function removeUserLayer(layerID) {
            delete layersInfoObj[layerID];
            if(layerID === 'select'){
                selectInteraction.value.getFeatures().clear();
                layersObj[layerID].getSource().clear(true);
                updateMapSettings('shapeActive', false);
            }
            else if(layerID === 'pointv'){
                clearSelections(false);
                mapSettings.pointVectorSource.clear(true);
                layersObj['heat'].setVisible(false);
                updateMapSettings('clusterSource', null);
                updateMapSettings('pointActive', false);
            }
            else{
                if(layerID === 'dragDrop1' || layerID === 'dragDrop2' || layerID === 'dragDrop3'){
                    layersObj[layerID].setSource(mapSettings.blankDragDropSource);
                    const sourceIndex = mapSettings.dragDropTarget + 'Source';
                    delete layersObj[sourceIndex];
                    if(layerID === 'dragDrop1') {
                        updateMapSettings('dragDrop1', false);
                    }
                    else if(layerID === 'dragDrop2') {
                        updateMapSettings('dragDrop2', false);
                    }
                    else if(layerID === 'dragDrop3') {
                        updateMapSettings('dragDrop3', false);
                    }
                }
                else if(layerID === 'dragDrop4' || layerID === 'dragDrop5' || layerID === 'dragDrop6') {
                    map.removeLayer(layersObj[layerID]);
                    layersObj[layerID].setSource(null);
                    const sourceIndex = mapSettings.dragDropTarget + 'Source';
                    const dataIndex = mapSettings.dragDropTarget + 'Data';
                    delete layersObj[sourceIndex];
                    delete layersObj[dataIndex];
                    if(layerID === 'dragDrop4') {
                        updateMapSettings('dragDrop4', false);
                    }
                    else if(layerID === 'dragDrop5') {
                        updateMapSettings('dragDrop5', false);
                    }
                    else if(layerID === 'dragDrop6') {
                        updateMapSettings('dragDrop6', false);
                    }
                    rasterLayersArr.value = rasterLayersArr.value.filter((obj) => {
                        return obj.value !== layerID;
                    });
                }
            }
            updateMapSettings('activeLayer', 'none');
            removeLayerFromActiveLayerOptions(layerID);
        }

        function resetSymbology() {
            for(let key in symbologyArr) {
                if(key !== 'taxonomy' && symbologyArr.hasOwnProperty(key)){
                    symbologyArr[key].forEach(keyObject => {
                        keyObject['color'] = mapSettings.pointLayerFillColor;
                    });
                }
            }
            changeMapSymbology();
        }

        function setClusterSymbol(feature) {
            let clusterindex, hexcolor, radius;
            let style = '';
            let stroke = '';
            let selected = false;
            if(feature.get('features')){
                const size = feature.get('features').length;
                if(size > 1){
                    const selections = searchStore.getSelectionsIds;
                    if(selections.length > 0){
                        clusterindex = feature.get('identifiers');
                        selections.forEach((id) => {
                            if(clusterindex.indexOf(id) !== -1) {
                                selected = true;
                            }
                        });
                    }
                    clusterindex = feature.get('identifiers');
                    const cKey = feature.get('clusterkey');
                    const symbologyData = symbologyArr[mapSettings.mapSymbology].find(key => key['value'] === cKey);
                    hexcolor = symbologyData['color'];
                    const colorArr = hexToRgb(hexcolor);
                    if(size < 10) {
                        radius = 10;
                    }
                    else if(size < 100) {
                        radius = 15;
                    }
                    else if(size < 1000) {
                        radius = 20;
                    }
                    else if(size < 10000) {
                        radius = 25;
                    }
                    else if(size < 100000) {
                        radius = 30;
                    }
                    else {
                        radius = 35;
                    }
                    if(selected) {
                        stroke = new ol.style.Stroke({color: mapSettings.pointLayerSelectionsBorderColor, width: Number(mapSettings.pointLayerSelectionsBorderWidth)})
                    }
                    style = new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: radius,
                            stroke: stroke,
                            fill: new ol.style.Fill({
                                color: [colorArr['r'],colorArr['g'],colorArr['b'],0.8]
                            })
                        }),
                        text: new ol.style.Text({
                            scale: 1,
                            text: size.toString(),
                            fill: new ol.style.Fill({
                                color: '#fff'
                            }),
                            stroke: new ol.style.Stroke({
                                color: 'rgba(0, 0, 0, 0.6)',
                                width: 3
                            })
                        })
                    });
                }
                else{
                    const originalFeature = feature.get('features')[0];
                    style = setSymbol(originalFeature);
                }
            }
            return style;
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

        function setLayersController() {
            baseStore.getGlobalConfigValue('SPATIAL_LAYER_CONFIG_JSON', (dataStr) => {
                const data = dataStr ? JSON.parse(dataStr) : null;
                if(data && data.length > 0){
                    data.forEach((object) => {
                        layersConfigArr.push(object);
                    });
                    layersConfigArr.forEach((object) => {
                        if(object['type'] === 'layer'){
                            processAddedLayer(object,false);
                        }
                        if(object['type'] === 'layerGroup' && object.hasOwnProperty('layers') && object['layers'].length > 0){
                            object['layers'].forEach((groupObject) => {
                                processAddedLayer(groupObject,false);
                            });
                        }
                    });
                }
            });
        }

        function setLayersOrder() {
            const layersObjKeys = Object.keys(layersObj);
            const layersObjLength = layersObjKeys.length;
            layerOrderArr.forEach((layerId, index) => {
                layersObj[layerId].setZIndex(index + 1);
                layersInfoObj[layerId]['layerOrder'] = (index + 1);
            });
            layersObj['base'].setZIndex(0);
            if(layersObj.hasOwnProperty('uncertainty')){
                layersObj['uncertainty'].setZIndex((layersObjLength - 4));
            }
            if(layersObj.hasOwnProperty('select')){
                layersObj['select'].setZIndex((layersObjLength - 3));
            }
            if(layersObj.hasOwnProperty('pointv')){
                layersObj['pointv'].setZIndex((layersObjLength - 2));
            }
            if(layersObj.hasOwnProperty('heat')){
                layersObj['heat'].setZIndex((layersObjLength - 1));
            }
            if(layersObj.hasOwnProperty('spider')){
                layersObj['spider'].setZIndex(layersObjLength);
            }
            if(layersObj.hasOwnProperty('radius')){
                layersObj['radius'].setZIndex((layersObjLength - 1));
            }
            if(layersObj.hasOwnProperty('vector')){
                layersObj['vector'].setZIndex(layersObjLength);
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
                target: 'map',
                layers: layersArr,
                overlays: [popupOverlay]
            });
            map.addInteraction(rasterAnalysisInteraction.value);
            map.addInteraction(rasterAnalysisTranslate.value);
            map.addControl(new ol.control.FullScreen());
            map.getView().on('change:resolution', () => {
                if(spiderCluster){
                    const source = layersObj['spider'].getSource();
                    source.clear();
                    const blankSource = new ol.source.Vector({
                        features: new ol.Collection(),
                        useSpatialIndex: true
                    });
                    layersObj['spider'].setSource(blankSource);
                    hiddenClusters.value.forEach((cluster) => {
                        showFeature(cluster);
                    });
                    hiddenClusters.value = [];
                    spiderCluster = false;
                    layersObj['pointv'].getSource().changed();
                }
            });
            map.getViewport().addEventListener('drop', (event) => {
                let filename = event.dataTransfer.files[0].name.split('.');
                const fileType = filename.pop();
                if(fileType === 'csv'){
                    if(setDragDropTarget()){
                        const pointArr = [];
                        showWorking('Loading...');
                        parseFile(event.dataTransfer.files[0], (fileContents) => {
                            csvToArray(fileContents).then((csvData) => {
                                csvData.forEach((dataObj) => {
                                    if(dataObj){
                                        let latitudeField, longitudeField;
                                        latitudeField = Object.keys(dataObj).find(field => field.toLowerCase() === 'decimallatitude');
                                        if(!latitudeField){
                                            latitudeField = Object.keys(dataObj).find(field => field.toLowerCase() === 'latitude');
                                        }
                                        longitudeField = Object.keys(dataObj).find(field => field.toLowerCase() === 'decimallongitude');
                                        if(!longitudeField){
                                            longitudeField = Object.keys(dataObj).find(field => field.toLowerCase() === 'longitude');
                                        }
                                        if(latitudeField && dataObj[latitudeField] && !isNaN(dataObj[latitudeField]) && longitudeField && dataObj[longitudeField] && !isNaN(dataObj[longitudeField])){
                                            const newPointGeometry = new ol.geom.Point(ol.proj.fromLonLat([Number(dataObj[longitudeField]), Number(dataObj[latitudeField])]));
                                            const pointFeature = new ol.Feature(newPointGeometry);
                                            Object.keys(dataObj).forEach((field) => {
                                                if(field !== latitudeField && field !== longitudeField){
                                                    pointFeature.set(field, dataObj[field]);
                                                }
                                            });
                                            pointArr.push(pointFeature);
                                        }
                                    }
                                });
                                if(pointArr.length > 0){
                                    const infoArr = [];
                                    infoArr['id'] = mapSettings.dragDropTarget;
                                    infoArr['type'] = 'userLayer';
                                    infoArr['fileType'] = fileType;
                                    infoArr['layerName'] = filename[0];
                                    infoArr['layerDescription'] = 'This layer is from a file that was added to the map.';
                                    infoArr['fillColor'] = mapSettings.dragDropFillColor;
                                    infoArr['borderColor'] = mapSettings.dragDropBorderColor;
                                    infoArr['borderWidth'] = mapSettings.dragDropBorderWidth;
                                    infoArr['pointRadius'] = mapSettings.dragDropPointRadius;
                                    infoArr['opacity'] = mapSettings.dragDropOpacity;
                                    const sourceIndex = mapSettings.dragDropTarget + 'Source';
                                    layersObj[sourceIndex] = new ol.source.Vector({
                                        features: pointArr
                                    });
                                    layersObj[mapSettings.dragDropTarget].setSource(layersObj[sourceIndex]);
                                    processAddedLayer(infoArr,true);
                                    map.getView().fit(layersObj[sourceIndex].getExtent());
                                }
                                else{
                                    hideWorking();
                                }
                            });
                        });
                    }
                }
            });
            map.on('singleclick', (evt) => {
                let infoHTML;
                if(evt.originalEvent.altKey){
                    if(mapSettings.activeLayer === 'pointv'){
                        let targetFeature = '';
                        let iFeature = '';
                        if(clickedFeatures.length === 1){
                            targetFeature = clickedFeatures[0];
                        }
                        if(targetFeature){
                            if(mapSettings.clusterPoints && targetFeature.get('features').length === 1){
                                iFeature = targetFeature.get('features')[0];
                            }
                            else if(!mapSettings.clusterPoints){
                                iFeature = targetFeature;
                            }
                        }
                        else{
                            return;
                        }
                        if(iFeature){
                            showPopup(getPointFeatureInfoHtml(iFeature), evt.coordinate, true);
                        }
                        else{
                            showNotification('negative','You clicked on multiple points. The info window can only display data for a single point.');
                        }
                        clickedFeatures.length = 0;
                    }
                    else if(mapSettings.activeLayer === 'dragDrop4' || mapSettings.activeLayer === 'dragDrop5' || mapSettings.activeLayer === 'dragDrop6' || layersObj[mapSettings.activeLayer] instanceof ol.layer.Image){
                        infoHTML = '';
                        const coords = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                        const dataIndex = mapSettings.activeLayer + 'Data';
                        const x = Math.floor(layersObj[dataIndex]['imageWidth'] * (coords[0] - layersObj[dataIndex]['x_min']) / (layersObj[dataIndex]['x_max'] - layersObj[dataIndex]['x_min']));
                        const y = layersObj[dataIndex]['imageHeight'] - Math.ceil(layersObj[dataIndex]['imageHeight'] * (coords[1] - layersObj[dataIndex]['y_max']) / (layersObj[dataIndex]['y_min'] - layersObj[dataIndex]['y_max']));
                        const rasterDataIndex = (Number(layersObj[dataIndex]['imageWidth']) * y) + x;
                        infoHTML += '<b>Value:</b> ' + layersObj[dataIndex]['data'][rasterDataIndex] + '<br />';
                        showPopup(infoHTML, evt.coordinate, true);
                    }
                    else if(layersObj[mapSettings.activeLayer] instanceof ol.layer.Vector){
                        infoHTML = '';
                        const feature = map.forEachFeatureAtPixel(evt.pixel, (feature, layer) => {
                            if(layer === layersObj[mapSettings.activeLayer]){
                                return feature;
                            }
                        });
                        if(feature){
                            const properties = feature.getKeys();
                            properties.forEach((prop) => {
                                const propValue = feature.get(prop);
                                if(String(prop) !== 'geometry' && propValue){
                                    infoHTML += '<b>' + prop + ':</b> ' + propValue + '<br />';
                                }
                            });
                            if(infoHTML){
                                showPopup(infoHTML, evt.coordinate, true);
                            }
                        }
                    }
                }
                else{
                    const coords = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    if(coords[0] < -180){
                        coords[0] = coords[0] + 360;
                    }
                    if(coords[0] > 180){
                        coords[0] = coords[0] - 360;
                    }
                    const template = 'Lat: {y} Lon: {x}';
                    document.getElementById('mapcoords').children[0].innerHTML = ol.coordinate.format(coords,template,5);
                    if(mapSettings.activeLayer !== 'none' && mapSettings.activeLayer !== 'select' && mapSettings.activeLayer !== 'pointv'){
                        if(layersObj[mapSettings.activeLayer] instanceof ol.layer.Vector){
                            map.forEachFeatureAtPixel(evt.pixel, (feature, layer) => {
                                if(layer === layersObj[mapSettings.activeLayer]){
                                    if(!mapSettings.selectSource.hasFeature(feature)){
                                        const featureClone = feature.clone();
                                        mapSettings.selectSource.addFeature(featureClone);
                                    }
                                }
                            });
                        }
                    }
                }
            });
            map.on('pointermove', (evt) => {
                if(!mapSettings.draw){
                    if(mapSettings.activeLayer === 'none'){
                        let infoHTML = '';
                        let idArr = [];
                        map.forEachFeatureAtPixel(evt.pixel, (feature, layer) => {
                            if(layer === layersObj['pointv']){
                                let iFeature;
                                if(feature){
                                    if(mapSettings.clusterPoints && feature.get('features').length === 1){
                                        iFeature = feature.get('features')[0];
                                    }
                                    else if(!mapSettings.clusterPoints){
                                        iFeature = feature;
                                    }
                                }
                                if(iFeature){
                                    infoHTML = getPointFeatureInfoHtml(iFeature);
                                }
                            }
                            else{
                                activeLayerSelectorOptions.forEach((option) => {
                                    if(option.value !== 'none' && layer === layersObj[option.value] && !idArr.includes(option.value)){
                                        idArr.push(option.value);
                                        if(infoHTML){
                                            infoHTML += '<br />';
                                        }
                                        infoHTML += option.label;
                                    }
                                });
                            }
                        });
                        if(infoHTML){
                            showPopup(infoHTML, evt.coordinate, false);
                        }
                        else{
                            closePopup();
                        }
                    }
                    else{
                        const featureHover = map.forEachFeatureAtPixel(evt.pixel, (feature, layer) => {
                            if(layer === layersObj[mapSettings.activeLayer] || layer === layersObj['spider']){
                                return true;
                            }
                        });
                        if(featureHover){
                            map.getTargetElement().style.cursor = 'pointer';
                        }
                        else{
                            map.getTargetElement().style.cursor = '';
                        }
                    }
                }
            });
            map.on('loadend', () => {
                if(spatialModuleInitialising.value){
                    mapPostLoadInitialize();
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
            layersObj['dragDrop4'] = new ol.layer.Image({
                zIndex: 4,
            });
            layersObj['dragDrop5'] = new ol.layer.Image({
                zIndex: 5,
            });
            layersObj['dragDrop6'] = new ol.layer.Image({
                zIndex: 6,
            });
            layersObj['uncertainty'] = new ol.layer.Vector({
                zIndex: 7,
                source: mapSettings.uncertaintyCircleSource,
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
            layersArr.push(layersObj['uncertainty']);
            layersObj['select'] = new ol.layer.Vector({
                zIndex: 8,
                source: mapSettings.selectSource,
                style: getVectorLayerStyle(mapSettings.shapesFillColor, mapSettings.shapesBorderColor, mapSettings.shapesBorderWidth, mapSettings.shapesPointRadius, mapSettings.shapesOpacity)
            });
            layersArr.push(layersObj['select']);
            layersObj['pointv'] = new ol.layer.Vector({
                zIndex: 9,
                source: mapSettings.pointVectorSource
            });
            layersArr.push(layersObj['pointv']);
            layersObj['heat'] = new ol.layer.Heatmap({
                zIndex: 10,
                source: mapSettings.pointVectorSource,
                weight: () => {
                    return 1;
                },
                gradient: ['#00f', '#0ff', '#0f0', '#ff0', '#f00'],
                blur: parseInt(mapSettings.heatMapBlur.toString(), 10),
                radius: parseInt(mapSettings.heatMapRadius.toString(), 10),
                visible: false
            });
            layersArr.push(layersObj['heat']);
            layersObj['spider'] = new ol.layer.Vector({
                zIndex: 11,
                source: new ol.source.Vector({
                    features: new ol.Collection(),
                    useSpatialIndex: true
                })
            });
            layersArr.push(layersObj['spider']);
            layersObj['rasteranalysis'] = new ol.layer.Vector({
                zIndex: 12,
                source: mapSettings.rasterAnalysisSource,
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
            layersArr.push(layersObj['rasteranalysis']);
            layersObj['dragDrop1'].on('postrender', () => {
                if(!mapSettings.loadPointsEvent){
                    hideWorking();
                }
            });
            layersObj['dragDrop2'].on('postrender', () => {
                if(!mapSettings.loadPointsEvent){
                    hideWorking();
                }
            });
            layersObj['dragDrop3'].on('postrender', () => {
                if(!mapSettings.loadPointsEvent){
                    hideWorking();
                }
            });
            layersObj['select'].on('postrender', () => {
                if(!mapSettings.loadPointsEvent){
                    hideWorking();
                }
            });
            layersObj['pointv'].on('prerender', () => {
                if(mapSettings.loadPointsEvent){
                    showWorking('Loading...');
                }
            });
            layersObj['heat'].on('prerender', () => {
                if(mapSettings.loadPointsEvent){
                    showWorking('Loading...');
                }
            });
            layersObj['pointv'].on('postrender', () => {
                if(mapSettings.loadPointsEvent && ((mapSettings.pointVectorSource.getFeatures().length === Number(searchStore.getSearchRecordCount)) || (mapSettings.toggleSelectedPoints && mapSettings.pointVectorSource.getFeatures().length === searchStore.getSelectionsIds.length))){
                    loadPointsPostrender();
                }
            });
            layersObj['heat'].on('postrender', () => {
                if(mapSettings.loadPointsEvent && ((mapSettings.pointVectorSource.getFeatures().length === Number(searchStore.getSearchRecordCount)) || (mapSettings.toggleSelectedPoints && mapSettings.pointVectorSource.getFeatures().length === searchStore.getSelectionsIds.length))){
                    loadPointsPostrender();
                }
            });
            dragAndDropInteraction.on('addfeatures', (event) => {
                let filename = event.file.name.split('.');
                const fileType = filename.pop();
                if(fileType === 'geojson' || fileType === 'kml' || fileType === 'zip' || fileType === 'tif' || fileType === 'tiff'){
                    if(fileType === 'geojson' || fileType === 'kml'){
                        if(setDragDropTarget()){
                            const infoArr = [];
                            infoArr['id'] = mapSettings.dragDropTarget;
                            infoArr['type'] = 'userLayer';
                            infoArr['fileType'] = fileType;
                            infoArr['layerName'] = filename[0];
                            infoArr['layerDescription'] = 'This layer is from a file that was added to the map.';
                            infoArr['fillColor'] = mapSettings.dragDropFillColor;
                            infoArr['borderColor'] = mapSettings.dragDropBorderColor;
                            infoArr['borderWidth'] = mapSettings.dragDropBorderWidth;
                            infoArr['pointRadius'] = mapSettings.dragDropPointRadius;
                            infoArr['opacity'] = mapSettings.dragDropOpacity;
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
                            processAddedLayer(infoArr,true);
                            map.getView().fit(layersObj[sourceIndex].getExtent());
                        }
                    }
                    else if(fileType === 'zip'){
                        if(setDragDropTarget()){
                            getArrayBuffer(event.file).then((data) => {
                                shp(data).then((geojson) => {
                                    const infoArr = [];
                                    infoArr['id'] = mapSettings.dragDropTarget;
                                    infoArr['type'] = 'userLayer';
                                    infoArr['fileType'] = 'zip';
                                    infoArr['layerName'] = filename[0];
                                    infoArr['layerDescription'] = 'This layer is from a file that was added to the map.';
                                    infoArr['fillColor'] = mapSettings.dragDropFillColor;
                                    infoArr['borderColor'] = mapSettings.dragDropBorderColor;
                                    infoArr['borderWidth'] = mapSettings.dragDropBorderWidth;
                                    infoArr['pointRadius'] = mapSettings.dragDropPointRadius;
                                    infoArr['opacity'] = mapSettings.dragDropOpacity;
                                    const sourceIndex = mapSettings.dragDropTarget + 'Source';
                                    const format = new ol.format.GeoJSON();
                                    const features = format.readFeatures(geojson, {
                                        featureProjection: 'EPSG:3857'
                                    });
                                    layersObj[sourceIndex] = new ol.source.Vector({
                                        features: features
                                    });
                                    layersObj[mapSettings.dragDropTarget].setSource(layersObj[sourceIndex]);
                                    processAddedLayer(infoArr,true);
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
                                        const infoArr = [];
                                        infoArr['id'] = mapSettings.dragDropTarget;
                                        infoArr['type'] = 'userLayer';
                                        infoArr['fileType'] = 'tif';
                                        infoArr['layerName'] = filename[0];
                                        infoArr['layerDescription'] = 'This layer is from a file that was added to the map.';
                                        infoArr['colorScale'] = mapSettings.dragDropRasterColorScale;
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
                                        processAddedLayer(infoArr,true);
                                        rasterLayersArr.value.push({value: mapSettings.dragDropTarget, label: filename[0]});
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
            pointInteraction.value.on('select', (event) => {
                let clusterCnt, newfeature, cFeatures;
                const newfeatures = event.selected;
                const zoomLevel = map.getView().getZoom();
                if(newfeatures.length > 0){
                    if(zoomLevel < 17){
                        const extent = ol.extent.createEmpty();
                        if(newfeatures.length > 1){
                            newfeatures.forEach((nfeature) => {
                                pointInteraction.value.getFeatures().remove(nfeature);
                                if(nfeature.get('features')){
                                    cFeatures = nfeature.get('features');
                                    cFeatures.forEach((cFeature) => {
                                        ol.extent.extend(extent, cFeature.getGeometry().getExtent());
                                    });
                                }
                                else{
                                    ol.extent.extend(extent, nfeature.getGeometry().getExtent());
                                }
                            });
                            map.getView().fit(extent, map.getSize());
                        }
                        else {
                            newfeature = newfeatures[0];
                            pointInteraction.value.getFeatures().remove(newfeature);
                            if(newfeature.get('features')){
                                clusterCnt = newfeature.get('features').length;
                                if(clusterCnt > 1){
                                    cFeatures = newfeature.get('features');
                                    cFeatures.forEach((cFeature) => {
                                        ol.extent.extend(extent, cFeature.getGeometry().getExtent());
                                    });
                                    map.getView().fit(extent, map.getSize());
                                }
                                else{
                                    processPointSelection(newfeature);
                                }
                            }
                            else{
                                processPointSelection(newfeature);
                            }
                        }
                    }
                    else{
                        if(newfeatures.length > 1 && spiderFeature.length === 0){
                            pointInteraction.value.getFeatures().clear();
                            if(!spiderCluster){
                                spiderifyPoints(newfeatures);
                            }
                        }
                        else{
                            if(spiderFeature.length === 1){
                                newfeature = spiderFeature[0];
                                spiderFeature.length = 0;
                            }
                            else{
                                newfeature = newfeatures[0];
                            }
                            pointInteraction.value.getFeatures().clear();
                            if(newfeature.get('features')){
                                clusterCnt = newfeatures[0].get('features').length;
                                if(clusterCnt > 1 && !spiderCluster){
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
            mapSettings.selectedFeatures = selectInteraction.value.getFeatures();
            mapSettings.selectedPointFeatures = pointInteraction.value.getFeatures();
            mapSettings.selectedFeatures.on('add', () => {
                processVectorInteraction();
            });
            mapSettings.selectedFeatures.on('remove', () => {
                processVectorInteraction();
            });
            mapSettings.selectedPointFeatures.on('add', () => {
                processVectorInteraction();
            });
            mapSettings.selectedPointFeatures.on('remove', () => {
                processVectorInteraction();
            });
            mapSettings.selectSource.on('change', () => {
                if(!mapSettings.draw){
                    const featureCnt = mapSettings.selectSource.getFeatures().length;
                    if(featureCnt > 0){
                        if(!mapSettings.shapeActive){
                            const infoArr = [];
                            infoArr['id'] = 'select';
                            infoArr['type'] = 'userLayer';
                            infoArr['fileType'] = 'vector';
                            infoArr['layerName'] = 'Shapes';
                            infoArr['layerDescription'] = 'This layer contains all of the features created through using the Draw Tool, and those that have been selected from other layers added to the map.';
                            infoArr['fillColor'] = mapSettings.shapesFillColor;
                            infoArr['borderColor'] = mapSettings.shapesBorderColor;
                            infoArr['borderWidth'] = mapSettings.shapesBorderWidth;
                            infoArr['pointRadius'] = mapSettings.shapesPointRadius;
                            infoArr['opacity'] = mapSettings.shapesOpacity;
                            processAddedLayer(infoArr,false);
                            updateMapSettings('shapeActive', true);
                        }
                    }
                    else{
                        if(mapSettings.shapeActive){
                            removeLayerFromActiveLayerOptions('select');
                            updateMapSettings('shapeActive', false);
                        }
                    }
                }
            });
            transformInteraction.value.on (['rotatestart','translatestart','scalestart'], (evt) => {
                transformInteractionStartAngle.value = evt.feature.get('angle')||0;
                transformInteractionStartRadius.value = evt.feature.get('radius')||10;
                transformInteractionD.value = [0, 0];
            });
            transformInteraction.value.on('rotating', (evt) => {
                evt.feature.set('angle', transformInteractionStartAngle.value - evt.angle);
            });
            transformInteraction.value.on('translating', (evt) => {
                transformInteractionD.value[0] += evt.delta[0];
                transformInteractionD.value[1] += evt.delta[1];
            });
            transformInteraction.value.on('scaling', (evt) => {
                if(evt.features.getLength() === 1) {
                    const feature = evt.features.item(0);
                    feature.set('radius', transformInteractionStartRadius.value * Math.abs(evt.scale[0]));
                }
            });
        }

        function setMapOverlays() {
            popupOverlay = new ol.Overlay({
                element: document.getElementById('popup'),
                autoPan: true,
                autoPanAnimation: {
                    duration: 250
                }
            });
        }

        function setPointInteraction() {
            return new ol.interaction.Select({
                layers: [layersObj['pointv'], layersObj['spider']],
                condition: (evt) => {
                    if(evt.type === 'click' && mapSettings.activeLayer === 'pointv'){
                        if(!evt.originalEvent.altKey){
                            if(spiderCluster){
                                const spiderclick = map.forEachFeatureAtPixel(evt.pixel, (feature, layer) => {
                                    spiderFeature.push(feature);
                                    if(feature && layer === layersObj['spider']){
                                        return feature;
                                    }
                                });
                                if(!spiderclick){
                                    const blankSource = new ol.source.Vector({
                                        features: new ol.Collection(),
                                        useSpatialIndex: true
                                    });
                                    layersObj['spider'].setSource(blankSource);
                                    hiddenClusters.value.forEach((cluster) => {
                                        showFeature(cluster);
                                    });
                                    hiddenClusters.value = [];
                                    spiderCluster = false;
                                    spiderFeature.length = 0;
                                    layersObj['pointv'].getSource().changed();
                                }
                            }
                            return true;
                        }
                        else if(evt.originalEvent.altKey){
                            map.forEachFeatureAtPixel(evt.pixel, (feature, layer) => {
                                if(feature){
                                    if(spiderCluster && layer === layersObj['spider']){
                                        clickedFeatures.push(feature);
                                        return feature;
                                    }
                                    else if(layer === layersObj['pointv']){
                                        clickedFeatures.push(feature);
                                        return feature;
                                    }
                                }
                            });
                            return false;
                        }
                    }
                    else{
                        return false;
                    }
                },
                toggleCondition: ol.events.condition.click,
                multi: true,
                hitTolerance: 2,
                style: getPointStyle
            });
        }

        function setQueryPopupDisplay(val) {
            displayQueryPopup.value = val;
        }

        function setRasterAnalysisInteraction() {
            return new ol.interaction.Select({
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
        }

        function setRasterAnalysisTranslate() {
            return new ol.interaction.Translate({
                features: rasterAnalysisInteraction.value.getFeatures(),
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

        function setSelectInteraction() {
            return new ol.interaction.Select({
                layers: [layersObj['select']],
                condition: (evt) => {
                    return (evt.type === 'click' && mapSettings.activeLayer === 'select' && !evt.originalEvent.altKey && !evt.originalEvent.shiftKey);
                },
                style: new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: getRgbaStrFromHexOpacity(mapSettings.shapesSelectionsFillColor, mapSettings.shapesSelectionsOpacity)
                    }),
                    stroke: new ol.style.Stroke({
                        color: getRgbaStrFromHexOpacity(mapSettings.shapesSelectionsBorderColor, 1),
                        width: mapSettings.shapesSelectionsBorderWidth
                    }),
                    image: new ol.style.Circle({
                        radius: mapSettings.shapesPointRadius,
                        stroke: new ol.style.Stroke({
                            color: getRgbaStrFromHexOpacity(mapSettings.shapesSelectionsBorderColor, 1),
                            width: (mapSettings.shapesBorderWidth + 2)
                        }),
                        fill: new ol.style.Fill({
                            color: getRgbaStrFromHexOpacity(mapSettings.shapesSelectionsBorderColor, 1)
                        })
                    })
                }),
                toggleCondition: ol.events.condition.click
            });
        }

        function setSymbol(feature) {
            let stroke, style;
            let selected = false;
            const cKey = feature.get(mapSettings.mapSymbology);
            const selections = searchStore.getSelectionsIds;
            if(selections.length > 0){
                const occid = Number(feature.get('id'));
                if(selections.indexOf(occid) !== -1) {
                    selected = true;
                }
            }
            const symbologyData = symbologyArr[mapSettings.mapSymbology].find(key => key['value'] === cKey);
            const color = symbologyData['color'];
            if(selected) {
                stroke = new ol.style.Stroke({color: mapSettings.pointLayerSelectionsBorderColor, width: Number(mapSettings.pointLayerSelectionsBorderWidth)});
            }
            else {
                stroke = new ol.style.Stroke({color: mapSettings.pointLayerBorderColor, width: Number(mapSettings.pointLayerBorderWidth)});
            }
            const fill = new ol.style.Fill({color: color});
            if(feature.get('basisofrecord') && feature.get('basisofrecord').toLowerCase().indexOf('observation') !== -1){
                style = new ol.style.Style({
                    image: new ol.style.RegularShape({
                        fill: fill,
                        stroke: stroke,
                        points: 3,
                        radius: mapSettings.pointLayerPointRadius
                    })
                });
            }
            else{
                style = new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: mapSettings.pointLayerPointRadius,
                        fill: fill,
                        stroke: stroke
                    })
                });
            }
            return style;
        }

        function setTransformHandleStyle() {
            if(transformInteraction.value instanceof ol.interaction.Transform){
                const circle = new ol.style.RegularShape({
                    fill: new ol.style.Fill({color:[255,255,255,0.01]}),
                    stroke: new ol.style.Stroke({width:1, color:[0,0,0,0.01]}),
                    radius: 8,
                    points: 10
                });
                transformInteraction.value.setStyle ('rotate', new ol.style.Style({
                    text: new ol.style.Text ({
                        text:'\uf0e2',
                        font:"16px Fontawesome",
                        textAlign: "left",
                        fill:new ol.style.Fill({color:'red'})
                    }),
                    image: circle
                }));
                transformInteraction.value.setStyle ('rotate0', new ol.style.Style({
                    text: new ol.style.Text ({
                        text:'\uf0e2',
                        font:"20px Fontawesome",
                        fill: new ol.style.Fill({ color:'red' }),
                        stroke: new ol.style.Stroke({ width:1, color:'red' })
                    }),
                }));
                transformInteraction.value.setStyle('translate', new ol.style.Style({
                    text: new ol.style.Text ({
                        text:'\uf047',
                        font:"20px Fontawesome",
                        fill: new ol.style.Fill({ color:'red' }),
                        stroke: new ol.style.Stroke({ width:1, color:'red' })
                    })
                }));
                transformInteraction.value.setStyle ('scaleh1', new ol.style.Style({
                    text: new ol.style.Text ({
                        text:'\uf07d',
                        font:"20px Fontawesome",
                        fill: new ol.style.Fill({ color:'red' }),
                        stroke: new ol.style.Stroke({ width:1, color:'red' })
                    })
                }));
                transformInteraction.value.style.scaleh3 = transformInteraction.value.style.scaleh1;
                transformInteraction.value.setStyle('scalev', new ol.style.Style({
                    text: new ol.style.Text ({
                        text:'\uf07e',
                        font:"20px Fontawesome",
                        fill: new ol.style.Fill({ color:'red' }),
                        stroke: new ol.style.Stroke({ width:1, color:'red' })
                    })
                }));
                transformInteraction.value.style.scalev2 = transformInteraction.value.style.scalev;
                transformInteraction.value.set('translate', transformInteraction.value.get('translate'));
            }
        }

        function setTransformInteraction() {
            return new ol.interaction.Transform ({
                enableRotatedTransform: false,
                condition: (evt) => {
                    return (mapSettings.activeLayer === 'select' && evt.originalEvent.shiftKey);
                },
                addCondition: ol.events.condition.shiftKeyOnly,
                layers: [layersObj['select']],
                hitTolerance: 2,
                translateFeature: false,
                scale: true,
                rotate: !(props.inputWindowMode && props.inputWindowToolsArr.includes('box')),
                keepAspectRatio: false,
                keepRectangle: false,
                translate: true,
                stretch: true,
                pointRadius: (f) => {
                    const radius = f.get('radius') || 10;
                    return [radius, radius];
                }
            });
        }

        function showFeature(feature) {
            let featureStyle;
            if(feature.get('features')){
                featureStyle = setClusterSymbol(feature);
            }
            else{
                featureStyle = setSymbol(feature);
            }
            feature.setStyle(featureStyle);
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

        function sortSymbologyArr() {
            for(let key in symbologyArr) {
                if(key !== 'taxonomy' && key !== 'sciname' && symbologyArr.hasOwnProperty(key)){
                    symbologyArr[key].sort((a, b) => {
                        return a.value.localeCompare(b.value);
                    });
                }
            }
            if(symbologyArr.hasOwnProperty('taxonomy')){
                symbologyArr['taxonomy'].sort((a, b) => {
                    return a.value.localeCompare(b.value);
                });
                symbologyArr['taxonomy'].forEach((taxon) => {
                    if(taxon.hasOwnProperty('taxa') && taxon['taxa'].length > 0){
                        taxon['taxa'].sort((a, b) => {
                            return a.value.localeCompare(b.value);
                        });
                    }
                });
            }
        }

        function spiderifyPoints(features) {
            let style, cf, p, a, max;
            spiderCluster = true;
            spiderFeature.length = 0;
            features.forEach((feature) => {
                hideFeature(feature);
                hiddenClusters.value.push(feature);
                if(feature.get('features')){
                    const addFeatures = feature.get('features');
                    addFeatures.forEach((aFeature) => {
                        spiderFeature.push(aFeature.clone());
                    });
                }
                else{
                    spiderFeature.push(feature.clone());
                }
            });
            const source = layersObj['spider'].getSource();
            source.clear();
            const center = features[0].getGeometry().getCoordinates();
            const pix = map.getView().getResolution();
            let r = pix * 12 * (0.5 + spiderFeature.length / 4);
            if(spiderFeature.length <= 10){
                max = Math.min(spiderFeature.length, 10);
                spiderFeature.forEach((feature, index) => {
                    a = 2 * Math.PI * index / max;
                    if(max === 2 || max === 4) {
                        a += Math.PI / 4;
                    }
                    p = [(center[0] + r * Math.sin(a)), (center[1] + r * Math.cos(a))];
                    cf = new ol.Feature({
                        'features': [feature],
                        geometry: new ol.geom.Point(p)
                    });
                    style = setClusterSymbol(cf);
                    cf.setStyle(style);
                    source.addFeature(cf);
                });
            }
            else{
                a = 0;
                let radius;
                const d = 30;
                spiderFeature.forEach((feature) => {
                    radius = d / 2 + d * a / (2 * Math.PI);
                    a = a + (d + 0.1) / radius;
                    const dx = pix * radius * Math.sin(a);
                    const dy = pix * radius * Math.cos(a);
                    p = [center[0] + dx, center[1] + dy];
                    cf = new ol.Feature({
                        'features': [feature],
                        geometry: new ol.geom.Point(p)
                    });
                    style = setClusterSymbol(cf);
                    cf.setStyle(style);
                    source.addFeature(cf);
                });
            }
        }

        function updateMapSettings(prop, value) {
            mapSettings[prop] = value;
        }

        function updatePointStyle(id) {
            let point, style;
            if(spiderCluster){
                const spiderPoints = layersObj['spider'].getSource().getFeatures();
                spiderPoints.forEach((feature) => {
                    if(Number(feature.get('features')[0].get('id')) === Number(id)){
                        style = setClusterSymbol(feature);
                        feature.setStyle(style);
                        layersObj['spider'].getSource().changed();
                    }
                });
            }
            if(!style){
                if(mapSettings.clusterPoints){
                    const cluster = findRecordCluster(Number(id));
                    point = findRecordPointInCluster(cluster, Number(id));
                }
                else{
                    point = findRecordPoint(Number(id));
                }
                style = setSymbol(point);
                point.setStyle(style);
            }
        }

        function zoomToSelections() {
            const selections = searchStore.getSelectionsIds;
            const extent = ol.extent.createEmpty();
            selections.forEach((id) => {
                let point;
                if(mapSettings.clusterPoints){
                    const cluster = findRecordCluster(id);
                    point = findRecordPointInCluster(cluster, id);
                }
                else{
                    point = findRecordPoint(id);
                }
                if(point){
                    ol.extent.extend(extent, point.getGeometry().getExtent());
                }
            });
            map.getView().fit(extent, map.getSize());
        }

        function zoomToShapesLayer() {
            const featureCnt = mapSettings.selectSource.getFeatures().length;
            if(featureCnt > 0){
                map.getView().fit(mapSettings.selectSource.getExtent(), map.getSize());
            }
        }

        Vue.provide('activeLayerSelectorOptions', activeLayerSelectorOptions);
        Vue.provide('addLayerToActiveLayerOptions', addLayerToActiveLayerOptions);
        Vue.provide('addLayerToLayersObj', addLayerToLayersObj);
        Vue.provide('addLayerToRasterLayersArr', addLayerToRasterLayersArr);
        Vue.provide('autoColorSymbologyKeys', autoColorSymbologyKeys);
        Vue.provide('changeDraw', changeDraw);
        Vue.provide('changeMapSymbology', changeMapSymbology);
        Vue.provide('clearSelections', clearSelections);
        Vue.provide('coreLayers', coreLayers);
        Vue.provide('findRecordCluster', findRecordCluster);
        Vue.provide('findRecordClusterPosition', findRecordClusterPosition);
        Vue.provide('findRecordPoint', findRecordPoint);
        Vue.provide('findRecordPointInCluster', findRecordPointInCluster);
        Vue.provide('getArrayBuffer', getArrayBuffer);
        Vue.provide('getVectorLayerStyle', getVectorLayerStyle);
        Vue.provide('inputResponseData', inputResponseData);
        Vue.provide('inputWindowMode', propsRefs.inputWindowMode);
        Vue.provide('inputWindowToolsArr', props.inputWindowToolsArr);
        Vue.provide('layerOrderArr', layerOrderArr);
        Vue.provide('layersConfigArr', layersConfigArr);
        Vue.provide('layersInfoObj', layersInfoObj);
        Vue.provide('layersObj', layersObj);
        Vue.provide('loadPointsLayer', loadPointsLayer);
        Vue.provide('map', Vue.computed(() => map));
        Vue.provide('mapSettings', mapSettings);
        Vue.provide('openRecordInfoWindow', openRecordInfoWindow);
        Vue.provide('processInputSelections', processInputSelections);
        Vue.provide('processInputSubmit', processInputSubmit);
        Vue.provide('processInputPointUncertaintyChange', processInputPointUncertaintyChange);
        Vue.provide('processSymbologyKeyColorChange', processSymbologyKeyColorChange);
        Vue.provide('processToggleSelectedChange', processToggleSelectedChange);
        Vue.provide('rasterAnalysisInteraction', rasterAnalysisInteraction);
        Vue.provide('rasterLayersArr', rasterLayersArr);
        Vue.provide('removeLayerFromActiveLayerOptions', removeLayerFromActiveLayerOptions);
        Vue.provide('removeLayerFromLayersObj', removeLayerFromLayersObj);
        Vue.provide('removeLayerFromRasterLayersArr', removeLayerFromRasterLayersArr);
        Vue.provide('removeUserLayer', removeUserLayer);
        Vue.provide('resetSymbology', resetSymbology);
        Vue.provide('selectInteraction', selectInteraction);
        Vue.provide('setLayersOrder', setLayersOrder);
        Vue.provide('setQueryPopupDisplay', setQueryPopupDisplay);
        Vue.provide('showPopup', showPopup);
        Vue.provide('symbologyArr', symbologyArr);
        Vue.provide('updateMapSettings', updateMapSettings);
        Vue.provide('updatePointStyle', updatePointStyle);
        Vue.provide('windowWidth', windowWidth);
        Vue.provide('zoomToSelections', zoomToSelections);

        Vue.onMounted(() => {
            spatialModuleInitialising.value = true;
            setMapLayersInteractions();
            setMapOverlays();
            setMap();
            addMapControlsInteractions();
            setLayersController();
            if(!propsRefs.inputWindowMode.value){
                searchStore.initializeSearchStorage(props.queryId);
            }
            getCoords();
            setTransformHandleStyle();
            if(!props.clusterPoints){
                updateMapSettings('clusterPoints', false);
            }
            updateMapSettings('drawToolFreehandMode', getPlatformProperty('has.touch'));
            changeDraw();
            controlPanelRef.value.changeBaseMap();
            window.addEventListener('resize', handleWindowResize);
            handleWindowResize();
        });

        return {
            controlPanelRef,
            displayQueryPopup,
            layersInfoObj,
            mapSettings,
            popupCloser,
            popupContent,
            transformInteraction,
            clearSelectedFeatures,
            closePopup,
            closeRecordInfoWindow,
            createCircleFromPointRadius,
            createCirclesFromCircleArr,
            createPointFromPointParams,
            createPolygonFromBoundingBox,
            createPolysFromFootprintWKT,
            createPolysFromPolyArr,
            createUncertaintyCircleFromPointRadius,
            emitClosePopup,
            loadRecords,
            processResetCriteria,
            setQueryPopupDisplay,
            updateMapSettings,
            zoomToShapesLayer
        }
    }
};
