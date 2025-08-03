<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$clid = array_key_exists('clid', $_REQUEST) ? (int)$_REQUEST['clid'] : 0;
$queryId = array_key_exists('queryId', $_REQUEST) ? (int)$_REQUEST['queryId'] : 0;
$pid = array_key_exists('pid', $_REQUEST) ? (int)$_REQUEST['pid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Checklist Details</title>
        <meta name="description" content="Individual checklist details in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol-ext.min.js?ver=20240115" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/turf.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/shp.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jszip.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/stream.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/FileSaver.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/html2canvas.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/geotiff.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/plotty.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const CLID = <?php echo $clid; ?>;
            const QUERYID = <?php echo $queryId; ?>;
            const PID = <?php echo $pid; ?>;
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')">Home</a> &gt;&gt;
                <template v-if="!temporaryChecklist">
                    <template v-if="Number(clId) > 0">
                        <a :href="(clientRoot + '/checklists/index.php')">Checklists</a> &gt;&gt;
                    </template>
                    <template v-else-if="Number(pId) > 0">
                        <a :href="(clientRoot + '/projects/index.php?pid=' + pId)">{{ projectName }}</a> &gt;&gt;
                    </template>
                    <template v-if="Number(clId) > 0">
                        <span class="text-bold">{{ checklistName }}</span>
                    </template>
                    <template v-else>
                        <span class="text-bold">Dynamic Checklist</span>
                    </template>
                </template>
                <template v-else>
                    <span class="text-bold">Dynamic Checklist</span>
                </template>
            </div>
            <div class="q-pa-md column">
                <template v-if="Number(clId) > 0 || Number(pId) > 0">
                    <div class="q-mb-md full-width row justify-between q-gutter-sm items-center">
                        <div class="row q-gutter-md">
                            <div>
                                <h1>{{ checklistName }}</h1>
                            </div>
                            <div class="row q-gutter-sm">
                                <div v-if="keyModuleIsActive && taxaDataArr.length > 0">
                                    <q-btn text-color="black" size="sm" :href="(clientRoot + '/ident/key.php?clid=' + clId + '&pid=' + pId)" icon="fas fa-key" dense unelevated :ripple="false">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Open Interactive Key
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                                <div v-if="taxaDataArr.length > 0">
                                    <q-btn text-color="black" size="sm" :href="(clientRoot + '/games/flashcards.php?clid=' + clId)" icon="fas fa-gamepad" dense unelevated :ripple="false">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Open Flashcard Game
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                                <div v-if="Object.keys(checklistVoucherData).length > 0">
                                    <q-btn text-color="black" size="sm" :href="mapViewUrl" icon="fas fa-globe" dense unelevated :ripple="false">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            View Vouchers in Interactive Map
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-end q-gutter-sm items-center">
                            <template v-if="Number(clId) > 0">
                                <template v-if="taxaDataArr.length > 0">
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="downloadChecklist('csv');" icon="fas fa-download" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Download Checklist as CSV
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                    <div>
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="downloadChecklist('docx');" icon="far fa-file-word" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Download Checklist as Word Document
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </template>
                                <template v-if="validUser">
                                    <template v-if="temporaryChecklist">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="saveTemporaryChecklist();" icon="fas fa-save" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Save Checklist
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </template>
                                    <template v-else-if="isEditor">
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" :href="(clientRoot + '/checklists/checklistadmin.php?clid=' + clId + '&pid=' + pId)" icon="fas fa-cog" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Open Checklist Administration
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" :href="(clientRoot + '/checklists/voucheradmin.php?clid=' + clId + '&pid=' + pId)" icon="fas fa-link" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Open Voucher Administration
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openChecklistTaxaEditorPopup(0)" icon="add_circle" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Add Taxon
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div>
                                            <template v-if="taxaEditingActive">
                                                <q-btn color="grey-4" text-color="red" class="black-border" size="sm" @click="taxaEditingActive = !taxaEditingActive" icon="fas fa-clipboard-list" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Toggle Taxa Editing Off
                                                    </q-tooltip>
                                                </q-btn>
                                            </template>
                                            <template v-else>
                                                <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="taxaEditingActive = !taxaEditingActive" icon="fas fa-clipboard-list" dense>
                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                        Toggle Taxa Editing On
                                                    </q-tooltip>
                                                </q-btn>
                                            </template>
                                        </div>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                    <div v-if="checklistData.hasOwnProperty('authors') && checklistData['authors']" class="text-body1">
                        <span class="text-bold">Authors: </span>{{ checklistData['authors'] }}
                    </div>
                    <div v-if="checklistData.hasOwnProperty('publication') && checklistData['publication']" class="text-body1">
                        <span class="text-bold">Publication: </span>{{ checklistData['publication'] }}
                    </div>
                    <template v-if="checklistLocalityText || (checklistData.hasOwnProperty('abstract') && checklistData['abstract']) || (checklistData.hasOwnProperty('notes') && checklistData['notes'])">
                        <template v-if="showMoreDescription">
                            <div v-if="checklistLocalityText" class="text-body1">
                                <span class="text-bold">Locality: </span>{{ checklistLocalityText }}
                            </div>
                            <div v-if="checklistData.hasOwnProperty('abstract') && checklistData['abstract']" class="text-body1">
                                <span class="text-bold">Abstract: </span><span v-html="checklistData['abstract']"></span>
                            </div>
                            <div v-if="checklistData.hasOwnProperty('notes') && checklistData['notes']" class="text-body1">
                                <span class="text-bold">Notes: </span>{{ checklistData['notes'] }}
                            </div>
                            <div class="text-body1 text-bold text-blue cursor-pointer">
                                <a @click="processDisplayDetailsChange(false);" class="text-primary">Less Details</a>
                            </div>
                        </template>
                        <template v-else>
                            <div class="text-body1 text-bold text-blue cursor-pointer">
                                <a @click="processDisplayDetailsChange(true);" class="text-primary">More Details</a>
                            </div>
                        </template>
                    </template>
                    <div class="q-mb-sm full-width">
                        <q-separator ></q-separator>
                    </div>
                    <div class="q-mb-xs full-width row justify-between q-gutter-sm">
                        <div class="col-8 column q-gutter-xs">
                            <div class="q-mb-sm row q-col-gutter-sm">
                                <div class="col-4">
                                    <selector-input-element label="Sort Taxa" :options="sortByOptions" :value="selectedSortByOption" @update:value="processSortByChange"></selector-input-element>
                                </div>
                                <div class="col-8">
                                    <single-scientific-common-name-auto-complete :sciname="(taxonFilterVal ? taxonFilterVal.sciname : null)" :options="taxaFilterOptions" label="Taxon Filter" limit-to-options="true" @update:sciname="processTaxonFilterValChange"></single-scientific-common-name-auto-complete>
                                </div>
                            </div>
                            <div class="row q-col-gutter-sm">
                                <div class="col-2">
                                    <div class="q-mr-md text-body1 text-bold">Display:</div>
                                </div>
                                <div class="col-10 row q-gutter-sm q-pa-xs">
                                    <div>
                                        <checkbox-input-element label="Synonyms" :value="displaySynonymsVal" @update:value="processDisplaySynonymsChange"></checkbox-input-element>
                                    </div>
                                    <div>
                                        <checkbox-input-element label="Common Names" :value="displayCommonNamesVal" @update:value="processDisplayCommonNameChange"></checkbox-input-element>
                                    </div>
                                    <div>
                                        <checkbox-input-element label="Images" :value="displayImagesVal" @update:value="processDisplayImagesChange"></checkbox-input-element>
                                    </div>
                                    <div>
                                        <checkbox-input-element label="Notes & Vouchers" :value="displayVouchersVal" @update:value="processDisplayVouchersChange"></checkbox-input-element>
                                    </div>
                                    <div>
                                        <checkbox-input-element label="Taxon Authors" :value="displayAuthorsVal" @update:value="processDisplayAuthorsChange"></checkbox-input-element>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 column q-col-gutter-sm q-pl-lg">
                            <div class="q-mt-sm column">
                                <div class="full-width row justify-end text-body1">
                                    <span class="text-bold q-mr-sm">Families: </span>{{ countData['families'] }}
                                </div>
                                <div class="full-width row justify-end text-body1">
                                    <span class="text-bold q-mr-sm">Genera: </span>{{ countData['genera'] }}
                                </div>
                                <div class="full-width row justify-end text-body1">
                                    <span class="text-bold q-mr-sm">Species: </span>{{ countData['species'] }}
                                </div>
                                <div class="full-width row justify-end text-body1">
                                    <span class="text-bold q-mr-sm">Total Taxa: </span>{{ countData['total'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="q-mb-sm full-width">
                        <q-separator ></q-separator>
                    </div>
                    <template v-if="activeTaxaArr.length > taxaPerPage">
                        <div class="q-mb-sm q-px-md full-width row justify-end">
                            <q-pagination v-model="paginationPage" :max="paginationLastPageNumber" direction-links flat color="grey" active-color="primary"></q-pagination>
                        </div>
                        <div class="q-mb-sm full-width">
                            <q-separator ></q-separator>
                        </div>
                    </template>
                    <template v-if="displayImagesVal">
                        <taxa-image-display
                            :display-authors="displayAuthorsVal"
                            :display-common-names="displayCommonNamesVal"
                            :display-synonyms="displaySynonymsVal"
                            :display-vouchers="displayVouchersVal"
                            :editing="taxaEditingActive"
                            :image-data="checklistImageData"
                            :sort-by="selectedSortByOption"
                            :taxa-arr="taxaDisplayDataArr"
                            :voucher-data="checklistVoucherData"
                            @open:checklist-taxa-editor="openChecklistTaxaEditorPopup"
                        ></taxa-image-display>
                    </template>
                    <template v-else>
                        <taxa-list-display
                            :display-authors="displayAuthorsVal"
                            :display-common-names="displayCommonNamesVal"
                            :display-synonyms="displaySynonymsVal"
                            :display-vouchers="displayVouchersVal"
                            :editing="taxaEditingActive"
                            :sort-by="selectedSortByOption"
                            :taxa-arr="taxaDisplayDataArr"
                            :voucher-data="checklistVoucherData"
                            @open:checklist-taxa-editor="openChecklistTaxaEditorPopup"
                        ></taxa-list-display>
                    </template>
                    <template v-if="activeTaxaArr.length > taxaPerPage">
                        <div class="q-mb-sm full-width">
                            <q-separator ></q-separator>
                        </div>
                        <div class="q-mb-sm q-px-md full-width row justify-end">
                            <q-pagination v-model="paginationPage" :max="paginationLastPageNumber" direction-links flat color="grey" active-color="primary"></q-pagination>
                        </div>
                        <div class="q-mb-sm full-width">
                            <q-separator ></q-separator>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="column">
                        <div class="q-pa-sm column q-col-gutter-xs">
                            <div class="row justify-start">
                                <div>
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="setQueryPopupDisplay(true);" icon="search" label="Search" />
                                </div>
                            </div>
                        </div>
                        <q-separator ></q-separator>
                        <div class="q-pa-md row justify-center text-h6 text-bold">
                            There are no taxa to display. Click the Search button to enter search criteria to build the taxa checklist.
                        </div>
                    </div>
                </template>
            </div>
            <template v-if="showChecklistTaxaEditorPopup">
                <checklist-taxa-editor-popup
                    :checklist-taxa-id="editChecklistTaxaId"
                    :show-popup="showChecklistTaxaEditorPopup"
                    @close:popup="showChecklistTaxaEditorPopup = false"
                ></checklist-taxa-editor-popup>
            </template>
            <template v-if="displayQueryPopup">
                <search-criteria-popup
                    :show-popup="(displayQueryPopup && !showSpatialPopup)"
                    popup-type="checklist"
                    :show-spatial="true"
                    @open:spatial-popup="openSpatialPopup"
                    @process:build-checklist="buildChecklist"
                    @close:popup="setQueryPopupDisplay(false)"
                ></search-criteria-popup>
            </template>
            <template v-if="showSpatialPopup">
                <spatial-analysis-popup
                    :bottom-lat="spatialInputValues['bottomLatitude']"
                    :circle-arr="spatialInputValues['circleArr']"
                    :left-long="spatialInputValues['leftLongitude']"
                    :point-lat="spatialInputValues['pointLatitude']"
                    :point-long="spatialInputValues['pointLongitude']"
                    :poly-arr="spatialInputValues['polyArr']"
                    :radius="spatialInputValues['radius']"
                    :radius-units="spatialInputValues['radiusUnit']"
                    :right-long="spatialInputValues['rightLongitude']"
                    :upper-lat="spatialInputValues['upperLatitude']"
                    :show-popup="showSpatialPopup"
                    :window-type="popupWindowType"
                    @update:spatial-data="processSpatialData"
                    @close:popup="closeSpatialPopup();"
                ></spatial-analysis-popup>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/determinationRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/geneticLinkRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/colorPicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/copyURLButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/collectionCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceSelectorInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/occurrenceLinkageToolPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/listDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/spatialDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDownloadOptionsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchDataDownloader.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/tableDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/keyDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/checklistDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/imageDisplayButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/advancedQueryBuilder.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCollectionsBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaPopupTabControls.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/searchCriteriaPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRecordsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSelectionsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSymbologyTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanelLeftShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanelTopShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSidePanelShowButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSideButtonTray.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialRasterColorScaleSelect.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialVectorToolsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialPointVectorToolsTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRecordsSymbologyExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialVectorToolsExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialRasterToolsExpansion.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialSidePanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialDrawToolSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialBaseLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/spatialActiveLayerSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialMapSettingsPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialControlPanel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerLayerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerLayerGroupElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerControllerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialLayerQuerySelectorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRow.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/mofDataFieldRowGroup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoTabModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/occurrenceInfoWindowPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialAnalysisPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/taxaListDisplay.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/taxaImageDisplay.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/checklistTaxaVoucherModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/checklistTaxaImageSelectorModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/checklistTaxaAddEditModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/checklists/checklistTaxaEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script>
            const checklistModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'checklist-taxa-editor-popup': checklistTaxaEditorPopup,
                    'search-criteria-popup': searchCriteriaPopup,
                    'selector-input-element': selectorInputElement,
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
                    'spatial-analysis-popup': spatialAnalysisPopup,
                    'taxa-image-display': taxaImageDisplay,
                    'taxa-list-display': taxaListDisplay
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const checklistStore = useChecklistStore();
                    const projectStore = useProjectStore();
                    const searchStore = useSearchStore();

                    const activeTaxaArr = Vue.ref([]);
                    const checklistData = Vue.computed(() => checklistStore.getChecklistData);
                    const checklistImageData = Vue.computed(() => checklistStore.getChecklistImageData);
                    const checklistLocalityText = Vue.computed(() => {
                        let returnVal = null;
                        if((checklistData.value.hasOwnProperty('locality') && checklistData.value['locality']) || (checklistData.value.hasOwnProperty('latcentroid') && checklistData.value['latcentroid'])){
                            if(checklistData.value.hasOwnProperty('locality') && checklistData.value['locality']){
                                returnVal = checklistData.value['locality'];
                            }
                            if(checklistData.value.hasOwnProperty('latcentroid') && checklistData.value['latcentroid']){
                                returnVal = (returnVal ? (returnVal + ' (') : '') + checklistData.value['latcentroid'] + ', ' + checklistData.value['longcentroid'] + (returnVal ? ')' : '');
                            }
                        }
                        return returnVal;
                    });
                    const checklistName = Vue.computed(() => {
                        let returnVal = 'Dynamic Checklist';
                        if(!temporaryChecklist.value && checklistData.value.hasOwnProperty('name') && checklistData.value['name']){
                            returnVal = checklistData.value['name'];
                        }
                        return returnVal;
                    });
                    const checklistVoucherData = Vue.computed(() => checklistStore.getChecklistVoucherData);
                    const clId = Vue.ref(CLID);
                    const clientRoot = baseStore.getClientRoot;
                    const countData = Vue.computed(() => {
                        const returnData = {};
                        const totalArr = [];
                        const speciesArr = [];
                        const generaArr = [];
                        const familyArr = [];
                        activeTaxaArr.value.forEach(taxon => {
                            if(!totalArr.includes(taxon['sciname'])){
                                totalArr.push(taxon['sciname']);
                            }
                            if(taxon['family'] && taxon['family'] !== '[Incertae Sedis]' && !familyArr.includes(taxon['family'])){
                                familyArr.push(taxon['family']);
                            }
                            if(Number(taxon['rankid']) === 180 && !generaArr.includes(taxon['sciname'])){
                                generaArr.push(taxon['sciname']);
                            }
                            else if(Number(taxon['rankid']) >= 220){
                                const unitNameArr = taxon['sciname'].split(' ');
                                if(!generaArr.includes(unitNameArr[0])){
                                    generaArr.push(unitNameArr[0]);
                                }
                                if(Number(taxon['rankid']) === 220 && !speciesArr.includes(taxon['sciname'])){
                                    speciesArr.push(taxon['sciname']);
                                }
                                else if(!speciesArr.includes((unitNameArr[0] + ' ' + unitNameArr[1]))){
                                    speciesArr.push((unitNameArr[0] + ' ' + unitNameArr[1]));
                                }
                            }
                        });
                        returnData['families'] = familyArr.length;
                        returnData['genera'] = generaArr.length;
                        returnData['species'] = speciesArr.length;
                        returnData['total'] = totalArr.length;
                        return returnData;
                    });
                    const displayAuthorsVal = Vue.computed(() => checklistStore.getDisplayAuthors);
                    const displayCommonNamesVal = Vue.computed(() => checklistStore.getDisplayVernaculars);
                    const displayImagesVal = Vue.computed(() => checklistStore.getDisplayImages);
                    const displayQueryPopup = Vue.ref(false);
                    const displaySynonymsVal = Vue.computed(() => checklistStore.getDisplaySynonyms);
                    const displayVouchersVal = Vue.computed(() => checklistStore.getDisplayVouchers);
                    const editChecklistTaxaId = Vue.ref(0);
                    const isEditor = Vue.ref(false);
                    const keyModuleIsActive = baseStore.getKeyModuleIsActive;
                    const mapViewUrl = Vue.computed(() => {
                        return (clientRoot + '/spatial/index.php?starr={"clid":"' + clId.value + '"}');
                    });
                    const paginatedTaxaArr = Vue.computed(() => {
                        let returnArr;
                        if(activeTaxaArr.value.length > taxaPerPage){
                            let endIndex = activeTaxaArr.value.length;
                            const index = (paginationPage.value - 1) * taxaPerPage;
                            if(activeTaxaArr.value.length > (index + taxaPerPage)){
                                endIndex = index + taxaPerPage;
                            }
                            returnArr = activeTaxaArr.value.slice(index, endIndex);
                        }
                        else{
                            returnArr = activeTaxaArr.value.slice();
                        }
                        return returnArr;
                    });
                    const paginationLastPageNumber = Vue.computed(() => {
                        let lastPage = 1;
                        if(activeTaxaArr.value.length > taxaPerPage){
                            lastPage = Math.floor(activeTaxaArr.value.length / taxaPerPage);
                        }
                        if(activeTaxaArr.value.length % taxaPerPage){
                            lastPage++;
                        }
                        return lastPage;
                    });
                    const paginationPage = Vue.ref(1);
                    const pId = Vue.ref(PID);
                    const popupWindowType = Vue.ref(null);
                    const projectData = Vue.computed(() => projectStore.getProjectData);
                    const projectName = Vue.computed(() => {
                        return projectData.value.hasOwnProperty('projname') ? projectData.value['projname'] : '';
                    });
                    const queryId = QUERYID;
                    const selectedSortByOption = Vue.computed(() => checklistStore.getDisplaySortVal);
                    const showChecklistTaxaEditorPopup = Vue.ref(false);
                    const showMoreDescription = Vue.computed(() => checklistStore.getDisplayDetails);
                    const showSpatialPopup = Vue.ref(false);
                    const sortByOptions = Vue.computed(() => checklistStore.getDisplaySortByOptions);
                    const spatialInputValues = Vue.computed(() => searchStore.getSpatialInputValues);
                    const taxaDataArr = Vue.computed(() => checklistStore.getChecklistTaxaArr);
                    const taxaDisplayDataArr = Vue.computed(() => {
                        const newDataArr = [];
                        if(paginatedTaxaArr.value.length > 0){
                            paginatedTaxaArr.value.forEach(taxon => {
                                if(selectedSortByOption.value === 'family'){
                                    const familyObj = newDataArr.find(family => family['familyName'] === taxon['family']);
                                    if(familyObj){
                                        familyObj['taxa'].push(taxon);
                                    }
                                    else{
                                        const taxaArr = [taxon];
                                        newDataArr.push({
                                            familyName: taxon['family'],
                                            taxa: taxaArr
                                        });
                                    }
                                }
                                else{
                                    newDataArr.push(taxon);
                                }
                            });
                            if(selectedSortByOption.value === 'family'){
                                newDataArr.sort((a, b) => {
                                    return a['familyName'].localeCompare(b['familyName']);
                                });
                                newDataArr.forEach(family => {
                                    family['taxa'].sort((a, b) => {
                                        return a['sciname'].localeCompare(b['sciname']);
                                    });
                                });
                            }
                            else{
                                newDataArr.sort((a, b) => {
                                    return a['sciname'].localeCompare(b['sciname']);
                                });
                            }
                        }
                        return newDataArr.slice();
                    });
                    const taxaEditingActive = Vue.ref(false);
                    const taxaFilterOptions = Vue.computed(() => checklistStore.getTaxaFilterOptions);
                    const taxaPerPage = 500;
                    const taxonFilterVal = Vue.computed(() => checklistStore.getDisplayTaxonFilterVal);
                    const temporaryChecklist = Vue.computed(() => {
                        let returnVal = false;
                        if(checklistData.value.hasOwnProperty('clid') && Number(checklistData.value['clid']) > 0 && checklistData.value['expiration']){
                            returnVal = true;
                        }
                        return returnVal;
                    });
                    const validUser = baseStore.getValidUser;

                    Vue.watch(taxaDataArr, () => {
                        setActiveTaxa();
                    });

                    function buildChecklist(){
                        if(searchStore.getSearchTermsValid){
                            showWorking('Loading...');
                            const options = {
                                schema: 'occurrence',
                                spatial: 0
                            };
                            searchStore.getSearchTidArr(options, (tidArr) => {
                                if(tidArr.length > 0){
                                    checklistStore.createTemporaryChecklistFromTidArr(tidArr, (res) => {
                                        hideWorking();
                                        if(Number(res) > 0){
                                            setQueryPopupDisplay(false);
                                            clId.value = Number(res);
                                            setChecklistData();
                                        }
                                        else{
                                            showNotification('negative', 'An error occurred while creating the checklist.');
                                        }
                                    });
                                }
                                else{
                                    hideWorking();
                                    showNotification('negative', 'There were no taxa matching your criteria.');
                                }
                            });
                        }
                        else{
                            showNotification('negative', 'Please enter search criteria.');
                        }
                    }

                    function closeSpatialPopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        searchStore.clearSpatialInputValues();
                    }

                    function downloadChecklist(type) {
                        showWorking();
                        checklistStore.processDownloadRequest(checklistName.value, type, (filename, dataBlob) => {
                            hideWorking();
                            if(dataBlob !== null){
                                const objectUrl = window.URL.createObjectURL(dataBlob);
                                const anchor = document.createElement('a');
                                anchor.href = objectUrl;
                                anchor.download = filename;
                                document.body.appendChild(anchor);
                                anchor.click();
                                anchor.remove();
                            }
                        });
                    }

                    function openChecklistTaxaEditorPopup(id) {
                        editChecklistTaxaId.value = id;
                        showChecklistTaxaEditorPopup.value = true;
                    }

                    function openSpatialPopup(type) {
                        searchStore.setSpatialInputValues();
                        popupWindowType.value = type;
                        showSpatialPopup.value = true;
                    }

                    function processDisplayAuthorsChange(value) {
                        checklistStore.setDisplayAuthors(value);
                    }

                    function processDisplayCommonNameChange(value) {
                        checklistStore.setDisplayVernaculars(value);
                    }

                    function processDisplayDetailsChange(value) {
                        checklistStore.setDisplayDetails(value);
                    }

                    function processDisplayImagesChange(value) {
                        checklistStore.setDisplayImages(value);
                    }

                    function processDisplaySynonymsChange(value) {
                        checklistStore.setDisplaySynonyms(value);
                    }

                    function processDisplayVouchersChange(value) {
                        checklistStore.setDisplayVouchers(value);
                    }

                    function processSortByChange(value) {
                        checklistStore.setDisplaySortVal(value);
                        sortActiveTaxa();
                        paginationPage.value = 1;
                    }

                    function processSpatialData(data) {
                        searchStore.processSpatialPopupData(popupWindowType.value, data);
                    }

                    function processTaxonFilterValChange(taxon) {
                        checklistStore.setDisplayTaxonFilterVal(taxon);
                        setActiveTaxa();
                        paginationPage.value = 1;
                    }

                    function saveTemporaryChecklist() {
                        checklistStore.saveTemporaryChecklist(searchStore.getSearchTermsJson, (res) => {
                            if(Number(res) === 1){
                                showNotification('positive','Checklist saved.');
                                setEditor();
                            }
                            else{
                                showNotification('negative', 'An error occurred while saving the checklist.');
                            }
                        });
                    }

                    function setActiveTaxa() {
                        const newActiveTaxaArr = [];
                        taxaDataArr.value.forEach(taxon => {
                            let includeTaxon = false;
                            if(taxonFilterVal.value){
                                if(Number(taxonFilterVal.value['rankid']) === 140 && taxon['family'] === taxonFilterVal.value['sciname']){
                                    includeTaxon = true;
                                }
                                else if(Number(taxonFilterVal.value['rankid']) > 140 && (taxon['sciname'] === taxonFilterVal.value['sciname'] || taxon['sciname'].startsWith((taxonFilterVal.value['sciname'] + ' ')))){
                                    includeTaxon = true;
                                }
                            }
                            else{
                                includeTaxon = true;
                            }
                            if(includeTaxon){
                                newActiveTaxaArr.push(taxon);
                            }
                        });
                        activeTaxaArr.value = newActiveTaxaArr.slice();
                        sortActiveTaxa();
                    }

                    function setChecklistData() {
                        showWorking();
                        setEditor();
                        checklistStore.setChecklist(clId.value, (clid) => {
                            hideWorking();
                            if(Number(clid) > 0){
                                checklistStore.setChecklistTaxaArr(false, true, true, () => {
                                    setActiveTaxa();
                                    checklistStore.setChecklistImageData(1);
                                    checklistStore.setChecklistVoucherData();
                                });
                            }
                        });
                    }

                    function setEditor() {
                        if(Number(clId.value) > 0){
                            const formData = new FormData();
                            formData.append('permission', 'ClAdmin');
                            formData.append('key', clId.value.toString());
                            formData.append('action', 'validatePermission');
                            fetch(permissionApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                return response.ok ? response.json() : null;
                            })
                            .then((resData) => {
                                isEditor.value = resData.includes('ClAdmin');
                            });
                        }
                    }

                    function setProjectData() {
                        projectStore.setProject(pId.value, (pid) => {
                            if(Number(pid) > 0){
                                checklistStore.setClidArr(projectData.value['clidArr']);
                                checklistStore.setChecklistTaxaArr(false, true, true);
                            }
                            else{
                                showNotification('negative', 'An error occurred while setting the project data.');
                            }
                        });
                    }

                    function setQueryPopupDisplay(val) {
                        displayQueryPopup.value = val;
                    }

                    function sortActiveTaxa() {
                        if(selectedSortByOption.value === 'family'){
                            activeTaxaArr.value.sort((a, b) => {
                                return a['family'].localeCompare(b['family']) || a['sciname'].localeCompare(b['sciname']);
                            });
                        }
                        else{
                            activeTaxaArr.value.sort((a, b) => {
                                return a['sciname'].localeCompare(b['sciname']);
                            });
                        }
                    }

                    Vue.onMounted(() => {
                        if(Number(clId.value) > 0){
                            setChecklistData();
                        }
                        else if(Number(pId.value) > 0){
                            setProjectData();
                        }
                        else{
                            if(Number(queryId) === 0){
                                displayQueryPopup.value = true;
                            }
                            searchStore.initializeSearchStorage(queryId);
                        }
                    });

                    return {
                        activeTaxaArr,
                        checklistData,
                        checklistImageData,
                        checklistLocalityText,
                        checklistName,
                        checklistVoucherData,
                        clId,
                        clientRoot,
                        countData,
                        displayAuthorsVal,
                        displayCommonNamesVal,
                        displayImagesVal,
                        displayQueryPopup,
                        displaySynonymsVal,
                        displayVouchersVal,
                        editChecklistTaxaId,
                        isEditor,
                        keyModuleIsActive,
                        mapViewUrl,
                        paginationLastPageNumber,
                        paginationPage,
                        pId,
                        popupWindowType,
                        projectData,
                        projectName,
                        selectedSortByOption,
                        showChecklistTaxaEditorPopup,
                        showMoreDescription,
                        showSpatialPopup,
                        sortByOptions,
                        spatialInputValues,
                        taxaDataArr,
                        taxaDisplayDataArr,
                        taxaEditingActive,
                        taxaFilterOptions,
                        taxaPerPage,
                        taxonFilterVal,
                        temporaryChecklist,
                        validUser,
                        buildChecklist,
                        closeSpatialPopup,
                        downloadChecklist,
                        openChecklistTaxaEditorPopup,
                        openSpatialPopup,
                        processDisplayAuthorsChange,
                        processDisplayCommonNameChange,
                        processDisplayDetailsChange,
                        processDisplayImagesChange,
                        processDisplaySynonymsChange,
                        processDisplayVouchersChange,
                        processSortByChange,
                        processSpatialData,
                        processTaxonFilterValChange,
                        saveTemporaryChecklist,
                        setQueryPopupDisplay
                    }
                }
            });
            checklistModule.use(Quasar, { config: {} });
            checklistModule.use(Pinia.createPinia());
            checklistModule.mount('#mainContainer');
        </script>
    </body>
</html>
