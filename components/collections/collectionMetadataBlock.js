const collectionMetadataBlock = {
    props: {
        collectionData: {
            type: Object,
            default: {}
        }
    },
    template: `
        <div class="column">
            <div v-if="collectionData['fulldescription']">
                {{ collectionData['fulldescription'] }}
            </div>
            <div v-if="collectionData['contact'] || collectionData['email']">
                <span class="text-h6 text-bold">Contact: </span>
                <span v-if="collectionData['contact']">{{ collectionData['contact'] }} </span>
                <span v-if="collectionData['contact'] && collectionData['email']">(</span>
                <span v-if="collectionData['email']">{{ collectionData['email'] }}</span>
                <span v-if="collectionData['contact'] && collectionData['email']">)</span>
            </div>
            <div v-if="collectionData['homepage']">
                <span class="text-h6 text-bold">Home Page: </span>
                <a :href="collectionData['homepage']" target="_blank">
                    {{ collectionData['homepage'] }}
                </a>
            </div>
            <div v-if="collectionData['colltype']">
                <span class="text-h6 text-bold">Collection Type: </span>{{ collectionData['colltype'] }}
            </div>
            <div v-if="collectionData['managementtype']">
                <span class="text-h6 text-bold">Management: </span>
                <span v-if="collectionData['managementtype'] === 'Live Data'">Live Data managed directly within data portal</span>
                <span v-else-if="collectionData['managementtype'] === 'Aggregate'">Data harvested from a data aggregator</span>
                <span v-else>Data snapshot of local collection database</span>
            </div>
            <div v-if="collectionData['uploaddate']">
                <span class="text-h6 text-bold">Last Update: </span>{{ collectionData['uploaddate'] }}
            </div>
            <div v-if="collectionData['managementtype'] && collectionData['managementtype'] === 'Live Data' && collectionData['guid']">
                <span class="text-h6 text-bold">Global Unique Identifier: </span>{{ collectionData['guid'] }}
            </div>
            <div v-if="collectionData['dwcaurl']">
                <span class="text-h6 text-bold">DwC-Archive Publishing: </span>
                <a :href="(clientRoot + '/collections/datasets/datapublisher.php')">
                    {{ (clientRoot + '/collections/datasets/datapublisher.php') }}
                </a>
            </div>
            <div v-if="collectionData['managementtype'] && collectionData['managementtype'] === 'Live Data'">
                <span class="text-h6 text-bold">Live Data Download: </span>
                <a :href="(clientRoot + '/webservices/dwc/dwcapubhandler.php?collid=' + collectionData['collid'])">
                    DwC-Archive File
                </a>
            </div>
            <div>
                <span class="text-h6 text-bold">Digital Metadata: </span>
                <a :href="(clientRoot + '/collections/datasets/emlhandler.php?collid=' + collectionData['collid'])" target="_blank">
                    EML File
                </a>
            </div>
            <div v-if="collectionData['rights'] && rightsTerms.hasOwnProperty(collectionData['rights'])">
                <span class="text-h6 text-bold">Usage Rights: </span>
                <a :href="collectionData['rights']" target="_blank">
                    {{ rightsTerms[collectionData['rights']]['title'] }}
                </a>
            </div>
            <div v-if="collectionData['rightsholder']">
                <span class="text-h6 text-bold">Rights Holder: </span>{{ collectionData['rightsholder'] }}
            </div>
            <div v-if="collectionData['accessrights']">
                <span class="text-h6 text-bold">Access Rights: </span>{{ collectionData['accessrights'] }}
            </div>
        </div>
    `,
    setup() {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;
        const rightsTerms = baseStore.getRightsTerms;

        return {
            clientRoot,
            rightsTerms
        }
    }
};
