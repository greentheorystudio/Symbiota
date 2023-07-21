const taxaProfileTaxonMap = {
    props: [
        'taxon'
    ],
    template: `
        <div class="map-thumb-frame">
            <q-card>
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
    methods: {
        openMapPopup(clustering){
            let taxonType;
            if(Number(this.taxon['rankId']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 1;
            }
            const url = CLIENT_ROOT + '/spatial/index.php?starr={"usethes":true,"taxontype":"' + taxonType + '","taxa":"' + this.taxon['sciName'].replaceAll("'",'%squot;') + '"}&clusterpoints=' + (clustering ? 'true' : 'false');
            window.open(url, '_blank');
        }
    }
};
