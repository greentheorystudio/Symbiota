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
        .processor-container {
            width: 95%;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .import-update-control {
            display: flex;
            gap: 25px;
        }
        .processor-control-container {
            width: 40%;
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
            <q-card>
                <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                    <q-tab name="importer" label="Data Import/Update" no-caps></q-tab>
                    <q-tab name="fileupload" label="Load Data File" no-caps></q-tab>
                    <q-tab name="maintenance" label="Maintenance Tools" no-caps></q-tab>
                </q-tabs>
                <q-separator></q-separator>
                <q-tab-panels v-model="tab">
                    <q-tab-panel name="importer">
                        <div class="processor-container">
                            <div class="processor-control-container">
                                <q-card class="processor-control-card">
                                    <q-card-section>
                                        <div class="q-my-sm">
                                            <single-scientific-common-name-auto-complete :sciname="taxonomicGroup" :disable="loading" label="Taxonomic Group" limit-to-thesaurus="true" accepted-taxa-only="true" rank-low="10" @update:sciname="updateTaxonomicGroup"></single-scientific-common-name-auto-complete>
                                        </div>
                                        <div class="q-my-sm">
                                            <taxon-rank-checkbox-selector :selected-ranks="selectedRanks" :required-ranks="requiredRanks" :kingdom-id="kingdomId" :disable="loading" link-label="Select Taxonomic Ranks" inner-label="Select taxonomic ranks for taxa to be included in import or update" @update:selected-ranks="updateSelectedRanks"></taxon-rank-checkbox-selector>
                                        </div>
                                        <div class="q-my-sm">
                                            <taxonomy-data-source-bullet-selector :disable="loading" :selected-data-source="dataSource" @update:selected-data-source="updateSelectedDataSource"></taxonomy-data-source-bullet-selector>
                                        </div>
                                        <q-card class="q-my-sm" flat bordered>
                                            <q-card-section>
                                                <div>
                                                    <q-checkbox v-model="updateAcceptance" label="Update acceptance for synonymized names" :disable="loading" />
                                                </div>
                                                <div>
                                                    <q-checkbox v-model="importCommonNames" label="Import common names" :disable="loading" />
                                                </div>
                                                <template v-if="importCommonNames">
                                                    <div class="q-my-sm">
                                                        <multiple-language-auto-complete :language-arr="commonNameLanguageArr" :disable="loading" label="Common Name Languages" @update:language="updateCommonNameLanguageArr"></multiple-language-auto-complete>
                                                    </div>
                                                    <div class="q-my-sm">
                                                        <div class="text-subtitle1 text-weight-bold">Format Common Names</div>
                                                        <q-option-group :options="commonNameFormattingOptions" type="radio" v-model="selectedCommonNameFormatting" :disable="loading" dense />
                                                    </div>
                                                </template>
                                            </q-card-section>
                                        </q-card>
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="loading" color="secondary" @click="initializeImportUpdate();" label="Start Import/Update" dense />
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
                                        </q-list>
                                    </q-scroll-area>
                                </q-card>
                            </div>
                        </div>
                    </q-tab-panel>
                    <q-tab-panel name="fileupload">
                        <?php include_once(__DIR__ . '/batchloader.php'); ?>
                    </q-tab-panel>
                    <q-tab-panel name="maintenance">
                        <?php include_once(__DIR__ . '/maintenancetools.php'); ?>
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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonRankCheckboxSelector.js?ver=20230413" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceBulletSelector.js" type="text/javascript"></script>
    <script>
        const taxonomicThesaurusManagerModule = Vue.createApp({
            data() {
                return {
                    childrenSearchPrimingArr: Vue.ref([]),
                    clientRoot: CLIENT_ROOT,
                    colInitialSearchResults: Vue.ref([]),
                    commonNameFormattingOptions: [
                        { label: 'First letter of each word uppercase', value: 'upper-each' },
                        { label: 'First letter uppercase', value: 'upper-first' },
                        { label: 'All uppercase', value: 'upper-all' },
                        { label: 'All lowercase', value: 'lower-all' }
                    ],
                    commonNameLanguageArr: Vue.ref([]),
                    commonNameLanguageIdArr: Vue.ref([]),
                    currentFamily: Vue.ref(null),
                    currentLocalChild: Vue.ref(null),
                    currentProcess: Vue.ref(null),
                    currentTaxonExternal: Vue.ref({}),
                    currentTaxonLocal: Vue.ref({}),
                    dataSource: Vue.ref('col'),
                    familyArr: Vue.ref([]),
                    importCommonNames: Vue.ref(false),
                    itisInitialSearchResults: Vue.ref([]),
                    kingdomId: Vue.ref(null),
                    kingdomName: Vue.ref(null),
                    languageArr: Vue.ref([]),
                    loading: Vue.ref(false),
                    nameTidIndex: Vue.ref({}),
                    newEditedTidArr: Vue.ref([]),
                    processCancelled: Vue.ref(false),
                    processingArr: Vue.ref([]),
                    processorDisplayArr: Vue.ref([]),
                    queueArr: Vue.ref([]),
                    rankArr: Vue.ref(null),
                    rebuildHierarchyLoop: Vue.ref(0),
                    requiredRanks: Vue.ref([10]),
                    selectedCommonNameFormatting: Vue.ref('upper-each'),
                    selectedRanks: Vue.ref([]),
                    selectedRanksHigh: Vue.ref(0),
                    setAddTaxaArr: Vue.ref([]),
                    targetTaxonIdentifier: Vue.ref(null),
                    targetTaxonLocal: Vue.ref(null),
                    taxaToAddArr: Vue.ref([]),
                    taxonomicGroup: Vue.ref(null),
                    taxonomicGroupTid: Vue.ref(null),
                    taxonSearchResults: Vue.ref([]),
                    updateAcceptance: Vue.ref(true)
                }
            },
            components: {
                'multiple-language-auto-complete': multipleLanguageAutoComplete,
                'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                'taxon-rank-checkbox-selector': taxonRankCheckboxSelector,
                'taxonomy-data-source-bullet-selector': taxonomyDataSourceBulletSelector
            },
            setup() {
                let procDisplayScrollAreaRef = Vue.ref(null);
                let procDisplayScrollHeight = Vue.ref(0);
                return {
                    tab: Vue.ref('importer'),
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
                this.setRankHigh();
            },
            methods: {
                addFamilyToFamilyArr(familyName){
                    const familyObj = {};
                    familyObj['name'] = familyName;
                    familyObj['processingArr'] = [];
                    familyObj['queueArr'] = [];
                    this.familyArr.push(familyObj);
                },
                addSubprocessToProcessorDisplay(type,text){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === this.currentProcess);
                    parentProcObj['subs'].push(this.getNewSubprocessObject(type,text));
                },
                addTaxonCommonName(tid,name,langid){
                    const formData = new FormData();
                    formData.append('action', 'addTaxonCommonName');
                    formData.append('tid', tid);
                    formData.append('name', name);
                    formData.append('langid', langid);
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    });
                },
                addTaxonIdentifier(tid,identifier){
                    const formData = new FormData();
                    formData.append('action', 'addTaxonIdentifier');
                    formData.append('tid', tid);
                    formData.append('idname', this.dataSource);
                    formData.append('id', identifier);
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    });
                },
                addTaxonToThesaurus(taxon,callback){
                    const rankId = Number(taxon['rankid']);
                    const newTaxonObj = {};
                    newTaxonObj['sciname'] = taxon['sciname'];
                    newTaxonObj['author'] = taxon['author'];
                    newTaxonObj['kingdomid'] = rankId > 10 ? this.kingdomId : '';
                    newTaxonObj['rankid'] = rankId;
                    newTaxonObj['acceptstatus'] = taxon.hasOwnProperty('acceptstatus') ? taxon['acceptstatus'] : 1;
                    newTaxonObj['tidaccepted'] = (taxon.hasOwnProperty('tidaccepted') && taxon['tidaccepted']) ? taxon['tidaccepted'] : '';
                    newTaxonObj['parenttid'] = taxon['parenttid'];
                    newTaxonObj['family'] = taxon['family'];
                    newTaxonObj['source'] = this.getDataSourceName();
                    newTaxonObj['source-name'] = this.dataSource;
                    newTaxonObj['source-id'] = taxon['id'];
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
                                taxon['tid'] = Number(res);
                                taxon['identifiers'] = [{
                                    name: this.dataSource,
                                    identifier: taxon['id']
                                }];
                                taxon['tidaccepted'] = newTaxonObj['tidaccepted'] !== '' ? Number(newTaxonObj['tidaccepted']) : taxon['tid'];
                                taxon['commonnames'] = [];
                                taxon['children'] = [];
                                callback(taxon);
                            }
                            else{
                                callback(null,'Error loading taxon');
                            }
                        });
                    });
                },
                adjustUIEnd(){
                    this.loading = false;
                },
                adjustUIStart(){
                    this.processorDisplayArr = [];
                    this.loading = true;
                },
                cancelProcess(){
                    this.processCancelled = true;
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
                },
                currentTaxonProcessAcceptance(){
                    if(Number(this.currentTaxonExternal['tidaccepted']) !== Number(this.currentTaxonLocal['tidaccepted'])){
                        this.processSubprocessSuccessResponse(false);
                        const subtext = 'Updating acceptance in Taxonomic Thesaurus';
                        this.addSubprocessToProcessorDisplay('text',subtext);
                        this.updateTaxonTidAccepted(this.currentTaxonExternal,(errorText = null) => {
                            if(errorText && errorText !== ''){
                                this.processSubprocessErrorResponse(errorText);
                                this.updateTaxonomicHierarchy(() => {
                                    this.adjustUIEnd();
                                });
                            }
                            else{
                                this.currentTaxonProcessParent();
                            }
                        });
                    }
                    else{
                        this.currentTaxonProcessParent();
                    }
                },
                currentTaxonProcessChildren(complete, text = null){
                    if(this.currentTaxonExternal['children'].length > 0){
                        this.processSubprocessSuccessResponse(false);
                        const subtext = 'Processing subtaxa';
                        this.addSubprocessToProcessorDisplay('text',subtext);
                        this.currentTaxonExternal['children'].forEach((child) => {
                            child['parenttid'] = this.currentTaxonExternal['tid'];
                            child['family'] = this.currentTaxonExternal['family'] !== '' ? this.currentTaxonExternal['family'] : '';
                            if(child['family'] === ''){
                                if(Number(child['rankid']) === 140){
                                    child['family'] = child['sciname'];
                                }
                                else{
                                    child['family'] = this.currentFamily;
                                }
                            }
                            child['tid'] = null;
                            child['tidaccepted'] = null;
                            const localChild = this.currentTaxonLocal['children'].find(lchild => lchild['sciname'] === child['sciname']);
                            if(localChild){
                                child['tid'] = localChild['tid'];
                                child['tidaccepted'] = localChild['tid'];
                                const index = this.currentTaxonLocal['children'].indexOf(localChild);
                                this.currentTaxonLocal['children'].splice(index,1);
                            }
                            if(Number(child['rankid']) <= 140){
                                this.queueArr.push(child);
                            }
                            else if(child['family'] !== ''){
                                let familyObj = this.familyArr.find(family => family['name'] === child['family']);
                                if(!familyObj){
                                    this.addFamilyToFamilyArr(child['family']);
                                    familyObj = this.familyArr.find(family => family['name'] === child['family']);
                                }
                                familyObj['queueArr'].push(child);
                            }
                        });
                    }
                    if(this.updateAcceptance && this.currentTaxonLocal['children'].length > 0){
                        const subtext = 'Updating acceptance for previously existing child taxa';
                        this.addSubprocessToProcessorDisplay('text',subtext);
                        this.currentTaxonProcessLocalChildren();
                    }
                    else{
                        this.processSubprocessSuccessResponse(true,'Complete');
                        this.processProcessingArrays();
                    }
                },
                currentTaxonProcessCommonNames(){
                    if(this.importCommonNames && this.currentTaxonExternal['commonnames'].length > 0){
                        this.processSubprocessSuccessResponse(false);
                        const subtext = 'Adding common names';
                        this.addSubprocessToProcessorDisplay('text',subtext);
                        this.currentTaxonExternal['commonnames'].forEach((commonname) => {
                            const existingName = this.currentTaxonLocal['commonnames'].length > 0 ? this.currentTaxonLocal['commonnames'].find(name => (name['commonname'].toLowerCase() === commonname['name'].toLowerCase() && Number(name['langid']) === Number(commonname['langid']))) : null;
                            if(!existingName){
                                this.addTaxonCommonName(this.currentTaxonExternal['tid'],commonname['name'],commonname['langid']);
                            }
                        });
                    }
                    this.currentTaxonProcessChildren();
                },
                currentTaxonProcessLocalChildren(){
                    if(this.updateAcceptance && this.currentTaxonLocal['children'].length > 0){
                        this.taxonSearchResults = [];
                        this.currentLocalChild = this.currentTaxonLocal['children'][0];
                        this.currentTaxonLocal['children'].splice(0, 1);
                        this.findExternalTaxonBySciname(this.currentLocalChild['sciname'],(errorText = null) => {
                            if(errorText){
                                this.currentTaxonProcessLocalChildren();
                            }
                            else{
                                this.validateExternalTaxonSearchResults(false);
                            }
                        });
                    }
                    else{
                        this.processSubprocessSuccessResponse(true,'Complete');
                        this.processProcessingArrays();
                    }
                },
                currentTaxonProcessMetadata(){
                    if(
                        this.currentTaxonExternal['author'] !== this.currentTaxonLocal['author'] ||
                        Number(this.currentTaxonExternal['rankid']) !== Number(this.currentTaxonLocal['rankid']) ||
                        this.currentTaxonExternal['family'] !== this.currentTaxonLocal['family']
                    ){
                        const taxonData = {};
                        taxonData['tid'] = this.currentTaxonExternal['tid'];
                        taxonData['author'] = this.currentTaxonExternal['author'];
                        taxonData['rankid'] = this.currentTaxonExternal['rankid'];
                        taxonData['family'] = this.currentTaxonExternal['family'];
                        taxonData['source'] = this.getDataSourceName();
                        this.editTaxonInThesaurus(taxonData,(errorText = null) => {
                            if(errorText){
                                this.processSubprocessErrorResponse(errorText);
                                this.updateTaxonomicHierarchy(() => {
                                    this.adjustUIEnd();
                                });
                            }
                            else{
                                this.currentTaxonProcessAcceptance();
                            }
                        });
                    }
                    else{
                        this.currentTaxonProcessAcceptance();
                    }
                },
                currentTaxonProcessParent(){
                    if(Number(this.currentTaxonExternal['parenttid']) !== Number(this.currentTaxonLocal['parenttid'])){
                        this.processSubprocessSuccessResponse(false);
                        const subtext = 'Updating parent taxon in Taxonomic Thesaurus';
                        this.addSubprocessToProcessorDisplay('text',subtext);
                        this.updateTaxonParent(this.currentTaxonExternal['parenttid'],this.currentTaxonExternal['tid'],(errorText = null) => {
                            if(errorText && errorText !== ''){
                                this.processSubprocessErrorResponse(errorText);
                                this.updateTaxonomicHierarchy(() => {
                                    this.adjustUIEnd();
                                });
                            }
                            else{
                                this.currentTaxonProcessCommonNames();
                            }
                        });
                    }
                    else{
                        this.currentTaxonProcessCommonNames();
                    }
                },
                currentTaxonValidate(){
                    if(this.currentTaxonExternal['tid']){
                        const subtext = 'Updating taxon in the Taxonomic Thesaurus';
                        this.addSubprocessToProcessorDisplay('text',subtext);
                        const dataSourceIdObj = this.currentTaxonLocal['identifiers'].find(obj => obj['name'] === this.dataSource);
                        if(!dataSourceIdObj){
                            this.addTaxonIdentifier(this.currentTaxonLocal['tid'],this.currentTaxonExternal['id']);
                        }
                        this.currentTaxonProcessMetadata();
                    }
                    else{
                        const subtext = 'Adding taxon to the Taxonomic Thesaurus';
                        this.addSubprocessToProcessorDisplay('text',subtext);
                        this.addTaxonToThesaurus(this.currentTaxonExternal,(newTaxon,errorText = null) => {
                            if(errorText){
                                this.processSubprocessErrorResponse(errorText);
                                this.updateTaxonomicHierarchy(() => {
                                    this.adjustUIEnd();
                                });
                            }
                            else{
                                const newTid = Number(newTaxon['tid']);
                                this.newEditedTidArr.push(newTid);
                                this.currentTaxonExternal['tid'] = newTid;
                                this.currentTaxonExternal['tidaccepted'] = newTid;
                                this.currentTaxonLocal = newTaxon;
                                this.currentTaxonProcessCommonNames();
                            }
                        });
                    }
                },
                editTaxonInThesaurus(taxonData,callback){
                    const tid = taxonData.hasOwnProperty('tid') ? taxonData['tid'] : null;
                    if(tid){
                        const formData = new FormData();
                        formData.append('tid', tid);
                        formData.append('taxonData', JSON.stringify(taxonData));
                        formData.append('action', 'editTaxon');
                        fetch(taxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.text().then((res) => {
                                if(res && res !== ''){
                                    callback(res);
                                }
                                else{
                                    callback();
                                }
                            });
                        });
                    }
                    else{
                        callback();
                    }
                },
                findCOLExternalTaxonChildren(callback){
                    if(this.childrenSearchPrimingArr.length > 0){
                        const currentId = this.childrenSearchPrimingArr[0];
                        this.childrenSearchPrimingArr.splice(0, 1);
                        const url = 'https://api.catalogueoflife.org/dataset/9840/tree/' + currentId + '/children?limit=100000';
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
                                    if(res.hasOwnProperty('total') && Number(res['total']) > 0){
                                        const resultArr = res['result'];
                                        resultArr.forEach((child) => {
                                            const rankname = child['rank'].toLowerCase();
                                            const rankid = this.rankArr.hasOwnProperty(rankname) ? Number(this.rankArr[rankname]) : null;
                                            if(rankid && this.selectedRanks.includes(rankid)){
                                                const newChildObj = {};
                                                newChildObj['id'] = child['id'];
                                                newChildObj['sciname'] = child['name'];
                                                newChildObj['author'] = child['authorship'];
                                                newChildObj['rankid'] = rankid;
                                                this.currentTaxonExternal['children'].push(newChildObj);
                                            }
                                            else if(!rankid || rankid <= this.selectedRanksHigh){
                                                this.childrenSearchPrimingArr.push(child['id']);
                                            }
                                        });
                                        this.findCOLExternalTaxonChildren(callback);
                                    }
                                    else{
                                        this.findCOLExternalTaxonChildren(callback);
                                    }
                                });
                            }
                            else{
                                this.findCOLExternalTaxonChildren(callback);
                            }
                        });
                    }
                    else{
                        this.processSubprocessSuccessResponse(false);
                        callback();
                    }
                },
                findCOLTaxonById(id,callback){
                    this.colInitialSearchResults = [];
                    const url = 'https://api.catalogueoflife.org/dataset/9840/taxon/' + id + '/info';
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
                                callback(res);
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            callback(text);
                        }
                    });
                },
                findCOLTaxonBySciname(sciname,callback){
                    this.colInitialSearchResults = [];
                    const url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&name=' + sciname;
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
                                this.processGetCOLTaxonByScinameResponse(res,callback);
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            callback(text);
                        }
                    });
                },
                findExternalTaxonBySciname(sciname,callback){
                    if(this.dataSource === 'col'){
                        this.findCOLTaxonBySciname(sciname,callback);
                    }
                    else if(this.dataSource === 'itis'){
                        this.findITISTaxonBySciname(sciname,callback);
                    }
                    else if(this.dataSource === 'worms'){
                        this.findWoRMSTaxonBySciname(sciname,callback);
                    }
                },
                findITISExternalTaxonChildren(callback){
                    if(this.childrenSearchPrimingArr.length > 0){
                        const currentId = this.childrenSearchPrimingArr[0];
                        this.childrenSearchPrimingArr.splice(0, 1);
                        const url = 'https://www.itis.gov/ITISWebService/jsonservice/getHierarchyDownFromTSN?tsn=' + currentId;
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
                                    if(res['hierarchyList'].length > 0){
                                        const resultArr = res['hierarchyList'];
                                        resultArr.forEach((child) => {
                                            if(child){
                                                const rankname = child['rankName'].toLowerCase();
                                                const rankid = this.rankArr.hasOwnProperty(rankname) ? Number(this.rankArr[rankname]) : null;
                                                if(rankid && this.selectedRanks.includes(rankid)){
                                                    const newChildObj = {};
                                                    newChildObj['id'] = child['tsn'];
                                                    newChildObj['sciname'] = child['taxonName'];
                                                    newChildObj['author'] = child['author'];
                                                    newChildObj['rankid'] = rankid;
                                                    this.currentTaxonExternal['children'].push(newChildObj);
                                                }
                                                else if(!rankid || rankid <= this.selectedRanksHigh){
                                                    this.childrenSearchPrimingArr.push(child['tsn']);
                                                }
                                            }
                                        });
                                        this.findITISExternalTaxonChildren(callback);
                                    }
                                    else{
                                        this.findITISExternalTaxonChildren(callback);
                                    }
                                });
                            }
                            else{
                                this.findITISExternalTaxonChildren(callback);
                            }
                        });
                    }
                    else{
                        this.processSubprocessSuccessResponse(false);
                        callback();
                    }
                },
                findITISTaxonBySciname(sciname,callback){
                    this.itisInitialSearchResults = [];
                    const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/searchByScientificName?srchKey=' + sciname;
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
                                if(res){
                                    this.processGetITISTaxonByScinameResponse(res,callback);
                                }
                                else{
                                    callback('Not found');
                                }
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            callback(text);
                        }
                    });
                },
                findTaxonBySciname(sciname,callback){
                    const formData = new FormData();
                    formData.append('action', 'getTaxonFromSciname');
                    formData.append('sciname', sciname);
                    formData.append('kingdomid', this.kingdomId);
                    formData.append('includeCommonNames', (this.importCommonNames ? '1' : '0'));
                    formData.append('includeChildren', '1');
                    fetch(taxonomyApiUrl, {
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
                            callback(null,text);
                        }
                    });
                },
                findTaxonByTid(tid,callback){
                    const formData = new FormData();
                    formData.append('action', 'getTaxonFromTid');
                    formData.append('tid', tid);
                    formData.append('includeCommonNames', (this.importCommonNames ? '1' : '0'));
                    formData.append('includeChildren', '1');
                    fetch(taxonomyApiUrl, {
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
                            callback(null,text);
                        }
                    });
                },
                findWoRMSExternalTaxonChildren(callback){
                    if(this.childrenSearchPrimingArr.length > 0){
                        const currentId = this.childrenSearchPrimingArr[0];
                        this.childrenSearchPrimingArr.splice(0, 1);
                        const url = 'https://www.marinespecies.org/rest/AphiaChildrenByAphiaID/' + currentId + '?marine_only=false';
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
                                    if(res.length > 0){
                                        res.forEach((child) => {
                                            const rankid = res.hasOwnProperty('taxonRankID') ? Number(res['taxonRankID']) : null;
                                            if(rankid && this.selectedRanks.includes(rankid)){
                                                const newChildObj = {};
                                                newChildObj['id'] = child['AphiaID'];
                                                newChildObj['sciname'] = child['scientificname'];
                                                newChildObj['author'] = child['authority'];
                                                newChildObj['rankid'] = rankid;
                                                this.currentTaxonExternal['children'].push(newChildObj);
                                            }
                                            else if(!rankid || rankid <= this.selectedRanksHigh){
                                                this.childrenSearchPrimingArr.push(child['AphiaID']);
                                            }
                                        });
                                        this.findWoRMSExternalTaxonChildren(callback);
                                    }
                                    else{
                                        this.findWoRMSExternalTaxonChildren(callback);
                                    }
                                });
                            }
                            else{
                                this.findWoRMSExternalTaxonChildren(callback);
                            }
                        });
                    }
                    else{
                        this.processSubprocessSuccessResponse(false);
                        callback();
                    }
                },
                findWoRMSTaxonBySciname(sciname,callback){
                    const url = 'https://www.marinespecies.org/rest/AphiaIDByName/' + sciname + '?marine_only=false';
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
                                    this.getWoRMSNameSearchResultsRecord(res,callback);
                                }
                                else{
                                    callback('Not found');
                                }
                            });
                        }
                        else if(response.status === 204){
                            callback('Not found');
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            callback(text);
                        }
                    });
                },
                getCOLExternalTaxonCommonNames(callback){
                    const url = 'https://api.catalogueoflife.org/dataset/9840/taxon/' + this.currentTaxonExternal['id'] + '/vernacular';
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
                                if(res.length > 0){
                                    res.forEach((cName) => {
                                        const langIso2Code = cName.hasOwnProperty('language') ? cName['language'] : null;
                                        const langObj = langIso2Code ? this.languageArr.find(lang => lang['iso-2'] === langIso2Code) : null;
                                        if(this.commonNameLanguageIdArr.length === 0 || (langObj && this.commonNameLanguageIdArr.includes(Number(langObj['langid'])))){
                                            const cNameObj = {};
                                            cNameObj['name'] = this.processCommonName(cName['name']);
                                            cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                            this.currentTaxonExternal['commonnames'].push(cNameObj);
                                        }
                                    });
                                }
                                this.processSubprocessSuccessResponse(false);
                                this.getExternalChildren(callback);
                            });
                        }
                        else{
                            this.processSubprocessSuccessResponse(false);
                            this.getExternalChildren(callback);
                        }
                    });
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
                getExternalChildren(callback){
                    const subtext = 'Getting subtaxa';
                    this.addSubprocessToProcessorDisplay('text',subtext);
                    this.childrenSearchPrimingArr = [];
                    this.childrenSearchPrimingArr.push(this.currentTaxonExternal['id']);
                    if(this.dataSource === 'col'){
                        this.findCOLExternalTaxonChildren(callback);
                    }
                    else if(this.dataSource === 'itis'){
                        this.findITISExternalTaxonChildren(callback);
                    }
                    else if(this.dataSource === 'worms'){
                        this.findWoRMSExternalTaxonChildren(callback);
                    }
                },
                getExternalCommonNames(callback){
                    if(this.importCommonNames && this.currentTaxonExternal['commonnames'].length === 0){
                        const subtext = 'Getting common names';
                        this.addSubprocessToProcessorDisplay('text',subtext);
                        if(this.dataSource === 'col'){
                            this.getCOLExternalTaxonCommonNames(callback);
                        }
                        else if(this.dataSource === 'itis'){
                            this.getITISExternalTaxonCommonNames(callback);
                        }
                        else if(this.dataSource === 'worms'){
                            this.getWoRMSExternalTaxonCommonNames(callback);
                        }
                    }
                    else{
                        this.getExternalChildren(callback);
                    }
                },
                getITISExternalTaxonCommonNames(callback){
                    const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/getCommonNamesFromTSN?tsn=' + this.currentTaxonExternal['id'];
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
                                if(res.hasOwnProperty('commonNames') && res['commonNames'].length > 0){
                                    res['commonNames'].forEach((cName) => {
                                        if(cName){
                                            const langObj = (cName.hasOwnProperty('language') && cName['language']) ? this.languageArr.find(lang => lang['name'] === cName['language']) : null;
                                            if(this.commonNameLanguageIdArr.length === 0 || (langObj && this.commonNameLanguageIdArr.includes(Number(langObj['langid'])))){
                                                const cNameObj = {};
                                                cNameObj['name'] = this.processCommonName(cName['commonName']);
                                                cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                                this.currentTaxonExternal['commonnames'].push(cNameObj);
                                            }
                                        }
                                    });
                                }
                                this.processSubprocessSuccessResponse(false);
                                this.getExternalChildren(callback);
                            });
                        }
                        else{
                            this.processSubprocessSuccessResponse(false);
                            this.getExternalChildren(callback);
                        }
                    });
                },
                getITISNameSearchResultsHierarchy(callback){
                    let id;
                    if(this.taxonSearchResults[0]['accepted']){
                        id = this.taxonSearchResults[0]['id'];
                    }
                    else{
                        id = this.taxonSearchResults[0]['accepted_id'];
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
                                let foundNameRank = this.taxonSearchResults[0]['rankid'];
                                if(!this.taxonSearchResults[0]['accepted']){
                                    const acceptedObj = resArr.find(rettaxon => rettaxon['taxonName'] === this.taxonSearchResults[0]['accepted_sciname']);
                                    foundNameRank = Number(this.rankArr[acceptedObj['rankName'].toLowerCase()]);
                                }
                                resArr.forEach((taxResult) => {
                                    if(taxResult['taxonName'] !== this.taxonSearchResults[0]['sciname']){
                                        const rankname = taxResult['rankName'].toLowerCase();
                                        const rankid = Number(this.rankArr[rankname]);
                                        if(rankid <= foundNameRank && this.selectedRanks.includes(rankid)){
                                            const resultObj = {};
                                            resultObj['id'] = taxResult['tsn'];
                                            resultObj['sciname'] = taxResult['taxonName'];
                                            resultObj['author'] = taxResult['author'] ? taxResult['author'] : '';
                                            resultObj['rankname'] = rankname;
                                            resultObj['rankid'] = rankid;
                                            if(rankname === 'family'){
                                                this.taxonSearchResults[0]['family'] = resultObj['sciname'];
                                            }
                                            if(!this.taxonSearchResults[0]['accepted'] && resultObj['sciname'] === this.taxonSearchResults[0]['accepted_sciname']){
                                                this.taxonSearchResults[0]['accepted_author'] = resultObj['author'];
                                                this.taxonSearchResults[0]['accepted_rankid'] = resultObj['rankid'];
                                            }
                                            hierarchyArr.push(resultObj);
                                        }
                                    }
                                });
                                this.taxonSearchResults[0]['hierarchy'] = hierarchyArr;
                                callback();
                            });
                        }
                        else{
                            callback('Unable to retrieve the parent taxon hierarchy');
                        }
                    });
                },
                getITISNameSearchResultsRecord(callback){
                    const id = this.taxonSearchResults[0]['id'];
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
                                this.taxonSearchResults[0]['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                                this.taxonSearchResults[0]['rankid'] = Number(taxonRankData['rankId']);
                                const scientificNameMetadata = resObj['scientificName'];
                                this.taxonSearchResults[0]['author'] = scientificNameMetadata['author'] ? scientificNameMetadata['author'] : '';
                                const coreMetadata = resObj['coreMetadata'];
                                const namestatus = coreMetadata['taxonUsageRating'];
                                this.taxonSearchResults[0]['accepted'] = namestatus === 'accepted' || namestatus === 'valid';
                                if(this.importCommonNames && resObj.hasOwnProperty('commonNameList')){
                                    this.taxonSearchResults[0]['commonnames'] = [];
                                    const commonNames = resObj['commonNameList']['commonNames'];
                                    commonNames.forEach((cName) => {
                                        if(cName){
                                            const langObj = (cName.hasOwnProperty('language') && cName['language']) ? this.languageArr.find(lang => lang['name'] === cName['language']) : null;
                                            if(this.commonNameLanguageIdArr.length === 0 || (langObj && this.commonNameLanguageIdArr.includes(Number(langObj['langid'])))){
                                                const cNameObj = {};
                                                cNameObj['name'] = this.processCommonName(cName['commonName']);
                                                cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                                this.taxonSearchResults[0]['commonnames'].push(cNameObj);
                                            }
                                        }
                                    });
                                }
                                if(this.taxonSearchResults[0]['accepted'] && this.taxonSearchResults[0]['rankid'] < 140){
                                    callback();
                                }
                                else{
                                    const acceptedNameList = resObj.hasOwnProperty('acceptedNameList') ? resObj['acceptedNameList'] : null;
                                    const acceptedNameArr = acceptedNameList ? acceptedNameList['acceptedNames'] : [];
                                    if(acceptedNameArr.length > 0 || (this.taxonSearchResults[0]['rankid'] >= 140 && !this.currentFamily)){
                                        if(!this.taxonSearchResults[0]['accepted'] && acceptedNameArr.length > 0){
                                            const acceptedName = acceptedNameArr[0];
                                            this.taxonSearchResults[0]['accepted_id'] = acceptedName['acceptedTsn'];
                                            this.taxonSearchResults[0]['accepted_sciname'] = acceptedName['acceptedName'];
                                        }
                                        this.getITISNameSearchResultsHierarchy(callback);
                                    }
                                    else{
                                        callback('Unable to distinguish the parent taxon by name');
                                    }
                                }
                            });
                        }
                        else{
                            callback('Unable to retrieve the parent taxon record');
                        }
                    });
                },
                getNewProcessObject(type,text){
                    const procObj = {
                        id: this.currentProcess,
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
                getNewSubprocessObject(type,text){
                    return {
                        procText: text,
                        type: type,
                        loading: true,
                        result: '',
                        resultText: ''
                    };
                },
                getWoRMSAddTaxonAuthor(res){
                    if(!this.processCancelled){
                        const id = this.setAddTaxaArr[0]['id'];
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
                                    const currentTaxon = this.setAddTaxaArr[0];
                                    currentTaxon['author'] = resObj['authority'] ? resObj['authority'] : '';
                                    if(this.setAddTaxaArr[0]['sciname'] === this.taxonSearchResults[0]['accepted_sciname']){
                                        this.taxonSearchResults[0]['accepted_author'] = currentTaxon['author'];
                                    }
                                    if(!res){
                                        this.taxaToAddArr.push(currentTaxon);
                                        this.setAddTaxaArr.splice(0, 1);
                                    }
                                    this.setTaxaToAdd();
                                });
                            }
                            else{
                                if(!res){
                                    const currentTaxon = this.setAddTaxaArr[0];
                                    this.taxaToAddArr.push(currentTaxon);
                                    this.setAddTaxaArr.splice(0, 1);
                                }
                                this.setTaxaToAdd();
                            }
                        });
                    }
                },
                getWoRMSExternalTaxonCommonNames(callback){
                    const url = 'https://www.marinespecies.org/rest/AphiaVernacularsByAphiaID/' + this.currentTaxonExternal['id'];
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
                                    if(res.length > 0){
                                        res.forEach((cName) => {
                                            const langIso2Code = cName.hasOwnProperty('language_code') ? cName['language_code'] : null;
                                            const langObj = langIso2Code ? this.languageArr.find(lang => lang['iso-2'] === langIso2Code) : null;
                                            if(this.commonNameLanguageIdArr.length === 0 || (langObj && this.commonNameLanguageIdArr.includes(Number(langObj['langid'])))){
                                                const cNameObj = {};
                                                cNameObj['name'] = this.processCommonName(cName['vernacular']);
                                                cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                                this.currentTaxonExternal['commonnames'].push(cNameObj);
                                            }
                                        });
                                    }
                                    this.processSubprocessSuccessResponse(false);
                                    this.getExternalChildren(callback);
                                });
                            }
                            else{
                                this.processSubprocessSuccessResponse(false);
                                this.getExternalChildren(callback);
                            }
                        });
                },
                getWoRMSNameSearchResultsHierarchy(callback){
                    let id;
                    if(this.taxonSearchResults[0]['accepted']){
                        id = this.taxonSearchResults[0]['id'];
                    }
                    else{
                        id = this.taxonSearchResults[0]['accepted_id'];
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
                                const foundNameRank = this.taxonSearchResults[0]['rankid'];
                                let childObj = resObj['child'];
                                const firstObj = {};
                                const firstrankname = childObj['rank'].toLowerCase();
                                const firstrankid = Number(this.rankArr[firstrankname]);
                                const newTaxonAccepted = this.taxonSearchResults[0]['accepted'];
                                firstObj['id'] = childObj['AphiaID'];
                                firstObj['sciname'] = childObj['scientificname'];
                                firstObj['author'] = '';
                                firstObj['rankname'] = firstrankname;
                                firstObj['rankid'] = firstrankid;
                                if(firstObj['sciname'] === this.taxonSearchResults[0]['accepted_sciname']){
                                    this.taxonSearchResults[0]['accepted_rankid'] = firstObj['rankid'];
                                }
                                hierarchyArr.push(firstObj);
                                let stopLoop = false;
                                while((childObj = childObj['child']) && !stopLoop){
                                    if(childObj['scientificname'] !== this.taxonSearchResults[0]['sciname']){
                                        const rankname = childObj['rank'].toLowerCase();
                                        const rankid = Number(this.rankArr[rankname]);
                                        if((newTaxonAccepted && rankid < foundNameRank && this.selectedRanks.includes(rankid)) || (!newTaxonAccepted && (childObj['scientificname'] === this.taxonSearchResults[0]['accepted_sciname'] || this.selectedRanks.includes(rankid)))){
                                            const resultObj = {};
                                            resultObj['id'] = childObj['AphiaID'];
                                            resultObj['sciname'] = childObj['scientificname'];
                                            resultObj['author'] = '';
                                            resultObj['rankname'] = rankname;
                                            resultObj['rankid'] = rankid;
                                            if(resultObj['sciname'] === this.taxonSearchResults[0]['accepted_sciname']){
                                                this.taxonSearchResults[0]['accepted_rankid'] = resultObj['rankid'];
                                            }
                                            if(rankname === 'family'){
                                                this.taxonSearchResults[0]['family'] = resultObj['sciname'];
                                            }
                                            hierarchyArr.push(resultObj);
                                        }
                                        if((newTaxonAccepted && rankid === foundNameRank) || (!newTaxonAccepted && childObj['scientificname'] === this.taxonSearchResults[0]['accepted_sciname'])){
                                            stopLoop = true;
                                        }
                                    }
                                }
                                this.taxonSearchResults[0]['hierarchy'] = hierarchyArr;
                                callback();
                            });
                        }
                        else{
                            callback('Unable to retrieve the parent taxon hierarchy');
                        }
                    });
                },
                getWoRMSNameSearchResultsRecord(id,callback){
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
                                if(resObj['kingdom'].toLowerCase() === this.kingdomName.toLowerCase() || resObj['scientificname'].toLowerCase() === this.kingdomName.toLowerCase()){
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
                                    this.taxonSearchResults.push(resultObj);
                                    if(resultObj['accepted'] && resultObj['rankid'] < 140){
                                        callback();
                                    }
                                    else{
                                        this.getWoRMSNameSearchResultsHierarchy(callback);
                                    }
                                }
                                else{
                                    callback('Not found');
                                }
                            });
                        }
                        else{
                            callback('Unable to retrieve the parent taxon record');
                        }
                    });
                },
                initializeCurrentTaxa(data){
                    this.currentTaxonExternal['id'] = data['id'];
                    this.currentTaxonExternal['sciname'] = data['sciname'];
                    this.currentTaxonExternal['author'] = (data.hasOwnProperty('author') && data['author']) ? data['author'] : '';
                    this.currentTaxonExternal['rankid'] = Number(data['rankid']);
                    this.currentTaxonExternal['family'] = data['family'];
                    if(this.currentTaxonExternal['family'] === '' && this.currentTaxonExternal['rankid'] === 140){
                        this.currentTaxonExternal['family'] = this.currentTaxonExternal['sciname'];
                    }
                    if(this.importCommonNames){
                        this.currentTaxonExternal['commonnames'] = [];
                    }
                    this.currentTaxonExternal['children'] = [];
                    this.currentTaxonExternal['tid'] = data['tid'];
                    this.currentTaxonExternal['parenttid'] = (data.hasOwnProperty('parenttid') && data['parenttid']) ? data['parenttid'] : null;
                    this.currentTaxonExternal['tidaccepted'] = (data.hasOwnProperty('tidaccepted') && data['tidaccepted']) ? data['tidaccepted'] : null;
                    const text = 'Processing ' + this.currentTaxonExternal['sciname'];
                    this.currentProcess = this.currentTaxonExternal['sciname'];
                    this.processorDisplayArr.push(this.getNewProcessObject('multi',text));
                    this.processSuccessResponse();
                    const callbackFunction = (resObj,errorText = null) => {
                        if(errorText){
                            this.updateTaxonomicHierarchy(() => {
                                this.adjustUIEnd();
                            });
                        }
                        else{
                            if(resObj){
                                this.currentTaxonLocal = resObj;
                                this.kingdomId = this.currentTaxonLocal['kingdomid'];
                                this.kingdomName = this.currentTaxonLocal['kingdom'];
                                this.currentTaxonExternal['tid'] = resObj['tid'];
                                this.currentTaxonExternal['tidaccepted'] = resObj['tidaccepted'];
                            }
                            this.getExternalCommonNames(() => {
                                this.currentTaxonValidate();
                            });
                        }
                    };
                    if(this.currentTaxonExternal['tid']){
                        this.findTaxonByTid(this.currentTaxonExternal['tid'],callbackFunction);
                    }
                    else{
                        this.findTaxonBySciname(this.currentTaxonExternal['sciname'],callbackFunction);
                    }
                },
                initializeImportUpdate(){
                    if(this.taxonomicGroupTid && this.selectedRanks.length > 0){
                        this.processCancelled = false;
                        this.adjustUIStart();
                        const text = 'Setting rank data';
                        this.currentProcess = 'setRankArr';
                        this.processorDisplayArr.push(this.getNewProcessObject('single',text));
                        const url = taxonomyApiUrl + '?action=getRankNameArr'
                        abortController = new AbortController();
                        fetch(url, {
                            signal: abortController.signal
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.json().then((resObj) => {
                                    this.processSuccessResponse('Complete');
                                    this.rankArr = resObj;
                                    if(this.importCommonNames){
                                        this.setLanguageArr();
                                    }
                                    else{
                                        this.setTargetTaxonLocal();
                                    }
                                });
                            }
                            else{
                                const text = getErrorResponseText(response.status,response.statusText);
                                this.processErrorResponse(text);
                            }
                        });
                    }
                    else if(this.taxonomicGroupTid){
                        alert('Please select the Taxonomic Ranks to be included in the import/update');
                    }
                    else{
                        alert('Please enter a Taxonomic Group to start an import/update');
                    }
                },
                populateTaxonomicHierarchy(callback){
                    if(this.rebuildHierarchyLoop < 40){
                        const formData = new FormData();
                        formData.append('tidarr', JSON.stringify(this.newEditedTidArr));
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
                                        this.populateTaxonomicHierarchy(callback);
                                    }
                                    else{
                                        this.processSuccessResponse('Complete');
                                        callback();
                                    }
                                });
                            }
                            else{
                                this.processErrorResponse('Error updating the taxonomic hierarchy');
                                callback('Error updating the taxonomic hierarchy');
                            }
                        });
                    }
                    else{
                        this.processErrorResponse('Error updating the taxonomic hierarchy');
                        callback('Error updating the taxonomic hierarchy');
                    }
                },
                primeTaxonomicHierarchy(callback){
                    this.rebuildHierarchyLoop = 0;
                    const formData = new FormData();
                    formData.append('tidarr', JSON.stringify(this.newEditedTidArr));
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
                                    this.populateTaxonomicHierarchy(callback);
                                }
                                else{
                                    this.processSuccessResponse('Complete');
                                    callback();
                                }
                            });
                        }
                        else{
                            this.processErrorResponse('Error updating the taxonomic hierarchy');
                            callback('Error updating the taxonomic hierarchy');
                        }
                    });
                },
                processAddTaxaArr(callback){
                    if(this.taxaToAddArr.length > 0){
                        const taxonToAdd = this.taxaToAddArr[0];
                        const rankId = Number(taxonToAdd['rankid']);
                        const text = 'Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus';
                        this.addSubprocessToProcessorDisplay('text',text);
                        taxonToAdd['parenttid'] = rankId > 10 ? this.nameTidIndex[taxonToAdd['parentName']] : 1;
                        this.addTaxonToThesaurus(taxonToAdd,(newTaxon,errorText = null) => {
                            if(errorText){
                                this.processSubprocessErrorResponse(errorText);
                                this.adjustUIEnd();
                                callback(errorText);
                            }
                            else{
                                const newTid = Number(newTaxon['tid']);
                                this.nameTidIndex[this.taxaToAddArr[0]['sciname']] = newTid;
                                this.newEditedTidArr.push(newTid);
                                this.taxaToAddArr.splice(0, 1);
                                this.processSubprocessSuccessResponse(false,'Complete');
                                this.processAddTaxaArr(callback);
                            }
                        });
                    }
                    else{
                        callback();
                    }
                },
                processCommonName(name){
                    if(this.selectedCommonNameFormatting === 'upper-each'){
                        const words = name.split(" ");
                        for(let i = 0; i < words.length; i++){
                            words[i] = words[i][0].toUpperCase() + words[i].substring(1).toLowerCase();
                        }
                        name = words.join(" ");
                    }
                    else if(this.selectedCommonNameFormatting === 'upper-first'){
                        name = name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
                    }
                    else if(this.selectedCommonNameFormatting === 'upper-all'){
                        name = name.toUpperCase();
                    }
                    else if(this.selectedCommonNameFormatting === 'lower-all'){
                        name = name.toLowerCase();
                    }
                    return name;
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
                processGetCOLTaxonByIdResponse(resObj,callback){
                    const taxResult = resObj['taxon'];
                    const nameData = taxResult['name'];
                    const resultObj = {};
                    resultObj['id'] = taxResult['id'];
                    resultObj['author'] = nameData.hasOwnProperty('authorship') ? nameData['authorship'] : '';
                    resultObj['rankname'] = nameData['rank'].toLowerCase();
                    resultObj['sciname'] = nameData['scientificName'];
                    resultObj['rankid'] = this.rankArr.hasOwnProperty(resultObj['rankname']) ? this.rankArr[resultObj['rankname']] : null;
                    resultObj['accepted'] = (taxResult['status'] === 'accepted');
                    this.colInitialSearchResults.push(resultObj);
                    this.validateCOLNameSearchResults(callback);
                },
                processGetCOLTaxonByScinameResponse(resObj,callback){
                    if(resObj['total_number_of_results'] > 0){
                        const resultArr = resObj['result'];
                        resultArr.forEach((taxResult) => {
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
                                    const acceptedAuthor = acceptedObj.hasOwnProperty('author') ? acceptedObj['author'] : '';
                                    resultObj['accepted'] = false;
                                    resultObj['accepted_id'] = acceptedObj['id'];
                                    resultObj['accepted_author'] = acceptedAuthor;
                                    resultHObj['id'] = acceptedObj['id'];
                                    resultHObj['author'] = acceptedAuthor;
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
                                    resultObj['accepted_rankid'] = resultHObj['rankid'];
                                    hierarchyArr.push(resultHObj);
                                    resultObj['hierarchy'] = hierarchyArr;
                                }
                                const existingObj = this.colInitialSearchResults.find(taxon => (taxon['sciname'] === resultObj['sciname'] && taxon['accepted_sciname'] === resultObj['accepted_sciname']));
                                if(!existingObj){
                                    this.colInitialSearchResults.push(resultObj);
                                }
                            }
                        });
                        if(this.colInitialSearchResults.length > 0){
                            this.validateCOLNameSearchResults(callback);
                        }
                        else{
                            callback('Not found');
                        }
                    }
                    else{
                        callback('Not found');
                    }
                },
                processGetITISTaxonByScinameResponse(resObj,callback){
                    this.itisInitialSearchResults = [];
                    const resultArr = resObj['scientificNames'];
                    if(resultArr && resultArr.length > 0 && resultArr[0]){
                        resultArr.forEach((taxResult) => {
                            if(taxResult['combinedName'] === this.taxonomicGroup.name && (taxResult['kingdom'].toLowerCase() === this.kingdomName.toLowerCase() || taxResult['combinedName'].toLowerCase() === this.kingdomName.toLowerCase())){
                                const resultObj = {};
                                resultObj['id'] = taxResult['tsn'];
                                resultObj['sciname'] = taxResult['combinedName'];
                                this.itisInitialSearchResults.push(resultObj);
                            }
                        });
                        if(this.itisInitialSearchResults.length === 1){
                            this.taxonSearchResults = this.itisInitialSearchResults;
                            this.getITISNameSearchResultsRecord(callback);
                        }
                        else if(this.itisInitialSearchResults.length === 0){
                            callback('Not found');
                        }
                        else if(this.itisInitialSearchResults.length > 1){
                            this.validateITISNameSearchResults(callback);
                        }
                    }
                    else{
                        callback('Not found');
                    }
                },
                processLocalChildSearch(){
                    this.currentLocalChild['tidaccepted'] = this.nameTidIndex[this.taxonSearchResults[0]['accepted_sciname']];
                    this.updateTaxonTidAccepted(this.currentLocalChild,(errorText = null) => {
                        if(errorText && errorText !== ''){
                            this.processErrorResponse(errorText);
                        }
                        else{
                            this.processSuccessResponse('Complete');
                        }
                        this.currentTaxonProcessLocalChildren();
                    });
                },
                processProcessingArrays(){
                    if(this.processCancelled){
                        this.updateTaxonomicHierarchy(() => {
                            this.adjustUIEnd();
                        });
                    }
                    else if(this.processingArr.length > 0){
                        this.initializeCurrentTaxa(this.processingArr[0]);
                        this.processingArr.splice(0, 1);
                    }
                    else if(this.queueArr.length > 0){
                        this.processingArr = this.processingArr.concat(this.queueArr);
                        this.queueArr = [];
                        this.updateTaxonomicHierarchy((errorText = null) => {
                            if(errorText){
                                this.adjustUIEnd();
                            }
                            else{
                                this.newEditedTidArr = [];
                                this.initializeCurrentTaxa(this.processingArr[0]);
                                this.processingArr.splice(0, 1);
                            }
                        });
                    }
                    else if(this.familyArr.length > 0){
                        if(this.familyArr[0]['processingArr'].length > 0 || this.familyArr[0]['queueArr'].length > 0){
                            this.currentFamily = this.familyArr[0]['name'];
                            if(this.familyArr[0]['processingArr'].length > 0){
                                this.initializeCurrentTaxa(this.familyArr[0]['processingArr'][0]);
                                this.familyArr[0]['processingArr'].splice(0, 1);
                            }
                            else if(this.familyArr[0]['queueArr'].length > 0){
                                this.familyArr[0]['processingArr'] = this.familyArr[0]['processingArr'].concat(this.familyArr[0]['queueArr']);
                                this.familyArr[0]['queueArr'] = [];
                                this.updateTaxonomicHierarchy((errorText = null) => {
                                    if(errorText){
                                        this.adjustUIEnd();
                                    }
                                    else{
                                        this.newEditedTidArr = [];
                                        this.initializeCurrentTaxa(this.familyArr[0]['processingArr'][0]);
                                        this.familyArr[0]['processingArr'].splice(0, 1);
                                    }
                                });
                            }
                        }
                        else{
                            this.familyArr.splice(0, 1);
                            this.processProcessingArrays();
                        }
                    }
                    else{
                        this.updateTaxonomicHierarchy(() => {
                            this.adjustUIEnd();
                        });
                    }
                },
                processSubprocessErrorResponse(text){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === this.currentProcess);
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
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === this.currentProcess);
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
                processSuccessResponse(text = null){
                    const procObj = this.processorDisplayArr.find(proc => proc['current'] === true);
                    if(procObj){
                        procObj['current'] = false;
                        if(procObj['loading'] === true){
                            procObj['loading'] = false;
                            procObj['result'] = 'success';
                            procObj['resultText'] = text;
                        }
                    }
                },
                setInitialTaxa(){
                    this.currentTaxonExternal['id'] = this.taxonSearchResults[0]['accepted'] ? this.taxonSearchResults[0]['id'] : this.taxonSearchResults[0]['accepted_id'];
                    this.currentTaxonExternal['sciname'] = this.taxonSearchResults[0]['accepted'] ? this.taxonSearchResults[0]['sciname'] : this.taxonSearchResults[0]['accepted_sciname'];
                    this.currentTaxonExternal['author'] = this.taxonSearchResults[0]['accepted'] ? this.taxonSearchResults[0]['author'] : this.taxonSearchResults[0]['accepted_author'];
                    this.currentTaxonExternal['rankid'] = this.taxonSearchResults[0]['accepted'] ? this.taxonSearchResults[0]['rankid'] : this.taxonSearchResults[0]['accepted_rankid'];
                    this.currentTaxonExternal['family'] = this.taxonSearchResults[0].hasOwnProperty('family') ? this.taxonSearchResults[0]['family'] : '';
                    if(this.currentTaxonExternal['family'] === '' && this.currentTaxonExternal['rankid'] === 140){
                        this.currentTaxonExternal['family'] = this.currentTaxonExternal['sciname'];
                    }
                    if(this.currentTaxonExternal['family'] !== ''){
                        this.currentFamily = this.currentTaxonExternal['family'];
                        this.addFamilyToFamilyArr(this.currentTaxonExternal['family']);
                    }
                    if(this.importCommonNames){
                        this.currentTaxonExternal['commonnames'] = this.taxonSearchResults[0].hasOwnProperty('commonnames') ? this.taxonSearchResults[0]['commonnames'] : [];
                    }
                    this.currentTaxonExternal['children'] = [];
                    this.currentTaxonExternal['tid'] = null;
                    this.currentTaxonExternal['parenttid'] = null;
                    this.currentTaxonExternal['tidaccepted'] = null;
                    const text = 'Processing ' + this.currentTaxonExternal['sciname'];
                    this.currentProcess = this.currentTaxonExternal['sciname'];
                    this.processorDisplayArr.push(this.getNewProcessObject('multi',text));
                    this.processSuccessResponse();
                    if(this.targetTaxonLocal['sciname'] === this.currentTaxonExternal['sciname']){
                        this.currentTaxonExternal['tid'] = this.targetTaxonLocal['tid'];
                        this.currentTaxonExternal['parenttid'] = this.targetTaxonLocal['parenttid'];
                        this.currentTaxonExternal['tidaccepted'] = this.targetTaxonLocal['tidaccepted'];
                        this.currentTaxonLocal['tid'] = this.targetTaxonLocal['tid'];
                        this.currentTaxonLocal['sciname'] = this.targetTaxonLocal['sciname'];
                        this.currentTaxonLocal['author'] = this.targetTaxonLocal['author'];
                        this.currentTaxonLocal['rankid'] = this.targetTaxonLocal['rankid'];
                        this.currentTaxonLocal['family'] = this.targetTaxonLocal['family'];
                        this.currentTaxonLocal['tidaccepted'] = this.targetTaxonLocal['tidaccepted'];
                        this.currentTaxonLocal['parenttid'] = this.targetTaxonLocal['parenttid'];
                        this.currentTaxonLocal['identifiers'] = this.targetTaxonLocal['identifiers'];
                        if(this.importCommonNames){
                            this.currentTaxonLocal['commonnames'] = this.targetTaxonLocal['commonnames'];
                        }
                        this.currentTaxonLocal['children'] = this.targetTaxonLocal['children'];
                        this.getExternalCommonNames((errorText = null) => {
                            if(errorText){
                                this.adjustUIEnd();
                            }
                            else{
                                this.currentTaxonValidate();
                            }
                        });
                    }
                    else{
                        this.findTaxonByTid(this.targetTaxonLocal['tidaccepted'],(resObj,errorText = null) => {
                            if(errorText){
                                this.adjustUIEnd();
                            }
                            else{
                                if(resObj){
                                    this.currentTaxonLocal = resObj;
                                    this.kingdomId = this.currentTaxonLocal['kingdomid'];
                                    this.kingdomName = this.currentTaxonLocal['kingdom'];
                                    this.currentTaxonExternal['tid'] = resObj['tid'];
                                    this.currentTaxonExternal['parenttid'] = resObj['parenttid'];
                                    this.currentTaxonExternal['tidaccepted'] = resObj['tidaccepted'];
                                }
                                this.getExternalCommonNames((errorText = null) => {
                                    if(errorText){
                                        this.adjustUIEnd();
                                    }
                                    else{
                                        this.currentTaxonValidate();
                                    }
                                });
                            }
                        });
                    }
                },
                setLanguageArr(){
                    const text = 'Setting language data';
                    this.currentProcess = 'setLanguageArr';
                    this.processorDisplayArr.push(this.getNewProcessObject('single',text));
                    const url = languageApiUrl + '?action=getLanguages'
                    abortController = new AbortController();
                    fetch(url, {
                        signal: abortController.signal
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.json().then((resObj) => {
                                this.processSuccessResponse('Complete');
                                this.languageArr = resObj;
                                this.setTargetTaxonLocal();
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            this.processErrorResponse(text);
                        }
                    });
                },
                setRankHigh() {
                    this.selectedRanks.forEach((rank) => {
                        if(rank > this.selectedRanksHigh){
                            this.selectedRanksHigh = rank;
                        }
                    });
                },
                setTargetSynonymy(){
                    const text = 'Updating target taxonomic group accepted parent taxon';
                    this.currentProcess = 'updateTargetAcceptedParent';
                    this.processorDisplayArr.push(this.getNewProcessObject('single',text));
                    if(!this.targetTaxonLocal['tidaccepted'] || this.targetTaxonLocal['tidaccepted'] === ''){
                        this.targetTaxonLocal['tidaccepted'] = this.nameTidIndex[this.taxonSearchResults[0]['accepted_sciname']];
                    }
                    this.updateTaxonTidAccepted(this.targetTaxonLocal,(errorText = null) => {
                        if(errorText && errorText !== ''){
                            this.processErrorResponse(errorText);
                            this.adjustUIEnd();
                        }
                        else{
                            this.processSuccessResponse('Complete');
                            this.setInitialTaxa();
                        }
                    });
                },
                setTargetTaxonLocal(){
                    const text = 'Setting the parent taxon for the taxonomic group from the Taxonomic Thesaurus';
                    this.currentProcess = 'setTargetTaxonLocal';
                    this.processorDisplayArr.push(this.getNewProcessObject('single',text));
                    this.findTaxonByTid(this.taxonomicGroupTid,(resObj,errorText = null) => {
                        if(errorText){
                            this.processErrorResponse(errorText);
                            this.adjustUIEnd();
                        }
                        else{
                            this.targetTaxonLocal = resObj;
                            this.kingdomName = this.targetTaxonLocal['kingdom'];
                            this.processSuccessResponse('Complete');
                            const text = 'Finding the parent taxon for the taxonomic group from the selected Data Source';
                            this.currentProcess = 'setTargetTaxonExternal';
                            this.processorDisplayArr.push(this.getNewProcessObject('single',text));
                            const dataSourceIdObj = this.targetTaxonLocal['identifiers'].find(obj => obj['name'] === this.dataSource);
                            if(dataSourceIdObj){
                                this.targetTaxonIdentifier = dataSourceIdObj['identifier'];
                                if(this.dataSource === 'col'){
                                    this.findCOLTaxonById(this.targetTaxonIdentifier,(res,errorText = null) => {
                                        if(errorText){
                                            this.processErrorResponse(errorText);
                                            this.adjustUIEnd();
                                        }
                                        else{
                                            if(res.hasOwnProperty('taxon')){
                                                this.processGetCOLTaxonByIdResponse(res,(errorText = null) => {
                                                    if(errorText){
                                                        this.processErrorResponse(errorText);
                                                        this.adjustUIEnd();
                                                    }
                                                    else{
                                                        this.validateExternalTaxonSearchResults(true);
                                                    }
                                                });
                                            }
                                            else{
                                                this.findExternalTaxonBySciname(this.taxonomicGroup.name,(errorText = null) => {
                                                    if(errorText){
                                                        this.processErrorResponse(errorText);
                                                        this.adjustUIEnd();
                                                    }
                                                    else{
                                                        this.validateExternalTaxonSearchResults(true);
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                                else if(this.dataSource === 'itis'){
                                    const resultObj = {};
                                    resultObj['id'] = this.targetTaxonIdentifier;
                                    resultObj['sciname'] = this.taxonomicGroup.name;
                                    this.taxonSearchResults.push(resultObj);
                                    this.getITISNameSearchResultsRecord((errorText = null) => {
                                        if(errorText){
                                            this.processErrorResponse(errorText);
                                            this.adjustUIEnd();
                                        }
                                        else{
                                            this.validateExternalTaxonSearchResults(true);
                                        }
                                    });
                                }
                                else if(this.dataSource === 'worms'){
                                    this.getWoRMSNameSearchResultsRecord(this.targetTaxonIdentifier,(errorText = null) => {
                                        if(errorText){
                                            this.processErrorResponse(errorText);
                                            this.adjustUIEnd();
                                        }
                                        else{
                                            this.validateExternalTaxonSearchResults(true);
                                        }
                                    });
                                }
                            }
                            else{
                                this.findExternalTaxonBySciname(this.taxonomicGroup.name,(errorText = null) => {
                                    if(errorText){
                                        this.processErrorResponse(errorText);
                                        this.adjustUIEnd();
                                    }
                                    else{
                                        this.validateExternalTaxonSearchResults(true);
                                    }
                                });
                            }
                        }
                    });
                },
                setTaxaToAdd(callback){
                    if(this.setAddTaxaArr.length > 0){
                        const sciname = this.setAddTaxaArr[0]['sciname'];
                        const url = CLIENT_ROOT + '/api/taxa/gettid.php';
                        const formData = new FormData();
                        formData.append('sciname', sciname);
                        formData.append('kingdomid', this.kingdomId);
                        fetch(url, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                response.text().then((res) => {
                                    if(this.dataSource === 'worms' && (!res || this.setAddTaxaArr[0]['sciname'] === this.taxonSearchResults[0]['accepted_sciname'])){
                                        this.getWoRMSAddTaxonAuthor(res);
                                    }
                                    else{
                                        const currentTaxon = this.setAddTaxaArr[0];
                                        if(res){
                                            this.nameTidIndex[currentTaxon['sciname']] = Number(res);
                                        }
                                        else{
                                            this.taxaToAddArr.push(currentTaxon);
                                        }
                                        this.setAddTaxaArr.splice(0, 1);
                                        this.setTaxaToAdd(callback);
                                    }
                                });
                            }
                        });
                    }
                    else{
                        this.processAddTaxaArr(callback);
                    }
                },
                updateCommonNameLanguageArr(langObj) {
                    this.commonNameLanguageIdArr = [];
                    this.commonNameLanguageArr = langObj;
                    this.commonNameLanguageArr.forEach((lang) => {
                        this.commonNameLanguageIdArr.push(Number(lang['id']));
                    });

                },
                updateSelectedDataSource(dataSourceObj) {
                    this.dataSource = dataSourceObj;
                },
                updateSelectedRanks(selectedArr) {
                    this.selectedRanks = selectedArr;
                    this.setRankHigh();
                },
                updateTaxonomicGroup(taxonObj) {
                    this.taxonomicGroup = taxonObj;
                    this.taxonomicGroupTid = taxonObj ? taxonObj.tid : null;
                    this.kingdomId = taxonObj ? taxonObj.kingdomid : null;
                },
                updateTaxonomicHierarchy(callback){
                    if(this.newEditedTidArr.length > 0){
                        const text = 'Updating taxonomic hierarchy table with new and edited taxa';
                        this.currentProcess = 'updateTaxonomicHierarchy';
                        this.processorDisplayArr.push(this.getNewProcessObject('single',text));
                        this.rebuildHierarchyLoop = 0;
                        const formData = new FormData();
                        formData.append('tidarr', JSON.stringify(this.newEditedTidArr));
                        formData.append('action', 'clearHierarchyTable');
                        fetch(taxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            if(response.status === 200){
                                this.primeTaxonomicHierarchy(callback);
                            }
                            else{
                                this.processErrorResponse('Error updating the taxonomic hierarchy');
                                callback('Error updating the taxonomic hierarchy');
                            }
                        });
                    }
                    else{
                        callback();
                    }
                },
                updateTaxonParent(parenttid,tid,callback){
                    const formData = new FormData();
                    formData.append('action', 'editTaxonParent');
                    formData.append('tid', tid);
                    formData.append('parenttid', parenttid);
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.text().then((res) => {
                                if(res && res !== ''){
                                    callback(res);
                                }
                                else{
                                    this.newEditedTidArr.push(tid);
                                    callback();
                                }
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            callback(text);
                        }
                    });
                },
                updateTaxonTidAccepted(taxon,callback){
                    const kingdom = Number(taxon['rankid']) === 10;
                    const formData = new FormData();
                    formData.append('action', 'updateTaxonTidAccepted');
                    formData.append('tid', taxon['tid']);
                    formData.append('tidaccepted', taxon['tidaccepted']);
                    formData.append('kingdom', (kingdom ? '1' : '0'));
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.text().then((res) => {
                                if(res && res !== ''){
                                    callback(res);
                                }
                                else{
                                    callback();
                                }
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            callback(text);
                        }
                    });
                },
                validateCOLNameSearchResults(callback){
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
                                    if(kingdomName.toLowerCase() === this.kingdomName.toLowerCase()){
                                        let hierarchyArr = [];
                                        if(taxon.hasOwnProperty('hierarchy')){
                                            hierarchyArr = taxon['hierarchy'];
                                        }
                                        resArr.forEach((taxResult) => {
                                            if(taxResult['name'] !== taxon['sciname']){
                                                const rankname = taxResult['rank'].toLowerCase();
                                                const rankid = Number(this.rankArr[rankname]);
                                                if(this.selectedRanks.includes(rankid) || (!taxon['accepted'] && taxon['accepted_sciname'] === taxResult['name'])){
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
                                        this.taxonSearchResults.push(taxon);
                                    }
                                    this.validateCOLNameSearchResults(callback);
                                });
                            }
                            else{
                                this.validateCOLNameSearchResults(callback);
                            }
                        });
                    }
                    else if(this.taxonSearchResults.length === 1){
                        callback();
                    }
                    else if(this.taxonSearchResults.length === 0){
                        callback('Not found');
                    }
                    else if(this.taxonSearchResults.length > 1){
                        callback('Unable to distinguish the parent taxon by name');
                    }
                },
                validateExternalTaxonSearchResults(target = false){
                    this.setAddTaxaArr = [];
                    this.taxaToAddArr = [];
                    if(this.taxonSearchResults.length === 1){
                        if(!this.taxonSearchResults[0]['accepted'] && !this.taxonSearchResults[0]['accepted_sciname']){
                            if(target){
                                this.processErrorResponse('Unable to distinguish the parent taxon accepted name');
                                this.adjustUIEnd();
                            }
                            else{
                                this.currentTaxonProcessLocalChildren();
                            }
                        }
                        else{
                            this.processSuccessResponse('Complete');
                            if(!this.targetTaxonIdentifier){
                                this.addTaxonIdentifier(this.taxonomicGroupTid,this.taxonSearchResults[0]['id']);
                                this.targetTaxonLocal['identifiers'].push({
                                    name: this.dataSource,
                                    identifier: this.taxonSearchResults[0]['id']
                                });
                            }
                            if(!this.taxonSearchResults[0]['accepted']){
                                let callbackFunction;
                                if(target){
                                    callbackFunction = (errorText = null) => {
                                        if(errorText){
                                            this.processSubprocessErrorResponse(errorText);
                                            this.adjustUIEnd();
                                        }
                                        else{
                                            this.processSubprocessSuccessResponse(true);
                                            this.setTargetSynonymy();
                                        }
                                    };
                                }
                                else{
                                    callbackFunction = (errorText = null) => {
                                        if(errorText){
                                            this.processSubprocessErrorResponse(errorText);
                                            this.adjustUIEnd();
                                        }
                                        else{
                                            this.processSubprocessSuccessResponse(true);
                                            this.processLocalChildSearch();
                                        }
                                    };
                                }
                                const text = 'Processing accepted name and hierarchy';
                                this.currentProcess = 'setAcceptedTargetTaxonNameHierarchy';
                                this.processorDisplayArr.push(this.getNewProcessObject('multi',text));
                                const addHierchyTemp = this.taxonSearchResults[0]['hierarchy'];
                                addHierchyTemp.sort((a, b) => {
                                    return a.rankid - b.rankid;
                                });
                                let parentName = addHierchyTemp[0]['sciname'];
                                addHierchyTemp.forEach((taxon) => {
                                    if(taxon['sciname'] !== parentName){
                                        taxon['parentName'] = parentName;
                                        taxon['family'] = taxon['rankid'] >= 140 ? this.taxonSearchResults[0]['family'] : null;
                                        parentName = taxon['sciname'];
                                        if(!this.taxonSearchResults[0]['accepted'] && taxon['sciname'] === this.taxonSearchResults[0]['accepted_sciname']){
                                            this.taxonSearchResults[0]['parentName'] = taxon['parentName'];
                                        }
                                    }
                                });
                                if(!this.taxonSearchResults[0].hasOwnProperty('parentName') || this.taxonSearchResults[0]['parentName'] === ''){
                                    this.taxonSearchResults[0]['parentName'] = parentName;
                                }
                                this.setAddTaxaArr = addHierchyTemp;
                                this.setTaxaToAdd(callbackFunction);
                            }
                            else{
                                if(target){
                                    this.setTargetSynonymy();
                                }
                                else{
                                    this.processLocalChildSearch();
                                }
                            }
                        }
                    }
                    else{
                        if(target){
                            this.processErrorResponse('Unable to distinguish the parent taxon accepted name');
                            this.adjustUIEnd();
                        }
                        else{
                            this.currentTaxonProcessLocalChildren();
                        }
                    }
                },
                validateITISNameSearchResults(callback){
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
                                    taxon['author'] = '';
                                    if(resObj.hasOwnProperty('taxonAuthor')){
                                        const authorMetadata = resObj['taxonAuthor'];
                                        taxon['author'] = authorMetadata.hasOwnProperty('authorship') ? authorMetadata['authorship'] : '';
                                    }
                                    if(this.importCommonNames && resObj.hasOwnProperty('commonNameList')){
                                        taxon['commonnames'] = [];
                                        const commonNames = resObj['commonNameList']['commonNames'];
                                        commonNames.forEach((cName) => {
                                            const langObj = this.languageArr.find(lang => lang['name'] === cName['language']);
                                            if(this.commonNameLanguageIdArr.length === 0 || (langObj && this.commonNameLanguageIdArr.includes(Number(langObj['langid'])))){
                                                const cNameObj = {};
                                                cNameObj['name'] = this.processCommonName(cName['commonName']);
                                                cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                                taxon['commonnames'].push(cNameObj);
                                            }
                                        });
                                    }
                                    if(namestatus === 'accepted'){
                                        const taxonRankData = resObj['taxRank'];
                                        taxon['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                                        taxon['rankid'] = Number(taxonRankData['rankId']);
                                        taxon['accepted'] = true;
                                        this.taxonSearchResults.push(taxon);
                                    }
                                    this.validateITISNameSearchResults(callback);
                                });
                            }
                            else{
                                callback('Unable to retrieve the parent taxon record');
                            }
                        });
                    }
                    else if(this.taxonSearchResults.length === 1){
                        if(!this.taxonSearchResults[0]['accepted'] || (this.itisInitialSearchResults[0]['rankid'] >= 140 && !this.currentFamily)){
                            this.getITISNameSearchResultsHierarchy(callback);
                        }
                        else{
                            callback();
                        }
                    }
                    else if(this.taxonSearchResults.length === 0){
                        callback('Not found');
                    }
                    else if(this.taxonSearchResults.length > 1){
                        callback('Unable to distinguish the parent taxon by name');
                    }
                },
                cancelAPIRequest,
                getErrorResponseText
            }
        });
        taxonomicThesaurusManagerModule.use(Quasar, { config: {} });
        taxonomicThesaurusManagerModule.mount('#innertext');
    </script>
</body>
</html>
