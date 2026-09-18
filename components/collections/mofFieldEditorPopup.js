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
                        <div class="q-pa-md column q-gutter-sm">
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
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="column q-gutter-sm">
                                        <div class="row">
                                            <div class="col-grow">
                                                <text-field-input-element label="Layer Name" :value="editData['layerName']" @update:value="(value) => editData['layerName'] = value"></text-field-input-element>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <text-field-input-element data-type="textarea" label="Description" :value="editData['layerDescription']" @update:value="(value) => editData['layerDescription'] = value"></text-field-input-element>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <text-field-input-element label="Provided By" :value="editData['providedBy']" @update:value="(value) => editData['providedBy'] = value"></text-field-input-element>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <text-field-input-element data-type="textarea" label="Source URL" :value="editData['sourceURL']" @update:value="(value) => editData['sourceURL'] = value"></text-field-input-element>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <date-input-element label="Date Aquired" :value="editData['dateAquired']" @update:value="(value) => editData['dateAquired'] = (value ? value['date'] : null)"></date-input-element>
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                            <!-- <template v-if="uploadedFile || editData['file']">
                                <q-card flat bordered>
                                    <q-card-section>
                                        <div class="text-subtitle1 text-bold">Initial Symbology</div>
                                        <div class="q-mt-xs q-pl-sm column">
                                            <template v-if="mapDataType === 'vector'">
                                                <div class="q-mb-sm row justify-start q-col-gutter-md">
                                                    <div>
                                                        <div class="row justify-start self-center">
                                                            <div class="text-bold">
                                                                Border color
                                                            </div>
                                                            <div class="q-ml-sm">
                                                                <color-picker :color-value="editData['borderColor']" @update:color-picker="(value) => editData['borderColor'] = value"></color-picker>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="row justify-start self-center">
                                                            <div class="text-bold">
                                                                Fill color
                                                            </div>
                                                            <div class="q-ml-sm">
                                                                <color-picker :color-value="editData['fillColor']" @update:color-picker="(value) => editData['fillColor'] = value"></color-picker>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row justify-between q-col-gutter-sm">
                                                    <div class="col-12 col-sm-6 col-md-4">
                                                        <text-field-input-element :clearable="false" data-type="int" label="Border width (px)" :value="editData['borderWidth']" min-value="0" @update:value="(value) => editData['borderWidth'] = value"></text-field-input-element>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-4">
                                                        <text-field-input-element :clearable="false" data-type="int" label="Point radius (px)" :value="editData['pointRadius']" min-value="1" @update:value="(value) => editData['pointRadius'] = value"></text-field-input-element>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-4">
                                                        <text-field-input-element :clearable="false" data-type="increment" label="Fill Opacity" :value="editData['opacity']" min-value="0" max-value="1" step=".1" @update:value="(value) => editData['opacity'] = value"></text-field-input-element>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="row justify-start">
                                                    <div class="col-12 col-sm-6 col-md-4">
                                                        <spatial-raster-color-scale-select :selected-color-scale="editData['colorScale']" @raster-color-scale-change="(value) => editData['colorScale'] = value"></spatial-raster-color-scale-select>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </template>
                            <template v-if="Number(layer.id) > 0">
                                <q-card flat bordered>
                                    <q-card-section>
                                        <div class="text-subtitle1 text-bold">Update Data File</div>
                                        <div class="q-mt-xs row justify-between">
                                            <div class="col-9">
                                                <file-picker-input-element label="Update File" :accepted-types="acceptedFileTypes" :value="uploadedFile" :validate-file-size="true" @update:file="(value) => processFileSelection(value)"></file-picker-input-element>
                                            </div>
                                            <div class="col-3 row justify-end">
                                                <q-btn color="secondary" @click="updateDataFile();" label="Update" :disabled="!uploadedFile" aria-label="Update data file" tabindex="0" />
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </template> -->
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const collectionStore = useCollectionStore();

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
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
            let exist = false;
            Object.keys(props.layer).forEach((key) => {
                if(props.layer[key] !== editData.value[key]){
                    exist = true;
                }
            });
            return exist;
        });
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
            return returnVal;
        });
        const showConfirmation = Vue.ref(false);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
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

        function updateEditData(key, value) {
            editData[key] = value;
        }

        Vue.onMounted(() => {
            if(props.field){
                setEditData();
            }
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editData,
            editDataValid,
            editsExist,
            showConfirmation,
            closePopup,
            updateEditData
        }
    }
};
