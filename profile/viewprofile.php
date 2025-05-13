<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> View Profile</title>
        <meta name="description" content="View and manage account profile for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/tiny_mce/tiny_mce.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer" class="q-pa-md">
            <template v-if="accountInfo">
                <q-card class="q-mt-lg">
                    <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                        <template v-if="validUser">
                            <q-tab name="checklists" label="Checklists and Projects" no-caps></q-tab>
                            <q-tab name="occurrence" label="Occurrence Management" no-caps></q-tab>
                        </template>
                        <q-tab name="account" label="Account Information" no-caps></q-tab>
                    </q-tabs>
                    <q-separator></q-separator>
                    <q-tab-panels v-model="tab">
                        <q-tab-panel name="checklists">
                            <?php include_once(__DIR__ . '/../checklists/checklistadminmeta.php'); ?>
                            <account-checklist-project-list></account-checklist-project-list>
                        </q-tab-panel>
                        <q-tab-panel name="occurrence">
                            <view-profile-occurrence-module></view-profile-occurrence-module>
                        </q-tab-panel>
                        <q-tab-panel name="account">
                            <view-profile-account-module></view-profile-account-module>
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/collection.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/user.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/pwdInput.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionCatalogNumberQuickSearch.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountInformationForm.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/viewProfileAccountModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountChecklistProjectList.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionControlPanelMenus.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/viewProfileOccurrenceModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const viewProfileModule = Vue.createApp({
                components: {
                    'account-checklist-project-list': accountChecklistProjectList,
                    'view-profile-account-module': viewProfileAccountModule,
                    'view-profile-occurrence-module': viewProfileOccurrenceModule
                },
                setup() {
                    const baseStore = useBaseStore();
                    const userStore = useUserStore();

                    const accountInfo = Vue.computed(() => userStore.getUserData);
                    const clientRoot = baseStore.getClientRoot;
                    const tab = Vue.ref('account');
                    const uid = baseStore.getSymbUid;
                    const validUser = baseStore.getValidUser;

                    Vue.onMounted(() => {
                        if(validUser){
                            tab.value = 'checklists';
                        }
                        if(Number(uid) > 0){
                            userStore.setUser(uid);
                        }
                        else{
                            window.location.href = clientRoot + '/index.php';
                        }
                    });

                    return {
                        accountInfo,
                        tab,
                        uid,
                        validUser
                    }
                }
            });
            viewProfileModule.use(Quasar, { config: {} });
            viewProfileModule.use(Pinia.createPinia());
            viewProfileModule.mount('#mainContainer');
        </script>
    </body>
</html>
