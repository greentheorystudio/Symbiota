const layersConfigurationsLayerGroupElement = {
    props: {
        expandedGroupArr: {
            type: Array,
            default: []
        },
        layerGroup: {
            type: Object,
            default: null
        }
    },
    template: `
        <q-card flat bordered class="full-width q-pa-sm map-configurations-layer-element">
            <div class="q-pt-xs row justify-between self-center" :class="(layerGroup.hasOwnProperty('layers') && (layerGroup['layers'].length === 0 || (layerGroup['layers'].length > 0 && expandedGroupArr.includes(layerGroup['id'].toString())))) ? 'q-pb-lg' : 'q-pb-xs'">
                <div class="text-bold row justify-start q-gutter-md">
                    <div>
                        {{ layerGroup['name'] }}
                    </div>
                    <template v-if="layerGroup.hasOwnProperty('layers') && layerGroup['layers'].length > 0">
                        <template v-if="expandedGroupArr.includes(layerGroup['id'].toString())">
                            <q-icon role="button" name="arrow_drop_up" class="cursor-pointer" size="sm" @click="hideLayerGroup(layerGroup['id']);" @keyup.enter="hideLayerGroup(layerGroup['id']);" aria-label="Hide layers" tabindex="0">
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Hide layers
                                </q-tooltip>
                            </q-icon>
                        </template>
                        <template v-else>
                            <q-icon role="button" name="arrow_drop_down" class="cursor-pointer text-bold" size="sm" @click="showLayerGroup(layerGroup['id']);" @keyup.enter="showLayerGroup(layerGroup['id']);" aria-label="Show layers" tabindex="0">
                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                    Show layers
                                </q-tooltip>
                            </q-icon>
                        </template>
                    </template>
                </div>
                <div>
                    <q-btn color="grey-4" text-color="black" size="sm" @click="openLayerGroupEditPopup(layerGroup);" icon="fas fa-edit" dense aria-label="Edit layer group" tabindex="0">
                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                            Edit layer group
                        </q-tooltip>
                    </q-btn>
                </div>
            </div>
            <template v-if="layerGroup.hasOwnProperty('layers') && (layerGroup['layers'].length === 0 || (layerGroup['layers'].length > 0 && expandedGroupArr.includes(layerGroup['id'].toString())))">
                <draggable v-model="layerGroup['layers']" :id="('group-' + layerGroup['id'])" v-bind="dragOptions" class="q-pa-sm bg-white q-gutter-y-sm" group="configItem" item-key="id" @add="processDragDrop" @update="processDragDrop">
                    <template #item="{ element: layer }">
                        <layers-configurations-layer-element :layer="layer" @edit:layer="openLayerEditPopup"></layers-configurations-layer-element>
                    </template>
                </draggable>
            </template>
        </q-card>
    `,
    components: {
        'draggable': draggable,
        'layers-configurations-layer-element': layersConfigurationsLayerElement
    },
    setup(props, context) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        const configurationStore = useConfigurationStore();

        const dragOptions = Vue.computed(() => {
            return {
                animation: 200,
                ghostClass: "ghost"
            };
        });

        function hideLayerGroup(layergroupid) {
            context.emit('hide:layer-group', layergroupid);
        }

        function openLayerEditPopup(layer) {
            context.emit('edit:layer', layer);
        }

        function openLayerGroupEditPopup(layerGroup) {
            context.emit('edit:layer-group', layerGroup);
        }

        function processDragDrop() {
            context.emit('update:layers-arr');
        }

        function showLayerGroup(layergroupid) {
            context.emit('show:layer-group', layergroupid);
        }

        return {
            dragOptions,
            hideLayerGroup,
            openLayerEditPopup,
            openLayerGroupEditPopup,
            processDragDrop,
            showLayerGroup
        }
    }
};
