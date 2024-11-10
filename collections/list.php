<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/OccurrenceListManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$queryId = array_key_exists('queryId',$_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$stArrJson = array_key_exists('starr',$_REQUEST) ? $_REQUEST['starr'] : '';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Search Results List</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
        <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol-ext.min.js?ver=20240115" type="text/javascript"></script>
        <script src="https://npmcdn.com/@turf/turf/turf.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/shp.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jszip.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/stream.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/FileSaver.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/html2canvas.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/geotiff.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/plotty.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const QUERYID = <?php echo $queryId; ?>;
            const STARRJSON = '<?php echo $stArrJson; ?>';

            function getTaxaList(){
                document.getElementById("taxalist").innerHTML = "<p>Loading...</p>";
                const http = new XMLHttpRequest();
                const url = "../api/search/getchecklist.php";
                const jsonStarr = encodeURIComponent(JSON.stringify(stArr));
                const params = 'starr='+jsonStarr;
                //console.log(url+'?'+params);
                http.open("POST", url, true);
                http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                http.onreadystatechange = function() {
                    if(http.readyState === 4 && http.status === 200) {
                        document.getElementById("taxalist").innerHTML = http.responseText;
                    }
                };
                http.send(params);
            }
        </script>
    </head>
    <body>
    <?php
    include(__DIR__ . '/../header.php');
    ?>
    <div class="navpath">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
            <b>Search Criteria</b>
        </div>
        <div id="innertext">
            <div class="fit">
                <q-card flat bordered>
                    <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                        <q-tab name="occurrence" label="Occurrence Records" no-caps></q-tab>
                        <q-tab name="taxa" label="Taxa List" no-caps :disable="true"></q-tab>
                    </q-tabs>
                    <q-separator></q-separator>
                    <q-tab-panels v-model="tab">
                        <q-tab-panel class="q-pa-none" name="occurrence">
                            <div class="column">
                                <div class="q-pa-sm column q-col-gutter-xs">
                                    <div class="row justify-start">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="setQueryPopupDisplay(true);" icon="search" label="Search" dense />
                                        </div>
                                    </div>
                                    <div class="row justify-between q-col-gutter-sm">
                                        <div>
                                            <search-data-downloader :spatial="false"></search-data-downloader>
                                        </div>
                                        <div class="row justify-end q-col-gutter-sm">
                                            <table-display-button></table-display-button>
                                            <spatial-display-button></spatial-display-button>
                                            <image-display-button></image-display-button>
                                            <template v-if="searchTermsJson.length <= 1800">
                                                <copy-url-button :page-number="pagination.page"></copy-url-button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <q-separator ></q-separator>
                                <template v-if="recordDataArr.length > 0">
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
                                </template>
                                <template v-else>
                                    <div class="q-pa-md row justify-center text-h6 text-bold">
                                        There are no records to display. Click the Search button to enter search criteria.
                                    </div>
                                </template>
                            </div>
                        </q-tab-panel>
                        <q-tab-panel class="q-pa-none" name="taxa">
                            <div class="column q-pa-sm q-col-gutter-md">
                                <div class="row justify-end q-col-gutter-sm">
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="resetCriteria();" label="Reset" dense />
                                    </div>
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="loadRecords();" label="Search Records" :disabled="!searchTermsValid" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                {{ searchRecordsTooltip }}
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                                <advanced-query-builder :field-options="advancedFieldOptions" query-type="advanced"></advanced-query-builder>
                            </div>
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card>
            </div>
            <template v-if="recordInfoWindowId">
                <occurrence-info-window-popup :occurrence-id="recordInfoWindowId" :show-popup="showRecordInfoWindow" @close:popup="closeRecordInfoWindow"></occurrence-info-window-popup>
            </template>
            <template v-if="displayQueryPopup">
                <search-criteria-popup :show-popup="(displayQueryPopup && !showSpatialPopup)" :show-spatial="true" @open:spatial-popup="openSpatialPopup" @close:popup="setQueryPopupDisplay(false)"></search-criteria-popup>
            </template>
            <template v-if="showSpatialPopup">
                <spatial-analysis-popup
                    :bottom-lat="bottomLatitude"
                    :circle-arr="circleArr"
                    :left-long="leftLongitude"
                    :point-lat="pointLatitude"
                    :point-long="pointLongitude"
                    :poly-arr="polyArr"
                    :radius="radius"
                    :right-long="rightLongitude"
                    :upper-lat="upperLatitude"
                    :show-popup="showSpatialPopup"
                    :window-type="popupWindowType"
                    @update:spatial-data="processSpatialData"
                    @close:popup="closeSpatialPopup();"
                ></spatial-analysis-popup>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/determinationRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/geneticLinkRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/colorPicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/copyURLButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/collectionCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/listDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/spatialDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDownloadOptionsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDataDownloader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/tableDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/imageDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/advancedQueryBuilder.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCollectionsBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRecordsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSelectionsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSymbologyTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanelLeftShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanelTopShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSidePanelShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSideButtonTray.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialRasterColorScaleSelect.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialVectorToolsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialPointVectorToolsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRecordsSymbologyExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialVectorToolsExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRasterToolsExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSidePanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialDrawToolSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialBaseLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialActiveLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialMapSettingsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerLayerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerLayerGroupElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerQuerySelectorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script>
            const searchListDisplayModule = Vue.createApp({
                components: {
                    'copy-url-button': copyURLButton,
                    'image-display-button': imageDisplayButton,
                    'occurrence-info-window-popup': occurrenceInfoWindowPopup,
                    'search-criteria-popup': searchCriteriaPopup,
                    'search-data-downloader': searchDataDownloader,
                    'spatial-analysis-popup': spatialAnalysisPopup,
                    'spatial-display-button': spatialDisplayButton,
                    'table-display-button': tableDisplayButton
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const searchStore = useSearchStore();

                    const bottomLatitude = Vue.ref(null);
                    const circleArr = Vue.ref(null);
                    const clientRoot = baseStore.getClientRoot;
                    const columns = [
                        { name: 'catalognumber', label: 'Catalog #', field: 'catalognumber' },
                        { name: 'collector', label: 'Collector', field: 'collector' },
                        { name: 'eventdate', label: 'Date', field: 'eventdate' },
                        { name: 'sciname', label: 'Scientific Name', field: 'sciname' }
                    ];
                    const displayQueryPopup = Vue.ref(false);
                    const lazyLoadCnt = 100;
                    const leftLongitude = Vue.ref(null);
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
                    const pointLatitude = Vue.ref(null);
                    const pointLongitude = Vue.ref(null);
                    const polyArr = Vue.ref(null);
                    const popupWindowType = Vue.ref(null);
                    const queryId = QUERYID;
                    const radius = Vue.ref(null);
                    const recordDataArr = Vue.computed(() => searchStore.getSearchRecordData);
                    const recordInfoWindowId = Vue.ref(null);
                    const rightLongitude = Vue.ref(null);
                    const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
                    const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
                    const searchTermsPageNumber = Vue.computed(() => searchStore.getSearchTermsPageNumber);
                    const showRecordInfoWindow = Vue.ref(false);
                    const showSpatialPopup = Vue.ref(false);
                    const stArrJson = STARRJSON;
                    const tab = Vue.ref('occurrence');
                    const tableLoading = Vue.computed(() => (recordDataArr.value.length === 0));
                    const upperLatitude = Vue.ref(null);

                    function changeRecordPage(props) {
                        setTableRecordData(props.pagination.page);
                    }

                    function clearSpatialInputValues() {
                        bottomLatitude.value = null;
                        circleArr.value = null;
                        leftLongitude.value = null;
                        pointLatitude.value = null;
                        pointLongitude.value = null;
                        polyArr.value = null;
                        radius.value = null;
                        rightLongitude.value = null;
                        upperLatitude.value = null;
                    }

                    function closeRecordInfoWindow(){
                        recordInfoWindowId.value = null;
                        showRecordInfoWindow.value = false;
                    }

                    function closeSpatialPopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        clearSpatialInputValues();
                    }

                    function loadRecords(){
                        if(searchStore.getSearchTermsValid){
                            searchStore.clearSelections();
                            showWorking('Loading...');
                            const options = {
                                schema: 'occurrence',
                                spatial: 0
                            };
                            searchStore.setSearchOccidArr(options, () => {
                                if(Number(searchStore.getSearchRecCnt) > 0){
                                    displayQueryPopup.value = false;
                                    setTableRecordData(pagination.value.page);
                                }
                                else{
                                    hideWorking();
                                    showNotification('negative','There were no records matching your query.');
                                }
                            });
                        }
                        else{
                            showNotification('negative','Please enter search criteria.');
                        }
                    }

                    function openRecordInfoWindow(id) {
                        recordInfoWindowId.value = id;
                        showRecordInfoWindow.value = true;
                    }

                    function openSpatialPopup(type) {
                        setSpatialInputValues();
                        popupWindowType.value = type;
                        showSpatialPopup.value = true;
                    }

                    function processSpatialData(data) {
                        if(popupWindowType.value.includes('box') && data.hasOwnProperty('boundingBoxArr')){
                            searchStore.updateSearchTerms('upperlat', data['boundingBoxArr']['upperlat']);
                            searchStore.updateSearchTerms('bottomlat', data['boundingBoxArr']['bottomlat']);
                            searchStore.updateSearchTerms('leftlong', data['boundingBoxArr']['leftlong']);
                            searchStore.updateSearchTerms('rightlong', data['boundingBoxArr']['rightlong']);
                        }
                        else if(popupWindowType.value.includes('circle') && data.hasOwnProperty('circleArr') && data['circleArr'].length === 1){
                            searchStore.updateSearchTerms('pointlat', data['circleArr'][0]['pointlat']);
                            searchStore.updateSearchTerms('pointlong', data['circleArr'][0]['pointlong']);
                            searchStore.updateSearchTerms('radius', data['circleArr'][0]['radius']);
                            searchStore.updateSearchTerms('groundradius', data['circleArr'][0]['groundradius']);
                            searchStore.updateSearchTerms('radiusval', (data['circleArr'][0]['radius'] / 1000));
                            searchStore.updateSearchTerms('radiusunit', 'km');
                        }
                        else if(popupWindowType.value === 'input' && (data.hasOwnProperty('circleArr') || data.hasOwnProperty('polyArr'))){
                            if(data.hasOwnProperty('circleArr')){
                                searchStore.updateSearchTerms('circleArr', data['circleArr']);
                            }
                            if(data.hasOwnProperty('polyArr')){
                                searchStore.updateSearchTerms('polyArr', data['polyArr']);
                            }
                        }
                    }

                    function setQueryPopupDisplay(val) {
                        displayQueryPopup.value = val;
                    }

                    function setSpatialInputValues() {
                        bottomLatitude.value = searchTerms.value.hasOwnProperty('bottomlat') ? searchTerms.value['bottomlat'] : null;
                        circleArr.value = searchTerms.value.hasOwnProperty('circleArr') ? searchTerms.value['circleArr'] : null;
                        leftLongitude.value = searchTerms.value.hasOwnProperty('leftlong') ? searchTerms.value['leftlong'] : null;
                        pointLatitude.value = searchTerms.value.hasOwnProperty('pointlat') ? searchTerms.value['pointlat'] : null;
                        pointLongitude.value = searchTerms.value.hasOwnProperty('pointlong') ? searchTerms.value['pointlong'] : null;
                        polyArr.value = searchTerms.value.hasOwnProperty('polyArr') ? searchTerms.value['polyArr'] : null;
                        radius.value = searchTerms.value.hasOwnProperty('radius') ? searchTerms.value['radius'] : null;
                        rightLongitude.value = searchTerms.value.hasOwnProperty('rightlong') ? searchTerms.value['rightlong'] : null;
                        upperLatitude.value = searchTerms.value.hasOwnProperty('upperlat') ? searchTerms.value['upperlat'] : null;
                    }

                    function setTableRecordData(index) {
                        const options = {
                            schema: 'occurrence',
                            spatial: 0,
                            numRows: lazyLoadCnt,
                            index: (index - 1),
                            output: 'json'
                        };
                        searchStore.setSearchRecordData(options);
                        pageNumber.value = Number(index);
                    }

                    Vue.provide('setQueryPopupDisplay', setQueryPopupDisplay);

                    Vue.onMounted(() => {
                        if(queryId || stArrJson){
                            showWorking('Loading...');
                        }
                        searchStore.initializeSearchStorage(queryId);
                        if(queryId || stArrJson){
                            if(stArrJson){
                                searchStore.loadSearchTermsArrFromJson(stArrJson.replaceAll('%squot;', "'"));
                            }
                            if(searchStore.getSearchTermsValid){
                                if(Number(searchTermsPageNumber.value) > Number(pageNumber.value)){
                                    pageNumber.value = searchTermsPageNumber.value;
                                }
                                loadRecords();
                            }
                        }
                        else{
                            displayQueryPopup.value = true;
                        }
                    });

                    return {
                        bottomLatitude,
                        circleArr,
                        clientRoot,
                        columns,
                        displayQueryPopup,
                        leftLongitude,
                        pagination,
                        pointLatitude,
                        pointLongitude,
                        polyArr,
                        popupWindowType,
                        radius,
                        recordDataArr,
                        recordInfoWindowId,
                        rightLongitude,
                        searchTermsJson,
                        showRecordInfoWindow,
                        showSpatialPopup,
                        tab,
                        tableLoading,
                        upperLatitude,
                        changeRecordPage,
                        closeRecordInfoWindow,
                        closeSpatialPopup,
                        openRecordInfoWindow,
                        openSpatialPopup,
                        processSpatialData,
                        setQueryPopupDisplay
                    }
                }
            });
            searchListDisplayModule.use(Quasar, { config: {} });
            searchListDisplayModule.use(Pinia.createPinia());
            searchListDisplayModule.mount('#innertext');
        </script>
    </body>
</html>
