const mediaEditorPopup = {
    props: {
        collId: {
            type: Number,
            default: null
        },
        mediaId: {
            type: Number,
            default: null
        },
        newMediaData: {
            type: Object,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" v-if="!showOccurrenceLinkageToolPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div v-if="Number(mediaId) > 0" class="row justify-between">
                                <div>
                                    <template v-if="editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <q-btn color="secondary" @click="saveMediaEdits();" label="Save Media Edits" :disabled="!editsExist" />
                                </div>
                            </div>
                            <template v-if="Number(mediaData.occid) === 0">
                                <div class="row">
                                    <div class="col-grow">
                                        <single-scientific-common-name-auto-complete :sciname="mediaData.sciname" label="Scientific Name" :clearable="false" :limit-to-thesaurus="true" @update:sciname="processScientificNameChange"></single-scientific-common-name-auto-complete>
                                    </div>
                                </div>
                            </template>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element label="Creator" :value="mediaData.creator" @update:value="(value) => updateData('creator', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element label="Owner" :value="mediaData.owner" @update:value="(value) => updateData('owner', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6 col-md-8">
                                    <text-field-input-element label="Title" :value="mediaData.title" @update:value="(value) => updateData('title', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <text-field-input-element label="Language" :value="mediaData.language" @update:value="(value) => updateData('language', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element label="Publisher" :value="mediaData.publisher" @update:value="(value) => updateData('publisher', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <text-field-input-element label="Contributor" :value="mediaData.contributor" @update:value="(value) => updateData('contributor', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Description" :value="mediaData.description" @update:value="(value) => updateData('description', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Location Created" :value="mediaData.locationcreated" @update:value="(value) => updateData('locationcreated', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Bibliographic Citation" :value="mediaData.bibliographiccitation" @update:value="(value) => updateData('bibliographiccitation', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Further Information URL" :value="mediaData.furtherinformationurl" @update:value="(value) => updateData('furtherinformationurl', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Usage Terms" :value="mediaData.usageterms" @update:value="(value) => updateData('usageterms', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Rights" :value="mediaData.rights" @update:value="(value) => updateData('rights', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-start">
                                <div class="col-12 col-sm-3">
                                    <text-field-input-element data-type="int" label="Sort Sequence" :value="mediaData.sortsequence" min-value="1" :clearable="false" @update:value="(value) => updateData('sortsequence', value)"></text-field-input-element>
                                </div>
                            </div>
                            <template v-if="Number(mediaId) > 0">
                                <div class="row">
                                    <div class="col-grow">
                                        <text-field-input-element :disabled="true" data-type="textarea" label="URL" :value="mediaData.accessuri"></text-field-input-element>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-grow">
                                        <text-field-input-element :disabled="true" data-type="textarea" label="Source URL" :value="mediaData.sourceurl"></text-field-input-element>
                                    </div>
                                </div>
                                <div class="row justify-between">
                                    <div class="row justify-start q-gutter-sm">
                                        <div>
                                            <q-btn color="primary" @click="showOccurrenceLinkageToolPopup = true" label="Set Occurrence Linkage" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Link, or change linkage, to an occurrence record
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div>
                                            <q-btn color="primary" @click="removeOccurrenceLinkage();" label="Remove Occurrence Linkage" dense>
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Remove occurrence linkage so that media only displays on Taxon Profile page
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                    <div class="row justify-end q-gutter-sm">
                                        <div>
                                            <q-btn color="negative" @click="processDeleteMediaRecord();" label="Delete Media" />
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
        <template v-if="showOccurrenceLinkageToolPopup">
            <occurrence-linkage-tool-popup
                :show-popup="showOccurrenceLinkageToolPopup"
                @close:popup="showOccurrenceLinkageToolPopup = false"
            ></occurrence-linkage-tool-popup>
        </template>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'occurrence-linkage-tool-popup': occurrenceLinkageToolPopup,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { showNotification } = useCore();
        const mediaStore = useMediaStore();

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => mediaStore.getMediaEditsExist);
        const mediaData = Vue.computed(() => {
            if(Number(props.mediaId) > 0){
                return mediaStore.getMediaData;
            }
            else{
                return props.newMediaData['uploadMetadata'];
            }
        });
        const showOccurrenceLinkageToolPopup = Vue.ref(false);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processDeleteMediaRecord() {
            const confirmText = 'Are you sure you want to delete this media? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    mediaStore.deleteMediaRecord(props.collId, (res) => {
                        if(res === 0){
                            showNotification('negative', ('An error occurred while deleting this media.'));
                        }
                        else{
                            showNotification('positive','Media deleted');
                            context.emit('media:updated');
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
            mediaStore.resetOccurrenceLinkage(props.collId, null, (res) => {
                if(res === 1){
                    showNotification('positive','Occurrence linkage removed');
                    context.emit('media:updated');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error removing the occurrence linkage.');
                }
            });
        }

        function saveMediaEdits() {
            mediaStore.updateMediaRecord(props.collId, (res) => {
                if(res === 1){
                    showNotification('positive','Edits saved.');
                    context.emit('media:updated');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error saving the media edits.');
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
            if(Number(props.mediaId) > 0){
                mediaStore.updateMediaEditData(key, value);
            }
            else{
                context.emit('update:media-data', {key: key, value: value});
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            if(Number(props.mediaId) > 0){
                mediaStore.setCurrentMediaRecord(props.mediaId);
            }
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            mediaData,
            showOccurrenceLinkageToolPopup,
            closePopup,
            processDeleteMediaRecord,
            processScientificNameChange,
            removeOccurrenceLinkage,
            saveMediaEdits,
            updateData
        }
    }
};
