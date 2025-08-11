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
                            <div class="text-h6 text-bold">Search Locations</div>
                        </div>
                        <div class="q-pa-sm column q-col-gutter-sm">
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <text-field-input-element label="Country" :value="locationData.country" @update:value="(value) => updateLocationData('country', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <text-field-input-element label="State/Province" :value="locationData.stateprovince" @update:value="(value) => updateLocationData('stateprovince', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <text-field-input-element label="County" :value="locationData.county" @update:value="(value) => updateLocationData('county', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row q-col-gutter-sm">
                                <div class="col-grow">
                                    <text-field-input-element data-type="textarea" label="Locality Contains" :value="localityVal" @update:value="(value) => localityVal = value"></text-field-input-element>
                                </div>
                            </div>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6 col-md-6">
                                    <text-field-input-element label="Latitude" :value="locationData.decimallatitude" @update:value="(value) => updateLocationData('decimallatitude', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6">
                                    <text-field-input-element label="Longitude" :value="locationData.decimallongitude" @update:value="(value) => updateLocationData('decimallongitude', value)"></text-field-input-element>
                                </div>
                            </div>
                            <div class="full-width row justify-end q-gutter-sm">
                                <div>
                                    <q-btn color="secondary" @click="findNearbyLocations();" label="Find Nearby Locations" :disabled="!searchCoordinates" />
                                </div>
                                <div>
                                    <q-btn color="secondary" @click="processSearch();" label="Search Locations" :disabled="!searchCriteria" />
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
                                        <q-btn color="primary" @click="processLocationSelection(location);" label="Select Location" dense />
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
        const occurrenceStore = useOccurrenceStore();

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const localityVal = Vue.ref(null);
        const locationArr = Vue.ref([]);
        const locationData = Vue.computed(() => occurrenceStore.getLocationData);
        const searchCoordinates = Vue.computed(() => {
            return !!(locationData.value['decimallatitude'] && locationData.value['decimallongitude']);
        });
        const searchCriteria = Vue.computed(() => {
            return !!(locationData.value['country'] || locationData.value['county'] || locationData.value['decimallatitude'] || locationData.value['decimallongitude'] || localityVal.value || locationData.value['stateprovince']);
        });

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function findNearbyLocations() {
            occurrenceStore.getNearbyLocations((dataArr) => {
                if(dataArr.length > 0){
                    locationArr.value = dataArr;
                }
                else{
                    showNotification('negative', 'There were no nearby locations found.');
                }
            });
        }

        function processLocationSelection(location) {
            context.emit('update:location', location);
            context.emit('close:popup');
        }

        function processSearch() {
            showWorking();
            const criteria = {
                country: locationData.value['country'],
                stateprovince: locationData.value['stateprovince'],
                county: locationData.value['county'],
                decimallatitude: locationData.value['decimallatitude'],
                decimallongitude: locationData.value['decimallongitude'],
                locality: localityVal.value
            };
            occurrenceStore.searchLocations(criteria, (dataArr) => {
                hideWorking();
                if(dataArr.length > 0){
                    locationArr.value = dataArr;
                }
                else{
                    showNotification('negative', 'There were no locations found.');
                }
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateLocationData(key, value) {
            occurrenceStore.updateLocationEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            contentRef,
            contentStyle,
            localityVal,
            locationArr,
            locationData,
            searchCoordinates,
            searchCriteria,
            closePopup,
            findNearbyLocations,
            processLocationSelection,
            processSearch,
            updateLocationData
        }
    }
};
