const occurrenceEditorFormCollectingEventElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="full-width row justify-between">
                    <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                        Collecting Event
                    </div>
                    <div v-if="Object.keys(configuredEventMofDataFields).length > 0">
                        <q-btn color="secondary" @click="showConfiguredDataEditorPopup = true" :label="configuredDataLabel" />
                    </div>
                </div>
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
                @close:popup="closeCollectingEventListPopup();"
            ></occurrence-collecting-event-list-popup>
        </template>
        <template v-if="showConfiguredDataEditorPopup">
            <event-mof-data-editor-popup
                :show-popup="showConfiguredDataEditorPopup"
                @close:popup="showConfiguredDataEditorPopup = false"
            ></event-mof-data-editor-popup>
        </template>
    `,
    components: {
        'collecting-event-field-module': collectingEventFieldModule,
        'event-mof-data-editor-popup': eventMofDataEditorPopup,
        'occurrence-collecting-event-list-popup': occurrenceCollectingEventListPopup
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const collectingEventArr = Vue.ref([]);
        const collectionEventAutoSearch = Vue.computed(() => occurrenceStore.getCollectingEventAutoSearch);
        const configuredDataLabel = Vue.computed(() => occurrenceStore.getEventMofDataLabel);
        const configuredEventMofDataFields = Vue.computed(() => occurrenceStore.getEventMofDataFields);
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
                occurrenceStore.getOccurrenceCollectingEvents((listArr) => {
                    if(listArr.length > 0){
                        collectingEventArr.value = listArr;
                        showCollectingEventListPopup.value = true;
                    }
                    else{
                        showNotification('negative', 'There were no probable events found matching this data.');
                    }
                });
            }
            else if(!silent){
                showNotification('negative', 'To search for the event a collector/observer value must be entered, as well as a numeric number value or a date.');
            }
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
            configuredDataLabel,
            configuredEventMofDataFields,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showCollectingEventListPopup,
            showConfiguredDataEditorPopup,
            closeCollectingEventListPopup,
            processCollectingEventSearch,
            updateOccurrenceData,
            updateOccurrenceDateData
        }
    }
};
