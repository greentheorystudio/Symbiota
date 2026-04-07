<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$occId = array_key_exists('occid', $_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collId = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$displayMode = array_key_exists('mode', $_REQUEST) ? (int)$_REQUEST['mode'] : 1;
$interface = array_key_exists('interface', $_REQUEST) ? $_REQUEST['interface'] : '';
$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$stArrJson = array_key_exists('starr', $_REQUEST) ? $_REQUEST['starr'] : '';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence List Display</title>
        <meta name="description" content="Occurrence navigator for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=10.8.1" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=10.8.1" type="text/javascript"></script>
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
            const DISPLAY_MODE = <?php echo $displayMode; ?>;
            const INTERFACE = '<?php echo ($interface ?: 'list'); ?>';
            const OCCID = <?php echo $occId; ?>;
            const QUERYID = <?php echo $queryId; ?>;
            const STARRJSON = '<?php echo $stArrJson; ?>';
        </script>
    </head>
    <body>
        <a class="screen-reader-only" href="#navContainer" tabindex="0">Skip to main content</a>
        <div id="navContainer" class="fit">
            <div id="containerBlockNode" class="fit">
                <?php
                include(__DIR__ . '/../header.php');
                include_once(__DIR__ . '/../config/footer-includes.php');
                include(__DIR__ . '/../footer.php');
                ?>
            </div>
            <div id="mainContainer">
                <template v-if="displayInterface === 'table'">
                    <table-search-interface
                        :collid="occurrenceEditorInterfaceCollId"
                        :occid="occurrenceEditorInterfaceOccId"
                        @load:records="loadRecords"
                        @open:query-popup="displayQueryPopup = true"
                        @open:record-info-window="openRecordInfoWindow"
                    ></table-search-interface>
                </template>
                <template v-else-if="displayInterface === 'spatial'">
                    <spatial-search-interface @open:query-popup="displayQueryPopup = true" @open:record-info-window="openRecordInfoWindow"></spatial-search-interface>
                </template>
                <template v-else-if="displayInterface === 'occurrence'">
                    <occurrence-editor-interface
                        :collid="occurrenceEditorInterfaceCollId"
                        :display-mode="occurrenceEditorInterfaceDisplayMode"
                        :occid="occurrenceEditorInterfaceOccId"
                        @load:records="loadRecords"
                        @open:query-popup="displayQueryPopup = true"
                    ></occurrence-editor-interface>
                </template>
                <template v-else>
                    <list-search-interface @open:query-popup="displayQueryPopup = true" @open:record-info-window="openRecordInfoWindow"></list-search-interface>
                </template>
            </div>
            <template v-if="recordInfoWindowId">
                <occurrence-info-window-popup
                    :navigator-mode="true"
                    :occurrence-id="recordInfoWindowId"
                    :show-popup="showRecordInfoWindow"
                    @close:popup="closeRecordInfoWindow"
                    @open:occurrence-editor-interface="openOccurrenceEditorInterface"
                ></occurrence-info-window-popup>
            </template>
            <template v-if="displayQueryPopup">
                <search-criteria-popup
                    :show-popup="(displayQueryPopup && !showSpatialPopup)"
                    :show-spatial="displayInterface !== 'spatial'"
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/keyDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/checklistDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCollectingEventReplicateTaxaListPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCollectingEventReplicateTaxaEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoTabModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/locationFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/collectingEventFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormLocationElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceLocationEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/determinationEditorModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceCloneRecordModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorRecordFooterElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEntryObservationFormModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEntrySkeletalFormModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorOccurrenceDataModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorDeterminationHistoryBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorDeterminationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorMediaTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorResourcesTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorAdminTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorTabModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorBatchUpdatePopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorImageTranscriberPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/tableColumnTogglePopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/listSearchInterface.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/spatialSearchInterface.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/tableSearchInterface.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorInterface.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const occurrenceNavigatorModule = Vue.createApp({
                components: {
                    'list-search-interface': listSearchInterface,
                    'occurrence-editor-interface': occurrenceEditorInterface,
                    'occurrence-info-window-popup': occurrenceInfoWindowPopup,
                    'search-criteria-popup': searchCriteriaPopup,
                    'spatial-analysis-popup': spatialAnalysisPopup,
                    'spatial-search-interface': spatialSearchInterface,
                    'table-search-interface': tableSearchInterface
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const occurrenceStore = useOccurrenceStore();
                    const searchStore = useSearchStore();

                    const clientRoot = baseStore.getClientRoot;
                    const containerElement = Vue.ref(null);
                    const currentUserPermissions = Vue.ref(null);
                    const displayInterface = Vue.computed(() => searchStore.getDisplayInterface);
                    const displayQueryPopup = Vue.ref(false);
                    const initialCollId = COLLID;
                    const initialDisplayMode = DISPLAY_MODE;
                    const initialInterface = INTERFACE;
                    const initialOccId = OCCID;
                    const isAdmin = Vue.computed(() => {
                        return currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('SuperAdmin');
                    });
                    const loadRecordOptions = Vue.computed(() => {
                        const returnVal = {
                            schema: 'occurrence'
                        };
                        if(displayInterface.value !== 'spatial'){
                            returnVal['sortField'] = searchTermsSortField.value;
                            returnVal['sortDirection'] = searchTermsSortDirection.value;
                        }
                        return returnVal;
                    });
                    const isEditor = Vue.computed(() => occurrenceStore.getIsEditor);
                    const loadRecordsCompleted = Vue.ref(false);
                    const occurrenceEditorInterfaceCollId = Vue.ref(null);
                    const occurrenceEditorInterfaceDisplayMode = Vue.ref(null);
                    const occurrenceEditorInterfaceOccId = Vue.ref(null);
                    const occurrenceEditorModeActive = Vue.computed(() => searchStore.getOccurrenceEditorModeActive);
                    const popupWindowType = Vue.ref(null);
                    const queryId = QUERYID;
                    const recordInfoWindowId = Vue.ref(null);
                    const searchRecordCount = Vue.computed(() => searchStore.getSearchRecordCount);
                    const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
                    const searchTermsSortDirection = Vue.computed(() => searchStore.getSearchTermsRecordSortDirection);
                    const searchTermsSortField = Vue.computed(() => searchStore.getSearchTermsRecordSortField);
                    const showRecordInfoWindow = Vue.ref(false);
                    const showSpatialPopup = Vue.ref(false);
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const stArrJson = STARRJSON;

                    Vue.watch(displayInterface, () => {
                        setInterfaceDisplay();
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

                    function loadRecords(){
                        if(searchStore.getSearchTermsValid || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                            showWorking('Loading...');
                            loadRecordsCompleted.value = false;
                            searchStore.clearQueryOccidArr();
                            searchStore.setSearchOccidArr(loadRecordOptions.value, () => {
                                loadRecordsCompleted.value = true;
                                if(Number(searchRecordCount.value) > 0){
                                    displayQueryPopup.value = false;
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

                    function openOccurrenceEditorInterface(collid, occid) {
                        closeRecordInfoWindow();
                        occurrenceEditorInterfaceCollId.value = 0;
                        occurrenceEditorInterfaceOccId.value = 0;
                        if(Number(collid) > 0){
                            occurrenceEditorInterfaceCollId.value = Number(collid);
                        }
                        if(Number(occid) > 0){
                            occurrenceEditorInterfaceOccId.value = Number(occid);
                        }
                        searchStore.setDisplayInterface('occurrence');
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
                        searchStore.clearQueryOccidArr();
                        loadRecordsCompleted.value = false;
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
                                    if(initialInterface !== 'occurrence'){
                                        loadRecords(true);
                                    }
                                    else{
                                        displayQueryPopup.value = false;
                                        hideWorking();
                                    }
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

                    function setInterfaceDisplay() {
                        const navContainerElement = document.getElementById('navContainer');
                        const mainContainerElement = document.getElementById('mainContainer');
                        document.body.classList.remove('q-pa-md', 'full-window-mode');
                        if(displayInterface.value === 'occurrence' || displayInterface.value === 'table' || displayInterface.value === 'spatial'){
                            document.body.classList.add('full-window-mode');
                        }
                        if(displayInterface.value === 'occurrence'){
                            document.body.classList.add('q-pa-md');
                        }
                        if(displayInterface.value !== 'list'){
                            navContainerElement.prepend(mainContainerElement);
                            containerElement.value.remove();
                        }
                        else{
                            navContainerElement.prepend(containerElement.value);
                            if(document.getElementById('appContainer')){
                                const topNavigationElement = document.getElementById('topNavigation');
                                topNavigationElement.after(mainContainerElement);
                            }
                            else{
                                containerElement.value.insertBefore(mainContainerElement, containerElement.value.children[1]);
                            }
                        }
                    }

                    Vue.provide('currentUserPermissions', currentUserPermissions);
                    Vue.provide('isAdmin', isAdmin);
                    Vue.provide('loadRecordsCompleted', loadRecordsCompleted);
                    Vue.provide('openOccurrenceEditorInterface', openOccurrenceEditorInterface);

                    Vue.onMounted(() => {
                        setCurrentUserPermissions();
                        containerElement.value = document.getElementById('containerBlockNode');
                        if(Number(queryId) === 0 && !stArrJson){
                            displayQueryPopup.value = true;
                        }
                        searchStore.initializeSearchStorage(queryId);
                        if(Number(initialCollId) > 0){
                            occurrenceEditorInterfaceCollId.value = Number(initialCollId);
                        }
                        if(Number(initialDisplayMode) > 0){
                            occurrenceEditorInterfaceDisplayMode.value = Number(initialDisplayMode);
                        }
                        if(Number(initialOccId) > 0){
                            occurrenceEditorInterfaceOccId.value = Number(initialOccId);
                        }
                        searchStore.setDisplayInterface(initialInterface);
                        if(Number(queryId) > 0 || stArrJson){
                            if(stArrJson){
                                searchStore.loadSearchTermsArrFromJson(stArrJson.replaceAll('%squot;', "'"));
                            }
                            if(searchStore.getSearchTermsValid || (searchTerms.value.hasOwnProperty('collid') && Number(searchTerms.value['collid']) > 0)){
                                if(Number(initialCollId) === 0 && Number(searchTerms.value['collid']) > 0){
                                    setCollection(searchTerms.value['collid']);
                                }
                                else{
                                    loadRecords();
                                }
                            }
                        }
                        else if(Number(initialCollId) > 0){
                            setCollection(initialCollId);
                        }
                    });

                    return {
                        clientRoot,
                        currentUserPermissions,
                        displayInterface,
                        displayQueryPopup,
                        isAdmin,
                        occurrenceEditorInterfaceCollId,
                        occurrenceEditorInterfaceDisplayMode,
                        occurrenceEditorInterfaceOccId,
                        popupWindowType,
                        recordInfoWindowId,
                        showRecordInfoWindow,
                        showSpatialPopup,
                        spatialInputValues,
                        closeRecordInfoWindow,
                        closeSpatialPopup,
                        loadRecords,
                        openOccurrenceEditorInterface,
                        openRecordInfoWindow,
                        openSpatialPopup,
                        processResetCriteria,
                        processSpatialData
                    }
                }
            });
            occurrenceNavigatorModule.use(Quasar, { config: {} });
            occurrenceNavigatorModule.use(Pinia.createPinia());
            occurrenceNavigatorModule.mount('#navContainer');
        </script>
    </body>
</html>
