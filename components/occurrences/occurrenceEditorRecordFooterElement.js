const occurrenceEditorRecordFooterElement = {
    template: `
        <div class="q-mt-sm q-px-sm row justify-between">
            <div v-if="Number(occurrenceData.occid) > 0" class="row q-col-gutter-sm">
                <div class="row q-col-gutter-xs">
                    <div class="text-bold">Record Id:</div><div>{{ occurrenceData.occid }}</div>
                </div>
                <div v-if="Number(eventId) > 0" class="row q-col-gutter-xs">
                    <div class="text-bold">Event Id:</div><div>{{ eventId }}</div>
                </div>
                <div v-if="Number(locationId) > 0" class="row q-col-gutter-xs">
                    <div class="text-bold">Location Id:</div><div>{{ locationId }}</div>
                </div>
            </div>
            <div>
                <template v-if="occurrenceData.datelastmodified">
                    Modified: {{ occurrenceData.datelastmodified }}
                </template>
            </div>
            <div>
                <template v-if="occurrenceData.recordenteredby || occurrenceData.dateentered">
                    <span v-if="occurrenceData.recordenteredby">
                        Entered by: {{ occurrenceData.recordenteredby + ' ' }}
                    </span>
                    <span v-if="occurrenceData.dateentered">
                        {{ '[' + occurrenceData.dateentered + ']' }}
                    </span>
                </template>
            </div>
        </div>
    `,
    setup() {
        const occurrenceStore = useOccurrenceStore();

        const eventId = Vue.computed(() => occurrenceStore.getCollectingEventID);
        const locationId = Vue.computed(() => occurrenceStore.getLocationID);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);

        return {
            eventId,
            locationId,
            occurrenceData
        }
    }
};
