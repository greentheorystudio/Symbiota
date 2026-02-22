const taxaProfileTaxonFamily = {
    template: `
        <template v-if="taxon.rankid > 140 && taxon.family">
            <div>
                <span class="text-bold">Family:</span> {{ taxon.family }}
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
