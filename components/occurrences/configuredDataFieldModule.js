const configuredDataFieldModule = {
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end">
                    <q-btn color="secondary" @click="saveConfiguredEditDataEdits();" :label="('Save ' + configuredDataLabel + ' Edits')" :disabled="!editsExist" />
                </div>
            </div>
            <template v-if="configuredDataFieldsLayoutData.length > 0">
                <template v-for="layoutElement in configuredDataFieldsLayoutData">
                    <template v-if="layoutElement.type === 'dataFieldRow'">
                        <configured-data-field-row :fields="layoutElement.fields"></configured-data-field-row>
                    </template>
                    <template v-else-if="layoutElement.type === 'dataFieldRowGroup'">
                        <configured-data-field-row :label="layoutElement.label" :rows="layoutElement.rows"></configured-data-field-row>
                    </template>
                </template>
            </template>
        </div>
    `,
    components: {
        'configured-data-field-row': configuredDataFieldRow,
        'configured-data-field-row-group': configuredDataFieldRowGroup
    },
    setup() {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const configuredData = Vue.computed(() => occurrenceStore.getConfiguredData);
        const configuredEditData = Vue.ref({});
        const configuredDataFields = Vue.computed(() => occurrenceStore.getConfiguredDataFields);
        const configuredDataFieldsLayoutData = Vue.computed(() => occurrenceStore.getConfiguredDataFieldsLayoutData);
        const configuredDataLabel = Vue.computed(() => occurrenceStore.getConfiguredDataLabel);
        const configuredUpdateData = Vue.ref({});
        const editsExist = Vue.computed(() => {
            let exist = false;
            configuredUpdateData.value = Object.assign({}, {});
            for(let key in configuredEditData.value) {
                if(configuredEditData.value.hasOwnProperty(key) && configuredEditData.value[key] !== configuredData.value[key]) {
                    exist = true;
                    configuredUpdateData.value[key] = configuredEditData.value[key];
                }
            }
            return exist;
        });

        Vue.watch(configuredData, () => {
            setEditData();
        });

        function saveConfiguredEditDataEdits() {
            const dataKeys = Object.keys(configuredUpdateData.value);
            if(dataKeys.length > 0){
                showWorking();
                const callbackFunction = (res) => {
                    if(Number(res) === 1){
                        delete configuredUpdateData.value[dataKeys[0]];
                        saveConfiguredEditDataEdits();
                    }
                    else{
                        hideWorking();
                        showNotification('negative', ('An error occurred while saving the ' + configuredDataFields.value[dataKeys[0]]['label'] + ' value.'));
                    }
                };
                if(configuredEditData.value[dataKeys[0]] && !configuredData.value[dataKeys[0]]){
                    occurrenceStore.addConfiguredDataValue(dataKeys[0], configuredUpdateData.value[dataKeys[0]], callbackFunction);
                }
                else if(!configuredEditData.value[dataKeys[0]] && configuredData.value[dataKeys[0]]){
                    occurrenceStore.deleteConfiguredDataValue(dataKeys[0], callbackFunction);
                }
                else if(configuredEditData.value[dataKeys[0]] !== configuredData.value[dataKeys[0]]){
                    occurrenceStore.updateConfiguredDataValue(dataKeys[0], configuredUpdateData.value[dataKeys[0]], callbackFunction);
                }
            }
            else{
                configuredData.value = Object.assign({}, configuredEditData.value);
                hideWorking();
                showNotification('positive','Edits saved.');
            }
        }

        function setEditData() {
            configuredEditData.value = Object.assign({}, configuredData.value);
            configuredUpdateData.value = Object.assign({}, {});
        }

        function updateConfiguredEditData(key, value) {
            configuredEditData.value[key] = value;
        }

        Vue.provide('configuredEditData', configuredEditData);
        Vue.provide('configuredDataFields', configuredDataFields);
        Vue.provide('updateConfiguredEditData', updateConfiguredEditData);

        Vue.onMounted(() => {
            setEditData();
        });

        return {
            configuredDataFieldsLayoutData,
            configuredDataLabel,
            editsExist,
            saveConfiguredEditDataEdits
        }
    }
};
