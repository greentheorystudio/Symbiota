const taxaProfileTaxonMap = {
    props: {
        taxon: {
            type: Object,
            default: {}
        }
    },
    template: `
        <div class="map-thumb-frame">
            <q-card class="taxon-profile-taxon-map-card">
                <div class="map-thumb-container">
                    <template v-if="taxon.map">
                        <div class="map-thumb-image">
                            <a @click="openMapPopup(true);" class="cursor-pointer">
                                <q-img :src="taxon.map" :fit="contain" :title="taxon.sciName" :alt="taxon.sciName"></q-img>
                            </a>
                        </div>
                    </template>
                    <div class="map-thumb-spatial-link">
                        <span class="cursor-pointer" @click="openMapPopup(true);">Open Interactive Map</span>
                    </div>
                </div>
            </q-card>
        </div>
    `,
    setup(props) {
        const store = useBaseStore();
        const clientRoot = store.getClientRoot;

        function openMapPopup(clustering) {
            let taxonType;
            if(Number(props.taxon['rankId']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 1;
            }
            const url = clientRoot + '/spatial/index.php?starr={"usethes":true,"taxontype":"' + taxonType + '","taxa":"' + props.taxon['sciName'].replaceAll("'",'%squot;') + '"}&clusterpoints=' + (clustering ? 'true' : 'false');
            window.open(url, '_blank');
        }

        return {
            openMapPopup
        }
    }
};
