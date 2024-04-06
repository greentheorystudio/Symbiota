const occurrenceEditorOccurrenceDataModule = {
    template: `
        <div class="column q-gutter-sm">
            <template v-if="additionalDataFields.length > 0 || occurrenceEntryFormat === 'benthic'">
                <occurrence-editor-location-module></occurrence-editor-location-module>
                <occurrence-editor-collecting-event-module></occurrence-editor-collecting-event-module>
                <template v-if="occurrenceEntryFormat !== 'benthic' || occId > 0">
                    <div class="rounded-borders black-border q-pa-sm column q-gutter-sm">
                        <occurrence-editor-occurrence-data-controls :editing-activated="editingActivated"></occurrence-editor-occurrence-data-controls>
                        <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                        <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                        <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                    </div>
                </template>
            </template>
            <template v-else>
                <div class="rounded-borders black-border q-px-sm q-pb-sm column q-gutter-sm">
                    <occurrence-editor-occurrence-data-controls :editing-activated="editingActivated"></occurrence-editor-occurrence-data-controls>
                    <occurrence-editor-form-collecting-event-element></occurrence-editor-form-collecting-event-element>
                    <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                    <occurrence-editor-form-location-element :editing-activated="editingActivated"></occurrence-editor-form-location-element>
                    <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                    <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                </div>
            </template>
        </div>
    `,
    components: {
        'occurrence-editor-form-collecting-event-element': occurrenceEditorFormCollectingEventElement,
        'occurrence-editor-form-curation-element': occurrenceEditorFormCurationElement,
        'occurrence-editor-form-latest-identification-element': occurrenceEditorFormLatestIdentificationElement,
        'occurrence-editor-form-location-element': occurrenceEditorFormLocationElement,
        'occurrence-editor-form-misc-element': occurrenceEditorFormMiscElement,
        'occurrence-editor-collecting-event-module': occurrenceEditorCollectingEventModule,
        'occurrence-editor-location-module': occurrenceEditorLocationModule,
        'occurrence-editor-occurrence-data-controls': occurrenceEditorOccurrenceDataControls
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const additionalDataFields = Vue.computed(() => occurrenceStore.getAdditionalDataFields);
        const editingActivated = Vue.ref(false);
        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);

        Vue.watch(occId, () => {
            if(entryFollowUpAction.value !== 'remain' && Number(occId.value) > 0){
                editingActivated.value = false;
            }
        });

        function setEditingActivated(value) {
            editingActivated.value = value;
        }

        Vue.provide('setEditingActivated', setEditingActivated);

        return {
            additionalDataFields,
            editingActivated,
            entryFollowUpAction,
            occId,
            occurrenceEntryFormat
        }
    }
};
