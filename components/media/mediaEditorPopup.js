const mediaEditorPopup = {
    props: {
        collection: {
            type: Object,
            default: null
        },
        collId: {
            type: Number,
            default: 0
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
        },
        taxon: {
            type: Object,
            default: null
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" v-if="!showOccurrenceLinkageToolPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div v-if="Number(mediaId) > 0" class="row justify-between">
                                <div>
                                    <template v-if="editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <q-btn color="secondary" @click="saveMediaEdits();" label="Save Media Edits" :disabled="!editsExist" tabindex="0" />
                                </div>
                            </div>
                            <template v-if="Number(mediaData.occid) === 0">
                                <div class="row">
                                    <div class="col-grow">
                                        <single-scientific-common-name-auto-complete :sciname="mediaData.sciname" label="Scientific Name" :clearable="false" :limit-to-options="true" @update:sciname="processScientificNameChange"></single-scientific-common-name-auto-complete>
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
                            <div v-if="Number(mediaData.occid) === 0" class="row">
                                <div class="col-grow">
                                    <user-auto-complete label="Portal Contributor" :value="mediaData.creatoruid" @update:value="processContributorChange"></user-auto-complete>
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
                                <div class="row">
                                    <div class="col-grow">
                                        <text-field-input-element :disabled="true" data-type="textarea" label="Descriptive Transcript URL" :value="mediaData.descriptivetranscripturi"></text-field-input-element>
                                    </div>
                                </div>
                                <div>
                                    <q-card flat bordered class="black-border bg-grey-4">
                                        <q-card-section class="q-pa-sm column q-col-gutter-sm">
                                            <div class="row justify-between">
                                                <div class="text-subtitle1 text-bold">{{ descriptiveTranscriptUploadLabel + ' Descriptive Transcript' }}</div>
                                                <div>
                                                    <q-btn-toggle v-model="selectedUploadMethod" :options="uploadMethodOptions" class="black-border" size="sm" rounded unelevated toggle-color="primary" color="white" text-color="primary" aria-label="Upload method" tabindex="0"></q-btn-toggle>
                                                </div>
                                            </div>
                                            <div class="q-mt-xs row justify-between">
                                                <div class="col-9">
                                                    <template v-if="selectedUploadMethod === 'upload'">
                                                        <file-picker-input-element :label="(descriptiveTranscriptUploadLabel + ' File')" :accepted-types="acceptedFileTypes" :value="uploadedTranscriptFile" :validate-file-size="true" @update:file="(value) => uploadedTranscriptFile = value[0]"></file-picker-input-element>
                                                    </template>
                                                    <template v-else>
                                                        <text-field-input-element data-type="textarea" label="Transcript URL" :value="transcriptUrl" @update:value="(value) => processTranscriptUrlChange"></text-field-input-element>
                                                    </template>
                                                </div>
                                                <div class="col-3 row justify-end">
                                                    <q-btn color="secondary" @click="preProcessUpdateUploadTranscriptFile();" :label="descriptiveTranscriptUploadLabel" :disabled="((selectedUploadMethod === 'upload' && !uploadedTranscriptFile) || (selectedUploadMethod === 'url' && !transcriptUrl))" tabindex="0" />
                                                </div>
                                            </div>
                                        </q-card-section>
                                    </q-card>
                                </div>
                                <div class="row justify-between">
                                    <div class="row justify-start q-gutter-sm">
                                        <div>
                                            <q-btn color="primary" @click="showOccurrenceLinkageToolPopup = true" label="Set Occurrence Linkage" dense tabindex="0">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Link, or change linkage, to an occurrence record
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                        <div v-if="Number(mediaData.occid) > 0">
                                            <q-btn color="primary" @click="removeOccurrenceLinkage();" label="Remove Occurrence Linkage" dense tabindex="0">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Remove occurrence linkage so that media only displays on Taxon Profile page
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                    <div class="row justify-end q-gutter-sm">
                                        <template v-if="mediaData.descriptivetranscripturi">
                                            <div>
                                                <q-btn color="negative" @click="processDeleteTranscript();" label="Delete Transcript" tabindex="0" />
                                            </div>
                                        </template>
                                        <div>
                                            <q-btn color="negative" @click="processDeleteMediaRecord();" label="Delete Media" tabindex="0" />
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
                :avoid-arr="[Number(mediaData.occid)]"
                @update:occid="updateOccurrenceLinkage"
                @close:popup="showOccurrenceLinkageToolPopup = false"
            ></occurrence-linkage-tool-popup>
        </template>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'file-picker-input-element': filePickerInputElement,
        'occurrence-linkage-tool-popup': occurrenceLinkageToolPopup,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement,
        'user-auto-complete': userAutoComplete
    },
    setup(props, context) {
        const { showNotification } = useCore();
        const mediaStore = useMediaStore();

        const acceptedFileTypes = ['doc', 'docx', 'pdf', 'txt'];
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const descriptiveTranscriptUploadLabel = Vue.computed(() => {
            return (mediaData.value.hasOwnProperty('descriptivetranscripturi') && mediaData.value.descriptivetranscripturi) ? 'Update' : 'Upload';
        });
        const editsExist = Vue.computed(() => mediaStore.getMediaEditsExist);
        const mediaData = Vue.computed(() => {
            if(Number(props.mediaId) > 0){
                return mediaStore.getMediaData;
            }
            else{
                return props.newMediaData['uploadMetadata'];
            }
        });
        const selectedUploadMethod = Vue.ref('upload');
        const showOccurrenceLinkageToolPopup = Vue.ref(false);
        const transcriptUrl = Vue.ref(null);
        const uploadedTranscriptFile = Vue.ref(null);
        const uploadMethodOptions = [
            {label: 'Local File', value: 'upload'},
            {label: 'From URL', value: 'url'}
        ];
        const uploadPath = Vue.computed(() => {
            let path = '';
            if(props.collection){
                if(props.collection.institutioncode){
                    path += props.collection.institutioncode;
                }
                if(props.collection.institutioncode && props.collection.collectioncode){
                    path += '_';
                }
                if(props.collection.collectioncode){
                    path += props.collection.collectioncode;
                }
            }
            else if(props.taxon){
                if(props.taxon.family){
                    path += props.taxon.family;
                }
                else{
                    path += props.taxon['unitname1'];
                }
            }
            else{
                path += 'general';
            }
            return path;
        });

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function preProcessUpdateUploadTranscriptFile() {
            if(mediaData.value.descriptivetranscripturi && mediaData.value.descriptivetranscripturi.startsWith('/')){
                mediaStore.deleteMediaTranscriptFile(props.collId, mediaData.value.descriptivetranscripturi, (res) => {
                    if(res === 0){
                        showNotification('negative', ('An error occurred while deleting the previous descriptive transcript file.'));
                    }
                    updateData('descriptivetranscripturi', null);
                    processUpdateUploadTranscriptFile();
                });
            }
            else{
                updateData('descriptivetranscripturi', null);
                processUpdateUploadTranscriptFile();
            }
        }

        function processContributorChange(user) {
            if(user){
                const fullName = user.firstname + ' ' + (user.middleinitial ? (user.middleinitial + ' ') : '') + user.lastname;
                updateData('creator', fullName);
                updateData('creatoruid', user.uid);
            }
            else{
                updateData('creatoruid', null);
            }
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

        function processDeleteTranscript() {
            const confirmText = 'Are you sure you want to delete the descriptive transcript file? This action cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    if(mediaData.value.descriptivetranscripturi.startsWith('/')){
                        mediaStore.deleteMediaTranscriptFile(props.collId, mediaData.value.descriptivetranscripturi, (res) => {
                            if(res === 0){
                                showNotification('negative', ('An error occurred while deleting the descriptive transcript file.'));
                            }
                            else{
                                updateData('descriptivetranscripturi', null);
                                if(Number(props.mediaId) > 0){
                                    saveMediaEdits();
                                    context.emit('media:updated');
                                }
                            }
                        });
                    }
                    else{
                        updateData('descriptivetranscripturi', null);
                        if(Number(props.mediaId) > 0){
                            saveMediaEdits();
                            context.emit('media:updated');
                        }
                    }
                }
            }});
        }

        function processScientificNameChange(taxon) {
            updateData('sciname', taxon.sciname);
            updateData('tid', taxon.tid);
        }

        function processTranscriptUrlChange(value) {
            if(value.name.endsWith('.doc') || value.name.endsWith('.docx') || value.name.endsWith('.pdf') || value.name.endsWith('.txt')){
                transcriptUrl.value = value;
            }
            else{
                showNotification('negative', ('Transcripts can only be in .doc, .docx, .pdf, or .txt file formats.'));
            }
        }

        function processUpdateUploadTranscriptFile() {
            if(selectedUploadMethod.value === 'upload' || !mediaData.value.accessuri || mediaData.value.accessuri.startsWith('/')){
                updateUploadTranscriptFile();
            }
            else{
                updateData('descriptivetranscripturi', transcriptUrl.value);
                saveMediaEdits();
                context.emit('media:updated');
            }
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

        function updateOccurrenceLinkage(occid) {
            updateData('occid', occid);
            setTimeout(() => {
                saveMediaEdits();
            }, 100);
        }

        function updateUploadTranscriptFile() {
            if(selectedUploadMethod.value === 'upload' && uploadedTranscriptFile.value){
                mediaStore.uploadDescriptiveTranscriptFromFile(props.collId, uploadedTranscriptFile.value, uploadPath.value, (res) => {
                    if(res.toString() === ''){
                        showNotification('negative', ('An error occurred while uploading the descriptive transcript file.'));
                    }
                    else{
                        updateData('descriptivetranscripturi', res.toString());
                        if(Number(props.mediaId) > 0){
                            saveMediaEdits();
                            context.emit('media:updated');
                        }
                    }
                });
            }
            else if(transcriptUrl.value){
                mediaStore.uploadDescriptiveTranscriptFromUrl(props.collId, transcriptUrl.value, uploadPath.value, (res) => {
                    if(res.toString() === ''){
                        showNotification('negative', ('An error occurred while copying the descriptive transcript file.'));
                    }
                    else{
                        updateData('descriptivetranscripturi', res.toString());
                        if(Number(props.mediaId) > 0){
                            saveMediaEdits();
                            context.emit('media:updated');
                        }
                    }
                });
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
            acceptedFileTypes,
            confirmationPopupRef,
            contentRef,
            contentStyle,
            descriptiveTranscriptUploadLabel,
            editsExist,
            mediaData,
            selectedUploadMethod,
            showOccurrenceLinkageToolPopup,
            transcriptUrl,
            uploadedTranscriptFile,
            uploadMethodOptions,
            closePopup,
            processContributorChange,
            processDeleteMediaRecord,
            processDeleteTranscript,
            processScientificNameChange,
            processTranscriptUrlChange,
            preProcessUpdateUploadTranscriptFile,
            removeOccurrenceLinkage,
            saveMediaEdits,
            updateData,
            updateOccurrenceLinkage
        }
    }
};
