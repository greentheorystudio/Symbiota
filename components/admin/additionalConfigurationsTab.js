const additionalConfigurationsTab = {
    template: `
        <q-card flat bordered>
            <q-card-section class="q-pa-sm column q-col-gutter-sm">
                <div class="text-grey-8 text-h6 text-weight-bolder">
                    Additional Configurations
                </div>
                <template v-for="config in configNameArr">
                    <div class="q-pl-md row justify-between q-col-gutter-sm">
                        <div class="col-5 text-body1 text-bold no-wrap self-center">
                            <div class="full-width">
                                {{ config }}:  
                                <q-btn color="primary" size="sm" @click="processConfigurationDelete(config);" label="Delete" />
                            </div>
                        </div>
                        <div class="col-6 self-center">
                            <text-field-input-element :value="additionalData[config]" @update:value="(value) => processConfigurationUpdate(config, value, false)"></text-field-input-element>
                        </div>
                    </div>
                </template>
            </q-card-section>
        </q-card>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const configurationStore = useConfigurationStore();

        const additionalData = Vue.computed(() => configurationStore.getAdditionalConfigurationData);
        const configNameArr = Vue.computed(() => {
            return Object.keys(additionalData.value).length > 0 ? Object.keys(additionalData.value) : [];
        });
        const confirmationPopupRef = Vue.ref(null);

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
            additionalData,
            configNameArr,
            confirmationPopupRef,
            processConfigurationDelete,
            processConfigurationUpdate
        }
    }
};
