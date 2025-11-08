const spatialLayerControllerPopup = {
    template: `
        <q-dialog class="z-max" v-model="mapSettings.showLayerController" persistent>
            <q-card class="lg-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="updateMapSettings('showLayerController', false);" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <q-scroll-area class="layer-controller-scroller">
                    <div class="q-pa-sm column items-center q-gutter-md">
                        <template v-if="layerCount > 0">
                            <template v-if="layersInfoObj['pointv']">
                                <spatial-layer-controller-layer-element :layer="layersInfoObj['pointv']" removable="true"></spatial-layer-controller-layer-element>
                            </template>
                            <template v-if="layersInfoObj['select']">
                                <spatial-layer-controller-layer-element :layer="layersInfoObj['select']" query="true" removable="true"></spatial-layer-controller-layer-element>
                            </template>
                            <template v-if="layersInfoObj['dragDrop1']">
                                <spatial-layer-controller-layer-element :layer="layersInfoObj['dragDrop1']" query="true" removable="true" sortable="true" symbology="true"></spatial-layer-controller-layer-element>
                            </template>
                            <template v-if="layersInfoObj['dragDrop2']">
                                <spatial-layer-controller-layer-element :layer="layersInfoObj['dragDrop2']" query="true" removable="true" sortable="true" symbology="true"></spatial-layer-controller-layer-element>
                            </template>
                            <template v-if="layersInfoObj['dragDrop3']">
                                <spatial-layer-controller-layer-element :layer="layersInfoObj['dragDrop3']" query="true" removable="true" sortable="true" symbology="true"></spatial-layer-controller-layer-element>
                            </template>
                            <template v-if="layersInfoObj['dragDrop4']">
                                <spatial-layer-controller-layer-element :layer="layersInfoObj['dragDrop4']" query="true" removable="true" sortable="true" symbology="true"></spatial-layer-controller-layer-element>
                            </template>
                            <template v-if="layersInfoObj['dragDrop5']">
                                <spatial-layer-controller-layer-element :layer="layersInfoObj['dragDrop5']" query="true" removable="true" sortable="true" symbology="true"></spatial-layer-controller-layer-element>
                            </template>
                            <template v-if="layersInfoObj['dragDrop6']">
                                <spatial-layer-controller-layer-element :layer="layersInfoObj['dragDrop6']" query="true" removable="true" sortable="true" symbology="true"></spatial-layer-controller-layer-element>
                            </template>
                            <template v-if="layersConfigArr.length > 0">
                                <template v-for="layerObj in layersConfigArr">
                                    <template v-if="layerObj['type'] === 'layer'">
                                        <spatial-layer-controller-layer-element :layer="layersInfoObj[layerObj['id']]" query="true" sortable="true" symbology="true"></spatial-layer-controller-layer-element>
                                    </template>
                                    <template v-if="layerObj['type'] === 'layerGroup'">
                                        <spatial-layer-controller-layer-group-element :layer-group="layerObj" :layers-info-obj="layersInfoObj"></spatial-layer-controller-layer-group-element>
                                    </template>
                                </template>
                            </template>
                        </template>
                        <template v-else>
                            <div class="text-h6 text-bold">
                                There are currently no layers available.
                            </div>
                        </template>
                    </div>
                </q-scroll-area>
            </q-card>
        </q-dialog>
    `,
    components: {
        'spatial-layer-controller-layer-element': spatialLayerControllerLayerElement,
        'spatial-layer-controller-layer-group-element': spatialLayerControllerLayerGroupElement
    },
    setup() {
        const layerCount = Vue.ref(0);
        const layersConfigArr = Vue.inject('layersConfigArr');
        const layersInfoObj = Vue.inject('layersInfoObj');
        const mapSettings = Vue.inject('mapSettings');

        const updateMapSettings = Vue.inject('updateMapSettings');

        Vue.watch(layersInfoObj, () => {
            setLayerCount();
        });

        function setLayerCount() {
            layerCount.value = Object.keys(layersInfoObj).length;
        }

        Vue.onMounted(() => {
            setLayerCount();
        });
        
        return {
            layerCount,
            layersConfigArr,
            layersInfoObj,
            mapSettings,
            updateMapSettings
        }
    }
};
