const taxaProfileTaxonImageLink = {
    template: `
        <div class="all-images-link-frame">
            <q-card class="taxon-profile-image-link-card">
                <div class="all-images-link">
                    <span class="cursor-pointer" @click="openImageSearch();">View All {{ taxaImageCount }} Images</span>
                </div>
            </q-card>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const taxaImageCount = Vue.computed(() => taxaStore.getTaxaImageArr);
        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);

        function openImageSearch() {
            let taxonType;
            if(Number(taxon.value['rankid']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 2;
            }
            const url = clientRoot + '/imagelib/search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"taxontype":"' + taxonType + '","taxa":"' + taxon.value['sciname'].replaceAll("'",'%squot;') + '"}';
            window.open(url, '_blank');
        }

        return {
            taxaImageCount,
            taxon,
            openImageSearch
        }
    }
};
