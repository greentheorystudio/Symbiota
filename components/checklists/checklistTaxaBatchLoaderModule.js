const checklistTaxaBatchLoaderModule = {
    template: `
        <div class="q-pa-md column q-col-gutter-sm">
            <div class="q-mb-md">
                Taxa can be batch uploaded to this checklist 
                <a :href="(clientRoot + '/templates/batchChecklistTaxaData.csv')" aria-label="Download batch checklist taxa data template csv" tabindex="0"><span class="text-bold">using the batch checklist taxa data template. </span></a>
                Upload the completed template in the box below and then click the Upload Taxa button to process the data.
            </div>
            <div class="row">
                <div class="col-grow">
                    <file-picker-input-element :accepted-types="['csv']" :value="uploadedFile" :validate-file-size="false" @update:file="(value) => processFileSelection(value)"></file-picker-input-element>
                </div>
            </div>
            <div class="row justify-end">
                <q-btn color="secondary" @click="processTaxaUpload();" label="Upload Taxa" :disabled="!uploadedFile" tabindex="0" />
            </div>
            <template v-if="errorArr.length > 0">
                <div class="text-subtitle1 text-bold text-red">
                    The following taxa could not be loaded because they were not found in the Taxonomic Thesaurus
                </div>
                <div class="q-pl-sm column">
                    <template v-for="taxon in errorArr">
                        <div class="text-subtitle1 text-red">
                            {{ taxon['scientificname'] }}
                        </div>
                    </template>
                </div>
            </template>
        </div>
    `,
    components: {
        'file-picker-input-element': filePickerInputElement
    },
    setup(_, context) {
        const { csvToArray, hideWorking, parseFile, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const checklistStore = useChecklistStore();

        const checklistId = Vue.computed(() => checklistStore.getChecklistID);
        const clientRoot = baseStore.getClientRoot;
        const errorArr = Vue.ref([]);
        const scinameArr = Vue.computed(() => {
            const returnArr = [];
            taxaDataArr.value.forEach((taxon) => {
                returnArr.push(taxon['scientificname']);
            });
            return returnArr;
        });
        const taxaDataArr = Vue.ref([]);
        const uploadArr = Vue.ref([]);
        const uploadCount = Vue.ref(0);
        const uploadedFile = Vue.ref(null);

        function batchUploadChecklistTaxa() {
            if(uploadArr.value.length > 0){
                const data = uploadArr.value.length > 500 ? uploadArr.value.slice(0, 500) : uploadArr.value.slice();
                if(uploadArr.value.length > 500){
                    uploadArr.value.splice(0, 500);
                }
                else{
                    uploadArr.value.length = 0;
                }
                const formData = new FormData();
                formData.append('clid', checklistId.value.toString());
                formData.append('data', JSON.stringify(data));
                formData.append('action', 'batchCreateRecords');
                fetch(checklistTaxaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    const resValue = isNaN(Number(res)) ? 0 : Number(res);
                    uploadCount.value += resValue;
                    batchUploadChecklistTaxa();
                });
            }
            else{
                hideWorking();
                context.emit('load:checklist-taxa');
                showNotification('positive',(uploadCount.value + ' taxa were added successfully.'));
                if(errorArr.value.length === 0){
                    context.emit('close:popup');
                }
            }
        }

        function processFileCsvData(csvData) {
            if(csvData.length > 0){
                csvData.forEach((dataObj) => {
                    if(dataObj){
                        if(dataObj.hasOwnProperty('scientificname') && dataObj['scientificname'] && dataObj['scientificname'].length > 0 && !scinameArr.value.includes(dataObj['scientificname'])){
                            taxaDataArr.value.push({
                                scientificname: dataObj['scientificname'],
                                tid: null,
                                habitat: ((dataObj.hasOwnProperty('habitat') && dataObj['habitat'] && dataObj['habitat'].length > 0) ? dataObj['habitat'] : null),
                                abundance: ((dataObj.hasOwnProperty('abundance') && dataObj['abundance'] && dataObj['abundance'].length > 0) ? dataObj['abundance'] : null),
                                notes: ((dataObj.hasOwnProperty('notes') && dataObj['notes'] && dataObj['notes'].length > 0) ? dataObj['notes'] : null),
                                source: ((dataObj.hasOwnProperty('source') && dataObj['source'] && dataObj['source'].length > 0) ? dataObj['source'] : null)
                            });
                        }
                    }
                });
                if(scinameArr.value.length > 0){
                    setTaxaIdData();
                }
                else{
                    hideWorking();
                    showNotification('negative','No scientificname values were found in the csv.');
                }
            }
        }

        function processFileSelection(file) {
            if(file){
                uploadedFile.value = file[0];
            }
            else{
                uploadedFile.value = null;
            }
        }

        function processTaxaUpload() {
            showWorking();
            errorArr.value.length = 0;
            taxaDataArr.value.length = 0;
            uploadArr.value.length = 0;
            uploadCount.value = 0;
            parseFile(uploadedFile.value, (fileContents) => {
                csvToArray(fileContents).then((csvData) => {
                    processFileCsvData(csvData);
                });
            });
        }

        function setTaxaIdData() {
            const formData = new FormData();
            formData.append('taxa', JSON.stringify(scinameArr.value));
            formData.append('action', 'getTaxaIdDataFromNameArr');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                Object.keys(resObj).forEach((key) => {
                    if(Number(resObj[key]['tid']) > 0){
                        const taxonData = taxaDataArr.value.find((taxObj) => taxObj['scientificname'].toLowerCase() === key.toLowerCase());
                        taxonData['tid'] = resObj[key]['tid'];
                    }
                });
                setUploadArr();
            });
        }

        function setUploadArr() {
            taxaDataArr.value.forEach((taxon) => {
                if(Number(taxon['tid']) > 0){
                    uploadArr.value.push({
                        clid: checklistId.value,
                        tid: taxon['tid'],
                        habitat: taxon['habitat'],
                        abundance: taxon['abundance'],
                        notes: taxon['notes'],
                        source: taxon['source']
                    });
                }
                else{
                    errorArr.value.push(taxon);
                }
            });
            if(uploadArr.value.length > 0){
                batchUploadChecklistTaxa();
            }
            else{
                hideWorking();
                showNotification('negative','None of the taxa uploaded are in the taxonomic thesaurus.');
            }
        }

        return {
            clientRoot,
            errorArr,
            uploadedFile,
            processFileSelection,
            processTaxaUpload
        }
    }
};
