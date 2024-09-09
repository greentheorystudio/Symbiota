const locationFieldModule = {
    props: {
        data: {
            type: Object,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        eventMode: {
            type: Boolean,
            default: false
        },
        fields: {
            type: Object,
            default: null
        },
        fieldDefinitions: {
            type: Object,
            default: null
        }
    },
    template: `
        <div class="row justify-between q-col-gutter-md">
            <div>
                <checkbox-input-element :disabled="disabled" :definition="fieldDefinitions['localitysecurity']" label="Locality Security" :value="data.localitysecurity" @update:value="updateLocalitySecuritySetting"></checkbox-input-element>
            </div>
            <div v-if="Number(data.localitysecurity) === 1" class="col-12 col-sm-grow col-md-grow">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['localitysecurityreason']" label="Locality Security Reason" :maxlength="fields['localitysecurityreason'] ? fields['localitysecurityreason']['length'] : 0" :value="data.localitysecurityreason" @update:value="(value) => updateData('localitysecurityreason', value)" :show-counter="true"></text-field-input-element>
            </div>
        </div>
        <div v-if="!eventMode" class="row justify-between q-col-gutter-sm">
            <div class="col-12 col-sm-6 col-md-3">
                <single-country-auto-complete :disabled="disabled" :definition="fieldDefinitions['country']" label="Country" :maxlength="fields['country'] ? fields['country']['length'] : 0" :value="data.country" @update:value="(value) => updateData('country', value)"></single-country-auto-complete>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <single-state-province-auto-complete :disabled="disabled" :definition="fieldDefinitions['stateprovince']" label="State/Province" :maxlength="fields['stateprovince'] ? fields['stateprovince']['length'] : 0" :value="data.stateprovince" @update:value="(value) => updateData('stateprovince', value)" :country="data.country"></single-state-province-auto-complete>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <single-county-auto-complete :disabled="disabled" :definition="fieldDefinitions['county']" label="County" :maxlength="fields['county'] ? fields['county']['length'] : 0" :value="data.county" @update:value="(value) => updateData('county', value)" :state-province="data.stateprovince"></single-county-auto-complete>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['municipality']" label="Municipality" :maxlength="fields['municipality'] ? fields['municipality']['length'] : 0" :value="data.municipality" @update:value="(value) => updateData('municipality', value)"></text-field-input-element>
            </div>
        </div>
        <div v-if="!eventMode" class="row q-col-gutter-sm">
            <div class="col-grow">
                <text-field-input-element :disabled="disabled" data-type="textarea" :definition="fieldDefinitions['locality']" label="Locality" :value="data.locality" @update:value="(value) => updateData('locality', value)"></text-field-input-element>
            </div>
        </div>
        <div class="row justify-between q-col-gutter-sm">
            <div class="col-12 col-sm-6 col-md-9 row q-gutter-xs">
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" data-type="number" label="Latitude" :value="data.decimallatitude" min-value="-90" max-value="90" @update:value="(value) => updateData('decimallatitude', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" data-type="number" label="Longitude" :value="data.decimallongitude" min-value="-180" max-value="180" @update:value="(value) => updateData('decimallongitude', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['coordinateuncertaintyinmeters']" label="Uncertainty" :value="data.coordinateuncertaintyinmeters" min-value="0" @update:value="(value) => updateData('coordinateuncertaintyinmeters', value)" :state-province="data.stateprovince"></text-field-input-element>
                </div>
            </div>
            <div v-if="!disabled" class="col-12 col-sm-6 col-md-3 row justify-end q-gutter-sm">
                <div class="self-center">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input-point,uncertainty');" icon="fas fa-globe" dense>
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Open Mapping Aid
                        </q-tooltip>
                    </q-btn>
                </div>
                <div v-if="!eventMode" class="self-center">
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
        <div v-if="!eventMode" class="row justify-between q-col-gutter-sm">
            <div class="col-12 col-sm-2 col-md-3">
                <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['geodeticdatum']" label="Datum" :maxlength="fields['geodeticdatum'] ? fields['geodeticdatum']['length'] : 0" :value="data.geodeticdatum" @update:value="(value) => updateData('geodeticdatum', value)"></text-field-input-element>
            </div>
            <div class="col-12 col-sm-10 col-md-9">
                <occurrence-verbatim-coordinates-input-element :disabled="disabled" :definition="fieldDefinitions['verbatimcoordinates']" label="Verbatim Coordinates" :maxlength="fields['verbatimcoordinates'] ? fields['verbatimcoordinates']['length'] : 0" :value="data.verbatimcoordinates" :geodetic-datum="data.geodeticdatum" :decimal-latitude="data.decimallatitude" @update:value="(value) => updateData('verbatimcoordinates', value)" @update:decimal-coordinates="processRecalculatedDecimalCoordinates"></occurrence-verbatim-coordinates-input-element>
            </div>
        </div>
        <div v-if="!eventMode" class="row justify-between q-col-gutter-sm">
            <div class="col-12 col-sm-6 row justify-start q-col-gutter-md">
                <div class="col-12 col-sm-6">
                    <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['minimumelevationinmeters']" label="Minimum Elevation (m)" :maxlength="fields['minimumelevationinmeters'] ? fields['minimumelevationinmeters']['length'] : 0" :value="data.minimumelevationinmeters" @update:value="(value) => updateData('minimumelevationinmeters', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6">
                    <text-field-input-element :disabled="disabled" data-type="int" :definition="fieldDefinitions['maximumelevationinmeters']" label="Maximum Elevation (m)" :maxlength="fields['maximumelevationinmeters'] ? fields['maximumelevationinmeters']['length'] : 0" :value="data.maximumelevationinmeters" @update:value="(value) => updateData('maximumelevationinmeters', value)"></text-field-input-element>
                </div>
            </div>
            <div class="col-6 row justify-between">
                <div class="col-10">
                    <occurrence-verbatim-elevation-input-element :disabled="disabled" :definition="fieldDefinitions['verbatimelevation']" label="Verbatim Elevation" :maxlength="fields['verbatimelevation'] ? fields['verbatimelevation']['length'] : 0" :value="data.verbatimelevation" :minimum-elevation-in-meters="data.minimumelevationinmeters" @update:value="(value) => updateData('verbatimelevation', value)" @update:elevation-values="processRecalculatedElevationValues"></occurrence-verbatim-elevation-input-element>
                </div>
                <div class="self-center">
                    <div>
                        <template v-if="showExtendedForm">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = false" icon="fas fa-minus" dense></q-btn>
                        </template>
                        <template v-else>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = true" icon="fas fa-plus" dense></q-btn>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <template v-if="!eventMode && showExtendedForm">
            <div class="row justify-between q-col-gutter-sm">
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['continent']" label="Continent" :maxlength="fields['continent'] ? fields['continent']['length'] : 0" :value="data.continent" @update:value="(value) => updateData('continent', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['island']" label="Island" :maxlength="fields['island'] ? fields['island']['length'] : 0" :value="data.island" @update:value="(value) => updateData('island', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['islandgroup']" label="Island Group" :maxlength="fields['islandgroup'] ? fields['islandgroup']['length'] : 0" :value="data.islandgroup" @update:value="(value) => updateData('islandgroup', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['waterbody']" label="Water Body" :maxlength="fields['waterbody'] ? fields['waterbody']['length'] : 0" :value="data.waterbody" @update:value="(value) => updateData('waterbody', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row justify-between q-col-gutter-sm">
                <div class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['georeferencedby']" label="Georeferenced By" :maxlength="fields['georeferencedby'] ? fields['georeferencedby']['length'] : 0" :value="data.georeferencedby" @update:value="(value) => updateData('georeferencedby', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['georeferenceprotocol']" label="Georeference Protocol" :maxlength="fields['georeferenceprotocol'] ? fields['georeferenceprotocol']['length'] : 0" :value="data.georeferenceprotocol" @update:value="(value) => updateData('georeferenceprotocol', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['georeferenceverificationstatus']" label="Georeference Verification Status" :maxlength="fields['georeferenceverificationstatus'] ? fields['georeferenceverificationstatus']['length'] : 0" :value="data.georeferenceverificationstatus" @update:value="(value) => updateData('georeferenceverificationstatus', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['georeferencesources']" label="Georeference Sources" :maxlength="fields['georeferencesources'] ? fields['georeferencesources']['length'] : 0" :value="data.georeferencesources" @update:value="(value) => updateData('georeferencesources', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" data-type="textarea" :definition="fieldDefinitions['georeferenceremarks']" label="Georeference Remarks" :maxlength="fields['georeferenceremarks'] ? fields['georeferenceremarks']['length'] : 0" :value="data.georeferenceremarks" @update:value="(value) => updateData('georeferenceremarks', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" data-type="textarea" :definition="fieldDefinitions['locationremarks']" label="Location Remarks" :value="data.locationremarks" @update:value="(value) => updateData('locationremarks', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row q-gutter-sm">
                <occurrence-footprint-wkt-input-element :disabled="disabled" :definition="fieldDefinitions['footprintwkt']" label="Footprint WKT" :value="data.footprintwkt" @update:value="(value) => updateData('footprintwkt', value)"></occurrence-footprint-wkt-input-element>
            </div>
        </template>
        <template v-if="eventMode">
            <div class="row justify-between q-col-gutter-sm">
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['geodeticdatum']" label="Datum" :maxlength="fields['geodeticdatum'] ? fields['geodeticdatum']['length'] : 0" :value="data.geodeticdatum" @update:value="(value) => updateData('geodeticdatum', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['georeferencedby']" label="Georeferenced By" :maxlength="fields['georeferencedby'] ? fields['georeferencedby']['length'] : 0" :value="data.georeferencedby" @update:value="(value) => updateData('georeferencedby', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['georeferenceprotocol']" label="Georeference Protocol" :maxlength="fields['georeferenceprotocol'] ? fields['georeferenceprotocol']['length'] : 0" :value="data.georeferenceprotocol" @update:value="(value) => updateData('georeferenceprotocol', value)"></text-field-input-element>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['georeferenceverificationstatus']" label="Georeference Verification Status" :maxlength="fields['georeferenceverificationstatus'] ? fields['georeferenceverificationstatus']['length'] : 0" :value="data.georeferenceverificationstatus" @update:value="(value) => updateData('georeferenceverificationstatus', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" :definition="fieldDefinitions['georeferencesources']" label="Georeference Sources" :maxlength="fields['georeferencesources'] ? fields['georeferencesources']['length'] : 0" :value="data.georeferencesources" @update:value="(value) => updateData('georeferencesources', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element :disabled="disabled" data-type="textarea" :definition="fieldDefinitions['georeferenceremarks']" label="Georeference Remarks" :maxlength="fields['georeferenceremarks'] ? fields['georeferenceremarks']['length'] : 0" :value="data.georeferenceremarks" @update:value="(value) => updateData('georeferenceremarks', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row q-gutter-sm">
                <occurrence-footprint-wkt-input-element :disabled="disabled" :definition="fieldDefinitions['footprintwkt']" label="Footprint WKT" :value="data.footprintwkt" @update:value="(value) => updateData('footprintwkt', value)"></occurrence-footprint-wkt-input-element>
            </div>
        </template>
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
                :country="data.country"
                :county="data.county"
                :locality="data.locality"
                :show-popup="showGeoLocatePopup"
                :state="data.stateprovince"
                :verbatim-coordinates="data.verbatimcoordinates"
                @update:geolocate-data="processGeolocateData"
                @close:popup="showGeoLocatePopup = false"
            ></geo-locate-popup>
        </template>
        <template v-if="showCoordinateToolPopup">
            <occurrence-coordinate-tool-popup
                :geodetic-datum="data.geodeticdatum"
                :show-popup="showCoordinateToolPopup"
                :verbatim-coordinates="data.verbatimcoordinates"
                @update:coordinate-tool-data="processCoordinateToolData"
                @close:popup="showCoordinateToolPopup = false"
            ></occurrence-coordinate-tool-popup>
        </template>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'confirmation-popup': confirmationPopup,
        'geo-locate-popup': geoLocatePopup,
        'occurrence-coordinate-tool-popup': occurrenceCoordinateToolPopup,
        'occurrence-footprint-wkt-input-element': occurrenceFootprintWktInputElement,
        'occurrence-verbatim-coordinates-input-element': occurrenceVerbatimCoordinatesInputElement,
        'occurrence-verbatim-elevation-input-element': occurrenceVerbatimElevationInputElement,
        'single-country-auto-complete': singleCountryAutoComplete,
        'single-county-auto-complete': singleCountyAutoComplete,
        'single-state-province-auto-complete': singleStateProvinceAutoComplete,
        'spatial-analysis-popup': spatialAnalysisPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { getCoordinateVerificationData, showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const confirmationPopupRef = Vue.ref(null);
        const coordinateUncertaintyInMetersValue = Vue.ref(null);
        const decimalLatitudeValue = Vue.ref(null);
        const decimalLongitudeValue = Vue.ref(null);
        const footprintWktValue = Vue.ref(null);
        const imageCount = Vue.computed(() => occurrenceStore.getImageCount);
        const popupWindowType = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const showCoordinateToolPopup = Vue.ref(false);
        const showExtendedForm = Vue.ref(false);
        const showGeoLocatePopup = Vue.ref(false);
        const showSpatialPopup = Vue.ref(false);

        Vue.watch(propsRefs.data, () => {
            if((!props.disabled && !props.eventMode) || imageCount.value > 0){
                setExtendedView();
            }
        });

        function clearSpatialInputValues() {
            coordinateUncertaintyInMetersValue.value = null;
            decimalLatitudeValue.value = null;
            decimalLongitudeValue.value = null;
            footprintWktValue.value = null;
        }

        function closeSpatialPopup() {
            popupWindowType.value = null;
            showSpatialPopup.value = false;
            clearSpatialInputValues();
        }

        function openGeolocatePopup() {
            if(!props.data.country){
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
            if(data.decimalLatitude && data.decimalLongitude){
                updateData('decimallatitude', data['decimalLatitude']);
                updateData('decimallongitude', data['decimalLongitude']);
            }
            if(data.verbatimCoordinates){
                updateData('verbatimcoordinates', data['verbatimCoordinates']);
            }
            showCoordinateToolPopup.value = false;
        }

        function processGeolocateData(data) {
            if(data.decimalLatitude && data.decimalLongitude){
                updateData('decimallatitude', data['decimalLatitude']);
                updateData('decimallongitude', data['decimalLongitude']);
                if(data.coordinateUncertaintyInMeters){
                    updateData('coordinateuncertaintyinmeters', data['coordinateUncertaintyInMeters']);
                }
            }
            if(data.footprintWkt){
                updateData('footprintwkt', data['footprintWkt']);
            }
            if((data.decimalLatitude && data.decimalLongitude) || data.footprintWkt){
                updateData('georeferencesources', 'GeoLocate');
                updateData('geodeticdatum', 'WGS84');
            }
            showGeoLocatePopup.value = false;
        }

        function processRecalculatedDecimalCoordinates(data) {
            if(data.decimalLatitude && data.decimalLongitude){
                updateData('decimallatitude', data['decimalLatitude']);
                updateData('decimallongitude', data['decimalLongitude']);
            }
        }

        function processRecalculatedElevationValues(data) {
            if(data.minimumElevationInMeters){
                updateData('minimumelevationinmeters', data['minimumElevationInMeters']);
                if(data.maximumElevationInMeters){
                    updateData('maximumelevationinmeters', data['maximumElevationInMeters']);
                }
            }
        }

        function processSpatialData(data) {
            if(popupWindowType.value.includes('point') && data.hasOwnProperty('decimalLatitude') && data.hasOwnProperty('decimalLongitude')){
                const latDecimalPlaces = (props.data && props.data.hasOwnProperty('decimallatitude') && props.data['decimallatitude']) ? props.data['decimallatitude'].toString().split('.')[1].length : null;
                const longDecimalPlaces = (props.data && props.data.hasOwnProperty('decimallongitude') && props.data['decimallongitude']) ? props.data['decimallongitude'].toString().split('.')[1].length : null;
                if(!latDecimalPlaces || Number(props.data['decimallatitude']) !== Number(Number(data['decimalLatitude']).toFixed(latDecimalPlaces))){
                    updateData('decimallatitude', data['decimalLatitude']);
                }
                if(!longDecimalPlaces || Number(props.data['decimallongitude']) !== Number(Number(data['decimalLongitude']).toFixed(longDecimalPlaces))){
                    updateData('decimallongitude', data['decimalLongitude']);
                }
                if(Number(data['coordinateUncertaintyInMeters']) > 0){
                    updateData('coordinateuncertaintyinmeters', data['coordinateUncertaintyInMeters']);
                }
            }
            else if(popupWindowType.value.includes('wkt') && data.hasOwnProperty('footprintWKT')){
                updateData('footprintwkt', data['footprintWKT']);
            }
        }

        function setExtendedView() {
            if(props.data.footprintwkt ||
                props.data.continent ||
                props.data.island ||
                props.data.islandgroup ||
                props.data.waterbody ||
                props.data.georeferencedby ||
                props.data.georeferenceprotocol ||
                props.data.georeferenceremarks ||
                props.data.georeferencesources ||
                props.data.georeferenceverificationstatus ||
                props.data.locationremarks ||
                (props.eventMode && imageCount.value > 0)
            ){
                showExtendedForm.value = true;
            }
        }

        function setSpatialInputValues() {
            coordinateUncertaintyInMetersValue.value = props.data['coordinateuncertaintyinmeters'];
            decimalLatitudeValue.value = props.data['decimallatitude'];
            decimalLongitudeValue.value = props.data['decimallongitude'];
            footprintWktValue.value = props.data['footprintwkt'];
        }

        function updateData(key, value) {
            context.emit('update:location-data', {key: key, value: value});
            if(key === 'decimallongitude' && props.data['decimallatitude']){
                validateCoordinates();
            }
        }

        function updateLocalitySecuritySetting(value) {
            if(Number(value) === 1){
                updateData('localitysecurity', value);
            }
            else{
                updateData('localitysecurity', '0');
                updateData('localitysecurityreason', value);
            }
        }

        function validateCoordinates() {
            getCoordinateVerificationData(props.data['decimallatitude'], props.data['decimallongitude'], (data) => {
                if(data.hasOwnProperty('address')){
                    const addressArr = data.address;
                    let coordCountry = addressArr.country;
                    let coordState = addressArr.state;
                    let coordCounty = addressArr.county;
                    let coordValid = true;
                    if((!props.data['country'] || props.data['country'] === '') && coordCountry && coordCountry !== ''){
                        updateData('country', coordCountry);
                    }
                    if(props.data['country'] && coordCountry && props.data['country'] !== '' && props.data['country'].toLowerCase() !== coordCountry.toLowerCase()){
                        if(props.data['country'].toLowerCase() !== 'usa' && props.data['country'].toLowerCase() !== 'united states of america' && coordCountry.toLowerCase() !== 'united states'){
                            coordValid = false;
                        }
                    }
                    if(coordState && coordState !== ''){
                        if(props.data['stateprovince'] && props.data['stateprovince'] !== '' && props.data['stateprovince'].toLowerCase() !== coordState.toLowerCase()){
                            coordValid = false;
                        }
                        else{
                            updateData('stateprovince', coordState);
                        }
                    }
                    if(coordCounty && coordCounty !== ''){
                        let coordCountyIn = coordCounty.replace(' County', '');
                        coordCountyIn = coordCountyIn.replace(' Parish', '');
                        if(props.data['county'] && props.data['county'] !== '' && props.data['county'].toLowerCase() !== coordCountyIn.toLowerCase()){
                            coordValid = false;
                        }
                        else{
                            updateData('county', coordCountyIn);
                        }
                    }
                    if(!coordValid){
                        let alertText = 'Are those coordinates accurate? They currently map to: ' + coordCountry + ', ' + coordState;
                        if(coordCounty) {
                            alertText += ', ' + coordCounty;
                        }
                        alertText += ', which differs from what you have entered.';
                        confirmationPopupRef.value.openPopup(alertText);
                    }
                }
                else{
                    showNotification('negative', 'Unable to identify a country from the coordinates entered. Are they accurate?');
                }
            });
        }

        Vue.provide('openSpatialPopup', openSpatialPopup);

        Vue.onMounted(() => {
            if((!props.disabled && !props.eventMode) || imageCount.value > 0){
                setExtendedView();
            }
        });

        return {
            confirmationPopupRef,
            coordinateUncertaintyInMetersValue,
            decimalLatitudeValue,
            decimalLongitudeValue,
            footprintWktValue,
            popupWindowType,
            showCoordinateToolPopup,
            showExtendedForm,
            showGeoLocatePopup,
            showSpatialPopup,
            closeSpatialPopup,
            openGeolocatePopup,
            openSpatialPopup,
            processCoordinateToolData,
            processGeolocateData,
            processRecalculatedDecimalCoordinates,
            processRecalculatedElevationValues,
            processSpatialData,
            updateData,
            updateLocalitySecuritySetting
        }
    }
};
