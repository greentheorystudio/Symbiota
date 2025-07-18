const timeInputElement = {
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
        <q-input outlined v-model="displayValue" mask="time" :label="label" debounce="2000" bg-color="white" @update:model-value="processValueChange" :readonly="disabled" dense>
            <template v-if="!disabled" v-slot:append>
                <q-icon v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        See field definition
                    </q-tooltip>
                </q-icon>
                <q-icon v-if="displayValue" name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
                <q-icon name="access_time" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale" class="z-max">
                        <q-time v-model="displayValue" @update:model-value="processValueChange" format24h>
                            <div class="row items-center justify-end">
                                <q-btn v-close-popup label="Close" color="primary" flat></q-btn>
                            </div>
                        </q-time>
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
    `,
    setup(props, context) {
        const { showNotification } = useCore();

        const displayDefinitionPopup = Vue.ref(false);
        const displayValue = Vue.computed(() => {
            let returnStr = null;
            if(props.value){
                returnStr = props.value;
                if(props.value.toString() !== '' && !props.value.toString().includes(':') && Number(props.value) > 0){
                    const time = new Date(props.value);
                    const seconds = time.getUTCSeconds().toString().padStart(2, '0');
                    const minutes = time.getUTCMinutes().toString().padStart(2, '0');
                    const hours = time.getUTCHours().toString().padStart(2, '0');
                    if(Number(hours) > 0 || Number(minutes) > 0 || Number(seconds) > 0){
                        returnStr = hours.toString() + ':' + minutes.toString() + ':' + seconds.toString();
                    }
                }
            }
            return returnStr;
        });

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(value) {
            if(value){
                if((value + ':00') !== displayValue.value){
                    let timeTokens = value.split(':');
                    if(Number(timeTokens[0]) >= 0 && Number(timeTokens[0]) <= 23 && Number(timeTokens[1]) >= 0 && Number(timeTokens[1]) <= 59){
                        context.emit('update:value', value);
                    }
                    else{
                        showNotification('negative', 'Time value must be a valid time.');
                    }
                }
            }
            else{
                context.emit('update:value', null);
            }
        }

        return {
            displayDefinitionPopup,
            displayValue,
            openDefinitionPopup,
            processValueChange
        }
    }
};
