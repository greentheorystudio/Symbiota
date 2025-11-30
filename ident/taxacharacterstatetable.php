<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$tId = array_key_exists('tid', $_REQUEST) ? (int)$_REQUEST['tid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Character State Management</title>
        <meta name="description" content="Taxa character state management module for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <style>
            .sticky-table td:first-child {
                /* bg color is important for td; just specify one */
                background-color: white;
            }
            .sticky-table th {
                border-top: 2px solid black;
            }
            .sticky-table tr th:first-child, .sticky-table tr td:first-child {
                border-right: 2px solid black;
            }
            .sticky-table tr th {
                position: sticky;
                /* higher than z-index for td below */
                z-index: 2;
                /* bg color is important; just specify one */
                background: white;
            }
            .sticky-table thead tr:last-child th {
                /* height of all previous header rows */
                top: 48px;
                /* highest z-index */
                z-index: 3;
            }
            .sticky-table thead tr:first-child th {
                top: 0;
                z-index: 1;
            }
            .sticky-table tr:first-child th:first-child {
                /* highest z-index */
                z-index: 3;
            }
            .sticky-table td:first-child {
                z-index: 1;
            }
            .sticky-table td:first-child, .sticky-table th:first-child {
                position: sticky;
                left: 0;
            }
            .sticky-table tbody {
                /* height of all previous header rows */
                scroll-margin-top: 48px;
            }
        </style>
    </head>
    <body class="full-window-mode">
        <a class="screen-reader-only" href="#tableContainer">Skip to main content</a>
        <div id="tableContainer">
            <q-table class="sticky-table" :style="tableStyle" flat bordered :rows="tableRowArr" :columns="columnHeaderArr" row-key="tid" virtual-scroll binary-state-sort v-model:pagination="pagination" :rows-per-page-options="[0]" :visible-columns="visibleColumns" separator="cell" @request="changeRecordPage">
                <template v-slot:no-data>
                    <div class="fit row flex-center text-h6 text-bold">
                        <span v-if="Number(taxonomicGroupId) > 0">
                            That taxon does not have any accepted subtaxa to display
                        </span>
                        <span v-else>
                            Please enter a taxon name in the Taxonomic Group field above to load data
                        </span>
                    </div>
                </template>
                <template v-slot:top>
                    <div class="full-width column q-gutter-sm">
                        <div class="q-mb-sm">
                            <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                            <span class="text-bold">Taxa Character State Management</span>
                        </div>
                        <div class="full-width row justify-between">
                            <div class="col-10 row justify-start q-gutter-md">
                                <div class="col-4">
                                    <single-scientific-common-name-auto-complete :sciname="taxonomicGroupName" label="Taxonomic Group" :limit-to-options="true" @update:sciname="processTaxonomicGroupChange"></single-scientific-common-name-auto-complete>
                                </div>
                                <div v-if="Number(taxonomicGroupParentId) > 0" class="self-center">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="setTaxon(taxonomicGroupParentId);" icon="fas fa-level-up-alt" aria-label="Go to Parent" tabindex="0">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Go to Parent
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                                <div class="self-center">
                                    <checkbox-input-element label="Include all subtaxa" :value="includeAllSubtaxa" @update:value="processIncludeAllSubtaxaChange"></checkbox-input-element>
                                </div>
                            </div>
                            <div class="col-2 row justify-end">
                                <div v-if="columnHeaderArr.length > 0 && tableRowArr.length > 0">
                                    <q-btn color="primary" @click="showColumnTogglePopup = true" label="Toggle Columns" tabindex="0" />
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
                        <q-td key="sciname" :props="props">
                            <a class="q-ml-sm cursor-pointer" title="Set taxonomic group" aria-label="Set taxonomic group" @click="setTaxon(props.row.tid);" tabindex="0">
                                <span class="text-subtitle1 text-italic">{{ props.row.sciname }}</span>
                            </a>
                        </q-td>
                        <template v-for="field in characterHeaderArr">
                            <q-td :key="field.name" :props="props">
                                <a class="q-ml-sm cursor-pointer" title="Edit character state" aria-label="Edit character state" @click="openTaxonCharacterStateEditorPopup(props.row.tid, field.name);" tabindex="0">
                                    <span class="text-body1">{{ props.row[field.name] }}</span>
                                </a>
                            </q-td>
                        </template>
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
            <template v-if="showColumnTogglePopup">
                <table-column-toggle-popup
                    :field-arr="characterHeaderArr"
                    :show-popup="showColumnTogglePopup"
                    :visible-columns="visibleColumns"
                    @update:visible-columns="updateVisibleColumns"
                    @close:popup="showColumnTogglePopup = false"
                ></table-column-toggle-popup>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/tableColumnTogglePopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const taxaCharacterStateManagementModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                    'table-column-toggle-popup': tableColumnTogglePopup
                },
                setup() {
                    const { hideWorking, showWorking } = useCore();
                    const baseStore = useBaseStore();

                    const characterArr = Vue.ref([]);
                    const characterHeaderArr = Vue.computed(() => {
                        const returnArr = [];
                        if(characterArr.value.length > 0){
                            characterArr.value.forEach((character) => {
                                returnArr.push({
                                    name: character['cid'].toString(),
                                    label: (character['headingname'] + ' - ' + character['charactername']),
                                    field: character['cid'].toString(),
                                    align: 'center',
                                    sortable: true
                                });
                            });
                        }
                        return returnArr;
                    });
                    const characterStateData = Vue.ref({});
                    const characterStateLoadIndex = Vue.ref(1);
                    const clientRoot = baseStore.getClientRoot;
                    const columnHeaderArr = Vue.computed(() => {
                        const returnArr = [
                            {
                                name: 'sciname',
                                label: 'Scientific Name',
                                field: 'sciname',
                                align: 'left',
                                sortable: true
                            }
                        ];
                        if(characterArr.value.length > 0){
                            characterArr.value.forEach((character) => {
                                returnArr.push({
                                    name: character['cid'].toString(),
                                    label: character['charactername'],
                                    field: character['cid'].toString(),
                                    align: 'center',
                                    sortable: true
                                });
                            });
                        }
                        return returnArr;
                    });
                    const includeAllSubtaxa = Vue.ref(false);
                    const isEditor = Vue.ref(false);
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
                    const perPageCnt = 100;
                    const recordsPageNumber = Vue.ref(1);
                    const showColumnTogglePopup = Vue.ref(false);
                    const showTaxonCharacterStateEditorPopup = Vue.ref(false);
                    const sortDescending = Vue.ref(false);
                    const sortedTaxaArr = Vue.computed(() => {
                        const returnArr = [];
                        taxaArr.value.forEach((taxon) => {
                            const charStateData = characterStateData.value.hasOwnProperty(taxon['tid'].toString()) ? characterStateData.value[taxon['tid'].toString()] : {};
                            const rowData = {
                                tid: taxon['tid'],
                                sciname: taxon['sciname']
                            };
                            characterArr.value.forEach((character) => {
                                rowData[character['cid'].toString()] = charStateData.hasOwnProperty(character['cid'].toString()) ? getCharacterStateStrFromData(charStateData[character['cid'].toString()]) : '[No Data]';
                            });
                            returnArr.push(rowData);
                        });
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
                    const sortField = Vue.ref('sciname');
                    const tableRowArr = Vue.computed(() => {
                        const startIndex = (recordsPageNumber.value - 1) * perPageCnt;
                        return sortedTaxaArr.value.slice(startIndex, (startIndex + perPageCnt));
                    });
                    const tableStyle = Vue.ref('');
                    const taxaArr = Vue.ref([]);
                    const taxaLoadIndex = Vue.ref(1);
                    const taxonomicGroupId = Vue.ref(null);
                    const taxonomicGroupName = Vue.ref(null);
                    const taxonomicGroupParentId = Vue.ref(null);
                    const tidArr = Vue.computed(() => {
                        const returnArr = [];
                        taxaArr.value.forEach((taxon) => {
                            returnArr.push(taxon['tid']);
                        });
                        return returnArr;
                    });
                    const visibleColumns = Vue.ref([]);

                    function changeRecordPage(props) {
                        if(Number(recordsPageNumber.value) !== Number(props.pagination.page)){
                            recordsPageNumber.value = props.pagination.page;
                        }
                        else{
                            sortDescending.value = !sortDescending.value;
                            sortField.value = props.pagination.sortBy;
                        }
                    }

                    function clearTaxaData() {
                        characterStateData.value = Object.assign({}, {});
                        characterStateLoadIndex.value = 1;
                        taxaArr.value.length = 0;
                        taxaLoadIndex.value = 1;
                    }

                    function getCharacterStateStrFromData(data) {
                        const strArr = [];
                        data.forEach((state) => {
                            strArr.push(state['characterstatename']);
                        });
                        return strArr.join(', ');
                    }

                    function loadCharacterStateData() {
                        const loadingCnt = 200;
                        const startIndex = (characterStateLoadIndex.value - 1) * loadingCnt;
                        const currentTidArr = tidArr.value.length > 0 ? tidArr.value.slice(startIndex, (startIndex + loadingCnt)) : [];
                        if(currentTidArr.length > 0){
                            const formData = new FormData();
                            formData.append('tidArr', JSON.stringify(currentTidArr));
                            formData.append('action', 'getCharacterStatesFromTidArr');
                            fetch(keyCharacterStateApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((resData) => {
                                characterStateData.value = Object.assign({ ...characterStateData.value, ...resData }, {});
                                characterStateLoadIndex.value++;
                                loadCharacterStateData();
                            });
                        }
                        else{
                            setTableStyle();
                            hideWorking();
                        }
                    }

                    function openTaxonCharacterStateEditorPopup(tid, cid) {
                        console.log(tid);
                        console.log(cid);
                    }

                    function processIncludeAllSubtaxaChange(value) {
                        clearTaxaData();
                        includeAllSubtaxa.value = (Number(value) === 1);
                        processSetTaxaArr();
                    }

                    function processSetTaxaArr() {
                        if(Number(taxonomicGroupId.value) > 0){
                            showWorking();
                            if(includeAllSubtaxa.value){
                                setAllSubtaxaTaxaArr();
                            }
                            else{
                                setDirectChildTaxaArr();
                            }
                        }
                    }

                    function processTaxonomicGroupChange(taxonData) {
                        clearTaxaData();
                        if(taxonData && taxonData.hasOwnProperty('tid') && Number(taxonData['tid']) > 0){
                            taxonomicGroupId.value = taxonData['tid'];
                            taxonomicGroupName.value = taxonData['sciname'];
                            taxonomicGroupParentId.value = taxonData['parenttid'];
                            processSetTaxaArr();
                        }
                        else{
                            taxonomicGroupId.value = null;
                            taxonomicGroupName.value = null;
                            taxonomicGroupParentId.value = null;
                            setTableStyle();
                        }
                    }

                    function setAllSubtaxaTaxaArr() {
                        const formData = new FormData();
                        formData.append('parenttid', taxonomicGroupId.value.toString());
                        formData.append('index', taxaLoadIndex.value.toString());
                        formData.append('action', 'getAcceptedTaxaByTaxonomicGroup');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            if(resData.length > 0){
                                taxaArr.value = taxaArr.value.concat(resData);
                            }
                            if(resData.length < 50000){
                                loadCharacterStateData();
                            }
                            else{
                                taxaLoadIndex.value++;
                                setAllSubtaxaTaxaArr();
                            }
                        });
                    }

                    function setCharacterArr() {
                        const formData = new FormData();
                        formData.append('action', 'getCharacterArr');
                        fetch(keyCharacterApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            characterArr.value = resData.slice();
                            visibleColumns.value.push('sciname');
                            characterArr.value.forEach((character) => {
                                visibleColumns.value.push(character['cid'].toString());
                            });
                        });
                    }

                    function setDirectChildTaxaArr() {
                        const formData = new FormData();
                        formData.append('parenttid', taxonomicGroupId.value.toString());
                        formData.append('action', 'getAcceptedChildTaxaByParentTid');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            taxaArr.value = resData.slice();
                            loadCharacterStateData();
                        });
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'KeyAdmin');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isEditor.value = resData.includes('KeyAdmin');
                            if(!isEditor.value){
                                window.location.href = clientRoot + '/index.php';
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

                    function setTaxon(tid) {
                        console.log(tid);
                        const formData = new FormData();
                        formData.append('tid', tid.toString());
                        formData.append('action', 'getTaxonFromTid');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            processTaxonomicGroupChange(resData);
                        });
                    }

                    function updateVisibleColumns(value) {
                        visibleColumns.value = value.slice();
                        visibleColumns.value.push('sciname');
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        window.addEventListener('resize', setTableStyle);
                        setTableStyle();
                        setCharacterArr();
                    });
                    
                    return {
                        characterHeaderArr,
                        clientRoot,
                        columnHeaderArr,
                        includeAllSubtaxa,
                        isEditor,
                        pagination,
                        paginationFirstRecordNumber,
                        paginationLastPageNumber,
                        paginationLastRecordNumber,
                        showColumnTogglePopup,
                        showTaxonCharacterStateEditorPopup,
                        tableRowArr,
                        tableStyle,
                        taxonomicGroupId,
                        taxonomicGroupName,
                        taxonomicGroupParentId,
                        visibleColumns,
                        changeRecordPage,
                        openTaxonCharacterStateEditorPopup,
                        processIncludeAllSubtaxaChange,
                        processTaxonomicGroupChange,
                        setTaxon,
                        updateVisibleColumns
                    }
                }
            });
            taxaCharacterStateManagementModule.use(Quasar, { config: {} });
            taxaCharacterStateManagementModule.use(Pinia.createPinia());
            taxaCharacterStateManagementModule.mount('#tableContainer');
        </script>
    </body>
</html>
