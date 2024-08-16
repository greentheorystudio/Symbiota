const occurrenceEditorFormLocationElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                    Location
                </div>
                <location-field-module :data="occurrenceData" :fields="occurrenceFields" :field-definitions="occurrenceFieldDefinitions" @update:location-data="(data) => updateOccurrenceData(data.key, data.value)"></location-field-module>
            </q-card-section>
        </q-card>
    `,
    components: {
        'location-field-module': locationFieldModule
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
        }

        return {
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            updateOccurrenceData
        }
    }
};
