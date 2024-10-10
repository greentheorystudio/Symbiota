const spatialPointVectorToolsTab = {
    template: `
        <div class="q-pa-sm column">
            <div class="q-mb-sm">
                <q-select bg-color="white" outlined v-model="selectedScope" :options="processScopeOptions" :option-value="value" :option-label="label" label="Create Polygon For:" popup-content-class="z-max" behavior="menu" @update:model-value="processScopeChange" dense options-dense />
            </div>
            <q-separator ></q-separator>
            <div class="q-my-sm column">
                <div>
                    <span class="text-bold">Concave Hull Polygon:</span> Creates a concave hull polygon or multipolygon for the occurrence 
                    points on the map based on the entered maximum edge length. Use the dropdown above to select whether the polygon should 
                    be created for all of the occurrence points on the map or only those selected.
                    <template v-if="searchRecordCnt === 0">
                        <span class="text-red"> Occurrence points need to be loaded on the map to use this tool.</span>
                    </template> 
                </div>
                <div class="q-mb-xs">
                    <div>
                        <q-input type="number" outlined v-model="maximumEdgeLengthValue" min="0" class="col-3" label="Maximum Edge Length (km)" @update:model-value="processMaximumEdgeLengthChange" dense />
                    </div>
                </div>
                <div class="row justify-end">
                    <div class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="createConcavePoly();" label="Create Concave Hull Polygon" :disabled="searchRecordCnt === 0" dense />
                    </div>
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-my-sm column">
                <div>
                    <span class="text-bold">Convex Hull Polygon:</span> Creates a convex hull polygon for the occurrence points on the 
                    map. Use the dropdown above to select whether the polygon should be created for all of the occurrence points on 
                    the map or only those selected.
                    <template v-if="searchRecordCnt === 0">
                        <span class="text-red"> Occurrence points need to be loaded on the map to use this tool.</span>
                    </template> 
                </div>
                <div class="row justify-end">
                    <div class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="createConvexPoly();" label="Create Convex Hull Polygon" :disabled="searchRecordCnt === 0" dense />
                    </div>
                </div>
            </div>
            <q-separator ></q-separator>
        </div>
    `,
    setup() {
        const layersObj = Vue.inject('layersObj');
        const mapProjection = new ol.proj.Projection({
            code: 'EPSG:3857'
        });
        const mapSettings = Vue.inject('mapSettings');
        const maximumEdgeLengthValue = Vue.ref(null);
        const processScopeOptions = [
            {value: 'all', label: 'All Points'},
            {value: 'selected', label: 'Selected Points'}
        ];

        const searchStore = useSearchStore();

        const searchRecordCnt = Vue.computed(() => searchStore.getSearchRecCnt);
        const selectedScope = Vue.ref({value: 'all', label: 'All Points'});
        const selections = searchStore.getSelections;
        const wgs84Projection = new ol.proj.Projection({
            code: 'EPSG:4326',
            units: 'degrees'
        });

        const findRecordCluster = Vue.inject('findRecordCluster');
        const findRecordPoint = Vue.inject('findRecordPoint');
        const findRecordPointInCluster = Vue.inject('findRecordPointInCluster');
        const { showNotification } = useCore();

        function createConcavePoly() {
            let features = [];
            const geoJSONFormat = new ol.format.GeoJSON();
            if(maximumEdgeLengthValue.value > 0){
                if(selectedScope.value.value === 'all'){
                    features = getTurfPointFeaturesetAll();
                }
                else if(selectedScope.value.value === 'selected'){
                    if(selections.length >= 3){
                        features = getTurfPointFeaturesetSelected();
                    }
                    else{
                        selectedScope.value = {value: 'all', label: 'All Points'};
                        showNotification('negative','There must be at least 3 selected points on the map. Please either select more points or re-run this tool for all points.');
                        return;
                    }
                }
                if(features){
                    let concavepoly = '';
                    try{
                        const options = {units: 'kilometers', maxEdge: Number(maximumEdgeLengthValue.value)};
                        concavepoly = turf.concave(features,options);
                    }
                    catch(e){
                        showNotification('negative','Concave polygon could not be calculated. Perhaps try using a larger value for the maximum edge length.');
                    }
                    if(concavepoly){
                        const cnvepoly = geoJSONFormat.readFeature(concavepoly);
                        cnvepoly.getGeometry().transform(wgs84Projection, mapProjection);
                        mapSettings.selectSource.addFeature(cnvepoly);
                    }
                }
                else{
                    showNotification('negative','There must be at least 3 points on the map to calculate polygon.');
                }
            }
            else{
                showNotification('negative','Please enter a number for the Maximum Edge Length.');
            }
        }

        function createConvexPoly() {
            let features = [];
            const geoJSONFormat = new ol.format.GeoJSON();
            if(selectedScope.value.value === 'all'){
                features = getTurfPointFeaturesetAll();
            }
            else if(selectedScope.value.value === 'selected'){
                if(selections.length >= 3){
                    features = getTurfPointFeaturesetSelected();
                }
                else{
                    selectedScope.value = {value: 'all', label: 'All Points'};
                    showNotification('negative','There must be at least 3 selected points on the map. Please either select more points or re-run this tool for all points.');
                    return;
                }
            }
            if(features){
                const convexpoly = turf.convex(features);
                if(convexpoly){
                    const cnvxpoly = geoJSONFormat.readFeature(convexpoly);
                    cnvxpoly.getGeometry().transform(wgs84Projection, mapProjection);
                    mapSettings.selectSource.addFeature(cnvxpoly);
                }
            }
            else{
                showNotification('negative','There must be at least 3 points on the map to calculate polygon.');
            }
        }

        function getTurfPointFeaturesetAll() {
            let pntCoords;
            let geojsonStr;
            let fixedselectgeometry;
            let selectiongeometry;
            let selectedClone;
            const turfFeatureArr = [];
            const geoJSONFormat = new ol.format.GeoJSON();
            if(mapSettings.clusterPoints){
                const clusters = layersObj['pointv'].getSource().getFeatures();
                clusters.forEach((cluster) => {
                    const cFeatures = cluster.get('features');
                    cFeatures.forEach((feat) => {
                        selectedClone = feat.clone();
                        selectiongeometry = selectedClone.getGeometry();
                        fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                        geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                        pntCoords = JSON.parse(geojsonStr).coordinates;
                        turfFeatureArr.push(turf.point(pntCoords));
                    });
                });
            }
            else{
                const features = layersObj['pointv'].getSource().getFeatures();
                features.forEach((feat) => {
                    selectedClone = feat.clone();
                    selectiongeometry = selectedClone.getGeometry();
                    fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                    geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                    pntCoords = JSON.parse(geojsonStr).coordinates;
                    turfFeatureArr.push(turf.point(pntCoords));
                });
            }
            return turfFeatureArr.length >= 3 ? turf.featureCollection(turfFeatureArr) : false;
        }

        function getTurfPointFeaturesetSelected() {
            const turfFeatureArr = [];
            const geoJSONFormat = new ol.format.GeoJSON();
            selections.forEach((occid) => {
                let point = '';
                if(mapSettings.clusterPoints){
                    const cluster = findRecordCluster(occid);
                    point = findRecordPointInCluster(cluster,occid);
                }
                else{
                    point = findRecordPoint(occid);
                }
                if(point){
                    const selectedClone = point.clone();
                    const selectiongeometry = selectedClone.getGeometry();
                    const fixedselectgeometry = selectiongeometry.transform(mapProjection, wgs84Projection);
                    const geojsonStr = geoJSONFormat.writeGeometry(fixedselectgeometry);
                    const pntCoords = JSON.parse(geojsonStr).coordinates;
                    turfFeatureArr.push(turf.point(pntCoords));
                }
            });
            return turfFeatureArr.length >= 3 ? turf.featureCollection(turfFeatureArr) : false;
        }

        function processMaximumEdgeLengthChange(val) {
            if(Number(val) < 0){
                maximumEdgeLengthValue.value = 0;
                showNotification('negative','Maximum Edge Length must be greater than zero.');
            }
        }

        function processScopeChange() {
            if(!(selections.length >= 3)){
                selectedScope.value = {value: 'all', label: 'All Points'};
                showNotification('negative','There must be at least 3 selected points on the map.');
            }
        }
        
        return {
            maximumEdgeLengthValue,
            processScopeOptions,
            searchRecordCnt,
            selectedScope,
            createConcavePoly,
            createConvexPoly,
            processMaximumEdgeLengthChange,
            processScopeChange
        }
    }
};
