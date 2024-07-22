const dynamicTaxaTree = {
    props: {
        data: {
            type: Object,
            required: false
        },
        selected: {
            type: Object,
            required: false,
            default: null
        },
        duration: {
            type: Number,
            default: 750
        },
        type: {
            type: String,
            default: 'tree'
        },
        layoutType: {
            type: String,
            default: 'horizontal'
        },
        linkLayout: {
            type: String,
            default: 'bezier'
        },
        marginX: {
            type: Number,
            default: 20
        },
        marginY: {
            type: Number,
            default: 20
        },
        nodeText: {
            type: String,
            default: 'name'
        },
        identifier: {
            type: Function,
            default: 0
        },
        zoomable: {
            type: Boolean,
            default: false
        },
        minZoom: {
            type: Number,
            default: 0.8
        },
        maxZoom: {
            type: Number,
            default: 9
        },
        nodeTextDisplay: {
            type: String,
            default: 'all'
        },
        radius: {
            type: Number,
            default: 3
        },
        strokeWidth: {
            type: Number,
            default: 1.5
        },
        leafTextMargin: {
            type: Number,
            default: 6
        },
        nodeTextMargin: {
            type: Number,
            default: 6
        }
    },
    setup() {
        function Point(x, y) {
            return {
                x: x,
                y: y,
                toString: function () {
                    return `${this.x},${this.y}`;
                },
                apply: function (transformation) {
                    return new Point(transformation.applyX(this.x), transformation.applyY(this.y));
                }
            }
        }

        const horizontal = {
            size: function (tree, size, margin, {last, first}) {
                tree.size([size.height - (margin.y * 2), size.width - (margin.x * 2) - (last + first)]);
            },
            transformNode: function (x, y) {
                return Point(y, x);
            },
            transformSvg: function (svg, margin, _, {first}) {
                return svg.attr('transform', `translate(${margin.x + first},${margin.y})`);
            },
            updateTransform: function (transform, {x, y}, _, {first}) {
                return transform.translate(x + first, y);
            },
            getLine: function (d3) {
                return d3.line()
                    .x(d => d.data.x)
                    .y(d => d.data.y);
            },
            verticalLine: function (target) {
                return `L ${this.transformNode(target.x, target.y)}`;
            },
            layoutNode: function (children, {leaf, node}) {
                return {
                    x: !children ? leaf : -node,
                    y: 0,
                    rotate: 0,
                    textRotate: 0,
                    anchor: !children ? 'start' : 'end'
                };
            }
        };

        const vertical = {
            size: function (tree, size, margin, {last}) {
                tree.size([size.width - (margin.x * 2), size.height - (margin.y * 2) - last - 9]);
            },
            transformNode: function (x, y) {
                return Point(x, y);
            },
            transformSvg: function (svg, margin) {
                return svg.attr('transform', `translate(${margin.x},${margin.y + 9})`);
            },
            updateTransform: function (transform, {x, y}) {
                return transform.translate(x, y);
            },
            getLine: function (d3) {
                return d3.line()
                    .x(d => d.data.x)
                    .y(d => d.data.y);
            },
            verticalLine: function (target) {
                return `L ${this.transformNode(target.x, target.y)}`;
            },
            layoutNode: function (children, {leaf, node}) {
                return {
                    x: !children ? leaf : 0,
                    y: !children ? 0 : -(9 + node),
                    rotate: 90,
                    textRotate: !children ? 0 : -90,
                    anchor: !children ? 'start' : 'middle'
                };
            }
        };

        const circular = {
            getRay: function ({width, height}, {x, y}, {xExtreme = null, yExtreme = null}) {
                const firstRay = this.computeRay((width - x) / 2, xExtreme, Math.cos);
                const secondRay = this.computeRay((height - y) / 2, yExtreme, Math.sin);
                return Math.min(firstRay, secondRay);
            },
            computeRay: function (space, extreme, trig) {
                if(!extreme) {
                    return space;
                }
                const available = space - extreme.value;
                const angle = (extreme.x - 90) / 180 * Math.PI;
                return Math.abs(available / trig(angle));
            },
            separation: function (a, b) {
                return (a.parent === b.parent ? 1 : 2) / (a.depth !== 0 ? a.depth : 1);
            },
            size: function (tree, {width, height}, {x, y}, {last}) {
                const ray = Math.min(width - x, height - y) / 2 - last;
                tree.size([360, ray]).separation(this.separation);
            },
            optimizeSize: function (tree, size, margin, extreme) {
                const ray = this.getRay(size, margin, extreme || {});
                tree.size([360, ray]).separation(this.separation);
            },
            transformNode: function (x, y) {
                const angle = (x - 90) / 180 * Math.PI;
                const radius = y;
                return Point(~~(radius * Math.cos(angle)), ~~(radius * Math.sin(angle)));
            },
            transformSvg: function (svg, _, {width, height}) {
                return svg.attr('transform', `translate(${width / 2},${height / 2})`);
            },
            updateTransform: function (transform, _, {width, height}) {
                return transform.translate(width / 2, height / 2);
            },
            getLine: function (d3) {
                return d3.radialLine()
                    .radius(d => d.y)
                    .angle(d => d.x / 180 * Math.PI);
            },
            verticalLine: function (target, source) {
                if(target.x === source.x && target.y === source.y) {
                    return '';
                }
                return `A ${target.y},${target.y} 0 ${Math.abs(target.x - source.x) > 180 ? 1 : 0} ${target.x > source.x ? 1 : 0} ${this.transformNode(target.x, target.y)}`;
            },
            layoutNode: function (children, {leaf, node}, d) {
                const isLeaf = !children;
                const pole = d.x < 180 ? 1 : -1;
                const leafOrReversed = d.x < 180 === isLeaf;
                return {
                    x: (isLeaf ? leaf : -node) * pole,
                    y: 0,
                    rotate: d.x - 90,
                    textRotate: d.x < 180 ? 0 : 180,
                    anchor: leafOrReversed ? 'start' : 'end'
                };
            }
        };

        const bezier = function (source, target, { transformNode }) {
            return `M ${transformNode(source.x, source.y)} C ${transformNode(source.x, (source.y + target.y) / 2)} ` +
                `${transformNode(target.x, (source.y + target.y) / 2)} ${transformNode(target.x, target.y)}`;
        }

        const orthogonal = function (source, target, { transformNode, verticalLine }) {
            return `M ${transformNode(source.x, source.y)} L ${transformNode(source.x, target.y)} ${verticalLine(target, source)}`;
        }

        const behaviorMixin = {
            props: {
                on: {
                    required: true,
                    type: Function
                },
                actions: {
                    required: true,
                    type: Object
                }
            },
            render: () => null
        };

        const collapseOnClick = {
            name: 'collapseOnClick',
            mixins: [behaviorMixin],
            created () {
                const {on, actions: {toggleExpandCollapse}} = this;
                on('clickedNode', ({element}) => {
                    toggleExpandCollapse(element);
                })
            }
        };

        const selectOnTextClick = {
            name: 'SelectOnTextClick',
            mixins: [behaviorMixin],
            created () {
                const {on, actions: {setSelected}} = this;
                on('clickedText', ({element}) => {
                    setSelected(element);
                })
            }
        };

        function compareString (a, b) {
            return (a < b) ? -1 : (a > b) ? 1 : 0;
        }

        function toPromise (transition) {
            let count = 0;
            let interrupted = false;
            transition.each(() => count++);
            return new Promise(function (resolve) {
                if (count === 0) {
                    resolve('ended');
                    return;
                }
                const check = () => {
                    if (--count === 0) {
                        resolve(interrupted ? 'interrupted' : 'ended');
                    }
                }
                transition.on('end', check);
                transition.on('interrupt', () => {
                    interrupted = true;
                    check();
                })
            })
        }

        function mapMany (arr, mapper) {
            return arr.reduce(function (prev, curr) {
                return prev.concat(mapper(curr));
            }, []);
        }

        function translate (vector, { transformNode }, transformation) {
            let destination = transformNode(vector.x, vector.y);
            if (transformation) {
                destination = destination.apply(transformation);
            }
            return `translate( ${destination} )`;
        }

        function renderInVueContext ({scope, props}, onChange) {
            const component = new Vue({
                render: h => {
                    const nodes = scope(props);
                    return h('div', nodes);
                },
                mounted () {
                    this.$once('hook:updated', onChange);
                }
            });
            return component.$mount().$el.innerHTML;
        }

        function onZoom (zoomCallBack) {
            return () => {
                const transform = d3.event.transform;
                zoomCallBack({transform});
            }
        }

        function setUpZoom ({ currentTransform, minZoom, maxZoom, svg }, zoomCallBack) {
            const zoom = d3.zoom().scaleExtent([minZoom, maxZoom]);
            zoom.on('zoom', onZoom(zoomCallBack));
            svg.call(zoom).on('wheel', () => d3.event.preventDefault());
            svg.call(zoom.transform, currentTransform || d3.zoomIdentity);
            return zoom;
        }

        const layoutObj = {
            horizontal,
            circular,
            vertical
        };

        const linkLayouts = {
            bezier,
            orthogonal
        };

        let i = 0;
        const types = ['tree', 'cluster'];
        const layouts = ['circular', 'horizontal', 'vertical'];
        const nodeDisplays = ['all', 'leaves', 'extremities'];
        const linkLayoutsType = ['bezier', 'orthogonal'];

        function hasChildren (d) {
            return d.children || d._children;
        }

        function getChildren (d) {
            return d.children ? {children: d.children, visible: true} : (d._children ? {children: d._children, visible: false} : null);
        }

        function onAllChilddren (d, callback, fatherVisible = undefined) {
            if (callback(d, fatherVisible) === false) {
                return;
            }
            var directChildren = getChildren(d);
            directChildren && directChildren.children.forEach(child => onAllChilddren(child, callback, directChildren.visible));
        }

        function filterTextNode (nodeTextDisplay, root) {
            switch (nodeTextDisplay) {
                case 'all':
                    return d => true;

                case 'leaves':
                    return d => !hasChildren(d);

                case 'extremities':
                    return d => !hasChildren(d) || d === root;
            }
        }

        const defaultBehaviors = [
            collapseOnClick,
            selectOnTextClick
        ];

        const popUpClass = 'pop-up-tree';

        const sendStyle = (callback) => {
            return ({styles}) => callback(styles);
        }

        return {
            horizontal,
            vertical,
            circular,
            bezier,
            orthogonal,
            behaviorMixin,
            collapseOnClick,
            selectOnTextClick,
            compareString,
            toPromise,
            mapMany,
            translate,
            renderInVueContext,
            onZoom,
            setUpZoom,
            layoutObj,
            linkLayouts,
            i,
            types,
            layouts,
            nodeDisplays,
            linkLayoutsType,
            hasChildren,
            getChildren,
            onAllChilddren,
            filterTextNode,
            defaultBehaviors,
            popUpClass
        }
    },
    model: {
        prop: 'selected',
        event: 'change'
    },
    data () {
        return {
            currentTransform: null,
            contextMenu: {
                node: null,
                style: null
            },
            maxTextLenght: {
                first: 0,
                last: 0
            }
        }
    },
    mounted () {
        const size = this.getSize();
        const svg = d3.select(this.$el.parentElement).append('svg')
            .attr('width', size.width)
            .attr('height', size.height)
            .on('click', () => { this.$emit('clickOutside') });
        const {zoomable, tree} = this;
        const g = zoomable ? svg.append('g') : this.transformSvg(svg.append('g'), size);

        this.internaldata = {
            svg,
            g,
            tree
        };

        this.internaldata.zoom = zoomable ? this.setUpZoom() : null;
        this.data && this.onData(this.data);
    },
    methods: {
        setSelected (node) {
            this.$emit('change', node);
        },

        getSize () {
            const {$el: {clientWidth: width, clientHeight: height}} = this;
            return { width, height };
        },

        resize () {
            const size = this.getSize();
            this.internaldata.svg
                .attr('width', size.width)
                .attr('height', size.height);
            this.layout.size(this.internaldata.tree, size, this.margin, this.maxTextLenght);
            this.applyZoom(size);
            this.redraw();
        },

        setUpZoom () {
            const { currentTransform, minZoom, maxZoom, onZoomed, internaldata: { svg } } = this;
            return this.setUpZoom({ currentTransform, minZoom, maxZoom, svg }, onZoomed);
        },

        onZoomed ({transform}) {
            this.$emit('zoom', {transform});
            this._originalZoom = transform;
            this.currentTransform = this.updateTransform(transform);
            this.redraw({transitionDuration: 0});
        },

        removeZoom () {
            const { internaldata } = this;
            internaldata.zoom.on('zoom', null);
            internaldata.zoom = null;
        },

        updateZoom () {
            if (!this.zoomable) {
                return;
            }
            const {minZoom, maxZoom} = this;
            this.internaldata.zoom.scaleExtent([minZoom, maxZoom]);
        },

        completeRedraw ({margin = null, layout = null}) {
            const size = this.getSize();
            this.layout.size(this.internaldata.tree, size, this.margin, this.maxTextLenght);
            this.applyZoom(size, true);
            this.redraw();
        },

        transformSvg (g, size) {
            size = size || this.getSize();
            return this.layout.transformSvg(g, this.margin, size, this.maxTextLenght);
        },

        updateTransform (transform, size) {
            size = size || this.getSize();
            return this.layout.updateTransform(transform, this.margin, size, this.maxTextLenght);
        },

        updateGraph (source, {transitionDuration = undefined} = {}) {
            const {root} = this.internaldata;
            const correctedSource = source || root;
            const originAngle = () => correctedSource.layoutInfo ? correctedSource.layoutInfo.rotate : 0;
            const {currentPosition} = this;
            const getOldPosition = (node) => {
                if (!currentPosition) {
                    return null;
                }
                const visibleParent = node.ancestors().find(({id}) => currentPosition.has(id));
                return visibleParent ? currentPosition.get(visibleParent.id) : null;
            };
            const currentNodesById = new Map();
            const getExitingParentIfAny = (node) => {
                const visibleParent = node.ancestors().find(a => currentNodesById.has(a.id));
                if (!visibleParent) {
                    return {x: correctedSource.x, y: correctedSource.y};
                }
                return currentNodesById.get(visibleParent.id);
            }
            const origin = currentPosition ? currentPosition.get(correctedSource.id) : {x: correctedSource.x0, y: correctedSource.y0};
            const originBuilder = d => {
                if (source || !d.parent) {
                    return origin;
                }
                return getOldPosition(d.parent) || origin;
            };
            const forExit = d => {
                if (source || !d.parent) {
                    return {x: correctedSource.x, y: correctedSource.y};
                }
                return getExitingParentIfAny(d.parent);
            };

            const links = this.internaldata.g.selectAll('.linktree')
                .data(this.internaldata.tree(root).descendants().slice(1), d => d.id);

            const newLinks = links.enter().append('path').attr('class', 'linktree').lower();
            const nodes = this.internaldata.g.selectAll('.nodetree').data(root.descendants(), d => d.id);
            const newNodes = nodes.enter().append('g').attr('class', d => `nodetree node-rank-${d.depth}`);
            const allNodes = newNodes.merge(nodes).each(({id, x, y}) => {
                currentNodesById.set(id, {x, y});
            });

            const { strokeWidth, layout, duration, drawLink } = this;
            transitionDuration = (transitionDuration === undefined) ? duration : transitionDuration;
            const transform = this.currentTransform || d3.zoomIdentity;
            const strokeWidthFinal = `${strokeWidth / transform.k}px`;

            newLinks.attr('d', d => drawLink(originBuilder(d), originBuilder(d), layout))
                .attr('transform', transform)
                .attr('stroke-width', strokeWidthFinal);
            const updateAndNewLinks = links.merge(newLinks);
            const updateAndNewLinksPromise = this.toPromise(updateAndNewLinks
                .transition().duration(transitionDuration)
                .attr('transform', transform)
                .attr('stroke-width', strokeWidthFinal)
                .attrTween('d', function (d) {
                    const previous = d3.select(this).attr('d');
                    const final = drawLink(d, d.parent, layout);
                    return this.interpolatePath(previous, final);
                })
            );
            const exitingLinksPromise = this.toPromise(links.exit().transition().duration(transitionDuration).attr('d', d => drawLink(forExit(d), forExit(d), layout)).remove());
            const {actions, radius, selected, $scopedSlots: {node}} = this;
            const getHtml = node ? d => this.renderInVueContext({
                scope: node,
                props: {
                    actions,
                    radius,
                    node: d,
                    data: d.data,
                    isRetracted: !!d._children,
                    isSelected: d.data === selected
                }
            }, this.redraw) : d => `<circle r="${radius}"/>`;

            newNodes.attr('transform', d => `${this.translate(originBuilder(d), layout, transform)} rotate(${originAngle(d)}) scale(0.1)`)
                .append('g')
                .attr('class', 'node');

            newNodes
                .append('text')
                .attr('dy', '.35em')
                .attr('x', 0)
                .attr('dx', 0)
                .on('click', this.onNodeTextClick)
                .on('mouseover', this.onNodeTextOver)
                .on('mouseleave', this.onNodeTextLeave);

            allNodes
                .select('.node')
                .html(getHtml);

            allNodes.classed('node--internal', d => this.hasChildren(d))
                .classed('node--leaf', d => !this.hasChildren(d))
                .classed('selected', d => d === selected)
                .on('click', this.onNodeClick);

            const { leafTextMargin, nodeTextMargin, layout: {layoutNode}, nodeTextDisplay } = this;
            const showNode = this.filterTextNode(nodeTextDisplay, root);
            allNodes.filter(d => !showNode(d)).select('text').text('');
            const text = allNodes.filter(showNode).select('text').text(d => d.data[this.nodeText]);

            allNodes.each((d) => {
                d.layoutInfo = layoutNode(this.hasChildren(d), {leaf: leafTextMargin, node: nodeTextMargin}, d);
            });

            const allNodesPromise = this.toPromise(allNodes.transition().duration(transitionDuration)
                .attr('transform', d => `${this.translate(d, layout, transform)} rotate(${d.layoutInfo.rotate})`)
                .attr('opacity', 1));

            text.attr('x', d => d.layoutInfo.x)
                .attr('y', d => d.layoutInfo.y)
                .attr('text-anchor', d => d.layoutInfo.anchor)
                .attr('transform', d => `rotate(${d.layoutInfo.textRotate})`);

            this.currentPosition = currentNodesById;

            const exitingNodes = nodes.exit();
            exitingNodes.select('.node').transition().duration(transitionDuration)
                .attr('transform', 'scale(0.1)');

            const exitingNodesPromise = this.toPromise(exitingNodes.transition().duration(transitionDuration)
                .attr('transform', d => `${this.translate(forExit(d), layout, transform)} rotate(${d.parent.layoutInfo.rotate})`)
                .attr('opacity', 0).remove());

            const leaves = root.leaves();
            const extremeNodes = text.filter(d => leaves.indexOf(d) !== -1).nodes();
            const last = Math.max(...extremeNodes.map(node => node.getComputedTextLength())) + leafTextMargin;
            const textNode = text.node();
            const first = (textNode ? textNode.getComputedTextLength() : 0) + leafTextMargin;
            if (last <= this.maxTextLenght.last && first <= this.maxTextLenght.first) {
                this._scheduledRedraw = false;
                return Promise.all([allNodesPromise, exitingNodesPromise, updateAndNewLinksPromise, exitingLinksPromise]);
            }

            this.maxTextLenght = {first, last};
            const size = this.getSize();
            this.applyZoom(size);
            this.layout.size(this.internaldata.tree, size, this.margin, this.maxTextLenght);
            return this.updateGraph(source);
        },

        onNodeTextOver (d) {
            this.onEvent('mouseOverText', d);
        },

        onNodeTextLeave (d) {
            this.onEvent('mouseLeaveText', d);
        },

        onNodeTextClick (d) {
            this.onEvent('clickedText', d);
        },

        onNodeClick (d) {
            this.onEvent('clickedNode', d);
        },

        onEvent (name, d) {
            const event = d3.event;
            this.$emit(name, {element: d, data: d.data, target: event.target});
            event.stopPropagation();
        },

        toggleExpandCollapse (d) {
            if (!d) {
                return Promise.resolve(false);
            }
            return d.children ? this.collapse(d) : this.expand(d);
        },

        onData (data) {
            if (!data) {
                this.internaldata.root = null;
                this.clean();
                return;
            }
            const root = d3.hierarchy(data).sort((a, b) => { return this.compareString(a.data.text, b.data.text) });
            this.internaldata.root = root;
            root.each(d => { d.id = this.identifier(d.data) });
            const size = this.getSize();
            root.x = size.height / 2;
            root.y = 0;
            root.x0 = root.x;
            root.y0 = root.y;
            this.redraw();
        },

        clean () {
            ['.linktree', '.nodetree', 'text', 'circle'].forEach(selector => {
                this.internaldata.g.selectAll(selector).transition().duration(this.duration).attr('opacity', 0).remove();
            });
        },

        redraw () {
            const { internaldata: { root }, _scheduledRedraw } = this;
            if (!root || _scheduledRedraw) {
                return;
            }
            this._scheduledRedraw = true;
            this.$nextTick(() => this.updateGraph(null));
        },

        applyZoom (size, transition) {
            const { internaldata: {g, zoom}, zoomable } = this;
            if (zoomable && zoom) {
                this.currentTransform = this.updateTransform(this._originalZoom);
                return;
            }
            const element = transition ? g.transition().duration(this.duration) : g;
            this.transformSvg(element, size);
        },

        updateIfNeeded (d, update) {
            return update ? this.updateGraph(d).then(() => true) : Promise.resolve(true);
        },

        // API
        collapse (d, update = true) {
            if (!d.children) {
                return Promise.resolve(false);
            }

            d._children = d.children;
            d.children = null;
            this.$emit('retract', {element: d, data: d.data});
            return this.updateIfNeeded(d, update);
        },

        expand (d, update = true) {
            if (!d._children) {
                return Promise.resolve(false);
            }

            d.children = d._children;
            d._children = null;
            this.$emit('expand', {element: d, data: d.data});
            return this.updateIfNeeded(d, update);
        },

        expandAll (d, update = true) {
            this.onAllChilddren(d, child => { this.expand(child, false) });
            return this.updateIfNeeded(null, update);
        },

        collapseAll (d, update = true) {
            this.onAllChilddren(d, child => this.collapse(child, false));
            return this.updateIfNeeded(d, update);
        },

        show (d, update = true) {
            const path = d.ancestors().reverse();
            const root = path.find(node => node.children === null) || d;
            path.forEach(node => this.expand(node, false));
            return this.updateIfNeeded(root, update);
        },

        showOnly (d) {
            const root = this.internaldata.root;
            const path = d.ancestors().reverse();
            const shouldBeRetracted = this.mapMany(path, p => p.children ? p.children : []).filter(node => node && (path.indexOf(node) === -1));
            const mapped = {};
            shouldBeRetracted.filter(node => node.children).forEach(rectractedNode => rectractedNode.each(c => { mapped[c.id] = rectractedNode }));
            const origin = node => {
                const reference = mapped[node.id];
                return !reference ? node : {x: reference.x, y: reference.y};
            };
            const updater = node => {
                if (shouldBeRetracted.indexOf(node) !== -1) {
                    this.collapse(node, false);
                    return false;
                }
                return (node !== d);
            };
            this.onAllChilddren(root, updater);
            return this.updateGraph(origin).then(() => true);
        },

        resetZoom () {
            if (!this.zoomable) {
                return Promise.resolve(false);
            }
            const {svg, zoom} = this.internaldata;
            const transitionPromise = this.toPromise(svg.transition().duration(this.duration).call(zoom.transform, () => d3.zoomIdentity));
            return transitionPromise.then(() => true);
        }
    },
    computed: {
        tree () {
            const size = this.getSize();
            const tree = this.type === 'cluster' ? d3.cluster() : d3.tree();
            this.layout.size(tree, size, this.margin, this.maxTextLenght);
            return tree;
        },

        margin () {
            return {x: this.marginX, y: this.marginY};
        },

        layout () {
            return this.layoutObj[this.layoutType];
        },

        drawLink () {
            return this.linkLayouts[this.linkLayout];
        }
    },
    watch: {
        data: {
            handler: function (current, old) {
                this.onData(current);
            },
            deep: true
        },

        type () {
            if (!this.internaldata.tree) {
                return;
            }
            this.internaldata.tree = this.tree;
            this.redraw();
        },

        marginX (newMarginX, oldMarginX) {
            this.completeRedraw({margin: {x: oldMarginX, y: this.marginY}});
        },

        marginY (newMarginY, oldMarginY) {
            this.completeRedraw({margin: {x: this.marginX, y: oldMarginY}});
        },

        layout (newLayout, oldLayout) {
            this.completeRedraw({layout: oldLayout});
        },

        selected () {
            this.completeRedraw({layout: this.layout});
        },

        radius () {
            this.completeRedraw({layout: this.layout});
        },

        leafTextMargin () {
            this.completeRedraw({layout: this.layout});
        },

        nodeTextMargin () {
            this.completeRedraw({layout: this.layout});
        },

        nodeTextDisplay () {
            this.completeRedraw({layout: this.layout});
        },

        linkLayout () {
            this.completeRedraw({layout: this.layout});
        },

        strokeWidth () {
            this.completeRedraw({layout: this.layout});
        },

        minZoom () {
            this.updateZoom();
        },

        maxZoom () {
            this.updateZoom();
        },

        zoomable (newValue) {
            if (newValue) {
                this.internaldata.zoom = this.setUpZoom();
                return;
            }
            this.removeZoom();
        }
    }
};
