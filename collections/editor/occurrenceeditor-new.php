<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceEditorManager.php');
include_once(__DIR__ . '/../../classes/ProfileManager.php');
include_once(__DIR__ . '/../../classes/SOLRManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 600);

$occId = array_key_exists('occid',$_REQUEST)?(int)$_REQUEST['occid']:0;
$collId = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;
$displayType = (array_key_exists('display',$_REQUEST) && $_REQUEST['display'] === 'table') ? 'table' : 'record';
$goToMode = array_key_exists('gotomode',$_REQUEST)?(int)$_REQUEST['gotomode']:0;
$occIndex = array_key_exists('occindex',$_REQUEST)?(int)$_REQUEST['occindex']:null;
$ouid = array_key_exists('ouid',$_REQUEST)?(int)$_REQUEST['ouid']:0;
$crowdSourceMode = array_key_exists('csmode',$_REQUEST)?(int)$_REQUEST['csmode']:0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Editor</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .editor-inner-container {
                width: 80%;
            }
        </style>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const COLLID = <?php echo $collId; ?>;
            const CROWD_SOURCE_MODE = <?php echo $crowdSourceMode ? 'true' : 'false'; ?>;
            const DISPLAY_TYPE = '<?php echo $displayType; ?>';
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
        <div id="occurrence-editor-container" class="row justify-center q-py-sm">
            <q-card class="editor-inner-container">
                <q-card-section class="column q-gutter-sm">
                    <div>
                        <a :href="clientRoot + '/index.php'">Home</a> &gt;&gt;
                        <template v-if="crowdSourceMode">
                            <a :href="clientRoot + '/collections/management/crowdsource/index.php'">Crowd Sourcing Central</a> &gt;&gt;
                        </template>
                        <template v-else-if="isEditor">
                            <a :href="clientRoot + '/collections/misc/collprofiles.php?collid=' + collId + '&emode=1'">Collection Control Panel</a> &gt;&gt;
                        </template>
                        <template v-if="occId > 0">
                            <span class="text-bold">Occurrence Editor</span>
                        </template>
                        <template v-else>
                            <span class="text-bold">Create New Record</span>
                        </template>
                    </div>
                    <q-card flat bordered class="q-mt-sm">
                        <q-card-section class="row justify-between">
                            <div class="text-h6 text-weight-bold self-center">
                                <template v-if="collInfo">
                                    <template v-if="collInfo.collectionname">{{ collInfo.collectionname }}</template>
                                    <template v-if="collInfo.institutioncode || collInfo.collectioncode"> (<template v-if="collInfo.institutioncode">{{ collInfo.institutioncode }}</template><template v-if="collInfo.institutioncode && collInfo.collectioncode">-</template><template v-if="collInfo.collectioncode">{{ collInfo.collectioncode }}</template>)</template>
                                </template>
                            </div>
                            <div class="column q-gutter-xs">
                                <div class="row q-gutter-xs">
                                    <template v-if="displayQueryPopupButton">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" icon="fas fa-search" ripple="false" @click="displayQueryPopup = true">
                                                <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Open Search/Filter Window
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </template>
                                    <template v-if="imageCount > 0">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayImageTranscriberPopup = true" label="Image Transcription" dense />
                                        </div>
                                    </template>
                                </div>
                                <template v-if="displayType === 'record'">
                                    <div class="row justify-end">
                                        <div class="self-center text-bold q-mr-xs">Record {{ currentRecordIndex }} of {{ recordCount }}</div>
                                        <q-btn v-if="recordCount > 1 && currentRecordIndex !== 1" icon="first_page" color="grey-8" round dense flat @click="goToFirstRecord"></q-btn>
                                        <q-btn v-if="currentRecordIndex !== 1" icon="chevron_left" color="grey-8" round dense flat @click="goToPreviousRecord"></q-btn>
                                        <q-btn v-if="currentRecordIndex !== recordCount" icon="chevron_right" color="grey-8" round dense flat @click="goToNextRecord"></q-btn>
                                        <q-btn v-if="recordCount > 1 && currentRecordIndex !== recordCount" icon="last_page" color="grey-8" round dense flat @click="goToLastRecord"></q-btn>
                                    </div>
                                </template>
                            </div>
                        </q-card-section>
                    </q-card>
                    <template v-if="displayType === 'record'">
                        <q-card flat bordered class="q-mt-sm">
                            <q-card-section>
                                <occurrence-editor-module :occid="occId"></occurrence-editor-module>
                            </q-card-section>
                        </q-card>
                    </template>
                </q-card-section>
            </q-card>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceEditorModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const occurrenceEditorManagerModule = Vue.createApp({
                components: {
                    'occurrence-editor-module': occurrenceEditorModule
                },
                setup() {
                    const baseStore = useBaseStore();
                    const occurrenceStore = useOccurrenceStore();

                    const clientRoot = baseStore.getClientRoot;
                    const collId = COLLID;
                    const collInfo = Vue.computed(() => occurrenceStore.getCollectionData);
                    const crowdSourceMode = CROWD_SOURCE_MODE;
                    const currentRecordIndex = Vue.computed(() => occurrenceStore.getCurrentRecordIndex);
                    const displayImageTranscriberPopup = Vue.ref(false);
                    const displayQueryPopup = Vue.ref(false);
                    const displayQueryPopupButton = Vue.ref(false);
                    const displayType = Vue.ref(DISPLAY_TYPE);
                    const imageCount = Vue.computed(() => occurrenceStore.getImageCount);
                    const isEditor = Vue.computed(() => occurrenceStore.getIsEditor);
                    const occId = Vue.ref(OCCID);
                    const recordCount = Vue.computed(() => occurrenceStore.getRecordCount);

                    function goToFirstRecord() {
                        occId.value = occurrenceStore.getFirstRecord;
                    }

                    function goToLastRecord() {
                        occId.value = occurrenceStore.getLastRecord;
                    }

                    function goToNextRecord() {
                        occId.value = occurrenceStore.getNextRecord;
                    }

                    function goToPreviousRecord() {
                        occId.value = occurrenceStore.getPreviousRecord;
                    }

                    Vue.provide('occurrenceStore', occurrenceStore);

                    Vue.onMounted(() => {
                        if(Number(collId) > 0){
                            //occurrenceStore.setCollection(collId);
                        }
                    });

                    return {
                        clientRoot,
                        collId,
                        collInfo,
                        crowdSourceMode,
                        currentRecordIndex,
                        displayImageTranscriberPopup,
                        displayQueryPopup,
                        displayQueryPopupButton,
                        displayType,
                        imageCount,
                        isEditor,
                        occId,
                        recordCount,
                        goToFirstRecord,
                        goToLastRecord,
                        goToNextRecord,
                        goToPreviousRecord
                    }
                }
            });
            occurrenceEditorManagerModule.use(Quasar, { config: {} });
            occurrenceEditorManagerModule.use(Pinia.createPinia());
            occurrenceEditorManagerModule.mount('#occurrence-editor-container');
        </script>
    </body>
</html>
