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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Mapping Configuration Manager</title>
        <meta name="description" content="Manage mapping configurations for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div class="q-pa-md">
                <template v-if="isAdmin">
                    <div class="row justify-end q-px-md q-mb-sm">
                        <div onclick="openTutorialWindow('/tutorial/admin/mappingConfigurationManager/index.php');" title="Open Tutorial Window">
                            <q-icon name="far fa-question-circle" size="20px" class="cursor-pointer" />
                        </div>
                    </div>
                    <q-card class="q-mt-lg">
                        <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab name="mapwindow" label="Map Window" no-caps></q-tab>
                            <q-tab name="symbology" label="Symbology" no-caps></q-tab>
                            <q-tab name="layers" label="Layers" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel name="mapwindow">
                                <map-window-configurations-tab></map-window-configurations-tab>
                            </q-tab-panel>
                            <q-tab-panel name="symbology">
                                <symbology-configurations-tab></symbology-configurations-tab>
                            </q-tab-panel>
                            <q-tab-panel name="layers">
                                <layers-configurations-tab></layers-configurations-tab>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/admin/layersConfigurationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/admin/mapWindowConfigurationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/admin/symbologyConfigurationsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const mappingConfigurationsModule = Vue.createApp({
                components: {
                    'layers-configurations-tab': layersConfigurationsTab,
                    'map-window-configurations-tab': mapWindowConfigurationsTab,
                    'symbology-configurations-tab': symbologyConfigurationsTab
                },
                setup() {
                    const { openTutorialWindow } = useCore();
                    const baseStore = useBaseStore();
                    const configurationStore = useConfigurationStore();

                    const isAdmin = Vue.ref(false);
                    const tab = Vue.ref('mapwindow');

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
                        tab,
                        openTutorialWindow
                    }
                }
            });
            mappingConfigurationsModule.use(Quasar, { config: {} });
            mappingConfigurationsModule.use(Pinia.createPinia());
            mappingConfigurationsModule.mount('#mainContainer');
        </script>
    </body>
</html>
