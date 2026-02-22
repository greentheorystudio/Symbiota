const occurrenceEditorEventLocationTransferPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog v-if="!showLocationLinkageToolPopup && !showCollectingEventListPopup" class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <q-btn-toggle v-model="selectedChangeType" :options="changeTypeOptions" class="black-border" rounded unelevated toggle-color="primary" color="white" text-color="primary" @update:model-value="processSelectedChangeTypeChange" aria-label="Change type" tabindex="0"></q-btn-toggle>
                                </div>
                                <div class="row justify-end q-gutter-xs">
                                    <template v-if="selectedChangeType === 'changelocation'">
                                        <q-btn color="secondary" @click="showLocationLinkageToolPopup = true" label="Search Locations" tabindex="0" />
                                    </template>
                                    <template v-if="collectingEventArr.length > 0">
                                        <q-btn color="secondary" @click="showCollectingEventListPopup = true" label="View Location Events" tabindex="0" />
                                    </template>
                                    <q-btn color="secondary" @click="processChangeOccurrence();" :label="(selectedChangeType === 'changelocation' ? 'Change Event & Location' : 'Change Event')" :disabled="!changeValid" tabindex="0" />
                                </div>
                            </div>
                            <q-card v-if="selectedChangeType === 'changelocation'" flat bordered>
                                <q-card-section class="q-px-sm q-pb-sm column q-col-gutter-sm">
                                    <div class="q-mb-xs row justify-between q-col-gutter-sm">
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <location-name-code-auto-complete :collid="collId" key-value="code" :definition="occurrenceFieldDefinitions['locationcode']" label="Location Code" :maxlength="locationFields['locationcode'] ? locationFields['locationcode']['length'] : 0" :value="locationData.locationcode" @update:value="(value) => processLocationCodeNameSelection('locationcode', value)"></location-name-code-auto-complete>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <location-name-code-auto-complete :collid="collId" key-value="name" :definition="occurrenceFieldDefinitions['locationname']" label="Location Name" :maxlength="locationFields['locationname'] ? locationFields['locationname']['length'] : 0" :value="locationData.locationname" @update:value="(value) => processLocationCodeNameSelection('locationname', value)"></location-name-code-auto-complete>
                                        </div>
                                    </div>
                                    <location-field-module :disabled="(locationData['locationid'] > 0)" :data="locationData" :fields="locationFields" :field-definitions="occurrenceFieldDefinitions" @update:location-data="(data) => updateLocationData(data.key, data.value)"></location-field-module>
                                </q-card-section>
                            </q-card>
                            <q-card flat bordered>
                                <q-card-section class="q-px-sm q-pb-sm column q-col-gutter-sm">
                                    <collecting-event-field-module :event-mode="true" :data="eventData" :fields="eventFields" :field-definitions="occurrenceFieldDefinitions" @update:collecting-event-data="(data) => updateCollectingEventData(data.key, data.value)"></collecting-event-field-module>
                                    <div class="row justify-between q-col-gutter-sm">
                                        <div class="col-12 col-sm-6 col-md-9">
                                            <text-field-input-element :definition="occurrenceFieldDefinitions['eventremarks']" label="Event Remarks" :maxlength="eventFields['eventremarks'] ? eventFields['eventremarks']['length'] : 0" :value="eventData.eventremarks" @update:value="(value) => updateCollectingEventData('eventremarks', value)"></text-field-input-element>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-3">
                                            <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['repcount']" label="Rep Count" :maxlength="eventFields['repcount'] ? eventFields['repcount']['length'] : 0" :value="eventData.repcount" @update:value="(value) => updateCollectingEventData('repcount', value)"></text-field-input-element>
                                        </div>
                                    </div>
                                </q-card-section>
                            </q-card>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <template v-if="showCollectingEventListPopup">
            <occurrence-collecting-event-list-popup
                popup-type="location"
                :event-arr="collectingEventArr"
                :show-popup="showCollectingEventListPopup"
                @update:event="setEventData"
                @close:popup="showCollectingEventListPopup = false"
            ></occurrence-collecting-event-list-popup>
        </template>
        <template v-if="showLocationLinkageToolPopup">
            <occurrence-location-linkage-tool-popup
                :show-popup="showLocationLinkageToolPopup"
                @update:location="setLocationData"
                @close:popup="showLocationLinkageToolPopup = false"
            ></occurrence-location-linkage-tool-popup>
        </template>
    `,
    components: {
        'collecting-event-field-module': collectingEventFieldModule,
        'location-field-module': locationFieldModule,
        'location-name-code-auto-complete': locationNameCodeAutoComplete,
        'occurrence-collecting-event-list-popup': occurrenceCollectingEventListPopup,
        'occurrence-location-linkage-tool-popup': occurrenceLocationLinkageToolPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const changeTypeOptions = [
            {label: 'Same Location', value: 'samelocation'},
            {label: 'Change Location', value: 'changelocation'}
        ];
        const changeValid = Vue.computed(() => {
            if(selectedChangeType.value === 'samelocation'){
                return eventValid.value;
            }
            else{
                return (eventValid.value && locationValid.value);
            }
        });
        const collectingEventArr = Vue.computed(() => {
            if(selectedChangeType.value === 'samelocation'){
                return occurrenceStore.getLocationCollectingEventArr;
            }
            else{
                return newLocationCollectingEventArr.value;
            }
        });
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const eventData = Vue.reactive({});
        const eventFields = Vue.computed(() => occurrenceStore.getCollectingEventFields);
        const eventValid = Vue.computed(() => {
            return (!!eventData['eventdate']);
        });
        const locationData = Vue.reactive({});
        const locationFields = Vue.computed(() => occurrenceStore.getLocationFields);
        const locationId = Vue.computed(() => occurrenceStore.getLocationID);
        const locationValid = Vue.computed(() => {
            return (locationData['country'] && locationData['stateprovince']);
        });
        const newLocationCollectingEventArr = Vue.ref([]);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceFieldDefinitions);
        const selectedChangeType = Vue.ref('samelocation');
        const showCollectingEventListPopup = Vue.ref(false);
        const showLocationLinkageToolPopup = Vue.ref(false);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processChangeEvent() {
            if(Number(eventData['eventid']) > 0){
                if(Number(locationData['locationid']) > 0){
                    eventData['locationid'] = Number(locationData['locationid']);
                }
                transferOccurrenceEvent();
            }
            else{
                eventData['locationid'] = Number(locationData['locationid']) > 0 ? Number(locationData['locationid']) : locationId.value;
                occurrenceStore.createCollectingEventRecord((newEventId) => {
                    if(newEventId > 0){
                        eventData['eventid'] = newEventId;
                        transferOccurrenceEvent();
                    }
                    else{
                        hideWorking();
                        showNotification('negative', 'There was an error creating the new collecting event record.');
                    }
                }, eventData);
            }
        }

        function processChangeLocation() {
            if(Number(locationData['locationid']) > 0){
                transferOccurrenceLocation();
            }
            else{
                occurrenceStore.createLocationRecord((newLocationId) => {
                    if(newLocationId > 0){
                        locationData['locationid'] = newLocationId;
                        eventData['locationid'] = newLocationId;
                        transferOccurrenceLocation();
                    }
                    else{
                        hideWorking();
                        showNotification('negative', 'There was an error creating the new location record.');
                    }
                }, locationData);
            }
        }

        function processChangeOccurrence() {
            showWorking('Saving edits...');
            if(selectedChangeType.value === 'samelocation'){
                processChangeEvent();
            }
            else{
                processChangeLocation();
            }
        }

        function processLocationCodeNameSelection(key, value) {
            if(value){
                if(value.hasOwnProperty('id') && Number(value.id) > 0){
                    setLocationData(value);
                }
                else{
                    updateLocationData(key, value.value);
                }
            }
            else{
                updateLocationData('locationid', 0);
                updateLocationData(key, null);
                newLocationCollectingEventArr.value = [];
            }
        }

        function processSelectedChangeTypeChange(value) {
            selectedChangeType.value = value;
            if(value === 'changelocation'){
                setEventData(occurrenceStore.getBlankCollectingEventRecord);
                occurrenceStore.setLocationFields();
                setLocationData(occurrenceStore.getBlankLocationRecord);
            }
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setEventData(data) {
            Object.keys(data).forEach((field) => {
                eventData[field] = data[field];
            });
            eventData['collid'] = collId.value;
        }

        function setLocationCollectingEventArr() {
            const formData = new FormData();
            formData.append('collid', collId.value.toString());
            formData.append('locationid', locationData['locationid'].toString());
            formData.append('action', 'getLocationCollectingEventArr');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data && data.length > 0){
                    newLocationCollectingEventArr.value = data;
                }
            });
        }

        function setLocationData(data) {
            newLocationCollectingEventArr.value = [];
            Object.keys(data).forEach((field) => {
                locationData[field] = data[field];
            });
            locationData['collid'] = collId.value;
            if(Number(locationData['locationid']) > 0){
                setLocationCollectingEventArr();
            }
        }

        function transferOccurrenceEvent() {
            occurrenceStore.updateOccurrenceEvent(eventData['eventid'], true, (res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Changes applied successfully.');
                    occurrenceStore.setCurrentOccurrenceRecord(occId.value);
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error changing the occurrence collecting event.');
                }
            });
        }

        function transferOccurrenceLocation() {
            occurrenceStore.updateOccurrenceLocation(locationData['locationid'], false, (res) => {
                if(res === 1){
                    processChangeEvent();
                }
                else{
                    hideWorking();
                    showNotification('negative', 'There was an error changing the occurrence location.');
                }
            });
        }

        function updateCollectingEventData(key, value) {
            eventData[key] = value;
            eventData['eventid'] = 0;
        }

        function updateLocationData(key, value) {
            locationData[key] = value;
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            occurrenceStore.setCollectingEventFields();
            setEventData(occurrenceStore.getBlankCollectingEventRecord);
        });

        return {
            changeTypeOptions,
            changeValid,
            collectingEventArr,
            collId,
            contentRef,
            contentStyle,
            eventData,
            eventFields,
            locationData,
            locationFields,
            locationValid,
            occurrenceFieldDefinitions,
            selectedChangeType,
            showCollectingEventListPopup,
            showLocationLinkageToolPopup,
            closePopup,
            processChangeOccurrence,
            processLocationCodeNameSelection,
            processSelectedChangeTypeChange,
            setEventData,
            setLocationData,
            updateCollectingEventData,
            updateLocationData
        }
    }
};
