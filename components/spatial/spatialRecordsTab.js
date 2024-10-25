const spatialRecordsTab = {
    template: `
        <div class="column">
            <div class="q-pa-sm full-width row justify-between q-col-gutter-xs">
                <div class="col-12 col-md-8">
                    <search-data-downloader :spatial="true"></search-data-downloader>
                </div>
                <div class="col-12 col-md-4 offset-md-grow">
                    <div class="row q-gutter-sm">
                        <list-display-button></list-display-button>
                        <table-display-button></table-display-button>
                        <image-display-button></image-display-button>
                        <template v-if="searchTermsJson.length <= 1800">
                            <copy-url-button :page-number="pagination.page"></copy-url-button>
                        </template>
                    </div>
                </div>
            </div>
            <q-separator ></q-separator>
            <div>
                <q-table flat bordered class="spatial-record-table" :rows="recordDataArr" :columns="columns" row-key="name" :loading="tableLoading" v-model:pagination="pagination" separator="cell" selection="multiple" @request="changeRecordPage" :rows-per-page-options="[0]" wrap-cells dense>
                    <template v-slot:top="scope">
                        <div class="spatial-record-table-top-pagination row justify-end">
                            <div class="self-center text-bold q-mr-xs">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>
                        
                            <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage"></q-btn>
                            
                            <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage"></q-btn>
                            
                            <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage"></q-btn>
                            
                            <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage"></q-btn>
                        </div>
                    </template>
                    <template v-slot:header="props">
                        <q-tr :props='props' class="bg-blue-grey-2">
                            <q-th>
                                <q-checkbox v-model="selectedRecordSelectAllVal" @update:model-value="processSelectAllChange" dense />
                            </q-th>
                            <q-th v-for="col in props.cols" :key="col.name" :props="props">
                                {{ col.label }}
                            </q-th>
                        </q-tr>
                    </template>
                    <template v-slot:body="props">
                        <q-tr v-if="!tableLoading" :props="props">
                            <q-td>
                                <q-checkbox v-model="props.row.selected" @update:model-value="(val) => processRecordSelectionChange(val, props.row)" dense />
                            </q-td>
                            <q-td key="catalognumber" :props="props">
                                {{ props.row.catalognumber }}
                            </q-td>
                            <q-td key="collector" :props="props">
                                <div class="column q-gutter-xs">
                                    <div class="fit text-left">
                                        <a class="cursor-pointer" @click="openRecordInfoWindow(props.row.occid);">{{ (props.row.collector ? props.row.collector : '[No data]') }}</a>
                                    </div>
                                    <div class="row justify-end">
                                        <q-btn color="grey-4" size="xs" text-color="black" class="q-ml-sm black-border" icon="fas fa-search-location" @click="setMapFinderPopup(props.row);" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                See location on map
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </q-td>
                            <q-td key="eventdate" :props="props">
                                {{ props.row.eventdate }}
                            </q-td>
                            <q-td key="sciname" :props="props">
                                <template v-if="Number(props.row.tid) > 0">
                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.tid)" target="_blank">{{ props.row.sciname }}</a>
                                </template>
                                <template v-else>
                                    {{ props.row.sciname }}
                                </template>
                            </q-td>
                        </q-tr>
                    </template>
                    <template v-slot:pagination="scope">
                        <div class="text-bold q-mr-xs">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>
                        
                        <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage"></q-btn>
                        
                        <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage"></q-btn>
                        
                        <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage"></q-btn>
                        
                        <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage"></q-btn>
                    </template>
                    <template v-slot:no-data>
                        <div class="text-bold">Loading...</div>
                    </template>
                    <template v-slot:loading>
                        <q-inner-loading showing color="primary"></q-inner-loading>
                    </template>
                </q-table>
            </div>
        </div>
    `,
    components: {
        'copy-url-button': copyURLButton,
        'image-display-button': imageDisplayButton,
        'list-display-button': listDisplayButton,
        'search-data-downloader': searchDataDownloader,
        'table-display-button': tableDisplayButton
    },
    setup() {
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const clientRoot = baseStore.getClientRoot;
        const columns = [
            { name: 'catalognumber', label: 'Catalog #', field: 'catalognumber' },
            { name: 'collector', label: 'Collector', field: 'collector' },
            { name: 'eventdate', label: 'Date', field: 'eventdate' },
            { name: 'sciname', label: 'Scientific Name', field: 'sciname' }
        ];
        const layersObj = Vue.inject('layersObj');
        const lazyLoadCnt = 100;
        const pageNumber = Vue.ref(1);
        const searchRecordCount = Vue.computed(() => searchStore.getSearchRecCnt);
        const paginationFirstRecordNumber = Vue.computed(() => {
            let recordNumber = 1;
            if(Number(pageNumber.value) > 1){
                recordNumber = recordNumber + ((Number(pageNumber.value) - 1) * Number(lazyLoadCnt));
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
        const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
        const searchTermsPageNumber = Vue.computed(() => searchStore.getSearchTermsPageNumber);
        const selectedRecordCount = Vue.computed(() => searchStore.getSearchRecordSelectedCount);
        const selectedRecordSelectAllVal = Vue.computed(() => {
            return (selectedRecordCount.value > 0 && selectedRecordCount.value < 100) ? null : selectedRecordCount.value !== 0;
        });
        const tableLoading = Vue.computed(() => (recordDataArr.value.length === 0));

        const findRecordClusterPosition = Vue.inject('findRecordClusterPosition');
        const openRecordInfoWindow = Vue.inject('openRecordInfoWindow');
        const showPopup = Vue.inject('showPopup');
        const updatePointStyle = Vue.inject('updatePointStyle');

        function changeRecordPage(props) {
            setTableRecordData(props.pagination.page);
        }

        function processRecordSelectionChange(selected, record) {
            if(selected){
                searchStore.addRecordToSelections(record);
            }
            else{
                searchStore.removeRecordFromSelections(record.occid);
            }
            updatePointStyle(record.occid);
        }

        function processSelectAllChange(selected) {
            if(selected){
                searchStore.selectAllCurrentRecords();
            }
            else{
                searchStore.deselectAllCurrentRecords();
            }
            layersObj['pointv'].getSource().changed();
        }

        function setMapFinderPopup(record) {
            const label = record.collector ? record.collector : record.occid.toString();
            const recordPosition = findRecordClusterPosition(record.occid);
            showPopup(label, recordPosition, false, true);
        }

        function setTableRecordData(index) {
            const options = {
                schema: 'map',
                spatial: 1,
                numRows: lazyLoadCnt,
                index: index,
                output: 'json'
            };
            searchStore.setSearchRecordData(options);
            pageNumber.value = Number(index);
        }

        Vue.onMounted(() => {
            if(Number(searchTermsPageNumber.value) > Number(pageNumber.value)){
                pageNumber.value = searchTermsPageNumber.value;
            }
            setTableRecordData(pagination.value.page);
        });

        return {
            clientRoot,
            searchTermsJson,
            columns,
            pagination,
            recordDataArr,
            selectedRecordSelectAllVal,
            tableLoading,
            changeRecordPage,
            openRecordInfoWindow,
            processRecordSelectionChange,
            processSelectAllChange,
            setMapFinderPopup
        }
    }
};
