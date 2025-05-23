<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid', $_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$pid = array_key_exists('pid', $_REQUEST) ? (int)$_REQUEST['pid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklist Details</title>
        <meta name="description" content="Individual checklist details in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
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
            const CLID = <?php echo $clid; ?>;
            const QUERYID = <?php echo $queryId; ?>;
            const PID = <?php echo $pid; ?>;
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')">Home</a> &gt;&gt;
                <template v-if="!temporaryChecklist">
                    <template v-if="Number(pId) > 0">
                        <a :href="(clientRoot + '/projects/index.php?pid=' + pId)">{{ projectName }}</a> &gt;&gt;
                    </template>
                    <template v-else-if="Number(clId) > 0">
                        <a :href="(clientRoot + '/checklists/index.php')">Checklists</a> &gt;&gt;
                    </template>
                    <template v-if="Number(clId) > 0">
                        <span class="text-bold">{{ checklistName }}</span>
                    </template>
                    <template v-else>
                        <span class="text-bold">Project Checklist</span>
                    </template>
                </template>
                <template v-else>
                    <span class="text-bold">Dynamic Checklist</span>
                </template>
            </div>
            <div class="q-pa-md column">
                <template v-if="Number(clId) > 0 || Number(pId) > 0">
                    <div class="q-mb-md full-width row justify-between q-gutter-sm items-center">
                        <div class="row q-gutter-md">
                            <div>
                                <h1>{{ checklistName }}</h1>
                            </div>
                            <div class="row q-gutter-sm">
                                <div v-if="keyModuleIsActive">
                                    <q-btn text-color="black" size="sm" :href="(clientRoot + '/ident/key.php?clid=' + clId + '&pid=' + pId)" icon="fas fa-key" dense unelevated :ripple="false">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Open Interactive Key
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                                <div>
                                    <q-btn text-color="black" size="sm" :href="(clientRoot + '/games/flashcards.php?clid=' + clId)" icon="fas fa-gamepad" dense unelevated :ripple="false">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Open Flashcard Game
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-end q-gutter-sm items-center">
                            <template v-if="Number(clId) > 0 && validUser">
                                <template v-if="temporaryChecklist">
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="saveTemporaryChecklist();" icon="fas fa-save" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Save Checklist
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </template>
                                <template v-else-if="isEditor">
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" :href="(clientRoot + '/checklists/checklistadmin.php?clid=' + clId + '&pid=' + pId)" icon="fas fa-cog" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Open Checklist Administration
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" :href="(clientRoot + '/checklists/voucheradmin.php?clid=' + clId + '&pid=' + pId)" icon="fas fa-link" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Open Voucher Administration
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                    <div>
                                        <template v-if="taxaEditingActive">
                                            <q-btn color="grey-4" text-color="red" class="black-border" size="sm" @click="taxaEditingActive = !taxaEditingActive" icon="fas fa-times-circle" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Toggle Taxa Editing Off
                                                </q-tooltip>
                                            </q-btn>
                                        </template>
                                        <template v-else>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="taxaEditingActive = !taxaEditingActive" icon="fas fa-clipboard-list" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Toggle Taxa Editing On
                                                </q-tooltip>
                                            </q-btn>
                                        </template>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                    <div v-if="checklistData.hasOwnProperty('authors') && checklistData['authors']" class="text-body1">
                        <span class="text-bold">Authors: </span>{{ checklistData['authors'] }}
                    </div>
                    <div v-if="checklistData.hasOwnProperty('publication') && checklistData['publication']" class="text-body1">
                        <span class="text-bold">Publication: </span>{{ checklistData['publication'] }}
                    </div>
                    <template v-if="checklistLocalityText || (checklistData.hasOwnProperty('abstract') && checklistData['abstract']) || (checklistData.hasOwnProperty('notes') && checklistData['notes'])">
                        <template v-if="showMoreDescription">
                            <div v-if="checklistLocalityText" class="text-body1">
                                <span class="text-bold">Locality: </span>{{ checklistLocalityText }}
                            </div>
                            <div v-if="checklistData.hasOwnProperty('abstract') && checklistData['abstract']" class="text-body1">
                                <span class="text-bold">Abstract: </span>{{ checklistData['abstract'] }}
                            </div>
                            <div v-if="checklistData.hasOwnProperty('notes') && checklistData['notes']" class="text-body1">
                                <span class="text-bold">Notes: </span>{{ checklistData['notes'] }}
                            </div>
                            <div class="text-body1 text-bold text-blue cursor-pointer">
                                <a @click="showMoreDescription = false" class="text-primary">Less Details</a>
                            </div>
                        </template>
                        <template v-else>
                            <div class="text-body1 text-bold text-blue cursor-pointer">
                                <a @click="showMoreDescription = true" class="text-primary">More Details</a>
                            </div>
                        </template>
                    </template>
                    <div class="q-mb-lg full-width">
                        <q-separator ></q-separator>
                    </div>
                    <div class="full-width row q-gutter-sm">
                        <div class="col-4 column q-col-gutter-sm">
                            <div class="full-width">
                                <q-card flat bordered>
                                    <q-card-section class="column q-gutter-xs">
                                        <div class="full-width">
                                            <selector-input-element label="Sort by" :options="sortByOptions" :value="selectedSortByOption" @update:value="processSortByChange"></selector-input-element>
                                        </div>
                                        <div class="full-width">
                                            <checkbox-input-element label="Display Common Names" :value="displayCommonNamesVal" @update:value="processDisplayCommonNameChange"></checkbox-input-element>
                                        </div>
                                        <div class="full-width">
                                            <checkbox-input-element label="Display Images" :value="displayImagesVal" @update:value="processDisplayImagesChange"></checkbox-input-element>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </div>
                        </div>
                        <div class="col-8 column q-col-gutter-sm q-pl-lg">
                            <div class="column">
                                <div class="full-width row justify-end text-h5 text-bold">
                                    <a :href="(clientRoot + '/checklists/checklist.php?clid=' + clId + '&proj=' + pId)">{{ checklistName }}</a>
                                </div>
                                <div class="full-width row justify-end text-body1">
                                    Taxa Count: {{ taxaCount }}
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="column">
                        <div class="q-pa-sm column q-col-gutter-xs">
                            <div class="row justify-start">
                                <div>
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="setQueryPopupDisplay(true);" icon="search" label="Search" />
                                </div>
                            </div>
                        </div>
                        <q-separator ></q-separator>
                        <div class="q-pa-md row justify-center text-h6 text-bold">
                            There are no taxa to display. Click the Search button to enter search criteria to build the taxa checklist.
                        </div>
                    </div>
                </template>
            </div>
            <template v-if="displayQueryPopup">
                <search-criteria-popup
                    :show-popup="(displayQueryPopup && !showSpatialPopup)"
                    popup-type="checklist"
                    :show-spatial="true"
                    @open:spatial-popup="openSpatialPopup"
                    @process:build-checklist="buildChecklist"
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
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
            const checklistModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'search-criteria-popup': searchCriteriaPopup,
                    'selector-input-element': selectorInputElement,
                    'spatial-analysis-popup': spatialAnalysisPopup
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const checklistStore = useChecklistStore();
                    const projectStore = useProjectStore();
                    const searchStore = useSearchStore();

                    const checklistData = Vue.computed(() => checklistStore.getChecklistData);
                    const checklistLocalityText = Vue.computed(() => {
                        let returnVal = null;
                        if((checklistData.value.hasOwnProperty('locality') && checklistData.value['locality']) || (checklistData.value.hasOwnProperty('latcentroid') && checklistData.value['latcentroid'])){
                            if(checklistData.value.hasOwnProperty('locality') && checklistData.value['locality']){
                                returnVal = checklistData.value['locality'];
                            }
                            if(checklistData.value.hasOwnProperty('latcentroid') && checklistData.value['latcentroid']){
                                returnVal = (returnVal ? (returnVal + ' (') : '') + checklistData.value['latcentroid'] + ', ' + checklistData.value['longcentroid'] + (returnVal ? ')' : '');
                            }
                        }
                        return returnVal;
                    });
                    const checklistName = Vue.computed(() => {
                        let returnVal = 'Dynamic Checklist';
                        if(!temporaryChecklist.value && checklistData.value.hasOwnProperty('name') && checklistData.value['name']){
                            returnVal = checklistData.value['name'];
                        }
                        return returnVal;
                    });
                    const clId = Vue.ref(CLID);
                    const clidArr = Vue.computed(() => {
                        let returnArr = [];
                        if(checklistData.value.hasOwnProperty('clidArr') && checklistData.value['clidArr'].length > 0){
                            returnArr = checklistData.value['clidArr'].slice();
                        }
                        else if(projectData.value.hasOwnProperty('clidArr') && projectData.value['clidArr'].length > 0){
                            returnArr = projectData.value['clidArr'].slice();
                        }
                        return returnArr;
                    });
                    const clientRoot = baseStore.getClientRoot;
                    const commonNameData = Vue.ref({});
                    const displayCommonNamesVal = Vue.ref(false);
                    const displayImagesVal = Vue.ref(false);
                    const displayQueryPopup = Vue.ref(false);
                    const imageData = Vue.ref({});
                    const isEditor = Vue.ref(false);
                    const keyModuleIsActive = baseStore.getKeyModuleIsActive;
                    const pId = Vue.ref(PID);
                    const popupWindowType = Vue.ref(null);
                    const projectData = Vue.computed(() => projectStore.getProjectData);
                    const projectName = Vue.computed(() => {
                        return projectData.value.hasOwnProperty('projname') ? projectData.value['projname'] : '';
                    });
                    const queryId = QUERYID;
                    const selectedSortByOption = Vue.ref('family');
                    const showMoreDescription = Vue.ref(false);
                    const showSpatialPopup = Vue.ref(false);
                    const sortByOptions = Vue.ref([
                        {value: 'family', label: 'Family/Scientific Name'},
                        {value: 'sciname', label: 'Scientific Name'}
                    ]);
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const taxaCount = Vue.computed(() => {
                        return 0;
                    });
                    const taxaDataArr = Vue.computed(() => checklistStore.getChecklistTaxaArr);
                    const taxaDisplayDataArr = Vue.computed(() => {
                        const newDataArr = [];
                        if(taxaDataArr.value.length > 0){
                            taxaDataArr.value.forEach(taxon => {
                                if(selectedSortByOption.value === 'family'){
                                    const familyObj = newDataArr.find(family => family['familyName'] === taxon['family']);
                                    if(familyObj){
                                        familyObj['taxa'].push(taxon);
                                    }
                                    else{
                                        const taxaArr = [taxon];
                                        newDataArr.push({
                                            familyName: taxon['family'],
                                            taxa: taxaArr
                                        });
                                    }
                                }
                                else{
                                    newDataArr.push(taxon);
                                }
                            });
                            if(selectedSortByOption.value === 'family'){
                                newDataArr.sort((a, b) => {
                                    return a['familyName'].localeCompare(b['familyName']);
                                });
                                newDataArr.forEach(family => {
                                    family['taxa'].sort((a, b) => {
                                        return a['sciname'].localeCompare(b['sciname']);
                                    });
                                });
                            }
                            else{
                                newDataArr.sort((a, b) => {
                                    return a['sciname'].localeCompare(b['sciname']);
                                });
                            }
                        }
                        return newDataArr.slice();
                    });
                    const taxaEditingActive = Vue.ref(false);
                    const temporaryChecklist = Vue.computed(() => {
                        let returnVal = false;
                        if(checklistData.value.hasOwnProperty('clid') && Number(checklistData.value['clid']) > 0 && checklistData.value['expiration']){
                            returnVal = true;
                        }
                        return returnVal;
                    });
                    const validUser = baseStore.getValidUser;

                    function buildChecklist(){
                        if(searchStore.getSearchTermsValid){
                            showWorking('Loading...');
                            const options = {
                                schema: 'occurrence',
                                spatial: 0
                            };
                            searchStore.getSearchTidArr(options, (tidArr) => {
                                if(tidArr.length > 0){
                                    checklistStore.createTemporaryChecklistFromTidArr(tidArr, (res) => {
                                        hideWorking();
                                        if(Number(res) > 0){
                                            setQueryPopupDisplay(false);
                                            clId.value = Number(res);
                                            setChecklistData();
                                        }
                                        else{
                                            showNotification('negative', 'An error occurred while creating the checklist.');
                                        }
                                    });
                                }
                                else{
                                    hideWorking();
                                    showNotification('negative', 'There were no taxa matching your criteria.');
                                }
                            });
                        }
                        else{
                            showNotification('negative', 'Please enter search criteria.');
                        }
                    }

                    function closeSpatialPopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        searchStore.clearSpatialInputValues();
                    }

                    function openSpatialPopup(type) {
                        searchStore.setSpatialInputValues();
                        popupWindowType.value = type;
                        showSpatialPopup.value = true;
                    }

                    function processDisplayCommonNameChange(value) {
                        displayCommonNamesVal.value = Number(value) === 1;
                    }

                    function processDisplayImagesChange(value) {
                        displayImagesVal.value = Number(value) === 1;
                    }

                    function processSortByChange(value) {
                        selectedSortByOption.value = value;
                    }

                    function processSpatialData(data) {
                        searchStore.processSpatialPopupData(popupWindowType.value, data);
                    }

                    function saveTemporaryChecklist() {
                        console.log(searchStore.getSearchTermsJson);
                        checklistStore.saveTemporaryChecklist(searchStore.getSearchTermsJson, (res) => {
                            if(Number(res) === 1){
                                showNotification('positive','Checklist saved.');
                                setEditor();
                            }
                            else{
                                showNotification('negative', 'An error occurred while saving the checklist.');
                            }
                        });
                    }

                    function setChecklistData() {
                        setEditor();
                        checklistStore.setChecklist(clId.value, (clid) => {
                            console.log(checklistData.value);
                            if(Number(clid) > 0){
                                checklistStore.setChecklistTaxaArr(clidArr.value, false);
                            }
                        });
                    }

                    function setEditor() {
                        if(Number(clId.value) > 0){
                            const formData = new FormData();
                            formData.append('permission', 'ClAdmin');
                            formData.append('key', clId.value.toString());
                            formData.append('action', 'validatePermission');
                            fetch(permissionApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((resData) => {
                                isEditor.value = resData.includes('ClAdmin');
                            });
                        }
                    }

                    function setProjectData() {
                        projectStore.setProject(pId.value, (pid) => {
                            if(Number(clId.value) === 0 && Number(pid) > 0){
                                checklistStore.setChecklistTaxaArr(clidArr.value, false);
                            }
                            else{
                                showNotification('negative', 'An error occurred while setting the project data.');
                            }
                        });
                    }

                    function setQueryPopupDisplay(val) {
                        displayQueryPopup.value = val;
                    }

                    Vue.onMounted(() => {
                        if(Number(clId.value) > 0 || Number(pId.value) > 0){
                            setChecklistData();
                            if(Number(pId.value) > 0){
                                setProjectData();
                            }
                        }
                        else{
                            if(Number(queryId) === 0){
                                displayQueryPopup.value = true;
                            }
                            searchStore.initializeSearchStorage(queryId);
                        }
                    });

                    return {
                        checklistData,
                        checklistLocalityText,
                        checklistName,
                        clId,
                        clientRoot,
                        displayCommonNamesVal,
                        displayImagesVal,
                        displayQueryPopup,
                        isEditor,
                        keyModuleIsActive,
                        pId,
                        popupWindowType,
                        projectData,
                        projectName,
                        selectedSortByOption,
                        showMoreDescription,
                        showSpatialPopup,
                        sortByOptions,
                        spatialInputValues,
                        taxaCount,
                        taxaDisplayDataArr,
                        taxaEditingActive,
                        temporaryChecklist,
                        validUser,
                        buildChecklist,
                        closeSpatialPopup,
                        openSpatialPopup,
                        processDisplayCommonNameChange,
                        processDisplayImagesChange,
                        processSortByChange,
                        processSpatialData,
                        saveTemporaryChecklist,
                        setQueryPopupDisplay
                    }
                }
            });
            checklistModule.use(Quasar, { config: {} });
            checklistModule.use(Pinia.createPinia());
            checklistModule.mount('#mainContainer');
        </script>
    </body>
</html>
