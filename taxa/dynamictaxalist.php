<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Dynamic Taxa List</title>
        <meta name="description" content="Dynamic taxa list for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body class="full-window-mode">
        <a class="screen-reader-only" href="#tableContainer">Skip to main content</a>
        <div id="tableContainer">
            <q-table ref="tableRef" class="sticky-table hide-scrollbar" :style="tableStyle" flat bordered :rows="tableRowArr" :columns="columnHeaderArr" row-key="tid" virtual-scroll binary-state-sort v-model:pagination="pagination" :rows-per-page-options="[0]" separator="cell" @request="changeRecordPage">
                <template v-slot:no-data>
                    <div class="fit row flex-center text-h6 text-bold">
                        <span v-if="Number(parentTaxonTid) > 0 || selectedVernacular">
                            There are no taxa matching your criteria
                        </span>
                        <span v-else>
                            Please enter criteria to build a taxa list
                        </span>
                    </div>
                </template>
                <template v-slot:top>
                    <div class="full-width column">
                        <div class="q-mb-sm">
                            <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                            <span class="text-bold">Dynamic Taxa List</span>
                        </div>
                        <div class="q-mb-sm row justify-start q-col-gutter-sm">
                            <div class="col-2">
                                <selector-input-element label="Kingdom" :options="kingdomOptions" :value="selectedKingdomTid" :clearable="true" @update:value="processKingdomSelection"></selector-input-element>
                            </div>
                            <div class="col-3">
                                <selector-input-element label="Phylum" :options="phylumOptions" :value="selectedPhylumTid" :clearable="true" @update:value="processPhylumSelection"></selector-input-element>
                            </div>
                            <div class="col-3">
                                <selector-input-element label="Class" :options="classOptions" :value="selectedClassTid" :clearable="true" @update:value="processClassSelection"></selector-input-element>
                            </div>
                            <div class="col-2">
                                <single-scientific-common-name-auto-complete :sciname="selectedOrderSciname" label="Order" rank-limit="100" :parent-tid="parentTaxonTid" :clearable="true" @update:sciname="processOrderSelection"></single-scientific-common-name-auto-complete>
                            </div>
                            <div class="col-2">
                                <single-scientific-common-name-auto-complete :sciname="selectedFamilySciname" label="Family" rank-limit="140" :parent-tid="parentTaxonTid" :clearable="true" @update:sciname="processFamilySelection"></single-scientific-common-name-auto-complete>
                            </div>
                        </div>
                        <div class="full-width row justify-between">
                            <div class="col-7 row justify-start q-col-gutter-sm">
                                <div class="col-6">
                                    <single-scientific-common-name-auto-complete :sciname="selectedScinameSciname" label="Scientific Name" :clearable="true" @update:sciname="processScinameSelection"></single-scientific-common-name-auto-complete>
                                </div>
                                <div class="col-6">
                                    <single-scientific-common-name-auto-complete :sciname="selectedVernacular" label="Common Name" taxon-type="5" :clearable="true" @update:sciname="processVernacularSelection"></single-scientific-common-name-auto-complete>
                                </div>
                            </div>
                            <div class="col-3 row justify-end q-gutter-sm self-center">
                                <div>
                                    <checkbox-input-element label="Limit to taxa with descriptions" :value="limitToDescriptions" @update:value="processLimitToDescriptionChange"></checkbox-input-element>
                                </div>
                                <div v-if="taxaArr.length > 0">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="processTaxaDownload();" icon="fas fa-download" dense aria-label="Download taxa list CSV" tabindex="0">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Download taxa list CSV
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:header="props">
                    <q-tr :props="props">
                        <q-th v-for="col in props.cols" :key="col.name" :props="props" class="bg-grey-4">
                            <span class="text-subtitle1 text-bold">{{ col.label }}</span>
                        </q-th>
                    </q-tr>
                </template>
                <template v-slot:body="props">
                    <q-tr :props="props">
                        <q-td key="kingdomname" :props="props">
                            <a v-if="Number(props.row.kingdomtid) > 0" class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.kingdomtid)" target="_blank" :aria-label="(props.row.kingdomname + ' taxon profile page page - Opens in separate tab')" tabindex="0">
                                <span class="text-subtitle1">{{ props.row.kingdomname }}</span>
                            </a>
                        </q-td>
                        <q-td key="phylumname" :props="props">
                            <a v-if="Number(props.row.phylumtid) > 0" class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.phylumtid)" target="_blank" :aria-label="(props.row.phylumname + ' taxon profile page page - Opens in separate tab')" tabindex="0">
                                <span class="text-subtitle1">{{ props.row.phylumname }}</span>
                            </a>
                        </q-td>
                        <q-td key="classname" :props="props">
                            <a v-if="Number(props.row.classtid) > 0" class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.classtid)" target="_blank" :aria-label="(props.row.classname + ' taxon profile page page - Opens in separate tab')" tabindex="0">
                                <span class="text-subtitle1">{{ props.row.classname }}</span>
                            </a>
                        </q-td>
                        <q-td key="ordername" :props="props">
                            <a v-if="Number(props.row.ordertid) > 0" class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.ordertid)" target="_blank" :aria-label="(props.row.ordername + ' taxon profile page page - Opens in separate tab')" tabindex="0">
                                <span class="text-subtitle1">{{ props.row.ordername }}</span>
                            </a>
                        </q-td>
                        <q-td key="familyname" :props="props">
                            <a v-if="Number(props.row.familytid) > 0" class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.familytid)" target="_blank" :aria-label="(props.row.familyname + ' taxon profile page page - Opens in separate tab')" tabindex="0">
                                <span class="text-subtitle1">{{ props.row.familyname }}</span>
                            </a>
                        </q-td>
                        <q-td key="sciname" :props="props">
                            <a v-if="props.row.sciname" class="text-black" :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.tid)" target="_blank" :aria-label="(props.row.sciname + ' taxon profile page page - Opens in separate tab')" tabindex="0">
                                <span class="text-subtitle1 text-italic">{{ props.row.sciname }}</span>
                            </a>
                        </q-td>
                        <q-td key="vernacularData" :props="props">
                            <div class="text-subtitle1">
                                {{ getVernacularStrFromArr(props.row['vernacularData'], props.row.tid) }}
                            </div>
                        </q-td>
                        <q-td key="identifierData" :props="props">
                            <template v-if="props.row['identifierData'].length > 0">
                                <div class="row q-gutter-sm no-wrap self-center">
                                    <template v-for="identifier in props.row['identifierData']">
                                        <template v-if="identifier['name'] === 'genetic-data-available'">
                                            <q-chip clickable color="teal" text-color="white" class="text-bold cursor-pointer" @click="openOccurrenceListGeneticSearch(props.row.sciname);" :aria-label="('View occurrence records with associated genetic data for ' + props.row.sciname + ' in occurrence list display - Opens in separate tab')" tabindex="0">
                                                Genetic Data
                                            </q-chip>
                                        </template>
                                        <template v-else-if="identifier['name'] === 'non-native'">
                                            <q-chip color="red" text-color="white" icon="block" class="text-bold">
                                                Non-native
                                            </q-chip>
                                        </template>
                                        <template v-else>
                                            <q-chip color="primary" text-color="white">
                                                <span class="text-bold">{{ identifier['name'] + ':' }}</span><span class="q-ml-xs">{{ identifier['identifier'] }}</span>
                                            </q-chip>
                                        </template>
                                    </template>
                                </div>
                            </template>
                        </q-td>
                    </q-tr>
                </template>
                <template v-slot:pagination="scope">
                    <div class="text-body2 text-bold q-mr-xs">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>

                    <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage" aria-label="Go to first record page" tabindex="0"></q-btn>

                    <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage" aria-label="Go to previous record page" tabindex="0"></q-btn>

                    <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage" aria-label="Go to next record page" tabindex="0"></q-btn>

                    <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage" aria-label="Go to last record page" tabindex="0"></q-btn>
                </template>
            </q-table>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const dynamicTaxaListModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'selector-input-element': selectorInputElement,
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
                },
                setup() {
                    const { hideWorking, processCsvDownload, showWorking } = useCore();
                    const baseStore = useBaseStore();

                    const classOptions = Vue.computed(() => {
                        const returnArr = [];
                        let classTaxa;
                        if(Number(selectedPhylumTid.value) > 0){
                            classTaxa = higherTaxaArr.value.filter(taxon => (Number(taxon['rankid']) === 60 && taxon['parentTidArr'].includes(selectedPhylumTid.value)));
                        }
                        else{
                            classTaxa = higherTaxaArr.value.filter(taxon => Number(taxon['rankid']) === 60);
                        }
                        if(classTaxa.length > 0){
                            classTaxa.forEach(taxon => {
                                let vernacularStr;
                                if(taxon['vernacularData'] && taxon['vernacularData'].length > 0){
                                    const vernacularArr = [];
                                    taxon['vernacularData'].forEach(vernacular => {
                                        if(Number(vernacular['vernaculartid']) === Number(taxon['tid'])){
                                            vernacularArr.push(vernacular['vernacularname']);
                                        }
                                    });
                                    vernacularStr = vernacularArr.join(', ');
                                }
                                returnArr.push({
                                    label: (vernacularStr ? (taxon['sciname'] + ' - ' + vernacularStr) : taxon['sciname']),
                                    value: taxon['tid']
                                });
                            });
                        }
                        return returnArr;
                    });
                    const clientRoot = baseStore.getClientRoot;
                    const columnHeaderArr = [
                        {name: 'kingdomname', label: 'Kingdom', field: 'kingdomname', align: 'left', sortable: true},
                        {name: 'phylumname', label: 'Phylum', field: 'phylumname', align: 'left', sortable: true},
                        {name: 'classname', label: 'Class', field: 'classname', align: 'left', sortable: true},
                        {name: 'ordername', label: 'Order', field: 'ordername', align: 'left', sortable: true},
                        {name: 'familyname', label: 'Family', field: 'familyname', align: 'left', sortable: true},
                        {name: 'sciname', label: 'Scientific Name', field: 'sciname', align: 'left', sortable: true},
                        {name: 'vernacularData', label: 'Common Names', field: 'vernacularData', align: 'left', sortable: false},
                        {name: 'identifierData', label: '', field: 'identifierData', align: 'left', sortable: false}
                    ];
                    const downloadData = Vue.computed(() => {
                        const returnArr = [];
                        taxaArr.value.forEach((taxon) => {
                            const rowData = {
                                Kingdom: taxon['kingdomname'],
                                Phylum: taxon['phylumname'],
                                Class: taxon['classname'],
                                Order: taxon['ordername'],
                                Family: taxon['familyname'],
                                ScientificName: taxon['sciname'],
                                CommonNames: taxon['vernacularData'].length > 0 ? getVernacularStrFromArr(taxon['vernacularData'], taxon['tid']) : ''
                            };
                            returnArr.push(rowData);
                        });
                        returnArr.sort((a, b) => {
                            return a['Kingdom'].toLowerCase().localeCompare(b['Kingdom'].toLowerCase());
                        });
                        return returnArr;
                    });
                    const higherTaxaArr = Vue.ref([]);
                    const kingdomOptions = Vue.computed(() => {
                        const returnArr = [];
                        const kingdomTaxa = higherTaxaArr.value.filter(taxon => Number(taxon['rankid']) === 10);
                        if(kingdomTaxa.length > 0){
                            kingdomTaxa.forEach(taxon => {
                                let vernacularStr;
                                if(taxon['vernacularData'] && taxon['vernacularData'].length > 0){
                                    const vernacularArr = [];
                                    taxon['vernacularData'].forEach(vernacular => {
                                        if(Number(vernacular['vernaculartid']) === Number(taxon['tid'])){
                                            vernacularArr.push(vernacular['vernacularname']);
                                        }
                                    });
                                    vernacularStr = vernacularArr.join(', ');
                                }
                                returnArr.push({
                                    label: (vernacularStr ? (taxon['sciname'] + ' - ' + vernacularStr) : taxon['sciname']),
                                    value: taxon['tid']
                                });
                            });
                        }
                        return returnArr;
                    });
                    const limitToDescriptions = Vue.ref(false);
                    const loadingIndex = Vue.ref(0);
                    const pagination = Vue.computed(() => {
                        return {
                            sortBy: sortField.value,
                            descending: sortDescending.value,
                            page: recordsPageNumber.value,
                            lastPage: paginationLastPageNumber.value,
                            rowsPerPage: perPageCnt,
                            firstRowNumber: paginationFirstRecordNumber.value,
                            lastRowNumber: paginationLastRecordNumber.value,
                            rowsNumber: taxaArr.value.length
                        };
                    });
                    const paginationFirstRecordNumber = Vue.computed(() => {
                        let recordNumber = 1;
                        if(Number(recordsPageNumber.value) > 1){
                            recordNumber += ((Number(recordsPageNumber.value) - 1) * Number(perPageCnt));
                        }
                        return recordNumber;
                    });
                    const paginationLastPageNumber = Vue.computed(() => {
                        let lastPage = 1;
                        if(taxaArr.value.length > Number(perPageCnt)){
                            lastPage = Math.floor(taxaArr.value.length / Number(perPageCnt));
                        }
                        if(taxaArr.value.length % Number(perPageCnt)){
                            lastPage++;
                        }
                        return lastPage;
                    });
                    const paginationLastRecordNumber = Vue.computed(() => {
                        let recordNumber = (taxaArr.value.length > Number(perPageCnt)) ? Number(perPageCnt) : taxaArr.value.length;
                        if(taxaArr.value.length > Number(perPageCnt) && Number(recordsPageNumber.value) > 1){
                            if(Number(recordsPageNumber.value) === Number(paginationLastPageNumber.value)){
                                recordNumber = (taxaArr.value.length % Number(perPageCnt)) + ((Number(recordsPageNumber.value) - 1) * Number(perPageCnt));
                            }
                            else{
                                recordNumber = Number(recordsPageNumber.value) * Number(perPageCnt);
                            }
                        }
                        return recordNumber;
                    });
                    const parentTaxonTid = Vue.computed(() => {
                        let returnVal = null;
                        if(Number(selectedScinameTid.value) > 0){
                            returnVal = selectedScinameTid.value;
                        }
                        else if(Number(selectedFamilyTid.value) > 0){
                            returnVal = selectedFamilyTid.value;
                        }
                        else if(Number(selectedOrderTid.value) > 0){
                            returnVal = selectedOrderTid.value;
                        }
                        else if(Number(selectedClassTid.value) > 0){
                            returnVal = selectedClassTid.value;
                        }
                        else if(Number(selectedPhylumTid.value) > 0){
                            returnVal = selectedPhylumTid.value;
                        }
                        else if(Number(selectedKingdomTid.value) > 0){
                            returnVal = selectedKingdomTid.value;
                        }
                        return returnVal;
                    });
                    const perPageCnt = 100;
                    const phylumOptions = Vue.computed(() => {
                        const returnArr = [];
                        let phylumTaxa;
                        if(Number(selectedKingdomTid.value) > 0){
                            phylumTaxa = higherTaxaArr.value.filter(taxon => (Number(taxon['rankid']) === 30 && taxon['parentTidArr'].includes(selectedKingdomTid.value)));
                        }
                        else{
                            phylumTaxa = higherTaxaArr.value.filter(taxon => Number(taxon['rankid']) === 30);
                        }
                        if(phylumTaxa.length > 0){
                            phylumTaxa.forEach(taxon => {
                                let vernacularStr;
                                if(taxon['vernacularData'] && taxon['vernacularData'].length > 0){
                                    const vernacularArr = [];
                                    taxon['vernacularData'].forEach(vernacular => {
                                        if(Number(vernacular['vernaculartid']) === Number(taxon['tid'])){
                                            vernacularArr.push(vernacular['vernacularname']);
                                        }
                                    });
                                    vernacularStr = vernacularArr.join(', ');
                                }
                                returnArr.push({
                                    label: (vernacularStr ? (taxon['sciname'] + ' - ' + vernacularStr) : taxon['sciname']),
                                    value: taxon['tid']
                                });
                            });
                        }
                        return returnArr;
                    });
                    const recordsPageNumber = Vue.ref(1);
                    const selectedClassTid = Vue.ref(null);
                    const selectedFamilySciname = Vue.ref(null);
                    const selectedFamilyTid = Vue.ref(null);
                    const selectedKingdomTid = Vue.ref(null);
                    const selectedOrderSciname = Vue.ref(null);
                    const selectedOrderTid = Vue.ref(null);
                    const selectedPhylumTid = Vue.ref(null);
                    const selectedScinameSciname = Vue.ref(null);
                    const selectedScinameTid = Vue.ref(null);
                    const selectedVernacular = Vue.ref(null);
                    const sortDescending = Vue.ref(false);
                    const sortedTaxaArr = Vue.computed(() => {
                        const returnArr = taxaArr.value.slice();
                        returnArr.sort((a, b) => {
                            if(sortDescending.value){
                                return b[sortField.value].toLowerCase().localeCompare(a[sortField.value].toLowerCase());
                            }
                            else{
                                return a[sortField.value].toLowerCase().localeCompare(b[sortField.value].toLowerCase());
                            }
                        });
                        return returnArr;
                    });
                    const sortField = Vue.ref('kingdomname');
                    const tableRef = Vue.ref(null);
                    const tableRowArr = Vue.computed(() => {
                        const startIndex = (recordsPageNumber.value - 1) * perPageCnt;
                        return sortedTaxaArr.value.slice(startIndex, (startIndex + perPageCnt));
                    });
                    const tableStyle = Vue.ref('');
                    const taxaArr = Vue.ref([]);

                    function changeRecordPage(props) {
                        if(Number(recordsPageNumber.value) !== Number(props.pagination.page)){
                            recordsPageNumber.value = props.pagination.page;
                        }
                        else{
                            sortDescending.value = !sortDescending.value;
                            sortField.value = props.pagination.sortBy;
                        }
                        tableRef.value.scrollTo(0);
                    }

                    function getParentTaxaArr(tid, callback) {
                        const formData = new FormData();
                        formData.append('tid', tid.toString());
                        formData.append('action', 'getParentTaxaFromTid');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            callback(data);
                        });
                    }

                    function getVernacularStrFromArr(vernacularArr, tid) {
                        const nameArr = [];
                        vernacularArr.forEach(vernacular => {
                            if(vernacular['vernacularname'] && Number(tid) === Number(vernacular['vernaculartid'])){
                                nameArr.push(vernacular['vernacularname']);
                            }
                        });
                        return nameArr.length > 0 ? nameArr.join(', ') : null;
                    }

                    function openOccurrenceListGeneticSearch(sciname) {
                        window.open((clientRoot + '/collections/list.php?starr={"hasgenetic":1,"taxa":"' + sciname + '"}'), '_blank');
                    }

                    function processClassSelection(value) {
                        selectedFamilySciname.value = null;
                        selectedFamilyTid.value = null;
                        selectedOrderSciname.value = null;
                        selectedOrderTid.value = null;
                        selectedVernacular.value = null;
                        selectedScinameSciname.value = null;
                        selectedScinameTid.value = null;
                        selectedClassTid.value = value;
                        if(value){
                            getParentTaxaArr(value, (parentTaxaArr) => {
                                const kingdomTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 10);
                                const phylumTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 30);
                                selectedKingdomTid.value = kingdomTaxon ? kingdomTaxon['tid'] : null;
                                selectedPhylumTid.value = phylumTaxon ? phylumTaxon['tid'] : null;
                                processCriteria();
                            });
                        }
                        else{
                            processCriteria()
                        }
                    }

                    function processCriteria() {
                        taxaArr.value.length = 0;
                        loadingIndex.value = 0;
                        if(Number(parentTaxonTid.value) > 0 || selectedVernacular.value){
                            showWorking();
                            setTaxaArr();
                        }
                        else{
                            setTableStyle();
                        }
                    }

                    function processFamilySelection(taxon) {
                        selectedVernacular.value = null;
                        selectedScinameSciname.value = null;
                        selectedScinameTid.value = null;
                        if(taxon){
                            getParentTaxaArr(taxon['tid'], (parentTaxaArr) => {
                                const kingdomTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 10);
                                const phylumTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 30);
                                const classTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 60);
                                const orderTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 100);
                                selectedKingdomTid.value = kingdomTaxon ? kingdomTaxon['tid'] : null;
                                selectedPhylumTid.value = phylumTaxon ? phylumTaxon['tid'] : null;
                                selectedClassTid.value = classTaxon ? classTaxon['tid'] : null;
                                selectedOrderSciname.value = orderTaxon ? orderTaxon['sciname'] : null;
                                selectedOrderTid.value = orderTaxon ? orderTaxon['tid'] : null;
                                selectedFamilySciname.value = taxon['sciname'];
                                selectedFamilyTid.value = taxon['tid'];
                                processCriteria();
                            });
                        }
                        else{
                            selectedFamilySciname.value = null;
                            selectedFamilyTid.value = null;
                            processCriteria();
                        }
                    }

                    function processKingdomSelection(value) {
                        selectedClassTid.value = null;
                        selectedFamilySciname.value = null;
                        selectedFamilyTid.value = null;
                        selectedKingdomTid.value = null;
                        selectedOrderSciname.value = null;
                        selectedOrderTid.value = null;
                        selectedPhylumTid.value = null;
                        selectedVernacular.value = null;
                        selectedScinameSciname.value = null;
                        selectedScinameTid.value = null;
                        selectedKingdomTid.value = value;
                        processCriteria();
                    }

                    function processLimitToDescriptionChange(value) {
                        limitToDescriptions.value = value;
                        processCriteria();
                    }

                    function processOrderSelection(taxon) {
                        selectedFamilySciname.value = null;
                        selectedFamilyTid.value = null;
                        selectedVernacular.value = null;
                        selectedScinameSciname.value = null;
                        selectedScinameTid.value = null;
                        if(taxon){
                            getParentTaxaArr(taxon['tid'], (parentTaxaArr) => {
                                const kingdomTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 10);
                                const phylumTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 30);
                                const classTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 60);
                                selectedKingdomTid.value = kingdomTaxon ? kingdomTaxon['tid'] : null;
                                selectedPhylumTid.value = phylumTaxon ? phylumTaxon['tid'] : null;
                                selectedClassTid.value = classTaxon ? classTaxon['tid'] : null;
                                selectedOrderSciname.value = taxon['sciname'];
                                selectedOrderTid.value = taxon['tid'];
                                processCriteria();
                            });
                        }
                        else{
                            selectedOrderSciname.value = null;
                            selectedOrderTid.value = null;
                            processCriteria();
                        }
                    }

                    function processPhylumSelection(value) {
                        selectedClassTid.value = null;
                        selectedFamilySciname.value = null;
                        selectedFamilyTid.value = null;
                        selectedOrderSciname.value = null;
                        selectedOrderTid.value = null;
                        selectedVernacular.value = null;
                        selectedScinameSciname.value = null;
                        selectedScinameTid.value = null;
                        selectedPhylumTid.value = value;
                        if(value){
                            getParentTaxaArr(value, (parentTaxaArr) => {
                                const kingdomTaxon = parentTaxaArr.find(taxon => Number(taxon['rankid']) === 10);
                                selectedKingdomTid.value = kingdomTaxon ? kingdomTaxon['tid'] : null;
                                processCriteria();
                            });
                        }
                        else{
                            processCriteria();
                        }
                    }

                    function processScinameSelection(taxon) {
                        selectedClassTid.value = null;
                        selectedFamilySciname.value = null;
                        selectedFamilyTid.value = null;
                        selectedKingdomTid.value = null;
                        selectedOrderSciname.value = null;
                        selectedOrderTid.value = null;
                        selectedPhylumTid.value = null;
                        selectedVernacular.value = null;
                        selectedScinameSciname.value = taxon ? taxon['sciname'] : null;
                        selectedScinameTid.value = taxon ? taxon['tid'] : null;
                        processCriteria();
                    }

                    function processTaxaDownload() {
                        processCsvDownload(downloadData.value, 'TaxonomyDownload');
                    }

                    function processVernacularSelection(vernacular) {
                        selectedClassTid.value = null;
                        selectedFamilySciname.value = null;
                        selectedFamilyTid.value = null;
                        selectedKingdomTid.value = null;
                        selectedOrderSciname.value = null;
                        selectedOrderTid.value = null;
                        selectedPhylumTid.value = null;
                        selectedScinameSciname.value = null;
                        selectedScinameTid.value = null;
                        selectedVernacular.value = vernacular ? vernacular['name'] : null;
                        processCriteria();
                    }

                    function setHigherTaxaArr() {
                        const formData = new FormData();
                        formData.append('rankIdArr', JSON.stringify([10,30,60]));
                        formData.append('includeVernacular', '1');
                        formData.append('includeParentTids', '1');
                        formData.append('action', 'getTaxaArrByRankIdArr');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            if(data && data.length > 0){
                                higherTaxaArr.value = data.slice();
                            }
                        });
                    }

                    function setTableStyle() {
                        let styleStr = '';
                        styleStr += 'width: ' + window.innerWidth + 'px;';
                        if(taxaArr.value.length > 0){
                            styleStr += 'max-height: ' + window.innerHeight + 'px;';
                        }
                        else{
                            styleStr += 'height: 0;';
                        }
                        tableStyle.value = styleStr;
                    }

                    function setTaxaArr() {
                        const loadingCnt = 5000;
                        const formData = new FormData();
                        formData.append('parentIdentifier', (Number(parentTaxonTid.value) > 0 ? parentTaxonTid.value.toString() : selectedVernacular.value));
                        formData.append('parentIdType', (Number(parentTaxonTid.value) > 0 ? 'parenttid' : 'vernacular'));
                        formData.append('limitToDescriptions', (limitToDescriptions.value ? '1' : '0'));
                        formData.append('index', loadingIndex.value.toString());
                        formData.append('reccnt', loadingCnt.toString());
                        formData.append('action', 'getDynamicTaxaListDataArr');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            taxaArr.value = taxaArr.value.concat(data);
                            if(data.length < loadingCnt){
                                hideWorking();
                                setTableStyle();
                            }
                            else{
                                loadingIndex.value++;
                                setTaxaArr();
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        setHigherTaxaArr();
                        window.addEventListener('resize', setTableStyle);
                        setTableStyle();
                    });

                    return {
                        classOptions,
                        clientRoot,
                        columnHeaderArr,
                        kingdomOptions,
                        limitToDescriptions,
                        pagination,
                        parentTaxonTid,
                        phylumOptions,
                        selectedFamilySciname,
                        selectedFamilyTid,
                        selectedClassTid,
                        selectedKingdomTid,
                        selectedOrderSciname,
                        selectedOrderTid,
                        selectedPhylumTid,
                        selectedScinameSciname,
                        selectedScinameTid,
                        selectedVernacular,
                        tableRef,
                        tableRowArr,
                        tableStyle,
                        taxaArr,
                        changeRecordPage,
                        getVernacularStrFromArr,
                        openOccurrenceListGeneticSearch,
                        processClassSelection,
                        processFamilySelection,
                        processKingdomSelection,
                        processLimitToDescriptionChange,
                        processOrderSelection,
                        processPhylumSelection,
                        processScinameSelection,
                        processTaxaDownload,
                        processVernacularSelection
                    }
                }
            });
            dynamicTaxaListModule.use(Quasar, { config: {} });
            dynamicTaxaListModule.use(Pinia.createPinia());
            dynamicTaxaListModule.mount('#tableContainer');
        </script>
    </body>
</html>
