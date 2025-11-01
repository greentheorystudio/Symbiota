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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Encyclopedia of Life Media Importer</title>
        <meta name="description" content="Encyclopedia of Life media importer for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Encyclopedia of Life Media Importer</span>
            </div>
            <div class="q-pa-md">
                <h1>Encyclopedia of Life Media Importer</h1>
                <template v-if="isEditor">
                    <div class="processor-container">
                        <div class="processor-control-container">
                            <q-card class="processor-control-card">
                                <q-card-section>
                                    <div class="q-my-sm">
                                        <single-scientific-common-name-auto-complete :sciname="taxonomicGroup" :disabled="loading" label="Taxonomic Group" limit-to-options="true" accepted-taxa-only="true" rank-low="10" rank-high="190" @update:sciname="updateTaxonomicGroup"></single-scientific-common-name-auto-complete>
                                    </div>
                                    <q-card class="q-my-sm" flat bordered>
                                        <q-card-section>
                                            <div class="text-subtitle1 text-weight-bold">Select Media Type</div>
                                            <q-option-group :options="mediaTypeOptions" type="radio" v-model="selectedMediaType" :disable="loading" @update:model-value="processMediaTypeChange" dense aria-label="Media type options" tabindex="0" />
                                        </q-card-section>
                                    </q-card>
                                    <q-card class="q-my-sm" flat bordered>
                                        <q-card-section>
                                            <template v-if="descriptionSelected">
                                                <div class="q-my-sm">
                                                    <single-language-auto-complete :language="descriptionLanguage" :disable="loading" label="Description Language" @update:language="updateDescriptionLanguage"></single-language-auto-complete>
                                                </div>
                                                <div class="q-my-sm">
                                                    <q-option-group :options="descriptionSaveOptions" type="radio" v-model="selectedDescSaveMethod" :disable="loading" dense aria-label="Description save options" tabindex="0" />
                                                </div>
                                            </template>
                                            <div class="row q-my-sm">
                                                <q-input type="number" outlined v-model="maximumRecordsPerTaxon" class="col-6" label="Maximum records per taxon" hint="(Maximum 25)" min="1" max="25" :readonly="loading" @update:model-value="validateMaximumRecordsValue" dense tabindex="0" />
                                            </div>
                                            <div class="q-my-sm">
                                                <taxon-rank-checkbox-selector :selected-ranks="selectedRanks" :kingdom-id="kingdomId" :disable="loading" link-label="Select Taxonomic Ranks" inner-label="Select taxonomic ranks for taxa to be included in import" @update:selected-ranks="updateSelectedRanks"></taxon-rank-checkbox-selector>
                                            </div>
                                            <div class="q-my-sm">
                                                <q-checkbox v-model="importMissingOnly" label="Import only for taxa missing selected media type" :disable="loading" tabindex="0" />
                                            </div>
                                        </q-card-section>
                                    </q-card>
                                    <div class="processor-tool-control-container">
                                        <div class="processor-cancel-message-container text-negative text-bold">
                                            <template v-if="processCancelling">
                                                Cancelling, please wait
                                            </template>
                                        </div>
                                        <div class="processor-tool-button-container">
                                            <div>
                                                <q-btn :loading="loading" color="secondary" @click="initializeEOLImport();" label="Start" dense aria-label="Start Encyclopedia of Life Media Import" tabindex="0" />
                                            </div>
                                            <div>
                                                <q-btn v-if="loading" :disabled="processCancelling" color="red" @click="cancelProcess();" label="Cancel" dense aria-label="Cancel Encyclopedia of Life Media Import" tabindex="0" />
                                            </div>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>

                        <div class="processor-display-container">
                            <q-card class="bg-grey-3 q-pa-sm">
                                <q-scroll-area ref="procDisplayScrollAreaRef" class="bg-grey-1 processor-display" @scroll="setScroller">
                                    <q-list dense>
                                        <template v-if="!currentProcess && processorDisplayCurrentIndex > 0">
                                            <q-item>
                                                <q-item-section>
                                                    <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollUp();" @keyup.enter="processorDisplayScrollUp();" aria-label="Show previous 100 entries" tabindex="0">Show previous 100 entries</a></div>
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
                                                                    <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="q-ml-sm text-weight-bold text-green-9">
                                                                        <span class="q-ml-sm text-weight-bold text-green-9">{{subproc.resultText}}</span>
                                                                        <span class="q-ml-sm">
                                                                        <a :href="subproc.taxonPageHref" target="_blank" aria-label="Go to Taxon Profile Page - opens in separate tab" tabindex="0">(Go to Taxon Profile Page)</a>
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
                                        <template v-if="!currentProcess && processorDisplayCurrentIndex < processorDisplayIndex">
                                            <q-item>
                                                <q-item-section>
                                                    <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollDown();" @keyup.enter="processorDisplayScrollDown();" aria-label="Show next 100 entries" tabindex="0">Show next 100 entries</a></div>
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                    </q-list>
                                </q-scroll-area>
                            </q-card>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="text-weight-bold">You do not have permissions to access this tool</div>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleLanguageAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxonRankCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const eolMediaImporterModule = Vue.createApp({
                components: {
                    'single-language-auto-complete': singleLanguageAutoComplete,
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                    'taxon-rank-checkbox-selector': taxonRankCheckboxSelector
                },
                setup() {
                    const { getErrorResponseText, showNotification } = useCore();
                    const baseStore = useBaseStore();

                    let abortController = null;
                    const clientRoot = baseStore.getClientRoot;
                    const currentTaxon = Vue.ref(null);
                    const descriptionLanguage = Vue.ref(null);
                    const descriptionSaveOptions = [
                        { label: 'Save descriptions under a single Encyclopedia of Life tab', value: 'singletab' },
                        { label: 'Save descriptions under a separate tab for each topic', value: 'separatetabs' }
                    ];
                    const descriptionSelected = Vue.ref(false);
                    const eolIdentifierArr = Vue.ref([]);
                    const eolMedia = Vue.ref([]);
                    const identifierImportIndex = Vue.ref(1);
                    const importMissingOnly = Vue.ref(false);
                    const isEditor = Vue.ref(false);
                    const kingdomId = Vue.ref(null);
                    const loading = Vue.ref(false);
                    const maximumRecordsPerTaxon = Vue.ref(1);
                    const mediaCountImportIndex = Vue.ref(1);
                    const mediaTypeOptions = [
                        { label: 'Image', value: 'image' },
                        { label: 'Video', value: 'video' },
                        { label: 'Audio', value: 'audio' },
                        { label: 'Text Description', value: 'description' }
                    ];
                    const procDisplayScrollAreaRef = Vue.ref(null);
                    const procDisplayScrollHeight = Vue.ref(0);
                    const processCancelling = Vue.ref(false);
                    const processorDisplayArr = Vue.reactive([]);
                    let processorDisplayDataArr = [];
                    const processorDisplayCurrentIndex = Vue.ref(0);
                    const processorDisplayIndex = Vue.ref(0);
                    const scrollProcess = Vue.ref(null);
                    const selectedDescSaveMethod = Vue.ref('singletab');
                    const selectedMediaType = Vue.ref('image');
                    const selectedRanks = Vue.ref([]);
                    const taxaMediaArr = Vue.ref([]);
                    const taxonMediaArr = Vue.ref([]);
                    const taxonomicGroup = Vue.ref(null);
                    const taxonomicGroupTid = Vue.ref(null);
                    const taxonomicRanks = baseStore.getTaxonomicRanks;
                    const taxonUploadCount = Vue.ref(0);

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

                    function addSubprocessToProcessorDisplay(id, type, text) {
                        const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
                        parentProcObj['subs'].push(getNewSubprocessObject(currentTaxon.value['sciname'],type,text));
                        const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
                        if(dataParentProcObj){
                            dataParentProcObj['subs'].push(getNewSubprocessObject(currentTaxon.value['sciname'],type,text));
                        }
                    }

                    function addTaxonDescriptionStatement(statement) {
                        const formData = new FormData();
                        formData.append('statement', JSON.stringify(statement));
                        formData.append('action', 'createTaxonDescriptionStatementRecord');
                        fetch(taxonDescriptionStatementApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.text().then((res) => {
                                if(res && Number(res) > 0){
                                    taxonUploadCount.value++;
                                }
                                processEOLDescriptionRecords();
                            });
                        });
                    }

                    function addTaxonDescriptionTab(descTab, statement = null) {
                        const formData = new FormData();
                        formData.append('description', JSON.stringify(descTab));
                        formData.append('action', 'createTaxonDescriptionBlockRecord');
                        fetch(taxonDescriptionBlockApiUrl, {
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
                                        addTaxonDescriptionStatement(statement);
                                        descTab['stmts'].push(statement);
                                    }
                                    taxonMediaArr.value.push(descTab);
                                }
                                else{
                                    processEOLDescriptionRecords();
                                }
                            });
                        });
                    }

                    function adjustUIEnd() {
                        processCancelling.value = false;
                        eolIdentifierArr.value = [];
                        taxaMediaArr.value = [];
                        identifierImportIndex.value = 1;
                        mediaCountImportIndex.value = 1;
                        currentTaxon.value = null;
                        loading.value = false;
                        processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
                    }

                    function adjustUIStart() {
                        processorDisplayArr.length = 0;
                        processorDisplayDataArr = [];
                        processorDisplayCurrentIndex.value = 0;
                        processorDisplayIndex.value = 0;
                        loading.value = true;
                    }

                    function cancelProcess() {
                        processCancelling.value = true;
                        if(!currentTaxon.value){
                            if(abortController){
                                abortController.abort();
                            }
                            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
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
                            adjustUIEnd();
                        }
                    }

                    function getNewProcessObject(id, type, text) {
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
                    }

                    function getNewSubprocessObject(id, type, text) {
                        return {
                            id: id,
                            procText: text,
                            type: type,
                            loading: true,
                            result: '',
                            tid: 0,
                            resultText: ''
                        };
                    }

                    function getStoredIdentifiers() {
                        const formData = new FormData();
                        formData.append('tid', taxonomicGroupTid.value);
                        formData.append('source', 'eol');
                        formData.append('index', identifierImportIndex.value);
                        formData.append('action', 'getIdentifiersForTaxonomicGroup');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((resObj) => {
                                    if(resObj.length > 0){
                                        eolIdentifierArr.value = eolIdentifierArr.value.concat(resObj);
                                    }
                                    if(resObj.length < 50000){
                                        processSuccessResponse(true,'Complete');
                                        const text = 'Getting taxa and ' + selectedMediaType.value + ' counts for taxa within ' + taxonomicGroup.value;
                                        addProcessToProcessorDisplay(getNewProcessObject('setTaxaMediaArr','single',text));
                                        getTaxaMediaCounts();
                                    }
                                    else{
                                        identifierImportIndex.value++;
                                        getStoredIdentifiers();
                                    }
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                processErrorResponse(text);
                            }
                        });
                    }

                    function getTaxaMediaCounts() {
                        abortController = new AbortController();
                        const formData = new FormData();
                        formData.append('tid', taxonomicGroupTid.value);
                        formData.append('index', mediaCountImportIndex.value);
                        if(selectedMediaType.value === 'image'){
                            formData.append('action', 'getImageCountsForTaxonomicGroup');
                        }
                        else if(selectedMediaType.value === 'video'){
                            formData.append('action', 'getVideoCountsForTaxonomicGroup');
                        }
                        else if(selectedMediaType.value === 'audio'){
                            formData.append('action', 'getAudioCountsForTaxonomicGroup');
                        }
                        else if(selectedMediaType.value === 'description'){
                            formData.append('action', 'getDescriptionCountsForTaxonomicGroup');
                        }
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            signal: abortController.signal,
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((resObj) => {
                                    if(resObj.length > 0){
                                        taxaMediaArr.value = taxaMediaArr.value.concat(processTaxaMediaArr(resObj));
                                    }
                                    if(resObj.length < 50000){
                                        processSuccessResponse(true,'Complete');
                                        setCurrentTaxon();
                                    }
                                    else{
                                        mediaCountImportIndex.value++;
                                        getTaxaMediaCounts();
                                    }
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                processErrorResponse(text);
                            }
                        })
                        .catch((err) => {});
                    }

                    function initializeEOLImport() {
                        if(taxonomicGroupTid.value){
                            adjustUIStart();
                            const text = 'Getting stored Encyclopedia of Life identifiers for taxa within ' + taxonomicGroup.value;
                            addProcessToProcessorDisplay(getNewProcessObject('setIdentifierArr','single',text));
                            getStoredIdentifiers();
                        }
                        else{
                            showNotification('negative', 'Please enter a Taxonomic Group to start an import.');
                        }
                    }

                    function processEOLDescriptionRecords() {
                        if(!processCancelling.value && eolMedia.value.length > 0 && taxonUploadCount.value < maximumRecordsPerTaxon.value){
                            const mediaRecord = eolMedia.value[0];
                            eolMedia.value.splice(0, 1);
                            if(mediaRecord['language'] === descriptionLanguage.value['iso-1']){
                                if(selectedDescSaveMethod.value === 'singletab'){
                                    const existingEOLTab = taxonMediaArr.value.length > 0 ? taxonMediaArr.value.find(obj => obj['caption'] === 'Encyclopedia of Life') : null;
                                    const newTaxonStatement = {};
                                    newTaxonStatement['heading'] = mediaRecord['title'];
                                    newTaxonStatement['statement'] = mediaRecord['description'];
                                    if(existingEOLTab){
                                        const existingEOLStatement = existingEOLTab['stmts'].length > 0 ? existingEOLTab['stmts'].find(obj => obj['heading'] === mediaRecord['title']) : null;
                                        if(existingEOLStatement){
                                            processEOLDescriptionRecords();
                                        }
                                        else{
                                            newTaxonStatement['tdbid'] = existingEOLTab['tdbid'];
                                            newTaxonStatement['sortsequence'] = (existingEOLTab['stmts'].length + 1);
                                            addTaxonDescriptionStatement(newTaxonStatement);
                                            existingEOLTab['stmts'].push(newTaxonStatement);
                                        }
                                    }
                                    else{
                                        const newTaxonDescTab = {};
                                        newTaxonDescTab['tid'] = currentTaxon.value['tid'];
                                        newTaxonDescTab['caption'] = 'Encyclopedia of Life';
                                        newTaxonDescTab['language'] = descriptionLanguage.value['name'];
                                        newTaxonDescTab['langid'] = descriptionLanguage.value['id'];
                                        newTaxonDescTab['displaylevel'] = (taxonMediaArr.value.length + 1);
                                        newTaxonStatement['sortsequence'] = 1;
                                        addTaxonDescriptionTab(newTaxonDescTab,newTaxonStatement);
                                    }
                                }
                                else{
                                    const existingTab = taxonMediaArr.value.length > 0 ? taxonMediaArr.value.find(obj => obj['caption'] === mediaRecord['title']) : null;
                                    if(existingTab){
                                        processEOLDescriptionRecords();
                                    }
                                    else{
                                        const newTaxonDescTab = {};
                                        newTaxonDescTab['tid'] = currentTaxon.value['tid'];
                                        newTaxonDescTab['caption'] = mediaRecord['title'];
                                        newTaxonDescTab['source'] = mediaRecord['source'];
                                        newTaxonDescTab['sourceurl'] = mediaRecord['source'];
                                        newTaxonDescTab['language'] = descriptionLanguage.value['name'];
                                        newTaxonDescTab['langid'] = descriptionLanguage.value['id'];
                                        newTaxonDescTab['displaylevel'] = (taxonMediaArr.value.length + 1);
                                        const newTaxonStatement = {};
                                        newTaxonStatement['statement'] = mediaRecord['description'];
                                        newTaxonStatement['sortsequence'] = 1;
                                        newTaxonStatement['displayheader'] = 0;
                                        addTaxonDescriptionTab(newTaxonDescTab,newTaxonStatement);
                                    }
                                }
                            }
                            else{
                                processEOLDescriptionRecords();
                            }
                        }
                        else{
                            processSubprocessSuccessResponse(true,(taxonUploadCount.value + ' records uploaded'));
                            setCurrentTaxon();
                        }
                    }

                    function processEOLImageRecords() {
                        if(!processCancelling.value && eolMedia.value.length > 0 && taxonUploadCount.value < maximumRecordsPerTaxon.value){
                            const mediaRecord = eolMedia.value[0];
                            eolMedia.value.splice(0, 1);
                            const existingRecord = taxonMediaArr.value.length > 0 ? taxonMediaArr.value.find(obj => obj['url'] === mediaRecord['eolMediaURL']) : null;
                            if(existingRecord){
                                processEOLImageRecords();
                            }
                            else{
                                const newImageObj = {};
                                newImageObj['tid'] = currentTaxon.value['tid'];
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
                                newImageObj['sortsequence'] = '20';
                                const formData = new FormData();
                                formData.append('image', JSON.stringify(newImageObj));
                                formData.append('action', 'addImage');
                                fetch(imageApiUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    response.text().then((res) => {
                                        if(res && Number(res) > 0){
                                            taxonMediaArr.value.push(newImageObj);
                                            taxonUploadCount.value++;
                                        }
                                        processEOLImageRecords();
                                    });
                                });
                            }
                        }
                        else{
                            processSubprocessSuccessResponse(true,(taxonUploadCount.value + ' records uploaded'));
                            setCurrentTaxon();
                        }
                    }

                    function processEOLMediaRecords() {
                        if(!processCancelling.value && eolMedia.value.length > 0 && taxonUploadCount.value < maximumRecordsPerTaxon.value){
                            const mediaRecord = eolMedia.value[0];
                            eolMedia.value.splice(0, 1);
                            const existingRecord = taxonMediaArr.value.length > 0 ? taxonMediaArr.value.find(obj => obj['accessuri'] === mediaRecord['mediaURL']) : null;
                            if(existingRecord){
                                processEOLMediaRecords();
                            }
                            else{
                                const newMediaObj = {};
                                newMediaObj['tid'] = currentTaxon.value['tid'];
                                newMediaObj['accessuri'] = mediaRecord['mediaURL'];
                                newMediaObj['creator'] = mediaRecord.hasOwnProperty('rightsHolder') ? mediaRecord['rightsHolder'] : null;
                                newMediaObj['type'] = mediaRecord.hasOwnProperty('mediumType') ? mediaRecord['mediumType'] : null;
                                newMediaObj['format'] = mediaRecord.hasOwnProperty('mimeType') ? mediaRecord['mimeType'] : null;
                                newMediaObj['owner'] = 'Encyclopedia of Life';
                                newMediaObj['language'] = mediaRecord.hasOwnProperty('language') ? mediaRecord['language'] : null;
                                newMediaObj['usageterms'] = mediaRecord.hasOwnProperty('license') ? mediaRecord['license'] : null;
                                newMediaObj['description'] = mediaRecord.hasOwnProperty('description') ? mediaRecord['description'] : null;
                                newMediaObj['sortsequence'] = '20';
                                const formData = new FormData();
                                formData.append('media', JSON.stringify(newMediaObj));
                                formData.append('action', 'addMedia');
                                fetch(mediaApiUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    response.text().then((res) => {
                                        if(res && Number(res) > 0){
                                            taxonMediaArr.value.push(newMediaObj);
                                            taxonUploadCount.value++;
                                        }
                                        processEOLMediaRecords();
                                    });
                                });
                            }
                        }
                        else{
                            processSubprocessSuccessResponse(true,(taxonUploadCount.value + ' records uploaded'));
                            setCurrentTaxon();
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

                    function processMediaTypeChange(mediatype) {
                        descriptionSelected.value = (mediatype === 'description');
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

                    function processSubprocessErrorResponse(id, text) {
                        const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
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
                        const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentTaxon.value['sciname']);
                        if(parentProcObj){
                            parentProcObj['current'] = !complete;
                            const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                            if(subProcObj){
                                subProcObj['loading'] = false;
                                subProcObj['result'] = 'success';
                                subProcObj['resultText'] = text;
                                subProcObj['taxonPageHref'] = clientRoot + '/taxa/index.php?taxon=' + currentTaxon.value['tid'];
                            }
                        }
                    }

                    function processSuccessResponse(complete, text = null) {
                        const procObj = processorDisplayArr.find(proc => proc['current'] === true);
                        if(procObj){
                            procObj['current'] = !complete;
                            if(procObj['loading'] === true){
                                procObj['loading'] = false;
                                procObj['result'] = 'success';
                                procObj['resultText'] = text;
                            }
                        }
                    }

                    function processTaxaMediaArr(inArr) {
                        const newMediaArr = [];
                        if(Array.isArray(inArr) && inArr.length > 0){
                            inArr.forEach((taxon) => {
                                if(selectedRanks.value.includes(Number(taxon['rankid'])) && (!importMissingOnly.value || Number(taxon['cnt']) === 0)){
                                    const idObj = eolIdentifierArr.value.find(obj => Number(obj['tid']) === Number(taxon['tid']));
                                    taxon['eolid'] = idObj ? Number(idObj['identifier']) : null;
                                    newMediaArr.push(taxon);
                                }
                            });
                        }
                        return newMediaArr;
                    }

                    function resetScrollProcess() {
                        setTimeout(() => {
                            scrollProcess.value = null;
                        }, 200);
                    }

                    function setCurrentTaxon() {
                        if(!processCancelling.value && taxaMediaArr.value.length > 0){
                            taxonMediaArr.value = [];
                            eolMedia.value = [];
                            taxonUploadCount.value = 0;
                            currentTaxon.value = taxaMediaArr.value[0];
                            taxaMediaArr.value.splice(0, 1);
                            const text = 'Searching for ' + currentTaxon.value['sciname'];
                            addProcessToProcessorDisplay(getNewProcessObject(currentTaxon.value['sciname'],'multi',text));
                            if(!currentTaxon.value['eolid']){
                                const url = 'https://eol.org/api/search/1.0.json?q=' + currentTaxon.value['sciname'];
                                const formData = new FormData();
                                formData.append('url', url);
                                formData.append('action', 'getExternalData');
                                formData.append('requestType', 'get');
                                fetch(proxyServiceApiUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    if(response.status === 200){
                                        response.json().then((resObj) => {
                                            if(Number(resObj['totalResults']) > 0){
                                                const resArr = resObj['results'];
                                                const taxonResObj = resArr.find(obj => obj['title'] === currentTaxon.value['sciname']);
                                                if(taxonResObj){
                                                    currentTaxon.value['eolid'] = taxonResObj['id'];
                                                    const formData = new FormData();
                                                    formData.append('tid', currentTaxon.value['tid']);
                                                    formData.append('idname', 'eol');
                                                    formData.append('id', taxonResObj['id']);
                                                    formData.append('action', 'addTaxonIdentifier');
                                                    fetch(taxaApiUrl, {
                                                        method: 'POST',
                                                        body: formData
                                                    })
                                                        .then(() => {
                                                            processSuccessResponse(false);
                                                            setTaxonMediaArr();
                                                        });
                                                }
                                                else{
                                                    processErrorResponse('Not found');
                                                    setCurrentTaxon();
                                                }
                                            }
                                            else{
                                                processErrorResponse('Not found');
                                                setCurrentTaxon();
                                            }
                                        });
                                    }
                                    else{
                                        processErrorResponse('Unable to retrieve EOL taxon record');
                                        setCurrentTaxon();
                                    }
                                });
                            }
                            else{
                                processSuccessResponse(false);
                                setTaxonMediaArr();
                            }
                        }
                        else{
                            adjustUIEnd();
                        }
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

                    function setEOLMediaArr() {
                        let url;
                        const text = 'Getting ' + selectedMediaType.value + ' records';
                        addSubprocessToProcessorDisplay(currentTaxon.value['sciname'],'text',text);
                        if(selectedMediaType.value === 'image'){
                            url = 'https://eol.org/api/pages/1.0/' + currentTaxon.value['eolid'] + '.json?images_per_page=75';
                        }
                        else if(selectedMediaType.value === 'video'){
                            url = 'https://eol.org/api/pages/1.0/' + currentTaxon.value['eolid'] + '.json?videos_per_page=75';
                        }
                        else if(selectedMediaType.value === 'audio'){
                            url = 'https://eol.org/api/pages/1.0/' + currentTaxon.value['eolid'] + '.json?sounds_per_page=75';
                        }
                        else if(selectedMediaType.value === 'description'){
                            url = 'https://eol.org/api/pages/1.0/' + currentTaxon.value['eolid'] + '.json?texts_per_page=75';
                        }
                        const formData = new FormData();
                        formData.append('url', url);
                        formData.append('action', 'getExternalData');
                        formData.append('requestType', 'get');
                        fetch(proxyServiceApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((resObj) => {
                                    if(resObj.hasOwnProperty('taxonConcept') && resObj['taxonConcept'].hasOwnProperty('dataObjects')){
                                        eolMedia.value = resObj['taxonConcept']['dataObjects'];
                                        processSubprocessSuccessResponse(false);
                                        const text = 'Processing ' + selectedMediaType.value + ' records';
                                        addSubprocessToProcessorDisplay(currentTaxon.value['sciname'],'text',text);
                                        if(selectedMediaType.value === 'image'){
                                            processEOLImageRecords();
                                        }
                                        else if(selectedMediaType.value === 'video' || selectedMediaType.value === 'audio'){
                                            processEOLMediaRecords();
                                        }
                                        else if(selectedMediaType.value === 'description'){
                                            processEOLDescriptionRecords();
                                        }
                                    }
                                    else{
                                        const text = 'No ' + selectedMediaType.value + ' records found for ' + currentTaxon.value['sciname'];
                                        processSubprocessErrorResponse(currentTaxon.value['sciname'],text);
                                        setCurrentTaxon();
                                    }
                                });
                            }
                            else{
                                processSubprocessErrorResponse(currentTaxon.value['sciname'],'Error getting records');
                                setCurrentTaxon();
                            }
                        });
                    }

                    function setScroller(info) {
                        if(info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                            procDisplayScrollHeight.value = info.verticalSize;
                            if(scrollProcess.value && scrollProcess.value === 'scrollDown'){
                                procDisplayScrollAreaRef.value.setScrollPosition('vertical', 0);
                            }
                            else{
                                procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                            }
                        }
                    }

                    function setTaxonMediaArr() {
                        if(Number(currentTaxon.value['cnt']) > 0){
                            let dataSource = null;
                            const text = 'Getting existing ' + selectedMediaType.value + 's';
                            addSubprocessToProcessorDisplay(currentTaxon.value['sciname'],'text',text);
                            const formData = new FormData();
                            if(selectedMediaType.value === 'description'){
                                formData.append('tid', currentTaxon.value['tid']);
                                formData.append('action', 'getTaxonDescriptions');
                                dataSource = taxonDescriptionBlockApiUrl;
                            }
                            else{
                                formData.append('property', 'tid');
                                formData.append('value', currentTaxon.value['tid']);
                                if(selectedMediaType.value === 'image'){
                                    formData.append('action', 'getImageArrByProperty');
                                    dataSource = imageApiUrl;
                                }
                                else if(selectedMediaType.value === 'audio' || selectedMediaType.value === 'video'){
                                    formData.append('limitFormat', selectedMediaType.value);
                                    formData.append('action', 'getMediaArrByProperty');
                                    dataSource = mediaApiUrl;
                                }
                            }
                            if(dataSource){
                                fetch(dataSource, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    if(response.status === 200){
                                        response.json().then((resObj) => {
                                            taxonMediaArr.value = resObj;
                                            processSubprocessSuccessResponse(false);
                                            setEOLMediaArr();
                                        });
                                    }
                                    else{
                                        processSubprocessErrorResponse(currentTaxon.value['sciname'],'Error getting records');
                                        setCurrentTaxon();
                                    }
                                });
                            }
                            else{
                                setEOLMediaArr();
                            }
                        }
                        else{
                            setEOLMediaArr();
                        }
                    }

                    function updateDescriptionLanguage(langObj) {
                        descriptionLanguage.value = langObj;
                    }

                    function updateSelectedRanks(selectedArr) {
                        selectedRanks.value = selectedArr;
                    }

                    function updateTaxonomicGroup(taxonObj) {
                        taxonomicGroup.value = taxonObj.sciname;
                        taxonomicGroupTid.value = taxonObj ? taxonObj.tid : null;
                        kingdomId.value = taxonObj ? taxonObj.kingdomid : null;
                    }

                    function validateMaximumRecordsValue() {
                        if(maximumRecordsPerTaxon.value > 25){
                            maximumRecordsPerTaxon.value = 25;
                        }
                        if(maximumRecordsPerTaxon.value < 1){
                            maximumRecordsPerTaxon.value = 1;
                        }
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        selectedRanks.value = taxonomicRanks;
                    });

                    return {
                        clientRoot,
                        descriptionLanguage,
                        descriptionSaveOptions,
                        descriptionSelected,
                        importMissingOnly,
                        isEditor,
                        kingdomId,
                        loading,
                        maximumRecordsPerTaxon,
                        mediaTypeOptions,
                        procDisplayScrollAreaRef,
                        processCancelling,
                        processorDisplayArr,
                        processorDisplayCurrentIndex,
                        processorDisplayIndex,
                        selectedDescSaveMethod,
                        selectedMediaType,
                        selectedRanks,
                        taxonomicGroup,
                        cancelProcess,
                        initializeEOLImport,
                        processMediaTypeChange,
                        processorDisplayScrollDown,
                        processorDisplayScrollUp,
                        setScroller,
                        updateDescriptionLanguage,
                        updateSelectedRanks,
                        updateTaxonomicGroup,
                        validateMaximumRecordsValue
                    }
                }
            });
            eolMediaImporterModule.use(Quasar, { config: {} });
            eolMediaImporterModule.use(Pinia.createPinia());
            eolMediaImporterModule.mount('#mainContainer');
        </script>
    </body>
</html>
