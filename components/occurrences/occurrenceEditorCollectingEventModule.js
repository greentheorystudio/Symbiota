const occurrenceEditorCollectingEventModule = {
    template: `
        <q-card flat bordered class="black-border">
            <q-card-section class="q-px-sm q-pb-sm column q-gutter-y-sm">
                <div class="row justify-between">
                    <div>
                        <div v-if="Number(eventId) > 0" class="row q-gutter-sm">
                            <template v-if="occurrenceEntryFormat === 'benthic'">
                                <q-btn color="secondary" @click="" label="View Taxa" />
                                <q-btn color="secondary" @click="" label="Add Taxon" />
                            </template>
                            <template v-else>
                                <q-btn color="secondary" @click="" label="View Collections" />
                            </template>
                            <template v-if="additionalDataFields.length > 0 && Number(occId) === 0">
                                <q-btn color="secondary" @click="" label="Additional Data" />
                            </template>
                        </div>
                    </div>
                    <div class="row justify-end">
                        <template v-if="Number(eventId) === 0">
                            <q-btn color="secondary" @click="createCollectingEventRecord();" label="Create Event Record" :disabled="!eventValid" />
                        </template>
                        <template v-else>
                            <q-btn color="secondary" @click="showEventEditorPopup = true" label="Edit Event" />
                        </template>
                    </div>
                </div>
                <collecting-event-field-module :event-mode="true" :disabled="(eventId > 0)" :data="eventData" :fields="eventFields" :field-definitions="occurrenceFieldDefinitions" update:collecting-event-data="(data) => updateCollectingEventData(data.key, data.value)"></collecting-event-field-module>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-6 col-md-9">
                        <text-field-input-element :disabled="(eventId > 0)" :definition="occurrenceFieldDefinitions['eventremarks']" label="Event Remarks" :maxlength="eventFields['eventremarks'] ? eventFields['eventremarks']['length'] : 0" :value="eventData.eventremarks" @update:value="(value) => updateCollectingEventData('eventremarks', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :disabled="(eventId > 0)" data-type="int" :definition="occurrenceFieldDefinitions['repcount']" label="Rep Count" :maxlength="eventFields['repcount'] ? eventFields['repcount']['length'] : 0" :value="eventData.repcount" @update:value="(value) => updateCollectingEventData('repcount', value)"></text-field-input-element>
                    </div>
                </div>
            </q-card-section>
        </q-card>
        <template v-if="showEventEditorPopup">
            <occurrence-collecting-event-editor-popup
                    :show-popup="showEventEditorPopup"
                    @close:popup="closeEventEditorPopup();"
            ></occurrence-collecting-event-editor-popup>
        </template>
    `,
    components: {
        'collecting-event-field-module': collectingEventFieldModule,
        'occurrence-collecting-event-editor-popup': occurrenceCollectingEventEditorPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const additionalDataFields = Vue.computed(() => occurrenceStore.getAdditionalDataFields);
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const eventFields = Vue.computed(() => occurrenceStore.getCollectingEventFields);
        const eventId = Vue.computed(() => occurrenceStore.getCollectingEventID);
        const eventValid = Vue.computed(() => occurrenceStore.getCollectingEventValid);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showEventEditorPopup = Vue.ref(false);

        function closeEventEditorPopup() {
            showEventEditorPopup.value = false;
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

        function updateCollectingEventData(key, value) {
            occurrenceStore.updateCollectingEventEditData(key, value);
        }

        Vue.onMounted(() => {
            occurrenceStore.setCollectingEventFields();
        });

        return {
            additionalDataFields,
            eventData,
            eventFields,
            eventId,
            eventValid,
            occId,
            occurrenceEntryFormat,
            occurrenceFieldDefinitions,
            showEventEditorPopup,
            closeEventEditorPopup,
            createCollectingEventRecord,
            updateCollectingEventData
        }
    }
};
