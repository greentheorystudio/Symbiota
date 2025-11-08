const taxonProfileEditorTaxonMapTab = {
    template: `
        <div ref="contentRef" class="fit row justify-between">
            <div class="col-6 q-pa-md">
                <div class="fit row justify-center">
                    <template v-if="Number(taxonMapData['mid']) > 0">
                        <q-img :src="(taxonMapData['url'].startsWith('/') ? (clientRoot + taxonMapData['url']) : taxonMapData['url'])" :height="imageHeight" fit="scale-down" :alt="(taxonMapData['alttext'] ? taxonMapData['alttext'] : ('Map displaying the range of ' + taxon.sciname))"></q-img>
                    </template>
                    <template v-else>
                        <span class="text-subtitle1 text-bold">A map image has not been uploaded for this taxon</span>
                    </template>
                </div>
            </div>
            <div class="col-6 column q-gutter-sm">
                <q-card flat bordered>
                    <q-card-section class="column q-gutter-sm">
                        <div class="text-subtitle1 text-bold">Upload a taxon map image</div>
                        <div class="row">
                            <div class="col-grow">
                                <file-picker-input-element label="Map Image File" :accepted-types="acceptedFileTypes" :value="uploadedFile" :validate-file-size="true" @update:file="(value) => uploadedFile = value[0]"></file-picker-input-element>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-grow">
                                <text-field-input-element data-type="textarea" label="Alt-Text" :value="newAltText" @update:value="(value) => newAltText = value"></text-field-input-element>
                            </div>
                        </div>
                        <div class="row justify-end">
                            <q-btn color="secondary" @click="processUploadImageFile();" label="Upload" :disabled="!uploadedFile" aria-label="Upload map image" tabindex="0" />
                        </div>
                    </q-card-section>
                </q-card>
                <q-card v-if="Number(taxonMapData['mid']) > 0" flat bordered>
                    <q-card-section class="column q-gutter-sm">
                        <div class="text-subtitle1 text-bold">Edit Map Record</div>
                        <div class="row">
                            <div class="col-grow">
                                <text-field-input-element data-type="textarea" label="Alt-Text" :value="taxonMapData['alttext']" @update:value="(value) => updateMapData('alttext', value)"></text-field-input-element>
                            </div>
                        </div>
                        <div class="row justify-end q-gutter-sm">
                            <q-btn color="secondary" @click="saveMapEdits();" label="Save Edits" :disabled="!editsExist" aria-label="Save edits" tabindex="0" />
                            <q-btn color="negative" @click="deleteMapFile();" label="Delete Map" aria-label="Delete map file" tabindex="0" />
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'file-picker-input-element': filePickerInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const acceptedFileTypes = ['jpg','jpeg','png'];
        const clientRoot = baseStore.getClientRoot;
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const editsExist = Vue.computed(() => taxaStore.getTaxaMapEditsExist);
        const imageHeight = Vue.ref(null);
        const newAltText = Vue.ref(null);
        const taxaMapData = Vue.computed(() => taxaStore.getTaxaMapArr);
        const taxon = Vue.computed(() => taxaStore.getTaxaData);
        const taxonId = Vue.computed(() => taxaStore.getTaxaID);
        const taxonMap = Vue.computed(() => {
            return taxaMapData.value.hasOwnProperty(taxonId.value) ? taxaMapData.value[taxonId.value] : null;
        });
        const taxonMapData = Vue.computed(() => taxaStore.getTaxaMapData);
        const uploadedFile = Vue.ref(null);

        Vue.watch(taxonMap, () => {
            taxaStore.setCurrentTaxaMapRecord(taxonMap.value ? taxonMap.value['mid'] : 0);
        });

        function createMapRecord() {
            const uploadPath = taxon.value['family'] ? taxon.value['family'] : taxon.value['unitname1'];
            taxaStore.setCurrentTaxaMapRecord(0);
            taxaStore.updateTaxaMapEditData('alttext', newAltText.value);
            taxaStore.createTaxaMapRecord(uploadedFile.value, uploadPath, (res) => {
                if(Number(res) > 0){
                    showNotification('positive','Map file uploaded successfully.');
                    newAltText.value = null;
                    uploadedFile.value = null;
                }
                else{
                    showNotification('negative', 'There was an error uploading the map file');
                }
            });
        }

        function deleteMapFile() {
            const confirmText = 'Are you sure you want to delete this map? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                    if(val){
                        taxaStore.deleteTaxaMapRecord((res) => {
                            if(res === 1){
                                showNotification('positive','Map has been deleted.');
                            }
                            else{
                                showNotification('negative', 'There was an error deleting the map.');
                            }
                        });
                    }
                }});
        }

        function processUploadImageFile() {
            if(Number(taxonMapData.value['mid']) > 0){
                taxaStore.deleteTaxaMapRecord(() => {
                    createMapRecord();
                });
            }
            else{
                createMapRecord();
            }
        }

        function saveMapEdits() {
            showWorking('Saving edits...');
            taxaStore.updateTaxaMapRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the map edits.');
                }
            });
        }

        function setContentStyle() {
            imageHeight.value = null;
            if(contentRef.value){
                imageHeight.value = (contentRef.value.clientHeight - 100) + 'px';
            }
        }

        function updateMapData(key, value) {
            taxaStore.updateTaxaMapEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            taxaStore.setCurrentTaxaMapRecord(taxonMap.value ? taxonMap.value['mid'] : 0);
        });

        return {
            acceptedFileTypes,
            clientRoot,
            confirmationPopupRef,
            contentRef,
            editsExist,
            imageHeight,
            newAltText,
            taxon,
            taxonMapData,
            uploadedFile,
            deleteMapFile,
            processUploadImageFile,
            saveMapEdits,
            updateMapData,
        }
    }
};
