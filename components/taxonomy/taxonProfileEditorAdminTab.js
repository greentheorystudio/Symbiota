const taxonProfileEditorAdminTab = {
    template: `
        <div class="column q-gutter-md">
            <q-card flat bordered>
                <q-card-section class="column q-gutter-sm">
                    <div class="text-subtitle1 text-bold">Remap Resources to Another Taxon</div>
                    <div class="row">
                        <div class="col-grow">
                            <single-scientific-common-name-auto-complete :sciname="remapTaxonVal" label="Taxon" :limit-to-options="true" @update:sciname="processRemapTaxonNameChange"></single-scientific-common-name-auto-complete>
                        </div>
                    </div>
                    <div class="row justify-end q-gutter-sm">
                        <div>
                            <q-btn color="primary" @click="processRemap();" label="Remap Resources" :disabled="Number(remapTaxonTid) > 0" tabindex="0" />
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    `,
    components: {
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete
    },
    setup() {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const remapTaxonTid = Vue.ref(null);
        const remapTaxonVal = Vue.ref(null);
        const taxon = Vue.computed(() => taxaStore.getTaxaData);

        function quietSetTaxonData(tid) {
            taxaStore.setTaxon(tid, true);
        }

        function processRemap() {
            showWorking();
            taxaStore.remapTaxonResources(remapTaxonTid.value, (res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Recources remapped successfully.');
                    quietSetTaxonData(taxon.value['tid']);
                    remapTaxonVal.value = null;
                    remapTaxonTid.value = null;
                }
                else{
                    showNotification('negative', 'There was an error remapping the resources.');
                }
            });
        }

        function processRemapTaxonNameChange(taxonData) {
            if(taxonData){
                remapTaxonVal.value = taxonData['sciname'];
                remapTaxonTid.value = taxonData['tid'];
            }
            else{
                remapTaxonVal.value = null;
                remapTaxonTid.value = null;
            }
            if(remapTaxonTid.value && Number(taxonData['kingdomid']) !== Number(taxon.value['kingdomid'])) {
                showNotification('negative', 'The taxon you entered is in a different kingdom than the current taxon. Please ensure it is correct.');
            }
        }

        return {
            clientRoot,
            remapTaxonVal,
            processRemap,
            processRemapTaxonNameChange
        }
    }
};
