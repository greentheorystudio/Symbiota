<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
?>
<!DOCTYPE html>
    <html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Add/Edit Collection Profile</title>
        <meta name="description" content="Add or edit a collection profile in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol.css?ver=10.8.1" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/ol-ext.min.css?ver=20240115" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ol.js?ver=10.8.1" type="text/javascript"></script>
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
            const COLLID = <?php echo $collid; ?>;
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <template v-if="collectionId > 0">
                    <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collectionId)" tabindex="0">Collection Control Panel</a> &gt;&gt;
                    <span class="text-bold">Metadata & Settings</span>
                </template>
                <template v-else>
                    <span class="text-bold">Create New Collection Profile</span>
                </template>
            </div>
            <template v-if="collectionId > 0">
                <div class="q-px-md text-h5 text-bold">{{ collectionData['collectionname'] + (collectionData['institutioncode'] ? (' (' + collectionData['institutioncode'] + ')') : '') }}</div>
            </template>
            <div class="q-px-md q-pt-sm q-pb-md column q-gutter-sm">
                <template v-if="isAdmin || isEditor">
                    <q-card flat bordered>
                        <q-card-section class="column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="collectionId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="collectionId > 0">
                                        <q-btn color="secondary" @click="saveCollectionEdits();" label="Save Edits" :disabled="!editsExist || !collectionValid || !collectionNameValid || !collectionCodesValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="processCreateCollectionRecord();" label="Create Collection" :disabled="!collectionValid || !collectionNameValid || !collectionCodesValid" aria-label="Create collection" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row q-col-gutter-sm">
                                <div class="col-12 col-sm-4">
                                    <text-field-input-element :definition="collectionFieldDefinitions['institutioncode']" label="Institution Code" maxlength="45" :value="collectionData.institutioncode" @update:value="(value) => updateCollectionData('institutioncode', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <single-country-auto-complete label="Country" :value="collectionData['country']" @update:value="processCountryChange"></single-country-auto-complete>
                                </div>
                                <div>
                                    <q-btn color="primary" @click="checkGBIF();" label="Check GBIF" :disabled="!collectionData['institutioncode'] || !collectionData['countrycode']" aria-label="Check GBIF" tabindex="0" />
                                </div>
                            </div>
                            <div class="row q-col-gutter-sm">
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element label="Collection Name" maxlength="150" :value="collectionData['collectionname']" @update:value="(value) => updateCollectionData('collectionname', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <text-field-input-element :definition="collectionFieldDefinitions['collectioncode']" label="Collection Code" maxlength="45" :value="collectionData['collectioncode']" @update:value="(value) => updateCollectionData('collectioncode', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-3 self-center">
                                    <checkbox-input-element label="Is Public" :value="collectionData['ispublic']" @update:value="(value) => updateCollectionData('ispublic', (Number(value) === 1 ? '1' : '0'))"></checkbox-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Description" :value="collectionData['fulldescription']" maxlength="2000" :show-counter="true" @update:value="(value) => updateCollectionData('fulldescription', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Homepage" :value="collectionData['homepage']" maxlength="250" @update:value="(value) => updateCollectionData('homepage', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element label="Contact" maxlength="250" :value="collectionData['contact']" @update:value="(value) => updateCollectionData('contact', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element label="Email" maxlength="45" :value="collectionData['email']" @update:value="(value) => updateCollectionData('email', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-start q-gutter-sm no-wrap">
                                <div class="col-3">
                                    <text-field-input-element data-type="number" label="Latitude" :value="collectionData['latitudedecimal']" @update:value="(value) => updateCollectionData('latitudedecimal', value)"></text-field-input-element>
                                </div>
                                <div class="col-3">
                                    <text-field-input-element data-type="number" label="Longitude" :value="collectionData['longitudedecimal']" @update:value="(value) => updateCollectionData('longitudedecimal', value)"></text-field-input-element>
                                </div>
                                <div class="col-1 self-center">
                                    <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openSpatialPopup('input-point');" icon="fas fa-globe" dense aria-label="Open Mapping Aid" tabindex="0">
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Open Mapping Aid
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </div>
                            <template v-if="collectionCategoryArr.length > 0">
                                <div class="row">
                                    <div class="col-grow">
                                        <selector-input-element label="Category" :options="collectionCategoryArr" option-value="ccpk" option-label="category" :value="collectionData['ccpk']" @update:value="(value) => updateCollectionData('ccpk', value)"></selector-input-element>
                                    </div>
                                </div>
                            </template>
                            <div class="row">
                                <div class="col-grow column">
                                    <selector-input-element :definition="collectionFieldDefinitions['rights']" label="Rights" :options="rightsTermsOptions" option-value="baseUrl" option-label="title" :value="collectionData['rights']" @update:value="(value) => updateCollectionData('rights', value)"></selector-input-element>
                                    <q-card v-if="selectedRightsTerm" flat bordered class="q-mt-xs q-mx-md bg-grey-2">
                                        <q-card-section class="q-pa-xs column">
                                            <div>{{ selectedRightsTerm['def'] }}</div>
                                            <div class="row q-gutter-sm">
                                                <a class="text-bold" :href="collectionData['rights']" target="_blank" aria-label="View usage rights - Opens in separate tab" tabindex="0">
                                                    [Full text]
                                                </a>
                                                <a class="text-bold" :href="selectedRightsTerm['url']" target="_blank" aria-label="View usage rights legal code - Opens in separate tab" tabindex="0">
                                                    [Full legal code]
                                                </a>
                                            </div>
                                        </q-card-section>
                                    </q-card>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element :definition="collectionFieldDefinitions['rightsholder']" label="Rights Holder" maxlength="250" :value="collectionData['rightsholder']" @update:value="(value) => updateCollectionData('rightsholder', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element :definition="collectionFieldDefinitions['accessrights']" label="Access Rights" maxlength="250" :value="collectionData['accessrights']" @update:value="(value) => updateCollectionData('accessrights', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-3">
                                    <selector-input-element :definition="collectionFieldDefinitions['colltype']" label="Dataset Type" :options="datasetTypeOptions" :value="collectionData['colltype']" @update:value="(value) => updateCollectionData('colltype', value)"></selector-input-element>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <selector-input-element :definition="collectionFieldDefinitions['managementtype']" label="Data Management" :options="dataManagementOptions" :value="collectionData['managementtype']" @update:value="(value) => updateCollectionData('managementtype', value)"></selector-input-element>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <selector-input-element label="Data Recording Method" :options="dataRecordingFormatOptions" :value="collectionData['datarecordingmethod']" @update:value="(value) => updateCollectionData('datarecordingmethod', value)"></selector-input-element>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <selector-input-element :definition="collectionFieldDefinitions['guidtarget']" label="GUID Source" :options="guidSourceOptions" :value="collectionData['guidtarget']" @update:value="(value) => updateCollectionData('guidtarget', value)"></selector-input-element>
                                </div>
                            </div>
                            <template v-if="collectionData['datarecordingmethod'] === 'replicate' || gbifPublishingConfigured">
                                <div class="row justify-between q-col-gutter-sm">
                                    <div v-if="collectionData['datarecordingmethod'] === 'replicate'" class="col-12 col-sm-6">
                                        <text-field-input-element data-type="int" label="Default Rep Count" min-value="1" :value="collectionData['defaultrepcount']" @update:value="(value) => updateCollectionData('defaultrepcount', value)"></text-field-input-element>
                                    </div>
                                    <div v-if="gbifPublishingConfigured" class="col-12 col-sm-6">
                                        <checkbox-input-element :definition="collectionFieldDefinitions['publishtogbif']" label="Publish to GBIF" :value="collectionData['publishtogbif']" @update:value="(value) => updateCollectionData('publishtogbif', (Number(value) === 1 ? '1' : '0'))"></checkbox-input-element>
                                    </div>
                                </div>
                            </template>
                            <template v-if="collectionId > 0">
                                <div class="q-mt-sm column">
                                    <div class="text-subtitle1"><span class="text-bold q-mr-sm">Security Key:</span>{{ collectionData['securitykey'] }}</div>
                                    <div class="text-subtitle1"><span class="text-bold q-mr-sm">Collection ID:</span>{{ collectionData['collectionguid'] }}</div>
                                </div>
                            </template>
                        </q-card-section>
                    </q-card>
                    <q-card v-if="collectionId > 0" flat bordered>
                        <q-card-section>
                            <div class="text-h6 text-bold">Collection Icon</div>
                            <div class="fit row justify-between">
                                <div class="col-6 q-pa-md">
                                    <template v-if="collectionData['icon']">
                                        <div class="fit">
                                            <div class="row justify-between q-gutter-xs">
                                                <div class="col-9 row justify-center">
                                                    <q-img :src="(collectionData['icon'].startsWith('/') ? (clientRoot + collectionData['icon']) : collectionData['icon'])" :height="imageHeight" fit="scale-down"></q-img>
                                                </div>
                                                <div class="row justify-end q-gutter-xs">
                                                    <div>
                                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="deleteCollectionIcon();" icon="far fa-trash-alt" dense aria-label="Remove icon image" tabindex="0">
                                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                Remove icon image
                                                            </q-tooltip>
                                                        </q-btn>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="fit row justify-center">
                                            <span class="col-8 text-subtitle1 text-bold">An icon has not been uploaded</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="col-6 column q-gutter-sm">
                                    <q-card flat bordered>
                                        <q-card-section class="column q-gutter-sm">
                                            <div class="row justify-between q-gutter-xs">
                                                <div class="text-subtitle1 text-bold">Upload a{{ (collectionData['icon'] ? ' new ' : ' ') }}collection icon image</div>
                                                <q-btn-toggle v-model="selectedUploadMethod" :options="uploadMethodOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" aria-label="Upload method" tabindex="0"></q-btn-toggle>
                                            </div>
                                            <div v-if="selectedUploadMethod === 'file'" class="row">
                                                <div class="col-grow">
                                                    <file-picker-input-element label="Icon Image File" :accepted-types="acceptedFileTypes" :value="uploadedFile" :validate-file-size="true" @update:file="(value) => uploadedFile = value[0]"></file-picker-input-element>
                                                </div>
                                            </div>
                                            <div v-if="selectedUploadMethod === 'url'" class="row">
                                                <div class="col-grow">
                                                    <text-field-input-element data-type="textarea" label="URL" :value="imageIconUrl" @update:value="(value) => imageIconUrl = value"></text-field-input-element>
                                                </div>
                                            </div>
                                            <div class="row justify-end">
                                                <q-btn color="secondary" @click="processCollectionIconImageUpload();" label="Upload" :disabled="!uploadedFile && !imageIconUrl" aria-label="Upload collection icon image" tabindex="0" />
                                            </div>
                                        </q-card-section>
                                    </q-card>
                                </div>
                            </div>
                        </q-card-section>
                    </q-card>
                    <q-card v-if="collectionId > 0" flat bordered>
                        <q-card-section>
                            <div class="text-h6 text-bold">Location</div>
                            <div class="fit row justify-between">
                                <div class="col-6 q-pa-md">
                                    <template v-if="collectionData['institutionname']">
                                        <div class="fit q-pl-md">
                                            <div class="row justify-between q-gutter-xs">
                                                <div class="column">
                                                    <div>{{ collectionData['institutionname'] }}</div>
                                                    <div v-if="collectionData['institutionname2']">{{ collectionData['institutionname2'] }}</div>
                                                    <div v-if="collectionData['address1']">{{ collectionData['address1'] }}</div>
                                                    <div v-if="collectionData['address2']">{{ collectionData['address2'] }}</div>
                                                    <div v-if="collectionData['city'] || collectionData['stateprovince'] || collectionData['postalcode']">
                                                        {{ (collectionData['city'] ? (collectionData['city'] + (collectionData['stateprovince'] ? ', ' : ' ')) : '') + (collectionData['stateprovince'] ? (collectionData['stateprovince'] + (collectionData['postalcode'] ? ' ' : '')) : '') + (collectionData['postalcode'] ? collectionData['postalcode'] : '') }}
                                                    </div>
                                                    <div v-if="collectionData['country']">{{ collectionData['country'] }}</div>
                                                </div>
                                                <div class="row justify-end q-gutter-xs">
                                                    <div>
                                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openInstitutionEditorPopup(collectionData['iid']);" icon="far fa-edit" dense aria-label="Open location editor" tabindex="0">
                                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                Edit location
                                                            </q-tooltip>
                                                        </q-btn>
                                                    </div>
                                                    <div>
                                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="deleteInstitutionLinkage();" icon="far fa-trash-alt" dense aria-label="Remove location linkage" tabindex="0">
                                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                Remove location linkage
                                                            </q-tooltip>
                                                        </q-btn>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="fit row justify-center">
                                            <span class="col-8 text-subtitle1 text-bold">A location has not been linked</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="col-6 column q-gutter-sm">
                                    <q-card flat bordered>
                                        <q-card-section class="column q-gutter-sm">
                                            <div class="text-subtitle1 text-bold">Link to a{{ (Number(collectionData['iid']) > 0 ? ' different ' : ' ') }}location</div>
                                            <div class="row">
                                                <div class="col-grow">
                                                    <single-location-auto-complete label="Location Name" :value="locationNameVal" @update:value="processLocationValueChange"></single-location-auto-complete>
                                                </div>
                                            </div>
                                            <div class="row justify-end">
                                                <div>
                                                    <q-btn color="primary" @click="openInstitutionEditorPopup(0);" label="Add New Location" aria-label="Create Location" tabindex="0" />
                                                </div>
                                            </div>
                                        </q-card-section>
                                    </q-card>
                                </div>
                            </div>
                        </q-card-section>
                    </q-card>
                </template>
            </div>
            <template v-if="showSpatialPopup">
                <spatial-analysis-popup
                    :decimal-latitude="decimalLatitudeValue"
                    :decimal-longitude="decimalLongitudeValue"
                    :show-popup="showSpatialPopup"
                    :window-type="popupWindowType"
                    @update:spatial-data="processSpatialData"
                    @close:popup="closeSpatialPopup();"
                ></spatial-analysis-popup>
            </template>
            <template v-if="showInstitutionEditorPopup">
                <institutions-editor-popup
                    :institution-id="editInstitutionId"
                    :show-popup="showInstitutionEditorPopup"
                    @update:institution="processLocationUpdate"
                    @close:popup="closeInstitutionEditorPopup();"
                ></institutions-editor-popup>
            </template>
            <template v-if="showGbifInstitutionCollectionListPopup">
                <gbif-institution-collection-list-popup
                    :data-arr="gbifCollectionArr"
                    :show-popup="showGbifInstitutionCollectionListPopup"
                    @update:data="setCollectionData"
                    @close:popup="showGbifInstitutionCollectionListPopup = false"
                ></gbif-institution-collection-list-popup>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/institution.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageCarousel.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/imageRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/media/mediaRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/determinationRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/occurrences/geneticLinkRecordInfoBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/colorPicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/search/copyURLButton.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/collectionCheckboxSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/dateInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/multipleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/computedValueInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleCountryAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/wysiwygInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/userPermissionManagementModule.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/spatial/spatialViewerPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/filePickerInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleStateProvinceAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleLocationAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/gbifInstitutionCollectionListPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/institutionEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const collectionMetadataSettingsModule = Vue.createApp({
                components: {
                    'checkbox-input-element': checkboxInputElement,
                    'file-picker-input-element': filePickerInputElement,
                    'gbif-institution-collection-list-popup': gbifInstitutionCollectionListPopup,
                    'institutions-editor-popup': institutionEditorPopup,
                    'selector-input-element': selectorInputElement,
                    'single-country-auto-complete': singleCountryAutoComplete,
                    'single-location-auto-complete': singleLocationAutoComplete,
                    'spatial-analysis-popup': spatialAnalysisPopup,
                    'text-field-input-element': textFieldInputElement
                },
                setup() {
                    const { hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();
                    const collectionStore = useCollectionStore();

                    const acceptedFileTypes = ['jpg','jpeg','png'];
                    const clientRoot = baseStore.getClientRoot;
                    const collectionCategoryArr = Vue.ref([]);
                    const collectionCodesValid = Vue.ref(true);
                    const collectionData = Vue.computed(() => collectionStore.getCollectionData);
                    const collectionFieldDefinitions = Vue.computed(() => collectionStore.getCollectionFieldDefinitions);
                    const collectionId = Vue.computed(() => collectionStore.getCollectionId);
                    const collectionNameValid = Vue.ref(true);
                    const collectionValid = Vue.computed(() => collectionStore.getCollectionValid);
                    const collId = COLLID;
                    const dataManagementOptions = [
                        {value: 'Live Data', label: 'Live Data'},
                        {value: 'Snapshot', label: 'Snapshot'}
                    ];
                    const dataRecordingFormatOptions = [
                        {value: 'specimen', label: 'Specimen'},
                        {value: 'observation', label: 'Observation'},
                        {value: 'skeletal', label: 'Skeletal'},
                        {value: 'lot', label: 'Lot'},
                        {value: 'replicate', label: 'Replicate'}
                    ];
                    const datasetTypeOptions = [
                        {value: 'PreservedSpecimen', label: 'Preserved Specimens'},
                        {value: 'HumanObservation', label: 'Observations'},
                        {value: 'FossilSpecimen', label: 'Fossil Specimens'},
                        {value: 'LivingSpecimen', label: 'Living Specimens'},
                        {value: 'MaterialSample', label: 'Material Samples'}
                    ];
                    const decimalLatitudeValue = Vue.ref(null);
                    const decimalLongitudeValue = Vue.ref(null);
                    const editInstitutionId = Vue.ref(null);
                    const editsExist = Vue.computed(() => collectionStore.getCollectionEditsExist);
                    const gbifCollectionArr = Vue.ref([]);
                    const gbifPublishingConfigured = baseStore.getGbifPublishingConfigured;
                    const guidSourceOptions = [
                        {value: 'symbiotaUUID', label: 'Generated GUID (UUID)'},
                        {value: 'occurrenceId', label: 'Occurrence ID'}
                    ];
                    const imageHeight = Vue.ref('200px');
                    const imageIconUrl = Vue.ref(null);
                    const isAdmin = Vue.ref(false);
                    const isEditor = Vue.computed(() => {
                        return collectionStore.getCollectionPermissions.includes('CollAdmin');
                    });
                    const locationNameVal = Vue.ref(null);
                    const newLocationData = Vue.ref({});
                    const popupWindowType = Vue.ref(null);
                    const rightsTermsOptions = baseStore.getRightsTermsOptions;
                    const selectedRightsTerm = Vue.computed(() => {
                        return rightsTermsOptions.find(term => collectionData.value['rights'] === term['baseUrl']);
                    });
                    const selectedUploadMethod = Vue.ref('file');
                    const showGbifInstitutionCollectionListPopup = Vue.ref(false);
                    const showInstitutionEditorPopup = Vue.ref(false);
                    const showSpatialPopup = Vue.ref(false);
                    const uploadedFile = Vue.ref(null);
                    const uploadMethodOptions = [
                        {label: 'File', value: 'file'},
                        {label: 'URL', value: 'url'}
                    ];

                    Vue.watch(selectedUploadMethod, () => {
                        if(selectedUploadMethod.value === 'url'){
                            uploadedFile.value = null;
                        }
                        else{
                            imageIconUrl.value = null;
                        }
                    });

                    function checkGBIF() {
                        newLocationData.value = Object.assign({}, {});
                        showWorking();
                        gbifCollectionArr.value.length = 0;
                        const url = 'https://api.gbif.org/v1/grscicoll/search?q=' + collectionData.value['institutioncode'] + '&hl=false&country=' + collectionData.value['countrycode'];
                        fetch(url, {
                            method: 'GET'
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((data) => {
                            hideWorking();
                            if(data){
                                gbifCollectionArr.value = data;
                                showGbifInstitutionCollectionListPopup.value = true;
                            }
                            else{
                                showNotification('negative', 'No collections could be found matching that code.');
                            }
                        });
                    }

                    function clearSpatialInputValues() {
                        decimalLatitudeValue.value = null;
                        decimalLongitudeValue.value = null;
                    }

                    function closeInstitutionEditorPopup() {
                        editInstitutionId.value = null;
                        showInstitutionEditorPopup.value = false;
                    }

                    function closeSpatialPopup() {
                        popupWindowType.value = null;
                        showSpatialPopup.value = false;
                        clearSpatialInputValues();
                    }

                    function createCollectionRecord() {
                        collectionStore.createCollectionRecord((newCollId) => {
                            if(newCollId > 0){
                                showNotification('positive','Collection created successfully.');
                            }
                            else{
                                showNotification('negative', 'There was an error creating the new collection.');
                            }
                        });
                    }

                    function createLocationRecord() {
                        const formData = new FormData();
                        formData.append('institution', JSON.stringify(newLocationData.value));
                        formData.append('action', 'createInstitutionRecord');
                        fetch(institutionsApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            if(Number(res) > 0){
                                updateCollectionData('iid', res);
                            }
                            createCollectionRecord();
                        });
                    }

                    function deleteCollectionIcon() {
                        showWorking();
                        collectionStore.deleteCollectionIconRecord((res) => {
                            hideWorking();
                            if(Number(res) === 1){
                                showNotification('positive','Collection icon deleted.');
                            }
                            else{
                                showNotification('negative', 'There was an error deleting the collection icon.');
                            }
                        });
                    }

                    function deleteInstitutionLinkage() {
                        updateCollectionData('iid', null);
                        updateCollectionData('institutionname', null);
                        updateCollectionData('institutionname2', null);
                        updateCollectionData('address1', null);
                        updateCollectionData('address2', null);
                        updateCollectionData('city', null);
                        updateCollectionData('stateprovince', null);
                        updateCollectionData('postalcode', null);
                        updateCollectionData('country', null);
                        if(collectionStore.getCollectionEditsExist){
                            saveCollectionEdits();
                        }
                    }

                    function openInstitutionEditorPopup(iid) {
                        editInstitutionId.value = iid;
                        showInstitutionEditorPopup.value = true;
                    }

                    function openSpatialPopup(type) {
                        setSpatialInputValues();
                        popupWindowType.value = type;
                        showSpatialPopup.value = true;
                    }

                    function processCollectionIconImageUpload() {
                        if(collectionData.value['icon'] && collectionData.value['icon'].startsWith('/')){
                            collectionStore.deleteCollectionIconRecord(() => {
                                uploadCollecctionIcon();
                            });
                        }
                        else{
                            uploadCollecctionIcon();
                        }
                    }

                    function processCountryChange(countryObj) {
                        if(countryObj){
                            updateCollectionData('country', countryObj['iso']);
                            updateCollectionData('countrycode', countryObj['iso']);
                        }
                        else{
                            updateCollectionData('country', null);
                            updateCollectionData('countrycode', null);
                        }
                    }

                    function processCreateCollectionRecord() {
                        if(Object.keys(newLocationData.value).length > 0 && newLocationData.value.hasOwnProperty('institutionname') && newLocationData.value['institutionname']){
                            validateLocationRecord();
                        }
                        else{
                            createCollectionRecord();
                        }
                    }

                    function processLocationUpdate(locationObj) {
                        showInstitutionEditorPopup.value = false;
                        if(locationObj && locationObj.hasOwnProperty('iid') && Number(locationObj['iid']) > 0){
                            updateCollectionData('iid', locationObj['iid']);
                            updateCollectionData('institutionname', locationObj['institutionname']);
                            updateCollectionData('institutionname2', locationObj['institutionname2']);
                            updateCollectionData('address1', locationObj['address1']);
                            updateCollectionData('address2', locationObj['address2']);
                            updateCollectionData('city', locationObj['city']);
                            updateCollectionData('stateprovince', locationObj['stateprovince']);
                            updateCollectionData('postalcode', locationObj['postalcode']);
                            updateCollectionData('country', locationObj['country']);
                            if(collectionStore.getCollectionEditsExist){
                                saveCollectionEdits();
                            }
                        }
                    }

                    function processLocationValueChange(locationObj) {
                        if(locationObj){
                            locationNameVal.value = locationObj['label'];
                            processLocationUpdate(locationObj);
                        }
                        else{
                            locationNameVal.value = null;
                        }
                    }

                    function processSpatialData(data) {
                        const latDecimalPlaces = (collectionData.value.hasOwnProperty('latitudedecimal') && collectionData.value['latitudedecimal']) ? collectionData.value['latitudedecimal'].toString().split('.')[1].length : null;
                        const longDecimalPlaces = (collectionData.value.hasOwnProperty('longitudedecimal') && collectionData.value['longitudedecimal']) ? collectionData.value['longitudedecimal'].toString().split('.')[1].length : null;
                        if(!latDecimalPlaces || Number(collectionData.value['latitudedecimal']) !== Number(Number(data['decimalLatitude']).toFixed(latDecimalPlaces))){
                            updateCollectionData('latitudedecimal', data['decimalLatitude']);
                        }
                        if(!longDecimalPlaces || Number(collectionData.value['longitudedecimal']) !== Number(Number(data['decimalLongitude']).toFixed(longDecimalPlaces))){
                            updateCollectionData('longitudedecimal', data['decimalLongitude']);
                        }
                    }

                    function saveCollectionEdits() {
                        showWorking('Saving edits...');
                        collectionStore.updateCollectionRecord((res) => {
                            hideWorking();
                            if(res === 1){
                                showNotification('positive','Edits saved.');
                            }
                            else{
                                showNotification('negative', 'There was an error saving the collection edits.');
                            }
                        });
                    }

                    function setCollectionCategories() {
                        const formData = new FormData();
                        formData.append('action', 'getCollectionCategoryArr');
                        fetch(collectionCategoryApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => response.json())
                        .then((result) => {
                            collectionCategoryArr.value = result;
                        });
                    }

                    function setCollectionData(data) {
                        showGbifInstitutionCollectionListPopup.value = false;
                        updateCollectionData('institutioncode', (data['collection'].hasOwnProperty('institutioncode') ? data['collection']['institutioncode'] : null));
                        updateCollectionData('collectioncode', (data['collection'].hasOwnProperty('collectioncode') ? data['collection']['collectioncode'] : null));
                        updateCollectionData('collectionname', (data['collection'].hasOwnProperty('collectionname') ? data['collection']['collectionname'] : null));
                        updateCollectionData('fulldescription', (data['collection'].hasOwnProperty('fulldescription') ? data['collection']['fulldescription'] : null));
                        updateCollectionData('homepage', (data['collection'].hasOwnProperty('homepage') ? data['collection']['homepage'] : null));
                        updateCollectionData('contact', (data['collection'].hasOwnProperty('contact') ? data['collection']['contact'] : null));
                        updateCollectionData('email', (data['collection'].hasOwnProperty('email') ? data['collection']['email'] : null));
                        newLocationData.value = Object.assign({}, data['location']);
                    }

                    function setIsAdmin() {
                        const formData = new FormData();
                        formData.append('permissionJson', JSON.stringify(['SuperAdmin']));
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isAdmin.value = resData.includes('SuperAdmin');
                            if(Number(collId) === 0 && !isAdmin.value){
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                        });
                    }

                    function setSpatialInputValues() {
                        decimalLatitudeValue.value = collectionData.value['latitudedecimal'];
                        decimalLongitudeValue.value = collectionData.value['longitudedecimal'];
                    }

                    function updateCollectionData(key, value) {
                        collectionStore.updateCollectionEditData(key, value);
                        if(key === 'collectionname' && value){
                            validateCollectionName();
                        }
                        else if(key === 'collectioncode' || key === 'institutioncode'){
                            validateCollectionCodes();
                        }
                    }

                    function uploadCollecctionIcon() {
                        showWorking();
                        collectionStore.uploadCollectionIcon(uploadedFile.value, imageIconUrl.value, (res) => {
                            hideWorking();
                            if(res !== ''){
                                showNotification('positive','Icon file uploaded successfully.');
                            }
                            else{
                                showNotification('negative', 'There was an error uploading the icon file');
                            }
                            uploadedFile.value = null;
                            imageIconUrl.value = null;
                        });
                    }

                    function validateCollectionCodes() {
                        collectionCodesValid.value = false;
                        const formData = new FormData();
                        formData.append('collid', collectionData.value['collid']);
                        formData.append('collectioncode', collectionData.value['collectioncode']);
                        formData.append('institutioncode', collectionData.value['institutioncode']);
                        formData.append('action', 'getCollectionIdByCollectionInstitutionCode');
                        fetch(collectionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            if(Number(res) === 0){
                                collectionCodesValid.value = true;
                            }
                            else{
                                showNotification('negative', 'A collection already exists with that Collection and Institution Code combination');
                            }
                        });
                    }

                    function validateCollectionName() {
                        collectionNameValid.value = false;
                        const formData = new FormData();
                        formData.append('collid', collectionData.value['collid']);
                        formData.append('collectionname', collectionData.value['collectionname']);
                        formData.append('action', 'getCollectionIdByName');
                        fetch(collectionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            if(Number(res) === 0){
                                collectionNameValid.value = true;
                            }
                            else{
                                showNotification('negative', 'A collection already exists with that name');
                            }
                        });
                    }

                    function validateLocationRecord() {
                        const formData = new FormData();
                        formData.append('institutionname', newLocationData.value['institutionname']);
                        formData.append('action', 'getInstitutionIdByName');
                        fetch(institutionsApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            if(Number(res) === 0){
                                createLocationRecord();
                            }
                            else{
                                updateCollectionData('iid', res);
                                createCollectionRecord();
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        setCollectionCategories();
                        collectionStore.setCollectionFieldDefinitions();
                        if(Number(collId) === 0){
                            setIsAdmin();
                        }
                        collectionStore.setCollection(collId, () => {
                            if(Number(collId) > 0 && !isEditor.value){
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                        });
                    });

                    return {
                        acceptedFileTypes,
                        clientRoot,
                        collectionCategoryArr,
                        collectionCodesValid,
                        collectionData,
                        collectionFieldDefinitions,
                        collectionId,
                        collectionNameValid,
                        collectionValid,
                        dataManagementOptions,
                        dataRecordingFormatOptions,
                        datasetTypeOptions,
                        decimalLatitudeValue,
                        decimalLongitudeValue,
                        editInstitutionId,
                        editsExist,
                        gbifCollectionArr,
                        gbifPublishingConfigured,
                        guidSourceOptions,
                        imageHeight,
                        imageIconUrl,
                        isAdmin,
                        isEditor,
                        locationNameVal,
                        popupWindowType,
                        rightsTermsOptions,
                        selectedRightsTerm,
                        selectedUploadMethod,
                        showGbifInstitutionCollectionListPopup,
                        showInstitutionEditorPopup,
                        showSpatialPopup,
                        uploadedFile,
                        uploadMethodOptions,
                        checkGBIF,
                        closeInstitutionEditorPopup,
                        closeSpatialPopup,
                        deleteCollectionIcon,
                        deleteInstitutionLinkage,
                        openInstitutionEditorPopup,
                        openSpatialPopup,
                        processCollectionIconImageUpload,
                        processCountryChange,
                        processCreateCollectionRecord,
                        processLocationUpdate,
                        processLocationValueChange,
                        processSpatialData,
                        saveCollectionEdits,
                        setCollectionData,
                        updateCollectionData
                    }
                }
            });
            collectionMetadataSettingsModule.use(Quasar, { config: {} });
            collectionMetadataSettingsModule.use(Pinia.createPinia());
            collectionMetadataSettingsModule.mount('#mainContainer');
        </script>
    </body>
</html>
