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
                        <q-btn color="secondary" @click="saveInstitutionsEdits();" label="Save Edits" :disabled="!editsExist || !institutionsValid" tabindex="0" />
                    </template>
                    <template v-else>
                        <span class="q-mr-md text-h6 text-bold">New Location</span>
                        <q-btn color="secondary" @click="createInstitutionslist();" label="Create" :disabled="!institutionsValid" aria-label="Create Location" tabindex="0" />
                    </template>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Location Name" :value="institutionsData['institutionname']" maxlength="100" @update:value="(value) => updateInstitutionsData('institutionname', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Location Name 2" :value="institutionsData['institutionname2']" maxlength="250" @update:value="(value) => updateInstitutionsData('institutionname2', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Location Code" :value="institutionsData['institutioncode']" maxlength="250" @update:value="(value) => updateInstitutionsData('institutioncode', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Address Line 1" :value="institutionsData['address1']" maxlength="500" @update:value="(value) => updateInstitutionsData('address1', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Address Line 2" :value="institutionsData['address2']" maxlength="500" @update:value="(value) => updateInstitutionsData('address2', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="City" :value="institutionsData['city']" maxlength="500" @update:value="(value) => updateInstitutionsData('city', value)"></text-field-input-element>
                </div>
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="State/Province" :value="institutionsData['country']" maxlength="500" @update:value="(value) => updateInstitutionsData('country', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Postal Code" :value="institutionsData['postalcode']" maxlength="500" @update:value="(value) => updateInstitutionsData('postalcode', value)"></text-field-input-element>
                </div>
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Country" :value="institutionsData['stateprovince']" maxlength="500" @update:value="(value) => updateInstitutionsData('stateprovince', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Contact Name" :value="institutionsData['contact']" maxlength="500" @update:value="(value) => updateInstitutionsData('contact', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Phone Number" :value="institutionsData['phone']" maxlength="500" @update:value="(value) => updateInstitutionsData('phone', value)"></text-field-input-element>
                </div>
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Email" :value="institutionsData['email']" maxlength="500" @update:value="(value) => updateInstitutionsData('email', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Notes" :value="institutionsData['notes']" maxlength="500" @update:value="(value) => updateInstitutionsData('notes', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row justify-end">
            <q-btn color="negative" @click="deleteInstitution();" label="Delete Location" tabindex="0" />
            </div>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>

    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement,

    },
    setup(_, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const institutionsStore = useInstitutionsStore();

        const accessOptions = [
            {value: 'private', label: 'Private'},
            {value: 'public', label: 'Public'}
        ];
        const canPublicPublish = Vue.ref(false);
        const confirmationPopupRef = Vue.ref(null);
        const institutionsData = Vue.computed(() => institutionsStore.getInstitutionsData);
        const institutionsId = Vue.computed(() => institutionsStore.getInstitutionsID);
        const institutionsValid = Vue.computed(() => institutionsStore.getInstitutionsValid);
        const editsExist = Vue.computed(() => institutionsStore.getInstitutionsEditsExist);

        function createInstitutionslist() {
            institutionsStore.createInstitutionsRecord((newBlockId) => {
                if(newBlockId > 0){
                    showNotification('positive','Description block added successfully.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the new description block.');
                }
            });
        }

        function deleteLocation() {
            const confirmText = 'Are you sure you want to delete this location? This cannot be undone.';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                    if(val){
                        taxaStore.deleteInstitutionsRecord((res) => {
                            if(res === 1){
                                showNotification('positive','Description block has been deleted.');
                                context.emit('close:popup');
                            }
                            else{
                                showNotification('negative', 'There was an error deleting the description block.');
                            }
                        });
                    }
                }});
        }

        function saveInstitutionsEdits() {
            showWorking('Saving edits...');
            institutionsStore.updateInstitutionsRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the checklist edits.');
                }
            });
        }

        function setPublicPermission() {
            const formData = new FormData();
            formData.append('permission', 'PublicChecklist');
            formData.append('action', 'validatePermission');
            fetch(permissionApiUrl, {
                method: 'POST',
                body: formData
            })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resData) => {
                    canPublicPublish.value = resData.includes('PublicChecklist');
                });
        }

        function updateInstitutionsData(key, value) {
            institutionsStore.updateInstitutionsEditData(key, value);
        }

        Vue.onMounted(() => {
            setPublicPermission();
        });

        return {
            accessOptions,
            canPublicPublish,
            institutionsData,
            institutionsId,
            institutionsValid,
            editsExist,
            confirmationPopupRef,
            createInstitutionslist,
            saveInstitutionsEdits,
            updateInstitutionsData,
        }
    }
};
