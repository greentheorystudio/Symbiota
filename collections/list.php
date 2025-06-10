<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$stArrJson = array_key_exists('starr', $_REQUEST) ? $_REQUEST['starr'] : '';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Collection Search List Display</title>
        <meta name="description" content="Collection search list display for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
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
            const QUERYID = <?php echo $queryId; ?>;
            const STARRJSON = '<?php echo $stArrJson; ?>';
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')">Home</a> &gt;&gt;
                <span class="text-bold">Search Collections</span>
            </div>
            <div class="q-pa-md">
                <div class="fit">
                    <q-card flat bordered>
                        <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                            <q-tab name="occurrence" label="Occurrence Records" no-caps></q-tab>
                            <q-tab name="taxa" label="Taxa List" no-caps :disable="taxaCnt === 0"></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel class="q-pa-none" name="occurrence">
                                <div class="column">
                                    <div class="q-pa-sm column q-col-gutter-xs">
                                        <div class="row justify-start">
                                            <div>
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="setQueryPopupDisplay(true);" icon="search" label="Search" />
                                            </div>
                                        </div>
                                        <div v-if="recordDataArr.length > 0" class="row justify-between q-col-gutter-sm">
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
                                        <div class="fit">
                                            <q-table flat bordered class="spatial-record-table" :rows="recordDataArr" row-key="occid" v-model:pagination="pagination" separator="cell" selection="multiple" @request="changeRecordPage" :rows-per-page-options="[0]" wrap-cells dense>
                                                <template v-slot:top="scope">
                                                    <div class="full-width row justify-end">
                                                        <div class="self-center text-bold q-mr-xs">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>

                                                        <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage"></q-btn>

                                                        <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage"></q-btn>

                                                        <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage"></q-btn>

                                                        <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage"></q-btn>
                                                    </div>
                                                </template>
                                                <template v-slot:header="props"></template>
                                                <template v-slot:body="props">
                                                    <q-tr v-if="recordDataArr.length > 0" :props="props" no-hover>
                                                        <q-td class="full-width">
                                                            <div class="full-width column">
                                                                <div class="q-mb-xs row justify-between">
                                                                    <div class="text-bold">
                                                                        {{ props.row.collectionname + ' ' + ((props.row.institutioncode || props.row.collectioncode) ? '(' : '') + (props.row.institutioncode ? props.row.institutioncode : '') + ((props.row.collectionname && props.row.collectionname) ? ':' : '') + (props.row.collectioncode ? props.row.collectioncode : '') + ((props.row.collectionname || props.row.collectionname) ? ')' : '') }}
                                                                    </div>
                                                                    <div class="row q-gutter-xs">
                                                                        <template v-if="isAdmin || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollAdmin') && currentUserPermissions['CollAdmin'].includes(Number(props.row.collid))) || (currentUserPermissions && currentUserPermissions.hasOwnProperty('CollEditor') && currentUserPermissions['CollEditor'].includes(Number(props.row.collid)))">
                                                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" :href="(clientRoot + '/collections/editor/occurrenceeditor.php?occid=' + props.row.occid + '&collid=' + props.row.collid)" target="_blank" icon="fas fa-edit" dense>
                                                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                                    Edit occurrence record
                                                                                </q-tooltip>
                                                                            </q-btn>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="full-width row justify-between">
                                                                    <div class="col-10 row q-col-gutter-md">
                                                                        <div class="col-1 row justify-center items-center">
                                                                            <div>
                                                                                <template v-if="props.row.icon">
                                                                                    <q-img :src="props.row.icon" class="occurrence-search-list-coll-icon" fit="contain"></q-img>
                                                                                </template>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-10 column text-body1 wrap">
                                                                            <div v-if="props.row.sciname">
                                                                                <template v-if="Number(props.row.tid) > 0">
                                                                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.tid)" target="_blank">
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
                                                                                <span class="cursor-pointer text-body1 text-bold" @click="openRecordInfoWindow(props.row.occid);">Full Record Details</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <div class="fit">
                                                                            <template v-if="props.row.img">
                                                                                <q-img :src="props.row.img" class="occurrence-search-image-thumbnail" fit="contain"></q-img>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </q-td>
                                                    </q-tr>
                                                </template>
                                                <template v-slot:pagination="scope">
                                                    <div class="full-width row justify-end">
                                                        <div class="self-center text-body2 text-bold q-mr-xs">Records {{ scope.pagination.firstRowNumber }} - {{ scope.pagination.lastRowNumber }} of {{ scope.pagination.rowsNumber }}</div>

                                                        <q-btn v-if="scope.pagesNumber > 2 && !scope.isFirstPage" icon="first_page" color="grey-8" round dense flat @click="scope.firstPage"></q-btn>

                                                        <q-btn v-if="!scope.isFirstPage" icon="chevron_left" color="grey-8" round dense flat @click="scope.prevPage"></q-btn>

                                                        <q-btn v-if="!scope.isLastPage" icon="chevron_right" color="grey-8" round dense flat @click="scope.nextPage"></q-btn>

                                                        <q-btn v-if="scope.pagesNumber > 2 && !scope.isLastPage" icon="last_page" color="grey-8" round dense flat @click="scope.lastPage"></q-btn>
                                                    </div>
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
                                <div v-if="taxaCnt > 0" class="column">
                                    <div class="q-pa-sm column q-col-gutter-xs">
                                        <div class="row justify-start">
                                            <div>
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="setQueryPopupDisplay(true);" icon="search" label="Search" />
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
                                    <q-separator ></q-separator>
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
                                                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon.tid)" target="_blank">
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
                <template v-if="recordInfoWindowId">
                    <occurrence-info-window-popup :occurrence-id="recordInfoWindowId" :show-popup="showRecordInfoWindow" @close:popup="closeRecordInfoWindow"></occurrence-info-window-popup>
                </template>
                <template v-if="displayQueryPopup">
                    <search-criteria-popup
                        :show-popup="(displayQueryPopup && !showSpatialPopup)"
                        :show-spatial="true"
                        @open:spatial-popup="openSpatialPopup"
                        @process:search-load-records="loadRecords"
                        @close:popup="setQueryPopupDisplay(false)"
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/keyDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/checklistDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
        <script>
            const searchListDisplayModule = Vue.createApp({
                components: {
                    'checklist-display-button': checklistDisplayButton,
                    'copy-url-button': copyURLButton,
                    'image-display-button': imageDisplayButton,
                    'key-display-button': keyDisplayButton,
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

                    const clientRoot = baseStore.getClientRoot;
                    const currentUserPermissions = Vue.ref(null);
                    const displayQueryPopup = Vue.ref(false);
                    const isAdmin = Vue.computed(() => {
                        return currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('SuperAdmin');
                    });
                    const keyModuleIsActive = baseStore.getKeyModuleIsActive;
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
                    const popupWindowType = Vue.ref(null);
                    const queryId = QUERYID;
                    const recordDataArr = Vue.computed(() => searchStore.getSearchRecordData);
                    const recordInfoWindowId = Vue.ref(null);
                    const searchTermsJson = Vue.computed(() => searchStore.getSearchTermsJson);
                    const searchTermsPageNumber = Vue.computed(() => searchStore.getSearchTermsPageNumber);
                    const showRecordInfoWindow = Vue.ref(false);
                    const showSpatialPopup = Vue.ref(false);
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const stArrJson = STARRJSON;
                    const tab = Vue.ref('occurrence');
                    const taxaCnt = Vue.ref(0);
                    const taxaDataArr = Vue.reactive([]);

                    function changeRecordPage(props) {
                        setTableRecordData(props.pagination.page);
                    }

                    function closeRecordInfoWindow(){
                        recordInfoWindowId.value = null;
                        showRecordInfoWindow.value = false;
                    }

                    function closeSpatialPopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        searchStore.clearSpatialInputValues();
                    }

                    function getTaxaData() {
                        const options = {
                            schema: 'taxa',
                            spatial: 0,
                            output: 'json'
                        };
                        searchStore.processSearch(options, (res) => {
                            processTaxaData(res);
                        });
                    }

                    function loadRecords(){
                        if(searchStore.getSearchTermsValid){
                            taxaCnt.value = 0;
                            taxaDataArr.length = 0;
                            showWorking('Loading...');
                            const options = {
                                schema: 'occurrence',
                                spatial: 0
                            };
                            searchStore.setSearchOccidArr(options, () => {
                                if(Number(searchStore.getSearchRecCnt) > 0){
                                    displayQueryPopup.value = false;
                                    setTableRecordData(pagination.value.page);
                                    getTaxaData();
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

                    function processSpatialData(data) {
                        searchStore.processSpatialPopupData(popupWindowType.value, data);
                    }

                    function processTaxaData(data) {
                        data.forEach((taxon) => {
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
                        taxaCnt.value = data.length;
                    }

                    function setCurrentUserPermissions() {
                        baseStore.getGlobalConfigValue('USER_RIGHTS', (dataStr) => {
                            const data = dataStr ? JSON.parse(dataStr) : null;
                            if(data && Object.keys(data).length > 0){
                                currentUserPermissions.value = data;
                            }
                        });
                    }

                    function setQueryPopupDisplay(val) {
                        displayQueryPopup.value = val;
                    }

                    function setTableRecordData(index) {
                        const options = {
                            schema: 'occurrence',
                            spatial: 0,
                            numRows: lazyLoadCnt,
                            index: (index - 1),
                            output: 'json'
                        };
                        searchStore.setSearchRecordData(options, (retCnt) => {
                            hideWorking();
                            if(retCnt === 0){
                                //showNotification('negative', 'An error occurred while loading the occurrence records.');
                            }
                        });
                        pageNumber.value = Number(index);
                    }

                    Vue.onMounted(() => {
                        setCurrentUserPermissions();
                        if(Number(queryId) === 0 && !stArrJson){
                            displayQueryPopup.value = true;
                        }
                        if(queryId || stArrJson){
                            showWorking('Loading...');
                        }
                        searchStore.initializeSearchStorage(queryId);
                        if(Number(queryId) > 0 || stArrJson){
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
                    });

                    return {
                        clientRoot,
                        currentUserPermissions,
                        displayQueryPopup,
                        isAdmin,
                        keyModuleIsActive,
                        pagination,
                        popupWindowType,
                        recordDataArr,
                        recordInfoWindowId,
                        searchTermsJson,
                        showRecordInfoWindow,
                        showSpatialPopup,
                        spatialInputValues,
                        tab,
                        taxaCnt,
                        taxaDataArr,
                        changeRecordPage,
                        closeRecordInfoWindow,
                        closeSpatialPopup,
                        loadRecords,
                        openRecordInfoWindow,
                        openSpatialPopup,
                        processSpatialData,
                        setQueryPopupDisplay
                    }
                }
            });
            searchListDisplayModule.use(Quasar, { config: {} });
            searchListDisplayModule.use(Pinia.createPinia());
            searchListDisplayModule.mount('#mainContainer');
        </script>
    </body>
</html>
