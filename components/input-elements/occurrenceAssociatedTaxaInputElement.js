const occurrenceEditorAssociatedTaxaToolPopup = {
    props: {
        associatedTaxaValue: {
            type: String,
            default: null
        }
    },
    template: `
        <q-card class="input-tool-popup">
            <q-card-section class="column q-col-gutter-sm input-tool-popup z-max">
                <div>
                    <single-scientific-common-name-auto-complete :sciname="scientificNameStr" label="Scientific Name" @update:sciname="updateScientificNameValue" @click:enter="addTaxon"></single-scientific-common-name-auto-complete>
                </div>
                <div class="q-mt-md row justify-end q-gutter-sm">
                    <q-btn color="negative" @click="closePopup();" label="Close" dense></q-btn>
                    <q-btn color="primary" @click="addTaxon();" label="Add Taxon" dense />
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
    },
    setup(props, context) {
        const occurrenceStore = useOccurrenceStore();

        const associatedTaxaStr = Vue.ref(null);
        const propsRefs = Vue.toRefs(props);
        const scientificNameStr = Vue.ref(null);

        Vue.watch(propsRefs.associatedTaxaValue, () => {
            if(associatedTaxaStr.value !== props.associatedTaxaValue){
                associatedTaxaStr.value = props.associatedTaxaValue;
            }
        });

        function addTaxon() {
            if(scientificNameStr.value){
                let tempValue = associatedTaxaStr.value ? associatedTaxaStr.value : '';
                if(tempValue.length > 0){
                    tempValue += ', ';
                }
                tempValue += scientificNameStr.value;
                occurrenceStore.updateOccurrenceEditData('associatedtaxa', tempValue);
                scientificNameStr.value = null;
            }
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function processStrValueChange(value) {
            occurrenceStore.updateOccurrenceEditData('associatedtaxa', value);
        }

        function updateScientificNameValue(taxon) {
            if(taxon){
                scientificNameStr.value = taxon.sciname;
            }
            else{
                scientificNameStr.value = null
            }
        }

        Vue.onMounted(() => {
            associatedTaxaStr.value = props.associatedTaxaValue;
        });

        return {
            associatedTaxaStr,
            scientificNameStr,
            addTaxon,
            closePopup,
            processStrValueChange,
            updateScientificNameValue
        }
    }
};
const occurrenceAssociatedTaxaInputElement = {
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
            <q-input outlined v-model="value" type="textarea" :label="label" bg-color="white" :maxlength="maxlength" @update:model-value="processValueChange" :autogrow="true" :tabindex="tabindex" autocomplete="associatedtaxa" dense>
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
                    <q-icon name="construction" class="cursor-pointer">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Open Associated Taxa Entry Tool
                        </q-tooltip>
                        <q-menu v-model="showAssociatedTaxaToolPopup" anchor="top middle" self="bottom middle" max-width="700px" cover transition-show="scale" transition-hide="scale" class="z-max">
                            <occurrence-editor-associated-taxa-tool-popup
                                :associated-taxa-value="value"
                                @close:popup="showAssociatedTaxaToolPopup = false"
                            ></occurrence-editor-associated-taxa-tool-popup>
                        </q-menu>
                    </q-icon>
                </template>
            </q-input>
        </template>
        <template v-else>
            <q-input outlined v-model="value" type="textarea" :label="label" bg-color="white" @update:model-value="processValueChange" :readonly="disabled" :autogrow="true" :tabindex="tabindex" autocomplete="associatedtaxa" dense>
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
                    <q-icon name="construction" class="cursor-pointer">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Open Associated Taxa Entry Tool
                        </q-tooltip>
                        <q-menu v-model="showAssociatedTaxaToolPopup" anchor="bottom end" self="top left" cover transition-show="scale" transition-hide="scale" class="z-max">
                            <occurrence-editor-associated-taxa-tool-popup
                                :associated-taxa-value="value"
                                @close:popup="showAssociatedTaxaToolPopup = false"
                            ></occurrence-editor-associated-taxa-tool-popup>
                        </q-menu>
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
    components: {
        'occurrence-editor-associated-taxa-tool-popup': occurrenceEditorAssociatedTaxaToolPopup
    },
    setup(props, context) {
        const displayCoordinateToolPopup = Vue.ref(false);
        const displayDefinitionPopup = Vue.ref(false);
        const showAssociatedTaxaToolPopup = Vue.ref(false);

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(val) {
            context.emit('update:value', val);
        }

        return {
            displayCoordinateToolPopup,
            displayDefinitionPopup,
            showAssociatedTaxaToolPopup,
            openDefinitionPopup,
            processValueChange
        }
    }
};
