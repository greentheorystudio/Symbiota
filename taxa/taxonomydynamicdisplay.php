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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Explorer</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .target-taxon-card {
                width: 50%;
            }
            .button-div {
                display: flex;
                justify-content: flex-end;
            }
            .taxon-node-rankname {
                font-size: 0.8rem;
            }
            .taxon-node-sciname, .taxon-node-author {
                font-size: 1.1rem;
            }
            .taxon-node-author {
                margin-left: 3px;
            }
            .taxon-node-sciname {
                margin-left: 5px;
                font-weight: bold;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div class="navpath">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
            <a href="taxonomydynamicdisplay.php"><b>Taxonomy Explorer</b></a>
        </div>
        <div id="innertext">
            <q-card class="target-taxon-card">
                <q-card-section>
                    <div class="q-my-sm">
                        <single-scientific-common-name-auto-complete :sciname="(targetTaxon ? targetTaxon.sciname : null)" :disabled="loading" label="Find a taxon" limit-to-thesaurus="true" rank-low="10" @update:sciname="updateTargetTaxon"></single-scientific-common-name-auto-complete>
                    </div>
                    <div class="button-div">
                        <q-btn :loading="loading" color="secondary" @click="initializeGetTargetTaxon();" label="Find Taxon" dense />
                    </div>
                    <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                    <div class="q-my-sm">
                        <q-checkbox v-model="displayAuthors" label="Show taxonomic authors" />
                    </div>
                </q-card-section>
            </q-card>
            <q-card class="q-my-md">
                <q-card-section>
                    <q-tree ref="treeRef" v-model:selected="selectedTid" :nodes="taxaNodes" node-key="tid" selected-color="green" @lazy-load="getTaxonChildren" @update:selected="processClick" @after-show="processTargetTaxonPath" @after-hide="processClose">
                        <template v-slot:default-header="prop">
                            <div :ref="prop.node.tid === selectedTid ? 'targetNodeRef' : undefined" v-if="prop.node.nodetype === 'child'">
                                <span class="taxon-node-rankname">{{ prop.node.rankname }}</span> <span class="taxon-node-sciname">{{ prop.node.sciname }}</span> <span v-if="displayAuthors" class="taxon-node-author">{{ prop.node.author }}</span>
                            </div>
                            <div :ref="prop.node.tid === selectedTid ? 'targetNodeRef' : undefined" v-else-if="prop.node.nodetype === 'synonym'">
                                <span class="taxon-node-rankname">{{ prop.node.rankname }}</span> <span class="taxon-node-author">[<span class="taxon-node-sciname">{{ prop.node.sciname }}</span> <span v-if="displayAuthors">{{ prop.node.author }}</span>]</span>
                            </div>
                        </template>
                    </q-tree>
                </q-card-section>
            </q-card>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleScientificCommonNameAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const taxonomyDynamicDisplayModule = Vue.createApp({
                components: {
                    'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
                },
                setup() {
                    const baseStore = useBaseStore();

                    const clientRoot = baseStore.getClientRoot;
                    const displayAuthors = Vue.ref(false);
                    const isEditor = Vue.ref(false);
                    const loading = Vue.ref(false);
                    const selectedTid = Vue.ref(null);
                    const targetFound = Vue.ref(false);
                    const targetNodeRef = Vue.ref(null);
                    const targetTaxon = Vue.ref(null);
                    const targetTaxonPathArr = Vue.ref([]);
                    const taxaNodes = Vue.ref([]);
                    const treeRef = Vue.ref(null);

                    function getTargetTaxonPath() {
                        const formData = new FormData();
                        formData.append('tid', selectedTid.value);
                        formData.append('action', 'getTaxonomicTreeTaxonPath');
                        fetch(taxonHierarchyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                targetTaxonPathArr.value = resObj;
                                processTargetTaxonPath();
                            });
                        });
                    }

                    function getTaxonChildren({ key, done }) {
                        const formData = new FormData();
                        formData.append('tid', key);
                        formData.append('action', 'getTaxonomicTreeChildNodes');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                done(resObj);
                                processTargetTaxonPath();
                            });
                        });
                    }

                    function initializeGetTargetTaxon() {
                        if(selectedTid.value){
                            loading.value = true;
                            targetFound.value = false;
                            const openNodes = treeRef.value.getExpandedNodes();
                            if(openNodes.length > 0){
                                treeRef.value.collapseAll();
                            }
                            else{
                                getTargetTaxonPath();
                            }
                        }
                    }

                    function processClick(tid) {
                        selectedTid.value = targetTaxon.value ? targetTaxon.value.tid : null;
                        if(!tid){
                            tid = selectedTid.value;
                        }
                        if(tid){
                            let url;
                            if(isEditor.value){
                                url = clientRoot + '/taxa/taxonomy/taxonomyeditor.php?tid=' + tid;
                            }
                            else{
                                url = clientRoot + '/taxa/index.php?taxon=' + tid;
                            }
                            window.open(url, '_blank');
                        }
                    }

                    function processClose() {
                        if(loading.value){
                            const openNodes = treeRef.value.getExpandedNodes();
                            if(openNodes.length === 0){
                                getTargetTaxonPath();
                            }
                        }
                    }

                    function processTargetTaxonPath() {
                        if(targetTaxonPathArr.value.length > 0 && treeRef.value.getNodeByKey(targetTaxonPathArr.value[0]['tid'])){
                            treeRef.value.setExpanded(targetTaxonPathArr.value[0]['tid'],true);
                            targetTaxonPathArr.value.splice(0, 1);
                        }
                        else if(selectedTid.value && targetNodeRef.value && !targetFound.value){
                            targetNodeRef.value.scrollIntoView();
                            targetFound.value = true;
                            loading.value = false;
                        }
                        else{
                            loading.value = false;
                        }
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
                            response.json().then((resData) => {
                                isEditor.value = resData.includes('Taxonomy');
                            });
                        });
                    }

                    function setKingdomNodes() {
                        const formData = new FormData();
                        formData.append('action', 'getTaxonomicTreeKingdomNodes');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                taxaNodes.value = resObj;
                            });
                        });
                    }

                    function updateTargetTaxon(taxonObj) {
                        targetTaxonPathArr.value = [];
                        targetTaxon.value = taxonObj;
                        selectedTid.value = taxonObj ? taxonObj['tid'] : null;
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        setKingdomNodes();
                    });

                    return {
                        displayAuthors,
                        loading,
                        selectedTid,
                        targetNodeRef,
                        targetTaxon,
                        taxaNodes,
                        treeRef,
                        getTaxonChildren,
                        initializeGetTargetTaxon,
                        processClick,
                        processClose,
                        processTargetTaxonPath,
                        updateTargetTaxon
                    }
                }
            });
            taxonomyDynamicDisplayModule.use(Quasar, { config: {} });
            taxonomyDynamicDisplayModule.use(Pinia.createPinia());
            taxonomyDynamicDisplayModule.mount('#innertext');
        </script>
    </body>
</html>

