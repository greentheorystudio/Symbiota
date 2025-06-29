const taxaProfileTaxonMap = {
    template: `
        <div class="map-thumb-frame">
            <q-card class="taxon-profile-taxon-map-card cursor-pointer" @click="openMapPopup(true);">
                <div class="map-thumb-container">
                    <template v-if="taxonMap">
                        <div class="map-thumb-image">
                            <a @click="openMapPopup(true);" class="cursor-pointer">
                                <q-img :src="taxonMap['url']" :fit="contain" :title="taxon.sciname" :alt="taxon.sciname"></q-img>
                            </a>
                        </div>
                    </template>
                    <div class="map-thumb-spatial-link">
                        Open Interactive Map
                    </div>
                </div>
            </q-card>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const taxaMapData = Vue.computed(() => taxaStore.getTaxaMapArr);
        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);
        const taxonId = Vue.computed(() => taxaStore.getAcceptedTaxonTid);
        const taxonMap = Vue.computed(() => {
            return taxaMapData.value.hasOwnProperty(taxonId.value) ? taxaMapData.value[taxonId.value] : null;
        });

        function openMapPopup(clustering) {
            let taxonType;
            if(Number(taxon.value['rankid']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 1;
            }
            const url = clientRoot + '/spatial/index.php?starr={"usethes":true,"taxontype":"' + taxonType + '","taxa":"' + taxon.value['sciname'].replaceAll("'",'%squot;') + '"}&clusterpoints=' + (clustering ? 'true' : 'false');
            window.open(url, '_blank');
        }

        return {
            taxon,
            taxonMap,
            openMapPopup
        }
    }
};
