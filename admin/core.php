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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Portal Configuration Manager</title>
        <meta name="description" content="Manage configurations for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer">Skip to main content</a>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div class="q-pa-md">
                <template v-if="isAdmin">
                    <q-card class="q-mt-lg">
                        <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab name="core" label="Core Configurations" no-caps></q-tab>
                            <q-tab name="taxonomy" label="Taxonomy Configurations" no-caps></q-tab>
                            <q-tab name="additional" label="Additional Configurations" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel name="core">
                                <core-configurations-tab></core-configurations-tab>
                            </q-tab-panel>
                            <q-tab-panel name="taxonomy">
                                <taxonomy-configurations-tab></taxonomy-configurations-tab>
                            </q-tab-panel>
                            <q-tab-panel name="additional">
                                <additional-configurations-tab></additional-configurations-tab>
                            </q-tab-panel>
                        </q-tab-panels>
                    </q-card>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/configuration.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/admin/coreConfigurationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/admin/taxonomyConfigurationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/admin/additionalConfigurationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const coreConfigurationsModule = Vue.createApp({
                components: {
                    'additional-configurations-tab': additionalConfigurationsTab,
                    'core-configurations-tab': coreConfigurationsTab,
                    'taxonomy-configurations-tab': taxonomyConfigurationsTab
                },
                setup() {
                    const baseStore = useBaseStore();
                    const configurationStore = useConfigurationStore();

                    const isAdmin = Vue.ref(false);
                    const tab = Vue.ref('core');

                    function setIsAdmin() {
                        const formData = new FormData();
                        formData.append('permissionJson', JSON.stringify(['SuperAdmin']));
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            if(resData.includes('SuperAdmin')){
                                isAdmin.value = true;
                                configurationStore.setConfigurationData();
                            }
                            else{
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        setIsAdmin();
                    });

                    return {
                        isAdmin,
                        tab
                    }
                }
            });
            coreConfigurationsModule.use(Quasar, { config: {} });
            coreConfigurationsModule.use(Pinia.createPinia());
            coreConfigurationsModule.mount('#mainContainer');
        </script>
    </body>
</html>
