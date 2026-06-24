const glossaryBatchLoaderPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="processor-container">
                            <div class="processor-control-container">
                                <q-card class="processor-control-card">
                                    <q-card-section>
                                        <q-card class="q-my-sm" flat bordered>
                                            <q-card-section>
                                                <div class="q-pa-md column q-col-gutter-sm">
                                                    <div class="q-mb-md column q-gutter-sm">
                                                        <div>
                                                            Glossary terms can be uploaded using CSV (comma delimited) text files here. For each language 
                                                            in the CSV file, name the column with the terms as the language the terms are in, and then 
                                                            name all columns related to that term as the language underscore and then the column name (ex. 
                                                            English, English_definition, Spanish, Spanish_definition, etc.) Columns can be added for the 
                                                            definition, author, translator, source, notes, and resourceurl. Synonyms can be 
                                                            added by naming the column the language underscore synonym (ex. English_synonym). Please specify 
                                                            at least one taxonomic group for which the terms are related in the box below. Please do not use spaces 
                                                            in the column names.
                                                        </div>
                                                        <div v-if="!uploadedFile || taxonomicGroupVal.length === 0" class="text-bold text-red">
                                                            Please enter at least one Taxonomic Group and choose the CSV data file in the boxes below to continue.
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-grow">
                                                            <multiple-scientific-common-name-auto-complete label="Enter Taxonomic Groups" :sciname="taxonomicGroupVal" :limit-to-options="true" :accepted-taxa-only="true" :name-string-mode="false" @update:sciname="processScientificNameChange"></multiple-scientific-common-name-auto-complete>
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
                                                    <q-btn :loading="loading" color="secondary" @click="initializeUpload();" label="Start Upload" dense aria-label="Start Upload" :disabled="!uploadedFile || taxonomicGroupVal.length === 0" tabindex="0" />
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
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'file-picker-input-element': filePickerInputElement,
        'multiple-scientific-common-name-auto-complete': multipleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { csvToArray, parseFile } = useCore();
        const glossaryStore = useGlossaryStore();

        const csvDataArr = Vue.ref([]);
        const currentProcess = Vue.ref(null);
        const dataLanguageArr = Vue.ref([]);
        const existingTranslationGlossidArr = Vue.ref([]);
        const existingTranslationGroupData = Vue.ref({});
        const glossaryArr = Vue.computed(() => glossaryStore.getGlossaryArr);
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processCancelling = Vue.ref(false);
        const processingArr = Vue.ref([]);
        const processorDisplayArr = Vue.reactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const scrollProcess = Vue.ref(null);
        const taxonomicGroupTidArr = Vue.computed(() => {
            const returnArr = [];
            taxonomicGroupVal.value.forEach(taxon => {
                returnArr.push(Number(taxon.tid));
            });
            return returnArr;
        });
        const taxonomicGroupVal = Vue.ref([]);
        const tidGlossidData = Vue.ref({});
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
            dataLanguageArr.value.length = 0;
            existingTranslationGlossidArr.value = 0;
            tidGlossidData.value = Object.assign({}, {});
            existingTranslationGroupData.value = Object.assign({}, {});
            context.emit('update:loading', true);
        }

        function cancelProcess() {
            processCancelling.value = true;
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function getExistingTranslationGroupData() {
            const text = 'Validating existing translation group data';
            currentProcess.value = 'getExistingTranslationGroupData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            const formData = new FormData();
            formData.append('glossIdArr', JSON.stringify(existingTranslationGlossidArr.value));
            formData.append('relationtype', 'translation');
            formData.append('action', 'getGlossaryRelatedTermsDataFromGlossidArr');
            fetch(glossaryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                existingTranslationGroupData.value = Object.assign({}, resData);
                validateExistingTranslationGroups();
            });
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

        function getNextSynonymDataFromCsvDataArr() {
            for (const row of csvDataArr.value) {
                if(row['synonymGlossidArr'].length > 0){
                    const newSynonymData = {
                        glossidArr: row['synonymGlossidArr'].slice(),
                        groupid: row['synonymGroupId']
                    };
                    row['synonymGlossidArr'].length = 0;
                    return newSynonymData;
                }
            }
            return null;
        }

        function getNextTaxaDataFromCsvDataArr() {
            for (const taxon of taxonomicGroupVal.value) {
                if(tidGlossidData.value[taxon.tid].length > 0){
                    const newTaxaData = {
                        glossidArr: tidGlossidData.value[taxon.tid].slice(),
                        tid: taxon.tid
                    };
                    tidGlossidData.value[taxon.tid].length = 0;
                    return newTaxaData;
                }
            }
            return null;
        }

        function getNextTermFromCsvDataArr() {
            for (const row of csvDataArr.value) {
                if(row['termObjects'].length > 0){
                    const newTerm = Object.assign({}, row['termObjects'][0]);
                    row['termObjects'].splice(0, 1);
                    return {
                        term: newTerm,
                        row: row
                    }
                }
            }
            return null;
        }

        function getNextTranslationDataFromCsvDataArr() {
            for (const row of csvDataArr.value) {
                if(row['translationGlossidArr'].length > 0){
                    const newTranslationData = {
                        glossidArr: row['translationGlossidArr'].slice(),
                        groupid: row['translationGroupId']
                    };
                    row['translationGlossidArr'].length = 0;
                    return newTranslationData;
                }
            }
            return null;
        }

        function initializeUpload() {
            adjustUIStart();
            setTidGlossidDataKeys();
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
            const currentTermData = getNextTermFromCsvDataArr();
            if(currentTermData){
                const text = 'Adding ' + currentTermData.term.term + ' (' + currentTermData.term.language + ') to glossary';
                currentProcess.value = (currentTermData.term.term + '-' + currentTermData.term.language);
                addProcessToProcessorDisplay(getNewProcessObject('single', text));
                const formData = new FormData();
                formData.append('glossary', JSON.stringify(currentTermData.term));
                formData.append('action', 'createGlossaryRecord');
                fetch(glossaryApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(res && Number(res) > 0){
                        const translationObj = currentTermData.row['translationTermArr'].find(term => (term['term'] === currentTermData.term.term && term['language'] === currentTermData.term.language));
                        if(translationObj){
                            currentTermData.row['translationGlossidArr'].push(Number(res));
                        }
                        const synonymObj = currentTermData.row['synonymTermArr'].find(term => (term['term'] === currentTermData.term.term && term['language'] === currentTermData.term.language));
                        if(synonymObj){
                            currentTermData.row['synonymGlossidArr'].push(Number(res));
                        }
                        taxonomicGroupTidArr.value.forEach(tid => {
                            tidGlossidData.value[tid.toString()].push(Number(res));
                        });
                        processSuccessResponse('Complete');
                    }
                    else{
                        processErrorResponse('An error occurred while adding the term');
                    }
                    processCsvDataArr();
                });
            }
            else{
                processCsvDataTranslationArr();
            }
        }

        function processCsvDataSynonymArr() {
            const currentSynonymData = getNextSynonymDataFromCsvDataArr();
            if(currentSynonymData){
                if(currentProcess.value !== 'processingSynonymRelationships'){
                    const text = 'Processing synonym relationship data';
                    currentProcess.value = 'processingSynonymRelationships';
                    addProcessToProcessorDisplay(getNewProcessObject('single', text));
                }
                const groupIdVal = Number(currentSynonymData['groupid']) > 0 ? currentSynonymData['groupid'] : glossaryStore.getNextGlossGroupIdValue();
                const formData = new FormData();
                formData.append('glossIdArr', JSON.stringify(currentSynonymData['glossidArr']));
                formData.append('groupId', groupIdVal.toString());
                formData.append('relationType', 'synonym');
                formData.append('action', 'addGlossaryTermRelationships');
                fetch(glossaryApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then(() => {
                    processCsvDataSynonymArr();
                });
            }
            else{
                if(currentProcess.value === 'processingSynonymRelationships'){
                    processSuccessResponse('Complete');
                }
                processCsvDataTaxaArr();
            }
        }

        function processCsvDataTaxaArr() {
            const currentTaxaData = getNextTaxaDataFromCsvDataArr();
            if(currentTaxaData){
                if(currentProcess.value !== 'processingTaxaRelationships'){
                    const text = 'Processing taxa relationship data';
                    currentProcess.value = 'processingTaxaRelationships';
                    addProcessToProcessorDisplay(getNewProcessObject('single', text));
                }
                const formData = new FormData();
                formData.append('glossIdArr', JSON.stringify(currentTaxaData['glossidArr']));
                formData.append('tid', currentTaxaData.tid.toString());
                formData.append('action', 'addGlossaryTaxaRelationships');
                fetch(glossaryApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then(() => {
                    processCsvDataTaxaArr();
                });
            }
            else{
                if(currentProcess.value === 'processingTaxaRelationships'){
                    processSuccessResponse('Import complete!');
                }
                glossaryStore.setGlossaryData(() => {
                    adjustUIEnd();
                });
            }
        }

        function processCsvDataTranslationArr() {
            const currentTranslationData = getNextTranslationDataFromCsvDataArr();
            if(currentTranslationData){
                if(currentProcess.value !== 'processingTranslationRelationships'){
                    const text = 'Processing translation relationship data';
                    currentProcess.value = 'processingTranslationRelationships';
                    addProcessToProcessorDisplay(getNewProcessObject('single', text));
                }
                const groupIdVal = Number(currentTranslationData['groupid']) > 0 ? currentTranslationData['groupid'] : glossaryStore.getNextGlossGroupIdValue();
                const formData = new FormData();
                formData.append('glossIdArr', JSON.stringify(currentTranslationData['glossidArr']));
                formData.append('groupId', groupIdVal.toString());
                formData.append('relationType', 'translation');
                formData.append('action', 'addGlossaryTermRelationships');
                fetch(glossaryApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then(() => {
                    processCsvDataTranslationArr();
                });
            }
            else{
                if(currentProcess.value === 'processingTranslationRelationships'){
                    processSuccessResponse('Complete');
                }
                processCsvDataSynonymArr();
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
                Object.keys(csvData[0]).forEach(field => {
                    if(!field.includes('_')){
                        dataLanguageArr.value.push(field);
                    }
                });
                csvData.forEach((dataObj) => {
                    const csvDataObj = {
                        termObjects: [],
                        synonymGlossidArr: [],
                        synonymGroupId: null,
                        synonymTermArr: [],
                        translationGlossidArr: [],
                        translationGroupId: null,
                        translationTermArr: []
                    };
                    dataLanguageArr.value.forEach((language) => {
                        if(dataObj[language]){
                            const existingTerm = glossaryArr.value.find(term => (term['term'] === dataObj[language] && term['language'] === (language.charAt(0).toUpperCase() + language.slice(1))));
                            if(existingTerm){
                                const existingTranslationGroup = existingTerm['groupIdArr'].find(group => group['relationshiptype'] === 'translation');
                                if(!csvDataObj['translationGroupId'] && existingTranslationGroup){
                                    existingTranslationGlossidArr.value.push(existingTerm['glossid']);
                                    csvDataObj['translationGroupId'] = existingTranslationGroup['glossgrpid'];
                                }
                                else if(dataLanguageArr.value.length > 1){
                                    csvDataObj['translationGlossidArr'].push(existingTerm['glossid']);
                                }
                                taxonomicGroupTidArr.value.forEach(tid => {
                                    if(!existingTerm['tidArr'].includes(tid)){
                                        tidGlossidData.value[tid.toString()].push(existingTerm['glossid']);
                                    }
                                });
                            }
                            else{
                                const termObj = {
                                    glossid: null,
                                    term: dataObj[language],
                                    relationship: 'translation',
                                    definition: (dataObj.hasOwnProperty(language + '_definition') ? dataObj[(language + '_definition')] : null),
                                    language: (language.charAt(0).toUpperCase() + language.slice(1)),
                                    source: (dataObj.hasOwnProperty(language + '_source') ? dataObj[(language + '_source')] : null),
                                    translator: (dataObj.hasOwnProperty(language + '_translator') ? dataObj[(language + '_translator')] : null),
                                    author: (dataObj.hasOwnProperty(language + '_author') ? dataObj[(language + '_author')] : null),
                                    notes: (dataObj.hasOwnProperty(language + '_notes') ? dataObj[(language + '_notes')] : null),
                                    resourceurl: (dataObj.hasOwnProperty(language + '_resourceurl') ? dataObj[(language + '_resourceurl')] : null)
                                };
                                csvDataObj['termObjects'].push(termObj);
                                if(dataLanguageArr.value.length > 1){
                                    csvDataObj['translationTermArr'].push({
                                        term: dataObj[language],
                                        language: (language.charAt(0).toUpperCase() + language.slice(1))
                                    });
                                }
                            }
                            if(dataObj.hasOwnProperty(language + '_synonym') && dataObj[language + '_synonym']){
                                if(existingTerm){
                                    const existingTermSynonymGroup = existingTerm['groupIdArr'].find(group => group['relationshiptype'] === 'synonym');
                                    if(!csvDataObj['synonymGroupId'] && existingTermSynonymGroup){
                                        csvDataObj['synonymGroupId'] = existingTermSynonymGroup['glossgrpid'];
                                    }
                                    else{
                                        csvDataObj['synonymGlossidArr'].push(existingTerm['glossid']);
                                    }
                                }
                                else{
                                    csvDataObj['synonymTermArr'].push({
                                        term: dataObj[language],
                                        language: (language.charAt(0).toUpperCase() + language.slice(1))
                                    });
                                }
                                const existingSynonym = glossaryArr.value.find(term => (term['term'] === dataObj[language + '_synonym'] && term['language'] === (language.charAt(0).toUpperCase() + language.slice(1))));
                                if(existingSynonym){
                                    const existingSynonymSynonymGroup = existingSynonym['groupIdArr'].find(group => group['relationshiptype'] === 'synonym');
                                    if(!csvDataObj['synonymGroupId'] && existingSynonymSynonymGroup){
                                        csvDataObj['synonymGroupId'] = existingSynonymSynonymGroup['glossgrpid'];
                                    }
                                    else{
                                        csvDataObj['synonymGlossidArr'].push(existingSynonym['glossid']);
                                    }
                                    const existingSynonymTranslationGroup = existingSynonym['groupIdArr'].find(group => group['relationshiptype'] === 'translation');
                                    if(!csvDataObj['translationGroupId'] && existingSynonymTranslationGroup){
                                        existingTranslationGlossidArr.value.push(existingSynonym['glossid']);
                                        csvDataObj['translationGroupId'] = existingSynonymTranslationGroup['glossgrpid'];
                                    }
                                    else if(dataLanguageArr.value.length > 1){
                                        csvDataObj['translationGlossidArr'].push(existingSynonym['glossid']);
                                    }
                                    taxonomicGroupTidArr.value.forEach(tid => {
                                        if(!existingSynonym['tidArr'].includes(tid)){
                                            tidGlossidData.value[tid.toString()].push(existingSynonym['glossid']);
                                        }
                                    });
                                }
                                else{
                                    const synObj = {
                                        glossid: null,
                                        term: dataObj[language + '_synonym'],
                                        relationship: 'synonym',
                                        definition: (dataObj.hasOwnProperty(language + '_definition') ? dataObj[(language + '_definition')] : null),
                                        language: (language.charAt(0).toUpperCase() + language.slice(1)),
                                        source: (dataObj.hasOwnProperty(language + '_source') ? dataObj[(language + '_source')] : null),
                                        translator: (dataObj.hasOwnProperty(language + '_translator') ? dataObj[(language + '_translator')] : null),
                                        author: (dataObj.hasOwnProperty(language + '_author') ? dataObj[(language + '_author')] : null),
                                        notes: (dataObj.hasOwnProperty(language + '_notes') ? dataObj[(language + '_notes')] : null),
                                        resourceurl: (dataObj.hasOwnProperty(language + '_resourceurl') ? dataObj[(language + '_resourceurl')] : null)
                                    };
                                    csvDataObj['termObjects'].push(synObj);
                                    if(dataLanguageArr.value.length > 1){
                                        csvDataObj['synonymTermArr'].push({
                                            term: dataObj[language + '_synonym'],
                                            language: (language.charAt(0).toUpperCase() + language.slice(1))
                                        });
                                    }
                                }
                            }
                        }
                    });
                    if(csvDataObj['termObjects'].length > 0){
                        csvDataArr.value.push(csvDataObj);
                    }
                });
                if(csvDataArr.value.length > 0){
                    processSuccessResponse('Complete');
                    if(existingTranslationGlossidArr.value.length > 0){
                        getExistingTranslationGroupData();
                    }
                    else{
                        processCsvDataArr();
                    }
                }
                else{
                    processErrorResponse('No glossary data was found in the csv.');
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

        function processScientificNameChange(taxonVal) {
            taxonomicGroupVal.value = taxonVal;
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

        function setTidGlossidDataKeys() {
            taxonomicGroupVal.value.forEach(taxon => {
                tidGlossidData.value[taxon.tid] = [];
            });
        }

        function validateExistingTranslationGroups() {
            for(const row of csvDataArr.value) {
                if(Number(row['translationGroupId']) > 0 && row['termObjects'].length > 0 && existingTranslationGroupData.value.hasOwnProperty(row['translationGroupId'])){
                    row['termObjects'].forEach(term => {
                        if(existingTranslationGroupData.value[row['translationGroupId']].hasOwnProperty(term['language']) && existingTranslationGroupData.value[row['translationGroupId']][term['language']].length > 0){
                            row['translationGroupId'] = null;
                        }
                    });
                }
            }
            processSuccessResponse('Complete');
            processCsvDataArr();
        }

        return {
            currentProcess,
            procDisplayScrollAreaRef,
            processCancelling,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            taxonomicGroupVal,
            uploadedFile,
            cancelProcess,
            closePopup,
            initializeUpload,
            processFileSelection,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            processScientificNameChange,
            setScroller
        }
    }
};
