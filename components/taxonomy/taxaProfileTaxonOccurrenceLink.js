const taxaProfileTaxonOccurrenceLink = {
    template: `
        <div class="occurrences-link-frame">
            <q-card class="taxon-profile-occurrence-link-card cursor-pointer" @click="openOccurrenceSearch();">
                <div class="occurrences-link">
                    View Occurrence Records
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
            const url = clientRoot + '/collections/list.php?starr={"imagetype":"all","usethes":true,"taxontype":"4","taxa":"' + taxon.value['sciname'].replaceAll("'",'%squot;') + '"}';
            window.open(url, '_blank');
        }

        return {
            openOccurrenceSearch
        }
    }
};
