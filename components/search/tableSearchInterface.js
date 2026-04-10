const tableSearchInterface = {
    props: {
        collid: {
            type: Number,
            default: null
        },
        occid: {
            type: Number,
            default: 0
        }
    },
    template: `
        <div id="tableContainer">
            <q-table ref="tableRef" class="sticky-table sticky-column hide-scrollbar" :style="tableStyle" flat bordered dense :rows="recordDataArr" :columns="recordDataFieldArr" row-key="occid" virtual-scroll binary-state-sort v-model:pagination="pagination" :rows-per-page-options="[0]" :visible-columns="visibleColumns" separator="cell" @request="processRequest">
                <template v-slot:no-data>
                    <div class="fit row flex-center text-h6 text-bold">
                        There are no records to display. Click the Search button to enter search criteria.
                    </div>
                </template>
                <template v-slot:top>
                    <div class="full-width column">
                        <div class="q-mb-sm">
                            <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                            <template v-if="Number(searchTermsCollId) > 0 && isEditor">
                                <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + searchTermsCollId)" tabindex="0">Collection Control Panel</a> &gt;&gt;
                                <span class="text-bold">View/Edit Existing Records</span>
                            </template>
                            <template v-else>
                                <span class="text-bold">Search Collections Table Display</span>
                            </template>
                        </div>
                        <div v-if="occurrenceEditorModeActive && collInfo" class="row justify-start text-h6 text-bold">
                            <template v-if="collInfo.collectionname">{{ collInfo.collectionname }}</template>
                            <template v-if="collInfo.institutioncode || collInfo.collectioncode"> (<template v-if="collInfo.institutioncode">{{ collInfo.institutioncode }}</template><template v-if="collInfo.institutioncode && collInfo.collectioncode">-</template><template v-if="collInfo.collectioncode">{{ collInfo.collectioncode }}</template>)</template>
                        </div>
                        <div class="full-width q-mb-sm row justify-between self-center">
                            <div class="row justify-start q-gutter-sm">
                                <div class="row justify-start self-center q-mr-lg">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="openQueryPopupDisplay();" icon="search" label="Search" aria-label="Open Search Window" tabindex="0" />
                                </div>
                                <div v-if="recordDataArr.length > 0">
                                    <search-data-downloader :spatial="false"></search-data-downloader>
                                </div>
                                <div v-if="recordDataArr.length > 0 && Number(searchTermsCollId) > 0 && isEditor" class="self-center">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayBatchUpdatePopup = true" icon="find_replace" dense aria-label="Open Batch Update Tool" :disabled="!searchTermsValid" tabindex="0">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Batch Update Tool
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                                <div v-if="recordDataFieldArr.length > 0 && recordDataArr.length > 0" class="self-center q-mr-sm">
                                    <q-btn color="primary" @click="showColumnTogglePopup = true" label="Toggle Columns" tabindex="0" />
                                </div>
                                <div v-if="recordDataFieldArr.length > 0 && recordDataArr.length > 0" class="self-center">
                                    <div>
                                        <q-btn-toggle v-model="selectedTextSize" rounded glossy toggle-color="primary" color="white" text-color="primary" :options="textSizeOptions"></q-btn-toggle>
                                    </div>
                                </div>
                            </div>
                            <div v-if="recordDataArr.length > 0" class="q-mr-lg row justify-start q-gutter-sm">
                                <list-display-button :navigator-mode="true"></list-display-button>
                                <spatial-display-button :navigator-mode="true"></spatial-display-button>
                                <image-display-button></image-display-button>
                                <template v-if="searchTermsJson.length <= 1800">
                                    <copy-url-button></copy-url-button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:header="props">
                    <q-tr :props="props">
                        <q-th v-for="col in props.cols" :key="col.name" :props="props" class="bg-grey-4">
                            <span :class="textSizeClass" class="text-bold">{{ col.label }}</span>
                        </q-th>
                    </q-tr>
                </template>
                <template v-slot:body="props">
                    <q-tr :props="props">
                        <q-td key="occid" :props="props" class="self-center">
                            <span role="button" :class="textSizeClass" class="cursor-pointer" @click="openRecordInfoWindow(props.row.occid);" aria-label="See record details" tabindex="0">{{ props.row.occid }}</span>
                            <template v-if="isAdmin || isEditor || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollAdmin') && currentUserPermissions['CollAdmin'].includes(Number(props.row.collid))) || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollEditor') && currentUserPermissions['CollEditor'].includes(Number(props.row.collid)))">
                                <q-btn color="grey-4" text-color="black" class="q-ml-sm black-border" size="xs" @click="openOccurrenceEditorInterface(searchTermsCollId, props.row.occid);" icon="fas fa-edit" dense aria-label="Edit occurrence record" tabindex="0">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Edit occurrence record
                                    </q-tooltip>
                                </q-btn>
                            </template>
                        </q-td>
                        <template v-for="field in recordDataFieldArr">
                            <q-td v-if="field.name !== 'occid'" :key="field.name" :props="props" :class="field.name === 'sciname' ? 'text-italic' : ''">
                                <span :class="textSizeClass">
                                    {{ (props.row[field.name] && props.row[field.name].length > 60) ? (props.row[field.name].substring(0, 60) + '...') : props.row[field.name] }}
                                </span>
                            </q-td>
                        </template>
                    </q-tr>
                </template>
                <template v-slot:bottom="scope">
                    <div class="full-width row justify-between q-gutter-sm">
                        <div class="text-body2 text-bold self-center">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>
                        <div v-if="pagination.lastPage > 1">
                            <div class="row q-gutter-sm">
                                <q-pagination
                                    ref="paginationRef"
                                    :model-value="pagination.page"
                                    color="grey-8"
                                    :max="pagination.lastPage"
                                    size="md"
                                    max-pages="8"
                                    @update:model-value="processPaginationRequest"
                                ></q-pagination>
                                <div class="table-page-number-input">
                                    <text-field-input-element data-type="int" :max-value="pagination.lastPage" min-value="1" :value="pagination.page" @update:value="processInputPaginationRequest" :clearable="false"></text-field-input-element>
                                </div>
                            </div>
                        </div>
                        <div class="self-center">
                            <template v-if="pagination.lastPage > 1">
                                <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage" aria-label="Go to first record page" tabindex="0"></q-btn>

                                <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage" aria-label="Go to previous record page" tabindex="0"></q-btn>

                                <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage" aria-label="Go to next record page" tabindex="0"></q-btn>

                                <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage" aria-label="Go to last record page" tabindex="0"></q-btn>
                            </template>
                        </div>
                    </div>
                </template>
            </q-table>
            <template v-if="displayBatchUpdatePopup">
                <occurrence-editor-batch-update-popup :show-popup="displayBatchUpdatePopup" @complete:batch-update="processBatchUpdate" @close:popup="displayBatchUpdatePopup = false"></occurrence-editor-batch-update-popup>
            </template>
            <template v-if="showColumnTogglePopup">
                <table-column-toggle-popup
                    :field-arr="tableColumnToggleOptions"
                    :show-popup="showColumnTogglePopup"
                    :visible-columns="visibleColumns"
                    @update:visible-columns="updateVisibleColumns"
                    @close:popup="showColumnTogglePopup = false"
                ></table-column-toggle-popup>
            </template>
        </div>
    `,
    components: {
        'copy-url-button': copyURLButton,
        'image-display-button': imageDisplayButton,
        'list-display-button': listDisplayButton,
        'occurrence-editor-batch-update-popup': occurrenceEditorBatchUpdatePopup,
        'search-data-downloader': searchDataDownloader,
        'selector-input-element': selectorInputElement,
        'spatial-display-button': spatialDisplayButton,
        'table-column-toggle-popup': tableColumnTogglePopup,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showWorking } = useCore();
        const baseStore = useBaseStore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const clientRoot = baseStore.getClientRoot;
        const collInfo = Vue.computed(() => occurrenceStore.getCollectionData);
        const displayBatchUpdatePopup = Vue.ref(false);
        const goToOccid = Vue.ref(null);
        const initialSearchResults = Vue.ref(false);
        const isEditor = Vue.computed(() => occurrenceStore.getIsEditor);
        const occurrenceEditorModeActive = Vue.computed(() => searchStore.getOccurrenceEditorModeActive);
        const occurrenceFieldLabels = Vue.computed(() => searchStore.getOccurrenceFieldLabels);
        const pagination = Vue.computed(() => {
            return {
                sortBy: sortField.value,
                descending: sortDescending.value,
                page: recordsPageNumber.value,
                lastPage: paginationLastPageNumber.value,
                rowsPerPage: perPageCnt,
                firstRowNumber: paginationFirstRecordNumber.value,
                lastRowNumber: paginationLastRecordNumber.value,
                rowsNumber: searchRecordCount.value
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
            if(Number(searchRecordCount.value) > Number(perPageCnt)){
                lastPage = Math.floor(Number(searchRecordCount.value) / Number(perPageCnt));
                if(Number(searchRecordCount.value) % Number(perPageCnt)){
                    lastPage++;
                }
            }
            return lastPage;
        });
        const paginationLastRecordNumber = Vue.computed(() => {
            let recordNumber = (Number(searchRecordCount.value) > Number(perPageCnt)) ? Number(perPageCnt) : Number(searchRecordCount.value);
            if(Number(searchRecordCount.value) > Number(perPageCnt) && Number(recordsPageNumber.value) > 1){
                if(Number(recordsPageNumber.value) === Number(paginationLastPageNumber.value)){
                    recordNumber = (Number(searchRecordCount.value) % Number(perPageCnt)) + ((Number(recordsPageNumber.value) - 1) * Number(perPageCnt));
                }
                else{
                    recordNumber = Number(recordsPageNumber.value) * Number(perPageCnt);
                }
            }
            return recordNumber;
        });
        const paginationRef = Vue.ref(null);
        const perPageCnt = 200;
        const recordDataArr = Vue.computed(() => searchStore.getSearchRecordData);
        const recordDataFieldArr = Vue.computed(() => searchStore.getTableFieldArr);
        const recordsPageNumber = Vue.ref(1);
        const searchRecordCount = Vue.computed(() => searchStore.getSearchRecordCount);
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const searchTermsCollId = Vue.computed(() => searchStore.getSearchTermsCollId);
        const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
        const searchTermsSortDirection = Vue.computed(() => searchStore.getSearchTermsRecordSortDirection);
        const searchTermsSortField = Vue.computed(() => searchStore.getSearchTermsRecordSortField);
        const searchTermsValid = Vue.computed(() => searchStore.getSearchTermsValid);
        const selectedTextSize = Vue.ref('medium');
        const showColumnTogglePopup = Vue.ref(false);
        const sortDescending = Vue.ref(false);
        const sortField = Vue.ref('occid');
        const tableColumnToggleOptions = Vue.computed(() => {
            const returnArr = [];
            recordDataFieldArr.value.forEach((field) => {
                if(field.name !== 'occid'){
                    returnArr.push(field);
                }
            });
            return returnArr;
        });
        const tableRef = Vue.ref(null);
        const tableStyle = Vue.ref('');
        const textSizeClass = Vue.computed(() => {
            let textClass = '';
            if(selectedTextSize.value === 'large'){
                textClass = 'text-subtitle1'
            }
            else if(selectedTextSize.value === 'medium'){
                textClass = 'text-body2'
            }
            return textClass;
        });
        const textSizeOptions = Vue.ref([
            { label: 'Large text', value: 'large' },
            { label: 'Medium text', value: 'medium' },
            { label: 'Small text', value: 'small' }
        ]);
        const visibleColumns = Vue.computed(() => searchStore.getTableVisibleFields);

        const currentUserPermissions = Vue.inject('currentUserPermissions');
        const isAdmin = Vue.inject('isAdmin');
        const loadRecordsCompleted = Vue.inject('loadRecordsCompleted');

        const openOccurrenceEditorInterface = Vue.inject('openOccurrenceEditorInterface');

        Vue.watch(loadRecordsCompleted, () => {
            if(loadRecordsCompleted.value){
                processSearchRecordCountChange();
            }
        });

        Vue.watch(searchTermsSortDirection, () => {
            if(sortDescending.value && searchTermsSortDirection.value !== 'DESC'){
                sortDescending.value = false;
            }
        });

        Vue.watch(searchTermsSortField, () => {
            if(sortField.value !== searchTermsSortField.value){
                sortField.value = searchTermsSortField.value;
            }
        });

        function findGoToOccidPage() {
            const occIndex = searchStore.getCurrentOccIdIndex;
            if(occIndex > 0){
                if(occIndex < perPageCnt){
                    recordsPageNumber.value = 1;
                }
                else{
                    recordsPageNumber.value = Math.ceil(occIndex / perPageCnt);
                }
                setTableRecordData();
            }
        }

        function goToRecord(occid) {
            const record = recordDataArr.value.find(record => Number(record['occid']) === Number(occid));
            if(record){
                setTimeout(() => {
                    const index = recordDataArr.value.indexOf(record);
                    tableRef.value.scrollTo(((index + 10) <= perPageCnt) ? (index + 10) : index);
                }, 200);
            }
        }

        function openQueryPopupDisplay() {
            context.emit('open:query-popup');
        }

        function openRecordInfoWindow(id) {
            context.emit('open:record-info-window', id);
        }

        function processBatchUpdate() {
            context.emit('load:records');
        }

        function processInputPaginationRequest(page) {
            paginationRef.value.set(page);
        }

        function processPaginationRequest(page) {
            showWorking();
            recordsPageNumber.value = page;
            setTableRecordData();
        }

        function processSearchRecordCountChange() {
            if(Number(searchRecordCount.value) > 0){
                initialSearchResults.value = true;
                setTableRecordData();
            }
            else{
                setTableStyle();
            }
        }

        function processRequest(props) {
            showWorking();
            let sortChange = false;
            if(props.pagination.sortBy !== sortField.value || props.pagination.descending !== sortDescending.value){
                sortChange = true;
            }
            sortField.value = props.pagination.sortBy;
            sortDescending.value = props.pagination.descending;
            if(sortChange){
                searchStore.setSearchTermsRecordSortField(sortField.value);
                searchStore.updateSearchTerms('sortField', sortField.value);
                searchStore.setSearchTermsRecordSortDirection(sortDescending.value ? 'DESC' : 'ASC');
                searchStore.updateSearchTerms('sortDirection', (sortDescending.value ? 'DESC' : 'ASC'));
                recordsPageNumber.value = 1;
            }
            else{
                recordsPageNumber.value = props.pagination.page;
            }
            setTableRecordData();
        }

        function setTableRecordData() {
            searchStore.updateSearchTerms('tableIndex', recordsPageNumber.value);
            const options = {
                schema: 'occurrence',
                display: 'table',
                spatial: 0,
                numRows: perPageCnt,
                index: (recordsPageNumber.value - 1),
                sortField: sortField.value,
                sortDirection: (sortDescending.value ? 'DESC' : 'ASC'),
                output: 'json'
            };
            searchStore.setSearchRecordData(options, () => {
                if(initialSearchResults.value){
                    searchStore.setTableVisibleFields();
                    initialSearchResults.value = false;
                }
                if(Number(goToOccid.value) > 0){
                    goToRecord(goToOccid.value);
                    goToOccid.value = null;
                }
                else{
                    tableRef.value.scrollTo(0);
                }
                setTableStyle();
                hideWorking();
            });
        }

        function setTableStyle() {
            let styleStr = '';
            styleStr += 'width: ' + window.innerWidth + 'px;';
            if(recordDataArr.value.length > 0){
                styleStr += 'height: ' + window.innerHeight + 'px;';
            }
            else{
                styleStr += 'height: 0;';
            }
            tableStyle.value = styleStr;
        }

        function updateVisibleColumns(value) {
            searchStore.updateTableVisibleFields(value);
        }

        Vue.onMounted(() => {
            window.addEventListener('resize', setTableStyle);
            setTableStyle();
            if(searchTerms.value.hasOwnProperty('tableIndex')){
                recordsPageNumber.value = Number(searchTerms.value['tableIndex']);
            }
            if(searchRecordCount.value > 0){
                initialSearchResults.value = true;
                if(Number(props.occid) > 0){
                    goToOccid.value = Number(props.occid);
                    searchStore.setCurrentOccId(goToOccid.value);
                    findGoToOccidPage();
                }
                else{
                    setTableRecordData();
                }
            }
        });

        return {
            clientRoot,
            collInfo,
            currentUserPermissions,
            displayBatchUpdatePopup,
            isAdmin,
            isEditor,
            occurrenceEditorModeActive,
            occurrenceFieldLabels,
            pagination,
            paginationRef,
            recordDataArr,
            recordDataFieldArr,
            recordsPageNumber,
            searchTermsCollId,
            searchTermsJson,
            searchTermsSortDirection,
            searchTermsSortField,
            searchTermsValid,
            selectedTextSize,
            showColumnTogglePopup,
            sortField,
            tableColumnToggleOptions,
            tableRef,
            tableStyle,
            textSizeClass,
            textSizeOptions,
            visibleColumns,
            openOccurrenceEditorInterface,
            openQueryPopupDisplay,
            openRecordInfoWindow,
            processBatchUpdate,
            processInputPaginationRequest,
            processPaginationRequest,
            processRequest,
            updateVisibleColumns
        }
    }
};
