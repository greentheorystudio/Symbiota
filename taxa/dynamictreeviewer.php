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
        <style>
            .tree-container {
                min-height: 600px;
                overflow: hidden;
            }
        </style>
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
                    <div class="col-3 q-pa-sm">
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
                                    <q-btn color="primary" @click="focusTree();" label="Center Tree" dense tabindex="0" />
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
                    const { hideWorking, showNotification, showWorking } = useCore();
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
                    const root = Vue.computed(() => {
                        return d3.hierarchy(treeData.value);
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
                    let treeData = Vue.ref({});
                    const treeDisplayRef = Vue.ref(null);
                    const treeRadius = Vue.computed(() => {
                        return ((Math.min(containerWidth.value, containerHeight.value)));
                    });
                    const treeScaleRatio = Vue.ref(1);
                    const zoom = d3.zoom().on('zoom', zoomed);

                    function focusTree() {
                        const treeWidth = d3.max(root.value.descendants(), d => d.x) - d3.min(root.value.descendants(), d => d.x);
                        const treeHeight = d3.max(root.value.descendants(), d => d.y) - d3.min(root.value.descendants(), d => d.y);
                        if(Number(treeWidth) > 0 && Number(treeHeight) > 0){
                            if((containerWidth.value / treeWidth) > (containerHeight.value / treeHeight)){
                                treeScaleRatio.value = containerWidth.value / treeWidth;
                            }
                            else{
                                treeScaleRatio.value = containerHeight.value / treeHeight;
                            }
                            console.log(treeScaleRatio.value);
                            d3.select('svg g').transition()
                                .duration(250)
                                .call(zoom.scaleBy, treeScaleRatio.value)
                                .attr('width', '100%')
                                .attr('height', '100%')
                                .attr('viewBox', `0 0 ${containerWidth.value} ${containerHeight.value}`)
                                .attr('preserveAspectRatio', 'none');
                        }
                    }

                    function getTaxonChildren(id, callback) {
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
                                hideWorking();
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
                        containerHeight.value = treeDisplayRef.value.clientHeight;
                        containerWidth.value = treeDisplayRef.value.clientWidth;
                    }

                    function setLayoutType(value) {
                        selectedLayoutType.value = value;
                        if(selectedLayoutType.value === 'Circular' && selectedLinkLayout.value === 'Orthogonal'){
                            selectedLinkLayout.value = 'Bezier';
                        }
                        if(Object.keys(treeData.value).length > 0){
                            update(null, root.value);
                            d3.select('svg').transition().call(zoom.scaleBy, 1);
                        }
                    }

                    function setLinkLayout(value) {
                        if(selectedLayoutType.value !== 'Circular' || value !== 'Orthogonal'){
                            selectedLinkLayout.value = value;
                            if(Object.keys(treeData.value).length > 0){
                                update(null, root.value);
                                d3.select('svg').transition().call(zoom.scaleBy, 1);
                            }
                        }
                        else{
                            showNotification('negative', 'Orthogonal node layout is not compatable with a circular layout type.');
                        }
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
                            .attr('fill', 'none')
                            .attr('stroke', '#555')
                            .attr('stroke-opacity', 0.6)
                            .attr('stroke-width', 10);
                        gNodeElement.value = gElement.value.append('g');
                        svgElement.value.call(zoom);
                        root.value.x0 = 0;
                        root.value.y0 = 0;
                        treeDisplayRef.value.append(svgElement.value.node());
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
                            .attr('transform', d => {
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
                            .attr('stroke-opacity', 1);

                        node.exit().transition().remove()
                            .attr('transform', d => {
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
                            .attr('d', d => {
                                const o = {x: source.x0, y: source.y0};
                                return diagonal.value({source: o, target: o});
                            });

                        link.merge(linkEnter).transition()
                            .attr('d', diagonal.value);

                        link.exit().transition().remove()
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
                                focusTree();
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
                                        setDefs(rootObj);
                                        treeData.value = Object.assign({}, rootObj);
                                        nodeArr.value.push(rootObj);
                                        data.forEach((node) => {
                                            setDefs(node);
                                            nodeArr.value.push(node);
                                        });
                                        update(null, root.value);
                                    });
                                }
                            });
                        }
                    }

                    function zoomed(event) {
                        if(event.hasOwnProperty('sourceEvent') && event['sourceEvent']){
                            if(event['sourceEvent']['type'] === 'mousemove'){

                            }
                            else if(event['sourceEvent']['type'] === 'wheel'){
                                treeScaleRatio.value = event.transform.k;
                            }
                            event.transform.k = treeScaleRatio.value;
                            console.log(event.transform);
                            d3.select('svg g').attr('transform', event.transform);
                        }
                    }

                    Vue.onMounted(() => {
                        window.addEventListener('resize', setDimensions);
                        setDimensions();
                    });

                    return {
                        clientRoot,
                        layoutTypeOptions,
                        linkLayoutOptions,
                        marginXValue,
                        marginYValue,
                        selectedKingdom,
                        selectedLayoutType,
                        selectedLinkLayout,
                        treeDisplayRef,
                        focusTree,
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

