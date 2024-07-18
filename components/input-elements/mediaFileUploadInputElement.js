const mediaFileUploadInputElement = {
    props: {
        collId: {
            type: Number,
            default: 1
        },
        createOccurrence: {
            type: Boolean,
            default: false
        },
        identifierField: {
            type: String,
            default: 'catalognumber'
        },
        identifierRegEx: {
            type: String,
            default: null
        },
        importExternalFiles: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'Upload Media Files'
        },
        occId: {
            type: Number,
            default: null
        },
        taxonId: {
            type: Number,
            default: null
        }
    },
    template: `
        <q-card flat bordered class="black-border fit bg-grey-4">
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="full-width row justify-between">
                    <div class="text-h6 text-bold">{{ label }}</div>
                    <div>
                        <q-btn-toggle v-model="selectedUploadMethod" :options="uploadMethodOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary"></q-btn-toggle>
                    </div>
                </div>
                <template v-if="selectedUploadMethod === 'upload'">
                    <div class="full-width row justify-between">
                        <div class="row justify-start q-gutter-sm">
                            <div>
                                <q-btn color="primary" @click="uploaderRef.pickFiles();" label="Choose Files" />
                            </div>
                            <div>
                                <q-btn color="negative" @click="cancelUpload();" label="Clear Files" :disabled="fileArr.length === 0" />
                            </div>
                        </div>
                        <div class="row justify-end">
                            <div>
                                <q-btn color="positive" @click="initializeUpload();" label="Start Upload" :disabled="fileArr.length === 0" />
                            </div>
                        </div>
                    </div>
                </template>
                <template v-if="selectedUploadMethod === 'url'">
                    <div class="full-width column q-gutter-sm">
                        <div class="full-width row justify-between">
                            <div class="col-grow">
                                <text-field-input-element data-type="textarea" label="Enter URL" :value="urlMethodUrl" @update:value="(value) => urlMethodUrl = value"></text-field-input-element>
                            </div>
                        </div>
                        <div class="full-width row justify-between">
                            <div>
                                <checkbox-input-element label="Copy file to server" :value="urlMethodCopyFile" @update:value="(value) => urlMethodCopyFile = value"></checkbox-input-element>
                            </div>
                            <div>
                                <q-btn color="primary" @click="processExternalUrl();" label="Process URL" />
                            </div>
                        </div>
                    </div>
                </template>
                <div class="full-width" :class="fileArr.length === 0 ? 'hidden' : ''">
                    <q-uploader ref="uploaderRef" class="fit" :style="uploaderStyle" color="grey-8" :factory="uploadFiles" :filter="validateFiles" @uploaded="processUploaded" multiple hide-upload-btn flat>
                        <template v-slot:header="scope">
                            <div class="full-width row justify-between">
                                <div class="row no-wrap justify-start q-pa-sm q-gutter-xs">
                                    <q-spinner v-if="scope.isUploading" class="q-uploader__spinner"></q-spinner>
                                    <div v-if="queueSize > 0" class="q-uploader__subtitle text-bold">Total upload size: {{ queueSizeLabel }}</div>
                                    <q-uploader-add-trigger></q-uploader-add-trigger>
                                </div>
                                <div class="row justify-end">
                                    <span v-if="csvFileDataUploaded" class="text-bold text-red">
                                        CSV Data Uploaded
                                    </span>
                                </div>
                            </div>
                        </template>
                        <template v-slot:list="scope">
                            <div ref="fileListRef">
                                <q-list separator class="fit">
                                    <q-item v-for="file in scope.files" :key="file.__key" class="full-width">
                                        <q-item-section>
                                            <div class="full-width row">
                                                <div class="col-2">
                                                    <div v-if="file.__img">
                                                        <q-img :src="file.__img.src" spinner-color="white"></q-img>
                                                    </div>
                                                    <div v-else class="text-h6 text-bold">
                                                        {{ file.name.split('.').pop() + ' file' }}
                                                    </div>
                                                </div>
                                                <div class="col-8 column q-pl-sm">
                                                    <div class="row full-width justify-between">
                                                        <div class="ellipsis">
                                                            {{ file.name }}
                                                        </div>
                                                        <div caption class="row justify-end">
                                                            {{ file.correctedSizeLabel }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div caption>
                                                            <span class="text-bold q-mr-xs">Metadata: </span>
                                                            <template v-for="key in Object.keys(file['uploadMetadata'])">
                                                                <template v-if="file['uploadMetadata'][key] && file['uploadMetadata'][key] !== ''">
                                                                    <span class="text-bold q-ml-xs">{{ key }}:</span> {{ file['uploadMetadata'][key] }}
                                                                </template>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div v-if="getFileErrorMessage(file)" class="text-bold text-red">
                                                        {{ getFileErrorMessage(file) }}
                                                    </div>
                                                    <div v-else class="text-bold text-green">
                                                        Ready to upload
                                                    </div>
                                                </div>
                                                <div class="col-2 row justify-end">
                                                    <div class="column q-gutter-xs">
                                                        <div class="row justify-end">
                                                            <q-btn color="negative" class="black-border" @click="removePickedFile(file);" label="Remove" dense/>
                                                        </div>
                                                        <div class="row justify-end">
                                                            <q-btn color="grey-4" class="black-border text-black" @click="" label="Edit Metadata" dense/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </template>
                    </q-uploader>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { getSubstringByRegEx, parseCsvFile, showNotification } = useCore();
        const baseStore = useBaseStore();
        const imageStore = useImageStore();
        const mediaStore = useMediaStore();
        const occurrenceStore = useOccurrenceStore();

        const audioTypes = ['audio/mpeg', 'audio/ogg', 'audio/wav'];
        let csvFileData = [];
        const csvFileDataUploaded = Vue.computed(() => {
            return csvFileData.length > 0;
        });
        const fileArr = Vue.shallowReactive([]);
        const fileExtensionTypes = ['jpeg', 'jpg', 'png', 'zc'];
        const fileListRef = Vue.ref(null);
        const identifierArr = Vue.ref([]);
        const identifierData = Vue.ref({});
        const maxUploadFilesize = baseStore.getMaxUploadFilesize;
        const queueSize = Vue.ref(0);
        const queueSizeLabel = Vue.ref('');
        const selectedUploadMethod = Vue.ref('upload');
        const systemProperties = Vue.ref(['format','type']);
        const taxaArr = Vue.ref([]);
        const taxaData = Vue.ref({});
        const uploaderRef = Vue.ref(null);
        const uploaderStyle = Vue.ref('');
        const uploadMethodOptions = [
            {label: 'Local Files', value: 'upload'},
            {label: 'From URL', value: 'url'}
        ];
        const urlMethodCopyFile = Vue.ref(false);
        const urlMethodUrl = Vue.ref(null);
        const videoTypes = ['video/mp4', 'video/webm', 'video/ogg'];

        function cancelUpload() {
            csvFileData.length = 0;
            fileArr.length = 0;
            identifierArr.value = [];
            identifierData.value = Object.assign({}, {});
            taxaArr.value = [];
            taxaData.value = Object.assign({}, {});
            updateQueueSize();
            uploaderRef.value.reset();
        }

        function getFileErrorMessage(file) {
            let errorMessage = null;
            if(Number(props.occId) === 0 && Number(props.taxonId) === 0){
                if(Number(props.collId) > 0){
                    if(!file['uploadMetadata']['occid'] && !props.createOccurrence){
                        if(file['catalognumber']){
                            errorMessage = 'Catalog number was not found in the database';
                        }
                        else{
                            errorMessage = 'Catalog number required';
                        }
                    }
                }
                else{
                    if(!file['uploadMetadata']['tid']){
                        if(file['scientificName']){
                            errorMessage = 'Scientific name was not found in taxonomic thesaurus';
                        }
                        else{
                            errorMessage = 'Scientific name required';
                        }
                    }
                }
            }
            return errorMessage;
        }

        function getUploadData(file) {
            if(file['uploadType'] === 'image'){
                return {
                    url: imageApiUrl,
                    formFields: [
                        {name: 'action', value: 'addImage'},
                        {name: 'collid', value: props.collId.toString()},
                        {name: 'copyToServer', value: (props.importExternalFiles ? '1' : '0')},
                        {name: 'image', value: JSON.stringify(file['uploadMetadata'])}
                    ],
                    fieldName: 'imgfile'
                }
            }
            else{
                return {
                    url: mediaApiUrl,
                    formFields: [
                        {name: 'action', value: 'addMedia'},
                        {name: 'collid', value: props.collId.toString()},
                        {name: 'copyToServer', value: (props.importExternalFiles ? '1' : '0')},
                        {name: 'media', value: JSON.stringify(file['uploadMetadata'])}
                    ],
                    fieldName: 'medfile'
                }
            }
        }

        function initializeUpload() {
            fileArr.forEach((file) => {
                if(file.hasOwnProperty('tid') && file.tid && Number(file.tid) > 0){
                    file['metadata'].push({name: 'tid', value: file.tid, system: true});
                    uploaderRef.value.updateFileStatus(file,'idle');
                }
            });
            uploaderRef.value.upload();
        }

        function processCsvFileData() {
            if(csvFileData.length > 0){
                taxaArr.value = [];
                csvFileData.forEach((dataObj, index) => {
                    if(dataObj.hasOwnProperty('filename') && dataObj['filename']){
                        if(dataObj.hasOwnProperty('scientificname') && dataObj['scientificname'] !== '' && !taxaArr.value.includes(dataObj['scientificname'])){
                            taxaArr.value.push(dataObj['scientificname']);
                        }
                        else if(dataObj.hasOwnProperty('sciname') && dataObj['sciname'] !== '' && !taxaArr.value.includes(dataObj['sciname'])){
                            taxaArr.value.push(dataObj['sciname']);
                        }
                        if(Number(props.collId) > 0 && dataObj.hasOwnProperty(props.identifierField) && dataObj[props.identifierField] !== '' && !identifierArr.value.includes(dataObj[props.identifierField])){
                            identifierArr.value.push(dataObj[props.identifierField]);
                        }
                    }
                    else{
                        csvFileData.splice(index,1);
                    }
                });
                setFileIdentifierData();
            }
        }

        function processExternalUrl() {
            if(urlMethodUrl.value){
                fetch(urlMethodUrl.value).then((fileFetch) => {
                    fileFetch.blob().then((blob) => {
                        uploaderRef.value.addFiles([
                            new File([blob], urlMethodUrl.value)
                        ]);
                        console.log(blob);
                    });
                });
            }
        }

        function processUploaded(info) {
            info.files.forEach((file) => {
                removePickedFile(file);
            });
        }

        function removePickedFile(file) {
            const fileIndex = fileArr.indexOf(file);
            fileArr.splice(fileIndex,1);
            uploaderRef.value.removeFile(file);
            updateQueueSize();
        }

        function setFileData() {
            if(fileArr.length > 0 && csvFileData.length > 0){
                fileArr.forEach((file) => {
                    if(!file['recordIdentifier'] && file['filenameRecordIdentifier']){
                        file['recordIdentifier'] = file['filenameRecordIdentifier'];
                    }
                    let csvData = csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.toLowerCase());
                    if(!csvData){
                        csvData = csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.substring(0, file.name.lastIndexOf('.')).toLowerCase());
                    }
                    if(csvData){
                        Object.keys(csvData).forEach((key) => {
                            if(key !== 'filename' && csvData[key] && csvData[key] !== ''){
                                if(key === 'scientificname' || key === 'sciname'){
                                    file['scientificName'] = csvData[key];
                                }
                                else if(key === props.identifierField){
                                    file['recordIdentifier'] = csvData[key];
                                }
                                else if(file['uploadMetadata'].hasOwnProperty(key)){
                                    file['uploadMetadata'][key] = csvData[key];
                                }
                            }
                        });
                    }
                    if(!file['uploadMetadata']['tid'] && file['scientificName'] && taxaData.value.hasOwnProperty(file['scientificName'].toLowerCase())){
                        file['uploadMetadata']['tid'] = taxaData.value[file['scientificName'].toLowerCase()]['tid'];
                    }
                    if(!file['uploadMetadata']['occid'] && file['recordIdentifier'] && identifierData.value.hasOwnProperty(file['recordIdentifier'].toLowerCase())){
                        file['uploadMetadata']['occid'] = identifierData.value[file['recordIdentifier'].toLowerCase()]['occid'];
                        if(!file['uploadMetadata']['tid'] && identifierData.value[file['recordIdentifier'].toLowerCase()]['tid']){
                            file['uploadMetadata']['tid'] = identifierData.value[file['recordIdentifier'].toLowerCase()]['tid'];
                        }
                    }
                    else if(!file['uploadMetadata']['occid'] && file['filenameRecordIdentifier'] && identifierData.value.hasOwnProperty(file['filenameRecordIdentifier'].toLowerCase())){
                        file['uploadMetadata']['occid'] = identifierData.value[file['filenameRecordIdentifier'].toLowerCase()]['occid'];
                        if(!file['uploadMetadata']['tid'] && identifierData.value[file['filenameRecordIdentifier'].toLowerCase()]['tid']){
                            file['uploadMetadata']['tid'] = identifierData.value[file['filenameRecordIdentifier'].toLowerCase()]['tid'];
                        }
                    }
                });
            }
        }

        function setFileIdentifierData() {
            if(Number(props.taxonId) === 0 && taxaArr.value.length > 0){
                setTaxaData();
            }
            else if(Number(props.collId) > 0 && identifierArr.value.length > 0){
                setIdentifierData();
            }
            else{
                setFileData();
            }
        }

        function setFileMediaTypeFormat(file) {
            if(file.name.endsWith(".mp4")){
                file['uploadMetadata']['type'] = 'MovingImage';
                file['uploadMetadata']['format'] = 'video/mp4';
            }
            else if(file.name.endsWith(".webm")){
                file['uploadMetadata']['type'] = 'MovingImage';
                file['uploadMetadata']['format'] = 'video/webm';
            }
            else if(file.name.endsWith(".ogg")){
                file['uploadMetadata']['type'] = 'MovingImage';
                file['uploadMetadata']['format'] = 'video/ogg';
            }
            else if(file.name.endsWith(".mp3")){
                file['uploadMetadata']['type'] = 'Sound';
                file['uploadMetadata']['format'] = 'audio/mpeg';
            }
            else if(file.name.endsWith(".wav")){
                file['uploadMetadata']['type'] = 'Sound';
                file['uploadMetadata']['format'] = 'audio/wav';
            }
            else if(file.name.endsWith(".zc")){
                file['uploadMetadata']['type'] = 'Sound';
            }
        }

        function setIdentifierData() {
            const formData = new FormData();
            formData.append('collid', props.collId.toString());
            formData.append('identifierField', props.identifierField);
            formData.append('identifiers', JSON.stringify(identifierArr.value));
            formData.append('action', 'getOccurrenceIdDataFromIdentifierArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    identifierArr.value.length = 0;
                    Object.keys(resObj).forEach((key) => {
                        identifierData.value[key] = Object.assign({}, resObj[key]);
                    });
                    setFileIdentifierData();
                });
            });
        }

        function setTaxaData() {
            const formData = new FormData();
            formData.append('taxa', JSON.stringify(taxaArr.value));
            formData.append('action', 'getTaxaIdDataFromNameArr');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    taxaArr.value.length = 0;
                    Object.keys(resObj).forEach((key) => {
                        taxaData.value[key] = Object.assign({}, resObj[key]);
                    });
                    setFileIdentifierData();
                });
            });
        }

        function setUploaderStyle() {
            uploaderStyle.value = '';
            setTimeout(() => {
                if(fileListRef.value.clientHeight > 0){
                    uploaderStyle.value = 'height: ' + (fileListRef.value.clientHeight + 50) + 'px;';
                }
            }, 400 );
        }

        function updateQueueSize() {
            let size = 0;
            fileArr.forEach((file) => {
                size += file.size;
            });
            const sizeMb = (Math.round((size / 1000000) * 10 ) / 100);
            queueSize.value = size;
            queueSizeLabel.value = sizeMb.toString() + 'MB';
            setUploaderStyle();
        }

        function uploadFiles(files) {
            if(Number(props.collId) > 0 && props.createOccurrence && !files[0]['uploadMetadata']['occid']){
                const occurrenceData = {};
                occurrenceData['collid'] = props.collId;
                occurrenceData[props.identifierField] = files[0]['recordIdentifier'];
                occurrenceData['sciname'] = files[0]['scientificName'];
                occurrenceData['tid'] = files[0]['uploadMetadata']['tid'];
                const formData = new FormData();
                formData.append('collid', props.collId.toString());
                formData.append('occurrence', JSON.stringify(occurrenceData));
                formData.append('action', 'createOccurrenceRecord');
                fetch(occurrenceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(res && Number(res) > 0){
                            files[0]['uploadMetadata']['occid'] = res;
                            return getUploadData(files[0]);
                        }
                    });
                });
            }
            else if((Number(props.collId) > 0 && Number(files[0]['uploadMetadata']['occid']) > 0) || (!props.collId && Number(files[0]['uploadMetadata']['tid']) > 0)){
                return getUploadData(files[0]);
            }
            else{
                return false;
            }
        }

        function validateFiles(files) {
            console.log(files);
            const returnArr = [];
            files.forEach((file) => {
                const fileSizeMb = Math.round((file.size / 1000000) * 10 ) / 100;
                const existingData = fileArr.find((obj) => obj.name.toLowerCase() === file.name.toLowerCase());
                if(file.name.endsWith('.csv')){
                    parseCsvFile(file, (csvData) => {
                        csvFileData = csvData;
                        processCsvFileData();
                    });
                }
                else if(!existingData){
                    if(fileSizeMb <= Number(maxUploadFilesize)){
                        if(videoTypes.includes(file.type) || audioTypes.includes(file.type) || fileExtensionTypes.includes(file.name.split('.').pop().toLowerCase())){
                            file['correctedSizeLabel'] =   fileSizeMb.toString() + 'MB';
                            if(videoTypes.includes(file.type) || audioTypes.includes(file.type) || file.name.endsWith('.zc')){
                                file['uploadType'] = 'media';
                                file['uploadMetadata'] = Object.assign({}, mediaStore.getBlankMediaRecord);
                                setFileMediaTypeFormat(file);
                            }
                            else{
                                file['uploadType'] = 'image';
                                file['uploadMetadata'] = Object.assign({}, imageStore.getBlankImageRecord);
                            }
                            file['filenameRecordIdentifier'] = (Number(props.collId) > 0 && props.identifierRegEx) ? getSubstringByRegEx(props.identifierRegEx, file.name) : null;
                            if(file['filenameRecordIdentifier'] && !identifierArr.value.includes(file['filenameRecordIdentifier'])){
                                identifierArr.value.push(file['filenameRecordIdentifier']);
                            }
                            file['recordIdentifier'] = null;
                            file['scientificName'] = null;
                            if(Number(props.occId) > 0){
                                file['uploadMetadata']['occid'] = props.occId;
                            }
                            if(Number(props.taxonId) > 0){
                                file['uploadMetadata']['tid'] = props.taxonId;
                            }
                            fileArr.push(file);
                            updateQueueSize();
                            returnArr.push(file);
                        }
                        else{
                            showNotification('negative', (file.name + ' cannot be uploaded because it is ' + file.type + ' file type. Only jpg, jpeg, png, zc, mp3, wav, ogg, mp4, webm, and csv files can be processed through this uploader.'));
                        }
                    }
                    else{
                        showNotification('negative', (file.name + ' cannot be uploaded because it is ' + fileSizeMb.toString() + 'MB, which exceeds the server limit of ' + maxUploadFilesize.toString() + 'MB for uploads.'));
                    }
                }
            });
            setFileIdentifierData();
            return returnArr;
        }

        return {
            csvFileDataUploaded,
            fileArr,
            fileListRef,
            queueSize,
            queueSizeLabel,
            selectedUploadMethod,
            uploaderRef,
            uploaderStyle,
            uploadMethodOptions,
            urlMethodCopyFile,
            urlMethodUrl,
            cancelUpload,
            getFileErrorMessage,
            initializeUpload,
            processExternalUrl,
            processUploaded,
            removePickedFile,
            uploadFiles,
            validateFiles
        }
    }
};
