<?php
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Site Map</title>
        <meta name="description" content="Site map for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div id="mainContainer">
            <div class="q-pa-md">
                <div class="text-h5 text-bold">Site Map</div>
                <div class="q-pa-md column">
                    <div class="text-h6 text-bold">Collections</div>
                    <ul>
                        <li><a :href="(clientRoot + '/collections/list.php')" tabindex="0">Search Collections</a></li>
                        <li><a :href="(clientRoot + '/spatial/index.php')" tabindex="0">Spatial Module</a></li>
                        <li><a :href="(clientRoot + '/collections/misc/collprofiles.php')" tabindex="0">Collections</a></li>
                        <!-- <li><a :href="(clientRoot + '/collections/misc/collstats.php')" tabindex="0">Collection Statistics</a></li> -->
                        <template v-if="activateExsiccati">
                            <li><a :href="(clientRoot + '/collections/exsiccati/index.php')" tabindex="0">Exsiccati Index</a></li>
                        </template>
                        <li>Data Publishing</li>
                        <li class="q-ml-md"><a :href="(clientRoot + '/collections/datasets/datapublisher.php')" tabindex="0">Darwin Core Archives (DwC-A)</a></li>
                        <li class="q-ml-md"><a :href="(clientRoot + '/rsshandler.php?feed=collection')" target="_blank" aria-label="Collection RSS Feed - opens in separate tab" tabindex="0">Collection RSS Feed</a></li>
                        <li class="q-ml-md"><a :href="(clientRoot + '/rsshandler.php?feed=dwc')" target="_blank" aria-label="DwC-A RSS Feed - opens in separate tab" tabindex="0">DwC-A RSS Feed</a></li>
                        <li><a :href="(clientRoot + '/taxa/protectedspecies.php')" tabindex="0">Protected Species</a></li>
                    </ul>
                    <div class="q-mt-md text-h6 text-bold">Image Library</div>
                    <ul>
                        <li><a :href="(clientRoot + '/media/search.php')" tabindex="0">Image Search</a></li>
                        <li><a :href="(clientRoot + '/media/contributors.php')" tabindex="0">Image Contributors</a></li>
                        <li><a :href="usagePolicyUrl" tabindex="0">Terms of Use</a></li>
                    </ul>
                    <div class="q-mt-md text-h6 text-bold">Additional Resources</div>
                    <ul>
                        <li><a :href="(clientRoot + '/projects/index.php')" tabindex="0">Biotic Inventory Projects</a></li>
                        <li><a :href="(clientRoot + '/checklists/index.php')" tabindex="0">Checklists</a></li>
                        <li><a :href="(clientRoot + '/checklists/checklist.php')" tabindex="0">Dynamic Checklist</a></li>
                        <template v-if="keyModuleIsActive">
                            <li><a :href="(clientRoot + '/ident/key.php')" tabindex="0">Dynamic Key</a></li>
                        </template>
                        <li><a :href="(clientRoot + '/taxa/dynamictaxalist.php')" tabindex="0">Dynamic Taxonomy List</a></li>
                        <template v-if="glossaryModuleIsActive">
                            <li><a :href="(clientRoot + '/glossary/index.php')" tabindex="0">Glossary</a></li>
                        </template>
                        <li><a :href="(clientRoot + '/taxa/taxonomydynamicdisplay.php')" tabindex="0">Taxonomy Explorer</a></li>
                        <li><a :href="(clientRoot + '/taxa/dynamictreeviewer.php')" tabindex="0">Interactive Taxonomic Tree</a></li>
                    </ul>
                    <template v-if="Number(symbUid) > 0">
                        <q-card flat bordered class="q-mt-md">
                            <q-card-section class="column">
                                <div class="text-h6 text-bold">Management Tools</div>
                                <template v-if="isAdmin">
                                    <div class="q-mt-md text-body1 text-bold">Administrative Tools</div>
                                    <ul>
                                        <li><a :href="(clientRoot + '/admin/core.php')" tabindex="0">Portal Configurations</a></li>
                                        <li><a :href="(clientRoot + '/admin/mapping.php')" tabindex="0">Mapping Configurations</a></li>
                                        <li><a :href="(clientRoot + '/profile/usermanagement.php')" tabindex="0">User Management</a></li>
                                        <li><a :href="(clientRoot + '/collections/misc/collmetadata.php')" tabindex="0">Create New Collection</a></li>
                                        <li><a :href="(clientRoot + '/collections/management/thumbnailbuilder.php')" tabindex="0">Build Image Thumbnails</a></li>
                                        <li><a :href="(clientRoot + '/collections/management/guidmapper.php')" tabindex="0">Generate GUIDs/UUIDs</a></li>
                                    </ul>
                                </template>
                                <template v-if="isAdmin || taxonomy || taxonProfile">
                                    <div class="q-mt-md text-body1 text-bold" tabindex="0">Taxonomy</div>
                                    <ul>
                                        <template v-if="isAdmin || taxonomy">
                                            <li><a :href="(clientRoot + '/taxa/thesaurus/index.php')" tabindex="0">Taxonomic Thesaurus Manager</a></li>
                                            <li><a :href="(clientRoot + '/taxa/thesaurus/identifiermanager.php')" tabindex="0">Taxonomic Identifier Manager</a></li>
                                            <li><a :href="(clientRoot + '/taxa/taxonomy/index.php')" tabindex="0">Taxon Editor</a></li>
                                        </template>
                                        <template v-if="isAdmin || taxonProfile">
                                            <li><a :href="(clientRoot + '/taxa/profile/index.php')" tabindex="0">Taxon Profile Editor</a></li>
                                            <li><a :href="(clientRoot + '/taxa/media/batchimageloader.php')" tabindex="0">Taxa Media Batch Uploader</a></li>
                                            <li><a :href="(clientRoot + '/taxa/media/eolimporter.php')" tabindex="0">Encyclopedia of Life Media Importer</a></li>
                                        </template>
                                    </ul>
                                </template>
                                <template v-if="keyModuleIsActive && (isAdmin || keyAdmin)">
                                    <div class="q-mt-md text-body1 text-bold">Identification Keys</div>
                                    <ul>
                                        <li><a :href="" tabindex="0">Characters and Character States Editor</a></li>
                                    </ul>
                                </template>
                                <div class="q-mt-md text-body1 text-bold">Glossary</div>
                                <ul>
                                    <li><a :href="(clientRoot + '/glossary/index.php')" tabindex="0">Manage Glossary</a></li>
                                </ul>
                                <div class="q-mt-md text-body1 text-bold">Datasets</div>
                                <ul>
                                    <li><a :href="(clientRoot + '/collections/datasets/index.php')" tabindex="0">Manage Datasets</a></li>
                                </ul>
                                <template v-if="activateExsiccati">
                                    <div class="q-mt-md text-body1 text-bold">Exsiccati</div>
                                    <ul>
                                        <li><a :href="(clientRoot + '/collections/exsiccati/index.php')" tabindex="0">Exsiccati Index</a></li>
                                    </ul>
                                </template>
                            </q-card-section>
                        </q-card>
                    </template>
                </div>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/config/footer-includes.php');
        include(__DIR__ . '/footer.php');
        ?>
        <script type="text/javascript">
            const siteMapModule = Vue.createApp({
                setup() {
                    const baseStore = useBaseStore();

                    const activateExsiccati = baseStore.getActivateExsiccati;
                    const clientRoot = baseStore.getClientRoot;
                    const confUsagePolicyUrl = baseStore.getUsagePolicyUrl;
                    const currentUserPermissions = Vue.ref(null);
                    const glossaryModuleIsActive = baseStore.getGlossaryModuleIsActive;
                    const isAdmin = Vue.computed(() => {
                        return currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('SuperAdmin');
                    });
                    const keyAdmin = Vue.computed(() => {
                        return currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('KeyAdmin');
                    });
                    const keyModuleIsActive = baseStore.getKeyModuleIsActive;
                    const symbUid = baseStore.getSymbUid;
                    const taxonomy = Vue.computed(() => {
                        return currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('Taxonomy');
                    });
                    const taxonProfile = Vue.computed(() => {
                        return currentUserPermissions.value && currentUserPermissions.value.hasOwnProperty('TaxonProfile');
                    });
                    const usagePolicyUrl = Vue.computed(() => {
                        if(confUsagePolicyUrl && confUsagePolicyUrl.length > 0){
                            return confUsagePolicyUrl;
                        }
                        else{
                            return (clientRoot + '/misc/usagepolicy.php');
                        }
                    });

                    function setCurrentUserPermissions() {
                        baseStore.getGlobalConfigValue('USER_RIGHTS', (dataStr) => {
                            const data = dataStr ? JSON.parse(dataStr) : null;
                            if(data && Object.keys(data).length > 0){
                                currentUserPermissions.value = data;
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        setCurrentUserPermissions();
                    });

                    return {
                        activateExsiccati,
                        clientRoot,
                        glossaryModuleIsActive,
                        isAdmin,
                        keyAdmin,
                        keyModuleIsActive,
                        symbUid,
                        taxonomy,
                        taxonProfile,
                        usagePolicyUrl
                    }
                }
            });
            siteMapModule.use(Quasar, { config: {} });
            siteMapModule.use(Pinia.createPinia());
            siteMapModule.mount('#mainContainer');
        </script>
    </body>
</html>
