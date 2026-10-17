<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
ini_set('max_execution_time', 3600);
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> GUID/UUID Management</title>
        <meta name="description" content="GUID/UUID management for collection occurrence data in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script type="text/javascript">
            const COLLID = <?php echo $collid; ?>;
        </script>
    </head>
    <body>
        <?php 
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collId)" tabindex="0">Collection Control Panel</a> &gt;&gt;
                <span class="text-bold">GUID/UUID Management</span>
            </div>
            <div class="q-pa-md">
                <template v-if="isEditor">
                    <div class="row justify-start q-px-md q-mb-sm">
                        <div class="text-h5 text-bold">GUID/UUID Management</div>
                    </div>
                    <div class="processor-container">
                        <div class="processor-control-container">
                            <q-card class="processor-control-card">
                                <q-card-section class="column q-gutter-sm">
                                    <div class="q-pa-sm column">
                                        <div class="row q-gutter-sm">
                                            <span>Image records missing GUID/UUID:</span><span class="text-bold">{{ nullImageCnt }}</span>
                                        </div>
                                        <div class="row q-gutter-sm">
                                            <span>Media records missing GUID/UUID:</span><span class="text-bold">{{ nullMediaCnt }}</span>
                                        </div>
                                        <div class="row q-gutter-sm">
                                            <span>Determination records missing GUID/UUID:</span><span class="text-bold">{{ nullDeterminationCnt }}</span>
                                        </div>
                                        <div class="row q-gutter-sm">
                                            <span>Genetic records missing GUID/UUID:</span><span class="text-bold">{{ nullGeneticCnt }}</span>
                                        </div>
                                        <template v-if="includeEventLocation">
                                            <div class="row q-gutter-sm">
                                                <span>Location records missing GUID/UUID:</span><span class="text-bold">{{ nullLocationCnt }}</span>
                                            </div>
                                            <div class="row q-gutter-sm">
                                                <span>Event records missing GUID/UUID:</span><span class="text-bold">{{ nullEventCnt }}</span>
                                            </div>
                                        </template>
                                        <template v-if="includeMof">
                                            <div class="row q-gutter-sm">
                                                <span>MoF records missing GUID/UUID:</span><span class="text-bold">{{ nullMofCnt }}</span>
                                            </div>
                                        </template>
                                        <div class="row q-gutter-sm">
                                            <span>Occurrence records missing GUID/UUID:</span><span class="text-bold">{{ nullOccurrenceCnt }}</span>
                                        </div>
                                    </div>
                                    <div class="processor-tool-control-container">
                                        <div class="processor-cancel-message-container text-negative text-bold">
                                            <template v-if="processCancelling">
                                                Cancelling, please wait
                                            </template>
                                        </div>
                                        <div class="processor-tool-button-container">
                                            <template v-if="nullCountsExist">
                                                <div>
                                                    <q-btn color="secondary" @click="initializeProcess();" label="Generate GUID/UUID values" dense aria-label="Start Process" :disabled="currentProcess || !nullCountsExist" tabindex="0" />
                                                </div>
                                                <div>
                                                    <q-btn v-if="currentProcess" :disabled="processCancelling" color="red" @click="cancelProcess();" label="Cancel" dense aria-label="Cancel" tabindex="0" />
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="text-bold text-green-9">
                                                    All records have GUID/UUID values
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>

                        <div class="processor-display-container">
                            <q-card class="bg-grey-3 q-pa-sm">
                                <q-scroll-area ref="procDisplayScrollAreaRef" class="bg-grey-1 processor-display" @scroll="setScroller">
                                    <q-list dense>
                                        <template v-if="!currentProcess && processorDisplayCurrentIndex > 0">
                                            <q-item>
                                                <q-item-section>
                                                    <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollUp();" @keyup.enter="processorDisplayScrollUp();" aria-label="Show previous 100 entries" tabindex="0">Show previous 100 entries</a></div>
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                        <q-item v-for="proc in processorDisplayArr">
                                            <q-item-section>
                                                <div>
                                                    {{ proc.procText }}
                                                    <q-spinner v-if="proc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner>
                                                </div>
                                                <template v-if="!proc.loading && proc.resultText">
                                                    <div v-if="proc.result === 'success'" class="q-ml-sm text-weight-bold text-green-9">
                                                        {{proc.resultText}}
                                                    </div>
                                                    <div v-if="proc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                                        {{proc.resultText}}
                                                    </div>
                                                </template>
                                            </q-item-section>
                                        </q-item>
                                        <template v-if="!currentProcess && processorDisplayCurrentIndex < processorDisplayIndex">
                                            <q-item>
                                                <q-item-section>
                                                    <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollDown();" @keyup.enter="processorDisplayScrollDown();" aria-label="Show next 100 entries" tabindex="0">Show next 100 entries</a></div>
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                    </q-list>
                                </q-scroll-area>
                            </q-card>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script type="text/javascript">
            const guidManagementModule = Vue.createApp({
                setup() {
                    const baseStore = useBaseStore();
                    const collectionStore = useCollectionStore();
                    const imageStore = useImageStore();
                    const mediaStore = useMediaStore();
                    const occurrenceStore = useOccurrenceStore();
                    const occurrenceCollectingEventStore = useOccurrenceCollectingEventStore();
                    const occurrenceDeterminationStore = useOccurrenceDeterminationStore();
                    const occurrenceGeneticLinkStore = useOccurrenceGeneticLinkStore();
                    const occurrenceLocationStore = useOccurrenceLocationStore();

                    const clientRoot = baseStore.getClientRoot;
                    const collectionData = Vue.computed(() => collectionStore.getCollectionData);
                    const collId = COLLID;
                    const collInfo = Vue.computed(() => collectionStore.getCollectionData);
                    const currentProcess = Vue.ref(null);
                    const eventMofDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
                    const includeEventLocation = Vue.computed(() => {
                        return (collectionData.value['datarecordingmethod'] === 'lot' || collectionData.value['datarecordingmethod'] === 'replicate');
                    });
                    const includeMof = Vue.computed(() => {
                        return (Object.keys(eventMofDataFields.value).length > 0 ||Object.keys(locationMofDataFields.value).length > 0 || Object.keys(occurrenceMofDataFields.value).length > 0);
                    });
                    const isEditor = Vue.computed(() => {
                        return collectionStore.getCollectionPermissions.includes('CollAdmin');
                    });
                    const locationMofDataFields = Vue.computed(() => collectionStore.getLocationMofDataFields);
                    const nullCountsExist = Vue.computed(() => {
                        return (
                            nullDeterminationCnt.value > 0 ||
                            nullEventCnt.value > 0 ||
                            nullGeneticCnt.value > 0 ||
                            nullImageCnt.value > 0 ||
                            nullLocationCnt.value > 0 ||
                            nullMediaCnt.value > 0 ||
                            nullMofCnt.value > 0 ||
                            nullOccurrenceCnt.value > 0
                        );
                    });
                    const nullDeterminationCnt = Vue.ref(0);
                    const nullEventCnt = Vue.ref(0);
                    const nullGeneticCnt = Vue.ref(0);
                    const nullImageCnt = Vue.ref(0);
                    const nullLocationCnt = Vue.ref(0);
                    const nullMediaCnt = Vue.ref(0);
                    const nullMofCnt = Vue.ref(0);
                    const nullOccurrenceCnt = Vue.ref(0);
                    const occurrenceMofDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
                    const procDisplayScrollAreaRef = Vue.ref(null);
                    const procDisplayScrollHeight = Vue.ref(0);
                    const processCancelling = Vue.ref(false);
                    const processorDisplayArr = Vue.reactive([]);
                    let processorDisplayDataArr = [];
                    const processorDisplayCurrentIndex = Vue.ref(0);
                    const processorDisplayIndex = Vue.ref(0);
                    const scrollProcess = Vue.ref(null);

                    function addProcessToProcessorDisplay(processObj) {
                        processorDisplayArr.push(processObj);
                        if(processorDisplayArr.length > 100){
                            if(processorDisplayDataArr.length > 900){
                                processorDisplayDataArr.splice(0, 100);
                            }
                            const precessorArrSegment = processorDisplayArr.slice(0, 100);
                            processorDisplayDataArr = processorDisplayDataArr.concat(precessorArrSegment);
                            processorDisplayArr.splice(0, 100);
                            processorDisplayIndex.value++;
                            processorDisplayCurrentIndex.value = processorDisplayIndex.value;
                        }
                    }

                    function adjustUIEnd() {
                        currentProcess.value = null;
                        processCancelling.value = false;
                        processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
                        setNullGuidCounts();
                    }

                    function adjustUIStart() {
                        processorDisplayArr.length = 0;
                        processorDisplayDataArr = [];
                        processorDisplayCurrentIndex.value = 0;
                        processorDisplayIndex.value = 0;
                    }

                    function cancelProcess() {
                        processCancelling.value = true;
                    }

                    function getNewProcessObject(id, type, text) {
                        return {
                            id: id,
                            procText: text,
                            type: type,
                            loading: true,
                            current: true,
                            result: '',
                            resultText: ''
                        };
                    }

                    function initializeProcess() {
                        adjustUIStart();
                        processImageRecords();
                    }

                    function processDeterminationRecords() {
                        if(processCancelling.value){
                            adjustUIEnd();
                        }
                        else if(nullDeterminationCnt.value > 0){
                            const text = 'Generating determination GUID/UUID values';
                            currentProcess.value = 'processDeterminationRecords';
                            addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                            occurrenceDeterminationStore.batchPopulateCollectionDeterminationRecordGUIDs(collId, () => {
                                processSuccessResponse('Complete');
                                processGeneticRecords();
                            });
                        }
                        else{
                            processGeneticRecords();
                        }
                    }

                    function processEventRecords() {
                        if(processCancelling.value){
                            adjustUIEnd();
                        }
                        else if(nullEventCnt.value > 0){
                            const text = 'Generating event GUID/UUID values';
                            currentProcess.value = 'processEventRecords';
                            addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                            occurrenceCollectingEventStore.batchPopulateCollectionEventRecordGUIDs(collId, () => {
                                processSuccessResponse('Complete');
                                processMofRecords();
                            });
                        }
                        else{
                            processMofRecords();
                        }
                    }

                    function processGeneticRecords() {
                        if(processCancelling.value){
                            adjustUIEnd();
                        }
                        else if(nullGeneticCnt.value > 0){
                            const text = 'Generating genetic record GUID/UUID values';
                            currentProcess.value = 'processGeneticRecords';
                            addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                            occurrenceGeneticLinkStore.batchPopulateCollectionGeneticRecordGUIDs(collId, () => {
                                processSuccessResponse('Complete');
                                processLocationRecords();
                            });
                        }
                        else{
                            processLocationRecords();
                        }
                    }

                    function processImageRecords() {
                        if(processCancelling.value){
                            adjustUIEnd();
                        }
                        else if(nullImageCnt.value > 0){
                            const text = 'Generating image GUID/UUID values';
                            currentProcess.value = 'processImageRecords';
                            addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                            imageStore.batchPopulateCollectionImageRecordGUIDs(collId, () => {
                                processSuccessResponse('Complete');
                                processMediaRecords();
                            });
                        }
                        else{
                            processMediaRecords();
                        }
                    }

                    function processLocationRecords() {
                        if(processCancelling.value){
                            adjustUIEnd();
                        }
                        else if(nullLocationCnt.value > 0){
                            const text = 'Generating location GUID/UUID values';
                            currentProcess.value = 'processLocationRecords';
                            addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                            occurrenceLocationStore.batchPopulateCollectionLocationRecordGUIDs(collId, () => {
                                processSuccessResponse('Complete');
                                processEventRecords();
                            });
                        }
                        else{
                            processEventRecords();
                        }
                    }

                    function processMediaRecords() {
                        if(processCancelling.value){
                            adjustUIEnd();
                        }
                        else if(nullMediaCnt.value > 0){
                            const text = 'Generating media GUID/UUID values';
                            currentProcess.value = 'processMediaRecords';
                            addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                            mediaStore.batchPopulateCollectionMediaRecordGUIDs(collId, () => {
                                processSuccessResponse('Complete');
                                processDeterminationRecords();
                            });
                        }
                        else{
                            processDeterminationRecords();
                        }
                    }

                    function processMofRecords() {
                        if(processCancelling.value){
                            adjustUIEnd();
                        }
                        else if(nullMofCnt.value > 0){
                            const text = 'Generating MoF GUID/UUID values';
                            currentProcess.value = 'processMofRecords';
                            addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                            collectionStore.batchPopulateCollectionMofRecordGUIDs(() => {
                                processSuccessResponse('Complete');
                                processOccurrenceRecords();
                            });
                        }
                        else{
                            processOccurrenceRecords();
                        }
                    }

                    function processOccurrenceRecords() {
                        if(processCancelling.value){
                            adjustUIEnd();
                        }
                        else if(nullOccurrenceCnt.value > 0){
                            const text = 'Generating occurrence GUID/UUID values';
                            currentProcess.value = 'processOccurrenceRecords';
                            addProcessToProcessorDisplay(getNewProcessObject(currentProcess.value, 'single', text));
                            occurrenceStore.batchPopulateCollectionOccurrenceRecordGUIDs(collId, () => {
                                processSuccessResponse('Complete');
                                adjustUIEnd();
                            });
                        }
                        else{
                            adjustUIEnd();
                        }
                    }

                    function processorDisplayScrollDown() {
                        scrollProcess.value = 'scrollDown';
                        processorDisplayArr.length = 0;
                        processorDisplayCurrentIndex.value++;
                        const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
                        newData.forEach((data) => {
                            processorDisplayArr.push(data);
                        });
                        resetScrollProcess();
                    }

                    function processorDisplayScrollUp() {
                        scrollProcess.value = 'scrollUp';
                        processorDisplayArr.length = 0;
                        processorDisplayCurrentIndex.value--;
                        const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
                        newData.forEach((data) => {
                            processorDisplayArr.push(data);
                        });
                        resetScrollProcess();
                    }

                    function processSuccessResponse(text = null) {
                        const procObj = processorDisplayArr.find(proc => proc['current'] === true);
                        if(procObj){
                            procObj['current'] = false;
                            if(procObj['loading'] === true){
                                procObj['loading'] = false;
                                procObj['result'] = 'success';
                            }
                            if(text){
                                procObj['resultText'] = text;
                            }
                        }
                    }

                    function resetScrollProcess() {
                        setTimeout(() => {
                            scrollProcess.value = null;
                        }, 200);
                    }

                    function setNullGuidCounts() {
                        const formData = new FormData();
                        formData.append('collid', collId);
                        formData.append('includeEventLocation', (includeEventLocation.value ? '1' : '0'));
                        formData.append('includeMof', (includeMof.value ? '1' : '0'));
                        formData.append('action', 'getCollectionNullGuidCounts');
                        fetch(collectionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            nullDeterminationCnt.value = Number(data['determinations']);
                            nullGeneticCnt.value = Number(data['genetic']);
                            nullImageCnt.value = Number(data['image']);
                            nullMediaCnt.value = Number(data['media']);
                            nullOccurrenceCnt.value = Number(data['occurrence']);
                            if(includeEventLocation.value){
                                nullEventCnt.value = Number(data['event']);
                                nullLocationCnt.value = Number(data['location']);
                            }
                            if(includeMof.value){
                                nullMofCnt.value = Number(data['mof']);
                            }
                        });
                    }

                    function setScroller(info) {
                        if((currentProcess.value || scrollProcess.value) && info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                            procDisplayScrollHeight.value = info.verticalSize;
                            if(scrollProcess.value === 'scrollDown'){
                                procDisplayScrollAreaRef.value.setScrollPosition('vertical', 0);
                            }
                            else{
                                procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                            }
                        }
                    }

                    Vue.onMounted(() => {
                        collectionStore.setCollection(collId, () => {
                            if(isEditor.value){
                                setNullGuidCounts();
                            }
                            else{
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                        });
                    });

                    return {
                        clientRoot,
                        collId,
                        collInfo,
                        currentProcess,
                        includeEventLocation,
                        includeMof,
                        isEditor,
                        nullCountsExist,
                        nullDeterminationCnt,
                        nullEventCnt,
                        nullGeneticCnt,
                        nullImageCnt,
                        nullLocationCnt,
                        nullMediaCnt,
                        nullMofCnt,
                        nullOccurrenceCnt,
                        procDisplayScrollAreaRef,
                        processCancelling,
                        processorDisplayArr,
                        processorDisplayCurrentIndex,
                        processorDisplayIndex,
                        cancelProcess,
                        initializeProcess,
                        processorDisplayScrollDown,
                        processorDisplayScrollUp,
                        setScroller
                    }
                }
            });
            guidManagementModule.use(Quasar, { config: {} });
            guidManagementModule.use(Pinia.createPinia());
            guidManagementModule.mount('#mainContainer');
        </script>
    </body>
</html>
