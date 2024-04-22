<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceEditorManager.php');
include_once(__DIR__ . '/../../classes/ProfileManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 600);

$occId = array_key_exists('occid',$_REQUEST) ? (int)$_REQUEST['occid'] : 0;
$collId = array_key_exists('collid',$_REQUEST) ? (int)$_REQUEST['collid'] : 0;
$displayMode = array_key_exists('mode',$_REQUEST) ? (int)$_REQUEST['mode'] : 1;
$goToMode = array_key_exists('gotomode',$_REQUEST)?(int)$_REQUEST['gotomode']:0;
$occIndex = array_key_exists('occindex',$_REQUEST)?(int)$_REQUEST['occindex']:null;
$ouid = array_key_exists('ouid',$_REQUEST)?(int)$_REQUEST['ouid']:0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Editor</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" type="text/css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .editor-inner-container {
                width: 80%;
            }
            .occurrence-entry-format-selector {
                min-width: 125px;
            }
            .black-border {
                border-color: black;
            }
        </style>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const COLLID = <?php echo $collId; ?>;
            const DISPLAY_MODE = <?php echo $displayMode; ?>;
            const OCCID = <?php echo $occId; ?>;

            function openSpatialInputWindow(type) {
                let mapWindow = open("../../spatial/index.php?windowtype=" + type,"input","resizable=0,width=900,height=700,left=100,top=20");
                if (mapWindow.opener == null) {
                    mapWindow.opener = self;
                }
                mapWindow.addEventListener('blur', function(){
                    mapWindow.close();
                    mapWindow = null;
                });
            }
        </script>
    </head>
    <body>
        <div id="occurrence-editor-container">
            <template v-if="displayMode !== 3">
                <occurrence-editor-single-display></occurrence-editor-single-display>
            </template>
            <template v-else>
                <occurrence-editor-table-display></occurrence-editor-table-display>
            </template>
            <occurrence-editor-query-popup :show-popup="displayQueryPopup"></occurrence-editor-query-popup>
            <occurrence-editor-batch-update-popup :show-popup="displayBatchUpdatePopup"></occurrence-editor-batch-update-popup>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include_once(__DIR__ . '/../../spatial/vue-spatial-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleCountryAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleStateProvinceAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleCountyAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceEntryFormatSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceEntryFollowUpActionSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorOccurrenceDataControls.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>

        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormLocationElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorLocationModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormCollectingEventElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorCollectingEventModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormLatestIdentificationElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormMiscElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorFormCurationElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEntryImageFormModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEntryObservationFormModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEntrySkeletalFormModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorOccurrenceDataModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorAdditionalDataTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorDeterminationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorImagesTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorMediaTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorResourcesTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorAdminTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorTabModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorQueryPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorBatchUpdatePopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorImageTranscriberPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorSingleDisplay.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorTableDisplay.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const occurrenceEditorControllerModule = Vue.createApp({
                components: {
                    'occurrence-editor-batch-update-popup': occurrenceEditorBatchUpdatePopup,
                    'occurrence-editor-query-popup': occurrenceEditorQueryPopup,
                    'occurrence-editor-single-display': occurrenceEditorSingleDisplay,
                    'occurrence-editor-table-display': occurrenceEditorTableDisplay
                },
                setup() {
                    const baseStore = useBaseStore();
                    const occurrenceStore = useOccurrenceStore();

                    const clientRoot = baseStore.getClientRoot;
                    const displayBatchUpdateButton = Vue.ref(true);
                    const displayBatchUpdatePopup = Vue.ref(false);
                    const displayQueryPopup = Vue.ref(false);
                    const displayQueryPopupButton = Vue.ref(true);
                    const displayMode = Vue.computed(() => occurrenceStore.getDisplayMode);
                    const initialCollId = COLLID;
                    const initialDisplayMode = DISPLAY_MODE;
                    const initialOccId = OCCID;

                    function changeBatchUpdatePopupDisplay(value) {
                        displayBatchUpdatePopup.value = value;
                    }

                    function changeQueryPopupDisplay(value) {
                        displayQueryPopup.value = value;
                    }

                    Vue.provide('changeBatchUpdatePopupDisplay', changeBatchUpdatePopupDisplay);
                    Vue.provide('changeQueryPopupDisplay', changeQueryPopupDisplay);
                    Vue.provide('displayBatchUpdateButton', displayBatchUpdateButton);
                    Vue.provide('displayQueryPopupButton', displayQueryPopupButton);
                    Vue.provide('occurrenceStore', occurrenceStore);

                    Vue.onMounted(() => {
                        if(Number(initialCollId) > 0 || Number(initialOccId) > 0){
                            if(Number(initialCollId) > 0){
                                occurrenceStore.setCollection(initialCollId);
                            }
                            if(Number(initialDisplayMode) > 1){
                                occurrenceStore.setDisplayMode(initialDisplayMode);
                            }
                            occurrenceStore.setCurrentOccurrenceRecord(initialOccId);
                            if(Number(initialOccId) > 0){
                                displayBatchUpdateButton.value = true;
                                displayQueryPopupButton.value = true;
                            }
                        }
                        else{
                            window.location.href = clientRoot + '/index.php';
                        }
                    });

                    return {
                        displayBatchUpdatePopup,
                        displayMode,
                        displayQueryPopup
                    }
                }
            });
            occurrenceEditorControllerModule.use(Quasar, { config: {} });
            occurrenceEditorControllerModule.use(Pinia.createPinia());
            occurrenceEditorControllerModule.mount('#occurrence-editor-container');
        </script>
    </body>
</html>
