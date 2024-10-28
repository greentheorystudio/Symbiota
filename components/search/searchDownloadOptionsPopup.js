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
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div class="q-mt-sm q-pa-md column q-gutter-sm">
                    <div class="text-h6 text-bold">Download Occurrence Data</div>
                    <div class="text-body1">
                        By downloading data, the user confirms that he/she has read and agrees with the 
                        <a :href="(clientRoot + '/misc/usagepolicy.php')" target="_blank">general data usage terms</a>.
                        Note that additional terms of use specific to the individual collections may be distributed with the 
                        data download. When present, the terms supplied by the owning institution should take precedence over 
                        the general terms posted on the website.
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <div class="text-body1 text-bold">Download Type</div>
                        </div>
                        <div class="col-7">
                            <q-option-group v-model="selectedDownloadType" :options="downloadTypeOptions" color="primary" dense></q-option-group>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <div class="text-body1 text-bold">Data Structure</div>
                            <a class="q-pa-none" href="https://www.tdwg.org/standards/dwc/" target="_blank">What is Darwin Core?</a>
                        </div>
                        <div class="col-7">
                            <q-option-group v-model="selectedDataStructure" :options="dataStructureOptions" color="primary" dense></q-option-group>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <div class="text-body1 text-bold">Data Extensions</div>
                        </div>
                        <div class="col-7 column">
                            <checkbox-input-element label="Include Determination History" :value="includeDeterminations" @update:value="(value) => includeDeterminations = value" :disabled="selectedDownloadType === 'csv'"></checkbox-input-element>
                            <checkbox-input-element label="Include Image Records" :value="includeImages" @update:value="(value) => includeImages = value" :disabled="selectedDownloadType === 'csv'"></checkbox-input-element>
                        </div>
                    </div>
                    <div class="row justify-end">
                        <div>
                            <q-btn color="primary" size="md" @click="downloadData();" label="Download Data" dense />
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement
    },
    setup(_, context) {
        const baseStore = useBaseStore();

        const clientRoot = baseStore.getClientRoot;
        const dataStructureOptions = Vue.ref([
            {value: 'native', label: 'Symbiota'},
            {value: 'dwc', label: 'Darwin Core'}
        ]);
        const downloadTypeOptions = Vue.ref([
            {value: 'csv', label: 'CSV'},
            {value: 'zip', label: 'ZIP'}
        ]);
        const includeDeterminations = Vue.ref(false);
        const includeImages = Vue.ref(false);
        const selectedDataStructure = Vue.ref('native');
        const selectedDownloadType = Vue.ref('csv');

        function closePopup() {
            context.emit('close:popup');
        }

        function downloadData() {
            context.emit('update:download-options', {
                type: selectedDownloadType.value,
                structure: selectedDataStructure.value,
                includeDet: includeDeterminations.value,
                includeImage: includeImages.value
            });
        }

        return {
            clientRoot,
            dataStructureOptions,
            downloadTypeOptions,
            includeDeterminations,
            includeImages,
            selectedDataStructure,
            selectedDownloadType,
            closePopup,
            downloadData
        }
    }
};
