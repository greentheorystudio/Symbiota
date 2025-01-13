<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid',$_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$pid = array_key_exists('pid',$_REQUEST) ? (int)$_REQUEST['pid'] : 0;
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
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="app-container">
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
                <div class="full-width row q-gutter-sm">
                    <div class="col-4 column q-gutter-sm">
                        <div class="full-width">
                            <q-card flat bordered>
                                <q-card-section class="column q-gutter-xs">
                                    <div class="full-width">
                                        <selector-input-element label="Sort by" :options="sortByOptions" :value="selectedSortByOption" @update:value="processSortByChange"></selector-input-element>
                                    </div>
                                    <div class="full-width">
                                        <checkbox-input-element label="Display Common Names" :value="displayCommonNamesVal" @update:value="processDisplayCommonNameChange"></checkbox-input-element>
                                    </div>
                                    <div class="full-width">
                                        <checkbox-input-element label="Display Images" :value="displayImagesVal" @update:value="processDisplayImagesChange"></checkbox-input-element>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                        <q-separator ></q-separator>
                        <template v-for="heading in keyDataArr">
                            <template v-if="activeChidArr.includes(Number(heading.chid))">
                                <div class="full-width">
                                    <q-card flat bordered>
                                        <q-card-section class="column q-gutter-sm">
                                            <div class="text-h6 text-bold">
                                                {{ heading.headingname }}
                                            </div>
                                            <template v-for="character in heading['characterArr']">
                                                <template v-if="activeCidArr.includes(Number(character.cid))">
                                                    <div class="full-width column q-gutter-xs">
                                                        <div class="text-body1 text-bold">
                                                            {{ character.charactername }}
                                                        </div>
                                                        <template v-for="state in character['stateArr']">
                                                            <div class="full-width">
                                                                <checkbox-input-element :label="state.characterstatename" :value="selectedCsidArr.includes(Number(state.csid)) ? '1' : '0'" @update:value="(value) => processCharacterStateSelectionChange(state, value)"></checkbox-input-element>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </template>
                                        </q-card-section>
                                    </q-card>
                                </div>
                            </template>
                        </template>
                    </div>
                    <div class="col-8 column">

                    </div>
                </div>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script>
            const keyIdentificationModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'selector-input-element': selectorInputElement
                },
                setup() {
                    const baseStore = useBaseStore();

                    const activeChidArr = Vue.computed(() => {
                        const valArr = [];
                        keyDataArr.value.forEach(heading => {
                            if(!heading['characterArr'].every((character) => !activeCidArr.value.includes(Number(character['cid'])))){
                                valArr.push(Number(heading['chid']));
                            }
                        });
                        return valArr;
                    });
                    const activeCidArr = Vue.ref([]);
                    const activeTidArr = Vue.ref([]);
                    const characterDependencyDataArr = Vue.ref([]);
                    const checklistData = Vue.ref({});
                    const checklistName = Vue.computed(() => {
                        return checklistData.value.hasOwnProperty('name') ? checklistData.value['name'] : '';
                    });
                    const clId = CLID;
                    const clidArr = Vue.ref([]);
                    const clientRoot = baseStore.getClientRoot;
                    const csidArr = Vue.ref([]);
                    const displayCommonNamesVal = Vue.ref(false);
                    const displayImagesVal = Vue.ref(false);
                    const keyDataArr = Vue.ref([]);
                    const languageArr = [];
                    const pId = PID;
                    const projectData = Vue.ref({});
                    const projectName = Vue.computed(() => {
                        return projectData.value.hasOwnProperty('projname') ? projectData.value['projname'] : '';
                    });
                    const selectedCidArr = Vue.computed(() => {
                        const valueArr = selectedStateArr.value.length > 0 ? selectedStateArr.value.map(state => Number(state['cid'])) : [];
                        return valueArr.length > 0 ? valueArr.filter((value, index, array) => array.indexOf(value) === index) : [];
                    });
                    const selectedCsidArr = Vue.computed(() => {
                        const valueArr = selectedStateArr.value.length > 0 ? selectedStateArr.value.map(state => Number(state['csid'])) : [];
                        return valueArr.length > 0 ? valueArr.filter((value, index, array) => array.indexOf(value) === index) : [];
                    });
                    const selectedSortByOption = Vue.ref('family');
                    const selectedStateArr = Vue.ref([]);
                    const sortByOptions = Vue.ref([
                        {value: 'family', label: 'Family/Scientific Name'},
                        {value: 'sciname', label: 'Scientific Name'}
                    ]);
                    const taxaDataArr = Vue.ref([]);
                    const tidArr = Vue.ref([]);

                    function processCharacterStateSelectionChange(state, value) {
                        if(Number(value) === 1){
                            selectedStateArr.value.push(state);
                        }
                        else{
                            const index = selectedStateArr.value.indexOf(state);
                            selectedStateArr.value.splice(index, 1);
                        }
                        setActiveCidArr();
                    }

                    function processDisplayCommonNameChange(value) {

                    }

                    function processDisplayImagesChange(value) {

                    }

                    function processKeyData(keyData) {
                        keyData['character-headings'].forEach(heading => {
                            const headingCharacterArr = [];
                            const characterArr = keyData['characters'].filter((character) => Number(character['chid']) === Number(heading['chid']));
                            characterArr.forEach(character => {
                                const characterStateArr = [];
                                const stateArr = keyData['character-states'].filter((state) => Number(state['cid']) === Number(character['cid']));
                                stateArr.forEach(state => {
                                    characterStateArr.push(state);
                                });
                                characterStateArr.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                                character['stateArr'] = characterStateArr.slice();
                                characterDependencyDataArr.value.push({
                                    cid: character['cid'],
                                    dependencies: character['dependencies'].slice()
                                });
                                headingCharacterArr.push(character);
                            });
                            headingCharacterArr.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                            heading['characterArr'] = headingCharacterArr.slice();
                            keyDataArr.value.push(heading);
                        });
                        keyDataArr.value.sort((a, b) => Number(a.sortsequence) - Number(b.sortsequence));
                        setActiveCidArr();
                    }

                    function processSortByChange(value) {
                        selectedSortByOption.value = value;
                    }

                    function processTaxaData() {
                        taxaDataArr.value.forEach(taxon => {
                            if(!tidArr.value.includes(taxon['tid'])){
                                tidArr.value.push(taxon['tid']);
                                activeTidArr.value.push(taxon['tid']);
                            }
                            if(taxon['keyData'].length > 0){
                                taxon['keyData'].forEach(keyData => {
                                    if(!csidArr.value.includes(keyData['csid'])){
                                        csidArr.value.push(keyData['csid']);
                                    }
                                });
                            }
                        });
                        setKeyData();
                    }

                    function setActiveCidArr() {
                        characterDependencyDataArr.value.forEach(character => {
                            if(character['dependencies'].length > 0){
                                let active = false;
                                character['dependencies'].forEach(dep => {
                                    if(!active){
                                        if(Number(dep['csid']) === 0){
                                            if(selectedCidArr.value.includes(Number(dep['cid']))){
                                                active = true;
                                            }
                                        }
                                        else{
                                            if(selectedCsidArr.value.includes(Number(dep['csid']))){
                                                active = true;
                                            }
                                        }
                                    }
                                });
                                if(active && !activeCidArr.value.includes(Number(character['cid']))){
                                    activeCidArr.value.push(Number(character['cid']));
                                }
                                else if(!active){
                                    if(activeCidArr.value.includes(Number(character['cid']))){
                                        const index = activeCidArr.value.indexOf(Number(character['cid']));
                                        activeCidArr.value.splice(index, 1);
                                    }
                                    if(selectedCidArr.value.includes(Number(character['cid']))){
                                        const targetStateArr = selectedStateArr.value.filter((state) => Number(state['cid']) === Number(character['cid']));
                                        targetStateArr.forEach(state => {
                                            const index = selectedStateArr.value.indexOf(state);
                                            selectedStateArr.value.splice(index, 1);
                                        });
                                    }
                                }
                            }
                            else if(!activeCidArr.value.includes(Number(character['cid']))){
                                activeCidArr.value.push(Number(character['cid']));
                            }
                        });
                    }

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
                            clidArr.value = checklistData.value['clidArr'].slice();
                            setTaxaData();
                        });
                    }

                    function setKeyData() {
                        const formData = new FormData();
                        formData.append('csidArr', JSON.stringify(csidArr.value));
                        formData.append('includeFullKeyData', '1');
                        formData.append('action', 'getKeyCharacterStatesArr');
                        fetch(keyCharacterStateApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            processKeyData(data);
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
                            clidArr.value = Object.values(projectData.value['clidArr']).slice();
                            setTaxaData();
                        });
                    }

                    function setTaxaData() {
                        const formData = new FormData();
                        formData.append('clidArr', JSON.stringify(clidArr.value));
                        formData.append('includeKeyData', '1');
                        formData.append('action', 'getChecklistTaxa');
                        fetch(checklistTaxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            taxaDataArr.value = data;
                            processTaxaData();
                        });
                    }

                    Vue.onMounted(() => {
                        if(Number(clId) > 0){
                            setChecklistData();
                        }
                        else if(Number(pId) > 0){
                            setProjectData();
                        }
                    });

                    return {
                        activeChidArr,
                        activeCidArr,
                        activeTidArr,
                        checklistData,
                        checklistName,
                        clId,
                        clientRoot,
                        displayCommonNamesVal,
                        displayImagesVal,
                        keyDataArr,
                        languageArr,
                        pId,
                        projectData,
                        projectName,
                        selectedCsidArr,
                        selectedSortByOption,
                        selectedStateArr,
                        sortByOptions,
                        processCharacterStateSelectionChange,
                        processDisplayCommonNameChange,
                        processDisplayImagesChange,
                        processSortByChange
                    }
                }
            });
            keyIdentificationModule.use(Quasar, { config: {} });
            keyIdentificationModule.use(Pinia.createPinia());
            keyIdentificationModule.mount('#app-container');
        </script>
    </body>
</html>

