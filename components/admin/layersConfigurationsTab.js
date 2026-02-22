const layersConfigurationsTab = {
    template: `
        <div class="fit column q-gutter-md">
            <div class="row justify-between">
                <div></div>
                <div class="row justify-end q-gutter-sm">
                    <div>
                        <q-btn color="primary" @click="openLayerEditPopup();" label="Add Layer" tabindex="0" />
                    </div>
                    <div>
                        <q-btn color="primary" @click="openLayerGroupEditPopup();" label="Add Layer Group" tabindex="0" />
                    </div>
                    <div role="button" class="cursor-pointer" @click="showTutorial();" @keyup.enter="showTutorial();" aria-label="Open Tutorial" tabindex="0">
                        <q-icon name="far fa-question-circle" size="20px" />
                    </div>
                </div>
            </div>
            <template v-if="layerConfigArr.length > 0">
                <draggable v-model="layerConfigArr" v-bind="dragOptions" class="q-gutter-sm items-center" group="configItem" item-key="id" :move="validateDragDrop" @add="processDragDrop" @update="processDragDrop">
                    <template #item="{ element: configData }">
                        <template v-if="configData['type'] === 'layer'">
                            <layers-configurations-layer-element :id="configData['id']" :layer="configData" @edit:layer="openLayerEditPopup"></layers-configurations-layer-element>
                        </template>
                        <template v-else-if="configData['type'] === 'layerGroup'">
                            <layers-configurations-layer-group-element :id="configData['id']" :layer-group="configData" :expanded-group-arr="expandedGroupArr" @show:layer-group="expandLayerGroup" @hide:layer-group="hideLayerGroup" @edit:layer-group="openLayerGroupEditPopup" @edit:layer="openLayerEditPopup" @update:layers-arr="processDragDrop"></layers-configurations-layer-group-element>
                        </template>
                    </template>
                </draggable>
            </template>
            <template v-else>
                <div class="q-pa-md row justify-center text-subtitle1 text-bold">
                    There is currently no layer data to display
                </div>
            </template>
        </div>
        <template v-if="showLayerGroupEditorPopup">
            <layer-configurations-layer-group-editor-popup
                :layer-group="editLayerGroup"
                :show-popup="showLayerGroupEditorPopup"
                @add:layer-group="addLayerGroup"
                @delete:layer-group="deleteLayerGroup"
                @update:layer-group="updateLayerGroup"
                @close:popup="showLayerGroupEditorPopup = false"
            ></layer-configurations-layer-group-editor-popup>
        </template>
        <template v-if="showLayerEditorPopup">
            <layer-configurations-layer-editor-popup
                :layer="editLayer"
                :show-popup="showLayerEditorPopup"
                @add:layer="addLayer"
                @delete:layer="deleteLayer"
                @update:layer="updateLayer"
                @close:popup="showLayerEditorPopup = false"
            ></layer-configurations-layer-editor-popup>
        </template>
    `,
    components: {
        'draggable': draggable,
        'layer-configurations-layer-editor-popup': layerConfigurationsLayerEditorPopup,
        'layer-configurations-layer-group-editor-popup': layerConfigurationsLayerGroupEditorPopup,
        'layers-configurations-layer-element': layersConfigurationsLayerElement,
        'layers-configurations-layer-group-element': layersConfigurationsLayerGroupElement
    },
    setup() {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        const configurationStore = useConfigurationStore();

        const configurationData = Vue.computed(() => configurationStore.getMapSymbologyData);
        const blankLayer = {
            id: 0,
            type: 'layer',
            layerName: null,
            layerDescription: null,
            providedBy: null,
            sourceURL: null,
            dateAquired: null,
            dateUploaded: null,
            colorScale: configurationData.value['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'],
            borderColor: configurationData.value['SPATIAL_DRAGDROP_BORDER_COLOR'],
            fillColor: configurationData.value['SPATIAL_DRAGDROP_FILL_COLOR'],
            borderWidth: configurationData.value['SPATIAL_DRAGDROP_BORDER_WIDTH'],
            pointRadius: configurationData.value['SPATIAL_DRAGDROP_POINT_RADIUS'],
            opacity: configurationData.value['SPATIAL_DRAGDROP_OPACITY'],
            file: null
        };
        const blankLayerGroup = {
            id: 0,
            type: 'layerGroup',
            name: null,
            layers: []
        };
        const dragOptions = Vue.computed(() => {
            return {
                animation: 200,
                ghostClass: "ghost"
            };
        });
        const editLayer = Vue.ref(null);
        const editLayerGroup = Vue.ref(null);
        const expandedGroupArr = Vue.ref([]);
        const layerConfigArr = Vue.ref([]);
        const showLayerEditorPopup = Vue.ref(false);
        const showLayerGroupEditorPopup = Vue.ref(false);

        const showTutorial = Vue.inject('showTutorial');

        function addLayer(layer) {
            layerConfigArr.value.push(layer);
            showLayerEditorPopup.value = false;
            saveLayersConfigurations();
        }

        function addLayerGroup(layerGroup) {
            layerConfigArr.value.push(layerGroup);
            showLayerGroupEditorPopup.value = false;
            saveLayersConfigurations();
        }

        function deleteLayer(layer) {
            let layerObj = layerConfigArr.value.find(item => item['id'].toString() === layer.id.toString());
            if(layerObj) {
                const index = layerConfigArr.value.indexOf(layerObj);
                layerConfigArr.value.splice(index, 1);
                saveLayersConfigurations();
            }
            else{
                let found = false;
                do{
                    layerConfigArr.value.forEach((item) => {
                        if(item.type === 'layerGroup' && item.layers.length > 0) {
                            layerObj = item.layers.find(item => item['id'].toString() === layer.id.toString());
                            if(layerObj) {
                                const index = item.layers.indexOf(layerObj);
                                item.layers.splice(index, 1);
                                updateLayerConfigArr();
                                saveLayersConfigurations();
                                found = true;
                            }
                        }
                    });
                }
                while(!found);
            }
            showLayerEditorPopup.value = false;
        }

        function deleteLayerGroup(layerGroup) {
            const groupObj = layerConfigArr.value.find(item => item['id'].toString() === layerGroup.id.toString());
            if(groupObj) {
                const index = layerConfigArr.value.indexOf(groupObj);
                layerConfigArr.value.splice(index, 1);
                saveLayersConfigurations();
            }
            showLayerGroupEditorPopup.value = false;
        }

        function expandLayerGroup(id) {
            expandedGroupArr.value.push(id.toString());
        }

        function hideLayerGroup(id) {
            const index = expandedGroupArr.value.indexOf(id.toString());
            expandedGroupArr.value.splice(index, 1);
        }

        function openLayerEditPopup(layer = null) {
            editLayer.value = layer ? Object.assign({}, layer) : Object.assign({}, blankLayer);
            showLayerEditorPopup.value = true;
        }

        function openLayerGroupEditPopup(layerGroup = null) {
            editLayerGroup.value = layerGroup ? Object.assign({}, layerGroup) : Object.assign({}, blankLayerGroup);
            showLayerGroupEditorPopup.value = true;
        }

        function processDragDrop() {
            saveLayersConfigurations();
        }

        function saveLayersConfigurations(){
            configurationStore.updateConfigurationEditData('SPATIAL_LAYER_CONFIG_JSON', JSON.stringify(layerConfigArr.value));
            configurationStore.updateConfigurationData((res) => {
                if(res === 0){
                    showNotification('negative', 'There was an error saving the settings');
                }
            });
        }

        function setLayersConfigArr() {
            baseStore.getGlobalConfigValue('SPATIAL_LAYER_CONFIG_JSON', (dataStr) => {
                layerConfigArr.value = dataStr ? JSON.parse(dataStr) : [];
            });
        }

        function updateLayer(layer) {
            let layerObj = layerConfigArr.value.find(item => item['id'].toString() === layer.id.toString());
            if(layerObj) {
                updateLayerValues(layerObj, layer);
                updateLayerConfigArr();
                saveLayersConfigurations();
            }
            else{
                let found = false;
                do{
                    layerConfigArr.value.forEach((item) => {
                        if(item.type === 'layerGroup' && item.layers.length > 0) {
                            layerObj = item.layers.find(item => item['id'].toString() === layer.id.toString());
                            if(layerObj) {
                                updateLayerValues(layerObj, layer);
                                updateLayerConfigArr();
                                saveLayersConfigurations();
                                found = true;
                            }
                        }
                    });
                }
                while(!found);
            }
            showLayerEditorPopup.value = false;
        }

        function updateLayerConfigArr() {
            const data = layerConfigArr.value.slice();
            layerConfigArr.value = data.slice();
        }

        function updateLayerGroup(layerGroup) {
            const groupObj = layerConfigArr.value.find(item => item['id'].toString() === layerGroup.id.toString());
            if(groupObj) {
                groupObj['name'] = layerGroup['name'];
                updateLayerConfigArr();
                saveLayersConfigurations();
            }
            showLayerGroupEditorPopup.value = false;
        }

        function updateLayerValues(layer, newValues) {
            Object.keys(layer).forEach((key) => {
                if(key !== 'id' && key !== 'type' && key !== 'file') {
                    layer[key] = newValues[key];
                }
            });
        }

        function validateDragDrop(evt){
            const draggedObj = evt.dragged.id ? layerConfigArr.value.find(item => item['id'].toString() === evt.dragged.id.toString()) : null;
            return !(!draggedObj || (draggedObj['type'] === 'layerGroup' && evt.to.id.startsWith('group-')));
        }

        Vue.onMounted(() => {
            setLayersConfigArr();
        });

        return {
            dragOptions,
            editLayer,
            editLayerGroup,
            expandedGroupArr,
            layerConfigArr,
            showLayerEditorPopup,
            showLayerGroupEditorPopup,
            addLayer,
            addLayerGroup,
            deleteLayer,
            deleteLayerGroup,
            expandLayerGroup,
            hideLayerGroup,
            openLayerEditPopup,
            openLayerGroupEditPopup,
            processDragDrop,
            showTutorial,
            updateLayer,
            updateLayerGroup,
            validateDragDrop
        }
    }
};
