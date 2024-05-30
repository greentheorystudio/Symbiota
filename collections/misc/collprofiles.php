<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$collid = (array_key_exists('collid',$_REQUEST) ? (int)$_REQUEST['collid'] : 0);
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title>Collection Profile</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <style>
        .coll-icon-image {
            border-width: 1px;
            height: 30px;
            width: 30px;
        }
        .coll-institution-info {
            width: 50%;
        }
    </style>
	<script src="../../js/external/all.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        const COLLID = <?php echo $collid; ?>;
    </script>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    ?>
    <div id="moduleContainer">
        <div class="q-mt-xs q-ml-xs row justify-start">
            <a :href="clientRoot + '/index.php'">Home</a> &gt;&gt;
            <a :href="clientRoot + '/collections/index.php'">Collection Search Page</a> &gt;&gt;
            <span class="text-body1 text-bold">{{ collectionData.collectionname }} Details</span>
        </div>
        <div id="innertext">
            <template v-if="collId > 0">
                <div class="column q-gutter-md">
                    <div class="text-h4 text-bold">
                        {{ collectionData['collectionname'] + ' ' }}
                        <template v-if="collectionData['institutioncode']">
                            {{ collectionData['institutioncode'] + (collectionData['collectioncode'] ? '-' : '') }}
                        </template>
                        <template v-if="collectionData['collectioncode']">
                            {{ collectionData['collectioncode'] }}
                        </template>
                    </div>
                </div>
                <template v-if="isEditor">
                    <collection-cotrol-panel-menus :collection-id="collId" :collection-type="collectionData.colltype" :collection-permissions="collectionPermissions"></collection-cotrol-panel-menus>
                </template>
                <div class="row justify-between">
                    <div class="col-12 col-sm-6">
                        <div class="q-ml-sm">
                            <collection-metadata-block :collection-data="collectionData"></collection-metadata-block>
                            <template v-if="publishGBIF && datasetKey">
                                <div class="q-mt-xs">
                                    <span class="text-h6 text-bold">GBIF Dataset page: </span>
                                    <a :href="('https://www.gbif.org/dataset/' + datasetKey)" target="_blank">
                                        {{ ('https://www.gbif.org/dataset/' + datasetKey) }}
                                    </a>
                                </div>
                            </template>
                            <template v-if="publishIDIGBIO && datasetKey">
                                <div class="q-mt-xs">
                                    <span class="text-h6 text-bold">iDigBio Dataset page: </span>
                                    <a :href="('https://www.idigbio.org/portal/recordsets/' + idigbioKey)" target="_blank">
                                        {{ ('https://www.idigbio.org/portal/recordsets/' + idigbioKey) }}
                                    </a>
                                </div>
                            </template>
                        </div>
                        <template v-if="Number(collectionData.iid) > 0">
                            <div class="q-ma-md q-mt-sm column q-gutter-sm">
                                <div class="full-width row justify-between">
                                    <div>
                                        {{ collectionData['institutionname'] }}
                                    </div>
                                    <div v-if="collectionPermissions.value.includes('CollAdmin')">
                                        <a :href="(clientRoot + '/collections/misc/institutioneditor.php?emode=1&targetcollid=' + collId + '&iid=' + collectionData.iid)" title="Edit institution information">
                                            <q-icon name="far fa-edit" size="13px" class="cursor-pointer" />
                                        </a>
                                    </div>
                                </div>
                                <div v-if="collectionData['institutionname2']">
                                    {{ collectionData['institutionname2'] }}
                                </div>
                                <div v-if="collectionData['address1']">
                                    {{ collectionData['address1'] }}
                                </div>
                                <div v-if="collectionData['address2']">
                                    {{ collectionData['address2'] }}
                                </div>
                                <div v-if="collectionData['city'] || collectionData['stateprovince'] || collectionData['postalcode']">
                                    <template v-if="collectionData['city']">
                                        {{ collectionData['city'] + ((collectionData['stateprovince'] || collectionData['postalcode']) ? ', ' : '') }}
                                    </template>
                                    <template v-if="collectionData['stateprovince']">
                                        {{ collectionData['stateprovince'] + (collectionData['postalcode'] ? '   ' : '') }}
                                    </template>
                                    <template v-if="collectionData['postalcode']">
                                        {{ collectionData['postalcode'] }}
                                    </template>
                                </div>
                                <div v-if="collectionData['country']">
                                    {{ collectionData['country'] }}
                                </div>
                            </div>
                        </template>
                        <div class="q-ml-sm column">
                            <div class="text-h6 text-bold">Collection Statistics:</div>
                            <div class="q-pl-sm column">
                                <div>
                                    {{ collectionData['recordcnt'] + ' occurrence ' + (Number(collectionData['recordcnt']) === 1 ? 'record' : 'records') }}
                                </div>
                                <div>
                                    {{ ((collectionData['georefcnt'] && Number(collectionData['georefcnt']) > 0) ? collectionData['georefcnt'] : '0' ) + ' ' }}
                                    <template v-if="georeferencedPercent > 0">
                                        {{ '(' + georeferencedPercent + '%) ' }}
                                    </template>
                                    georeferenced
                                </div>
                                <template v-if="collectionData['dynamicProperties']">
                                    <div v-if="collectionData['dynamicProperties']['imgcnt'] && Number(collectionData['dynamicProperties']['imgcnt']) > 0">
                                        {{ collectionData['dynamicProperties']['imgcnt'] + ' ' }}
                                        <template v-if="imagePercent > 0">
                                            {{ '(' + imagePercent + '%) ' }}
                                        </template>
                                        with images
                                    </div>
                                    <div v-if="collectionData['dynamicProperties']['gencnt'] && Number(collectionData['dynamicProperties']['gencnt']) > 0">
                                        {{ collectionData['dynamicProperties']['gencnt'] + ' with GenBank references' }}
                                    </div>
                                    <div v-if="collectionData['dynamicProperties']['boldcnt'] && Number(collectionData['dynamicProperties']['boldcnt']) > 0">
                                        {{ collectionData['dynamicProperties']['boldcnt'] + ' with BOLD references' }}
                                    </div>
                                    <div v-if="collectionData['dynamicProperties']['refcnt'] && Number(collectionData['dynamicProperties']['refcnt']) > 0">
                                        {{ collectionData['dynamicProperties']['refcnt'] + ' with publication references' }}
                                    </div>
                                    <div v-if="collectionData['dynamicProperties']['SpecimensCountID'] && Number(collectionData['dynamicProperties']['SpecimensCountID']) > 0">
                                        {{ collectionData['dynamicProperties']['SpecimensCountID'] + ' ' }}
                                        <template v-if="speciesIDPercent > 0">
                                            {{ '(' + speciesIDPercent + '%) ' }}
                                        </template>
                                        identified to species
                                    </div>
                                </template>
                                <div>
                                    {{ collectionData['familycnt'] + (Number(collectionData['familycnt']) === 1 ? ' family' : ' families') }}
                                </div>
                                <div>
                                    {{ collectionData['genuscnt'] + (Number(collectionData['genuscnt']) === 1 ? ' genus' : ' genera') }}
                                </div>
                                <div>
                                    {{ collectionData['speciescnt'] + ' species' }}
                                </div>
                                <template v-if="collectionData['dynamicProperties']">
                                    <div v-if="collectionData['dynamicProperties']['TotalTaxaCount'] && Number(collectionData['dynamicProperties']['TotalTaxaCount']) > 0">
                                        {{ collectionData['dynamicProperties']['TotalTaxaCount'] + ' total taxa (including subsp. and var.)' }}
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 row justify-end q-gutter-sm">
                        <div class="col-auto">
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="text-h6 text-bold">Extra Statistics</div>
                                    <div class="q-pl-sm column">
                                        <div class="cursor-pointer">
                                            <a @click="">Show Geographic Distribution</a>
                                        </div>
                                        <div class="cursor-pointer">
                                            <a @click="">Show Family Distribution</a>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                        <div class="col-auto">
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="text-h6 text-bold">Data Downloads</div>
                                    <div class="q-pl-sm column">
                                        <div class="cursor-pointer">
                                            <a @click="processDownloadSpeciesList()">Download Taxa List</a>
                                        </div>
                                        <template v-if="configuredDataDownloads.length > 0">
                                            <template v-for="download in configuredDataDownloads">
                                                <div class="cursor-pointer">
                                                    <a @click="processConfiguredDataDownload(download['api-endpoint'], download['endpoint-action'], download['filename'])">{{ download.label }}</a>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="column q-gutter-md">
                    <div class="column">
                        <div class="text-h4">
                            {{ defaultTitle + ' Natural History Collections and Observation Projects' }}
                        </div>
                        <div class="q-ma-sm">
                            <a :href="(clientRoot + '/collections/datasets/rsshandler.php')" target="_blank">
                                View RSS feed
                            </a>
                        </div>
                    </div>
                    <template v-for="collection in collectionArr">
                        <q-card v-if="collection" flat bordered>
                            <q-card-section horizontal class="q-pa-md">
                                <q-card-section v-if="collection.icon" class="col-3 column flex flex-center">
                                    <q-img class="rounded-borders coll-icon-image" :src="collection.icon"></q-img>
                                    <div>
                                        {{ collection.institutioncode + ((collection.institutioncode && collection.collectioncode)? '-' : '') + collection.collectioncode }}
                                    </div>
                                </q-card-section>
                                <q-card-section class="column">
                                    <div class="text-h6 text-bold">
                                        <a @click="setCollection(collection.collid)">
                                            {{ collection.collectionname }}
                                        </a>
                                    </div>
                                    <div class="q-mt-sm">
                                        <collection-metadata-block :collection-data="collection"></collection-metadata-block>
                                    </div>
                                </q-card-section>
                            </q-card-section>
                        </q-card>
                    </template>
                </div>
            </template>
        </div>
    </div>
	<?php
	include(__DIR__ . '/../../footer.php');
    include_once(__DIR__ . '/../../config/footer-includes.php');
	?>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionControlPanelMenus.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/collections/collectionMetadataBlock.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
    <script type="text/javascript">
        const collectionProfileModule = Vue.createApp({
            components: {
                'collection-cotrol-panel-menus': collectionControlPanelMenus,
                'collection-metadata-block': collectionMetadataBlock
            },
            setup() {
                const { hideWorking, processCsvDownload, showWorking } = useCore();
                const baseStore = useBaseStore();
                const collectionStore = useCollectionStore();

                const clientRoot = baseStore.getClientRoot;
                const collId = Vue.ref(COLLID);
                const collectionArr = Vue.computed(() => collectionStore.getCollectionArr);
                const collectionData = Vue.computed(() => collectionStore.getCollectionData);
                const collectionPermissions = Vue.computed(() => collectionStore.getCollectionPermissions);
                const configuredDataDownloads = Vue.computed(() => collectionStore.getConfiguredDataDownloads);
                const datasetKey = Vue.computed(() => collectionStore.getDatasetKey);
                const defaultTitle = baseStore.getDefaultTitle;
                const endpointKey = Vue.computed(() => collectionStore.getEndpointKey);
                const georeferencedPercent = Vue.computed(() => collectionStore.getGeoreferencedPercent);
                const idigbioKey = Vue.computed(() => collectionStore.getIdigbioKey);
                const imagePercent = Vue.computed(() => collectionStore.getImagePercent);
                const installationKey = Vue.computed(() => collectionStore.getInstallationKey);
                const isEditor = Vue.computed(() => {
                    return (collectionPermissions.value.includes('CollAdmin') || collectionPermissions.value.includes('CollEditor'));
                });
                const publishGBIF = Vue.computed(() => collectionStore.getPublishToGBIF);
                const publishIDIGBIO = Vue.computed(() => collectionStore.getPublishToIdigbio);
                const speciesIDPercent = Vue.computed(() => collectionStore.getSpeciesIDPercent);

                function processConfiguredDataDownload(endpoint, action, filename) {
                    showWorking();
                    const formData = new FormData();
                    formData.append('collid', collId.value.toString());
                    formData.append('action', action);
                    fetch((clientRoot + endpoint), {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        return response.ok ? response.json() : null;
                    })
                    .then((data) => {
                        hideWorking();
                        if(data){
                            processCsvDownload(data, (collId.value.toString() + '_' + filename));
                        }
                    });;
                }

                function processDownloadSpeciesList() {
                    showWorking();
                    const formData = new FormData();
                    formData.append('collid', collId.value.toString());
                    formData.append('action', 'getSpeciesListDownloadData');
                    fetch(collectionApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        response.json().then((resObj) => {
                            hideWorking();
                            processCsvDownload(resObj, (collId.value.toString() + '_taxa_list'));
                        });
                    });
                }

                function setCollection(collid) {
                    collId.value = collid;
                    collectionStore.setCollection(collId.value);
                }

                Vue.onMounted(() => {
                    if(collId.value > 0){
                        collectionStore.setCollection(collId.value);
                    }
                    else{
                        collectionStore.setCollectionArr();
                    }
                });

                return {
                    clientRoot,
                    collId,
                    collectionArr,
                    collectionData,
                    collectionPermissions,
                    configuredDataDownloads,
                    datasetKey,
                    defaultTitle,
                    endpointKey,
                    georeferencedPercent,
                    idigbioKey,
                    imagePercent,
                    installationKey,
                    isEditor,
                    publishGBIF,
                    publishIDIGBIO,
                    speciesIDPercent,
                    processConfiguredDataDownload,
                    processDownloadSpeciesList,
                    setCollection
                }
            }
        });
        collectionProfileModule.use(Quasar, { config: {} });
        collectionProfileModule.use(Pinia.createPinia());
        collectionProfileModule.mount('#moduleContainer');
    </script>
</body>
</html>
