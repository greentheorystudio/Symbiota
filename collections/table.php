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
        <style>
            table.styledtable td {
                white-space: nowrap;
            }
        </style>
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
        <div id="tableContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <template v-if="Number(searchTermsCollId) > 0 && isEditor">
                    <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + searchTermsCollId)" tabindex="0">Collection Control Panel</a> &gt;&gt;
                    <span class="text-bold">View/Edit Existing Records</span>
                </template>
                <template v-else>
                    <span class="text-bold">Search Collections Table Display</span>
                </template>
            </div>
            <div v-if="Number(searchTermsCollId) > 0 && collInfo" class="row justify-start text-h6 text-weight-bold">
                <template v-if="collInfo.collectionname">{{ collInfo.collectionname }}</template>
                <template v-if="collInfo.institutioncode || collInfo.collectioncode"> (<template v-if="collInfo.institutioncode">{{ collInfo.institutioncode }}</template><template v-if="collInfo.institutioncode && collInfo.collectioncode">-</template><template v-if="collInfo.collectioncode">{{ collInfo.collectioncode }}</template>)</template>
            </div>
            <div class="q-mb-sm row justify-start q-col-gutter-sm">
                <div class="col-3">
                    <selector-input-element label="Sort by" :options="fieldOptions" option-value="field" option-label="label" :value="sortField" @update:value="processSortFieldChange"></selector-input-element>
                </div>
                <div class="col-2">
                    <selector-input-element :options="sortDirectionOptions" :value="sortDirection" @update:value="processSortDirectionChange"></selector-input-element>
                </div>
                <div>
                    <q-btn color="secondary" @click="loadRecords()" label="Sort" aria-label="Sort records" tabindex="0" />
                </div>
            </div>
            <div class="q-mb-sm row justify-start q-col-gutter-md self-center">
                <div class="q-mr-lg row justify-start q-gutter-sm">
                    <div class="row justify-start self-center q-mr-lg">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayQueryPopup = true" icon="search" label="Search" aria-label="Open Search Window" tabindex="0" />
                    </div>
                    <div v-if="recordDataArr.length > 0">
                        <search-data-downloader :spatial="false"></search-data-downloader>
                    </div>
                    <div v-if="recordDataArr.length > 0 && Number(searchTermsCollId) > 0 && isEditor" class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayBatchUpdatePopup = true" icon="find_replace" dense aria-label="Open Batch Update Tool" tabindex="0">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Batch Update Tool
                            </q-tooltip>
                        </q-btn>
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
                <div v-if="recordDataArr.length > 0" class="row justify-start">
                    <div class="self-center text-body2 text-bold q-mr-xs">Records {{ pagination.firstRowNumber }} - {{ pagination.lastRowNumber }} of {{ pagination.rowsNumber }}</div>

                    <q-btn v-if="pagination.lastPage > 2 && pageNumber > 1" icon="first_page" color="grey-8" round dense flat @click="setTableRecordData(1);" aria-label="Go to first record page" tabindex="0"></q-btn>

                    <q-btn v-if="pageNumber > 1" icon="chevron_left" color="grey-8" round dense flat @click="setTableRecordData(pageNumber - 1);" aria-label="Go to previous record page" tabindex="0"></q-btn>

                    <q-btn v-if="pageNumber < pagination.lastPage" icon="chevron_right" color="grey-8" round dense flat @click="setTableRecordData(pageNumber + 1);" aria-label="Go to next record page" tabindex="0"></q-btn>

                    <q-btn v-if="pagination.lastPage > 2 && pageNumber < pagination.lastPage" icon="last_page" color="grey-8" round dense flat @click="setTableRecordData(pagination.lastPage);" aria-label="Go to last record page" tabindex="0"></q-btn>
                </div>
            </div>
            <template v-if="recordDataArr.length > 0">
                <div>
                    <table class="styledtable">
                        <tr>
                            <template v-for="field in recordDataFieldArr">
                                <th class="no-wrap">{{ occurrenceFieldLabels[field] }}</th>
                            </template>
                        </tr>
                        <template v-for="record in recordDataArr">
                            <tr>
                                <template v-for="field in recordDataFieldArr">
                                    <td :class="field === 'sciname' ? 'text-italic' : ''">
                                        <template v-if="field === 'occid'">
                                            <span class="cursor-pointer" @click="openRecordInfoWindow(record[field]);">{{ record[field] }}</span>
                                            <template v-if="isAdmin || isEditor || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollAdmin') && currentUserPermissions['CollAdmin'].includes(Number(record['collid']))) || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollEditor') && currentUserPermissions['CollEditor'].includes(Number(record['collid'])))">
                                                <q-btn color="grey-4" text-color="black" class="q-ml-sm black-border" size="xs" @click="redirectToOccurrenceEditorWithQueryId(record[field], searchTermsCollId);" icon="fas fa-edit" dense aria-label="Edit occurrence record" tabindex="0">
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Edit occurrence record
                                                    </q-tooltip>
                                                </q-btn>
                                            </template>
                                        </template>
                                        <template v-else>
                                            {{ (record[field] && record[field].length > 60) ? (record[field].substring(0, 60) + '...') : record[field] }}
                                        </template>
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </table>
                </div>
            </template>
            <template v-else>
                <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                <div class="q-pa-md row justify-center text-h6 text-bold">
                    There are no records to display. Click the Search button to enter search criteria.
                </div>
            </template>
            <div v-if="recordDataArr.length > 0" class="q-mt-xs row justify-start q-col-gutter-md self-center">
                <div class="q-mr-lg row justify-start q-gutter-sm">
                    <div class="row justify-start self-center q-mr-lg">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayQueryPopup = true" icon="search" label="Search" aria-label="Open Search Window" tabindex="0" />
                    </div>
                    <div>
                        <search-data-downloader :spatial="false"></search-data-downloader>
                    </div>
                    <div v-if="Number(searchTermsCollId) > 0 && isEditor" class="self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayBatchUpdatePopup = true" icon="find_replace" dense aria-label="Open Batch Update Tool" tabindex="0">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Batch Update Tool
                            </q-tooltip>
                        </q-btn>
                    </div>
                </div>
                <div class="q-mr-lg row justify-start q-gutter-sm">
                    <list-display-button></list-display-button>
                    <spatial-display-button></spatial-display-button>
                    <image-display-button></image-display-button>
                    <template v-if="searchTermsJson.length <= 1800">
                        <copy-url-button></copy-url-button>
                    </template>
                </div>
                <div class="row justify-start">
                    <div class="self-center text-body2 text-bold q-mr-xs">Records {{ pagination.firstRowNumber }} - {{ pagination.lastRowNumber }} of {{ pagination.rowsNumber }}</div>

                    <q-btn v-if="pagination.lastPage > 2 && pageNumber > 1" icon="first_page" color="grey-8" round dense flat @click="setTableRecordData(1);" aria-label="Go to first record page" tabindex="0"></q-btn>

                    <q-btn v-if="pageNumber > 1" icon="chevron_left" color="grey-8" round dense flat @click="setTableRecordData(pageNumber - 1);" aria-label="Go to previous record page" tabindex="0"></q-btn>

                    <q-btn v-if="pageNumber < pagination.lastPage" icon="chevron_right" color="grey-8" round dense flat @click="setTableRecordData(pageNumber + 1);" aria-label="Go to next record page" tabindex="0"></q-btn>

                    <q-btn v-if="pagination.lastPage > 2 && pageNumber < pagination.lastPage" icon="last_page" color="grey-8" round dense flat @click="setTableRecordData(pagination.lastPage);" aria-label="Go to last record page" tabindex="0"></q-btn>
                </div>
            </div>
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
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        ?>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
                    'spatial-display-button': spatialDisplayButton
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
                    const fieldOptions = Vue.computed(() => searchStore.getQueryBuilderFieldOptions);
                    const initialCollId = COLLID;
                    const isAdmin = Vue.computed(() => {
                        return currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('SuperAdmin');
                    });
                    const isEditor = Vue.computed(() => occurrenceStore.getIsEditor);
                    const lazyLoadCnt = 200;
                    const occurrenceEditorModeActive = Vue.computed(() => searchStore.getOccurrenceEditorModeActive);
                    const occurrenceFieldLabels = Vue.computed(() => searchStore.getOccurrenceFieldLabels);
                    const pageNumber = Vue.ref(1);
                    const searchRecordCount = Vue.computed(() => searchStore.getSearchRecordCount);
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
                    const popupWindowType = Vue.ref(null);
                    const queryId = QUERYID;
                    const recordDataArr = Vue.computed(() => searchStore.getSearchRecordData);
                    const recordDataFieldArr = Vue.computed(() => searchStore.getSearchRecordDataFieldArr);
                    const recordInfoWindowId = Vue.ref(null);
                    const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
                    const searchTermsCollId = Vue.computed(() => searchStore.getSearchTermsCollId);
                    const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
                    const searchTermsSortDirection = Vue.computed(() => searchStore.getSearchTermsRecordSortDirection);
                    const searchTermsSortField = Vue.computed(() => searchStore.getSearchTermsRecordSortField);
                    const showRecordInfoWindow = Vue.ref(false);
                    const showSpatialPopup = Vue.ref(false);
                    const sortDirection = Vue.ref('ASC');
                    const sortDirectionOptions = [
                        { label: 'Ascending', value: 'ASC' },
                        { label: 'Descending', value: 'DESC' }
                    ];
                    const sortField = Vue.ref(null);
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const stArrJson = STARRJSON;

                    Vue.watch(searchTermsSortDirection, () => {
                        sortDirection.value = searchTermsSortDirection.value;
                    });

                    Vue.watch(searchTermsSortField, () => {
                        sortField.value = searchTermsSortField.value;
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
                        if(searchStore.getSearchTermsValid || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                            searchStore.clearQueryOccidArr();
                            showWorking('Loading...');
                            const options = {
                                schema: 'occurrence',
                                display: 'table',
                                spatial: 0,
                                sortField: sortField.value,
                                sortDirection: sortDirection.value
                            };
                            searchStore.setSearchOccidArr(options, () => {
                                if(Number(searchStore.getSearchRecordCount) > 0){
                                    if(!initial){
                                        displayQueryPopup.value = false;
                                    }
                                    setTableRecordData(pageNumber.value);
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

                    function processResetCriteria() {
                        if(occurrenceEditorModeActive.value){
                            loadRecords();
                        }
                    }

                    function processSortDirectionChange(value) {
                        sortDirection.value = value;
                        searchStore.setSearchTermsRecordSortDirection(value);
                        searchStore.updateSearchTerms('sortDirection', value);
                    }

                    function processSortFieldChange(value) {
                        sortField.value = value;
                        searchStore.setSearchTermsRecordSortField(value);
                        searchStore.updateSearchTerms('sortField', value);
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

                    function setTableRecordData(index) {
                        searchStore.updateSearchTerms('tableIndex', index);
                        const options = {
                            schema: 'occurrence',
                            display: 'table',
                            spatial: 0,
                            numRows: lazyLoadCnt,
                            index: (index - 1),
                            sortField: sortField.value,
                            sortDirection: sortDirection.value,
                            output: 'json'
                        };
                        searchStore.setSearchRecordData(options, () => {
                            hideWorking();
                        });
                        pageNumber.value = Number(index);
                    }

                    Vue.onMounted(() => {
                        setCurrentUserPermissions();
                        if(Number(queryId) === 0 && !stArrJson){
                            displayQueryPopup.value = true;
                        }
                        searchStore.initializeSearchStorage(queryId);
                        if(Number(queryId) > 0 || stArrJson){
                            if(stArrJson){
                                searchStore.loadSearchTermsArrFromJson(stArrJson.replaceAll('%squot;', "'"));
                            }
                            if(searchStore.getSearchTermsValid || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                                if(searchTerms.value.hasOwnProperty('tableIndex')){
                                    pageNumber.value = Number(searchTerms.value['tableIndex']);
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
                        fieldOptions,
                        isAdmin,
                        isEditor,
                        occurrenceFieldLabels,
                        pageNumber,
                        pagination,
                        popupWindowType,
                        recordDataArr,
                        recordDataFieldArr,
                        recordInfoWindowId,
                        searchTermsCollId,
                        searchTermsJson,
                        searchTermsSortDirection,
                        searchTermsSortField,
                        showRecordInfoWindow,
                        showSpatialPopup,
                        sortDirection,
                        sortDirectionOptions,
                        sortField,
                        spatialInputValues,
                        closeRecordInfoWindow,
                        closeSpatialPopup,
                        loadRecords,
                        openRecordInfoWindow,
                        openSpatialPopup,
                        processResetCriteria,
                        processSortDirectionChange,
                        processSortFieldChange,
                        processSpatialData,
                        redirectToOccurrenceEditorWithQueryId: searchStore.redirectToOccurrenceEditorWithQueryId,
                        setTableRecordData
                    }
                }
            });
            occurrenceTableDisplayModule.use(Quasar, { config: {} });
            occurrenceTableDisplayModule.use(Pinia.createPinia());
            occurrenceTableDisplayModule.mount('#tableContainer');
        </script>
    </body>
</html>
