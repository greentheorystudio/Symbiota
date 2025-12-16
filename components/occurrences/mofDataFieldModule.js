const mofDataFieldModule = {
    props: {
        dataType: {
            type: String,
            default: 'event'
        },
        newRecord: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div v-if="!newRecord" class="row justify-between">
                <div>
                    <template v-if="editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end">
                    <q-btn color="secondary" @click="processSaveDataEdits();" :label="('Save ' + configuredDataLabel + ' Edits')" :disabled="!editsExist" tabindex="0" />
                </div>
            </div>
            <div v-if="configuredDataFieldsLayoutData.length > 0" class="q-mt-sm column q-col-gutter-sm">
                <template v-for="layoutElement in configuredDataFieldsLayoutData">
                    <template v-if="layoutElement.type === 'dataFieldRow'">
                        <mof-data-field-row :editor="true" :configured-data="configuredData" :configured-data-fields="configuredDataFields" :fields="layoutElement.fields" @update:configured-edit-data="updateConfiguredEditData"></mof-data-field-row>
                    </template>
                    <template v-else-if="layoutElement.type === 'dataFieldRowGroup'">
                        <mof-data-field-row-group :editor="true" :configured-data="configuredData" :configured-data-fields="configuredDataFields" :label="layoutElement.label" :rows="layoutElement.rows" :expansion="layoutElement.expansion" @update:configured-edit-data="updateConfiguredEditData"></mof-data-field-row-group>
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
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const configuredData = Vue.computed(() => {
            if(props.dataType === 'event'){
                return occurrenceStore.getEventMofData;
            }
            else{
                return occurrenceStore.getOccurrenceMofData;
            }
        });
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
        const editsExist = Vue.computed(() => {
            if(props.dataType === 'event'){
                return occurrenceStore.getEventMofEditsExist;
            }
            else{
                return occurrenceStore.getOccurrenceMofEditsExist;
            }
        });
        const newEventRecord = Vue.ref(false);

        function processSaveDataEdits() {
            if(props.dataType === 'occurrence' || Number(occurrenceStore.getCollectingEventID) > 0){
                saveConfiguredDataEdits();
            }
            else{
                newEventRecord.value = true;
                occurrenceStore.setNewCollectingEventDataFromCurrentOccurrence();
                occurrenceStore.createCollectingEventRecord(() => {
                    if(occurrenceStore.getOccurrenceEditsExist){
                        occurrenceStore.updateOccurrenceRecord((res) => {
                            if(res === 1){
                                saveConfiguredDataEdits();
                            }
                            else{
                                showNotification('negative', 'There was an error setting the event data.');
                            }
                        });
                    }
                    else{
                        showNotification('negative', 'There was an error setting the event data.');
                    }
                });
            }
        }

        function saveConfiguredDataEdits() {
            newEventRecord.value = false;
            occurrenceStore.processMofEditData(props.dataType, (res) => {
                if(Number(res) === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', ('An error occurred while saving the edited data.'));
                }
            });
        }

        function updateConfiguredEditData(data) {
            if(props.dataType === 'event'){
                occurrenceStore.updateEventMofEditData(data.key, data.value);
            }
            else{
                occurrenceStore.updateOccurrenceMofEditData(data.key, data.value);
            }
        }

        return {
            configuredData,
            configuredDataFields,
            configuredDataFieldsLayoutData,
            configuredDataLabel,
            editsExist,
            processSaveDataEdits,
            updateConfiguredEditData
        }
    }
};
