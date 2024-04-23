const occurrenceCoordinateToolPopup = {
    props: {
        geodeticDatum: {
            type: String,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        verbatimCoordinates: {
            type: String,
            default: null
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div class="q-px-sm q-mt-xl row items-center justify-between q-col-gutter-sm">
                    <div class="col-5">
                        <q-card flat bordered class="black-border">
                            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                                <div class="row justify-between q-col-gutter-xs">
                                    <div class="self-center text-bold">
                                        Lat:
                                    </div>
                                    <div class="col self-center">
                                        <q-input outlined v-model="latDegreeValue" label="Degrees" dense>
                                    </div>
                                    <div class="col self-center">
                                        <q-input outlined v-model="latMinuteValue" label="Minutes" dense>
                                    </div>
                                    <div class="col self-center">
                                        <q-input outlined v-model="latSecondValue" label="Seconds" dense>
                                    </div>
                                    <div class="self-center">
                                        <q-select bg-color="white" outlined v-model="latNorthSouthValue" :options="nsSelectorOptions" popup-content-class="z-max" dense options-dense />
                                    </div>
                                </div>
                                <div class="row justify-between q-col-gutter-xs">
                                    <div class="self-center text-bold">
                                        Long:
                                    </div>
                                    <div class="col self-center">
                                        <q-input outlined v-model="longDegreeValue" label="Degrees" dense>
                                    </div>
                                    <div class="col self-center">
                                        <q-input outlined v-model="longMinuteValue" label="Minutes" dense>
                                    </div>
                                    <div class="col self-center">
                                        <q-input outlined v-model="longSecondValue" label="Seconds" dense>
                                    </div>
                                    <div class="self-center">
                                        <q-select bg-color="white" outlined v-model="longWestEastValue" :options="weSelectorOptions" popup-content-class="z-max" dense options-dense />
                                    </div>
                                </div>
                                <div class="q-mt-md row justify-end">
                                    <q-btn color="grey-4" text-color="black" class="black-border" @click="transcribeDMSData();" label="Insert Lat/Long Values" dense></q-btn>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-3">
                        <q-card flat bordered class="black-border">
                            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                                <div>
                                    <q-input outlined v-model="utmZoneValue" label="UTM Zone" dense>
                                </div>
                                <div>
                                    <q-input outlined v-model="utmEastingValue" label="UTM Easting" dense>
                                </div>
                                <div>
                                    <q-input outlined v-model="utmNorthingValue" label="UTM Northing" dense>
                                </div>
                                <div >
                                    <q-select bg-color="white" outlined v-model="utmHemisphereValue" :options="northSouthSelectorOptions" label="Hemisphere" popup-content-class="z-max" dense options-dense />
                                </div>
                                <div class="q-mt-md row justify-end">
                                    <q-btn color="grey-4" text-color="black" class="black-border" @click="transcribeUTMData();" label="Insert UTM Values" dense></q-btn>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div class="col-4">
                        <q-card flat bordered class="black-border">
                            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                                <div class="row justify-between q-col-gutter-xs">
                                    <div class="col">
                                        <q-input outlined v-model="trsTownshipValue" label="Township" dense>
                                    </div>
                                    <div class="col">
                                        <q-select bg-color="white" outlined v-model="trsTownshipNorthSouthValue" :options="nsSelectorOptions" popup-content-class="z-max" dense options-dense />
                                    </div>
                                    <div class="col">
                                        <q-input outlined v-model="trsRangeValue" label="Range" dense>
                                    </div>
                                    <div>
                                        <q-select bg-color="white" outlined v-model="trsRangeWestEastValue" :options="weSelectorOptions" popup-content-class="z-max" dense options-dense />
                                    </div>
                                </div>
                                <div class="row justify-between q-col-gutter-xs">
                                    <div class="col-3">
                                        <q-input outlined v-model="trsSectionValue" label="Section" dense>
                                    </div>
                                    <div class="col-9">
                                        <q-input outlined v-model="trsDetailsValue" label="Details" dense>
                                    </div>
                                </div>
                                <div class="row justify-between q-col-gutter-xs">
                                    <div class="col-12">
                                        <q-select bg-color="white" outlined v-model="trsMerideanValue" :options="trsMerideanOptions" :option-value="value" :option-label="label" label="Meridian Selection" popup-content-class="z-max" dense options-dense />
                                    </div>
                                </div>
                                <div class="q-mt-md row justify-end">
                                    <q-btn color="grey-4" text-color="black" class="black-border" @click="transcribeTRSData();" label="Insert TRS Values" dense></q-btn>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const { convertUtmToDecimalDegrees, showNotification } = useCore();

        const latDegreeValue = Vue.ref(null);
        const latMinuteValue = Vue.ref(null);
        const latSecondValue = Vue.ref(null);
        const latNorthSouthValue = Vue.ref('N');
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
                        showNotification('negative', 'Minutes and seconds values can only be between 0 and 60.');
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
                        returnData['verbatimCoordinates'] += latNorthSouthValue.value + '  ' + longDegreeValue.value + '\u00B0 ' + longMinuteValue.value + "' ";
                        if(longSecondValue.value){
                            returnData['verbatimCoordinates'] += longSecondValue.value + '" ';
                        }
                        returnData['verbatimCoordinates'] += longWestEastValue.value;
                        let decimalLat = parseInt(latDegreeValue.value) + (parseFloat(latMinuteValue.value) / 60) + (parseFloat(latSecondValue.value) / 3600);
                        let decimalLong = parseInt(longDegreeValue.value) + (parseFloat(longMinuteValue.value) / 60) + (parseFloat(longSecondValue.value) / 3600);
                        if(latNorthSouthValue.value === 'S') {
                            decimalLat = decimalLat * -1;
                        }
                        if(longWestEastValue.value === 'W') {
                            decimalLong = decimalLong * -1;
                        }
                        returnData['decimalLatitude'] = Math.round(decimalLat * 1000000) / 1000000;
                        returnData['decimalLongitude'] = Math.round(decimalLong * 1000000) / 1000000;
                        context.emit('update:coordinate-tool-data', returnData);
                    }
                }
                else{
                    showNotification('negative', 'Degrees, minutes, and seconds values must all be numeric.');
                }
            }
            else{
                showNotification('negative', 'There must be degrees and minutes values for both Lat and Long.');
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
                        const latLngData = utm2LatLng(zNum, utmEastingValue.value, utmNorthingValue.value, props.geodeticDatum);
                        if(latLngData){
                            const latFact = utmHemisphereValue.value === 'North' ? 1 : -1;
                            returnData['decimalLatitude'] = latFact * Math.round(latLngData['lat'] * 1000000) / 1000000;
                            returnData['decimalLongitude'] = Math.round(latLngData['long'] * 1000000) / 1000000;
                        }
                    }
                    context.emit('update:coordinate-tool-data', returnData);
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
            latDegreeValue,
            latMinuteValue,
            latSecondValue,
            latNorthSouthValue,
            longDegreeValue,
            longMinuteValue,
            longSecondValue,
            longWestEastValue,
            northSouthSelectorOptions,
            nsSelectorOptions,
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
            transcribeDMSData,
            transcribeTRSData,
            transcribeUTMData
        }
    }
};
