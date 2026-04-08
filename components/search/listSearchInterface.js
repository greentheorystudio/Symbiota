const listSearchInterface = {
    template: `
        <div id="breadcrumbs">
            <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
            <span class="text-bold">Search Collections List Display</span>
        </div>
        <div class="q-pa-md">
            <div class="fit">
                <q-card flat bordered>
                    <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                        <q-tab name="occurrence" label="Occurrence Records" no-caps></q-tab>
                        <q-tab name="taxa" label="Taxa List" no-caps></q-tab>
                    </q-tabs>
                    <q-separator></q-separator>
                    <q-tab-panels v-model="tab">
                        <q-tab-panel class="q-pa-none" name="occurrence">
                            <div class="fit column">
                                <div class="q-pa-sm column q-col-gutter-xs">
                                    <div class="row justify-start">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="openQueryPopupDisplay(true);" icon="search" label="Search" aria-label="Open Search Window" tabindex="0" />
                                        </div>
                                    </div>
                                    <div v-if="recordDataArr.length > 0" class="row justify-between q-col-gutter-sm">
                                        <div>
                                            <search-data-downloader :spatial="false"></search-data-downloader>
                                        </div>
                                        <div class="row justify-end q-col-gutter-sm">
                                            <table-display-button :navigator-mode="true"></table-display-button>
                                            <spatial-display-button :navigator-mode="true"></spatial-display-button>
                                            <image-display-button></image-display-button>
                                            <template v-if="searchTermsJson.length <= 1800">
                                                <copy-url-button></copy-url-button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <q-separator></q-separator>
                                <template v-if="recordDataArr.length > 0">
                                    <q-table flat bordered class="spatial-record-table" :rows="recordDataArr" row-key="occid" v-model:pagination="pagination" separator="cell" selection="multiple" @request="changeRecordPage" :rows-per-page-options="[0]" wrap-cells dense>
                                        <template v-slot:top="scope">
                                            <div class="full-width row justify-end">
                                                <div class="self-center text-bold q-mr-xs">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>

                                                <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage" aria-label="Go to first record page" tabindex="0"></q-btn>

                                                <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage" aria-label="Go to previous record page" tabindex="0"></q-btn>

                                                <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage" aria-label="Go to next record page" tabindex="0"></q-btn>

                                                <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage" aria-label="Go to last record page" tabindex="0"></q-btn>
                                            </div>
                                        </template>
                                        <template v-slot:header="props"></template>
                                        <template v-slot:body="props">
                                            <q-tr v-if="recordDataArr.length > 0" :props="props" class="fit" no-hover>
                                                <q-td class="full-width">
                                                    <div class="full-width row no-wrap">
                                                        <div class="col-9 column">
                                                            <div class="full-width q-mb-xs q-pa-xs text-bold">
                                                                {{ props.row.collectionname + ' ' + ((props.row.institutioncode || props.row.collectioncode) ? '(' : '') + (props.row.institutioncode ? props.row.institutioncode : '') + ((props.row.collectionname && props.row.collectionname) ? ':' : '') + (props.row.collectioncode ? props.row.collectioncode : '') + ((props.row.collectionname || props.row.collectionname) ? ')' : '') }}
                                                            </div>
                                                            <div class="full-width row q-pa-xs">
                                                                <div class="col-1 row justify-center items-center">
                                                                    <div>
                                                                        <template v-if="props.row.icon">
                                                                            <q-img :src="props.row.icon" class="occurrence-search-list-coll-icon" fit="contain" :alt="('Logo of ' + props.row.collectionname)"></q-img>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-11 column text-body1 wrap">
                                                                    <div v-if="props.row.sciname">
                                                                        <template v-if="Number(props.row.tid) > 0">
                                                                            <a :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.tid)" target="_blank" :aria-label="(props.row.sciname + ' taxon profile page - Opens in separate tab')" tabindex="0">
                                                                                <span class="text-italic">{{ props.row.sciname }}</span><span class="q-ml-sm">{{ props.row.scientificnameauthorship }}</span>
                                                                            </a>
                                                                        </template>
                                                                        <template v-else>
                                                                            <span class="text-italic">{{ props.row.sciname }}</span><span class="q-ml-sm">{{ props.row.scientificnameauthorship }}</span>
                                                                        </template>
                                                                    </div>
                                                                    <div v-if="props.row.catalognumber || props.row.othercatalognumbers">
                                                                            <span v-if="props.row.catalognumber">
                                                                                {{ props.row.catalognumber + (props.row.othercatalognumbers ? '  ' : '') }}
                                                                            </span>
                                                                        <span v-if="props.row.othercatalognumbers">
                                                                                {{ props.row.othercatalognumbers }}
                                                                            </span>
                                                                    </div>
                                                                    <div v-if="props.row.recordedby || props.row.recordnumber || props.row.eventdate || props.row.verbatimeventdate" class="full-width">
                                                                            <span v-if="props.row.recordedby || props.row.recordnumber">
                                                                                {{ (props.row.recordedby ? props.row.recordedby : '') + ((props.row.recordedby && props.row.recordnumber) ? ' ' : '') + (props.row.recordnumber ? props.row.recordnumber : '') + ((props.row.eventdate || props.row.verbatimeventdate) ? '  ' : '') }}
                                                                            </span>
                                                                        <span v-if="props.row.eventdate">
                                                                                {{ props.row.eventdate }}
                                                                            </span>
                                                                        <span v-else-if="props.row.verbatimeventdate">
                                                                                {{ props.row.verbatimeventdate }}
                                                                            </span>
                                                                    </div>
                                                                    <div v-if="props.row.country || props.row.stateprovince || props.row.county || props.row.locality || props.row.minimumelevationinmeters || props.row.maximumelevationinmeters || props.row.verbatimelevation" class="full-width">
                                                                            <span v-if="props.row.country">
                                                                                {{ props.row.country + ((props.row.stateprovince || props.row.county || props.row.locality || props.row.minimumelevationinmeters || props.row.maximumelevationinmeters || props.row.verbatimelevation) ? ', ' : '') }}
                                                                            </span>
                                                                        <span v-if="props.row.stateprovince">
                                                                                {{ props.row.stateprovince + ((props.row.county || props.row.locality || props.row.minimumelevationinmeters || props.row.maximumelevationinmeters || props.row.verbatimelevation) ? ', ' : '') }}
                                                                            </span>
                                                                        <span v-if="props.row.county">
                                                                                {{ props.row.county + ((props.row.locality || props.row.minimumelevationinmeters || props.row.maximumelevationinmeters || props.row.verbatimelevation) ? ', ' : '') }}
                                                                            </span>
                                                                        <span v-if="props.row.locality">
                                                                                {{ props.row.locality + ((props.row.minimumelevationinmeters || props.row.maximumelevationinmeters || props.row.verbatimelevation) ? ', ' : '') }}
                                                                            </span>
                                                                        <span v-if="props.row.minimumelevationinmeters || props.row.maximumelevationinmeters">
                                                                                {{ (props.row.minimumelevationinmeters ? props.row.minimumelevationinmeters : '') + ((props.row.minimumelevationinmeters && props.row.maximumelevationinmeters) ? '-' : '') + (props.row.maximumelevationinmeters ? props.row.maximumelevationinmeters : '') + 'm' }}
                                                                            </span>
                                                                        <span v-else-if="props.row.verbatimelevation">
                                                                                {{ props.row.verbatimelevation }}
                                                                            </span>
                                                                    </div>
                                                                    <div v-if="props.row.informationwithheld" class="text-red">
                                                                        {{ props.row.informationwithheld }}
                                                                    </div>
                                                                    <div>
                                                                        <span role="button" class="cursor-pointer text-body1 text-bold" @click="openRecordInfoWindow(props.row.occid);" tabindex="0">Full Record Details</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-3 row justify-end q-gutter-sm no-wrap">
                                                            <div class="full-width q-pa-xs">
                                                                <template v-if="props.row.img">
                                                                    <q-img :src="props.row.img" class="occurrence-search-image-thumbnail" fit="contain" :alt="(props.row['img-alt'] ? props.row['img-alt'] : ('Image of occurrence record ' + props.row.occid + ' of ' + props.row.sciname))"></q-img>
                                                                </template>
                                                            </div>
                                                            <div v-if="isAdmin || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollAdmin') && currentUserPermissions['CollAdmin'].includes(Number(props.row.collid))) || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollEditor') && currentUserPermissions['CollEditor'].includes(Number(props.row.collid)))" class="col-1">
                                                                <div class="row justify-end vertical-top">
                                                                    <div>
                                                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openOccurrenceEditorInterface(props.row.collid, props.row.occid);" icon="fas fa-edit" dense aria-label="Edit occurrence record" tabindex="0">
                                                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                                Edit occurrence record
                                                                            </q-tooltip>
                                                                        </q-btn>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </q-td>
                                            </q-tr>
                                        </template>
                                        <template v-slot:pagination="scope">
                                            <div class="text-subtitle1 full-width row justify-end">
                                                <div class="self-center text-bold q-mr-xs">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>

                                                <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage" aria-label="Go to first record page" tabindex="0"></q-btn>

                                                <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage" aria-label="Go to previous record page" tabindex="0"></q-btn>

                                                <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage" aria-label="Go to next record page" tabindex="0"></q-btn>

                                                <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage" aria-label="Go to last record page" tabindex="0"></q-btn>
                                            </div>
                                        </template>
                                        <template v-slot:no-data>
                                            <div class="text-bold">Loading...</div>
                                        </template>
                                        <template v-slot:loading>
                                            <q-inner-loading showing color="primary"></q-inner-loading>
                                        </template>
                                    </q-table>
                                </template>
                                <template v-else>
                                    <div class="q-pa-md row justify-center text-h6 text-bold">
                                        There are no records to display. Click the Search button to enter search criteria.
                                    </div>
                                </template>
                            </div>
                        </q-tab-panel>
                        <q-tab-panel class="q-pa-none" name="taxa">
                            <div v-if="taxaCnt > 0" class="column">
                                <div class="q-pa-sm column q-col-gutter-xs">
                                    <div class="row justify-start">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="openQueryPopupDisplay(true);" icon="search" label="Search" aria-label="Open Search Window" tabindex="0" />
                                        </div>
                                    </div>
                                    <div v-if="recordDataArr.length > 0" class="row justify-between q-col-gutter-sm">
                                        <div>
                                            <search-data-downloader :spatial="false"></search-data-downloader>
                                        </div>
                                        <div class="row justify-end q-col-gutter-sm">
                                            <template v-if="keyModuleIsActive">
                                                <key-display-button></key-display-button>
                                            </template>
                                            <checklist-display-button></checklist-display-button>
                                        </div>
                                    </div>
                                </div>
                                <q-separator></q-separator>
                                <div class="q-pa-md">
                                    <div class="text-h6 text-bold">
                                        {{ 'Taxa Count: ' + taxaCnt }}
                                    </div>
                                    <div class="column q-gutter-sm">
                                        <template v-for="family in taxaDataArr">
                                            <div class="q-mt-sm">
                                                <div class="text-h6 text-bold">
                                                    {{ family.name }}
                                                </div>
                                                <div class="q-ml-md column q-gutter-xs">
                                                    <template v-for="taxon in family['taxa']">
                                                        <div>
                                                            <template v-if="Number(taxon.tid) > 0">
                                                                <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon.tid)" target="_blank" :aria-label="(taxon.sciname + ' taxon profile page - Opens in separate tab')" tabindex="0">
                                                                    <span class="text-italic">{{ taxon.sciname }}</span><span class="q-ml-sm">{{ taxon.author }}</span>
                                                                </a>
                                                            </template>
                                                            <template v-else>
                                                                <span class="text-italic">{{ taxon.sciname }}</span><span class="q-ml-sm">{{ taxon.author }}</span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card>
            </div>
        </div>
    `,
    components: {
        'checklist-display-button': checklistDisplayButton,
        'copy-url-button': copyURLButton,
        'image-display-button': imageDisplayButton,
        'key-display-button': keyDisplayButton,
        'search-data-downloader': searchDataDownloader,
        'spatial-display-button': spatialDisplayButton,
        'table-display-button': tableDisplayButton
    },
    setup(_, context) {
        const { hideWorking, showWorking } = useCore();
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const clientRoot = baseStore.getClientRoot;
        const keyModuleIsActive = baseStore.getKeyModuleIsActive;
        const lazyLoadCnt = 100;
        const pageNumber = Vue.ref(1);
        const paginationFirstRecordNumber = Vue.computed(() => {
            let recordNumber = 1;
            if(Number(pageNumber.value) > 1){
                recordNumber += ((Number(pageNumber.value) - 1) * Number(lazyLoadCnt));
            }
            return recordNumber;
        });
        const paginationLastPageNumber = Vue.computed(() => {
            let lastPage = 1;
            if(Number(searchRecordCount.value) > Number(lazyLoadCnt)){
                lastPage = Math.floor(Number(searchRecordCount.value) / Number(lazyLoadCnt));
            }
            if(Number(searchRecordCount.value) % Number(lazyLoadCnt)){
                lastPage++;
            }
            return lastPage;
        });
        const paginationLastRecordNumber = Vue.computed(() => {
            let recordNumber = (Number(searchRecordCount.value) > Number(lazyLoadCnt)) ? Number(lazyLoadCnt) : Number(searchRecordCount.value);
            if(Number(searchRecordCount.value) > Number(lazyLoadCnt) && Number(pageNumber.value) > 1){
                if(Number(pageNumber.value) === Number(paginationLastPageNumber.value)){
                    recordNumber = (Number(searchRecordCount.value) % Number(lazyLoadCnt)) + ((Number(pageNumber.value) - 1) * Number(lazyLoadCnt));
                }
                else{
                    recordNumber = Number(pageNumber.value) * Number(lazyLoadCnt);
                }
            }
            return recordNumber;
        });
        const pagination = Vue.computed(() => {
            return {
                page: pageNumber.value,
                lastPage: paginationLastPageNumber.value,
                rowsPerPage: lazyLoadCnt,
                firstRowNumber: paginationFirstRecordNumber.value,
                lastRowNumber: paginationLastRecordNumber.value,
                rowsNumber: Number(searchRecordCount.value)
            };
        });
        const recordDataArr = Vue.computed(() => searchStore.getSearchRecordData);
        const searchRecordCount = Vue.computed(() => searchStore.getSearchRecordCount);
        const searchTaxaArr = Vue.computed(() => searchStore.getSearchTaxaArr);
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
        const tab = Vue.ref('occurrence');
        const taxaCnt = Vue.ref(0);
        const taxaDataArr = Vue.reactive([]);

        const currentUserPermissions = Vue.inject('currentUserPermissions');
        const isAdmin = Vue.inject('isAdmin');
        const loadRecordsCompleted = Vue.inject('loadRecordsCompleted');

        const openOccurrenceEditorInterface = Vue.inject('openOccurrenceEditorInterface');

        Vue.watch(loadRecordsCompleted, () => {
            if(loadRecordsCompleted.value){
                processSearchRecordCountChange();
            }
        });

        Vue.watch(tab, () => {
            if(tab.value === 'taxa' && !searchStore.getTaxaArrInitialized){
                setSearchTaxaArr();
            }
        });

        function changeRecordPage(props) {
            pageNumber.value = Number(props.pagination.page);
            searchStore.updateSearchTerms('listIndex', pageNumber.value);
            setTableRecordData();
        }

        function openRecordInfoWindow(id) {
            context.emit('open:record-info-window', id);
        }

        function processSearchRecordCountChange() {
            taxaCnt.value = 0;
            taxaDataArr.length = 0;
            if(Number(searchRecordCount.value) > 0){
                setTableRecordData();
            }
        }

        function processTaxaData() {
            searchTaxaArr.value.forEach((taxon) => {
                if(taxon['sciname']){
                    const familyName = (taxon['family'] && taxon['family'] !== '') ? taxon['family'] : '[Family Unknown]';
                    let familyData = taxaDataArr.find((family) => family.name === familyName);
                    if(!familyData){
                        taxaDataArr.push({
                            name: familyName,
                            taxa: []
                        });
                        familyData = taxaDataArr.find((family) => family.name === familyName);
                    }
                    const taxonData = familyData['taxa'].find((taxonObj) => taxonObj.sciname.toLowerCase() === taxon['sciname'].toLowerCase());
                    if(!taxonData){
                        familyData['taxa'].push({
                            tid: taxon['id'],
                            sciname: taxon['sciname'],
                            author: taxon['scientificNameAuthorship']
                        });
                    }
                    else if(Number(taxonData['tid']) === 0 && Number(taxon['id']) > 0){
                        taxonData['tid'] = taxon['id'];
                        taxonData['author'] = taxon['scientificNameAuthorship'];
                    }
                }
            });
            taxaDataArr.sort((a, b) => {
                return a['name'].toLowerCase().localeCompare(b['name'].toLowerCase());
            });
            taxaDataArr.forEach((family) => {
                family['taxa'].sort((a, b) => {
                    return a['sciname'].toLowerCase().localeCompare(b['sciname'].toLowerCase());
                });
            });
            taxaCnt.value = searchTaxaArr.value.length;
            hideWorking();
        }

        function openQueryPopupDisplay() {
            context.emit('open:query-popup');
        }

        function setTableRecordData() {
            showWorking();
            const options = {
                schema: 'occurrence',
                spatial: 0,
                numRows: lazyLoadCnt,
                index: (pageNumber.value - 1),
                output: 'json'
            };
            searchStore.setSearchRecordData(options, () => {
                hideWorking();
            });
        }

        function setSearchTaxaArr() {
            showWorking('Loading...');
            searchStore.setSearchTaxaArr(() => {
                processTaxaData();
            });
        }

        Vue.onMounted(() => {
            if(searchTerms.value.hasOwnProperty('listIndex')){
                pageNumber.value = Number(searchTerms.value['listIndex']);
            }
            if(searchRecordCount.value > 0){
                setTableRecordData();
            }
        });

        return {
            clientRoot,
            currentUserPermissions,
            isAdmin,
            keyModuleIsActive,
            pagination,
            recordDataArr,
            searchTermsJson,
            tab,
            taxaCnt,
            taxaDataArr,
            changeRecordPage,
            openOccurrenceEditorInterface,
            openQueryPopupDisplay,
            openRecordInfoWindow
        }
    }
};
