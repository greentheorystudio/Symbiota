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
                    <q-btn color="secondary" @click="saveTaxonEdits();" label="Save Taxon Edits" :disabled="!editsExist || !taxonValid || !uniqueTaxon" tabindex="0" />
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
        const uniqueTaxon = Vue.ref(true);

        function saveTaxonEdits() {
            taxaStore.updateTaxonRecord((res) => {
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
            if(key === 'sciname'){
                uniqueTaxon.value = false;
                validateUniqueTaxon();
            }
            if(key === 'rankid') {
                if(Number(value) === 140){
                    taxaStore.updateTaxonEditData('family', taxon.value['sciname']);
                }
                else if(Number(value) < 140){
                    taxaStore.updateTaxonEditData('family', null);
                }
            }
        }

        function validateUniqueTaxon() {
            uniqueTaxon.value = false;
            if(Number(taxon.value['rankid']) === 10){
                uniqueTaxon.value = true;
            }
            else if(taxon.value['sciname'] && taxon.value['sciname'].length > 0 && Number(taxon.value['kingdomid']) > 0){
                const formData = new FormData();
                formData.append('action', 'getTaxaIdDataFromNameArr');
                formData.append('kingdomid', taxon.value['kingdomid'].toString());
                formData.append('taxa', JSON.stringify([taxon.value['sciname']]));
                fetch(taxaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    if(Object.keys(data).length === 0 || (Object.keys(data).length === 1 && Number(data[Object.keys(data)[0]]['tid']) === Number(taxon.value['tid']))){
                        uniqueTaxon.value = true;
                    }
                    else{
                        showNotification('negative', 'A taxon already exists in the thesaurus with that name.');
                        taxaStore.revertScinameEdits();
                        uniqueTaxon.value = true;
                    }
                });
            }
        }

        return {
            editsExist,
            taxon,
            taxonValid,
            uniqueTaxon,
            saveTaxonEdits,
            updateTaxonData
        }
    }
};
