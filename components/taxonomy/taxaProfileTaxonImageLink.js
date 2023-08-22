const taxaProfileTaxonImageLink = {
    props: [
        'taxon'
    ],
    template: `
        <div class="all-images-link-frame">
            <q-card class="taxon-profile-image-link-card">
                <div class="all-images-link">
                    <span class="cursor-pointer" @click="openImageSearch();">View All {{ taxon.imageCnt }} Images</span>
                </div>
            </q-card>
        </div>
    `,
    methods: {
        openImageSearch(){
            let taxonType;
            if(Number(this.taxon['rankId']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 2;
            }
            const url = CLIENT_ROOT + '/imagelib/search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"taxontype":"' + taxonType + '","taxa":"' + this.taxon['sciName'].replaceAll("'",'%squot;') + '"}';
            window.open(url, '_blank');
        }
    }
};
