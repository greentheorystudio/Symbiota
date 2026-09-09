const institutionsEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" v-if="!showGbifInstitutionCollectionListPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
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
                                    <q-btn color="primary" @click="checkGBIF();" label="Check GBIF" :disabled="!institutionsData['institutioncode'] || !institutionsData['countrycode']" aria-label="Check GBIF" tabindex="0" />
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
                                    <single-state-province-auto-complete label="State/Province" maxlength="45" :value="institutionsData['stateprovince']" @update:value="(value) => updateInstitutionsData('stateprovince', (value ? value.name : null))" :country="institutionsData['country']"></single-state-province-auto-complete>
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
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <template v-if="showGbifInstitutionCollectionListPopup">
            <gbif-institution-collection-list-popup
                popup-type="institution"
                :data-arr="gbifInstitutionArr"
                :show-popup="showGbifInstitutionCollectionListPopup"
                @update:data="setInstitutionData"
                @close:popup="showGbifInstitutionCollectionListPopup = false"
            ></gbif-institution-collection-list-popup>
        </template>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'confirmation-popup': confirmationPopup,
        'gbif-institution-collection-list-popup': gbifInstitutionCollectionListPopup,
        'selector-input-element': selectorInputElement,
        'single-country-auto-complete': singleCountryAutoComplete,
        'text-field-input-element': textFieldInputElement,
        'single-state-province-auto-complete': singleStateProvinceAutoComplete
    },
    setup(_, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const institutionsStore = useInstitutionsStore();

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => institutionsStore.getInstitutionsEditsExist);
        const gbifInstitutionArr = Vue.ref([]);
        const institutionsData = Vue.computed(() => institutionsStore.getInstitutionsData);
        const institutionsId = Vue.computed(() => institutionsStore.getInstitutionsID);
        const institutionsValid = Vue.computed(() => institutionsStore.getInstitutionsValid);
        const showGbifInstitutionCollectionListPopup = Vue.ref(false);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function checkGBIF() {
            showWorking();
            gbifInstitutionArr.value.length = 0;
            const url = 'https://api.gbif.org/v1/grscicoll/search?q=' + institutionsData.value['institutioncode'] + '&hl=false&country=' + institutionsData.value['countrycode'];
            fetch(url, {
                method: 'GET'
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                hideWorking();
                if(data){
                    gbifInstitutionArr.value = data;
                    showGbifInstitutionCollectionListPopup.value = true;
                }
                else{
                    showNotification('negative', 'No institutions could be found matching that code.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
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
                updateInstitutionsData('country', countryObj['iso']);
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

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setInstitutionData(data) {
            showGbifInstitutionCollectionListPopup.value = false;
            updateInstitutionsData('address2', null);
            if(data['additionalNames'] && data['additionalNames'].length > 0){
                updateInstitutionsData('institutionname', data['additionalNames'][0]);
                updateInstitutionsData('institutionname2', data['name']);
            }
            else{
                updateInstitutionsData('institutionname', data['name']);
                updateInstitutionsData('institutionname2', null);
            }
            if(data['address']){
                updateInstitutionsData('address1', data['address']['address']);
                updateInstitutionsData('country', data['address']['country']);
                updateInstitutionsData('city', data['address']['city']);
                updateInstitutionsData('stateprovince', data['address']['province']);
                updateInstitutionsData('postalcode', data['address']['postalCode']);
            }
            else{
                updateInstitutionsData('address1', null);
                updateInstitutionsData('country', null);
                updateInstitutionsData('city', null);
                updateInstitutionsData('stateprovince', null);
                updateInstitutionsData('postalcode', null);
            }
            if(data['contactPersons'] && data['contactPersons'].length > 0){
                const contactName = (data['contactPersons'][0]['firstName'] ? data['contactPersons'][0]['firstName'] : '') + ((data['contactPersons'][0]['firstName'] && data['contactPersons'][0]['lastName']) ? ' ' : '') + (data['contactPersons'][0]['lastName'] ? data['contactPersons'][0]['lastName'] : '');
                updateInstitutionsData('contact', contactName);
                if(data['contactPersons'][0]['address'] && data['contactPersons'][0]['address'].length > 0){
                    updateInstitutionsData('address1', data['contactPersons'][0]['address'][0]);
                }
                if(data['contactPersons'][0]['phone'] && data['contactPersons'][0]['phone'].length > 0){
                    updateInstitutionsData('phone', data['contactPersons'][0]['phone'][0]);
                }
                else{
                    updateInstitutionsData('phone', null);
                }
                if(data['contactPersons'][0]['email'] && data['contactPersons'][0]['email'].length > 0){
                    updateInstitutionsData('email', data['contactPersons'][0]['email'][0]);
                }
                else{
                    updateInstitutionsData('email', null);
                }
            }
            else{
                updateInstitutionsData('contact', null);
                updateInstitutionsData('phone', null);
                updateInstitutionsData('email', null);
            }
        }

        function updateInstitutionsData(key, value) {
            institutionsStore.updateInstitutionsEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            gbifInstitutionArr,
            institutionsData,
            institutionsId,
            institutionsValid,
            showGbifInstitutionCollectionListPopup,
            checkGBIF,
            closePopup,
            createInstitutionRecord,
            deleteInstitutionRecord,
            processCountryChange,
            saveInstitutionEdits,
            setInstitutionData,
            updateInstitutionsData
        }
    }
};
