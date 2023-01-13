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
            border: 2px #aaaaaa solid;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        .processor-display-container {
            width: 50%;
            height: 650px;
            padding: 15px;
            overflow-x: hidden;
            overflow-y: auto;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            border: 2px black solid;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
        }
        .processor-display {
            height: 610px;
            margin: auto;
            padding: 15px;
            overflow-x: hidden;
            overflow-y: auto;
            border: 1px black solid;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .processor-display ul {
            padding-left: 15px;
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
        .success-status {
            display: block;
            color: green;
            font-weight: bold;
        }
        .error-status {
            display: block;
            color: red;
            font-weight: bold;
        }
        .current-status {
            margin-left: 10px;
        }
        .current-status {
            margin-left: 10px;
        }
        ul.processor-display-list li.first-indent {
            margin-left: 15px;
            list-style-type: none;
        }
        .fuzzy-select-button-li {
            margin-top: 10px;
        }
        .undo-button, .fuzzy-skip-button-li {
            margin-bottom: 5px;
        }
        .fuzzy-match {
            font-weight: bold;
        }
        .fuzzy-select-button {
            margin-left: 15px;
        }
        .process-header {
            margin-bottom: 5px;
            font-weight: bold;
        }
    </style>
    <script src="../../js/collections.taxonomytools.js?ver=202301122" type="text/javascript"></script>
    <script>
        const collId = <?php echo $collid; ?>;
        const processStatus = '<span class="current-status">' + getSmallWorkingSpinnerHtml(11) + '</span>';

        document.addEventListener("DOMContentLoaded", function() {
            setKingdomSelector();
            setUnlinkedRecordCounts();
        });
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
        $collMap = $cleanManager->getCollMap();
        if($collid && $isEditor){
            ?>
            <div id="module-header" style="display:flex;justify-content: space-between;margin-bottom:8px;">
                <div style="font-weight: bold;margin-left:30px;">
                    <?php echo $collMap[(int)$collid]['collectionname'].($collMap[(int)$collid]['code']?' ('.$collMap[(int)$collid]['code'].')':''); ?>
                </div>
                <div onclick="openTutorialWindow('../../tutorial/collections/management/taxonomy/index.php?collid=<?php echo $collid; ?>');" title="Open Tutorial Window">
                    <q-icon name="far fa-question-circle" size="20px" class="icon-link" />
                </div>
            </div>
            <div style="display:flex;justify-content: space-between;margin-bottom:8px;">
                <div style="margin-left:30px;font-weight:bold;">
                    <div id="upper-controls">
                        <div style="margin-top:5px;">
                            <q-select outlined v-model="kingdom" id="targetkingdomselect" :options="kingdomOpts" label="Target Kingdom" @update:model-value="setKingdomId" :readonly="upperdisabled" dense />
                        </div>
                        <div style="margin-top:5px;">
                            <q-input outlined v-model="startIndex" label="Processing Start Index" style="width:250px;" :readonly="upperdisabled" dense />
                        </div>
                        <div style="margin-top:5px;">
                            <q-input outlined v-model="batchLimit" label="Processing Batch Limit" style="width:175px;" @update:model-value="processingBatchLimitChange" :readonly="upperdisabled" dense />
                        </div>
                    </div>
                </div>
                <div style="margin-right:30px;font-weight:bold;">
                    <u>Occurrences not linked to taxonomic thesaurus</u>: <span id="unlinkedOccCnt"></span><br/>
                    <u>Unique scientific names</u>: <span id="unlinkedTaxaCnt"></span><br/>
                </div>
            </div>
            <div class="processor-container">
                <div class="processor-control-container">
                    <div id="processor-accordion" class="q-pa-md">
                        <q-list bordered class="rounded-borders">
                            <q-expansion-item group="controlgroup" label="Maintenance Utilities" header-class="bg-grey-3 text-bold" default-opened>
                                <q-card class="accordion-panel">
                                    <q-card-section>
                                        <div class="processor-accordion-panel">
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
                                            <div style="clear:both;margin-top:5px;">
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
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </q-expansion-item>
                            <q-separator></q-separator>
                            <q-expansion-item group="controlgroup" label="Search Utilities" header-class="bg-grey-3 text-bold">
                                <q-card class="accordion-panel">
                                    <q-card-section>
                                        <div class="processor-accordion-panel">
                                            <div class="process-header">
                                                Search Taxonomic Data Sources
                                            </div>
                                            <div style="margin-bottom:10px;">
                                                Search for occurrence record scientific names that are not currently linked to the Taxonomic Thesaurus
                                                from an external Taxonomic Data Source.
                                            </div>
                                            <div style="margin-bottom:10px;">
                                                <fieldset style="padding:5px;">
                                                    <legend><b>Taxonomic Data Source</b></legend>
                                                    <q-option-group :options="dataSourceOptions" type="radio" v-model="taxresource" :disable="upperdisabled" dense />
                                                </fieldset>
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
                                            <div style="clear:both;margin-top:5px;">
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
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </q-expansion-item>
                        </q-list>
                    </div>
                </div>

                <div id="processor-display" class="processor-display-container">
                    <q-scroll-area ref="procDisplayScrollAreaRef" class="processor-display" @scroll="setScroller">
                        <ul class="processor-display-list" id="progressDisplayList"></ul>
                    </q-scroll-area>
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
    <script>
        const kingdomOptions = Vue.ref([]);
        let uppercontrolsdisabled = Vue.ref(false);
        let currentProcess = Vue.ref(null);
        let selectedKingdom = Vue.ref(null);
        let processingStartIndex = Vue.ref(null);
        let processingLimit = Vue.ref(null);
        let updatedet = Vue.ref(false);
        let dataSource = Vue.ref('col');
        let levValue = Vue.ref('2');

        const moduleHeader = Vue.createApp();
        moduleHeader.use(Quasar, { config: {} });
        moduleHeader.mount('#module-header');

        const upperControls = Vue.createApp({
            data() {
                return {
                    kingdomOpts: kingdomOptions,
                    kingdom: selectedKingdom,
                    upperdisabled: uppercontrolsdisabled,
                    startIndex: processingStartIndex,
                    batchLimit: processingLimit
                }
            },
            methods: {
                processingBatchLimitChange(value) {
                    if(value && (isNaN(value) || Number(value) <= 0)){
                        alert('Processing batch limit must be a number greater than zero.');
                        processingLimit.value = null;
                    }
                },
                setKingdomId(kingdomobj) {
                    targetKingdomId = kingdomobj.value;
                    targetKingdomName = kingdomobj.label;
                }
            }
        });
        upperControls.use(Quasar, { config: {} });
        upperControls.mount('#upper-controls');

        const controlPanel = Vue.createApp({
            data() {
                return {
                    upperdisabled: uppercontrolsdisabled,
                    currProcess: currentProcess,
                    updatedet: updatedet,
                    taxresource: dataSource,
                    levVal: levValue,
                    dataSourceOptions: [
                        { label: 'Catalogue of Life (COL)', value: 'col' },
                        { label: 'Integrated Taxonomic Information System (ITIS)', value: 'itis' },
                        { label: 'World Register of Marine Species (WoRMS)', value: 'worms' }
                    ]
                }
            },
            methods: {
                callCleaningController,
                cancelProcess,
                initializeCleanScinameAuthor,
                callTaxThesaurusLinkController,
                updateOccLocalitySecurity,
                initializeDataSourceSearch,
                initializeTaxThesaurusFuzzyMatch
            }
        });
        controlPanel.use(Quasar, { config: {} });
        controlPanel.mount('#processor-accordion');

        const processorDisplay = Vue.createApp({
            setup() {
                let procDisplayScrollAreaRef = Vue.ref(null);
                return {
                    procDisplayScrollAreaRef,
                    setScroller(info) {
                        if(info.hasOwnProperty('verticalSize') && info.verticalSize > 610){
                            procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                        }
                    }
                }
            }
        });
        processorDisplay.use(Quasar, { config: {} });
        processorDisplay.mount('#processor-display');
    </script>
</body>
</html>
