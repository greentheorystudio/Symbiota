<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Occurrence Data Upload Module</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const COLLID = <?php echo $collid; ?>;
        </script>
    </head>
    <body>
        <?php
            include(__DIR__ . '/../../header.php');
        ?>
        <div class="navpath">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/collections/misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
            <b>Occurrence Data Upload Module</b>
        </div>
        <div id="innertext">
            <h1>Occurrence Data Upload Module</h1>
            <template v-if="isEditor">
                <q-card>
                    <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                        <q-tab name="occurrence" label="Occurrence Records" no-caps></q-tab>
                        <q-tab name="media" label="Media" no-caps></q-tab>
                        <q-tab name="temp" label="Temp" no-caps></q-tab>
                    </q-tabs>
                    <q-separator></q-separator>
                    <q-tab-panels v-model="tab">
                        <q-tab-panel name="occurrence">
                            <?php include_once(__DIR__ . '/occurrenceloader.php'); ?>
                        </q-tab-panel>
                        <q-tab-panel name="media">
                            <?php include_once(__DIR__ . '/imageloader.php'); ?>
                        </q-tab-panel>
                        <q-tab-panel name="temp">
                            <div class="row q-mt-xs">
                                <div class="col-grow">
                                    <file-picker-input-element :accepted-types="acceptedFileTypes" :value="selectedFile" :validate-file-size="false" @update:file="(value) => processUploadFile(value[0])"></file-picker-input-element>
                                </div>
                            </div>
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/filePickerInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const occurrenceDataUploadModule = Vue.createApp({
                components: {
                    'file-picker-input-element': filePickerInputElement
                },
                setup() {
                    const { processCsvDownload } = useCore();
                    const baseStore = useBaseStore();
                    const collectionStore = useCollectionStore();

                    const acceptedFileTypes = ['geojson'];
                    const collId = COLLID;
                    const isEditor = Vue.computed(() => {
                        return (collectionStore.getCollectionPermissions.includes('CollAdmin') || collectionStore.getCollectionPermissions.includes('CollEditor'));
                    });
                    const selectedFile = Vue.ref(null);
                    const tab = Vue.ref('occurrence');

                    function processUploadFile(file) {
                        const fileReader = new FileReader();
                        fileReader.onload = () => {
                            const csvArr = [];
                            const filename = 'rare_plant_upload.csv';
                            const geoJSONFormat = new ol.format.GeoJSON();
                            const wktFormat = new ol.format.WKT();
                            const uploadData = JSON.parse(fileReader.result);
                            const uploadFeatures = geoJSONFormat.readFeatures(uploadData);
                            uploadFeatures.forEach((feature) => {
                                if(feature){
                                    const featureData = {};
                                    const featureProps = feature.getProperties();
                                    const featureGeometry = feature.getGeometry();
                                    const wktStr = wktFormat.writeGeometry(featureGeometry);
                                    Object.keys(featureProps).forEach((prop) => {
                                        if(prop !== 'geometry'){
                                            if(featureProps[prop]){
                                                if(prop.toLowerCase().includes('date')){
                                                    const date = new Date(featureProps[prop]);
                                                    const year = date.getFullYear();
                                                    const month = String(date.getMonth() + 1).padStart(2, '0');
                                                    const day = String(date.getDate()).padStart(2, '0');
                                                    featureData[prop.toLowerCase()] = `${year}-${month}-${day}`;
                                                }
                                                else{
                                                    featureData[prop.toLowerCase()] = isNaN(featureProps[prop]) ? featureProps[prop].trim() : featureProps[prop];
                                                }
                                            }
                                            else{
                                                featureData[prop.toLowerCase()] = null;
                                            }
                                        }
                                    });
                                    featureData['footprintwkt'] = wktStr;
                                    csvArr.push(featureData);
                                }
                            });
                            processCsvDownload(csvArr, filename);
                        };
                        fileReader.readAsText(file);
                    }

                    Vue.onMounted(() => {
                        collectionStore.setCollection(collId, () => {
                            if(isEditor.value){
                                //setUnlinkedRecordCounts();
                            }
                            else{
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                        });
                    });

                    return {
                        acceptedFileTypes,
                        isEditor,
                        selectedFile,
                        tab,
                        processUploadFile
                    }
                }
            });
            occurrenceDataUploadModule.use(Quasar, { config: {} });
            occurrenceDataUploadModule.use(Pinia.createPinia());
            occurrenceDataUploadModule.mount('#innertext');
        </script>
    </body>
</html>
