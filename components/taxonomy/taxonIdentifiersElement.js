const taxonIdentifiersElement = {
    props: {
        identifierArr: {
            type: Array,
            default: []
        },
        noWrap: {
            type: Boolean,
            default: false
        },
        sciname: {
            type: String,
            default: ''
        }
    },
    template: `
        <template v-if="identifierArr.length > 0">
            <div class="row q-gutter-sm self-center" :class="noWrap ? 'no-wrap' : ''">
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
                        <template v-if="identifier['name'] === 'col' || identifier['name'] === 'eol' || identifier['name'] === 'itis' || identifier['name'] === 'worms'">
                            <q-chip clickable color="primary" text-color="white" @click="openExternalResource(identifier['name'], identifier['identifier']);" :aria-label="('Go to ' + getIdentifierLabel(identifier['name']) + ' page - External link that opens in separate tab')" tabindex="0">
                                <span class="text-bold">{{ getIdentifierLabel(identifier['name']) }}</span>
                            </q-chip>
                        </template>
                        <template v-else>
                            <q-chip color="primary" text-color="white">
                                <span class="text-bold">{{ identifier['name'] + ':' }}</span><span class="q-ml-xs">{{ identifier['identifier'] }}</span>
                            </q-chip>
                        </template>
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

        function getIdentifierLabel(identifierName) {
            let returnVal = '';
            if(identifierName === 'col'){
                returnVal = 'Catalogue of Life';
            }
            else if(identifierName === 'eol'){
                returnVal = 'Encyclopedia of Life';
            }
            else if(identifierName === 'itis'){
                returnVal = 'ITIS';
            }
            else if(identifierName === 'worms'){
                returnVal = 'WoRMS';
            }
            return returnVal;
        }

        function getNonNativeIdentifier() {
            return !!props.identifierArr.find(identifier => identifier['name'] === 'non-native');
        }

        function openExternalResource(identifierName, idVal) {
            let url = null;
            if(idVal){
                if(identifierName === 'col'){
                    url = 'https://www.catalogueoflife.org/data/taxon/' + idVal;
                }
                else if(identifierName === 'eol'){
                    url = 'https://eol.org/pages/' + idVal;
                }
                else if(identifierName === 'itis'){
                    url = 'https://www.itis.gov/servlet/SingleRpt/SingleRpt?search_topic=TSN&search_value=' + idVal;
                }
                else if(identifierName === 'worms'){
                    url = 'https://www.marinespecies.org/aphia.php?p=taxdetails&id=' + idVal + '&marine_only=false';
                }
            }
            if(url){
                window.open(url, '_blank');
            }
        }

        function openOccurrenceListGeneticSearch() {
            window.open((clientRoot + '/collections/occurrenceNavigator.php?interface=list&starr={"hasgenetic":1,"taxa":"' + props.sciname + '"}'), '_blank');
        }

        return {
            getGeneticIdentifier,
            getIdentifierArr,
            getIdentifierLabel,
            getNonNativeIdentifier,
            openExternalResource,
            openOccurrenceListGeneticSearch
        }
    }
};
