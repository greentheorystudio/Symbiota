const occurrenceEditorFormCollectingEventElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <template v-if="!collectionEventAutoSearch || (occId > 0 && Object.keys(configuredDataFields).length > 0)">
                    <div class="row justify-between">
                        <div class="text-grey-8 text-h6 text-weight-bolder q-pl-sm">
                            Collecting Event
                        </div>
                        <div class="row justify-end q-gutter-sm">
                            <div v-if="occId > 0 && Object.keys(configuredDataFields).length > 0">
                                <q-btn color="secondary" @click="showConfiguredDataEditorPopup = true" :label="configuredDataLabel" tabindex="0" />
                            </div>
                            <div v-if="!collectionEventAutoSearch">
                                <q-btn color="secondary" size="md" @click="processCollectingEventSearch(false);" label="Search for Event" tabindex="0" />
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                        Collecting Event
                    </div>
                </template>
                <collecting-event-field-module
                    :auto-search="collectionEventAutoSearch"
                    :data="occurrenceData"
                    :fields="occurrenceFields"
                    :field-definitions="occurrenceFieldDefinitions"
                    @process-event-search="processCollectingEventSearch"
                    @update:collecting-event-data="(data) => updateOccurrenceData(data.key, data.value)"
                ></collecting-event-field-module>
            </q-card-section>
        </q-card>
        <template v-if="showCollectingEventListPopup">
            <occurrence-collecting-event-list-popup
                :event-arr="collectingEventArr"
                :show-popup="showCollectingEventListPopup"
                @merge:event="processMergeEventData"
                @close:popup="closeCollectingEventListPopup();"
            ></occurrence-collecting-event-list-popup>
        </template>
        <template v-if="showConfiguredDataEditorPopup">
            <mof-data-editor-popup
                data-type="event"
                :new-record="Number(eventId) === 0"
                :show-popup="showConfiguredDataEditorPopup"
                @close:popup="showConfiguredDataEditorPopup = false"
            ></mof-data-editor-popup>
        </template>
    `,
    components: {
        'collecting-event-field-module': collectingEventFieldModule,
        'mof-data-editor-popup': mofDataEditorPopup,
        'occurrence-collecting-event-list-popup': occurrenceCollectingEventListPopup
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const collectingEventArr = Vue.ref([]);
        const collectionEventAutoSearch = Vue.computed(() => occurrenceStore.getCollectingEventAutoSearch);
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const configuredDataFields = Vue.computed(() => occurrenceStore.getEventMofDataFields);
        const configuredDataLabel = Vue.computed(() => occurrenceStore.getEventMofDataLabel);
        const eventId = Vue.computed(() => occurrenceStore.getCollectingEventID);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showCollectingEventListPopup = Vue.ref(false);
        const showConfiguredDataEditorPopup = Vue.ref(false);

        function closeCollectingEventListPopup() {
            showCollectingEventListPopup.value = false;
            collectingEventArr.value = [];
        }

        function processCollectingEventSearch(silent = true) {
            if(occurrenceData.value.recordedby && ((occurrenceData.value.recordnumber && !isNaN(occurrenceData.value.recordnumber)) || occurrenceData.value.eventdate)){
                const searchTermsArr = {
                    db: [collId.value],
                    collector: occurrenceData.value.recordedby,
                    collnum: occurrenceData.value.recordnumber,
                    eventdate1: occurrenceData.value.eventdate
                };
                const options = {
                    schema: 'occurrence',
                    output: 'json'
                };
                searchStore.processSimpleSearch(searchTermsArr, options, (data) => {
                    const returnData = [];
                    data.forEach(record => {
                        if(Number(record.occid) !== Number(occId.value)){
                            returnData.push(record);
                        }
                    });
                    if(returnData.length > 0){
                        collectingEventArr.value = returnData.slice();
                        showCollectingEventListPopup.value = true;
                    }
                    else{
                        showNotification('negative', 'There were no events found matching this data.');
                    }
                });
            }
            else if(!silent){
                showNotification('negative', 'To search for the event, the collector/observer, number, and date values must be entered.');
            }
        }

        function processMergeEventData(data) {
            occurrenceStore.mergeSelectedEventOccurrenceData(data.event, data.missing);
        }

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
            if(collectionEventAutoSearch.value && (key === 'recordedby' || key === 'recordnumber')){
                processCollectingEventSearch();
            }
        }

        function updateOccurrenceDateData(data) {
            updateOccurrenceData('eventdate', data['date']);
            updateOccurrenceData('year', data['year']);
            updateOccurrenceData('month', data['month']);
            updateOccurrenceData('day', data['day']);
            updateOccurrenceData('startdayofyear', data['startDayOfYear']);
            updateOccurrenceData('enddayofyear', data['endDayOfYear']);
            if(collectionEventAutoSearch.value){
                processCollectingEventSearch();
            }
        }

        return {
            collectingEventArr,
            collectionEventAutoSearch,
            configuredDataFields,
            configuredDataLabel,
            eventId,
            occId,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showCollectingEventListPopup,
            showConfiguredDataEditorPopup,
            closeCollectingEventListPopup,
            processCollectingEventSearch,
            processMergeEventData,
            updateOccurrenceData,
            updateOccurrenceDateData
        }
    }
};
