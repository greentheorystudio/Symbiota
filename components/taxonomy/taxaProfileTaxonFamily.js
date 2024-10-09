const taxaProfileTaxonFamily = {
    props: {
        taxon: {
            type: Object,
            default: {}
        }
    },
    template: `
        <template v-if="taxon.rankId > 140 && taxon.family">
            <div>
                <span class="text-weight-bold">Family:</span> {{ taxon.family }}
            </div>
        </template>
    `
};
