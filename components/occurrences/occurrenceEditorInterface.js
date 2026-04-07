const occurrenceEditorInterface = {
    props: {
        collid: {
            type: Number,
            default: null
        },
        displayMode: {
            type: Number,
            default: null
        },
        occid: {
            type: Number,
            default: 0
        }
    },
    template: `
        <div class="row justify-center">
            <div ref="moduleContainerRef" class="editor-inner-container rounded-borders shadow-5 q-pa-md column q-gutter-y-sm self-center bg-white">
                <div class="row justify-start">
                    <div><a :href="clientRoot + '/index.php'" tabindex="0">Home</a> &gt;&gt;</div>
                    <template v-if="displayMode === 4">
                        <a :href="clientRoot + '/collections/management/crowdsource/index.php'" tabindex="0">Crowd Sourcing Central</a> &gt;&gt;
                    </template>
                    <template v-else-if="isEditor">
                        <div><a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collId)" tabindex="0">Collection Control Panel</a> &gt;&gt;</div>
                    </template>
                    <span class="text-bold">Occurrence Editor</span>
                </div>
                <div class="row justify-between">
                    <div class="row justify-start q-gutter-sm self-center">
                        <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="openQueryPopupDisplay();" icon="search" label="Search" aria-label="Open Search Window" tabindex="0"></q-btn>
                        <template v-if="recordCount > 1">
                            <table-display-button :navigator-mode="true"></table-display-button>
                            <list-display-button :navigator-mode="true"></list-display-button>
                            <spatial-display-button :navigator-mode="true"></spatial-display-button>
                            <image-display-button></image-display-button>
                        </template>
                    </div>
                    <div class="row justify-end self-center">
                        <div class="self-center text-bold q-mr-xs">Record {{ currentRecordIndex }} of {{ recordCount }}</div>
                        <q-btn v-if="recordCount > 1 && currentRecordIndex > 1" icon="first_page" color="grey-8" round dense flat @click="goToFirstRecord" aria-label="Go to first record" tabindex="0"></q-btn>
                        <q-btn v-if="recordCount > 1 && currentRecordIndex > 1" icon="chevron_left" color="grey-8" round dense flat @click="goToPreviousRecord" aria-label="Go to previous record" tabindex="0"></q-btn>
                        <q-btn v-if="recordCount > 1 && currentRecordIndex < recordCount && occId > 0" icon="chevron_right" color="grey-8" round dense flat @click="goToNextRecord" aria-label="Go to next record" tabindex="0"></q-btn>
                        <q-btn v-if="recordCount > 1 && currentRecordIndex < recordCount && occId > 0" icon="last_page" color="grey-8" round dense flat @click="goToLastRecord" aria-label="Go to last record" tabindex="0"></q-btn>
                        <q-btn v-if="occurrenceEntryFormat !== 'replicate' && occId > 0" icon="add_circle" color="grey-8" round dense flat @click="goToNewRecord" aria-label="Go to new record" tabindex="0">
                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                Create new occurrence record
                            </q-tooltip>
                        </q-btn>
                    </div>
                </div>
                <div class="row justify-between">
                    <div class="row justify-start text-h6 text-weight-bold">
                        <template v-if="collInfo">
                            <template v-if="collInfo.collectionname">{{ collInfo.collectionname }}</template>
                            <template v-if="collInfo.institutioncode || collInfo.collectioncode"> (<template v-if="collInfo.institutioncode">{{ collInfo.institutioncode }}</template><template v-if="collInfo.institutioncode && collInfo.collectioncode">-</template><template v-if="collInfo.collectioncode">{{ collInfo.collectioncode }}</template>)</template>
                        </template>
                    </div>
                    <div class="row justify-end q-gutter-sm self-center">
                        <template v-if="Number(occId) === 0">
                            <div>
                                <occurrence-entry-format-selector :selected-format="occurrenceEntryFormat" @change-occurrence-entry-format="changeOccurrenceEntryFormat"></occurrence-entry-format-selector>
                            </div>
                        </template>
                        <template v-if="recordCount > 1">
                            <div class="self-center">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayBatchUpdatePopup = true" icon="find_replace" dense aria-label="Open Batch Update Tool" :disabled="!searchTermsValid" tabindex="0">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Open Batch Update Tool
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </template>
                        <template v-if="(occurrenceEntryFormat === 'specimen' || occurrenceEntryFormat === 'skeletal') && imageCount > 0">
                            <div class="self-center">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="displayImageTranscriberPopup = true" icon="image_search" dense aria-label="Display image transcription window" tabindex="0">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Display image transcription window
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </template>
                    </div>
                </div>
                <template v-if="Number(occId) > 0">
                    <q-card flat bordered class="q-mt-sm black-border">
                        <q-card-section class="q-pa-none">
                            <occurrence-editor-tab-module></occurrence-editor-tab-module>
                        </q-card-section>
                    </q-card>
                </template>
                <template v-else-if="Number(occId) === 0">
                    <q-card flat>
                        <q-card-section class="q-pa-sm">
                            <template v-if="occurrenceEntryFormat === 'observation'">
                                <occurrence-entry-observation-form-module></occurrence-entry-observation-form-module>
                            </template>
                            <template v-else-if="occurrenceEntryFormat === 'skeletal'">
                                <occurrence-entry-skeletal-form-module></occurrence-entry-skeletal-form-module>
                            </template>
                            <template v-else>
                                <occurrence-editor-occurrence-data-module></occurrence-editor-occurrence-data-module>
                            </template>
                        </q-card-section>
                    </q-card>
                </template>
            </div>
        </div>
        <template v-if="displayBatchUpdatePopup">
            <occurrence-editor-batch-update-popup :show-popup="displayBatchUpdatePopup" @complete:batch-update="processBatchUpdate" @close:popup="displayBatchUpdatePopup = false"></occurrence-editor-batch-update-popup>
        </template>
        <template v-if="displayImageTranscriberPopup">
            <occurrence-editor-image-transcriber-popup :show-popup="displayImageTranscriberPopup" @close:popup="displayImageTranscriberPopup = false"></occurrence-editor-image-transcriber-popup>
        </template>
        <confirmation-popup ref="confirmationPopupRef"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'image-display-button': imageDisplayButton,
        'list-display-button': listDisplayButton,
        'occurrence-editor-batch-update-popup': occurrenceEditorBatchUpdatePopup,
        'occurrence-editor-image-transcriber-popup': occurrenceEditorImageTranscriberPopup,
        'occurrence-editor-occurrence-data-module': occurrenceEditorOccurrenceDataModule,
        'occurrence-editor-tab-module': occurrenceEditorTabModule,
        'occurrence-entry-format-selector': occurrenceEntryFormatSelector,
        'occurrence-entry-observation-form-module': occurrenceEntryObservationFormModule,
        'occurrence-entry-skeletal-form-module': occurrenceEntrySkeletalFormModule,
        'spatial-display-button': spatialDisplayButton,
        'table-display-button': tableDisplayButton
    },
    setup(props, context) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();
        const occurrenceStore = useOccurrenceStore();
        const searchStore = useSearchStore();

        const clientRoot = baseStore.getClientRoot;
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const collInfo = Vue.computed(() => occurrenceStore.getCollectionData);
        const confirmationPopupRef = Vue.ref(null);
        const containerWidth = Vue.ref(0);
        const currentRecordIndex = Vue.computed(() => searchStore.getCurrentOccIdIndex);
        const displayBatchUpdatePopup = Vue.ref(false);
        const displayImageTranscriberPopup = Vue.ref(false);
        const displayMode = Vue.computed(() => occurrenceStore.getDisplayMode);
        const imageCount = Vue.computed(() => occurrenceStore.getImageCount);
        const isEditor = Vue.computed(() => occurrenceStore.getIsEditor);
        const moduleContainerRef = Vue.ref(null);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEditorModeActive = Vue.computed(() => searchStore.getOccurrenceEditorModeActive);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const searchRecordCount = Vue.computed(() => searchStore.getSearchRecordCount);
        const searchTermsValid = Vue.computed(() => searchStore.getSearchTermsValid);
        const recordCount = Vue.computed(() => {
            if(Number(searchRecordCount.value) > 0){
                return Number(occId.value) === 0 ? searchRecordCount.value + 1 : searchRecordCount.value;
            }
            else{
                return 1;
            }
        });

        const loadRecordsCompleted = Vue.inject('loadRecordsCompleted');

        Vue.watch(collId, () => {
            if(occurrenceEditorModeActive.value){
                searchStore.updateSearchTerms('collid', collId.value);
            }
        });

        Vue.watch(occId, () => {
            searchStore.setCurrentOccId(occId.value);
        });

        Vue.watch(loadRecordsCompleted, () => {
            processSearchRecordCountChange();
        });

        function changeOccurrenceEntryFormat(value) {
            occurrenceStore.setOccurrenceEntryFormat(value);
        }

        function goToFirstRecord() {
            occurrenceStore.setCurrentOccurrenceRecord(searchStore.getFirstOccidInOccidArr);
        }

        function goToLastRecord() {
            occurrenceStore.setCurrentOccurrenceRecord(searchStore.getLastOccidInOccidArr);
        }

        function goToNewRecord() {
            occurrenceStore.goToNewOccurrenceRecord();
        }

        function goToNextRecord() {
            occurrenceStore.setCurrentOccurrenceRecord(searchStore.getNextOccidInOccidArr);
        }

        function goToPreviousRecord() {
            occurrenceStore.setCurrentOccurrenceRecord(searchStore.getPreviousOccidInOccidArr);
        }

        function openQueryPopupDisplay() {
            context.emit('open:query-popup');
        }

        function processBatchUpdate() {
            occurrenceStore.setCurrentOccurrenceRecord(occId.value);
            context.emit('load:records');
        }

        function processSearchRecordCountChange() {
            if(Number(searchRecordCount.value) > 0){
                if(Number(occId.value) === 0 || currentRecordIndex.value === 0){
                    goToFirstRecord();
                }
            }
            else{
                occurrenceStore.setCurrentOccurrenceRecord(0);
            }
        }

        function setContainerWidth() {
            containerWidth.value = moduleContainerRef.value.clientWidth;
        }

        function validateCoordinates() {
            occurrenceStore.getCoordinateVerificationData((data) => {
                if(data.address){
                    if(!data.valid){
                        let alertText = 'Are those coordinates accurate? They currently map to: ' + data.country + ', ' + data.state;
                        if(data.county) {
                            alertText += ', ' + data.county;
                        }
                        alertText += ', which differs from what you have entered.';
                        confirmationPopupRef.value.openPopup(alertText);
                    }
                }
                else{
                    showNotification('negative', 'Unable to identify a country from the coordinates entered. Are they accurate?');
                }
            });
        }

        Vue.provide('containerWidth', containerWidth);
        Vue.provide('validateCoordinates', validateCoordinates);

        Vue.onMounted(() => {
            setContainerWidth();
            window.addEventListener('resize', setContainerWidth);
            occurrenceStore.setOccurrenceFields();
            if(Number(props.collid) > 0 || Number(props.occid) > 0){
                if(Number(props.displayMode) > 1){
                    occurrenceStore.setDisplayMode(props.displayMode);
                }
                occurrenceStore.setCurrentOccurrenceRecord(Number(props.occid));
            }
            else{
                searchStore.setDisplayInterface('list');
            }
        });

        return {
            clientRoot,
            collId,
            collInfo,
            confirmationPopupRef,
            currentRecordIndex,
            displayBatchUpdatePopup,
            displayImageTranscriberPopup,
            displayMode,
            imageCount,
            isEditor,
            moduleContainerRef,
            occId,
            occurrenceEntryFormat,
            recordCount,
            searchTermsValid,
            changeOccurrenceEntryFormat,
            goToFirstRecord,
            goToLastRecord,
            goToNextRecord,
            goToNewRecord,
            goToPreviousRecord,
            openQueryPopupDisplay,
            processBatchUpdate
        }
    }
};
