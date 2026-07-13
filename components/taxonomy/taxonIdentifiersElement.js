const taxonIdentifiersElement = {
    props: {
        identifierArr: {
            type: Array,
            default: []
        },
        sciname: {
            type: String,
            default: ''
        }
    },
    template: `
        <template v-if="identifierArr.length > 0">
            <div class="row q-gutter-sm">
                <template v-if="getGeneticIdentifier()">
                    <q-chip clickable color="teal" text-color="white" class="text-bold cursor-pointer" @click="openOccurrenceListGeneticSearch();" :aria-label="('View occurrence records with associated genetic data for ' + sciname + ' in occurrence list display - Opens in separate tab')" tabindex="0">
                        Genetic Data
                    </q-chip>
                </template>
                <template v-else-if="getNonNativeIdentifier()">
                    <q-chip color="red" text-color="white" icon="block" class="text-bold">
                        Non-native
                    </q-chip>
                </template>
                <template v-if="getIdentifierArr().length > 0">
                    <template v-for="identifier in getIdentifierArr()">
                        <q-chip color="primary" text-color="white">
                            <span class="text-bold">{{ identifier['name'] + ':' }}</span><span class="q-ml-xs">{{ identifier['identifier'] }}</span>
                        </q-chip>
                    </template>
                </template>
            </div>
        </template>
    `,
    setup(props) {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;

        function getGeneticIdentifier() {
            return !!props.identifierArr.find(identifier => identifier['name'] === 'genetic-data-available');
        }

        function getIdentifierArr() {
            const returnArr = [];
            props.identifierArr.forEach(identifier => {
                if(identifier['name'] !== 'genetic-data-available' && identifier['name'] !== 'non-native'){
                    returnArr.push(identifier);
                }
            });
            return returnArr;
        }

        function getNonNativeIdentifier() {
            return !!props.identifierArr.find(identifier => identifier['name'] === 'non-native');
        }

        function openOccurrenceListGeneticSearch() {
            window.open((clientRoot + '/collections/occurrenceNavigator.php?interface=list&starr={"hasgenetic":1,"taxa":"' + props.sciname + '"}'), '_blank');
        }

        return {
            getGeneticIdentifier,
            getIdentifierArr,
            getNonNativeIdentifier,
            openOccurrenceListGeneticSearch
        }
    }
};
