const occurrenceEditorOccurrenceDataControls = {
    template: `
        <div class="row justify-between">
            <div>
                <template v-if="Number(occId) === 0">
                    <occurrence-entry-follow-up-action-selector :selected-action="entryFollowUpAction" @change-occurrence-entry-follow-up-action="changeEntryFollowUpAction"></occurrence-entry-follow-up-action-selector>
                </template>
                <template v-else-if="editsExist">
                    <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                </template>
            </div>
            <div class="row justify-end">
                <template v-if="Number(occId) === 0">
                    <q-btn color="secondary" @click="createOccurrenceRecord();" label="Create Occurrence Record" :disabled="!occurrenceValid" />
                </template>
                <template v-else>
                    <q-btn color="secondary" @click="saveOccurrenceEdits();" label="Save Occurrence Edits" :disabled="!editsExist || !occurrenceValid" />
                </template>
            </div>
        </div>
    `,
    components: {
        'occurrence-entry-follow-up-action-selector': occurrenceEntryFollowUpActionSelector
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const editsExist = Vue.computed(() => occurrenceStore.getOccurrenceEditsExist);
        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceValid = Vue.computed(() => occurrenceStore.getOccurrenceValid);

        function changeEntryFollowUpAction(value) {
            occurrenceStore.setEntryFollowUpAction(value);
        }

        function createOccurrenceRecord() {
            occurrenceStore.createOccurrenceRecord((newOccid) => {
                if(newOccid > 0){
                    showNotification('positive','Occurrence record created successfully.');
                }
                else{
                    showNotification('negative', 'There was an error creating the occurrence record.');
                }
            });
        }

        function saveOccurrenceEdits() {
            occurrenceStore.updateOccurrenceRecord((res) => {
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the occurrence edits.');
                }
            });
        }

        return {
            editsExist,
            entryFollowUpAction,
            occId,
            occurrenceValid,
            changeEntryFollowUpAction,
            createOccurrenceRecord,
            saveOccurrenceEdits
        }
    }
};
