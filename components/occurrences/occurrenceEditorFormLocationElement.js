const occurrenceEditorFormLocationElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                <div class="row justify-between q-col-gutter-md">
                    <div>
                        <checkbox-input-element :definition="occurrenceFieldDefinitions['localitysecurity']" label="Locality Security" :value="occurrenceData.localitysecurity" @update:value="updateLocalitySecuritySetting"></checkbox-input-element>
                    </div>
                    <div v-if="Number(occurrenceData.localitysecurity) === 1" class="col-12 col-sm-grow col-md-grow">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['localitysecurityreason']" label="Locality Security Reason" :maxlength="occurrenceFields['localitysecurityreason'] ? occurrenceFields['localitysecurityreason']['length'] : 0" :value="occurrenceData.localitysecurityreason" @update:value="(value) => updateOccurrenceData('localitysecurityreason', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-6 col-md-3">
                        <single-country-auto-complete :definition="occurrenceFieldDefinitions['country']" label="Country" :maxlength="occurrenceFields['country'] ? occurrenceFields['country']['length'] : 0" :value="occurrenceData.country" @update:value="(value) => updateOccurrenceData('country', value)" :show-counter="false"></single-country-auto-complete>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <single-state-province-auto-complete :definition="occurrenceFieldDefinitions['stateprovince']" label="State/Province" :maxlength="occurrenceFields['stateprovince'] ? occurrenceFields['stateprovince']['length'] : 0" :value="occurrenceData.stateprovince" @update:value="(value) => updateOccurrenceData('stateprovince', value)" :show-counter="false" :country="occurrenceData.country"></single-state-province-auto-complete>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <single-county-auto-complete :definition="occurrenceFieldDefinitions['county']" label="County" :maxlength="occurrenceFields['county'] ? occurrenceFields['county']['length'] : 0" :value="occurrenceData.county" @update:value="(value) => updateOccurrenceData('county', value)" :show-counter="false" :state-province="occurrenceData.stateprovince"></single-county-auto-complete>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['municipality']" label="Municipality" :maxlength="occurrenceFields['municipality'] ? occurrenceFields['municipality']['length'] : 0" :value="occurrenceData.municipality" @update:value="(value) => updateOccurrenceData('municipality', value)" :show-counter="false"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-grow">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['locality']" label="Locality" :value="occurrenceData.locality" @update:value="(value) => updateOccurrenceData('locality', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element data-type="number" label="Latitude" :value="occurrenceData.decimallatitude" min-value="-90" max-value="90" @update:value="(value) => updateOccurrenceData('decimallatitude', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element data-type="number" label="Longitude" :value="occurrenceData.decimallongitude" min-value="-180" max-value="180" @update:value="(value) => updateOccurrenceData('decimallongitude', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['coordinateuncertaintyinmeters']" label="Uncertainty" :value="occurrenceData.coordinateuncertaintyinmeters" min-value="0" @update:value="(value) => updateOccurrenceData('coordinateuncertaintyinmeters', value)" :show-counter="false" :state-province="occurrenceData.stateprovince"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 row justify-end q-gutter-sm">
                        <div class="self-center">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input-point,uncertainty');" icon="fas fa-globe" dense>
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Open Mapping Aid
                                </q-tooltip>
                            </q-btn>
                        </div>
                        <div class="self-center">
                            <q-btn color="grey-4" class="black-border" size="sm" @click="openGeolocatePopup();" dense>
                                <q-avatar size="xs">
                                    <img src="../../images/geolocate.png">
                                </q-avatar>
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    GeoLocate locality
                                </q-tooltip>
                            </q-btn>
                        </div>
                        <div class="self-center">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="showCoordinateToolPopup = true" icon="fas fa-tools" dense>
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Tools for converting additional formats
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-2 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['geodeticdatum']" label="Datum" :maxlength="occurrenceFields['geodeticdatum'] ? occurrenceFields['geodeticdatum']['length'] : 0" :value="occurrenceData.geodeticdatum" @update:value="(value) => updateOccurrenceData('geodeticdatum', value)" :show-counter="false"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-10 col-md-9">
                        <occurrence-verbatim-coordinates-input-element :definition="occurrenceFieldDefinitions['verbatimcoordinates']" label="Verbatim Coordinates" :maxlength="occurrenceFields['verbatimcoordinates'] ? occurrenceFields['verbatimcoordinates']['length'] : 0" :value="occurrenceData.verbatimcoordinates" :geodetic-datum="occurrenceData.geodeticdatum" @update:value="(value) => updateOccurrenceData('verbatimcoordinates', value)" @update:decimal-coordinates="processRecalculatedDecimalCoordinates"></occurrence-verbatim-coordinates-input-element>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <template v-if="showSpatialPopup">
            <spatial-analysis-popup
                    :coordinate-uncertainty-in-meters="coordinateUncertaintyInMetersValue"
                    :decimal-latitude="decimalLatitudeValue"
                    :decimal-longitude="decimalLongitudeValue"
                    :footprint-wkt="footprintWktValue"
                    :show-popup="showSpatialPopup"
                    :window-type="popupWindowType"
                    @update:spatial-data="processSpatialData"
                    @close:popup="closeSpatialPopup();"
            ></spatial-analysis-popup>
        </template>
        <template v-if="showGeoLocatePopup">
            <geo-locate-popup
                    :country="occurrenceData.country"
                    :county="occurrenceData.county"
                    :locality="occurrenceData.locality"
                    :show-popup="showGeoLocatePopup"
                    :state="occurrenceData.stateprovince"
                    :verbatim-coordinates="occurrenceData.verbatimcoordinates"
                    @update:geolocate-data="processGeolocateData"
                    @close:popup="closeGeolocatePopup();"
            ></geo-locate-popup>
        </template>
        <template v-if="showCoordinateToolPopup">
            <occurrence-coordinate-tool-popup
                    :geodetic-datum="occurrenceData.geodeticdatum"
                    :show-popup="showCoordinateToolPopup"
                    :verbatim-coordinates="occurrenceData.verbatimcoordinates"
                    @update:coordinate-tool-data="processCoordinateToolData"
                    @close:popup="closeCoordinateToolPopup();"
            ></occurrence-coordinate-tool-popup>
        </template>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'geo-locate-popup': geoLocatePopup,
        'occurrence-coordinate-tool-popup': occurrenceCoordinateToolPopup,
        'occurrence-verbatim-coordinates-input-element': occurrenceVerbatimCoordinatesInputElement,
        'single-country-auto-complete': singleCountryAutoComplete,
        'single-county-auto-complete': singleCountyAutoComplete,
        'single-state-province-auto-complete': singleStateProvinceAutoComplete,
        'spatial-analysis-popup': spatialAnalysisPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const coordinateUncertaintyInMetersValue = Vue.ref(null);
        const decimalLatitudeValue = Vue.ref(null);
        const decimalLongitudeValue = Vue.ref(null);
        const footprintWktValue = Vue.ref(null);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const popupWindowType = Vue.ref(null);
        const showCoordinateToolPopup = Vue.ref(false);
        const showGeoLocatePopup = Vue.ref(false);
        const showSpatialPopup = Vue.ref(false);

        function clearSpatialInputValues() {
            coordinateUncertaintyInMetersValue.value = null;
            decimalLatitudeValue.value = null;
            decimalLongitudeValue.value = null;
            footprintWktValue.value = null;
        }

        function closeCoordinateToolPopup() {
            showCoordinateToolPopup.value = false;
        }

        function closeGeolocatePopup() {
            showGeoLocatePopup.value = false;
        }

        function closeSpatialPopup() {
            popupWindowType.value = null;
            showSpatialPopup.value = false;
            clearSpatialInputValues();
        }

        function openGeolocatePopup() {
            if(!occurrenceData.value.country){
                showNotification('negative', 'Country is a required field for GeoLocate.');
            }
            else{
                showGeoLocatePopup.value = true;
            }
        }

        function openSpatialPopup(type) {
            setSpatialInputValues();
            popupWindowType.value = type;
            showSpatialPopup.value = true;
        }

        function processCoordinateToolData(data) {
            if(data.decimalLatitude && data.decimalLongitude && !occurrenceData.value.decimallatitude && !occurrenceData.value.decimallongitude){
                occurrenceStore.updateOccurrenceEditData('decimallatitude', data['decimalLatitude']);
                occurrenceStore.updateOccurrenceEditData('decimallongitude', data['decimalLongitude']);
            }
            if(data.verbatimCoordinates){
                occurrenceStore.updateOccurrenceEditData('verbatimcoordinates', data['verbatimCoordinates']);
            }
            closeCoordinateToolPopup();
        }

        function processGeolocateData(data) {
            if(data.decimalLatitude && data.decimalLongitude){
                occurrenceStore.updateOccurrenceEditData('decimallatitude', data['decimalLatitude']);
                occurrenceStore.updateOccurrenceEditData('decimallongitude', data['decimalLongitude']);
                if(data.coordinateUncertaintyInMeters){
                    occurrenceStore.updateOccurrenceEditData('coordinateuncertaintyinmeters', data['coordinateUncertaintyInMeters']);
                }
            }
            if(data.footprintWkt){
                occurrenceStore.updateOccurrenceEditData('footprintwkt', data['footprintWkt']);
            }
            if((data.decimalLatitude && data.decimalLongitude) || data.footprintWkt){
                occurrenceStore.updateOccurrenceEditData('georeferencesources', 'GeoLocate');
                occurrenceStore.updateOccurrenceEditData('geodeticdatum', 'WGS84');
            }
            closeGeolocatePopup();
        }

        function processRecalculatedDecimalCoordinates(data) {
            if(data.decimalLatitude && data.decimalLongitude){
                occurrenceStore.updateOccurrenceEditData('decimallatitude', data['decimalLatitude']);
                occurrenceStore.updateOccurrenceEditData('decimallongitude', data['decimalLongitude']);
            }
        }

        function processSpatialData(data) {
            if(popupWindowType.value.includes('point') && data.hasOwnProperty('decimalLatitude') && data.hasOwnProperty('decimalLongitude')){
                const latDecimalPlaces = occurrenceData.value['decimallatitude'].toString().split('.')[1].length;
                const longDecimalPlaces = occurrenceData.value['decimallongitude'].toString().split('.')[1].length;
                if(Number(occurrenceData.value['decimallatitude']) !== Number(Number(data['decimalLatitude']).toFixed(latDecimalPlaces))){
                    occurrenceStore.updateOccurrenceEditData('decimallatitude', data['decimalLatitude']);
                }
                if(Number(occurrenceData.value['decimallongitude']) !== Number(Number(data['decimalLongitude']).toFixed(longDecimalPlaces))){
                    occurrenceStore.updateOccurrenceEditData('decimallongitude', data['decimalLongitude']);
                }
                if(Number(data['coordinateUncertaintyInMeters']) > 0){
                    occurrenceStore.updateOccurrenceEditData('coordinateuncertaintyinmeters', data['coordinateUncertaintyInMeters']);
                }
            }
        }

        function setSpatialInputValues() {
            coordinateUncertaintyInMetersValue.value = occurrenceData.value['coordinateuncertaintyinmeters'];
            decimalLatitudeValue.value = occurrenceData.value['decimallatitude'];
            decimalLongitudeValue.value = occurrenceData.value['decimallongitude'];
            footprintWktValue.value = occurrenceData.value['footprintwkt'];
        }

        function updateLocalitySecuritySetting(value) {
            if(Number(value) === 1){
                occurrenceStore.updateOccurrenceEditData('localitysecurity', value);
            }
            else{
                occurrenceStore.updateOccurrenceEditData('localitysecurity', '0');
                occurrenceStore.updateOccurrenceEditData('localitysecurityreason', value);
            }
        }

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
        }

        return {
            coordinateUncertaintyInMetersValue,
            decimalLatitudeValue,
            decimalLongitudeValue,
            footprintWktValue,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            popupWindowType,
            showCoordinateToolPopup,
            showGeoLocatePopup,
            showSpatialPopup,
            closeCoordinateToolPopup,
            closeGeolocatePopup,
            closeSpatialPopup,
            openGeolocatePopup,
            openSpatialPopup,
            processCoordinateToolData,
            processGeolocateData,
            processRecalculatedDecimalCoordinates,
            processSpatialData,
            updateLocalitySecuritySetting,
            updateOccurrenceData
        }
    }
};
