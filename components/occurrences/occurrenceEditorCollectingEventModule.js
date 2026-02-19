const occurrenceEditorCollectingEventModule = {
    template: `
        <q-card flat bordered class="black-border">
            <q-card-section class="q-px-sm q-pb-sm column q-col-gutter-sm">
                <div class="row justify-between">
                    <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                        Collecting Event
                    </div>
                    <div class="row justify-end q-gutter-sm">
                        <template v-if="Number(eventId) > 0">
                            <template v-if="occurrenceEntryFormat === 'replicate' && Number(eventData.repcount) > 0">
                                <template v-if="collectingEventReplicateTaxaCnt > 0">
                                    <q-btn color="secondary" @click="showReplicateTaxaListPopup = true" label="View Taxa" tabindex="0" />
                                </template>
                                <q-btn color="secondary" @click="showReplicateTaxaEditorPopup = true" label="Add Taxa" tabindex="0" />
                            </template>
                            <template v-else-if="collectingEventCollectionArr.length > 0">
                                <q-btn color="secondary" @click="showCollectionListPopup = true" label="View Collections" tabindex="0" />
                            </template>
                        </template>
                        <template v-if="Number(eventId) > 0 && Object.keys(configuredDataFields).length > 0">
                            <q-btn color="secondary" @click="showConfiguredDataEditorPopup = true" :label="configuredDataLabel" tabindex="0" />
                        </template>
                        <template v-if="Number(eventId) === 0">
                            <q-btn color="secondary" @click="createCollectingEventRecord();" label="Create Event Record" :disabled="!eventValid" tabindex="0" />
                        </template>
                        <template v-else>
                            <q-btn color="secondary" @click="processOpenEditor" label="Edit Event" tabindex="0" />
                        </template>
                    </div>
                </div>
                <collecting-event-field-module :event-mode="true" :disabled="(eventId > 0)" :data="eventData" :fields="eventFields" :field-definitions="occurrenceFieldDefinitions" @update:collecting-event-data="(data) => updateCollectingEventData(data.key, data.value)"></collecting-event-field-module>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-6 col-md-9">
                        <text-field-input-element :disabled="(eventId > 0)" :definition="occurrenceFieldDefinitions['eventremarks']" label="Event Remarks" :maxlength="eventFields['eventremarks'] ? eventFields['eventremarks']['length'] : 0" :value="eventData.eventremarks" @update:value="(value) => updateCollectingEventData('eventremarks', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :disabled="(eventId > 0)" data-type="int" :definition="occurrenceFieldDefinitions['repcount']" label="Rep Count" :maxlength="eventFields['repcount'] ? eventFields['repcount']['length'] : 0" :value="eventData.repcount" @update:value="(value) => updateCollectingEventData('repcount', value)"></text-field-input-element>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <template v-if="showCollectionListPopup">
            <occurrence-collection-list-popup
                :collection-arr="collectingEventCollectionArr"
                :show-popup="showCollectionListPopup"
                @close:popup="showCollectionListPopup = false"
            ></occurrence-collection-list-popup>
        </template>
        <template v-if="showReplicateTaxaListPopup">
            <occurrence-collecting-event-replicate-taxa-list-popup
                :show-popup="showReplicateTaxaListPopup"
                @update:edit-taxon="processEditTaxonRequest"
                @close:popup="showReplicateTaxaListPopup = false"
            ></occurrence-collecting-event-replicate-taxa-list-popup>
        </template>
        <template v-if="showReplicateTaxaEditorPopup">
            <occurrence-collecting-event-replicate-taxa-editor-popup
                :edit-taxon="editTaxonPopupTaxonData"
                :show-popup="showReplicateTaxaEditorPopup"
                @close:popup="closeReplicateTaxaEditorPopup();"
            ></occurrence-collecting-event-replicate-taxa-list-popup>
        </template>
        <template v-if="showEventEditorPopup">
            <occurrence-collecting-event-editor-popup
                :show-popup="showEventEditorPopup"
                @close:popup="showEventEditorPopup = false"
            ></occurrence-collecting-event-editor-popup>
        </template>
        <template v-if="showConfiguredDataEditorPopup">
            <mof-data-editor-popup
                data-type="event"
                :new-record="Number(eventId) === 0"
                :show-popup="showConfiguredDataEditorPopup"
                @close:popup="showConfiguredDataEditorPopup = false"
            ></mof-data-editor-popup>
        </template>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'collecting-event-field-module': collectingEventFieldModule,
        'confirmation-popup': confirmationPopup,
        'mof-data-editor-popup': mofDataEditorPopup,
        'occurrence-collecting-event-replicate-taxa-editor-popup': occurrenceCollectingEventReplicateTaxaEditorPopup,
        'occurrence-collecting-event-replicate-taxa-list-popup': occurrenceCollectingEventReplicateTaxaListPopup,
        'occurrence-collecting-event-editor-popup': occurrenceCollectingEventEditorPopup,
        'occurrence-collection-list-popup': occurrenceCollectionListPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const collectingEventCollectionArr = Vue.computed(() => occurrenceStore.getCollectingEventCollectionArr);
        const collectingEventReplicateData = Vue.computed(() => occurrenceStore.getCollectingEventReplicateData);
        const collectingEventReplicateTaxaCnt = Vue.computed(() => occurrenceStore.getCollectingEventReplicateTaxaCnt);
        const configuredDataFields = Vue.computed(() => occurrenceStore.getEventMofDataFields);
        const configuredDataLabel = Vue.computed(() => occurrenceStore.getEventMofDataLabel);
        const confirmationPopupRef = Vue.ref(null);
        const editorConfirmed = Vue.ref(false);
        const editTaxonPopupTaxonData = Vue.ref(null);
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const eventFields = Vue.computed(() => occurrenceStore.getCollectingEventFields);
        const eventId = Vue.computed(() => occurrenceStore.getCollectingEventID);
        const eventValid = Vue.computed(() => occurrenceStore.getCollectingEventValid);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceFieldDefinitions);
        const showCollectionListPopup = Vue.ref(false);
        const showConfiguredDataEditorPopup = Vue.ref(false);
        const showEventEditorPopup = Vue.ref(false);
        const showReplicateTaxaEditorPopup = Vue.ref(false);
        const showReplicateTaxaListPopup = Vue.ref(false);

        function closeReplicateTaxaEditorPopup() {
            editTaxonPopupTaxonData.value = null;
            showReplicateTaxaEditorPopup.value = false;
        }

        function createCollectingEventRecord() {
            occurrenceStore.createCollectingEventRecord((newEventId) => {
                if(newEventId > 0){
                    showNotification('positive','Event record created successfully.');
                }
                else{
                    showNotification('negative', 'There was an error creating the event record.');
                }
            });
        }

        function processEditTaxonRequest(taxon) {
            editTaxonPopupTaxonData.value = taxon;
            showReplicateTaxaListPopup.value = false;
            showReplicateTaxaEditorPopup.value = true;
        }

        function processOpenEditor() {
            if(editorConfirmed.value){
                showEventEditorPopup.value = true;
            }
            else{
                const confirmText = 'If you want to edit this collecting event, click OK to continue. If you want to change the collecting event for this occurrence, click Cancel, and then click Change Event/Location button in the bottom section. ';
                confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'Cancel', trueText: 'OK', callback: (val) => {
                    editorConfirmed.value = true;
                    if(val){
                        showEventEditorPopup.value = true;
                    }
                }});
            }
        }

        function updateCollectingEventData(key, value) {
            occurrenceStore.updateCollectingEventEditData(key, value);
        }

        Vue.onMounted(() => {
            occurrenceStore.setCollectingEventFields();
        });

        return {
            collectingEventCollectionArr,
            collectingEventReplicateData,
            collectingEventReplicateTaxaCnt,
            configuredDataFields,
            configuredDataLabel,
            confirmationPopupRef,
            editTaxonPopupTaxonData,
            eventData,
            eventFields,
            eventId,
            eventValid,
            occId,
            occurrenceEntryFormat,
            occurrenceFieldDefinitions,
            showCollectionListPopup,
            showConfiguredDataEditorPopup,
            showEventEditorPopup,
            showReplicateTaxaEditorPopup,
            showReplicateTaxaListPopup,
            closeReplicateTaxaEditorPopup,
            createCollectingEventRecord,
            processEditTaxonRequest,
            processOpenEditor,
            updateCollectingEventData
        }
    }
};
