const occurrenceEditorMediaTab = {
    template: `
        <div class="column q-gutter-sm">
            <div class="row justify-end">
                <q-btn color="secondary" @click="showMediaEditorPopup = true" label="Add New Media File" />
            </div>
        </div>
        <template v-if="showMediaEditorPopup">
            <occurrence-media-editor-popup
                    :show-popup="showMediaEditorPopup"
                    @close:popup="showMediaEditorPopup = false"
            ></occurrence-media-editor-popup>
        </template>
    `,
    components: {
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
