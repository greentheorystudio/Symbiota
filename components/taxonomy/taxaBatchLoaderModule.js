const taxaBatchLoaderModule = {
    props: {
        kingdomId: {
            type: Number,
            default: null
        },
        loading: {
            type: Boolean,
            default: false
        },
        taxonomicGroupTid: {
            type: Number,
            default: null
        }
    },
    template: `
        <div class="processor-container">
            <div class="processor-control-container">
                <q-card class="processor-control-card">
                    <q-card-section>
                        <q-card class="q-my-sm" flat bordered>
                            <q-card-section>
                                <div class="q-pa-md column q-col-gutter-sm">
                                    <div class="q-mb-md column q-gutter-sm">
                                        <div>
                                            Taxa can be batch uploaded 
                                            <a :href="(clientRoot + '/templates/batchTaxaData.csv')" aria-label="Download batch taxa data template csv" tabindex="0"><span class="text-bold">using the batch taxa data template. </span></a>
                                            In the template, Scientific name  and Rank name values are required for all taxa, and Parent scientific name 
                                            values are required for all taxa above genus rank. Please use standard taxonomic rank names for the Rank name 
                                            values (e.g., Kingdom, Phylum, Class, Order, etc.) Upload the completed template 
                                            in the box below and then click the Upload Taxa button to process the data.
                                        </div>
                                        <div v-if="!uploadedFile || Number(taxonomicGroupTid) === 0" class="text-bold text-red">
                                            Please enter a Taxonomic Group in the box above, and choose the CSV data file in the box below to continue.
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <file-picker-input-element :accepted-types="['csv','txt']" :value="uploadedFile" :validate-file-size="false" @update:file="(value) => processFileSelection(value)"></file-picker-input-element>
                                        </div>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn :loading="loading" color="secondary" @click="initializeUpload();" label="Start Upload" dense aria-label="Start Upload" :disabled="!uploadedFile || Number(taxonomicGroupTid) === 0" tabindex="0" />
                                </div>
                                <div>
                                    <q-btn v-if="loading" :disabled="processCancelling" color="red" @click="cancelProcess();" label="Cancel" dense aria-label="Cancel Import" tabindex="0" />
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>

            <div class="processor-display-container">
                <q-card class="bg-grey-3 q-pa-sm">
                    <q-scroll-area ref="procDisplayScrollAreaRef" class="bg-grey-1 processor-display" @scroll="setScroller">
                        <q-list dense>
                            <template v-if="!currentProcess && processorDisplayCurrentIndex > 0">
                                <q-item>
                                    <q-item-section>
                                        <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollUp();" @keyup.enter="processorDisplayScrollUp();" aria-label="Show previous 100 entries" tabindex="0">Show previous 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                            <q-item v-for="proc in processorDisplayArr">
                                <q-item-section>
                                    <div>{{ proc.procText }} <q-spinner v-if="proc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                    <template v-if="!proc.loading && proc.resultText">
                                        <div v-if="proc.result === 'success'" class="q-ml-sm text-weight-bold text-green-9">
                                            {{proc.resultText}}
                                        </div>
                                        <div v-if="proc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                            {{proc.resultText}}
                                        </div>
                                    </template>
                                    <template v-if="proc.type === 'multi' && proc.subs.length">
                                        <div class="q-ml-sm">
                                            <div v-for="subproc in proc.subs">
                                                <template v-if="subproc.type === 'text' || subproc.type === 'undo'">
                                                    <div>{{ subproc.procText }} <q-spinner v-if="subproc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                                    <template v-if="!subproc.loading && subproc.resultText">
                                                        <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="text-weight-bold text-green-9">
                                                            <span class="text-weight-bold text-green-9">{{subproc.resultText}}</span>
                                                        </div>
                                                        <div v-if="subproc.result === 'error'" class="text-weight-bold text-negative">
                                                            {{subproc.resultText}}
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </q-item-section>
                            </q-item>
                            <template v-if="!currentProcess && processorDisplayCurrentIndex < processorDisplayIndex">
                                <q-item>
                                    <q-item-section>
                                        <div><a role="button" class="text-bold cursor-pointer" @click="processorDisplayScrollDown();" @keyup.enter="processorDisplayScrollDown();" aria-label="Show next 100 entries" tabindex="0">Show next 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-list>
                    </q-scroll-area>
                </q-card>
            </div>
        </div>
    `,
    components: {
        'file-picker-input-element': filePickerInputElement
    },
    setup(props, context) {
        const { csvToArray, parseFile } = useCore();

        const csvDataArr = Vue.ref([]);
        const currentProcess = Vue.ref(null);
        const newTidArr = Vue.ref([]);
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processCancelling = Vue.ref(false);
        const processingArr = Vue.ref([]);
        const processorDisplayArr = Vue.reactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const rankData = Vue.ref({});
        const rebuildHierarchyLoop = Vue.ref(0);
        const scinameTidData = Vue.ref({});
        const scrollProcess = Vue.ref(null);
        const uploadedFile = Vue.ref(null);

        function addProcessToProcessorDisplay(processObj) {
            processorDisplayArr.push(processObj);
            if(processorDisplayArr.length > 100){
                const precessorArrSegment = processorDisplayArr.slice(0, 100);
                processorDisplayDataArr = processorDisplayDataArr.concat(precessorArrSegment);
                processorDisplayArr.splice(0, 100);
                processorDisplayIndex.value++;
                processorDisplayCurrentIndex.value = processorDisplayIndex.value;
            }
        }

        function adjustUIEnd() {
            processCancelling.value = false;
            context.emit('update:loading', false);
            processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
        }

        function adjustUIStart() {
            currentProcess.value = null;
            csvDataArr.value.length = 0;
            processingArr.value = [];
            processorDisplayArr.length = 0;
            processorDisplayDataArr = [];
            processorDisplayCurrentIndex.value = 0;
            processorDisplayIndex.value = 0;
            newTidArr.value.length = 0;
            rebuildHierarchyLoop.value = 0;
            rankData.value = Object.assign({}, {});
            scinameTidData.value = Object.assign({}, {});
            context.emit('update:loading', true);
        }

        function cancelProcess() {
            processCancelling.value = true;
        }

        function getNewProcessObject(type, text) {
            if(processorDisplayArr.length > 0){
                const pastProcObj = processorDisplayArr[(processorDisplayArr.length - 1)];
                if(pastProcObj){
                    pastProcObj['current'] = false;
                    if(pastProcObj.hasOwnProperty('subs') && pastProcObj['subs'].length > 0){
                        const subProcObj = pastProcObj['subs'][(pastProcObj['subs'].length - 1)];
                        if(subProcObj){
                            subProcObj['loading'] = false;
                            if(!subProcObj['result'] || subProcObj['result'] === ''){
                                subProcObj['result'] = 'success';
                            }
                            if(!subProcObj['resultText'] || subProcObj['resultText'] === ''){
                                subProcObj['resultText'] = 'Complete';
                            }
                        }
                    }
                    else{
                        if(!pastProcObj['result'] || pastProcObj['result'] === ''){
                            pastProcObj['result'] = 'success';
                        }
                        if(!pastProcObj['resultText'] || pastProcObj['resultText'] === ''){
                            pastProcObj['resultText'] = 'Complete';
                        }
                    }
                }
            }
            const procObj = {
                id: currentProcess.value,
                procText: text,
                type: type,
                loading: true,
                current: true,
                result: '',
                resultText: ''
            };
            if(type === 'multi'){
                procObj['subs'] = [];
            }
            return procObj;
        }

        function getNextTaxonFromCsvDataArr() {
            let returnData;
            const nextTaxon = csvDataArr.value.find(taxon => (Number(scinameTidData.value[taxon.parentsciname]) > 0 && (!taxon.acceptedsciname || Number(scinameTidData.value[taxon.acceptedsciname]) > 0)));
            if(nextTaxon){
                returnData = Object.assign({}, nextTaxon);
                const index = csvDataArr.value.indexOf(nextTaxon);
                csvDataArr.value.splice(index, 1);
            }
            return returnData;
        }

        function initializeUpload() {
            adjustUIStart();
            const text = 'Setting rank data';
            currentProcess.value = 'setRankArr';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const url = taxonRankApiUrl + '?action=getRankNameArr&kingdomid=' + props.kingdomId;
            fetch(url)
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(Object.keys(data).length > 0){
                    rankData.value = Object.assign({}, data);
                    processSuccessResponse('Complete');
                    processCsvData();
                }
                else{
                    processErrorResponse('Taxonomic rank data could not be found.');
                    adjustUIEnd();
                }
            });
        }

        function populateTaxonomicHierarchy(callback) {
            if(rebuildHierarchyLoop.value < 40){
                const formData = new FormData();
                formData.append('action', 'populateHierarchyTable');
                fetch(taxonHierarchyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.text().then((res) => {
                            if(Number(res) > 0){
                                rebuildHierarchyLoop.value++;
                                populateTaxonomicHierarchy(callback);
                            }
                            else{
                                processSuccessResponse('Import complete!');
                                callback();
                            }
                        });
                    }
                    else{
                        processErrorResponse('Error updating the taxonomic hierarchy');
                        callback('Error updating the taxonomic hierarchy');
                    }
                });
            }
            else{
                processErrorResponse('Error updating the taxonomic hierarchy');
                callback('Error updating the taxonomic hierarchy');
            }
        }

        function primeTaxonomicHierarchy(callback) {
            rebuildHierarchyLoop.value = 0;
            const formData = new FormData();
            formData.append('tidarr', JSON.stringify(newTidArr.value));
            formData.append('action', 'primeHierarchyTable');
            fetch(taxonHierarchyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        if(Number(res) > 0){
                            rebuildHierarchyLoop.value++;
                            populateTaxonomicHierarchy(callback);
                        }
                        else{
                            processSuccessResponse('Complete');
                            callback();
                        }
                    });
                }
                else{
                    processErrorResponse('Error updating the taxonomic hierarchy');
                    callback('Error updating the taxonomic hierarchy');
                }
            });
        }

        function processCsvData() {
            const text = 'Processing CSV data';
            currentProcess.value = 'processingCsvData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            parseFile(uploadedFile.value, (fileContents) => {
                csvToArray(fileContents).then((csvData) => {
                    processFileCsvData(csvData);
                });
            });
        }

        function processCsvDataArr() {
            const currentTaxonData = getNextTaxonFromCsvDataArr();
            if(currentTaxonData){
                const text = 'Adding ' + currentTaxonData.sciname + ' to Taxonomic Thesaurus';
                currentProcess.value = currentTaxonData.sciname;
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                currentTaxonData['parenttid'] = Number(scinameTidData.value[currentTaxonData.parentsciname]);
                if(currentTaxonData.acceptedsciname){
                    currentTaxonData['tidaccepted'] = Number(scinameTidData.value[currentTaxonData.acceptedsciname]);
                }
                const formData = new FormData();
                formData.append('taxon', JSON.stringify(currentTaxonData));
                formData.append('action', 'addTaxon');
                fetch(taxaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(res && Number(res) > 0){
                        scinameTidData.value[currentTaxonData.sciname] = Number(res);
                        newTidArr.value.push(Number(res));
                        processSuccessResponse('Complete');
                    }
                    else{
                        processErrorResponse('An error occurred while adding taxon');
                    }
                    processCsvDataArr();
                });
            }
            else if(csvDataArr.value.length > 0){
                processRemainingTaxa();
            }
            else{
                updateTaxonomicHierarchy(() => {
                    adjustUIEnd();
                });
            }
        }

        function processErrorResponse(text) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = false;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'error';
                    procObj['resultText'] = text;
                }
            }
        }

        function processFileCsvData(csvData) {
            if(csvData.length > 0){
                csvData.forEach((dataObj) => {
                    if(
                        dataObj.hasOwnProperty('scientific_name') &&
                        dataObj['scientific_name'] &&
                        dataObj.hasOwnProperty('parent_scientific_name') &&
                        dataObj['parent_scientific_name'] &&
                        dataObj.hasOwnProperty('rank_name') &&
                        dataObj['rank_name'] &&
                        rankData.value.hasOwnProperty(dataObj['rank_name'].toLowerCase()) &&
                        dataObj['rank_name'].toLowerCase() !== 'kingdom'
                    ) {
                        const existingTaxon = csvDataArr.value.find(taxon => taxon['sciname'] === dataObj['scientific_name']);
                        if(!existingTaxon){
                            const taxonObj = {
                                tid: null,
                                kingdomid: props.kingdomId,
                                rankid: rankData.value[dataObj['rank_name'].toLowerCase()],
                                sciname: dataObj['scientific_name'],
                                author: (dataObj.hasOwnProperty('author') ? dataObj['author'] : null),
                                family: (dataObj.hasOwnProperty('family') ? dataObj['family'] : null),
                                parentsciname: dataObj['parent_scientific_name'],
                                parenttid: null,
                                acceptedsciname: ((dataObj.hasOwnProperty('accepted_scientific_name') && dataObj['accepted_scientific_name'] && dataObj['accepted_scientific_name'] !== dataObj['scientific_name']) ? dataObj['accepted_scientific_name'] : null),
                                tidaccepted: null
                            };
                            csvDataArr.value.push(taxonObj);
                            if(!scinameTidData.value.hasOwnProperty(dataObj['scientific_name'])) {
                                scinameTidData.value[dataObj['scientific_name']] = null;
                            }
                            if(!scinameTidData.value.hasOwnProperty(dataObj['parent_scientific_name'])) {
                                scinameTidData.value[dataObj['parent_scientific_name']] = null;
                            }
                            if(dataObj.hasOwnProperty('accepted_scientific_name') && dataObj['accepted_scientific_name'] && !scinameTidData.value.hasOwnProperty(dataObj['accepted_scientific_name'])) {
                                scinameTidData.value[dataObj['accepted_scientific_name']] = null;
                            }
                        }
                    }
                });
                if(csvDataArr.value.length > 0){
                    processSuccessResponse('Complete');
                    setTidData();
                }
                else{
                    processErrorResponse('No taxa data was found in the csv.');
                    adjustUIEnd();
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

        function processorDisplayScrollDown() {
            scrollProcess.value = 'scrollDown';
            processorDisplayArr.length = 0;
            processorDisplayCurrentIndex.value++;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processorDisplayScrollUp() {
            scrollProcess.value = 'scrollUp';
            processorDisplayArr.length = 0;
            processorDisplayCurrentIndex.value--;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processRemainingTaxa() {
            csvDataArr.value.forEach((taxon) => {
                const text = 'Processing ' + taxon.sciname;
                currentProcess.value = taxon.sciname + '-remaining';
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                let errorText = 'Could not be upload because ';
                if(Number(scinameTidData.value[taxon.parentsciname]) === 0){
                    errorText += 'the parent taxon '
                }
                if(taxon.acceptedsciname && Number(scinameTidData.value[taxon.acceptedsciname]) === 0){
                    errorText += (Number(scinameTidData.value[taxon.parentsciname]) === 0 ? 'and ' : '') + 'the accepted taxon '
                }
                errorText += 'could not be found in either the Taxonomic Thesaurus or in the data uploaded ';
                processErrorResponse(errorText);
            });
            updateTaxonomicHierarchy(() => {
                adjustUIEnd();
            });
        }

        function processSuccessResponse(text = null) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = false;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'success';
                    procObj['resultText'] = text;
                }
            }
        }

        function resetScrollProcess() {
            setTimeout(() => {
                scrollProcess.value = null;
            }, 200);
        }

        function setScroller(info) {
            if((currentProcess.value || scrollProcess.value) && info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                procDisplayScrollHeight.value = info.verticalSize;
                if(scrollProcess.value === 'scrollDown'){
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', 0);
                }
                else{
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                }
            }
        }

        function setTidData() {
            const text = 'Setting IDs for existing taxa';
            currentProcess.value = 'setTidArr';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('taxa', JSON.stringify(Object.keys(scinameTidData.value)));
            formData.append('kingdomid', props.kingdomId.toString());
            formData.append('action', 'getTaxaIdDataFromNameArr');
            fetch(taxaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                Object.keys(scinameTidData.value).forEach((taxon) => {
                    if(resObj.hasOwnProperty(taxon.toLowerCase())){
                        scinameTidData.value[taxon] = resObj[taxon.toLowerCase()]['tid'];
                    }
                });
                processSuccessResponse('Complete');
                processCsvDataArr();
            });
        }

        function updateTaxonomicHierarchy(callback) {
            if(newTidArr.value.length > 0){
                const text = 'Updating taxonomic hierarchy table with new taxa';
                currentProcess.value = 'updateTaxonomicHierarchy';
                addProcessToProcessorDisplay(getNewProcessObject('single',text));
                rebuildHierarchyLoop.value = 0;
                const formData = new FormData();
                formData.append('tidarr', JSON.stringify(newTidArr.value));
                formData.append('action', 'clearHierarchyTable');
                fetch(taxonHierarchyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        primeTaxonomicHierarchy(callback);
                    }
                    else{
                        processErrorResponse('Error updating the taxonomic hierarchy');
                        callback('Error updating the taxonomic hierarchy');
                    }
                });
            }
            else{
                callback();
            }
        }

        return {
            currentProcess,
            procDisplayScrollAreaRef,
            processCancelling,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            uploadedFile,
            cancelProcess,
            initializeUpload,
            processFileSelection,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            setScroller
        }
    }
};
