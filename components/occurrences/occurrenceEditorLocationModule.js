const occurrenceEditorLocationModule = {
    template: `
        <q-card flat bordered class="black-border">
            <q-card-section class="q-px-sm q-pb-sm column q-col-gutter-sm">
                <div class="row justify-between">
                    <div class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                        Location
                    </div>
                    <div class="row justify-end q-gutter-sm">
                        <template v-if="locationId > 0 && collectingEventArr.length > 0">
                            <q-btn color="secondary" @click="showCollectingEventListPopup = true" label="View Events" />
                        </template>
                        <template v-if="Number(locationId) === 0">
                            <q-btn color="secondary" @click="showLocationLinkageToolPopup = true" label="Search Locations" />
                            <q-btn color="secondary" @click="createLocationRecord();" label="Create Location Record" :disabled="!locationValid" />
                        </template>
                        <template v-else>
                            <q-btn color="secondary" @click="processOpenEditor" label="Edit Location" />
                        </template>
                    </div>
                </div>
                <div class="q-mb-xs row justify-between q-col-gutter-sm">
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
                @update:event="processEventSelection"
                @close:popup="showCollectingEventListPopup = false"
            ></occurrence-collecting-event-list-popup>
        </template>
        <template v-if="showLocationLinkageToolPopup">
            <occurrence-location-linkage-tool-popup
                :show-popup="showLocationLinkageToolPopup"
                @update:location="processLocationSelection"
                @close:popup="showLocationLinkageToolPopup = false"
            ></occurrence-location-linkage-tool-popup>
        </template>
        <template v-if="showLocationEditorPopup">
            <occurrence-location-editor-popup
                :show-popup="showLocationEditorPopup"
                @close:popup="showLocationEditorPopup = false"
            ></occurrence-location-editor-popup>
        </template>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'location-field-module': locationFieldModule,
        'location-name-code-auto-complete': locationNameCodeAutoComplete,
        'occurrence-collecting-event-list-popup': occurrenceCollectingEventListPopup,
        'occurrence-location-editor-popup': occurrenceLocationEditorPopup,
        'occurrence-location-linkage-tool-popup': occurrenceLocationLinkageToolPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const collectingEventArr = Vue.computed(() => occurrenceStore.getLocationCollectingEventArr);
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const confirmationPopupRef = Vue.ref(null);
        const editorConfirmed = Vue.ref(false);
        const locationData = Vue.computed(() => occurrenceStore.getLocationData);
        const locationFields = Vue.computed(() => occurrenceStore.getLocationFields);
        const locationId = Vue.computed(() => occurrenceStore.getLocationID);
        const locationValid = Vue.computed(() => occurrenceStore.getLocationValid);
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const showCollectingEventListPopup = Vue.ref(false);
        const showLocationEditorPopup = Vue.ref(false);
        const showLocationLinkageToolPopup = Vue.ref(false);

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

        function processEventSelection(event) {
            occurrenceStore.setCurrentCollectingEventRecord(event.eventid);
        }

        function processLocationCodeNameSelection(key, value) {
            if(value.hasOwnProperty('id') && Number(value.id) > 0){
                occurrenceStore.setCurrentLocationRecord(value.id);
            }
            else{
                updateLocationData(key, value.value);
            }
        }

        function processLocationSelection(location) {
            occurrenceStore.setCurrentLocationRecord(location.locationid);
        }

        function processOpenEditor() {
            if(editorConfirmed.value){
                showLocationEditorPopup.value = true;
            }
            else{
                const confirmText = 'If you want to edit this location, click OK to continue. If you want to change the location for this collecting event, click Cancel, and then click Edit Event button in the Collecting Event section, and then click the Change Location button. If you want to change the location for this occurrence only, click Cancel, and then click Change Event/Location button in the bottom section. ';
                confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'Cancel', trueText: 'OK', callback: (val) => {
                    editorConfirmed.value = true;
                    if(val){
                        showLocationEditorPopup.value = true;
                    }
                }});
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
            confirmationPopupRef,
            locationData,
            locationId,
            locationFields,
            locationValid,
            occurrenceFieldDefinitions,
            showCollectingEventListPopup,
            showLocationEditorPopup,
            showLocationLinkageToolPopup,
            createLocationRecord,
            processEventSelection,
            processLocationCodeNameSelection,
            processLocationSelection,
            processOpenEditor,
            updateLocationData
        }
    }
};
