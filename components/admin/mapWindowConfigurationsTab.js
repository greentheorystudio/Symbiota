const mapWindowConfigurationsTab = {
    template: `
        <div class="fit column">
            <div class="col-1 q-mb-md full-width column">
                <div class="full-width row justify-between">
                    <div class="col-11">
                        Adjust the Base Layer and zoom level, and move the map below to how you would like map windows to open within the portal.
                        Then click the Save Settings button to save the settings.
                    </div>
                    <div class="col-1 row justify-end">
                        <div onclick="openTutorialWindow('/tutorial/admin/mappingConfigurationManager/index.php');" title="Open Tutorial Window">
                            <q-icon name="far fa-question-circle" size="20px" class="cursor-pointer" />
                        </div>
                    </div>
                </div>
                <div class="full-width row justify-end items-center">
                    <div>
                        <q-btn color="primary" @click="updateConfigurations();" label="Save Settings" :disabled="!mapBaseLayerValue || !mapCenterValue || !mapZoomValue" />
                    </div>
                </div>
            </div>
            <div class="col-grow">
                <div class="fit">
                    <spatial-viewer-element height="625" @change:map-settings="processMapChange"></spatial-viewer-element>
                </div>
            </q-card>
        </div>
    `,
    components: {
        'spatial-viewer-element': spatialViewerElement
    },
    setup() {
        const { showNotification } = useCore();
        const configurationStore = useConfigurationStore();

        const mapBaseLayerValue = Vue.ref(null);
        const mapCenterValue = Vue.ref(null);
        const mapZoomValue = Vue.ref(null);

        function processMapChange(data) {
            mapBaseLayerValue.value = data['baseLayer'];
            mapCenterValue.value = data['mapCenter'];
            mapZoomValue.value = data['zoom'];
        }

        function updateConfigurations(){
            configurationStore.updateConfigurationValueDataObj({
                'SPATIAL_INITIAL_BASE_LAYER': mapBaseLayerValue.value,
                'SPATIAL_INITIAL_ZOOM': mapZoomValue.value.toString(),
                'SPATIAL_INITIAL_CENTER': mapCenterValue.value
            }, (res) => {
                if(res === 1){
                    showNotification('positive','Settings saved');
                }
                else{
                    showNotification('negative', 'There was an error saving the settings');
                }
            });
        }

        return {
            mapBaseLayerValue,
            mapCenterValue,
            mapZoomValue,
            processMapChange,
            updateConfigurations
        }
    }
};
