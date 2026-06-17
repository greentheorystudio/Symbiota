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
                                                            at least one taxonomic group for which the terms are related in the box below. A source can 
                                                            be added for the uploaded terms by filling in the Enter Sources box below. Please do not use spaces 
                                                            in the column names.
                                                        </div>
                                                        <div v-if="!uploadedFile || taxonomicGroupVal.length === 0" class="text-bold text-red">
                                                            Please enter at least one Taxonomic Group and choose the CSV data file in the boxes below to continue.
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-grow">
                                                            <multiple-scientific-common-name-auto-complete label="Enter Taxonomic Groups" :sciname="taxonomicGroupVal" :limit-to-options="true" :name-string-mode="false" @update:sciname="processScientificNameChange"></multiple-scientific-common-name-auto-complete>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-grow">
                                                            <text-field-input-element data-type="textarea" label="Enter Sources" :value="sourcesVal" @update:value="(value) => sourcesVal = value"></text-field-input-element>
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
        const sourcesVal = Vue.ref(null);
        const taxonomicGroupVal = Vue.ref([]);
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

        function addSubprocessToProcessorDisplay(type, text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentProcess.value);
            parentProcObj['subs'].push(getNewSubprocessObject(type,text));
            const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === currentProcess.value);
            if(dataParentProcObj){
                dataParentProcObj['subs'].push(getNewSubprocessObject(type,text));
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

        function getNewSubprocessObject(type, text) {
            return {
                procText: text,
                type: type,
                loading: true,
                result: '',
                resultText: ''
            };
        }

        function initializeUpload() {
            adjustUIStart();
            const text = 'Processing CSV data';
            currentProcess.value = 'processingCsvData';
            addProcessToProcessorDisplay(getNewProcessObject('single', text));
            parseFile(uploadedFile.value, (fileContents) => {
                csvToArray(fileContents).then((csvData) => {
                    processFileCsvData(csvData);
                });
            });
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
                const languageArr = [];
                Object.keys(csvData[0]).forEach(field => {
                    if(!field.includes('_')){
                        languageArr.push(field);
                    }
                });
                csvData.forEach((dataObj) => {
                    const csvDataObj = {
                        termObjects: [],
                    };
                    languageArr.forEach((language) => {
                        if(dataObj[language]){
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
                            if(dataObj.hasOwnProperty(language + '_synonym') && dataObj[language + '_synonym']){
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
                            }
                        }
                    });
                    if(csvDataObj['termObjects'].length > 0){
                        csvDataArr.value.push(csvDataObj);
                    }
                });
                if(csvDataArr.value.length > 0){
                    processSuccessResponse('Complete');
                    console.log(csvDataArr.value);
                    //setTaxaIdData();
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

        function processSubprocessErrorResponse(text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentProcess.value);
            if(parentProcObj){
                parentProcObj['current'] = false;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'error';
                    subProcObj['resultText'] = text;
                }
            }
        }

        function processSubprocessSuccessResponse(complete, text = null) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === currentProcess.value);
            if(parentProcObj){
                parentProcObj['current'] = !complete;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'success';
                    subProcObj['resultText'] = text;
                }
            }
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

        return {
            currentProcess,
            procDisplayScrollAreaRef,
            processCancelling,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            sourcesVal,
            taxonomicGroupVal,
            uploadedFile,
            cancelProcess,
            initializeUpload,
            processFileSelection,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            processScientificNameChange,
            setScroller
        }
    }
};
