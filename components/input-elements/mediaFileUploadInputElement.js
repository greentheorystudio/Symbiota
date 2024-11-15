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
            default: null
        },
        showStart: {
            type: Boolean,
            default: true
        },
        taxon: {
            type: Object,
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
                            <div v-if="showStart">
                                <q-btn color="positive" @click="uploadFiles();" label="Start Upload" :disabled="fileArr.length === 0" />
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
                    <q-uploader ref="uploaderRef" class="fit" :style="uploaderStyle" color="grey-8" :filter="validateFiles" multiple hide-upload-btn flat>
                        <template v-slot:header="scope">
                            <div class="full-width row justify-between">
                                <div class="row no-wrap justify-start q-pa-sm q-gutter-xs">
                                    <q-spinner v-if="scope.isUploading" class="q-uploader__spinner"></q-spinner>
                                    <div class="q-uploader__subtitle text-bold">Total upload size: {{ queueSizeLabel }}</div>
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
                                                            <template v-for="key in Object.keys(file['uploadMetadata'])">
                                                                <template v-if="file['uploadMetadata'][key] && file['uploadMetadata'][key] !== ''">
                                                                    <span class="q-mr-xs">
                                                                        <span class="text-bold">{{ key }}:</span> {{ key === 'tagArr' ? JSON.stringify(file['uploadMetadata'][key]) : file['uploadMetadata'][key] }}
                                                                    </span>
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
                                                            <q-btn color="grey-4" class="black-border text-black" @click="openDataEditor(file['uploadMetadata']);" label="Edit Metadata" dense/>
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
                :new-media-data="editData"
                :show-popup="showMediaEditorPopup"
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
        const { getSubstringByRegEx, hideWorking, parseCsvFile, showNotification, showWorking } = useCore();
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
        const identifierArr = Vue.ref([]);
        const identifierData = Vue.ref({});
        const maxUploadFilesize = baseStore.getMaxUploadFilesize;
        const processingArr = Vue.ref([]);
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

        Vue.watch(propsRefs.occId, () => {
            setOccid();
        });

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
                        if(file['catalognumber']){
                            errorMessage = 'Catalog number was not found in the database';
                        }
                        else{
                            errorMessage = 'Catalog number required';
                        }
                    }
                }
                else if(!file['uploadMetadata']['tid']){
                    errorMessage = 'Scientific name required';
                }
            }
            return errorMessage;
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
                        if(collId.value > 0 && dataObj.hasOwnProperty(props.identifierField) && dataObj[props.identifierField] !== '' && !identifierArr.value.includes(dataObj[props.identifierField])){
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
                const imageFile = ((urlMethodUrl.value.toLowerCase().endsWith('.jpg') || urlMethodUrl.value.toLowerCase().endsWith('.jpeg') || urlMethodUrl.value.toLowerCase().endsWith('.png')) ? '1' : '0');
                const file = {
                    name: urlMethodUrl.value.split('/').pop(),
                    size: 0,
                    externalUrl: urlMethodUrl.value,
                    copyToServer: urlMethodCopyFile.value
                };
                if(urlMethodCopyFile.value){
                    const formData = new FormData();
                    formData.append('url', urlMethodUrl.value);
                    formData.append('image', imageFile);
                    formData.append('action', 'getFileInfoFromUrl');
                    fetch(proxyServiceApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                file.height = resObj['fileHeight'];
                                file.size = resObj['fileSize'];
                                file.width = resObj['fileWidth'];
                                validateFiles([file]);
                                resetUrlMethodSettings();
                            });
                        }
                    });
                }
                else{
                    validateFiles([file]);
                    resetUrlMethodSettings();
                }
            }
        }

        function processUpload(file) {
            let action;
            processingArr.value.push({file: file['uploadMetadata']['filename'], status: 'processing'});
            if(file['uploadMetadata']['sourceurl']){
                if(file['copyToServer']){
                    action = file['uploadMetadata']['type'] === 'StillImage' ? 'addImageFromUrl' : 'addMediaFromUrl';
                }
                else{
                    action = file['uploadMetadata']['type'] === 'StillImage' ? 'addImage' : 'addMedia';
                }
            }
            else{
                action = file['uploadMetadata']['type'] === 'StillImage' ? 'addImageFromFile' : 'addMediaFromFile';
            }
            if(file['uploadMetadata']['type'] === 'StillImage'){
                if(action === 'addImage'){
                    file['uploadMetadata']['url'] = file['uploadMetadata']['sourceurl'];
                    file['uploadMetadata']['originalurl'] = file['uploadMetadata']['sourceurl'];
                }
                uploadImageFile(file, action, (id, file) => {
                    uploadPostProcess(id, file);
                });
            }
            else{
                if(action === 'addMedia'){
                    file['uploadMetadata']['accessuri'] = file['uploadMetadata']['sourceurl'];
                }
                uploadMediaFile(file, action, (id, file) => {
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
                        const dataKeys = Object.keys(csvData);
                        if(dataKeys.includes(props.identifierField)){
                            file['recordIdentifier'] = csvData[props.identifierField];
                            file['occurrenceData'] = csvData;
                        }
                        dataKeys.forEach((key) => {
                            if(key !== 'filename' && csvData[key] && csvData[key] !== ''){
                                if(key === 'scientificname' || key === 'sciname'){
                                    file['uploadMetadata']['sciname'] = csvData[key];
                                }
                                else if(file['uploadMetadata'].hasOwnProperty(key)){
                                    file['uploadMetadata'][key] = csvData[key];
                                }
                            }
                        });
                    }
                    if(!file['uploadMetadata']['tid'] && file['uploadMetadata']['sciname'] && taxaData.value.hasOwnProperty(file['uploadMetadata']['sciname'].toLowerCase())){
                        file['uploadMetadata']['tid'] = taxaData.value[file['uploadMetadata']['sciname'].toLowerCase()]['tid'];
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
                response.json().then((resObj) => {
                    identifierArr.value.length = 0;
                    Object.keys(resObj).forEach((key) => {
                        identifierData.value[key] = Object.assign({}, resObj[key]);
                    });
                    setFileIdentifierData();
                });
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
                if(fileListRef.value && fileListRef.value.clientHeight > 0){
                    uploaderStyle.value = 'height: ' + (fileListRef.value.clientHeight + 50) + 'px;';
                }
            }, 400 );
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
            const sizeMb = (Math.round((size / 1000000) * 10 ) / 100);
            queueSize.value = size;
            queueSizeLabel.value = sizeMb.toString() + 'MB';
            setUploaderStyle();
        }

        function uploadFiles() {
            if(fileArr.length > 0){
                showWorking();
                processingArr.value.length = 0;
                fileArr.forEach((file) => {
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
                        occurrenceData[props.identifierField] = file['recordIdentifier'];
                        occurrenceData['sciname'] = file['uploadMetadata']['sciname'];
                        occurrenceData['tid'] = file['uploadMetadata']['tid'];
                        const formData = new FormData();
                        formData.append('collid', collId.value.toString());
                        formData.append('occurrence', JSON.stringify(occurrenceData));
                        formData.append('action', 'createOccurrenceRecord');
                        fetch(occurrenceApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.text().then((res) => {
                                if(res && Number(res) > 0){
                                    file['uploadMetadata']['occid'] = res;
                                    processUpload(file);
                                }
                            });
                        });
                    }
                    else if((collId.value > 0 && Number(file['uploadMetadata']['occid']) > 0) || (collId.value === 0 && Number(file['uploadMetadata']['tid']) > 0)){
                        processUpload(file);
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
                response.text().then((res) => {
                    callback(res, file);
                });
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
                response.text().then((res) => {
                    callback(res, file);
                });
            });
        }

        function uploadPostProcess(id, file) {
            if(id && Number(id) > 0){
                removePickedFile(file);
            }
            const fileProcess = processingArr.value.find(proc => proc['file'] === file['uploadMetadata']['filename']);
            fileProcess['status'] = 'complete';
            const currentProcess = processingArr.value.find(proc => proc['status'] === 'processing');
            if(!currentProcess){
                hideWorking();
                context.emit('upload:complete');
            }
        }

        function validateFiles(files) {
            files.forEach((file) => {
                const existingData = fileArr.find((obj) => obj.name.toLowerCase() === file.name.toLowerCase());
                if(file.name.endsWith('.csv')){
                    parseCsvFile(file, (csvData) => {
                        csvFileData = csvData;
                        processCsvFileData();
                    });
                }
                else if(!existingData){
                    const fileSizeMb = Number(file.size) > 0 ? Math.round((file.size / 1000000) * 10) / 100 : 0;
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
                            file['filenameRecordIdentifier'] = (collId.value > 0 && props.identifierRegEx) ? getSubstringByRegEx(props.identifierRegEx, file.name) : null;
                            if(file['filenameRecordIdentifier'] && !identifierArr.value.includes(file['filenameRecordIdentifier'])){
                                identifierArr.value.push(file['filenameRecordIdentifier']);
                            }
                            file['recordIdentifier'] = null;
                            file['uploadMetadata']['sciname'] = null;
                            if(Number(props.occId) > 0){
                                file['uploadMetadata']['occid'] = props.occId;
                            }
                            if(Number(props.taxonId) > 0){
                                file['uploadMetadata']['tid'] = props.taxonId;
                            }
                            if(props.collection){
                                file['uploadMetadata']['sortsequence'] = 50;
                            }
                            else{
                                file['uploadMetadata']['sortsequence'] = 20;
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

        return {
            csvFileDataUploaded,
            editData,
            fileArr,
            fileListRef,
            queueSize,
            queueSizeLabel,
            selectedUploadMethod,
            showImageEditorPopup,
            showMediaEditorPopup,
            uploaderRef,
            uploaderStyle,
            uploadMethodOptions,
            urlMethodCopyFile,
            urlMethodUrl,
            cancelUpload,
            getFileErrorMessage,
            openDataEditor,
            processExternalUrl,
            removePickedFile,
            updateFileMetadata,
            uploadFiles,
            validateFiles
        }
    }
};
