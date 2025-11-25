<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$tId = array_key_exists('tid', $_REQUEST) ? (int)$_REQUEST['tid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Character State Management</title>
        <meta name="description" content="Taxa character state management module for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Taxa Character State Management</span>
            </div>
            <div v-if="isTaxonProfileEditor" class="q-pa-md">
                <div class="q-mb-sm text-h5 text-bold">
                    Taxa Character State Management
                </div>

            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script type="text/javascript">
            const taxaCharacterStateManagementModule = Vue.createApp({
                setup() {
                    const baseStore = useBaseStore();

                    const clientRoot = baseStore.getClientRoot;
                    const isEditor = Vue.ref(false);

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'KeyAdmin');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isEditor.value = resData.includes('KeyAdmin');
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
                        isEditor
                    }
                }
            });
            taxaCharacterStateManagementModule.use(Quasar, { config: {} });
            taxaCharacterStateManagementModule.use(Pinia.createPinia());
            taxaCharacterStateManagementModule.mount('#mainContainer');
        </script>
    </body>
</html>
