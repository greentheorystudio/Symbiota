const mapWindowConfigurationsTab = {
    template: `
        <div class="fit column">
            <div class="fit col-1 q-mb-md full-width column">
                <div class="full-width row justify-between">
                    <div class="col-11">
                        Adjust the Base Layer and zoom level, and move the map below to how you would like map windows to open within the portal.
                        Then click the Save Settings button to save the settings.
                    </div>
                    <div class="col-1 row justify-end">
                        <div role="button" class="cursor-pointer" @click="openTutorialWindow('/tutorial/admin/mappingConfigurationManager/index.php');" aria-label="Open Tutorial Window" tabindex="0">
                            <q-icon name="far fa-question-circle" size="20px" />
                        </div>
                    </div>
                </div>
                <div class="full-width row justify-end items-center">
                    <div>
                        <q-btn color="primary" @click="updateConfigurations();" label="Save Settings" :disabled="!mapBaseLayerValue || !mapCenterValue || !mapZoomValue" tabindex="0" />
                    </div>
                </div>
            </div>
            <div class="col-grow">
                <div>
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
            configurationStore.updateConfigurationEditData('SPATIAL_INITIAL_BASE_LAYER', mapBaseLayerValue.value);
            configurationStore.updateConfigurationEditData('SPATIAL_INITIAL_ZOOM', mapZoomValue.value.toString());
            configurationStore.updateConfigurationEditData('SPATIAL_INITIAL_CENTER', mapCenterValue.value);
            configurationStore.updateConfigurationData((res) => {
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
