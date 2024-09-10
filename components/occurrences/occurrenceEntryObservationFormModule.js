const occurrenceEntryObservationFormModule = {
    template: `
        <div>
            <q-card flat>
                <q-card-section class="column q-gutter-y-md">
                    <occurrence-editor-occurrence-data-controls @occurrence:created="processOccurrenceCreated"></occurrence-editor-occurrence-data-controls>
                    <div class="column q-col-gutter-sm">
                        <div class="row">
                            <div class="col-grow">
                                <media-file-upload-input-element ref="mediaUploaderRef" :collection="collectionData" :occ-id="newOccid" :taxon-id="occurrenceData.tid" :show-start="false" @upload:complete="processMediaUpdate"></media-file-upload-input-element>
                            </div>
                        </div>
                        <div class="row justify-between q-col-gutter-sm">
                            <div class="col-12 col-sm-3">
                                <text-field-input-element :definition="occurrenceFieldDefinitions['catalognumber']" label="Catalog Number" :maxlength="occurrenceFields['catalognumber'] ? occurrenceFields['catalognumber']['length'] : 0" :value="occurrenceData.catalognumber" @update:value="(value) => updateOccurrenceData('catalognumber', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-9">
                                <text-field-input-element :definition="occurrenceFieldDefinitions['othercatalognumbers']" label="Other Catalog Numbers" :maxlength="occurrenceFields['othercatalognumbers'] ? occurrenceFields['othercatalognumbers']['length'] : 0" :value="occurrenceData.othercatalognumbers" @update:value="(value) => updateOccurrenceData('othercatalognumbers', value)"></text-field-input-element>
                            </div>
                        </div>
                        <div class="row justify-between q-col-gutter-sm">
                            <div class="col-12 col-sm-6 col-md-grow">
                                <text-field-input-element :definition="occurrenceFieldDefinitions['recordedby']" label="Collector/Observer" :maxlength="occurrenceFields['recordedby'] ? occurrenceFields['recordedby']['length'] : 0" :value="occurrenceData.recordedby" @update:value="(value) => updateOccurrenceData('recordedby', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-2">
                                <text-field-input-element :definition="occurrenceFieldDefinitions['recordnumber']" label="Number" :maxlength="occurrenceFields['recordnumber'] ? occurrenceFields['recordnumber']['length'] : 0" :value="occurrenceData.recordnumber" @update:value="(value) => updateOccurrenceData('recordnumber', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-2">
                                <date-input-element :definition="occurrenceFieldDefinitions['eventdate']" label="Date" :value="occurrenceData.eventdate" @update:value="updateDateData"></date-input-element>
                            </div>
                            <div class="col-12 col-sm-6 col-md-2">
                                <time-input-element :definition="occurrenceFieldDefinitions['eventtime']" label="Time" :value="occurrenceData.eventtime" @update:value="(value) => updateOccurrenceData('eventtime', value)"></time-input-element>
                            </div>
                        </div>
                        <div class="row justify-between q-col-gutter-sm">
                            <div class="col-12 col-sm-4">
                                <single-scientific-common-name-auto-complete :definition="occurrenceFieldDefinitions['sciname']" :sciname="occurrenceData.sciname" label="Scientific Name" @update:sciname="updateScientificNameValue"></single-scientific-common-name-auto-complete>
                            </div>
                            <div class="col-12 col-sm-3">
                                <text-field-input-element :disabled="Number(occurrenceData.tid) > 0" :definition="occurrenceFieldDefinitions['scientificnameauthorship']" label="Author" :maxlength="occurrenceFields['scientificnameauthorship'] ? occurrenceFields['scientificnameauthorship']['length'] : 0" :value="((Number(occurrenceData.tid) > 0 && occurrenceData.hasOwnProperty('taxonData')) ? occurrenceData['taxonData'].author : occurrenceData.scientificnameauthorship)" @update:value="(value) => updateOccurrenceData('scientificnameauthorship', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-2">
                                <text-field-input-element :definition="occurrenceFieldDefinitions['identificationqualifier']" label="ID Qualifier" :maxlength="occurrenceFields['identificationqualifier'] ? occurrenceFields['identificationqualifier']['length'] : 0" :value="occurrenceData.identificationqualifier" @update:value="(value) => updateOccurrenceData('identificationqualifier', value)"></text-field-input-element>
                            </div>
                            <div class="col-12 col-sm-3">
                                <text-field-input-element :disabled="Number(occurrenceData.tid) > 0" :definition="occurrenceFieldDefinitions['family']" label="Family" :maxlength="occurrenceFields['family'] ? occurrenceFields['family']['length'] : 0" :value="(Number(occurrenceData.tid) > 0 ? occurrenceData['taxonData'].family : occurrenceData.family)" @update:value="(value) => updateOccurrenceData('family', value)"></text-field-input-element>
                            </div>
                        </div>
                        <div class="row justify-between q-col-gutter-sm">
                            <div class="col-12 col-sm-6 col-md-3">
                                <single-country-auto-complete :definition="occurrenceFieldDefinitions['country']" label="Country" :maxlength="occurrenceFields['country'] ? occurrenceFields['country']['length'] : 0" :value="occurrenceData.country" @update:value="(value) => updateOccurrenceData('country', value)"></single-country-auto-complete>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <single-state-province-auto-complete :definition="occurrenceFieldDefinitions['stateprovince']" label="State/Province" :maxlength="occurrenceFields['stateprovince'] ? occurrenceFields['stateprovince']['length'] : 0" :value="occurrenceData.stateprovince" @update:value="(value) => updateOccurrenceData('stateprovince', value)" :country="occurrenceData.country"></single-state-province-auto-complete>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <single-county-auto-complete :definition="occurrenceFieldDefinitions['county']" label="County" :maxlength="occurrenceFields['county'] ? occurrenceFields['county']['length'] : 0" :value="occurrenceData.county" @update:value="(value) => updateOccurrenceData('county', value)" :state-province="occurrenceData.stateprovince"></single-county-auto-complete>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <text-field-input-element :definition="occurrenceFieldDefinitions['municipality']" label="Municipality" :maxlength="occurrenceFields['municipality'] ? occurrenceFields['municipality']['length'] : 0" :value="occurrenceData.municipality" @update:value="(value) => updateOccurrenceData('municipality', value)"></text-field-input-element>
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    `,
    components: {
        'date-input-element': dateInputElement,
        'media-file-upload-input-element': mediaFileUploadInputElement,
        'occurrence-editor-occurrence-data-controls': occurrenceEditorOccurrenceDataControls,
        'single-country-auto-complete': singleCountryAutoComplete,
        'single-county-auto-complete': singleCountyAutoComplete,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'single-state-province-auto-complete': singleStateProvinceAutoComplete,
        'text-field-input-element': textFieldInputElement,
        'time-input-element': timeInputElement
    },
    setup() {
        const occurrenceStore = Vue.inject('occurrenceStore');

        const collectionData = Vue.computed(() => occurrenceStore.getCollectionData);
        const entryFollowUpAction = Vue.computed(() => occurrenceStore.getEntryFollowUpAction);
        const mediaUploaderRef = Vue.ref(null);
        const newOccid = Vue.ref(0);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');

        function processMediaUpdate() {
            if(entryFollowUpAction.value === 'remain' || entryFollowUpAction.value === 'none'){
                occurrenceStore.setCurrentOccurrenceRecord(newOccid.value);
            }
            else{
                occurrenceStore.setCurrentOccurrenceRecord(0);
            }
        }

        function processOccurrenceCreated(occid) {
            newOccid.value = occid;
            setTimeout(() => {
                mediaUploaderRef.value.uploadFiles();
            }, 200);
        }

        function updateDateData(dateData) {
            updateOccurrenceData('eventdate', dateData['date']);
            updateOccurrenceData('year', dateData['year']);
            updateOccurrenceData('month', dateData['month']);
            updateOccurrenceData('day', dateData['day']);
            updateOccurrenceData('startdayofyear', dateData['startDayOfYear']);
            updateOccurrenceData('enddayofyear', dateData['endDayOfYear']);
        }

        function updateOccurrenceData(key, value) {
            occurrenceStore.updateOccurrenceEditData(key, value);
        }

        function updateScientificNameValue(taxon) {
            occurrenceStore.updateOccurrenceEditDataTaxon(taxon);
        }

        return {
            collectionData,
            mediaUploaderRef,
            newOccid,
            occurrenceData,
            occurrenceFields,
            occurrenceFieldDefinitions,
            processMediaUpdate,
            processOccurrenceCreated,
            updateDateData,
            updateOccurrenceData,
            updateScientificNameValue
        }
    }
};
