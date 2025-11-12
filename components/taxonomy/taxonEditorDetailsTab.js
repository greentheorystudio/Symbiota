const taxonEditorDetailsTab = {
    template: `
        <div class="column q-col-gutter-sm">
            <div class="row justify-between">
                <div>
                    <template v-if="editsExist">
                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                    </template>
                </div>
                <div class="row justify-end">
                    <q-btn color="secondary" @click="saveTaxonEdits();" label="Save Taxon Edits" :disabled="!editsExist || !taxonValid" tabindex="0" />
                </div>
            </div>
            <taxon-field-module :data="taxon" @update:taxon-data="(data) => updateTaxonData(data.key, data.value)"></taxon-field-module>
        </div>
    `,
    components: {
        'taxon-field-module': taxonFieldModule
    },
    setup() {
        const { showNotification } = useCore();
        const taxaStore = useTaxaStore();

        const editsExist = Vue.computed(() => taxaStore.getTaxaEditsExist);
        const taxon = Vue.computed(() => taxaStore.getTaxaData);
        const taxonValid = Vue.computed(() => taxaStore.getTaxaValid);

        function saveTaxonEdits() {
            taxaStore.updateTaxonRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the taxon edits.');
                }
            });
        }

        function updateTaxonData(key, value) {
            taxaStore.updateTaxonEditData(key, value);
        }

        return {
            editsExist,
            taxon,
            taxonValid,
            saveTaxonEdits,
            updateTaxonData
        }
    }
};
