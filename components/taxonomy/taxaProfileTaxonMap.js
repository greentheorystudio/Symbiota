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
                            <a :href="taxon.map">
                                <q-img :src="taxon.map" :fit="contain" :title="taxon.sciName" :alt="taxon.sciName"></q-img>
                            </a>
                        </div>
                    </template>
                    <div class="map-thumb-spatial-link">
                        <span class="cursor-pointer" @click="openMapPopup(taxon.sciName,true);">Open Interactive Map</span>
                    </div>
                </div>
            </q-card>
        </div>
    `,
    methods: {
        openMapPopup(taxonVar,clustering){
            const url = CLIENT_ROOT + '/spatial/index.php?starr={"usethes":true,"taxontype":"1","taxa":"' + taxonVar.replaceAll("'",'%squot;') + '"}&clusterpoints=' + (clustering ? 'true' : 'false');
            window.open(url, '_blank');
        }
    }
};
