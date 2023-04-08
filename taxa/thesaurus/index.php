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
                                            <taxon-rank-checkbox-selector :selected-ranks="selectedRanks" :kingdom-id="kingdomId" :disable="loading" link-label="Select Taxonomic Ranks" inner-label="Select taxonomic ranks for taxa to be included in import or update" @update:selected-ranks="updateSelectedRanks"></taxon-rank-checkbox-selector>
                                        </div>
                                        <div class="q-my-sm">
                                            <taxonomy-data-source-bullet-selector :disable="loading" :selected-data-source="dataSource" @update:selected-data-source="updateSelectedDataSource"></taxonomy-data-source-bullet-selector>
                                        </div>
                                        <q-card class="q-my-sm" flat bordered>
                                            <q-card-section>
                                                <div>
                                                    <q-checkbox v-model="updateAcceptance" label="Update accepted taxa for synonymized names" :disable="loading" />
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
                                                                        <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="q-ml-sm text-weight-bold text-green-9">
                                                                            <span class="q-ml-sm text-weight-bold text-green-9">{{subproc.resultText}}</span>
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
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonRankCheckboxSelector.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceBulletSelector.js" type="text/javascript"></script>
    <script>
        const taxonomicThesaurusManagerModule = Vue.createApp({
            data() {
                return {
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
                    currentProcess: Vue.ref(null),
                    currentTaxonExternal: Vue.ref({}),
                    currentTaxonLocal: Vue.ref({}),
                    dataSource: Vue.ref('col'),
                    importCommonNames: Vue.ref(false),
                    itisInitialSearchResults: Vue.ref([]),
                    kingdomId: Vue.ref(null),
                    kingdomName: Vue.ref(null),
                    languageArr: Vue.ref([]),
                    loading: Vue.ref(false),
                    nameTidIndex: Vue.ref({}),
                    newTidArr: Vue.ref([]),
                    processCancelled: Vue.ref(false),
                    processingArr: Vue.ref([]),
                    processorDisplayArr: Vue.ref([]),
                    rankArr: Vue.ref(null),
                    selectedCommonNameFormatting: Vue.ref('upper-each'),
                    selectedRanks: Vue.ref([]),
                    selectedRanksHigh: Vue.ref(0),
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
                addSubprocessToProcessorDisplay(type,text){
                    const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === this.currentProcess);
                    parentProcObj['subs'].push(this.getNewSubprocessObject(type,text));
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
                    newTaxonObj['tidaccepted'] = taxon.hasOwnProperty('tidaccepted') ? taxon['tidaccepted'] : '';
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
                            callback(null,text);
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
                findTargetTaxonExternalBySciname(){
                    if(this.dataSource === 'col'){
                        this.findCOLTaxonBySciname(this.taxonomicGroup.name,(errorText = null) => {
                            if(errorText){
                                this.processErrorResponse(errorText);
                                this.adjustUIEnd();
                            }
                            else{
                                this.validateTargetTaxonSearchResults();
                            }
                        });
                    }
                    else if(this.dataSource === 'itis'){
                        this.findITISTaxonBySciname(this.taxonomicGroup.name,(errorText = null) => {
                            if(errorText){
                                this.processErrorResponse(errorText);
                                this.adjustUIEnd();
                            }
                            else{
                                this.validateTargetTaxonSearchResults();
                            }
                        });
                    }
                    else if(this.dataSource === 'worms'){
                        this.findWoRMSTaxonBySciname(this.taxonomicGroup.name,(errorText = null) => {
                            if(errorText){
                                this.processErrorResponse(errorText);
                                this.adjustUIEnd();
                            }
                            else{
                                this.validateTargetTaxonSearchResults();
                            }
                        });
                    }
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
                                        const langObj = this.languageArr.find(lang => lang['name'] === cName['language']);
                                        if(this.commonNameLanguageIdArr.length === 0 || (langObj && this.commonNameLanguageIdArr.includes(Number(langObj['langid'])))){
                                            const cNameObj = {};
                                            cNameObj['name'] = cName['commonName'];
                                            cNameObj['langid'] = langObj ? Number(langObj['langid']) : null;
                                            this.taxonSearchResults[0]['commonnames'].push(cNameObj);
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
                                    if(this.processingArr[0]['sciname'] === this.taxonSearchResults[0]['accepted_sciname']){
                                        this.taxonSearchResults[0]['accepted_author'] = currentTaxon['author'];
                                    }
                                    if(!res){
                                        this.taxaToAddArr.push(currentTaxon);
                                        this.processingArr.splice(0, 1);
                                    }
                                    this.setTaxaToAdd();
                                });
                            }
                            else{
                                if(!res){
                                    const currentTaxon = this.processingArr[0];
                                    this.taxaToAddArr.push(currentTaxon);
                                    this.processingArr.splice(0, 1);
                                }
                                this.setTaxaToAdd();
                            }
                        });
                    }
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
                processAddTaxaArr(){
                    if(this.taxaToAddArr.length > 0){
                        const taxonToAdd = this.taxaToAddArr[0];
                        const rankId = Number(taxonToAdd['rankid']);
                        const text = 'Adding ' + taxonToAdd['sciname'] + ' to the Taxonomic Thesaurus';
                        this.addSubprocessToProcessorDisplay('text',text);
                        taxonToAdd['parenttid'] = rankId > 10 ? this.nameTidIndex[taxonToAdd['parentName']] : 1;
                        this.addTaxonToThesaurus(taxonToAdd,(newTaxon,errorText = null) => {
                            if(errorText){
                                this.processSubprocessErrorResponse(false,errorText);
                                this.adjustUIEnd();
                            }
                            else{
                                const newTid = Number(newTaxon['tid']);
                                this.nameTidIndex[this.taxaToAddArr[0]['sciname']] = newTid;
                                this.newTidArr.push(newTid);
                                this.taxaToAddArr.splice(0, 1);
                                this.processSubprocessSuccessResponse(false);
                                this.processAddTaxaArr();
                            }
                        });
                    }
                    else{
                        this.processSubprocessSuccessResponse(true);
                        this.setTargetSynonymy();
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
                    if(this.importCommonNames){
                        this.currentTaxonExternal['commonnames'] = this.taxonSearchResults[0].hasOwnProperty('commonnames') ? this.taxonSearchResults[0]['commonnames'] : [];
                    }
                    if(this.targetTaxonLocal['sciname'] === this.currentTaxonExternal['sciname']){
                        this.currentTaxonLocal['tid'] = this.targetTaxonLocal['tid'];
                        this.currentTaxonLocal['sciname'] = this.targetTaxonLocal['sciname'];
                        this.currentTaxonLocal['author'] = this.targetTaxonLocal['author'];
                        this.currentTaxonLocal['rankid'] = this.targetTaxonLocal['rankid'];
                        this.currentTaxonLocal['tidaccepted'] = this.targetTaxonLocal['tidaccepted'];
                        this.currentTaxonLocal['parenttid'] = this.targetTaxonLocal['parenttid'];
                        if(this.importCommonNames){
                            this.currentTaxonLocal['commonnames'] = this.targetTaxonLocal['commonnames'];
                        }
                    }
                    else{
                        this.findTaxonByTid(this.targetTaxonLocal['tidaccepted'],(resObj,errorText = null) => {
                            if(errorText){
                                this.processErrorResponse(errorText);
                                this.adjustUIEnd();
                            }
                            else{
                                this.currentTaxonLocal = resObj;
                                this.kingdomId = this.currentTaxonLocal['kingdomid'];
                                this.kingdomName = this.currentTaxonLocal['kingdom'];
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
                    this.targetTaxonLocal['tidaccepted'] = this.nameTidIndex[this.taxonSearchResults[0]['accepted_sciname']];
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
                                                        this.validateTargetTaxonSearchResults();
                                                    }
                                                });
                                            }
                                            else{
                                                this.findTargetTaxonExternalBySciname();
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
                                            this.validateTargetTaxonSearchResults();
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
                                            this.validateTargetTaxonSearchResults();
                                        }
                                    });
                                }
                            }
                            else{
                                this.findTargetTaxonExternalBySciname();
                            }
                        }
                    });
                },
                setTaxaToAdd(){
                    if(this.processingArr.length > 0){
                        const sciname = this.processingArr[0]['sciname'];
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
                                    if(this.dataSource === 'worms' && (!res || this.processingArr[0]['sciname'] === this.taxonSearchResults[0]['accepted_sciname'])){
                                        this.getWoRMSAddTaxonAuthor(res);
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
                        this.processSubprocessSuccessResponse(false);
                        //this.processAddTaxaArr();
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
                                callback(res);
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
                                                cNameObj['name'] = cName['commonName'];
                                                cNameObj['langid'] = langObj ? Number(langObj['langid']) : null;
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
                validateTargetTaxonSearchResults(){
                    this.processingArr = [];
                    this.taxaToAddArr = [];
                    if(this.taxonSearchResults.length === 1){
                        if(!this.taxonSearchResults[0]['accepted'] && !this.taxonSearchResults[0]['accepted_sciname']){
                            this.processErrorResponse('Unable to distinguish the parent taxon accepted name');
                            this.adjustUIEnd();
                        }
                        else{
                            console.log(this.targetTaxonLocal);
                            console.log(this.taxonSearchResults[0]);
                            this.processSuccessResponse('Complete');
                            if(!this.targetTaxonIdentifier){
                                this.addTaxonIdentifier(this.taxonomicGroupTid,this.taxonSearchResults[0]['id']);
                            }
                            if(!this.taxonSearchResults[0]['accepted']){
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
                                this.processingArr = addHierchyTemp;
                                this.setTaxaToAdd();
                            }
                            else{
                                this.setTargetSynonymy();
                            }
                        }
                    }
                    else{
                        this.processSubprocessErrorResponse('Unable to distinguish the parent taxon by name');
                        this.adjustUIEnd();
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
