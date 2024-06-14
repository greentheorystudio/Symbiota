const taxonomicThesaurusMaintenanceModule = {
    props: {
        loading: {
            type: Boolean,
            default: false
        },
        selectedRanks: {
            type: Array,
            default: []
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
                        <div class="process-header">
                            Set Family Names
                        </div>
                        Set, or update, family names for all taxa within the Taxonomic Group.
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling && currentProcess === 'setUpdateFamilies'">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn :loading="currentProcess === 'setUpdateFamilies'" :disabled="currentProcess && currentProcess !== 'setUpdateFamilies'" color="secondary" @click="initializeSetUpdateFamilies('setUpdateFamiliesAccepted');" label="Start" dense />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess === 'setUpdateFamilies'" :disabled="processCancelling && currentProcess === 'setUpdateFamilies'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                </div>
                            </div>
                        </div>
                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                        <div class="process-header">
                            Remove Unaccepted Taxa
                        </div>
                        Remove unaccepted taxa within the Taxonomic Group that are not associated with other data
                        (e.g. occurrence records, checklists, images, etc.)
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling && currentProcess === 'removeUnacceptedTaxa'">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn :loading="currentProcess === 'removeUnacceptedTaxa'" :disabled="currentProcess && currentProcess !== 'removeUnacceptedTaxa'" color="secondary" @click="initializeRemoveUnacceptedTaxa();" label="Start" dense />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess === 'removeUnacceptedTaxa'" :disabled="processCancelling && currentProcess === 'removeUnacceptedTaxa'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                </div>
                            </div>
                        </div>
                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                        <div class="process-header">
                            Remove Taxa By Rank
                        </div>
                        Remove taxa within the Taxonomic Group that are of ranks not included in the Selected Taxonomic
                        Ranks and not associated with other data (e.g. occurrence records, checklists, images, etc.)
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling && currentProcess === 'removeTaxaByRank'">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn :loading="currentProcess === 'removeTaxaByRank'" :disabled="currentProcess && currentProcess !== 'removeTaxaByRank'" color="secondary" @click="initializeRemoveTaxaByRank();" label="Start" dense />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess === 'removeTaxaByRank'" :disabled="processCancelling && currentProcess === 'removeTaxaByRank'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                </div>
                            </div>
                        </div>
                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                        <div class="process-header">
                            Remove Taxa
                        </div>
                        Remove taxa within the Taxonomic Group that are not associated with other data (e.g. occurrence
                        records, checklists, images, etc.)
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling && currentProcess === 'removeTaxa'">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn :loading="currentProcess === 'removeTaxa'" :disabled="currentProcess && currentProcess !== 'removeTaxa'" color="secondary" @click="initializeRemoveTaxa();" label="Start" dense />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess === 'removeTaxa'" :disabled="processCancelling && currentProcess === 'removeTaxa'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                </div>
                            </div>
                        </div>
                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                        <div class="process-header">
                            Rebuild Taxonomic Hierarchy
                        </div>
                        Rebuild the taxonomic heirarchy data for taxa within the Taxonomic Group.
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling && currentProcess === 'rebuildHierarchy'">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn :loading="currentProcess === 'rebuildHierarchy'" :disabled="currentProcess && currentProcess !== 'rebuildHierarchy'" color="secondary" @click="initializeRebuildHierarchy();" label="Start" dense />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess === 'rebuildHierarchy'" :disabled="processCancelling && currentProcess === 'rebuildHierarchy'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                </div>
                            </div>
                        </div>
                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                        <div class="process-header">
                            Format Common Names
                        </div>
                        Format common names according to the formatting style selected below for all taxa within the
                        Taxonomic Group.
                        <div class="q-my-sm">
                            <q-option-group :options="commonNameFormattingOptions" type="radio" v-model="selectedCommonNameFormatting" :disable="loading" dense />
                        </div>
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling && currentProcess === 'formatCommonNames'">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn :loading="currentProcess === 'formatCommonNames'" :disabled="currentProcess && currentProcess !== 'formatCommonNames'" color="secondary" @click="initializeFormatCommonNames();" label="Start" dense />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess === 'formatCommonNames'" :disabled="processCancelling && currentProcess === 'formatCommonNames'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                </div>
                            </div>
                        </div>
                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                        <div class="process-header">
                            Remove Common Names
                        </div>
                        Remove common names for all taxa within the
                        Taxonomic Group.
                        <div class="processor-tool-control-container">
                            <div class="processor-cancel-message-container text-negative text-bold">
                                <template v-if="processCancelling && currentProcess === 'removeCommonNames'">
                                    Cancelling, please wait
                                </template>
                            </div>
                            <div class="processor-tool-button-container">
                                <div>
                                    <q-btn :loading="currentProcess === 'removeCommonNames'" :disabled="currentProcess && currentProcess !== 'removeCommonNames'" color="secondary" @click="initializeRemoveCommonNames();" label="Start" dense />
                                </div>
                                <div>
                                    <q-btn v-if="currentProcess === 'removeCommonNames'" :disabled="processCancelling && currentProcess === 'removeCommonNames'" color="red" @click="cancelProcess();" label="Cancel" dense />
                                </div>
                            </div>
                        </div>
                        <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
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
                                        <div><a class="text-bold cursor-pointer" @click="processorDisplayScrollUp();">Show previous 100 entries</a></div>
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
                                                        <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="q-ml-sm text-weight-bold text-green-9">
                                                            {{subproc.resultText}}
                                                        </div>
                                                        <div v-if="subproc.result === 'success' && subproc.type === 'undo'" class="q-ml-sm text-weight-bold text-green-9">
                                                            {{subproc.resultText}} <q-btn :disabled="undoButtonsDisabled" class="q-ml-md text-grey-9" color="warning" size="sm" @click="undoChangedSciname(proc.id,subproc.undoOrigName,subproc.undoChangedName);" label="Undo" dense />
                                                        </div>
                                                        <div v-if="subproc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                                            {{subproc.resultText}}
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-if="subproc.type === 'fuzzy'">
                                                    <template v-if="subproc.procText === 'skip'">
                                                        <div class="q-mx-xl q-my-sm fuzzy-match-row">
                                                            <div></div>
                                                            <div>
                                                                <q-btn :disabled="!(currentTaxon.sciname === proc.id)" class="q-ml-md" color="primary" size="sm" @click="runTaxThesaurusFuzzyMatchProcess();" label="Skip Taxon" dense />
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="q-mx-xl q-my-sm fuzzy-match-row">
                                                            <div class="text-weight-bold">
                                                                {{ subproc.procText }}
                                                            </div>
                                                            <div>
                                                                <q-btn :disabled="!(currentTaxon.sciname === proc.id)" class="q-ml-md" color="primary" size="sm" @click="selectFuzzyMatch(subproc.undoOrigName,subproc.undoChangedName,subproc.changedTid);" label="Select" dense />
                                                            </div>
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
                                        <div><a class="text-bold cursor-pointer" @click="processorDisplayScrollDown();">Show next 100 entries</a></div>
                                    </q-item-section>
                                </q-item>
                            </template>
                        </q-list>
                    </q-scroll-area>
                </q-card>
            </div>
        </div>
    `,
    setup(props, context) {
        const { getErrorResponseText, showNotification } = useCore();
        let abortController = null;
        const commonNameFormattingOptions = [
            { label: 'First letter of each word uppercase', value: 'upper-each' },
            { label: 'First letter uppercase', value: 'upper-first' },
            { label: 'All uppercase', value: 'upper-all' },
            { label: 'All lowercase', value: 'lower-all' }
        ];
        const currentCommonName = Vue.ref(null);
        const currentProcess = Vue.ref(null);
        const currentRank = Vue.ref(null);
        const currentTaxon = Vue.ref(null);
        const getTaxaImportIndex = Vue.ref(1);
        const hierarchyTidArr = Vue.ref([]);
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processCancelling = Vue.ref(false);
        const processingCommonNameArr = Vue.ref([]);
        const processingRankArr = Vue.ref([]);
        const processingTaxaArr = Vue.ref([]);
        const processorDisplayArr = Vue.reactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const rebuildHierarchyLoop = Vue.ref(0);
        const scrollProcess = Vue.ref(null);
        const selectedCommonNameFormatting = Vue.ref('upper-each');

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

        function addSubprocessToProcessorDisplay(id, type, text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
            parentProcObj['subs'].push(getNewSubprocessObject(currentTaxon.value['sciname'], type, text));
            const dataParentProcObj = processorDisplayDataArr.find(proc => proc['id'] === id);
            if(dataParentProcObj){
                dataParentProcObj['subs'].push(getNewSubprocessObject(currentTaxon.value['sciname'], type, text));
            }
        }

        function adjustUIEnd() {
            processCancelling.value = false;
            currentProcess.value = null;
            processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
            context.emit('update:loading', false);
        }

        function adjustUIStart(id) {
            processorDisplayArr.length = 0;
            processorDisplayDataArr = [];
            processorDisplayCurrentIndex.value = 0;
            processorDisplayIndex.value = 0;
            currentProcess.value = id;
            context.emit('update:loading', true);
        }

        function cancelProcess() {
            processCancelling.value = true;
            if(abortController){
                abortController.abort();
            }
        }

        function deleteCurrentTaxon(callback) {
            const formData = new FormData();
            formData.append('tid', currentTaxon.value['tid']);
            formData.append('action', 'deleteTaxonByTid');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then(() => {
                callback();
            });
        }

        function evaluateTaxonForAssociatedData() {
            const text = 'Evaluating ' + currentTaxon.value['sciname'] + ' for deletion';
            addSubprocessToProcessorDisplay(currentTaxon.value['sciname'], 'text', text);
            const formData = new FormData();
            formData.append('tid', currentTaxon.value['tid']);
            formData.append('action', 'evaluateTaxonForDeletion');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        if(Number(res) > 0){
                            processSubprocessErrorResponse(currentTaxon.value['sciname'],'Taxon associated with other data');
                            processTaxaArr();
                        }
                        else{
                            processSubprocessSuccessResponse(currentTaxon.value['sciname'],true,'Taxon deleted');
                            deleteCurrentTaxon(() => {
                                processTaxaArr();
                            });
                        }
                    });
                }
                else{
                    processSubprocessErrorResponse(currentTaxon.value['sciname'],'Error evaluating taxon');
                    processTaxaArr();
                }
            });
        }

        function getCommonNamesForTaxonomicGroup() {
            if(!processCancelling.value){
                abortController = new AbortController();
                const formData = new FormData();
                formData.append('parenttid', props.taxonomicGroupTid);
                formData.append('index', getTaxaImportIndex.value);
                formData.append('action', 'getCommonNamesByTaxonomicGroup');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    signal: abortController.signal,
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            if(resObj.length > 0){
                                processingCommonNameArr.value = processingCommonNameArr.value.concat(resObj);
                            }
                            if(resObj.length < 50000){
                                processSuccessResponse(true,'Complete');
                                processCommonNameArr();
                            }
                            else{
                                getTaxaImportIndex.value++;
                                getCommonNamesForTaxonomicGroup();
                            }
                        });
                    }
                    else{
                        const text = getErrorResponseText(response.status, response.statusText);
                        processErrorResponse(text);
                    }
                })
                .catch((err) => {});
            }
            else{
                adjustUIEnd();
            }
        }

        function getNewProcessObject(id, type, text) {
            const procObj = {
                id: id,
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

        function getNewSubprocessObject(id, type, text) {
            return {
                id: id,
                procText: text,
                type: type,
                loading: true,
                result: '',
                undoOrigName: '',
                undoChangedName: '',
                changedTid: 0,
                resultText: ''
            };
        }

        function getRankDataForTaxonomicGroup() {
            const text = 'Setting rank data for the Taxonomic Group';
            addProcessToProcessorDisplay(getNewProcessObject('setRankData','single', text));
            const formData = new FormData();
            formData.append('parenttid', props.taxonomicGroupTid);
            formData.append('action', 'getRankArrForTaxonomicGroup');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        processSuccessResponse(true, 'Complete');
                        if(currentProcess.value === 'removeTaxa' || currentProcess.value === 'removeUnacceptedTaxa'){
                            processingRankArr.value = resObj.reverse().slice();
                        }
                        else{
                            processingRankArr.value = resObj.slice();
                        }
                        processRankArr();
                    });
                }
                else{
                    const text = getErrorResponseText(response.status, response.statusText);
                    processErrorResponse(true,text);
                }
            });
        }

        function getTaxaArr(callback) {
            if(!processCancelling.value){
                abortController = new AbortController();
                const formData = new FormData();
                formData.append('parenttid', props.taxonomicGroupTid);
                if(currentRank.value){
                    formData.append('rankid', currentRank.value['rankid']);
                }
                formData.append('index', getTaxaImportIndex.value);
                if(currentProcess.value === 'removeUnacceptedTaxa'){
                    formData.append('action', 'getUnacceptedTaxaByTaxonomicGroup');
                }
                else{
                    formData.append('action', 'getAcceptedTaxaByTaxonomicGroup');
                }
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    signal: abortController.signal,
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            if(resObj.length > 0){
                                processingTaxaArr.value = processingTaxaArr.value.concat(resObj);
                            }
                            if(resObj.length < 50000){
                                processSuccessResponse(true,'Complete');
                                callback();
                            }
                            else{
                                getTaxaImportIndex.value++;
                                getTaxaArr(callback);
                            }
                        });
                    }
                    else{
                        const text = getErrorResponseText(response.status, response.statusText);
                        processErrorResponse(text);
                    }
                })
                .catch((err) => {});
            }
            else{
                adjustUIEnd();
            }
        }

        function initializeFormatCommonNames() {
            if(props.taxonomicGroupTid){
                processingCommonNameArr.value = [];
                getTaxaImportIndex.value = 1;
                currentCommonName.value = null;
                adjustUIStart('formatCommonNames');
                const text = 'Getting common names for taxa within the Taxonomic Group';
                addProcessToProcessorDisplay(getNewProcessObject('gettingCommonNames','single',text));
                getCommonNamesForTaxonomicGroup();
            }
            else{
                showNotification('negative', 'Please enter a Taxonomic Group.');
            }
        }

        function initializeRebuildHierarchy() {
            adjustUIStart('rebuildHierarchy');
            if(props.taxonomicGroupTid){
                currentRank.value = null;
                hierarchyTidArr.value = [];
                processingTaxaArr.value = [];
                getTaxaImportIndex.value = 1;
                const text = 'Getting taxa within the Taxonomic Group';
                addProcessToProcessorDisplay(getNewProcessObject('gettingRebuildHierarchyTaxa','single',text));
                getTaxaArr(() => {
                    processingTaxaArr.value.forEach((taxon) => {
                        if(!hierarchyTidArr.value.includes(Number(taxon['tid']))){
                            hierarchyTidArr.value.push(Number(taxon['tid']));
                        }
                    });
                    const text = 'Clearing taxonomic hierarchy in preparation for rebuild';
                    addProcessToProcessorDisplay(getNewProcessObject('clearHierarchyTaxa','single',text));
                    const formData = new FormData();
                    formData.append('tidarr', JSON.stringify(hierarchyTidArr.value));
                    formData.append('action', 'clearHierarchyTable');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            processSuccessResponse(true,'Complete');
                            primeTaxonomicHierarchy();
                        }
                        else{
                            processErrorResponse('Error clearing the taxonomic hierarchy');
                            adjustUIEnd();
                        }
                    });
                });
            }
            else{
                primeTaxonomicHierarchy();
            }
        }

        function initializeRemoveCommonNames() {
            if(props.taxonomicGroupTid){
                adjustUIStart('removeCommonNames');
                const text = 'Removing common names for taxa within the Taxonomic Group';
                addProcessToProcessorDisplay(getNewProcessObject('removeCommonNames','single',text));
                const formData = new FormData();
                formData.append('parenttid', props.taxonomicGroupTid);
                formData.append('action', 'removeCommonNamesInTaxonomicGroup');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(Number(res) > 0){
                            processSuccessResponse(true,'Complete');
                        }
                        else{
                            processErrorResponse('Error removing common names');
                        }
                        adjustUIEnd();
                    });
                });
            }
            else{
                showNotification('negative', 'Please enter a Taxonomic Group.');
            }
        }

        function initializeRemoveTaxa() {
            if(props.taxonomicGroupTid){
                processingRankArr.value = [];
                adjustUIStart('removeTaxa');
                getRankDataForTaxonomicGroup();
            }
            else{
                showNotification('negative', 'Please enter a Taxonomic Group.');
            }
        }

        function initializeRemoveTaxaByRank() {
            if(props.taxonomicGroupTid){
                processingRankArr.value = [];
                adjustUIStart('removeTaxaByRank');
                getRankDataForTaxonomicGroup();
            }
            else{
                showNotification('negative', 'Please enter a Taxonomic Group.');
            }
        }

        function initializeRemoveUnacceptedTaxa() {
            if(props.taxonomicGroupTid){
                processingRankArr.value = [];
                adjustUIStart('removeUnacceptedTaxa');
                getRankDataForTaxonomicGroup();
            }
            else{
                showNotification('negative', 'Please enter a Taxonomic Group.');
            }
        }

        function initializeSetUpdateFamilies(step) {
            if(props.taxonomicGroupTid){
                const formData = new FormData();
                formData.append('parenttid', props.taxonomicGroupTid);
                if(step === 'setUpdateFamiliesAccepted'){
                    adjustUIStart('setUpdateFamilies');
                    const text = 'Setting families for accepted taxa';
                    addProcessToProcessorDisplay(getNewProcessObject('setUpdateFamiliesAccepted','single',text));
                    formData.append('action', 'setUpdateFamiliesAccepted');
                }
                if(!processCancelling.value){
                    if(step === 'setUpdateFamiliesUnaccepted'){
                        const text = 'Setting families for unaccepted taxa';
                        addProcessToProcessorDisplay(getNewProcessObject('setUpdateFamiliesUnaccepted','single',text));
                        formData.append('action', 'setUpdateFamiliesUnaccepted');
                    }
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.text().then((text) => {
                                processSuccessResponse(true,'Complete: ' + text + ' records updated');
                                if(step === 'setUpdateFamiliesAccepted'){
                                    initializeSetUpdateFamilies('setUpdateFamiliesUnaccepted');
                                }
                                else{
                                    adjustUIEnd();
                                }
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status, response.statusText);
                            processErrorResponse(true,text);
                        }
                    });
                }
            }
            else{
                showNotification('negative', 'Please enter a Taxonomic Group.');
            }
        }

        function populateTaxonomicHierarchy() {
            if(rebuildHierarchyLoop.value < 40){
                const formData = new FormData();
                formData.append('action', 'populateHierarchyTable');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.text().then((res) => {
                            if(Number(res) > 0){
                                rebuildHierarchyLoop.value++;
                                populateTaxonomicHierarchy();
                            }
                            else{
                                processSuccessResponse(true,'Complete');
                                adjustUIEnd();
                            }
                        });
                    }
                    else{
                        processErrorResponse('Error rebuilding the taxonomic hierarchy');
                        adjustUIEnd();
                    }
                });
            }
            else{
                processErrorResponse('Error rebuilding the taxonomic hierarchy');
                adjustUIEnd();
            }
        }

        function primeTaxonomicHierarchy() {
            rebuildHierarchyLoop.value = 0;
            const text = 'Populating taxonomic hierarchy with new taxa';
            addProcessToProcessorDisplay(getNewProcessObject('populateHierarchyTaxa','single',text));
            const formData = new FormData();
            if(hierarchyTidArr.value.length > 0){
                formData.append('tidarr', JSON.stringify(hierarchyTidArr.value));
            }
            formData.append('action', 'primeHierarchyTable');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then(() => {
                        populateTaxonomicHierarchy();
                    });
                }
                else{
                    processErrorResponse('Error rebuilding the taxonomic hierarchy');
                    adjustUIEnd();
                }
            });
        }

        function processCommonName(name) {
            if(selectedCommonNameFormatting.value === 'upper-each'){
                const words = name.split(" ");
                for(let i = 0; i < words.length; i++){
                    words[i] = words[i][0].toUpperCase() + words[i].substring(1).toLowerCase();
                }
                name = words.join(" ");
            }
            else if(selectedCommonNameFormatting.value === 'upper-first'){
                name = name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
            }
            else if(selectedCommonNameFormatting.value === 'upper-all'){
                name = name.toUpperCase();
            }
            else if(selectedCommonNameFormatting.value === 'lower-all'){
                name = name.toLowerCase();
            }
            return name;
        }

        function processCommonNameArr() {
            if(!processCancelling.value && processingCommonNameArr.value.length > 0){
                currentCommonName.value = processingCommonNameArr.value[0];
                processingCommonNameArr.value.splice(0, 1);
                const text = 'Processing ' + currentCommonName.value['vernacularname'];
                addProcessToProcessorDisplay(getNewProcessObject(currentCommonName.value['vid'],'single',text));
                const commonNameData = {};
                commonNameData['vernacularname'] = processCommonName(currentCommonName.value['vernacularname']);
                const formData = new FormData();
                formData.append('vid', currentCommonName.value['vid']);
                formData.append('commonNameData', JSON.stringify(commonNameData));
                formData.append('action', 'editCommonName');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(Number(res) > 0){
                            processSuccessResponse(true,'Complete');
                        }
                        else{
                            processErrorResponse('Error updating common name');
                        }
                        processCommonNameArr();
                    });
                });
            }
            else{
                adjustUIEnd();
            }
        }

        function processErrorResponse(setCounts, text) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = false;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'error';
                    procObj['resultText'] = text;
                }
            }
            if(setCounts){
                adjustUIEnd();
            }
        }

        function processorDisplayScrollDown() {
            scrollProcess.value = 'scrollDown';
            processorDisplayCurrentIndex.value++;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processorDisplayScrollUp() {
            scrollProcess.value = 'scrollUp';
            processorDisplayCurrentIndex.value--;
            const newData = processorDisplayDataArr.slice((processorDisplayCurrentIndex.value * 100), ((processorDisplayCurrentIndex.value + 1) * 100));
            newData.forEach((data) => {
                processorDisplayArr.push(data);
            });
            resetScrollProcess();
        }

        function processRankArr() {
            if(!processCancelling.value && processingRankArr.value.length > 0){
                processingTaxaArr.value = [];
                getTaxaImportIndex.value = 1;
                currentRank.value = processingRankArr.value[0];
                processingRankArr.value.splice(0, 1);
                if(currentProcess.value === 'removeTaxa' || currentProcess.value === 'removeUnacceptedTaxa' || !props.selectedRanks.includes(Number(currentRank.value['rankid']))){
                    const text = 'Getting ' + currentRank.value['rankname'] + ' level ' + (currentProcess.value === 'removeUnacceptedTaxa' ? 'unaccepted' : 'accepted') + ' taxa';
                    addProcessToProcessorDisplay(getNewProcessObject(currentRank.value['rankname'],'single',text));
                    getTaxaArr(() => {
                        processTaxaArr();
                    });
                }
                else{
                    processRankArr();
                }
            }
            else{
                adjustUIEnd();
            }
        }

        function processTaxaArr() {
            if(!processCancelling.value){
                if(processingTaxaArr.value.length > 0){
                    currentTaxon.value = processingTaxaArr.value[0];
                    processingTaxaArr.value.splice(0, 1);
                    const text = 'Processing ' + currentTaxon.value['sciname'];
                    addProcessToProcessorDisplay(getNewProcessObject(currentTaxon.value['sciname'],'multi',text));
                    processSuccessResponse(false);
                    if(currentProcess.value === 'removeTaxaByRank'){
                        removeCurrentTaxonFromTaxonomicHierarchy();
                    }
                    else{
                        evaluateTaxonForAssociatedData();
                    }
                }
                else{
                    processRankArr();
                }
            }
            else{
                processErrorResponse(true,'Cancelled');
                adjustUIEnd();
            }
        }

        function processSubprocessErrorResponse(id, text) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
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

        function processSubprocessSuccessResponse(id, complete, text = null) {
            const parentProcObj = processorDisplayArr.find(proc => proc['id'] === id);
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

        function processSuccessResponse(complete, text = null) {
            const procObj = processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = !complete;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'success';
                    procObj['resultText'] = text;
                }
            }
        }

        function removeCurrentTaxonFromTaxonomicHierarchy() {
            const text = 'Removing ' + currentTaxon.value['sciname'] + ' from taxonomic hierarchy';
            addSubprocessToProcessorDisplay(currentTaxon.value['sciname'],'text',text);
            const formData = new FormData();
            formData.append('tid', currentTaxon.value['tid']);
            formData.append('parenttid', currentTaxon.value['parenttid']);
            formData.append('action', 'removeTaxonFromTaxonomicHierarchy');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        if(Number(res) === 1){
                            processSubprocessSuccessResponse(currentTaxon.value['sciname'],true,'Complete');
                            evaluateTaxonForAssociatedData();
                        }
                        else{
                            processSubprocessErrorResponse(currentTaxon.value['sciname'],'Error removing taxon from taxonomic hierarchy');
                            processTaxaArr();
                        }
                    });
                }
                else{
                    processSubprocessErrorResponse(currentTaxon.value['sciname'],'Error removing taxon from taxonomic hierarchy');
                    processTaxaArr();
                }
            });
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
            commonNameFormattingOptions,
            currentProcess,
            currentTaxon,
            procDisplayScrollAreaRef,
            processCancelling,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            scrollProcess,
            selectedCommonNameFormatting,
            cancelProcess,
            initializeFormatCommonNames,
            initializeRebuildHierarchy,
            initializeRemoveCommonNames,
            initializeRemoveTaxa,
            initializeRemoveTaxaByRank,
            initializeRemoveUnacceptedTaxa,
            initializeSetUpdateFamilies,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            setScroller
        }
    }
};
