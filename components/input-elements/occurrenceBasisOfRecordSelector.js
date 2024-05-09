const occurrenceBasisOfRecordSelector = {
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
            default: 'Basis of Record'
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <q-select v-model="value" outlined dense options-dense input-debounce="0" popup-content-class="z-max" :options="selectorOptions" option-value="value" option-label="label" @update:model-value="processValueChange" :label="label" :disable="disabled">
            <template v-if="!disabled && definition" v-slot:after>
                <q-icon name="help" class="cursor-pointer" @click="openDefinitionPopup();">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        See field definition
                    </q-tooltip>
                </q-icon>
            </template>
        </q-select>
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
        const displayDefinitionPopup = Vue.ref(false);
        const propsRefs = Vue.toRefs(props);
        const selectedOption = Vue.ref(null);
        const selectorOptions = [
            {value: 'PreservedSpecimen', label: 'Preserved Specimen'},
            {value: 'HumanObservation', label: 'Observation'},
            {value: 'FossilSpecimen', label: 'Fossil Specimen'},
            {value: 'LivingSpecimen', label: 'Living Specimen'},
            {value: 'MaterialSample', label: 'Material Sample'}
        ];

        Vue.watch(propsRefs.value, () => {
            setSelectedOption();
        });

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(selectedObj) {
            context.emit('update:value', (selectedObj ? selectedObj.value : null));
        }

        function setSelectedOption() {
            selectedOption.value = selectorOptions.find(opt => opt['value'] === props.value);
        }

        Vue.onMounted(() => {
            setSelectedOption();
        });

        return {
            displayDefinitionPopup,
            selectedOption,
            selectorOptions,
            openDefinitionPopup,
            processValueChange
        }
    }
};
