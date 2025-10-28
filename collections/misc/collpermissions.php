<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Collection Permissions Management Module</title>
        <meta name="description" content="Manage permissions for collections in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
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
                <a :href="(clientRoot + '/index.php')" tabindex="1">Home</a> &gt;&gt;
                <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collId)" tabindex="1">Collection Control Panel</a> &gt;&gt;
                <span class="text-bold">Manage Permissions</span>
            </div>
            <div v-if="isEditor" class="q-pa-md column q-gutter-md">
                <q-card flat bordered>
                    <q-card-section>
                        <user-permission-management-module permission-label="Administrator" permission="CollAdmin" :table-pk="collId"></user-permission-management-module>
                    </q-card-section>
                </q-card>
            </div>
            <div v-if="isEditor" class="q-pa-md column q-gutter-md">
                <q-card flat bordered>
                    <q-card-section>
                        <user-permission-management-module permission-label="Editor" permission="CollEditor" :table-pk="collId"></user-permission-management-module>
                    </q-card-section>
                </q-card>
            </div>
            <div v-if="isEditor" class="q-pa-md column q-gutter-md">
                <q-card flat bordered>
                    <q-card-section>
                        <user-permission-management-module permission-label="Rare Species Reader" permission="RareSppReader" :table-pk="collId"></user-permission-management-module>
                    </q-card-section>
                </q-card>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userPermissionManagementModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const occurrenceTaxonomyManagementModule = Vue.createApp({
                components: {
                    'user-permission-management-module': userPermissionManagementModule
                },
                setup() {
                    const baseStore = useBaseStore();
                    const collectionStore = useCollectionStore();

                    const clientRoot = baseStore.getClientRoot;
                    const collId = COLLID;
                    const isEditor = Vue.computed(() => {
                        return collectionStore.getCollectionPermissions.includes('CollAdmin');
                    });

                    Vue.onMounted(() => {
                        collectionStore.setCollection(collId, () => {
                            if(!isEditor.value){
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                        });
                    });

                    return {
                        clientRoot,
                        collId,
                        isEditor
                    }
                }
            });
            occurrenceTaxonomyManagementModule.use(Quasar, { config: {} });
            occurrenceTaxonomyManagementModule.use(Pinia.createPinia());
            occurrenceTaxonomyManagementModule.mount('#mainContainer');
        </script>
    </body>
</html>
