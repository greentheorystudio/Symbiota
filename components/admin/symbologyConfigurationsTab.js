const symbologyConfigurationsTab = {
    template: `
        <div class="fit column q-gutter-md">
            <div class="row justify-between">
                <div>
                    <template v-if="editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end q-gutter-sm">
                    <div>
                        <q-btn color="secondary" @click="setDefaultSymbologySettings();" label="Set Default Settings" tabindex="0" />
                    </div>
                    <div>
                        <q-btn color="primary" @click="saveConfigurationEdits();" label="Save Settings" :disabled="!editsExist" tabindex="0" />
                    </div>
                    <div onclick="openTutorialWindow('/tutorial/admin/mappingConfigurationManager/index.php');" title="Open Tutorial Window">
                        <q-icon name="far fa-question-circle" size="20px" class="cursor-pointer" />
                    </div>
                </div>
            </div>
            <q-card flat bordered>
                <q-card-section>
                    <div class="text-h6 text-bold">Points Layer</div>
                    <div class="q-mt-xs q-pl-sm column">
                        <div class="q-mb-sm row justify-start q-col-gutter-md">
                            <div>
                                <checkbox-input-element label="Cluster Points" :value="configurationData['SPATIAL_POINT_CLUSTER']" @update:value="(value) => updateConfigurationData('SPATIAL_POINT_CLUSTER', (value ? '1' : '0'))"></checkbox-input-element>
                            </div>
                            <div>
                                <checkbox-input-element label="Display Heat Map" :value="configurationData['SPATIAL_POINT_DISPLAY_HEAT_MAP']" @update:value="(value) => updateConfigurationData('SPATIAL_POINT_DISPLAY_HEAT_MAP', (value ? '1' : '0'))"></checkbox-input-element>
                            </div>
                        </div>
                        <div class="q-mb-sm row justify-start q-col-gutter-md">
                            <div>
                                <div class="row justify-start">
                                    <div class="text-bold">
                                        Border color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_POINT_BORDER_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_POINT_BORDER_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row justify-start">
                                    <div class="text-bold">
                                        Fill color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_POINT_FILL_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_POINT_FILL_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row justify-start">
                                    <div class="text-bold">
                                        Selections Border color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_POINT_SELECTIONS_BORDER_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_POINT_SELECTIONS_BORDER_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="q-mb-sm row justify-between q-col-gutter-sm">
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Cluster Distance (px)" :value="configurationData['SPATIAL_POINT_CLUSTER_DISTANCE']" min-value="1" @update:value="(value) => updateConfigurationData('SPATIAL_POINT_CLUSTER_DISTANCE', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Heat Map Radius (px)" :value="configurationData['SPATIAL_POINT_HEAT_MAP_RADIUS']" min-value="1" @update:value="(value) => updateConfigurationData('SPATIAL_POINT_HEAT_MAP_RADIUS', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Heat Map Blur (px)" :value="configurationData['SPATIAL_POINT_HEAT_MAP_BLUR']" min-value="0" @update:value="(value) => updateConfigurationData('SPATIAL_POINT_HEAT_MAP_BLUR', value)"></text-field-input-element>
                            </div>
                        </div>
                        <div class="row justify-between q-col-gutter-sm">
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Border width (px)" :value="configurationData['SPATIAL_POINT_BORDER_WIDTH']" min-value="0" @update:value="(value) => updateConfigurationData('SPATIAL_POINT_BORDER_WIDTH', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Point radius (px)" :value="configurationData['SPATIAL_POINT_POINT_RADIUS']" min-value="1" @update:value="(value) => updateConfigurationData('SPATIAL_POINT_POINT_RADIUS', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Selections Border width (px)" :value="configurationData['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH']" min-value="0" @update:value="(value) => updateConfigurationData('SPATIAL_POINT_SELECTIONS_BORDER_WIDTH', value)"></text-field-input-element>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section>
                    <div class="text-h6 text-bold">Shapes Layer</div>
                    <div class="q-mt-xs q-pl-sm column">
                        <div class="q-mb-sm row justify-start q-col-gutter-md">
                            <div>
                                <div class="row justify-start self-center">
                                    <div class="text-bold">
                                        Border color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_SHAPES_BORDER_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_SHAPES_BORDER_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row justify-start self-center">
                                    <div class="text-bold">
                                        Fill color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_SHAPES_FILL_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_SHAPES_FILL_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row justify-start self-center">
                                    <div class="text-bold">
                                        Selections Border color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row justify-start self-center">
                                    <div class="text-bold">
                                        Selections Fill color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_SHAPES_SELECTIONS_FILL_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="q-mb-sm row justify-between q-col-gutter-sm">
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Border width (px)" :value="configurationData['SPATIAL_SHAPES_BORDER_WIDTH']" min-value="0" @update:value="(value) => updateConfigurationData('SPATIAL_SHAPES_BORDER_WIDTH', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Point radius (px)" :value="configurationData['SPATIAL_SHAPES_POINT_RADIUS']" min-value="1" @update:value="(value) => updateConfigurationData('SPATIAL_SHAPES_POINT_RADIUS', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="increment" label="Fill Opacity" :value="configurationData['SPATIAL_SHAPES_OPACITY']" min-value="0" max-value="1" step=".1" @update:value="(value) => updateConfigurationData('SPATIAL_SHAPES_OPACITY', value)"></text-field-input-element>
                            </div>
                        </div>
                        <div class="row justify-start q-col-gutter-sm">
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="int" label="Selections Border width (px)" :value="configurationData['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH']" min-value="0" @update:value="(value) => updateConfigurationData('SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <text-field-input-element :clearable="false" data-type="increment" label="Selections Opacity" :value="configurationData['SPATIAL_SHAPES_SELECTIONS_OPACITY']" min-value="0" max-value="1" step=".1" @update:value="(value) => updateConfigurationData('SPATIAL_SHAPES_SELECTIONS_OPACITY', value)"></text-field-input-element>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
            <q-card flat bordered>
                <q-card-section>
                    <div class="text-h6 text-bold">Drag and Drop Layers</div>
                    <div class="q-mt-xs q-pl-sm column">
                        <div class="q-mb-sm row justify-start q-col-gutter-md">
                            <div>
                                <div class="row justify-start self-center">
                                    <div class="text-bold">
                                        Border color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_DRAGDROP_BORDER_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_DRAGDROP_BORDER_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row justify-start self-center">
                                    <div class="text-bold">
                                        Fill color
                                    </div>
                                    <div class="q-ml-sm">
                                        <color-picker :color-value="configurationData['SPATIAL_DRAGDROP_FILL_COLOR']" @update:color-picker="(value) => updateConfigurationData('SPATIAL_DRAGDROP_FILL_COLOR', value)"></color-picker>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-between q-col-gutter-sm">
                            <div class="col-12 col-sm-6 col-md-3">
                                <spatial-raster-color-scale-select :selected-color-scale="configurationData['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE']" @raster-color-scale-change="(value) => updateConfigurationData('SPATIAL_DRAGDROP_RASTER_COLOR_SCALE', value)"></spatial-raster-color-scale-select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <text-field-input-element :clearable="false" data-type="int" label="Border width (px)" :value="configurationData['SPATIAL_DRAGDROP_BORDER_WIDTH']" min-value="0" @update:value="(value) => updateConfigurationData('SPATIAL_DRAGDROP_BORDER_WIDTH', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <text-field-input-element :clearable="false" data-type="int" label="Point radius (px)" :value="configurationData['SPATIAL_DRAGDROP_POINT_RADIUS']" min-value="1" @update:value="(value) => updateConfigurationData('SPATIAL_DRAGDROP_POINT_RADIUS', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <text-field-input-element :clearable="false" data-type="increment" label="Fill Opacity" :value="configurationData['SPATIAL_DRAGDROP_OPACITY']" min-value="0" max-value="1" step=".1" @update:value="(value) => updateConfigurationData('SPATIAL_DRAGDROP_OPACITY', value)"></text-field-input-element>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'color-picker': colorPicker,
        'spatial-raster-color-scale-select': spatialRasterColorScaleSelect,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const configurationStore = useConfigurationStore();

        const configurationData = Vue.computed(() => configurationStore.getMapSymbologyData);
        const editsExist = Vue.computed(() => configurationStore.getConfigurationEditsExist);

        function saveConfigurationEdits() {
            configurationStore.updateConfigurationData((res) => {
                if(res === 1){
                    showNotification('positive','Settings saved');
                }
                else{
                    showNotification('negative', 'There was an error saving the settings');
                }
            });
        }

        function setDefaultSymbologySettings() {
            configurationStore.setDefaultSymbologyData((res) => {
                if(res === 1){
                    showNotification('positive','Settings saved');
                }
                else{
                    showNotification('negative', 'There was an error saving the settings');
                }
            });
        }

        function updateConfigurationData(key, value) {
            configurationStore.updateConfigurationEditData(key, value);
        }

        return {
            configurationData,
            editsExist,
            saveConfigurationEdits,
            setDefaultSymbologySettings,
            updateConfigurationData
        }
    }
};
