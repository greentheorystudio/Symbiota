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
                                <selector-input-element label="Type" :options="typeOptions" :value="selectedType" @update:value="(value) => selectedType = value"></selector-input-element>
                            </div>
                            <div>
                                <selector-input-element label="Layout Type" :options="layoutTypeOptions" :value="selectedLayoutType" @update:value="(value) => selectedLayoutType = value"></selector-input-element>
                            </div>
                            <div>
                                <selector-input-element label="Link Layout" :options="linkLayoutOptions" :value="selectedLinkLayout" @update:value="(value) => selectedLinkLayout = value"></selector-input-element>
                            </div>
                            <div>
                                <text-field-input-element data-type="int" label="Margin x (px)" min-value="0" :value="marginXValue" @update:value="(value) => marginXValue = value"></text-field-input-element>
                            </div>
                            <div>
                                <text-field-input-element data-type="int" label="Margin y (px)" min-value="0" :value="marginYValue" @update:value="(value) => marginYValue = value"></text-field-input-element>
                            </div>
                            <div>
                                <text-field-input-element data-type="int" label="Radius (px)" min-value="0" :value="radiusValue" @update:value="(value) => radiusValue = value"></text-field-input-element>
                            </div>
                            <div>
                                <text-field-input-element data-type="int" label="Text Margin (px)" min-value="0" :value="textMarginValue" @update:value="(value) => textMarginValue = value"></text-field-input-element>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/taxonomy/dynamicTaxaTree.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
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
                    const marginXValue = Vue.ref(30);
                    const marginYValue = Vue.ref(30);
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

                    const marginTop = 0;
                    const marginRight = 0;
                    const marginBottom = 0;
                    const marginLeft = 0;

                    const root = d3.hierarchy(data);
                    const dx = 10;
                    const dy = (containerWidth.value - marginRight - marginLeft) / (1 + root.height);

                    const tree = d3.tree().nodeSize([dx, dy]);
                    const diagonal = d3.linkHorizontal().x(d => d.y).y(d => d.x);

                    const svg = d3.create("svg")
                        .attr("width", containerWidth.value)
                        .attr("height", containerHeight.value)
                        .attr("viewBox", [-10, (-1 * (containerHeight.value / 2)), containerWidth.value, containerHeight.value])
                        .style("font", "10px sans-serif")
                        .style("user-select", "none");

                    const g = svg.append("g");

                    const gLink = svg.append("g")
                        .attr("fill", "none")
                        .attr("stroke", "#555")
                        .attr("stroke-opacity", 0.4)
                        .attr("stroke-width", 1.5);

                    const gNode = svg.append("g")
                        .attr("cursor", "pointer")
                        .attr("pointer-events", "all");

                    const zoomBehaviours = d3.zoom()
                        .scaleExtent([0.05, 3])
                        .on("zoom", zoomed);

                    function setDimensions() {
                        containerHeight.value = treeDisplayRef.value.clientHeight;
                        containerWidth.value = treeDisplayRef.value.clientWidth;
                        setPng();
                    }

                    function setPng() {
                        svg.call(zoomBehaviours);
                        setTimeout(() => zoomBehaviours.translateTo(svg, 0, 0), 100);

                        root.x0 = dy / 2;
                        root.y0 = 0;
                        root.descendants().forEach((d, i) => {
                            d.id = i;
                            d._children = d.children;
                            if(d.depth && d.data.name.length !== 7) {
                                d.children = null;
                            }
                        });

                        update(null, root);

                        treeDisplayRef.value.append(svg.node());
                    }

                    function zoomed({transform}) {
                        svg.attr("transform", transform);
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

                        const height = right.x - left.x + marginTop + marginBottom;

                        const transition = svg.transition()
                            .duration(duration)
                            .attr("width", containerWidth.value)
                            .attr("height", containerHeight.value)
                            .attr("viewBox", [-10, (-1 * (containerHeight.value / 2)), containerWidth.value, containerHeight.value])
                            .tween("resize", window.ResizeObserver ? null : () => () => svg.dispatch("toggle"));

                        const node = gNode.selectAll("g")
                            .data(nodes, d => d.id);

                        const nodeEnter = node.enter().append("g")
                            .attr("transform", d => `translate(${source.y0},${source.x0})`)
                            .attr("fill-opacity", 0)
                            .attr("stroke-opacity", 0)
                            .on("click", (event, d) => {
                                d.children = d.children ? null : d._children;
                                update(event, d);
                            });

                        nodeEnter.append("circle")
                            .attr("r", 2.5)
                            .attr("fill", d => d._children ? "#555" : "#999")
                            .attr("stroke-width", 10);

                        nodeEnter.append("text")
                            .attr("dy", "0.31em")
                            .attr("x", d => d._children ? -6 : 6)
                            .attr("text-anchor", d => d._children ? "end" : "start")
                            .text(d => d.data.name)
                            .attr("stroke-linejoin", "round")
                            .attr("stroke-width", 3)
                            .attr("stroke", "white")
                            .attr("paint-order", "stroke");

                        node.merge(nodeEnter).transition(transition)
                            .attr("transform", d => `translate(${d.y},${d.x})`)
                            .attr("fill-opacity", 1)
                            .attr("stroke-opacity", 1);

                        node.exit().transition(transition).remove()
                            .attr("transform", d => `translate(${source.y},${source.x})`)
                            .attr("fill-opacity", 0)
                            .attr("stroke-opacity", 0);

                        const link = gLink.selectAll("path")
                            .data(links, d => d.target.id);

                        const linkEnter = link.enter().append("path")
                            .attr("d", d => {
                                const o = {x: source.x0, y: source.y0};
                                return diagonal({source: o, target: o});
                            });

                        link.merge(linkEnter).transition(transition)
                            .attr("d", diagonal);

                        link.exit().transition(transition).remove()
                            .attr("d", d => {
                                const o = {x: source.x, y: source.y};
                                return diagonal({source: o, target: o});
                            });

                        root.eachBefore(d => {
                            d.x0 = d.x;
                            d.y0 = d.y;
                        });
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
                        typeOptions
                    }
                }
            });
            dynamicTaxonomicTreeViewer.use(Quasar, { config: {} });
            dynamicTaxonomicTreeViewer.use(Pinia.createPinia());
            dynamicTaxonomicTreeViewer.mount('#app');
        </script>
    </body>
</html>

