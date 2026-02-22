const taxaProfileTaxonNotes = {
    template: `
        <template v-if="taxon.notes">
            <div>
                <span class="text-weight-bold">Notes:</span> {{ taxon.notes }}
            </div>
        </template>
    `,
    setup() {
        const taxaStore = useTaxaStore();

        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);

        return {
            taxon
        }
    }
};
