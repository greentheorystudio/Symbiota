const additionalConfigurationsTab = {
    template: `
        <div class="q-mb-md full-width row justify-end items-center">
            <div>
                <q-btn color="primary" @click="showAddPopup = true" label="Add Configuration" tabindex="0" />
            </div>
        </div>
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder">
                    Additional Configurations
                </div>
                <template v-if="configNameArr.length > 0">
                    <template v-for="config in configNameArr">
                        <div class="q-pl-md row justify-between q-col-gutter-sm">
                            <div class="col-5 text-body1 text-bold no-wrap self-center">
                                <div class="full-width">
                                    {{ config }}:  
                                    <q-btn color="primary" size="sm" @click="processConfigurationDelete(config);" label="Delete" aria-label="Delete configuration" tabindex="0" />
                                </div>
                            </div>
                            <div class="col-6 self-center">
                                <text-field-input-element debounce="2000" :value="additionalData[config]" @update:value="(value) => processConfigurationUpdate(config, value, false)"></text-field-input-element>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="text-body1 text-bold">There are no additional configurations set</div>
                </template>
            </q-card-section>
        </q-card>
        <q-dialog class="z-max" v-model="showAddPopup" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    <div class="text-grey-8 text-h6 text-weight-bolder">
                        Add Configuration
                    </div>
                    <div>
                        <text-field-input-element debounce="2000" label="New Configuration Name" :value="addConfigNameValue" @update:value="(value) => addConfigNameValue = value"></text-field-input-element>
                    </div>
                    <div>
                        <text-field-input-element debounce="2000" label="New Configuration Value" :value="addConfigValueValue" @update:value="(value) => addConfigValueValue = value"></text-field-input-element>
                    </div>
                    <div class="row justify-end">
                        <div>
                            <q-btn color="primary" @click="processAddNewConfiguration();" label="Add" :disabled="!addConfigNameValue || !addConfigValueValue" aria-label="Add configuration" tabindex="0" />
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const configurationStore = useConfigurationStore();

        const addConfigNameValue = Vue.ref(null);
        const addConfigValueValue = Vue.ref(null);
        const additionalData = Vue.computed(() => configurationStore.getAdditionalConfigurationData);
        const configNameArr = Vue.computed(() => {
            return Object.keys(additionalData.value).length > 0 ? Object.keys(additionalData.value) : [];
        });
        const confirmationPopupRef = Vue.ref(null);
        const showAddPopup = Vue.ref(false);

        function closePopup(){
            showAddPopup.value = false;
            addConfigNameValue.value = null;
            addConfigValueValue.value = null;
        }

        function processAddNewConfiguration(){
            addConfigNameValue.value = addConfigNameValue.value.replace(/ /g, '_');
            addConfigNameValue.value = addConfigNameValue.value.toUpperCase();
            configurationStore.validateNameCore(addConfigNameValue.value, (res) => {
                if(res === 1){
                    showNotification('negative', 'That Configuration Name is used internally within the software and cannot be set as an additional configuration name. Please enter a different name.');
                }
                else{
                    configurationStore.validateNameExisting(addConfigNameValue.value, (res) => {
                        if(res === 0){
                            configurationStore.addConfigurationValue(addConfigNameValue.value, addConfigValueValue.value, (res) => {
                                processCallbackResponse(res);
                                if(res === 1){
                                    closePopup();
                                }
                            });
                        }
                        else{
                            showNotification('negative', 'That Configuration Name is already set and in use within the portal. Please enter a different name.');
                        }
                    });
                }
            });
        }

        function processCallbackResponse(res){
            if(res === 1){
                showNotification('positive','Saved and activated');
            }
            else{
                showNotification('negative', 'There was an error saving and activating the change');
            }
        }

        function processConfigurationDelete(configName){
            configurationStore.deleteConfigurationValue(configName, (res) => {
                processCallbackResponse(res);
            });
        }

        function processConfigurationUpdate(configName, value, required, int = false){
            const confirmText = 'Do you want to save and activate this change?';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    if((int && Number(value) > 0) || (value && value !== '')){
                        configurationStore.updateConfigurationValue(configName, value, (res) => {
                            processCallbackResponse(res);
                        });
                    }
                    else if(required){
                        showNotification('negative', 'This value is required');
                    }
                    else{
                        configurationStore.deleteConfigurationValue(configName, (res) => {
                            processCallbackResponse(res);
                        });
                    }
                }
            }});
        }

        return {
            addConfigNameValue,
            addConfigValueValue,
            additionalData,
            configNameArr,
            confirmationPopupRef,
            showAddPopup,
            closePopup,
            processAddNewConfiguration,
            processConfigurationDelete,
            processConfigurationUpdate
        }
    }
};
