const occurrenceEditorOccurrenceDataControls = {
    template: `
        <div class="row justify-between">
            <div>
                <template v-if="Number(occId) === 0">
                    <div class="row q-gutter-sm">
                        <occurrence-entry-follow-up-action-selector :selected-action="entryFollowUpAction" @change-occurrence-entry-follow-up-action="changeEntryFollowUpAction"></occurrence-entry-follow-up-action-selector>
                        <div v-if="additionalDataFields.length === 0 && occurrenceEntryFormat !== 'benthic'">
                            <q-toggle v-model="collectionEventAutoSearch" checked-icon="check" color="green" unchecked-icon="clear" label="Event Auto Search" @update:model-value="setCollectionEventAutoSearch"></q-toggle>
                        </div>
                    </div>
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

        const additionalDataFields = Vue.computed(() => occurrenceStore.getAdditionalDataFields);
        const collectionEventAutoSearch = Vue.computed(() => occurrenceStore.getCollectionEventAutoSearch);
        const editsExist = Vue.computed(() => occurrenceStore.getOccurrenceEditsExist);
        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
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

        function setCollectionEventAutoSearch(value) {
            occurrenceStore.setCollectionEventAutoSearch(value);
        }

        return {
            additionalDataFields,
            collectionEventAutoSearch,
            editsExist,
            entryFollowUpAction,
            occId,
            occurrenceEntryFormat,
            occurrenceValid,
            changeEntryFollowUpAction,
            createOccurrenceRecord,
            saveOccurrenceEdits,
            setCollectionEventAutoSearch
        }
    }
};
