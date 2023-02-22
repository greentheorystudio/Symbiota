<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceTaxonomyCleaner.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;

$cleanManager = new OccurrenceTaxonomyCleaner();
$cleanManager->setCollId($collid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = true;
}
$collMap = $cleanManager->getCollMap();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Management Module</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <style>
        .header-block {
            display: flex;
            justify-content: space-between;
            margin: 0 30px 8px;
        }
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
        .accordion-panel {
            max-height: 532px;
            overflow: auto;
        }
        .process-header {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .fuzzy-match-row {
            display: flex;
            justify-content: space-between;
        }
    </style>
    <script>
        const collId = <?php echo $collid; ?>;
    </script>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    ?>
    <div class='navpath'>
        <a href="../../index.php">Home</a> &gt;&gt;
        <?php
        if($collid && is_numeric($collid)){
            ?>
            <a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
            <?php
        }
        ?>
        <b>Taxonomy Management Module</b>
    </div>
    <div id="innertext">
        <?php
        if($collid && $isEditor){
            ?>
            <div class="header-block">
                <div class="text-weight-bold">
                    <?php echo $collMap[(int)$collid]['collectionname'].($collMap[(int)$collid]['code']?' ('.$collMap[(int)$collid]['code'].')':''); ?>
                </div>
                <div onclick="openTutorialWindow('/tutorial/collections/management/taxonomy/index.php?collid=<?php echo $collid; ?>');" title="Open Tutorial Window">
                    <q-icon name="far fa-question-circle" size="20px" class="cursor-pointer" />
                </div>
            </div>
            <div class="header-block">
                <div class="text-weight-bold">
                    <div class="q-mt-xs">
                        <taxa-kingdom-selector :disable="uppercontrolsdisabled" :selected-kingdom="selectedKingdom" label="Target Kingdom" @update:selected-kingdom="updateSelectedKingdom"></taxa-kingdom-selector>
                    </div>
                    <div class="q-mt-xs">
                        <q-input outlined v-model="processingStartIndex" label="Processing Start Index" style="width:250px;" :readonly="uppercontrolsdisabled" dense />
                    </div>
                    <div class="q-mt-xs">
                        <q-input type="number" outlined v-model="processingLimit" label="Processing Batch Limit" style="width:175px;" @update:model-value="processingBatchLimitChange" :readonly="uppercontrolsdisabled" dense />
                    </div>
                </div>
                <div class="text-weight-bold">
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
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currentProcess === 'cleanProcesses'" :disabled="currentProcess && currentProcess !== 'cleanProcesses'" color="secondary" @click="callCleaningController('question-marks');" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currentProcess === 'cleanProcesses'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                        <div class="process-header">
                                            Scientific Name Authorship Cleaning
                                        </div>
                                        Run a cleaning process to remove the scientific name authors from occurrence record scientific
                                        names that are not linked to the Taxonomic Thesaurus.
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currentProcess === 'cleanScinameAuthor'" :disabled="currentProcess && currentProcess !== 'cleanScinameAuthor'" color="secondary" @click="initializeCleanScinameAuthor();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currentProcess === 'cleanScinameAuthor'" color="red" @click="cancelProcess();" label="Cancel" dense />
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
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currentProcess === 'updateWithTaxThesaurus'" :disabled="currentProcess && currentProcess !== 'updateWithTaxThesaurus'" color="secondary" @click="callTaxThesaurusLinkController();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currentProcess === 'updateWithTaxThesaurus'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                        <div class="process-header">
                                            Update Locality Security Settings
                                        </div>
                                        Update locality security settings for occurrence records of protected species.
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currentProcess === 'updateOccLocalitySecurity'" :disabled="currentProcess && currentProcess !== 'updateOccLocalitySecurity'" color="secondary" @click="updateOccLocalitySecurity();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currentProcess === 'updateOccLocalitySecurity'" color="red" @click="cancelProcess();" label="Cancel" dense />
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
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currentProcess === 'resolveFromTaxaDataSource'" :disabled="currentProcess && currentProcess !== 'resolveFromTaxaDataSource'" color="secondary" @click="initializeDataSourceSearch();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currentProcess === 'resolveFromTaxaDataSource'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                        <div class="process-header">
                                            Taxonomic Thesaurus Fuzzy Search
                                        </div>
                                        Get fuzzy matches to occurrence record scientific names that are not yet linked to the Taxonomic Thesaurus
                                        with taxa currently in the Taxonomic Thesaurus.
                                        <div class="q-mt-xs">
                                            <q-input type="number" outlined v-model="levValue" style="width:225px;" label="Character difference tolerance" :readonly="uppercontrolsdisabled" dense />
                                        </div>
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currentProcess === 'taxThesaurusFuzzyMatch'" :disabled="currentProcess && currentProcess !== 'taxThesaurusFuzzyMatch'" color="secondary" @click="initializeTaxThesaurusFuzzyMatch();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currentProcess === 'taxThesaurusFuzzyMatch'" color="red" @click="cancelProcess();" label="Cancel" dense />
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
                            </q-list>
                        </q-scroll-area>
                    </q-card>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    include(__DIR__ . '/../../footer.php');
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaKingdomSelector.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceBulletSelector.js" type="text/javascript"></script>
    <script>
        const occurrenceTaxonomyManagementModule = Vue.createApp({
            data() {
                return {
                    changedCurrentSciname: Vue.ref(''),
                    changedParsedSciname: Vue.ref(''),
                    colInitialSearchResults: Vue.ref([]),
                    currentProcess: Vue.ref(null),
                    currentSciname: Vue.ref(null),
                    dataSource: Vue.ref('col'),
                    itisInitialSearchResults: Vue.ref([]),
                    levValue: Vue.ref('2'),
                    nameSearchResults: Vue.ref([]),
                    nameTidIndex: Vue.ref({}),
                    newTidArr: Vue.ref([]),
                    processCancelled: Vue.ref(false),
                    processingArr: Vue.ref([]),
                    processingLimit: Vue.ref(null),
                    processingStartIndex: Vue.ref(null),
                    processorDisplayArr: Vue.ref([]),
                    rankArr: Vue.ref(null),
                    rebuildHierarchyLoop: Vue.ref(0),
                    selectedKingdom: Vue.ref(null),
                    selectedKingdomId: Vue.ref(null),
                    selectedKingdomName: Vue.ref(null),
                    taxaLoaded: Vue.ref(0),
                    taxaToAddArr: Vue.ref([]),
                    undoButtonsDisabled: Vue.ref(true),
                    undoId: Vue.ref(''),
                    unlinkedLoading: Vue.ref(false),
                    unlinkedNamesArr: Vue.ref([]),
                    unlinkedOccCnt: Vue.ref(null),
                    unlinkedTaxaCnt: Vue.ref(null),
                    updatedet: Vue.ref(false),
                    uppercontrolsdisabled: Vue.ref(false)
                }
            },
            components: {
                'taxa-kingdom-selector': taxaKingdomSelector,
                'taxonomy-data-source-bullet-selector': taxonomyDataSourceBulletSelector
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
                this.setUnlinkedRecordCounts();
            },
            methods: {
                addSubprocessToProcessorDisplay(id,type,text){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    parentProcObj['subs'].push(this.getNewSubprocessObject(this.currentSciname,type,text));
                },
                adjustUIEnd(){
                    this.unlinkedNamesArr = [];
                    this.currentSciname = null;
                    this.setUnlinkedRecordCounts();
                },
                adjustUIStart(id){
                    this.processorDisplayArr = [];
                    this.currentProcess = id;
                    this.uppercontrolsdisabled = true;
                    this.undoButtonsDisabled = true;
                },
                callCleaningController(step){
                    abortController = new AbortController();
                    const formData = new FormData();
                    formData.append('collid', collId);
                    if(step === 'question-marks'){
                        this.processCancelled = false;
                        this.adjustUIStart('cleanProcesses');
                        const text = 'Cleaning question marks from scientific names';
                        this.processorDisplayArr.push(this.getNewProcessObject('cleanQuestionMarks','single',text));
                        formData.append('action', 'cleanQuestionMarks');
                    }
                    if(!this.processCancelled){
                        if(step === 'clean-sp'){
                            const text = 'Cleaning scientific names ending in sp., sp. nov., spp., or group';
                            this.processorDisplayArr.push(this.getNewProcessObject('cleanSpNames','single',text));
                            formData.append('action', 'cleanSpNames');
                        }
                        else if(step === 'clean-infra'){
                            const text = 'Normalizing infraspecific rank abbreviations';
                            this.processorDisplayArr.push(this.getNewProcessObject('cleanInfra','single',text));
                            formData.append('action', 'cleanInfra');
                        }
                        else if(step === 'clean-qualifier'){
                            const text = 'Cleaning scientific names containing cf. or aff.';
                            this.processorDisplayArr.push(this.getNewProcessObject('cleanQualifierNames','single',text));
                            formData.append('action', 'cleanQualifierNames');
                        }
                        else if(step === 'double-spaces'){
                            const text = 'Cleaning scientific names containing double spaces';
                            this.processorDisplayArr.push(this.getNewProcessObject('cleanDoubleSpaces','single',text));
                            formData.append('action', 'cleanDoubleSpaces');
                        }
                        else if(step === 'leading-trailing-spaces'){
                            const text = 'Cleaning leading and trailing spaces in scientific names';
                            this.processorDisplayArr.push(this.getNewProcessObject('cleanTrimNames','single',text));
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
                                    this.processSuccessResponse(true,'Complete: ' + text + ' records cleaned');
                                    if(step === 'question-marks'){
                                        this.callCleaningController('clean-sp');
                                    }
                                    else if(step === 'clean-sp'){
                                        this.callCleaningController('clean-infra');
                                    }
                                    else if(step === 'clean-infra'){
                                        this.callCleaningController('clean-qualifier');
                                    }
                                    else if(step === 'clean-qualifier'){
                                        this.callCleaningController('double-spaces');
                                    }
                                    else if(step === 'double-spaces'){
                                        this.callCleaningController('leading-trailing-spaces');
                                    }
                                    else if(step === 'leading-trailing-spaces'){
                                        this.adjustUIEnd();
                                    }
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                this.processErrorResponse(true,text);
                            }
                        })
                        .catch((err) => {});
                    }
                },
                callTaxThesaurusLinkController(step = ''){
                    if(this.selectedKingdomId){
                        abortController = new AbortController();
                        const formData = new FormData();
                        formData.append('collid', collId);
                        formData.append('kingdomid', this.selectedKingdomId);
                        if(!step){
                            this.processCancelled = false;
                            this.adjustUIStart('updateWithTaxThesaurus');
                            const text = 'Updating linkages of occurrence records to the Taxonomic Thesaurus';
                            this.processorDisplayArr.push(this.getNewProcessObject('updateOccThesaurusLinkages','single',text));
                            formData.append('action', 'updateOccThesaurusLinkages');
                        }
                        if(!this.processCancelled){
                            if(step === 'update-det-linkages'){
                                const text = 'Updating linkages of associated determination records to the Taxonomic Thesaurus';
                                this.processorDisplayArr.push(this.getNewProcessObject('updateDetThesaurusLinkages','single',text));
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
                                        this.processSuccessResponse(true,'Complete: ' + text + ' records updated');
                                        if(!step && this.updatedet){
                                            this.callTaxThesaurusLinkController('update-det-linkages');
                                        }
                                        else{
                                            this.adjustUIEnd();
                                        }
                                    });
                                }
                                else{
                                    const text = getErrorResponseText(response.status,response.statusText);
                                    this.processErrorResponse(true,text);
                                }
                            })
                            .catch((err) => {});
                        }
                    }
                    else{
                        alert('Please select a Target Kingdom from the dropdown menu above.');
                    }
                },
                cancelProcess(){
                    this.processCancelled = true;
                    if(!this.currentSciname){
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
                        this.setUnlinkedRecordCounts();
                        this.adjustUIEnd();
                    }
                },
                clearSubprocesses(id){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    parentProcObj['subs'] = [];
                },
                getDataSourceName(){
                    if(this.dataSource === 'col'){
                        return 'Catalogue of Life';
                    }
                    else if(this.dataSource === 'itis'){
                        return 'Integrated Taxonomic Information System';
                    }
                    else if(this.dataSource === 'worms'){
                        return 'World Register of Marine Species';
                    }
                },
                getITISNameSearchResultsHierarchy(){
                    let id;
                    if(this.nameSearchResults[0]['accepted']){
                        id = this.nameSearchResults[0]['id'];
                    }
                    else{
                        id = this.nameSearchResults[0]['accepted_id'];
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
                                let foundNameRank = this.nameSearchResults[0]['rankid'];
                                if(!this.nameSearchResults[0]['accepted']){
                                    const acceptedObj = resArr.find(rettaxon => rettaxon['taxonName'] === this.nameSearchResults[0]['accepted_sciname']);
                                    foundNameRank = Number(this.rankArr[acceptedObj['rankName'].toLowerCase()]);
                                }
                                for(let i in resArr){
                                    if(resArr.hasOwnProperty(i)){
                                        const taxResult = resArr[i];
                                        if(taxResult['taxonName'] !== this.nameSearchResults[0]['sciname']){
                                            const rankname = taxResult['rankName'].toLowerCase();
                                            const rankid = Number(this.rankArr[rankname]);
                                            if(rankid <= foundNameRank && TAXONOMIC_RANKS.includes(rankid)){
                                                const resultObj = {};
                                                resultObj['id'] = taxResult['tsn'];
                                                resultObj['sciname'] = taxResult['taxonName'];
                                                resultObj['author'] = taxResult['author'] ? taxResult['author'] : '';
                                                resultObj['rankname'] = rankname;
                                                resultObj['rankid'] = rankid;
                                                if(rankname === 'family'){
                                                    this.nameSearchResults[0]['family'] = resultObj['sciname'];
                                                }
                                                hierarchyArr.push(resultObj);
                                            }
                                        }
                                    }
                                }
                                this.nameSearchResults[0]['hierarchy'] = hierarchyArr;
                                this.processSuccessResponse(false);
                                this.validateNameSearchResults();
                            });
                        }
                        else{
                            this.processErrorResponse(false,'Unable to retrieve taxon hierarchy');
                            this.runScinameDataSourceSearch();
                        }
                    });
                },
                getITISNameSearchResultsRecord(){
                    const id = this.nameSearchResults[0]['id'];
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
                                this.nameSearchResults[0]['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                                this.nameSearchResults[0]['rankid'] = Number(taxonRankData['rankId']);
                                const coreMetadata = resObj['coreMetadata'];
                                const namestatus = coreMetadata['taxonUsageRating'];
                                if(namestatus === 'accepted' || namestatus === 'valid'){
                                    this.nameSearchResults[0]['accepted'] = true;
                                    this.getITISNameSearchResultsHierarchy();
                                }
                                else{
                                    this.nameSearchResults[0]['accepted'] = false;
                                    const acceptedNameList = resObj['acceptedNameList'];
                                    const acceptedNameArr = acceptedNameList['acceptedNames'];
                                    if(acceptedNameArr.length > 0){
                                        const acceptedName = acceptedNameArr[0];
                                        this.nameSearchResults[0]['accepted_id'] = acceptedName['acceptedTsn'];
                                        this.nameSearchResults[0]['accepted_sciname'] = acceptedName['acceptedName'];
                                        this.getITISNameSearchResultsHierarchy();
                                    }
                                    else{
                                        this.processErrorResponse(false,'Unable to distinguish taxon by name');
                                        this.runScinameDataSourceSearch();
                                    }
                                }
                            });
                        }
                        else{
                            this.processErrorResponse(false,'Unable to retrieve taxon record');
                            this.runScinameDataSourceSearch();
                        }
                    });
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
                        undoOrigName: '',
                        undoChangedName: '',
                        changedTid: 0,
                        resultText: ''
                    };
                },
                getWoRMSAddTaxonAuthor(){
                    if(!this.processCancelled){
                        const id = this.processingArr[0]['id'];
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
                                    const currentTaxon = this.processingArr[0];
                                    currentTaxon['author'] = resObj['authority'] ? resObj['authority'] : '';
                                    this.taxaToAddArr.push(currentTaxon);
                                    this.processingArr.splice(0, 1);
                                    this.setTaxaToAdd();
                                });
                            }
                            else{
                                const currentTaxon = this.processingArr[0];
                                this.taxaToAddArr.push(currentTaxon);
                                this.processingArr.splice(0, 1);
                                this.setTaxaToAdd();
                            }
                        });
                    }
                },
                getWoRMSNameSearchResultsHierarchy(){
                    let id;
                    if(this.nameSearchResults[0]['accepted']){
                        id = this.nameSearchResults[0]['id'];
                    }
                    else{
                        id = this.nameSearchResults[0]['accepted_id'];
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
                                const foundNameRank = this.nameSearchResults[0]['rankid'];
                                let childObj = resObj['child'];
                                const firstObj = {};
                                const firstrankname = childObj['rank'].toLowerCase();
                                const firstrankid = Number(this.rankArr[firstrankname]);
                                const newTaxonAccepted = this.nameSearchResults[0]['accepted'];
                                firstObj['id'] = childObj['AphiaID'];
                                firstObj['sciname'] = childObj['scientificname'];
                                firstObj['author'] = '';
                                firstObj['rankname'] = firstrankname;
                                firstObj['rankid'] = firstrankid;
                                hierarchyArr.push(firstObj);
                                let stopLoop = false;
                                while((childObj = childObj['child']) && !stopLoop){
                                    if(childObj['scientificname'] !== this.nameSearchResults[0]['sciname']){
                                        const rankname = childObj['rank'].toLowerCase();
                                        const rankid = Number(this.rankArr[rankname]);
                                        if((newTaxonAccepted && rankid < foundNameRank && TAXONOMIC_RANKS.includes(rankid)) || (!newTaxonAccepted && (childObj['scientificname'] === this.nameSearchResults[0]['accepted_sciname'] || TAXONOMIC_RANKS.includes(rankid)))){
                                            const resultObj = {};
                                            resultObj['id'] = childObj['AphiaID'];
                                            resultObj['sciname'] = childObj['scientificname'];
                                            resultObj['author'] = '';
                                            resultObj['rankname'] = rankname;
                                            resultObj['rankid'] = rankid;
                                            if(rankname === 'family'){
                                                this.nameSearchResults[0]['family'] = resultObj['sciname'];
                                            }
                                            hierarchyArr.push(resultObj);
                                        }
                                        if((newTaxonAccepted && rankid === foundNameRank) || (!newTaxonAccepted && childObj['scientificname'] === this.nameSearchResults[0]['accepted_sciname'])){
                                            stopLoop = true;
                                        }
                                    }
                                }
                                this.nameSearchResults[0]['hierarchy'] = hierarchyArr;
                                this.processSuccessResponse(false);
                                this.validateNameSearchResults();
                            });
                        }
                        else{
                            this.processErrorResponse(false,'Unable to retrieve taxon hierarchy');
                            this.runScinameDataSourceSearch();
                        }
                    });
                },
                getWoRMSNameSearchResultsRecord(id){
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
                                if(resObj['kingdom'].toLowerCase() === this.selectedKingdomName.toLowerCase()){
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
                                    this.nameSearchResults.push(resultObj);
                                    this.getWoRMSNameSearchResultsHierarchy();
                                }
                                else{
                                    this.processErrorResponse(false,'Not found');
                                    this.runScinameDataSourceSearch();
                                }
                            });
                        }
                        else{
                            this.processErrorResponse(false,'Unable to retrieve taxon record');
                            this.runScinameDataSourceSearch();
                        }
                    });
                },
                initializeCleanScinameAuthor(){
                    this.processCancelled = false;
                    this.adjustUIStart('cleanScinameAuthor');
                    const text = 'Getting unlinked occurrence record scientific names';
                    this.processorDisplayArr.push(this.getNewProcessObject('cleanScinameAuthor','multi',text));
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
                                this.processSuccessResponse(true,'Complete');
                                this.unlinkedNamesArr = this.processUnlinkedNamesArr(resObj);
                                this.runCleanScinameAuthorProcess();
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            this.processErrorResponse(true,text);
                        }
                    })
                    .catch((err) => {});
                },
                initializeDataSourceSearch(){
                    if(this.selectedKingdomId){
                        this.processCancelled = false;
                        this.nameTidIndex = {};
                        this.taxaLoaded = 0;
                        this.newTidArr = [];
                        this.adjustUIStart('resolveFromTaxaDataSource');
                        const text = 'Setting rank data for processing search returns';
                        this.processorDisplayArr.push(this.getNewProcessObject('resolveFromTaxaDataSource','multi',text));
                        const url = taxonomyApiUrl + '?action=getRankNameArr'
                        abortController = new AbortController();
                        fetch(url, {
                            signal: abortController.signal
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((resObj) => {
                                    this.processSuccessResponse(true, 'Complete');
                                    this.rankArr = resObj;
                                    this.setUnlinkedTaxaList();
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                this.processErrorResponse(true,text);
                            }
                        });
                    }
                    else{
                        alert('Please select a Target Kingdom from the dropdown menu above.');
                    }
                },
                initializeTaxThesaurusFuzzyMatch(){
                    if(this.selectedKingdomId && this.levValue && Number(this.levValue) > 0){
                        this.processCancelled = false;
                        this.adjustUIStart('taxThesaurusFuzzyMatch');
                        const text = 'Getting unlinked occurrence record scientific names';
                        this.processorDisplayArr.push(this.getNewProcessObject('taxThesaurusFuzzyMatch','multi',text));
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
                                    this.processSuccessResponse(true,'Complete');
                                    this.unlinkedNamesArr = this.processUnlinkedNamesArr(resObj);
                                    this.runTaxThesaurusFuzzyMatchProcess();
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                this.processErrorResponse(true,text);
                            }
                        })
                        .catch((err) => {});
                    }
                    else if(!this.selectedKingdomId){
                        alert('Please select a Target Kingdom from the dropdown menu above.');
                    }
                    else{
                        alert('Please select a character difference tolerance value greater than zero.');
                    }
                },
                populateTaxonomicHierarchy(){
                    if(this.rebuildHierarchyLoop < 40){
                        const formData = new FormData();
                        formData.append('tidarr', JSON.stringify(this.newTidArr));
                        formData.append('action', 'populateHierarchyTable');
                        fetch(taxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.text().then((res) => {
                                    if(Number(res) > 0){
                                        this.rebuildHierarchyLoop++;
                                        this.populateTaxonomicHierarchy();
                                    }
                                    else{
                                        this.processSuccessResponse(true,'Complete');
                                        this.adjustUIEnd();
                                    }
                                });
                            }
                            else{
                                this.processErrorResponse(false,'Error rebuilding the taxonomic hierarchy');
                                this.adjustUIEnd();
                            }
                        });
                    }
                    else{
                        this.processErrorResponse(false,'Error rebuilding the taxonomic hierarchy');
                        this.adjustUIEnd();
                    }
                },
                primeTaxonomicHierarchy(){
                    this.rebuildHierarchyLoop = 0;
                    const text = 'Populating taxonomic hierarchy with new taxa';
                    this.processorDisplayArr.push(this.getNewProcessObject('primeHierarchyTable','multi',text));
                    const formData = new FormData();
                    formData.append('tidarr', JSON.stringify(this.newTidArr));
                    formData.append('action', 'primeHierarchyTable');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.text().then((res) => {
                                if(Number(res) > 0){
                                    this.rebuildHierarchyLoop++;
                                    this.populateTaxonomicHierarchy();
                                }
                                else{
                                    this.adjustUIEnd();
                                }
                            });
                        }
                        else{
                            this.processErrorResponse(false,'Error rebuilding the taxonomic hierarchy');
                            this.adjustUIEnd();
                        }
                    });
                },
                processAddTaxaArr(){
                    if(this.taxaToAddArr.length > 0){
                        const taxonToAdd = this.taxaToAddArr[0];
                        const text = 'Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus';
                        this.addSubprocessToProcessorDisplay(this.currentSciname,'text',text);
                        const newTaxonObj = {};
                        newTaxonObj['sciname'] = taxonToAdd['sciname'];
                        newTaxonObj['author'] = taxonToAdd['author'];
                        newTaxonObj['kingdomid'] = this.selectedKingdomId;
                        newTaxonObj['rankid'] = taxonToAdd['rankid'];
                        newTaxonObj['acceptstatus'] = 1;
                        newTaxonObj['tidaccepted'] = '';
                        newTaxonObj['parenttid'] = this.nameTidIndex[taxonToAdd['parentName']];
                        newTaxonObj['family'] = taxonToAdd['family'];
                        newTaxonObj['source'] = this.getDataSourceName();
                        newTaxonObj['source-name'] = this.dataSource;
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
                                    this.nameTidIndex[this.taxaToAddArr[0]['sciname']] = newTid;
                                    this.newTidArr.push(newTid);
                                    this.taxaToAddArr.splice(0, 1);
                                    this.processSubprocessSuccessResponse(this.currentSciname,false);
                                    this.processAddTaxaArr();
                                }
                                else{
                                    this.processSubprocessErrorResponse(this.currentSciname,false,'Error loading taxon');
                                    this.runScinameDataSourceSearch();
                                }
                            });
                        });
                    }
                    else{
                        this.processAddTaxon();
                    }
                },
                processAddTaxon(){
                    const taxonToAdd = this.nameSearchResults[0];
                    const text = 'Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus';
                    this.addSubprocessToProcessorDisplay(this.currentSciname,'text',text);
                    if(this.nameTidIndex.hasOwnProperty(taxonToAdd['sciname'])){
                        this.processSubprocessSuccessResponse(this.currentSciname,false,this.nameSearchResults[0]['sciname'] + 'already added');
                        this.updateOccurrenceLinkages();
                    }
                    else{
                        const newTaxonObj = {};
                        newTaxonObj['sciname'] = taxonToAdd['sciname'];
                        newTaxonObj['author'] = taxonToAdd['author'];
                        newTaxonObj['kingdomid'] = this.selectedKingdomId;
                        newTaxonObj['rankid'] = taxonToAdd['rankid'];
                        newTaxonObj['acceptstatus'] = taxonToAdd['accepted'] ? 1 : 0;
                        newTaxonObj['tidaccepted'] = !taxonToAdd['accepted'] ? this.nameTidIndex[taxonToAdd['accepted_sciname']] : '';
                        newTaxonObj['parenttid'] = this.nameTidIndex[taxonToAdd['parentName']];
                        newTaxonObj['family'] = taxonToAdd['family'];
                        newTaxonObj['source'] = this.getDataSourceName();
                        newTaxonObj['source-name'] = this.dataSource;
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
                                    this.nameTidIndex[this.nameSearchResults[0]['sciname']] = newTid;
                                    this.newTidArr.push(newTid);
                                    this.processSubprocessSuccessResponse(this.currentSciname,false,'Successfully added ' + this.nameSearchResults[0]['sciname']);
                                    if(this.currentSciname === this.nameSearchResults[0]['sciname']){
                                        this.updateOccurrenceLinkages();
                                    }
                                    else{
                                        const text = 'Updating occurrence records with cleaned scientific name';
                                        this.addSubprocessToProcessorDisplay(this.currentSciname,'undo',text);
                                        this.changedCurrentSciname = this.currentSciname;
                                        this.changedParsedSciname = this.nameSearchResults[0]['sciname'];
                                        const formData = new FormData();
                                        formData.append('collid', collId);
                                        formData.append('sciname', this.currentSciname);
                                        formData.append('cleanedsciname', this.nameSearchResults[0]['sciname']);
                                        formData.append('tid', newTid.toString());
                                        formData.append('action', 'updateOccWithCleanedName');
                                        fetch(occurrenceTaxonomyApiUrl, {
                                            method: 'POST',
                                            body: formData
                                        })
                                        .then((response) => {
                                            if(response.status === 200){
                                                this.setSubprocessUndoNames(this.currentSciname,this.changedCurrentSciname,this.changedParsedSciname);
                                                this.processSubprocessSuccessResponse(this.currentSciname,false,(res + ' records updated'));
                                                this.updateOccurrenceLinkages();
                                            }
                                            else{
                                                this.processSubprocessErrorResponse(this.currentSciname,true,'Error updating occurrence records');
                                                this.updateOccurrenceLinkages();
                                            }
                                        });
                                    }
                                }
                                else{
                                    this.processSubprocessErrorResponse(this.currentSciname,false,'Error loading taxon');
                                    this.runScinameDataSourceSearch();
                                }
                            });
                        });
                    }
                },
                processErrorResponse(setCounts,text){
                    const procObj = this.processorDisplayArr.find(proc => proc['current'] === true);
                    if(procObj){
                        procObj['current'] = false;
                        if(procObj['loading'] === true){
                            procObj['loading'] = false;
                            procObj['result'] = 'error';
                            procObj['resultText'] = text;
                        }
                    }
                    if(setCounts){
                        this.setUnlinkedRecordCounts();
                    }
                },
                processFuzzyMatches(fuzzyMatches){
                    fuzzyMatches.forEach((match) => {
                        const fuzzyMatchName = match['sciname'];
                        const text = 'Match: ' + fuzzyMatchName;
                        this.addSubprocessToProcessorDisplay(this.currentSciname,'fuzzy',text);
                        this.setSubprocessUndoNames(this.currentSciname,this.currentSciname,fuzzyMatchName,match['tid']);
                        this.processSubprocessSuccessResponse(this.currentSciname,false);
                    });
                    const text = 'skip';
                    this.addSubprocessToProcessorDisplay(this.currentSciname,'fuzzy',text);
                    this.processSubprocessSuccessResponse(this.currentSciname,true);
                },
                processGetCOLTaxonByScinameResponse(resObj){
                    if(resObj['total_number_of_results'] > 0){
                        const resultArr = resObj['result'];
                        for(let i in resultArr){
                            if(resultArr.hasOwnProperty(i)){
                                const taxResult = resultArr[i];
                                const status = taxResult['name_status'];
                                if(status !== 'common name'){
                                    const resultObj = {};
                                    resultObj['id'] = taxResult['id'];
                                    resultObj['author'] = taxResult.hasOwnProperty('author') ? taxResult['author'] : '';
                                    let rankName = taxResult['rank'].toLowerCase();
                                    if(rankName === 'infraspecies'){
                                        resultObj['sciname'] = taxResult['genus'] + ' ' + taxResult['species'] + ' ' + taxResult['infraspeciesMarker'] + ' ' + taxResult['infraspecies'];
                                        if(taxResult['infraspeciesMarker'] === 'var.'){
                                            rankName = 'variety';
                                        }
                                        else if(taxResult['infraspeciesMarker'] === 'subsp.'){
                                            rankName = 'subspecies';
                                        }
                                        else if(taxResult['infraspeciesMarker'] === 'f.'){
                                            rankName = 'form';
                                        }
                                    }
                                    else{
                                        resultObj['sciname'] = taxResult['name'];
                                    }
                                    resultObj['rankname'] = rankName;
                                    resultObj['rankid'] = this.rankArr.hasOwnProperty(resultObj['rankname']) ? this.rankArr[resultObj['rankname']] : null;
                                    if(status === 'accepted name'){
                                        resultObj['accepted'] = true;
                                    }
                                    else if(status === 'synonym'){
                                        const hierarchyArr = [];
                                        const resultHObj = {};
                                        const acceptedObj = taxResult['accepted_name'];
                                        resultObj['accepted'] = false;
                                        resultObj['accepted_id'] = acceptedObj['id'];
                                        resultHObj['id'] = acceptedObj['id'];
                                        resultHObj['author'] = acceptedObj.hasOwnProperty('author') ? acceptedObj['author'] : '';
                                        let rankName = acceptedObj['rank'].toLowerCase();
                                        if(rankName === 'infraspecies'){
                                            resultHObj['sciname'] = acceptedObj['genus'] + ' ' + acceptedObj['species'] + ' ' + acceptedObj['infraspeciesMarker'] + ' ' + acceptedObj['infraspecies'];
                                            if(acceptedObj['infraspeciesMarker'] === 'var.'){
                                                rankName = 'variety';
                                            }
                                            else if(acceptedObj['infraspeciesMarker'] === 'subsp.'){
                                                rankName = 'subspecies';
                                            }
                                            else if(acceptedObj['infraspeciesMarker'] === 'f.'){
                                                rankName = 'form';
                                            }
                                        }
                                        else{
                                            resultHObj['sciname'] = acceptedObj['name'];
                                        }
                                        resultObj['accepted_sciname'] = resultHObj['sciname'];
                                        resultHObj['rankname'] = rankName;
                                        resultHObj['rankid'] = this.rankArr.hasOwnProperty(resultHObj['rankname']) ? this.rankArr[resultHObj['rankname']] : null;
                                        hierarchyArr.push(resultHObj);
                                        resultObj['hierarchy'] = hierarchyArr;
                                    }
                                    const existingObj = this.colInitialSearchResults.find(taxon => (taxon['sciname'] === resultObj['sciname'] && taxon['accepted_sciname'] === resultObj['accepted_sciname']));
                                    if(!existingObj){
                                        this.colInitialSearchResults.push(resultObj);
                                    }
                                }
                            }
                        }
                        if(this.colInitialSearchResults.length > 0){
                            this.validateCOLInitialNameSearchResults();
                        }
                        else{
                            this.processErrorResponse(false,'Not found');
                            this.runScinameDataSourceSearch();
                        }
                    }
                    else{
                        this.processErrorResponse(false,'Not found');
                        this.runScinameDataSourceSearch();
                    }
                },
                processGetITISTaxonByScinameResponse(resObj){
                    this.itisInitialSearchResults = [];
                    const resultArr = resObj['scientificNames'];
                    if(resultArr && resultArr.length > 0 && resultArr[0]){
                        for(let i in resultArr){
                            if(resultArr.hasOwnProperty(i)){
                                const taxResult = resultArr[i];
                                if(taxResult['combinedName'] === this.currentSciname && taxResult['kingdom'].toLowerCase() === this.selectedKingdomName.toLowerCase()){
                                    const resultObj = {};
                                    resultObj['id'] = taxResult['tsn'];
                                    resultObj['sciname'] = taxResult['combinedName'];
                                    resultObj['author'] = taxResult['author'];
                                    this.itisInitialSearchResults.push(resultObj);
                                }
                            }
                        }
                        if(this.itisInitialSearchResults.length === 1){
                            this.nameSearchResults = this.itisInitialSearchResults;
                            this.getITISNameSearchResultsRecord();
                        }
                        else if(this.itisInitialSearchResults.length === 0){
                            this.processErrorResponse(false,'Not found');
                            this.runScinameDataSourceSearch();
                        }
                        else if(this.itisInitialSearchResults.length > 1){
                            this.validateITISInitialNameSearchResults();
                        }
                    }
                    else{
                        this.processErrorResponse(false,'Not found');
                        this.runScinameDataSourceSearch();
                    }
                },
                processingBatchLimitChange(value) {
                    if(value && (isNaN(value) || Number(value) <= 0)){
                        alert('Processing batch limit must be a number greater than zero.');
                        this.processingLimit = null;
                    }
                },
                processSubprocessErrorResponse(id,setCounts,text){
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
                    if(setCounts){
                        this.setUnlinkedRecordCounts();
                    }
                },
                processSubprocessSuccessResponse(id,complete,text = null){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    if(parentProcObj){
                        parentProcObj['current'] = !complete;
                        const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                        if(subProcObj){
                            subProcObj['loading'] = false;
                            subProcObj['result'] = 'success';
                            subProcObj['resultText'] = text;
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
                processUnlinkedNamesArr(inArr){
                    if(Array.isArray(inArr) && inArr.length > 0){
                        if(this.processingStartIndex){
                            let nameArrLength = inArr.length;
                            let startIndexVal = null;
                            for(let i = 0 ; i < nameArrLength; i++) {
                                if(inArr.hasOwnProperty(i) && inArr[i].toLowerCase() > this.processingStartIndex.toLowerCase()){
                                    startIndexVal = i;
                                    break;
                                }
                            }
                            if(!startIndexVal){
                                startIndexVal = nameArrLength;
                            }
                            inArr = inArr.splice(startIndexVal, (nameArrLength - startIndexVal));
                        }
                        if(this.processingLimit){
                            inArr = inArr.splice(0, this.processingLimit);
                        }
                    }
                    return inArr;
                },
                runCleanScinameAuthorProcess(){
                    if(!this.processCancelled && this.unlinkedNamesArr.length > 0){
                        this.currentSciname = this.unlinkedNamesArr[0];
                        this.unlinkedNamesArr.splice(0, 1);
                        const text = 'Attempting to parse author name from: ' + this.currentSciname;
                        this.processorDisplayArr.push(this.getNewProcessObject(this.currentSciname,'multi',text));
                        const formData = new FormData();
                        formData.append('sciname', this.currentSciname);
                        formData.append('action', 'parseSciName');
                        fetch(taxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((parsedName) => {
                                    if(parsedName.hasOwnProperty('author') && parsedName['author'] !== ''){
                                        this.processSuccessResponse(false,'Parsed author: ' + parsedName['author'] + '; Cleaned scientific name: ' + parsedName['sciname']);
                                        const text = 'Updating occurrence records with cleaned scientific name';
                                        this.addSubprocessToProcessorDisplay(this.currentSciname,'undo',text);
                                        this.changedCurrentSciname = this.currentSciname;
                                        this.changedParsedSciname = parsedName['sciname'];
                                        const formData = new FormData();
                                        formData.append('collid', collId);
                                        formData.append('sciname', this.currentSciname);
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
                                                        this.setSubprocessUndoNames(this.currentSciname,this.changedCurrentSciname,this.changedParsedSciname);
                                                        this.processSubprocessSuccessResponse(this.currentSciname,true,(res + ' records updated'));
                                                        this.runCleanScinameAuthorProcess();
                                                    });
                                                }
                                                else{
                                                    this.processSubprocessErrorResponse(this.currentSciname,false,'Error updating occurrence records');
                                                    this.runCleanScinameAuthorProcess();
                                                }
                                            });
                                    }
                                    else{
                                        this.processErrorResponse(false,'No author found in scientific name');
                                        this.runCleanScinameAuthorProcess();
                                    }
                                });
                            }
                        });
                    }
                    else{
                        this.adjustUIEnd();
                    }
                },
                runScinameDataSourceSearch(){
                    if(!this.processCancelled && this.unlinkedNamesArr.length > 0){
                        this.nameSearchResults = [];
                        this.currentSciname = this.unlinkedNamesArr[0];
                        this.unlinkedNamesArr.splice(0, 1);
                        if(this.dataSource === 'col'){
                            this.colInitialSearchResults = [];
                            const text = 'Searching the Catalogue of Life (COL) for ' + this.currentSciname;
                            this.processorDisplayArr.push(this.getNewProcessObject(this.currentSciname,'multi',text));
                            const url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&name=' + this.currentSciname;
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
                                        this.processGetCOLTaxonByScinameResponse(res);
                                    });
                                }
                                else{
                                    const text = getErrorResponseText(response.status,response.statusText);
                                    this.processErrorResponse(false,text);
                                    this.runScinameDataSourceSearch();
                                }
                            });
                        }
                        else if(this.dataSource === 'itis'){
                            this.itisInitialSearchResults = [];
                            const text = 'Searching the Integrated Taxonomic Information System (ITIS) for ' + this.currentSciname;
                            this.processorDisplayArr.push(this.getNewProcessObject(this.currentSciname,'multi',text));
                            const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/searchByScientificName?srchKey=' + this.currentSciname;
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
                                        this.processGetITISTaxonByScinameResponse(res);
                                    });
                                }
                                else{
                                    const text = getErrorResponseText(response.status,response.statusText);
                                    this.processErrorResponse(false,text);
                                    this.runScinameDataSourceSearch();
                                }
                            });
                        }
                        else if(this.dataSource === 'worms'){
                            const text = 'Searching the World Register of Marine Species (WoRMS) for ' + this.currentSciname;
                            this.processorDisplayArr.push(this.getNewProcessObject(this.currentSciname,'multi',text));
                            const url = 'https://www.marinespecies.org/rest/AphiaIDByName/' + this.currentSciname + '?marine_only=false';
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
                                            this.getWoRMSNameSearchResultsRecord(res);
                                        }
                                        else{
                                            this.processErrorResponse(false,'Not found');
                                            this.runScinameDataSourceSearch();
                                        }
                                    });
                                }
                                else if(response.status === 204){
                                    this.processErrorResponse(false,'Not found');
                                    this.runScinameDataSourceSearch();
                                }
                                else{
                                    const text = getErrorResponseText(response.status,response.statusText);
                                    this.processErrorResponse(false,text);
                                    this.runScinameDataSourceSearch();
                                }
                            });
                        }
                    }
                    else if(this.newTidArr.length > 0){
                        this.primeTaxonomicHierarchy();
                    }
                    else{
                        this.adjustUIEnd();
                    }
                },
                runTaxThesaurusFuzzyMatchProcess(){
                    this.changedCurrentSciname = '';
                    this.changedParsedSciname = '';
                    if(!this.processCancelled && this.unlinkedNamesArr.length > 0){
                        this.currentSciname = this.unlinkedNamesArr[0];
                        this.unlinkedNamesArr.splice(0, 1);
                        const text = 'Finding fuzzy matches for ' + this.currentSciname;
                        this.processorDisplayArr.push(this.getNewProcessObject(this.currentSciname,'multi',text));
                        const formData = new FormData();
                        formData.append('sciname', this.currentSciname);
                        formData.append('lev', this.levValue);
                        formData.append('action', 'getSciNameFuzzyMatches');
                        fetch(taxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((fuzzyMatches) => {
                                    if(fuzzyMatches.length > 0){
                                        this.processSuccessResponse(false);
                                        this.processFuzzyMatches(fuzzyMatches);
                                    }
                                    else{
                                        this.processErrorResponse(false,'No fuzzy matches found');
                                        this.runTaxThesaurusFuzzyMatchProcess();
                                    }
                                });
                            }
                        });
                    }
                    else{
                        this.adjustUIEnd();
                    }
                },
                selectFuzzyMatch(sciName,newName,newtid){
                    this.changedCurrentSciname = sciName;
                    this.changedParsedSciname = newName;
                    this.clearSubprocesses(this.currentSciname);
                    const text = 'Updating occurrence records with selected scientific name';
                    this.addSubprocessToProcessorDisplay(this.currentSciname,'undo',text);
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
                                this.setSubprocessUndoNames(this.currentSciname,this.changedCurrentSciname,this.changedParsedSciname);
                                this.processSubprocessSuccessResponse(this.currentSciname,true,(res + ' records updated'));
                                this.runTaxThesaurusFuzzyMatchProcess();
                            });
                        }
                        else{
                            this.processSubprocessErrorResponse(this.currentSciname,false,'Error updating occurrence records');
                            this.runTaxThesaurusFuzzyMatchProcess();
                        }
                    });
                },
                setSubprocessUndoNames(id,origName,newName,tid = null){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                    subProcObj['undoOrigName'] = origName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
                    subProcObj['undoChangedName'] = newName.replaceAll("'",'%squot;').replaceAll('"','%dquot;');
                    if(tid){
                        subProcObj['changedTid'] = tid;
                    }
                },
                setTaxaToAdd(){
                    if(this.processingArr.length > 0){
                        const sciname = this.processingArr[0]['sciname'];
                        if(!this.nameTidIndex.hasOwnProperty(sciname)){
                            const url = CLIENT_ROOT + '/api/taxa/gettid.php';
                            const formData = new FormData();
                            formData.append('sciname', sciname);
                            formData.append('kingdomid', this.selectedKingdomId);
                            fetch(url, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                if(response.status === 200){
                                    response.text().then((res) => {
                                        if(this.dataSource === 'worms' && !res){
                                            this.getWoRMSAddTaxonAuthor();
                                        }
                                        else{
                                            const currentTaxon = this.processingArr[0];
                                            if(res){
                                                this.nameTidIndex[currentTaxon['sciname']] = Number(res);
                                            }
                                            else{
                                                this.taxaToAddArr.push(currentTaxon);
                                            }
                                            this.processingArr.splice(0, 1);
                                            this.setTaxaToAdd();
                                        }
                                    });
                                }
                            });
                        }
                        else{
                            this.processingArr.splice(0, 1);
                            this.setTaxaToAdd();
                        }
                    }
                    else{
                        this.processSubprocessSuccessResponse(this.currentSciname,false);
                        this.processAddTaxaArr();
                    }
                },
                setUnlinkedRecordCounts(){
                    this.unlinkedOccCnt = null;
                    this.unlinkedTaxaCnt = null;
                    this.unlinkedLoading = true;
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
                            this.unlinkedOccCnt = result['occCnt'];
                        }
                        if(result.hasOwnProperty('taxaCnt')){
                            this.unlinkedTaxaCnt = result['taxaCnt'];
                        }
                        this.currentProcess = null;
                        this.undoButtonsDisabled = false;
                        this.uppercontrolsdisabled = false;
                        this.unlinkedLoading = false;
                    });
                },
                setUnlinkedTaxaList(){
                    if(!this.processCancelled){
                        const text = 'Getting unlinked occurrence record scientific names';
                        this.processorDisplayArr.push(this.getNewProcessObject('getUnlinkedOccSciNames','multi',text));
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
                                    this.processSuccessResponse(true,'Complete');
                                    this.unlinkedNamesArr = this.processUnlinkedNamesArr(resObj);
                                    this.runScinameDataSourceSearch();
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                this.processErrorResponse(true,text);
                            }
                        })
                        .catch((err) => {});
                    }
                },
                undoChangedSciname(id,oldName,newName){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    const subProcObj = parentProcObj['subs'].find(subproc => subproc['undoChangedName'] === newName);
                    subProcObj['type'] = 'text';
                    const text = 'Reverting scientific name change from ' + oldName.replaceAll('%squot;',"'").replaceAll('%dquot;','"') + ' to ' + newName.replaceAll('%squot;',"'").replaceAll('%dquot;','"');
                    this.addSubprocessToProcessorDisplay(id,'text',text);
                    this.undoId = id;
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
                                this.processSubprocessSuccessResponse(this.undoId,true,(res + ' records reverted'));
                            });
                        }
                        else{
                            this.processSubprocessErrorResponse(this.undoId,false,'Error undoing name change');
                        }
                    });
                },
                updateOccLocalitySecurity(){
                    this.adjustUIStart('updateOccLocalitySecurity');
                    const text = 'Updating the locality security settings for occurrence records of protected species';
                    this.processorDisplayArr.push(this.getNewProcessObject('updateLocalitySecurity','single',text));
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
                                this.processSuccessResponse(true,'Complete: ' + res + ' records updated');
                                this.adjustUIEnd();
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            this.processErrorResponse(true,text);
                        }
                    })
                    .catch((err) => {});
                },
                updateOccurrenceLinkages(){
                    const newSciname = this.nameSearchResults[0]['sciname'];
                    const newScinameTid = this.nameTidIndex[this.nameSearchResults[0]['sciname']];
                    const text = 'Updating linkages of occurrence records to ' + newSciname;
                    this.addSubprocessToProcessorDisplay(this.currentSciname,'text',text);
                    const formData = new FormData();
                    formData.append('collid', collId);
                    formData.append('sciname', newSciname);
                    formData.append('tid', newScinameTid);
                    formData.append('kingdomid', this.selectedKingdomId);
                    formData.append('action', 'updateOccWithNewSciname');
                    fetch(occurrenceTaxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.text().then((res) => {
                                this.processSubprocessSuccessResponse(this.currentSciname,true,res + ' records updated');
                                this.taxaLoaded++;
                                if(this.taxaLoaded > 30){
                                    this.setUnlinkedRecordCounts();
                                    this.taxaLoaded = 0;
                                }
                                this.runScinameDataSourceSearch();
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            this.processSubprocessErrorResponse(this.currentSciname,true,text);
                            this.taxaLoaded++;
                            if(this.taxaLoaded > 30){
                                this.setUnlinkedRecordCounts();
                                this.taxaLoaded = 0;
                            }
                            this.runScinameDataSourceSearch();
                        }
                    });
                },
                updateSelectedDataSource(dataSourceObj) {
                    this.dataSource = dataSourceObj;
                },
                updateSelectedKingdom(kingdomObj) {
                    this.selectedKingdom = kingdomObj;
                    this.selectedKingdomId = kingdomObj.id;
                    this.selectedKingdomName = kingdomObj.name;
                },
                validateCOLInitialNameSearchResults(){
                    if(this.colInitialSearchResults.length > 0){
                        let id;
                        const taxon = this.colInitialSearchResults[0];
                        this.colInitialSearchResults.splice(0, 1);
                        if(taxon['accepted']){
                            id = taxon['id'];
                        }
                        else{
                            id = taxon['accepted_id'];
                        }
                        const url = 'https://api.catalogueoflife.org/dataset/9840/taxon/' + id + '/classification';
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
                                    const kingdomObj = resArr.find(rettaxon => rettaxon['rank'].toLowerCase() === 'kingdom');
                                    if(kingdomObj && kingdomObj['name'].toLowerCase() === this.selectedKingdomName.toLowerCase()){
                                        let hierarchyArr = [];
                                        if(taxon.hasOwnProperty('hierarchy')){
                                            hierarchyArr = taxon['hierarchy'];
                                        }
                                        for(let i in resArr){
                                            if(resArr.hasOwnProperty(i)){
                                                const taxResult = resArr[i];
                                                if(taxResult['name'] !== taxon['sciname']){
                                                    const rankname = taxResult['rank'].toLowerCase();
                                                    const rankid = Number(this.rankArr[rankname]);
                                                    if(TAXONOMIC_RANKS.includes(rankid) || (!taxon['accepted'] && taxon['accepted_sciname'] === taxResult['name'])){
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
                                            }
                                        }
                                        taxon['hierarchy'] = hierarchyArr;
                                        this.nameSearchResults.push(taxon);
                                    }
                                    this.validateCOLInitialNameSearchResults();
                                });
                            }
                            else{
                                this.validateCOLInitialNameSearchResults();
                            }
                        });
                    }
                    else if(this.nameSearchResults.length === 1){
                        this.processSuccessResponse(false);
                        this.validateNameSearchResults();
                    }
                    else if(this.nameSearchResults.length === 0){
                        this.processErrorResponse(false,'Not found');
                        this.runScinameDataSourceSearch();
                    }
                    else if(this.nameSearchResults.length > 1){
                        this.processErrorResponse(false,'Unable to distinguish taxon by name');
                        this.runScinameDataSourceSearch();
                    }
                },
                validateITISInitialNameSearchResults(){
                    if(this.itisInitialSearchResults.length > 0){
                        const taxon = this.itisInitialSearchResults[0];
                        this.itisInitialSearchResults.splice(0, 1);
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
                                    const namestatus = coreMetadata['taxonUsageRating'];
                                    if(namestatus === 'accepted'){
                                        const taxonRankData = resObj['taxRank'];
                                        taxon['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                                        taxon['rankid'] = Number(taxonRankData['rankId']);
                                        taxon['accepted'] = true;
                                        this.nameSearchResults.push(taxon);
                                    }
                                    this.validateITISInitialNameSearchResults();
                                });
                            }
                            else{
                                this.processErrorResponse(false,'Unable to retrieve taxon record');
                                this.runScinameDataSourceSearch();
                            }
                        });
                    }
                    else if(this.nameSearchResults.length === 1){
                        this.getITISNameSearchResultsHierarchy();
                    }
                    else if(this.nameSearchResults.length === 0){
                        this.processErrorResponse(false,'Not found');
                        this.runScinameDataSourceSearch();
                    }
                    else if(this.nameSearchResults.length > 1){
                        this.processErrorResponse(false,'Unable to distinguish taxon by name');
                        this.runScinameDataSourceSearch();
                    }
                },
                validateNameSearchResults(){
                    this.processingArr = [];
                    this.taxaToAddArr = [];
                    if(this.nameSearchResults.length === 1){
                        if(!this.nameSearchResults[0]['accepted'] && !this.nameSearchResults[0]['accepted_sciname']){
                            this.processErrorResponse(false,'Unable to distinguish accepted name');
                            this.runScinameDataSourceSearch();
                        }
                        else{
                            const addHierchyTemp = this.nameSearchResults[0]['hierarchy'];
                            addHierchyTemp.sort((a, b) => {
                                return a.rankid - b.rankid;
                            });
                            let parentName = addHierchyTemp[0]['sciname'];
                            for(let i in addHierchyTemp){
                                if(addHierchyTemp.hasOwnProperty(i) && addHierchyTemp[i]['sciname'] !== parentName){
                                    addHierchyTemp[i]['parentName'] = parentName;
                                    addHierchyTemp[i]['family'] = addHierchyTemp[i]['rankid'] >= 140 ? this.nameSearchResults[0]['family'] : null;
                                    parentName = addHierchyTemp[i]['sciname'];
                                    if(!this.nameSearchResults[0]['accepted'] && addHierchyTemp[i]['sciname'] === this.nameSearchResults[0]['accepted_sciname']){
                                        this.nameSearchResults[0]['parentName'] = addHierchyTemp[i]['parentName'];
                                    }
                                }
                            }
                            if(!this.nameSearchResults[0].hasOwnProperty('parentName') || this.nameSearchResults[0]['parentName'] === ''){
                                this.nameSearchResults[0]['parentName'] = parentName;
                            }
                            this.processingArr = addHierchyTemp;
                            const text = 'Matching parent and accepted taxa to the Taxonomic Thesaurus';
                            this.addSubprocessToProcessorDisplay(this.currentSciname,'text',text);
                            this.setTaxaToAdd();
                        }
                    }
                    else{
                        this.processErrorResponse(false,'Unable to distinguish taxon by name');
                        this.runScinameDataSourceSearch();
                    }
                },
                cancelAPIRequest,
                getErrorResponseText,
                openTutorialWindow
            }
        });
        occurrenceTaxonomyManagementModule.use(Quasar, { config: {} });
        occurrenceTaxonomyManagementModule.mount('#innertext');
    </script>
</body>
</html>
