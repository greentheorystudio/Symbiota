<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$tId = array_key_exists('tid', $_REQUEST) ? (int)$_REQUEST['tid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxa Character State Management</title>
        <meta name="description" content="Taxa character state management module for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <style>
            .sticky-table td:first-child {
                /* bg color is important for td; just specify one */
                background-color: white;
            }
            .sticky-table th {
                border-top: 2px solid black;
            }
            .sticky-table tbody {
                border-bottom: 2px solid black;
            }
            .sticky-table tr th {
                position: sticky;
                /* higher than z-index for td below */
                z-index: 2;
                /* bg color is important; just specify one */
                background: white;
            }
            .sticky-table thead tr:last-child th {
                /* height of all previous header rows */
                top: 48px;
                /* highest z-index */
                z-index: 3;
            }
            .sticky-table thead tr:first-child th {
                top: 0;
                z-index: 1;
            }
            .sticky-table tr:first-child th:first-child {
                /* highest z-index */
                z-index: 3;
            }
            .sticky-table td:first-child {
                z-index: 1;
            }
            .sticky-table td:first-child, .sticky-table th:first-child {
                position: sticky;
                left: 0;
            }
            .sticky-table tbody {
                /* height of all previous header rows */
                scroll-margin-top: 48px;
            }
        </style>
    </head>
    <body class="full-window-mode">
        <a class="screen-reader-only" href="#tableContainer">Skip to main content</a>
        <div id="tableContainer">
            <q-table class="sticky-table" :style="tableStyle" flat bordered :rows="tableRowArr" :columns="columnHeaderArr" row-key="index" virtual-scroll v-model:pagination="pagination" :rows-per-page-options="[0]" separator="cell">
                <template v-slot:top>
                    <div class="full-width column q-gutter-sm">
                        <div class="q-mb-sm">
                            <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                            <span class="text-bold">Taxa Character State Management</span>
                        </div>
                        <div class="row justify-start q-gutter-md">
                            <div class="col-4">
                                <single-scientific-common-name-auto-complete :sciname="taxonomicGroupName" label="Taxonomic Group" :limit-to-options="true" @update:sciname="processTaxonomicGroupChange"></single-scientific-common-name-auto-complete>
                            </div>
                            <div>
                                <checkbox-input-element label="Include all subtaxa" :value="includeAllSubtaxa" @update:value="processIncludeAllSubtaxaChange"></checkbox-input-element>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:bottom>
                    Bottom
                </template>
            </q-table>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const taxaCharacterStateManagementModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
                },
                setup() {
                    const { hideWorking, showWorking } = useCore();
                    const baseStore = useBaseStore();

                    const characterArr = Vue.ref([]);
                    const characterStateData = Vue.ref({});
                    const characterStateLoadIndex = Vue.ref(1);
                    const clientRoot = baseStore.getClientRoot;
                    const columnHeaderArr = Vue.computed(() => {
                        const returnArr = [
                            {
                                name: 'sciname',
                                label: 'Scientific Name',
                                field: 'sciname',
                                sortable: true
                            }
                        ];
                        if(characterArr.value.length > 0){
                            characterArr.value.forEach((character) => {
                                returnArr.push({
                                    name: character['cid'].toString(),
                                    label: character['charactername'],
                                    field: character['cid'].toString(),
                                    sortable: true
                                });
                            });
                        }
                        return returnArr;
                    });
                    const includeAllSubtaxa = Vue.ref(false);
                    const isEditor = Vue.ref(false);
                    const pagination = Vue.ref({
                        rowsPerPage: 0
                    });
                    const tableRowArr = Vue.computed(() => {
                        const returnArr = [];
                        taxaArr.value.forEach((taxon) => {
                            const charStateData = characterStateData.value.hasOwnProperty(taxon['tid'].toString()) ? characterStateData.value[taxon['tid'].toString()] : {};
                            const rowData = {
                                tid: taxon['tid'],
                                sciname: taxon['sciname']
                            };
                            characterArr.value.forEach((character) => {
                                rowData[character['cid'].toString()] = charStateData.hasOwnProperty(character['cid'].toString()) ? getCharacterStateStrFromData(charStateData[character['cid'].toString()]) : '[No Data]';
                            });
                            returnArr.push(rowData);
                        });
                        return returnArr;
                    });
                    const tableStyle = Vue.ref('');
                    const taxaArr = Vue.ref([]);
                    const taxaLoadIndex = Vue.ref(1);
                    const taxonomicGroupId = Vue.ref(null);
                    const taxonomicGroupName = Vue.ref(null);
                    const tidArr = Vue.computed(() => {
                        const returnArr = [];
                        taxaArr.value.forEach((taxon) => {
                            returnArr.push(taxon['tid']);
                        });
                        return returnArr;
                    });

                    function clearTaxaData() {
                        characterStateData.value = Object.assign({}, {});
                        characterStateLoadIndex.value = 1;
                        taxaArr.value.length = 0;
                        taxaLoadIndex.value = 1;
                    }

                    function getCharacterStateStrFromData(data) {
                        const strArr = [];
                        data.forEach((state) => {
                            strArr.push(state['characterstatename']);
                        });
                        return strArr.join(', ');
                    }

                    function loadCharacterStateData() {
                        const loadingCnt = 200;
                        const currentTidArr = tidArr.value.length > 0 ? tidArr.value.slice((characterStateLoadIndex.value - 1), loadingCnt) : [];
                        if(currentTidArr.length > 0){
                            const formData = new FormData();
                            formData.append('tidArr', JSON.stringify(currentTidArr));
                            formData.append('action', 'getCharacterStatesFromTidArr');
                            fetch(keyCharacterStateApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((resData) => {
                                characterStateData.value = Object.assign({ ...characterStateData.value, ...resData }, {});
                                characterStateLoadIndex.value++;
                                loadCharacterStateData();
                            });
                        }
                        else{
                            hideWorking();
                        }
                    }

                    function processIncludeAllSubtaxaChange(value) {
                        clearTaxaData();
                        includeAllSubtaxa.value = (Number(value) === 1);
                        processSetTaxaArr();
                    }

                    function processSetTaxaArr() {
                        if(Number(taxonomicGroupId.value) > 0){
                            showWorking();
                            if(includeAllSubtaxa.value){
                                setAllSubtaxaTaxaArr();
                            }
                            else{
                                setDirectChildTaxaArr();
                            }
                        }
                    }

                    function processTaxonomicGroupChange(taxonData) {
                        clearTaxaData();
                        if(taxonData){
                            taxonomicGroupId.value = taxonData['tid'];
                            taxonomicGroupName.value = taxonData['sciname'];
                            processSetTaxaArr();
                        }
                        else{
                            taxonomicGroupId.value = null;
                            taxonomicGroupName.value = null;
                        }
                    }

                    function setAllSubtaxaTaxaArr() {
                        const formData = new FormData();
                        formData.append('parenttid', taxonomicGroupId.value.toString());
                        formData.append('index', taxaLoadIndex.value.toString());
                        formData.append('action', 'getAcceptedTaxaByTaxonomicGroup');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            if(resData.length > 0){
                                taxaArr.value = taxaArr.value.concat(resData);
                            }
                            if(resData.length < 50000){
                                loadCharacterStateData();
                            }
                            else{
                                taxaLoadIndex.value++;
                                setAllSubtaxaTaxaArr();
                            }
                        });
                    }

                    function setCharacterArr() {
                        const formData = new FormData();
                        formData.append('action', 'getCharacterArr');
                        fetch(keyCharacterApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            characterArr.value = resData.slice();
                        });
                    }

                    function setDirectChildTaxaArr() {
                        const formData = new FormData();
                        formData.append('parenttid', taxonomicGroupId.value.toString());
                        formData.append('action', 'getAcceptedChildTaxaByParentTid');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            taxaArr.value = resData.slice();
                            loadCharacterStateData();
                        });
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'KeyAdmin');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isEditor.value = resData.includes('KeyAdmin');
                            if(!isEditor.value){
                                window.location.href = clientRoot + '/index.php';
                            }
                        });
                    }

                    function setTableStyle() {
                        tableStyle.value = 'width: ' + window.innerWidth + 'px; height: ' + window.innerHeight + 'px;';
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        window.addEventListener('resize', setTableStyle);
                        setTableStyle();
                        setCharacterArr();
                    });
                    
                    return {
                        clientRoot,
                        columnHeaderArr,
                        includeAllSubtaxa,
                        isEditor,
                        pagination,
                        tableRowArr,
                        tableStyle,
                        taxonomicGroupName,
                        processIncludeAllSubtaxaChange,
                        processTaxonomicGroupChange
                    }
                }
            });
            taxaCharacterStateManagementModule.use(Quasar, { config: {} });
            taxaCharacterStateManagementModule.use(Pinia.createPinia());
            taxaCharacterStateManagementModule.mount('#tableContainer');
        </script>
    </body>
</html>
