<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Media Uploader & Importer</title>
        <meta name="description" content="Taxa media uploader and importer for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Taxa Media Uploader & Importer</span>
            </div>
            <div v-if="isEditor" class="q-pa-md">
                <div class="q-mb-sm text-h5 text-bold">
                    Taxa Media Uploader & Importer
                </div>
                <q-card>
                    <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                        <q-tab name="uploader" label="Batch Uploader" no-caps></q-tab>
                        <q-tab name="eol" label="EOL Importer" no-caps></q-tab>
                        <q-tab name="external" label="External File Importer" no-caps></q-tab>
                    </q-tabs>
                    <q-separator></q-separator>
                    <q-tab-panels v-model="tab">
                        <q-tab-panel name="uploader">
                            <taxa-media-batch-loader></taxa-media-batch-loader>
                        </q-tab-panel>
                        <q-tab-panel name="eol">
                            <eol-media-importer></eol-media-importer>
                        </q-tab-panel>
                        <q-tab-panel name="external">
                            <external-media-file-import-module media-type="taxa"></external-media-file-import-module>
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleLanguageAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/imageTagSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/filePickerInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceSelectorInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceLinkageToolPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxonRankCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/mediaFileUploadInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaMediaBatchLoader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/eolMediaImporter.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/externalMediaFileImportModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const taxonomicThesaurusManagerModule = Vue.createApp({
                components: {
                    'eol-media-importer': eolMediaImporter,
                    'external-media-file-import-module': externalMediaFileImportModule,
                    'taxa-media-batch-loader': taxaMediaBatchLoader
                },
                setup() {
                    const baseStore = useBaseStore();

                    const clientRoot = baseStore.getClientRoot;
                    const isEditor = Vue.ref(false);
                    const tab = Vue.ref('uploader');

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'TaxonProfile');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isEditor.value = resData.includes('TaxonProfile');
                            if(!isEditor.value){
                                window.location.href = clientRoot + '/index.php';
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        setEditor();
                    });
                    
                    return {
                        clientRoot,
                        isEditor,
                        tab
                    }
                }
            });
            taxonomicThesaurusManagerModule.use(Quasar, { config: {} });
            taxonomicThesaurusManagerModule.use(Pinia.createPinia());
            taxonomicThesaurusManagerModule.mount('#mainContainer');
        </script>
    </body>
</html>
