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
                    <div :style="contentStyle" class="q-pa-sm overflow-auto">
                        <div class="q-px-sm q-pt-sm">
                            <div class="text-h6 text-bold">Occurrence Linkage Tool</div>
                            <div class="text-body1">
                                Select the collection and enter criteria to search for the occurrence record you would like to link, 
                                or which to create a new occurrence record to link.
                            </div>
                        </div>
                        <div class="q-pa-sm column q-col-gutter-sm">
                            <div v-if="!searchTerms.hasOwnProperty('db') || !searchTerms['db']" class="row">
                                <div class="col-grow">
                                    <selector-input-element :options="collectionOptions" label="Collection" :value="selectedCollection" option-value="collid" :clearable="true" @update:value="updateSelectedCollection"></selector-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-6">
                                    <text-field-input-element label="Collector/Observer" :value="searchTermsArr['collector']" @update:value="(value) => updateSearchTerms('collector', value)"></text-field-input-element>
                                </div>
                                <div class="col-6">
                                    <text-field-input-element label="Number" :value="searchTermsArr['collnum']" @update:value="(value) => updateSearchTerms('collnum', value)"></text-field-input-element>
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
                                        <q-btn color="secondary" @click="createOccurrence();" label="Create Occurrence" :disabled="!selectedCollection" />
                                    </div>
                                    <div>
                                        <q-btn color="secondary" @click="processSearch();" label="Search Occurrences" :disabled="!searchCriteriaValid" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <q-separator></q-separator>
                        <div v-if="recordData.length" class="q-px-sm q-mt-sm column q-gutter-sm">
                            <q-card v-for="record in recordData">
                                <q-card-section class="row justify-between q-col-gutter-sm">
                                    <div class="col-10 text-body1">
                                        <occurrence-selector-info-block :occurrence-data="record"></occurrence-selector-info-block>
                                    </div>
                                    <div class="col-2 row justify-end self-center">
                                        <q-btn color="primary" @click="linkOccurrence(record.occid);" label="Link Occurrence" dense />
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement,
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

        const collectionOptions = Vue.ref([]);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editCollectionIdArr = Vue.computed(() => {
            const idArr = [];
            collectionOptions.value.forEach((collection) => {
                idArr.push(Number(collection['collid']));
            });
            return idArr.length > 0 ? idArr : null;
        });
        const recordData = Vue.ref([]);
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
        const symbUid = baseStore.getSymbUid;

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function createOccurrence() {
            const occurrenceData = occurrenceStore.getBlankOccurrenceRecord;
            occurrenceData['collid'] = selectedCollection.value;
            if(searchTermsArr['catnum']){
                occurrenceData['catalognumber'] = searchTermsArr['catnum'];
            }
            if(searchTermsArr['collector']){
                occurrenceData['recordedby'] = searchTermsArr['collector'];
            }
            if(searchTermsArr['collnum']){
                occurrenceData['recordnumber'] = searchTermsArr['collnum'];
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

        function linkOccurrence(occid) {
            context.emit('update:occid', occid);
            context.emit('close:popup');
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
                });
            }
        }

        function setCollectionList() {
            collectionStore.getCollectionListByUid(symbUid, (collListData) => {
                collectionOptions.value = collListData;
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setSearchTerms() {
            Object.keys(props.searchTerms).forEach((key) => {
                if(props.searchTerms[key]){
                    searchTermsArr[key] = props.searchTerms[key];
                }
            });
        }

        function updateSearchTerms(prop, value) {
            searchTermsArr[prop] = value;
        }

        function updateSelectedCollection(value) {
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
            recordData,
            searchCriteriaValid,
            searchTermsArr,
            selectedCollection,
            closePopup,
            createOccurrence,
            linkOccurrence,
            processSearch,
            updateSearchTerms,
            updateSelectedCollection
        }
    }
};
