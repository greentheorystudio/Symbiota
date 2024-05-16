const occurrenceEditorLocationModule = {
    template: `
        <q-card flat bordered class="black-border">
            <q-card-section class="q-px-sm q-pb-sm column q-col-gutter-xs">
                <div class="row justify-between">
                    <div>
                        <div class="row q-gutter-sm">
                            <template v-if="locationId > 0 && collectingEventArr.length > 0">
                                <q-btn color="secondary" @click="showCollectingEventListPopup = true" label="View Events" />
                            </template>
                            <template v-if="locationData.decimallatitude && locationData.decimallongitude">
                                <q-btn color="secondary" @click="findNearbyLocations();" label="Find Nearby Locations" />
                            </template>
                        </div>
                    </div>
                    <div class="row justify-end">
                        <template v-if="Number(locationId) === 0">
                            <q-btn color="secondary" @click="createLocationRecord();" label="Create Location Record" :disabled="!locationValid" />
                        </template>
                        <template v-else>
                            <q-btn color="secondary" @click="showLocationEditorPopup = true" label="Edit Location" />
                        </template>
                    </div>
                </div>
                <div class="q-mb-xs row justify-between q-col-gutter-xs">
                    <div class="col-12 col-sm-6 col-md-4">
                        <location-name-code-auto-complete :disabled="(locationId > 0)" :collid="collId" key-value="code" :definition="occurrenceFieldDefinitions['locationcode']" label="Location Code" :maxlength="locationFields['locationcode'] ? locationFields['locationcode']['length'] : 0" :value="locationData.locationcode" @update:value="(value) => processLocationCodeNameSelection('locationcode', value)"></location-name-code-auto-complete>
                    </div>
                    <div class="col-12 col-sm-6 col-md-6">
                        <location-name-code-auto-complete :disabled="(locationId > 0)" :collid="collId" key-value="name" :definition="occurrenceFieldDefinitions['locationname']" label="Location Name" :maxlength="locationFields['locationname'] ? locationFields['locationname']['length'] : 0" :value="locationData.locationname" @update:value="(value) => processLocationCodeNameSelection('locationname', value)"></location-name-code-auto-complete>
                    </div>
                </div>
                <location-field-module :disabled="(locationId > 0)" :data="locationData" :fields="locationFields" :field-definitions="occurrenceFieldDefinitions" @update:location-data="(data) => updateLocationData(data.key, data.value)"></location-field-module>
            </q-card-section>
        </q-card>
        <template v-if="showCollectingEventListPopup">
            <occurrence-collecting-event-list-popup
                    popup-type="location"
                    :event-arr="collectingEventArr"
                    :show-popup="showCollectingEventListPopup"
                    @close:popup="showCollectingEventListPopup = false"
            ></occurrence-collecting-event-list-popup>
        </template>
        <template v-if="showLocationListPopup">
            <occurrence-location-list-popup
                    :location-arr="nearbyLocationArr"
                    :show-popup="showLocationListPopup"
                    @close:popup="closeLocationListPopup();"
            ></occurrence-location-list-popup>
        </template>
        <template v-if="showLocationEditorPopup">
            <occurrence-location-editor-popup
                    :show-popup="showLocationEditorPopup"
                    @close:popup="showLocationEditorPopup = false"
            ></occurrence-location-editor-popup>
        </template>
    `,
    components: {
        'location-field-module': locationFieldModule,
        'location-name-code-auto-complete': locationNameCodeAutoComplete,
        'occurrence-collecting-event-list-popup': occurrenceCollectingEventListPopup,
        'occurrence-location-editor-popup': occurrenceLocationEditorPopup,
        'occurrence-location-list-popup': occurrenceLocationListPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const collectingEventArr = Vue.computed(() => occurrenceStore.getLocationCollectingEventArr);
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const locationData = Vue.computed(() => occurrenceStore.getLocationData);
        const locationFields = Vue.computed(() => occurrenceStore.getLocationFields);
        const locationId = Vue.computed(() => occurrenceStore.getLocationID);
        const locationValid = Vue.computed(() => occurrenceStore.getLocationValid);
        const nearbyLocationArr = Vue.ref([]);
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showCollectingEventListPopup = Vue.ref(false);
        const showLocationEditorPopup = Vue.ref(false);
        const showLocationListPopup = Vue.ref(false);

        function closeLocationListPopup() {
            showLocationListPopup.value = false;
            nearbyLocationArr.value = [];
        }

        function createLocationRecord() {
            occurrenceStore.createLocationRecord((newLocationId) => {
                if(newLocationId > 0){
                    showNotification('positive','Location record created successfully.');
                }
                else{
                    showNotification('negative', 'There was an error creating the location record.');
                }
            });
        }

        function findNearbyLocations() {
            occurrenceStore.getNearbyLocations((locationArr) => {
                if(locationArr.length > 0){
                    nearbyLocationArr.value = locationArr;
                    showLocationListPopup.value = true;
                }
                else{
                    showNotification('negative', 'There were no nearby locations found.');
                }
            });
        }

        function processLocationCodeNameSelection(key, value) {
            if(value.hasOwnProperty('id') && Number(value.id) > 0){
                occurrenceStore.setCurrentLocationRecord(value.id);
            }
            else{
                updateLocationData(key, value.value);
            }
        }

        function updateLocationData(key, value) {
            occurrenceStore.updateLocationEditData(key, value);
        }

        Vue.onMounted(() => {
            occurrenceStore.setLocationFields();
        });

        return {
            collectingEventArr,
            collId,
            locationData,
            locationId,
            locationFields,
            locationValid,
            nearbyLocationArr,
            occurrenceFieldDefinitions,
            showCollectingEventListPopup,
            showLocationEditorPopup,
            showLocationListPopup,
            closeLocationListPopup,
            createLocationRecord,
            findNearbyLocations,
            processLocationCodeNameSelection,
            updateLocationData
        }
    }
};
