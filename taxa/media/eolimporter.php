<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$submitAction = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';
$statusStr = array_key_exists('status',$_REQUEST)?$_REQUEST['status']:'';

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
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Encyclopedia of Life Media Importer</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/external/jquery-ui.css?ver=20221204" rel="stylesheet" type="text/css" />
    <style>
        .processor-container {
            width: 95%;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .processor-control-container {
            width: 40%;
        }
        .processor-control-card {
            height: 630px;
        }
        .processor-control-accordion {
            height: 610px;
        }
        .processor-display-container {
            width: 50%;
        }
        .processor-display {
            height: 610px;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .process-button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            clear: both;
            margin-top: 5px;
        }
        .anchor-link {
            font-weight: bold;
            cursor: pointer;
        }
        a.anchor-link:link, a.anchor-link:visited, a.anchor-link:hover, a.anchor-link:active {
            text-decoration: none;
        }
    </style>
    <script src="../../js/external/all.min.js" type="text/javascript"></script>
	<script src="../../js/external/jquery.js" type="text/javascript"></script>
	<script src="../../js/external/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
    <?php
        include(__DIR__ . '/../../header.php');
    ?>
    <div class="navpath">
        <a href="../../index.php">Home</a> &gt;&gt;
        <b>Encyclopedia of Life Media Importer</b>
    </div>
    <div id="innertext">
        <h1>Encyclopedia of Life Media Importer</h1>
        <?php
        if($isEditor){
            ?>
            <div class="processor-container">
                <div class="processor-control-container">
                    <q-card class="processor-control-card">
                        <q-card-section>
                            <div class="q-my-sm">
                                <single-scientific-common-name-auto-complete :sciname="taxonomicGroup" :disable="loading" label="Taxonomic Group" limit-to-thesaurus="true" accepted-taxa-only="true" rank-low="10" rank-high="190" @update:sciname="updateTaxonomicGroup"></single-scientific-common-name-auto-complete>
                            </div>
                            <q-card class="q-my-sm" flat bordered>
                                <q-card-section>
                                    <div class="text-subtitle1 text-weight-bold">Select Media Type</div>
                                    <q-option-group :options="mediaTypeOptions" type="radio" v-model="selectedMediaType" :disable="loading" @update:model-value="processMediaTypeChange" dense />
                                </q-card-section>
                            </q-card>
                            <q-card class="q-my-sm" flat bordered>
                                <q-card-section>
                                    <template v-if="descriptionSelected">
                                        <div class="q-my-sm">
                                            <single-language-auto-complete :language="descriptionLanguage" :disable="loading" label="Description Language" @update:language="updateDescriptionLanguage"></single-language-auto-complete>
                                        </div>
                                        <div class="q-my-sm">
                                            <q-option-group :options="descriptionSaveOptions" type="radio" v-model="selectedDescSaveMethod" :disable="loading" dense />
                                        </div>
                                    </template>
                                    <div class="q-my-sm">
                                        <q-input type="number" outlined v-model="maximumRecordsPerTaxon" style="width:225px;" label="Maximum records per taxon" hint="(Maximum 25)" min="1" max="25" :readonly="loading" @update:model-value="validateMaximumRecordsValue" dense />
                                    </div>
                                    <div class="q-my-sm">
                                        <taxon-rank-checkbox-selector :selected-ranks="selectedRanks" :kingdom-id="kingdomId" :disable="loading" link-label="Select Taxonomic Ranks" inner-label="Select taxonomic ranks for taxa to be included in import" @update:selected-ranks="updateSelectedRanks"></taxon-rank-checkbox-selector>
                                    </div>
                                    <div class="q-my-sm">
                                        <q-checkbox v-model="importMissingOnly" label="Import only for taxa missing selected media type" :disable="loading" />
                                    </div>
                                </q-card-section>
                            </q-card>
                            <div class="process-button-container">
                                <div>
                                    <q-btn :loading="loading" color="secondary" @click="initializeEOLImport();" label="Start Import" dense />
                                </div>
                                <div>
                                    <q-btn v-if="loading" color="red" @click="cancelProcess();" label="Cancel" dense />
                                </div>
                            </div>
                        </q-card-section>
                    </q-card>
                </div>

                <div class="processor-display-container">
                    <q-card class="bg-grey-3 q-pa-sm">
                        <q-scroll-area ref="procDisplayScrollAreaRef" class="bg-grey-1 processor-display" @scroll="setScroller">
                            <q-list dense>
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
                                                            <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="q-ml-sm text-weight-bold text-green-9">
                                                                <span class="q-ml-sm text-weight-bold text-green-9">{{subproc.resultText}}</span>
                                                                <span class="q-ml-sm">
                                                                    <a :href="subproc.taxonPageHref" target="_blank">(Go to Taxon Profile Page)</a>
                                                                </span>
                                                            </div>
                                                            <div v-if="subproc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                                                {{subproc.resultText}}
                                                            </div>
                                                        </template>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </q-item-section>
                                </q-item>
                            </q-list>
                        </q-scroll-area>
                    </q-card>
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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/misc/singleLanguageAutoComplete.js?ver=20230204" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/singleScientificCommonNameAutoComplete.js?ver=20230202" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonRankCheckboxSelector.js" type="text/javascript"></script>
    <script>
        const eolMediaImporterModule = Vue.createApp({
            data() {
                return {
                    clientRoot: CLIENT_ROOT,
                    currentTaxon: Vue.ref(null),
                    descriptionLanguage: Vue.ref(null),
                    descriptionSaveOptions: [
                        { label: 'Save descriptions under a single Encyclopedia of Life tab', value: 'singletab' },
                        { label: 'Save descriptions under a separate tab for each topic', value: 'separatetabs' }
                    ],
                    descriptionSelected: Vue.ref(false),
                    eolIdentifierArr: Vue.ref([]),
                    eolMedia: Vue.ref([]),
                    identifierImportIndex: Vue.ref(1),
                    importMissingOnly: Vue.ref(false),
                    kingdomId: Vue.ref(null),
                    loading: Vue.ref(false),
                    maximumRecordsPerTaxon: Vue.ref(1),
                    mediaCountImportIndex: Vue.ref(1),
                    mediaTypeOptions: [
                        { label: 'Image', value: 'image' },
                        { label: 'Video', value: 'video' },
                        { label: 'Audio', value: 'audio' },
                        { label: 'Text Description', value: 'description' }
                    ],
                    processCancelled: Vue.ref(false),
                    processorDisplayArr: Vue.ref([]),
                    selectedDescSaveMethod: Vue.ref('singletab'),
                    selectedMediaType: Vue.ref('image'),
                    selectedRanks: Vue.ref([]),
                    taxaMediaArr: Vue.ref([]),
                    taxonMediaArr: Vue.ref([]),
                    taxonomicGroup: Vue.ref(null),
                    taxonomicGroupTid: Vue.ref(null),
                    taxonUploadCount: Vue.ref(0)
                }
            },
            components: {
                'single-language-auto-complete': singleLanguageAutoComplete,
                'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                'taxon-rank-checkbox-selector': taxonRankCheckboxSelector
            },
            setup() {
                let procDisplayScrollAreaRef = Vue.ref(null);
                let procDisplayScrollHeight = Vue.ref(0);
                return {
                    procDisplayScrollAreaRef,
                    setScroller(info) {
                        if(info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                            procDisplayScrollHeight.value = info.verticalSize;
                            procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                        }
                    }
                }
            },
            mounted() {
                this.selectedRanks = TAXONOMIC_RANKS;
            },
            methods: {
                addSubprocessToProcessorDisplay(id,type,text){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    parentProcObj['subs'].push(this.getNewSubprocessObject(this.currentTaxon['sciname'],type,text));
                },
                addTaxonDescriptionStatement(statement){
                    const formData = new FormData();
                    formData.append('statement', JSON.stringify(statement));
                    formData.append('action', 'addTaxonDescriptionStatement');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        response.text().then((res) => {
                            if(res && Number(res) > 0){
                                this.taxonUploadCount++;
                            }
                            this.processEOLDescriptionRecords();
                        });
                    });
                },
                addTaxonDescriptionTab(descTab,statement = null){
                    const formData = new FormData();
                    formData.append('description', JSON.stringify(descTab));
                    formData.append('action', 'addTaxonDescriptionTab');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        response.text().then((res) => {
                            if(res && Number(res) > 0 && statement){
                                descTab['tdbid'] = res;
                                descTab['stmts'] = [];
                                if(statement){
                                    statement['tdbid'] = res;
                                    this.addTaxonDescriptionStatement(statement);
                                    descTab['stmts'].push(statement);
                                }
                                this.taxonMediaArr.push(descTab);
                            }
                            else{
                                this.processEOLDescriptionRecords();
                            }
                        });
                    });
                },
                adjustUIEnd(){
                    this.eolIdentifierArr = [];
                    this.taxaMediaArr = [];
                    this.identifierImportIndex = 1;
                    this.mediaCountImportIndex = 1;
                    this.currentTaxon = null;
                    this.loading = false;
                },
                adjustUIStart(){
                    this.processorDisplayArr = [];
                    this.loading = true;
                },
                cancelProcess(){
                    this.processCancelled = true;
                    if(!this.currentTaxon){
                        cancelAPIRequest();
                        const procObj = this.processorDisplayArr.find(proc => proc['current'] === true);
                        if(procObj){
                            let subProcObj;
                            procObj['current'] = false;
                            procObj['loading'] = false;
                            if(procObj.hasOwnProperty('subs')){
                                subProcObj = procObj['subs'].find(subproc => subproc['loading'] === true);
                            }
                            if(subProcObj){
                                subProcObj['loading'] = false;
                                subProcObj['result'] = 'error';
                                subProcObj['resultText'] = 'Cancelled';
                            }
                            else{
                                procObj['result'] = 'error';
                                procObj['resultText'] = 'Cancelled';
                            }
                        }
                        this.adjustUIEnd();
                    }
                },
                getNewProcessObject(id,type,text){
                    const procObj = {
                        id: id,
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
                },
                getNewSubprocessObject(id,type,text){
                    return {
                        id: id,
                        procText: text,
                        type: type,
                        loading: true,
                        result: '',
                        tid: 0,
                        resultText: ''
                    };
                },
                getStoredIdentifiers(){
                    const formData = new FormData();
                    formData.append('tid', this.taxonomicGroupTid);
                    formData.append('source', 'eol');
                    formData.append('index', this.identifierImportIndex);
                    formData.append('action', 'getIdentifiersForTaxonomicGroup');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                if(resObj.length > 0){
                                    this.eolIdentifierArr = this.eolIdentifierArr.concat(resObj);
                                }
                                if(resObj.length < 50000){
                                    this.processSuccessResponse(true,'Complete');
                                    const text = 'Getting taxa and ' + this.selectedMediaType + ' counts for taxa within ' + this.taxonomicGroup.name;
                                    this.processorDisplayArr.push(this.getNewProcessObject('setTaxaMediaArr','single',text));
                                    this.getTaxaMediaCounts();
                                }
                                else{
                                    this.identifierImportIndex++;
                                    this.getStoredIdentifiers();
                                }
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            this.processErrorResponse(text);
                        }
                    });
                },
                getTaxaMediaCounts(){
                    abortController = new AbortController();
                    const formData = new FormData();
                    formData.append('tid', this.taxonomicGroupTid);
                    formData.append('index', this.mediaCountImportIndex);
                    if(this.selectedMediaType === 'image'){
                        formData.append('action', 'getImageCountsForTaxonomicGroup');
                    }
                    else if(this.selectedMediaType === 'video'){
                        formData.append('action', 'getVideoCountsForTaxonomicGroup');
                    }
                    else if(this.selectedMediaType === 'audio'){
                        formData.append('action', 'getAudioCountsForTaxonomicGroup');
                    }
                    else if(this.selectedMediaType === 'description'){
                        formData.append('action', 'getDescriptionCountsForTaxonomicGroup');
                    }
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        signal: abortController.signal,
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                if(resObj.length > 0){
                                    this.taxaMediaArr = this.taxaMediaArr.concat(this.processTaxaMediaArr(resObj));
                                }
                                if(resObj.length < 50000){
                                    this.processSuccessResponse(true,'Complete');
                                    this.setCurrentTaxon();
                                }
                                else{
                                    this.mediaCountImportIndex++;
                                    this.getTaxaMediaCounts();
                                }
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            this.processErrorResponse(text);
                        }
                    })
                    .catch((err) => {});
                },
                initializeEOLImport(){
                    if(this.taxonomicGroupTid){
                        this.processCancelled = false;
                        this.adjustUIStart();
                        const text = 'Getting stored Encyclopedia of Life identifiers for taxa within ' + this.taxonomicGroup.name;
                        this.processorDisplayArr.push(this.getNewProcessObject('setIdentifierArr','single',text));
                        this.getStoredIdentifiers();
                    }
                    else{
                        alert('Please enter a Taxonomic Group to start an import');
                    }
                },
                processEOLDescriptionRecords(){
                    if(!this.processCancelled && this.eolMedia.length > 0 && this.taxonUploadCount < this.maximumRecordsPerTaxon){
                        const mediaRecord = this.eolMedia[0];
                        this.eolMedia.splice(0, 1);
                        if(mediaRecord['language'] === this.descriptionLanguage['iso']){
                            if(this.selectedDescSaveMethod === 'singletab'){
                                const existingEOLTab = this.taxonMediaArr.length > 0 ? this.taxonMediaArr.find(obj => obj['caption'] === 'Encyclopedia of Life') : null;
                                const newTaxonStatement = {};
                                newTaxonStatement['heading'] = mediaRecord['title'];
                                newTaxonStatement['statement'] = mediaRecord['description'];
                                if(existingEOLTab){
                                    const existingEOLStatement = existingEOLTab['stmts'].length > 0 ? existingEOLTab['stmts'].find(obj => obj['heading'] === mediaRecord['title']) : null;
                                    if(existingEOLStatement){
                                        this.processEOLDescriptionRecords();
                                    }
                                    else{
                                        newTaxonStatement['tdbid'] = existingEOLTab['tdbid'];
                                        newTaxonStatement['sortsequence'] = (existingEOLTab['stmts'].length + 1);
                                        this.addTaxonDescriptionStatement(newTaxonStatement);
                                        existingEOLTab['stmts'].push(newTaxonStatement);
                                    }
                                }
                                else{
                                    const newTaxonDescTab = {};
                                    newTaxonDescTab['tid'] = this.currentTaxon['tid'];
                                    newTaxonDescTab['caption'] = 'Encyclopedia of Life';
                                    newTaxonDescTab['language'] = this.descriptionLanguage['name'];
                                    newTaxonDescTab['langid'] = this.descriptionLanguage['id'];
                                    newTaxonDescTab['displaylevel'] = (this.taxonMediaArr.length + 1);
                                    newTaxonStatement['sortsequence'] = 1;
                                    this.addTaxonDescriptionTab(newTaxonDescTab,newTaxonStatement);
                                }
                            }
                            else{
                                const existingTab = this.taxonMediaArr.length > 0 ? this.taxonMediaArr.find(obj => obj['caption'] === mediaRecord['title']) : null;
                                if(existingTab){
                                    this.processEOLDescriptionRecords();
                                }
                                else{
                                    const newTaxonDescTab = {};
                                    newTaxonDescTab['tid'] = this.currentTaxon['tid'];
                                    newTaxonDescTab['caption'] = mediaRecord['title'];
                                    newTaxonDescTab['source'] = mediaRecord['source'];
                                    newTaxonDescTab['sourceurl'] = mediaRecord['source'];
                                    newTaxonDescTab['language'] = this.descriptionLanguage['name'];
                                    newTaxonDescTab['langid'] = this.descriptionLanguage['id'];
                                    newTaxonDescTab['displaylevel'] = (this.taxonMediaArr.length + 1);
                                    const newTaxonStatement = {};
                                    newTaxonStatement['statement'] = mediaRecord['description'];
                                    newTaxonStatement['sortsequence'] = 1;
                                    newTaxonStatement['displayheader'] = 0;
                                    this.addTaxonDescriptionTab(newTaxonDescTab,newTaxonStatement);
                                }
                            }
                        }
                        else{
                            this.processEOLDescriptionRecords();
                        }
                    }
                    else{
                        this.processSubprocessSuccessResponse(true,(this.taxonUploadCount + ' records uploaded'));
                        this.setCurrentTaxon();
                    }
                },
                processEOLImageRecords(){
                    if(!this.processCancelled && this.eolMedia.length > 0 && this.taxonUploadCount < this.maximumRecordsPerTaxon){
                        const mediaRecord = this.eolMedia[0];
                        this.eolMedia.splice(0, 1);
                        const existingRecord = this.taxonMediaArr.length > 0 ? this.taxonMediaArr.find(obj => obj['url'] === mediaRecord['eolMediaURL']) : null;
                        if(existingRecord){
                            this.processEOLImageRecords();
                        }
                        else{
                            const newImageObj = {};
                            newImageObj['tid'] = this.currentTaxon['tid'];
                            newImageObj['url'] = mediaRecord['eolMediaURL'];
                            newImageObj['thumbnailurl'] = mediaRecord['eolMediaURL'];
                            newImageObj['photographer'] = mediaRecord.hasOwnProperty('rightsHolder') ? mediaRecord['rightsHolder'] : null;
                            newImageObj['format'] = mediaRecord.hasOwnProperty('mimeType') ? mediaRecord['mimeType'] : null;
                            newImageObj['caption'] = mediaRecord.hasOwnProperty('title') ? mediaRecord['title'] : null;
                            newImageObj['owner'] = 'Encyclopedia of Life';
                            newImageObj['sourceurl'] = mediaRecord.hasOwnProperty('source') ? mediaRecord['source'] : null;
                            newImageObj['accessrights'] = mediaRecord.hasOwnProperty('license') ? mediaRecord['license'] : null;
                            newImageObj['notes'] = mediaRecord.hasOwnProperty('description') ? mediaRecord['description'] : null;
                            newImageObj['sourceidentifier'] = mediaRecord['identifier'];
                            const formData = new FormData();
                            formData.append('image', JSON.stringify(newImageObj));
                            formData.append('action', 'addImageRecord');
                            fetch(imageApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(res && Number(res) > 0){
                                        this.taxonMediaArr.push(newImageObj);
                                        this.taxonUploadCount++;
                                    }
                                    this.processEOLImageRecords();
                                });
                            });
                        }
                    }
                    else{
                        this.processSubprocessSuccessResponse(true,(this.taxonUploadCount + ' records uploaded'));
                        this.setCurrentTaxon();
                    }
                },
                processEOLMediaRecords(){
                    if(!this.processCancelled && this.eolMedia.length > 0 && this.taxonUploadCount < this.maximumRecordsPerTaxon){
                        const mediaRecord = this.eolMedia[0];
                        this.eolMedia.splice(0, 1);
                        const existingRecord = this.taxonMediaArr.length > 0 ? this.taxonMediaArr.find(obj => obj['accessuri'] === mediaRecord['mediaURL']) : null;
                        if(existingRecord){
                            this.processEOLMediaRecords();
                        }
                        else{
                            const newMediaObj = {};
                            newMediaObj['tid'] = this.currentTaxon['tid'];
                            newMediaObj['accessuri'] = mediaRecord['mediaURL'];
                            newMediaObj['creator'] = mediaRecord.hasOwnProperty('rightsHolder') ? mediaRecord['rightsHolder'] : null;
                            newMediaObj['type'] = mediaRecord.hasOwnProperty('mediumType') ? mediaRecord['mediumType'] : null;
                            newMediaObj['format'] = mediaRecord.hasOwnProperty('mimeType') ? mediaRecord['mimeType'] : null;
                            newMediaObj['owner'] = 'Encyclopedia of Life';
                            newMediaObj['language'] = mediaRecord.hasOwnProperty('language') ? mediaRecord['language'] : null;
                            newMediaObj['usageterms'] = mediaRecord.hasOwnProperty('license') ? mediaRecord['license'] : null;
                            newMediaObj['description'] = mediaRecord.hasOwnProperty('description') ? mediaRecord['description'] : null;
                            const formData = new FormData();
                            formData.append('media', JSON.stringify(newMediaObj));
                            formData.append('action', 'addMediaRecord');
                            fetch(mediaApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(res && Number(res) > 0){
                                        this.taxonMediaArr.push(newMediaObj);
                                        this.taxonUploadCount++;
                                    }
                                    this.processEOLMediaRecords();
                                });
                            });
                        }
                    }
                    else{
                        this.processSubprocessSuccessResponse(true,(this.taxonUploadCount + ' records uploaded'));
                        this.setCurrentTaxon();
                    }
                },
                processErrorResponse(text){
                    const procObj = this.processorDisplayArr.find(proc => proc['current'] === true);
                    if(procObj){
                        procObj['current'] = false;
                        if(procObj['loading'] === true){
                            procObj['loading'] = false;
                            procObj['result'] = 'error';
                            procObj['resultText'] = text;
                        }
                    }
                },
                processMediaTypeChange(mediatype) {
                    this.descriptionSelected = (mediatype === 'description');
                },
                processSubprocessErrorResponse(id,text){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    if(parentProcObj){
                        parentProcObj['current'] = false;
                        const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                        if(subProcObj){
                            subProcObj['loading'] = false;
                            subProcObj['result'] = 'error';
                            subProcObj['resultText'] = text;
                        }
                    }
                },
                processSubprocessSuccessResponse(complete,text = null){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === this.currentTaxon['sciname']);
                    if(parentProcObj){
                        parentProcObj['current'] = !complete;
                        const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                        if(subProcObj){
                            subProcObj['loading'] = false;
                            subProcObj['result'] = 'success';
                            subProcObj['resultText'] = text;
                            subProcObj['taxonPageHref'] = this.clientRoot + '/taxa/index.php?taxon=' + this.currentTaxon['tid'];
                        }
                    }
                },
                processSuccessResponse(complete,text = null){
                    const procObj = this.processorDisplayArr.find(proc => proc['current'] === true);
                    if(procObj){
                        procObj['current'] = !complete;
                        if(procObj['loading'] === true){
                            procObj['loading'] = false;
                            procObj['result'] = 'success';
                            procObj['resultText'] = text;
                        }
                    }
                },
                processTaxaMediaArr(inArr){
                    const newMediaArr = [];
                    if(Array.isArray(inArr) && inArr.length > 0){
                        inArr.forEach((taxon) => {
                            if(this.selectedRanks.includes(Number(taxon['rankid'])) && (!this.importMissingOnly || Number(taxon['cnt']) === 0)){
                                const idObj = this.eolIdentifierArr.find(obj => Number(obj['tid']) === Number(taxon['tid']));
                                taxon['eolid'] = idObj ? Number(idObj['identifier']) : null;
                                newMediaArr.push(taxon);
                            }
                        });
                    }
                    return newMediaArr;
                },
                setCurrentTaxon(){
                    if(!this.processCancelled && this.taxaMediaArr.length > 0){
                        this.taxonMediaArr = [];
                        this.eolMedia = [];
                        this.taxonUploadCount = 0;
                        this.currentTaxon = this.taxaMediaArr[0];
                        this.taxaMediaArr.splice(0, 1);
                        const text = 'Searching for ' + this.currentTaxon['sciname'];
                        this.processorDisplayArr.push(this.getNewProcessObject(this.currentTaxon['sciname'],'multi',text));
                        if(!this.currentTaxon['eolid']){
                            const url = 'https://eol.org/api/search/1.0.json?q=' + this.currentTaxon['sciname'];
                            const formData = new FormData();
                            formData.append('url', url);
                            formData.append('action', 'get');
                            fetch(proxyApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.json().then((resObj) => {
                                        if(Number(resObj['totalResults']) > 0){
                                            const resArr = resObj['results'];
                                            const taxonResObj = resArr.find(obj => obj['title'] === this.currentTaxon['sciname']);
                                            if(taxonResObj){
                                                this.currentTaxon['eolid'] = taxonResObj['id'];
                                                const formData = new FormData();
                                                formData.append('tid', this.currentTaxon['tid']);
                                                formData.append('idname', 'eol');
                                                formData.append('id', taxonResObj['id']);
                                                formData.append('action', 'addTaxonIdentifier');
                                                fetch(taxonomyApiUrl, {
                                                    method: 'POST',
                                                    body: formData
                                                })
                                                .then((response) => {
                                                    this.processSuccessResponse(false);
                                                    this.setTaxonMediaArr();
                                                });
                                            }
                                            else{
                                                this.processErrorResponse('Not found');
                                                this.setCurrentTaxon();
                                            }
                                        }
                                        else{
                                            this.processErrorResponse('Not found');
                                            this.setCurrentTaxon();
                                        }
                                    });
                                }
                                else{
                                    this.processErrorResponse('Unable to retrieve EOL taxon record');
                                    this.setCurrentTaxon();
                                }
                            });
                        }
                        else{
                            this.processSuccessResponse(false);
                            this.setTaxonMediaArr();
                        }
                    }
                    else{
                        this.adjustUIEnd();
                    }
                },
                setEOLMediaArr(){
                    let url;
                    const text = 'Getting ' + this.selectedMediaType + ' records';
                    this.addSubprocessToProcessorDisplay(this.currentTaxon['sciname'],'text',text);
                    if(this.selectedMediaType === 'image'){
                        url = 'https://eol.org/api/pages/1.0/' + this.currentTaxon['eolid'] + '.json?images_per_page=75';
                    }
                    else if(this.selectedMediaType === 'video'){
                        url = 'https://eol.org/api/pages/1.0/' + this.currentTaxon['eolid'] + '.json?videos_per_page=75';
                    }
                    else if(this.selectedMediaType === 'audio'){
                        url = 'https://eol.org/api/pages/1.0/' + this.currentTaxon['eolid'] + '.json?sounds_per_page=75';
                    }
                    else if(this.selectedMediaType === 'description'){
                        url = 'https://eol.org/api/pages/1.0/' + this.currentTaxon['eolid'] + '.json?texts_per_page=75';
                    }
                    const formData = new FormData();
                    formData.append('url', url);
                    formData.append('action', 'get');
                    fetch(proxyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                if(resObj.hasOwnProperty('taxonConcept') && resObj['taxonConcept'].hasOwnProperty('dataObjects')){
                                    this.eolMedia = resObj['taxonConcept']['dataObjects'];
                                    this.processSubprocessSuccessResponse(false);
                                    const text = 'Processing ' + this.selectedMediaType + ' records';
                                    this.addSubprocessToProcessorDisplay(this.currentTaxon['sciname'],'text',text);
                                    if(this.selectedMediaType === 'image'){
                                        this.processEOLImageRecords();
                                    }
                                    else if(this.selectedMediaType === 'video' || this.selectedMediaType === 'audio'){
                                        this.processEOLMediaRecords();
                                    }
                                    else if(this.selectedMediaType === 'description'){
                                        this.processEOLDescriptionRecords();
                                    }
                                }
                                else{
                                    const text = 'No ' + this.selectedMediaType + ' records found for ' + this.currentTaxon['sciname'];
                                    this.processSubprocessErrorResponse(this.currentTaxon['sciname'],text);
                                    this.setCurrentTaxon();
                                }
                            });
                        }
                        else{
                            this.processSubprocessErrorResponse(this.currentTaxon['sciname'],'Error getting records');
                            this.setCurrentTaxon();
                        }
                    });
                },
                setTaxonMediaArr(){
                    if(Number(this.currentTaxon['cnt']) > 0){
                        const text = 'Getting existing ' + this.selectedMediaType + 's';
                        this.addSubprocessToProcessorDisplay(this.currentTaxon['sciname'],'text',text);
                        const formData = new FormData();
                        formData.append('tid', this.currentTaxon['tid']);
                        if(this.selectedMediaType === 'image'){
                            formData.append('action', 'getTaxonImages');
                        }
                        else if(this.selectedMediaType === 'video'){
                            formData.append('action', 'getTaxonVideos');
                        }
                        else if(this.selectedMediaType === 'audio'){
                            formData.append('action', 'getTaxonAudios');
                        }
                        else if(this.selectedMediaType === 'description'){
                            formData.append('action', 'getTaxonDescriptions');
                        }
                        fetch(taxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((resObj) => {
                                    this.taxonMediaArr = resObj;
                                    this.processSubprocessSuccessResponse(false);
                                    this.setEOLMediaArr();
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                this.processSubprocessErrorResponse(this.currentTaxon['sciname'],'Error getting records');
                                this.setCurrentTaxon();
                            }
                        });
                    }
                    else{
                        this.setEOLMediaArr();
                    }
                },
                updateDescriptionLanguage(langObj) {
                    this.descriptionLanguage = langObj;
                },
                updateSelectedRanks(selectedArr) {
                    this.selectedRanks = selectedArr;
                },
                updateTaxonomicGroup(taxonObj) {
                    this.taxonomicGroup = taxonObj;
                    this.taxonomicGroupTid = taxonObj ? taxonObj.tid : null;
                    this.kingdomId = taxonObj ? taxonObj.kingdomid : null;
                },
                validateMaximumRecordsValue() {
                    if(this.maximumRecordsPerTaxon > 25){
                        this.maximumRecordsPerTaxon = 25;
                    }
                    if(this.maximumRecordsPerTaxon < 1){
                        this.maximumRecordsPerTaxon = 1;
                    }
                },
                cancelAPIRequest,
                getErrorResponseText
            }
        });
        eolMediaImporterModule.use(Quasar, { config: {} });
        eolMediaImporterModule.mount('#innertext');
    </script>
</body>
</html>
