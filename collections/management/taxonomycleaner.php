<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceTaxonomyCleaner.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid',$_REQUEST)?(int)$_REQUEST['collid']:0;

$cleanManager = new OccurrenceTaxonomyCleaner();
$cleanManager->setCollId($collid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || (isset($GLOBALS['USER_RIGHTS']['CollAdmin']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
	$isEditor = true;
}
$collMap = $cleanManager->getCollMap();
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Management Module</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <style>
        .header-block {
            display: flex;
            justify-content: space-between;
            margin: 0 30px 8px;
        }
        .icon-link {
            cursor: pointer;
        }
        .processor-container {
            width: 95%;
            margin: 20px auto;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .processor-control-container {
            width: 40%;
            height: 650px;
        }
        .processor-display-container {
            width: 50%;
            height: 650px;
        }
        .processor-display {
            height: 610px;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .process-button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            clear: both;
            margin-top: 5px;
        }
        .accordion-panel {
            max-height: 520px;
            overflow: auto;
        }
        .process-header {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .fuzzy-match-row {
            display: flex;
            justify-content: space-between;
        }
    </style>
    <script src="../../js/collections.taxonomytools.js?ver=20230117" type="text/javascript"></script>
    <script>
        const collId = <?php echo $collid; ?>;
        const processStatus = '<span class="current-status">' + getSmallWorkingSpinnerHtml(11) + '</span>';

        function clearSubprocesses(id){
            const parentProcObj = processorDisplayArr.value.find(proc => proc['id'] === id);
            parentProcObj['subs'] = [];
        }
    </script>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    ?>
    <div class='navpath'>
        <a href="../../index.php">Home</a> &gt;&gt;
        <?php
        if($collid && is_numeric($collid)){
            ?>
            <a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Control Panel</a> &gt;&gt;
            <?php
        }
        ?>
        <b>Taxonomy Management Module</b>
    </div>
    <div id="innertext">
        <?php
        if($collid && $isEditor){
            ?>
            <div class="header-block">
                <div class="text-weight-bold">
                    <?php echo $collMap[(int)$collid]['collectionname'].($collMap[(int)$collid]['code']?' ('.$collMap[(int)$collid]['code'].')':''); ?>
                </div>
                <div onclick="openTutorialWindow('../../tutorial/collections/management/taxonomy/index.php?collid=<?php echo $collid; ?>');" title="Open Tutorial Window">
                    <q-icon name="far fa-question-circle" size="20px" class="icon-link" />
                </div>
            </div>
            <div class="header-block">
                <div class="text-weight-bold">
                    <div class="q-mt-xs">
                        <taxa-kingdom-selector :disable="upperdisabled"></taxa-kingdom-selector>
                    </div>
                    <div class="q-mt-xs">
                        <q-input outlined v-model="startIndex" label="Processing Start Index" style="width:250px;" :readonly="upperdisabled" dense />
                    </div>
                    <div class="q-mt-xs">
                        <q-input outlined v-model="batchLimit" label="Processing Batch Limit" style="width:175px;" @update:model-value="processingBatchLimitChange" :readonly="upperdisabled" dense />
                    </div>
                </div>
                <div class="text-weight-bold">
                    Occurrences not linked to taxonomic thesaurus: {{ unlinkedOccCnt }}<q-spinner v-if="unlinkedLoading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner><br/>
                    Unique scientific names: {{ unlinkedTaxaCnt }}<q-spinner v-if="unlinkedLoading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner><br/>
                </div>
            </div>
            <div class="processor-container">
                <div class="processor-control-container">
                    <q-card>
                        <q-list>
                            <q-expansion-item group="controlgroup" label="Maintenance Utilities" header-class="bg-grey-3 text-bold" default-opened>
                                <q-card class="accordion-panel">
                                    <q-card-section>
                                        <div class="process-header">
                                            General Cleaning
                                        </div>
                                        Run cleaning processes to remove unnecessary endings, identification qualifiers and question marks, and normalize
                                        infraspecific rank references in occurrence record scientific names that are not linked to
                                        the Taxonomic Thesaurus.
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currProcess === 'cleanProcesses'" :disabled="currProcess && currProcess !== 'cleanProcesses'" color="secondary" @click="callCleaningController('question-marks');" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currProcess === 'cleanProcesses'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                        <div class="process-header">
                                            Scientific Name Authorship Cleaning
                                        </div>
                                        Run a cleaning process to remove the scientific name authors from occurrence record scientific
                                        names that are not linked to the Taxonomic Thesaurus.
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currProcess === 'cleanScinameAuthor'" :disabled="currProcess && currProcess !== 'cleanScinameAuthor'" color="secondary" @click="initializeCleanScinameAuthor();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currProcess === 'cleanScinameAuthor'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                        <div class="process-header">
                                            Set Taxonomic Thesaurus Linkages
                                        </div>
                                        Set occurrence record linkages to the Taxonomic Thesaurus.
                                        <div class="q-mt-xs">
                                            <q-checkbox v-model="updatedet" label="Include associated determination records" :disable="upperdisabled" />
                                        </div>
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currProcess === 'updateWithTaxThesaurus'" :disabled="currProcess && currProcess !== 'updateWithTaxThesaurus'" color="secondary" @click="callTaxThesaurusLinkController();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currProcess === 'updateWithTaxThesaurus'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                        <div class="process-header">
                                            Update Locality Security Settings
                                        </div>
                                        Update locality security settings for occurrence records of protected species.
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currProcess === 'updateOccLocalitySecurity'" :disabled="currProcess && currProcess !== 'updateOccLocalitySecurity'" color="secondary" @click="updateOccLocalitySecurity();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currProcess === 'updateOccLocalitySecurity'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                    </q-card-section>
                                </q-card>
                            </q-expansion-item>
                            <q-separator></q-separator>
                            <q-expansion-item group="controlgroup" label="Search Utilities" header-class="bg-grey-3 text-bold">
                                <q-card class="accordion-panel">
                                    <q-card-section>
                                        <div class="process-header">
                                            Search Taxonomic Data Sources
                                        </div>
                                        <div class="q-mb-sm">
                                            Search for occurrence record scientific names that are not currently linked to the Taxonomic Thesaurus
                                            from an external Taxonomic Data Source.
                                        </div>
                                        <div class="q-mb-sm">
                                            <taxonomy-data-source-bullet-selector></taxonomy-data-source-bullet-selector>
                                        </div>
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currProcess === 'resolveFromTaxaDataSource'" :disabled="currProcess && currProcess !== 'resolveFromTaxaDataSource'" color="secondary" @click="initializeDataSourceSearch();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currProcess === 'resolveFromTaxaDataSource'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                        <div class="process-header">
                                            Taxonomic Thesaurus Fuzzy Search
                                        </div>
                                        Get fuzzy matches to occurrence record scientific names that are not yet linked to the Taxonomic Thesaurus
                                        with taxa currently in the Taxonomic Thesaurus.
                                        <div class="q-mt-xs">
                                            <q-input outlined v-model="levVal" style="width:225px;" label="Character difference tolerance" :readonly="upperdisabled" dense />
                                        </div>
                                        <div class="process-button-container">
                                            <div>
                                                <q-btn :loading="currProcess === 'taxThesaurusFuzzyMatch'" :disabled="currProcess && currProcess !== 'taxThesaurusFuzzyMatch'" color="secondary" @click="initializeTaxThesaurusFuzzyMatch();" label="Start" dense />
                                            </div>
                                            <div>
                                                <q-btn v-if="currProcess === 'taxThesaurusFuzzyMatch'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                            </div>
                                        </div>
                                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                                    </q-card-section>
                                </q-card>
                            </q-expansion-item>
                        </q-list>
                    </q-card>
                </div>

                <div class="processor-display-container">
                    <occurrence-taxonomy-manager-processor-display></occurrence-taxonomy-manager-processor-display>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    include(__DIR__ . '/../../footer.php');
    include_once(__DIR__ . '/../../config/footer-includes.php');
    ?>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxaKingdomSelector.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonomyDataSourceBulletSelector.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrence/occurrenceTaxonomyManagerProcessorDisplay.js" type="text/javascript"></script>
    <script>
        let uppercontrolsdisabled = Vue.ref(false);
        let unlinkedLoading = Vue.ref(false);
        let unlinkedOccCnt = Vue.ref(null);
        let unlinkedTaxaCnt = Vue.ref(null);
        let processingStartIndex = Vue.ref(null);
        let processingLimit = Vue.ref(null);
        let updatedet = Vue.ref(false);
        let levValue = Vue.ref('2');
        let currentProcess = Vue.ref(null);

        const occurrenceTaxonomyManagementModule = Vue.createApp({
            data() {
                return {
                    currProcess: currentProcess,
                    upperdisabled: uppercontrolsdisabled,
                    unlinkedLoading: unlinkedLoading,
                    unlinkedOccCnt: unlinkedOccCnt,
                    unlinkedTaxaCnt: unlinkedTaxaCnt,
                    startIndex: processingStartIndex,
                    batchLimit: processingLimit,
                    updatedet: updatedet,
                    levVal: levValue
                }
            },
            components: {
                'taxa-kingdom-selector': taxaKingdomSelector,
                'taxonomy-data-source-bullet-selector': taxonomyDataSourceBulletSelector,
                'occurrence-taxonomy-manager-processor-display': occurrenceTaxonomyManagerProcessorDisplay
            },
            mounted() {
                setUnlinkedRecordCounts();
            },
            methods: {
                processingBatchLimitChange(value) {
                    if(value && (isNaN(value) || Number(value) <= 0)){
                        alert('Processing batch limit must be a number greater than zero.');
                        processingLimit.value = null;
                    }
                },
                callCleaningController,
                cancelProcess,
                initializeCleanScinameAuthor,
                callTaxThesaurusLinkController,
                updateOccLocalitySecurity,
                initializeDataSourceSearch,
                initializeTaxThesaurusFuzzyMatch,
                setUnlinkedRecordCounts
            }
        });
        occurrenceTaxonomyManagementModule.use(Quasar, { config: {} });
        occurrenceTaxonomyManagementModule.mount('#innertext');
    </script>
</body>
</html>
