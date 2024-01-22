const spatialInfoWindowPopup = {
    template: `
        <q-dialog class="z-top" v-model="mapSettings.showInfoWindow" persistent>
            <q-card class="sm-map-popup">
                <div class="row justify-end items-start map-popup-header">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="updateMapSettings('showInfoWindow', false);"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md">
                    {{ infoText }}
                </div>
            </q-card>
        </q-dialog>
    `,
    setup() {
        const infoText = Vue.ref('');
        const inputWindowToolsArr = Vue.inject('inputWindowToolsArr');
        const mapSettings = Vue.inject('mapSettings');

        const updateMapSettings = Vue.inject('updateMapSettings');

        Vue.onMounted(() => {
            if(inputWindowToolsArr.includes('point')){
                infoText.value += 'Select a Point feature type in the Draw drop-down menu and then click on the map to draw a new point. ';
                if(inputWindowToolsArr.includes('uncertainty')){
                    infoText.value += 'Once a point has been drawn, enter a numeric value for the Coordinate uncertainty in meters to see the uncertainty circle. ';
                    infoText.value += 'Click the Submit Coordinates button to submit both the point and uncertainty. ';
                }
                else if(inputWindowToolsArr.includes('radius')){
                    infoText.value += 'Once a point has been drawn, enter a numeric value for the Radius in meters to see the point-radius circle. ';
                    infoText.value += 'Click the Submit Coordinates button to submit both the point and radius. ';
                }
                else{
                    infoText.value += 'Click the Submit Coordinates button to submit the point. ';
                }
            }
            else{
                infoText.value += 'Select a feature type in the Draw drop-down menu and then click on the map to draw a new feature. ';
                infoText.value += 'Click on any drawn feature on the map to select and deselect. ';
                if(inputWindowToolsArr.length > 0){
                    infoText.value += 'When the feature you would like to submit is selected, and it is the only selected feature, you can click the Submit Coordinates button to submit. ';
                }
                else{
                    infoText.value += 'Once all of the features you would like to submit have been selected, click the Submit Coordinates button to submit. ';
                }
            }
        });
        
        return {
            infoText,
            mapSettings,
            updateMapSettings
        }
    }
};
