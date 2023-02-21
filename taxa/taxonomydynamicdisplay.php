<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: SAMEORIGIN');

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('Taxonomy',$GLOBALS['USER_RIGHTS'])){
    $isEditor = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Taxonomy Explorer</title>
        <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
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
        <script src="../js/external/all.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            const isEditor = Vue.ref(<?php echo ($isEditor?'true':'false'); ?>);
        </script>
    </head>
    <body>
    <?php
    include(__DIR__ . '/../header.php');
    ?>
    <div class="navpath">
        <a href="../index.php">Home</a> &gt;&gt;
        <a href="taxonomydynamicdisplay.php"><b>Taxonomy Explorer</b></a>
    </div>
    <div id="innertext">
        <q-card class="target-taxon-card">
            <q-card-section>
                <div class="q-my-sm">
                    <single-scientific-common-name-auto-complete :sciname="targetTaxon" label="Find a taxon" limit-to-thesaurus="true" rank-low="10" @update:sciname="updateTargetTaxon"></single-scientific-common-name-auto-complete>
                </div>
                <div class="button-div">
                    <q-btn color="secondary" @click="getTargetTaxonPath();" label="Find Taxon" dense />
                </div>
                <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                <div class="q-my-sm">
                    <q-checkbox v-model="displayAuthors" label="Show taxonomic authors" />
                </div>
            </q-card-section>
        </q-card>
        <q-card class="q-my-md">
            <q-card-section>
                <q-tree ref="treeRef" v-model:selected="selectedTid" :nodes="taxaNodes" node-key="tid" selected-color="green" @lazy-load="getTaxonChildren" @update:selected="processClick" @after-show="processTargetTaxonPath">
                    <template v-slot:default-header="prop">
                        <div v-if="prop.node.nodetype === 'child'">
                            <span class="taxon-node-rankname">{{ prop.node.rankname }}</span> <span class="taxon-node-sciname">{{ prop.node.sciname }}</span> <span v-if="displayAuthors" class="taxon-node-author">{{ prop.node.author }}</span>
                        </div>
                        <div v-else-if="prop.node.nodetype === 'synonym'">
                            <span class="taxon-node-rankname">{{ prop.node.rankname }}</span> <span class="taxon-node-author">[<span class="taxon-node-sciname">{{ prop.node.sciname }}</span> <span v-if="displayAuthors">{{ prop.node.author }}</span>]</span>
                        </div>
                    </template>
                </q-tree>
            </q-card-section>
        </q-card>
    </div>
    <?php
    include(__DIR__ . '/../footer.php');
    include_once(__DIR__ . '/../config/footer-includes.php');
    ?>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/singleScientificCommonNameAutoComplete.js?ver=20230202" type="text/javascript"></script>
    <script>
        const taxonomyDynamicDisplayModule = Vue.createApp({
            data() {
                return {
                    displayAuthors: Vue.ref(false),
                    isEditor: isEditor,
                    taxaNodes: Vue.ref([]),
                    selectedTid: Vue.ref(null),
                    targetTaxon: Vue.ref(null),
                    targetTaxonPathArr: Vue.ref([])
                }
            },
            components: {
                'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
            },
            setup() {
                let treeRef = Vue.ref(null);
                return {
                    treeRef
                }
            },
            mounted() {
                this.setKingdomNodes();
            },
            methods: {
                findFirstTargetPathUnexpandedNode(){
                    const expandedNodes = this.treeRef.getExpandedNodes();
                    let firstNodeFound = false;
                    let index = 0;
                    while(index < this.targetTaxonPathArr.length && !firstNodeFound){
                        const expanded = expandedNodes.find(obj => Number(obj['tid']) === Number(this.targetTaxonPathArr[index]['tid']));
                        if(!expanded){
                            this.treeRef.setExpanded(this.targetTaxonPathArr[index]['tid'],true);
                            firstNodeFound = true;
                            this.targetTaxonPathArr.splice(0, (index + 1));
                        }
                    }
                },
                getTargetTaxonPath(){
                    if(this.selectedTid){
                        this.treeRef.collapseAll();
                        const formData = new FormData();
                        formData.append('tid', this.selectedTid);
                        formData.append('action', 'getTaxonomicTreeTaxonPath');
                        fetch(taxonomyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                this.targetTaxonPathArr = resObj;
                                this.processTargetTaxonPath();
                            });
                        });
                    }
                },
                getTaxonChildren({ key, done }){
                    const formData = new FormData();
                    formData.append('tid', key);
                    formData.append('action', 'getTaxonomicTreeChildNodes');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        response.json().then((resObj) => {
                            done(resObj);
                        });
                    });
                },
                processClick(tid){
                    this.selectedTid = this.targetTaxon ? this.targetTaxon.tid : null;
                    if(!tid){
                        tid = this.selectedTid;
                    }
                    if(tid){
                        let url;
                        if(this.isEditor){
                            url = CLIENT_ROOT + '/taxa/taxonomy/taxonomyeditor.php?tid=' + tid;
                        }
                        else{
                            url = CLIENT_ROOT + '/taxa/index.php?taxon=' + tid;
                        }
                        window.open(url, '_blank');
                    }
                },
                processTargetTaxonPath(){
                    if(this.targetTaxonPathArr.length > 0){
                        this.treeRef.setExpanded(this.targetTaxonPathArr[0]['tid'],true);
                        this.targetTaxonPathArr.splice(0, 1);
                    }
                },
                setKingdomNodes(){
                    const formData = new FormData();
                    formData.append('action', 'getTaxonomicTreeKingdomNodes');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        response.json().then((resObj) => {
                            this.taxaNodes = resObj;
                        });
                    });
                },
                updateTargetTaxon(taxonObj) {
                    this.targetTaxonPathArr = [];
                    this.targetTaxon = taxonObj;
                    this.selectedTid = taxonObj ? taxonObj['tid'] : null;
                }
            }
        });
        taxonomyDynamicDisplayModule.use(Quasar, { config: {} });
        taxonomyDynamicDisplayModule.mount('#innertext');
    </script>
    </body>
</html>

