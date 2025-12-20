const occurrenceEditorDeterminationHistoryBlock = {
    template: `
        <div class="column q-gutter-sm">
            <div class="row justify-between q-gutter-sm">
                <div class="text-h6 text-bold">Determination History</div>
                <div>
                    <q-btn color="secondary" @click="openDeterminationEditorPopup(0);" label="Add New Determination" tabindex="0" />
                </div>
            </div>
            <div class="q-mt-sm column q-gutter-sm">
                <template v-if="determinationArr.length > 0">
                    <template v-for="determination in determinationArr">
                        <determination-record-info-block :determination-data="determination" :editor="true" @open:determination-editor="openDeterminationEditorPopup"></determination-record-info-block>
                    </template>
                </template>
                <template v-else>
                    <span class="text-body1 text-bold">There are no previous determinations for this record.</span>
                </template>
            </div>
        </div>
    `,
    components: {
        'determination-record-info-block': determinationRecordInfoBlock
    },
    setup(_, context) {
        const occurrenceStore = useOccurrenceStore();

        const determinationArr = Vue.computed(() => occurrenceStore.getDeterminationArr);

        function openDeterminationEditorPopup(id) {
            context.emit('open:determination-editor', id);
        }

        return {
            determinationArr,
            openDeterminationEditorPopup
        }
    }
};
