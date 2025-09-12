const occurrenceEditorFormLatestIdentificationElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                    Determination
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-4">
                        <single-scientific-common-name-auto-complete :disabled="occurrenceEntryFormat === 'benthic'" :definition="occurrenceFieldDefinitions['sciname']" :sciname="occurrenceData.sciname" label="Scientific Name" @update:sciname="updateScientificNameValue"></single-scientific-common-name-auto-complete>
                    </div>
                    <div class="col-12 col-sm-3">
                        <text-field-input-element :disabled="occurrenceEntryFormat === 'benthic'" :definition="occurrenceFieldDefinitions['scientificnameauthorship']" label="Author" :maxlength="occurrenceFields['scientificnameauthorship'] ? occurrenceFields['scientificnameauthorship']['length'] : 0" :value="((Number(occurrenceData.tid) > 0 && occurrenceData.hasOwnProperty('taxonData')) ? occurrenceData['taxonData'].author : occurrenceData.scientificnameauthorship)" @update:value="(value) => updateOccurrenceData('scientificnameauthorship', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-2">
                        <text-field-input-element :disabled="occurrenceEntryFormat === 'benthic'" :definition="occurrenceFieldDefinitions['identificationqualifier']" label="ID Qualifier" :maxlength="occurrenceFields['identificationqualifier'] ? occurrenceFields['identificationqualifier']['length'] : 0" :value="occurrenceData.identificationqualifier" @update:value="(value) => updateOccurrenceData('identificationqualifier', value)"></text-field-input-element>
                    </div>
                    <div class="col-12 col-sm-3">
                        <text-field-input-element :disabled="occurrenceEntryFormat === 'benthic'" :definition="occurrenceFieldDefinitions['family']" label="Family" :maxlength="occurrenceFields['family'] ? occurrenceFields['family']['length'] : 0" :value="(Number(occurrenceData.tid) > 0 ? occurrenceData['taxonData'].family : occurrenceData.family)" @update:value="(value) => updateOccurrenceData('family', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-sm">
                    <div class="col-12 col-sm-7">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['identifiedby']" label="Identified By" :maxlength="occurrenceFields['identifiedby'] ? occurrenceFields['identifiedby']['length'] : 0" :value="occurrenceData.identifiedby" @update:value="(value) => updateOccurrenceData('identifiedby', value)"></text-field-input-element>
                    </div>
                    <div class="col-11 col-sm-4">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['dateidentified']" label="Date Identified" :maxlength="occurrenceFields['dateidentified'] ? occurrenceFields['dateidentified']['length'] : 0" :value="occurrenceData.dateidentified" @update:value="(value) => updateOccurrenceData('dateidentified', value)"></text-field-input-element>
                    </div>
                    <div class="col-1 row justify-end self-center">
                        <div>
                            <template v-if="showExtendedForm">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = false" icon="fas fa-minus" dense></q-btn>
                            </template>
                            <template v-else>
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="showExtendedForm = true" icon="fas fa-plus" dense></q-btn>
                            </template>
                        </div>
                    </div>
                </div>
                <template v-if="showExtendedForm">
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['verbatimscientificname']" label="Verbatim Scientific Name" :maxlength="occurrenceFields['verbatimscientificname'] ? occurrenceFields['verbatimscientificname']['length'] : 0" :value="occurrenceData.verbatimscientificname" @update:value="(value) => updateOccurrenceData('verbatimscientificname', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['identificationreferences']" label="ID References" :maxlength="occurrenceFields['identificationreferences'] ? occurrenceFields['identificationreferences']['length'] : 0" :value="occurrenceData.identificationreferences" @update:value="(value) => updateOccurrenceData('identificationreferences', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['identificationremarks']" label="ID Remarks" :maxlength="occurrenceFields['identificationremarks'] ? occurrenceFields['identificationremarks']['length'] : 0" :value="occurrenceData.identificationremarks" @update:value="(value) => updateOccurrenceData('identificationremarks', value)"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element :definition="occurrenceFieldDefinitions['taxonremarks']" label="Taxon Remarks" :maxlength="occurrenceFields['taxonremarks'] ? occurrenceFields['taxonremarks']['length'] : 0" :value="occurrenceData.taxonremarks" @update:value="(value) => updateOccurrenceData('taxonremarks', value)"></text-field-input-element>
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

        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
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
