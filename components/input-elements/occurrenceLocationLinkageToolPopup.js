const occurrenceLocationLinkageToolPopup = {
    props: {
        currentOccid: {
            type: Number,
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
                        <div class="q-px-sm q-pt-sm">
                            <div class="text-h6 text-bold">Occurrence Linkage Tool</div>
                            <div class="text-body1">
                                Select the collection and enter criteria to search for the occurrence record you would like to link, 
                                or which to create a new occurrence record to link.
                            </div>
                        </div>
                        <div class="q-pa-sm column q-col-gutter-sm">
                            <div class="row">
                                <div class="col-grow">
                                    <selector-input-element :options="collectionOptions" label="Collection" :value="selectedCollection" option-value="collid" :clearable="true" @update:value="(value) => selectedCollection = value"></selector-input-element>
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
                            <div class="full-width row">
                                <div class="col-6 row">
                                    <div class="col-6">
                                        <text-field-input-element label="Catalog Number" :value="catalogNumberVal" @update:value="(value) => catalogNumberVal = value"></text-field-input-element>
                                    </div>
                                    <div class="col-6">
                                        <checkbox-input-element label="Include other catalog numbers" :value="includeOtherCatalogNumberVal" @update:value="(value) => includeOtherCatalogNumberVal = value"></checkbox-input-element>
                                    </div>
                                </div>
                                <div class="col-6 row justify-end q-gutter-sm">
                                    <div>
                                        <q-btn color="secondary" @click="createOccurrence();" label="Create Occurrence" :disabled="!selectedCollection" />
                                    </div>
                                    <div>
                                        <q-btn color="secondary" @click="processSearch();" label="Search Occurrences" :disabled="!searchCriteria" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <q-separator></q-separator>
                        <div v-if="locationArr.length" class="q-pa-md column q-gutter-md">
                            <q-card v-for="location in locationArr">
                                <q-card-section class="q-pa-md column">
                                    <div v-if="location.locationcode || location.locationname">
                                        <template v-if="location.locationcode">
                                            <span>
                                                {{ location.locationcode }}
                                            </span>
                                        </template>
                                        <template v-if="location.locationname">
                                            <span>
                                                {{ (location.locationcode ? ' - ' : '') + location.locationname }}
                                            </span>
                                        </template>
                                    </div>
                                    <div>
                                        <template v-if="location.country">
                                            <span>
                                                {{ location.country + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="location.stateprovince">
                                            <span>
                                                {{ location.stateprovince + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="location.county">
                                            <span>
                                                {{ location.county + '; ' }}
                                            </span>
                                        </template>
                                        <template v-if="location.locality">
                                            <span>
                                                {{ location.locality }}
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span>
                                                Locality data empty
                                            </span>
                                        </template>
                                    </div>
                                    <template v-if="location.decimallatitude || location.verbatimcoordinates">
                                        <div>
                                            <template v-if="location.decimallatitude">
                                                <span>
                                                    {{ location.decimallatitude + ', ' + location.decimallongitude }}
                                                </span>
                                                <span v-if="location.coordinateuncertaintyinmeters">
                                                    {{ ' +-' + location.coordinateuncertaintyinmeters + 'm.' }}
                                                </span>
                                                <span v-if="location.geodeticdatum">
                                                    {{ ' (' + location.geodeticdatum + ')' }}
                                                </span>
                                            </template>
                                            <template v-if="location.verbatimcoordinates">
                                                <span :class="location.decimallatitude ? 'q-ml-md' : ''">
                                                    {{ location.verbatimcoordinates }}
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                    <div v-if="location.locationremarks">
                                        <span>
                                            {{ location.locationremarks }}
                                        </span>
                                    </div>
                                    <div v-if="location.minimumelevationinmeters || location.maximumelevationinmeters || location.verbatimelevation">
                                        <template v-if="location.minimumelevationinmeters">
                                            <span>
                                                {{ location.minimumelevationinmeters + (location.maximumelevationinmeters ? ('-' + location.maximumelevationinmeters) : '') + ' meters' }}
                                            </span>
                                        </template>
                                        <template v-if="location.verbatimelevation">
                                            <span>
                                                {{ (location.minimumelevationinmeters ? '; ' : '') + 'Verbatim elevation: ' + location.verbatimelevation }}
                                            </span>
                                        </template>
                                    </div>
                                    <div class="q-mt-md q-pl-md row justify-start q-gutter-md">
                                        <q-btn color="primary" @click="processLocationSelection(location.locationid);" label="Select Location" dense />
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
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const collectionStore = useCollectionStore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const catalogNumberVal = Vue.ref(null);
        const collectionOptions = Vue.ref([]);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const includeOtherCatalogNumberVal = Vue.ref(false);
        const recordData = Vue.ref([]);
        const recordedByVal = Vue.ref(null);
        const recordNumberVal = Vue.ref(null);
        const searchCriteria = Vue.computed(() => {
            return !!(selectedCollection && (catalogNumberVal.value || recordedByVal.value || recordNumberVal.value));
        });
        const selectedCollection = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function createOccurrence() {
            const occurrenceData = occurrenceStore.getBlankOccurrenceRecord;
            occurrenceData['collid'] = selectedCollection.value;
            if(catalogNumberVal.value){
                occurrenceData['catalognumber'] = catalogNumberVal.value;
            }
            if(recordedByVal.value){
                occurrenceData['recordedby'] = recordedByVal.value;
            }
            if(recordNumberVal.value){
                occurrenceData['recordnumber'] = recordNumberVal.value;
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

        function findNearbyLocations() {
            occurrenceStore.getNearbyLocations((locationArr) => {
                if(locationArr.length > 0){
                    nearbyLocationArr.value = locationArr;
                    showLocationLinkageToolPopup.value = true;
                }
                else{
                    showNotification('negative', 'There were no nearby locations found.');
                }
            });
        }

        function linkOccurrence(occid) {
            context.emit('update:occid', occid);
            context.emit('close:popup');
        }

        function processSearch() {
            showWorking();
            const options = {
                schema: 'occurrence',
                output: 'json'
            };
            const starr = {
                db: selectedCollection.value.toString(),
                catnum: catalogNumberVal.value,
                collector: recordedByVal.value,
                collnum: recordNumberVal.value
            };
            if(includeOtherCatalogNumberVal.value){
                starr['othercatnum'] = 1;
            }
            searchStore.processSimpleSearch(starr, options, (data) => {
                hideWorking();
                if(props.currentOccid){
                    const currentObj = data.find(record => Number(record.occid) === Number(props.currentOccid));
                    if(currentObj){
                        const index = data.indexOf(currentObj);
                        data.splice(index, 1);
                    }
                }
                if(data.length > 0){
                    recordData.value = data.slice();
                }
                else{
                    showNotification('negative', ('There were no occurrences found matching that criteria in the selected collection.'));
                }
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
            includeOtherCatalogNumberVal,
            recordData,
            recordedByVal,
            recordNumberVal,
            searchCriteria,
            selectedCollection,
            closePopup,
            createOccurrence,
            linkOccurrence,
            processSearch
        }
    }
};
