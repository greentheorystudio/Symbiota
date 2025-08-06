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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklists</title>
        <meta name="description" content="Checklist index for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
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
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')">Home</a> &gt;&gt;
                <span class="text-bold">Checklists</span>
            </div>
            <div class="q-pa-md">
                <div class="column q-gutter-md">
                    <h1>
                        Checklists
                    </h1>
                    <template v-if="checklistArr.length > 0">
                        <template v-for="projectGroup in checklistArr">
                            <q-card v-if="projectGroup['checklists'].length > 0" flat>
                                <q-card-section class="column q-gutter-md">
                                    <div v-if="projectGroup['projname']" class="row justify-start">
                                        <div class="text-h4 text-bold">
                                            <a :href="(clientRoot + '/projects/project.php?pid=' + projectGroup['pid'])">{{ projectGroup['projname'] }}</a>
                                        </div>
                                        <div v-if="projectGroup['coordinates'].length > 0" class="q-ml-sm self-center">
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openSpatialPopup(projectGroup['coordinates']);" icon="fas fa-globe" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    See checklists on map
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                    <div class="column">
                                        <template v-for="checklist in projectGroup['checklists']">
                                            <div class="row justify-start">
                                                <div class="text-body1">
                                                    <a :href="(clientRoot + '/checklists/checklist.php?clid=' + checklist['clid'])">{{ checklist['name'] }}</a>
                                                </div>
                                                <div v-if="(checklist['latcentroid'] && checklist['longcentroid']) || checklist['footprintwkt']" class="q-ml-md self-center">
                                                    <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openSpatialPopup(((checklist['latcentroid'] && checklist['longcentroid']) ? [[Number(checklist['longcentroid']), Number(checklist['latcentroid'])]] : null), (checklist['footprintwkt'] ? checklist['footprintwkt'] : null));" icon="fas fa-globe" dense>
                                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                            See checklist on map
                                                        </q-tooltip>
                                                    </q-btn>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </template>
                    </template>
                    <template v-else>
                        <div class="text-h4 text-bold">
                            There are no checklists available at this time.
                        </div>
                    </template>
                </div>
                <template v-if="showSpatialPopup">
                    <spatial-viewer-popup
                        :coordinate-set="spatialPopupCoordinateSet"
                        :footprint-wkt="spatialPopupFootprintWkt"
                        :show-popup="showSpatialPopup"
                        @close:popup="closeSpatialPopup();"
                    ></spatial-viewer-popup>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialBaseLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const checklistIndexModule = Vue.createApp({
                components: {
                    'spatial-viewer-popup': spatialViewerPopup
                },
                setup() {
                    const baseStore = useBaseStore();

                    const checklistArr = Vue.ref([]);
                    const clientRoot = baseStore.getClientRoot;
                    const showSpatialPopup = Vue.ref(false);
                    const spatialPopupCoordinateSet = Vue.ref(null);
                    const spatialPopupFootprintWkt = Vue.ref(null);

                    function closeSpatialPopup() {
                        spatialPopupCoordinateSet.value = null;
                        spatialPopupFootprintWkt.value = null;
                        showSpatialPopup.value = false;
                    }

                    function openSpatialPopup(coordSet, footprintWkt = null) {
                        if(coordSet){
                            spatialPopupCoordinateSet.value = coordSet;
                        }
                        if(footprintWkt){
                            spatialPopupFootprintWkt.value = footprintWkt;
                        }
                        showSpatialPopup.value = true;
                    }

                    function setChecklistArr() {
                        const formData = new FormData();
                        formData.append('action', 'getChecklistIndexArr');
                        fetch(checklistApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            checklistArr.value = resData;
                            Object.keys(checklistArr.value).forEach((pid) => {
                                if(checklistArr.value[pid]['checklists'].length > 0){
                                    checklistArr.value[pid]['checklists'].sort((a, b) => {
                                        return a['name'].toLowerCase().localeCompare(b['name'].toLowerCase());
                                    });
                                }
                            });
                        });
                    }

                    Vue.onMounted(() => {
                        setChecklistArr();
                    });

                    return {
                        checklistArr,
                        clientRoot,
                        showSpatialPopup,
                        spatialPopupCoordinateSet,
                        spatialPopupFootprintWkt,
                        closeSpatialPopup,
                        openSpatialPopup
                    }
                }
            });
            checklistIndexModule.use(Quasar, { config: {} });
            checklistIndexModule.use(Pinia.createPinia());
            checklistIndexModule.mount('#mainContainer');
        </script>
    </body>
</html>
