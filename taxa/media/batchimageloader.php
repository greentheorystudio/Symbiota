<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Media Batch Uploader</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .instruction-container {
                margin-bottom: 20px;
            }
            .button-csv-container, .list-item-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .button-container {
                display: flex;
                justify-content: flex-start;
                gap: 20px;
            }
            .thumbnail-section {
                max-height: 120px;
                max-width: 120px;
            }
            .list-item-delete {
                display: flex;
                justify-content: flex-end;
            }
            .uploader {
                width: 100%;
                min-height: 150px;
            }
        </style>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div class="navpath">
            <a href="../../index.php">Home</a> &gt;&gt;
            <b>Taxa Media Batch Uploader</b>
        </div>
        <div id="innertext">
            <h1>Taxa Media Batch Uploader</h1>
            <template v-if="isEditor">
                <div>
                    <div class="instruction-container">
                        To batch upload taxa image, audio, and video files either click the Add files button to select the files to be uploaded or drag and
                        drop the files onto the box below. A csv spreadheet can also be uploaded to provide further metadata for the files.
                        <a href="../../templates/batchTaxaImageData.csv"><b>Use this template for the csv spreadsheet for image files. </b></a>
                        <a href="../../templates/batchTaxaMediaData.csv"><b>Use this template for the csv spreadsheet for audio and video files.</b></a>
                        Data for image files can be combined with data for audio and video files in the same csv spreadsheet. For each
                        row in the spreadsheet, the value in the filename column must match the filename of the associated file being uploaded.
                    </div>
                    <div class="button-csv-container">
                        <div class="button-container">
                            <q-btn color="positive" class="text-bold" label="Add Files" icon="fas fa-plus" @click="uploaderRef.pickFiles();" glossy></q-btn>
                            <q-btn color="primary" class="text-bold" label="Start Upload" icon="fas fa-upload" @click="initializeUpload();" glossy></q-btn>
                            <q-btn color="warning" class="text-bold" label="Cancel Upload" icon="fas fa-ban" @click="cancelUpload();" glossy></q-btn>
                        </div>
                        <div v-if="csvFileData.length > 0" class="text-bold text-red">
                            CSV Data Uploaded
                        </div>
                    </div>
                    <div class="q-mt-md">
                        <q-uploader ref="uploaderRef" class="uploader q-mx-auto" color="grey-8" :factory="uploadFiles" :filter="validateFiles" @uploaded="processUploaded" multiple>
                            <template v-slot:header="scope">
                                <div class="row no-wrap items-center q-pa-sm q-gutter-xs">
                                    <q-spinner v-if="scope.isUploading" class="q-uploader__spinner"></q-spinner>
                                    <div class="col">
                                        <div class="q-uploader__title">To add files click the Add Files button above or drag and drop files into this box</div>
                                        <div v-if="queueSize > 0" class="q-uploader__subtitle">{{ queueSizeLabel }}</div>
                                    </div>
                                    <q-uploader-add-trigger></q-uploader-add-trigger>
                                </div>
                            </template>
                            <template v-slot:list="scope">
                                <q-list separator>
                                    <q-item v-for="file in scope.files" :key="file.__key" class="list-item-container">
                                        <q-item-section>
                                            <q-item-label class="full-width ellipsis">
                                                {{ file.name }}
                                            </q-item-label>
                                            <q-item-label class="full-width">
                                                <media-scientific-name-auto-complete :sciname="file.scientificname ? file.scientificname : null" label="Scientific Name" :filename="file.name" limit-to-thesaurus="true" accepted-taxa-only="true" @update:mediataxon="updateMediaScientificName"></media-scientific-name-auto-complete>
                                            </q-item-label>
                                            <q-item-label v-if="file.errorMessage" class="full-width text-bold text-red">
                                                {{ file.errorMessage }}
                                            </q-item-label>
                                            <q-item-label v-else class="full-width text-bold text-green">
                                                Ready to upload
                                            </q-item-label>
                                            <q-item-label v-if="file.additionalData" class="full-width">
                                                Additional Data:
                                                <q-item-label caption>
                                                    <template v-for="data in file.metadata">
                                                        <template v-if="!data.system && data.value && data.value !== ''">
                                                            <span class="text-bold q-ml-xs">{{ data.name }}:</span> {{ data.value }}
                                                        </template>
                                                    </template>
                                                </q-item-label>
                                            </q-item-label>
                                            <q-item-label caption>
                                                {{ file.correctedSizeLabel }}
                                            </q-item-label>
                                        </q-item-section>
                                        <q-item-section top class="col-2 gt-sm"></q-item-section>
                                        <q-item-section v-if="file.__img" class="thumbnail-section">
                                            <q-img :src="file.__img.src" spinner-color="white"></q-img>
                                        </q-item-section>
                                        <q-item-section>
                                            <div class="list-item-delete">
                                                <q-btn color="negative" class="text-bold" label="Remove" icon="fas fa-times" @click="removePickedFile(file);" glossy dense></q-btn>
                                            </div>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </template>
                        </q-uploader>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="text-weight-bold">You do not have permissions to access this tool</div>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const mediaScientificNameAutoComplete = {
                props: {
                    acceptedTaxaOnly: {
                        type: Boolean,
                        default: false
                    },
                    filename: {
                        type: String,
                        default: null
                    },
                    label: {
                        type: String,
                        default: 'Scientific Name'
                    },
                    limitToThesaurus: {
                        type: Boolean,
                        default: false
                    },
                    sciname: {
                        type: String,
                        default: null
                    }
                },
                template: `
                    <single-scientific-common-name-auto-complete :sciname="sciname" :label="label" :limit-to-thesaurus="limitToThesaurus" :accepted-taxa-only="acceptedTaxaOnly" @update:sciname="updateMediaTaxon"></single-scientific-common-name-auto-complete>
                `,
                components: {
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
                },
                setup(props, context) {
                    function updateMediaTaxon(taxonObj) {
                        const resObj = {};
                        resObj['filename'] = props.filename;
                        resObj['sciname'] = taxonObj ? taxonObj.sciname : null;
                        resObj['tid'] = taxonObj ? taxonObj.tid : null;
                        context.emit('update:mediataxon', resObj);
                    }

                    return {
                        updateMediaTaxon
                    }
                }
            };

            const taxaBatchMediaUploaderModule = Vue.createApp({
                components: {
                    'media-scientific-name-auto-complete': mediaScientificNameAutoComplete
                },
                setup() {
                    const store = useBaseStore();
                    const csvFileData = Vue.ref([]);
                    const fileArr = Vue.ref([]);
                    const isEditor = Vue.ref(false);
                    const maxUploadFilesize = store.getMaxUploadFilesize;
                    const queueSize = Vue.ref(0);
                    const queueSizeLabel = Vue.ref('');
                    const systemProperties = Vue.ref(['format','type']);
                    const taxaDataArr = Vue.ref({});
                    const uploaderRef = Vue.ref(null);

                    function cancelUpload() {
                        csvFileData.value = [];
                        fileArr.value = [];
                        taxaDataArr.value = Object.assign({}, {});
                        updateQueueSize();
                        uploaderRef.value.reset();
                    }

                    function csvToArray(str) {
                        const headers = str.slice(0, str.indexOf("\n")).split(',');
                        if(str.endsWith("\n")){
                            str = str.substring(0, str.length - 2);
                        }
                        const rows = str.slice(str.indexOf("\n") + 1).split("\n");
                        return rows.map((row) => {
                            if(row){
                                const values = row.split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);
                                return headers.reduce((object, header, index) => {
                                    const fieldName = header.trim();
                                    let fieldValue = values[index] ? values[index].replace('\r', '') : '';
                                    if(fieldValue.startsWith('"')){
                                        fieldValue = fieldValue.replaceAll('"','');
                                    }
                                    object[fieldName] = fieldValue;
                                    return object;
                                }, {});
                            }
                        });
                    }

                    function initializeUpload() {
                        fileArr.value.forEach((file) => {
                            if(file.hasOwnProperty('tid') && file.tid && Number(file.tid) > 0){
                                file['metadata'].push({name: 'tid', value: file.tid, system: true});
                                uploaderRef.value.updateFileStatus(file,'idle');
                            }
                        });
                        uploaderRef.value.upload();
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
                        setTaxaData([adjustedFileName],fileName);
                    }

                    function processCsvFile(file) {
                        const fileReader = new FileReader();
                        fileReader.onload = () => {
                            csvFileData.value = csvToArray(fileReader.result);
                            if(csvFileData.value.length > 0){
                                const taxaArr = [];
                                csvFileData.value.forEach((dataObj) => {
                                    if(dataObj.hasOwnProperty('scientificname') && dataObj['scientificname'] !== '' && !taxaArr.includes(dataObj['scientificname'])){
                                        taxaArr.push(dataObj['scientificname']);
                                    }
                                    if(dataObj.hasOwnProperty('filename') && dataObj['filename']){
                                        const file = fileArr.value.find((obj) => obj.name.toLowerCase() === dataObj['filename'].toLowerCase());
                                        if(file){
                                            const keys = Object.keys(dataObj);
                                            keys.forEach((key) => {
                                                if(key !== 'filename' && dataObj[key] !== ''){
                                                    if(key === 'scientificname'){
                                                        file['scientificname'] = dataObj[key];
                                                    }
                                                    else{
                                                        const existingData = file['metadata'].find((obj) => obj.name === key);
                                                        if(existingData){
                                                            existingData['value'] = dataObj[key];
                                                        }
                                                        else{
                                                            file['metadata'].push({name: key, value: dataObj[key], system: systemProperties.value.includes(key)});
                                                        }
                                                    }
                                                }
                                            });
                                            setAdditionalData(file);
                                        }
                                    }
                                });
                                setTaxaData(taxaArr);
                            }
                        };
                        fileReader.readAsText(file);
                    }

                    function processImageFileData(file, csvData) {
                        file['metadata'].push({name: 'type', value: 'StillImage', system: true});
                        file['metadata'].push({name: 'action', value: 'uploadTaxonImage', system: true});
                        file['metadata'].push({name: 'photographer', value: ((csvData && csvData.hasOwnProperty('photographer') && csvData['photographer'] !== '') ? csvData['photographer'] : ''), system: false});
                        file['metadata'].push({name: 'caption', value: ((csvData && csvData.hasOwnProperty('caption') && csvData['caption'] !== '') ? csvData['caption'] : ''), system: false});
                        file['metadata'].push({name: 'owner', value: ((csvData && csvData.hasOwnProperty('owner') && csvData['owner'] !== '') ? csvData['owner'] : ''), system: false});
                        file['metadata'].push({name: 'sourceurl', value: ((csvData && csvData.hasOwnProperty('sourceurl') && csvData['sourceurl'] !== '') ? csvData['sourceurl'] : ''), system: false});
                        file['metadata'].push({name: 'copyright', value: ((csvData && csvData.hasOwnProperty('copyright') && csvData['copyright'] !== '') ? csvData['copyright'] : ''), system: false});
                        file['metadata'].push({name: 'locality', value: ((csvData && csvData.hasOwnProperty('locality') && csvData['locality'] !== '') ? csvData['locality'] : ''), system: false});
                        file['metadata'].push({name: 'notes', value: ((csvData && csvData.hasOwnProperty('notes') && csvData['notes'] !== '') ? csvData['notes'] : ''), system: false});
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
                        const fileIndex = fileArr.value.indexOf(file);
                        fileArr.value.splice(fileIndex,1);
                        uploaderRef.value.removeFile(file);
                        updateQueueSize();
                    }

                    function setAdditionalData(file) {
                        let additionalData = false;
                        file['metadata'].forEach((data) => {
                            if(data.value && data.value !== '' && !data.system){
                                additionalData = true;
                            }
                        });
                        file['additionalData'] = additionalData;
                        uploaderRef.value.updateFileStatus(file,new Date().toTimeString());
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'TaxonProfile');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resData) => {
                                isEditor.value = resData.includes('TaxonProfile');
                            });
                        });
                    }

                    function setTaxaData(nameArr, fileName = null) {
                        const formData = new FormData();
                        formData.append('taxa', JSON.stringify(nameArr));
                        formData.append('action', 'getTaxaIdDataFromNameArr');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                Object.keys(resObj).forEach((key) => {
                                    taxaDataArr.value[key] = Object.assign({}, resObj[key]);
                                });
                                if(fileName && resObj.length === 1){
                                    const file = fileArr.value.find((obj) => obj.name.toLowerCase() === fileName.toLowerCase());
                                    file['scientificname'] = resObj[0]['sciname'];
                                    file['tid'] = resObj[0]['tid'];
                                    uploaderRef.value.updateFileStatus(file,new Date().toTimeString());
                                }
                                updateMediaDataTids();
                            });
                        });
                    }

                    function updateMediaDataTids() {
                        fileArr.value.forEach((file) => {
                            if(!file.hasOwnProperty('tid') || !file.tid || file.tid === ''){
                                const sciname = file.scientificname;
                                if(sciname){
                                    if(taxaDataArr.value.hasOwnProperty(sciname.toLowerCase())){
                                        file.tid = taxaDataArr[sciname.toLowerCase()]['tid'];
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
                        const file = fileArr.value.find((obj) => obj.name.toLowerCase() === taxonObj['filename'].toLowerCase());
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
                        uploaderRef.value.updateFileStatus(file,new Date().toTimeString());
                    }

                    function updateQueueSize() {
                        let size = 0;
                        fileArr.value.forEach((file) => {
                            size += file.size;
                        });
                        const sizeMb = (Math.round((size / 1000000) * 10 ) / 10);
                        queueSize.value = size;
                        queueSizeLabel.value = sizeMb.toString() + 'MB';
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
                        const maxFileSizeBytes = maxUploadFilesize * 1000 * 1000;
                        const videoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
                        const audioTypes = ['audio/mpeg', 'audio/ogg', 'audio/wav'];
                        const fileExtensionTypes = ['jpeg', 'jpg', 'png', 'zc'];
                        const returnArr = [];
                        files.forEach((file) => {
                            const fileType = file.type;
                            const fileName = file.name;
                            const fileExtension = fileName.split('.').pop().toLowerCase();
                            const existingData = fileArr.value.find((obj) => obj.name.toLowerCase() === fileName.toLowerCase());
                            if(fileName.endsWith(".csv")){
                                processCsvFile(file);
                            }
                            else if(!existingData && (file.size <= maxFileSizeBytes && (videoTypes.includes(fileType) || audioTypes.includes(fileType) || fileExtensionTypes.includes(fileExtension)))){
                                let tid = null;
                                let csvData = csvFileData.value.find((obj) => obj.filename.toLowerCase() === file.name.toLowerCase());
                                if(!csvData){
                                    csvData = csvFileData.value.find((obj) => obj.filename.toLowerCase() === file.name.substring(0, file.name.lastIndexOf('.')).toLowerCase());
                                }
                                if(!csvData || !csvData.hasOwnProperty('scientificname')){
                                    parseScinameFromFilename(file.name);
                                }
                                const sciname = (csvData && csvData.hasOwnProperty('scientificname')) ? csvData['scientificname'] : null;
                                if(sciname){
                                    if(taxaDataArr.value.hasOwnProperty(sciname.toLowerCase())){
                                        tid = taxaDataArr[sciname.toLowerCase()]['tid'];
                                    }
                                }
                                file['scientificname'] = sciname;
                                file['tid'] = tid;
                                if(sciname && tid){
                                    file['errorMessage'] = null;
                                }
                                else if(sciname){
                                    file['errorMessage'] = 'Scientific name not found in taxonomic thesaurus';
                                }
                                else{
                                    file['errorMessage'] = 'Scientific name required';
                                }
                                file['metadata'] = [];
                                file['correctedSizeLabel'] =   (Math.round((file.size / 1000000) * 10 ) / 10).toString() + 'MB';
                                if(videoTypes.includes(fileType) || audioTypes.includes(fileType) || fileName.endsWith(".zc")){
                                    processMediaFileData(file, csvData);
                                }
                                else{
                                    processImageFileData(file, csvData);
                                }
                                setAdditionalData(file);
                                fileArr.value.push(file);
                                updateQueueSize();
                                returnArr.push(file);
                            }
                        });
                        return returnArr;
                    }

                    Vue.onMounted(() => {
                        setEditor();
                    });

                    return {
                        csvFileData,
                        isEditor,
                        queueSize,
                        queueSizeLabel,
                        uploaderRef,
                        cancelUpload,
                        initializeUpload,
                        processUploaded,
                        removePickedFile,
                        updateMediaScientificName,
                        uploadFiles,
                        validateFiles
                    }
                }
            });
            taxaBatchMediaUploaderModule.use(Quasar, { config: {} });
            taxaBatchMediaUploaderModule.use(Pinia.createPinia());
            taxaBatchMediaUploaderModule.mount('#innertext');
        </script>
    </body>
</html>
