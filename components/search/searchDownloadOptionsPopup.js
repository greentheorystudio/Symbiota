const searchDownloadOptionsPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="sm-popup">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    <div class="text-h6 text-bold">Download Data</div>
                    <div class="text-body1">
                        By downloading data, the user confirms that he/she has read and agrees with the 
                        <a :href="(clientRoot + '/misc/usagepolicy.php')" target="_blank" aria-label="View usage terms - Opens in separate tab" tabindex="0">general data usage terms</a>.
                        Note that additional terms of use specific to the individual collections may be distributed with the 
                        data download. When present, the terms supplied by the owning institution should take precedence over 
                        the general terms posted on the website.
                    </div>
                    <div class="q-mb-md row">
                        <div class="col-8">
                            <selector-input-element class="download-type-dropdown" :options="downloadTypeOptions" label="Download Type" :value="selectedDownloadType" @update:value="updateSelectedDownloadType"></selector-input-element>
                        </div>
                    </div>
                    <template v-if="selectedDownloadType && selectedDownloadType !== 'taxa' && selectedDownloadType !== 'fasta' && selectedDownloadType !== 'media'">
                        <div class="row">
                            <div class="col-5">
                                <div class="text-body1 text-bold">Data Structure</div>
                                <a class="q-pa-none" href="https://www.tdwg.org/standards/dwc/" target="_blank">What is Darwin Core?</a>
                            </div>
                            <div class="col-7">
                                <q-option-group v-model="selectedDataStructure" :options="dataStructureOptions" color="primary" dense aria-label="Data Structure options" tabindex="0"></q-option-group>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5">
                                <div class="text-body1 text-bold">Data Extensions</div>
                            </div>
                            <div class="col-7 column">
                                <template v-if="selectedDownloadType === 'zip'">
                                    <checkbox-input-element label="Include Determination Records" :value="includeDeterminations" @update:value="(value) => includeDeterminations = value"></checkbox-input-element>
                                    <checkbox-input-element label="Include Genetic Records" :value="includeGenetic" @update:value="(value) => includeGenetic = value"></checkbox-input-element>
                                    <checkbox-input-element label="Include Media Records" :value="includeMedia" @update:value="(value) => includeMedia = value"></checkbox-input-element>
                                </template>
                                <checkbox-input-element label="Include MoF Data" :value="includeMof" @update:value="(value) => includeMof = value"></checkbox-input-element>
                            </div>
                        </div>
                    </template>
                    <div class="row justify-end">
                        <div>
                            <q-btn color="primary" size="md" @click="downloadData();" label="Download" :disabled="!selectedDownloadType" dense tabindex="0" />
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement
    },
    setup(_, context) {
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const clientRoot = baseStore.getClientRoot;
        const dataStructureOptions = Vue.ref([
            {value: 'dwc', label: 'Darwin Core'},
            {value: 'native', label: 'Symbiota'}
        ]);
        const downloadTypeOptions = Vue.computed(() => {
            const returnArr = [
                {value: 'csv', label: 'CSV'},
                {value: 'zip', label: 'ZIP'},
                {value: 'kml', label: 'KML'},
                {value: 'geojson', label: 'GeoJSON'},
                {value: 'gpx', label: 'GPX'},
                {value: 'taxa', label: 'Taxa List'},
                {value: 'fasta', label: 'FASTA'}
            ];
            if(Number(searchImageCount.value) > 0){
                returnArr.push({value: 'media', label: 'Media'});
            }
            return returnArr;
        });
        const includeDeterminations = Vue.ref(false);
        const includeGenetic = Vue.ref(false);
        const includeMedia = Vue.ref(false);
        const includeMof = Vue.ref(false);
        const searchImageCount = Vue.computed(() => searchStore.getSearchImgCount);
        const selectedDataStructure = Vue.ref('dwc');
        const selectedDownloadType = Vue.ref(null);

        function closePopup() {
            context.emit('close:popup');
        }

        function downloadData() {
            context.emit('update:download-options', {
                type: selectedDownloadType.value,
                structure: selectedDataStructure.value,
                includeDet: includeDeterminations.value,
                includeGenetic: includeGenetic.value,
                includeMedia: includeMedia.value,
                includeMof: includeMof.value
            });
        }

        function processTypeChange(val) {
            if(val === 'csv'){
                includeDeterminations.value = false;
                includeGenetic.value = false;
                includeMedia.value = false;
            }
            selectedDownloadType.value = val;
        }

        function updateSelectedDownloadType(value) {
            selectedDownloadType.value = value;
        }

        return {
            clientRoot,
            dataStructureOptions,
            downloadTypeOptions,
            includeDeterminations,
            includeGenetic,
            includeMedia,
            includeMof,
            selectedDataStructure,
            selectedDownloadType,
            closePopup,
            downloadData,
            processTypeChange,
            updateSelectedDownloadType
        }
    }
};
