const layersConfigurationsLayerElement = {
    props: {
        layer: {
            type: Object,
            default: null
        }
    },
    template: `
        <q-card flat bordered class="full-width q-pa-sm map-configurations-layer-element">
            <div class="bg-white q-pa-sm">
                <div class="row justify-between">
                    <div class="text-subtitle1 text-bold">
                        {{ layer['layerName'] }}
                    </div>
                    <div>
                        <q-btn color="grey-4" text-color="black" size="sm" @click="openLayerEditPopup(layer['id']);" icon="fas fa-edit" dense>
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Edit layer
                            </q-tooltip>
                        </q-btn>
                    </div>
                </div>
                <template v-if="layer.hasOwnProperty('layerDescription') && layer['layerDescription']">
                    <div>
                        {{ layer['layerDescription'] }}
                    </div>
                </template>
                <template v-if="(layer.hasOwnProperty('providedBy') && layer['providedBy']) || (layer.hasOwnProperty('sourceURL') && layer['sourceURL'])">
                    <div>
                        <template v-if="layer.hasOwnProperty('providedBy') && layer['providedBy']">
                            <span class="text-bold">Provided by: </span>{{ layer['providedBy'] + ' ' }}
                        </template>
                        <template v-if="layer.hasOwnProperty('sourceURL') && layer['sourceURL']">
                            <a class="text-bold" :href="layer['sourceURL']" target="_blank">(Go to source)</a>
                        </template>
                    </div>
                </template>
                <template v-if="(layer.hasOwnProperty('dateAquired') && layer['dateAquired']) || (layer.hasOwnProperty('dateUploaded') && layer['dateUploaded'])">
                    <div>
                        <template v-if="layer.hasOwnProperty('dateAquired') && layer['dateAquired']">
                            <span class="text-bold">Date aquired: </span>{{ layer['dateAquired'] + ' ' }}
                        </template>
                        <template v-if="layer.hasOwnProperty('dateUploaded') && layer['dateUploaded']">
                            <span class="text-bold">Date uploaded: </span>{{ layer['dateUploaded'] + ' ' }}
                        </template>
                    </div>
                </template>
                <div>
                    <span class="text-bold">File: </span>{{ layer['file'] }}
                </div>
            </div>
        </q-card>
    `,
    setup(props, context) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        const configurationStore = useConfigurationStore();

        function openLayerEditPopup(layerid) {

        }

        return {
            openLayerEditPopup
        }
    }
};
