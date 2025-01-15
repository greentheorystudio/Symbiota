const occurrenceDataUploadModule = {
    props: {
        collid: {
            type: Number,
            default: null
        }
    },
    template: `
        <div class="processor-container">
            <div class="processor-control-container">
                <q-card class="processor-control-card">
                    <q-list class="processor-control-accordion">
                        <q-expansion-item class="overflow-hidden" group="expansiongroup" label="Configuration" header-class="bg-grey-3 text-bold" default-opened>
                            <q-card class="accordion-panel">
                                <q-card-section>
                                    <div class="column q-col-gutter-sm">
                                        <div class="row justify-between q-col-gutter-sm">
                                            <div class="col-12 col-sm-9">
                                                <template v-if="collectionDataUploadParametersArr.length > 0">
                                                    <selector-input-element :disabled="!!currentProcess" label="Select Upload Profile" :options="collectionDataUploadParametersArr" option-value="uspid" option-label="title" :value="collectionDataUploadParametersId" @update:value="(value) => processParameterProfileSelection(value)"></selector-input-element>
                                                </template>
                                            </div>
                                            <div class="col-12 col-sm-3 row justify-end">
                                                <div>
                                                    <q-btn color="secondary" @click="showCollectionDataUploadParametersEditorPopup = true" :label="Number(collectionDataUploadParametersId) > 0 ? 'Edit' : 'Create'" :disabled="!!currentProcess" dense />
                                                </div>
                                            </div>
                                        </div>
                                        <collection-data-upload-parameters-field-module :disabled="!!currentProcess"></collection-data-upload-parameters-field-module>
                                        <div v-if="Number(profileData.uploadtype) === 6" class="row">
                                            <div class="col-grow">
                                                <file-picker-input-element :disabled="!!currentProcess" :accepted-types="acceptedFileTypes" :value="uploadedFile" :validate-file-size="false" @update:file="(value) => uploadedFile = value[0]"></file-picker-input-element>
                                            </div>
                                        </div>
                                        <div class="row justify-end">
                                            <div>
                                                <q-btn color="secondary" @click="initializeUpload();" label="Initialize Upload" :disabled="!!currentProcess" dense />
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </q-expansion-item>
                        <q-separator></q-separator>
                        <q-expansion-item :model-value="currentTab === 'mapping'" class="overflow-hidden" group="expansiongroup" label="Field Mapping" header-class="bg-grey-3 text-bold" :disable="currentTab !== 'mapping' && currentTab !== 'summary'">
                            <q-card class="accordion-panel">
                                <q-card-section>
                                    <div class="column">
                                        <template v-if="flatFileMode">
                                            <div class="q-mb-sm q-pl-sm">
                                                <span class="text-body1 text-bold">Occurrence records</span> <span class="cursor-pointer" @click="openFieldMapperPopup('flat-file');">(view mapping)</span>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="q-mb-sm row q-gutter-sm">
                                                <div class="text-body1 text-bold">Occurrence Records</div>
                                                <div class="cursor-pointer" @click="openFieldMapperPopup('occurrence');">(view mapping)</div>
                                            </div>
                                            <template v-if="determinationDataIncluded">
                                                <div class="row q-gutter-sm">
                                                    <checkbox-input-element :value="includeDeterminationData" @update:value="(value) => includeDeterminationData = value"></checkbox-input-element>
                                                    <div class="text-body1 text-bold">Import Identification History</div>
                                                    <div class="cursor-pointer" @click="openFieldMapperPopup('determination');">(view mapping)</div>
                                                </div>
                                            </template>
                                            <template v-if="multimediaDataIncluded">
                                                <div class="row q-gutter-sm">
                                                    <checkbox-input-element :value="includeMultimediaData" @update:value="(value) => includeMultimediaData = value"></checkbox-input-element>
                                                    <div class="text-body1 text-bold">Import Media Records</div>
                                                    <div class="cursor-pointer" @click="openFieldMapperPopup('multimedia');">(view mapping)</div>
                                                </div>
                                            </template>
                                            <template v-if="mofDataIncluded">
                                                <div class="row q-gutter-sm">
                                                    <checkbox-input-element :value="includeMofData" @update:value="(value) => includeMofData = value"></checkbox-input-element>
                                                    <div class="text-body1 text-bold">Import Measurement or Fact Records</div>
                                                    <div class="cursor-pointer" @click="openFieldMapperPopup('mof');">(view mapping)</div>
                                                </div>
                                            </template>
                                        </template>
                                        <div class="q-mt-sm">
                                            <selector-input-element label="Incoming Records Processing Status" :options="processingStatusOptions" :value="selectedProcessingStatus" @update:value="(value) => selectedProcessingStatus = value" :clearable="true"></selector-input-element>
                                        </div>
                                        <div class="q-mt-sm row justify-end q-gutter-sm">
                                            <div v-if="collectionDataUploadParametersId">
                                                <q-btn color="secondary" @click="saveMapping();" label="Save Mapping" :disabled="currentTab !== 'mapping' || currentProcess" dense />
                                            </div>
                                            <div>
                                                <q-btn color="secondary" @click="startUpload();" label="Start Upload" :disabled="currentTab !== 'mapping' || currentProcess" dense />
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </q-expansion-item>
                        <q-separator></q-separator>
                        <q-expansion-item :model-value="currentTab === 'summary'" class="overflow-hidden" group="expansiongroup" label="Summary" header-class="bg-grey-3 text-bold" :disable="currentTab !== 'summary' || currentProcess">
                            <q-card class="accordion-panel">
                                <q-card-section>
                                    
                                </q-card-section>
                            </q-card>
                        </q-expansion-item>
                    </q-list>
                </q-card>
            </div>

            <div class="processor-display-container">
                <q-card class="bg-grey-3 q-pa-sm">
                    <q-scroll-area ref="procDisplayScrollAreaRef" class="bg-grey-1 processor-display" @scroll="setScroller">
                        <q-list dense>
                            <template v-if="!currentProcess && processorDisplayCurrentIndex > 0">
                                <q-item>
                                    <q-item-section>
                                        <div><a class="text-bold cursor-pointer" @click="processorDisplayScrollUp();">Show previous 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                            <q-item v-for="proc in processorDisplayArr">
                                <q-item-section>
                                    <div>{{ proc.procText }} <q-spinner v-if="proc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                    <template v-if="!proc.loading && proc.resultText">
                                        <div v-if="proc.result === 'success'" class="q-ml-sm text-weight-bold text-green-9">
                                            {{proc.resultText}}
                                        </div>
                                        <div v-if="proc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                            {{proc.resultText}}
                                        </div>
                                    </template>
                                    <template v-if="proc.type === 'multi' && proc.subs.length">
                                        <div class="q-ml-sm">
                                            <div v-for="subproc in proc.subs">
                                                <template v-if="subproc.type === 'text' || subproc.type === 'undo'">
                                                    <div>{{ subproc.procText }} <q-spinner v-if="subproc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                                    <template v-if="!subproc.loading && subproc.resultText">
                                                        <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="text-weight-bold text-green-9">
                                                            <span class="text-weight-bold text-green-9">{{subproc.resultText}}</span>
                                                        </div>
                                                        <div v-if="subproc.result === 'error'" class="text-weight-bold text-negative">
                                                            {{subproc.resultText}}
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </q-item-section>
                            </q-item>
                            <template v-if="!currentProcess && processorDisplayCurrentIndex < processorDisplayIndex">
                                <q-item>
                                    <q-item-section>
                                        <div><a class="text-bold cursor-pointer" @click="processorDisplayScrollDown();">Show next 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-list>
                    </q-scroll-area>
                </q-card>
            </div>
        </div>
        <template v-if="showCollectionDataUploadParametersEditorPopup">
            <collection-data-upload-parameters-editor-popup
                :show-popup="showCollectionDataUploadParametersEditorPopup"
                @close:popup="showCollectionDataUploadParametersEditorPopup = false"
            ></collection-data-upload-parameters-editor-popup>
        </template>
        <template v-if="showFieldMapperPopup">
            <field-mapper-popup
                :field-mapping="fieldMapperFieldMapping"
                :source-fields="fieldMapperSourceFields"
                :target-fields="fieldMapperTargetFields"
                :show-popup="showFieldMapperPopup"
                @update:field-mapping="processFieldMapperUpdate"
                @close:popup="showFieldMapperPopup = false"
            ></field-mapper-popup>
        </template>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'collection-data-upload-parameters-editor-popup': collectionDataUploadParametersEditorPopup,
        'collection-data-upload-parameters-field-module': collectionDataUploadParametersFieldModule,
        'field-mapper-popup': fieldMapperPopup,
        'file-picker-input-element': filePickerInputElement,
        'selector-input-element': selectorInputElement
    },
    setup(props) {
        const { parseCsvFile, showNotification } = useCore();
        const baseStore = useBaseStore();
        const collectionDataUploadParametersStore = useCollectionDataUploadParametersStore();
        const collectionStore = useCollectionStore();
        
        const acceptedFileTypes = ['csv','geojson','txt','zip'];
        const collectionDataUploadParametersArr = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersArr);
        const collectionDataUploadParametersId = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersID);
        const currentProcess = Vue.ref(null);
        const currentTab = Vue.ref('configuration');
        const determinationDataIncluded = Vue.ref(false);
        const eventMofDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
        const fieldMapperPopupType = Vue.ref(null);
        const fieldMapperFieldMapping = Vue.computed(() => {
            if(fieldMapperPopupType.value === 'occurrence' || fieldMapperPopupType.value === 'flat-file'){
                return fieldMappingDataOccurrence.value;
            }
            else if(fieldMapperPopupType.value === 'determination'){
                return fieldMappingDataDetermiation.value;
            }
            else if(fieldMapperPopupType.value === 'multimedia'){
                return fieldMappingDataMedia.value;
            }
            else if(fieldMapperPopupType.value === 'mof'){
                return fieldMappingDataMof.value;
            }
            else{
                return null;
            }
        });
        const fieldMapperSourceFields = Vue.computed(() => {
            if(fieldMapperPopupType.value === 'occurrence'){
                return sourceDataFieldsOccurrence.value;
            }
            else if(fieldMapperPopupType.value === 'flat-file'){
                return sourceDataFieldsFlatFile.value;
            }
            else if(fieldMapperPopupType.value === 'determination'){
                return sourceDataFieldsDetermination.value;
            }
            else if(fieldMapperPopupType.value === 'multimedia'){
                return sourceDataFieldsMultimedia.value;
            }
            else if(fieldMapperPopupType.value === 'mof'){
                return sourceDataFieldsMof.value;
            }
            else{
                return null;
            }
        });
        const fieldMapperTargetFields = Vue.computed(() => {
            if(fieldMapperPopupType.value === 'occurrence'){
                return symbiotaFieldOptionsOccurrence.value;
            }
            else if(fieldMapperPopupType.value === 'flat-file'){
                return symbiotaFieldOptionsFlatFile.value;
            }
            else if(fieldMapperPopupType.value === 'determination'){
                return symbiotaFieldOptionsDetermination.value;
            }
            else if(fieldMapperPopupType.value === 'multimedia'){
                return symbiotaFieldOptionsMedia.value;
            }
            else if(fieldMapperPopupType.value === 'mof'){
                return symbiotaFieldOptionsMof.value;
            }
            else{
                return null;
            }
        });
        const fieldMappingDataDetermiation = Vue.ref({});
        const fieldMappingDataMedia = Vue.ref({});
        const fieldMappingDataMof = Vue.ref({});
        const fieldMappingDataOccurrence = Vue.ref({});
        const flatFileMode = Vue.ref(false);
        const flatFileMofData = Vue.ref([]);
        const flatFileOccurrenceData = Vue.ref([]);
        const includeDeterminationData = Vue.ref(true);
        const includeMultimediaData = Vue.ref(true);
        const includeMofData = Vue.ref(true);
        const localDwcaFileArr = Vue.ref([]);
        const localDwcaServerPath = Vue.ref(null);
        const maxUploadFilesize = baseStore.getMaxUploadFilesize;
        const multimediaDataIncluded = Vue.ref(false);
        const mofDataIncluded = Vue.ref(false);
        const occurrenceMofDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processingStatusOptions = Vue.computed(() => baseStore.getOccurrenceProcessingStatusOptions);
        const processorDisplayArr = Vue.reactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const profileData = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersData);
        const recordsUploadedDetermination = Vue.ref(0);
        const recordsUploadedMof = Vue.ref(0);
        const recordsUploadedMultimedia = Vue.ref(0);
        const recordsUploadedOccurrence = Vue.ref(0);
        const savedMappingDataDetermiation = Vue.ref({});
        const savedMappingDataMedia = Vue.ref({});
        const savedMappingDataMof = Vue.ref({});
        const savedMappingDataOccurrence = Vue.ref({});
        const scrollProcess = Vue.ref(null);
        const selectedProcessingStatus = Vue.ref(null);
        const showCollectionDataUploadParametersEditorPopup = Vue.ref(false);
        const showFieldMapperPopup = Vue.ref(null);
        const skipDeterminationFields = ['updid','occid','collid','dbpk','tid','initialtimestamp'];
        const skipMediaFields = ['upmid','tid','occid','collid','dbpk','username','initialtimestamp'];
        const skipOccurrenceFields = ['upspid','occid','collid','dbpk','institutionid','collectionid','datasetid','tid',
            'eventid','eventdbpk','locationid','initialtimestamp'];
        const sourceDataFieldsDetermination = Vue.ref({});
        const sourceDataFieldsFlatFile = Vue.ref({});
        const sourceDataFieldsMof = Vue.ref({});
        const sourceDataFieldsMultimedia = Vue.ref({});
        const sourceDataFieldsOccurrence = Vue.ref({});
        const sourceDataFilesDetermination = Vue.ref([]);
        const sourceDataFilesMof = Vue.ref([]);
        const sourceDataFilesMultimedia = Vue.ref([]);
        const sourceDataFilesOccurrence = Vue.ref([]);
        const sourceDataFlatFile = Vue.ref([]);
        const symbiotaFieldOptionsDetermination = Vue.ref([]);
        const symbiotaFieldOptionsFlatFile = Vue.ref([]);
        const symbiotaFieldOptionsMedia = Vue.ref([]);
        const symbiotaFieldOptionsMof = Vue.ref([
            {value: 'unmapped', label: 'UNMAPPED'},
            {value: 'field', label: 'measurementtype'},
            {value: 'datavalue', label: 'measurementvalue'}
        ]);
        const symbiotaFieldOptionsOccurrence = Vue.ref([]);
        const uploadedFile = Vue.ref(null);
        
        function addProcessToProcessorDisplay(processObj) {
            processorDisplayArr.push(processObj);
            if(processorDisplayArr.length > 100){
                const precessorArrSegment = processorDisplayArr.slice(0, 100);
                processorDisplayDataArr = processorDisplayDataArr.concat(precessorArrSegment);
                processorDisplayArr.splice(0, 100);
                processorDisplayIndex.value++;
                processorDisplayCurrentIndex.value = processorDisplayIndex.value;
            }
        }

        function addSubprocessToProcessorDisplay(type, text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentProcess.value);
            parentProcObj['subs'].push(getNewSubprocessObject(type,text));
            const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === currentProcess.value);
            if(dataParentProcObj){
                dataParentProcObj['subs'].push(getNewSubprocessObject(type,text));
            }
        }

        function adjustUIEnd() {
            processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
        }

        function adjustUIStart() {
            processorDisplayArr.length = 0;
            processorDisplayDataArr = [];
            processorDisplayCurrentIndex.value = 0;
            processorDisplayIndex.value = 0;
        }

        function clearData() {
            flatFileMode.value = false;
            includeDeterminationData.value = true;
            includeMultimediaData.value = true;
            includeMofData.value = true;
            determinationDataIncluded.value = false;
            multimediaDataIncluded.value = false;
            mofDataIncluded.value = false;
            fieldMappingDataDetermiation.value = Object.assign({}, {});
            fieldMappingDataMedia.value = Object.assign({}, {});
            fieldMappingDataMof.value = Object.assign({}, {});
            fieldMappingDataOccurrence.value = Object.assign({}, {});
            flatFileMofData.value.length = 0;
            flatFileOccurrenceData.value.length = 0;
            savedMappingDataDetermiation.value = Object.assign({}, {});
            savedMappingDataMedia.value = Object.assign({}, {});
            savedMappingDataMof.value = Object.assign({}, {});
            savedMappingDataOccurrence.value = Object.assign({}, {});
            symbiotaFieldOptionsDetermination.value.length = 0;
            symbiotaFieldOptionsFlatFile.value.length = 0;
            symbiotaFieldOptionsMedia.value.length = 0;
            symbiotaFieldOptionsOccurrence.value.length = 0;
            symbiotaFieldOptionsDetermination.value.push({value: 'unmapped', label: 'UNMAPPED'});
            symbiotaFieldOptionsMedia.value.push({value: 'unmapped', label: 'UNMAPPED'});
            symbiotaFieldOptionsOccurrence.value.push({value: 'unmapped', label: 'UNMAPPED'});
            sourceDataFieldsDetermination.value = Object.assign({}, {});
            sourceDataFieldsFlatFile.value = Object.assign({}, {});
            sourceDataFieldsMof.value = Object.assign({}, {});
            sourceDataFieldsMultimedia.value = Object.assign({}, {});
            sourceDataFieldsOccurrence.value = Object.assign({}, {});
            sourceDataFilesDetermination.value.length = 0;
            sourceDataFilesMof.value.length = 0;
            sourceDataFilesMultimedia.value.length = 0;
            sourceDataFilesOccurrence.value.length = 0;
            sourceDataFlatFile.value.length = 0;
            recordsUploadedDetermination.value = 0;
            recordsUploadedMof.value = 0;
            recordsUploadedMultimedia.value = 0;
            recordsUploadedOccurrence.value = 0;
        }

        function clearOccurrenceUploadTables() {
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'clearOccurrenceUploadTables');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 0){
                    showNotification('negative', 'An error occurred while clearing the upload tables. Please contact the portal administrator before proceeding.');
                }
            });
        }

        function getFieldData() {
            const formData = new FormData();
            formData.append('tableArr', JSON.stringify(['uploaddetermtemp', 'uploadmediatemp', 'uploadspectemp']));
            formData.append('action', 'getUploadTableFieldData');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('uploaddetermtemp') && data['uploaddetermtemp'].length > 0){
                    data['uploaddetermtemp'].sort();
                    data['uploaddetermtemp'].forEach((field) => {
                        if(!skipDeterminationFields.includes(field)){
                            symbiotaFieldOptionsDetermination.value.push({value: field, label: field});
                        }
                    });
                }
                if(data.hasOwnProperty('uploadmediatemp') && data['uploadmediatemp'].length > 0){
                    data['uploadmediatemp'].sort();
                    data['uploadmediatemp'].forEach((field) => {
                        if(!skipMediaFields.includes(field)){
                            symbiotaFieldOptionsMedia.value.push({value: field, label: field});
                        }
                    });
                }
                if(data.hasOwnProperty('uploadspectemp') && data['uploadspectemp'].length > 0){
                    data['uploadspectemp'].sort();
                    data['uploadspectemp'].forEach((field) => {
                        if(!skipOccurrenceFields.includes(field)){
                            symbiotaFieldOptionsOccurrence.value.push({value: field, label: field});
                        }
                    });
                }
                processSuccessResponse('Complete');
                processSourceDataTransfer();
            });
        }

        function getFieldMapping() {
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('uspid', collectionDataUploadParametersId.value.toString());
            formData.append('action', 'getUploadParametersFieldMapping');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.length > 0){
                    data.forEach((mapData) => {
                        if(mapData['symbspecfield'].startsWith('ID-')){
                            savedMappingDataDetermiation.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(3);
                        }
                        else if(mapData['symbspecfield'].startsWith('IM-')){
                            savedMappingDataMedia.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(3);
                        }
                        else if(mapData['symbspecfield'].startsWith('MOF-')){
                            savedMappingDataMof.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(4);
                        }
                        else{
                            savedMappingDataOccurrence.value[mapData['sourcefield']] = mapData['symbspecfield'];
                        }
                    });
                }
                getFieldData();
            });
        }

        function getNewProcessObject(type, text) {
            if(processorDisplayArr.length > 0){
                const pastProcObj = processorDisplayArr[(processorDisplayArr.length - 1)];
                if(pastProcObj){
                    pastProcObj['current'] = false;
                    if(pastProcObj.hasOwnProperty('subs') && pastProcObj['subs'].length > 0){
                        const subProcObj = pastProcObj['subs'][(pastProcObj['subs'].length - 1)];
                        if(subProcObj){
                            subProcObj['loading'] = false;
                            if(!subProcObj['result'] || subProcObj['result'] === ''){
                                subProcObj['result'] = 'success';
                            }
                            if(!subProcObj['resultText'] || subProcObj['resultText'] === ''){
                                subProcObj['resultText'] = 'Complete';
                            }
                        }
                    }
                    else{
                        if(!pastProcObj['result'] || pastProcObj['result'] === ''){
                            pastProcObj['result'] = 'success';
                        }
                        if(!pastProcObj['resultText'] || pastProcObj['resultText'] === ''){
                            pastProcObj['resultText'] = 'Complete';
                        }
                    }
                }
            }
            const procObj = {
                id: currentProcess.value,
                procText: text,
                type: type,
                loading: true,
                current: true,
                result: '',
                resultText: ''
            };
            if(type === 'multi'){
                procObj['subs'] = [];
            }
            return procObj;
        }

        function getNewSubprocessObject(type, text) {
            return {
                procText: text,
                type: type,
                loading: true,
                result: '',
                resultText: ''
            };
        }

        function initializeUpload() {
            adjustUIStart();
            clearData();
            const text = 'Setting Symbiota field mapping data';
            currentProcess.value = 'setFieldMappingData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            if(Number(collectionDataUploadParametersId.value) > 0){
                getFieldMapping();
            }
            else{
                getFieldData();
            }
        }

        function openFieldMapperPopup(type) {
            fieldMapperPopupType.value = type;
            showFieldMapperPopup.value = true;
        }

        function parseFlatFileData() {
            const text = 'Processing data for upload';
            currentProcess.value = 'transferSourceData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const idField = Object.keys(fieldMappingDataOccurrence.value).find(field => fieldMappingDataOccurrence.value[field] === 'dbpk');
            sourceDataFlatFile.value.forEach((dataRow) => {
                const occurrenceData = {};
                occurrenceData['dbpk'] = dataRow[idField];
                Object.keys(dataRow).forEach((field) => {
                    if(fieldMappingDataOccurrence.value.hasOwnProperty(field) && fieldMappingDataOccurrence.value[field] !== 'unmapped'){
                        if(eventMofDataFields.value.hasOwnProperty(fieldMappingDataOccurrence.value[field]) || occurrenceMofDataFields.value.hasOwnProperty(fieldMappingDataOccurrence.value[field])){
                            if(dataRow[field]){
                                const mofData = {};
                                mofData['dbpk'] = dataRow[idField];
                                mofData['field'] = fieldMappingDataOccurrence.value[field];
                                mofData['datavalue'] = dataRow[field];
                                flatFileMofData.value.push(mofData);
                            }
                        }
                        else{
                            occurrenceData[fieldMappingDataOccurrence.value[field]] = dataRow[field];
                        }
                    }
                });
                flatFileOccurrenceData.value.push(occurrenceData);
            });
            processSuccessResponse('Complete');
            processFlatFileSourceData();
        }

        function processErrorResponse(text) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = false;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'error';
                    procObj['resultText'] = text;
                }
            }
        }

        function processFieldMapperUpdate(data) {
            if(fieldMapperPopupType.value === 'occurrence' || fieldMapperPopupType.value === 'flat-file'){
                fieldMappingDataOccurrence.value[data['sourceField']] = data['targetField'];
            }
            else if(fieldMapperPopupType.value === 'determination'){
                fieldMappingDataDetermiation.value[data['sourceField']] = data['targetField'];
            }
            else if(fieldMapperPopupType.value === 'multimedia'){
                fieldMappingDataMedia.value[data['sourceField']] = data['targetField'];
            }
            else if(fieldMapperPopupType.value === 'mof'){
                fieldMappingDataMof.value[data['sourceField']] = data['targetField'];
            }
        }

        function processFlatFileCsvData(csvData) {
            if(csvData.length > 0){
                let generateCoreIds = false;
                csvData.forEach((dataObj, index) => {
                    if(index === 0){
                        if(!dataObj.hasOwnProperty('id') && !dataObj.hasOwnProperty('coreid')){
                            generateCoreIds = true;
                        }
                        if(generateCoreIds){
                            sourceDataFieldsFlatFile.value['id'] = 'coreid';
                        }
                        Object.keys(dataObj).forEach((key) => {
                            if(key.toLowerCase() === 'id' || key.toLowerCase() === 'coreid'){
                                sourceDataFieldsFlatFile.value[key.toLowerCase()] = 'coreid';
                            }
                            else if(key.toLowerCase() === 'eventid'){
                                sourceDataFieldsFlatFile.value[key.toLowerCase()] = 'coreeventid';
                            }
                            else{
                                sourceDataFieldsFlatFile.value[key.toLowerCase()] = key;
                            }
                        });
                    }
                    if(generateCoreIds){
                        dataObj['id'] = (index + 1).toString();
                    }
                    sourceDataFlatFile.value.push(dataObj);
                });
            }
            validateFieldMappingData();
        }

        function processFlatFileGeoJson(geojsonData) {
            let generateCoreIds = false;
            const geoJSONFormat = new ol.format.GeoJSON();
            const uploadFeatures = geoJSONFormat.readFeatures(geojsonData);
            uploadFeatures.forEach((feature, index) => {
                if(feature){
                    const featureData = {};
                    const featureProps = feature.getProperties();
                    const selectedClone = feature.clone();
                    const geoType = selectedClone.getGeometry().getType();
                    const featureGeometry = selectedClone.getGeometry();
                    if(!featureProps.hasOwnProperty('id') && !featureProps.hasOwnProperty('coreid')){
                        generateCoreIds = true;
                    }
                    if(generateCoreIds){
                        sourceDataFieldsFlatFile.value['id'] = 'coreid';
                    }
                    Object.keys(featureProps).forEach((prop) => {
                        if(prop !== 'geometry'){
                            if(prop.toLowerCase() === 'id' || prop.toLowerCase() === 'coreid'){
                                sourceDataFieldsFlatFile.value[prop.toLowerCase()] = 'coreid';
                            }
                            else if(prop.toLowerCase() === 'eventid'){
                                sourceDataFieldsFlatFile.value[prop.toLowerCase()] = 'coreeventid';
                            }
                            else{
                                sourceDataFieldsFlatFile.value[prop.toLowerCase()] = prop;
                            }
                            if(featureProps[prop]){
                                featureData[prop.toLowerCase()] = isNaN(featureProps[prop]) ? featureProps[prop].trim() : featureProps[prop];
                            }
                            else{
                                featureData[prop.toLowerCase()] = null;
                            }
                        }
                    });
                    if(generateCoreIds){
                        featureData['id'] = (index + 1).toString();
                    }
                    if(geoType === 'Polygon' || geoType === 'MultiPolygon'){
                        const wktFormat = new ol.format.WKT();
                        featureData['footprintwkt'] = wktFormat.writeGeometry(featureGeometry);
                        sourceDataFieldsFlatFile.value['footprintwkt'] = 'footprintwkt';
                    }
                    else if((geoType === 'Point' || geoType === 'MultiPoint') && (!featureData.hasOwnProperty('decimallatitude') || !featureData.hasOwnProperty('decimallongitude') || !featureData['decimallatitude'] || !featureData['decimallongitude'])){
                        const geoJSONFormat = new ol.format.GeoJSON();
                        const geojsonStr = geoJSONFormat.writeGeometry(featureGeometry);
                        const featCoords = geoType === 'Point' ? JSON.parse(geojsonStr).coordinates : JSON.parse(geojsonStr).coordinates[0];
                        featureData['decimallatitude'] = featCoords[1];
                        featureData['decimallongitude'] = featCoords[0];
                        sourceDataFieldsFlatFile.value['decimallatitude'] = 'decimallatitude';
                        sourceDataFieldsFlatFile.value['decimallongitude'] = 'decimallongitude';
                    }
                    sourceDataFlatFile.value.push(featureData);
                }
            });
            validateFieldMappingData();
        }

        function processFlatFileSourceData() {
            let data = [];
            const configuration = {
                processingStatus: selectedProcessingStatus.value
            };
            if(flatFileOccurrenceData.value.length > 0){
                data = flatFileOccurrenceData.value.length > 500 ? flatFileOccurrenceData.value.slice(0, 500) : flatFileOccurrenceData.value.slice();
                configuration['dataType'] = 'occurrence';
                if(flatFileOccurrenceData.value.length > 500){
                    flatFileOccurrenceData.value.splice(0, 500);
                }
                else{
                    flatFileOccurrenceData.value.length = 0;
                }
            }
            else if(flatFileMofData.value.length > 0){

                data = flatFileMofData.value.length > 500 ? flatFileMofData.value.slice(0, 500) : flatFileMofData.value.slice();
                configuration['dataType'] = 'mof';
                if(flatFileMofData.value.length > 500){
                    flatFileMofData.value.splice(0, 500);
                }
                else{
                    flatFileMofData.value.length = 0;
                }
            }
            if(configuration.hasOwnProperty('dataType')){
                const text = 'Loading ' + configuration['dataType'] + ' data';
                currentProcess.value = 'transferSourceData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('uploadConfig', JSON.stringify(configuration));
                formData.append('data', JSON.stringify(data));
                formData.append('action', 'processFlatFileDataUpload');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    let resText = '';
                    if(configuration['dataType'] === 'occurrence'){
                        recordsUploadedOccurrence.value = recordsUploadedOccurrence.value + Number(res);
                        resText = recordsUploadedOccurrence.value + ' records loaded'
                    }
                    else if(configuration['dataType'] === 'mof'){
                        recordsUploadedMof.value = recordsUploadedMof.value + Number(res);
                        resText = recordsUploadedMof.value + ' records loaded'
                    }
                    processSuccessResponse(resText);
                    processFlatFileSourceData();
                });
            }
            else{
                processPostUploadCleaning();
            }
        }

        function processorDisplayScrollDown() {
            scrollProcess.value = 'scrollDown';
            processorDisplayArr.length = 0;
            processorDisplayCurrentIndex.value++;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processorDisplayScrollUp() {
            scrollProcess.value = 'scrollUp';
            processorDisplayArr.length = 0;
            processorDisplayCurrentIndex.value--;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processParameterProfileSelection(uspid) {
            collectionDataUploadParametersStore.setCurrentCollectionDataUploadParametersRecord(uspid);
        }

        function processPostUploadCleaning() {
            console.log('done');
        }

        function processSourceDataFiles() {
            const configuration = {
                eventMofFields: eventMofDataFields.value,
                occurrenceMofFields: occurrenceMofDataFields.value,
                processingStatus: selectedProcessingStatus.value,
                serverPath: localDwcaServerPath.value
            };
            if(sourceDataFilesOccurrence.value.length > 0){
                configuration['uploadFile'] = sourceDataFilesOccurrence.value[0];
                configuration['dataType'] = 'occurrence';
                configuration['fieldMap'] = Object.assign({}, fieldMappingDataOccurrence.value);
                sourceDataFilesOccurrence.value.splice(0, 1);
            }
            else if(includeDeterminationData.value && sourceDataFilesDetermination.value.length > 0){
                configuration['uploadFile'] = sourceDataFilesDetermination.value[0];
                configuration['dataType'] = 'determination';
                configuration['fieldMap'] = Object.assign({}, fieldMappingDataDetermiation.value);
                sourceDataFilesDetermination.value.splice(0, 1);
            }
            else if(includeMultimediaData.value && sourceDataFilesMultimedia.value.length > 0){
                configuration['uploadFile'] = sourceDataFilesMultimedia.value[0];
                configuration['dataType'] = 'multimedia';
                configuration['fieldMap'] = Object.assign({}, fieldMappingDataMedia.value);
                sourceDataFilesMultimedia.value.splice(0, 1);
            }
            else if(includeMofData.value && sourceDataFilesMof.value.length > 0){
                configuration['uploadFile'] = sourceDataFilesMof.value[0];
                configuration['dataType'] = 'mof';
                configuration['fieldMap'] = Object.assign({}, fieldMappingDataMof.value);
                sourceDataFilesMof.value.splice(0, 1);
            }
            if(configuration.hasOwnProperty('dataType')){
                const text = 'Loading ' + configuration['dataType'] + ' data';
                currentProcess.value = 'transferSourceData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('uploadConfig', JSON.stringify(configuration));
                formData.append('action', 'processDwcaFileDataUpload');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    let resText = '';
                    if(configuration['dataType'] === 'occurrence'){
                        recordsUploadedOccurrence.value = recordsUploadedOccurrence.value + Number(res);
                        resText = recordsUploadedOccurrence.value + ' records loaded'
                    }
                    else if(configuration['dataType'] === 'determination'){
                        recordsUploadedDetermination.value = recordsUploadedDetermination.value + Number(res);
                        resText = recordsUploadedDetermination.value + ' records loaded'
                    }
                    else if(configuration['dataType'] === 'multimedia'){
                        recordsUploadedMultimedia.value = recordsUploadedMultimedia.value + Number(res);
                        resText = recordsUploadedMultimedia.value + ' records loaded'
                    }
                    else if(configuration['dataType'] === 'mof'){
                        recordsUploadedMof.value = recordsUploadedMof.value + Number(res);
                        resText = recordsUploadedMof.value + ' records loaded'
                    }
                    processSuccessResponse(resText);
                    processSourceDataFiles();
                });
            }
            else{
                processPostUploadCleaning();
            }
        }

        function processSourceDataProcessing(metaFile) {
            const text = 'Processing source data';
            currentProcess.value = 'processSourceData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('serverPath', localDwcaServerPath.value);
            formData.append('metaFile', metaFile.toString());
            formData.append('action', 'processTransferredDwca');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('occurrence') && data['occurrence']['dataFiles'].length > 0){
                    sourceDataFieldsOccurrence.value = Object.assign({}, data['occurrence']['fields']);
                    sourceDataFilesOccurrence.value = data['occurrence']['dataFiles'].slice();
                    if(data.hasOwnProperty('identification') && data['identification']['dataFiles'].length > 0){
                        sourceDataFieldsDetermination.value = Object.assign({}, data['identification']['fields']);
                        sourceDataFilesDetermination.value = data['identification']['dataFiles'].slice();
                        determinationDataIncluded.value = true;
                    }
                    if(data.hasOwnProperty('multimedia') && data['multimedia']['dataFiles'].length > 0){
                        sourceDataFieldsMultimedia.value = Object.assign({}, data['multimedia']['fields']);
                        sourceDataFilesMultimedia.value = data['multimedia']['dataFiles'].slice();
                        multimediaDataIncluded.value = true;
                    }
                    if(data.hasOwnProperty('measurementorfact') && data['measurementorfact']['dataFiles'].length > 0){
                        sourceDataFieldsMof.value = Object.assign({}, data['measurementorfact']['fields']);
                        sourceDataFilesMof.value = data['measurementorfact']['dataFiles'].slice();
                        mofDataIncluded.value = true;
                    }
                }
                validateFieldMappingData();
            });
        }

        function processSourceDataTransfer() {
            if(Number(profileData.value['uploadtype']) === 8 || Number(profileData.value['uploadtype']) === 10){
                const text = 'Transferring source data archive';
                currentProcess.value = 'transferSourceData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                const formData = new FormData();
                formData.append('collid', props.collid.toString());
                formData.append('uploadType', profileData.value['uploadtype'].toString());
                formData.append('dwcaPath', profileData.value['dwcpath'].toString());
                formData.append('action', 'processExternalDwcaTransfer');
                fetch(dataUploadServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    if(data.hasOwnProperty('targetPath') && data.hasOwnProperty('archivePath')){
                        processSuccessResponse('Complete');
                        processSourceDataUnpacking(data['targetPath'], data['archivePath']);
                    }
                    else{
                        processErrorResponse('The source data archive could not be transferred.');
                    }
                });
            }
            else if(Number(profileData.value['uploadtype']) === 6){
                processUploadFile();
            }
        }

        function processSourceDataUnpacking(targetPath, archivePath) {
            const text = 'Unpacking source data archive';
            currentProcess.value = 'unpackSourceData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('targetPath', targetPath.toString());
            formData.append('archivePath', archivePath.toString());
            formData.append('action', 'processExternalDwcaUnpack');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                processSuccessResponse('Complete');
                localDwcaServerPath.value = data['baseFolderPath'];
                localDwcaFileArr.value = data['files'].slice();
                const metaFile = localDwcaFileArr.value.find(filename => filename.toLowerCase() === 'meta.xml');
                if(metaFile){
                    processSourceDataProcessing(metaFile);
                }
                else{
                    showNotification('negative', 'The Darwin Core Archive does not contain a meta.xml file, which is necessary for upload processing.');
                }
            });
        }

        function processSubprocessErrorResponse(text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentProcess.value);
            if(parentProcObj){
                parentProcObj['current'] = false;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'error';
                    subProcObj['resultText'] = text;
                }
            }
        }

        function processSubprocessSuccessResponse(complete, text = null) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentProcess.value);
            if(parentProcObj){
                parentProcObj['current'] = !complete;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'success';
                    subProcObj['resultText'] = text;
                }
            }
        }

        function processSuccessResponse(text = null) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = false;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'success';
                    procObj['resultText'] = text;
                }
            }
        }

        function processUploadFile() {
            if(uploadedFile.value.name.endsWith('.zip')){
                const fileSizeMb = Number(uploadedFile.value.size) > 0 ? Math.round((uploadedFile.value.size / 1000000) * 10) / 100 : 0;
                if(fileSizeMb <= Number(maxUploadFilesize)){
                    transferUploadedDwcaFileToServer();
                }
                else{
                    showNotification('negative', (uploadedFile.value.name + ' cannot be uploaded because it is ' + fileSizeMb.toString() + 'MB, which exceeds the server limit of ' + maxUploadFilesize.toString() + 'MB for uploads.'));
                }
            }
            else if(uploadedFile.value.name.endsWith('.csv')){
                const text = 'Processing source data';
                currentProcess.value = 'processSourceData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                flatFileMode.value = true;
                setSymbiotaFlatFileFieldOptions();
                parseCsvFile(uploadedFile.value, (csvData) => {
                    processFlatFileCsvData(csvData);
                });
            }
            else{
                const text = 'Processing source data';
                currentProcess.value = 'processSourceData';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                flatFileMode.value = true;
                setSymbiotaFlatFileFieldOptions();
                const fileReader = new FileReader();
                fileReader.onload = () => {
                    const uploadData = JSON.parse(fileReader.result);
                    processFlatFileGeoJson(uploadData);
                };
                fileReader.readAsText(uploadedFile.value);
            }
        }

        function resetScrollProcess() {
            setTimeout(() => {
                scrollProcess.value = null;
            }, 200);
        }

        function saveMapping() {
            const saveMappingData = {};
            saveMappingData['occurrence'] = {};
            saveMappingData['determination'] = {};
            saveMappingData['multimedia'] = {};
            saveMappingData['mof'] = {};
            Object.keys(fieldMappingDataOccurrence.value).forEach((field) => {
                const fieldName = flatFileMode.value ? sourceDataFieldsFlatFile.value[field] : sourceDataFieldsOccurrence.value[field];
                if(fieldName){
                    saveMappingData['occurrence'][fieldName.toLowerCase()] = fieldMappingDataOccurrence.value[field];
                }
            });
            Object.keys(fieldMappingDataDetermiation.value).forEach((field) => {
                const fieldName = sourceDataFieldsDetermination.value[field];
                if(fieldName){
                    saveMappingData['determination'][fieldName.toLowerCase()] = 'ID-' + fieldMappingDataDetermiation.value[field];
                }
            });
            Object.keys(fieldMappingDataMedia.value).forEach((field) => {
                const fieldName = sourceDataFieldsMultimedia.value[field];
                if(fieldName){
                    saveMappingData['multimedia'][fieldName.toLowerCase()] = 'IM-' + fieldMappingDataMedia.value[field];
                }
            });
            Object.keys(fieldMappingDataMof.value).forEach((field) => {
                const fieldName = sourceDataFieldsMof.value[field];
                if(fieldName){
                    saveMappingData['mof'][fieldName.toLowerCase()] = 'MOF-' + fieldMappingDataMof.value[field];
                }
            });
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('uspid', collectionDataUploadParametersId.value.toString());
            formData.append('fieldMappingData', JSON.stringify(saveMappingData));
            formData.append('action', 'saveFieldMapping');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    showNotification('positive','Field mapping saved.');
                }
                else{
                    showNotification('negative','An error occurred while saving the field mapping.');
                }
            });
        }

        function setScroller(info) {
            if((currentProcess.value || scrollProcess.value) && info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                procDisplayScrollHeight.value = info.verticalSize;
                if(scrollProcess.value === 'scrollDown'){
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', 0);
                }
                else{
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                }
            }
        }

        function setSymbiotaFlatFileFieldOptions() {
            symbiotaFieldOptionsOccurrence.value.forEach((fieldOption) => {
                symbiotaFieldOptionsFlatFile.value.push(fieldOption);
            });
            Object.keys(eventMofDataFields.value).forEach((key) => {
                symbiotaFieldOptionsFlatFile.value.push({value: key, label: eventMofDataFields.value[key]['label']});
            });
            Object.keys(occurrenceMofDataFields.value).forEach((key) => {
                symbiotaFieldOptionsFlatFile.value.push({value: key, label: occurrenceMofDataFields.value[key]['label']});
            });
        }

        function startUpload() {
            adjustUIStart();
            currentTab.value = 'mapping';
            if(flatFileMode.value){
                parseFlatFileData();
            }
            else{
                processSourceDataFiles();
            }
        }

        function transferUploadedDwcaFileToServer() {
            const text = 'Transferring source data';
            currentProcess.value = 'transferSourceData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('dwcaFile', uploadedFile.value);
            formData.append('action', 'uploadDwcaFile');
            fetch(dataUploadServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                processSuccessResponse('Complete');
                localDwcaServerPath.value = data['baseFolderPath'];
                localDwcaFileArr.value = data['files'].slice();
                const metaFile = localDwcaFileArr.value.find(filename => filename.toLowerCase() === 'meta.xml');
                if(metaFile){
                    processSourceDataProcessing(metaFile);
                }
                else{
                    showNotification('negative', 'The Darwin Core Archive does not contain a meta.xml file, which is necessary for upload processing.');
                }
            });
        }

        function validateFieldMappingData() {
            let validated = true;
            if(flatFileMode.value && sourceDataFlatFile.value.length > 0 && Object.keys(sourceDataFieldsFlatFile.value).length > 0){
                Object.keys(sourceDataFieldsFlatFile.value).forEach((field) => {
                    const fieldName = sourceDataFieldsFlatFile.value[field];
                    if(!fieldMappingDataOccurrence.value.hasOwnProperty(fieldName.toLowerCase())){
                        if(fieldName === 'coreid'){
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = 'dbpk';
                        }
                        else if(fieldName === 'coreeventid'){
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = 'eventdbpk';
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsFlatFile.value.find(option => option.value.toLowerCase() === fieldName.toLowerCase());
                            const usedField = fieldOption ? Object.keys(fieldMappingDataOccurrence.value).find(field => fieldMappingDataOccurrence.value[field] === fieldOption.value) : null;
                            fieldMappingDataOccurrence.value[fieldName.toLowerCase()] = (fieldOption && !usedField) ? fieldOption.value : 'unmapped';
                        }
                    }
                });
            }
            else if(sourceDataFilesOccurrence.value.length > 0 && Object.keys(sourceDataFieldsOccurrence.value).length > 0){
                Object.keys(sourceDataFieldsOccurrence.value).forEach((field) => {
                    const fieldName = sourceDataFieldsOccurrence.value[field];
                    if(Object.keys(savedMappingDataOccurrence.value).length === 0 || !savedMappingDataOccurrence.value.hasOwnProperty(fieldName.toLowerCase())){
                        if(fieldName === 'coreid'){
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = 'dbpk';
                        }
                        else if(fieldName === 'coreeventid'){
                            fieldMappingDataOccurrence.value[field.toLowerCase()] = 'eventdbpk';
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsOccurrence.value.find(option => option.value.toLowerCase() === fieldName.toLowerCase());
                            const usedField = fieldOption ? Object.keys(fieldMappingDataOccurrence.value).find(field => fieldMappingDataOccurrence.value[field] === fieldOption.value) : null;
                            fieldMappingDataOccurrence.value[field] = (fieldOption && !usedField) ? fieldOption.value : 'unmapped';
                        }
                    }
                    else{
                        const fieldOption = symbiotaFieldOptionsOccurrence.value.find(option => option.value.toLowerCase() === savedMappingDataOccurrence.value[fieldName.toLowerCase()]);
                        fieldMappingDataOccurrence.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                    }
                });
                if(sourceDataFilesDetermination.value.length > 0 && Object.keys(sourceDataFieldsDetermination.value).length > 0){
                    Object.keys(sourceDataFieldsDetermination.value).forEach((field) => {
                        const fieldName = sourceDataFieldsDetermination.value[field];
                        if(Object.keys(savedMappingDataDetermiation.value).length === 0 || !savedMappingDataDetermiation.value.hasOwnProperty(fieldName.toLowerCase())){
                            if(fieldName === 'coreid'){
                                fieldMappingDataDetermiation.value[field.toLowerCase()] = 'dbpk';
                            }
                            else{
                                const fieldOption = symbiotaFieldOptionsDetermination.value.find(option => option.value.toLowerCase() === fieldName.toLowerCase());
                                const usedField = fieldOption ? Object.keys(fieldMappingDataDetermiation.value).find(field => fieldMappingDataDetermiation.value[field] === fieldOption.value) : null;
                                fieldMappingDataDetermiation.value[field] = (fieldOption && !usedField) ? fieldOption.value : 'unmapped';
                            }
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsDetermination.value.find(option => option.value.toLowerCase() === savedMappingDataDetermiation.value[fieldName.toLowerCase()]);
                            fieldMappingDataDetermiation.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                        }
                    });
                }
                if(sourceDataFilesMultimedia.value.length > 0 && Object.keys(sourceDataFieldsMultimedia.value).length > 0){
                    Object.keys(sourceDataFieldsMultimedia.value).forEach((field) => {
                        const fieldName = sourceDataFieldsMultimedia.value[field];
                        if(Object.keys(savedMappingDataMedia.value).length === 0 || !savedMappingDataMedia.value.hasOwnProperty(fieldName.toLowerCase())){
                            if(fieldName === 'coreid'){
                                fieldMappingDataMedia.value[field.toLowerCase()] = 'dbpk';
                            }
                            else{
                                const fieldOption = symbiotaFieldOptionsMedia.value.find(option => option.value.toLowerCase() === fieldName.toLowerCase());
                                const usedField = fieldOption ? Object.keys(fieldMappingDataMedia.value).find(field => fieldMappingDataMedia.value[field] === fieldOption.value) : null;
                                fieldMappingDataMedia.value[field] = (fieldOption && !usedField) ? fieldOption.value : 'unmapped';
                            }
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsMedia.value.find(option => option.value.toLowerCase() === savedMappingDataMedia.value[fieldName.toLowerCase()]);
                            fieldMappingDataMedia.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                        }
                    });
                }
                if(sourceDataFilesMof.value.length > 0 && Object.keys(sourceDataFieldsMof.value).length > 0){
                    Object.keys(sourceDataFieldsMof.value).forEach((field) => {
                        const fieldName = sourceDataFieldsMof.value[field];
                        if(Object.keys(savedMappingDataMof.value).length === 0 || !savedMappingDataMof.value.hasOwnProperty(fieldName.toLowerCase())){
                            if(fieldName === 'coreid'){
                                fieldMappingDataMof.value[field.toLowerCase()] = 'dbpk';
                            }
                            else if(fieldName === 'coreeventid'){
                                fieldMappingDataMof.value[field.toLowerCase()] = 'eventdbpk';
                            }
                            else if(fieldName.toLowerCase() === 'measurementtype'){
                                fieldMappingDataMof.value[field] = 'field';
                            }
                            else if(fieldName.toLowerCase() === 'measurementvalue'){
                                fieldMappingDataMof.value[field] = 'datavalue';
                            }
                            else{
                                fieldMappingDataMof.value[field] = 'unmapped';
                            }
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsMof.value.find(option => option.value.toLowerCase() === savedMappingDataMof.value[fieldName.toLowerCase()]);
                            fieldMappingDataMof.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                        }
                    });
                }
            }
            else{
                validated = false;
            }
            processSuccessResponse('Complete');
            if(validated){
                currentTab.value = 'mapping';
                currentProcess.value = null;
            }
            else{
                showNotification('negative', 'Source data could not be correctly read to process upload.');
                adjustUIStart();
                clearData();
            }
        }

        Vue.onMounted(() => {
            if(Number(props.collid) > 0){
                clearOccurrenceUploadTables();
                collectionDataUploadParametersStore.setCollectionDataUploadParametersArr(props.collid);
            }
        });

        return {
            acceptedFileTypes,
            collectionDataUploadParametersArr,
            collectionDataUploadParametersId,
            currentProcess,
            currentTab,
            determinationDataIncluded,
            fieldMapperFieldMapping,
            fieldMapperSourceFields,
            fieldMapperTargetFields,
            flatFileMode,
            includeDeterminationData,
            includeMultimediaData,
            includeMofData,
            multimediaDataIncluded,
            mofDataIncluded,
            procDisplayScrollAreaRef,
            processingStatusOptions,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            profileData,
            selectedProcessingStatus,
            showCollectionDataUploadParametersEditorPopup,
            showFieldMapperPopup,
            sourceDataFilesDetermination,
            sourceDataFilesMof,
            sourceDataFilesMultimedia,
            uploadedFile,
            initializeUpload,
            openFieldMapperPopup,
            processFieldMapperUpdate,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            processParameterProfileSelection,
            processUploadFile,
            saveMapping,
            startUpload
        }
    }
};
