const mediaFileUploadInputElement = {
    props: {
        collection: {
            type: Object,
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
            default: 0
        },
        showStart: {
            type: Boolean,
            default: true
        },
        tabindex: {
            type: Number,
            default: 0
        },
        taxon: {
            type: Object,
            default: null
        },
        taxonId: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-card flat bordered class="black-border fit bg-grey-4">
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="full-width row justify-between">
                    <div class="text-h6 text-bold">{{ label }}</div>
                    <div class="row justify-end q-gutter-sm">
                        <div class="col-5">
                            <text-field-input-element data-type="int" label="Batch Sort Sequence" :value="batchSortSequenceVal" min-value="1" :clearable="false" @update:value="processBatchSortSequenceChange"></text-field-input-element>
                        </div>
                        <q-btn-toggle v-model="selectedUploadMethod" :options="uploadMethodOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" aria-label="Upload method" tabindex="0"></q-btn-toggle>
                    </div>
                </div>
                <template v-if="selectedUploadMethod === 'upload'">
                    <div class="full-width row justify-between">
                        <div class="row justify-start q-gutter-sm">
                            <div>
                                <q-btn color="primary" @click="uploaderRef.pickFiles();" label="Choose Files" :tabindex="tabindex" />
                            </div>
                            <div>
                                <q-btn color="negative" @click="cancelUpload();" label="Clear Files" :disabled="fileArr.length === 0" :tabindex="tabindex" />
                            </div>
                        </div>
                        <div class="row justify-end">
                            <div v-if="showStart">
                                <q-btn color="positive" @click="uploadFiles();" label="Start Upload" :disabled="fileArr.length === 0" :tabindex="tabindex" />
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
                                <q-btn color="primary" @click="processExternalUrl();" label="Process URL" :tabindex="tabindex" />
                            </div>
                        </div>
                    </div>
                </template>
                <div class="full-width" :class="fileArr.length === 0 ? 'hidden' : ''">
                    <q-uploader ref="uploaderRef" class="fit" :style="uploaderStyle" color="grey-8" :filter="validateFiles" multiple hide-upload-btn flat>
                        <template v-slot:header="scope">
                            <div class="full-width row justify-between">
                                <div class="row no-wrap justify-start q-pa-sm q-gutter-xs">
                                    <q-spinner v-if="scope.isUploading" class="q-uploader__spinner"></q-spinner>
                                    <div class="q-uploader__subtitle text-bold">Total upload size: {{ queueSizeLabel }}</div>
                                    <q-uploader-add-trigger></q-uploader-add-trigger>
                                </div>
                                <div class="row no-wrap justify-end q-pa-sm q-gutter-xs">
                                    <span v-if="csvFileDataUploaded" class="text-bold text-red">
                                        CSV Data Uploaded
                                    </span>
                                    <div class="q-uploader__subtitle text-bold">
                                        <span class="text-bold">Queued: {{ filesQueued }}</span>/<span class="text-bold text-green">Uploaded: {{ filesUploaded }}</span>/<span class="text-bold text-red">Errors: {{ filesError }}</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-slot:list="scope">
                            <div ref="fileListRef">
                                <q-list separator class="fit">
                                    <q-item v-for="file in fileArr" :key="file.__key" class="full-width">
                                        <q-item-section>
                                            <div class="full-width row">
                                                <div class="col-2">
                                                    <div v-if="file.hasOwnProperty('externalUrl') && file['uploadMetadata']['type'] === 'StillImage'">
                                                        <q-img :src="file['externalUrl']" spinner-color="white" class="media-thumbnail"></q-img>
                                                    </div>
                                                    <div v-else-if="file.__img">
                                                        <q-img :src="file.__img.src" spinner-color="white" class="media-thumbnail"></q-img>
                                                    </div>
                                                    <div v-else class="text-h6 text-bold">
                                                        {{ file.name.split('.').pop() + ' file' }}
                                                    </div>
                                                </div>
                                                <div class="col-8 column q-pl-md overflow-hidden ellipsis">
                                                    <div class="row full-width justify-between">
                                                        <div class="ellipsis">
                                                            {{ file.name.split('/').pop() }}
                                                        </div>
                                                        <div caption class="row justify-end">
                                                            {{ file.correctedSizeLabel }}
                                                        </div>
                                                    </div>
                                                    <div class="row full-width">
                                                        <template v-if="file['filenameRecordIdentifier']">
                                                            <div class="ellipsis-2-lines q-mr-xs">
                                                                <span class="text-bold">{{ identifierField }}:</span> {{ file['filenameRecordIdentifier'] }}
                                                            </div>
                                                        </template>
                                                        <template v-for="key in Object.keys(file['uploadMetadata'])">
                                                            <template v-if="file['uploadMetadata'][key] && file['uploadMetadata'][key] !== '' && key !== 'filename'">
                                                                <div class="q-mr-xs overflow-hidden ellipsis">
                                                                    <span class="text-bold">{{ key }}:</span> {{ key === 'tagArr' ? JSON.stringify(file['uploadMetadata'][key]) : file['uploadMetadata'][key] }}
                                                                </div>
                                                            </template>
                                                        </template>
                                                    </div>
                                                    <template v-if="!file['uploadErrorMessage']">
                                                        <div v-if="getFileErrorMessage(file)" class="text-bold text-red">
                                                            {{ getFileErrorMessage(file) }}
                                                        </div>
                                                        <div v-else class="text-bold text-green">
                                                            {{ getFileUploadMessage(file) }}
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="text-bold text-red">
                                                            {{ file['uploadErrorMessage'] }}
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="col-2 row justify-end">
                                                    <div class="column q-gutter-xs">
                                                        <div class="row justify-end">
                                                            <q-btn color="negative" class="black-border" @click="removePickedFile(file);" label="Remove" dense :tabindex="tabindex" />
                                                        </div>
                                                        <div class="row justify-end">
                                                            <q-btn color="grey-4" class="black-border text-black" @click="openDataEditor(file['uploadMetadata']);" label="Edit Metadata" dense :tabindex="tabindex" />
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
        <template v-if="showImageEditorPopup">
            <image-editor-popup
                :new-image-data="editData"
                :show-popup="showImageEditorPopup"
                @update:image-data="updateFileMetadata"
                @close:popup="showImageEditorPopup = false"
            ></image-editor-popup>
        </template>
        <template v-if="showMediaEditorPopup">
            <media-editor-popup
                :collection="collection"
                :new-media-data="editData"
                :show-popup="showMediaEditorPopup"
                :taxon="taxon"
                :upload-path="uploadPath"
                @update:media-data="updateFileMetadata"
                @close:popup="showMediaEditorPopup = false"
            ></media-editor-popup>
        </template>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'image-editor-popup': imageEditorPopup,
        'media-editor-popup': mediaEditorPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { csvToArray, getSubstringByRegEx, hideWorking, parseFile, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const imageStore = useImageStore();
        const mediaStore = useMediaStore();

        const acceptedMediaTypes = [
            {extension: 'jpeg', type: 'StillImage', mimetype: 'image/jpeg'},
            {extension: 'jpg', type: 'StillImage', mimetype: 'image/jpeg'},
            {extension: 'png', type: 'StillImage', mimetype: 'image/png'},
            {extension: 'zc', type: 'Zipkey', mimetype: 'application/zc'},
            {extension: 'mp4', type: 'MovingImage', mimetype: 'video/mp4'},
            {extension: 'webm', type: 'MovingImage', mimetype: 'video/webm'},
            {extension: 'ogg', type: 'MovingImage', mimetype: 'video/ogg'},
            {extension: 'wav', type: 'Sound', mimetype: 'audio/wav'},
            {extension: 'mp3', type: 'Sound', mimetype: 'audio/mpeg'}
        ];
        const batchSortSequenceVal = Vue.ref(0);
        const collId = Vue.computed(() => {
            return props.collection ? Number(props.collection.collid) : 0;
        });
        let csvFileData = [];
        const csvFileDataUploaded = Vue.computed(() => {
            return csvFileData.length > 0;
        });
        const editData = Vue.ref({});
        const editFile = Vue.ref(null);
        const fileArr = Vue.reactive([]);
        const fileListRef = Vue.ref(null);
        const filesError = Vue.ref(0);
        const filesQueued = Vue.computed(() => {
            return fileArr.length;
        });
        const filesUploaded = Vue.ref(0);
        const identifierArr = Vue.ref([]);
        const identifierData = Vue.ref({});
        const maxUploadFilesize = baseStore.getMaxUploadFilesize;
        const processingArr = Vue.ref([]);
        const processingCsvData = Vue.ref(false);
        const propsRefs = Vue.toRefs(props);
        const queueSize = Vue.ref(0);
        const queueSizeLabel = Vue.ref('');
        const selectedUploadMethod = Vue.ref('upload');
        const showImageEditorPopup = Vue.ref(false);
        const showMediaEditorPopup = Vue.ref(false);
        const taxaArr = Vue.ref([]);
        const taxaData = Vue.ref({});
        const uploaderRef = Vue.ref(null);
        const uploaderStyle = Vue.ref('');
        const uploadMethodOptions = [
            {label: 'Local Files', value: 'upload'},
            {label: 'From URL', value: 'url'}
        ];
        const uploadPath = Vue.computed(() => {
            let path = '';
            if(props.collection){
                if(props.collection.institutioncode){
                    path += props.collection.institutioncode;
                }
                if(props.collection.institutioncode && props.collection.collectioncode){
                    path += '_';
                }
                if(props.collection.collectioncode){
                    path += props.collection.collectioncode;
                }
            }
            else if(props.taxon){
                if(props.taxon.family){
                    path += props.taxon.family;
                }
                else{
                    path += props.taxon['unitname1'];
                }
            }
            else{
                path += 'general';
            }
            return path;
        });
        const urlMethodCopyFile = Vue.ref(true);
        const urlMethodUrl = Vue.ref(null);
        const urlProcessingArr = Vue.ref([]);

        Vue.watch(propsRefs.occId, () => {
            setOccid();
        });

        function addExternalFileToQueue(url, copyToServer) {
            const imageFile = ((url.toLowerCase().endsWith('.jpg') || url.toLowerCase().endsWith('.jpeg') || url.toLowerCase().endsWith('.png')) ? '1' : '0');
            const file = {
                name: url,
                size: 0,
                externalUrl: url,
                copyToServer: copyToServer
            };
            if(copyToServer){
                urlProcessingArr.value.push(url);
                const formData = new FormData();
                formData.append('url', url);
                formData.append('image', imageFile);
                formData.append('action', 'getFileInfoFromUrl');
                fetch(proxyServiceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resObj) => {
                    file.height = resObj['fileHeight'];
                    file.size = resObj['fileSize'];
                    file.width = resObj['fileWidth'];
                    validateFiles([file]);
                    const index = urlProcessingArr.value.indexOf(url);
                    urlProcessingArr.value.splice(index, 1);
                    if(urlProcessingArr.value.length === 0){
                        hideWorking();
                    }
                });
            }
            else{
                validateFiles([file]);
            }
        }

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
                if(collId.value > 0){
                    if(!file['uploadMetadata']['occid'] && !props.createOccurrence){
                        const idField = props.identifierField === 'catalognumber' ? 'Catalog number' : 'Other catalog number';
                        if(file['filenameRecordIdentifier']){
                            errorMessage = idField + ' could not be matched to a record in the database';
                        }
                        else{
                            errorMessage = idField + ' value required';
                        }
                    }
                }
                else if(!file['uploadMetadata']['tid'] && !file['uploadMetadata']['scientificname'] && !file['uploadMetadata']['scientificname']){
                    errorMessage = 'Scientific name required';
                }
                else if(!file['uploadMetadata']['tid'] && (file['uploadMetadata']['scientificname'] || file['uploadMetadata']['scientificname'] )){
                    errorMessage = 'Taxon not in the database';
                }
            }
            return errorMessage;
        }

        function getFileUploadMessage(file) {
            let message = 'Ready to upload';
            if(Number(props.occId) === 0 && Number(props.taxonId) === 0 && collId.value > 0){
                if(Number(file['uploadMetadata']['occid']) > 0){
                    message = 'Ready to upload - will associate with existing occurrence record';
                }
                else if(props.createOccurrence){
                    message = 'Ready to upload - will associate with new occurrence record';
                }
            }
            return message;
        }

        function openDataEditor(data) {
            editFile.value = data['filename'];
            setEditData();
            if(data['type'] === 'StillImage'){
                showImageEditorPopup.value = true;
            }
            else{
                showMediaEditorPopup.value = true;
            }
        }

        function parseScinameFromFilename(fileName) {
            let adjustedFileName = fileName.replace(/_/g, ' ');
            adjustedFileName = adjustedFileName.replace(/\s+/g, ' ').trim();
            const lastDotIndex = adjustedFileName.lastIndexOf('.');
            adjustedFileName = adjustedFileName.substring(0, lastDotIndex);
            const lastSpaceIndex = adjustedFileName.lastIndexOf(' ');
            if(lastSpaceIndex){
                const lastPartAfterSpace = adjustedFileName.substring(lastSpaceIndex);
                if(Number(lastPartAfterSpace) > 0){
                    adjustedFileName = adjustedFileName.substring(0, lastSpaceIndex);
                }
            }
            setTaxaData([adjustedFileName], fileName);
        }

        function processBatchSortSequenceChange(value) {
            batchSortSequenceVal.value = value;
            if(fileArr.length > 0){
                fileArr.forEach((file) => {
                    file['uploadMetadata']['sortsequence'] = batchSortSequenceVal.value;
                });
            }
        }

        function processCsvFileData() {
            if(csvFileData.length > 0){
                taxaArr.value = [];
                csvFileData.forEach((dataObj, index) => {
                    if(dataObj && dataObj.hasOwnProperty('sourceurl') && dataObj['sourceurl']){
                        dataObj['filename'] = dataObj['sourceurl'];
                        addExternalFileToQueue(dataObj['sourceurl'], true);
                    }
                    if(dataObj && dataObj.hasOwnProperty('filename') && dataObj['filename']){
                        if(dataObj.hasOwnProperty('scientificname') && dataObj['scientificname'] !== '' && !taxaArr.value.includes(dataObj['scientificname'])){
                            taxaArr.value.push(dataObj['scientificname']);
                        }
                        else if(dataObj.hasOwnProperty('sciname') && dataObj['sciname'] !== '' && !taxaArr.value.includes(dataObj['sciname'])){
                            taxaArr.value.push(dataObj['sciname']);
                        }
                        if(collId.value > 0 && dataObj.hasOwnProperty(props.identifierField) && dataObj[props.identifierField] !== '' && !identifierArr.value.includes(dataObj[props.identifierField])){
                            identifierArr.value.push(dataObj[props.identifierField]);
                        }
                    } else{
                        csvFileData.splice(index,1);
                    }
                });
                processingCsvData.value = false;
                setFileIdentifierData();
            }
            else{
                processingCsvData.value = false;
                if(urlProcessingArr.value.length === 0){
                    hideWorking();
                }
            }
        }

        function processExternalUrl() {
            if(urlMethodUrl.value){
                addExternalFileToQueue(urlMethodUrl.value, urlMethodCopyFile.value);
                resetUrlMethodSettings();
            }
        }

        function processUpload(file) {
            let action;
            if(file['uploadMetadata']['sourceurl']){
                processingArr.value.push({file: file['uploadMetadata']['sourceurl'], status: 'processing'});
                if(file['copyToServer']){
                    action = file['uploadMetadata']['type'] === 'StillImage' ? 'addImageFromUrl' : 'addMediaFromUrl';
                }
                else{
                    action = file['uploadMetadata']['type'] === 'StillImage' ? 'addImage' : 'addMedia';
                }
            }
            else{
                processingArr.value.push({file: file['uploadMetadata']['filename'], status: 'processing'});
                action = file['uploadMetadata']['type'] === 'StillImage' ? 'addImageFromFile' : 'addMediaFromFile';
            }
            if(file['uploadMetadata']['type'] === 'StillImage'){
                if(action === 'addImage' && action === 'addImageFromUrl'){
                    file['uploadMetadata']['url'] = file['uploadMetadata']['sourceurl'];
                    file['uploadMetadata']['originalurl'] = file['uploadMetadata']['sourceurl'];
                }
                if(action === 'addImageFromUrl' && file['uploadMetadata']['sourceurl']) {
                    file['uploadMetadata']['filename'] = file['uploadMetadata']['sourceurl'].split('/').pop();
                }
                uploadImageFile(file, action, (id, file) => {
                    if(Number(id) > 0){
                        filesUploaded.value++;
                    }
                    else{
                        filesError.value++;
                        showNotification('negative', ('An error occurred uploading ' + file.name));
                        file['uploadErrorMessage'] = 'An error occurred uploading this file';
                    }
                    uploadPostProcess(id, file);
                });
            }
            else{
                if(action === 'addMedia'){
                    file['uploadMetadata']['accessuri'] = file['uploadMetadata']['sourceurl'];
                }
                uploadMediaFile(file, action, (id, file) => {
                    if(Number(id) > 0){
                        filesUploaded.value++;
                    }
                    else{
                        filesError.value++;
                        showNotification('negative', ('An error occurred uploading ' + file.name));
                        file['uploadErrorMessage'] = 'An error occurred uploading this file';
                    }
                    uploadPostProcess(id, file);
                });
            }
        }

        function removePickedFile(file) {
            const fileIndex = fileArr.indexOf(file);
            fileArr.splice(fileIndex,1);
            updateQueueSize();
        }

        function resetUrlMethodSettings() {
            urlMethodUrl.value = null;
            selectedUploadMethod.value = 'upload';
            urlMethodCopyFile.value = true;
        }

        function setEditData() {
            editData.value = Object.assign({}, fileArr.find((obj) => obj['uploadMetadata']['filename'] === editFile.value));
        }

        function setFileData() {
            if(fileArr.length > 0){
                fileArr.forEach((file) => {
                    let csvData;
                    if(file.hasOwnProperty('externalUrl') && file['externalUrl']){
                        csvData = csvFileData.find((obj) => obj.sourceurl.toLowerCase() === file['externalUrl'].toLowerCase());
                    }
                    else{
                        csvData = csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.toLowerCase());
                        if(!csvData){
                            csvData = csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.substring(0, file.name.lastIndexOf('.')).toLowerCase());
                        }
                    }
                    if(csvData){
                        const dataKeys = Object.keys(csvData);
                        if(dataKeys.includes(props.identifierField)){
                            file['filenameRecordIdentifier'] = csvData[props.identifierField];
                            file['occurrenceData'] = csvData;
                        }
                        dataKeys.forEach((key) => {
                            if(key !== 'filename' && csvData[key] && csvData[key] !== ''){
                                if(key === 'scientificname' || key === 'sciname'){
                                    file['uploadMetadata']['scientificname'] = csvData[key];
                                }
                                else if(file['uploadMetadata'].hasOwnProperty(key)){
                                    file['uploadMetadata'][key] = csvData[key];
                                }
                            }
                        });
                    }
                    if(!file['uploadMetadata']['tid'] && file['uploadMetadata']['scientificname'] && taxaData.value.hasOwnProperty(file['uploadMetadata']['scientificname'].toLowerCase())){
                        file['uploadMetadata']['tid'] = taxaData.value[file['uploadMetadata']['scientificname'].toLowerCase()]['tid'];
                    }
                    if(!file['uploadMetadata']['occid'] && file['filenameRecordIdentifier'] && identifierData.value.hasOwnProperty(file['filenameRecordIdentifier'])){
                        file['uploadMetadata']['occid'] = identifierData.value[file['filenameRecordIdentifier']]['occid'];
                        if(!file['uploadMetadata']['tid'] && identifierData.value[file['filenameRecordIdentifier']]['tid']){
                            file['uploadMetadata']['tid'] = identifierData.value[file['filenameRecordIdentifier']]['tid'];
                        }
                    }
                });
            }
            if(!processingCsvData.value && urlProcessingArr.value.length === 0){
                hideWorking();
            }
        }

        function setFileIdentifierData() {
            if(!props.collection && Number(props.taxonId) === 0 && taxaArr.value.length > 0){
                setTaxaData();
            }
            else if(collId.value > 0 && identifierArr.value.length > 0){
                setIdentifierData();
            }
            else{
                setFileData();
            }
        }

        function setIdentifierData() {
            const formData = new FormData();
            formData.append('collid', collId.value.toString());
            formData.append('identifierField', props.identifierField);
            formData.append('identifiers', JSON.stringify(identifierArr.value));
            formData.append('action', 'getOccurrenceIdDataFromIdentifierArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                identifierArr.value.length = 0;
                Object.keys(resObj).forEach((key) => {
                    identifierData.value[key] = Object.assign({}, resObj[key]);
                });
                setFileIdentifierData();
            });
        }

        function setOccid() {
            fileArr.forEach((file) => {
                file['uploadMetadata']['occid'] = props.occId;
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
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                taxaArr.value.length = 0;
                Object.keys(resObj).forEach((key) => {
                    taxaData.value[key] = Object.assign({}, resObj[key]);
                });
                setFileIdentifierData();
            });
        }

        function setUploaderStyle() {
            uploaderStyle.value = '';
            setTimeout(() => {
                if(fileListRef.value && fileListRef.value.clientHeight > 0){
                    uploaderStyle.value = 'height: ' + (fileListRef.value.clientHeight + 50) + 'px;';
                }
            }, 400 );
        }

        function updateFileArrWithNewOccid(identifier, occid) {
            fileArr.forEach((file) => {
                if(file['filenameRecordIdentifier'] === identifier){
                    file['uploadMetadata']['occid'] = occid;
                }
            });
        }

        function updateFileMetadata(data) {
            const file = fileArr.find((obj) => obj['uploadMetadata']['filename'] === editFile.value);
            if(file){
                file['uploadMetadata'][data.key] = data.value;
                uploaderRef.value.updateFileStatus(file, new Date().toTimeString());
                setEditData();
            }
        }

        function updateQueueSize() {
            let size = 0;
            fileArr.forEach((file) => {
                size += file.size;
            });
            const sizeMb = (size / 1000000).toFixed(2);
            queueSize.value = size;
            queueSizeLabel.value = sizeMb.toString() + 'MB';
            setUploaderStyle();
        }

        function uploadFiles() {
            if(fileArr.length > 0){
                showWorking();
                processingArr.value.length = 0;
                fileArr.forEach((file, index) => {
                    if(!file.hasOwnProperty('height')){
                        file['uploadMetadata']['height'] = file.hasOwnProperty('__img') ? file['__img']['height'] : null;
                    }
                    else{
                        file['uploadMetadata']['height'] = file.height;
                    }
                    if(!file.hasOwnProperty('width')){
                        file['uploadMetadata']['width'] = file.hasOwnProperty('__img') ? file['__img']['width'] : null;
                    }
                    else{
                        file['uploadMetadata']['width'] = file.width;
                    }
                    if(collId.value > 0 && props.createOccurrence && !file['uploadMetadata']['occid']){
                        const occurrenceData = {};
                        occurrenceData['collid'] = collId.value.toString();
                        occurrenceData[props.identifierField] = file['filenameRecordIdentifier'];
                        occurrenceData['sciname'] = file['uploadMetadata']['scientificname'];
                        occurrenceData['tid'] = file['uploadMetadata']['tid'];
                        occurrenceData['processingstatus'] = 'unprocessed';
                        const formData = new FormData();
                        formData.append('collid', collId.value.toString());
                        formData.append('occurrence', JSON.stringify(occurrenceData));
                        formData.append('action', 'createOccurrenceRecord');
                        fetch(occurrenceApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            if(res && Number(res) > 0){
                                file['uploadMetadata']['occid'] = res;
                                updateFileArrWithNewOccid(file['filenameRecordIdentifier'], res);
                                processUpload(file);
                            }
                        });
                    }
                    else if((collId.value > 0 && Number(file['uploadMetadata']['occid']) > 0) || (collId.value === 0 && Number(file['uploadMetadata']['tid']) > 0)){
                        processUpload(file);
                    }
                    else if((index + 1) === fileArr.length){
                        uploadPostProcess(0, file);
                    }
                });
            }
        }

        function uploadImageFile(file, action, callback) {
            const formData = new FormData();
            formData.append('collid', collId.value.toString());
            formData.append('image', JSON.stringify(file['uploadMetadata']));
            formData.append('imgfile', file);
            formData.append('uploadpath', uploadPath.value);
            formData.append('action', action);
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(res, file);
            });
        }

        function uploadMediaFile(file, action, callback) {
            const formData = new FormData();
            formData.append('collid', collId.value.toString());
            formData.append('media', JSON.stringify(file['uploadMetadata']));
            formData.append('medfile', file);
            formData.append('uploadpath', uploadPath.value);
            formData.append('action', action);
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(res, file);
            });
        }

        function uploadPostProcess(id, file) {
            if(id && Number(id) > 0){
                removePickedFile(file);
            }
            let fileProcess;
            if(file['uploadMetadata']['sourceurl']){
                fileProcess = processingArr.value.find(proc => proc['file'] === file['uploadMetadata']['sourceurl']);
            }
            else{
                fileProcess = processingArr.value.find(proc => proc['file'] === file['uploadMetadata']['filename']);
            }
            if(fileProcess){
                fileProcess['status'] = 'complete';
            }
            const currentProcess = processingArr.value.find(proc => proc['status'] === 'processing');
            if(!currentProcess){
                hideWorking();
                showNotification('positive','Upload complete');
                if(filesUploaded.value > 0){
                    context.emit('upload:complete');
                }
            }
        }

        function validateFiles(files) {
            showWorking();
            files.forEach((file) => {
                let existingData;
                if(file.hasOwnProperty('externalUrl') && file['externalUrl']){
                    existingData = fileArr.find((obj) => obj['externalUrl'].toLowerCase() === file['externalUrl'].toLowerCase());
                }
                else{
                    existingData = fileArr.find((obj) => obj.name.toLowerCase() === file.name.toLowerCase());
                }
                if(file.name.endsWith('.csv')){
                    processingCsvData.value = true;
                    parseFile(file, (fileContents) => {
                        csvToArray(fileContents).then((csvData) => {
                            csvFileData = csvData;
                            processCsvFileData();
                        });
                    });
                }
                else if(!existingData){
                    const fileSizeMb = Number(file.size) > 0 ? (file.size / 1000000).toFixed(2) : 0;
                    if(fileSizeMb <= Number(maxUploadFilesize)){
                        const mediaTypeInfo = acceptedMediaTypes.find((mType) => mType.extension === file.name.split('.').pop().toLowerCase());
                        if(mediaTypeInfo){
                            file['correctedSizeLabel'] =   fileSizeMb.toString() + 'MB';
                            if(mediaTypeInfo.type === 'StillImage'){
                                file['uploadMetadata'] = Object.assign({}, imageStore.getBlankImageRecord);
                            }
                            else{
                                file['uploadMetadata'] = Object.assign({}, mediaStore.getBlankMediaRecord);
                            }
                            if(file.hasOwnProperty('externalUrl') && file['externalUrl']){
                                file['uploadMetadata']['sourceurl'] = file['externalUrl'];
                            }
                            file['uploadMetadata']['filename'] = file.name;
                            file['uploadMetadata']['type'] = mediaTypeInfo.type;
                            file['uploadMetadata']['format'] = mediaTypeInfo.mimetype;
                            file['uploadErrorMessage'] = null;
                            file['filenameRecordIdentifier'] = (collId.value > 0 && props.identifierRegEx) ? getSubstringByRegEx(props.identifierRegEx, file.name) : null;
                            if(file['filenameRecordIdentifier'] && !identifierArr.value.includes(file['filenameRecordIdentifier'])){
                                identifierArr.value.push(file['filenameRecordIdentifier']);
                            }
                            file['uploadMetadata']['scientificname'] = null;
                            if(Number(props.occId) > 0){
                                file['uploadMetadata']['occid'] = props.occId;
                            }
                            if(Number(props.taxonId) > 0){
                                file['uploadMetadata']['tid'] = props.taxonId;
                            }
                            file['uploadMetadata']['sortsequence'] = batchSortSequenceVal.value;
                            if(Number(file['uploadMetadata']['tid']) === 0){
                                let tid = null;
                                let csvData = csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.toLowerCase());
                                if(!csvData){
                                    csvData = csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.substring(0, file.name.lastIndexOf('.')).toLowerCase());
                                }
                                if(!csvData || !csvData.hasOwnProperty('scientificname')){
                                    parseScinameFromFilename(file.name);
                                }
                                const sciname = (csvData && csvData.hasOwnProperty('scientificname')) ? csvData['scientificname'] : null;
                                if(sciname){
                                    if(taxaData.value.hasOwnProperty(sciname.toLowerCase())){
                                        tid = taxaData.value[sciname.toLowerCase()]['tid'];
                                    }
                                }
                                file['uploadMetadata']['scientificname'] = sciname;
                                file['uploadMetadata']['tid'] = tid;
                            }
                            if(!file.hasOwnProperty('copyToServer')){
                                file['copyToServer'] = false;
                            }
                            fileArr.push(file);
                            updateQueueSize();
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
            return fileArr;
        }

        Vue.onMounted(() => {
            if(props.collection){
                batchSortSequenceVal.value = 50;
            }
            else{
                batchSortSequenceVal.value = 20;
            }
        });

        return {
            batchSortSequenceVal,
            csvFileDataUploaded,
            editData,
            fileArr,
            fileListRef,
            filesError,
            filesQueued,
            filesUploaded,
            queueSize,
            queueSizeLabel,
            selectedUploadMethod,
            showImageEditorPopup,
            showMediaEditorPopup,
            uploaderRef,
            uploaderStyle,
            uploadMethodOptions,
            uploadPath,
            urlMethodCopyFile,
            urlMethodUrl,
            cancelUpload,
            getFileErrorMessage,
            getFileUploadMessage,
            openDataEditor,
            processBatchSortSequenceChange,
            processExternalUrl,
            removePickedFile,
            updateFileMetadata,
            uploadFiles,
            validateFiles
        }
    }
};
