const taxaProfileTaxonNotes = {
    props: [
        'taxon'
    ],
    template: `
        <template v-if="taxon.taxonNotes">
            <div id="taxonnotes">
                <span class="text-weight-bold">Notes:</span> {{ taxon.taxonNotes }}
            </div>
        </template>
    `
};
