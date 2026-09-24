const mofFieldEditorPopup = {
    props: {
        field: {
            type: Object,
            default: null
        },
        fieldType: {
            type: String,
            default: 'occurrence'
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog v-if="layer" class="z-top" v-model="showPopup" v-if="!showConfirmation && !showRenamePopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="field && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end q-gutter-sm">
                                    <template v-if="field">
                                        <q-btn color="secondary" @click="processSaveUpdateData();" label="Save Edits" :disabled="!editsExist || !editDataValid" tabindex="0" />
                                        <q-btn color="secondary" @click="openRenamePopup();" label="Rename" tabindex="0" />
                                        <q-btn color="negative" @click="processDeleteField();" label="Remove" aria-label="Remove field" :disabled="includedInCalculation" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="processAddField();" label="Add Field" :disabled="!editDataValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element :disabled="!!field" :debounce="900" :definition="collectionMofFieldDefinitions['key']" label="Field Name" :value="editData['key']" :clearable="false" @update:value="(value) => processKeyValueChange(value, false)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <selector-input-element :definition="collectionMofFieldDefinitions['dataType']" label="Data Input Type" :options="dataInputTypeOptions" :value="editData['dataType']" @update:value="(value) => updateEditData('dataType', value)"></selector-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element :definition="collectionMofFieldDefinitions['label']" label="Field Label" :value="editData['label']" :clearable="false" @update:value="(value) => updateEditData('label', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element :definition="collectionMofFieldDefinitions['measurementType']" label="Measurement Type" :value="editData['measurementType']" @update:value="(value) => updateEditData('measurementType', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element :definition="collectionMofFieldDefinitions['measurementUnit']" label="Measurement Unit" :value="editData['measurementUnit']" @update:value="(value) => updateEditData('measurementUnit', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div>
                                <q-card flat bordered>
                                    <q-card-section class="q-pa-sm">
                                        <div class="text-subtitle1 text-bold">Definition Popup</div>
                                        <div class="q-mt-xs column q-col-gutter-sm">
                                            <div class="row">
                                                <div class="col-grow">
                                                    <text-field-input-element data-type="textarea" :definition="collectionMofFieldDefinitions['definition']" label="Definition" :value="editData['definition']['definition']" @update:value="(value) => updateDefinitionEditData('definition', value)"></text-field-input-element>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-grow">
                                                    <text-field-input-element data-type="textarea" :definition="collectionMofFieldDefinitions['comments']" label="Comments" :value="editData['definition']['comments']" @update:value="(value) => updateDefinitionEditData('comments', value)"></text-field-input-element>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-grow">
                                                    <text-field-input-element data-type="textarea" :definition="collectionMofFieldDefinitions['examples']" label="Examples" :value="editData['definition']['examples']" @update:value="(value) => updateDefinitionEditData('examples', value)"></text-field-input-element>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-grow">
                                                    <text-field-input-element data-type="textarea" :definition="collectionMofFieldDefinitions['source']" label="Source URL" :value="editData['definition']['source']" @update:value="(value) => updateDefinitionEditData('source', value)"></text-field-input-element>
                                                </div>
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                            <div v-if="editData['dataType'] === 'select'">
                                <q-card flat bordered>
                                    <q-card-section class="q-pa-sm">
                                        <div class="text-subtitle1 text-bold">Dropdown Options</div>
                                        <div class="q-mt-xs row justify-between q-gutter-sm">
                                            <div class="col-5 row q-gutter-sm">
                                                <div class="col-grow">
                                                    <text-field-input-element label="New Option" :value="newOptionValue" @update:value="processNewOptionValueChange"></text-field-input-element>
                                                </div>
                                                <div>
                                                    <q-btn color="secondary" @click="addNewOptionValue();" label="Add" :disabled="!newOptionValue" tabindex="0" />
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <template v-if="editData['options'].length > 0">
                                                    <draggable v-model="editData['options']" v-bind="dragOptions" class="column q-gutter-sm" group="optionItem">
                                                        <template #item="{ element: option }">
                                                            <q-card>
                                                                <q-card-section class="cursor-grab q-px-md q-py-xs row justify-between q-gutter-sm">
                                                                    <div class="text-subtitle1 text-bold">
                                                                        {{ option }}
                                                                    </div>
                                                                    <div>
                                                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="removeOptionValue(option);" icon="far fa-trash-alt" dense aria-label="Remove option" tabindex="0">
                                                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                                Remove option
                                                                            </q-tooltip>
                                                                        </q-btn>
                                                                    </div>
                                                                </q-card-section>
                                                            </q-card>
                                                        </template>
                                                    </draggable>
                                                </template>
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                            <div v-else-if="editData['dataType'] === 'string' || editData['dataType'] === 'textarea' || editData['dataType'] === 'int' || editData['dataType'] === 'number' || editData['dataType'] === 'increment' || editData['dataType'] === 'single-taxon-auto-complete' || editData['dataType'] === 'multi-taxon-auto-complete' || editData['dataType'] === 'taxon-identifier'">
                                <q-card flat bordered>
                                    <q-card-section class="q-pa-sm">
                                        <div class="text-subtitle1 text-bold">Input Configurations</div>
                                        <div class="q-mt-xs column q-col-gutter-sm">
                                            <template v-if="editData['dataType'] === 'taxon-identifier'">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <selector-input-element :clearable="true" :definition="collectionMofFieldDefinitions['identifier']" label="Identifier" :options="taxonValueIdentifierOptions" :value="editData['identifier']" @update:value="(value) => updateEditData('identifier', value)"></selector-input-element>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else-if="editData['dataType'] === 'string' || editData['dataType'] === 'textarea' || editData['dataType'] === 'int' || editData['dataType'] === 'number' || editData['dataType'] === 'increment'">
                                                <template v-if="editData['dataType'] === 'string' || editData['dataType'] === 'textarea'">
                                                    <div class="row q-col-gutter-sm">
                                                        <div class="col-12 col-sm-6">
                                                            <checkbox-input-element :definition="collectionMofFieldDefinitions['showCounter']" label="Show Character Counter" :value="editData['showCounter']" @update:value="(value) => updateEditData('showCounter', Number(value) === 1)"></checkbox-input-element>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <text-field-input-element data-type="int" :definition="collectionMofFieldDefinitions['maxlength']" label="Maximum Length" :value="editData['maxlength']" min-value="1" @update:value="(value) => updateEditData('maxlength', value)"></text-field-input-element>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template v-else-if="editData['dataType'] === 'int' || editData['dataType'] === 'number' || editData['dataType'] === 'increment'">
                                                    <div class="row q-col-gutter-sm">
                                                        <div class="col-12 col-sm-6">
                                                            <text-field-input-element data-type="number" :definition="collectionMofFieldDefinitions['minValue']" label="Minimum Value" :value="editData['minValue']" @update:value="(value) => updateEditData('minValue', value)"></text-field-input-element>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <text-field-input-element data-type="number" :definition="collectionMofFieldDefinitions['maxValue']" label="Maximum Value" :value="editData['maxValue']" @update:value="(value) => updateEditData('maxValue', value)"></text-field-input-element>
                                                        </div>
                                                    </div>
                                                    <div class="row q-col-gutter-sm">
                                                        <div class="col-12 col-sm-6">
                                                            <text-field-input-element data-type="int" :definition="collectionMofFieldDefinitions['roundValue']" label="Round Value" :value="editData['roundValue']" min-value="0" @update:value="(value) => updateEditData('roundValue', value)"></text-field-input-element>
                                                        </div>
                                                        <div v-if="editData['dataType'] === 'increment'" class="col-12 col-sm-6">
                                                            <text-field-input-element data-type="number" :definition="collectionMofFieldDefinitions['step']" label="Step Amount" :value="editData['step']" min-value="0" @update:value="(value) => updateEditData('step', value)"></text-field-input-element>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div class="row">
                                                    <div class="col-grow">
                                                        <text-field-input-element :definition="collectionMofFieldDefinitions['fieldHint']" label="Field Hint" :value="editData['fieldHint']" @update:value="(value) => updateEditData('fieldHint', value)"></text-field-input-element>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else-if="editData['dataType'] === 'single-taxon-auto-complete' || editData['dataType'] === 'multi-taxon-auto-complete'">
                                                <div class="row q-col-gutter-sm">
                                                    <div v-if="editData['dataType'] === 'multi-taxon-auto-complete'" class="col-12 col-sm-4 q-mr-md">
                                                        <text-field-input-element :definition="collectionMofFieldDefinitions['concatenator']" label="Concatenator" :value="editData['concatenator']" @update:value="(value) => updateEditData('concatenator', value)"></text-field-input-element>
                                                    </div>
                                                    <div class="q-mr-md">
                                                        <checkbox-input-element :definition="collectionMofFieldDefinitions['acceptedTaxaOnly']" label="Show Accepted Taxa Only" :value="editData['acceptedTaxaOnly']" @update:value="(value) => updateEditData('acceptedTaxaOnly', Number(value) === 1)"></checkbox-input-element>
                                                    </div>
                                                    <div>
                                                        <checkbox-input-element :definition="collectionMofFieldDefinitions['hideProtected']" label="Hide Protected Taxa" :value="editData['hideProtected']" @update:value="(value) => updateEditData('hideProtected', Number(value) === 1)"></checkbox-input-element>
                                                    </div>
                                                </div>
                                                <div v-if="taxonIdentifierOptions.length > 0" class="row q-col-gutter-sm">
                                                    <div class="col-12 col-sm-6">
                                                        <selector-input-element :clearable="true" :definition="collectionMofFieldDefinitions['identifierName']" label="Identifier Name" :options="taxonIdentifierOptions" :value="editData['identifierName']" @update:value="(value) => updateEditData('identifierName', value)"></selector-input-element>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <text-field-input-element :disabled="!editData['identifierName'] || !taxonValueIdentifierOptions.find(id => id['value'] === editData['identifierName'])" :definition="collectionMofFieldDefinitions['identifierValue']" label="Identifier Value" :value="editData['identifierValue']" @update:value="(value) => updateEditData('identifierValue', value)"></text-field-input-element>
                                                    </div>
                                                </div>
                                                <div class="row q-col-gutter-sm">
                                                    <div class="col-12 col-sm-3">
                                                        <taxa-kingdom-selector :clearable="true" :definition="collectionMofFieldDefinitions['kingdomId']" :selected-kingdom="taxonKingdom" label="Kingdom" @update:selected-kingdom="updateSelectedKingdom"></taxa-kingdom-selector>
                                                    </div>
                                                    <div class="col-12 col-sm-4">
                                                        <single-scientific-common-name-auto-complete :definition="collectionMofFieldDefinitions['parentTid']" :sciname="taxonomicGroupName" label="Taxonomic Group" :limit-to-options="true" :accepted-taxa-only="true" rank-low="11" @update:sciname="updateTaxonomicGroup"></single-scientific-common-name-auto-complete>
                                                    </div>
                                                    <div class="col-12 col-sm-5">
                                                        <selector-input-element :clearable="true" :definition="collectionMofFieldDefinitions['taxonType']" label="Taxon Type" :options="taxonTypeOptions" :value="editData['taxonType']" @update:value="(value) => updateEditData('taxonType', value)"></selector-input-element>
                                                    </div>
                                                </div>
                                                <div class="row q-col-gutter-sm">
                                                    <div class="col-12 col-sm-4">
                                                        <taxon-rank-selector :clearable="true" :kingdom-id="editData['kingdomId']" :definition="collectionMofFieldDefinitions['rankLimit']" label="Taxon Rank Limit" :value="editData['rankLimit']" @update:value="(value) => updateEditData('rankLimit', (value ? value['rankid'] : null))"></taxon-rank-selector>
                                                    </div>
                                                    <div class="col-12 col-sm-4">
                                                        <taxon-rank-selector :clearable="true" :kingdom-id="editData['kingdomId']" :definition="collectionMofFieldDefinitions['rankLow']" label="Highest Taxon Rank Limit" :value="editData['rankLow']" @update:value="(value) => updateEditData('rankLow', (value ? value['rankid'] : null))"></taxon-rank-selector>
                                                    </div>
                                                    <div class="col-12 col-sm-4">
                                                        <taxon-rank-selector :clearable="true" :kingdom-id="editData['kingdomId']" :definition="collectionMofFieldDefinitions['rankHigh']" label="Lowest Taxon Rank Limit" :value="editData['rankHigh']" @update:value="(value) => updateEditData('rankHigh', (value ? value['rankid'] : null))"></taxon-rank-selector>
                                                    </div>
                                                </div>
                                                <div class="row q-col-gutter-sm">
                                                    <div class="col-12 col-sm-4">
                                                        <text-field-input-element data-type="int" :definition="collectionMofFieldDefinitions['optionLimit']" label="Option Limit" :value="editData['optionLimit']" min-value="1" @update:value="(value) => updateEditData('optionLimit', value)"></text-field-input-element>
                                                    </div>
                                                    <div class="col-grow">
                                                        <checkbox-input-element :definition="collectionMofFieldDefinitions['hideAuthor']" label="Hide Author Names" :value="editData['hideAuthor']" @update:value="(value) => updateEditData('hideAuthor', Number(value) === 1)"></checkbox-input-element>
                                                    </div>
                                                    <div class="col-grow">
                                                        <checkbox-input-element :definition="collectionMofFieldDefinitions['limitToOptions']" label="Limit to Options" :value="editData['limitToOptions']" @update:value="(value) => updateEditData('limitToOptions', Number(value) === 1)"></checkbox-input-element>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-grow">
                                                        <multiple-scientific-common-name-auto-complete :definition="collectionMofFieldDefinitions['options']" label="Taxon Options Override" :sciname-arr="editData['options']" :limit-to-options="true" :accepted-taxa-only="true" :name-string-mode="false" @update:sciname="(value) => updateEditData('options', ((value && value.length > 0) ? value : []))"></multiple-scientific-common-name-auto-complete>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                            <div v-else-if="editData['dataType'] === 'calculated'">
                                <q-card flat bordered>
                                    <q-card-section class="q-pa-sm">
                                        <div class="text-subtitle1 text-bold">Calculation Settings</div>
                                        <div class="q-mt-xs column q-col-gutter-sm">
                                            <div class="row q-col-gutter-sm">
                                                <div class="col-12 col-sm-6">
                                                    <text-field-input-element data-type="number" :definition="collectionMofFieldDefinitions['calculation-minValue']" label="Minimum Value" :value="editData['minValue']" @update:value="(value) => updateEditData('minValue', value)"></text-field-input-element>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <text-field-input-element data-type="number" :definition="collectionMofFieldDefinitions['calculation-maxValue']" label="Maximum Value" :value="editData['maxValue']" @update:value="(value) => updateEditData('maxValue', value)"></text-field-input-element>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-grow">
                                                    <json-field-input-element :definition="collectionMofFieldDefinitions['calculation']" label="Calculation JSON" :value="editData['calculation'] ? JSON.stringify(editData['calculation'], null, 5) : null" @update:value="processCalculationJsonChange"></json-field-input-element>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-grow">
                                                    <text-field-input-element :disabled="true" data-type="textarea" label="Fields" :value="editData['fields'].length > 0 ? editData['fields'].join() : null"></text-field-input-element>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-grow">
                                                    <text-field-input-element :disabled="true" data-type="textarea" label="Required Fields" :value="editData['requiredFields'].length > 0 ? editData['requiredFields'].join() : null"></text-field-input-element>
                                                </div>
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <q-dialog class="z-top" v-model="showRenamePopup" v-if="!showConfirmation" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="showRenamePopup = false" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    <div class="row">
                        <div class="col-12">
                            <text-field-input-element label="New Field Name" :value="newFieldNameValue" @update:value="(value) => processKeyValueChange(value, true)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="q-mt-md row justify-end q-gutter-md">
                        <div>
                            <q-btn color="primary" @click="processRenameField();" label="Rename Field" :disabled="!newFieldNameValue" tabindex="0" />
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'confirmation-popup': confirmationPopup,
        'draggable': draggable,
        'json-field-input-element': jsonFieldInputElement,
        'multiple-scientific-common-name-auto-complete': multipleScientificCommonNameAutoComplete,
        'selector-input-element': selectorInputElement,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'taxa-kingdom-selector': taxaKingdomSelector,
        'taxon-rank-selector': taxonRankSelector,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { areObjectsEqual, hideWorking, showNotification, showWorking } = useCore();
        const collectionStore = useCollectionStore();
        const occurrenceStore = useOccurrenceStore();

        const activeTaxonGroupIdentifierOptions = Vue.ref([]);
        const activeTaxonValueIdentifierOptions = Vue.ref([]);
        const calculationFields = Vue.ref([]);
        const calculationRequiredFields = Vue.ref([]);
        const calculationValid = Vue.ref(true);
        const collectionId = Vue.computed(() => collectionStore.getCollectionId);
        const collectionMofFieldDefinitions = Vue.computed(() => collectionStore.getCollectionMofFieldDefinitions);
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const dataInputTypeOptions = Vue.computed(() => {
            const returnArr = [
                {value: 'string', label: 'String'},
                {value: 'textarea', label: 'Text'},
                {value: 'int', label: 'Interger'},
                {value: 'number', label: 'Number'},
                {value: 'increment', label: 'Incremental Number'},
                {value: 'boolean', label: 'Checkbox'},
                {value: 'select', label: 'Dropdown Menu'},
                {value: 'date', label: 'Date'},
                {value: 'single-taxon-auto-complete', label: 'Single Taxon Auto-Complete'},
                {value: 'multi-taxon-auto-complete', label: 'Multi Taxa Auto-Complete'},
                {value: 'calculated', label: 'Calculated Value'}
            ];
            if(taxonValueIdentifierOptions.value.length > 0){
                returnArr.push({value: 'taxon-identifier', label: 'Taxon Identifier'});
            }
            return returnArr;
        });
        const dragOptions = Vue.computed(() => {
            return {
                animation: 200,
                ghostClass: 'ghost'
            };
        });
        const editData = Vue.reactive({
            key: null,
            label: null,
            measurementType: null,
            measurementUnit: null,
            fieldHint: null,
            dataType: 'string',
            options: [],
            fields: [],
            requiredFields: [],
            calculation: null,
            minValue: null,
            maxValue: null,
            roundValue: null,
            step: null,
            maxlength: null,
            showCounter: false,
            acceptedTaxaOnly: false,
            hideAuthor: false,
            hideProtected: false,
            identifierName: null,
            identifierValue: null,
            kingdomId: null,
            limitToOptions: false,
            optionLimit: null,
            parentTid: null,
            rankHigh: null,
            rankLimit: null,
            rankLow: null,
            taxonType: null,
            concatenator: null,
            identifier: null,
            definition: {
                definition: null,
                comments: null,
                examples: null,
                source: null
            }
        });
        const editDataValid = Vue.computed(() => {
            let valid = true;
            if(!editData.key || editData.key === '' || !editData.label || editData.label === ''){
                valid = false;
            }
            else if(editData['dataType'] === 'select' && editData.options.length === 0){
                valid = false;
            }
            else if(editData['dataType'] === 'taxon-identifier' && !editData['identifier']){
                valid = false;
            }
            else if(editData['dataType'] === 'calculated' && (!editData['calculation'] || !calculationValid.value)){
                valid = false;
            }
            return valid;
        });
        const editsExist = Vue.computed(() => {
            let exist;
            if((!props.field && editDataValid.value)){
                exist = true;
            }
            else{
                exist = !areObjectsEqual(Object.assign({}, props.field), Object.assign({}, saveData.value));
            }
            return exist;
        });
        const eventCalculatedDataFields = Vue.computed(() => collectionStore.getEventMofCalculatedDataFields);
        const eventDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
        const eventDataFieldsLayoutData = Vue.computed(() => collectionStore.getEventMofDataFieldsLayoutData);
        const eventDataLabel = Vue.computed(() => collectionStore.getEventMofDataLabel);
        const includedInCalculation = Vue.computed(() => {
            let included = false;
            Object.keys(eventCalculatedDataFields.value).forEach((fieldName) => {
                if(eventCalculatedDataFields.value[fieldName].hasOwnProperty('fields') && eventCalculatedDataFields.value[fieldName]['fields'].length > 0 && eventCalculatedDataFields.value[fieldName]['fields'].includes(editData['key'])){
                    included = true;
                }
            });
            if(!included){
                Object.keys(locationCalculatedDataFields.value).forEach((fieldName) => {
                    if(locationCalculatedDataFields.value[fieldName].hasOwnProperty('fields') && locationCalculatedDataFields.value[fieldName]['fields'].length > 0 && locationCalculatedDataFields.value[fieldName]['fields'].includes(editData['key'])){
                        included = true;
                    }
                });
            }
            if(!included){
                Object.keys(occurrenceCalculatedDataFields.value).forEach((fieldName) => {
                    if(occurrenceCalculatedDataFields.value[fieldName].hasOwnProperty('fields') && fieldName['fields'].length > 0 && fieldName['fields'].includes(editData['key'])){
                        included = true;
                    }
                });
            }
            return included;
        });
        const locationCalculatedDataFields = Vue.computed(() => collectionStore.getLocationMofCalculatedDataFields);
        const locationDataFields = Vue.computed(() => collectionStore.getLocationMofDataFields);
        const locationDataFieldsLayoutData = Vue.computed(() => collectionStore.getLocationMofDataFieldsLayoutData);
        const locationDataLabel = Vue.computed(() => collectionStore.getLocationMofDataLabel);
        const newFieldNameValue = Vue.ref(null);
        const newOptionValue = Vue.ref(null);
        const numericDataTypes = ['int','number','increment','calculated'];
        const occurrenceData = occurrenceStore.getBlankOccurrenceRecord;
        const occurrenceCalculatedDataFields = Vue.computed(() => collectionStore.getOccurrenceMofCalculatedDataFields);
        const occurrenceDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
        const occurrenceDataFieldsLayoutData = Vue.computed(() => collectionStore.getOccurrenceMofDataFieldsLayoutData);
        const occurrenceDataLabel = Vue.computed(() => collectionStore.getOccurrenceMofDataLabel);
        const presetTaxonIdentifierOptions = [
            {value: 'col', label: 'Catalogue of Life ID'},
            {value: 'eol', label: 'Encyclopedia of Life ID'},
            {value: 'itis', label: 'ITIS TSN'},
            {value: 'usda', label: 'USDA Code'},
            {value: 'worms', label: 'WoRMS Aphia ID'}
        ];
        const saveData = Vue.computed(() => {
            const returnVal = {};
            returnVal['key'] = editData['key'];
            returnVal['label'] = editData['label'];
            returnVal['dataType'] = editData['dataType'];
            if(editData['measurementType']){
                returnVal['measurementType'] = editData['measurementType'];
            }
            if(editData['measurementUnit']){
                returnVal['measurementUnit'] = editData['measurementUnit'];
            }
            if(editData['definition']['definition'] || editData['definition']['comments'] || editData['definition']['examples'] || editData['definition']['source']){
                returnVal['definition'] = {};
                if(editData['definition']['definition']){
                    returnVal['definition']['definition'] = editData['definition']['definition'];
                }
                if(editData['definition']['comments']){
                    returnVal['definition']['comments'] = editData['definition']['comments'];
                }
                if(editData['definition']['examples']){
                    returnVal['definition']['examples'] = editData['definition']['examples'];
                }
                if(editData['definition']['source']){
                    returnVal['definition']['source'] = editData['definition']['source'];
                }
            }
            if((editData['dataType'] === 'select' || editData['dataType'] === 'single-taxon-auto-complete' || editData['dataType'] === 'multi-taxon-auto-complete') && editData['options'].length > 0){
                returnVal['options'] = editData['options'];
            }
            if((editData['dataType'] === 'int' || editData['dataType'] === 'number' || editData['dataType'] === 'increment' || editData['dataType'] === 'string' || editData['dataType'] === 'textarea') && editData['fieldHint']){
                returnVal['fieldHint'] = editData['fieldHint'];
            }
            if(editData['dataType'] === 'int' || editData['dataType'] === 'number' || editData['dataType'] === 'increment' || editData['dataType'] === 'calculated'){
                if(editData['minValue']){
                    returnVal['minValue'] = editData['minValue'];
                }
                if(editData['maxValue']){
                    returnVal['maxValue'] = editData['maxValue'];
                }
                if(editData['roundValue']){
                    returnVal['roundValue'] = editData['roundValue'];
                }
            }
            if(editData['dataType'] === 'string' || editData['dataType'] === 'textarea'){
                if(editData['maxlength']){
                    returnVal['maxlength'] = editData['maxlength'];
                }
                if(editData['showCounter']){
                    returnVal['showCounter'] = editData['showCounter'];
                }
            }
            if(editData['dataType'] === 'increment' && editData['step']){
                returnVal['step'] = editData['step'];
            }
            if(editData['dataType'] === 'single-taxon-auto-complete' || editData['dataType'] === 'multi-taxon-auto-complete'){
                if(editData['acceptedTaxaOnly']){
                    returnVal['acceptedTaxaOnly'] = editData['acceptedTaxaOnly'];
                }
                if(editData['hideAuthor']){
                    returnVal['hideAuthor'] = editData['hideAuthor'];
                }
                if(editData['hideProtected']){
                    returnVal['hideProtected'] = editData['hideProtected'];
                }
                if(editData['identifierName']){
                    returnVal['identifierName'] = editData['identifierName'];
                }
                if(editData['identifierValue']){
                    returnVal['identifierValue'] = editData['identifierValue'];
                }
                if(editData['kingdomId']){
                    returnVal['kingdomId'] = editData['kingdomId'];
                }
                if(editData['limitToOptions']){
                    returnVal['limitToOptions'] = editData['limitToOptions'];
                }
                if(editData['optionLimit']){
                    returnVal['optionLimit'] = editData['optionLimit'];
                }
                if(editData['parentTid']){
                    returnVal['parentTid'] = editData['parentTid'];
                }
                if(editData['rankHigh']){
                    returnVal['rankHigh'] = editData['rankHigh'];
                }
                if(editData['rankLimit']){
                    returnVal['rankLimit'] = editData['rankLimit'];
                }
                if(editData['rankLow']){
                    returnVal['rankLow'] = editData['rankLow'];
                }
                if(editData['taxonType']){
                    returnVal['taxonType'] = editData['taxonType'];
                }
            }
            if(editData['dataType'] === 'multi-taxon-auto-complete' && editData['concatenator']){
                returnVal['concatenator'] = editData['concatenator'];
            }
            if(editData['dataType'] === 'calculated'){
                returnVal['calculation'] = editData['calculation'];
                returnVal['fields'] = editData['fields'];
                if(editData['requiredFields'].length > 0){
                    returnVal['requiredFields'] = editData['requiredFields'];
                }
            }
            if(editData['dataType'] === 'taxon-identifier' && editData['identifier']){
                returnVal['identifier'] = editData['identifier'];
            }
            return returnVal;
        });
        const showConfirmation = Vue.ref(false);
        const showRenamePopup = Vue.ref(false);
        const taxonIdentifierOptions = Vue.computed(() => {
            const returnArr = [];
            if(activeTaxonValueIdentifierOptions.value.length > 0){
                activeTaxonGroupIdentifierOptions.value.forEach((identifier) => {
                    returnArr.push({value: identifier, label: identifier});
                });
                activeTaxonValueIdentifierOptions.value.forEach((identifier) => {
                    if(!presetTaxonIdentifierOptions.find(id => id['value'] === identifier)){
                        returnArr.push({value: identifier, label: identifier});
                    }
                });
                returnArr.sort((a, b) => {
                    return a['label'].localeCompare(b['label']);
                });
            }
            return returnArr;
        });
        const taxonKingdom = Vue.ref(null);
        const taxonomicGroupName = Vue.ref(null);
        const taxonTypeOptions = [
            {value: '1', label: 'Family or Scientific Name'},
            {value: '2', label: 'Family only'},
            {value: '3', label: 'Scientific Name only'},
            {value: '4', label: 'Taxonomic group'},
            {value: '5', label: 'Common Name'}
        ];
        const taxonValueIdentifierOptions = Vue.computed(() => {
            const returnArr = [];
            if(activeTaxonValueIdentifierOptions.value.length > 0){
                activeTaxonValueIdentifierOptions.value.forEach((identifier) => {
                    const preset = presetTaxonIdentifierOptions.find(id => id['value'] === identifier);
                    if(preset){
                        returnArr.push(preset);
                    }
                    else{
                        returnArr.push({value: identifier, label: identifier});
                    }
                });
                returnArr.sort((a, b) => {
                    return a['label'].localeCompare(b['label']);
                });
            }
            return returnArr;
        });

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addNewOptionValue() {
            const optionsArr = editData['options'].slice();
            optionsArr.push(newOptionValue.value);
            editData['options'] = optionsArr.slice();
            newOptionValue.value = null;
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function openRenamePopup() {
            newFieldNameValue.value = null;
            showRenamePopup.value = true;
        }

        function processAddField() {
            context.emit('create:field', saveData.value);
        }

        function processCalculationJsonChange(value) {
            updateEditData('fields', []);
            updateEditData('requiredFields', []);
            calculationFields.value.length = 0;
            calculationRequiredFields.value.length = 0;
            if(value){
                const calculationData = JSON.parse(value);
                const dataChanged = JSON.stringify(editData['calculation']) !== JSON.stringify(calculationData);
                calculationValid.value = false;
                updateEditData('calculation', Object.assign({}, calculationData));
                if(calculationData.hasOwnProperty('type') && calculationData['type'] && validateCalculationData(calculationData)){
                    calculationValid.value = true;
                    calculationFields.value.sort((a, b) => a.localeCompare(b));
                    calculationRequiredFields.value.sort((a, b) => a.localeCompare(b));
                    updateEditData('fields', calculationFields.value.slice());
                    updateEditData('requiredFields', calculationRequiredFields.value.slice());
                    if(dataChanged){
                        showNotification('positive','Configuration JSON is valid');
                    }
                }
            }
            else{
                updateEditData('calculation', null);
            }
        }

        function processDeleteField() {
            showWorking();
            occurrenceStore.getMofFieldDataRecordCount(props.fieldType, editData['key'], (res) => {
                hideWorking();
                if(Number(res) > 0){
                    const confirmText = 'This field has ' + res + ' data points. Removing the field will remove all data points as well. This cannot be undone. Do you want to continue?';
                    confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                        if(val){
                            showWorking();
                            removeMofFieldData(() => {
                                context.emit('delete:field', saveData.value);
                            });
                        }
                    }});
                }
                else{
                    context.emit('delete:field', saveData.value);
                }
            });
        }

        function processKeyValueChange(value, newName) {
            value = value.toLowerCase().replaceAll(' ', '_').replaceAll('"', '').replaceAll("'", '');
            if(eventDataFields.value.hasOwnProperty(value) || locationDataFields.value.hasOwnProperty(value) || occurrenceDataFields.value.hasOwnProperty(value)){
                showNotification('negative', 'There is already a measurement or fact field with the field name you entered. Please enter a different name.');
            }
            else if(Object.keys(occurrenceData).includes(value)){
                showNotification('negative', 'There is already an occurrence field with the field name you entered. Please enter a different name.');
            }
            else{
                if(newName){
                    newFieldNameValue.value = value;
                }
                else{
                    updateEditData('key', value);
                }
            }
        }

        function processNewOptionValueChange(value) {
            value = value.trim();
            newOptionValue.value = (value && value.length > 0) ? value : null;
        }

        function processRenameField() {
            showWorking();
            occurrenceStore.getMofFieldDataRecordCount(props.fieldType, editData['key'], (res) => {
                hideWorking();
                if(Number(res) > 0){
                    const confirmText = res + ' data points will be updated with the new name for this field. This cannot be undone. Do you want to continue?';
                    confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                        if(val){
                            showWorking();
                            renameMofFieldData(() => {
                                renameMofFieldInEventFieldData();
                            });
                        }
                    }});
                }
                else{
                    renameMofFieldInEventFieldData();
                }
            });
        }

        function processSaveUpdateData() {
            context.emit('update:field', saveData.value);
        }

        function removeMofFieldData(callback) {
            const formData = new FormData();
            formData.append('collid', collectionId.value.toString());
            formData.append('type', props.fieldType);
            formData.append('field', editData['key']);
            formData.append('action', 'deleteMofRecordsByField');
            fetch(occurrenceMeasurementOrFactApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) > 0){
                    removeMofFieldData(callback);
                }
                else{
                    callback();
                }
            });
        }

        function renameField() {
            const searchStr = '"' + editData['key'] + '"';
            const changeStr = '"' + newFieldNameValue.value + '"';
            let fieldData, layoutData, fieldLabel, updateKey;
            if(props.fieldType === 'occurrence'){
                fieldData = Object.assign({}, occurrenceDataFields.value);
                layoutData = occurrenceDataFieldsLayoutData.value.slice();
                fieldLabel = occurrenceDataLabel.value;
                updateKey = 'occurrenceMofExtension';
            }
            else if(props.fieldType === 'event'){
                fieldData = Object.assign({}, eventDataFields.value);
                layoutData = eventDataFieldsLayoutData.value.slice();
                fieldLabel = eventDataLabel.value;
                updateKey = 'eventMofExtension';
            }
            else{
                fieldData = Object.assign({}, locationDataFields.value);
                layoutData = locationDataFieldsLayoutData.value.slice();
                fieldLabel = locationDataLabel.value;
            }
            const results = renameMofFieldInFieldData(fieldData);
            const newFieldData = Object.assign({}, saveData.value);
            delete newFieldData.key;
            results.fieldData[newFieldNameValue.value] = Object.assign({}, newFieldData);
            delete results.fieldData[editData['key']];
            if(layoutData.length > 0 && JSON.stringify(layoutData).includes(searchStr)){
                const jsonStr = JSON.stringify(layoutData).replaceAll(searchStr, changeStr);
                layoutData = JSON.parse(jsonStr);
            }
            const updateData = {};
            updateData['dataFields'] = Object.assign({}, results.fieldData);
            updateData['dataLayout'] = layoutData.slice();
            updateData['dataLabel'] = fieldLabel;
            collectionStore.updateConfiguredPropertyValue(updateKey, updateData, (res) => {
                hideWorking();
                if(!res){
                    showNotification('positive', 'Field renamed.');
                }
                else{
                    showNotification('negative', 'There was an error renaming the field.');
                }
                context.emit('data:update');
                closePopup();
            });
        }

        function renameMofFieldData(callback) {
            const formData = new FormData();
            formData.append('collid', collectionId.value.toString());
            formData.append('type', props.fieldType);
            formData.append('field', editData['key']);
            formData.append('newFieldName', newFieldNameValue.value);
            formData.append('action', 'renameMofField');
            fetch(occurrenceMeasurementOrFactApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) > 0){
                    renameMofFieldData(callback);
                }
                else{
                    callback();
                }
            });
        }

        function renameMofFieldInEventFieldData() {
            if(props.fieldType !== 'event' && Object.keys(eventCalculatedDataFields.value).length > 0){
                const fieldData = Object.assign({}, eventDataFields.value);
                const results = renameMofFieldInFieldData(fieldData);
                if(results.changed){
                    const updateData = {};
                    updateData['dataFields'] = Object.assign({}, results.fieldData);
                    updateData['dataLayout'] = eventDataFieldsLayoutData.value;
                    updateData['dataLabel'] = eventDataLabel.value;
                    collectionStore.updateConfiguredPropertyValue('eventMofExtension', updateData, () => {
                        context.emit('data:update');
                        renameMofFieldInLocationFieldData();
                    });
                }
                else{
                    renameMofFieldInLocationFieldData();
                }
            }
            else{
                renameMofFieldInLocationFieldData();
            }
        }

        function renameMofFieldInFieldData(fieldData) {
            const searchStr = '"' + editData['key'] + '"';
            const changeStr = '"' + newFieldNameValue.value + '"';
            let changed = false;
            Object.keys(fieldData).forEach((fieldName) => {
                if(fieldData[fieldName]['dataType'] === 'calculated'){
                    if(JSON.stringify(fieldData[fieldName]['calculation']).includes(searchStr)){
                        const jsonStr = JSON.stringify(fieldData[fieldName]['calculation']).replaceAll(searchStr, changeStr);
                        fieldData[fieldName]['calculation'] = Object.assign({}, JSON.parse(jsonStr));
                        changed = true;
                    }
                    if(fieldData[fieldName].hasOwnProperty('fields') && fieldData[fieldName]['fields'].length > 0 && JSON.stringify(fieldData[fieldName]['fields']).includes(searchStr)){
                        const jsonStr = JSON.stringify(fieldData[fieldName]['fields']).replaceAll(searchStr, changeStr);
                        fieldData[fieldName]['fields'] = JSON.parse(jsonStr);
                        changed = true;
                    }
                    if(fieldData[fieldName].hasOwnProperty('requiredFields') && fieldData[fieldName]['requiredFields'].length > 0 && JSON.stringify(fieldData[fieldName]['requiredFields']).includes(searchStr)){
                        const jsonStr = JSON.stringify(fieldData[fieldName]['requiredFields']).replaceAll(searchStr, changeStr);
                        fieldData[fieldName]['requiredFields'] = JSON.parse(jsonStr);
                        changed = true;
                    }
                }
            });
            return {
                changed: changed,
                fieldData: Object.assign({}, fieldData)
            };
        }

        function renameMofFieldInLocationFieldData() {
            if(props.fieldType !== 'location' && Object.keys(locationCalculatedDataFields.value).length > 0){
                const fieldData = Object.assign({}, locationDataFields.value);
                const results = renameMofFieldInFieldData(fieldData);
                if(results.changed){
                    const updateData = {};
                    updateData['dataFields'] = Object.assign({}, results.fieldData);
                    updateData['dataLayout'] = locationDataFieldsLayoutData.value;
                    updateData['dataLabel'] = locationDataLabel.value;
                    collectionStore.updateConfiguredPropertyValue('locationMofExtension', updateData, () => {
                        context.emit('data:update');
                        renameMofFieldInOccurrenceFieldData();
                    });
                }
                else{
                    renameMofFieldInOccurrenceFieldData();
                }
            }
            else{
                renameMofFieldInOccurrenceFieldData();
            }
        }

        function renameMofFieldInOccurrenceFieldData() {
            if(props.fieldType !== 'occurrence' && Object.keys(occurrenceCalculatedDataFields.value).length > 0){
                const fieldData = Object.assign({}, occurrenceDataFields.value);
                const results = renameMofFieldInFieldData(fieldData);
                if(results.changed){
                    const updateData = {};
                    updateData['dataFields'] = Object.assign({}, results.fieldData);
                    updateData['dataLayout'] = occurrenceDataFieldsLayoutData.value;
                    updateData['dataLabel'] = occurrenceDataLabel.value;
                    collectionStore.updateConfiguredPropertyValue('occurrenceMofExtension', updateData, () => {
                        context.emit('data:update');
                        renameField();
                    });
                }
                else{
                    renameField();
                }
            }
            else{
                renameField();
            }
        }

        function removeOptionValue(value) {
            const optionsArr = editData['options'].slice();
            const index = editData['options'].indexOf(value);
            optionsArr.splice(index, 1);
            editData['options'] = optionsArr.slice();
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setEditData() {
            Object.keys(props.field).forEach((prop) => {
                if(prop !== 'options' && prop !== 'fields' && prop !== 'requiredFields' && prop !== 'definition' && props.field[prop]){
                    editData[prop] = props.field[prop];
                }
            });
            if(props.field.hasOwnProperty('options') && props.field['options'] && props.field['options'].length > 0){
                editData['options'] = props.field['options'];
            }
            if(props.field.hasOwnProperty('fields') && props.field['fields'] && props.field['fields'].length > 0){
                editData['fields'] = props.field['fields'];
            }
            if(props.field.hasOwnProperty('requiredFields') && props.field['requiredFields'] && props.field['requiredFields'].length > 0){
                editData['requiredFields'] = props.field['requiredFields'];
            }
            if(props.field.hasOwnProperty('definition') && props.field['definition']){
                editData['definition']['definition'] = (props.field['definition'].hasOwnProperty('definition') && props.field['definition']['definition']) ? props.field['definition']['definition'] : null;
                editData['definition']['comments'] = (props.field['definition'].hasOwnProperty('comments') && props.field['definition']['comments']) ? props.field['definition']['comments'] : null;
                editData['definition']['examples'] = (props.field['definition'].hasOwnProperty('examples') && props.field['definition']['examples']) ? props.field['definition']['examples'] : null;
                editData['definition']['source'] = (props.field['definition'].hasOwnProperty('source') && props.field['definition']['source']) ? props.field['definition']['source'] : null;
            }
        }

        function setTaxonGroupIdentifierOptions() {
            const formData = new FormData();
            formData.append('action', 'getGroupIdentifierNameArr');
            fetch(taxonIdentifierApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((data) => {
                if(data && data.length > 0){
                    activeTaxonGroupIdentifierOptions.value = data.slice();
                }
            });
        }

        function setTaxonValueIdentifierOptions() {
            const formData = new FormData();
            formData.append('action', 'getValueIdentifierNameArr');
            fetch(taxonIdentifierApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((data) => {
                if(data && data.length > 0){
                    activeTaxonValueIdentifierOptions.value = data.slice();
                }
            });
        }

        function updateDefinitionEditData(key, value) {
            editData['definition'][key] = value;
        }

        function updateEditData(key, value) {
            editData[key] = value;
            if(key === 'dataType' && (value === 'select' || value === 'single-taxon-auto-complete' || value === 'multi-taxon-auto-complete')){
                editData['options'].length = 0;
            }
        }

        function updateSelectedKingdom(kingdomObj) {
            taxonKingdom.value = kingdomObj;
            updateEditData('kingdomId', (kingdomObj ? kingdomObj.id : null));
        }

        function updateTaxonomicGroup(taxonObj) {
            taxonomicGroupName.value = taxonObj ? taxonObj.sciname : null;
            updateEditData('parentTid', (taxonObj ? taxonObj.tid : null));
        }

        function validateCalculationData(calculationObj, required = null) {
            let returnVal = true;
            if(calculationObj['type'] === 'value'){
                if(calculationObj.hasOwnProperty('field')){
                    if(!calculationObj['field'] || calculationObj['field'] === ''){
                        returnVal = false;
                        showNotification('negative', 'Required field name value missing from Calculation JSON.');
                    }
                    else if(!eventDataFields.value.hasOwnProperty(calculationObj['field']) && !locationDataFields.value.hasOwnProperty(calculationObj['field']) && !occurrenceDataFields.value.hasOwnProperty(calculationObj['field'])){
                        returnVal = false;
                        showNotification('negative', (calculationObj['field'] + ' not found in configured Measurement or Fact fields.'));
                    }
                    else if(eventDataFields.value.hasOwnProperty(calculationObj['field']) && !numericDataTypes.includes(eventDataFields.value[calculationObj['field']]['dataType'])){
                        returnVal = false;
                        showNotification('negative', (calculationObj['field'] + ' is not a numeric data type.'));
                    }
                    else if(locationDataFields.value.hasOwnProperty(calculationObj['field']) && !numericDataTypes.includes(locationDataFields.value[calculationObj['field']]['dataType'])){
                        returnVal = false;
                        showNotification('negative', (calculationObj['field'] + ' is not a numeric data type.'));
                    }
                    else if(occurrenceDataFields.value.hasOwnProperty(calculationObj['field']) && !numericDataTypes.includes(occurrenceDataFields.value[calculationObj['field']]['dataType'])){
                        returnVal = false;
                        showNotification('negative', (calculationObj['field'] + ' is not a numeric data type.'));
                    }
                    else{
                        if(!calculationFields.value.includes(calculationObj['field'])){
                            calculationFields.value.push(calculationObj['field']);
                        }
                        if(required && !calculationRequiredFields.value.includes(calculationObj['field'])){
                            calculationRequiredFields.value.push(calculationObj['field']);
                        }
                    }
                }
                else if(calculationObj.hasOwnProperty('value')){
                    returnVal = (calculationObj['value'] && calculationObj['value'] !== '');
                    if(!returnVal){
                        showNotification('negative', 'Required value data missing from Calculation JSON.');
                    }
                }
                else{
                    returnVal = false;
                    showNotification('negative', 'Field and value data missing from Calculation JSON.');
                }
            }
            else{
                const calculationValues = calculationObj['values'].slice();
                const initialValObj = calculationValues.shift();
                const requiredFields = (required || calculationObj['type'] === 'subtract' || calculationObj['type'] === 'divide' || calculationObj['type'] === 'multiply');
                if(initialValObj){
                    returnVal = validateCalculationData(initialValObj, requiredFields);
                }
                if(returnVal){
                    calculationValues.forEach((calcObj) => {
                        if(returnVal){
                            returnVal = validateCalculationData(calcObj, requiredFields);
                        }
                    });
                }
            }
            return returnVal;
        }

        Vue.onMounted(() => {
            setTaxonGroupIdentifierOptions();
            setTaxonValueIdentifierOptions();
            if(props.field){
                setEditData();
            }
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            collectionMofFieldDefinitions,
            confirmationPopupRef,
            contentRef,
            contentStyle,
            dataInputTypeOptions,
            dragOptions,
            editData,
            editDataValid,
            editsExist,
            includedInCalculation,
            newFieldNameValue,
            newOptionValue,
            showConfirmation,
            showRenamePopup,
            taxonIdentifierOptions,
            taxonKingdom,
            taxonomicGroupName,
            taxonTypeOptions,
            taxonValueIdentifierOptions,
            addNewOptionValue,
            closePopup,
            openRenamePopup,
            processAddField,
            processCalculationJsonChange,
            processDeleteField,
            processKeyValueChange,
            processNewOptionValueChange,
            processRenameField,
            processSaveUpdateData,
            removeOptionValue,
            updateDefinitionEditData,
            updateEditData,
            updateSelectedKingdom,
            updateTaxonomicGroup
        }
    }
};
