const occurrenceCollectingEventEditorPopup = {
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
                        <div class="q-pa-sm column q-gutter-y-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red text-h6 self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end">
                                    <q-btn color="secondary" @click="saveEventEdits();" label="Save Event Edits" :disabled="!editsExist || !eventValid" />
                                </div>
                            </div>
                            <collecting-event-field-module :event-mode="true" :data="eventData" :fields="eventFields" :field-definitions="occurrenceFieldDefinitions" @update:collecting-event-data="(data) => updateCollectingEventData(data.key, data.value)"></collecting-event-field-module>
                            <div class="row justify-between q-col-gutter-xs">
                                <div class="col-12 col-sm-6 col-md-9">
                                    <text-field-input-element :definition="occurrenceFieldDefinitions['eventremarks']" label="Event Remarks" :maxlength="eventFields['eventremarks'] ? eventFields['eventremarks']['length'] : 0" :value="eventData.eventremarks" @update:value="(value) => updateCollectingEventData('eventremarks', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <text-field-input-element data-type="int" :definition="occurrenceFieldDefinitions['repcount']" label="Rep Count" :maxlength="eventFields['repcount'] ? eventFields['repcount']['length'] : 0" :value="eventData.repcount" @update:value="(value) => updateCollectingEventData('repcount', value)"></text-field-input-element>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'collecting-event-field-module': collectingEventFieldModule,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => occurrenceStore.getCollectingEventEditsExist);
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const eventFields = Vue.computed(() => occurrenceStore.getCollectingEventFields);
        const eventValid = Vue.computed(() => occurrenceStore.getCollectingEventValid);
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');

        Vue.watch(contentRef, () => {
            setcontentStyle();
        });

        function closePopup() {
            if(editsExist.value){
                occurrenceStore.revertCollectingEventEditData();
            }
            context.emit('close:popup');
        }

        function saveEventEdits() {
            showWorking('Saving edits...');
            occurrenceStore.updateCollectingEventRecord((res) => {
                hideWorking();
                if(res === 1){
                    showNotification('positive','Edits saved.');
                }
                else{
                    showNotification('negative', 'There was an error saving the event edits.');
                }
                context.emit('close:popup');
            });
        }

        function setcontentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateCollectingEventData(key, value) {
            occurrenceStore.updateCollectingEventEditData(key, value);
        }

        Vue.onMounted(() => {
            setcontentStyle();
            occurrenceStore.setCollectingEventFields();
        });

        return {
            contentRef,
            contentStyle,
            editsExist,
            eventData,
            eventFields,
            eventValid,
            occurrenceFieldDefinitions,
            closePopup,
            saveEventEdits,
            updateCollectingEventData
        }
    }
};
