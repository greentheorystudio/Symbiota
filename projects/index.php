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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Biotic Inventory Projects Index</title>
        <meta name="description" content="Biotic inventory projects index for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <style>
            div.q-menu.q-position-engine {
                z-index: 100000000000;
            }
        </style>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Biotic Inventory Projects</span>
            </div>
            <div class="q-pa-md">
                <div class="column q-gutter-sm">
                    <div class="q-mb-md row justify-between">
                        <h1>
                            Biotic Inventory Projects
                        </h1>
                        <div v-if="validUser" class="row justify-end q-gutter-sm q-pr-md">
                            <div>
                                <q-btn color="secondary" @click="openProjectEditorPopup();" label="Create Project" tabindex="0" />
                            </div>
                        </div>
                    </div>
                    <template v-if="projectArr.length > 0">
                        <template v-for="project in projectArr">
                            <q-card>
                                <q-card-section>
                                    <div class="text-h5 text-bold">
                                        <a :href="(clientRoot + '/projects/project.php?pid=' + project['pid'])" tabindex="0">{{ project['projname'] }}</a>
                                    </div>
                                    <div class="text-body1 text-bold">{{ 'Managers: ' + project['managers'] }}</div>
                                </q-card-section>
                                <q-card-section class="q-pt-none" v-html="project['fulldescription']"></q-card-section>
                            </q-card>
                        </template>
                    </template>
                    <template v-else>
                        <div class="text-h4 text-bold">
                            There are no biotic inventory projects available at this time.
                        </div>
                    </template>
                </div>
                <template v-if="showProjectEditorPopup">
                    <project-editor-popup
                        :show-popup="showProjectEditorPopup"
                        @close:popup="showProjectEditorPopup = false"
                    ></project-editor-popup>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/wysiwygInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userPermissionManagementModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/projectEditorChecklistManagementTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/projectEditorAdminTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/projectFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/projectEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const projectIndexModule = Vue.createApp({
                components: {
                    'project-editor-popup': projectEditorPopup
                },
                setup() {
                    const baseStore = useBaseStore();
                    const projectStore = useProjectStore();

                    const clientRoot = baseStore.getClientRoot;
                    const projectArr = Vue.ref([]);
                    const showProjectEditorPopup = Vue.ref(false);
                    const validUser = baseStore.getValidUser;

                    function openProjectEditorPopup() {
                        projectStore.setProject(0);
                        showProjectEditorPopup.value = true;
                    }

                    function setProjectArr() {
                        const formData = new FormData();
                        formData.append('action', 'getProjectArr');
                        fetch(projectApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            projectArr.value = resData;
                        });
                    }

                    Vue.onMounted(() => {
                        setProjectArr();
                    });

                    return {
                        projectArr,
                        clientRoot,
                        showProjectEditorPopup,
                        validUser,
                        openProjectEditorPopup
                    }
                }
            });
            projectIndexModule.use(Quasar, { config: {} });
            projectIndexModule.use(Pinia.createPinia());
            projectIndexModule.mount('#mainContainer');
        </script>
    </body>
</html>
