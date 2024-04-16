const occurrenceEditorFormLocationElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column">
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['country']" label="Country" :maxlength="occurrenceFields['country'] ? occurrenceFields['country']['length'] : 0" :value="occurrenceData.country" @update:value="(value) => updateOccurrenceData('country', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['stateprovince']" label="State/Province" :maxlength="occurrenceFields['stateprovince'] ? occurrenceFields['stateprovince']['length'] : 0" :value="occurrenceData.stateprovince" @update:value="(value) => updateOccurrenceData('stateprovince', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['county']" label="County" :maxlength="occurrenceFields['county'] ? occurrenceFields['county']['length'] : 0" :value="occurrenceData.county" @update:value="(value) => updateOccurrenceData('county', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['municipality']" label="Municipality" :maxlength="occurrenceFields['municipality'] ? occurrenceFields['municipality']['length'] : 0" :value="occurrenceData.municipality" @update:value="(value) => updateOccurrenceData('municipality', value)"></text-field-input-element>
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
