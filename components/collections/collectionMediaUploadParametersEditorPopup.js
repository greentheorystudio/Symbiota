const collectionMediaUploadParametersEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="collectionMediaUploadParametersId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="collectionMediaUploadParametersId > 0">
                                        <q-btn color="secondary" @click="saveCollectionMediaUploadParametersEdits();" label="Save Edits" :disabled="!editsExist || !profileDataValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="addCollectionMediaUploadParameters();" label="Add Profile" :disabled="!profileDataValid" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row q-col-gutter-sm">
                                <div class="col-grow">
                                    <text-field-input-element label="Title" :value="profileData.title" @update:value="(value) => updateData('title', value)"></text-field-input-element>
                                </div>
                            </div>
                            <collection-media-upload-parameters-field-module></collection-media-upload-parameters-field-module>
                            <div v-if="Number(collectionMediaUploadParametersId) > 0" class="row justify-end q-gutter-md">
                                <div>
                                    <q-btn color="negative" @click="deleteCollectionMediaUploadParameters();" label="Delete Profile" tabindex="0" />
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
        'collection-media-upload-parameters-field-module': collectionMediaUploadParametersFieldModule,
        'confirmation-popup': confirmationPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const collectionMediaUploadParametersStore = useCollectionMediaUploadParametersStore();
        const collectionStore = useCollectionStore();

        const collectionMediaUploadParametersId = Vue.computed(() => collectionMediaUploadParametersStore.getCollectionMediaUploadParametersID);
        const collId = Vue.computed(() => collectionStore.getCollectionId);
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => collectionMediaUploadParametersStore.getCollectionMediaUploadParametersEditsExist);
        const profileData = Vue.computed(() => collectionMediaUploadParametersStore.getCollectionMediaUploadParametersData);
        const profileDataValid = Vue.computed(() => collectionMediaUploadParametersStore.getCollectionMediaUploadParametersValid);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addCollectionMediaUploadParameters() {
            collectionMediaUploadParametersStore.createCollectionMediaUploadParametersRecord(collId.value, (newProfileId) => {
                if(newProfileId > 0){
                    showNotification('positive','Data upload profile added successfully.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the new data upload profile.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function deleteCollectionMediaUploadParameters() {
            const confirmText = 'Are you sure you want to delete this upload profile? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                    if(val){
                        collectionMediaUploadParametersStore.deleteCollectionMediaUploadParametersRecord(collId.value, (res) => {
                            if(res === 1){
                                showNotification('positive','Upload profile has been deleted.');
                                context.emit('close:popup');
                            }
                            else{
                                showNotification('negative', 'There was an error deleting the upload profile.');
                            }
                        });
                    }
                }});
        }

        function saveCollectionMediaUploadParametersEdits() {
            showWorking('Saving edits...');
            collectionMediaUploadParametersStore.updateCollectionMediaUploadParametersRecord(collId.value, (res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the profile edits.');
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

        function updateData(key, value) {
            collectionMediaUploadParametersStore.updateCollectionMediaUploadParametersEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            collectionMediaUploadParametersId,
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            profileData,
            profileDataValid,
            addCollectionMediaUploadParameters,
            closePopup,
            deleteCollectionMediaUploadParameters,
            saveCollectionMediaUploadParametersEdits,
            updateData
        }
    }
};
