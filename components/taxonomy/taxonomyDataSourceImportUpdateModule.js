const taxonomyDataSourceImportUpdateModule = {
    props: {
        kingdomId: {
            type: Number,
            default: null
        },
        loading: {
            type: Boolean,
            default: false
        },
        selectedRanks: {
            type: Array,
            default: []
        },
        selectedRanksHigh: {
            type: Number,
            default: 0
        },
        taxonomicGroup: {
            type: Object,
            default: null
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
                        <div class="q-my-sm">
                            <taxonomy-data-source-bullet-selector :disable="loading" :selected-data-source="dataSource" @update:selected-data-source="updateSelectedDataSource"></taxonomy-data-source-bullet-selector>
                        </div>
                        <q-card class="q-my-sm" flat bordered>
                            <q-card-section>
                                <div>
                                    <q-checkbox v-model="updateMetadata" label="Update metadata for taxa" :disable="loading" />
                                </div>
                                <div>
                                    <q-checkbox v-model="updateParent" label="Update parent linkages for taxa" :disable="loading" />
                                </div>
                                <div>
                                    <q-checkbox v-model="updateAcceptance" label="Update acceptance for synonymized taxa" :disable="loading" />
                                </div>
                                <div>
                                    <q-checkbox v-model="importTaxa" label="Import accepted taxa not currently in the Taxonomic Thesaurus" :disable="loading" />
                                </div>
                                <div>
                                    <q-checkbox v-model="importCommonNames" label="Import common names" :disable="loading" />
                                </div>
                                <template v-if="importCommonNames">
                                    <div class="q-my-sm">
                                        <multiple-language-auto-complete :language-arr="commonNameLanguageArr" :disable="loading" label="Common Name Languages" @update:language="updateCommonNameLanguageArr"></multiple-language-auto-complete>
                                    </div>
                                    <div class="q-my-sm">
                                        <div class="text-subtitle1 text-weight-bold">Format Common Names</div>
                                        <q-option-group :options="commonNameFormattingOptions" type="radio" v-model="selectedCommonNameFormatting" :disable="loading" dense />
                                    </div>
                                </template>
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
                                    <q-btn :loading="loading" color="secondary" @click="initializeImportUpdate();" label="Start" dense />
                                </div>
                                <div>
                                    <q-btn v-if="loading" :disabled="processCancelling" color="red" @click="cancelProcess();" label="Cancel" dense />
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
    components: {
        'multiple-language-auto-complete': multipleLanguageAutoComplete,
        'taxonomy-data-source-bullet-selector': taxonomyDataSourceBulletSelector
    },
    setup(props, context) {
        const { getErrorResponseText, showNotification } = useCore();
        const store = useBaseStore();
        let abortController = null;
        const childrenSearchPrimingArr = Vue.ref([]);
        const clientRoot = store.getClientRoot;
        const colInitialSearchResults = Vue.ref([]);
        const commonNameFormattingOptions = [
            { label: 'First letter of each word uppercase', value: 'upper-each' },
            { label: 'First letter uppercase', value: 'upper-first' },
            { label: 'All uppercase', value: 'upper-all' },
            { label: 'All lowercase', value: 'lower-all' }
        ];
        const commonNameLanguageArr = Vue.ref([]);
        const commonNameLanguageIdArr = Vue.ref([]);
        const currentFamily = Vue.ref(null);
        const currentLocalChild = Vue.ref(null);
        const currentProcess = Vue.ref(null);
        const currentTaxonExternal = Vue.ref({});
        const currentTaxonLocal = Vue.ref({});
        const dataSource = Vue.ref('col');
        const familyArr = Vue.ref([]);
        const importCommonNames = Vue.ref(false);
        const importTaxa = Vue.ref(false);
        const itisInitialSearchResults = Vue.ref([]);
        const kingdomName = Vue.ref(null);
        const languageArr = Vue.ref([]);
        const nameTidIndex = Vue.ref({});
        const newEditedTidArr = Vue.ref([]);
        const procDisplayScrollAreaRef = Vue.ref(null);
        const procDisplayScrollHeight = Vue.ref(0);
        const processCancelling = Vue.ref(false);
        const processingArr = Vue.ref([]);
        const processorDisplayArr = Vue.shallowReactive([]);
        let processorDisplayDataArr = [];
        const processorDisplayCurrentIndex = Vue.ref(0);
        const processorDisplayIndex = Vue.ref(0);
        const queueArr = Vue.ref([]);
        const rankArr = Vue.ref(null);
        const rebuildHierarchyLoop = Vue.ref(0);
        const scrollProcess = Vue.ref(null);
        const selectedCommonNameFormatting = Vue.ref('upper-each');
        const setAddTaxaArr = Vue.ref([]);
        const targetTaxonIdentifier = Vue.ref(null);
        const targetTaxonLocal = Vue.ref(null);
        const taxaToAddArr = Vue.ref([]);
        const taxonSearchResults = Vue.ref([]);
        const updateAcceptance = Vue.ref(false);
        const updateMetadata = Vue.ref(true);
        const updateParent = Vue.ref(true);

        function addFamilyToFamilyArr(familyName) {
            const familyObj = {};
            familyObj['name'] = familyName;
            familyObj['processingArr'] = [];
            familyObj['queueArr'] = [];
            familyArr.value.push(familyObj);
        }

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

        function addTaxonCommonName(tid, name, langid) {
            const formData = new FormData();
            formData.append('action', 'addTaxonCommonName');
            formData.append('tid', tid);
            formData.append('name', name);
            formData.append('langid', langid);
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            });
        }

        function addTaxonIdentifier(tid, identifier) {
            const formData = new FormData();
            formData.append('action', 'addTaxonIdentifier');
            formData.append('tid', tid);
            formData.append('idname', dataSource.value);
            formData.append('id', identifier);
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            });
        }

        function addTaxonToThesaurus(taxon, callback) {
            const rankId = Number(taxon['rankid']);
            const newTaxonObj = {};
            newTaxonObj['sciname'] = taxon['sciname'];
            newTaxonObj['author'] = taxon['author'];
            newTaxonObj['kingdomid'] = rankId > 10 ? props.kingdomId : '';
            newTaxonObj['rankid'] = rankId;
            newTaxonObj['acceptstatus'] = taxon.hasOwnProperty('acceptstatus') ? taxon['acceptstatus'] : 1;
            newTaxonObj['tidaccepted'] = (taxon.hasOwnProperty('tidaccepted') && taxon['tidaccepted']) ? taxon['tidaccepted'] : '';
            newTaxonObj['parenttid'] = taxon['parenttid'];
            newTaxonObj['family'] = taxon['family'];
            newTaxonObj['source'] = getDataSourceName();
            newTaxonObj['source-name'] = dataSource.value;
            newTaxonObj['source-id'] = taxon['id'];
            const formData = new FormData();
            formData.append('taxon', JSON.stringify(newTaxonObj));
            formData.append('action', 'addTaxon');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    if(res && Number(res) > 0){
                        taxon['tid'] = Number(res);
                        taxon['identifiers'] = [{
                            name: dataSource.value,
                            identifier: taxon['id']
                        }];
                        taxon['tidaccepted'] = newTaxonObj['tidaccepted'] !== '' ? Number(newTaxonObj['tidaccepted']) : taxon['tid'];
                        taxon['commonnames'] = [];
                        taxon['children'] = [];
                        callback(taxon);
                    }
                    else{
                        callback(null,'Error loading taxon');
                    }
                });
            });
        }

        function adjustUIEnd() {
            processCancelling.value = false;
            context.emit('update:loading', false);
            processorDisplayDataArr = processorDisplayDataArr.concat(processorDisplayArr);
        }

        function adjustUIStart() {
            childrenSearchPrimingArr.value = [];
            colInitialSearchResults.value = [];
            currentFamily.value = null;
            currentLocalChild.value = null;
            currentProcess.value = null;
            currentTaxonExternal.value = Object.assign({}, {});
            currentTaxonLocal.value = Object.assign({}, {});
            familyArr.value = [];
            itisInitialSearchResults.value = [];
            nameTidIndex.value = Object.assign({}, {});
            newEditedTidArr.value = [];
            processingArr.value = [];
            processorDisplayArr.length = 0;
            processorDisplayDataArr = [];
            processorDisplayCurrentIndex.value = 0;
            processorDisplayIndex.value = 0;
            queueArr.value = [];
            rebuildHierarchyLoop.value = 0;
            setAddTaxaArr.value = [];
            targetTaxonIdentifier.value = null;
            targetTaxonLocal.value = null;
            taxaToAddArr.value = [];
            taxonSearchResults.value = [];
            context.emit('update:loading', true);
        }

        function cancelProcess() {
            processCancelling.value = true;
            if(abortController){
                abortController.abort();
            }
        }

        function currentTaxonProcessAcceptance() {
            if(updateAcceptance.value && currentTaxonExternal.value['tidaccepted'] && currentTaxonLocal.value['tidaccepted'] && Number(currentTaxonExternal.value['tidaccepted']) !== Number(currentTaxonLocal.value['tidaccepted'])){
                const subtext = 'Updating acceptance in Taxonomic Thesaurus';
                addSubprocessToProcessorDisplay('text',subtext);
                updateTaxonTidAccepted(Object.assign({}, currentTaxonExternal.value),(errorText = null) => {
                    if(errorText && errorText !== ''){
                        processSubprocessErrorResponse(errorText);
                        updateTaxonomicHierarchy(() => {
                            adjustUIEnd();
                        });
                    }
                    else{
                        processSubprocessSuccessResponse(false);
                        currentTaxonProcessParent();
                    }
                });
            }
            else{
                currentTaxonProcessParent();
            }
        }

        function currentTaxonProcessChildren() {
            if(currentTaxonExternal.value['children'].length > 0){
                const subtext = 'Processing subtaxa';
                addSubprocessToProcessorDisplay('text',subtext);
                currentTaxonExternal.value['children'].forEach((child) => {
                    child['parenttid'] = currentTaxonExternal.value['tid'];
                    child['family'] = currentTaxonExternal.value['family'] !== '' ? currentTaxonExternal.value['family'] : '';
                    if(child['family'] === ''){
                        if(Number(child['rankid']) === 140){
                            child['family'] = child['sciname'];
                        }
                        else{
                            child['family'] = currentFamily.value;
                        }
                    }
                    child['tid'] = null;
                    child['tidaccepted'] = null;
                    const localChild = currentTaxonLocal.value['children'].find(lchild => lchild['sciname'] === child['sciname']);
                    if(localChild){
                        child['tid'] = localChild['tid'];
                        child['tidaccepted'] = localChild['tid'];
                        const index = currentTaxonLocal.value['children'].indexOf(localChild);
                        currentTaxonLocal.value['children'].splice(index,1);
                    }
                    if(Number(child['rankid']) <= 140){
                        queueArr.value.push(child);
                    }
                    else if(child['family'] !== ''){
                        let familyObj = familyArr.value.find(family => family['name'] === child['family']);
                        if(!familyObj){
                            addFamilyToFamilyArr(child['family']);
                            familyObj = familyArr.value.find(family => family['name'] === child['family']);
                        }
                        familyObj['queueArr'].push(child);
                    }
                });
                if(updateAcceptance.value && currentTaxonLocal.value['children'].length > 0 && currentTaxonLocal.value['rankid'] < props.selectedRanksHigh){
                    processSubprocessSuccessResponse(false);
                }
                else{
                    processSubprocessSuccessResponse(true,'Complete');
                }
            }
            if(updateAcceptance.value && currentTaxonLocal.value['children'].length > 0 && currentTaxonLocal.value['rankid'] < props.selectedRanksHigh){
                const subtext = 'Updating acceptance for previously existing child taxa';
                addSubprocessToProcessorDisplay('text',subtext);
                currentTaxonProcessLocalChildren();
            }
            else{
                processProcessingArrays();
            }
        }

        function currentTaxonProcessCommonNames() {
            if(importCommonNames.value && currentTaxonExternal.value['tid'] && currentTaxonExternal.value['commonnames'].length > 0){
                const subtext = 'Adding common names';
                addSubprocessToProcessorDisplay('text',subtext);
                currentTaxonExternal.value['commonnames'].forEach((commonname) => {
                    const existingName = currentTaxonLocal.value['commonnames'].length > 0 ? currentTaxonLocal.value['commonnames'].find(name => (name['commonname'].toLowerCase() === commonname['name'].toLowerCase() && Number(name['langid']) === Number(commonname['langid']))) : null;
                    if(!existingName){
                        addTaxonCommonName(currentTaxonExternal.value['tid'],commonname['name'],commonname['langid']);
                    }
                });
                processSubprocessSuccessResponse(false);
            }
            currentTaxonProcessChildren();
        }

        function currentTaxonProcessLocalChildren() {
            if(updateAcceptance.value && currentTaxonLocal.value['children'].length > 0){
                taxonSearchResults.value = [];
                currentLocalChild.value = Object.assign({}, currentTaxonLocal.value['children'][0]);
                currentTaxonLocal.value['children'].splice(0, 1);
                findExternalTaxonBySciname(currentLocalChild.value['sciname'],(errorText = null) => {
                    if(errorText){
                        currentTaxonProcessLocalChildren();
                    }
                    else{
                        validateExternalTaxonSearchResults(false);
                    }
                });
            }
            else{
                processSubprocessSuccessResponse(true,'Complete');
                processProcessingArrays();
            }
        }

        function currentTaxonProcessMetadata() {
            if(
                updateMetadata.value && currentTaxonExternal.value['tid'] &&
                ((currentTaxonExternal.value['author'] && (currentTaxonExternal.value['author'] !== currentTaxonLocal.value['author'])) ||
                    props.kingdomId !== Number(currentTaxonLocal.value['kingdomid']) ||
                    (currentTaxonExternal.value['rankid'] && (Number(currentTaxonExternal.value['rankid']) !== Number(currentTaxonLocal.value['rankid']))) ||
                    (currentTaxonExternal.value['family'] && (currentTaxonExternal.value['family'] !== currentTaxonLocal.value['family'])))
            ){
                const subtext = 'Updating taxon in the Taxonomic Thesaurus';
                addSubprocessToProcessorDisplay('text',subtext);
                const taxonData = {};
                taxonData['tid'] = currentTaxonExternal.value['tid'];
                if(props.kingdomId !== Number(currentTaxonLocal.value['kingdomid'])){
                    taxonData['kingdomid'] = props.kingdomId;
                }
                if(currentTaxonExternal.value['author'] && (currentTaxonExternal.value['author'] !== currentTaxonLocal.value['author'])){
                    taxonData['author'] = currentTaxonExternal.value['author'];
                }
                if(currentTaxonExternal.value['rankid'] && (Number(currentTaxonExternal.value['rankid']) !== Number(currentTaxonLocal.value['rankid']))){
                    taxonData['rankid'] = currentTaxonExternal.value['rankid'];
                }
                if(currentTaxonExternal.value['family'] && (currentTaxonExternal.value['family'] !== currentTaxonLocal.value['family'])){
                    taxonData['family'] = currentTaxonExternal.value['family'];
                }
                taxonData['source'] = getDataSourceName();
                editTaxonInThesaurus(taxonData,(errorText = null) => {
                    if(errorText){
                        processSubprocessErrorResponse(errorText);
                        updateTaxonomicHierarchy(() => {
                            adjustUIEnd();
                        });
                    }
                    else{
                        processSubprocessSuccessResponse(false);
                        currentTaxonProcessAcceptance();
                    }
                });
            }
            else{
                currentTaxonProcessAcceptance();
            }
        }

        function currentTaxonProcessParent() {
            if(updateParent.value && currentTaxonExternal.value['parenttid'] && Number(currentTaxonExternal.value['parenttid']) !== Number(currentTaxonLocal.value['parenttid'])){
                const subtext = 'Updating parent taxon in Taxonomic Thesaurus';
                addSubprocessToProcessorDisplay('text',subtext);
                updateTaxonParent(currentTaxonExternal.value['parenttid'],currentTaxonExternal.value['tid'],(errorText = null) => {
                    if(errorText && errorText !== ''){
                        processSubprocessErrorResponse(errorText);
                        updateTaxonomicHierarchy(() => {
                            adjustUIEnd();
                        });
                    }
                    else{
                        processSubprocessSuccessResponse(false);
                        currentTaxonProcessCommonNames();
                    }
                });
            }
            else{
                currentTaxonProcessCommonNames();
            }
        }

        function currentTaxonValidate() {
            if(currentTaxonExternal.value['tid']){
                const dataSourceIdObj = currentTaxonLocal.value['identifiers'].find(obj => obj['name'] === dataSource.value);
                if(!dataSourceIdObj){
                    addTaxonIdentifier(currentTaxonLocal.value['tid'],currentTaxonExternal.value['id']);
                }
                currentTaxonProcessMetadata();
            }
            else{
                if(importTaxa.value){
                    const subtext = 'Adding taxon to the Taxonomic Thesaurus';
                    addSubprocessToProcessorDisplay('text',subtext);
                    addTaxonToThesaurus(Object.assign({}, currentTaxonExternal.value),(newTaxon,errorText = null) => {
                        if(errorText){
                            processSubprocessErrorResponse(errorText);
                            updateTaxonomicHierarchy(() => {
                                adjustUIEnd();
                            });
                        }
                        else{
                            const newTid = Number(newTaxon['tid']);
                            newEditedTidArr.value.push(newTid);
                            currentTaxonExternal.value['tid'] = newTid;
                            currentTaxonExternal.value['tidaccepted'] = newTid;
                            currentTaxonLocal.value = Object.assign({}, newTaxon);
                            processSubprocessSuccessResponse(false);
                            currentTaxonProcessCommonNames();
                        }
                    });
                }
                else{
                    currentTaxonExternal.value['tid'] = null;
                    currentTaxonExternal.value['tidaccepted'] = null;
                    currentTaxonLocal.value = Object.assign({}, currentTaxonExternal.value);
                    currentTaxonProcessCommonNames();
                }
            }
        }

        function editTaxonInThesaurus(taxonData, callback) {
            const tid = taxonData.hasOwnProperty('tid') ? taxonData['tid'] : null;
            if(tid){
                const formData = new FormData();
                formData.append('tid', tid);
                formData.append('taxonData', JSON.stringify(taxonData));
                formData.append('action', 'editTaxon');
                fetch(taxonomyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(res && res !== ''){
                            callback(res);
                        }
                        else{
                            callback();
                        }
                    });
                });
            }
            else{
                callback();
            }
        }

        function findCOLExternalTaxonChildren(callback) {
            if(childrenSearchPrimingArr.value.length > 0){
                const currentId = childrenSearchPrimingArr.value[0];
                childrenSearchPrimingArr.value.splice(0, 1);
                const url = 'https://api.catalogueoflife.org/dataset/9840/tree/' + currentId + '/children?limit=100000';
                const formData = new FormData();
                formData.append('url', url);
                formData.append('action', 'get');
                fetch(proxyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((res) => {
                            if(res.hasOwnProperty('total') && Number(res['total']) > 0){
                                const resultArr = res['result'];
                                resultArr.forEach((child) => {
                                    if(child['status'] === 'accepted'){
                                        const rankname = child['rank'].toLowerCase();
                                        const rankid = rankArr.value.hasOwnProperty(rankname) ? Number(rankArr.value[rankname]) : null;
                                        if(rankid && props.selectedRanks.includes(rankid) && child['name'] !== currentTaxonExternal.value['sciname']){
                                            const newChildObj = {};
                                            newChildObj['id'] = child['id'];
                                            newChildObj['sciname'] = child['name'];
                                            newChildObj['author'] = child['authorship'];
                                            newChildObj['rankid'] = rankid;
                                            currentTaxonExternal.value['children'].push(newChildObj);
                                        }
                                        else if(!rankid || rankid <= props.selectedRanksHigh){
                                            childrenSearchPrimingArr.value.push(child['id']);
                                        }
                                    }
                                });
                                findCOLExternalTaxonChildren(callback);
                            }
                            else{
                                findCOLExternalTaxonChildren(callback);
                            }
                        });
                    }
                    else{
                        findCOLExternalTaxonChildren(callback);
                    }
                });
            }
            else{
                processSubprocessSuccessResponse(false);
                callback();
            }
        }

        function findCOLTaxonById(id, callback) {
            colInitialSearchResults.value = [];
            const url = 'https://api.catalogueoflife.org/dataset/9840/taxon/' + id + '/info';
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((res) => {
                        callback(res);
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    callback(text);
                }
            });
        }

        function findCOLTaxonBySciname(sciname, callback) {
            colInitialSearchResults.value = [];
            const url = 'http://webservice.catalogueoflife.org/col/webservice?response=full&format=json&name=' + sciname;
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((res) => {
                        processGetCOLTaxonByScinameResponse(res,callback);
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    callback(text);
                }
            });
        }

        function findExternalTaxonBySciname(sciname, callback) {
            if(dataSource.value === 'col'){
                findCOLTaxonBySciname(sciname,callback);
            }
            else if(dataSource.value === 'itis'){
                findITISTaxonBySciname(sciname,callback);
            }
            else if(dataSource.value === 'worms'){
                findWoRMSTaxonBySciname(sciname,callback);
            }
        }

        function findITISExternalTaxonChildren(callback) {
            if(childrenSearchPrimingArr.value.length > 0){
                const currentId = childrenSearchPrimingArr.value[0];
                childrenSearchPrimingArr.value.splice(0, 1);
                const url = 'https://www.itis.gov/ITISWebService/jsonservice/getHierarchyDownFromTSN?tsn=' + currentId;
                const formData = new FormData();
                formData.append('url', url);
                formData.append('action', 'get');
                fetch(proxyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((res) => {
                            if(res && res.hasOwnProperty('hierarchyList') && res['hierarchyList'].length > 0){
                                const resultArr = res['hierarchyList'];
                                resultArr.forEach((child) => {
                                    if(child){
                                        const rankname = child['rankName'].toLowerCase();
                                        const rankid = rankArr.value.hasOwnProperty(rankname) ? Number(rankArr.value[rankname]) : null;
                                        if(rankid && props.selectedRanks.includes(rankid) && child['taxonName'] !== currentTaxonExternal.value['sciname']){
                                            const newChildObj = {};
                                            newChildObj['id'] = child['tsn'];
                                            newChildObj['sciname'] = child['taxonName'];
                                            newChildObj['author'] = child['author'];
                                            newChildObj['rankid'] = rankid;
                                            currentTaxonExternal.value['children'].push(newChildObj);
                                        }
                                        else if(!rankid || rankid <= props.selectedRanksHigh){
                                            childrenSearchPrimingArr.value.push(child['tsn']);
                                        }
                                    }
                                });
                                findITISExternalTaxonChildren(callback);
                            }
                            else{
                                findITISExternalTaxonChildren(callback);
                            }
                        });
                    }
                    else{
                        findITISExternalTaxonChildren(callback);
                    }
                });
            }
            else{
                processSubprocessSuccessResponse(false);
                callback();
            }
        }

        function findITISTaxonBySciname(sciname, callback) {
            itisInitialSearchResults.value = [];
            const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/searchByScientificName?srchKey=' + sciname;
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((res) => {
                        if(res){
                            processGetITISTaxonByScinameResponse(res,callback);
                        }
                        else{
                            callback('Not found');
                        }
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    callback(text);
                }
            });
        }

        function findTaxonBySciname(sciname, callback) {
            const formData = new FormData();
            formData.append('action', 'getTaxonFromSciname');
            formData.append('sciname', sciname);
            formData.append('kingdomid', props.kingdomId);
            formData.append('includeCommonNames', (importCommonNames.value ? '1' : '0'));
            formData.append('includeChildren', '1');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        callback(resObj);
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    callback(null,text);
                }
            });
        }

        function findTaxonByTid(tid, callback) {
            const formData = new FormData();
            formData.append('action', 'getTaxonFromTid');
            formData.append('tid', tid);
            formData.append('includeCommonNames', (importCommonNames.value ? '1' : '0'));
            formData.append('includeChildren', '1');
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        callback(resObj);
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    callback(null,text);
                }
            });
        }

        function findWoRMSExternalTaxonChildren(callback) {
            if(childrenSearchPrimingArr.value.length > 0){
                const currentId = childrenSearchPrimingArr.value[0];
                childrenSearchPrimingArr.value.splice(0, 1);
                const url = 'https://www.marinespecies.org/rest/AphiaChildrenByAphiaID/' + currentId + '?marine_only=false';
                const formData = new FormData();
                formData.append('url', url);
                formData.append('action', 'get');
                fetch(proxyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((res) => {
                            if(res.length > 0){
                                res.forEach((child) => {
                                    if(child['status'] === 'accepted'){
                                        const rankid = child.hasOwnProperty('taxonRankID') ? Number(child['taxonRankID']) : null;
                                        if(rankid && props.selectedRanks.includes(rankid) && child['scientificname'] !== currentTaxonExternal.value['sciname']){
                                            const newChildObj = {};
                                            newChildObj['id'] = child['AphiaID'];
                                            newChildObj['sciname'] = child['scientificname'];
                                            newChildObj['author'] = child['authority'];
                                            newChildObj['rankid'] = rankid;
                                            currentTaxonExternal.value['children'].push(newChildObj);
                                        }
                                        else if(!rankid || rankid <= props.selectedRanksHigh){
                                            childrenSearchPrimingArr.value.push(child['AphiaID']);
                                        }
                                    }
                                });
                                findWoRMSExternalTaxonChildren(callback);
                            }
                            else{
                                findWoRMSExternalTaxonChildren(callback);
                            }
                        });
                    }
                    else{
                        findWoRMSExternalTaxonChildren(callback);
                    }
                });
            }
            else{
                processSubprocessSuccessResponse(false);
                callback();
            }
        }

        function findWoRMSTaxonBySciname(sciname, callback) {
            const url = 'https://www.marinespecies.org/rest/AphiaIDByName/' + sciname + '?marine_only=false';
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        if(res && Number(res) > 0){
                            getWoRMSNameSearchResultsRecord(res,callback);
                        }
                        else{
                            callback('Not found');
                        }
                    });
                }
                else if(response.status === 204){
                    callback('Not found');
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    callback(text);
                }
            });
        }

        function getCOLExternalTaxonCommonNames(callback) {
            const url = 'https://api.catalogueoflife.org/dataset/9840/taxon/' + currentTaxonExternal.value['id'] + '/vernacular';
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((res) => {
                        if(res.length > 0){
                            res.forEach((cName) => {
                                const langIso2Code = cName.hasOwnProperty('language') ? cName['language'] : null;
                                const langObj = langIso2Code ? languageArr.value.find(lang => lang['iso-2'] === langIso2Code) : null;
                                if(commonNameLanguageIdArr.value.length === 0 || (langObj && commonNameLanguageIdArr.value.includes(Number(langObj['langid'])))){
                                    const cNameObj = {};
                                    cNameObj['name'] = processCommonName(cName['name']);
                                    cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                    currentTaxonExternal.value['commonnames'].push(cNameObj);
                                }
                            });
                        }
                        processSubprocessSuccessResponse(false);
                        if(currentTaxonExternal.value['rankid'] < props.selectedRanksHigh){
                            getExternalChildren(callback);
                        }
                        else{
                            callback();
                        }
                    });
                }
                else{
                    processSubprocessSuccessResponse(false);
                    if(currentTaxonExternal.value['rankid'] < props.selectedRanksHigh){
                        getExternalChildren(callback);
                    }
                    else{
                        callback();
                    }
                }
            });
        }

        function getDataSourceName() {
            if(dataSource.value === 'col'){
                return 'Catalogue of Life';
            }
            else if(dataSource.value === 'itis'){
                return 'Integrated Taxonomic Information System';
            }
            else if(dataSource.value === 'worms'){
                return 'World Register of Marine Species';
            }
        }

        function getExternalChildren(callback) {
            const subtext = 'Getting subtaxa';
            addSubprocessToProcessorDisplay('text',subtext);
            childrenSearchPrimingArr.value = [];
            childrenSearchPrimingArr.value.push(currentTaxonExternal.value['id']);
            if(dataSource.value === 'col'){
                findCOLExternalTaxonChildren(callback);
            }
            else if(dataSource.value === 'itis'){
                findITISExternalTaxonChildren(callback);
            }
            else if(dataSource.value === 'worms'){
                findWoRMSExternalTaxonChildren(callback);
            }
        }

        function getExternalCommonNames(callback) {
            if(importCommonNames.value && currentTaxonExternal.value['commonnames'].length === 0){
                const subtext = 'Getting common names';
                addSubprocessToProcessorDisplay('text',subtext);
                if(dataSource.value === 'col'){
                    getCOLExternalTaxonCommonNames(callback);
                }
                else if(dataSource.value === 'itis'){
                    getITISExternalTaxonCommonNames(callback);
                }
                else if(dataSource.value === 'worms'){
                    getWoRMSExternalTaxonCommonNames(callback);
                }
            }
            else{
                if(currentTaxonExternal.value['rankid'] < props.selectedRanksHigh){
                    getExternalChildren(callback);
                }
                else{
                    callback();
                }
            }
        }

        function getITISExternalTaxonCommonNames(callback) {
            const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/getCommonNamesFromTSN?tsn=' + currentTaxonExternal.value['id'];
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((res) => {
                        if(res.hasOwnProperty('commonNames') && res['commonNames'].length > 0){
                            res['commonNames'].forEach((cName) => {
                                if(cName){
                                    const langObj = (cName.hasOwnProperty('language') && cName['language']) ? languageArr.value.find(lang => lang['name'] === cName['language']) : null;
                                    if(commonNameLanguageIdArr.value.length === 0 || (langObj && commonNameLanguageIdArr.value.includes(Number(langObj['langid'])))){
                                        const cNameObj = {};
                                        cNameObj['name'] = processCommonName(cName['commonName']);
                                        cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                        currentTaxonExternal.value['commonnames'].push(cNameObj);
                                    }
                                }
                            });
                        }
                        processSubprocessSuccessResponse(false);
                        if(currentTaxonExternal.value['rankid'] < props.selectedRanksHigh){
                            getExternalChildren(callback);
                        }
                        else{
                            callback();
                        }
                    });
                }
                else{
                    processSubprocessSuccessResponse(false);
                    if(currentTaxonExternal.value['rankid'] < props.selectedRanksHigh){
                        getExternalChildren(callback);
                    }
                    else{
                        callback();
                    }
                }
            });
        }

        function getITISNameSearchResultsHierarchy(callback) {
            let id;
            if(taxonSearchResults.value[0]['accepted']){
                id = taxonSearchResults.value[0]['id'];
            }
            else{
                id = taxonSearchResults.value[0]['accepted_id'];
            }
            const url = 'https://www.itis.gov/ITISWebService/jsonservice/ITISService/getFullHierarchyFromTSN?tsn=' + id;
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        const resArr = resObj['hierarchyList'];
                        const hierarchyArr = [];
                        let foundNameRank = taxonSearchResults.value[0]['rankid'];
                        if(!taxonSearchResults.value[0]['accepted']){
                            const acceptedObj = resArr.find(rettaxon => rettaxon['taxonName'] === taxonSearchResults.value[0]['accepted_sciname']);
                            foundNameRank = Number(rankArr.value[acceptedObj['rankName'].toLowerCase()]);
                        }
                        resArr.forEach((taxResult) => {
                            if(taxResult['taxonName'] !== taxonSearchResults.value[0]['sciname']){
                                const rankname = taxResult['rankName'].toLowerCase();
                                const rankid = Number(rankArr.value[rankname]);
                                if(rankid <= foundNameRank && props.selectedRanks.includes(rankid)){
                                    const resultObj = {};
                                    resultObj['id'] = taxResult['tsn'];
                                    resultObj['sciname'] = taxResult['taxonName'];
                                    resultObj['author'] = taxResult['author'] ? taxResult['author'] : '';
                                    resultObj['rankname'] = rankname;
                                    resultObj['rankid'] = rankid;
                                    if(rankname === 'family'){
                                        taxonSearchResults.value[0]['family'] = resultObj['sciname'];
                                    }
                                    if(!taxonSearchResults.value[0]['accepted'] && resultObj['sciname'] === taxonSearchResults.value[0]['accepted_sciname']){
                                        taxonSearchResults.value[0]['accepted_author'] = resultObj['author'];
                                        taxonSearchResults.value[0]['accepted_rankid'] = resultObj['rankid'];
                                    }
                                    hierarchyArr.push(resultObj);
                                }
                            }
                        });
                        taxonSearchResults.value[0]['hierarchy'] = hierarchyArr.slice();
                        callback();
                    });
                }
                else{
                    callback('Unable to retrieve the parent taxon hierarchy');
                }
            });
        }

        function getITISNameSearchResultsRecord(callback) {
            const id = taxonSearchResults.value[0]['id'];
            const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        const taxonRankData = resObj['taxRank'];
                        taxonSearchResults.value[0]['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                        taxonSearchResults.value[0]['rankid'] = Number(taxonRankData['rankId']);
                        const scientificNameMetadata = resObj['scientificName'];
                        taxonSearchResults.value[0]['author'] = scientificNameMetadata['author'] ? scientificNameMetadata['author'] : '';
                        const coreMetadata = resObj['coreMetadata'];
                        const namestatus = coreMetadata['taxonUsageRating'];
                        taxonSearchResults.value[0]['accepted'] = (namestatus === 'accepted' || namestatus === 'valid');
                        if(importCommonNames.value && resObj.hasOwnProperty('commonNameList')){
                            taxonSearchResults.value[0]['commonnames'] = [];
                            const commonNames = resObj['commonNameList']['commonNames'];
                            commonNames.forEach((cName) => {
                                if(cName){
                                    const langObj = (cName.hasOwnProperty('language') && cName['language']) ? languageArr.value.find(lang => lang['name'] === cName['language']) : null;
                                    if(commonNameLanguageIdArr.value.length === 0 || (langObj && commonNameLanguageIdArr.value.includes(Number(langObj['langid'])))){
                                        const cNameObj = {};
                                        cNameObj['name'] = processCommonName(cName['commonName']);
                                        cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                        taxonSearchResults.value[0]['commonnames'].push(cNameObj);
                                    }
                                }
                            });
                        }
                        if(taxonSearchResults.value[0]['accepted'] && taxonSearchResults.value[0]['rankid'] < 140){
                            callback();
                        }
                        else{
                            const acceptedNameList = resObj.hasOwnProperty('acceptedNameList') ? resObj['acceptedNameList'] : null;
                            const acceptedNameArr = acceptedNameList ? acceptedNameList['acceptedNames'] : [];
                            if(acceptedNameArr.length > 0 || (taxonSearchResults.value[0]['rankid'] >= 140 && !currentFamily.value)){
                                if(!taxonSearchResults.value[0]['accepted'] && acceptedNameArr.length > 0){
                                    const acceptedName = acceptedNameArr[0];
                                    taxonSearchResults.value[0]['accepted_id'] = acceptedName['acceptedTsn'];
                                    taxonSearchResults.value[0]['accepted_sciname'] = acceptedName['acceptedName'];
                                }
                                getITISNameSearchResultsHierarchy(callback);
                            }
                            else{
                                callback('Unable to distinguish the parent taxon by name');
                            }
                        }
                    });
                }
                else{
                    callback('Unable to retrieve the parent taxon record');
                }
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

        function getNewSubprocessObject(type, text) {
            return {
                procText: text,
                type: type,
                loading: true,
                result: '',
                resultText: ''
            };
        }

        function getWoRMSAddTaxonAuthor(res, callback) {
            if(!processCancelling.value){
                const id = setAddTaxaArr.value[0]['id'];
                const url = 'https://www.marinespecies.org/rest/AphiaRecordByAphiaID/' + id;
                const formData = new FormData();
                formData.append('url', url);
                formData.append('action', 'get');
                fetch(proxyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            const currentTaxon = Object.assign({}, setAddTaxaArr.value[0]);
                            currentTaxon['author'] = resObj['authority'] ? resObj['authority'] : '';
                            if(setAddTaxaArr.value[0]['sciname'] === taxonSearchResults.value[0]['accepted_sciname']){
                                taxonSearchResults.value[0]['accepted_author'] = currentTaxon['author'];
                            }
                            if(!res){
                                taxaToAddArr.value.push(currentTaxon);
                                setAddTaxaArr.value.splice(0, 1);
                            }
                            setTaxaToAdd(callback);
                        });
                    }
                    else{
                        if(!res){
                            const currentTaxon = Object.assign({}, setAddTaxaArr.value[0]);
                            taxaToAddArr.value.push(currentTaxon);
                            setAddTaxaArr.value.splice(0, 1);
                        }
                        setTaxaToAdd(callback);
                    }
                });
            }
            else{
                adjustUIEnd();
            }
        }

        function getWoRMSExternalTaxonCommonNames(callback) {
            const url = 'https://www.marinespecies.org/rest/AphiaVernacularsByAphiaID/' + currentTaxonExternal.value['id'];
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((res) => {
                        if(res.length > 0){
                            res.forEach((cName) => {
                                const langIso2Code = cName.hasOwnProperty('language_code') ? cName['language_code'] : null;
                                const langObj = langIso2Code ? languageArr.value.find(lang => lang['iso-2'] === langIso2Code) : null;
                                if(commonNameLanguageIdArr.value.length === 0 || (langObj && commonNameLanguageIdArr.value.includes(Number(langObj['langid'])))){
                                    const cNameObj = {};
                                    cNameObj['name'] = processCommonName(cName['vernacular']);
                                    cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                    currentTaxonExternal.value['commonnames'].push(cNameObj);
                                }
                            });
                        }
                        processSubprocessSuccessResponse(false);
                        if(currentTaxonExternal.value['rankid'] < props.selectedRanksHigh){
                            getExternalChildren(callback);
                        }
                        else{
                            callback();
                        }
                    });
                }
                else{
                    processSubprocessSuccessResponse(false);
                    if(currentTaxonExternal.value['rankid'] < props.selectedRanksHigh){
                        getExternalChildren(callback);
                    }
                    else{
                        callback();
                    }
                }
            });
        }

        function getWoRMSNameSearchResultsHierarchy(callback) {
            let id;
            if(taxonSearchResults.value[0]['accepted']){
                id = taxonSearchResults.value[0]['id'];
            }
            else{
                id = taxonSearchResults.value[0]['accepted_id'];
            }
            const url = 'https://www.marinespecies.org/rest/AphiaClassificationByAphiaID/' + id;
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        const hierarchyArr = [];
                        const foundNameRank = taxonSearchResults.value[0]['rankid'];
                        let childObj = resObj['child'];
                        const firstObj = {};
                        const firstrankname = childObj['rank'].toLowerCase();
                        const firstrankid = Number(rankArr.value[firstrankname]);
                        const newTaxonAccepted = taxonSearchResults.value[0]['accepted'];
                        firstObj['id'] = childObj['AphiaID'];
                        firstObj['sciname'] = childObj['scientificname'];
                        firstObj['author'] = '';
                        firstObj['rankname'] = firstrankname;
                        firstObj['rankid'] = firstrankid;
                        if(firstObj['sciname'] === taxonSearchResults.value[0]['accepted_sciname']){
                            taxonSearchResults.value[0]['accepted_rankid'] = firstObj['rankid'];
                        }
                        hierarchyArr.push(firstObj);
                        let stopLoop = false;
                        while((childObj = childObj['child']) && !stopLoop){
                            if(childObj['scientificname'] !== taxonSearchResults.value[0]['sciname']){
                                const rankname = childObj['rank'].toLowerCase();
                                const rankid = Number(rankArr.value[rankname]);
                                if((newTaxonAccepted && rankid < foundNameRank && props.selectedRanks.includes(rankid)) || (!newTaxonAccepted && (childObj['scientificname'] === taxonSearchResults.value[0]['accepted_sciname'] || props.selectedRanks.includes(rankid)))){
                                    const resultObj = {};
                                    resultObj['id'] = childObj['AphiaID'];
                                    resultObj['sciname'] = childObj['scientificname'];
                                    resultObj['author'] = '';
                                    resultObj['rankname'] = rankname;
                                    resultObj['rankid'] = rankid;
                                    if(resultObj['sciname'] === taxonSearchResults.value[0]['accepted_sciname']){
                                        taxonSearchResults.value[0]['accepted_rankid'] = resultObj['rankid'];
                                    }
                                    if(rankname === 'family'){
                                        taxonSearchResults.value[0]['family'] = resultObj['sciname'];
                                    }
                                    hierarchyArr.push(resultObj);
                                }
                                if((newTaxonAccepted && rankid === foundNameRank) || (!newTaxonAccepted && childObj['scientificname'] === taxonSearchResults.value[0]['accepted_sciname'])){
                                    stopLoop = true;
                                }
                            }
                        }
                        taxonSearchResults.value[0]['hierarchy'] = hierarchyArr.slice();
                        callback();
                    });
                }
                else{
                    callback('Unable to retrieve the parent taxon hierarchy');
                }
            });
        }

        function getWoRMSNameSearchResultsRecord(id, callback) {
            const url = 'https://www.marinespecies.org/rest/AphiaRecordByAphiaID/' + id;
            const formData = new FormData();
            formData.append('url', url);
            formData.append('action', 'get');
            fetch(proxyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        if(resObj['kingdom'].toLowerCase() === kingdomName.value.toLowerCase() || resObj['scientificname'].toLowerCase() === kingdomName.value.toLowerCase()){
                            const resultObj = {};
                            resultObj['id'] = resObj['AphiaID'];
                            resultObj['sciname'] = resObj['scientificname'];
                            resultObj['author'] = resObj['authority'] ? resObj['authority'] : '';
                            resultObj['rankname'] = resObj['rank'].toLowerCase();
                            resultObj['rankid'] = Number(resObj['taxonRankID']);
                            const namestatus = resObj['status'];
                            if(namestatus === 'accepted'){
                                resultObj['accepted'] = true;
                            }
                            else{
                                resultObj['accepted'] = false;
                                resultObj['accepted_id'] = resObj['valid_AphiaID'];
                                resultObj['accepted_sciname'] = resObj['valid_name'];
                            }
                            taxonSearchResults.value.push(resultObj);
                            if(resultObj['accepted'] && resultObj['rankid'] < 140){
                                callback();
                            }
                            else{
                                getWoRMSNameSearchResultsHierarchy(callback);
                            }
                        }
                        else{
                            callback('Not found');
                        }
                    });
                }
                else{
                    callback('Unable to retrieve the parent taxon record');
                }
            });
        }

        function initializeCurrentTaxa(data) {
            currentTaxonExternal.value['id'] = data['id'];
            currentTaxonExternal.value['sciname'] = data['sciname'];
            currentTaxonExternal.value['author'] = (data.hasOwnProperty('author') && data['author']) ? data['author'] : '';
            currentTaxonExternal.value['rankid'] = Number(data['rankid']);
            currentTaxonExternal.value['family'] = data['family'];
            if((!currentTaxonExternal.value['family'] || currentTaxonExternal.value['family'] === '') && currentTaxonExternal.value['rankid'] === 140){
                currentTaxonExternal.value['family'] = currentTaxonExternal.value['sciname'];
            }
            if(importCommonNames.value){
                currentTaxonExternal.value['commonnames'] = [];
            }
            currentTaxonExternal.value['children'] = [];
            currentTaxonExternal.value['tid'] = data['tid'];
            if((!currentTaxonExternal.value['tid'] || !Number(currentTaxonExternal.value['tid'])) && nameTidIndex.value.hasOwnProperty(currentTaxonExternal.value['sciname'])){
                currentTaxonExternal.value['tid'] = nameTidIndex.value[currentTaxonExternal.value['sciname']];
            }
            currentTaxonExternal.value['parenttid'] = (data.hasOwnProperty('parenttid') && data['parenttid']) ? data['parenttid'] : null;
            currentTaxonExternal.value['tidaccepted'] = (data.hasOwnProperty('tidaccepted') && data['tidaccepted']) ? data['tidaccepted'] : null;
            const text = 'Processing ' + currentTaxonExternal.value['sciname'];
            currentProcess.value = currentTaxonExternal.value['sciname'];
            addProcessToProcessorDisplay(getNewProcessObject('multi',text));
            processSuccessResponse();
            const callbackFunction = (resObj,errorText = null) => {
                if(errorText){
                    updateTaxonomicHierarchy(() => {
                        adjustUIEnd();
                    });
                }
                else{
                    if(resObj){
                        currentTaxonLocal.value = Object.assign({}, resObj);
                        currentTaxonExternal.value['tid'] = resObj['tid'];
                        currentTaxonExternal.value['tidaccepted'] = resObj['tidaccepted'];
                    }
                    getExternalCommonNames(() => {
                        currentTaxonValidate();
                    });
                }
            };
            if(currentTaxonExternal.value['tid']){
                findTaxonByTid(currentTaxonExternal.value['tid'],callbackFunction);
            }
            else{
                findTaxonBySciname(currentTaxonExternal.value['sciname'],callbackFunction);
            }
        }

        function initializeImportUpdate() {
            if(props.taxonomicGroupTid && props.selectedRanks.length > 0){
                adjustUIStart();
                const text = 'Setting rank data';
                currentProcess.value = 'setRankArr';
                addProcessToProcessorDisplay(getNewProcessObject('single',text));
                const url = taxonomyApiUrl + '?action=getRankNameArr'
                abortController = new AbortController();
                fetch(url, {
                    signal: abortController.signal
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            processSuccessResponse('Complete');
                            rankArr.value = resObj;
                            if(importCommonNames.value){
                                setLanguageArr();
                            }
                            else{
                                setTargetTaxonLocal();
                            }
                        });
                    }
                    else{
                        const text = getErrorResponseText(response.status,response.statusText);
                        processErrorResponse(text);
                    }
                });
            }
            else if(props.taxonomicGroupTid){
                showNotification('negative', 'Please select the Taxonomic Ranks to be included in the import/update.');
            }
            else{
                showNotification('negative', 'Please enter a Taxonomic Group to start an import/update.');
            }
        }

        function populateTaxonomicHierarchy(callback) {
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
            else{
                processErrorResponse('Error updating the taxonomic hierarchy');
                callback('Error updating the taxonomic hierarchy');
            }
        }

        function primeTaxonomicHierarchy(callback) {
            rebuildHierarchyLoop.value = 0;
            const formData = new FormData();
            formData.append('tidarr', JSON.stringify(newEditedTidArr.value));
            formData.append('action', 'primeHierarchyTable');
            fetch(taxonomyApiUrl, {
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

        function processAddTaxaArr(callback) {
            if(taxaToAddArr.value.length > 0){
                const taxonToAdd = Object.assign({}, taxaToAddArr.value[0]);
                const rankId = Number(taxonToAdd['rankid']);
                taxonToAdd['parenttid'] = rankId > 10 ? nameTidIndex.value[taxonToAdd['parentName']] : 1;
                addTaxonToThesaurus(taxonToAdd,(newTaxon,errorText = null) => {
                    if(errorText){
                        callback(errorText);
                    }
                    else{
                        const newTid = Number(newTaxon['tid']);
                        nameTidIndex.value[taxaToAddArr.value[0]['sciname']] = newTid;
                        newEditedTidArr.value.push(newTid);
                        taxaToAddArr.value.splice(0, 1);
                        processAddTaxaArr(callback);
                    }
                });
            }
            else{
                callback();
            }
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

        function processGetCOLTaxonByIdResponse(resObj, callback) {
            const taxResult = resObj['taxon'];
            const nameData = taxResult['name'];
            const resultObj = {};
            resultObj['id'] = taxResult['id'];
            resultObj['author'] = nameData.hasOwnProperty('authorship') ? nameData['authorship'] : '';
            resultObj['rankname'] = nameData['rank'].toLowerCase();
            resultObj['sciname'] = nameData['scientificName'];
            resultObj['rankid'] = rankArr.value.hasOwnProperty(resultObj['rankname']) ? rankArr.value[resultObj['rankname']] : null;
            resultObj['accepted'] = (taxResult['status'] === 'accepted');
            colInitialSearchResults.value.push(resultObj);
            validateCOLNameSearchResults(callback);
        }

        function processGetCOLTaxonByScinameResponse(resObj, callback) {
            if(resObj['total_number_of_results'] > 0){
                const resultArr = resObj['result'];
                resultArr.forEach((taxResult) => {
                    const status = taxResult['name_status'];
                    if(status !== 'common name'){
                        const resultObj = {};
                        resultObj['id'] = taxResult['id'];
                        resultObj['author'] = taxResult.hasOwnProperty('author') ? taxResult['author'] : '';
                        let rankName = taxResult['rank'].toLowerCase();
                        if(rankName === 'infraspecies'){
                            resultObj['sciname'] = taxResult['genus'] + ' ' + taxResult['species'] + ' ' + taxResult['infraspeciesMarker'] + ' ' + taxResult['infraspecies'];
                            if(taxResult['infraspeciesMarker'] === 'var.'){
                                rankName = 'variety';
                            }
                            else if(taxResult['infraspeciesMarker'] === 'subsp.'){
                                rankName = 'subspecies';
                            }
                            else if(taxResult['infraspeciesMarker'] === 'f.'){
                                rankName = 'form';
                            }
                        }
                        else{
                            resultObj['sciname'] = taxResult['name'];
                        }
                        resultObj['rankname'] = rankName;
                        resultObj['rankid'] = rankArr.value.hasOwnProperty(resultObj['rankname']) ? rankArr.value[resultObj['rankname']] : null;
                        if(status === 'accepted name'){
                            resultObj['accepted'] = true;
                        }
                        else if(status === 'synonym'){
                            const hierarchyArr = [];
                            const resultHObj = {};
                            const acceptedObj = taxResult['accepted_name'];
                            const acceptedAuthor = acceptedObj.hasOwnProperty('author') ? acceptedObj['author'] : '';
                            resultObj['accepted'] = false;
                            resultObj['accepted_id'] = acceptedObj['id'];
                            resultObj['accepted_author'] = acceptedAuthor;
                            resultHObj['id'] = acceptedObj['id'];
                            resultHObj['author'] = acceptedAuthor;
                            let rankName = acceptedObj['rank'].toLowerCase();
                            if(rankName === 'infraspecies'){
                                resultHObj['sciname'] = acceptedObj['genus'] + ' ' + acceptedObj['species'] + ' ' + acceptedObj['infraspeciesMarker'] + ' ' + acceptedObj['infraspecies'];
                                if(acceptedObj['infraspeciesMarker'] === 'var.'){
                                    rankName = 'variety';
                                }
                                else if(acceptedObj['infraspeciesMarker'] === 'subsp.'){
                                    rankName = 'subspecies';
                                }
                                else if(acceptedObj['infraspeciesMarker'] === 'f.'){
                                    rankName = 'form';
                                }
                            }
                            else{
                                resultHObj['sciname'] = acceptedObj['name'];
                            }
                            resultObj['accepted_sciname'] = resultHObj['sciname'];
                            resultHObj['rankname'] = rankName;
                            resultHObj['rankid'] = rankArr.value.hasOwnProperty(resultHObj['rankname']) ? rankArr.value[resultHObj['rankname']] : null;
                            resultObj['accepted_rankid'] = resultHObj['rankid'];
                            hierarchyArr.push(resultHObj);
                            resultObj['hierarchy'] = hierarchyArr;
                        }
                        const existingObj = colInitialSearchResults.value.find(taxon => (taxon['sciname'] === resultObj['sciname'] && taxon['accepted_sciname'] === resultObj['accepted_sciname']));
                        if(!existingObj){
                            colInitialSearchResults.value.push(resultObj);
                        }
                    }
                });
                if(colInitialSearchResults.value.length > 0){
                    validateCOLNameSearchResults(callback);
                }
                else{
                    callback('Not found');
                }
            }
            else{
                callback('Not found');
            }
        }

        function processGetITISTaxonByScinameResponse(resObj, callback) {
            itisInitialSearchResults.value = [];
            const resultArr = resObj['scientificNames'];
            if(resultArr && resultArr.length > 0 && resultArr[0]){
                resultArr.forEach((taxResult) => {
                    if(taxResult['combinedName'] === props.taxonomicGroup.name && (taxResult['kingdom'].toLowerCase() === kingdomName.value.toLowerCase() || taxResult['combinedName'].toLowerCase() === kingdomName.value.toLowerCase())){
                        const resultObj = {};
                        resultObj['id'] = taxResult['tsn'];
                        resultObj['sciname'] = taxResult['combinedName'];
                        itisInitialSearchResults.value.push(resultObj);
                    }
                });
                if(itisInitialSearchResults.value.length === 1){
                    taxonSearchResults.value = itisInitialSearchResults.value;
                    getITISNameSearchResultsRecord(callback);
                }
                else if(itisInitialSearchResults.value.length === 0){
                    callback('Not found');
                }
                else if(itisInitialSearchResults.value.length > 1){
                    validateITISNameSearchResults(callback);
                }
            }
            else{
                callback('Not found');
            }
        }

        function processLocalChildSearch() {
            currentLocalChild.value['tidaccepted'] = nameTidIndex.value[taxonSearchResults.value[0]['accepted_sciname']];
            updateTaxonTidAccepted(Object.assign({}, currentLocalChild.value),() => {
                currentTaxonProcessLocalChildren();
            });
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

        function processProcessingArrays() {
            if(processCancelling.value){
                updateTaxonomicHierarchy(() => {
                    adjustUIEnd();
                });
            }
            else if(processingArr.value.length > 0){
                initializeCurrentTaxa(Object.assign({}, processingArr.value[0]));
                processingArr.value.splice(0, 1);
            }
            else if(queueArr.value.length > 0){
                processingArr.value = queueArr.value.slice();
                queueArr.value = [];
                updateTaxonomicHierarchy((errorText = null) => {
                    if(errorText){
                        adjustUIEnd();
                    }
                    else{
                        newEditedTidArr.value = [];
                        initializeCurrentTaxa(processingArr.value[0]);
                        processingArr.value.splice(0, 1);
                    }
                });
            }
            else if(familyArr.value.length > 0){
                if(familyArr.value[0]['processingArr'].length > 0 || familyArr.value[0]['queueArr'].length > 0){
                    currentFamily.value = familyArr.value[0]['name'];
                    if(familyArr.value[0]['processingArr'].length > 0){
                        initializeCurrentTaxa(Object.assign({}, familyArr.value[0]['processingArr'][0]));
                        familyArr.value[0]['processingArr'].splice(0, 1);
                    }
                    else if(familyArr.value[0]['queueArr'].length > 0){
                        familyArr.value[0]['processingArr'] = familyArr.value[0]['queueArr'].slice();
                        familyArr.value[0]['queueArr'] = [];
                        updateTaxonomicHierarchy((errorText = null) => {
                            if(errorText){
                                adjustUIEnd();
                            }
                            else{
                                newEditedTidArr.value = [];
                                initializeCurrentTaxa(Object.assign({}, familyArr.value[0]['processingArr'][0]));
                                familyArr.value[0]['processingArr'].splice(0, 1);
                            }
                        });
                    }
                }
                else{
                    familyArr.value.splice(0, 1);
                    processProcessingArrays();
                }
            }
            else{
                updateTaxonomicHierarchy(() => {
                    adjustUIEnd();
                });
            }
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

        function setInitialTaxa() {
            currentTaxonExternal.value['id'] = taxonSearchResults.value[0]['accepted'] ? taxonSearchResults.value[0]['id'] : taxonSearchResults.value[0]['accepted_id'];
            currentTaxonExternal.value['sciname'] = taxonSearchResults.value[0]['accepted'] ? taxonSearchResults.value[0]['sciname'] : taxonSearchResults.value[0]['accepted_sciname'];
            currentTaxonExternal.value['author'] = taxonSearchResults.value[0]['accepted'] ? taxonSearchResults.value[0]['author'] : taxonSearchResults.value[0]['accepted_author'];
            currentTaxonExternal.value['rankid'] = taxonSearchResults.value[0]['accepted'] ? taxonSearchResults.value[0]['rankid'] : taxonSearchResults.value[0]['accepted_rankid'];
            currentTaxonExternal.value['family'] = taxonSearchResults.value[0].hasOwnProperty('family') ? taxonSearchResults.value[0]['family'] : '';
            if(currentTaxonExternal.value['family'] === '' && currentTaxonExternal.value['rankid'] === 140){
                currentTaxonExternal.value['family'] = currentTaxonExternal.value['sciname'];
            }
            if(currentTaxonExternal.value['family'] !== ''){
                currentFamily.value = currentTaxonExternal.value['family'];
                addFamilyToFamilyArr(currentTaxonExternal.value['family']);
            }
            if(importCommonNames.value){
                currentTaxonExternal.value['commonnames'] = taxonSearchResults.value[0].hasOwnProperty('commonnames') ? taxonSearchResults.value[0]['commonnames'] : [];
            }
            currentTaxonExternal.value['children'] = [];
            currentTaxonExternal.value['tid'] = null;
            currentTaxonExternal.value['parenttid'] = null;
            currentTaxonExternal.value['tidaccepted'] = null;
            const text = 'Processing ' + currentTaxonExternal.value['sciname'];
            currentProcess.value = currentTaxonExternal.value['sciname'];
            addProcessToProcessorDisplay(getNewProcessObject('multi',text));
            processSuccessResponse();
            if(targetTaxonLocal.value['sciname'] === currentTaxonExternal.value['sciname']){
                currentTaxonExternal.value['tid'] = targetTaxonLocal.value['tid'];
                currentTaxonExternal.value['parenttid'] = targetTaxonLocal.value['parenttid'];
                currentTaxonExternal.value['tidaccepted'] = targetTaxonLocal.value['tidaccepted'];
                currentTaxonLocal.value['tid'] = targetTaxonLocal.value['tid'];
                currentTaxonLocal.value['sciname'] = targetTaxonLocal.value['sciname'];
                currentTaxonLocal.value['author'] = targetTaxonLocal.value['author'];
                currentTaxonLocal.value['rankid'] = targetTaxonLocal.value['rankid'];
                currentTaxonLocal.value['family'] = targetTaxonLocal.value['family'];
                currentTaxonLocal.value['tidaccepted'] = targetTaxonLocal.value['tidaccepted'];
                currentTaxonLocal.value['parenttid'] = targetTaxonLocal.value['parenttid'];
                currentTaxonLocal.value['identifiers'] = targetTaxonLocal.value['identifiers'].slice();
                if(importCommonNames.value){
                    currentTaxonLocal.value['commonnames'] = targetTaxonLocal.value['commonnames'].slice();
                }
                currentTaxonLocal.value['children'] = targetTaxonLocal.value['children'].slice();
                getExternalCommonNames((errorText = null) => {
                    if(errorText){
                        adjustUIEnd();
                    }
                    else{
                        currentTaxonValidate();
                    }
                });
            }
            else{
                findTaxonByTid(targetTaxonLocal.value['tidaccepted'],(resObj,errorText = null) => {
                    if(errorText){
                        adjustUIEnd();
                    }
                    else{
                        if(resObj){
                            currentTaxonLocal.value = Object.assign({}, resObj);
                            props.kingdomId = currentTaxonLocal.value['kingdomid'];
                            kingdomName.value = currentTaxonLocal.value['kingdom'];
                            currentTaxonExternal.value['tid'] = resObj['tid'];
                            currentTaxonExternal.value['parenttid'] = resObj['parenttid'];
                            currentTaxonExternal.value['tidaccepted'] = resObj['tidaccepted'];
                        }
                        getExternalCommonNames((errorText = null) => {
                            if(errorText){
                                adjustUIEnd();
                            }
                            else{
                                currentTaxonValidate();
                            }
                        });
                    }
                });
            }
        }

        function setLanguageArr() {
            const text = 'Setting language data';
            currentProcess.value = 'setLanguageArr';
            addProcessToProcessorDisplay(getNewProcessObject('single',text));
            const url = languageApiUrl + '?action=getLanguages'
            abortController = new AbortController();
            fetch(url, {
                signal: abortController.signal
            })
            .then((response) => {
                if(response.status === 200){
                    response.json().then((resObj) => {
                        processSuccessResponse('Complete');
                        languageArr.value = resObj;
                        setTargetTaxonLocal();
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    processErrorResponse(text);
                }
            });
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

        function setTargetSynonymy() {
            const text = 'Updating target taxonomic group accepted parent taxon';
            currentProcess.value = 'updateTargetAcceptedParent';
            addProcessToProcessorDisplay(getNewProcessObject('single',text));
            if(targetTaxonLocal.value['sciname'] !== taxonSearchResults.value[0]['accepted_sciname']){
                targetTaxonLocal.value['tidaccepted'] = nameTidIndex.value[taxonSearchResults.value[0]['accepted_sciname']];
            }
            updateTaxonTidAccepted(Object.assign({}, targetTaxonLocal.value),(errorText = null) => {
                if(errorText && errorText !== ''){
                    processErrorResponse(errorText);
                    adjustUIEnd();
                }
                else{
                    processSuccessResponse('Complete');
                    setInitialTaxa();
                }
            });
        }

        function setTargetTaxonLocal() {
            const text = 'Setting the parent taxon for the taxonomic group from the Taxonomic Thesaurus';
            currentProcess.value = 'setTargetTaxonLocal';
            addProcessToProcessorDisplay(getNewProcessObject('single',text));
            findTaxonByTid(props.taxonomicGroupTid,(resObj,errorText = null) => {
                if(errorText){
                    processErrorResponse(errorText);
                    adjustUIEnd();
                }
                else{
                    targetTaxonLocal.value = Object.assign({}, resObj);
                    kingdomName.value = targetTaxonLocal.value['kingdom'];
                    processSuccessResponse('Complete');
                    const text = 'Finding the parent taxon for the taxonomic group from the selected Data Source';
                    currentProcess.value = 'setTargetTaxonExternal';
                    addProcessToProcessorDisplay(getNewProcessObject('single',text));
                    const dataSourceIdObj = targetTaxonLocal.value['identifiers'].find(obj => obj['name'] === dataSource.value);
                    if(dataSourceIdObj){
                        targetTaxonIdentifier.value = dataSourceIdObj['identifier'];
                        if(dataSource.value === 'col'){
                            findCOLTaxonById(targetTaxonIdentifier.value,(res,errorText = null) => {
                                if(errorText){
                                    processErrorResponse(errorText);
                                    adjustUIEnd();
                                }
                                else{
                                    if(res.hasOwnProperty('taxon')){
                                        processGetCOLTaxonByIdResponse(res,(errorText = null) => {
                                            if(errorText){
                                                processErrorResponse(errorText);
                                                adjustUIEnd();
                                            }
                                            else{
                                                validateExternalTaxonSearchResults(true);
                                            }
                                        });
                                    }
                                    else{
                                        findExternalTaxonBySciname(props.taxonomicGroup.name,(errorText = null) => {
                                            if(errorText){
                                                processErrorResponse(errorText);
                                                adjustUIEnd();
                                            }
                                            else{
                                                validateExternalTaxonSearchResults(true);
                                            }
                                        });
                                    }
                                }
                            });
                        }
                        else if(dataSource.value === 'itis'){
                            const resultObj = {};
                            resultObj['id'] = targetTaxonIdentifier.value;
                            resultObj['sciname'] = props.taxonomicGroup.name;
                            taxonSearchResults.value.push(resultObj);
                            getITISNameSearchResultsRecord((errorText = null) => {
                                if(errorText){
                                    processErrorResponse(errorText);
                                    adjustUIEnd();
                                }
                                else{
                                    validateExternalTaxonSearchResults(true);
                                }
                            });
                        }
                        else if(dataSource.value === 'worms'){
                            getWoRMSNameSearchResultsRecord(targetTaxonIdentifier.value,(errorText = null) => {
                                if(errorText){
                                    processErrorResponse(errorText);
                                    adjustUIEnd();
                                }
                                else{
                                    validateExternalTaxonSearchResults(true);
                                }
                            });
                        }
                    }
                    else{
                        findExternalTaxonBySciname(props.taxonomicGroup.name,(errorText = null) => {
                            if(errorText){
                                processErrorResponse(errorText);
                                adjustUIEnd();
                            }
                            else{
                                validateExternalTaxonSearchResults(true);
                            }
                        });
                    }
                }
            });
        }

        function setTaxaToAdd(callback) {
            if(setAddTaxaArr.value.length > 0){
                const sciname = setAddTaxaArr.value[0]['sciname'];
                const url = clientRoot + '/api/taxa/gettid.php';
                const formData = new FormData();
                formData.append('sciname', sciname);
                formData.append('kingdomid', props.kingdomId);
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.text().then((res) => {
                            if(dataSource.value === 'worms' && !res){
                                getWoRMSAddTaxonAuthor(res,callback);
                            }
                            else{
                                const currentTaxon = Object.assign({}, setAddTaxaArr.value[0]);
                                if(res){
                                    nameTidIndex.value[currentTaxon['sciname']] = Number(res);
                                }
                                else{
                                    taxaToAddArr.value.push(currentTaxon);
                                }
                                setAddTaxaArr.value.splice(0, 1);
                                setTaxaToAdd(callback);
                            }
                        });
                    }
                });
            }
            else{
                processAddTaxaArr(callback);
            }
        }

        function updateCommonNameLanguageArr(langObj) {
            commonNameLanguageIdArr.value = [];
            commonNameLanguageArr.value = langObj;
            commonNameLanguageArr.value.forEach((lang) => {
                commonNameLanguageIdArr.value.push(Number(lang['id']));
            });

        }

        function updateSelectedDataSource(dataSourceObj) {
            dataSource.value = dataSourceObj;
        }

        function updateTaxonomicHierarchy(callback) {
            if(newEditedTidArr.value.length > 0){
                const text = 'Updating taxonomic hierarchy table with new and edited taxa';
                currentProcess.value = 'updateTaxonomicHierarchy';
                addProcessToProcessorDisplay(getNewProcessObject('single',text));
                rebuildHierarchyLoop.value = 0;
                const formData = new FormData();
                formData.append('tidarr', JSON.stringify(newEditedTidArr.value));
                formData.append('action', 'clearHierarchyTable');
                fetch(taxonomyApiUrl, {
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

        function updateTaxonParent(parenttid, tid, callback) {
            const formData = new FormData();
            formData.append('action', 'editTaxonParent');
            formData.append('tid', tid);
            formData.append('parenttid', parenttid);
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        if(res && res !== ''){
                            callback(res);
                        }
                        else{
                            newEditedTidArr.value.push(tid);
                            callback();
                        }
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    callback(text);
                }
            });
        }

        function updateTaxonTidAccepted(taxon, callback) {
            const kingdom = Number(taxon['rankid']) === 10;
            const formData = new FormData();
            formData.append('action', 'updateTaxonTidAccepted');
            formData.append('tid', taxon['tid']);
            formData.append('tidaccepted', taxon['tidaccepted']);
            formData.append('kingdom', (kingdom ? '1' : '0'));
            fetch(taxonomyApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                if(response.status === 200){
                    response.text().then((res) => {
                        if(res && res !== ''){
                            callback(res);
                        }
                        else{
                            callback();
                        }
                    });
                }
                else{
                    const text = getErrorResponseText(response.status,response.statusText);
                    callback(text);
                }
            });
        }

        function validateCOLNameSearchResults(callback) {
            if(colInitialSearchResults.value.length > 0){
                let id;
                const taxon = colInitialSearchResults.value[0];
                colInitialSearchResults.value.splice(0, 1);
                if(taxon['accepted']){
                    id = taxon['id'];
                }
                else{
                    id = taxon['accepted_id'];
                }
                const url = 'https://api.catalogueoflife.org/dataset/9840/taxon/' + id + '/classification';
                const formData = new FormData();
                formData.append('url', url);
                formData.append('action', 'get');
                fetch(proxyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resArr) => {
                            let kingdomName = '';
                            if(taxon['rankname'].toLowerCase() === 'kingdom'){
                                kingdomName = taxon['sciname'];
                            }
                            else{
                                const kingdomObj = resArr.find(rettaxon => rettaxon['rank'].toLowerCase() === 'kingdom');
                                if(kingdomObj){
                                    kingdomName = kingdomObj['name'];
                                }
                            }
                            if(kingdomName.toLowerCase() === kingdomName.value.toLowerCase()){
                                let hierarchyArr = [];
                                if(taxon.hasOwnProperty('hierarchy')){
                                    hierarchyArr = taxon['hierarchy'];
                                }
                                resArr.forEach((taxResult) => {
                                    if(taxResult['name'] !== taxon['sciname']){
                                        const rankname = taxResult['rank'].toLowerCase();
                                        const rankid = Number(rankArr.value[rankname]);
                                        if(props.selectedRanks.includes(rankid) || (!taxon['accepted'] && taxon['accepted_sciname'] === taxResult['name'])){
                                            const resultObj = {};
                                            resultObj['id'] = taxResult['id'];
                                            resultObj['sciname'] = taxResult['name'];
                                            resultObj['author'] = taxResult.hasOwnProperty('authorship') ? taxResult['authorship'] : '';
                                            resultObj['rankname'] = rankname;
                                            resultObj['rankid'] = rankid;
                                            if(rankname === 'family'){
                                                taxon['family'] = resultObj['sciname'];
                                            }
                                            hierarchyArr.push(resultObj);
                                        }
                                    }
                                });
                                taxon['hierarchy'] = hierarchyArr;
                                taxonSearchResults.value.push(taxon);
                            }
                            validateCOLNameSearchResults(callback);
                        });
                    }
                    else{
                        validateCOLNameSearchResults(callback);
                    }
                });
            }
            else if(taxonSearchResults.value.length === 1){
                callback();
            }
            else if(taxonSearchResults.value.length === 0){
                callback('Not found');
            }
            else if(taxonSearchResults.value.length > 1){
                callback('Unable to distinguish the parent taxon by name');
            }
        }

        function validateExternalTaxonSearchResults(target = false) {
            setAddTaxaArr.value = [];
            taxaToAddArr.value = [];
            if(taxonSearchResults.value.length === 1){
                if(!taxonSearchResults.value[0]['accepted'] && !taxonSearchResults.value[0]['accepted_sciname']){
                    if(target){
                        processErrorResponse('Unable to distinguish the parent taxon accepted name');
                        adjustUIEnd();
                    }
                    else{
                        currentTaxonProcessLocalChildren();
                    }
                }
                else{
                    processSuccessResponse();
                    if(!targetTaxonIdentifier.value){
                        addTaxonIdentifier(props.taxonomicGroupTid,taxonSearchResults.value[0]['id']);
                        targetTaxonLocal.value['identifiers'].push({
                            name: dataSource.value,
                            identifier: taxonSearchResults.value[0]['id']
                        });
                    }
                    if(!taxonSearchResults.value[0]['accepted']){
                        let callbackFunction;
                        if(target){
                            callbackFunction = (errorText = null) => {
                                if(errorText){
                                    processErrorResponse(errorText);
                                    adjustUIEnd();
                                }
                                else{
                                    processSuccessResponse('Complete');
                                    setTargetSynonymy();
                                }
                            };
                        }
                        else{
                            callbackFunction = (errorText = null) => {
                                if(errorText){
                                    currentTaxonProcessLocalChildren();
                                }
                                else{
                                    processLocalChildSearch();
                                }
                            };
                        }
                        const addHierchyTemp = taxonSearchResults.value[0]['hierarchy'].slice();
                        addHierchyTemp.sort((a, b) => {
                            return a.rankid - b.rankid;
                        });
                        let parentName = addHierchyTemp[0]['sciname'];
                        addHierchyTemp.forEach((taxon) => {
                            if(taxon['sciname'] !== parentName){
                                taxon['parentName'] = parentName;
                                taxon['family'] = taxon['rankid'] >= 140 ? taxonSearchResults.value[0]['family'] : null;
                                parentName = taxon['sciname'];
                                if(!taxonSearchResults.value[0]['accepted'] && taxon['sciname'] === taxonSearchResults.value[0]['accepted_sciname']){
                                    taxonSearchResults.value[0]['parentName'] = taxon['parentName'];
                                }
                            }
                        });
                        if(!taxonSearchResults.value[0].hasOwnProperty('parentName') || taxonSearchResults.value[0]['parentName'] === ''){
                            taxonSearchResults.value[0]['parentName'] = parentName;
                        }
                        setAddTaxaArr.value = addHierchyTemp.slice();
                        setTaxaToAdd(callbackFunction);
                    }
                    else{
                        if(target){
                            setTargetSynonymy();
                        }
                        else{
                            processLocalChildSearch();
                        }
                    }
                }
            }
            else{
                if(target){
                    processErrorResponse('Unable to distinguish the parent taxon accepted name');
                    adjustUIEnd();
                }
                else{
                    currentTaxonProcessLocalChildren();
                }
            }
        }

        function validateITISNameSearchResults(callback) {
            if(itisInitialSearchResults.value.length > 0){
                const taxon = itisInitialSearchResults.value[0];
                itisInitialSearchResults.value.splice(0, 1);
                const id = taxon['id'];
                const url = 'https://www.itis.gov/ITISWebService/jsonservice/getFullRecordFromTSN?tsn=' + id;
                const formData = new FormData();
                formData.append('url', url);
                formData.append('action', 'get');
                fetch(proxyApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    if(response.status === 200){
                        response.json().then((resObj) => {
                            const coreMetadata = resObj['coreMetadata'];
                            const namestatus = coreMetadata['taxonUsageRating'];
                            taxon['author'] = '';
                            if(resObj.hasOwnProperty('taxonAuthor')){
                                const authorMetadata = resObj['taxonAuthor'];
                                taxon['author'] = authorMetadata.hasOwnProperty('authorship') ? authorMetadata['authorship'] : '';
                            }
                            if(importCommonNames.value && resObj.hasOwnProperty('commonNameList')){
                                taxon['commonnames'] = [];
                                const commonNames = resObj['commonNameList']['commonNames'];
                                commonNames.forEach((cName) => {
                                    const langObj = languageArr.value.find(lang => lang['name'] === cName['language']);
                                    if(commonNameLanguageIdArr.value.length === 0 || (langObj && commonNameLanguageIdArr.value.includes(Number(langObj['langid'])))){
                                        const cNameObj = {};
                                        cNameObj['name'] = processCommonName(cName['commonName']);
                                        cNameObj['langid'] = langObj ? Number(langObj['langid']) : '';
                                        taxon['commonnames'].push(cNameObj);
                                    }
                                });
                            }
                            if(namestatus === 'accepted'){
                                const taxonRankData = resObj['taxRank'];
                                taxon['rankname'] = taxonRankData['rankName'].toLowerCase().trim();
                                taxon['rankid'] = Number(taxonRankData['rankId']);
                                taxon['accepted'] = true;
                                taxonSearchResults.value.push(taxon);
                            }
                            validateITISNameSearchResults(callback);
                        });
                    }
                    else{
                        callback('Unable to retrieve the parent taxon record');
                    }
                });
            }
            else if(taxonSearchResults.value.length === 1){
                if(!taxonSearchResults.value[0]['accepted'] || (itisInitialSearchResults.value[0]['rankid'] >= 140 && !currentFamily.value)){
                    getITISNameSearchResultsHierarchy(callback);
                }
                else{
                    callback();
                }
            }
            else if(taxonSearchResults.value.length === 0){
                callback('Not found');
            }
            else if(taxonSearchResults.value.length > 1){
                callback('Unable to distinguish the parent taxon by name');
            }
        }

        return {
            commonNameFormattingOptions,
            commonNameLanguageArr,
            currentProcess,
            dataSource,
            importCommonNames,
            importTaxa,
            procDisplayScrollAreaRef,
            processCancelling,
            processorDisplayArr,
            processorDisplayCurrentIndex,
            processorDisplayIndex,
            selectedCommonNameFormatting,
            updateAcceptance,
            updateMetadata,
            updateParent,
            cancelProcess,
            initializeImportUpdate,
            processorDisplayScrollDown,
            processorDisplayScrollUp,
            setScroller,
            updateCommonNameLanguageArr,
            updateSelectedDataSource
        }
    }
};
