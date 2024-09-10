const taxaProfileTaxonOccurrenceLink = {
    props: {
        taxon: {
            type: Object,
            default: {}
        }
    },
    template: `
        <div class="occurrences-link-frame">
            <q-card class="taxon-profile-occurrence-link-card">
                <div class="occurrences-link">
                    <span class="cursor-pointer" @click="openOccurrenceSearch();">View Occurrence Records</span>
                </div>
            </q-card>
        </div>
    `,
    setup(props) {
        const store = useBaseStore();
        const clientRoot = store.getClientRoot;

        function openOccurrenceSearch() {
            let taxonType;
            if(Number(props.taxon['rankId']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 1;
            }
            const url = clientRoot + '/collections/list.php?starr={"imagetype":"all","usethes":true,"taxontype":"' + taxonType + '","taxa":"' + props.taxon['sciName'].replaceAll("'",'%squot;') + '"}';
            window.open(url, '_blank');
        }

        return {
            openOccurrenceSearch
        }
    }
};
