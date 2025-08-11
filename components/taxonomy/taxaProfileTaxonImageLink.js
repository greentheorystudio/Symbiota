const taxaProfileTaxonImageLink = {
    template: `
        <div class="all-images-link-frame">
            <q-card class="taxon-profile-image-link-card cursor-pointer" @click="openImageSearch();">
                <div class="all-images-link">
                    View All {{ taxaImageCount }} Images
                </div>
            </q-card>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const taxaImageCount = Vue.computed(() => taxaStore.getTaxaImageCount);
        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);

        function openImageSearch() {
            const url = clientRoot + '/imagelib/search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"taxontype":"4","taxa":"' + taxon.value['sciname'].replaceAll("'",'%squot;') + '"}';
            window.open(url, '_blank');
        }

        return {
            taxaImageCount,
            taxon,
            openImageSearch
        }
    }
};
