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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> - View User Profile</title>
        <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .create-account-container {
                width: 90%;
            }
        </style>
        <script src="../js/external/all.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../js/external/tiny_mce/tiny_mce.js"></script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="innertext">
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
                            <account-checklist-project-list :checklist-arr="checklistArr" :project-arr="projectArr"></account-checklist-project-list>
                        </q-tab-panel>
                        <q-tab-panel name="occurrence">
                            <view-profile-occurrence-module :account-info="accountInfo"></view-profile-occurrence-module>
                        </q-tab-panel>
                        <q-tab-panel name="account">
                            <view-profile-account-module :account-info="accountInfo" :checklist-arr="checklistArr" :project-arr="projectArr" :uid="uid" @update:account-information="updateAccountObj"></view-profile-account-module>
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card>
            </template>
        </div>
        <?php
        include(__DIR__ . '/../footer.php');
        include_once(__DIR__ . '/../config/footer-includes.php');
        ?>
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
                    'view-profile-account-module': viewProfileAccountModule,
                    'account-checklist-project-list': accountChecklistProjectList,
                    'view-profile-occurrence-module': viewProfileOccurrenceModule
                },
                setup() {
                    const store = useBaseStore();
                    const accountInfo = Vue.ref(null);
                    const checklistArr = Vue.ref([]);
                    const clientRoot = store.getClientRoot;
                    const projectArr = Vue.ref([]);
                    const tab = Vue.ref('account');
                    const uid = store.getSymbUid;
                    const validUser = store.getValidUser;

                    function setAccountChecklists() {
                        const formData = new FormData();
                        formData.append('action', 'getChecklistListByUserRights');
                        fetch(checklistApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                checklistArr.value = resObj;
                            });
                        });
                    }

                    function setAccountInfo() {
                        const formData = new FormData();
                        formData.append('uid', uid);
                        formData.append('action', 'getUserByUid');
                        fetch(profileApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                accountInfo.value = resObj;
                            });
                        });
                    }

                    function setAccountProjects() {
                        const formData = new FormData();
                        formData.append('action', 'getProjectListByUserRights');
                        fetch(projectApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                projectArr.value = resObj;
                            });
                        });
                    }

                    function updateAccountObj(obj) {
                        accountInfo.value = Object.assign({}, obj);
                    }

                    Vue.onMounted(() => {
                        if(validUser){
                            tab.value = 'checklists';
                        }
                        if(Number(uid) > 0){
                            setAccountInfo();
                            setAccountChecklists();
                            setAccountProjects();
                        }
                        else{
                            window.location.href = clientRoot + '/index.php';
                        }
                    });

                    return {
                        accountInfo,
                        checklistArr,
                        projectArr,
                        tab,
                        uid,
                        validUser,
                        updateAccountObj
                    }
                }
            });
            viewProfileModule.use(Quasar, { config: {} });
            viewProfileModule.use(Pinia.createPinia());
            viewProfileModule.mount('#innertext');
        </script>
    </body>
</html>
