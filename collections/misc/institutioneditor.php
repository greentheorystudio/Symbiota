<?php
include_once(__DIR__ . '../../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '../../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Locations</title>
    <meta name="description" content="Institution index for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=10.8.1" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <style>
        div.q-menu.q-position-engine {
            z-index: 100000000000;
        }
    </style>
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
</head>
<body>
<a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
<?php
include(__DIR__ . '../../../header.php');
?>
<div id="mainContainer">
    <div id="breadcrumbs">
        <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
        <span class="text-bold">Locations</span>
    </div>
    <div class="q-pa-md">
        <div class="column q-gutter-sm">
            <div class="row justify-between">
                <h1>
                    Locations
                </h1>
<!--                the button here doesn't show up ... valid user works, so it's quasar not working?-->
                <div v-if="validUser" class="row justify-end q-gutter-sm q-pr-md">
                    <div>
                        <b-button size="sm">Small Button</b-button>
                        <q-btn color="secondary" @click="openInstitutionsEditorPopup();" label="Create Locatiion" tabindex="0" />
                    </div>
                </div>
            </div>
            <template v-if="Object.keys(institutionsArr).length > 0">
                <template v-for="projectGroup in institutionsArr">
                    <q-card v-if="projectGroup['checklists'].length > 0" flat>
                        <q-card-section class="column q-gutter-sm">
                            <div v-if="projectGroup['projname']" class="row justify-start">
                                <div class="text-h6 text-bold">
                                    {{ projectGroup['projname'] }}
                                </div>
<!--                                SPACIAL STUFF, IGNORE FOR NOW-->
<!--                                <div v-if="projectGroup['coordinates'].length > 0" class="q-ml-sm self-center">-->
<!--                                    <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openSpatialPopup(projectGroup['coordinates']);" icon="fas fa-globe" dense aria-label="See checklists on map" tabindex="0">-->
<!--                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">-->
<!--                                            See locations on map-->
<!--                                        </q-tooltip>-->
<!--                                    </q-btn>-->
<!--                                </div>-->
                            </div>
                            <div class="column">
<!--                                what's the difference between the  checklist['name'] and projectGroup['projname']??? which one is shown in checklists rn -->
                                <template v-for="checklist in projectGroup['checklists']">
                                    <div class="row justify-start">
                                        <div class="text-body1">
                                            <a :href="(clientRoot + '/checklists/checklist.php?clid=' + checklist['clid'])" tabindex="0">{{ checklist['name'] }}</a>
                                        </div>
                                        <div v-if="(checklist['latcentroid'] && checklist['longcentroid']) || checklist['footprintwkt']" class="q-ml-md self-center">
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openSpatialPopup(((checklist['latcentroid'] && checklist['longcentroid']) ? [[Number(checklist['longcentroid']), Number(checklist['latcentroid'])]] : null), (checklist['footprintwkt'] ? checklist['footprintwkt'] : null));" icon="fas fa-globe" dense aria-label="See checklist on map" tabindex="0">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    See checklist on map
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </q-card-section>
                    </q-card>
                </template>
            </template>
            <template v-else>
                <div class="text-h4 text-bold">
                    There are no checklists available at this time.
                </div>
            </template>
        </div>
<!--        POP-UPS, FOCUS ON SHOWING LIST FOR NOW-->
<!--        <template v-if="showInstitutionsEditorPopup">-->
<!--            <institutions-editor-popup-->
<!--                    :show-popup="showInstitutionsEditorPopup"-->
<!--                    @close:popup="showInstitutionsEditorPopup = false"-->
<!--            ></institutions-editor-popup>-->
<!--        </template>-->
<!--        <template v-if="showSpatialPopup">-->
<!--            <spatial-viewer-popup-->
<!--                    :coordinate-set="spatialPopupCoordinateSet"-->
<!--                    :footprint-wkt="spatialPopupFootprintWkt"-->
<!--                    :show-popup="showSpatialPopup"-->
<!--                    @close:popup="closeSpatialPopup();"-->
<!--            ></spatial-viewer-popup>-->
<!--        </template>-->
    </div>
</div>
<?php
include_once(__DIR__ . '../../../config/footer-includes.php');
include(__DIR__ . '../../../footer.php');
?>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/institutions.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<!--<script src="--><?php //echo $GLOBALS['CLIENT_ROOT']; ?><!--/stores/checklist-taxa.js?ver=--><?php //echo $GLOBALS['JS_VERSION']; ?><!--" type="text/javascript"></script>-->
<!--<script src="--><?php //echo $GLOBALS['CLIENT_ROOT']; ?><!--/stores/checklist.js?ver=--><?php //echo $GLOBALS['JS_VERSION']; ?><!--" type="text/javascript"></script>-->
<!--<script src="--><?php //echo $GLOBALS['CLIENT_ROOT']; ?><!--/stores/project.js?ver=--><?php //echo $GLOBALS['JS_VERSION']; ?><!--" type="text/javascript"></script>-->
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/pwdInput.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionCatalogNumberQuickSearch.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountInformationForm.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/viewProfileAccountModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountChecklistProjectList.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionControlPanelMenus.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/viewProfileOccurrenceModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxonDescriptionSourceTabAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceFootprintWktInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/wysiwygInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userPermissionManagementModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/institutions/institutionsFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/institutions/institutionsEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<!--<script src="--><?php //echo $GLOBALS['CLIENT_ROOT']; ?><!--/components/checklists/checklistEditorAppConfigTab.js?ver=--><?php //echo $GLOBALS['JS_VERSION']; ?><!--" type="text/javascript"></script>-->
<!--<script src="--><?php //echo $GLOBALS['CLIENT_ROOT']; ?><!--/components/checklists/checklistEditorAdminTab.js?ver=--><?php //echo $GLOBALS['JS_VERSION']; ?><!--" type="text/javascript"></script>-->
<!--<script src="--><?php //echo $GLOBALS['CLIENT_ROOT']; ?><!--/components/checklists/checklistFieldModule.js?ver=--><?php //echo $GLOBALS['JS_VERSION']; ?><!--" type="text/javascript"></script>-->
<!--<script src="--><?php //echo $GLOBALS['CLIENT_ROOT']; ?><!--/components/checklists/checklistEditorPopup.js?ver=--><?php //echo $GLOBALS['JS_VERSION']; ?><!--" type="text/javascript"></script>-->
<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
<script type="text/javascript">
    const institutionIndexModule = Vue.createApp({
        components: {
            'institutions-editor-popup': institutionsEditorPopup,
            'spatial-viewer-popup': spatialViewerPopup
        },
        setup() {
            const baseStore = useBaseStore();
            const institutionsStore = useInstitutionsStore();

            const institutionsArr = Vue.ref([]);
            const clientRoot = baseStore.getClientRoot;
            const showInstitutionsEditorPopup = Vue.ref(false);
            const showSpatialPopup = Vue.ref(false);
            const spatialPopupCoordinateSet = Vue.ref(null);
            const spatialPopupFootprintWkt = Vue.ref(null);
            const validUser = baseStore.getValidUser;

            function closeSpatialPopup() {
                spatialPopupCoordinateSet.value = null;
                spatialPopupFootprintWkt.value = null;
                showSpatialPopup.value = false;
            }

            function openInstitutionsEditorPopup() {
                // institutionsStore.setChecklist(0);
                showInstitutionsEditorPopup.value = true;
            }

            function openSpatialPopup(coordSet, footprintWkt = null) {
                if(coordSet){
                    spatialPopupCoordinateSet.value = coordSet;
                }
                if(footprintWkt){
                    spatialPopupFootprintWkt.value = footprintWkt;
                }
                showSpatialPopup.value = true;
            }
            function setInstitutionsArr() {
                const formData = new FormData();
                formData.append('action', 'getInstitutionsArr');
                fetch(institutionsApiUrl, {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => {
                        return response.ok ? response.json() : null;
                    })
                    .then((resData) => {
                        institutionsArr.value = resData;
                    });
            }

            Vue.onMounted(() => {
                setInstitutionsArr();
            });

            return {
                institutionsArr,
                clientRoot,
                showInstitutionsEditorPopup,
                showSpatialPopup,
                spatialPopupCoordinateSet,
                spatialPopupFootprintWkt,
                validUser,
                closeSpatialPopup,
                openInstitutionsEditorPopup,
                openSpatialPopup
            }
        }
    });
    institutionIndexModule.use(Quasar, { config: {} });
    institutionIndexModule.use(Pinia.createPinia());
    institutionIndexModule.mount('#mainContainer');
</script>
</body>
</html>
