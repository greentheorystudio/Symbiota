const occurrenceEditorFormCurationElement = {
    template: `
        <q-card v-if="showElement" flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                    Curation
                </div>
                <div v-if="!editorHideFields.includes('basisofrecord') || !editorHideFields.includes('typestatus')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('basisofrecord')" class="col-12 col-sm-4">
                        <selector-input-element :definition="occurrenceFieldDefinitions['basisofrecord']" label="Basis of Record" :options="basisOfRecordOptions" :value="occurrenceData.basisofrecord" @update:value="(value) => updateOccurrenceData('basisofrecord', value)"></selector-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('typestatus')" class="col-12 col-sm-8">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['typestatus']" label="Type Status" :maxlength="occurrenceFields['typestatus'] ? occurrenceFields['typestatus']['length'] : 0" :value="occurrenceData.typestatus" @update:value="(value) => updateOccurrenceData('typestatus', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('occurrenceid') || !editorHideFields.includes('language') || !editorHideFields.includes('processingstatus')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('occurrenceid')" class="col-12 col-sm-6 col-md-6">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['occurrenceid']" label="Occurrence ID" :maxlength="occurrenceFields['occurrenceid'] ? occurrenceFields['occurrenceid']['length'] : 0" :value="occurrenceData.occurrenceid" @update:value="(value) => updateOccurrenceData('occurrenceid', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('language')" class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['language']" label="Language" :maxlength="occurrenceFields['language'] ? occurrenceFields['language']['length'] : 0" :value="occurrenceData.language" @update:value="(value) => updateOccurrenceData('language', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('processingstatus')" class="col-12 col-sm-6 col-md-3">
                        <selector-input-element :definition="occurrenceFieldDefinitions['processingstatus']" label="Processing Status" :options="processingStatusOptions" :value="occurrenceData.processingstatus" @update:value="(value) => updateOccurrenceData('processingstatus', value)"></selector-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('preparations') || !editorHideFields.includes('disposition') || !editorHideFields.includes('duplicatequantity')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('preparations')" class="col-12 col-sm-6 col-md-5">
                        <template v-if="controlledVocabularies.hasOwnProperty('preparations') && controlledVocabularies['preparations'] && controlledVocabularies['preparations'].length > 0 && (!occurrenceData.preparations || controlledVocabularies['preparations'].includes(occurrenceData.preparations))">
                            <selector-input-element :definition="occurrenceFieldDefinitions['preparations']" label="Preparations" :options="controlledVocabularies['preparations']" :value="occurrenceData.preparations" @update:value="(value) => updateOccurrenceData('preparations', value)" :clearable="true"></selector-input-element>
                        </template>
                        <template v-else>
                            <text-field-input-element :definition="occurrenceFieldDefinitions['preparations']" label="Preparations" :maxlength="occurrenceFields['preparations'] ? occurrenceFields['preparations']['length'] : 0" :value="occurrenceData.preparations" @update:value="(value) => updateOccurrenceData('preparations', value)"></text-field-input-element>
                        </template>
                    </div>
                    <div v-if="!editorHideFields.includes('disposition')" class="col-12 col-sm-6 col-md-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['disposition']" label="Disposition" :maxlength="occurrenceFields['disposition'] ? occurrenceFields['disposition']['length'] : 0" :value="occurrenceData.disposition" @update:value="(value) => updateOccurrenceData('disposition', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('duplicatequantity')" class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['duplicatequantity']" label="Duplicate Quantity" :maxlength="occurrenceFields['duplicatequantity'] ? occurrenceFields['duplicatequantity']['length'] : 0" :value="occurrenceData.duplicatequantity" @update:value="(value) => updateOccurrenceData('duplicatequantity', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('institutioncode') || !editorHideFields.includes('collectioncode') || !editorHideFields.includes('ownerinstitutioncode')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('institutioncode')" class="col-12 col-sm-6 col-md-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['institutioncode']" label="Institution Code" :maxlength="occurrenceFields['institutioncode'] ? occurrenceFields['institutioncode']['length'] : 0" :value="occurrenceData.institutioncode" @update:value="(value) => updateOccurrenceData('institutioncode', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('collectioncode')" class="col-12 col-sm-6 col-md-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['collectioncode']" label="Collection Code" :maxlength="occurrenceFields['collectioncode'] ? occurrenceFields['collectioncode']['length'] : 0" :value="occurrenceData.collectioncode" @update:value="(value) => updateOccurrenceData('collectioncode', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('ownerinstitutioncode')" class="col-12 col-sm-6 col-md-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['ownerinstitutioncode']" label="Owner Institution Code" :maxlength="occurrenceFields['ownerinstitutioncode'] ? occurrenceFields['ownerinstitutioncode']['length'] : 0" :value="occurrenceData.ownerinstitutioncode" @update:value="(value) => updateOccurrenceData('ownerinstitutioncode', value)"></text-field-input-element>
                    </div>
                </div>
                <div v-if="!editorHideFields.includes('datageneralizations')" class="row">
                    <div class="col-grow">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['datageneralizations']" label="Data Generalizations" :maxlength="occurrenceFields['datageneralizations'] ? occurrenceFields['datageneralizations']['length'] : 0" :value="occurrenceData.datageneralizations" @update:value="(value) => updateOccurrenceData('datageneralizations', value)"></text-field-input-element>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const baseStore = useBaseStore();
        const occurrenceStore = useOccurrenceStore();

        const basisOfRecordOptions = Vue.computed(() => occurrenceStore.getBasisOfRecordOptions);
        const controlledVocabularies = Vue.computed(() => occurrenceStore.getOccurrenceFieldControlledVocabularies);
        const editorHideFields = Vue.computed(() => occurrenceStore.getEditorHideFields);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const processingStatusOptions = Vue.computed(() => baseStore.getOccurrenceProcessingStatusOptions);
        const showElement = Vue.computed(() => {
            return (
                !editorHideFields.value.includes('basisofrecord') ||
                !editorHideFields.value.includes('typestatus') ||
                !editorHideFields.value.includes('occurrenceid') ||
                !editorHideFields.value.includes('language') ||
                !editorHideFields.value.includes('processingstatus') ||
                !editorHideFields.value.includes('preparations') ||
                !editorHideFields.value.includes('disposition') ||
                !editorHideFields.value.includes('duplicatequantity') ||
                !editorHideFields.value.includes('institutioncode') ||
                !editorHideFields.value.includes('collectioncode') ||
                !editorHideFields.value.includes('ownerinstitutioncode') ||
                !editorHideFields.value.includes('datageneralizations')
            );
        });

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
        }

        return {
            basisOfRecordOptions,
            controlledVocabularies,
            editorHideFields,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            processingStatusOptions,
            showElement,
            updateOccurrenceData
        }
    }
};
