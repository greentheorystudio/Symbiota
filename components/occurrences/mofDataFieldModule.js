const mofDataFieldModule = {
    props: {
        dataType: {
            type: String,
            default: 'event'
        }
    },
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
            <div v-if="configuredDataFieldsLayoutData.length > 0" class="q-mt-sm column q-gutter-sm">
                <template v-for="layoutElement in configuredDataFieldsLayoutData">
                    <template v-if="layoutElement.type === 'dataFieldRow'">
                        <mof-data-field-row :fields="layoutElement.fields"></mof-data-field-row>
                    </template>
                    <template v-else-if="layoutElement.type === 'dataFieldRowGroup'">
                        <mof-data-field-row-group :label="layoutElement.label" :rows="layoutElement.rows" :expansion="layoutElement.expansion"></mof-data-field-row-group>
                    </template>
                </template>
            </div>
        </div>
    `,
    components: {
        'mof-data-field-row': mofDataFieldRow,
        'mof-data-field-row-group': mofDataFieldRowGroup
    },
    setup(props) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const configuredData = Vue.computed(() => {
            if(props.dataType === 'event'){
                return occurrenceStore.getEventMofData;
            }
            else{
                return occurrenceStore.getOccurrenceMofData;
            }
        });
        const configuredEditData = Vue.ref({});
        const configuredDataFields = Vue.computed(() => {
            if(props.dataType === 'event'){
                return occurrenceStore.getEventMofDataFields;
            }
            else{
                return occurrenceStore.getOccurrenceMofDataFields;
            }
        });
        const configuredDataFieldsLayoutData = Vue.computed(() => {
            if(props.dataType === 'event'){
                return occurrenceStore.getEventMofDataFieldsLayoutData;
            }
            else{
                return occurrenceStore.getOccurrenceMofDataFieldsLayoutData;
            }
        });
        const configuredDataLabel = Vue.computed(() => {
            if(props.dataType === 'event'){
                return occurrenceStore.getEventMofDataLabel;
            }
            else{
                return occurrenceStore.getOccurrenceMofDataLabel;
            }
        });
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
            const editData = {
                add: [],
                delete: [],
                update: []
            };
            Object.keys(configuredUpdateData.value).forEach((key) => {
                if(configuredEditData.value[key] && !configuredData.value[key]){
                    editData.add.push({field: key, value: configuredUpdateData.value[key]});
                }
                else if(!configuredEditData.value[key] && configuredData.value[key]){
                    editData.delete.push(key);
                }
                else if(configuredEditData.value[key] !== configuredData.value[key]){
                    editData.update.push({field: key, value: configuredUpdateData.value[key]});
                }
            });
            occurrenceStore.processMofEditData(props.dataType, editData, (res) => {
                hideWorking();
                if(Number(res) === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', ('An error occurred while saving the edited data.'));
                }
            });
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
