const jsonFieldInputElement = {
    props: {
        clearable: {
            type: Boolean,
            default: true
        },
        definition: {
            type: Object,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        field: {
            type: String,
            default: ''
        },
        label: {
            type: String,
            default: ''
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
        <q-input outlined v-model="jsonValue" type="textarea" :label="label" bg-color="white" @blur="validateJsonValue" @update:model-value="processValueChange" :readonly="disabled" :autogrow="true" :tabindex="tabindex" :name="field" :autocomplete="field" dense>
            <template v-if="!disabled && (jsonValue || definition)" v-slot:append>
                <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        See field definition
                    </q-tooltip>
                </q-icon>
                <q-icon role="button" v-if="jsonValue" name="check_circle" class="cursor-pointer" @click="validateJsonValue();" @keyup.enter="processValueChange(null);" aria-label="Validate JSON" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Validate JSON
                    </q-tooltip>
                </q-icon>
                <q-icon role="button" v-if="jsonValue && clearable" name="cancel" class="cursor-pointer" @click="clearValue();" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
            </template>
        </q-input>
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
    setup(props, context) {
        const { showNotification } = useCore();

        const displayDefinitionPopup = Vue.ref(false);
        const jsonValue = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);

        Vue.watch(propsRefs.value, () => {
            jsonValue.value = props.value;
        });

        function clearValue() {
            context.emit('update:value', null);
        }

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(val) {
            jsonValue.value = val;
        }

        function validateJsonValue() {
            if(jsonValue.value && jsonValue.value !== ''){
                try{
                    const parsedValue = JSON.parse(jsonValue.value);
                    context.emit('update:value', JSON.stringify(parsedValue));
                }
                catch(error){
                    showNotification('negative', 'JSON is not valid.');
                }
            }
            else{
                context.emit('update:value', null);
            }
        }

        Vue.onMounted(() => {
            jsonValue.value = props.value;
        });

        return {
            displayDefinitionPopup,
            jsonValue,
            clearValue,
            openDefinitionPopup,
            processValueChange,
            validateJsonValue
        }
    }
};
