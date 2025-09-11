const occurrenceVerbatimElevationInputElement = {
    props: {
        definition: {
            type: Object,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: ''
        },
        maxlength: {
            type: Number,
            default: null
        },
        minimumElevationInMeters: {
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
            <q-input outlined v-model="value" :label="label" :maxlength="maxlength" bg-color="white" @update:model-value="processValueChange" :tabindex="tabindex" dense>
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
                    <q-icon v-if="value" name="calculate" class="cursor-pointer" @click="parseElevationValues();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Recalculate minimum and maximum elevation values
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
                    <q-icon v-if="value" name="calculate" class="cursor-pointer" @click="parseElevationValues();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Recalculate minimum and maximum elevation values
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
        const { showNotification } = useCore();

        const displayDefinitionPopup = Vue.ref(false);

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function parseElevationValues(verbose = true){
            if(props.value){
                const regEx1 = /(\d+)\s*-\s*(\d+)\s*[fte|']/i;
                const regEx2 = /(\d+)\s*[fte|']/i;
                const regEx3 = /(\d+)\s*-\s*(\d+)\s?m/i;
                const regEx4 = /(\d+)\s?-\s?(\d+)\s?m/i;
                const regEx5 = /(\d+)\s?m/i;
                let min, max;
                let extractArr = [];
                let verbElevStr = props.value.replaceAll(/,/g ,"");
                if(regEx1.exec(verbElevStr)){
                    extractArr = regEx1.exec(verbElevStr);
                    min = Math.round(extractArr[1] * .3048);
                    max = Math.round(extractArr[2] * .3048);
                }
                else if(regEx2.exec(verbElevStr)){
                    extractArr = regEx2.exec(verbElevStr);
                    min = Math.round(extractArr[1] * .3048);
                }
                else if(regEx3.exec(verbElevStr)){
                    extractArr = regEx3.exec(verbElevStr);
                    min = extractArr[1];
                    max = extractArr[2];
                }
                else if(regEx4.exec(verbElevStr)){
                    extractArr = regEx4.exec(verbElevStr);
                    min = extractArr[1];
                    max = extractArr[2];
                }
                else if(regEx5.exec(verbElevStr)){
                    extractArr = regEx5.exec(verbElevStr);
                    min = extractArr[1];
                }
                if(min){
                    const returnData = {};
                    returnData['minimumElevationInMeters'] = min;
                    if(max){
                        returnData['maximumElevationInMeters'] = max;
                    }
                    context.emit('update:elevation-values', returnData);
                }
            }
            else{
                if(verbose){
                    showNotification('negative', 'Verbatim Elevation must have a value to recalculate.');
                }
            }
        }

        function processValueChange(val) {
            context.emit('update:value', val);
            if(val && !props.minimumElevationInMeters){
                parseElevationValues(false);
            }
        }

        return {
            displayDefinitionPopup,
            openDefinitionPopup,
            parseElevationValues,
            processValueChange
        }
    }
};
