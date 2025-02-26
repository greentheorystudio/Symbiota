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
        <title>Protected Species</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="app-container">
            <div id="innertext">
                <div class="column q-gutter-md">
                    <div class="row justify-between">
                        <div class="text-h6 text-bold">Protected Species</div>
                        <q-btn v-if="isEditor" color="secondary" @click="setOccurrenceSecurity();" label="Secure occurrence data" dense />
                    </div>
                    <template v-if="isEditor">
                        <q-card flat bordered>
                            <q-card-section class="column q-gutter-sm">
                                <div class="text-body1 text-bold">Add new taxon or taxonomic group</div>
                                <div class="row q-gutter-xs">
                                    <div class="col-5">
                                        <taxa-kingdom-selector :selected-kingdom="selectedKingdom" label="Select Kingdom" @update:selected-kingdom="(value) => selectedKingdom = value"></taxa-kingdom-selector>
                                    </div>
                                    <div class="col-6">
                                        <single-scientific-common-name-auto-complete :disabled="!selectedKingdom" :kingdom-id="selectedKingdom ? selectedKingdom['id'] : null" :sciname="selectedTaxon ? selectedTaxon['sciname'] : null" label="Taxon or Taxonomic Group" :accepted-taxa-only="true" :limit-to-thesaurus="true" @update:sciname="(value) => selectedTaxon = value"></single-scientific-common-name-auto-complete>
                                    </div>
                                </div>
                                <div class="row justify-between">
                                    <div>
                                        <checkbox-input-element label="Add subtaxa" :value="addSubtaxaValue" @update:value="(value) => addSubtaxaValue = value"></checkbox-input-element>
                                    </div>
                                    <div>
                                        <q-btn color="secondary" @click="addTaxon();" label="Add" :disabled="!selectedTaxon" dense />
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </template>
                    <template v-if="taxaDisplayData.length > 0">
                        <template v-if="kingdomArr.length > 1">
                            <template v-for="kingdom in kingdomArr">
                                <q-card flat bordered>
                                    <q-card-section class="column q-gutter-sm">
                                        <div class="text-body1 text-bold">{{ kingdom }}</div>
                                        <template v-for="family in taxaDisplayData">
                                            <template v-if="family['kingdom'] === kingdom">
                                                <div class="q-pl-md column q-gutter-xs">
                                                    <div v-if="family['familyName'] !== 'noFamily'" class="text-body1 text-bold">{{ family['familyName'] }}</div>
                                                    <div class="q-pl-md column q-gutter-xs">
                                                        <template v-for="taxon in family['taxa']">
                                                            <div class="row">
                                                                <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon['tid'])" class="row" target="_blank">
                                                                    <div class="text-italic">
                                                                        {{ taxon['sciname'] }}
                                                                    </div>
                                                                    <div class="q-ml-sm">
                                                                        {{ taxon['author'] }}
                                                                    </div>
                                                                </a>
                                                                <div v-if="isEditor" class="q-ml-sm">
                                                                    <q-btn color="white" text-color="black" size="sm" @click="removeTaxon(taxon['tid']);" icon="far fa-trash-alt" dense>
                                                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                            Remove taxon
                                                                        </q-tooltip>
                                                                    </q-btn>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                    </q-card-section>
                                </q-card>
                            </template>
                        </template>
                        <template v-else>
                            <q-card flat bordered>
                                <q-card-section class="column q-gutter-sm">
                                    <template v-for="family in taxaDisplayData">
                                        <div class="q-pl-md column">
                                            <div v-if="family['familyName'] !== 'noFamily'" class="text-body1 text-bold">{{ family['familyName'] }}</div>
                                            <div class="q-pl-md column q-gutter-xs">
                                                <template v-for="taxon in family['taxa']">
                                                    <div class="row">
                                                        <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon['tid'])" class="row" target="_blank">
                                                            <div class="text-italic">
                                                                {{ taxon['sciname'] }}
                                                            </div>
                                                            <div class="q-ml-sm">
                                                                {{ taxon['author'] }}
                                                            </div>
                                                        </a>
                                                        <div v-if="isEditor" class="q-ml-sm">
                                                            <q-btn color="white" text-color="black" size="sm" @click="removeTaxon(taxon['tid']);" icon="far fa-trash-alt" dense>
                                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                    Remove taxon
                                                                </q-tooltip>
                                                            </q-btn>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </q-card-section>
                            </q-card>
                        </template>
                    </template>
                    <template v-else>
                        <div class="text-body1 text-bold">There are no taxa currently listed for protection.</div>
                    </template>
                </div>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxaKingdomSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const protectedTaxaModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                    'taxa-kingdom-selector': taxaKingdomSelector
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();

                    const addSubtaxaValue = Vue.ref(false);
                    const clientRoot = baseStore.getClientRoot;
                    const isEditor = Vue.ref(false);
                    const kingdomArr = Vue.ref([]);
                    const selectedKingdom = Vue.ref(null);
                    const selectedTaxon = Vue.ref(null);
                    const taxaArr = Vue.ref([]);
                    const taxaDisplayData = Vue.ref([]);

                    function addTaxon() {
                        showWorking();
                        const formData = new FormData();
                        formData.append('tid', selectedTaxon.value['tid'].toString());
                        formData.append('includeSubtaxa', (addSubtaxaValue.value ? '1' : '0'));
                        formData.append('action', 'setSecurityForTaxonOrTaxonomicGroup');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            hideWorking();
                            if(Number(res) === 1){
                                showNotification('positive', 'Added successfully');
                                setTaxaData();
                            }
                            else{
                                showNotification('negative','An error occurred');
                            }
                        });
                    }

                    function processTaxaData() {
                        taxaArr.value.forEach((taxon) => {
                            let familyData;
                            const familyName = taxon['family'] ? taxon['family'] : 'noFamily'
                            if(!kingdomArr.value.includes(taxon['kingdom'])){
                                kingdomArr.value.push(taxon['kingdom']);
                            }
                            familyData = taxaDisplayData.value.find(family => (family['familyName'] === familyName && family['kingdom'] === taxon['kingdom']));
                            if(!familyData){
                                const newData = {familyName: familyName, kingdom: taxon['kingdom'], taxa: []};
                                taxaDisplayData.value.push(newData);
                                familyData = taxaDisplayData.value.find(family => (family['familyName'] === familyName && family['kingdom'] === taxon['kingdom']));
                            }
                            familyData['taxa'].push(taxon);
                        });
                        kingdomArr.value.sort((a, b) => {
                            return a.toLowerCase().localeCompare(b.toLowerCase());
                        });
                        taxaDisplayData.value.sort((a, b) => {
                            if(a['familyName'] === 'noFamily'){
                                return -1;
                            }
                            else{
                                return a['familyName'].toLowerCase().localeCompare(b['familyName'].toLowerCase());
                            }
                        });
                        taxaDisplayData.value.forEach((family) => {
                            family['taxa'].sort((a, b) => {
                                return a['sciname'].toLowerCase().localeCompare(b['sciname'].toLowerCase());
                            });
                        });
                    }

                    function removeTaxon(tid) {
                        showWorking();
                        const formData = new FormData();
                        formData.append('tid', tid.toString());
                        formData.append('action', 'removeSecurityForTaxon');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            hideWorking();
                            if(Number(res) === 1){
                                showNotification('positive', 'Removed taxon successfully');
                                setTaxaData();
                            }
                            else{
                                showNotification('negative','An error occurred while removing taxon');
                            }
                        });
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'RareSppAdmin');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isEditor.value = resData.includes('RareSppAdmin');
                        });
                    }

                    function setOccurrenceSecurity() {
                        showWorking();
                        const formData = new FormData();
                        formData.append('action', 'updateLocalitySecurity');
                        fetch(occurrenceApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            hideWorking();
                            showNotification('positive',('Security set for ' + res + ' occurrence records'));
                        });
                    }

                    function setTaxaData() {
                        kingdomArr.value.length = 0;
                        taxaArr.value.length = 0;
                        taxaDisplayData.value.length = 0;
                        const formData = new FormData();
                        formData.append('action', 'getProtectedTaxaArr');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            taxaArr.value = data;
                            if(taxaArr.value.length > 0){
                                processTaxaData();
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        setTaxaData();
                    });

                    return {
                        addSubtaxaValue,
                        clientRoot,
                        isEditor,
                        kingdomArr,
                        selectedKingdom,
                        selectedTaxon,
                        taxaArr,
                        taxaDisplayData,
                        addTaxon,
                        removeTaxon,
                        setOccurrenceSecurity
                    }
                }
            });
            protectedTaxaModule.use(Quasar, { config: {} });
            protectedTaxaModule.use(Pinia.createPinia());
            protectedTaxaModule.mount('#app-container');
        </script>
    </body>
</html>
