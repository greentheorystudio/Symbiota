const occurrenceEditorSingleDisplay = {
    template: `
        <div class="row justify-center q-py-sm">
            <div ref="moduleContainerRef" class="editor-inner-container rounded-borders shadow-5 q-pa-md column q-gutter-y-sm self-center">
                <div class="row justify-between">
                    <div class="row justify-start">
                        <a :href="clientRoot + '/index.php'">Home</a> &gt;&gt;
                        <template v-if="displayMode === 4">
                            <a :href="clientRoot + '/collections/management/crowdsource/index.php'">Crowd Sourcing Central</a> &gt;&gt;
                        </template>
                        <template v-else-if="isEditor">
                            <a :href="clientRoot + '/collections/misc/collprofiles.php?collid=' + collId + '&emode=1'">Collection Control Panel</a> &gt;&gt;
                        </template>
                        <template v-if="occId > 0">
                            <span class="text-bold">Occurrence Editor</span>
                        </template>
                        <template v-else>
                            <span class="text-bold">Create New Record</span>
                        </template>
                    </div>
                    <div class="row justify-end self-center">
                        <q-btn v-if="recordCount > 1 && currentRecordIndex !== 1" icon="first_page" color="grey-8" round dense flat @click="goToFirstRecord"></q-btn>
                        <q-btn v-if="currentRecordIndex !== 1" icon="chevron_left" color="grey-8" round dense flat @click="goToPreviousRecord"></q-btn>
                        <div class="self-center text-bold q-mr-xs">Record {{ currentRecordIndex }} of {{ recordCount }}</div>
                        <q-btn v-if="currentRecordIndex !== recordCount && occId > 0" icon="chevron_right" color="grey-8" round dense flat @click="goToNextRecord"></q-btn>
                        <q-btn v-if="recordCount > 1 && currentRecordIndex !== recordCount && occId > 0" icon="last_page" color="grey-8" round dense flat @click="goToLastRecord"></q-btn>
                        <q-btn v-if="recordCount > 1 && occId > 0" icon="note_add" color="grey-8" round dense flat @click="goToNewRecord"></q-btn>
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
                        <template v-if="displayQueryPopupButton">
                            <div class="self-center">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="changeQueryPopupDisplay(true);" icon="filter_alt" dense>
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Filter records
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </template>
                        <template v-if="displayBatchUpdateButton">
                            <div class="self-center">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="changeBatchUpdatePopupDisplay(true);" icon="find_replace" dense>
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Search and Replace Tool
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </template>
                        <template v-if="imageCount > 0">
                            <div class="self-center">
                                <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="changeImageTranscriberPopupDisplay(true);" icon="image_search" dense>
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Display image transcription window
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </template>
                    </div>
                </div>
                <q-card flat bordered class="q-mt-sm black-border">
                    <template v-if="Number(occId) > 0">
                        <q-card-section class="q-pa-none">
                            <occurrence-editor-tab-module></occurrence-editor-tab-module>
                        </q-card-section>
                    </template>
                    <template v-else-if="Number(occId) === 0">
                        <q-card-section class="q-pa-sm">
                            <template v-if="occurrenceEntryFormat === 'observation'">
                                <occurrence-entry-observation-form-module></occurrence-entry-observation-form-module>
                            </template>
                            <template v-else-if="occurrenceEntryFormat === 'image'">
                                <occurrence-entry-image-form-module></occurrence-entry-image-form-module>
                            </template>
                            <template v-else-if="occurrenceEntryFormat === 'skeletal'">
                                <occurrence-entry-skeletal-form-module></occurrence-entry-skeletal-form-module>
                            </template>
                            <template v-else>
                                <occurrence-editor-occurrence-data-module></occurrence-editor-occurrence-data-module>
                            </template>
                        </q-card-section>
                    </template>
                </q-card>
            </div>
            <occurrence-editor-image-transcriber-popup :show-popup="displayImageTranscriberPopup"></occurrence-editor-image-transcriber-popup>
        </div>
    `,
    components: {
        'occurrence-editor-image-transcriber-popup': occurrenceEditorImageTranscriberPopup,
        'occurrence-editor-occurrence-data-module': occurrenceEditorOccurrenceDataModule,
        'occurrence-editor-tab-module': occurrenceEditorTabModule,
        'occurrence-entry-format-selector': occurrenceEntryFormatSelector,
        'occurrence-entry-image-form-module': occurrenceEntryImageFormModule,
        'occurrence-entry-observation-form-module': occurrenceEntryObservationFormModule,
        'occurrence-entry-skeletal-form-module': occurrenceEntrySkeletalFormModule
    },
    setup() {
        const baseStore = useBaseStore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const clientRoot = baseStore.getClientRoot;
        const collId = Vue.computed(() => occurrenceStore.getCollId);
        const collInfo = Vue.computed(() => occurrenceStore.getCollectionData);
        const containerWidth = Vue.ref(0);
        const currentRecordIndex = Vue.computed(() => occurrenceStore.getCurrentRecordIndex);
        const displayBatchUpdateButton = Vue.inject('displayBatchUpdateButton');
        const displayImageTranscriberPopup = Vue.ref(false);
        const displayMode = Vue.computed(() => occurrenceStore.getDisplayMode);
        const displayQueryPopupButton = Vue.inject('displayQueryPopupButton');
        const imageCount = Vue.computed(() => occurrenceStore.getImageCount);
        const isEditor = Vue.computed(() => occurrenceStore.getIsEditor);
        const moduleContainerRef = Vue.ref(null);
        const occId = Vue.computed(() => occurrenceStore.getOccId);
        const occurrenceEntryFormat = Vue.computed(() => occurrenceStore.getOccurrenceEntryFormat);
        const occurrenceFields = Vue.computed(() => occurrenceStore.getOccurrenceFields);
        const occurrenceFieldDefinitions = Vue.computed(() => occurrenceStore.getOccurrenceFieldDefinitions);
        const recordCount = Vue.computed(() => occurrenceStore.getRecordCount);

        const changeBatchUpdatePopupDisplay = Vue.inject('changeBatchUpdatePopupDisplay');
        const changeQueryPopupDisplay = Vue.inject('changeQueryPopupDisplay');

        function changeImageTranscriberPopupDisplay(value) {
            displayImageTranscriberPopup.value = value;
        }

        function changeOccurrenceEntryFormat(value) {
            occurrenceStore.setOccurrenceEntryFormat(value);
        }

        function goToFirstRecord() {
            occurrenceStore.goToFirstRecord();
        }

        function goToLastRecord() {
            occurrenceStore.goToLastRecord();
        }

        function goToNextRecord() {
            occurrenceStore.goToNextRecord();
        }

        function goToNewRecord() {
            occurrenceStore.goToNewOccurrenceRecord();
        }

        function goToPreviousRecord() {
            occurrenceStore.goToPreviousRecord();
        }

        function setContainerWidth() {
            containerWidth.value = moduleContainerRef.value.clientWidth;
        }

        Vue.provide('changeImageTranscriberPopupDisplay', changeImageTranscriberPopupDisplay);
        Vue.provide('containerWidth', containerWidth);
        Vue.provide('occurrenceFields', occurrenceFields);
        Vue.provide('occurrenceFieldDefinitions', occurrenceFieldDefinitions);

        Vue.onMounted(() => {
            setContainerWidth();
            window.addEventListener('resize', setContainerWidth);
            occurrenceStore.setOccurrenceFields();
        });

        return {
            clientRoot,
            collId,
            collInfo,
            currentRecordIndex,
            displayBatchUpdateButton,
            displayImageTranscriberPopup,
            displayMode,
            displayQueryPopupButton,
            imageCount,
            isEditor,
            moduleContainerRef,
            occId,
            occurrenceEntryFormat,
            recordCount,
            changeBatchUpdatePopupDisplay,
            changeImageTranscriberPopupDisplay,
            changeQueryPopupDisplay,
            goToFirstRecord,
            goToLastRecord,
            goToNextRecord,
            goToNewRecord,
            goToPreviousRecord,
            changeOccurrenceEntryFormat
        }
    }
};
