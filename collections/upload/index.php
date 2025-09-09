<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Data Upload Module</title>
        <meta name="description" content="Occurrence data upload module for collections in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/turf.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const COLLID = <?php echo $collid; ?>;
        </script>
    </head>
    <body>
        <?php
            include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')">Home</a> &gt;&gt;
                <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collId)">Collection Control Panel</a> &gt;&gt;
                <span class="text-bold">Occurrence Data Upload Module</span>
            </div>
            <div class="q-pa-md">
                <div class="text-h5 text-bold">Occurrence Data Upload Module</div>
                <div v-if="Number(collectionData.collid) > 0" class="q-ml-md column q-mb-sm">
                    <div class="text-bold">
                        {{ collectionData.collectionname }}
                    </div>
                    <div>
                    <span class="text-bold">
                        Last Data Upload Date:
                    </span>
                        {{ collectionData.uploaddate }}
                    </div>
                </div>
                <template v-if="isEditor">
                    <q-card>
                        <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab name="occurrence" label="Data" no-caps></q-tab>
                            <q-tab name="media" label="Media Files" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel name="occurrence">
                                <occurrence-data-upload-module :collid="collId"></occurrence-data-upload-module>
                            </q-tab-panel>
                            <q-tab-panel name="media">
                                <occurrence-media-file-upload-module :collid="collId"></occurrence-media-file-upload-module>
                            </q-tab-panel>
                        </q-tab-panels>
                    </q-card>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/collection-data-upload-parameters.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/collection-media-upload-parameters.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/filePickerInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/imageTagSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceSelectorInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceLinkageToolPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/mediaFileUploadInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/upload/fieldMapperPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/upload/uploadDataTableViewerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionDataUploadParametersFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionMediaUploadParametersFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionDataUploadParametersEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionMediaUploadParametersEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/occurrenceDataUploadModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/occurrenceMediaFileUploadModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const collectionOccurrenceDataUploadModule = Vue.createApp({
                components: {
                    'occurrence-data-upload-module': occurrenceDataUploadModule,
                    'occurrence-media-file-upload-module': occurrenceMediaFileUploadModule
                },
                setup() {
                    const baseStore = useBaseStore();
                    const collectionStore = useCollectionStore();

                    const clientRoot = baseStore.getClientRoot;
                    const collectionData = Vue.computed(() => collectionStore.getCollectionData);
                    const collId = COLLID;
                    const isEditor = Vue.computed(() => {
                        return (collectionStore.getCollectionPermissions.includes('CollAdmin') || collectionStore.getCollectionPermissions.includes('CollEditor'));
                    });
                    const tab = Vue.ref('occurrence');

                    Vue.onMounted(() => {
                        collectionStore.setCollection(collId, () => {
                            if(!isEditor.value){
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                        });
                    });

                    return {
                        clientRoot,
                        collectionData,
                        collId,
                        isEditor,
                        tab
                    }
                }
            });
            collectionOccurrenceDataUploadModule.use(Quasar, { config: {} });
            collectionOccurrenceDataUploadModule.use(Pinia.createPinia());
            collectionOccurrenceDataUploadModule.mount('#mainContainer');
        </script>
    </body>
</html>
