const mediaFileUploadInputElement = {
    props: {
        collId: {
            type: Number,
            default: null
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
                                <q-btn color="primary" @click="uploaderRef.pickFiles();" label="Process URL" />
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
                                                    <div v-if="file.__img" class="q-uploader-thumbnail">
                                                        <q-img :src="file.__img.src" spinner-color="white"></q-img>
                                                    </div>
                                                    <div v-else class="text-h6 text-bold">
                                                        {{ file.name.split('.').pop() + ' file' }}
                                                    </div>
                                                </div>
                                                <div class="col-8 column">
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
        const { parseCsvFile, showNotification } = useCore();
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
        const maxUploadFilesize = baseStore.getMaxUploadFilesize;
        const queueSize = Vue.ref(0);
        const queueSizeLabel = Vue.ref('');
        const selectedUploadMethod = Vue.ref('upload');
        const systemProperties = Vue.ref(['format','type']);
        const taxaDataArr = Vue.ref([]);
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
            taxaDataArr.value = [];
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
                        if(file['scientificname']){
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
                const taxaArr = [];
                csvFileData.forEach((dataObj, index) => {
                    if(dataObj.hasOwnProperty('filename') && dataObj['filename']){
                        if(dataObj.hasOwnProperty('scientificname') && dataObj['scientificname'] !== '' && !taxaArr.includes(dataObj['scientificname'])){
                            taxaArr.push(dataObj['scientificname']);
                        }
                    }
                    else{
                        csvFileData.splice(index,1);
                    }
                });
                //setTaxaData(taxaArr);
            }
        }

        function processMediaFileData(file, csvData) {
            if(file.name.endsWith(".mp4")){
                file['metadata'].push({name: 'type', value: 'MovingImage', system: true});
                file['metadata'].push({name: 'format', value: 'video/mp4', system: true});
            }
            else if(file.name.endsWith(".webm")){
                file['metadata'].push({name: 'type', value: 'MovingImage', system: true});
                file['metadata'].push({name: 'format', value: 'video/webm', system: true});
            }
            else if(file.name.endsWith(".ogg")){
                file['metadata'].push({name: 'type', value: 'MovingImage', system: true});
                file['metadata'].push({name: 'format', value: 'video/ogg', system: true});
            }
            else if(file.name.endsWith(".mp3")){
                file['metadata'].push({name: 'type', value: 'Sound', system: true});
                file['metadata'].push({name: 'format', value: 'audio/mpeg', system: true});
            }
            else if(file.name.endsWith(".wav")){
                file['metadata'].push({name: 'type', value: 'Sound', system: true});
                file['metadata'].push({name: 'format', value: 'audio/wav', system: true});
            }
            else if(file.name.endsWith(".zc")){
                file['metadata'].push({name: 'type', value: 'Sound', system: true});
                file['metadata'].push({name: 'format', value: '', system: true});
            }
            else{
                file['metadata'].push({name: 'type', value: '', system: true});
                file['metadata'].push({name: 'format', value: '', system: true});
            }
            file['metadata'].push({name: 'action', value: 'uploadTaxonMedia', system: true});
            file['metadata'].push({name: 'title', value: ((csvData && csvData.hasOwnProperty('title') && csvData['title'] !== '') ? csvData['title'] : ''), system: false});
            file['metadata'].push({name: 'creator', value: ((csvData && csvData.hasOwnProperty('creator') && csvData['creator'] !== '') ? csvData['creator'] : ''), system: false});
            file['metadata'].push({name: 'description', value: ((csvData && csvData.hasOwnProperty('description') && csvData['description'] !== '') ? csvData['description'] : ''), system: false});
            file['metadata'].push({name: 'locationcreated', value: ((csvData && csvData.hasOwnProperty('locationcreated') && csvData['locationcreated'] !== '') ? csvData['locationcreated'] : ''), system: false});
            file['metadata'].push({name: 'language', value: ((csvData && csvData.hasOwnProperty('language') && csvData['language'] !== '') ? csvData['language'] : ''), system: false});
            file['metadata'].push({name: 'usageterms', value: ((csvData && csvData.hasOwnProperty('usageterms') && csvData['usageterms'] !== '') ? csvData['usageterms'] : ''), system: false});
            file['metadata'].push({name: 'rights', value: ((csvData && csvData.hasOwnProperty('rights') && csvData['rights'] !== '') ? csvData['rights'] : ''), system: false});
            file['metadata'].push({name: 'owner', value: ((csvData && csvData.hasOwnProperty('owner') && csvData['owner'] !== '') ? csvData['owner'] : ''), system: false});
            file['metadata'].push({name: 'publisher', value: ((csvData && csvData.hasOwnProperty('publisher') && csvData['publisher'] !== '') ? csvData['publisher'] : ''), system: false});
            file['metadata'].push({name: 'contributor', value: ((csvData && csvData.hasOwnProperty('contributor') && csvData['contributor'] !== '') ? csvData['contributor'] : ''), system: false});
            file['metadata'].push({name: 'bibliographiccitation', value: ((csvData && csvData.hasOwnProperty('bibliographiccitation') && csvData['bibliographiccitation'] !== '') ? csvData['bibliographiccitation'] : ''), system: false});
            file['metadata'].push({name: 'furtherinformationurl', value: ((csvData && csvData.hasOwnProperty('furtherinformationurl') && csvData['furtherinformationurl'] !== '') ? csvData['furtherinformationurl'] : ''), system: false});
            file['metadata'].push({name: 'accessuri', value: ((csvData && csvData.hasOwnProperty('accessuri') && csvData['accessuri'] !== '') ? csvData['accessuri'] : ''), system: false});
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

        function setCsvFileData() {
            if(fileArr.length > 0 && csvFileData.length > 0){
                fileArr.forEach((file) => {
                    let csvData = csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.toLowerCase());
                    if(!csvData){
                        csvData = csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.substring(0, file.name.lastIndexOf('.')).toLowerCase());
                    }
                    if(csvData){
                        let tid = null;
                        const keys = Object.keys(csvData);
                        keys.forEach((key) => {
                            if(key !== 'filename' && csvData[key] !== ''){
                                if(key === 'scientificname'){
                                    const taxonData = taxaDataArr.value.find((obj) => obj.sciname.toLowerCase() === csvData[key].toLowerCase());
                                    if(taxonData){
                                        tid = taxonData['tid'];
                                    }
                                    file['scientificname'] = csvData[key];
                                    file['tid'] = tid;
                                }
                                else{
                                    const existingData = file['metadata'].find((obj) => obj.name === key);
                                    if(existingData){
                                        existingData['value'] = csvData[key];
                                    }
                                    else{
                                        file['metadata'].push({name: key, value: csvData[key], system: systemProperties.value.includes(key)});
                                    }
                                }
                            }
                        });
                    }
                });
            }
        }

        function setTaxaData(nameArr, fileName = null) {
            const formData = new FormData();
            formData.append('taxa', JSON.stringify(nameArr));
            formData.append('action', 'getTaxaArrFromNameArr');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    taxaDataArr.value = taxaDataArr.value.concat(resObj);
                    if(fileName && resObj.length === 1){
                        const file = fileArr.find((obj) => obj.name.toLowerCase() === fileName.toLowerCase());
                        file['scientificname'] = resObj[0]['sciname'];
                        file['tid'] = resObj[0]['tid'];
                        uploaderRef.value.updateFileStatus(file,new Date().toTimeString());
                    }
                    updateMediaDataTids();
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

        function updateMediaDataTids() {
            fileArr.forEach((file) => {
                if(!file.hasOwnProperty('tid') || !file.tid || file.tid === ''){
                    const sciname = file.scientificname;
                    if(sciname){
                        const taxonData = taxaDataArr.value.find((obj) => obj.sciname.toLowerCase() === sciname.toLowerCase());
                        if(taxonData){
                            file.tid = taxonData['tid'];
                            file.errorMessage = '';
                        }
                        else{
                            file.errorMessage = 'Scientific name not found in taxonomic thesaurus';
                        }
                        uploaderRef.value.updateFileStatus(file,new Date().toTimeString());
                    }
                }
            });
        }

        function updateMediaScientificName(taxonObj) {
            const file = fileArr.find((obj) => obj.name.toLowerCase() === taxonObj['filename'].toLowerCase());
            file['scientificname'] = taxonObj['sciname'];
            file['tid'] = taxonObj['tid'];
            if(taxonObj['sciname'] && taxonObj['tid']){
                file['errorMessage'] = null;
            }
            else if(taxonObj['sciname']){
                file['errorMessage'] = 'Scientific name not found in taxonomic thesaurus';
            }
            else{
                file['errorMessage'] = 'Scientific name required';
            }
            uploaderRef.value.updateFileStatus(file, new Date().toTimeString());
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
            if(files[0].hasOwnProperty('tid') && files[0].tid && Number(files[0].tid) > 0){
                const typeData = files[0]['metadata'].find((obj) => obj.name === 'type');
                if(typeData.value === 'StillImage' || typeData.value === 'MovingImage' || typeData.value === 'Sound'){
                    return {
                        url: taxaProfileApiUrl,
                        formFields: files[0]['metadata'],
                        fieldName: (typeData.value === 'StillImage' ? 'imgfile' : 'medfile')
                    }
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }

        function validateFiles(files) {
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
                                //processMediaFileData(file, csvData);
                            }
                            else{
                                file['uploadType'] = 'image';
                                file['uploadMetadata'] = Object.assign({}, imageStore.getBlankImageRecord);
                                //processImageFileData(file, csvData);
                            }
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
            setCsvFileData();
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
            processUploaded,
            removePickedFile,
            updateMediaScientificName,
            uploadFiles,
            validateFiles
        }
    }
};
