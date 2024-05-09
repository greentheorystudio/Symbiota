const occurrenceEditorFormIdentifierElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['catalognumber']" label="Catalog Number" :maxlength="occurrenceFields['catalognumber'] ? occurrenceFields['catalognumber']['length'] : 0" :value="occurrenceData.catalognumber" @update:value="(value) => updateOccurrenceData('catalognumber', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-9">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['othercatalognumbers']" label="Other Catalog Numbers" :maxlength="occurrenceFields['othercatalognumbers'] ? occurrenceFields['othercatalognumbers']['length'] : 0" :value="occurrenceData.othercatalognumbers" @update:value="(value) => updateOccurrenceData('othercatalognumbers', value)"></text-field-input-element>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'text-field-input-element': textFieldInputElement
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
