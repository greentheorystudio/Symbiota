<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('TaxonProfile',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Media Batch Uploader</title>
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
        .list-item-delete {
            display: flex;
            justify-content: flex-end;
        }
        .uploader {
            width: 100%;
            min-height: 150px;
        }
        .uploader i {
            display: none;
        }
    </style>
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
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
        <?php
        if($isEditor){
            ?>
            <div>
                <div class="instruction-container">
                    To batch upload taxa images, either click the Add files button to select the files to be uploaded or drag and
                    drop the files onto the box below. A csv spreadheet can also be uploaded to provide further metadata for the files.
                    <a href="../../templates/batchTaxaImageData.csv"><b>Use this template for the csv spreadsheet.</b></a> For each
                    row in the spreadsheet, the value in the filename column must match the filename of the associated file being uploaded.
                </div>
                <div class="button-csv-container">
                    <div class="button-container">
                        <q-btn color="positive" class="text-bold" label="Add Files" icon="fas fa-plus" @click="uploaderRef.pickFiles();" glossy></q-btn>
                        <q-btn color="primary" class="text-bold" label="Start Upload" icon="fas fa-upload" @click="uploaderRef.upload();" glossy></q-btn>
                        <q-btn color="warning" class="text-bold" label="Cancel Upload" icon="fas fa-ban" @click="cancelUpload();" glossy></q-btn>
                    </div>
                    <div v-if="csvFileData.length > 0" class="text-bold text-red">
                        CSV Data Uploaded
                    </div>
                </div>
                <div class="q-mt-md">
                    <q-uploader ref="uploaderRef" class="uploader q-mx-auto" color="grey-8" :factory="uploadFiles" :filter="validateFiles" label="To add files click the Add Files button above or drag and drop files into this box" multiple>
                        <template v-slot:list="scope">
                            <q-list separator>
                                <q-item v-for="file in scope.files" :key="file.__key" class="list-item-container">
                                    <q-item-section>
                                        <q-item-label class="full-width ellipsis">
                                            {{ file.name }}
                                        </q-item-label>
                                        <q-item-label class="full-width ellipsis">
                                            <media-scientific-name-auto-complete :sciname="{tid: mediaData[file.name]['tid'], label: mediaData[file.name]['scientificname'], name: mediaData[file.name]['scientificname']}" label="Scientific Name" :filename="file.name" limit-to-thesaurus="true" accepted-taxa-only="true" @update:mediataxon="updateMediaScientificName"></media-scientific-name-auto-complete>
                                        </q-item-label>
                                        <q-item-label caption>
                                            {{ mediaData[file.name]['errorMessage'] }}
                                        </q-item-label>

                                        <q-item-label caption>
                                            {{ file.__sizeLabel }} / {{ file.__progressLabel }}
                                        </q-item-label>
                                    </q-item-section>
                                    <q-item-section v-if="file.__img" style="max-height: 100px; max-width: 100px">
                                        <q-img :src="file.__img.src" spinner-color="white"></q-img>
                                    </q-item-section>
                                    <q-item-section>
                                        <div class="list-item-delete">
                                            <q-btn color="negative" class="text-bold" label="Remove" icon="fas fa-times" @click="scope.removeFile(file)" glossy dense></q-btn>
                                        </div>
                                    </q-item-section>
                                </q-item>
                            </q-list>
                        </template>
                    </q-uploader>
                </div>
            </div>
            <?php
        }
        else{
            echo '<div style="font-weight:bold;">You do not have permissions to access this tool</div>';
        }
        ?>
    </div>
    <?php
    include(__DIR__ . '/../../footer.php');
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/singleScientificCommonNameAutoComplete.js" type="text/javascript"></script>
    <script>
        const mediaScientificNameAutoComplete = {
            props: {
                sciname: {
                    type: Object
                },
                label: {
                    type: String,
                    default: 'Scientific Name'
                },
                filename: {
                    type: String
                },
                limitToThesaurus: {
                    type: Boolean,
                    default: false
                },
                acceptedTaxaOnly: {
                    type: Boolean,
                    default: false
                }
            },
            template: `
                <single-scientific-common-name-auto-complete :sciname="sciname" :label="label" :limit-to-thesaurus="limitToThesaurus" :accepted-taxa-only="acceptedTaxaOnly" @update:sciname="updateMediaTaxon"></single-scientific-common-name-auto-complete>
            `,
            components: {
                'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
            },
            methods: {
                updateMediaTaxon(taxonObj) {
                    const resObj = {};
                    resObj['filename'] = this.filename;
                    resObj['sciname'] = taxonObj ? taxonObj.name : null;
                    resObj['tid'] = taxonObj ? taxonObj.tid : null;
                    this.$emit('update:mediataxon', resObj);
                }
            }
        };

        const taxaBatchMediaUploaderModule = Vue.createApp({
            data() {
                return {
                    csvFileData: Vue.ref([]),
                    mediaData: Vue.ref({}),
                    taxaDataArr: Vue.ref([])
                }
            },
            components: {
                'media-scientific-name-auto-complete': mediaScientificNameAutoComplete
            },
            setup() {
                let uploaderRef = Vue.ref(null);
                return {
                    uploaderRef
                }
            },
            methods: {
                cancelUpload(){
                    this.csvFileData = [];
                    this.mediaData = {};
                    this.taxaDataArr = [];
                    this.uploaderRef.reset();
                },
                csvToArray(str){
                    const headers = str.slice(0, str.indexOf("\n")).split(',');
                    if(str.endsWith("\n")){
                        str = str.substring(0, str.length - 2);
                    }
                    const rows = str.slice(str.indexOf("\n") + 1).split("\n");
                    return rows.map(function (row){
                        if(row){
                            const values = row.split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);
                            return headers.reduce(function (object, header, index) {
                                const fieldName = header.trim();
                                let fieldValue = values[index].replace('\r', '');
                                if(fieldValue.startsWith('"')){
                                    fieldValue = fieldValue.replaceAll('"','');
                                }
                                object[fieldName] = fieldValue;
                                return object;
                            }, {});
                        }
                    });
                },
                parseScinameFromFilename(fileName){
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
                    this.setTaxaData([adjustedFileName],fileName);
                },
                processCsvFile(file){
                    const fileReader = new FileReader();
                    fileReader.onload = () => {
                        this.csvFileData = this.csvToArray(fileReader.result);
                        if(this.csvFileData.length > 0){
                            const taxaArr = [];
                            this.csvFileData.forEach((dataObj) => {
                                if(dataObj.hasOwnProperty('scientificname') && dataObj['scientificname'] !== '' && !taxaArr.includes(dataObj['scientificname'])){
                                    taxaArr.push(dataObj['scientificname']);
                                }
                            });
                            this.setTaxaData(taxaArr);
                        }
                    };
                    fileReader.readAsText(file);
                },
                processImageFileData(file, csvData){
                    this.mediaData[file.name]['type'] = 'StillImage';
                    this.mediaData[file.name]['photographer'] = (csvData && csvData.hasOwnProperty('photographer') && csvData['photographer'] !== '') ? csvData['photographer'] : null;
                    this.mediaData[file.name]['caption'] = (csvData && csvData.hasOwnProperty('caption') && csvData['caption'] !== '') ? csvData['caption'] : null;
                    this.mediaData[file.name]['owner'] = (csvData && csvData.hasOwnProperty('owner') && csvData['owner'] !== '') ? csvData['owner'] : null;
                    this.mediaData[file.name]['sourceurl'] = (csvData && csvData.hasOwnProperty('sourceurl') && csvData['sourceurl'] !== '') ? csvData['sourceurl'] : null;
                    this.mediaData[file.name]['copyright'] = (csvData && csvData.hasOwnProperty('copyright') && csvData['copyright'] !== '') ? csvData['copyright'] : null;
                    this.mediaData[file.name]['locality'] = (csvData && csvData.hasOwnProperty('locality') && csvData['locality'] !== '') ? csvData['locality'] : null;
                    this.mediaData[file.name]['notes'] = (csvData && csvData.hasOwnProperty('notes') && csvData['notes'] !== '') ? csvData['notes'] : null;
                },
                processMediaFileData(file, csvData){
                    if(file.name.endsWith(".mp4")){
                        this.mediaData[file.name]['type'] = 'MovingImage';
                        this.mediaData[file.name]['format'] = 'video/mp4';
                    }
                    else if(file.name.endsWith(".webm")){
                        this.mediaData[file.name]['type'] = 'MovingImage';
                        this.mediaData[file.name]['format'] = 'video/webm';
                    }
                    else if(file.name.endsWith(".ogg")){
                        this.mediaData[file.name]['type'] = 'MovingImage';
                        this.mediaData[file.name]['format'] = 'video/ogg';
                    }
                    else if(file.name.endsWith(".mp3")){
                        this.mediaData[file.name]['type'] = 'Sound';
                        this.mediaData[file.name]['format'] = 'audio/mpeg';
                    }
                    else if(file.name.endsWith(".wav")){
                        this.mediaData[file.name]['type'] = 'Sound';
                        this.mediaData[file.name]['format'] = 'audio/wav';
                    }
                    else if(file.name.endsWith(".zc")){
                        this.mediaData[file.name]['type'] = 'Sound';
                        this.mediaData[file.name]['format'] = null;
                    }
                    else{
                        this.mediaData[file.name]['type'] = null;
                        this.mediaData[file.name]['format'] = null;
                    }
                    this.mediaData[file.name]['title'] = (csvData && csvData.hasOwnProperty('title') && csvData['title'] !== '') ? csvData['title'] : null;
                    this.mediaData[file.name]['creator'] = (csvData && csvData.hasOwnProperty('creator') && csvData['creator'] !== '') ? csvData['creator'] : null;
                    this.mediaData[file.name]['description'] = (csvData && csvData.hasOwnProperty('description') && csvData['description'] !== '') ? csvData['description'] : null;
                    this.mediaData[file.name]['locationcreated'] = (csvData && csvData.hasOwnProperty('locationcreated') && csvData['locationcreated'] !== '') ? csvData['locationcreated'] : null;
                    this.mediaData[file.name]['language'] = (csvData && csvData.hasOwnProperty('language') && csvData['language'] !== '') ? csvData['language'] : null;
                    this.mediaData[file.name]['usageterms'] = (csvData && csvData.hasOwnProperty('usageterms') && csvData['usageterms'] !== '') ? csvData['usageterms'] : null;
                    this.mediaData[file.name]['rights'] = (csvData && csvData.hasOwnProperty('rights') && csvData['rights'] !== '') ? csvData['rights'] : null;
                    this.mediaData[file.name]['owner'] = (csvData && csvData.hasOwnProperty('owner') && csvData['owner'] !== '') ? csvData['owner'] : null;
                    this.mediaData[file.name]['publisher'] = (csvData && csvData.hasOwnProperty('publisher') && csvData['publisher'] !== '') ? csvData['publisher'] : null;
                    this.mediaData[file.name]['contributor'] = (csvData && csvData.hasOwnProperty('contributor') && csvData['contributor'] !== '') ? csvData['contributor'] : null;
                    this.mediaData[file.name]['bibliographiccitation'] = (csvData && csvData.hasOwnProperty('bibliographiccitation') && csvData['bibliographiccitation'] !== '') ? csvData['bibliographiccitation'] : null;
                    this.mediaData[file.name]['furtherinformationurl'] = (csvData && csvData.hasOwnProperty('furtherinformationurl') && csvData['furtherinformationurl'] !== '') ? csvData['furtherinformationurl'] : null;
                },
                setTaxaData(nameArr,fileName = null){
                    const formData = new FormData();
                    formData.append('taxa', JSON.stringify(nameArr));
                    formData.append('action', 'getTaxaArrFromNameArr');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        response.json().then((resObj) => {
                            this.taxaDataArr = this.taxaDataArr.concat(resObj);
                            if(fileName && resObj.length === 1){
                                this.mediaData[fileName]['scientificname'] = resObj[0]['sciname'];
                                this.mediaData[fileName]['tid'] = resObj[0]['tid'];
                            }
                            this.updateMediaDataTids();
                        });
                    });
                },
                updateMediaDataTids(){
                    for(let i in this.mediaData){
                        if(this.mediaData.hasOwnProperty(i)){
                            if(!this.mediaData[i].hasOwnProperty('tid') || this.mediaData[i].tid === ''){
                                const sciname = this.mediaData[i].scientificname;
                                if(sciname){
                                    const taxonData = this.taxaDataArr.find((obj) => obj.sciname.toLowerCase() === sciname.toLowerCase());
                                    if(taxonData){
                                        this.mediaData[i].tid = taxonData['tid'];
                                        this.mediaData[i].errorMessage = '';
                                    }
                                    else{
                                        this.mediaData[i].errorMessage = 'Scientific name not found in taxonomic thesaurus';
                                    }
                                }
                            }
                        }
                    }
                },
                updateMediaScientificName(taxonObj) {
                    this.mediaData[taxonObj['filename']]['scientificname'] = taxonObj['sciname'];
                    this.mediaData[taxonObj['filename']]['tid'] = taxonObj['tid'];
                    if(taxonObj['sciname'] && taxonObj['tid']){
                        this.mediaData[taxonObj['filename']]['errorMessage'] = null;
                    }
                    else if(taxonObj['sciname']){
                        this.mediaData[taxonObj['filename']]['errorMessage'] = 'Scientific name not found in taxonomic thesaurus';
                    }
                    else{
                        this.mediaData[taxonObj['filename']]['errorMessage'] = 'Scientific name required';
                    }
                },
                uploadFiles(file){
                    console.log('upload');
                    console.log(file);
                    this.uploaderRef.updateFileStatus(file,'failed');
                    return false;
                },
                validateFiles(files){
                    console.log(files);
                    const maxFileSizeBytes = MAX_UPLOAD_FILESIZE * 1000 * 1000;
                    const videoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
                    const audioTypes = ['audio/mpeg', 'audio/ogg', 'audio/wav'];
                    const fileExtensionTypes = ['jpeg', 'jpg', 'png', 'zc'];
                    const returnArr = [];
                    files.forEach((file) => {
                        const fileType = file.type;
                        const fileName = file.name;
                        const fileExtension = fileName.split('.').pop().toLowerCase();
                        if(fileName.endsWith(".csv")){
                            this.processCsvFile(file);
                        }
                        else if(!this.mediaData.hasOwnProperty(fileName) && (file.size <= maxFileSizeBytes && (videoTypes.includes(fileType) || audioTypes.includes(fileType) || fileExtensionTypes.includes(fileExtension)))){
                            let tid = null;
                            let csvData = this.csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.toLowerCase());
                            if(!csvData){
                                csvData = this.csvFileData.find((obj) => obj.filename.toLowerCase() === file.name.substring(0, file.name.lastIndexOf('.')).toLowerCase());
                            }
                            if(!csvData || !csvData.hasOwnProperty('scientificname')){
                                this.parseScinameFromFilename(file.name);
                            }
                            const sciname = (csvData && csvData.hasOwnProperty('scientificname')) ? csvData['scientificname'] : null;
                            if(sciname){
                                const taxonData = this.taxaDataArr.find((obj) => obj.sciname.toLowerCase() === sciname.toLowerCase());
                                if(taxonData){
                                    tid = taxonData['tid'];
                                }
                            }
                            this.mediaData[fileName] = {};
                            this.mediaData[fileName]['scientificname'] = sciname;
                            this.mediaData[fileName]['tid'] = tid;
                            if(sciname && tid){
                                this.mediaData[fileName]['errorMessage'] = null;
                            }
                            else if(sciname){
                                this.mediaData[fileName]['errorMessage'] = 'Scientific name not found in taxonomic thesaurus';
                            }
                            else{
                                this.mediaData[fileName]['errorMessage'] = 'Scientific name required';
                            }
                            if(videoTypes.includes(fileType) || audioTypes.includes(fileType) || fileName.endsWith(".zc")){
                                this.processMediaFileData(file, csvData);
                            }
                            else{
                                this.processImageFileData(file, csvData);
                            }
                            returnArr.push(file);
                        }
                    });
                    return returnArr;
                }
            }
        });
        taxaBatchMediaUploaderModule.use(Quasar, { config: {} });
        taxaBatchMediaUploaderModule.mount('#innertext');
    </script>
</body>
</html>
