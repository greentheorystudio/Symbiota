const mofFieldEditorPopup = {
    props: {
        field: {
            type: Object,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog v-if="layer" class="z-top" v-model="showPopup" v-if="!showConfirmation" persistent>
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
                                        <q-btn color="secondary" @click="updateLayer();" label="Save Edits" :disabled="!editsExist || !editDataValid" tabindex="0" />
                                        <q-btn color="negative" @click="deleteLayer();" label="Remove" aria-label="Remove layer" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addLayer();" label="Add Field" :disabled="!editDataValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element :disabled="!!field" :debounce="900" :definition="collectionMofFieldDefinitions['key']" label="Field Name" :value="editData['key']" :clearable="false" @update:value="processKeyValueChange"></text-field-input-element>
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
                                    <q-card-section>
                                        <div class="text-subtitle1 text-bold">Definition Popup</div>
                                        <div class="q-mt-xs q-pl-sm column q-gutter-sm">
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
                                    <q-card-section>
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
                                    <q-card-section>
                                        <div class="text-subtitle1 text-bold">Input Configurations</div>
                                        <div class="q-mt-xs column q-gutter-y-sm">
                                            <template v-if="editData['dataType'] === 'taxon-identifier'">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <selector-input-element :definition="collectionMofFieldDefinitions['identifier']" label="Identifier" :options="taxonIdentifierOptions" :value="editData['identifier']" @update:value="(value) => updateEditData('identifier', value)"></selector-input-element>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else-if="editData['dataType'] === 'string' || editData['dataType'] === 'textarea' || editData['dataType'] === 'int' || editData['dataType'] === 'number' || editData['dataType'] === 'increment'">
                                                <template v-if="editData['dataType'] === 'string' || editData['dataType'] === 'textarea'">
                                                    <div class="row q-gutter-x-sm">
                                                        <div class="col-grow">
                                                            <checkbox-input-element :definition="collectionMofFieldDefinitions['showCounter']" label="Show Character Counter" :value="editData['showCounter']" @update:value="(value) => updateEditData('showCounter', Number(value) === 1)"></checkbox-input-element>
                                                        </div>
                                                        <div class="col-grow">
                                                            <text-field-input-element :definition="collectionMofFieldDefinitions['maxlength']" label="Maximum Length" :value="editData['maxlength']" @update:value="(value) => updateEditData('maxlength', value)"></text-field-input-element>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template v-else-if="editData['dataType'] === 'int' || editData['dataType'] === 'number' || editData['dataType'] === 'increment'">
                                                    <div class="row q-gutter-x-sm">
                                                        <div class="col-grow">
                                                            <text-field-input-element data-type="number" :definition="collectionMofFieldDefinitions['minValue']" label="Minimum Value" :value="editData['minValue']" @update:value="(value) => updateEditData('minValue', value)"></text-field-input-element>
                                                        </div>
                                                        <div class="col-grow">
                                                            <text-field-input-element data-type="number" :definition="collectionMofFieldDefinitions['maxValue']" label="Maximum Value" :value="editData['maxValue']" @update:value="(value) => updateEditData('maxValue', value)"></text-field-input-element>
                                                        </div>
                                                    </div>
                                                    <div class="row q-gutter-x-sm">
                                                        <div :class="editData['dataType'] === 'increment' ? 'col-grow' : 'col-12 col-sm-6'">
                                                            <text-field-input-element data-type="int" :definition="collectionMofFieldDefinitions['roundValue']" label="Round Value" :value="editData['roundValue']" min-value="0" @update:value="(value) => updateEditData('roundValue', value)"></text-field-input-element>
                                                        </div>
                                                        <div v-if="editData['dataType'] === 'increment'" class="col-grow">
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
                                            
                                            </template>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
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
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { areObjectsEqual, hideWorking, showNotification, showWorking } = useCore();
        const collectionStore = useCollectionStore();
        const occurrenceStore = useOccurrenceStore();

        const activeTaxonIdentifierOptions = Vue.ref([]);
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
            if(taxonIdentifierOptions.value.length > 0){
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
            return editData.key && editData.label;
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
        const eventDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
        const locationDataFields = Vue.computed(() => collectionStore.getLocationMofDataFields);
        const newOptionValue = Vue.ref(null);
        const occurrenceData = occurrenceStore.getBlankOccurrenceRecord;
        const occurrenceDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
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
        const taxonIdentifierOptions = Vue.computed(() => {
            const returnArr = [];
            if(activeTaxonIdentifierOptions.value.length > 0){
                activeTaxonIdentifierOptions.value.forEach((identifier) => {
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

        function processKeyValueChange(value) {
            value = value.toLowerCase().replaceAll(' ', '_');
            if(eventDataFields.value.hasOwnProperty(value) || locationDataFields.value.hasOwnProperty(value) || occurrenceDataFields.value.hasOwnProperty(value)){
                showNotification('negative', 'There is already a measurement or fact field with the field name you entered. Please enter a different name.');
            }
            else if(Object.keys(occurrenceData).includes(value)){
                showNotification('negative', 'There is already an occurrence field with the field name you entered. Please enter a different name.');
            }
            else{
                updateEditData('key', value);
            }
        }

        function processNewOptionValueChange(value) {
            value = value.trim();
            newOptionValue.value = (value && value.length > 0) ? value : null;
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

        function setTaxonIdentifierOptions() {
            const formData = new FormData();
            formData.append('action', 'getValueIdentifierNameArr');
            fetch(taxonIdentifierApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((data) => {
                if(data && data.length > 0){
                    activeTaxonIdentifierOptions.value = data.slice();
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

        Vue.onMounted(() => {
            setTaxonIdentifierOptions();
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
            newOptionValue,
            showConfirmation,
            taxonIdentifierOptions,
            addNewOptionValue,
            closePopup,
            processKeyValueChange,
            processNewOptionValueChange,
            removeOptionValue,
            updateDefinitionEditData,
            updateEditData
        }
    }
};
