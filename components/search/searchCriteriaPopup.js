const searchCriteriaPopup = {
    props: {
        collectionId: {
            type: Number,
            default: null
        },
        popupType: {
            type: String,
            default: 'search'
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        showSpatial: {
            type: Boolean,
            default: true
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="search-criteria-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit overflow-auto">
                    <div :style="contentStyle" class="overflow-auto">
                        <q-tabs v-model="tab" content-class="bg-grey-3" active-bg-color="grey-4" align="justify">
                            <q-tab name="criteria" label="Criteria" no-caps></q-tab>
                            <q-tab v-if="!collectionId" name="collections" label="Collections" no-caps></q-tab>
                            <q-tab name="advanced" label="Advanced" no-caps></q-tab>
                            <q-tab v-if="mofExtensionFieldsArr.length > 0" name="mofextension" label="Data Extension" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel class="q-pa-none" name="criteria">
                                <div class="column q-pa-sm q-col-gutter-sm">
                                    <search-criteria-popup-tab-controls :popup-type="popupType" @reset:search-criteria="resetCriteria" @process:search-load-records="loadRecords" @process:build-checklist="buildChecklist"></search-criteria-popup-tab-controls>
                                    <search-criteria-block ref="searchCriteriaBlockRef" :collection-id="collectionId" :show-spatial="showSpatial" @open:spatial-popup="openSpatialPopup"></search-criteria-block>
                                </div>
                            </q-tab-panel>
                            <q-tab-panel class="q-pa-none" v-if="!collectionId" name="collections">
                                <div class="column q-pa-sm q-col-gutter-sm">
                                    <search-criteria-popup-tab-controls :popup-type="popupType" @reset:search-criteria="resetCriteria" @process:search-load-records="loadRecords" @process:build-checklist="buildChecklist"></search-criteria-popup-tab-controls>
                                    <search-collections-block></search-collections-block>
                                </div>
                            </q-tab-panel>
                            <q-tab-panel class="q-pa-none" name="advanced">
                                <div class="column q-pa-sm q-col-gutter-sm">
                                    <search-criteria-popup-tab-controls :popup-type="popupType" @reset:search-criteria="resetCriteria" @process:search-load-records="loadRecords" @process:build-checklist="buildChecklist"></search-criteria-popup-tab-controls>
                                    <advanced-query-builder :field-options="advancedFieldOptions" query-type="advanced"></advanced-query-builder>
                                </div>
                            </q-tab-panel>
                            <q-tab-panel v-if="mofExtensionFieldsArr.length > 0" class="q-pa-none" name="mofextension">
                                <div class="column q-pa-sm q-col-gutter-sm">
                                    <search-criteria-popup-tab-controls :popup-type="popupType" @reset:search-criteria="resetCriteria" @process:search-load-records="loadRecords" @process:build-checklist="buildChecklist"></search-criteria-popup-tab-controls>
                                    <advanced-query-builder :field-options="mofExtensionFieldsArr" query-type="mofextension"></advanced-query-builder>
                                </div>
                            </q-tab-panel>
                        </q-tab-panels>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'advanced-query-builder': advancedQueryBuilder,
        'search-collections-block': searchCollectionsBlock,
        'search-criteria-block': searchCriteriaBlock,
        'search-criteria-popup-tab-controls': searchCriteriaPopupTabControls
    },
    setup(props, context) {
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const advancedFieldOptions = Vue.computed(() => searchStore.getQueryBuilderFieldOptions);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const mofExtensionFieldsArr = Vue.reactive([]);
        const searchCriteriaBlockRef = Vue.ref(null);
        const tab = Vue.ref('criteria');

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function buildChecklist() {
            context.emit('process:build-checklist');
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function loadRecords() {
            context.emit('process:search-load-records');
        }

        function openSpatialPopup(type) {
            context.emit('open:spatial-popup', type);
        }

        function resetCriteria() {
            searchStore.clearSearchTerms();
            if(searchCriteriaBlockRef.value){
                searchCriteriaBlockRef.value.resetCriteria();
            }
            context.emit('reset:search-criteria');
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                const heightOffset = contentRef.value.clientWidth > 700 ? 20 : 30;
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - heightOffset) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setMoFExtensionFieldArrFromCollectionId() {
            const formData = new FormData();
            formData.append('collid', props.collectionId.toString());
            formData.append('action', 'getCollectionInfoArr');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    if(resObj['configuredData']){
                        if(resObj['configuredData'].hasOwnProperty('eventMofExtension') && Object.keys(resObj['configuredData']['eventMofExtension']['dataFields']).length > 0){
                            Object.keys(resObj['configuredData']['eventMofExtension']['dataFields']).forEach((key) => {
                                mofExtensionFieldsArr.push({
                                    dataType: 'event',
                                    field: key,
                                    label: resObj['configuredData']['eventMofExtension']['dataFields'][key]['label']
                                });
                            });
                        }
                        if(resObj['configuredData'].hasOwnProperty('occurrenceMofExtension') && Object.keys(resObj['configuredData']['occurrenceMofExtension']['dataFields']).length > 0){
                            Object.keys(resObj['configuredData']['occurrenceMofExtension']['dataFields']).forEach((key) => {
                                mofExtensionFieldsArr.push({
                                    dataType: 'occurrence',
                                    field: key,
                                    label: resObj['configuredData']['occurrenceMofExtension']['dataFields'][key]['label']
                                });
                            });
                        }
                    }
                });
            });
        }

        function setMoFExtensionFieldArrFromGlobalArr() {
            baseStore.getGlobalConfigValue('MOF_SEARCH_FIELD_JSON', (dataStr) => {
                const data = dataStr ? JSON.parse(dataStr) : null;
                if(data && data.length > 0){
                    data.forEach((object) => {
                        mofExtensionFieldsArr.push(object);
                    });
                }
            });
        }

        function updateSearchTerms(prop, value) {
            searchStore.updateSearchTerms(prop, value);
        }

        Vue.provide('updateSearchTerms', updateSearchTerms);

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            if(Number(props.collectionId) > 0){
                setMoFExtensionFieldArrFromCollectionId();
            }
            else{
                setMoFExtensionFieldArrFromGlobalArr();
            }
        });

        return {
            advancedFieldOptions,
            contentRef,
            contentStyle,
            mofExtensionFieldsArr,
            searchCriteriaBlockRef,
            tab,
            buildChecklist,
            closePopup,
            loadRecords,
            openSpatialPopup,
            resetCriteria
        }
    }
};
