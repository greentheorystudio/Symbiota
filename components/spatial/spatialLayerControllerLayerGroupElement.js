const spatialLayerControllerLayerGroupElement = {
    props: {
        layerGroup: {
            type: Object,
            default: {}
        },
        layersInfoObj: {
            type: Object,
            default: {}
        }
    },
    template: `
        <q-expansion-item class="shadow-1 overflow-hidden expansion-element layer-controller-element" :label="layerGroup['name']" header-class="bg-grey-3 text-bold text-center" expand-icon-class="text-bold">
            <div class="q-mt-sm q-pa-md column items-center q-gutter-md">
                <template v-for="layer in layerGroup['layers']">
                    <spatial-layer-controller-layer-element :layer="layersInfoObj[layer['id']]" query="true" sortable="true" symbology="true"></spatial-layer-controller-layer-element>
                </template>
            </div>
        </q-expansion-item>
    `,
    components: {
        'spatial-layer-controller-layer-element': spatialLayerControllerLayerElement
    }
};
