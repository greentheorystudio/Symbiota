const occurrenceLinkageToolPopup = {
    props: {
        avoidArr: {
            type: Array,
            default: []
        },
        editorLimit: {
            type: Boolean,
            default: true
        },
        multiSelect: {
            type: Boolean,
            default: false
        },
        searchTerms: {
            type: Object,
            default: null
        },
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
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                            <q-tab name="criteria" label="Criteria" no-caps></q-tab>
                            <q-tab name="records" label="Records" :disable="recordData.length === 0" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel class="q-pa-md" name="criteria" :style="tabStyle">
                                <div class="q-px-sm q-pt-sm">
                                    <div class="text-h6 text-bold">Occurrence Linkage Tool</div>
                                    <div class="text-body1">
                                        <template v-if="editorLimit">
                                            Enter criteria to search for {{ occurrenceTerm }} you would like to link, 
                                            or select a collection in which to create a new occurrence record to link.
                                        </template>
                                        <template v-else>
                                            Enter criteria to search for {{ occurrenceTerm }} you would like to link.
                                        </template>
                                    </div>
                                </div>
                                <div class="q-pa-sm column q-col-gutter-sm">
                                    <div v-if="!searchTerms.hasOwnProperty('db') || !searchTerms['db']" class="row">
                                        <div class="col-grow">
                                            <selector-input-element :options="fullCollectionArr" label="Collection" :value="selectedCollection" option-value="collid" :clearable="true" @update:value="updateSelectedCollection"></selector-input-element>
                                        </div>
                                    </div>
                                    <div class="row justify-between q-col-gutter-sm">
                                        <div class="col-4">
                                            <text-field-input-element label="Collector/Observer" :value="searchTermsArr['collector']" @update:value="(value) => updateSearchTerms('collector', value)"></text-field-input-element>
                                        </div>
                                        <div class="col-4">
                                            <text-field-input-element label="Number" :value="searchTermsArr['collnum']" @update:value="(value) => updateSearchTerms('collnum', value)"></text-field-input-element>
                                        </div>
                                        <div class="col-4">
                                            <date-input-element label="Date" :value="searchTermsArr['eventdate1']" @update:value="updateDateValue"></date-input-element>
                                        </div>
                                    </div>
                                    <div class="row justify-between q-col-gutter-sm">
                                        <div class="col-4">
                                            <text-field-input-element label="Country" :value="searchTermsArr['country']" @update:value="(value) => updateSearchTerms('country', value)"></text-field-input-element>
                                        </div>
                                        <div class="col-4">
                                            <text-field-input-element label="State/Province" :value="searchTermsArr['state']" @update:value="(value) => updateSearchTerms('state', value)"></text-field-input-element>
                                        </div>
                                        <div class="col-4">
                                            <text-field-input-element label="County" :value="searchTermsArr['county']" @update:value="(value) => updateSearchTerms('county', value)"></text-field-input-element>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-grow">
                                            <text-field-input-element data-type="textarea" label="Locality" :value="searchTermsArr['local']" @update:value="(value) => updateSearchTerms('local', value)"></text-field-input-element>
                                        </div>
                                    </div>
                                    <div class="full-width row">
                                        <div class="col-6 row q-col-gutter-sm">
                                            <div class="col-6">
                                                <text-field-input-element label="Catalog Number" :value="searchTermsArr['catnum']" @update:value="(value) => updateSearchTerms('catnum', value)"></text-field-input-element>
                                            </div>
                                            <div class="col-6">
                                                <checkbox-input-element label="Include other catalog numbers" :value="searchTermsArr['othercatnum']" @update:value="(value) => updateSearchTerms('othercatnum', value)"></checkbox-input-element>
                                            </div>
                                        </div>
                                        <div class="col-6 row justify-end q-gutter-sm">
                                            <div>
                                                <q-btn color="secondary" @click="createOccurrence();" label="Create Occurrence" :disabled="!isEditor" />
                                            </div>
                                            <div>
                                                <q-btn color="secondary" @click="processSearch();" label="Search Occurrences" :disabled="!searchCriteriaValid" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </q-tab-panel>
                            <q-tab-panel v-if="recordData.length > 0" class="q-pa-sm" name="records" :style="tabStyle">
                                <template v-if="multiSelect">
                                    <div>
                                        <div class="q-pa-sm q-mb-sm row justify-end q-gutter-sm" :style="recordTopRowStyle">
                                            <div>
                                                <q-btn color="secondary" @click="linkAllRecords();" label="Link All" />
                                            </div>
                                            <div>
                                                <q-btn color="secondary" @click="linkSelectedRecords();" label="Link Selected" :disabled="selectedOccidArr.length === 0" />
                                            </div>
                                        </div>
                                        <div class="q-pa-xs column q-gutter-sm overflow-auto no-wrap" :style="recordBottomRowStyle">
                                            <q-card v-for="record in recordData">
                                                <q-card-section class="row justify-between q-col-gutter-sm">
                                                    <div class="col-10 text-body1">
                                                        <occurrence-selector-info-block :occurrence-data="record"></occurrence-selector-info-block>
                                                    </div>
                                                    <div class="col-2 row justify-end self-center">
                                                        <checkbox-input-element :value="selectedOccidArr.includes(Number(record.occid))" @update:value="(value) => processOccidCheckboxSelection(record.occid, value)"></checkbox-input-element>
                                                    </div>
                                                </q-card-section>
                                            </q-card>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="q-pa-xs column q-gutter-sm">
                                        <q-card v-for="record in recordData">
                                            <q-card-section class="row justify-between q-col-gutter-sm">
                                                <div class="col-10 text-body1">
                                                    <occurrence-selector-info-block :occurrence-data="record"></occurrence-selector-info-block>
                                                </div>
                                                <div class="col-2 row justify-end self-center">
                                                    <q-btn color="primary" @click="linkOccurrence(record.occid);" label="Link Record" dense />
                                                </div>
                                            </q-card-section>
                                        </q-card>
                                    </div>
                                </template>
                            </q-tab-panel>
                        </q-tab-panels>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
        'date-input-element': dateInputElement,
        'occurrence-selector-info-block': occurrenceSelectorInfoBlock,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const collectionStore = useCollectionStore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const collectionData = Vue.computed(() => collectionStore.getCollectionData);
        const collectionOptions = Vue.computed(() => {
            if(props.editorLimit){
                return editorCollectionArr.value;
            }
            else{
                return fullCollectionArr.value;
            }
        });
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editCollectionIdArr = Vue.computed(() => {
            const idArr = [];
            editorCollectionArr.value.forEach((collection) => {
                idArr.push(Number(collection['collid']));
            });
            return idArr.length > 0 ? idArr : null;
        });
        const editorCollectionArr = Vue.ref([]);
        const fullCollectionArr = Vue.computed(() => collectionStore.getCollectionArr);
        const isEditor = Vue.computed(() => {
            let returnVal = false;
            if(selectedCollection.value && (props.editorLimit || editCollectionIdArr.value.includes(Number(selectedCollection.value)))){
                returnVal = true;
            }
            return returnVal;
        });
        const occurrenceTerm = Vue.computed(() => {
            if(props.multiSelect){
                return 'occurrence records';
            }
            else{
                return 'the occurrence record';
            }
        });
        const recordBottomRowStyle = Vue.ref(null);
        const recordData = Vue.ref([]);
        const recordDataOccidArr = Vue.computed(() => {
            const occidArr = [];
            recordData.value.forEach((record) => {
                occidArr.push(Number(record['occid']));
            });
            return occidArr;
        });
        const recordTopRowStyle = Vue.ref(null);
        const searchCriteriaValid = Vue.computed(() => {
            return (!!props.searchTerms || !!(searchTermsArr['catnum'] || searchTermsArr['collector'] || searchTermsArr['collnum']));
        });
        const searchTermsArr = Vue.reactive({
            country: null,
            state: null,
            county: null,
            local: null,
            collector: null,
            collnum: null,
            eventdate1: null,
            catnum: null,
            othercatnum: false
        });
        const selectedCollection = Vue.ref(null);
        const selectedOccidArr = Vue.ref([]);
        const symbUid = baseStore.getSymbUid;
        const tab = Vue.ref('criteria');
        const tabStyle = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function createOccurrence() {
            const occurrenceData = occurrenceStore.getBlankOccurrenceRecord;
            occurrenceData['collid'] = selectedCollection.value;
            occurrenceData['basisofrecord'] = collectionData.value['colltype'];
            occurrenceData['catalognumber'] = searchTermsArr['catnum'];
            occurrenceData['recordedby'] = searchTermsArr['collector'];
            occurrenceData['recordnumber'] = searchTermsArr['collnum'];
            occurrenceData['eventdate'] = searchTermsArr['eventdate1'];
            occurrenceData['country'] = searchTermsArr['country'];
            occurrenceData['stateprovince'] = searchTermsArr['state'];
            occurrenceData['county'] = searchTermsArr['county'];
            occurrenceData['locality'] = searchTermsArr['local'];
            if(searchTermsArr.hasOwnProperty('taxa')){
                occurrenceData['tid'] = searchTermsArr['taxa'];
            }
            if(searchTermsArr.hasOwnProperty('sciname')){
                occurrenceData['sciname'] = searchTermsArr['sciname'];
            }
            if(searchTermsArr.hasOwnProperty('family')){
                occurrenceData['family'] = searchTermsArr['family'];
            }
            const formData = new FormData();
            formData.append('collid', selectedCollection.value.toString());
            formData.append('occurrence', JSON.stringify(occurrenceData));
            formData.append('action', 'createOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    if(res && Number(res) > 0){
                        linkOccurrence(res);
                    }
                    else{
                        showNotification('negative', 'There was an error creating the occurrence record.');
                    }
                });
            });
        }

        function linkAllRecords() {
            context.emit('update:occid-arr', recordDataOccidArr.value);
            context.emit('close:popup');
        }

        function linkOccurrence(occid) {
            if(props.multiSelect){
                context.emit('update:occid-arr', [Number(occid)]);
            }
            else{
                context.emit('update:occid', occid);
            }
            context.emit('close:popup');
        }

        function linkSelectedRecords() {
            context.emit('update:occid-arr', selectedOccidArr.value);
            context.emit('close:popup');
        }

        function processOccidCheckboxSelection(occid, value) {
            if(value){
                selectedOccidArr.value.push(Number(occid));
            }
            else{
                const index = selectedOccidArr.value.indexOf(Number(occid));
                selectedOccidArr.value.splice(index, 1);
            }
        }

        function processSearch() {
            if(selectedCollection.value || editCollectionIdArr.value){
                showWorking();
                const options = {
                    schema: 'occurrence',
                    output: 'json'
                };
                if(props.editorLimit && !selectedCollection.value){
                    updateSearchTerms('db', editCollectionIdArr.value);
                }
                searchStore.processSimpleSearch(searchTermsArr, options, (data) => {
                    hideWorking();
                    if(props.avoidArr.length > 0){
                        const returnData = [];
                        data.forEach(record => {
                            if(!props.avoidArr.includes(Number(record.occid))){
                                returnData.push(record);
                            }
                        });
                        recordData.value = returnData.slice();
                    }
                    else{
                        recordData.value = data.slice();
                    }
                    if(recordData.value.length === 0){
                        showNotification('negative', ('There were no occurrences found matching that criteria in the selected collection.'));
                    }
                    else{
                        tab.value = 'records';
                    }
                });
            }
        }

        function setCollectionList() {
            collectionStore.getCollectionListByUid(symbUid, (collListData) => {
                editorCollectionArr.value = collListData;
            });
            if(!props.editorLimit){
                collectionStore.setCollectionArr();
            }
        }

        function setContentStyle() {
            contentStyle.value = null;
            tabStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
                tabStyle.value = 'height: ' + (contentRef.value.clientHeight - 90) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
                recordTopRowStyle.value = 'height: 60px;';
                recordBottomRowStyle.value = 'height: ' + ((contentRef.value.clientHeight - 90) - 60) + 'px;';
            }
        }

        function setSearchTerms() {
            Object.keys(props.searchTerms).forEach((key) => {
                if(props.searchTerms[key]){
                    searchTermsArr[key] = props.searchTerms[key];
                }
            });
        }

        function updateDateValue(value) {
            updateSearchTerms('eventdate1', (value ? value['date'] : null));
        }

        function updateSearchTerms(prop, value) {
            searchTermsArr[prop] = value;
        }

        function updateSelectedCollection(value) {
            selectedCollection.value = value;
            collectionStore.setCollection(selectedCollection.value);
            if(value){
                updateSearchTerms('db', [value]);
            }
            else{
                updateSearchTerms('db', null);
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            if(props.searchTerms){
                setSearchTerms();
            }
            setCollectionList();
        });

        return {
            collectionOptions,
            contentRef,
            contentStyle,
            fullCollectionArr,
            isEditor,
            occurrenceTerm,
            recordBottomRowStyle,
            recordData,
            recordTopRowStyle,
            searchCriteriaValid,
            searchTermsArr,
            selectedCollection,
            selectedOccidArr,
            tab,
            tabStyle,
            closePopup,
            createOccurrence,
            linkAllRecords,
            linkOccurrence,
            linkSelectedRecords,
            processOccidCheckboxSelection,
            processSearch,
            updateDateValue,
            updateSearchTerms,
            updateSelectedCollection
        }
    }
};
