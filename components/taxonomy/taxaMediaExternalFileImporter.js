const taxaMediaExternalFileImporter = {
    template: `
        <div class="processor-container">
            <div class="processor-control-container">
                <q-card class="processor-control-card">
                    <q-card-section>
                        <q-card class="q-my-sm" flat bordered>
                            <q-card-section>
                                <div class="q-pa-md column q-col-gutter-sm">
                                    <div>
                                        This module will import externally hosted taxa media files onto the local server and 
                                        update the media records with the local urls.
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <selector-input-element :options="mediaTypeOptions" label="Media Type" :value="selectedMediaType" @update:value="(value) => selectedMediaType = value"></selector-input-element>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <checkbox-input-element label="Remove records with broken media links" :value="removeBrokenLinksVal" @update:value="(value) => removeBrokenLinksVal = Number(value) === 1"></checkbox-input-element>
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
                                    <q-btn color="secondary" @click="initializeProcess();" label="Start Process" dense aria-label="Start Process" :disabled="currentProcess" tabindex="0" />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess" :disabled="processCancelling" color="red" @click="cancelProcess();" label="Cancel" dense aria-label="Cancel" tabindex="0" />
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
        'checkbox-input-element': checkboxInputElement,
        'selector-input-element': selectorInputElement
    },
    setup(props, context) {
        const currentProcess = Vue.ref(null);
        const imageIdArr = Vue.ref([]);
        const mediaIdArr = Vue.ref([]);
        const mediaTypeOptions = [
            {value: 'all', label: 'All Media'},
            {value: 'images', label: 'Images Only'},
            {value: 'media', label: 'Sound & Video Only'},
            {value: 'thumbnail', label: 'Image Thumbnails Only'},
            {value: 'web', label: 'Web Images Only'},
            {value: 'original', label: 'Image Originals Only'}
        ];
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processCancelling = Vue.ref(false);
        const processingArr = Vue.ref([]);
        const processorDisplayArr = Vue.reactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const removeBrokenLinksVal = Vue.ref(false);
        const scrollProcess = Vue.ref(null);
        const selectedMediaType = Vue.ref('all');

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
            processingArr.value = [];
            processorDisplayArr.length = 0;
            processorDisplayDataArr = [];
            processorDisplayCurrentIndex.value = 0;
            processorDisplayIndex.value = 0;
            imageIdArr.value.length = 0;
            mediaIdArr.value.length = 0;
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

        function initializeProcess() {
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
            mediaTypeOptions,
            procDisplayScrollAreaRef,
            processCancelling,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            removeBrokenLinksVal,
            selectedMediaType,
            cancelProcess,
            initializeProcess,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            setScroller
        }
    }
};
