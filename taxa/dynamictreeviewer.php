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
            .tree-container {
                min-height: 600px;
                overflow: hidden;
            }
        </style>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/all.min.js" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/d3.v7.js" type="text/javascript"></script>
        <script type="text/javascript">
            const data = <?php include(__DIR__ . '/../collapse-data.json'); ?>;
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
                                <text-field-input-element data-type="int" label="Radius (px)" min-value="0" :value="radiusValue" :clearable="false" @update:value="setRadius"></text-field-input-element>
                            </div>
                            <div>
                                <text-field-input-element data-type="int" label="Text Margin (px)" min-value="0" :value="textMarginValue" :clearable="false" @update:value="setTextMargin"></text-field-input-element>
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
                    const containerHeight = Vue.ref(0);
                    const containerWidth = Vue.ref(0);
                    const layoutTypeOptions = [
                        'horizontal',
                        'vertical',
                        'circular'
                    ];
                    const linkLayoutOptions = [
                        'bezier',
                        'orthogonal'
                    ];
                    const marginXValue = Vue.ref(20);
                    const marginYValue = Vue.ref(150);
                    const nodeArr = Vue.ref([]);
                    const radiusValue = Vue.ref(3);
                    const selectedKingdom = Vue.ref(null);
                    const selectedLayoutType = Vue.ref('horizontal');
                    const selectedLinkLayout = Vue.ref('bezier');
                    const selectedType = Vue.ref('tree');
                    const textMarginValue = Vue.ref(6);
                    let treeData = Vue.ref({});
                    const treeDisplayRef = Vue.ref(null);
                    const typeOptions = [
                        'tree',
                        'cluster'
                    ];

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
                        return (Math.min(containerWidth.value, containerHeight.value) / 2 - 30);
                    });

                    const tree = Vue.computed(() => {
                        if(selectedLayoutType.value === 'circular'){
                            if(selectedType.value === 'tree'){
                                return d3.tree()
                                    .size([2 * Math.PI, treeRadius.value])
                                    .separation((a, b) => (a.parent == b.parent ? 1 : 2) / a.depth);
                            }
                            else{
                                return d3.cluster()
                                    .size([2 * Math.PI, treeRadius.value])
                                    .separation((a, b) => (a.parent == b.parent ? 1 : 2) / a.depth);
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

                    const svg = d3.create('svg')
                        .attr('width', containerWidth.value)
                        .attr('height', containerHeight.value)
                        .attr('viewBox', [-cx.value, -cy.value, containerWidth.value, containerHeight.value])
                        .attr('style', 'width: 100%; height: auto; font: 10px sans-serif;')
                        .style('user-select', 'none');
                    const g = svg.append('g');
                    const gLink = g.append('g')
                        .attr('fill', 'none')
                        .attr('stroke', '#555')
                        .attr('stroke-opacity', 0.4)
                        .attr('stroke-width', 1.5);
                    const gNode = g.append('g')
                        .attr('cursor', 'pointer')
                        .attr('pointer-events', 'all');

                    function getTaxonChildren(id, callback) {
                        const formData = new FormData();
                        formData.append('tid', id);
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

                    function processText() {
                        d3.selectAll('text')
                        .each(function() {
                            const element = d3.select(this);
                            const parentText = element.attr('text-type') === 'parent';
                            element.attr('x', function() {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return parentText ? (-1 * textMarginValue.value) : textMarginValue.value;
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return parentText ? null : textMarginValue.value;
                                }
                                else{
                                    return parentText ? (-1 * textMarginValue.value) : textMarginValue.value;
                                }
                            });
                            element.attr('y', function() {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return null;
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return parentText ? (-1 * textMarginValue.value) : null;
                                }
                                else{
                                    return null;
                                }
                            });
                            element.attr('text-anchor', function() {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return parentText ? 'end' : 'start'
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return 'middle'
                                }
                                else{
                                    return parentText ? 'end' : 'start'
                                }
                            });
                            element.attr('transform', function() {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return null
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return parentText ? null : 'rotate(90)'
                                }
                                else{
                                    return 'rotate(' + ((Number(element.attr('x')) * 180) / (Math.PI - 180)).toString() + ')';
                                }
                            });
                        });
                    }

                    function setDimensions() {
                        containerHeight.value = treeDisplayRef.value.clientHeight;
                        containerWidth.value = treeDisplayRef.value.clientWidth;
                    }

                    function setLayoutType(value) {
                        selectedLayoutType.value = value;
                        update(null, root.value);
                    }

                    function setLinkLayout(value) {
                        selectedLinkLayout.value = value;
                        update(null, root.value);
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
                        svg.call(d3.zoom()
                            .on('zoom', zoomed));
                        root.value.x0 = marginYValue.value / 2;
                        root.value.y0 = 0;
                        treeDisplayRef.value.append(svg.node());
                        update(null, root.value);
                    }

                    function setRadius(value) {
                        radiusValue.value = value;
                        d3.selectAll('circle').attr('r', radiusValue.value);
                    }

                    function setTextMargin(value) {
                        textMarginValue.value = value;
                        processText();
                    }

                    function setTreeType(value) {
                        selectedType.value = value;
                        update(null, root.value);
                    }

                    function update(event, source) {
                        const nodes = root.value.descendants().reverse();
                        const links = root.value.links();

                        tree.value(root.value);

                        let left = root.value;
                        let right = root.value;
                        root.value.eachBefore(node => {
                            if(node.x < left.x) {
                                left = node;
                            }
                            if(node.x > right.x) {
                                right = node;
                            }
                        });

                        const height = right.x - left.x;

                        const transition = svg.transition()
                            .duration(250)
                            .attr('width', containerWidth.value)
                            .attr('height', containerHeight.value)
                            .attr('viewBox', [(-1 * (containerWidth.value * 0.2)), (-1 * (containerHeight.value / 2)), containerWidth.value, height])
                            .tween('resize', window.ResizeObserver ? null : () => () => svg.dispatch('toggle'));

                        const node = gNode.selectAll('g')
                            .data(nodes, d => d.tid);

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
                            .attr('stroke-opacity', 0)
                            .on('click', (event, d) => {
                                if(d.hasOwnProperty('children') && d.children){
                                    let parentNode = nodeArr.value.find((node) => Number(node.tid) === Number(d.data.tid));
                                    parentNode.children = null;
                                    update(event, d);
                                }
                                else{
                                    getTaxonChildren(d.data.tid, (data) => {
                                        let parentNode = nodeArr.value.find((node) => Number(node.tid) === Number(d.data.tid));
                                        parentNode.children = data;
                                        data.forEach((node) => {
                                            nodeArr.value.push(node);
                                        });
                                        update(event, d);
                                    });
                                }
                            });

                        nodeEnter.append('circle')
                            .attr('r', radiusValue.value)
                            .attr('fill', d => {
                                return d.data.expandable ? '#555' : '#999'
                            })
                            .attr('stroke-width', 10);

                        nodeEnter.append('text')
                            .attr('dy', '0.31em')
                            .attr('text-type', d => d.data.expandable ? 'parent' : 'child')
                            .text(d => d.data.sciname)
                            .attr('stroke-linejoin', 'round')
                            .attr('stroke-width', 3)
                            .attr('stroke', 'white')
                            .attr('paint-order', 'stroke');

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

                        const link = gLink.selectAll('path')
                            .data(links, d => d.target.tid);

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

                        processText();
                    }

                    function updateSelectedKingdom(kingdomObj) {
                        treeDisplayRef.value.innerHTML = '';
                        nodeArr.value.length = 0;
                        treeData.value = Object.assign({}, {});
                        selectedKingdom.value = kingdomObj;
                        if(selectedKingdom.value){
                            getTaxonChildren(selectedKingdom.value['tid'], (data) => {
                                if(data.length > 0){
                                    const rootObj = {
                                        tid: selectedKingdom.value['tid'],
                                        expandable: true,
                                        sciname: selectedKingdom.value['name'],
                                        author: null,
                                        children: data
                                    };
                                    treeData.value = Object.assign({}, rootObj);
                                    nodeArr.value.push(rootObj);
                                    data.forEach((node) => {
                                        nodeArr.value.push(node);
                                    });
                                    setPng();
                                }
                            });
                        }
                    }

                    function zoomed(e) {
                        d3.select('svg g')
                            .attr('transform', e.transform);
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
                        radiusValue,
                        selectedKingdom,
                        selectedLayoutType,
                        selectedLinkLayout,
                        selectedType,
                        textMarginValue,
                        treeDisplayRef,
                        typeOptions,
                        setLayoutType,
                        setLinkLayout,
                        setMarginX,
                        setMarginY,
                        setRadius,
                        setTextMargin,
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

