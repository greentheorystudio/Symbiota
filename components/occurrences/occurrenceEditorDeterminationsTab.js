const occurrenceEditorDeterminationsTab = {
    template: `
        <occurrence-editor-determination-history-block @open:determination-editor="openDeterminationEditorPopup"></occurrence-editor-determination-history-block>
        <template v-if="showDeterminationEditorPopup">
            <occurrence-determination-editor-popup
                :determination-id="editDeterminationId"
                :show-popup="showDeterminationEditorPopup"
                @close:popup="showDeterminationEditorPopup = false"
            ></occurrence-determination-editor-popup>
        </template>
    `,
    components: {
        'occurrence-determination-editor-popup': occurrenceDeterminationEditorPopup,
        'occurrence-editor-determination-history-block': occurrenceEditorDeterminationHistoryBlock,
    },
    setup() {
        const editDeterminationId = Vue.ref(0);
        const showDeterminationEditorPopup = Vue.ref(false);

        function openDeterminationEditorPopup(id) {
            editDeterminationId.value = id;
            showDeterminationEditorPopup.value = true;
        }

        return {
            editDeterminationId,
            showDeterminationEditorPopup,
            openDeterminationEditorPopup
        }
    }
};
