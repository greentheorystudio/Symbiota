<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/TaxonomyEditorManager.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$tabIndex = array_key_exists('tabindex',$_REQUEST)?(int)$_REQUEST['tabindex']:0;

$loaderObj = new TaxonomyEditorManager();

$isEditor = false;
$status = '';
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomic Thesaurus Manager</title>
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
        <?php
        if($isEditor){
            ?>
            <q-card class="top-tool-container q-mb-md">
                <q-card-section>
                    <div class="q-my-sm">
                        <single-scientific-common-name-auto-complete :sciname="taxonomicGroup" :disable="loading" label="Enter Taxonomic Group" limit-to-thesaurus="true" accepted-taxa-only="true" rank-low="10" @update:sciname="updateTaxonomicGroup"></single-scientific-common-name-auto-complete>
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
            <?php
        }
        else{
            echo '<div style="font-weight:bold;">You do not have permissions to access this tool</div>';
        }
        ?>
    </div>
    <?php
    include(__DIR__ . '/../../footer.php');
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/misc/multipleLanguageAutoComplete.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/singleScientificCommonNameAutoComplete.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonRankCheckboxSelector.js?ver=20230530" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceBulletSelector.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceImportUpdateModule.js?ver=20230618" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomicThesaurusMaintenanceModule.js" type="text/javascript"></script>
    <script>
        const taxonomicThesaurusManagerModule = Vue.createApp({
            data() {
                return {
                    kingdomId: Vue.ref(null),
                    loading: Vue.ref(false),
                    requiredRanks: Vue.ref([10]),
                    selectedRanks: Vue.ref([]),
                    selectedRanksHigh: Vue.ref(0),
                    taxonomicGroup: Vue.ref(null),
                    taxonomicGroupTid: Vue.ref(null)
                }
            },
            components: {
                'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                'taxon-rank-checkbox-selector': taxonRankCheckboxSelector,
                'taxonomic-thesaurus-maintenance-module': taxonomicThesaurusMaintenanceModule,
                'taxonomy-data-source-import-update-module': taxonomyDataSourceImportUpdateModule
            },
            setup() {
                return {
                    tab: Vue.ref('importer')
                }
            },
            mounted() {
                this.selectedRanks = TAXONOMIC_RANKS;
                this.setRankHigh();
            },
            methods: {
                setRankHigh() {
                    this.selectedRanksHigh = 0;
                    this.selectedRanks.forEach((rank) => {
                        if(rank > this.selectedRanksHigh){
                            this.selectedRanksHigh = rank;
                        }
                    });
                },
                updateLoading(value) {
                    this.loading = value;
                },
                updateSelectedRanks(selectedArr) {
                    this.selectedRanks = selectedArr;
                    this.setRankHigh();
                },
                updateTaxonomicGroup(taxonObj) {
                    this.taxonomicGroup = taxonObj;
                    this.taxonomicGroupTid = taxonObj ? taxonObj.tid : null;
                    this.kingdomId = taxonObj ? taxonObj.kingdomid : null;
                }
            }
        });
        taxonomicThesaurusManagerModule.use(Quasar, { config: {} });
        taxonomicThesaurusManagerModule.mount('#innertext');
    </script>
</body>
</html>
