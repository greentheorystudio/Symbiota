const occurrenceEditorFormCurationElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-6 col-md-6">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['occurrenceid']" label="Occurrence ID" :maxlength="occurrenceFields['occurrenceid'] ? occurrenceFields['occurrenceid']['length'] : 0" :value="occurrenceData.occurrenceid" @update:value="(value) => updateOccurrenceData('occurrenceid', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['language']" label="Language" :maxlength="occurrenceFields['language'] ? occurrenceFields['language']['length'] : 0" :value="occurrenceData.language" @update:value="(value) => updateOccurrenceData('language', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <occurrence-processing-status-selector :definition="occurrenceFieldDefinitions['processingstatus']" label="Processing Status" :value="occurrenceData.processingstatus" @update:value="(value) => updateOccurrenceData('processingstatus', value)"></occurrence-processing-status-selector>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-6 col-md-5">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['preparations']" label="Preparations" :maxlength="occurrenceFields['preparations'] ? occurrenceFields['preparations']['length'] : 0" :value="occurrenceData.preparations" @update:value="(value) => updateOccurrenceData('preparations', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['disposition']" label="Disposition" :maxlength="occurrenceFields['disposition'] ? occurrenceFields['disposition']['length'] : 0" :value="occurrenceData.disposition" @update:value="(value) => updateOccurrenceData('disposition', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['duplicatequantity']" label="Duplicate Quantity" :maxlength="occurrenceFields['duplicatequantity'] ? occurrenceFields['duplicatequantity']['length'] : 0" :value="occurrenceData.duplicatequantity" @update:value="(value) => updateOccurrenceData('duplicatequantity', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-6 col-md-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['institutioncode']" label="Institution Code" :maxlength="occurrenceFields['institutioncode'] ? occurrenceFields['institutioncode']['length'] : 0" :value="occurrenceData.institutioncode" @update:value="(value) => updateOccurrenceData('institutioncode', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['collectioncode']" label="Collection Code" :maxlength="occurrenceFields['collectioncode'] ? occurrenceFields['collectioncode']['length'] : 0" :value="occurrenceData.collectioncode" @update:value="(value) => updateOccurrenceData('collectioncode', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['ownerinstitutioncode']" label="Owner Institution Code" :maxlength="occurrenceFields['ownerinstitutioncode'] ? occurrenceFields['ownerinstitutioncode']['length'] : 0" :value="occurrenceData.ownerinstitutioncode" @update:value="(value) => updateOccurrenceData('ownerinstitutioncode', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row">
                    <div class="col-grow">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['datageneralizations']" label="Data Generalizations" :maxlength="occurrenceFields['datageneralizations'] ? occurrenceFields['datageneralizations']['length'] : 0" :value="occurrenceData.datageneralizations" @update:value="(value) => updateOccurrenceData('datageneralizations', value)"></text-field-input-element>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'occurrence-processing-status-selector': occurrenceProcessingStatusSelector,
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
