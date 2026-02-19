const occurrenceCollectingEventTransferPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog v-if="!showLocationLinkageToolPopup" class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div></div>
                                <div class="row justify-end q-gutter-xs">
                                    <q-btn color="secondary" @click="showLocationLinkageToolPopup = true" label="Search Locations" tabindex="0" />
                                    <q-btn color="secondary" @click="processChangeLocation();" label="Change Event Location" :disabled="!locationValid" tabindex="0" />
                                </div>
                            </div>
                            <div class="q-mb-xs row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <location-name-code-auto-complete :collid="collId" key-value="code" :definition="occurrenceFieldDefinitions['locationcode']" label="Location Code" :maxlength="locationFields['locationcode'] ? locationFields['locationcode']['length'] : 0" :value="locationData.locationcode" @update:value="(value) => processLocationCodeNameSelection('locationcode', value)"></location-name-code-auto-complete>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6">
                                    <location-name-code-auto-complete :collid="collId" key-value="name" :definition="occurrenceFieldDefinitions['locationname']" label="Location Name" :maxlength="locationFields['locationname'] ? locationFields['locationname']['length'] : 0" :value="locationData.locationname" @update:value="(value) => processLocationCodeNameSelection('locationname', value)"></location-name-code-auto-complete>
                                </div>
                            </div>
                            <location-field-module :disabled="(locationData['locationid'] > 0)" :data="locationData" :fields="locationFields" :field-definitions="occurrenceFieldDefinitions" @update:location-data="(data) => updateLocationData(data.key, data.value)"></location-field-module>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <template v-if="showLocationLinkageToolPopup">
            <occurrence-location-linkage-tool-popup
                :show-popup="showLocationLinkageToolPopup"
                @update:location="setLocationData"
                @close:popup="showLocationLinkageToolPopup = false"
            ></occurrence-location-linkage-tool-popup>
        </template>
    `,
    components: {
        'location-field-module': locationFieldModule,
        'location-name-code-auto-complete': locationNameCodeAutoComplete,
        'occurrence-location-linkage-tool-popup': occurrenceLocationLinkageToolPopup
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const locationData = Vue.reactive({});
        const locationFields = Vue.computed(() => occurrenceStore.getLocationFields);
        const locationValid = Vue.computed(() => {
            return (locationData['country'] && locationData['stateprovince']);
        });
        const occurrenceFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceFieldDefinitions);
        const showLocationLinkageToolPopup = Vue.ref(false);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processChangeLocation() {
            showWorking('Saving edits...');
            if(Number(locationData['locationid']) > 0){
                transferEventRecord(locationData['locationid']);
            }
            else{
                occurrenceStore.createLocationRecord((newLocationId) => {
                    if(newLocationId > 0){
                        transferEventRecord(newLocationId);
                    }
                    else{
                        hideWorking();
                        showNotification('negative', 'There was an error creating the new location record.');
                    }
                }, locationData);
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
            }
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setLocationData(data) {
            Object.keys(data).forEach((field) => {
                locationData[field] = data[field];
            });
            locationData['collid'] = collId.value;
        }

        function transferEventRecord(locationId) {
            occurrenceStore.updateCollectingEventLocation(locationId, (res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Event location changed.');
                    context.emit('location-change:updated');
                }
                else{
                    showNotification('negative', 'There was an error changing the event location.');
                }
            });
        }

        function updateLocationData(key, value) {
            locationData[key] = value;
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            occurrenceStore.setLocationFields();
            setLocationData(occurrenceStore.getBlankLocationRecord);
        });

        return {
            collId,
            contentRef,
            contentStyle,
            locationData,
            locationFields,
            locationValid,
            occurrenceFieldDefinitions,
            showLocationLinkageToolPopup,
            closePopup,
            processChangeLocation,
            processLocationCodeNameSelection,
            setLocationData,
            updateLocationData
        }
    }
};
