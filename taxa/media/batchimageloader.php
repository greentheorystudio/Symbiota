<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' . SanitizerService::getCleanedRequestPath(true));
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Media Batch Uploader</title>
        <meta name="description" content="Taxa media batch uploader for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <style>
            .instruction-container {
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')">Home</a> &gt;&gt;
                <span class="text-bold">Taxa Media Batch Uploader</span>
            </div>
            <div class="q-pa-md">
                <h1>Taxa Media Batch Uploader</h1>
                <template v-if="isEditor">
                    <div>
                        <div class="instruction-container">
                            To batch upload taxa image, audio, and video files either click the Add files button to select the files to be uploaded or drag and
                            drop the files onto the box below. A csv spreadheet can also be uploaded to provide further metadata for the files.
                            <a :href="(clientRoot + '/templates/batchTaxaImageData.csv')"><span class="text-bold">Use this template for the csv spreadsheet for image files. </span></a>
                            <a :href="(clientRoot + '/templates/batchTaxaMediaData.csv')"><span class="text-bold">Use this template for the csv spreadsheet for audio and video files.</span></a>
                            Data for image files can be combined with data for audio and video files in the same csv spreadsheet. For each
                            row in the spreadsheet, the value in the filename column must match the filename of the associated file being uploaded.
                        </div>
                        <div class="q-mt-md">
                            <media-file-upload-input-element @upload:complete="processMediaUpdate"></media-file-upload-input-element>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="text-bold">You do not have permissions to access this tool</div>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/imageTagSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceSelectorInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceLinkageToolPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/mediaFileUploadInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const taxaBatchMediaUploaderModule = Vue.createApp({
                components: {
                    'media-file-upload-input-element': mediaFileUploadInputElement,
                },
                setup() {
                    const { showNotification } = useCore();
                    const baseStore = useBaseStore();

                    const clientRoot = baseStore.getClientRoot;
                    const isEditor = Vue.ref(false);

                    function processMediaUpdate() {
                        showNotification('positive','Upload successful.');
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'TaxonProfile');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resData) => {
                                isEditor.value = resData.includes('TaxonProfile');
                            });
                        });
                    }

                    Vue.onMounted(() => {
                        setEditor();
                    });

                    return {
                        clientRoot,
                        isEditor,
                        processMediaUpdate
                    }
                }
            });
            taxaBatchMediaUploaderModule.use(Quasar, { config: {} });
            taxaBatchMediaUploaderModule.use(Pinia.createPinia());
            taxaBatchMediaUploaderModule.mount('#mainContainer');
        </script>
    </body>
</html>
