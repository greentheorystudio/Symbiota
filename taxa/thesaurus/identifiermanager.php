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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomic Identifier Manager</title>
        <meta name="description" content="Taxonomic identifier manager for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <style>
            .top-tool-container {
                width: 500px;
            }
        </style>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Taxonomic Identifier Manager</span>
            </div>
            <div class="q-pa-md">
                <h1>Taxonomic Identifier Manager</h1>
                <template v-if="isEditor">
                    <q-card>
                        <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab name="datafileupload" label="Upload Data File" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel name="datafileupload">
                                <div class="processor-container">
                                    <div class="processor-control-container">
                                        <div class="row q-mb-md">
                                            <taxa-kingdom-selector :disable="loading" :selected-kingdom="selectedKingdom" label="Target Kingdom" class="col-grow" @update:selected-kingdom="updateSelectedKingdom"></taxa-kingdom-selector>
                                        </div>
                                        <q-card class="processor-control-card">
                                            <q-card-section>
                                                <div class="process-header">
                                                    Upload USDA Symbol data file
                                                </div>
                                                Copy and save the <a href="https://plants.usda.gov/csvdownload?plantLst=nonLichenFungiSymbol" target="_blank">Fungi data</a> or <a href="https://plants.usda.gov/csvdownload?plantLst=plantCompleteList" target="_blank">Plant data</a>
                                                from the <a href="https://plants.usda.gov/home/downloads" target="_blank">USDA PLANTS Download page</a> into a txt file, then upload the file below and click Start.
                                                <div class="row q-mt-xs">
                                                    <div class="col-grow">
                                                        <file-picker-input-element :accepted-types="acceptedFileTypes" :disabled="loading" :value="selectedUsdaFile" :validate-file-size="false" @update:file="(value) => processFileSelection(value)"></file-picker-input-element>
                                                    </div>
                                                </div>
                                                <div class="processor-tool-control-container">
                                                    <div class="processor-cancel-message-container text-negative text-bold">
                                                        <template v-if="processCancelling && currentProcess === 'initializeUSDAImport'">
                                                            Cancelling, please wait
                                                        </template>
                                                    </div>
                                                    <div class="processor-tool-button-container">
                                                        <div>
                                                            <q-btn :loading="currentProcess === 'initializeUSDAImport'" :disabled="currentProcess && currentProcess !== 'initializeUSDAImport'" color="secondary" @click="initializeUSDAImport();" label="Start" dense aria-label="Start Upload USDA Symbol data file" tabindex="0" />
                                                        </div>
                                                        <div>
                                                            <q-btn v-if="currentProcess === 'initializeUSDAImport'" :disabled="processCancelling && currentProcess === 'initializeUSDAImport'" color="red" @click="cancelProcess();" label="Cancel" dense aria-label="Cancel Upload USDA Symbol data file" tabindex="0" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
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
                                                                <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollUp();" aria-label="Show previous 100 entries" tabindex="0">Show previous 100 entries</a></div>
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
                                                                <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollDown();" aria-label="Show next 100 entries" tabindex="0">Show next 100 entries</a></div>
                                                            </q-item-section>
                                                        </q-item>
                                                    </template>
                                                </q-list>
                                            </q-scroll-area>
                                        </q-card>
                                    </div>
                                </div>
                            </q-tab-panel>
                        </q-tab-panels>
                    </q-card>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxaKingdomSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/filePickerInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const taxonomicThesaurusManagerModule = Vue.createApp({
                components: {
                    'file-picker-input-element': filePickerInputElement,
                    'taxa-kingdom-selector': taxaKingdomSelector
                },
                setup() {
                    const { csvToArray, getErrorResponseText, parseFile, showNotification } = useCore();
                    const baseStore = useBaseStore();

                    let abortController = null;
                    const acceptedFileTypes = ['csv', 'txt'];
                    const clientRoot = baseStore.getClientRoot;
                    const currentProcess = Vue.ref(null);
                    const isEditor = Vue.ref(false);
                    const loading = Vue.ref(false);
                    const procDisplayScrollAreaRef = Vue.ref(null);
                    const procDisplayScrollHeight = Vue.ref(0);
                    const processCancelling = Vue.ref(false);
                    const processingArr = Vue.ref([]);
                    const processorDisplayArr = Vue.reactive([]);
                    let processorDisplayDataArr = [];
                    const processorDisplayCurrentIndex = Vue.ref(0);
                    const processorDisplayIndex = Vue.ref(0);
                    const scrollProcess = Vue.ref(null);
                    const selectedKingdom = Vue.ref(null);
                    const selectedKingdomId = Vue.ref(null);
                    const selectedKingdomName = Vue.ref(null);
                    const selectedUsdaFile = Vue.ref(null);
                    const tab = Vue.ref('datafileupload');

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

                    function addTaxonIdentifier(tid, identifier, identifierName) {
                        const formData = new FormData();
                        formData.append('action', 'addTaxonIdentifier');
                        formData.append('tid', tid);
                        formData.append('idname', identifierName);
                        formData.append('id', identifier);
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        });
                    }

                    function adjustUIEnd() {
                        currentProcess.value = null;
                        processCancelling.value = false;
                        processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
                        loading.value = false;
                    }

                    function adjustUIStart() {
                        loading.value = true;
                        processorDisplayArr.length = 0;
                        processorDisplayDataArr = [];
                        processorDisplayCurrentIndex.value = 0;
                        processorDisplayIndex.value = 0;
                    }

                    function cancelProcess() {
                        processCancelling.value = true;
                        if(abortController){
                            abortController.abort();
                        }
                    }

                    function cleanUsdaSciName(sciname) {
                        if(sciname.includes(' sp. nov.')){
                            sciname = sciname.replace(' sp. nov.', '');
                        }
                        if(sciname.includes(' sp.')){
                            sciname = sciname.replace(' sp.', '');
                        }
                        if(sciname.includes(' ssp. ')){
                            sciname = sciname.replace(' ssp. ', ' subsp. ');
                        }
                        if(sciname.includes(' ×')){
                            sciname = sciname.replace(' ×', ' x ');
                        }
                        if(sciname.includes(' auct. non ')){
                            sciname = sciname.replace(' auct. non ', ' ');
                        }
                        if(sciname.endsWith(' f., orth. var.')){
                            sciname = sciname.replace(' f., orth. var.', '');
                        }
                        if(sciname.endsWith(' f.')){
                            sciname = sciname.replace(' f.', '');
                        }
                        return sciname;
                    }

                    function findTaxonBySciname(sciname, callback) {
                        const formData = new FormData();
                        formData.append('action', 'getTaxonFromSciname');
                        formData.append('sciname', sciname);
                        formData.append('kingdomid', selectedKingdomId.value);
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((resObj) => {
                                    callback(resObj);
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                callback(null, text);
                            }
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

                    function initializeUSDAImport() {
                        processingArr.value.length = 0;
                        if(selectedKingdomName.value === 'Fungi' || selectedKingdomName.value === 'Plantae'){
                            if(selectedUsdaFile.value){
                                adjustUIStart();
                                currentProcess.value = 'initializeUSDAImport';
                                parseFile(selectedUsdaFile.value, (fileContents) => {
                                    csvToArray(fileContents).then((csvData) => {
                                        if(csvData[0] && csvData[0].hasOwnProperty('Symbol') && ((selectedKingdomName.value === 'Fungi' && csvData[0].hasOwnProperty('ScientificName')) || (selectedKingdomName.value === 'Plantae' && csvData[0].hasOwnProperty('Scientific Name with Author')))){
                                            processingArr.value = csvData;
                                            if(selectedKingdomName.value === 'Fungi'){
                                                processUsdaFungiSymbolUpload();
                                            }
                                            else{
                                                processUsdaPlantaeSymbolUpload();
                                            }
                                        }
                                        else{
                                            showNotification('negative', 'There is an issue with processing the USDA data.');
                                        }
                                    });
                                });
                            }
                            else{
                                showNotification('negative', 'You must choose a data file before starting the upload.');
                            }
                        }
                        else{
                            showNotification('negative', 'USDA symbols are only available for taxa in the Fungi or Plantae kingdoms. Please select one of those kingdoms.');
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

                    function processFileSelection(file) {
                        if(file){
                            selectedUsdaFile.value = file[0];
                        }
                        else{
                            selectedUsdaFile.value = null;
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

                    function processUsdaFungiSymbolUpload() {
                        if(!processCancelling.value && processingArr.value.length > 0){
                            const currentData = processingArr.value[0];
                            processingArr.value.splice(0, 1);
                            const sciname = cleanUsdaSciName(currentData['ScientificName']);
                            const text = 'Processing: ' + sciname;
                            addProcessToProcessorDisplay(getNewProcessObject('single', text));
                            findTaxonBySciname(sciname, (resObj, errorText = null) => {
                                if(errorText){
                                    adjustUIEnd();
                                }
                                else{
                                    if(resObj && resObj.hasOwnProperty('tid')){
                                        const usdaIdentifier = resObj['identifiers'].find(obj => obj['name'] === 'usda');
                                        if(usdaIdentifier){
                                            if(usdaIdentifier['identifier'] !== currentData['Symbol']){
                                                updateTaxonIdentifier(resObj['tid'], currentData['Symbol'], 'usda');
                                            }
                                            else{
                                                processErrorResponse('USDA symbol already exists');
                                                processUsdaFungiSymbolUpload();
                                            }
                                        }
                                        else{
                                            addTaxonIdentifier(resObj['tid'], currentData['Symbol'], 'usda');
                                            processSuccessResponse('USDA symbol added');
                                            processUsdaFungiSymbolUpload();
                                        }
                                    }
                                    else{
                                        processErrorResponse('Not found in Taxonomic Thesaurus');
                                        processUsdaFungiSymbolUpload();
                                    }
                                }
                            });
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function processUsdaPlantaeSymbolUpload() {
                        if(!processCancelling.value && processingArr.value.length > 0){
                            const currentData = processingArr.value[0];
                            processingArr.value.splice(0, 1);
                            const sciname = cleanUsdaSciName(currentData['Scientific Name with Author']);
                            const text = 'Processing: ' + sciname;
                            addProcessToProcessorDisplay(getNewProcessObject('single', text));
                            const formData = new FormData();
                            formData.append('sciname', sciname);
                            formData.append('action', 'parseSciName');
                            fetch(taxonomyServiceApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.json().then((parsedName) => {
                                        if(parsedName.hasOwnProperty('sciname') && parsedName['sciname'] !== ''){
                                            findTaxonBySciname(parsedName['sciname'], (resObj, errorText = null) => {
                                                if(errorText){
                                                    adjustUIEnd();
                                                }
                                                else{
                                                    if(resObj && resObj.hasOwnProperty('tid')){
                                                        const usdaIdentifier = resObj['identifiers'].find(obj => obj['name'] === 'usda');
                                                        if(usdaIdentifier){
                                                            if(usdaIdentifier['identifier'] !== currentData['Symbol']){
                                                                updateTaxonIdentifier(resObj['tid'], currentData['Symbol'], 'usda');
                                                            }
                                                            else{
                                                                processErrorResponse('USDA symbol already exists');
                                                                processUsdaPlantaeSymbolUpload();
                                                            }
                                                        }
                                                        else{
                                                            addTaxonIdentifier(resObj['tid'], currentData['Symbol'], 'usda');
                                                            processSuccessResponse('USDA symbol added');
                                                            processUsdaPlantaeSymbolUpload();
                                                        }
                                                    }
                                                    else{
                                                        processErrorResponse('Not found in Taxonomic Thesaurus');
                                                        processUsdaPlantaeSymbolUpload();
                                                    }
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function resetScrollProcess() {
                        setTimeout(() => {
                            scrollProcess.value = null;
                        }, 200);
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'Taxonomy');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resData) => {
                                isEditor.value = resData.includes('Taxonomy');
                            });
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

                    function updateLoading(value) {
                        loading.value = value;
                    }

                    function updateSelectedKingdom(kingdomObj) {
                        selectedKingdom.value = kingdomObj;
                        selectedKingdomId.value = kingdomObj.id;
                        selectedKingdomName.value = kingdomObj.name;
                    }

                    function updateTaxonIdentifier(tid, identifier, identifierName) {
                        const formData = new FormData();
                        formData.append('action', 'updateTaxonIdentifier');
                        formData.append('tid', tid);
                        formData.append('idname', identifierName);
                        formData.append('id', identifier);
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        });
                    }

                    Vue.onMounted(() => {
                        setEditor();
                    });
                    
                    return {
                        acceptedFileTypes,
                        clientRoot,
                        currentProcess,
                        isEditor,
                        loading,
                        procDisplayScrollAreaRef,
                        processCancelling,
                        processorDisplayArr,
                        processorDisplayCurrentIndex,
                        processorDisplayIndex,
                        selectedKingdom,
                        selectedUsdaFile,
                        tab,
                        cancelProcess,
                        initializeUSDAImport,
                        processorDisplayScrollDown,
                        processorDisplayScrollUp,
                        processFileSelection,
                        setScroller,
                        updateLoading,
                        updateSelectedKingdom
                    }
                }
            });
            taxonomicThesaurusManagerModule.use(Quasar, { config: {} });
            taxonomicThesaurusManagerModule.use(Pinia.createPinia());
            taxonomicThesaurusManagerModule.mount('#mainContainer');
        </script>
    </body>
</html>
