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
            default: 1
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
                    <q-icon v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            See field definition
                        </q-tooltip>
                    </q-icon>
                    <q-icon v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                    <q-icon v-if="value" name="calculate" class="cursor-pointer" @click="parseDecimalCoordinates();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Recalculate decimal coordinates
                        </q-tooltip>
                    </q-icon>
                </template>
            </q-input>
        </template>
        <template v-else>
            <q-input outlined v-model="value" :label="label" bg-color="white" @update:model-value="processValueChange" :readonly="disabled" :tabindex="tabindex" dense>
                <template v-if="!disabled && (value || definition)" v-slot:append>
                    <q-icon v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            See field definition
                        </q-tooltip>
                    </q-icon>
                    <q-icon v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                    <q-icon v-if="value" name="calculate" class="cursor-pointer" @click="parseDecimalCoordinates();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Recalculate decimal coordinates
                        </q-tooltip>
                    </q-icon>
                </template>
            </q-input>
        </template>
        <template v-if="definition">
            <q-dialog class="z-top" v-model="displayDefinitionPopup" persistent>
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="displayDefinitionPopup = false"></q-btn>
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
                                <a :href="definition.source" target="_blank"><span class="text-bold">Go to source</span></a>
                            </div>
                        </template>
                    </div>
                </q-card>
            </q-dialog>
        </template>
    `,
    setup(props, context) {
        const { convertUtmToDecimalDegrees, showNotification } = useCore();

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
                                latDec = latDec * -1;
                            }
                            if(lngDec > 0 && extractArr[8] !== "E" && extractArr[8] !== "e") {
                                lngDec = lngDec * -1;
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
                                latDec = latDec * -1;
                            }
                            if(lngDec > 0 && extractArr[6] !== "E" && extractArr[6] !== "e") {
                                lngDec = lngDec * -1;
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

        function processValueChange(val) {
            context.emit('update:value', val);
            if(val && !props.decimalLatitude){
                parseDecimalCoordinates(false);
            }
        }

        return {
            displayDefinitionPopup,
            openDefinitionPopup,
            parseDecimalCoordinates,
            processValueChange
        }
    }
};
