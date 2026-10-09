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
                                <q-card role="button" flat bordered class="cursor-pointer" @click="openPopup(image);" :style="cardStyle" :aria-label="( image['sciname'] + ' image profile page page - Opens in separate tab')" tabindex="0">
                                    <q-img class="rounded-borders" :height="imageHeight" :src="(image['url'].startsWith('/') ? (clientRoot + image['url']) : image['url'])" fit="scale-down" :alt="(image['alttext'] ? image['alttext'] : image['sciname'])"></q-img>
                                    <q-card-section class="q-pa-sm">
                                        <div class="column text-body1 text-black">
                                            <span class="column text-bold text-italic">
                                                {{ image['sciname'] }}
                                            </span>
                                            <span v-if="Number(image['occid']) > 0" class="column text-bold text-italic">
                                                {{ image['institutioncode'] }}: 
                                                {{ image['catalognumber'] }}
                                            </span>
                                            <template v-if="image['photographer']">
                                                <span class="q-ml-sm text-bold">{{ image['photographer'] }}</span>
                                            </template>
                                            <template v-if="editing">
                                                <span class="q-ml-sm">
                                                    <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openEditorPopup(image['cltlid']);" icon="far fa-edit" dense aria-label="Edit this image" tabindex="0">
                                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                            Edit this image
                                                        </q-tooltip>
                                                    </q-btn>
                                                </span>
                                            </template>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </template>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="q-pa-md row justify-center text-h6 text-bold">
                        There are no records to display. Click the Search button to enter search criteria.
                    </div>
                </template>
                <template v-if="showOccurrenceInfoPopup">
                    <occurrence-info-window-popup :occurrence-id="occurrenceId" :show-popup="showOccurrenceInfoPopup" @close:popup="closePopup"></occurrence-info-window-popup>
                </template>
                <template v-if="showMediaInfoPopup">
                    <media-info-window-popup :image-data="imageData" :show-popup="showMediaInfoPopup" @close:popup="closePopup"></media-info-window-popup>
                </template>
            </div>
        </div>
        <template v-if="recordInfoWindowId">
           <occurrence-info-window-popup :occurrence-id="recordInfoWindowId" :show-popup="showRecordInfoWindow" @close:popup="closeRecordInfoWindow"></occurrence-info-window-popup>
        </template>
    `,
    components: {
        'checklist-display-button': checklistDisplayButton,
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
        const imageData = Vue.ref(null);
        const imgDataArr = Vue.reactive([]);
        const imageHeight = Vue.ref(null);
        const imgPerPage = 100;
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
        const searchTaxaArr = Vue.computed(() => searchStore.getSearchTaxaArr);
        const searchImgArr = Vue.computed(() => searchStore.getSearchImgidArr);
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
        const tab = Vue.ref('occurrence');
        const taxaCnt = Vue.ref(0);
        const taxaDataArr = Vue.reactive([]);

        const currentUserPermissions = Vue.inject('currentUserPermissions');
        const isAdmin = Vue.inject('isAdmin');
        const loadRecordsCompleted = Vue.inject('loadRecordsCompleted');

        const openOccurrenceEditorInterface = Vue.inject('openOccurrenceEditorInterface');

        Vue.watch(containerRef, () => {
            setContentStyle();
        });

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
            if(Number(image['occid']) > 0){
                openRecordInfoWindow(image['occid']);
            }
            else{
                context.emit('open:image-info-window', image);
            }
        }

        function openRecordInfoWindow(id) {
            context.emit('open:record-info-window', id);
        }

        function processImgData() {
            searchImgArr.value.forEach((taxon) => {
                if(taxon['sciname']){
                    const familyName = (taxon['family'] && taxon['family'] !== '') ? taxon['family'] : '[Family Unknown]';
                    let familyData = imgDataArr.find((family) => family.name === familyName);
                    if(!familyData){
                        imgDataArr.push({
                            name: familyName,
                            taxa: []
                        });
                        familyData = imgDataArr.find((family) => family.name === familyName);
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
            imgDataArr.sort((a, b) => {
                return a['name'].toLowerCase().localeCompare(b['name'].toLowerCase());
            });
            imgDataArr.forEach((family) => {
                family['taxa'].sort((a, b) => {
                    return a['sciname'].toLowerCase().localeCompare(b['sciname'].toLowerCase());
                });
            });
            taxaCnt.value = searchImgArr.value.length;
            hideWorking();
        }

        function processSearchRecordCountChange() {
            taxaCnt.value = 0;
            taxaDataArr.length = 0;
            if(Number(searchStore.getSearchImgCount) > 0){
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
        function setSearchTaxaArr() {
            showWorking('Loading...');
            searchStore.setSearchTaxaArr(() => {
                processTaxaData();
            });
        }

        Vue.onMounted(() => {
            setContentStyle();
            if(searchTerms.value.hasOwnProperty('listIndex')){
                pageNumber.value = Number(searchTerms.value['listIndex']);
            }
            if(searchStore.getSearchImgCount > 0){
                console.log("setting grid");
                setTableRecordData();
            }
            window.addEventListener('resize', setContentStyle);
        });

        return {
            cardStyle,
            clientRoot,
            currentUserPermissions,
            imageData,
            imageHeight,
            imgPerPage,
            isAdmin,
            openPopup,
            pageNumber,
            pagination,
            paginationLastPageNumber,
            paginationLastRecordNumber,
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
