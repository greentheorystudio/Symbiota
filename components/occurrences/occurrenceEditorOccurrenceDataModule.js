const occurrenceEditorOccurrenceDataModule = {
    template: `
        <div class="column q-gutter-y-sm">
            <template v-if="occurrenceEntryFormat === 'benthic' || occurrenceEntryFormat === 'lot'">
                <occurrence-editor-location-module></occurrence-editor-location-module>
                <template v-if="locationId > 0">
                    <occurrence-editor-collecting-event-module></occurrence-editor-collecting-event-module>
                    <template v-if="eventId > 0 && (occurrenceEntryFormat === 'lot' || occId > 0)">
                        <q-card flat bordered class="black-border">
                            <q-card-section class="q-pa-sm column q-gutter-y-sm">
                                <occurrence-editor-occurrence-data-controls></occurrence-editor-occurrence-data-controls>
                                <occurrence-editor-form-latest-identifier-element></occurrence-editor-form-latest-identifier-element>
                                <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                                <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                                <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                                <occurrence-editor-record-footer-element></occurrence-editor-record-footer-element>
                            </q-card-section>
                        </q-card>
                    </template>
                </template>
            </template>
            <template v-else>
                <q-card flat bordered class="black-border">
                    <q-card-section class="q-px-sm q-pb-sm column q-gutter-y-sm">
                        <occurrence-editor-occurrence-data-controls></occurrence-editor-occurrence-data-controls>
                        <occurrence-editor-form-latest-identifier-element></occurrence-editor-form-latest-identifier-element>
                        <occurrence-editor-form-collecting-event-element></occurrence-editor-form-collecting-event-element>
                        <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                        <occurrence-editor-form-location-element></occurrence-editor-form-location-element>
                        <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                        <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                        <occurrence-editor-record-footer-element></occurrence-editor-record-footer-element>
                    </q-card-section>
                </q-card>
            </template>
        </div>
    `,
    components: {
        'occurrence-editor-form-collecting-event-element': occurrenceEditorFormCollectingEventElement,
        'occurrence-editor-form-curation-element': occurrenceEditorFormCurationElement,
        'occurrence-editor-form-latest-identification-element': occurrenceEditorFormLatestIdentificationElement,
        'occurrence-editor-form-latest-identifier-element': occurrenceEditorFormIdentifierElement,
        'occurrence-editor-form-location-element': occurrenceEditorFormLocationElement,
        'occurrence-editor-form-misc-element': occurrenceEditorFormMiscElement,
        'occurrence-editor-collecting-event-module': occurrenceEditorCollectingEventModule,
        'occurrence-editor-location-module': occurrenceEditorLocationModule,
        'occurrence-editor-occurrence-data-controls': occurrenceEditorOccurrenceDataControls,
        'occurrence-editor-record-footer-element': occurrenceEditorRecordFooterElement
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const additionalDataFields = Vue.computed(() => occurrenceStore.getAdditionalDataFields);
        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const eventId = Vue.computed(() => occurrenceStore.getEventID);
        const locationId = Vue.computed(() => occurrenceStore.getLocationID);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);

        return {
            additionalDataFields,
            entryFollowUpAction,
            eventId,
            locationId,
            occId,
            occurrenceEntryFormat
        }
    }
};
