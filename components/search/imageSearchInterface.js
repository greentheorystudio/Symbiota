const imageSearchInterface = {
    template: `
        <div id="breadcrumbs">
            <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
            <span class="text-bold">Search Collections Image Display</span>
        </div>
        <div class="q-pa-md">
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
                            <list-display-button></list-display-button>
                            <template v-if="searchTermsJson.length <= 1800">
                                <copy-url-button></copy-url-button>
                            </template>
                        </div>
                    </div>
                </div>
                <q-separator></q-separator>
                <template v-if="recordDataArr.length > 0">
                    <template v-if="paginationLastPageNumber > 1">
                        <div class="q-mb-sm q-px-md full-width row justify-end">
                            <q-pagination v-model="pageNumber" :max="paginationLastPageNumber" direction-links flat color="grey" active-color="primary" max-pages="10" aria-label="Image search page navigation" @update:model-value="changeRecordPage"></q-pagination>
                        </div>
                        <q-separator></q-separator>
                    </template>
                    <div ref="containerRef" class="fit q-pa-sm">
                        <div class="full-width row q-gutter-sm">
                            <template v-for="image in recordDataArr">
                                <q-card v-if="image.hasOwnProperty('url')" role="button" flat bordered class="cursor-pointer" @click="openPopup(image);" :style="cardStyle" :aria-label="( image['sciname'] + ' image profile page page - Opens in separate tab')" tabindex="0">
                                    <q-img class="rounded-borders" :height="imageHeight" :src="(image['url'].startsWith('/') ? (clientRoot + image['url']) : image['url'])" fit="scale-down" :alt="(image['alttext'] ? image['alttext'] : image['sciname'])"></q-img>
                                    <q-card-section class="q-pa-sm">
                                        <div class="column text-body1 text-black">
                                            <div class="column text-bold text-italic">
                                                {{ image['sciname'] }}
                                            </div>
                                            <div v-if="Number(image['occid']) > 0" class="column text-bold text-italic">
                                                {{ image['institutioncode'] }}: 
                                                {{ image['catalognumber'] }}
                                            </div>
                                            <template v-if="image['photographer']">
                                                <div>{{ image['photographer'] }}</div>
                                            </template>
                                            <div v-if="validatePermissions(image)" class="row justify-end vertical-top">
                                                <div>
                                                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openRecordEditingPopup(image);" icon="fas fa-edit" dense aria-label="Edit record" tabindex="0">
                                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                            Edit record
                                                        </q-tooltip>
                                                    </q-btn>
                                                </div>
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </template>
                        </div>
                    </div>
                    <template v-if="paginationLastPageNumber > 1">
                        <div class="q-mb-sm q-px-md full-width row justify-end">
                            <q-pagination v-model="pageNumber" :max="paginationLastPageNumber" direction-links flat color="grey" active-color="primary" max-pages="10" aria-label="Image search page navigation" @update:model-value="changeRecordPage"></q-pagination>
                        </div>
                        <q-separator></q-separator>
                    </template>
                </template>
                <template v-else>
                    <div class="q-pa-md row justify-center text-h6 text-bold">
                        There are no records to display. Click the Search button to enter search criteria.
                    </div>
                </template>
            </div>
        </div>
    `,
    components: {
        'copy-url-button': copyURLButton,
        'list-display-button': listDisplayButton,
        'search-data-downloader': searchDataDownloader,
        'spatial-display-button': spatialDisplayButton,
        'table-display-button': tableDisplayButton
    },
    setup(_, context) {
        const { hideWorking, showWorking } = useCore();
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const cardStyle = Vue.ref(null);
        const clientRoot = baseStore.getClientRoot;
        const containerRef = Vue.ref(null);
        const editorOpening = Vue.ref(false);
        const imageData = Vue.ref(null);
        const imageHeight = Vue.ref(null);
        const imgPerPage = 100;
        const isTaxonProfileEditor = Vue.computed(() => {
            return (isAdmin.value || (currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('TaxonProfile')));
        });
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
            if(Number(searchStore.getSearchImgCount) > Number(lazyLoadCnt)){
                lastPage = Math.floor(Number(searchStore.getSearchImgCount) / Number(lazyLoadCnt));
            }
            if(Number(searchStore.getSearchImgCount) > Number(lazyLoadCnt) && Number(searchStore.getSearchImgCount) % Number(lazyLoadCnt)){
                lastPage++;
            }
            return lastPage;
        });
        const paginationLastRecordNumber = Vue.computed(() => {
            let recordNumber = (Number(searchStore.getSearchImgCount) > Number(lazyLoadCnt)) ? Number(lazyLoadCnt) : Number(searchStore.getSearchImgCount);
            if(Number(searchStore.getSearchImgCount) > Number(lazyLoadCnt) && Number(pageNumber.value) > 1){
                if(Number(pageNumber.value) === Number(paginationLastPageNumber.value)){
                    recordNumber = (Number(searchStore.getSearchImgCount) % Number(lazyLoadCnt)) + ((Number(pageNumber.value) - 1) * Number(lazyLoadCnt));
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
                rowsNumber: Number(searchStore.getSearchImgCount)
            };
        });
        const recordDataArr = Vue.computed(() => searchStore.getSearchRecordData);
        const searchRecordCount = Vue.computed(() => searchStore.getSearchImgCount);
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);

        const currentUserPermissions = Vue.inject('currentUserPermissions');
        const isAdmin = Vue.inject('isAdmin');
        const loadRecordsCompleted = Vue.inject('loadRecordsCompleted');

        const openImageEditorPopup = Vue.inject('openImageEditorPopup');
        const openOccurrenceEditorInterface = Vue.inject('openOccurrenceEditorInterface');

        Vue.watch(containerRef, () => {
            setContentStyle();
        });

        Vue.watch(loadRecordsCompleted, () => {
            if(loadRecordsCompleted.value){
                processSearchRecordCountChange();
            }
        });

        Vue.watch(searchRecordCount, () => {
            if(searchRecordCount.value > 0){
                setTableRecordData();
            }
        });

        function changeRecordPage(page) {
            pageNumber.value = Number(page);
            searchStore.updateSearchTerms('listIndex', pageNumber.value);
            setTableRecordData();
        }

        function openPopup(image) {
            if(!editorOpening.value){
                if(Number(image['occid']) > 0){
                    context.emit('open:record-info-window', image['occid']);
                }
                else{
                    context.emit('open:image-info-window', image);
                }
            }
        }

        function openQueryPopupDisplay() {
            context.emit('open:query-popup');
        }

        function openRecordEditingPopup(record) {
            editorOpening.value = true;
            if(Number(record['occid']) > 0){
                openOccurrenceEditorInterface(record['collid'], record['occid']);
            }
            else{
                openImageEditorPopup(record['imgid']);
            }
            setTimeout(() => {
                editorOpening.value = false;
            }, 200 );
        }

        function processSearchRecordCountChange() {
            if(Number(searchStore.getSearchImgCount) > 0){
                setTableRecordData();
            }
        }

        function setContentStyle() {
            cardStyle.value = null;
            imageHeight.value = null;
            if(containerRef.value){
                let cardDim;
                if(containerRef.value.clientWidth > 900){
                    cardDim = (containerRef.value.clientWidth / 4) - 30;
                }
                else if(containerRef.value.clientWidth > 600){
                    cardDim = (containerRef.value.clientWidth / 3) - 30;
                }
                else if(containerRef.value.clientWidth > 400){
                    cardDim = (containerRef.value.clientWidth / 2) - 30;
                }
                else{
                    cardDim = containerRef.value.clientWidth - 30;
                }
                cardStyle.value = 'width: ' + cardDim + 'px;';
                imageHeight.value = cardDim + 'px';
            }
        }

        function setTableRecordData() {
            showWorking();
            const options = {
                schema: 'image',
                spatial: 0,
                numRows: lazyLoadCnt,
                index: (pageNumber.value - 1),
                output: 'json'
            };
            searchStore.setSearchRecordData(options, () => {
                hideWorking();
            });
        }

        function validatePermissions(record) {
            let returnVal = false;
            if(isAdmin.value){
                returnVal = true;
            }
            else if(Number(record['occid']) > 0){
                if(currentUserPermissions.value && ((currentUserPermissions.value.hasOwnProperty('CollAdmin') && currentUserPermissions.value['CollAdmin'].includes(Number(record['collid']))) || (currentUserPermissions.value.hasOwnProperty('CollEditor') && currentUserPermissions.value['CollEditor'].includes(Number(record['collid']))))){
                    returnVal = true;
                }
            }
            else if(Number(record['occid']) === 0 && isTaxonProfileEditor.value){
                returnVal = true;
            }
            return returnVal;
        }

        Vue.onMounted(() => {
            setContentStyle();
            if(searchTerms.value.hasOwnProperty('listIndex')){
                pageNumber.value = Number(searchTerms.value['listIndex']);
            }
            if(searchStore.getSearchImgCount > 0){
                setTableRecordData();
            }
            window.addEventListener('resize', setContentStyle);
        });

        return {
            cardStyle,
            clientRoot,
            containerRef,
            imageData,
            imageHeight,
            imgPerPage,
            openPopup,
            pageNumber,
            pagination,
            paginationLastPageNumber,
            paginationLastRecordNumber,
            recordDataArr,
            searchTermsJson,
            changeRecordPage,
            openOccurrenceEditorInterface,
            openQueryPopupDisplay,
            openRecordEditingPopup,
            validatePermissions
        }
    }
};
