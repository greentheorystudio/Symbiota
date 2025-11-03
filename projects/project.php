<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$pid = array_key_exists('pid', $_REQUEST) ? (int)$_REQUEST['pid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Biotic Inventory Project Details</title>
        <meta name="description" content="Individual biotic inventory project details in the the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <style>
            div.q-menu.q-position-engine {
                z-index: 100000000000;
            }
        </style>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol-ext.min.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/turf.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/shp.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jszip.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/stream.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/FileSaver.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/html2canvas.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/geotiff.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/plotty.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const PID = <?php echo $pid; ?>;
        </script>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer">Skip to main content</a>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <a :href="(clientRoot + '/projects/index.php')" tabindex="0">Biotic Inventory Projects</a> &gt;&gt;
                <span class="text-bold">{{ projectData['projname'] }}</span>
            </div>
            <div class="q-pa-md column">
                <div class="q-mb-md full-width row justify-between q-gutter-sm items-center">
                    <div class="row q-gutter-md">
                        <div>
                            <h1>{{ projectData['projname'] }}</h1>
                        </div>
                    </div>
                    <div class="row justify-end q-gutter-sm items-center">
                        <template v-if="Number(pId) > 0 && isEditor">
                            <div>
                                <q-btn color="grey-4" text-color="black" class="black-border cursor-pointer" size="sm" @click="showProjectEditorPopup = true" icon="fas fa-cog" dense aria-label="Open Project Administration" tabindex="0">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Open Project Administration
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </template>
                    </div>
                </div>
                <div v-if="projectData.hasOwnProperty('managers') && projectData['managers']" class="text-h6">
                    <span class="text-bold">Project Managers: </span>{{ projectData['managers'] }}
                </div>
                <div v-if="projectData.hasOwnProperty('fulldescription') && projectData['fulldescription']" class="text-body1" v-html="projectData['fulldescription']"></div>
                <template v-if="projectChecklistArr.length > 0">
                    <div class="q-mt-md column">
                        <div class="row justify-start q-gutter-md">
                            <div class="text-h6 text-bold">Checklists</div>
                            <div v-if="projectChecklistCoordArr.length > 0" class="self-center">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showSpatialPopup = true" icon="fas fa-globe" dense aria-label="See checklists on map" tabindex="0">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        See checklists on map
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </div>
                        <div class="q-mt-xs q-ml-md column">
                            <template v-for="checklist in projectChecklistArr">
                                <div class="row justify-start">
                                    <div class="text-body1">
                                        <a :href="(clientRoot + '/checklists/checklist.php?clid=' + checklist['clid'] + '&pid=' + projectData['pid'])" tabindex="0">{{ checklist['name'] }}</a>
                                    </div>
                                    <div v-if="keyModuleIsActive && checklist['defaultsettings'] && checklist['defaultsettings'].hasOwnProperty('keyactive') && checklist['defaultsettings']['keyactive']" class="self-center">
                                        <q-btn role="link" text-color="black" size="sm" :href="(clientRoot + '/ident/key.php?clid=' + checklist['clid'] + '&pid=' + projectId)" icon="fas fa-key" dense unelevated :ripple="false" aria-label="Open Interactive Key" tabindex="0">
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Open Interactive Key
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            <template v-if="showSpatialPopup">
                <spatial-viewer-popup
                    :coordinate-set="projectChecklistCoordArr"
                    :show-popup="showSpatialPopup"
                    @close:popup="showSpatialPopup = false"
                ></spatial-viewer-popup>
            </template>
            <template v-if="showProjectEditorPopup">
                <project-editor-popup
                    :show-popup="showProjectEditorPopup"
                    @close:popup="showProjectEditorPopup = false"
                ></project-editor-popup>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/wysiwygInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialBaseLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userPermissionManagementModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/projectEditorChecklistManagementTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/projectEditorAdminTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/projectFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/projectEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const projectModule = Vue.createApp({
                components: {
                    'project-editor-popup': projectEditorPopup,
                    'spatial-viewer-popup': spatialViewerPopup
                },
                setup() {
                    const baseStore = useBaseStore();
                    const projectStore = useProjectStore();

                    const clientRoot = baseStore.getClientRoot;
                    const isEditor = Vue.ref(false);
                    const keyModuleIsActive = baseStore.getKeyModuleIsActive;
                    const pId = Vue.ref(PID);
                    const projectChecklistArr = Vue.computed(() => projectStore.getProjectChecklistArr);
                    const projectChecklistCoordArr = Vue.computed(() => projectStore.getProjectChecklistCoordArr);
                    const projectData = Vue.computed(() => projectStore.getProjectData);
                    const projectId = Vue.computed(() => projectStore.getProjectID);
                    const showProjectEditorPopup = Vue.ref(false);
                    const showSpatialPopup = Vue.ref(false);

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'ProjAdmin');
                        formData.append('key', pId.value.toString());
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isEditor.value = resData.includes('ProjAdmin');
                        });
                    }

                    function setProjectData() {
                        projectStore.setProject(pId.value, (pid) => {
                            if(!Number(pid) > 0){
                                window.location.href = (clientRoot + '/projects/index.php');
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        if(Number(pId.value) > 0){
                            setProjectData();
                            setEditor();
                        }
                        else{
                            window.location.href = (clientRoot + '/projects/index.php');
                        }
                    });

                    return {
                        clientRoot,
                        isEditor,
                        keyModuleIsActive,
                        pId,
                        projectChecklistArr,
                        projectChecklistCoordArr,
                        projectData,
                        projectId,
                        showProjectEditorPopup,
                        showSpatialPopup
                    }
                }
            });
            projectModule.use(Quasar, { config: {} });
            projectModule.use(Pinia.createPinia());
            projectModule.mount('#mainContainer');
        </script>
    </body>
</html>
