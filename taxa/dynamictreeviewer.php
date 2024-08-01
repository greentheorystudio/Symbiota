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
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Interactive Taxonomic Tree</title>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <style>
            .tree-container {
                min-height: 600px;
                overflow: hidden;
            }
        </style>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/d3.v7.js" type="text/javascript"></script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div class="navpath">
            <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
            <a href="dynamictreeviewer.php"><b>Dynamic Taxonomic Tree Viewer</b></a>
        </div>
        <div id="app" class="fit">
            <div class="fit row justify-between q-ma-none q-pa-none q-col-gutter-sm">
                <div class="col-3 q-pa-sm">
                    <q-card flat bordered>
                        <q-card-section class="column q-gutter-sm q-pa-sm">
                            <div>
                                <taxa-kingdom-selector :selected-kingdom="selectedKingdom" label="Select Kingdom" @update:selected-kingdom="updateSelectedKingdom"></taxa-kingdom-selector>
                            </div>
                            <div>
                                <selector-input-element label="Type" :options="typeOptions" :value="selectedType" @update:value="setTreeType"></selector-input-element>
                            </div>
                            <div>
                                <selector-input-element label="Layout Type" :options="layoutTypeOptions" :value="selectedLayoutType" @update:value="setLayoutType"></selector-input-element>
                            </div>
                            <div>
                                <selector-input-element label="Link Layout" :options="linkLayoutOptions" :value="selectedLinkLayout" @update:value="setLinkLayout"></selector-input-element>
                            </div>
                            <div>
                                <text-field-input-element data-type="int" label="Margin x (px)" min-value="0" :value="marginXValue" :clearable="false" @update:value="setMarginX"></text-field-input-element>
                            </div>
                            <div>
                                <text-field-input-element data-type="int" label="Margin y (px)" min-value="0" :value="marginYValue" :clearable="false" @update:value="setMarginY"></text-field-input-element>
                            </div>
                            <div>
                                <q-btn color="primary" @click="centerTree();" label="Center Tree" dense />
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
                <div class="col-9 q-pa-sm">
                    <q-card flat bordered>
                        <q-card-section class="q-pa-sm">
                            <div ref="treeDisplayRef" class="tree-container"></div>
                        </q-card-section>
                    </q-card>
                </div>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/taxaKingdomSelector.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const dynamicTaxonomicTreeViewer = Vue.createApp({
                components: {
                    'selector-input-element': selectorInputElement,
                    'taxa-kingdom-selector': taxaKingdomSelector,
                    'text-field-input-element': textFieldInputElement
                },
                setup() {
                    const { showNotification } = useCore();
                    const baseStore = useBaseStore();

                    const clientRoot = baseStore.getClientRoot;
                    const containerHeight = Vue.ref(0);
                    const containerWidth = Vue.ref(0);
                    const defsElement = Vue.ref(null);
                    const gElement = Vue.ref(null);
                    const gLinkElement = Vue.ref(null);
                    const gNodeElement = Vue.ref(null);
                    const initialCenter = Vue.ref(true);
                    const layoutTypeOptions = [
                        'horizontal',
                        'vertical',
                        'circular'
                    ];
                    const linkLayoutOptions = [
                        'bezier',
                        'orthogonal'
                    ];
                    const marginXValue = Vue.ref(1000);
                    const marginYValue = Vue.ref(5000);
                    const nodeArr = Vue.ref([]);
                    const selectedKingdom = Vue.ref(null);
                    const selectedLayoutType = Vue.ref('horizontal');
                    const selectedLinkLayout = Vue.ref('bezier');
                    const selectedType = Vue.ref('tree');
                    const svgElement = Vue.ref(null);
                    let treeData = Vue.ref({});
                    const treeDisplayRef = Vue.ref(null);
                    const typeOptions = [
                        'tree',
                        'cluster'
                    ];
                    const zoom = d3.zoom().on('zoom', zoomed);

                    const cx = Vue.computed(() => {
                        return (containerWidth.value * 0.5);
                    });
                    const cy = Vue.computed(() => {
                        return (containerHeight.value * 0.59);
                    });
                    const diagonal = Vue.computed(() => {
                        if(selectedLinkLayout.value === 'bezier'){
                            if(selectedLayoutType.value === 'horizontal'){
                                return d3.link(d3.curveBumpX).x(d => d.y).y(d => d.x);
                            }
                            else if(selectedLayoutType.value === 'vertical'){
                                return d3.link(d3.curveBumpY).x(d => d.x).y(d => d.y);
                            }
                            else{
                                return d3.linkRadial().angle(d => d.x).radius(d => d.y);
                            }
                        }
                        else{
                            if(selectedLayoutType.value === 'horizontal'){
                                return d3.link(d3.curveStep).x(d => d.y).y(d => d.x);
                            }
                            else if(selectedLayoutType.value === 'vertical'){
                                return d3.link(d3.curveStepAfter).x(d => d.x).y(d => d.y);
                            }
                            else{
                                return d3.linkRadial().angle(d => d.x).radius(d => d.y);
                            }
                        }
                    });
                    const root = Vue.computed(() => {
                        return d3.hierarchy(treeData.value);
                    });
                    const treeRadius = Vue.computed(() => {
                        return ((Math.min(containerWidth.value, containerHeight.value)));
                    });

                    const tree = Vue.computed(() => {
                        if(selectedLayoutType.value === 'circular'){
                            if(selectedType.value === 'tree'){
                                return d3.tree()
                                    .size([2 * Math.PI, ((treeRadius.value * root.value.height) * 10)])
                                    .separation((a, b) => (a.parent === b.parent ? 1 : 2) / a.depth);
                            }
                            else{
                                return d3.cluster()
                                    .size([2 * Math.PI, ((treeRadius.value * root.value.height) * 10)])
                                    .separation((a, b) => (a.parent === b.parent ? 1 : 2) / a.depth);
                            }
                        }
                        else{
                            if(selectedType.value === 'tree'){
                                return d3.tree().nodeSize([marginXValue.value, marginYValue.value]);
                            }
                            else{
                                return d3.cluster().nodeSize([marginXValue.value, marginYValue.value]);
                            }
                        }
                    });

                    function centerTree() {
                        d3.select('svg')
                            .transition()
                            .call(zoom.translateTo, (0.5 * containerWidth.value), (0.5 * containerHeight.value));
                    }

                    function getTaxonChildren(id, callback) {
                        const formData = new FormData();
                        formData.append('tid', id);
                        formData.append('includeimage', '1');
                        formData.append('limittoaccepted', '1');
                        formData.append('action', 'getTaxonomicTreeChildNodes');
                        fetch(taxaApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                callback(resObj);
                            });
                        });
                    }

                    function setDefs(node) {
                        if(node.image){
                            const patternElement = defsElement.value.append('pattern')
                                .attr('id', node.tid.toString())
                                .attr('height', '100%')
                                .attr('width', '100%');
                            patternElement.append('image')
                                .attr('preserveAspectRatio', 'none')
                                .attr('xlink:href', node.image);
                        }
                    }

                    function setDimensions() {
                        containerHeight.value = treeDisplayRef.value.clientHeight;
                        containerWidth.value = treeDisplayRef.value.clientWidth;
                    }

                    function setLayoutType(value) {
                        selectedLayoutType.value = value;
                        if(selectedLayoutType.value === 'circular' && selectedLinkLayout.value === 'orthogonal'){
                            selectedLinkLayout.value = 'bezier';
                        }
                        update(null, root.value);
                        d3.select('svg').transition().call(zoom.scaleBy, 1);
                    }

                    function setLinkLayout(value) {
                        if(selectedLayoutType.value !== 'circular' || value !== 'orthogonal'){
                            selectedLinkLayout.value = value;
                            update(null, root.value);
                        }
                        else{
                            showNotification('negative', 'Orthogonal link layout is not compatable with a circular layout type.');
                        }
                    }

                    function setMarginX(value) {
                        marginXValue.value = value;
                        update(null, root.value);
                    }

                    function setMarginY(value) {
                        marginYValue.value = value;
                        update(null, root.value);
                    }

                    function setPng() {
                        svgElement.value = d3.create('svg')
                            .attr('width', containerWidth.value)
                            .attr('height', containerHeight.value)
                            .attr('viewBox', [-cx.value, -cy.value, containerWidth.value, containerHeight.value])
                            .style('user-select', 'none');
                        defsElement.value = svgElement.value.append('defs');
                        gElement.value = svgElement.value.append('g');
                        gLinkElement.value = gElement.value.append('g')
                            .attr('fill', 'none')
                            .attr('stroke', '#555')
                            .attr('stroke-opacity', 0.6)
                            .attr('stroke-width', 10);
                        gNodeElement.value = gElement.value.append('g');
                        svgElement.value.call(zoom);
                        root.value.x0 = marginYValue.value / 2;
                        root.value.y0 = 0;
                        treeDisplayRef.value.append(svgElement.value.node());
                        update(null, root.value);
                    }

                    function setTreeType(value) {
                        selectedType.value = value;
                        update(null, root.value);
                    }

                    function update(event, source) {
                        let transition;
                        if(!source.hasOwnProperty('x0') || !source.x0){
                            source.x0 = 0;
                        }
                        if(!source.hasOwnProperty('y0') || !source.y0){
                            source.y0 = 0;
                        }
                        const nodes = root.value.descendants();
                        const links = root.value.links();

                        tree.value(root.value);

                        if(Number(source.x0) === 0){
                            transition = svgElement.value.transition()
                                .call(zoom.scaleBy, 0.046)
                                .attr('width', containerWidth.value)
                                .attr('height', containerHeight.value)
                                .attr('viewBox', [(-0.5 * containerWidth.value), (-0.5 * containerHeight.value), containerWidth.value, containerHeight.value]);
                        }
                        else{
                            transition = svgElement.value.transition()
                                .duration(250)
                                .attr('width', containerWidth.value)
                                .attr('height', containerHeight.value);
                        }

                        const node = gNodeElement.value.selectAll('g')
                            .data(nodes, d => d.data.tid);

                        const nodeEnter = node.enter().append('g')
                            .attr('transform', d => {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return `translate(${source.y0}, ${source.x0})`
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return `translate(${source.x0}, ${source.y0})`
                                }
                                else{
                                    return `rotate(${source.x0 * 180 / Math.PI - 90}) translate(${source.y0}, 0)`
                                }
                            })
                            .attr('fill-opacity', 0)
                            .attr('stroke-opacity', 0);

                        nodeEnter.append('circle')
                            .attr('height', 250)
                            .attr('width', 250)
                            .attr('r', 250)
                            .attr('fill', d => {
                                return d.data.image ? `url(#${d.data.tid})` : '#999'
                            })
                            .attr('stroke-width', 10)
                            .attr('cursor', d => {
                                return d.data.expandable ? 'pointer' : 'default'
                            })
                            .attr('pointer-events', d => {
                                return d.data.expandable ? 'all' : null
                            })
                            .on('click', (event, d) => {
                                if(d.data.expandable){
                                    if(d.data.hasOwnProperty('children') && d.data.children){
                                        let parentNode = nodeArr.value.find((node) => Number(node.tid) === Number(d.data.tid));
                                        parentNode.children = null;
                                        update(event, d);
                                    }
                                    else{
                                        getTaxonChildren(d.data.tid, (data) => {
                                            let parentNode = nodeArr.value.find((node) => Number(node.tid) === Number(d.data.tid));
                                            parentNode.children = data;
                                            data.forEach((node) => {
                                                const existingNode = nodeArr.value.find((eNode) => Number(eNode.tid) === Number(node.tid));
                                                if(!existingNode){
                                                    setDefs(node);
                                                    nodeArr.value.push(node);
                                                }
                                            });
                                            update(event, d);
                                        });
                                    }
                                }
                            });

                        nodeEnter.append('text')
                            .attr('dy', '0.31em')
                            .text(d => d.data.sciname)
                            .attr('y', 300)
                            .attr('text-anchor', 'middle')
                            .attr('stroke-linejoin', 'round')
                            .attr('stroke-width', 50)
                            .attr('stroke', 'white')
                            .attr('paint-order', 'stroke')
                            .style('font-size', '60px')
                            .attr('cursor', d => {
                                return d.data.expandable ? 'pointer' : 'default'
                            })
                            .attr('pointer-events', d => {
                                return d.data.expandable ? 'all' : null
                            })
                            .on('click', (event, d) => {
                                const url = clientRoot + '/taxa/index.php?taxon=' + d.data.tid;
                                window.open(url, '_blank');
                            });

                        node.merge(nodeEnter).transition(transition)
                            .attr('transform', d => {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return `translate(${d.y}, ${d.x})`
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return `translate(${d.x}, ${d.y})`
                                }
                                else{
                                    return `rotate(${d.x * 180 / Math.PI - 90}) translate(${d.y}, 0)`
                                }
                            })
                            .attr('fill-opacity', 1)
                            .attr('stroke-opacity', 1);

                        node.exit().transition(transition).remove()
                            .attr('transform', d => {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return `translate(${source.y}, ${source.x})`
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return `translate(${source.x}, ${source.y})`
                                }
                                else{
                                    return `rotate(${source.x * 180 / Math.PI - 90}) translate(${source.y}, 0)`
                                }
                            })
                            .attr('fill-opacity', 0)
                            .attr('stroke-opacity', 0);

                        const link = gLinkElement.value.selectAll('path')
                            .data(links, d => d.target.data.tid);

                        const linkEnter = link.enter().append('path')
                            .attr('d', d => {
                                const o = {x: source.x0, y: source.y0};
                                return diagonal.value({source: o, target: o});
                            });

                        link.merge(linkEnter).transition(transition)
                            .attr('d', diagonal.value);

                        link.exit().transition(transition).remove()
                            .attr('d', d => {
                                const o = {x: source.x, y: source.y};
                                return diagonal.value({source: o, target: o});
                            });

                        root.value.eachBefore(d => {
                            d.x0 = d.x;
                            d.y0 = d.y;
                        });

                        if(initialCenter.value){
                            setTimeout(() => {
                                centerTree();
                                initialCenter.value = false;
                            }, 200);
                        }
                    }

                    function updateSelectedKingdom(kingdomObj) {
                        treeDisplayRef.value.innerHTML = '';
                        nodeArr.value.length = 0;
                        treeData.value = Object.assign({}, {});
                        selectedKingdom.value = kingdomObj;
                        if(selectedKingdom.value){
                            getTaxonChildren(selectedKingdom.value['tid'], (data) => {
                                if(data.length > 0){
                                    setPng();
                                    const rootObj = {
                                        tid: selectedKingdom.value['tid'],
                                        expandable: true,
                                        sciname: selectedKingdom.value['name'],
                                        author: null,
                                        image: null,
                                        children: data
                                    };
                                    treeData.value = Object.assign({}, rootObj);
                                    nodeArr.value.push(rootObj);
                                    data.forEach((node) => {
                                        setDefs(node);
                                        nodeArr.value.push(node);
                                    });
                                    update(null, root.value);
                                }
                            });
                        }
                    }

                    function zoomed(e) {
                        d3.select('svg g').attr('transform', e.transform);
                    }

                    Vue.onMounted(() => {
                        window.addEventListener('resize', setDimensions);
                        setDimensions();
                    });

                    return {
                        layoutTypeOptions,
                        linkLayoutOptions,
                        marginXValue,
                        marginYValue,
                        selectedKingdom,
                        selectedLayoutType,
                        selectedLinkLayout,
                        selectedType,
                        treeDisplayRef,
                        typeOptions,
                        centerTree,
                        setLayoutType,
                        setLinkLayout,
                        setMarginX,
                        setMarginY,
                        setTreeType,
                        updateSelectedKingdom
                    }
                }
            });
            dynamicTaxonomicTreeViewer.use(Quasar, { config: {} });
            dynamicTaxonomicTreeViewer.use(Pinia.createPinia());
            dynamicTaxonomicTreeViewer.mount('#app');
        </script>
    </body>
</html>

