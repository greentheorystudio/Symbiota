const occurrenceEditorOccurrenceDataControls = {
    template: `
        <div class="row justify-between">
            <div>
                <template v-if="Number(occId) === 0">
                    <div class="row q-gutter-sm">
                        <occurrence-entry-follow-up-action-selector :selected-action="entryFollowUpAction" @change-occurrence-entry-follow-up-action="changeEntryFollowUpAction"></occurrence-entry-follow-up-action-selector>
                        <div v-if="Object.keys(configuredEventMofDataFields).length === 0 && occurrenceEntryFormat !== 'lot' && occurrenceEntryFormat !== 'replicate'">
                            <q-toggle v-model="collectionEventAutoSearch" checked-icon="check" color="green" unchecked-icon="clear" label="Event Auto Search" @update:model-value="setCollectingEventAutoSearch"></q-toggle>
                        </div>
                    </div>
                </template>
                <template v-else-if="editsExist">
                    <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                </template>
            </div>
            <div class="row justify-end q-gutter-sm">
                <template v-if="Number(occId) === 0">
                    <template v-if="Object.keys(configuredDataFields).length > 0">
                        <q-btn color="secondary" @click="showConfiguredDataEditorPopup = true" :label="configuredDataLabel" tabindex="0" />
                    </template>
                    <q-btn color="secondary" @click="createOccurrenceRecord();" label="Create Occurrence Record" :disabled="!occurrenceValid" tabindex="0" />
                </template>
                <template v-else>
                    <template v-if="occurrenceEntryFormat === 'lot' || occurrenceEntryFormat === 'replicate'">
                        <q-btn color="secondary" @click="showEventLocationTransferPopup = true" label="Change Event/Location" tabindex="0" />
                    </template>
                    <q-btn color="secondary" @click="saveOccurrenceEdits();" label="Save Occurrence Edits" :disabled="!editsExist || !occurrenceValid" tabindex="0" />
                </template>
            </div>
        </div>
        <template v-if="showConfiguredDataEditorPopup">
            <mof-data-editor-popup
                data-type="occurrence"
                :new-record="Number(occId) === 0"
                :show-popup="showConfiguredDataEditorPopup"
                @close:popup="showConfiguredDataEditorPopup = false"
            ></mof-data-editor-popup>
        </template>
        <template v-if="showEventLocationTransferPopup">
            <occurrence-editor-event-location-transfer-popup
                :show-popup="showEventLocationTransferPopup"
                @close:popup="showEventLocationTransferPopup = false"
            ></occurrence-editor-event-location-transfer-popup>
        </template>
    `,
    components: {
        'mof-data-editor-popup': mofDataEditorPopup,
        'occurrence-editor-event-location-transfer-popup': occurrenceEditorEventLocationTransferPopup,
        'occurrence-entry-follow-up-action-selector': occurrenceEntryFollowUpActionSelector
    },
    setup(_, context) {
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const collectionEventAutoSearch = Vue.computed(() => occurrenceStore.getCollectingEventAutoSearch);
        const configuredDataFields = Vue.computed(() => occurrenceStore.getOccurrenceMofDataFields);
        const configuredDataLabel = Vue.computed(() => occurrenceStore.getOccurrenceMofDataLabel);
        const configuredEventMofDataFields = Vue.computed(() => occurrenceStore.getEventMofDataFields);
        const editsExist = Vue.computed(() => occurrenceStore.getOccurrenceEditsExist);
        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceValid = Vue.computed(() => occurrenceStore.getOccurrenceValid);
        const showConfiguredDataEditorPopup = Vue.ref(false);
        const showEventLocationTransferPopup = Vue.ref(false);

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
                            searchStore.addNewOccidToOccidArrs(newOccid);
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
                        searchStore.addNewOccidToOccidArrs(newOccid);
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
            configuredDataFields,
            configuredDataLabel,
            configuredEventMofDataFields,
            editsExist,
            entryFollowUpAction,
            occId,
            occurrenceEntryFormat,
            occurrenceValid,
            showConfiguredDataEditorPopup,
            showEventLocationTransferPopup,
            changeEntryFollowUpAction,
            createOccurrenceRecord,
            saveOccurrenceEdits,
            setCollectingEventAutoSearch
        }
    }
};
