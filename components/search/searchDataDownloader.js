const searchDataDownloader = {
    props: {
        selections: {
            type: Boolean,
            default: false
        },
        spatial: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <div class="row">
            <div class="self-center">
                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayOptionsPopup = true" label="Download Data" icon="fas fa-download" dense aria-label="Download Records" tabindex="0">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Download Records
                    </q-tooltip>
                </q-btn>
            </div>
        </div>
        <search-download-options-popup :show-popup="displayOptionsPopup" @update:download-options="processDownloadOptions" @close:popup="displayOptionsPopup = false"></search-download-options-popup>
    `,
    components: {
        'search-download-options-popup': searchDownloadOptionsPopup
    },
    setup(props) {
        const { hideWorking, processCsvDownload, showWorking } = useCore();
        const searchStore = useSearchStore();

        const displayOptionsPopup = Vue.ref(false);
        const requestOptions = {
            selections: props.selections,
            schema: 'occurrence',
            spatial: props.spatial,
            type: null,
            output: null
        };

        function processDownload(){
            showWorking();
            searchStore.processDownloadRequest(requestOptions, (filename, dataBlob) => {
                hideWorking();
                if(dataBlob !== null){
                    const objectUrl = window.URL.createObjectURL(dataBlob);
                    const anchor = document.createElement('a');
                    anchor.href = objectUrl;
                    anchor.download = (filename + '.' + requestOptions['type']);
                    document.body.appendChild(anchor);
                    anchor.click();
                    anchor.remove();
                }
            });
        }

        function processDownloadOptions(options){
            requestOptions.type = options.type;
            requestOptions.schema = options.structure;
            requestOptions.identifications = options['includeDet'];
            requestOptions.genetic = options['includeGenetic'];
            requestOptions.media = options['includeMedia'];
            requestOptions.mof = options['includeMof'];
            if(requestOptions.type === 'gpx'){
                requestOptions.schema = 'native';
                requestOptions.mof = false;
            }
            if(requestOptions.type === 'csv' || requestOptions.type === 'media' || requestOptions.type === 'taxa'){
                requestOptions.type = 'csv';
                if(requestOptions.type === 'taxa'){
                    requestOptions.schema = requestOptions.type;
                }
                else if(requestOptions.type === 'media'){
                    requestOptions.schema = 'image';
                }
                requestOptions.output = null;
            }
            else{
                requestOptions.output = requestOptions.type;
            }
            displayOptionsPopup.value = false;
            if(requestOptions.type === 'taxa'){
                processTaxaDownload();
            }
            else{
                processDownload();
            }
        }

        function processTaxaDownload(){
            showWorking();
            const filename = 'occurrence_search_taxa_list_' + searchStore.getDateTimeString;
            searchStore.processSearch(requestOptions, (res) => {
                hideWorking();
                processCsvDownload(res, filename);
            });
        }

        return {
            displayOptionsPopup,
            processDownloadOptions
        }
    }
};
