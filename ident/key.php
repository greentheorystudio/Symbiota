<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid',$_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$pid = array_key_exists('proj',$_REQUEST) ? (int)$_REQUEST['pid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Interactive Key</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            const CLID = <?php echo $clid; ?>;
            const PID = <?php echo $pid; ?>;
        </script>
    </head>
    <body>
        <div id="app-container">
            <?php
            include(__DIR__ . '/../header.php');
            ?>
            <div class="navpath">
                <a :href="(clientRoot + '/index.php')">Home</a> &gt;&gt;
                <template v-if="Number(clId) > 0">
                    <a :href="(clientRoot + '/checklists/checklist.php?cl=' + clId + '&proj=' + pId)">Checklist: {{ checklistName }}</a> &gt;&gt;
                    <span class="q-ml-xs text-bold">Key: {{ checklistName }}</span>
                </template>
                <template v-else-if="Number(pId) > 0">
                    <a :href="(clientRoot + '/projects/index.php?pid=' + pId)">Project Checklists</a> &gt;&gt;
                    <span class="q-ml-xs text-bold">Key: {{ projectName }} Project</span>
                </template>
            </div>
            <div id="innertext">

            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script>
            const keyIdentificationModule = Vue.createApp({
                setup() {
                    const baseStore = useBaseStore();

                    const checklistData = Vue.ref({});
                    const checklistName = Vue.computed(() => {
                        return checklistData.value.hasOwnProperty('name') ? checklistData.value['name'] : '';
                    });
                    const clId = CLID;
                    const clientRoot = baseStore.getClientRoot;
                    const languageArr = [];
                    const pId = PID;
                    const projectData = Vue.ref({});
                    const projectName = Vue.computed(() => {
                        return projectData.value.hasOwnProperty('projname') ? projectData.value['projname'] : '';
                    });

                    function setChecklistData() {
                        const formData = new FormData();
                        formData.append('clid', clId.toString());
                        formData.append('action', 'getChecklistData');
                        fetch(checklistApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                        .then((data) => {
                            checklistData.value = Object.assign({}, data);
                        });
                    }

                    function setProjectData() {
                        const formData = new FormData();
                        formData.append('pid', pId.toString());
                        formData.append('action', 'getProjectData');
                        fetch(projectApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            projectData.value = Object.assign({}, data);
                        });
                    }

                    Vue.onMounted(() => {
                        if(Number(clId) > 0){
                            setChecklistData();
                        }
                        else if(Number(clId) > 0){
                            setProjectData();
                        }
                    });

                    return {
                        checklistData,
                        checklistName,
                        clId,
                        clientRoot,
                        languageArr,
                        pId,
                        projectData,
                        projectName
                    }
                }
            });
            keyIdentificationModule.use(Quasar, { config: {} });
            keyIdentificationModule.use(Pinia.createPinia());
            keyIdentificationModule.mount('#app-container');
        </script>
    </body>
</html>

