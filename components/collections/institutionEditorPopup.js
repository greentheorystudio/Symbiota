const institutionEditorPopup = {
    props: {
        institutionId: {
            type: Number,
            default: 0
        },
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
                                    <template v-if="institutionId > 0 && editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <template v-if="institutionId > 0">
                                        <q-btn color="secondary" @click="saveInstitutionEdits();" label="Save Edits" :disabled="!editsExist || !institutionValid || !institutionNameValid" tabindex="0" />
                                    </template>
                                    <template v-else>
                                        <q-btn color="secondary" @click="createInstitutionRecord();" label="Add" :disabled="!institutionValid || !institutionNameValid" aria-label="Create institution or location" tabindex="0" />
                                    </template>
                                </div>
                            </div>
                            <div class="row q-gutter-sm">
                                <div class="col-4">
                                    <text-field-input-element label="Institution Code" :value="institutionData['institutioncode']" maxlength="45" @update:value="(value) => updateInstitutionData('institutioncode', value)"></text-field-input-element>
                                </div>
                                <div class="col-4">
                                    <single-country-auto-complete label="Country" maxlength="45" :value="institutionData['country']" @update:value="processCountryChange"></single-country-auto-complete>
                                </div>
                                <div>
                                    <q-btn color="primary" @click="checkGBIF();" label="Check GBIF" :disabled="!institutionData['institutioncode'] || !institutionData['countrycode']" aria-label="Check GBIF" tabindex="0" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Location Name" :value="institutionData['institutionname']" maxlength="150" @update:value="(value) => updateInstitutionData('institutionname', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Location Name 2" :value="institutionData['institutionname2']" maxlength="150" @update:value="(value) => updateInstitutionData('institutionname2', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Address Line 1" :value="institutionData['address1']" maxlength="150" @update:value="(value) => updateInstitutionData('address1', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Address Line 2" :value="institutionData['address2']" maxlength="150" @update:value="(value) => updateInstitutionData('address2', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row q-gutter-x-sm">
                                <div class="col-grow">
                                    <text-field-input-element label="City" :value="institutionData['city']" maxlength="45" @update:value="(value) => updateInstitutionData('city', value)"></text-field-input-element>
                                </div>
                                <div class="col-grow">
                                    <single-state-province-auto-complete label="State/Province" maxlength="45" :value="institutionData['stateprovince']" @update:value="(value) => updateInstitutionData('stateprovince', (value ? value.name : null))" :country="institutionData['country']"></single-state-province-auto-complete>
                                </div>
                                <div class="col-grow">
                                    <text-field-input-element label="Postal Code" :value="institutionData['postalcode']" maxlength="45" @update:value="(value) => updateInstitutionData('postalcode', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element label="Contact Name" :value="institutionData['contact']" maxlength="65" @update:value="(value) => updateInstitutionData('contact', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row q-gutter-x-sm">
                                <div class="col-grow">
                                    <text-field-input-element label="Phone Number" :value="institutionData['phone']" maxlength="45" @update:value="(value) => updateInstitutionData('phone', value)"></text-field-input-element>
                                </div>
                                <div class="col-grow">
                                    <text-field-input-element label="Email" :value="institutionData['email']" maxlength="45" @update:value="(value) => updateInstitutionData('email', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Notes" :value="institutionData['notes']" maxlength="250" @update:value="(value) => updateInstitutionData('notes', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-end">
                                <template v-if="institutionId > 0">
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
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const institutionsStore = useInstitutionStore();

        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => institutionsStore.getInstitutionEditsExist);
        const gbifInstitutionArr = Vue.ref([]);
        const institutionData = Vue.computed(() => institutionsStore.getInstitutionData);
        const institutionId = Vue.computed(() => institutionsStore.getInstitutionID);
        const institutionNameValid = Vue.ref(true);
        const institutionValid = Vue.computed(() => institutionsStore.getInstitutionValid);
        const showGbifInstitutionCollectionListPopup = Vue.ref(false);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function checkGBIF() {
            showWorking();
            gbifInstitutionArr.value.length = 0;
            const url = 'https://api.gbif.org/v1/grscicoll/search?q=' + institutionData.value['institutioncode'] + '&hl=false&country=' + institutionData.value['countrycode'];
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
            institutionsStore.createInstitutionRecord((newInstId) => {
                if(newInstId > 0){
                    showNotification('positive','Successfully added.');
                    context.emit('update:institution', institutionData.value);
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
                    institutionsStore.deleteInstitutionRecord((res) => {
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
                updateInstitutionData('country', countryObj['iso']);
                updateInstitutionData('countrycode', countryObj['iso']);
            }
            else{
                updateInstitutionData('country', null);
                updateInstitutionData('countrycode', null);
            }
        }

        function saveInstitutionEdits() {
            showWorking('Saving edits...');
            institutionsStore.updateInstitutionRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                    context.emit('update:institution', institutionData.value);
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
            updateInstitutionData('address2', null);
            if(data['additionalNames'] && data['additionalNames'].length > 0){
                updateInstitutionData('institutionname', data['additionalNames'][0]);
                updateInstitutionData('institutionname2', data['name']);
            }
            else{
                updateInstitutionData('institutionname', data['name']);
                updateInstitutionData('institutionname2', null);
            }
            if(data['address']){
                updateInstitutionData('address1', data['address']['address']);
                updateInstitutionData('country', data['address']['country']);
                updateInstitutionData('city', data['address']['city']);
                updateInstitutionData('stateprovince', data['address']['province']);
                updateInstitutionData('postalcode', data['address']['postalCode']);
            }
            else{
                updateInstitutionData('address1', null);
                updateInstitutionData('country', null);
                updateInstitutionData('city', null);
                updateInstitutionData('stateprovince', null);
                updateInstitutionData('postalcode', null);
            }
            if(data['contactPersons'] && data['contactPersons'].length > 0){
                const contactName = (data['contactPersons'][0]['firstName'] ? data['contactPersons'][0]['firstName'] : '') + ((data['contactPersons'][0]['firstName'] && data['contactPersons'][0]['lastName']) ? ' ' : '') + (data['contactPersons'][0]['lastName'] ? data['contactPersons'][0]['lastName'] : '');
                updateInstitutionData('contact', contactName);
                if(data['contactPersons'][0]['address'] && data['contactPersons'][0]['address'].length > 0){
                    updateInstitutionData('address1', data['contactPersons'][0]['address'][0]);
                }
                if(data['contactPersons'][0]['phone'] && data['contactPersons'][0]['phone'].length > 0){
                    updateInstitutionData('phone', data['contactPersons'][0]['phone'][0]);
                }
                else{
                    updateInstitutionData('phone', null);
                }
                if(data['contactPersons'][0]['email'] && data['contactPersons'][0]['email'].length > 0){
                    updateInstitutionData('email', data['contactPersons'][0]['email'][0]);
                }
                else{
                    updateInstitutionData('email', null);
                }
            }
            else{
                updateInstitutionData('contact', null);
                updateInstitutionData('phone', null);
                updateInstitutionData('email', null);
            }
        }

        function updateInstitutionData(key, value) {
            institutionsStore.updateInstitutionEditData(key, value);
            if(key === 'institutionname' && value){
                institutionNameValid.value = false;
                validateInstitutionName();
            }
        }

        function validateInstitutionName() {
            const formData = new FormData();
            formData.append('iid', institutionData.value['iid']);
            formData.append('institutionname', institutionData.value['institutionname']);
            formData.append('action', 'getInstitutionIdByName');
            fetch(institutionsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 0){
                    institutionNameValid.value = true;
                }
                else{
                    showNotification('negative', 'A location already exists with that name');
                }
            });
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            institutionsStore.setInstitutionData(props.institutionId);
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            gbifInstitutionArr,
            institutionData,
            institutionId,
            institutionNameValid,
            institutionValid,
            showGbifInstitutionCollectionListPopup,
            checkGBIF,
            closePopup,
            createInstitutionRecord,
            deleteInstitutionRecord,
            processCountryChange,
            saveInstitutionEdits,
            setInstitutionData,
            updateInstitutionData
        }
    }
};
