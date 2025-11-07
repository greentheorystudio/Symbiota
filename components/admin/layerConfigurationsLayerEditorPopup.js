const layerConfigurationsLayerEditorPopup = {
    props: {
        layer: {
            type: Object,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog v-if="layer" class="z-max" v-model="showPopup" v-if="!showConfirmation" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="Number(layer.id) > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end q-gutter-sm">
                                    <template v-if="Number(layer.id) > 0">
                                        <q-btn color="secondary" @click="updateLayer();" label="Save Edits" :disabled="!editsExist || !editDataValid" tabindex="0" />
                                        <q-btn color="negative" @click="deleteLayer();" label="Remove" aria-label="Remove layer" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addLayer();" label="Add Layer" :disabled="!editDataValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <q-card flat bordered>
                                <q-card-section>
                                    <div class="column q-gutter-sm">
                                        <template v-if="Number(layer.id) === 0">
                                            <div class="row">
                                                <div class="col-grow">
                                                    <file-picker-input-element label="File" :accepted-types="acceptedFileTypes" :value="uploadedFile" :validate-file-size="true" @update:file="(value) => processFileSelection(value)"></file-picker-input-element>
                                                </div>
                                            </div>
                                        </template>
                                        <div class="row">
                                            <div class="col-grow">
                                                <text-field-input-element label="Layer Name" :value="editData['layerName']" @update:value="(value) => editData['layerName'] = value"></text-field-input-element>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <text-field-input-element data-type="textarea" label="Description" :value="editData['layerDescription']" @update:value="(value) => editData['layerDescription'] = value"></text-field-input-element>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <text-field-input-element label="Provided By" :value="editData['providedBy']" @update:value="(value) => editData['providedBy'] = value"></text-field-input-element>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <text-field-input-element data-type="textarea" label="Source URL" :value="editData['sourceURL']" @update:value="(value) => editData['sourceURL'] = value"></text-field-input-element>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-grow">
                                                <date-input-element label="Date Aquired" :value="editData['dateAquired']" @update:value="(value) => editData['dateAquired'] = (value ? value['date'] : null)"></date-input-element>
                                            </div>
                                        </div>
                                        <template v-if="Number(layer.id) > 0">
                                            <div class="row">
                                                <div class="col-grow">
                                                    <date-input-element :disabled="true" label="Date Uploaded" :value="editData['dateUploaded']"></date-input-element>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-grow">
                                                    <text-field-input-element :disabled="true" label="File" :value="editData['file']"></text-field-input-element>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </q-card-section>
                            </q-card>
                            <template v-if="uploadedFile || editData['file']">
                                <q-card flat bordered>
                                    <q-card-section>
                                        <div class="text-subtitle1 text-bold">Initial Symbology</div>
                                        <div class="q-mt-xs q-pl-sm column">
                                            <template v-if="mapDataType === 'vector'">
                                                <div class="q-mb-sm row justify-start q-col-gutter-md">
                                                    <div>
                                                        <div class="row justify-start self-center">
                                                            <div class="text-bold">
                                                                Border color
                                                            </div>
                                                            <div class="q-ml-sm">
                                                                <color-picker :color-value="editData['borderColor']" @update:color-picker="(value) => editData['borderColor'] = value"></color-picker>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="row justify-start self-center">
                                                            <div class="text-bold">
                                                                Fill color
                                                            </div>
                                                            <div class="q-ml-sm">
                                                                <color-picker :color-value="editData['fillColor']" @update:color-picker="(value) => editData['fillColor'] = value"></color-picker>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row justify-between q-col-gutter-sm">
                                                    <div class="col-12 col-sm-6 col-md-4">
                                                        <text-field-input-element :clearable="false" data-type="int" label="Border width (px)" :value="editData['borderWidth']" min-value="0" @update:value="(value) => editData['borderWidth'] = value"></text-field-input-element>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-4">
                                                        <text-field-input-element :clearable="false" data-type="int" label="Point radius (px)" :value="editData['pointRadius']" min-value="1" @update:value="(value) => editData['pointRadius'] = value"></text-field-input-element>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-md-4">
                                                        <text-field-input-element :clearable="false" data-type="increment" label="Fill Opacity" :value="editData['opacity']" min-value="0" max-value="1" step=".1" @update:value="(value) => editData['opacity'] = value"></text-field-input-element>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="row justify-start">
                                                    <div class="col-12 col-sm-6 col-md-4">
                                                        <spatial-raster-color-scale-select :selected-color-scale="editData['colorScale']" @raster-color-scale-change="(value) => editData['colorScale'] = value"></spatial-raster-color-scale-select>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </template>
                            <template v-if="Number(layer.id) > 0">
                                <q-card flat bordered>
                                    <q-card-section>
                                        <div class="text-subtitle1 text-bold">Update Data File</div>
                                        <div class="q-mt-xs row justify-between">
                                            <div class="col-9">
                                                <file-picker-input-element label="Update File" :accepted-types="acceptedFileTypes" :value="uploadedFile" :validate-file-size="true" @update:file="(value) => processFileSelection(value)"></file-picker-input-element>
                                            </div>
                                            <div class="col-3 row justify-end">
                                                <q-btn color="secondary" @click="updateDataFile();" label="Update" :disabled="!uploadedFile" aria-label="Update data file" tabindex="0" />
                                            </div>
                                        </div>
                                    </q-card-section>
                                </q-card>
                            </template>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'color-picker': colorPicker,
        'confirmation-popup': confirmationPopup,
        'date-input-element': dateInputElement,
        'file-picker-input-element': filePickerInputElement,
        'spatial-raster-color-scale-select': spatialRasterColorScaleSelect,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { getCurrentDateStr, hideWorking, showNotification, showWorking } = useCore();
        const configurationStore = useConfigurationStore();

        const acceptedFileTypes = ['dbf','geojson','json','kml','shp','tif','tiff','zip'];
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editData = Vue.ref(null);
        const editDataValid = Vue.computed(() => {
            return editData.value.layerName && (uploadedFile.value || editData.value.file);
        });
        const editsExist = Vue.computed(() => {
            let exist = false;
            Object.keys(props.layer).forEach((key) => {
                if(props.layer[key] !== editData.value[key]){
                    exist = true;
                }
            });
            return exist;
        });
        const mapDataType = Vue.computed(() => {
            let returnVal = '';
            if(editData.value.file){
                returnVal = (editData.value.file.endsWith('.tif') || editData.value.file.endsWith('.tiff')) ? 'raster' : 'vector';
            }
            else if(uploadedFile.value){
                returnVal = (uploadedFile.value.name.endsWith('.tif') || uploadedFile.value.name.endsWith('.tiff')) ? 'raster' : 'vector';
            }
            return returnVal;
        });
        const showConfirmation = Vue.ref(false);
        const uploadedFile = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addLayer() {
            showWorking();
            configurationStore.uploadMapDataFile(uploadedFile.value, (res) => {
                hideWorking();
                if(res && res.toString() !== ''){
                    editData.value['id'] = Date.now();
                    editData.value['file'] = res.toString();
                    editData.value['dateUploaded'] = getCurrentDateStr();
                    context.emit('add:layer', editData.value);
                }
                else{
                    showNotification('negative', 'An error occurred while uploading the map file');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteLayer() {
            showConfirmation.value = true;
            const confirmText = 'Are you sure you want to delete this map layer? This action cannot be undone';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                showConfirmation.value = false;
                if(val){
                    showWorking();
                    configurationStore.deleteMapDataFile(editData.value['file'], (res) => {
                        hideWorking();
                        if(Number(res) === 0){
                            showNotification('negative', 'An error occurred while deleting the map file from the server');
                        }
                        context.emit('delete:layer', editData.value);
                    });
                }
            }});
        }

        function processFileSelection(file) {
            uploadedFile.value = null;
            if(file){
                if(file[0].name.endsWith('.shp') || file[0].name.endsWith('.dbf')){
                    showNotification('negative', 'In order to upload a shapefile, the entire shapefile zip file must be uploaded.');
                }
                else{
                    uploadedFile.value = file[0];
                }
            }
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateDataFile() {
            showConfirmation.value = true;
            const confirmText = 'Are you sure you want to update this layer data file? This action cannot be undone';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                showWorking();
                configurationStore.deleteMapDataFile(editData.value['file'], (res) => {
                    if(Number(res) === 1){
                        configurationStore.uploadMapDataFile(uploadedFile.value, (res) => {
                            hideWorking();
                            if(res && res.toString() !== ''){
                                editData.value['file'] = res.toString();
                                editData.value['dateUploaded'] = getCurrentDateStr();
                                context.emit('update:layer', editData.value);
                            }
                            else{
                                showNotification('negative', 'An error occurred while uploading the update map file');
                            }
                        });
                    }
                    else{
                        hideWorking();
                        showNotification('negative', 'An error occurred while deleting the previous map file from the server');
                    }
                });
            }});
        }

        function updateLayer() {
            context.emit('update:layer', editData.value);
        }

        Vue.onMounted(() => {
            editData.value = Object.assign({}, props.layer);
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            acceptedFileTypes,
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editData,
            editDataValid,
            editsExist,
            mapDataType,
            showConfirmation,
            uploadedFile,
            closePopup,
            addLayer,
            deleteLayer,
            processFileSelection,
            updateDataFile,
            updateLayer
        }
    }
};
