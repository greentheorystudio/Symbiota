const taxaProfileTaxonNotes = {
    props: {
        taxon: {
            type: Object,
            default: {}
        }
    },
    template: `
        <template v-if="taxon.taxonNotes">
            <div>
                <span class="text-weight-bold">Notes:</span> {{ taxon.taxonNotes }}
            </div>
        </template>
    `
};
