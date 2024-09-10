const taxaProfileTaxonImageLink = {
    props: {
        taxon: {
            type: Object,
            default: {}
        }
    },
    template: `
        <div class="all-images-link-frame">
            <q-card class="taxon-profile-image-link-card">
                <div class="all-images-link">
                    <span class="cursor-pointer" @click="openImageSearch();">View All {{ taxon.imageCnt }} Images</span>
                </div>
            </q-card>
        </div>
    `,
    setup(props) {
        const store = useBaseStore();
        const clientRoot = store.getClientRoot;

        function openImageSearch() {
            let taxonType;
            if(Number(props.taxon['rankId']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 2;
            }
            const url = clientRoot + '/imagelib/search.php?imagedisplay=thumbnail&submitaction=Load Images&starr={"imagetype":"all","usethes":true,"taxontype":"' + taxonType + '","taxa":"' + props.taxon['sciName'].replaceAll("'",'%squot;') + '"}';
            window.open(url, '_blank');
        }

        return {
            openImageSearch
        }
    }
};
