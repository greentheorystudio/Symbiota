const spatialMapSettingsPopup = {
    template: `
        <q-dialog class="z-top" v-model="mapSettings.showMapSettings" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="updateMapSettings('showMapSettings', false);"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    <div>
                        <q-checkbox v-model="mapSettings.drawToolFreehandMode" label="Set Draw Tool to Freehand Mode" @update:model-value="changeFreehandMode" />
                    </div>
                    <div>
                        <q-checkbox v-model="mapSettings.clusterPoints" label="Cluster Points" @update:model-value="changeClusterPoints" />
                    </div>
                    <div class="row col-5">
                        <q-input type="number" outlined v-model="mapSettings.clusterDistance" class="col-6" label="Cluster Distance (px)" min="1" dense @update:model-value="changeClusterDistance" />
                    </div>
                    <div>
                        <q-checkbox v-model="mapSettings.showHeatMap" label="Display Heat Map" @update:model-value="toggleHeatMap" />
                    </div>
                    <div class="row col-5">
                        <q-input type="number" outlined v-model="mapSettings.heatMapRadius" class="col-6" label="Heat Map Radius (px)" min="1" dense @update:model-value="changeHeatMapRadius" />
                    </div>
                    <div class="row col-5">
                        <q-input type="number" outlined v-model="mapSettings.heatMapBlur" class="col-6" label="Heat Map Blur (px)" min="1" dense @update:model-value="changeHeatMapBlur" />
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup() {
        const { getPlatformProperty } = useCore();

        const layersObj = Vue.inject('layersObj');
        const mapSettings = Vue.inject('mapSettings');
        const windowWidth = Vue.inject('windowWidth');

        const loadPointsLayer = Vue.inject('loadPointsLayer');
        const { showNotification } = useCore();
        const updateMapSettings = Vue.inject('updateMapSettings');

        function changeClusterDistance(val) {
            updateMapSettings('clusterDistance', val);
            if(mapSettings.clusterPoints && layersObj['pointv'].getSource().getFeatures().length > 0){
                mapSettings.clusterSource.setDistance(mapSettings.clusterDistance);
            }
        }

        function changeClusterPoints(val) {
            updateMapSettings('clusterPoints', val);
            changeClusterSetting();
        }

        function changeClusterSetting() {
            if(mapSettings.clusterPoints && layersObj['pointv'].getSource().getFeatures().length > 0){
                loadPointsLayer();
            }
            else{
                layersObj['pointv'].setSource(mapSettings.pointVectorSource);
            }
        }

        function changeFreehandMode(val) {
            updateMapSettings('drawToolFreehandMode', val);
            if(val === false && getPlatformProperty('has.touch')){
                showNotification('negative','WARNING: Draw Tool must be set to Freehand Mode when being used on touch screens.');
            }
        }

        function changeHeatMapBlur(val) {
            updateMapSettings('heatMapBlur', val);
            layersObj['heat'].setBlur(parseInt(mapSettings.heatMapBlur, 10));
        }

        function changeHeatMapRadius(val) {
            updateMapSettings('heatMapRadius', val);
            layersObj['heat'].setRadius(parseInt(mapSettings.heatMapRadius, 10));
        }

        function toggleHeatMap(val) {
            updateMapSettings('showHeatMap', val);
            if(mapSettings.showHeatMap){
                layersObj['pointv'].setVisible(false);
                layersObj['heat'].setVisible(true);
            }
            else{
                if(mapSettings.returnClusters){
                    updateMapSettings('returnClusters', false);
                    updateMapSettings('clusterPoints', true);
                    changeClusterSetting();
                }
                layersObj['heat'].setVisible(false);
                layersObj['pointv'].setVisible(true);
            }
        }

        return {
            mapSettings,
            changeClusterDistance,
            changeClusterPoints,
            changeFreehandMode,
            changeHeatMapBlur,
            changeHeatMapRadius,
            toggleHeatMap,
            updateMapSettings
        }
    }
};
