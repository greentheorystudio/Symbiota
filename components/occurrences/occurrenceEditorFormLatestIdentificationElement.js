const occurrenceEditorFormLatestIdentificationElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                    Determination
                </div>
                <div v-if="!editorHideFields.includes('sciname') || !editorHideFields.includes('scientificnameauthorship') || !editorHideFields.includes('identificationqualifier') || !editorHideFields.includes('family')" class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('sciname')" class="col-12 col-sm-4">
                        <single-scientific-common-name-auto-complete :disabled="occurrenceEntryFormat === 'replicate'" :definition="occurrenceFieldDefinitions['sciname']" :sciname="occurrenceData.sciname" label="Scientific Name" :limit-to-options="limitIdsToThesaurus" @update:sciname="updateScientificNameValue"></single-scientific-common-name-auto-complete>
                    </div>
                    <div v-if="!editorHideFields.includes('scientificnameauthorship')" class="col-12 col-sm-3">
                        <text-field-input-element :disabled="occurrenceEntryFormat === 'replicate'" :definition="occurrenceFieldDefinitions['scientificnameauthorship']" label="Author" :maxlength="occurrenceFields['scientificnameauthorship'] ? occurrenceFields['scientificnameauthorship']['length'] : 0" :value="occurrenceData.scientificnameauthorship" @update:value="(value) => updateOccurrenceData('scientificnameauthorship', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('identificationqualifier')" class="col-12 col-sm-2">
                        <text-field-input-element :disabled="occurrenceEntryFormat === 'replicate'" :definition="occurrenceFieldDefinitions['identificationqualifier']" label="ID Qualifier" field="identificationqualifier" :maxlength="occurrenceFields['identificationqualifier'] ? occurrenceFields['identificationqualifier']['length'] : 0" :value="occurrenceData.identificationqualifier" @update:value="(value) => updateOccurrenceData('identificationqualifier', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('family')" class="col-12 col-sm-3">
                        <text-field-input-element :disabled="occurrenceEntryFormat === 'replicate'" :definition="occurrenceFieldDefinitions['family']" label="Family" :maxlength="occurrenceFields['family'] ? occurrenceFields['family']['length'] : 0" :value="occurrenceData.family" @update:value="(value) => updateOccurrenceData('family', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div v-if="!editorHideFields.includes('identifiedby')" class="col-12 col-sm-7">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['identifiedby']" label="Identified By" field="identifiedby" :maxlength="occurrenceFields['identifiedby'] ? occurrenceFields['identifiedby']['length'] : 0" :value="occurrenceData.identifiedby" @update:value="(value) => updateOccurrenceData('identifiedby', value)"></text-field-input-element>
                    </div>
                    <div v-if="!editorHideFields.includes('dateidentified')" class="col-11 col-sm-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['dateidentified']" label="Date Identified" field="dateidentified" :maxlength="occurrenceFields['dateidentified'] ? occurrenceFields['dateidentified']['length'] : 0" :value="occurrenceData.dateidentified" @update:value="(value) => updateOccurrenceData('dateidentified', value)"></text-field-input-element>
                    </div>
                    <div class="col-1 row justify-end self-center">
                        <div>
                            <template v-if="showExtendedForm">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = false" icon="fas fa-minus" dense aria-label="Hide additional fields" tabindex="0"></q-btn>
                            </template>
                            <template v-else>
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = true" icon="fas fa-plus" dense aria-label="Show additional fields" tabindex="0"></q-btn>
                            </template>
                        </div>
                    </div>
                </div>
                <template v-if="showExtendedForm">
                    <div v-if="!editorHideFields.includes('verbatimscientificname')" class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimscientificname']" label="Verbatim Scientific Name" :maxlength="occurrenceFields['verbatimscientificname'] ? occurrenceFields['verbatimscientificname']['length'] : 0" :value="occurrenceData.verbatimscientificname" @update:value="(value) => updateOccurrenceData('verbatimscientificname', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div v-if="!editorHideFields.includes('identificationreferences')" class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['identificationreferences']" label="ID References" field="identificationreferences" :maxlength="occurrenceFields['identificationreferences'] ? occurrenceFields['identificationreferences']['length'] : 0" :value="occurrenceData.identificationreferences" @update:value="(value) => updateOccurrenceData('identificationreferences', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div v-if="!editorHideFields.includes('identificationremarks')" class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['identificationremarks']" label="ID Remarks" field="identificationremarks" :maxlength="occurrenceFields['identificationremarks'] ? occurrenceFields['identificationremarks']['length'] : 0" :value="occurrenceData.identificationremarks" @update:value="(value) => updateOccurrenceData('identificationremarks', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div v-if="!editorHideFields.includes('taxonremarks')" class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['taxonremarks']" label="Taxon Remarks" field="taxonremarks" :maxlength="occurrenceFields['taxonremarks'] ? occurrenceFields['taxonremarks']['length'] : 0" :value="occurrenceData.taxonremarks" @update:value="(value) => updateOccurrenceData('taxonremarks', value)"></text-field-input-element>
                        </div>
                    </div>
                </template>
            </q-card-section>
        </q-card>
    `,
    components: {
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const occurrenceStore = useOccurrenceStore();

        const editorHideFields = Vue.computed(() => occurrenceStore.getEditorHideFields);
        const limitIdsToThesaurus = Vue.computed(() => occurrenceStore.getLimitIdsToThesaurus);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceFields = Vue.computed(() => occurrenceStore.getOccurrenceFields);
        const occurrenceFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceFieldDefinitions);
        const showExtendedForm = Vue.ref(false);

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
        }

        function updateScientificNameValue(taxon) {
            if(taxon && Number(taxon['securitystatus']) === 1){
                occurrenceStore.updateOccurrenceEditData('localitysecurity', '1');
            }
            else if(Number(occurrenceData.value['localitysecurity']) && !occurrenceData.value['localitysecurityreason']){
                occurrenceStore.updateOccurrenceEditData('localitysecurity', '0');
            }
            occurrenceStore.updateOccurrenceEditDataTaxon(taxon);
        }

        return {
            editorHideFields,
            limitIdsToThesaurus,
            occurrenceData,
            occurrenceEntryFormat,
            occurrenceFields,
            occurrenceFieldDefinitions,
            showExtendedForm,
            updateOccurrenceData,
            updateScientificNameValue
        }
    }
};
