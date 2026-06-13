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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Glossary Index</title>
        <meta name="description" content="Glossary index for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Glossary</span>
            </div>
            <div class="q-pa-md column">
                <div class="q-mb-sm row justify-between">
                    <div class="text-h5 text-bold">Search/Browse Glossary</div>
                    <div class="row justify-end q-gutter-sm q-pr-md">
                        <template v-if="isEditor">
                            <div>
                                <q-btn color="secondary" @click="openChecklistEditorPopup();" label="Create Term" tabindex="0" />
                            </div>
                            <div>
                                <q-btn color="secondary" @click="openChecklistEditorPopup();" label="Upload Terms" tabindex="0" />
                            </div>
                            <div v-if="Number(selectedTaxonomicGroupId) > 0 && Number(glossarySourceData.tid) === 0">
                                <q-btn color="secondary" @click="openChecklistEditorPopup();" label="Add Sources" tabindex="0" />
                            </div>
                        </template>
                    </div>
                </div>
                <div class="q-mb-sm full-width">
                    <q-separator></q-separator>
                </div>
                <div class="q-mb-sm row q-col-gutter-sm">
                    <div class="col-6">
                        <selector-input-element label="Taxonomic Group" :options="glossaryTaxaOptions" :value="selectedTaxonomicGroupId" :clearable="true" @update:value="processTaxonomicGroupChange"></selector-input-element>
                    </div>
                    <div v-if="glossaryLanguageArr.length > 1" class="col-3">
                        <selector-input-element label="Language" :options="glossaryLanguageArr" :value="selectedLanguage" :clearable="true" @update:value="processLanguageChange"></selector-input-element>
                    </div>
                </div>
                <div class="q-mb-sm row q-col-gutter-sm">
                    <div class="col-5">
                        <text-field-input-element label="Search Term" :value="searchTermVal" @update:value="processSearchTermChange"></text-field-input-element>
                    </div>
                    <div>
                        <checkbox-input-element label="Search within definitions" :value="searchWithinDefinitionsVal" @update:value="processSearchWithinDefinitionsChange"></checkbox-input-element>
                    </div>
                </div>
                <div class="q-mb-sm full-width">
                    <q-separator></q-separator>
                </div>
                <template v-if="glossarySourceData && Number(glossarySourceData.tid) > 0">
                    <div class="row justify-between q-gutter-sm">
                        <div>
                            <template v-if="showSources">
                                <div class="text-body1 text-bold text-blue cursor-pointer">
                                    <a @click="showSources = false" class="text-primary" tabindex="0">Hide Sources</a>
                                </div>
                            </template>
                            <template v-else>
                                <div class="text-body1 text-bold text-blue cursor-pointer">
                                    <a @click="showSources = true" class="text-primary" tabindex="0">Show Sources</a>
                                </div>
                            </template>
                        </div>
                        <div>
                            <q-btn color="grey-4" text-color="black" class="black-border cursor-pointer" size="sm" @click="showChecklistEditorPopup = true" icon="fas fa-cog" aria-label="Edit sources" dense tabindex="0">
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Edit sources
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                    <template v-if="showSources">
                        <div v-if="glossarySourceData['contributorterm']" class="text-body1">
                            <span class="text-bold">Terms and Definitions contributed by: </span>{{ glossarySourceData['contributorterm'] }}
                        </div>
                        <div v-if="glossarySourceData['contributorimage']" class="text-body1">
                            <span class="text-bold">Images contributed by: </span>{{ glossarySourceData['contributorimage'] }}
                        </div>
                        <div v-if="glossarySourceData['translator']" class="text-body1">
                            <span class="text-bold">Translations by: </span>{{ glossarySourceData['translator'] }}
                        </div>
                        <div v-if="glossarySourceData['additionalsources']" class="text-body1">
                            <span class="text-bold">Translations and images were also sourced from the following references: </span>{{ glossarySourceData['additionalsources'] }}
                        </div>
                    </template>
                    <div class="q-mb-sm full-width">
                        <q-separator></q-separator>
                    </div>
                </template>
                <template v-if="activeGlossaryArr.length > termsPerPage">
                    <div class="q-mb-sm q-px-md full-width row justify-end q-gutter-md">
                        <q-pagination v-model="paginationPage" :max="paginationLastPageNumber" direction-links flat color="grey" active-color="primary" max-pages="10" aria-label="Glossary term page navigation"></q-pagination>
                        <div>
                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="downloadChecklist('csv');" icon="fas fa-download" dense aria-label="Download Checklist CSV" tabindex="0">
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Download Checklist CSV
                                </q-tooltip>
                            </q-btn>
                        </div>
                    </div>
                    <div class="q-mb-sm full-width">
                        <q-separator></q-separator>
                    </div>
                </template>
                <template v-if="activeGlossaryArr.length > 0">
                    <template v-for="glossary in paginatedGlossaryArr">
                        <div class="q-my-xs">
                            <a class="text-bold" tabindex="0">{{ glossary['term'] }}</a>
                            <template v-if="glossary['definition']">
                                {{ ' - ' + glossary['definition'] }}
                            </template>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="text-h6 text-bold">
                        There are no glossary terms available at this time.
                    </div>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/glossary-source.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/glossary-image.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/glossary.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const glossaryIndexModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'selector-input-element': selectorInputElement,
                    'text-field-input-element': textFieldInputElement
                },
                setup() {
                    const { hideWorking, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const glossaryStore = useGlossaryStore();

                    const activeGlossaryArr = Vue.computed(() => {
                        const returnArr = [];
                        glossaryArr.value.forEach(glossary => {
                            let includeTerm = true;
                            if(Number(selectedTaxonomicGroupId.value) > 0 && !glossary['tidArr'].includes(Number(selectedTaxonomicGroupId.value))) {
                                includeTerm = false;
                            }
                            if(includeTerm && selectedLanguage.value && selectedLanguage.value !== glossary.language) {
                                includeTerm = false;
                            }
                            if(includeTerm && searchTermVal.value) {
                                if(!glossary.term.startsWith(searchTermVal.value) && !glossary.term.includes(' ' + searchTermVal.value)) {
                                    includeTerm = false;
                                }
                                if(searchWithinDefinitionsVal.value && glossary.definition && glossary.definition.includes(searchTermVal.value)) {
                                    includeTerm = true;
                                }
                            }
                            if(includeTerm){
                                returnArr.push(glossary);
                            }
                        });
                        returnArr.sort((a, b) => {
                            return a['term'].localeCompare(b['term']);
                        });
                        return returnArr;
                    });
                    const clientRoot = baseStore.getClientRoot;
                    const glossaryArr = Vue.computed(() => glossaryStore.getGlossaryArr);
                    const glossaryLanguageArr = Vue.computed(() => glossaryStore.getGlossaryLanguageArr);
                    const glossarySourceData = Vue.computed(() => glossaryStore.getGlossarySourceData);
                    const glossaryTaxaArr = Vue.computed(() => glossaryStore.getGlossaryTaxaArr);
                    const glossaryTaxaOptions = Vue.computed(() => {
                        const returnArr = [];
                        glossaryTaxaArr.value.forEach(taxon => {
                            const vernacularArr = [];
                            if(taxon['vernacularData']){
                                taxon['vernacularData'].forEach(vernacular => {
                                    if(Number(vernacular['vernaculartid']) === Number(taxon['tid'])) {
                                        if(!vernacular['vernacularlanguage'] || !selectedLanguage.value || (selectedLanguage.value && vernacular['vernacularlanguage'] && selectedLanguage.value === vernacular['vernacularlanguage'])){
                                            vernacularArr.push(vernacular['vernacularname']);
                                        }
                                    }
                                });
                            }
                            returnArr.push({
                                value: taxon['tid'],
                                label: (taxon['sciname'] + (vernacularArr.length > 0 ? (' (' + vernacularArr.join(',') + ')') : '')),
                            })
                        });
                        return returnArr;
                    });
                    const isEditor = Vue.ref(false);
                    const paginatedGlossaryArr = Vue.computed(() => {
                        let returnArr;
                        if(activeGlossaryArr.value.length > termsPerPage){
                            let endIndex = activeGlossaryArr.value.length;
                            const index = (paginationPage.value - 1) * termsPerPage;
                            if(activeGlossaryArr.value.length > (index + termsPerPage)){
                                endIndex = index + termsPerPage;
                            }
                            returnArr = activeGlossaryArr.value.slice(index, endIndex);
                        }
                        else{
                            returnArr = activeGlossaryArr.value.slice();
                        }
                        return returnArr;
                    });
                    const paginationLastPageNumber = Vue.computed(() => {
                        let lastPage = 1;
                        if(activeGlossaryArr.value.length > termsPerPage){
                            lastPage = Math.floor(activeGlossaryArr.value.length / termsPerPage);
                        }
                        if(activeGlossaryArr.value.length % termsPerPage){
                            lastPage++;
                        }
                        return lastPage;
                    });
                    const paginationPage = Vue.ref(1);
                    const searchTermVal = Vue.ref(null);
                    const searchWithinDefinitionsVal = Vue.ref(false);
                    const selectedLanguage = Vue.ref(null);
                    const selectedTaxonomicGroupId = Vue.computed(() => glossaryStore.getGlossarySourceID);
                    const showSources = Vue.ref(false);
                    const termsPerPage = 100;

                    function processLanguageChange(value) {
                        selectedLanguage.value = value;
                    }

                    function processSearchTermChange(value) {
                        searchTermVal.value = value;
                    }

                    function processSearchWithinDefinitionsChange(value) {
                        searchWithinDefinitionsVal.value = Number(value) === 1;
                    }

                    function processTaxonomicGroupChange(id) {
                        glossaryStore.setGlossarySourceData(id);
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'Taxonomy');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isEditor.value = resData.includes('Taxonomy');
                        });
                    }

                    Vue.onMounted(() => {
                        showWorking();
                        setEditor();
                        glossaryStore.setGlossaryData(() => {
                            hideWorking();
                        });
                    });

                    return {
                        activeGlossaryArr,
                        clientRoot,
                        glossaryLanguageArr,
                        glossarySourceData,
                        glossaryTaxaOptions,
                        isEditor,
                        paginatedGlossaryArr,
                        paginationLastPageNumber,
                        paginationPage,
                        searchTermVal,
                        searchWithinDefinitionsVal,
                        selectedLanguage,
                        selectedTaxonomicGroupId,
                        showSources,
                        termsPerPage,
                        processLanguageChange,
                        processSearchTermChange,
                        processSearchWithinDefinitionsChange,
                        processTaxonomicGroupChange
                    }
                }
            });
            glossaryIndexModule.use(Quasar, { config: {} });
            glossaryIndexModule.use(Pinia.createPinia());
            glossaryIndexModule.mount('#mainContainer');
        </script>
    </body>
</html>
