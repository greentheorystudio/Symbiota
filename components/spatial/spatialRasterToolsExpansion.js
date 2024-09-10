const spatialRasterToolsExpansion = {
    props: {
        selectedTargetRaster: {
            type: String,
            default: ''
        }
    },
    template: `
        <div class="q-pa-sm column">
            <div class="q-mb-sm">
                <q-select bg-color="white" outlined v-model="selectedTarget" :options="rasterLayersArr" :option-value="value" :option-label="label" label="Target Raster Layer" popup-content-class="z-max" @update:model-value="changeTargetRaster" behavior="menu" dense options-dense />
            </div>
            <q-separator ></q-separator>
            <div class="q-my-sm column">
                <div>
                    <span class="text-bold">Data-Based Vectorize:</span> Process creates vector features for regions within the selected 
                    Target Raster Layer with a value equal to or between the entered Low and High Values based on a data analysis of the 
                    selected Target Raster Layer with a resolution relative to that layer. 
                    <template v-if="rasterLayersArr.length < 2 || mapSettings.polyCount !== 1">
                        <span class="text-red"> At least one raster layer needs to be loaded on the map and one feature in the Shapes layer 
                        needs to be selected to use this tool.</span>
                    </template>
                </div>
                <div class="q-mt-xs row justify-between q-gutter-sm">
                    <div>
                        <q-input type="number" outlined v-model="dataVectorizeLowValue" class="col-3" label="Low Value" dense />
                    </div>
                    <div>
                        <q-input type="number" outlined v-model="dataVectorizeHighValue" class="col-3" label="High Value" dense />
                    </div>
                </div>
                <div class="q-mt-xs row justify-end">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="vectorizeRasterByData();" label="Start Process" :disabled="selectedTargetRaster === 'none' || mapSettings.polyCount !== 1" dense />
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-my-sm">
                <div>
                    <span class="text-bold">Grid-Based Vectorize:</span> Process creates vector features within the bounds of the Target Polygon 
                    for regions within the selected Target Raster Layer with a value equal to or between the entered Low and High Values based 
                    on a grid analysis with of the selected resolution.
                    <template v-if="rasterLayersArr.length < 2 || !targetBoxDisplayed">
                        <span class="text-red"> At least one raster layer needs to be loaded on the map and the Target Polygon 
                        needs to be displayed to use this tool.</span>
                    </template>
                </div>
                <div class="q-mt-xs row justify-between q-gutter-sm">
                    <div>
                        <q-input type="number" outlined v-model="gridVectorizeLowValue" label="Low Value" dense />
                    </div>
                    <div>
                        <q-input type="number" outlined v-model="gridVectorizeHighValue" label="High Value" dense />
                    </div>
                </div>
                <div class="q-mt-xs row justify-center">
                    <div class="col-6">
                        <q-select bg-color="white" outlined v-model="selectedResolution" :options="gridResolutionOptions" :option-value="value" :option-label="label" label="Grid Analysis Resolution (m)" popup-content-class="z-max" @update:model-value="processVectorizeRasterByGridResolutionChange" behavior="menu" dense options-dense />
                    </div>
                </div>
                <div class="q-mt-xs row justify-between q-gutter-sm">
                    <div>
                        <template v-if="!targetBoxDisplayed">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayVectorizeRasterByGridTargetPolygon();" label="Display Target Box" :disabled="selectedTargetRaster === 'none'" dense />
                        </template>
                        <template v-else>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="hideVectorizeRasterByGridTargetPolygon();" label="Hide Target Box" dense />
                        </template>
                    </div>
                    <div>
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="vectorizeRasterByGrid();" label="Start Process" :disabled="selectedTargetRaster === 'none' || !targetBoxDisplayed" dense />
                    </div>
                </div>
            </div>
            <q-separator ></q-separator>
        </div>
    `,
    setup(props) {
        const dataVectorizeHighValue = Vue.ref(null);
        const dataVectorizeLowValue = Vue.ref(null);
        const gridResolutionOptions = [
            {value: '0.025', label: '25'},
            {value: '0.05', label: '50'},
            {value: '0.1', label: '100'},
            {value: '0.25', label: '250'},
            {value: '0.5', label: '500'}
        ];
        const gridVectorizeHighValue = Vue.ref(null);
        const gridVectorizeLowValue = Vue.ref(null);
        const layersObj = Vue.inject('layersObj');
        const map = Vue.inject('map');
        const mapProjection = new ol.proj.Projection({
            code: 'EPSG:3857'
        });
        const mapSettings = Vue.inject('mapSettings');
        const rasterAnalysisInteraction = Vue.inject('rasterAnalysisInteraction');
        const rasterLayersArr = Vue.inject('rasterLayersArr');
        const selectedResolution = Vue.ref({value: '0.025', label: '25'});
        const selectedTarget = Vue.ref(null);
        const selectInteraction = Vue.inject('selectInteraction');
        const targetBoxDisplayed = Vue.ref(false);
        const wgs84Projection = new ol.proj.Projection({
            code: 'EPSG:4326',
            units: 'degrees'
        });

        const propsRefs = Vue.toRefs(props);
        const updateMapSettings = Vue.inject('updateMapSettings');
        const { hideWorking, showNotification, showWorking } = useCore();

        Vue.watch(propsRefs.selectedTargetRaster, () => {
            setSelectedOption();
        });

        function changeTargetRaster(val) {
            updateMapSettings('selectedTargetRaster', val.value);
        }

        function displayVectorizeRasterByGridTargetPolygon() {
            mapSettings.rasterAnalysisSource.clear();
            rasterAnalysisInteraction.value.getFeatures().clear();
            let polyOffset = 0;
            const resolutionVal = Number(selectedResolution.value);
            if(resolutionVal === 0.025){
                polyOffset = 10000;
            }
            else if(resolutionVal === 0.05){
                polyOffset = 25000;
            }
            else if(resolutionVal === 0.1){
                polyOffset = 55000;
            }
            else if(resolutionVal === 0.25){
                polyOffset = 145000;
            }
            else if(resolutionVal === 0.5){
                polyOffset = 295000;
            }
            const geoJSONFormat = new ol.format.GeoJSON();
            const mapCenterPoint = map.value.getView().getCenter();
            const highLong = mapCenterPoint[0] + polyOffset;
            const lowLong = mapCenterPoint[0] - polyOffset;
            const highLat = mapCenterPoint[1] + polyOffset;
            const lowLat = mapCenterPoint[1] - polyOffset;
            const line = turf.lineString([[lowLong, lowLat], [lowLong, highLat], [highLong, highLat]]);
            const bbox = turf.bbox(line);
            const bboxPolygon = turf.bboxPolygon(bbox);
            const newPoly = geoJSONFormat.readFeature(bboxPolygon);
            mapSettings.rasterAnalysisSource.addFeature(newPoly);
            rasterAnalysisInteraction.value.getFeatures().push(newPoly);
            targetBoxDisplayed.value = true;
        }

        function getRasterXYFromDataIndex(index,rasterWidth) {
            let xyArr = [];
            if(index < rasterWidth){
                xyArr.push(index);
                xyArr.push(0);
            }
            else{
                let y = Math.trunc(index / rasterWidth);
                let x = ((index - (y * rasterWidth)) - 1);
                xyArr.push(x);
                xyArr.push(y);
            }
            return xyArr;
        }

        function hideVectorizeRasterByGridTargetPolygon() {
            mapSettings.rasterAnalysisSource.clear();
            rasterAnalysisInteraction.value.getFeatures().clear();
            targetBoxDisplayed.value = false;
        }

        function processVectorizeRasterByGridResolutionChange() {
            if(targetBoxDisplayed.value){
                displayVectorizeRasterByGridTargetPolygon();
            }
        }

        function setSelectedOption() {
            selectedTarget.value = rasterLayersArr.find(opt => opt['value'] === props.selectedTargetRaster);
        }

        function vectorizeRasterByData() {
            let selectedClone;
            const turfFeatureArr = [];
            if(props.selectedTargetRaster === 'none'){
                showNotification('negative','Please select a target raster layer.');
            }
            else if(!dataVectorizeLowValue.value || !dataVectorizeHighValue.value){
                showNotification('negative','Please enter high and low numbers for the value range.');
            }
            else{
                showWorking('Loading...');
                setTimeout(() => {
                    selectInteraction.value.getFeatures().forEach((feature) => {
                        selectedClone = feature.clone();
                        const geoType = selectedClone.getGeometry().getType();
                        if(geoType === 'Polygon' || geoType === 'MultiPolygon' || geoType === 'Circle'){
                            const geoJSONFormat = new ol.format.GeoJSON();
                            const selectiongeometry = selectedClone.getGeometry();
                            selectiongeometry.transform(mapProjection, wgs84Projection);
                            const geojsonStr = geoJSONFormat.writeGeometry(selectiongeometry);
                            const featCoords = JSON.parse(geojsonStr).coordinates;
                            const turfPoly = turf.polygon(featCoords);
                            const dataIndex = props.selectedTargetRaster + 'Data';
                            const dataObj = layersObj[dataIndex];
                            const box = [dataObj['bbox'][0],dataObj['bbox'][1] - (dataObj['bbox'][3] - dataObj['bbox'][1]), dataObj['bbox'][2], dataObj['bbox'][1]];
                            dataObj['data'].forEach((item, index) => {
                                if(Number(item) >= Number(dataVectorizeLowValue.value) && Number(item) <= Number(dataVectorizeHighValue.value)){
                                    const xyArr = getRasterXYFromDataIndex(index,dataObj['imageWidth']);
                                    const lat = box[3] - (((box[3] - box[1]) / dataObj['imageHeight']) * xyArr[1]);
                                    const long = box[0] + (((box[2] - box[0]) / dataObj['imageWidth']) * xyArr[0]);
                                    const turfPoint = turf.point([long,lat]);
                                    if(turf.booleanPointInPolygon(turfPoint, turfPoly)){
                                        turfFeatureArr.push(turfPoint);
                                    }
                                }
                            });
                            const turfFeatureCollection = turf.featureCollection(turfFeatureArr);
                            let concavepoly = '';
                            try{
                                const options = {units: 'kilometers', maxEdge: dataObj['resolution']};
                                concavepoly = turf.concave(turfFeatureCollection,options);
                            }
                            catch(e){}
                            if(concavepoly){
                                const cnvepoly = geoJSONFormat.readFeature(concavepoly);
                                cnvepoly.getGeometry().transform(wgs84Projection,mapProjection);
                                mapSettings.selectSource.addFeature(cnvepoly);
                            }
                            hideWorking();
                        }
                    });
                }, 50);
            }
        }

        function vectorizeRasterByGrid() {
            let selectedClone;
            const turfFeatureArr = [];
            if(props.selectedTargetRaster === 'none'){
                showNotification('negative','Please select a target raster layer.');
            }
            else if(!gridVectorizeLowValue.value || !gridVectorizeHighValue.value){
                showNotification('negative','Please enter high and low numbers for the value range.');
            }
            else{
                showWorking('Loading...');
                setTimeout(() => {
                    rasterAnalysisInteraction.value.getFeatures().forEach((feature) => {
                        selectedClone = feature.clone();
                    });
                    if(selectedClone){
                        const geoJSONFormat = new ol.format.GeoJSON();
                        const selectiongeometry = selectedClone.getGeometry();
                        selectiongeometry.transform(mapProjection, wgs84Projection);
                        const geojsonStr = geoJSONFormat.writeGeometry(selectiongeometry);
                        const featCoords = JSON.parse(geojsonStr).coordinates;
                        const extentBBox = turf.bbox(turf.polygon(featCoords));
                        const gridPoints = turf.pointGrid(extentBBox, selectedResolution.value, {units: 'kilometers',mask: turf.polygon(featCoords)});
                        const gridPointFeatures = geoJSONFormat.readFeatures(gridPoints);
                        const dataIndex = props.selectedTargetRaster + 'Data';
                        gridPointFeatures.forEach((feature) => {
                            const coords = feature.getGeometry().getCoordinates();
                            const x = Math.floor(layersObj[dataIndex]['imageWidth'] * (coords[0] - layersObj[dataIndex]['x_min']) / (layersObj[dataIndex]['x_max'] - layersObj[dataIndex]['x_min']));
                            const y = layersObj[dataIndex]['imageHeight'] - Math.ceil(layersObj[dataIndex]['imageHeight'] * (coords[1] - layersObj[dataIndex]['y_max']) / (layersObj[dataIndex]['y_min'] - layersObj[dataIndex]['y_max']));
                            const rasterDataIndex = (Number(layersObj[dataIndex]['imageWidth']) * y) + x;
                            if(coords[0] >= layersObj[dataIndex]['x_min'] && coords[0] <= layersObj[dataIndex]['x_max'] && coords[1] <= layersObj[dataIndex]['y_min'] && coords[1] >= layersObj[dataIndex]['y_max']){
                                if(Number(layersObj[dataIndex]['data'][rasterDataIndex]) >= Number(gridVectorizeLowValue.value) && Number(layersObj[dataIndex]['data'][rasterDataIndex]) <= Number(gridVectorizeHighValue.value)){
                                    turfFeatureArr.push(turf.point(coords));
                                }
                            }
                        });
                        const turfFeatureCollection = turf.featureCollection(turfFeatureArr);
                        let concavepoly = '';
                        try{
                            const maxEdgeVal = Number(selectedResolution.value) + (Number(selectedResolution.value) / 2);
                            const options = {units: 'kilometers', maxEdge: maxEdgeVal};
                            concavepoly = turf.concave(turfFeatureCollection,options);
                        }
                        catch(e){}
                        if(concavepoly){
                            const cnvepoly = geoJSONFormat.readFeature(concavepoly);
                            cnvepoly.getGeometry().transform(wgs84Projection, mapProjection);
                            mapSettings.selectSource.addFeature(cnvepoly);
                        }
                        hideWorking();
                    }
                    else{
                        hideWorking();
                        showNotification('negative','Click the Show Target button and then click and drag the Target to the area you would like to vectorize. Then click the Grid-Based Vectorize button.');
                    }
                }, 50);
            }
        }

        Vue.onMounted(() => {
            setSelectedOption();
        });
        
        return {
            dataVectorizeHighValue,
            dataVectorizeLowValue,
            gridResolutionOptions,
            gridVectorizeHighValue,
            gridVectorizeLowValue,
            mapSettings,
            rasterLayersArr,
            selectedResolution,
            selectedTarget,
            targetBoxDisplayed,
            changeTargetRaster,
            displayVectorizeRasterByGridTargetPolygon,
            hideVectorizeRasterByGridTargetPolygon,
            processVectorizeRasterByGridResolutionChange,
            vectorizeRasterByData,
            vectorizeRasterByGrid
        }
    }
};
