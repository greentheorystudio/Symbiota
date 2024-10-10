const searchDataDownloader = {
    props: {
        selection: {
            type: Boolean,
            default: false
        },
        taxaId: {
            type: Number,
            default: null
        },
        taxaType: {
            type: Number,
            default: null
        }
    },
    template: `
        <q-select bg-color="white" outlined v-model="selectedOption" :options="selectorOptions" option-value="value" option-label="label" label="Download Type" popup-content-class="z-max" class="download-type-dropdown" behavior="menu" dense options-dense />
        <div class="self-center">
            <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processDownload();" icon="fas fa-download" dense>
                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                    Download Records
                </q-tooltip>
            </q-btn>
        </div>
    `,
    setup(props) {
        const searchStore = useSearchStore();

        const selectedOption = Vue.ref(null);
        const selectorOptions = [
            {value: 'csv', label: 'CSV'},
            {value: 'kml', label: 'KML'},
            {value: 'geojson', label: 'GeoJSON'},
            {value: 'gpx', label: 'GPX'}
        ];

        const { showNotification } = useCore();

        function processDownload(){
            if(selectedOption.value){
                searchStore.processDownloadRequest({
                    dlType: selectedOption.value.value,
                    selection: props.selection,
                    taxaId: props.taxaId,
                    taxaType: props.taxaType
                });
            }
            else{
                showNotification('negative','Please select a download type.');
            }
        }

        return {
            selectedOption,
            selectorOptions,
            processDownload
        }
    }
};
