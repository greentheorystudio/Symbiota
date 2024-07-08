const occurrenceEditorMediaTab = {
    template: `
        <div class="column q-gutter-sm">
            <media-file-upload-input-element :occ-id="occId" :taxon-id="occurrenceData.tid"></media-file-upload-input-element>
        </div>
        <template v-if="showMediaEditorPopup">
            <occurrence-media-editor-popup
                    :show-popup="showMediaEditorPopup"
                    @close:popup="showMediaEditorPopup = false"
            ></occurrence-media-editor-popup>
        </template>
    `,
    components: {
        'media-file-upload-input-element': mediaFileUploadInputElement,
        'occurrence-media-editor-popup': occurrenceMediaEditorPopup
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const showMediaEditorPopup = Vue.ref(false);

        return {
            occId,
            occurrenceData,
            showMediaEditorPopup
        }
    }
};
