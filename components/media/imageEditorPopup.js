const imageEditorPopup = {
    props: {
        collId: {
            type: Number,
            default: null
        },
        imageId: {
            type: Number,
            default: null
        },
        newImageData: {
            type: Object,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div v-if="Number(imageId) > 0" class="row justify-between">
                                <div>
                                    <template v-if="editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <q-btn color="secondary" @click="saveImageEdits();" label="Save Image Edits" :disabled="!editsExist" />
                                </div>
                            </div>
                            <template v-if="Number(imageData.occid) === 0">
                                <div class="row">
                                    <div class="col-grow">
                                        <single-scientific-common-name-auto-complete :sciname="imageData.sciname" label="Scientific Name" :clearable="false" :limit-to-thesaurus="true" @update:sciname="processScientificNameChange"></single-scientific-common-name-auto-complete>
                                    </div>
                                </div>
                            </template>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6 col-md-5">
                                    <text-field-input-element label="Photographer" :value="imageData.photographer" @update:value="(value) => updateData('photographer', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6 col-md-7">
                                    <text-field-input-element label="Owner" :value="imageData.owner" @update:value="(value) => updateData('owner', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Caption" :value="imageData.caption" @update:value="(value) => updateData('caption', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Locality" :value="imageData.locality" @update:value="(value) => updateData('locality', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Notes" :value="imageData.notes" @update:value="(value) => updateData('notes', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Anatomy" :value="imageData.anatomy" @update:value="(value) => updateData('anatomy', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Reference URL" :value="imageData.referenceurl" @update:value="(value) => updateData('referenceurl', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Copyright" :value="imageData.copyright" @update:value="(value) => updateData('copyright', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Rights" :value="imageData.rights" @update:value="(value) => updateData('rights', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-3">
                                    <text-field-input-element data-type="int" label="Sort Sequence" :value="imageData.sortsequence" min-value="1" :clearable="false" @update:value="(value) => updateData('sortsequence', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <image-tag-selector label="Image Tags" :tag-arr="imageData.tagArr" @update:value="(value) => updateData('tagArr', value)"></image-tag-selector>
                                </div>
                            </div>
                            <template v-if="Number(imageId) > 0">
                                <div class="row">
                                    <div class="col-grow">
                                        <text-field-input-element :disabled="true" data-type="textarea" label="URL" :value="imageData.url"></text-field-input-element>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-grow">
                                        <text-field-input-element :disabled="true" data-type="textarea" label="Thumbnail URL" :value="imageData.thumbnailurl"></text-field-input-element>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-grow">
                                        <text-field-input-element :disabled="true" data-type="textarea" label="Original URL" :value="imageData.originalurl"></text-field-input-element>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-grow">
                                        <text-field-input-element :disabled="true" data-type="textarea" label="Source URL" :value="imageData.sourceurl"></text-field-input-element>
                                    </div>
                                </div>
                                <div class="row justify-between">
                                    <div class="row justify-start q-gutter-sm">
                                        <div>
                                            <q-btn color="primary" @click="setOccurrenceLinkage();" label="Set Occurrence Linkage" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Link, or change linkage, to an occurrence record
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div>
                                            <q-btn color="primary" @click="removeOccurrenceLinkage();" label="Remove Occurrence Linkage" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Remove occurrence linkage so that image only displays on Taxon Profile page
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                    <div class="row justify-end q-gutter-sm">
                                        <div>
                                            <q-btn color="negative" @click="processDeleteImageRecord();" label="Delete Image" />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'image-tag-selector': imageTagSelector,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { showNotification } = useCore();
        const imageStore = useImageStore();

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => imageStore.getImageEditsExist);
        const imageData = Vue.computed(() => {
            if(Number(props.imageId) > 0){
                return imageStore.getImageData;
            }
            else{
                return props.newImageData['uploadMetadata'];
            }
        });

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processDeleteImageRecord() {
            const confirmText = 'Are you sure you want to delete this image? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    imageStore.deleteImageRecord(props.collId, (res) => {
                        if(res === 0){
                            showNotification('negative', ('An error occurred while deleting this image.'));
                        }
                        else{
                            showNotification('positive','Image deleted');
                            context.emit('image:updated');
                            context.emit('close:popup');
                        }
                    });
                }
            }});
        }

        function processScientificNameChange(taxon) {
            updateData('sciname', taxon.sciname);
            updateData('tid', taxon.tid);
        }

        function removeOccurrenceLinkage() {
            imageStore.resetOccurrenceLinkage(props.collId, null, (res) => {
                if(res === 1){
                    showNotification('positive','Occurrence linkage removed');
                    context.emit('image:updated');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error removing the occurrence linkage.');
                }
            });
        }

        function saveImageEdits() {
            imageStore.updateImageRecord(props.collId, (res) => {
                if(res === 1){
                    showNotification('positive','Edits saved.');
                    context.emit('image:updated');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error saving the image edits.');
                }
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateData(key, value) {
            if(Number(props.imageId) > 0){
                imageStore.updateImageEditData(key, value);
            }
            else{
                context.emit('update:image-data', {key: key, value: value});
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            if(Number(props.imageId) > 0){
                imageStore.setCurrentImageRecord(props.imageId);
            }
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            imageData,
            closePopup,
            processDeleteImageRecord,
            processScientificNameChange,
            removeOccurrenceLinkage,
            saveImageEdits,
            updateData
        }
    }
};
