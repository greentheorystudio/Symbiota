const occurrenceLocationEditorPopup = {
    props: {
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
                        <div class="q-pa-md column q-gutter-y-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <q-btn color="secondary" @click="saveLocationEdits();" label="Save Location Edits" :disabled="!editsExist || !locationValid" />
                                </div>
                            </div>
                            <div class="q-mb-xs row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['locationcode']" label="Location Code" :maxlength="locationFields['locationcode'] ? locationFields['locationcode']['length'] : 0" :value="locationData.locationcode" @update:value="(value) => updateLocationData('locationcode', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6">
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['locationname']" label="Location Name" :maxlength="locationFields['locationname'] ? locationFields['locationname']['length'] : 0" :value="locationData.locationname" @update:value="(value) => updateLocationData('locationname', value)"></text-field-input-element>
                                </div>
                            </div>
                            <location-field-module :data="locationData" :fields="locationFields" :field-definitions="occurrenceFieldDefinitions" @update:location-data="(data) => updateLocationData(data.key, data.value)"></location-field-module>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'location-field-module': locationFieldModule,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => occurrenceStore.getLocationEditsExist);
        const locationData = Vue.computed(() => occurrenceStore.getLocationData);
        const locationFields = Vue.computed(() => occurrenceStore.getLocationFields);
        const locationValid = Vue.computed(() => occurrenceStore.getLocationValid);
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            if(editsExist.value){
                occurrenceStore.revertLocationEditData();
            }
            context.emit('close:popup');
        }

        function saveLocationEdits() {
            showWorking('Saving edits...');
            occurrenceStore.updateLocationRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the location edits.');
                }
                context.emit('close:popup');
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
            occurrenceStore.setLocationFields();
        });

        return {
            contentRef,
            contentStyle,
            editsExist,
            locationData,
            locationFields,
            locationValid,
            occurrenceFieldDefinitions,
            closePopup,
            saveLocationEdits,
            updateLocationData
        }
    }
};
