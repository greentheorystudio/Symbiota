const taxaProfileTaxonIdentifiers = {
    template: `
        <template v-if="taxaIdentifiers.length > 0">
            <div class="row q-gutter-sm">
                <template v-if="getGeneticIdentifier(taxaIdentifiers)">
                    <q-chip clickable color="teal" text-color="white" class="text-bold cursor-pointer" @click="openOccurrenceListGeneticSearch(taxon.sciname);" :aria-label="('View occurrence records with associated genetic data for ' + taxon.sciname + ' in occurrence list display - Opens in separate tab')" tabindex="0">
                        Genetic Data
                    </q-chip>
                </template>
                <template v-else-if="getNonNativeIdentifier(taxaIdentifiers)">
                    <q-chip color="red" text-color="white" icon="block" class="text-bold">
                        Non-native
                    </q-chip>
                </template>
                <template v-if="getIdentifierArr(taxaIdentifiers).length > 0">
                    <template v-for="identifier in getIdentifierArr(taxaIdentifiers)">
                        <q-chip color="primary" text-color="white">
                            <span class="text-bold">{{ identifier['name'] + ':' }}</span><span class="q-ml-xs">{{ identifier['identifier'] }}</span>
                        </q-chip>
                    </template>
                </template>
            </div>
        </template>
    `,
    setup() {
        const baseStore = useBaseStore();
        const taxaStore = useTaxaStore();

        const clientRoot = baseStore.getClientRoot;
        const taxaIdentifiers = Vue.computed(() => taxaStore.getTaxaIdentifiers);
        const taxon = Vue.computed(() => taxaStore.getAcceptedTaxonData);

        function getGeneticIdentifier(identifierArr) {
            return !!identifierArr.find(identifier => identifier['name'] === 'genetic-data-available');
        }

        function getIdentifierArr(identifierArr) {
            const returnArr = [];
            identifierArr.forEach(identifier => {
                if(identifier['name'] !== 'genetic-data-available' && identifier['name'] !== 'non-native'){
                    returnArr.push(identifier);
                }
            });
            return returnArr;
        }

        function getNonNativeIdentifier(identifierArr) {
            return !!identifierArr.find(identifier => identifier['name'] === 'non-native');
        }

        function openOccurrenceListGeneticSearch(sciname) {
            window.open((clientRoot + '/collections/occurrenceNavigator.php?interface=list&starr={"hasgenetic":1,"taxa":"' + sciname + '"}'), '_blank');
        }

        return {
            taxaIdentifiers,
            taxon,
            getGeneticIdentifier,
            getIdentifierArr,
            getNonNativeIdentifier,
            openOccurrenceListGeneticSearch
        }
    }
};
