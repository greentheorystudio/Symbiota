const institutionsFieldModule = {
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="institutionsId > 0 && editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end">
                    <template v-if="institutionsId > 0">
                        <q-btn color="secondary" @click="saveInstitutionEdits();" label="Save Edits" :disabled="!editsExist || !institutionsValid" tabindex="0" />
                    </template>
                    <template v-else>
                        <q-btn color="secondary" @click="createInstitutionRecord();" label="Create" :disabled="!institutionsValid" aria-label="Create Institution or location" tabindex="0" />
                    </template>
                </div>
            </div>
            <div class="row q-gutter-sm">
                <div class="col-4">
                    <text-field-input-element label="Institution Code" :value="institutionsData['institutioncode']" maxlength="45" @update:value="(value) => updateInstitutionsData('institutioncode', value)"></text-field-input-element>
                </div>
                <div class="col-4">
                    <single-country-auto-complete label="Country" maxlength="45" :value="institutionsData['country']" @update:value="processCountryChange"></single-country-auto-complete>
                </div>
                <div>
                    <q-btn color="primary" @click="checkGBIF();" label="Check GBIF" :disabled="!institutionsData['institutioncode'] || !institutionsData['countrycode']" aria-label="Check GRSciColl" tabindex="0" />
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Institution/Location Name" :value="institutionsData['institutionname']" maxlength="150" @update:value="(value) => updateInstitutionsData('institutionname', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Institution/Location Name 2" :value="institutionsData['institutionname2']" maxlength="150" @update:value="(value) => updateInstitutionsData('institutionname2', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Address Line 1" :value="institutionsData['address1']" maxlength="150" @update:value="(value) => updateInstitutionsData('address1', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Address Line 2" :value="institutionsData['address2']" maxlength="150" @update:value="(value) => updateInstitutionsData('address2', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row q-gutter-x-sm">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="City" :value="institutionsData['city']" maxlength="45" @update:value="(value) => updateInstitutionsData('city', value)"></text-field-input-element>
                </div>
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="State/Province" :value="institutionsData['stateprovince']" maxlength="45" @update:value="(value) => updateInstitutionsData('country', value)"></text-field-input-element>
                </div>
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Postal Code" :value="institutionsData['postalcode']" maxlength="45" @update:value="(value) => updateInstitutionsData('postalcode', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Contact Name" :value="institutionsData['contact']" maxlength="65" @update:value="(value) => updateInstitutionsData('contact', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row q-gutter-x-sm">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Phone Number" :value="institutionsData['phone']" maxlength="45" @update:value="(value) => updateInstitutionsData('phone', value)"></text-field-input-element>
                </div>
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Email" :value="institutionsData['email']" maxlength="45" @update:value="(value) => updateInstitutionsData('email', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Notes" :value="institutionsData['notes']" maxlength="250" @update:value="(value) => updateInstitutionsData('notes', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row justify-end">
                <template v-if="institutionsId > 0">
                    <q-btn color="negative" @click="deleteInstitutionRecord();" label="Delete Location" tabindex="0" />
                </template>
            </div>
            
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'confirmation-popup': confirmationPopup,
        'selector-input-element': selectorInputElement,
        'single-country-auto-complete': singleCountryAutoComplete,
        'text-field-input-element': textFieldInputElement,
        'single-state-province-auto-complete': singleStateProvinceAutoComplete
    },
    setup(_, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const institutionsStore = useInstitutionsStore();

        const confirmationPopupRef = Vue.ref(null);
        const editsExist = Vue.computed(() => institutionsStore.getInstitutionsEditsExist);
        const institutionsData = Vue.computed(() => institutionsStore.getInstitutionsData);
        const institutionsId = Vue.computed(() => institutionsStore.getInstitutionsID);
        const institutionsValid = Vue.computed(() => institutionsStore.getInstitutionsValid);

        function checkGBIF() {
            const url = 'https://api.gbif.org/v1/grscicoll/search?q=' + institutionsData.value['institutioncode'] + '&hl=false&country=' + institutionsData.value['countrycode'];
            fetch(url, {
                method: 'GET'
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                console.log(data);
            });
        }
        
        function createInstitutionRecord() {
            institutionsStore.createInstitutionsRecord((newBlockId) => {
                if(newBlockId > 0){
                    showNotification('positive','Successfully added.');
                    context.emit('update:institution-arr');
                }
                else{
                    showNotification('negative', 'An error occurred.');
                }
            });
        }

        function deleteInstitutionRecord() {
            const confirmText = 'Are you sure you want to delete this record? This cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    institutionsStore.deleteInstitutionsRecord((res) => {
                        if(res === 1){
                            showNotification('positive','Successfully deleted.');
                            context.emit('update:institution-arr');
                        }
                        else{
                            showNotification('negative', 'An error occurred.');
                        }
                    });
                }
            }});
        }

        function processCountryChange(countryObj) {
            if(countryObj){
                updateInstitutionsData('country', countryObj['name']);
                updateInstitutionsData('countrycode', countryObj['iso']);
            }
            else{
                updateInstitutionsData('country', null);
                updateInstitutionsData('countrycode', null);
            }
        }

        function saveInstitutionEdits() {
            showWorking('Saving edits...');
            institutionsStore.updateInstitutionsRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                    context.emit('update:institution-arr');
                }
                else{
                    showNotification('negative', 'An error occurred.');
                }
            });
        }

        function updateInstitutionsData(key, value) {
            institutionsStore.updateInstitutionsEditData(key, value);
        }

        return {
            confirmationPopupRef,
            editsExist,
            institutionsData,
            institutionsId,
            institutionsValid,
            checkGBIF,
            createInstitutionRecord,
            deleteInstitutionRecord,
            processCountryChange,
            saveInstitutionEdits,
            updateInstitutionsData,
        }
    }
};
