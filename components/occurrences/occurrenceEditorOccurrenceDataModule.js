const occurrenceEditorOccurrenceDataModule = {
    template: `
        <div class="column q-gutter-y-sm">
            <template v-if="occurrenceEntryFormat === 'benthic' || occurrenceEntryFormat === 'lot'">
                <occurrence-editor-location-module></occurrence-editor-location-module>
                <occurrence-editor-collecting-event-module></occurrence-editor-collecting-event-module>
                <template v-if="occurrenceEntryFormat === 'lot' || occId > 0">
                    <div class="rounded-borders black-border q-pa-sm column q-gutter-y-sm">
                        <occurrence-editor-occurrence-data-controls></occurrence-editor-occurrence-data-controls>
                        <occurrence-editor-form-latest-identifier-element></occurrence-editor-form-latest-identifier-element>
                        <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                        <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                        <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                        <occurrence-editor-record-footer-element></occurrence-editor-record-footer-element>
                    </div>
                </template>
            </template>
            <template v-else>
                <div class="rounded-borders black-border q-px-sm q-pb-sm column q-gutter-y-sm">
                    <occurrence-editor-occurrence-data-controls></occurrence-editor-occurrence-data-controls>
                    <occurrence-editor-form-latest-identifier-element></occurrence-editor-form-latest-identifier-element>
                    <occurrence-editor-form-collecting-event-element></occurrence-editor-form-collecting-event-element>
                    <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                    <occurrence-editor-form-location-element></occurrence-editor-form-location-element>
                    <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                    <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                    <occurrence-editor-record-footer-element></occurrence-editor-record-footer-element>
                </div>
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
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);

        return {
            additionalDataFields,
            entryFollowUpAction,
            occId,
            occurrenceEntryFormat
        }
    }
};
