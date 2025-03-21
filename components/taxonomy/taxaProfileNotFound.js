const taxaProfileNotFound = {
    template: `
        <div class="q-mt-xl q-ml-lg">
            <h2><span class="text-italic">{{ taxaStr }}</span> not found</h2>
            <div class="q-ml-md text-subtitle1 text-weight-bold">
                <template v-if="fuzzyMatches.length">
                    Did you mean?
                    <div class="q-ml-lg">
                        <template v-for="match in fuzzyMatches">
                            <a :href="(clientRoot + '/taxa/index.php?taxon=' + match.tid)">{{ match.sciname }}</a><br/>
                        </template>
                    </div>
                </template>
                <template v-else>
                    There are no close matches to that axon in the Taxonomic Thesaurus
                </template>
            </div>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const fuzzyMatches = Vue.computed(() => taxaStore.getTaxaFuzzyMatches);
        const taxaStr = Vue.computed(() => taxaStore.getTaxaStr);

        return {
            clientRoot,
            fuzzyMatches,
            taxaStr
        }
    }
};
