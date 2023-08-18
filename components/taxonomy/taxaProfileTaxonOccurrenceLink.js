const taxaProfileTaxonOccurrenceLink = {
    props: [
        'taxon'
    ],
    template: `
        <div class="occurrences-link-frame">
            <q-card class="taxon-profile-occurrence-link-card">
                <div class="occurrences-link">
                    <span class="cursor-pointer" @click="openOccurrenceSearch();">View Occurrence Records</span>
                </div>
            </q-card>
        </div>
    `,
    methods: {
        openOccurrenceSearch(){
            let taxonType;
            if(Number(this.taxon['rankId']) < 140){
                taxonType = 4;
            }
            else{
                taxonType = 1;
            }
            const url = CLIENT_ROOT + '/collections/list.php?starr={"imagetype":"all","usethes":true,"taxontype":"' + taxonType + '","taxa":"' + this.taxon['sciName'].replaceAll("'",'%squot;') + '"}';
            window.open(url, '_blank');
        }
    }
};
