<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$collId = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$stArrJson = array_key_exists('starr', $_REQUEST) ? $_REQUEST['starr'] : '';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Table Display</title>
        <meta name="description" content="Occurrence table display for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol-ext.min.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/turf.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/shp.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jszip.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/stream.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/FileSaver.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/html2canvas.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/geotiff.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/plotty.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const COLLID = <?php echo $collId; ?>;
            const QUERYID = <?php echo $queryId; ?>;
            const STARRJSON = '<?php echo $stArrJson; ?>';
        </script>
    </head>
    <body class="full-window-mode">
        <a class="screen-reader-only" href="#tableContainer">Skip to main content</a>
        <div id="tableContainer">
            <q-table class="sticky-table sticky-column hide-scrollbar" :style="tableStyle" flat bordered dense :rows="recordDataArr" :columns="recordDataFieldArr" row-key="occid" virtual-scroll binary-state-sort v-model:pagination="pagination" :rows-per-page-options="[0]" :visible-columns="visibleColumns" separator="cell" @request="processRequest">
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
                        <div v-if="Number(searchTermsCollId) > 0 && collInfo" class="row justify-start text-h6 text-bold">
                            <template v-if="collInfo.collectionname">{{ collInfo.collectionname }}</template>
                            <template v-if="collInfo.institutioncode || collInfo.collectioncode"> (<template v-if="collInfo.institutioncode">{{ collInfo.institutioncode }}</template><template v-if="collInfo.institutioncode && collInfo.collectioncode">-</template><template v-if="collInfo.collectioncode">{{ collInfo.collectioncode }}</template>)</template>
                        </div>
                        <div class="full-width q-mb-sm row justify-between self-center">
                            <div class="row justify-start q-gutter-sm">
                                <div class="row justify-start self-center q-mr-lg">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayQueryPopup = true" icon="search" label="Search" aria-label="Open Search Window" tabindex="0" />
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
                                <div v-if="recordDataFieldArr.length > 0 && recordDataArr.length > 0" class="self-center">
                                    <q-btn color="primary" @click="showColumnTogglePopup = true" label="Toggle Columns" tabindex="0" />
                                </div>
                            </div>
                            <div v-if="recordDataArr.length > 0" class="q-mr-lg row justify-start q-gutter-sm">
                                <list-display-button></list-display-button>
                                <spatial-display-button></spatial-display-button>
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
                            <span class="text-subtitle1 text-bold">{{ col.label }}</span>
                        </q-th>
                    </q-tr>
                </template>
                <template v-slot:body="props">
                    <q-tr :props="props">
                        <q-td key="occid" :props="props">
                            <span role="button" class="cursor-pointer text-subtitle1" @click="openRecordInfoWindow(props.row.occid);" aria-label="See record details" tabindex="0">{{ props.row.occid }}</span>
                            <template v-if="isAdmin || isEditor || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollAdmin') && currentUserPermissions['CollAdmin'].includes(Number(props.row.collid))) || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollEditor') && currentUserPermissions['CollEditor'].includes(Number(props.row.collid)))">
                                <q-btn color="grey-4" text-color="black" class="q-ml-sm black-border" size="xs" @click="redirectToOccurrenceEditorWithQueryId(props.row.occid, searchTermsCollId);" icon="fas fa-edit" dense aria-label="Edit occurrence record" tabindex="0">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Edit occurrence record
                                    </q-tooltip>
                                </q-btn>
                            </template>
                        </q-td>
                        <template v-for="field in recordDataFieldArr">
                            <q-td v-if="field.name !== 'occid'" :key="field.name" :props="props" :class="field.name === 'sciname' ? 'text-italic' : ''">
                                <span class="text-subtitle1">
                                    {{ (props.row[field.name] && props.row[field.name].length > 60) ? (props.row[field.name].substring(0, 60) + '...') : props.row[field.name] }}
                                </span>
                            </q-td>
                        </template>
                    </q-tr>
                </template>
                <template v-slot:bottom="scope">
                    <div class="full-width row justify-between q-gutter-sm">
                        <div class="text-body2 text-bold self-center">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>
                        <div>
                            <q-pagination
                                :model-value="pagination.page"
                                color="grey-8"
                                :max="pagination.lastPage"
                                size="md"
                                max-pages="10"
                                @update:model-value="processPaginationRequest"
                            ></q-pagination>
                        </div>
                        <div>
                            <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage" aria-label="Go to first record page" tabindex="0"></q-btn>

                            <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage" aria-label="Go to previous record page" tabindex="0"></q-btn>

                            <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage" aria-label="Go to next record page" tabindex="0"></q-btn>

                            <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage" aria-label="Go to last record page" tabindex="0"></q-btn>
                        </div>
                    </div>
                </template>
            </q-table>
            <template v-if="displayBatchUpdatePopup">
                <occurrence-editor-batch-update-popup :show-popup="displayBatchUpdatePopup" @complete:batch-update="loadRecords();" @close:popup="displayBatchUpdatePopup = false"></occurrence-editor-batch-update-popup>
            </template>
            <template v-if="recordInfoWindowId">
                <occurrence-info-window-popup :occurrence-id="recordInfoWindowId" :show-popup="showRecordInfoWindow" @close:popup="closeRecordInfoWindow"></occurrence-info-window-popup>
            </template>
            <template v-if="displayQueryPopup">
                <search-criteria-popup
                    :show-popup="(displayQueryPopup && !showSpatialPopup)"
                    :show-spatial="true"
                    @open:spatial-popup="openSpatialPopup"
                    @process:search-load-records="loadRecords"
                    @reset:search-criteria="processResetCriteria"
                    @close:popup="displayQueryPopup = false"
                ></search-criteria-popup>
            </template>
            <template v-if="showSpatialPopup">
                <spatial-analysis-popup
                    :bottom-lat="spatialInputValues['bottomLatitude']"
                    :circle-arr="spatialInputValues['circleArr']"
                    :left-long="spatialInputValues['leftLongitude']"
                    :point-lat="spatialInputValues['pointLatitude']"
                    :point-long="spatialInputValues['pointLongitude']"
                    :poly-arr="spatialInputValues['polyArr']"
                    :radius="spatialInputValues['radius']"
                    :radius-units="spatialInputValues['radiusUnit']"
                    :right-long="spatialInputValues['rightLongitude']"
                    :upper-lat="spatialInputValues['upperLatitude']"
                    :show-popup="showSpatialPopup"
                    :window-type="popupWindowType"
                    @update:spatial-data="processSpatialData"
                    @close:popup="closeSpatialPopup();"
                ></spatial-analysis-popup>
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
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/determinationRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/geneticLinkRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/colorPicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/copyURLButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/collectionCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/listDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/tableDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/spatialDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDownloadOptionsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDataDownloader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/imageDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/advancedQueryBuilder.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCollectionsBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaPopupTabControls.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRow.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRowGroup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoTabModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/tableColumnTogglePopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorBatchUpdatePopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const occurrenceTableDisplayModule = Vue.createApp({
                components: {
                    'copy-url-button': copyURLButton,
                    'image-display-button': imageDisplayButton,
                    'list-display-button': listDisplayButton,
                    'occurrence-editor-batch-update-popup': occurrenceEditorBatchUpdatePopup,
                    'occurrence-info-window-popup': occurrenceInfoWindowPopup,
                    'search-criteria-popup': searchCriteriaPopup,
                    'search-data-downloader': searchDataDownloader,
                    'selector-input-element': selectorInputElement,
                    'spatial-analysis-popup': spatialAnalysisPopup,
                    'spatial-display-button': spatialDisplayButton,
                    'table-column-toggle-popup': tableColumnTogglePopup
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const occurrenceStore = useOccurrenceStore();
                    const searchStore = useSearchStore();

                    const clientRoot = baseStore.getClientRoot;
                    const collInfo = Vue.computed(() => occurrenceStore.getCollectionData);
                    const currentUserPermissions = Vue.ref(null);
                    const displayBatchUpdatePopup = Vue.ref(false);
                    const displayQueryPopup = Vue.ref(false);
                    const initialCollId = COLLID;
                    const isAdmin = Vue.computed(() => {
                        return currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('SuperAdmin');
                    });
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
                        }
                        if(Number(searchRecordCount.value) % Number(perPageCnt)){
                            lastPage++;
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
                    const perPageCnt = 200;
                    const popupWindowType = Vue.ref(null);
                    const queryId = QUERYID;
                    const recordDataArr = Vue.computed(() => searchStore.getSearchRecordData);
                    const recordDataFieldArr = Vue.computed(() => searchStore.getTableFieldArr);
                    const recordInfoWindowId = Vue.ref(null);
                    const recordsPageNumber = Vue.ref(1);
                    const searchRecordCount = Vue.computed(() => searchStore.getSearchRecordCount);
                    const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
                    const searchTermsCollId = Vue.computed(() => searchStore.getSearchTermsCollId);
                    const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
                    const searchTermsSortDirection = Vue.computed(() => searchStore.getSearchTermsRecordSortDirection);
                    const searchTermsSortField = Vue.computed(() => searchStore.getSearchTermsRecordSortField);
                    const searchTermsValid = Vue.computed(() => searchStore.getSearchTermsValid);
                    const showColumnTogglePopup = Vue.ref(false);
                    const showRecordInfoWindow = Vue.ref(false);
                    const showSpatialPopup = Vue.ref(false);
                    const sortDescending = Vue.ref(false);
                    const sortField = Vue.ref('occid');
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const stArrJson = STARRJSON;
                    const tableColumnToggleOptions = Vue.computed(() => {
                        const returnArr = [];
                        recordDataFieldArr.value.forEach((field) => {
                            if(field.name !== 'occid'){
                                returnArr.push(field);
                            }
                        });
                        return returnArr;
                    });
                    const tableStyle = Vue.ref('');
                    const visibleColumns = Vue.computed(() => searchStore.getTableVisibleFields);

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

                    function closeRecordInfoWindow(){
                        recordInfoWindowId.value = null;
                        showRecordInfoWindow.value = false;
                    }

                    function closeSpatialPopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        searchStore.clearSpatialInputValues();
                    }

                    function loadRecords(initial = false) {
                        if(searchTermsValid.value || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                            searchStore.clearQueryResultData();
                            showWorking('Loading...');
                            const options = {
                                schema: 'occurrence',
                                display: 'table',
                                spatial: 0,
                                sortField: sortField.value,
                                sortDirection: (sortDescending.value ? 'DESC' : 'ASC')
                            };
                            searchStore.setSearchRecordCount(options, () => {
                                if(Number(searchStore.getSearchRecordCount) > 0){
                                    if(!initial){
                                        displayQueryPopup.value = false;
                                    }
                                    setTableRecordData();
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
                        searchStore.setSpatialInputValues();
                        popupWindowType.value = type;
                        showSpatialPopup.value = true;
                    }

                    function processPaginationRequest(page) {
                        showWorking();
                        recordsPageNumber.value = page;
                        setTableRecordData();
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
                            searchStore.setSearchTermsRecordSortDirection(sortDescending.value ? 'DESC' : 'ASC');
                            recordsPageNumber.value = 1;
                        }
                        else{
                            recordsPageNumber.value = props.pagination.page;
                        }
                        setTableRecordData();
                    }

                    function processResetCriteria() {
                        if(occurrenceEditorModeActive.value){
                            loadRecords();
                        }
                    }

                    function processSpatialData(data) {
                        searchStore.processSpatialPopupData(popupWindowType.value, data);
                    }

                    function setCollection(collid) {
                        if(Number(collid) > 0){
                            occurrenceStore.setCollection(collid, false, () => {
                                if(isEditor.value){
                                    if(!searchTerms.value.hasOwnProperty('collid') || Number(searchTerms.value['collid']) === 0 || Number(searchTerms.value['collid']) !== Number(searchTermsCollId.value)){
                                        searchStore.updateSearchTerms('collid', collid);
                                    }
                                    loadRecords(true);
                                }
                                else{
                                    searchStore.updateSearchTerms('db', [collid]);
                                    displayQueryPopup.value = true;
                                }
                            });
                        }
                    }

                    function setCurrentUserPermissions() {
                        baseStore.getGlobalConfigValue('USER_RIGHTS', (dataStr) => {
                            const data = dataStr ? JSON.parse(dataStr) : null;
                            if(data && Object.keys(data).length > 0){
                                currentUserPermissions.value = data;
                            }
                        });
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
                            if(recordsPageNumber.value === 1){
                                searchStore.setTableVisibleFields();
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
                        setCurrentUserPermissions();
                        if(Number(queryId) === 0 && !stArrJson){
                            displayQueryPopup.value = true;
                        }
                        searchStore.initializeSearchStorage(queryId);
                        if(Number(queryId) > 0 || stArrJson){
                            if(stArrJson){
                                searchStore.loadSearchTermsArrFromJson(stArrJson.replaceAll('%squot;', "'"));
                            }
                            if(searchTermsValid.value || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                                if(searchTerms.value.hasOwnProperty('tableIndex')){
                                    recordsPageNumber.value = Number(searchTerms.value['tableIndex']);
                                }
                                if(Number(initialCollId) === 0 && Number(searchTerms.value['collid']) > 0){
                                    setCollection(searchTerms.value['collid']);
                                }
                                else{
                                    loadRecords();
                                }
                            }
                        }
                        if(Number(initialCollId) > 0){
                            setCollection(initialCollId);
                        }
                    });

                    return {
                        clientRoot,
                        collInfo,
                        currentUserPermissions,
                        displayBatchUpdatePopup,
                        displayQueryPopup,
                        isAdmin,
                        isEditor,
                        occurrenceFieldLabels,
                        recordsPageNumber,
                        pagination,
                        popupWindowType,
                        recordDataArr,
                        recordDataFieldArr,
                        recordInfoWindowId,
                        searchTermsCollId,
                        searchTermsJson,
                        searchTermsSortDirection,
                        searchTermsSortField,
                        searchTermsValid,
                        showColumnTogglePopup,
                        showRecordInfoWindow,
                        showSpatialPopup,
                        sortField,
                        spatialInputValues,
                        tableColumnToggleOptions,
                        tableStyle,
                        visibleColumns,
                        closeRecordInfoWindow,
                        closeSpatialPopup,
                        loadRecords,
                        openRecordInfoWindow,
                        openSpatialPopup,
                        processPaginationRequest,
                        processRequest,
                        processResetCriteria,
                        processSpatialData,
                        redirectToOccurrenceEditorWithQueryId: searchStore.redirectToOccurrenceEditorWithQueryId,
                        updateVisibleColumns
                    }
                }
            });
            occurrenceTableDisplayModule.use(Quasar, { config: {} });
            occurrenceTableDisplayModule.use(Pinia.createPinia());
            occurrenceTableDisplayModule.mount('#tableContainer');
        </script>
    </body>
</html>
