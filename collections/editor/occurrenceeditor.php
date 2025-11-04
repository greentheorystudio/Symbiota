<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 600);

$occId = array_key_exists('occid', $_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collId = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$displayMode = array_key_exists('mode', $_REQUEST) ? (int)$_REQUEST['mode'] : 1;
$goToMode = array_key_exists('gotomode', $_REQUEST) ? (int)$_REQUEST['gotomode'] : 0;
$occIndex = array_key_exists('occindex', $_REQUEST) ? (int)$_REQUEST['occindex'] : null;
$ouid = array_key_exists('ouid', $_REQUEST) ? (int)$_REQUEST['ouid'] : 0;
$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$stArrJson = array_key_exists('starr', $_REQUEST) ? $_REQUEST['starr'] : '';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Editor</title>
        <meta name="description" content="Edit an occurrence record">
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/panzoom.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const COLLID = <?php echo $collId; ?>;
            const DISPLAY_MODE = <?php echo $displayMode; ?>;
            const OCCID = <?php echo $occId; ?>;
            const QUERYID = <?php echo $queryId; ?>;
            const STARRJSON = '<?php echo $stArrJson; ?>';
        </script>
    </head>
    <body class="full-window-mode">
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <div id="mainContainer" class="q-mt-lg">
            <div class="row justify-center">
                <div ref="moduleContainerRef" class="editor-inner-container rounded-borders shadow-5 q-pa-md column q-gutter-y-sm self-center bg-white">
                    <div class="row justify-start">
                        <div><a :href="clientRoot + '/index.php'" tabindex="0">Home</a> &gt;&gt;</div>
                        <template v-if="displayMode === 4">
                            <a :href="clientRoot + '/collections/management/crowdsource/index.php'" tabindex="0">Crowd Sourcing Central</a> &gt;&gt;
                        </template>
                        <template v-else-if="isEditor">
                            <div><a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collId)" tabindex="0">Collection Control Panel</a> &gt;&gt;</div>
                        </template>
                        <span class="text-bold">Occurrence Editor</span>
                    </div>
                    <div class="row justify-between">
                        <div class="row justify-start q-gutter-sm self-center">
                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayQueryPopup = true" icon="search" label="Search" aria-label="Open Search Window" tabindex="0"></q-btn>
                            <template v-if="recordCount > 1">
                                <table-display-button></table-display-button>
                                <list-display-button></list-display-button>
                                <spatial-display-button></spatial-display-button>
                                <image-display-button></image-display-button>
                            </template>
                        </div>
                        <div class="row justify-end self-center">
                            <div class="self-center text-bold q-mr-xs">Record {{ currentRecordIndex }} of {{ recordCount }}</div>
                            <q-btn v-if="recordCount > 1 && currentRecordIndex > 1" icon="first_page" color="grey-8" round dense flat @click="goToFirstRecord" aria-label="Go to first record" tabindex="0"></q-btn>
                            <q-btn v-if="recordCount > 1 && currentRecordIndex > 1" icon="chevron_left" color="grey-8" round dense flat @click="goToPreviousRecord" aria-label="Go to previous record" tabindex="0"></q-btn>
                            <q-btn v-if="recordCount > 1 && currentRecordIndex < recordCount && occId > 0" icon="chevron_right" color="grey-8" round dense flat @click="goToNextRecord" aria-label="Go to next record" tabindex="0"></q-btn>
                            <q-btn v-if="recordCount > 1 && currentRecordIndex < recordCount && occId > 0" icon="last_page" color="grey-8" round dense flat @click="goToLastRecord" aria-label="Go to last record" tabindex="0"></q-btn>
                            <q-btn v-if="occurrenceEntryFormat !== 'benthic' && occId > 0" icon="add_circle" color="grey-8" round dense flat @click="goToNewRecord" aria-label="Go to new record" tabindex="0">
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Create new occurrence record
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                    <div class="row justify-between">
                        <div class="row justify-start text-h6 text-weight-bold">
                            <template v-if="collInfo">
                                <template v-if="collInfo.collectionname">{{ collInfo.collectionname }}</template>
                                <template v-if="collInfo.institutioncode || collInfo.collectioncode"> (<template v-if="collInfo.institutioncode">{{ collInfo.institutioncode }}</template><template v-if="collInfo.institutioncode && collInfo.collectioncode">-</template><template v-if="collInfo.collectioncode">{{ collInfo.collectioncode }}</template>)</template>
                            </template>
                        </div>
                        <div class="row justify-end q-gutter-sm self-center">
                            <template v-if="Number(occId) === 0">
                                <div>
                                    <occurrence-entry-format-selector :selected-format="occurrenceEntryFormat" @change-occurrence-entry-format="changeOccurrenceEntryFormat"></occurrence-entry-format-selector>
                                </div>
                            </template>
                            <template v-if="recordCount > 1">
                                <div class="self-center">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayBatchUpdatePopup = true" icon="find_replace" dense aria-label="Open Batch Update Tool" :disabled="!searchTermsValid" tabindex="0">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Open Batch Update Tool
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </template>
                            <template v-if="(occurrenceEntryFormat === 'specimen' || occurrenceEntryFormat === 'skeletal') && imageCount > 0">
                                <div class="self-center">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayImageTranscriberPopup = true" icon="image_search" dense aria-label="Display image transcription window" tabindex="0">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Display image transcription window
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </template>
                        </div>
                    </div>
                    <template v-if="Number(occId) > 0">
                        <q-card flat bordered class="q-mt-sm black-border">
                            <q-card-section class="q-pa-none">
                                <occurrence-editor-tab-module></occurrence-editor-tab-module>
                            </q-card-section>
                        </q-card>
                    </template>
                    <template v-else-if="Number(occId) === 0">
                        <q-card flat>
                            <q-card-section class="q-pa-sm">
                                <template v-if="occurrenceEntryFormat === 'observation'">
                                    <occurrence-entry-observation-form-module></occurrence-entry-observation-form-module>
                                </template>
                                <template v-else-if="occurrenceEntryFormat === 'skeletal'">
                                    <occurrence-entry-skeletal-form-module></occurrence-entry-skeletal-form-module>
                                </template>
                                <template v-else>
                                    <occurrence-editor-occurrence-data-module></occurrence-editor-occurrence-data-module>
                                </template>
                            </q-card-section>
                        </q-card>
                    </template>
                </div>
            </div>
            <template v-if="displayBatchUpdatePopup">
                <occurrence-editor-batch-update-popup :show-popup="displayBatchUpdatePopup" @complete:batch-update="processBatchUpdate" @close:popup="displayBatchUpdatePopup = false"></occurrence-editor-batch-update-popup>
            </template>
            <template v-if="displayImageTranscriberPopup">
                <occurrence-editor-image-transcriber-popup :show-popup="displayImageTranscriberPopup" @close:popup="displayImageTranscriberPopup = false"></occurrence-editor-image-transcriber-popup>
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
            <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/colorPicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/copyURLButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/collectionCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/listDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDownloadOptionsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDataDownloader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/tableDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/spatialDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/timeInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleCountryAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleStateProvinceAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleCountyAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/imageTagSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/filePickerInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceSelectorInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceLinkageToolPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceLocationLinkageToolPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/mediaFileUploadInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceEntryFormatSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceEntryFollowUpActionSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceAssociatedTaxaInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceVerbatimCoordinatesInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceVerbatimElevationInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceFootprintWktInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/locationNameCodeAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/geoLocatePopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>

        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/determinationRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/geneticLinkRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRow.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRowGroup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCollectingEventListPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCollectionListPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCollectingEventBenthicTaxaListPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCollectingEventBenthicTaxaEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoTabModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/locationFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/collectingEventFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormLocationElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceLocationEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceDeterminationEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceGeneticRecordLinkageEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorChecklistVoucherModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorGeneticLinkModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCollectingEventTransferPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorEventLocationTransferPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorOccurrenceDataControls.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCollectingEventEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorLocationModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormCollectingEventElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorCollectingEventModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormLatestIdentificationElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormMiscElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormCurationElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormIdentifierElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorRecordFooterElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEntryObservationFormModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEntrySkeletalFormModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorOccurrenceDataModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorDeterminationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorMediaTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorResourcesTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorAdminTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorTabModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorBatchUpdatePopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorImageTranscriberPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const occurrenceEditorControllerModule = Vue.createApp({
                components: {
                    'confirmation-popup': confirmationPopup,
                    'image-display-button': imageDisplayButton,
                    'list-display-button': listDisplayButton,
                    'occurrence-editor-batch-update-popup': occurrenceEditorBatchUpdatePopup,
                    'occurrence-editor-image-transcriber-popup': occurrenceEditorImageTranscriberPopup,
                    'occurrence-editor-occurrence-data-module': occurrenceEditorOccurrenceDataModule,
                    'occurrence-editor-tab-module': occurrenceEditorTabModule,
                    'occurrence-entry-format-selector': occurrenceEntryFormatSelector,
                    'occurrence-entry-observation-form-module': occurrenceEntryObservationFormModule,
                    'occurrence-entry-skeletal-form-module': occurrenceEntrySkeletalFormModule,
                    'search-criteria-popup': searchCriteriaPopup,
                    'spatial-analysis-popup': spatialAnalysisPopup,
                    'spatial-display-button': spatialDisplayButton,
                    'table-display-button': tableDisplayButton
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const occurrenceStore = useOccurrenceStore();
                    const searchStore = useSearchStore();

                    const clientRoot = baseStore.getClientRoot;
                    const collId = Vue.computed(() => occurrenceStore.getCollId);
                    const collInfo = Vue.computed(() => occurrenceStore.getCollectionData);
                    const confirmationPopupRef = Vue.ref(null);
                    const containerWidth = Vue.ref(0);
                    const currentRecordIndex = Vue.computed(() => searchStore.getCurrentOccIdIndex);
                    const displayBatchUpdatePopup = Vue.ref(false);
                    const displayImageTranscriberPopup = Vue.ref(false);
                    const displayMode = Vue.computed(() => occurrenceStore.getDisplayMode);
                    const displayQueryPopup = Vue.ref(false);
                    const imageCount = Vue.computed(() => occurrenceStore.getImageCount);
                    const initialCollId = COLLID;
                    const initialDisplayMode = DISPLAY_MODE;
                    const initialOccId = OCCID;
                    const isEditor = Vue.computed(() => occurrenceStore.getIsEditor);
                    const moduleContainerRef = Vue.ref(null);
                    const occId = Vue.computed(() => occurrenceStore.getOccId);
                    const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
                    const occurrenceFields = Vue.computed(() => occurrenceStore.getOccurrenceFields);
                    const occurrenceFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceFieldDefinitions);
                    const popupWindowType = Vue.ref(null);
                    const queryId = QUERYID;
                    const searchRecordCount = Vue.computed(() => searchStore.getSearchRecordCount);
                    const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
                    const searchTermsValid = Vue.computed(() => searchStore.getSearchTermsValid);
                    const recordCount = Vue.computed(() => {
                        return Number(occId.value) === 0 ? searchRecordCount.value + 1 : searchRecordCount.value;
                    });
                    const showSpatialPopup = Vue.ref(false);
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const stArrJson = STARRJSON;

                    Vue.watch(collId, () => {
                        searchStore.updateSearchTerms('collid', collId.value);
                    });

                    Vue.watch(occId, () => {
                        searchStore.setCurrentOccId(occId.value);
                    });

                    function changeOccurrenceEntryFormat(value) {
                        occurrenceStore.setOccurrenceEntryFormat(value);
                    }

                    function closeSpatialPopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        searchStore.clearSpatialInputValues();
                    }

                    function goToFirstRecord() {
                        occurrenceStore.setCurrentOccurrenceRecord(searchStore.getFirstOccidInOccidArr);
                    }

                    function goToLastRecord() {
                        occurrenceStore.setCurrentOccurrenceRecord(searchStore.getLastOccidInOccidArr);
                    }

                    function goToNextRecord() {
                        occurrenceStore.setCurrentOccurrenceRecord(searchStore.getNextOccidInOccidArr);
                    }

                    function goToNewRecord() {
                        occurrenceStore.goToNewOccurrenceRecord();
                    }

                    function goToPreviousRecord() {
                        occurrenceStore.setCurrentOccurrenceRecord(searchStore.getPreviousOccidInOccidArr);
                    }

                    function loadRecords() {
                        if(searchTermsValid.value || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                            searchStore.clearQueryOccidArr();
                            showWorking('Loading...');
                            const options = {
                                schema: 'occurrence',
                                display: 'table',
                                spatial: 0
                            };
                            searchStore.setSearchOccidArr(options, () => {
                                if(Number(searchStore.getSearchRecordCount) > 0){
                                    displayQueryPopup.value = false;
                                    if(Number(occId.value) === 0 || currentRecordIndex.value < 0){
                                        goToFirstRecord();
                                    }
                                }
                                else{
                                    showNotification('negative','There were no records matching your query.');
                                }
                                hideWorking();
                            });
                        }
                        else{
                            showNotification('negative','Please enter search criteria.');
                        }
                    }

                    function openSpatialPopup(type) {
                        searchStore.setSpatialInputValues();
                        popupWindowType.value = type;
                        showSpatialPopup.value = true;
                    }

                    function processBatchUpdate() {
                        occurrenceStore.setCurrentOccurrenceRecord(occId.value);
                        loadRecords();
                    }

                    function processResetCriteria() {
                        occurrenceStore.setCurrentOccurrenceRecord(0);
                    }

                    function processSpatialData(data) {
                        searchStore.processSpatialPopupData(popupWindowType.value, data);
                    }

                    function setContainerWidth() {
                        containerWidth.value = moduleContainerRef.value.clientWidth;
                    }

                    function validateCoordinates() {
                        occurrenceStore.getCoordinateVerificationData((data) => {
                            if(data.address){
                                if(!data.valid){
                                    let alertText = 'Are those coordinates accurate? They currently map to: ' + data.country + ', ' + data.state;
                                    if(data.county) {
                                        alertText += ', ' + data.county;
                                    }
                                    alertText += ', which differs from what you have entered.';
                                    confirmationPopupRef.value.openPopup(alertText);
                                }
                            }
                            else{
                                showNotification('negative', 'Unable to identify a country from the coordinates entered. Are they accurate?');
                            }
                        });
                    }

                    Vue.provide('containerWidth', containerWidth);
                    Vue.provide('occurrenceFields', occurrenceFields);
                    Vue.provide('occurrenceFieldDefinitions', occurrenceFieldDefinitions);
                    Vue.provide('validateCoordinates', validateCoordinates);

                    Vue.onMounted(() => {
                        setContainerWidth();
                        window.addEventListener('resize', setContainerWidth);
                        occurrenceStore.setOccurrenceFields();
                        if(Number(initialCollId) > 0 || Number(initialOccId) > 0){
                            if(Number(initialCollId) > 0 && Number(initialOccId) === 0){
                                occurrenceStore.setCollection(initialCollId);
                            }
                            if(Number(initialDisplayMode) > 1){
                                occurrenceStore.setDisplayMode(initialDisplayMode);
                            }
                            occurrenceStore.setCurrentOccurrenceRecord(initialOccId);
                            searchStore.initializeSearchStorage(queryId);
                            if(Number(queryId) > 0 || stArrJson){
                                if(stArrJson){
                                    searchStore.loadSearchTermsArrFromJson(stArrJson.replaceAll('%squot;', "'"));
                                }
                                if(searchTermsValid.value || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                                    loadRecords();
                                }
                            }
                        }
                        else{
                            window.location.href = clientRoot + '/index.php';
                        }
                    });

                    return {
                        clientRoot,
                        collId,
                        collInfo,
                        confirmationPopupRef,
                        currentRecordIndex,
                        displayBatchUpdatePopup,
                        displayImageTranscriberPopup,
                        displayMode,
                        displayQueryPopup,
                        imageCount,
                        isEditor,
                        moduleContainerRef,
                        occId,
                        occurrenceEntryFormat,
                        popupWindowType,
                        recordCount,
                        searchTermsValid,
                        showSpatialPopup,
                        spatialInputValues,
                        changeOccurrenceEntryFormat,
                        closeSpatialPopup,
                        goToFirstRecord,
                        goToLastRecord,
                        goToNextRecord,
                        goToNewRecord,
                        goToPreviousRecord,
                        loadRecords,
                        openSpatialPopup,
                        processBatchUpdate,
                        processResetCriteria,
                        processSpatialData
                    }
                }
            });
            occurrenceEditorControllerModule.use(Quasar, { config: {} });
            occurrenceEditorControllerModule.use(Pinia.createPinia());
            occurrenceEditorControllerModule.mount('#mainContainer');
        </script>
    </body>
</html>
