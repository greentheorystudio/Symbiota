const collectionMetadataBlock = {
    props: {
        collectionData: {
            type: Object,
            default: {}
        }
    },
    template: `
        <div class="column">
            <div v-if="collectionData['fulldescription']" v-html="collectionData['fulldescription']"></div>
            <div v-if="collectionData['contact'] || collectionData['email']">
                <span class="text-body1 text-bold">Contact: </span>
                <span v-if="collectionData['contact']">{{ collectionData['contact'] }} </span>
                <span v-if="collectionData['contact'] && collectionData['email']">(</span>
                <span v-if="collectionData['email']">{{ collectionData['email'] }}</span>
                <span v-if="collectionData['contact'] && collectionData['email']">)</span>
            </div>
            <div v-if="collectionData['homepage']">
                <span class="text-body1 text-bold">Home Page: </span>
                <a :href="collectionData['homepage']" target="_blank">
                    {{ collectionData['homepage'] }}
                </a>
            </div>
            <div v-if="collectionData['colltype']">
                <span class="text-body1 text-bold">Collection Type: </span>{{ collectionData['colltype'] }}
            </div>
            <div v-if="collectionData['managementtype']">
                <span class="text-body1 text-bold">Management: </span>
                <span v-if="collectionData['managementtype'] === 'Live Data'">Live Data managed directly within data portal</span>
                <span v-else-if="collectionData['managementtype'] === 'Aggregate'">Data harvested from a data aggregator</span>
                <span v-else>Data snapshot of local collection database</span>
            </div>
            <div v-if="collectionData['uploaddate']">
                <span class="text-body1 text-bold">Last Update: </span>{{ collectionData['uploaddate'] }}
            </div>
            <div v-if="collectionData['managementtype'] && collectionData['managementtype'] === 'Live Data' && collectionData['guid']">
                <span class="text-body1 text-bold">Global Unique Identifier: </span>{{ collectionData['guid'] }}
            </div>
            <div v-if="collectionData['dwcaurl']">
                <span class="text-body1 text-bold">DwC-Archive Publishing: </span>
                <a :href="(clientRoot + '/collections/datasets/datapublisher.php')">
                    {{ (clientRoot + '/collections/datasets/datapublisher.php') }}
                </a>
            </div>
            <div v-if="collectionData['managementtype'] && collectionData['managementtype'] === 'Live Data'">
                <span class="text-body1 text-bold">Live Data Download: </span>
                <a class="cursor-pointer" @click="processLiveDataDownload(collectionData['collid']);">
                    DwC-Archive File
                </a>
            </div>
            <div>
                <span class="text-body1 text-bold">Digital Metadata: </span>
                <a :href="(clientRoot + '/collections/datasets/emlhandler.php?collid=' + collectionData['collid'])" target="_blank">
                    EML File
                </a>
            </div>
            <div v-if="collectionData['rights'] && rightsTerms.hasOwnProperty(collectionData['rights'])">
                <span class="text-body1 text-bold">Usage Rights: </span>
                <a :href="collectionData['rights']" target="_blank">
                    {{ rightsTerms[collectionData['rights']]['title'] }}
                </a>
            </div>
            <div v-if="collectionData['rightsholder']">
                <span class="text-body1 text-bold">Rights Holder: </span>{{ collectionData['rightsholder'] }}
            </div>
            <div v-if="collectionData['accessrights']">
                <span class="text-body1 text-bold">Access Rights: </span>{{ collectionData['accessrights'] }}
            </div>
        </div>
    `,
    setup() {
        const { hideWorking, showWorking } = useCore();
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const clientRoot = baseStore.getClientRoot;
        const rightsTerms = baseStore.getRightsTerms;

        function processLiveDataDownload(collid){
            showWorking();
            const requestOptions = {
                filename: ('occurrence_data_DwCA_' + searchStore.getDateTimeString),
                identifications: 1,
                media: 1,
                mof: 1,
                schema: 'dwc',
                spatial: false,
                type: 'zip',
                output: null
            };
            const formData = new FormData();
            formData.append('starr', '{"db":["' + Number(collid) + '"]}');
            formData.append('options', JSON.stringify(requestOptions));
            formData.append('action', 'processSearchDownload');
            fetch(searchServiceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.blob() : null;
            })
            .then((blob) => {
                hideWorking();
                if(blob !== null){
                    const objectUrl = window.URL.createObjectURL(blob);
                    const anchor = document.createElement('a');
                    anchor.href = objectUrl;
                    anchor.download = requestOptions.filename;
                    document.body.appendChild(anchor);
                    anchor.click();
                    anchor.remove();
                }
            });
        }

        return {
            clientRoot,
            rightsTerms,
            processLiveDataDownload
        }
    }
};
