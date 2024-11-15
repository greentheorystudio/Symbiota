<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomic Thesaurus Manager</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .top-tool-container {
                width: 500px;
            }
        </style>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div class="navpath">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
            <b>Taxonomic Thesaurus Manager</b>
        </div>
        <div id="innertext">
            <h1>Taxonomic Thesaurus Manager</h1>
            <template v-if="isEditor">
                <q-card class="top-tool-container q-mb-md">
                    <q-card-section>
                        <div class="q-my-sm">
                            <single-scientific-common-name-auto-complete :sciname="taxonomicGroup" :disabled="loading" label="Enter Taxonomic Group" limit-to-thesaurus="true" accepted-taxa-only="true" rank-low="10" @update:sciname="updateTaxonomicGroup"></single-scientific-common-name-auto-complete>
                        </div>
                        <div class="q-my-sm q-mt-md">
                            <taxon-rank-checkbox-selector :selected-ranks="selectedRanks" :required-ranks="requiredRanks" :kingdom-id="kingdomId" :disable="loading" link-label="Select Taxonomic Ranks" inner-label="Select taxonomic ranks for taxa to be included in import or update" @update:selected-ranks="updateSelectedRanks"></taxon-rank-checkbox-selector>
                        </div>
                    </q-card-section>
                </q-card>
                <q-card>
                    <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                        <q-tab name="importer" label="Data Import/Update" no-caps></q-tab>
                        <q-tab name="fileupload" label="Load Data File" no-caps></q-tab>
                        <q-tab name="maintenance" label="Maintenance Tools" no-caps></q-tab>
                    </q-tabs>
                    <q-separator></q-separator>
                    <q-tab-panels v-model="tab">
                        <q-tab-panel name="importer">
                            <taxonomy-data-source-import-update-module :kingdom-id="kingdomId" :loading="loading" :required-ranks="requiredRanks" :selected-ranks="selectedRanks" :selected-ranks-high="selectedRanksHigh" :taxonomic-group="taxonomicGroup" :taxonomic-group-tid="taxonomicGroupTid" @update:loading="updateLoading"></taxonomy-data-source-import-update-module>
                        </q-tab-panel>
                        <q-tab-panel name="fileupload">
                            <?php include_once(__DIR__ . '/batchloader.php'); ?>
                        </q-tab-panel>
                        <q-tab-panel name="maintenance">
                            <taxonomic-thesaurus-maintenance-module :loading="loading" :selected-ranks="selectedRanks" :taxonomic-group-tid="taxonomicGroupTid" @update:loading="updateLoading"></taxonomic-thesaurus-maintenance-module>
                        </q-tab-panel>
                    </q-tab-panels>
                </q-card>
            </template>
            <template v-else>
                <div class="text-weight-bold">You do not have permissions to access this tool</div>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleLanguageAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxonRankCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxonomyDataSourceBulletSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceImportUpdateModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomicThesaurusMaintenanceModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const taxonomicThesaurusManagerModule = Vue.createApp({
                components: {
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                    'taxon-rank-checkbox-selector': taxonRankCheckboxSelector,
                    'taxonomic-thesaurus-maintenance-module': taxonomicThesaurusMaintenanceModule,
                    'taxonomy-data-source-import-update-module': taxonomyDataSourceImportUpdateModule
                },
                setup() {
                    const store = useBaseStore();
                    const isEditor = Vue.ref(false);
                    const kingdomId = Vue.ref(null);
                    const loading = Vue.ref(false);
                    const requiredRanks = Vue.ref([10]);
                    const selectedRanks = Vue.ref([]);
                    const selectedRanksHigh = Vue.ref(0);
                    const tab = Vue.ref('importer');
                    const taxonomicGroup = Vue.ref(null);
                    const taxonomicGroupTid = Vue.ref(null);

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'Taxonomy');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resData) => {
                                isEditor.value = resData.includes('Taxonomy');
                            });
                        });
                    }

                    function setRankHigh() {
                        selectedRanksHigh.value = 0;
                        selectedRanks.value.forEach((rank) => {
                            if(rank > selectedRanksHigh.value){
                                selectedRanksHigh.value = rank;
                            }
                        });
                    }

                    function updateLoading(value) {
                        loading.value = value;
                    }

                    function updateSelectedRanks(selectedArr) {
                        selectedRanks.value = selectedArr;
                        setRankHigh();
                    }

                    function updateTaxonomicGroup(taxonObj) {
                        taxonomicGroup.value = taxonObj ? taxonObj.sciname : null;
                        taxonomicGroupTid.value = taxonObj ? taxonObj.tid : null;
                        kingdomId.value = taxonObj ? taxonObj.kingdomid : null;
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        selectedRanks.value = store.getTaxonomicRanks;
                        setRankHigh();
                    });
                    
                    return {
                        isEditor,
                        kingdomId,
                        loading,
                        requiredRanks,
                        selectedRanks,
                        selectedRanksHigh,
                        tab,
                        taxonomicGroup,
                        taxonomicGroupTid,
                        updateLoading,
                        updateSelectedRanks,
                        updateTaxonomicGroup
                    }
                }
            });
            taxonomicThesaurusManagerModule.use(Quasar, { config: {} });
            taxonomicThesaurusManagerModule.use(Pinia.createPinia());
            taxonomicThesaurusManagerModule.mount('#innertext');
        </script>
    </body>
</html>
