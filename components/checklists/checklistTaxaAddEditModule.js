const checklistTaxaAddEditModule = {
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="checklistTaxaId > 0 && editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end">
                    <template v-if="checklistTaxaId > 0">
                        <q-btn color="secondary" @click="saveChecklistTaxaEdits();" label="Save Edits" :disabled="!editsExist || !checklistTaxaValid" />
                    </template>
                    <template v-else>
                        <q-btn color="secondary" @click="addChecklistTaxon();" label="Add Taxon" :disabled="!checklistTaxaValid" />
                    </template>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <single-scientific-common-name-auto-complete :sciname="checklistTaxaData['sciname']" :disabled="Number(checklistTaxaData['tid']) > 0" label="Taxon" rank-low="180" limit-to-options="true" @update:sciname="processTaxonValChange"></single-scientific-common-name-auto-complete>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Habitat" :value="checklistTaxaData['habitat']" maxlength="250" @update:value="(value) => updateChecklistTaxonData('habitat', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Abundance" :value="checklistTaxaData['abundance']" maxlength="50" @update:value="(value) => updateChecklistTaxonData('abundance', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element data-type="textarea" label="Notes" :value="checklistTaxaData['notes']" maxlength="2000" @update:value="(value) => updateChecklistTaxonData('notes', value)"></text-field-input-element>
                </div>
            </div>
            <div class="row">
                <div class="col-grow">
                    <text-field-input-element label="Source" :value="checklistTaxaData['source']" maxlength="250" @update:value="(value) => updateChecklistTaxonData('source', value)"></text-field-input-element>
                </div>
            </div>
            <div v-if="Number(checklistTaxaId) > 0" class="row justify-end q-gutter-md">
                <div>
                    <q-btn color="negative" @click="deleteChecklistTaxon();" label="Remove Taxon" />
                </div>
            </div>
        </div>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const checklistStore = useChecklistStore();

        const checklistTaxaData = Vue.computed(() => checklistStore.getChecklistTaxaData);
        const checklistTaxaId = Vue.computed(() => checklistStore.getChecklistTaxaID);
        const checklistTaxaValid = Vue.computed(() => checklistStore.getChecklistTaxaValid);
        const editsExist = Vue.computed(() => checklistStore.getChecklistTaxaEditsExist);
        const confirmationPopupRef = Vue.ref(null);

        function addChecklistTaxon() {
            checklistStore.createChecklistTaxaRecord((newChecklistTaxaId) => {
                if(newChecklistTaxaId > 0){
                    showNotification('positive','Taxon added successfully.');
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error adding the taxon to the checklist');
                }
            });
        }

        function deleteChecklistTaxon() {
            const confirmText = 'Are you sure you want to remove this taxon from the checklist? This action cannot be undone';
            confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'No', trueText: 'Yes', callback: (val) => {
                if(val){
                    checklistStore.deleteChecklistTaxonRecord((res) => {
                        if(res === 1){
                            showNotification('positive','The taxon has been removed');
                            context.emit('close:popup');
                        }
                        else{
                            showNotification('negative', 'There was an error removing the taxon from the checklist');
                        }
                    });
                }
            }});
        }

        function processTaxonValChange(taxon) {
            checklistStore.updateChecklistTaxonEditData('tid', (taxon ? taxon['tid'] : null));
            checklistStore.updateChecklistTaxonEditData('sciname', (taxon ? taxon['sciname'] : null));
        }

        function saveChecklistTaxaEdits() {
            showWorking('Saving edits...');
            checklistStore.updateChecklistTaxonRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the taxon edits.');
                }
                context.emit('close:popup');
            });
        }

        function updateChecklistTaxonData(key, value) {
            checklistStore.updateChecklistTaxonEditData(key, value);
        }

        return {
            checklistTaxaData,
            checklistTaxaId,
            checklistTaxaValid,
            confirmationPopupRef,
            editsExist,
            addChecklistTaxon,
            deleteChecklistTaxon,
            processTaxonValChange,
            saveChecklistTaxaEdits,
            updateChecklistTaxonData
        }
    }
};
