const taxaProfileTaxonOccurrenceLink = {
    template: `
        <div class="occurrences-link-frame">
            <q-card class="taxon-profile-occurrence-link-card">
                <div class="occurrences-link">
                    <span class="cursor-pointer" @click="openOccurrenceSearch();">View Occurrence Records</span>
                </div>
            </q-card>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);

        function openOccurrenceSearch() {
            let taxonType;
            if(Number(taxon.value['rankid']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 1;
            }
            const url = clientRoot + '/collections/list.php?starr={"imagetype":"all","usethes":true,"taxontype":"' + taxonType + '","taxa":"' + taxon.value['sciname'].replaceAll("'",'%squot;') + '"}';
            window.open(url, '_blank');
        }

        return {
            openOccurrenceSearch
        }
    }
};
