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
                            <view-profile-account-module :account-info="accountInfo" :checklist-arr="checklistArr" :project-arr="projectArr" :uid="uid"  @update:account-information="updateAccountObj"></view-profile-account-module>
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card>
            </template>
        </div>
        <?php
        include(__DIR__ . '/../footer.php');
        include_once(__DIR__ . '/../config/footer-includes.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/pwdInput.js?ver=20230702" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountInformationForm.js?ver=20230707" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/viewProfileAccountModule.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountChecklistProjectList.js?ver=20230709" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionControlPanelMenus.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/viewProfileOccurrenceModule.js" type="text/javascript"></script>
        <script>
            const viewProfileModule = Vue.createApp({
                data() {
                    return {
                        accountInfo: Vue.ref(null),
                        checklistArr: Vue.ref([]),
                        clientRoot: Vue.ref(CLIENT_ROOT),
                        projectArr: Vue.ref([]),
                        solrMode: Vue.ref(SOLR_MODE),
                        uid: Vue.ref(SYMB_UID)
                    }
                },
                components: {
                    'view-profile-account-module': viewProfileAccountModule,
                    'account-checklist-project-list': accountChecklistProjectList,
                    'view-profile-occurrence-module': viewProfileOccurrenceModule
                },
                setup () {
                    const validUser = Vue.ref(VALID_USER);
                    const tab = validUser ? Vue.ref('checklists') : Vue.ref('account');
                    return {
                        validUser,
                        tab
                    }
                },
                mounted() {
                    if(Number(this.uid) > 0){
                        this.setAccountInfo();
                        this.setAccountChecklistsProjects();
                    }
                    else{
                        window.location.href = CLIENT_ROOT + '/index.php';
                    }
                },
                methods: {
                    setAccountChecklistsProjects(){
                        const formData = new FormData();
                        formData.append('uid', this.uid);
                        formData.append('action', 'getChecklistsProjectsByUid');
                        fetch(checklistApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                if(resObj.hasOwnProperty('cl')){
                                    this.checklistArr = resObj['cl'];
                                }
                                if(resObj.hasOwnProperty('proj')){
                                    this.projectArr = resObj['proj'];
                                }
                            });
                        });
                    },
                    setAccountInfo(){
                        const formData = new FormData();
                        formData.append('uid', this.uid);
                        formData.append('action', 'getAccountInfoByUid');
                        fetch(profileApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                this.accountInfo = resObj;
                            });
                        });
                    },
                    updateAccountObj(obj) {
                        this.accountInfo = Object.assign({}, obj);
                    }
                }
            });
            viewProfileModule.use(Quasar, { config: {} });
            viewProfileModule.mount('#innertext');
        </script>
    </body>
</html>
