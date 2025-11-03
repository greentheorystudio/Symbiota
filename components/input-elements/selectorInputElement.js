const selectorInputElement = {
    props: {
        clearable: {
            type: Boolean,
            default: false
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
        optionLabel: {
            type: String,
            default: 'label'
        },
        options: {
            type: Array,
            default: []
        },
        optionValue: {
            type: String,
            default: 'value'
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
        <q-select ref="selectorRef" v-model="value" class="selector-input-spacer" outlined dense options-dense input-debounce="500" bg-color="white" popup-content-class="z-top" behavior="menu" input-class="z-top" :options="selectorOptions" option-value="value" option-label="label" @filter="checkFilter" @update:model-value="processValueChange" :label="label" :tabindex="tabindex" :disable="disabled">
            <template v-if="!disabled && (definition || (clearable && value))" v-slot:append>
                <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        See field definition
                    </q-tooltip>
                </q-icon>
                <q-icon role="button" v-if="clearable && value" name="cancel" class="cursor-pointer" @click="clearValue();" @keyup.enter="clearValue();" aria-label="Clear value" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
            </template>
            <template v-if="selectedOption || value" v-slot:selected>
                <template v-if="selectedOption && selectedOption.label">
                    {{ selectedOption.label.replaceAll(' ', '&nbsp;') }}
                </template>
                <template v-else>
                    {{ value.toString().replaceAll(' ', '&nbsp;') }}
                </template>
            </template>
        </q-select>
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
        const clearing = Vue.ref(false);
        const displayDefinitionPopup = Vue.ref(false);
        const propsRefs = Vue.toRefs(props);
        const selectedOption = Vue.ref(null);
        const selectorOptions = Vue.shallowReactive([]);
        const selectorRef = Vue.ref(null);

        Vue.watch(propsRefs.value, () => {
            setSelectedOption();
        });

        Vue.watch(propsRefs.options, () => {
            setOptions();
            setSelectedOption();
        });

        function checkFilter(input, proceed, abort) {
            if(displayDefinitionPopup.value || clearing.value){
                abort();
            }
            else{
                proceed();
            }
        }

        function clearValue() {
            clearing.value = true;
            processValueChange(null);
            setTimeout(() => {
                clearing.value = false;
            }, 500);
        }

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(selectedObj) {
            context.emit('update:value', (selectedObj ? selectedObj.value : null));
        }

        function setOptions() {
            selectorOptions.length = 0;
            if(props.options.length > 0){
                props.options.forEach(option => {
                    if(typeof option === 'string' || typeof option === 'number'){
                        selectorOptions.push({value: option.toString(), label: option.toString()});
                    }
                    else if(typeof option === 'object' && option.hasOwnProperty(props.optionValue) && option.hasOwnProperty(props.optionLabel)){
                        selectorOptions.push({value: option[props.optionValue].toString(), label: option[props.optionLabel].toString()});
                    }
                });
            }
        }

        function setSelectedOption() {
            if(props.value){
                selectedOption.value = selectorOptions.find(opt => opt['value'].toString() === props.value.toString());
            }
            else{
                selectedOption.value = null;
            }
        }

        Vue.onMounted(() => {
            setOptions();
            setSelectedOption();
        });

        return {
            displayDefinitionPopup,
            selectedOption,
            selectorOptions,
            selectorRef,
            checkFilter,
            clearValue,
            openDefinitionPopup,
            processValueChange
        }
    }
};
