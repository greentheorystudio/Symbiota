<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$tId = array_key_exists('tid', $_REQUEST) ? (int)$_REQUEST['tid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxon Profile Editor</title>
        <meta name="description" content="Taxon profile editor for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <style>
            div.q-menu.q-position-engine {
                z-index: 9998!important;
            }
            div.q-tooltip.q-position-engine {
                z-index: 9998!important;
            }
        </style>
        <script type="text/javascript">
            const TID = <?php echo $tId; ?>;
        </script>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Taxon Profile Editor</span>
            </div>
            <div v-if="isTaxonProfileEditor" class="q-pa-md">
                <div class="q-mb-sm text-h5 text-bold">
                    Taxon Profile Editor
                </div>
                <q-card class="q-mb-sm">
                    <q-card-section class="row justify-between q-gutter-x-sm">
                        <div class="col-4">
                            <single-scientific-common-name-auto-complete :sciname="taxonNameVal" label="Taxon" :limit-to-options="true" @update:sciname="processTaxonNameChange"></single-scientific-common-name-auto-complete>
                        </div>
                        <div class="col-4 row justify-end">
                            <div v-if="isTaxonEditor">
                                <q-btn color="primary" @click="showNewTaxonEditorPopup = true" label="Add Taxon" tabindex="0" />
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
                <template v-if="Number(taxon['tid']) > 0">
                    <q-card class="column">
                        <div class="q-px-sm q-pt-sm row justify-between q-gutter-x-sm">
                            <div class="q-pl-sm column">
                                <div>
                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon.tid)" aria-label="Go to taxon profile page" tabindex="0"><span class="taxon-profile-sciname"><span class="species">{{ taxon.sciname + ' ' }}</span></span></a>
                                    <template v-if="taxon.author">
                                        <span>{{ taxon.author }}</span>
                                    </template>
                                    <template v-if="isAccepted && Number(taxon.parenttid) > 0">
                                        <a class="q-ml-sm cursor-pointer" title="Go to Parent" aria-label="Go to Parent" @click="setTaxonData(taxon.parenttid);" tabindex="0">
                                            <q-icon name="fas fa-level-up-alt" size="15px" class="cursor-pointer" />
                                        </a>
                                    </template>
                                    <template v-if="isAccepted && taxon.family">
                                        <span class="q-ml-md text-bold">Family:</span> {{ taxon.family }}
                                    </template>
                                </div>
                                <div v-if="!isAccepted">
                                    <span class="text-subtitle1 text-bold text-red">Synonym of: </span>
                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + taxon.tidaccepted)" aria-label="Go to accepted taxon profile page" tabindex="0"><span class="taxon-profile-sciname"><span class="species">{{ acceptedTaxon.sciname + ' ' }}</span></span></a>
                                    <template v-if="acceptedTaxon.author">
                                        <span>{{ acceptedTaxon.author }}</span>
                                    </template>
                                    <template v-if="Number(acceptedTaxon.parenttid) > 0">
                                        <a class="q-ml-sm cursor-pointer" title="Go to Parent" aria-label="Go to Parent" @click="setTaxonData(acceptedTaxon.parenttid);" tabindex="0">
                                            <q-icon name="fas fa-level-up-alt" size="15px" class="cursor-pointer" />
                                        </a>
                                    </template>
                                    <template v-if="acceptedTaxon.family">
                                        <span class="q-ml-md text-bold">Family:</span> {{ acceptedTaxon.family }}
                                    </template>
                                </div>
                            </div>
                            <div class="row justify-end">
                                <div v-if="isTaxonEditor">
                                    <q-btn role="link" color="grey-4" text-color="black" class="black-border text-bold" size="md" :href="(clientRoot + '/taxa/taxonomy/taxonomyeditor.php?tid=' + taxon['tid'])" label="Edit Taxon" no-wrap tabindex="0"></q-btn>
                                </div>
                            </div>
                        </div>
                        <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab name="media" label="Media" no-caps></q-tab>
                            <q-tab name="descriptions" label="Descriptions" no-caps></q-tab>
                            <q-tab v-if="isAccepted" name="tag" label="Tag Primary Image" no-caps></q-tab>
                            <q-tab name="common" label="Common Names" no-caps></q-tab>
                            <q-tab v-if="isAccepted || taxonMap" name="map" label="Taxon Map" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel name="media" class="main-container-height">
                                <taxon-profile-editor-media-tab></taxon-profile-editor-media-tab>
                            </q-tab-panel>
                            <q-tab-panel name="descriptions" class="main-container-height">
                                <taxon-profile-editor-descriptions-tab></taxon-profile-editor-descriptions-tab>
                            </q-tab-panel>
                            <q-tab-panel v-if="isAccepted" name="tag" class="main-container-height">
                                <taxon-profile-editor-primary-image-tab></taxon-profile-editor-primary-image-tab>
                            </q-tab-panel>
                            <q-tab-panel name="common" class="main-container-height">
                                <taxon-profile-editor-vernacular-tab></taxon-profile-editor-vernacular-tab>
                            </q-tab-panel>
                            <q-tab-panel v-if="isAccepted || taxonMap" name="map" class="main-container-height">
                                <taxon-profile-editor-taxon-map-tab></taxon-profile-editor-taxon-map-tab>
                            </q-tab-panel>
                        </q-tab-panels>
                    </q-card>
                </template>
                <template v-else>
                    <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                    <div class="q-pa-md row justify-center text-h6 text-bold">
                        Please enter the scientific name of the taxon you wish to edit in the box above
                    </div>
                </template>
                <template v-if="showNewTaxonEditorPopup">
                    <new-taxon-editor-popup
                        :show-popup="showNewTaxonEditorPopup"
                        @taxon:created="processTaxonCreated"
                        @close:popup="showNewTaxonEditorPopup = false"
                    ></new-taxon-editor-popup>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-description-block.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-description-statement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-map.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxonRankSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/imageTagSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/filePickerInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceSelectorInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceLinkageToolPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleLanguageAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/wysiwygInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/mediaFileUploadInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonFieldModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/newTaxonEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonProfileEditorDescriptionBlockEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonProfileEditorDescriptionStatementEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonProfileEditorVernacularEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonProfileEditorDescriptionsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonProfileEditorMediaTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonProfileEditorPrimaryImageTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonProfileEditorTaxonMapTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/taxonProfileEditorVernacularTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const taxonProfileEditorModule = Vue.createApp({
                components: {
                    'new-taxon-editor-popup': newTaxonEditorPopup,
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                    'taxon-profile-editor-descriptions-tab': taxonProfileEditorDescriptionsTab,
                    'taxon-profile-editor-media-tab': taxonProfileEditorMediaTab,
                    'taxon-profile-editor-primary-image-tab': taxonProfileEditorPrimaryImageTab,
                    'taxon-profile-editor-taxon-map-tab': taxonProfileEditorTaxonMapTab,
                    'taxon-profile-editor-vernacular-tab': taxonProfileEditorVernacularTab
                },
                setup() {
                    const baseStore = useBaseStore();
                    const taxaStore = useTaxaStore();

                    const acceptedTaxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);
                    const clientRoot = baseStore.getClientRoot;
                    const initialTid = TID;
                    const isAccepted = Vue.computed(() => taxaStore.getAccepted);
                    const isTaxonEditor = Vue.ref(false);
                    const isTaxonProfileEditor = Vue.ref(false);
                    const showNewTaxonEditorPopup = Vue.ref(false);
                    const tab = Vue.ref('media');
                    const taxaMapData = Vue.computed(() => taxaStore.getTaxaMapArr);
                    const taxon = Vue.computed(() => taxaStore.getTaxaData);
                    const taxonId = Vue.computed(() => taxaStore.getTaxaID);
                    const taxonMap = Vue.computed(() => {
                        return taxaMapData.value.hasOwnProperty(taxonId.value) ? taxaMapData.value[taxonId.value] : null;
                    });
                    const taxonNameVal = Vue.ref(null);

                    function processTaxonCreated(taxonId) {
                        setTaxonData(taxonId);
                    }

                    function processTaxonNameChange(taxonData) {
                        if(taxonData && Number(taxonData['tid']) > 0) {
                            setTaxonData(taxonData['tid']);
                        }
                        else{
                            setTaxonData(0);
                        }
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permissionJson', JSON.stringify(['TaxonProfile', 'Taxonomy']));
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resData) => {
                                isTaxonEditor.value = resData.includes('Taxonomy');
                                isTaxonProfileEditor.value = resData.includes('TaxonProfile');
                                if(!isTaxonProfileEditor.value){
                                    window.location.href = clientRoot + '/index.php';
                                }
                            });
                        });
                    }

                    function setTaxonData(tidValue) {
                        taxaStore.setTaxon(tidValue, (tid) => {
                            if(Number(tid) > 0){
                                taxonNameVal.value = taxon.value['sciname'];
                                taxaStore.setTaxonVernacularArr(taxon.value['tid']);
                                taxaStore.setTaxonMapArr(taxon.value['tid']);
                                taxaStore.setTaxonDescriptionData(taxon.value['tid']);
                                taxaStore.setTaxaImageArr(taxon.value['tid'], false);
                                taxaStore.setTaxaMediaArr(taxon.value['tid'], false);
                                taxaStore.setTaxaTaggedImageArr(taxon.value['tid']);
                            }
                            else{
                                taxonNameVal.value = null;
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        if(Number(initialTid) > 0){
                            setTaxonData(initialTid);
                        }
                    });
                    
                    return {
                        acceptedTaxon,
                        clientRoot,
                        isAccepted,
                        isTaxonEditor,
                        isTaxonProfileEditor,
                        showNewTaxonEditorPopup,
                        tab,
                        taxon,
                        taxonMap,
                        taxonNameVal,
                        processTaxonCreated,
                        processTaxonNameChange,
                        setTaxonData
                    }
                }
            });
            taxonProfileEditorModule.use(Quasar, { config: {} });
            taxonProfileEditorModule.use(Pinia.createPinia());
            taxonProfileEditorModule.mount('#mainContainer');
        </script>
    </body>
</html>
