const occurrenceEditorImagesTab = {
    template: `
        <div class="column q-gutter-sm">
            <div class="row justify-end">
                <q-btn color="secondary" @click="showImageEditorPopup = true" label="Add New Image" />
            </div>
        </div>
        <template v-if="showImageEditorPopup">
            <occurrence-image-editor-popup
                    :show-popup="showImageEditorPopup"
                    @close:popup="showImageEditorPopup = false"
            ></occurrence-image-editor-popup>
        </template>
    `,
    components: {
        'occurrence-image-editor-popup': occurrenceImageEditorPopup
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const showImageEditorPopup = Vue.ref(false);

        return {
            showImageEditorPopup
        }
    }
};
