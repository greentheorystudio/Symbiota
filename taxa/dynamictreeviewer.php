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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Dynamic Taxonomic Tree Viewer</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            #app {
                font-family: 'Avenir', Helvetica, Arial, sans-serif;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-align: center;
                color: #2c3e50;
                margin-top: 20px;
            }

            .tree {
                height: 800px;
            }

            .graph-root {
                height: 800px;
                width: 100%;
            }

            .feedback{
                height: 50px;
                line-height: 50px;
                vertical-align: middle;
            }

            .log  {
                height: 200px;
                overflow-x: auto;
                overflow-y: auto;
                overflow: auto;
                text-align: left;
            }

            .pop-up-tree {
                position: absolute;
            }

            .treeclass .nodetree  circle {
                fill: #999;
            }

            .treeclass .node--internal circle {
                cursor: pointer;
                fill:  #555;
            }

            .treeclass .nodetree text {
                font: 10px sans-serif;
                cursor: pointer;
            }

            .treeclass .nodetree.selected text {
                font-weight: bold;
            }

            .treeclass .node--internal text {
                text-shadow: 0 1px 0 #fff, 0 -1px 0 #fff, 1px 0 0 #fff, -1px 0 0 #fff;
            }

            .treeclass .linktree {
                fill: none;
                stroke: #555;
                stroke-opacity: 0.4;
            }

            .treeclass {
                max-height: 100%;
                width: 100%;
            }
        </style>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/d3.v7.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/d3-interpolate-path.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ResizeSensor.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/ElementQueries.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/lodash.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/Vueresize.umd.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/popper.js" type="text/javascript"></script>
        <script type="text/javascript">
            let data = {};

            function setData() {
                fetch('/data.json')
                .then((response) => {
                    if(response.ok){
                        return response.json();
                    }
                })
                .then((resObj) => {
                    data = Object.assign(resObj, {
                        type: 'tree',
                        layoutType: 'horizontal',
                        duration: 750,
                        Marginx: 30,
                        Marginy: 30,
                        radius: 3,
                        leafTextMargin: 6,
                        nodeTextMargin: 6,
                        nodeText: 'text',
                        currentData: null,
                        zoomable: true,
                        isLoading: false,
                        isUnderGremlinsAttack: false,
                        nodeTextDisplay: 'all',
                        linkLayout: 'bezier',
                        minZoom: 0.8,
                        maxZoom: 9,
                        events: []
                    });
                });
            }
            setData();
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div class="navpath">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
            <a href="dynamictreeviewer.php"><b>Dynamic Taxonomic Tree Viewer</b></a>
        </div>
        <div id="innertext">
            <div id="app" class="container-fluid">
                <div class="col-md-3">

                    <div class="panel panel-default">
                        <div class="panel-heading">Props</div>

                        <div class="panel-body">
                            <div class="form-horizontal">

                                <div class="form-group">
                                    <label for="type" class="control-label col-sm-3">type</label>
                                    <div  class="col-sm-9">
                                        <select id="type" class="form-control" v-model="type">
                                            <option>tree</option>
                                            <option>cluster</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="layout-type" class="control-label col-sm-3">layoutType</label>
                                    <div  class="col-sm-9">
                                        <select id="layout-type" class="form-control" v-model="layoutType">
                                            <option>horizontal</option>
                                            <option>vertical</option>
                                            <option>circular</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="layout-type" class="control-label col-sm-3">nodeTextDisplay</label>
                                    <div  class="col-sm-9">
                                        <select id="layout-type" class="form-control" v-model="nodeTextDisplay">
                                            <option>all</option>
                                            <option>leaves</option>
                                            <option>extremities</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="layout-type" class="control-label col-sm-3">linkLayout</label>
                                    <div  class="col-sm-9">
                                        <select id="layout-type" class="form-control" v-model="linkLayout">
                                            <option>bezier</option>
                                            <option>orthogonal</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="margin-x" class="control-label col-sm-3">marginx</label>
                                    <div class="col-sm-7">
                                        <input id="margin-x" class="form-control" type="range" min="0" max="200" v-model.number="Marginx">
                                    </div>
                                    <div class="col-sm-2">
                                        <p>{{Marginx}}px</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="margin-y" class="control-label col-sm-3">marginy</label>
                                    <div class="col-sm-7">
                                        <input id="margin-y" class="form-control" type="range" min="0" max="200" v-model.number="Marginy">
                                    </div>
                                    <div class="col-sm-2">
                                        <p>{{Marginy}}px</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="margin-y" class="control-label col-sm-3">radius</label>
                                    <div class="col-sm-7">
                                        <input id="radius" class="form-control" type="range" min="1" max="10" v-model.number="radius">
                                    </div>
                                    <div class="col-sm-2">
                                        <p>{{radius}}px</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="velocity" class="control-label col-sm-3">Duration</label>
                                    <div class="col-sm-7">
                                        <input id="velocity" class="form-control" type="range" min="0" max="3000" v-model.number="duration">
                                    </div>
                                    <div class="col-sm-2">
                                        <p>{{duration}}ms</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="leaf-text-margin" class="control-label col-sm-3">leafTextMargin</label>
                                    <div class="col-sm-7">
                                        <input id="leaf-text-margin" class="form-control" type="range" min="0" max="100" v-model.number="leafTextMargin">
                                    </div>
                                    <div class="col-sm-2">
                                        <p>{{leafTextMargin}}px</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="node-text-margin" class="control-label col-sm-3">nodeTextMargin</label>
                                    <div class="col-sm-7">
                                        <input id="node-text-margin" class="form-control" type="range" min="0" max="100" v-model.number="nodeTextMargin">
                                    </div>
                                    <div class="col-sm-2">
                                        <p>{{nodeTextMargin}}px</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="minZoom" class="control-label col-sm-3">minZoom</label>
                                    <div class="col-sm-7">
                                        <input id="minZoom" class="form-control" type="range" min="0.01" max="1" step="0.05" v-model.number="minZoom">
                                    </div>
                                    <div class="col-sm-2">
                                        <p>{{minZoom}}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="maxZoom" class="control-label col-sm-3">maxZoom</label>
                                    <div class="col-sm-7">
                                        <input id="maxZoom" class="form-control" type="range" min="1" max="100" v-model.number="maxZoom">
                                    </div>
                                    <div class="col-sm-2">
                                        <p>{{maxZoom}}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="zoomable" class="">Zoomable</label>
                                    <input id="zoomable" class="form-check-input" type="checkbox" v-model="zoomable">
                                </div>

                                <div class="form-group feedback">
                                    <i v-show="isLoading" class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
                                </div>

                                <button type="button" :disabled="!currentData" class="btn btn-primary" @click="expandAll" data-toggle="tooltip" data-placement="top" title="Expand All from current">
                                    <i class="fa fa-expand" aria-hidden="true"></i>
                                </button>

                                <button type="button" :disabled="!currentData" class="btn btn-secondary" @click="collapseAll" data-toggle="tooltip" data-placement="top" title="Collapse All from current">
                                    <i class="fa fa-compress" aria-hidden="true"></i>
                                </button>

                                <button type="button" :disabled="!currentData" class="btn btn-success" @click="showOnly" data-toggle="tooltip" data-placement="top" title="Show Only from current">
                                    <i class="fa fa-search-plus" aria-hidden="true"></i>
                                </button>

                                <button type="button" :disabled="!currentData" class="btn btn-warning" @click="show" data-toggle="tooltip" data-placement="top" title="Show current">
                                    <i class="fa fa-binoculars" aria-hidden="true"></i>
                                </button>

                                <button v-if="zoomable" type="button" class="btn btn-warning" @click="resetZoom" data-toggle="tooltip" data-placement="top" title="Reset Zoom">
                                    <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                                </button>

                            </div>
                        </div>
                    </div>


                    <div class="panel panel-default">
                        <div class="panel-heading">Events</div>

                        <div class="panel-body log">
                            <div v-for="(event,index) in events" :key="index">
                                <p><b>Name:</b> {{event.eventName}} <b>Data:</b>{{event.data.text}}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div v-if="Graph.tree" class="col-md-9 panel panel-default">
                    <dynamic-taxa-tree ref="tree"
                          v-model="currentData"
                          :nodeTextDisplay="nodeTextDisplay"
                          :identifier="getId"
                          :nodeTextMargin="nodeTextMargin"
                          :zoomable="zoomable"
                          :data="Graph.tree"
                          :leafTextMargin="leafTextMargin"
                          :node-text="nodeText"
                          :margin-x="Marginx"
                          :margin-y="Marginy"
                          :radius="radius"
                          :type="type"
                          :layout-type="layoutType"
                          :linkLayout="linkLayout"
                          :duration="duration"
                          :minZoom="minZoom"
                          :maxZoom="maxZoom"
                          contextMenuPlacement="bottom-start"
                          class="tree"
                          @clickedText="onClick"
                          @expand="onExpand"
                          @retract="onRetract"
                          @clickedNode="onClickNode">
                    </dynamic-taxa-tree>
                </div>

            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/dynamicTaxaTree.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const dynamicTaxonomicTreeViewer = Vue.createApp({
                components: {
                    'dynamic-taxa-tree': dynamicTaxaTree
                },
                setup() {
                    let currentId = 500;

                    const removeElement = (arr, element) => {
                        const index = arr.indexOf(element)
                        if (index === -1) {
                            return
                        }
                        arr.splice(index, 1)
                    }

                    return {
                        currentId,
                        removeElement
                    }
                },
                data () {
                    return {
                        type: 'tree',
                        layoutType: 'horizontal',
                        duration: 750,
                        Marginx: 30,
                        Marginy: 30,
                        radius: 3,
                        leafTextMargin: 6,
                        nodeTextMargin: 6,
                        nodeText: 'text',
                        currentData: null,
                        zoomable: true,
                        isLoading: false,
                        isUnderGremlinsAttack: false,
                        nodeTextDisplay: 'all',
                        linkLayout: 'bezier',
                        minZoom: 0.8,
                        maxZoom: 9,
                        events: [],
                        Graph: {
                            tree: null,
                            links: [],
                            text: null
                        }
                    };
                },
                mounted () {
                    fetch('/data.json')
                    .then((response) => {
                        if(response.ok){
                            return response.json();
                        }
                    })
                    .then((data) => {
                        this.Graph = Object.assign({}, data.Graph);
                    });
                },
                methods: {
                    async do (action) {
                        if (this.currentData) {
                            this.isLoading = true
                            await this.$refs['tree'][action](this.currentData)
                            this.isLoading = false
                        }
                    },
                    getId (node) {
                        return node.id
                    },
                    expandAll () {
                        this.do('expandAll')
                    },
                    collapseAll () {
                        this.do('collapseAll')
                    },
                    showOnly () {
                        this.do('showOnly')
                    },
                    show () {
                        this.do('show')
                    },
                    onClick (evt) {
                        this.onEvent('clickedText', evt)
                    },
                    onClickNode (evt) {
                        this.onEvent('clickedNode', evt)
                    },
                    onExpand (evt) {
                        this.onEvent('onExpand', evt)
                    },
                    onRetract (evt) {
                        this.onEvent('onRetract', evt)
                    },
                    onEvent (eventName, data) {
                        this.events.push({eventName, data: data.data})
                    },
                    addFor (data) {
                        const newData = {
                            id: this.currentId++,
                            children: [],
                            text: Math.random().toString(36).substring(7)
                        }
                        data.children.push(newData)
                    },
                    remove (data, node) {
                        const parent = node.parent.data
                        this.removeElement(parent.children, data)
                    },
                    resetZoom () {
                        if (!this.$refs['tree']) {
                            return
                        }
                        this.isLoading = true
                        this.$refs['tree'].resetZoom().then(() => { this.isLoading = false })
                    }
                }
            });
            dynamicTaxonomicTreeViewer.use(Quasar, { config: {} });
            dynamicTaxonomicTreeViewer.use(Pinia.createPinia());
            dynamicTaxonomicTreeViewer.mount('#innertext');
        </script>
    </body>
</html>

