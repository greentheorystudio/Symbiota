const layersConfigurationsTab = {
    template: `
        <div class="fit column q-gutter-md">
            <div class="row justify-between">
                <div></div>
                <div class="row justify-end q-gutter-sm">
                    <div>
                        <q-btn color="primary" @click="setDefaultSymbologySettings();" label="Add Layer" />
                    </div>
                    <div>
                        <q-btn color="primary" @click="openLayerGroupEditPopup();" label="Add Layer Group" />
                    </div>
                    <div>
                        <q-btn color="primary" @click="saveLayersConfigurations();" label="Save Settings" />
                    </div>
                    <div onclick="openTutorialWindow('/tutorial/admin/mappingConfigurationManager/index.php');" title="Open Tutorial Window">
                        <q-icon name="far fa-question-circle" size="20px" class="cursor-pointer" />
                    </div>
                </div>
            </div>
            <template v-if="layerConfigArr.length > 0">
                <draggable v-model="layerConfigArr" v-bind="dragOptions" class="q-gutter-sm items-center" group="configItem" item-key="id" :move="validateDragDrop">
                    <template #item="{ element: configData }">
                        <template v-if="configData['type'] === 'layer'">
                            <layers-configurations-layer-element :id="configData['id']" :layer="configData"></layers-configurations-layer-element>
                        </template>
                        <template v-else-if="configData['type'] === 'layerGroup'">
                            <layers-configurations-layer-group-element :id="configData['id']" :layer-group="configData" :expanded-group-arr="expandedGroupArr" @show:layer-group="expandLayerGroup" @hide:layer-group="hideLayerGroup" @edit:layer-group="openLayerGroupEditPopup"></layers-configurations-layer-group-element>
                        </template>
                    </template>
                </draggable>
            </template>
            <template v-else>
                <div class="q-pa-md row justify-center text-h6 text-bold">
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

        function addLayerGroup(layerGroup) {
            layerConfigArr.value.push(layerGroup);
            showLayerGroupEditorPopup.value = false;
        }

        function deleteLayerGroup(layerGroup) {
            const groupObj = layerConfigArr.value.find(item => item['id'].toString() === layerGroup.id.toString());
            if(groupObj) {
                const index = layerConfigArr.value.indexOf(groupObj);
                layerConfigArr.value.splice(index, 1);
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

        function openLayerGroupEditPopup(layerGroup = null) {
            editLayerGroup.value = layerGroup ? Object.assign({}, layerGroup) : null;
            showLayerGroupEditorPopup.value = true;
        }

        function saveLayersConfigurations(){
            configurationStore.updateConfigurationEditData('SPATIAL_LAYER_CONFIG_JSON', JSON.stringify(layerConfigArr.value));
            configurationStore.updateConfigurationData((res) => {
                if(res === 1){
                    showNotification('positive','Settings saved');
                }
                else{
                    showNotification('negative', 'There was an error saving the settings');
                }
            });
        }

        function setLayersConfigArr() {
            baseStore.getGlobalConfigValue('SPATIAL_LAYER_CONFIG_JSON', (dataStr) => {
                layerConfigArr.value = dataStr ? JSON.parse(dataStr) : [];
            });
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
            }
            showLayerGroupEditorPopup.value = false;
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
            addLayerGroup,
            deleteLayerGroup,
            expandLayerGroup,
            hideLayerGroup,
            openLayerGroupEditPopup,
            saveLayersConfigurations,
            updateLayerGroup,
            validateDragDrop
        }
    }
};
