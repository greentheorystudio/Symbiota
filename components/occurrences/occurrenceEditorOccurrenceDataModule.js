const occurrenceEditorOccurrenceDataModule = {
    template: `
        <div class="column q-gutter-sm">
            <q-card flat bordered>
                <q-card-section class="q-pa-sm row justify-between">
                    <div>
                        <template v-if="Number(occId) === 0">
                            <occurrence-entry-follow-up-action-selector :selected-action="entryFollowUpAction" @change-occurrence-entry-follow-up-action="changeEntryFollowUpAction"></occurrence-entry-follow-up-action-selector>
                        </template>
                    </div>
                    <div class="row justify-end">
                        <template v-if="Number(occId) === 0">
                            <q-btn color="secondary" @click="createOccurrenceRecord();" label="Add Record" />
                        </template>
                        <template v-else>
                            <template v-if="!editingActivated">
                                <q-btn color="green" @click="editingActivated = true" label="Edit Record" />
                            </template>
                            <template v-else>
                                <q-btn color="red" @click="editingActivated = false" label="Stop Editing" />
                            </template>
                        </template>
                    </div>
                </q-card-section>
            </q-card>
            <template v-if="additionalDataFields.length > 0 || occurrenceEntryFormat === 'benthic'">
                <occurrence-editor-location-module></occurrence-editor-location-module>
                <occurrence-editor-collecting-event-module></occurrence-editor-collecting-event-module>
                <template v-if="occurrenceEntryFormat !== 'benthic' || occId > 0">
                    <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                    <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                    <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
                </template>
            </template>
            <template v-else>
                <occurrence-editor-form-collecting-event-element></occurrence-editor-form-collecting-event-element>
                <occurrence-editor-form-latest-identification-element></occurrence-editor-form-latest-identification-element>
                <occurrence-editor-form-location-element></occurrence-editor-form-location-element>
                <occurrence-editor-form-misc-element></occurrence-editor-form-misc-element>
                <occurrence-editor-form-curation-element></occurrence-editor-form-curation-element>
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
        'occurrence-entry-follow-up-action-selector': occurrenceEntryFollowUpActionSelector
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

        function changeEntryFollowUpAction(value) {
            occurrenceStore.setEntryFollowUpAction(value);
        }

        Vue.onMounted(() => {
            if(occId.value){
                if(Number(occId.value) > 0){
                    if(entryFollowUpAction.value === 'remain'){
                        editingActivated.value = true;
                    }
                    occurrenceStore.setEntryFollowUpAction('none');
                }
                else{
                    editingActivated.value = true;
                    occurrenceStore.setEntryFollowUpAction('remain');
                }
            }
        });

        return {
            additionalDataFields,
            editingActivated,
            entryFollowUpAction,
            occId,
            occurrenceEntryFormat,
            changeEntryFollowUpAction
        }
    }
};
