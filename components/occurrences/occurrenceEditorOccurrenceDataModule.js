const occurrenceEditorOccurrenceDataModule = {
    template: `
        <div>
            <template v-if="occurrenceEntryFormat === 'benthic' || occurrenceEntryFormat === 'lot'">
                <div class="q-pa-md column q-gutter-md">
                    <div>
                        <div class="row justify-between q-col-gutter-sm">
                            <div class="col-12" :class="imageCount > 0 ? 'col-lg-8' : null">
                                <occurrence-editor-location-module></occurrence-editor-location-module>
                            </div>
                            <div v-if="imageCount > 0" class="gt-md col-4">
                                <occurrence-editor-image-preview-carousel></occurrence-editor-image-preview-carousel>
                            </div>
                        </div>
                    </div>
                    <template v-if="locationId > 0">
                        <occurrence-editor-collecting-event-module></occurrence-editor-collecting-event-module>
                        <template v-if="eventId > 0 && (occurrenceEntryFormat === 'lot' || occId > 0)">
                            <q-card flat bordered class="black-border">
                                <q-card-section class="q-pa-sm q-pt-md column q-gutter-y-md">
                                    <occurrence-editor-occurrence-data-controls></occurrence-editor-occurrence-data-controls>
                                    <occurrence-editor-form-identifier-element></occurrence-editor-form-identifier-element>
                                    <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                                    <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                                    <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                                    <occurrence-editor-record-footer-element></occurrence-editor-record-footer-element>
                                </q-card-section>
                            </q-card>
                        </template>
                    </template>
                </div>
            </template>
            <template v-else>
                <q-card flat>
                    <q-card-section class="column q-gutter-y-md">
                        <occurrence-editor-occurrence-data-controls></occurrence-editor-occurrence-data-controls>
                        <div class="q-mt-xs row justify-between q-col-gutter-md">
                            <div class="col-12 column q-gutter-y-md" :class="imageCount > 0 ? 'col-lg-8' : null">
                                <occurrence-editor-form-identifier-element></occurrence-editor-form-identifier-element>
                                <occurrence-editor-form-collecting-event-element></occurrence-editor-form-collecting-event-element>
                            </div>
                            <div v-if="imageCount > 0" class="gt-md col-4">
                                <occurrence-editor-image-preview-carousel></occurrence-editor-image-preview-carousel>
                            </div>
                        </div>
                        <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                        <occurrence-editor-form-location-element></occurrence-editor-form-location-element>
                        <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                        <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                        <template v-if="occId > 0">
                            <occurrence-editor-record-footer-element></occurrence-editor-record-footer-element>
                        </template>
                    </q-card-section>
                </q-card>
            </template>
        </div>
    `,
    components: {
        'occurrence-editor-form-collecting-event-element': occurrenceEditorFormCollectingEventElement,
        'occurrence-editor-form-curation-element': occurrenceEditorFormCurationElement,
        'occurrence-editor-form-identifier-element': occurrenceEditorFormIdentifierElement,
        'occurrence-editor-form-latest-identification-element': occurrenceEditorFormLatestIdentificationElement,
        'occurrence-editor-form-location-element': occurrenceEditorFormLocationElement,
        'occurrence-editor-form-misc-element': occurrenceEditorFormMiscElement,
        'occurrence-editor-collecting-event-module': occurrenceEditorCollectingEventModule,
        'occurrence-editor-image-preview-carousel': occurrenceEditorImagePreviewCarousel,
        'occurrence-editor-location-module': occurrenceEditorLocationModule,
        'occurrence-editor-occurrence-data-controls': occurrenceEditorOccurrenceDataControls,
        'occurrence-editor-record-footer-element': occurrenceEditorRecordFooterElement
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const eventId = Vue.computed(() => occurrenceStore.getCollectingEventID);
        const imageCount = Vue.computed(() => occurrenceStore.getImageCount);
        const locationId = Vue.computed(() => occurrenceStore.getLocationID);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);

        return {
            entryFollowUpAction,
            eventId,
            imageCount,
            locationId,
            occId,
            occurrenceEntryFormat
        }
    }
};
