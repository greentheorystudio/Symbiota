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
        <div class="download-type-dropdown">
            <selector-input-element :options="downloadTypeOptions" label="Download Type" :value="selectedDownloadType" @update:value="updateSelectedDownloadType"></selector-input-element>
        </div>
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="initializeDownload();" icon="fas fa-download" dense>
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Download Records
                </q-tooltip>
            </q-btn>
        </div>
        <search-download-options-popup :show-popup="displayOptionsPopup" @update:download-options="processDownloadOptions" @close:popup="displayOptionsPopup = false"></search-download-options-popup>
    `,
    components: {
        'search-download-options-popup': searchDownloadOptionsPopup,
        'selector-input-element': selectorInputElement,
    },
    setup(props) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const searchStore = useSearchStore();

        const displayOptionsPopup = Vue.ref(false);
        const downloadTypeOptions = [
            {value: 'csv', label: 'CSV/ZIP'},
            {value: 'kml', label: 'KML'},
            {value: 'geojson', label: 'GeoJSON'},
            {value: 'gpx', label: 'GPX'},
            {value: 'taxa', label: 'Taxa List'}
        ];
        const requestOptions = {
            selections: props.selections,
            schema: 'occurrence',
            spatial: props.spatial,
            type: null,
            output: null
        };
        const selectedDownloadType = Vue.ref(null);

        function initializeDownload(){
            if(selectedDownloadType.value){
                if(selectedDownloadType.value === 'csv'){
                    displayOptionsPopup.value = true;
                }
                else{
                    processDownload();
                }
            }
            else{
                showNotification('negative','Please select a download type.');
            }
        }

        function processDownload(){
            showWorking();
            searchStore.processDownloadRequest(requestOptions, (filename, dataBlob) => {
                hideWorking();
                if(dataBlob !== null){
                    const objectUrl = window.URL.createObjectURL(dataBlob);
                    const anchor = document.createElement('a');
                    anchor.href = objectUrl;
                    anchor.download = filename;
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
            requestOptions.images = options['includeImage'];
            displayOptionsPopup.value = false;
            processDownload();
        }

        function updateSelectedDownloadType(value) {
            selectedDownloadType.value = value;
            if(value === 'csv' || value === 'taxa'){
                requestOptions.type = 'csv';
                if(value === 'taxa'){
                    requestOptions.schema = value;
                }
                requestOptions.output = null;
            }
            else{
                requestOptions.output = value;
                requestOptions.type = value;
            }
        }

        return {
            displayOptionsPopup,
            downloadTypeOptions,
            selectedDownloadType,
            initializeDownload,
            processDownloadOptions,
            updateSelectedDownloadType
        }
    }
};
