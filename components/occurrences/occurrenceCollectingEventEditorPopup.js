const occurrenceCollectingEventEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card v-if="!showEventTransferPopup" class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <template v-if="editsExist">
                                        <span class="q-ml-md text-h6 text-bold text-red self-center">Unsaved Edits</span>
                                    </template>
                                </div>
                                <div class="row justify-end q-gutter-xs">
                                    <q-btn color="negative" @click="deleteEvent();" label="Delete Event" :disabled="eventId === 0 || collectingEventReplicateTaxaCnt > 0 || collectingEventCollectionArr.length > 0" tabindex="0" />
                                    <q-btn color="secondary" @click="showEventTransferPopup = true" label="Change Location" tabindex="0" />
                                    <q-btn color="secondary" @click="saveEventEdits();" label="Save Event Edits" :disabled="!editsExist || !eventValid" tabindex="0" />
                                </div>
                            </div>
                            <collecting-event-field-module :event-mode="true" :data="eventData" :field-definitions="eventFieldDefinitions" @update:collecting-event-data="(data) => updateCollectingEventData(data.key, data.value)"></collecting-event-field-module>
                            <div class="row justify-between q-col-gutter-sm">
                                <div class="col-12 col-sm-6 col-md-9">
                                    <text-field-input-element :definition="eventFieldDefinitions['eventremarks']" :label="eventFieldDefinitions['eventremarks']['label']" :maxlength="eventFieldDefinitions['eventremarks']['length']" :value="eventData.eventremarks" @update:value="(value) => updateCollectingEventData('eventremarks', value)"></text-field-input-element>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <text-field-input-element data-type="int" :definition="eventFieldDefinitions['repcount']" :label="eventFieldDefinitions['repcount']['label']" :maxlength="eventFieldDefinitions['repcount']['length']" :value="eventData.repcount" @update:value="(value) => updateCollectingEventData('repcount', value)"></text-field-input-element>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
            <template v-if="showEventTransferPopup">
                <occurrence-collecting-event-transfer-popup
                    :show-popup="showEventTransferPopup"
                    @location-change:updated="closePopup"
                    @close:popup="showEventTransferPopup = false"
                ></occurrence-collecting-event-transfer-popup>
            </template>
        </q-dialog>
    `,
    components: {
        'collecting-event-field-module': collectingEventFieldModule,
        'occurrence-collecting-event-transfer-popup': occurrenceCollectingEventTransferPopup,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = useOccurrenceStore();

        const collectingEventCollectionArr = Vue.computed(() => occurrenceStore.getCollectingEventCollectionArr);
        const collectingEventReplicateTaxaCnt = Vue.computed(() => occurrenceStore.getCollectingEventReplicateTaxaCnt);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => occurrenceStore.getCollectingEventEditsExist);
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const eventFieldDefinitions = Vue.computed(() => occurrenceStore.getCollectingEventFieldDefinitions);
        const eventId = Vue.computed(() => occurrenceStore.getCollectingEventID);
        const eventValid = Vue.computed(() => occurrenceStore.getCollectingEventValid);
        const showEventTransferPopup = Vue.ref(false);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            if(editsExist.value){
                occurrenceStore.revertCollectingEventEditData();
            }
            context.emit('close:popup');
        }

        function deleteEvent() {
            occurrenceStore.deleteCollectingEventRecord((res) => {
                if(res === 1){
                    context.emit('close:popup');
                }
                else{
                    showNotification('negative', 'There was an error while deleting the collecting event');
                }
            });
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

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateCollectingEventData(key, value) {
            occurrenceStore.updateCollectingEventEditData(key, value);
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            collectingEventCollectionArr,
            collectingEventReplicateTaxaCnt,
            contentRef,
            contentStyle,
            editsExist,
            eventData,
            eventFieldDefinitions,
            eventId,
            eventValid,
            showEventTransferPopup,
            closePopup,
            deleteEvent,
            saveEventEdits,
            updateCollectingEventData
        }
    }
};
