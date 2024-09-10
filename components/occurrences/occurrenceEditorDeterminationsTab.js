const occurrenceEditorDeterminationsTab = {
    template: `
        <div class="column q-gutter-sm">
            <div class="row justify-between q-gutter-sm">
                <div class="text-h6 text-bold">Determination History</div>
                <div>
                    <q-btn color="secondary" @click="openDeterminationEditorPopup(0);" label="Add New Determination" />
                </div>
            </div>
            <div class="q-mt-sm column q-gutter-sm">
                <template v-if="determinationArr.length > 0">
                    <template v-for="determination in determinationArr">
                        <determination-record-info-block :determination-data="determination" :editor="true"></determination-record-info-block>
                    </template>
                </template>
                <template v-else>
                    <span class="text-body1 text-bold">There are no previous determinations for this record.</span>
                </template>
            </div>
        </div>
        <template v-if="showDeterminationEditorPopup">
            <occurrence-determination-editor-popup
                :determination-id="editDeterminationId"
                :show-popup="showDeterminationEditorPopup"
                @close:popup="showDeterminationEditorPopup = false"
            ></occurrence-determination-editor-popup>
        </template>
    `,
    components: {
        'determination-record-info-block': determinationRecordInfoBlock,
        'occurrence-determination-editor-popup': occurrenceDeterminationEditorPopup
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const determinationArr = Vue.computed(() => occurrenceStore.getDeterminationArr);
        const editDeterminationId = Vue.ref(0);
        const showDeterminationEditorPopup = Vue.ref(false);

        function openDeterminationEditorPopup(id) {
            editDeterminationId.value = id;
            showDeterminationEditorPopup.value = true;
        }

        return {
            determinationArr,
            editDeterminationId,
            showDeterminationEditorPopup,
            openDeterminationEditorPopup
        }
    }
};
