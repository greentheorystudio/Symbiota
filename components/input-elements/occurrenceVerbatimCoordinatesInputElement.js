const occurrenceCoordinateToolPopup = {
    props: {
        geodeticDatum: {
            type: String,
            default: null
        },
        tabindex: {
            type: Number,
            default: 0
        },
        verbatimCoordinates: {
            type: String,
            default: null
        }
    },
    template: `
        <q-card class="z-max">
            <q-tabs v-model="tab" dense class="text-grey" active-color="primary" indicator-color="primary" align="justify" narrow-indicator>
                <q-tab name="dms" label="DMS"></q-tab>
                <q-tab name="ddm" label="DDM"></q-tab>
                <q-tab name="utm" label="UTM"></q-tab>
                <q-tab name="trs" label="TRS"></q-tab>
            </q-tabs>
            <q-separator></q-separator>
            <q-tab-panels v-model="tab" animated>
                <q-tab-panel name="dms" class="q-pa-sm column q-col-gutter-xs">
                    <div class="text-h6">Degrees, Minutes, Seconds</div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="self-center text-bold">
                            Lat:
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="latDegreeValue" label="Degrees" dense :tabindex="tabindex">
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="latMinuteValue" label="Minutes" dense :tabindex="tabindex">
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="latSecondValue" label="Seconds" dense :tabindex="tabindex">
                        </div>
                        <div class="self-center">
                            <q-select bg-color="white" outlined v-model="latNorthSouthValue" :options="nsSelectorOptions" popup-content-class="z-max" behavior="menu" dense options-dense :tabindex="tabindex" />
                        </div>
                    </div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="self-center text-bold">
                            Long:
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="longDegreeValue" label="Degrees" dense :tabindex="tabindex">
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="longMinuteValue" label="Minutes" dense :tabindex="tabindex">
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="longSecondValue" label="Seconds" dense :tabindex="tabindex">
                        </div>
                        <div class="self-center">
                            <q-select bg-color="white" outlined v-model="longWestEastValue" :options="weSelectorOptions" popup-content-class="z-max" behavior="menu" dense options-dense :tabindex="tabindex" />
                        </div>
                    </div>
                    <div class="q-mt-md row justify-end q-gutter-sm">
                        <q-btn color="negative" @click="closePopup();" label="Close" dense aria-label="Close definition pop up" :tabindex="tabindex"></q-btn>
                        <q-btn color="primary" @click="transcribeDMSData();" label="Process DMS Values" dense :tabindex="tabindex"></q-btn>
                    </div>
                </q-tab-panel>
                <q-tab-panel name="ddm" class="q-pa-sm column q-col-gutter-xs">
                    <div class="text-h6">Degrees, Decimal Minutes</div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="self-center text-bold">
                            Lat:
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="latDDMDegreeValue" label="Degrees" dense :tabindex="tabindex">
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="latDDMMinuteValue" label="Decimal Minutes" dense :tabindex="tabindex">
                        </div>
                        <div class="self-center">
                            <q-select bg-color="white" outlined v-model="latDDMNorthSouthValue" :options="nsSelectorOptions" popup-content-class="z-max" behavior="menu" dense options-dense :tabindex="tabindex" />
                        </div>
                    </div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="self-center text-bold">
                            Long:
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="longDDMDegreeValue" label="Degrees" dense :tabindex="tabindex">
                        </div>
                        <div class="col self-center">
                            <q-input outlined v-model="longDDMMinuteValue" label="Decimal Minutes" dense :tabindex="tabindex">
                        </div>
                        <div class="self-center">
                            <q-select bg-color="white" outlined v-model="longDDMWestEastValue" :options="weSelectorOptions" popup-content-class="z-max" behavior="menu" dense options-dense :tabindex="tabindex" />
                        </div>
                    </div>
                    <div class="q-mt-md row justify-end q-gutter-sm">
                        <q-btn color="negative" @click="closePopup();" label="Close" dense :tabindex="tabindex"></q-btn>
                        <q-btn color="primary" @click="transcribeDDMData();" label="Process DDM Values" dense :tabindex="tabindex"></q-btn>
                    </div>
                </q-tab-panel>
                <q-tab-panel name="utm" class="q-pa-sm column q-col-gutter-xs">
                    <div class="text-h6">UTM</div>
                    <div>
                        <q-input outlined v-model="utmZoneValue" label="UTM Zone" dense :tabindex="tabindex">
                    </div>
                    <div>
                        <q-input outlined v-model="utmEastingValue" label="UTM Easting" dense :tabindex="tabindex">
                    </div>
                    <div>
                        <q-input outlined v-model="utmNorthingValue" label="UTM Northing" dense :tabindex="tabindex">
                    </div>
                    <div >
                        <q-select bg-color="white" outlined v-model="utmHemisphereValue" :options="northSouthSelectorOptions" label="Hemisphere" popup-content-class="z-max" behavior="menu" dense options-dense :tabindex="tabindex" />
                    </div>
                    <div class="q-mt-md row justify-end q-gutter-sm">
                        <q-btn color="negative" @click="closePopup();" label="Close" dense :tabindex="tabindex"></q-btn>
                        <q-btn color="primary" @click="transcribeUTMData();" label="Process UTM Values" dense :tabindex="tabindex"></q-btn>
                    </div>
                </q-tab-panel>
                <q-tab-panel name="trs" class="q-pa-sm column q-col-gutter-xs">
                    <div class="text-h6">Township, Range, Section</div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="col">
                            <q-input outlined v-model="trsTownshipValue" label="Township" dense :tabindex="tabindex">
                        </div>
                        <div class="col">
                            <q-select bg-color="white" outlined v-model="trsTownshipNorthSouthValue" :options="nsSelectorOptions" popup-content-class="z-max" behavior="menu" dense options-dense :tabindex="tabindex" />
                        </div>
                        <div class="col">
                            <q-input outlined v-model="trsRangeValue" label="Range" dense :tabindex="tabindex">
                        </div>
                        <div>
                            <q-select bg-color="white" outlined v-model="trsRangeWestEastValue" :options="weSelectorOptions" popup-content-class="z-max" behavior="menu" dense options-dense :tabindex="tabindex" />
                        </div>
                    </div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="col-3">
                            <q-input outlined v-model="trsSectionValue" label="Section" dense :tabindex="tabindex">
                        </div>
                        <div class="col-9">
                            <q-input outlined v-model="trsDetailsValue" label="Details" dense :tabindex="tabindex">
                        </div>
                    </div>
                    <div class="row justify-between q-col-gutter-xs">
                        <div class="col-12">
                            <q-select bg-color="white" outlined v-model="trsMerideanValue" :options="trsMerideanOptions" :option-value="value" :option-label="label" label="Meridian Selection" popup-content-class="z-max" behavior="menu" dense options-dense :tabindex="tabindex" />
                        </div>
                    </div>
                    <div class="q-mt-md row justify-end q-gutter-sm">
                        <q-btn color="negative" @click="closePopup();" label="Close" dense :tabindex="tabindex"></q-btn>
                        <q-btn color="primary" @click="transcribeTRSData();" label="Process TRS Values" dense :tabindex="tabindex"></q-btn>
                    </div>
                </q-tab-panel>
            </q-tab-panels>
        </q-card>
    `,
    setup(props, context) {
        const { convertUtmToDecimalDegrees, showNotification } = useCore();

        const latDDMDegreeValue = Vue.ref(null);
        const latDDMMinuteValue = Vue.ref(null);
        const latDDMNorthSouthValue = Vue.ref('N');
        const latDegreeValue = Vue.ref(null);
        const latMinuteValue = Vue.ref(null);
        const latSecondValue = Vue.ref(null);
        const latNorthSouthValue = Vue.ref('N');
        const longDDMDegreeValue = Vue.ref(null);
        const longDDMMinuteValue = Vue.ref(null);
        const longDDMWestEastValue = Vue.ref('W');
        const longDegreeValue = Vue.ref(null);
        const longMinuteValue = Vue.ref(null);
        const longSecondValue = Vue.ref(null);
        const longWestEastValue = Vue.ref('W');
        const northSouthSelectorOptions = [
            'North', 'South'
        ];
        const nsSelectorOptions = [
            'N', 'S'
        ];
        const returnData = {};
        const tab = Vue.ref('dms');
        const trsTownshipValue = Vue.ref(null);
        const trsTownshipNorthSouthValue = Vue.ref('N');
        const trsRangeValue = Vue.ref(null);
        const trsRangeWestEastValue = Vue.ref('W');
        const trsSectionValue = Vue.ref(null);
        const trsDetailsValue = Vue.ref(null);
        const trsMerideanOptions = [
            {value: '--------------------------', label: '--------------------------'},
            {value: 'G-AZ', label: 'Arizona, Gila & Salt River'},
            {value: 'NAAZ', label: 'Arizona, Navajo'},
            {value: 'F-AR', label: 'Arkansas, Fifth Principal'},
            {value: 'H-CA', label: 'California, Humboldt'},
            {value: 'M-CA', label: 'California, Mt. Diablo'},
            {value: 'S-CA', label: 'California, San Bernardino'},
            {value: 'NMCO', label: 'Colorado, New Mexico'},
            {value: 'SPCO', label: 'Colorado, Sixth Principal'},
            {value: 'UTCO', label: 'Colorado, Ute'},
            {value: 'B-ID', label: 'Idaho, Boise'},
            {value: 'SPKS', label: 'Kansas, Sixth Principal'},
            {value: 'F-MO', label: 'Missouri, Fifth Principal'},
            {value: 'P-MT', label: 'Montana, Principal'},
            {value: 'SPNE', label: 'Nebraska, Sixth Principal'},
            {value: 'M-NV', label: 'Nevada, Mt. Diablo'},
            {value: 'NMNM', label: 'New Mexico, New Mexico'},
            {value: 'F-ND', label: 'North Dakota, Fifth Principal'},
            {value: 'C-OK', label: 'Oklahoma, Cimarron'},
            {value: 'I-OK', label: 'Oklahoma, Indian'},
            {value: 'W-OR', label: 'Oregon, Willamette'},
            {value: 'BHSD', label: 'South Dakota, Black Hills'},
            {value: 'F-SD', label: 'South Dakota, Fifth Principal'},
            {value: 'SPSD', label: 'South Dakota, Sixth Principal'},
            {value: 'SLUT', label: 'Utah, Salt Lake'},
            {value: 'U-UT', label: 'Utah, Uinta'},
            {value: 'W-WA', label: 'Washington, Willamette'},
            {value: 'SPWY', label: 'Wyoming, Sixth Principal'},
            {value: 'WRWY', label: 'Wyoming, Wind River'}
        ];
        const trsMerideanValue = Vue.ref('--------------------------');
        const utmZoneValue = Vue.ref(null);
        const utmEastingValue = Vue.ref(null);
        const utmNorthingValue = Vue.ref(null);
        const utmHemisphereValue = Vue.ref('North');
        const weSelectorOptions = [
            'W', 'E'
        ];

        function closePopup() {
            context.emit('close:popup');
        }

        function transcribeDDMData() {
            if(latDDMDegreeValue.value && latDDMMinuteValue.value && longDDMDegreeValue.value && longDDMMinuteValue.value){
                if(!isNaN(latDDMDegreeValue.value) && !isNaN(latDDMMinuteValue.value) && !isNaN(longDDMDegreeValue.value) && !isNaN(longDDMMinuteValue.value)){
                    if(Number(latDDMDegreeValue.value) < 0 || Number(latDDMDegreeValue.value) > 90){
                        showNotification('negative', 'Lat degrees must be between 0 and 90.');
                    }
                    else if(Number(longDDMDegreeValue.value) < 0 || Number(longDDMDegreeValue.value) > 180){
                        showNotification('negative', 'Long degrees must be between 0 and 180.');
                    }
                    else if(Number(latDDMMinuteValue.value) < 0 || Number(latDDMMinuteValue.value) > 60 || Number(longDDMMinuteValue.value) < 0 || Number(longDDMMinuteValue.value) > 60){
                        showNotification('negative', 'Minute values can only be between 0 and 60.');
                    }
                    else{
                        returnData['verbatimCoordinates'] = '';
                        if(props.verbatimCoordinates && props.verbatimCoordinates !== ''){
                            returnData['verbatimCoordinates'] += props.verbatimCoordinates + '; ';
                        }
                        returnData['verbatimCoordinates'] += latDDMDegreeValue.value + '\u00B0 ' + latDDMMinuteValue.value + "' ";
                        returnData['verbatimCoordinates'] += latDDMNorthSouthValue.value + ',  ' + longDDMDegreeValue.value + '\u00B0 ' + longDDMMinuteValue.value + "' ";
                        returnData['verbatimCoordinates'] += longDDMWestEastValue.value;
                        let decimalLat = parseInt(latDDMDegreeValue.value) + (parseFloat(latDDMMinuteValue.value) / 60);
                        let decimalLong = parseInt(longDDMDegreeValue.value) + (parseFloat(longDDMMinuteValue.value) / 60);
                        if(latDDMNorthSouthValue.value === 'S') {
                            decimalLat *= -1;
                        }
                        if(longDDMWestEastValue.value === 'W') {
                            decimalLong *= -1;
                        }
                        returnData['decimalLatitude'] = decimalLat;
                        returnData['decimalLongitude'] = decimalLong;
                        context.emit('update:coordinate-tool-data', returnData);
                        closePopup();
                    }
                }
                else{
                    showNotification('negative', 'Degree and decimal minute values must all be numeric.');
                }
            }
            else{
                showNotification('negative', 'There must be degrees and decimal minutes values for both Lat and Long.');
            }
        }

        function transcribeDMSData() {
            if(latDegreeValue.value && latMinuteValue.value && longDegreeValue.value && longMinuteValue.value){
                if(!isNaN(latDegreeValue.value) && !isNaN(latMinuteValue.value) && !isNaN(latSecondValue.value) && !isNaN(longDegreeValue.value) && !isNaN(longMinuteValue.value) && !isNaN(longSecondValue.value)){
                    if(Number(latDegreeValue.value) < 0 || Number(latDegreeValue.value) > 90){
                        showNotification('negative', 'Lat degrees must be between 0 and 90.');
                    }
                    else if(Number(longDegreeValue.value) < 0 || Number(longDegreeValue.value) > 180){
                        showNotification('negative', 'Long degrees must be between 0 and 180.');
                    }
                    else if(Number(latMinuteValue.value) < 0 || Number(latMinuteValue.value) > 60 || Number(latSecondValue.value) < 0 || Number(latSecondValue.value) > 60 || Number(longMinuteValue.value) < 0 || Number(longMinuteValue.value) > 60 || Number(longSecondValue.value) < 0 || Number(longSecondValue.value) > 60){
                        showNotification('negative', 'Minute and second values can only be between 0 and 60.');
                    }
                    else{
                        returnData['verbatimCoordinates'] = '';
                        if(props.verbatimCoordinates && props.verbatimCoordinates !== ''){
                            returnData['verbatimCoordinates'] += props.verbatimCoordinates + '; ';
                        }
                        returnData['verbatimCoordinates'] += latDegreeValue.value + '\u00B0 ' + latMinuteValue.value + "' ";
                        if(latSecondValue.value){
                            returnData['verbatimCoordinates'] += latSecondValue.value + '" ';
                        }
                        returnData['verbatimCoordinates'] += latNorthSouthValue.value + ',  ' + longDegreeValue.value + '\u00B0 ' + longMinuteValue.value + "' ";
                        if(longSecondValue.value){
                            returnData['verbatimCoordinates'] += longSecondValue.value + '" ';
                        }
                        returnData['verbatimCoordinates'] += longWestEastValue.value;
                        let decimalLat = parseInt(latDegreeValue.value) + (parseFloat(latMinuteValue.value) / 60) + (parseFloat(latSecondValue.value) / 3600);
                        let decimalLong = parseInt(longDegreeValue.value) + (parseFloat(longMinuteValue.value) / 60) + (parseFloat(longSecondValue.value) / 3600);
                        if(latNorthSouthValue.value === 'S') {
                            decimalLat *= -1;
                        }
                        if(longWestEastValue.value === 'W') {
                            decimalLong *= -1;
                        }
                        returnData['decimalLatitude'] = decimalLat;
                        returnData['decimalLongitude'] = decimalLong;
                        context.emit('update:coordinate-tool-data', returnData);
                        closePopup();
                    }
                }
                else{
                    showNotification('negative', 'Degree, minute, and second values must all be numeric.');
                }
            }
            else{
                showNotification('negative', 'There must be degree and minute values for both Lat and Long.');
            }
        }

        function transcribeTRSData() {
            if(!trsTownshipValue.value || !trsRangeValue.value){
                showNotification('negative', 'Township and Range fields must have values.');
            }
            else if(isNaN(trsTownshipValue.value) || isNaN(trsRangeValue.value) || isNaN(trsSectionValue.value)){
                showNotification('negative', 'Township, Range, and Section values must be numeric. If a non-standard format is being used, enter it directly into the Verbatim Coordinates field.');
            }
            else if(Number(trsSectionValue.value) < 1 || Number(trsSectionValue.value) > 36){
                showNotification('negative', 'Section value must be between 1-36.');
            }
            else{
                returnData['verbatimCoordinates'] = '';
                if(props.verbatimCoordinates && props.verbatimCoordinates !== ''){
                    returnData['verbatimCoordinates'] += props.verbatimCoordinates + '; ';
                }
                returnData['verbatimCoordinates'] += 'TRS: T' + trsTownshipValue.value + trsTownshipNorthSouthValue.value + ' R' + trsRangeValue.value + trsRangeWestEastValue.value;
                if(trsSectionValue.value){
                    returnData['verbatimCoordinates'] += ' sec ' + trsSectionValue.value;
                }
                if(trsDetailsValue.value){
                    returnData['verbatimCoordinates'] += ' ' + trsDetailsValue.value;
                }
                if(trsMerideanValue.value && trsMerideanValue.value !== '--------------------------'){
                    returnData['verbatimCoordinates'] += ' ' + trsMerideanValue.value;
                }
                context.emit('update:coordinate-tool-data', returnData);
                closePopup();
            }
        }

        function transcribeUTMData() {
            if(utmZoneValue.value && utmEastingValue.value && utmNorthingValue.value){
                if(!isNaN(utmEastingValue.value) && !isNaN(utmNorthingValue.value)){
                    returnData['verbatimCoordinates'] = '';
                    if(props.verbatimCoordinates && props.verbatimCoordinates !== ''){
                        returnData['verbatimCoordinates'] += props.verbatimCoordinates + '; ';
                    }
                    returnData['verbatimCoordinates'] += utmZoneValue.value + ' ' + utmEastingValue.value + 'E ' + utmNorthingValue.value + 'N';
                    if(!isNaN(utmZoneValue.value)){
                        const zNum = parseInt(utmZoneValue.value);
                        const latLngData = convertUtmToDecimalDegrees(zNum, utmEastingValue.value, utmNorthingValue.value, props.geodeticDatum);
                        if(latLngData){
                            const latFact = utmHemisphereValue.value === 'North' ? 1 : -1;
                            returnData['decimalLatitude'] = latFact * Math.round(latLngData['lat'] * 1000000) / 1000000;
                            returnData['decimalLongitude'] = Math.round(latLngData['long'] * 1000000) / 1000000;
                        }
                    }
                    context.emit('update:coordinate-tool-data', returnData);
                    closePopup();
                }
                else{
                    showNotification('negative', 'Easting and Northing fields must have numeric values only.');
                }
            }
            else{
                showNotification('negative', 'Zone, Easting, and Northing fields must not be empty.');
            }
        }

        return {
            latDDMDegreeValue,
            latDDMMinuteValue,
            latDDMNorthSouthValue,
            latDegreeValue,
            latMinuteValue,
            latSecondValue,
            latNorthSouthValue,
            longDDMDegreeValue,
            longDDMMinuteValue,
            longDDMWestEastValue,
            longDegreeValue,
            longMinuteValue,
            longSecondValue,
            longWestEastValue,
            northSouthSelectorOptions,
            nsSelectorOptions,
            tab,
            trsTownshipValue,
            trsTownshipNorthSouthValue,
            trsRangeValue,
            trsRangeWestEastValue,
            trsSectionValue,
            trsDetailsValue,
            trsMerideanOptions,
            trsMerideanValue,
            utmZoneValue,
            utmEastingValue,
            utmNorthingValue,
            utmHemisphereValue,
            weSelectorOptions,
            closePopup,
            transcribeDDMData,
            transcribeDMSData,
            transcribeTRSData,
            transcribeUTMData
        }
    }
};
const occurrenceVerbatimCoordinatesInputElement = {
    props: {
        definition: {
            type: Object,
            default: null
        },
        decimalLatitude: {
            type: Number,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        geodeticDatum: {
            type: String,
            default: null
        },
        label: {
            type: String,
            default: ''
        },
        maxlength: {
            type: Number,
            default: null
        },
        showCounter: {
            type: Boolean,
            default: true
        },
        tabindex: {
            type: Number,
            default: 0
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <template v-if="!disabled && maxlength && Number(maxlength) > 0">
            <q-input outlined v-model="value" :label="label" bg-color="white" :maxlength="maxlength" @update:model-value="processValueChange" :tabindex="tabindex" dense>
                <template v-if="value || definition" v-slot:append>
                    <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            See field definition
                        </q-tooltip>
                    </q-icon>
                    <q-icon role="button" v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                    <q-icon role="button" v-if="value" name="calculate" class="cursor-pointer" @click="parseDecimalCoordinates();" @keyup.enter="parseDecimalCoordinates();" aria-label="Recalculate decimal coordinates" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Recalculate decimal coordinates
                        </q-tooltip>
                    </q-icon>
                    <q-icon role="button" name="construction" class="cursor-pointer" aria-label="Open Tools for converting additional formats" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Open Tools for converting additional formats
                        </q-tooltip>
                        <q-menu v-model="displayCoordinateToolPopup" anchor="bottom end" self="top left" cover transition-show="scale" transition-hide="scale" class="z-max">
                            <occurrence-coordinate-tool-popup
                                :geodetic-datum="geodeticDatum"
                                :verbatim-coordinates="value"
                                @close:popup="displayCoordinateToolPopup = false"
                                @update:coordinate-tool-data="processCoordinateToolData"
                            ></occurrence-coordinate-tool-popup>
                        </q-menu>
                    </q-icon>
                </template>
            </q-input>
        </template>
        <template v-else>
            <q-input outlined v-model="value" :label="label" bg-color="white" @update:model-value="processValueChange" :readonly="disabled" :tabindex="tabindex" dense>
                <template v-if="!disabled && (value || definition)" v-slot:append>
                    <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            See field definition
                        </q-tooltip>
                    </q-icon>
                    <q-icon role="button" v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                    <q-icon role="button" v-if="value" name="calculate" class="cursor-pointer" @click="parseDecimalCoordinates();" @keyup.enter="parseDecimalCoordinates();" aria-label="Recalculate decimal coordinates" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Recalculate decimal coordinates
                        </q-tooltip>
                    </q-icon>
                    <q-icon role="button" name="construction" class="cursor-pointer" aria-label="Open Tools for converting additional formats" :tabindex="tabindex">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Open Tools for converting additional formats
                        </q-tooltip>
                        <q-menu v-model="displayCoordinateToolPopup" anchor="bottom end" self="top left" cover transition-show="scale" transition-hide="scale" class="z-max">
                            <occurrence-coordinate-tool-popup
                                :geodetic-datum="geodeticDatum"
                                :verbatim-coordinates="value"
                                @close:popup="displayCoordinateToolPopup = false"
                                @update:coordinate-tool-data="processCoordinateToolData"
                            ></occurrence-coordinate-tool-popup>
                        </q-menu>
                    </q-icon>
                </template>
            </q-input>
        </template>
        <template v-if="definition">
            <q-dialog class="z-top" v-model="displayDefinitionPopup" persistent aria-label="Definition pop up">
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="displayDefinitionPopup = false" aria-label="Close definition pop up" :tabindex="tabindex"></q-btn>
                        </div>
                    </div>
                    <div class="q-pa-sm column q-gutter-sm">
                        <div class="text-h6">{{ label }}</div>
                        <template v-if="definition.definition">
                            <div>
                                <span class="text-bold">Definition: </span>{{ definition.definition }}
                            </div>
                        </template>
                        <template v-if="definition.comments">
                            <div>
                                <span class="text-bold">Comments: </span>{{ definition.comments }}
                            </div>
                        </template>
                        <template v-if="definition.examples">
                            <div>
                                <span class="text-bold">Examples: </span>{{ definition.examples }}
                            </div>
                        </template>
                        <template v-if="definition.source">
                            <div>
                                <a :href="definition.source" target="_blank" aria-label="External link: Go to source - Opens in separate tab" :tabindex="tabindex"><span class="text-bold">Go to source</span></a>
                            </div>
                        </template>
                    </div>
                </q-card>
            </q-dialog>
        </template>
    `,
    components: {
        'occurrence-coordinate-tool-popup': occurrenceCoordinateToolPopup
    },
    setup(props, context) {
        const { convertUtmToDecimalDegrees, showNotification } = useCore();

        const displayCoordinateToolPopup = Vue.ref(false);
        const displayDefinitionPopup = Vue.ref(false);

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function parseDecimalCoordinates(verbose = true){
            let lngDeg;
            let lngMin;
            let latDeg;
            let latMin;
            if(props.value){
                const zoneEx = /^\D?(\d{1,2})\D*$/;
                const eEx1 = /^(\d{6,7})E/i;
                const nEx1 = /^(\d{7})N/i;
                const eEx2 = /^E(\d{6,7})\D*$/i;
                const nEx2 = /^N(\d{4,7})\D*$/i;
                const eEx3 = /^0?(\d{6})\D*$/i;
                const nEx3 = /^(\d{7})\D*$/i;
                let latDec = null;
                let lngDec = null;
                let z = null;
                let e = null;
                let n = null;
                let extractArr = [];
                let verbCoordStr = props.value.replaceAll(/’/g,"'");
                const tokenArr = verbCoordStr.split(" ");
                for(let i = 0; i < tokenArr.length; i++){
                    if(zoneEx.exec(tokenArr[i])){
                        extractArr = zoneEx.exec(tokenArr[i]);
                        z = extractArr[1];
                    }
                    else if(eEx1.exec(tokenArr[i])){
                        extractArr = eEx1.exec(tokenArr[i]);
                        e = extractArr[1];
                    }
                    else if(nEx1.exec(tokenArr[i])){
                        extractArr = nEx1.exec(tokenArr[i]);
                        n = extractArr[1];
                    }
                    else if(eEx2.exec(tokenArr[i])){
                        extractArr = eEx2.exec(tokenArr[i]);
                        e = extractArr[1];
                    }
                    else if(nEx2.exec(tokenArr[i])){
                        extractArr = nEx2.exec(tokenArr[i]);
                        n = extractArr[1];
                    }
                    else if(eEx3.exec(tokenArr[i])){
                        extractArr = eEx3.exec(tokenArr[i]);
                        e = extractArr[1];
                    }
                    else if(nEx3.exec(tokenArr[i])){
                        extractArr = nEx3.exec(tokenArr[i]);
                        n = extractArr[1];
                    }
                }
                if(z && e && n){
                    const latLngData = convertUtmToDecimalDegrees(z, e, n, props.geodeticDatum);
                    if(latLngData){
                        latDec = Math.round(latLngData['lat'] * 1000000) / 1000000;
                        lngDec = Math.round(latLngData['long'] * 1000000) / 1000000;
                    }
                }
                if(!latDec || !lngDec){
                    const llEx1 = /(\d{1,2})[\D\s]{1,2}(\d{1,2}\.?\d*)['m]\s*(\d{1,2}\.?\d*)['"s]{1,2}\s*([NS]?)[\D\s]*(\d{1,3})[\D\s]{1,2}(\d{1,2}\.?\d*)['m]\s*(\d{1,2}\.?\d*)['"s]{1,2}\s*([EW]?)/i;
                    const llEx2 = /(\d{1,2})[\D\s]{1,2}(\d{1,2}\.?\d*)['m]\s*([NS]?)\D*\s*(\d{1,3})[\D\s]{1,2}(\d{1,2}\.?\d*)['m]\s*([EW]?)/i;
                    if(llEx1.exec(verbCoordStr)){
                        extractArr = llEx1.exec(verbCoordStr);
                        latDeg = parseInt(extractArr[1]);
                        latMin = parseInt(extractArr[2]);
                        const latSec = parseFloat(extractArr[3]);
                        lngDeg = parseInt(extractArr[5]);
                        lngMin = parseInt(extractArr[6]);
                        const lngSec = parseFloat(extractArr[7]);
                        if(latDeg > 90){
                            if(verbose){
                                showNotification('negative', 'Latitude degrees cannot be greater than 90.');
                            }
                        }
                        else if(lngDeg > 180){
                            if(verbose){
                                showNotification('negative', 'Longitude degrees cannot be greater than 180.');
                            }
                        }
                        else if(latMin > 60 || latSec > 60 || lngMin > 60 || lngSec > 60){
                            if(verbose){
                                showNotification('negative', 'Minutes and seconds values for latitude and longitude cannot be greater than 60.');
                            }
                        }
                        else{
                            latDec = latDeg + (latMin / 60) + (latSec / 3600);
                            lngDec = lngDeg + (lngMin / 60) + (lngSec / 3600);
                            if((extractArr[4] === "S" || extractArr[4] === "s") && latDec > 0) {
                                latDec *= -1;
                            }
                            if(lngDec > 0 && extractArr[8] !== "E" && extractArr[8] !== "e") {
                                lngDec *= -1;
                            }
                        }
                    }
                    else if(llEx2.exec(verbCoordStr)){
                        extractArr = llEx2.exec(verbCoordStr);
                        latDeg = parseInt(extractArr[1]);
                        latMin = parseFloat(extractArr[2]);
                        lngDeg = parseInt(extractArr[4]);
                        lngMin = parseFloat(extractArr[5]);
                        if(latDeg > 90){
                            if(verbose){
                                showNotification('negative', 'Latitude degrees cannot be greater than 90.');
                            }
                        }
                        else if(lngDeg > 180){
                            if(verbose){
                                showNotification('negative', 'Longitude degrees cannot be greater than 180.');
                            }
                        }
                        else if(latMin > 60 || lngMin > 60){
                            if(verbose){
                                showNotification('negative', 'Minutes values for latitude and longitude cannot be greater than 60.');
                            }
                        }
                        else{
                            latDec = latDeg + (latMin / 60);
                            lngDec = lngDeg + (lngMin / 60);
                            if((extractArr[3] === "S" || extractArr[3] === "s") && latDec > 0) {
                                latDec *= -1;
                            }
                            if(lngDec > 0 && extractArr[6] !== "E" && extractArr[6] !== "e") {
                                lngDec *= -1;
                            }
                        }
                    }
                }
                if(latDec && lngDec){
                    context.emit('update:decimal-coordinates', {
                        decimalLatitude: (Math.round(latDec * 1000000) / 1000000),
                        decimalLongitude: (Math.round(lngDec * 1000000) / 1000000)
                    });
                }
                else{
                    if(verbose){
                        showNotification('negative', 'Unable to calculate decimal coordinates.');
                    }
                }
            }
            else{
                if(verbose){
                    showNotification('negative', 'Verbatim Coordinates must have a value to recalculate.');
                }
            }
        }

        function processCoordinateToolData(data) {
            if(data.decimalLatitude && data.decimalLongitude){
                context.emit('update:decimal-coordinates', {
                    decimalLatitude: (Math.round(data['decimalLatitude'] * 1000000) / 1000000),
                    decimalLongitude: (Math.round(data['decimalLongitude'] * 1000000) / 1000000)
                });
            }
            if(data.verbatimCoordinates){
                context.emit('update:value', data['verbatimCoordinates']);
            }
        }

        function processValueChange(val) {
            context.emit('update:value', val);
            if(val && !props.decimalLatitude){
                parseDecimalCoordinates(false);
            }
        }

        return {
            displayCoordinateToolPopup,
            displayDefinitionPopup,
            openDefinitionPopup,
            parseDecimalCoordinates,
            processCoordinateToolData,
            processValueChange
        }
    }
};
