const textFieldInputElement = {
    props: {
        dataType: {
            type: String,
            default: 'text'
        },
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
        minValue: {
            type: Number,
            default: null
        },
        maxlength: {
            type: Number,
            default: null
        },
        maxValue: {
            type: Number,
            default: null
        },
        showCounter: {
            type: Boolean,
            default: true
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <template v-if="!disabled && maxlength && Number(maxlength) > 0">
            <q-input outlined v-model="value" :type="inputType" :label="label" :counter="(showCounter && dataType !== 'int' && dataType !== 'number')" :maxlength="maxlength" @update:model-value="processValueChange" :autogrow="inputType === 'textarea'" dense>
                <template v-if="definition" v-slot:append>
                    <q-icon v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                    <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            See field definition
                        </q-tooltip>
                    </q-icon>
                </template>
                <template v-else-if="value" v-slot:append>
                    <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                </template>
            </q-input>
        </template>
        <template v-else>
            <q-input outlined v-model="value" :type="inputType" :label="label" @update:model-value="processValueChange" :readonly="disabled" :autogrow="inputType === 'textarea'" dense>
                <template v-if="!disabled && definition" v-slot:append>
                    <q-icon v-if="value" name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                    <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            See field definition
                        </q-tooltip>
                    </q-icon>
                </template>
                <template v-else-if="!disabled && value" v-slot:append>
                    <q-icon name="cancel" class="cursor-pointer" @click="processValueChange(null);">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
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
        const inputType = Vue.ref('text');

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(val) {
            if(props.dataType === 'int' || props.dataType === 'number'){
                if(val && isNaN(val)){
                    showNotification('negative', (props.label + ' must be a number.'));
                }
                else if(val && props.minValue && Number(props.minValue) > Number(val)){
                    showNotification('negative', (props.label + ' cannot be less than ' + props.minValue + '.'));
                    if(props.dataType === 'int'){
                        context.emit('update:value', props.minValue);
                    }
                }
                else if(val && props.maxValue && Number(props.maxValue) < Number(val)){
                    showNotification('negative', (props.label + ' cannot be greater than ' + props.maxValue + '.'));
                    if(props.dataType === 'int'){
                        context.emit('update:value', props.maxValue);
                    }
                }
                else{
                    context.emit('update:value', val);
                }
            }
            else{
                context.emit('update:value', val);
            }
        }

        Vue.onMounted(() => {
            if(props.dataType === 'int'){
                inputType.value = 'number';
            }
            else if(props.dataType !== 'text' && props.dataType !== 'number'){
                inputType.value = props.dataType;
            }
        });

        return {
            displayDefinitionPopup,
            inputType,
            openDefinitionPopup,
            processValueChange
        }
    }
};
