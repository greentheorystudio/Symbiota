const taxaProfileScinameHeader = {
    template: `
        <div>
            <span class="taxon-profile-sciname"><span :class="taxonStyleClass">{{ acceptedTaxon.sciname }}</span></span> <span>{{ acceptedTaxon.author }}</span>
            <a :href="(clientRoot + '/taxa/index.php?taxon=' + acceptedTaxon.parenttid)" class="parent-link" title="Go to Parent">
                <q-icon name="fas fa-level-up-alt" size="15px" class="cursor-pointer" />
            </a>
            <template v-if="Number(acceptedTaxon['tid']) !== Number(taxon['tid'])">
                <span class="redirected-from"> (redirected from: <span class="text-italic">{{ taxon['sciname'] }}</span>)</span>
            </template>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const acceptedTaxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);
        const clientRoot = baseStore.getClientRoot;
        const taxon = Vue.computed(() => taxaStore.getTaxaData);
        const taxonStyleClass = Vue.computed(() => {
            let styleClass;
            if(Number(taxon.value['rankid']) > 180){
                styleClass = 'species';
            }
            else if(Number(taxon.value['rankid']) === 180){
                styleClass = 'genus';
            }
            else{
                styleClass = 'higher';
            }
            return styleClass;
        });

        return {
            acceptedTaxon,
            clientRoot,
            taxon,
            taxonStyleClass
        }
    }
};
