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
        <meta name="description" content="Interactive taxonomic tree for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/d3.v7.js" type="text/javascript"></script>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Interactive Taxonomic Tree</span>
            </div>
            <div class="q-pa-md fit">
                <div class="fit row justify-between q-ma-none q-pa-none q-col-gutter-sm">
                    <div class="col-3 q-pa-sm column q-gutter-sm">
                        <q-card flat bordered>
                            <q-card-section class="column q-gutter-sm q-pa-sm">
                                <div>
                                    <taxa-kingdom-selector :selected-kingdom="selectedKingdom" label="Select Kingdom" @update:selected-kingdom="updateSelectedKingdom"></taxa-kingdom-selector>
                                </div>
                                <div>
                                    <selector-input-element label="Layout Type" :options="layoutTypeOptions" :value="selectedLayoutType" @update:value="setLayoutType"></selector-input-element>
                                </div>
                                <div>
                                    <selector-input-element label="Node Layout" :options="linkLayoutOptions" :value="selectedLinkLayout" @update:value="setLinkLayout"></selector-input-element>
                                </div>
                                <div>
                                    <q-btn color="primary" @click="centerTree();" label="Center Tree" dense tabindex="0" />
                                </div>
                            </q-card-section>
                        </q-card>
                        <template v-if="phylaKeyArr.length > 0">
                            <q-card flat bordered>
                                <q-card-section class="column q-gutter-sm q-pa-sm">
                                    <template v-for="phyla in phylaKeyArr">
                                        <div class="row justify-start">
                                            <div class="q-mr-lg">
                                                <color-picker :color-value="phyla.color" @update:color-picker="(value) => processPhylaLineColorChange(value, phyla.tid)"></color-picker>
                                            </div>
                                            <div class="text-bold self-center">
                                                {{ phyla.sciname }}
                                            </div>
                                        </div>
                                    </template>
                                </q-card-section>
                            </q-card>
                        </template>
                    </div>
                    <div class="col-9 q-pa-sm">
                        <q-card flat bordered>
                            <q-card-section class="q-pa-none">
                                <div ref="treeContainerRef" class="fit">
                                    <div ref="treeDisplayRef" :style="treeStyle"></div>
                                </div>
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
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/colorPicker.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const dynamicTaxonomicTreeViewer = Vue.createApp({
                components: {
                    'color-picker': colorPicker,
                    'selector-input-element': selectorInputElement,
                    'taxa-kingdom-selector': taxaKingdomSelector,
                    'text-field-input-element': textFieldInputElement
                },
                setup() {
                    const { generateRandHexColor, hideWorking, showNotification, showWorking } = useCore();
                    const baseStore = useBaseStore();

                    const clientRoot = baseStore.getClientRoot;
                    const containerHeight = Vue.ref(0);
                    const containerWidth = Vue.ref(0);
                    const defsElement = Vue.ref(null);
                    const diagonal = Vue.computed(() => {
                        if(selectedLinkLayout.value === 'Bezier'){
                            if(selectedLayoutType.value === 'Horizontal'){
                                return d3.link(d3.curveBumpX).x(d => d.y).y(d => d.x);
                            }
                            else if(selectedLayoutType.value === 'Vertical'){
                                return d3.link(d3.curveBumpY).x(d => d.x).y(d => d.y);
                            }
                            else{
                                return d3.linkRadial().angle(d => d.x).radius(d => d.y);
                            }
                        }
                        else{
                            if(selectedLayoutType.value === 'Horizontal'){
                                return d3.link(d3.curveStep).x(d => d.y).y(d => d.x);
                            }
                            else if(selectedLayoutType.value === 'Vertical'){
                                return d3.link(d3.curveStepAfter).x(d => d.x).y(d => d.y);
                            }
                            else{
                                return d3.linkRadial().angle(d => d.x).radius(d => d.y);
                            }
                        }
                    });
                    const gElement = Vue.ref(null);
                    const gLinkElement = Vue.ref(null);
                    const gNodeElement = Vue.ref(null);
                    const initialCenter = Vue.ref(true);
                    const layoutTypeOptions = [
                        'Horizontal',
                        'Vertical',
                        'Circular'
                    ];
                    const linkLayoutOptions = [
                        'Bezier',
                        'Orthogonal'
                    ];
                    const marginXValue = Vue.ref(1000);
                    const marginYValue = Vue.ref(5000);
                    const nodeArr = Vue.ref([]);
                    const phylaKeyArr = Vue.ref([]);
                    const root = Vue.computed(() => {
                        return d3.hierarchy(treeRootData.value);
                    });
                    const selectedKingdom = Vue.ref(null);
                    const selectedLayoutType = Vue.ref('Horizontal');
                    const selectedLinkLayout = Vue.ref('Bezier');
                    const svgElement = Vue.ref(null);
                    const tree = Vue.computed(() => {
                        if(selectedLayoutType.value === 'Circular'){
                            return d3.tree()
                                .size([2 * Math.PI, ((treeRadius.value * root.value.height) * 10)])
                                .separation((a, b) => (a.parent === b.parent ? 1 : 2) / a.depth);
                        }
                        else{
                            return d3.tree()
                                .size([containerHeight.value, containerWidth.value])
                                .nodeSize([marginXValue.value, marginYValue.value]);
                        }
                    });
                    const treeContainerRef = Vue.ref(null);
                    const treeDisplayRef = Vue.ref(null);
                    const treeHeightCenterDifference = Vue.computed(() => {
                        let extremeValue = 0;
                        let evenValue = 0;
                        root.value.descendants().forEach((node) => {
                            if(Math.abs(node.x) > Math.abs(extremeValue)){
                                extremeValue = node.x;
                            }
                            if(Math.abs(node.x) === Math.abs(extremeValue) && Math.abs(node.x) !== extremeValue){
                                evenValue = Math.abs(node.x);
                            }
                        });
                        let difference = Math.abs(extremeValue) - evenValue;
                        if(difference !== 0 && extremeValue > evenValue){
                            difference *= -1;
                        }
                        return difference;
                    });
                    const treeNodeData = Vue.ref([]);
                    const treeRadius = Vue.computed(() => {
                        return Math.min(containerWidth.value, containerHeight.value);
                    });
                    let treeRootData = Vue.ref({});
                    const treeScaleRatio = Vue.ref(1);
                    const treeStyle = Vue.ref(null);
                    const treeXValue = Vue.ref(0);
                    const treeYValue = Vue.ref(0);
                    const zoom = d3.zoom().on('zoom', zoomed);

                    function clearTreeDisplayData() {
                        while(treeDisplayRef.value.firstChild) {
                            treeDisplayRef.value.removeChild(treeDisplayRef.value.firstChild);
                        }
                        initialCenter.value = true;
                        treeStyle.value = null;
                        resetZoom();
                    }

                    function centerTree() {
                        const treeHeight = d3.max(root.value.descendants(), d => d.x) - d3.min(root.value.descendants(), d => d.x);
                        const treeWidth = d3.max(root.value.descendants(), d => d.y) - d3.min(root.value.descendants(), d => d.y);
                        if(Number(treeWidth) > 0 || Number(treeHeight) > 0){
                            const fixedTreeHeight = treeHeight + 500;
                            const fixedTreeWidth = treeWidth + 500;
                            resetZoom();
                            if(selectedLayoutType.value === 'Circular'){
                                treeScaleRatio.value = containerWidth.value / ((fixedTreeWidth - 500) * 2.1);
                            }
                            else if((containerWidth.value / fixedTreeWidth) < (containerHeight.value / fixedTreeHeight)){
                                treeScaleRatio.value = containerWidth.value / fixedTreeWidth;
                            }
                            else{
                                treeScaleRatio.value = containerHeight.value / fixedTreeHeight;
                            }
                            if(selectedLayoutType.value === 'Horizontal'){
                                treeXValue.value = ((containerWidth.value - (fixedTreeWidth * treeScaleRatio.value)) / 2) + (250 * treeScaleRatio.value);
                                treeYValue.value = (containerHeight.value / 2) + ((treeHeightCenterDifference.value * treeScaleRatio.value) / 2);
                            }
                            else if(selectedLayoutType.value === 'Vertical'){
                                treeXValue.value = (containerHeight.value / 2) + ((treeHeightCenterDifference.value * treeScaleRatio.value) / 2);
                                treeYValue.value = ((containerWidth.value - (fixedTreeWidth * treeScaleRatio.value)) / 2) + (250 * treeScaleRatio.value);
                            }
                            else{
                                treeXValue.value = containerWidth.value / 2;
                                treeYValue.value = containerHeight.value / 2;
                            }
                            d3.select('svg').transition().call(zoom.transform, d3.zoomIdentity.translate(treeXValue.value, treeYValue.value).scale(treeScaleRatio.value));
                            d3.select('svg g').attr('transform', 'translate(' + treeXValue.value + ',' + treeYValue.value + ') scale(' + treeScaleRatio.value + ')');
                        }
                    }

                    function getTaxonChildren(id, phylatid, callback) {
                        showWorking();
                        const formData = new FormData();
                        formData.append('tid', id);
                        formData.append('includeimage', '1');
                        formData.append('limittoaccepted', '1');
                        formData.append('action', 'getTaxonomicTreeChildNodes');
                        fetch(taxonHierarchyApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            response.json().then((resObj) => {
                                if(phylatid && resObj.length > 0){
                                    resObj.forEach((node) => {
                                        node['phylatid'] = phylatid;
                                    });
                                }
                                hideWorking();
                                callback(resObj);
                            });
                        });
                    }

                    function processDisplayChange() {
                        clearTreeDisplayData();
                        setDimensions();
                        setPng();
                        setTreeDisplay();
                    }

                    function processPhylaLineColorChange(color, tid) {
                        const phylaData = phylaKeyArr.value.find(phyla => Number(phyla['phylatid']) === Number(tid));
                        if(phylaData){
                            phylaData.color = color;
                            update(null, root.value);
                        }
                    }

                    function resetZoom() {
                        treeScaleRatio.value = 1;
                        treeXValue.value = 0;
                        treeYValue.value = 0;
                        d3.select('svg').transition().call(zoom.transform, d3.zoomIdentity.translate(treeXValue.value, treeYValue.value).scale(treeScaleRatio.value));
                    }

                    function setDefs(node) {
                        if(node.image){
                            const patternElement = defsElement.value.append('pattern')
                                .attr('id', node.tid.toString())
                                .attr('height', '100%')
                                .attr('width', '100%');
                            patternElement.append('image')
                                .attr('height', '500px')
                                .attr('width', '100%')
                                .attr('preserveAspectRatio', 'none')
                                .attr('xlink:href', node.image);

                            const newPattern = d3.selectAll('pattern')
                                .filter(function() {
                                    return d3.select(this).attr('id') === node.tid.toString();
                                });
                            const bbox = newPattern.select('image').node().getBBox();
                            newPattern.select('image').attr('x', -((bbox.width - 500) * 0.5));
                        }
                    }

                    function setDimensions() {
                        containerWidth.value = treeContainerRef.value.clientWidth;
                        containerHeight.value = containerWidth.value;
                        treeStyle.value = 'width: ' + containerWidth.value + 'px; height: ' + containerHeight.value + 'px;overflow: hidden;';
                    }

                    function setLayoutType(value) {
                        selectedLayoutType.value = value;
                        if(selectedLayoutType.value === 'Circular' && selectedLinkLayout.value === 'Orthogonal'){
                            selectedLinkLayout.value = 'Bezier';
                        }
                        if(Object.keys(treeRootData.value).length > 0){
                            update(null, root.value);
                            centerTree();
                        }
                    }

                    function setLinkLayout(value) {
                        if(selectedLayoutType.value !== 'Circular' || value !== 'Orthogonal'){
                            selectedLinkLayout.value = value;
                            if(Object.keys(treeRootData.value).length > 0){
                                update(null, root.value);
                                centerTree();
                            }
                        }
                        else{
                            showNotification('negative', 'Orthogonal node layout is not compatable with a circular layout type.');
                        }
                    }

                    function setPhylaKeyArr(phylaArr) {
                        phylaArr.forEach((node) => {
                            node['color'] = generateRandHexColor();
                            phylaKeyArr.value.push(node);
                        });
                    }

                    function setPng() {
                        svgElement.value = d3.create('svg')
                            .attr('width', '100%')
                            .attr('height', '100%')
                            .attr('viewBox', `0 0 ${containerWidth.value} ${containerHeight.value}`)
                            .attr('preserveAspectRatio', 'none');
                        defsElement.value = svgElement.value.append('defs');
                        gElement.value = svgElement.value.append('g');
                        gLinkElement.value = gElement.value.append('g')
                            .attr('fill', 'none');
                        gNodeElement.value = gElement.value.append('g');
                        svgElement.value.call(zoom);
                        root.value.x0 = 0;
                        root.value.y0 = 0;
                        treeDisplayRef.value.append(svgElement.value.node());
                        update(null, root.value);
                    }

                    function setTreeDisplay() {
                        nodeArr.value.length = 0;
                        setDefs(treeRootData.value);
                        nodeArr.value.push(treeRootData.value);
                        treeNodeData.value.forEach((node) => {
                            setDefs(node);
                            nodeArr.value.push(node);
                        });
                        update(null, root.value);
                    }

                    function update(event, source) {
                        if(!source.hasOwnProperty('x0') || !source.x0){
                            source.x0 = 0;
                        }
                        if(!source.hasOwnProperty('y0') || !source.y0){
                            source.y0 = 0;
                        }
                        const nodes = root.value.descendants();
                        const links = root.value.links();

                        tree.value(root.value);

                        const node = gNodeElement.value.selectAll('g')
                            .data(nodes, d => d.data.tid);

                        const nodeEnter = node.enter().append('g')
                            .attr('transform', () => {
                                if(selectedLayoutType.value === 'Horizontal'){
                                    return `translate(${source.y0}, ${source.x0})`
                                }
                                else if(selectedLayoutType.value === 'Vertical'){
                                    return `translate(${source.x0}, ${source.y0})`
                                }
                                else{
                                    return `rotate(${(source.x0 * 180 / Math.PI) - 90}) translate(${source.y0}, 0) rotate(${-(source.x0 * 180 / Math.PI) + 90})`
                                }
                            })
                            .attr('fill-opacity', 0)
                            .attr('stroke-opacity', 0)
                            .style('opacity', 0);

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
                            .attr('tabindex', d => {
                                return d.data.expandable ? '0' : null
                            })
                            .attr('aria-label', d => {
                                if(d.data.expandable){
                                    if(d.data.hasOwnProperty('children') && d.data.children){
                                        return ('Hide ' + d.data.sciname);
                                    }
                                    else{
                                        return ('Expand ' + d.data.sciname);
                                    }
                                }
                                else{
                                    return null;
                                }
                            })
                            .on('keydown', (event, d) => {
                                if(event.key === 'Enter' && d.data.expandable) {
                                    if(d.data.hasOwnProperty('children') && d.data.children){
                                        let parentNode = nodeArr.value.find((node) => Number(node.tid) === Number(d.data.tid));
                                        parentNode.children = null;
                                        update(event, d);
                                    }
                                    else{
                                        getTaxonChildren(d.data.tid, d.data.phylatid, (data) => {
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
                            })
                            .on('click', (event, d) => {
                                if(d.data.expandable){
                                    if(d.data.hasOwnProperty('children') && d.data.children){
                                        let parentNode = nodeArr.value.find((node) => Number(node.tid) === Number(d.data.tid));
                                        parentNode.children = null;
                                        update(event, d);
                                    }
                                    else{
                                        getTaxonChildren(d.data.tid, d.data.phylatid, (data) => {
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
                            .attr('cursor', 'pointer')
                            .attr('tabindex', '0')
                            .attr('aria-label', d => {
                                return (d.data.sciname + ' taxon profile page page - Opens in separate tab')
                            })
                            .attr('pointer-events', d => {
                                return d.data.expandable ? 'all' : null
                            })
                            .on('keydown', (event, d) => {
                                if(event.key === 'Enter') {
                                    const url = clientRoot + '/taxa/index.php?taxon=' + d.data.tid;
                                    window.open(url, '_blank');
                                }
                            })
                            .on('click', (event, d) => {
                                const url = clientRoot + '/taxa/index.php?taxon=' + d.data.tid;
                                window.open(url, '_blank');
                            });

                        node.merge(nodeEnter).transition()
                            .attr('transform', d => {
                                if(selectedLayoutType.value === 'Horizontal'){
                                    return `translate(${d.y}, ${d.x})`
                                }
                                else if(selectedLayoutType.value === 'Vertical'){
                                    return `translate(${d.x}, ${d.y})`
                                }
                                else{
                                    return `rotate(${(d.x * 180 / Math.PI) - 90}) translate(${d.y}, 0) rotate(${-(d.x * 180 / Math.PI) + 90})`
                                }
                            })
                            .attr('fill-opacity', 1)
                            .attr('stroke-opacity', 1)
                            .transition().duration(500)
                            .style('opacity', 1);

                        node.exit().transition().remove()
                            .attr('transform', () => {
                                if(selectedLayoutType.value === 'Horizontal'){
                                    return `translate(${source.y}, ${source.x})`
                                }
                                else if(selectedLayoutType.value === 'Vertical'){
                                    return `translate(${source.x}, ${source.y})`
                                }
                                else{
                                    return `rotate(${(source.x * 180 / Math.PI) - 90}) translate(${source.y}, 0) rotate(${-(source.x * 180 / Math.PI) + 90})`
                                }
                            })
                            .attr('fill-opacity', 0)
                            .attr('stroke-opacity', 0);

                        const link = gLinkElement.value.selectAll('path')
                            .data(links, d => d.target.data.tid);

                        const linkEnter = link.enter().append('path')
                            .attr('d', () => {
                                const o = {x: source.x0, y: source.y0};
                                return diagonal.value({source: o, target: o});
                            })
                            .attr('stroke-opacity', d => {
                                return d.source.parent ? 1 : 0.6
                            })
                            .attr('stroke', d => {
                                if(!d.source.parent){
                                    return '#555'
                                }
                                else{
                                    const phylaData = phylaKeyArr.value.find(phyla => Number(phyla['phylatid']) === Number(d.source.data.phylatid));
                                    if(phylaData){
                                        return phylaData['color'];
                                    }
                                    else{
                                        return '#555'
                                    }
                                }
                            })
                            .attr('stroke-width', d => {
                                const level = Number(d.source.depth) + 1;
                                return 10 + ((1 / level) * 50);
                            })
                            .style('opacity', 0);

                        link.merge(linkEnter).transition()
                            .attr('d', diagonal.value)
                            .transition().duration(500)
                            .style('opacity', 1);

                        link.exit().transition().remove()
                            .attr('d', () => {
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
                        clearTreeDisplayData();
                        phylaKeyArr.value = [];
                        treeNodeData.value = [];
                        treeRootData.value = Object.assign({}, {});
                        selectedKingdom.value = kingdomObj;
                        if(selectedKingdom.value){
                            getTaxonChildren(selectedKingdom.value['tid'], null, (data) => {
                                if(data.length > 0){
                                    setDimensions();
                                    setPng();
                                    const formData = new FormData();
                                    formData.append('tidArr', JSON.stringify([selectedKingdom.value['tid'].toString()]));
                                    formData.append('includetagged', '1');
                                    formData.append('includeoccurrence', '0');
                                    formData.append('limitPerTaxon', '1');
                                    formData.append('sortsequenceLimit', '50');
                                    formData.append('action', 'getTaxonArrDisplayImageData');
                                    fetch(imageApiUrl, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then((response) => {
                                        return response.ok ? response.json() : null;
                                    })
                                    .then((resObj) => {
                                        const rootObj = {
                                            tid: selectedKingdom.value['tid'],
                                            expandable: true,
                                            sciname: selectedKingdom.value['name'],
                                            author: null,
                                            image: (resObj.hasOwnProperty(selectedKingdom.value['tid'].toString()) && resObj[selectedKingdom.value['tid'].toString()].length > 0) ? resObj[selectedKingdom.value['tid'].toString()][0]['url'] : null,
                                            children: data
                                        };
                                        data.forEach((node) => {
                                            node['phylatid'] = node['tid'];
                                        });
                                        setPhylaKeyArr(data.slice());
                                        treeNodeData.value = data.slice();
                                        treeRootData.value = Object.assign({}, rootObj);
                                        setTreeDisplay();
                                    });
                                }
                            });
                        }
                    }

                    function zoomed(event) {
                        if(event.hasOwnProperty('sourceEvent') && event['sourceEvent']){
                            if(event['sourceEvent']['type'] === 'mousemove'){
                                treeXValue.value = event.transform.x;
                                treeYValue.value = event.transform.y;
                            }
                            else if(event['sourceEvent']['type'] === 'wheel'){
                                treeScaleRatio.value = event.transform.k;
                            }
                            d3.select('svg g').attr('transform', 'translate(' + treeXValue.value + ',' + treeYValue.value + ') scale(' + treeScaleRatio.value + ')');
                        }
                    }

                    Vue.onMounted(() => {
                        window.addEventListener('resize', processDisplayChange);
                        setDimensions();
                    });

                    return {
                        clientRoot,
                        layoutTypeOptions,
                        linkLayoutOptions,
                        marginXValue,
                        marginYValue,
                        phylaKeyArr,
                        selectedKingdom,
                        selectedLayoutType,
                        selectedLinkLayout,
                        treeContainerRef,
                        treeDisplayRef,
                        treeStyle,
                        centerTree,
                        processPhylaLineColorChange,
                        setLayoutType,
                        setLinkLayout,
                        updateSelectedKingdom
                    }
                }
            });
            dynamicTaxonomicTreeViewer.use(Quasar, { config: {} });
            dynamicTaxonomicTreeViewer.use(Pinia.createPinia());
            dynamicTaxonomicTreeViewer.mount('#mainContainer');
        </script>
    </body>
</html>

