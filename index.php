<?php
include_once(__DIR__ . '/config/symbbase.php');
include_once(__DIR__ . '/classes/IRLManager.php');
header('Content-Type: text/html; charset=UTF-8' );

$IRLManager = new IRLManager();

$totalTaxa = number_format($IRLManager->getTotalTaxa());
$totalTaxaWithDesc = number_format($IRLManager->getTotalTaxaWithDesc());
$totalOccurrenceRecords = number_format($IRLManager->getTotalOccurrenceRecords());
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/external/jquery-ui.css" type="text/css" rel="Stylesheet" />
    <script src="js/external/all.min.js" type="text/javascript"></script>
    <script src="js/external/jquery.js" type="text/javascript"></script>
    <script src="js/external/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            const quicksearchDiv = document.getElementById('quicksearchinputcontainer');
            const linkDiv = document.createElement('div');
            linkDiv.setAttribute("class","as");
            const linkElement = document.createElement('a');
            linkElement.setAttribute("href","<?php echo $GLOBALS['CLIENT_ROOT']; ?>/taxa/dynamictaxalist.php");
            linkElement.innerHTML = "Advanced Search";
            linkDiv.appendChild(linkElement);
            quicksearchDiv.appendChild(linkDiv);
         });
    </script>
    <script type="text/javascript">
        var imgArray = [
            "url(content/imglib/static/09ThomanR1.jpg)",
            "url(content/imglib/static/20Donahue1.jpg)",
            "url(content/imglib/static/21ShirahD1.jpg)",
            "url(content/imglib/static/21VanMeterS2.jpg)",
            "url(content/imglib/static/04BergerJ3.jpg)",
            "url(content/imglib/static/11SmithA1.jpg)",
            "url(content/imglib/static/17AdamsN1.jpg)",
            "url(content/imglib/static/18FischerD1.JPG)",
            "url(content/imglib/static/19CunninghamD1.jpg)",
            "url(content/imglib/static/19GilbertD1.jpg)",
            "url(content/imglib/static/19PalmerC2.jpg)",
            "url(content/imglib/static/21SmithA1.jpg)",
            "url(content/imglib/static/06KemptonR1.jpg)",
            "url(content/imglib/static/13ClarkeG1.jpg)",
            "url(content/imglib/static/13CorapiP1.jpg)",
            "url(content/imglib/static/17Spratt2.jpg)",
            "url(content/imglib/static/18CoteM1.JPG)",
            "url(content/imglib/static/18PhippsL2.jpg)",
            "url(content/imglib/static/18SimonsT1.jpg)",
            "url(content/imglib/static/20MandevilleJ2.jpg)"];
        var photographerArray = [
            "R. Thoman",
            "M. Donahue",
            "D. Shirah",
            "S. Van Meter",
            "J. Berger",
            "A. Smith",
            "N. Adams",
            "D. Fischer",
            "D. Cunningham",
            "D. Gilbert",
            "C. Palmer",
            "A. Smith",
            "R. Kempton",
            "G. Clarke",
            "P. Corapi",
            "R. Spratt",
            "M. Cote",
            "L. Phipps",
            "T. Simons",
            "J. Mandeville"];

        $(document).ready(function() {
            const imgIndex = Math.floor(Math.random() * 20);
            document.getElementById('hero-container').style.backgroundImage = imgArray[imgIndex];
            document.getElementById('photographerName').innerHTML = photographerArray[imgIndex];
        });
    </script>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $GLOBALS['GOOGLE_TAG_MANAGER_ID']; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div class="hero-container" id="hero-container">
        <div class="top-shade-container"></div>
        <div class="logo-container">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" >
                <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/layout/janky_mangrove_logo_med.png" />
            </a>
        </div>
        <div class="title-container">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php" class="header-home-link" >
                <span class="titlefont">Indian River Lagoon<br />
                Species Inventory</span>
            </a>
        </div>
        <div class="login-bar">
            <?php
            include(__DIR__ . '/header-login.php');
            ?>
        </div>
        <div class="nav-bar-container">
            <?php
            include(__DIR__ . '/header-navigation.php');
            ?>
        </div>
        <div id="quicksearch-container">
            <q-card flat bordered class="black-border bg-grey-3">
                <q-card-section class="q-pa-sm column">
                    <div class="q-mb-xs row justify-between">
                        <div>
                            <q-btn-toggle v-model="selectedTaxonType" :options="taxonTypeOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" @update:model-value="processTaxonTypeChange"></q-btn-toggle>
                        </div>
                        <div class="row justify-end">
                            <div>
                                <a class="text-body1 text-bold" :href="(clientRoot + '/taxa/dynamictaxalist.php')">
                                    Advanced Search
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <taxa-search-auto-complete :label="autoCompleteLabel" :taxon-type="taxonType"></taxa-search-auto-complete>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <div class="heading-container">
            <div class="heading-inner">
                <h3>The <b>Indian River Lagoon Species Inventory</b> is a dynamic and growing research resource and ecological encyclopedia that documents the biodiversity of the 156-mile-long estuary system along Florida’s Atlantic coast.</h3>
            </div>
        </div>
        <div class="photo-credit-container">
            Photo credit: <span id="photographerName"></span>
        </div>
    </div>
    <div id="innertext">
        <div class="totals-row">
            <div class="totals-box">
                <i style="height:60px;width:60px;" class="fas fa-leaf"></i>
                <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalTaxaWithDesc; ?></h2>
                <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;">Species Reports</h5>
            </div>
            <div class="totals-box">
                <i style="height:60px;width:60px;background-image: url('/content/imglib/layout/fat_tree.svg');background-size:cover;filter: drop-shadow(10px 10px 4px lightgrey);"></i>
                <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalTaxa; ?></h2>
                <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;"> Total Taxa</h5>
            </div>
            <div class="totals-box">
                <i style="height:60px;width:60px;" class="fas fa-map-marked-alt"></i>
                <h2 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 3.5em;text-align: center;margin-bottom: 5px;"><?php echo $totalOccurrenceRecords; ?></h2>
                <h5 style="font-family:'Whitney A','Whitney B',Helvetica,Arial,sans-serif;color: gray;font-weight: 300;font-size: 2.1em;text-align: center;line-height: normal;">Occurrence Records</h5>
            </div>
        </div>
    </div>
    <?php
    include_once(__DIR__ . '/config/footer-includes.php');
    include(__DIR__ . '/footer.php');
    ?>
    <script>
        const taxaSearchAutoComplete = {
            props: {
                hideAuthor: {
                    type: Boolean,
                    default: true
                },
                hideProtected: {
                    type: Boolean,
                    default: false
                },
                label: {
                    type: String,
                    default: 'Scientific Name'
                },
                limitToThesaurus: {
                    type: Boolean,
                    default: false
                },
                optionLimit: {
                    type: Number,
                    default: 10
                },
                rankHigh: {
                    type: Number,
                    default: null
                },
                rankLimit: {
                    type: Number,
                    default: null
                },
                rankLow: {
                    type: Number,
                    default: null
                },
                taxonType: {
                    type: Number,
                    default: null
                }
            },
            template: `
            <q-select v-model="selectedTaxon.label" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon popup-content-class="z-max" input-debounce="0" bg-color="white" @new-value="createValue" :options="autocompleteOptions" @filter="getOptions" @blur="blurAction" @update:model-value="processChange" :label="label">
                <template v-slot:append>
                    <q-icon name="search" class="cursor-pointer" @click="processSearch();">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Clear value
                        </q-tooltip>
                    </q-icon>
                </template>
            </q-select>
        `,
            setup(props) {
                const { showNotification } = useCore();
                const store = useBaseStore();

                const autocompleteOptions = Vue.ref([]);
                const clientRoot = store.getClientRoot;
                const propsRefs = Vue.toRefs(props);
                const selectedTaxon = Vue.ref({});

                Vue.watch(propsRefs.taxonType, () => {
                    selectedTaxon.value = Object.assign({}, {});
                });

                function blurAction(val) {
                    if(val && selectedTaxon.value && val.target.value !== selectedTaxon.value.label){
                        const optionObj = autocompleteOptions.value.find(option => option['sciname'] === val.target.value);
                        if(optionObj){
                            processChange(optionObj);
                        }
                        else if(!props.limitToThesaurus){
                            processChange({
                                label: val.target.value,
                                sciname: val.target.value,
                                tid: null,
                                family: null,
                                author: null
                            });
                        }
                        else{
                            showNotification('negative', 'That name was not found in the Taxonomic Thesaurus.');
                        }
                    }
                }

                function createValue(val, done) {
                    if(val.length > 0) {
                        const optionObj = autocompleteOptions.value.find(option => option['sciname'] === val);
                        if(optionObj){
                            done(optionObj, 'add');
                        }
                        else if(!props.limitToThesaurus){
                            done({
                                label: val,
                                sciname: val,
                                tid: null,
                                family: null,
                                author: null
                            }, 'add');
                        }
                        else{
                            showNotification('negative', 'That name was not found in the Taxonomic Thesaurus.');
                        }
                    }
                }

                function getOptions(val, update) {
                    update(() => {
                        if(val.length > 2) {
                            let action = 'getAutocompleteSciNameList';
                            let rankLimit, rankLow, rankHigh;
                            let dataSource = taxaApiUrl;
                            if(props.taxonType){
                                if(Number(props.taxonType) === 1){
                                    rankLow = 140;
                                }
                                else if(Number(props.taxonType) === 2){
                                    rankLimit = 140;
                                }
                                else if(Number(props.taxonType) === 3){
                                    rankLow = 180;
                                }
                                else if(Number(props.taxonType) === 4){
                                    rankLow = 10;
                                    rankHigh = 130;
                                }
                                else if(Number(props.taxonType) === 6){
                                    action = 'getAutocompleteVernacularList';
                                    dataSource = taxonVernacularApiUrl;
                                }
                            }
                            else{
                                rankLimit = props.rankLimit;
                                rankLow = props.rankLow;
                                rankHigh = props.rankHigh;
                            }
                            const formData = new FormData();
                            formData.append('action', action);
                            formData.append('term', val);
                            formData.append('hideauth', props.hideAuthor);
                            formData.append('hideprotected', props.hideProtected);
                            formData.append('rlimit', rankLimit);
                            formData.append('rlow', rankLow);
                            formData.append('rhigh', rankHigh);
                            formData.append('limit', props.optionLimit);
                            fetch(dataSource, {
                                method: 'POST',
                                body: formData
                            })
                                .then((response) => response.json())
                                .then((result) => {
                                    autocompleteOptions.value = result;
                                });
                        }
                        else{
                            autocompleteOptions.value = [];
                        }
                    });
                }

                function processChange(taxonObj) {
                    selectedTaxon.value = Object.assign({}, taxonObj);
                }

                function processSearch() {
                    if(Number(props.taxonType) === 6){
                        const formData = new FormData();
                        formData.append('vernacular', selectedTaxon.value['label']);
                        formData.append('action', 'getHighestRankingTidByVernacular');
                        fetch(taxonVernacularApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                            .then((response) => {
                                if(response.status === 200){
                                    response.text().then((res) => {
                                        if(Number(res) > 0){
                                            window.location.href = (clientRoot + '/taxa/index.php?taxon=' + res);
                                        }
                                        else{
                                            showNotification('negative', 'That common name was not found in the database');
                                        }
                                    });
                                }
                            });
                    }
                    else{
                        if(selectedTaxon.value.hasOwnProperty('tid') && Number(selectedTaxon.value['tid']) > 0){
                            window.location.href = (clientRoot + '/taxa/index.php?taxon=' + selectedTaxon.value['tid']);
                        }
                        else{
                            showNotification('negative', 'That scientific name was not found in the database');
                        }
                    }
                }

                return {
                    autocompleteOptions,
                    selectedTaxon,
                    blurAction,
                    createValue,
                    getOptions,
                    processChange,
                    processSearch
                }
            }
        };

        const quicksearchElement = Vue.createApp({
            components: {
                'taxa-search-auto-complete': taxaSearchAutoComplete
            },
            setup() {


                return {

                };
            }
        });
        quicksearchElement.use(Quasar, { config: {} });
        quicksearchElement.use(Pinia.createPinia());
        quicksearchElement.mount('#quicksearch-container');
    </script>
</body>
</html>
