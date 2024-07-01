const occurrenceEditorMediaTab = {
    template: `
        <div class="column q-gutter-sm">
            <media-file-upload-input-element></media-file-upload-input-element>
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

        const showMediaEditorPopup = Vue.ref(false);

        return {
            showMediaEditorPopup
        }
    }
};
