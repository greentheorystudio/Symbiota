const dateInputElement = {
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
            default: null
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <q-input outlined v-model="value" :label="label" debounce="2000" bg-color="white" @update:model-value="processValueChange" :readonly="disabled" dense>
            <template v-if="!disabled" v-slot:append>
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
                <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale" class="z-max">
                        <q-date v-model="value" mask="YYYY-MM-DD" @update:model-value="processValueChange" minimal>
                            <div class="row items-center justify-end">
                                <q-btn v-close-popup label="Close" color="primary" flat></q-btn>
                            </div>
                        </q-date>
                    </q-popup-proxy>
                </q-icon>
            </template>
        </q-input>
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
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="q-dialog-plugin q-pa-lg">
                {{ popupText }}
                <q-card-actions align="right">
                    <q-btn color="primary" label="Yes" @click="emitValue" />
                    <q-btn color="primary" label="Cancel" @click="showPopup = false" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const { parseDate, showNotification } = useCore();

        const dateData = Vue.ref(null);
        const displayDefinitionPopup = Vue.ref(false);
        const popupText = Vue.ref(null);
        const showPopup = Vue.ref(false);

        function emitValue() {
            context.emit('update:value', dateData.value);
            showPopup.value = false;
        }

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(value) {
            dateData.value = Object.assign({}, parseDate(value));
            if(value){
                if(!dateData.value['year']){
                    showNotification('negative', 'Unable to interpret the date entered. Please use one of the following formats: yyyy-mm-dd, mm/dd/yyyy, or dd mmm yyyy.');
                }
                else if(dateData.value['month'] && dateData.value['day']){
                    const testDate = new Date(dateData.value['year'], (dateData.value['month'] - 1), dateData.value['day']);
                    const today = new Date();
                    const diffYears = Math.abs(Math.round((((today.getTime() - testDate.getTime()) / 1000) / (60 * 60 * 24)) / 365.25));
                    if(testDate > today){
                        showNotification('negative', 'Date cannot be in the future.');
                    }
                    else if(diffYears > 99){
                        popupText.value = 'That date is ' + diffYears.toString() + ' years ago, are you sure it\'s correct?';
                        showPopup.value = true;
                    }
                    else{
                        emitValue();
                    }
                }
            }
        }

        return {
            displayDefinitionPopup,
            popupText,
            showPopup,
            emitValue,
            openDefinitionPopup,
            processValueChange
        }
    }
};
