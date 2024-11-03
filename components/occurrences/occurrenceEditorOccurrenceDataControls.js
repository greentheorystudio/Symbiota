const occurrenceEditorOccurrenceDataControls = {
    template: `
        <div class="row justify-between">
            <div>
                <template v-if="Number(occId) === 0">
                    <div class="row q-gutter-sm">
                        <occurrence-entry-follow-up-action-selector :selected-action="entryFollowUpAction" @change-occurrence-entry-follow-up-action="changeEntryFollowUpAction"></occurrence-entry-follow-up-action-selector>
                        <div v-if="Object.keys(configuredEventMofDataFields).length === 0 && occurrenceEntryFormat !== 'lot' && occurrenceEntryFormat !== 'benthic'">
                            <q-toggle v-model="collectionEventAutoSearch" checked-icon="check" color="green" unchecked-icon="clear" label="Event Auto Search" @update:model-value="setCollectingEventAutoSearch"></q-toggle>
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
    setup(_, context) {
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const collectionEventAutoSearch = Vue.computed(() => occurrenceStore.getCollectingEventAutoSearch);
        const configuredEventMofDataFields = Vue.computed(() => occurrenceStore.getEventMofDataFields);
        const editsExist = Vue.computed(() => occurrenceStore.getOccurrenceEditsExist);
        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceValid = Vue.computed(() => occurrenceStore.getOccurrenceValid);

        function changeEntryFollowUpAction(value) {
            occurrenceStore.setEntryFollowUpAction(value);
        }

        function createOccurrenceRecord() {
            if(Object.keys(configuredEventMofDataFields.value).length > 0 && !occurrenceData.value['eventid']){
                occurrenceStore.setNewCollectingEventDataFromCurrentOccurrence();
                occurrenceStore.createCollectingEventRecord(() => {
                    occurrenceStore.createOccurrenceRecord((newOccid) => {
                        if(newOccid > 0){
                            context.emit('occurrence:created', newOccid);
                            showNotification('positive','Occurrence record created successfully.');
                        }
                        else{
                            showNotification('negative', 'There was an error creating the occurrence record.');
                        }
                    });
                });
            }
            else{
                occurrenceStore.createOccurrenceRecord((newOccid) => {
                    if(newOccid > 0){
                        context.emit('occurrence:created', newOccid);
                        showNotification('positive','Occurrence record created successfully.');
                    }
                    else{
                        showNotification('negative', 'There was an error creating the occurrence record.');
                    }
                });
            }
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

        function setCollectingEventAutoSearch(value) {
            occurrenceStore.setCollectingEventAutoSearch(value);
        }

        return {
            collectionEventAutoSearch,
            configuredEventMofDataFields,
            editsExist,
            entryFollowUpAction,
            occId,
            occurrenceEntryFormat,
            occurrenceValid,
            changeEntryFollowUpAction,
            createOccurrenceRecord,
            saveOccurrenceEdits,
            setCollectingEventAutoSearch
        }
    }
};
