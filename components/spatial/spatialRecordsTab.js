const spatialRecordsTab = {
    template: `
        <div class="column">
            <div class="q-px-sm q-py-md row justify-between q-gutter-xs">
                <div class="row q-gutter-sm">
                    <search-data-downloader></search-data-downloader>
                </div>
                <div class="row q-gutter-sm">
                    <list-display-button></list-display-button>
                    <table-display-button></table-display-button>
                    <template v-if="searchTermsJson.length <= 1800">
                        <copy-url-button :page-number="pagination.page"></copy-url-button>
                    </template>
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-py-md">
                <q-table flat bordered class="spatial-record-table" :rows="recordDataArr" :columns="columns" row-key="name" :loading="tableLoading" v-model:pagination="pagination" separator="cell" selection="multiple" @request="changeRecordPage" :rows-per-page-options="[0]" wrap-cells>
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
                            <q-td key="siteId" :props="props">
                                {{ props.row.siteid }}
                            </q-td>
                            <q-td key="siteName" :props="props">
                                <div class="column q-gutter-xs">
                                    <div class="fit text-left">
                                        <a class="cursor-pointer" @click="openRecordInfoWindow(props.row.siteid);">{{ props.row.name }}</a>
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
                            <q-td key="landUnitDetail" :props="props">
                                {{ props.row.landunitdetail }}
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
            <q-separator ></q-separator>
        </div>
    `,
    components: {
        'copy-url-button': copyURLButton,
        'list-display-button': listDisplayButton,
        'search-data-downloader': searchDataDownloader,
        'table-display-button': tableDisplayButton
    },
    setup() {
        const searchStore = Vue.inject('searchStore');

        const columns = [
            { name: 'siteId', label: 'Site ID', field: 'siteid' },
            { name: 'siteName', label: 'Site Name', field: 'name' },
            { name: 'landUnitDetail', label: 'Land Unit Detail', field: 'landunitdetail' }
        ];
        const layersObj = Vue.inject('layersObj');
        const pagination = Vue.computed(() => searchStore.getPaginationObj);
        const recordDataArr = Vue.computed(() => searchStore.getSearchRecordData);
        const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
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
            searchStore.processGetQueryResultsRecordData(props.pagination.page);
        }

        function processRecordSelectionChange(selected, record) {
            if(selected){
                searchStore.addRecordToSelections(record);
            }
            else{
                searchStore.removeRecordFromSelections(record.siteid);
            }
            updatePointStyle(record.siteid);
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
            const label = record.name ? record.name : record.siteid.toString();
            const recordPosition = findRecordClusterPosition(record.siteid);
            showPopup(label, recordPosition, false, true);
        }

        Vue.onMounted(() => {
            searchStore.processGetQueryResultsRecordData(pagination.value.page);
        });

        return {
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
