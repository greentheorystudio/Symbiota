const occurrenceEditorFormCollectingEventElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                
                <template v-if="showExtendedForm">
                    
                </template>
            </q-card-section>
        </q-card>
    `,
    components: {
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { getCoordinateVerificationData, showAlert, showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showExtendedForm = Vue.ref(false);

        return {
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showExtendedForm
        }
    }
};
