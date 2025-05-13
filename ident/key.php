<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid',$_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$pid = array_key_exists('pid',$_REQUEST) ? (int)$_REQUEST['pid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Interactive Key</title>
        <meta name="description" content="Interactive key for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
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
                <template v-if="Number(clId) > 0">
                    <a :href="(clientRoot + '/checklists/checklist.php?cl=' + clId + '&proj=' + pId)">Checklist: {{ checklistName }}</a> &gt;&gt;
                    <span class="text-bold">Key: {{ checklistName }}</span>
                </template>
                <template v-else-if="Number(pId) > 0">
                    <a :href="(clientRoot + '/projects/index.php?pid=' + pId)">Project Checklists</a> &gt;&gt;
                    <span class="text-bold">Key: {{ projectName }} Project</span>
                </template>
                <template v-else>
                    <span class="text-bold">Dynamic Key</span>
                </template>
            </div>
            <div class="q-pa-md">
                <template v-if="Number(clId) > 0 || Number(pId) > 0">
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
                            <div>
                                <q-separator ></q-separator>
                            </div>
                            <template v-for="heading in keyDataArr">
                                <template v-if="activeChidArr.includes(Number(heading.chid))">
                                    <div class="full-width">
                                        <q-card flat bordered>
                                            <q-card-section class="column q-gutter-sm">
                                                <div class="text-h6 text-bold">
                                                    {{ heading.headingname }}
                                                </div>
                                                <template v-for="character in heading['characterArr']">
                                                    <template v-if="activeCidArr.includes(Number(character.cid))">
                                                        <div class="full-width column q-gutter-xs">
                                                            <div v-if="character.charactername !== heading.headingname" class="text-body1 text-bold">
                                                                {{ character.charactername }}
                                                            </div>
                                                            <template v-for="state in character['stateArr']">
                                                                <div class="full-width">
                                                                    <checkbox-input-element :label="state.characterstatename" :value="selectedCsidArr.includes(Number(state.csid)) ? '1' : '0'" @update:value="(value) => processCharacterStateSelectionChange(state, value)"></checkbox-input-element>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </template>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </template>
                            </template>
                        </div>
                        <div class="col-8 column q-col-gutter-sm q-pl-lg">
                            <div class="column">
                                <div class="full-width row justify-end text-h5 text-bold">
                                    <a :href="(clientRoot + '/checklists/checklist.php?cl=' + clId + '&proj=' + pId)">{{ checklistName }}</a>
                                </div>
                                <div class="full-width row justify-end text-body1">
                                    Taxa Count: {{ taxaCount }}
                                </div>
                            </div>
                            <template v-if="selectedSortByOption === 'family'">
                                <template v-if="displayImagesVal">

                                </template>
                                <template v-else>
                                    <template v-for="family in taxaDisplayDataArr">
                                        <template v-if="activeFamilyArr.includes(family['familyName'])">
                                            <div class="full-width column q-gutter-xs">
                                                <div class="text-body1 text-bold">
                                                    {{ family['familyName'] }}
                                                </div>
                                                <template v-for="taxon in family['taxa']">
                                                    <template v-if="activeTidArr.includes(taxon['tid'])">
                                                        <div class="full-width">
                                                            <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon['tid'])" target="_blank">{{ taxon['sciname'] }}</a>
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>
                                    </template>
                                </template>
                            </template>
                            <template v-else>
                                <template v-if="displayImagesVal">

                                </template>
                                <template v-else>
                                    <template v-for="taxon in taxaDisplayDataArr">
                                        <template v-if="activeTidArr.includes(taxon['tid'])">
                                            <div class="full-width">
                                                <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon['tid'])" target="_blank">{{ taxon['sciname'] }}</a>
                                            </div>
                                        </template>
                                    </template>
                                </template>
                            </template>
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
                            There are no taxa to display. Click the Search button to enter search criteria to build the taxa checklist for the key.
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
            const keyIdentificationModule = Vue.createApp({
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
                    const searchStore = useSearchStore();

                    const activeChidArr = Vue.computed(() => {
                        const valArr = [];
                        keyDataArr.value.forEach(heading => {
                            if(!heading['characterArr'].every((character) => !activeCidArr.value.includes(Number(character['cid'])))){
                                valArr.push(Number(heading['chid']));
                            }
                        });
                        return valArr;
                    });
                    const activeCidArr = Vue.ref([]);
                    const activeFamilyArr = Vue.ref([]);
                    const activeTidArr = Vue.ref([]);
                    const characterDependencyDataArr = Vue.ref([]);
                    const checklistData = Vue.ref({});
                    const checklistName = Vue.computed(() => {
                        return checklistData.value.hasOwnProperty('name') ? checklistData.value['name'] : '';
                    });
                    const clId = Vue.ref(CLID);
                    const clidArr = Vue.ref([]);
                    const clientRoot = baseStore.getClientRoot;
                    const commonNameData = Vue.ref({});
                    const csidArr = Vue.ref([]);
                    const displayCommonNamesVal = Vue.ref(false);
                    const displayImagesVal = Vue.ref(false);
                    const displayQueryPopup = Vue.ref(false);
                    const imageData = Vue.ref({});
                    const keyDataArr = Vue.ref([]);
                    const languageArr = [];
                    const pId = Vue.ref(PID);
                    const popupWindowType = Vue.ref(null);
                    const projectData = Vue.ref({});
                    const projectName = Vue.computed(() => {
                        return projectData.value.hasOwnProperty('projname') ? projectData.value['projname'] : '';
                    });
                    const queryId = QUERYID;
                    const selectedCidArr = Vue.computed(() => {
                        const valueArr = selectedStateArr.value.length > 0 ? selectedStateArr.value.map(state => Number(state['cid'])) : [];
                        return valueArr.length > 0 ? valueArr.filter((value, index, array) => array.indexOf(value) === index) : [];
                    });
                    const selectedCsidArr = Vue.computed(() => {
                        const valueArr = selectedStateArr.value.length > 0 ? selectedStateArr.value.map(state => Number(state['csid'])) : [];
                        return valueArr.length > 0 ? valueArr.filter((value, index, array) => array.indexOf(value) === index) : [];
                    });
                    const selectedSortByOption = Vue.ref('family');
                    const selectedStateArr = Vue.ref([]);
                    const showSpatialPopup = Vue.ref(false);
                    const sortByOptions = Vue.ref([
                        {value: 'family', label: 'Family/Scientific Name'},
                        {value: 'sciname', label: 'Scientific Name'}
                    ]);
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const taxaCount = Vue.computed(() => {
                        return activeTidArr.value.length;
                    });
                    const taxaDataArr = Vue.ref([]);
                    const taxaDisplayDataArr = Vue.ref([]);
                    const tidArr = Vue.ref([]);

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

                    function processCharacterStateSelectionChange(state, value) {
                        if(Number(value) === 1){
                            selectedStateArr.value.push(state);
                        }
                        else{
                            const index = selectedStateArr.value.indexOf(state);
                            selectedStateArr.value.splice(index, 1);
                        }
                        setActiveCidArr();
                        setActiveTaxa();
                    }

                    function processDisplayCommonNameChange(value) {
                        displayCommonNamesVal.value = Number(value) === 1;
                    }

                    function processDisplayImagesChange(value) {
                        displayImagesVal.value = Number(value) === 1;
                    }

                    function processKeyData(keyData) {
                        keyData['character-headings'].forEach(heading => {
                            const headingCharacterArr = [];
                            const characterArr = keyData['characters'].filter((character) => Number(character['chid']) === Number(heading['chid']));
                            characterArr.forEach(character => {
                                const characterStateArr = [];
                                const stateArr = keyData['character-states'].filter((state) => Number(state['cid']) === Number(character['cid']));
                                stateArr.forEach(state => {
                                    characterStateArr.push(state);
                                });
                                characterStateArr.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                                character['stateArr'] = characterStateArr.slice();
                                characterDependencyDataArr.value.push({
                                    cid: character['cid'],
                                    dependencies: character['dependencies'].slice()
                                });
                                headingCharacterArr.push(character);
                            });
                            headingCharacterArr.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                            heading['characterArr'] = headingCharacterArr.slice();
                            keyDataArr.value.push(heading);
                        });
                        keyDataArr.value.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                        setActiveCidArr();
                    }

                    function processSortByChange(value) {
                        selectedSortByOption.value = value;
                        setTaxaDisplayData();
                    }

                    function processSpatialData(data) {
                        searchStore.processSpatialPopupData(popupWindowType.value, data);
                    }

                    function processTaxaData() {
                        taxaDataArr.value.forEach(taxon => {
                            if(!tidArr.value.includes(taxon['tid'])){
                                tidArr.value.push(taxon['tid']);
                                activeTidArr.value.push(taxon['tid']);
                                if(!activeFamilyArr.value.includes(taxon['family'])){
                                    activeFamilyArr.value.push(taxon['family']);
                                }
                            }
                            if(taxon['keyData'].length > 0){
                                taxon['keyData'].forEach(keyData => {
                                    if(!csidArr.value.includes(keyData['csid'])){
                                        csidArr.value.push(keyData['csid']);
                                    }
                                });
                            }
                        });
                        setTaxaDisplayData();
                        setKeyData();
                    }

                    function setActiveCidArr() {
                        characterDependencyDataArr.value.forEach(character => {
                            if(character['dependencies'].length > 0){
                                let active = false;
                                character['dependencies'].forEach(dep => {
                                    if(!active){
                                        if(Number(dep['csid']) === 0){
                                            if(selectedCidArr.value.includes(Number(dep['cid']))){
                                                active = true;
                                            }
                                        }
                                        else{
                                            if(selectedCsidArr.value.includes(Number(dep['csid']))){
                                                active = true;
                                            }
                                        }
                                    }
                                });
                                if(active && !activeCidArr.value.includes(Number(character['cid']))){
                                    activeCidArr.value.push(Number(character['cid']));
                                }
                                else if(!active){
                                    if(activeCidArr.value.includes(Number(character['cid']))){
                                        const index = activeCidArr.value.indexOf(Number(character['cid']));
                                        activeCidArr.value.splice(index, 1);
                                    }
                                    if(selectedCidArr.value.includes(Number(character['cid']))){
                                        const targetStateArr = selectedStateArr.value.filter((state) => Number(state['cid']) === Number(character['cid']));
                                        targetStateArr.forEach(state => {
                                            const index = selectedStateArr.value.indexOf(state);
                                            selectedStateArr.value.splice(index, 1);
                                        });
                                    }
                                }
                            }
                            else if(!activeCidArr.value.includes(Number(character['cid']))){
                                activeCidArr.value.push(Number(character['cid']));
                            }
                        });
                    }

                    function setActiveTaxa() {
                        const newActiveFamilyArr = [];
                        const newActiveTidArr = [];
                        taxaDataArr.value.forEach(taxon => {
                            const cidArr = [];
                            let includeTaxon = true;
                            taxon['keyData'].forEach(char => {
                                if(includeTaxon && selectedCidArr.value.includes(Number(char['cid'])) && !selectedCsidArr.value.includes(Number(char['csid']))){
                                    includeTaxon = false;
                                }
                                else if(!cidArr.includes(Number(char['cid']))){
                                    cidArr.push(Number(char['cid']));
                                }
                            });
                            selectedCidArr.value.forEach(cid => {
                                if(!cidArr.includes(Number(cid))){
                                    includeTaxon = false;
                                }
                            });
                            if(includeTaxon){
                                newActiveTidArr.push(taxon['tid']);
                                if(!newActiveFamilyArr.includes(taxon['family'])){
                                    newActiveFamilyArr.push(taxon['family']);
                                }
                            }
                        });
                        activeFamilyArr.value = newActiveFamilyArr.slice();
                        activeTidArr.value = newActiveTidArr.slice();
                    }

                    function setChecklistData() {
                        const formData = new FormData();
                        formData.append('clid', clId.value.toString());
                        formData.append('action', 'getChecklistData');
                        fetch(checklistApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                        .then((data) => {
                            checklistData.value = Object.assign({}, data);
                            clidArr.value = checklistData.value['clidArr'].slice();
                            setTaxaData();
                        });
                    }

                    function setKeyData() {
                        const formData = new FormData();
                        formData.append('csidArr', JSON.stringify(csidArr.value));
                        formData.append('includeFullKeyData', '1');
                        formData.append('action', 'getKeyCharacterStatesArr');
                        fetch(keyCharacterStateApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            processKeyData(data);
                        });
                    }

                    function setProjectData() {
                        const formData = new FormData();
                        formData.append('pid', pId.value.toString());
                        formData.append('action', 'getProjectData');
                        fetch(projectApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            projectData.value = Object.assign({}, data);
                            clidArr.value = Object.values(projectData.value['clidArr']).slice();
                            setTaxaData();
                        });
                    }

                    function setQueryPopupDisplay(val) {
                        displayQueryPopup.value = val;
                    }

                    function setTaxaData() {
                        const formData = new FormData();
                        formData.append('clidArr', JSON.stringify(clidArr.value));
                        formData.append('includeKeyData', '1');
                        formData.append('action', 'getChecklistTaxa');
                        fetch(checklistTaxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            taxaDataArr.value = data;
                            processTaxaData();
                        });
                    }

                    function setTaxaDisplayData() {
                        const newDataArr = [];
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
                        taxaDisplayDataArr.value = newDataArr.slice();
                    }

                    Vue.onMounted(() => {
                        if(Number(clId.value) > 0){
                            setChecklistData();
                        }
                        else if(Number(pId.value) > 0){
                            setProjectData();
                        }
                        else{
                            if(Number(queryId) === 0){
                                displayQueryPopup.value = true;
                            }
                            searchStore.initializeSearchStorage(queryId);
                        }
                    });

                    return {
                        activeChidArr,
                        activeCidArr,
                        activeFamilyArr,
                        activeTidArr,
                        checklistData,
                        checklistName,
                        clId,
                        clientRoot,
                        displayCommonNamesVal,
                        displayImagesVal,
                        displayQueryPopup,
                        keyDataArr,
                        languageArr,
                        pId,
                        popupWindowType,
                        projectData,
                        projectName,
                        selectedCsidArr,
                        selectedSortByOption,
                        selectedStateArr,
                        showSpatialPopup,
                        sortByOptions,
                        spatialInputValues,
                        taxaCount,
                        taxaDisplayDataArr,
                        buildChecklist,
                        closeSpatialPopup,
                        openSpatialPopup,
                        processCharacterStateSelectionChange,
                        processDisplayCommonNameChange,
                        processDisplayImagesChange,
                        processSortByChange,
                        processSpatialData,
                        setQueryPopupDisplay
                    }
                }
            });
            keyIdentificationModule.use(Quasar, { config: {} });
            keyIdentificationModule.use(Pinia.createPinia());
            keyIdentificationModule.mount('#mainContainer');
        </script>
    </body>
</html>

