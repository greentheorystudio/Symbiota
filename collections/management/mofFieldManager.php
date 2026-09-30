<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Measurement or Fact Field Configuration Module</title>
        <meta name="description" content="Measurement or fact field configuration module for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/sortable.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/vuedraggable.umd.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const COLLID = <?php echo $collid; ?>;
            const draggable = vuedraggable;
        </script>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collectionId)" tabindex="0">Collection Control Panel</a> &gt;&gt;
                <span class="text-bold">Measurement or Fact Field Configurations</span>
            </div>
            <div class="q-pa-md">
                <div class="row justify-between q-px-md q-mb-sm">
                    <div class="text-h5 text-bold">Measurement or Fact Field Configurations</div>
                    <div>
                        <q-btn-toggle v-if="collectionData['datarecordingmethod'] === 'lot' || collectionData['datarecordingmethod'] === 'replicate'" v-model="selectedMofType" :options="mofTypeOptions" class="black-border" size="md" rounded unelevated toggle-color="primary" color="white" text-color="primary" aria-label="Measurement or fact data type" tabindex="0"></q-btn-toggle>
                    </div>
                </div>
                <div class="row justify-between q-px-md q-mb-sm">
                    <div class="col-7 row justify-start q-col-gutter-sm">
                        <div class="col-9">
                            <file-picker-input-element label="Upload Configuration File" :accepted-types="acceptedFileTypes" :value="uploadedFile" @update:file="(value) => processFileSelection(value)"></file-picker-input-element>
                        </div>
                        <div class="col-3">
                            <q-btn color="primary" @click="processConfigurationUpload();" label="Upload" :disabled="!uploadedFile" tabindex="0" />
                        </div>
                    </div>
                    <div class="col-5 row justify-end">
                        <div>
                            <q-btn color="secondary" @click="downloadConfigFile();" label="Download Configuration File" tabindex="0" />
                        </div>
                    </div>
                </div>
                <template v-if="isEditor">
                    <q-card>
                        <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab name="fields" label="Fields" no-caps></q-tab>
                            <q-tab name="layout" label="Layout" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel name="fields">
                                <div class="q-pa-sm">
                                    <div class="q-mb-md row justify-between">
                                        <div class="col-6 row q-gutter-sm">
                                            <template v-if="Object.keys(currentDataFields).length > 0">
                                                <div class="col-9">
                                                    <text-field-input-element class="col-grow" label="Field Set Label" :value="currentDataLabel" @update:value="processDataLabelChange" :clearable="false"></text-field-input-element>
                                                </div>
                                                <div>
                                                    <q-btn color="secondary" @click="saveConfiguredDataEdits();" label="Save" :disabled="!labelEditsExist" tabindex="0" />
                                                </div>
                                            </template>
                                        </div>
                                        <div class="row justify-end q-gutter-sm">
                                            <div>
                                                <q-btn color="primary" @click="openMofFieldEditorPopup();" label="Add Field" tabindex="0" />
                                            </div>
                                        </div>
                                    </div>
                                    <template v-if="currentDataFields.length > 0">
                                        <div class="column q-gutter-sm">
                                            <template v-for="field in currentDataFields">
                                                <q-card>
                                                    <q-card-section>
                                                        <div class="row justify-between q-gutter-sm">
                                                            <div class="text-subtitle1">
                                                                <span class="text-bold">{{ field['label'] }}  [</span>{{ field['key'] }}<span class="text-bold">]</span>
                                                            </div>
                                                            <div>
                                                                <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openMofFieldEditorPopup(field);" icon="far fa-edit" dense aria-label="Open field editor" tabindex="0">
                                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                        Open field editor
                                                                    </q-tooltip>
                                                                </q-btn>
                                                            </div>
                                                        </div>
                                                    </q-card-section>
                                                </q-card>
                                            </template>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="q-pa-md row justify-center text-h6 text-bold">
                                            There are currently no data fields to display
                                        </div>
                                    </template>
                                </div>
                            </q-tab-panel>
                            <q-tab-panel name="layout">
                                <mof-field-layout-tab ref="layoutTabRef" :field-type="selectedMofType" @update:layout="processUpdateLayoutData"></mof-field-layout-tab>
                            </q-tab-panel>
                        </q-tab-panels>
                    </q-card>
                </template>
            </div>
            <template v-if="showMofFieldEditorPopup">
                <mof-field-editor-popup
                    :field="editField"
                    :field-type="selectedMofType"
                    :show-popup="showMofFieldEditorPopup"
                    @create:field="processUpdateField"
                    @delete:field="processDeleteField"
                    @update:field="processUpdateField"
                    @data:update="setEditData"
                    @close:popup="closeMofFieldEditorPopup"
                ></mof-field-editor-popup>
            </template>
            <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/jsonFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxaKingdomSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxonRankSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/computedValueInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/filePickerInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRow.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRowGroup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/mofFieldEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/mofFieldLayoutTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const measurementOrFactFieldConfigurationModule = Vue.createApp({
                components: {
                    'confirmation-popup': confirmationPopup,
                    'file-picker-input-element': filePickerInputElement,
                    'mof-field-editor-popup': mofFieldEditorPopup,
                    'mof-field-layout-tab': mofFieldLayoutTab,
                    'text-field-input-element': textFieldInputElement
                },
                setup() {
                    const { hideWorking, parseFile, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const collectionStore = useCollectionStore();
                    const searchStore = useSearchStore();

                    const acceptedFileTypes = ['json'];
                    const clientRoot = baseStore.getClientRoot;
                    const collectionData = Vue.computed(() => collectionStore.getCollectionData);
                    const collectionId = Vue.computed(() => collectionStore.getCollectionId);
                    const collId = COLLID;
                    const confirmationPopupRef = Vue.ref(null);
                    const currentDataFields = Vue.computed(() => {
                        let returnVal;
                        if(selectedMofType.value === 'occurrence'){
                            returnVal = occurrenceDataFieldArr.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            returnVal = eventDataFieldArr.value;
                        }
                        else{
                            returnVal = locationDataFieldArr.value;
                        }
                        return returnVal;
                    });
                    const currentDataFieldsLayoutData = Vue.computed(() => {
                        let returnVal;
                        if(selectedMofType.value === 'occurrence'){
                            returnVal = occurrenceDataFieldsLayoutDataEdit.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            returnVal = eventDataFieldsLayoutDataEdit.value;
                        }
                        else{
                            returnVal = locationDataFieldsLayoutDataEdit.value;
                        }
                        return returnVal;
                    });
                    const currentDataLabel = Vue.computed(() => {
                        let returnVal;
                        if(selectedMofType.value === 'occurrence'){
                            returnVal = occurrenceDataLabelEdit.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            returnVal = eventDataLabelEdit.value;
                        }
                        else{
                            returnVal = locationDataLabelEdit.value;
                        }
                        return returnVal;
                    });
                    const editField = Vue.ref(null);
                    const eventDataFieldArr = Vue.computed(() => {
                        const returnArr = [];
                        Object.keys(eventDataFieldsEdit.value).forEach((field) => {
                            const fieldData = Object.assign({}, eventDataFieldsEdit.value[field]);
                            fieldData['key'] = field;
                            returnArr.push(fieldData);
                        });
                        returnArr.sort((a, b) => {
                            return a['label'].localeCompare(b['label']);
                        });
                        return returnArr;
                    });
                    const eventDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
                    const eventDataFieldsEdit = Vue.ref({});
                    const eventDataFieldsLayoutData = Vue.computed(() => collectionStore.getEventMofDataFieldsLayoutData);
                    const eventDataFieldsLayoutDataEdit = Vue.ref([]);
                    const eventDataLabel = Vue.computed(() => collectionStore.getEventMofDataLabel);
                    const eventDataLabelEdit = Vue.ref('');
                    const eventLabelEditsExist = Vue.computed(() => {
                        return eventDataLabel.value !== eventDataLabelEdit.value;
                    });
                    const eventLayoutDataEditsExist = Vue.computed(() => {
                        return JSON.stringify(eventDataFieldsLayoutData.value) !== JSON.stringify(eventDataFieldsLayoutDataEdit.value);
                    });
                    const isEditor = Vue.computed(() => {
                        return collectionStore.getCollectionPermissions.includes('CollAdmin');
                    });
                    const labelEditsExist = Vue.computed(() => {
                        let returnVal;
                        if(selectedMofType.value === 'occurrence'){
                            returnVal = occurrenceLabelEditsExist.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            returnVal = eventLabelEditsExist.value;
                        }
                        else{
                            returnVal = locationLabelEditsExist.value;
                        }
                        return returnVal;
                    });
                    const layoutEditsExist = Vue.computed(() => {
                        let returnVal;
                        if(selectedMofType.value === 'occurrence'){
                            returnVal = occurrenceLayoutDataEditsExist.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            returnVal = eventLayoutDataEditsExist.value;
                        }
                        else{
                            returnVal = locationLayoutDataEditsExist.value;
                        }
                        return returnVal;
                    });
                    const layoutTabRef = Vue.ref(null);
                    const locationDataFieldArr = Vue.computed(() => {
                        const returnArr = [];
                        Object.keys(locationDataFieldsEdit.value).forEach((field) => {
                            const fieldData = Object.assign({}, locationDataFieldsEdit.value[field]);
                            fieldData['key'] = field;
                            returnArr.push(fieldData);
                        });
                        returnArr.sort((a, b) => {
                            return a['label'].localeCompare(b['label']);
                        });
                        return returnArr;
                    });
                    const locationDataFields = Vue.computed(() => collectionStore.getLocationMofDataFields);
                    const locationDataFieldsEdit = Vue.ref({});
                    const locationDataFieldsLayoutData = Vue.computed(() => collectionStore.getLocationMofDataFieldsLayoutData);
                    const locationDataFieldsLayoutDataEdit = Vue.ref([]);
                    const locationDataLabel = Vue.computed(() => collectionStore.getLocationMofDataLabel);
                    const locationDataLabelEdit = Vue.ref('');
                    const locationLabelEditsExist = Vue.computed(() => {
                        return locationDataLabel.value !== locationDataLabelEdit.value;
                    });
                    const locationLayoutDataEditsExist = Vue.computed(() => {
                        return JSON.stringify(locationDataFieldsLayoutData.value) !== JSON.stringify(locationDataFieldsLayoutDataEdit.value);
                    });
                    const mofTypeOptions = [
                        {label: 'Occurrence', value: 'occurrence'},
                        {label: 'Event', value: 'event'},
                        {label: 'Location', value: 'location'}
                    ];
                    const occurrenceDataFieldArr = Vue.computed(() => {
                        const returnArr = [];
                        Object.keys(occurrenceDataFieldsEdit.value).forEach((field) => {
                            const fieldData = Object.assign({}, occurrenceDataFieldsEdit.value[field]);
                            fieldData['key'] = field;
                            returnArr.push(fieldData);
                        });
                        returnArr.sort((a, b) => {
                            return a['label'].localeCompare(b['label']);
                        });
                        return returnArr;
                    });
                    const occurrenceDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
                    const occurrenceDataFieldsEdit = Vue.ref({});
                    const occurrenceDataFieldsLayoutData = Vue.computed(() => collectionStore.getOccurrenceMofDataFieldsLayoutData);
                    const occurrenceDataFieldsLayoutDataEdit = Vue.ref([]);
                    const occurrenceDataLabel = Vue.computed(() => collectionStore.getOccurrenceMofDataLabel);
                    const occurrenceDataLabelEdit = Vue.ref('');
                    const occurrenceLabelEditsExist = Vue.computed(() => {
                        return occurrenceDataLabel.value !== occurrenceDataLabelEdit.value;
                    });
                    const occurrenceLayoutDataEditsExist = Vue.computed(() => {
                        return JSON.stringify(occurrenceDataFieldsLayoutData.value) !== JSON.stringify(occurrenceDataFieldsLayoutDataEdit.value);
                    });
                    const otherMofDataFields = Vue.computed(() => {
                        const fieldData = {};
                        if(selectedMofType.value !== 'occurrence'){
                            Object.keys(occurrenceDataFields.value).forEach((field) => {
                                fieldData[field] = occurrenceDataFields.value[field];
                            });
                        }
                        if(selectedMofType.value !== 'event'){
                            Object.keys(eventDataFields.value).forEach((field) => {
                                fieldData[field] = eventDataFields.value[field];
                            });
                        }
                        if(selectedMofType.value !== 'location'){
                            Object.keys(locationDataFields.value).forEach((field) => {
                                fieldData[field] = locationDataFields.value[field];
                            });
                        }
                        return fieldData;
                    });
                    const selectedMofType = Vue.ref('occurrence');
                    const showMofFieldEditorPopup = Vue.ref(false);
                    const tab = Vue.ref('fields');
                    const updateData = Vue.computed(() => {
                        const updateData = {};
                        if(selectedMofType.value === 'occurrence'){
                            updateData['dataFields'] = occurrenceDataFieldsEdit.value;
                            updateData['dataLayout'] = occurrenceDataFieldsLayoutDataEdit.value;
                            updateData['dataLabel'] = occurrenceDataLabelEdit.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            updateData['dataFields'] = eventDataFieldsEdit.value;
                            updateData['dataLayout'] = eventDataFieldsLayoutDataEdit.value;
                            updateData['dataLabel'] = eventDataLabelEdit.value;
                        }
                        else{
                            updateData['dataFields'] = locationDataFieldsEdit.value;
                            updateData['dataLayout'] = locationDataFieldsLayoutDataEdit.value;
                            updateData['dataLabel'] = locationDataLabelEdit.value;
                        }
                        return updateData;
                    });
                    const uploadedData = Vue.ref({});
                    const uploadedFile = Vue.ref(null);

                    Vue.watch(selectedMofType, () => {
                        tab.value = 'fields';
                    });

                    function closeMofFieldEditorPopup() {
                        editField.value = null;
                        showMofFieldEditorPopup.value = false;
                    }

                    function downloadConfigFile() {
                        const filename = 'coll' + collectionData.value['collid'] + '_' + selectedMofType.value + '_MoF-config_' + searchStore.getDateTimeString + '.json';
                        const blob = new Blob([JSON.stringify(updateData.value)], {type: 'application/json'});
                        const elem = window.document.createElement('a');
                        elem.href = window.URL.createObjectURL(blob);
                        elem.download = filename;
                        document.body.appendChild(elem);
                        elem.click();
                        document.body.removeChild(elem);
                    }

                    function openMofFieldEditorPopup(field = null) {
                        editField.value = field ? Object.assign({}, field) : null;
                        showMofFieldEditorPopup.value = true;
                    }

                    function processConfigurationUpload() {
                        const confirmText = 'This will import the fields, layout, and field set label from the file you selected. Any ' + selectedMofType.value + ' fields with the same name as fields in the import will be replaced by those fields. All layout data and the field set label will also be replaced. This cannot be undone. Do you want to continue?';
                        confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                            if(val){
                                showWorking();
                                validateUploadFieldNames();
                            }
                        }});
                    }

                    function processDataLabelChange(value) {
                        if(!value || value === ''){
                            value = 'Measurement or Fact Data';
                        }
                        if(selectedMofType.value === 'occurrence'){
                            occurrenceDataLabelEdit.value = value;
                        }
                        else if(selectedMofType.value === 'event'){
                            eventDataLabelEdit.value = value;
                        }
                        else{
                            locationDataLabelEdit.value = value;
                        }
                    }

                    function processDeleteField(field) {
                        const fieldName = field['key'];
                        if(selectedMofType.value === 'occurrence'){
                            removeFieldFromLayoutData(fieldName, occurrenceDataFieldsLayoutDataEdit.value);
                            delete occurrenceDataFieldsEdit.value[fieldName];
                        }
                        else if(selectedMofType.value === 'event'){
                            removeFieldFromLayoutData(fieldName, eventDataFieldsLayoutDataEdit.value);
                            delete eventDataFieldsEdit.value[fieldName];
                        }
                        else{
                            removeFieldFromLayoutData(fieldName, locationDataFieldsLayoutDataEdit.value);
                            delete locationDataFieldsEdit.value[fieldName];
                        }
                        showMofFieldEditorPopup.value = false;
                        saveConfiguredDataEdits();
                    }

                    function processFileSelection(file) {
                        uploadedData.value = Object.assign({}, {});
                        uploadedFile.value = null;
                        if(file){
                            parseFile(file[0], (fileContents) => {
                                if(fileContents && fileContents.length > 0){
                                    try {
                                        const jsonData = JSON.parse(fileContents);
                                        if(jsonData && typeof jsonData === 'object' && jsonData.hasOwnProperty('dataFields') && jsonData['dataFields'] && Object.keys(jsonData['dataFields']).length > 0){
                                            uploadedData.value = Object.assign({}, jsonData);
                                            uploadedFile.value = file[0];
                                        }
                                        else{
                                            showNotification('negative', 'The file you selected does not include any field data.');
                                        }
                                    }
                                    catch(error) {
                                        showNotification('negative', 'The file you selected is not a valid configuration file.');
                                    }
                                }
                                else{
                                    showNotification('negative', 'The file you selected appears to be empty.');
                                }
                            });
                        }
                    }

                    function processUpdateField(field) {
                        const fieldName = field['key'];
                        delete field.key;
                        if(selectedMofType.value === 'occurrence'){
                            occurrenceDataFieldsEdit.value[fieldName] = Object.assign({}, field);
                        }
                        else if(selectedMofType.value === 'event'){
                            eventDataFieldsEdit.value[fieldName] = Object.assign({}, field);
                        }
                        else{
                            locationDataFieldsEdit.value[fieldName] = Object.assign({}, field);
                        }
                        showMofFieldEditorPopup.value = false;
                        saveConfiguredDataEdits();
                    }

                    function processUpdateLayoutData(layoutArr) {
                        if(selectedMofType.value === 'occurrence'){
                            occurrenceDataFieldsLayoutDataEdit.value = layoutArr.slice();
                        }
                        else if(selectedMofType.value === 'event'){
                            eventDataFieldsLayoutDataEdit.value = layoutArr.slice();
                        }
                        else{
                            locationDataFieldsLayoutDataEdit.value = layoutArr.slice();
                        }
                        saveConfiguredDataEdits();
                    }

                    function removeFieldFromLayoutData(fieldName, layoutData) {
                        layoutData.forEach((layoutObj) => {
                            if(layoutObj['type'] === 'dataFieldRow'){
                                layoutObj['fields'].forEach((fieldObj) => {
                                    if(fieldObj['fieldName'] === fieldName){
                                        const index = layoutObj['fields'].indexOf(fieldObj);
                                        layoutObj['fields'].splice(index, 1);
                                    }
                                });
                            }
                            else if(layoutObj['type'] === 'dataFieldRowGroup'){
                                layoutObj['rows'].forEach((rowObj) => {
                                    rowObj['fields'].forEach((fieldObj) => {
                                        if(fieldObj['fieldName'] === fieldName){
                                            const index = rowObj['fields'].indexOf(fieldObj);
                                            rowObj['fields'].splice(index, 1);
                                        }
                                    });
                                });
                            }
                        });
                    }

                    function saveConfiguredDataEdits() {
                        showWorking();
                        let dataKey;
                        if(selectedMofType.value === 'occurrence'){
                            dataKey = 'occurrenceMofExtension';
                        }
                        else if(selectedMofType.value === 'event'){
                            dataKey = 'eventMofExtension';
                        }
                        else{
                            dataKey = 'locationMofExtension';
                        }
                        collectionStore.updateConfiguredPropertyValue(dataKey, updateData.value, (res) => {
                            hideWorking();
                            if(!res){
                                showNotification('positive', 'Changes saved.');
                            }
                            else{
                                showNotification('negative', 'There was an error saving the changes.');
                            }
                            setEditData();
                            if(tab.value === 'layout'){
                                layoutTabRef.value.setEditData();
                            }
                        });
                    }

                    function setEditData() {
                        eventDataFieldsEdit.value = Object.assign({}, eventDataFields.value);
                        eventDataFieldsLayoutDataEdit.value = eventDataFieldsLayoutData.value ? eventDataFieldsLayoutData.value.slice() : [];
                        eventDataLabelEdit.value = eventDataLabel.value;
                        locationDataFieldsEdit.value = Object.assign({}, locationDataFields.value);
                        locationDataFieldsLayoutDataEdit.value = locationDataFieldsLayoutData.value ? locationDataFieldsLayoutData.value.slice() : [];
                        locationDataLabelEdit.value = locationDataLabel.value;
                        occurrenceDataFieldsEdit.value = Object.assign({}, occurrenceDataFields.value);
                        occurrenceDataFieldsLayoutDataEdit.value = occurrenceDataFieldsLayoutData.value ? occurrenceDataFieldsLayoutData.value.slice() : [];
                        occurrenceDataLabelEdit.value = occurrenceDataLabel.value;
                    }

                    function setUploadedData() {
                        Object.keys(uploadedData.value['dataFields']).forEach((field) => {
                            if(selectedMofType.value === 'occurrence'){
                                occurrenceDataFieldsEdit.value[field] = Object.assign({}, uploadedData.value['dataFields'][field]);
                            }
                            else if(selectedMofType.value === 'event'){
                                eventDataFieldsEdit.value[field] = Object.assign({}, uploadedData.value['dataFields'][field]);
                            }
                            else{
                                locationDataFieldsEdit.value[field] = Object.assign({}, uploadedData.value['dataFields'][field]);
                            }
                        });
                        if(selectedMofType.value === 'occurrence'){
                            occurrenceDataFieldsLayoutDataEdit.value = uploadedData.value['dataLayout'].slice();
                            occurrenceDataLabelEdit.value = uploadedData.value['dataLabel'];
                        }
                        else if(selectedMofType.value === 'event'){
                            eventDataFieldsLayoutDataEdit.value = uploadedData.value['dataLayout'].slice();
                            eventDataLabelEdit.value = uploadedData.value['dataLabel'];
                        }
                        else{
                            locationDataFieldsLayoutDataEdit.value = uploadedData.value['dataLayout'].slice();
                            locationDataLabelEdit.value = uploadedData.value['dataLabel'];
                        }
                        uploadedData.value = Object.assign({}, {});
                        uploadedFile.value = null;
                        saveConfiguredDataEdits();
                    }

                    function validateUploadCalculatedFields() {
                        const invalidFieldArr = [];
                        Object.keys(uploadedData.value['dataFields']).forEach((field) => {
                            if(field['dataType'] === 'calculated'){
                                field['fields'].forEach((cField) => {
                                    if(!uploadedData.value['dataFields'].hasOwnProperty(cField) && !otherMofDataFields.value.hasOwnProperty(cField)){
                                        invalidFieldArr.push(field);
                                        delete uploadedData.value['dataFields'][field];
                                        removeFieldFromLayoutData(field, uploadedData.value['dataLayout']);
                                    }
                                });
                            }
                        });
                        if(invalidFieldArr.length > 0){
                            hideWorking();
                            const confirmText = 'The following calculated fields cannot be uploaded because they include fields that do not exist in their calculation: ' + invalidFieldArr.join(', ') + ' Do you want to continue?';
                            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                                if(val){
                                    showWorking();
                                    setUploadedData();
                                }
                            }});
                        }
                        else{
                            setUploadedData();
                        }
                    }

                    function validateUploadFieldNames() {
                        const invalidFieldArr = [];
                        Object.keys(uploadedData.value['dataFields']).forEach((field) => {
                            if(otherMofDataFields.value.hasOwnProperty(field)){
                                invalidFieldArr.push(field);
                                delete uploadedData.value['dataFields'][field];
                                removeFieldFromLayoutData(field, uploadedData.value['dataLayout']);
                            }
                        });
                        if(invalidFieldArr.length > 0){
                            hideWorking();
                            const confirmText = 'The following fields cannot be uploaded because there is already a field with the same name in a different measurement or fact field group: ' + invalidFieldArr.join(', ') + ' Do you want to continue?';
                            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                                if(val){
                                    showWorking();
                                    validateUploadCalculatedFields();
                                }
                            }});
                        }
                        else{
                            validateUploadCalculatedFields();
                        }
                    }

                    Vue.onMounted(() => {
                        collectionStore.setCollectionMofFieldDefinitions();
                        collectionStore.setCollection(collId, () => {
                            if(Number(collId) > 0 && !isEditor.value){
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                            else{
                                setEditData();
                            }
                        });
                    });

                    return {
                        acceptedFileTypes,
                        clientRoot,
                        collectionData,
                        collectionId,
                        confirmationPopupRef,
                        currentDataFields,
                        currentDataFieldsLayoutData,
                        currentDataLabel,
                        editField,
                        isEditor,
                        labelEditsExist,
                        layoutEditsExist,
                        layoutTabRef,
                        mofTypeOptions,
                        selectedMofType,
                        showMofFieldEditorPopup,
                        tab,
                        uploadedFile,
                        closeMofFieldEditorPopup,
                        downloadConfigFile,
                        openMofFieldEditorPopup,
                        processConfigurationUpload,
                        processDataLabelChange,
                        processDeleteField,
                        processFileSelection,
                        processUpdateField,
                        processUpdateLayoutData,
                        saveConfiguredDataEdits,
                        setEditData
                    }
                }
            });
            measurementOrFactFieldConfigurationModule.use(Quasar, { config: {} });
            measurementOrFactFieldConfigurationModule.use(Pinia.createPinia());
            measurementOrFactFieldConfigurationModule.mount('#mainContainer');
        </script>
    </body>
</html>
