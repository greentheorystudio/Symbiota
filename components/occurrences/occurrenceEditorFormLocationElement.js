const occurrenceEditorFormLocationElement = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-xs">
                <div class="row justify-between q-col-gutter-md">
                    <div>
                        <checkbox-input-element :definition="occurrenceFieldDefinitions['localitysecurity']" label="Locality Security" :value="occurrenceData.localitysecurity" @update:value="updateLocalitySecuritySetting"></checkbox-input-element>
                    </div>
                    <div v-if="Number(occurrenceData.localitysecurity) === 1" class="col-12 col-sm-grow col-md-grow">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['localitysecurityreason']" label="Locality Security Reason" :maxlength="occurrenceFields['localitysecurityreason'] ? occurrenceFields['localitysecurityreason']['length'] : 0" :value="occurrenceData.localitysecurityreason" @update:value="(value) => updateOccurrenceData('localitysecurityreason', value)"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-6 col-md-3">
                        <single-country-auto-complete :definition="occurrenceFieldDefinitions['country']" label="Country" :maxlength="occurrenceFields['country'] ? occurrenceFields['country']['length'] : 0" :value="occurrenceData.country" @update:value="(value) => updateOccurrenceData('country', value)" :show-counter="false"></single-country-auto-complete>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <single-state-province-auto-complete :definition="occurrenceFieldDefinitions['stateprovince']" label="State/Province" :maxlength="occurrenceFields['stateprovince'] ? occurrenceFields['stateprovince']['length'] : 0" :value="occurrenceData.stateprovince" @update:value="(value) => updateOccurrenceData('stateprovince', value)" :show-counter="false" :country="occurrenceData.country"></single-state-province-auto-complete>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <single-county-auto-complete :definition="occurrenceFieldDefinitions['county']" label="County" :maxlength="occurrenceFields['county'] ? occurrenceFields['county']['length'] : 0" :value="occurrenceData.county" @update:value="(value) => updateOccurrenceData('county', value)" :show-counter="false" :state-province="occurrenceData.stateprovince"></single-county-auto-complete>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <text-field-input-element :definition="occurrenceFieldDefinitions['municipality']" label="Municipality" :maxlength="occurrenceFields['municipality'] ? occurrenceFields['municipality']['length'] : 0" :value="occurrenceData.municipality" @update:value="(value) => updateOccurrenceData('municipality', value)" :show-counter="false"></text-field-input-element>
                    </div>
                </div>
                <div class="row justify-between q-col-gutter-xs">
                    <div class="col-grow">
                        <text-field-input-element data-type="textarea" :definition="occurrenceFieldDefinitions['locality']" label="Locality" :value="occurrenceData.locality" @update:value="(value) => updateOccurrenceData('locality', value)"></text-field-input-element>
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'single-country-auto-complete': singleCountryAutoComplete,
        'single-county-auto-complete': singleCountyAutoComplete,
        'single-state-province-auto-complete': singleStateProvinceAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');

        function updateLocalitySecuritySetting(value) {
            if(Number(value) === 1){
                occurrenceStore.updateOccurrenceEditData('localitysecurity', value);
            }
            else{
                occurrenceStore.updateOccurrenceEditData('localitysecurity', '0');
                occurrenceStore.updateOccurrenceEditData('localitysecurityreason', value);
            }
        }

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
        }

        return {
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            updateLocalitySecuritySetting,
            updateOccurrenceData
        }
    }
};
