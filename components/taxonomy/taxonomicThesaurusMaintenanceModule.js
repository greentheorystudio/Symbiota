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
                                                                <q-btn :disabled="!(currentSciname === proc.id)" class="q-ml-md" color="primary" size="sm" @click="runTaxThesaurusFuzzyMatchProcess();" label="Skip Taxon" dense />
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="q-mx-xl q-my-sm fuzzy-match-row">
                                                            <div class="text-weight-bold">
                                                                {{ subproc.procText }}
                                                            </div>
                                                            <div>
                                                                <q-btn :disabled="!(currentSciname === proc.id)" class="q-ml-md" color="primary" size="sm" @click="selectFuzzyMatch(subproc.undoOrigName,subproc.undoChangedName,subproc.changedTid);" label="Select" dense />
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
    data() {
        return {
            commonNameFormattingOptions: [
                { label: 'First letter of each word uppercase', value: 'upper-each' },
                { label: 'First letter uppercase', value: 'upper-first' },
                { label: 'All uppercase', value: 'upper-all' },
                { label: 'All lowercase', value: 'lower-all' }
            ],
            currentCommonName: Vue.ref(null),
            currentRank: Vue.ref(null),
            currentTaxon: Vue.ref(null),
            getTaxaImportIndex: Vue.ref(1),
            hierarchyTidArr: Vue.ref([]),
            processCancelling: Vue.ref(false),
            processingCommonNameArr: Vue.ref([]),
            processingRankArr: Vue.ref([]),
            processingTaxaArr: Vue.ref([]),
            processorDisplayArr: Vue.ref([]),
            processorDisplayDataArr: Vue.ref([]),
            processorDisplayCurrentIndex: Vue.ref(0),
            processorDisplayIndex: Vue.ref(0),
            rebuildHierarchyLoop: Vue.ref(0),
            selectedCommonNameFormatting: Vue.ref('upper-each'),
        }
    },
    setup() {
        let currentProcess = Vue.ref(null);
        let procDisplayScrollAreaRef = Vue.ref(null);
        let procDisplayScrollHeight = Vue.ref(0);
        let scrollProcess = Vue.ref(null);
        return {
            currentProcess,
            procDisplayScrollAreaRef,
            scrollProcess,
            setScroller(info) {
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
        }
    },
    methods: {
        addProcessToProcessorDisplay(processObj){
            this.processorDisplayArr.push(processObj);
            if(this.processorDisplayArr.length > 100){
                const precessorArrSegment = this.processorDisplayArr.slice(0, 100);
                this.processorDisplayDataArr = this.processorDisplayDataArr.concat(precessorArrSegment);
                this.processorDisplayArr.splice(0, 100);
                this.processorDisplayIndex++;
                this.processorDisplayCurrentIndex = this.processorDisplayIndex;
            }
        },
        addSubprocessToProcessorDisplay(id,type,text){
            const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
            parentProcObj['subs'].push(this.getNewSubprocessObject(this.currentSciname,type,text));
            const dataParentProcObj = this.processorDisplayDataArr.find(proc => proc['id'] === id);
            if(dataParentProcObj){
                dataParentProcObj['subs'].push(this.getNewSubprocessObject(this.currentSciname,type,text));
            }
        },
        adjustUIEnd(){
            this.processCancelling = false;
            this.currentProcess = null;
            this.processorDisplayDataArr = this.processorDisplayDataArr.concat(this.processorDisplayArr);
            this.$emit('update:loading', false);
        },
        adjustUIStart(id){
            this.processorDisplayArr = [];
            this.processorDisplayDataArr = [];
            this.processorDisplayCurrentIndex = 0;
            this.processorDisplayIndex = 0;
            this.currentProcess = id;
            this.uppercontrolsdisabled = true;
            this.undoButtonsDisabled = true;
            this.$emit('update:loading', true);
        },
        cancelProcess(){
            this.processCancelling = true;
            cancelAPIRequest();
        },
        clearSubprocesses(id){
            const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
            parentProcObj['subs'] = [];
            const dataParentProcObj = this.processorDisplayDataArr.find(proc => proc['id'] === id);
            if(dataParentProcObj){
                dataParentProcObj['subs'] = [];
            }
        },
        deleteCurrentTaxon(callback){
            const formData = new FormData();
            formData.append('tid', this.currentTaxon['tid']);
            formData.append('action', 'deleteTaxonByTid');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then(() => {
                callback();
            });
        },
        evaluateTaxonForAssociatedData(){
            const text = 'Evaluating ' + this.currentTaxon['sciname'] + ' for deletion';
            this.addSubprocessToProcessorDisplay(this.currentTaxon['sciname'],'text',text);
            const formData = new FormData();
            formData.append('tid', this.currentTaxon['tid']);
            formData.append('action', 'checkTidForDataLinkages');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        if(Number(res) > 0){
                            this.processSubprocessErrorResponse(this.currentTaxon['sciname'],'Taxon associated with other data');
                            this.processTaxaArr();
                        }
                        else{
                            this.processSubprocessSuccessResponse(this.currentTaxon['sciname'],true,'Taxon deleted');
                            this.deleteCurrentTaxon(() => {
                                this.processTaxaArr();
                            });
                        }
                    });
                }
                else{
                    this.processSubprocessErrorResponse(this.currentTaxon['sciname'],'Error evaluating taxon');
                    this.processTaxaArr();
                }
            });
        },
        getCommonNamesForTaxonomicGroup(){
            if(!this.processCancelling){
                abortController = new AbortController();
                const formData = new FormData();
                formData.append('parenttid', this.taxonomicGroupTid);
                formData.append('index', this.getTaxaImportIndex);
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
                                this.processingCommonNameArr = this.processingCommonNameArr.concat(resObj);
                            }
                            if(resObj.length < 50000){
                                this.processSuccessResponse(true,'Complete');
                                this.processCommonNameArr();
                            }
                            else{
                                this.getTaxaImportIndex++;
                                this.getCommonNamesForTaxonomicGroup();
                            }
                        });
                    }
                    else{
                        const text = getErrorResponseText(response.status,response.statusText);
                        this.processErrorResponse(text);
                    }
                })
                .catch((err) => {});
            }
            else{
                this.adjustUIEnd();
            }
        },
        getNewProcessObject(id,type,text){
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
        },
        getNewSubprocessObject(id,type,text){
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
        },
        getRankDataForTaxonomicGroup(){
            const text = 'Setting rank data for the Taxonomic Group';
            this.addProcessToProcessorDisplay(this.getNewProcessObject('setRankData','single',text));
            const formData = new FormData();
            formData.append('parenttid', this.taxonomicGroupTid);
            formData.append('action', 'getRankArrForTaxonomicGroup');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        this.processSuccessResponse(true, 'Complete');
                        if(this.currentProcess === 'removeTaxa' || this.currentProcess === 'removeUnacceptedTaxa'){
                            this.processingRankArr = resObj.reverse().slice();
                        }
                        else{
                            this.processingRankArr = resObj.slice();
                        }
                        this.processRankArr();
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    this.processErrorResponse(true,text);
                }
            });
        },
        getTaxaArr(callback){
            if(!this.processCancelling){
                abortController = new AbortController();
                const formData = new FormData();
                formData.append('parenttid', this.taxonomicGroupTid);
                if(this.currentRank){
                    formData.append('rankid', this.currentRank['rankid']);
                }
                formData.append('index', this.getTaxaImportIndex);
                if(this.currentProcess === 'removeUnacceptedTaxa'){
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
                                this.processingTaxaArr = this.processingTaxaArr.concat(resObj);
                            }
                            if(resObj.length < 50000){
                                this.processSuccessResponse(true,'Complete');
                                callback();
                            }
                            else{
                                this.getTaxaImportIndex++;
                                this.getTaxaArr(callback);
                            }
                        });
                    }
                    else{
                        const text = getErrorResponseText(response.status,response.statusText);
                        this.processErrorResponse(text);
                    }
                })
                .catch((err) => {});
            }
            else{
                this.adjustUIEnd();
            }
        },
        initializeFormatCommonNames(){
            if(this.taxonomicGroupTid){
                this.processingCommonNameArr = [];
                this.getTaxaImportIndex = 1;
                this.currentCommonName = null;
                this.adjustUIStart('formatCommonNames');
                const text = 'Getting common names for taxa within the Taxonomic Group';
                this.addProcessToProcessorDisplay(this.getNewProcessObject('gettingCommonNames','single',text));
                this.getCommonNamesForTaxonomicGroup();
            }
            else{
                alert('Please enter a Taxonomic Group');
            }
        },
        initializeRebuildHierarchy(){
            this.adjustUIStart('rebuildHierarchy');
            if(this.taxonomicGroupTid){
                this.currentRank = null;
                this.hierarchyTidArr = [];
                this.processingTaxaArr = [];
                this.getTaxaImportIndex = 1;
                const text = 'Getting taxa within the Taxonomic Group';
                this.addProcessToProcessorDisplay(this.getNewProcessObject('gettingRebuildHierarchyTaxa','single',text));
                this.getTaxaArr(() => {
                    this.processingTaxaArr.forEach((taxon) => {
                        if(!this.hierarchyTidArr.includes(Number(taxon['tid']))){
                            this.hierarchyTidArr.push(Number(taxon['tid']));
                        }
                    });
                    const text = 'Clearing taxonomic hierarchy in preparation for rebuild';
                    this.addProcessToProcessorDisplay(this.getNewProcessObject('clearHierarchyTaxa','single',text));
                    const formData = new FormData();
                    formData.append('tidarr', JSON.stringify(this.hierarchyTidArr));
                    formData.append('action', 'clearHierarchyTable');
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            this.processSuccessResponse(true,'Complete');
                            this.primeTaxonomicHierarchy();
                        }
                        else{
                            this.processErrorResponse('Error clearing the taxonomic hierarchy');
                            this.adjustUIEnd();
                        }
                    });
                });
            }
            else{
                this.primeTaxonomicHierarchy();
            }
        },
        initializeRemoveCommonNames(){
            if(this.taxonomicGroupTid){
                this.adjustUIStart('removeCommonNames');
                const text = 'Removing common names for taxa within the Taxonomic Group';
                this.addProcessToProcessorDisplay(this.getNewProcessObject('removeCommonNames','single',text));
                const formData = new FormData();
                formData.append('parenttid', this.taxonomicGroupTid);
                formData.append('action', 'removeCommonNamesInTaxonomicGroup');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(Number(res) > 0){
                            this.processSuccessResponse(true,'Complete');
                        }
                        else{
                            this.processErrorResponse('Error removing common names');
                        }
                        this.adjustUIEnd();
                    });
                });
            }
            else{
                alert('Please enter a Taxonomic Group');
            }
        },
        initializeRemoveTaxa(){
            if(this.taxonomicGroupTid){
                this.processingRankArr = [];
                this.adjustUIStart('removeTaxa');
                this.getRankDataForTaxonomicGroup();
            }
            else{
                alert('Please enter a Taxonomic Group');
            }
        },
        initializeRemoveTaxaByRank(){
            if(this.taxonomicGroupTid){
                this.processingRankArr = [];
                this.adjustUIStart('removeTaxaByRank');
                this.getRankDataForTaxonomicGroup();
            }
            else{
                alert('Please enter a Taxonomic Group');
            }
        },
        initializeRemoveUnacceptedTaxa(){
            if(this.taxonomicGroupTid){
                this.processingRankArr = [];
                this.adjustUIStart('removeUnacceptedTaxa');
                this.getRankDataForTaxonomicGroup();
            }
            else{
                alert('Please enter a Taxonomic Group');
            }
        },
        initializeSetUpdateFamilies(step){
            if(this.taxonomicGroupTid){
                const formData = new FormData();
                formData.append('parenttid', this.taxonomicGroupTid);
                if(step === 'setUpdateFamiliesAccepted'){
                    this.adjustUIStart('setUpdateFamilies');
                    const text = 'Setting families for accepted taxa';
                    this.addProcessToProcessorDisplay(this.getNewProcessObject('setUpdateFamiliesAccepted','single',text));
                    formData.append('action', 'setUpdateFamiliesAccepted');
                }
                if(!this.processCancelling){
                    if(step === 'setUpdateFamiliesUnaccepted'){
                        const text = 'Setting families for unaccepted taxa';
                        this.addProcessToProcessorDisplay(this.getNewProcessObject('setUpdateFamiliesUnaccepted','single',text));
                        formData.append('action', 'setUpdateFamiliesUnaccepted');
                    }
                    fetch(taxonomyApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        if(response.status === 200){
                            response.text().then((text) => {
                                this.processSuccessResponse(true,'Complete: ' + text + ' records updated');
                                if(step === 'setUpdateFamiliesAccepted'){
                                    this.initializeSetUpdateFamilies('setUpdateFamiliesUnaccepted');
                                }
                                else{
                                    this.adjustUIEnd();
                                }
                            });
                        }
                        else{
                            const text = getErrorResponseText(response.status,response.statusText);
                            this.processErrorResponse(true,text);
                        }
                    });
                }
            }
            else{
                alert('Please enter a Taxonomic Group');
            }
        },
        populateTaxonomicHierarchy(){
            if(this.rebuildHierarchyLoop < 40){
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
                                this.rebuildHierarchyLoop++;
                                this.populateTaxonomicHierarchy();
                            }
                            else{
                                this.processSuccessResponse(true,'Complete');
                                this.adjustUIEnd();
                            }
                        });
                    }
                    else{
                        this.processErrorResponse('Error rebuilding the taxonomic hierarchy');
                        this.adjustUIEnd();
                    }
                });
            }
            else{
                this.processErrorResponse('Error rebuilding the taxonomic hierarchy');
                this.adjustUIEnd();
            }
        },
        primeTaxonomicHierarchy(){
            this.rebuildHierarchyLoop = 0;
            const text = 'Populating taxonomic hierarchy with new taxa';
            this.addProcessToProcessorDisplay(this.getNewProcessObject('populateHierarchyTaxa','single',text));
            const formData = new FormData();
            if(this.hierarchyTidArr.length > 0){
                formData.append('tidarr', JSON.stringify(this.hierarchyTidArr));
            }
            formData.append('action', 'primeHierarchyTable');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then(() => {
                        this.populateTaxonomicHierarchy();
                    });
                }
                else{
                    this.processErrorResponse('Error rebuilding the taxonomic hierarchy');
                    this.adjustUIEnd();
                }
            });
        },
        processCommonName(name){
            if(this.selectedCommonNameFormatting === 'upper-each'){
                const words = name.split(" ");
                for(let i = 0; i < words.length; i++){
                    words[i] = words[i][0].toUpperCase() + words[i].substring(1).toLowerCase();
                }
                name = words.join(" ");
            }
            else if(this.selectedCommonNameFormatting === 'upper-first'){
                name = name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
            }
            else if(this.selectedCommonNameFormatting === 'upper-all'){
                name = name.toUpperCase();
            }
            else if(this.selectedCommonNameFormatting === 'lower-all'){
                name = name.toLowerCase();
            }
            return name;
        },
        processCommonNameArr(){
            if(!this.processCancelling && this.processingCommonNameArr.length > 0){
                this.currentCommonName = this.processingCommonNameArr[0];
                this.processingCommonNameArr.splice(0, 1);
                const text = 'Processing ' + this.currentCommonName['vernacularname'];
                this.addProcessToProcessorDisplay(this.getNewProcessObject(this.currentCommonName['vid'],'single',text));
                const commonNameData = {};
                commonNameData['vernacularname'] = this.processCommonName(this.currentCommonName['vernacularname']);
                const formData = new FormData();
                formData.append('vid', this.currentCommonName['vid']);
                formData.append('commonNameData', JSON.stringify(commonNameData));
                formData.append('action', 'editCommonName');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(Number(res) > 0){
                            this.processSuccessResponse(true,'Complete');
                        }
                        else{
                            this.processErrorResponse('Error updating common name');
                        }
                        this.processCommonNameArr();
                    });
                });
            }
            else{
                this.adjustUIEnd();
            }
        },
        processErrorResponse(setCounts,text){
            const procObj = this.processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = false;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'error';
                    procObj['resultText'] = text;
                }
            }
            if(setCounts){
                this.adjustUIEnd();
            }
        },
        processorDisplayScrollDown(){
            this.scrollProcess = 'scrollDown';
            this.processorDisplayCurrentIndex++;
            this.processorDisplayArr = this.processorDisplayDataArr.slice((this.processorDisplayCurrentIndex * 100), ((this.processorDisplayCurrentIndex + 1) * 100));
            this.resetScrollProcess();
        },
        processorDisplayScrollUp(){
            this.scrollProcess = 'scrollUp';
            this.processorDisplayCurrentIndex--;
            this.processorDisplayArr = this.processorDisplayDataArr.slice((this.processorDisplayCurrentIndex * 100), ((this.processorDisplayCurrentIndex + 1) * 100));
            this.resetScrollProcess();
        },
        processRankArr(){
            if(!this.processCancelling && this.processingRankArr.length > 0){
                this.processingTaxaArr = [];
                this.getTaxaImportIndex = 1;
                this.currentRank = this.processingRankArr[0];
                this.processingRankArr.splice(0, 1);
                if(this.currentProcess === 'removeTaxa' || this.currentProcess === 'removeUnacceptedTaxa' || !this.selectedRanks.includes(Number(this.currentRank['rankid']))){
                    const text = 'Getting ' + this.currentRank['rankname'] + ' level ' + (this.currentProcess === 'removeUnacceptedTaxa' ? 'unaccepted' : 'accepted') + ' taxa';
                    this.addProcessToProcessorDisplay(this.getNewProcessObject(this.currentRank['rankname'],'single',text));
                    this.getTaxaArr(() => {
                        this.processTaxaArr();
                    });
                }
                else{
                    this.processRankArr();
                }
            }
            else{
                this.adjustUIEnd();
            }
        },
        processTaxaArr(){
            if(!this.processCancelling){
                if(this.processingTaxaArr.length > 0){
                    this.currentTaxon = this.processingTaxaArr[0];
                    this.processingTaxaArr.splice(0, 1);
                    const text = 'Processing ' + this.currentTaxon['sciname'];
                    this.addProcessToProcessorDisplay(this.getNewProcessObject(this.currentTaxon['sciname'],'multi',text));
                    this.processSuccessResponse(false);
                    if(this.currentProcess === 'removeTaxaByRank'){
                        this.removeCurrentTaxonFromTaxonomicHierarchy();
                    }
                    else{
                        this.evaluateTaxonForAssociatedData();
                    }
                }
                else{
                    this.processRankArr();
                }
            }
            else{
                this.processErrorResponse(true,'Cancelled');
                this.adjustUIEnd();
            }
        },
        processSubprocessErrorResponse(id,text){
            const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
            if(parentProcObj){
                parentProcObj['current'] = false;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'error';
                    subProcObj['resultText'] = text;
                }
            }
        },
        processSubprocessSuccessResponse(id,complete,text = null){
            const parentProcObj = this.processorDisplayArr.find(proc => proc['id'] === id);
            if(parentProcObj){
                parentProcObj['current'] = !complete;
                const subProcObj = parentProcObj['subs'].find(subproc => subproc['loading'] === true);
                if(subProcObj){
                    subProcObj['loading'] = false;
                    subProcObj['result'] = 'success';
                    subProcObj['resultText'] = text;
                }
            }
        },
        processSuccessResponse(complete,text = null){
            const procObj = this.processorDisplayArr.find(proc => proc['current'] === true);
            if(procObj){
                procObj['current'] = !complete;
                if(procObj['loading'] === true){
                    procObj['loading'] = false;
                    procObj['result'] = 'success';
                    procObj['resultText'] = text;
                }
            }
        },
        removeCurrentTaxonFromTaxonomicHierarchy(){
            const text = 'Removing ' + this.currentTaxon['sciname'] + ' from taxonomic hierarchy';
            this.addSubprocessToProcessorDisplay(this.currentTaxon['sciname'],'text',text);
            const formData = new FormData();
            formData.append('tid', this.currentTaxon['tid']);
            formData.append('parenttid', this.currentTaxon['parenttid']);
            formData.append('action', 'removeTaxonFromTaxonomicHierarchy');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        if(Number(res) === 1){
                            this.processSubprocessSuccessResponse(this.currentTaxon['sciname'],true,'Complete');
                            this.evaluateTaxonForAssociatedData();
                        }
                        else{
                            this.processSubprocessErrorResponse(this.currentTaxon['sciname'],'Error removing taxon from taxonomic hierarchy');
                            this.processTaxaArr();
                        }
                    });
                }
                else{
                    this.processSubprocessErrorResponse(this.currentTaxon['sciname'],'Error removing taxon from taxonomic hierarchy');
                    this.processTaxaArr();
                }
            });
        },
        resetScrollProcess(){
            setTimeout(() => {
                this.scrollProcess = null;
            }, 200);
        },
        cancelAPIRequest,
        getErrorResponseText
    }
};
