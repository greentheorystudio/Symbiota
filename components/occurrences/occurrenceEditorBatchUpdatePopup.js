const occurrenceEditorBatchUpdatePopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    <div class="row">
                        <div class="col-grow">
                            <selector-input-element label="Field Name" :options="fieldOptions" option-value="field" option-label="label" :value="selectedField" @update:value="processFieldSelectionChange"></selector-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <text-field-input-element label="Current Value" :value="currentValueValue" @update:value="(value) => currentValueValue = value"></text-field-input-element>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <template v-if="selectedField === 'processingstatus'">
                                <selector-input-element label="New Value" :options="processingStatusOptions" :value="newValueValue" @update:value="(value) => newValueValue = value"></selector-input-element>
                            </template>
                            <template v-else>
                                <text-field-input-element label="New Value" :value="newValueValue" @update:value="(value) => newValueValue = value"></text-field-input-element>
                            </template>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-grow">
                            <q-option-group :options="matchOptions" type="radio" v-model="selectedMatchOption" dense />
                        </div>
                    </div>
                    <div class="row justify-end q-gutter-md">
                        <div>
                            <q-btn color="primary" @click="processBatchUpdateData();" label="Batch Update Field" :disabled="!selectedField || !currentValueValue" />
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(_, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const confirmationPopupRef = Vue.ref(null);
        const currentValueValue = Vue.ref(null);
        const fieldOptions = Vue.computed(() => searchStore.getQueryBuilderFieldOptions);
        const matchOptions = [
            { label: 'Match Whole Field', value: 'whole' },
            { label: 'Match Any Part of Field', value: 'part' }
        ];
        const newValueValue = Vue.ref(null);
        const processingStatusOptions = Vue.computed(() => baseStore.getOccurrenceProcessingStatusOptions);
        const searchTerms = Vue.computed(() => searchStore.getSearchTerms);
        const selectedField = Vue.ref(null);
        const selectedMatchOption = Vue.ref('whole');

        function closePopup() {
            context.emit('close:popup');
        }

        function processBatchUpdateData() {
            showWorking();
            occurrenceStore.getBatchUpdateCount(searchTerms.value, selectedField.value, currentValueValue.value, selectedMatchOption.value, (res) => {
                hideWorking();
                if(Number(res) > 0){
                    const confirmText = 'You are about to update ' + res + ' records. This cannot be undone. Do you want to continue?';
                    confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                        if(val){
                            showWorking('Batch updating data');
                            occurrenceStore.batchUpdateOccurrenceData(searchTerms.value, selectedField.value, currentValueValue.value, newValueValue.value, selectedMatchOption.value, (res) => {
                                hideWorking();
                                if(res === 1){
                                    showNotification('positive','Batch update successful');
                                    closePopup();
                                }
                                else{
                                    showNotification('negative', 'An error occurred while batch updating the data');
                                }
                            });
                        }
                    }});
                }
                else{
                    showNotification('negative', 'There are no records that would be updated');
                }
            });
        }

        function processFieldSelectionChange(value) {
            if(value === 'processingstatus'){
                newValueValue.value = null;
            }
            selectedField.value = value;
        }
        
        return {
            confirmationPopupRef,
            currentValueValue,
            fieldOptions,
            matchOptions,
            newValueValue,
            processingStatusOptions,
            selectedField,
            selectedMatchOption,
            closePopup,
            processBatchUpdateData,
            processFieldSelectionChange
        }
    }
};
