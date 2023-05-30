<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyEditorManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

$loaderObj = new TaxonomyEditorManager();

$isEditor = false;
$status = '';
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomic Thesaurus Manager</title>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <style>
        .top-tool-container {
            width: 500px;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
</head>
<body>
    <?php
        include(__DIR__ . '/../../header.php');
    ?>
    <div class="navpath">
        <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
        <b>Taxonomic Thesaurus Manager</b>
    </div>
    <div id="innertext">
        <h1>Taxonomic Thesaurus Manager</h1>
        <?php
        if($isEditor){
            ?>
            <q-card class="top-tool-container q-mb-md">
                <q-card-section>
                    <div class="q-my-sm">
                        <single-scientific-common-name-auto-complete :sciname="taxonomicGroup" :disable="loading" label="Enter Taxonomic Group" limit-to-thesaurus="true" accepted-taxa-only="true" rank-low="10" @update:sciname="updateTaxonomicGroup"></single-scientific-common-name-auto-complete>
                    </div>
                    <div class="q-my-sm q-mt-md">
                        <taxon-rank-checkbox-selector :selected-ranks="selectedRanks" :required-ranks="requiredRanks" :kingdom-id="kingdomId" :disable="loading" link-label="Select Taxonomic Ranks" inner-label="Select taxonomic ranks for taxa to be included in import or update" @update:selected-ranks="updateSelectedRanks"></taxon-rank-checkbox-selector>
                    </div>
                </q-card-section>
            </q-card>
            <q-card>
                <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                    <q-tab name="importer" label="Data Import/Update" no-caps></q-tab>
                    <q-tab name="fileupload" label="Load Data File" no-caps></q-tab>
                    <q-tab name="maintenance" label="Maintenance Tools" no-caps></q-tab>
                </q-tabs>
                <q-separator></q-separator>
                <q-tab-panels v-model="tab">
                    <q-tab-panel name="importer">
                        <taxonomy-data-source-import-update-module :kingdom-id="kingdomId" :loading="loading" :required-ranks="requiredRanks" :selected-ranks="selectedRanks" :selected-ranks-high="selectedRanksHigh" :taxonomic-group="taxonomicGroup" :taxonomic-group-tid="taxonomicGroupTid" @update:loading="updateLoading"></taxonomy-data-source-import-update-module>
                    </q-tab-panel>
                    <q-tab-panel name="fileupload">
                        <?php include_once(__DIR__ . '/batchloader.php'); ?>
                    </q-tab-panel>
                    <q-tab-panel name="maintenance">
                        <taxonomic-thesaurus-maintenance-module></taxonomic-thesaurus-maintenance-module>
                    </q-tab-panel>
                </q-tab-panels>
            </q-card>
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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/misc/multipleLanguageAutoComplete.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/singleScientificCommonNameAutoComplete.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonRankCheckboxSelector.js?ver=20230530" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceBulletSelector.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceImportUpdateModule.js?ver=20230530" type="text/javascript"></script>
    <script>
        const taxonomicThesaurusMaintenanceModule = {
            template: `
                <div class="processor-container">
                    <div class="processor-control-container">
                        <q-card class="processor-control-card">
                            <q-list class="processor-control-accordion">
                                <q-expansion-item class="overflow-hidden" group="controlgroup" label="Thesaurus Wide Utilities" header-class="bg-grey-3 text-bold" default-opened>
                                    <q-card class="accordion-panel">
                                        <q-card-section>

                                        </q-card-section>
                                    </q-card>
                                </q-expansion-item>
                                <q-separator></q-separator>
                                <q-expansion-item class="overflow-hidden" group="controlgroup" label="Group Based Utilities" header-class="bg-grey-3 text-bold">
                                    <q-card class="accordion-panel">
                                        <q-card-section>

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
            `,
            data() {
                return {
                    changedCurrentSciname: Vue.ref(''),
                    changedParsedSciname: Vue.ref(''),
                    colInitialSearchResults: Vue.ref([]),
                    currentSciname: Vue.ref(null),
                    dataSource: Vue.ref('col'),
                    itisInitialSearchResults: Vue.ref([]),
                    levValue: Vue.ref('2'),
                    nameSearchResults: Vue.ref([]),
                    nameTidIndex: Vue.ref({}),
                    newTidArr: Vue.ref([]),
                    processCancelling: Vue.ref(false),
                    processingArr: Vue.ref([]),
                    processingLimit: Vue.ref(null),
                    processingStartIndex: Vue.ref(null),
                    processorDisplayArr: Vue.ref([]),
                    processorDisplayDataArr: Vue.ref([]),
                    processorDisplayCurrentIndex: Vue.ref(0),
                    processorDisplayIndex: Vue.ref(0),
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
            setup() {
                let currentProcess = Vue.ref(null);
                let procDisplayScrollAreaRef = Vue.ref(null);
                let procDisplayScrollHeight = Vue.ref(0);
                let scrollProcess = Vue.ref(null);
                return {
                    currentProcess,
                    procDisplayScrollAreaRef,
                    scrollProcess,
                    setScroller(info) {
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
                }
            },
            methods: {
                addProcessToProcessorDisplay(processObj){
                    this.processorDisplayArr.push(processObj);
                    if(this.processorDisplayArr.length > 100){
                        const precessorArrSegment = this.processorDisplayArr.slice(0, 100);
                        this.processorDisplayDataArr = this.processorDisplayDataArr.concat(precessorArrSegment);
                        this.processorDisplayArr.splice(0, 100);
                        this.processorDisplayIndex++;
                        this.processorDisplayCurrentIndex = this.processorDisplayIndex;
                    }
                },
                addSubprocessToProcessorDisplay(id,type,text){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    parentProcObj['subs'].push(this.getNewSubprocessObject(this.currentSciname,type,text));
                    const dataParentProcObj = this.processorDisplayDataArr.find(proc => proc['id'] === id);
                    if(dataParentProcObj){
                        dataParentProcObj['subs'].push(this.getNewSubprocessObject(this.currentSciname,type,text));
                    }
                },
                adjustUIEnd(){
                    this.processCancelling = false;
                    this.unlinkedNamesArr = [];
                    this.currentSciname = null;
                    this.setUnlinkedRecordCounts();
                    this.currentProcess = null;
                    this.undoButtonsDisabled = false;
                    this.uppercontrolsdisabled = false;
                    this.processorDisplayDataArr = this.processorDisplayDataArr.concat(this.processorDisplayArr);
                },
                adjustUIStart(id){
                    this.processorDisplayArr = [];
                    this.processorDisplayDataArr = [];
                    this.processorDisplayCurrentIndex = 0;
                    this.processorDisplayIndex = 0;
                    this.currentProcess = id;
                    this.uppercontrolsdisabled = true;
                    this.undoButtonsDisabled = true;
                },
                cancelProcess(){
                    this.processCancelling = true;
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
                        this.adjustUIEnd();
                    }
                },
                clearSubprocesses(id){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
                    parentProcObj['subs'] = [];
                    const dataParentProcObj = this.processorDisplayDataArr.find(proc => proc['id'] === id);
                    if(dataParentProcObj){
                        dataParentProcObj['subs'] = [];
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
                        undoOrigName: '',
                        undoChangedName: '',
                        changedTid: 0,
                        resultText: ''
                    };
                },
                populateTaxonomicHierarchy(){
                    if(this.rebuildHierarchyLoop < 40){
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
                    this.addProcessToProcessorDisplay(this.getNewProcessObject('primeHierarchyTable','multi',text));
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
                        this.adjustUIEnd();
                    }
                },
                processorDisplayScrollDown(){
                    this.scrollProcess = 'scrollDown';
                    this.processorDisplayCurrentIndex++;
                    this.processorDisplayArr = this.processorDisplayDataArr.slice((this.processorDisplayCurrentIndex * 100), ((this.processorDisplayCurrentIndex + 1) * 100));
                    this.resetScrollProcess();
                },
                processorDisplayScrollUp(){
                    this.scrollProcess = 'scrollUp';
                    this.processorDisplayCurrentIndex--;
                    this.processorDisplayArr = this.processorDisplayDataArr.slice((this.processorDisplayCurrentIndex * 100), ((this.processorDisplayCurrentIndex + 1) * 100));
                    this.resetScrollProcess();
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
                    const dataParentProcObj = this.processorDisplayDataArr.find(proc => proc['id'] === id);
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
                        this.adjustUIEnd();
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
                    const dataParentProcObj = this.processorDisplayDataArr.find(proc => proc['id'] === id);
                    if(dataParentProcObj){
                        dataParentProcObj['current'] = !complete;
                        const dataSubProcObj = dataParentProcObj['subs'].find(subproc => subproc['loading'] === true);
                        if(dataSubProcObj){
                            dataSubProcObj['loading'] = false;
                            dataSubProcObj['result'] = 'success';
                            dataSubProcObj['resultText'] = text;
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
                resetScrollProcess(){
                    setTimeout(() => {
                        this.scrollProcess = null;
                    }, 200);
                },
                cancelAPIRequest,
                getErrorResponseText,
                openTutorialWindow
            }
        };

        const taxonomicThesaurusManagerModule = Vue.createApp({
            data() {
                return {
                    kingdomId: Vue.ref(null),
                    loading: Vue.ref(false),
                    requiredRanks: Vue.ref([10]),
                    selectedRanks: Vue.ref([]),
                    selectedRanksHigh: Vue.ref(0),
                    taxonomicGroup: Vue.ref(null),
                    taxonomicGroupTid: Vue.ref(null)
                }
            },
            components: {
                'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                'taxon-rank-checkbox-selector': taxonRankCheckboxSelector,
                'taxonomic-thesaurus-maintenance-module': taxonomicThesaurusMaintenanceModule,
                'taxonomy-data-source-import-update-module': taxonomyDataSourceImportUpdateModule
            },
            setup() {
                return {
                    tab: Vue.ref('importer')
                }
            },
            mounted() {
                this.selectedRanks = TAXONOMIC_RANKS;
                this.setRankHigh();
            },
            methods: {
                setRankHigh() {
                    this.selectedRanksHigh = 0;
                    this.selectedRanks.forEach((rank) => {
                        if(rank > this.selectedRanksHigh){
                            this.selectedRanksHigh = rank;
                        }
                    });
                },
                updateLoading(value) {
                    this.loading = value;
                },
                updateSelectedRanks(selectedArr) {
                    this.selectedRanks = selectedArr;
                    this.setRankHigh();
                },
                updateTaxonomicGroup(taxonObj) {
                    this.taxonomicGroup = taxonObj;
                    this.taxonomicGroupTid = taxonObj ? taxonObj.tid : null;
                    this.kingdomId = taxonObj ? taxonObj.kingdomid : null;
                }
            }
        });
        taxonomicThesaurusManagerModule.use(Quasar, { config: {} });
        taxonomicThesaurusManagerModule.mount('#innertext');
    </script>
</body>
</html>
