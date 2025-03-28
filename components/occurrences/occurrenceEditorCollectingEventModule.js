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
                            <template v-if="occurrenceEntryFormat === 'benthic' && Number(eventData.repcount) > 0">
                                <template v-if="collectingEventBenthicTaxaCnt > 0">
                                    <q-btn color="secondary" @click="showBenthicTaxaListPopup = true" label="View Taxa" />
                                </template>
                                <q-btn color="secondary" @click="showBenthicTaxaEditorPopup = true" label="Add Taxa" />
                            </template>
                            <template v-else-if="collectingEventCollectionArr.length > 0">
                                <q-btn color="secondary" @click="showCollectionListPopup = true" label="View Collections" />
                            </template>
                        </template>
                        <template v-if="Object.keys(configuredDataFields).length > 0">
                            <q-btn color="secondary" @click="showConfiguredDataEditorPopup = true" :label="configuredDataLabel" />
                        </template>
                        <template v-if="Number(eventId) === 0">
                            <q-btn color="secondary" @click="createCollectingEventRecord();" label="Create Event Record" :disabled="!eventValid" />
                        </template>
                        <template v-else>
                            <q-btn color="secondary" @click="processOpenEditor" label="Edit Event" />
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
        <template v-if="showBenthicTaxaListPopup">
            <occurrence-collecting-event-benthic-taxa-list-popup
                :show-popup="showBenthicTaxaListPopup"
                @update:edit-taxon="processEditTaxonRequest"
                @close:popup="showBenthicTaxaListPopup = false"
            ></occurrence-collecting-event-benthic-taxa-list-popup>
        </template>
        <template v-if="showBenthicTaxaEditorPopup">
            <occurrence-collecting-event-benthic-taxa-editor-popup
                :edit-taxon="editTaxonPopupTaxonData"
                :show-popup="showBenthicTaxaEditorPopup"
                @close:popup="closeBenthicTaxaEditorPopup();"
            ></occurrence-collecting-event-benthic-taxa-list-popup>
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
        'occurrence-collecting-event-benthic-taxa-editor-popup': occurrenceCollectingEventBenthicTaxaEditorPopup,
        'occurrence-collecting-event-benthic-taxa-list-popup': occurrenceCollectingEventBenthicTaxaListPopup,
        'occurrence-collecting-event-editor-popup': occurrenceCollectingEventEditorPopup,
        'occurrence-collection-list-popup': occurrenceCollectionListPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const collectingEventBenthicData = Vue.computed(() => occurrenceStore.getCollectingEventBenthicData);
        const collectingEventBenthicTaxaCnt = Vue.computed(() => occurrenceStore.getCollectingEventBenthicTaxaCnt);
        const collectingEventCollectionArr = Vue.computed(() => occurrenceStore.getCollectingEventCollectionArr);
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
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showBenthicTaxaEditorPopup = Vue.ref(false);
        const showBenthicTaxaListPopup = Vue.ref(false);
        const showCollectionListPopup = Vue.ref(false);
        const showConfiguredDataEditorPopup = Vue.ref(false);
        const showEventEditorPopup = Vue.ref(false);

        function closeBenthicTaxaEditorPopup() {
            editTaxonPopupTaxonData.value = null;
            showBenthicTaxaEditorPopup.value = false;
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
            showBenthicTaxaListPopup.value = false;
            showBenthicTaxaEditorPopup.value = true;
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
            collectingEventBenthicData,
            collectingEventBenthicTaxaCnt,
            collectingEventCollectionArr,
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
            showBenthicTaxaEditorPopup,
            showBenthicTaxaListPopup,
            showCollectionListPopup,
            showConfiguredDataEditorPopup,
            showEventEditorPopup,
            closeBenthicTaxaEditorPopup,
            createCollectingEventRecord,
            processEditTaxonRequest,
            processOpenEditor,
            updateCollectingEventData
        }
    }
};
