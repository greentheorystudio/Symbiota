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
                                    
                                </q-card-section>
                            </q-card>
                        </q-expansion-item>
                        <q-separator></q-separator>
                        <q-expansion-item :model-value="currentTab === 'summary'" class="overflow-hidden" group="expansiongroup" label="Summary" header-class="bg-grey-3 text-bold" :disable="currentTab !== 'summary'">
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
    `,
    components: {
        'collection-data-upload-parameters-editor-popup': collectionDataUploadParametersEditorPopup,
        'collection-data-upload-parameters-field-module': collectionDataUploadParametersFieldModule,
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
        const eventMofDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
        const fieldMappingDataDetermiation = Vue.ref({});
        const fieldMappingDataEventMof = Vue.ref({});
        const fieldMappingDataMedia = Vue.ref({});
        const fieldMappingDataMof = Vue.ref({});
        const fieldMappingDataOccurrence = Vue.ref({});
        const flatFileMode = Vue.ref(false);
        const localDwcaFileArr = Vue.ref([]);
        const localDwcaServerPath = Vue.ref(null);
        const maxUploadFilesize = baseStore.getMaxUploadFilesize;
        const occurrenceMofDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processorDisplayArr = Vue.reactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const profileData = Vue.computed(() => collectionDataUploadParametersStore.getCollectionDataUploadParametersData);
        const scrollProcess = Vue.ref(null);
        const showCollectionDataUploadParametersEditorPopup = Vue.ref(false);
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
            fieldMappingDataDetermiation.value = Object.assign({}, {});
            fieldMappingDataEventMof.value = Object.assign({}, {});
            fieldMappingDataMedia.value = Object.assign({}, {});
            fieldMappingDataMof.value = Object.assign({}, {});
            fieldMappingDataOccurrence.value = Object.assign({}, {});
            symbiotaFieldOptionsDetermination.value.length = 0;
            symbiotaFieldOptionsFlatFile.value.length = 0;
            symbiotaFieldOptionsMedia.value.length = 0;
            symbiotaFieldOptionsOccurrence.value.length = 0;
            symbiotaFieldOptionsDetermination.value.push({value: 'unmapped', label: 'UNMAPPED'});
            symbiotaFieldOptionsFlatFile.value.push({value: 'unmapped', label: 'UNMAPPED'});
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
        }

        function clearUploadTables() {
            const formData = new FormData();
            formData.append('collid', props.collid.toString());
            formData.append('action', 'clearUploadTables');
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
                            fieldMappingDataDetermiation.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(3);
                        }
                        else if(mapData['symbspecfield'].startsWith('IM-')){
                            fieldMappingDataMedia.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(3);
                        }
                        else if(mapData['symbspecfield'].startsWith('MOF-')){
                            fieldMappingDataMof.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(4);
                        }
                        else if(mapData['symbspecfield'].startsWith('EMOF-')){
                            fieldMappingDataEventMof.value[mapData['sourcefield']] = mapData['symbspecfield'].slice(5);
                        }
                        else{
                            fieldMappingDataOccurrence.value[mapData['sourcefield']] = mapData['symbspecfield'];
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
                    }
                    else if((geoType === 'Point' || geoType === 'MultiPoint') && (!featureData.hasOwnProperty('decimallatitude') || !featureData.hasOwnProperty('decimallongitude') || !featureData['decimallatitude'] || !featureData['decimallongitude'])){
                        const geoJSONFormat = new ol.format.GeoJSON();
                        const geojsonStr = geoJSONFormat.writeGeometry(featureGeometry);
                        const featCoords = geoType === 'Point' ? JSON.parse(geojsonStr).coordinates : JSON.parse(geojsonStr).coordinates[0];
                        featureData['decimallatitude'] = featCoords[1];
                        featureData['decimallongitude'] = featCoords[0];
                    }
                    sourceDataFlatFile.value.push(featureData);
                }
            });
            validateFieldMappingData();
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
                    }
                    if(data.hasOwnProperty('multimedia') && data['multimedia']['dataFiles'].length > 0){
                        sourceDataFieldsMultimedia.value = Object.assign({}, data['multimedia']['fields']);
                        sourceDataFilesMultimedia.value = data['multimedia']['dataFiles'].slice();
                    }
                    if(data.hasOwnProperty('measurementorfact') && data['measurementorfact']['dataFiles'].length > 0){
                        sourceDataFieldsMof.value = Object.assign({}, data['measurementorfact']['fields']);
                        sourceDataFilesMof.value = data['measurementorfact']['dataFiles'].slice();
                    }
                }
                validateFieldMappingData();
            });
        }

        function processSourceDataTransfer() {
            if(Number(profileData.value['uploadtype']) === 8 || Number(profileData.value['uploadtype']) === 10){
                const text = 'Transferring source data';
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
            else if(Number(profileData.value['uploadtype']) === 6){
                processUploadFile();
            }
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
                    if(!fieldMappingDataOccurrence.value.hasOwnProperty(field)){
                        if(sourceDataFieldsFlatFile.value[field] === 'coreid'){
                            fieldMappingDataOccurrence.value[field] = 'dbpk';
                        }
                        else if(sourceDataFieldsFlatFile.value[field] === 'coreeventid'){
                            fieldMappingDataOccurrence.value[field] = 'eventdbpk';
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsFlatFile.value.find(option => option.value.toLowerCase() === field.toLowerCase());
                            fieldMappingDataOccurrence.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                        }
                    }
                });
            }
            else if(sourceDataFilesOccurrence.value.length > 0 && Object.keys(sourceDataFieldsOccurrence.value).length > 0){
                Object.keys(sourceDataFieldsOccurrence.value).forEach((field) => {
                    if(!fieldMappingDataOccurrence.value.hasOwnProperty(field)){
                        if(sourceDataFieldsOccurrence.value[field] === 'coreid'){
                            fieldMappingDataOccurrence.value[field] = 'dbpk';
                        }
                        else if(sourceDataFieldsOccurrence.value[field] === 'coreeventid'){
                            fieldMappingDataOccurrence.value[field] = 'eventdbpk';
                        }
                        else{
                            const fieldOption = symbiotaFieldOptionsOccurrence.value.find(option => option.value.toLowerCase() === field.toLowerCase());
                            fieldMappingDataOccurrence.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                        }
                    }
                });
                if(sourceDataFilesDetermination.value.length > 0 && Object.keys(sourceDataFieldsDetermination.value).length > 0){
                    Object.keys(sourceDataFieldsDetermination.value).forEach((field) => {
                        if(!fieldMappingDataDetermiation.value.hasOwnProperty(field)){
                            if(sourceDataFieldsDetermination.value[field] === 'coreid'){
                                fieldMappingDataDetermiation.value[field] = 'dbpk';
                            }
                            else{
                                const fieldOption = symbiotaFieldOptionsDetermination.value.find(option => option.value.toLowerCase() === field.toLowerCase());
                                fieldMappingDataDetermiation.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                            }
                        }
                    });
                }
                if(sourceDataFilesMultimedia.value.length > 0 && Object.keys(sourceDataFieldsMultimedia.value).length > 0){
                    Object.keys(sourceDataFieldsMultimedia.value).forEach((field) => {
                        if(!fieldMappingDataMedia.value.hasOwnProperty(field)){
                            if(sourceDataFieldsMultimedia.value[field] === 'coreid'){
                                fieldMappingDataMedia.value[field] = 'dbpk';
                            }
                            else{
                                const fieldOption = symbiotaFieldOptionsMedia.value.find(option => option.value.toLowerCase() === field.toLowerCase());
                                fieldMappingDataMedia.value[field] = fieldOption ? fieldOption.value : 'unmapped';
                            }
                        }
                    });
                }
                if(sourceDataFilesMof.value.length > 0 && Object.keys(sourceDataFieldsMof.value).length > 0){
                    Object.keys(sourceDataFieldsMof.value).forEach((field) => {
                        if(!fieldMappingDataMof.value.hasOwnProperty(field)){
                            if(sourceDataFieldsMof.value[field] === 'coreid'){
                                fieldMappingDataMof.value[field] = 'dbpk';
                            }
                            else if(sourceDataFieldsMof.value[field] === 'coreeventid'){
                                fieldMappingDataMof.value[field] = 'eventdbpk';
                            }
                            else if(sourceDataFieldsMof.value[field].toLowerCase() === 'measurementtype'){
                                fieldMappingDataMof.value[field] = 'field';
                            }
                            else if(sourceDataFieldsMof.value[field].toLowerCase() === 'measurementvalue'){
                                fieldMappingDataMof.value[field] = 'datavalue';
                            }
                            else{
                                fieldMappingDataOccurrence.value[field] = 'unmapped';
                            }
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
            }
            else{
                showNotification('negative', 'Source data could not be correctly read to process upload.');
                adjustUIStart();
                clearData();
            }
        }

        Vue.onMounted(() => {
            if(Number(props.collid) > 0){
                clearUploadTables();
                collectionDataUploadParametersStore.setCollectionDataUploadParametersArr(props.collid);
            }
        });

        return {
            acceptedFileTypes,
            collectionDataUploadParametersArr,
            collectionDataUploadParametersId,
            currentProcess,
            currentTab,
            flatFileMode,
            procDisplayScrollAreaRef,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            profileData,
            showCollectionDataUploadParametersEditorPopup,
            uploadedFile,
            initializeUpload,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            processParameterProfileSelection,
            processUploadFile
        }
    }
};
