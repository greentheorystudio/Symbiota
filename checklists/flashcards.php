<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid', $_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$pid = array_key_exists('pid', $_REQUEST) ? (int)$_REQUEST['pid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklist Flashcard Game</title>
        <meta name="description" content="Flashcard game for checklists in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script type="text/javascript">
            const CLID = <?php echo $clid; ?>;
            const PID = <?php echo $pid; ?>;
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <template v-if="!temporaryChecklist">
                    <template v-if="Number(pId) > 0">
                        <a :href="(clientRoot + '/projects/index.php')" tabindex="0">Biotic Inventory Projects</a> &gt;&gt;
                        <a :href="(clientRoot + '/projects/project.php?pid=' + pId)" tabindex="0">{{ projectName }}</a> &gt;&gt;
                    </template>
                    <template v-else-if="Number(clId) > 0">
                        <a :href="(clientRoot + '/checklists/index.php')" tabindex="0">Checklists</a> &gt;&gt;
                    </template>
                    <template v-if="Number(clId) > 0">
                        <a :href="(clientRoot + '/checklists/checklist.php?clid=' + clId + '&pid=' + pId)" tabindex="0">Checklist: {{ checklistName }}</a> &gt;&gt;
                    </template>
                </template>
                <template v-else>
                    <a :href="(clientRoot + '/checklists/checklist.php?clid=' + clId + '&pid=' + pId)" tabindex="0">Dynamic Checklist</a> &gt;&gt;
                </template>
                <span class="text-bold">Flashcards</span>
            </div>
            <div class="q-pa-md column">
                <template v-if="Number(clId) > 0 || Number(pId) > 0">
                    <div class="q-mb-md full-width row justify-start q-gutter-sm">
                        <div class="row q-gutter-md">
                            <div>
                                <h1>{{ checklistName }} Flashcard Game</h1>
                            </div>
                        </div>
                    </div>
                    <div class="q-mb-sm full-width">
                        <q-separator ></q-separator>
                    </div>
                    <div class="q-py-sm full-width row">
                        <div class="col-12 col-sm-6">
                            <single-scientific-common-name-auto-complete :sciname="(taxonFilterVal ? taxonFilterVal.sciname : null)" :options="taxaFilterOptions" label="Taxon Filter" limit-to-options="true" @update:sciname="processTaxonFilterValChange"></single-scientific-common-name-auto-complete>
                        </div>
                        <div class="col-12 col-sm-6 row justify-end no-wrap self-center">
                            <div class="q-mr-md text-body1 text-bold">Display:</div>
                            <div>
                                <checkbox-input-element label="Common Names" :value="displayCommonNamesVal" @update:value="processDisplayCommonNameChange"></checkbox-input-element>
                            </div>
                        </div>
                    </div>
                    <div class="q-mb-sm full-width">
                        <q-separator ></q-separator>
                    </div>
                    <div class="q-py-sm full-width row justify-between">
                        <div class="row justify-start q-gutter-md">
                            <div>
                                <span class="q-mr-sm text-h6 text-bold">Correct:</span><span class="text-h6">{{ correctTidArr.length }}</span>
                            </div>
                            <div>
                                <span class="q-mr-sm text-h6 text-bold">Incorrect:</span><span class="text-h6">{{ incorrectTidArr.length }}</span>
                            </div>
                            <div>
                                <span class="q-mr-sm text-h6 text-bold">Points:</span><span class="text-h6">{{ points }}</span>
                            </div>
                        </div>
                        <div class="row justify-end q-gutter-sm">
                            <div>
                                <q-btn color="negative" @click="resetGame();" label="Reset" />
                            </div>
                            <div>
                                <q-btn size="md" icon="far fa-question-circle" stretch flat dense ripple="false" @click="displayInstructionsPopup = true">
                                    <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Show instructions
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </div>
                    </div>
                    <div class="q-mb-sm full-width">
                        <q-separator ></q-separator>
                    </div>
                    <div ref="containerRef" class="q-mt-lg full-width row justify-center">
                        <q-card v-if="currentImage" :style="cardStyle">
                            <q-img class="rounded-borders" :height="cardImageHeight" :src="(currentImage['url'].startsWith('/') ? (clientRoot + currentImage['url']) : currentImage['url'])" fit="scale-down" :no-native-menu="true"></q-img>
                            <q-card-section class="q-pa-sm column q-gutter-sm">
                                <div class="row justify-between">
                                    <div>
                                        <q-btn color="primary" @click="setCurrentTaxon();" label="Skip" />
                                    </div>
                                    <div class="row justify-end q-gutter-xs">
                                        <q-btn round dense color="primary" text-color="white" icon="arrow_left" @click="currentImageIndex--" :disabled="currentImageIndex === 0">
                                            <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Previous image
                                            </q-tooltip>
                                        </q-btn>
                                        <q-btn round dense color="primary" text-color="white" icon="arrow_right" @click="currentImageIndex++" :disabled="(currentImageIndex + 1) === checklistImageData[currentTaxon['tidaccepted']].length">
                                            <q-tooltip anchor="center right" self="center left" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Next image
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                                <div v-if="displayCommonNamesVal && currentTaxon['vernacularData'] && currentTaxon['vernacularData'].length > 0" class="text-body1">
                                    <span class="text-bold">Common names: </span>{{ getVernacularStrFromArr(currentTaxon['vernacularData']) }}
                                </div>
                                <div class="row">
                                    <div class="col-grow">
                                        <single-scientific-common-name-auto-complete :sciname="(familyAnswer ? familyAnswer.sciname : null)" :options="familyAnswerOptions" label="Family" limit-to-options="true" @update:sciname="processFamilyAnswerChange"></single-scientific-common-name-auto-complete>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-grow">
                                        <single-scientific-common-name-auto-complete :sciname="(scinameAnswer ? scinameAnswer.sciname : null)" :options="scinameAnswerOptions" label="Scientific Name" limit-to-options="true" @update:sciname="processScinameAnswerChange"></single-scientific-common-name-auto-complete>
                                    </div>
                                </div>
                                <div class="row justify-between">
                                    <div>
                                        <q-btn color="negative" @click="showCurrentTaxon();" label="Show Me" />
                                    </div>
                                    <div>
                                        <q-btn color="primary" @click="checkAnswers();" label="Check Answer" :disabled="!scinameAnswer && !familyAnswer" />
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                </template>
                <template v-else>
                    <div class="column">
                        <div class="q-pa-md row justify-center text-h6 text-bold">
                            There are no taxa to display
                        </div>
                    </div>
                </template>
            </div>
            <q-dialog class="z-top" v-model="displayInstructionsPopup" persistent>
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="displayInstructionsPopup = false"></q-btn>
                        </div>
                    </div>
                    <div class="q-pa-md text-body1">
                        Enter the scientific name and family of the species in the image.
                        Click the previous and next image buttons to scroll through the images for the current taxon.
                        Click the skip button to skip to the next taxon. Click the Show Me button to give up and open the
                        taxon profile page to show the current taxon. 1 point will be awarded for each correct scientific
                        name and family answer.
                    </div>
                </q-card>
            </q-dialog>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script>
            const checklistModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
                },
                setup() {
                    const baseStore = useBaseStore();
                    const checklistStore = useChecklistStore();
                    const projectStore = useProjectStore();
                    const $q = useQuasar();

                    const activeTaxaArr = Vue.computed(() => {
                        const returnArr = [];
                        taxaDataArr.value.forEach(taxon => {
                            let includeTaxon = false;
                            if(checklistImageData.value.hasOwnProperty(taxon['tidaccepted']) && checklistImageData.value[taxon['tidaccepted']].length > 0){
                                if(taxonFilterVal.value){
                                    if(Number(taxonFilterVal.value['rankid']) === 140 && taxon['family'] === taxonFilterVal.value['sciname']){
                                        includeTaxon = true;
                                    }
                                    else if(Number(taxonFilterVal.value['rankid']) > 140 && (taxon['sciname'] === taxonFilterVal.value['sciname'] || taxon['sciname'].startsWith((taxonFilterVal.value['sciname'] + ' ')))){
                                        includeTaxon = true;
                                    }
                                }
                                else{
                                    includeTaxon = true;
                                }
                            }
                            if(includeTaxon){
                                returnArr.push(taxon);
                            }
                        });
                        returnArr.sort((a, b) => Number(a['tid']) - Number(b['tid']));
                        return returnArr;
                    });
                    const cardStyle = Vue.ref(null);
                    const cardImageHeight = Vue.ref(null);
                    const checklistData = Vue.computed(() => checklistStore.getChecklistData);
                    const checklistImageData = Vue.computed(() => checklistStore.getChecklistImageData);
                    const checklistName = Vue.computed(() => {
                        let returnVal = 'Dynamic Checklist';
                        if(!temporaryChecklist.value && checklistData.value.hasOwnProperty('name') && checklistData.value['name']){
                            returnVal = checklistData.value['name'];
                        }
                        return returnVal;
                    });
                    const clId = Vue.ref(CLID);
                    const clientRoot = baseStore.getClientRoot;
                    const containerRef = Vue.ref(null);
                    const correctTidArr = Vue.ref([]);
                    const currentImage = Vue.computed(() => {
                        return currentTaxon.value ? checklistImageData.value[currentTaxon.value['tidaccepted']][Number(currentImageIndex.value)] : null;
                    });
                    const currentImageIndex = Vue.ref(0);
                    const currentTaxon = Vue.ref(null);
                    const displayCommonNamesVal = Vue.computed(() => checklistStore.getDisplayVernaculars);
                    const displayInstructionsPopup = Vue.ref(false);
                    const familyAnswer = Vue.ref(null);
                    const familyAnswerOptions = Vue.computed(() => {
                        const returnArr = [];
                        unusedTaxaArr.value.forEach(taxon => {
                            const familyObj = returnArr.find(family => family['sciname'] === taxon['family']);
                            if(!familyObj){
                                returnArr.push({
                                    sciname: taxon['family'],
                                    label: taxon['family']
                                });
                            }
                        });
                        returnArr.sort((a, b) => {
                            return a['sciname'].localeCompare(b['sciname']);
                        });
                        return returnArr;
                    });
                    const incorrectTidArr = Vue.ref([]);
                    const pId = Vue.ref(PID);
                    const points = Vue.ref(0);
                    const projectData = Vue.computed(() => projectStore.getProjectData);
                    const projectName = Vue.computed(() => {
                        return projectData.value.hasOwnProperty('projname') ? projectData.value['projname'] : '';
                    });
                    const scinameAnswer = Vue.ref(null);
                    const scinameAnswerOptions = Vue.computed(() => {
                        const returnArr = [];
                        unusedTaxaArr.value.forEach(taxon => {
                            returnArr.push({
                                sciname: taxon['sciname'],
                                label: taxon['sciname'],
                                family: taxon['family'],
                                tid: taxon['tid']
                            });
                        });
                        returnArr.sort((a, b) => {
                            return a['sciname'].localeCompare(b['sciname']);
                        });
                        return returnArr;
                    });
                    const taxaDataArr = Vue.computed(() => checklistStore.getChecklistFlashcardTaxaArr);
                    const taxaFilterOptions = Vue.computed(() => checklistStore.getTaxaFilterOptions);
                    const taxonFilterVal = Vue.computed(() => checklistStore.getDisplayTaxonFilterVal);
                    const temporaryChecklist = Vue.computed(() => {
                        let returnVal = false;
                        if(checklistData.value.hasOwnProperty('clid') && Number(checklistData.value['clid']) > 0 && checklistData.value['expiration']){
                            returnVal = true;
                        }
                        return returnVal;
                    });
                    const unusedTaxaArr = Vue.computed(() => {
                        const returnArr = [];
                        activeTaxaArr.value.forEach(taxon => {
                            if(!correctTidArr.value.includes(Number(taxon['tid'])) && !incorrectTidArr.value.includes(Number(taxon['tid']))){
                                returnArr.push(taxon);
                            }
                        });
                        return returnArr;
                    });

                    Vue.watch(checklistImageData, () => {
                        if(!currentTaxon.value){
                            setCurrentTaxon();
                        }
                    });

                    Vue.watch(containerRef, () => {
                        setContentStyle();
                    });

                    function checkAnswers() {
                        let newPoints = 0;
                        let message = '';
                        if(scinameAnswer.value && scinameAnswer.value['sciname'] === currentTaxon.value['sciname']){
                            if(familyAnswer.value && familyAnswer.value['sciname'] === currentTaxon.value['family']){
                                message = 'Excellent work! You have both the scientific name and family correct. You earned 2 points!';
                                newPoints += 2;
                            }
                            else{
                                message = 'Good job! You have the scientific name correct. The correct family is ' + currentTaxon.value['family'] + '. You earned 1 point!';
                                newPoints++;
                            }
                            correctTidArr.value.push(Number(currentTaxon.value['tid']));
                            showAnswerNotification(true, message);
                        }
                        else{
                            if(familyAnswer.value && familyAnswer.value['sciname'] === currentTaxon.value['family']){
                                if(scinameAnswer.value){
                                    message = 'The scientific name you entered is incorrect. ';
                                }
                                message += 'The correct scientific name is ' + currentTaxon.value['sciname'] + '. You did get the family correct however. You earned 1 point!';
                                newPoints++;
                            }
                            else{
                                message = 'Unfortunately your answer is incorrect. The correct scientific name is ' + currentTaxon.value['sciname'] + ', and the family is ' + currentTaxon.value['family'] + '.';
                            }
                            incorrectTidArr.value.push(Number(currentTaxon.value['tid']));
                            showAnswerNotification(false, message);
                        }
                        points.value += newPoints;
                        setCurrentTaxon();
                    }

                    function getVernacularStrFromArr(vernacularArr) {
                        const nameArr = [];
                        vernacularArr.forEach(vernacular => {
                            if(vernacular['vernacularname']){
                                nameArr.push(vernacular['vernacularname']);
                            }
                        });
                        return nameArr.length > 0 ? nameArr.join(', ') : '';
                    }

                    function processDisplayCommonNameChange(value) {
                        checklistStore.setDisplayVernaculars(value);
                    }

                    function processFamilyAnswerChange(value) {
                        familyAnswer.value = value;
                    }

                    function processScinameAnswerChange(value) {
                        scinameAnswer.value = value;
                    }

                    function processTaxonFilterValChange(taxon) {
                        checklistStore.setDisplayTaxonFilterVal(taxon);
                        resetGame();
                    }

                    function resetGame() {
                        correctTidArr.value.length = 0;
                        incorrectTidArr.value.length = 0;
                        points.value = 0;
                        setCurrentTaxon();
                    }

                    function setChecklistData() {
                        checklistStore.setChecklist(clId.value, (clid) => {
                            if(Number(clid) > 0){
                                checklistStore.setChecklistTaxaArr(false, false, true, () => {
                                    checklistStore.setChecklistFlashcardImageData(3);
                                });
                            }
                        });
                    }

                    function setContentStyle() {
                        cardStyle.value = null;
                        cardImageHeight.value = null;
                        if(containerRef.value){
                            let cardDim;
                            if(containerRef.value.clientWidth > 900){
                                cardDim = (containerRef.value.clientWidth * 0.5);
                            }
                            else if(containerRef.value.clientWidth > 600){
                                cardDim = (containerRef.value.clientWidth * 0.6);
                            }
                            else if(containerRef.value.clientWidth > 400){
                                cardDim = (containerRef.value.clientWidth * 0.7);
                            }
                            else{
                                cardDim = containerRef.value.clientWidth * 0.8;
                            }
                            cardStyle.value = 'width: ' + cardDim + 'px;';
                            cardImageHeight.value = cardDim + 'px';
                        }
                    }

                    function setCurrentTaxon() {
                        scinameAnswer.value = null;
                        familyAnswer.value = null;
                        currentImageIndex.value = 0;
                        currentTaxon.value = null;
                        if(unusedTaxaArr.value.length > 0){
                            const randomIndex = Math.floor(Math.random() * unusedTaxaArr.value.length);
                            currentTaxon.value = Object.assign({}, unusedTaxaArr.value[randomIndex]);
                        }
                    }

                    function setProjectData() {
                        projectStore.setProject(pId.value, (pid) => {
                            if(Number(pid) > 0 && Number(clId.value) === 0){
                                checklistStore.setClidArr(projectData.value['clidArr']);
                                checklistStore.setChecklistTaxaArr(false, false, true, () => {
                                    checklistStore.setChecklistFlashcardImageData(3);
                                });
                            }
                        });
                    }

                    function showAnswerNotification(correct, message) {
                        $q.notify({
                            color: (correct ? 'green' : 'red'),
                            classes: 'text-h6 text-bold',
                            textColor: 'white',
                            message: message,
                            position: 'center',
                            multiLine: true,
                            timeout: 2500
                        })
                    }

                    function showCurrentTaxon() {
                        window.open((clientRoot + '/taxa/index.php?taxon=' + currentTaxon.value['tid']), '_blank');
                        incorrectTidArr.value.push(Number(currentTaxon.value['tid']));
                        setCurrentTaxon();
                    }

                    Vue.onMounted(() => {
                        if(Number(clId.value) > 0 || Number(pId.value) > 0){
                            if(Number(clId.value) > 0){
                                setChecklistData();
                            }
                            if(Number(pId.value) > 0){
                                setProjectData();
                            }
                        }
                        else{
                            window.location.href = (clientRoot + '/checklists/checklist.php');
                        }
                        setContentStyle();
                        window.addEventListener('resize', setContentStyle);
                    });

                    return {
                        cardStyle,
                        cardImageHeight,
                        checklistData,
                        checklistImageData,
                        checklistName,
                        clId,
                        clientRoot,
                        containerRef,
                        correctTidArr,
                        currentImage,
                        currentImageIndex,
                        currentTaxon,
                        displayInstructionsPopup,
                        displayCommonNamesVal,
                        familyAnswer,
                        familyAnswerOptions,
                        incorrectTidArr,
                        pId,
                        points,
                        projectData,
                        projectName,
                        scinameAnswer,
                        scinameAnswerOptions,
                        taxaFilterOptions,
                        taxonFilterVal,
                        temporaryChecklist,
                        checkAnswers,
                        getVernacularStrFromArr,
                        processDisplayCommonNameChange,
                        processFamilyAnswerChange,
                        processScinameAnswerChange,
                        processTaxonFilterValChange,
                        resetGame,
                        setCurrentTaxon,
                        showCurrentTaxon
                    }
                }
            });
            checklistModule.use(Quasar, { config: {} });
            checklistModule.use(Pinia.createPinia());
            checklistModule.mount('#mainContainer');
        </script>
    </body>
</html>
