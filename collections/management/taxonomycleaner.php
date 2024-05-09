<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Management Module</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            const COLLID = <?php echo $collid; ?>;
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div class='navpath'>
            <a href="../../index.php">Home</a> &gt;&gt;
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
            <b>Taxonomy Management Module</b>
        </div>
        <div id="innertext">
            <template v-if="isEditor">
                <div class="row justify-between q-px-md q-mb-sm">
                    <div class="text-h6 text-weight-bold">
                        <template v-if="collInfo && collInfo.collectionname">{{ collInfo.collectionname }}</template>
                        <template v-if="collInfo && (collInfo.institutioncode || collInfo.collectioncode)"> (<template v-if="collInfo.institutioncode">{{ collInfo.institutioncode }}</template><template v-if="collInfo.institutioncode && collInfo.collectioncode">-</template><template v-if="collInfo.collectioncode">{{ collInfo.collectioncode }}</template>)</template>
                    </div>
                    <div onclick="openTutorialWindow('/tutorial/collections/management/taxonomy/index.php?collid=' + collId);" title="Open Tutorial Window">
                        <q-icon name="far fa-question-circle" size="20px" class="cursor-pointer" />
                    </div>
                </div>
                <div class="row justify-between q-px-lg q-mb-sm">
                    <div class="text-weight-bold col-grow">
                        <div class="row q-mt-xs">
                            <taxa-kingdom-selector :disable="uppercontrolsdisabled" :selected-kingdom="selectedKingdom" label="Target Kingdom" class="col-4" @update:selected-kingdom="updateSelectedKingdom"></taxa-kingdom-selector>
                        </div>
                        <div class="row q-mt-xs">
                            <q-input outlined v-model="processingStartIndex" label="Processing Start Index" class="col-4" :readonly="uppercontrolsdisabled" dense></q-input>
                        </div>
                        <div class="row q-mt-xs">
                            <q-input type="number" outlined v-model="processingLimit" label="Processing Batch Limit" class="col-4" @update:model-value="processingBatchLimitChange" :readonly="uppercontrolsdisabled" dense></q-input>
                        </div>
                    </div>
                    <div class="row text-weight-bold justify-end col-4">
                        Occurrences not linked to taxonomic thesaurus: {{ unlinkedOccCnt }}<q-spinner v-if="unlinkedLoading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner><br/>
                        Unique scientific names: {{ unlinkedTaxaCnt }}<q-spinner v-if="unlinkedLoading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner><br/>
                    </div>
                </div>
                <div class="processor-container">
                    <div class="processor-control-container">
                        <q-card class="processor-control-card">
                            <q-list class="processor-control-accordion">
                                <q-expansion-item class="overflow-hidden" group="controlgroup" label="Maintenance Utilities" header-class="bg-grey-3 text-bold" default-opened>
                                    <q-card class="accordion-panel">
                                        <q-card-section>
                                            <div class="process-header">
                                                General Cleaning
                                            </div>
                                            Run cleaning processes to remove unnecessary endings, identification qualifiers and question marks, and normalize
                                            infraspecific rank references in occurrence record scientific names that are not linked to
                                            the Taxonomic Thesaurus.
                                            <div class="processor-tool-control-container">
                                                <div class="processor-cancel-message-container text-negative text-bold">
                                                    <template v-if="processCancelling && currentProcess === 'cleanProcesses'">
                                                        Cancelling, please wait
                                                    </template>
                                                </div>
                                                <div class="processor-tool-button-container">
                                                    <div>
                                                        <q-btn :loading="currentProcess === 'cleanProcesses'" :disabled="currentProcess && currentProcess !== 'cleanProcesses'" color="secondary" @click="callCleaningController('question-marks');" label="Start" dense />
                                                    </div>
                                                    <div>
                                                        <q-btn v-if="currentProcess === 'cleanProcesses'" :disabled="processCancelling && currentProcess === 'cleanProcesses'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                                    </div>
                                                </div>
                                            </div>
                                            <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                            <div class="process-header">
                                                Scientific Name Authorship Cleaning
                                            </div>
                                            Run a cleaning process to remove the scientific name authors from occurrence record scientific
                                            names that are not linked to the Taxonomic Thesaurus.
                                            <div class="processor-tool-control-container">
                                                <div class="processor-cancel-message-container text-negative text-bold">
                                                    <template v-if="processCancelling && currentProcess === 'cleanScinameAuthor'">
                                                        Cancelling, please wait
                                                    </template>
                                                </div>
                                                <div class="processor-tool-button-container">
                                                    <div>
                                                        <q-btn :loading="currentProcess === 'cleanScinameAuthor'" :disabled="currentProcess && currentProcess !== 'cleanScinameAuthor'" color="secondary" @click="initializeCleanScinameAuthor();" label="Start" dense />
                                                    </div>
                                                    <div>
                                                        <q-btn v-if="currentProcess === 'cleanScinameAuthor'" :disabled="processCancelling && currentProcess === 'cleanScinameAuthor'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                                    </div>
                                                </div>
                                            </div>
                                            <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                            <div class="process-header">
                                                Set Taxonomic Thesaurus Linkages
                                            </div>
                                            Set occurrence record linkages to the Taxonomic Thesaurus.
                                            <div class="q-mt-xs">
                                                <q-checkbox v-model="updatedet" label="Include associated determination records" :disable="uppercontrolsdisabled" />
                                            </div>
                                            <div class="processor-tool-control-container">
                                                <div class="processor-cancel-message-container text-negative text-bold">
                                                    <template v-if="processCancelling && currentProcess === 'updateWithTaxThesaurus'">
                                                        Cancelling, please wait
                                                    </template>
                                                </div>
                                                <div class="processor-tool-button-container">
                                                    <div>
                                                        <q-btn :loading="currentProcess === 'updateWithTaxThesaurus'" :disabled="currentProcess && currentProcess !== 'updateWithTaxThesaurus'" color="secondary" @click="callTaxThesaurusLinkController();" label="Start" dense />
                                                    </div>
                                                    <div>
                                                        <q-btn v-if="currentProcess === 'updateWithTaxThesaurus'" :disabled="processCancelling && currentProcess === 'updateWithTaxThesaurus'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                                    </div>
                                                </div>
                                            </div>
                                            <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                            <div class="process-header">
                                                Update Locality Security Settings
                                            </div>
                                            Update locality security settings for occurrence records of protected species.
                                            <div class="processor-tool-control-container">
                                                <div class="processor-cancel-message-container text-negative text-bold">
                                                    <template v-if="processCancelling && currentProcess === 'updateOccLocalitySecurity'">
                                                        Cancelling, please wait
                                                    </template>
                                                </div>
                                                <div class="processor-tool-button-container">
                                                    <div>
                                                        <q-btn :loading="currentProcess === 'updateOccLocalitySecurity'" :disabled="currentProcess && currentProcess !== 'updateOccLocalitySecurity'" color="secondary" @click="updateOccLocalitySecurity();" label="Start" dense />
                                                    </div>
                                                    <div>
                                                        <q-btn v-if="currentProcess === 'updateOccLocalitySecurity'" :disabled="processCancelling && currentProcess === 'updateOccLocalitySecurity'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                                    </div>
                                                </div>
                                            </div>
                                            <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                        </q-card-section>
                                    </q-card>
                                </q-expansion-item>
                                <q-separator></q-separator>
                                <q-expansion-item class="overflow-hidden" group="controlgroup" label="Search Utilities" header-class="bg-grey-3 text-bold">
                                    <q-card class="accordion-panel">
                                        <q-card-section>
                                            <div class="process-header">
                                                Search Taxonomic Data Sources
                                            </div>
                                            <div class="q-mb-sm">
                                                Search for occurrence record scientific names that are not currently linked to the Taxonomic Thesaurus
                                                from an external Taxonomic Data Source.
                                            </div>
                                            <div class="q-mb-sm">
                                                <taxonomy-data-source-bullet-selector :disable="uppercontrolsdisabled" :selected-data-source="dataSource" @update:selected-data-source="updateSelectedDataSource"></taxonomy-data-source-bullet-selector>
                                            </div>
                                            <div class="processor-tool-control-container">
                                                <div class="processor-cancel-message-container text-negative text-bold">
                                                    <template v-if="processCancelling && currentProcess === 'resolveFromTaxaDataSource'">
                                                        Cancelling, please wait
                                                    </template>
                                                </div>
                                                <div class="processor-tool-button-container">
                                                    <div>
                                                        <q-btn :loading="currentProcess === 'resolveFromTaxaDataSource'" :disabled="currentProcess && currentProcess !== 'resolveFromTaxaDataSource'" color="secondary" @click="initializeDataSourceSearch();" label="Start" dense />
                                                    </div>
                                                    <div>
                                                        <q-btn v-if="currentProcess === 'resolveFromTaxaDataSource'" :disabled="processCancelling && currentProcess === 'resolveFromTaxaDataSource'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                                    </div>
                                                </div>
                                            </div>
                                            <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                            <div class="process-header">
                                                Taxonomic Thesaurus Fuzzy Search
                                            </div>
                                            Get fuzzy matches to occurrence record scientific names that are not yet linked to the Taxonomic Thesaurus
                                            with taxa currently in the Taxonomic Thesaurus.
                                            <div class="row q-mt-xs">
                                                <q-input type="number" outlined v-model="levValue" class="col-5" label="Character difference tolerance" :readonly="uppercontrolsdisabled" dense></q-input>
                                            </div>
                                            <div class="processor-tool-control-container">
                                                <div class="processor-cancel-message-container text-negative text-bold">
                                                    <template v-if="processCancelling && currentProcess === 'taxThesaurusFuzzyMatch'">
                                                        Cancelling, please wait
                                                    </template>
                                                </div>
                                                <div class="processor-tool-button-container">
                                                    <div>
                                                        <q-btn :loading="currentProcess === 'taxThesaurusFuzzyMatch'" :disabled="currentProcess && currentProcess !== 'taxThesaurusFuzzyMatch'" color="secondary" @click="initializeTaxThesaurusFuzzyMatch();" label="Start" dense />
                                                    </div>
                                                    <div>
                                                        <q-btn v-if="currentProcess === 'taxThesaurusFuzzyMatch'" :disabled="processCancelling && currentProcess === 'taxThesaurusFuzzyMatch'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                                    </div>
                                                </div>
                                            </div>
                                            <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
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
                                                                <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="q-ml-sm text-weight-bold text-green-9">
                                                                    {{subproc.resultText}}
                                                                </div>
                                                                <div v-if="subproc.result === 'success' && subproc.type === 'undo'" class="q-ml-sm text-weight-bold text-green-9">
                                                                    {{subproc.resultText}} <q-btn :disabled="undoButtonsDisabled" class="q-ml-md text-grey-9" color="warning" size="sm" @click="undoChangedSciname(proc.id,subproc.undoOrigName,subproc.undoChangedName);" label="Undo" dense />
                                                                </div>
                                                                <div v-if="subproc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                                                    {{subproc.resultText}}
                                                                </div>
                                                            </template>
                                                        </template>
                                                        <template v-if="subproc.type === 'fuzzy'">
                                                            <template v-if="subproc.procText === 'skip'">
                                                                <div class="q-mx-xl q-my-sm fuzzy-match-row">
                                                                    <div></div>
                                                                    <div>
                                                                        <q-btn :disabled="!(currentSciname === proc.id)" class="q-ml-md" color="primary" size="sm" @click="runTaxThesaurusFuzzyMatchProcess();" label="Skip Taxon" dense />
                                                                    </div>
                                                                </div>
                                                            </template>
                                                            <template v-else>
                                                                <div class="q-mx-xl q-my-sm fuzzy-match-row">
                                                                    <div class="text-weight-bold">
                                                                        {{ subproc.procText }}
                                                                    </div>
                                                                    <div>
                                                                        <q-btn :disabled="!(currentSciname === proc.id)" class="q-ml-md" color="primary" size="sm" @click="selectFuzzyMatch(subproc.undoOrigName,subproc.undoChangedName,subproc.changedTid);" label="Select" dense />
                                                                    </div>
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
            </template>
        </div>
        <?php
        include(__DIR__ . '/../../footer.php');
        include_once(__DIR__ . '/../../config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxaKingdomSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxonomyDataSourceBulletSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const occurrenceTaxonomyManagementModule = Vue.createApp({
                components: {
                    'taxa-kingdom-selector': taxaKingdomSelector,
                    'taxonomy-data-source-bullet-selector': taxonomyDataSourceBulletSelector
                },
                setup() {
                    const { getErrorResponseText, openTutorialWindow, showNotification } = useCore();
                    const store = useBaseStore();
                    let abortController = null;
                    const changedCurrentSciname = Vue.ref('');
                    const changedParsedSciname = Vue.ref('');
                    const colInitialSearchResults = [];
                    const collId = COLLID;
                    const collInfo = Vue.ref(null);
                    const currentProcess = Vue.ref(null);
                    const currentSciname = Vue.ref(null);
                    const dataSource = Vue.ref('col');
                    const isEditor = Vue.ref(false);
                    const itisInitialSearchResults = [];
                    const levValue = Vue.ref(2);
                    let nameSearchResults = [];
                    let nameTidIndex = {};
                    const newTidArr = [];
                    const procDisplayScrollAreaRef = Vue.ref(null);
                    const procDisplayScrollHeight = Vue.ref(0);
                    const processCancelling = Vue.ref(false);
                    let processingArr = [];
                    const processingLimit = Vue.ref(null);
                    const processingStartIndex = Vue.ref(null);
                    const processorDisplayArr = Vue.reactive([]);
                    let processorDisplayDataArr = [];
                    const processorDisplayCurrentIndex = Vue.ref(0);
                    const processorDisplayIndex = Vue.ref(0);
                    let rankArr = {};
                    let rebuildHierarchyLoop = 0;
                    let scrollProcess = null;
                    const selectedKingdom = Vue.ref(null);
                    const selectedKingdomId = Vue.ref(null);
                    const selectedKingdomName = Vue.ref(null);
                    let taxaLoaded = 0;
                    let taxaToAddArr = [];
                    const taxonomicRanks = store.getTaxonomicRanks;
                    const undoButtonsDisabled = Vue.ref(true);
                    const undoId = Vue.ref(null);
                    const unlinkedLoading = Vue.ref(false);
                    let unlinkedNamesArr = [];
                    const unlinkedOccCnt = Vue.ref(0);
                    const unlinkedTaxaCnt = Vue.ref(0);
                    const updatedet = Vue.ref(false);
                    const uppercontrolsdisabled = Vue.ref(false);

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
                        parentProcObj['subs'].push(getNewSubprocessObject(currentSciname.value, type, text));
                        const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
                        if(dataParentProcObj){
                            dataParentProcObj['subs'].push(getNewSubprocessObject(currentSciname.value, type, text));
                        }
                    }

                    function adjustUIEnd() {
                        processCancelling.value = false;
                        unlinkedNamesArr = [];
                        currentSciname.value = null;
                        setUnlinkedRecordCounts();
                        currentProcess.value = null;
                        undoButtonsDisabled.value = false;
                        uppercontrolsdisabled.value = false;
                        processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
                    }

                    function adjustUIStart(id) {
                        processorDisplayArr.length = 0;
                        processorDisplayDataArr = [];
                        processorDisplayCurrentIndex.value = 0;
                        processorDisplayIndex.value = 0;
                        scrollProcess = null;
                        currentProcess.value = id;
                        uppercontrolsdisabled.value = true;
                        undoButtonsDisabled.value = true;
                    }

                    function callCleaningController(step) {
                        abortController = new AbortController();
                        const formData = new FormData();
                        formData.append('collid', collId);
                        if(step === 'question-marks'){
                            adjustUIStart('cleanProcesses');
                            const text = 'Cleaning question marks from scientific names';
                            addProcessToProcessorDisplay(getNewProcessObject('cleanQuestionMarks', 'single', text));
                            formData.append('action', 'cleanQuestionMarks');
                        }
                        if(!processCancelling.value){
                            if(step === 'clean-sp'){
                                const text = 'Cleaning scientific names ending in sp., sp. nov., spp., or group';
                                addProcessToProcessorDisplay(getNewProcessObject('cleanSpNames', 'single', text));
                                formData.append('action', 'cleanSpNames');
                            }
                            else if(step === 'clean-infra'){
                                const text = 'Normalizing infraspecific rank abbreviations';
                                addProcessToProcessorDisplay(getNewProcessObject('cleanInfra', 'single', text));
                                formData.append('action', 'cleanInfra');
                            }
                            else if(step === 'clean-qualifier'){
                                const text = 'Cleaning scientific names containing cf. or aff.';
                                addProcessToProcessorDisplay(getNewProcessObject('cleanQualifierNames', 'single', text));
                                formData.append('action', 'cleanQualifierNames');
                            }
                            else if(step === 'double-spaces'){
                                const text = 'Cleaning scientific names containing double spaces';
                                addProcessToProcessorDisplay(getNewProcessObject('cleanDoubleSpaces', 'single', text));
                                formData.append('action', 'cleanDoubleSpaces');
                            }
                            else if(step === 'leading-trailing-spaces'){
                                const text = 'Cleaning leading and trailing spaces in scientific names';
                                addProcessToProcessorDisplay(getNewProcessObject('cleanTrimNames', 'single', text));
                                formData.append('action', 'cleanTrimNames');
                            }
                            fetch(occurrenceTaxonomyApiUrl, {
                                method: 'POST',
                                signal: abortController.signal,
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.text().then((text) => {
                                        processSuccessResponse(true, 'Complete: ' + text + ' records cleaned');
                                        if(step === 'question-marks'){
                                            callCleaningController('clean-sp');
                                        }
                                        else if(step === 'clean-sp'){
                                            callCleaningController('clean-infra');
                                        }
                                        else if(step === 'clean-infra'){
                                            callCleaningController('clean-qualifier');
                                        }
                                        else if(step === 'clean-qualifier'){
                                            callCleaningController('double-spaces');
                                        }
                                        else if(step === 'double-spaces'){
                                            callCleaningController('leading-trailing-spaces');
                                        }
                                        else if(step === 'leading-trailing-spaces'){
                                            adjustUIEnd();
                                        }
                                    });
                                }
                                else{
                                    const text = getErrorResponseText(response.status, response.statusText);
                                    processErrorResponse(true, text);
                                }
                            })
                            .catch((err) => {});
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function callTaxThesaurusLinkController(step = '') {
                        if(selectedKingdomId.value){
                            abortController = new AbortController();
                            const formData = new FormData();
                            formData.append('collid', collId);
                            formData.append('kingdomid', selectedKingdomId.value);
                            if(!step){
                                adjustUIStart('updateWithTaxThesaurus');
                                const text = 'Updating linkages of occurrence records to the Taxonomic Thesaurus';
                                addProcessToProcessorDisplay(getNewProcessObject('updateOccThesaurusLinkages','single',text));
                                formData.append('action', 'updateOccThesaurusLinkages');
                            }
                            if(!processCancelling.value){
                                if(step === 'update-det-linkages'){
                                    const text = 'Updating linkages of associated determination records to the Taxonomic Thesaurus';
                                    addProcessToProcessorDisplay(getNewProcessObject('updateDetThesaurusLinkages','single',text));
                                    formData.append('action', 'updateDetThesaurusLinkages');
                                }
                                fetch(occurrenceTaxonomyApiUrl, {
                                    method: 'POST',
                                    signal: abortController.signal,
                                    body: formData
                                })
                                .then((response) => {
                                    if(response.status === 200){
                                        response.text().then((text) => {
                                            processSuccessResponse(true, 'Complete: ' + text + ' records updated');
                                            if(!step && updatedet.value){
                                                callTaxThesaurusLinkController('update-det-linkages');
                                            }
                                            else{
                                                adjustUIEnd();
                                            }
                                        });
                                    }
                                    else{
                                        const text = getErrorResponseText(response.status, response.statusText);
                                        processErrorResponse(true, text);
                                    }
                                })
                                .catch((err) => {});
                            }
                            else{
                                adjustUIEnd();
                            }
                        }
                        else{
                            showNotification('negative', 'Please select a Target Kingdom from the dropdown menu above.');
                        }
                    }

                    function cancelAPIRequest(){
                        abortController.abort();
                    }

                    function cancelProcess() {
                        processCancelling.value = true;
                        if(currentProcess.value === 'taxThesaurusFuzzyMatch' || !currentSciname.value){
                            cancelAPIRequest();
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

                    function clearSubprocesses(id) {
                        const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
                        parentProcObj['subs'] = [];
                        const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
                        if(dataParentProcObj){
                            dataParentProcObj['subs'] = [];
                        }
                    }

                    function getDataSourceName() {
                        if(dataSource.value === 'col'){
                            return 'Catalogue of Life';
                        }
                        else if(dataSource.value === 'itis'){
                            return 'Integrated Taxonomic Information System';
                        }
                        else if(dataSource.value === 'worms'){
                            return 'World Register of Marine Species';
                        }
                    }

                    function getITISNameSearchResultsHierarchy() {
                        let id;
                        if(nameSearchResults[0]['accepted']){
                            id = nameSearchResults[0]['id'];
                        }
                        else{
                            id = nameSearchResults[0]['accepted_id'];
                        }
                        const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/getFullHierarchyFromTSN?tsn=' + id;
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
                                    const resArr = resObj['hierarchyList'];
                                    const hierarchyArr = [];
                                    if(resArr && resArr.length > 0){
                                        let foundNameRank = nameSearchResults[0]['rankid'];
                                        if(!nameSearchResults[0]['accepted']){
                                            const acceptedObj = resArr.find(rettaxon => rettaxon['taxonName'] === nameSearchResults[0]['accepted_sciname']);
                                            foundNameRank = Number(rankArr[acceptedObj['rankName'].toLowerCase()]);
                                        }
                                        resArr.forEach((taxResult) => {
                                            if(taxResult['taxonName'] !== nameSearchResults[0]['sciname']){
                                                const rankname = taxResult['rankName'].toLowerCase();
                                                const rankid = Number(rankArr[rankname]);
                                                if(rankid <= foundNameRank && taxonomicRanks.includes(rankid)){
                                                    const resultObj = {};
                                                    resultObj['id'] = taxResult['tsn'];
                                                    resultObj['sciname'] = taxResult['taxonName'];
                                                    resultObj['author'] = taxResult['author'] ? taxResult['author'] : '';
                                                    resultObj['rankname'] = rankname;
                                                    resultObj['rankid'] = rankid;
                                                    if(rankname === 'family'){
                                                        nameSearchResults[0]['family'] = resultObj['sciname'];
                                                    }
                                                    hierarchyArr.push(resultObj);
                                                }
                                            }
                                        });
                                    }
                                    nameSearchResults[0]['hierarchy'] = hierarchyArr;
                                    processSuccessResponse(false);
                                    validateNameSearchResults();
                                });
                            }
                            else{
                                processErrorResponse(false, 'Unable to retrieve taxon hierarchy');
                                runScinameDataSourceSearch();
                            }
                        });
                    }

                    function getITISNameSearchResultsRecord() {
                        const id = nameSearchResults[0]['id'];
                        const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
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
                                    const taxonRankData = resObj['taxRank'];
                                    if(taxonRankData && taxonRankData.hasOwnProperty('rankName')){
                                        nameSearchResults[0]['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                                        nameSearchResults[0]['rankid'] = Number(taxonRankData['rankId']);
                                        const coreMetadata = resObj['coreMetadata'];
                                        const namestatus = coreMetadata['taxonUsageRating'];
                                        if(namestatus === 'accepted' || namestatus === 'valid'){
                                            nameSearchResults[0]['accepted'] = true;
                                            getITISNameSearchResultsHierarchy();
                                        }
                                        else{
                                            nameSearchResults[0]['accepted'] = false;
                                            const acceptedNameList = resObj['acceptedNameList'];
                                            const acceptedNameArr = acceptedNameList['acceptedNames'];
                                            if(acceptedNameArr.length > 0){
                                                const acceptedName = acceptedNameArr[0];
                                                nameSearchResults[0]['accepted_id'] = acceptedName['acceptedTsn'];
                                                nameSearchResults[0]['accepted_sciname'] = acceptedName['acceptedName'];
                                                getITISNameSearchResultsHierarchy();
                                            }
                                            else{
                                                processErrorResponse(false, 'Unable to distinguish taxon by name');
                                                runScinameDataSourceSearch();
                                            }
                                        }
                                    }
                                    else{
                                        processErrorResponse(false, 'Unable to distinguish taxon by name');
                                        runScinameDataSourceSearch();
                                    }
                                });
                            }
                            else{
                                processErrorResponse(false, 'Unable to retrieve taxon record');
                                runScinameDataSourceSearch();
                            }
                        });
                    }

                    function getNewProcessObject(id, type, text) {
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
                            undoOrigName: '',
                            undoChangedName: '',
                            changedTid: 0,
                            resultText: ''
                        };
                    }

                    function getWoRMSAddTaxonAuthor() {
                        if(!processCancelling.value){
                            const id = processingArr[0]['id'];
                            const url = 'https://www.marinespecies.org/rest/AphiaRecordByAphiaID/' + id;
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
                                        const currentTaxon = processingArr[0];
                                        currentTaxon['author'] = resObj['authority'] ? resObj['authority'] : '';
                                        taxaToAddArr.push(currentTaxon);
                                        processingArr.splice(0, 1);
                                        setTaxaToAdd();
                                    });
                                }
                                else{
                                    const currentTaxon = processingArr[0];
                                    taxaToAddArr.push(currentTaxon);
                                    processingArr.splice(0, 1);
                                    setTaxaToAdd();
                                }
                            });
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function getWoRMSNameSearchResultsHierarchy() {
                        let id;
                        if(nameSearchResults[0]['accepted']){
                            id = nameSearchResults[0]['id'];
                        }
                        else{
                            id = nameSearchResults[0]['accepted_id'];
                        }
                        const url = 'https://www.marinespecies.org/rest/AphiaClassificationByAphiaID/' + id;
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
                                    const hierarchyArr = [];
                                    const foundNameRank = nameSearchResults[0]['rankid'];
                                    let childObj = resObj['child'];
                                    if(childObj){
                                        const firstObj = {};
                                        const firstrankname = childObj['rank'].toLowerCase();
                                        const firstrankid = Number(rankArr[firstrankname]);
                                        const newTaxonAccepted = nameSearchResults[0]['accepted'];
                                        firstObj['id'] = childObj['AphiaID'];
                                        firstObj['sciname'] = childObj['scientificname'];
                                        firstObj['author'] = '';
                                        firstObj['rankname'] = firstrankname;
                                        firstObj['rankid'] = firstrankid;
                                        hierarchyArr.push(firstObj);
                                        let stopLoop = false;
                                        while((childObj = childObj['child']) && !stopLoop){
                                            if(childObj['scientificname'] !== nameSearchResults[0]['sciname']){
                                                const rankname = childObj['rank'].toLowerCase();
                                                const rankid = Number(rankArr[rankname]);
                                                if((newTaxonAccepted && rankid < foundNameRank && taxonomicRanks.includes(rankid)) || (!newTaxonAccepted && (childObj['scientificname'] === nameSearchResults[0]['accepted_sciname'] || taxonomicRanks.includes(rankid)))){
                                                    const resultObj = {};
                                                    resultObj['id'] = childObj['AphiaID'];
                                                    resultObj['sciname'] = childObj['scientificname'];
                                                    resultObj['author'] = '';
                                                    resultObj['rankname'] = rankname;
                                                    resultObj['rankid'] = rankid;
                                                    if(rankname === 'family'){
                                                        nameSearchResults[0]['family'] = resultObj['sciname'];
                                                    }
                                                    hierarchyArr.push(resultObj);
                                                }
                                                if((newTaxonAccepted && rankid === foundNameRank) || (!newTaxonAccepted && childObj['scientificname'] === nameSearchResults[0]['accepted_sciname'])){
                                                    stopLoop = true;
                                                }
                                            }
                                        }
                                        nameSearchResults[0]['hierarchy'] = hierarchyArr;
                                    }
                                    processSuccessResponse(false);
                                    validateNameSearchResults();
                                });
                            }
                            else{
                                processErrorResponse(false, 'Unable to retrieve taxon hierarchy');
                                runScinameDataSourceSearch();
                            }
                        });
                    }

                    function getWoRMSNameSearchResultsRecord(id) {
                        const url = 'https://www.marinespecies.org/rest/AphiaRecordByAphiaID/' + id;
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
                                    if(resObj.hasOwnProperty('kingdom') && resObj['kingdom'] && resObj.hasOwnProperty('scientificname') && resObj['scientificname'] && (resObj['kingdom'].toLowerCase() === selectedKingdomName.value.toLowerCase() || resObj['scientificname'].toLowerCase() === selectedKingdomName.value.toLowerCase())){
                                        const resultObj = {};
                                        resultObj['id'] = resObj['AphiaID'];
                                        resultObj['sciname'] = resObj['scientificname'];
                                        resultObj['author'] = resObj['authority'] ? resObj['authority'] : '';
                                        resultObj['rankname'] = resObj['rank'].toLowerCase();
                                        resultObj['rankid'] = Number(resObj['taxonRankID']);
                                        const namestatus = resObj['status'];
                                        if(namestatus === 'accepted'){
                                            resultObj['accepted'] = true;
                                        }
                                        else{
                                            resultObj['accepted'] = false;
                                            resultObj['accepted_id'] = resObj['valid_AphiaID'];
                                            resultObj['accepted_sciname'] = resObj['valid_name'];
                                        }
                                        nameSearchResults.push(resultObj);
                                        getWoRMSNameSearchResultsHierarchy();
                                    }
                                    else{
                                        processErrorResponse(false, 'Not found');
                                        runScinameDataSourceSearch();
                                    }
                                });
                            }
                            else{
                                processErrorResponse(false, 'Unable to retrieve taxon record');
                                runScinameDataSourceSearch();
                            }
                        });
                    }

                    function initializeCleanScinameAuthor() {
                        adjustUIStart('cleanScinameAuthor');
                        const text = 'Getting unlinked occurrence record scientific names';
                        addProcessToProcessorDisplay(getNewProcessObject('cleanScinameAuthor', 'multi', text));
                        abortController = new AbortController();
                        const formData = new FormData();
                        formData.append('collid', collId);
                        formData.append('action', 'getUnlinkedOccSciNames');
                        fetch(occurrenceTaxonomyApiUrl, {
                            method: 'POST',
                            signal: abortController.signal,
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((resObj) => {
                                    processSuccessResponse(true, 'Complete');
                                    unlinkedNamesArr = processUnlinkedNamesArr(resObj);
                                    runCleanScinameAuthorProcess();
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status, response.statusText);
                                processErrorResponse(true, text);
                            }
                        })
                        .catch((err) => {});
                    }

                    function initializeDataSourceSearch() {
                        if(selectedKingdomId.value){
                            nameTidIndex = Object.assign({}, {});
                            taxaLoaded = 0;
                            newTidArr.length = 0;
                            adjustUIStart('resolveFromTaxaDataSource');
                            const text = 'Setting rank data for processing search returns';
                            addProcessToProcessorDisplay(getNewProcessObject('resolveFromTaxaDataSource', 'multi', text));
                            const url = taxonomyApiUrl + '?action=getRankNameArr'
                            abortController = new AbortController();
                            fetch(url, {
                                signal: abortController.signal
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.json().then((resObj) => {
                                        processSuccessResponse(true, 'Complete');
                                        rankArr = Object.assign({}, resObj);
                                        setUnlinkedTaxaList();
                                    });
                                }
                                else{
                                    const text = getErrorResponseText(response.status, response.statusText);
                                    processErrorResponse(true, text);
                                }
                            });
                        }
                        else{
                            showNotification('negative', 'Please select a Target Kingdom from the dropdown menu above.');
                        }
                    }

                    function initializeTaxThesaurusFuzzyMatch() {
                        if(selectedKingdomId.value && levValue.value && Number(levValue.value) > 0){
                            adjustUIStart('taxThesaurusFuzzyMatch');
                            const text = 'Getting unlinked occurrence record scientific names';
                            addProcessToProcessorDisplay(getNewProcessObject('taxThesaurusFuzzyMatch', 'multi', text));
                            abortController = new AbortController();
                            const formData = new FormData();
                            formData.append('collid', collId);
                            formData.append('action', 'getUnlinkedOccSciNames');
                            fetch(occurrenceTaxonomyApiUrl, {
                                method: 'POST',
                                signal: abortController.signal,
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.json().then((resObj) => {
                                        processSuccessResponse(true,'Complete');
                                        unlinkedNamesArr = processUnlinkedNamesArr(resObj);
                                        runTaxThesaurusFuzzyMatchProcess();
                                    });
                                }
                                else{
                                    const text = getErrorResponseText(response.status, response.statusText);
                                    processErrorResponse(true, text);
                                }
                            })
                            .catch((err) => {});
                        }
                        else if(!selectedKingdomId.value){
                            showNotification('negative', 'Please select a Target Kingdom from the dropdown menu above.');
                        }
                        else{
                            showNotification('negative', 'Please select a character difference tolerance value greater than zero.');
                        }
                    }

                    function populateTaxonomicHierarchy() {
                        if(rebuildHierarchyLoop < 40){
                            const formData = new FormData();
                            formData.append('action', 'populateHierarchyTable');
                            fetch(taxonomyApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.text().then((res) => {
                                        if(Number(res) > 0){
                                            rebuildHierarchyLoop++;
                                            populateTaxonomicHierarchy();
                                        }
                                        else{
                                            processSuccessResponse(true, 'Complete');
                                            adjustUIEnd();
                                        }
                                    });
                                }
                                else{
                                    processErrorResponse(false, 'Error rebuilding the taxonomic hierarchy');
                                    adjustUIEnd();
                                }
                            });
                        }
                        else{
                            processErrorResponse(false, 'Error rebuilding the taxonomic hierarchy');
                            adjustUIEnd();
                        }
                    }

                    function primeTaxonomicHierarchy() {
                        rebuildHierarchyLoop = 0;
                        const text = 'Populating taxonomic hierarchy with new taxa';
                        addProcessToProcessorDisplay(getNewProcessObject('primeHierarchyTable', 'multi', text));
                        const formData = new FormData();
                        formData.append('tidarr', JSON.stringify(newTidArr));
                        formData.append('action', 'primeHierarchyTable');
                        fetch(taxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.text().then((res) => {
                                    if(Number(res) > 0){
                                        rebuildHierarchyLoop++;
                                        populateTaxonomicHierarchy();
                                    }
                                    else{
                                        adjustUIEnd();
                                    }
                                });
                            }
                            else{
                                processErrorResponse(false, 'Error rebuilding the taxonomic hierarchy');
                                adjustUIEnd();
                            }
                        });
                    }

                    function processAddTaxaArr() {
                        if(taxaToAddArr.length > 0){
                            const taxonToAdd = taxaToAddArr[0];
                            const text = 'Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus';
                            addSubprocessToProcessorDisplay(currentSciname.value, 'text', text);
                            const newTaxonObj = {};
                            newTaxonObj['sciname'] = taxonToAdd['sciname'];
                            newTaxonObj['author'] = taxonToAdd['author'];
                            newTaxonObj['kingdomid'] = selectedKingdomId.value;
                            newTaxonObj['rankid'] = taxonToAdd['rankid'];
                            newTaxonObj['acceptstatus'] = 1;
                            newTaxonObj['tidaccepted'] = '';
                            newTaxonObj['parenttid'] = nameTidIndex[taxonToAdd['parentName']];
                            newTaxonObj['family'] = taxonToAdd['family'];
                            newTaxonObj['source'] = getDataSourceName();
                            newTaxonObj['source-name'] = dataSource.value;
                            newTaxonObj['source-id'] = taxonToAdd['id'];
                            const formData = new FormData();
                            formData.append('taxon', JSON.stringify(newTaxonObj));
                            formData.append('action', 'addTaxon');
                            fetch(taxonomyApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(res && Number(res) > 0){
                                        const newTid = Number(res);
                                        nameTidIndex[taxaToAddArr[0]['sciname']] = newTid;
                                        newTidArr.push(newTid);
                                        taxaToAddArr.splice(0, 1);
                                        processSubprocessSuccessResponse(currentSciname.value, false);
                                        processAddTaxaArr();
                                    }
                                    else{
                                        processSubprocessErrorResponse(currentSciname.value, false, 'Error loading taxon');
                                        runScinameDataSourceSearch();
                                    }
                                });
                            });
                        }
                        else{
                            processAddTaxon();
                        }
                    }

                    function processAddTaxon() {
                        const taxonToAdd = nameSearchResults[0];
                        const text = 'Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus';
                        addSubprocessToProcessorDisplay(currentSciname.value, 'text', text);
                        if(nameTidIndex.hasOwnProperty(taxonToAdd['sciname'])){
                            processSubprocessSuccessResponse(currentSciname.value, false, nameSearchResults[0]['sciname'] + ' already added');
                            updateOccurrenceLinkages();
                        }
                        else{
                            const newTaxonObj = {};
                            newTaxonObj['sciname'] = taxonToAdd['sciname'];
                            newTaxonObj['author'] = taxonToAdd['author'];
                            newTaxonObj['kingdomid'] = selectedKingdomId.value;
                            newTaxonObj['rankid'] = taxonToAdd['rankid'];
                            newTaxonObj['acceptstatus'] = taxonToAdd['accepted'] ? 1 : 0;
                            newTaxonObj['tidaccepted'] = !taxonToAdd['accepted'] ? nameTidIndex[taxonToAdd['accepted_sciname']] : '';
                            newTaxonObj['parenttid'] = nameTidIndex[taxonToAdd['parentName']];
                            newTaxonObj['family'] = taxonToAdd['family'];
                            newTaxonObj['source'] = getDataSourceName();
                            newTaxonObj['source-name'] = dataSource.value;
                            newTaxonObj['source-id'] = taxonToAdd['id'];
                            const formData = new FormData();
                            formData.append('taxon', JSON.stringify(newTaxonObj));
                            formData.append('action', 'addTaxon');
                            fetch(taxonomyApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.text().then((res) => {
                                    if(res && Number(res) > 0){
                                        const newTid = Number(res);
                                        nameTidIndex[nameSearchResults[0]['sciname']] = newTid;
                                        newTidArr.push(newTid);
                                        processSubprocessSuccessResponse(currentSciname.value, false, 'Successfully added ' + nameSearchResults[0]['sciname']);
                                        if(currentSciname.value === nameSearchResults[0]['sciname']){
                                            updateOccurrenceLinkages();
                                        }
                                        else{
                                            const text = 'Updating occurrence records with cleaned scientific name';
                                            addSubprocessToProcessorDisplay(currentSciname.value, 'undo', text);
                                            changedCurrentSciname.value = currentSciname.value;
                                            changedParsedSciname.value = nameSearchResults[0]['sciname'];
                                            const formData = new FormData();
                                            formData.append('collid', collId);
                                            formData.append('sciname', currentSciname.value);
                                            formData.append('cleanedsciname', nameSearchResults[0]['sciname']);
                                            formData.append('tid', newTid.toString());
                                            formData.append('action', 'updateOccWithCleanedName');
                                            fetch(occurrenceTaxonomyApiUrl, {
                                                method: 'POST',
                                                body: formData
                                            })
                                            .then((response) => {
                                                if(response.status === 200){
                                                    response.text().then((resp) => {
                                                        setSubprocessUndoNames(currentSciname.value, changedCurrentSciname.value, changedParsedSciname.value);
                                                        processSubprocessSuccessResponse(currentSciname.value, false, (resp + ' records updated'));
                                                        updateOccurrenceLinkages();
                                                    });
                                                }
                                                else{
                                                    processSubprocessErrorResponse(currentSciname.value, true, 'Error updating occurrence records');
                                                    updateOccurrenceLinkages();
                                                }
                                            });
                                        }
                                    }
                                    else{
                                        processSubprocessErrorResponse(currentSciname.value, false, 'Error loading taxon');
                                        runScinameDataSourceSearch();
                                    }
                                });
                            });
                        }
                    }

                    function processErrorResponse(setCounts, text) {
                        const procObj = processorDisplayArr.find(proc => proc['current'] === true);
                        if(procObj){
                            procObj['current'] = false;
                            if(procObj['loading'] === true){
                                procObj['loading'] = false;
                                procObj['result'] = 'error';
                                procObj['resultText'] = text;
                            }
                        }
                        if(setCounts){
                            adjustUIEnd();
                        }
                    }

                    function processFuzzyMatches(fuzzyMatches) {
                        fuzzyMatches.forEach((match) => {
                            const fuzzyMatchName = match['sciname'];
                            const text = 'Match: ' + fuzzyMatchName;
                            addSubprocessToProcessorDisplay(currentSciname.value, 'fuzzy', text);
                            setSubprocessUndoNames(currentSciname.value, currentSciname.value, fuzzyMatchName,match['tid']);
                            processSubprocessSuccessResponse(currentSciname.value, false);
                        });
                        const text = 'skip';
                        addSubprocessToProcessorDisplay(currentSciname.value, 'fuzzy', text);
                        processSubprocessSuccessResponse(currentSciname.value, true);
                    }

                    function processGetCOLTaxonByScinameResponse(resObj) {
                        if(resObj['total'] > 0){
                            const resultArr = resObj['result'];
                            resultArr.forEach((taxResult) => {
                                const usageData = taxResult.hasOwnProperty('usage') ? taxResult['usage'] : null;
                                if(usageData){
                                    const status = usageData['status'];
                                    if(status !== 'common name' && usageData.hasOwnProperty('name')){
                                        const resultObj = {};
                                        resultObj['id'] = taxResult['id'];
                                        resultObj['author'] = usageData['name'].hasOwnProperty('authorship') ? usageData['name']['authorship'] : '';
                                        resultObj['sciname'] = usageData['name']['scientificName'];
                                        resultObj['rankname'] = usageData['name']['rank'].toLowerCase();
                                        resultObj['rankid'] = rankArr.hasOwnProperty(resultObj['rankname']) ? rankArr[resultObj['rankname']] : null;
                                        if(status === 'accepted'){
                                            resultObj['accepted'] = true;
                                        }
                                        else if(status === 'synonym'){
                                            const hierarchyArr = [];
                                            const resultHObj = {};
                                            const acceptedObj = usageData['accepted'];
                                            if(acceptedObj.hasOwnProperty('name')){
                                                resultObj['accepted'] = false;
                                                resultObj['accepted_id'] = acceptedObj['id'];
                                                resultHObj['id'] = acceptedObj['id'];
                                                resultHObj['author'] = acceptedObj['name'].hasOwnProperty('authorship') ? acceptedObj['name']['authorship'] : '';
                                                resultHObj['sciname'] = acceptedObj['name']['scientificName'];
                                                resultObj['accepted_sciname'] = resultHObj['sciname'];
                                                resultHObj['rankname'] = acceptedObj['name']['rank'].toLowerCase();
                                                resultHObj['rankid'] = rankArr.hasOwnProperty(resultHObj['rankname']) ? rankArr[resultHObj['rankname']] : null;
                                                hierarchyArr.push(resultHObj);
                                                resultObj['hierarchy'] = hierarchyArr;
                                            }
                                        }
                                        const existingObj = colInitialSearchResults.find(taxon => (taxon['sciname'] === resultObj['sciname'] && taxon['accepted_sciname'] === resultObj['accepted_sciname']));
                                        if(existingObj){
                                            if(Number(existingObj['rankid']) < Number(resultObj['rankid'])){
                                                const index = colInitialSearchResults.indexOf(existingObj);
                                                colInitialSearchResults.splice(index, 1);
                                                colInitialSearchResults.push(resultObj);
                                            }
                                        }
                                        else{
                                            colInitialSearchResults.push(resultObj);
                                        }
                                    }
                                }
                            });
                            if(colInitialSearchResults.length > 0){
                                validateCOLInitialNameSearchResults();
                            }
                            else{
                                processErrorResponse(false, 'Not found');
                                runScinameDataSourceSearch();
                            }
                        }
                        else{
                            processErrorResponse(false, 'Not found');
                            runScinameDataSourceSearch();
                        }
                    }

                    function processGetITISTaxonByScinameResponse(resObj) {
                        itisInitialSearchResults.length = 0;
                        const resultArr = resObj['scientificNames'];
                        if(resultArr && resultArr.length > 0 && resultArr[0]){
                            resultArr.forEach((taxResult) => {
                                if(taxResult['combinedName'] === currentSciname.value && (taxResult['kingdom'].toLowerCase() === selectedKingdomName.value.toLowerCase() || taxResult['combinedName'].toLowerCase() === selectedKingdomName.value.toLowerCase())){
                                    const resultObj = {};
                                    resultObj['id'] = taxResult['tsn'];
                                    resultObj['sciname'] = taxResult['combinedName'];
                                    resultObj['author'] = taxResult['author'];
                                    itisInitialSearchResults.push(resultObj);
                                }
                            });
                            if(itisInitialSearchResults.length === 1){
                                nameSearchResults = itisInitialSearchResults;
                                getITISNameSearchResultsRecord();
                            }
                            else if(itisInitialSearchResults.length === 0){
                                processErrorResponse(false, 'Not found');
                                runScinameDataSourceSearch();
                            }
                            else if(itisInitialSearchResults.length > 1){
                                validateITISInitialNameSearchResults();
                            }
                        }
                        else{
                            processErrorResponse(false, 'Not found');
                            runScinameDataSourceSearch();
                        }
                    }

                    function processingBatchLimitChange(value) {
                        if(value && (isNaN(value) || Number(value) <= 0)){
                            showNotification('negative', 'Processing batch limit must be a number greater than zero.');
                            processingLimit.value = null;
                        }
                    }

                    function processorDisplayScrollDown() {
                        scrollProcess = 'scrollDown';
                        processorDisplayCurrentIndex.value++;
                        const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
                        newData.forEach((data) => {
                            processorDisplayArr.push(data);
                        });
                        resetScrollProcess();
                    }

                    function processorDisplayScrollUp() {
                        scrollProcess = 'scrollUp';
                        processorDisplayCurrentIndex.value--;
                        const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
                        newData.forEach((data) => {
                            processorDisplayArr.push(data);
                        });
                        resetScrollProcess();
                    }

                    function processSubprocessErrorResponse(id, setCounts, text) {
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
                        const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
                        if(dataParentProcObj){
                            dataParentProcObj['current'] = false;
                            const dataSubProcObj = dataParentProcObj['subs'].find(subproc => subproc['loading'] === true);
                            if(dataSubProcObj){
                                dataSubProcObj['loading'] = false;
                                dataSubProcObj['result'] = 'error';
                                dataSubProcObj['resultText'] = text;
                            }
                        }
                        if(setCounts){
                            adjustUIEnd();
                        }
                    }

                    function processSubprocessSuccessResponse(id, complete, text = null) {
                        const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
                        if(parentProcObj){
                            parentProcObj['current'] = !complete;
                            const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                            if(subProcObj){
                                subProcObj['loading'] = false;
                                subProcObj['result'] = 'success';
                                subProcObj['resultText'] = text;
                            }
                        }
                        const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
                        if(dataParentProcObj){
                            dataParentProcObj['current'] = !complete;
                            const dataSubProcObj = dataParentProcObj['subs'].find(subproc => subproc['loading'] === true);
                            if(dataSubProcObj){
                                dataSubProcObj['loading'] = false;
                                dataSubProcObj['result'] = 'success';
                                dataSubProcObj['resultText'] = text;
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

                    function processUnlinkedNamesArr(inArr) {
                        if(Array.isArray(inArr) && inArr.length > 0){
                            if(processingStartIndex.value){
                                let nameArrLength = inArr.length;
                                let startIndexVal = null;
                                for(let i = 0 ; i < nameArrLength; i++) {
                                    if(inArr.hasOwnProperty(i) && inArr[i].toLowerCase() > processingStartIndex.value.toLowerCase()){
                                        startIndexVal = i;
                                        break;
                                    }
                                }
                                if(!startIndexVal){
                                    startIndexVal = nameArrLength;
                                }
                                inArr = inArr.splice(startIndexVal, (nameArrLength - startIndexVal));
                            }
                            if(processingLimit.value){
                                inArr = inArr.splice(0, processingLimit.value);
                            }
                        }
                        return inArr;
                    }

                    function resetScrollProcess() {
                        setTimeout(() => {
                            scrollProcess = null;
                        }, 200);
                    }

                    function runCleanScinameAuthorProcess() {
                        if(!processCancelling.value && unlinkedNamesArr.length > 0){
                            currentSciname.value = unlinkedNamesArr[0];
                            unlinkedNamesArr.splice(0, 1);
                            const text = 'Attempting to parse author name from: ' + currentSciname.value;
                            addProcessToProcessorDisplay(getNewProcessObject(currentSciname.value, 'multi', text));
                            const formData = new FormData();
                            formData.append('sciname', currentSciname.value);
                            formData.append('action', 'parseSciName');
                            fetch(taxonomyApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.json().then((parsedName) => {
                                        if(parsedName.hasOwnProperty('author') && parsedName['author'] !== ''){
                                            processSuccessResponse(false, 'Parsed author: ' + parsedName['author'] + '; Cleaned scientific name: ' + parsedName['sciname']);
                                            const text = 'Updating occurrence records with cleaned scientific name';
                                            addSubprocessToProcessorDisplay(currentSciname.value, 'undo', text);
                                            changedCurrentSciname.value = currentSciname.value;
                                            changedParsedSciname.value = parsedName['sciname'];
                                            const formData = new FormData();
                                            formData.append('collid', collId);
                                            formData.append('sciname', currentSciname.value);
                                            formData.append('cleanedsciname', parsedName['sciname']);
                                            formData.append('tid', null);
                                            formData.append('action', 'updateOccWithCleanedName');
                                            fetch(occurrenceTaxonomyApiUrl, {
                                                method: 'POST',
                                                body: formData
                                            })
                                            .then((response) => {
                                                if(response.status === 200){
                                                    response.text().then((res) => {
                                                        setSubprocessUndoNames(currentSciname.value, changedCurrentSciname.value, changedParsedSciname.value);
                                                        processSubprocessSuccessResponse(currentSciname.value, true, (res + ' records updated'));
                                                        runCleanScinameAuthorProcess();
                                                    });
                                                }
                                                else{
                                                    processSubprocessErrorResponse(currentSciname.value, false, 'Error updating occurrence records');
                                                    runCleanScinameAuthorProcess();
                                                }
                                            });
                                        }
                                        else{
                                            processErrorResponse(false, 'No author found in scientific name');
                                            runCleanScinameAuthorProcess();
                                        }
                                    });
                                }
                            });
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function runScinameDataSourceSearch() {
                        if(!processCancelling.value && unlinkedNamesArr.length > 0){
                            nameSearchResults.length = 0;
                            currentSciname.value = unlinkedNamesArr[0];
                            unlinkedNamesArr.splice(0, 1);
                            if(dataSource.value === 'col'){
                                colInitialSearchResults.length = 0;
                                const text = 'Searching the Catalogue of Life (COL) for ' + currentSciname.value;
                                addProcessToProcessorDisplay(getNewProcessObject(currentSciname.value, 'multi', text));
                                const url = 'https://api.checklistbank.org/dataset/3/nameusage/search?content=SCIENTIFIC_NAME&q=' + currentSciname.value + '&offset=0&limit=100';
                                const formData = new FormData();
                                formData.append('url', url);
                                formData.append('action', 'get');
                                fetch(proxyApiUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    if(response.status === 200){
                                        response.json().then((res) => {
                                            processGetCOLTaxonByScinameResponse(res);
                                        });
                                    }
                                    else{
                                        const text = getErrorResponseText(response.status, response.statusText);
                                        processErrorResponse(false, text);
                                        runScinameDataSourceSearch();
                                    }
                                });
                            }
                            else if(dataSource.value === 'itis'){
                                itisInitialSearchResults.length = 0;
                                const text = 'Searching the Integrated Taxonomic Information System (ITIS) for ' + currentSciname.value;
                                addProcessToProcessorDisplay(getNewProcessObject(currentSciname.value, 'multi', text));
                                const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/searchByScientificName?srchKey=' + currentSciname.value;
                                const formData = new FormData();
                                formData.append('url', url);
                                formData.append('action', 'get');
                                fetch(proxyApiUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    if(response.status === 200){
                                        response.json().then((res) => {
                                            processGetITISTaxonByScinameResponse(res);
                                        });
                                    }
                                    else{
                                        const text = getErrorResponseText(response.status, response.statusText);
                                        processErrorResponse(false, text);
                                        runScinameDataSourceSearch();
                                    }
                                });
                            }
                            else if(dataSource.value === 'worms'){
                                const text = 'Searching the World Register of Marine Species (WoRMS) for ' + currentSciname.value;
                                addProcessToProcessorDisplay(getNewProcessObject(currentSciname.value, 'multi', text));
                                const url = 'https://www.marinespecies.org/rest/AphiaIDByName/' + currentSciname.value + '?marine_only=false';
                                const formData = new FormData();
                                formData.append('url', url);
                                formData.append('action', 'get');
                                fetch(proxyApiUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    if(response.status === 200){
                                        response.text().then((res) => {
                                            if(res && Number(res) > 0){
                                                getWoRMSNameSearchResultsRecord(res);
                                            }
                                            else{
                                                processErrorResponse(false, 'Not found');
                                                runScinameDataSourceSearch();
                                            }
                                        });
                                    }
                                    else if(response.status === 204){
                                        processErrorResponse(false, 'Not found');
                                        runScinameDataSourceSearch();
                                    }
                                    else{
                                        const text = getErrorResponseText(response.status, response.statusText);
                                        processErrorResponse(false, text);
                                        runScinameDataSourceSearch();
                                    }
                                });
                            }
                        }
                        else if(newTidArr.length > 0){
                            primeTaxonomicHierarchy();
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function runTaxThesaurusFuzzyMatchProcess() {
                        changedCurrentSciname.value = '';
                        changedParsedSciname.value = '';
                        if(!processCancelling.value && unlinkedNamesArr.length > 0){
                            currentSciname.value = unlinkedNamesArr[0];
                            unlinkedNamesArr.splice(0, 1);
                            const text = 'Finding fuzzy matches for ' + currentSciname.value;
                            addProcessToProcessorDisplay(getNewProcessObject(currentSciname.value, 'multi', text));
                            const formData = new FormData();
                            formData.append('sciname', currentSciname.value);
                            formData.append('lev', levValue.value);
                            formData.append('action', 'getSciNameFuzzyMatches');
                            fetch(taxonomyApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.json().then((fuzzyMatches) => {
                                        if(fuzzyMatches.length > 0){
                                            processSuccessResponse(false);
                                            processFuzzyMatches(fuzzyMatches);
                                        }
                                        else{
                                            processErrorResponse(false, 'No fuzzy matches found');
                                            runTaxThesaurusFuzzyMatchProcess();
                                        }
                                    });
                                }
                            });
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function selectFuzzyMatch(sciName, newName, newtid) {
                        changedCurrentSciname.value = sciName;
                        changedParsedSciname.value = newName;
                        clearSubprocesses(currentSciname.value);
                        const text = 'Updating occurrence records with selected scientific name';
                        addSubprocessToProcessorDisplay(currentSciname.value, 'undo', text);
                        const formData = new FormData();
                        formData.append('collid', collId);
                        formData.append('sciname', sciName);
                        formData.append('cleanedsciname', newName);
                        formData.append('tid', newtid);
                        formData.append('action', 'updateOccWithCleanedName');
                        fetch(occurrenceTaxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.text().then((res) => {
                                    setSubprocessUndoNames(currentSciname.value, changedCurrentSciname.value, changedParsedSciname.value);
                                    processSubprocessSuccessResponse(currentSciname.value, true, (res + ' records updated'));
                                    runTaxThesaurusFuzzyMatchProcess();
                                });
                            }
                            else{
                                processSubprocessErrorResponse(currentSciname.value, false, 'Error updating occurrence records');
                                runTaxThesaurusFuzzyMatchProcess();
                            }
                        });
                    }

                    function setCollInfo() {
                        if(collId){
                            const formData = new FormData();
                            formData.append('collid', collId);
                            formData.append('action', 'getCollectionInfoArr');
                            fetch(collectionApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                response.json().then((resObj) => {
                                    collInfo.value = resObj;
                                });
                            });
                        }
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'CollAdmin');
                        formData.append('key', collId);
                        formData.append('action', 'validatePermission');
                        fetch(profileApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.text().then((res) => {
                                isEditor.value = Number(res) === 1;
                            });
                        });
                    }

                    function setScroller(info) {
                        if((currentProcess.value || scrollProcess) && info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                            procDisplayScrollHeight.value = info.verticalSize;
                            if(scrollProcess === 'scrollDown'){
                                procDisplayScrollAreaRef.value.setScrollPosition('vertical', 0);
                            }
                            else{
                                procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                            }
                        }
                    }

                    function setSubprocessUndoNames(id, origName, newName, tid = null) {
                        const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
                        const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                        subProcObj['undoOrigName'] = origName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
                        subProcObj['undoChangedName'] = newName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
                        if(tid){
                            subProcObj['changedTid'] = tid;
                        }
                        const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
                        if(dataParentProcObj){
                            const dataSubProcObj = dataParentProcObj['subs'].find(subproc => subproc['loading'] === true);
                            dataSubProcObj['undoOrigName'] = origName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
                            dataSubProcObj['undoChangedName'] = newName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
                            if(tid){
                                dataSubProcObj['changedTid'] = tid;
                            }
                        }
                    }

                    function setTaxaToAdd() {
                        if(processingArr.length > 0){
                            const sciname = processingArr[0]['sciname'];
                            const rankid = processingArr[0]['rankid'];
                            if(!nameTidIndex.hasOwnProperty(sciname)){
                                const formData = new FormData();
                                formData.append('sciname', sciname);
                                formData.append('rankid', rankid);
                                formData.append('kingdomid', selectedKingdomId.value);
                                formData.append('action', 'getTid');
                                fetch(taxonomyApiUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then((response) => {
                                    if(response.status === 200){
                                        response.text().then((res) => {
                                            if(dataSource.value === 'worms' && Number(res) === 0){
                                                getWoRMSAddTaxonAuthor();
                                            }
                                            else{
                                                const currentTaxon = processingArr[0];
                                                if(Number(res) > 0){
                                                    nameTidIndex[currentTaxon['sciname']] = Number(res);
                                                }
                                                else{
                                                    taxaToAddArr.push(currentTaxon);
                                                }
                                                processingArr.splice(0, 1);
                                                setTaxaToAdd();
                                            }
                                        });
                                    }
                                });
                            }
                            else{
                                processingArr.splice(0, 1);
                                setTaxaToAdd();
                            }
                        }
                        else{
                            processSubprocessSuccessResponse(currentSciname.value, false);
                            processAddTaxaArr();
                        }
                    }

                    function setUnlinkedRecordCounts() {
                        unlinkedOccCnt.value = 0;
                        unlinkedTaxaCnt.value = 0;
                        unlinkedLoading.value = true;
                        const formData = new FormData();
                        formData.append('collid', collId);
                        formData.append('action', 'getUnlinkedScinameCounts');
                        fetch(occurrenceTaxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => response.json())
                        .then((result) => {
                            if(result.hasOwnProperty('occCnt')){
                                unlinkedOccCnt.value = Number(result['occCnt']);
                            }
                            if(result.hasOwnProperty('taxaCnt')){
                                unlinkedTaxaCnt.value = Number(result['taxaCnt']);
                            }
                            unlinkedLoading.value = false;
                        });
                    }

                    function setUnlinkedTaxaList() {
                        if(!processCancelling.value){
                            const text = 'Getting unlinked occurrence record scientific names';
                            addProcessToProcessorDisplay(getNewProcessObject('getUnlinkedOccSciNames', 'multi', text));
                            abortController = new AbortController();
                            const formData = new FormData();
                            formData.append('collid', collId);
                            formData.append('action', 'getUnlinkedOccSciNames');
                            fetch(occurrenceTaxonomyApiUrl, {
                                method: 'POST',
                                signal: abortController.signal,
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.json().then((resObj) => {
                                        processSuccessResponse(true,'Complete');
                                        unlinkedNamesArr = processUnlinkedNamesArr(resObj);
                                        runScinameDataSourceSearch();
                                    });
                                }
                                else{
                                    const text = getErrorResponseText(response.status, response.statusText);
                                    processErrorResponse(true, text);
                                }
                            })
                            .catch((err) => {});
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function undoChangedSciname(id, oldName, newName) {
                        const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
                        const subProcObj = parentProcObj['subs'].find(subproc => subproc['undoChangedName'] === newName);
                        subProcObj['type'] = 'text';
                        const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
                        if(dataParentProcObj){
                            const dataSubProcObj = dataParentProcObj['subs'].find(subproc => subproc['undoChangedName'] === newName);
                            dataSubProcObj['type'] = 'text';
                        }
                        const text = 'Reverting scientific name change from ' + oldName.replaceAll('%squot;',"'").replaceAll('%dquot;','"') + ' to ' + newName.replaceAll('%squot;',"'").replaceAll('%dquot;','"');
                        addSubprocessToProcessorDisplay(id, 'text', text);
                        undoId.value = id;
                        const formData = new FormData();
                        formData.append('collid', collId);
                        formData.append('oldsciname', oldName);
                        formData.append('newsciname', newName);
                        formData.append('action', 'undoOccScinameChange');
                        fetch(occurrenceTaxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.text().then((res) => {
                                    processSubprocessSuccessResponse(undoId.value, true, (res + ' records reverted'));
                                });
                            }
                            else{
                                processSubprocessErrorResponse(undoId.value, false, 'Error undoing name change');
                            }
                        });
                    }

                    function updateOccLocalitySecurity() {
                        adjustUIStart('updateOccLocalitySecurity');
                        const text = 'Updating the locality security settings for occurrence records of protected species';
                        addProcessToProcessorDisplay(getNewProcessObject('updateLocalitySecurity', 'single', text));
                        abortController = new AbortController();
                        const formData = new FormData();
                        formData.append('collid', collId);
                        formData.append('action', 'updateLocalitySecurity');
                        fetch(occurrenceTaxonomyApiUrl, {
                            method: 'POST',
                            signal: abortController.signal,
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.text().then((res) => {
                                    processSuccessResponse(true, 'Complete: ' + res + ' records updated');
                                    adjustUIEnd();
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status, response.statusText);
                                processErrorResponse(true, text);
                            }
                        })
                        .catch((err) => {});
                    }

                    function updateOccurrenceLinkages() {
                        const newSciname = nameSearchResults[0]['sciname'];
                        const newScinameTid = nameTidIndex[nameSearchResults[0]['sciname']];
                        const text = 'Updating linkages of occurrence records to ' + newSciname;
                        addSubprocessToProcessorDisplay(currentSciname.value, 'text', text);
                        const formData = new FormData();
                        formData.append('collid', collId);
                        formData.append('sciname', newSciname);
                        formData.append('tid', newScinameTid);
                        formData.append('kingdomid', selectedKingdomId.value);
                        formData.append('action', 'updateOccWithNewSciname');
                        fetch(occurrenceTaxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.text().then((res) => {
                                    processSubprocessSuccessResponse(currentSciname.value, true, res + ' records updated');
                                    taxaLoaded++;
                                    if(taxaLoaded > 30){
                                        setUnlinkedRecordCounts();
                                        taxaLoaded = 0;
                                    }
                                    runScinameDataSourceSearch();
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status, response.statusText);
                                processSubprocessErrorResponse(currentSciname.value, true, text);
                                taxaLoaded++;
                                if(taxaLoaded > 30){
                                    setUnlinkedRecordCounts();
                                    taxaLoaded = 0;
                                }
                                runScinameDataSourceSearch();
                            }
                        });
                    }

                    function updateSelectedDataSource(dataSourceObj) {
                        dataSource.value = dataSourceObj;
                    }

                    function updateSelectedKingdom(kingdomObj) {
                        selectedKingdom.value = kingdomObj;
                        selectedKingdomId.value = kingdomObj.id;
                        selectedKingdomName.value = kingdomObj.name;
                    }

                    function validateCOLInitialNameSearchResults() {
                        if(colInitialSearchResults.length > 0){
                            let id;
                            const taxon = colInitialSearchResults[0];
                            colInitialSearchResults.splice(0, 1);
                            if(taxon['accepted']){
                                id = taxon['id'];
                            }
                            else{
                                id = taxon['accepted_id'];
                            }
                            const url = 'https://api.catalogueoflife.org/dataset/3/taxon/' + id + '/classification';
                            const formData = new FormData();
                            formData.append('url', url);
                            formData.append('action', 'get');
                            fetch(proxyApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.json().then((resArr) => {
                                        if(resArr){
                                            let kingdomName = '';
                                            if(taxon['rankname'].toLowerCase() === 'kingdom'){
                                                kingdomName = taxon['sciname'];
                                            }
                                            else{
                                                const kingdomObj = resArr.find(rettaxon => rettaxon['rank'].toLowerCase() === 'kingdom');
                                                if(kingdomObj){
                                                    kingdomName = kingdomObj['name'];
                                                }
                                            }
                                            if(kingdomName.toLowerCase() === selectedKingdomName.value.toLowerCase()){
                                                let hierarchyArr = [];
                                                if(taxon.hasOwnProperty('hierarchy')){
                                                    hierarchyArr = taxon['hierarchy'];
                                                }
                                                resArr.forEach((taxResult) => {
                                                    if(taxResult['name'] !== taxon['sciname']){
                                                        const rankname = taxResult['rank'].toLowerCase();
                                                        const rankid = Number(rankArr[rankname]);
                                                        if(taxonomicRanks.includes(rankid) || (!taxon['accepted'] && taxon['accepted_sciname'] === taxResult['name'])){
                                                            const resultObj = {};
                                                            resultObj['id'] = taxResult['id'];
                                                            resultObj['sciname'] = taxResult['name'];
                                                            resultObj['author'] = taxResult.hasOwnProperty('authorship') ? taxResult['authorship'] : '';
                                                            resultObj['rankname'] = rankname;
                                                            resultObj['rankid'] = rankid;
                                                            if(rankname === 'family'){
                                                                taxon['family'] = resultObj['sciname'];
                                                            }
                                                            hierarchyArr.push(resultObj);
                                                        }
                                                    }
                                                });
                                                taxon['hierarchy'] = hierarchyArr;
                                                nameSearchResults.push(taxon);
                                            }
                                        }
                                        validateCOLInitialNameSearchResults();
                                    });
                                }
                                else{
                                    validateCOLInitialNameSearchResults();
                                }
                            });
                        }
                        else if(nameSearchResults.length === 1){
                            processSuccessResponse(false);
                            validateNameSearchResults();
                        }
                        else if(nameSearchResults.length === 0){
                            processErrorResponse(false, 'Not found');
                            runScinameDataSourceSearch();
                        }
                        else if(nameSearchResults.length > 1){
                            processErrorResponse(false, 'Unable to distinguish taxon by name');
                            runScinameDataSourceSearch();
                        }
                    }

                    function validateITISInitialNameSearchResults() {
                        if(itisInitialSearchResults.length > 0){
                            const taxon = itisInitialSearchResults[0];
                            itisInitialSearchResults.splice(0, 1);
                            const id = taxon['id'];
                            const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
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
                                        const coreMetadata = resObj['coreMetadata'];
                                        if(coreMetadata){
                                            const namestatus = coreMetadata['taxonUsageRating'];
                                            if(namestatus === 'accepted'){
                                                const taxonRankData = resObj['taxRank'];
                                                taxon['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                                                taxon['rankid'] = Number(taxonRankData['rankId']);
                                                taxon['accepted'] = true;
                                                const existingObj = nameSearchResults.find(nSTaxon => (nSTaxon['sciname'] === taxon['sciname']));
                                                if(existingObj){
                                                    if(Number(existingObj['rankid']) < Number(taxon['rankid'])){
                                                        const index = nameSearchResults.indexOf(existingObj);
                                                        nameSearchResults.splice(index, 1);
                                                        nameSearchResults.push(taxon);
                                                    }
                                                }
                                                else{
                                                    nameSearchResults.push(taxon);
                                                }
                                            }
                                        }
                                        validateITISInitialNameSearchResults();
                                    });
                                }
                                else{
                                    processErrorResponse(false, 'Unable to retrieve taxon record');
                                    runScinameDataSourceSearch();
                                }
                            });
                        }
                        else if(nameSearchResults.length === 1){
                            getITISNameSearchResultsHierarchy();
                        }
                        else if(nameSearchResults.length === 0){
                            processErrorResponse(false, 'Not found');
                            runScinameDataSourceSearch();
                        }
                        else if(nameSearchResults.length > 1){
                            processErrorResponse(false, 'Unable to distinguish taxon by name');
                            runScinameDataSourceSearch();
                        }
                    }

                    function validateNameSearchResults() {
                        processingArr = [];
                        taxaToAddArr = [];
                        if(nameSearchResults.length === 1){
                            if(!nameSearchResults[0]['accepted'] && !nameSearchResults[0]['accepted_sciname']){
                                processErrorResponse(false, 'Unable to distinguish accepted name');
                                runScinameDataSourceSearch();
                            }
                            else if(nameSearchResults[0]['hierarchy'].length > 0){
                                const addHierchyTemp = nameSearchResults[0]['hierarchy'];
                                addHierchyTemp.sort((a, b) => {
                                    return a.rankid - b.rankid;
                                });
                                let parentName = addHierchyTemp[0]['sciname'];
                                addHierchyTemp.forEach((taxon) => {
                                    if(taxon['sciname'] !== parentName){
                                        taxon['parentName'] = parentName;
                                        taxon['family'] = taxon['rankid'] >= 140 ? nameSearchResults[0]['family'] : null;
                                        parentName = taxon['sciname'];
                                        if(!nameSearchResults[0]['accepted'] && taxon['sciname'] === nameSearchResults[0]['accepted_sciname']){
                                            nameSearchResults[0]['parentName'] = taxon['parentName'];
                                        }
                                    }
                                });
                                if(!nameSearchResults[0].hasOwnProperty('parentName') || nameSearchResults[0]['parentName'] === ''){
                                    nameSearchResults[0]['parentName'] = parentName;
                                }
                                processingArr = addHierchyTemp;
                                const text = 'Matching parent and accepted taxa to the Taxonomic Thesaurus';
                                addSubprocessToProcessorDisplay(currentSciname.value, 'text', text);
                                setTaxaToAdd();
                            }
                            else{
                                processErrorResponse(false, 'Unable to distinguish taxon by name');
                                runScinameDataSourceSearch();
                            }
                        }
                        else{
                            processErrorResponse(false, 'Unable to distinguish taxon by name');
                            runScinameDataSourceSearch();
                        }
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        setCollInfo();
                        setUnlinkedRecordCounts();
                    });
                    
                    return {
                        collId,
                        collInfo,
                        currentProcess,
                        currentSciname,
                        dataSource,
                        isEditor,
                        levValue,
                        procDisplayScrollAreaRef,
                        processCancelling,
                        processingLimit,
                        processingStartIndex,
                        processorDisplayArr,
                        processorDisplayCurrentIndex,
                        processorDisplayIndex,
                        selectedKingdom,
                        undoButtonsDisabled,
                        unlinkedLoading,
                        unlinkedOccCnt,
                        unlinkedTaxaCnt,
                        updatedet,
                        uppercontrolsdisabled,
                        callCleaningController,
                        callTaxThesaurusLinkController,
                        cancelProcess,
                        initializeCleanScinameAuthor,
                        initializeDataSourceSearch,
                        initializeTaxThesaurusFuzzyMatch,
                        openTutorialWindow,
                        processingBatchLimitChange,
                        processorDisplayScrollDown,
                        processorDisplayScrollUp,
                        selectFuzzyMatch,
                        setScroller,
                        undoChangedSciname,
                        updateOccLocalitySecurity,
                        updateSelectedDataSource,
                        updateSelectedKingdom
                    }
                }
            });
            occurrenceTaxonomyManagementModule.use(Quasar, { config: {} });
            occurrenceTaxonomyManagementModule.use(Pinia.createPinia());
            occurrenceTaxonomyManagementModule.mount('#innertext');
        </script>
    </body>
</html>
