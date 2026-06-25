const glossaryImageEditorPopup = {
    props: {
        imageId: {
            type: Number,
            default: 0
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="q-mb-md row justify-between">
                                <div>
                                    <template v-if="glossaryImageId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="glossaryImageId > 0">
                                        <q-btn color="secondary" @click="saveGlossaryImageEdits();" label="Save Image Edits" :disabled="!editsExist || !glossaryImageValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addGlossaryImage();" label="Add Image" :disabled="!glossaryImageValid || (selectedUploadMethod === 'upload' && !uploadedFile) || (selectedUploadMethod === 'url' && !urlMethodUrl)" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div v-if="glossaryImageId === 0" class="row">
                                <div class="col-9">
                                    <template v-if="selectedUploadMethod === 'upload'">
                                        <file-picker-input-element label="Image File" :accepted-types="acceptedFileTypes" :value="uploadedFile" :validate-file-size="true" @update:file="(value) => uploadedFile = value[0]"></file-picker-input-element>
                                    </template>
                                    <template v-else>
                                        <text-field-input-element data-type="textarea" label="Enter URL" :value="urlMethodUrl" @update:value="(value) => urlMethodUrl = value"></text-field-input-element>
                                    </template>
                                </div>
                                <div class="col-3 row justify-end">
                                    <q-btn-toggle v-model="selectedUploadMethod" :options="uploadMethodOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" aria-label="Upload method" tabindex="0"></q-btn-toggle>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Created By" maxlength="250" :value="glossaryImageData['createdby']" @update:value="(value) => updateGlossaryImageData('createdby', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Structures" maxlength="150" :value="glossaryImageData['structures']" @update:value="(value) => updateGlossaryImageData('structures', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Notes" maxlength="250" :value="glossaryImageData['notes']" @update:value="(value) => updateGlossaryImageData('notes', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div v-if="Number(glossaryImageId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteGlossaryImage();" label="Delete Image" tabindex="0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'file-picker-input-element': filePickerInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const glossaryStore = useGlossaryStore();

        const acceptedFileTypes = ['jpg','jpeg','png'];
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => glossaryStore.getGlossaryImageEditsExist);
        const glossaryImageData = Vue.computed(() => glossaryStore.getGlossaryImageData);
        const glossaryImageId = Vue.computed(() => glossaryStore.getGlossaryImageID);
        const glossaryImageValid = Vue.computed(() => glossaryStore.getGlossaryImageValid);
        const selectedUploadMethod = Vue.ref('upload');
        const uploadedFile = Vue.ref(null);
        const uploadMethodOptions = [
            {label: 'File', value: 'upload'},
            {label: 'URL', value: 'url'}
        ];
        const urlMethodUrl = Vue.ref(null);
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addGlossaryImage() {
            if(urlMethodUrl.value){
                updateGlossaryImageData('filename', urlMethodUrl.value.split('/').pop())
            }
            glossaryStore.createGlossaryImageRecord(uploadedFile.value, urlMethodUrl.value, (res) => {
                if(Number(res) > 0){
                    showNotification('positive','Image uploaded successfully.');
                    uploadedFile.value = null;
                    urlMethodUrl.value = null;
                }
                else{
                    showNotification('negative', 'There was an error uploading the image');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteGlossaryImage() {
            const confirmText = 'Are you sure you want to delete this image? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    glossaryStore.deleteGlossaryImageRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Image has been deleted.');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error deleting the image.');
                        }
                    });
                }
            }});
        }

        function saveGlossaryImageEdits() {
            showWorking('Saving edits...');
            glossaryStore.updateGlossaryImageRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the image edits.');
                }
                context.emit('close:popup');
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateGlossaryImageData(key, value) {
            glossaryStore.updateGlossaryImageEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            glossaryStore.setCurrentGlossaryImageRecord(props.imageId);
        });

        return {
            acceptedFileTypes,
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            glossaryImageData,
            glossaryImageId,
            glossaryImageValid,
            selectedUploadMethod,
            uploadedFile,
            uploadMethodOptions,
            urlMethodUrl,
            addGlossaryImage,
            closePopup,
            deleteGlossaryImage,
            saveGlossaryImageEdits,
            updateGlossaryImageData
        }
    }
};
