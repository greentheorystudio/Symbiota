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
        <script type="text/javascript">
            const dynamicTaxonomicTreeViewer = Vue.createApp({
                components: {
                    'selector-input-element': selectorInputElement,
                    'text-field-input-element': textFieldInputElement
                },
                setup() {
                    const containerHeight = Vue.ref(1945);
                    const containerWidth = Vue.ref(928);
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
                    const radiusValue = Vue.ref(3);
                    const selectedLayoutType = Vue.ref('horizontal');
                    const selectedLinkLayout = Vue.ref('bezier');
                    const selectedType = Vue.ref('tree');
                    const textMarginValue = Vue.ref(6);
                    const treeData = data;
                    const treeDisplayRef = Vue.ref(null);
                    const typeOptions = [
                        'tree',
                        'cluster'
                    ];

                    const diagonal = Vue.computed(() => {
                        if(selectedLinkLayout.value === 'bezier'){
                            if(selectedLayoutType.value === 'horizontal'){
                                return d3.link(d3.curveBumpX).x(d => d.y).y(d => d.x);
                            }
                            else if(selectedLayoutType.value === 'vertical'){
                                return d3.link(d3.curveBumpY).x(d => d.x).y(d => d.y);
                            }
                            else{

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

                            }
                        }
                    });
                    const root = d3.hierarchy(treeData);
                    let tree = d3.tree().nodeSize([marginXValue.value, marginYValue.value]);

                    const svg = d3.create('svg')
                        .attr('width', containerWidth.value)
                        .attr('height', containerHeight.value)
                        .attr('viewBox', [(-1 * (containerWidth.value * 0.2)), (-1 * (containerHeight.value / 2)), containerWidth.value, containerHeight.value])
                        .style('font', '10px sans-serif')
                        .style('user-select', 'none');
                    const g = svg.append('g');
                    const gLink = svg.append('g')
                        .attr('fill', 'none')
                        .attr('stroke', '#555')
                        .attr('stroke-opacity', 0.4)
                        .attr('stroke-width', 1.5);
                    const gNode = svg.append('g')
                        .attr('cursor', 'pointer')
                        .attr('pointer-events', 'all');

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

                                }
                            });
                        });
                    }

                    function setDimensions() {
                        containerHeight.value = treeDisplayRef.value.clientHeight;
                        containerWidth.value = treeDisplayRef.value.clientWidth;
                        setPng();
                    }

                    function setLayoutType(value) {
                        selectedLayoutType.value = value;
                        update(null, root);
                    }

                    function setLinkLayout(value) {
                        selectedLinkLayout.value = value;
                        update(null, root);
                    }

                    function setMarginX(value) {
                        marginXValue.value = value;
                        tree = d3.tree().nodeSize([marginXValue.value, marginYValue.value]);
                        update(null, root);
                    }

                    function setMarginY(value) {
                        marginYValue.value = value;
                        tree = d3.tree().nodeSize([marginXValue.value, marginYValue.value]);
                        update(null, root);
                    }

                    function setPng() {
                        const extent = [[0, 0], [containerWidth.value - 0, containerHeight.value - 0]];
                        svg.call(d3.zoom()
                            .scaleExtent([-100, 100])
                            .translateExtent(extent)
                            .extent(extent)
                            .on('zoom', zoomed));

                        root.x0 = marginYValue.value / 2;
                        root.y0 = 0;
                        root.descendants().forEach((d, i) => {
                            d.id = i;
                            d._children = d.children;
                            if(d.depth && d.data.name.length !== 7) {
                                d.children = null;
                            }
                        });
                        treeDisplayRef.value.append(svg.node());
                        update(null, root);
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
                        if(selectedType.value === 'tree'){
                            tree = d3.tree().nodeSize([marginXValue.value, marginYValue.value]);
                        }
                        else{
                            tree = d3.cluster ().nodeSize([marginXValue.value, marginYValue.value]);
                        }
                        update(null, root);
                    }

                    function update(event, source) {
                        const duration = event?.altKey ? 2500 : 250;
                        const nodes = root.descendants().reverse();
                        const links = root.links();

                        tree(root);

                        let left = root;
                        let right = root;
                        root.eachBefore(node => {
                            if(node.x < left.x) {
                                left = node;
                            }
                            if(node.x > right.x) {
                                right = node;
                            }
                        });

                        const height = right.x - left.x;

                        const transition = svg.transition()
                            .duration(duration)
                            .attr('width', containerWidth.value)
                            .attr('height', containerHeight.value)
                            .attr('viewBox', [(-1 * (containerWidth.value * 0.2)), (-1 * (containerHeight.value / 2)), containerWidth.value, height])
                            .tween('resize', window.ResizeObserver ? null : () => () => svg.dispatch('toggle'));

                        const node = gNode.selectAll('g')
                            .data(nodes, d => d.id);

                        const nodeEnter = node.enter().append('g')
                            .attr('transform', d => {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return `translate(${source.y0},${source.x0})`
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return `translate(${source.x0},${source.y0})`
                                }
                                else{

                                }
                            })
                            .attr('fill-opacity', 0)
                            .attr('stroke-opacity', 0)
                            .on('click', (event, d) => {
                                d.children = d.children ? null : d._children;
                                update(event, d);
                            });

                        nodeEnter.append('circle')
                            .attr('r', radiusValue.value)
                            .attr('fill', d => d._children ? '#555' : '#999')
                            .attr('stroke-width', 10);

                        nodeEnter.append('text')
                            .attr('dy', '0.25em')
                            .attr('text-type', d => d._children ? 'parent' : 'child')
                            .text(d => d.data.name)
                            .attr('stroke-linejoin', 'round')
                            .attr('stroke-width', 3)
                            .attr('stroke', 'white')
                            .attr('paint-order', 'stroke');

                        node.merge(nodeEnter).transition(transition)
                            .attr('transform', d => {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return `translate(${d.y},${d.x})`
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return `translate(${d.x},${d.y})`
                                }
                                else{

                                }
                            })
                            .attr('fill-opacity', 1)
                            .attr('stroke-opacity', 1);

                        node.exit().transition(transition).remove()
                            .attr('transform', d => {
                                if(selectedLayoutType.value === 'horizontal'){
                                    return `translate(${source.y},${source.x})`
                                }
                                else if(selectedLayoutType.value === 'vertical'){
                                    return `translate(${source.x},${source.y})`
                                }
                                else{

                                }
                            })
                            .attr('fill-opacity', 0)
                            .attr('stroke-opacity', 0);

                        const link = gLink.selectAll('path')
                            .data(links, d => d.target.id);

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

                        root.eachBefore(d => {
                            d.x0 = d.x;
                            d.y0 = d.y;
                        });

                        processText();
                    }

                    function zoomed({transform}) {
                        svg.attr('transform', transform);
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
                        selectedLayoutType,
                        selectedLinkLayout,
                        selectedType,
                        textMarginValue,
                        treeData,
                        treeDisplayRef,
                        typeOptions,
                        setLayoutType,
                        setLinkLayout,
                        setMarginX,
                        setMarginY,
                        setRadius,
                        setTextMargin,
                        setTreeType
                    }
                }
            });
            dynamicTaxonomicTreeViewer.use(Quasar, { config: {} });
            dynamicTaxonomicTreeViewer.use(Pinia.createPinia());
            dynamicTaxonomicTreeViewer.mount('#app');
        </script>
    </body>
</html>

