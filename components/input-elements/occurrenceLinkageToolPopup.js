const occurrenceLinkageToolPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-sm column q-col-gutter-sm">
                            <div class="row">
                                <div class="col-grow">
                                    <selector-input-element :options="collectionOptions" label="Collection" :value="selectedCollection" option-value="collid" :clearable="true" @update:value="(value) => selectedCollection = value"></selector-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-6">
                                    <text-field-input-element label="Catalog Number" :value="catalogNumberVal" @update:value="(value) => catalogNumberVal = value"></text-field-input-element>
                                </div>
                                <div class="col-6">
                                    <text-field-input-element label="Other Catalog Numbers" :value="otherCatalogNumberVal" @update:value="(value) => otherCatalogNumberVal = value"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-6">
                                    <text-field-input-element label="Collector/Observer" :value="recordedByVal" @update:value="(value) => recordedByVal = value"></text-field-input-element>
                                </div>
                                <div class="col-6">
                                    <text-field-input-element label="Number" :value="recordNumberVal" @update:value="(value) => recordNumberVal = value"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-end">
                                <div>
                                    <q-btn color="secondary" @click="processSearch();" label="Search Occurrences" :disabled="!searchCriteria" />
                                </div>
                            </div>
                        </div>
                        <q-separator></q-separator>
                        <div v-if="recordArr.length" class="q-pa-md column q-gutter-md">
                            <q-card v-for="record in recordArr">
                                <q-card-section class="q-pa-md column">
                                    <div>
                                        
                                    </div>
                                    <div class="q-mt-md q-pl-md row justify-start q-gutter-md">
                                        <template v-if="popupType === 'occurrence'">
                                            <q-btn color="primary" @click="processMergeEventData(event, false);" label="Merge All Data" dense />
                                            <q-btn color="primary" @click="processMergeEventData(event);" label="Merge Missing Data Only" dense />
                                        </template>
                                        <template v-else-if="popupType === 'location'">
                                            <q-btn color="primary" @click="processEventSelection(event.eventid);" label="Select Event" dense />
                                        </template>
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
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const collectionStore = useCollectionStore();
        const searchStore = useSearchStore();

        const catalogNumberVal = Vue.ref(null);
        const collectionOptions = Vue.ref([]);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const otherCatalogNumberVal = Vue.ref(null);
        const recordArr = Vue.ref([]);
        const recordedByVal = Vue.ref(null);
        const recordNumberVal = Vue.ref(null);
        const searchCriteria = Vue.computed(() => {
            return !!(selectedCollection && (catalogNumberVal || otherCatalogNumberVal || recordedByVal || recordNumberVal));
        });
        const selectedCollection = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processSearch() {
            const options = {
                schema: 'map',
                output: 'json'
            };
            const starr = {
                db: selectedCollection.value.toString(),
                catnum: catalogNumberVal.value,
                collector: recordedByVal.value,
                collnum: recordNumberVal.value
            };
            searchStore.processSimpleSearch(starr, options, (data) => {
                console.log(data);
            });
        }

        function setCollectionList() {
            collectionStore.getCollectionListByUserRights((collListData) => {
                collectionOptions.value = collListData;
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            setCollectionList();
        });

        return {
            catalogNumberVal,
            collectionOptions,
            contentRef,
            contentStyle,
            otherCatalogNumberVal,
            recordArr,
            recordedByVal,
            recordNumberVal,
            searchCriteria,
            selectedCollection,
            closePopup,
            processSearch
        }
    }
};
